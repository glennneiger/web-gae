<!--code for registration-->
var xmlHttp
var vardiv

//New user
function NewLogin(sBody, bounceval)
{	
	var urllength = bounceval.length;
	var urlindex = bounceval.lastIndexOf('&page');
	if(urlindex != -1){
		var passpameters=bounceval;
		var bounceval = bounceval.substr(urlindex, urllength);
	}else{
		var passpameters=bounceval;
	}
	xmlHttp=GetXmlHttpObject();
	height=50;
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	var url = host + '/community/exchange_register/welcome.htm?' + sBody + '&' + bounceval + '&flag=index'+'&'+passpameters;
	$('screenThree').hide();
	$('headerbar').hide();
	$('newregistration').show();
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() 
	{ 
		stateChange(vardiv); 
	};
	xmlHttp.send(null);
}


//New IntermediateReg
function IntermediateReg(sBodyI, bounceval,pagename)
{
	if(pagename==null){
		pagename='';
	}
	var passpagename=pagename;
	var urllength = bounceval.length;
	var urlindex = bounceval.lastIndexOf('&page');

	if(urlindex != -1){
		var passpameters=bounceval;
		var bounceval = bounceval.substr(urlindex, urllength);
	}else{
		var passpameters=bounceval;
	}
	xmlHttp=GetXmlHttpObject();
	height=50;
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
    if(passpagename=='email_screen_2.htm')
	{
		$wrapperHeight='490px';
	}
	else
	{
		$wrapperHeight='400px';
	}
	var url = host+'/community/exchange_register/'+passpagename+'?' + sBodyI + '&' + bounceval+'&'+passpameters;
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() 
	{ 
		stateChange(vardiv); 
		$('ibox_wrapper').style.height = $wrapperHeight;
	};
	xmlHttp.send(null);
}
//New IntermediateReg

