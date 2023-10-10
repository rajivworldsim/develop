<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\Model\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Magento\Framework\Model\AbstractModel;

class Link extends AbstractModel implements LinkInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(LinkInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(LinkInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLink()
    {
        return $this->_getData(LinkInterface::LINK);
    }

    /**
     * @inheritdoc
     */
    public function setLink($link)
    {
        $this->setData(LinkInterface::LINK, $link);

        return $this;
    }

    public function getLinkType(): int
    {
        return (int) $this->_getData(LinkInterface::TYPE);
    }

    public function setLinkType(int $linkType): void
    {
        $this->setData(LinkInterface::TYPE, $linkType);
    }

    public function getParentId(): int
    {
        return (int) $this->_getData(self::PARENT_ID);
    }

    public function setParentId(?int $parentId): void
    {
        $this->setData(self::PARENT_ID, $parentId);
    }

    public function getPath(): ?string
    {
        return $this->_getData(self::PATH);
    }

    public function setPath(?string $path): void
    {
        $this->setData(self::PATH, $path);
    }

    public function getLevel(): int
    {
        return (int) $this->_getData(self::LEVEL);
    }

    public function setLevel(int $level): void
    {
        $this->setData(self::LEVEL, $level);
    }
}
