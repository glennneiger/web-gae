function actionname(frm){
	$("#action").val("pagechange");
	frm.submit();
}

function deleteIt(){
	if(confirm("Are you sure you want to delete this article?")){
		var frm=document["articleForm"];
		frm["deletearticle"].value=1;
		frm.submit();
	}
}

function Save(type){
	
	var err="";
	var frm=document["articleForm"];

	if(!trim(getValByName("articles[title]"))){
		err+="Please give this article a title.\n";
	}

	if((findObj("articles[navigation_section_id][]").selectedIndex<0)){
		err+="Please associate this article with at least one navigation section.\n";
	}
	if((findObj("articles[contrib_id]").selectedIndex) <=0){
		err+="Please associate this article with an Author.\n";
	}
	
	if($('#chkMp3').is(':checked')==true){
		checkmp3value=$('#chkMp3').val();
	}
	else
	{
		checkmp3value="0";
	}
		
	jQuery('#articles\\[savetype\\]').val(type);
	
	var article_tickers = trim(jQuery('#articles\\[tag\\]').val());	
	if(article_tickers)
	{
		arTickers = article_tickers.split(",");		
		if(arTickers.length > 5)
		{
			err+="You are allowed to enter only five tickers";
		}
	}	
	
	if((findObj("layout-type").value == 'thestreet' || findObj("layout-type").value == 'realmoneysilver' || findObj("layout-type").value == 'observer') && 
	(findObj("articles[is_yahoofeed]").checked == true))
	{		
		err+="Partner content cannot be resyndicated. Please unselect any syndication partners before saving.\n";
	}
	if(err.length){
		alert(err);
		return;
	}
	var strvalue = findObj("articles[tag]").value;
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
	$("#action").val("save");
	if(checkmp3value=="0")
	{
		$("#audioFile").val('');		
	}
	findObj("articleForm").submit();
}

function checkYahoo()
{
	if(document.getElementById("articles[is_yahoofeed]").checked==true){
		document.getElementById("articles[yahoo_full_body]").checked = false;
		alert('Please uncheck "Send to Yahoo" checkbox. ');		
	}
}

function validatetag(field) {
}

 function setFormSubmitMarker(frm,value)
{
    frm.submitname.value=value;
}

function setAutorName(frm,frmobj)
{
    if (frmobj.selectedIndex>0)
    {
        frm["articles[contributor]"].value=frmobj[frmobj.selectedIndex].text;
    }
    else
    {
        frm["articles[contributor]"].value="";
    }
}

function displayHost()
{
	if(document.getElementById("articles[is_msnfeed]").checked)
	{
		document.getElementById("tdFeed").style.display="";
	}
	else
	{
		document.getElementById("tdFeed").style.display="none";
	}
}

function displayHostYahoo()
{
	if(document.getElementById("articles[yahoo_full_body]").checked==false)
	{
		if(document.getElementById("articles[is_yahoofeed]").checked)
		{
			document.getElementById("yahooFeed").style.display="";
		}
		else
		{
			document.getElementById("yahooFeed").style.display="none";
		}
	}
	else
	{
		document.getElementById("articles[is_yahoofeed]").checked = false;
		alert('Please uncheck "Send to Yahoo(Full Body)" checkbox. ');
	}
}

function displayStreetHost()
{
	if(document.getElementById("articles[is_streetfeed]").checked)
	{
		document.getElementById("theStreetFeed").style.display="";
	}
	else
	{
		document.getElementById("theStreetFeed").style.display="none";
	}
}
function toggleLayoutEditor()
{
	if((jQuery("#layout-type").val()=="no-photo") || (jQuery("#layout-type").val()=="live-blog")|| (jQuery("#layout-type").val()=="thestreet") || (jQuery("#layout-type").val()=="realmoneysilver")  || (jQuery("#layout-type").val()=="observer") || (jQuery("#layout-type").val()=="no-related"))
	{	
		jQuery("#layouteditor").hide();
	}
	else
	{	
		jQuery("#layouteditor").show(); 
	}
}

function getblogLive()
{
	if((jQuery("#layout-type").val()=="live-blog"))
	{	
		jQuery("#layoutcoverlive").show();

	}
	else
	{	jQuery("#layoutcoverlive").hide(); 
	}
}

