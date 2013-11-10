function openArchive(month,year){
	closeHoverDiv();
	var url = host+'/investing/lloyds-wall-of-worry/archive_mod.php';
	var pars = "type=openArchive"+"&month="+month+"&year="+year;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
				onComplete:function(req){
					$('archive-right').innerHTML=req.responseText;
				}
			});
}

function displayArchiveWry(date){
	closeHoverDiv();
	var url = host+'/investing/lloyds-wall-of-worry/archive_mod.php';
	jQuery('#archive-left-wry').effect("explode",{pieces : 16},1000, function(){
							jQuery('#archive-left-wry').css("visibility","visible");
					jQuery('#archive-left-wry').css("display","block");												  
																			  });
	var pars = "type=getArchive"+"&date="+date;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
				onComplete:function(req){
					
					$('archive-left-worry').innerHTML=req.responseText;
					jQuery('.tTip').betterTooltip({speed: 150, delay: 300});
				}
			});
}

function closeArchive(){
	closeHoverDiv();
	var url = host+'/investing/lloyds-wall-of-worry/archive_mod.php';
	var pars = "type=closeArchive";
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
				onComplete:function(req){
					$('archive-right').innerHTML=req.responseText;
				}
			});

}


function closeHoverDiv(){
	if (jQuery('#tipBoxDiv').is(':visible')) {
		var tip = jQuery('.tipBox');
		tip.css({"display" :"none"});
	} 
	if (jQuery('#tipBoxFourDiv').is(':visible')) {
		var tipBoxfour = jQuery('.tipBoxfour');
		tipBoxfour.css({"display" :"none"});
	} 
}