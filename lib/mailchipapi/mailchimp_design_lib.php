<?php

class mailChimpFancyBoxDesign{

    function displayArticleFancyBoxPopup($emailId){
        global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
        $objMailChimpData= new mailChimp();
        $getMailChimpData=$objMailChimpData->getMailChimpActiveList();

        if(empty($emailId)){
            $emailId="enter your email address";
        }
        ?>

	<div><a id="mailcimpfancypopup" href="#inline1"></a></div>
        <div style="display: none;">
		<<!--div id="inline1">-->
                    <div id="inline1" class="wrapper">
                        <div class="main_img"><img src="<?=$IMG_SERVER.'/'.$getMailChimpData['imagename'];?>" /></div>
			<div class="close_icon"><a id="fancybox-close" style="display: inline;"></a></div>
			<?
				$purifier = new HTMLPurifier($config);
				$self_url = recursive_array_replace($purifier,urldecode($_SERVER['PHP_SELF'])); ?>
                        <div class="bottom_bar">
                          <form autocomplete="off" id="signup" action="<?=$self_url; ?>" method="get">
			<div id="response">&nbsp;
							<? require_once($D_R.'/lib/mailchipapi/store-address.php'); if($_GET['submit']){ echo storeAddress(); } ?>
			</div>
                        <div class="inputContainer">

                           <div class="inputTextVal">
			    <input type="text" name="email" id="email"  value="<?=$emailId;?>" class="input_box" onblur="this.value=!	this.value?'enter your email address':this.value;" onfocus="this.select()" onclick="this.value='';" />
			   </div>
			   <div class="submit_btn">
                            <input type="image" src="<?=$IMG_SERVER;?>/images/fancybox/option_submit_btn.png" name="submit" value="Join" class="btn" alt="SUBMIT" />
                        </div>

                        </div>

                        </form>

                        <div class="konafilter" id="bottom_text">After you download the report, you will receive Minyanville offers from time to time. Unsubscribe at anytime with one click.</div>

                        </div>
                    </div>
		</div>
	<!--</div>-->
        <?

	//mcookie("mailChimpFancyBox",$val="1",$exp="5814000");
	//setcookie("mailChimpFancyBox", "1","5814000");

    }

function displayBuzzFancyBoxPopup($emailId){
        global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
        $objMailChimpData= new mailChimp();
        $getMailChimpData=$objMailChimpData->getMailChimpActiveList();

        if(empty($emailId)){
            $emailId="enter your email address";
        }
        ?>

	<div><a id="mailcimpfancypopup" href="#inline1"></a></div>
        <div style="display: none;">
		<<!--div id="inline1">-->
                    <div id="inline1" class="wrapper">
	     			  <div id="fancybox_container">
						<!-------------------start closebtn---------------------->
						<div id="top_closebtn"><div class="close-button"><img src="<?=$IMG_SERVER;?>/images/fancybox/close-btn.png" /></div></div>
						<!-------------------end closebtn---------------------->
						<!------------------------start main-logocontainer--------------------------->
						<div id="main-logocontainer"><img src="<?=$IMG_SERVER;?>/images/fancybox/logo-image.png" /></div>


						<!------------------------end main-logocontainer--------------------------->
						<div id="data-container">
						<div id="leftdata-container">
						<div id="text-imgcon"><img src="<?=$IMG_SERVER;?>/images/fancybox/text-img.png" /></div>
						<div id="signup-container">
						<?
				$purifier = new HTMLPurifier($config);
				$self_url = recursive_array_replace($purifier,urldecode($_SERVER['PHP_SELF'])); ?>
						<form autocomplete="off" id="signup" action="<?=$self_url; ?>" method="get">
						<div id="response">&nbsp;
								<? require_once($D_R.'/lib/mailchipapi/store-address.php'); if($_GET['submit']){ echo storeAddress(); } ?>
						</div>
						<div id="lfttxt-container"><input type="text" name="email" id="email"  value="<?=$emailId;?>" class="fancybox_email" onblur="this.value=!	this.value?'enter your email address':this.value;" onfocus="this.select()" onclick="this.value='';" /></div>
						<input type="image" id="fancybox_button" src="<?=$IMG_SERVER;?>/images/fancybox/signup-btn.png" name="submit" value="Join" class="btn" alt="SUBMIT" />
						</form>
						</div>

						</div>
						<div id="rightbook-container"><img src="<?=$IMG_SERVER;?>/images/fancybox/book.png" width="230" height="232" /></div>
						</div>

						</div>
						<!-------------------end wrapper---------------------->
                    </div>
		</div>
	<!--</div>-->
        <?

	//mcookie("mailChimpFancyBox",$val="1",$exp="5814000");
	//setcookie("mailChimpFancyBox", "1","5814000");

    }
    function showSurveyBox(){
    	global $IMG_SERVER; ?>
    	<div><a id="surveyBoxPopUp" href="#surveyBox"></a></div>
        <div style="display: none;">
			<div id="surveyBox" class="surveyWapper">
				<div class="surveyHeader"><img src="<?=$IMG_SERVER;?>/images/surveyBox/survey_logo.PNG"></div>
				<div class="surveyContainer">
					<div class="surveyHelp"><img src="<?=$IMG_SERVER;?>/images/surveyBox/survey_helpus.png"></div>
					<div><a href="https://www.surveymonkey.com/s/RP8YRMH" target="_blank" onClick="closeFancyBox();"><img src="<?=$IMG_SERVER;?>/images/surveyBox/survey_button.jpg"></a></div>
				</div>
				<div class="surveyFooter" onClick="closeFancyBox();">No Thanks</div>
			</div>
		</div>
    <? }

