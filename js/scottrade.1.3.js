 
function get_register_key_Scott(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		validateScottBuzz('statusmsgstep1');
	}
}

function validateEmail(email, errorDiv,status){ 
	var bools=true;	
	if(status.length==0){
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(email)))
		{
			var erormsg = '<font color="red">Invalid URL</font>';
			$(errorDiv).style.display="block";
			$(errorDiv).innerHTML=erormsg;			
			bools=false;
			return false;
		
		}	else{
			$(errorDiv).style.display="block";
			return true;
		}
	}
	return bools;

}
	function validateScottBuzz(errordiv){
	var status=true;
	if($('firstname').value==''){
		$(errordiv).innerHTML='Enter First name.';
		
		$('firstname').select(); 
		return false; 
	}
	if(validateAlphaFieldsOnly(errordiv,'firstname','First name')==false){
		
		$('firstname').select();
		return false;
	}

	if($('lastname').value==''){
		$(errordiv).innerHTML='Enter Last name.';
		
		$('lastname').select();
		return false;
	}

	if(validateAlphaFieldsOnly(errordiv,'lastname','Last name')==false){
		
		$('lastname').select();
		return false;
	}	

	if(iboxisValidEmail(errordiv,'viauserid')==false){
		
		$(errordiv).innerHTML='Invalid URL.';
		//$('viauserid').select();
		return false;
	}
	if(iboxisValidPasswordRegistration(errordiv,'viapass')==false){
		
		$('viapass').select();
		return false;
	}
	 
	/***	if($('viapass').value!=$('viarepass').value){
		$(errordiv).innerHTML='Password and Confirm password does not match.';		
		
		$('viarepass').select();
		return false;
	} ***/
	if($("viapass").value.toUpperCase()=='PASSWORD'){
		$(errordiv).innerHTML="Please Enter Any other Password Except 'Password'";
		
		$('viapass').select();
		return false;
	}	
	/*if($("terms").checked==false){
		$(errordiv).innerHTML="You did not agree with our privacy terms.";
		
		$('terms').select();
		return false;
	}*/
	buzzScottregistration();
}
function buzzScottregistration(){
	//checkcart('41,'9','SUBSCRIPTION');
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=scottBuzzRegister";
	pars+='&uid='+$('viauserid').value;
	pars+='&pwd='+$('viapass').value;
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;
	pars+="&rememeber=1";
	/**if($('viauserremember').checked==true){
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
	}***/
	/*
	if($('referralcode').value!=''){
		pars+='&refcode='+$('referralcode').value;
	}
	*/
	$('statusmsgstep1').innerHTML='';
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onLoading:$('statusmsgstep1').innerHTML="Loading...",
	onComplete:function(req)
	{
		var post = eval('('+req.responseText+')');
		if(post.status==false)$('statusmsgstep1').innerHTML=post.msg;
		else 
		{
		//$('statusmsgstep1').innerHTML='You have sucessfully subscribed Buzz Banter.';
		//Effect.SlideUp('main_box', { duration: 2.0 });
		//$("launch_buzz").show();
		window.location.href=host+'/subscription/scottrade?success=true';
		}
	}});
}function softtrials_registration(){
	//checkcart('41,'9','SUBSCRIPTION');
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=softtrials_register";
	pars+='&uid='+$('viauserid').value;
	pars+='&pwd='+$('viapass').value;
	pars+='&firstname='+$('firstname').value;
	pars+='&lastname='+$('lastname').value;
	pars+='&buzzST='+$('BuzzST').checked;
	pars+='&cooperST='+$('CooperST').checked;
	pars+='&optionST='+$('OptionST').checked;
	pars+='&flexfolioST='+$('FlexFolioST').checked;
	pars+='&jackST='+$('JackST').checked;
	
	if(trim($('promocode').value)!=''){
		pars+='&promocode='+$('promocode').value;
	}
	pars+="&rememeber=1";

	$('statusmsgstep1').innerHTML='';
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onLoading:$('statusmsgstep1').innerHTML="Loading...",
	onComplete:function(req)
	{
		var post = eval('('+req.responseText+')');
		if(post.status==false)$('statusmsgstep1').innerHTML=post.msg;
		else 
		{
			var url = host+'/subscription/register/registration_ajax.php';
			var parms_email = 'type=softtrials_emails&email='+post.email+'&pd='+post.pd+'&name='+post.firstname+'&buzz='+post.buzz+'&flex='+post.flex+'&cooper='+post.cooper+'&optionsmith='+post.optionsmith+'&jack='+post.jack; 
			var myAjax5 = new Ajax.Request(url, {method: 'post', parameters: parms_email,
			onLoading:$('statusmsgstep1').innerHTML="Loading...",
			onComplete:function(req_email)
		{	
			$('statusmsgstep1').innerHTML='';
			window.location.href=host+'/subscription/softtrials/welcome.htm?a=1';
		
		}});
		}
	}});
}
function validate_softtrials(errordiv){
	var status=true;
	$(errordiv).innerHTML='';
	if($('firstname').value==''){
		$(errordiv).innerHTML='Enter First name.';
		
		$('firstname').select(); 
		return false; 
	}
	if(validateAlphaFieldsOnly(errordiv,'firstname','First name')==false){
		
		$('firstname').select();
		return false;
	}

	if($('lastname').value==''){
		$(errordiv).innerHTML='Enter Last name.';
		
		$('lastname').select();
		return false;
	}

	if(validateAlphaFieldsOnly(errordiv,'lastname','Last name')==false){
		
		$('lastname').select();
		return false;
	}
	if(iboxisValidEmail(errordiv,'viauserid')==false){
		$(errordiv).innerHTML='Invalid Email Id.';
		
		$('viauserid').select();
		return false;
	}
	if(iboxisValidPasswordRegistration(errordiv,'viapass')==false){
		
		$('viapass').select();
		return false;
	}
	 

	if($("viapass").value.toUpperCase()=='PASSWORD'){
		$(errordiv).innerHTML="Please Enter Any other Password Except 'Password'";
		
		$('viapass').select();
		return false;
	}
	
	softtrials_registration();
}