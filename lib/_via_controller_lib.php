<?php
/*
 This class contains methods to execute via library methods
 These class methods will identify via actions to perform and creates necessary
 arrays and parameters to pass via library.
 */

// include via library
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/_includes.php");
$logObj=new ViaException();

// class definition
class ViaController {

	// member variables

	//  via library object to communicate with Via library
	public $viaLibObj=null;


	// constructor
	public function __construct(){

		try{
			$this->viaLibObj=new Via();
			//echo '<br>Via library object created.<br>';
		}//end of try block
		catch ( Exception $exception ) {
			// Error handling
			echo "<br><font color='red'>Cannot create Via Library Object.</font><br>";
		}//end of catch block

	}//end of constructor


	// to get custIdent array which will contain merchantLogin and merchantPassword
	public function createCustIdentElement(){

		$merchantLogin=null;
		$merchantPassword=null;

		// if merchantLogin and merchantPassword are set
		if($this->viaLibObj->merchantLogin && $this->viaLibObj->merchantPassword){
			$merchantLogin=$this->viaLibObj->merchantLogin;
			$merchantPassword=$this->viaLibObj->merchantPassword;
		}
		else{
			exit;
		}

		// create custIdent array to communicate with Via
		$custIdent=array(
			'merchantLogin'=>$merchantLogin,
			'merchantPassword'=>$merchantPassword
		);

		return $custIdent;

	}//end of function createCustIdentElement


	// to get sourcecodeid
	public function getSourceCodeId(){

		// if sourcecodeid is not set
		if(!$this->viaLibObj->sourceCodeId){
			exit;
		}

		return $this->viaLibObj->sourceCodeId;

	}//end of function getSourceCodeId


	// create cust array in
	public function createCustElement($loginInfo,$addresses=null,$paymentAccounts=null){

		$cust=array(
			'loginInfo'=>$loginInfo,
			'addresses'=>$addresses,
			'paymentAccounts'=>$paymentAccounts
		);

		return $cust;

	}//end of function createCustElement

	public function sendSoftTrialEmails($id, $recipient_email, $event, $pd, $userName){
		global $D_R,$HTHOST,$HTPFX, $REG_EML_REPLYTO,$SUBSCRIPTION_EML_REPLYTO;
		$res = exec_query("select email_subject,template_path from ex_email_template where event='".$event."'",1);
		$subject=$res['email_subject'];
		if(isset($res['template_path'])){
			$path=$res['template_path'];
			$INVITE_EML_TMPL="$HTPFX$HTHOST".$path;
			// send by bulk mailer
			$text=$recipient_email;
			$exists = true;
			$msgfile="$D_R/assets/welcome_emails/".getrandnum()."_welcome.eml";//getrandnum() _module_design_lib.php
			$list="$D_R/assets/welcome_emails/".getrandnum()."_list";
			while($exists){
				if(is_file($msgfile) || is_file($msghtmlfile) ){
					$msgfile="$D_R/assets/welcome_emails/".getrandnum()."_welcome.eml";
					$list="$D_R/assets/welcome_emails/".getrandnum()."_list";
				}else{
					$exists = false;
				}
			}
			write_file($list,$text);
			$msghtmlfile=$msgfile;
			//write out file
			$msgurl="$INVITE_EML_TMPL?event=$event&id=$id&recipient_email=$recipient_email&name=$userName&pd=$pd";
			$mailbody=inc_web($msgurl);
			//write out file as web page
			write_file($msghtmlfile,$mailbody);
			//write out file to email format
			write_file($msgfile, mymail2str($recipient_email,$SUBSCRIPTION_EML_REPLYTO,$subject,$mailbody) );
			//spam to everyone.
			softtrial_bulk_mailer($list,$msgfile,$SUBSCRIPTION_EML_REPLYTO);
		}
	}

	function isSetAdsFree(){
		global $_SESSION, $viaProducts, $productAd, $arrcases;
		if($_SESSION['AdsFree']){
			$case = $arrcases['already_have_subscribed_product'];
			echo  trim($sdefid.', ,'.$case) ;
			return true;

		}
		else{
			foreach($_SESSION['viacart']['SUBSCRIPTION'] as $key=>$value){
				if ($value['oc_id']==$viaProducts['AdFreeMonthly']['orderClassId'] || in_array($value['subscription_def_id'],$productAd)){
					$case = $arrcases['already_adfree_added'];
					echo  trim($sdefid.','.$sdefid.','.$case);
					return true;
				}
			}
		}
		return false;
	}

	function isValidateAdsFree(){
		global $_SESSION, $viaProducts, $productAd, $arrcases;
		if($_SESSION['AdsFree']){
			$case = $arrcases['adfree_remove_cart'];
			return $case;
		}
		return false;
	}


}//end of class ViaController
//=============================================================================================


/*
 User class contains function to deal with customers.
 */
class user
{
	public $viaObj=null;
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


