<?php

include_once("$D_R/admin/lib/_admin_data_lib.php");
include_once("$D_R/lib/_content_data_lib.php");
include_once("$D_R/admin/lib/_contributor_class.php");
include_once("$D_R/admin/lib/_yahoo_lib.php");
include_once("$D_R/lib/config/_yahoobuzz_config.php");
include_once($D_R.'/lib/_action.php');
$objAction= new Action();
$objYahoo = new YahooSyndication();
$objBuzz = new Buzz();

set_time_limit ( 60*30 );//1 hour

echo "Publishing posts started at : ".date("Y-m-d h:i")."<BR>\n";

$banter=array();
$banter['is_live']='1';


$qry="SELECT id,approved,publish_date FROM buzzbanter WHERE is_live!='1' AND publish_date <= '".mysqlNow()."' ORDER BY id ";

$db=new dbObj($qry);

while($row=$db->getRow()){

	if ($row[approved]==1)
	{
		$banter['date']= $row[publish_date];
		echo "Post ID : ".$row[id]." published<br>\n";
		update_query("buzzbanter",$banter,array(id=>$row[id]));
		updateBuzzTodayTable($row[id]);
		writeLatestBanterPostJSON();
		$obContent = new Content(2,$row[id]);
		$obContent->setBuzzMeta();
		/*syndicate to content API*/
		$objAction->trigger('buzzDataPublish',$row['id']);
	}
	else
	{
	    echo "Post ID : ".$row[id]." can be published<br>\n";
		update_query("buzzbanter",$banter,array(id=>$row[id]));
		updateBuzzTodayTable($row[id]);

	}
	
	/*start Yahoo syndication if buzz is approved*/
	$objBuzz->sendBuzzYahooSyndication($row[id]);


}
?>
