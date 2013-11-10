<?php
class registrationFunnelData{
	function getPlanDetails($planCode) {
		$qryPlanDetail = "SELECT id AS plan_id,recurly_plan_charge AS
price,recurly_plan_desc AS plan_name,recurly_plan_promotional_headline AS plan_promotional_headline,recurly_plan_feature_headline AS plan_feature_headline,recurly_plan_promotional_features AS plan_promotional_features, recurly_plan_promotional_desc AS plan_promotional_desc,subGroup AS plan_group, recurly_plan_code, recurly_plan_charge, recurly_plan_setup_fee, recurly_plan_free_trial, recurly_plan_period,subType, recurly_plan_type FROM product WHERE recurly_plan_code='$planCode' and is_active='1'";
		$resPlanDetail = exec_query($qryPlanDetail,1);
		return $resPlanDetail;
	}

	function getAccountCode(){
		$accountCode = rand(60000,9999999999);
		$qryChkAccount = "SELECT id,fname,lname,email FROM subscription WHERE id='$accountCode'";
		$resChkAccount = exec_query($qryChkAccount,1);
		if(empty($resChkAccount)){
			return $accountCode;
		}else{
			$this->getAccountCode();
		}
	}

	function getCurrentProducts($subId){
		/*$existingProducts = $_SESSION['activeProducts'];
		if($existingProducts==''){
			if(is_array($_SESSION['products']['SUBSCRIPTION'])){
				$currentProducts = array_keys($_SESSION['products']['SUBSCRIPTION']);
				$currentProductCode = implode(",", $currentProducts);
				$existingProductKey = str_replace(",","','",$currentProductCode);
				$productKey = "'".$existingProductKey."'";
			}
		}else{
			$existingProductKey = str_replace("-","','",$existingProducts);
			$productKey = substr($existingProductKey,2,-2);
		}
		$qryProductList="SELECT subGroup,recurly_plan_code,subscription_def_id,product FROM product WHERE (recurly_plan_code IN (".$productKey.") OR subscription_def_id IN (".$productKey.")) AND subGroup IS NOT NULL GROUP BY subGroup";*/
		$qryProductList="SELECT  P.subGroup,SCO.recurly_plan_code,P.product FROM subscription_cust_order SCO, product P WHERE SCO.subscription_id='".$subId."' AND ((SCO.typeSpecificId=P.subscription_def_id) or (SCO.recurly_plan_code=P.recurly_plan_code)) and (((SCO.expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00') AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')) OR (SCO.recurly_state<>'expire' or SCO.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
		$getResult=exec_query($qryProductList);
		if(!empty($getResult)){
			return $getResult;
		}else{
			return false;
		}
	}

	function getProductData($product)
	{
		//$qryGetProduct = "SELECT * FROM `recurly_plans` WHERE `plan_group`='$product'";
		$qryGetProduct = "SELECT id AS plan_id,recurly_plan_code AS plan_code,recurly_plan_promotional_headline AS plan_promotional_headline,recurly_plan_feature_headline AS plan_feature_headline,recurly_plan_promotional_features AS plan_promotional_features, recurly_plan_promotional_desc AS plan_promotional_desc,subGroup AS plan_group FROM product WHERE subGroup='$product' AND recurly_plan_code<>''";
		$resProduct = exec_query($qryGetProduct,1);
		if(!empty($resProduct)){
			return $resProduct;
		}else{
			return false;
		}
	}

	function getUrlFromPageName($pageName){
		$qryUrl = "SELECT lp.id,lp.name,lp.url FROM `layout_pages` lp WHERE name='".$pageName."'";
		$resUrl = exec_query($qryUrl,1);

		if(!empty($resUrl)){
			return $resUrl['url'];;
		}else{
			return false;
		}
	}

	function getAllPlanCodeOfProduct($product){
		$qryCode = "SELECT recurly_plan_code FROM product WHERE subGroup='".$product."'";
		$resCode = exec_query($qryCode);

		if(!empty($resCode)){
			return $resCode;
		}else{
			return false;
		}
	}

	function getEnumOptions($columnName){
		$qryCol = "SHOW COLUMNS FROM product LIKE '".$columnName."'";
		$getCol = exec_query($qryCol);
		$getCol = substr($getCol['0']['Type'],5,-1);
		$getCol = str_replace("'","",$getCol);
		$getEnumValues = explode(',',$getCol);

		if(!empty($getEnumValues)){
			return $getEnumValues;
		}else{
			return false;
		}
	}

