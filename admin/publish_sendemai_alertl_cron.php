<?
ini_set('memory_limit', '64M');
global $D_R;
include_once($D_R.'/lib/email_alert/_lib.php');
include_once($D_R.'/admin/lib/_article_data_lib.php');
include_once($D_R.'/lib/config/_article_config.php');
include_once($D_R.'/lib/_action.php');
include_once($D_R."/lib/config/_rsync_config.php");
$objAction= new Action();
global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
$objArticleCache= new Cache();
set_time_limit ( 60*30 );//1 hour

echo "Mail sent articles started at : ".date("Y-m-d h:i")."<BR>\n";

$articles=array();

$qry="SELECT id,approved,publish_date,contrib_id,email_category_ids,title,subsection_ids 
FROM articles WHERE is_live='1' AND sent='0' and approved='1' 
AND IF(publish_date!='0000-00-00 00:00:00',publish_date,date) >= DATE_SUB('".date('Y-m-d',strtotime(mysqlNow()))."', INTERVAL 24 HOUR) ORDER BY id desc";

$db=new dbObj($qry);

while($row=$db->getRow()){

	if ($row[approved]==1)
	{
		echo "Article ID : ".$row[id]." send email alert<br>\n";

		$articles['sent']='1';
		//$uid=update_query("articles",$articles,array(id=>$row[id]));
		send_approved_article_mail($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$row['title'],$SPAM_EML_SUBS_ALERT_TMPL,$row['id'],$row['contrib_id'],$row['email_category_ids'],$row['subsection_ids'],$size_emailalert);
	}
	else
	{
	    echo "No article found to send email alert.";
		
	}
}
?>
