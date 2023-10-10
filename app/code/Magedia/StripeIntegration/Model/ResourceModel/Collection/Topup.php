<?php
declare(strict_types=1);

namespace Magedia\StripeIntegration\Model\ResourceModel\Collection;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magedia\StripeIntegration\Model\Topup as TopupModel;
use Magedia\StripeIntegration\Model\ResourceModel\Topup as TopupResourceModel;

class Topup extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(TopupModel::class, TopupResourceModel::class);
    }
}
