<?php
set_time_limit(0);//1 hour
global $D_R;
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");
$startSubId=$_GET['startid'];
global $recurlyApiKey;
$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

// create update accout to recurly
$getCustInfo=$objRecurly->getViaCustInfo($startSubId);

exit;
?>