<?php
include_once($D_R.'/lib/MemCache.php');
global $memCache,$articleCacheExpiry;
$memCache = new memCacheObj();
$json = new Services_JSON();
if(!empty($_POST['height'])){
	$id=$_POST['articleId'];
	$memCache->setKey("article_height_".$id,$_POST['height'],$articleCacheExpiry);
	$value=array('status'=>true,'msg'=>'');
	$output = $json->encode($value);
	echo strip_tags($output);
}
?>