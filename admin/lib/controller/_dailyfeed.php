<?php
global $D_R;
$itemtype=$_REQUEST['itemtype'];
$imageid=$_REQUEST['imageid'];
include_once($D_R."/admin/lib/_dailyfeed_data_lib.php");
$objDailyFeed= new Dailyfeed();
$objDailyFeed->removeImage($itemtype,$imageid);
?>
