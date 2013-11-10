<?php
global $D_R;
ini_set('magic_quotes_runtime',0);
include_once("$D_R/lib/_includes.php");
include_once("$D_R/lib/_db.php");
require_once("$D_R/lib/magpierss/rss_fetch.inc");
include_once($D_R.'/lib/_convert_charset.class.php');
include_once("$D_R/lib/MemCache.php");
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
$url = "http://blogs.minyanville.com/feed/";
$url = $url."?time=".time(); // Append dummy parameter to the feed URL in order to avoid cache 
$obCache= new Cache();
	$rss = fetch_rss($url);
	date_default_timezone_set('America/New_York');
	foreach($rss->items as $item){

			$arData['guid']=$item['guid']['0'];
			$arData['title']=htmlspecialchars(implode('', $item['title']), ENT_QUOTES);
			$arData['url']=$item['link']['0'];
			$arData['author_name']=$item['dc']['creator']['0'];
			$date=strtotime($item['pubdate']['0']);
			$arData['publish_date']=mysqlNow($date);

			$arCondition['guid'] = $item['guid']['0'];

			insert_or_update("blogs_posts",$arData,$arCondition);

	} // foreach
	$obCache->setBlogPostCache();
?>