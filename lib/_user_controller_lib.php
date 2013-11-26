<?php
/*
User class contains function to deal with customers.
*/
global $D_R;
include_once($D_R.'/lib/_user_data_lib.php');
class user{
		public $userDetails;
		public $userExists=1;
		public $isExchange;
		public $is_emailalerts_user;
		public $isquint;
		public $iscooper;
		public $isBuzz;
		public $id;
		public $login_sessid;
		public $login_lastactive;
		public $isAdmin;
		public $isAuthed;
		public $isActive=true;
		public $expired;
		public $signedIn;
		public $needsPremium;
		public $user_id;
		public $has_preferences;
		public $manageUrl;
		public $isAdsFree;


		public function user()
		{
			$this->manageUrl="/register/manage_setting.htm";
			$this->user_id=$this->id=$_SESSION['user_id'];
			$this->isAdmin=($_SESSION[AMADMIN]?1:0);
			$this->email=$_SESSION['email'];
			$this->signedIn=$_SESSION['signedIn'];
			$this->isAuthed=$_SESSION['isAuthed'];
			$this->isActive=$_SESSION['isActive'];
			$this->expired=$_SESSION['expired'];
			$this->needsPremium=$_SESSION['needsPremium'];
			$this->is_emailalerts_user=$_SESSION['emailalerts'];
			$this->isExchange=$_SESSION['exchange'];
			$this->isquint=$_SESSION['Flexfolio'];
			$this->iscooper=$_SESSION['Cooper'];
			$this->isBuzz=$_SESSION['Buzz'];
			$this->isAdsFree=$_SESSION['AdsFree'];
			$this->isTechStrat=$_SESSION['TechStrat'];
			$this->isGaryK=$_SESSION['GaryK'];
			$this->isHousingReport=$_SESSION['HousingReport'];
			$this->isHS_LasVegas=$_SESSION['HS-LasVegas'];
			$this->isHS_Chicago=$_SESSION['HS-Chicago'];
			$this->isHS_Phoenix=$_SESSION['HS-Phoenix'];
			$this->isHS_WashingtonDC=$_SESSION['HS-WashingtonDC'];
			$this->isHS_SanDiego=$_SESSION['HS-SanDiego'];
			$this->isHS_NewYorkMetro=$_SESSION['HS-NewYorkMetro'];
			$this->isHS_Miami=$_SESSION['HS-Miami-DadeCo'];

			$this->isHS_Atlanta=$_SESSION['HS-Atlanta'];
			$this->isHS_Dallas=$_SESSION['HS-Miami-Dallas'];
			$this->isHS_LosAngles=$_SESSION['HS-LosAngles'];
			$this->isHS_Minneapolis=$_SESSION['HS-Minneapolis'];
			$this->isHS_Portland=$_SESSION['HS-Portland'];
			$this->isHS_Orlendo=$_SESSION['HS-Orlendo'];
			$this->isHS_Seattle=$_SESSION['HS-Seattle'];
			$this->isHS_SanFrancisco=$_SESSION['HS-SanFrancisco'];
			$this->isHS_LongIsland=$_SESSION['HS-LongIsland'];

			$this->haspreference=$_SESSION['haspreference'];
		}

		public function hasPreference($user_id)
		{
			/* B&B Preference*/
			$qry2="SELECT ssid FROM subscriber_buzzbanter_preferences where SSID = " . $user_id;
			$res2=exec_query($qry2,1);
			$this->has_preferences = 1;
			if(!count($res2))
			{
				$this->has_preferences = 0;
			}
			return $this->has_preferences;
		}

		function countBuzzSubscriber(){ /*B&B contributor to subscriber count check.*/
	        $sid=$_SESSION['SID'];
	 		$getSql="SELECT count(SCF.contrib_id) countfilter FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."'";
			$getResult=exec_query($getSql,1);
			if($getResult['countfilter']){
				return $getResult['countfilter'];
			}else{
				return false;
			}
	 }

		public function isLoggedIn()
		{
			if($_SESSION['LoggedIn'])
			{
				return true;
			}
			else return false;
		}

	// Logout function
		public function logout()
		{
			unset($_SESSION['LoggedIn']);
			set_sess_vars("buzzSubscriberContributor","");
			@session_destroy();
			if($_COOKIE['email']!='')
			{
				set_sess_vars('sessautologin',1); // this is checked for autologin in _minyanville_header file
				//resetSession(0);
			}
			mcookie("email",'');
			mcookie("password",'');
			mcookie("sid",'');
			domaincookie("sharedAdsFreeFlag",'');			// --- Unset domainCookie AdsFree used for Magnify SSO----
			//mcookie("autologin",1);
			$this->logVisitTime();
			return true;
			exit;
		}


