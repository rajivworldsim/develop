<?php
namespace Agtech\CmsBlocks\ViewModel;

class TarrifWidget implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Agtech\CmsBlocks\Model\ResourceModel\CollectionFactory
     */
    protected $jsonDataFactory;
    /**
     * @var Agtech\CmsBlocks\Model\ResourceModel\VirtualRates\CollectionFactory;
     */
    protected $virualRateFactory;

    /**
     * @var  \Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews\CollectionFactory;
     */
    protected $GoogleReviewsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface;
     */
    protected $storeManagerInterface;

    /**
     * @var  \Magento\Framework\App\ResourceConnection;
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory;
     */
    protected $currencyFactory;

    /**
     * @param Agtech\CmsBlocks\Model\ResourceModel\CollectionFactory
     * @param Agtech\CmsBlocks\Model\ResourceModel\VirtualRates\CollectionFactory;
     * @param \Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews\CollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\App\ResourceConnection
     * @param \Magento\Directory\Model\CurrencyFactory
     */
    public function __construct(
        \Agtech\CmsBlocks\Model\ResourceModel\CollectionFactory $jsonDataFactory,
        \Agtech\CmsBlocks\Model\ResourceModel\VirtualRates\CollectionFactory $virualRateFactory,
        \Agtech\CmsBlocks\Model\ResourceModel\GoogleReviews\CollectionFactory $GoogleReviewsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->jsonDataFactory = $jsonDataFactory;
        $this->virualRateFactory = $virualRateFactory;
        $this->GoogleReviewsFactory = $GoogleReviewsFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->resourceConnection = $resourceConnection;
        $this->currencyFactory = $currencyFactory;
    }
    public function getAllCountryFrom() {
        $jsonData = $this->jsonDataFactory->create();
        $countryCollection = $jsonData->addFieldToSelect('country')->addFieldToFilter('outgoing',array('gt' => 0))->addFieldToFilter('country',array('notnull' => true))->addFieldToFilter('operator',array('notnull' => true))->distinct('country');
        return $countryCollection;
    }
    public function getAllCountryTo() {
        $jsonData = $this->jsonDataFactory->create();
        $countryCollection = $jsonData->addFieldToSelect('country')->addFieldToFilter('outgoing',array('gt' => 0))->addFieldToFilter('country',array('notnull' => true))->distinct('country');
        return $countryCollection;
    }
    public function getEsimCountryFrom() {
        $jsonData = $this->jsonDataFactory->create();
        $countryCollection = $jsonData->addFieldToSelect('country')->addFieldToFilter('outgoing',array('gt' => 0))->addFieldToFilter('country',array('notnull' => true))->addFieldToFilter('operator',array('notnull' => true))->addFieldToFilter('recommended_profile',array(array('like' => 'Manx%')))->distinct('country');
        return $countryCollection;
    }
    public function getOperatorRates() {
        $Collection = $this->jsonDataFactory->create()->addFieldToSelect('country')->addFieldToFilter('outgoing_bleg',array('gt' => 0))->addFieldToFilter('country',array('notnull' => true))->distinct('country');
        return $Collection;
    }
    public function getCurrencySymbol() {
        $currency = $this->storeManagerInterface->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currency);
        $currencySymbol = $currency->getCurrencySymbol();
        return $currencySymbol;
    }
    public function getCurrencyRate() { 
        $currency = $this->storeManagerInterface->getStore()->getCurrentCurrencyCode();
        $rates_currency = $this->storeManagerInterface->getStore()->getCurrentCurrencyRate();
        return $rates_currency;
    }
    public function getVirtualRateCountry() {
        $countryCollection = 'SELECT DISTINCT country_name FROM worldsim_virtual_number_rates where active=1 AND country_name <> "" ORDER BY country_name ASC';
        $countryCollection = $this->resourceConnection->getConnection()->fetchAll($countryCollection);
        return $countryCollection;
    }
    public function getVirtualOperatorRates() {
        $Collection = $this->virualRateFactory->create();
        return $Collection;
    }
    public function getDataSimCountry() {
        $countryCollection = 'SELECT DISTINCT country FROM worldsim_uksim_rates_from_new WHERE data>0 AND country <> "" ORDER BY country ASC';
        $countryCollection = $this->resourceConnection->getConnection()->fetchAll($countryCollection);
        return $countryCollection;
    }
    public function getDataSimRate($country) {
        $countryCollection = 'SELECT DISTINCT operator,data,country FROM worldsim_uksim_rates_from_new WHERE data>0 AND country="'.$country.'" AND operator <> "" ORDER BY data ASC';
        $countryCollection = $this->resourceConnection->getConnection()->fetchAll($countryCollection);
        return $countryCollection;
    }
    public function getGoogleReviews() {
        return $this->GoogleReviewsFactory->create();
    }
}
