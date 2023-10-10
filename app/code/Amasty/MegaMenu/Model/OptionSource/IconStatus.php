<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class IconStatus implements OptionSourceInterface
{
    public const ENABLED = 'both';

    public const DESKTOP = 'desktop';

    public const MOBILE = 'mobile';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Both Desktop and Mobile')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('Desktop Only')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Mobile Only')
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            self::ENABLED => __('Both Desktop and Mobile'),
            self::DESKTOP => __('Desktop Only'),
            self::MOBILE => __('Mobile Only')
        ];
    }
}
