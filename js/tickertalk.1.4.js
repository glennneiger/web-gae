host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;

function get_login_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		doLogin();
	}
}

function get_register_keys(event){
	var keyVal=event.keyCode;//  for IE 
	if(keyVal==undefined){
		keyVal=event.which; // for Firefox/Opera/Netscape
	}
	
	if(keyVal==13){ // Enter key pressed
		doRegister();
	}
}


function tickertrim(str)
	{
		
		while(''+str.charAt(0)==' ')
			str=str.substring(1,str.length);
		
		while(''+str.charAt(str.length-1)==' ')
			str=str.substring(0,str.length-1);
		
		return str;
	}
	
	function validateTickerChars(value) {

	var string = tickertrim(value);
    var bVal = true;
	if (!string) 
	{	
		bVal = false;
	}
	
    var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for (var i = 0; i < string.length; i++) {
    	if (Chars.indexOf(string.charAt(i)) == -1)
        {
		     bVal = false; 
		}
    }
	
	return bVal;
	
	} 	
	function tickerisValidEmail(errorDiv,emailFieldId)
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
				/*$(emailFieldId).select();
				$(emailFieldId).activate();*/
				bools=false;
				return false;
			}
		}
		$(errorDiv).innerHTML='&nbsp;';
		return bools;
	}	
	function searchTicker(){
		        $("showerrormsg").style.display='none';
			    var searchsymbol;
			    searchsymbol=$("symbol").value;
				var url= host+'/tickertalk/tickersearch.php';
				var pars = 'ticker=' + searchsymbol;
				var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:getticker});
	}
	
	function getticker(req){
		var resposnseval;
		resposnseval =req.responseText;	
		if(isNaN(resposnseval)){
			$("showerrormsg").style.display='block';
			if(resposnseval=="Invalid Symbol"){
				$("showtickermsg").style.display='block';	
				$("showerrormsg").style.display='none';
				$("showtickermsg").innerHTML=resposnseval + '&nbsp;';	
			}else{
				$("showtickermsg").style.display='none';	
				$("showerrormsg").innerHTML=resposnseval;	
			}
			$("showerr").innerHTML='&nbsp';
			if($("tickererrmsg"))
			{
				$("tickererrmsg").innerHTML='&nbsp;';
			}
			
			
		}else{			
			$("showtickermsg").style.display='none';
			if($("tickererrmsg"))
			{
				$("tickererrmsg").innerHTML='&nbsp;';			
			}
			$("showerrormsg").style.display='none';
			$('shareideas').style.display='block';
    		$('postcomment').style.display='block';	
			$('showticker').value=0;
			$('selectval').innerHTML='Select';
			loadDiscussion(resposnseval);		
		}
	}

 function showCommentPostDivArea(show){	
	// check login status of user
			$('tickerlogin').style.display="none";
			var url=host+'/tickertalk/post.php';
			var show;
			var topicchatid=$('hidTopicId').value
			var pars='showbox=' + show + '&topicchatid=' + topicchatid;
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});
		//==============================================
	}
	function showPostComment(req){
		$('msgid').innerHTML='';	
		 var reqval=req.responseText;
		   if(reqval.substr(2,6)=="errmsg"){
			   $('postcomment').style.display='none';	
			   $("showerrormsg").style.display="block";
	 		   $("showerrormsg").innerHTML=reqval.substring(11,reqval.length-2);
			}else{
				$("showtickermsg").style.display='none';				
				$("showerrormsg").style.display="none";
				$("postcomment").style.display="block";
				$("postcomment").innerHTML=reqval;
				//$('postsymbol').value=jQuery('#topic_name').text();
				var obj = actb(document.getElementById('postsymbol'),customarray,'',4,0,204);
				showUploadTool();
				jQuery("textarea[class*=expand]").TextAreaExpander();
				// Code for blind down effect for upload image URL 
				// Code for Jquery effect required for this has been plugged into jquery.textarea-expander.js
				var $box = jQuery('#addurl_box').wrap('<div id="addurl"></div>');
				jQuery('#uploadurl').click(function() { $box.blindToggle('slow'); });
			}
		/*}*/
	}
	
	function showLoginRegisterbox(subid){
		if(subid==''){
			$("jointicker").style.display="none";
			$("tickerlogin").style.display="block";
			showCommonBox('','','','','');	
		}
	}
	
	function ShowLoginRegisteronSymbol(obj,subid){
		showLoginRegisterbox(subid);
		focusedsymbol(obj);
		
	}
	
	function cancelComment(){
		//$("default_button").show();
		$("postcomment").style.display='none';
		$("showtickermsg").style.display='none';
	}
	
	
	function checktextbody(str) {
		return str.replace(/^\s*/, "").replace(/\s*$/, "");
	}

	function showCommentPostDiv(badwords,longwmsg,notallowed,emptytext,topicid,stockid,postid){
		var urlpost=host+'/tickertalk/post.php';
		var show;
		var post;
		var textbody;
		var checkvalue,i;
		var topicchatid=$('hidTopicId').value;
			if(show!=1){
			textbody=checktextbody($("reply").value);
				if(textbody.length>0){
					emptytext="";
				}
				if((textbody.length>=1)&&(textbody.length<=10)){
					$('showerr').innerHTML="Post must be more than 10 characters";
					return false;
				}
				var postsymbol = checktextbody($('postsymbol').value);
				if((postsymbol.length==0)||(postsymbol=='Symbol')){
					$('showerr').innerHTML="Please enter a symbol to post comment";
					$('postsymbol').focus();
					return false;
				}
					if(textbody.length>0){  // send post comment 
							var matchedbadwords="";
							var objWordLength=new Object();
							objWordLength.myParameter=false;
							//match bad words in posted comment
							matchedbadwords=matchBadWords(badwords,textbody,matchedbadwords,objWordLength,43);
							if(matchedbadwords.length>0){ // if bad words are found in posted comment.
								alert(notallowed + '\n'+matchedbadwords);
								return false;
							}else{
								$("showtickermsg").style.display='none'; 
								post=1;	
								checkvalue="";
								if(document.upload_image.chkImage)
								{
									var uploaded_image_length = document.upload_image.chkImage.length;
									if(uploaded_image_length)
									{
										for(i=0;i<uploaded_image_length;i++)
										{				
											var value = document.upload_image.chkImage[i].value;									
											if(value)
											{  
												if(document.upload_image.chkImage[i].checked==true)
												{
													if(i==0)
													{
														checkvalue=value;
													}else{
														checkvalue = checkvalue + '~' + value;	
													}
												}
												
											}
										}
										
									}
									else
									{
										if(document.upload_image.chkImage.checked==true){
											checkvalue=document.upload_image.chkImage.value;
										}
									}
								}
								textbody=encodeURIComponent(textbody);
								var pars='textbody=' + textbody + '&post=' + post + '&topicid=' + topicid + '&stockid=' + stockid + '&topicchatid=' + topicchatid + '&checkvalue=' + checkvalue + '&postsymbol=' + postsymbol + '&postid='+ postid;  // used to indert values in table
								var postAjax = new Ajax.Request(urlpost, {method: 'post', parameters: pars,onComplete:showCommentDiv});
							}

					}else{
						   show=1;
							var pars='emptytext=' + emptytext ; // show the comment div
							var postAjax = new Ajax.Request(urlpost, {method: 'post', parameters: pars,onComplete:showCommentDiv});
					}

				
			}
			
	}
	
	
	function showCommentDiv(req){
		 var reqval_1=eval('('+req.responseText+')');;		  
		 if(reqval_1.errmsg == ""){
			$("showerr").innerHTML="";	
			$("showtickermsg").style.display='none';
		} 
		if(reqval_1.errmsg != ''){
			if(reqval_1.errmsg=='not login'){
				
				textbody=checktextbody($("reply").value);
				$("tickerlogin").style.display="block";
				showCommonBox('postcomment',textbody,reqval_1.stockid,reqval_1.topicid,'');  // call if user is not login and post a comment 				
			}else{
				$("showerr").innerHTML=reqval_1.errmsg;	
				$("showerr").style.display='block';
				$("showtickermsg").style.display='none';
			}
		}else{
			$('postcomment').style.display='none';
			$('jointicker').style.display='none';
			if(reqval_1.postid){
				$('msgid').innerHTML='Your reply is submitted successfully';
			}else{
				$('msgid').innerHTML='Your post is submitted successfully';
			}
			spyAjaxFetchCount = 0; // Set this counter to 0 as we need to make ajax request after interval set in spy (2 sec) if user has posted a post
			$("postcomment").style.display="none";			
			var curtime=new Date().getTime();
			var url= host+'/lib/facebook/fbhandler.php';
			var pars ='action=fbchkpublish';
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:function(req){			
				if(req.responseText=='true'){	
					if(reqval_1.image)
					{
						var imgStr = new Array();
						var len; 									
						for (len=0;len<=reqval_1.image.length-1;len++){	
							imgStr[len] =  {'type':'image','src':host+reqval_1.image[len],'href':host};
						}
						imgStr.toJSON();			 
						var attachment = {'name':'Minyanville:Ticker Talk','href':host,'caption':'Minyanville Ticker Talk','description':reqval_1.commentbody,'media':imgStr};		
					}
					else
					{						
						var attachment = {'name':'Minyanville:Ticker Talk','href':host,'caption':'Minyanville Ticker Talk','description':reqval_1.commentbody};
					}
					FB.ensureInit(function() { 									
						FB.init("65723f6a3fb308488a02bc66fdcef0e3", host+"/tickertalk/xd_receiver.htm", {"ifUserConnected" : auth_fb});														
						FB.Connect.streamPublish('',attachment);
						
					}); 						
				}
			}																									   
			});
			$("postcomment").innerHTML=reqval_1.commentbody;	
			$("showtickermsg").style.display='none';
		} 
	}
	function showCommentDiv_org(req){
		 var reqval=req.responseText;
		 var response= reqval.split("msg");
		if(response[1]==undefined){
			$("showerr").innerHTML="";	
			$("showtickermsg").style.display='none';
		} 
		if(response[1]!==undefined){
			$("showerr").innerHTML=response[1];	
			$("showerr").style.display='block';
			$("showtickermsg").style.display='none';
		}else{
			$("postcomment").style.display="none";
			/*$("final_msg").innerHTML;
			$("default_button").show();*/
			var curtime=new Date().getTime();
			var url= host+'/lib/facebook/fbhandler.php';
			var pars ='action=fbchkpublish';
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:function(req){			
												if(req.responseText=='true'){
													var data = {"images":[{"src":"http://pad.thedigitalmovement.com/_blaise/2007-06-15-dgen-breakfast.jpg", "href":"http://www.facebook.com"}, {"src": "http://pad.thedigitalmovement.com/_blaise/2007-06-13-roger-waters.jpg", "href":"http://www.facebook.com"}],"stock":"GOOG","comment":"Buy Google Now"};
													var attachment = {'name':'Minyanville Ticker Talk','href':host,'caption':'Minyanville Ticker Talk','description':reqval};
													 FB.ensureInit(function() { 									
														FB.init("65723f6a3fb308488a02bc66fdcef0e3", host+"/tickertalk/xd_receiver.htm", {"ifUserConnected" : auth_fb});														
														FB.Connect.streamPublish('',attachment);

													}); 
												 	//FB.Connect.showFeedDialog(154730676347, data);		
												}
											}																									   
											});
			$("postcomment").innerHTML=response[0];	
			$("showtickermsg").style.display='none';
		} 
	}
	
	
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
	for(j=0; j<strpostwords.length;j++){			
		
		postword=strpostwords[j];
		var postarray=postword.split(' ');
		
		
		for (k=0;k<postarray.length;k++){
			
			if(postarray[k].length>maxcharcount){
				objWordLength.myParameter=true;
			}	
		}
		
		
	}//end of text loop
	
	return matchedbadwords;
}

  function ismaxlength(obj){
	var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
	if (obj.getAttribute && obj.value.length>mlength)
	obj.value=obj.value.substring(0,mlength)
  }



 function countPostCharacters(txtObject,maxLength)
{   
    var maxLength;
     maxLength=txtObject.getAttribute("maxlength")
	if (txtObject.value.length > maxLength) // if the current length is more than allowed
	{
	txtObject.value = txtObject.value.substring(0, maxLength); // don't allow further input
	}
	$("showcharcount").innerHTML="("+ (maxLength - (txtObject.value.length)) +" characters left)"; 
}

 function countPostCharactersReportAbuse(txtObject,maxLength)
{   
    var maxLength;
     maxLength=txtObject.getAttribute("maxlength")
	if (txtObject.value.length > maxLength) // if the current length is more than allowed
	{
	txtObject.value = txtObject.value.substring(0, maxLength); // don't allow further input
	}
	$("showcharcount_bottom").innerHTML="("+ (maxLength - (txtObject.value.length)) +" characters left)"; 
	
}

  
  function searchEnterKeyTicker(evt)
	{
		evt = (evt) ? evt : ((event) ? event : null);
		var evver = (evt.target) ? evt.target : ((evt.srcElement)?evt.srcElement : null );
		var keynumber = evt.keyCode;
		if(keynumber==13)
		{
			searchTicker();
			
		}
	}
  
