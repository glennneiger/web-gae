d=document;l=(d.layers)?1:0;op=navigator.userAgent.toLowerCase().indexOf('opera')!=-1;
function gE(e,f){if(l){f=(f)?f:self;var V=f.document.layers;if(V[e])return V[e];for(var W=0;W<V.length;)t=gE(e,V[W++]);return t;}if(d.all)return d.all[e];return d.getElementById(e);}
function sE(e){if(l)e.visibility='show';else e.style.visibility='visible';}
function hE(e){if(l)e.visibility='hide';else e.style.visibility='hidden';}
function sZ(e,z){if(l)e.zIndex=z;else e.style.zIndex=z;}
function sX(e,x){if(l)e.left=x;else if(op)e.style.pixelLeft=x;else e.style.left=x;}
function sY(e,y){if(l)e.top=y;else if(op)e.style.pixelTop=y;else e.style.top=y;}
function sXY(e,x,y){sX(e,x);sY(e,y);}
function gX(e){if(l)return e.top;else if(op)return e.style.pixelTop;else return e.style.top;}
function gY(e){if(l)return e.left;else if(op)return e.style.pixelLeft;else return e.style.left;}
function sW(e,w){if(l)e.clip.width=w;else if(op)e.style.pixelWidth=w;else e.style.width=w;}
function sH(e,h){if(l)e.clip.height=h;else if(op)e.style.pixelHeight=h;else e.style.height=h;}
function goH(e){if(l)return e.clip.height;else return parseInt(e.offsetHeight);}
function goW(e){var ret;if(l)return e.clip.width;else return parseInt(e.offsetWidth);}
function sC(e,t,r,b,x){if(l){X=e.clip;X.top=t;X.right=r;X.bottom=b;X.left=x;}else e.style.clip='rect('+t+' '+r+' '+b+' '+x+')';}
function wH(e,h){if(l){Y=e.document;Y.write(h);Y.close();}if(e.innerHTML)e.innerHTML=h;}
function slide(e,x,y,sp,funcCall,xNow,yNow){
	var num;
	if(typeof e!='object'){num=e;e=slide.all[num];e.sliding=true;}
	else{if(e.sliding)return}
	xNow=xNow||parseInt(e.left||e.style.left||e.style.pixelLeft);
	yNow=yNow||parseInt(e.top||e.style.top||e.style.pixelTop);
	distX=Math.abs(xNow-x);
	distY=Math.abs(yNow-y);
	if(Math.round(xNow)!=x)xNow+=(distX/(11-sp)*sign(xNow,x));
	if(Math.round(yNow)!=y)yNow+=(distY/(11-sp)*sign(yNow,y));
	sX(e,px(Math.round(xNow)));
	sY(e,px(Math.round(yNow)));
	if(num==null){num=slide.all.length;slide.all[num]=e;}
	if(Math.round(xNow)!=x||Math.round(yNow)!=y)setTimeout('slide('+num+','+x+','+y+','+sp+',"'+funcCall+'",'+xNow+','+yNow+')', 30);
	else{
		e.sliding=false;
		if(funcCall!='')eval(funcCall);
	}
};
slide.all=[];
function sign(x,y){return(x<y)?1:-1};
function px(n){return n+(!l&&!op?'px':0)};
/*
	gE(elementName,[frame]) - element ref
	sE(elementRef)  - show element
	hE(elementRef)	- hide element
	sZ(elemtnRef,z-index) - set index
	sW(elementRef,width)  - set width
	sH(elementRef,height) - set height
	sC(elementRef,top,right,bottom,left) - set clipping
	sX(elementRef,x)    - set x pos
	sY(elementRef,y)    - set y pos	
	sXY(elementRef,x,y) - set both
	gX(elementRef)		- get x
	gY(elementRef)		-get y
	wH(elementRef,html) - write html text to a layer
*/

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function ReadCookie (name) {
      var namearg = name + "=";
      var nlen = namearg.length;
      var clen = document.cookie.length;
      var i = 0;
      while (i < clen) {
        var j = i + nlen;
        if (document.cookie.substring(i, j) == namearg) {
           var endpos = document.cookie.indexOf (";", j);
           if (endpos == -1) endpos = document.cookie.length;
           return unescape(document.cookie.substring(j, endpos));
	  }
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0) break;
      }
      return null;
    }


