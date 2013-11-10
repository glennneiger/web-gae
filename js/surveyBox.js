function closeFancyBox(){
	jQuery("#fancybox-overlay").css("display", "none");
	jQuery("#fancybox-wrap").css("display", "none");
}

function surveyBox(){
	jQuery("#surveyBoxPopUp").fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
	jQuery(document).ready(function() {
		jQuery("#surveyBoxPopUp").trigger('click');
		//jQuery("a#fancybox-close").css("display","none");
		//jQuery("#fancybox-overlay").css('background-color','#000');
	});
}