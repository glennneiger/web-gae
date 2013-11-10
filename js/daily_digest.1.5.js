function clean_text(id,msg)
{
	if(jQuery('#'+id).val()==msg || jQuery('#'+id).html()==msg)
	{
		jQuery('#'+id).val('');
		jQuery('#'+id).html('');
	}
}

function addArticle()
{
	var item_id = jQuery("#list_article option:selected").val();
	if(item_id=="0") return false;
	var sel_article = jQuery("#article_id").val();
	var list_article='';
	
	  jQuery("#list_article option[value='"+item_id+"']").remove();	  
	  jQuery("#article_"+item_id).removeClass("hide");
	  jQuery("#article_"+item_id).addClass("show");
	  if(sel_article!=="")
	  {
		  list_article = sel_article.split(',');
		  list_article.push(item_id);
		  sel_article = list_article.join(",");		  
	  }
	  else
	  {	
		  sel_article = item_id;
	  }
	  jQuery("#article_id").val(sel_article);
}

function sendmail(id)
{	
	jQuery('#btnSend').click(function(e) { e.preventDefault(); }); 
	var subject = jQuery("#subject").html();	
	 jQuery.ajax({
		  type: "POST",
		  url: host+"/admin/email-digest/articles.htm",
		  data: "id="+id+"&subject="+subject+"&action=sendMail",
		  error:function(){alert('error');},
		  success: function(msg){ 
			  var data = eval('('+msg+')'); 
			  if(data>0)
			  {
				  document.location.href=host+"/admin/email-digest/daily_digest.php";
			  }
						    
		  }
	  }); 
}

function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
	var result = pattern.test(emailAddress);
	return result;
}

function forwardmail(id,subject)
{	
	var name = jQuery("#username").val();	
	var  to = jQuery("#f_email").val();	
	var from = jQuery("#useremail").val();
	var comment = jQuery("#f_comment").val();
	if(name=="" || name=="Enter your name")
	{	
		alert('Please enter your name.');
		return false;
		
	}
	if(!isValidEmailAddress(from))
	{
		alert('Please enter a valid email address.');
		return false;		
	}
	if(!isValidEmailAddress(to))
	{
		alert('Please enter a valid email address.');
		return false;		
	}
	if(comment=="Enter your comment")
	{
		comment="";
	}
	jQuery("#unsubscribeErrorMsgs").html('Sending .....');
	
	 jQuery.ajax({
		  type: "POST",
		  url: host+"/subscription/register/forward_mail.php",
		   data: "action=forwardmail&to="+to+"&from="+from+"&name="+name+"&id="+id+"&subject="+subject+"&comment="+escape(comment),
		  error:function(){alert('error');},
		  success: function(msg){ 
			  var data = eval('('+msg+')'); 
			  if(data.result=="1")
			  {
				  jQuery("#unsubscribeErrorMsgs").html('');
				  jQuery(".unsubscribe_content").html('Your mail has been successfully sent.');
				  jQuery(".unsubscribe_html_display").html('');
			  }
		  }
	  });  
}


function removeArticle(id,title)
{
	 jQuery("#article_"+id).removeClass("show");
	  jQuery("#article_"+id).addClass("hide");
	var sel_article = jQuery("#article_id").val();
	list_article = sel_article.split(',');
	list_article = jQuery.grep(list_article, function(value) {
        return value != id;
      });
	jQuery("#article_id").val(list_article);
	jQuery("#list_article").append('<option value="'+id+'">'+stripslashes(title)+'</option>');	
}
function popitup(url) {
	newwindow=window.open(url,'name','height=600,width=660');
	if (window.focus) {newwindow.focus()}
	return false;
}
function getText(id)
{
	var text = jQuery('#'+id).html();
	if(id=="subject" && text=="Click here to enter heading")
	{	
		return "";
	}
	else
	{
		return text;
	}
}
function addHtml(text,id,edit_id,text_id)
{
	jQuery('#'+id).html(text);
	if(text_id!=""){
		jQuery('#'+text_id).val(text);
	}
	if(edit_id!="")
	{
		jQuery('#'+edit_id).html("Edit");
		jQuery('#'+edit_id).bind('click', function() {
			return popitup(host+"/admin/email-digest/editor.htm?id="+id+"&edit_id="+edit_id);
			});
	}
	jQuery('#'+id).attr('onclick', 'return false;');
}
function prevTemplate()
{	
	var id = jQuery("#dd_id").val();
	if(id!="")
	{
		var subject = jQuery("#subject").html();
		if(subject=="" || subject=="Click here to enter heading") 
		{
			alert("Please enter the subject.");
			return false;
		}	
		var module1_detail = jQuery("#module1_detail").html();
		if(module1_detail=="") 
		{
			alert("Please enter first article's description.");
			return false;
		}
		var module2_detail = jQuery("#module2_detail").html();
		if(module2_detail=="") 
		{
			alert("Please enter second article's description.");
			return false;
		}
		var sel_article = jQuery("#article_id").val();
		if(sel_article=="") 
		{
			alert("Please select today's articles.");
			return false;
		}
		var id = jQuery("#dd_id").val();
		document.location.href=host+'/admin/email-digest/send_digest.php?id='+id;	 
	}
	else
	{
		alert("Please save the daily digest first.");
		return false;
	}
}
function saveTemplate()
{	
	var subject = jQuery("#subject").html();
	if(subject=="" || subject=="Click here to enter heading") 
	{
		alert("Please enter the subject.");
		return false;
	}	
	var module1_detail = jQuery("#module1_detail").html();
	
	if(module1_detail=="") 
	{
		alert("Please enter first article's description.");
		return false;
	}
	var module2_detail = jQuery("#module2_detail").html();
	if(module2_detail=="") 
	{
		alert("Please enter second article's description.");
		return false;
	}
	var sel_article = jQuery("#article_id").val();
	if(sel_article=="") 
	{
		alert("Please select today's articles.");
		return false;
	}
	var id = jQuery("#dd_id").val();

	jQuery.ajax({
		  type: "POST",
		  url: "articles.htm",
		  data: 'action=setTemplate&id='+id+'&sel_article='+sel_article+'&subject='+escape(subject)+'&module1_detail='+escape(module1_detail)+'&module2_detail='+escape(module2_detail),
		  error:function(){alert('error');},
		  success: function(msg){
			  var data = eval('('+msg+')'); 
			  jQuery('#prev').attr("disabled", false);	
			  jQuery('#dd_id').val(data.id);
			  alert('Your daily digest has been saved.');
		  }
	  }); 
	 
	
}

function htmlspecialchars_decode (string, quote_style) {
    var optTemp = 0,
        i = 0,
        noquotes = false;
    if (typeof quote_style === 'undefined') {
        quote_style = 2;
    }
    string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE': 1,
        'ENT_HTML_QUOTE_DOUBLE': 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i = 0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
        // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
    }
    if (!noquotes) {
        string = string.replace(/&quot;/g, '"');
    }
    // Put this in last place to avoid escape being double-decoded
    string = string.replace(/&amp;/g, '&');
 
    return string;
}

function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\&/g, '\\&');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}

 	
function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\&/g, '&');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    str = str.replace(/\\\\/g, '\\');
    return str;
}