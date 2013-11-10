<?php
ini_set("memory_limit","256M");
set_time_limit(60*30 );//1 hour
global $D_R;
include_once($D_R."/admin/lib/_admin_data_lib.php");
$objSiteMap=new SiteMap();

$objSiteMap->generateSiteMap("SECTORS");
$objSiteMap->generateSiteMap("BUSINESS NEWS");
$objSiteMap->generateSiteMap("TRADING AND INVESTING");
$objSiteMap->generateSiteMap("SPECIAL FEATURES"); 
$objSiteMap->generateDailyFeedSiteMap("DAILY FEED","18","0");
$objSiteMap->generateDailyFeedSiteMap("BUZZ","2","1");
$objSiteMap->generateDailyFeedSiteMap("COOPER","14","1");
$objSiteMap->generateDailyFeedSiteMap("SMITH","15","1");
$objSiteMap->generateDailyFeedSiteMap("TECHSTRAT","22","1");
/*
$objSiteMap->generateSiteMap("'BUSINESS & MARKETS','MARKETS'");
$objSiteMap->generateSiteMap("INVESTING");
$objSiteMap->generateSiteMap("SPECIAL FEATURES"); 
$objSiteMap->generateDailyFeedSiteMap("DAILY FEED","18");
*/
?>