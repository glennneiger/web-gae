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
      document.cookie = name + "=" + escape (value) + "; expires=" + expdate.toGMTString();
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

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
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
	params = "height="+height
		   + " ,width="+width
		   + " ,top=0"
		   + " ,left=0"
		   + " ,scrollbars=1"
		   + ", resizable=1"
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

function openEmbed(Src,w,h,bgcolor){
	location.href=Src;return;
	if(!bgcolor)bgcolor="fff";
	var url="/embed.htm?src="+Src
		   + "&h="+h
		   + "&w="+w
		   + "bg="+bgcolor;
	
	var wnd=window.open(url,"embedWnd","width="+w+",height="+h+",resizable=1");
}


function valid_email(val){
	return val.match(/[\w\-\.]{2,255}@[\w\-\.]{2,255}\.\w{2,5}/)
}

function getVal(frmObj,getSelVal){
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
		if(getSelVal)
			return selectedValue(frmObj.name);
		else
			return selectedText(frmObj.name);
	}
	
	return trim(frmObj.value);
}

function getValByName(frmObjName){
	return getVal(findObj(frmObjName));
}


function selectedValue(fieldName){
	if(typeof(fieldName)=="string")
		fieldName=findObj(fieldName)
	if(typeof(fieldName)=="undefined")return "";
	if(typeof(fieldName.selectedIndex)=="undefined")return "";
	return fieldName[fieldName.selectedIndex].value;
}
function selectedText(fieldName,ignoreDash){
	if(typeof(fieldName)=="string")
		fieldName=findObj(fieldName)
	if(typeof(fieldName)=="undefined")return "";
	if(typeof(fieldName.selectedIndex)=="undefined")return "";
	if(ignoreDash && fieldName[fieldName.selectedIndex].text.charAt(0)=="-")return "";
	return fieldName[fieldName.selectedIndex].text;
}
function goToValue(fieldname,value){
	if(typeof(fieldname)=="string")
		fieldname=findObj(fieldname)
	if(typeof(fieldname)=="undefined")return "";
	if(typeof(fieldname.fieldname)=="undefined")return "";
	for(i=0;i<fieldname.options.length;i++){
		if(fieldname.options[i].value==value){
			fieldname.selectedIndex=i;
			return;
		}
	}
}
function goToText(fieldname,text){
	if(typeof(fieldname)=="string")
		fieldname=findObj(fieldname)
	if(typeof(fieldname)=="undefined")return "";
	if(typeof(fieldname.selectedIndex)=="undefined")return "";
	for(i=0;i<fieldname.options.length;i++){
		if(fieldname.options[i].text==text){
			fieldname.selectedIndex=i;
			return;
		}
	}
}

function valueInList(fieldName,expVal,readText){
	if(expVal=="")return 0;
	if(typeof(fieldName)=="string")
		fieldName=findObj(fieldName);
	if(typeof(fieldName)=="undefined")return 0;
	if(typeof(fieldName.options)=="undefined")return 0;
	for(i=0;i<fieldName.options.length;i++){
		var opt=fieldName.options[i];
		var val=(readText?opt.text:opt.value);
		if(expVal==val){
			return 1;
		}
	}
	return 0;
}

function validEmail(emStr){
	return valid_email(emStr);
}
function validEmailValue(fieldname){
	return valid_email(getValByName(fieldname));
}

