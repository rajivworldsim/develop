<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Magento\Framework\Data\Tree\Node;

interface ModifyNodeDataInterface
{
    public function execute(Node $node, array $data): array;
}
