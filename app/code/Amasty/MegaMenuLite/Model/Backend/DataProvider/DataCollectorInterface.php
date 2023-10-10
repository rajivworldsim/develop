<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Backend\DataProvider;

interface DataCollectorInterface
{
    public function execute(array $data, int $storeId, int $entityId): array;
}
