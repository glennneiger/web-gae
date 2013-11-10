<?php
session_start();
global $D_R;
include_once($D_R.'/lib/tickertalk/_tickertalk_lib.php');
/*if(!$_REQUEST['fbId']){
	$fbId = $_COOKIE['userFacebookId'];
}*/
/*if(!$_COOKIE['userFacebookId'] or !$_SESSION['userFacebookId']){
	//$_COOKIE['userFacebookId'] = $fbId;
	setcookie('userFacebookId',$fbId );
	$_SESSION['userFacebookId'] = $_COOKIE['userFacebookId'];
}*/
//$_SESSION['userFacebookId'] = $fbId;

$fbId = $_REQUEST['fbId'];

$action = $_REQUEST['action'];
$publish_post = $_REQUEST['publish_post'];
$follow_frnds = $_REQUEST['follow_frnds'];
$email_alerts = $_REQUEST['email_alerts'];
$frndlist = $_REQUEST['frndlist'];
$fb_permission = $_REQUEST['fb_permission'];

$vars = array();
$vars['SID'] = $_SESSION['SID'] ;
$vars['fbId'] = $fbId ;
$vars['action'] = $action ;
$vars['publish_post'] = $publish_post ;
$vars['follow_frnds'] = $follow_frnds ;
$vars['email_alerts'] = $email_alerts ;
$vars['frnd_list']= $frndlist;
$vars['fb_permission']= $fb_permission;


$objhandler = new fbhandler($vars);


switch ($action){
	case "exists":
		$objhandler->$action();
		echo $objhandler->exists ;
	break;
	case "fbinsert":
		echo $objhandler->$action();

	break;
	case "autologin":
		echo $objhandler->autologin($fbId);

	break;
	case "fbupdate":
		echo $objhandler->fbupdate();

	break;
	case "fbchkpublish":
		echo $objhandler->fbchkpublish();
	break;
	case "default":
		break;

}


class fbhandler{
	public $fbId;
	public $action;
	public $exists = 'false';
	public $flag = true;
	public $SID ;
	public $frnd_list = array() ;

	function __construct($vars){
		$this->SID = $vars['SID'];
		$this->fbId = $vars['fbId'];
		$this->action = $vars['action'];
		$this->publish_post = $vars['publish_post'];
		$this->follow_frnds = $vars['follow_frnds'];
		$this->email_alerts = $vars['email_alerts'];
		$this->frnd_list = explode(",",$vars['frnd_list']);
	}
	function exists(){
		session_start();
		$query  = "select id from fb_user where fbuser_id = '".$this->fbId."'";
		if($this->SID!=''){
			$query  .= "  "."AND subscription_id = '".$this->SID."'";
		}
		$result = exec_query($query,1);
		if($result['id']>0){
			$this->exists = 'true';
		}else{
			if($_SESSION['LoggedIn']){
				$this->flag = true;
				$this->exists = 'true';
				$this->publish_post ='1';
				$this->follow_frnds ='1';
				$this->email_alerts ='1';
				$this->fbinsert();
			}else{
				$this->exists = 'false';
			}

		}
		return;

	}
	function fbinsert(){
		$subscription_id = $this->SID;
		set_sess_vars('userFacebookId',$this->fbId);
		if(!$this->flag){
			$this->publish_post ='0';
			$this->follow_frnds ='0';
			$this->email_alerts ='0';
		}
		$query  = "insert into fb_user (subscription_id, fbuser_id, publish_post,follow_frnds,fb_permission) values('".$subscription_id."','".$this->fbId."','".$this->publish_post."','".$this->follow_frnds."','".$this->publish_post."') ";
		$result = exec_query($query);
		if($this->follow_frnds=='1'){
			$this->getFacebookUserFriendsId($subscription_id,$this->frnd_list);
		}
		$query ="update subscription set recv_daily_gazette='".$this->email_alerts."' where id  = '".$subscription_id."'";
		$result = exec_query($query);

		//now update users frindlist
		/*Insert logs for tickertalk registration*/
		$objticker= new ticker();
		$objticker->setTickertalkLogs('fb_login',$subscription_id);


		if(count($this->frnd_list)){
			$script ='';
			foreach($this->frnd_list as $val){
				$script = "insert into fb_user_friends(subscription_id,fb_friend_id) values ('".$subscription_id."','".$val."');";
				exec_query($script);
			}
			//$result = exec_query($script);

				// now add new facebook user's frind in exchnage community
				$query = "SELECT fb.subscription_id subscription_id  FROM fb_user_friends fuf,fb_user fb WHERE fuf.subscription_id ='".$subscription_id."' AND fuf.fb_friend_id = fb.fbuser_id";
				$result = exec_query($query, 1);
					if(count($result)){
						foreach ($result as $val){
							$query = "insert into ex_user_friends (subscription_id,friend_subscription_id,date) values('".$subscription_id."','".$val."',sysdate())";
							exec_query($query);
						}
					}
				return 'true';

		}
		return 'true';
	}
	function fbupdate(){
		//session_start();
		$subscription_id = $this->SID;
		$query  = "update fb_user set fb_permission ='".$this->fb_permission."' where subscription_id='".$subscription_id."'";
		$result = exec_query($query);
		return 'true';
	}
	function fbchkpublish(){
		$query  = "select publish_post, fb_permission from fb_user where subscription_id = '".$this->SID."'";
		$result = exec_query($query,1);
		if($result['publish_post'] == '1'){
			return 'true';
		}
		return 'false';
	}
	function autologin($fbId){

		//$_SESSION['userFacebookId'] = $this->fbId;
		set_sess_vars('userFacebookId',$this->fbId);
		if($_SESSION['SID']){
			return 'true';
		}
		// get via id from facebook id
		$strQuery="SELECT S.id as SID, S.via_id AS VIAID FROM subscription S,fb_user FU WHERE FU.fbuser_id='".$this->fbId."' AND S.id=FU.subscription_id ORDER BY S.id DESC";
		$result=exec_query($strQuery,1);
		if(!$result){
			return false;
		}
		else{
			// autologin user basis of his details
			$objVia = new Via();
			$arrayFields = array('customerIdVia'=>$result['VIAID']);
			$customerInfo = $objVia->getCustomersViaDetail($arrayFields);
			// orders details
			$bEmanagerIds = true;
			$customerIds  = $result['VIAID'];
			$SID = $result['SID'];
			$orderItemTypeList = '';
			$orderStatus = 'ALL';
			$custIdent=$objVia->getCustIdent();
			$orders = $objVia->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>$bEmanagerIds,'customerIds'=>$customerIds,'orderItemTypeList'=>$orderItemTypeList,'orderStatus'=>$orderStatus));
			$orderDetails = $orders->CustomerSubInfoReqResult->CustomerSubInfo->orders;

