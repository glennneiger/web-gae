var fieldValidate=false;
var isValidCC=false;
var mothCVV=false;

function iboxisValidEmail(errorDiv,emailFieldId)
{
	var bools=true;
	var emails=$(emailFieldId).value.split(';');

	for (var i=0;i<emails.length;i++)
	{
		if(emails[i]=='')
		{
			$(errorDiv).innerHTML='E-mail address field cannot be left blank.'; 
			$(emailFieldId).select();
			bools=false;
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
		{
			var erormsg = 'Please enter a valid E-mail id.';
			$(errorDiv).innerHTML=erormsg;
			$(emailFieldId).select();
			bools=false;
			return false;
		}
	}
	$(errorDiv).innerHTML='';
	return bools;
}

/*Is valid Password. Length must be 6-10
*Characters must be alpha numeric only.*/
function iboxisValidPassword(errorDiv,paswdFldId)
{
	var string = $F(paswdFldId);
   	var bValid =new Boolean(true);
	if (!string) 
	{
		$(errorDiv).innerHTML='Password field can not be left blank.';
		$(paswdFldId).select();
		bValid=false; 
	}
	
    return bValid;
}

function iboxChklogin(erroDiv,email,password,remmbermeid)
{
	var url = host+'/subscription/register/loginAjax.php';
	var auto =0;
	if($(remmbermeid).checked == true) auto=1;
	var pars="type=login&login="+email+"&pwd="+encodeURIComponent(password)+'&autologin='+auto;

	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onLoading:loading(erroDiv),
		//onLoading:$('loginErrorMsg').innerHTML="Loading...",
		onComplete:function getLoginStatus(req)
		{
		var post = eval('('+req.responseText+')');
		var tageturl="";
		var tageturl=$F('targeturl');
		if(post.status==true)
		{
		   	if(tageturl!='' && tageturl!=='undefined')
			{
				if(tageturl=="accountactivation"){
					window.location.href= host;
				}else{
					window.location.href= tageturl;	
				}
				
			}else{
				window.location.href= host + '/subscription/register/controlPanel.htm';
			}
		}
		else
		{
			if(post.msg=='Inactive account'){
				location.replace( host + "/subscription/activate.htm");
			}else{
				$(erroDiv).innerHTML = post.msg;
				return false;
			}
		}
	}});
}
 
function iboxLogin(emailFieldId,pwdFieldId,remmbermeid,errorDiv)
{
	var strlemail = $(emailFieldId).value;
	var strlpassword = $(pwdFieldId).value;
	var isvalidEmail=iboxisValidEmail(errorDiv,emailFieldId);
		
	if(isvalidEmail)
	{
		//password check except password
		if($(pwdFieldId).value.toLowerCase()=='password')
		{
			$(errorDiv).innerHTML="Please Enter Any other Password Except 'Password'";
			$(pwdFieldId).select();
			return false;
		}
	}
	var isvalidPassword=iboxisValidPassword(errorDiv,pwdFieldId);
	if(isvalidEmail && isvalidPassword)
	{
		
		iboxChklogin(errorDiv,strlemail,strlpassword,remmbermeid);
	}
}

function disableLoginButton(){
	$('header_login').hide();
	$('header_login_hide').show(); 
}
/****Header Login Code Starts**/
function processLogin(email, password,pagename)
{
	var url = host+'/subscription/register/iboxLoginAjax.php';
	var auto =0;
	if($('chkremember').checked == true) auto=1;
	var pars="type=login&login="+email+"&pwd="+encodeURIComponent(password)+'&autologin='+auto;
	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,	onLoading:disableLoginButton(), 
		onComplete:function getLoginStatus(req)
		{
		var post = eval('('+req.responseText+')');
		if(post.status==true)
		{	
			if(pagename=="accountactivation"){
				location.replace(host);	
			}else{
				window.location.reload();
			}
		}
		else if(post.msg=='Inactive account'){
			location.replace( host + "/subscription/activate.htm");
		}
		else
		{	
			$('header_login_hide').hide();
			$('header_login').show();
			alert(post.msg);
			return false;
		}
	}});
}

