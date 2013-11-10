
function iboxChkemail(email,errDiv)
{
	var url = host+'/subscription/register/unsubscribe_ajax.php';
	var pars="&checkemail="+email;

	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		onLoading:loading(errDiv),
		onComplete:function getUnsubscribeStatus(req)
		{
		var post = eval('('+req.responseText+')');
				
		if(post.status==true)
		{
			iboxclose();
			
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

function iboxUnsubscribe(emailFieldId,errorDiv)
{
	
	var strlemail = $(emailFieldId).value;
	var isvalidEmail=iboxisValidEmail(errorDiv,emailFieldId);
	
	if(isvalidEmail)
	{
		iboxChkemail(strlemail,errorDiv);
	}
}