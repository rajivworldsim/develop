<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magedia\StripeIntegration\Api\Data\StripeProductInterface;

class StripeProduct  extends AbstractDb
{

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeProductInterface::TABLE_NAME, StripeProductInterface::ENTITY_ID);
    }
}
