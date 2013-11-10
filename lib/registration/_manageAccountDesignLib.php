<?php
class manageAccountDesignLib{
	function displayManageAccount(){
		global $_SESSION;
		?>
		<div class="welcomeContainer"><? $this->displayWelcomeInfo();?></div>
		<div class="infoContainer"><? $this->displayUserInfo();?></div>
		<div class="creditContainer"><? $this->displayCreditInfo();?></div>
		<div class="subsContainer"><? $this->displaySubscriptionInfo();?></div>
		<?
	}

	function displayWelcomeInfo(){
?>
	<div class="welcomeUsername">Welcome, <?=ucfirst(strtolower($_SESSION['nameFirst']));?></div>
	<div class="welcomeInfo">If you wish to change your information please make a correction as needed, then press <span class="manageSaveGreen">SAVE</span></div>
<?

	}

	function displayUserInfo(){
		$objData= new manageAccountDataLib();
		$getUserInfo=$objData->getUserInfo($_SESSION['SID']);
		$state=$getUserInfo['state'];
		$country=$getUserInfo['country'];
		$objUserData= new userData();
		$userPassword=$objUserData->decryptUserPassword($getUserInfo['password']);
		?>

		<div class="accountInfoContainer">
			<div class="managePageAccountInfoLabel">Account Information</div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Email Address(username)</label><input id="manageemail" name="managemail" value="<?=$_SESSION['email'];?>" type="text" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Password</label>
				<input type="password" id="managePassword" name="managePassword" value="<?=$userPassword?>" class="manageAccountText" onfocus="javascript:checkPasswordField('managePassword');" onblur="javascript:chkSpaceNull('managePassword',this.value,'Password');" onkeydown="removeErrorDiv(this.id);" onfocus="javascript:checkPasswordField('confirmPassword');">
			</div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Confirm Password</label>
				<input type="password" id="confirmPassword" name="confirmPassword" value="<?=$userPassword?>" class="manageAccountText" onblur="javascript:chkSpaceNull('confirmPassword',this.value,'Password');" onkeydown="removeErrorDiv(this.id);" onfocus="javascript:checkPasswordField('confirmPassword');" >
			</div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">First Name</label><input type="text" id="firstname" name="firstname" value="<?=ucfirst(strtolower($_SESSION['nameFirst']));?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Last Name</label><input type="text" id="lastname" name="lastname" value="<?=ucfirst(strtolower($_SESSION['nameLast']));?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
		</div>
		<div class="billingInfoContainer">
			<div class="managePageAccountInfoLabel" style="">Billing Information<span class="manageAccountRequired">required field</span></div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Billing Address Line 1</label><input type="text" id="billingadd1" name="billingadd1" value="<?=$getUserInfo['address'];?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabelBilling2">Billing Address Line 2</label><input type="text" id="billingadd2" name="billingadd2" value="<?=$getUserInfo['address2'];?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">City</label><input type="text" id="city" name="city" value="<?=$getUserInfo['city'];?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
			<div class="accountInfoDetailConainer">
			<label class="manageAccountLabelState">State</label>
			<input type="text" id="state" name="state" value="<?=$getUserInfo['state'];?>" class="manageAccountStateText" onkeydown="removeErrorDiv(this.id);">

			<label class="manageAccountLabelZip">Zip Code</label><input id="zip" name="zip" value="<?=$getUserInfo['zip'];?>" class="manageAccountZipText" type="text" onkeydown="removeErrorDiv(this.id);">
			</div>
			<div class="accountInfoDetailConainer">
				<label class="manageAccountLabel">Country</label>
			<select class="select_country" style="width:270px" id="country" onKeyPress="get_order_keys(event);" name="country" class="manageAccountCountryText" ><option value="select">--Select--</option><?php echo $this->displayCountryManageAccount(); ?></select>
			<? if(!empty($country)){ ?>
			<script language="javascript">
			getSelected('<?=$country?>','country');
			</script>
<? } ?>
				<!--<input type="text" id="country" name="country" value="<?=$getUserInfo['country'];?>" class="manageAccountCountryText"onkeydown="removeErrorDiv(this.id);">-->


			</div>
			<div class="accountInfoDetailConainer"><label class="manageAccountLabel">Phone</label><input type="text" id="phone" name="phone" value="<?=$getUserInfo['tel'];?>" class="manageAccountText" onkeydown="removeErrorDiv(this.id);"></div>
		</div>
		<!--  <div class="manageRequiredField">
			<div class="manageAccountRequired">required field</div>
		</div> -->
		<?
	}

