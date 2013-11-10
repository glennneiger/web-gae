function trim(str)
{
	
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
	
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
	
	return str;
}

function validatePhone(strValue){
	//var objRegExp  = /^\([1-9]\d{2}\)\s?\d{3}\-\d{4}$/;
	var objRegExp  = /^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/;
	return objRegExp.test(strValue);
	// Ommit Phone format check
	return true;
}
function validateZipcode(strValue){
	// allow alphanumeric, spaces , + , - , _ , ( , )
	//var objRegExp  = /(^\d{5}$)|(^\d{5}-\d{4}$)/;
	//var objRegExp  = /^[a-zA-Z0-9+)(-_ ]+$/;
	//return objRegExp.test(strValue);
	var bValid=true;
	var string =strValue;
	var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-+)( ";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1)
        {
		      bValid=false; 
		}
    }
	return bValid;
}


function LZ(x) {return(x<0||x>9?"":"0")+x}

/* 
Text must be numeric */
function validateNumField(field1,errordiv) {
	field=document.getElementById(field1);
	string = trim(field.value);
    var bValid =new Boolean(true);
	if (!string) 
	{	
		bValid=false;
	}
    var Chars = "0123456789";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1)
        {
		      bValid=false; 
		}
    }
	
	if(!bValid)
	{
		var msg='Not a valid '
		var long="";
		if(field.name=='zipcode')
			long="Zip Code.";
		if(field.name=='phone')
			long="Phone number.";
		if(field.name=='cvvnum')
			long="CVV number.";		
		if(field.name=='ccnum')
			long="Credit Card number.";	
		$(errordiv).innerHTML=msg + long;
		field.select();
		field.focus();
	}
    return bValid;
} 
//=====================================================================================
function get_login_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		userLogin();
	}
}



//=====================================================================================
function enable_billingdetails(){
	$('address1').disabled=false;
	$('address2').disabled=false;
	$('city').disabled=false;
	$('state').disabled=false;
	$('zipcode').disabled=false;
	$('country').disabled=false;
	$('phone').disabled=false;
	$('cctype').disabled=false;
	$('ccnum').disabled=false;
	$('ccexpire').disabled=false;
	$('cvvnum').disabled=false;
	$('address1').select();
	$('activeplaceorder').src=host+'/images/redesign/place_order_active.jpg';
}

function disable_billingdetails(){
	$('address1').disabled=true;
	$('address2').disabled=true;
	$('city').disabled=true;
	$('state').disabled=true;
	$('zipcode').disabled=true;
	$('country').disabled=true;
	$('phone').disabled=true;
	$('cctype').disabled=true;
	$('ccnum').disabled=true;
	$('ccexpire').disabled=true;
	$('cvvnum').disabled=true;
}

function get_register_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		product_registration();
	}
}

function get_register_submit_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		billing_validate();
	}
}



function validateName(errorDiv,field1,alertMsg) {

	var string = trim($F(field1));
	//alert(string.length+" "+field1);
	var bValid =new Boolean(true);
	if (!string) 
	{	
		bValid=false;
	}
	var Chars = "-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
	for (var i = 0; i < string.length; i++) {
		if (Chars.indexOf(string.charAt(i)) == -1)
		{
			bValid=false; 
		}
	}
	if(!bValid)
	{
		var msg='Not a valid '+alertMsg;
		$(errorDiv).innerHTML=msg;
		//alert(msg);
		$(field1).select();
	}
	return bValid;
}

function validate_registration(errordiv){
	var errordiv="regerror";
	var status=true;
	$("regmovingdiv").style.display='block';
	//timedOut();
	if($('firstname').value==''){
		// $(errordiv).innerHTML='Enter First name.';
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('firstname').select();
		return false;
	}
	if(validateName(errordiv,'firstname','First name')==false){
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('firstname').select();
		return false;
	}

	if($('lastname').value==''){
		$(errordiv).innerHTML='Enter Last name.';
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('lastname').select();
		return false;
	}

	if(validateName(errordiv,'lastname','Last name')==false){
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('lastname').select();
		return false;
	}

	if(iboxisValidEmail(errordiv,'viauserid')==false){
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('viauserid').select();
		return false;
	}
	if(iboxisValidPasswordRegistration(errordiv,'viapass')==false){
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('viarepass').select();
		return false;
	}
	 
	if($('viapass').value!=$('viarepass').value){
		$(errordiv).innerHTML='Password and Confirm password does not match.';		
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('viarepass').select();
		return false;
	}
	if($("viapass").value.toUpperCase()=='PASSWORD'){
		$(errordiv).innerHTML="Please Enter Any other Password Except 'Password'";
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('viarepass').select();
		return false;
	}	
	if($("terms").checked==false){
		$(errordiv).innerHTML="Please accept terms of use.";
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('terms').select();
		return false;
	}
	create_registration();
}

function create_registration(){
	$("regmovingdiv").style.display='none';
	// var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=register";
	pars+='&uid='+$('viauserid').value;
	pars+='&pwd='+$('viapass').value;
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;
	if($('viauserremember').checked==true){
		pars+="&rememeber=1";
	}
	else{
		pars+="&rememeber=0";	
	}	
	if($('alerts').checked==true){
		pars+='&alerts=1';
	}
	else{
			pars+='&alerts=0';
	}
	/*
	if($('referralcode').value!=''){
		pars+='&refcode='+$('referralcode').value;
	}
	*/
	$('statusmsgstep1').innerHTML='';
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onLoading:loading_manage('regerror'),
	onComplete:finish_account});
}

function finish_account(req){
	// clear validation messgae 
	$('statusmsgstep1').innerHTML="";
	//get response fron ajax request
	post = eval('('+req.responseText+')');
	$("regmovingdiv").style.display="none";	
	if(post.status==true){		
		create_account();				
	}
	else{
		$("regmovingdiv").style.display='block';
		timedOut();
		$('regerror').innerHTML='';
		$('regerror').innerHTML=post.msg;
	}
}
//=====================================================================================
function get_order_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		billing_validate();
	}
}