    function loadFancybox($pagename)
    {
    	if($pagename=="home" || $pagename=="article_template")
    	{
    		$this->loadSurveyBox();
    		$this->loadTechstratBox();
    	}
    	else
    	{
    		$this->loadSurveyBox();
    	}
    }

    function loadSurveyBox()
    {
    	global $showSurveybox;
    	$objMailChimp= new mailChimp();
	    if(strtolower($_GET['from'])!=="yahoo"){
		if(empty($_COOKIE['surveyBox'])){
			    $setCookieTime=mktime()+259200;  //3 days
				mcookie("surveyBox","1",$setCookieTime);
				$showSurveybox=1;
				$_SESSION['surveyFancybox']="1";
			}
		}
		if($showSurveybox){
			echo $this->showSurveyBox(); ?>
			<script>setTimeout("surveyBox()",3000);</script>
<?
	    }
	 }

	 function loadTechstratBox()
	 {
	 	global $objCache,$showSurveybox;
	 	$objMailChimp= new mailChimp();
	 	$objCache = new Cache();
		if(strtolower($from)!=="yahoo"){

	        $getMailChimpListName=$objCache->getMailChimpActiveList();
	        $mailChimpListId=$getMailChimpListName['listid'];

			if($_COOKIE['mailChimpListId']!=$mailChimpListId){
				unset($_COOKIE['mailChimpListId']);
				unset($_COOKIE['mailChimpFancyBox']);

			}
			if(isset($_COOKIE['mailChimpFancyBox']) || isset($_SESSION['ActiveFancybox'])){
				// do nothing
			}
			else
			{
		        $setCookieTime=mktime()+1209600;  //14 days
				mcookie("mailChimpFancyBox","1",$setCookieTime);
				mcookie("mailChimpListId",$mailChimpListId,$setCookieTime);
				$_SESSION['ActiveFancybox']="1";
				$showFancybox=1;
			}

		 }
		if($showFancybox){
			$emailId=$_SESSION['email'];
			echo $this->displayBuzzFancyBoxPopup($emailId);
			?>
				<script>setTimeout("mailChimpFancyBox()",3000);</script>
		       <?

		}
	 }
	function showBuzzFancyBox(){
		global $IMG_SERVER;
		if(strtolower($from)!=="yahoo"){
			$currentDate=date('Y-m-d');
			$ptrCookieVal = $_COOKIE['ptrFancyBox'];
			$keeneCookieVal = $_COOKIE['keeneFancyBox'];
			
			$ptrCookieDiff = date_difftime($ptrCookieVal,$currentDate);
			$ptrCookieDiffVal = intval($ptrCookieDiff['d']);
			
			$keeneCookieDiff = date_difftime($keeneCookieVal,$currentDate);
			$keeneCookieDiffVal = intval($keeneCookieDiff['d']);
			
			if(!isset($_COOKIE['ptrFancyBox']) && !isset($_COOKIE['keeneFancyBox'])){
				// set for peter
				$setCookieTime=mktime()+1209600;  //14 days
				mcookie("ptrFancyBox",$currentDate,$setCookieTime);
				$submitBttnImg = 'pt_fBox_btn_.png';
				$fancyBox = 'peter';
				$showFancybox=1;
			}elseif(isset($_COOKIE['ptrFancyBox']) && $ptrCookieDiffVal>=7 && !isset($_COOKIE['keeneFancyBox'])){
				// set for keene
				$setCookieTime=mktime()+1209600;  //14 days
				mcookie("keeneFancyBox",$currentDate,$setCookieTime);
				$submitBttnImg = 'fancybox_keeneOnOptions_btn.png';
				$fancyBox = 'keene';
				$showFancybox=1;
			}elseif(isset($_COOKIE['keeneFancyBox']) && $keeneCookieDiffVal>=7 && !isset($_COOKIE['ptrFancyBox'])){		
				// set for peter
				$setCookieTime=mktime()+1209600;  //14 days
				mcookie("ptrFancyBox",$currentDate,$setCookieTime);
				$submitBttnImg = 'pt_fBox_btn_.png';
				$fancyBox = 'peter';
				$showFancybox=1;
			}else{
				$showFancybox=0;
			}
			
			if($showFancybox==1){
				echo $this->displayFancyBoxPopUp($fancyBox,$submitBttnImg); ?>
				<script>setTimeout("mailChimpFancyBox()",3000);</script>
		<? 	}
		}

	}

