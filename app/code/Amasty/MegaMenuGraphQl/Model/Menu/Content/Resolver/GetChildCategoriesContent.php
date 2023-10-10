<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model\Menu\Content\Resolver;

use Amasty\MegaMenuLite\Model\Menu\Content\Resolver\ResolverInterface;

class GetChildCategoriesContent implements ResolverInterface
{
    public function execute(): string
    {
        return '{{child_categories_content}}';
    }
}
