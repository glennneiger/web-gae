var vardiv;
var xmlHttp;

function expand_blogs(is_exchange_user,login_user_id,unreadids){
xmlHttp=GetXmlHttpObject();

if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  }
  var urlblogsub='showblog_request.htm?subscriber_id=' + login_user_id+'&text_pass=expand&unreaids='+unreadids;
  showblogs(urlblogsub);
}

function showblogs(urlblogsub)
{ 
height=0;
//alert(url);
vardiv='blogdetail';
targetdiv='cntrl_pnl_updates';
xmlHttp.open("GET",urlblogsub,true);
xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
xmlHttp.send(null);
}

function handler(url,type,is_exchange,subscribed_to,login_user_id,blog_id,unreaids){
		//alert(" url "+url+" type "+type+" is_exchange "+is_exchange+" subscribed_to "+subscribed_to+" login_user_id "+login_user_id+" blog_id "+blog_id);;
		var urlpassed=url;
		var typepassed=type;

		if(typepassed=='view')
		{
			window.location.href = urlpassed+"?blog_id="+blog_id;
			return true;
		}else if(typepassed=='read'){
				var urlblogsub=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=read&blog_mark_id='+blog_id+'&subscribed_to='+subscribed_to+'&unreaids='+unreaids;
				//alert(url);
				showblogs(urlblogsub);
		}
		else if(typepassed=='unread'){
			var urlblogsub=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=unread&blog_mark_id='+blog_id+'&unreaids='+unreaids;
			showblogs(urlblogsub);
		}
		else if(typepassed=='unsubscr'){
		if(confirm("Are u Sure to Unsubscribe this BLOG?")){
		var urlblogsub='';	
		urlblogsub=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=unsubscribe&blog_unsub_id='+subscribed_to+'&unreaids='+unreaids+'&blog_mark_id='+blog_id;
		showblogs(urlblogsub);
		}
		}
		else{return;}
	}
	function collapse_blogs(is_exchange_user,login_user_id,unreadids){
		var urlblogsub='showblog_request.htm?subscriber_id=' + login_user_id+'&text_pass=collapse&unreaids='+unreadids;
		showblogs(urlblogsub);
	}
	
	function hidecomments(id)
    {
		var objHideComment=document.getElementById('hidecomments');
		var objShowComment=document.getElementById('showcomments');
		var obj = document.getElementById(id);
		if (objHideComment.style.display == "none"){
			objHideComment.style.display="";
		}else{
			objHideComment.style.display="none";
		}
		if (objShowComment.style.display == "none"){
			objShowComment.style.display="";
		}else{
			objShowComment.style.display="none";
		}
		if(obj){
			if (obj.style.display == "none"){
				obj.style.display="";
			}else{
				obj.style.display="none";
			}
		}
     }