var xmlHttp;
function checkprevilages(divId,url,chk){
	if(chk='true')
	{
		alert("Sorry! You're not authorized to do this!");
		return false;
	}
}
// remove white spaces from left and right
function trim(str)
{
	
	while(''+str.charAt(0)==' ')
		str=str.substring(1,str.length);
	
	while(''+str.charAt(str.length-1)==' ')
		str=str.substring(0,str.length-1);
	
	return str;
}

// This function does the AJAX request
function preHttpRequest(divId,url,strfriend) {
	/*strfriend used for friends page*/
	if(divId=='divPost'){
		document.getElementById("hid").style.display="";
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	height=20;
	xmlHttp.open("GET",url, true);
    
	xmlHttp.onreadystatechange=function() { stateChange(divId); };
	xmlHttp.send(null); 	
}

/*function used for inbox functionality*/
function preHttpRequestinbox(divId,url,strfriend)
{ 
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	height=20;
	xmlHttp.open("GET",url,true);
	
	xmlHttp.onreadystatechange=function() { stateChange(divId); };
	xmlHttp.send(null);
} 

function preHttpRequestPostinbox(divId,url,post)
{   
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	height=20;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	
	xmlHttp.onreadystatechange=function() { stateChange(divId); };
	xmlHttp.send(post);
} 
/*function used for inbox functionality*/


// This function does the AJAX post request
function preHttpRequestPost(divId,url,post) {

	if(divId=='divPost'){
		document.getElementById("hid").style.display="";
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	height=20;
	
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: post,
									onComplete:function(req)
									{										
										document.getElementById(divId).innerHTML= req.responseText;
									}
									});

	
/*	http.open("POST",url, true);
	
	http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
	http.onreadystatechange=function() {

		if (http.readyState==4 && http.status == 200) {
			
			http.onreadystatechange =function() { stateChange(divId); };
			
		}
		
	}
	
	http.send(post);*/
	
}// end of preHttpRequestPost function
// get response text

/*function getHttpRes(divId) {
	// These following lines get the response and update the page
	res = http.responseText; 
	document.getElementById(divId).innerHTML = res;
} */

