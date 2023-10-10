<?php


namespace Agtech\ProductObject\Block;
use Magento\Framework\View\Element\Template;

class Objproductconv extends Template
{

    public function __construct(
		\Magento\Reports\Model\ResourceModel\Report\Collection\Factory $collectionReportFactory,
		\Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection $bestsellerCollection,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $CollectionFactory,
		\Magento\Catalog\Helper\Image $Helperimage,
		\Magento\Store\Model\StoreManagerInterface $StoreManager,
		\Magento\Checkout\Helper\Cart	$CartHelper,
		\Magento\Catalog\Model\Product $ProductModel,
		\Magento\Catalog\Model\Product\Option $ProductOptionModel,
		\Magento\Directory\Model\CurrencyFactory $CurrencyFactoryModel,
		\Magento\Framework\Registry $ProductRegistry,
		\Magento\Customer\Model\Session $CustomerSession
    )
    {
		$this->_collectionReportFactory = $collectionReportFactory;
		$this->_bestsellerCollection = $bestsellerCollection;
		$this->_collectionFactory = $CollectionFactory;
		$this->_helperImage = $Helperimage;
		$this->_storeManager = $StoreManager;
		$this->_cartHelper = $CartHelper;
		$this->_productModel = $ProductModel;
		$this->_productOptionModel = $ProductOptionModel;
		$this->_currencyFactoryModel = $CurrencyFactoryModel;
		$this->_registryCurrent = $ProductRegistry;
		$this->_customerSessionModel = $CustomerSession;
    }
	public function getColletionReport(){
		return $this->_collectionReportFactory;
	}
	public function getColletionBest(){
		return $this->_bestsellerCollection;
	}
	public function getCollectionFactoryResource(){
		return $this->_collectionFactory;
	}
	public function getHelperimagep(){
		return $this->_helperImage;
	}
	public function getStoremangerInter(){
		return $this->_storeManager;
	}
	public function getCarthelperObj(){
		return $this->_cartHelper;
	}
	public function getproductModel(){
		return $this->_productModel;
	}
	public function getproductOptionModel(){
		return $this->_productOptionModel;
	}
	public function getcurrencyFactoryModel(){
		return $this->_currencyFactoryModel;
	}
	public function getRegistryCurrentId(){
		return $this->_registryCurrent;
	}
	public function getcustomerSession(){
		return $this->_customerSessionModel;
	}
	
}