//Existing User
function chkLogin(email, password, bounceval)
{
	var urllength = bounceval.length;
	var urlindex = bounceval.lastIndexOf('&page');
	if(urlindex != -1){
		var bounceval = bounceval.substr(urlindex, urllength);
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	
	var url = host + '/community/exchange_register/welcome.htm?email=' + email + '&password=' + password  + '&' +bounceval + '&flag=fromindex';
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function()
	{ 
		registrationstateChange(vardiv); 
	};
	xmlHttp.send(null);
}

function registrationstateChange(vardivid)
{
	if (xmlHttp.readyState==4)
	{
		if (xmlHttp.status==200)
		{
			//if (xmlHttp.responseText.length==34)
			if (xmlHttp.responseText.search('Login_Successful') == -1) 
			{
				document.getElementById(vardivid).innerHTML=xmlHttp.responseText;
			}
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	}
	else
	{
		height=50;
		showProgress(vardivid);
	}
	
	if (xmlHttp.responseText.search('Login_Successful') != -1) 
	{
		var url = xmlHttp.responseText;
		var urllength = url.length;
		//var urlindex = url.lastIndexOf('Login_Successful');
		var urlindex = url.lastIndexOf('http://');
		url = url.substr(urlindex, urllength);
		window.location = url;
	} 

//if (xmlHttp.responseText.length!=34)
	
}
//Existing User

//index.htm
function UValidate(loginform, bounceval,userIsLoggedIn){
	var strlemail = $('lemail').value;
	var strlpassword = $('lpassword').value;
	var str = isValidEmail('lemail');

	if( str== false){
		return false;
	}

	if (strlpassword == "")
	{
		alert("Password field can not be left blank.");
		$('lpassword').focus();
		return false;
	}
	else if(str == false)
	{
		return false;
	}
	else if(str = true && strlpassword != "")
	{	
		height=10;
		var email = $('lemail').value;
		var password = $('lpassword').value;
		if(bounceval!=''){
		bounceURL=1;
		}else{
		bounceURL=0; // when no parameter is passed it will redirect to home page by default
		}
		var url = host + '/community/exchange_register/welcome.htm?email=' + email + '&password=' + password  + '&op=existvalidateRecord&bounceURL='+bounceURL+'&'+bounceval;
		new Ajax.Request(url, {
		method: 'get',
		onLoading:showProgress1('errormsg'),
		onSuccess: function(transport) {
					
			var response = transport.responseText.evalJSON();
			
			if (response.message) //Invalid record
			{
				$('errormsg').innerHTML = "<span style='color:red;'>"+response.message+"</span>";
				$('errormsg').show();
			}else
			{
				window.location = response.URL; // redirecting to the destination
			}
		}
		});
	}
}

function RValidate(regform, bounceval){
	var strremailvalue = $('remail').value;
	var strcemailvalue = $('rcemail').value;
	var strrpasswordvalue = $('rpassword').value;
	var strrcpasswordvalue = $('rcpassword').value;	
	var strfirstname = validateAlphaField('firstname');
	
        
        if(strfirstname)
	{
		var strlastname = validateAlphaField('lastname');	
	}
	if(strlastname)
	{var strremail = isValidEmail('remail');}
       if(strremail){	
		if(strremailvalue != strcemailvalue)
		{
			alert("Email and Confirm Email does not match.");
			return false;
		}
		var strrpassword = isValidPassword('rpassword');	
	}
	if(strrpassword)
	{
		if(strrpasswordvalue != strrcpasswordvalue)
		{
			alert("Password and Confirm password does not match.");
			return false;
		}
		
		if($('agree').checked == false)
	    {
		 alert("You did not agree with our privacy terms.");
		 return false;
	    }	
	}
	
	if((strfirstname == false) || (strlastname == false) || (strremail == false) || (strrpassword == false))
	{
		return false;
	}
	else
	{
	
		/********* Check the user detail **********/
		var url= host + '/community/useremail_chk.htm?useremail='+strremailvalue;
		height=0;
		new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(statechanged){
				// if xmlHttpobj.responseText!=0 user is an existing
				// else he is a new user then just pass email id only	
						if(statechanged.responseText!=0){
								var result=(statechanged.responseText).split(",");
								var hasemail_detail=result[5].split("=");
								var hasexchange_detail=result[4].split("=");

								if(hasemail_detail[1]==1){
									var managval=1;
									
								} else if(hasemail_detail[1]==0)
								{
									var managval=2;
								}
								
								$('has_email_alerts').value=managval;
								$('exchangeuser').value=hasexchange_detail[1];
								$('errorMsg').show();
								
							} else if(statechanged.responseText==0){
								
									$('has_email_alerts').value=0;
									$('exchangeuser').value=0;
									$('h_firstname').value = $('firstname').value;
									$('h_lastname').value = $('lastname').value;
									$('h_remail').value = $('remail').value;
									$('h_age').value = $F('agegroup');
									$('h_zip').value = $F('zip');
									$('ibox_wrapper').style.height='440px';
									$('h_rpassword').value = $F('rcpassword');
									$('newregistration').hide();
									$('catgBlock').show();
							}

				}});
		//IntermediateReg(sBody, bounceval,"email_screen_2.htm");
	}	
}
//index.htm

//general
function getRequestBody(oForm) 
{
	var aParams = new Array();
	for (var i=0 ; i < oForm.elements.length; i++) 
	{
		var sParam = encodeURIComponent(oForm.elements[i].name);
		sParam += "=";
		sParam += encodeURIComponent(oForm.elements[i].value);
		aParams.push(sParam);
	}	
	return aParams.join("&");
}

