function showslideshow(nav)
{
	if(slide_no==1)
	{
	   firstpreview=1;	
	}
	else
	{
	   firstpreview=0;	
	}
	
	if(nav== 'prev')
	{
		if ((slide_no >1)  )
		{
			slide_no=--slide_no;		 		
		}  
	}
	else if(nav== 'next')
	{
		if (slide_no<total_slides)
		{
			slide_no=++slide_no;
		}	 	 
	}

	
	if(slide_no==total_slides)
	{
	  lastpreviews=1;
	  //clearInterval(intr); 
	}
	
	else
	{
		lastpreviews=0;
	}
	
	if(lastpreview==1 && nav=='next' )
	{
		var url= host+'/slideshow/end_slide.php?a=' + anum+'&title='+slide_title;
		var myAjaxdata = new Ajax.Request(url, {method: 'GET',onComplete:showEndSlide});	
	}
	
	if(lastpreview!=1 && nav=='next' )
	{
		var url= host+'/slideshow/navigate.php?a=' + anum+'&slide_no='+slide_no+'&preview='+ previewdata+'&lastpreview='+ lastpreviews+'&firstpreview='+ firstpreview;
		var myAjaxdata = new Ajax.Request(url, {method: 'GET',onComplete:slideupd});
}

	if(firstpreview!=1 && nav=='prev')
	{
		var url= host+'/slideshow/navigate.php?a=' + anum+'&slide_no='+slide_no+'&preview='+ previewdata+'&lastpreview='+ lastpreviews+'&firstpreview='+ firstpreview;
		var myAjaxdata = new Ajax.Request(url, {method: 'GET',onComplete:slideupd});	
	}  
}

function showEndSlide(req)
{
	var resposnseval;
	var responsearr= new Array();
	resposnseval =req.responseText;	
	var slide_html = resposnseval;
	jQuery(".mv_slideshow_head").css('display','none');
	jQuery(".slide_main_container").html(slide_html);

}
function slideupd(req)
{
var resposnseval;
var responsearr= new Array();
var img_server = "http://image.minyanville.com";
 resposnseval =req.responseText;	
if(resposnseval==""){
		$("bt_left").src = image_server + "/images/slideshow/previous_button_disable.jpg";//disabled nav_back_disabled.png";
		$("bt_left").disabled = true;
		$("bt_left").style.cursor = "default";
		$("bt_right").src = image_server + "/images/slideshow/next_button_disable.jpg";//disable nav_fwd_disabled.png";
		$("bt_right").disabled = true;
		$("bt_right").style.cursor = "default";

}
	responsearr = resposnseval.split('~');			
if(responsearr[1]=='Y')
{
	$("bt_left").src = image_server + "/images/slideshow/previous_button.jpg";
	$("bt_left").disabled = false;
	$("bt_left").style.cursor = "pointer";
}
else if(responsearr[1]=='N')
{
		$("bt_left").src = image_server + "/images/slideshow/previous_button_disable.jpg";//disabled nav_back_disabled.png";
		$("bt_left").disabled = true;
		$("bt_left").style.cursor = "default";
	}	   

if(responsearr[2]=='Y')
{
		$("bt_right").src = image_server + "/images/slideshow/next_button.jpg";
		$("bt_right").disabled = false;
			$("bt_right").style.cursor = "pointer";
}
	//$("titletext").innerHTML=responsearr[4];
	var slideimgurl=responsearr[8];
	$("slidecontent").innerHTML=responsearr[5];
	if(slideimgurl=='No Slide Image')
	{
			$('slideimage').innerHTML='';
			$('slideimage').removeClassName("slideimages");
	}
	else
	{
		$('slideimage').addClassName("mv_mid_left_cont");
		var imgwidth=responsearr[9];
		var imgheight=responsearr[10];
		$('slideimage').innerHTML='<img style="width: 317px; height: 321px;" src="'+slideimgurl+'" />';
	}
	slide_no =responsearr[3];
	lastpreview=responsearr[6];
	firstpreview=responsearr[7];
	var vardivid ="slideshow- " + responsearr[4] + " - " +responsearr[3];
	
	googleAnalytics('1',vardivid);

	/* Adds coding starts */
	var axel = Math.random() + "";
	var ord = axel * 1000000000000000000;
	var url= host+'/slideshow/navigate.php';
	var pars = '?requestForadds=1&ord='+ord;
	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onComplete:function finishSearchkeyword(req)
	{
	$('slideshowadds').innerHTML="";
	$('slideshowadds').innerHTML=req.responseText;
	}});
	
	var url= host+'/slideshow/navigate.php';
	var pars = '?requestForRightadds=1&ord='+ord;
	
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onComplete:function finishSearchkeyword(req)
	{
	$('slideshowrightadds').innerHTML="";
	$('slideshowrightadds').innerHTML=req.responseText;
	}});
	
	/* Adds coding ends */
        // when slideshow completes and autoplay is true
	if(slide_no==total_slides)
	{
		//alert("slide_no==total_slides"+slide_no+"=="+total_slides)
		if(autoplay)
		{
			timer = setInterval("goForNewSlideShow("+anum+")",pausetime);
			return false;
		}	
	}
}

