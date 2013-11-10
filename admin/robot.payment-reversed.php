<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/config/_via_config.php");
$objVia= new Via();

if($_SERVER['SERVER_NAME']=="admin.minyanville.com"){
	$i=0;
	for($i=0;$i<1000000;$i=$i+10000){
		$userTransDetails=$objVia->getPaymentReversedViaDetail($i,$i+10000);
		foreach($userTransDetails as $key=>$val){
			$params['original_customer_id'] = $val['original_customer_id'];
			$params['original_payment_seq'] = $val['original_payment_seq'];
			$params['reversed_customer_id'] = $val['reversed_customer_id'];
			$params['reversed_payment_seq'] = $val['reversed_payment_seq'];

			$id = insert_query('via_payment_reversed_payment',$params);
			if($id){
				echo $val['original_customer_id'].' has been inserted successfully';
			}
		}
	}
}else{
	echo "Please run this script from admin server.";
}
?>