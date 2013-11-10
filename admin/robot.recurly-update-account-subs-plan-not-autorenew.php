<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");
global $recurlyApiKey;
$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

// update do not auto renew Subscription Plan to Recurly

$setSubsPlanInfo=$objRecurly->updateDoNotAutoRenewPlanToRecurly();

exit;
?>