function isValidEmail(field1){
	field=document.getElementById(field1);
	//if(!field.value) return true;
	var emails=field.value.split(';');
	for (var i=0;i<emails.length;i++)
	{
		if(emails[i]=='')
		{
			alert('Email field can not be left blank.') 
			field.select();
			field.focus();
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
		{
			alert('Not a valid E-mail id "'+emails[i]+'"') 
			field.select();
			field.focus();			 
			return false;
		}
	}
	return true;
}

/*Is valid Password. Length must be 6-10
*Characters must be alpha numeric only.*/
function isValidPassword(field1){
	field=document.getElementById(field1);
	string = field.value
   	var bValid =new Boolean(true);
   	if (!string) 
	{
		alert("Password field can not be left blank.");
		bValid=false; 
	}
	else if(string.length >15 || string.length <6) 
	{
		alert("Password can't be less than 6 or greater then 15 characters.");
		bValid=false;
	}
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
			var msg='Not a valid '
			var long="";
			if(field.name=='rpassword')
				long="Password.";
			if(field.name=='rcpassword')
				long="Confirm Password.";
			alert(msg + long);
			field.select();
			field.focus();
		}
	}
    return bValid;
}
/* 
Text must be Alpha */
function validateAlphaField(field1) {
	field=document.getElementById(field1);
	string = trim(field.value);
    var bValid =new Boolean(true);
	if (!string) 
	{	
		bValid=false;
	}
    var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
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
		if(field.name=='firstname')
			long="First Name.";
		if(field.name=='lastname')
			long="Last Name.";
		if(field.name.substring(0,4)=='unit')
			long="Unit.";
		
		alert(msg + long);
		field.select();
		field.focus();
	}
    return bValid;
} 
//general

//welcome.htm
function gotoHome()
{
	var homeURL = host +'/community/home.htm';
	window.location = homeURL;		
}

function WelcomeValidate(welcomeform, bounceval, loginid)
{
	var sBody = getRequestBody(welcomeform);
	if(welcomeform.welcomeprivacy.checked == false)
	{
		alert("You did not agree with our privacy terms.");
		return false;
	}
	else
	{
		WelcomeLogin(sBody, bounceval, loginid);
	}
}

function WelcomeLogin(sBody, bounceval, loginid)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	var url = host + '/community/exchange_register/welcome.htm?' + sBody + '&' + bounceval + '&flag=fromwelcome&lid=' + loginid;
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() 
	{ 
		stateChange(vardiv,0); 
	};
	xmlHttp.send(null);	
}
//welcome.htm


//email_screen_2.htm
function CValidate(categoryform, bounceval){
	var cBody = CgetRequestBody(categoryform);
	if($('exst').value=='existing'){
		var useremail=$('useremail').value;
		var userpwd=$('userpwd').value;
		var userid=$('userid').value;
		if(userid==0){
		chkLogin_for_existinguser(cBody,useremail,userpwd,bounceval);
		}else if(userid!=0){
		// user already logged in
		chkLogin_for_existinguser(cBody,useremail,userpwd,bounceval);
		}
	}else{
	IntermediateReg(cBody, bounceval,"email_screen_3.htm");
}
}
//email_screen_2.htm


//email_screen_3.htm
function PValidateSelf(productform, bounceval){
	// bounceval = $(productform).serialize();
	if(($('exs').value)!='existing'){
		bounceval = "firstname="+$F('h_firstname')+"&lastname="+$F('h_lastname')+"&remail="+$F('h_remail')+"&rpassword="+$F('h_rpassword')+"&zip="+$F('h_zip')+"&age="+$F('h_age');
		var pBody = PgetRequestBody(productform);
		NewLogin(pBody, bounceval);
	}
	else if(($('exs').value)=='existing'){
		var pBody = PgetRequestBody(productform);
		var useremail=$('useremail').value;
		var userpwd=$('userpwd').value;
		var userid=$('userid').value;
		if(userid==0){
		chkLogin_for_existinguser(pBody,useremail,userpwd,bounceval);
		}else if(userid!=0){
		// user already logged in
		chkLogin_for_existinguser(pBody,useremail,userpwd,bounceval);
		}
	}
}
//email_screen_3.htm

