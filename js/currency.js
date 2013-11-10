host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;

// execute if enter key is pressed
function get_currency_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		validateSoftTrial();
	}
}

// trim a string
function softTrialTrim(str){	
		
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
		
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
		
	return str;
}

// allow only numbers
function validateSoftTrialNumbers(value) {

	var string = softTrialTrim(value);
    var bVal = true;
	if (!string){	
		bVal = false;
	}
	
    var Chars = "0123456789";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1){
		     bVal = false; 
		}
    }
	
	return bVal;
	
}

// allow only characters
function validateSoftTrialChars(value) {

	var string = softTrialTrim(value);
    var bVal = true;
	if (!string){	
		bVal = false;
	}
	
    var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1){
		     bVal = false; 
		}
    }
	
	return bVal;
	
} 	

// validate email address
function softTrialValidEmail(errorDiv,emailFieldId){
	var bools=true;
	var emails=$(emailFieldId).value.split(';');

	for (var i=0;i<emails.length;i++){
	
		if(emails[i]==''){
			$(errorDiv).innerHTML='Please enter email address.'; 
			$(emailFieldId).select();
			bools=false;
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i]))){
			var erormsg = 'Please use a valid email address.';
			$(errorDiv).innerHTML=erormsg;
			$(emailFieldId).select();
			bools=false;
			return false;
		}
	}
	$(errorDiv).innerHTML='&nbsp;';
	return bools;
}	

// validate soft trials user inputs
// subscription will be name of subscription 
// buzz/cooper/flexfolio/optionsmith/jacklavery
function validateSoftTrial(){
	
	var url='';
	var pars='';
	
	
	if(softTrialTrim($('fname').value)==''){
		$('msgdiv').innerHTML='Please enter first name.';
		$('fname').select();
		return false;		
	}
	
	if(validateSoftTrialChars(softTrialTrim($('fname').value))==false){
		$('msgdiv').innerHTML='Please enter valid first name.';
		$('fname').select();
		return false;		
	}
	
	if(softTrialTrim($('lname').value)==''){
		$('msgdiv').innerHTML='Please enter last name.';
		$('lname').select();
		return false;		
	}	
	
	if(validateSoftTrialChars(softTrialTrim($('lname').value))==false){
		$('msgdiv').innerHTML='Please enter valid last name.';
		$('lname').select();
		return false;		
	}	
	if(softTrialValidEmail('msgdiv','uid')==false){
		$('uid').select();
		return false;
	}
	if(softTrialTrim($('zipcode').value)==''){
		$('msgdiv').innerHTML='Please enter zip code.';
		$('zipcode').select();
		return false;		
	}
	
	if(softTrialTrim($('serialnumber').value)==''){
		$('msgdiv').innerHTML='Please enter serial number.';
		$('serialnumber').select();
		return false;		
	}	
	

	
	$('msgdiv').innerHTML='Loading...';
	/*
	// check if zipcode record exists in geo_location table
	url=host+'/getpositivemoney/currencycontroller.php';
	pars = 'type=checkzipcode';
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
			post = eval('('+req.responseText+')');
			if(post.status==false){
				
				url=host+'/getpositivemoney/getLonLatFromZip.php';
				pars='';
				pars =  'zipcode='+$('zipcode').value;
				
				var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
			alert('responseText:'+req.responseText);	
			//alert('responseXML:'+req.responseXML.xml);
		
		}
		});		
			}
		
		}
	});
	return false;
	*/
	url=host+'/getpositivemoney/currencycontroller.php';
	
	pars = pars+'type=doregister';
	pars = pars+'&firstname='+softTrialTrim($('fname').value);
	pars = pars+'&lastname='+softTrialTrim($('lname').value);	
	pars = pars+'&uid='+softTrialTrim($('uid').value);
	pars = pars+'&zipcode='+softTrialTrim($('zipcode').value);
	pars = pars+'&serialnumber='+softTrialTrim($('serialnumber').value);
	
	if($('register').checked==true){
		pars = pars+'&register=1';
	}
	else{
		pars = pars+'&register=0';
	}
	
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
		post = eval('('+req.responseText+')');
		if(post.status==false){
			if(softTrialTrim(post.msg)!='Please use valid zip code.'){
				if(post.luckystatus==true){
					window.location=host+'/getpositivemoney/congratulations.htm?err=1';
				}
				else{
					window.location=host+'/getpositivemoney/sorry.htm?err=1';
				}
			}
			else{
				$('msgdiv').innerHTML=softTrialTrim(post.msg);		
			}
			return false;
		}
		else{
			//successful registration
			if(post.luckystatus==true){
				window.location=host+'/getpositivemoney/congratulations.htm';
			}
			else{
				window.location=host+'/getpositivemoney/sorry.htm';
			}
		}
	}//end of ajax request
	});
	
}
function signup(){
	$('fname').focus();
}