function validate_order(){
	//$('regstep2').style.display='none';
	// $("regmovingdiv").style.display='block';
	var status=true;

	if(trim($('address1').value)==''){
		timedOut();		
		$('regerror').innerHTML='Billing Address is blank';		
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('address1').select();
		return false;
	}
	if(trim($('city').value)==''){
		timedOut();
		$('regerror').innerHTML='City is blank';
	//    window.location.href=host+'/subscription/register/#accountbilldiv';
		$('city').select();
		return false;
	}
	if(trim($('phone').value)==''){
		timedOut();
		//$('regerror').innerHTML='Enter phone.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('phone').select();
		return false;
	}

	if(trim($('zipcode').value)==''){
		timedOut();
		$('regerror').innerHTML='Enter zipcode.';		
	//	window.location.href=host+'/subscription/register/#accountbilldiv';
		$('zipcode').select();
		return false;
	}
	
	if(validateZipcode($('zipcode').value)==false){
		timedOut();
		$('regerror').innerHTML='Invalid zipcode';
	//	window.location.href=host+'/subscription/register/#accountbilldiv';
		$('zipcode').select();
		return false;
	}
	

	if(($('country').value=='AA' || $('country').value=='AB') && $('state').value==''){
		timedOut();
		$('regerror').innerHTML='Select state.';
	//	window.location.href=host+'/subscription/register/#accountbilldiv';
		$('state').select();
		return false;
	}	
	if($('country').value==''){
		timedOut();
		$('regerror').innerHTML='Select country.';		
	//	window.location.href=host+'/subscription/register/#accountbilldiv';
		$('country').select();
		return false;
	}	
	
	if($('cctype').value==''){
		timedOut();
		$('regerror').innerHTML='Select credit card type.';	
	//	window.location.href=host+'/subscription/register/#paymentdetails';
		$('cctype').select();
		return false;
	}
	if($('ccnum').value==''){
		timedOut();
		$('regerror').innerHTML='Enter credit card number.';
		// window.location.href=host+'/subscription/register/#paymentdetails';
		$('ccnum').select();
		return false;
	}
	
	
	if(validateNumField('ccnum','regerror')==false){	
	  timedOut();
		$('ccnum').select();
		return false;
	}
	if(!checkCreditCard ($('ccnum').value, $('cctype').value)){
		if(ccErrorNo!=0 ){
			timedOut();
			$('regerror').innerHTML=ccErrors[ccErrorNo];	
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('ccnum').select();
		}

		return false;
	}

	if($('ccexpire').value==''){
		timedOut();
		$('regerror').innerHTML='Enter credit card expiry date.';		
		//window.location.href=host+'/subscription/register/#paymentdetails';
		$('ccexpire').select();
		return false;
	}

	if(validateExDate('ccexpire','regerror')==false){
		timedOut();
	//	window.location.href=host+'/subscription/register/#paymentdetails';
		$('ccexpire').select();
		return false;
	}
	
	if($('cvvnum').value==''){
		timedOut();
		$('regerror').innerHTML='Enter CVV number.';
	//	window.location.href=host+'/subscription/register/#paymentdetails';
		$('cvvnum').select();
		return false;
	}		
	if(validateNumField('cvvnum','regerror')==false){
		timedOut();
	//	window.location.href=host+'/subscription/register/#paymentdetails';
		$('cvvnum').select();
		return false;
	}
	$("regmovingdiv").style.display='none';
	place_order();
}

function place_order(){	
   $("regmovingdiv").style.display='none';
    //$('regerror').style.display='none';
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=update_customer_order";
	pars+="&address1="+encodeURIComponent($("address1").value);
	pars+="&address2="+encodeURIComponent($("address2").value);
	pars+="&city="+$("city").value;
	pars+="&zipcode="+$("zipcode").value;
	pars+="&state="+$("state").value;
	pars+="&country="+$("country").value;
	pars+="&phone="+$("phone").value;
	pars+="&cctype="+$("cctype").value;
	pars+="&ccnum="+$("ccnum").value;
	var exDate=$("ccexpire").value.split('-');
	pars+="&ccexpire="+exDate[1]+'-'+exDate[0];	
	pars+="&cvvnum="+$("cvvnum").value;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_manage('regerror'),onComplete:finish_order});
}

function finish_order(req){
	post = eval('('+req.responseText+')');
	if(post.status==true){
		//alert(post.trackname);
		
		    var strval=post.trackname;
			var strvalur=strval.split(',');
			var arrlen=strvalur.length;
			var i;
			for(i=0;i<=arrlen-1;i++){
				//googleAnalytics('',strvalur[i]);
			}
		     
		
		//alert(post.msg);
		//$("overlay").style.display="none";
		window.location.href=host+"/subscription/register/welcome.htm";
	}
	else{
		//alert(post.msg);
		$("regmovingdiv").style.display='block';
		timedOut();
		$('regerror').innerHTML=post.msg;
		//alert('Error');
	}
}
//=====================================================================================
function manage_state(countryid,stateid){
	var countryCode = $(countryid).value;
	if(countryCode=='AB' || countryCode=='AA'){
		var url=host+'/lib/registration/displayState.php';
		var parstate="countryCode="+countryCode;
		var State = new Ajax.Request(url, {method: 'post', parameters: parstate,
						onComplete:function(req){
								$(stateid).disabled=false;
								$('state-div').innerHTML = "";
								$('state-div').innerHTML = req.responseText;
						}
			 });
	}/*else if($(countryid).value=='AA'){
		$(stateid).disabled=false;
		var url=host+'/assets/data/states.txt';
		var parstate="";
		var canadaState = new Ajax.Request(url, {method: 'post', parameters: parstate, 
						onComplete:function getStatesUS(req){
								$(stateid).innerHTML = req.responseText;
						}
			 });
	}*/else if($(countryid).value!='AA'){
		$(stateid).disabled=true;
	}
	else{
		$(stateid).disabled=false;
	}
}

function checkProductSession(){

	var url=host+'/subscription/register/registration_ajax.php';
	var pars='&type=checkccstatus';
	var ccDetails;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_manage('regerror'),onComplete: function finish_settings (req){
		ccDetails=parseInt(req.responseText);
		checkCartStatus(ccDetails);
	}
	});	
}
function checkCartStatus(ccDetails){
	var url = host+'/subscription/_carthandler.php';
	var pars = '&action=submitcart';
	var postAjax= new Ajax.Request(url, {method: 'post',
		parameters: pars,onLoading:loading_manage('regerror'),onComplete:function sumitcartaction(req){
			var strval=req.responseText;
			var strvalur=strval.split(',');
			var strResponse =strvalur[0];
			var arrlen=strvalur.length;
			var i;
			
			for(i=1;i<=arrlen-1;i++){
				//googleAnalytics('',strvalur[i]);
			}
		
			if(strResponse!='true'){
				$("regmovingdiv").style.display='block';
				timedOut();
				$('regerror').innerHTML=strResponse;
				return false;
			}
			else{
				validate_manage_settings(ccDetails);
			}
		}
	});	

}


