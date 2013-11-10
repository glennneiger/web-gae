<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/config/_via_config.php");
$objVia= new Via();

if($_SERVER['SERVER_NAME']=="admin.minyanville.com"){
	$i=0;
	for($i=0;$i<1000000;$i=$i+10000){
		$customerAddress=$objVia->getCustomerAddresDetail($i,$i+10000);
		//htmlprint_r($customerAddress);exit;
		foreach($customerAddress as $key=>$val){
			$params['customer_id'] = $val['customer_id'];
			$params['customer_address_seq'] = $val['customer_address_seq'];
			$params['state'] = $val['state'];
			$params['address_type'] = $val['address_type'];
			$params['city'] = $val['city'];
			$params['zip']  = $val['zip'];
			$params['email'] = $val['email'];
			$params['fname']  = $val['fname'];
			$params['lname']  = $val['lname'];
			$params['salutation']  = $val['salutation'];
			$params['address_status'] = $val['address_status'];
			$params['audit_name_change']  = $val['audit_name_change'];
			$params['audit_title_change']  = $val['audit_title_change'];
			$params['supp_clean']  = $val['supp_clean'];
			$params['address1']  = $val['address1'];
			$params['address2']  = $val['address2'];
			$params['special_tax_id']  = $val['special_tax_id'];
			$params['future_temp_exists']  = $val['future_temp_exists'];

			$id = insert_query('via_customer_address',$params);
			if($id){
				echo $val['customer_id'].' has been inserted successfully';
			}
		}
	}
}else{
	echo "Please run this script from admin server.";
}

?>