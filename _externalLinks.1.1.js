<!-- Copyright 2005 Bontrager Connection, LLC
// More info: "Automatic New Window for External Links" 
//    at http://willmaster.com/possibilities/archives/
// Also see http://BontragerConnection.com/

var Domains = "minyanville-buzz.appspot.com,storage.googleapis.com,admin.minyanville.com,model.minyanville.com,beta.minyanville.com,cooper.minyanville.com,finance.minyanville.com,qa.minyanville.com,redesign.minyanville.com,videos.minyanville.com,minyanville.com";
////////////////////////////////////////////
// No other customizations are necessary. //
////////////////////////////////////////////
if(Domains.indexOf(" ") != -1) {
   var splitarray = Domains.split(" ");
   Domains = splitarray.join("");
}
Domains = Domains.toLowerCase();
var DomainsArray = Domains.split(",");
for(var i = 0; i < document.links.length; i++) {
   if(document.links[i].hostname.length < 1) 
   { continue; }
   if(document.links[i].target.length > 0)
   { continue; }
   var h = document.links[i].hostname.toLowerCase();
   var makeNewWindow = true;
   for(var ii = 0; ii < DomainsArray.length; ii++) {
      if(DomainsArray[ii] != h) 
      { continue; }
      makeNewWindow = false;
      break;
   }
   if(makeNewWindow == true)
   { document.links[i].target = '_blank'; }
}
//-->
