<?php
global $D_R;
include_once($D_R."/lib/_includes.php");
include_once("$D_R/lib/json.php");
session_start();
$json = new Services_JSON();

set_sess_vars("new_register_billing",$_POST);
if(count($_SESSION['viacart']['SUBSCRIPTION'])>0)
{	
	if(isset($_SESSION['SID']))
	{
	$user_id = $_SESSION['SID'];
	}
	else
	{
		$user_id = $_POST['session_id'];
	}
	$params['firstname'] = $_POST['firstname'];
	$params['lastname'] = $_POST['lastname'];
	$params['email'] = $_POST['email'];
	$params['user_id'] = $user_id;
	$params['address1'] = $_POST['address1'];
	$params['address2'] = $_POST['address2'];
	$params['city'] = $_POST['city'];
	$params['state'] = $_POST['state'];
	$params['zipcode'] = $_POST['zipcode'];
	$params['country'] = $_POST['country'];
	$params['phone'] = $_POST['phone'];
	/*$params['cc_type'] = $_POST['cctype'];
	$params['cc_num'] = $_POST['ccnum'];
	$params['cc_expire'] = $_POST['year'].'-'.$_POST['month'];
	$params['cc_cvv2'] = $_POST['cvvnum'];*/
	$params['last_step_performed'] = "Step 2";
	$params['product_id'] = $_POST['product_id'];
	if($params['country']=='AA'){
		$params['country']='USA';
	}
	$condition['user_id'] = $user_id;
	$params['date']=mysqlNow();
	$id = insert_or_update('new_registeration',$params,$condition);
	
	if(!isset($_SESSION['SID']))
	{
		unset($_SESSION['new_register_billing']);
	
	}
	
	$value=array(
			'status'=>true,
			'msg'=>'Success'
		);
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	
}
else
{
	$value=array(
			'status'=>false,
			'msg'=>'No product in cart.'
		);
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
}
?>