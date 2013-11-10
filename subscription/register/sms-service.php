<?php 
session_start();
global $D_R;
require_once($D_R."/lib/registration/_register_funnel_data_lib.php");
require_once($D_R.'/lib/aws-sns/lib/amazonsns.class.php');
require_once($D_R."/lib/config/_aws_config.php");

$json = new Services_JSON();
$objAmazonSNS = new AmazonSNS($snsAccessKeyId, $snsSecretAccessKey);
$getType=$_REQUEST['type'];
if((isset($getType))&& ($getType=='subscribeToSms')){
	$mobileNum = '1-'.$_POST['mobileNum'];
	try{
		$subscriberList = $objAmazonSNS->listSubscriptionsByTopic($topicName);
		$confirmedSubscriber = array();
		foreach($subscriberList as $k=>$v){
			if($v['SubscriptionArn']!='PendingConfirmation'){
				$confirmedSubscriber[$k]=$v['Endpoint'];
			}
		}
		$chkNum = str_replace('-','',$mobileNum);
		if(in_array($chkNum,$confirmedSubscriber)){
			$msg = 'Number Already Subscribed For Text Alerts.';
			$value=array('status'=>false,'msg'=>$msg);
		}else{
			$addSubscribers = $objAmazonSNS->subscribe($topicName, 'sms', $mobileNum);
			$subId = $_POST['subid'];
			$pref = $_POST['pref'];
			$objRegistrationFunnelData = new registrationFunnelData();
			$prefId = $objRegistrationFunnelData->setAlertPreference($subId,$pref,$mobileNum);	
			$msg = 'You must have recieve a confirmation Message. Please Confirm your Subscription to recieve the alerts.';
			$value=array('status'=>true,'msg'=>$msg);
		}
	}catch(Exception $e){
		if(strpos($e,'Unsupported SMS endpoint')){
			$msg = 'Oops!! Unsupported Mobile Carrier.';
		}else{
			$msg = $e;
		}
		$value=array('status'=>false,'msg'=>$msg);
	}
	$output = $json->encode($value);
	echo strip_tags($output);
	exit;
}
?>