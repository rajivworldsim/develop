<?php
use Magento\Framework\App\Bootstrap;
require '../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$baseUrl =  $_POST['baseUrl'];
$currencySymbol = $_POST['currencySymbol'];
$convRate = $_POST['currencyRate'] ?? 1;
$resourceConnection = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
$storeManager = $objectManager->create(\Magento\Store\Model\StoreManagerInterface::class);
?>
<!-- Start of Tariff Widget calling_from update -->
<?php if($_POST['page'] == "Tariff-Widget-From") {

    $jsonFactory = $objectManager->create(\Agtech\CmsBlocks\Model\ResourceModel\CollectionFactory::class);
    $jsonData = $jsonFactory->create();

    $fromCountry = $_POST['calling_from']; 
    $toCountry = $_POST['calling_to'];
    
    $outgoingBleg = 'SELECT DISTINCT country,outgoing_bleg FROM worldsim_uksim_rates_from_new wt1 WHERE TRIM(country) <> "" AND outgoing_bleg>0';
    $outgoingBlegRate = $resourceConnection->getConnection()->fetchAll($outgoingBleg);
    
    $outgoingBlegCountryRate = 0;
    foreach($outgoingBlegRate as $blegRate){
        if($blegRate['country']==$toCountry){
            $outgoingBlegCountryRate = $blegRate['outgoing_bleg'];
        }
    }
    
    $smsRate = 'SELECT DISTINCT country,sms_bleg FROM worldsim_uksim_rates_from_new wt1
    WHERE TRIM(country) <> "" AND sms_bleg = (SELECT MAX(sms_bleg) FROM worldsim_uksim_rates_from_new AS wt2 WHERE sms_bleg>0 AND country=wt1.country)
    ORDER BY wt1.country';
    $smsRateData = $resourceConnection->getConnection()->fetchAll($smsRate);
    
    $smsTo = 0;
    foreach($smsRateData as $smsRateDatas){
        if($smsRateDatas['country']==$toCountry){
          $smsTo = $smsRateDatas['sms_bleg'];
        }
    }
    
    foreach ($jsonData as $operatorRates){
        if($operatorRates->getcountry()==$fromCountry){
            $countryOperator = $jsonFactory->create()->addFieldToFilter('country',$fromCountry)->addFieldToFilter('outgoing',array('gt' => 0))->addFieldToFilter('operator',array('notnull' => true));
            $operators_options = '';
            $operator_counter = 0;
            foreach($countryOperator as $specificOperator){ ?>
                <?php
                    $data_from=$specificOperator->getData('data');
                    $incoming_from=$specificOperator->getIncoming();
                    $outgoing_from=$specificOperator->getOutgoing();
                    $eu_bleg_discount_operator=$specificOperator->getEuBlegDiscount();
                    $sms_from=$specificOperator->getSms();
                    $sms_from=$sms_from + $smsTo;
                    $profile_from = $specificOperator->getRecommendedProfile();
                    //echo 'outgong-from = '.$outgoing_from.' outgoing-to = '.$outgoingTo.' eu bleg discount to = '.$euBlegCountryTo.' eu bleg discount from = '.$euBlegCountryFrom.'<br/>';
                    if($fromCountry==$toCountry){ // local call
                        if(strpos($profile_from,'Manx') !== false){ // Manx Profile
                            //Outgoing +  outgoing_bleg (without operator) + bleg_discount
                            $out_rate = ($outgoing_from + $outgoingBlegCountryRate + $eu_bleg_discount_operator);
                        }elseif(strpos($profile_from,'Vodafone') !== false){ // Vodafone Profile
                            //Outgoing +  bleg_discount
                            $out_rate = ($outgoing_from + $eu_bleg_discount_operator);
                        }
                    }else{ // international call
                        if(strpos($profile_from,'Manx') !== false){ // Manx Profile
                            //Outgoing +  outgoing_bleg (without operator)
                            $out_rate = ($outgoing_from + $outgoingBlegCountryRate);
                        }elseif(strpos($profile_from,'Vodafone') !== false){ // Vodafone Profile
                            //only Outgoing
                            $out_rate = $outgoing_from;
                        }
                    }
                    
                    $out_cheap = round($out_rate*$convRate,2);
                    $out_cheap = $currencySymbol.number_format((float)$out_cheap, 2, '.', '');
                    
                    $in_cheap = round($incoming_from*$convRate,2);
                    $in_cheap = number_format((float)$in_cheap, 2, '.', '');
                    if($in_cheap <= 0):
                      $in_cheap='FREE';
                    else:
                      $in_cheap = $currencySymbol.$in_cheap;
                    endif;
                    
                    $sms_send = round($sms_from*$convRate,2);
                    $sms_send = $currencySymbol.number_format((float)$sms_send, 2, '.', '');
                    
                    $data_receive = round($data_from*$convRate,2);
                    $data_receive = $currencySymbol.number_format((float)$data_receive, 2, '.', '');
                    
                    if($outgoing_from<=0){
                        $out_cheap="N/A";
                    }
                    if($sms_from<=0){
                        $sms_send="N/A";
                    }
                    if($data_from<=0){
                        $data_receive="N/A";
                    }
                    if($operator_counter==0){ //show by default rate for fist operator
                        $json_data['datarate'] = $data_receive;
                        $json_data['incoming'] = $in_cheap;
                        $json_data['outgoing'] = $out_cheap;
                        $json_data['sms'] = $sms_send;
                    }
                    ?>
                    <?php $operators_options = $operators_options.'<option value="'.$specificOperator->getOperator().'" data-datarate="'.$data_receive.'" data-incoming="'.$in_cheap.'" data-outgoing="'.$out_cheap.'" data-sms="'.$sms_send.'">'.$specificOperator->getOperator().'</option>'; ?>
                    <?php $operator_counter++;
            }   
        }
    }
    
    $json_data['operators'] = 'asdf';//$operators_options;
    
    echo json_encode($json_data);
}
?>
<!-- Data Bundles on Product Pages -->
<?php if($_POST['page'] == "Data-Bundles-On-Product-Pages") {

        $bundle_country = $_POST['bundle_country']; 
        $region_code = $_POST['region_code'];
        
        $file_name_with_path = $baseUrl."media/tariff/databundle_plans_info.json";
        $db_info =  file_get_contents($file_name_with_path);
        $db_info =  json_decode($db_info);
        $bundle_plans = '';
        $xx = 1;
        foreach($db_info as $key =>$value){
               //echo $db_info[$key]->region_code; exit;
              if($region_code==$db_info[$key]->region_code ){//show only data bundles having bundle_cat=D
                  $short_code = $db_info[$key]->short_code;
                  $bundle_limit = $db_info[$key]->bundle_limit;
                  $bundle_validity = $db_info[$key]->bundle_validity;
                  $bundle_rate = $db_info[$key]->bundle_rate;
                  $region_with_limit = $db_info[$key]->region_with_limit;
                  $bundle_sim_id = $db_info[$key]->bundle_sim_id;
                  $bundle_topup_id = $db_info[$key]->bundle_topup_id;
                  
                  $bundle_rate = round($bundle_rate*$convRate);
                  $bundle_rate_format = $currencySymbol.number_format((float)$bundle_rate, 2, '.', '');
                  
                  if($xx==1):
                  $bundle_plans .= '<li>  
                                        <label>
                                            <input type="radio" checked="checked" name="bundle_value" data-shortcode="'.$short_code.'" data-bundlelimit="'.$bundle_limit.'" data-bundlevalidity="'.$bundle_validity.'" data-bundlerate="'.$bundle_rate.'" data-bundlesimid="'.$bundle_sim_id.'" data-bundletopupid="'.$bundle_topup_id.'" data-regionwithlimit="'.$region_with_limit.'"  >
                                            <span class="roundList w-100">
                                                <i class="roundblock rounded-circle">
                                                    '.$bundle_limit.'<span>'.$bundle_rate_format.'</span>
                                                </i>
                                            </span>
                                            <p class="pl-3">Valid for '.$bundle_validity.'</p>
                                        </label>
                                    </li>';
                  else:
                  $bundle_plans .= '<li>  
                                        <label>
                                            <input type="radio" name="bundle_value" data-shortcode="'.$short_code.'" data-bundlelimit="'.$bundle_limit.'" data-bundlevalidity="'.$bundle_validity.'" data-bundlerate="'.$bundle_rate.'" data-bundlesimid="'.$bundle_sim_id.'" data-bundletopupid="'.$bundle_topup_id.'" data-regionwithlimit="'.$region_with_limit.'"  >
                                            <span class="roundList w-100">
                                                <i class="roundblock rounded-circle">
                                                    '.$bundle_limit.'<span>'.$bundle_rate_format.'</span>
                                                </i>
                                            </span>
                                            <p class="pl-3">Valid for '.$bundle_validity.'</p>
                                        </label>
                                    </li>';
                  endif;
              $xx++;
              }
        }
        
        //echo $bundle_plans;
        
        $json_data['plans'] = $bundle_plans;
        
        echo json_encode($json_data);
}
?>


