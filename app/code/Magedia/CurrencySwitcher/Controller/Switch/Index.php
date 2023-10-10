<?php
declare(strict_types=1);

namespace Magedia\CurrencySwitcher\Controller\Switch;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\PageCache\Model\Cache\Type;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Index implements HttpPostActionInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected  StoreManagerInterface $storeManager;

    /**
     * @var Type
     */
    protected Type $type;

    /**
     * @var PageRepositoryInterface
     */
    protected PageRepositoryInterface $pageRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var TypeListInterface
     */
    protected TypeListInterface $typeList;

    /**
     * @var Pool
     */
    protected Pool $pool;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultFactory;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PageRepositoryInterface $pageRepository
     * @param Type $type
     * @param TypeListInterface $typeList
     * @param Pool $pool
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PageRepositoryInterface $pageRepository,
        Type $type,
        TypeListInterface $typeList,
        Pool $pool,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ResultFactory $resultFactory
    ){
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->type = $type;
        $this->pool = $pool;
        $this->typeList = $typeList;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
    }


    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $currencyCode=$this->request->getParam('currencyCode');
        if ($this->request->getParam('currentCurrencyCode')){
            if ($this->request->getParam('currentCurrencyCode')!=$this->storeManager->getStore()->getCurrentCurrency()->getCode()){
                $response->setData(['reload' => true]);
            }
        }
        if ($this->request->getParam('currencyCode'))
        {
            try {
                if($this->storeManager->getStore()->getCode()!=$currencyCode) {
                    $this->cleanCache();
                    $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
                }

                $response->setData(['status' => true]);
            }
            catch (NoSuchEntityException $e) {
                $response->setData(['status' => false]);
            }
        }
        return $response;
    }
    /**
     * @return void
     */
    protected function cleanCache():void
    {
        $types = array_keys($this->typeList->getTypes());
        foreach ($types as $type) {
            $this->typeList->cleanType($type);
        }
        foreach ($this->pool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }

        foreach ($this->getCmsPageCollection() as $cmsPage){
            $this->type->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,[$cmsPage->getIdentifier()]);
        }
    }

    /**
     * @return \Exception|array
     */
    protected function getCmsPageCollection(): \Exception|array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $collection = $this->pageRepository->getList($searchCriteria)->getItems();
        } catch (\Exception $e) {
            return $e;
        }
        return $collection;
    }
}