function getXHTTP() {
	var xhttp;
	// The following "try" blocks get the XMLHTTP object for various browsers�
	try {   
		xhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } 
	catch (e) {
		try {
			xhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch (e2) {
			// This block handles Mozilla/Firefox browsers...
			try {
				xhttp = new XMLHttpRequest();
			} 
			catch (e3) {
				xhttp = false;
			}
		}
    }
	return xhttp; // Return the XMLHTTP object
}
/*

function matchBadWords(badwords,strbody,matchedbadwords){

	// getting array of bad words
	var strbadwords=badwords.split(',');
	// getting array of posted words
	var strpostwords=strbody.split(' ');
	var checkword=false;
	var badword="";
	var postword="";
	
	// get bad word
	for(i=0; i<strbadwords.length;i++){
		//match bad word in posted words
		for(j=0; j<strpostwords.length;j++){			
			badword=strbadwords[i].toLowerCase();
			postword=strpostwords[j].toLowerCase();			
			badword=trim(badword);
			postword=trim(postword);

			// if bad word is found in posted words
			if(badword==postword){
				checkword=false;
			
				if(matchedbadwords.length>0){
					
					var reg = new RegExp(postword);
					// if badword already added in error message list 
					if (reg.test(matchedbadwords)){
						checkword=true;
					}
				}
				
				if(checkword==false){
					matchedbadwords+=postword+'\n';
				}
			}
			
		}//end of inner for loop
		
	}//end of outer for loop
	
	return matchedbadwords;
}
*/
function matchBadWords(badwords,strbody,matchedbadwords,objWordLength,maxcharcount){
	
	// getting array of bad words
	var strbadwords=badwords.split(',');
	// getting array of posted words
	var strpostwords=strbody.split('\n');
	
	var checkword=false;
	var badword="";
	var postword="";
	
	//convert all text to lowercase
	strbody=strbody.toLowerCase();
	badwords=badwords.toLowerCase();
	var intIndexOfMatch = strbody.indexOf( '\n' );
 	while (intIndexOfMatch != -1){
		strbody = strbody.replace( '\n', ' ' )
		intIndexOfMatch = strbody.indexOf( "\n" );
	}
	re = /\$|,|@|#|~|`|\%|\*|\^|\&|\(|\)|\+|\=|\[|\-|\_|\]|\[|\}|\{|\;|\:|\'|\"|\<|\>|\?|\||\\|\!|\$|\./g;
	strbody = strbody.replace(re, " ");
	strbody=' '+strbody+' ';
	
	// get bad word
	for(i=0; i<strbadwords.length;i++){
		strbadwords[i]=' '+strbadwords[i]+' ';
		
		//if bad word matched with text
		if(strbody.search(strbadwords[i])!=-1){
			
			//if badword is not already in the list.
			if(matchedbadwords.search(strbadwords[i])==-1){
				matchedbadwords+=strbadwords[i]+'\n';
			}
			
		}
	}//end of badword loop
	
	//match bad word in posted words
	/* for(j=0; j<strpostwords.length;j++){			
		
		postword=strpostwords[j];
		var postarray=postword.split(' ');
		
		
		for (k=0;k<postarray.length;k++){
			
			if(postarray[k].length>maxcharcount){
				objWordLength.myParameter=true;
			}	
		}
		
		
	}//end of text loop
	*/
	return matchedbadwords;
}
function stripHTML(oldString) {

   var newString = "";
   var inTag = false;
   for(var i = 0; i < oldString.length; i++) {
   
        if(oldString.charAt(i) == '<') inTag = true;
        if(oldString.charAt(i) == '>') {
              if(oldString.charAt(i+1)=="<")
              {
              		//dont do anything
	}
	else
	{
		inTag = false;
		i++;
	}
        }
   
        if(!inTag) newString += oldString.charAt(i);

   }
   return newString;
}
function postHttpRequest(frm,$type,badwords,conv_ids,strinbox) {
	if(frm.id=='divPost'){
		frm='divPost';
	}	

	str='txtTitle'+frm;
	var strtitle=document.getElementById(str).value;
	
	str='txtBody'+frm;
	var strbody=document.getElementById(str).value;
	
	var matchedbadwords="";
	var objWordLength=new Object();
	objWordLength.myParameter=false;
	// match bad words in subject
	matchedbadwords+=matchBadWords(badwords,strtitle,matchedbadwords,objWordLength,43);	
	//match bad words in comment
	matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords,objWordLength,43);
	
	if(objWordLength.myParameter==true){
		alert('Some words are too long.');
		return false;
	}
	
	// match bad words in subject
	//matchedbadwords+=matchBadWords(badwords,strtitle,matchedbadwords);	
	//match bad words in comment
	//matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords);
		
	//if bad words are found in posted words.
	if(matchedbadwords.length>0){
		alert('Following words are not allowed.\n'+matchedbadwords);
		return false;
	}	
	if($type==0){
		
	
		if(strbody==''){
			alert('Enter Comment');
			return false;
		}
		
		str='subscription_id'+frm;
		var strsubscription_id=document.getElementById(str).value;
		
		str='thread_id'+frm;
		var strthread_id=document.getElementById(str).value;
		
		str='comment_id'+frm;
		var strcomment_id=document.getElementById(str).value;	
/*	
	
		var url='Save.php';
		url=url+'?title='+encodeURIComponent(strtitle);
		url=url+'&post_text='+encodeURIComponent(strbody);
		url=url+'&poster_id='+strsubscription_id;	
		url=url+'&thread_id='+strthread_id;
		url=url+'&comment_id='+strcomment_id;
		preHttpRequest(frm,url);		
*/
		
		var url='title='+encodeURIComponent(stripHTML(strtitle));
		url=url+'&post_text='+encodeURIComponent(stripHTML(strbody));
		url=url+'&poster_id='+strsubscription_id;	
		url=url+'&thread_id='+strthread_id;
		url=url+'&comment_id='+strcomment_id;
		url=url+'&Ptype=post';
		var save_url=host + '/articles/Save.php'
		preHttpRequestPost(frm,save_url,url);
	}
	else
	{		
		if(strbody==''){
			alert('Enter Message');
			return false;
		}
	
		str='from_subscription_id'+frm;
		var strsubscription_id=document.getElementById(str).value;
		
		str='to_subscription_id'+frm;
		var strthread_id=document.getElementById(str).value;
		
		str='message_id'+frm;
		var strmessage_id=document.getElementById(str).value;	
		
		var urlmessage='title='+encodeURIComponent(stripHTML(strtitle));
		urlmessage=urlmessage+'&from_subscription_id='+strsubscription_id;
		urlmessage=urlmessage+'&to_subscription_id='+strthread_id;
		urlmessage=urlmessage+'&private_msg_id='+strmessage_id;
		urlmessage=urlmessage+'&text='+encodeURIComponent(stripHTML(strbody));
		urlmessage=urlmessage+'&Ptype=sendmsg';
               urlmessage=urlmessage+'&strinbox='+strinbox;
		urlmessage=urlmessage+'&conv_ids='+conv_ids;
		if(strinbox){
			var save_url= host + '/articles/Save.php'
			preHttpRequestPostinbox(frm,save_url,urlmessage);
		    //var urlmessage='../articles/Save.php';
		} else {
			var save_url= host + '/articles/Save.php';
			preHttpRequestPost(frm,save_url,urlmessage);
			//var urlmessage='Save.php';	
		}
	}
}

