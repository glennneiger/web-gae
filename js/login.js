<!--code for registration-->
var xmlHttp
var vardiv

//New user
function NewLogin(sBody)
{	
	xmlHttp=GetXmlHttpObject();
	height=50;
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	var url = host + '/community/register/welcome.htm?' + sBody + '&flag=index';
	vardiv='newregistration';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() 
	{ 
		stateChange(vardiv); 
	};
	xmlHttp.send(null);
}
//New user

//Existing User
function chkLogin(sBody)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	
	var url = host + '/community/register/welcome.htm?' + sBody + '&flag=fromindex' ;
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
function UValidate(loginform){
	var sBody = getRequestBody(loginform);
	var strlemail = document.getElementById('lemail').value;
	var str = isValidEmail('lemail');
	var strlpassword = document.getElementById('lpassword').value;
	if (strlpassword == "")
	{
		alert("Password field can not be left blank.");
		document.getElementById('lpassword').focus( );
		return false;
	}
	else if(str == false)
	{
		return false;
	}
	else if(str = true && strlpassword != "")
	{	
		chkLogin(sBody);
	}
}

function RValidate(regform){

	//var oForm = document.forms[0];
	var sBody = getRequestBody(regform);
	var strremailvalue = document.getElementById('remail').value;
	var strcemailvalue = document.getElementById('rcemail').value;
	var strrpasswordvalue = document.getElementById('rpassword').value;
	var strrcpasswordvalue = document.getElementById('rcpassword').value;
	var strfirstname = validateAlphaField('firstname');
	if(strfirstname)
	{
		var strlastname = validateAlphaField('lastname');	
	}
	if(strlastname)
	{	var strremail = isValidEmail('remail'); }
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
		
		if(regform.rprivacy.checked == false)
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
		NewLogin(sBody);
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
	var homeURL = host + "/community/home.htm";
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

