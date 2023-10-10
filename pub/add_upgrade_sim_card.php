<?php
/**
 * add to cart from url, useful for email promos
 * based on http://www.magentocommerce.com/wiki/4_-_themes_and_template_customization/catalog/adding_a_product_to_the_cart_via_querystring
 * which no longer works because of session validation of form_key
 * usage: link to add.php?product=[id]&qty=[qty]
 */

use Magento\Framework\App\Bootstrap;
require '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$cart = $objectManager->create('Magento\Checkout\Model\Cart');  
$formKey = $objectManager->create('\Magento\Framework\Data\Form\FormKey')->getFormKey();
$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
$home_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
// load product and get url

// init params
$productId = isset($_POST['product']) ? (int) $_POST['product'] : null;
$qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 1;

$MobOptionId = isset($_POST['MobOptionId']) ? (int) $_POST['MobOptionId'] : null;
$MobOptionValue = isset($_POST['MobOptionValue']) ? (int) $_POST['MobOptionValue'] : null;

$optionId2 = isset($_POST['CreditOptionId']) ? (int) $_POST['CreditOptionId'] : null;
$optionValue2 = isset($_POST['CreditOptionValue']) ? (int) $_POST['CreditOptionValue'] : null;
$current_currency_code = $_POST['current_currency_code'];


// if invalid params, send to homepage

$cart = $objectManager->create('Magento\Checkout\Model\Cart');  
$formKey = $objectManager->create('\Magento\Framework\Data\Form\FormKey')->getFormKey();

// load product and get url

$product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
$attVal = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);


foreach($attVal as $optionKey => $optionVal):

    $optionType = $optionVal->getType();
    $optionId = $optionVal->getId();
    $optionLabel = $optionVal->getTitle();
    if (strpos(strtolower($optionLabel), 'credit') !== false) { //credit
        $i = 1;
        foreach($optionVal->getValues() as $valuesKey => $valuesVal){ 
            //TopUp Promotion Code Starts
            $textValue = $valuesVal->getTitle();
            $credit_sku = $valuesVal->getSku();
            //only show Store Currency Credit Options NOT Others
                        if($i==1){
                            $upgradesim_first_creditid = $valuesVal->getId();
                        }
                        if($textValue==$optionValue2){
                            $upgradesim_creditid = $valuesVal->getId();
                            break;
                        }
                        $i++; 
           
         } //endforeach for option value credit 
     }//if Credit   
endforeach; 

if(!$optionValue2){//if no credit selected then make first credit option
    $upgradesim_creditid = $upgradesim_first_creditid;
}



if($optionId2 && $upgradesim_creditid){//if two options
 $option = array($optionId2=>$upgradesim_creditid,$MobOptionId => $MobOptionValue);
$params = array(
                    'form_key' => $formKey,
                    'product' => $productId, //product Id
                    'qty'   =>1, //quantity of product                
                    'options' => $option
                    );
$cart->addProduct($product, $params);
$cart->save();
echo $home_url.'checkout/cart/';
  exit;
} else {
    header('Location: '.$home_url);
    exit;
}
//echo $url;

//header('Location: ' . $url);
//exit;