<!-- Data Bundle Page -->
<?php if($_POST['page'] == "Data-Bundle-Page") {
        $baseUrl =  $storeManager->getStore()->getBaseUrl();
        $currency = $storeManager->getStore()->getCurrentCurrencyCode();
        $rates_currency = $storeManager->getStore()->getCurrentCurrencyRate();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currency);
        $currencySymbol = $currency->getCurrencySymbol();
        $convRate = $rates_currency;
        $bundle_country = $_POST['bundle_country']; 
        $region_code = $_POST['region_code'];
        
        $file_name_with_path = $baseUrl."media/tariff/databundle_plans_info.json";
        $db_info =  file_get_contents($file_name_with_path);
        $db_info =  json_decode($db_info);
        $bundle_plans = '';
        $xx = 1;
        foreach($db_info as $key =>$value){
              if($region_code==$db_info[$key]->region_code ){//show only data bundles having bundle_cat=D
                  $short_code = $db_info[$key]->short_code;
                  $bundle_limit = $db_info[$key]->bundle_limit;
                  $bundle_validity = $db_info[$key]->bundle_validity;
                  $bundle_rate = $db_info[$key]->bundle_rate;
                  $region_with_limit = $db_info[$key]->region_with_limit;
                  $bundle_sim_id = $db_info[$key]->bundle_sim_id;
                  $bundle_topup_id = $db_info[$key]->bundle_topup_id;
                  
                  $bundle_rate = round($bundle_rate*$convRate);
                  $bundle_rate_format = $currencySymbol.number_format((float)$bundle_rate, 2, '.', '');
                  
                  if($xx==1):
                  $bundle_plans .= '<li>
                                        <label>
                                            <input type="radio" checked="checked" name="bundle_value" data-shortcode="'.$short_code.'" data-bundlelimit="'.$bundle_limit.'" data-bundlevalidity="'.$bundle_validity.'" data-bundlerate="'.$bundle_rate.'" data-bundlesimid="'.$bundle_sim_id.'" data-bundletopupid="'.$bundle_topup_id.'" data-regionwithlimit="'.$region_with_limit.'"  />
                                            <span class="circle">
                                                '.$bundle_limit.'
                                                <i class="grayColor"> Validity: '.$bundle_validity.' </i> 
                                            </span>
                                            <span class="rate">'.$bundle_rate_format.'</span>
                                        </label>
                                    </li>';
                  else:
                  $bundle_plans .= '<li>  
                                        <label>
                                            <input type="radio" name="bundle_value" data-shortcode="'.$short_code.'" data-bundlelimit="'.$bundle_limit.'" data-bundlevalidity="'.$bundle_validity.'" data-bundlerate="'.$bundle_rate.'" data-bundlesimid="'.$bundle_sim_id.'" data-bundletopupid="'.$bundle_topup_id.'" data-regionwithlimit="'.$region_with_limit.'"  >
                                            <span class="circle">
                                                '.$bundle_limit.'
                                                <i class="grayColor"> Validity: '.$bundle_validity.' </i> 
                                            </span>
                                            <span class="rate">'.$bundle_rate_format.'</span>
                                        </label>
                                    </li>';
                  endif;
              $xx++;
              }
        }
        
        //echo $bundle_plans;
        
        $json_data['plans'] = $bundle_plans;
        
        echo json_encode($json_data);
}
?>

