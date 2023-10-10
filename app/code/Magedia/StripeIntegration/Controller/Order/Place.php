<?php

declare(strict_types=1);

namespace Magedia\StripeIntegration\Controller\Order;

use Magedia\StripeIntegration\Model\ResourceModel\Topup;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\InstantPurchase\Model\InstantPurchaseOptionLoadingFactory;
use Magento\InstantPurchase\Model\PlaceOrder as PlaceOrderModel;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;
use Stripe\StripeClient;
use StripeIntegration\Payments\Exception\InvalidAddressException;
use StripeIntegration\Payments\Model\Config;
use Magento\Checkout\Model\Cart;
use StripeIntegration\Payments\Helper\ExpressHelper;
use StripeIntegration\Payments\Helper\Address;
use StripeIntegration\Payments\Helper\Generic;
use Magento\Framework\Event\ManagerInterface;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Api\Data\GroupInterface;
use Magedia\StripeIntegration\Model\TopupFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Place extends Action
{
    /**
     * @var TopupFactory
     */
    private TopupFactory $topupFactory;

    /**
     * @var CheckoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var Topup
     */
    private Topup $topup;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Cart
     */
    private Cart $cart;

    /**
     * @var CartManagementInterface
     */
    private CartManagementInterface $quoteManagement;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $eventManager;

    /**
     * @var Generic
     */
    private Generic $paymentsHelper;

    /**
     * @var ExpressHelper
     */
    private ExpressHelper $expressHelper;

    /**
     * @var Address
     */
    private Address $addressHelper;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var FormKeyValidator
     */
    private FormKeyValidator $formKeyValidator;

    /**
     * @var InstantPurchaseOptionLoadingFactory
     */
    private InstantPurchaseOptionLoadingFactory $instantPurchaseOptionLoadingFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var PlaceOrderModel
     */
    private PlaceOrderModel $placeOrder;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var Stripe
     */
    private Stripe $stripe;

    /**
     * @var Config
     */
    private Config $config;


    /**
     * @param CheckoutSession $checkoutSession
     * @param TopupFactory $topupFactory
     * @param Topup $topup
     * @param Address $addressHelper
     * @param ExpressHelper $expressHelper
     * @param Generic $paymentsHelper
     * @param ManagerInterface $eventManager
     * @param CartManagementInterface $quoteManagement
     * @param Cart $cart
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param InstantPurchaseOptionLoadingFactory $instantPurchaseOptionLoadingFactory
     * @param ProductRepositoryInterface $productRepository
     * @param PlaceOrderModel $placeOrder
     * @param OrderRepositoryInterface $orderRepository
     * @param Stripe $stripe
     * @param Config $config
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        TopupFactory $topupFactory,
        Topup $topup,
        Address $addressHelper,
        ExpressHelper $expressHelper,
        Generic $paymentsHelper,
        ManagerInterface $eventManager,
        CartManagementInterface $quoteManagement,
        Cart $cart,
        Context $context,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        InstantPurchaseOptionLoadingFactory $instantPurchaseOptionLoadingFactory,
        ProductRepositoryInterface $productRepository,
        PlaceOrderModel $placeOrder,
        OrderRepositoryInterface $orderRepository,
        Stripe $stripe,
        Config $config
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->topup = $topup;
        $this->topupFactory = $topupFactory;
        $this->addressHelper = $addressHelper;
        $this->expressHelper = $expressHelper;
        $this->paymentsHelper = $paymentsHelper;
        $this->eventManager = $eventManager;
        $this->quoteManagement = $quoteManagement;
        $this->cart = $cart;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->instantPurchaseOptionLoadingFactory = $instantPurchaseOptionLoadingFactory;
        $this->productRepository = $productRepository;
        $this->placeOrder = $placeOrder;
        $this->orderRepository = $orderRepository;
        $this->stripe = $stripe;
        $this->config = $config;
    }

    public function execute()
    {
        $this->stripe::setApiKey($this->config->getSecretKey());
        $stripeCheckoutSession = StripeCheckoutSession::retrieve($this->getRequest()->getParam('session_id'));
        $stripeAdmin = new StripeClient($this->config->getSecretKey());
        $paymentIntent = $stripeAdmin->paymentIntents->retrieve($stripeCheckoutSession->payment_intent);
        $paymentMethodStripe = $stripeAdmin->paymentMethods->retrieve($paymentIntent->payment_method);
        $paymentMethod = json_decode($paymentMethodStripe->getLastResponse()->body,true);
        $paymentMethod['billing_details']['phone'] = $this->getRequest()->getParam('telephone');
        $paymentMethodId = $paymentMethodStripe->id;

        $quote = $this->cart->getQuote();
        $quote->setIsWalletButton(true);

        try {
            // Create an Order ID for the customer's quote
            $quote->reserveOrderId()->save(); // Warning: The may cause order ID skipping if the customer abandons the checkout

            // Set Billing Address
            $billingAddress = $this->expressHelper->getBillingAddress($paymentMethod['billing_details']);
            $quote->getBillingAddress()
                ->addData($billingAddress);

            if (!$quote->isVirtual())
            {
                // Set Shipping Address
                try
                {
                    $shippingAddress = $billingAddress;
                }
                catch (InvalidAddressException $e)
                {
                    $data = $quote->getShippingAddress()->getData();
                    $shippingAddress = $this->addressHelper->filterAddressData($data);
                }

                if ($this->addressHelper->isRegionRequired($shippingAddress["country_id"]))
                {
                    if (empty($shippingAddress["region"]) && empty($shippingAddress["region_id"]))
                    {
                        throw new LocalizedException(__("Please specify a shipping address region/state."));
                    }
                }

                if (empty($shippingAddress["telephone"]) && !empty($billingAddress["telephone"]))
                    $shippingAddress["telephone"] = $billingAddress["telephone"];

                $shipping = $quote->getShippingAddress()
                    ->addData($shippingAddress);

                // Set Shipping Method

                    $shipping->setShippingMethod(null)
                        ->setCollectShippingRates(true);

            }

            // Update totals
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();

            // For multi-stripe account configurations, load the correct Stripe API key from the correct store view
            $this->storeManager->setCurrentStore($quote->getStoreId());
            $this->config->initStripe();

            // Set Checkout Method
            if (!$this->customerSession->isLoggedIn())
            {
                // Use Guest Checkout
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST)
                    ->setCustomerId(0)
                    ->setCustomerEmail($quote->getBillingAddress()->getEmail())
                    ->setCustomerIsGuest(true)
                    ->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
            }
            else
            {
                $quote->setCheckoutMethod(Onepage::METHOD_CUSTOMER);
            }

            $quote->getPayment()->unsPaymentId(); // Causes the Helper/Generic.php::resetPaymentData() method to reset any previous values
            $quote->getPayment()->importData(['method' => 'stripe_payments_express', 'additional_data' => [
                'cc_stripejs_token' => $paymentMethodId,
                'is_prapi' => true,
                'prapi_location' => 'en_US',
                'prapi_title' => $this->paymentsHelper->getPRAPIMethodType()
            ]]);

            // Save Quote
            $this->paymentsHelper->saveQuote($quote);

            // Place Order
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->quoteManagement->submit($quote);
            if ($order)
            {
                $this->eventManager->dispatch(
                    'checkout_type_onepage_save_order_after',
                    ['order' => $order, 'quote' => $quote]
                );

                // if ($order->getCanSendNewEmailFlag()) {
                //     try {
                //         $this->orderSender->send($order);
                //     } catch (\Exception $e) {
                //         $this->logger->critical($e);
                //     }
                // }

                $this->checkoutSession
                    ->setLastQuoteId($quote->getId())
                    ->setLastSuccessQuoteId($quote->getId())
                    ->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId())
                    ->setLastOrderStatus($order->getStatus());
            }

            $topUpModel = $this->topupFactory->create();
            $topUpModel->setCount($order->getTotalInvoiced());
            $topUpModel->setCurrency($order->getOrderCurrency()->getCode());
            $topUpModel->setOrderIncrementId($order->getIncrementId());
            $topUpModel->setCustomerEmail($order->getAddresses()[0]->getEmail());
            $topUpModel->setStripePaymentMethod($paymentMethodId);
            $topUpModel->setStripePaymentIntent($stripeCheckoutSession->payment_intent);
            $topUpModel->setStripeCustomerId($stripeCheckoutSession->customer);
            $topUpModel->setUsuallyPayment((float)$this->getRequest()->getParam('usually_payment'));
            $this->topup->save($topUpModel);

            $this->eventManager->dispatch(
                'checkout_submit_all_after',
                [
                    'order' => $order,
                    'quote' => $quote
                ]
            );

            return $this->_redirect($this->_url->getUrl('checkout/onepage/success', ['_secure' => $this->paymentsHelper->isSecure()]));
        }
        catch (\Exception $e)
        {
            return $this->paymentsHelper->dieWithError($e->getMessage(), $e);
        }

    }
}