function Login(emailFieldId,pwdFieldId,pagename)
{
	var strlemail = $(emailFieldId).value;
	var strlpassword = $(pwdFieldId).value;
	var isvalidEmail=isValidEmail(emailFieldId);

	if(isvalidEmail)
	{
		//password check except password
		if($(pwdFieldId).value.toLowerCase()=='password')
		{
			alert("Please Enter Any other Password Except 'Password'");
			$(pwdFieldId).select();
			return false;
		}
	}
	if(isvalidEmail)
	{
		processLogin(strlemail,strlpassword,pagename);
	}
}

function displayLoginProfile(div1,div2,div3){
	$(div1).style.display='block';
	$(div2).style.display='none';
	if(div1=='profilemv'){
		$(div3).style.display='block';
	}else{
	  $(div3).style.display='none';	
	}

}

function displayLoginProfilePage(divId){
	if(divId=='signUp'){
		jQuery(".logIn").hide();
		jQuery(".logIn_Profile").show();
		jQuery(".mvilIntro").show();
	}
	else{
		jQuery(".logIn").show();
		jQuery(".logIn_Profile").hide();
		jQuery(".mvilIntro").hide();
	}
//	window.location=host+'/subscription/register/login.htm';
}


/****Header Login Code Ends**/

function checkPasswordField(fieldId)
{
	$(fieldId).type='password';
	$(fieldId).value="";
}

function chkSpaceNullPasswod(fieldId,str,defMsg,errorDiv)
{
	var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.
	if((str=='')||(str.length==0) || (str==null) || (str.search(re) > -1))
{
		$(fieldId).type='text';
		$(fieldId).value=defMsg;
		return false;
	}else{
		//return checkPassWordLower(fieldId,errorDiv)
		return true;
	}
}


function checkPassWordLower(pwdFieldId,errorDiv)
{
var passwordget=$F(pwdFieldId).toLowerCase();

if(passwordget=='password')
{
	$(errorDiv).innerHTML="Please Enter Any other Password Except 'Password'";
	$(pwdFieldId).select();
	return false;
}else{
	$(errorDiv).innerHTML="";
	return true;
}
}

function launchCategoryScreen(errorDiv,closescreenID,openscreenID,trackurl)
{
	return registrtionFieldsValidate(errorDiv,closescreenID,openscreenID,trackurl);
}

