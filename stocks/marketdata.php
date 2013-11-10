<?php
ini_set('magic_quotes_runtime', 0);
global $HTPFX;
$arModule = explode(",",$_POST['module']);
$arTicker = explode(",",$_POST['value']);
$memCache = new memCacheObj();
foreach($arModule as $module)
{
	switch ($module){
	case 'summary':
		$memCacheKey[] = 'mem_'.$module;
		$arUrl[] =$HTPFX."studio-5.financialcontent.com/minyanville?Module=watchlist&Tickers=$COMP+$NYA+$SPX+$RUT+$XAX&Output=HTML";
	break;
	case 'watchlist':
		$memCacheKey[] = 'mem_'.$module;
		$arUrl[] =$HTPFX."studio-5.financialcontent.com/minyanville?Module=commodities&Output=HTML";
	break;
	case 'currency':
		$memCacheKey[] = 'mem_'.$module;
		$arUrl[] =$HTPFX."studio-5.financialcontent.com/minyanville?Module=currency&Output=HTML";
	break;
	case 'marketmovers':
		$memCacheKey[] = 'mem_'.$module;
		$arUrl[] =$HTPFX."studio-5.financialcontent.com/minyanville?Module=marketmovers&Output=HTML";
	break;
	case 'stockquote':
		foreach($arTicker as $ticker)
		{
			$memCacheKey[] = 'mem_'.$module."_".$ticker;
			$arUrl[] ='http://studio-5.financialcontent.com/minyanville?Module=stockquote&Ticker='.$ticker.'&Output=HTML';
		}
	break;
	case 'default':
	break;
	}
}
foreach($arUrl as $urlKey => $url)
{
	 $data = $memCache->getKey($memCacheKey[$urlKey]);
	if(!$data) // If no memcahe data is there
	{
		$data = file_get_contents($url);
		$memCache->setKey($memCacheKey,$data,0,10);
	}
	$arContents[] = $data;
}
$json = new Services_JSON();
echo $output = $json->encode($arContents);
?>