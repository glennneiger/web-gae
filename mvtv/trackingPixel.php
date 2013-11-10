<?php
include_once("$D_R/lib/_ing_config.php");

// Get Advertiser Id
$tracking[advid]=$_GET[advid];

//Get teh Video id
if($_GET['videoid'])
	$tracking[videoid]=$_GET['videoid'];

// Get position [start, mid , end]
$tracking[pos]=$_GET[pos];

//Get domain name
$tracking[domain]=$_GET[domain];

//Get current time
$tracking[time]=date('U');

$id="";

if(isset($tracking[advid]) && isset($tracking[pos])){
	$id=insert_query('trackingPixel',$tracking);
	$handle = fopen($ING_URL[$tracking[pos]], "r");
}

if(isset($id)){
	echo "&result=success";
}
else{
	echo "&result=failed";
}
?>