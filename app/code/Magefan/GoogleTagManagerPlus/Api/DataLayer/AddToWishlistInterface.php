<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Api\DataLayer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Model\Item;

interface AddToWishlistInterface
{
    /**
     * Get GTM datalayer
     *
     * @param Item $wishlistItem
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(Item $wishlistItem): array;
}
