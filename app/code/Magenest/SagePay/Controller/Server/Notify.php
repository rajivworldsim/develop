<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 18/01/2019
 * Time: 9:41
 */

namespace Magenest\SagePay\Controller\Server;

use Magenest\SagepayLib\Classes\Constants;
use Magento\Quote\Model\CustomerManagement;
use Magento\Quote\Model\QuoteValidator;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magenest\SagepayLib\Classes\SagepayUtil;

/**
 *
 */
class Notify extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magenest\SagePay\Helper\SageHelper
     */
    protected $sageHelper;

    /**
     * @var \Magenest\SagePay\Helper\Logger
     */
    protected $sageLogger;

    /**
     * @var \Magento\Quote\Model\QuoteValidator
     */
    protected $quoteValidator;

    /**
     * @var \Magento\Quote\Model\CustomerManagement
     */
    protected $customerManagement;

    /**
     * @var \Magenest\SagePay\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magenest\SagePay\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Magenest\SagePay\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magenest\SagePay\Model\ResourceModel\Transaction $transactionResource
     * @param \Magenest\SagePay\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magenest\SagePay\Helper\SageHelper $sageHelper
     * @param \Magenest\SagePay\Helper\Logger $sageLogger
     * @param \Magento\Quote\Model\CustomerManagement $customerManagement
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magenest\SagePay\Helper\Data $dataHelper
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magenest\SagePay\Model\ResourceModel\Transaction $transactionResource,
        \Magenest\SagePay\Model\TransactionFactory $transactionFactory,
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\SagePay\Helper\SageHelper $sageHelper,
        \Magenest\SagePay\Helper\Logger $sageLogger,
        CustomerManagement $customerManagement,
        QuoteValidator $quoteValidator,
        \Magenest\SagePay\Helper\Data $dataHelper,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->coreRegistry = $registry;
        $this->_formKeyValidator = $formKeyValidator;
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->sageHelper = $sageHelper;
        $this->sageLogger = $sageLogger;
        $this->quoteValidator = $quoteValidator;
        $this->customerManagement = $customerManagement;
        $this->dataHelper = $dataHelper;
        $this->cache = $cache;
        $this->transactionFactory = $transactionFactory;
        $this->transactionResource = $transactionResource;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $data = [];
            $response = $this->getRequest()->getParams();
            $this->sageLogger->debug("Begin SagePay Server notify");
            $transaction = $this->transactionFactory->create();
            $this->transactionResource->load($transaction,$response['VendorTxCode'] ?? '','vendor_tx_code');
            $quote = $this->quoteRepository->get($transaction->getQuoteId());
            $payment = $quote->getPayment();
            $siteFqdn = $this->sageHelper->getSageApiConfigArray()['website'];
            $vtxData = filter_input_array(INPUT_POST);
            $transactionId = isset($vtxData['VPSTxId']) ? $vtxData['VPSTxId'] : '';
            $transactionId = str_replace(["{", "}"], "", $transactionId);

            $this->_eventManager->dispatch(
                "magenest_sagepay_save_transaction",
                ['transaction_data' => $this->sageHelper->getResponseData($vtxData, $quote, "Server")]
            );

            $this->sageLogger->debug(var_export($vtxData, true));
            $paymentProfile = $this->sageHelper->getPaymentProfileMode();
            if ($paymentProfile == Constants::SAGEPAY_SERVER_PROFILE_LOW) {
                $redirectUrl = $siteFqdn . 'sagepay/server/redirectsuccess?vtx=' . filter_input(INPUT_POST, 'VendorTxCode');
            } else {
                $redirectUrl = $siteFqdn . 'sagepay/server/success?vtx=' . filter_input(INPUT_POST, 'VendorTxCode');
            }
            if (in_array(
                filter_input(INPUT_POST, 'Status'),
                [
                    Constants::SAGEPAY_REMOTE_STATUS_OK,
                    Constants::SAGEPAY_REMOTE_STATUS_AUTHENTICATED,
                    Constants::SAGEPAY_REMOTE_STATUS_REGISTERED
                ]
            )) {
                $surcharge = floatval(filter_input(INPUT_POST, 'Surcharge', FILTER_VALIDATE_FLOAT));
                $vtxData['Amount'] = $payment->getAmount() + $surcharge;
                if (filter_input(INPUT_POST, 'TxType') == Constants::SAGEPAY_REMOTE_STATUS_PAYMENT) {
                    $vtxData['CapturedAmount'] = $vtxData['Amount'];
                }
                $data = [
                    "Status" => Constants::SAGEPAY_REMOTE_STATUS_OK,
                    "RedirectURL" => $redirectUrl,
                    "StatusDetail" => __('The transaction was successfully processed.')
                ];
            } else {
                $data = [
                    "Status" => Constants::SAGEPAY_REMOTE_STATUS_OK,
                    "RedirectURL" => $redirectUrl,
                    "StatusDetail" => filter_input(INPUT_POST, 'StatusDetail')
                ];
            }
            $vtxData['AllowGiftAid'] = filter_input(INPUT_POST, 'GiftAid');
            $payment->addData($vtxData);
            $payment->setAdditionalInformation('profile', $paymentProfile);
            foreach ($response as $key => $value) {
                $payment->setAdditionalInformation($key, $value);
            }
            $payment->save();
            $vendorTxCode = isset($vtxData['VendorTxCode']) ? $vtxData['VendorTxCode'] : '';
            if ($vendorTxCode) {
                $this->cache->save(json_encode($data), $vendorTxCode);
            }
            if ($payment->getAdditionalInformation('can_save_card') && isset($response['Token'])) {
                $card['cardType'] = $response['CardType'] ?? '';
                $card['lastFourDigits'] = $response['Last4Digits'] ?? '';
                $card['expiryDate'] = $response['ExpiryDate'] ?? '';
                $card['cardIdentifier'] = $response['Token'] ?? '';
                $card['reusable'] = 1;
                $card['payment_method'] = $payment->getMethod();
                $this->sageHelper->saveCard($quote->getCustomerId(), $card);
            }
        } catch (\Exception $exception) {
            $this->sageLogger->debug('Server notify error '.$exception->getMessage());
        }
        return $this->resultFactory->create('raw')->setContents(SagepayUtil::arrayToQueryString($data, "\n"));
    }
}
