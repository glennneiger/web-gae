host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;

// trim a string
function softTrialTrim(str){	
		
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
		
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
		
	return str;
}

// allow only characters
function validateSoftTrialChars(value) {

	var string = softTrialTrim(value);
    var bVal = true;
	if (!string){	
		bVal = false;
	}
	
    var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1){
		     bVal = false; 
		}
    }
	
	return bVal;
	
} 	

// validate email address
function softTrialValidEmail(errorDiv,emailFieldId){
	var bools=true;
	var emails=$(emailFieldId).value.split(';');

	for (var i=0;i<emails.length;i++){
	
		if(emails[i]==''){
			$(errorDiv).innerHTML='Email field can not be left blank.'; 
			$(emailFieldId).select();
			bools=false;
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i]))){
			var erormsg = 'Not a valid E-mail id "'+emails[i]+'"';
			$(errorDiv).innerHTML=erormsg;
			$(emailFieldId).select();
			bools=false;
			return false;
		}
	}
	$(errorDiv).innerHTML='&nbsp;';
	return bools;
}	

// validate soft trials user inputs
// subscription will be name of subscription 
// buzz/cooper/flexfolio/optionsmith/jacklavery
function validateSoftTrial(subscription,period,trackname,refererSourceId){
	googleAnalytics('1',trackname);
	var tracklen=trackname.length;
	var strpos=trackname.indexOf("Landed");
    var tracknamewel=trackname.substring(0,strpos) + 'Welcome' + trackname.substring(strpos+6,tracklen);
	var url='';
	var pars='';
	var curtime=new Date().getTime();
	
	if(softTrialTrim($('fname').value)==''){
		$('errmsg').innerHTML='Enter First Name.';
		$('fname').select();
		return false;		
	}
	
	if(validateSoftTrialChars(softTrialTrim($('fname').value))==false){
		$('errmsg').innerHTML='Enter Valid First Name.';
		$('fname').select();
		return false;		
	}
	
	if(softTrialTrim($('lname').value)==''){
		$('errmsg').innerHTML='Enter Last Name.';
		$('lname').select();
		return false;		
	}	
	
	if(validateSoftTrialChars(softTrialTrim($('lname').value))==false){
		$('errmsg').innerHTML='Enter Valid Last Name.';
		$('lname').select();
		return false;		
	}	
	
	if(softTrialValidEmail('errmsg','uid')==false){
		$('uid').select();
		return false;
	}
	
	if(softTrialTrim($('pwd').value)==''){
		$('errmsg').innerHTML='Password field can not be left blank.';
		$('pwd_t').select();
		return false;
	}
	
	if(softTrialTrim($('pwd').value)!=softTrialTrim($('repwd').value)){
		$('errmsg').innerHTML='Password and Re password does not match.';
		$('pwd').select();
		return false;
	}
	
	url=host+'/subscription/softtrials/ajaxcontroller.php';
	
	pars = pars+'type=doregister';
	pars = pars+'&subscription='+subscription;
	pars = pars+'&days='+period;	
	pars = pars+'&firstname='+softTrialTrim($('fname').value);
	pars = pars+'&lastname='+softTrialTrim($('lname').value);	
	pars = pars+'&uid='+softTrialTrim($('uid').value);
	pars = pars+'&pwd='+softTrialTrim($('pwd').value);
	pars = pars+'&refererSourceId='+refererSourceId;

	if(softTrialTrim($('promocode').value)=='' || softTrialTrim($('promocode').value)=='Enter Promo Code (Optional)'){
		//Do nothing
	}
	else{
		pars = pars+'&promocode='+softTrialTrim($('promocode').value);
	}
	pars = pars+'&timestamp='+curtime;
	pars = pars +'&trackname='+trackname;
	$('errmsg').innerHTML='Loading...';
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
		
		post = eval('('+req.responseText+')');
		if(post.status==false){
			$('errmsg').innerHTML=softTrialTrim(post.msg);
			return false;
		}
		else{
			<!-- GA code to track -->
				var trackname=post.trackname;
				//googleAnalytics('1',trackname);
				$('errmsg').innerHTML='&nbsp;';
				window.location.href=host+'/subscription/softtrials/welcome.htm?trackname='+ encodeURIComponent(tracknamewel);
		/*	curtime=new Date().getTime();
			pars='';
			pars = pars+'type=softtrials_emails';
			pars = pars+'&subscription='+subscription;
			pars = pars+'&days='+period;	
			pars = pars+'&name='+softTrialTrim($('fname').value);
			pars = pars+'&lastname='+softTrialTrim($('lname').value);	
			pars = pars+'&email='+softTrialTrim($('uid').value);
			pars = pars+'&pd='+post.pd;
			pars = pars+'&timestamp='+curtime;
			pars = pars +'&trackname='+trackname;
			
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
             post = eval('('+req.responseText+')'); */
		/*	if(post.status==true){
				<!-- GA code to track -->
				var trackname=post.trackname;
				//googleAnalytics('1',trackname);
				$('errmsg').innerHTML='&nbsp;';
				window.location.href=host+'/subscription/softtrials/welcome.htm?trackname='+ encodeURIComponent(tracknamewel);
			} 
			
		}//end of finishLogin
		});*/
	
		}
	}//end of finishLogin
	});
	
}
function signup(){
	$('fname').focus();
}
function convertTextToPass(text_id,pass_id,action)
{
if(action == 'text')
	{
		document.getElementById(pass_id).style.display='none';
		document.getElementById(text_id).style.display='';
		//document.getElementById(text_id).focus();
	}
	else
	{
		document.getElementById(text_id).style.display='none';
		document.getElementById(pass_id).style.display='';
		document.getElementById(pass_id).focus();
	}
}
function checkPassNull(text_id,pass_id)
{
	var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.
	str = document.getElementById(pass_id).value;
	if((str==''))
	{
		convertTextToPass(text_id,pass_id,'text')
		return false;
	}
	return false
}