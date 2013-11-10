<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/config/_via_config.php");
$objVia= new Via();

if($_SERVER['SERVER_NAME']=="admin.minyanville.com"){
	$i=0;
	for($i=0;$i<1000000;$i=$i+10000){
		$subDef=$objVia->getSubscriptionDefViaDetail($i,$i+10000);
		//htmlprint_r($subDef);exit;
		foreach($subDef as $key=>$val){
			$params['subscription_def_id'] = $val['subscription_def_id'];
			$params['subscription_def'] = $val['subscription_def'];
			$params['term_id'] = $val['term_id'];
			$params['oc_id'] = $val['oc_id'];
			$params['rate_class_id'] = $val['rate_class_id'];
			$params['description'] = $val['description'];
			$params['renewal_card_id'] = $val['renewal_card_id'];
			$params['order_code_id'] = $val['order_code_id'];
			$params['allow_on_internet'] = $val['allow_on_internet'];
			$params['expire_gap'] = $val['expire_gap'];
			$params['logical_start'] = $val['logical_start'];

			$id = insert_query('via_subscription_def',$params);
			if($id){
				echo $val['subscription_def_id'].' has been inserted successfully';
			}
		}
	}
}else{
	echo "Please run this script from admin server.";
}

?>