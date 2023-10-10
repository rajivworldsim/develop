<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Worldsim\Databundle\Api\Data\GoAPIResponseInterface;
use Worldsim\Databundle\Api\Data\GoAPIResponseInterfaceFactory;
use Worldsim\Databundle\Api\Data\GoAPIResponseSearchResultsInterfaceFactory;
use Worldsim\Databundle\Api\GoAPIResponseRepositoryInterface;
use Worldsim\Databundle\Model\ResourceModel\GoAPIResponse as ResourceGoAPIResponse;
use Worldsim\Databundle\Model\ResourceModel\GoAPIResponse\CollectionFactory as GoAPIResponseCollectionFactory;

class GoAPIResponseRepository implements GoAPIResponseRepositoryInterface
{

    /**
     * @var ResourceGoAPIResponse
     */
    protected $resource;

    /**
     * @var GoAPIResponseCollectionFactory
     */
    protected $goAPIResponseCollectionFactory;

    /**
     * @var GoAPIResponse
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var GoAPIResponseInterfaceFactory
     */
    protected $goAPIResponseFactory;


    /**
     * @param ResourceGoAPIResponse $resource
     * @param GoAPIResponseInterfaceFactory $goAPIResponseFactory
     * @param GoAPIResponseCollectionFactory $goAPIResponseCollectionFactory
     * @param GoAPIResponseSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGoAPIResponse $resource,
        GoAPIResponseInterfaceFactory $goAPIResponseFactory,
        GoAPIResponseCollectionFactory $goAPIResponseCollectionFactory,
        GoAPIResponseSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->goAPIResponseFactory = $goAPIResponseFactory;
        $this->goAPIResponseCollectionFactory = $goAPIResponseCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(GoAPIResponseInterface $goAPIResponse)
    {
        try {
            $this->resource->save($goAPIResponse);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the goAPIResponse: %1',
                $exception->getMessage()
            ));
        }
        return $goAPIResponse;
    }

    /**
     * @inheritDoc
     */
    public function get($goAPIResponseId)
    {
        $goAPIResponse = $this->goAPIResponseFactory->create();
        $this->resource->load($goAPIResponse, $goAPIResponseId);
        if (!$goAPIResponse->getId()) {
            throw new NoSuchEntityException(__('Go_API_Response with id "%1" does not exist.', $goAPIResponseId));
        }
        return $goAPIResponse;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->goAPIResponseCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(GoAPIResponseInterface $goAPIResponse)
    {
        try {
            $goAPIResponseModel = $this->goAPIResponseFactory->create();
            $this->resource->load($goAPIResponseModel, $goAPIResponse->getGoApiResponseId());
            $this->resource->delete($goAPIResponseModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Go_API_Response: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($goAPIResponseId)
    {
        return $this->delete($this->get($goAPIResponseId));
    }
}

