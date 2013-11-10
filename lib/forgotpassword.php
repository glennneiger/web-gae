<?php

class forgotPassFancyBoxDesign{

    function displayFancyBoxPopup(){
        global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
        ?>

	<div><a id="forgotpassfancypopup" href="#inline1"></a></div>
        <div style="display: none;">
		<<!--div id="inline1">-->
                    <div id="inline1" class="wrapper" style="width:400px;">
			<div class="forgot_wrapper">
			    <div class="forgot_password_hdr"><img src="<?=$IMG_SERVER?>/images/subscription/forgot_password.jpg" /></div>
			    <hr class="forgot_hdr_hr" />

			    <div class="forgot_main_area">
			    <div class="forgot_main_area_txt">Enter your email address here. We'll email you a new one. <br /><br /> <strong>Your Email Address:</strong></div>
			    <div class="login_error_password" style="float: left;padding: 4px 0 4px 2px;" id="password_login_error"></div>
			    <div> <input type="text" id="forgot_pwd" tabindex="5" class="forgot_input_box" onkeyup="javascript:fpEnterKeyChk(event);"   /></div>
			    <div class="forgot_submit_btn"><a ><img style="cursor: pointer;" tabindex="6" onClick="javascript:checkforgotpassword('password_login_error','forgot_pwd');" src="<?=$IMG_SERVER?>/images/subscription/forgot_submit.jpg" onkeyup="javascript:fpEnterKeyChk(event);" /></a></div>
			    </div>
			</div>
                    </div>
		</div>
	<!--</div>-->
        <?

	//mcookie("mailChimpFancyBox",$val="1",$exp="5814000");
	//setcookie("mailChimpFancyBox", "1","5814000");

    }


}





?>