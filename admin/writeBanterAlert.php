<?php
global $D_R;
include_once($D_R."/lib/_image_rsync.php");
$gettimestamp=$_GET['timestamp'];
write_file($BANTER_LATEST_POST, $gettimestamp);
$obRsync = new ImageSync();
$obRsync->synchBuzzFile();
?>