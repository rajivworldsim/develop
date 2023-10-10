<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel\Collection;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magedia\StripeIntegration\Model\StripeProduct as StripeProductModel;
use Magedia\StripeIntegration\Model\ResourceModel\StripeProduct as StripeProductResourceModel;

class StripeProduct extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(StripeProductModel::class, StripeProductResourceModel::class);
    }
}
