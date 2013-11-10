function addScrollContent()
{
	 var modules=["448","451","449","450"];
     modules.forEach(function(moduleId) {
     jQuery('ul#list'+moduleId).endlessScroll({
     fireOnce: false,
     insertAfter: "ul#list"+moduleId+" li:last",
     data: function(i) {
    	var len = jQuery(".scrollCon"+moduleId).length;
        var url = host+'/lib/edu/eduModuleBuzzMod.php';
            var pars="action=scrollModule&module_id="+moduleId+"&len="+len;
            var scrollContent;
            jQuery.ajax({
               type: "POST",
               url: url,
               async: false,
               data:pars,
               success: function getModuleContent(req){
                    var post = eval('('+req+')');
                    if(post.status==true )
                    {
                            scrollContent =post.html;
                    }
                    else
                    {
                    		scrollContent ='';
                    }
               }
             });
		return scrollContent;
     }
   });
    });
}
        
function uploadEduImage(fileInput,postUrl){

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
					fd.append( "text", "eduUploadImage");
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
						jQuery("#imgFile").val(post.file);
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

function uploadEduFile(fileInput,postUrl){

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
					fd.append( "text", "eduUpload");
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
						jQuery("#audioFile").val(post.file);
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


function saveEduAlerts(submitType){
	var alert_title = jQuery('#alertTitle').val();
	var text_box= jQuery('#alertbody').val();
	var category = jQuery('#alertCategory').val();
	var contributor = jQuery('#alertContributor').val();

	
	if (alert_title =="" || text_box=="" ){
		alert("Title & entry cannot be blank!!");
		return false;
	}
	if (category =="" || category ==null ){
		alert("Category cannot be blank!!");
		return false;
	}
	if (contributor =="" || contributor ==null ){
		alert("Please select Author for this article!!");
		return false;
	}
	if(jQuery('#chkMp3').is(':checked')==true){
		checkmp3value=$('#chkMp3').val();
	}else{
		checkmp3value="0";
	}
	if(checkmp3value=="0"){
		$("#audioFile").val('');		
	}
	
	if(submitType=="delete")
	{
		if(confirm("Are you sure you want to delete this article?")){
			jQuery('#submit_type').val(submitType);
			jQuery('#frmEduAlert').submit();
		}
	}
	else
	{
		jQuery('#submit_type').val(submitType);
		jQuery('#frmEduAlert').submit();
	}
}

function returnAlertId(divId,filepath){
	var alertId = jQuery('#'+divId).val();
	window.location= filepath+'?id='+alertId;
}

function saveEduGlossary(actionType){
	var glossaryName = jQuery('#glossaryName').val();
	var gloassaryDef = jQuery('#gloassaryDef').val();

	
	if (glossaryName =="" || glossaryName=="" ){
		alert("Term cannot be blank!!");
		return false;
	}
	/*if (gloassaryDef =="" ){
		alert("Defination cannot be blank!!");
		return false;
	}*/
	if(actionType=="delete")
	{
		if(confirm("Are you sure you want to delete this term?")){
			jQuery('#actionType').val(actionType);
			jQuery('#frmEduGlossay').submit();
		}
	}
	else
	{
		jQuery('#actionType').val(actionType);
		jQuery('#frmEduGlossay').submit();
	}
}

function searchEduGlossary(){
	var searchText = jQuery('#eduGlossSearchText').val();
	jQuery.ajax({
		type : "POST",
		url : host+"/edu/searchGlossary.php",
		data : "type=glossary"+"&searchText="+searchText,
		beforeSend : function(){
			jQuery('div#eduGlossBody div.eduGlossSearchProg').show();
			jQuery('div#eduGlossBody div.eduGlossSearchProg').html('<span>Searching...</span>');
		},
		error : function(){},
		success : function(res){
			if(res){
				jQuery('div#eduGlossBody').html(res);
			}else{
				jQuery('div#eduGlossBody div.eduGlossSearchProg').html('<div id="crossbttn" onclick="closeOverlayDiv()">X</div><span>No Result Found</span>');
			}
		}
	});
}

function closeOverlayDiv(){
	jQuery('div.eduGlossSearchProg').hide();
}

function showActiveAlpha(id){
	jQuery('*').removeClass('eduGlossAlphabetsIconActive');
	jQuery('div#'+id).addClass('eduGlossAlphabetsIconActive');
}

function displayLeavingWindow(productUrl){
	jQuery.fancybox.init();
	//jQuery.noConflict();
	jQuery.fancybox({
			showCloseButton : false,
	        type: 'inline',
	        content: '#eduLeavingWindow',
	        overlayOpacity : 0.8,
			overlayColor : '#000',
			onClosed : function(){
				window.location.href = productUrl;
			}
	    });
	jQuery('#fancybox-wrap').css('top','100px');
	setTimeout("jQuery('#fancybox-overlay, #fancybox-wrap').fadeIn('slow').delay(15000).hide(1)",5000);
	
}

function redirectToProduct(){
	window.location.href = "http://www.tradingacademy.net/";
}

function saveEduProduct(actionType){
	var eduProductTitle = jQuery('#productTitle').val();
	var eduProductPrice = jQuery('#productPrice').val();
	var eduProductImg = jQuery('#eduproductImg').val();
	var eduProductUrl = jQuery('#productUrl').val();
	
	if (eduProductTitle =="" || eduProductTitle=="" ){
		alert("Title cannot be blank!!");
		return false;
	}
	
	if (eduProductUrl =="" || eduProductUrl=="" ){
		alert("Product URL cannot be blank!!");
		return false;
	}
	
	if (eduProductPrice =="" || eduProductPrice=="" ){
		alert("Price cannot be blank!!");
		return false;
	}
	
	if(actionType=="delete")
	{
		if(confirm("Are you sure you want to delete this product?")){
			jQuery('#actionType').val(actionType);
			jQuery('#frmEduProducts').submit();
		}
	}
	else
	{
		jQuery('#actionType').val(actionType);
		jQuery('#frmEduProducts').submit();
	}
}