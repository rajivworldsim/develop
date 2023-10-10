<?php

use Magento\Framework\App\Bootstrap;
require '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$customerSession = $objectManager->create('Magento\Customer\Model\Session');
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');

$Topuplogger_Log = $objectManager->get('Agtech\Loginlog\Block\Topuploggermod');
$Expiredlogger_Log = $objectManager->get('Agtech\Loginlog\Block\Expiredlogmod');


?>

<?php

$mob_cus_id  = $_POST['mob_cus_id'] ?? '';
$supplier  = $_POST['supplier'] ?? '';
$pass_puk    = $_POST['pass_puk'] ?? '';
$planCode    = $_POST['planCode'] ?? '';
$is_new_sim    = $_POST['is_new_sim'] ?? '';
$isauto_topup = $_POST['isauto_topup'] ?? '';
if (isset($_POST['page'])) {
    $_page = $_POST['page'];
} else {
    $_page = null;
}
//$isauto_topup = strtolower($isauto_topup);
//$topupvoucher = $_POST['topupvoucher'];
$topupvoucher = '';

$error_msg   = "Your details could not be located, please ensure you have typed them correctly. For assistance from our agents please <a href='javascript:void(Tawk_API.toggle())'>click here</a> ";
$topup_error_msg = "You need to place atleast one normal topup order before you setup Auto TopUp against your number.";
$topupvoucher_error_msg = "";
$success_msg = "Authentication successful";

if ($pass_puk == "") {
    $pass_puk = "PinLess";
}

