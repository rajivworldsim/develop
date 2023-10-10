<?php if(!empty($_GET['CusEmail'])): ?>

<?php

try {
    $client = new SoapClient("https://accounts.worldsim.com/services/xmlservice.asmx?wsdl");
    
    $item = new stdClass;
    $item->CustomerEmail = $_GET['CusEmail'];
    $crm_emailResult = $client->GetCustomerIDByEmail($item);
    
    $crm_email_result = $crm_emailResult->GetCustomerIDByEmailResult;
    echo $item->CustomerEmail.'=>'.$crm_email_result;
    
} catch (Exception $e) {
    //echo "Error!<br />";
    $error_msg = $e -> getMessage ();
    echo $error_msg;
}

?>
<?php endif; ?>