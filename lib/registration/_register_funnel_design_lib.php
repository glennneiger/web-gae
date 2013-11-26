<?php
global $D_R;
include_once($D_R."/lib/registration/_manageAccountDesignLib.php");

class registrationFunnelDesign{
	function registrationFunnel($planCode,$accountCode,$email,$firstName,$lastName,$address1,$address2,$city,$zip,$state,$country,$phone)
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $IMG_SERVER,$HTPFX,$HTHOST,$viaProductsName;
		$objManageAccount = new manageAccountDesignLib();
		if($email!=''){
			$emailValue = 'value="'.$email.'"';
		}else {
			$emailValue = '';
		}
		$objFunnelData = new registrationFunnelData();
		$planDetails = $objFunnelData->getPlanDetails($planCode);
		$currentYear = date("Y");
		$productName = $viaProductsName[$planDetails['plan_group']];
		$hasFreeTrial = "";
		if($planDetails['recurly_plan_free_trial']!="No Trial"){
			$hasFreeTrial = "<span style='font-weight:normal;'><i>(First ".$planDetails['recurly_plan_free_trial']." Free)</i></span>";
		}
		$term = strtolower($planDetails['subType']);
		if($term=="monthly" || strpos($term,'monthly')!==false){
			$term = "Monthly";
			$timePeriod = "per month";
		}elseif($term=="quaterly" || strpos($term,'quaterly')!==false){
			$term = "Quarterly";
			$timePeriod = "per quarter";
		}elseif($term=="annual" || strpos($term,'annual')!==false){
			$term = "Annual";
			$timePeriod = "per year";
		}elseif($term=="complimentary"){
			$term = "Complimentary";
			$timePeriod = "per year";
		}elseif ($term=="singles"){
			$term = "";
			$timePeriod = "";
			$productName = $planDetails['plan_name'];
		}

