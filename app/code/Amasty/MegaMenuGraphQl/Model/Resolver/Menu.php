<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model\Resolver;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class Menu extends MenuTree implements ResolverInterface
{
    protected function prepareData(Node $tree): array
    {
        $data = [];
        $items = $tree->getChildren()->getNodes();
        $parentId = $tree->getId();
        foreach ($items as $key => $item) {
            /** @var ItemInterface $item */
            $itemData = $this->convertData($item);
            $itemData['parent_id'] = $parentId;
            $itemData['parent_uid'] = $this->getUidEncoder()->encode(
                str_replace(GetItemData::CATEGORY_NODE_PREFIX, '', (string) $parentId)
            );
            $data[] = $itemData;

            $children = $item->getChildren()->getNodes();
            if ($children) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $data = array_merge($data, $this->prepareData($item));
            }
        }

        return $data;
    }
}
