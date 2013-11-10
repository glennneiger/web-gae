// allow only characters
function validateSoftTrialChars(value) {

	var string = jQuery.trim(value);
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
	emailFieldId="#"+emailFieldId;
	var emails=jQuery(emailFieldId).val().split(';');

	for (var i=0;i<emails.length;i++){
	
		if(emails[i]==''){
			jQuery(errorDiv).html('Email field can not be left blank.'); 
			jQuery(emailFieldId).select();
			bools=false;
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i]))){
			var erormsg = 'Not a valid E-mail id "'+emails[i]+'"';
			jQuery(errorDiv).html(erormsg);
			jQuery(emailFieldId).select();
			bools=false;
			return false;
		}
	}
	jQuery(errorDiv).innerHTML='&nbsp;';
	return bools;
}	

// validate soft trials user inputs
// subscription will be name of subscription 
// buzz/cooper/flexfolio/optionsmith/jacklavery
function validateSoftTrial(subscription,period,refererSourceId){
	var url='';
	var pars='';
	var curtime=new Date().getTime();	
	if(jQuery.trim(jQuery('#FirstName').val())==''){
		jQuery('#errmsg').html('Enter First Name.');
		jQuery('#FirstName').focus();		
		return false;		
	}
	
	if(validateSoftTrialChars(jQuery.trim(jQuery('#FirstName').val()))==false){
		jQuery('#errmsg').html('Enter Valid First Name.');
		jQuery('#FirstName').focus();
		return false;		
	}
	
	if(jQuery.trim(jQuery('#LastName').val())==''){
		jQuery('#errmsg').html('Enter Last Name.');
		jQuery('#LastName').focus();
		return false;		
	}	
	
	if(validateSoftTrialChars(jQuery.trim(jQuery('#LastName').val()))==false){
		jQuery('#errmsg').html('Enter Valid Last Name.');
		jQuery('#LastName').focus();
		return false;		
	}	
	
	if(softTrialValidEmail('#errmsg','Email')==false){
		jQuery('#Email').select();
		return false;
	}
	
	if(jQuery.trim(jQuery('#Password').val())==''){
		jQuery('#errmsg').html('Password field can not be left blank.');
		jQuery('#Password').select();
		return false;
	}
	
	url=host+'/subscription/softtrials/ajaxcontroller.php';	
	pars = pars+'type=doregisterbuzz';
	pars = pars+'&subscription='+subscription;
	pars = pars+'&days='+period;	
	pars = pars+'&firstname='+jQuery.trim(jQuery('#FirstName').val());
	pars = pars+'&lastname='+jQuery.trim(jQuery('#LastName').val());	
	pars = pars+'&uid='+jQuery.trim(jQuery('#Email').val());
	pars = pars+'&pwd='+jQuery.trim(jQuery('#Password').val());
	pars = pars+'&refererSourceId='+refererSourceId;	
	pars = pars+'&timestamp='+curtime;
	pars = pars+'&promocode='+jQuery.trim(jQuery('#promocode').val());
	jQuery('#errmsg').html('Loading...');
	jQuery.ajax({
		  url: url,
		  type: 'POST',
		  dataType:'json',
		  data:pars,
		  success: function finishRegister(post){
				if(post.status==false){
					jQuery('#errmsg').html(jQuery.trim(post.msg));
					return false;
				}
				else{
					jQuery('#errmsg').html('');
					window.location.href=host+'/subscription/softtrials/buzz/welcomesem2012.htm';			
				}
	}//end of finishLogin
		});	
}

function convertTextToPass(text_id,pass_id,action)
{
	text_id="#"+text_id;
	pass_id="#"+pass_id;
if(action == 'text')
	{
		jQuery(text_id).hide();
		jQuery(pass_id).show();
		jQuery(text_id).focus();
	}
	else
	{
		jQuery(text_id).hide();
		jQuery(pass_id).show();
		jQuery(pass_id).focus();		
	}
}
function chkSpaceNull(fieldId,str,defMsg)
{
	fieldId="#"+fieldId;
	var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.
	if((str=='')||(str.length==0) || (str==null) || (str.search(re) > -1))
	{
		jQuery(fieldId).val(defMsg);
		return false;
	}
	else
	{
		return true;
	}
}

// trim a string
function softTrialTrim(str){	
		
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
		
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
		
	return str;
}