/********************Start of Code for the SPY Effect****************/
var newChat = true;
var newContent = true;
var max_id;
var spyAjaxFetchCount = 0;

jQuery(function () {
jQuery('ul.spy').simpleSpy();

});

(function ($) 
{
	$.fn.appendVal = function(txt) { return this.each(function(){ this.value += txt; }); };
	$.fn.simpleSpy = function (limit, interval) {
	limit = limit || 10;
	interval = interval || 2000;
	firstCall = false
	function getSpyItem($source)
	{
		var $items = $source.find('> li');
		var $span = $source.find('span');
		var $suspendPost = $source.find('suspend');
		var max_id = "";
		if(newChat == true)
		{
			firstCall = false
		}
		var current_topic_id = document.getElementById('hidTopicId').value;
		if (!firstCall || spyAjaxFetchCount >=0)
		{
			// if chat is newly loaded always get max comment id from hidden variable			
			if(newChat == true)
			{
				max_id = document.getElementById('hidMaxId').value;
			}
			else // else get from request
			{
				max_id = $("#last_fetch_id").val();
			}
			if(parseInt(max_id) >= 0 && current_topic_id)
			{
				firstCall = true;
				if(newChat == true)
				{
					newContent = true; // variable need not to display old ticker's content from the pool when new ticker is loaded
				}
				var showticker = document.getElementById('showticker').value;
				var suspendposttimestamp = document.getElementById('hidPostSuspendedTimeUserView').value; // this variable contains the latest time stamp when a post is suspended.
				//finishTickerProgress();
				var timestamp = new Date().getTime();					
				if(spyAjaxFetchCount == 0 || newChat == true)
				{
					spyAjaxFetchCount = 14; // next ajax request will be after 30 sec (interval*(spyAjaxFetchCount+1))
					// Go for Ajax Request if items(post) in the pool are not more than one (This is the main logic of SPY) else old pool will be replaced with the new one
					if($items.length <= 1 || $span.length == 1 || newChat == true)
					{					
						$source.load(host+'/tickertalk/live_chat.php?hidMaxId='+max_id+'&hidTopicId='+current_topic_id+"&time="+timestamp+"&showticker="+showticker+"&suspendposttimestamp="+suspendposttimestamp);
					}					
					newChat = false;
				}
				else
				{
					if($suspendPost.length)// this case will be there when a post is suspended and uses's view need to be refresh rather than loading posts from the pool
					{
						loadDiscussion(current_topic_id);
					}
					spyAjaxFetchCount = spyAjaxFetchCount-1;
				}
			}
		}	
		else
		{
			return false;
		}
		// grab the first item, and remove it from the $source
		return $items.filter(':first').remove();
}
return this.each(function () {
// 1. setup
// capture a cache of all the list items
// chomp the list down to limit li elements
var $list = $(this),
running = true,
height = $list.find('> li:first').height();
// TODO create the $source element....
var $source = $('<ul />').hide().appendTo('body');
$list.wrap('<div class="spyWrapper" />').parent();
$list.find('> li').filter(':gt(' + (limit - 1) + ')').appendTo($source);
$list.bind('stop', function () {
running = false;
}).bind('start', function () {
running = true;
});
// 2. effect
function spy() {
if (running) {
var $item = getSpyItem($source);
content_height = 200;
if($item[0])
{
var content = $item[0].innerHTML;
var textcontent = content.substring(content.toUpperCase().indexOf("<H2>")+4,content.toUpperCase().indexOf("</H2>"));
textcontent = textcontent.replace(/<a ([^<]+)>/gi, "");
textcontent = textcontent.replace(/<\/a>/gi, "");
textcontent = textcontent.replace(/&lt; br&gt;/gi, "<BR>");
var arContent =(textcontent.toUpperCase().split(/<BR>/g));
var content_row=0;
var character_per_row = 23;
var ticker_size = document.getElementById('hidTickerSize').value;
if(ticker_size == 'medium')
{
	character_per_row = 30;
}
for(i=0;i<arContent.length;i++)
{
if(arContent[i].length > 0)
{
content_row += Math.ceil(arContent[i].length/character_per_row);
}
else
{
content_row +=1;
}
}
var chart_height = content.substring(content.toUpperCase().indexOf("<P>")+3,content.toUpperCase().indexOf("</P>"));
if(content_row == 0)
{
content_row = 1;
}
var content_height =16+(parseInt(content_row)*15)+parseInt(chart_height); // 15 for author name and date
if(content.search(/filter_time/i)!=-1) // Time of the post is displayed in different div for filters only
{
content_height+=12+6; // 12 for time div and extra 6 for ticker name
}
if(content.search(/reportabusecomment/i)!=-1){
content_height+=18;
}
}
if(newContent == true) // Don not load old ticker content from the pool when new discussion is loaded
{
newContent = false;
}
else if ($item != false) {
// insert a new item with opacity and height of zero
if($item[0]) // Remove message on load of first comment
{
$("#ticker_error").remove();
}
var $insert = $item.css({
height : 0,
opacity : 0,
display : 'none'
}).prependTo($list);
// fade the LAST item out
//$list.find('> li:last').animate({ opacity : 0}, 1000, function () {
// increase the height of the NEW first item
$insert.animate({ height : content_height}, 1000).animate({ opacity : 1 }, 1000);
//});
//Start Code to Implement fancy zoom on all the ticker charts if any
if(document.getElementById('hidIsOwn').value == '1')
{
var allChartList = jQuery('#tickerPostBody a.fancyzoom_special');
var allChartArray = $A(allChartList);
for(i = 0;i<allChartArray.length;i++)
{
// If Chart's Chat Id is greater than the max parsed chart chat id in previous rotation
var chat_chart_id = allChartArray[i].id.split('_')[3];
if(chat_chart_id > document.getElementById('hidMaxChartChatId').value)
{
document.getElementById('hidMaxChartChatId').value = chat_chart_id;
$('#'+allChartArray[i].id).fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 300});
}
}
}
//End Code to Implement fancy zoom on all the ticker charts if any
}
}
setTimeout(spy, interval);
}
spy();
});
};
})(jQuery);
/********************End of Code for the SPY Effect*****************/

