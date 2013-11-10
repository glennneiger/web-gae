<?php
global $D_R,$recurlyApiKey;
require_once($D_R.'/lib/recurly/recurly.php');
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/_user_data_lib.php");
session_start();
$json = new Services_JSON();
Recurly_Client::$apiKey = $recurlyApiKey;
$type=$_POST['type'];
$objUserData= new userData();
if($type=="manageAccount"){
    $objRecurlyData= new recurlyData();
    $manageEmail=$_POST['manageEmail'];
    if($_SESSION['email']!==$manageEmail){

        $checkEmail=$objUserData->checkUserViaAvailibilityByEmail($manageEmail);
        if(!empty($checkEmail)){
            $messg='This email address is already in our system. Please choose another email ID.';
            $value=array('status'=>false,'msg'=>$messg);
            $output = $json->encode($value);
            echo strip_tags($output);
            exit;
        }
    }
    $subId=$_SESSION['SID'];
    $managePassword=$_POST['managePassword'];
    $firstName=$_POST['firstname'];
    $lastName=$_POST['lastname'];
    $address1=$_POST['billingadd1'];
    $address2=$_POST['billingadd2'];
    $city=$_POST['city'];
    $state=$_POST['state'];
    $zip=$_POST['zip'];
    $country=$_POST['country'];
    $phone=$_POST['phone'];
    $manageAccountCreditType=$_POST['manageAccountCreditType'];
    $ccNumber=$_POST['manageAccountCreditNumber'];
    $ccMonth=$_POST['manageAccountccMonth'];
    $ccYear=$_POST['manageAccountccYear'];

    $objRecurlyData->updateAccount($subId,$email,$email,$firstName,$lastName,$companyName,NULL);
    $str = $objRecurlyData->setBillingInfoToRecurly($subId,$firstName,$lastName,$address1,$address2,$city,$state,$country,$zip,$phone,$ccNumber,$cVV,$ccMonth,$ccYear,NULL,NULL);
    $objUserData->setCustomerInfo($manageEmail,$managePassword,$subId);

    if($str!=''){
		$messg=$str;
        $value=array('status'=>false,'msg'=>$messg);
        $output = $json->encode($value);
        echo strip_tags($output);
        exit;
    }

    $messg='Your changes have been saved.';
    $value=array('status'=>true,'msg'=>$messg);
    $output = $json->encode($value);
    echo strip_tags($output);

}

?>