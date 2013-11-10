/*///////////////////////////////////////////////////////////////////////
Ported to jquery from prototype by Joel Lisenby (joel.lisenby@gmail.com)
http://joellisenby.com
original prototype code by Aarron Walter (aarron@buildingfindablewebsites.com)
http://buildingfindablewebsites.com
Distrbuted under Creative Commons license
http://creativecommons.org/licenses/by-sa/3.0/us/
///////////////////////////////////////////////////////////////////////*/
host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;
jQuery(document).ready(function() {
	jQuery('#signup').submit(function() {
		// update user interface
		jQuery('#response').html('Adding email address...');
		
		// Prepare query string and send AJAX request
		jQuery.ajax({
			url: host +'/lib/mailchipapi/store-address.php',
			data: 'ajax=true&email=' + escape(jQuery('#email').val()),
			success: function(msg) {
				jQuery('#response').html(msg);
				if(msg=="Success! Check your email to confirm sign up."){
					closeFancyBox();	
				}
			}
			
		});
	
		return false;
	});
});
     function closeFancyBox(){
	jQuery("#fancybox-overlay").css("display", "none");
	jQuery("#fancybox-wrap").css("display", "none");
	
     }
	function mailChimpFancyBox(){
		
		jQuery("#mailcimpfancypopup").fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
		});
		
		jQuery(document).ready(function() {
		  jQuery("#mailcimpfancypopup").trigger('click');
		});
	}
	
	function thankYouFancyBox(){
		jQuery.fancybox({
			showCloseButton : false,
	        type: 'inline',
	        content: '#inlineThanks',
	        overlayOpacity : 0.7,
			overlayColor : '#000'
	    });
	}
