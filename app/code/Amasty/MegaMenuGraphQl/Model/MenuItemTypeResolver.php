<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

/**
 * {@inheritdoc}
 */
class MenuItemTypeResolver implements TypeResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolveType(array $data) : string
    {
        if (isset($data['id'])) {
            return strpos($data['id'], 'category') !== false ? 'MenuCategoryItem' : 'MenuCustomItem';
        }

        return '';
    }
}
