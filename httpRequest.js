
var req;
var theID
function requestHTTP(url, formElemID) {
	theID = formElemID;
   // Internet Explorer
   try { req = new ActiveXObject("Msxml2.XMLHTTP"); }
   catch(e) {
      try { req = new ActiveXObject("Microsoft.XMLHTTP"); }
      catch(oc) { req = null; }
   }

   // Mozailla/Safari
   if (!req && typeof XMLHttpRequest != "undefined") { req = new XMLHttpRequest(); }

   // Call the processChange() function when the page has loaded
   if (req != null) {
      req.onreadystatechange = processChange;
      req.open("GET", url, true);
      req.send(null);
   }
}


function requestHTTP_POST(processForm, url, formElemID) {
	var sParams = "";
	
	theID = formElemID;
   // Internet Explorer
   try { req = new ActiveXObject("Msxml2.XMLHTTP"); }
   catch(e) {
      try { req = new ActiveXObject("Microsoft.XMLHTTP"); }
      catch(oc) { req = null; }
   }

   // Mozailla/Safari
   if (!req && typeof XMLHttpRequest != "undefined") { req = new XMLHttpRequest(); }

   // Call the processChange() function when the page has loaded
   if (req != null) {
      req.onreadystatechange = processChange;
      req.open("POST", url, true);
      req.send(null);
   }
}




function processChange() {
   // The page has loaded and the HTTP status code is 200 OK
   if (req.readyState == 4 && req.status == 200) {

      // Write the contents of this URL to the searchResult layer
      getObject(theID).innerHTML = req.responseText;
      //getObject("searchResult").innerHTML = req.responseText;
   }
}

function getObject(name) {
   var ns4 = (document.layers) ? true : false;
   var w3c = (document.getElementById) ? true : false;
   var ie4 = (document.all) ? true : false;

   if (ns4) return eval('document.' + name);
   if (w3c) return document.getElementById(name);
   if (ie4) return eval('document.all.' + name);
   return false;
}


/*window.onload = function() {
   getObject("q").focus();
}*/



function addPostParam(sParams, sParamName, sParamValue) {
	if (sParams.length > 0) { 
		sParams += "&";
	}
	
	return sParams + encodeURIComponent(sParamName) + "="
		+ encodeURIComponent(sParamValue);
}