<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magento\Checkout\CustomerData;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;

class CartPlugin
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
     * CartPlugin constructor.
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
     * @param Cart $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Cart $subject, array $result): array
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $dataLayers = $this->checkoutSession->getMfAddToCartDataLayers();
        if ($dataLayers) {
            foreach ($dataLayers as $dataLayer) {
                $result['mf_datalayer'][] = $dataLayer;
            }
            $this->checkoutSession->setMfAddToCartDataLayers(null);
        }

        $dataLayers = $this->checkoutSession->getMfRemoveFromCartDataLayers();
        if ($dataLayers) {
            foreach ($dataLayers as $dataLayer) {
                $result['mf_datalayer'][] = $dataLayer;
            }
            $this->checkoutSession->setMfRemoveFromCartDataLayers(null);
        }

        return $result;
    }
}