	public function getUserProductInfo($id)
	{
		$userInfoArr = array();
		$qry="
SELECT `id`,`recurly_uuid`,`recurly_current_period_ends_at`,`expireDate`,`description`, `recurly_activated_at`, `price`,`recurly_total_amount_in_cents`,`n_issues_left`,`typeSpecificId`,`orderNumber`,`recurly_plan_code` FROM subscription_cust_order
WHERE `subscription_id`='$id' AND
 (((expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR expireDate='0000-00-00 00:00:00') AND  orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))
  OR (recurly_state<>'expired' and recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
		$userProductInfoArr=exec_query($qry);
		return $userProductInfoArr;
	}

	public function getUserProductGroupInfo($id)
	{
		$userInfoArr = array();
		$qry="
SELECT sco.`id`,p.subGroup,sco.`recurly_plan_code` FROM subscription_cust_order sco, product p
WHERE p.recurly_plan_code=sco.`recurly_plan_code` AND `subscription_id`='".$id."' AND
 (((expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR expireDate='0000-00-00 00:00:00') AND  orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))
  OR (recurly_state<>'expired' and recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
		$userProductInfoArr=exec_query($qry);
		return $userProductInfoArr;
	}

	public function login($uid=NULL,$pwd=NULL,$autologin=null)
	{
		session_start();
		global $_COOKIE,$_SESSION,$productAd;
		$this->email=lc(trim($uid));
		$this->password=trim($pwd);
		if(!$this->email || !$this->password)
		{
			set_sess_vars('isAuthed',0);
			return 0;
		}
		try{
				if(isset($uid) && isset($pwd))
				{
				$objUserData= new userData();
				$userDetails=$objUserData->authenticateCustomer($uid,$pwd);
					if(!empty($userDetails)){
						
						set_sess_vars("email",$userDetails['email']);
						$email = $userDetails['email'];
						$this->user_id=$userDetails['id'];
						set_sess_vars("SID",$userDetails['id']);
						set_sess_vars("PhNo",$userDetails['tel']);
						set_sess_vars("first_name",$userDetails['fname']);
						set_sess_vars("last_name",$userDetails['lname']);
						set_sess_vars("address",$userDetails['address']);
						set_sess_vars("state",$userDetails['state']);
						set_sess_vars("city",$userDetails['city']);
						set_sess_vars("zip",$userDetails['zip']);
						set_sess_vars("EID",1);
						set_sess_vars("user_id",$userDetails['id']);
						//set_sess_vars("haspreference",$this->hasPreference($result['id']));

						set_sess_vars("nameLast",$userDetails['lname']);
						set_sess_vars("nameFirst",$userDetails['fname']);
						set_sess_vars("phone",$userDetails['tel']);

						$products = $this->getUserProductInfo($_SESSION['user_id']);

						$productsArr=array();

							foreach($products as $key => $value)
							{
								if(!empty($value['recurly_uuid']))
								{
									$order_id = $value['recurly_uuid'];
								}
								else
								{
									$order_id = $value['orderNumber'];
								}

								if(!empty($value['recurly_current_period_ends_at']))
								{
									$expiry_date = $value['recurly_current_period_ends_at'];
								}
								else
								{
									$expiry_date = $value['expireDate'];
								}

								if(!empty($value['recurly_total_amount_in_cents']))
								{
									$price = ($value['recurly_total_amount_in_cents']/100);
								}
								else
								{
									$price = $value['price'];
								}

								if(!empty($value['recurly_plan_code']))
								{
									$type_id = $value['recurly_plan_code'];
								}
								else
								{
									$type_id = $value['typeSpecificId'];
								}

								$productsArr['SUBSCRIPTION'][$type_id]=array('orderNumber'=>$order_id,'sourceCodeId'=>$value->sourceCodeId,'typeSpecificId'=>$type_id,'activationDate'=>$value['recurly_activated_at'],'expireDate'=>$expiry_date,'description'=>$value['description'],'price'=>$value->price,'orderItemType'=>'SUBSCRIPTION','n_issues_left'=>$value['n_issues_left']);
								// echo $this->userProducts[]=$value->typeSpecificId;
								$this->userProducts[]=$type_id;
								if(in_array($type_id,$productAd)){
									$_SESSION['AdsFree']='1';
								}
							}


						/*elseif(count($products)==1){

						if(!empty($products[0]['recurly_uuid']))
								{
									$order_id = $products[0]['recurly_uuid'];
								}
								else
								{
									$order_id = $products[0]['recurly_uuid'];
								}

								if(!empty($products[0]['recurly_current_period_ends_at']))
								{
									$expiry_date = $products[0]['recurly_current_period_ends_at'];
								}
								else
								{
									$expiry_date = $products[0]['expireDate'];
								}

								if(!empty($products[0]['recurly_total_amount_in_cents']))
								{
									$price = ($products[0]['recurly_total_amount_in_cents']/100);
								}
								else
								{
									$price = $products[0]['price'];
								}

								if(!empty($products[0]['recurly_plan_code']))
								{
									$type_id = $products[0]['recurly_plan_code'];
								}
								else
								{
									$type_id = $products[0]['typeSpecificId'];
								}
							$productsArr['SUBSCRIPTION'][$type_id]=array('orderNumber'=>$order_id,'sourceCodeId'=>$products->sourceCodeId,'typeSpecificId'=>$type_id,'expireDate'=>$expiry_date,'description'=>$products['description'],'price'=>$price,'orderItemType'=>'SUBSCRIPTION','n_issues_left'=>$products[0]['n_issues_left']);
							if(in_array($type_id,$productAd)){
									$_SESSION['AdsFree']='1';
							}
						}*/

						/*Get Active products */


						set_sess_vars("products",$productsArr);

						$productstatusarray = $this->getSubcriptionProductDetails($_SESSION['user_id']);
						if($productstatusarray['Buzz']==1){
						    $countBuzzContributor=$this->countBuzzSubscriber();
							if($countBuzzContributor==1){
								set_sess_vars("buzzSubscriberContributor","1");
							}
						}
						if(is_array($productstatusarray))
						{
							session_start();
							foreach($productstatusarray as $key => $value)
							{
								set_sess_vars($key,$value);
							}
						}

						set_sess_vars('isAuthed',1);
						set_sess_vars('signedIn',1);
						set_sess_vars('isActive',1); // true or false
						if($_SESSION['Buzz']==0)
						{
							//***$this->expired=1; // true or false
							set_sess_vars('expired',1);
						}
						if(($_SESSION['Buzz']==0) && $BANTER_ON){
							//not authorized to view buzz+banter. continue to get user info
							set_sess_vars('isAuthed',0);
							set_sess_vars('needsPremium',1);
						}

						if(isset($_SESSION['AdsFree'])  && $_SESSION['AdsFree']=='1')
						{
							domaincookie("sharedAdsFreeFlag",'1');
						}

						if($autologin){
							$cookieExpiration=time()+2592000; // 30 days from now
							mcookie("email",$uid,$cookieExpiration);
							mcookie("password",base64_encode($pwd),$cookieExpiration);

							mcookie("sid",$_SESSION['user_id'],$cookieExpiration);

							if(isset($_SESSION['AdsFree'])  && $_SESSION['AdsFree']=='1')
							{
								domaincookie("sharedAdsFreeFlag",'1');
							}
							/*-----------------------  Magnify SSO domain Cookie end ------------------ */
							mcookie("autologin",1,$cookieExpiration);
						}else{
							mcookie("email");
							mcookie("password");
							mcookie("sid");

							domaincookie("sharedAdsFreeFlag");
							mcookie("autologin");
						}

						set_sess_vars("EMAIL",$email);
						set_sess_vars("EID",1);
						set_sess_vars("LoggedIn",'true');


						$this->logVisit();
						return $_SESSION['LoggedIn']; // true/false

					}
					else
					{
						set_sess_vars('isAuthed',0);
						set_sess_vars('isActive',false);
						mcookie("email");
						mcookie("password");
						mcookie("sid");
						mcookie("autologin");
						// Maintain the transaction log
						return $userDetails; // error message
					}
				}
		}catch (Exception $exception)
		{
			return "Error".$exception;
		}
	}//end of function login

	function logVisit(){
		$user['subId']=$this->user_id;
		$user['time']=strtotime("now");
		if($this->userProducts){
			$user['products']=implode(",",$this->userProducts);
			set_sess_vars("activeProducts","-".implode("-",$this->userProducts)."-");
		}
		$id=insert_query("user_audit",$user);
		return $id;
	}

	function logVisitTime()
		{
			$qry="SELECT * FROM user_audit";
			$qry.=" WHERE ";
			$qry.="subId='".$this->user_id."'";
			$qry.=" AND TIME >UNIX_TIMESTAMP('".mysqlNow()."' - INTERVAL 8 HOUR)";
			$qry.=" ORDER BY ";
			$qry.="time DESC";
			$res=exec_query($qry,1);
			$user['duration']=strtotime("now") - $res['time'];
			$id=update_query("user_audit",$user,array("id"=>$res['id']));
			return $id;
	}

	function is_exchangeuser(){
			return $_SESSION['exchange'];
	}
	function is_emailalerts($userid=''){
		return $_SESSION['emailalerts']; // to be get from product function
	}

	function is_quint(){ // completely different from old one. old one was not in use in application.
		return $_SESSION['Flexfolio'];
	}

	function is_cooper(){
		return $_SESSION['Cooper'];
	}

	function is_buzz(){
		return $_SESSION['Buzz'];
	}

	function is_option(){ // new function added for option smith
		return $_SESSION['Optionsmith'];
	}

	function is_bmtpalert(){ // new function added for option smith
		return $_SESSION['BMTPAlert'];
	}

	function is_bmtp(){ // new function added for option smith
		return $_SESSION['BMTP'];
	}

	function is_jack(){ // new function added for jack
		return $_SESSION['Jack'];
	}
	function is_etf(){ // new function added for ETF Ttrader
		return $_SESSION['ETFTrader'];
	}

	function is_buyhedge(){ // new function added for BuyHedge-ETF Ttrader
		return $_SESSION['buyhedge'];
	}

	function is_theStockPlayBook(){ // new function added for ETF Ttrader
			return $_SESSION['TheStockPlayBook'];
	}
	function is_theStockPlayBookPremium(){ // new function added for TSPB Premium
			return $_SESSION['TheStockPlayBookPremium'];
	}
	function is_adFree(){ // new function added for Ads Free Minyanville
		global $_SESSION;
		return $_SESSION['AdsFree'];
	}

    function is_garyK(){ // new function added for Sean Udall
		global $_SESSION;
		return $_SESSION['GaryK'];
	}
	function is_techStrat(){ // new function added for Sean Udall
		global $_SESSION;
		return $_SESSION['TechStrat'];
	}

	function is_housingReport(){ // new function added for Housing Report
		global $_SESSION;
		return $_SESSION['HousingReport'];
	}

	function is_hsLasVegas(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-LasVegas'];
	}

	function is_hsChicago(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-Chicago'];
	}

	function is_hsPhoenix(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-Phoenix'];
	}

	function is_hsWashingtonDC(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-WashingtonDC'];
	}

	function is_hsSanDiego(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-SanDiego'];
	}

	function is_hsNewYorkMetro(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-NewYorkMetro'];
	}

	function is_hsMiami(){ // new function added for Housing Market Single Issue
		global $_SESSION;
		return $_SESSION['HS-Miami-DadeCo'];
	}

	function is_hsAtlanta(){
		return $_SESSION['HS-Atlanta'];
	}

	function is_hsDallas(){
		return $_SESSION['HS-Dallas'];
	}

	function is_hsLosAngles(){
		return $_SESSION['HS-LosAngles'];
	}

	function is_hsMinneapolis(){
		return $_SESSION['HS-Minneapolis'];
	}

	function is_hsPortland(){
		return $_SESSION['HS-Portland'];
	}

	function is_hsOrlendo(){
		return $_SESSION['HS-Orlendo'];
	}

	function is_hsSeattle(){
		return $_SESSION['HS-Seattle'];
	}

	function is_hsSanFrancisco(){
		return $_SESSION['HS-SanFrancisco'];
	}
	function is_hsLongIsland(){
		return $_SESSION['HS-LongIsland'];
	}


	// to reset user password
	public function forgotPassword($login){
		try{}//end of try block
		catch ( Exception $exception ) {}//end of catch block
	}//end  of function forgotPassword


	// to reset user password
	public function resetPassword($login){}//end  of function forgotPassword



	public function getExchangeInfo($id)
	{
		$userInfoArr = array();
		if($id!=$_SESSION['user_id'])
		{
			$qry="select via_id,id,email,fname,lname from subscription where id =$id";
			$info=exec_query($qry,1);
			$userInfoArr['email']=$info['email'];
			$userInfoArr['fname']=$info['fname'];
			$userInfoArr['lname']=$info['lname'];
			$userInfoArr['id']=$info['id'];
			return $userInfoArr;
		}
		else
		{
			$userInfoArr['email']=$_SESSION['EMAIL'];
			$userInfoArr['fname']=$_SESSION['nameFirst'];
			$userInfoArr['lname']=$_SESSION['nameLast'];
			$userInfoArr['id']=$_SESSION['user_id'];
			return $userInfoArr;
		}
	}

	//public function is_quitdisable(){} // old NOT IN USE ANYWHERE
	//****public function getInfo(){} // to be done

	function isDupeLogin(){
			//will tell if person is a dupe login
		global $REMOTE_ADDR,$_COOKIE,$HTTPS,$REG_ALLOW_MULTI_LOGIN;
		if($REG_ALLOW_MULTI_LOGIN){
			debug("REG_ALLOW_MULTI_LOGIN is on. ");
			return 0;//
		}
		if(!$_COOKIE[LOGIN_ID]){
			$this->setSessId();
		}
		$sess_id=$_COOKIE[LOGIN_ID];
		mcookie("LOGIN_ID",$sess_id);//restamp cookie
		if(!$this->signedIn){
			debug("USER:isDupLogin:not signed in");
			return 0;
		}
		if($HTTPS=="on"){
			debug("USER:isDupeLogin:don't use secure mode to check dupes");
			return 0;
		}
		if($this->isAdmin){
			debug("USER:isDupeLogin:user is an adinistrator. not a dupe");
			return 0;
		}
		$login_timeout=minute(10);
		$update_age=minute(5);//only freshen fields periodically. not every hit
		$ip=$REMOTE_ADDR;
		$upd=array();
		$now=time();
			//set defaults if nothing's there
		if(!$this->login_lastactive)
			$upd[login_lastactive]=$now;
		if(!$this->login_sessid)
			$upd[login_sessid]=$sess_id;
		if(count($upd)){
			debug("USER:isDupeLogin: update user fields");
				//this is a fresh login. just update the table
			update_query("subscription",$upd,array(id=>$this->id));
			return 0;
		}
		$login_age=$now-$this->login_lastactive;
		if($login_age<$login_timeout){
			debug("USER:isDupeLogin: within session timeout window");
				//only check dupes younger than an hour
			if( $this->login_sessid!=$sess_id ){
				debug("USER:isDupeLogin: dupe login! {$this->login_sessid}:$sess_id");
					//if they have a diff ip and session in the same hr they're crooks
				return 1;
			}else{
				debug("USER:isDupeLogin:not a dupe db_sessid:{$this->login_sessid}, cookie:$sess_id");
			}
		}else{
			debug("USER:isDupeLogin: not a dupe. {$this->login_sessid}:$sess_id, age: $login_age");
			//activity over an hour old consider user valid
			if($login_age>$update_age){
				debug("USER:isDupeLogin: fields not updated in $updateage secs. update");
				$upd=array(login_sessid=>$sess_id,login_lastactive=>$now);
				update_query(subscription,$upd,array(id=>$this->id));
			}
			return 0;
		}
	}


	function setSessId(){
		mcookie("LOGIN_ID","mv_".mrand());
	}


	function resetAuthFields(){
		//reset session field in subscription table.
		mcookie("LOGIN_ID");
		domaincookie("sharedAdsFreeFlag");
		if($this->isAuthed){
			$this->login_sessid="";
			$this->login_lastactive=0;
			$upd=array(login_sessid=>"",login_lastactive=>0);
			$cond=array(id=>$this->id);
			update_query("subscription",$upd,$cond);
		}
	}
	function resetSession($kill=0){
			$this->resetAuthFields();
			set_sess_vars("EMAIL");
			set_sess_vars("PASSWORD");
			set_sess_vars("AMADMIN");
			set_sess_vars("USERNAME");
			set_sess_vars("SID");
			if($kill){
				mcookie("email");
				mcookie("password");
				mcookie("sid");
			}
		}

	public function isAlreadyUser($loginID)
	{
		$result=array();
		$strQuery="select email from subscription where email='$loginID'";
		$result=exec_query($strQuery,1);
		if(isset($result)&&is_array($result))
		{
			if($result['email']==$loginID)
			{
				return $this->userExists;
			}
			else
			{
				$this->userExists=0;
				return $this->userExists;
			}
		}
		else
		{
			$this->userExists=0;
			return $this->userExists;
		}
	}


	function getSubcriptionProductDetails($userid='')
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $viaProducts,$_SESSION;

		$productCache = new Cache();
		$productArr = $productCache->getProductArray();

		$product=array();
		$product=$_SESSION['products']['SUBSCRIPTION'];

		$result = array();

		$result['Buzz']=0;
		$result['Cooper']=0;
		$result['peterTchir']=0;
		$result['ElliottWave']=0;
		$result['keene']=0;
		$result['Flexfolio']=0;
		$result['exchange']=0;
		$result['emailalerts']=0;
		$result['Optionsmith']=0;
		$result['BMTP']=0;
		$result['BMTPAlert']=0;
		$result['Jack']=0;
		$result['TheStockPlayBook']=0;
		$result['TheStockPlayBookPremium']=0;
		$result['TechStrat']=0;
		$result['GaryK']=0;
		$result['HousingReport']=0;
		$result['HS-LasVegas']=0;
		$result['HS-Chicago']=0;
		$result['HS-Phoenix']=0;
		$result['HS-WashingtonDC']=0;
		$result['HS-SanDiego']=0;
		$result['HS-NewYorkMetro']=0;
		$result['HS-Miami-DadeCo']=0;
		$result['HS-Atlanta']=0;
		$result['HS-Dallas']=0;
		$result['HS-LosAngles']=0;
		$result['HS-Minneapolis']=0;
		$result['HS-Portland']=0;
		$result['HS-Orlendo']=0;
		$result['HS-Seattle']=0;
		$result['HS-SanFrancisco']=0;
		$result['HS-LongIsland']=0;
		if(!$_SESSION['AdsFree']){
		$result['AdsFree']=0;
		}

		if(is_array($productArr))
		{

		$buzzArr= array_merge($productArr['buzz']['plan_code'],$productArr['buzz']['typeSpecificId']);
		$searchBuzz= $buzzArr;
		foreach($searchBuzz as $key=>$value){
			if(is_array($product))
				$buzzprod=array_key_exists($value,$product);
			if($buzzprod=="1"){
				$result['Buzz']=1;
			}
		}

		$flexArr= array_merge($productArr['flexfolio']['plan_code'],$productArr['flexfolio']['typeSpecificId']);
		$searchFlex= $flexArr;
		foreach($searchFlex as $key=>$value){
			if(is_array($product))
				$flexprod=array_key_exists($value,$product);
			if($flexprod=="1"){
				$result['Flexfolio']=1;
			}
		}

		$jeffArr= array_merge($productArr['cooper']['plan_code'],$productArr['cooper']['typeSpecificId']);
		$searchjeff= $jeffArr;
		foreach($searchjeff as $key=>$value){
			if(is_array($product))
				$jeffprod=array_key_exists($value,$product);
			if($jeffprod=="1"){
				$result['Cooper']=1;
			}
		}

		$searchExchange=array(15);
		foreach($searchExchange as $key=>$value)
		{
			if(is_array($product))
				$exchangeprod=array_key_exists($value,$product);
			if($exchangeprod=="1"){
				$result['exchange']=1;
			}
		}

		$optionsmithArr= array_merge($productArr['optionsmith']['plan_code'],$productArr['optionsmith']['typeSpecificId']);
		$searchoptionsmith= $optionsmithArr;
		foreach($searchoptionsmith as $key=>$value){
			if(is_array($product))
				$optionsmithprod=array_key_exists($value,$product);
			if($optionsmithprod=="1"){
				$result['Optionsmith']=1;
			}
		}

		$bmtpArr= array_merge($productArr['bmtp']['plan_code'],$productArr['bmtp']['typeSpecificId']);
		$searchbmtpalert= $bmtpArr;

		foreach($searchbmtpalert as $key=>$value){
			if(is_array($product))
				$bmtpalertprod=array_key_exists($value,$product);
			if($bmtpalertprod=="1" && $product[$value]['orderItemType']==$viaProducts['BMTPAlert']['orderItemType']){
				$result['BMTPAlert']=1;
			}
		}

		$searchjack=array(
			$viaProducts['JackMonthlyTrial']['typeSpecificId'],
			$viaProducts['JackAnnualTrial']['typeSpecificId'],
			$viaProducts['JackMonthly']['typeSpecificId'],
			$viaProducts['JackAnnual']['typeSpecificId'],
			$viaProducts['JackComplimentary']['typeSpecificId'],
			$viaProducts['JackCK']['typeSpecificId'],
			$viaProducts['JackST']['typeSpecificId']
		);
		foreach($searchjack as $key=>$value){
			if(is_array($product))
				$jackprod=array_key_exists($value,$product);
			if($jackprod=="1"){
				$result['Jack']=1;
			}
		}

		$grailetfArr= array_merge($productArr['grailetf']['plan_code'],$productArr['grailetf']['typeSpecificId']);
		$searchETF= $grailetfArr;
		foreach($searchETF as $key=>$value){
			if(is_array($product))
				$etfprod=array_key_exists($value,$product);
			if($etfprod=="1"){
				$result['ETFTrader']=1;
			}
		}

		$buyhedgeArr= array_merge($productArr['buyhedge']['plan_code'],$productArr['buyhedge']['typeSpecificId']);
		$searchBuyHedge= $buyhedgeArr;
		foreach($searchBuyHedge as $key=>$value){
			if(is_array($product))
				$BuyHedgeprod=array_key_exists($value,$product);
			if($BuyHedgeprod=="1"){
				$result['buyhedge']=1;
			}
		}

		$thestockplaybookArr= array_merge($productArr['thestockplaybook']['plan_code'],$productArr['thestockplaybook']['typeSpecificId']);
		$searchTheStockPlayBook= $thestockplaybookArr;
		foreach($searchTheStockPlayBook as $key=>$value){
			if(is_array($product))
				$theStockPlayBookprod=array_key_exists($value,$product);
			if($theStockPlayBookprod=="1"){
				$result['TheStockPlayBook']=1;
			}
		}

		$thestockplaybookpremiumArr= array_merge($productArr['thestockplaybookpremium']['plan_code'],$productArr['thestockplaybookpremium']['typeSpecificId']);
		$searchTheStockPlayBookPremium= $thestockplaybookpremiumArr;
		foreach($searchTheStockPlayBookPremium as $key=>$value){
			if(is_array($product))
				$theStockPlayBookPremiumprod=array_key_exists($value,$product);
			if($theStockPlayBookPremiumprod=="1"){
				$result['TheStockPlayBookPremium']=1;
			}
		}


		$adsfreeArr= array_merge($productArr['adsfree']['plan_code'],$productArr['adsfree']['typeSpecificId']);
		$searchAdFree= $adsfreeArr;
		foreach($searchAdFree as $key=>$value){
			if(is_array($product))
				$adFreeprod=array_key_exists($value,$product);
			if($adFreeprod=="1"){
				$result['AdsFree']=1;
			}
		}

		$techstratArr= array_merge($productArr['techstrat']['plan_code'],$productArr['techstrat']['typeSpecificId']);
		$searchTechStrat= $techstratArr;
		foreach($searchTechStrat as $key=>$value){
			if(is_array($product))
				$TechStratProd=array_key_exists($value,$product);
			if($TechStratProd=="1"){
				$result['TechStrat']=1;
			}
		}

		$peterTchirArr= array_merge($productArr['peterTchir']['plan_code'],$productArr['peterTchir']['typeSpecificId']);
		$searchpeterTchir= $peterTchirArr;
		foreach($searchpeterTchir as $key=>$value){
			if(is_array($product))
				$TechStratProd=array_key_exists($value,$product);
			if($TechStratProd=="1"){
				$result['peterTchir']=1;
			}
		}

		$elliottWaveArr= array_merge($productArr['ElliottWave']['plan_code'],$productArr['ElliottWave']['typeSpecificId']);
		$searchElliottWave= $elliottWaveArr;
		foreach($searchElliottWave as $key=>$value){
			if(is_array($product))
				$ewiProd=array_key_exists($value,$product);
			if($ewiProd=="1"){
				$result['ElliottWave']=1;
				set_sess_vars('ewiActivationDate',$product[$value]['activationDate']);
			}
		}
		
		$keeneArr= array_merge($productArr['keene']['plan_code'],$productArr['keene']['typeSpecificId']);
		$searchKeene = $keeneArr;
		foreach($searchKeene as $key=>$value){
			if(is_array($product))
				$keeneProd = array_key_exists($value,$product);
			if($keeneProd=="1"){
				$result['keene']=1;
			}
		}

		$garykArr= array_merge($productArr['garyk']['plan_code'],$productArr['garyk']['typeSpecificId']);
		$searchGaryK= $garykArr;
		foreach($searchGaryK as $key=>$value){
			if(is_array($product))
				$GarykProd=array_key_exists($value,$product);
			if($GarykProd=="1"){
				$result['GaryK']=1;
			}
		}


		$housingmarketArr= array_merge($productArr['housingmarket']['plan_code'],$productArr['housingmarket']['typeSpecificId']);
		$searchHousingReport= $housingmarketArr;
		foreach($searchHousingReport as $key=>$value){
			if(is_array($product))
				$HousingReportProd=array_key_exists($value,$product);
			if($HousingReportProd=="1"){
				$result['HousingReport']=1;
			}
		}

		//For Housing market Single Issue
		$searchHSLasVegas=array(
			$viaProducts['LasVegas']['typeSpecificId'],
			$viaProducts['LasVegas']['plan_code']
		);
		foreach($searchHSLasVegas as $key=>$value){
			if(is_array($product))
				$HSLasVegasProd=array_key_exists($value,$product);
			if($HSLasVegasProd=="1"){
				$result['HS-LasVegas']=1;
			}
		}

		$searchHSChicago=array(
			$viaProducts['Chicago']['typeSpecificId'],
			$viaProducts['Chicago']['plan_code']
		);
		foreach($searchHSChicago as $key=>$value){
			if(is_array($product))
				$HSChicagoProd=array_key_exists($value,$product);
			if($HSChicagoProd=="1"){
				$result['HS-Chicago']=1;
			}
		}

		$searchHSPhoenix=array(
			$viaProducts['Phoenix']['typeSpecificId'],
			$viaProducts['Phoenix']['plan_code']
		);
		foreach($searchHSPhoenix as $key=>$value){
			if(is_array($product))
				$HSPhoenixProd=array_key_exists($value,$product);
			if($HSPhoenixProd=="1"){
				$result['HS-Phoenix']=1;
			}
		}

		$searchHSWashingtonDC=array(
			$viaProducts['WashingtonDC']['typeSpecificId'],
			$viaProducts['WashingtonDC']['plan_code']
		);
		foreach($searchHSWashingtonDC as $key=>$value){
			if(is_array($product))
				$HSWashingtonDCProd=array_key_exists($value,$product);
			if($HSWashingtonDCProd=="1"){
				$result['HS-WashingtonDC']=1;
			}
		}

		$searchHSSanDiego=array(
			$viaProducts['SanDiego']['typeSpecificId'],
			$viaProducts['SanDiego']['plan_code']
		);
		foreach($searchHSSanDiego as $key=>$value){
			if(is_array($product))
				$HSSanDiegoProd=array_key_exists($value,$product);
			if($HSSanDiegoProd=="1"){
				$result['HS-SanDiego']=1;
			}
		}

		$searchHSNewYorkMetro=array(
			$viaProducts['NewYorkMetro']['typeSpecificId'],
			$viaProducts['NewYorkMetro']['plan_code']
		);
		foreach($searchHSNewYorkMetro as $key=>$value){
			if(is_array($product))
				$HSNewYorkMetroProd=array_key_exists($value,$product);
			if($HSNewYorkMetroProd=="1"){
				$result['HS-NewYorkMetro']=1;
			}
		}

		$searchHSMiami=array(
			$viaProducts['Miami']['typeSpecificId'],
			$viaProducts['Miami']['plan_code']
		);
		foreach($searchHSMiami as $key=>$value){
			if(is_array($product))
				$HSMiamiProd=array_key_exists($value,$product);
			if($HSMiamiProd=="1"){
				$result['HS-Miami-DadeCo']=1;
			}
		}

		$searchHSAtlanta=array(
			$viaProducts['Atlanta']['typeSpecificId'],
			$viaProducts['AtlantaComp']['typeSpecificId'],
			$viaProducts['Atlanta']['plan_code'],
			$viaProducts['AtlantaComp']['plan_code']
		);
		foreach($searchHSAtlanta as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Atlanta']=1;
			}
		}

		$searchHSDallas=array(
			$viaProducts['Dallas']['typeSpecificId'],
			$viaProducts['DallasComp']['typeSpecificId'],
			$viaProducts['Dallas']['plan_code'],
			$viaProducts['DallasComp']['plan_code']
		);
		foreach($searchHSDallas as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Dallas']=1;
			}
		}

		$searchHSLosAngles=array(
			$viaProducts['LosAngles']['typeSpecificId'],
			$viaProducts['LosAnglesComp']['typeSpecificId'],
			$viaProducts['LosAngles']['plan_code'],
			$viaProducts['LosAnglesComp']['plan_code']
		);
		foreach($searchHSLosAngles as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-LosAngles']=1;
			}
		}

