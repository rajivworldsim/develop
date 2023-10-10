<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer\Wishlist;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\Wishlist\ItemInterface;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(\Magento\Wishlist\Model\Item $wishlistItem): array
    {
        $product = $wishlistItem->getProduct();

        $categoryNames = $this->getCategoryNames($product);
        return array_merge(array_filter([
            'item_id' => ($this->config->getProductAttribute() == 'sku') ?
                $product->getSku() :
                $product->getData($this->config->getProductAttribute()),
            'item_name' => $product->getName(),
            'item_brand' => $this->config->getBrandAttribute() ?
                $product->getData($this->config->getBrandAttribute()) : '',
            'price' => $this->getPrice($product),
            'quantity' => $wishlistItem->getQty() * 1
        ]), $categoryNames);
    }
}
