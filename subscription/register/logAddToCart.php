<?php
global $D_R;
session_start();
$user_id = $_SESSION['SID'];
if($user_id){
	$params['user_id'] = $user_id;
	$params['email'] = $_SESSION['EMAIL'];
	$params['firstname'] = $_SESSION['nameFirst'];
	$params['lastname'] = $_SESSION['nameLast'];
	$params['last_step_performed'] = "Add to cart";
	if(isset($_SESSION['viacart']))
	{
		$arProduct = array();
		foreach($_SESSION['viacart']['SUBSCRIPTION'] as $arVal)
		{
			$arProduct[] = $arVal['subscription_def_id'];
		}
	}

	$params['product_id'] = implode(",",$arProduct);
	$condition['user_id'] = $user_id;
	$params['date']=mysqlNow();
	$id = insert_or_update('new_registeration',$params,$condition);
}
echo $_POST['url'];
?>