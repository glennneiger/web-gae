function addSubscription(planCode){
	jQuery.ajax({
		type : "POST",
		url : host+"/lib/registration/register_mod.php",
		data : "type=addSubscription"+"&planCode="+planCode,
		beforeSend : function(){
			jQuery('div#cross_sell_error_div').html('Loading..');
		},
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==1){
				window.location = host+'/subscription/register/welcome.htm';
			}else{
				var error = result.error;
				jQuery('div#cross_sell_error_div').html(error);
			}
		}
	});
}

function saveLoginDetailFromFunnel(isAgree){
	if(jQuery('#errorFunnel').html()!=''){
		jQuery('#errorFunnel').empty();
	}
	if(jQuery('#couponErrorFunnel').html()!=''){
		jQuery('#couponErrorFunnel').empty();
	}
	if(jQuery('#errorAgreeFunnel').html()!=''){
		jQuery('#errorAgreeFunnel').empty();
	}
	
	var subId = jQuery('#subId').val();
	var funnelFirstName = jQuery('#funnelFirstName').val();
	if(funnelFirstName==""){
		jQuery('#errorFunnel').html('Please enter your First Name');
		jQuery('#funnelFirstName').focus();
		return false;
	}
	
	var funnelLastName = jQuery('#funnelLastName').val();
	if(funnelLastName==""){
		jQuery('#errorFunnel').html('Please enter your Last Name');
		jQuery('#funnelLastName').focus();
		return false;
	}
	
	var funnelEmail = jQuery('#funnelEmail').val();
	if(funnelEmail==""){
		jQuery('#errorFunnel').html('Please enter your Email');
		jQuery('#funnelEmail').focus();
		return false;
	}
	
	
	var emailRegex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/;
	if(!emailRegex.test(funnelEmail)){
		jQuery('#errorFunnel').html('Invalid Email');
		jQuery('#funnelEmail').focus();
		return false;
	}
	
	var funnelAddress = jQuery('#funnelAddress').val();
	if(funnelAddress==""){
		jQuery('#errorFunnel').html('Please enter your Billing Address');
		jQuery('#funnelAddress').focus();
		return false;
	}
	
	var funnelCountry = jQuery('#funnelCountry').val();
	if(funnelCountry=="select"){
		jQuery('#errorFunnel').html('Please select Country');
		jQuery('#funnelCountry').focus();
		return false;
	}
	
	var funnelCity = jQuery('#funnelCity').val();
	if(funnelCity==""){
		jQuery('#errorFunnel').html('Please enter City');
		jQuery('#funnelCity').focus();
		return false;
	}
	
	var funnelState = jQuery('#funnelState').val();
	if(funnelState==""){
		jQuery('#errorFunnel').html('Please enter State');
		jQuery('#funnelState').focus();
		return false;
	}
	
	var funnelZip = jQuery('#funnelZip').val();
	if(funnelZip==""){
		jQuery('#errorFunnel').html('Please enter Zip Code');
		jQuery('#funnelZip').focus();
		return false;
	}
	
	var funnelPhone = jQuery('#funnelPhone').val();
	if(funnelPhone==""){
		jQuery('#errorFunnel').html('Please enter Phone Number');
		jQuery('#funnelPhone').focus();
		return false;
	}
	
	var funnelCCtype = jQuery('#funnelCCtype').val();
	if(funnelCCtype=="select" || funnelCCtype=="undefined"){
		jQuery('#errorFunnel').html('Please select Payment Method');
		jQuery('#funnelCCtype').focus();
		return false;
	}
	
	var funnelCCnum = jQuery('#funnelCCnum').val();
	if(funnelCCnum==""){
		jQuery('#errorFunnel').html('Please enter Card Number');
		jQuery('#funnelCCnum').focus();
		return false;
	}
	
	var intRegex = /^\d+$/;
    if(!intRegex.test(funnelCCnum)) {
		jQuery('#errorFunnel').html('Invalid Card Number');
		jQuery('#funnelCCnum').focus();
		return false;
	}
	
	var funnelExpMnth = jQuery('#funnelExpMnth').val();
	if(funnelExpMnth==""){
		jQuery('#errorFunnel').html('Please select CC Exp Month');
		jQuery('#funnelExpMnth').focus();
		return false;
	}
	
	var funnelExpYr = jQuery('#funnelExpYr').val();
	if(funnelExpYr==""){
		jQuery('#errorFunnel').html('Please select CC Exp Year');
		jQuery('#funnelExpYr').focus();
		return false;
	}
	
	var funnelcvv = jQuery('#funnelcvv').val();
	if(funnelcvv==""){
		jQuery('#errorFunnel').html('Please enter Security Code');
		jQuery('#funnelcvv').focus();
		return false;
	}
	var prefVal='';
	if(isAgree=='sms-agree'){
		var smsCheck = jQuery('#funnelSmsCheck:checked').is(':checked');
		var emailCheck = jQuery('#funnelEmailCheck:checked').is(':checked');
		if(emailCheck==false && smsCheck==false){
			jQuery('#errorAgreeFunnel').html('Please Select your alert update preference.');
			return false;
		}
		
		if(emailCheck==true && smsCheck==true){
			prefVal = 'both';
		}else if(smsCheck==true){
			prefVal = 'sms';
		}else if(emailCheck==true){
			prefVal = 'email';
		}
	}
	var agreeCheck = jQuery('#funnelAgreeCheck:checked').is(':checked');
	if(agreeCheck==false){
		jQuery('#errorAgreeFunnel').html('Please confirm that you agree with Terms and Conditions by checking the box.');
		return false;
	}
		
	var funnelCouponCode = jQuery('#funnel_promo_code').val();
	var funnelPlanCode = jQuery('#funnelPlanCode').val();
	var funnelPlanGroup = jQuery('#funnelPlanGroup').val();
	if(subId==''){
		jQuery.ajax({
			type : "POST",
			url : host+"/subscription/register/loginAjax.php",
			data : "type=newSubscription&token="+token,
			beforeSend : function(){
				jQuery('div#recurly-subscribe .footer').addClass('funnel_loading_img');
			},
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==1){
					window.location = host+'/subscription/register/cross-sell.htm';
				}else{
					
				}
			}
		});
	}else{
		jQuery.ajax({
			type : "POST",
			url : host+"/subscription/register/loginAjax.php",
			data : "type=registration&subid="+subId+"&funnelEmail="+funnelEmail+"&funnelFirstName="+funnelFirstName+"&funnelLastName="+funnelLastName+"&funnelAddress="+funnelAddress+"&funnelCountry="+funnelCountry+"&funnelCity="+funnelCity+"&funnelState="+funnelState+"&funnelZip="+funnelZip+"&funnelPhone="+funnelPhone+"&funnelCCtype="+funnelCCtype+"&funnelCCnum="+funnelCCnum+"&funnelExpMnth="+funnelExpMnth+"&funnelExpYr="+funnelExpYr+"&funnelcvv="+funnelcvv+"&funnelCouponCode="+funnelCouponCode+"&funnelPlanCode="+funnelPlanCode+"&funnelPlanGroup="+funnelPlanGroup+"&pref="+prefVal,
			beforeSend : function(){
				jQuery('div#funnelSubmitBttn').hide();
				jQuery('div#funnelWaitBttn').show();
			},
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				jQuery('div#funnelWaitBttn').hide();
				jQuery('div#funnelSubmitBttn').show();
				if(result.status==true){
					window.location = host+'/subscription/register/cross-sell.htm';
				}else{
					jQuery('#errorFunnel').html(result.id);
					jQuery('#errorFunnel').focus();
					//window.location = host+'/subscription/register/cross-sell.htm';
				}
			}
		});
	}
}

