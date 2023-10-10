<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Menu\Content\Resolver;

class GetChildCategoriesContent implements ResolverInterface
{
    public function execute(): string
    {
        return '<!-- ko scope: "index = ammenu_columns_wrapper" --> '
            . '<!-- ko template: getTemplate() --><!-- /ko --> '
            . '<!-- /ko -->';
    }
}
