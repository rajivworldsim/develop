<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RateSheetDataBundleRepositoryInterface
{

    /**
     * Save rate_sheet_data_bundle
     * @param \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface $rateSheetDataBundle
     * @return \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface $rateSheetDataBundle
    );

    /**
     * Retrieve rate_sheet_data_bundle
     * @param string $rateSheetDataBundleId
     * @return \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($rateSheetDataBundleId);

    /**
     * Retrieve rate_sheet_data_bundle matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Worldsim\Databundle\Api\Data\RateSheetDataBundleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete rate_sheet_data_bundle
     * @param \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface $rateSheetDataBundle
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface $rateSheetDataBundle
    );

    /**
     * Delete rate_sheet_data_bundle by ID
     * @param string $rateSheetDataBundleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($rateSheetDataBundleId);
}