	public function user($userid=null,$pwd=null)
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
		domaincookie("sharedUserId",'');			// --- Unset domainCookie used for Magnify SSO----
		domaincookie("sharedAdsFreeFlag",'');			// --- Unset domainCookie AdsFree used for Magnify SSO----
		//mcookie("autologin",1);
		$this->logVisitTime();
		return true;
		exit;
	}
	// to login user using Via authentication
	public function initSoapObject(){
		$this->viaObj=new Via();
	}

	public function login($uid=NULL,$pwd=NULL,$autologin=null)
	{
		session_start();
		global $_COOKIE,$BANTER_ON,$_SESSION;
		$this->email=lc(trim($uid));
		$this->password=trim($pwd);
		if(!$this->email || !$this->password)
		{
			set_sess_vars('isAuthed',0);
			return 0;
		}
		// Check if user is a blocked User
		/*$loginqry="select * from blocked_emails WHERE email = '".$this->email."'";
		$loginresult=exec_query($loginqry,1);
		if(is_array($loginresult) && count($loginresult) > 0)
		{
			return "blocked";
		}*/
		try{
			$this->initSoapObject();
			if(isset($uid) && isset($pwd) && isset($this->viaObj))
			{
				$userDetails=$this->viaObj->authenticateCustomer($uid,$pwd);
				if(is_object($userDetails))
				{
					
					$userEmail=trim($uid);
					$password=$pwd;
					$setPass=$this->setUsersPassword($userEmail,$password);	

					//*get auxilary account activated field from via*/
					$inactiveaccount=0;
					$getAuxField=$userDetails->CustomerAuthenticationResult->customer->auxFields->field;
					if(is_array($getAuxField))
					{
						foreach($getAuxField as $auxvalue){
							if($auxvalue->name=='account_activated'){
								$inactiveaccount=$auxvalue->value;
							}
						}
					}
					else
					{
						if($userDetails->CustomerAuthenticationResult->customer->auxFields->field->name=='account_activated'){
							$inactiveaccount=$userDetails->CustomerAuthenticationResult->customer->auxFields->field->value;
						}
					}
					// $inactiveaccount=0;
					if(!$inactiveaccount){
						set_sess_vars("notactivatedemail",$uid);
						set_sess_vars("action","inactiveaccount");
						set_sess_vars("via_inactive_id",$userDetails->CustomerAuthenticationResult->customerId);
						return "Inactive account";
					}else{

						global $_SESSION,$productAd;
						session_start();
						unset($_SESSION['action']);
						unset($_SESSION['notactivatedemail']);
						set_sess_vars("email",$userDetails->CustomerAuthenticationResult->customer->loginInfo->login);
						$email = $userDetails->CustomerAuthenticationResult->customer->loginInfo->login;
						set_sess_vars("viaid",$userDetails->CustomerAuthenticationResult->customerId);

						/* get the subscription table id for this viaid */
						$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
						$result=exec_query($strQuery,1);

						/** Author: Aswini Nayak Date: 14/01/2009
						 * Here we are first checking the viaid from subscription table if its not present
						 * that means the user is an existing MVIL user and viaid not updated while data migrated from old system to via
						 * So we are updating the viaid on the base of emailid
						 **/

						/*update ip of user in case of login*/
						//$updateip = array(ip=>$_SERVER['REMOTE_ADDR']);
						//$ip_up = update_query(subscription,$updateip,array(email=>$_SESSION['email']));


						if(empty($result) && isset($result)){
							$arrsubscription = array(via_id=>$_SESSION['viaid']);
							$subps_up = update_query(subscription,$arrsubscription,array(email=>$_SESSION['email']));
							$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
							$resultUpdate=exec_query($strQuery,1);

							/** Author: Aswini Nayak Date: 06/02/2009
							 * Here we are trying to insert the records in our db
							 * ViaAdmin creates user which is not reflecting at our DB
							 * So we are inserting the record in our DB
							 **/
							if(empty($resultUpdate) && isset($resultUpdate))
							{
								$userdata=array('via_id'=>$userDetails->CustomerAuthenticationResult->customerId,'email'=>$email,'fname'=>$userDetails->CustomerAuthenticationResult->customer->nameFirst,'lname'=>$userDetails->CustomerAuthenticationResult->customer->nameLast);
								$insertedUserId=$this->saveUserData('subscription',$userdata);
								//*** $resultUpdate['id']=$insertedUserId;
								$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
								$resultUpdate=exec_query($strQuery,1);
							}
							$result=$resultUpdate;
						}

						/*update ip of user in case of login*/
						$this->user_id=$result['id'];
						set_sess_vars("SID",$result['id']);
						/* ------ --- Set domainCookie used for Magnify SSO -----------------  */
						if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
						{
							domaincookie("sharedUserId",trim($_SESSION['SID']));
						}

						/*-----------------------  Magnify SSO domain Cookie end ------------------ */

						set_sess_vars("user_id",$result['id']);
						set_sess_vars("haspreference",$this->hasPreference($result['id']));
						unset($result);
						set_sess_vars("nameLast",$userDetails->CustomerAuthenticationResult->customer->nameLast);
						set_sess_vars("nameFirst",$userDetails->CustomerAuthenticationResult->customer->nameFirst);
						set_sess_vars("ccType",$userDetails->CustomerAuthenticationResult->customer->ccType);
						/* Prouct Info get and set in session */
						$paymentRenew=array('AUTO_RENEW'=>1,'MANUAL_RENEW'=>0,'AUTO_RENEW_BILL'=>0,'DO_NOT_RENEW'=>0);
						$products = $userDetails->CustomerAuthenticationResult->orders;
						//set user orderItmType = PRODUCT info in session

						$productsArr=array();

						if(count($products)>1){
							foreach($products as $key => $value)
							{
								$productsArr[$value->orderItemType][$value->typeSpecificId]=array('orderNumber'=>$value->orderNumber,'orderItemSeq'=>$value->orderItemSeq,'orderClassId'=>$value->orderClassId,'orderCodeId'=>$value->orderCodeId,'sourceCodeId'=>$value->sourceCodeId,'typeSpecificId'=>$value->typeSpecificId,'startDate'=>$value->startDate,'expireDate'=>$value->expireDate,'billDate'=>$value->billDate,'description'=>$value->description,'price'=>$value->price,'auto_renew'=>$paymentRenew[$value->auto_renew],'subscriptionId'=>$value->subscriptionId,'orderItemType'=>$value->orderItemType,'n_issues_left'=>$value->n_issues_left);
								// echo $this->userProducts[]=$value->typeSpecificId;
								$this->userProducts[]=$value->typeSpecificId;
								if(in_array($value->typeSpecificId,$productAd)){
									$_SESSION['AdsFree']='1';
								}
							}
						}elseif(count($products)==1){
							//echo $products->typeSpecificId;
							$productsArr[$products->orderItemType][$products->typeSpecificId]=array('orderNumber'=>$products->orderNumber,'orderItemSeq'=>$products->orderItemSeq,'orderClassId'=>$products->orderClassId,'orderCodeId'=>$products->orderCodeId,'sourceCodeId'=>$products->sourceCodeId,'typeSpecificId'=>$products->typeSpecificId,'startDate'=>$products->startDate,'expireDate'=>$products->expireDate,'billDate'=>$products->billDate,'description'=>$products->description,'price'=>$products->price,'auto_renew'=>$paymentRenew[$products->auto_renew],'subscriptionId'=>$products->subscriptionId,'orderItemType'=>$products->orderItemType,'n_issues_left'=>$products->n_issues_left);
							if(in_array($value->typeSpecificId,$productAd)){
								$_SESSION['AdsFree']='1';
							}
						}
						set_sess_vars("products",$productsArr);

						$this->getCustomerSubInfo();
						$productstatusarray = $this->getSubcriptionProductDetails($_SESSION['user_id']);
						if($productstatusarray['Buzz']==1){
						$countBuzzContributor=$this->countBuzzSubscriber();
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
							mcookie("password",$pwd,$cookieExpiration);
							mcookie("sid",$_SESSION['user_id'],$cookieExpiration);
							/* ------ --- Set domainCookie used for Magnify SSO -----------------  */
							if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
							{
								domaincookie("sharedUserId",trim($_SESSION['SID']));
							}
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
							domaincookie("sharedUserId");		//--------------- Unset domainCookie for Magnify SSO--------
							domaincookie("sharedAdsFreeFlag");	//--------------- Unset domainCookie for Adsfree Magnify--------
							mcookie("autologin");
						}

						set_sess_vars("EMAIL",$email);
						set_sess_vars("EID",$this->is_exchangeuser());
						set_sess_vars("LoggedIn",'true');

						/***
						 * @Author: Aswini Nayak
						 * Date: 05/02/2009 Block the user's who has 'corp' account (in susbcription.type filed) to access B&B
						 * We will check only for users who has Buzz and after Via returns
						 * When a user has buzz account Via will return the same and we were storing this in session var.
						 * $_SESSION['Buzz']==1 when this is true we will reset the $_SESSION['Buzz']=0;
						 ***/


						$this->logVisit();
						return $_SESSION['LoggedIn']; // true/false
					}
				}
				else
				{
					set_sess_vars('isAuthed',0);
					set_sess_vars('isActive',false);
					mcookie("email");
					mcookie("password");
					mcookie("sid");
					domaincookie("sharedUserId");		// Unset domainCookie set for Magnify SSO -------
					mcookie("autologin");
					// Maintain the transaction log
					return $userDetails; // error message
				}
			}
		}catch (Exception $exception)
		{
			return "Via Error".$exception;
		}
	}//end of function login

	// get list of all the orders a user is having
	public function getUserProductDetail($via_id)
	{
		$this->VIAID = $via_id;
		try
		{
			$this->initSoapObject();
			$arrayFields = array('customerIds'=>$this->VIAID,'orderStatus'=>'ALL','orderItemTypeList'=>'','bEmanagerIds'=>TRUE);
			$userDetails = $this->viaObj->getCustomerSubInfoReq($this->VIAID,$arrayFields);
			return $userDetails->CustomerSubInfoReqResult->CustomerSubInfo->orders;

		}catch (Exception $exception)
		{
			return "Via Error".$exception;
		}
	}

	public function loginByViaId($VIAID)
	{
		session_start();
		global $_COOKIE,$BANTER_ON,$_SESSION;
		$this->VIAID=$VIAID;
		if(!$this->VIAID)
		{
			set_sess_vars('isAuthed',0);
			return 0;
		}
		try{
			$this->initSoapObject();
			$arrayFields = array('customerIds'=>$this->VIAID,'orderStatus'=>'ACTIVE','orderItemTypeList'=>'','bEmanagerIds'=>TRUE);
			$userDetails = $this->viaObj->getCustomerSubInfoReq($this->VIAID,$arrayFields);
			if(is_object($userDetails))
			{
				//*get auxilary account activated field from via*/
				$inactiveaccount=0;
				$getAuxField=$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->auxFields->field;
				if(is_array($getAuxField))
				{
					foreach($getAuxField as $auxvalue){
						if($auxvalue->name=='account_activated'){
							$inactiveaccount=$auxvalue->value;
						}
					}
				}
				else
				{
					if($userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->auxFields->field->name=='account_activated'){
						$inactiveaccount=$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->auxFields->field->value;
					}
				}
				// $inactiveaccount=0;
				if(!$inactiveaccount){
					set_sess_vars("notactivatedemail",$uid);
					set_sess_vars("action","inactiveaccount");
					set_sess_vars("via_inactive_id",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customerId);
					return "Inactive account";
				}else{

					global $_SESSION,$productAd;
					session_start();
					unset($_SESSION['action']);
					unset($_SESSION['notactivatedemail']);
					set_sess_vars("email",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->loginInfo->login);
					$email = $userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->loginInfo->login;
					set_sess_vars("viaid",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customerId);

					/* get the subscription table id for this viaid */
					$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
					$result=exec_query($strQuery,1);

					/** Author: Aswini Nayak Date: 14/01/2009
						* Here we are first checking the viaid from subscription table if its not present
						* that means the user is an existing MVIL user and viaid not updated while data migrated from old system to via
						* So we are updating the viaid on the base of emailid
						**/

					/*update ip of user in case of login*/
					//$updateip = array(ip=>$_SERVER['REMOTE_ADDR']);
					//$ip_up = update_query(subscription,$updateip,array(email=>$_SESSION['email']));


					if(empty($result) && isset($result)){
						$arrsubscription = array(via_id=>$_SESSION['viaid']);
						$subps_up = update_query(subscription,$arrsubscription,array(email=>$_SESSION['email']));
						$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
						$resultUpdate=exec_query($strQuery,1);

						/** Author: Aswini Nayak Date: 06/02/2009
						 * Here we are trying to insert the records in our db
						 * ViaAdmin creates user which is not reflecting at our DB
						 * So we are inserting the record in our DB
						 **/
						if(empty($resultUpdate) && isset($resultUpdate))
						{
							$userdata=array('via_id'=>$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customerId,'email'=>$email,'fname'=>$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->nameFirst,'lname'=>$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->nameLast);
							$insertedUserId=$this->saveUserData('subscription',$userdata);
							//*** $resultUpdate['id']=$insertedUserId;
							$strQuery="select id from subscription where via_id=".$_SESSION['viaid'];
							$resultUpdate=exec_query($strQuery,1);
						}
						$result=$resultUpdate;
					}

					/*update ip of user in case of login*/
					$this->user_id=$result['id'];
					set_sess_vars("SID",$result['id']);
					set_sess_vars("user_id",$result['id']);
					/* ------ --- Set domainCookie used for Magnify SSO -----------------  */
					if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
					{
						domaincookie("sharedUserId",trim($_SESSION['SID']));
					}
					/*-----------------------  Magnify SSO domain Cookie end ------------------ */
					set_sess_vars("haspreference",$this->hasPreference($result['id']));
					unset($result);
					set_sess_vars("nameLast",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->nameLast);
					set_sess_vars("nameFirst",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->nameFirst);
					set_sess_vars("ccType",$userDetails->CustomerSubInfoReqResult->CustomerSubInfo->customer->ccType);



					/* Prouct Info get and set in session */
					$paymentRenew=array('AUTO_RENEW'=>1,'MANUAL_RENEW'=>0,'AUTO_RENEW_BILL'=>0,'DO_NOT_RENEW'=>0);
					$products = $userDetails->CustomerSubInfoReqResult->CustomerSubInfo->orders;
					//set user orderItmType = PRODUCT info in session

					$productsArr=array();

					if(count($products)>1){
						foreach($products as $key => $value)
						{
							$productsArr[$value->orderItemType][$value->typeSpecificId]=array('orderNumber'=>$value->orderNumber,'orderItemSeq'=>$value->orderItemSeq,'orderClassId'=>$value->orderClassId,'orderCodeId'=>$value->orderCodeId,'sourceCodeId'=>$value->sourceCodeId,'typeSpecificId'=>$value->typeSpecificId,'startDate'=>$value->startDate,'expireDate'=>$value->expireDate,'billDate'=>$value->billDate,'description'=>$value->description,'price'=>$value->price,'auto_renew'=>$paymentRenew[$value->auto_renew],'subscriptionId'=>$value->subscriptionId,'orderItemType'=>$value->orderItemType,'n_issues_left'=>$value->n_issues_left);
							// echo $this->userProducts[]=$value->typeSpecificId;
							$this->userProducts[]=$value->typeSpecificId;
							if(in_array($value->typeSpecificId,$productAd)){
								$_SESSION['AdsFree']='1';
							}
						}
					}elseif(count($products)==1){
						//echo $products->typeSpecificId;
						$productsArr[$products->orderItemType][$products->typeSpecificId]=array('orderNumber'=>$products->orderNumber,'orderItemSeq'=>$products->orderItemSeq,'orderClassId'=>$products->orderClassId,'orderCodeId'=>$products->orderCodeId,'sourceCodeId'=>$products->sourceCodeId,'typeSpecificId'=>$products->typeSpecificId,'startDate'=>$products->startDate,'expireDate'=>$products->expireDate,'billDate'=>$products->billDate,'description'=>$products->description,'price'=>$products->price,'auto_renew'=>$paymentRenew[$products->auto_renew],'subscriptionId'=>$products->subscriptionId,'orderItemType'=>$products->orderItemType,'n_issues_left'=>$products->n_issues_left);
						if(in_array($value->typeSpecificId,$productAd)){
							$_SESSION['AdsFree']='1';
						}
					}
					set_sess_vars("products",$productsArr);

					$this->getCustomerSubInfo();
					$productstatusarray = $this->getSubcriptionProductDetails($_SESSION['user_id']);
					if(is_array($productstatusarray))
					{
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
						mcookie("email",$uid);
						mcookie("password",$pwd);
						mcookie("sid",$_SESSION['user_id']);
						/* ------ --- Set domainCookie used for Magnify SSO -----------------  */
						if(isset($_SESSION['SID']) && $_SESSION['SID']!='')
						{
							domaincookie("sharedUserId",trim($_SESSION['SID']));
						}

						if(isset($_SESSION['AdsFree'])  && $_SESSION['AdsFree']=='1')
						{
							domaincookie("sharedAdsFreeFlag",'1');
						}
						/*-----------------------  Magnify SSO domain Cookie end ------------------ */
						mcookie("autologin",1);
					}else{
						mcookie("email");
						mcookie("password");
						mcookie("sid");
						domaincookie("sharedUserId");		// Unset domainCookie set for Magnify SSO -------
						mcookie("autologin");
					}

					set_sess_vars("EMAIL",$email);
					set_sess_vars("EID",$this->is_exchangeuser());
					set_sess_vars("LoggedIn",'true');

					/***
						* @Author: Aswini Nayak
						* Date: 05/02/2009 Block the user's who has 'corp' account (in susbcription.type filed) to access B&B
						* We will check only for users who has Buzz and after Via returns
						* When a user has buzz account Via will return the same and we were storing this in session var.
						* $_SESSION['Buzz']==1 when this is true we will reset the $_SESSION['Buzz']=0;
						***/


					$this->logVisit();
					return $_SESSION['LoggedIn']; // true/false
				}
			}
			else
			{
				set_sess_vars('isAuthed',0);
				set_sess_vars('isActive',false);
				// Maintain the transaction log
				return $userDetails; // error message
			}
		}catch (Exception $exception)
		{
			return "Via Error".$exception;
		}
	}//end of function loginbyViaId


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

	/*
	 function getExchangeInfo($id)
	 {
		$this->initSoapObject();
		$qry="SELECT via_id FROM subscription WHERE id =".$id;
		$info=exec_query($qry,1);
		$viaId = $info['via_id'];
		$fieldsArray['customerIdVia']=$viaId; // key must be matched to Via keys
		$custList=$this->viaObj->getCustomersViaDetail($fieldsArray); // defined in _via_data_lib getCustomersViaDetail($FieldsArray) function of via class
		$userInfoArr = array();
		$userInfoArr['email']=$custList->CustomerGetResult->Customer->loginInfo->login;
		$userInfoArr['fname']=$custList->CustomerGetResult->Customer->nameFirst;
		$userInfoArr['lname']=$custList->CustomerGetResult->Customer->nameLast;
		$userInfoArr['id']=$id;
		$userInfoArr['password']=$custList->CustomerGetResult->Customer->loginInfo->password;
		return $userInfoArr;
		}
		*/

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
		domaincookie("sharedUserId");			// Unset domainCookie set for Magnify SSO -------
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

	/* Make array like this
		$email = $_POST['login'];
		$fieldsArray['customerLogin']=$email; // key must be matched to Via keys
		*/
	function checkUserViaAvailibilityByEmail($fieldsArray){
		$this->initSoapObject();
		$custList=array();
		$custList=$this->viaObj->getCustomersExistence($fieldsArray); // defined in _via_data_lib getCustomersByFields($FieldsArray) function of via class
		return $custList;
	}
	/**************deepika jain 5/12/2009**********
	 function defined to Set user orderItemTypeList == PRODUCTS info in SESSION
	 defined in _via_data_lib.php
	 **/
	function getCustomerSubInfo(){
		global  $_SESSION, $viProductsOrderStatusValid;
		$viaId = $_SESSION['viaid'];
		$this->initSoapObject();
		if(isset($viaId) && isset($this->viaObj)){
			$orders = $this->viaObj->customerSubInfo($viaId);
			if(count($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders)>1){
				foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){
					if(in_array($val->orderStatus, $viProductsOrderStatusValid)){
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderNumber']=$val->orderNumber;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderItemSeq']=$val->orderItemSeq;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderClassId']=$val->orderClassId;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderCodeId']=$val->orderCodeId;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderItemType']=$val->orderItemType;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['typeSpecificId']=$val->typeSpecificId;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderStatus']=$val->orderStatus;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['startDate']=$val->startDate;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['price']=$val->price;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['qty']=$val->qty;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['sourceCodeId']=$val->sourceCodeId;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['description']=$val->description;
						$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['auto_renew']=0;
					}
				}
			}else{
				if(in_array($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderStatus, $viProductsOrderStatusValid)){
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderNumber']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderNumber;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderItemSeq']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemSeq;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderClassId']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderClassId;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderCodeId']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderCodeId;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderItemType']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['typeSpecificId']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['orderStatus']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderStatus;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['startDate']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->startDate;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['price']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->price;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['qty']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->qty;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['sourceCodeId']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->sourceCodeId;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['description']=$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->description;
					$_SESSION['products'][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->orderItemType][$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders->typeSpecificId]['auto_renew']=0;
				}
			}
		}

	}// function getCustomerSubInfo ends here

	function getSubcriptionProductDetails($userid='')
	{
		global $viaProducts,$_SESSION;

		$product=array();
		$product=$_SESSION['products']['SUBSCRIPTION'];

		$result = array();

		$result['Buzz']=0;
		$result['Cooper']=0;
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
		$result['buyhedge']=0;
		if(!$_SESSION['AdsFree']){
			$result['AdsFree']=0;
		}

		if(is_array($product))
		{

			$searchBuzz=array(
			$viaProducts['BuzzMonthlyTrial']['typeSpecificId'],
			$viaProducts['BuzzQuartTrial']['typeSpecificId'],
			$viaProducts['BuzzAnnualTrial']['typeSpecificId'],
			$viaProducts['BuzzMonthly']['typeSpecificId'],
			$viaProducts['BuzzQuarterly']['typeSpecificId'],
			$viaProducts['BuzzAnnual']['typeSpecificId'],
			$viaProducts['BuzzComplimentary']['typeSpecificId'],
			$viaProducts['BuzzCK']['typeSpecificId'],
			$viaProducts['BuzzScott']['typeSpecificId'],
			$viaProducts['BuzzST']['typeSpecificId'],
			$viaProducts['BuzzFT1M-ST']['typeSpecificId'],
			$viaProducts['BuzzFT3M-ST']['typeSpecificId'],
			$viaProducts['BuzzFT1W-ST']['typeSpecificId']

			);

			foreach($searchBuzz as $key=>$value){
				if(is_array($product))
				$buzzprod=array_key_exists($value,$product);
				if($buzzprod=="1"){
					$result['Buzz']=1;
				}
			}
			$searchFlex=array(
			$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'],
			$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'],
			$viaProducts['FlexfolioMonthly']['typeSpecificId'],
			$viaProducts['FlexfolioAnnual']['typeSpecificId'],
			$viaProducts['FlexfolioComplimentary']['typeSpecificId'],
			$viaProducts['FlexfolioCK']['typeSpecificId'],
			$viaProducts['FlexfolioST']['typeSpecificId'],
			$viaProducts['FlexfolioFT1M-ST']['typeSpecificId'],
			$viaProducts['FlexfolioFT3M-ST']['typeSpecificId']
			);
			foreach($searchFlex as $key=>$value){
				if(is_array($product))
				$flexprod=array_key_exists($value,$product);
				if($flexprod=="1"){
					$result['Flexfolio']=1;
				}
			}
			$searchjeff=array(
			$viaProducts['CooperMonthlyTrial']['typeSpecificId'],
			$viaProducts['CooperQuartTrial']['typeSpecificId'],
			$viaProducts['CooperAnnualTrial']['typeSpecificId'],
			$viaProducts['CooperMonthly']['typeSpecificId'],
			$viaProducts['CooperQuarterly']['typeSpecificId'],
			$viaProducts['CooperAnnual']['typeSpecificId'],
			$viaProducts['CooperComplimentary']['typeSpecificId'],
			$viaProducts['CooperCK']['typeSpecificId'],
			$viaProducts['CooperST']['typeSpecificId'],
			$viaProducts['CooperFT1M-ST']['typeSpecificId'],
			$viaProducts['CooperFT3M-ST']['typeSpecificId']
			);
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
			$searchoptionsmith=array(
			$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'],
			$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'],
			$viaProducts['OptionsmithMonthly']['typeSpecificId'],
			$viaProducts['OptionsmithAnnual']['typeSpecificId'],
			$viaProducts['OptionsmithComplimentary']['typeSpecificId'],
			$viaProducts['OptionsmithCK']['typeSpecificId'],
			$viaProducts['OptionsmithST']['typeSpecificId'],
			$viaProducts['OptionsmithFT1M-ST']['typeSpecificId'],
			$viaProducts['OptionsmithFT3M-ST']['typeSpecificId']
			);
			foreach($searchoptionsmith as $key=>$value){
				if(is_array($product))
				$optionsmithprod=array_key_exists($value,$product);
				if($optionsmithprod=="1"){
					$result['Optionsmith']=1;
				}
			}

			$searchbmtpalert=array(
			$viaProducts['BMTPAlertTrial']['typeSpecificId'],
			$viaProducts['BMTPAlert']['typeSpecificId'],
			$viaProducts['BMTPAlertComplimentary']['typeSpecificId'],
			$viaProducts['BMTPAlertCK']['typeSpecificId']
			);

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

			$searchETF=array(
			$viaProducts['ETFMonthlyTrial']['typeSpecificId'],
			$viaProducts['ETFQuartTrial']['typeSpecificId'],
			$viaProducts['ETFAnnualTrial']['typeSpecificId'],
			$viaProducts['ETFMonthly']['typeSpecificId'],
			$viaProducts['ETFQuart']['typeSpecificId'],
			$viaProducts['ETFAnnual']['typeSpecificId'],
			$viaProducts['ETFComplimentary']['typeSpecificId'],
			$viaProducts['ETFCK']['typeSpecificId'],
			$viaProducts['ETFST']['typeSpecificId'],
			$viaProducts['ETFST1M']['typeSpecificId'],
			$viaProducts['ETFST3M']['typeSpecificId']
			);
			foreach($searchETF as $key=>$value){
				if(is_array($product))
				$etfprod=array_key_exists($value,$product);
				if($etfprod=="1"){
					$result['ETFTrader']=1;
				}
			}

			$searchTheStockPlayBook=array(
			$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'],
			$viaProducts['TheStockPlaybookQuart']['typeSpecificId'],
			$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'],
			$viaProducts['TheStockPlaybookComplimentary']['typeSpecificId'],
			$viaProducts['TheStockPlaybookCK']['typeSpecificId'],
			$viaProducts['TheStockPlaybookST']['typeSpecificId'],
			$viaProducts['TheStockPlaybookST1M']['typeSpecificId'],
			$viaProducts['TheStockPlaybook3MST']['typeSpecificId']
			);
			foreach($searchTheStockPlayBook as $key=>$value){
				if(is_array($product))
				$theStockPlayBookprod=array_key_exists($value,$product);
				if($theStockPlayBookprod=="1"){
					$result['TheStockPlayBook']=1;
				}
			}

			$searchTheStockPlayBookPremium=array(
			$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId'],
			$viaProducts['TheStockPlaybookPremiumCK']['typeSpecificId']
			);
			foreach($searchTheStockPlayBookPremium as $key=>$value){
				if(is_array($product))
				$theStockPlayBookPremiumprod=array_key_exists($value,$product);
				if($theStockPlayBookPremiumprod=="1"){
					$result['TheStockPlayBookPremium']=1;
				}
			}
			$searchBuyHedge=array(
			$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId'],
			$viaProducts['BuyHedgeQuartTrial']['typeSpecificId'],
			$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'],
			$viaProducts['BuyHedgeMonthly']['typeSpecificId'],
			$viaProducts['BuyHedgeQuart']['typeSpecificId'],
			$viaProducts['BuyHedgeAnnual']['typeSpecificId'],
			$viaProducts['BuyHedgeComplimentary']['typeSpecificId'],
			$viaProducts['BuyHedgeCK']['typeSpecificId'],
			$viaProducts['BuyHedgeST']['typeSpecificId'],
			$viaProducts['BuyHedgeST1M']['typeSpecificId'],
			$viaProducts['BuyHedgeST3M']['typeSpecificId']
			);
			foreach($searchBuyHedge as $key=>$value){
				if(is_array($product))
					$BuyHedgeprod=array_key_exists($value,$product);
				if($BuyHedgeprod=="1"){
					$result['buyhedge']=1;
				}
			}

			$searchAdFree=array(
			$viaProducts['AdFreeMonthly']['typeSpecificId'],
			$viaProducts['AdFreeComplimentary']['typeSpecificId']
			);
			foreach($searchAdFree as $key=>$value){
				if(is_array($product))
				$adFreeprod=array_key_exists($value,$product);
				if($adFreeprod=="1"){
					$result['AdsFree']=1;
				}
			}

			$searchTechStrat=array(
			$viaProducts['TechStratMonthlyTrial']['typeSpecificId'],
			$viaProducts['TechStratQuarterTrial']['typeSpecificId'],
			$viaProducts['TechStratAnnualTrial']['typeSpecificId'],
			$viaProducts['TechStratMonthly']['typeSpecificId'],
			$viaProducts['TechStratQuarterly']['typeSpecificId'],
			$viaProducts['TechStratAnnual']['typeSpecificId'],
			$viaProducts['TechStratComplimentary']['typeSpecificId'],
			$viaProducts['TechStratST']['typeSpecificId'],
			$viaProducts['TechStratCK']['typeSpecificId']
			);
			foreach($searchTechStrat as $key=>$value){
				if(is_array($product))
				$TechStratProd=array_key_exists($value,$product);
				if($TechStratProd=="1"){
					$result['TechStrat']=1;
				}
			}


			$searchGaryK=array(
			$viaProducts['GaryKMonthlyTrial']['typeSpecificId'],
			$viaProducts['GaryKQuarterTrial']['typeSpecificId'],
			$viaProducts['GaryKAnnualTrial']['typeSpecificId'],
			$viaProducts['GaryKMonthly']['typeSpecificId'],
			$viaProducts['GaryKQuarterly']['typeSpecificId'],
			$viaProducts['GaryKAnnual']['typeSpecificId'],
			$viaProducts['GaryKComplimentary']['typeSpecificId'],
			$viaProducts['GaryKST']['typeSpecificId'],
			$viaProducts['GaryKCK']['typeSpecificId']
			);
			foreach($searchGaryK as $key=>$value){
				if(is_array($product))
				$GarykProd=array_key_exists($value,$product);
				if($GarykProd=="1"){
					$result['GaryK']=1;
				}
			}


			$searchHousingReport=array(
			$viaProducts['Housing3Months']['typeSpecificId'],
			$viaProducts['Housing6Months']['typeSpecificId'],
			$viaProducts['HousingAnnual']['typeSpecificId'],
			$viaProducts['HousingComplimentary']['typeSpecificId']
			);
			foreach($searchHousingReport as $key=>$value){
				if(is_array($product))
				$HousingReportProd=array_key_exists($value,$product);
				if($HousingReportProd=="1"){
					$result['HousingReport']=1;
				}
			}

			//For Housing market Single Issue
			$searchHSLasVegas=array(
			$viaProducts['LasVegas']['typeSpecificId']
			);
			foreach($searchHSLasVegas as $key=>$value){
				if(is_array($product))
				$HSLasVegasProd=array_key_exists($value,$product);
				if($HSLasVegasProd=="1"){
					$result['HS-LasVegas']=1;
				}
			}

			$searchHSChicago=array(
			$viaProducts['Chicago']['typeSpecificId']
			);
			foreach($searchHSChicago as $key=>$value){
				if(is_array($product))
				$HSChicagoProd=array_key_exists($value,$product);
				if($HSChicagoProd=="1"){
					$result['HS-Chicago']=1;
				}
			}

			$searchHSPhoenix=array(
			$viaProducts['Phoenix']['typeSpecificId']
			);
			foreach($searchHSPhoenix as $key=>$value){
				if(is_array($product))
				$HSPhoenixProd=array_key_exists($value,$product);
				if($HSPhoenixProd=="1"){
					$result['HS-Phoenix']=1;
				}
			}

			$searchHSWashingtonDC=array(
			$viaProducts['WashingtonDC']['typeSpecificId']
			);
			foreach($searchHSWashingtonDC as $key=>$value){
				if(is_array($product))
				$HSWashingtonDCProd=array_key_exists($value,$product);
				if($HSWashingtonDCProd=="1"){
					$result['HS-WashingtonDC']=1;
				}
			}

			$searchHSSanDiego=array(
			$viaProducts['SanDiego']['typeSpecificId']
			);
			foreach($searchHSSanDiego as $key=>$value){
				if(is_array($product))
				$HSSanDiegoProd=array_key_exists($value,$product);
				if($HSSanDiegoProd=="1"){
					$result['HS-SanDiego']=1;
				}
			}

			$searchHSNewYorkMetro=array(
			$viaProducts['NewYorkMetro']['typeSpecificId']
			);
			foreach($searchHSNewYorkMetro as $key=>$value){
				if(is_array($product))
				$HSNewYorkMetroProd=array_key_exists($value,$product);
				if($HSNewYorkMetroProd=="1"){
					$result['HS-NewYorkMetro']=1;
				}
			}

			$searchHSMiami=array(
			$viaProducts['Miami']['typeSpecificId']
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
			$viaProducts['AtlantaComp']['typeSpecificId']
			);
			foreach($searchHSAtlanta as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Atlanta']=1;
				}
			}

			$searchHSDallas=array(
			$viaProducts['Dallas']['typeSpecificId'],
			$viaProducts['DallasComp']['typeSpecificId']
			);
			foreach($searchHSDallas as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Dallas']=1;
				}
			}

			$searchHSLosAngles=array(
			$viaProducts['LosAngles']['typeSpecificId'],
			$viaProducts['LosAnglesComp']['typeSpecificId']
			);
			foreach($searchHSLosAngles as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-LosAngles']=1;
				}
			}

			$searchHSMinneapolis=array(
			$viaProducts['Minneapolis']['typeSpecificId'],
			$viaProducts['MinneapolisComp']['typeSpecificId']
			);
			foreach($searchHSMinneapolis as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Minneapolis']=1;
				}
			}

			$searchHSPortland=array(
			$viaProducts['Portland']['typeSpecificId'],
			$viaProducts['PortlandComp']['typeSpecificId']
			);
			foreach($searchHSPortland as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Portland']=1;
				}
			}

			$searchHSOrlendo=array(
			$viaProducts['Orlendo']['typeSpecificId'],
			$viaProducts['OrlendoComp']['typeSpecificId']
			);
			foreach($searchHSOrlendo as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Orlendo']=1;
				}
			}

			$searchHSSeattle=array(
			$viaProducts['Seattle']['typeSpecificId'],
			$viaProducts['SeattleComp']['typeSpecificId']
			);
			foreach($searchHSSeattle as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-Seattle']=1;
				}
			}

			$searchHSSanFrancisco=array(
			$viaProducts['SanFrancisco']['typeSpecificId'],
			$viaProducts['SanFranciscoComp']['typeSpecificId']
			);
			foreach($searchHSSanFrancisco as $key=>$value){
				if(array_key_exists($value,$product) == 1)
				{
					$result['HS-SanFrancisco']=1;
				}
			}

		$searchHSLongIsland=array(
			$viaProducts['LongIsland']['typeSpecificId'],
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
	function saveUserData($tablename,$userdata){
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
	
	function setUsersPassword($userId,$userPassword){
		$getSql="select id from subscription where email='".$userId."'";
		$getResult=exec_query($getSql,1);
		if(!empty($getResult['id'])){
			$user['password']=base64_encode($userPassword);
			$updateResult=update_query("subscription",$user,array("id"=>$getResult['id']));
		}
	}
}//end of class User
?>