if ($mob_cus_id != '') {
    $mob_cus_id_1st2 = substr($mob_cus_id, 0, 2);
    if ($mob_cus_id_1st2 == "07") {
        $mob_cus_id = str_replace('07', '447', $mob_cus_id);
    }
    if ($_page == "DataBundleTopUp") { // DataBundle TopUp Button Clicked
        if (strlen($mob_cus_id) != 19) {
            if ($supplier == 'WorldSIM') {
                //set sessions for msisdn and passpuk
                $customerSession->setTopUpMobCusId($mob_cus_id);
                $customerSession->setTopUpPassPuk($pass_puk);
                $databundle_location = $_POST['databundle_location'] ?? ''; //Band1, Band2,...
                $databundle_type = $_POST['databundle_type'] ?? ''; //7days, 10days,...
                $databundle_country = $_POST['databundle_country'] ?? ''; //Sweden, Turkey, ...
                $databundle_autotopup = $_POST['isauto_topup'] ?? ''; //No or Yes
                $databundle_limit = $_POST['databundle_limit'] ?? ''; //means 1GB, 3GB, 5GB, 10GB...
                $databundle_sku = $databundle_location . "_" . $databundle_limit . "-" . $databundle_type;
                $bundle_cat = "D"; //default DataBundle (D)
                
                try {
                    $client1 = new SoapClient("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");
                    $params = array();
                    $vouchercheckResult = '';
                    $item = new stdClass;
                    $item->MSISDN = $mob_cus_id;
                    $item->PIN = "PinLess";
                    $autoTopUp_Validation = $client1->ValidateAutoTopUp($item);
                    $topUpValidationResult =  $autoTopUp_Validation->ValidateAutoTopUpResult;
                    $customerSession->setTopUpValidationResult($topUpValidationResult);
                    if ($topUpValidationResult == "MsisdnNotValid") {
                        $topUpValidationResult = "notvalidate";
                    }
                    if ($topUpValidationResult != "notvalidate") {
                        $databundle_item = new stdClass;
                        $databundle_item->MSISDN = $mob_cus_id;
                        $databundle_item->PIN = "DataBundle";
                        //$BundleCheck_Validation = $client1->ValidateBundle($databundle_item);
                        $BundleCheck_Validation = $client1->ValidateAutoTopUp($databundle_item);
                        $BundleCheck_Validation_Result = $BundleCheck_Validation->ValidateAutoTopUpResult;
                        $return_data = $BundleCheck_Validation_Result;
                        //echo $return_data; exit;

                        if ($bundle_cat == "VD") { //means Voice Bundle then check SIM Type should not be DataSIM (DA)
                            $client_bundle = new SoapClient("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");
                            $item_bundle = new stdClass;
                            $item_bundle->MSISDN = $mob_cus_id;
                            $crm_NumbResult = $client_bundle->CheckSimTypeByMsisdn($item_bundle);
                            $crm_numb_type = $crm_NumbResult->CheckSimTypeByMsisdnResult;
                            if ($crm_numb_type == "DA") { //if DataSIM then break the code and return error, canot add Voice Bundle on Data SIM
                                echo "Voice-Bundle-Selected-Over-Data-SIM";
                                exit;
                            }
                        }

                        if ($return_data == "Bundle-Is-Valid") { //if Number Enter is OK for Bundle then return ADD BUNDLE BUTTON CODE
                            echo "ok-to-add-bundle"; //add bundle here
                            exit;
                        } else {
                            echo $return_data;
                            exit;
                        }
                    } else {
                        echo $topUpValidationResult; // echo error message MSISDN and PIN/PUK not matched...details are incorrect
                        exit;
                    }
                } catch (Exception $e) {
                    $topUpValidation_error_msg = $e->getMessage();
                    $customerSession->setTopUpValidationResult($topUpValidation_error_msg);
                    $Topuplogger_Log->messageLog('Mobile Number/Customer ID : ' . $mob_cus_id . '####' . $topUpValidation_error_msg, null, 'TopUpValidation_Error.log');
                    echo $topUpValidation_error_msg;
                    exit;
                }
            } else {
                echo "Bundle-Not-Valid";
                exit;
            }
        } elseif (strlen($mob_cus_id) == 19) {

            if ($supplier == 'Go') {
                $apiUrl = "https://api.esim-go.com/v2.2/esims/{$mob_cus_id}?api_key=gdrhEi5Aclz5ioEYZsBtiz2eD3Spb5FK8g711dKi";

                // Initialize cURL session
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Execute the GET request
                $response = curl_exec($ch);

                // Check if cURL request was successful
                if ($response === false) {
                    echo 'Error: ' . curl_error($ch);
                    exit;
                }

                // Get the HTTP status code
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // Close the cURL session
                curl_close($ch);

                // Check the HTTP status code
                if ($httpCode == 200) {
                    // Request was successful, process the response
                    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customercheck.log');
                    $logger1 = new \Zend_Log();
                    $logger1->addWriter($writer);
                    $responseData = json_decode($response, true);
                    $logger1->info(print_r($responseData,true));
                     $date = new \DateTime();
                     $date->setTimestamp($responseData['firstInstalledDateTime'] / 1000); // Divide by 1000 to convert milliseconds to seconds
                     $formattedDate = $date->format('Y-m-d H:i:s');
                     $responseData['firstInstalledDateTime'] = $formattedDate;


                    
                    $logger1->info(print_r($responseData,true));


                    // Check the profileStatus
                    // Assuming profileStatus is stored in a variable called $profileStatus
                    if ($responseData['profileStatus'] != "Unavailable") {
                        // SIM is OK to apply bundle
                        // Place your code here
                        echo "ok-to-add-bundle";
                        $responseData['bundle_code'] = $planCode;            
                        exit;
                    } else {
                        echo 'SIM profile status is Unavailable';
                        exit;
                    }
                } else {
                    // Request failed, display the HTTP status code
                    echo 'Error: Access denied' . $httpCode;
                    exit;
                }
            } else {
                echo "Bundle-Not-Valid";
                exit;
            }
        }
    } else { // Normal TopUp Button Clicked

        /////////////////new CRM check////////////////
        ///AutoTopUp Validation and other checks
        try {
            $client = new SoapClient("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");

            $params = array();
            $vouchercheckResult = '';
            $item = new stdClass;
            $item->MSISDN = $mob_cus_id;
            $item->PIN = $pass_puk;
            //$item->PIN = "PinLess";
            $item->promo = $topupvoucher;
            $autoTopUp_Validation = $client->ValidateAutoTopUp($item);
            $topUpValidationResult =  $autoTopUp_Validation->ValidateAutoTopUpResult;
            $customerSession->setTopUpValidationResult($topUpValidationResult);
            //echo $topUpValidationResult;
            if ($topupvoucher != '') {
                //if(strpos($topUpValidationResult,"notopupfound")!==false || strpos($topUpValidationResult,"nocustomerfound")!==false || strpos($topUpValidationResult,"topupfound")!==false || strpos($topUpValidationResult,"successful")!==false){
                if (strpos($topUpValidationResult, "MsisdnIsValid") !== false || strpos($topUpValidationResult, "successful") !== false) {
                    $PromoCodeValidationResult = substr($topUpValidationResult, strpos($topUpValidationResult, "-") + 1); //NotUsed  -  Used   - NotValid
                    if ($PromoCodeValidationResult == "NotUsed") {
                        echo $topUpValidationResult;
                    } else if ($PromoCodeValidationResult == "Used") {
                        echo "Promo Code has already been used. Please either remove or add another promo code.";
                    } else if ($PromoCodeValidationResult == "NotValid") {
                        echo "Promo Code entered is not valid. Please either remove or add another promo code.";
                    } else {
                        echo $topUpValidationResult;
                    }
                } else {
                    echo $topUpValidationResult;
                }
            } else {
                if ($topUpValidationResult == "UR") {
                    $Expiredlogger_Log->messageLog('Mobile Number: ' . $mob_cus_id, null, 'Expired_Numbers_TopUp_Attempt.log');
                    //insert into database
                    $write = $resource->getConnection();
                    $write->insert(
                        "worldsim_expired_topup_attempt",
                        array("id" => "", "date" => date('Y-m-d H:i:s'), "mobile" => $mob_cus_id)
                    );
                }
                echo $topUpValidationResult;
            }
        } catch (Exception $e) {
            $topUpValidation_error_msg = $e->getMessage();
            $customerSession->setTopUpValidationResult($topUpValidation_error_msg);
            $Topuplogger_Log->messageLog('Mobile Number/Customer ID : ' . $mob_cus_id . '####' . $topUpValidation_error_msg, null, 'TopUpValidation_Error.log');
            echo $topUpValidation_error_msg;
        }
    }
} else { //if no mobile_cus_id and no pass_puk
    echo $error_msg;
}
