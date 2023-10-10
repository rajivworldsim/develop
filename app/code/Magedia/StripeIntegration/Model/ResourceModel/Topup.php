<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magedia\StripeIntegration\Api\Data\TopupInterface;

class Topup  extends AbstractDb
{

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(TopupInterface::TABLE_NAME, TopupInterface::ENTITY_ID);
    }
}
