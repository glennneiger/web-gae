<?php
	$objPost=new Post();
	unset($_POST['_']);
	$articlecache= new Cache();
	$msg_id=$_POST[private_msg_id];
	if($msg_id==''){
		$_POST[article]=1;
		$savepage=$objPost->post_comment($_POST);
		echo $savepage;
	}
	else
	{
		unset($_POST[Ptype]);
		unset($_POST[strinbox]);
		$savemessage=$objPost->post_message($_POST);
		if($savemessage){
			$event='NewMessage';
			$subId=$_POST[to_subscription_id];
			$sender_id=$_POST[from_subscription_id];
			$message = $_POST['text'];
			$resEmail=getAlertStatus($event,$subId);
			$emailBody=getAlertBody($event,$sender_id,$subId);
	
			if($event == 'NewMessage') {
				$bodyorder = array("\r\n", "\n", "\r");
				$bodyreplace = '<br />';
				$message_body = str_replace($bodyorder, $bodyreplace, $message);
				$sendEmail=sendAlert($subId,$sender_id,$resEmail,$event,$message_body);
			}
			else
			{
				$sendEmail=sendAlert($subId,$sender_id,$resEmail,$event);
			}
			?><div align="center" class="error" style="padding-bottom:10px;"><?php echo 'The message has been sent'?></div>
		<?php
		} 
	
	}
	$articlecache->deleteDiscussionArticleCache($_POST['thread_id']);
 ?>
