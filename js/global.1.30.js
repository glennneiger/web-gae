
var $j = jQuery.noConflict();

$j(document).ready(function() {				
							

var page = $j("#page-name").html();
// you can manually set the page type here which controls the nav
// var page = "randomthoughts";

if (page == "") {
   page = "generic";
}



if($j(".article_").html())
{
	var pos = page.indexOf("article_");
    if (pos>=0) {
        page = page.substring(8);
    }
}
    
if (page != "home" || page == "generic") {
	$j("#main-navigation li").each( function() {
            var current = $j(this).attr("id");
            if (page == current) {
                showSubNav(page);
                return;
	    }
	});

        $j("#sub-navigation-container li").each( function() {
             var currentSubNav = $j(this).attr("id");
                 if (page == currentSubNav) {
                       $j(this).addClass("sub-on");
			var parentSection = $j(this.parentNode).attr("id");
			parentSection = parentSection.substring(4);
                        showSubNav(parentSection);
                        return;
		}
	});

} 

	$j("#main-navigation li").bind("mouseenter", function(e) {
                var state = $j(this).attr("class");
                if (state == "on") {
                   return;
                } else {
		    $j("#main-navigation li").removeClass("on");
                    $j("#sub-navigation-container li").removeClass("sub-on");
        	    var id = $j(this).attr("id");
	            showSubNav(id);
                }
    	});

});

function showPopUp()
{
    var win_height = screen.height;
    var win_width = screen.width;
    var pop_up_height = Math.ceil((win_height*40)/100);

    $j("#fixbttm").css("height",pop_up_height+'px');
    $j("#mvMobilePopUp").css("display","block");
}

function submitFreeReport()
{
	var name = $j('#fmrName').val();
	var email = $j('#fmrEmail').val();
	
	
	if(name=="" || name=="Enter your name" || name ==null)
	{
		alert('Enter a name');
		return false;
	}
	if (name.match(/^\s*$/)) {
		alert('Enter a valid name');
		return false;
	}
        var nameArr = name.split(" ");
        var first_name = nameArr[0];
        nameArr.splice(0,1);
        var last_name = nameArr.join(' ');

	var emailCheck = isValidEmail('fmrEmail');
        if(emailCheck==false)
        {
            return false;
        }
	var elliott_check = $j('#check_1').is(':checked');
	var tchir_check = $j('#check_2').is(':checked');
	var keene_check = $j('#check_3').is(':checked');
	
	if(elliott_check==false && tchir_check==false && keene_check==false){
		alert('Please select atleast one free report!!');
		return false;
	}

	$j('#fmrSubmit').unbind('click');
	 $j.ajax({
		type : "POST",
		url : host+"/subscription/register/loginAjax.php",
		data : "type=signUpFreeModule&email="+email+"&first_name="+first_name+"&last_name="+last_name+"&elliott_check="+elliott_check+"&tchir_check="+tchir_check+"&keene_check="+keene_check,
		error : function(){},
		success : function(req){
			var post = eval('('+req+')');

		 	if(post.status==true )
			{
		 		displayFreeModuleWindow();
		 		$j('#fmrName').val('Enter your name');
		 		$j('#fmrName').removeAttr("readonly");
		 		$j('#fmrEmail').removeAttr("readonly");
		 		$j('#fmrEmail').val('Enter your email');
		 		$j('#check_1').attr('checked','checked');
		 		$j('#check_2').attr('checked','checked');
		 		$j('#check_3').attr('checked','checked');
			}
		},
		error : function(req){
			alert(req);
		}
	}); 

}

function closeFreeReportFancyBox(){
	jQuery("#fancybox-overlay").css("display", "none");
	jQuery("#fancybox-wrap").css("display", "none");
	
     }

function displayFreeModuleWindow(){
	jQuery.noConflict();
    jQuery.fancybox.init();
    jQuery.fancybox({
            showCloseButton : false,
    type: 'inline',
    content: '#freeModule',
    overlayOpacity : 0.8,
            overlayColor : '#000'
});
    jQuery('#fancybox-wrap').css('top','100px');
	
}

function clearText(id,text)
{
	str = $j('#'+id).val();
	if(str==text)
	{
		$j('#'+id).val('');
	}
}


