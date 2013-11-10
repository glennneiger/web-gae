function selectAuthor(selObj){
	selObj=selObj[selObj.selectedIndex];
	var txtname=selObj.text.split('/')[0];	
	if(txtname.substring(0,1)=="-")txtname="";
	$('#chat_image').attr('src',selObj.value);
}

function updateCharts(){
	count=1;
	$('#uploaded_image :checked').each(function() {
		var chart="#chart" + count;
	   $(chart).val($(this).val());
	   count=count+1;
	 });
}

function uploadBuzzFile(fileInput,postUrl){
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
		   fd.append( "text", "buzzUpload");
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



function validate_Tag(){
	updateCharts();

	var err="";

	if(!trim(getValByName("banter[title]"))){
		err+="Please give this buzz a title.\n";
	}
	if((findObj("banter[contrib_id]").selectedIndex) <=0){
		err+="Please associate this buzz with a Chat Name.\n";
	}
	
	if(document.getElementById("is_yahoonews").checked && findObj("buzzticker").value.length==0)
	{
		err+="Please enter the Tickers for Yahoo Syndication \n";
	}
	if(err.length){
		alert(err);
		return false;
	}
	var oEditor = FCKeditorAPI.GetInstance("banter[body]");
	 var fckText = oEditor.GetHTML(true);
	 if(fckText=='<br>'){
		err+="Please enter text in Banter Text for the buzz.\n";
	 }
	if(err.length){
		alert(err);
		return false;
	}
	var strvalue = findObj("pagetag[tag]").value;
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
	document.buzzbanter.submit();
}//End of Function

function removeChart(buzzId,chartId){
        $.ajax({
		   type: "POST",
		   url: host + "/admin/lib/controller/_buzz.php",
		   data: "buzzId="+buzzId+"&chartId="+chartId,
		   success: function(msg){
		     $('#buzzchart'+chartId).hide();
		   }
 		});
}
