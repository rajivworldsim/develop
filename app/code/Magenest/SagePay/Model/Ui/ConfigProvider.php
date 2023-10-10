<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 18/01/2019
 * Time: 9:41
 */

namespace Magenest\SagePay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\ScopeInterface;
use Magenest\SagePay\Helper\SageHelper;

/**
 *
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var
     */
    protected $_helper;

    /**
     * @var \Magenest\SagePay\Model\CardFactory
     */
    protected $_cardFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magenest\SagePay\Helper\SageHelper
     */
    protected $sageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magenest\SagePay\Helper\SageHelperMoto
     */
    protected $sageHelperMoto;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magenest\SagePay\Model\Source\SageCctype
     */
    protected $ccTypeSource;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     *
     */
    const CODE = 'magenest_sagepay';

    /**
     *
     */
    const DIRECT_CODE = 'magenest_sagepay_direct';

    /**
     *
     */
    const PAYPAL_CODE = 'magenest_sagepay_paypal';

    /**
     * @var \Magenest\SagePay\Model\ResourceModel\Card\CollectionFactory
     */
    protected $cardCollection;

    /**
     * @param \Magenest\SagePay\Model\ResourceModel\Card\CollectionFactory $cardCollection
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magenest\SagePay\Helper\SageHelper $sageHelper
     * @param \Magenest\SagePay\Model\CardFactory $cardFactory
     * @param \Magenest\SagePay\Helper\SageHelperMoto $sageHelperMoto
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magenest\SagePay\Model\Source\SageCctype $cctype
     */
    public function __construct(
        \Magenest\SagePay\Model\ResourceModel\Card\CollectionFactory $cardCollection,
        PaymentHelper $paymentHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        SageHelper $sageHelper,
        \Magenest\SagePay\Model\CardFactory $cardFactory,
        \Magenest\SagePay\Helper\SageHelperMoto $sageHelperMoto,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magenest\SagePay\Model\Source\SageCctype $cctype
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_urlBuilder = $urlBuilder;
        $this->sageHelper = $sageHelper;
        $this->_cardFactory = $cardFactory;
        $this->sageHelperMoto = $sageHelperMoto;
        $this->ccTypeSource = $cctype;
        $this->_assetRepo = $assetRepo;
        $this->cardCollection = $cardCollection;
    }

    /**
     * @return \array[][]
     */
    public function getConfig()
    {
        $cardData = $this->getDataCard();
        $directSavedCard = json_encode($this->cardCollection->create()
                                                            ->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId())
                                                            ->addFieldToFilter('payment_method',['null' => true])
                                                            ->getData());
        return [
            'payment' => [
                "magenest_sagepay"=>[
                    'isSave' => (boolean)$this->sageHelper->getCanSave(),
                    'isGiftAid' => (boolean)$this->sageHelper->isGiftAid(),
                    'isSandbox' => (boolean)$this->sageHelper->getIsSandbox(),
                    'instruction' => $this->sageHelper->getInstructions(),
                    'saveCards' => $directSavedCard,
                    'hasCard' => count($cardData) > 0,
                    'useDropin' => (boolean)$this->sageHelper->useDropIn(),
                    'dropinMode' => $this->sageHelper->getDropInMode()
                ],
                "magenest_sagepay_direct" => [
                    'isSave' => (boolean)$this->sageHelper->getCanSave(),
                    'isGiftAid' => (boolean)$this->sageHelper->isGiftAid(),
                    'isSandbox' => (boolean)$this->sageHelper->getIsSandbox(),
                    'instruction' => $this->sageHelper->getInstructions(),
                    'saveCards' => $directSavedCard,
                    'cardType' => json_encode($this->getAllowCardTypesConfig()),
                    'hasCard' => count($cardData) > 0,
                    'useDropin' => (boolean)$this->sageHelper->useDropIn(),
                ],
                "magenest_sagepay_paypal" => [
                    'redirect_url' => $this->getUrl('sagepay/paypal/redirect'),
                    'paypal_icon' => $this->_assetRepo->getUrl('Magenest_SagePay::images/paypal_icon.png')
                ],
                "magenest_sagepay_server" => [
                    'isSave' => (boolean) $this->scopeConfig->getValue(SageHelper::SAGEPAY_SERVER_CAN_SAVE_CARD, ScopeInterface::SCOPE_STORE),
                    'enable_3ds2' => (boolean) $this->scopeConfig->getValue(SageHelper::SAGEPAY_SERVER_ENABLE_3DS2, ScopeInterface::SCOPE_STORE),
                    'saveCards' => json_encode($this->cardCollection->create()
                                                                    ->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId())
                                                                    ->addFieldToFilter('payment_method','magenest_sagepay_server')
                                                                    ->getData()),
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getDataCard()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomerId();
            return $this->_cardFactory->create()->loadCards($customerId);
        } else {
            return [];
        }
    }

    /**
     * @param $route
     * @param $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @return array
     */
    public function getAllowCardTypesConfig()
    {
        $returnOptions = [];
        $config = $this->scopeConfig->getValue('payment/magenest_sagepay_direct/cctypes', ScopeInterface::SCOPE_STORE);
        $allowType = explode(',', $config);
        $allType = $this->ccTypeSource->getAllSageCardType();
        if (is_array($allowType)) {
            foreach ($allowType as $value) {
                if (isset($allType[$value])) {
                    $returnOptions[] =
                        [
                            'value' => $value,
                            'label' => $allType[$value]
                        ]
                    ;
                }
            }
        }
        return $returnOptions;
    }
}
