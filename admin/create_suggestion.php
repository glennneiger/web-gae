<?php
//This file creates a .js file(../js/suggestion.js) which gives a Suggestion box for the tag/keyword field.

$strTagQuery  = "SELECT tag from ex_tags";
$strTag="";
foreach(exec_query($strTagQuery) as $row)
{
	if($strTag==""){
   		$strTag.= $row['tag'] ;
	}
	else{
		$strTag.= "','".$row['tag'] ;
	}
}

$strStockTagQuery  = "select stocksymbol as tag from ex_stock";
$strStockTag="";
foreach(exec_query($strStockTagQuery) as $row)
{
	if($strStockTag==""){
   		$strStockTag.= $row['tag'] ;
	}
	else{
		$strStockTag.= "','".$row['tag'] ;
	}
}
$strTags=$strTag."','".$strStockTag;
$offset = 0;
$limit = 500;
$srtTags=get_most_popular_tags($strTags,$offset, $limit);
$strTags="var customarray=new Array('".$strTags."');";
$fFile=fopen($SUGGESTIONJSSCRIPT,"w+");
fwrite($fFile,$strTags);
fclose($fFile);

?>