<!-- DataSIM Tariff Widget -->
<?php if($_POST['page'] == "DataSIM-Tariff-Widget") {
     
        //$convRate=$convRate/1.35;//as DataSIM rates are in USD in DB  

        $fromCountry = $_POST['callingFrom'];
        $callingTo = 'SELECT DISTINCT operator,data FROM worldsim_uksim_rates_from_new WHERE data > 0 AND country="'.$fromCountry.'" AND operator <> "" ORDER BY data ASC';
        $dataSimRate = $resourceConnection->getConnection()->fetchAll($callingTo);
            $operator_counter = 0;
            foreach($dataSimRate as $operatorRates){ ?>
                    <?php
                        $data_from=$operatorRates['data'];
                        $data_receive = round($data_from*$convRate,2);
                        $data_receive = $currencySymbol.number_format((float)$data_receive, 2, '.', '');
                        if($data_from<=0){
                            $data_receive="N/A";
                        }
                        if($operator_counter==0){ //show by default rate for fist operator
                            $json_data['datarate'] = $data_receive;
                        }
                        ?>
                        <?php $operators_options .= '<option value="'.$operatorRates['operator'].'" data-datarate="'.$data_receive.'">'.$operatorRates['operator'].'</option>'; ?>
                        <?php $operator_counter++;
            }
        
        $json_data['operators'] = $operators_options;
        
        echo json_encode($json_data);
}
?>
<!-- Vitrual-Tariff-Widget -->
<?php if($_POST['page'] == "Vitrual-Tariff-Widget") {
        $fromCountry = $_POST['calling_from']; 
        $callingTo = 'SELECT month12 FROM worldsim_virtual_number_rates WHERE country_name = "'.$fromCountry.'" ORDER BY month3 ASC LIMIT 1';
        $virtualRate = $resourceConnection->getConnection()->fetchAll($callingTo);
            $operator_counter = 0;
            foreach($virtualRate as $operatorRates){ 
                $data_from=$operatorRates['month12'];
                $data_receive = round($data_from/12,2);//12 months converted to per month rate
                $data_receive = round($data_receive*$convRate,2);
                $data_receive = $currencySymbol.number_format((float)$data_receive, 2, '.', '');
                if($data_from<=0){
                    $data_receive="N/A";
                }
                if($operator_counter==0){ //show by default rate for fist operator
                    $json_data['datarate'] = $data_receive;
                }
                $operator_counter++;
            }
        echo json_encode($json_data);
}
?>