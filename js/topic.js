
function iboxTopicemail(emailFieldId,feed_user_id,sectionId,section_name)
{
     	var strlemail =document.getElementById(emailFieldId).value;
	    var strluserid = document.getElementById(feed_user_id).value;
		var strlsectionId = document.getElementById(sectionId).value;
		var strlsectionName = document.getElementById(section_name).value;
	    var isvalidEmail=isValidEmail(emailFieldId);
         if(isvalidEmail)
	     {
			processTopicemail(strlemail,strluserid,strlsectionId,strlsectionName);
       }
}

function processTopicemail(email,userid,sectionId,strlsectionName)
{
	
	var url = host+'/subscription/register/loginAjax.php';	
	var pars="type=articletopic_newsletter&checkemail="+email+"&sessuserid="+userid+"&section_id="+sectionId+"&section_name="+strlsectionName;
	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		//onLoading:loading(errDiv),
		onComplete:function getUnsubscribeStatus(req)
		{
		var post = eval('('+req.responseText+')');
		if(post.status==true)
		{
			//iboxclose();
			window.location.href= host +"/topic/topic_newsletter_success.htm?sec_id="+sectionId;
		}
		else
		{
			alert(post.msg);
			return false;
		}
	}});
}


function topicEmailEnterKeyChk(evt,emailFieldId,feed_user_id,sectionId,section_name)
{
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13)
	{
		iboxTopicemail(emailFieldId,feed_user_id,sectionId,section_name);
		
	}
}


function settopicbox(CheckValue)
{
	var array = document.getElementsByTagName("input");
	for(var ii = 0; ii < array.length; ii++)
	{
		if(array[ii].type == "checkbox")
		{
			if(array[ii].className == "topic_select")
			{
			array[ii].checked = CheckValue;
			}
		}
	}
}

function setauthorbox(CheckValue)
{
	var array = document.getElementsByTagName("input");
	for(var ii = 0; ii < array.length; ii++)
	{
		if(array[ii].type == "checkbox")
		{
			if(array[ii].className == "author_select")
			{
				array[ii].checked = CheckValue;
			}
		}
	}
}	 