function loadDiscussion(topic_id)
{	
	$('checkuserview').value=topic_id;
	if($('symbol').value!=='Symbol'){
		$('symbol').value='';
	}
	$("showtickermsg").style.display='none';
	$("showerrormsg").style.display='none';
	if($('drop_down'))
	{
	$('drop_down').style.display='none';
	}
	var url= host+'/tickertalk/ticker_chat.php';
	var pars = 'topic_id=' + topic_id;
	var postAjax = new Ajax.Request(url, {method: 'post',
										  parameters: pars,
										 onLoading:showTickerProgress(),
										 onComplete:function(req)
										 {												
											newChat = true;	
											var commentsArray = eval('(' + req.responseText + ')');	
											var discussionListing = "";
											// the key body at 0th place name of the ticker											
											var topic_name = commentsArray[0]['topic_name'];
											var topic_status = commentsArray[0]['topic_status'];
											var is_viewmore = commentsArray[0]['view_more'];
											var least_parsed_id = commentsArray[0]['least_parsed_id'];
											var moderator = commentsArray[0]['moderator'];
											var loginid = commentsArray[0]['loginid'];
											var add_tag = commentsArray[0]['add_tag'];

											$("topic_name").innerHTML = topic_name;	
											$("add_tag").innerHTML = add_tag;	
											$('hidTopicId').value=topic_id;
											$('hidPostSuspendedTimeUserView').value = commentsArray[0]['latest_post_suspended_time'];
											// Listing will only be displayed if topic is Live or Archived											
											if(topic_status == 'L' || topic_status == 'A')
											{												
												if(commentsArray.length > 1)
												{
													for(i=1;i<commentsArray.length;i++)
													{
														
													<!--	discussionListing +="<li onmouseover='displayReply("+commentsArray[i]['id']+");' onmouseout='hideReply("+commentsArray[i]['id']+");'>";-->
														discussionListing +="<li>";
														  if(commentsArray[i]['stocksymbol']){
														  discussionListing +="<h3><a onclick=loadDiscussion("+commentsArray[i]['topic_id']+");>" + commentsArray[i]['stocksymbol'] + "</a>&nbsp;&raquo;</h3><h1 style='padding-top:6px;'><a onclick=loadDiscussion('USER_"+commentsArray[i]['subscription_id']+"');>"+commentsArray[i]['username']+"</a></h1><div class='filter_time'>"+commentsArray[i]['created_on']+"</div>";
														  }
														  else
														  {
														discussionListing +="<h1><a onclick=loadDiscussion('USER_"+commentsArray[i]['subscription_id']+"');>"+commentsArray[i]['username']+"</a><span>"+commentsArray[i]['created_on']+"</span></h1>";
														  }
   	
		  discussionListing +="<h2>"+commentsArray[i]['body']+"</h2>";		
						
						discussionListing +="<div class='Reportabusecomment'>";
											
											followmsg='followmsg' + commentsArray[i]['id'];
												discussionListing +="<div class='follow_error' id="+ followmsg + "></div>";
												
												if(loginid!=commentsArray[i]['subscription_id']){
													var followuser = commentsArray[i]['followuser'];
													emptyfollowdiv='emptyfollowdiv' + commentsArray[i]['id'];
													if(followuser){
													var followuser=0;	
													discussionListing +="<div id="+ emptyfollowdiv +" class='follow' onclick='checkFollowUserLogin("+commentsArray[i]['subscription_id']+","+commentsArray[i]['id']+","+followuser+","+loginid+");'>Stop Following</div>";												
													}else{
													var followuser=1;	
													discussionListing +="<div id="+ emptyfollowdiv +" class='follow' onclick='checkFollowUserLogin("+commentsArray[i]['subscription_id']+","+commentsArray[i]['id']+","+followuser+","+loginid+");'>Follow " + commentsArray[i]['fname'] + "</div>";	
													}		
				
												}
											var replyid='replyid' + commentsArray[i]['id'];	
if(commentsArray[i]['stocksymbol']){
	var symbolname=commentsArray[i]['stocksymbol'];
}else{
	var symbolname=topic_name;
}											
								
<!--											discussionListing +="<div id="+replyid+" style='display:none; font-size:9px; padding-left:5px; color:#60229C; text-decoration:underline; cursor:pointer;' onclick='showReplyBox("+commentsArray[i]['id']+",\""+symbolname+"\")';>Reply</div>";	-->										
												    commentsuspend='comment_suspend' + commentsArray[i]['id'];
													emptydiv='emptydiv' + commentsArray[i]['id'];
													discussionListing +="<div class='PostErrorComment' id="+ commentsuspend + "></div>";											
													if(moderator=='1'){
													discussionListing +="<div id=" + emptydiv + " onclick='sendRequestSuspend("+commentsArray[i]['id']+",\""+topic_id+"\");'><span class='Report_Abuse'>Suspend</span></div>";														
													}else{
														if(loginid!=commentsArray[i]['subscription_id']){
														discussionListing +="<div id=" + emptydiv + "  onclick='checkLoginUserAbuseRequest("+ loginid+","+commentsArray[i]['id']+",\""+commentsArray[i]['fullusername']+"\",\""+commentsArray[i]['reportabuse']+"\","+commentsArray[i]['subscription_id']+");'><span class='Report_Abuse'>Report Abuse</span></div>";														
														}
													}
									discussionListing +="</div>";
												
												    discussionListing +="</li>";														
														
														$('comment_listing').innerHTML = discussionListing;
													}
													//Start Code to Implement fancy zoom on all the ticker charts if any
													if($('hidIsOwn').value == '1')
													{
														var allChartList = jQuery('#tickerPostBody a.fancyzoom_special');
														var allChartArray = $A(allChartList);
														for(i = 0;i<allChartArray.length;i++)
														{				
															jQuery('#'+allChartArray[i].id).fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 300});
														}	
													}
													//End Code to Implement fancy zoom on all the ticker charts if any
													$('hidMaxId').value=commentsArray[1]['id'];
													$('hidMaxChartChatId').value=commentsArray[1]['id'];													
													$('hidLeastParsedId').value=least_parsed_id;													
													$('showticker').value=commentsArray[0]['showticker'];
													 $('msgid').innerHTML='';
													if(is_viewmore)
													{														
														if(!$("view_more"))
														{
															viewmore_link = '<div id="view_more" onClick=getMoreComments(); class="load_more">Click to load more posts &raquo;</div>';
															jQuery("#tickerPostBody").append(viewmore_link);
														}
													}
													else
													{
														if($("view_more"))
														{
														$("view_more").remove();
														}
													}
												}
												else
												{
													$('hidMaxId').value=0;
													$('comment_listing').innerHTML = "";													
													// Remove view more while loading new discussion
													if($("view_more"))
													{
														$("view_more").remove();
													}
												}												
											}	
											if(commentsArray[0]['msg'])
											{
												var ticker_msg = commentsArray[0]['msg'];
												ticker_msg = ticker_msg.replace("Click Here", "<a onclick=showCommentPostDivArea('1','0','','');>Click Here</a>");
												$('comment_listing').innerHTML = '<span id="ticker_error" class="empty_ticker">'+ticker_msg+'</span>';
											}
											//$('postcomment').style.display='none';
											$('jointicker').style.display='none';
											if(isNaN(topic_id)||(topic_id=='0'))
											{
												$('shareideas').style.display='block';
												//$('jointicker').style.display='block';
												$('postsymbol').value='Symbol';
												if(topic_id.substring(0,4) == 'USER')
												{
													$('selectval').innerHTML='Select';
												}else{
													$('selectval').innerHTML=topic_name;  	
												}
											}
											else
											{
												$('postsymbol').value=topic_name;
												$('shareideas').style.display='block';
												 $('selectval').innerHTML='Select';
											
											}
											 $('msgid').innerHTML='';
											finishTickerProgress();												
										 }																							 
									});
}
function getMoreComments()
{
	topic_id = $('hidTopicId').value;
	least_id = $('hidLeastParsedId').value;	
	var showticker = $('showticker').value;	
	var url= host+'/tickertalk/view_more.php';
	var pars = 'topic_id='+topic_id+'&least_id='+least_id+'&showticker='+showticker;
	var postAjax = new Ajax.Request(url, {method: 'post',
										  parameters: pars,
										  onLoading:showTickerProgress(),
										 onComplete:function(req)
										 {
											finishTickerProgress();
											var commentsArray = eval('(' + req.responseText + ')');																						
											var discussionListing = "";							
											var is_viewmore = commentsArray[0]['view_more'];
											var least_parsed_id = commentsArray[0]['least_parsed_id'];																															
											var moderator = commentsArray[0]['moderator'];
											var loginid = commentsArray[0]['loginid'];
											// Listing will only be displayed if topic is Lavie or Archived
											for(i=1;i<commentsArray.length;i++)
											{
												discussionListing +="<li>";
												 if(commentsArray[i]['stocksymbol']){
												  discussionListing +="<h3><a  onclick=loadDiscussion("+commentsArray[i]['topic_id']+");>" + commentsArray[i]['stocksymbol'] + "</a>&nbsp;&raquo;</h3><h1 style='padding-top:6px;'><a  onclick=loadDiscussion('USER_"+commentsArray[i]['subscription_id']+"');>"+commentsArray[i]['username']+"</a></h1><div class='filter_time'>"+commentsArray[i]['created_on']+"</div>";
												  }
												  else
												  {
												discussionListing +="<h1><a  onclick=loadDiscussion('USER_"+commentsArray[i]['subscription_id']+"');>"+commentsArray[i]['username']+"</a><span>"+commentsArray[i]['created_on']+"</span></h1>";
												  }
												  discussionListing +="<h2>"+commentsArray[i]['body']+"</h2>";												
												
												discussionListing +="<div class='Reportabusecomment' >";
												followmsg='followmsg' + commentsArray[i]['id'];
												discussionListing +="<div class='follow_error' id="+ followmsg + "></div>";
												//if((loginid!=commentsArray[i]['subscription_id'])&&(!commentsArray[i]['reportabuse'])){
												if(loginid!=commentsArray[i]['subscription_id']){
													var followuser = commentsArray[i]['followuser'];
													emptyfollowdiv='emptyfollowdiv' + commentsArray[i]['id'];
													if(followuser){
													var followuser=0;	
													discussionListing +="<div id="+ emptyfollowdiv +" class='follow' onclick='checkFollowUserLogin("+commentsArray[i]['subscription_id']+","+commentsArray[i]['id']+","+followuser+","+loginid+");'>Stop Following</div>";												
													}else{
													var followuser=1;	
													discussionListing +="<div id="+ emptyfollowdiv +" class='follow' onclick='checkFollowUserLogin("+commentsArray[i]['subscription_id']+","+commentsArray[i]['id']+","+followuser+","+loginid+");'>Follow " + commentsArray[i]['fname'] + "</div>";	
													}		
				
												}
											
												commentsuspend='comment_suspend' + commentsArray[i]['id'];							
													discussionListing +="<div class='PostErrorComment' id="+ commentsuspend + "></div>";											emptydiv='emptydiv' + commentsArray[i]['id'];
													if(moderator=='1'){
													discussionListing +="<div id=" + emptydiv + " onclick='sendRequestSuspend("+commentsArray[i]['id']+",\""+topic_id+"\");'><span class='Report_Abuse'>Suspend</span></div>";														
													}else{
														if(loginid!=commentsArray[i]['subscription_id']){
														discussionListing +="<div id=" + emptydiv + " onclick='checkLoginUserAbuseRequest("+ loginid+","+commentsArray[i]['id']+",\""+commentsArray[i]['fullusername']+"\",\""+commentsArray[i]['reportabuse']+"\","+commentsArray[i]['subscription_id']+");'><span class='Report_Abuse'>Report Abuse</span></div>";														
														}
													}
													discussionListing +="</div>";
													
													discussionListing +="</li>";		
												
											}												
											jQuery("#comment_listing").append(discussionListing);
											
											//Start Code to Implement fancy zoom on all the ticker charts if any
											if($('hidIsOwn').value == '1')
											{
												var allChartList = jQuery('#tickerPostBody a.fancyzoom_special');
												var allChartArray = $A(allChartList);
												for(i = 0;i<allChartArray.length;i++)
												{				
													// If Chart's Chat Id is less that the least parsed id in previous rotation
													var chat_chart_id = allChartArray[i].id.split('_')[3];
													if(chat_chart_id < $('hidLeastParsedId').value)
													{
														jQuery('#'+allChartArray[i].id).fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 300});
													}
												}	
											}
											//End Code to Implement fancy zoom on all the ticker charts if any
											
											$('hidLeastParsedId').value=least_parsed_id;
											$('showticker').value=commentsArray[0]['showticker'];
											if(is_viewmore)
											{												
												if(!$("view_more"))
												{
													viewmore_link = '<div id="view_more" onClick=getMoreComments(); class="load_more">Click To Load More</div>';												
													jQuery("#tickerPostBody").append(viewmore_link);
												}

											}
											else
											{												
												$("view_more").remove();
											}											
										}
									});
}

