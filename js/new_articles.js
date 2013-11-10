var xmlHttp
var vardiv


function shownewarticles(is_exchange_user,login_user_id,event,unreadartids){
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  }
  var url=host + '/community/new_articles.htm?subscriber_id=' + login_user_id+'&text_pass='+event+'&unreadartids='+unreadartids;
  showarticledetails(url);
}

function showarticledetails(url)
{
height=0;
vardiv='articledetail';
targetdiv='cntrl_pnl_updates';
xmlHttp.open("GET",url,true);
xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
xmlHttp.send(null);
}

function handler1(url,type,is_exchange,author_id,login_user_id,article_id,unreadartids){
	var urlpassed=url;
	var typepassed=type;

	if(typepassed=='view')
	{
		window.location.href = urlpassed+"?author="+author_id+"&articleid="+article_id+'&unreadartids='+unreadartids;
		return true;
	}else if(typepassed=='read'){
//		alert(url+" "+type+" "+is_exchange+" "+author_id+" "+login_user_id+" "+article_id+" "+unreadartids);
		var urlartsub=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=read&article_mark_id='+article_id+'&subscribed_to='+author_id+'&unreadartids='+unreadartids;
		showarticledetails(urlartsub);
	}else if(typepassed=='unread'){
		var urlartsub=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=unread&article_mark_id='+article_id+'&subscribed_to='+author_id+'&unreadartids='+unreadartids;
		showarticledetails(urlartsub);
	}
	else{
		return;
	}
}