<?
session_start();
include_once($D_R."/lib/_content_data_lib.php");
global $VIDEO_SERVER,$Video_file_path,$Still_file_path,$Thumb_file_path,$bucketPath;
include_once("$D_R/lib/json.php");
$json = new Services_JSON();

$bounceback=$HTPFX.$HTHOST."/admin/upload_video.htm";
$action = $_POST['page_action'];
$arData['title'] =  $_POST['video_title'];
$arData['cat_id'] =  implode(',',$_POST['category']);
$arData['description'] =  $_POST['video_desc'];

if($_FILES['uploadVideo']['name'] != "")
{
	$arData['videofile'] =  $VIDEO_SERVER.$Video_file_path.str_replace(" ","_",$_FILES['uploadVideo']['name']);
	move_uploaded_file($_FILES['uploadVideo']['tmp_name'],$bucketPath."/".$Video_file_path.str_replace(" ","_",$_FILES['uploadVideo']['name']));
	
}
if($_FILES['uploadPodcastVideo']['name'] != "")
{
	$arData['podcasturl'] =  $VIDEO_SERVER.$Podcast_video_file_path.str_replace(" ","_",$_FILES['uploadPodcastVideo']['name']);
	move_uploaded_file($_FILES['uploadPodcastVideo']['tmp_name'],$bucketPath."/".$Podcast_video_file_path.str_replace(" ","_",$_FILES['uploadPodcastVideo']['name']));
}
if($_FILES['uploadStill']['name']!="")
{
	$arData['stillfile'] =  $VIDEO_SERVER.$Still_file_path.str_replace(" ","_",$_FILES['uploadStill']['name']);
	$arData['thumbfile'] =  $VIDEO_SERVER.$Thumb_file_path.str_replace(" ","_",$_FILES['uploadStill']['name']);

	copy($_FILES['uploadStill']['tmp_name'],$bucketPath."/".$Still_file_path.str_replace(" ","_",$_FILES['uploadStill']['name']));
	copy($_FILES['uploadStill']['tmp_name'],$bucketPath."/".$Thumb_file_path.str_replace(" ","_",$_FILES['uploadStill']['name']));
}
$arData['aol'] =  $_POST['chkAol'];
$arData['ameritrade'] =  $_POST['ckhAmeritrade'];

switch($action)
{
	case "add_video";
	$arData['creation_time'] = mysqlNow();
	$arData['uploaded_by'] = $_SESSION['AUSER'];
	$id = insert_query("mvtv",$arData);
	break;
	case "edit_video";
	$arData['updation_time'] = mysqlNow();
	update_query("mvtv",$arData,array('id'=>$_POST['video_id']));
	$id = $_POST['video_id'];
	break;
}

$status = false;
$msg = 'An error occurred while processing your request. Pleas try again.';

if($id)
{
	$obContent = new Content(11,$id);
	$meta_status = $obContent->setVideoMeta();
	if($meta_status)
	{
		$status = true;
		$msg = "Video has been uploaded successfully";
	}
}

header( "HTTP/1.1 301 Moved Permanently" );
header( "Location: ".$bounceback."?id=".$id."&msg=".$msg);
exit;

?>