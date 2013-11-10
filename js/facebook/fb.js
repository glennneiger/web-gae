host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;
var fFname;
var fLname;
var fUserId ='';
var arFrnd = new Array();
function authUser(){
	 FB.ensureInit(function() {
		var fbUserId = FB.Facebook.apiClient.get_session().uid; 	
		var sql = "SELECT uid, name,first_name,last_name,pic_small FROM user WHERE uid ="+fbUserId;
		FB.Facebook.apiClient.fql_query(sql, function(result, ex) {
				//Get user details
				var fbUserId = result[0]['uid'];			
				var fbUserName = result[0]['name'];
				var fbFname = result[0]['first_name'];
				var fbLname = result[0]['last_name'];
				var fbImage = result[0]['pic_small'];
				var fbProfileUrl  = result[0]['profile_url']; 
				userMvExists(fbUserId, fbUserName, fbFname, fbLname, fbImage, fbProfileUrl);
			}); 	
				var sql = "select uid2 from friend where uid1="+fbUserId;
				FB.Facebook.apiClient.fql_query(sql, function(result, ex) {
				  for(i=0;i<result.length;i++)
				  {
					  arFrnd[i] = result[i]['uid2']; 
				  }   
			});
	 });
}

function userMvExists(fbId, fbUserName, fbFname, fbLname, fbImage,fbProfileUrl){	
	fFname = fbFname;
	fLname = fbLname;
	fUserId = fbId;
	
	var frndlist = arFrnd.join(',');  
	var url = host+'/lib/facebook/fbhandler.php';
	var parms  = 'fbId='+fUserId+'&action=exists&frndlist='+frndlist;
	var myAjax4 = new Ajax.Request(url, {method: 'post',parameters:parms,onComplete:function(req){		 
		if(req.responseText == 'true'){ //user alreday exists 
			var url = host+'/lib/facebook/fbhandler.php';
			var parms  = 'fbId='+fUserId+'&action=autologin';
			var myAjax4 = new Ajax.Request(url, {method: 'post',parameters:parms,onComplete:function(req){
			// auto login
			if(req.responseText=='true'){
				$('tickerlogin').hide();
				var url=host+'/tickertalk/post.php';
				var show;
				var topicchatid=$('hidTopicId').value
				var pars='showbox=' + show + '&topicchatid=' + topicchatid;
				var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});
				showfilter();		// show combobox
			}  // end of autologin code
				$('fconn').hide();
			 
			
			}// end of ajax request processing
			
			});			
			
		}else{ //ask user for registration
			var reg_url = host+'/lib/facebook/register.htm';
			init_ibox('fb_connect',reg_url);			
			$('ibox_content').setStyle({'backgroundImage':'none'});			
			$('fconn').hide();
		}

		
	}});
}


