<?php

namespace Magenest\SagePay\ViewModel\Checkout;

class Overview implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    private $taxHelperData;
    private $checkoutHelperData;

    /**
     * Overview constructor.
     * @param \Magento\Tax\Helper\Data $taxHelperData
     * @param \Magento\Checkout\Helper\Data $checkoutHelperData
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelperData,
        \Magento\Checkout\Helper\Data $checkoutHelperData
    ) {
        $this->taxHelperData = $taxHelperData;
        $this->checkoutHelperData = $checkoutHelperData;
    }

    /**
     * @return bool
     */
    public function displayCartBothPrices()
    {
        return $this->taxHelperData->displayCartBothPrices();
    }

    /**
     * @return bool
     */
    public function displayShippingBothPrices()
    {
        return $this->taxHelperData->displayShippingBothPrices();
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->checkoutHelperData->formatPrice($price);
    }
}
