host=window.location.protocol+"//"+window.location.host;
image_server=window.location.protocol+"//"+window.location.host;

function fetchHousingReportObject(page_name,width,height,container_id,pdfId){
	var url= host+'/housing-market-report/fetch_object.php';
	var pars = 'page_name='+page_name+"&width="+width+"&height="+height+"&pdfId="+pdfId;
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:
		function(req)
		{															
			$(container_id).innerHTML = req.responseText;
		}
	  });
}

function checkfordownload(orderNumber,orderItemSeq,orderClassId,orderCodeId,sourceCodeId,typeSpecificId,startDate,billDate,description,price,subscriptionId,orderItemType,issuesLeft,fileName,title,id){
	var url=host+'/lib/housingreport/check_download.php';
//	var url="";
	var pars = 'orderNumber='+orderNumber+'&orderItemSeq='+orderItemSeq+'&orderClassId='+orderClassId+'&orderCodeId='+orderCodeId+'&sourceCodeId='+sourceCodeId+'&typeSpecificId='+typeSpecificId+'&startDate='+startDate+'&billDate='+billDate+'&description='+description+'&price='+price+'&subscriptionId='+subscriptionId+'&orderItemType='+orderItemType+'&issuesLeft='+issuesLeft+'&fileName='+fileName+'&title='+title;
	//var issuestitle='leftIssue-'+typeSpecificId;
	if($(id).value){
		pars +='&remainingIssue='+$(id).value;
	}
	var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,onComplete:function(req)
	{	
		var post = eval('('+req.responseText+')');
		if(post.status == "notPublish"){
			alert("This issue is not yet published. We will email it to you as soon as it is and you will be able to download it right here.");
		}
		
		if(post.status == true){
			$(id).value=post.issuesLeft;
			//window.open(host+'/housing-market-report/download.php?download_file='+fileName,'blank');
			window.location=host+'/housing-market-report/download.php?download_file='+fileName;
			setTimeout("alert(\'1 more download left.\')",8000);
		}
		
		if(post.status == "last"){
			// window.open(host+'/housing-market-report/download.php?download_file='+fileName,'blank');
			window.location=host+'/housing-market-report/download.php?download_file='+fileName;
			$('downloadPaidIssue-'+typeSpecificId).innerHTML=post.msg;
			setTimeout("alert(\'no more download left.\')",8000);
		}
	}});
}

function downloadFreeIssue(fileName){
	window.open(host+'/housing-market-report/download.php?download_file='+fileName,'blank');
}

function goToList() {
	  var $target = $j("#showList");
      $target = $target.length && $target 
      var targetOffset = ($target.offset().top - 20);
      $j('html,body').animate({scrollTop: targetOffset}, 1800);

}
