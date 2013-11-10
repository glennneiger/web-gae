
function iboxFeedemail(emailFieldId,feed_user_id)
{
	var strlemail = $(emailFieldId).value;
	var strluserid = $(feed_user_id).value;
	var isvalidEmail=isValidEmail(emailFieldId);

	
	if(isvalidEmail)
	{
		processFeedemail(strlemail, strluserid);
	}
}

function processFeedemail(email,userid)
{
	var url = host+'/subscription/register/registration_ajax.php';	
	var pars="type=daily_feed_newsletter&checkemail="+email+"&sessuserid="+userid;
	
	var myAjax4 = new Ajax.Request(url, {method: 'post',
		parameters: pars,
		//onLoading:loading(errDiv),
		onComplete:function getUnsubscribeStatus(req)
		{
		var post = eval('('+req.responseText+')');
		if(post.status==true)
		{
			window.location.href= host +"/dailyfeed/df_newsletter_success.htm";
		}
		else
		{
			alert(post.msg);
			return false;
		}
	}});
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
	
	/*for(var i=0; i < document.email_category.category.length; i++){
	if(document.email_category.category[i].checked)
	total +=document.email_category.category[i].value + ","
	}*/

	jQuery('#category:checked').each(function() {
		total +=jQuery(this).val() + ","
	 });

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
	var url = host+'/mvpremium/feed_newsletter_ajax.php';
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