function get_manage_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		//$('manage_err_div').innerHTML="";
		//$('via_billingerror').innerHTML="";
		//$('via_paymenterror').innerHTML="";
		checkProductSession();
	}
}
function validate_manage_settings(ccDetails){
	$("regmovingdiv").style.display='none';
	var status=true;
	if(iboxisValidEmail('regerror','viauserid')==false){
		timedOut();
		//window.location.href=host+'/subscription/register/#manage_err_div';
		$('viauserid').select();
		return false;
	}

	if($('viachgpass').value!='' && iboxisValidPasswordRegistration('regerror','viachgpass')==false){
		timedOut();
		//window.location.href=host+'/subscription/register/#manage_err_div';
		$('viachgpass').select();
		return false;
	}
	
	if($('firstname').value==''){
		timedOut();
		$('regerror').innerHTML='Enter First name.';		
		//window.location.href=host+'/subscription/register/#manage_err_div';
		$('firstname').select();
		return false;
	}
	
	if(validateName('regerror','firstname','First name')==false){
		timedOut();
	//	window.location.href=host+'/subscription/register/#manage_err_div';
		$('firstname').select();
		return false;
	}
	
	if($('lastname').value==''){
		timedOut();
		$('regerror').innerHTML='Enter Last name.';		
		//window.location.href=host+'/subscription/register/#manage_err_div';
		$('lastname').select();
		return false;
	}

	if(validateName('regerror','lastname','Last name')==false){
		timedOut();
		//window.location.href=host+'/subscription/register/#manage_err_div';
		$('lastname').select();
		return false;
	}		


	if(trim($('address1').value)==''){
		timedOut();
		$('regerror').innerHTML='Enter address.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('address1').select();
		return false;
	}
	/*
	if($('address2').value==''){
		$('via_billingerror').innerHTML='Enter address.';
		$('address2').select();
		return false;
	}
		*/
	if(trim($('city').value)==''){
		timedOut();
		$('regerror').innerHTML='Enter city.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('city').select();
		return false;
	}
 	
	if($('zipcode').value==''){
		timedOut();
		$('regerror').innerHTML='Enter zipcode.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('zipcode').select();
		return false;
	}
	if(validateZipcode($('zipcode').value)==false){
		timedOut();
		$('regerror').innerHTML='Invalid zipcode';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('zipcode').select();
		return false;
	}
	
	if(($('country').value=='AA' || $('country').value=='AB') && $('state').value==''){
		timedOut();
		$('regerror').innerHTML='Select state.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('state').select();
		return false;
	}	
	if($('country').value==''){
		timedOut();
		$('regerror').innerHTML='Select country.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('country').select();
		return false;
	}	
	if(trim($('phone').value)==''){
		timedOut();
		$('regerror').innerHTML='Enter phone.';
		//window.location.href=host+'/subscription/register/#accountbilldiv';
		$('phone').select();
		return false;
	}
	/**if(validatePhone($('phone').value)==false){
		$('via_billingerror').innerHTML='Not a valid Phone Number';
		return false;
	}****/
	/*
	if(validateNumField('phone','via_billingerror')==false){
		return false;
	}
	*/
	if(ccDetails==1){
		if($('cctype').value==''){
			timedOut();
			$('regerror').innerHTML='Select credit card type.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cctype').select();
			return false;
		}
		if($('ccnum').value==''){
			timedOut();
			$('regerror').innerHTML='Enter credit card number.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('ccnum').select();
			return false;
		}
	
		/**if(validateNumField('ccnum','via_paymenterror')==false){
			return false;
		}**/
		if($('ccexpire').value==''){
			timedOut();
			$('regerror').innerHTML='Enter credit card expiry date.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('ccexpire').select();
			return false;
		}		
		if(validateExDate('ccexpire','regerror')==false){
			timedOut();
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('ccexpire').select();
			return false;
		}
		
				
		/**if(validateNumField('cvvnum','via_paymenterror')==false){
			return false;
		}**/
	 
		var CCnumber = trim($('ccnum').value);
		var CCstr = CCnumber.substr(0,12); 		 
		var CCstrnum  = CCnumber.substr(12,16); 
		 
		var objRegExp  = /^(\*){12}/; 
		if(objRegExp.test(CCstr)){		 
			if(isNaN(CCstrnum) && CCstrnum.length==4){	 
  				timedOut();
				$('regerror').innerHTML=ccErrors['2'];//'Credit card number is in invalid format';
				//$('manage').innerHTML ='';
				//window.location.href=host+'/subscription/register/#paymentdetails';
				$('ccnum').select();
				return false;
			}	 		
		}else{
			if($('cvvnum').value==''){
			timedOut();	
			$('regerror').innerHTML='Enter CVV number.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cvvnum').select();
			return false;
		}
			if(validateNumField('cvvnum','regerror')==false){
				timedOut();
				//window.location.href=host+'/subscription/register/#paymentdetails';
				$('cvvnum').select();
				return false;
			}
			if(!checkCreditCard ($('ccnum').value, $('cctype').value)){
				if(ccErrorNo!=0 ){
					timedOut();
					$('regerror').innerHTML=ccErrors[ccErrorNo];
					// window.location.href=host+'/subscription/register/#paymentdetails';
					$('ccnum').select();
				}
				
				return false;
			}
		}
	}
	manage_settings();
}

//=============================================================

function manage_settings(){
	$("regmovingdiv").style.display='none';
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=manage_settings";
	pars+="&uid="+$("viauserid").value;
	pars+="&uid_default="+$("viauserid").defaultValue;
	pars+="&password="+$("viachgpass").value;
	pars+="&firstname="+$("firstname").value;
	pars+="&lastname="+$("lastname").value;
	pars+="&address1="+encodeURIComponent($("address1").value);
	pars+="&address2="+encodeURIComponent($("address2").value);
	pars+="&city="+$("city").value;
	pars+="&zipcode="+$("zipcode").value;
	pars+="&state="+$("state").value;
	pars+="&country="+$("country").value;
	pars+="&phone="+$("phone").value;
	pars+="&cctype="+$("cctype").value;
	pars+="&ccnum="+$("ccnum").value;
	var exDate=$("ccexpire").value.split('-');
	pars+="&ccexpire="+exDate[1]+'-'+exDate[0];
	pars+="&cvvnum="+$("cvvnum").value;
//=====================================================================================================	
	// check for CC details updation	
	if($("ccnum").value != $("ccnum").defaultValue){
		updateCCStatus=true;
		if($('cctype').value==''){
			timedOut();
			$('regerror').innerHTML='Select credit card type.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cctype').select();
			return false;
		}
		if($('cvvnum').value==''){
			timedOut();
			$('regerror').innerHTML='Enter CVV number.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cvvnum').select();
			return false;
		}
		if(validateNumField('cvvnum','regerror')==false){				
		timedOut();
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cvvnum').select();
			return false;
		}
		if(!checkCreditCard ($('ccnum').value, $('cctype').value)){
			if(ccErrorNo!=0 ){
				timedOut();
				$('regerror').innerHTML=ccErrors[ccErrorNo];
				//window.location.href=host+'/subscription/register/#paymentdetails';
				$('ccnum').select();
			}
				
			return false;
		}
		
		pars+="&updateccnum="+$("ccnum").value;
	}
	
	if($("ccexpire").value != $("ccexpire").defaultValue){
		updateCCStatus=true;
		if($('cctype').value==''){
			timedOut();
			$('regerror').innerHTML='Select credit card type.';
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('cctype').select();
			return false;
		}		
		// validate Expire date
		if(validateExDate('ccexpire','regerror')==false){
			timedOut();
			//window.location.href=host+'/subscription/register/#paymentdetails';
			$('ccexpire').select();
			return false;
		}
		
		pars+="&updateccexpire="+exDate[1]+'-'+exDate[0];
	}
	$("regmovingdiv").style.display='none';

	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_manage_hide('regerror'),onComplete:finish_settings});	
}
function loading_manage_hide(id){
	$("regmovingdiv").style.display='block';
	$(id).innerHTML="We are processing your request. Please wait....";  			 
	$('submit_show').hide();
	$('submit_hide').show();
	//window.location.href=host+'/subscription/register/';
}