function addSlashes(str){
	return str.replace(/"/g,'\\"');
}

function parseStr(ampString){
	ampString=ampString.split("&");
	for(var i=0;i<ampString.length;i++){
		kv = ampString[i]
		var key=kv.substring(0,kv.indexOf("=")-1);
		var val = kv.substring(kv.indexOf("=")+1);
		eval(key+"=\""+addSlashes(val)+"\"");
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
	return str.replace(/[^\d\.]+/g,"");
}
function intonly(str){
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
function email_format(str){
	return str.replace(/[^a-zA-Z\-0-9@\.]+/g,"");
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




/*
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
		
}*/


function fixUrl(urlval){
	var loc="http://"+location.hostname
	if(location.port)
		loc+=":"+location.port;
	urlval=urlval.replace(loc,"");
	urlval=urlval.replace(/\/admin\/richedit\/?/g,"");
	return urlval;
}



function validateCCNum(cardType,cardNum){
	cardType = cardType.toUpperCase();
	var cardLen = cardNum.length;
	var firstdig = cardNum.substring(0,1);
	var seconddig = cardNum.substring(1,2);
	var first4digs = cardNum.substring(0,4);

	switch (cardType){
		case "VISA":
			return (((cardLen == 16) || (cardLen == 13)) && (firstdig == "4"));
		case "AMEX":
			var validNums = "47";
			return ((cardLen == 15) && (firstdig == "3") && (validNums.indexOf(seconddig)>=0));
		case "MASTERCARD":
			var validNums = "12345";
			return ((cardLen == 16) && (firstdig == "5") && (validNums.indexOf(seconddig)>=0));
		case "DISCOVER":
			return  ((cardLen == 16) && (first4digs == "6011"));
		case "DINERS":
			var validNums = "068";
			return ((cardLen == 14) && (firstdig == "3") && (validNums.indexOf(seconddig)>=0));
	}
	return 0;
}

function isValidExpDate(mmyy){
	mmyy=mmyy.split("/");
	if(mmyy[0].charAt(0)=="0")mmyy[0]=mmyy[0].substring(1);
	mo=parseInt(mmyy[0]);yr=parseInt("20"+mmyy[1]);
	if(!mo||isNaN(mo) || !yr ||isNaN(yr) || (mo<1||mo>12))
		return 0;
	var thisYear=new Date().getYear();
	var thisMo=new Date().getMonth();
	if(thisYear>yr)return 0;
	return 1;
}

function validZip(zipObj){
	if(getValByName(zipObj).length<5)
		return 0;
	return 1;
}
function validPhone(phoneObj){
	if(findObj(phoneObj).value.length<10)
		return 0
	return 1;
}

function openCVV2(){
	window.open('/lib/cvv2.html','cvv2','width=560,height=500,resizable=1,scrollbars=1');
}

/*============= list box stuff ==========*/
function lbMove(fbox,tbox) {
	if(typeof(fbox)=="string")fbox=findObj(fbox);
	if(typeof(tbox)=="string")tbox=findObj(tbox);
	var i = 0;
	if(fbox.value != "") {
		tbox.options[tbox.options.length] =new Option(fbox.value,fbox.value,true);
		fbox.value = "";
	}
	fbox.focus();
}

function lbRemove(box) {
	if(typeof(box)=="string")box=findObj(box);
	for(var i=0; i<box.options.length; i++) {
		if(box.options[i].selected && box.options[i] != "") {
			box.options[i].value = "";
			box.options[i].text = "";
		}
	}
	lbBumpUp(box);
} 

function lbBumpUp(abox) {
	if(typeof(abox)=="string")abox=findObj(abox);
	for(var i = 0; i < abox.options.length; i++) {
		if(abox.options[i].value == "")  {
			for(var j = i; j < abox.options.length - 1; j++)  {
				abox.options[j].value = abox.options[j + 1].value;
				abox.options[j].text = abox.options[j + 1].text;
			}
			var ln = i;
			break;
		}
	}
	if(ln < abox.options.length)  {
		abox.options.length -= 1;
		lbBumpUp(abox);
	}
}

function lbMoveUp(dbox) {
	if(typeof(dbox)=="string")dbox=findObj(dbox);
	for(var i = 0; i < dbox.options.length; i++) {
		if (dbox.options[i].selected&&dbox.options[i]!=""&&dbox.options[i]!=dbox.options[0]) {
			var tmpval = dbox.options[i].value;
			var tmpval2 = dbox.options[i].text;
			dbox.options[i].value = dbox.options[i - 1].value;
			dbox.options[i].text = dbox.options[i - 1].text
			dbox.options[i-1].value = tmpval;
			dbox.options[i-1].text = tmpval2;
			var sel=i-1;
		}
	}
	if(typeof(sel)!="undefined")
		dbox.selectedIndex=sel;
}


function lbMoveDown(ebox) {
	if(typeof(ebox)=="string")ebox=findObj(ebox);
	for(var i = 0; i < ebox.options.length; i++) {
		if (ebox.options[i].selected&&ebox.options[i]!=""&&ebox.options[i+1]!=ebox.options[ebox.options.length]) {
			var tmpval = ebox.options[i].value;
			var tmpval2 = ebox.options[i].text;
			ebox.options[i].value = ebox.options[i+1].value;
			ebox.options[i].text = ebox.options[i+1].text
			ebox.options[i+1].value = tmpval;
			ebox.options[i+1].text = tmpval2;
			sel=i+1;
		}
	}
	if(typeof(sel)!="undefined")
		ebox.selectedIndex=sel;
}

function lbSelectAll(listObj){
	if(typeof(listObj)=="string")listObj=findObj(listObj);
	if(!listObj)return;
	for(i=0;i<listObj.options.length;i++)listObj.options[i].selected="true"
}

function lbSelectSame(srcList, destList){
	if(typeof(srcList)=="string")srcList=findObj(srcList);
	if(typeof(destList)=="string")destList=findObj(destList);
	alert(typeof(destList.selectedIndex));
	destList.selectedIndex=srcList.selectedIndex;
}