	function displayCreditInfo(){
		$objData= new manageAccountDataLib();
		$getCCInfo=$objData->getManageAccountBillingInfo();
		$currentYear = date("Y");
		if(!empty($getCCInfo)){
		    $ccType=$getCCInfo->card_type;
			$ccLastFourDigit='**********'.$getCCInfo->last_four;
			$ccYear=$getCCInfo->year;
			$ccMonth=$getCCInfo->month;
			//$ccDate=$ccMonth.'/'.$ccYear;
		}

		?>
		<div class="creditInfoContainer">
		<div class="managePageAccountInfoLabel" style="width:100%;">Credit Card Information</div>
		<div class="creditInfoDetailsContainer">
			<div class="creditInfoDetailsLabelContainer">
				<label id="credit_card" class="manageAccountCreditInfoLabel">Credit Card Type</label>
				<label id="card_no" class="manageAccountCreditInfoLabel"><span class="manageAccountSmallGaryText">No"-" or space please</span><br>Credit Card Number</label>
				<label id="exp_date" class="manageAccountCreditInfoLabel">Expiration Date</label>
				<label id="cvv" class="manageAccountCreditInfoLabel"><span class="manageAccountSmallGaryText">CVV2 <a onclick="window.open('<?=$HTPFXSSL.$HTHOST?>/subscription/register/cvv2.htm','cvv2','width=560,height=500,resizable=1,scrollbars=1')"  href="#">?</a></span><br>Security Code</label>
			</div>
			<div class="creditInfoDetailsValueContainer">

				<!--<input type="text" id="manageAccountCreditType" name="manageAccountCreditType" value="<?=$ccType?>" class="manageAccountCreditTypeText" onkeydown="removeErrorDiv(this.id);">-->
				<div class="manageAccountCreditTypeText" ><?=$this->displayCreditCardType($ccType,'manageAccountCreditTypeText');?></div>

				<input type="text" id="manageAccountCreditNumber" name="manageAccountCreditNumber" value="<?=$ccLastFourDigit;?>" class="manageAccountCreditText" onkeydown="removeErrorDiv(this.id);">
				<div class="manageAccountCreditExpirationDiv">
					<div class="manageAccountCreditExpMonth">
					<select class="select_month" id="manageAccountccMonth">
						<? for ($i=1; $i<13; $i++){
							if($i==$ccMonth){ ?>
								<option value="<?=$i;?>" selected="selected"><?=$i;?></option>
							<? 	}else{ ?>
								<option value="<?=$i;?>"><?=$i;?></option>
							<? 	} ?>
						<? } ?>
					</select></div>
					<div class="manageAccountCreditExpYear">
					<select class="select_yr" id="manageAccountccYear">
						<? for ($j=0; $j<20; $j++){
								$year = $currentYear+$j;
								if($year==$ccYear){ ?>
									<option value="<?=$year;?>" selected="selected"><?=$year;?></option>
								<? 	}else{ ?>
									<option value="<?=$year;?>"><?=$year;?></option>
								<? 	} ?>
						<? } ?>
					</select></div>
				</div>
				<input type="text" id="ccSecurityCode" name="ccSecurityCode" value="" class="manageAccountCvvText" onkeydown="removeErrorDiv(this.id);">
			</div>
		</div>
		<div class="creditInfo">Your credit card will not be charged until 14 days from the day you place your order.<br><br>If you wish to cancel your subscription during the trial period, you must email <a href="mailto:support@minyanville.com">support@minyanville.com</a> or call 212-991-9357.</div>
		</div>
		<?


	}