	function displayFancyBoxPopUp($fancyBox,$submitBttnImg){
		global $IMG_SERVER,$D_R;
		if(empty($emailId)){
			$emailId='enter your email here';
		}
		?>
		<div><a id="mailcimpfancypopup" href="#inline1"></a></div>
        <div style="display: none;">
			<div id="inline1" class="wrapper">
				<div id="fancybox_container">
				<?php if($fancyBox=="keene"){ ?>
					<div id="data-container-next">
				<?php } else { ?>
					<div id="data-container">
				<?php } ?>
						<div id="signup-container">
							<form autocomplete="off" id="signup" action="<?=$self_url;?>" method="get">
								<input type="hidden" name="fancyboxName" id="fancyboxName" value="<?=$fancyBox?>">
								<div id="response">&nbsp;
										<? require_once($D_R.'/lib/mailchipapi/store-address.php'); if($_GET['submit']){ echo storeAddress(); } ?></div>
								<input type="text" class="fancybox_email" name="email" id="email"  value="<?=$emailId;?>" onblur="this.value=!	this.value?'enter your email here':this.value;" onfocus="this.select()" onclick="this.value='';"/>
								<input type="image" id="fancybox_button" src="<?=$IMG_SERVER;?>/images/fancybox/<?=$submitBttnImg;?>" name="submit" class="btn" alt="SUBMIT" />
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div><a id="thankYouFancyBox" href="#inlineThanks"></a></div>
		<div style="display: none;">
			<div id="inlineThanks" class="wrapper">
				<div id="fancybox_container">
					<div id="data-container-thanku">
						<div class="thanksFancyBoxCloseBttn">
							<img onClick="closeFancyBox();" align="right" src="<?php echo $IMG_SERVER;?>/images/fancybox/bnb_closeBtn.png" alt="" />
						</div>
						<div class="thanksFancyBoxText">Thank you very much;<br/> you're only a step away from<br/> downloading your report.</div>
						<div class="thanksFancyBoxLinkText">You will receive a download link right in your <br/>email inbox to get your report.</div>
					</div>
				</div>
			</div>
		</div>
<? 	}
} //class end
?>