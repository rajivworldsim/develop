<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_StoreviewFlags
 * @author     Extension Team
 * @copyright  Copyright (c) 2019-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreviewFlags\Plugin;

/**
 * Class ShowFlagFrontend
 *
 * @package Bss\StoreviewFlags\Plugin
 */
class ShowFlagFrontend
{
    /**
     * @var \Bss\StoreviewFlags\Helper\Data
     */
    protected $helper;

    /**
     * ShowFlagFrontend constructor.
     *
     * @param \Bss\StoreviewFlags\Helper\Data $helper
     */
    public function __construct(
        \Bss\StoreviewFlags\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Plugin After
     *
     * @param \Magento\Store\Block\Switcher $subject
     * @param \Magento\Store\Block\Switcher $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetStores(\Magento\Store\Block\Switcher $subject, $result)
    {
        $data = [];
        if ($this->helper->getEndableModule()) {
            $storeIds = array_keys($result);
            foreach ($storeIds as $storeId) {
                if ($this->helper->getUrlImageFlag($storeId)) {
                    $data[$storeId] = [ 'width'  => $this->helper->getWidth($storeId),
                                        'height' => $this->helper->getHeight($storeId),
                                        'image'  => $this->helper->getUrlImageFlag($storeId),
                                        'show_label' => $this->helper->getShowStoreviewName($storeId)
                                        ];
                }
            }
        }
        $subject->setData('store_view_flag', $data);
        return $result;
    }
}
