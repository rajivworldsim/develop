<?php
declare(strict_types=1);

namespace Worldsim\Databundle\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Worldsim\Databundle\Model\RateSheetDataBundle;
use Magento\Framework\Controller\Result\JsonFactory;

class Showplan extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    protected $_storeManager;

    protected $resultPageFactory;
    public function __construct(
        Context $context, 
        JsonFactory $resultJsonFactory,
        RateSheetDataBundle $rateSheetDataBundle,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    )
    {
        parent::__construct($context);
        $this->_rateSheetDataBundle = $rateSheetDataBundle;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory; 

    }
    public function execute()
    {
        
        
        $countryName = $this->getRequest()->getParam('bundle_country');
        $regionCode = $this->getRequest()->getParam('region_code');
        $page = $this->getRequest()->getParam('page');
        $supplier = '';
        if($page == 'Data-Bundle-Page'){
            //show WorldSIM bundles only
            $supplier = 'WorldSIM';
        }elseif($page == 'Data-GOSIM-Page'){
            //show GO eSIM bundles only
            $supplier = 'Go';
        }
     
        $plan = '';

        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $rates_currency = $this->_storeManager->getStore()->getCurrentCurrencyRate();
        $currencySymbol = $this->currencyFactory->create()->load($currencyCode);
        $currencySymbol = $currencySymbol->getCurrencySymbol();
      
        if($countryName){

            $collections = $this->_rateSheetDataBundle->getCollection()->addFieldToFilter('country', $countryName);
            if(isset($supplier)){
                $collections = $collections->addFieldToFilter('supplier', $supplier);    
            }
            foreach($collections as $collection){
                if($planData = $collection->getData()){
                    if($planData['onegb'] != '' && $planData['onegb'] != 'N/A' && $planData['onegb'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["onegbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["onegb"], '$')) * $rates_currency, 2).'" data-planName = "- 1 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>1 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["onegb"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';
                    }
                  
                    if($planData['threegb'] != '' && $planData['threegb'] != 'N/A' && $planData['threegb'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["threegbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["threegb"], '$')) * $rates_currency, 2).'" data-planName = "- 3 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>3 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["threegb"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';  
                    }
                   
                    if($planData['fivegb'] != '' && $planData['fivegb'] != 'N/A' && $planData['fivegb'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["fivegbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["fivegb"], '$')) * $rates_currency, 2).'" data-planName = "- 5 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>5 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["fivegb"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';
                    }

                    if($planData['sixgb'] != '' && $planData['sixgb'] != 'N/A' && $planData['sixgb'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["sixgbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["sixgb"], '$')) * $rates_currency, 2).'" data-planName = "- 6 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>6 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["sixgb"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';
                    }
                  
                    if($planData['tengb'] != '' && $planData['tengb'] != 'N/A' && $planData['tengb'] != NULL ){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["tengbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["tengb"], '$')) * $rates_currency, 2).'" data-planName = "- 10 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>10 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["tengb"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';
                    }
                   
                    if($planData['twenty'] != '' && $planData['twenty'] != 'N/A' && $planData['twenty'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["twentygbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["twenty"], '$')) * $rates_currency, 2).'" data-planName = "- 20 GB up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>20 GB</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["twenty"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';
                    }

                    if($planData['unlimited'] != '' && $planData['unlimited'] != 'N/A' && $planData['unlimited'] != NULL){
                        $plan .=    '<div class="col-lg-3 col-md-6 col-6 plan-box">
                                    <div class="validList text-center">
                                        <label>
                                            <input type="radio" name="text" data-sku="'.$planData["unlimitedgbcode"].'"data-shortcode="'.$planData["worldsimshortCode"].'"data-supplier="'.$planData["supplier"].'" data-bundlevalidity="'.$planData["days"].'" data-simtype="'.$planData["simtype"].'" data-planid="'.$planData["id"].'" data-bundlerate="'.$currencySymbol . round(floatval(ltrim($planData["unlimited"], '$')) * $rates_currency, 2).'" data-planName = "- Unlimited up to-'.$planData["days"].'days.">
                                            <div class="selectdData">
                                                <h3>Unlimited</h3>
                                                <p>Validity: '.$planData["days"].' days</p>
                                                <div class="dollerText">'.$currencySymbol . round(floatval(ltrim($planData["unlimited"], '$')) * $rates_currency, 2).'</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>';               
                    }
                }
            }
            
        }

        $result = $this->resultJsonFactory->create();
        $result->setData(['success' => true, 'plans' => $plan]);
        return $result;
    }
}