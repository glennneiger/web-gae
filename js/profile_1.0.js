var xmlHttp;
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	} 
function requestProfileInfo(url) {  // This function does the AJAX request
  var vardiv='txtHint';
	height='40';
	width='475';
  xmlHttp.open("GET", url, true);
  xmlHttp.onreadystatechange =  function(){stateChange(vardiv,width);};
  xmlHttp.send(null);
}
function preHttpRequest(divId,url) {
	width='475'; 
	xmlHttp.open("GET",url, true);
	xmlHttp.onreadystatechange=function() {stateChange(divId,width);};
	xmlHttp.send(null); 
}
function refreshdiv(url,targetdivpass) {  // This function does the AJAX request
	var vardiv='txtHint';
	var targetdiv=targetdivpass;
	height='20';
	width='475';
	//document.getElementById("hidfriend").style.display="";
	//document.getElementById("watchlist").style.display="";  
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange =  function(){statesChangesrefresh(vardiv,targetdiv,width);};
	xmlHttp.send(null);
}
function requestfriend(url,visitorid,sid) {  // This function does the AJAX request
  var vardiv='profileheader';
  var strfriend=url + "?sid=" + sid + "&visitorid=" + visitorid;
	height='20';
  xmlHttp.open("GET", strfriend, true);
	xmlHttp.onreadystatechange =  function(){stateChange(vardiv,0);};
  xmlHttp.send(null);
}
function editProfileInfo(url,type) {  // This function does the AJAX request
	height='20';
	width='475';
  if(type=='profile')
  {
	var str=document.getElementById('attribut').value;
	var dobid=document.getElementById('dobid').value;
	var txt=str.split("~");
    var len=txt.length;
    var strlen=len-1;
    var strval="";
    for (x=1;x<=strlen;x++)
    {
		if(txt[x]==dobid){
	if((document.getElementById('day').value!='')||(document.getElementById('month').value!='')||(document.getElementById('year').value!='')){
		strval=strval + "~" + escape(document.getElementById('day').value);
/*		if((document.getElementById('day').value!='')&&((document.getElementById('month').value!='')||(document.getElementById('year').value!='')))
		{*/
			strval=strval + "/"; 
//		}
		strval=strval + escape(document.getElementById('month').value);
//		if((document.getElementById('month').value!='')&&(document.getElementById('year').value!=''))
//		{
			strval=strval + "/"; 
//		}
		
		strval=strval + escape(document.getElementById('year').value);
		}
		else
		{
			strval=strval + "~" + "";
		}
	  }
	  else
	  {
		strval=strval + "~" + escape(document.getElementById(txt[x]).value);
	  }
    }
	var str=url + "?mode=edit&type=" + type + "&attribute=" + str + "&attval=" + strval;
	}else if(type=='friend'){
    document.getElementById("hidfriend").style.display="none";
    var styles= document.getElementById("watchlist").style.display;
    //alert(styles);
    if(styles=="none")
    {
      document.getElementById("watchlist").style.display="";
    }
    var str=url + "?mode=edit&type=" + type;
  }
	else if(type=='watchlist'){
    document.getElementById("watchlist").style.display="none";
    var stylewatchlist= document.getElementById("hidfriend").style.display;
    if(stylewatchlist=="none")
    {
      document.getElementById("hidfriend").style.display="";
    }
    var str=url + "?mode=edit&type=" + type;
  }
  else
  {
	  alert("clicked twice");
  }
  if(type=='profile')
  {
  //when submit button will click
	xmlHttp.open("POST", str, true);
	xmlHttp.onreadystatechange = function() { stateChange('txtHint',width); }
	xmlHttp.send(str);
  }
  else
  {
	xmlHttp.open("GET", str, true);
	xmlHttp.onreadystatechange = function() { stateChange('txtHint',width); }
	xmlHttp.send(null);
  }
}
function deleteProfileInfo(url,sid,sname) {  // This function does the AJAX request
	var str=url + "&mode=delete&sid=" + sid + "&sname=" + sname;
	//alert(str);
	width='475'; 
  xmlHttp.open("GET", str, true);
  xmlHttp.onreadystatechange = function() { stateChange('txtHint',width); }
  xmlHttp.send(null);
}
function addProfileInfo(url,sid) {  // This function does the AJAX request
  width='475'; 
  var str=url + "&mode=add&sid=" + sid;
  xmlHttp.open("GET", str, true);
  xmlHttp.onreadystatechange = function() { stateChange('txtHint',width); }
  xmlHttp.send(null);
}
function viewfulllist(url,type,userid)
{
	height='20';
	width='475'; 
  if(type=='friend')
  {
    document.getElementById("hidfriend").style.display="none";
    var styles= document.getElementById("watchlist").style.display;
    if(styles=="none")
    {
      document.getElementById("watchlist").style.display="";
    }
    var str=url + "?userid=" + userid + "&mode=edit&viewfull=yes&type=" + type;
  }
  else if(type=='watchlist')
  {
    document.getElementById("watchlist").style.display="none";
    var stylewatchlist= document.getElementById("hidfriend").style.display;
    if(stylewatchlist=="none")
    {
      document.getElementById("hidfriend").style.display="";
    }


    var str=url + "?userid=" + userid + "&mode=edit&viewfull=yes&type=" + type;
 
  }
  xmlHttp.open("GET", str, true);
	xmlHttp.onreadystatechange = function() { stateChange('txtHint',width); }
	xmlHttp.send(null);

}
function saveProfileInfo(url,sid) {  // This function does the AJAX request
  height='50';
  width='475'; 
  var selObj = document.getElementById("stocks").value;
  //var val = selObj.options[selObj.selectedIndex].value;
  var str=url + "&mode=save&sid=" + sid + "&val=" + selObj;
  xmlHttp.open("GET", str, true);
  xmlHttp.onreadystatechange = function() { stateChange('txtHint',width);}
  xmlHttp.send(null);
}
