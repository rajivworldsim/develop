<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model\ResourceModel\GoAPIResponse;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'go_api_response_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Worldsim\Databundle\Model\GoAPIResponse::class,
            \Worldsim\Databundle\Model\ResourceModel\GoAPIResponse::class
        );
    }
}

