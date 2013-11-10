// This file is used to set the value of has_email_alerts either 0/1/2
// 0 is for new user
// 1 is for existing user with email-alerts
// 2 is for existing user with no email-alerts
var host=window.location.protocol+"//"+window.location.host;
var xmlHttpobj
var contntdiv
var page
var isexchange
var isemailsubscbd
var isloggedin

function CreatexmlHttpobjObject()
{
	var xmlHttpobj=null;
	try
	{
  // Firefox, Opera 8.0+, Safari
		var xmlHttpobj=new XMLHttpRequest();
	}
	catch (e)
	{
  // Internet Explorer
		try
		{
			var xmlHttpobj=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			var xmlHttpobj=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttpobj;
}   

function emailcheck(str) {
	str=trim(str);
	var at="@"
		   var dot="."
				   var lat=str.indexOf(at)
						   var lstr=str.length
									var ldot=str.indexOf(dot)
											 if (str.indexOf(at)==-1){
		alert("Not an valid email id")
		return false
	}

	if(str.indexOf(dot)==str.length){
		alert("Not an valid email id")
		return false
	}

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		alert("Not an valid email id")
		return false
	}

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr-1){
		alert("Not an valid email id")
		return false
	}

	if (str.indexOf(at,(lat+1))!=-1){
		alert("Not an valid email id")
		return false
	}

	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		alert("Not an valid email id")
		return false
	}

	if (str.indexOf(dot,(lat+2))==-1){
		alert("Not an valid email id")
		return false
	}

	if (str.indexOf(" ")!=-1){
		alert("Not an valid email id")
		return false
	}
	return true
}


function emailvalidation(formname,fieldid){
	var usermailid='';
	var event=0;
	var emailID=document.getElementById(fieldid);
	usermailid=emailID.value;
		if ((emailID.value==null)||(emailID.value=="")){
		alert("Please Enter Email ID");
		emailID.value=""
		return false
		}
		else if (emailcheck(emailID.value)==false){
		emailID.value=""
		emailID.focus();
		return false
		}
		event=1;
		if(event==1){
		// if user id automatical mns logged in right panel
		isexchange=document.getElementById('isexchange').value;
		isemailsubscbd=document.getElementById('isemailalerts').value;
		isloggedin=document.getElementById('isloggedin').value;
		// try to get the user details as he is not logged in
		if(isloggedin==0){
		// the user is in our user list or/not
		// if the user is an existing then check whether he has subscribed email-id or not 0/1
		// if he is a new user then just pass email id only	
		chkthruajax(usermailid);
		}
		// just pass as the user logged in
		else{
		//******************document.getElementById("navlink").setAttribute("href","<?=$HTPFX.$HTHOST;?>/community/exchange_register/index.htm?from="+page+"&isloggedin="+isloggedin+"&is_exchang="+isexchange+"&is_email="+isemailsubscbd+"&user_type=existing");
		}
		
		}
		
}


function chkthruajax(usermailid){
	xmlHttpobj=CreatexmlHttpobjObject();

	if (xmlHttpobj==null)
		{
		alert ("Your browser does not support AJAX!");
		return;
		}
		var url=host+'/community/useremail_chk.htm?useremail='+usermailid;
		passforcheck(url,usermailid);
}


function passforcheck(url,usermailid)
{
	contntdiv="";
	xmlHttpobj.open("GET",url,true);
	xmlHttpobj.onreadystatechange=function() { stateChanged(contntdiv,usermailid); };
	xmlHttpobj.send(null);
}

function stateChanged(vardivid,usermailid) 
{
	if (xmlHttpobj.readyState==4)
	{  
		if (xmlHttpobj.status==200)
		{
		
		// if xmlHttpobj.responseText!=0 user is an existing
		// else he is a new user then just pass email id only	

			if(xmlHttpobj.responseText!=0){
	///		document.getElementById("navlink").setAttribute("href","<?=$HTPFX.$HTHOST;?>/community/exchange_register/index.htm?from="+page+"&isloggedin="+isloggedin+"&user_type=existing&emailidpass="+usermailid+""+xmlHttpobj.responseText);
			var result=(xmlHttpobj.responseText).split(",");
			var hasemail_detail=result[5].split("=");
			var hasexchange_detail=result[4].split("=");

			//document.getElementById("user_hasemailalert").value=hasemail_detail[1];
			
			if(hasemail_detail[1]==1){
			var managval=1;
			}else if(hasemail_detail[1]==0){
			var managval=2;
			}
			document.getElementById("has_email_alerts").value=managval;
			document.getElementById("exchangeuser").value=hasexchange_detail[1];
			}else if(xmlHttpobj.responseText==0){
			//************document.getElementById("navlink").setAttribute("href","<?=$HTPFX.$HTHOST;?>/community/exchange_register/index.htm?from="+page+"&isloggedin="+isloggedin+"&is_exchang="+isexchange+"&is_email="+isemailsubscbd+"&user_type=new&emailidpass="+usermailid);
			document.getElementById("has_email_alerts").value=0;
			document.getElementById("exchangeuser").value=0;
			}
			return true;
		}
	}
}