		$address = $address1.' '.$address2;
		?>
		<div class="funnel_heading"><?=$planDetails['plan_promotional_headline']?></div>
		<input type="hidden" id="subId" value="<?=$accountCode;?>" />
		<input type="hidden" id="funnelPlanCode" value="<?=$planCode;?>" />
		<input type="hidden" id="funnelPlanGroup" value="<?=$planDetails['plan_group'];?>" />
		<div class="funnel_container">
			<div class="funnel_plan_user_detail">
				<div class="funnel_plan_info">Plan Information</div>
				<div class="funnel_plan_info_txt">Plan: <?=$productName.' '.$term.' '.$hasFreeTrial;?> <br /><br />Price: $<?=$planDetails['price'].' '.$timePeriod;?></div>
				<div class="funnel_plan_info">Payment Information</div>
				<div class="errorFunnel" id="errorFunnel">&nbsp;</div>
				<div class="funnel_user_info_frst">
					<div class="funnel_user_info_txt">First Name</div>
					<input type="text" class="funnel_user_info_input" id="funnelFirstName" value="<?=$firstName?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Last Name</div>
					<input type="text" class="funnel_user_info_input" id="funnelLastName" value="<?=$lastName?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Email Address</div>
					<input type="text" class="funnel_user_info_input" id="funnelEmail" value="<?=$email?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Billing Street Address</div>
					<input type="text" class="funnel_input_street_address" id="funnelAddress" value="<?=$address?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Country</div>
					<select class="select_country" id="funnelCountry" onKeyPress="get_order_keys(event);" name="funnelCountry" class="manageAccountCountryText" ><option value="select">--Select--</option><?php echo $objManageAccount->displayCountryManageAccount(); ?></select>
				<? if(!empty($country)){ ?>
					<script language="javascript">getSelected('<?=$country?>','funnelCountry');</script>
				<? } ?>
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">City</div>
					<input type="text" class="funnel_input_city_state_zip" id="funnelCity" value="<?=$city?>" />
					<div class="funnel_user_info_txt">&nbsp;&nbsp;State</div>
					<input type="text" class="funnel_input_city_state_zip" id="funnelState" value="<?=$state?>" />
					<div class="funnel_user_info_txt">&nbsp;&nbsp;Zip</div>
					<input type="text" class="funnel_input_city_state_zip" id="funnelZip" value="<?=$zip?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Phone Number</div>
					<input type="text" class="funnel_user_info_input" id="funnelPhone" value="<?=$phone?>" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Payment Method</div>
					<?=$objManageAccount->displayCreditCardType($ccType,'funnelCCtype');?>
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Card Number</div>
					<input type="text" class="funnel_user_info_input" id="funnelCCnum" />
				</div>
				<div class="funnel_user_info">
					<div class="funnel_user_info_txt">Expiration Date</div>
					<select class="select_month" id="funnelExpMnth">
						<? for ($i=1; $i<13; $i++){
							if($i==$ccMonth){ ?>
								<option value="<?=$i;?>" selected="selected"><?=$i;?></option>
							<? 	}else{ ?>
								<option value="<?=$i;?>"><?=$i;?></option>
							<? 	} ?>
						<? } ?>
					</select>
					<select class="select_yr" id="funnelExpYr">
					<? for ($j=0; $j<20; $j++){
							$year = $currentYear+$j;
							if($year==$ccYear){ ?>
								<option value="<?=$year;?>" selected="selected"><?=$year;?></option>
							<? 	}else{ ?>
								<option value="<?=$year;?>"><?=$year;?></option>
							<? 	} ?>
					<? } ?>
					</select>
					<div class="funnel_user_info_cvv">CVV</div>
					<input type="text" class="funnel_cvv_code" id="funnelcvv" />
				</div>
				<div class="funnel_user_info">
					<div class="errorApplyCoupon" id="couponErrorFunnel">&nbsp;</div>
					<div class="funnelTotalV1">Total: $<?=$planDetails['price']?>.00</div> 
					<? if($hasFreeTrial==""){
						$todaysChargeAmt = $planDetails['price'];
					}else{
						$todaysChargeAmt = "0";
					} ?>
					<div class="funnel_user_info_txt">Promo Code:<input type="text" class="funnel_promo_code" id="funnel_promo_code" /></div>
					<input type="button" value="APPLY" class="funnel_apply_img" id="funnel_apply_img" onClick="javascript:applyCoupon('<?=$todaysChargeAmt;?>');" /><img src="<?=$IMG_SERVER?>/images/registration/applyLoading.gif" id="applyLoading" class="applyLoading" />
				</div>
				<div class="funnelAmountCharge">Amount to be Charged Today: $<span id="payableAmt"><?=$todaysChargeAmt?>.00</span></div>
			</div>
		<div class="funnel_left_panel">
				<div class="funnel_top"></div>
				<div class="funnel_mid">
					<div class="funnel_trial_text"><?=strtoupper($planDetails['plan_feature_headline'])?>:</div>
					<div class="funnel_product_features"><?=$planDetails['plan_promotional_features']?></div>
					<div class="funnel_product_desc"><?=$planDetails['plan_promotional_desc']?></div>
				</div>
				<div class="funnel_bottom"></div>
				<div class="termsBox">
					<div class="funnelTerms">Terms Of Use</div>
					<div class="funnelAgreementDate">THIS AGREEMENT WAS LAST UPDATED ON September 9, 2009.</div>
					<div class="funnelAgreementRead">PLEASE SCROLL DOWN AND READ THIS AGREEMENT IN ITS ENTIRETY BEFORE YOU USE ANY OF OUR PRODUCTS OR SERVICES OR BECOME A MEMBER OF MINYANVILLE.</div>
					<div class="funnelAgreementBox"><div class="funnelTermsText">
						<div class="funnelAgreementTxt">This Terms of Use Agreement (the "Agreement") and our Privacy Policy, which is hereby incorporated by reference, governs your use of the Minyanville.com site, products and services ("Minyanville" or the "Service"); Minyanville provides financial infotainment and education from Minyanville Media, INC ("we" or "us"). By accessing or using the products, services, website and software provided through or in connection with Minyanville, you signify that you have read, understood, and agree to be bound by this Agreement and our current Privacy Policy, whether or not you are a registered member. If you do not agree to any of these terms or any future Terms of Use, you may not use or access (or continue to access) the Service.</div>
						<div class="funnelAgreementTxt"><strong>1. Changes to the Agreement.</strong> We may change the terms of this Agreement at any time and without prior notice. If we do this, we will post the changes to this page and indicate at the top of the page the date the Agreement was last revised. You can access this document at any time by selecting the Terms of Use link located at the bottom of every page on the Minyanville web site. Your use of Minyanville after changes are made to this Agreement means that you agree </div></div>
					</div>
					<div class="funnelSubmitCheck"><input type="checkbox" id="funnelAgreeCheck"><span class="funnelAgree">I have read and agree to the terms & conditions outlined above.</span> </div>
				</div>
			</div>
			<div class="errorAgreeFunnelV2" id="errorAgreeFunnel">&nbsp;</div>
			<div class="funnelSubmitBox">
				<div class="funnelSubmitBttnV2" id="funnelSubmitBttn" onClick="javascript:saveLoginDetailFromFunnel('agree');"><span class="funnelSubmitBttnTxt">SUBMIT ORDER</span></div>
				<div class="funnel_hide_btn" id="funnelWaitBttn"><img src ="<?=$IMG_SERVER?>/images/registration/pls-wait.jpg" onClick="javascript:saveLoginDetailFromFunnel();" /></div>
			</div>
		</div>
	<? }

	function crossSell($planCode)
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $IMG_SERVER,$productDesc,$HTPFX,$HTHOST,$viaProductsName;
		$objFunnelData = new registrationFunnelData();
		$planDetail = $objFunnelData->getPlanDetails($planCode);

		$planName = $viaProductsName[$planDetail['plan_group']];
		$productGroup = $this->getPromotionalProductDetails($planCode);
		$promotionalName = $productDesc[$productGroup]['planName'];
		$promoProductDetail = $productDesc[$productGroup]['description'];
		$promoProductPlanCode = $productDesc[$productGroup]['planCode'];
		$promoProductPlanImg = $productDesc[$productGroup]['trialImage'];
		?>
		<div class="cross_sell_container">
		<div id="reg_top"></div>
		<div class="cross_sell_middle">
			<div class="cross_sell_heading">SUCCESS! YOU ARE NOW SUBSCRIBED TO <?=strtoupper($planName);?> </div>
			<div class="cross_sell_text">
				<span class="cross_sell_texthead">Don't miss out on these special offers from Minyanville's premier partners  </span>
				<br> <br>
				<span class="cross_sell_textbottom">Minyanville will provide the partner(s) you select with your email address, and any additional information you choose to provide.</span>
			</div>
			<?
			$redirect_url= $HTPFX.$HTHOST."/subscription/register/welcome.htm";
			$this->getRandomScriptCode($planDetail['plan_group'],$redirect_url);
			?>
			<div class="cross_sell_bottom_text">
				<span>Select as many offers as you'd like, then click Continue to access your subscription. <br>You will receive a separate email for the offers you choose.</span>
			</div>

		</div>
		<div id="reg_bottom"></div>
	</div>
	<? 	}

	function getRandomScriptCode($plan_group,$redirect_url)
	{
		$ads = array("IC", "IMS");
		$rand_ads = array_rand($ads, 1);
		if($ads[$rand_ads]=="IC")
		{
			$this->getICScriptCode($plan_group,$redirect_url);
		}
		else
		{
			echo '<div class="cross_sell_offers_ims"><script type="text/javascript" src="http://ldsapi.tmginteractive.com/generateplacementscript.aspx?placement=19964200&publisher=200952&affid=&subid=&email=&redirect="></script>
			
			<script type="text/javascript" src="http://ldsapi.tmginteractive.com/generateplacementscript.aspx?placement=19964200&publisher=200952&affid='.$plan_group.'&subid='.$_SESSION[SID].'&email='.$_SESSION[email].'&redirect='.$redirect_url.'"></script></div>';
		}
	}

	function getICScriptCode($plan_group,$redirect_url)
	{
			echo '<div class="cross_sell_offers_ic">';
			if($plan_group == "buzz")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=165&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="cooper")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=168&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="adsfree")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=170&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="housingmarket")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=165&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="techstrat")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=166&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="optionsmith")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=167&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="thestockplaybook")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=169&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="peterTchir")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=186&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="ElliottWave")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=241&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			else if($plan_group=="keene")
			{
					echo "<script type='text/javascript' src='http://coreg.investingchannel.com/AdServe.aspx?pid=239&pubid=5&first_name=".$_SESSION[nameFirst]."&last_name=".$_SESSION[nameLast]."&address=".urlencode($_SESSION['address'])."&city=".$_SESSION[city]."&state=".$_SESSION[state]."&zip=".$_SESSION[zip]."&email=".$_SESSION[email]."&phone_number=".$_SESSION[phone]."&redirect_url=".$redirect_url."&pixel_url={VALUE}'></script>";
			}
			echo '</div>';
	}
	function registrationPage($product,$id){
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		 global $HTPFX,$HTHOST,$viaProductsName;
		 $viaProductsName['peterTchir']="Tchir's Fixed Income Report";
		 if(!empty($product))
		 {
			$objFunnelData = new registrationFunnelData();
			$productData = $objFunnelData->getProductData($product);
		 }
	?>
		<form name ="productfrm" id="productfrm"  method="POST" >
		       <table width="200px" border="0" cellpadding="8" cellspacing="0">
		       <tr>
			       <td colspan="2"><div style="color:#FF0000;" id="showmsg">
			       <?php
				if($id>0)
				{
					echo "Updated successfully";
				}
				?>
				</div></td>
		       </tr>
			 <tr>
					<td nowrap="nowrap">Product Name:</td>

					<td><select name="productName" id="productName" style="width:300px;" onChange="returnproductname()">
					<option  value="0">-- Select Product --</option>
				 <?
					foreach($viaProductsName as $key=>$val)
					{
					    if($_GET['product']==$key)
					    {
						 echo '<option id='.$key.' value='.$key.' selected >'.$val.'</option>';
					    }
					    else
					    {
						 echo '<option id='.$key.' value='.$key.'  >'.$val.'</option>';
					    }
					}
				 ?>
					</select></td>
			</tr>

			<tr>
			       <td nowrap="nowrap">Page Heading:</td>
			       <td><?input_text("product_heading",$productData['plan_promotional_headline'],"15","2000","style=width:500px;")?></td>
			</tr>
			<tr>
			       <td nowrap="nowrap">Feature Heading:</td>
			       <td><b>Do not include ':' at the end.</b><br>
			       <?input_text("product_feature_heading",$productData['plan_feature_headline'],"15","2000","style=width:500px;")?></td>
			</tr>
			<tr>
			       <td nowrap="nowrap" style="vertical-align: top;">Feature:</td>
			       <td>
			       <b>Use bullets format for features, Otherwise the layout will be distorted on the page.</b><br>
				<?php
				input_textarea("product_features",strip($productData['plan_promotional_features']),"600");				?>
				<script language="javascript">showEditor('product_features',600,300);</script>
			       </td>
			 </tr>
			 <tr>
			       <td nowrap="nowrap" style="vertical-align: top;">Description:</td>
			       <td>
			       <b>For Heading please enclose the content in h1 tags. </b><br>
			        <?php
				input_textarea("product_description",strip($productData['plan_promotional_desc']),"600");
				?>
				<script language="javascript">showEditor('product_description',600,300);</script>
			       </td>
			 </tr>
			  <tr>
			       <td></td>
			       <td><input type="button" name="save" value="save" onclick="insertProductData();" /></td>
			 </tr>
		       </table>
		       <input type="hidden" name="save" id="save" value="0">
		</form>

	<?
	}

	function welcomeFunnel()
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $IMG_SERVER,$viaProductsName;
	?>
		<div class="welcome_container">
			<div id="reg_top"></div>
			<div class="welcome_middle">
				<div class="welcome_thanks">Thank you for subscribing!</div>
				<div class="welcome_text">
					<div class="welcome_access">You can now access your free trials to:</div>
					<? foreach($_SESSION['recently_added']['SUBSCRIPTION'] as $prodSession){

    						switch($prodSession['planGroup']){
								case 'buzz':
						            $link = $HTPFX.$HTHOST."/buzz/buzz.php";
						            $str=' '."<a style='cursor:pointer;' onClick='window.open(\"".$link."\",\"Banter\",\"width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\");banterWindow.focus();'>Launch it now</a>";
								break;
						        case 'techstrat':
						            $str=" <a href='".$HTPFX.$HTHOST."/techstrat/index.htm'>Read Sean Udall's latest report</a>";
									$str.="<br/><a href='".$HTPFX.$HTHOST."/whitepapers/seanudall/apple/techstrat.pdf'>Download my free report now!</a>";
						        break;
						        case 'grailetf':
						            $str=" <a href='".$HTPFX.$HTHOST."/etf/index.htm'>Read Ron & Denny's most recent alert</a>";
						        break;
						        case 'cooper':
						            $str=' <a href="'.$HTPFX.$HTHOST.'/cooper/index.htm">View the latest reports</a>';
						        break;
						        case 'optionsmith':
						            $str=" <a href='".$HTPFX.$HTHOST."/optionsmith/index.htm'>See Steve's most recent trades</a>";
						        break;
						        case 'activeinvestor':
						            $str=" <a href='".$HTPFX.$HTHOST."/active-investor'>See Active Investor's most recent trades</a>";
						        break;
						        case 'thestockplaybook':
						            $str=" <a href='".$HTPFX.$HTHOST."/the-stock-playbook/'>View latest video now</a>";
						        break;
								case 'buyhedge':
						            $str=" <a href='".$HTPFX.$HTHOST."/buyhedge/index.htm'>Buy and Hedge ETF Strategies</a>";
						        break;
								case 'thestockplaybookpremium':
						            $str=" <a href='".$HTPFX.$HTHOST."/the-stock-playbook/'>View latest video now</a>";
						        break;
								case 'housingmarket':
						            $str=" <a href='".$HTPFX.$HTHOST."/housing-market-report/'>Read Keith Jurow's Housing Market Report</a>";
						        break;
								case 'housingmarketsingle':
									$str=" It will be sent momentarily to the email address you provided.";
						        break;
						        case 'peterTchir':
									 $str=" <a href='".$HTPFX.$HTHOST."/tchir-fixed-income/'>Read Peter Tchir's Fixed Income Report</a>";
						        break;
						        case 'ElliottWave':
									 $str=" <a href='".$HTPFX.$HTHOST."/ewi/'>Read Elliott Wave Insider's most recent alerts</a>";
						        break;
						        case 'keene':
									 $str=" <a href='".$HTPFX.$HTHOST."/keene/'>Read Andrew Keene's most recent alerts</a>";
						        break;
    						}
						    $str ="<span>".$viaProductsName[$prodSession['planGroup']]."</span>".$str; ?>
						    <div class="welcome_product_name"><?=$str;?></div>
				<? 	} ?>

				</div>
				<div class="welcome_footer">
					<div class="welcome_info">Check your email for updates from Minyanville!</div>
					<div class="welcome_support"><b>Questions about your subscriptions?</b><br>Contact our team at<br><a href="mailto:support@minyanville.com">support@minyanville.com</a> or 212-991-9357</div>
				</div>
			</div>
			<div id="reg_bottom"></div>
		</div>
	<? }

	function welcomeHouseAdPixels(){
		 foreach($_SESSION['recently_added']['SUBSCRIPTION'] as $prodSession){
					//htmlprint_r($prodSession['planGroup']);
    						switch($prodSession['planGroup']){
							case 'buzz':
							?>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=513713&considered_events=imp,ad_play,click&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511393&considered_events=imp,ad_play,click&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511389&considered_events=click,ad_play,imp&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511141&considered_events=click,imp,ad_play&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511397&considered_events=imp,click,ad_play&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&ads=511147&considered_events=imp,ad_play,click&url=" /></div>
							<?

								break;
							case 'cooper':
							?>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511095&considered_events=imp,ad_play,click&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511103&considered_events=imp,click,ad_play&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511083&considered_events=imp,click,ad_play&url=" /></div>

							<?
								break;
							case 'optionsmith':
							?>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511091&considered_events=ad_play,imp,click&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511111&considered_events=click,ad_play,imp&url=" /></div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511075&considered_events=ad_play,click,imp&url=" />
</div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&ads=511149&considered_events=click,imp,ad_play&url=" />
</div>

							<?
								break;
							case 'techstrat':
							?>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=513709&considered_events=click,imp,ad_play&url=" />
</div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511401&considered_events=imp,click,ad_play&url=" />
</div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511407&considered_events=click,ad_play,imp&url=" />
</div>
							<div><IMG width="1" height="1" src="http://minyanville.checkm8.com/adam/pv?hits_event=action&campaigns=511069&considered_events=click,imp,ad_play&url=" />
</div>

							<?
								break;
						}
		 }

	}

	function getPromotionalProductDetails($planCode){

		global $productDesc,$HTPFX,$HTHOST;
		$promotionalProduct = array_rand($productDesc);
		$promotionalProductCode = $productDesc[$promotionalProduct]['planCode'];
		$objFunnelData = new registrationFunnelData();
		$productList = $objFunnelData->getCurrentProducts($_SESSION['SID']);
		$activeProducts = array();
		foreach ($productList as $key=>$row){
			$activeProducts[$row['subGroup']] = $row['subGroup'];
		}
		if($planCode==$promotionalProductCode){
			$promotionalProduct = $this->getPromotionalProductDetails($planCode);
			if($promotionalProduct!='')
			return $promotionalProduct;
		}elseif(array_key_exists($promotionalProduct,$activeProducts)){
			if(count($activeProducts)>="5"){
				$productDescKey = array();
				foreach($productDesc as $key=>$row){
					$productDescKey[$key]=$key;
				}
				/*$chkAllFive = array_diff_key($productDescKey, $activeProducts);
				if(empty($chkAllFive)){
					$destination = $HTPFX.$HTHOST."/subscription/register/welcome.htm";
					location($destination,$exit=0);
				}*/
			}
			$promotionalProduct=$this->getPromotionalProductDetails($planCode);
			if($promotionalProduct!='')
			return $promotionalProduct;
		}else{
			return $promotionalProduct;
		}
	}

	function errorPage(){
		global $IMG_SERVER,$HTPFX,$HTHOST; ?>
		<div class="cross_sell_container">
		<div id="reg_top"></div>
		<div class="error_middle">Sorry we are not able to process your selected subscription. Please <a href="<?=$HTPFX.$HTHOST?>/subscription">click here</a> to select it again.</div>
		<div id="reg_bottom"></div>
	</div>
<? 	}

	function welcomeFunnelDisplayMsg($msg){

	?>
		<div class="welcome_container">
			<div id="reg_top"></div>
			<div class="welcome_middle">
				<div class="welcome_text_subscribed">
					<div class="welcome_access"><?=$msg?></div>
				</div>
				<div class="welcome_footer">
					<div class="welcome_info">Check your email for updates from Minyanville!</div>
					<div class="welcome_support"><b>Questions about your subscriptions?</b><br>Contant our team at<br><a href="mailto:support@minyanville.com">support@minyanville.com</a> or 212-991-9357</div>
				</div>
			</div>
			<div id="reg_bottom"></div>
		</div>
	<?
	}

	function welcomeFunnelSoftTrial()
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $IMG_SERVER,$viaProductsName;
	?>
		<div class="welcome_container">
			<div id="reg_top"></div>
			<div class="welcome_middle">
				<div class="welcome_thanks">Thank you for subscribing!</div>
				<div class="welcome_text">
					<div class="welcome_access">You can now access your free trial to:</div>
					<? foreach($_SESSION['recently_added']['SUBSCRIPTION'] as $prodSession){

    						switch($prodSession['planGroup']){
								case 'buzz':
						            $link = $HTPFX.$HTHOST."/buzz/buzz.php";
						            $str=' '."<a style='cursor:pointer;' onClick='window.open(\"".$link."\",\"Banter\",\"width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\");banterWindow.focus();'>Launch it now</a>";
								break;
						        case 'techstrat':
						            $str=" <a href='".$HTPFX.$HTHOST."/techstrat/index.htm'>Read Sean Udall's latest report</a>";
									$str.="<br/><a href='".$HTPFX.$HTHOST."/whitepapers/seanudall/apple/techstrat.pdf'>Download my free report now!</a>";
						        break;
						        case 'grailetf':
						            $str=" <a href='".$HTPFX.$HTHOST."/etf/index.htm'>Read Ron & Denny's most recent alert</a>";
						        break;
						        case 'cooper':
						            $str=' <a href="'.$HTPFX.$HTHOST.'/cooper/index.htm">View the latest reports</a>';
						        break;
						        case 'optionsmith':
						            $str=" <a href='".$HTPFX.$HTHOST."/optionsmith/index.htm'>See Steve's most recent trades</a>";
						        break;
						        case 'activeinvestor':
						            $str=" <a href='".$HTPFX.$HTHOST."/active-investor'>See Active Investor's most recent trades</a>";
						        break;
						        case 'thestockplaybook':
						            $str=" <a href='".$HTPFX.$HTHOST."/the-stock-playbook/'>View latest video now</a>";
						        break;
								case 'buyhedge':
						            $str=" <a href='".$HTPFX.$HTHOST."/buyhedge/index.htm'>Buy and Hedge ETF Strategies</a>";
						        break;
								case 'thestockplaybookpremium':
						            $str=" <a href='".$HTPFX.$HTHOST."/the-stock-playbook/'>View latest video now</a>";
						        break;
								case 'housingmarket':
						            $str=" <a href='".$HTPFX.$HTHOST."/housing-market-report/'>Read Keith Jurow's Housing Market Report</a>";
						        break;
								case 'housingmarketsingle':
									$str=" It will be sent momentarily to the email address you provided.";
						        break;
						        case 'peterTchir':
									$str=" <a href='".$HTPFX.$HTHOST."/tchir-fixed-income/'>Read Peter Tchir's Fixed Income Report</a>";
						        break;
    						}
						    $str ="<span>".$viaProductsName[$prodSession['planGroup']]."</span>".$str; ?>
						    <div class="welcome_product_name"><?=$str;?></div>
				<? 	} ?>

				</div>
				<div class="welcome_footer">
					<div class="welcome_info">Check your email for updates from Minyanville!</div>
					<div class="welcome_support"><b>Questions about your subscriptions?</b><br>Contant our team at<br><a href="mailto:support@minyanville.com">support@minyanville.com</a> or 212-991-9357</div>
				</div>
			</div>
			<div id="reg_bottom"></div>
		</div>
	<? }
	
	function registrationFunnelV2($planCode,$accountCode,$email,$firstName,$lastName,$address1,$address2,$city,$zip,$state,$country,$phone)
	{
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $IMG_SERVER, $HTPFX, $HTHOST, $viaProductsName, $objAmazonSNS;
		$objManageAccount = new manageAccountDesignLib();
		if($email!=''){
			$emailValue = 'value="'.$email.'"';
		}else {
			$emailValue = '';
		}
		$objFunnelData = new registrationFunnelData();
		$planDetails = $objFunnelData->getPlanDetails($planCode);
		$currentYear = date("Y");
		$hasFreeTrial = "";
		if($planDetails['recurly_plan_free_trial']!="No Trial"){
			$hasFreeTrial = "<span class='funnelItalicTextV2'>(First ".$planDetails['recurly_plan_free_trial']." Free)</span>";
		}
		$term = strtolower($planDetails['subType']);
		if($term=="monthly" || strpos($term,'monthly')!==false){
			$term = "Monthly";
			$timePeriod = "month";
		}elseif($term=="quaterly" || strpos($term,'quaterly')!==false){
			$term = "Quarterly";
			$timePeriod = "quarter";
		}elseif($term=="annual" || strpos($term,'annual')!==false){
			$term = "Annual";
			$timePeriod = "year";
		}elseif($term=="complimentary"){
			$term = "Complimentary";
			$timePeriod = "year";
		}

		$address = $address1.' '.$address2;
		?>
		<div class="funnel_heading"><?=$planDetails['plan_promotional_headline']?></div>
		<input type="hidden" id="subId" value="<?=$accountCode;?>" />
		<input type="hidden" id="funnelPlanCode" value="<?=$planCode;?>" />
		<input type="hidden" id="funnelPlanGroup" value="<?=$planDetails['plan_group'];?>" />
		<div class="funnel_container">
			<div class="funnelHeadingV2">PRODUCT INFORMATION</div>
			<div>
				<div class="funnelPlanDescV2">
					<p class="funnelPlanDetailV2 funnelBoldV2">Plan:</p> 
					<p class="funnelPlanDetailV2 funnelNewLineV2 funnelBoldV2"><?=$viaProductsName[$planDetails['plan_group']].' '.$term.' '.$hasFreeTrial;?></p>
					<p class="funnelPlanDetailV2 funnelNewLineV2 funnelBoldV2">Price:</p> 
					<p class="funnelPlanDetailV2 funnelNewLineV2 funnelBoldV2">$<?=$planDetails['price']?> per <?=$timePeriod;?></p>
				</div>
				<div class="funnelPlanDescRightV2">
					<p class="funnelBoldV2 funnelPlanDetailV2"><?=strtoupper($planDetails['plan_feature_headline'])?>:</p>
					<div class="funnelProductFeaturesV2"><?=$planDetails['plan_promotional_features']?></div>
				</div>
			</div>
			
			<div class="funnelHeadingV2">SIGN UP FOR KEENE ON OPTIONS TEXT MESSAGE ALERTS</div>
			<div class="errorFunnelV2" id="errorNumFunnel">&nbsp;</div>
			<div class="funnelOpt">
				<p class="funnelAlertOptV2">Minyanville will send you text message alerts:</p>
				<ul class="funnelAlertOptTextV2">
					<li><span>When a new Keene On Options article is published.</span></li>
				</ul>
				
				<div class="funnelNumDetailV2">
					<p class="funnelBoldV2 funnelPlanDetailV2">Mobile Number<input type="text" class="funnelMobileNum" id="funnelMobileNum"/></p>
					<div class="funnelMobileNumExample">(Example: XXX-XXX-XXXX)</div>
				</div>
				<p id="funnelOptTerms"><span class="funnelOptTermsText funnelBoldV2">Keene On Options Text Alerts - Terms and Conditions:</span> <span class="funnelOptTermsText">Message and data rates may apply. You are opting to receive text messages when a new Keene On Options article is published, which is equal to approximately 20 messages or more per month.</span> <span class="funnelOptAsstTxt funnelBoldV2">Text STOP to quit receiving messages. Text HELP for assistance.</span><span class="funnelOptTermsText "> Service availability is on a carrier by carrier basis and based on handset compatibility. You must be 18 years or older to subscribe. Your information will not be shared.not be shared.</span></p>
				<div class="funnelOptCheck">
					<div class="funnelLeftV2"><input type="checkbox" id="funnelSmsCheck" onclick="javascript:checkFoneValidity();"></div>
					<div class="funnelCheckBoxV2">
						<label for="funnelSmsCheck" class="funnelAlertOptV2">I'd like to sign up for Keene On Options text message alerts and I agree with the Terms and Conditions described above.</label>
					</div>
				</div>
			</div>
			
			<div class="funnelHeadingV2">SIGN UP FOR KEENE ON OPTIONS EMAIL ALERTS</div>
			<div class="funnelOpt">
				<p class="funnelAlertOptV2">Minyanville will send you email alerts:</p>
				<ul class="funnelAlertOptTextV2">
					<li><span>When a new Keene On Options article is published.</span></li>
				</ul>
				<div class="funnelNumDetailV2">
					<div class="errorFunnelV2" id="errorEmailFunnel">&nbsp;</div>
					<div class="funnelEmailExample">Please provide a valid email address below:</div>
					<p class="funnelBoldV2 funnelEmailForAlertV2">Email Address<input type="text" id="funnelEmailForAlert" class="funnelMobileNum" value="email@exmaple.com" onfocus="if(this.value=='email@exmaple.com')this.value=''" onBlur="if(this.value=='')this.value='email@exmaple.com'" /></p>
				</div>
				<p id="funnelOptTerms"><span class="funnelOptTermsText funnelBoldV2">Keene On Options Email Alerts - Terms and Conditions:</span> <span class="funnelOptTermsText">By entering your email addess, and checking the box, you are opting to recive daily updates, from Keene On Options amd pecial offers from other Minyanville product and services.</span></p>
				<div class="funnelOptCheck">
					<div class="funnelLeftV2"><input type="checkbox" id="funnelEmailCheck" onclick="javascript:checkEmailValidity();"></div>
					<div class="funnelCheckBoxV2">
						<label for="funnelSmsCheck" class="funnelAlertOptV2">I'd like to sign up for Keene On Options email alerts and I agree with the Terms and Conditions described above.</label>
					</div>
				</div>
			</div>
			
			<div class="funnelHeadingV2">PAYMENT INFORMATION</div>
			<div class="errorFunnelV2" id="errorFunnel">&nbsp;</div>
			<div class="funnelPaymentInfoFormV2">
				<div class="funnelPayInfoLeft">
					<div class="funnelUserInfoFrstV2">
						<div class="funnelUserInfoTxtV2">First Name</div>
						<input type="text" class="funnelUserInfoInputV2" id="funnelFirstName" value="<?=$firstName?>" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Last Name</div>
						<input type="text" class="funnelUserInfoInputV2" id="funnelLastName" value="<?=$lastName?>" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Email Address</div>
						<input type="text" class="funnelUserInfoInputV2" id="funnelEmail" value="<?=$email?>" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Billing Street Address</div>
						<input type="text" class="funnelInputStreetAddressV2" id="funnelAddress" value="<?=$address?>" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Country</div>
						<select class="select_country" id="funnelCountry" onKeyPress="get_order_keys(event);" name="funnelCountry" class="manageAccountCountryText" ><option value="select">--Select--</option><?php echo $objManageAccount->displayCountryManageAccount(); ?></select>
					<? if(!empty($country)){ ?>
						<script language="javascript">getSelected('<?=$country?>','funnelCountry');</script>
					<? } ?>
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">City</div>
						<input type="text" class="funnelInputCityStateZipV2" id="funnelCity" value="<?=$city?>" />
						<div class="funnelUserInfoTxtV2">&nbsp;&nbsp;State</div>
						<input type="text" class="funnelInputCityStateZipV2" id="funnelState" value="<?=$state?>" />
						<div class="funnelUserInfoTxtV2">&nbsp;&nbsp;Zip</div>
						<input type="text" class="funnelInputCityStateZipV2" id="funnelZip" value="<?=$zip?>" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Phone Number</div>
						<input type="text" class="funnelUserInfoInputV2" id="funnelPhone" value="<?=$phone?>" />
					</div>
				</div>
				<div class="funnelPayInfoRight">
					<div class="funnelUserInfoFrstV2">
						<div class="funnelUserInfoTxtV2">Payment Method</div>
						<?=$objManageAccount->displayCreditCardType($ccType,'funnelCCtype');?>
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Card Number</div>
						<input type="text" class="funnelUserInfoInputV2" id="funnelCCnum" />
					</div>
					<div class="funnelUserInfoV2">
						<div class="funnelUserInfoTxtV2">Expiration Date</div>
						<select class="select_month" id="funnelExpMnth">
							<? for ($i=1; $i<13; $i++){
								if($i==$ccMonth){ ?>
									<option value="<?=$i;?>" selected="selected"><?=$i;?></option>
								<? 	}else{ ?>
									<option value="<?=$i;?>"><?=$i;?></option>
								<? 	} ?>
							<? } ?>
						</select>
						<select class="select_yr" id="funnelExpYr">
						<? for ($j=0; $j<20; $j++){
								$year = $currentYear+$j;
								if($year==$ccYear){ ?>
									<option value="<?=$year;?>" selected="selected"><?=$year;?></option>
								<? 	}else{ ?>
									<option value="<?=$year;?>"><?=$year;?></option>
								<? 	} ?>
						<? } ?>
						</select>
						<div class="funnelUserInfoCvvV2">CVV</div>
						<input type="text" class="funnelCvvCodeV2" id="funnelcvv" />
					</div>
					<div class="errorFunnel" id="couponErrorFunnel">&nbsp;</div>
					<div class="funnelPlanPriceTxtV2">
						<div id="funnelTotalAmtV2">Total: $<?=$planDetails['price']?>.00 </div>
						<? if($hasFreeTrial==""){
							$todaysChargeAmt = $planDetails['price'];
						}else{
							$todaysChargeAmt = "0";
						} ?>
						<div class="funnelUserInfoTxtV2">Promo Code:</div>
						<input type="text" class="funnelPromoCodeV2" id="funnel_promo_code" />
						<div class="funnelApplyBttnV2" id="funnel_apply_img" onClick="javascript:applyCoupon('<?=$todaysChargeAmt;?>');">Apply</div>
						<div class="funnelAmtChargedV2">Amount to be Charged Today: $<span id="payableAmt"><?=$todaysChargeAmt?>.00</span></div>
					</div>
				</div>
			</div>
			
			<div class="funnelHeadingV2">ABOUT YOUR SUBSCRIPTION</div>
			<p class="funnelCardChargeInfoV2"><?=$planDetails['plan_promotional_desc']?></p>
			<div class="funnelTermsV2">Terms Of Use</div>
			<div class="funnelAgreementDateV2">THIS AGREEMENT WAS LAST UPDATED ON September 9, 2009.</div>
			<div class="funnelAgreementReadV2">PLEASE SCROLL DOWN AND READ THIS AGREEMENT IN ITS ENTIRETY BEFORE YOU USE ANY OF OUR PRODUCTS OR SERVICES OR BECOME A MEMBER OF MINYANVILLE.</div>
			<div class="funnelAgreementBoxV2">
				<div class="funnelAgreementTxtV2">This Terms of Use Agreement (the "Agreement") and our Privacy Policy, which is hereby incorporated by reference, governs your use of the Minyanville.com site, products and services ("Minyanville" or the "Service"); Minyanville provides financial infotainment and education from Minyanville Media, INC ("we" or "us"). By accessing or using the products, services, website and software provided through or in connection with Minyanville, you signify that you have read, understood, and agree to be bound by this Agreement and our current Privacy Policy, whether or not you are a registered member. If you do not agree to any of these terms or any future Terms of Use, you may not use or access (or continue to access) the Service.</div>
				<div class="funnelAgreementTxtV2"><strong>1. Changes to the Agreement.</strong> We may change the terms of this Agreement at any time and without prior notice. If we do this, we will post the changes to this page and indicate at the top of the page the date the Agreement was last revised. You can access this document at any time by selecting the Terms of Use link located at the bottom of every page on the Minyanville web site. Your use of Minyanville after changes are made to this Agreement means that you agree </div>
			</div>
			
			<div class="errorAgreeFunnelV2" id="errorAgreeFunnel">&nbsp;</div>
			<div class="funnelSubmitBoxV2">
				<div class="funnelSubmitCheckV2"><input type="checkbox" id="funnelAgreeCheck"></div>
				<div class="funnelAgreeV2">I have read and agree to the terms & conditions outlined above.</div> 
				<div class="funnelSubmitBttnV2" id="funnelSubmitBttn" onClick="javascript:saveLoginDetailFromFunnel('sms-agree');"><span class="funnelSubmitBttnTxt">SUBMIT ORDER</span></div>
				<div class="funnel_hide_btn" id="funnelWaitBttn"><img src ="<?=$IMG_SERVER?>/images/registration/pls-wait.jpg" onClick="javascript:saveLoginDetailFromFunnel();" /></div>
			</div>
		</div>
	<? }
} //class end
?>