function showTickerProgress (message) { // puts spinner in specified div
	if(!message)
	{
		message = 'Loading...';
	}
	var x = $('msgid');
	x.innerHTML = message;
	// this would be a good place to include a timeout function
}
function finishTickerProgress () { // puts spinner in specified div
	var x = $('msgid');
	x.innerHTML = '';
	// this would be a good place to include a timeout function
}

function doRegister(){
	$("showtickermsg").style.display='none';
	var curtime=new Date().getTime();
	var url= host+'/tickertalk/tickerajaxcontroller.php';
	var pars ='';
	$('tickererrmsg').innerHTML='&nbsp;';
	
	if(tickertrim($('fname').value)==''){
		$('tickererrmsg').innerHTML='Enter first name.';
		$('fname').select();
		return false;		
	}
	if(validateTickerChars(tickertrim($('fname').value))==false){
		$('tickererrmsg').innerHTML='Enter valid first name.';
		$('fname').select();
		return false;		
	}
	if(tickertrim($('lname').value)==''){
		$('tickererrmsg').innerHTML='Enter last name.';
		$('lname').select();
		return false;		
	}	
	if(validateTickerChars(tickertrim($('lname').value))==false){
		$('tickererrmsg').innerHTML='Enter valid last name.';
		$('lname').select();
		return false;		
	}	
	
	if(tickerisValidEmail('tickererrmsg','uid')==false){
		//window.location.href=host+'/subscription/register/#Registrationstep1';
		$('uid').select();
		return false;
	}
	if(tickertrim($('pwd').value)==''){
		$('tickererrmsg').innerHTML='Enter password.';
		$('pwd').select();
		return false;
	}
	if($('tc').checked==false){
		$('tickererrmsg').innerHTML='You did not agree with our privacy terms.';
		return false;
	}
	
	
	
	pars = 'type=doregister';
	pars = pars+'&firstname='+tickertrim($('fname').value);
	pars = pars+'&lastname='+tickertrim($('lname').value);	
	pars = pars+'&uid='+tickertrim($('uid').value);
	pars = pars+'&pwd='+tickertrim($('pwd').value);
	pars = pars+'&timestamp='+curtime;
	
var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:function(){$('tt_register_active').hide();$('tt_register_fade').show();},onComplete:function finishRegister(req){
		post = eval('('+req.responseText+')');
		if(post.status==false){
			$('tt_register_active').show();
			$('tt_register_fade').hide();
			if(post.blockip){
				$('tickererrmsg').style.display='block';
				$('tickererrmsg').innerHTML=tickertrim(post.blockip);
				return false;
			}
			if(post.msg){
				$('showerrormsg').style.display='none';
				$('tickererrmsg').innerHTML=tickertrim(post.msg);
				return false;
			}			
		}
		else{
			location.replace( host + "/subscription/activate.htm");
			/*$('showerrormsg').style.display='none';
			showfilter();		// show combobox	
			$('tickerlogin').style.display="none";
			var url=host+'/tickertalk/post.php';
			var show;
			var topicchatid=$('hidTopicId').value
			var pars='showbox=' + show + '&topicchatid=' + topicchatid;
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment}); */

		}
		
	}//end of finishLogin
	});	
}

