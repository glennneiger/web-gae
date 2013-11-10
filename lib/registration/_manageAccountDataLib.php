<?php
class manageAccountDataLib{

	function getUserInfo($id){
		$sql="select password,address,address2,city,state,zip,country,tel from subscription where id='".$id."'";
		$getResult=exec_query($sql,1);
		if($getResult){
		    return $getResult;
		}else{
		    return false;
		}


	}

	function checkUserLogin(){
		if(!$_SESSION['SID']){
			Header( "Location: ".$HTPFX.$HTHOST."/subscription/register/login.htm" );
			exit;
		}

	}

	function getManageAccountBillingInfo(){
		try {
	    	$billing_info = Recurly_BillingInfo::get($_SESSION['SID']);
	    	return $billing_info;
		}catch (Exception $e){
			return;
		}
	}


	function checkExistingEmail(){

	}

}
?>