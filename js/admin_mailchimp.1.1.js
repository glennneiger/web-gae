
function saveFancyBoxImage(){
var list_id=jQuery("#list_id").val();
var filename= jQuery("input:file").val();
var imageName=jQuery('#uploadedimage').attr('src');
	if(list_id=="-Select Mailchimp List-"){
		jQuery("#savemsg").html('Please select mailchimp list name');
	}
	else {
		jQuery("#listname").val(jQuery("#list_id :selected").text());
		jQuery("#listid").val(list_id);
		jQuery("savemsg").val('');
		frmlogo.submit();
	}
}
function getListData(req){
	if(req.responseText){
		$("savemsg").innerHTML=req.responseText;
	}
}
function cancelFancyBoxImage(){
	//$("logoname").value='';
	//$("logourl").value='';
}
