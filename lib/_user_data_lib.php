<?php
/******************************************
This class contains functions to communicate with
Via web service.
*******************************************/

class userData{

	public function __construct(){
		//$this->$securityKey="MiQjP68YaO3115Vi3llq7e65";
	}

	function decryptUserPassword($userPaswd){
		if(!empty($userPaswd)){
			$securitykey="MiQjP68YaO3115Vi3llq7e65";
			$decodedQueryString=base64_decode($userPaswd);
			$decryptedQueryString= mcrypt_ecb(MCRYPT_BLOWFISH,$securitykey,$decodedQueryString, MCRYPT_DECRYPT);
			$decryptedQueryString=strip_tags($decryptedQueryString);
			if(!empty($decryptedQueryString)){
				return $decryptedQueryString;
			}else{
				return false;
			}
		}
	}

	function encryptUserPassword($userPaswd){
		if(!empty($userPaswd)){
			$securitykey="MiQjP68YaO3115Vi3llq7e65";
			$encrypted_data = mcrypt_ecb(MCRYPT_BLOWFISH,$securitykey,$userPaswd, MCRYPT_ENCRYPT);
			$encryptedData=base64_encode($encrypted_data);
			return $encryptedData;
		}else{
			return false;
		}


	}

	public function authenticateCustomer($customerLogin,$customerPassword){

		try{
		$customerAuth=$this->CustomerAuthentication($customerLogin,$customerPassword);
			return $customerAuth;
		}
		catch (Exception $fault)
		{
			return "Invalid email address or password. Please check again.";
		}

	}// end of  function authenticateCustomer

	function CustomerAuthentication($customerLogin,$customerPassword){
		$getEncryptedPass=$this->encryptUserPassword($customerPassword);
		$qry="select id,fname,lname,email,city,state,zip,address,tel from subscription where email='".$customerLogin."' and password='".$getEncryptedPass."'";
		$getResult=exec_query($qry,1);
		if(!empty($getResult)){
		    return $getResult;
		}else{
			return false;
		}

	}

	function checkUserViaAvailibilityByEmail($email){
		$sql="select id from subscription where email='".$email."'";

		$getResult=exec_query($sql,1);
		if(!empty($getResult)){
		   return $getResult['id'];
		}else{
		  return false;
		}
	}

	function setCustomerInfo($email,$passwd,$SID){
		$params['email']=$email;
		if($passwd!=="Password"){
			$passwd=$this->encryptUserPassword($passwd);
			$params['password']=$passwd;
		}
		$conditions['id']=$SID;
		update_query('subscription',$params,$conditions,$keynames=array());
	}

