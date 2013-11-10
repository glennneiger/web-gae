<?php
//This file creates a .js file(../js/suggestion.js) which gives a Suggestion box for the tag/keyword field.

//$strStockTagQuery  = "select stocksymbol as tag from ex_stock";
$strStockTagQuery  = "select id,stocksymbol as tag,CompanyName,exchange from ex_stock order by stocksymbol ";
$strStockTag="";
foreach(exec_query($strStockTagQuery) as $row)
{
	if($strStockTag==""){
   		$strStockTag.= $row[tag].' : '.$row[CompanyName].' : '.$row[exchange] ;
	}
	else{
		$strStockTag.= '","'.$row[tag].':'.$row[CompanyName].':'.$row[exchange] ;
	}
}
$strTags=$strStockTag;
$offset = 0;
$limit = 500;
//$srtTags=get_most_popular_tags($strTags,$offset, $limit);

$strTags='var customarray=new Array("'.$strTags.'");';

$fFile=fopen($STOCKSUGGESTIONJSSCRIPT,"w+");
fwrite($fFile,$strTags);
fclose($fFile);
?>
