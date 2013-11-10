function redirectMobileSite(url){
	var searchagaent= new Array("iphone","ipod","aspen","dream","android","cupcake","blackberry","opera mini","webos","incognito","webmate");
	var stragent=navigator.userAgent;
	var i,strfind;
	for(i=0;i<searchagaent.length; i++){
		struseragent=stragent.toLowerCase();
		strfind=struseragent.search(searchagaent[i]);
		if(strfind>=0){
			location=url;
		}
	}
}

function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});
	//alert(uri[[prop]]);
	return uri;
};

function getUrlMobile(guid) {
	parseUri.options = {
		strictMode: true,
		key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
		q:   {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
		}
	};
	
	var result=parseUri(window.location.href);
	var url,urlpara;
	urlpara="";	
	
	var regexarticle = "(.*)/articles/(.*)/id/([0-9]{1,})";	
	var regexvalarticle = new RegExp( regexarticle );
	var arArticlePath = regexvalarticle.exec(result.relative);

	if(arArticlePath != null)
	{
		var inLength = arArticlePath.length;
		urlpara="?guid=" + arArticlePath[inLength-1] + "&catid=4";
	}else if(result.directory.search("dailyfeed")==1){
		urlpara="?guid=" + guid + "&catid=5";
	}else if(result.directory.search("audiovideo")==1){
	  var regexdigit = "([0-9]{1,})\/{0,}(.*)";
	  var regexval = new RegExp( regexdigit );
	  var guid = regexval.exec(result.directory);
	  if(guid==null){
		urlpara="?cat=6";	  
	  }else{
		urlpara="?guid=" + guid[1] + "&catid=6";	
	  }
	}else if(result.directory=='/'){
	  url="http://m.minyanville.com";	
	}	
	if(urlpara){
		url="http://m.minyanville.com" + urlpara;	
	}	
	return url;	
}