<?php

declare(strict_types=1);

namespace Magedia\StripeIntegration\Ui\DataProvider\Topup\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('entity_id', 'main_table.entity_id');

        parent::_initSelect();
    }
}