function registrtionFieldsValidate(errorDiv,closescreenID,openscreenID,trackurl)
{
	var strremailvalue = $F('useremail');
	var strcemailvalue = $F('useremailConf');
	try{
		var strrpasswordvalue = $F('tuserRPassword');
	}catch(err){
		var strrpasswordvalue = $F('userRPassword');
	}
	try
	{
		var strrcpasswordvalue = $F('tuserConfPassword');
	}catch(err){
		var strrcpasswordvalue = $F('userConfPassword');
	}	
	if(($F('userFname').toLowerCase()=='first name')||($F('userFname').toLowerCase()=='firstname'))
{
		$(errorDiv).innerHTML="Not a valid 'First Name'";
		$('userFname').select();
		return false;
	}

	var strfirstname = validateAlphaFieldsOnly(errorDiv,'userFname','First Name');
	if(strfirstname)
	{
		var strlastname = validateAlphaFieldsOnly(errorDiv,'userLname','Last Name');
	}
	
	if(strlastname)
		{
			var strremail = iboxisValidEmail(errorDiv,'useremail')//isValidEmail('useremail');
		}
	if(strremail)
	{
		if(strremailvalue != strcemailvalue)
		{
			$(errorDiv).innerHTML="Email and Confirm Email does not match.";
			$('useremail').select();
			return false;
		}
		else{
		try{
			var PWL=checkPassWordLower('userRPassword',errorDiv);
		}catch(errs){
			var PWL=checkPassWordLower('tuserRPassword',errorDiv);
		}
		if(PWL)
		{
		try{
			var PWCL=checkPassWordLower('userConfPassword',errorDiv);
		}catch(errs){
			var PWCL=checkPassWordLower('tuserConfPassword',errorDiv);
		}
		}
		if(PWL && PWCL)
			{
			try{
				var strrpassword = iboxisValidPasswordRegistration(errorDiv,'userRPassword');
			}catch(err){
				var strrpassword = iboxisValidPasswordRegistration(errorDiv,'tuserRPassword');
			}
			}else
			{
				var strrpassword=false;
			}
		}
	}
	if(strrpassword)
	{
		if(strrpasswordvalue != strrcpasswordvalue)
		{
			$(errorDiv).innerHTML="Password and Confirm password does not match.";
			$('tuserRPassword').select();
			 
			return false;
		}
		
		if($('agreechkbox').checked == false)
		{
			//alert("You did not agree with our privacy terms.");
			$(errorDiv).innerHTML="You did not agree with our privacy terms.";
			return false;
		}	
	}

	if((strfirstname == false) || (strlastname == false) || (strremail == false) || (strrpassword == false))
	{
		return false;
	}
	else
	{   googleAnalytics('',trackurl);
		$(errorDiv).innerHTML="";
			var url = host+'/subscription/register/iboxLoginAjax.php';
			var pars="type=checkUser&login="+strremailvalue;
			var myAjax4 = new Ajax.Request(url, {method: 'post',
			parameters: pars,
			onLoading:loading(errorDiv),
			//onLoading:$(errorDiv).innerHTML="Loading...",
			onComplete:function checkExistingUserInMVIL(req)
			{
				var getStatus=0;
				getStatus=parseInt(req.responseText);
				if(getStatus)
				{
					$(errorDiv).innerHTML='This user already in use our System. Please Select Another email ID';
					$('useremail').select();
					return false;
				}else{
					
					if(req.responseText !="") // for site mainence code
					{
						var res = eval('('+req.responseText+')');
						if(res.status == false && res.msg)
						{						
							$(errorDiv).innerHTML=res.msg;
							return false;
						}
					}
					$(errorDiv).innerHTML="";
					$(closescreenID).hide();
					$('cuurntscreen').value = parseInt($F('cuurntscreen'))+1;
					
					if($('cuurntscreen').value==2)
					{
						$('ibox_wrapper').setStyle("width:540px;height:540px;");
						$('parentdiv').setStyle("width:537px;");
					}
					$(openscreenID).show();
					return true;
				}
			}});
	}
}



/* You are in product screen and not selecting any products then u wont ask for credit details otherwise u will ask for credit details*/

