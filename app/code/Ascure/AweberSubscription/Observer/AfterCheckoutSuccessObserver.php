<?php
/**
 * Ascure AweberSubscription Observer
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com
 * @license     https://www.ascuretech.com/license.html
 */

namespace Ascure\AweberSubscription\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Directory\Model\CountryFactory;
use Ascure\AweberSubscription\Helper\Data;
use GuzzleHttp\Client;

class AfterCheckoutSuccessObserver implements ObserverInterface
{
    const BASE_URL = 'https://api.aweber.com/1.0/';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * AfterCheckoutSuccessObserver constructor.
     *
     * @param LoggerInterface $logger
     * @param Order $order
     * @param CountryFactory $countryFactory
     * @param Data $helper
     * @param Client $httpClient
     */
    public function __construct(
        LoggerInterface $logger,
        Order $order,
        CountryFactory $countryFactory,
        Data $helper,
        Client $httpClient
    ) {
        $this->logger = $logger;
        $this->order = $order;
        $this->countryFactory = $countryFactory;
        $this->helper = $helper;
        $this->httpClient = $httpClient;
    }

    /**
     * Execute method triggered after successful checkout.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->helper->isModuleEnabled()) {
                $this->logger->info("Observer executed: AfterCheckoutSuccessObserver");
                $accessToken = $this->helper->getAccessToken();

                // Get the completed order
                $orderIds = $observer->getEvent()->getOrderIds();
                if (!empty($orderIds)) {
                    $orderId = $orderIds[0];
                    $order = $this->order->load($orderId);
                    $orderDetails = $this->getOrderDetails($order);
                    $tag = $orderDetails['item_names'];

                    $this->logger->info("Order Details:\n" . json_encode($orderDetails, JSON_PRETTY_PRINT));

                    $email = $orderDetails['customer_email'];
                    $country = $orderDetails['country'];
                    $shippingAddress = $orderDetails['shipping_address'];
                    $trackingId = $orderDetails['order_id'];
                    $customerName = $orderDetails['customer_name'];

                    if ($email) {
                        // Get all the accounts entries
                        $accounts = $this->helper->getCollection($this->httpClient, $accessToken, self::BASE_URL . 'accounts');

                        // Get all the list entries for the first account
                        $listsUrl = $accounts[0]['lists_collection_link'];
                        $lists = $this->helper->getCollection($this->httpClient, $accessToken, $listsUrl);

                        // Find out if a subscriber exists on the first list
                        $params = array(
                            'ws.op' => 'find',
                            'email' => $email
                        );
                        $subsUrl = $lists[0]['subscribers_collection_link'];
                        $findUrl = $subsUrl . '?' . http_build_query($params);
                        $foundSubscribers = $this->helper->getCollection($this->httpClient, $accessToken, $findUrl);
                        if (empty($foundSubscribers[0]['tags'])) {
                            $tag[] = 'new';
                            $data = array(
                                'name' => $customerName,
                                'email' => $email,
                                'ad_tracking' => $trackingId,
                                'custom_fields' => array('country' => $country, 'shippingAddress' => $shippingAddress),
                                'tags' => array('add' => $tag),
                            );
                            $this->logger->info("Data New :\n" . json_encode($data, JSON_PRETTY_PRINT));
                        } else {
                            $data = array(
                                'name' => $customerName,
                                'ad_tracking' => $trackingId,
                                'custom_fields' => array('country' => $country, 'shippingAddress' => $shippingAddress),
                                'tags' => array('add' => $tag, 'remove' => array('new')),
                            );
                            $this->logger->info("Data old :\n" . json_encode($data, JSON_PRETTY_PRINT));
                        }

                        $subscriberUrl = $foundSubscribers[0]['self_link'];
                        $subscriberResponse = $this->httpClient->patch($subscriberUrl, [
                            'json' => $data,
                            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                        ])->getBody()->__toString();
                        $subscriber = json_decode($subscriberResponse, true);
                        $message = "following tags are updated" . implode(', ', $tag) . "for the " . $email;

                    } else {
                        $message = "Please enter an email address";
                        $subscriber = array();
                    }

                    // Log success message
                    $this->logger->info("Message:\n" . json_encode($message, JSON_PRETTY_PRINT));
                } else {
                    $this->logger->info("No order ID found.");
                }
            }
        } catch (LocalizedException $e) {
            // Handle Magento localized exceptions (e.g., validation errors)
            $this->logger->error("Localized Exception: " . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            $this->logger->error("Exception: " . $e->getMessage());
        }


    }

    /**
     * Get order details including shipping address, customer name, and item names.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    private function getOrderDetails($order)
    {
        if($order->getShippingAddress()){
            $address = $order->getShippingAddress();
        } else {
            $address = $order->getBillingAddress();
        }
        $countryId = $address->getCountryId();
        $country = $this->countryFactory->create()->loadByCode($countryId);
        $countryName = $country->getName();

        // Get ordered item names
        $orderedItemNames = [];
        foreach ($order->getAllItems() as $item) {
            $orderedItemNames[] = $item->getName();
        }

        // Retrieve customer first name and last name
        $customerFirstName = $order->getCustomerFirstname();
        $customerLastName = $order->getCustomerLastname();

        // Concatenate the address elements into a single string
        $shippingAddressString = implode(', ', [
            $address->getStreetLine(1),
            $address->getStreetLine(2),
            $address->getCity(),
            $address->getPostcode(),
            $countryName
        ]);

        $orderDetails = [
            'order_id' => $order->getIncrementId(),
            'customer_name' => $customerFirstName . ' ' . $customerLastName,
            'customer_email' => $order->getCustomerEmail(),
            'shipping_address' => $shippingAddressString,
            'country' => $countryName,
            'item_names' => $orderedItemNames,
            // Add more fields as needed
        ];

        return $orderDetails;
    }

}