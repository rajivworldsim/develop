<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ascure\AweberSubscription\Controller\Manage;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Ascure\AweberSubscription\Helper\Data;
use GuzzleHttp\Client;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Customers newsletter subscription save controller
 */
class Save extends \Magento\Newsletter\Controller\Manage implements HttpPostActionInterface, HttpGetActionInterface
{
    const BASE_URL = 'https://api.aweber.com/1.0/';

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var SubscriptionManagerInterface
     */
    private $subscriptionManager;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param Validator $formKeyValidator
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepository $customerRepository
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param Data $helper
     * @param Client $httpClient
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Validator $formKeyValidator,
        StoreManagerInterface $storeManager,
        CustomerRepository $customerRepository,
        SubscriptionManagerInterface $subscriptionManager,
        Data $helper,
        Client $httpClient,
        SubscriberFactory $subscriberFactory
    ) {
        parent::__construct($context, $customerSession);
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->subscriptionManager = $subscriptionManager;
        $this->helper = $helper;
        $this->httpClient = $httpClient;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Save newsletter subscription preference action
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/');
        }

        $customerId = $this->_customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $storeId = (int) $this->storeManager->getStore()->getId();
                $customer->setStoreId($storeId);
                $isSubscribedState = $customer->getExtensionAttributes()->getIsSubscribed();
                $isSubscribedParam = (boolean) $this->getRequest()->getParam('is_subscribed', false);
                if ($isSubscribedParam !== $isSubscribedState) {
                    // No need to validate customer and customer address while saving subscription preferences
                    $this->setIgnoreValidationFlag($customer);
                    $this->customerRepository->save($customer);
                    if ($isSubscribedParam) {
                        $subscribeModel = $this->subscriptionManager->subscribeCustomer((int) $customerId, $storeId);
                        $subscribeStatus = (int) $subscribeModel->getStatus();
                        if ($subscribeStatus === Subscriber::STATUS_SUBSCRIBED) {
                            $this->messageManager->addSuccess(__('We have saved your subscription.'));
                        } else {
                            $this->messageManager->addSuccess(__('A confirmation request has been sent.'));
                        }
                    } else {
                        $this->subscriptionManager->unsubscribeCustomer((int) $customerId, $storeId);

                        if ($this->helper->isModuleEnabled()) {
                            // Get customer email using customer ID
                            $email = $customer->getEmail();
                            $accessToken = $this->helper->getAccessToken();
                            if ($email) {
                                $accounts = $this->helper->getCollection($this->httpClient, $accessToken, self::BASE_URL . 'accounts');
                                $listsUrl = $accounts[0]['lists_collection_link'];
                                $lists = $this->helper->getCollection($this->httpClient, $accessToken, $listsUrl);
                                $params = array(
                                    'ws.op' => 'find',
                                    'email' => $email
                                );
                                $subsUrl = $lists[0]['subscribers_collection_link'];
                                $findUrl = $subsUrl . '?' . http_build_query($params);
                                $foundSubscribers = $this->helper->getCollection($this->httpClient, $accessToken, $findUrl);
                                $subscriberUrl = $foundSubscribers[0]['self_link'];
                                if (isset($foundSubscribers[0]['self_link'])) {
                                    if ($foundSubscribers[0]['status'] == 'subscribed') {
                                        $this->httpClient->delete(
                                            $subscriberUrl,
                                            ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
                                        )->getBody();
                                        $this->messageManager->addSuccess(__($email . 'is unsubscribe From Aweber '));
                                    }
                                }
                            }
                        }


                        $this->messageManager->addSuccess(__('We have removed your newsletter subscription.'));
                    }
                } else {
                    $this->messageManager->addSuccess(__('We have updated your subscription.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('customer/account/');
    }

    /**
     * Set ignore_validation_flag to skip unnecessary address and customer validation
     *
     * @param CustomerInterface $customer
     * @return void
     */
    private function setIgnoreValidationFlag(CustomerInterface $customer): void
    {
        $customer->setData('ignore_validation_flag', true);
    }
}