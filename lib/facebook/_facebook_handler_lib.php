<?php
global $D_R;
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
include_once($D_R."/lib/_exchange_lib.php");

class fbhandler{
	public $fbId;
	public $action;
	public $exists = 'false';
	public $flag = true;
	public $SID ;
	// public $frnd_list = array() ;

	function __construct($vars){
		$this->SID = $vars['SID'];
		$this->fbId = $vars['fbId'];
		$this->action = $vars['action'];
		$this->publish_post = $vars['publish_post'];
		$this->follow_frnds = $vars['follow_frnds'];
		$this->email_alerts = $vars['email_alerts'];
		$this->frnd_list = explode(",",$vars['frnd_list']);
		$this->email=$vars['email'];
		$this->firstname=$vars['firstname'];
		$this->lastname=$vars['lastname'];
		$this->country=$vars['country'];
		$this->dailtDigest=$vars['dailydigest'];
		$this->pwd=$vars['pwd'];


	}
	function exists(){
		global $_SESSION;
		$qry = "select id from subscription where email='".$this->email."'";
		$result= exec_query($qry,1);
		if($result['id']>0){
			$this->exists = TRUE;
			$this->SID=$result['id'];
			$qryIsFacebook = "select id from fb_user where subscription_id='".$this->SID."'";
			$resultIsFacebook= exec_query($qryIsFacebook,1);
			if(count($resultIsFacebook)<1){
				$this->fbinsert();
			}
		}else{
			if($_SESSION['LoggedIn']){
				$this->fbinsert();
				$this->exists = TRUE;
			}else{
				$this->exists = FALSE;
			}

		}
		return $this->exists;

	}
	function fbinsert(){  // user exist in MV and facebook id not exist
		$this->flag = true;
		$this->exists = 'true';
		$this->publish_post ='1';
		$this->follow_frnds ='1';
		$this->email_alerts ='1';
		$subscription_id = $this->SID;
		set_sess_vars('userFacebookId',$this->fbId);

		if(!$subscription_id){
			$qry="select id from subscription where email='".$email."'";
			$result=exec_query($qry,1);
			$subscription_id =$result['id'];
		}


		if(!$this->flag){
			$this->publish_post ='0';
			$this->follow_frnds ='0';
			$this->email_alerts ='0';
		}

		$fbUserMapping['subscription_id'] = $this->SID;
		$fbUserMapping['fbuser_id'] = $this->fbId;
		$fbUserMapping['publish_post'] = $this->publish_post;
		$fbUserMapping['follow_frnds'] = $this->follow_frnds;
		$fbUserMapping['fb_permission'] = $this->fb_permission;

		$queryInsertFbUser= insert_query('fb_user',$fbUserMapping);

		$updateValueSub['recv_daily_gazette'] = $this->email_alerts;
		$conditionSub['id'] = $this->SID;
		$queryUpdateSub= update_query('subscription',$updateValueSub,$conditionSub);

		//now update users frindlist

/*		if(count($this->frnd_list)){
			$script ='';
			foreach($this->frnd_list as $val){
			   if($val){

					$script = "insert into fb_user_friends(subscription_id,fb_friend_id) values ('".$subscription_id."','".$val."');";
					exec_query($script);
				}
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

		}*/
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
	function autologin($fbId, $isRegister){
		global $globalPwd,$D_R;
		include_once("$D_R/lib/json.php");
		$objUser = new user();
		$json = new Services_JSON();
		set_sess_vars('userFacebookId',$this->fbId);

		$strQuery="SELECT S.id AS SID,S.`email`,S.`password` FROM subscription S,fb_user FU
WHERE FU.fbuser_id='".$this->fbId."'
AND S.id=FU.subscription_id ORDER BY S.id DESC";
		$result=exec_query($strQuery,1);
		if(!$result){
			return false;
		}
		else{
			// autologin user basis of his details
				$dataObj = new userData();
				if($result['password']=='')
				{
					$pass=$globalPwd;
					$passArr['password'] = $dataObj->encryptUserPassword($pass);
					update_query("subscription", $passArr,array(id=>$result['SID']));
				}
				else
				{
					$pass = $dataObj->decryptUserPassword($result['password']);
				}
				$status=$objUser->login($result['email'],$pass);
				if($status){
					$result= array(
						'resultStatus' => true,
						'isRegister' => $isRegister
					);
					$output = $json->encode($result);
					return $output;
					//return true;
				}else{
					return false;
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

	function fbregisteration()
	{

		 global $HTPFX,$HTHOST,$D_R,$globalPwd,$pwdSubject,$REG_EML_REPLYTO;

		 $regisFunnelObj = new registrationFunnelData();
		 $dataObj = new userData();

		 $email = $this->email;
		 $password = $globalPwd;
		 $fname = $this->firstname;
		 $lname = $this->lastname;
		 $country = $this->country;

		 $userPaswd=$dataObj->encryptUserPassword($password);
		 $subId= $regisFunnelObj->getAccountCode();
		 $userdata=array('email'=>$email,'fname'=>$fname,'lname'=>$lname,'country'=>$country,'password'=>$userPaswd,'id'=>$subId);
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
				$emailalertsArray=array('subscriber_id'=>$subId,'category_ids'=>$catgors,'email_alert'=>$email_alert);
				$authorsrray=array('subscriber_id'=>$subId,'author_id'=>$_POST['authorCategories'],'email_alert'=>$authemail_alert);
				insert_or_update('email_alert_categorysubscribe',$emailalertsArray,array('subscriber_id'=>$insertid));
				insert_or_update('email_alert_authorsubscribe',$authorsrray,array('subscriber_id'=>$insertid));

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */

				RegisterUser($subId); // defined in _exchange_lib.php

				/* Insert into ex_user_profile table */
				$subarray = array('subscription_id'=>$subId);
				$profileid = insert_query('ex_user_profile', $subarray);
				$this->SID=$subId;
				$this->fbinsert();

				$userObj = new user();
				$isLoggedIn=$userObj->login($email,$password);

				$value=array(
					'status'=>true,
					'firstname'=>ucwords($this->firstname),
					'lastname'=>ucwords($this->lastname)
				);
				if($value['status']){

					$EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_new_pass.htm?email=".$email."&password=".$password;
	           		mymail($email,$REG_EML_REPLYTO,$pwdSubject,inc_web($EML_TMPL));
					return $this->autologin($this->fbId,1);
				}
			}
	}

}
?>