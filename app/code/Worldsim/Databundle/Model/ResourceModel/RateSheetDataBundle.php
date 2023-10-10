<?php
/**
 * Copyright © Worldsim_Databundle All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RateSheetDataBundle extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('worldsim_databundle_rate_sheet_data_bundle', 'id');
    }
}

