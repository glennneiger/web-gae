var xmlHttp
var vardiv

<!--Code for Add to friends-->
function exchangeuser(is_exchange,article_id,receiver_id,lang_text,comment_id,postid,subscription_id,iddiv) 
{  
  if(is_exchange==1)
 	{ 
	 addfriends(article_id,receiver_id,lang_text,comment_id,postid,subscription_id,iddiv);
   } 
}

function addfriends(article,subid,lang_text,comment_id,postid,subscription_id,iddiv)
{ 
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	height=20;
	var url=host + '/articles/addfriend_request.htm?article=' + article + '&subid=' + subid + '&langtext=' + lang_text + '&comment_id=' + comment_id+ '&postid=' + postid + '&subscription_id=' +subscription_id;
	if(iddiv=='0'){
		var vardiv='sendrequest0';
	}else if(iddiv=='1'){
		var vardiv='sendrequest1';
	}else if(iddiv=='2'){
		var vardiv='sendrequest2';
	}else if(iddiv=='3'){
		var vardiv='sendrequest3';
	}else if(iddiv=='4'){
		var vardiv='sendrequest5';
	}else{
	var vardiv='sendrequest';
	}
	
	xmlHttp.open("GET",url,true);
	
	xmlHttp.onreadystatechange=function() { stateChange(vardiv,0); };
	xmlHttp.send(null);
}  <!--Code end for Add to friends-->

<!--Code for approverequest-->
function exchangeapproverequest(is_exchange,receiver_id,sender_id) 
{ 
var flgapprovedeny='1';
  if(is_exchange==1)
 	{ 
	 approvedenyfriends(receiver_id,sender_id,flgapprovedeny);
    } 
} 

<!--Code for denyrequest-->
function exchangedenyrequest(is_exchange,receiver_id,sender_id) 
{  
var flgapprovedeny='2';
  if(is_exchange==1)
 	{ 
	 approvedenyfriends(receiver_id,sender_id,flgapprovedeny);
    } 
}
<!--end function for denyrequest-->

function approvedenyfriends(receiver_id,sender_id,flgapprovedeny)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
height=20;
var url= host + '/community/approvefriend_request.htm?receiver_id=' + receiver_id + '&sender_id=' + sender_id + '&flgapprovedeny=' + flgapprovedeny;
var vardiv='approverequest';
targetdiv='cntrl_pnl_requests';
xmlHttp.open("GET",url,true);
xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
xmlHttp.send(null);
}

<!--code for pending request-->
function showrequest(is_exchange,receiver_id,sender_id) 
{  
  if(is_exchange==1)
 	{ 
	 var url= host + '/community/approvefriend_request.htm?receiver_id=' + receiver_id + '&sender_id=' + sender_id;
	 showfriendsrequest(receiver_id,sender_id,url);
	 
    } 
}

<!--code for hide on click on +-->

function showrequesthide(is_exchange,receiver_id,sender_id, varurl)  
{  
  if(is_exchange==1)
 	{ 
	 if(!varurl){
	 var url= host + '/community/pendinghiddenfriend_request.htm?receiver_id=' + receiver_id + '&sender_id=' + sender_id;
	 }else {
	  var url= host + '/community/approvefriend_request.htm?receiver_id=' + receiver_id + '&sender_id=' + sender_id;
	 }
	 showfriendsrequest(receiver_id,sender_id,url);
    } 
}

function showfriendsrequest(receiver_id,sender_id,url)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
height=20;
vardiv='approverequest';
targetdiv='cntrl_pnl_requests';
xmlHttp.open("GET",url,true);
xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
xmlHttp.send(null);
}

<!--code end for hide on click on +-->
<!--code for remove friends from friends.htm-->
function remove_friend(is_exchange,subid,id,phpself,offset,invitation_send,remove_friend,remove_name,sessionid) 
{   
  if(is_exchange==1)
 	{  
	height=20;
	 var url=host + '/community/friends_list.htm?subid=' + subid + '&id=' + id + '&is_exchange=' + is_exchange + '&phpself=' + phpself + '&offset=' + offset + '&invitation_send=' + invitation_send + '&remove_friend=' + remove_friend + '&remove_name=' + remove_name + '&sessionid=' + sessionid;
	 xmlHttp=GetXmlHttpObject();
	 if (xmlHttp==null)
	  {
  		alert ("Your browser does not support AJAX!");
  		return;
  	 } 

	vardiv='removefriend';
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(vardiv); };
	xmlHttp.send(null);
    } 
}

<!--code end for remove friends from friends.htm-->
<!--code for send invation from friends.htm-->

function send_invitation(is_exchange,id,phpself,offset,invitation_send,email_error_message,remove_friend,sessionid) 
{  
 if(document.getElementById('to_email').value!==''){
     if(isValidEmail(document.getElementById('to_email'))==false)
	 	return false;
 	 var to_email = document.getElementById('to_email').value;
	 var strinvitation='1';
     height=20;
     var url=host + '/community/friends_list.htm?receiver_email=' + to_email + '&is_exchange=' + is_exchange + '&id=' + id + '&phpself=' + phpself + '&offset=' + offset + '&strinvitation=' + strinvitation + '&invitation_send=' + invitation_send + '&email_error_message=' + email_error_message + '&remove_friend=' + remove_friend + '&sessionid=' + sessionid;
	 xmlHttp=GetXmlHttpObject();
	 if (xmlHttp==null)
	 	 {
  			alert ("Your browser does not support AJAX!");
  			return;
  	 	} 
		vardiv='removefriend';
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { stateChange(vardiv); };
		xmlHttp.send(null);
	} else {
	 alert('Please enter email-id.');
	}
}
<!--code for send invitation -->

<!--code for report abuse-->
function preHttpRequestURL(isexchangeuser, subid, commentid, article_id, poster_id, pagename, strAttribute,iddiv)
{  
	if(isexchangeuser==1)
 	{ 
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		} 
		height=20;
		var url= host + '/articles/addfriend_request.htm?subscription_id=' + subid + '&comment_id=' + commentid + '&itemtype=comments&ra=true&article=' + article_id + '&poster_id=' + poster_id + '&pagename=' + pagename + '&strAttribute=' + strAttribute;
		if(iddiv=='0'){
			var vardiv='sendabuse0';
		}else if(iddiv=='1'){
			var vardiv='sendabuse1';
		}else if(iddiv=='2'){
			var vardiv='sendabuse2';
		}else if(iddiv=='3'){
			var vardiv='sendabuse3';
		}else if(iddiv=='4'){
			var vardiv='sendabuse4';
		}else{
			var vardiv='sendreport';
		}
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange=function() { stateChange(vardiv,0); };
		xmlHttp.send(null);
	}
}
<!--code for report abuse-->