	function displaySubscriptionInfo(){
		global $IMG_SERVER;
		?>
		<div class="managePageAccountInfoLabel" style="width:100%;">Subscriptions</div>
		<div class="subsDetailsOurterContainer">
			<div class="allsubsDetailContainer">
			<div class="subscriptionDetailsContainer"><? $this->displaySubscribedSubscriptions();?></div>
			<div class="avSubscriptionDetailsContainer"><? $this->displayAvailableSybscriptions();?></div>
			</div>
			<div class="saveContainer">
				<div class="productTopImage">
					<img src="<?=$IMG_SERVER;?>/images/registration/sp_myac_bttn_chngsub_top.jpg">
				</div>
				<div class="saveMid">
					<label class="productCallText">Please call <span class="manageContactNumber">212-991-9357</span> to upgrade or change your subscription</label>
					<div class="manageAccountSaveBttn"><img onclick="javascript:submitUserInfo();" onkeypress="Javascript:get_manage_keys(event);" alt="SAVE" src="<?=$IMG_SERVER;?>/images/registration/sp_myac_bttn_save.jpg"></div>
					<div id="messageDiv" class="messageDiv">&nbsp;</div>
					<div id="errorMessageDiv" class="errorMessageDiv">&nbsp;</div>
				</div>
				<div class="productBottomImage">
					<img src="<?=$IMG_SERVER;?>/images/registration/sp_myac_bttn_chngsub_bttm.jpg">

				</div>


			</div>

		</div>
	<?

	}

