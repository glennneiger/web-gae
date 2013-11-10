<?php
set_time_limit(60*30 );//1 hour

global $D_R;
include_once($D_R."/admin/qp/qp_classes/class_transaction.php");
$sqlGetStocks="SELECT stocksymbol,SecurityName FROM ex_stock GROUP BY stocksymbol";
$resGetStocks=exec_query($sqlGetStocks);

$objTransaction=new Qtransaction();
foreach($resGetStocks as $stock)
{
	$stockDetails=$objTransaction->getstockdetails($stock['stocksymbol']);
	if($stockDetails==0)
	{
		update_query("ex_stock",array('is_active'=>'0'),array('stocksymbol'=>$stock['stocksymbol']));
		echo "Synbol: ". $stock['stocksymbol']. " deactivated sucessfully.";
	}
}

?>
