<?php
	session_start();
	include_once("$D_R/lib/json.php");
	include_once("$D_R/lib/_includes.php");
	include_once("$D_R/lib/_via_data_lib.php");
	include_once("$D_R/lib/_via_controller_lib.php");
	include_once("$D_R/lib/json.php");
	global $viaDefaultAddr;
	$json = new Services_JSON();
	$objVia=new Via();
	/* Via Server Exception handling - if Via Server is Down or Under maintennce*/
	if($objVia->viaException!='')
	{
		global $viaMaintainenceMsg;
		$value=array('status'=>false,'msg'=>$viaMaintainenceMsg);
		$output = $json->encode($value);
		echo strip_tags($output);
		exit;
	}
	$errorObj=new ViaException();
	//$errorsresultset = $errorObj->apierrors();
	$getType=$_REQUEST['type'];
	// Case to check user exists
	if((isset($getType)) && ($getType == 'checkUser'))
	{
		$loginSystem = new user(); // _via_conroller_lib.php
		$email = $_POST['login'];
		$fieldsArray['customerLogin']=$email;
		echo $loginSystem->checkUserViaAvailibilityByEmail($fieldsArray); // _via_controller_lib.php
	}
	// Case to check user login
	else if((isset($getType))&& ($getType=='login'))
	{
		if(isset($_POST['login']) && (!isset($_POST['checkUser'])))
		{
			if((!$_POST['login']) || (!$_POST['pwd']))
			{
				echo "Either username or password is wrong";
				exit;
			}
			$loginSystem = new user(); // _via_conroller_lib.php
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
				}elseif($isLoggedIn=='Inactive account'){
					$mesasg='Inactive account';
				}else{
					$mesasg = $errorObj->getExactCustomError($isLoggedIn);
					if($mesasg==''){
						$mesasg='An error occurred while processing your request. Please check your data.';
				}
					//$value=array('status'=>false,'msg'=>$errorObj->getExactCustomError($isLoggedIn));
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
		$expirydate= $_POST['iboxccexpire'];
		$res=explode('/', $expirydate);
		$expirydate = $res[1]."-".$res[0];

		/*Auxilary fields updated to via*/

		if($_POST['productselected']=='1'){
		   $account_activated=1; /*set account activation to via- 0,1*/
		   $temp_orders="";
		}else{
		  $account_activated=0; /*set account activation to via- 0,1*/
		  $temp_orders="15".'-'."1";
		}
		$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders);

		if($_POST['productselected']=='1'){

		if(trim($_POST['iboxcountry'])!='AA')
		{
			$_POST['iboxstate']=$_POST['iboxcountry'];
		}

		$custInfo=array('loginInfo'=>array('login'=>$_POST['useremail'],'password'=>$_POST['tuserRPassword'],'creationDate'=>date('Y-m-d')),'addresses'=>array('typeOfAddr'=>'Billing','address1'=>$_POST['billingaddress1'],'address2'=>$_POST['billingaddress2'],'city'=>$_POST['billingcity'],'state'=>$_POST['iboxstate'],'zip'=>$_POST['userzipcode']),'paymentAccounts'=>array('number'=>'1','ePaymentTypes'=>$_POST['iboxcctype'],'accountNumber'=>$_POST['iboxccnum'],'ccExpire'=>$expirydate,'ccVerificationValue'=>$_POST['iboxcvvnum']),'phone'=>$_POST['iboxphone'],'nameFirst'=>$_POST['userFname'],'nameLast'=>$_POST['userLname'],'email'=>$_POST['useremail'],'auxFields'=>$auxInfo);
		}else if($_POST['productselected']=='0'){
			$custInfo=array(
							'loginInfo'=>array('login'=>$_POST['useremail'],'password'=>$_POST['tuserRPassword'],'creationDate'=>date('Y-m-d')),
							'addresses'=>array('typeOfAddr'=>$viaDefaultAddr['typeOfAddr'],'state'=>$viaDefaultAddr['state'],'city'=>$viaDefaultAddr['city'],'zip'=>$_POST['userzipcode']),
							'nameFirst'=>$_POST['userFname'],
							'nameLast'=>$_POST['userLname'],
							'email'=>$_POST['useremail'],
							'auxFields'=>$auxInfo
							);
			// addCustomerAndOrder ****
		}

		$subdefid=array();
		$buzzSelected='0';
		if($_POST['skip']!=1) // selected the products but skipped
		{
		$profileid=1;
		$trialtype="iboxHardTrial";
		$eventname="TrialProduct";
		$source=$_SERVER['HTTP_HOST'];
		$from=substr($_SERVER['HTTP_REFERER'],7);
		$tracking_name="";
		$trackname="";
			if($_POST['buzzproduct'] =='buzz'){
				$subdefid[] = $_POST['buzz_1'];
				$productName="subBuzzBanter";
				$producttype=getProductType($_POST['buzz_1']);
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				$tracking_name=$trackname;
				$buzzSelected='1';
			}
			if($_POST['coopproduct'] =='cooper'){
				$subdefid[] = $_POST['cooper_1'];
				$productName="subCooper";
				$producttype=getProductType($_POST['cooper_1']);
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				if($tracking_name){
					$tracking_name.=','.$trackname;
				}else{
					$tracking_name.=$trackname;
				}
			}
			if($_POST['flexproduct'] =='quint'){
				$subdefid[] = $_POST['flexfolio_1'];
				$productName="subFlexFolio";
				$producttype=getProductType($_POST['flexfolio_1']);
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				if($tracking_name){
					$tracking_name.=','.$trackname;
				}else{
					$tracking_name.=$trackname;
				}
			}
			if($_POST['optionsmithproduct'] =='optionsmith'){
				$subdefid[] = $_POST['optionsmithproduct_1'];
				$productName="subOptionSmith";
				$producttype=getProductType($_POST['optionsmithproduct_1']);
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				if($tracking_name){
					$tracking_name.=','.$trackname;
				}else{
					$tracking_name.=$trackname;
				}
			}
			if($_POST['jackproduct'] =='jack'){
				$subdefid[] = $_POST['jack_1'];
				$productName="subLaveryInsight";
				$producttype=getProductType($_POST['jack_1']);
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				if($tracking_name){
					$tracking_name.=','.$trackname;
				}else{
					$tracking_name.=$trackname;
				}
			}
			if($_POST['etfproduct'] =='etf'){
				$subdefid[] = $_POST['etf_1'];
				$productName="subETF";
				$producttype=getProductType($_POST['etf_1']);
				if(trim(strtolower($producttype))=='3 months') $producttype='Quarterly';
				$trackname=createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from);
				if($tracking_name){
					$tracking_name.=','.$trackname;
				}else{
					$tracking_name.=$trackname;
				}
			}
		}
		if($_POST['freeemailexchng']!=''){
			$subdefid[] = $_POST['freeemailexchng'];
		}
		$comma_separated_ids = implode(",", $subdefid); // for the case of free user this will be blank so only give exchange + email alertys
		if(isset($comma_separated_ids) && ($comma_separated_ids!=''))
		{
			$orderDetails=array();
			$allProducts=$objVia->getProductDetailsArray($comma_separated_ids,'SUBSCRIPTION');// defined in _via_data_lib.php
			$actualDiscountedpricearray = getPayableProds($comma_separated_ids);// defined in _module_design_lib.php
			$indexedProducts=array();
			$index=0;
			foreach($allProducts as $key =>$value)
			{
				$indexedProducts['OrderItem'][$index]['orderClassId']=$value['oc_id'];
				$indexedProducts['OrderItem'][$index]['orderCodeId']=$value['order_code_id'];
				$indexedProducts['OrderItem'][$index]['sourceCodeId']=$actualDiscountedpricearray[$value['subscription_def_id']]['source_code_id'];
				$indexedProducts['OrderItem'][$index]['orderItemType']='SUBSCRIPTION';
				$indexedProducts['OrderItem'][$index]['typeSpecificId']=$value['subscription_def_id'];
				$indexedProducts['OrderItem'][$index]['paymentAccountNumb']=1;
				$indexedProducts['OrderItem'][$index]['qty']=$value['qty'];
				$indexedProducts['OrderItem'][$index]['price']=$actualDiscountedpricearray[$value['subscription_def_id']]['discountedPrice'];
				$index++;

			}
		}
		$cartDetails=array('billDate'=>date('Y-m-d'),'items'=>$indexedProducts);
		// Via insertion starts

		if($_POST['productselected']=='1'){ // with products
			$customerDetails=$objVia->addCustomerAndOrderAndPayment($custInfo,$cartDetails); // returns true false
		}else if($_POST['productselected']=='0'){ // only exchange
			//$customerDetails=$objVia->addCustomerAndOrder($custInfo,$cartDetails,$hardtrial=0); // returns true false
			$customerDetails=$objVia->addCustomer($custInfo,$hardtrial=0); // only add customer info, order place after activation
		}
		if($customerDetails=='true')
		{
		/* After Via Insertion done make insertion in MVIL DB Start  */
			if($_POST['emailCategories']!='')
			{
				$afterexplode=explode(",",$_POST['emailCategories']);
				$key=array_search('0',$afterexplode);
				if(empty($key))
				{
					$catgors=$_POST['emailCategories'];
					$recvdaliygazet=0;
				}
				else
				{
					unset($afterexplode[$key]);
					$catgors=implode(",",$afterexplode);
					$catgors=$catgors.",";
					$recvdaliygazet=1;
				}
				if(($catgors!='')&&(($_POST['productselected']=='1'))){$email_alert=1;}else{$email_alert=0;}
			}
			else
			{
				$email_alert=0;$catgors="";
			}
			$userdata=array('via_id'=>$objVia->customerIdVia,'email'=>$objVia->email,'fname'=>$objVia->nameFirst,'lname'=>$objVia->nameLast,'premium'=>$buzzSelected,'recv_daily_gazette'=>$recvdaliygazet);
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

				if(($_POST['authorCategories']!='')&&(($_POST['productselected']=='1'))){$authemail_alert=1;}else{$authemail_alert=0;}
				$emailalertsArray=array('subscriber_id'=>$insertid,'category_ids'=>$catgors,'email_alert'=>$email_alert);
				$authorsrray=array('subscriber_id'=>$insertid,'author_id'=>$_POST['authorCategories'],'email_alert'=>$authemail_alert);
				insert_or_update('email_alert_categorysubscribe',$emailalertsArray,array('subscriber_id'=>$insertid));
				insert_or_update('email_alert_authorsubscribe',$authorsrray,array('subscriber_id'=>$insertid));

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				RegisterUser($insertid); // defined in _exchange_lib.php

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$insertid);
				$profileid = insert_query('ex_user_profile', $subarray);

				$loginSystem = new user(); // _via_conroller_lib.php
				$isLoggedIn=$loginSystem->login($_POST['useremail'],$_POST['tuserRPassword']);
				/* --------- Set domainCookie used for Magnify SSO -----------------  */
				if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
				{
					//domaincookie("LogInUserId",session_id());
					domaincookie("sharedUserId",trim($_SESSION['SID']));
				}
				if(isset($_SESSION['AdsFree'])  && $_SESSION['AdsFree']=='1')
				{
					domaincookie("sharedAdsFreeFlag",'1');
				}

				/*-----------------------  Magnify SSO domain Cookie end ------------------ */
			}
		/* After Via Insertion done make insertion in MVIL DB End  */
			$value=array('status'=>true,'firstname'=>$_SESSION['firstname'],'lastname'=>$_SESSION['lastname'],'msg'=>'success','tracking_name'=>$tracking_name);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}
		else
		{
			$mssge=$errorObj->getExactCustomError($customerDetails);
			if($mssge==''){
				$mssge='An error occurred while processing your request. Please check your data.';
			}
			$value=array('status'=>false,'msg'=>$mssge);
			$output = $json->encode($value);
			echo strip_tags($output);
			exit;
		}
	}
	else if((isset($getType))&& ($getType=='forgotpassword')){
		$loginSystem = new user(); // _via_conroller_lib.php
		$email = $_POST['uid'];
		$fieldsArray['customerLogin']=$email;
		echo $loginSystem->checkUserViaAvailibilityByEmail($fieldsArray); // _via_controller_lib.php
	}
	else if((isset($getType))&& ($getType=='sendmail')){
		$fieldsArray=array();
		$fieldsArray['customerLogin']=trim($_POST['uid']);
		$fieldsArray['customerIdVia']=$_POST['viaid'];
		$status = $objVia->resetCustomerPassword($fieldsArray);
		if($status){
			echo "Your password has been sent.";
		}else{
			echo "";
		}
	}
?>
