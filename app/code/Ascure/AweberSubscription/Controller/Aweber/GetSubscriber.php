<?php
/**
 * Ascure AweberSubscription Controller
 *
 * @category    Ascure
 * @package     Ascure_AweberSubscription
 * @author      www.ascuretech.com
 * @copyright   Copyright (c) www.ascuretech.com. All rights reserved.
 * @license     https://www.ascuretech.com/license.html
 */
declare(strict_types=1);

namespace Ascure\AweberSubscription\Controller\Aweber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Ascure\AweberSubscription\Helper\Data;
use GuzzleHttp\Client;
use Magento\Newsletter\Model\SubscriberFactory;

class GetSubscriber extends Action
{
    const BASE_URL = 'https://api.aweber.com/1.0/';

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

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
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Data $helper
     * @param Client $httpClient
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Data $helper,
        Client $httpClient,
        SubscriberFactory $subscriberFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
        $this->httpClient = $httpClient;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Execute view action
     *
     * @return Json
     */
    public function execute(): Json
    {
        try {
            $name = $this->getRequest()->getParam('name');
            $email = $this->getRequest()->getParam('email');
            $country = $this->getRequest()->getParam('country');
            $accessToken = $this->helper->getAccessToken();
            if ($this->helper->isModuleEnabled()) {
                if ($email) {
                    // get all the accounts entries
                    $accounts = $this->helper->getCollection($this->httpClient, $accessToken, self::BASE_URL . 'accounts');

                    // get all the list entries for the first account
                    $listsUrl = $accounts[0]['lists_collection_link'];

                    $lists = $this->helper->getCollection($this->httpClient, $accessToken, $listsUrl);

                    // find out if a subscriber exists on the first list

                    $params = array(
                        'ws.op' => 'find',
                        'email' => $email
                    );

                    $subsUrl = $lists[0]['subscribers_collection_link'];
                    $findUrl = $subsUrl . '?' . http_build_query($params);

                    $foundSubscribers = $this->helper->getCollection($this->httpClient, $accessToken, $findUrl);


                    if (isset($foundSubscribers[0]['self_link'])) {
                        // update the subscriber if they are on the first list
                        $data = array(
                            'name' => $name,
                            'custom_fields' => array('country' => $country),
                        );
                        $subscriberUrl = $foundSubscribers[0]['self_link'];
                        $subscriberResponse = $this->httpClient->patch($subscriberUrl, [
                            'json' => $data,
                            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                        ])->getBody()->__toString();
                        $subscriber = json_decode($subscriberResponse, true);
                        $message = $name . " is already subscribed to AWeber with ". $email . ", Country : " . $country;
                    } else {
                        // add the subscriber if they are not already on the first list
                        $data = array(
                            'name' => $name,
                            'email' => $email,
                            'custom_fields' => array('country' => $country),
                        );
                        $body = $this->httpClient->post($subsUrl, [
                            'json' => $data,
                            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
                        ]);

                        // get the subscriber entry using the Location header from the post request
                        $subscriberUrl = $body->getHeader('Location')[0];
                        $subscriberResponse = $this->httpClient->get(
                            $subscriberUrl,
                            ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
                        )->getBody()->__toString();
                        $subscriber = json_decode($subscriberResponse, true);

                        $message = $name . " is Subscribed Successfully to AWeber with ". $email . ", Country : " . $country;

                        // Subscribe to the Magento default newsletter
                        $newsletterSubscriber = $this->subscriberFactory->create();
                        $newsletterSubscriber->subscribe($email);
                    }       
                } else {
                    $message = "Please enter an email address";
                    $subscriber = array();
                }
            } else {
                $message = "Aweber subscribtion module is not enable from admin";
                $subscriber = "";
            }

            $content = [
                'success' => true,
                'message' => $message,
                'subscriber' => $subscriber
            ];
        } catch (\Exception $e) {
            $content = [
                'success' => false,
                'message' => 'List Generation failed: ' . $e->getMessage()
            ];
        }

        $result = $this->jsonFactory->create();
        $result->setData($content);

        return $result;
    }
}