function finish_settings(req){
	$("manage").innerHTML="";
	post = eval('('+req.responseText+')');	
	if(post.status==true){
		if(post.logout == 1)
		{
			// Logout user if he has changed email/password
			logout(post.sub_id);
			return;
		}
		
		if(post.cancel=='cancelAdFree'){
			cancelViaAdsFreeProd(post.frm,post.to,'','','SUBSCRIPTION');
		}

		if(post.msg=='welcome'){
			//setTimeout("alert('Thirty seconds has passed.');",30000);
			setTimeout("window.location=host+'/subscription/register/welcome.htm';",25000);
		}
		else{
			setTimeout("window.location=host+'/subscription/register/welcome.htm?action=update';",25000);	

		}
		 render_cart();
	}
	else{
		//alert(post.msg);
		$("manage").innerHTML=post.msg;
		$('submit_show').show();
		$('submit_hide').hide();
	}

}
//=====================================================================================
function render_cart(){
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=render_cart";
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_manage('cart'),onComplete:finish_rendercart});	
}
function finish_rendercart(req){
	$("cart").innerHTML=req.responseText;
	/*
	post = eval('('+req.responseText+')');
	
	if(post.status==true){
		//alert(post.msg);
		$("manage").innerHTML="Record updated successfully.";
	}
	else{
		//alert(post.msg);
		$("manage").innerHTML="<font color='red'>Error while updating record.</font>";
	}
*/
}
//=====================================================================================
function cancelProduct(orderNo,orderItemSeq,payType,refundAmount,refundToCustId,typeSpecificId,targetUrl,add,remove){
 
	if($('cancelreason').value==''){
		if($('cancel_prod_err')){
			$('cancel_prod_err').show();
		}		 
		return false;
	}else{
		if($('cancel_prod_err')){
			$('cancel_prod_err').hide();
		}	
	}

	var url = host+'/subscription/register/registration_ajax.php';	
	var pars='type=cancel_product';
	pars+='&orderNo='+orderNo;
	pars+='&orderItemSeq='+orderItemSeq;
	pars+='&payType='+payType;
	pars+='&refundAmount='+refundAmount;
	pars+='&refundToCustId='+refundToCustId;
	pars+='&cancelReason='+$('cancelreason').value;
	pars+='&typeSpecificId='+typeSpecificId;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading('cancel'),
	onComplete:function finish_cancelproduct(req)
		{ 
			$('cancel').innerHTML='';

			post = eval('('+req.responseText+')');			

			if(post.status==true){
				if(add!=''){
					var rurl = host+'/subscription/_carthandler.php';
					var rpars = 'action=confirmcart&remove='+remove+'&add='+add; 
					var rpostAjax= new Ajax.Request(rurl, {method: 'post', parameters: rpars, 
					onComplete:function rupdatecart(result)
					{				   
						if(result.responseText!=''){ 
							iboxclose();
							$("yourcart").innerHTML=result.responseText;
							window.location=host+targetUrl;
						}
						
					}});	
					
					
					
				}else{
					window.location=host+'/subscription/register/';
				}
				
			}
			else{
				 
				if(add!=''){
					$('cancel').innerHTML=post.msg;
					var rurl = host+'/subscription/_carthandler.php';
					var rpars = 'action=deletecart&sdefid='+add;
					var rpostAjax= new Ajax.Request(rurl, {method: 'post', parameters: rpars, 
					onComplete:function rupdatecart(result)
					{				   
						if(result.responseText!=''){ 
							iboxclose();
							$("yourcart").innerHTML=result.responseText;
							window.location=host+targetUrl;
						}
						
					}});
						
				}else{
					$('cancel').innerHTML=post.msg;
				}
			}
		
	}});
																							
																							
																							
																							//onComplete:finish_cancelproduct});		
}

//==================================================================================
// add to be added
//remove to be reset auto_renew=0
	//unset typespecificId unset from cart
function editProduct(targetUrl,add,remove,unset){
	 
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars='type=edit_product';
	pars+='&add='+add;
	pars+='&remove='+remove;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onComplete:function finish_editproduct(req)
		{ 
			post = eval('('+req.responseText+')');
			if(post.status==true){
				if(add!=''){
					var rurl = host+'/subscription/_carthandler.php';
					var rpars = 'action=confirmcart&remove='+remove+'&add='+add+'&startdate='+post.msg+'&unset='+unset; //alert(rpars);
					var rpostAjax= new Ajax.Request(rurl, {method: 'post', parameters: rpars, 
					onComplete:function rupdatecart(result)
					{				   
						if(result.responseText!=''){ 
							iboxclose();
							$("yourcart").innerHTML=result.responseText;
							//window.location=host+targetUrl;
						}
						
					}});	
					
					
					
				}else{
					window.location=host+'/subscription/register/';
				}
				
			}
			else{
				 
				if(add!=''){
					$('cancel').innerHTML=post.msg;
					var rurl = host+'/subscription/_carthandler.php';
					var rpars = 'action=deletecart&sdefid='+add;
					var rpostAjax= new Ajax.Request(rurl, {method: 'post', parameters: rpars, 
					onComplete:function rupdatecart(result)
					{				   
						if(result.responseText!=''){ 
							iboxclose();
							$("yourcart").innerHTML=result.responseText;
							window.location=host+targetUrl;
						}
						
					}});
						
				}else{
					$('cancel').innerHTML=post.msg;
				}
			}
		
	}}); 
																							
}
//==================================================================================
function loading(id){
	$(id).innerHTML="<font color=green>Loading....</font>"; 	 
		 
}
function loading_manage(id){
	$("regmovingdiv").style.display='block';
	$(id).innerHTML="We are processing your request. Please wait...."; 
}