	function setTrackingPixel($planCode,$productName,$userId,$url,$refererUrl,$utmSource,$utmMedium,$utmTerm,$utmContent,$utmCampaign,$pageName){
		if(!empty($planCode)){
			$qry="select subType,recurly_plan_type,recurly_plan_free_trial,subGroup from product where recurly_plan_code='".$planCode."'";

			$getResult=exec_query($qry,1);
			if(is_array($getResult)){
			    $productTerm=$getResult['subType'];
			    $productType=$getResult['recurly_plan_type'];
			    if($getResult['recurly_plan_free_trial']=="14 days"){
				$trialType="FT";
			    }elseif($getResult['recurly_plan_free_trial']=="No Trial"){
				$trialType="NFT";
			    }
			    $productName=$getResult['subGroup'];
			}
		    }

		    $params['subscription_id']=$_SESSION['SID'];
		    $params['productTerm']=$productTerm;
		    $params['productType']=$productType;
		    $params['trialType']=$trialType;
		    $params['userid']=$userId;
		    $params['planCode']=$planCode;
		    $params['url']=$url;
		    $params['refererUrl']=$refererUrl;
		    $params['productName']=$productName;
		    $params['utmSource']=$utmSource;
		    $params['utmMedium']=$utmMedium;
		    $params['utmTerm']=$utmTerm;
		    $params['utmContent']=$utmContent;
		    $params['utmCampaign']=$utmCampaign;
		    $params['pageName']=$pageName;

		    $id=insert_query('registrationTrackingPixel',$params,$safe=0);
		    if(!empty($id)){
			return $id;
		    }
	}


