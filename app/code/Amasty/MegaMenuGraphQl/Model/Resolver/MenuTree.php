<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model\Resolver;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Content\Resolver;
use Amasty\MegaMenu\Model\OptionSource\Font;
use Amasty\MegaMenuGraphQl\Model\Di\Wrapper;
use Amasty\MegaMenuLite\Model\Menu\Frontend\GetItemData;
use Amasty\MegaMenuLite\Model\Menu\TreeResolver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class MenuTree implements ResolverInterface
{
    /**
     * @var TreeResolver
     */
    private $treeResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Resolver
     */
    private $contentResolver;

    /**
     * @var Uid
     */
    private $uidEncoder;

    public function __construct(
        TreeResolver $treeResolver,
        StoreManagerInterface $storeManager,
        Resolver $contentResolver,
        Wrapper $uidEncoder
    ) {
        $this->treeResolver = $treeResolver;
        $this->storeManager = $storeManager;
        $this->contentResolver = $contentResolver;
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $data = $this->treeResolver->get((int) $context->getExtensionAttributes()->getStore()->getId());

        return ['items' => $this->prepareData($data)];
    }

    protected function prepareData(Node $tree): array
    {
        $data = [];
        $items = $tree->getChildren()->getNodes();
        foreach ($items as $key => $item) {
            /** @var Node $item */
            $itemData = $this->convertData($item);

            $children = $item->getChildren()->getNodes();
            if ($children) {
                $itemData['children'] = $this->prepareData($item);
            }

            $data[] = $itemData;
        }

        return $data;
    }

    protected function convertData(Node $item): array
    {
        $itemData = $item->getData();
        $itemData['uid'] = $this->getUidEncoder()->encode(
            str_replace(GetItemData::CATEGORY_NODE_PREFIX, '', (string) $itemData[ItemInterface::ID])
        );
        $itemData['url'] = $this->makeRelativePath($itemData['url' ?? '']);
        $itemData[ItemInterface::ICON] = $this->makeRelativePath($itemData['icon'] ?? '');
        $itemData[ItemInterface::SUBMENU_TYPE] = (int)$item->getSubmenuType();
        $itemData[ItemInterface::SUBCATEGORIES_POSITION] = (int)$item->getSubcategoriesPosition();
        $itemData[ItemInterface::CONTENT] = $this->contentResolver->resolve($item);
        $itemData[ItemInterface::DESKTOP_FONT] = $itemData[ItemInterface::DESKTOP_FONT] ?? Font::BOLD;
        $itemData[ItemInterface::MOBILE_FONT] = $itemData[ItemInterface::MOBILE_FONT] ?? Font::BOLD;
        $itemData['model'] = $item;

        return $itemData;
    }

    protected function makeRelativePath(string $path): string
    {
        return str_replace([$this->getBaseUrl(), $this->storeManager->getStore()->getBaseUrl()], '', $path);
    }

    protected function getBaseUrl(): string
    {
        if ($this->baseUrl === null) {
            $store = $this->storeManager->getStore();
            $isSecure = $store->isUrlSecure();

            $this->baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, $isSecure);
        }

        return $this->baseUrl;
    }

    /**
     * @return Wrapper
     */
    public function getUidEncoder(): Wrapper
    {
        return $this->uidEncoder;
    }
}
