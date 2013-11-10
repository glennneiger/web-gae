var xmlHttp
var vardiv
var url_disc1='';

function shownewblogdiscussed(is_exchange_user,login_user_id,event,fixedthredblogids,fixedpostblogids){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}else
	{
	
	var url_disc1=host + '/community/newblogdiscusionsreply.htm?subscriber_id=' + login_user_id+'&text_pass='+event+'&fixthrdids='+fixedthredblogids+'&fixpostids='+fixedpostblogids;
	shownewblogdiscusdetail(url_disc1);
	}
	
}

function shownewblogdiscusdetail(url_disc1)
{
	height=0;
	vardiv='blogdiscussion';
	targetdiv='cntrl_pnl_updates';
//	targetdiv='cntrl_pnl_updates';
	xmlHttp.open("GET",url_disc1,true);
	xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
	xmlHttp.send(null);
}

function handetids1(url,type,is_exchange,login_user_id,t_id,fixedthredblogids,fixedpostblogids){
	var urlpassed=url;
	var typepassed=type;
	if(typepassed=='read'){
		var urltids1=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=read&thrd_mark_id='+t_id+'&fixthrdids='+fixedthredblogids+'&fixpostids='+fixedpostblogids;
		shownewblogdiscusdetail(urltids1);
	}else if(typepassed=='unread'){
		var urltids1=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=unread&thrd_mark_id='+t_id+'&fixthrdids='+fixedthredblogids+'&fixpostids='+fixedpostblogids;
		shownewblogdiscusdetail(urltids1);
	}
	else{
		return;
	}

}