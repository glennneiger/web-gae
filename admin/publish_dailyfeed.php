<?
include_once("$D_R/lib/config/_dailyfeed_config.php");
include_once($D_R.'/lib/_action.php');
include_once("../lib/_includes.php");
include_once("../lib/_db.php");
include_once("../lib/MemCache.php");
include_once("../lib/_content_data_lib.php");
include_once("../lib/config/_article_config.php");
include_once("$ADMIN_PATH/lib/_yahoo_lib.php");
include_once("$ADMIN_PATH/lib/_dailyfeed_data_lib.php");
$objYahoo	= new YahooSyndication();
$objDailyFeed= new Dailyfeed();
set_time_limit ( 60*30 );//1 hour
echo "Publishing Dailyfeed started at : ".date("Y-m-d h:i")."<BR>\n";

$qry="select id,is_buzzalert,is_live,publish_date,title from daily_feed where is_approved='1' and is_deleted='0' and is_draft='0' and is_live='0' and publish_date <= '".mysqlNow()."' ORDER BY id";
$result=exec_query($qry);

if($result){
$objAction= new Action();
foreach($result as $row)
{
		$par['is_live']='1';
		update_query("daily_feed",$par,array(id=>$row['id']));
		$chkSyn =	$objDailyFeed->getSyndicationValue($row['id']);
		if($chkSyn == '1')
		{
			$existSynd = getSyndication($row['id'],'18','yahoo');
			if($existSynd && $existSynd['id']!='')
			{
			}
			else
			{
				$feed = $objDailyFeed->getFeedData($row['id']);
				$objYahoo->generateYahooXml($row['id'],$feed,'Feeds','yahoo');
			}
		}
		$objContent = new Content(18,$row['id']);
		$url = $objContent->getDailyFeedURL($row['id']);

		$objContent->updateContentSeoUrl($row['id'],"18",$url);
		$objContent->setDailyFeedMeta();
		if($row['id']){
			$objDailyFeed= new Dailyfeed();
			$objDailyFeed->sendMailContent($row['id']);
			if($row['is_buzzalert']=="1" && $row['is_live']!="1")
			   {
			  $objDailyFeed->synidicateFeed($row['id']);
			   }
		}
		$objAction->trigger('dailyFeedDataUpdate',$row['id']);
}
$objAction->trigger('dailyFeedListUpdate');
}
?>
