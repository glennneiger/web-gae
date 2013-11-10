<?php
global $D_R;
include_once($D_R."/admin/lib/_article_data_lib.php");
require_once $D_R.'/lib/swift/lib/swift_required.php';
$mailer = Swift_MailTransport::newInstance();
$message = Swift_Message::newInstance();
set_time_limit(3600);
//don't send on weekends
if(in_array(lc(date("D")),qw("sat sun")) ){
	exit();
}

/*============ check first that articles have been written today =====*/

$objArticle = new ArticleData();
$articles = $objArticle->getTodaysArticleData();
if(empty($articles)){
	echo "No posts made today.";
	exit();
}

/*=============== write out mailing list for enabled users only=========================*/
$listemail=$EMAIL_LISTS[gazette];
if(!$is_dev){
	$qry="SELECT `id`,`subject` FROM `daily_digest` WHERE is_sent='1'";
	$result = exec_query($qry,1);
	if(!empty($result))
	{
		$id=$result['id'];
		$daily_digest['sent_on']=date('Y-m-d H:i:s');
		$daily_digest['is_sent']="2";
		$update=update_query("daily_digest",$daily_digest,array("id"=>$id));
		if($update=="1")
		{
			$msgfile="/tmp/".date("mdY")."_digest.eml";
			$msghtmlfile="$D_R/assets/data/".basename($msgfile);

			$NOTIFY_DAILY_JOURNAL_FROM_NAME="Minyanville";
			$NOTIFY_DAILY_JOURNAL_FROM_EMAIL="support@minyanville.com";

			$subject=html_entity_decode($result['subject']);
			$msgurl=$HTPFX.$HTADMINHOST."/admin/email-digest/send_digest.php?id=".$id."&mail=1";
			$from[$NOTIFY_DAILY_JOURNAL_FROM_EMAIL]=$NOTIFY_DAILY_JOURNAL_FROM_NAME;

			$mailbody=inc_web($msgurl);
			$resCampaign = sendDailyDigestMails($msgurl,$subject);
			if($resCampaign!="" && $resCampaign!="-1")
			{
				$to=$NOTIFY_JOURNAL_TO;
				$from=$NOTIFY_JOURNAL_FROM;
				write_file($msghtmlfile,$mailbody);
				write_file($msgfile, mymail2str($to,$from,$subject,$mailbody) );

				$id=$result['id'];
				$daily_digest['sent_on']=date('Y-m-d H:i:s');
				$daily_digest['is_sent']="3";
				$update=update_query("daily_digest",$daily_digest,array("id"=>$id));
			}
		}
	}
}
echo "Done";

?>