			if(!is_object($customerInfo)){
				return false;
			}
			else{
				$status=$this->loginByViaId($customerInfo,$orderDetails, $SID);
				/*Insert logs for tickertalk registration*/
				$objticker= new ticker();
				$objticker->setTickertalkLogs('fb_login',$SID);
				return 'true';
			}
		}
	}
	function loginByViaId($customerDetails,$orderDetails, $SID){
		global $_SESSION;

		$user = new user();
		session_start();
		$email=$customerDetails->CustomerGetResult->Customer->loginInfo->login;
		$firstName=$customerDetails->CustomerGetResult->Customer->nameFirst;
		$lastName=$customerDetails->CustomerGetResult->Customer->nameLast;
		//$SID=$customerDetails->CustomerGetResult->Customer->customerIdExternal;
		$via_id=$customerDetails->CustomerGetResult->Customer->customerIdVia;
		// set session for user details
		set_sess_vars('email',$email);
		set_sess_vars('firstname',$firstName);
		set_sess_vars('nameFirst',$firstName);
		set_sess_vars('lastname',$lastName);
		set_sess_vars('nameLast',$lastName);
		set_sess_vars('SID',$SID);
		set_sess_vars('user_id',$SID);
		set_sess_vars('viaid',$via_id);
		set_sess_vars('isAuthed',1);
		set_sess_vars('signedIn',1);
		set_sess_vars('isActive',1); // true or false
		set_sess_vars('LoggedIn',true); // true or false
		if(!$orderDetails){
			return false;
		}
		//htmlprint_R($_SESSION);

		// set session for user's product details
		$productsArr=array();

		if(count($orderDetails)>1){
			foreach($orderDetails as $key => $value){
				$productsArr[$value->orderItemType][$value->typeSpecificId]=array('orderNumber'=>$value->orderNumber,'orderItemSeq'=>$value->orderItemSeq,'orderClassId'=>$value->orderClassId,'orderCodeId'=>$value->orderCodeId,'sourceCodeId'=>$value->sourceCodeId,'typeSpecificId'=>$value->typeSpecificId,'startDate'=>$value->startDate,'expireDate'=>$value->expireDate,'billDate'=>$value->billDate,'description'=>$value->description,'price'=>$value->price,'auto_renew'=>$paymentRenew[$value->auto_renew],'subscriptionId'=>$value->subscriptionId,'orderItemType'=>$value->orderItemType);
			}
		}elseif(count($products)==1){
			$productsArr[$products->orderItemType][$products->typeSpecificId]=array('orderNumber'=>$products->orderNumber,'orderItemSeq'=>$products->orderItemSeq,'orderClassId'=>$products->orderClassId,'orderCodeId'=>$products->orderCodeId,'sourceCodeId'=>$products->sourceCodeId,'typeSpecificId'=>$products->typeSpecificId,'startDate'=>$products->startDate,'expireDate'=>$products->expireDate,'billDate'=>$products->billDate,'description'=>$products->description,'price'=>$products->price,'auto_renew'=>$paymentRenew[$products->auto_renew]);
		}
		set_sess_vars("products",$productsArr);
		$productstatusarray=$user->getSubcriptionProductDetails($_SESSION['SID']);
		if(is_array($productstatusarray)){
			foreach($productstatusarray as $key => $value){
				set_sess_vars($key,$value);
			}
		}
	}

	function getFacebookUserFriendsId($loginid,$friendids){
		$qry="select subscription_id from fb_user where fbuser_id in ($friendids);";
			$getsubid=exec_query($qry);
			foreach($getsubid as $row){
				if($loginid!=$row['subscription_id']){
					$qrycheckfolow="select subscribed_to from ex_blog_subscribed where subscribed_to='".$row['subscription_id']."' and  user_id='".$loginid."'";
					$qryresult=exec_query($qrycheckfolow,1);
					if(!$qryresult['subscribed_to']){
						$subscriptiondata=array(
								subscribed_to=>$row['subscription_id'],
								user_id=>$loginid,
								subscribed_on=>date('Y-m-d H:i:s'),
								subscription_status=>1
								);
							$id=insert_query("ex_blog_subscribed",$subscriptiondata);
					}
				}

			}


	}
}
?>