<?php
global $D_R;
session_start();
require_once($D_R.'/lib/recurly/recurly.php');
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
Recurly_Client::$apiKey = $recurlyApiKey;

$objFunnelData = new registrationFunnelData();
$objRecurlyData = new recurlyData();
switch($_POST['type']){
	case "addSubscription":
		$subId = $_SESSION['SID'];
		echo $objRecurlyData->addSubscriptionToAccount($subId,$_POST['planCode'],'1','','addSubscription');
		break;
} //switch
?>