<?
include_once("$D_R/lib/config/_housingmarket_config.php");
include_once("../lib/_includes.php");
include_once("../lib/_db.php");
include_once("../lib/MemCache.php");
include_once("../lib/_content_data_lib.php");
include_once("../lib/config/_article_config.php");
include_once("$D_R/lib/housingreport/_housingreport_data.php");
$objHousingReport= new housingReportData("housingreport_articles");
set_time_limit ( 60*30 );//1 hour
echo "Publishing Article started at : ".date("Y-m-d h:i")."<BR>\n";
$qry="SELECT id,publish_date,title FROM housingreport_articles WHERE approved='1' AND is_live='0' AND is_deleted='0' AND publish_date <= '".mysqlNow()."' ORDER BY id DESC";
$result=exec_query($qry);
	if($result){
		foreach($result as $row){
			$par['is_live']='1';
			update_query("housingreport_articles",$par,array(id=>$row['id']));
			
	
			$objContent = new Content("housingreport_articles",$row['id']);
			$urltitle=$objContent->getFirstFiveWords($row['title']);
			$url="/housing-market-report/".$urltitle.'/';
			// $objContent->updateContentSeoUrl($row['id'],"23",$url);
			$objHousingReport->updateHMContentSeo($contentType="housingreport_articles",$row['id'],$item_type,$url);
			$objContent->setHousingreportMeta();
			if($row['id']){
							   global $housingmarket_from, $housingmarket_alert_tmpl;
								$from= $housingmarket_from;
								$subject=htmlentities($objHousingReport->getArticleTitleHousingReport($keys),ENT_QUOTES);
								$msgfile="/tmp/spam_".mrand().".eml";
								$msghtmlfile="$D_R/assets/data/".basename($msgfile);
								$msgurl=$housingmarket_alert_tmpl.qsa(array(aid=>$row['id']));
								$mailbody=inc_web($msgurl);
								include_once("$D_R/lib/_user_controller_lib.php");
								$userObj=new user();
								$result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'housingmarket');
			}
		}
	}

?>
