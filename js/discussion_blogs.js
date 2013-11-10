var xmlHttp
var vardiv
var height=0;
function shownewdiscussions(is_exchange_user,login_user_id,event,fixthrdids,fixpostids){
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  }
  var url_disc=host + '/community/newdiscusions.htm?subscriber_id=' + login_user_id+'&text_pass='+event+'&fixthrdids='+fixthrdids+'&fixpostids='+fixpostids;
  showdiscussiondetails(url_disc);
}

function showdiscussiondetails(url_disc)
{ 
height=0;
vardiv='discussiondetail';
targetdiv='cntrl_pnl_updates';
xmlHttp.open("GET",url_disc,true);
xmlHttp.onreadystatechange=function() { statesChanges(vardiv,targetdiv); };
xmlHttp.send(null);
}

function handetids(url,type,is_exchange,login_user_id,t_id,fixthrdids,fixpostids){
	var urlpassed=url;
	var typepassed=type;

	if(typepassed=='read'){
		urltids='';
		var urltids=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=read&thrd_mark_id='+t_id+'&fixthrdids='+fixthrdids+'&fixpostids='+fixpostids;
		showdiscussiondetails(urltids);
	}else if(typepassed=='unread'){
		var urltids=urlpassed+'?subscriber_id=' + login_user_id+'&text_pass=unread&thrd_mark_id='+t_id+'&fixthrdids='+fixthrdids+'&fixpostids='+fixpostids;
		showdiscussiondetails(urltids);
	}
	else{
		return;
	}
	
}