function launchNextScreen(errorDiv,closescreenID,openscreenID,trackurl,googleAdWord){
	var goforreg=false;

	if(closescreenID=='screen_4')
	{
				if(!checkForAnySelectedProducts())
				{
					goforreg=true;
					openscreenID='screen_6';
				}else
				{
					if($('buzzproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'buzz_1').each(function(e)
						{
							if(e.checked){
								checked=true;
						}});
						if(!checked)
						{
							$('producterr').innerHTML='Please Select Any subscription';
							return checked;
						}
					}
					 if($('coopproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'cooper_1').each(function(e)
						{
							if(e.checked){
								checked=true;
						}});
						if(!checked)
						{
								$('producterr').innerHTML='Please Select Any subscription';
								return checked;
						}
					}
					 if($('flexproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'flexfolio_1').each(function(e){
							if(e.checked){
								checked=true;
							}});
							if(!checked){
								$('producterr').innerHTML='Please Select Any subscription';
								return checked;
							}
					}
					 if($('optionsmithproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'optionsmithproduct_1').each(function(e){
							if(e.checked){
								checked=true;
							}});
							if(!checked){
								$('producterr').innerHTML='Please Select Any subscription';
								return checked;
							}
					}

					 if($('tspproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'tsp_1').each(function(e){
							if(e.checked){
								checked=true;
							}});
							if(!checked){
								$('producterr').innerHTML='Please Select Any subscription';
								return checked;
							}
					}
					 if($('etfproduct').checked)
					{
						var checked=false;
						$('iboxregform').getInputs('radio', 'etf_1').each(function(e){
							if(e.checked){
								checked=true;
							}});
							if(!checked){
								$('producterr').innerHTML='Please Select Any subscription';
								return checked;
							}
					}
				}
	}

	if(goforreg==false)
	{
		$(closescreenID).hide();
		$('cuurntscreen').value = parseInt($F('cuurntscreen'))+1;

		if($('cuurntscreen').value==3) // product screen
		{
			$('ibox_wrapper').setStyle("width:537px;height:475px;");
			$('parentdiv').setStyle("width:537px;");
		}
		else if($('cuurntscreen').value==4) // product screen
		{
			$('ibox_wrapper').setStyle("width:537px;height:520px;");
			$('parentdiv').setStyle("width:537px;");
		}else if($('cuurntscreen').value==5) // payment screen
		{
			$('ibox_wrapper').setStyle("width:537px;height:445px;");
			$('parentdiv').setStyle("width:537px;");
		}
		$(openscreenID).show();
		var strvalur=trackurl.split(',');
		var arrlen=strvalur.length;
		var i;
		for(i=0;i<=arrlen-1;i++){
			  if(openscreenID=='screen_6'){
				var trackname=strvalur[i];
				var tracklen=trackname.length;
				var strpos=trackname.indexOf("TrialProduct");
		    	var tracknamega=trackname.substring(0,strpos) + 'Welcome' + trackname.substring(strpos+12,tracklen);
				$('gAds').innerHTML= googleAdWord;
			  }else{
				tracknamega=strvalur[i];
			  }
			googleAnalytics('',tracknamega);
		}
		
		

	}else if((goforreg==true)&&(openscreenID=='screen_6'))
	{
		// Go for registration and open screen 6
		registeruser(0,errorDiv,closescreenID,openscreenID);
	}
	
}

function checkForAnySelectedProducts(){

	var checkedAny=false;
	$$('input.checkboxes').each(function(e){
		if(e.checked){
			checkedAny="true";
		}
	});
	return checkedAny;	
}

function radiofldsupdates(checkboxid){
	switch(checkboxid)
	{
		case 'buzzproduct':
			if($(checkboxid).checked){
				$$('input.buzzs').each(function(e){
					e.enable();
				});
			}else{
				$$('input.buzzs').each(function(e){
					e.disable();
				});

			}
			break;    
		case 'coopproduct':
			if($(checkboxid).checked){
				$$('input.coopss').each(function(e){
					e.enable();
				});
			}else{
				$$('input.coopss').each(function(e){
					e.disable();
				});
			}

		break;
		case 'flexproduct':
			if($(checkboxid).checked){
				$$('input.flexos').each(function(e){
					e.enable();
				});
			}else{
				$$('input.flexos').each(function(e){
					e.disable();
				});
			}
		break;
		case 'optionsmithproduct':
			if($(checkboxid).checked){
				$$('input.optionsmiths').each(function(e){
					e.enable();
				});
			}else{
				$$('input.optionsmiths').each(function(e){
					e.disable();
				});
			}
		break;
		case 'jackproduct':
			if($(checkboxid).checked){
				$$('input.jacks').each(function(e){
					e.enable();
				});
			}else{
				$$('input.jacks').each(function(e){
					e.disable();
				});
			}
		break;
		case 'etfproduct':
			if($(checkboxid).checked){
				$$('input.etfs').each(function(e){
					e.enable();
				});
			}else{
				$$('input.etfs').each(function(e){
					e.disable();
				});
			}
		break;
		case 'tspproduct':
					if($(checkboxid).checked){
						$$('input.tsps').each(function(e){
							e.enable();
						});
					}else{
						$$('input.tsps').each(function(e){
							e.disable();
						});
					}
		break;
		default:
		return false;
	}
}

