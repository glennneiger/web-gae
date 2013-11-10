function saveKeeneAlerts(submitType){
    
	var alert_title = jQuery('#alertTitle').val();
	var text_box= jQuery('#alertbody').val();
        var category = jQuery('#alertCategory').val();
        
	if (alert_title =="" || alert_title ==null){
		alert("Title cannot be blank");
		return false;
	}
       
	if (category =="" || category ==null ){
		alert("Category cannot be blank");
		return false;
	}
        else{
		if(submitType=="delete")
		{
			if(confirm("Are you sure you want to delete this article?")){
				jQuery('#submit_type').val(submitType);
				jQuery('#frmKeeneAlert').submit();
			}
		}
		else
		{
			jQuery('#submit_type').val(submitType);
			jQuery('#frmKeeneAlert').submit();
		}
	}
}

function backToTop(focusId){
	var $target = jQuery('#'+focusId);
    $target = $target.length && $target;
    var targetOffset = ($target.offset().top - 20);
    jQuery('html,body').animate({scrollTop: targetOffset}, 600);
}

function showTop()
{
	var height = jQuery('.ptc_alertDetailBox').height();
	if(height<'767')
	{		
		jQuery('#ptc_goTop').css('display','none');
	}
}

function returnAlertId(divId,filepath){
	var alertId = jQuery('#'+divId).val();
	window.location= filepath+'?id='+alertId;
}
