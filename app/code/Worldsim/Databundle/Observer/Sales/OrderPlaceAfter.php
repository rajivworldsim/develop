<?php
/**
 * Copyright © Ascuretech.com All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Worldsim\Databundle\Observer\Sales;

use Worldsim\Databundle\Model\ResourceModel\RateSheetDataBundle\CollectionFactory as RateSheetCollectionFactory;
use Worldsim\Databundle\Model\GoAPIResponse;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class OrderPlaceAfter implements ObserverInterface
{
    private $goAPIResponse;
    private $rateSheetCollectionFactory;

    public function __construct(
        GoAPIResponse $goAPIResponse,
        RateSheetCollectionFactory $rateSheetCollectionFactory
    ) {
        $this->goAPIResponse = $goAPIResponse;
        $this->rateSheetCollectionFactory = $rateSheetCollectionFactory;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $logger = $this->createLogger();

        // Log order information
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();
        $customerEmail = $order->getCustomerEmail();
        $responseData = array();
        $finalResponseData = array();
        $responseDataOrder = array();
        $isNewSim = "";
        $items = $order->getAllVisibleItems();

        foreach ($items as $item) {
            $plan_code = "";
            $_sku_id = $item->getSku();
            $itemData = $item->getData();
            //$logger->info(print_r($itemData, true));
            
            foreach ($itemData as $key => $value) {
                if ($key === 'product_options') {
                    $productOptions = $value;

                    if (isset($productOptions['options'])) {
                        
                        foreach ($productOptions['options'] as $option) {
                            $isNewSim = "";
                            $logger->info($option);
                            if ($option['label'] === 'Bundle') {
                                $plan_code = $option['print_value'];
                                $logger->info($plan_code);
                                
                                $plan_details = $this->extractPlanDetails($plan_code);

                                // Accessing the extracted values
                                $country = $plan_details['country'];
                                $plan = $plan_details['plan'];
                                $validity = $plan_details['validity'];
                                
                                $logger->info($country);
                                $logger->info($plan);
                                $logger->info($validity);

                                $currentItemData = $this->getRateSheetDetails($country, $plan, $validity);

                                if (!empty($currentItemData)) {
                                    $supplier = $currentItemData['supplier'];
                                    $simType = $currentItemData['simType'];
                                    $bundleCode = $currentItemData['bundleCode'];
                                    
                                } else {
                                    $supplier = '';
                                    $simType = '';
                                    $bundleCode = '';
                                    $logger->info('No matching rate sheet found.');
                                }
                                    
                                $logger->info('Supplier: ' . $supplier);
                                $logger->info('SIM Type: ' . $simType);
                                $logger->info('Bundle Code: ' . $bundleCode);
                            }  
           
                            if ($option['label'] === 'Mobile number/Customer ID' && $option['print_value'] != '' && strpos($_sku_id,"data-bundle-topup")!==false) {
                                $mob_cus_id = $option['value'];
                                $isNewSim = 'NO';

                                if ($supplier == 'Worldsim') {
                                    $logger->info('SIM Type: ' . $simType);
                                    $product_name = 'International sim card';
                                } elseif ($supplier == 'Go') {
                                    $logger->info('SIM Type: ' . $simType);
                                    $product_name = 'International esim card';

                                    $responseData = $this->validationExistingGoSimAPI($mob_cus_id);

                                    $this->saveToGoSimAPIResponseSheet($responseData, $orderId, $isNewSim, $bundleCode, $customerEmail);

                                    $payload = array(
                                        "type" => "transaction",
                                        "assign" => false,
                                        "Order" => array(
                                            array(
                                                "type" => "bundle",
                                                "quantity" => 1,
                                                "item" => $bundleCode
                                            )
                                        )
                                    );

                                    $responseDataOrder = $this->NewGoSimAPI($payload);

                                    

                                    if($responseDataOrder['statusMessage'] == "Order completed") {
                                        // Payload data
                                        $payload = array(
                                            'iccid' => $mob_cus_id,
                                            'name' => $bundleCode,
                                            'startTime' => '',
                                            'repeat' => 0
                                        );

                                        $finalResponseData = $this->FinalGoSimAPI($payload);
                                    }
                                    else {
                                        throw new \Exception('Something went wrong please try again letter');
                                    }
                                }
                            } 

                            elseif($option['label'] === 'SIM Type' && $option['print_value'] != ''){
                                $isNewSim = 'YES';
                                if ($supplier == 'Worldsim') {
                                    $logger->info('SIM Type: ' . $simType);
                                    $product_name = 'International sim card';
                                } elseif ($supplier == 'Go') {
                                    $logger->info('SIM Type: ' . $simType);
                                    $product_name = 'International esim card';

                                    $payload = array(
                                        "type" => "transaction",
                                        "assign" => false,
                                        "Order" => array(
                                            array(
                                                "type" => "bundle",
                                                "quantity" => 1,
                                                "item" => $bundleCode
                                            )
                                        )
                                    );

                                    $responseDataOrder = $this->NewGoSimAPI($payload);

                                    

                                    if($responseDataOrder['statusMessage'] == "Order completed") {
                                        // Payload data
                                            $payload = array(
                                            "iccid" => "",
                                            "name" => $bundleCode,
                                            "startTime" => "",
                                            "repeat" => 0
                                        );

                                        $finalResponseData = $this->FinalGoSimAPI($payload);

                                        $iccid = $finalResponseData["esims"][0]["iccid"];
                                        if (!empty($iccid)) {
                                            $responseData = $this->validationExistingGoSimAPI($iccid);
                                            $this->saveToGoSimAPIResponseSheet($responseData, $orderId, $isNewSim, $bundleCode, $customerEmail);
                                        }




                                    }
                                    else {
                                        throw new \Exception('Something went wrong please try again letter');
                                    }
                                    
                                } elseif ($supplier == 'E-Sim2Fly') {
                                    $logger->info('SIM Type: ' . $simType);
                                    $product_name = 'International esim card';
                                }
                            }
                        }
                    }
                }
            }
        }
        $logger->info('Response Data: ');
        $logger->info(print_r($responseData, true));
        $logger->info('Final Response Data: ');
        $logger->info(print_r($finalResponseData, true));
        $logger->info('Order placed: Order ID - ' . $orderId);
    }

    public function NewGoSimAPI($payload)
    {
        $logger = $this->createLogger();

        // API endpoint URL
        $apiUrl = 'https://api.esim-go.com/v2.2/orders?api_key=gdrhEi5Aclz5ioEYZsBtiz2eD3Spb5FK8g711dKi';

        // Convert payload data to JSON
        $payloadJson = json_encode($payload);

        // Initialize cURL session
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payloadJson)
        ));

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            throw new \Exception('cURL request failed: ' . curl_error($ch));
        }

        // Get the HTTP status code
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($ch);

        // Handle the response
        if ($httpStatus === 200) {
            // Successful response
            $responseData = json_decode($response, true);
            return $responseData;
        } else {
            // Error response
            $logger->info('Response Data: ');
            $logger->info(print_r(json_decode($response, true), true));

            // Throw an exception with the error response
            throw new \Exception('1-ORDER- API request failed with status code: ' . $httpStatus . '. Response: ' . $response);
        }
    }

    public function validationExistingGoSimAPI($mob_cus_id)
    {
        $logger = $this->createLogger();
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
            throw new \Exception('cURL request failed: ' . curl_error($ch));
        }

        // Get the HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($ch);

        // Check the HTTP status code
        if ($httpCode == 200) {
            // Request was successful, process the response
            $responseData = json_decode($response, true);

            $logger->info(print_r($responseData, true)); 




            $date = new \DateTime();
            $date->setTimestamp($responseData['firstInstalledDateTime'] / 1000); // Divide by 1000 to convert milliseconds to seconds
            $formattedDate = $date->format('Y-m-d H:i:s');
                     

            $logger->info('formattedDate'.$formattedDate);
            $responseData['firstInstalledDateTime'] = $formattedDate;
            $logger->info('formattedDateqq'.$responseData['firstInstalledDateTime']);
            if($responseData['profileStatus'] != "Unavailable" ){
                return $responseData;
            } else {
                throw new \Exception('Profile status is Unavailable');
            }
            
        } else {
            // Request failed, display the HTTP status code
            throw new \Exception('2- Validation-API request failed with status code: ' . $httpCode . '. Response: ' . $response);
        }
    }

   public function FinalGoSimAPI($payload)
    {

        $logger = $this->createLogger();
        // API endpoint URL
        $apiUrl = 'https://api.esim-go.com/v2.2/esims/apply?api_key=gdrhEi5Aclz5ioEYZsBtiz2eD3Spb5FK8g711dKi';

        // Convert payload data to JSON
        $payloadJson = json_encode($payload);

        // Initialize cURL session
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payloadJson)
        ));

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            throw new \Exception('cURL request failed: ' . curl_error($ch));
        }

        // Get the HTTP status code
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($ch);

        // Handle the response
        if ($httpStatus === 200) {
            // Successful response
            $responseData = json_decode($response, true);
            return $responseData;
        } else {
            $logger->info('FinalGoSimAPI Data: ');
            $logger->info(print_r(json_decode($response, true), true));
            // Error response
            throw new \Exception('3- Apply-API request failed with status code: ' . $httpStatus. '. Response: ' . $response);
        }
    }


    public function saveToGoSimAPIResponseSheet($responseData, $orderId, $isNewSim, $bundleCode, $customerEmail)
    {
        $logger = $this->createLogger();

        $responseData['order_id'] = $orderId;
        $responseData['is_new_sim'] = $isNewSim;
        $responseData['bundle_code'] = $bundleCode;
        $responseData['email'] = $customerEmail;
    
        // Save response data to the model
        try {
            $goAPIResponse = $this->goAPIResponse->load(null);
            $goAPIResponse->setData($responseData);
            $goAPIResponse->save();
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        }
    }

    public function extractPlanDetails($plan_code) 
    {
        $logger = $this->createLogger();
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

    public function getRateSheetDetails($country, $plan, $validity)
    {
        $logger = $this->createLogger();
        // Create the collection instance
        $rateSheetCollection = $this->rateSheetCollectionFactory->create();

        // Apply filters
        $rateSheetCollection->addFieldToFilter('country', $country);
        $rateSheetCollection->addFieldToFilter('days', $validity);

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

    public function createLogger()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GoAPIResponse.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        
        return $logger;
    }
}

