<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Api\Data;

interface GoAPIResponseSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Go_API_Response list.
     * @return \Worldsim\Databundle\Api\Data\GoAPIResponseInterface[]
     */
    public function getItems();

    /**
     * Set iccid list.
     * @param \Worldsim\Databundle\Api\Data\GoAPIResponseInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

