<?php
set_time_limit(0);
ini_set('memory_limit','256M');
global $D_R,$recurlyApiKey;
$offset = $_GET['offset'];

$sqlGetVIATrans="SELECT * FROM subscription_transaction_via WHERE creation_date >='2011-05-01'
AND creation_date <= '2012-05-31' AND payment_clear_status = '3' AND base_amount<0 ";
$resGetVIATrans=exec_query($sqlGetVIATrans);
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");

$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;
$index="0";

foreach($resGetVIATrans as $key=>$transaction)
{
	$sqlGetTransDetails="SELECT SCO.id,subscription_id,viaid,typeSpecificId,price,description,startDate,
	expireDate,billDate,orderStatus
FROM subscription_cust_order_via SCO,subscription_transaction_via T WHERE SCO.viaid=T.customer_id AND
 (DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.billDate,'%Y-%m-%d')
 or DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.startDate,'%Y-%m-%d'))
AND round(SCO.price,2)='".$transaction['base_amount']."' and SCO.viaid=".$transaction['customer_id']." ORDER BY SCO.orderNumber DESC" ;
	$resGetTransDetails=exec_query($sqlGetTransDetails,1);
	if(empty($resGetTransDetails)){
						$sqlGetTransDetails_amount="SELECT SCO.id,subscription_id,viaid,typeSpecificId,price,description,startDate,
						expireDate,billDate,orderStatus
					FROM subscription_cust_order_via SCO,subscription_transaction_via T WHERE SCO.viaid=T.customer_id AND
					 (DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.billDate,'%Y-%m-%d')
					 or DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.startDate,'%Y-%m-%d'))
					 and SCO.viaid=".$transaction['customer_id']." GROUP BY SCO.orderNumber ORDER BY SCO.orderNumber DESC" ;
						$resGetTransDetails_amount=exec_query($sqlGetTransDetails_amount,1);
						if(empty($resGetTransDetails_amount))
						{
								foreach($transaction as $k=>$v)
								{
									$arr[$index][$k]=$v;
								}
								$index++;
						}
	}

}

csv_header("VIA-active-subscription-refund-05/01/2011-05-31-2012.xls");
$datestr="%m/%d/%Y";
data2csv($arr);


?>