function loadIframeTradCalen()
{
   var querystring = location.search.replace( '?', '' ).split( '&' );
   var params = new Array();
   // loop through each name-value pair and populate object
   for ( var i=0; i<querystring.length; i++ )
   {
          // get name and value
         var name = querystring[i].split('=')[0];
         var value = querystring[i].split('=')[1];
          // populate object
         params[name] = value;
   }
   var url = "http://kensho.com/widget/minyanville/"+params['Symbol'];
   jQuery('#tradCalenIframe').attr('src',url);
   
   var marketIqUrl = "http://fp.themarketiq.com/widgets/social-fundamentals/?symbol="+params['Symbol']+"&mode=social&campaign=minyanville";
   jQuery('#widgetMarketIq').attr('src',marketIqUrl);
}


function goToMobile(item_type,item_id)
{
	var now = new Date();
	var time = now.getTime();
	time += 3600*24*30*1000;
	now.setTime(time);
	document.cookie = "mvmobile=1;expires="+now.toGMTString();
	if(item_type=="1")
	{	
		if(item_id!="")
		{
			var url = "http://m.minyanville.com/section/MvNewsArticle?guid="+item_id;
		}
		else
		{
			var url = "http://m.minyanville.com/section/mv-news";
		}
		
	}
	else if(item_type=="18")
	{
		if(item_id!="")
		{
			var url = "http://m.minyanville.com/section/MvPremiumArticle?guid="+item_id;
		}
		else
		{
			var url = "http://m.minyanville.com/section/mvpremium";
		}
	}
	else
	{
		var url = "http://m.minyanville.com/section/mv-news";
	}
	redirectMobileSite(url);
}
function ContinueWeb()
{
	var now = new Date();
	var time = now.getTime();
	time += 3600*24*30*1000;
	now.setTime(time);
	 var $j = jQuery.noConflict();
	 document.cookie = "mvweb=1;expires="+now.toGMTString();
	 $j('#mvMobilePopUp').html('');
	
}


function showSubNav(id, section) {
        var $j = jQuery.noConflict();
	$j("#" + id).addClass("on");
	$j(".sub-nav").hide();
	$j("#sub-" + id).fadeIn("fast");
}

// article javascript begins here 
$j(document).ready(function() {
	resetFont();
	

    $j("#your-account").bind("click", function () {
        $j("#control-pannel").slideDown("slow");
    });

$j("#popular-module-nav li").bind("mouseenter", function() {
	var id = $j(this).attr("id");
	//alert(id)
	$j("#popular-module-nav li").each(function() {
		$j(this).removeClass("hover-on");
	});
	
	$j(".popular-module ol").hide();
	$j("#pm-" + id).fadeIn("fast");
	$j("#" + id).addClass("hover-on");

});

$j(".idTabs li").bind("mouseenter", function() {
        var id = $j(this).find("a").attr("id");
        //alert(id)
        $j(".idTabs li").each(function() {
                $j(this).removeClass("hover-on");
        });
        
        $j(".market_div").hide();
        $j("#market" + id).fadeIn("fast");
        $j("#" + id).parent().addClass("hover-on");

});

});

function increaseFont() {

	var current = $j("#article-content").css("font-size");
	current = parseInt(current);
	current = (current * 1.2);
        $j("#article-content").css("font-size", current);
}

function decreaseFont() {

        var current = $j("#article-content").css("font-size");
        current = parseInt(current);
        current = (current / 1.2);
        $j("#article-content").css("font-size", current);
}

function resetFont() {
	$j("#article-content").css("font-size", 10);
}

function goToComments() {
	  var $target = $j("#disqus_thread");
      $target = $target.length && $target 
      var targetOffset = ($target.offset().top - 20);
      $j('html,body').animate({scrollTop: targetOffset}, 1800);

}


function closeControlPanel() {
	$j("#control-pannel").slideUp("slow");

}

function reloadPage() {
	window.location.reload();
}