function doLogin(action,abuse_item_id,abusedname,abuseuserid){
	$("showtickermsg").style.display='none';
	var curtime=new Date().getTime();
	var url= host+'/tickertalk/tickerajaxcontroller.php';
	var pars ='';
	if(tickerisValidEmail('tickererrmsg','uid')==false){
		$('uid').select();
		return false;
	}
	if($('pwd').value==''){
		$('tickererrmsg').innerHTML='Enter password.';
		$('pwd').select();
		return false;
	}	

	pars = 'type=dologin';
	pars = pars+'&uid='+tickertrim($('uid').value);
	pars = pars+'&pwd='+tickertrim($('pwd').value);
	pars = pars+'&timestamp='+curtime;
	
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onLoading:function(){$('tt_login_active').hide();$('tt_login_fade').show();},onComplete:function finishLogin(req){
		
		post = eval('('+req.responseText+')');
		
		if(post.status==false){
			if(post.msg=='Inactive account'){
				location.replace( host + "/subscription/activate.htm");
			}else{
			$('tickererrmsg').style.display='block';
			$('tickererrmsg').innerHTML=tickertrim(post.msg);
			loginStatus=false;
			$('tt_login_active').show();
			$('tt_login_fade').hide();
			}
		}
		else{
				showfilter(); // show combobox
				$('showbuzz').innerHTML='<a onclick=\'window.open("' +host + '/buzz/buzz.php","Banter","width=350,height=708,resizable=yes,toolbar=no,scrollbars=no");banterWindow.focus();\'><img src= '+image_server+ '/images/tickertalk/launch_buzz.jpg\ border=\'0\' align=\'right\' alt=\'Buzz\' /></a>';
				
			    $('showerrormsg').style.display='none';
						if(action=='reportabuse'){
					var url=host + '/tickertalk/request.php';
					var checkuser=1;
					var pars='checkuser=' + checkuser + '&abuseuserid=' + abuseuserid + '&abuse_item_id=' + abuse_item_id +'&abusedname=' + abusedname;
					var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:getReportAbuserDeatail});
				}else if(action=='followuser'){
					$('tickerlogin').style.display="none";
					var subscribeid=abuse_item_id;
					var commentid=abusedname;
					var followuser=abuseuserid;
					var followrequest=1;
					var url=host+ '/tickertalk/request.php';
					var pars = 'followrequest=' + followrequest + '&commentid=' + commentid + '&subscribedid=' + subscribeid + '&followuser='+followuser;
					var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:getFollowMesg});
				}else if(action=='watchlist'){
					 var addwatchlist=1;
					 var stockid=abuse_item_id;
					 var addstock=abusedname;
					 var url=host+ '/tickertalk/request.php';
					 var pars = 'stockid=' + stockid + '&addstock=' + addstock + '&addwatchlist=' + addwatchlist;
					 var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:getWatchListRequest});				
				}else if(action=='postcomment'){
					var topicid=abusedname;
					var stockid=abuseuserid;
					showCommentPostDiv('','','','',topicid,stockid);
					$("tickerlogin").style.display="none";
					}else{
					// show post comment text area
					$('tickerlogin').style.display="none";
					loginStatus=true;
					var url=host+'/tickertalk/post.php';
					var show;
					var topicchatid=$('hidTopicId').value
					var pars='showbox=' + show + '&topicchatid=' + topicchatid;
					var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});
				}
			}

		//}
		
	}//end of finishLogin
	});
}

function populatePeopleYouFellow(req){
	var post = eval('('+req.responseText+')');
	refreshFilterCombo();
	populateFilterCombo('My Watchlist','MW');
	var clength =post.length;
	if(clength>0){
		populateFilterCombo('-- People You Follow','FA','disabled');
		var i;
		for(i=0;i<=post.length; i++){
			var val='USER_'+ post[i].subscribed_to;
			populateFilterCombo(post[i].name,val,'');
		}
	}
	
	
	
	
}

function getReportAbuserDeatail(req){
	$('tickerlogin').style.display="none";
	result=(req.responseText).split("~");
	if(req.responseText=='notabuse'){
	  $('showerrormsg').style.display='block';	
	  $('showerrormsg').innerHTML="You can't report abuse your own post";
	}else if (isNaN(result[0])){
		('showerrormsg').style.display='none';	
		$("emptydiv" + result['1']).style.display='none';
		$("comment_suspend" + result['1']).innerHTML=result['0'];
	}else{
		 $('showerrormsg').style.display='none';	
 	     var abuse_item_id=result[0];
         var abusedname=result[1];
		 $('postabuse').style.display="block";
		 var str="";
		 str +="<div class='talkreply'>";
			 str +='<div class="close_button_reply_reportabuse"><h1>Please tell us what do you found abusive about this post:</h1><img vspace="3" align="right" style="cursor: pointer;" onclick="cancelReport();" alt="Login" src="'+host+'/images/tickertalk/closeLogin.jpg"/></div>';
			 str +="<textarea class='expand68-200' name='replyreport' id='replyreport' cols='6' rows='' maxlength='180' onfocus='return countPostCharactersReportAbuse(this);' onkeyup='return countPostCharactersReportAbuse(this);' onmousedown='return countPostCharactersReportAbuse(this);' onmouseup='return countPostCharactersReportAbuse(this);' onclick='clearTextReplyDiv();'></textarea>";
			 str +="<div id='showerr' class='PostError'>&nbsp;</div>";		 
			 str +="<div id='enable_submit' class='talkreplyLeft'><span id='showcharcount_bottom'></span><img src='"+host+"/images/tickertalk/reply_submit.jpg' border='0' align='absmiddle' alt='Submit Reply' onclick='submitReport("+abuse_item_id+",\""+ abusedname + "\");'/></div>";
		 str +="</div>";
		 $("postabuse").innerHTML=str;
	}
}
function showFconnect(){
	var url = host+'/lib/facebook/_fbconnect_lib.php';
	var parms = 'case=displayFBConnect';
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: parms,onComplete:function(req){
		var res = req.responseText;
		$('fconn').innerHTML = res;	
	}});	
	FB.init("6751423f6be0884e2bf1c406fafa1c1c", "xd_receiver.htm", {"ifUserConnected" : auth_fb});
	return true;
}
function showCommonBox(action,abuse_item_id,abusedname,abuseuserid,loginmsg){

$("showtickermsg").style.display='none';
$("postcomment").style.display="none";
$('tickerlogin').innerHTML='<div class="TickerLogin"><div class="closelogin"><span><h1><span class="close_button"><div id="loginmsg" class="loginmsg_commonbox">' + loginmsg + '</div><img vspace="3" align="right" style="cursor: pointer;" onclick="closeLogin(0);" alt="Login" src="'+host+'/images/tickertalk/closeLogin.jpg"/></span>Want to join the <br/>discussion? </h1><h2>Log-in now or sign up <br/>for a free Minyanville ID.</h2></div><img src="'+host+'/images/tickertalk/TickerLogin.jpg" hspace="6" align="absmiddle" alt="Login" onclick="Javascript:showLoginBox(\''+action+'\',\''+abuse_item_id+'\',\''+abusedname+'\',\''+abuseuserid+'\');" style="cursor:pointer;" /><img src="'+host+'/images/tickertalk/TickerRegister.jpg" align="absmiddle" alt="Register" style="cursor:pointer;" onclick="Javascript:showRegisterBox();" /><div id="fconn"></div></div>';
showFconnect();
}
 
