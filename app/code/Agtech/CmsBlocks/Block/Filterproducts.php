<?php
namespace Agtech\CmsBlocks\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class Filterproducts extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    protected $productCollectionFactory;
    protected $storeManager;
    protected $catalogConfig;
    protected $productVisibility;
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, \Magento\Catalog\Model\Layer\Resolver $layerResolver, CategoryRepositoryInterface $categoryRepository, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $productVisibility, array $data = [])
    {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->catalogConfig = $context->getCatalogConfig();
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $data);
    }
    public function getProducts()
    {
        $storeId = $this
            ->storeManager
            ->getStore()
            ->getId();
        $products = $this
            ->productCollectionFactory
            ->create()
            ->setStoreId($storeId);
        $todayDate = date('Y-m-d', time());
        $products->addAttributeToSelect($this
            ->catalogConfig
            ->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this
            ->productVisibility
            ->getVisibleInCatalogIds())
            ->addAttributeToFilter('news_from_date', array(
            'date' => true,
            'to' => $todayDate
        ))->addAttributeToFilter('news_to_date', array(
            'date' => true,
            'from' => $todayDate
        ))->addAttributeToSort('news_from_date', 'desc');
        $products->setPageSize($this->getConfig('qty'))
            ->setCurPage(1);
        $this
            ->_eventManager
            ->dispatch('catalog_block_product_list_collection', ['collection' => $products]);
        return $products;
    }

    
    public function getHomePageProducts()
    {
        $storeId = $this
            ->storeManager
            ->getStore()
            ->getId();
        $products = $this
            ->productCollectionFactory
            ->create()
            ->setStoreId($storeId);
        $products->addAttributeToSelect($this
            ->catalogConfig
            ->getProductAttributes())
            //->addAttributeToFilter('entity_id', array('in'=>$producIds))
            ->addAttributeToFilter('homepage_products', array('eq' => '1')) //Filter Products Based on HomePage Product Attribute
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addUrlRewrite()
            ->setVisibility($this
            ->productVisibility
            ->getVisibleInCatalogIds());
        $products->setPageSize($this->getConfig('qty'))
            ->setCurPage(1);
        $this
            ->_eventManager
            ->dispatch('catalog_block_product_list_collection', ['collection' => $products]);
        return $products;
    }

}
