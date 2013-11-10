<?php
$offset= $_GET['offset'];
echo $qryFeed = "SELECT * FROM `ex_stock` WHERE is_active='1' limit ".$offset.",1000";
$resList = exec_query($qryFeed);

foreach($resList as $key=>$val)
{
	if(empty($val['googlesymbol']))
	{
		$getStockData=xml2array("http://www.google.com/ig/api?stock=".$val['stocksymbol']);
		if(!empty($getStockData['xml_api_reply']['finance']['company_attr']['data']) && (strtolower($getStockData['xml_api_reply']['finance']['exchange_attr']['data'])==strtolower($val['exchange'])))
		{
			$stock = $getStockData['xml_api_reply']['finance']['exchange_attr']['data'].":".$getStockData['xml_api_reply']['finance']['pretty_symbol_attr']['data'];
			$data= array('googlesymbol'=>$stock);
			update_query('ex_stock',$data,array(id=>$val['id']));
			echo "stock ".$val['stocksymbol']." is done";
		}

	}

}

?>