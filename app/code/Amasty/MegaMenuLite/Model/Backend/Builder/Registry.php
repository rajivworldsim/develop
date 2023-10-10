<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Backend\Builder;

class Registry
{
    /**
     * @var int|null
     */
    private $storeId;

    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    public function setStoreId(int $storeId): void
    {
        if ($this->storeId !== null) {
            throw new \RuntimeException('Registry key "store_id" already exists');
        }
        $this->storeId = $storeId;
    }

    public function unsetStoreId(): void
    {
        $this->storeId = null;
    }
}
