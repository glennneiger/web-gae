<?php
global $D_R;
set_time_limit ( 600*300 );
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");

global $recurlyApiKey;
$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

$filePath="D:\minyanvilledata.csv";
$row = 1;

if (($handle = fopen($filePath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
         htmlprint_r($data[0]);
         htmlprint_r($data[2]);
        $viaId=trim($data['0']);
        $creditCardNo=trim($data[2]);
        $creditCardExpdate=trim($data[3]);
        
        $ccDate=date("Y-m-d",strtotime($creditCardExpdate));;
        $currentDate=date("Y-m-d");
        $ccExpireMonth=date("m",strtotime($creditCardExpdate));
        $ccExpireYear=date("Y",strtotime($creditCardExpdate));
       
        $getCustAddress=$objRecurly->getViaCustAddress($viaId);
        if(!empty($getCustAddress)){
            /*Update billing info in Recurly*/
            try {
             $subId=$getCustAddress['subid'];
             $firstName=$getCustAddress['nameFirst'];
             $lastName=$getCustAddress['nameLast'];
             $phone=$getCustAddress['phone'];
             $address1=$getCustAddress['address1'];
             if(!empty($getCustAddress['address3'])){
                $address2=$getCustAddress['address2'].','.$getCustAddress['address3'];   
             }else{
                $address2=$getCustAddress['address2'];
             }
             $address1=str_replace('&','&amp;',$address1);
             $address2=str_replace('&','&amp;',$address2);
             
             $city=$getCustAddress['city'];
             $state=$getCustAddress['state'];
             $country=$getCustAddress['country'];
             $zip=$getCustAddress['zip'];
             $ccNumber=$creditCardNo;
            /* $cVV=$cvvNumber;*/
             $ccMonth=$ccExpireMonth;
             $ccYear=$ccExpireYear;
            $email=$getCustAddress['email'];
             $objRecurly->updateBillingInfo($subId,$firstName,$lastName,$address1,$address2,$city,$state,$country,$zip,$phone,$ccNumber,$cVV,$ccMonth,$ccYear,$viaId,$email);
            
           } catch(Error $e){
                echo "<br>".'Error: '.$e;
           }
            
        }else{
            echo "<br>".'viaid '.$viaId. ' not exist or already updated.';
        }
        
        
        
    }
    fclose($handle);
}


?>