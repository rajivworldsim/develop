<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magento\Wishlist\CustomerData;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Wishlist\CustomerData\Wishlist;
use Magento\Checkout\Model\Session as CheckoutSession;

class WishlistPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Config
     */
    private $config;

    /**
     * WishlistPlugin constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param Config $config
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    /**
     * Transport datalayer to frontend local storage
     *
     * @param Wishlist $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Wishlist $subject, array $result): array
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $dataLayers = $this->checkoutSession->getMfAddToWishlistDataLayers();
        if ($dataLayers) {
            foreach ($dataLayers as $dataLayer) {
                $result['mf_datalayer'][] = $dataLayer;
            }
            $this->checkoutSession->setMfAddToWishlistDataLayers(null);
        }

        return $result;
    }
}
