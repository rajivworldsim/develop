<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magefan\GoogleTagManager\Model;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Model\Config as PlusConfig;
use Magefan\GoogleTagManager\Model\Container;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ContainerPlugin
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PlusConfig
     */
    private $plusConfig;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $containerId;

    /**
     * ContainerPlugin constructor.
     *
     * @param DateTime $dateTime
     * @param Config $config
     * @param PlusConfig $plusConfig
     */
    public function __construct(
        DateTime $dateTime,
        Config $config,
        PlusConfig $plusConfig
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
        $this->plusConfig = $plusConfig;
    }

    /**
     * Generate JSON container
     *
     * @param Container $subject
     * @param array $result
     * @param string|null $storeId
     * @return array
     */
    public function afterGenerate(Container $subject, array $result, string $storeId = null): array
    {
        $this->timestamp = $this->timestamp ?: (string)$this->dateTime->timestamp();
        $this->accountId = $this->accountId ?: $this->config->getAccountId($storeId);
        $this->containerId = $this->containerId ?: $this->config->getContainerId($storeId);
        $isAnalyticsEnabled = $this->config->isAnalyticsEnabled($storeId);

        if ($this->isGoogleAdsEnabled($storeId)) {
            $result['containerVersion']['tag'] = array_merge(
                $result['containerVersion']['tag'],
                $this->generateTags($storeId)
            );
        }

        $triggers = $result['containerVersion']['trigger'];
        if ($isAnalyticsEnabled) {
            foreach ($triggers as $key => $trigger) {
                if ($trigger['triggerId'] != 162 && $trigger['triggerId'] != 167) {
                    continue;
                }
                unset($triggers[$key]);
            }

            $triggers = array_merge($triggers, $this->getRemarketingTriggers());

            if (!$this->isGoogleAdsEnabled($storeId)) {
                $result['containerVersion']['trigger'] = array_merge(
                    $triggers,
                    $this->generateTriggers($storeId)
                );
            }
        } else {
            if ($this->plusConfig->isRemarketingEnabled($storeId)) {
                $triggers = array_merge($triggers, $this->getRemarketingTriggers());
            }
        }

        if ($this->isGoogleAdsEnabled($storeId)) {
            $result['containerVersion']['trigger'] = array_merge(
                $triggers,
                $this->generateTriggers($storeId)
            );
            $result['containerVersion']['variable'] = array_merge(
                $result['containerVersion']['variable'],
                $this->generateVariables($storeId)
            );
        }

        if (!$isAnalyticsEnabled && $this->isGoogleAdsEnabled($storeId)) {
            $result['containerVersion']['builtInVariable'] = [
                [
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    'type' => 'EVENT',
                    'name' => 'Event'
                ]
            ];
        }

        return $result;
    }

    /**
     * Get triggers for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateTriggers(string $storeId = null): array
    {
        $triggers[] = [
            'accountId' => $this->accountId,
            'containerId' => $this->containerId,
            'triggerId' => '162',
            'name' => 'Magefan GTM - Configuration',
            'type' => 'PAGEVIEW',
            'fingerprint' => $this->timestamp
        ];

        $triggerNames = [
            'View Item List',
            'Add To Cart',
            'Remove From Cart',
            'Add Payment Info',
            'Add Shipping Info',
            'Add To Wishlist'
        ];
        foreach ($triggerNames as $key => $triggerName) {
            $triggers[] = [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'triggerId' => 173 + $key,
                'name' => 'Magefan GTM - ' . $triggerName,
                'type' => 'CUSTOM_EVENT',
                'customEventFilter' => [
                    [
                        'type' => 'EQUALS',
                        'parameter' => [
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg1',
                                'value' => strtolower(str_replace(' ', '_', $triggerName))
                            ]
                        ]
                    ]
                ],
                'fingerprint' => $this->timestamp
            ];
        }

        return $triggers;
    }

    /**
     * Get remarketing triggers
     *
     * @return array
     */
    private function getRemarketingTriggers(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'triggerId' => '167',
                'name' => 'Magefan GTM - Ecommerce',
                'type' => 'CUSTOM_EVENT',
                'customEventFilter' => [
                    [
                        'type' => 'MATCH_REGEX',
                        'parameter' => [
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg1',
                                'value' => 'view_item|view_cart|purchase|begin_checkout|view_item_list|add_to_cart|remove_from_cart|add_payment_info|add_shipping_info|add_to_wishlist' // phpcs:ignore
                            ]
                        ]
                    ]
                ],
                'fingerprint' => $this->timestamp
            ]
        ];
    }

    /**
     * Get tags for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateTags(string $storeId = null): array
    {
        $tags = [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '183',
                'name' => 'Magefan - Conversion Linker',
                'type' => 'gclidw',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableCrossDomain',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableUrlPassthrough',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableCookieOverrides',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '162'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ],
        ];

        if ($this->plusConfig->isConversionTrackingEnabled($storeId)) {
            $tags = array_merge($tags, $this->getConversionTags($storeId));
        }

        if ($this->plusConfig->isRemarketingEnabled($storeId)) {
            $tags = array_merge($tags, $this->getRemarketingTags($storeId));
        }

        return $tags;
    }

    /**
     * Get conversion tags
     *
     * @param string|null $storeId
     * @return array
     */
    private function getConversionTags(string $storeId = null): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '184',
                'name' => 'Magefan Google Ads - Conversion Tracking',
                'type' => 'awct',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableNewCustomerReporting',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'orderId',
                        'value' => '{{Magefan DLV - Transaction ID}}'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableProductReporting',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableEnhancedConversion',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionValue',
                        'value' => '{{Magefan DLV - Value}}'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionCookiePrefix',
                        'value' => '_gcl'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableShippingData',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getPurchaseConversionId($storeId)
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'currencyCode',
                        'value' => '{{Magefan DLV - Currency}}'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionLabel',
                        'value' => $this->plusConfig->getPurchaseConversionLabel($storeId)
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '170'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ]
        ];
    }

    /**
     * Get remarketing tags
     *
     * @param string|null $storeId
     * @return array
     */
    private function getRemarketingTags(string $storeId = null): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '258',
                'name' => 'Magefan Google Ads - Remarketing',
                'type' => 'sp',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableDynamicRemarketing',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'customParams',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_prodid'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan DLV - Item ID}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_category'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan DLV - Category}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_totalvalue'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan DLV - Value}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionCookiePrefix',
                        'value' => '_gcl'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getRemarketingId($storeId)
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'customParamsFormat',
                        'value' => 'USER_SPECIFIED'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '167'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '259',
                'name' => 'Magefan Google Ads - Gtag',
                'type' => 'sp',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableDynamicRemarketing',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionCookiePrefix',
                        'value' => '_gcl'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getRemarketingId($storeId)
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'customParamsFormat',
                        'value' => 'NONE'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '162'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ]
        ];
    }

    /**
     * Get variables for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateVariables(string $storeId = null): array
    {
        $variables = [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '253',
                'name' => 'Magefan DLV - Value',
                'type' => 'v',
                'parameter' => [
                    [
                        'type' => 'INTEGER',
                        'key' => 'dataLayerVersion',
                        'value' => '2'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'setDefaultValue',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'name',
                        'value' => 'ecommerce.value'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ]
        ];

        if ($this->plusConfig->isConversionTrackingEnabled($storeId)) {
            $variables = array_merge($variables, $this->getConversionVariables());
        }

        if ($this->plusConfig->isRemarketingEnabled($storeId)) {
            $variables = array_merge($variables, $this->getRemarketingVariables());
        }

        return $variables;
    }

    /**
     * Get conversion variables
     *
     * @return array
     */
    private function getConversionVariables()
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '254',
                'name' => 'Magefan DLV - Transaction ID',
                'type' => 'v',
                'parameter' => [
                    [
                        'type' => 'INTEGER',
                        'key' => 'dataLayerVersion',
                        'value' => '2'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'setDefaultValue',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'name',
                        'value' => 'ecommerce.transaction_id'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '255',
                'name' => 'Magefan DLV - Currency',
                'type' => 'v',
                'parameter' => [
                    [
                        'type' => 'INTEGER',
                        'key' => 'dataLayerVersion',
                        'value' => '2'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'setDefaultValue',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'name',
                        'value' => 'ecommerce.currency'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ]
        ];
    }

    /**
     * Get remarketing variables
     *
     * @return array
     */
    private function getRemarketingVariables(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '260',
                'name' => 'Magefan DLV - Item ID',
                'type' => 'v',
                'parameter' => [
                    [
                        'type' => 'INTEGER',
                        'key' => 'dataLayerVersion',
                        'value' => '2'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'setDefaultValue',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'name',
                        'value' => 'ecommerce.items.0.item_id'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '261',
                'name' => 'Magefan DLV - Category',
                'type' => 'v',
                'parameter' => [
                    [
                        'type' => 'INTEGER',
                        'key' => 'dataLayerVersion',
                        'value' => '2'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'setDefaultValue',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'name',
                        'value' => 'ecommerce.items.0.category'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ]
        ];
    }

    /**
     * Check if at least one Google Ads option enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    private function isGoogleAdsEnabled(string $storeId = null): bool
    {
        return $this->plusConfig->isRemarketingEnabled($storeId) ||
            $this->plusConfig->isConversionTrackingEnabled($storeId);
    }
}
