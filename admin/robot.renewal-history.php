<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/config/_via_config.php");
$objVia= new Via();

if($_SERVER['SERVER_NAME']=="admin.minyanville.com"){
	$i=0;
	for($i=0;$i<1000000;$i=$i+10000){
		$renewalData=$objVia->getRenewalHistoryDetail($i,$i+10000);
		//htmlprint_r($renewalData);exit;
		foreach($renewalData as $key=>$val){
			$params['subscrip_id'] = $val['subscrip_id'];
			$params['renewal_history_seq'] = $val['renewal_history_seq'];
			$params['job_id'] = $val['job_id'];
			$params['currency'] = $val['currency'];
			$params['customer_id'] = $val['customer_id'];
			$params['effort_number']  = $val['effort_number'];
			$params['source_code_id'] = $val['source_code_id'];
			$params['orderhdr_id']  = $val['orderhdr_id'];
			$params['order_item_seq']  = $val['order_item_seq'];

			$id = insert_query('via_renewal_history',$params);
			if($id){
				echo $val['subscrip_id'].' has been inserted successfully';
			}
		}
	}
}else{
	echo "Please run this script from admin server.";
}

?>