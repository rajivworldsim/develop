<?php
namespace Agtech\Checkauto\Plugin\Magento\Shipping\Model;
 
class ShowShipping
{
    protected $product;
 
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $product
    ) {
        $this->product = $product; 
    }
 
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        if ($carrierCode == 'matrixrate' && $request->getFreeMethodWeight() == 0) {
            $request->setFreeMethodWeight(1);
            $request->setPackageWeight(1);
            $request->setFreeShipping(0);
            $request->setPackageQty(0);
        }
 
        $result = $proceed($carrierCode, $request);
        return $result;
    }
}