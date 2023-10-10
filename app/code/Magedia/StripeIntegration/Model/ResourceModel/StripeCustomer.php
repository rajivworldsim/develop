<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magedia\StripeIntegration\Api\Data\StripeCustomerInterface;

class StripeCustomer  extends AbstractDb
{

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeCustomerInterface::TABLE_NAME, StripeCustomerInterface::ENTITY_ID);
    }
}
