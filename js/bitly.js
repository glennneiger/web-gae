
function callBitlyApi(status,url){
	postval="status="+status+"&url="+url;
	jQuery.ajax({
		type:"POST",
	    url:host+"/lib/_bitly_ajax.php",
		data:postval,
		error:function(){
			
		},
			success:function(response){
				if(response){
					window.open(response,'_blank');
				}								
			}
		});	
}