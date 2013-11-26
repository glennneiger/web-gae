function previewDFimage(img_path)
{
	if(img_path == "")
	{
		$('#image_preview').hide();
	}
	else
	{
		$('#image_preview').show();
		$('#image_preview').attr("src", img_path);
	}
}
function updateCharts(){
		count=1;
		$('#showimageuploaded :checked').each(function() {
			var chart="#chart" + count;
		   $(chart).val($(this).val());
		   count=count+1;
		 });
}

function uploadDFFile(fileInput,postUrl){

	jQuery.ajax({
                type: "POST",
                url: host+"/admin/lib/uploadFileBucket.php",
                data:"text=getUploadUrl&posturl="+postUrl,
                success: function (req){
                             var post = eval('('+req+')');
                             if(post.status=="1" )
                             {
				var fd = new FormData();
				fd.append( "fileInput", jQuery("#"+fileInput)[0].files[0]);
				fd.append( "text", "feedUpload");
				jQuery.ajax({
				    url: post.url,
				    type: 'POST',
				    cache: false,
				    data: fd,
				    processData: false,
				    contentType: false,
				    beforeSend: function () {
					jQuery("#output").html("Uploading, please wait....");
				    },
				    success: function (req) { 
					var post = eval('('+req+')');
					jQuery("#output").html(post.msg);
					jQuery("#chkImage").val(post.file);
					jQuery("#upImage").val('1');
					jQuery("#"+fileInput).val('');
					jQuery("#image_directory_name").val(post.image_directory_name);
				    },
				    error: function () {
					alert("ERROR in upload");
				    }
				});
                             }
			     else{
				alert("Upload URL could not be created");
			    }
                }
                }); 
   }

function removeImage(itemtype,imageid){
        $.ajax({
		   type: "POST",
		   url: host + "/admin/lib/controller/_dailyfeed.php",
		   data: "itemtype="+itemtype+"&imageid="+imageid,
		   success: function(msg){
		     $('#dfuploaddedimage').hide();
		   }
 		});
}

function Save(inputval){
    updateCharts();
	var err="";
	if(getValByName("feed[title]").length==0){
		err+="Please enter the feed title.\n";
	}
	$('#inputvalue').val(inputval);
	var regUrl = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

	if(getValByName("feedsource").length >0 && getValByName("feedsource_link").length==0){
			err+="Please enter the feed resource URL.\n";
	}
	if(getValByName("feedsource_link").length!=0){
			var fsourceurl_value = getValByName("feedsource_link");
			if(regUrl.test(fsourceurl_value) == false) {
					err+="Please enter valid feed resource URL.\n";
			}
	}

	if(document.getElementById("feed[is_yahoofeed]").checked && $('textarea#feedticker').val() == "")
	{
		err+="Please enter the feed Tickers for Yahoo Syndication \n";
	}

	var article_tickers = trim(jQuery('textarea#feedticker').val());	
	if(article_tickers)
	{
		arTickers = article_tickers.split(",");		
		if(arTickers.length > 5)
		{
			err+="You are allowed to enter only five tickers \n";
		}
	}	

   var selObj = document.getElementById("author-drop-down");
   val = selObj.options[selObj.selectedIndex].value;
   if(val.length==0){
			err+="Please select Author for the feed.\n";
	}

	if(err.length){
		alert(err);
		return false;
	}

	document.feedForm.submit();
}


function deteFeed(inputval){
	$('#inputvalue').val(inputval);
    document.feedForm.submit();
}

function selectAuthor(selObj){
	selObj=selObj[selObj.selectedIndex];
	var txtname=selObj.text.split('/')[0];
	if(txtname.substring(0,1)=="-")txtname="";
	findObj("chat_image").src=selObj.value;
}
//Validation Function for Tag-Keyword field
function validate_Tag(){
	var strvalue = findObj("pagetag[topic]").value;
	if (strvalue){
		var tagArray = new Array();
		tagArray=strvalue.split(",");
		var strlength = tagArray.length;
		for (var i = 0; i < strlength; i++) {
			var elementlen = tagArray[i].length;
			if (elementlen > 200) {
				alert ("Tag cannot be greater than 200 characters.");
				return false;
			}
		}
	}
}//End of Function

function setImage(){

	var image_upload = false;
	if( $('#upImage').val()=="1") //if file is uploaded and check box is selected
	{
		image_upload = true;
	}
	if(image_upload == false && $('#dailyfeedimage').val()=="")
	{
		alert('Please select an image to upload');
		return false;
	}
	else if(image_upload == true && $('#imagenamedf').val()=="")
	{
		alert('Please enter a descriptive name for image');
		return false;
	}
	else
	{
		if(image_upload)
		{
			var img_name = $('#chkImage').val();
			var img_src = '/assets/dailyfeed/uploadimage/'+$('#image_directory_name').val()+'/'+img_name;
		}
		else
		{
			var img_name = $("#dailyfeedimage option:selected").text();
			var img_src =$('#image_preview').attr("src");
		}

		$('#imageuploaded').html('Image uploaded successfully');
		$('#dfuploaddedimage').hide();
		image_link = '<input type="checkbox" checked="checked" value="'+img_name+'" name="chkImageupload" id="chkImageupload"/>'+ img_name+'&nbsp;&nbsp;<img src="'+image_server+img_src+'"width="80" height="80" >';
		$('#showimageuploaded').html(image_link);
		$('#mask').hide();
	   	$('.window').hide();
	}
}

function cancelImage(){
		 $('#mask').hide();
		 $('.window').hide();
}