function logout(ids){
	var url = host+'/subscription/register/iboxLoginAjax.php';
	var pars="type=logout&login="+ids;
	var myAjax4 = new Ajax.Request(url, {method: 'post',
	parameters: pars,
	onComplete:function getLogoutStatus(req){
		var post = eval('('+req.responseText+')');					
		if(post.status==false)
		{
			alert(post.msg);
			return false;			
		}
		else
		{
			window.location.reload();
		}
	}});
}

function validatePaymentInfo(errorDiv,number, name){
	if (!checkCreditCard (number, name)){
		$(errorDiv).innerHTML=(ccErrors[ccErrorNo]);

		if(ccErrors[ccErrorNo]!='Unknown card type'){
			$('iboxccnum').select();
		}else{
			$('iboxcctype').select();
		}
		return false;
	}else{
		return true;
	}
}
function validatePaymentFields(errorDiv){
if($F('billingaddress1')==''){$(errorDiv).innerHTML="Billing Address Is Blank"; $('billingaddress1').select(); return false;}
//else if($F('billingaddress2')==''){$(errorDiv).innerHTML="Billing Address Is Blank";$('billingaddress2').select();return false;}
else if($F('billingcity')==''){$(errorDiv).innerHTML="City Is Blank";$('billingcity').select();return false;}
else if(($F('iboxphone')=='')||(isNaN($F('iboxphone')))){$(errorDiv).innerHTML="Invalid Phone Number";$('iboxphone').select();return false;}
else if($('iboxstate').disabled==false && $F('iboxstate')==''){$(errorDiv).innerHTML="State is Blank";$('iboxstate').select();return false;}
else if(($F('iboxcountry')=='') || ($F('iboxcountry')=='CLEARED')){$(errorDiv).innerHTML="Please Select the country";$('iboxcountry').select();return false;}
else{ return true;}
}

