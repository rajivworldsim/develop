<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty Mega Menu GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\MegaMenuGraphQl\Model\Resolver;

use Amasty\MegaMenu\Block\Product\ProductsSlider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class Widget implements ResolverInterface
{
    /**
     * @var \Amasty\MegaMenu\Block\Product\ProductsSlider
     */
    private $productsSlider;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection
     */
    private $widgetCollection;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    public function __construct(
        \Amasty\MegaMenu\Block\Product\ProductsSlider $productsSlider,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Catalog\Helper\Product\Compare $compareHelper,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection
    ) {
        $this->productsSlider = $productsSlider;
        $this->widgetCollection = $widgetCollection;
        $this->serializer = $serializer;
        $this->compareHelper = $compareHelper;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            $this->setSliderData($args['id'], (int) $context->getExtensionAttributes()->getStore()->getId());
            $products = $this->productsSlider->createCollection();
            $data = $this->productsSlider->getData();
            $data['items'] = [];
            foreach ($products as $product) {
                $productData = $product->getData();
                $productData['model'] = $product;
                $data['items'][] = $productData;
            }
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        }

        return $data;
    }

    /**
     * @param int $id
     * @param int $storeId
     * @throws \Exception
     */
    private function setSliderData(int $id, int $storeId)
    {
        $widget = $this->widgetCollection->getItemById($id);
        if (!$this->validateStore($widget, $storeId)) {
            throw new LocalizedException(__('Wrong parameter storeId.'));
        }
        $widgetParams = $widget->getWidgetParameters();

        if (isset($widgetParams['slider_autoplay_speed']) && $widgetParams['slider_autoplay_speed'] === '') {
            $widgetParams['slider_autoplay_speed'] = ProductsSlider::DEFAULT_SLIDER_AUTOPLAY_SPEED;
        }

        if (isset($widgetParams['slider_items_show']) && $widgetParams['slider_items_show'] === '') {
            $widgetParams['slider_items_show'] = ProductsSlider::DEFAULT_SLIDES_TO_SHOW;
        }

        $this->productsSlider->setNameInLayout('ammenu_products_list');
        $this->productsSlider->setData('store_id', $storeId);
        $this->productsSlider->setData($widgetParams);
        $this->productsSlider->setData(
            'conditions',
            $this->serializer->serialize($this->productsSlider->getConditions())
        );
    }

    /**
     * @param $widget
     * @param int $storeId
     * @return bool
     */
    private function validateStore($widget, int $storeId)
    {
        return in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $widget->getStoreIds())
            || in_array($storeId, $widget->getStoreIds());
    }
}