function dailyFeedImageselect(){
	if($(dailyfeedimage).val().length>0){
		$('input[name=chkImage]').attr('checked', false);
		$(imagenamedf).val('');
		$('#upImage').val('0');
	}
}
function HostYahoo()
{
        if(document.getElementById("feed[is_yahoofeed]").checked)
        {
			document.getElementById("yahooFeed").style.display = "";

        }
        else
        {
           document.getElementById("yahooFeed").style.display = "none";
        }
}

jQuery(document).ready(function() {

	//select all the a tag with name equal to modal
	jQuery('a[name=modal]').click(function(e) {
		//Cancel the link behavior
		e.preventDefault();

		//Get the A tag
		var id = jQuery(this).attr('href');

		//Get the screen height and width
		var maskHeight = jQuery(document).height();
		var maskWidth = jQuery(window).width();
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});

		//transition effect
		jQuery('#mask').fadeIn(1000);
		jQuery('#mask').fadeTo("slow",0.8);

		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
		//Set the popup window to center
		jQuery(id).css('top',230);
		jQuery(id).css('left',300);
		jQuery(id).css('width',545);
		jQuery(id).fadeIn(2000);

	});

	jQuery('.window .close').click(function (e) {
		e.preventDefault();
	});

	jQuery('#mask').click(function () {
		jQuery(this).hide();
		jQuery('.window').hide();
	});

});

jQuery(document).ready(function(){
		jQuery('#feed_title').jqEasyCounter({
			'maxChars': 150,
			'maxCharsWarning': 100,
			'msgFontSize': '11px',
			'msgFontColor': '#000',
			'msgFontFamily': 'Arial',
			'msgTextAlign': 'left',
			'msgWarningColor': '#F00'
			//'msgAppendMethod': 'insertBefore'
		});
		jQuery('#quicktitle').jqEasyCounter({
			'maxChars': 38,
			'maxCharsWarning': 30,
			'msgFontSize': '11px',
			'msgFontColor': '#000',
			'msgFontFamily': 'Arial',
			'msgTextAlign': 'left',
			'msgWarningColor': '#F00'
		});
		jQuery('#feed_excerpt').jqEasyCounter({
			'maxChars': 130,
			'maxCharsWarning': 110,
			'msgFontSize': '11px',
			'msgFontColor': '#000',
			'msgFontFamily': 'Arial',
			'msgTextAlign': 'left',
			'msgWarningColor': '#F00'
		});
		/*jQuery("#feedtopic").autocomplete(host+"/admin/lib/_dailyfeed_suggestion.php?type=topic", {
		width:225,
		selectFirst: true
		});
		jQuery("#feedticker").autocomplete(host+"/admin/lib/_dailyfeed_suggestion.php?type=ticker", {
			width: 225,
			selectFirst: true
		});*/
});


function callTags(val)
{
	var img_server = "http://image.minyanville.com";
	if(!val)
	{
		alert("Please enter some data in 'Full Entry' box.");
		return false;
	}
	else
	{
		jQuery("#pop_tags_msg").html('<font color="red">Tags are being populated. Please wait...</font>');
		jQuery.ajax({
		   type: "POST",
		   url: "keywords.php",
		   data: "str="+escape(val),
		   success: function(msg){
		   	jQuery("#pop_tags_msg").html('(Please click on the right side button to populate Tags)&nbsp;<img style="cursor: pointer; vertical-align: middle;" src="'+img_server+'/images/DailyFeed/bttn_populate.png"  alt="Populate Tags" title="Populate Tags" id="pop_tags_img" onclick="callTags(editor.getData());" >');

			var checkData=jQuery('textarea#feedtopic').val();

			if(checkData!='')
			{
				var combinedData=checkData+','+msg;
				Arr=combinedData.split(",");
				var unique=Arr.filter(function(itm,i,Arr){
				    return i==Arr.indexOf(itm);
				});
			}
			else
			{
				var unique=msg;
			}						
			jQuery("#feedtopic").val(unique);
			
		   }
		});
	}
}

function callTickers(val)
{
	var img_server = "http://image.minyanville.com";
	
	if(!val)
	{
		alert("Please enter some data in 'Full Entry' box.");
		return false;
	}
	else
	{
		jQuery("#pop_tickers_msg").html('<font color="red">Tickers are being populated. Please wait...</font>');
		jQuery.ajax({
		   type: "POST",
		   url: "populate-tickers.php",
		   data: "str="+escape(val),
		   success: function(msg){
		     jQuery("#pop_tickers_msg").html('(Please click on the right side button to populate Tickers)&nbsp;<img style="cursor: pointer; vertical-align: middle;" src="'+img_server+'/images/DailyFeed/bttn_populate.png"  alt="Populate Tickers" title="Populate Tickers" id="pop_tickers_img" onclick="callTickers(editor.getData());" >');

				var checkData=jQuery('textarea#feedticker').val();

				if(checkData!='')
				{
					var combinedData=checkData+','+msg;
					Arr=combinedData.split(",");
					var unique=Arr.filter(function(itm,i,Arr){
					    return i==Arr.indexOf(itm);
					});

				}
				else
				{
					var unique=msg;
				}
				jQuery("#feedticker").val(unique);
		   }
		});
	}
}

function SaveDataToDraft()
{
	jQuery("#savedraft_button").css('opacity','0.4');
	jQuery("#editorDatafld").val(editor.getData());
	var form_data = jQuery("#feedForm").serialize();
	jQuery.ajax({
		   type: "POST",
		   url: "feed-draft.php",
		   data: form_data,
		   success: function(msg){
		   jQuery("#savedraft_button").css('opacity','1');
		   }

		});
}