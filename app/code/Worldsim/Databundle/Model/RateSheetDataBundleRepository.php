<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface;
use Worldsim\Databundle\Api\Data\RateSheetDataBundleInterfaceFactory;
use Worldsim\Databundle\Api\Data\RateSheetDataBundleSearchResultsInterfaceFactory;
use Worldsim\Databundle\Api\RateSheetDataBundleRepositoryInterface;
use Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle as ResourceRateSheetDataBundle;
use Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle\CollectionFactory as RateSheetDataBundleCollectionFactory;

class RateSheetDataBundleRepository implements RateSheetDataBundleRepositoryInterface
{

    /**
     * @var RateSheetDataBundle
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceRateSheetDataBundle
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var RateSheetDataBundleInterfaceFactory
     */
    protected $rateSheetDataBundleFactory;

    /**
     * @var RateSheetDataBundleCollectionFactory
     */
    protected $rateSheetDataBundleCollectionFactory;


    /**
     * @param ResourceRateSheetDataBundle $resource
     * @param RateSheetDataBundleInterfaceFactory $rateSheetDataBundleFactory
     * @param RateSheetDataBundleCollectionFactory $rateSheetDataBundleCollectionFactory
     * @param RateSheetDataBundleSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRateSheetDataBundle $resource,
        RateSheetDataBundleInterfaceFactory $rateSheetDataBundleFactory,
        RateSheetDataBundleCollectionFactory $rateSheetDataBundleCollectionFactory,
        RateSheetDataBundleSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->rateSheetDataBundleFactory = $rateSheetDataBundleFactory;
        $this->rateSheetDataBundleCollectionFactory = $rateSheetDataBundleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        RateSheetDataBundleInterface $rateSheetDataBundle
    ) {
        try {
            $this->resource->save($rateSheetDataBundle);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rateSheetDataBundle: %1',
                $exception->getMessage()
            ));
        }
        return $rateSheetDataBundle;
    }

    /**
     * @inheritDoc
     */
    public function get($rateSheetDataBundleId)
    {
        $rateSheetDataBundle = $this->rateSheetDataBundleFactory->create();
        $this->resource->load($rateSheetDataBundle, $rateSheetDataBundleId);
        if (!$rateSheetDataBundle->getId()) {
            throw new NoSuchEntityException(__('rate_sheet_data_bundle with id "%1" does not exist.', $rateSheetDataBundleId));
        }
        return $rateSheetDataBundle;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->rateSheetDataBundleCollectionFactory->create();
        
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
    public function delete(
        RateSheetDataBundleInterface $rateSheetDataBundle
    ) {
        try {
            $rateSheetDataBundleModel = $this->rateSheetDataBundleFactory->create();
            $this->resource->load($rateSheetDataBundleModel, $rateSheetDataBundle->getRateSheetDataBundleId());
            $this->resource->delete($rateSheetDataBundleModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rate_sheet_data_bundle: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($rateSheetDataBundleId)
    {
        return $this->delete($this->get($rateSheetDataBundleId));
    }
}

