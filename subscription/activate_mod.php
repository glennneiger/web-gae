<?php
header("Pragma: nocache\n");
header("cache-control: no-cache, must-revalidate, no-store\n\n");
header("Expires: 0");
session_start();
	$ViaEmail= new ViaEmail();
	$objVia=new Via();
	$errorObj=new ViaException();
	$email=$_POST['email'];
	$notactivatedviaid=$_SESSION['via_inactive_id'];
	$result=getUserDetailsByViaid($notactivatedviaid);
	$objVia->customerIdVia=$result['via_id'];
	

$loginInfo=array();
$loginInfo['login']=$email;
// $custInfo=array('loginInfo'=>$loginInfo,'email'=>$email,'auxFields'=>$field);
$custInfo=array('loginInfo'=>$loginInfo,'email'=>$email);
	$customerDetails=$objVia->updateCustomer($custInfo);
	if($customerDetails!='true'){
		$errMessage=$errorObj->getExactCustomError($customerDetails);
		if($errMessage==''){
			$pattern = '/Error:(.*)/';
			preg_match($pattern, $errViaMessage, $matches);
			$errMessage=$matches[1];
			echo $errMessage;
		}
		if($errMessage==''){
			echo 'An error occurred while processing your request. Please check your data.';
		}
	}else{
	
		 			// update customer in minyanville db
			// defined in /lib/_via_data_lib.php
			$userDetails['email']=$email;
			$objVia->updateUserDetails($result['via_id'],$userDetails);
			$fname=$result['fname'];
			$viaid=$result['via_id'];
			$ViaEmail->sendSoftTrialActivationEmail($fname,$viaid,$email,$activate='',$dfuser);
			$activateemail='"'. $email.'"';
			echo '<div id="showactivatemsg" style="display:block; color:#000000;">Activation email sent to '.$activateemail.'</div>';
	}
?>