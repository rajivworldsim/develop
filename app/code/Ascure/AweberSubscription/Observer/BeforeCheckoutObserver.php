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
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Exception\LocalizedException;

class BeforeCheckoutObserver implements ObserverInterface
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
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * BeforeCheckoutObserver constructor.
     *
     * @param LoggerInterface $logger
     * @param Order $order
     * @param CountryFactory $countryFactory
     * @param Data $helper
     * @param Client $httpClient
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Order $order,
        CountryFactory $countryFactory,
        Data $helper,
        Client $httpClient,
        SubscriberFactory $subscriberFactory
    ) {
        $this->logger = $logger;
        $this->order = $order;
        $this->countryFactory = $countryFactory;
        $this->helper = $helper;
        $this->httpClient = $httpClient;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Execute method triggered before placing an order.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->helper->isModuleEnabled()) {
                $this->logger->info("Observer executed: before place order");
                $accessToken = $this->helper->getAccessToken();

                // Get the most recent order
                $order = $observer->getEvent()->getOrder();
                if ($order) {
                    $orderDetails = $this->getOrderDetails($order);

                    // Log order details
                    $this->logger->info("Order Details:\n" . json_encode($orderDetails, JSON_PRETTY_PRINT));

                    $email = $orderDetails['customer_email'];
                    $country = $orderDetails['country'];
                    $shippingAddress = $orderDetails['shipping_address'];
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

                        if (isset($foundSubscribers[0]['self_link'])) {
                            // Update the subscriber if they are on the first list
                            $data = array(
                                'name' => $customerName,
                                'custom_fields' => array('country' => $country, 'shippingAddress' => $shippingAddress),
                            );
                            $subscriberUrl = $foundSubscribers[0]['self_link'];
                            $subscriberResponse = $this->httpClient->patch($subscriberUrl, [
                                'json' => $data,
                                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                            ])->getBody()->__toString();
                            $subscriber = json_decode($subscriberResponse, true);
                            $message = $email . " is already subscribed from " . $country . " Country and Address is updated: " . $shippingAddress;
                        } else {
                            // Add the subscriber if they are not already on the first list
                            $data = array(
                                'name' => $customerName,
                                'email' => $email,
                                'custom_fields' => array('country' => $country, 'shippingAddress' => $shippingAddress),
                            );
                            $body = $this->httpClient->post($subsUrl, [
                                'json' => $data,
                                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                            ]);

                            // Get the subscriber entry using the Location header from the post request
                            $subscriberUrl = $body->getHeader('Location')[0];
                            $subscriberResponse = $this->httpClient->get(
                                $subscriberUrl,
                                ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
                            )->getBody()->__toString();
                            $subscriber = json_decode($subscriberResponse, true);

                            $message = $email . " Subscribed Successfully with Country: " . $country . " and Address: " . $shippingAddress;
                            // Subscribe to the Magento default newsletter
                            //$newsletterSubscriber = $this->subscriberFactory->create();
                            //$newsletterSubscriber->subscribe($email);
                        }
                    } else {
                        $message = "Please enter an email address";
                        $subscriber = array();
                    }

                    // Example success response
                    $this->logger->info("Message:\n" . json_encode($message, JSON_PRETTY_PRINT));
                } else {
                    $this->logger->info("No recent order found.");
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
     * Get order details including shipping address and customer name.
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

        // Concatenate the address elements into a single string
        $shippingAddressString = implode(', ', [
            $address->getStreetLine(1),
            $address->getStreetLine(2),
            $address->getCity(),
            $address->getPostcode(),
            $countryName
        ]);

        // Get customer first name and last name
        $customerFirstName = $order->getCustomerFirstname();
        $customerLastName = $order->getCustomerLastname();

        $orderDetails = [
            'customer_name' => $customerFirstName . ' ' . $customerLastName,
            'customer_email' => $order->getCustomerEmail(),
            'shipping_address' => $shippingAddressString,
            'country' => $countryName,
            // Add more fields as needed
        ];

        return $orderDetails;
    }
}