function showLoginBox(action,abuse_item_id,abusedname,abuseuserid){
   
	$("showtickermsg").style.display='none';
	$('tickerlogin').innerHTML='<div class="TickerLogin"><div class="close_button"><img vspace="3" align="right" style="cursor: pointer;" onclick="closeLogin(1);" alt="Login" src="'+host+'/images/tickertalk/closeLogin.jpg"/><div class="tickerError" id="tickererrmsg">&nbsp;</div></div><label>Username (Email)<input id="uid" type="text" onKeyPress="Javascript:get_login_keys(event);" /></label><label>Password<input id="pwd" type="password" onKeyPress="Javascript:get_login_keys(event);" /></label><label><img  id="tt_login_active" style="cursor:pointer;" src="'+host+'/images/tickertalk/TickerLogin.jpg" align="left" alt="Login"  hspace="0"/ onclick="Javascript:doLogin(\''+action+'\',\''+abuse_item_id+'\',\''+abusedname+'\',\''+abuseuserid+'\');"><img  id="tt_login_fade" style="display:none;" src="'+host+'/images/tickertalk/TickerLogin_hide.jpg" align="left" alt="Login"  hspace="0" /><div class="talkreplyRight" onclick="Javascript:closeLogin(1);">Cancel</div></lable></div>';
}
function showRegisterBox(){
	var tcurl=host + "/community/exchange_register/Subscription-Agreement.html";
	$("showtickermsg").style.display='none';
	$('tickerlogin').innerHTML='<div class="TickerLogin"><div class="close_button"><img vspace="3" align="right" style="cursor: pointer;" onclick="closeLogin(1);" alt="Login" src="'+host+'/images/tickertalk/closeLogin.jpg"/><div class="tickerError" id="tickererrmsg">&nbsp;</div></div><label>First Name<input id="fname" type="text" onKeyPress="Javascript:get_register_keys(event);" /></label><label>Last Name<input id="lname" type="text" onKeyPress="Javascript:get_register_keys(event);" /></label><label>Username (Email)<input id="uid" type="text" onKeyPress="Javascript:get_register_keys(event);" /></label><label>Password<input id="pwd" type="password" onKeyPress="Javascript:get_register_keys(event);" /><span class="inputcheck" ><input type="checkbox" id="tc" name="tc" />I agree to the <a href="#" onclick="window.open(\''+tcurl+'\',\'terms\',\'width=560,height=500,resizable=1,scrollbars=1\')">Terms of Use</a></span></label><img id="tt_register_active" src="'+host+'/images/tickertalk/TickerRegister.jpg" align="left" alt="Login" class="embed_register" onclick="Javascript:doRegister();" /><img id="tt_register_fade" src="'+host+'/images/tickertalk/TickerRegister_hide.jpg" align="left" alt="Login" style="display:none; padding-left:25px;" /><div class="talkreplyRight" onclick="Javascript:closeLogin(1);">Cancel</div></div>';
}
function closeLogin(state){
	$("showtickermsg").style.display='none';
	// close login / register box
	if(state==0){
		$('tickerlogin').style.display="none";
	}
	else{
		showCommonBox('','','','','');
	}
}

/*Image upload functions*/
function $m(theVar){
	return document.getElementById(theVar);
}
function remove(theVar){
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}
function removeEvent(obj, type, fn){
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}
function isWebKit(){
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}
function ajaxUpload(form,url_action,id_element,html_show_loading,html_error_http){
	var detectWebKit = isWebKit();
	form = typeof(form)=="string"?$m(form):form;
	var erro="";
	if(form==null || typeof(form)=="undefined"){
		erro += "The form of 1st parameter does not exists.\n";
	}else if(form.nodeName.toLowerCase()!="form"){
		erro += "The form of 1st parameter its not a form.\n";
	}
	if($m(id_element)==null){
		erro += "The element of 3rd parameter does not exists.\n";
	}
	if(erro.length>0){
		alert("Error in call ajaxUpload:\n" + erro);
		return;
	}
	var iframe = document.createElement("iframe");
	iframe.setAttribute("id","ajax-temp");
	iframe.setAttribute("name","ajax-temp");
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	form.parentNode.appendChild(iframe);
	window.frames['ajax-temp'].name="ajax-temp";
	var doUpload = function(){
		removeEvent($m('ajax-temp'),"load", doUpload);
		var cross = "javascript: ";
		cross += "window.parent.$m('"+id_element+"').innerHTML = document.body.innerHTML; void(0);";
		$m(id_element).innerHTML = html_error_http;
		$m('ajax-temp').src = cross;
		if(detectWebKit){
        	remove($m('ajax-temp'));
        }else{
        	setTimeout(function(){ remove($m('ajax-temp'))}, 250);
        }
    }
	addEvent($m('ajax-temp'),"load", doUpload);
	form.setAttribute("target","ajax-temp");
	form.setAttribute("action",url_action);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");
	if(html_show_loading.length > 0){
		$m(id_element).innerHTML = html_show_loading;
	}
	form.submit();
	
}

function hideUpload()
{
	$m('left_col').style.display='none';
}

/*Image upload functions end here*/

	var swfu;
	function showUploadTool() {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: host+"/tickertalk/file_upload/upload.php",
				
				// File Upload Settings
				file_size_limit : "2 MB",	// 2MB
				file_types : "*.jpg;*.gif",
				file_types_description : "Images Only",
				file_upload_limit : "0",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : image_server+"/images/tickertalk/tt_addchart.gif",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 75,
				button_height: 18.9,
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : host+"/tickertalk/file_upload/swf/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
			});

		};

function clearTextReplyDiv(){
	$("showerr").innerHTML='&nbsp';
}

function sendRequestSuspend(commentid,topicid){
	$("showerrormsg").style.display='none';
	var suspend=1;
	var url=host+ '/tickertalk/request.php';
	var pars = 'suspend=' + suspend + '&commentid=' + commentid + '&topicid=' + topicid;
	var postAjax = new Ajax.Request(url, {method: 'post',onLoading:showTickerProgress(),parameters: pars,onComplete:getSuspendMesg});
}

function getSuspendMesg(req){
	$('msgid').innerHTML='';
	var result=(req.responseText).split("~");
	$("emptydiv" + result['1']).style.display='none';
    $("comment_suspend" + result['1']).style.display='block';
	$("comment_suspend" + result['1']).innerHTML=result['0'];	
	
}