	function displaySubscribedSubscriptions(){
		global $IMG_SERVER,$viaProductsName;
		$objDatafunnel= new registrationFunnelData();
		$getActiveProduct=$objDatafunnel->getCurrentProducts($_SESSION['SID']);
		if(is_array($getActiveProduct)){
		foreach($getActiveProduct as $key=>$prodRow){
			if($prodRow['subGroup']=="garyk" || $prodRow['subGroup']=="grailetf" || $prodRow['subGroup']=="flexfolio" || $prodRow['subGroup']=="thestockplaybookpremium" || $prodRow['subGroup']=="bmtp" || $prodRow['subGroup']=="buyhedge" || $prodRow['subGroup']=="housingmarket"){
					unset($prodRow);
					continue;
			}

			$activeProductName=$viaProductsName[$prodRow['subGroup']];
			$key=$key + 1;
			$htmKeenePref='';
			if($prodRow['subGroup']=='keene'){
				$getAlertPreference=$objDatafunnel->getAlertPreference($_SESSION['SID']);
				$htmKeenePref = '<div class="keeneMainPref">';
				if($getAlertPreference=='sms'){
					$htmKeenePref .= '<div class="keeneSmsBox">
						<div class="accKeeneHead">Text Message Alerts</div>
						<div class="accKeeneMobileNum">You have registered to Keene On Options text message alerts.</div>
						<div class="accKeeneTerms">Keene On Options Text Alerts - Terms and Conditions: Message and data rates may apply. You are opting to receive text messages when a new Keene On Options article is published, which is equal to approximately 20 messages or more per month.<b> Text STOP to quit receiving messages. Text HELP for assistance.</b> Service availability is on a carrier by carrier basis and based on handset compatibility. You must be 18 years or older to subscribe. Your information will not be shared.</div>
					</div>
					<div class="keeneEmailBox">
						<div class="accKeeneHead">Email Alerts</div>
						<div class="accKeeneSign">Sign up for Keene On Options email alerts by entering a valid email address below: </div>
						<div class="accKeeneExample"><input type="text" id="funnelEmailForAlert" value="email@exmaple.com" onfocus="if(this.value==\'email@exmaple.com\')this.value=\'\'" onBlur="if(this.value==\'\')this.value=\'email@exmaple.com\'" /></div>
						<div class="accKeeneAlertType">Minyanville will send you email alerts:</div>
						<ul class="accKeeneTick"><li><span class="accKeeneTickTxt">When a new Keene On Options article is published.<span></li></ul>
						<div class="accKeeneTerms">Keene On Options Email Alerts - Terms and Conditions: By entering your email address, and checking the box, you are opting to receive receive daily updates, from Keene on Options and special offers from other Minyanville products and services.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelEmailCheck" onclick="javascript:checkEmailValidity();"></div>
							<div class="label1"><label for="funnelEmailCheck" class="accKeeneCheckTxt">I\'d like to sign up for Keene On Options email alerts and I agree with the Terms and &nbsp;&nbsp;&nbsp;&nbsp;Conditions described above.</label></div>
						</div>
					</div></div>';
				}elseif($getAlertPreference=='email'){
					$htmKeenePref .= '<div class="keeneSmsBox">
						<div class="accKeeneHead">Text Message Alerts</div>
						<div class="accKeeneSign">Sign up for Keene On Options text message alerts by entering a valid phone number below: </div>
						<div class="accKeeneExample"><input type="text" id="funnelMobileNum" value="Example: XXX-XXX-XXXX" onfocus="if(this.value==\'Example: XXX-XXX-XXXX\')this.value=\'\'" onBlur="if(this.value==\'\')this.value=\'Example: XXX-XXX-XXXX\'"></div>
						<div class="accKeeneAlertType">Minyanville will send you text message alerts:</div>
						<ul class="accKeeneTick"><li><span class="accKeeneTickTxt">When a new Keene On Options article is published.<span></li></ul>
						<div class="accKeeneTerms">Keene On Options Text Alerts - Terms and Conditions: Message and data rates may apply. You are opting to receive text messages when a new Keene On Options article is published, which is equal to approximately 20 messages or more per month.<span> Text STOP to quit receiving messages. Text HELP for assistance.</span> Service availability is on a carrier by carrier basis and based on handset compatibility. You must be 18 years or older to subscribe. Your information will not be shared.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelSmsCheck" onclick="javascript:checkFoneValidity();"></div>
							<div><label for="funnelSmsCheck" class="accKeeneCheckTxt">I\'d like to sign up for Keene On Options text message alerts and I agree with the Terms and &nbsp;&nbsp;&nbsp;&nbsp;Conditions described above.</label></div>
						</div>
					</div>
					<div class="keeneEmailBox">
						<div class="accKeeneHead">Email Alerts</div>
						<div class="accKeeneMobileNum">You have registered to Keene On Options email alerts.</div>
						<div class="accKeeneTerms">Keene On Options Email Alerts - Terms and Conditions: By entering your email address, and checking the box, you are opting to receive receive daily updates, from Keene on Options and special offers from other Minyanville products and services.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelEmailCheck" onclick="javascript:checkEmailValidity();"></div>
							<div class="label1"><label for="funnelEmailCheck" class="accKeeneCheckTxt">I\'d like to stop receiving Keene On Options email alerts.</label></div>
						</div>
					</div></div>';
				}elseif ($getAlertPreference=='both'){
					$htmKeenePref .= '<div class="keeneSmsBox">
						<div class="accKeeneHead">Text Message Alerts</div>
						<div class="accKeeneMobileNum">You have registered to Keene On Options text message alerts.</div>
						<div class="accKeeneTerms">Keene On Options Text Alerts - Terms and Conditions: Message and data rates may apply. You are opting to receive text messages when a new Keene On Options article is published, which is equal to approximately 20 messages or more per month.<b> Text STOP to quit receiving messages. Text HELP for assistance.</b> Service availability is on a carrier by carrier basis and based on handset compatibility. You must be 18 years or older to subscribe. Your information will not be shared.</div>
					</div>
					<div class="keeneEmailBox">
						<div class="accKeeneHead">Email Alerts</div>
						<div class="accKeeneMobileNum">You have registered to Keene On Options email alerts.</div>
						<div class="accKeeneTerms">Keene On Options Email Alerts - Terms and Conditions: By entering your email address, and checking the box, you are opting to receive receive daily updates, from Keene on Options and special offers from other Minyanville products and services.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelEmailCheck" onclick="javascript:checkEmailValidity();"></div>
							<div class="label1"><label for="funnelEmailCheck" class="accKeeneCheckTxt">I\'d like to stop receiving Keene On Options email alerts.</label></div>
						</div>
					</div></div>';
				}
			}
			if($key % 2) {
			?>
			<div class="subDetails">
			<div class="subName"><?=$activeProductName;?></div>
			<div class="subStatus"><img src="<?=$IMG_SERVER ?>/images/registration/sp_myac_bttn_rightsign.jpg">You are Subscribed</div>
			<?php echo $htmKeenePref;?>
			</div>
		       <? } else { ?>
			<div class="subDetails">
				<div class="subName"><?=$activeProductName;?></div>
				<div class="subStatus"><img src="<?=$IMG_SERVER ?>/images/registration/sp_myac_bttn_rightsign.jpg">You are Subscribed</div>
				<?php echo $htmKeenePref;?>
			</div>
		<?
		       }
		}
		echo '<div class="divider">&nbsp;</div>';
		}


	}

