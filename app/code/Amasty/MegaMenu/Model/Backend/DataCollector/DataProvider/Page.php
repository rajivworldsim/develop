<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenu\Model\Backend\DataCollector\DataProvider;

use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\OptionSource\CmsPage;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Amasty\MegaMenuLite\Model\Backend\DataProvider\DataCollectorInterface;

class Page implements DataCollectorInterface
{
    public function execute(array $data, int $storeId, int $entityId): array
    {
        if ($data[LinkInterface::TYPE] == UrlKey::LANDING_PAGE) {
            $data['landing_page'] = $data[LinkInterface::PAGE_ID];
            $data[LinkInterface::PAGE_ID] = CmsPage::NO;
        }

        return $data;
    }
}
