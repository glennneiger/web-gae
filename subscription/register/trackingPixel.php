<?php
session_start();

include_once($D_R."/lib/registration/_register_funnel_data_lib.php");

$objRegisterData= new registrationFunnelData();

if(empty($_COOKIE['trackingUserId'])){
    $userId = rand(60000,9999999999);
    $setCookieTime=time()+63072000;  /*  2 yrs */
    mcookie("trackingUserId",$userId,$setCookieTime);    
}else{
    $userId=$_COOKIE['trackingUserId'];
}

$utmSource=$_REQUEST['utmSource'];
if(!empty($utmSource)){
    $_SESSION['utmSourceUrl'] = $utmSource;
}
$utmMedium=$_REQUEST['utmMedium'];
if(!empty($utmMedium)){
    $_SESSION['utmMediumUrl'] = $utmMedium;
}
$utmTerm=$_REQUEST['utmTerm'];
if(!empty($utmTerm)){
    $_SESSION['utmTermUrl'] = $utmTerm;
}
$utmContent=$_REQUEST['utmContent'];
if(!empty($utmContent)){
    $_SESSION['utmContentUrl'] = $utmContent;
}
$utmCampaign=$_REQUEST['utmCampaign'];
if(!empty($utmCampaign)){
    $_SESSION['utmCampaignUrl'] = $utmCampaign;
}

$utmSource=$_SESSION['utmSourceUrl'];
$utmMedium=$_SESSION['utmMediumUrl'];
$utmTerm=$_SESSION['utmTermUrl'];
$utmContent=$_SESSION['utmContentUrl'];
$utmCampaign=$_SESSION['utmCampaignUrl'];
$productName=$_REQUEST['productName'];
$refererUrl=$_REQUEST['refererUrl'];
$url=$_REQUEST['url'];
$pageName=$_REQUEST['pageName'];

if(!empty($_REQUEST['planCode'])){
    $planCode=$_REQUEST['planCode'];
    $objRegisterData->setTrackingPixel($planCode,$productName,$userId,$url,$refererUrl,$utmSource,$utmMedium,$utmTerm,$utmContent,$utmCampaign,$pageName);
}elseif(is_array($_SESSION['recently_added']['SUBSCRIPTION'])){
    foreach($_SESSION['recently_added']['SUBSCRIPTION'] as $key=>$value){
       $planCode=$key;
       $objRegisterData->setTrackingPixel($planCode,$productName,$userId,$url,$refererUrl,$utmSource,$utmMedium,$utmTerm,$utmContent,$utmCampaign,$pageName);
    }
}else{
        $objRegisterData->setTrackingPixel($planCode,$productName,$userId,$url,$refererUrl,$utmSource,$utmMedium,$utmTerm,$utmContent,$utmCampaign,$pageName);        
}

?>