		$searchHSMinneapolis=array(
			$viaProducts['Minneapolis']['typeSpecificId'],
			$viaProducts['MinneapolisComp']['typeSpecificId'],
			$viaProducts['Minneapolis']['plan_code'],
			$viaProducts['MinneapolisComp']['plan_code']
		);
		foreach($searchHSMinneapolis as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Minneapolis']=1;
			}
		}

		$searchHSPortland=array(
			$viaProducts['Portland']['typeSpecificId'],
			$viaProducts['PortlandComp']['typeSpecificId'],
			$viaProducts['Portland']['plan_code'],
			$viaProducts['PortlandComp']['plan_code']
		);
		foreach($searchHSPortland as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Portland']=1;
			}
		}

		$searchHSOrlendo=array(
			$viaProducts['Orlendo']['typeSpecificId'],
			$viaProducts['OrlendoComp']['typeSpecificId'],
			$viaProducts['Orlendo']['plan_code'],
			$viaProducts['OrlendoComp']['plan_code']
		);
		foreach($searchHSOrlendo as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Orlendo']=1;
			}
		}

		$searchHSSeattle=array(
			$viaProducts['Seattle']['typeSpecificId'],
			$viaProducts['SeattleComp']['typeSpecificId'],
			$viaProducts['Seattle']['plan_code'],
			$viaProducts['SeattleComp']['plan_code']
		);
		foreach($searchHSSeattle as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-Seattle']=1;
			}
		}

		$searchHSSanFrancisco=array(
			$viaProducts['SanFrancisco']['typeSpecificId'],
			$viaProducts['SanFranciscoComp']['typeSpecificId'],
			$viaProducts['SanFrancisco']['plan_code'],
			$viaProducts['SanFranciscoComp']['plan_code']
		);
		foreach($searchHSSanFrancisco as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-SanFrancisco']=1;
			}
		}

		$searchHSLongIsland=array(
			$viaProducts['LongIsland']['typeSpecificId'],
			$viaProducts['LongIsland']['plan_code']
		);
		foreach($searchHSLongIsland as $key=>$value){
			if(array_key_exists($value,$product) == 1)
			{
				$result['HS-LongIsland']=1;
			}
		}


		} // End of check is_array(product)

		// Products search
		$product=$_SESSION['products']['PRODUCT'];
		$searchbmtp=array(
			$viaProducts['BMTP']['typeSpecificId'],
			$viaProducts['BMTPComplimentary']['typeSpecificId'],
			$viaProducts['BMTPCK']['typeSpecificId']
		);

		foreach($searchbmtp as $key=>$value){
			if(is_array($product))
				$bmtpprod=array_key_exists($value,$product);
			if($bmtpprod=="1"){
				$result['BMTP']=1;
			}
		}
		/* Email alerts check */
		$emailalertuser_qry="select category_ids from email_alert_categorysubscribe where subscriber_id='$userid' and email_alert='1' union select author_id from email_alert_authorsubscribe where subscriber_id='$userid' and email_alert='1'";
		$qryresult=exec_query($emailalertuser_qry,1);
		if(count($qryresult)){
			$result['emailalerts']=1;
		}
		else
		{
			$result['emailalerts']=0;
		}
		if($result){
			$this->filterCombo();
			//unset($_SESSION['products']);
			return $result;
		}
	}

	function filterCombo(){
		foreach($_SESSION['products'] as $key => $val){

			if($key=='1' && $val['sourceCodeId']=='2'){
				$_SESSION['combo']=$val;
				unset($_SESSION['products']['1']);
				//unset($_SESSION['products']['4']);
				//unset($_SESSION['products']['9']);
			}
			if($key=='2' && $val['sourceCodeId']=='2'){
				$_SESSION['combo']=$val;
				unset($_SESSION['products']['2']);
				//unset($_SESSION['products']['4']);
				//unset($_SESSION['products']['13']);
			}
		}
	}
	public function saveUserData($tablename,$userdata){
		return $insertid = insert_query($tablename,$userdata);
	}



	function setBolckedIP($loginid){
	   $params['subscription_id']=$loginid;
	   $params['ip']=$_SERVER['REMOTE_ADDR'];
	   $condition= array('subscription_id' => $loginid);
	   $id=insert_or_update('bocked_ip',$params,$condition);
	   return $id;
	}

	function setEmailAletsAfterActivation($subid){

		$params['email_alert']=1;
		$conditions['subscriber_id']=$subid;
		$qryauthor="select author_id from email_alert_authorsubscribe where subscriber_id='".$subid."'";
		$getAuthResult=exec_query($qryauthor,1);
		if($getAuthResult['author_id']){
			$uaid=update_query("email_alert_authorsubscribe",$params,$conditions);
		}
		$qrycategory="select category_ids from email_alert_categorysubscribe where subscriber_id='".$subid."'";
		$getCatResult=exec_query($qrycategory,1);
		if($getCatResult['category_ids']){
			$ucid=update_query("email_alert_categorysubscribe",$params,$conditions);
		}

		$qrySubSection="select section_ids from email_alert_sectionsubscribe where subscriber_id='".$subid."'";
		$getSubsectionResult=exec_query($qrySubSection,1);
		if($getSubsectionResult['section_ids']){
			$usid=update_query("email_alert_sectionsubscribe",$params,$conditions);
		}
	}

	function getDailyFeedUser($subid){
		$qry = "select category_ids from email_alert_categorysubscribe where subscriber_id = '$subid'";
		$result=exec_query($qry,1);
		if($result['category_ids']==",7,"){
			return 1;
		}else {
			return 0;
		}
    }
   function getTopicUser($subid,$sec_id){
		$qry = "select section_ids from email_alert_sectionsubscribe where subscriber_id ='$subid'";
		$result=exec_query($qry,1);
		$res_sec=explode(',',$result['section_ids']);
		if (in_array($sec_id,$res_sec))
		   {


		      return 1;
		   }
		  else
		   {
		      return 0;
		   }
	}

     function forgotPass($userEmail)
	 {

		$user_id = $this->checkUserAvailablity($userEmail);
		if($user_id>0)
		{
			$password = rand(100000, 99999999);
			$objUserData= new userData();
		    $user['password']=$objUserData->encryptUserPassword($password);
			if($user['password']=="")
		    {
		    	$user['password']=$objUserData->encryptUserPassword("minyan");
		    }
			$updateResult=update_query("subscription",$user,array("id"=>$user_id));
			return $user_id;
		}
		else
		{
		     return false;
		}
	 }

	 function checkUserAvailablity($userEmail)
	 {
		$getSql="select id,email from subscription where email='".$userEmail."'";
		$getResult=exec_query($getSql,1);
		if(!empty($getResult['id'])){
			return $getResult['id'];
		}
		else
		{
		         return false;
		}

	 }

	 function sendForgotPasswordMail($email,$id)
	 {
		global $D_R,$HTPFX,$HTHOST;
		$path="/emails/_eml_new_forgotpass.htm?id=".$id;
		$toemail = $email;
		$from = "support@minyanville.com";
		$subject= "Your Minyanville password has been reset";
		$EML_TMPL=$HTPFX.$HTHOST.$path;
		$bcc="support@minyanville.com";
		mymail($toemail,$from,$subject,inc_web($EML_TMPL),'','','','',$bcc);
	}

	function setUsersPassword($userId,$userPassword){
		$getSql="select id from subscription where email='".$userId."'";
		$getResult=exec_query($getSql,1);
		if(!empty($getResult['id'])){
			$user['password']=base64_encode($userPassword);
			$updateResult=update_query("subscription",$user,array("id"=>$getResult['id']));
		}
	}

	private function sendemail($message,$product)
	{
		global $D_R,$HTPFX,$HTHOST,$mailchimpProductArr,$mailChimpApiKey,$productTemplateId,$productListId,$productFromName;
		include_once($D_R."/lib/config/_mailchimp_config.php");
		
		$qryGetProductSubDefId="SELECT subscription_def_id FROM product WHERE subGroup='".$product."' AND subscription_def_id IS NOT NULL";
		$resGetProductSubDefId=exec_query($qryGetProductSubDefId);
		foreach($resGetProductSubDefId as $key=>$val)
		{
			$productSubDefIdArr[] = $val['subscription_def_id'];
		}
		$subDefIds=implode("','",$productSubDefIdArr);
		$qryGetProductRecurlyPlanCode="SELECT recurly_plan_code FROM product WHERE subGroup='".$product."'";
		$resGetProductRecurlyPlanCode=exec_query($qryGetProductRecurlyPlanCode);
		foreach($resGetProductRecurlyPlanCode as $key1=>$val1)
		{
			$productRecurlyPlanCodeArr[] = $val1['recurly_plan_code'];
		}
		$planCodes=implode("','",$productRecurlyPlanCodeArr);
		$qry= "SELECT S.id,S.`email`,S.`fname`,S.`lname` FROM
`subscription_cust_order` SCO , `subscription` S
WHERE
SCO.recurly_state<>'expired' AND SCO.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d') AND SCO.recurly_plan_code
IN('".$planCodes."') AND SCO.subscription_id=S.id
UNION
SELECT S.id,S.`email`,S.`fname`,S.`lname` FROM
`subscription_cust_order` SCO , `subscription` S
WHERE
SCO.subscription_id=S.id AND SCO.typeSpecificId IN
('".$subDefIds."') AND ((SCO.expireDate >= DATE_FORMAT(NOW(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00')
 AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))";

		$getResult=exec_query($qry,0);
		foreach($getResult as $key=>$val){
			if($product=='keene'){
				$chkForAlertPref = "select alert_pref from subscription_alert_pref where subscription_id='".$val['id']."'";
				$resForAlertPref = exec_query($chkForAlertPref,1);
				if($resForAlertPref['alert_pref']=='email' || $resForAlertPref['alert_pref']='both'){
					$email_listArr[$val['email']] = $val['fname']." ".$val['lname'];
				}
			}else{
				$email_listArr[$val['email']] = $val['fname']." ".$val['lname'];
			}
		}

	 	$from= $message['FromAddress']; //  "support@minyanville.com";
		$subject= $message['Subject'];
	 	$body=$message['Body'];

	    require_once $D_R.'/lib/swift/lib/swift_required.php';
	    $mailer = Swift_MailTransport::newInstance();
		$message = Swift_Message::newInstance();

		if(in_array($product,$mailchimpProductArr))
		{
			sendProductMails($subject,$body,$product);
		}
		else
		{
			 
			$this->sendProductEmail($from,$subject,$body,$email_listArr,$message,$mailer);
		}
	}

function sendProductEmail($from,$subject,$body,$to,$message,$mailer){
    
	/*$mailbody=$body;
	$message->setSubject($subject);
	$message->setBody($mailbody, 'text/html');
    $message->setFrom($from);*/

	$failedRecipients = array();
	$numSent = 0;

	foreach ($to as $address => $name)
	{
		try{
			  if (is_int($address)) {
			    $to=$name;
			  } else {
			    $to=array($address => $name);
			  }

			  $numSent +=  mymail($to,$from,$subject,$body);
		}
	  catch (Exception $e)
	  {
	  	echo "Mail could not be sent to ".$address;
	  }
	}
}

	public function emailDetails($from,$subject,$mailbody,$product){
		global $productOrder;
		$yesterday = date("Y-m-d\TH:i:s", strtotime("-3 hour", strtotime(date("Y-m-d H:i:s"))));
		$message=array(
					   'Name'=>'Minyanville Support',
					   'SubmitDate'=>$yesterday,
					   'Subject'=>$subject,
					   'Body'=>$mailbody,
					   'FromAddress'=>$from,
					   'IsHTML'=>true,
					   'IsTemplate'=>false
						);

		return $this->sendemail($message,$product);
	}

}//end of class User
?>
