<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\AddToCartInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Quote\Model\Quote\Item;
use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddToCart extends AbstractDataLayer implements AddToCartInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * AddToCart constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ItemInterface $item
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ItemInterface $item
    ) {
        $this->gtmItem = $item;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     */
    public function get(Item $quoteItem): array
    {
        return $this->eventWrap([
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->formatPrice((float)$quoteItem->getPrice()),
                'items' => [
                    $this->gtmItem->get($quoteItem)
                ]
            ]
        ]);
    }
}
