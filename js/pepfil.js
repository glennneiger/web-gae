host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;

function fetchPepfilObject(page_name,width,height,container_id){
	var url= host+'/pepfil/fetch_object.php';
	var pars = 'page_name='+page_name+"&width="+width+"&height="+height;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:
					function(req)
					{															
						$(container_id).innerHTML = req.responseText;
					}
				  });
}


function fetchPepfilRptObject(page_name,articleId,width,height,container_id){
	var url= host+'/pepfil/fetch_object.php';
	var pars = 'page_name='+page_name+"&articleId="+articleId+"&width="+width+"&height="+height;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:
					function(req)
					{															
						$(container_id).innerHTML = req.responseText;
					}
				  });
}
