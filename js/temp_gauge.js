function changeLevel(level)
{
	var url='';
	var position='';

	if(level=="a")
	{
		url = "url("+host+"/images/temp_gauge/meter_a.png)";
		position='111 175';		
	}
	else if(level=="b")
	{
		url = "url("+host+"/images/temp_gauge/meter_b.png)";
		position='111 225';		
	}
	else if(level=="c")
	{
		url = "url("+host+"/images/temp_gauge/meter_c.png)";
		position='111 285';		
	}
	else if(level=="d")
	{
		url = "url("+host+"/images/temp_gauge/meter_d.png)";
		position='111 337';		
	}
	$('.left_meter_img').css('background-image', url);	
	$('.left_meter_img').css('background-repeat', 'no-repeat');
	$('.left_meter_img').css('background-position', position);
	
}

function sendmail(id)
{	
	$('#btnSend').click(function(e) { e.preventDefault(); }); 
	var subject = $("#mail_subject").val();	
	 $.ajax({
		  type: "POST",
		  url: host+"/admin/robot.journaltempgauge.php",
		  data: "id="+id+"&subject="+subject,
		  error:function(){alert('error');},
		  success: function(msg){ 
			  document.location.href=host+"/admin/temp-gauge/temp_gauge.php";			    
		  }
	  }); 
}


function popitup(url) {
	newwindow=window.open(url,'name','height=600,width=660');
	if (window.focus) {newwindow.focus()}
	return false;
}

function getText(id)
{
	var text = $('#'+id).html();

	if(text=="Click here to enter text")
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
	$('#'+id).html(text);
	if(text_id!=""){
		$('#'+text_id).val(text);
	}
	if(edit_id!="")
	{
		$('#'+edit_id).html("Edit");
		$('#'+edit_id).bind('click', function() {
			return popitup(host+"/admin/email-digest/editor.htm?id="+id+"&edit_id="+edit_id);
			});
	}
	$('#'+id).attr('onclick', 'return false;');
}
function prevGauge()
{	
	var id = $("#tg_id").val();
	if(id!="")
	{
		var level = $('input:radio[name=level]:checked').val();
		if(level=="")
		{
			alert("Please select a gauge level.");
			return false;		
		}
		
		var subject = $("#subject").val();
		if(subject=="") 
		{
			alert("Please enter the subject.");
			return false;
		}
		
		var outlook_desc = $("#text_below_para").html();
		if(outlook_desc=="") 
		{
			alert("Please enter description.");
			return false;
		}

		var link = $("#tg_link").val();
		if(link=="") 
		{
			alert("Please enter This Week's Lead-Lag Report link .");
			return false;
		}

		document.location.href=host+'/admin/temp-gauge/send_temp_gauge.php?id='+id+'&prev=1';	 
	}
	else
	{
		alert("Please save the Temperature Gauge first.");
		return false;
	}
}
function saveGauge()
{		
	var level = $('input:radio[name=level]:checked').val();

	if(level=="" || level==undefined)
	{ 
		alert("Please select a gauge level.");
		return false;		
	}
	
	var subject = $("#subject").val();
	if(subject=="") 
	{
		alert("Please enter the subject.");
		return false;
	}
	
	var outlook_desc = $("#text_below_para").html();
	if(outlook_desc=="") 
	{
		alert("Please enter description.");
		return false;
	}

	var link = $("#tg_link").val();
	if(link=="") 
	{
		alert("Please enter This Week's Lead-Lag Report link .");
		return false;
	}

	
	var id = $("#tg_id").val();

	$.ajax({
		  type: "POST",
		  url: "gauge.php",
		  data: 'action=setGauge&id='+id+'&desc='+outlook_desc+'&subject='+subject+'&link='+escape(link)+'&level='+level,
		  error:function(){alert('error');},
		  success: function(msg){
			  var data = eval('('+msg+')'); 
			  $('#prev').attr("disabled", false);	
			  $('#tg_id').val(data.id);
			  alert('Your Temperature Gauge has been saved.');
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