	function setSoftTrialUserRegistration($email,$firstName,$lastName,$phone,$planCode){
		//$objRegisterFunnel= new registrationFunnelData();
		global $viaProductsName;
		$objRecurlyData= new recurlyData();
		$objUserData= new userData();
		$checkUser=$objUserData->checkUserViaAvailibilityByEmail($email);
		if(empty($checkUser)){
			$password = rand(100000, 99999999);
		        $userPassword=$objUserData->encryptUserPassword($password);
			$accountCode=$this->getAccountCode();
			$params['id']=$accountCode;
			$params['email']=$email;
			$params['account_status']='enabled';
			$params['password']=$userPassword;
			$params['fname']=$firstName;
			$params['lname']=$lastName;
			$params['tel']=$phone;
			$id=insert_query('subscription',$params,$safe=0);
			if(!empty($id)){
				$email_alert=0;
				$authemail_alert=0;
				$emailalertsArray=array('subscriber_id'=>$accountCode,'email_alert'=>$email_alert);
				$authorsrray=array('subscriber_id'=>$accountCode,'email_alert'=>$authemail_alert);
				insert_or_update('email_alert_categorysubscribe',$emailalertsArray,array('subscriber_id'=>$accountCode));
				insert_or_update('email_alert_authorsubscribe',$authorsrray,array('subscriber_id'=>$accountCode));

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				RegisterUser($accountCode); // defined in _exchange_lib.php

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$accountCode);
				$profileid = insert_query('ex_user_profile', $subarray);

				/*add user in recurly*/
				$setUserRecurly=$objRecurlyData->getAccount($accountCode,$email,$email,$firstName,$lastName,$companyName,$viaId);
				/*Add softtrial plan*/
				$insId=$this->setSoftTrialUserPlans($accountCode,$planCode,$email,$password,$firstName,$lastName);
			}
		}else{

			$accountCode=$checkUser;
			$qry="SELECT  P.subGroup
			FROM subscription_cust_order SCO, product P
			WHERE SCO.subscription_id='".$accountCode."' AND
			((SCO.typeSpecificId=P.subscription_def_id) or (SCO.recurly_plan_code=P.recurly_plan_code)) and
			(((SCO.expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00') AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))
			OR (SCO.recurly_current_period_ends_at >= DATE_FORMAT(NOW(),'%Y-%m-%d'))
AND SCO.recurly_state ='active') AND SCO.recurly_current_period_ends_at IS NOT NULL";

			$getResult=exec_query($qry);
			$getProductName="select subGroup from product where recurly_plan_code='".$planCode."'";
			$getResultPrductName=exec_query($getProductName,1);
			$productName=$getResultPrductName['subGroup'];
			foreach($getResult as $key=>$value){
				if($value['subGroup']==$productName){
					$chkProduct=1;
				}
			}
			if(empty($chkProduct)){ /*user not subscribed to any Buzz and Banter Plan*/
				/*Add softtrial plan*/
				$passQry="select password,fname,lname from subscription where id='".$accountCode."'";
				$getResultPass=exec_query($passQry,1);
				$userPaswd=$getResultPass['password'];
				if(empty($userPaswd)){
					$password = rand(100000, 99999999);
					$setPassword=$this->setCustomerBasicInfo($email,$password,$accountCode,$firstName,$lastName,$phone);
				}else{
					$setPassword=$this->setCustomerBasicInfo($email,$password,$accountCode,$firstName,$lastName,$phone);
					$password=$objUserData->decryptUserPassword($userPaswd);
				}

				$insId=$this->setSoftTrialUserPlans($accountCode,$planCode,$email,$password,$firstName,$lastName);
			}else{
				$product=strtolower($productName);
				$msg="You are already subscribed to ".$viaProductsName[$product];
				return $msg;

			}
		}

	}

	function setSoftTrialUserPlans($accountCode,$planCode,$email,$password,$firstName,$lastName){
		$getPlanData=$this->getPlanDetails($planCode);
		if(strpos($planCode,'7days')>0){
			$endDate = strtotime("+6 day",strtotime(mysqlNow($date=0)));
		}elseif(strpos($planCode,'60days')>0){
			$endDate = strtotime("+60 day",strtotime(mysqlNow($date=0)));
		}else{
			$endDate = strtotime("+6 day",strtotime(mysqlNow($date=0)));
		}

		$planName=$getPlanData['plan_name'];
		$productName=$getPlanData['plan_group'];
		$paramsCust['subscription_id'] = $accountCode;
		$paramsCust['recurly_plan_code'] = $planCode;
		$paramsCust['recurly_plan_name'] = $planName;
		$paramsCust['recurly_uuid'] = "";
		$paramsCust['recurly_state'] = "active";
		$paramsCust['recurly_quantity'] ="1";
		$paramsCust['recurly_total_amount_in_cents'] ="0.00";
		$paramsCust['recurly_activated_at'] = mysqlNow($date=0);
		$paramsCust['recurly_current_period_started_at'] = mysqlNow($date=0);
		$paramsCust['recurly_current_period_ends_at'] = mysqlNow($endDate);
		$paramsCust['recurly_trial_started_at'] = mysqlNow($date=0);
		$paramsCust['recurly_trial_ends_at'] = mysqlNow($endDate);
		$insertId = insert_query("subscription_cust_order",$paramsCust);
		if(!empty($insertId)){
			$this->softTrialUserLogin($email,$password,$accountCode,$planCode,$endDate,$firstName,$lastName);
			$this->sendSoftTrialUserWelcomeEmail($firstName,$lastName,$email,$password,$productName);
			return $insertId;
		}
	}

	function sendSoftTrialUserWelcomeEmail($firstName,$lastName,$email,$password,$productName){
		global $fromWelcomeEmailSoftTrial,$subjectWelcomeEmailSoftTrial,$tmplWelcomeEmailSoftTrial,$D_R,$HTPFX,$HTHOST,$viaProductsName,$tmplWelcomeEmailTechstratSoftTrial,$tmplWelcomeEmailPeterTchirSoftTrial,$tempWelBuzz60SoftTrial,$subWelBuzz60SoftTrial;

		$user_email =$email;
		if($productName!="peterTchir")
		{
			$productName=strtolower($productName);
		}
	   	$subject= $subjectWelcomeEmailSoftTrial.' '.$viaProductsName[$productName];
	   	$from[$fromWelcomeEmailSoftTrial]=$fromWelcomeEmailSoftTrial;
	   	$userName = $firstName." ".$lastName;
	   	if($productName=="TechStrat Free Week - Email 3/19/2013")
	  	{
	  	 	$welcomeTmpl = $HTPFX.$HTHOST.$tmplWelcomeEmailTechstratSoftTrial;
	  	}
	  	else if($productName=="peterTchir")
	  	{
	  		$welcomeTmpl = $HTPFX.$HTHOST.$tmplWelcomeEmailPeterTchirSoftTrial;
	  	}
	  	elseif($productName=="buzz"){
	  		$subject= $subWelBuzz60SoftTrial.' '.$viaProductsName[$productName];;
			$welcomeTmpl = $HTPFX.$HTHOST.$tempWelBuzz60SoftTrial;
	  	}
	  	else
	  	{
	   		$welcomeTmpl = $HTPFX.$HTHOST.$tmplWelcomeEmailSoftTrial;
	  	}
 		$msgurl=$welcomeTmpl.qsa(array(firstname=>$firstName,email=>$email,password=>$password,product=>$productName));
	  	$mailbody=inc_web($msgurl);
	  	mymail($user_email,$from,$subject,$mailbody);
 		/*require_once $D_R.'/lib/swift/lib/swift_required.php';
		$mailer = Swift_MailTransport::newInstance();
		$message = Swift_Message::newInstance();
		$message->setSubject($subject);
		$message->setBody($mailbody, 'text/html');
		$message->setSender($from);
		$message->setTo($user_email);
		$mailer->send($message);*/
	}

	function softTrialUserLogin($email,$password,$subId,$planCode,$endDate,$firstName,$lastName)
	 {
	 	global $REG_EML_REPLYTO;
	 	$sql="select id,email from subscription where email='".$email."'";
		$chkEmail=exec_query($sql,1);
		if(!empty($chkEmail['email'])){
			$loginSystem = new user();
			$isLoggedIn=$loginSystem->login($email,$password);
			$planCharge = '0';
			$objFunnelData = new registrationFunnelData();
			$planDetail = $objFunnelData->getPlanDetails($planCode);
			$planName=$planDetail['plan_name'];
			if($_SESSION['welcomeVisitCount']>1){
				unset($_SESSION['recently_added']);
			 }
//For welcome user
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $planCode;
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['expireDate'] = $endDate;
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['description'] = $planName;
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderNumber'] = "";
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = "";
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
			$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];
// For product access
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $planCode;
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['expireDate'] = $endDate;
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['description'] = $planName;
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderNumber'] = "";
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = "";
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
			$_SESSION['products']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];

			$_SESSION['recentPlanCode']='';
			$_SESSION['recentPlanCode'] =$planCode;

			$objUser = new user;
			$productstatusarray = $objUser->getSubcriptionProductDetails($subId);
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

			set_sess_vars('nameFirst',$firstName);
			set_sess_vars('nameLast',$lastName);
			$objUserData = new userData;
			$objUserData->addGATrans("",$planCode,$planName,$planCharge,"","","");
		}
	 }

	function chkProductExistence($planCode,$subId){
		$qry="SELECT  P.subGroup FROM subscription_cust_order SCO, product P WHERE SCO.subscription_id='".$subId."' AND ((SCO.typeSpecificId=P.subscription_def_id) or (SCO.recurly_plan_code=P.recurly_plan_code)) and (((SCO.expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00') AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')) OR (SCO.recurly_state<>'expired' AND SCO.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
		$getResult=exec_query($qry);
		if(!empty($getResult)){
			$getProductName="select subGroup from product where recurly_plan_code='".$planCode."'";
			$getResultPrductName=exec_query($getProductName,1);
			$productName=$getResultPrductName['subGroup'];

			foreach($getResult as $key=>$value){
				if($value['subGroup']==$productName){
					$chkProduct=1;
					return $chkProduct;
				}else{
					$chkProduct=0;
				}
			}
		}else{
			$chkProduct=0;
		}
		return $chkProduct;
	}


	function setCustomerBasicInfo($email,$password,$accountCode,$firstName,$lastName,$phone){
		$objUserData= new userData();
		$params['email']=$email;
		$params['account_status']='enabled';
		$params['fname']=$firstName;
		$params['lname']=$lastName;
		$params['tel']=$phone;
		if(!empty($password)){
			$passwd=$objUserData->encryptUserPassword($password);
			$params['password']=$passwd;
		}
		$conditions['id']=$accountCode;
		update_query('subscription',$params,$conditions,$keynames=array());
	}

	function setAlertPreference($subId,$pref,$mobileNum){
		$params['subscription_id']=$subId;
		$params['alert_pref']=$pref;
		if($mobileNum!=''){
			$params['mobile_num']=$mobileNum;
		}
		$cond['subscription_id']=$subId;
		$insertId = insert_or_update("subscription_alert_pref",$params,$cond);
		if($insertId){
			return true;
		}
	}
	
	function getAlertPreference($subId){
		$chkForAlertPref = "select alert_pref,mobile_num from subscription_alert_pref where subscription_id='".$subId."'";
		$resForAlertPref = exec_query($chkForAlertPref,1);
		if(!empty($resForAlertPref)){
			return $resForAlertPref;
		}
		
	}
}
?>