function setCookie(c_name,value,expiry)
{
	var obDate = new Date();
	if(expiry == 'midnight')
	{
		var today_year = obDate.getFullYear();
		var today_month =  obDate.getMonth();
		var today_day = obDate.getDate();
		var today_midnight = new Date(today_year, today_month, today_day,'23','59','59').getTime();
		obDate.setTime(today_midnight);
	}
	else // No of days 
	{
		obDate.setTime(obDate.getTime() + (expiry*24*60*60*1000));
	}
	var c_value=escape(value) + ((obDate==null) ? "" : "; expires="+obDate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name)
{
	var nameEQ = c_name + "=";
	var ca = document.cookie.split(';');	
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

var reloadstatus=1;
var stoprefresh=0;
var refreshTime=0;
function checkElement(id){
	var Val=false;
	if(document.getElementById(id)!=null){

		if(document.getElementById(id).style.display=="block"){
			Val=true;
		}
	}

	return Val;
}
function loadPage(){
	
	if(reloadstatus==0 || stoprefresh==1)
	{		
		window.setTimeout("loadPage();",this.refreshTime);
	}
	else
	{
		setCookie('autoRefresh','1');
		var url = window.location.href;
		if(url.indexOf('?')!= -1)
		{
			if(url.indexOf('refresh=1')!= -1)
			{
				var redirect_url = url;
			}
			else
			{
				var redirect_url = url+'&refresh=1';
			}
		}
		else
		{
			var redirect_url = url+'?refresh=1';
		}
		window.location = redirect_url;
	}	
}
function RefreshPage(status,time){

	this.refreshTime=time;

	if (status==1){
		window.setTimeout("loadPage();",time);
	}
}
function stopRefreshPage()
{	
	stoprefresh=1;
}

// article js ends here

// google virtual page tracking function

function googleVirtualPageTracking(page){
	if(pageTracker) {
		pageTracker._trackPageview(page);
	}
}

function getQuoteSearch()
{
	var quote = $j("#txtQuote").val();
	window.location = "http://finance.minyanville.com/minyanville?Page=QUOTE&Ticker="+quote+"&from=header";
}
function enableclick()
{
	document.onmousedown=new Function("return true");void(0);
}

function isValidEmail(field1){
	field=document.getElementById(field1);
	//if(!field.value) return true;
	var emails=field.value.split(';');
	for (var i=0;i<emails.length;i++)
	{
		if(emails[i]=='')
		{
			alert('Email field can not be left blank.') 
			field.select();
			field.focus();
			return false;
		}
		if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails[i])))
		{
			alert('Not a valid E-mail id "'+emails[i]+'"') 
			field.select();
			field.focus();			 
			return false;
		}
	}
	return true;
}
function chkSpaceNull(fieldId,str,defMsg)
{
	var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.
	if((str=='')||(str.length==0) || (str==null) || (str.search(re) > -1))
	{
		//$(fieldId).value=defMsg;
		return false;
	}
	else
	{
		return true;
	}
}


/* Text must be Alpha */
function validateAlphaFieldsOnly(errorDiv,field1,alertMsg) {
	var string = trim($F(field1));
	//alert(string.length+" "+field1);
	var bValid =new Boolean(true);
	if (!string) 
	{	
		bValid=false;
	}
	var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
	for (var i = 0; i < string.length; i++) {
		if (Chars.indexOf(string.charAt(i)) == -1)
		{
			bValid=false; 
		}
	}
	if(!bValid)
	{
		var msg='Not a valid '+alertMsg;
		$(errorDiv).innerHTML=msg;
		//alert(msg);
		$(field1).select();
	}
	return bValid;
}
function makeLinksNewWindow(divId)
{
  // Grab the appropriate div
  theDiv = document.getElementById(divId);  
  // Grab all of the links inside the div
  links = theDiv.getElementsByTagName('a');
  // Loop through those links and attach the target attribute
  for (var i=0, len=links.length; i < len; i++) {
    // the _blank will make the link open in new window
  links[i].setAttribute('target', '_blank');
  }
}
function refreshAds() 
{ 	
	setTimeout(refreshAds, 300000);
	var RequestID = CM8AjaxRefresh(adBanners);
} 
//setTimeout(refreshAds, 300000);  // Perform the AJAX request in 5 mins

function hideBuzzFancyBox(){
	$j("#inline1").hide();
	$j("#fancybox-outer").hide();
	$j("#fancybox-overlay").hide();
}

function realHpBuzz(pageid,module){
	$j.ajax({
		type : "POST",
		url : host+"/lib/_home_buzz_lib.php",
		data : "pageid="+pageid+"&module="+module,
		error : function(){},
		success : function(req){
			if(req!="" || req!="null" || req!="undefined"){
			   $j('#hpBuzz').html(req);
			}
		}
	});
}

function realEduBuzz(pageid,module){
	$j.ajax({
		type : "POST",
		url : host+"/lib/edu/eduModuleBuzzMod.php",
		data : "action=realbuzz&pageid="+pageid+"&module="+module,
		error : function(){},
		success : function(req){
			if(req!="" || req!="null" || req!="undefined"){
				$j('#hpBuzz').html(req);
			}
		}
	});
}