function showProgress (id) { // puts spinner in specified div
	var x = $(id);
	x.innerHTML = '<div style="padding-left:280px;padding-top:10px; width:70px; height:70px;border:0px solid red;" ><img src="'+image_server+'/images/slideshow/indicator.gif" height="62" width="62" /></div>';
}

/* Play functionalities */
function setSVal(coksid)
{
	var timetoset=parseInt($('debug1').value);
	var url= host+'/slideshow/navigate.php';
	var pars = 'ses='+timetoset+'&requestForses=1&coksid='+coksid;
	var myAjaxdata = new Ajax.Request(url, {method: 'post',parameters: pars});
}

function goForNewSlideShow(curnt_slideshowid)
{
	if(slide_no==total_slides)
	{
		var url= host+'/slideshow/navigate.php';
	var pars = '?requestForReload=1&curnt_slideshowid='+curnt_slideshowid;
		var myAjax14 = new Ajax.Request(url, {method: 'post', parameters: pars,
		onComplete:function finishSearchkeyword(req){
		var resposnseval=req.responseText;
//		alert(resposnseval);
		var responsearr = resposnseval.split('~');
		var id=responsearr[0];
		var position=responsearr[1];
		var preview=responsearr[2];
		//clearInterval(timer);
		if(autoplay)
		{
			if(id=='noslide' || preview=='0')
			{
				//***alert("No related Slideshow Found");
			}
			else
			{
			if(!isNaN(parseInt(id)))
			{
			var slidepgno = $F('slidepagenos');
				window.location.href="/slideshow/"+parseInt(id)+"/"+position;
			}else
			{
				//alert("Invalid id");
				}
			}
		}
	}});
}	else
	{
		//clearInterval(timer);
	}
}

function getSlidePhotos(divid,url)
{
	//alert("url"+url);
	var  pars= "";
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars,
	onLoading:showSlideSmallProgress(divid),
	onComplete:function(req)
	{
		$(divid).innerHTML = req.responseText;
		// change the style
		$('thumb_'+anum).removeClassName('mv_tumb');
		$('thumb_'+anum).addClassName('mv_tumb_selected');
	 }
	});
}
function showSlideSmallProgress(id) { // puts spinner in specified div
	var x = $(id);
	x.innerHTML = '<table border="0" align="center" style="clear:both;margin-top:50px;"><tr><td style="text-align:right;color:#FFF;">Loading...</td><td style="text-align:left;"><img align="absmiddle" src="'+ image_server +'/images/slideshow/indicator.gif" width="45" height="45" /></td></tr></table>';
}