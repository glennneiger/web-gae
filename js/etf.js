function fetchETFObject(page_name,width,height,container_id){
	var url= host+'/etf/fetch_object.php';
	var pars = 'page_name='+page_name+"&width="+width+"&height="+height;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:
					function(req)
					{															
						$(container_id).innerHTML = req.responseText;
					}
				  });
}