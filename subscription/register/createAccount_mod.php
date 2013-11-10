<?php
global $D_R;
include_once($D_R."/lib/_includes.php");
session_start();

if($_POST['firstname'] == "")
{
$_POST['firstname'] = $_SESSION['nameFirst'];
}

if($_POST['lastname'] == "")
{
$_POST['lastname'] = $_SESSION['nameLast'];
}
if($_POST['viauserid'] == "")
{
$_POST['viauserid'] = $_SESSION['email'];
}

set_sess_vars("new_register_account",$_POST);

$user_id = $_SESSION['SID'];

$params['user_id'] = $user_id;
$params['email'] = $_POST['viauserid'];
$params['firstname'] = $_POST['firstname'];
$params['lastname'] = $_POST['lastname'];
//$params['password'] = $_POST['viapass'];
//$params['new_password'] = $_POST['viarepass'];
$params['remember_me'] = $_POST['remember_me'];
$params['alerts'] = $_POST['alerts'];
$params['dailyfeed'] = $_POST['dailyfeed'];
$params['terms'] = $_POST['terms'];
$params['last_step_performed'] = "Step 1";
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
?>