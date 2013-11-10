<?php
global $D_R,$keeneItemMeta;
include_once("$D_R/lib/_content_data_lib.php");
include_once($D_R."/lib/config/_keene_config.php");
require_once($D_R.'/lib/aws-sns/lib/amazonsns.class.php');
require_once($D_R."/lib/config/_aws_config.php");

$objAmazonSNS = new AmazonSNS($snsAccessKeyId, $snsSecretAccessKey);

global $D_R,$keeneTemplate,$keeneFromName,$keeneFromEmail;
if(!empty($_POST)){
	/* Publish Alerts */
	if($_POST['submit_type']== 'save'){
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = $_POST['alert']['title'];
		$params['creation_date'] = $_POST['alert']['date'];
		$params['body'] = $_POST['alert']['body'];
		$params['trade_value'] = $_POST['alert']['trade_value'];
		if($_POST['id']==""){
			$id = insert_query('keene_alerts',$params);
		}else{
			$update_id = update_query('keene_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('keene_alerts',$id);
		$objContent->setKeeneMeta();
		$msg = "Alert has been saved Successfully.";
		$bounceback = 'alert.htm';
		location($bounceback.qsa(array(id=>$id,message=>$msg)));
	}

	if($_POST['submit_type']== 'publish')
	{
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = $_POST['alert']['title'];
		$params['is_approved'] = "1";
		$params['is_live'] = "1";
		$params['creation_date'] = $_POST['alert']['date'];
		$params['publish_date'] = $_POST['alert']['date'];
		$params['body'] = $_POST['alert']['body'];
		$params['trade_value'] = $_POST['alert']['trade_value'];
		if($_POST['id']==""){
			$id = insert_query('keene_alerts',$params);
		}else{
			$update_id = update_query('keene_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('keene_alerts',$id);
		$url=$objContent->getKeeneAlertUrl($id);
		$objContent->updateContentSeoUrl($id,$keeneItemMeta,$url);
		$objContent->setKeeneMeta();
		$qry="select is_sms_sent,is_email_sent,title from keene_alerts where id='".$id."'";
		$sendEmailResult=exec_query($qry,1);
		$sentEmail=$sendEmailResult['is_email_sent'];
		$sentSms=$sendEmailResult['is_sms_sent'];
		if($sentEmail=="0" || $sentEmail==""){
			update_query("keene_alerts",array(is_email_sent=>1),array(id=>$id));
			$from[$keeneFromEmail]= $keeneFromName;
			$subject=trim(stripslashes($sendEmailResult['title']));
			$msgfile="/tmp/spam_keene_".mrand().".eml";
			$msghtmlfile="$D_R/assets/data/".basename($msgfile);
			$msgurl=$keeneTemplate.qsa(array(id=>$id));
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
				$smsBody = $_POST['alert']['title'];
				$tradeValBody = $_POST['alert']['trade_value'];
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
	 				$body = var_dump($e);
                   
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
		
		if(!empty($update_id) || !empty($id)){
			$msg = "Alert is live now.";
		}
		$bounceback = 'approve-alert.htm?viewapproved=1';
		location($bounceback.qsa(array(message=>$msg)));
	}

	/* Delete Alerts */
	if($_POST['submit_type']== 'delete'){
		$params['is_deleted'] = '1';
		update_query('keene_alerts', $params,array(id=>$_POST['id']));
		$bounceback = 'approve-alert.htm?viewapproved=1';
		$msg = "Article has been deleted";
		location($bounceback.qsa(array(id=>$_POST['id'],message=>$msg)));
	}
}
?>