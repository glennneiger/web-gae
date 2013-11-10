<?php
set_time_limit(60*30 );//1 hour
global $HTPFX,$HTHOST;
include_once($D_R.'/lib/_bitly_lib.php');
$objbitly= new bitly();

$sql = "select id,url from ex_item_meta where bitly_url is NULL and item_type IN (1,18) LIMIT 0,1000";
$res = exec_query($sql);
if(count($res) >  0)
{
	$i = 0;
	foreach ($res as $row)
	{
		$bitlyUrl=$objbitly->shorten($HTPFX.$HTHOST.$row['url']);
		if($bitlyUrl != "")
		{
			$sqlUpdate = "UPDATE ex_item_meta set bitly_url ='".$bitlyUrl."' WHERE id = ".$row['id'];
			$res = exec_query($sqlUpdate);
			echo $i."Row Done";
		}
		else
		{
			echo "record processed = ".$i;
			exit();
		}
		$i++;
	}
}
else 
{
	echo "no more record";
}
echo $i."Records processed";
?>