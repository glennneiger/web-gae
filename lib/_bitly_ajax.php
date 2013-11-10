<?php
global $D_R,$HTPFX,$HTHOST;
include_once($D_R.'/lib/_bitly_lib.php');
$objbitly= new bitly();
$status=stripslashes($_POST['status']);
$fullUrl=$_POST['url'];
$twitturl=$objbitly->shorten($fullUrl);
$twitturl="http://twitter.com/?status=".$status.' '.$twitturl.' '."@minyanville";
echo $twitturl;
?>