function applyCoupon(funnelPrice){
	if(jQuery('#couponErrorFunnel').html()!=''){
		jQuery('#couponErrorFunnel').empty();
	}
	var coupon_code = jQuery('#funnel_promo_code').val();
	var funnelPlanCode = jQuery('#funnelPlanCode').val();
	if(coupon_code==''){
		jQuery('#couponErrorFunnel').html('Please enter coupon code.');
		jQuery('#funnel_promo_code').focus();
		return false;
	}
	jQuery.ajax({
		type : "POST",
		url : host+"/subscription/register/loginAjax.php",
		data : "type=applyCoupon&couponCode="+coupon_code+"&funnelPlanCode="+funnelPlanCode+"&funnelPrice="+funnelPrice,
		error : function(){},
		beforeSend : function(){
			jQuery('#funnel_apply_img').hide();
			jQuery('#applyLoading').show();
		},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				jQuery('#payableAmt').html(result.msg);
			}else{
				jQuery('#couponErrorFunnel').html(result.msg);
				if(result.msg=="Coupon does not exist."){
					jQuery('#payableAmt').html(funnelPrice+'.00');
				}
				jQuery('#funnel_promo_code').focus();
			}
			jQuery('#funnel_apply_img').show();
			jQuery('#applyLoading').hide();
		}
	});
}

function checkFoneValidity(){
	if(jQuery('#errorNumFunnel').html()!=''){
		jQuery('#errorNumFunnel').empty();
	}
	var textAlertCheck = jQuery('#funnelSmsCheck:checked').is(':checked');
	if(textAlertCheck==true){
		jQuery('#funnelSmsCheck').attr('checked','checked');
		var numForSms = jQuery('#funnelMobileNum').val();
		if(numForSms==""){
			jQuery('#errorNumFunnel').html('Please Enter Mobile Number.');
			jQuery('#funnelSmsCheck').removeAttr('checked');
			jQuery('#funnelMobileNum').focus();
			return false;
		}
	
		var usNumRegex=/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/;
		if(!usNumRegex.test(numForSms)) {
			jQuery('#errorNumFunnel').html('Invalid Mobile Number');
			jQuery('#funnelSmsCheck').removeAttr('checked');
			jQuery('#funnelMobileNum').focus();
			return false;
		}
	}
}

function checkEmailValidity(){
	if(jQuery('#errorEmailFunnel').html()!=''){
		jQuery('#errorEmailFunnel').empty();
	}
	var textAlertCheck = jQuery('#funnelEmailCheck:checked').is(':checked');
	if(textAlertCheck==true){
		jQuery('#funnelEmailCheck').attr('checked','checked');
		var emailForAlert = jQuery('#funnelEmailForAlert').val();
		if(emailForAlert==""){
			jQuery('#errorEmailFunnel').html('Please Enter your Email id.');
			jQuery('#funnelEmailCheck').removeAttr('checked');
			jQuery('#funnelEmailForAlert').focus();
			return false;
		}
	
		var emailRegex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/;
		if(!emailRegex.test(emailForAlert)) {
			jQuery('#errorEmailFunnel').html('Invalid Email Address.');
			jQuery('#funnelEmailCheck').removeAttr('checked');
			jQuery('#funnelEmailForAlert').focus();
			return false;
		}
	}
}