<?php
global $D_R,$VIDEO_SERVER,$HTPFX,$HTHOST,$bucketPath,$cloudStorageTool,$StorageListPath,$CDN_SERVER;
include_once("$D_R/lib/_content_data_lib.php");
include_once($D_R.'/lib/config/_syndication_config.php');
?>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/min/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/min/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/admin.1.12.js" ></script>
<?php 
function uploadFile($upload_file_path, $fileName,$name, $fname)
{
?>
<script>
uploadradioFile('<?=$upload_file_path?>','<?=$fileName?>','<?=$name?>','<?=$fname?>');
</script>
<?
return 1;
}

$StorageListPath."?prefix=assets/radio/";

$bucketUrl = file_get_contents($StorageListPath."?prefix=radio/");
$bucketFiles = json_decode($bucketUrl);
$uploadFiles = $bucketFiles->items;

if(!empty($uploadFiles))
{
foreach($uploadFiles as $key=>$val)
{
	$name = str_replace("radio/" ,"", $val->name);
	$ext = explode(".",$name);
	$ext = array_pop($ext);
	if($ext=="mp3")
	{
		$date = date('MdyHis');
		$fname = $date.'.mp3';
		$local_file_path = $bucketPath.'/assets/radio/'.$date.'.mp3';
		$qry = "SELECT id FROM article_meta WHERE item_key='radiofile' AND `item_value` LIKE '%".$date.".mp3'";
		$resList = exec_query($qry,'1');
		if(empty($resList))
		{
			$res = uploadFile($local_file_path, $val->name,$name,$fname);
		}
	}
	else
	{
		$fileUrl = file_get_contents("http://admin02d.minyanville.com/admin/robot.RemoveFileBucket.php?type=radio&file=".$fname);
		$bucketFileDelData = json_decode($fileUrl);  
		$to="nidhi.singh@mediaagility.co.in,news@minyanville.com";
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject="Radio Artcile Posted";
		$strurl = "?type=radio&error=1&file=".$val->name;
		mymail($to,$from,$subject,inc_web($feed_error_template.$strurl));
	}
}
	
	if(is_array($article_id)){
		$articleList = implode(",",$article_id);
		$to="nidhi.singh@mediaagility.co.in,news@minyanville.com";
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject="Radio Artcile Posted";
		$strurl = "?article_id=".$articleList."&type=radio";
		mymail($to,$from,$subject,inc_web($feed_error_template.$strurl));
	}
}



?>