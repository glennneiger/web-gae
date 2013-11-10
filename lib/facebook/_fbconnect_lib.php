<?php
$objFconnect = new FacebookConnect();
$case = $_REQUEST['case'];

 switch($case){
	case 'displayFBConnect':
	echo $objFconnect->$case();	
	
	break;
}


class FacebookConnect{

	function getFBId($SID){
		$strQuery="select distinct(fb_id) as FBID from mv_facebook_mapping where subscription_id='".$SID."'";
		//echo $strQuery;
		$result = exec_query($strQuery,1);
		return $result;
	}

	function displayFBConnect(){
		global $IMG_SERVER, $_SESSION, $_COOKIE;
		$FBState=true;
		if($_SESSION['SID']){
			$result=$this->getFBId($_SESSION['SID']);
			if(!$result) $FBState=false;
		}
		if(!$_SESSION['SID']){
			$FBState=false;
		}
		$data ='';
		if($FBState==false && !$_SESSION['userFacebookId']){
		$data = '<div id="container"><form id="comment-form"><div id="userbox" style="display:none;"></div>
            <div id="userinfo" class="fb_button">
              <div class="login" >
			  <span class="fbBtn">
			  <a href="#" onclick="FB.Connect.requireSession(function(){ authUser()});return false;"> 
			  <img id="fb_login_image" align="absmiddle" vspac="5" src="/images/subscription/fbConnect.jpg" alt="Connect" /> </a>	
			  </span>
			  <span class="fbLogin"> <a href="#" onclick="FB.Connect.requireSession(function(){ authUser()});return false;"> 
			  Log-in&nbsp;using&nbsp;Facebook</a></span> 
			  </div>
            </div>
          </form>
		</div>';
		}
		return $data;
	}

	function displayFBComment(){
		$FBState=true;
		if($_SESSION['SID']){
			$result=$this->getFBId($_SESSION['SID']);
			if(!$result) $FBState=false;
		}
		if(!$_SESSION['SID']){
			$FBState=false;
		}
		if($FBState==false){
		?>
		<div id="container">
          <form id="comment-form">
            <div id="userbox" style="display:none;"></div>
            <div id="userinfo">
              <div class="login" style="width:200px;"><fb:comments xid="dppj1" simple="1"></fb:comments></div>
            </div>
          </form>
		<script type="text/javascript">
	      FB.init("6751423f6be0884e2bf1c406fafa1c1c", "xd_receiver.htm", {"ifUserConnected" : auth_fb});
		</script>
		</div>
		<?
		}
	}

}
?>