	function displayAvailableSybscriptions(){
		global $IMG_SERVER,$viaProductsName,$productStep2Url;
		$objDatafunnel= new registrationFunnelData();
		$activeProductArray=array();

		$getActiveProduct=$objDatafunnel->getCurrentProducts($_SESSION['SID']);
		if(is_array($getActiveProduct)){
			foreach($getActiveProduct as $key=>$prodRow){
				$activeProductArray[$prodRow['subGroup']]=$prodRow['product'];
			}
		}

		unset($viaProductsName['garyk']);
		unset($viaProductsName['grailetf']);
		unset($viaProductsName['flexfolio']);
		unset($viaProductsName['thestockplaybookpremium']);
		unset($viaProductsName['buyhedge']);
		//unset($viaProductsName['housingmarket']);
		unset($viaProductsName['bmtp']);

		$getAvailableProduct = array_diff_key($viaProductsName,$activeProductArray);
		$i = 0;
		foreach($getAvailableProduct as $key=>$row){
			$htmKeenePref='';
			if($key=='keene'){
				$htmKeenePref = '<div class="keeneMainPref">
					<div class="keeneSmsBox">
						<div class="accKeeneHead">Text Message Alerts</div>
						<div class="accKeeneSign">Sign up for Keene On Options text message alerts by entering a valid phone number below: </div>
						<div class="accKeeneExample"><input type="text" id="funnelMobileNum" value="Example: XXX-XXX-XXXX" onfocus="if(this.value==\'Example: XXX-XXX-XXXX\')this.value=\'\'" onBlur="if(this.value==\'\')this.value=\'Example: XXX-XXX-XXXX\'"></div>
						<div class="accKeeneAlertType">Minyanville will send you text message alerts:</div>
						<ul class="accKeeneTick"><li><span class="accKeeneTickTxt">When a new Keene On Options article is published.<span></li></ul>
						<div class="accKeeneTerms">Keene On Options Text Alerts - Terms and Conditions: Message and data rates may apply. You are opting to receive text messages when a new Keene On Options article is published, which is equal to approximately 20 messages or more per month.<span> Text STOP to quit receiving messages. Text HELP for assistance.</span> Service availability is on a carrier by carrier basis and based on handset compatibility. You must be 18 years or older to subscribe. Your information will not be shared.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelSmsCheck" onclick="javascript:checkFoneValidity();"></div>
							<div><label for="funnelSmsCheck" class="accKeeneCheckTxt">I\'d like to sign up for Keene On Options text message alerts and I agree with the Terms and &nbsp;&nbsp;&nbsp;&nbsp;Conditions described above.</label></div>
						</div>
					</div>
					<div class="keeneEmailBox">
						<div class="accKeeneHead">Email Alerts</div>
						<div class="accKeeneSign">Sign up for Keene On Options email alerts by entering a valid email address below: </div>
						<div class="accKeeneExample"><input type="text" id="funnelEmailForAlert" value="email@exmaple.com" onfocus="if(this.value==\'email@exmaple.com\')this.value=\'\'" onBlur="if(this.value==\'\')this.value=\'email@exmaple.com\'" /></div>
						<div class="accKeeneAlertType">Minyanville will send you email alerts:</div>
						<ul class="accKeeneTick"><li><span class="accKeeneTickTxt">When a new Keene On Options article is published.<span></li></ul>
						<div class="accKeeneTerms">Keene On Options Email Alerts - Terms and Conditions: By entering your email address, and checking the box, you are opting to receive receive daily updates, from Keene on Options and special offers from other Minyanville products and services.</div>
						<div class="accKeeneCheck"><div class="accKeeneLeft"><input type="checkbox" id="funnelEmailCheck" onclick="javascript:checkEmailValidity();"></div>
							<div class="label1"><label for="funnelEmailCheck" class="accKeeneCheckTxt">I\'d like to sign up for Keene On Options text message alerts and I agree with the Terms and &nbsp;&nbsp;&nbsp;&nbsp;Conditions described above.</label></div>
						</div>
					</div></div>';
			}
		$i=$i + 1;
		if($i % 2) {
?>
		<div class="subDetails">
			<div class="subName"><?=$row;?></div>
			<div class="subStatus">
			<? if($row=="Ads Free" || $row=="Housing Market Report") { ?>
				<a href="<?=$productStep2Url[$key];?>&email=<?=$_SESSION['EMAIL']?>&first_name=<?=$_SESSION['nameFirst']?>"><img src="<?=$IMG_SERVER ?>/images/registration/buyNow_blue_btn.png"></a>

			<? } else { ?>
				<a href="<?=$productStep2Url[$key];?>&email=<?=$_SESSION['EMAIL']?>&first_name=<?=$_SESSION['nameFirst']?>"><img src="<?=$IMG_SERVER ?>/images/registration/sp_myac_bttn_getfreetrial.jpg"></a>

			<? } ?>

			</div>
			<?php echo $htmKeenePref;?>
		</div>
		<? }else{ ?>
		<div class="subDetails">
			<div class="subName"><?=$row;?></div>
			<div class="subStatus">
			<? if($row=="Ads Free" || $row=="Housing Market Report") { ?>
				<a href="<?=$productStep2Url[$key];?>&email=<?=$_SESSION['EMAIL']?>&first_name=<?=$_SESSION['nameFirst']?>"><img src="<?=$IMG_SERVER ?>/images/registration/buyNow_blue_btn.png"></a>
			<? } else { ?>
				<a href="<?=$productStep2Url[$key];?>&email=<?=$_SESSION['EMAIL']?>&first_name=<?=$_SESSION['nameFirst']?>"><img src="<?=$IMG_SERVER ?>/images/registration/sp_myac_bttn_getfreetrial.jpg"></a>
			<? } ?>

			</div>
			<?php echo $htmKeenePref;?>
		</div>

<?
		}
		$i++;
	}

	}