function paymentScreenvalidate(skip,errorDiv,closescreenID,openscreenID,trackurl){
	if(skip){
		if (confirm(skip_alert)) {
			registeruser(skip,errorDiv,closescreenID,openscreenID);
		} else {
			return false;
		}
	}
	if((skip==0) && (closescreenID=='screen_5'))
	{
		fieldValidate = validatePaymentFields(errorDiv);

		if(fieldValidate)
		{
			var name=$F('iboxcctype');
			var number=$F('iboxccnum');
			if(name=='')
			{
				$(errorDiv).innerHTML="Please Select the Credit Crard Type";
				$('iboxcctype').focus();
				return false;

			}else if((isNaN(number)) || (number==''))
			{

				$(errorDiv).innerHTML="Invalid Credit Crard Number";
				$('iboxccnum').select();
				return false;
			}
			isValidCC = validatePaymentInfo(errorDiv,number, name);
			if(!isValidCC){return false;}

			if((fieldValidate) && (isValidCC))
			{
				mothCVV = checkMonthCvv(errorDiv);
			}
		}
		
		if(fieldValidate && isValidCC && mothCVV)
		{
			googleAnalytics('',trackurl);
			/* Get the Email Categories and concat*/
			var fnalcategory_ids='';
			var fnalcontributors_ids='';
			for(i=0;i<document.iboxregform.category.length;i++){if(document.iboxregform.category[i].checked){var category_ids='';category_ids=document.iboxregform.category[i].value;

			/*
			if(category_ids==7){ // static check as the id of news and views is 7 in email_categories
				var recv_daily_gazette=1;
			}else{
				var recv_daily_gazette=0;
			}*/
			
			if(fnalcategory_ids==''){fnalcategory_ids=","+category_ids;}else{fnalcategory_ids=fnalcategory_ids+","+category_ids;}}}
			for(i=0;i<document.iboxregform.contributors.length;i++){if(document.iboxregform.contributors[i].checked){var contributors_ids='';contributors_ids=document.iboxregform.contributors[i].value;if(fnalcontributors_ids==''){fnalcontributors_ids=","+contributors_ids;}else{fnalcontributors_ids=fnalcontributors_ids+","+contributors_ids;}}}
			var myhash = ($('iboxregform').serialize());
			var url = host+'/subscription/register/iboxLoginAjax.php';
			var pars="type=registration&productselected=1&"+myhash+'&emailCategories='+fnalcategory_ids+'&authorCategories='+fnalcontributors_ids;
			var myAjax4 = new Ajax.Request(url, {method: 'post',
								parameters: pars,
								onLoading:loading(errorDiv),
								//onLoading:$(errorDiv).innerHTML="Loading...",
								onComplete:function submitRegData(req)
								{
									var getStatus=0;
									var post = eval('('+req.responseText+')');
									/**********************/
									var strval=post.tracking_name;
										var strvalur=strval.split(',');
										var arrlen=strvalur.length;
										var i;
										var gAdProds= '';
										var gAdProdsVals = new Array();
									
									
										for(i=0;i<=arrlen-1;i++){
											//googleAnalytics('','',strvalur[i]);										
											gAdProdsVals = strvalur[i].split("-");
											gAdProds  = gAdProds + "," + gAdProdsVals['0'];													
										}
										//var googlead = googleAdWord(gAdProds,'hard');
									
									
									if(post.status==true)
									{
										// Go to screen 6
										$('jsfname').innerHTML=post.firstname;
										$('jslname').innerHTML=post.lastname;

										var url = host+'/subscription/register/registration_ajax.php';
										var parms = 'type=gogleAdWorld&prod='+gAdProds;	
										var postAjax= new Ajax.Request(url, {method: 'post', parameters: parms, onComplete:function(req){										   
											var res = req.responseText;												
											launchNextScreen(errorDiv,closescreenID,openscreenID,strval,res);
												
										}});

										
									}else{
										$(errorDiv).innerHTML=post.msg;
										return false;
									}
			}});
		}
	}
}

function checkMonthCvv(errorDiv)
{
	var flag=true;

	if($F('iboxccexpire')==''){$(errorDiv).innerHTML="Expiration Date Is Blank";$('iboxccexpire').select();flag=false;
	return false;}
	
	var month = $F('iboxccexpire');

	if((month.search('/')==-1)||(month.length!=7)){
		$(errorDiv).innerHTML="Invalid Format";
		$('iboxccexpire').select();
		flag=false;
	}
	var result=month.split('/');
	
	if(isNaN(parseInt(result[0]))){$(errorDiv).innerHTML="Invalid Month";$('iboxccexpire').select();flag=false;}
	
	else if( (parseInt(result[0])<0) || (parseInt(result[0])>12)){$(errorDiv).innerHTML="Invalid Month";$('iboxccexpire').select();flag=false;}
	
	else if(isNaN(parseInt(result[1]))){$(errorDiv).innerHTML="Invalid Year";$('iboxccexpire').select();flag=false;}
	
	else if(parseInt(result[1])<0){$(errorDiv).innerHTML="Invalid Year";$('iboxccexpire').select();flag=false;}
	
	if(flag){$(errorDiv).innerHTML="";

		if((isNaN($F('iboxcvvnum'))) ||($F('iboxcvvnum')=='')){$(errorDiv).innerHTML="Invalid CVV Number";$('iboxcvvnum').select();flag=false;}

	}
	return flag;
}

