<?
global $D_R;
ini_set('memory_limit', '64M');
include_once("$D_R/lib/_content_data_lib.php");
include_once($D_R.'/lib/email_alert/_lib.php');
include_once($D_R.'/admin/lib/_article_data_lib.php');
include_once($D_R.'/lib/config/_article_config.php');
include_once($D_R.'/lib/_action.php');
include_once($D_R."/lib/config/_rsync_config.php");
$objAction= new Action();
$objArticleData = new ArticleData();

global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
$objArticleCache= new Cache();
set_time_limit ( 60*30 );//1 hour
$buzzArr = array();

echo "Publishing articles started at : ".date("Y-m-d h:i")."<BR>\n";

$articles=array();
$articles['is_live']='1';


$qry="SELECT id,approved,is_buzzalert,publish_date,contrib_id,email_category_ids,title,subsection_ids 
FROM articles WHERE is_live!='1' AND approved='1' AND publish_date <= '".mysqlNow()."' AND 
publish_date >= '".mysqlNow()."'-INTERVAL 1 MONTH ORDER BY id ";

$db=new dbObj($qry);

while($row=$db->getRow()){

	if ($row[approved]==1)
	{
		echo "Article ID : ".$row[id]." published<br>\n";

		$articles['sent']='0';
		$articles['date']= $row[publish_date];
		$uid=update_query("articles",$articles,array(id=>$row[id]));

		$objArticleCache->setArticleCache($row[id]);
		$obContent = new Content(1,$row[id]);
		$obContent->setArticleMeta();
		if($uid){
			/* Generating Yahoo XML Start */
			$yahooFullBody = $objArticleData->getYahooFullBodySynd($row['id']);
			if($yahooFullBody=="1")
            {                                                                      
            	$objAction->trigger('setYahooCache',$arArticle);
            }
            
			$changeYahoofeedqry = "SELECT * FROM articles where approved='1' and is_live='1' and id=".$row['id'];
			$getChangeForYahoo=exec_query($changeYahoofeedqry,1);
			
			if($getChangeForYahoo['publish_date']!='0000-00-00 00:00:00' && $getChangeForYahoo['publish_date']!=''){
				$getChangeForYahoo['date']=$getChangeForYahoo['publish_date'];
			}
			
			if($getChangeForYahoo['is_yahoofeed']=="1" || $yahooFullBody=="1"){
				echo "<br>Generating Yahoo XML";
				$itemtype="articles";
				if($getChangeForYahoo['is_yahoofeed']=="1")
				{
					$syndchannel="yahoo";
					$chkSyndication = getSyndication($row['id'],'1',$syndchannel);
					if(count($chkSyndication)>0){ $yahoosynd="0"; }
					else { $yahoosynd="1"; }
				}
				else
				{
					$yahoosynd="0";
				}
				
				if($yahoosynd=="1" || $yahooFullBody=="1") {
					$feedFileName=generateYahooXml($getChangeForYahoo,$itemtype,$yahoosynd,$yahooFullBody);
				}

			}
			/* Generating Yahoo XML End */
			/*Generate newsml feed
			 $newsMLqry = "SELECT * FROM articles where approved='1' and is_msnfeed='1' and id=".$row['id'];
			 $getNewsMlData=exec_query($newsMLqry,1);
			 if($getNewsMlData){
				$generateNewsML=generateNewsML($getNewsMlData);
				}*/

		}
		//add Article URL in content_seo_url table
		insert_query("content_seo_url",array("item_id"=>$row['id'],"item_type"=>"1","url"=>makeArticleslink($row['id']),"time"=>mysqlNow()));
		$objAction->trigger('articlePublish',$row[id]);
		$objAction->trigger('articleUpdate',$row[id]);

		if($row[is_buzzalert] == '1') {
			$buzzArr[] = $row[id];
			//spamJournalToUsers($row[id]);
		}
		// call function for mail send to all the subscribed users for email alerts
		//send_approved_article_mail($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$row['title'],$SPAM_EML_SUBS_ALERT_TMPL,$row['id'],$row['contrib_id'],$row['email_category_ids'],$row['subsection_ids'],$size_emailalert);
	}
	else
	{
		echo "Article ID : ".$row[id]." can be published<br>\n";
		$articles['sent']='0';
		update_query("articles",$articles,array(id=>$row[id]));

	}
}

foreach($buzzArr as $key=>$val)
{
	spamJournalToUsers($val);
}
?>
