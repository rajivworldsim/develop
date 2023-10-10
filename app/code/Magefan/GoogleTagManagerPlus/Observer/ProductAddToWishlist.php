<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Observer;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\AddToWishlistInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductAddToWishlist implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var AddToWishlistInterface
     */
    private $addToWishlist;

    /**
     * ProductAddToWishlistAfter constructor.
     *
     * @param Config $config
     * @param CheckoutSession $checkoutSession
     * @param AddToWishlistInterface $addToWishlist
     */
    public function __construct(
        Config $config,
        CheckoutSession $checkoutSession,
        AddToWishlistInterface $addToWishlist
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->addToWishlist = $addToWishlist;
    }

    /**
     * Set datalayer on add product to wishlist
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $wishlistItem = $observer->getData('item');
            $dataLayers = $this->checkoutSession->getMfAddToWishlistDataLayers() ?: [];
            $dataLayers[] = $this->addToWishlist->get($wishlistItem);
            $this->checkoutSession->setMfAddToWishlistDataLayers($dataLayers);

        }
    }
}
