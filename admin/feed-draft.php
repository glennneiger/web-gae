<?php
session_start();
global $D_R,$IMG_SERVER;
include_once("$D_R/admin/lib/_admin_data_lib.php");


$feed['id']=$_POST['id'];
$feed['title']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['feed']['title'])));
$feed['excerpt']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['feed']['excerpt'])));
$feed['layout_type']=$_POST['feed']['layout_type'];
$feed['body']=$_POST['editorDatafld'];

$feed['contrib_id']=$_POST['feed']['contrib_id'];
$feed['position']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['feed']['position'])));
$currenttime= strtotime(date('Y-m-d h:i:s'));
$feed['publish_date']=strtotime($_POST['year']."-".$_POST['mo']."-".$_POST['day']." ".$_POST['hour'].":".$_POST['minute'] .":0");

$feed['quicktitle']=addslashes(mswordReplaceSpecialChars(stripslashes($_POST['quicktitle'])));


$feed['is_buzzalert']=($_POST['feed'][is_buzzalert]=="1")?1:0;
$feed['feedtopic']=$_POST['feedtopic'];
$feed['feedticker']=$_POST['feedticker'];
$feed['feedsource']=$_POST['feedsource'];
$feed['feedsource_link']=$_POST['feedsource_link'];
$feed['is_yahoofeed'] = ($_POST['feed'][is_yahoofeed]=="1")?1:0;
$feed['is_approved']=0;
$feed['is_live']=0;
$feed['is_draft']=0;

$feed['imagenamedf']=$_POST['imagenamedf'];

if($_POST['dailyfeedimage'])
{
	$feed['chart1']=$IMG_SERVER."/assets/dailyfeed/uploadimage/".date('mdy').'/'.$_POST['dailyfeedimage'];
	$feed['dailyfeedimage']=$_POST['dailyfeedimage'];
	
}

if($_POST['chkImage'])
{
	$feed['chart1']=$IMG_SERVER."/assets/dailyfeed/uploadimage/".date('mdy').'/'.$_POST['chkImage'];
	$feed['dailyfeedimage']=$_POST['chkImage'];
}


$feed['publish_date']=mysqlNow($feed['publish_date']);
$feed['creation_date']=mysqlNow();
$feed['admin_id']=$_SESSION['AID'];


$cond['admin_id']=$_SESSION['AID'];

$id=insert_or_update("daily_feed_draft",$feed,$cond);

?>