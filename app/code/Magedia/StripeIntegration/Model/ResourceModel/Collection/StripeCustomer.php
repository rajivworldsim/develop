<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel\Collection;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magedia\StripeIntegration\Model\StripeCustomer as StripeCustomerModel;
use Magedia\StripeIntegration\Model\ResourceModel\StripeCustomer as StripeCustomerResourceModel;

class StripeCustomer extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeCustomerModel::class, StripeCustomerResourceModel::class);
    }
}
