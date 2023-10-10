<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * Google Ads config
     */
    public const XML_PATH_GOOGLE_ADS_CONVERSION_ENABLE = 'mfgoogletagmanager/ads/enable';
    public const XML_PATH_GOOGLE_ADS_PURCHASE_CONVERSION_ID = 'mfgoogletagmanager/ads/purchase/conversion_id';
    public const XML_PATH_GOOGLE_ADS_PURCHASE_CONVERSION_LABEL = 'mfgoogletagmanager/ads/purchase/conversion_label';
    public const XML_PATH_GOOGLE_ADS_REMARKETING_ENABLE = 'mfgoogletagmanager/remarketing/enable';
    public const XML_PATH_GOOGLE_ADS_REMARKETING_ID = 'mfgoogletagmanager/remarketing/conversion_id';
    public const XML_PATH_GOOGLE_ADS_REMARKETING_LABEL = 'mfgoogletagmanager/remarketing/conversion_label';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve true if conversion tracking enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isConversionTrackingEnabled(string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_GOOGLE_ADS_CONVERSION_ENABLE, $storeId) &&
            $this->getPurchaseConversionId($storeId) &&
            $this->getPurchaseConversionLabel($storeId);
    }

    /**
     * Retrieve Google Ads conversion ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getPurchaseConversionId(string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GOOGLE_ADS_PURCHASE_CONVERSION_ID, $storeId));
    }

    /**
     * Retrieve Google Ads conversion label
     *
     * @param string|null $storeId
     * @return string
     */
    public function getPurchaseConversionLabel(string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GOOGLE_ADS_PURCHASE_CONVERSION_LABEL, $storeId));
    }

    /**
     * Retrieve true if remarketing tracking enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isRemarketingEnabled(string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_GOOGLE_ADS_REMARKETING_ENABLE, $storeId) &&
            $this->getRemarketingId($storeId);
    }

    /**
     * Retrieve Google Ads remarketing ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getRemarketingId(string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GOOGLE_ADS_REMARKETING_ID, $storeId));
    }

    /**
     * Retrieve Google Ads remarketing Label
     *
     * @param string|null $storeId
     * @return string
     */
    public function getRemarketingLabel(string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GOOGLE_ADS_REMARKETING_LABEL, $storeId));
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param string|null $storeId
     * @return mixed
     */
    public function getConfig(string $path, string $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
