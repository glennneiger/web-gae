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

     function closeFancyBox(){
	jQuery("#fancybox-overlay").css("display", "none");
	jQuery("#fancybox-wrap").css("display", "none");
	
     }
	function forgotPassFancyBox(){
		
		jQuery("#password_login_error").html('');
		jQuery("#forgot_pwd").val('');
		
		jQuery("#forgotpassfancypopup").fancybox({
		'titlePosition'		: 'inside',
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'autoDimensions'	: 'true',
		'centerOnScroll' 	:'true'
		});
		
		
		jQuery(document).ready(function() {
		  jQuery("#forgotpassfancypopup").trigger('click');
		});
		jQuery('#forgot_pwd').focus();
	}
