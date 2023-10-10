<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Plugin\Menu\Content\Resolver;

use Amasty\MegaMenu\Model\Menu\Content\Resolver;
use Magento\Framework\Data\Tree\Node;

class DisableCategoriesContent
{
    public function aroundResolveCategoriesContent(Resolver $subject, callable $proceed, Node $node): string
    {
        return Resolver::CHILD_CATEGORIES;
    }
}
