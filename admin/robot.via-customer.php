<?php
set_time_limit(3600*60);//1 hour
global $D_R;
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/config/_via_config.php");
$objVia= new Via();

if($_SERVER['SERVER_NAME']=="admin.minyanville.com"){
	$i=0;
	for($i=0;$i<1000000;$i=$i+10000){
		$customer=$objVia->getCustomerDetail($i,$i+10000);
		//htmlprint_r($customer);exit;
		foreach($customer as $key=>$val){
			$params['customer_id'] = $val['customer_id'];
			$params['user_code'] = $val['user_code'];
			$params['creation_date'] = $val['creation_date'];
			$params['lname'] = $val['lname'];
			$params['fname'] = $val['fname'];
			$params['salutation']  = $val['salutation'];
			$params['email'] = $val['email'];
			$params['credit_status']  = $val['credit_status'];
			$params['list_rental_category']  = $val['list_rental_category'];
			$params['default_bill_to_customer_id']  = $val['default_bill_to_customer_id'];
			$params['def_bill_to_cust_addr_seq'] = $val['def_bill_to_cust_addr_seq'];
			$params['default_renew_to_customer_id']  = $val['default_renew_to_customer_id'];
			$params['def_renew_to_cust_addr_seq']  = $val['def_renew_to_cust_addr_seq'];
			$params['old_customer_id']  = $val['old_customer_id'];
			$params['inactive']  = $val['inactive'];
			$params['email_authorization']  = $val['email_authorization'];
			$params['prospect_only']  = $val['prospect_only'];
			$params['zzaux_account_activated']  = $val['zzaux_account_activated'];

			$id = insert_query('via_customer',$params);
			if($id){
				echo $val['customer_id'].' has been inserted successfully';
			}
		}
	}
}else{
	echo "Please run this script from admin server.";
}

?>