function checkLoginUserAbuseRequest(abuserid,abuse_item_id,abusedname,checkreportabuse,abuseuserid){
	$("showerrormsg").style.display='none';
	$('msgid').style.display='block';
	if(abuserid){
		sendReportAbuse(abuserid,abuse_item_id,abusedname,checkreportabuse,abuseuserid);
	}else{
		reportabuse=1;
		var url=host+ '/tickertalk/request.php';
		var pars = 'reportabuse=' + reportabuse + '&abuse_item_id=' + abuse_item_id + '&abusedname=' + abusedname + '&checkreportabuse=' + checkreportabuse + '&abuseuserid=' + abuseuserid;
		var postAjax = new Ajax.Request(url, {method: 'post',onLoading:showTickerProgress(), parameters: pars,onComplete:getResponseLoginUser});
		
	}
}

function getResponseLoginUser(req){
	$('msgid').innerHTML='';
	var result=(req.responseText).split("~");
	if(result[0]=="reportabuse"){
		if(result[1]!=result[5]){
			$('showerrormsg').style.display='none';	
		sendReportAbuse(result[1],result[2],result[3],result[4],result[5],result[6]);		
		}else{
			$('showerrormsg').style.display='block';
			$('showerrormsg').innerHTML="You can't report abuse your own post";			
		}
	}
	
}

function sendReportAbuse(abuserid,abuse_item_id,abusedname,checkreportabuse,abuseuserid,loginmsg){
		var action='reportabuse';
		if(abuserid){
			if(checkreportabuse!=0){
				$("comment_suspend" + abuse_item_id).innerHTML="Already Sent";
				$("emptydiv" + abuse_item_id).style.display='none';
			}else{
				showReportAbuseCommentPost(abuse_item_id,abusedname);	
				$("postcomment").style.display='none';	
				$("postabuse").style.display='block';	
			}
			
		}else{
			$('tickerlogin').style.display='block';
			showCommonBox(action,abuse_item_id,abusedname,abuseuserid,loginmsg);	
		}
}

function showReportAbuseCommentPost(abuse_item_id,abusedname){
		 var str="";
		 str +="<div class='talkreply'>";
		 str +="<div class='rprtabuse_box'>"
 		 str +='<div class="close_button_reply_reportabuse"><img vspace="3" align="right" style="cursor: pointer;" onclick="cancelReport();" alt="Login" src="'+host+'/images/tickertalk/closeLogin.jpg"/><h1>Please tell us what do you found abusive about this post:</h1></div>';
		 str +="<textarea name='replyreport' id='replyreport' cols='6' rows='' maxlength='180' onfocus='return countPostCharactersReportAbuse(this);' onkeyup='return countPostCharactersReportAbuse(this);' onmousedown='return countPostCharactersReportAbuse(this);' onmouseup='return countPostCharactersReportAbuse(this);' onclick='clearTextReplyDiv();'></textarea>";
		 str +="<div id='showerr' class='PostError'>&nbsp;</div>";
		 str +="<div id='enable_submit' class='talkreplyLeft'><span  id='showcharcount_bottom' ></span><span class='btn_space'><img src='"+host+"/images/tickertalk/reply_submit.jpg' border='0' align='absmiddle' alt='Submit Reply' onclick='submitReport("+abuse_item_id+",\""+ abusedname + "\");'/></span</div></div>";
		 str +="</div>";
		 $("postabuse").innerHTML=str;
	}
	
	function submitReport(abuse_item_id,abusedname){
		var abuse=1;
		reporttext=checktextbody($("replyreport").value);
		if(reporttext.length>10){
								reporttext=encodeURIComponent(reporttext);
								var url=host+ '/tickertalk/request.php';
								var pars = 'abuse=' + abuse + '&reporttext=' + reporttext + '&abuse_item_id=' + abuse_item_id + '&abusedname=' +abusedname;
								var postAjax = new Ajax.Request(url, {method: 'post',onLoading:showTickerProgress(),parameters: pars,onComplete:getAbuseMesg});
		}else if((reporttext.length<=10)&&(reporttext.length>=1)){
			$("showerr").innerHTML="Post must be more than 10 characters";
		}else{
			$("showerr").innerHTML="Please enter a text!";
			
		}					
		
	}	
	
	function getAbuseMesg(req){
		$('msgid').innerHTML='';
		var result=(req.responseText).split("~");
		if(result['0']=='msg'){
			$('showerrormsg').style.display='block';
			$('showerrormsg').innerHTML=result['1'];
			$("postabuse").style.display='none';
		}else{
		$('showerrormsg').style.display='none';	
		$("emptydiv" + result['1']).style.display='none';
		$("comment_suspend" + result['1']).innerHTML=result['0'];
		$("postabuse").style.display='none';
		}
		
	}
		
	function cancelReport(){
		$("postabuse").style.display='none';
	}	
	
	
	function applyFilter(filter){
		    if(filter){
				loadDiscussion(filter);			
				$('shareideas').style.display='none';
				$('postcomment').style.display='none';
			}
	}
	
	function populateFilterCombo(combotext,combovalue,disable){
		var combo = document.getElementById("filtercombo");    
		var option = document.createElement("option");  
		option.text = combotext;  
		option.value = combovalue;
		option.disabled=disable;
		if(disable=='disabled'){
		option.setStyle({
			fontWeight:'bold'
			});
		}
		try {
			
			combo.add(option, null); //Standard  
		}catch(error) {  
			combo.add(option); // IE only  
		}
	}
	function refreshFilterCombo(){
		var combo = document.getElementById("filtercombo");
		var i;
		for(i=combo.options.length-1;i>=2;i--)
		{
			combo.remove(i);
		}

	}
	
	function checkFollowUserLogin(subscribeid,commentid,followuser,loginid){
		$('msgid').style.display='block';
		$('showerrormsg').style.display='none';	
		if(loginid){
			sendFollowRequest(subscribeid,commentid,followuser,loginid);
		}else{
			folowlogin=1;
			var url=host+ '/tickertalk/request.php';
			var pars = 'folowlogin=' + folowlogin + '&subscribeid=' + subscribeid + '&commentid=' + commentid + '&followuser=' + followuser;
			var postAjax = new Ajax.Request(url, {method: 'post',onLoading:showTickerProgress(), parameters: pars,onComplete:getResponseFolowLoginUser});
		}
		
	}
	
	function getResponseFolowLoginUser(req){
		$('msgid').innerHTML='';
		var result=(req.responseText).split("~");
		sendFollowRequest(result[0],result[1],result[2],result[3],result[4]);		
		
	}
	
	function sendFollowRequest(subscribeid,commentid,followuser,loginid,loginmsg){

		if(loginid){
			var followrequest=1;
			var url=host+ '/tickertalk/request.php';
			var pars = 'followrequest=' + followrequest + '&commentid=' + commentid + '&subscribedid=' + subscribeid + '&followuser='+followuser;
			var postAjax = new Ajax.Request(url, {method: 'post',onLoading:showTickerProgress(), parameters: pars,onComplete:getFollowMesg});
		}else{
			$("tickerlogin").style.display="block";
			var action='followuser';
			showCommonBox(action,subscribeid,commentid,followuser,loginmsg);
		}
	}

	function getFollowMesg(req){
		$('msgid').innerHTML='';
		var result=(req.responseText).split("~");
		if(result[0]!='msg'){
		/* update filter combo on follow/ stopfollowing */	
		/*var url=host+'/tickertalk/request.php';	
		pars = 'followpeople=1';
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:populatePeopleYouFellow});	*/
		showfilter();			
			$("emptyfollowdiv" + result['1']).style.display='none';
			$("followmsg" + result['1']).innerHTML=result['0'];	
		}else{
			 $('showerrormsg').style.display='block';	
		     $('showerrormsg').innerHTML=result[1];			
		}
		
	}	
	function openImages(image_path)
	{	
	
		if($('hidIsOwn').value == '1')
		{
			// open ibox	
			var ar_image_path = image_path.split(".");	
			var new_image_path;
			total = ar_image_path.length;
			var nwar =  Array();
			for(i=0;i<total;i++)
			{
				
				if(i==total-1)
				{					
					new_image_ext =ar_image_path[i];
				}
				else
				{
					nwar[i] = ar_image_path[i];
				}
			}
			//alert(nwar);
			new_image_path =nwar.join(".");
			// dot was causing problem while passing in ibox URL so image path need to send in different parametrs			
			var img = new Image();
			img.src = image_path;
			img.onload = function() { //alert("Callback: " + this.width);
			width = img.width;
			height = img.height+20;						
			var url = host+'/tickertalk/show_image.htm?image_name='+new_image_path+'&ext='+new_image_ext+'&height='+height+'&width='+width;			
			init_ibox('ticker_talk',url);
			};
		}
		else
		{		
			window.open(image_path);
		}		
	}    
  function setUserWatchList(loginid,stockid,addstock){
	 $("showerrormsg").style.display='none'; 
	 if(loginid){ 
		 var addwatchlist=1;
		 var url=host+ '/tickertalk/request.php';
		 var pars = 'stockid=' + stockid + '&addstock=' + addstock + '&addwatchlist=' + addwatchlist;
		 var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:getWatchListRequest});
	 }else{
		 	var action='watchlist';
			$("tickerlogin").style.display='block';
			showCommonBox(action,stockid,addstock,'','Please login to add a stock');
     }	  
  }
 function getWatchListRequest(req){
	  $('add_tag').innerHTML=req.responseText;
	  $('tickerlogin').style.display="none";
	  
 } 

