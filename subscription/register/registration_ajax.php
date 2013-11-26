<?php
	ini_set("max_execution_time",3600);
	session_start();
	global $D_R;
	include_once($D_R.'/lib/config/_products_config.php');
	include_once("$D_R/lib/_includes.php");
	
	include_once("$D_R/lib/json.php");
	global $errViaMessage,$viaDefaultAddr,$_SESSION;
	$json = new Services_JSON();
	// Via library object
	$objVia=new Via();
	$userObj=new user();
	$objViaCtrl=new ViaController();
	/*  Via Server Exception handling - if Via Server is Down or Under maintennce*/
	if($objVia->viaException!=''){
		global $viaMaintainenceMsg ;
		//$_SESSION['viaServiceError'] = True;
		$value=array(
			'status'=>false,
			'msg'=>$viaMaintainenceMsg
		);

		$output = $json->encode($value);
		echo strip_tags($output);
		exit;

	}
	$errorObj=new ViaException();
	$errMessage='';

	switch($_POST['type']){
		// welcome section
		case 'checkccstatus':
			if(($_SESSION['viacart']['SUBSCRIPTION'] && count($_SESSION['viacart']['SUBSCRIPTION'])>0) ||
			($_SESSION['viacart']['PRODUCT'] && count($_SESSION['viacart']['PRODUCT'])>0)
		)
			{
				$CCStatus=1;
			}
			else{
				$CCStatus=0;
			}
			echo $CCStatus;
			break;
		//login
		case 'login':
			//$loginInfo=$userObj->login($_POST['uid'],md5($_POST['pwd']));
			$password = trim(stripslashes($_POST['pwd']));
			$loginInfo=$userObj->login($_POST['uid'],$password);
			// message handling
			//echo "MVIL DB insertion failed";
			if($loginInfo=='true'){
				$value=array(
					'status'=>true,
					'msg'=>""
				);
			}
			else{
				if($loginInfo=='blocked') // Condition for blocked user
				{
					$errMessage ="Your Minyanville services are blocked. Please contact at support@minyanville.com";
				}elseif($loginInfo=='Inactive account'){
					$errMessage='Inactive account';
				}
				else
				{
					$errMessage=$errorObj->getExactCustomError($loginInfo);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}
				}
					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
			}
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		// registration step 1
		case 'register':
			global $D_R;
			include_once("$D_R/lib/_layout_data_lib.php");
			//============================================
			// check if cart is empty
			if(!$_SESSION['viacart'] || count($_SESSION['viacart'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'Your cart is empty.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
			//============================================
			// check login availability
			$email = $_POST['uid'];
			$fieldsArray['customerLogin']=$email;
			// function is defined in class user and script /lib/_via_controller_lib.php
			$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
			if($userExistanceStatus!=''){
				$value=array(
					'status'=>false,
					'msg'=>'The email address you\'re attempting to register is already in our system. Please either login to that account above or choose another email ID.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
		//==================================================================================

			// login information
			$loginInfo=array(
				'login'=>$_POST['uid'],
/* 				'password'=>md5($_POST[pwd])        */
				'password'=>$_POST['pwd']
			);

			// default address
			$addresses=$viaDefaultAddr;

			// integrate auxilary field
			$account_activated=1; /*set account activation to via- 0,1*/
			$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders="");

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

			$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
			);
			// set user name and password
			$objVia->nameFirst=$_POST['firstname'];
			$objVia->nameLast=$_POST['lastname'];

			// send request to via
			// defined in /lib/_via_data_lib.php
			$hardtrial=1;
			$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial);
			// via responded successfully
			if($customerDetails=='true'){
				$via_id=$objVia->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				if($_POST['dailydigest']){
					$_POST['alerts']=$_POST['dailydigest'];
				}
				$insertedId=$objVia->insertBasicUser($_POST['alerts']);
				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				if($_POST['dailyfeed']){
					$qry = "select id from email_categories where name like 'dailyfeed'";
					$result=exec_query($qry,1);
					$daily_feed_email_cat_id = $result['id'];
					if($insertedId != '') {
						update_email_category_alert($insertedId,$daily_feed_email_cat_id);
					}
				}

				RegisterUser($insertedId);

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$insertedId);
				$profileid = insert_query('ex_user_profile', $subarray);
				// login new user to system
				$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);
					// account created successfully
					$value=array(
						'status'=>true,
						'firstname'=>ucwords($_POST['firstname']),
						'lastname'=>ucwords($_POST['lastname'])
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
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);

				}

			// generate array that can be used with js
			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		// registration step 2
		case "update_customer_order":
			// check if cart is empty
			global $productAd;
			if(!$_SESSION['viacart'] || count($_SESSION['viacart'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'Your cart is empty.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
			$arrayFields=array('customerIdVia'=>$_SESSION['viaid']);
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			
			if(is_array($custDetails->CustomerGetResult->Customer->addresses))
			{
				$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses[0]->typeOfAddr;
			}
			else
			{
				$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses->typeOfAddr;
			}	
			// address
			if($_POST['country']!='AA'){
				$_POST['state']=$_POST['country'];
			}
			$addresses=array(
				'typeOfAddr'=>$stTypeOfAddress,
				'address1'=>$_POST['address1'],
				'address2'=>$_POST['address2'],
				'city'=>$_POST['city'],
				//'country'=>$_POST['country'],
				'state'=>$_POST['state'],
				'zip'=>$_POST['zipcode']
			);

			// integrate auxilary field
			$account_activated=1; /*set account activation to via- 0,1*/
			$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders="");

			$custInfo=array(
				'addresses'=>$addresses,
				'nameFirst'=>$_POST['firstname'],
				'nameLast'=>$_POST['lastname'],
				//'phone'=>$_POST['phone'],
				'auxFields'=>$auxInfo
			);
			if(isset($_POST['email']))
			{
				$fieldsArray['customerLogin']=$_POST['email'];
				// function is defined in class user and script /lib/_via_controller_lib.php
				$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
				if($userExistanceStatus!=''){
					$value=array(
						'status'=>false,
						'msg'=>'The email address you\'re attempting to register is already in our system. Please either login to that account above or choose another email ID.'
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
				$loginInfo=array(
				'login'=>$_POST['email']
				);

				$custInfo['email'] = $_POST['email'];
				$custInfo['loginInfo'] = $loginInfo;

			}

			// payment accounts
			$paymentAcnt=array(
				'number'=>'1',
				'ePaymentTypes'=>$_POST['cctype'],
				'accountNumber'=>$_POST['ccnum'],
				'ccExpire'=>$_POST['ccexpire'],
				'ccVerificationValue'=>$_POST['cvvnum']
			);

			if($_SESSION['viacart']){
				// order details
				$_SESSION['viacart']=$objVia->validate_cart(); //  defined in _via_data_lib.php

				$orderDetails=array();
				$trialDetails=array();
				$paidIndex=0;
				$trialIndex=0;
				if($_SESSION['viacart']['SUBSCRIPTION']){
				// Subscription
				$keyword=$_POST['keyword'];
				if($_POST['from']){
					$from=$_POST['from'];
				}else{
					$from=substr($_SERVER['HTTP_REFERER'],7);
				}
				$source=$_SERVER['HTTP_HOST'];
				foreach($_SESSION['viacart']['SUBSCRIPTION'] as $key=>$val){

/*GA code for subscription*/

				$productName=$val['productName'];
				$producttype=$val['producttype'];

					if(is_array($val) && $val['discountedPrice']!=''){

						// check if user is valid to get this info or need to pass trial to Via
						// defined in _via_data_lib.php
						$trialStatus=$objVia->validate_premium($val['oc_id'],$_SESSION['viaid']);
						global $promoCodeSourceCodeNoFreeTrial,$viaOrderClassId;
						if(in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial))
						{
							$trialStatus = true;
						}
						// if user needs to get trial
						if($trialStatus!=true && !(in_array($val['oc_id'],$viaOrderClassId))){

							// get trial info for specific product
							// defined in _via_data_lib.php
							$trialStatus=$objVia->getTrial($val['subscription_def_id']);

							$trialDetails['OrderItem'][$trialIndex]['orderClassId']=$trialStatus['orderClassId'];
							$trialDetails['OrderItem'][$trialIndex]['orderCodeId']=$trialStatus['orderCodeId'];
							$trialDetails['OrderItem'][$trialIndex]['typeSpecificId']=$trialStatus['typeSpecificId'];
							$trialDetails['OrderItem'][$trialIndex]['price']=$trialStatus['price'];

							$trialDetails['OrderItem'][$trialIndex]['customerIdVia']=$_SESSION['viaid'];
							$trialDetails['OrderItem'][$trialIndex]['sourceCodeId']=$val['source_code_id'];
							$trialDetails['OrderItem'][$trialIndex]['orderItemType']=$val['orderItemType'];

							$trialDetails['OrderItem'][$trialIndex]['paymentAccountNumb']=1;
							$trialDetails['OrderItem'][$trialIndex]['qty']=1;
							if(!$val['startDate']){
								$trialDetails['OrderItem'][$trialIndex]['startDate']=date('Y-m-d');
							}
							else{
								$trialDetails['OrderItem'][$trialIndex]['startDate']=$val['startDate'];
							}
							if($val['subscriptionId']){
								$trialDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}

							if(in_array($trialStatus['typeSpecificId'],$productAd)){
								$adsFree=1;
							}

							$trialIndex++;
						}
						else{
							$orderDetails['OrderItem'][$paidIndex]['orderClassId']=$val['oc_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderCodeId']=$val['order_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['typeSpecificId']=$val['subscription_def_id'];

							if($val['discountedPrice']){
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['discountedPrice'];
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['price'];


							}

							$orderDetails['OrderItem'][$paidIndex]['customerIdVia']=$_SESSION['viaid'];
							$orderDetails['OrderItem'][$paidIndex]['sourceCodeId']=$val['source_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderItemType']=$val['orderItemType'];
							$orderDetails['OrderItem'][$paidIndex]['paymentAccountNumb']=1;
							$orderDetails['OrderItem'][$paidIndex]['qty']=1;
							if(!$val['startDate']){
								$orderDetails['OrderItem'][$paidIndex]['startDate']=date('Y-m-d');
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['startDate']=$val['startDate'];
							}
							if($val['subscriptionId']){
								$orderDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}

							$paidIndex++;
						}

					}
				}
				}
				// product
				if($_SESSION['viacart']['PRODUCT']){
				foreach($_SESSION['viacart']['PRODUCT'] as $key=>$val){
/*GA code for product*/
				$productName=$val['productName'];
				$producttype=$val['producttype'];
					if(is_array($val) && $val['price']!=''){

						// check if user is valid to get this info or need to pass trial to Via
						// defined in _via_data_lib.php

							$orderDetails['OrderItem'][$paidIndex]['orderClassId']=$val['oc_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderCodeId']=$val['order_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['typeSpecificId']=$val['subscription_def_id'];
							if($val['discountedPrice']){
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['discountedPrice'];
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['price'];
							}

							$orderDetails['OrderItem'][$paidIndex]['customerIdVia']=$_SESSION['viaid'];
							$orderDetails['OrderItem'][$paidIndex]['sourceCodeId']=1;
							$orderDetails['OrderItem'][$paidIndex]['orderItemType']=$val['orderItemType'];
							$orderDetails['OrderItem'][$paidIndex]['paymentAccountNumb']=1;
							$orderDetails['OrderItem'][$paidIndex]['qty']=1;
							if(!$val['startDate']){
								$orderDetails['OrderItem'][$paidIndex]['startDate']=date('Y-m-d');
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['startDate']=$val['startDate'];
							}
							if($val['subscriptionId']){
								$orderDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}

							$paidIndex++;
						//}
					}
				}
				}
				if($trialDetails && count($trialDetails)>0){
					$trialCartDetails=array(
						'billDate'=>date('Y-m-d'),
						'items'=>$trialDetails
					);
				}

				if($orderDetails && count($orderDetails)>0){
				$cartDetails=array(
					'billDate'=>date('Y-m-d'),
					'items'=>$orderDetails
				);
				}
			}//end of session


			// check if viaid is set in session
			if($_SESSION['viaid']){
				$objVia->customerIdVia=$_SESSION['viaid'];
			}

			if(count($_SESSION['viacart']['SUBSCRIPTION'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'No product in cart.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}

			// update customer information
			// defined in /lib/_via_data_lib.php


			$customerDetails=$objVia->updateCustomer($custInfo);
			// exception occurs
			if($customerDetails!='true'){
				$errMessage=$errorObj->getExactCustomError($customerDetails);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request. Please check your data.';
				}

				$value=array(
					'status'=>false,
					'msg'=>$errMessage
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}

			// add payment account
			// defined in /lib/_via_data_lib.php
			$customerPaymentDetails=$objVia->addPaymentAccount($paymentAcnt);
			// exception occurs
			if($customerPaymentDetails!='true'){
				$errMessage=$errorObj->getExactCustomError($customerPaymentDetails);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request. Please check your data.';
				}

				$value=array(
					'status'=>false,
					'msg'=>$errMessage
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
			//=====================================================================
			if($customerPaymentDetails==true && $cartDetails && count($cartDetails)>0){
				// add orders
				// defined in /lib/_via_data_lib.php
				$customerOrderDetails=$objVia->addOrderAndPayment($cartDetails);

				if($customerOrderDetails!='true'){
					$errMessage=$errorObj->getExactCustomError($customerOrderDetails);
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}
			if($customerPaymentDetails==true && $trialCartDetails && count($trialCartDetails)>0){
				// add orders
				// defined in /lib/_via_data_lib.php

				$customerOrderDetails=$objVia->addOrder($trialCartDetails);

				if($customerOrderDetails!='true'){
					$errMessage=$errorObj->getExactCustomError($customerOrderDetails);
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}
//set session variable for welcome page
			set_sess_vars("recently_added",$_SESSION['viacart']);

			if($customerOrderDetails=='true' || $customerPaymentDetails=='true' || $customerDetails=='true'){
				unset($_SESSION['viacart']);
				$_SESSION['gatrackname']=$tracking_name;

				if($adsFree){
					$_SESSION['AdsFree']='1';
				}

				$value=array(
					'status'=>true,
					'msg'=>'Success',
					'trackname'=>$tracking_name
				);
			}
			/*
			else{
				$value=array(
					'status'=>false,
					'msg'=>'Error'
				);
			}
			*/
			// generate array that can be used with js
			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		case 'manage_settings':
			// if user changed password
			$loginInfo=array();
			if($_POST['password'] && $_POST['password']!=''){
				$loginInfo=array(
					'password'=>$_POST['password']
				);
			}

			if($_POST['uid'] != $_POST['uid_default'])
			{
				$stremail = $_POST['uid'];
				$fieldsArray['customerLogin']=$stremail;
				// function is defined in class user and script /lib/_via_controller_lib.php
				$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
				if($userExistanceStatus!=''){
					$value=array(
						'status'=>false,
						'msg'=>'The email address you have entered is already in our system. Please choose another email ID.'
					);

					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
				$loginInfo['login']=$_POST['uid'];
			}

			// get customer info
			$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			
			if(is_array($custDetails->CustomerGetResult->Customer->addresses))
			{
				$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses[0]->typeOfAddr;
			}
			else
			{
				$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses->typeOfAddr;
			}
			// address
			if($_POST['country']!='AA'){
				$_POST['state']=$_POST['country'];
			}
			$addresses=array(
				'typeOfAddr'=>$stTypeOfAddress,
				'address1'=>$_POST['address1'],
				'address2'=>$_POST['address2'],
				'city'=>$_POST['city'],
				//'country'=>$_POST['country'],
				'state'=>$_POST['state'],
				'zip'=>$_POST['zipcode']
			);

			if($_SESSION['viacart']){
				// order details
				$orderDetails=array();
				$trialDetails=array();
				//$_SESSION['viacart']=$objVia->validate_cart(); //  defined in _via_data_lib.php

				$paidIndex=0;
				$trialIndex=0;

				// subscription

				if($_SESSION['viacart']['SUBSCRIPTION']){
				foreach($_SESSION['viacart']['SUBSCRIPTION'] as $key=>$val){

					if(is_array($val) && $val['discountedPrice']!=''){

						// check if user is valid to get this info or need to pass trial to Via
						// defined in _via_data_lib.php
						$trialStatus=$objVia->validate_premium($val['oc_id'],$_SESSION['viaid']);
						global $promoCodeSourceCodeNoFreeTrial,$viaOrderClassId;
						if(in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial))
						{
							$trialStatus = true;
						}

						// if user needs to get trial
						if($trialStatus!=true && !(in_array($val['oc_id'],$viaOrderClassId))){
							// get trial info for specific product
							// defined in _via_data_lib.php

							$trialStatus=$objVia->getTrial($val['subscription_def_id']);

							$trialDetails['OrderItem'][$trialIndex]['orderClassId']=$trialStatus['orderClassId'];
							$trialDetails['OrderItem'][$trialIndex]['orderCodeId']=$trialStatus['orderCodeId'];
							$trialDetails['OrderItem'][$trialIndex]['typeSpecificId']=$trialStatus['typeSpecificId'];
							$trialDetails['OrderItem'][$trialIndex]['price']=$trialStatus['price'];

							$trialDetails['OrderItem'][$trialIndex]['customerIdVia']=$_SESSION['viaid'];
							$trialDetails['OrderItem'][$trialIndex]['sourceCodeId']=$val['source_code_id'];
							$trialDetails['OrderItem'][$trialIndex]['orderItemType']=$val['orderItemType'];

							$trialDetails['OrderItem'][$trialIndex]['paymentAccountNumb']=1;
							$trialDetails['OrderItem'][$trialIndex]['qty']=1;
							if(!$val['startDate']){
								$trialDetails['OrderItem'][$trialIndex]['startDate']=date('Y-m-d');
							}
							else{
								$trialDetails['OrderItem'][$trialIndex]['startDate']=$val['startDate'];
							}
							if($val['subscriptionId']){
								$trialDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}
							$trialIndex++;
						}
						else{
							$orderDetails['OrderItem'][$paidIndex]['orderClassId']=$val['oc_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderCodeId']=$val['order_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['typeSpecificId']=$val['subscription_def_id'];

							if($val['discountedPrice']){
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['discountedPrice'];
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['price'];
							}

							$orderDetails['OrderItem'][$paidIndex]['customerIdVia']=$_SESSION['viaid'];
							$orderDetails['OrderItem'][$paidIndex]['sourceCodeId']=$val['source_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderItemType']=$val['orderItemType'];

							$orderDetails['OrderItem'][$paidIndex]['paymentAccountNumb']=1;
							$orderDetails['OrderItem'][$paidIndex]['qty']=1;
							if(!$val['startDate']){
								$orderDetails['OrderItem'][$paidIndex]['startDate']=date('Y-m-d');
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['startDate']=$val['startDate'];
							}
							if($val['subscriptionId']){
								$orderDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}

							$paidIndex++;
						}

					}
				}
				}
				// product


				if($_SESSION['viacart']['PRODUCT']){
				foreach($_SESSION['viacart']['PRODUCT'] as $key=>$val){

					if(is_array($val) && $val['price']!=''){

							$orderDetails['OrderItem'][$paidIndex]['orderClassId']=$val['oc_id'];
							$orderDetails['OrderItem'][$paidIndex]['orderCodeId']=$val['order_code_id'];
							$orderDetails['OrderItem'][$paidIndex]['typeSpecificId']=$val['subscription_def_id'];

							if($val['discountedPrice']){
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['discountedPrice'];
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['price']=$val['price'];


							}

							$orderDetails['OrderItem'][$paidIndex]['customerIdVia']=$_SESSION['viaid'];
							$orderDetails['OrderItem'][$paidIndex]['sourceCodeId']=1;
							$orderDetails['OrderItem'][$paidIndex]['orderItemType']=$val['orderItemType'];

							$orderDetails['OrderItem'][$paidIndex]['paymentAccountNumb']=1;
							$orderDetails['OrderItem'][$paidIndex]['qty']=1;

							if(!$val['startDate']){
								$orderDetails['OrderItem'][$paidIndex]['startDate']=date('Y-m-d');
							}
							else{
								$orderDetails['OrderItem'][$paidIndex]['startDate']=$val['startDate'];
							}

							if($val['subscriptionId']){
								$orderDetails['OrderItem'][$paidIndex]['subscriptionId']=$val['subscriptionId'];
							}

							$paidIndex++;

						//}


					}
				}
				}

				if($trialDetails && count($trialDetails)>0){
					$trialCartDetails=array(
						'billDate'=>date('Y-m-d'),
						'items'=>$trialDetails
					);
				}

				if($orderDetails && count($orderDetails)>0){
				$cartDetails=array(
					'billDate'=>date('Y-m-d'),
					'items'=>$orderDetails
				);
				}

			}

			$custInfo=array(
				'loginInfo'=>$loginInfo,
				'addresses'=>$addresses,
				//'phone'=>$_POST['phone'],
				'email'=>$_POST['uid'],
				/*'login'=>$_POST['viauserid'],*/
				'nameFirst'=>$_POST['firstname'],
				'nameLast'=>$_POST['lastname']
				);
			if($_POST['uid'] != $_POST['uid_default'])
			{
				$custInfo['email'] = $_POST['uid'];
			}

			// check if viaid is set in session
			if($_SESSION['viaid']){
				$objVia->customerIdVia=$_SESSION['viaid'];
			}

			// updated customer record at Via
			// defined in /lib/_via_data_lib.php

			$customerDetails=$objVia->updateCustomer($custInfo);
			if($customerDetails!='true'){

					$errMessage=$errorObj->getExactCustomError($customerPaymentDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
			}
			// put password in cookie
			if($customerDetails==true && $_POST['password'] && $_POST['password']!=''){
				mcookie("password",$_POST['password']);
			}
			$userDetails=array(
				'fname'=>$_POST['firstname'],
				'lname'=>$_POST['lastname'],
				'email'=>$_POST['uid'],
				'password'=>base64_encode($_POST['password'])
			);
			// update customer in minyanville db
			// defined in /lib/_via_data_lib.php
			$objVia->updateUserDetails($_SESSION['viaid'],$userDetails);

			// updated payment account at Via
			// defined in /lib/_via_data_lib.php
			// set customerIdVia
			
			
			$customerPaymentDetails = true;
			
//=============================================================================================

			if(!$_POST['updateccnum'] && $_POST['updateccexpire']){
			$paymentTempAcnt=array(
				'number'=>'1',
				'ePaymentTypes'=>$_POST['cctype'],
				'ccExpire'=>$_POST['ccexpire']
			);
			}
			elseif($_POST['updateccnum'] ){
		 	$paymentTempAcnt=array(
				'number'=>'1',
				'ePaymentTypes'=>$_POST['cctype'],
				'accountNumber'=>$_POST['ccnum'],
				'ccExpire'=>$_POST['ccexpire'],
				'ccVerificationValue'=>$_POST['cvvnum']
			);
			}

			if($paymentTempAcnt && count($paymentTempAcnt)>1){
				if($custDetails->CustomerGetResult->Customer->paymentAccounts){
					// update payment account
					$customerPaymentDetails=$objVia->updatePaymentAccount($paymentTempAcnt);
				}
				// if user has no payment account
				else{
					// add payment account
					$customerPaymentDetails=$objVia->addPaymentAccount($paymentTempAcnt);
				}
				if($customerPaymentDetails!='true'){

					$errMessage=$errorObj->getExactCustomError($customerPaymentDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}

			if($customerPaymentDetails==true && $trialCartDetails && count($trialCartDetails)>0){
				// add orders
				// defined in /lib/_via_data_lib.php

				$customerOrderDetails=$objVia->addOrder($trialCartDetails);

				if($customerOrderDetails!='true'){
					$errMessage=$errorObj->getExactCustomError($customerOrderDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}

			//===================================================================

			if($customerPaymentDetails==true && $cartDetails && count($cartDetails)>0){
				// add orders
				// defined in /lib/_via_data_lib.php
				$customerOrderDetails=$objVia->addOrderAndPayment($cartDetails);

				if($customerOrderDetails!='true'){
					$errMessage=$errorObj->getExactCustomError($customerOrderDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}

			//=====================================================================
        	//customer record updated
			if($customerDetails== 'true' || $customerPaymentDetails=='true' || $customerOrderDetails=='true'){
					foreach($_SESSION['products']['SUBSCRIPTION'] as $key=>$value){
					if(in_array($key,$productAd)){
							if(array_key_exists($viaProducts['AdFreeMonthly']['typeSpecificId'],$_SESSION['products']['SUBSCRIPTION'])){
								// $to = $value['subscription_def_id'];
								$to = "";
								$frm = $viaProducts['AdFreeMonthly']['typeSpecificId'];
								$cancel='cancelAdFree';
							}
						}
					}
				// set variavble in session for welcome page
				set_sess_vars("recently_added",$_SESSION['viacart']);
				unset($_SESSION['viacart']);
				$msg='';
				if($customerOrderDetails=='true'){
					$msg='welcome';
				}
				$value=array(
					'status'=>true,
					'msg'=>$msg,
					'cancel'=>$cancel,
					'to'=>$to,
					'frm'=>$frm

				);
				if($_POST['uid'] != $_POST['uid_default'])
				{
					$value['logout'] = 1;
					$value['sub_id'] = $_SESSION['SID'];
					$_COOKIE['email'] = '';
				}

			}
			/*
			// record not updated
			else{
				$errMessage=$errorObj->getExactCustomError($customerDetails);
				$value=array(
					'status'=>false,
					'msg'=>
				);

				$value=array(
					'status'=>false,
					'msg'=>'Error'
				);
			}
			*/
			// generate array that can be used with js
			// defined in /lib/json.php
			if($_SESSION['new_register_billing'])
			{
				unset($_SESSION['new_register_billing']);
			}


			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		case "render_cart":
			echo renderyourcart($_SESSION['viacart']);
			exit;
		case "cancel_product":

			$objVia->customerIdVia=$_SESSION['viaid'];
			$cancelStatus=$objVia->cancelOrder($_POST['orderNo'],$_POST['orderItemSeq'],$_POST['payType'],$_POST['refundAmount'],$_POST['refundToCustId'],$_POST['cancelReason']);
			//$cancelStatus=$objVia->editOrder($_SESSION['products'][$_POST['typeSpecificId']]);
			//htmlprint_r($_SESSION['products'][$_POST['typeSpecificId']]);
			//exit;
			if($cancelStatus!='true'){
				$errMessage=$errorObj->getExactCustomError($cancelStatus);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request. Please check your data.';
				}

				$value=array(
					'status'=>false,
					'msg'=>$errMessage


				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
			if($_SESSION['combo'] && ($_POST['typeSpecificId']==1 || $_POST['typeSpecificId']==2)){
				unset($_SESSION['combo']);
				$_SESSION['Buzz']=0;
				$buzzFlag=array('premium'=>'0');
				$objVia->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
				unset($_SESSION['products']['4']);

				if($_POST['typeSpecificId']==1){
					$_SESSION['Flexfolio']=0;
					unset($_SESSION['products']['9']);
				}

				if($_POST['typeSpecificId']==2){
					$_SESSION['Cooper']=0;
					unset($_SESSION['products']['13']);
				}

			}
			else{
				unset($_SESSION['products'][$_POST['typeSpecificId']]);
				if($_POST['typeSpecificId']==1 || $_POST['typeSpecificId']==2 || $_POST['typeSpecificId']==3 || $_POST['typeSpecificId']==4 || $_POST['typeSpecificId']==16){
					$_SESSION['Buzz']=0;
					$buzzFlag=array('premium'=>'0');
					$objVia->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
				}
				elseif($_POST['typeSpecificId']==10 || $_POST['typeSpecificId']==11 || $_POST['typeSpecificId']==12 || $_POST['typeSpecificId']==13 || $_POST['typeSpecificId']==17) {
					$_SESSION['Cooper']=0;
				}
				elseif($_POST['typeSpecificId']==5 || $_POST['typeSpecificId']==6 || $_POST['typeSpecificId']==8 || $_POST['typeSpecificId']==9 || $_POST['typeSpecificId']==18) {
					$_SESSION['Flexfolio']=0;
				}
			}

			$value=array(
				'status'=>true,
				'msg'=>'Success'
			);

			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		// registration step 1
		case 'scottBuzzRegister':
			//============================================
			// check if cart is empty
			/***if(!$_SESSION['viacart'] || count($_SESSION['viacart'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'Your cart is empty.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}**/
			//============================================
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
/* 				'password'=>md5($_POST[pwd])        */
				'password'=>$_POST['pwd']
			);

			// default address
			$addresses=$viaDefaultAddr;

			// integrate customer information
			$custInfo=array(
				'loginInfo'=>$loginInfo,
				'addresses'=>$addresses,
				'email'=>$_POST['uid'],
				'nameFirst'=>$_POST['firstname'],
				'nameLast'=>$_POST['lastname'],
				'email'=>$_POST['uid']
			);
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


			$orderDetails['OrderItem'][1]['orderClassId']=$viaProducts['BuzzScott']['orderClassId'];
			$orderDetails['OrderItem'][1]['orderCodeId']=$viaProducts['BuzzScott']['orderCodeId'];
			$orderDetails['OrderItem'][1]['sourceCodeId']=1;
			$orderDetails['OrderItem'][1]['orderItemType']=$viaProducts['BuzzScott']['orderItemType'];
			$orderDetails['OrderItem'][1]['typeSpecificId']=$viaProducts['BuzzScott']['typeSpecificId'];
			$orderDetails['OrderItem'][1]['price']=$viaProducts['BuzzScott']['price'];
			$orderDetails['OrderItem'][1]['paymentAccountNumb']=1;
			$orderDetails['OrderItem'][1]['qty']=1;

			$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
			);
			// set user name and password
			$objVia->nameFirst=$_POST['firstname'];
			$objVia->nameLast=$_POST['lastname'];

			// send request to via
			// defined in /lib/_via_data_lib.php
			$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=1 );

			// via responded successfully
			if($customerDetails=='true'){
				$via_id=$objVia->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				$insertedId=$objVia->insertBasicUser($_POST['alerts']);
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
				if(is_array($productstatusarray))
				{
							foreach($productstatusarray as $key => $value)
							{
								set_sess_vars($key,$value);
							}
						}

				/* Send Welcome email to Scottrade registered user */
				send_scottrade_buzz_welcome_email($insertedId);

				// login new user to system
				$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);
					// account created successfully
					$value=array(
						'status'=>true,
						'firstname'=>ucwords($_POST['firstname']),
						'lastname'=>ucwords($_POST['lastname'])
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

			// generate array that can be used with js
			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		case 'softtrials_register':
			//============================================
			// check if cart is empty
			/***if(!$_SESSION['viacart'] || count($_SESSION['viacart'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'Your cart is empty.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}**/
			//============================================
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
/* 				'password'=>md5($_POST[pwd])        */
				'password'=>$_POST['pwd']
			);

			// default address
			$addresses=$viaDefaultAddr;

// integrate customer information
			$account_activated=1; /*set account activation to via- 0,1*/
			$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders="");
			$custInfo=array(
				'loginInfo'=>$loginInfo,
				'addresses'=>$addresses,
				'email'=>$_POST['uid'],
				'nameFirst'=>$_POST['firstname'],
				'nameLast'=>$_POST['lastname'],
				'email'=>$_POST['uid'],
                                'auxFields'=>$auxInfo
			);
			// cart details with exchange
			$orderDetails=array();
			$prodCount=0;
			$orderDetails['OrderItem'][$prodCount]['orderClassId']=9;
			$orderDetails['OrderItem'][$prodCount]['orderCodeId']=9;
			$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=1;
			$orderDetails['OrderItem'][$prodCount]['orderItemType']='SUBSCRIPTION';
			$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=15;
			$orderDetails['OrderItem'][$prodCount]['price']=0;
			$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
			$orderDetails['OrderItem'][$prodCount]['qty']=1;
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
			}
			else{
				$sourceCodeId=1;
			}

			if($_POST['buzzST']=='true'){
				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['BuzzST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['BuzzST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['BuzzST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['BuzzST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['BuzzST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;
			}
			if($_POST['cooperST']=='true'){
				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['CooperST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['CooperST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['CooperST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['CooperST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['CooperST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;
			}
			if($_POST['optionST']=='true'){
				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['OptionsmithST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['OptionsmithST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['OptionsmithST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['OptionsmithST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['OptionsmithST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;
			}
			if($_POST['flexfolioST']=='true'){
				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['FlexfolioST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['FlexfolioST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['FlexfolioST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['FlexfolioST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['FlexfolioST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;
			}
			if($_POST['jackST']=='true'){
				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['JackST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['JackST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['JackST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['JackST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['JackST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;
			}

			$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
			);
			// set user name and password
			$objVia->nameFirst=$_POST['firstname'];
			$objVia->nameLast=$_POST['lastname'];

			// send request to via
			// defined in /lib/_via_data_lib.php
			$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=1 );

			// via responded successfully
			if($customerDetails=='true'){
				$via_id=$objVia->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				$insertedId=$objVia->insertBasicUser($_POST['alerts']);
				if(!$insertedId){
					$qrysubid="select id from subscription where via_id='".$via_id."'";
					$getsubid=exec_query($qrysubid,1);
					$insertedId=$getsubid['id'];
				}

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
				if(is_array($productstatusarray))
				{
							foreach($productstatusarray as $key => $value)
							{
								set_sess_vars($key,$value);
							}
						}

				/* Send Welcome email to Scottrade registered user */
				//send_softtrials_welcome_email($insertedId,$_POST['firstname'],$_POST['lastname'],$_POST['uid']);

				// login new user to system
				$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);
					// account created successfully
					$value=array(
						'status'=>true,
						'firstname'=>ucwords($_POST['firstname']),
						'lastname'=>ucwords($_POST['lastname']),
						'email'=>$_POST['uid'],
						'buzz'=>ucwords($_POST['buzzST']),
						'jack'=>ucwords($_POST['jackST']),
						'cooper'=>ucwords($_POST['cooperST']),
						'optionsmith'=>ucwords($_POST['optionST']),
						'flex'=>ucwords($_POST['flexfolioST']),
						'pd'=>$_POST['pwd']
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

			// generate array that can be used with js
			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		case 'softtrials_emails':
			$recipient_email = $_POST['email'];
			$id = $_SESSION['user_id'];
			$name = $_POST['name'];
			 $pd = $_POST['pd'];

			 if($_POST['buzz']=='True'){
				 $objViaCtrl->sendSoftTrialEmails($id,$recipient_email,'buzzST',$pd, $name);
			 }
			 if($_POST['cooper']=='True'){
				  $objViaCtrl->sendSoftTrialEmails($id,$recipient_email,'cooperST',$pd,$name);
			 }
			 if($_POST['optionsmith']=='True'){
				  $objViaCtrl->sendSoftTrialEmails($id,$recipient_email,'optionsmithST',$pd,$name);
			 }
 			 if($_POST['jack']=='True'){
				  $objViaCtrl->sendSoftTrialEmails($id,$recipient_email,'jacklaveryST',$pd,$name);
			 }
 			 if($_POST['flex']=='True'){
				  $objViaCtrl->sendSoftTrialEmails($id,$recipient_email,'flexfolioST',$pd,$name);
			 }
			 $value=array(
						'status'=>true,
						'msg'=>$errMessage
					);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		case 'daily_feed_newsletter':
				global $D_R;
				include_once("$D_R/lib/_layout_data_lib.php");
				$feed_email = $_POST['checkemail'];
				$fuser_id = $_POST['sessuserid'];
				$user_type = 'olduser';

				$qry = "select id from email_categories where name like 'dailyfeed'";
				$result=exec_query($qry,1);
				$daily_feed_email_cat_id = $result['id'];
				if($fuser_id != '') {
					update_email_category_alert($fuser_id,$daily_feed_email_cat_id);
					daily_feed_welcome_email($user_type,$feed_email,$password);

					$errMessage='success';
					$value=array('status'=>true,'msg'=>$errMessage);
				}
				else {
					$qry = "select id from subscription where email like '$feed_email'";
					$result=exec_query($qry,1);
					$fuser_id = $result['id'];

					if($fuser_id != '') {
						update_email_category_alert($fuser_id,$daily_feed_email_cat_id);
						daily_feed_welcome_email($user_type,$feed_email,$password);

						$errMessage='success';
						$value=array('status'=>true,'msg'=>$errMessage);
					}
					else {
										// check login availability
										$email = $feed_email;
										$password = gen_trivial_password(6);
										$fieldsArray['customerLogin']=$email;
										// login information
										$loginInfo=array(
											'login'=>$feed_email,
											'password'=>$password
										);

										// default address
										$addresses=$viaDefaultAddr;

										$explode_email = explode("@",$feed_email);
										$fname = $explode_email[0];
										$lname = $explode_email[0];

										/*Add order in via aux field*/
										$temp_orders="15".'-'."1";
										// add aux field
										$account_activated=0; /*set account activation to via- 0,1*/
										$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);

										// integrate customer information
										$custInfo=array(
											'loginInfo'=>$loginInfo,
											'addresses'=>$addresses,
											'email'=>$feed_email,
											'nameFirst'=>$fname,
											'nameLast'=>$lname,
											'email'=>$feed_email,
											'auxFields'=>$auxInfo
										);
										// set user name and password
										$objVia->nameFirst=$fname;
										$objVia->nameLast=$lname;

										// send request to via
										// defined in /lib/_via_data_lib.php
										//$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=0);

										$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0,$dfuser=1);
										// via responded successfully
										if($customerDetails=='true'){
											$via_id=$objVia->customerIdVia;
											// insert record to minyanville db
											// defined in /lib/_via_data_lib.php
											$insertedId=$objVia->insertBasicUser($_POST['alerts']);
											/* Insert into ex_user_email_settings + ex_profile_privacy tables */
											RegisterUser($insertedId);

											/* Insert into ex_user_profile table */
											$subarray = array('subscription_id'=>$insertedId);
											$profileid = insert_query('ex_user_profile', $subarray);

											/*Insert into email_alert_categoryalert for daliyfeed */
											insert_email_category_alert($insertedId);

											/* Send Daily Feed Welcome Email */
											//$user_type = 'newuser';
											//daily_feed_welcome_email($user_type,$feed_email,$password);

											// login new user to system
											$loginInfo=$userObj->login($feed_email,$password,1);
											$errMessage = "newuser";
												// account created successfully
												$value=array(
													'status'=>true,
													'firstname'=>ucwords($fname),
													'lastname'=>ucwords($lname),
													'msg'=>$errMessage
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
													$errMessage='An error occurred while processing your request. Please check your data.';
												}

												$value=array(
													'status'=>false,
													'msg'=>$errMessage
												);

											}
									}
								}
						// generate array that can be used with js
						// defined in /lib/json.php
						$output = $json->encode($value);
						echo strip_tags($output);
						exit;
			case 'dailydigest':
				global $D_R;
				include_once("$D_R/lib/_layout_data_lib.php");
				$feed_email = $_POST['checkemail'];
				$fuser_id = $_POST['sessuserid'];
				$user_type = 'olduser';
					$qry = "select id from subscription where email like '$feed_email'";
					$result=exec_query($qry,1);
					$fuser_id = $result['id'];
					if($fuser_id != '') {
						updateDailyDigestEmail($fuser_id);
						dailyDigestWelcomeEmail($user_type,$feed_email,$password);

						$errMessage='success';
						$value=array('status'=>true,'msg'=>$errMessage);
					}
					else {
						// check login availability
						$alerts=1;
						$email = $feed_email;
						$password = gen_trivial_password(6);
						$fieldsArray['customerLogin']=$email;
						// login information
						$loginInfo=array(
						'login'=>$feed_email,
						'password'=>$password
						);
     					// default address
						$addresses=$viaDefaultAddr;
						$explode_email = explode("@",$feed_email);
						$fname = $explode_email[0];
						$lname = $explode_email[0];
						/*Add order in via aux field*/
						$temp_orders="15".'-'."1";
						// add aux field
						$account_activated=0; /*set account activation to via- 0,1*/
						$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);
						// integrate customer information
										$custInfo=array(
											'loginInfo'=>$loginInfo,
											'addresses'=>$addresses,
											'email'=>$feed_email,
											'nameFirst'=>$fname,
											'nameLast'=>$lname,
											'email'=>$feed_email,
											'auxFields'=>$auxInfo
										);
										// set user name and password
						$objVia->nameFirst=$fname;
						$objVia->nameLast=$lname;
						/*3 for dailygigest*/
						$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0,$dfuser=3);
						// via responded successfully
										if($customerDetails=='true'){
											$via_id=$objVia->customerIdVia;

											$insertedId=$objVia->insertBasicUser($alerts);

											RegisterUser($insertedId);

											/* Insert into ex_user_profile table */
											$subarray = array('subscription_id'=>$insertedId);

											$profileid = insert_query('ex_user_profile', $subarray);
											//insert_email_category_alert($insertedId);

											$loginInfo=$userObj->login($feed_email,$password,1);
											$errMessage = "newuser";

												$value=array(
													'status'=>true,
													'firstname'=>ucwords($fname),
													'lastname'=>ucwords($lname),
													'msg'=>$errMessage
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
													$errMessage='An error occurred while processing your request. Please check your data.';
												}

												$value=array(
													'status'=>false,
													'msg'=>$errMessage
												);

											}
									}
						
						// generate array that can be used with js
						// defined in /lib/json.php
						$output = $json->encode($value);
						echo strip_tags($output);
						exit;
			case 'dailyrecap':
				global $D_R;
				include_once("$D_R/lib/_layout_data_lib.php");
				
				$feed_email = $_POST['checkemail'];
				$fuser_id = $_POST['sessuserid'];
				$user_type = 'olduser';
					$qry = "select id,recv_daily_gazette from subscription where email like '$feed_email'";
					
					$result=exec_query($qry,1);
					$fuser_id = $result['id'];
					//$recv_daily_gazette
					if($result['recv_daily_gazette']=="1"){
						$errMessage='You have already subscribed for Daily Recap Newsletter';
						$value=array('status'=>subscribed,'msg'=>$errMessage);
						
					}elseif($fuser_id != '') {
						updateDailyDigestEmail($fuser_id);
						dailyDigestWelcomeEmail($user_type,$feed_email,$password);

						$errMessage='success';
						$value=array('status'=>true,'msg'=>$errMessage);
					}
					else {
						// check login availability
						$alerts=1;
						$email = $feed_email;
						$password = gen_trivial_password(6);
						$fieldsArray['customerLogin']=$email;
						// login information
						$loginInfo=array(
						'login'=>$feed_email,
						'password'=>$password
						);
     					// default address
						$addresses=$viaDefaultAddr;
						$explode_email = explode("@",$feed_email);
						$fname = $explode_email[0];
						$lname = $explode_email[0];
						/*Add order in via aux field*/
						$temp_orders="15".'-'."1";
						// add aux field
						$account_activated=0; /*set account activation to via- 0,1*/
						$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);
						// integrate customer information
										$custInfo=array(
											'loginInfo'=>$loginInfo,
											'addresses'=>$addresses,
											'email'=>$feed_email,
											'nameFirst'=>$fname,
											'nameLast'=>$lname,
											'email'=>$feed_email,
											'auxFields'=>$auxInfo
										);
										// set user name and password
						$objVia->nameFirst=$fname;
						$objVia->nameLast=$lname;
						/*3 for dailygigest*/
						$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0,$dfuser=3);
						// via responded successfully
										if($customerDetails=='true'){
											$via_id=$objVia->customerIdVia;

											$insertedId=$objVia->insertBasicUser($alerts);

											RegisterUser($insertedId);

											/* Insert into ex_user_profile table */
											$subarray = array('subscription_id'=>$insertedId);

											$profileid = insert_query('ex_user_profile', $subarray);
											//insert_email_category_alert($insertedId);

											$loginInfo=$userObj->login($feed_email,$password,1);
											$errMessage = "newuser";

												$value=array(
													'status'=>true,
													'firstname'=>ucwords($fname),
													'lastname'=>ucwords($lname),
													'msg'=>$errMessage
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
												if(trim($errMessage)=="Customer login already in use."){
														$errMessage="Customer login already signed up.";	
												}
												if($errMessage==''){
													$errMessage='An error occurred while processing your request. Please check your data.';
												}

												$value=array(
													'status'=>false,
													'msg'=>$errMessage
												);

											}
									}
						
						// generate array that can be used with js
						// defined in /lib/json.php
						$output = $json->encode($value);
						echo strip_tags($output);
						exit;
			

			 case 'articletopic_newsletter':
			 	global $D_R;
			 	include_once("$D_R/lib/_layout_data_lib.php");
				$feed_email = $_POST['checkemail'];
				$fuser_id = $_POST['sessuserid'];
				$section_id = $_POST['section_id'];
				$section_name = $_POST['section_name'];
			    $user_type = 'olduser';
				if($fuser_id != '') {
					update_email_topic_alert($fuser_id,$section_id);
					topic_welcome_email($user_type,$feed_email,$password,$section_name);

					$errMessage='success';
					$value=array('status'=>true,'msg'=>$errMessage);
				}
				else {
					$qry = "select id from subscription where email like '$feed_email'";
					$result=exec_query($qry,1);
					$fuser_id = $result['id'];

					if($fuser_id != '') {
						update_email_topic_alert($fuser_id,$section_id );
						topic_welcome_email($user_type,$feed_email,$password,$section_name);

						$errMessage='success';
						$value=array('status'=>true,'msg'=>$errMessage);
					}
					else {

										//============================================
										// check login availability
										$email = $feed_email;
										$password = gen_trivial_password(6);
										$fieldsArray['customerLogin']=$email;
										// login information
										$loginInfo=array(
											'login'=>$feed_email,
											'password'=>$password
										);

										// default address
										$addresses=$viaDefaultAddr;

										$explode_email = explode("@",$feed_email);
										$fname = $explode_email[0];
										$lname = $explode_email[0];

										/*Add order in via aux field*/
										$temp_orders="15".'-'."1";
										// add aux field
										$account_activated=0; /*set account activation to via- 0,1*/
										$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);

										// integrate customer information
										$custInfo=array(
											'loginInfo'=>$loginInfo,
											'addresses'=>$addresses,
											'email'=>$feed_email,
											'nameFirst'=>$fname,
											'nameLast'=>$lname,
											'email'=>$feed_email,
											'auxFields'=>$auxInfo
										);
										// set user name and password
										$objVia->nameFirst=$fname;
										$objVia->nameLast=$lname;

										// send request to via
										// defined in /lib/_via_data_lib.php
										//$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=0);

										$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0,$dfuser=2,$section_id);
										// via responded successfully
										if($customerDetails=='true'){
											$via_id=$objVia->customerIdVia;
											// insert record to minyanville db
											// defined in /lib/_via_data_lib.php
											$insertedId=$objVia->insertBasicUser($_POST['alerts']);
											/* Insert into ex_user_email_settings + ex_profile_privacy tables */
											RegisterUser($insertedId);

											/* Insert into ex_user_profile table */
											$subarray = array('subscription_id'=>$insertedId);
											$profileid = insert_query('ex_user_profile', $subarray);

											/*Insert into email_alert_categoryalert for daliyfeed */
											insert_email_topic_alert($insertedId,$section_id);

											/* Send Topic Feed Welcome Email */
											$user_type = 'newuser';
										//	topic_welcome_email($user_type,$feed_email,$password,$section_name);

											// login new user to system
											$loginInfo=$userObj->login($feed_email,$password,1);
											$errMessage = "newuser";
												// account created successfully
												$value=array(
													'status'=>true,
													'firstname'=>ucwords($fname),
													'lastname'=>ucwords($lname),
													'msg'=>$errMessage
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
													$errMessage='An error occurred while processing your request. Please check your data.';
												}

												$value=array(
													'status'=>false,
													'msg'=>$errMessage
												);

											}
									}
								}
						// generate array that can be used with js
						// defined in /lib/json.php
						$output = $json->encode($value);
						echo strip_tags($output);
						exit;

			            case 'gogleAdWorld':
			            global $D_R;
			            include_once($D_R.'/lib/config/_googleadwords_config.php');
						global $GOOGLEADWORDTRACKING;
						$prodAd = $_REQUEST['prod'];
						$prodValAd = explode(",",$prodAd);
						$trialTypeAd = 'hard';
						$prodAd = trim($prodAd);
						$prodAd = substr($prodAd,1);
						$productsAd = explode(",",$prodAd);
						foreach($productsAd as $key=>$val){
							$str .= googleAdWordTracking($val, $trialTypeAd);
						}
						//return $GOOGLEADWORDTRACKING[$prodVal['0']][$trialType];
						return true;
						exit;
      case 'registerUser':
			global $D_R;
			include_once("$D_R/lib/_layout_data_lib.php");
      	
			// check login availability
			$email = $_POST['uid'];
			$fieldsArray['customerLogin']=$email;
			// function is defined in class user and script /lib/_via_controller_lib.php
			$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
			if($userExistanceStatus!=''){
				$value=array(
					'status'=>false,
					'msg'=>'This email address is already in our system. Please either login to that account above or choose another email ID.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
		//==================================================================================

			// login information
			$loginInfo=array(
				'login'=>$_POST['uid'],
/* 				'password'=>md5($_POST[pwd])        */
				'password'=>$_POST['pwd']
			);


			// default address
			if($_POST['country']!='AA'){
				$_POST['state']=$_POST['country'];
			}else{
				$_POST['state']="NY";
			}

			$addresses=array(
				'typeOfAddr'=>"Billing",
				'state'=>$_POST['state'],
				'city'=>$viaDefaultAddr['city'],
				'zip'=>$viaDefaultAddr['zip'],
				'address1'=>$viaDefaultAddr['address1']
			);



			// integrate auxilary field
			$account_activated=0; /*set account activation to via- 0,1*/
		  	$temp_orders="15".'-'."1";
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
					// send request to via
			// defined in /lib/_via_data_lib.php
			$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0);

			// via responded successfully
			if($customerDetails=='true'){
				$_SESSION['freeUserRegistration']=TRUE;
				$via_id=$objVia->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				if($_POST['dailydigest']==""){ $_POST['dailydigest']=0; }
				$insertedId=$objVia->insertBasicUser($_POST['dailydigest']);
				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				if($_POST['dailyfeed']){
					$qry = "select id from email_categories where name like 'dailyfeed'";
					$result=exec_query($qry,1);
					$daily_feed_email_cat_id = $result['id'];
					if($insertedId != '') {
						update_email_category_alert($insertedId,$daily_feed_email_cat_id);
					}
				}

				RegisterUser($insertedId);
				/* facebook id */
				if($_POST['fbid']){
					$fbArray = array('subscription_id'=>$insertedId, 'fb_id'=> $_POST['fbid']);
					$fbQry = insert_query('mv_facebook_mapping', $fbArray);
				}

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$insertedId);
				$profileid = insert_query('ex_user_profile', $subarray);
				// login new user to system
				$loginInfo=$userObj->login($_POST['uid'],$_POST['pwd'],$_POST['rememeber']);
					// account created successfully

			$value=array('status'=>true,'firstname'=>$_SESSION['firstname'],'lastname'=>$_SESSION['lastname'],'msg'=>'success','tracking_name'=>$tracking_name);

					$value=array(
						'status'=>true,
						'firstname'=>$_SESSION['firstname'],
						'lastname'=>$_SESSION['lastname']
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
						$errMessage='An error occurred while processing your request. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);

				}

			// generate array that can be used with js
			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;

		 case 'purchase_housing_report':
		 	global $D_R;
		 	include_once("$D_R/lib/_layout_data_lib.php");
			//============================================
			// check if cart is empty
			if(!$_SESSION['viacart'] || count($_SESSION['viacart'])==0){
				$value=array(
					'status'=>false,
					'msg'=>'Your cart is empty.'
				);
				$output = $json->encode($value);
				echo strip_tags($output);
				exit;
			}
			// billing Address
			if($_POST['country']!='AA'){
				$_POST['state']=$_POST['country'];
			}
			$arAddress=array(
				'typeOfAddr'=>'Billing',
				'address1'=>$_POST['address1'],
				'address2'=>$_POST['address2'],
				'city'=>$_POST['city'],
				'state'=>$_POST['state'],
				'zip'=>$_POST['zipcode']
			);
			$fieldsArray['customerLogin']=$_POST['uid'];
			$user_via_id=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);

			// integrate auxilary field
			$account_activated=1;
			$auxInfo=$objVia->setAuxilaryFields($account_activated,"");
			$arCustomerInfo=array(
					'addresses'=>$arAddress,
					'email'=>$_POST['uid'],
					'nameFirst'=>$_POST['firstname'],
					'nameLast'=>$_POST['lastname'],
					//'phone'=>$_POST['phone'],
					'auxFields' => $auxInfo
				);

			if($user_via_id) // User is already in DB
			{
				$objVia->customerIdVia = $user_via_id;
				$arrayFields=array('customerIdVia'=>$user_via_id);
				$custDetails=$objVia->getCustomersViaDetail($arrayFields);

				if(is_array($custDetails->CustomerGetResult->Customer->addresses))
				{
					$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses[0]->typeOfAddr;
				}
				else
				{
					$stTypeOfAddress = $custDetails->CustomerGetResult->Customer->addresses->typeOfAddr;
				}
				$arCustomerInfo['addresses']['typeOfAddr'] = $stTypeOfAddress;
				$customerDetails=$objVia->updateCustomer($arCustomerInfo);
				if($customerDetails!='true'){

					$errMessage=$errorObj->getExactCustomError($customerPaymentDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while updating your Info. Please check your data.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
			}else
			{

				// login information
				$autoPassword="housing";
				$arLoginInfo=array(
					'login'=>$_POST['uid'],
					'password'=>$autoPassword
				);
				$arAddCustomerInfo = $arCustomerInfo;
				$arAddCustomerInfo['loginInfo'] = $arLoginInfo;

				$customerDetails=$objVia->addCustomer($arAddCustomerInfo,1);
				// via responded successfully
				if($customerDetails=='true'){
					$user_via_id=$objVia->customerIdVia;
					$insertedId=$objVia->insertBasicUser($_POST['alerts']);
					/* Insert into ex_user_email_settings + ex_profile_privacy tables */
					if($_POST['dailyfeed']){
						$qry = "select id from email_categories where name like 'dailyfeed'";
						$result=exec_query($qry,1);
						$daily_feed_email_cat_id = $result['id'];
						if($insertedId != '') {
							update_email_category_alert($insertedId,$daily_feed_email_cat_id);
						}
					}
					RegisterUser($insertedId);
					// Insert into ex_user_profile table
					$subarray = array('subscription_id'=>$insertedId);
					$profileid = insert_query('ex_user_profile', $subarray);
				}
			}
			if($user_via_id)
			{
				// login new user to system
				$loginInfo=$userObj->loginByViaId($user_via_id);


				$arrayFields=array('customerIdVia'=>$user_via_id);
				$custDetails=$objVia->getCustomersViaDetail($arrayFields);

				$arPaymentDetails=array(
					'number'=>'1',
					'ePaymentTypes'=>$_POST['cctype'],
					'accountNumber'=>$_POST['ccnum'],
					'ccExpire'=>$_POST['ccexpire'],
					'ccVerificationValue'=>$_POST['cvvnum']
				);

				// add payment account
				$objVia->loginId = "";	// Don't remove this code
				if($custDetails->CustomerGetResult->Customer->paymentAccounts)
				{

					if(isset($_POST['updateccnum'])) //Update customer account information only if there is change in CC number
					{
						$customerPaymentDetails=$objVia->updatePaymentAccount($arPaymentDetails);
					}
					else
					{
						$customerPaymentDetails = true;
					}
				}
				else
				{
					$customerPaymentDetails=$objVia->addPaymentAccount($arPaymentDetails);
				}

				if($customerPaymentDetails!='true')
				{
					$errMessage=$errorObj->getExactCustomError($customerPaymentDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request. Please check your Credit data.';
					}
					$value=array(
					'status'=>false,
					'msg'=>$errMessage
					);
					$output = $json->encode($value);
					echo strip_tags($output);
					exit;
				}
				// add Order and Payment

				$orderDetails=array();
				$productIndex = 0;
				if($_SESSION['viacart'])
				{
					// order details
					$_SESSION['viacart']=$objVia->validate_cart(); //  defined in _via_data_lib.php
					if($_SESSION['viacart']['SUBSCRIPTION'])
					{
						foreach($_SESSION['viacart']['SUBSCRIPTION'] as $key=>$val)
						{
							if(is_array($val) && $val['discountedPrice']!='')
							{
								$orderDetails['OrderItem'][$productIndex]['orderClassId']=$val['oc_id'];
								$orderDetails['OrderItem'][$productIndex]['orderCodeId']=$val['order_code_id'];
								$orderDetails['OrderItem'][$productIndex]['typeSpecificId']=$val['subscription_def_id'];
								if(!empty($val['discountedPrice'])){
									$orderDetails['OrderItem'][$productIndex]['price']=$val['discountedPrice'];
								}else{
									$orderDetails['OrderItem'][$productIndex]['price']=$val['price'];
								}
								$orderDetails['OrderItem'][$productIndex]['sourceCodeId']=$val['source_code_id'];
								$orderDetails['OrderItem'][$productIndex]['orderItemType']=$val['orderItemType'];
								$orderDetails['OrderItem'][$productIndex]['paymentAccountNumb']=1;
								$orderDetails['OrderItem'][$productIndex]['qty']=1;
								if(!$val['startDate']){
									$orderDetails['OrderItem'][$productIndex]['startDate']=date('Y-m-d');
								}
								else{
									$orderDetails['OrderItem'][$productIndex]['startDate']=$val['startDate'];
								}
								if($val['subscriptionId']){
									$orderDetails['OrderItem'][$productIndex]['subscriptionId']=$val['subscriptionId'];
								}
								$orderDetails['OrderItem'][$productIndex]['customerIdVia']=$user_via_id;

								$productIndex++;
							}
						} //End Foreach subscription
					} // End If Subscription

				}//end of session

				$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
				);

				$orderDetails=$objVia->addOrderAndPayment($cartDetails);
				if($orderDetails!='true')
				{
					$errMessage=$errorObj->getExactCustomError($customerDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your Order. Please check your Credit Card Information.';
					}

					$value=array(
						'status'=>false,
						'msg'=>$errMessage
					);
				}
				else
				{
					$value=array(
						'status'=>true,
						'msg'=>"Success"
					);

					foreach($_SESSION['products']['SUBSCRIPTION'] as $key=>$val)
					{
						if($val['orderClassId']=="26")
						{
							$arrIssue=array();
							$arrIssue['orderNumber'] = $val['orderNumber'];
							$arrIssue['orderItemSeq'] = $val['orderItemSeq'];
							$arrIssue['orderClassId'] = $val['orderClassId'];
							$arrIssue['orderCodeId'] = $val['orderCodeId'];
							$arrIssue['sourceCodeId'] = $val['sourceCodeId'];
							$arrIssue['typeSpecificId'] = $val['typeSpecificId'];
							$arrIssue['startDate'] = $val['startDate'];
							$arrIssue['billDate'] = $val['billDate'];
							$arrIssue['description'] = $val['description'];
							$arrIssue['price'] = $val['price'];
							$arrIssue['subscriptionId'] = $val['subscriptionId'];
							$arrIssue['orderItemType'] = $val['orderItemType'];
							$arrIssue['issuesLeft'] = $val['n_issues_left'];
							$res = $objVia->getUnitBasedOrder($arrIssue);
						}
					}
					set_sess_vars("recently_added",$_SESSION['viacart']);
					unset($_SESSION['viacart']);
				}
			}
			else
			{
				$value=array(
						'status'=>false,
						'msg'=>$errMessage='An error occurred while processing your request. Pleas try again.'
					);
			}

			// defined in /lib/json.php
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;

		 case 'checkcartmanage':
			if(count($_SESSION['viacart']['SUBSCRIPTION'])>0){
				$output=true;
				echo $output;
				exit;
			}

		default:
			// code to be build
	}//end of switch case
?>