	function addGATrans($uuid,$planCode,$planName,$planCharge,$city=null,$state=null,$country=null){
		global $_SESSION;

		$i=count($_SESSION['ecommerceTracking']['items']);
		$_SESSION['ecommerceTracking']['items'][$i+1]['id']=$uuid;            // order ID - required
		$_SESSION['ecommerceTracking']['items'][$i+1]['SKU']=$planCode;  // SKU/code - required
		$_SESSION['ecommerceTracking']['items'][$i+1]['name']=$planName;           // product name
		$_SESSION['ecommerceTracking']['items'][$i+1]['category']=getProductType($planCode)." - Subscription";        // category or variation
		$_SESSION['ecommerceTracking']['items'][$i+1]['price']=$planCharge;      // unit price - required
		$_SESSION['ecommerceTracking']['items'][$i+1]['quantity']=1;             // quantity - required

		$_SESSION['ecommerceTracking']['trans']['id']=$uuid;            // order ID - required
		$_SESSION['ecommerceTracking']['trans']['store']="Minyanville Main Site";  // affiliation or store name
		$_SESSION['ecommerceTracking']['trans']['total']=$planCharge;           // total - required
		$_SESSION['ecommerceTracking']['trans']['city']=$city;        // city
		$_SESSION['ecommerceTracking']['trans']['state']=$state;      // state or province
      	$_SESSION['ecommerceTracking']['trans']['country']=$country;            // country
	}
	
	
	function moneyShowUserRegister($email,$firstname,$lastname,$address,$city,$state,$zipCode){
		$objRegisterFunnel= new registrationFunnelData();
		$objRecurlyData= new recurlyData();
		$checkUser=$this->checkUserViaAvailibilityByEmail($email);
		if(empty($checkUser)){
			$password = rand(100000, 99999999);
			$objUserData= new userData();
		        $userPassword=$objUserData->encryptUserPassword($password);
			$accountCode=$objRegisterFunnel->getAccountCode();
			$params['id']=$accountCode;
			$params['email']=$email;
			$params['account_status']='enabled';
			$params['password']=$userPassword;
			$params['fname']=$firstname;
			$params['lname']=$lastname;
			$params['address']=$address;
			$params['city']=$city;
			$params['state']=$state;
			$params['zip']=$zipCode;
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
				$setUserRecurly=$objRecurlyData->getAccount($accountCode,$email,$email,$firstname,$lastname,$companyName,$viaId);
				/*Add softtrial plan*/
				$insId=$this->setSoftTrialPlans($accountCode);
				if(!empty($insId)){
					$this->sendSoftTrialWelcomeEmail($firstname,$lastname,$email,$password);
				}
			}
		}else{
			
			$accountCode=$checkUser;
			$qry="SELECT  P.subGroup
			FROM subscription_cust_order SCO, product P
			WHERE SCO.subscription_id='".$accountCode."' AND 
			((SCO.typeSpecificId=P.subscription_def_id) or (SCO.recurly_plan_code=P.recurly_plan_code)) and
			(((SCO.expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00') AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))
			OR (SCO.recurly_state='active' or SCO.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
			$getResult=exec_query($qry);
			foreach($getResult as $key=>$value){
				if($value['subGroup']=='buzz'){
					$chkBuzz=1;
				}
			}
			if(empty($chkBuzz)){ /*user not subscribed to any Buzz and Banter Plan*/
				$passQry="select password,fname,lname from subscription where id='".$accountCode."'";
				$getResultPass=exec_query($passQry,1);
				$userPaswd=$getResultPass['password'];
				if(empty($userPaswd)){
					$password = rand(100000, 99999999);
					$setPassword=$this->setMoneyShowCustomerBasicInfo($email,$password,$accountCode,$firstname,$lastname,$address,$city,$state,$zipCode);
				}else{
					$setPassword=$this->setMoneyShowCustomerBasicInfo($email,$password,$accountCode,$firstname,$lastname,$address,$city,$state,$zipCode);
					$password=$this->decryptUserPassword($userPaswd);
				}
				/*Add softtrial plan*/
				$insId=$this->setSoftTrialPlans($accountCode);
				if(!empty($insId)){
					/*update user in recurly*/
				$setUserRecurly=$objRecurlyData->getAccount($accountCode,$email,$email,$firstname,$lastname,$companyName,$viaId);
					$this->sendSoftTrialWelcomeEmail($firstname,$lastname,$email,$password);
				}
			}
		}
		
	}
	
	function setSoftTrialPlans($accountCode){
		$endDate = strtotime("+13 day",strtotime(mysqlNow($date=0)));
		
		$paramsCust['subscription_id'] = $accountCode;
		$paramsCust['recurly_plan_code'] = "buzz_mntly_stndrd_ft_moneyshow_08242012";
		$paramsCust['recurly_plan_name'] = "Buzz & Banter - Monthly Standard + Free Trial - Moneyshow 8/24/2012";
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
			return $insertId;
		}
	}
	
	function sendSoftTrialWelcomeEmail($firstname,$lastname,$email,$password){
		global $fromSoftTrialWelcomeEmail,$subjectSoftTrialWelcomeEmail,$tmplSoftTrialWelcome,$D_R,$HTPFX,$HTHOST;

		$user_email =$email;
	   	$subject= $subjectSoftTrialWelcomeEmail;
	   	$from[$fromSoftTrialWelcomeEmail]=$fromSoftTrialWelcomeEmail;
	   	$userName = $firstname." ".$lastname;
	  	$welcomeTmpl = $HTPFX.$HTHOST.$tmplSoftTrialWelcome;
		//$welcomeTmpl = $HTPFX."minyanville:fE8Gnnhn3TI8L4f@".$HTHOST.$tmplSoftTrialWelcome;
 		$msgurl=$welcomeTmpl.qsa(array(firstname=>$firstname,email=>$email,password=>$password));
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
	
	function setMoneyShowCustomerBasicInfo($email,$password,$accountCode,$firstName,$lastName,$address,$city,$state,$zipCode){
		$params['email']=$email;
		$params['account_status']='enabled';
		$params['fname']=$firstName;
		$params['lname']=$lastName;
		$params['address']=$address;
		$params['city']=$city;
		$params['state']=$state;
		$params['zip']=$zipCode;
		if(!empty($password)){
			$passwd=$this->encryptUserPassword($password);
			$params['password']=$passwd;
		}
		$conditions['id']=$accountCode;
		update_query('subscription',$params,$conditions,$keynames=array());
	}
	
	
} //class end

?>