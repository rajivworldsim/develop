<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GoAPIResponseRepositoryInterface
{

    /**
     * Save Go_API_Response
     * @param \Worldsim\Databundle\Api\Data\GoAPIResponseInterface $goAPIResponse
     * @return \Worldsim\Databundle\Api\Data\GoAPIResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Worldsim\Databundle\Api\Data\GoAPIResponseInterface $goAPIResponse
    );

    /**
     * Retrieve Go_API_Response
     * @param string $goApiResponseId
     * @return \Worldsim\Databundle\Api\Data\GoAPIResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($goApiResponseId);

    /**
     * Retrieve Go_API_Response matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Worldsim\Databundle\Api\Data\GoAPIResponseSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Go_API_Response
     * @param \Worldsim\Databundle\Api\Data\GoAPIResponseInterface $goAPIResponse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Worldsim\Databundle\Api\Data\GoAPIResponseInterface $goAPIResponse
    );

    /**
     * Delete Go_API_Response by ID
     * @param string $goApiResponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($goApiResponseId);
}

