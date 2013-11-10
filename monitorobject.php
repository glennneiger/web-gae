<?php
	echo '<pre>';
	$cacheObject= new ArticleCache();
	$memcacheOutput=$cacheObject->getFreeAndPremiumArticles();
	echo "Memcache Object Output:<br>";
	print_r($memcacheOutput);
	echo "-----------------------<br>";
	$arLatestArticles=$cacheObject->setLatestArticles();
	$arLatestPremiumArticles=$cacheObject->setLatestPremiumContent();
		/*$qryItems = "SELECT item_id id,title,author_name author,author_id,publish_date,url,item_type FROM ex_item_meta EIT
WHERE item_type IN('1') AND is_live='1' AND publish_date > ('".date('Y-m-d',strtotime(mysqlNow()))."' - INTERVAL 2 WEEK) order by publish_date DESC";
		$resItems = exec_query($qryItems);
print_r($resItems);*/
	//print_r($arLatestArticles);
	echo "Actual Output :<br>";
	$arAllArticles = array_merge($arLatestArticles,$arLatestPremiumArticles);
print_r($arAllArticles);
	
?>
