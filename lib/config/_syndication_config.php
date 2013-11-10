<?php
global $D_R,$STORAGE_SERVER;
/*Yahoo Syndication Configuration Starts */
$yahoohost="ftp.minyanville.com";
$yahoouser="yahoo-test";
$yahoopass="mvil19";
$yahoopath="minyanville";
$yahoofullpath = "yahoo";
$NOTIFY_FEED_ERROR_FROM="Minyanville <support@minyanville.com>";
$NOTIFY_FEED_ERROR_TO="Minyanville <tech@minyanville.com>";
$NOTIFY_FEED_ERROR_SUBJECT="Yahoo XML Feed Error";
$feed_error_template=$HTPFX.$HTHOST."/emails/_eml_feed_error.htm";
/*Yahoo Syndication COnfiguration Ends */

/*====== MSN NEWSML Configuration Start ======*/
$LATESTARTICLE="/home/sites/minyanville/web/assets/NewsMLCreatesArticle.txt";
$NEWSMLTIMEINTERVAL="30 MINUTE";
//$readfile = file($STORAGE_SERVER."/assets/data/urls_for_msn.csv"); // read block
/*====== MSN NEWSML Configuration End ======*/

/*DirectoryM Syndication Configuration Starts */
$ftp_conn_id = "ftp.drivehq.com";
$ftp_user_name = "minyanville_dm";
$ftp_password = "dm_62009";
/*DirectoryM Syndication Configuration Ends */

/*Money Show Co-reg. Syndication*/
$moneyShowHost="ftp.minyanville.com";
//$moneyShowHost="84.40.30.10";
$moneyShowUser="moneyshow";
$moneyShowPass="M68ru36B";
$moneyShowFilePath="/home/sites/minyanville/moneyshow/";
$moneyShowPort="21";
$moneyShowBackup="backup";
$moneyShowFileName="moneyshow.csv";

?>