//email_screen_3.htm
function PValidate(productform, bounceval){
	// bounceval = $(productform).serialize();
	bounceval = "firstname="+$F('h_firstname')+"&lastname="+$F('h_lastname')+"&remail="+$F('h_remail')+"&rpassword="+$F('h_rpassword')+"&zip="+$F('h_zip')+"&age="+$F('h_age');
	var cBody = CgetRequestBody(productform);
    bounceval =  bounceval +"&"+cBody;
	if(($('exs').value)!='existing'){
	var pBody = PgetRequestBody(productform);
	NewLogin(pBody, bounceval);
}
	else if(($('exs').value)=='existing'){
		var pBody = PgetRequestBody(productform);
		var useremail=$('useremail').value;
		var userpwd=$('userpwd').value;
		var userid=$('userid').value;
		if(userid==0){
		chkLogin_for_existinguser(pBody,useremail,userpwd,bounceval);
		}else if(userid!=0){
		// user already logged in
		chkLogin_for_existinguser(pBody,useremail,userpwd,bounceval);	
		}
		}
}
//email_screen_3.htm


function CgetRequestBody(oForm1) 
{
	var fnalcategory_ids='';
	if(!oForm1.category.length){
		if(oForm1.category.checked){
			fnalcategory_ids=oForm1.category.value;
		}
	}else{
	for(i=0;i<oForm1.category.length;i++){
		if(oForm1.category[i].checked){
			var category_ids="";	
			category_ids=oForm1.category[i].value;
			if(fnalcategory_ids==''){
				fnalcategory_ids=","+category_ids;
			}else{
				fnalcategory_ids=fnalcategory_ids+","+category_ids;
			}
		}

	}
	}
	fnalcategory_ids=fnalcategory_ids+",";

	var fnalcontributors_ids='';
	if(!oForm1.contributors.length){
		if(oForm1.contributors.checked){
			fnalcontributors_ids=oForm1.contributors.value;
		}
	}else{
	
	for(k=0;k<oForm1.contributors.length;k++){
		if(oForm1.contributors[k].checked){
			var contributors_ids="";
			contributors_ids=oForm1.contributors[k].value;
			if(fnalcontributors_ids==''){
				fnalcontributors_ids=","+contributors_ids;
			}else{
				fnalcontributors_ids=fnalcontributors_ids+","+contributors_ids;
			}
		}
	}
	}
	fnalcontributors_ids=fnalcontributors_ids+",";
	var cParams = new Array();
	cParams.push("categories="+fnalcategory_ids);
	cParams.push("contributors="+fnalcontributors_ids);
	return cParams.join("&");
}


function PgetRequestBody(oForm2)
{
	var fnalproduct_ids='';
	if(!oForm2.product.length){
		if(oForm2.product.checked){
			fnalproduct_ids=oForm2.product.value;
		}
	}else{
	for(i=0;i<oForm2.product.length;i++){
		if(oForm2.product[i].checked){
			var product_ids="";	
			product_ids=oForm2.product[i].value;
			if(fnalproduct_ids==''){
				fnalproduct_ids=product_ids;
			}else{
				fnalproduct_ids=fnalproduct_ids+","+product_ids;
			}
		}

	}
	}
	var pParams = new Array();
	pParams.push("products="+fnalproduct_ids);
	return pParams.join("&");
}

