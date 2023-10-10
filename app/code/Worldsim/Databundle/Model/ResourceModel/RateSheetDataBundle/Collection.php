<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Worldsim\Databundle\Model\RateSheetDataBundle::class,
            \Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle::class
        );
    }
}

