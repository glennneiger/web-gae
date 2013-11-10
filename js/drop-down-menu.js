 var noCon = jQuery.noConflict();
     noCon(document).ready(function(){
    	 var disp = noCon("#dropmenu ul").css('display');
	
	 noCon("#dropmenu ul").css({display: "none"}); 
	 // For 1 Level
	    // noCon("#dropmenu li > ul > a > span").text("");
		 
	  // For 7 Level  
	     noCon("#portfolio").hover(function(){
		 noCon(this).find("#dropmenu ul:first").stop(true,true).fadeIn('fast');
		},
		function(){
		noCon(this).find("#dropmenu ul:first").stop(true,true).fadeOut('fast');
		}); });