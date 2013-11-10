function saveEliottAlerts(submitType){
	
	alert_title = jQuery('#alertTitle').val();
	text_box= jQuery('#alertbody').val();
	var section = jQuery('#alertSection').val();
	var category = jQuery('#alertCategory').val();
	var contributor = jQuery('#alertContributor').val();

	
	if (alert_title =="" || text_box=="" ){
		alert("Title & entry cannot be blank");
		return false;
	}
	if (category =="" || category ==null ){
		alert("Category cannot be blank");
		return false;
	}
	if (contributor =="" || contributor ==null ){
		alert("Analyst cannot be blank");
		return false;
	}
	if (section =="" || section ==null ){
		alert("Section cannot be blank");
		return false;
	}
	if(submitType=="delete")
	{
		if(confirm("Are you sure you want to delete this article?")){
			jQuery('#submit_type').val(submitType);
			jQuery('#frmElliottWaveAlert').submit();
		}
	}
	else
	{
		jQuery('#submit_type').val(submitType);
		jQuery('#frmElliottWaveAlert').submit();
	}
}

function returnAlertId(divId,filepath){
	var alertId = jQuery('#'+divId).val();
	window.location= filepath+'?id='+alertId;
}

function showTop(divid,divgoto)
{
	var height = jQuery(divid).height();
	if(height<'720')
	{
		jQuery(divgoto).css('display','none');
	}
}

function backToTop(focusId){
	var $target = jQuery('#'+focusId);
    $target = $target.length && $target;
    var targetOffset = ($target.offset().top - 20);
    jQuery('html,body').animate({scrollTop: targetOffset}, 600);
}