/***** Aswini ****/
function checkMonthCvvcheck(errorDiv,fieldid)
{
	var flag=true;
	if($F(fieldid)==''){$(errorDiv).innerHTML="Expiration Date Is Blank";$(fieldid).select();flag=false;
	return false;}

	var month = $F(fieldid);
	if((month.search('/')==-1)||(month.length!=7)){
		$(errorDiv).innerHTML="Invalid Format";
		$(fieldid).select();
		flag=false;
	}
	var result=month.split('/');

	if(isNaN(parseInt(result[0]))){$(errorDiv).innerHTML="Invalid Month";$(fieldid).select();flag=false;}

	else if( (parseInt(result[0])<0) || (parseInt(result[0])>12)){$(errorDiv).innerHTML="Invalid Month";$(fieldid).select();flag=false;}

	else if(isNaN(parseInt(result[1]))){$(errorDiv).innerHTML="Invalid Year";$(fieldid).select();flag=false;}

	else if(parseInt(result[1])<0){$(errorDiv).innerHTML="Invalid Year";$(fieldid).select();flag=false;}

	if(flag){$(errorDiv).innerHTML="";

	/*if((isNaN($F('cvvnum'))) ||($F('iboxcvvnum')=='')){$(errorDiv).innerHTML="Invalid CVV Number";$('iboxcvvnum').select();flag=false;}*/

	}
	
	return flag;
}
/***** Aswini ****/
function iboxisValidPasswordRegistration(errorDiv,paswdFldId)
{ 
	var string = $F(paswdFldId);
   	var bValid =new Boolean(true);
	if (!string) 
	{
		$(errorDiv).innerHTML='Password field can not be left blank.';
		$(paswdFldId).select();
		bValid=false; 
	}
	/*
	else if(string.length >15 || string.length <6) 
	{
		$(errorDiv).innerHTML="Password can't be less than 6 or greater then 15 characters.";
		bValid=false;
	}
	*/
     else
	{
		var Chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for (var i = 0; i < string.length; i++) {
		if (Chars.indexOf(string.charAt(i)) == -1)
		   {
				  bValid=false; 
			}
		}
		if(!bValid)
		{
			var msg='Not a valid Password'
			$(errorDiv).innerHTML=msg;
			$(paswdFldId).select();
		}
	} 
    return bValid;
}

function timedOut()
{
	//$("regmovingdiv").style.display='block';
	var t=setTimeout('$("regmovingdiv").style.display="none"',5000);
}


function product_registration(){
	var errordiv="regerror";
	var status=true;
	var divmsg=0;
	$('errordivname').innerHTML="";
	$('errordivlastname').innerHTML="";
	$('errordivemail').innerHTML="";
	$('errordivpassword').innerHTML="";
	$('errordivrepassword').innerHTML="";
	$('errordivtermuse').innerHTML="";
	$('reg_error').innerHTML="";
	if($('firstname').value==''){
		$('errordivname').innerHTML='Enter First name.';
		$('firstname').select();
		divmsg=1;
		// return false;
	}

	if(validateName('errordivname','firstname','First name')==false){
		$('errordivname').innerHTML='Please enter a valid First Name.';
		$('firstname').select();
		divmsg=1;
		// return false;
	}

	if($('lastname').value==''){
		$('errordivlastname').innerHTML='Enter Last name.';
		$('lastname').select();
		divmsg=1;
		// return false;
	}
	if(validateName('errordivlastname','lastname','Last name')==false){
		$('errordivlastname').innerHTML='Please enter a valid Last Name.';
		$('lastname').select();
		divmsg=1;
		// return false;
	}
	if(iboxisValidEmail('errordivemail','viauserid')==false){
		$('errordivemail').innerHTML='Please enter a valid Email.';
		$('viauserid').select();
		divmsg=1;
		// return false;
	}
	if(iboxisValidPasswordRegistration('errordivpassword','viapass')==false){
		$('errordivpassword').innerHTML='Please enter a valid Password.';
		$('viapass').select();
		divmsg=1;
		// return false;
	}
	if($('viapass').value!=$('viarepass').value){
		$('errordivrepassword').innerHTML='Password and Confirm password does not match.';		
		$('viarepass').select();
		divmsg=1;
		// return false;
	}
	if($("viapass").value.toUpperCase()=='PASSWORD'){
		$('errordivpassword').innerHTML="Please Enter Any other Password Except 'Password'";
		$('viarepass').select();
		divmsg=1;
		// return false;
	}	
	if(!$('terms').checked)
	{
		$('errordivtermuse').innerHTML="Please accept terms of use.";
		$('terms').focus();
		divmsg=1;
		// return false;
	}
	if(divmsg=="1"){
		return false;
	}
	else
	{
		$('reg_next_en').hide();
		$('reg_next_ds').show();
	}
	start_registration()
	
}
function start_registration(){	
   var country="NY";	
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=register";
	pars+='&uid='+$('viauserid').value;
	pars+='&pwd='+$('viapass').value;
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;	
	pars+='&country='+country;
	if($("alerts").checked==true){
		pars+="&dailydigest=1";
	}else{
		pars+="&dailydigest=0";
	}
	if($("dailyfeed").checked==true){
		pars+="&dailyfeed=1";
	}
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_registration('reg_error'),
	onComplete:finish_userregistration});
}

function finish_userregistration(req){
	post = eval('('+req.responseText+')');
	if(post.status==true){
	  create_account();
    }
	else
	{
		
		$('reg_error').innerHTML='';
		$('reg_error').innerHTML=post.msg;
		$('reg_next_en').show();
		$('reg_next_ds').hide();
	}
}
function loading_registration(id){
	$(id).style.display='block';
	$(id).innerHTML="We are processing your request. Please wait...."; 
}

function create_account()
{
	
	if($('viauserid').value == "" && $('email') == "")
	{
		return;
	}
	var url = host+'/subscription/register/createAccount_mod.php';	
	var pars="";
	var remember = 0;
	var alerts = 0;
	var terms = 0;
	var dailyfeed=0;
	pars+='&viauserid='+$('viauserid').value;	
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;	
	if($('viapass'))
	{
		pars+='&viapass='+$('viapass').value;
		if($('viauserremember').checked == true)
		{
			remember = 1;
		}
		if($('alerts').checked == true)
		{
			alerts = 1;
		}
		if($('dailyfeed').checked == true)
		{
			dailyfeed = 1;
		}
		if($('terms').checked == true)
		{
			terms = 1;
		}
		pars+='&remember_me='+remember;	
		pars+='&alerts='+alerts;
		pars+='&dailyfeed='+dailyfeed;
		pars+='&terms='+terms;	
	}
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onComplete:finish_create_account});
}
function finish_create_account()
{
	window.location.href=host+"/subscription/register/billing/";
}


