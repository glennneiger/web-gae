<?php
global $D_R;
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
include_once($D_R."/lib/_user_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/recurly/recurly.php");
global $recurlyApiKey;
Recurly_Client::$apiKey = $recurlyApiKey;
$objUser= new userData();
$objRecurly = new recurlyData();
$objFunnelData = new registrationFunnelData();

if(!empty($_POST)){
	if($_POST['id']==''){
		$chkDupEmail="select id from subscription where email='".$_POST['email']."'";
		$res = exec_query($chkDupEmail,1);
		if($res['id']!=''){
			$str = 'Email Already Exists';
		}else{
			$subId = $accountCode=$objFunnelData->getAccountCode();
			$params['id'] = $subId;
			$params['password'] = $objUser->encryptUserPassword($_POST['password']);
			$params['date']=time();
			$params['prefix'] = $_POST['prefix'];
			$params['account_status'] = 'enabled';
			$params['fname'] = $_POST['fname'];
			$params['lname'] = $_POST['lname'];
			$params['email'] = $_POST['email'];
			$params['tel'] = $_POST['phone'];

			/* After Via Insertion done make insertion in MVIL DB Start  */
			$insertid = insert_query("subscription",$params);
			if(isset($insertid))
			{
				/*
				After insertion in subscription table insert the data into
				1. email_alert_categorysubscribe
				2. email_alert_authorsubscribe
				3. ex_user_profile
				4. ex_user_email_settings
				5. ex_profile_privacy
				RegisterUser($subid)
				tables then go for login
				*/

				if(($_POST['authorCategories']!='')&&(($_POST['productselected']=='1'))){
					$authemail_alert=1;
				}else{
					$authemail_alert=0;
				}
				//Add in Recurly
				$success = $objRecurly->createAccount($subId,$_POST['email'],$_POST['email'],$_POST['fname'],$_POST['lname'],'','');
			}
			$emailalertsArray=array('subscriber_id'=>$subId,'category_ids'=>$catgors,'email_alert'=>$email_alert);
			$authorsrray=array('subscriber_id'=>$subId,'author_id'=>$_POST['authorCategories'],'email_alert'=>$authemail_alert);

			insert_or_update('email_alert_categorysubscribe',$emailalertsArray,array('subscriber_id'=>$insertid));
			insert_or_update('email_alert_authorsubscribe',$authorsrray,array('subscriber_id'=>$insertid));

			/* Insert into ex_user_email_settings + ex_profile_privacy tables */
			RegisterUser($subId); // defined in _exchange_lib.php

			/* Insert into ex_user_profile table */
			$subarray = array('subscription_id'=>$subId);
			$profileid = insert_query('ex_user_profile', $subarray);

			$str = 'User has been Added Successfully.';
		}
	}else{
		//$params['id'] = $_POST['id'];
		$params['password'] = $objUser->encryptUserPassword($_POST['password']);
		$params['prefix'] = $_POST['prefix'];
		$params['modified']=time();
		$params['account_status'] = 'enabled';
		$params['fname'] = $_POST['fname'];
		$params['lname'] = $_POST['lname'];
		$params['email'] = $_POST['email'];
		$params['tel'] = $_POST['phone'];

		$update = update_query("subscription",$params,array(id=>$_POST['id']));
		$objRecurly->getAccount($_POST['id'],$_POST['email'],$_POST['email'],$_POST['fname'],$_POST['lname'],'','');

		$str = 'Record has been updated Successfully.';
	}
}

if($subId==''){
	$subId = $_POST['id'];
}

$bounceback = './adduser.htm';

location($bounceback.qsa(array(id=>$subId,error=>$str)));


?>