/**/
function unlock_content(id,donotRedirect)
{
	var url = host + "/admin/ajax_unlock.htm?id="+id;
	var postAjax = jQuery.ajax({url:url,success:function(){
													if(!donotRedirect){
														window.location='approve.htm';
													}
													}});
	window.onbeforeunload = null;
}

jQuery(document).ready(function(){
//   var myTextArea = jQuery('textarea#articles\\[keyword\\]').tagify({addTagPrompt: 'Add Tags',txtBoxID:'feedtagtext',divCssClass:'keyword-container'});
//	var myTextArea1 = jQuery('textarea#articles\\[tag\\]').tagify({addTagPrompt: 'Add Tickers',txtBoxID:'feedtickertext',divCssClass:'ticker-container'});
/*jQuery("textarea#articles\\[keyword\\]").autocomplete(host+"/admin/lib/_dailyfeed_suggestion.php?type=topic", {
		width:225,
		selectFirst: true
	});
	jQuery("textarea#articles\\[tag\\]").autocomplete(host+"/admin/lib/_dailyfeed_suggestion.php?type=ticker", {
		width: 225,
		selectFirst: true
	});*/

	jQuery('#articles\\[seo_title\\]').jqEasyCounter({
		'maxChars': 150,
		'maxCharsWarning': 75,
		'msgFontSize': '11px',
		'msgFontColor': '#000',
		'msgFontFamily': 'Arial',
		'msgTextAlign': 'left',
		'msgWarningColor': '#F00'
		//'msgAppendMethod': 'insertBefore'
	});
	jQuery('#articles\\[title\\]').jqEasyCounter({
		'maxChars': 150,
		'maxCharsWarning': 75,
		'msgFontSize': '11px',
		'msgFontColor': '#000',
		'msgFontFamily': 'Arial',
		'msgTextAlign': 'left',
		'msgWarningColor': '#F00'
	});
	jQuery('#articles\\[character_text\\]').jqEasyCounter({
		'maxChars': 255,
		'maxCharsWarning': 255,
		'msgFontSize': '11px',
		'msgFontColor': '#000',
		'msgFontFamily': 'Arial',
		'msgTextAlign': 'left',
		'msgWarningColor': '#F00'
	});
});


function callTags(val)
{
	var img_server = "http://storage.googleapis.com/mvassets";
	
	if(!val)
	{
		alert("Please enter some data in 'Entry' box.");
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
			var checkData=jQuery('textarea#articles\\[keyword\\]').val();

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
			 	jQuery("#articles\\[keyword\\]").val(unique);
		   }
		});
	}
}

function callTickers(val)
{
	var img_server = "http://storage.googleapis.com/mvassets";
	if(!val)
	{
		alert("Please enter some data in 'Entry' box.");
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
				var checkData=jQuery('textarea#articles\\[tag\\]').val();				
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
			 	jQuery("#articles\\[tag\\]").val(unique);		
		   }

		});
	}
}
function load_contib_data(contrib_id)
{	
	if(parseInt(contrib_id) >0)
	{
		var pars ='cont_id='+contrib_id;	
		jQuery.ajax({
				   type: "POST",
				   url: "ajax_contributor.php",
				   data: pars,
				   dataType: "json",
				   beforeSend: function()
								{
									jQuery("#load_contributor").html("Loading ...");
								},
				   success :function(data){ 				
						var arData = eval(data);
						if(arData['editor_note'] != "")
						{										
							CKEDITOR.instances.article_body.insertHtml(arData['editor_note']);
						}	
						jQuery("#load_contributor").html("");			
				   }
				 });
	}	
}

function keepContentActive(ArtID)
{
	jQuery.ajax({
		   type: "POST",
		   url: host+"/admin/lock-unlock.php",
		   data: "aid="+ArtID,
		   success: function(msg){

		   }
		});
	setTimeout("keepContentActive(ArtID)",300000);
}
function SaveDataToDraft()
{
	jQuery("#save").css('opacity','0.4');
	jQuery("#editorDatafld").val(editor.getData());

	var form_data = jQuery("#articleForm").serialize();
	
	jQuery.ajax({
		   type: "POST",
		   url: "article-draft.php",
		   data: form_data,
		   success: function(msg){
		   jQuery("#save").css('opacity','1');
		   }

		});
}

if(ArtID)
	setTimeout("keepContentActive(ArtID)",300000);