	function displayStatesManageAccount($selected_state){
		global $D_R;
		$selected_state=strtolower($selected_state);
		$data=implode("",file("$D_R/assets/data/states.txt"));
		foreach(explode("<OPTION ",$data) as $state){
			if(!$state)continue;
			$abbr = substr($state,(strpos($state,"\"")+1),2);
			$sel=(strtolower($abbr)==strtolower($selected_state))?" selected":"";
			$name=array_pop(explode(">",$state));
			echo "<option ".$sel." value=\"$abbr\">$abbr";

		}
	}

	function displayCountryManageAccount(){
		global $D_R,$STORAGE_SERVER;
		$countryData = file_get_contents("gs://mvassets/assets/data/country.txt");
		htmlprint_r($countryData);
		$data=implode("",$countryData);
		$data=stripslashes($data);
		echo $data;
	}

	function displayCreditCardType($ccType,$selectOptionId){
		global $IMG_SERVER,$masterCardSelected,$visaSelected,$americanExpressSelected,$discoverSelected;
		switch($ccType){
			case "MasterCard":
			      $masterCardSelected='selected="selected"';
			break;
			case "Visa":
			      $visaSelected='selected="selected"';
			break;
			case "American Express":
			      $americanExpressSelected='selected="selected"';
			break;
			case "Discover":
			      $discoverSelected='selected="selected"';
			break;
			default:
				break;
		}
	?>
			<select class="select" id="<?=$selectOptionId;?>" onKeyPress="get_order_keys(event);" name="manageAccountCreditTypeText" onkeydown="removeErrorDiv(this.id);">
				<option value="select">--select--</option>
				<option value="MasterCard" <?=$masterCardSelected;?>>Master Card</option>
				<option value="Visa" <?=$visaSelected;?>>Visa</option>
				<option value="AmericanExpress" <?=$americanExpressSelected;?>>American Express</option>
				<option value="Discover" <?=$discoverSelected;?>>Discover</option>
			</select>
		<!--<input type="text" id="manageAccountCreditType" name="manageAccountCreditTypeText" value="<?=$ccType?>" class="manageAccountCreditTypeText" onkeydown="removeErrorDiv(this.id);">-->
	<?
	}

	function displayCreditCardExpirationDate(){

	}


}
?>