// JavaScript Document
function showProgress (id,width) { // puts spinner in specified div
	var x = $(id);	
	if(!width){
		width='667';
	}
	/*x.innerHTML = '<div id="showprogress" style="align:center;"><table border="0" width="100%" style="align:center;padding-top:'+ height +'px"><tr><td style="text-align:right;vertical-align:middle;padding-top:0px;padding-bottom:0px;">Loading...</td><td style="text-align:left;padding-top:0px;vertical-align:middle;"><img src="'+ image_server +'/images/community_images/spinner.gif" style="vertical-align:middle;"></td></tr></table></div>';
	*/
x.innerHTML = '<div id="showprogress" style="float:left;"><table border="0" align="center" width="' + width + '" style="padding-top:'+ height +'px;"><tr><td style="text-align:right;vertical-align:middle;padding-top:0px;padding-bottom:0px;">Loading...</td><td style="text-align:left;padding-top:0px;vertical-align:middle;"><img src="'+ image_server +'/images/community_images/spinner.gif" style="vertical-align:middle;"></td></tr></table></div>';
	
}
// Default the image will be displayed no need to pass the 2nd parameter
// If u don't want to display the image then just call this function as :   stateChange(vardivid,0) 
function stateChange(vardivid,display) 
{
	if(display==null){
		display=1;
	}

	if (xmlHttp.readyState==4)
	{  
		if (xmlHttp.status==200)
		{  		
			document.getElementById(vardivid).innerHTML=xmlHttp.responseText;
			_uacct = "UA-30222-2";
			urchinTracker(vardivid);
			//specially written for profile use
			if(vardivid=='txtHint'){
				
				var strLocation=window.location;
				var strQueryString=strLocation.toString();
				var strFinalString=strQueryString.split('#'); 
				if(strFinalString.length>1){
					location.href=strFinalString[0]+'#userprofile';
				}
				else{
					location.href=window.location+'#userprofile';
				}
				
			}
			//end here
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	} else {
		   if(display==1){// Default the image will be displayed
			showProgress(vardivid);
		   }
		}
}

// Default the image will be displayed no need to pass the 3rd parameter
// If u don't want to display the image then just call this function as :   statesChanges(vardivid,0)

function statesChanges(vardivid,targetdiv,display) 
{
	if(display==null){
		display=1;
	}
	
	if (xmlHttp.readyState==4)
	{  
		if (xmlHttp.status==200)
		{
			var responsetext=xmlHttp.responseText;
			_uacct = "UA-30222-2";
			urchinTracker(targetdiv);
			var firstpos=0;
			var lastpos=0;
			document.getElementById(vardivid).innerHTML=responsetext;
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	} else {
		if(display==1){
			showProgress(vardivid);
		}
		}
}

function statesChangesrefresh(vardivid,targetdiv,display) 
{
	if(display==null){
		display=1;
	}

	if (xmlHttp.readyState==4)
	{  
		if (xmlHttp.status==200)
		{
			var responsetext=xmlHttp.responseText;
			googleAnalytics('2',targetdiv);
			var firstpos=0;
			var lastpos=0;

			if (responsetext.indexOf("~end~") !=-1)
			{
				lastpos=responsetext.indexOf("~end~");

			}
			if (responsetext.indexOf("~start~") !=-1)
			{
				firstpos=responsetext.indexOf("~start~")+7;
			}
			//alert(firstpos+" "+lastpos);
			document.getElementById(targetdiv).innerHTML=responsetext.substring(firstpos,lastpos);

			var totlen=responsetext.length;
			var lastpos1=lastpos+5;
			document.getElementById(vardivid).innerHTML=responsetext.substring(lastpos1,totlen);
		}
		else
		{
			document.getElementById(vardivid).innerHTML=xmlHttp.statusText;
		}
	} else {
		if(display==1){
			showProgress(vardivid);
		}
	}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  var xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    var xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    var xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}