function doCancel(frm){
	document.getElementById(frm).innerHTML="";
}

function onClickEmailAlert(url, checkLogin)
{
	var passedUrl=url;
	var emailAdd=document.getElementById("emailAddress").value;
	if(trim(emailAdd)=='Enter email Address')
	{
		emailAdd='';
	}
	if(checkLogin==0)
	{
		passedUrl=passedUrl+'&emailaddress='+emailAdd;
		document.getElementById("navlink").setAttribute("href",passedUrl);
	}
	init_ibox();
}

function onFocusRemoveText(element)
{
	if(trim(element.value)==element.defaultValue)
		{
			element.value='';
		}
}

function onBlurrGetText(element) {
    if (element.value == "") {
        element.value = element.defaultValue;
    }
}
function showCommentbox(appcommentcount,articleid,subscription_id,pageName,profile_exchange,from,imagevalue,sid,eid,urlPost){
	//var url = 'Post.php';
	var show=1;
	if(imagevalue=="1"){
		var showcomment=0;
	}else {
		var showcomment=1;	
	}
	var pars = 'show=' + show + '&showcomment=' + showcomment + '&appcommentcount=' + appcommentcount + '&articleid=' + articleid + '&subscription_id=' + subscription_id + '&pageName=' + pageName + '&profile_exchange=' + profile_exchange + '&from=' + from +'&imagevalue=' + imagevalue +'&sid=' + sid +'&eid=' + eid + '&urlPost='+ urlPost;
	var postAjax = new Ajax.Request(urlPost, {method: 'post', parameters: pars, onComplete:getshowCommentData});
}

function getshowCommentData(req){
	if(req.responseText){
		$('showcomment').innerHTML="";
		$('showcomment').innerHTML=req.responseText;		
	}
}

function print(articleid) {
	window.open(host + "/articles/print.php?a="+articleid,"print","width=850,height=550,top=50,left=200,resizable=yes,toolbar=no,scrollbars=yes");
}

function dailyfeedprint(day) {
window.open(host + "/dailyfeed/print.php?getday="+day,"print","width=850,height=550,top=50,left=200,resizable=yes,toolbar=no,scrollbars=yes");
}

function clearNewsletterText(id,text)
{
	str = jQuery('#'+id).val();
	if(str==text)
	{
		jQuery('#'+id).val('');
	}
}

function sendNewsletter()
{
	var daily_recap = '1';
	var trading_radar = '1';
	var email = jQuery('#user_email').val();
	var isvalidEmail=isValidEmail('user_email');
	if(isvalidEmail==false)
	{
		return false;
	}
	var type= "dailydigest_article";
	var userid = jQuery('#subscriber_id').val();
	
	jQuery('#submit_btn').unbind('click');
	var url = host+'/subscription/register/loginAjax.php';	
	var pars="type="+type+"&checkemail="+email+"&sessuserid="+userid+"&daily_recap="+daily_recap+"&trading_radar="+trading_radar;
	jQuery.ajax({
	   type: "POST",
	   url: url,
	   data:pars,
	   success: function getUnsubscribeStatus(req){
		 	var post = eval('('+req+')');

		 	if(post.status==true )
			{
		 		if(daily_recap=="1")
			 	{
			 		trackEmailNewsletterClick("Daily Recap");
			 	}
			 	if(trading_radar=="1")
			 	{
			 		trackEmailNewsletterClick("Trading Radar");
			 	}
			 	alert('Thank you for subscribing');
		 		if(post.msg=="newuser")
		 		{
		 			location.reload();
		 		}
			}
			else
			{
				alert(post.msg);
				return false;
			}
	   }
	 });

}

function getSetArticleDivHeight(articleId){
	var divHeight =  jQuery('div#articleBodyContent').height();
	var url = host+'/articles/setHeight.php';	
	var pars="height="+divHeight+"&articleId="+articleId;
	jQuery.ajax({
	   type: "POST",
	   url: url,
	   data:pars,
	   success: function(req){
		 	var post = eval('('+req+')');

		 	if(post.status==true )
			{
		 		return true;
			}
		}
	});
}