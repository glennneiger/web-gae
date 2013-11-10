<?php
$qry = "SELECT id, stocksymbol FROM ex_stock WHERE (CompanyName IS NULL OR CompanyName='') AND is_active='1'";
$res = exec_query($qry);
if(!empty($res)){
	foreach($res as $key){
		$getStockData=xml2array("http://feeds.financialcontent.com/XMLREST?Account=minyanville&Ticker=".$key['stocksymbol']);
			if(!empty($getStockData))
			{
				$params['CompanyName'] = $getStockData['Quote']['Record']['CompanyName'];
				$id = update_query('ex_stock', $params, array(id=>$key['id'],stocksymbol=>$key['stocksymbol']));
				if(!empty($id)){
					echo 'Company Name has been updated for Stock Symbol--'.$key['stocksymbol'].'<br>';
				}
			}
	}
}
?>