function validateFbUser(trackname){
	var errorDiv = 'errmsg';		 
	if(isValidEmailfb(errorDiv,'email')==false){		
		$(errorDiv).innerHTML='Invalid Email.';
		$('email').select();
		return false;
	}
	if(tickertrim($('pwd').value)==''){
		$(errorDiv).innerHTML='Enter password.';
		$('pwd').select();
		return false;
	}
	if($('tc').checked==false){
		$(errorDiv).innerHTML='You did not agree with our privacy terms.';
		return false;
	}
	var frndlist = arFrnd.join(','); 
	var curtime=new Date().getTime();
	var url= host+'/tickertalk/tickerajaxcontroller.php';
	var pars ='';
 	pars = 'type=doregister';
	pars = pars+'&firstname='+fFname;
	pars = pars+'&lastname='+fLname;	
	pars = pars+'&uid='+tickertrim($('email').value);
	pars = pars+'&pwd='+tickertrim($('pwd').value);
	pars = pars+'&is_fbuser=1&timestamp='+curtime;
	
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishRegister(req){
		
		post = eval('('+req.responseText+')');
		if(post.status==false){
			$('errmsg').innerHTML=tickertrim(post.msg);
			return false;
		}
		else{
			 
			//set fb user details  & prfrences in db
			var url = host+'/lib/facebook/fbhandler.php';
			var publish =  '0';
			var followfrnd = '0';
			var emailalert = '0';
			if($('publish_post').checked){
				publish = '1';
			}
			if($('followfrnd').checked){
				followfrnd = '1';
			}
			if($('emailalert').checked){
				emailalert = '1';
			}
			
			var parms  = 'fbId='+fUserId+'&action=fbinsert&publish_post='+publish+'&follow_frnds='+followfrnd+'&email_alerts='+emailalert+'&frndlist='+frndlist;
			var myAjax4 = new Ajax.Request(url, {method: 'post',parameters:parms,onComplete:function(req){
			if(req.responseText == 'true'){ //user alreday exists 	
				iboxclose();			
			}
			
			}});
			$('fconn').hide();
			showfilter();		// show combobox			
			$('tickerlogin').style.display="none";
			var url=host+'/tickertalk/post.php';
			var show;
			var topicchatid=$('hidTopicId').value
			var pars='showbox=' + show + '&topicchatid=' + topicchatid;
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});

		}
		
	}//end of finishLogin
	});	

}
function doFBLogin(){ 
	var errorDiv="errmsglogin";
	var curtime=new Date().getTime();
	var url= host+'/tickertalk/tickerajaxcontroller.php';
	var pars ='';
	if(tickerisValidEmail('errmsglogin','fbuid')==false){
		$('fbuid').select();
		return false;
	}
	if($('fbpwd').value==''){
		$(errorDiv).innerHTML='Enter password.';
		$('fbpwd').select();
		return false;
	}	
	pars = 'type=dologin';
	pars = pars+'&uid='+tickertrim($('fbuid').value);
	pars = pars+'&pwd='+tickertrim($('fbpwd').value);
	pars = pars+'&timestamp='+curtime;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function finishLogin(req){
		post = eval('('+req.responseText+')');		 
		if(post.status==false){
			$(errorDiv).innerHTML=tickertrim(post.msg);
			loginStatus=false;
		}
		else
		{  
			var sql = "select uid2 from friend where uid1="+fUserId;
			FB.Facebook.apiClient.fql_query(sql, function(result, ex) {
			for(i=0;i<result.length;i++) {
				  arFrnd[i] = result[i]['uid2']; 
			}   
			}); 
			var frndlist = arFrnd.join(',');  
			var url = host+'/lib/facebook/fbhandler.php';
			var parms  ='fbId='+fUserId+'&action=exists&frndlist='+frndlist;			 
			var myAjax4 = new Ajax.Request(url, {method: 'post',parameters:parms,onComplete:function(req){
				if(req.responseText == 'true'){ //user alreday exists 					
					iboxclose(); 
					$('fconn').hide();
					showfilter();		// show combobox								
					$('tickerlogin').style.display="none";
					loginStatus=true;
					var url=host+'/tickertalk/post.php';
					var show;
					var topicchatid=$('hidTopicId').value
					var pars='showbox=' + show + '&topicchatid=' + topicchatid;
					var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});
				}
				
			}});
			
		}
		//}
		
	}//end of finishLogin
	});
}


function isValidEmailfb(errorDiv,emailFieldId)
{
	var bools=true;
	var emails=$(emailFieldId).value.split(';');

	for (var i=0;i<emails.length;i++)
	{
		if(emails[i]=='')
		{
			$(errorDiv).innerHTML='Email field can not be left blank.'; 
			$(emailFieldId).select();
			bools=false;
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
		{
			var erormsg = 'Not a valid E-mail id "'+emails[i]+'"';
			$(errorDiv).innerHTML=erormsg;
			$(emailFieldId).select();			
			bools=false;
			return false;
		}
	}
	$(errorDiv).innerHTML='';
	return bools;
}

 function auth_fb() {
	 if($('fconn')){
		//$('fconn').hide();
	 }
}


function load_data(name, image, url, logout) {
/*	$('#userbox').html( "<a href='"+url+"'>"
	    + "<img alt='"+name+"' src='"+image+"' /></a>"
	    + "<div id='ubname'><a href='"+url+"'>" + name + " logged in with Facebook Connect</a></div> "
	    + "<a href='./index.html' id='logout' onclick='" + logout + "'>logout</a>" ).show();
	$('div#submit').html( "<input type='submit' id='sub' onClick='fb_reg(); return false;' value='Post Comment' />");
	$('#name').val(name);
	$('#url').val(url);
	$('#image').val(image);
	$('#userinfo').hide();*/
} 
function displayFBMVLogin(display)
{
	if(display == '1')
	{
		$('fb_mv_register').hide();		
		$('ibox_wrapper').setStyle("height:193px");		
		$('fb_mv_login').show();
		$('fbuid').focus();	
		 		
	}
	else
	{
		$('fb_mv_login').hide();
		 
	}
	return true;
}

function chkUserFb(){	
		  /*  var frndlist = arFrnd.join(',');  
			var url = host+'/lib/facebook/fbhandler.php';
			var parms  ='fbId='+fUserId+'&action=exists';			
			var myAjax4 = new Ajax.Request(url, {method: 'post',parameters:parms,onComplete:function(req){							
				iboxclose();
				
			}});*/
		iboxclose();
} 