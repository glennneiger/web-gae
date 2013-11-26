<?php
// ajax controller
// handles soft trials of mv subscriptions
header("Pragma: nocache\n");
header("cache-control: no-cache, must-revalidate, no-store\n\n");
header("Expires: 0");

session_start();
global $_SESSION,$refSourceCodes,$D_R,$viaDefaultAddr,$viaMaintainenceMsg,$viaProducts;
global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
include_once("$D_R/lib/json.php");
include_once($D_R.'/lib/config/_google_referral_config.php');

$json = new Services_JSON();
$objVia=new Via();
/* Via Server Exception handling - if Via Server is Down or Under maintennce*/
if($objVia->viaException!=''){
	global $viaMaintainenceMsg;
	$value=array('status'=>false,'msg'=>$viaMaintainenceMsg);
	$output = $json->encode($value);
	echo strip_tags($output);
	exit;
}
$userObj=new user();
$objViaCtrl=new ViaController();
$errorObj=new ViaException();
$errMessage='';
switch($_POST['type']){

	case 'doregister':
		if($_POST['refererSourceId']!=''){
			$_SESSION['refererSourceId'] = $_POST['refererSourceId'];
		}

		// check login availability
		$email = $_POST['uid'];
		$fieldsArray['customerLogin']=$email;
		// function is defined in class user and script /lib/_via_controller_lib.php
		$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
		if($userExistanceStatus!=''){
			$value=array(
				'status'=>false,
				'msg'=>'A Minyanville account already exists for this email address.  Please contact support@minyanville.com or 212-991-9357 to setup your trial.'
			);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}
		//==================================================================================

		// login information
		$loginInfo=array(
			'login'=>$_POST['uid'],
			'password'=>$_POST['pwd']
		);

		// default address
		$addresses=$viaDefaultAddr;

		// get promo code status
		if($_POST['promocode']){
			$promoCodeStatus=getPromoStatus($_POST['promocode']);
			if ($promoCodeStatus['source_code_id']){
				$sourceCodeId=$promoCodeStatus['source_code_id'];
			}
			else{
				$value=array(
				'status'=>false,
				'msg'=>'Invalid Promo Code.'
			);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
			}
		}elseif($_POST['refererSourceId']){  			// add source code id if of google/yahoo/bing



				if($_POST['refererSourceId']=="google"){
					$sourceCodeId=$refSourceCodes['google']['code'];
				}elseif($_POST['refererSourceId']=="yahoo"){
					$sourceCodeId=$refSourceCodes['yahoo']['code'];
				}elseif($_POST['refererSourceId']=="bing"){
					$sourceCodeId=$refSourceCodes['bing']['code'];
				}
		}else{
			$sourceCodeId=1;
		}
            $temp_orders="";
			$tmpOrder="";
			/*add exchange details*/
			// subscription def id-source code id
			$temp_orders="15".'-'.$sourceCodeId;

			if($_POST['subscription']=='buzz'){
				$tmpOrder=$viaProducts['BuzzST']['typeSpecificId'].'-'.$sourceCodeId;
				$temp_orders=$temp_orders.','.$tmpOrder;
			}

			if($_POST['subscription']=='cooper'){
				$tmpOrder=$viaProducts['CooperST']['typeSpecificId'].'-'.$sourceCodeId;
				$temp_orders=$temp_orders.','.$tmpOrder;
			}

			if($_POST['subscription']=='optionsmith'){
				$tmpOrder=$viaProducts['OptionsmithST']['typeSpecificId'].'-'.$sourceCodeId;
				$temp_orders=$temp_orders.','.$tmpOrder;
			}

			if($_POST['subscription']=='flexfolio'){
				$tmpOrder=$viaProducts['FlexfolioST']['typeSpecificId'].'-'.$sourceCodeId;
				$temp_orders=$temp_orders.','.$tmpOrder;

			}

			if($_POST['subscription']=='jacklavery'){
				$tmpOrder=$viaProducts['JackST']['typeSpecificId'].'-'.$sourceCodeId;
				$temp_orders=$temp_orders.','.$tmpOrder;
			}
			/*end add temp orders*/

		// add aux field
		$account_activated=0; /*set account activation to via- 0,1*/
		$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);

		// integrate customer information
		$custInfo=array(
			'loginInfo'=>$loginInfo,
			'addresses'=>$addresses,
			'email'=>$_POST['uid'],
			'nameFirst'=>$_POST['firstname'],
			'nameLast'=>$_POST['lastname'],
			'email'=>$_POST['uid'],
			'auxFields'=>$auxInfo
		);


		// set user name and password
		$objVia->nameFirst=$_POST['firstname'];
		$objVia->nameLast=$_POST['lastname'];
		// send request to via
		// defined in /lib/_via_data_lib.php
		$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0); // only add customer info, order place after activation
		// via responded successfully
		if($customerDetails=='true'){
			$via_id=$objVia->customerIdVia;
			// insert record to minyanville db
			// defined in /lib/_via_data_lib.php
			$alerts =  1; //set by default to true for soft trial users
			$insertedId=$objVia->insertBasicUser($alerts);

			/* Insert into ex_user_email_settings + ex_profile_privacy tables */
			RegisterUser($insertedId);

			/* Insert into ex_user_profile table */
			$subarray = array('subscription_id'=>$insertedId);
			$profileid = insert_query('ex_user_profile', $subarray);

			$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
			$resultUpdate=exec_query($strQuery,1);
			$result=$resultUpdate;
			set_sess_vars("user_id",$result['id']);
			$userObj->getCustomerSubInfo();
			$productstatusarray = $userObj->getSubcriptionProductDetails($_SESSION['user_id']);
			if(is_array($productstatusarray)){
				foreach($productstatusarray as $key => $value){
					set_sess_vars($key,$value);
				}
			}

			// login new user to system
			$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);

			// account created successfully
			$value=array(
				'status'=>true,
				'firstname'=>ucwords($_POST['firstname']),
				'lastname'=>ucwords($_POST['lastname']),
				'email'=>$_POST['uid'],
				'pd'=>$_POST['pwd'],
				'trackname'=>$_POST['trackname']
			);
		}

		// minyanville db insertion failed
		else{

			// message handling
			//echo "MVIL DB insertion failed";
			$errMessage=$errorObj->getExactCustomError($customerDetails);
			if($errMessage==''){
				$pattern = '/Error:(.*)/';
				preg_match($pattern, $errViaMessage, $matches);
				$errMessage=$matches[1];
			}
			if($errMessage==''){
				$errMessage='An error occurred while processing your request. Please try again later.';
			}

			$value=array(
				'status'=>false,
				'msg'=>$errMessage
			);

		}

		$output = $json->encode($value);
		echo strip_tags($output);
		exit;

		case'doregisterbuzz':
			if($_POST['refererSourceId']!=''){
			$_SESSION['refererSourceId'] = $_POST['refererSourceId'];
		}

		// check login availability
		$email = $_POST['uid'];
		$fieldsArray['customerLogin']=$email;
		// function is defined in class user and script /lib/_via_controller_lib.php
		$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
		if($userExistanceStatus!=''){
			$value=array(
				'status'=>false,
				'msg'=>'A Minyanville account already exists for this email address.  Please contact support@minyanville.com or 212-991-9357 to setup your trial.'
			);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}
		//==================================================================================

		// login information
		$loginInfo=array(
			'login'=>$_POST['uid'],
			'password'=>$_POST['pwd']
		);
		// default address
		$addresses=$viaDefaultAddr;

		// get promo code status
		if($_POST['promocode']){
			$promoCodeStatus=getPromoStatus($_POST['promocode']);
			if ($promoCodeStatus['source_code_id']){
				$sourceCodeId=$promoCodeStatus['source_code_id'];
			}
			else{
				$value=array(
				'status'=>false,
				'msg'=>'Invalid Promo Code.'
			);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
			}
		}elseif($_POST['refererSourceId']){  			// add source code id if of google/yahoo/bing



				if($_POST['refererSourceId']=="google"){
					$sourceCodeId=$refSourceCodes['google']['code'];
				}elseif($_POST['refererSourceId']=="yahoo"){
					$sourceCodeId=$refSourceCodes['yahoo']['code'];
				}elseif($_POST['refererSourceId']=="bing"){
					$sourceCodeId=$refSourceCodes['bing']['code'];
				}
		}else{
			$sourceCodeId=1;
		}


		// add aux field
		$account_activated=1; /*set account activation to via- 0,1*/
		$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders="");
                // cart details with exchange
		$orderDetails=array();

		$orderDetails['OrderItem'][0]['orderClassId']=9;
		$orderDetails['OrderItem'][0]['orderCodeId']=9;
		$orderDetails['OrderItem'][0]['sourceCodeId']=1;
		$orderDetails['OrderItem'][0]['orderItemType']='SUBSCRIPTION';
		$orderDetails['OrderItem'][0]['typeSpecificId']=15;
		$orderDetails['OrderItem'][0]['price']=0;
		$orderDetails['OrderItem'][0]['paymentAccountNumb']=1;
		$orderDetails['OrderItem'][0]['qty']=1;
		
		$orderDetails['OrderItem'][1]['orderClassId']=$viaProducts['BuzzST']['orderClassId'];
		$orderDetails['OrderItem'][1]['orderCodeId']=$viaProducts['BuzzST']['orderCodeId'];
		$orderDetails['OrderItem'][1]['sourceCodeId']=$sourceCodeId;
		$orderDetails['OrderItem'][1]['orderItemType']=$viaProducts['BuzzST']['orderItemType'];
		$orderDetails['OrderItem'][1]['typeSpecificId']=$viaProducts['BuzzST']['typeSpecificId'];
		$orderDetails['OrderItem'][1]['price']=$viaProducts['BuzzST']['price'];
		$orderDetails['OrderItem'][1]['paymentAccountNumb']=1;
		$orderDetails['OrderItem'][1]['qty']=1;

		$cartDetails=array(
			'billDate'=>date('Y-m-d'),
			'items'=>$orderDetails
		);
		// integrate customer information
		$custInfo=array(
			'loginInfo'=>$loginInfo,
			'addresses'=>$addresses,
			'email'=>$_POST['uid'],
			'nameFirst'=>$_POST['firstname'],
			'nameLast'=>$_POST['lastname'],
			'email'=>$_POST['uid'],
			'auxFields'=>$auxInfo
		);


		// set user name and password
		$objVia->nameFirst=$_POST['firstname'];
		$objVia->nameLast=$_POST['lastname'];
		// send request to via
		// defined in /lib/_via_data_lib.php
		//$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0); // only add customer info, order place after activation
		$hardtrial=1;
		$customerDetails=$objVia->addCustomerAndOrder($custInfo,$cartDetails,$hardtrial);
		// via responded successfully
		if($customerDetails=='true'){
			$via_id=$objVia->customerIdVia;
			// insert record to minyanville db
			// defined in /lib/_via_data_lib.php
			$alerts =  1; //set by default to true for soft trial users
			$insertedId=$objVia->insertBasicUser($alerts);

			/* Insert into ex_user_email_settings + ex_profile_privacy tables */
			RegisterUser($insertedId);

			/* Insert into ex_user_profile table */
			$subarray = array('subscription_id'=>$insertedId);
			$profileid = insert_query('ex_user_profile', $subarray);

			$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
			$resultUpdate=exec_query($strQuery,1);
			$result=$resultUpdate;
			set_sess_vars("user_id",$result['id']);
			$userObj->getCustomerSubInfo();
			$productstatusarray = $userObj->getSubcriptionProductDetails($_SESSION['user_id']);
			if(is_array($productstatusarray)){
				foreach($productstatusarray as $key => $value){
					set_sess_vars($key,$value);
				}
			}

			// login new user to system
			$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);

			// account created successfully
			$value=array(
				'status'=>true,
				'firstname'=>ucwords($_POST['firstname']),
				'lastname'=>ucwords($_POST['lastname']),
				'email'=>$_POST['uid'],
				'pd'=>$_POST['pwd'],
				'trackname'=>$_POST['trackname']
			);
			
			if($_POST['promocode']=="SEMBUZZ"){
				$objViaEmail= new ViaEmail();
				$email=$_POST['uid'];
				$pwd=$_POST['pwd'];
				$firstname=$_POST['firstname'];
				$objViaEmail->sendSofttrialSemBuzzWelcomeEmail($email,$pwd,$firstname);
			}	
		}

		// minyanville db insertion failed
		else{

			// message handling
			//echo "MVIL DB insertion failed";
			$errMessage=$errorObj->getExactCustomError($customerDetails);
			
			if($errMessage==''){
				$pattern = '/Error:(.*)/';
				preg_match($pattern, $errViaMessage, $matches);
				$errMessage=$matches[1];
			}
			if($errMessage==''){
				$errMessage='An error occurred while processing your request. Please try again later.';
			}

			$value=array(
				'status'=>false,
				'msg'=>$errMessage
			);

		}

		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
			
		break;	

	case 'buzz_oneweek':						
		// login information
		$stEmail = $_POST['uid'];
		$stPass = $_POST['pwd'];
		$stFirstName = $_POST['firstname'];
		$stLastName = $_POST['lastname'];
		$stPhone = $_POST['phone'];
		$arLoginInfo=array(
			'login'=>$stEmail,
			'password'=>$stPass
		);
		// default address
		$addresses=$viaDefaultAddr;
		// integrate auxilary field
		$account_activated=1;
		$auxInfo=$objVia->setAuxilaryFields($account_activated,"");
		// integrate customer information
		$arCustomerInfo=array(								
			'email'=>$stEmail,
			'nameFirst'=>$stFirstName,
			'nameLast'=>$stLastName,
			'phone'=>$stPhone,
			'auxFields' => $auxInfo
		);
		// cart details with exchange
		$orderDetails=array();
		$arExchange['orderClassId']=9;
		$arExchange['orderCodeId']=9;
		$arExchange['sourceCodeId']=1;
		$arExchange['orderItemType']='SUBSCRIPTION';
		$arExchange['typeSpecificId']=15;
		$arExchange['price']=0;
		$arExchange['paymentAccountNumb']=1;
		$arExchange['qty']=1;
		
		// Associate Promo code with the purchase
		$arPromoCode=getPromoStatus($_POST['promocode']);
		if ($arPromoCode['source_code_id']){
			$sourceCodeId=$arPromoCode['source_code_id'];
		}
		else{
			$sourceCodeId = 1;
		}
		$arBuzzWeekFreeTrial['orderClassId']=$viaProducts['BuzzFT1W-ST']['orderClassId'];
		$arBuzzWeekFreeTrial['orderCodeId']=$viaProducts['BuzzFT1W-ST']['orderCodeId'];
		$arBuzzWeekFreeTrial['sourceCodeId']=$sourceCodeId;
		$arBuzzWeekFreeTrial['orderItemType']=$viaProducts['BuzzFT1W-ST']['orderItemType'];
		$arBuzzWeekFreeTrial['typeSpecificId']=$viaProducts['BuzzFT1W-ST']['typeSpecificId'];
		$arBuzzWeekFreeTrial['price']=$viaProducts['BuzzFT1W-ST']['price'];
		$arBuzzWeekFreeTrial['paymentAccountNumb']=1;
		$arBuzzWeekFreeTrial['qty']=1;
		
		$fieldsArray['customerLogin']=$stEmail;
		$user_via_id=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);						
		
		if($user_via_id) // User is already in DB
		{
			// add order 
			$objVia->customerIdVia = $user_via_id;		
					
			// check if user is already subscribed to 1 week free trail
			$user_already_subscribed = '0';
			$arOrders = $userObj->getUserProductDetail($user_via_id);
			foreach($arOrders as $arOrder)
			{
				if($arOrder->typeSpecificId == $arBuzzWeekFreeTrial['typeSpecificId'])
				{
					$user_already_subscribed = '1';
				}
			}				
			if($user_already_subscribed == 0)
			{												
				unset($arCustomerInfo['email']);
				$arCustomerInfo['loginInfo']['password'] = $stPass;
				// Update customer information, Exception is not handled purposely
				//because VIA system behaves inconsistent some time it update record sucesssully, sometime throw Exception
				// in both the scenario records are updated sucessfully
				$customerDetails=$objVia->updateCustomer($arCustomerInfo);				
				$orderDetails['OrderItem'][0] = $arBuzzWeekFreeTrial; // Add only weekly trial
				$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
				);		
				$customerOrderDetails=$objVia->addOrder($cartDetails);
			}
		}
		else
		{				
			$arAddCustomerInfo = $arCustomerInfo;
			$arAddCustomerInfo['loginInfo'] = $arLoginInfo;
			$arAddCustomerInfo['addresses'] = $addresses;	
			// Add both exchange and weekly trial
			$orderDetails['OrderItem'][0] = $arExchange;
			$orderDetails['OrderItem'][1] = $arBuzzWeekFreeTrial;
			$cartDetails=array(
			'billDate'=>date('Y-m-d'),
			'items'=>$orderDetails
			);		
			// add customer and order 
			$customerOrderDetails=$objVia->addCustomerAndOrder($arAddCustomerInfo,$cartDetails,$hardtrial=1);				
		}
		if($customerOrderDetails=='true') // if order added sucessfully
		{				
			if(!$user_via_id) // If new User
			{
				$user_via_id=$objVia->customerIdVia;
				$insertedId=$objVia->insertBasicUser();					
				RegisterUser($insertedId);
				// Insert into ex_user_profile table
				$subarray = array('subscription_id'=>$insertedId);
				$profileid = insert_query('ex_user_profile', $subarray);
			}			
			$loginInfo=$userObj->loginByViaId($user_via_id);
			// Send Welcome email
			$objViaEmail= new ViaEmail();
			$objViaEmail->sendOneWeekBuzzTrialWelcomeEmail($stEmail,$stPass,$stFirstName);
		}
		else
		{
			if($user_already_subscribed == 1)
			{
				$errMessage = "You are already subscribed for Buzz & Banter 1 week free trial";				
			}
			else
			{
				$errMessage=$errorObj->getExactCustomError($customerOrderDetails);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request. Please check your data.';
				}				
			}		
		}
		$status = false;	
		if(empty($errMessage))
		{
			$status = true;
			$errMessage = "Success";
		}
		$value=array(
					'status'=>$status,
					'msg'=>$errMessage
				);				
		// generate array that can be used with js
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	break;
	default:
}
?>