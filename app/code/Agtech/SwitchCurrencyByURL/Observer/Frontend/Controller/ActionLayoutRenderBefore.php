<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Agtech\SwitchCurrencyByURL\Observer\Frontend\Controller;

class ActionLayoutRenderBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
     
    protected $_storeManager;
    protected $_currency;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,        
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;        
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
        // Get the list of available currencies
		$availableCurrencyCodes = array_values($this->getAvailableCurrencyCodes(true));
        if(isset($_GET["currency"])){
            // Check if a currency parameter is available and if yes, if the selected currency is allowed
            if (($currencyCode = (string) strtoupper($_GET["currency"])) && in_array($currencyCode, $availableCurrencyCodes)) {

                //Switch the currency
                $this->_storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
            }
		}
    }
     
    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        return $this->_storeManager->getStore()->getAvailableCurrencyCodes($skipBaseNotAllowed);
    }
}