function registeruser(skip,errorDiv,closescreenID,openscreenID){
	var fnalcategory_ids='';
	var fnalcontributors_ids='';
	for(i=0;i<document.iboxregform.category.length;i++){if(document.iboxregform.category[i].checked){var category_ids='';category_ids=document.iboxregform.category[i].value;
	/***
	if(category_ids==7){
		var recv_daily_gazette=1;
	}else{
		var recv_daily_gazette=0;
	}**/
	
	if(fnalcategory_ids==''){fnalcategory_ids=","+category_ids;}else{fnalcategory_ids=fnalcategory_ids+","+category_ids;}}}
	for(i=0;i<document.iboxregform.contributors.length;i++){if(document.iboxregform.contributors[i].checked){var contributors_ids='';contributors_ids=document.iboxregform.contributors[i].value;if(fnalcontributors_ids==''){fnalcontributors_ids=","+contributors_ids;}else{fnalcontributors_ids=fnalcontributors_ids+","+contributors_ids;}}}
	var myhash = ($('iboxregform').serialize());
	var url = host+'/subscription/register/iboxLoginAjax.php';
	var pars="skip="+skip+"&type=registration&productselected=0&"+myhash+'&emailCategories='+fnalcategory_ids+'&authorCategories='+fnalcontributors_ids;
	var myAjax4 = new Ajax.Request(url, {method: 'post',
					parameters: pars,
					onLoading:loading(errorDiv),
					//onLoading:$(errorDiv).innerHTML="Loading...",
					onComplete:function submitRegData(req)
					{
					var getStatus=0;
					var post = eval('('+req.responseText+')');
					if(post.status==true)
					{
						$('jsfname').innerHTML=post.firstname;
						$('jslname').innerHTML=post.lastname;
						$(closescreenID).hide();
						if(skip)
						{
							$('cuurntscreen').value = parseInt($F('cuurntscreen'))+1;
						}else{
							$('cuurntscreen').value = parseInt($F('cuurntscreen'))+2;
						}
						if($('cuurntscreen').value==6)
						{
							$('ibox_wrapper').setStyle("width:537px;height:430px;");
							$('parentdiv').setStyle("width:537px;");
						}
						
						$(openscreenID).show();
						
					}else{
						//$(errorDiv).innerHTML='JS:Error';
						return false;
					}
	}});
}

function closemeibox(trackurl){
	googleAnalytics('',trackurl);
	if(parseInt($F('cuurntscreen'))==6)
	{
		if($F('iboxccnum').length>0){
			window.location.reload();
		}else{
			location.replace( host + "/subscription/activate.htm");			
		}
		iboxclose();
	}else
	{
		iboxclose();
	}
}


function forgotPassword(event,errorDiv,emailFieldId){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		checkforgotpassword(errorDiv,emailFieldId);
	}
}

