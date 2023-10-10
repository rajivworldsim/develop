<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Block\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;

class ViewItemList extends AbstractDataLayer
{
    /**
     * @var array
     */
    private $items;

    /**
     * Set items
     *
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get GTM datalayer for pages with item list
     *
     * @return array
     */
    protected function getDataLayer(): array
    {
        return $this->getItems();
    }
}