function focused(obj) {
    if (obj.value == obj.defaultValue) {
        obj.value = "";
    }
}

function blurred(obj) {
    if (obj.value == "") {
        obj.value = obj.defaultValue;
    }
}

function manilupateClickEmailAlert(url, checkLogin)
{
	var passedUrl=url;
	
	var emailAdd=$F("emailAddress");
	if(trim(emailAdd)=='Enter email Address')
	{
		emailAdd='';
	}
	if(checkLogin==0)
	{
		passedUrl=passedUrl+'&emailaddress='+emailAdd;
		document.getElementById("navlink_go").setAttribute("href",passedUrl);
		document.getElementById("navlink_1").setAttribute("href",passedUrl);
	}
	init_ibox();
}



function onBlurrGetText(element) {
    if (element.value == "") {
        element.value = element.defaultValue;
    }
}

function trim(str)
{
	
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
	
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
	
	return str;
}

// newsletter functions-

function emailNewsLetter(emailFieldId,alertemailid,type)
{
	var strlemail = jQuery('#'+emailFieldId).val();
	var strluserid = jQuery('#'+alertemailid).val();
	var isvalidEmail=isValidEmail(emailFieldId);

	if(isvalidEmail)
	{
		processAlertemail(strlemail,strluserid,type);
	}
}

function processAlertemail(email,userid,type)
{
	var url = host+'/subscription/register/loginAjax.php';	
	var pars="type="+type+"&checkemail="+email+"&sessuserid="+userid;
	jQuery.ajax({
	   type: "POST",
	   url: url,
	   data:pars,
	   success: function getUnsubscribeStatus(req){
		 	var post = eval('('+req+')');
			if(post.status==true)
			{
				window.location.href= host +"/offers.htm";
			}
			else
			{
				alert(post.msg);
				return false;
			}
	   }
	 });

	
}



function iboxEmailcategory(catFieldId,uid,errorDiv)
{
	
	var strlcat = $(catFieldId).value;
	var struid = $(uid).value;
	
	if(struid == '') {
	alert("Please login to subscribe to email newsletters");
	return false;
	}
	var total=""
	
	for(var i=0; i < document.email_category.category.length; i++){
	if(document.email_category.category[i].checked)
	total +=document.email_category.category[i].value + ","
	}
	
	if(total=="") {
	alert("Select Categories");
	return false;
	}
	else {
	processEmailcategory(total, struid,errorDiv);
	}
		
	
}

function processEmailcategory(email,uid,errDiv)
{
	var url = host+'/feed_newsletter_ajax.php';
	var pars="&emaillist="+email+"&sessuserid="+uid;
	
	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onLoading:loading(errDiv),
		onComplete:function getUnsubscribeStatus(req)
		{
		
		var post = eval('('+req.responseText+')');
			
		if(post.status==true)
		{	
			$(errDiv).innerHTML = post.msg;
			return false;
		}
		else
		{
			$(errDiv).innerHTML = post.msg;
			return false;
		}
	}});
}

function subscribeNewsletter(){
	window.location.href=host + '/subscription/register/controlPanel.htm';	
}
function dailyfeedStoryPrint(feedid) {
window.open(host + "/dailyfeed/print.php?d="+feedid,"print","width=850,height=550,top=50,left=200,resizable=yes,toolbar=no,scrollbars=yes");
}

function onFocusRemoveText(element)
{
	if(trim(element.value)==element.defaultValue)
		{
			element.value='';
		}
}


function dailyRecapEnterKeyChk(evt,emailFieldId,alertemailid,type)
{
	
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13)
	{
		dailyRecapNewsLetter(emailFieldId,alertemailid,type);
		
	}
}


function dailyRecapNewsLetter(emailFieldId,alertemailid,type)
{
	
	var strlemail = jQuery('#'+emailFieldId).val();
	var strluserid = jQuery('#'+alertemailid).val();
	var isvalidEmail=isValidEmail(emailFieldId);

	if(isvalidEmail)
	{
		dailyRecapProcessAlertemail(strlemail,strluserid,type);
	}
}

function dailyRecapProcessAlertemail(email,userid,type)
{
	var url = host+'/subscription/register/loginAjax.php';	
	var pars="type="+type+"&checkemail="+email+"&sessuserid="+userid;
	jQuery.ajax({
	   type: "POST",
	   url: url,
	   data:pars,
	   success: function getDailyRecapStatus(req){
		 	var post = eval('('+req+')');
			if(post.status=="subscribed"){
			   window.location.href= host +"/offers.htm?dr=1&email="+email;	
			}else if(post.status==true)
			{
				window.location.href= host +"/offers.htm?email="+email;
			}
			else
			{
				alert(post.msg);
				return false;
			}
	   }
	 });

	
}


