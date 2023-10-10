<?php
namespace Agtech\CmsBlocks\Block;

class HomepageProducts extends \Magento\Framework\View\Element\Template
{
    protected $productCollectionFactory;
    protected $categoryFactory;
    protected $_storeManager;
    
    public function __construct(
       \Magento\Framework\View\Element\Template\Context $context,
       \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
       \Magento\Catalog\Model\CategoryFactory $categoryFactory,
       \Magento\Store\Model\StoreManagerInterface $storemanager,
       array $data = []
    ){
       $this->productCollectionFactory = $productCollectionFactory;
       $this->categoryFactory = $categoryFactory;
       $this->_storeManager =  $storemanager;
       parent::__construct($context, $data);
    }  
    public function getProductCollection()
    {        
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name','price', 'image']);
        //$collection->addAttributeToFilter('homepage_product', ['eq' => 1]);
		$collection->addAttributeToFilter('homepage_product',1);
        $collection->setPageSize(3);
        $collection->setOrder('entity_id', 'DESC');
        
        return $collection;
    }
    
    Public function getProductImageUrl($product)
    {
        $store = $this->_storeManager->getStore();    
        $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getImage();
        return $productImageUrl;
    }
}