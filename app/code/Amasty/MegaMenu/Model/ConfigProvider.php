<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Model;

use Amasty\MegaMenuLite\Model\ConfigProvider as ConfigProviderLite;

class ConfigProvider extends ConfigProviderLite
{
    public const STICKY_ENABLED = 'general/sticky';

    public const SHOW_ICONS = 'general/show_icons';

    public const VIEW_ALL_ENABLED = 'general/hide_view_all_link';

    public const MOBILE_TEMPLATE = 'general/mobile_template';

    public function getStickyEnabled(?int $storeId = null): int
    {
        return (int) $this->getValue(self::STICKY_ENABLED, $storeId);
    }

    public function getIconsStatus(): string
    {
        return (string) $this->getValue(self::SHOW_ICONS);
    }

    public function getMobileTemplateClass(?int $storeId = null): ?string
    {
        return (string) $this->getValue(self::MOBILE_TEMPLATE, $storeId);
    }

    public function isHideViewAllLink(?int $storeId = null): ?bool
    {
        return (bool)$this->isSetFlag(self::VIEW_ALL_ENABLED, $storeId);
    }
}
