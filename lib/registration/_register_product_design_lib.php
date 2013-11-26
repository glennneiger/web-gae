<?php
global $IMG_SERVER,$HTPFX,$HTHOST,$D_R,$page_config;

class regitrationProductDesign{

	function regitrationHeader($navSelected1,$navSelected2,$navSelected3){
		global $HTPFX,$HTHOST,$HTPFXSSL;
		?>
<div id="cart_topnvg">
<div class="<?=$navSelected1;?>">Step 1</div>
<div class="<?=$navSelected2;?>">
<? if(isset($_SESSION['new_register_billing'])) {?>
<a style="cursor:pointer;color:#000000;" href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/billing"">Step 2</a>
<? }
else
{
?>
Step 2
<? } ?>
</div>
<div class="<?=$navSelected3;?>">Step 3</div>
</div>
<!--Top nvg end -->
		<?
	}

	function loginOptionHousing(){

		if(!isset($_SESSION['viaid'])) {
		 ?>
<div class="user_login">
<div class="login_reg">
<div class="login_radio"><input type="radio" value="reg"
	name="radUser[]" id="radUser" checked="checked"
	onclick="javascript:login_reg(this.value);"/ ></div>
<div style="float: left;">&nbsp;I want to create an account</div>
</div>

<div class="login_reg">
<div class="login_radio"><input type="radio" value="login"
	name="radUser[]" id="radUser"
	onclick="javascript:login_reg(this.value);" /></div>
<div style="float: left;">&nbsp;I want to login into my account</div>
</div>
</div>
		 <? }
	}
	function userloginHousing(){
		?>
<div class="cont_info">
<div class="ttle_txt">Email Address</div>
		<?php input_email("email","",0,255,"class=inpt TABINDEX=1"); ?>
<div id="loginerrorname" class="erordiv_name"></div>
</div>
<!--1st input end -->
<div class="cont_info">
<div class="ttle_txt">Password</div>
		<?php input_pass("password","",20,255,"class=inpt onKeyPress= get_login_keys(event); TABINDEX=2");  ?>
<div id="loginerrorpassword" class="erordiv_name"></div>
</div>
<!--2nd input end -->
<div class="cont_info">
<div class="ttle_txt"><?php input_check("viauserremember",$remeber_me,"TABINDEX=3"); ?></div>
<div class="remember_info">Remember my Member ID and Password at this
computer</div>
</div>
<div class="nexstep_div">
<div id="loginerror" class="erordiv_processing"></div>
<div class="bttn_nxtstep1" style="cursor: pointer;"><a
	onclick="userLogin(); return false;"> <input type="image"
	id="login_next_en"
	src="<?=$IMG_SERVER?>/images/registration/bttn_nextstep.jpg"
	width="120" height="39" alt="Next Step" tabindex="4" /></a> <input
	type="image" id="login_next_ds" style="display: none;"
	src="<?=$IMG_SERVER?>/images/registration/bttngrey_nextstep.jpg"
	width="120" height="39" alt="Next Step" /></div>
</div>
		<?
	}
	function userDetailHousing(){
		global $tabStart;
		if(empty($tabStart)){
			$tabStart=1;
		}
		if($_SESSION['viaid'])
		{
			$objVia=new Via();
			$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			$email=	$custDetails->CustomerGetResult->Customer->loginInfo->login;
			$firstname=$custDetails->CustomerGetResult->Customer->nameFirst;
			$lastname=$custDetails->CustomerGetResult->Customer->nameLast;
		}

		?>
<div
	class="cont_info"><!-- <div id="errordiv" class="erordiv_name">&nbsp;</div> -->
<div class="ttle_txt">First Name</div>
		<?php input_wordsonly("firstname",$firstname,0,255,"class=inpt onKeyPress=get_register_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivname" class="erordiv_name"></div>
</div>
<!--1st input end -->
<div class="cont_info">
<div class="ttle_txt">Last Name</div>
		<?php input_wordsonly("lastname",$lastname,0,255,"class=inpt onKeyPress=get_register_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivlastname" class="erordiv_name"></div>
</div>
<!--2nd input end -->
<div class="cont_info">
<div class="ttle_txt">Email Address</div>
		<?php input_email("viauserid",$email,0,0,"class=inpt onKeyPress=get_register_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivemail" class="erordiv_name"></div>
</div>
		<?
	}
	function acceptTermsHousing()
	{
		global $HTPFX,$HTHOST,$IMG_SERVER,$tabStart;
		if(empty($tabStart)){
			$tabStart=1;
		}
		if(!$_SESSION['viaid']){ ?>
<div class="cont_info">
<div class="mvl_digest_chkbox"><?php input_check("alerts",$alerts,"checked=checked tabindex=".$tabStart++); ?></div>
<div class="mvl_digest"><strong>Minyanville Digest</strong><br />
Yes, I'd like a daily email summary of the articles published on
Minyanville.com</div>
</div>
<div class="cont_info">
<div class="mvl_digest_chkbox"><?php input_check("dailyfeed",$dailyfeed,"checked=checked tabindex=".$tabStart++); ?></div>
<div class="mvl_digest"><strong>The Daily Feed</strong><br />
News around the web you may have missed, with a unique spin.</div>
</div>
		<? } ?>
<div id="reg_error"
	class="erordiv_duplicate_email"></div>
<div
	id="errordivtermuse" class="erordiv_termofuse"></div>

<div class="bttn_nxtstep" style="cursor: pointer; margin-left: 0px;">
<div style="width: 300px; float: left; margin-top: 10px;">
<div class="mvl_digest_chkbox"><?php input_check("terms",$terms,"tabindex=".$tabStart++); ?></div>
<div class="mvl_digest">I hereby accept Minyanville's <a
	onclick="window.open('<?=$HTPFX.$HTHOST?>/company/substerms.htm','terms','width=560,height=500,resizable=1,scrollbars=1');"
	href="javascript:void(0);">Terms of Use</a></div>
</div>
<div class="nextButtonProductRegistration"><a
	onclick="housingMarketRegistration(); return false;"><input
	type="image" id="reg_next_en" onKeyPress="get_register_keys(event);"
	src="<?=$IMG_SERVER?>/images/housingReport/purchase_btn.jpg"
	width="120" height="39" alt="Next Step" tabindex="10" /></a> <input
	type="image" id="reg_next_ds" style="display: none;"
	src="<?=$IMG_SERVER?>/images/housingReport/purchase_btn.jpg"
	width="120" height="39" alt="Next Step" /></div>





</div>
		<?
	}
	function registrationUserDetail($userloggedin){
		global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;

		$objVia=new Via();
		if(isset($_SESSION['new_register_account']))
		{
			$email=	$_SESSION['new_register_account']['viauserid'];
			$firstname=$_SESSION['new_register_account']['firstname'];
			$lastname=$_SESSION['new_register_account']['lastname'];
			$password=$_SESSION['new_register_account']['viapass'];
			$repassword=$_SESSION['new_register_account']['viarepass'];
			$remeber_me = $_SESSION['new_register_account']['remember_me'];
			$alerts = $_SESSION['new_register_account']['alerts'];
			$dailyfeed = $_SESSION['new_register_account']['dailyfeed'];
			$terms = $_SESSION['new_register_account']['terms'];

		}
		else if($_SESSION['viaid'])
		{
			$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			$email=	$custDetails->CustomerGetResult->Customer->loginInfo->login;
			$firstname=$custDetails->CustomerGetResult->Customer->nameFirst;
			$lastname=$custDetails->CustomerGetResult->Customer->nameLast;
		}
		if(!isset($_SESSION['new_register_account']))
		{
			$existing_user_display = "none";
			$new_user_checked = 'checked';
			$remeber_me = 1;
			$alerts = 1;
		}
		else
		{
			$new_user_display = "none";
			$existing_user_checked = 'checked';
			$remeber_me = 1;
		}

		?>
<!--<body onunload="set_in_session('createAccountform');">-->
		<? if(!isset($_SESSION['viaid'])) {?>
<div class="user_login">
<div class="login_reg">
<div class="login_radio"><input type="radio" value="reg"
	name="radUser[]" id="radUser" <?=$new_user_checked;?>
	onclick="login_reg(this.value);"/ ></div>
<div style="float: left;">&nbsp;I want to create an account</div>
</div>

<div class="login_reg">
<div class="login_radio"><input type="radio" value="login"
	name="radUser[]" id="radUser" <?=$existing_user_checked;?>
	onclick="login_reg(this.value);" /></div>
<div style="float: left;">&nbsp;I want to login into my account</div>
</div>
</div>
		<? } ?>
<div id="existing_user" class="bllng_content" style="display:<?=$existing_user_display;?>;">

<div class="cont_info">
<div class="ttle_txt">Email Address</div>
		<?php input_email("email","",0,255,"class=inpt TABINDEX=1"); ?>
<div id="loginerrorname" class="erordiv_name"></div>
</div>
<!--1st input end -->
<div class="cont_info">
<div class="ttle_txt">Password</div>
		<?php input_pass("password","",20,255,"class=inpt onKeyPress= get_login_keys(event); TABINDEX=2");  ?>
<div id="loginerrorpassword" class="erordiv_name"></div>
</div>
<!--2nd input end -->
<div class="cont_info">
<div class="ttle_txt"><?php input_check("viauserremember",$remeber_me,"TABINDEX=3"); ?></div>
<div class="remember_info">Remember my Member ID and Password at this
computer</div>
</div>
<div class="nexstep_div">
<div id="loginerror" class="erordiv_processing"></div>
<div class="bttn_nxtstep1" style="cursor: pointer;"><a
	id="login_next_en"
	href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/billing/"
	onclick="userLogin(); return false;"> <input type="image"
	src="<?=$IMG_SERVER?>/images/registration/bttn_nextstep.jpg"
	width="120" height="39" alt="Next Step" tabindex="4" /></a> <input
	type="image" id="login_next_ds" style="display: none;"
	src="<?=$IMG_SERVER?>/images/registration/bttngrey_nextstep.jpg"
	width="120" height="39" alt="Next Step" /></div>
</div>
</div>
<div id="new_user" class="bllng_content" style="display:<?=$new_user_display;?>;">
<div class="cont_info"><!-- <div id="errordiv" class="erordiv_name">&nbsp;</div> -->
<div class="ttle_txt">First Name</div>
		<?php input_wordsonly("firstname",$firstname,0,255,"class=inpt onKeyPress=get_register_keys(event); tabindex=1"); ?>
<div id="errordivname" class="erordiv_name"></div>
</div>
<!--1st input end -->
<div class="cont_info">
<div class="ttle_txt">Last Name</div>
		<?php input_wordsonly("lastname",$lastname,0,255,"class=inpt onKeyPress=get_register_keys(event); TABINDEX=2"); ?>
<div id="errordivlastname" class="erordiv_name"></div>
</div>
<!--2nd input end -->
<div class="cont_info">
<div class="ttle_txt">Email Address</div>
		<?php input_email("viauserid",$email,0,0,"class=inpt onKeyPress=get_register_keys(event); TABINDEX=3"); ?>
<div id="errordivemail" class="erordiv_name"></div>
</div>
<!--3rd input end --> <? if(!isset($_SESSION['viaid'])) {?>
<div class="cont_info">
<div class="ttle_txt">Password</div>
		<?php input_pass("viapass",$password,20,255,"class=inpt onKeyPress=get_register_keys(event); TABINDEX=4"); ?>
<div id="errordivpassword" class="erordiv_name"></div>
</div>
<!--4th input end -->
<div class="cont_info">
<div class="ttle_txt">Re-enter Password</div>
		<?php input_pass("viarepass",$repassword,20,255,"class=inpt onKeyPress=get_register_keys(event); TABINDEX=5"); ?>
<div id="errordivrepassword" class="erordiv_name"></div>
</div>
<!--5th input end -->
<div class="cont_info">
<div class="ttle_txt"><?php input_check("viauserremember",$remeber_me,"checked=checked tabindex=6"); ?></div>
<div class="remember_info">Remember my Member ID and Password at this
computer</div>
</div>
<!--6th input end -->
<div style="clear: both;">&nbsp;</div>
<div class="cont_info">
<div class="mvl_digest_chkbox"><?php input_check("alerts",$alerts,"checked=checked tabindex=7"); ?></div>
<!--<input type="checkbox" class="ac_chckbox" />-->
<div class="mvl_digest"><strong>Minyanville Digest</strong><br />
Yes, I'd like a daily email summary of the articles published on
Minyanville.com</div>
</div>
<!--Mvil Digest end -->
<div class="cont_info">
<div class="mvl_digest_chkbox"><?php input_check("dailyfeed",$dailyfeed,"checked=checked tabindex=8"); ?></div>
<!--<input type="checkbox" class="ac_chckbox" />-->
<div class="mvl_digest"><strong>The Daily Feed</strong><br />
News around the web you may have missed, with a unique spin.</div>
</div>
<!--Mvil DailyFeed end -->
<div id="reg_error" class="erordiv_duplicate_email"></div>
<div id="errordivtermuse" class="erordiv_termofuse"></div>

<div class="bttn_nxtstep" style="cursor: pointer; margin-left: 0px;">
<div style="width: 300px; float: left; margin-top: 10px;">
<div class="mvl_digest_chkbox"><?php input_check("terms",$terms,"tabindex=9"); ?></div>
<!--<input type="checkbox" class="ac_chckbox" />-->
<div class="mvl_digest">I hereby accept Minyanville's <a
	onclick="window.open('<?=$HTPFX.$HTHOST?>/company/substerms.htm','terms','width=560,height=500,resizable=1,scrollbars=1');"
	href="javascript:void(0);">Terms of Use</a></div>
</div>
<div class="nextButtonProductRegistration"><a
	href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/billing/"
	onclick="product_registration(); return false;"><input type="image"
	id="reg_next_en" onKeyPress="get_register_keys(event);"
	src="<?=$IMG_SERVER?>/images/registration/bttn_nextstep.jpg"
	width="120" height="39" alt="Next Step" tabindex="10" /></a> <input
	type="image" id="reg_next_ds" style="display: none;"
	src="<?=$IMG_SERVER?>/images/registration/bttngrey_nextstep.jpg"
	width="120" height="39" alt="Next Step" /></div>





</div>
<!--Next Step end --> <? } else {?>
<div class="bttn_nxtstep" style="cursor: pointer;"><a
	href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/billing/"
	onclick="create_account(); return false;"> <input type="image"
	onKeyPress="get_register_keys(event);"
	src="<?=$IMG_SERVER?>/images/registration/bttn_nextstep.jpg"
	width="120" height="39" alt="Next Step" tabindex="4" /> </a></div>

		<? } ?></div>
<!--Content end -->
<!--</body>-->
		<?
	}

	function registerationNeedHelp(){
		?>
<div class="needhelp"><strong>Need Help?</strong><br />
Call now to finish booking <br />
<strong>212-991-9357</strong><br />
Or Email us at <a href="mailto:support@minyanville.com">support@minyanville.com</a>
</div>
<!--Need Help area End -->
		<?
	}

	function registrationBillingInfo(){
		global $tabStart;
		if(empty($tabStart)){
			$tabStart=1;
		}
		$objVia=new Via();
		$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
		if($_SESSION['new_register_billing'])
		{
			$address1=$_SESSION['new_register_billing']['address1'];
			$address2=$_SESSION['new_register_billing']['address2'];
			$city=$_SESSION['new_register_billing']['city'];
			$state=$_SESSION['new_register_billing']['state'];
			$zipcode=$_SESSION['new_register_billing']['zipcode'];
			$phone=$_SESSION['new_register_billing']['phone'];
			$country=$_SESSION['new_register_billing']['country'];
		}
		else if($_SESSION['viaid'])
		{
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			if(is_array($custDetails->CustomerGetResult->Customer->addresses)){
				$custDetails->CustomerGetResult->Customer->addresses=$custDetails->CustomerGetResult->Customer->addresses[0];
			}
			$address1=$custDetails->CustomerGetResult->Customer->addresses->address1;
			$address2=$custDetails->CustomerGetResult->Customer->addresses->address2;
			$city=$custDetails->CustomerGetResult->Customer->addresses->city;
			$state=$custDetails->CustomerGetResult->Customer->addresses->state;
			$zipcode=$custDetails->CustomerGetResult->Customer->addresses->zip;
			$phone=$custDetails->CustomerGetResult->Customer->phone;
			$country=$custDetails->CustomerGetResult->Customer->addresses->country;
		}
		//if($country=='USA') $country='AA'; else $country=$state;
		if(isset($_SESSION['viacart']['SUBSCRIPTION']))
		{
			foreach($_SESSION['viacart']['SUBSCRIPTION'] as $arVal)
			{
				$arProduct[] = $arVal['subscription_def_id'];
			}
		}
		if(is_array($arProduct))
		{
			$stProductId = implode(",",$arProduct);
		}
		if($country=="" || $country=="USA"){
			$country="AA";
		}elseif($country=="CANADA"){
			$country="AB";
		}elseif(!$_SESSION['new_register_billing']){
			$country=$state;
		}
		?>

<!--<div id="regerror" class="erordiv"></div>-->
<input
	type="hidden" id="session_id" value="<?=$_SESSION['SID']?>" />
<input
	type="hidden" id="email" value="<?=$_SESSION['email']?>" />
<input
	type="hidden" id="firstname" value="<?=$_SESSION['nameFirst']?>" />
<input
	type="hidden" id="lastname" value="<?=$_SESSION['nameLast']?>" />
<input
	type="hidden" id="product_id" value="<?=$stProductId?>" />
<div class="cont_info">
<div class="ttle_txt">Billing Address</div>
		<?php input_text("address1",$address1,15,255,"class=inpt style=background-color:#FFFFFF;  onKeyPress=get_order_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivaddress" class="erordiv_name"></div>
</div>
<!--1st input end -->
<div class="cont_info">
<div class="ttle_txt">Billing Address 2</div>
		<?php input_text("address2",$address2,15,255,"class=inpt style=background-color:#FFFFFF; onKeyPress=get_order_keys(event); tabindex=".$tabStart); ?>
<!--<div style="height:15px;">&nbsp;</div>--></div>
<!--2nd input end -->
<div class="cont_info">
<div class="ttle_txt">City</div>
		<?php input_wordsonly("city",$city,15,255,"class=inpt style=background-color:#FFFFFF; onKeyPress=get_order_keys(event); tabindex=".$tabStart); ?>
<div id="errordivcity" class="erordiv_name"></div>
</div>
<!--3rd input end -->
<div class="cont_info">
<div class="ttle_txt">State</div>
<span id="state-div"> <? if($country =="AA") { ?> <select tabindex="<?php echo $tabStart++; ?>" id="state" name="state"
	style="background-color: #FFFFFF;" class="slct_option"
	onKeyPress="get_order_keys(event);">
	<option value="">--Select--<?php display_states($state); ?>

</select> <? } elseif($country =="AB") { ?> <select tabindex="<?php echo $tabStart++; ?>"
 id="state" name="state"
	style="background-color: #FFFFFF;" class="slct_option"
	onKeyPress="get_order_keys(event);">
	<option value="">--Select--<?php display_canada_states($state); ?>

</select> <? }else { ?> <select tabindex="<?php echo $tabStart++; ?>" 
	disabled="" id="state" name="state" style="background-color: #FFFFFF;"
	class="slct_option" onKeyPress="get_order_keys(event);">
	<option value="">--Select--<?php display_states($state); ?>

</select> <? } ?> </span>
<div id="errordivstate" class="erordiv_selectbox"></div>
</div>
<!--4th input end -->
<div class="cont_info">
<div class="ttle_txt">Zip Code</div>
<?php input_text("zipcode",$zipcode,10,255,"class=zip_inpt style=background-color:#FFFFFF; onKeyPress=get_order_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivzip" class="erordiv_selectbox"></div>
</div>
<!--5th input end -->
<div class="cont_info">
<div class="ttle_txt">Country</div>
<select tabindex="<?php echo $tabStart++; ?>" <?php echo $strNoTab; ?> id="country"
	style="background-color: #FFFFFF;"
	onChange="Javascript:manage_state('country','state');"
	onKeyPress="get_order_keys(event);" name="country" class="slct_option">
	<option value="">--Select--<?php echo $objVia->populateCountry($country); ?>

</select>
<div id="errordivcountry" class="erordiv_selectbox"></div>
</div>
<!--6th input end -->
<div class="cont_info">
<div class="ttle_txt">Phone</div>
<?php input_text("phone",$phone,10,255,"class=zip_inpt style=background-color:#FFFFFF; onKeyPress=get_order_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivphone" class="erordiv_name"></div>
</div>
<!--7th input end -->
<?
	}

	function registrationCreditCardInfo($userloggedin){
		global $HTPFX,$HTHOST,$IMG_SERVER,$HTPFXSSL,$tabStart;
		if(empty($tabStart)){
			$tabStart=1;
		}
		$objVia=new Via();
		if($_SESSION['viaid'])
		{
			$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
			$objVia=new Via();
			$custDetails=$objVia->getCustomersViaDetail($arrayFields);
			if($custDetails->CustomerGetResult->Customer->ccNumber!=''){
				$custCCNUM='************'.$custDetails->CustomerGetResult->Customer->ccNumber;
			}
			else{
				$custCCNUM='';
			}
		}
		if($_SESSION['new_register_billing'])
		{
			$ccType =$_SESSION['new_register_billing']['cctype'];
			$ccNumber=$_SESSION['new_register_billing']['ccnum'];
			$ccExpire=$_SESSION['new_register_billing']['year']."-".$_SESSION['new_register_billing']['month'];
			$cvvNum = $_SESSION['new_register_billing']['cvvnum'];
		}
		else if($_SESSION['viaid'])
		{
			$ccType=$custDetails->CustomerGetResult->Customer->ccType;
			if($custDetails->CustomerGetResult->Customer->ccNumber!=''){
				$ccNumber='************'.$custDetails->CustomerGetResult->Customer->ccNumber;
			}
			else{
				$ccNumber='';
			}
			if($custDetails->CustomerGetResult->Customer->ccExpire=='0001-01-01T00:00:00'){
				$ccExpire='';
			}
			else
			{
				$ccExpire=substr($custDetails->CustomerGetResult->Customer->ccExpire,0,7);
			}

		}
		$creditCardType=array('MasterCard'=>'','Visa'=>'','AmericanExpress'=>'','Discover'=>'');
		$creditCardType[$ccType]='selected';
		?>
<div class="CC_infopanel">
<div class="CC_img">
<h2>Credit Card Information</h2>
<img src="<?=$IMG_SERVER?>/images/registration/CC_01.gif" width="35"
	height="24" alt="" /> <img
	src="<?=$IMG_SERVER?>/images/registration/CC_02.gif" width="35"
	height="24" alt="" /> <img
	src="<?=$IMG_SERVER?>/images/registration/CC_03.gif" width="35"
	height="24" alt="" /> <img
	src="<?=$IMG_SERVER?>/images/registration/CC_04.gif" width="35"
	height="24" alt="" /> <img
	src="<?=$IMG_SERVER?>/images/registration/CC_05.gif" width="35"
	height="24" alt="" /> <img
	src="<?=$IMG_SERVER?>/images/registration/CC_06.gif" width="35"
	height="24" alt="" /> <span id='last_img'> <script
	type="text/javascript"
	src="https://seal.thawte.com/getthawteseal?host_name=www.minyanville.com&amp;size=M&amp;lang=en"></script>
</span></div>
<!--Img of CC end -->
<div class="CC_form">
<div class="cont_info">
<div class="ttle_txt_bill">Card Type</div>
<input type="hidden" id="hidCNUM" value="<?=$custCCNUM?>" /> <select
	tabindex="<?php echo $tabStart++;?>" id="cctype" name="cctype" class="slct_option"
	onKeyPress="Javascript:get_manage_keys(event);">
	<option value="">--select--</option>
	<option value="MasterCard" <?=$creditCardType[MasterCard]?>>Master Card</option>
	<option value="Visa" <?=$creditCardType[Visa]?>>Visa</option>
	<option value="AmericanExpress" <?=$creditCardType[AmericanExpress]?>>American
	Express</option>
	<option value="Discover" <?=$creditCardType[Discover]?>>Discover</option>
</select>
<div id="errordivcctype" class="erordiv_selectbox"></div>
</div>
<!--1th CC input end -->
<div class="cont_info">
<div class="ttle_txt_bill">Card Number</div>
		<?php input_text("ccnum",$ccNumber,15,16,"class=inpt style=background-color:#FFFFFF;  onKeyPress=get_order_keys(event); tabindex=".$tabStart++); ?>
<div id="errordivccnum" class="erordiv_name"></div>
</div>
<!--2nd CC input end -->
<div class="cont_info">
<div class="ttle_txt_bill">Exp. Date</div>
		<?
		$month ='';
		$year = '';
		if($ccExpire != "") {
			$ccDate=explode("-",$ccExpire);
			// $month=date('m',strtotime($ccExpire));
			// $year=date('Y',strtotime($ccExpire));
			$year=$ccDate[0];
			$month=$ccDate[1];
		}
		month_options("month",$month,"","month","tabindex=".$tabStart++,"class=slct_option_month"); ?>
&nbsp;&nbsp; <? year_options("year",$year,"0",date("Y")+13,2011,"year","tabindex=".$tabStart++,"class=slct_option_year"); ?>
<div id="errordivccexpire" class="erordiv_name"></div>
</div>
<!--3rd CC input end -->
<div class="cont_info">
<div class="ttle_txt_bill">Card Security Code</div>
<div class="CC_seccode"><?php input_numsonly("cvvnum",$cvvNum,4,4,"class=small_common_input  style=background-color:#FFFFFF;  onKeyPress=get_order_keys(event); tabindex=".$tabStart++); ?>&nbsp;&nbsp;<a
	onclick="window.open('<?=$HTPFXSSL.$HTHOST?>/subscription/register/cvv2.htm','cvv2','width=560,height=500,resizable=1,scrollbars=1')"
	tabindex="17" href="javascript:void(0);">What is this?</a></div>
<div id="errordivcvvnum" class="erordiv_name"></div>
</div>
<!--4th CC input end --></div>
</div>
<!--CC info end -->
		<?
	}

	function registrationCancellationPolicy(){
		global $free_trial_products,$HTPFXSSL;
		?>
<div class="cnlplcy_info">You may contact customer service at any time
at 212-991-9357 or <a href="mailto:support@minyanville.com">support@minyanville.com</a>
for changes to your account.</div>
<!--Cancellation policy end -->

		<?
	}

	function userproductinfo() {
		global $promoCodeSourceCodeNoFreeTrial,$pageName,$viaOrderClassId;
		?>
<div class="user_bllnginfo"><?
foreach($_SESSION['viacart']['SUBSCRIPTION'] as $product)
{
	$arOrderClassId = array();
	foreach($_SESSION['products']['SUBSCRIPTION'] as $arProduct)
	{
		$arOrderClassId[] = $arProduct['orderClassId'];
	}
	$show_price_value = "";
	if($_SESSION['cancelledOrdersStatus']==''){
		$objVia=new Via();
		$cancelledOrderStatus = $objVia->get_cancelled_order_status_with_ocId($_SESSION["viaid"]);
		set_sess_vars("cancelledOrdersStatus",$cancelledOrderStatus);
	}

	if(array_key_exists($product['oc_id'],$_SESSION['cancelledOrdersStatus']))
	{
		$searchWord = '/^cancel/';
		preg_match($searchWord, strtolower($_SESSION['cancelledOrdersStatus'][$product['oc_id']]), $matches, PREG_OFFSET_CAPTURE);
		if((!empty($matches)) || ($_SESSION['cancelledOrdersStatus'][$product['oc_id']] == "SHIPPED_COMPLETE") )
		{
			$show_price_value = "showPrice";
		}
	}

	if($show_price_value ==''){
		if((in_array($product['oc_id'],$viaOrderClassId)) || (in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial))){
			$show_free_trial = "showPrice";
		}
		elseif(count($arOrderClassId) > 0 && in_array($product['oc_id'],$arOrderClassId))
		{
			$show_free_trial = "showPrice";
		}
		elseif(($pageName=="subscription_product_billing") || ($pageName=="subscription_product_order")){
			$show_free_trial = "showBoth";
			$show_disclaimers = 1;
		}
		else
		{
			$show_free_trial = "showBoth";
			$show_disclaimers = "showBoth";
		}
	}else{
		$show_free_trial = $show_price_value;
		$show_disclaimers = "0";
	}

	if($show_free_trial == "showBoth"){
		?>
<div class="usercart_freetrial"
	style="width: 100%; padding: 0px; margin: 0px;">
<div class="usercart_inside"><?=$product['product']?> 14 Day Free Trial</div>
<div class="money_dtl">$0.00</div>
<div class="edit"><a
	onclick="remove_product('<?=$product['subscription_def_id']?>','<?=$product['orderItemType']?>');"
	href="javascript://">Remove</a></div>
</div>
		<?
	}
	?>
<div class="usercart_inside"><?=$product['product']?></div>
<div class="money_dtl">$<?=$product['price']?></div>
<div class="edit"><a
	onclick="remove_product('<?=$product['subscription_def_id']?>','<?=$product['orderItemType']?>');"
	href="javascript://">Remove</a></div>
	<?
}
?></div>
<!--1st end -->
<? }

function registrationSubmitOrder(){
	global $D_R;
	include_once($D_R.'/lib/config/_products_config.php');
	global $HTPFX, $HTHOST,$_SESSION,$IMG_SERVER,$viaProducts,$promoCodeSourceCodeNoFreeTrial,$HTPFXSSL;
	$objRegProdData = new regitrationProductData();

	if($_SESSION['viaid'])
	{
		$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
		$objVia=new Via();
		$custDetails=$objVia->getCustomersViaDetail($arrayFields);
		if($custDetails->CustomerGetResult->Customer->ccNumber!=''){
			$custCCNUM='************'.$custDetails->CustomerGetResult->Customer->ccNumber;
		}
		else{
			$custCCNUM='';
		}
		$custCCExpire=substr($custDetails->CustomerGetResult->Customer->ccExpire,0,7);
		if($custCCExpire=='0001-01'){
			$custCCExpire='';
		}
	}
	if($_SESSION['new_register_account'])
	{
		$userName = $_SESSION['new_register_account']['firstname'].' '.$_SESSION['new_register_account']['lastname'];
		$email = $_SESSION['new_register_account']['viauserid'];
		$firstName = $_SESSION['new_register_account']['firstname'];
		$lastName = $_SESSION['new_register_account']['lastname'];
	}
	else if($_SESSION['viaid'])
	{
		$userName=$custDetails->CustomerGetResult->Customer->nameFirst." ".$custDetails->CustomerGetResult->Customer->nameLast;
		$email=	$custDetails->CustomerGetResult->Customer->loginInfo->login;
		$firstName = $custDetails->CustomerGetResult->Customer->nameFirst;
		$lastName = $custDetails->CustomerGetResult->Customer->nameLast;
	}
	if($_SESSION['new_register_billing'])
	{
		$address1=$_SESSION['new_register_billing']['address1'];
		$address2=$_SESSION['new_register_billing']['address2'];
		$city=$_SESSION['new_register_billing']['city'];
		$state=$_SESSION['new_register_billing']['state'];
		$zipcode=$_SESSION['new_register_billing']['zipcode'];
		$country=$_SESSION['new_register_billing']['country'];
		$phone=$_SESSION['new_register_billing']['phone'];
	}
	else if($_SESSION['viaid'])
	{
		$address1=$custDetails->CustomerGetResult->Customer->addresses->address1;
		$address2=$custDetails->CustomerGetResult->Customer->addresses->address2;
		$city=$custDetails->CustomerGetResult->Customer->addresses->city;
		$state=$custDetails->CustomerGetResult->Customer->addresses->state;
		$zipcode=$custDetails->CustomerGetResult->Customer->addresses->zip;
		$phone=$custDetails->CustomerGetResult->Customer->phone;
		$country=$custDetails->CustomerGetResult->Customer->addresses->country;
	}
	if($state == "")
	{
		$hidState = "NY";
	}
	else
	{
		$hidState = $state;
	}
	if($_SESSION['new_register_billing'])
	{
		$ccType =$_SESSION['new_register_billing']['cctype'];
		$ccNumber=$_SESSION['new_register_billing']['ccnum'];
		$ccExpire=$_SESSION['new_register_billing']['year']."-".$_SESSION['new_register_billing']['month'];
		$cvvNum = $_SESSION['new_register_billing']['cvvnum'];
	}
	else if($_SESSION['viaid'])
	{
		$ccType=$custDetails->CustomerGetResult->Customer->ccType;
		if($custDetails->CustomerGetResult->Customer->ccNumber!=''){
			$ccNumber='************'.$custDetails->CustomerGetResult->Customer->ccNumber;
		}
		else{
			$ccNumber='';
		}
		$ccExpire=substr($custDetails->CustomerGetResult->Customer->ccExpire,0,7);
		if($ccExpire=='0001-01'){
			$ccExpire='';
		}
		$cvvNum = $custDetails->CustomerGetResult->Customer->cvvNumber;
	}
	?>
<div
	class="bllng_content">

<div id="billingProductInfo"><?= $this->userproductinfo(); ?></div>
<div class="user_bllnginfo"><input type="hidden" id="uid"
	value="<?=$email?>" /> <input type="hidden" id="uid_default"
	value="<?=$custDetails->CustomerGetResult->Customer->loginInfo->login?>" />
<input type="hidden" id="firstname" value="<?=$firstName?>" /> <input
	type="hidden" id="lastname" value="<?=$lastName?>" /> <input
	type="hidden" id="address1" value="<?=$address1?>" /> <input
	type="hidden" id="address2" value="<?=$address2?>" /> <input
	type="hidden" id="city" value="<?=$city?>" /> <input type="hidden"
	id="state" value="<?=$hidState?>" /> <input type="hidden" id="zipcode"
	value="<?=$zipcode?>" /> <input type="hidden" id="country"
	value="<?=$country?>" /> <input type="hidden" id="phone"
	value="<?=$phone?>" /> <input type="hidden" id="cctype"
	value="<?=$ccType?>" /> <input type="hidden" id="ccnum"
	value="<?=$ccNumber?>" /> <input type="hidden" id="ccexpire"
	value="<?=$ccExpire?>" /> <input type="hidden" id="cvvnum"
	value="<?=$cvvNum?>" /> <input type="hidden" id="hidCNUM"
	value="<?=$custCCNUM?>" /> <input type="hidden" id="hidCEXP"
	value="<?=$custCCExpire?>" /> <input type="hidden" id="hidSubCount"
	value="<?=count($_SESSION['viacart']['SUBSCRIPTION']);?>" />
<div class="usercart_inside"><?=$userName;?></div>
<div class="edit"><a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register">Edit</a></div>
<div class="usercart_inside"><?=$email?></div>
</div>
<!--2nd end -->
<div class="user_bllnginfo">
<div class="usercart_inside"><?=$address1?></div>
<div class="edit"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/billing">Edit</a></div>
	<? if($address2 !=''){ ?>
<div class="usercart_inside"><?=$address2?></div>
	<? } ?>
<div class="usercart_inside"><?=$city?> <span><?
if($country!=="AA" && $country!=="AB"){ $state=""; } echo $state;
?></span> <span><?=$zipcode?></span></div>
<div class="usercart_inside"><? if($country=="AA"){ echo "USA";}
elseif($country=="AB"){ echo "CANADA";}
else{ echo $country;}?></div>
<div class="usercart_inside"><?=$phone?></div>






<?php /*?><?=$address1?><br /><br /><?=$address2?><br /><br /><?=$city?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$state?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$zipcode?><br /><br /><? if($country=="AA"){ echo "USA";} else{ echo $country;}?><br /><br /><?=$phone?><?php */?>

</div>
<!--3rd end -->
<div class="user_bllnginfo_last">
<div class="usercart_inside"><?=$ccType?></div>
<div class="edit"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/billing">Edit</a></div>
<div class="usercart_inside"><?
if($ccType=="AmericanExpress"){
	?> xxxx-xxxxxx-x<?=substr($ccNumber,-4);?> <?
}else{
	?> xxxx-xxxxxxx-x<?=substr($ccNumber,-4);?> <?
}
?></div>
<div class="usercart_inside"><?=$ccExpire?></div>
<div class="usercart_inside"><? $cvvlength = strlen($cvvNum); echo substr("xxxxxxxxxxx",0,$cvvlength); ?></div>
</div>
<!--4th end -->



<div id="regerror" class="erordivsubmit"></div>
<div class="bttn_cnclsub" style="cursor: pointer; margin-left: 0px;">
<div style="width: 300px; float: left; margin-top: 10px;">
<div class="mvl_digest_chkbox"><?php input_check("terms",1,"tabindex=9"); ?></div>
<!--<input type="checkbox" class="ac_chckbox" />-->
<div class="mvl_digest">I hereby accept Minyanville's <a
	onclick="window.open('<?=$HTPFX.$HTHOST?>/company/substerms.htm','terms','width=560,height=500,resizable=1,scrollbars=1');"
	href="javascript:void(0);">Terms of Use</a></div>
</div>
<div class="nextButtonProductRegistration"><a
	href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/welcome.htm"
	onclick="final_registration(); return false;"><img id="final_next_en"
	style="cursor: pointer"
	src="<?=$IMG_SERVER?>/images/registration/bttn_submitbig.jpg"
	width="118" height="37" alt="" /></a> <img id="final_next_ds"
	style="display: none;"
	src="<?=$IMG_SERVER?>/images/registration/bttngrey_nextstep.jpg"
	width="118" height="37" alt="" /></div>





</div>
<!--Next Step end --></div>
<!--Content end -->

<?
}

function registerationHeaderName($page_name){
	global $page_config, $D_R, $HTPFX, $HTHOST;
	if(isset($_SESSION['viaid']) && is_numeric($_SESSION['viaid'])){ ?>
<div class="registerTitle"><?=ucwords(strtolower($_SESSION['nameFirst']))?>'s
account Settings</div>
<div class="registerLoginTab"><?php include_once($D_R.'/ex_header.htm'); ?></div>
	<?	}else if($page_name == 'subscription_product_registration'){ ?>
<div class="headertxt">Please Create an Account or Login Here</a></div>
	<?	}
}

}

?>