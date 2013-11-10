<?php
include_once("$D_R/admin/ss/ss_classes/class_Optiontransaction.php");
$objTickerCheck = new optiontransaction();
$offset= $_GET['offset'];
echo $qryFeed = "SELECT * FROM `ex_stock` WHERE is_active='1' limit ".$offset.",1000";
$resList = exec_query($qryFeed);

foreach($resList as $key=>$val)
{
	if(empty($val['minyanvilleFinance']))
	{
		$getStockData=$objTickerCheck->getQuoteDetails($val['stocksymbol'],'');
		if(!empty($getStockData))
		{
			$stock = $getStockData['ExchangeShortName'].":".$getStockData['Ticker'];
			$data= array('minyanvilleFinance'=>$stock);
			update_query('ex_stock',$data,array(id=>$val['id']));
			echo "stock ".$val['stocksymbol']." is done";
		}
	}

}

?>