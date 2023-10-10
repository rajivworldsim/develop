<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

class MenuIdentity implements IdentityInterface
{
    /**
     * @var string
     */
    private $cacheMenu = 'menu';

    public function getIdentities(array $resolvedData): array
    {
        $ids = [];
        $items = $resolvedData['items'] ?? [];
        foreach ($items as $item) {
            $ids[] = sprintf('%s_%s', $this->cacheMenu, $item['id']);
        }

        return $ids;
    }
}
