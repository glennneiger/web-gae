<?php
set_time_limit(60*30 );//1 hour
set_magic_quotes_runtime(0);
include_once($D_R.'/lib/ap/_model.php');
include_once($D_R.'/lib/ap/_controller.php');

//Capture AP feed data
$objapNewsController = new apNewsController();
$objapNewsController->parseRssFeed();
?>