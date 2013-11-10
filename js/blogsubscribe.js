var xmlHttp
var contntdiv

   function doajaxfunctionalities(guest_id,subscribe_to,event,tabid){
	xmlHttp=GetXmlHttpObject();

		if (xmlHttp==null)
		{
			alert ("Your browser does not support AJAX!");
			return;
		}

		var url=host + '/community/usersblog.htm?guest_id='+guest_id+'&user_login_id='+subscribe_to+'&text_pass='+event+'&tabid='+tabid;
		callforsubscription(url);
	
}
function callforsubscription(url)
{
	contntdiv='statusdiv';
	height=0;
	xmlHttp.open("GET",url,true);
	xmlHttp.onreadystatechange=function() { stateChange(contntdiv,0); };
	xmlHttp.send(null);
}

function subscribe(guest_id,user_login_id){
	if((user_login_id=='')||(guest_id=='')){
		return false;
	}
	else{
		doajaxfunctionalities(guest_id,user_login_id,'new',0);
	}
}
function subscribe1(guest_id,user_login_id,action,retid){
	var guest_id=guest_id;
	var user_login_id=user_login_id;
	var event=action;
	var tabid=retid;
		if(event=='remove'){
			if(confirm("Are you sure to Unsubscribe This Author ?")){
				doajaxfunctionalities(guest_id,user_login_id,event,tabid);
			}
		}else{
		doajaxfunctionalities(guest_id,user_login_id,event,tabid);
		}
}