<?
   	   include_once("$D_R/lib/email_alert/_lib.php");

   	   $json = new Services_JSON();
	   $userObj = new user();
	   $unsub_email = $_POST['checkemail'];
	   $fieldsArray['customerLogin']=$unsub_email;

	   // function is defined in class user and script /lib/_via_controller_lib.php
	   $userExistanceStatus=$userObj->checkUserAvailablity($fieldsArray['customerLogin']);
	   if($userExistanceStatus !=''){

					unSubscribe($unsub_email);
	   				$errMessage='You have sucessfully unsubscribed from Minyanville Newsletters.';
					$value=array('status'=>true,'msg'=>$errMessage);
				}
		else{
				$errMessage='Email address not found.';

				$value=array('status'=>false,'msg'=>$errMessage);
		}
		$output = $json->encode($value);
		echo strip_tags($output);

?>