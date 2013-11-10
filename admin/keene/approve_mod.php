<?php
session_start();
global $D_R,$keeneItemMeta;
include_once("$D_R/lib/_content_data_lib.php");
include_once($D_R."/lib/config/_keene_config.php");
include($D_R."/lib/keene/_keene_data_lib.php");
require_once($D_R.'/lib/aws-sns/lib/amazonsns.class.php');
require_once($D_R."/lib/config/_aws_config.php");

$objAmazonSNS = new AmazonSNS($snsAccessKeyId, $snsSecretAccessKey);
global $D_R,$keeneTemplate,$keeneFromName,$keeneFromEmail;

$bounceback="approve-alert.htm".qsa(array(viewapproved=>$viewapproved,error=>"-"));

$objKeeneData = new keeneData("keene_alerts","");
$objremove= new Content("keene_alerts","");
$alertTable="keene_alerts";
$viewapproved =	trim($_REQUEST['viewapproved']);


$approve = $_REQUEST['approve'];
$publishdatefield = $_REQUEST['publishdatefield'];
$delarticle	= $_REQUEST['delarticle'];
if(!count($approve) && !count($delarticle)){
	location($bounceback.urlencode("No changes were made"));
	exit;
}
if(is_array($delarticle)){
	$keys = implode(",",array_keys($delarticle));
	$qry="update keene_alerts set is_deleted='1' WHERE find_in_set(id,'$keys') LIMIT ".count($delarticle);
	exec_query($qry);
	$qryDel="update ex_item_meta set is_live='0' WHERE find_in_set(item_id,'$keys') and item_type='29' ";
	exec_query($qryDel);
}
if(is_array($approve)){
	foreach($approve as $article_id => $status)
	{
		if($viewapproved!="1"){
			$publish_date_stamp=time($publishdatefield['hour'][$article_id],$publishdatefield['min'][$article_id],0, $publishdatefield['mo'][$article_id],$publishdatefield['day'][$article_id],$publishdatefield['year'][$article_id]);
			if($publish_date_stamp)
			{
				$publish_date=mysqlNow($publish_date_stamp);
				if($publish_date_stamp > time())
				{
					$is_live = 0;
				}
				else
				{
					$is_live = 1;
				}
			}
			else
			{
				$publish_date = '0000-00-00 00:00:00';
				$is_live = 1;
			}
			$qry = "UPDATE keene_alerts SET is_approved='".($viewapproved?0:1)."' ,publish_date = '".$publish_date."' , is_live='".$is_live."'";
		}
		else {
			$qry = "UPDATE keene_alerts SET is_approved='".($viewapproved?0:1)."' , is_live='".$is_live."'";
		}


		//echo $viewapproved;
		//exit();
		if(!$viewapproved){
			$qry.=", creation_date='".mysqlNow()."' WHERE id='".$article_id."' AND is_approved='0' LIMIT ".count($approve);
		}


		if($viewapproved=='1'){
			$qry.=", creation_date='".mysqlNow()."' WHERE id='".$article_id."' AND is_approved='1' LIMIT ".count($approve);
		}
		exec_query($qry);

		/* set data in meta table and content seo*/
		if($is_live){
			$objContent = new Content('keene_alerts',$article_id);
			$url=$objContent->getKeeneAlertUrl($article_id);
			$objContent->updateContentSeoUrl($article_id,$objKeeneData->contentType,$url);
			$objContent->setKeeneMeta();

			/*Start sending email to subscriber using Via*/

			$qry="select is_email_sent,is_sms_sent,title,trade_value from keene_alerts where id='".$article_id."'";
			$sendEmailResult=exec_query($qry,1);
			$sentEmail=$sendEmailResult['is_email_sent'];
			$sentSms=$sendEmailResult['is_sms_sent'];
			if($sentEmail=="0" || $sentEmail==""){
				update_query("keene_alerts",array(is_email_sent=>1),array(id=>$id));
				$from[$keeneFromEmail]= $keeneFromName;
				$subject=trim(stripslashes($sendEmailResult['title']));
				$msgfile="/tmp/spam_keene_".mrand().".eml";
				$msghtmlfile="$D_R/assets/data/".basename($msgfile);
				$msgurl=$keeneTemplate.qsa(array(id=>$article_id));
				$mailbody=inc_web($msgurl);
				include_once("$D_R/lib/_user_controller_lib.php");
				$userObj=new user();
				$result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'keene');
				if($result=="1")
				{
				    update_query("keene_alerts",array(is_email_sent=>2),array(id=>$id));
				}
				$error="Posts were changed and an email was sent to subscribers.";
			}
		//send SMS
		if($sentSms=="0" || $sentSms==""){
			$subscriberList = $objAmazonSNS->listSubscriptionsByTopic($topicName);
			if(!empty($subscriberList)){
				$smsBody = $sendEmailResult['title'];
				$tradeValBody = $sendEmailResult['trade_value'];
				try{
					$messageId = $objAmazonSNS->publish($topicName, $smsBody);
					if($tradeValBody!=''){
						$tradeMessId = $objAmazonSNS->publish($topicName, $tradeValBody);
					}
					update_query("keene_alerts",array(is_sms_sent=>1),array(id=>$id));
					$msgHistory['alertId'] = $id;
					$msgHistory['itemType'] = $keeneItemMeta;
					$msgHistory['smsBody'] = $smsBody;
					$msgHistory['messageId'] = $messageId;
					$msgHistory['tradeSmsBody'] = $tradeValBody;
					$msgHistory['tradeSmsId'] = $tradeMessId;
					$idSms = insert_query('sms_history',$msgHistory);
				}catch(Exception $e){
					$from= 'it@minyanville.com';
					$to = 'anshul.budhiraja@mediaagility.co.in';
					$subject = 'Keene SMS Not Sent For '.$id;
	 				$body = var_dump($e);;
                    mymail($to,$from,$subject,$body);
					/*require_once $D_R.'/lib/swift/lib/swift_required.php';
					$mailer = Swift_MailTransport::newInstance();
					$message = Swift_Message::newInstance();
					$message->setSubject($subject);
					$message->setBody($body);
      	 			$message->setFrom($from);
      	 			$message->setTo($to);
      	 			$mailer->send($message);*/
				}
			}
		}
		}
    }
}
if(count($delarticle))
{
	location($bounceback.urlencode("The Posts were deleted"));
exit();
}
if(!$viewapproved)
{
	location($bounceback.urlencode("The Posts were changed"));
}
else
{
	location($bounceback.urlencode("The posts were changed and are now not live on the site"));

}
exit;
?>
