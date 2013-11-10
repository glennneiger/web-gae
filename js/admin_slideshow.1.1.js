function unlock_slideshow(id,donotRedirect)
{
	var url = host + "/admin/slideshow_unlock.htm?id="+id;
	var postAjax = jQuery.ajax({url:url,success:function(){
													if(!donotRedirect){
														window.location='approve_slideshow.htm';
													}
													}});
	window.onbeforeunload = null;
}

function cancel(id)
{

	if(id!=="")
	{
		unlock_slideshow(id,false);
	}
	else
	{
		window.location='approve_slideshow.htm';
	}
}