function applyComboFilter(filter){
	if(filter){
		$('add_tag').innerHTML='';
		$('selectval').innerHTML=$(filter).innerHTML;
		$('topic_name').innerHTML=$(filter).innerHTML;
		loadDiscussion(filter);			
		$('shareideas').style.display='none';
		$('jointicker').style.display='';
		$('postcomment').style.display='none';
		$('msgid').innerHTML='';
		$('drop_down').style.display='none';
	}
}
function selectCombo()
{
	if($('drop_down').style.display=='none')
	{
		$('drop_down').style.display='';
		$('showerrormsg').style.display='none';
	}
	else
	{
		$('drop_down').style.display='none';
		$('showerrormsg').style.display='none';
	}
}

function showfilter(){
		var url=host+'/tickertalk/request.php';	
		pars = 'showfilter=1';
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showfiltercombobox});
}

function showfiltercombobox(req){
	$('filterdiv').innerHTML=req.responseText;
	$('drop_down').style.display='none'
}

function focusedsymbol(obj) {
    if (obj.value == obj.defaultValue) {
        obj.value = "";
    }
}

function blurredsymbol(obj) {
    if (obj.value == "") {
        obj.value = obj.defaultValue;
    }
}

function showFeedBack(){
	$('tickerlogin').style.display='none';
	$('postcomment').style.display='none';
	$('postcomment').innerHTML='';
	$('showerrormsg').style.display='none';
	var url=host + '/tickertalk/request.php';	
	var pars='feedback=1';
	var postAjax= new Ajax.Request(url,{method:'post',parameters:pars,onComplete: function getFeedBack(req){
		$('postcomment').style.display='block';
		$('postcomment').innerHTML=req.responseText;
	}});

}

function sendFeedBack(badwords,longwmsg,notallowed,emptytext){
	$('showerrfeedback').style.display='block';
	textbody=tickertrim($('reply').value);
	if(tickertrim($('username').value)!=''){
		if(!isNaN(tickertrim($('username').value))){
			$('showerrfeedback').innerHTML='Enter valid username.';
			$('username').select();
			return false;		
		}
	}
	if(tickerisValidEmail('showerrfeedback','uid')==false){
		$('uid').select();
		return false;
	} else if(tickertrim($('reply').value)==''){
		$('showerrfeedback').innerHTML=emptytext;
		$('reply').select();
		return false;
	}else if(textbody.length<=10){
		$('showerrfeedback').innerHTML="Post must be more than 10 characters";
		$('reply').select();
		return false;
	}else{
		
		if(textbody.length>0){
			var matchedbadwords="";
			var objWordLength=new Object();
			objWordLength.myParameter=false;
			matchedbadwords=matchBadWords(badwords,textbody,matchedbadwords,objWordLength,43);
			if(matchedbadwords.length>0){ // if bad words are found in posted comment.
				alert(notallowed + '\n'+matchedbadwords);
				return false;
			}else{
				var url=host + '/tickertalk/request.php';	
				var pars='sendfeedback=1' + '&username=' + $('username').value + '&email=' + $('uid').value + '&feedbacktxt=' + textbody;
				var postAjax= new Ajax.Request(url,{method:'post',parameters:pars,onComplete: function setFeedBack(req){
					$('postcomment').style.display='none';
					$('showerrormsg').style.display='block';
					$('postcomment').innerHTML='';
					$('showerrormsg').innerHTML=req.responseText;
				}});
			}
		}
	}
	
}

function clearErrFeedback(){
	$("showerrfeedback").innerHTML='&nbsp';
}
function showUploadImage(){
	var url=host + '/tickertalk/request.php';	
	var pars='showupload=1';
	var postAjax= new Ajax.Request(url,{method:'post',parameters:pars,onComplete: function showUploadurltext(req){
		$('uploadurl').style.display='block';	
//		$('fconn').style.display='none';
		$('uploadurl').innerHTML=req.responseText;
	}});
		
}	

function setuploadchart(){
	var url= host + '/tickertalk/request.php';	
	var uploadurl=checktextbody($('urlupload').value);
	if(document.upload_image.chkImage)
	{
		var uploaded_image_length = document.upload_image.chkImage.length;
		if(uploaded_image_length>4){
		    $('showerr').innerHTML='You can upload only 5 images!';
			return false;
		}
	}
	if(uploadurl==''){
		$('showerr').innerHTML='Please enter url';
	}else if(uploadurl.search(/,|;/)!=-1){
		$('showerr').innerHTML='Please enter single url';
	}else{
		var pars='uploadurl=1' + '&url=' + encodeURIComponent(uploadurl);
		var postAjax= new Ajax.Request(url,{method:'post',parameters:pars,onComplete: function showUploadurltext(req){
			 uploadUrl(req.responseText);
		}});
	}	
}


function uploadUrl(serverData) {	
	try {			
		if (serverData.substring(0, 7) === "FILEID:") {
			image_link = '<input type="checkbox" checked="checked" value="'+serverData.substring(7)+'" name="chkImage[]" id="chkImage"/>'+serverData.substring(7,27)+"<br>";
			jQuery("#uploaded_image").append(image_link);
			$("showerr").innerHTML="";
			$("urlupload").value='paste image url here';
		} else {
			$("showerr").innerHTML=serverData;
		}
		$("enable_submit").show();
		$("disable_submit").hide();
	} catch (ex) {
		this.debug(ex);
	}
}

function showUploadurl(){
	
	if($('uploadurl').style.display=='none')
	{
		$('uploadurl').style.display='block';
		//showfilter(hide=0); // for logout
	}
	else
	{
		$('uploadurl').style.display='none'
	}
	
}

	function displayReply(commentid){
		$('replyid'+commentid).style.display='block';
	}
	
	function hideReply(commentid){
		$('replyid'+commentid).style.display='none';
	}
	
	function showReplyBox(postid,stocksymbol){
		$('postcomment').style.display='none';
		var show=1;
		showReply(show,postid,stocksymbol);
	}

 function showReply(show,postid,stocksymbol){	
	// check login status of user
	$('tickerlogin').style.display="none";
	var url=host+'/tickertalk/post.php';
	var topicchatid=$('hidTopicId').value
	var pars='showbox=' + show + '&topicchatid=' + topicchatid + '&postid=' + postid + '&stocksymbol=' + stocksymbol;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:showPostComment});
 }

function showEmbed(){
$('tickerlogin').style.display='none';
$('postcomment').style.display='none';
$('showerrormsg').style.display='none';
var str="";
str +="<div class='talkreply'>";
str +="<div class='close_button_reply'>";
str +="<span class='embed_msg'>Share this Talk on your site</span><img vspace='3' align='absmiddle' src='/images/tickertalk/closeLogin.jpg' alt='Close' onclick='cancelComment();' style='cursor: pointer;'/>";
str +="</div>";
str +="<input type='text' class='embed_input' value='&lt;iframe src=&quot;";
str += host + "/tickertalk/index.htm";
str += "&quot; scrolling=&quot;no&quot; frameborder=&quot;0&quot; width=&quot;226&quot; height=&quot;730&quot;&gt;&lt;/iframe&gt;'/>";
str +="</div";
$('postcomment').style.display='block';
$('postcomment').innerHTML=str;
}

