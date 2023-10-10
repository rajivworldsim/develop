<?php
/**
 * Copyright © Etailors All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Agtech\OrderAPI\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle\CollectionFactory as RateSheetCollectionFactory;
use Worldsim\Databundle\Model\ResourceModel\GoAPIResponse\CollectionFactory as GoApiResponseCollectionFactory;

class Xmlview implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    private $rateSheetCollectionFactory;
    
    private $goApiResponseCollectionFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\RequestInterface $request,
    PageFactory $resultPageFactory,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Directory\Model\Currency $currency,
    \Magento\Sales\Api\OrderRepositoryInterface $order,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Directory\Model\Currency $currencyModel,
    \Magento\Framework\Webapi\Soap\ClientFactory $soapClientFactory,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    RateSheetCollectionFactory $rateSheetCollectionFactory,
    GoApiResponseCollectionFactory $goApiResponseCollectionFactory,
    \StripeIntegration\Payments\Block\PaymentInfo\Element $paymentsConfig,
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;
        $this->_order = $order;        
        $this->logger = $logger;
        $this->currenciesModel = $currencyModel;
        $this->soapClientFactory = $soapClientFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->rateSheetCollectionFactory = $rateSheetCollectionFactory;
        $this->goApiResponseCollectionFactory = $goApiResponseCollectionFactory;
        $this->paymentsConfig = $paymentsConfig;
        
    }
    
    public function getPaymentIntent($stripeTransactionId){
        if(strpos($stripeTransactionId,"pi_")!==false){
            $stripeTransactionId = str_replace('"','',$stripeTransactionId);
            try{
                return $this->paymentsConfig->getPaymentIntentByTransactionId($stripeTransactionId);
            }catch (\Exception $e){
                return null;
            }
        }
        return null;
    }
    
    public function getPaymentIntentStatus($paymentIntent)
    {
        if (empty($paymentIntent->status))
            return null;

        switch ($paymentIntent->status)
        {
            case "requires_payment_method":
            case "requires_confirmation":
            case "requires_action":
            case "processing":
                return "pending";
            case "requires_capture":
                return "uncaptured";
            case "canceled":
                if (!empty($paymentIntent->charges->data[0]->failure_code))
                    return "failed";
                else
                    return "canceled";
            case "succeeded":
                if ($paymentIntent->charges->data[0]->refunded)
                    return "refunded";
                else if ($paymentIntent->charges->data[0]->amount_refunded > 0)
                    return "partial_refund";
                else
                    return "succeeded";
            default:
                return $paymentIntent->status;
        }
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {   
        $username = 'npcxuuhacw';
        $password = 'K9FjwNc7UE';
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Authorization: Basic " . base64_encode("$username:$password")
            )
        ));
        $orderid = $this->request->getParam('id');
        
        $crm_orderNumb='';
        $long = array('Afghanistan' , 'Åland Islands' , 'Albania' , 'Algeria' , 'American Samoa' , 'Andorra' , 'Angola' , 'Anguilla' , 'Antarctica' , 'Antigua and Barbuda' , 'Argentina' , 'Armenia' , 'Aruba' , 'Australia' , 'Austria' , 'Azerbaijan' , 'Bahamas' , 'Bahrain' , 'Bangladesh' , 'Barbados' , 'Belarus' , 'Belgium' , 'Belize' , 'Benin' , 'Bermuda' , 'Bhutan' , 'Bolivia - Plurinational State of' , 'Bonaire - Sint Eustatius and Saba' , 'Bosnia and Herzegovina' , 'Botswana' , 'Bouvet Island' , 'Brazil' , 'British Indian Ocean Territory' , 'Brunei Darussalam' , 'Bulgaria' , 'Burkina Faso' , 'Burundi' , 'Cambodia' , 'Cameroon' , 'Canada' , 'Cape Verde' , 'Cayman Islands' , 'Central African Republic' , 'Chad' , 'Chile' , 'China' , 'Christmas Island' , 'Cocos (Keeling) Islands' , 'Colombia' , 'Comoros' , 'Congo' , 'Congo - the Democratic Republic of the' , 'Cook Islands' , 'Costa Rica' , 'Côte d\'Ivoire' , 'Croatia' , 'Cuba' , 'Curaçao' , 'Cyprus' , 'Czech Republic' , 'Denmark' , 'Djibouti' , 'Dominica' , 'Dominican Republic' , 'Ecuador' , 'Egypt' , 'El Salvador' , 'Equatorial Guinea' , 'Eritrea' , 'Estonia' , 'Ethiopia' , 'Falkland Islands (Malvinas)' , 'Faroe Islands' , 'Fiji' , 'Finland' , 'France' , 'French Guiana' , 'French Polynesia' , 'French Southern Territories' , 'Gabon' , 'Gambia' , 'Georgia' , 'Germany' , 'Ghana' , 'Gibraltar' , 'Greece' , 'Greenland' , 'Grenada' , 'Guadeloupe' , 'Guam' , 'Guatemala' , 'Guernsey' , 'Guinea' , 'Guinea-Bissau' , 'Guyana' , 'Haiti' , 'Heard Island and McDonald Islands' , 'Holy See (Vatican City State)' , 'Honduras' , 'Hong Kong' , 'Hungary' , 'Iceland' , 'India' , 'Indonesia' , 'Iran - Islamic Republic of' , 'Iraq' , 'Ireland' , 'Isle of Man' , 'Israel' , 'Italy' , 'Jamaica' , 'Japan' , 'Jersey' , 'Jordan' , 'Kazakhstan' , 'Kenya' , 'Kiribati' , 'Korea - Democratic People\'s Republic of' , 'Korea - Republic of' , 'Kuwait' , 'Kyrgyzstan' , 'Lao People\'s Democratic Republic' , 'Latvia' , 'Lebanon' , 'Lesotho' , 'Liberia' , 'Libya' , 'Liechtenstein' , 'Lithuania' , 'Luxembourg' , 'Macao' , 'Macedonia - the former Yugoslav Republic of' , 'Madagascar' , 'Malawi' , 'Malaysia' , 'Maldives' , 'Mali' , 'Malta' , 'Marshall Islands' , 'Martinique' , 'Mauritania' , 'Mauritius' , 'Mayotte' , 'Mexico' , 'Micronesia - Federated States of' , 'Moldova - Republic of' , 'Monaco' , 'Mongolia' , 'Montenegro' , 'Montserrat' , 'Morocco' , 'Mozambique' , 'Myanmar' , 'Namibia' , 'Nauru' , 'Nepal' , 'Netherlands' , 'New Caledonia' , 'New Zealand' , 'Nicaragua' , 'Niger' , 'Nigeria' , 'Niue' , 'Norfolk Island' , 'Northern Mariana Islands' , 'Norway' , 'Oman' , 'Pakistan' , 'Palau' , 'Palestinian Territory - Occupied' , 'Panama' , 'Papua New Guinea' , 'Paraguay' , 'Peru' , 'Philippines' , 'Pitcairn' , 'Poland' , 'Portugal' , 'Puerto Rico' , 'Qatar' , 'Réunion' , 'Romania' , 'Russian Federation' , 'Rwanda' , 'Saint Barthélemy' , 'Saint Helena - Ascension and Tristan da Cunha' , 'Saint Kitts and Nevis' , 'Saint Lucia' , 'Saint Martin (French part)' , 'Saint Pierre and Miquelon' , 'Saint Vincent and the Grenadines' , 'Samoa' , 'San Marino' , 'Sao Tome and Principe' , 'Saudi Arabia' , 'Senegal' , 'Serbia' , 'Seychelles' , 'Sierra Leone' , 'Singapore' , 'Sint Maarten (Dutch part)' , 'Slovakia' , 'Slovenia' , 'Solomon Islands' , 'Somalia' , 'South Africa' , 'South Georgia and the South Sandwich Islands' , 'South Sudan' , 'Spain' , 'Sri Lanka' , 'Sudan' , 'Suriname' , 'Svalbard and Jan Mayen' , 'Swaziland' , 'Sweden' , 'Switzerland' , 'Syrian Arab Republic' , 'Taiwan - Province of China' , 'Tajikistan' , 'Tanzania - United Republic of' , 'Thailand' , 'Timor-Leste' , 'Togo' , 'Tokelau' , 'Tonga' , 'Trinidad and Tobago' , 'Tunisia' , 'Turkey' , 'Turkmenistan' , 'Turks and Caicos Islands' , 'Tuvalu' , 'Uganda' , 'Ukraine' , 'United Arab Emirates' , 'United Kingdom' , 'United States' , 'United States Minor Outlying Islands' , 'Uruguay' , 'Uzbekistan' , 'Vanuatu' , 'Venezuela - Bolivarian Republic of' , 'Viet Nam' , 'Virgin Islands - British' , 'Virgin Islands - U.S.' , 'Wallis and Futuna' , 'Western Sahara' , 'Yemen' , 'Zambia' , 'Zimbabwe');
        $short = array('AF','AX','AL','DZ','AS','AD','AO','AI','AQ','AG','AR','AM','AW','AU','AT','AZ','BS','BH','BD','BB','BY','BE','BZ','BJ','BM','BT','BO','BQ','BA','BW','BV','BR','IO','BN','BG','BF','BI','KH','CM','CA','CV','KY','CF','TD','CL','CN','CX','CC','CO','KM','CG','CD','CK','CR','CI','HR','CU','CW','CY','CZ','DK','DJ','DM','DO','EC','EG','SV','GQ','ER','EE','ET','FK','FO','FJ','FI','FR','GF','PF','TF','GA','GM','GE','DE','GH','GI','GR','GL','GD','GP','GU','GT','GG','GN','GW','GY','HT','HM','VA','HN','HK','HU','IS','IN','ID','IR','IQ','IE','IM','IL','IT','JM','JP','JE','JO','KZ','KE','KI','KP','KR','KW','KG','LA','LV','LB','LS','LR','LY','LI','LT','LU','MO','MK','MG','MW','MY','MV','ML','MT','MH','MQ','MR','MU','YT','MX','FM','MD','MC','MN','ME','MS','MA','MZ','MM','NA','NR','NP','NL','NC','NZ','NI','NE','NG','NU','NF','MP','NO','OM','PK','PW','PS','PA','PG','PY','PE','PH','PN','PL','PT','PR','QA','RE','RO','RU','RW','BL','SH','KN','LC','MF','PM','VC','WS','SM','ST','SA','SN','RS','SC','SL','SG','SX','SK','SI','SB','SO','ZA','GS','SS','ES','LK','SD','SR','SJ','SZ','SE','CH','SY','TW','TJ','TZ','TH','TL','TG','TK','TO','TT','TN','TR','TM','TC','TV','UG','UA','AE','GB','US','UM','UY','UZ','VU','VE','VN','VG','VI','WF','EH','YE','ZM','ZW');
        
        
        $order = $this->_order->get($orderid);
        
        $lastOrderId = 'M2-'.$order->getIncrementId();
        
        $_items = $order->getItemsCollection();
        $_orderData = $order->getData();
        
        //$additionalInformation = $order->getPayment()->getAdditionalInformation();
        /* $this->logger->info(json_encode($additionalInformation));
        //print_r($_orderData);
        exit;  */
        
        $paymentMethod = $order->getPayment()->getMethod();
        
        //Default Variables
        $Protx="";
        $SecureStatus="";
        $ThreeDSecureStatus="";
        $OrigSecureStatus="";
        $CV2Result="";
        $AddressResult=""; 
        $PostCodeResult=""; 
        $TxAuthNo=""; 
        $VPSTxId=""; 
        $VendorTxnCode="";
        $Referrer=""; 
        $ProtxDetails="";
        $Result="";
        $CCLast4Digits="";
        $CAVV=""; 
        $MSISDN1="";
        $CustomerID=""; 
        $PromotionalCode="";
        $VAT=""; 
        $SecurityKey="";  
        $VoucherCode="";
        $OrderPlacedIp="";
        
        $shippingAddress_name=" ";
        $shippingAddress_region=" ";
        $shippingAddress_postcode=" ";
        $shippingAddress_street1=" ";
        $shippingAddress_street2=" ";
        $shippingAddress_country=" ";
        $shippingAddress_countryId=" ";
        $shippingAddress_city=" ";
        $shippingAddress_telephone=" ";
        

        //Payment method Info
        if(strpos($paymentMethod,"sagepay")!==false){
            
            $additionalInformation = $order->getPayment()->getAdditionalInformation();
            /* if(isset($_orderData['sagepay_info'])){
            $sagePayInfo = $_orderData['sagepay_info'];
            }else{
            $sagePayInfo = '';
            } */
            $sagePayInfo = $additionalInformation['transaction_details'];
            $sagePayInfo = unserialize($sagePayInfo);
            
            if($sagePayInfo){
            $Protx = $sagePayInfo['AVSCV2'];
            $SecureStatus = $sagePayInfo['StatusDetail'];
            $ThreeDSecureStatus = $sagePayInfo['3DSecureStatus'];
            $OrigSecureStatus = $sagePayInfo['Status']; 
            $CV2Result = $sagePayInfo['CV2Result'];
            $AddressResult = $sagePayInfo['AddressResult'];
            $PostCodeResult = $sagePayInfo['PostCodeResult'];
            $TxAuthNo = $sagePayInfo['TxAuthNo'];
            $VPSTxId = $sagePayInfo['VPSTxId'];
            $VPSTxId = str_replace('{','',$VPSTxId);
            $VPSTxId = str_replace('}','',$VPSTxId);
            $VendorTxnCode = $sagePayInfo['VendorTxCode'];
            $Referrer = '';//$sagePayInfo['vendorname'];
            $ProtxDetails = $sagePayInfo['StatusDetail'];
            $Result = '';
            $CCLast4Digits = $sagePayInfo['Last4Digits'];
            $CAVV = $sagePayInfo['CAVV'];
            $MSISDN = '';
            $CustomerID = '';
            $PromotionalCode = $_orderData['coupon_code'];
            $VAT =   '';
            $SecurityKey = '';//$sagePayInfo['security_key'];
            }
            $VoucherCode =  '';
            $OrderPlacedIp = $_orderData['remote_ip'];
            
        } else if(strpos($paymentMethod,"paypal")!==false){ //PayPal
            $Protx="PayPal";
            $VPSTxId = $order->getPayment()->getLastTransId();
            if($order->getPayment()->getAdditionalInformation()){
                $SecureStatus_paypal = $order->getPayment()->getAdditionalInformation();
                $SecureStatus = $SecureStatus_paypal['paypal_payment_status'];
                $ProtxDetails = $SecureStatus_paypal['paypal_payer_email'];
            }
        } else if(strpos($paymentMethod,"payfast")!==false){ //PayFast
            $Protx="PayFast";
            if($order->getPayment()->getAdditionalInformation()){
                    $VPSTxId_payfast = $order->getPayment()->getAdditionalInformation();
                    $VPSTxId = $VPSTxId_payfast['pf_payment_id'];	
            }
        } else if(strpos($paymentMethod,"stripe_payments_express")!==false){ //stripe_payments_express
            $Protx="stripe_payments";
            if($order->getPayment()->getAdditionalInformation()){
                    $VPSTxId_stripe = $order->getPayment()->getAdditionalInformation();
                    //echo $order->getPayment()->getTransactionId();
                    //echo '<br/>stripe_express <pre>'; print_r($VPSTxId_stripe); exit;
                    $VPSTxId = $VPSTxId_stripe['token'];	
            }
        } else if(strpos($paymentMethod,"stripe_payments")!==false){ //stripe_payments
            $Protx="stripe_payments";
            if($order->getPayment()->getAdditionalInformation()){
                    $VPSTxId_stripe = $order->getPayment()->getAdditionalInformation();
                    $transactionId = '';
                    if(isset($VPSTxId_stripe['client_side_confirmation'])){
                        $transactionId = $VPSTxId_stripe['client_side_confirmation'];   
                    }
                    $orderComment = [];
                    foreach ($order->getStatusHistoryCollection() as $status) {
                        if ($status->getComment()) {
                            if (($pos = strpos($status->getComment(), "Transaction ID: ")) !== FALSE) { 
                                $transactionId = substr($status->getComment(), $pos+16);
                                break;
                            }
                        }
                    }
                    $VPSTxId = $transactionId;
            }
        } else {
            $Protx=$paymentMethod;
        }
        
        //if stripe payment then check transaction status
        if(strpos($VPSTxId,"pi_")!==false && strpos($paymentMethod,"stripe_payments")!==false){
            $stripePaymentJson = $this->getPaymentIntent($VPSTxId);
            if($stripePaymentJson){
                $paymentStatus = $this->getPaymentIntentStatus($stripePaymentJson);
                if($paymentStatus != 'succeeded'){
                    $SecureStatus = "pending";//order is not paid.
                }
            }
        }
        
        //Customer Data
        $CustomerFirstName = $_orderData['customer_firstname'];
        $CustomerLastName = $_orderData['customer_lastname'];
        $CustomerFirstName = ($CustomerFirstName!='')? str_replace("&","and",$CustomerFirstName) : '';
        $CustomerLastName = ($CustomerLastName!='')? str_replace("&","and",$CustomerLastName) : '';
        $_orderCustomerName = ($CustomerFirstName!='')? $CustomerFirstName.' '.$CustomerLastName : '';
        $_orderCustomerName = ($_orderCustomerName!='')?$this->replaceAccents($_orderCustomerName) : '';
        //$_orderCustomerName =  iconv(mb_detect_encoding($_orderCustomerName, mb_detect_order(), true), "UTF-8", $_orderCustomerName);
        $crmCusEmail = $_orderData['customer_email'];
        $customerPassword = ($_orderData['password']) ? $_orderData['password'] : '';
        //echo 'pwd = '.$customerPassword;exit;
        $crmCusPwd = "";
        if($crmCusPwd==""){
            if(isset($_orderData['tm_field1'])){
                $crmCusPwd   = $_orderData['tm_field1'];	
            }
        }
        $_order_Date = $_orderData['created_at'];
        $SimQty = 0;
        $BundleTopUp_Link = 0;
        $ProductQty = $_orderData['total_qty_ordered'];
        $_orderStoreCode = $_orderData['store_id'];
        $_orderCurrencyCode = $_orderData['order_currency_code'];
        
        //Store Decide
        switch ($_orderCurrencyCode){
                case "GBP":
                    $_orderStoreName = "100000003";//UK
                    break;
                case "USD":
                    $_orderStoreName = "100000004";//US
                    break;
                case "EUR":
                    $_orderStoreName = "100000005";//EU
                    break;
                case "AUS":
                    $_orderStoreName = "100000007";//AU
                    break;
                case "ZAR":
                    $_orderStoreName = "100000006";//SA
                    break;
                
                case "INR":
                    $_orderStoreName = "100000001";//Indian Store
                    break;
                default:
                    $_orderStoreName = "100000003";//By Default UK
                    break;
        }
        
        
        $_orderCurrencyCode = $_orderData['order_currency_code'];
        //$_orderCurrencySymbol = Mage::app()->getLocale()->currency($_orderCurrencyCode)->getSymbol();
        //$currencyModel = Mage::getModel('directory/currency');
        $currencies = $this->currenciesModel->getConfigAllowCurrencies();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $defaultCurrencies = $this->currenciesModel->getConfigBaseCurrencies();
        
        $_orderCouponCode = $_orderData['coupon_code'];

        $convRate=1; 
        $rates = $this->currenciesModel->getCurrencyRates($defaultCurrencies, $currencies);
        
        foreach($rates[$baseCurrencyCode] as $key=>$value  ) {
              if($_orderCurrencyCode==$key){
                
                $convRate = $value;//convert to GBP
              
              }
        }
        
        //Shipping Informations
        $_taxvat=0;
        $_shippingId = $_orderData['shipping_method'];
        $_shippingDescription = $_orderData['shipping_description'];
        $_shippingAmount = round((float)$_orderData['shipping_amount'],2);
        $_shippingAmount_conv = $_shippingAmount/$convRate;
        
        if($_shippingId == ''){
              $_shippingId = '121212';
              $_shippingDescription = 'Digital Download';
              $_shippingAmount = 0.00;
              $_shippingAmount_conv = 0.00;
        }
        $_subTotal = round((float)$_orderData['subtotal'],2);
        $_subTotal_conv = $_subTotal/$convRate;
        $_taxAmount = round((float)$_orderData['tax_amount'],2);
        $_taxAmount_conv = $_taxAmount/$convRate;
        $_discountAmount = (string)round((float)$_orderData['discount_amount'],2);
        $_discountAmount = str_replace("-","",$_discountAmount);
        $_discountAmount_conv = $_discountAmount/$convRate;
        $_toalAmount = round((float)$_orderData['total_invoiced'],2);
        $_base_grand_total = round((float)$_orderData['base_grand_total'],2);
        $_toalAmount_conv = $_base_grand_total;
        $_totalDue = $_orderData['base_total_due'];
        $_totalItems = $_orderData['total_item_count'];
        
        if($_totalDue>0){
            if($Protx == "PayPal" && $SecureStatus == "pending"){ 
            //if Payment Method is PayPal and status is pending still push into CRM by selecting payment is not pending
            $paymentPending = 0;
            } else { 
            // else obviously pending payment is yes
            $paymentPending = 1;
            }
        } else {
            $paymentPending = 0;
        }
        //if Payment Method is paypal and there is no transaction ID then do not push order into CRM:
        if($Protx=="PayPal" && $VPSTxId == ""){
            $paymentPending = 1;
        }
        
        //if($paymentPending){
        //    $SecureStatus = "pending";
        //}
        
        //Shipping Address Information
        if($order->getShippingAddress()){
            $shippingAddress = $order->getShippingAddress()->getData();
            $shippingAddress_firstname = $this->replaceAccents($shippingAddress['firstname']);
            $shippingAddress_lastname = $this->replaceAccents($shippingAddress['lastname']);
            $shippingAddress_firstname = ($shippingAddress_firstname!='') ? str_replace("&","and",$shippingAddress_firstname) : '';
            $shippingAddress_lastname = ($shippingAddress_lastname!='') ? str_replace("&","and",$shippingAddress_lastname) : '';
            $shippingAddress_name = $shippingAddress_firstname.' '.$shippingAddress_lastname;
            $shippingAddress_region = $this->replaceAccents($shippingAddress['region']);
            $shippingAddress_postcode = $shippingAddress['postcode'];
            $shippingAddress_street = $this->replaceAccents($shippingAddress['street']);
            $arr_shippingstreet = explode("\n", $shippingAddress_street);
            $shippingAddress_street1 = " ";
            if(isset($arr_shippingstreet[0])){
            $shippingAddress_street1 = $arr_shippingstreet[0];
            }
            $shippingAddress_street2 = " ";
            if(isset($arr_shippingstreet[1])){
            $shippingAddress_street2 = $arr_shippingstreet[1];
            }
            $shippingAddress_city = $this->replaceAccents($shippingAddress['city']);
            $shippingAddress_telephone = $shippingAddress['telephone'];
            $shippingAddress_countryId = $shippingAddress['country_id'];
            $shippingAddress_country = str_replace($short, $long, $shippingAddress_countryId);
            $shippingAddress_country = $this->replaceAccents($shippingAddress_country);
            if($shippingAddress_countryId=='US'){
                $IsWFIorder="true";
            } else {
                $IsWFIorder="false";
            }
            //$shippingAddress_firstname =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_firstname, mb_detect_order(), true), "UTF-8", $shippingAddress_firstname)));
            //$shippingAddress_lastname  =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_lastname, mb_detect_order(), true), "UTF-8", $shippingAddress_lastname)));
            //$shippingAddress_region    =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_region, mb_detect_order(), true), "UTF-8", $shippingAddress_region)));
            //$shippingAddress_street    =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_street, mb_detect_order(), true), "UTF-8", $shippingAddress_street)));
            //$shippingAddress_street1   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_street1, mb_detect_order(), true), "UTF-8", $shippingAddress_street1)));
            //$shippingAddress_street2   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_street2, mb_detect_order(), true), "UTF-8", $shippingAddress_street2)));
            //$shippingAddress_city      =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_city, mb_detect_order(), true), "UTF-8", $shippingAddress_city)));
            //$shippingAddress_country   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($shippingAddress_country, mb_detect_order(), true), "UTF-8", $shippingAddress_country)));
            
        } else {
            $shippingAddress_firstname = '';
            $shippingAddress_lastname = '';
            $shippingAddress_name = '';
            $shippingAddress_region = '';
            $shippingAddress_postcode = '';
            $shippingAddress_street = '';
            $shippingAddress_city = '';
            $shippingAddress_telephone = '';
            $shippingAddress_countryId = '';
            $shippingAddress_country = '';
            if($shippingAddress_countryId=='US'){
                $IsWFIorder="true";
            } else {
                $IsWFIorder="false";
            }
        }
        
        //Billing Address Information
        $billingAddress = $order->getBillingAddress()->getData();
        $billingAddress_firstname = $this->replaceAccents($billingAddress['firstname']);
        $billingAddress_lastname = $this->replaceAccents($billingAddress['lastname']);
        //$billingAddress_firstname = str_replace("&","and",$billingAddress_firstname);
        //$billingAddress_lastname = str_replace("&","and",$billingAddress_lastname);
        $billingAddress_name = $billingAddress_firstname.' '.$billingAddress_lastname;
        $billingAddress_region = $this->replaceAccents($billingAddress['region']);
        $billingAddress_postcode = $billingAddress['postcode'];
        $billingAddress_street = $this->replaceAccents($billingAddress['street']);
        $arr_billingstreet = explode("\n", $billingAddress_street ?? '');
        $billingAddress_street1 = " ";
        if(isset($arr_billingstreet[0])){
        $billingAddress_street1=$arr_billingstreet[0];
        }
        $billingAddress_street2 = " ";
        if(isset($arr_billingstreet[1])){
        $billingAddress_street2=$arr_billingstreet[0];
        }
        
        
        $billingAddress_city = $this->replaceAccents($billingAddress['city']);
        $billingAddress_telephone = $billingAddress['telephone'];
        $billingAddress_countryId = $billingAddress['country_id'];
        $billingAddress_country = str_replace($short, $long, $billingAddress_countryId);
        $billingAddress_country = $this->replaceAccents($billingAddress_country);
        
        if($_orderCustomerName==' ' || $CustomerFirstName==''){
            $_orderCustomerName = $billingAddress_name;
        }
        if($CustomerFirstName==''){
            $CustomerFirstName = $billingAddress_firstname;
        }
        if($CustomerLastName==''){
            $CustomerLastName = $billingAddress_lastname;
        }
        
        //special check for France to make all orders from france as rejected
		if($_SERVER['HTTP_CF_IPCOUNTRY']=='FR'  || $billingAddress_countryId =='FR' || $shippingAddress_countryId == 'FR'){
			$VPSTxId = '';
		}

        //$billingAddress_firstname =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_firstname, mb_detect_order(), true), "UTF-8", $billingAddress_firstname)));
        //$billingAddress_lastname  =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_lastname, mb_detect_order(), true), "UTF-8", $billingAddress_lastname)));
        //$billingAddress_region    =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_region, mb_detect_order(), true), "UTF-8", $billingAddress_region)));
        //$billingAddress_street    =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_street, mb_detect_order(), true), "UTF-8", $billingAddress_street)));
        //$billingAddress_street1   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_street1, mb_detect_order(), true), "UTF-8", $billingAddress_street1)));
        //$billingAddress_street2   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_street2, mb_detect_order(), true), "UTF-8", $billingAddress_street2)));
        //$billingAddress_city      =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_city, mb_detect_order(), true), "UTF-8", $billingAddress_city)));
        //$billingAddress_country   =  utf8_encode($this->replaceAccents(iconv(mb_detect_encoding($billingAddress_country, mb_detect_order(), true), "UTF-8", $billingAddress_country)));
        
        /* ============= Creating XML for CRM ============= */
        
        $productXML='';
        $item=0;
        $SIM_type="true";
        $OrderName="";
        $isAutoTopUP=0;
        $OrderName="";
        $_ItemDiscount=0.00;
        //if multiple europe bundle has been added within the same order then only add 1 SIM Card with it.
        $europe_sim_bundle_card=0;
        //if multiple USA bundle has been added within the same order then only add 1 SIM Card with it.
        $usa_sim_bundle_card=0;
        $url = 'https://' . $_SERVER['SERVER_NAME'];
        $file_name_with_path = $url ."/media/tariff/databundle_codes.json";
        $db_code =  file_get_contents($file_name_with_path,false, $context);
        $db_code =  json_decode($db_code);
        
        
        
        foreach ($_items as $_item){
            
                $item++;
                $parent_prod_id = $_item->getParentItemId();
                if($parent_prod_id!=''){//means configurable product
                continue;
                }

                $portableProductBundle="false";
                $int_sim="false";
                $uk_sim="false";
                $europe_sim="false";
                $usa_sim="false";
                $isSimProduct="false";

                $_sku_id_product = $_sku_id = $_item->getSku();
                $_orderdItemQty = $_item->getQtyOrdered();
                $_productPrice = $_item->getPrice();
                $_productPrice_conv = round($_productPrice/$convRate);
                $_productAmount = $_productPrice*$_orderdItemQty;
                $_productAmount_conv = round($_productAmount/$convRate);
                $_ItemDiscount = $_item->getBaseDiscountAmount();
                if(strpos($_sku_id,"data-bundle-topup")!==false){
                $_prod_sku = "data-bundle-topup";
                } else if(strpos($_sku_id,"data-sim-bundle")!==false){
                $_prod_sku = "data-sim-bundle";
                } else {
                $_prod_sku = $_sku_id; 
                }
                $_productName = $_item->getName();
                $_productName = str_replace("&","&amp;",$_productName);
                $_productName = str_replace("+","",$_productName);
                
                //$this->logger->info(json_encode($_item->getProductOptions()['options']));
                //$_options = $_item->getItemOptions();
                //$_options = $_item->getProductOptions()['options'];
                
                $product_Size = "";
                $product_Credit = "";
                $product_BonousCredit = "";
                $product_Color = "";
                $product_MobileNumber = "";
                $product_CustomerID = "";
                $product_PipSim = "";
                $product_PasswordPuk = "";
                $product_CcaPromoCode = "";
                $product_AutoTopUp = "";
                $product_AutoTopUpLimit = "";
                $product_AutoTopUpAmount = "";
                $product_Bundle = "";
                $product_BundleLocation = "";
                $product_DataBundleTopUp = "";
                $product_DataBundleRenew = "";
                $product_AutoRenew = "";
                $product_WiFiStartDate = "";
                $product_Quantity = "";
                $product_Price = "";
                $product_Amount = "";
                $product_ProductDiscount = "";
                
                if(is_array($_item->getProductOptions()) && array_key_exists('options',$_item->getProductOptions())){
                    $_options = $_item->getItemOptions();
                    $_options = $_item->getProductOptions()['options'];
                if(isset($_options)){
                    foreach ($_options as $option) {
                        $label = str_replace(' ','',ucwords($option['label']));//Select Size/Color etc
                        $textValue = $option['value'];//Nano/Black etc
                        if(strpos(strtolower($option['label']),"size") !== false){
                            $product_Size = $option['value'];
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"color")!==false){
                            $product_Color = $option['value'];
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"additional usa number")!==false){
                            $product_usanumber = $option['value'];
                            if($product_usanumber=="Added"){
                                $product_Size = "WITH USA MOBILE DID";
                            }
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"if balance reaches")!==false){//if credit reaches
                            //if topup then check last 3 char of sku for credit currency
                            if(strpos($_sku_id,"top-up-")!==false){ 
                                $creditCurrency = substr($_sku_id, -3);
                                $_creditCurrencyCode = strtoupper($creditCurrency);
                            } else {
                                $_creditCurrencyCode = $_orderCurrencyCode;	
                            }
                            
                            foreach($rates[$baseCurrencyCode] as $key=>$value  ) {
                                if($_creditCurrencyCode==$key){
                                  $credit_convRate = $value;//convert to GBP
                                }
                            }
                            
                            if($option['value'] !== '-'){
                                $product_AutoTopUpLimit = round($option['value']/$credit_convRate,2);
                            }
                            
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"credit")!==false){
                            $textValue = $option['value'];
                            
                            ///////////GET FLOAT FROM PRICE////////////
                            $s = str_replace(',', '.', $textValue);
                            // remove everything except numbers and dot "."
                            $s = preg_replace("/[^0-9\.]/", "", $s);
                            // remove all seperators from first part and keep the end
                            $textValue = str_replace('.', '',substr($s, 0, -3)) . substr($s, -3);
                            $org_Credit_value = $textValue;
                            
                            //if topup then check last 3 char of sku for credit currency
                            if(strpos($_sku_id,"top-up-")!==false || strpos($_item->getSku(),"upgraded")!==false){ 
                                $creditCurrency = substr($_sku_id, -3);
                                $_creditCurrencyCode = strtoupper($creditCurrency);
                            } else {
                                $_creditCurrencyCode = $_orderCurrencyCode;	
                            }
                            
                            foreach($rates[$baseCurrencyCode] as $key=>$value  ) {
                                if($_creditCurrencyCode==$key){
                                  $credit_convRate = $value;//convert to GBP
                                }
                            }
                            //$credit_convRate = 1;//if store wise credit mentioned then uncomment above lines to get dynamic conversation rate.		
                            
                            if($_creditCurrencyCode=="ZAR"){
                                if(strpos($_sku_id,"top-up-")!==false){
                                    if($textValue=="250"){
                                        $textValue="9.25";  //15	
                                    }else if($textValue=="500"){
                                        $textValue="18.50"; //29	
                                    }else if($textValue=="750"){
                                        $textValue="27.78"; //44	
                                    }else if($textValue=="1250"){	
                                        $textValue="48.20"; //73
                                    }else if($textValue=="2500"){
                                        $textValue="92.60";  //146
                                    } else {
                                        $textValue = round($textValue/$credit_convRate,2);
                                    }
                                }else{
                                    if($textValue=="250"){
                                        $textValue="15";	
                                    }else if($textValue=="500"){
                                        $textValue="29";	
                                    }else if($textValue=="750"){
                                        $textValue="44";	
                                    }else if($textValue=="1250"){	
                                        $textValue="73";
                                    }else if($textValue=="2500"){
                                        $textValue="146";
                                    }/* else {
                                        $textValue = round($textValue/$credit_convRate,2);
                                    }*/
                                }
                            } else {
                                if(strpos($_sku_id,"top-up-")!==false || strpos($_item->getSku(),"upgraded")!==false){
                                    $textValue = round($textValue/$credit_convRate,2);
                                }
                            }
                            $product_Credit = $textValue;
                            $extraCredit=0;
                            //Extra Credit 09 May 2017 for TopUp on Arif Request
                            if(strpos($_sku_id,"top-up")!==false){
                                if($_creditCurrencyCode=="GBP"){
                                    if($org_Credit_value==30){
                                      $extraCredit=2;
                                    } elseif ($org_Credit_value==50){
                                      $extraCredit=3.75;
                                    } elseif ($org_Credit_value==100){
                                      $extraCredit=10;
                                    } elseif ($org_Credit_value==150){
                                      $extraCredit=20;
                                    }
                                } elseif($_creditCurrencyCode=="USD"){
                                    if($org_Credit_value==30){
                                      $extraCredit=2;
                                    } elseif ($org_Credit_value==50){
                                      $extraCredit=3.75;
                                    } elseif ($org_Credit_value==100){
                                      $extraCredit=10;
                                    } elseif ($org_Credit_value==150){
                                      $extraCredit=20;
                                    }
                                } elseif($_creditCurrencyCode=="EUR"){
                                    if($org_Credit_value==30){
                                      $extraCredit=2;
                                    } elseif ($org_Credit_value==50){
                                      $extraCredit=3.75;
                                    } elseif ($org_Credit_value==100){
                                      $extraCredit=10;
                                    } elseif ($org_Credit_value==150){
                                      $extraCredit=20;
                                    }
                                } elseif($_creditCurrencyCode=="AUD"){
                                    if($org_Credit_value==50){
                                      $extraCredit=3.35;
                                    } elseif ($org_Credit_value==75){
                                      $extraCredit=5.62;
                                    } elseif ($org_Credit_value==100){
                                      $extraCredit=10;
                                    } elseif ($org_Credit_value==200){
                                      $extraCredit=26.60;
                                    }
                                } elseif($_creditCurrencyCode=="ZAR"){
                                    if($org_Credit_value==500){
                                      $extraCredit=33.50;
                                    } elseif ($org_Credit_value==750){
                                      $extraCredit=56.25;
                                    } elseif ($org_Credit_value==1250){
                                      $extraCredit=125.00;
                                    } elseif ($org_Credit_value==2500){
                                      $extraCredit=332.51;
                                    }
                                } elseif($_creditCurrencyCode=="INR"){
                                    if($org_Credit_value==2000){
                                      $extraCredit=134;
                                    } elseif ($org_Credit_value==3000){
                                      $extraCredit=224.97;
                                    } elseif ($org_Credit_value==5000){
                                      $extraCredit=499.97;
                                    } elseif ($org_Credit_value==8500){
                                      $extraCredit=1130.50;
                                    }
                                }
                            }//end of condition if TopUp
                            
                            if(strpos($_sku_id,"UK-SIM-CARD-100")!==false || strpos($_sku_id,"international-simcard-100")!==false || strpos($_sku_id,"data-sim-card-new-100")!==false || strpos($_sku_id,"international-e-simcard-100")!==false){ //one of SIM Cards with 100 GBP Credit
                                $product_BonousCredit = 10;//bonus credit already defined in GBP
                            }
    				
                            if($extraCredit>1 && $product_BonousCredit==""){// then convert back to GBP
                                $product_BonousCredit = round($extraCredit/$credit_convRate,2);//convert $extraCredit back to GBP	
                            }
                            
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"mobile")!==false){
                            $mob_cus_id = $option['print_value'];
                            $mob_cus_id_1st2 = substr($mob_cus_id, 0, 2);
                            if($mob_cus_id_1st2=="07"){
                                    $mob_cus_id = str_replace('07','447',$mob_cus_id);
                            }
                            $mob_cus_id_1st5 = substr($mob_cus_id, 0, 5);
                            if($mob_cus_id_1st5=="00447"){
                                    $mob_cus_id = str_replace('00447','447',$mob_cus_id);
                            }
                            
                            $MSISDN1 = $product_CustomerID = $product_MobileNumber = $mob_cus_id;
                
                            if(strpos($_sku_id,"top-up")!==false || strpos($_sku_id,"data-bundle-topup")!==false){//if Normal TopUp OR DataBundle TopUp
                              $ProductQty=$ProductQty-1;
                              $OrderName="TopUp Order";
                            }
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"pin")!==false){ //Password oR PUK
                            $product_PasswordPuk = $option['value'];
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"auto top up")!==false){
                            $product_AutoTopUp = $option['value'];
                            if($option['value']=='Yes'){
                                $isAutoTopUP=1;
                                $OrderName="AutoTopUp Order";
                            }
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"amount")!==false){//auto top up amount
                            //if topup then check last 3 char of sku for credit currency
                            if(strpos($_sku_id,"top-up-")!==false){ 
                                $creditCurrency = substr($_sku_id, -3);
                                $_creditCurrencyCode = strtoupper($creditCurrency);
                            } else {
                                $_creditCurrencyCode = $_orderCurrencyCode;	
                            }
                            
                            foreach($rates[$baseCurrencyCode] as $key=>$value  ) {
                                if($_creditCurrencyCode==$key){
                                  $credit_convRate = $value;//convert to GBP
                                }
                            }
                            
                            if($textValue !== '-'){
                                $product_AutoTopUpAmount = round($textValue/$credit_convRate,2);    
                            }
                            
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"bundle country")!==false){//bundle country
                            //$product_BundleLocation = $option['value'];
                            continue;
                        }
                        elseif(strpos(strtolower($option['label']),"sim type")){
                            $isSimProduct="true";
                            if(strpos(strtolower($textValue),"international sim")!== false){// if this condition true it means Bundle needs to be purchased with International SIM instead of default Data SIM
                                $int_sim="true";
                            } elseif(strpos(strtolower($textValue),"uk")!== false){// UK SIM Card
                                $uk_sim="true";
                            } elseif(strpos(strtolower($textValue),"europe")!== false){// Europe SIM Card
                                $europe_sim="true";
                            } elseif(strpos(strtolower($textValue),"usa")!== false){// USA/Canada/Mexico SIM Card
                                $usa_sim="true";
                            }
                            continue;
                        }	
                        elseif(strpos(strtolower($option['label']),"associated product")!==false){//bundle country
                            $isSimProduct="true";
                            if($option['value']!=""){//if this condition true it means this Bundle has been purchased via Portable WiFi Proudct
                                $portableProductBundle="tue";	
                            }
                            $product_Color = $option['value'];
                            continue;
                        }
                        
                        elseif(strpos(strtolower($option['label']),"bundle")!==false){//Bundle
                            $plan_code = $option['print_value'];
                            $product_BundleLocation = $plan_code;
                            $plan_details = $this->extractPlanDetails($plan_code);
                            
                            //extract SIM Type from options
                            $bundleSupplier="WorldSIM";	 //set default to WorldSIM
                            foreach ($_options as $option) {
                                if(strpos(strtolower($option['label']),"sim type")!==false){//eSIM Connect or WorldSIM Cards
                                    if(strpos(strtolower($option['value']),"esim connect")!== false){ // if this condition true it means GO SIM Bundle
                                        $bundleSupplier="Go";	
                                    }
                                    break;
                                }
                            }
    
                            // Accessing the extracted values
                            $country = $plan_details['country'];
                            $plan = $plan_details['plan'];
                            $validity = $plan_details['validity'];
                                                            
                            $currentItemData = $this->getRateSheetDetails($country, $plan, $validity, $bundleSupplier);
                            //print_r($currentItemData);
                            //echo '<br/>========================<br/>';
                            if (!empty($currentItemData)) {
                                $bundle_supplier = $currentItemData['supplier'];
                                $bundle_simType = $currentItemData['simType'];
                                $bundle_code = $currentItemData['bundleCode'];
                                //echo 'Bundle Supplier = '.$bundle_supplier.'; SimType = '.$bundle_simType.' Bundle Code = '.$bundle_code;exit;
                                
                            } else {
                                $bundle_supplier = '';
                                $bundle_simType = '';
                                $bundle_code = '';
                            }
                            
                            if(strpos(strtolower($_sku_id),"data-bundle-topup-new")!==false) { //NEW Data Bundle TopUp 2017
                                $product_DataBundleTopUp = "Yes";
                                $BundleTopUp_Link = 1;
                                $sku_start_pos = explode('data-bundle-topup-new-', $_sku_id);
                                //check if number entered is either DataSIM (DA), or RoamFreeSIM (RF) or HomeAwaySIM (HA) or Euro (EU) or EU_SUPER or ESIM or DATASIM
                                //since Mobile number attribute comes after bundle attribute so have to go through every attribute loop to find mobile number fist then will check if its DA,RF or HA, EU, EU_SUPER
                                foreach ($_options as $option_bundle) {
                                    if(strpos(strtolower($option_bundle['label']),"mobile")!==false){
                                        $mobile_number_bundle = $option_bundle['print_value'];
                                    }
                                }
                                if($mobile_number_bundle!="" && $bundle_supplier){
                                    $client_bundle = $this->soapClientFactory->create("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");
                                    $soapResponse = $client_bundle->__getFunctions();  
                                    $item_bundle = new \stdClass();
                                    $item_bundle->MSISDN = $mobile_number_bundle;
                                    $crm_NumbResult = $client_bundle->CheckSimTypeByMsisdn($item_bundle);
                                    $crm_numb_type = $crm_NumbResult->CheckSimTypeByMsisdnResult; 
                                }
                            } elseif(strpos(strtolower($_sku_id),"data-sim-bundle-new")!==false) {
                                $sku_start_pos = explode('data-sim-bundle-new-', $_sku_id);
                                if($bundle_supplier == 'Go'){
                                    $mobile_number_bundle = $this->getGoSimIccid($order->getIncrementId());
                                    $product_CustomerID = $product_MobileNumber = $mobile_number_bundle;
                                    $crm_numb_type = 'GO-SIM'; // sim type is eSIM by default
                                    $esim_type_purchased = 'eSIM Connect';
                                }elseif($bundle_supplier == 'E-Sim2Fly'){
                                    $crm_numb_type = 'E-Sim2Fly'; // sim type is eSIM by default
                                    $esim_type_purchased = 'eSIM Connect';
                                }else{
                                    if($bundle_simType == 'eSIM'){
                                        $crm_numb_type = 'WorldSIM-Data-eSIM';
                                        $esim_type_purchased = 'eSIM Pro';
                                    }else{
                                        $crm_numb_type = "DA";
                                        $esim_type_purchased = 'International SIM Card';
                                    }
                                }
                                echo $bundle_supplier.' - ',$bundle_simType.' - '.$esim_type_purchased.'<br/>';
                            } elseif(strpos(strtolower($_sku_id),"data-esim-bundle-new")!==false) {
                                    $sku_start_pos = explode('data-esim-bundle-new-', $_sku_id);
                                    //if supplier is GO SIM then get ICCID from GO SIM Response API
                                    if($bundle_supplier == 'Go'){
                                        $mobile_number_bundle = $this->getGoSimIccid($order->getIncrementId());
                                        $product_CustomerID = $product_MobileNumber = $mobile_number_bundle;
                                        $crm_numb_type = 'GO-SIM'; // sim type is eSIM by default
                                        $esim_type_purchased = 'eSIM Connect';
                                    }elseif($bundle_supplier == 'E-Sim2Fly'){
                                        $crm_numb_type = 'E-Sim2Fly'; // sim type is eSIM by default
                                        $esim_type_purchased = 'eSIM Connect';
                                    }else{
                                        if($bundle_simType == 'eSIM' || $bundle_simType == 'Multiple SIM'){
                                            $crm_numb_type = 'WorldSIM-Data-eSIM';
                                            $esim_type_purchased = 'eSIM Pro';
                                            $product_CustomerID = $product_MobileNumber = '';
                                        }else{
                                            $crm_numb_type = "DA";
                                            $esim_type_purchased = 'International SIM Card';
                                        }
                                    }
                            } elseif(strpos(strtolower($_sku_id),"europe-sim-bundle")!==false) {
                                $sku_start_pos = explode('europe-sim-bundle-', $_sku_id);
                                $crm_numb_type = "EU_SUPER";
                            } elseif(strpos(strtolower($_sku_id),"usa-sim-bundle")!==false) {
                                $sku_start_pos = explode('usa-sim-bundle-', $_sku_id);
                                $crm_numb_type = "RF";
                            } elseif(strpos(strtolower($_sku_id),"uk-sim-bundle")!==false) {
                                $sku_start_pos = explode('uk-sim-bundle-', $_sku_id);
                                $crm_numb_type = "HA";
                            } elseif(strpos(strtolower($_sku_id),"aus-nz-sim-bundle")!==false) {
                                $sku_start_pos = explode('aus-nz-sim-bundle-', $_sku_id);
                                $crm_numb_type = "RF";
                            } elseif(strpos(strtolower($_sku_id),"sa-sim-bundle")!==false) {
                                $sku_start_pos = explode('sa-sim-bundle-', $_sku_id);
                                $crm_numb_type = "RF";
                            } elseif(strpos($_sku_id,"data-sim-card-new")!==false || strpos($_sku_id,"data-roaming-solutions")!==false){
                                $crm_numb_type = "DA";
                            } elseif(strpos($_sku_id,"international-simcard")!==false){
                                $crm_numb_type = "RF";
                            } elseif(strpos($_sku_id,"UK-SIM-CARD")!==false){
                                $crm_numb_type = "HA";		
                            } elseif(strpos($_sku_id,"-sim-card")!==false){
                                $crm_numb_type = "EU_SUPER";		
                            } else{
                                $crm_numb_type = "RF";//default sim type
                                $esim_type_purchased = 'International SIM Card';
                            }
                            $esim_type_purchased = 'International SIM Card';
                            
                            $sku_bundle_selected = $sku_start_pos[1];
                            //$bundle_location_pos = explode('-', $sku_bundle_selected);
                            //$bundle_location = $bundle_location_pos[0];
                            //$bundle_validity = $bundle_location_pos[1];

                            if($sku_bundle_selected!==false){
                                $product_Bundle = $bundle_code;
                                if($crm_numb_type=="HA" || $crm_numb_type=="DA" || $crm_numb_type=="DATASIM" || $crm_numb_type=="ESIM" || $crm_numb_type=="WorldSIM-Data-eSIM"){
                                    if($crm_numb_type=="HA"){
                                        $product_Bundle = str_replace("RF_","HA_",$bundle_code);
                                        $product_Bundle = str_replace("_USD","_GBP",$product_Bundle);
                                    }elseif($crm_numb_type=="DA" || $crm_numb_type=="DATASIM"){
                                        $product_Bundle = str_replace("RF_","DataSIM_",$bundle_code);
                                    }elseif($crm_numb_type=="ESIM" || $crm_numb_type=="WorldSIM-Data-eSIM"){
                                        $product_Bundle = str_replace("RF_","RF_ESIM_",$bundle_code);
                                    }
                                }
                                $product_Size = $option['value'];
                                //$product_Color = $esim_type_purchased;
                                if($product_DataBundleTopUp != "Yes"){//if not already set to Yes
                                    $product_DataBundleTopUp = "No";
                                }
                            }
                            
                            if($product_BundleLocation==""){
                                $product_BundleLocation = $plan_code;
                            }
                            
                            continue;
                        }//end of "bundle" option check
                        
                    }//end of options For Loop
                    } // if options exists
                }
               
                //special check as per request from Osajja	
                if(strpos(strtolower($_sku_id_product), "kn95-disposable-earloop-face-masks")!==false){
                    $_orderdItemQty = $_orderdItemQty*10;//pack of 2
                    $_productPrice_conv = $_productPrice_conv/10;//pack of 2
                    $_productAmount_conv = $_productAmount_conv/10;//pack of 2
                }
                
                $product_Quantity = $_orderdItemQty;
                $product_Price = $_productPrice_conv;
                $product_Amount = $_productAmount_conv;
                $product_ProductDiscount = $_ItemDiscount;
                
                if(strpos(strtolower($_productName),"sim") !== false || strpos(strtolower($_sku_id),"sim") !== false || $isSimProduct=="true"){
                    $SimQty=$SimQty+1;
                    $OrderName="SIM Order";
                }
                
                $productXML = $productXML."<Product>";
                $productXML = $productXML."<ProMagentoId>".$_sku_id_product."</ProMagentoId>";
                $productXML = $productXML."<Name>".$_productName."</Name>";
                $productXML = $productXML."<Unit>Each</Unit>";
                $productXML = $productXML."<IsTrial>False</IsTrial>";
                $productXML = $productXML."<EbayProductOptions></EbayProductOptions>";
                $productXML = $productXML."<Size>".$product_Size."</Size>";
                $productXML = $productXML."<Credit>".$product_Credit."</Credit>";
                $productXML = $productXML."<BonousCredit>".$product_BonousCredit."</BonousCredit>";
                $productXML = $productXML."<Color>".$product_Color."</Color>";
                $productXML = $productXML."<MobileNumber>".$product_MobileNumber."</MobileNumber>";
                $productXML = $productXML."<CustomerID>".$product_CustomerID."</CustomerID>";
                $productXML = $productXML."<PipSim>".$product_PipSim."</PipSim>";
                $productXML = $productXML."<PasswordPuk>".$product_PasswordPuk."</PasswordPuk>";
                $productXML = $productXML."<CcaPromoCode></CcaPromoCode>";
                $productXML = $productXML."<AutoTopUp>".$product_AutoTopUp."</AutoTopUp>";
                $productXML = $productXML."<AutoTopUpLimit>".$product_AutoTopUpLimit."</AutoTopUpLimit>";
                $productXML = $productXML."<AutoTopUpAmount>".$product_AutoTopUpAmount."</AutoTopUpAmount>";
                $productXML = $productXML."<Bundle>".$product_Bundle."</Bundle>";
                $productXML = $productXML."<BundleLocation>".$product_BundleLocation."</BundleLocation>";
                $productXML = $productXML."<DataBundleTopUp>".$product_DataBundleTopUp."</DataBundleTopUp>";
                $productXML = $productXML."<DataBundleRenew></DataBundleRenew>";
                $productXML = $productXML."<AutoRenew></AutoRenew>";
                $productXML = $productXML."<WiFiStartDate></WiFiStartDate>";
                $productXML = $productXML."<Quantity>".$product_Quantity."</Quantity>";
                $productXML = $productXML."<Price>".$product_Price."</Price>";
                $productXML = $productXML."<Amount>".$product_Amount."</Amount>";
                $productXML = $productXML."<ProductDiscount>".$product_ProductDiscount."</ProductDiscount>";
                $productXML = $productXML."</Product>";
                
                /*$xmlProduct = '<Product></Product>';
                $xmlPro = simplexml_load_string($xmlProduct);
                $xmlPro->addAttribute("ProMagentoId", $_sku_id_product);
                $xmlPro->addAttribute("Name", $_productName);
                $xmlPro->addAttribute("Unit", 'Each');
                $xmlPro->addAttribute("IsTrial", 'False');
                $xmlPro->addAttribute("EbayProductOptions", '');
                $xmlPro->addAttribute("Size", $product_Size);
                $xmlPro->addAttribute("Credit", $product_Credit);
                $xmlPro->addAttribute("BonousCredit", $product_Color);
                $xmlPro->addAttribute("MobileNumber", $product_MobileNumber);
                $xmlPro->addAttribute("CustomerID", $product_CustomerID);
                $xmlPro->addAttribute("PipSim", $product_PipSim);
                $xmlPro->addAttribute("PasswordPuk", $product_PasswordPuk);
                $xmlPro->addAttribute("CcaPromoCode", '');
                $xmlPro->addAttribute("AutoTopUp", $product_AutoTopUp);
                $xmlPro->addAttribute("AutoTopUpLimit", $product_AutoTopUpLimit);
                $xmlPro->addAttribute("AutoTopUpAmount", $product_AutoTopUpLimit);
                $xmlPro->addAttribute("Bundle", $product_Bundle);
                $xmlPro->addAttribute("BundleLocation", $product_BundleLocation);
                $xmlPro->addAttribute("DataBundleTopUp", $product_DataBundleTopUp);
                $xmlPro->addAttribute("DataBundleRenew", ''); */
                
                //$xmlData = $xmlPro->asXML();
                
                
                ///////////////////////////////////////////////////////////////////////////////////////////////////////
                /////////////////////////////////////////BUNDLE EXTA SIM CHECKS///////////////////////////////////////
                /////////////////////////////////////////////////START///////////////////////////////////////////////
                if(strpos(strtolower($_sku_id), "data-sim-bundle-new")!==false || strpos(strtolower($_sku_id), "data-esim-bundle-new")!==false){
                   if($portableProductBundle=="false"){//only add extra SIM if Bundle not purchased via Portable WiFi
                       if($crm_numb_type=='GO-SIM'){
                            $productXML = $productXML."<Product><ProMagentoId>gosim-esimcard</ProMagentoId><Name>eSIM Connect</Name><Unit>Each</Unit><Size></Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber>".$mobile_number_bundle."</MobileNumber><CustomerID>".$mobile_number_bundle."</CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation>".$product_BundleLocation."</BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";                                   
                       }elseif($crm_numb_type=='E-Sim2Fly'){
                            $productXML = $productXML."<Product><ProMagentoId>sim2fly-esimcard</ProMagentoId><Name>eSIM Connect</Name><Unit>Each</Unit><Size></Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation>".$product_BundleLocation."</BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";                                                               
                       }elseif($crm_numb_type=='WorldSIM-Data-eSIM'){
                            $productXML = $productXML."<Product><ProMagentoId>international-e-simcard</ProMagentoId><Name>WorldSIM International eSIM Pro</Name><Unit>Each</Unit><Size></Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation>".$product_BundleLocation."</BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";                                   
                       } elseif($int_sim=="true"){
                            $productXML = $productXML."<Product><ProMagentoId>data-sim-card-new</ProMagentoId><Name>WorldSIM Data SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                       } elseif($uk_sim=="true"){
                           $productXML = $productXML."<Product><ProMagentoId>UK-SIM-CARD</ProMagentoId><Name>WorldSIM UK Travel SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                       } elseif($europe_sim=="true"){
                           $productXML = $productXML."<Product><ProMagentoId>european-sim-card</ProMagentoId><Name>Europe SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                       } elseif($usa_sim=="true"){
                           $productXML = $productXML."<Product><ProMagentoId>usa-sim-card</ProMagentoId><Name>USA SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";			
                       } else {
                           $productXML = $productXML."<Product><ProMagentoId>data-sim-card-new</ProMagentoId><Name>WorldSIM Data SIM Card </Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";		
                       }
                   }
                }elseif(strpos(strtolower($_sku_id), "uk-sim-bundle")!==false){
                    $productXML = $productXML."<Product><ProMagentoId>UK-SIM-CARD</ProMagentoId><Name>WorldSIM UK Travel SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                }elseif(strpos(strtolower($_sku_id), "europe-sim-bundle")!==false){ /*&& $europe_sim_bundle_card==0*/
                    //$europe_sim_bundle_card=1;
                    $productXML = $productXML."<Product><ProMagentoId>european-sim-card</ProMagentoId><Name>Europe SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                }elseif(strpos(strtolower($_sku_id), "aus-nz-sim-bundle")!==false){
                    $productXML = $productXML."<Product><ProMagentoId>australia-sim-card</ProMagentoId><Name>Australia/New Zealand SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                }elseif(strpos(strtolower($_sku_id), "usa-sim-bundle")!==false){
                    $productXML = $productXML."<Product><ProMagentoId>usa-sim-card</ProMagentoId><Name>USA SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                }elseif(strpos(strtolower($_sku_id), "sa-sim-bundle")!==false){
                    $productXML = $productXML."<Product><ProMagentoId>south-african-sim-card</ProMagentoId><Name>South African SIM Card</Name><Unit>Each</Unit><Size>3-in-1</Size><EbayProductOptions></EbayProductOptions><Credit>0</Credit><Color></Color><MobileNumber></MobileNumber><CustomerID></CustomerID><PipSim></PipSim><PasswordPuk></PasswordPuk><CcaPromoCode></CcaPromoCode><AutoTopUp></AutoTopUp><AutoTopUpLimit></AutoTopUpLimit><AutoTopUpAmount></AutoTopUpAmount><Bundle>".$product_Bundle."</Bundle><BundleLocation></BundleLocation><DataBundleTopUp></DataBundleTopUp><DataBundleRenew></DataBundleRenew><AutoRenew></AutoRenew><Quantity>".$product_Quantity."</Quantity><Price>0</Price><Amount>0</Amount><ProductDiscount>0.0000</ProductDiscount></Product>";
                }
                ///////////////////////////////////////////////////////////////////////////////////////////////////////
                /////////////////////////////////////////BUNDLE EXTA SIM CHECKS///////////////////////////////////////
                /////////////////////////////////////////////////END/////////////////////////////////////////////// 
              
        }
        
        $string = <<<XML
        <Order>
          <MagentoOrderNumber>$lastOrderId</MagentoOrderNumber>
          <EbayOrderNumber></EbayOrderNumber>
          <EbayFee></EbayFee>
          <TransactionDate></TransactionDate>
          <BuyerMessage></BuyerMessage>
          <OrderName>$OrderName</OrderName>
          <SIMType>$SIM_type</SIMType> 
          <Currency>GBP</Currency>
          <OrderCurrency>$_orderCurrencyCode</OrderCurrency>
          <OrderGenerated>$_orderStoreName</OrderGenerated> 
          <PromotionCode>$_orderCouponCode</PromotionCode> 
          <PriceList>Retail Price</PriceList> 
          <IsWFIorder>$IsWFIorder</IsWFIorder>  
          <OrderStatus>Payment Approved</OrderStatus>
          <SimQty>$SimQty</SimQty>
          <ProductQty>$ProductQty</ProductQty>
          
          
          <Customer> 
            <CustomerName>$_orderCustomerName</CustomerName>
            <CustomerFirstName>$CustomerFirstName</CustomerFirstName>
            <CustomerLastName>$CustomerLastName</CustomerLastName>
            <CustomerId></CustomerId>
            <CustomerEmail>$crmCusEmail</CustomerEmail>
            <CustomerPassword><![CDATA[$customerPassword]]></CustomerPassword>
            <Password><![CDATA[$customerPassword]]></Password>
          </Customer>
          
          <DeliveryType>   
            <MagentoId>$_shippingId</MagentoId>
            <DeliverTypeName>$_shippingDescription</DeliverTypeName>
            <DeliveryCost>$_shippingAmount_conv</DeliveryCost>
          </DeliveryType>
          
          
          
          <ShippingAddressInfo Type="ShipToAddress">
            <Name>$shippingAddress_name</Name>
            <State>$shippingAddress_region</State>
            <PosatalCode>$shippingAddress_postcode</PosatalCode>
            <AddressLine1>$shippingAddress_street1</AddressLine1>
            <AddressLine2>$shippingAddress_street2</AddressLine2>
            <Country>$shippingAddress_country</Country>
            <CountryCode>$shippingAddress_countryId</CountryCode>
            <City>$shippingAddress_city</City>
            <Phone>$shippingAddress_telephone</Phone>
          </ShippingAddressInfo>
          
          <BillingAddressInfo Type="BillToAddress">
            <Name>$billingAddress_name</Name>
            <State>$billingAddress_region</State>
            <PosatalCode>$billingAddress_postcode</PosatalCode>
            <AddressLine1>$billingAddress_street1</AddressLine1>
            <AddressLine2>$billingAddress_street2</AddressLine2>
            <Country>$billingAddress_country</Country>
            <CountryCode>$billingAddress_countryId</CountryCode>
            <City>$billingAddress_city</City>
            <Phone>$billingAddress_telephone</Phone>
          </BillingAddressInfo>
          
          $productXML
          
          <Misc Type="SagePayvalues">
            <Protx>$Protx</Protx> 
            <SecureStatus>$SecureStatus</SecureStatus>   
            <ThreeDSecureStatus>$ThreeDSecureStatus</ThreeDSecureStatus> 
            <OrigSecureStatus>$OrigSecureStatus</OrigSecureStatus>
            <CV2Result>$CV2Result</CV2Result> 
            <AddressResult>$AddressResult</AddressResult> 
            <PostCodeResult>$PostCodeResult</PostCodeResult> 
            <TxAuthNo>$TxAuthNo</TxAuthNo> 
            <VPSTxId>$VPSTxId</VPSTxId> 
            <VendorTxnCode>$VendorTxnCode</VendorTxnCode>
            <Referrer>$Referrer</Referrer> 
            <ProtxDetails>$ProtxDetails</ProtxDetails>
            <Result>$Result</Result> 
            <CCLast4Digits>$CCLast4Digits</CCLast4Digits> 
            <CAVV>$CAVV</CAVV> 
            <MSISDN1>$MSISDN1</MSISDN1>
            <CustomerID>$MSISDN1</CustomerID> 
            <PromotionalCode>$PromotionalCode</PromotionalCode>
            <VAT>$VAT</VAT> 
            <SecurityKey>$SecurityKey</SecurityKey>  
            <VoucherCode>$VoucherCode</VoucherCode>
            <OrderPlacedIp>$OrderPlacedIp</OrderPlacedIp>
          </Misc>
          
          <Total>
            <OrderSubTotal>$_subTotal_conv</OrderSubTotal>
            <Tax>$_taxAmount_conv</Tax>
            <OrderDiscount>$_discountAmount_conv</OrderDiscount>
            <OrderedAmount>$_toalAmount_conv</OrderedAmount>
          </Total>
          
        </Order>
          
        XML;
        
        $xml = new \SimpleXMLElement($string, LIBXML_NOCDATA);
        $xml_content =  $xml->asXML();
        echo '<pre>'.htmlentities($string).'</pre>';
        //$this->logger->info($xml_content); 
        exit;
        
        
        
        
    }
    
    public function replaceAccents($str){
        if($str!=''){
			$search = explode(",","ç,æ,,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,,Ç,Æ,,&,ß,ã,9º-1ª,Ñ,1º,nº,Ã,Nº,Dtº,3º,avª,2.º,ñ,2º,2ª,ý,n-º,2ºa,õ,3º,5ª,N°,Sº,Srª,n°,4º,n.º,6º,5º,º,ª,8º,1ª,3ª,2°D°,´,2°,D°,ð,Õ");
			$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE,and,B,a,9o-1a,N,1o,n,A,N,Dto,3o,ava,2.o,n,2o,2a,y,n-o,2oa,o,30,5a,No,So,Sra,no,4o,n.o,6o,5o,o,a,8o,1a,3a,2D,.,2,D,o,O");
			return str_replace($search, $replace, $str);
		}else{
			return $str;
		}
    }
    
    public function extractPlanDetails($plan_code) 
    {
        $result = array();
        
        // Extracting the country
        $country = trim(substr($plan_code, 0, strpos($plan_code, '-')));
        $result['country'] = $country;
        
        // Extracting the plan
        $start = strpos($plan_code, '- ') + 2;
        $end = strpos($plan_code, ' up to-');
        $plan = trim(substr($plan_code, $start, $end - $start));
        $result['plan'] = $plan;
        
        // Extracting the validity
        $start = strpos($plan_code, 'up to-') + 6;
        $validity = intval(trim(substr($plan_code, $start, strpos($plan_code, 'days') - $start)));
        $result['validity'] = $validity;
        
        return $result;
    }
    
    public function getGoSimIccid($orderId){
        $gosim_iccid = 0;
        $goSimApiRespnseCollection = $this->goApiResponseCollectionFactory->create();
        $goSimApiRespnseCollection->addFieldToFilter('order_id', $orderId);
        $finalCollectionCount = count($goSimApiRespnseCollection);
        if ($finalCollectionCount) {
            $finalItem = $goSimApiRespnseCollection->getFirstItem();
            $gosim_iccid = $finalItem->getData('iccid');
        }
        return $gosim_iccid;
    }
    
    public function getRateSheetDetails($country, $plan, $validity, $bundleSupplier)
    {
        // Create the collection instance
        $rateSheetCollection = $this->rateSheetCollectionFactory->create();

        // Apply filters
        $rateSheetCollection->addFieldToFilter('country', $country);
        $rateSheetCollection->addFieldToFilter('days', $validity);
        $rateSheetCollection->addFieldToFilter('supplier', $bundleSupplier);

        switch ($plan) {
            case '1 GB':
                $rateSheetCollection->addFieldToFilter('onegbcode', ['notnull' => true]);
                break;
            case '3 GB':
                $rateSheetCollection->addFieldToFilter('threegbcode', ['notnull' => true]);
                break;
            case '5 GB':
                $rateSheetCollection->addFieldToFilter('fivegbcode', ['notnull' => true]);
                break;
            case '6 GB':
                $rateSheetCollection->addFieldToFilter('sixgbcode', ['notnull' => true]);
                break;
            case '10 GB':
                $rateSheetCollection->addFieldToFilter('tengbcode', ['notnull' => true]);
                break;
            case '20 GB':
                $rateSheetCollection->addFieldToFilter('twentygbcode', ['notnull' => true]);
                break;
            case 'Unlimited GB':
                $rateSheetCollection->addFieldToFilter('unlimitedgbcode', ['notnull' => true]);
                break;
            default:
                break;
        }

        // Log the result
        $finalCollectionCount = count($rateSheetCollection);

        if ($finalCollectionCount === 1) {
            $finalItem = $rateSheetCollection->getFirstItem();
            $supplier = $finalItem->getData('supplier');
            $simType = $finalItem->getData('simtype');

            switch ($plan) {
                case '1 GB':
                    $bundleCode = $finalItem->getData('onegbcode');
                    break;
                case '3 GB':
                    $bundleCode = $finalItem->getData('threegbcode');
                    break;
                case '5 GB':
                    $bundleCode = $finalItem->getData('fivegbcode');
                    break;
                case '6 GB':
                    $bundleCode = $finalItem->getData('sixgbcode');
                    break;
                case '10 GB':
                    $bundleCode = $finalItem->getData('tengbcode');
                    break;
                case '20 GB':
                    $bundleCode = $finalItem->getData('twentygbcode');
                    break;
                case 'Unlimited GB':
                    $bundleCode = $finalItem->getData('unlimitedgbcode');
                    break;
                default:
                    $bundleCode = '';
                    break;
            }

            // Changin case from upper to lower for the ESIMG
            if (substr($bundleCode, 0, 5) === "ESIMG") {
                $bundleCode = substr_replace($bundleCode, 'esimg', 0, 5);
            }

            $result = array(
                'supplier' => $supplier,
                'simType' => $simType,
                'bundleCode' => $bundleCode
            );

            return $result;
        }

        return array(); // Return an empty array if no matching rate sheet found
    }
    
}

