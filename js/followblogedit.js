function modifystructure(userid,authorsids,path){
	var uid=userid;
	var div;
	var authrarr = new Array();
	authrarr = authorsids.split(',');
	for( var i = 0; i<authrarr.length; i++ ) {
		var removeid=authrarr[i];
		idvsetid="subauthlnk_"+authrarr[i];
		document.getElementById(idvsetid).innerHTML=" &nbsp;&nbsp;<a class='Blogpanal1' style='cursor:pointer' onclick='javascript:removeid("+uid+","+removeid+")'>Remove</a>";
	}
	document.getElementById('editlnk').innerHTML='<table border=0 width=100% cellpadding=0 cellspacing=0><tr><td width="53%" vAlign="center"><span class="homerequests" style="padding-left:10px;"><input align="absmiddle" type="text" id="to_email" style="height:22px;"></span></td><td  vAlign="center"><input type="image" style="border:hidden;" src="'+path+'/images/community_images/blog_subscribe.gif" onChange="validateemail('+userid+','+authorsids+')" onClick="validateemail('+userid+','+authorsids+')" onLoad="setfocus()" align="absmiddle"></td></tr><tr><td colspan="2">&nbsp;</td></tr></table>';
}
function setfocus(){
	document.getElementById('to_email').focus();
}

  function echeck(str) {
	var at="@"
    var dot="."
	var lat=str.indexOf(at)
	var lstr=str.length
	var ldot=str.indexOf(dot)
	if (str.indexOf(at)==-1){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.indexOf(at,(lat+1))!=-1){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.indexOf(dot,(lat+2))==-1){
	alert("Invalid E-mail ID")
	return false
	}

	if (str.indexOf(" ")!=-1){
	alert("Invalid E-mail ID")
	return false
	}
	return true
}


function validateemail(userid,authorsids){
	var useridpass=userid;
	var authridspass=authorsids;
	var subscribe_to='';
	var event=0;
	
	var emailID=document.getElementById('to_email');
	subscribe_to=emailID.value;
		if ((emailID.value==null)||(emailID.value=="")){
		alert("Please Enter Email ID")
		emailID.focus()
		return false
		}
		if (echeck(emailID.value)==false){
		emailID.value=""
		emailID.focus()
		return false
	}
		event=1;
//		return true;
		doajaxworks(useridpass,subscribe_to,event);
}

var xmlHttp
var contntdiv

function doajaxworks(login_user_id,subscribe_to,event){
	xmlHttp=GetXmlHttpObject();

	if(event=='1'){
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		}
		var url=host + '/community/newblogsubscribe_chk.htm?subscriber_id='+login_user_id+'&text_pass='+event+"&newid="+subscribe_to;
		passforsubscription(url);
	}else if(event==0){
		var url=host + '/community/newblogsubscribe_chk.htm?subscriber_id='+login_user_id+'&text_pass='+event+"&unsubscribe_to="+subscribe_to;
		//alert(url);
		passforsubscription(url);
	}
}
function passforsubscription(url)
{
	contntdiv='followsubscrbd';
	height=100;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}


function removeid(userid,authrid){
	var login_user_id=userid;
	var unsubscribe_to=authrid;
	if(confirm("Are you sure to remove this?")){
	//alert(userid+" Wnats to remove "+authrid);
	var event=0;
	doajaxworks(login_user_id,unsubscribe_to,event);
	}
}
	function makenextlinks(user_login_id,newemailchk,msg,unsubscribeid,start,end){
		//alert(user_login_id+"  ******** "+newemailchk+" ******** "+msg+" ******** "+unsubscribeid+" ******* "+start+" ******** "+end);
		links='next';
		doajaxworks1(user_login_id,newemailchk,msg,unsubscribeid,start,end,links);
	}

	function makeprevlinks(user_login_id,newemailchk,msg,unsubscribeid,start,end){
		links='prev';
		//alert(user_login_id+"  ******** "+newemailchk+" ******** "+msg+" ******** "+unsubscribeid+" ******* "+start+" ******** "+end);
		doajaxworks1(user_login_id,newemailchk,msg,unsubscribeid,start,end,links);
	}
	

	function doajaxworks1(user_login_id,newemailchk,msg,unsubscribeid,start,end,links){
		xmlHttp=GetXmlHttpObject();
	
			if (xmlHttp==null)
			{
				alert ("Your browser does not support AJAX!");
				return;
			}
			var url=host + '/community/newblogsubscribe_chk.htm?subscriber_id='+user_login_id+'&text_pass='+links+'&start='+start+"&end="+end;
			//alert(url);
			passforsubscription(url);
}