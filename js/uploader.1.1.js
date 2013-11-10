var http=createRequestObject();
var uploader="";
var uploadDir="";
var dirname="";
var filename="";
var timeInterval="";
var idname="";


function createRequestObject() {
    var obj;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
    	return new ActiveXObject("Microsoft.XMLHTTP");
    }
    else{
    	return new XMLHttpRequest();
    }   
}

function traceUpload() {
   var image_prefix = document.getElementById('hidImagePrefix').value;  
  	var url = 'imageupload.php';
	var pars="filename="+filename+"&image_prefix="+image_prefix;	
	var myAjax4 = new Ajax.Request(url, {method: 'get', parameters: pars,									   										
										onComplete:function(req)
										{
											var response=req.responseText; 
											if(response.indexOf("File uploaded") != -1)
											{
												clearInterval(timeInterval);												
											}
											document.getElementById(uploaderId).innerHTML=response;
										}
								   });
 
}
function uploadFile(obj, dname) {
	uploadDir=obj.value;
	idname=obj.name;
	dirname=dname;
	
	filename=uploadDir.substr(uploadDir.lastIndexOf('\\')+1);
	//document.getElementById('loading'+idname).innerHTML="<img src='loading.gif' alt='loading...' />";
	uploaderId = 'uploader'+obj.name;
	uploader = obj.name;
	document.getElementById('formName'+obj.name).submit();
	timeInterval=setInterval("traceUpload()", 500);
}