function billing_validate()
{
	var billingdiv=0;
	$('errordivaddress').innerHTML="";
	$('errordivcity').innerHTML="";
	$('errordivzip').innerHTML="";
	$('errordivcountry').innerHTML="";
	$('errordivstate').innerHTML="";
	$('errordivphone').innerHTML="";
	$('errordivcctype').innerHTML="";
	$('errordivccnum').innerHTML="";
	$('errordivccexpire').innerHTML="";
	$('errordivcvvnum').innerHTML="";
	
	if(trim($('address1').value)==''){
		$('errordivaddress').innerHTML='Enter address.';
		$('address1').select();
		billingdiv=1;
		//return false;
	}
	if(trim($('city').value)==''){
		$('errordivcity').innerHTML='Enter city.';
		$('city').select();
		billingdiv=1;
		//return false;
	} 	
	if($('zipcode').value==''){
		$('errordivzip').innerHTML='Enter zipcode.';
		$('zipcode').select();
		billingdiv=1;
		//return false;
	}
	if(validateZipcode($('zipcode').value)==false){
		$('errordivzip').innerHTML='Invalid zipcode';
		$('zipcode').select();
		billingdiv=1;
		//return false;
	}	
	if($('country').value==''){
		$('errordivcountry').innerHTML='Select country.';
		$('country').select();
		billingdiv=1;
		//return false;
	}		
	if(($('country').value=='AA' || $('country').value=='AB') && $('state').value==''){
		$('errordivstate').innerHTML='Select state.';
		$('state').select();
		billingdiv=1;
		//return false;
	}	
	if(trim($('phone').value)==''){
		$('errordivphone').innerHTML='Enter phone.';
		$('phone').select();
		billingdiv=1;
		//return false;
	}
	if($('cctype').value==''){
		$('errordivcctype').innerHTML='Select credit card type.';
		$('cctype').select();
		billingdiv=1;
		//return false;
	}
	if($('ccnum').value==''){
			$('errordivccnum').innerHTML='Enter credit card number.';
			$('ccnum').select();
			billingdiv=1;
			//return false;
	}
	if($('year').value=='' || $('month').value==''){
			$('errordivccexpire').innerHTML='Enter credit card expiry date.';		
			billingdiv=1;
			//return false;
	}	
	var month = $('month').value;
	if($('month').value.length == 1)
	{
		month = "0"+$('month').value
	}
	var date = month+"-"+$('year').value;
	if(validateExDate(date,'errordivccexpire')==false){
			$('ccexpire').select();
			billingdiv=1;
			//return false;
	}	 
	if($('hidCNUM').value != "" && $('ccnum').value == $('hidCNUM').value)
	{
		// User is existing user and already have added Credit card detail
	}
	else
	{
		var CCnumber = trim($('ccnum').value);
		var CCstr = CCnumber.substr(0,12); 		 
		var CCstrnum  = CCnumber.substr(12,16); 
		var objRegExp  = /^(\*){12}/; 
		if(objRegExp.test(CCstr))
		{		 		
			if(isNaN(CCstrnum) && CCstrnum.length==4)
			{	 
					$('errordivccnum').innerHTML=ccErrors['2'];//'Credit card number is in invalid format';
					$('ccnum').select();
					billingdiv=1;
					//return false;
			}	 		
		}
	
		if($('cvvnum').value==''){
		$('errordivcvvnum').innerHTML='Enter CVV number.';
		$('cvvnum').select();
		billingdiv=1;
		//return false;
		}		
		if(!checkCreditCard ($('ccnum').value, $('cctype').value))
		{
			if(ccErrorNo!=0 ){
				$('errordivccnum').innerHTML=ccErrors[ccErrorNo];
				$('ccnum').select();
			}
			billingdiv=1;
			//return false;
		}
	}
	if(validateNumField('cvvnum','errordivcvvnum')==false){
		$('cvvnum').select();
		billingdiv=1;		
	}
		
	if(billingdiv=="1"){
		return false;
	}
	else
	{
		$('bill_next_en').hide();
		$('bill_next_ds').show();
	}
	add_billing_info()
}
function validateExDate(date,errordivid){
	 
	var result=date.split('-');
		
	if(result[0].length!=2 || result[1].length!=4 ){
		$(errordivid).innerHTML="Invalid Expire Date";
		//$(divid).select();
		return false;	
	}
	var myDate = new Date();//|| trim(parseInt(result[1])) < curYear)
	var curYear = parseInt(myDate.getFullYear()); 
	if(isNaN(parseInt(result[0]), 10) || isNaN(parseInt(result[1]), 10)  || parseInt(result[0], 10)>12  
		|| parseInt(result[1])<curYear ){
		$(errordivid).innerHTML="Invalid Expire Date";
		//$(divid).select();
		return false;
	} 
    var curMonth = LZ(myDate.getMonth()+1) ;//alert(parseInt(result[0]));alert(parseInt(curMonth));
	if(parseInt(result[1])== curYear && parseInt(result[0], 10) < parseInt(curMonth) ){
		$(errordivid).innerHTML="Invalid Expire Date";
		//$(divid).select();
		return false;
	}
}
function add_billing_info()
{
	var url = host+'/subscription/register/billing_mod.php';	
	var pars="";
	//var email_deals = 0;
	//var policy = 0;
	var month = $('month').value
	if($('month').value.length == 1)
	{
		month = "0"+$('month').value
	}
	pars+='&session_id='+$('session_id').value;	
	pars+='&email='+$('email').value;	
	pars+='&firstname='+$('firstname').value;	
	pars+='&lastname='+$('lastname').value;	
	pars+='&product_id='+$('product_id').value;	
	pars+='&address1='+encodeURIComponent($('address1').value);	
	pars+='&address2='+encodeURIComponent($('address2').value);
	pars+='&city='+$('city').value;	
	pars+='&state='+$('state').value;	
	pars+='&zipcode='+$('zipcode').value;
	pars+='&country='+$('country').value;
	pars+='&phone='+$('phone').value;
	pars+='&cctype='+$('cctype').value;
	pars+='&ccnum='+$('ccnum').value;
	pars+='&month='+month;
	pars+='&year='+$('year').value;	
	pars+='&cvvnum='+$('cvvnum').value;
	/*if($('email_deals').checked == true)
	{
		email_deals = 1;
	}
	if($('policy').checked == true)
	{
		policy = 1;
	}
	pars+='&email_deals='+email_deals;
	pars+='&policy='+policy;*/
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars, onLoading:loading_registration('billingerror'),
	onComplete:finish_billing_info});
}
function finish_billing_info(req)
{
	var post = eval('('+req.responseText+')');
	if(post.status==true)
{
	window.location.href=host+"/subscription/register/submit/";	
}
	else
	{
		$('billingerror').innerHTML=post.msg;	
		$('bill_next_en').show();
		$('bill_next_ds').hide();
	}
}
function final_registration()
{	
	if($('address1').value == "" || $('city').value == "" || $('state').value == "" ||
		$('zipcode').value == "" || $('country').value == "" || $('phone').value == "" ||
		$('cctype').value == "" || $('ccnum').value == "" || $('ccexpire').value == "" || 
		($('hidCNUM').value == "" && $('cvvnum').value == "") ||
		($('ccnum').value != $('hidCNUM').value && $('cvvnum').value == "")
		)
	{
		$('regerror').innerHTML='Please enter complete billing information in Step 2';
		return false;
	}
	else if($("terms").checked==false){
		$('regerror').innerHTML="Please accept terms of use.";		
		$('terms').select();
		return false;
	}else if($("hidSubCount").value=="0"){
		$('regerror').innerHTML="Please add a product in your cart.";						
		return false;
	}else{
		$('final_next_en').hide();
		$('final_next_ds').show();
	}
	var url=host+'/subscription/register/registration_ajax.php';
	var pars='&type=checkccstatus';	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_registration('regerror'),onComplete: 
	submit_final_registration});	
	
}
function setGATracking(){
	var url = host+'/subscription/_carthandler.php';
	var pars = '&action=submitcart';
	var postAjax= new Ajax.Request(url, {method: 'post',
		parameters: pars,onComplete:function sumitcartaction(req){
			var strval=req.responseText;
			var strvalur=strval.split(',');
			var strResponse =strvalur[0];
			var arrlen=strvalur.length;			
		}
	});	

}
function submit_final_registration(req)
{
				
			var ccDetails=parseInt(req.responseText);			
			if(ccDetails == 1)
			{
				setGATracking()
				var pars="type=manage_settings";	
			}
			else
			{
				/*if cart is empty redirect user to subscription page*/
				// window.location.href=host+"/subscription/"; 
				var pars="type=update_customer_order";
			}
			var url = host+'/subscription/register/registration_ajax.php';
			
			pars+='&uid='+$('uid').value;
			pars+='&uid_default='+$('uid_default').value;
			pars+='&firstname='+$('firstname').value;
			pars+='&lastname='+$('lastname').value;	
			pars+='&address1='+encodeURIComponent($('address1').value);
			pars+='&address2='+encodeURIComponent($('address2').value);
			pars+='&city='+$('city').value;
			pars+='&state='+$('state').value;	
			pars+='&zipcode='+$('zipcode').value;
			pars+='&country='+$('country').value;	
			pars+='&phone='+$('phone').value;
			pars+='&cctype='+$('cctype').value;
			pars+='&ccnum='+$('ccnum').value;
			pars+='&ccexpire='+$('ccexpire').value;
			pars+='&cvvnum='+$('cvvnum').value;
			if($('ccnum').value != $('hidCNUM').value)
			{
				updateCCStatus=true;
				pars+="&updateccnum="+$("ccnum").value;
			}
			if($('ccexpire').value != $('hidCEXP').value)
			{
				updateCCStatus=true;
				pars+="&updateccexpire="+$('ccexpire').value;
			}			
			var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_registration('regerror'),
			onComplete:finish_final_registration});
}
function finish_final_registration(req)
{
	
	var post = eval('('+req.responseText+')');		
	if(post.status==true)
	{
		var url = host+'/subscription/register/final_step.php';
		var pars = "";
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
		onComplete:function () {window.location.href=host+"/subscription/register/welcome.htm"; }
		});	
	}
	else
	{
		$('regerror').innerHTML=post.msg;	
		$('final_next_en').show();
		$('final_next_ds').hide();
	}
	
}
function remove_product(id, orderItemType){	

	var url = host+'/subscription/_carthandler.php';
	var pars = 'action=deletecart&sdefid='+id+'&orderItemType='+orderItemType;
	var postAjax= new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onComplete:function (req)
		{		
			if(req.responseText==""){
							window.location.href=host+"/subscription/";
			}else{
				window.location.reload();
			}
	}});	
	 
}
function login_reg(action)
{
	if(action == 'login')
	{
		$('existing_user').show();
		$('new_user').hide();
	}
	else
	{
		$('new_user').show();
		$('existing_user').hide();
	}
}
function userLogin(from)
{
	var errordiv="loginerror";
	var email = $('email').value;
	var password = $('password').value;
	var errdiv=0;
	$('loginerrorname').innerHTML="";
	$('loginerrorpassword').innerHTML="";
	if(email==''){
		$('loginerrorname').innerHTML='Please enter your Email.';
		$('email').select();
		errdiv=1;
		// return false;
	}
	if(password==''){
		$('loginerrorpassword').innerHTML='Please enter your Password.';
		$('password').select();
		errdiv=1;
		// return false;
	}
	if(errdiv=="1"){
		return false;
	}
	else
	{
		$('login_next_en').hide();
		$('login_next_ds').show();
	}
	var url = host+'/subscription/register/iboxLoginAjax.php';
	
	var auto =0;
	if($('viauserremember').checked == true) auto=1;
	var pars="type=login&login="+email+"&pwd="+encodeURIComponent(password)+'&autologin='+auto;

	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onLoading:loading_registration('loginerror'),		
		onComplete:function getLoginStatus(req)
		{
		var post = eval('('+req.responseText+')');		
		if(post.status==true)
		{		   	
			
			if($('purchase_from') && $('purchase_from').value == 'housing_market')
			{
				window.location.href=""; 
			}
			else
			{
			logAddtoCart(host+'/subscription/register/billing/');			
		}
		}
		else
		{
			if(post.msg=='Inactive account'){
				location.replace( host + "/subscription/activate.htm");
			}else{
				$(errordiv).innerHTML = post.msg;
				$('login_next_ds').hide();
				$('login_next_en').show();
				return false;
			}
		}
	}});
}

