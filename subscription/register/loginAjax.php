<?php
session_start();
global $D_R,$HTPFX,$HTHOST,$globalPwd,$REG_EML_REPLYTO;
	include_once($D_R."/lib/_user_data_lib.php");
	include_once($D_R."/lib/_user_controller_lib.php");
	include_once($D_R."/lib/_user_exceptionhandler.php");
	require_once($D_R."/lib/recurly/recurly.php");
	include_once("$D_R/lib/json.php");
	include_once($D_R."/lib/config/_recurly_config.php");
	include_once ($D_R."/lib/recurly/_recurly_data_lib.php");
	include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
	Recurly_js::$privateKey = $privateKey;
	Recurly_Client::$apiKey = $recurlyApiKey;

	$json = new Services_JSON();
	/* Via Server Exception handling - if Via Server is Down or Under maintennce*/

	$getType=$_REQUEST['type'];

	// Case to check user exists
	// Case to check user login
	if((isset($getType))&& ($getType=='login'))
	{
		if(isset($_POST['login']) && (!isset($_POST['checkUser'])))
		{
			if((!$_POST['login']) || (!$_POST['pwd']))
			{
				echo "Invalid username or password";
				exit;
			}
			$loginSystem = new user(); // _user_conroller_lib.php
			$isLoggedIn=false;
			if($_POST['autologin']){
				$autologin=1;
			}else{
				$autologin=0;
			}

			$password=trim(stripslashes($_POST['pwd'])); // password may contain special characters so we are using stipslashes()
			$isLoggedIn=$loginSystem->login($_POST['login'],$password,$autologin);
			
			if($isLoggedIn=='true')
			{
				$value=array('status'=>true,'msg'=>'');
				/* --------- Set domainCookie used for Magnify SSO -----------------  */
				if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
				{
					domaincookie("sharedUserId",trim($_SESSION['SID']));
					//domaincookie("LogInUserId",session_id());
				}
				if(isset($_SESSION['AdsFree'])  && $_SESSION['AdsFree']=='1')
				{
					domaincookie("sharedAdsFreeFlag",'1');
				}
				/*-----------------------  Magnify SSO domain Cookie end ------------------ */
			}
			else
			{
				if($isLoggedIn=='blocked') // Condition for blocked user
				{
					$mesasg ="Your Minyanville services are blocked. Please contact at support@minyanville.com";
				}else{
						$mesasg="Invalid username or password";
				}
				$value=array('status'=>false,'msg'=>$mesasg);
			}

			$output = $json->encode($value);
			echo strip_tags($output);
		}
	}
	else if((isset($getType))&& ($getType=='logout'))
	{
		$loginSystem = new user(); 							// user class _via_conroller_lib.php
		$loginSystem->logout();
		unset($_COOKIE['sharedUserId']);
		unset($_COOKIE['sharedAdsFreeFlag']);
		header("location:".$_SERVER['HTTP_REFERER']);

	}
	else if((isset($getType))&& ($getType=='registration'))
	{
		$isLoggedIn=false;
		$recurlyData = new recurlyData();

		$subId = $_POST['subid'];
		$email=$_POST['funnelEmail'];
		$firstName = $_POST['funnelFirstName'];
		$lastName=$_POST['funnelLastName'];
		$address = $_POST['funnelAddress'];
		$country=$_POST['funnelCountry'];
		$city = $_POST['funnelCity'];
		$state=$_POST['funnelState'];
		$zip = $_POST['funnelZip'];
		$phoneNum=$_POST['funnelPhone'];
		$cardType = $_POST['funnelCCtype'];
		$cardNum=$_POST['funnelCCnum'];
		$expMnth = $_POST['funnelExpMnth'];
		$expYr=$_POST['funnelExpYr'];
		$cvv = $_POST['funnelcvv'];
		$couponCode=$_POST['funnelCouponCode'];
		$planCode = $_POST['funnelPlanCode'];
		$planGroup = $_POST['funnelPlanGroup'];
		$pref = $_POST['pref'];

		$result = $recurlyData->productAdd($subId, $firstName, $lastName, $email, $address, $country, $city, $state, $zip, $phoneNum, $cardType, $cardNum, $expMnth, $expYr, $cvv, $couponCode, $planCode, $planGroup,$pref);
		if($result['status']==true){
			$value=array('status'=>true);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}else{
			$value=array('status'=>false,'id'=>$result['msg']);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}
	}
	else if((isset($getType))&& ($getType=='forgotpassword')){
		$loginSystem = new user(); // _user_conroller_lib.php
		$email = $_POST['uid'];
		$result = $loginSystem->forgotPass($email); // _user_controller_lib.php
		if($result>0){
			$value=array('status'=>true,'id'=>$result);
		}else{
			$value=array('status'=>false);
		}
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;

	}
	else if((isset($getType))&& ($getType=='signUpFreeModule')){
		$email = $_POST['email'];
		$firstName = $_POST['first_name'];
		$lastName = $_POST['last_name'];
		$elliott_check = $_POST['elliott_check'];
		$tchir_check = $_POST['tchir_check'];
		$keene_check = $_POST['keene_check'];
		
		if($elliott_check=="true")
		{
			subscribeMailChimpUser($email,'buzzFreeReport',$firstName,$lastName);
		}
		if($keene_check=="true")
		{
			subscribeMailChimpUser($email,'keeneFreeReport',$firstName,$lastName);
		}
		if($tchir_check=="true")
		{
			subscribeMailChimpUser($email,'tchirFreeReport',$firstName,$lastName);
		}
		$value=array('status'=>true);
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	}
	else if((isset($getType))&& ($getType=='sendmail')){
		$loginSystem = new user(); // _user_conroller_lib.php
		$email=trim($_POST['uid']);
		$id = trim($_POST['subId']);
		$loginSystem->sendForgotPasswordMail($email,$id);
		echo "Your password has been sent.";

		/*$value=array('status'=>true,'msg'=>$msg);
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;*/

	}
	else if((isset($getType))&& ($getType=='newSubscription'))
	{
		$token = $_POST['token'];
		$recentPlanDetail = Recurly_js::fetch($token);
		$planCharge = $recentPlanDetail->unit_amount_in_cents;
		$planCode = $recentPlanDetail->plan->plan_code;
		$objFunnelData = new registrationFunnelData();
		$planDetail = $objFunnelData->getPlanDetails($planCode);
		if($planCharge>0){
			$planCharge = $planCharge/100;
		}
        if($_SESSION['welcomeVisitCount']>1){
			unset($_SESSION['recently_added']);
        }
	    $_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $recentPlanDetail->plan->plan_code;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['expireDate'] = $recentPlanDetail->trial_ends_at->date;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['description'] = $recentPlanDetail->plan->name;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderNumber'] = $recentPlanDetail->uuid;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = $recentPlanDetail->coupon_code;
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];

		$_SESSION['products']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $recentPlanDetail->plan->plan_code;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['expireDate'] = $recentPlanDetail->trial_ends_at->date;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['description'] = $recentPlanDetail->plan->name;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderNumber'] = $recentPlanDetail->uuid;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = $recentPlanDetail->coupon_code;
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
		$_SESSION['products']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];

		$_SESSION['recentPlanCode']='';
		$_SESSION['recentPlanCode'] = $recentPlanDetail->plan->plan_code;


		$objUser = new user;
		$productstatusarray = $objUser->getSubcriptionProductDetails($_SESSION['SID']);
		if($productstatusarray['Buzz']==1){
	    	$countBuzzContributor=$objUser->countBuzzSubscriber();
			if($countBuzzContributor==1){
				set_sess_vars("buzzSubscriberContributor","1");
			}
		}
		if(is_array($productstatusarray))
		{
			foreach($productstatusarray as $key => $value)
			{
				set_sess_vars($key,$value);

			}
		}
		$billingInfo = Recurly_BillingInfo::get($_SESSION['SID']);
		$objUserData = new userData;
		$objUserData->addGATrans($recentPlanDetail->uuid,$recentPlanDetail->plan->plan_code,$recentPlanDetail->plan->name,$planCharge,$billingInfo->city,$billingInfo->state,$billingInfo->country);
		if($recentPlanDetail->plan->plan_code!=''){
			$val['status'] = '1';
			$val['planCode'] = $recentPlanDetail->plan->plan_code;
		}else{
			$val['status'] = '0';
		}
		$output = $json->encode($val);
		echo strip_tags($output);
		exit;
	}
	else if((isset($getType))&& $getType=='articletopic_newsletter'){
		include_once($D_R."/lib/config/_email_config.php");
		include_once("$D_R/lib/_layout_data_lib.php");
		global $dailyDigestPassword;

		$feed_email = $_POST['checkemail'];
		$fuser_id = $_POST['sessuserid'];
		$section_id = $_POST['section_id'];
		$section_name = $_POST['section_name'];

	    $qry = "select id from subscription where email like '$feed_email'";
		$result=exec_query($qry,1);
		$fuser_id = $result['id'];
		if($fuser_id != '') {

			$user_type = 'olduser';
			update_email_topic_alert($fuser_id,$section_id);
			topic_welcome_email($user_type,$feed_email,$password,$section_name);

			$errMessage='success';
			$value=array('status'=>true,'msg'=>$errMessage);
		}
		else {
		    $user_type = 'newuser';
			$recurlyData = new recurlyData();
			$objFunnelData = new registrationFunnelData();
			$alerts="0";
			$email = $feed_email;
			if(empty($fuser_name))
			{
				$explode_email = explode("@",$email);
				$firstName = $explode_email[0];
			}
			else
			{
				$firstName = $fuser_name;
			}
			$objUser= new userData();
			if($_SESSION['SID']==''){
				$subId=$objFunnelData->getAccountCode();
			}else{
				$subId=$_SESSION['SID'];
			}
			$accountSuccess = $recurlyData->createAccount($subId,$email,$email,$firstName,'','','');
			if ($accountSuccess==1){
				/* After Via Insertion done make insertion in MVIL DB Start  */
			    $userPaswd=$objUser->encryptUserPassword($dailyDigestPassword);
			    $userdata=array('fname'=>$firstName,'email'=>$email,'account_status'=>'enabled','password'=>$userPaswd,'tel'=>$fuser_tel,'id'=>$subId,'recv_daily_gazette'=>$alerts);
			    $insertid = insert_query("subscription",$userdata);
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

					if(($_POST['authorCategories']!='')&&(($_POST['productselected']=='1'))) {
						$authemail_alert=1;
					}else{
						$authemail_alert=0;
					}
				}
				$email_alert ="0";
				$emailalertsArray=array('subscriber_id'=>$subId,'category_ids'=>$catgors,'email_alert'=>$email_alert);
				$authorsrray=array('subscriber_id'=>$subId,'author_id'=>$_POST['authorCategories'],'email_alert'=>$authemail_alert);
				insert_or_update('email_alert_categorysubscribe',$emailalertsArray,array('subscriber_id'=>$insertid));
				insert_or_update('email_alert_authorsubscribe',$authorsrray,array('subscriber_id'=>$insertid));
				insert_email_topic_alert($subId,$section_id);

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				RegisterUser($subId); // defined in _exchange_lib.php

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$subId);
				$profileid = insert_query('ex_user_profile', $subarray);

				$loginSystem = new user(); // _via_conroller_lib.php
				$isLoggedIn=$loginSystem->login($email,$dailyDigestPassword);
				$errMessage='newuser';
				topic_welcome_email($user_type,$feed_email,$dailyDigestPassword,$section_name);
				$value=array('status'=>true,'msg'=>$errMessage);
			}else{
				$errMessage='An error occurred while processing your request. Please check your data.';
				$value=array('status'=>false,'msg'=>$errMessage);
			}
		}
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	}

	else if((isset($getType))&& ($getType=='dailydigest' || $getType=='dailydigest_article')){
		include_once($D_R."/lib/config/_email_config.php");
		include_once("$D_R/lib/_layout_data_lib.php");
		global $D_R,$mailChimpApiKey,$dailyDigestListId,$dailyDigestPassword;

		$feed_email = $_POST['checkemail'];
		$fuser_id = $_POST['sessuserid'];
		$fuser_name = $_POST['name'];
		$trading_radar = $_POST['trading_radar'];
		$daily_digest = $_POST['daily_recap'];
		$fuser_tel = $_POST['phone'];

		$user_type = 'olduser';
		$qry = "select id from subscription where email like '$feed_email'";
		$result=exec_query($qry,1);
		$fuser_id = $result['id'];
		if($fuser_id != '') {

			if($getType=='dailydigest_article')
			{
				if($daily_digest=='true' || $daily_digest=='1')
				{
					updateDailyDigestEmail($fuser_id);
					dailyDigestWelcomeEmail($user_type,$feed_email,$password);
				}
				if($trading_radar=='true' || $trading_radar=='1')
				{
					update_email_category_alert($fuser_id,"5");
				}
			}
			else
			{
				updateDailyDigestEmail($fuser_id);
				dailyDigestWelcomeEmail($user_type,$feed_email,$password);
			}
			$errMessage='success';
			$value=array('status'=>true,'msg'=>$errMessage);
		}
		else {
			$recurlyData = new recurlyData();
			$objFunnelData = new registrationFunnelData();
			if($getType = "dailydigest_article")
			{
				if($daily_digest=='false')
				{ $alerts="0"; }
				else { $alerts="1"; }
			}
			else
			{
			   	$alerts="1";
			}
			$email = $feed_email;
			if(empty($fuser_name))
			{
				$explode_email = explode("@",$email);
				$firstName = $explode_email[0];
			}
			else
			{
				$firstName = $fuser_name;
			}
			$objUser= new userData();
			if($_SESSION['SID']==''){
				$subId=$objFunnelData->getAccountCode();
			}else{
				$subId=$_SESSION['SID'];
			}
			$accountSuccess = $recurlyData->createAccount($subId,$email,$email,$firstName,'','','');
			if ($accountSuccess==1){
				/* After Via Insertion done make insertion in MVIL DB Start  */
			    $userPaswd=$objUser->encryptUserPassword($dailyDigestPassword);
			    $userdata=array('fname'=>$firstName,'email'=>$email,'account_status'=>'enabled','password'=>$userPaswd,'tel'=>$fuser_tel,'id'=>$subId,'recv_daily_gazette'=>$alerts);
			    $insertid = insert_query("subscription",$userdata);
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

					if(($_POST['authorCategories']!='')&&(($_POST['productselected']=='1'))) {
						$authemail_alert=1;
					}else{
						$authemail_alert=0;
					}
				}
				if($trading_radar=='true' || $trading_radar=='1')
				{
					$catgors="5";
					$email_alert ="1";
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

				$loginSystem = new user(); // _via_conroller_lib.php
				$isLoggedIn=$loginSystem->login($email,$dailyDigestPassword);
				$errMessage='newuser';
				/********** Subscribe user in the mailchimp list **********/
				include_once($D_R."/lib/config/_mailchimp_config.php");
				include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
				include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
				$objApi = new MCAPI($mailChimpApiKey);

				$merge_vars = array("FNAME"=>$firstName, "LNAME"=>'');
				try{
					$objApi->listSubscribe($dailyDigestListId, $email, $merge_vars, 'html', false, true, false, false);
				}catch(Exception $e){
					//$from= 'it@minyanville.com';
					$from= 'it@minyanville.com';
					$to = 'anshul.budhiraja@mediaagility.com';
					$subject = 'Daily Digest MailChimp Exception';
	 				$body = var_dump($e);
	 				
	 				mymail($to,$from,$subject,$body);
	
					/*require_once $D_R.'/lib/swift/lib/swift_required.php';
					$mailer = Swift_MailTransport::newInstance();
					$message = Swift_Message::newInstance();
					$message->setSubject($subject);
					$message->setBody($body);
	       			$message->setFrom($from);
	       			$message->setTo($to);
	       			$mailer->send($message);*/
				}
				
				dailyDigestWelcomeEmail($errMessage,$email,$userPaswd);
				$value=array('status'=>true,'msg'=>$errMessage);
			}else{
				$errMessage='An error occurred while processing your request. Please check your data.';
				$value=array('status'=>false,'msg'=>$errMessage);
			}
		}
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	}
		else if((isset($getType))&& ($getType=='applyCoupon')){
		$couponCode = $_POST['couponCode'];
		$planCode = $_POST['planCode'];
		$price = $_POST['funnelPrice'];
		$objrecurlyData = new recurlyData;

		try {
			$coupon = Recurly_Coupon::get($couponCode);
			if($coupon->state=="redeemable"){
				$useIt = 1;
				if($coupon->applies_to_all_plans==''){
					if(in_array($planCode,$coupon->plan_codes)){
						$useIt = 1;
					}else{
						$useIt = 0;
					}
				}
				if($useIt=='1'){
					if($price=='0'){
						$payableAmt = '0.00';
					}else{
						if($coupon->discount_type=="percent"){
							$discountAmt = $coupon->discount_percent;
							$payableAmt = $price-($price*($discountAmt/100));
							if($payableAmt=='0'){
								$payableAmt = '0.00';
							}
						}elseif ($coupon->discount_type=="dollars"){
							$discountAmt = $coupon->discount_in_cents['USD']->amount_in_cents;
							$priceInCents = intval($price*100);
							$payableAmt = ($priceInCents-$discountAmt)/100;		//convert from cents to dollars
							$payableAmt = $payableAmt.'.00';
						}
					}
					$msg = $payableAmt;
					$value=array('status'=>true,'msg'=>$msg);
				}else{
					$msg = 'Coupon Not Valid on this Plan.';
					$value=array('status'=>false,'msg'=>$msg);
				}
			}else{
				$msg = 'Coupon has been expired.';
				$value=array('status'=>false,'msg'=>$msg);
			}
		} catch (Recurly_NotFoundError $e) {
			$msg = 'Coupon does not exist.';
			$value=array('status'=>false,'msg'=>$msg);
		}
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	}


?>
