var xmlHttp
var vardiv
function showtradingcenter(div,url)
{ 
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
	  	return;
	} 
	var vardiv='tradingcenter';
	xmlHttp.open("GET",url,true);
	
	xmlHttp.onreadystatechange=function() { stateChange(vardiv,0); };
	xmlHttp.send(null);
}  <!--Code end for trading center-->

function load_ads()
{
	var tradingcenter_url=host + '/layout/trading_center.php';
	showtradingcenter('tradingcenter',tradingcenter_url)
}