function updateCartRadio(subdefId, orderItemType, oc_id, productName,pageName){
	if(checkProdArr(subdefId)){
		return false;
	}
	var url = host+'/subscription/_carthandler.php';
	var pars="action=updateCart&subdefId="+subdefId+"&orderItemType="+orderItemType+"&oc_id="+oc_id+"&pageName="+pageName;
	var myAjax = new Ajax.Request(url,{ 
		method: 'post', 
		parameters: pars,
		onLoading:loadingProduct('productdiv'),
		onComplete: function(req) 
		{
			if($('productdiv').value != ""){
				$('productdiv').style.display='block';
				$('productdiv').innerHTML="&nbsp;";
				
			}
			rendervalidatedcart(req); 
			if($('removeId').value){
				var replaceId= $('removeId').value;
			}
			updateProductArray(subdefArr,subdefId,replaceId);
		} 
	});
}
	
function loadingProduct(id){
	$(id).style.display='block';
	$(id).innerHTML="Loading....";
}

function updateProductArray(arrName,addId,removeId){
	for(var i=0;i<arrName.length;i++){
		if(arrName[i]==removeId){
			arrName.splice(i,1,addId); 
		}
	}
}

function checkProdArr(subdefId){
	for(var i=0;i<subdefArr.length;i++){
		if(subdefArr[i]==subdefId){
			return true;
		}
	}
}function validateEmail(email)
{
	if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(email)))
	{
		return false
	}
	return true;
}
function housingMarketFormValidation()
{
		var error=0;
		$('errordivname').innerHTML="";
		$('errordivlastname').innerHTML="";
		$('errordivemail').innerHTML="";
		$('errordivaddress').innerHTML="";
		$('errordivcity').innerHTML="";
		$('errordivzip').innerHTML="";
		$('errordivcountry').innerHTML="";
		$('errordivstate').innerHTML="";
		$('errordivphone').innerHTML="";
		$('errordivcctype').innerHTML="";
		$('errordivccnum').innerHTML="";
		$('errordivccexpire').innerHTML="";
		$('errordivcvvnum').innerHTML="";					
		$('errordivtermuse').innerHTML="";
		
		if($('firstname').value==''){
			$('errordivname').innerHTML='Enter First name.';
			$('firstname').select();
			error=1;			
		}
		if(validateName('errordivname','firstname','First name')==false){
			$('errordivname').innerHTML='Please enter a valid First Name.';
			$('firstname').select();
			error=1;			
		}

		if($('lastname').value==''){
			$('errordivlastname').innerHTML='Enter Last name.';
			$('lastname').select();
			error=1;			
		}
		if(validateName('errordivlastname','lastname','Last name')==false){
			$('errordivlastname').innerHTML='Please enter a valid Last Name.';
			$('lastname').select();
			error=1;			
		}
		if(validateEmail($('viauserid').value)==false){
			$('errordivemail').innerHTML='Please enter a valid Email.';
			$('viauserid').select();
			error=1;
			// return false;
		}						
		if(trim($('address1').value)==''){
			$('errordivaddress').innerHTML='Enter address.';
			$('address1').select();
			error=1;
		}
		if(trim($('city').value)==''){
			$('errordivcity').innerHTML='Enter city.';
			$('city').select();
			error=1;
			//return false;
		} 	
		if($('zipcode').value==''){
			$('errordivzip').innerHTML='Enter zipcode.';
			$('zipcode').select();
			error=1;
			//return false;
		}
		if(validateZipcode($('zipcode').value)==false){
			$('errordivzip').innerHTML='Invalid zipcode';
			$('zipcode').select();
			error=1;
			//return false;
		}	
		if($('country').value==''){
			$('errordivcountry').innerHTML='Select country.';
			$('country').select();
			error=1;
			//return false;
		}		
		if(($('country').value=='AA' || $('country').value=='AB') && $('state').value==''){
			$('errordivstate').innerHTML='Select state.';
			$('state').select();
			error=1;
			//return false;
		}	
		if(trim($('phone').value)==''){
			$('errordivphone').innerHTML='Enter phone.';
			$('phone').select();
			error=1;
			//return false;
		}
		if($('cctype').value==''){
			$('errordivcctype').innerHTML='Select credit card type.';
			$('cctype').select();
			error=1;
			//return false;
		}
		if($('ccnum').value==''){
				$('errordivccnum').innerHTML='Enter credit card number.';
				$('ccnum').select();
				error=1;
				//return false;
		}
		if($('year').value=='' || $('month').value==''){
				$('errordivccexpire').innerHTML='Enter credit card expiry date.';		
				error=1;
				//return false;
		}	
		var month = $('month').value;
		if($('month').value.length == 1)
		{
			month = "0"+$('month').value
		}
		var date = month+"-"+$('year').value;
		if(validateExDate(date,'errordivccexpire')==false){				
				error=1;
				//return false;
		}	 
		if($('hidCNUM').value != "" && $('ccnum').value == $('hidCNUM').value)
		{
			// User is existing user and already have added Credit card detail
		}
		else
		{
			var CCnumber = trim($('ccnum').value);
			var CCstr = CCnumber.substr(0,12); 		 
			var CCstrnum  = CCnumber.substr(12,16); 
			var objRegExp  = /^(\*){12}/; 
			if(objRegExp.test(CCstr))
			{		 		
				if(isNaN(CCstrnum) && CCstrnum.length==4)
				{	 
						$('errordivccnum').innerHTML=ccErrors['2'];//'Credit card number is in invalid format';
						$('ccnum').select();
						error=1;
				}	 		
			}
		
			if($('cvvnum').value==''){
			$('errordivcvvnum').innerHTML='Enter CVV number.';
			$('cvvnum').select();
			error=1;
			}		
			if(!checkCreditCard ($('ccnum').value, $('cctype').value))
			{
				if(ccErrorNo!=0 ){
					$('errordivccnum').innerHTML=ccErrors[ccErrorNo];
					$('ccnum').select();
				}
				error=1;				
			}
		}
		if(validateNumField('cvvnum','errordivcvvnum')==false){
			$('cvvnum').select();
			error=1;		
		}		
		if(!$('terms').checked)
		{
			$('errordivtermuse').innerHTML="Please accept terms of use.";
			$('terms').focus();
			error=1;			
		}
		return error;
}
function housingMarketRegistration()
{	
	var error = housingMarketFormValidation();
	if(!error)
	{
		//$('final_next_en').hide();
		//$('final_next_ds').show();
		var month = $('month').value
		if($('month').value.length == 1)
		{
			month = "0"+$('month').value
		}
		var pars="type=purchase_housing_report";
		pars+='&uid='+$('viauserid').value;		
		pars+='&pwd=12';
		pars+='&firstname='+$('firstname').value;
		pars+='&lastname='+$('lastname').value;	
		pars+='&address1='+encodeURIComponent($('address1').value);
		pars+='&address2='+encodeURIComponent($('address2').value);
		pars+='&city='+$('city').value;
		pars+='&state='+$('state').value;	
		pars+='&zipcode='+$('zipcode').value;
		pars+='&country='+$('country').value;	
		pars+='&phone='+$('phone').value;
		pars+='&cctype='+$('cctype').value;
		pars+='&ccnum='+$('ccnum').value;
		pars+='&ccexpire='+$('year').value+'-'+month;
		pars+='&cvvnum='+$('cvvnum').value;			
		if($('ccnum').value != $('hidCNUM').value)
		{
			updateCCStatus=true;
			pars+="&updateccnum="+$("ccnum").value;
		}
		var url=host+'/subscription/register/registration_ajax.php';
		var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:loading_registration('reg_error'),
		onComplete:function(req){
				var post = eval('('+req.responseText+')');		
				if(post.status==true)
				{
					window.location.href=host+"/subscription/register/welcome.htm";						
				}
				else
				{
					$('reg_error').innerHTML=post.msg;					
					//$('final_next_en').show();
					//$('final_next_ds').hide();
				}
			}
		});		
	}
	
}