function checkforgotpassword(errorDiv,emailFieldId){
var chk = iboxisValidEmail(errorDiv,emailFieldId);
	if(chk)
	{
		var uid=trim($F(emailFieldId));
		var url = host+'/subscription/register/loginAjax.php';
		var pars='type=forgotpassword&uid='+uid;
		var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onLoading:loading(errorDiv),
		//onLoading:$(errorDiv).innerHTML="Checking...",
		onComplete:function checkavail(req)
		{
			var getStatus=0;
			var post = eval('('+req.responseText+')');
			if(post.status==true)
			{
				getStatus = post.id;
				//alert(getStatus)// then send the request for email
				mailforgotpassword(errorDiv,emailFieldId,getStatus)
			}else
			{
				if(req.responseText !="") // for site mainence code
				{
					var res = eval('('+req.responseText+')');
					if(res.status == false && res.msg)
					{						
						$(errorDiv).innerHTML=res.msg;
						return false;
					}
				}
				$(errorDiv).innerHTML='Sorry, that email doesn\'t appear to be in the system.';
				$(emailFieldId).select();
				return false;
			}

		}});
	}
}
function mailforgotpassword(errorDiv,emailFieldId,getStatus){
	var chk = iboxisValidEmail(errorDiv,emailFieldId);
	if(chk)
	{
		var uid=trim($F(emailFieldId));
		var url = host+'/subscription/register/loginAjax.php';
		var pars='type=sendmail&uid='+uid+'&subId='+getStatus;
		var myAjax4 = new Ajax.Request(url, {method: 'post',
			parameters: pars,
			//onLoading:loading(errorDiv),
			onLoading:$(errorDiv).innerHTML="Sending...",
			onComplete:function checkavail(req)
			{
			if(req.responseText!=''){
				$(errorDiv).innerHTML=req.responseText;
			}else{
				$(errorDiv).innerHTML='';
			}
		}});
	}
}
function checkenterKey(event,panel,screenno){
	//alert(event+panel+screenno)
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==27){ // escape key
		//alert("u presed ")
		closemeibox();
	}

	if(keyVal==13){ // Enter key pressed
		if((panel=='iboxlogin') && (screenno==1)){
			iboxLogin('useremailadd','userpassword','autologin','loginErrorMsg');
		}else if((panel=='iboxRegist') && (screenno==1)){
			launchCategoryScreen('regError','screen_1','screen_2');
		}else if((panel=='iboxRegist') && (screenno==2)){
			launchNextScreen('regError','screen_2','screen_3');
		}else if((panel=='iboxRegist') && (screenno==3)){
			launchNextScreen('regError','screen_3','screen_4');
		}else if((panel=='iboxRegist') && (screenno==4)){
			launchNextScreen('producterr','screen_4','screen_5');
		}else if((panel=='iboxRegist') && (screenno==5)){
			paymentScreenvalidate(skip,'paymenterr','screen_5','screen_6');
		}
		}
}
function textboxToPassword(element,newfieldid,passtabindex){
	// this function is to fix the problem with IE's inability to change form element types on the fly. but firefox can. go firefox.
	var oldP = $(element.id);	//get the existing password textbox
	var oNewP = document.createElement("input"); //create a temp new password box
	oNewP.setAttribute("name",newfieldid);
	oNewP.setAttribute("id",newfieldid);
	oNewP.setAttribute("type","password"); //mask the input *****
	oNewP.setAttribute('tabIndex',passtabindex);
	oNewP.onKeyPress = function() {checkenterKey(event,"iboxRegist",1); };
	//oNewP.setAttribute('onKeyPress','javascript:checkenterKey(event,"iboxRegist",1);'); // not working in IE
	//overwrite existing password textbox with new password box.
	oldP.parentNode.replaceChild(oNewP,oldP);
	//grab focus of the new password box.
	var pbox = document.getElementById(newfieldid);
	$(newfieldid).addClassName('login_input_box');
	pbox.focus();
	$(newfieldid).select();
}
function chkSpaceNullHeader(fieldId,defMsg)
{ 
	var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.
	var str = $F(fieldId);
	if((str=='')||(str.length==0) || (str==null) || (str.search(re) > -1))
	{
	 
		$(fieldId).value=defMsg;
		return false;
	}
	else
	{
		return true;
	}
}

function textboxToPasswordHeader(element,newfieldid,passtabindex,classId){
	 
	// this function is to fix the problem with IE's inability to change form element types on the fly. but firefox can. go firefox.
	var oldP = $(element.id);	//get the existing password textbox
	var oNewP = document.createElement("input"); //create a temp new password box
	oNewP.setAttribute("name",newfieldid);
	oNewP.setAttribute("id",newfieldid);
	oNewP.setAttribute("value",'');
	oNewP.setAttribute("tabIndex",passtabindex);
	oNewP.setAttribute("type","password");  		
	oNewP.onblur = function() {chkSpaceNullHeader(newfieldid,'Password'); };
	oNewP.onfocus = function() {checkPasswordField(newfieldid); };	
	oldP.parentNode.replaceChild(oNewP,oldP);
	//grab focus of the new password box.
	var pbox = document.getElementById(newfieldid);
	$(newfieldid).addClassName(classId);
	pbox.focus();
	$(newfieldid).select();
}

function loginEnterKeyChk(evt,pagename)
{
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13)
	{
		iboxLogin('luseremailadd','luserpassword','lautologin','loginErrorMsgs');
		
	}
}

function fpEnterKeyChk(evt)
{
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13)
	{
		checkforgotpassword('password_login_error','forgot_pwd');
		
	}
}