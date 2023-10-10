<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Worldsim\Databundle\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GoAPIResponse extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('worldsim_databundle_go_api_response', 'go_api_response_id');
    }
}

