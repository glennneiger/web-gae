<?
		include_once("$D_R/lib/email_alert/_lib.php");
		include_once("$D_R/lib/json.php");
		$json = new Services_JSON();
		$email_ids = $_POST['emaillist'];
		$user_sess_id = $_POST['sessuserid'];
		$dailyfeed_email_category = ",7";
		$email_ids = $dailyfeed_email_category.",".$email_ids;
		$email_alert="1";

		$arrcategoryids = array(category_ids=>$email_ids,email_alert=>$email_alert);
		update_query("email_alert_categorysubscribe",$arrcategoryids,array('subscriber_id'=>$user_sess_id));
		subscribeTopicAlertMailChimp($user_sess_id,'7','1');
		$errMessage='Email Newsletters Updated.';

		$value=array('status'=>true,'msg'=>$errMessage);

		$output = $json->encode($value);
		echo strip_tags($output);

?>