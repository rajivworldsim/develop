<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Agtech\DisablePaymentMethod\Observer;

class PaymentMethodDisable implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * 
     */
	 protected $storeManager;
	 public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager){
		 $this->_storeManager =  $storeManager;
	 }
	 
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
	if($this->_storeManager->getStore()->getCurrentCurrencyCode() != "ZAR"){
        if($observer->getEvent()->getMethodInstance()->getCode()=="payfast"){
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', false); 
        }
    }
	if($this->_storeManager->getStore()->getCurrentCurrencyCode() == "ZAR"){
        if($observer->getEvent()->getMethodInstance()->getCode()!="payfast"){
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', false); 
        }
    }
	}
}

