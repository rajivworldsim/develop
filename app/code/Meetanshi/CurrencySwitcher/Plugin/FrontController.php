<?php

namespace Meetanshi\CurrencySwitcher\Plugin;

use Magento\Store\Model\StoreManager;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;

class FrontController
{
    protected $storeManager;

    public function __construct(
        StoreManager    $storeManager
    )  {
        $this->storeManager  =   $storeManager;
    }
       
    public function aroundDispatch(
        FrontControllerInterface $subject,
        \Closure $proceed,
         RequestInterface $request
    ) {

            //$baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $curCurrencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $this->checkCurIp($curCurrencyCode);
            return $proceed($request);
            
    }
    
    public function setDefaultIPCurrency($currencyCode){
        if ($currencyCode) {
            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
        }
    }
    
    public function checkCurIp($curCurrencyCode){
            $cookie_store_value="";
            $visitor_country = "GB";
            if(isset($_SERVER["HTTP_CF_IPCOUNTRY"]) && $_SERVER["HTTP_CF_IPCOUNTRY"]!=''){
                $visitor_country = $_SERVER["HTTP_CF_IPCOUNTRY"];
            }
            if (!isset($_COOKIE['WsCountryStoreIP_V4']) && $visitor_country!=''){
            
                $europe_country = 0;
                if($visitor_country=="AT" || $visitor_country=="BE" || $visitor_country=="CY" || $visitor_country=="EE" || $visitor_country=="FI" || $visitor_country=="FR" || $visitor_country=="DE" || $visitor_country=="GR" || $visitor_country=="IE" || $visitor_country=="IT" || $visitor_country=="LV" || $visitor_country=="LT" || $visitor_country=="LU" || $visitor_country=="MT" || $visitor_country=="MC" || $visitor_country=="NL" || $visitor_country=="PT" || $visitor_country=="SM" || $visitor_country=="SK" || $visitor_country=="SI" || $visitor_country=="ES" || $visitor_country=="VA"){
                    $europe_country = 1;
                }
                
                if($visitor_country=="IN"){
                    $CusSelectedStore="INR";
                }else if($visitor_country=="AU"){
                    $CusSelectedStore="AUD";
                } else if($visitor_country=="ZA"){
                    $CusSelectedStore="ZAR";
                } else if($visitor_country=="GB"){
                    $CusSelectedStore="GBP";
                } else if($visitor_country=="US"){
                    $CusSelectedStore="USD";
                } else if($europe_country==1){
                    $CusSelectedStore="EUR";
                } else {
                    $CusSelectedStore="GBP";
                }
                
                $cookie_store_value=$CusSelectedStore;
                
                setcookie('WsCountryStoreIP_V4', $cookie_store_value,  time()+21600); // 1 day => 86400 x 30 => 2592000
                
                $current_path = $_SERVER['REQUEST_URI'];
                if($current_path=="/"){
                    $current_path = "";
                }           
                
                //Redirect Vistor to Respective Site:
                if($visitor_country=="IN" && $curCurrencyCode!="INR" && $CusSelectedStore=="INR"){
                    $this->setDefaultIPCurrency('INR');
                } else if($visitor_country=="AU" && $curCurrencyCode!="AUD" && $CusSelectedStore=="AUD"){
                    $this->setDefaultIPCurrency('AUD');
                } else if($visitor_country=="ZA" && $curCurrencyCode!="ZAR"  && $CusSelectedStore=="ZAR"){
                    $this->setDefaultIPCurrency('ZAR');
                } else if($visitor_country=="GB" && $curCurrencyCode!="GBP"  && $CusSelectedStore=="GBP"){
                    $this->setDefaultIPCurrency('GBP');
                } else if($visitor_country=="US" && $curCurrencyCode!="USD"  && $CusSelectedStore=="USD"){
                    $this->setDefaultIPCurrency('USD');
                } else if($europe_country==1 && $curCurrencyCode!="EUR"  && $CusSelectedStore=="EUR"){
                    $this->setDefaultIPCurrency('EUR');
                } else if($curCurrencyCode!="GBP" && $visitor_country!="AU" && $visitor_country!="ZA" && $visitor_country!="GB" && $visitor_country!="IN" && $visitor_country!="US" && $europe_country!=1  && $CusSelectedStore=="GBP"){
                    $this->setDefaultIPCurrency('GBP');
                }
            }    else {
                $cookie_store_value="old saved: ".$_COOKIE['WsCountryStoreIP_V4'];
            }
    }
}