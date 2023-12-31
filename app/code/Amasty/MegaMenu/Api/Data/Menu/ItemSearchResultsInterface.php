<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Api\Data\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemSearchResultsInterface as ItemSearchResultsInterfaceLite;

interface ItemSearchResultsInterface extends ItemSearchResultsInterfaceLite
{
    /**
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface[]
     */
    public function getItems();

    /**
     * @param \Amasty\MegaMenu\Api\Data\Menu\ItemInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