//Existing User going for email-alerts
function chkLogin_for_existinguser(pBody,email, password, bounceval)
{
	var urllength = bounceval.length;
	var urlindex = bounceval.lastIndexOf('&page');
	if(urlindex != -1){
		var bounceval = bounceval.substr(urlindex, urllength);
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	var url = host + '/community/exchange_register/welcome.htm?email=' + email + '&password=' + password  + '&' +bounceval + '&flag=existuser_new_emailalert'+'&'+pBody;
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function()
	{ 
		registrationstateChanged(vardiv); 
	};
	xmlHttp.send(null);
}

function registrationstateChanged(vardivid)
{
	if (xmlHttp.readyState==4)
	{
		if (xmlHttp.status==200)
		{
			//if (xmlHttp.responseText.length==34)
			if (xmlHttp.responseText.search('Login_Successful') == -1) 
			{
				document.getElementById(vardivid).innerHTML=xmlHttp.responseText;
			}
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	}
	else
	{
		height=50;
		showProgress(vardivid);
	}

	if (xmlHttp.responseText.search('Login_Successful') != -1) 
	{
		var url = xmlHttp.responseText;
		var urllength = url.length;
		//var urlindex = url.lastIndexOf('Login_Successful');
		var urlindex = url.lastIndexOf('http://');
		url = url.substr(urlindex, urllength);
		window.location = url;
	}
//if (xmlHttp.responseText.length!=34)
}


//Existing User going for email-alerts and not logged in
function UNavigate(loginform,bounceval,user_isloggedin){
	//document.write(loginform+"="+bounceval+"="+user_isloggedin);
	var strlemail = $('lemail').value;
	var strlpassword = $('lpassword').value;
	var emailstatval=$('has_email_alerts').value;
	var isexchnguser=$('exchangeuser').value;
	$('headerbar').hide();
	//if emailstatval = 1 the user has email_alert already
	//if emailstatval = 2 the user wants email_alert
	//if emailstatval = 0 the user is new
	//if emailstatval=2 and isexchnguser=0 may be an old minyanville user who has no exchange

	if(emailstatval==1){
	bounceval="page=emailsetting";
	chkLogin(strlemail, strlpassword, bounceval);
	}else if(emailstatval==2 && isexchnguser==1){
		bounceval="page=emailsetting&userfor=emailalerts&gotomanagesetting=2&exchangeuser=1";
		chkLogin(strlemail, strlpassword, bounceval);
	}else if(emailstatval==2){
	var statbody="lemail="+strlemail+"&lpassword="+strlpassword+"&useris=existing";
	///chkLogin(strlemail, strlpassword, bounceval);
	IntermediateReg(statbody,bounceval,"email_screen_2.htm");
	}
	// This is the rare case if it comes then ajax is not working properly to set hidden field value
	
}

/**
* Validate user login credentials
*
* @Created By Maverick
*/ 

function validateUserRegistration(loginForm, val,userIsLoggedIn)
{
		var str = isValidEmail('lemail');
	if(str == false)
	{
		return false;
	}else if(($('lemail').value=='')){
		alert("Email field can not be left blank.");
		$('lemail').focus();
		return false;
	}else if(($('lpassword').value=='')){
		alert("Password field can not be left blank.");
		$('lpassword').focus();
		return false;
	}
	height=10;
        var email = $('lemail').value;
	var password = $('lpassword').value;
	var url = host + '/community/exchange_register/welcome.htm?email=' + email + '&password=' + password  + '&op=validateRecord&';
	new Ajax.Request(url, {
  	method: 'get',
	onLoading: showProgress1('errormsg'),
  	onSuccess: function(transport) {
  	    	if (transport.responseText.search('validation') == -1) //Invalid record
				{
					$('errormsg').innerHTML = "<span style='color:red;'>"+transport.responseText+"</span>";
					$('errormsg').show();
				}else
				{

					if(transport.responseText.match('exchange_user')){
					$('exchangeuser').value=1;
					}else{
					$('exchangeuser').value=0;
					}

					if(transport.responseText.match('hasemail')){
						$('has_email_alerts').value=1;
					}else{
					$('has_email_alerts').value=2;
						if($('exchangeuser').value==1){
						$('has_email_alerts').value=1;
						}
					}
					UNavigate(loginForm, val,userIsLoggedIn);
				}
        }
	});
}
function showProgress1(divid){
		var x = $(divid);
		x.innerHTML = '<div id="showprogress" style="align:center;"><table border="0" width="100%" style="align:center;padding-top:'+ height +'px"><tr><td style="text-align:right;vertical-align:middle;padding-top:0px;padding-bottom:0px;">Loading...</td><td style="text-align:left;padding-top:0px;vertical-align:middle;"><img src="'+image_server+"/images/community_images/spinner.gif" style="vertical-align:middle;"></td></tr></table></div>';
}