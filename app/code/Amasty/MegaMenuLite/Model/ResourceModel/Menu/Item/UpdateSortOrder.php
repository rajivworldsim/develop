<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item;

use Magento\Framework\App\ResourceConnection;

class UpdateSortOrder
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(array $items): void
    {
        $this->resourceConnection->getConnection()->insertOnDuplicate(
            $this->resourceConnection->getTableName(Position::TABLE),
            $items,
            [Position::POSITION]
        );
    }
}
