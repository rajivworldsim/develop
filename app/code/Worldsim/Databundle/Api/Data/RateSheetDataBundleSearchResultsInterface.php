<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api\Data;

interface RateSheetDataBundleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get rate_sheet_data_bundle list.
     * @return \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Worldsim\Databundle\Api\Data\RateSheetDataBundleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