function WriteCookie (name, value) {
      var expdate=new Date();
     // expire cookie in 10 years by default
      expdate.setTime(expdate.getTime()+10*365*24*60*60*1000);
      document.cookie = name + "=" + escape (value) + "; expires=" + expdate.toGMTString()+"; path=/";
    }

function DeleteCookie (name) {
     var expdate = new Date();
     expdate.setTime (expdate.getTime() - 1);  // Already gone!
     var cval = ReadCookie (name);
     document.cookie = name + "=" + cval + "; expires=" + expdate.toGMTString();
   }



function inArray(str,arr){
	for(i=0;i<arr.length;i++){
		if(arr[i]==str)return 1;
	}
	return 0;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function findObj(n,d){
	return MM_findObj(n,d);
}

function WM_netscapeCssFix() {
    document.location = document.location;
}
 
function WM_netscapeCssFixCheckIn() {
  if ((navigator.appName == 'Netscape') && (parseInt(navigator.appVersion) == 4)) {
   if (typeof document.WM == 'undefined'){
      document.WM = new Object;
    }
    if (typeof document.WM.WM_scaleFont == 'undefined') {
      document.WM.WM_netscapeCssFix = new Object;
    }
    window.onresize = WM_netscapeCssFix;
  }
}
 
WM_netscapeCssFixCheckIn();

function winop(filename,width,height){
	if(!loaded)return;
	params = "height="+height
		   + " ,width="+width
		   + " ,top=0"
		   + " ,left=0"
		   + " ,scrollBars=0"
		   + ", resize=1"
	window.open(filename,"preview",params);
}

function preload() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=preload.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function rollOut(){
	MM_swapImgRestore();
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function rollOver(imgName,imgSrc){
	MM_swapImage(imgName,'',imgSrc,1);
}

function stristr(needle,haystack){
	needle = new String(needle).toLowerCase();
	haystack = new String(haystack).toLowerCase();
	return haystack.indexOf(needle)>-1?1:0;
}

function get_form(form_name){
	if(!form_name)form_name=0;
	theForm = document[form_name];
	for(i=0;i<theForm.elements.length;i++){
		var thisOne = theForm.elements[i];
		var thisName = thisOne.name.toUpperCase();
		if(stristr("select",thisOne.type)){
			eval(thisName+"_idx="+thisOne.selectedIndex);
			eval(thisName+"_txt=\""+thisOne[thisOne.selectedIndex].text+"\";");
			eval(thisName+"_val=\""+thisOne[thisOne.selectedIndex].value+"\";");
			continue;
		}
		if(stristr("text",thisOne.type)|| 
		   stristr("radio",thisOne.type)||
		   stristr("hidden",thisOne.type)){
		   eval(thisName+"=\""+escape(thisOne.value)+"\";");
		   eval(thisName+"=unescape("+thisName+");");
			continue;
		}
		if(stristr("check",thisOne.type)){
			eval(thisName+"="+thisOne.checked?1:0);
			continue;
		}
	}
}

function openEmbed(Src,w,h,bgcolor){
	location.href=Src;return;
	if(!bgcolor)bgcolor="fff";
	var url="/embed.htm?src="+Src
		   + "&h="+h
		   + "&w="+w
		   + "bg="+bgcolor;
	
	var wnd=window.open(url,"embedWnd","width="+w+",height="+h+",resizable=1");
}


function writeMonthOptions(selectedValue){
		mos = "January February March April May June July August September October November December".split(" ");
		for(i=0;i<mos.length;i++){
			sel=(selectedValue==(i+1)?" selected":"");
			document.write("<option value="+(i+1)+sel+">"+mos[i])
		}
}

function writeDayOptions(selectedValue){
	for(i=1;i<31;i++){
		sel=(i==selectedValue?" selected":"");
		document.write("<option"+sel+" value='"+i+"'>"+i)
	}
}

function writeYearOptions(startFromNowMinus,selectedValue,firstYear){
	if(!firstYear)firstYear=1900
	var Start=new Date().getYear()-startFromNowMinus;
	for(i=Start;i>firstYear;i--){
		sel=(i==selectedValue?" selected":"");
		document.write("<option"+sel+" value='"+i+"'>"+i)
	}
}


function valid_email(val){
	return val.match(/[\w\-\.]{2,255}@[\w\-\.]{2,255}\.\w{2,255}/)
}

function getVal(frmObj){
	var t=frmObj.type;
	if(stristr("text",t)||stristr("password",t))
		return trim(frmObj.value);

	if(stristr("radio",t)){
		for(i=0;i<frmObj.length;i++)
			if(frmObj[i].checked) return frmObj[i].value;
		return false;
	}
	
	if(stristr("checkbox",t))
		return frmObj.checked;
		
	if(stristr("select",t)){
		if(!frmObj[frmObj.selectedIndex].value)
			return frmObj[frmObj.selectedIndex].text
		else
			return frmObj[frmObj.selectedIndex].value;	
	}
	
			

	return trim(frmObj.value);

}

function selectedValue(fieldName){
	fieldName=findObj(fieldName);
	if(typeof(fieldName)=="undefined")return "";
	if(typeof(fieldName.selectedIndex)=="undefined")return "";
	return fieldName[fieldName.selectedIndex].value;
}
function selectedText(fieldName,ignoreDash){
	fieldName=findObj(fieldName);
	if(typeof(fieldName)=="undefined")return "";
	if(typeof(fieldName.selectedIndex)=="undefined")return "";
	if(ignoreDash && fieldName[fieldName.selectedIndex].text.charAt(0)=="-")return "";
	return fieldName[fieldName.selectedIndex].text;
}
function validEmail(emStr){
	return valid_email(emStr);
}


function parseStr(ampString){
	ampString=ampString.split("&");
	for(var i=0;i<ampString.length;i++){
		kv = ampString[i]
		var key=kv.substring(0,kv.indexOf("=")-1);
		var val = kv.substring(kv.indexOf("=")+1);
		eval(key+"=\""+addSlashes(str)+"\"");
	}
}

function ucfirst(str){
	var ret = "";
	var let=str.substring(0,1).toUpperCase();
	return let+str.substring(1,str.length);
}

function ucwords(str){
	var ret = new Array();
	words = str.split(" ");
	for(i=0;i<words.length;i++)
		ret[ret.length]=ucfirst(words[i]);
	return ret.join(" ");
}

function uspace(str){
	return str.replace(/_/g," ");
}

function stripSlashes(str){
	return str.replace(/\\/g,"");
}

function stristr(needle,haystack){
	needle=needle.toString().toLowerCase();
	haystack=haystack.toString().toLowerCase();
	if(haystack.indexOf(needle)>-1)return 1;
	return 0;
}

function trim(str){
	if(typeof(str)=="undefined")return "";
	return str.toString().replace(/(^\s+|\s+$)/g,"");
}

function numsonly(str){
	return str.replace(/[^\d]+/g,"");
}

function nobadchars(str){
	return str.replace(/\W+/g,"");
}
function alphanum(str){
	return nobadchars(str);
}

function limitCharLength(str,len){
	return str.substring(0,len);
}

function wordsonly(str){
	return str.replace(/[^a-zA-Z-\s]+/g,"");
}

function array_diff(arr1, arr2){ //returns array w/ all values of arr1 not in arr2
	var ret = new Array();
	for(var i=0;i < arr1.length;i++){
		if(!in_array(arr1[i], arr2)) ret[ret.length]=arr1[i];
	}
	return ret;
}

function array_keys(arr){
	var ret = new Array();
	for(e in arr)
		ret[ret.length]=e;
	return ret;
}

function array_search(needle,haystack){
	for(var i=0;i< haystack.length;i++){
		if(needle==haystack[i])return i;
	}
	return false;
}

function array_sum(arr){
	ret = 0;
	for(var i=0;i< arr.length;i++){
		var item = parseFloat(arr[i]);
		ret+=item;
	}
	return ret;
}
function array_unique(arr){
	var ret = new Array();
	for(var i=0;i< arr.length;i++)
		if(!in_array(item, arr[i])) ret[ret.length]=item[i];
	return ret;
}
function array_values(arr){
	var ret = new Array();
	for(e in arr)
		ret[ret.length]=arr[e];
	return ret;
}
function in_array(needle,haystack){
	for(var i=0;i< haystack.length;i++)
		if(needle==haystack[i])return 1;
	return 0;
}




function validateSignin(){
	var frm=findObj("signinform");
	var msg="";
	if(!valid_email(getVal(frm["email"])))
		msg+=" - Invalid Email Address.\n";
	if(!getVal(frm["password"]))
		msg+=" - Please enter your password\n";
	if(msg.length && !getVal(frm["password"])){
		alert("There were some problems with the information you provided:\n"+msg);
		return;
	}
	frm.submit();
}


function disagree(){
	if(confirm("Are you sure you disagree? Disagreeing with this will prohibit you from registering with Minyanville.")){
		location.href="/";
		return;
	}
}

function validateLnavSearch(){
	var frm=findObj("lnavsearch");
	if(!frm["q"].value.length){
		alert("Please enter a valid search term.");
		return;
	}
	frm.submit();
}
















var requiredVersion = 6;   
var upgradePage = "http://www.macromedia.com/software/flashplayer/";
var upgradeMsg  = "You need to upgrade your Flash version. Click OK to go to the Flash homepage. Click cancel to continue viewing Minyanville.";

var flash2Installed = false;    
var flash3Installed = false;    
var flash4Installed = false;    
var flash5Installed = false;    
var flash6Installed = false;    
var maxVersion = 6;             
var actualVersion = 0;          
var hasRightVersion = false;    
var jsVersion = 1.0;            

var isIE = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;    
var isWin = (navigator.appVersion.indexOf("Windows") != -1) ? true : false; 

jsVersion = 1.1;

if(isIE && isWin){
	document.write('<SCR' + 'IPT LANGUAGE=VBScript\> \n');
	document.write('on error resume next \n');
	document.write('flash2Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.2"))) \n');
	document.write('flash3Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.3"))) \n');
	document.write('flash4Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.4"))) \n');
	document.write('flash5Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.5"))) \n');  
	document.write('flash6Installed = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash.6"))) \n');  
	document.write('</SCR' + 'IPT\> \n'); 
}

function detectFlash() {  
	if(navigator.plugins) {
		if(typeof(navigator.plugins.refresh)=="function")
			navigator.plugins.refresh();
		if (navigator.plugins["Shockwave Flash 2.0"]|| navigator.plugins["Shockwave Flash"]) {
			var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;
      		var flashVersion = parseInt(flashDescription.charAt(flashDescription.indexOf(".") - 1));
			flash2Installed = flashVersion == 2;    
			flash3Installed = flashVersion == 3;
			flash4Installed = flashVersion == 4;
			flash5Installed = flashVersion == 5;
			flash6Installed = flashVersion >= 6;
		}
  }
	for(i=2;i<=maxVersion;i++) if(eval("flash" + i + "Installed") == true)actualVersion=i;

  	if(navigator.userAgent.indexOf("WebTV")!=-1)actualVersion=3;  
	if(actualVersion < requiredVersion && !(isIE && isWin)){
		if(ReadCookie("GOTWARNED")!="YES"){
			WriteCookie("GOTWARNED","YES");
			if(confirm(upgradeMsg)){	
					window.open(upgradePage);
			}
		}
		return;
	}else{
		void(0);//nothing because they have it. just want to display the damn page
	}
		
}


function copyHandler(e){//return
	if(typeof(document.selection)!="undefined")
		document.selection.empty();
	return false;
}


function fixUrl(urlval){
	var loc="http://"+location.hostname
	if(location.port)
		loc+=":"+location.port;
	urlval=urlval.replace(loc,"");
	urlval=urlval.replace(/\/admin\/richedit\/?/g,"");
	return urlval;
}

/*=========stylesheet from wired.com==========*/
function setActiveStyleSheet(title, reset) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title){
	  	 a.disabled = false;
		 WriteCookie("fstyle",title);
	   }
    }
  }
  if (reset == 1) {
	  WriteCookie("fstyle","");
  }
}

function setStyle() {
	var style = ReadCookie("fstyle");
	if (style != null) {
		setActiveStyleSheet(style, 0);
	}
}

function rand(max,min){
	return Math.round((max-min) * Math.random() + min);
}

function randomCharacter(){
	var chars="hoofy boo daisy sammy snapper".split(" ");
	return chars[rand(chars.length-1,0) ];
}

function tabShow(show,total,prefix) {
  if (!prefix) prefix = 'tab-';
  for (i=1;i<=total;i++) {
    _tc = 'off'; if (i==show) _tc = 'on';
    _bs = 'none'; if (i==show) _bs = 'block';
    document.getElementById(prefix+'tab-'+i).className = _tc;
    document.getElementById(prefix+'box-'+i).style.display = _bs;
  } 
  return false;
}
function showAdminProgress () 
{ // puts spinner in specified div
	var x = $('progress_bar');
	x.innerHTML = '<center><div class="statusmsg">Loading...</div></center>';
}
function finishAdminProgress () { // puts spinner in specified div
	var x = $('progress_bar');
	x.innerHTML = '';
}