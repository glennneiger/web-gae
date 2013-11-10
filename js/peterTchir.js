function savePeterAlerts(submitType){
	
	alert_title = jQuery('#alertTitle').val();
	text_box= jQuery('#alertbody').val();
	
	if (alert_title =="" || text_box=="" ){
		alert("Title & entry cannot be blank");
	}else{
		if(submitType=="delete")
		{
			if(confirm("Are you sure you want to delete this article?")){
				jQuery('#submit_type').val(submitType);
				jQuery('#frmPeterTchirAlert').submit();
			}
		}
		else
		{
			jQuery('#submit_type').val(submitType);
			jQuery('#frmPeterTchirAlert').submit();
		}
	}
}

function returnAlertId(divId,filepath){
	var alertId = jQuery('#'+divId).val();
	window.location= filepath+'?id='+alertId;
}

function showTop()
{
	var height = jQuery('.ptc_alertDetailBox').height();
	if(height<'767')
	{		
		jQuery('#ptc_goTop').css('display','none');
	}
}

function saveHeatMap(frm){
	var heatMapTitle = jQuery("#heatMapTitle").value;
	var heatMapImg = jQuery("#heatMapImg").val();
	var heatmapUploadedImg = jQuery("#heatmapUploadedImg").attr('src');

	var error="";
	
	if(heatMapTitle==''){
		alert('Enter Title of the HeapMap.');
		heatMapTitle.focus();
		var error = "1";
	}
	if(heatMapImg=='' && heatmapUploadedImg==""){
		alert('Please upload the image');
		heatMapImg.focus();
		var error = "1";
	}
	if(error!="1")
	{
		frm.submit();
	}
}

function backToTop(focusId){
	var $target = jQuery('#'+focusId);
    $target = $target.length && $target;
    var targetOffset = ($target.offset().top - 20);
    jQuery('html,body').animate({scrollTop: targetOffset}, 600);
}

function showHeatMap(){
	jQuery.fancybox({
        type: 'inline',
        content: '#inline1',
        overlayOpacity : 0.8,
		overlayColor : '#000'
    });
}

