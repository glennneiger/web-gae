<?php
set_time_limit(300) ;
global $D_R;
$sitem=$_GET['syncitem'];
$showlogs=$_GET['logs'];
include_once($D_R."/lib/_image_rsync.php");
$obRsync = new ImageSync($showlogs);
if($sitem){
	$obRsync->RsynchImages($sitem);
}else{
	$obRsync->RsyncUploadImages();
}

?>