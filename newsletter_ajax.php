<?
		include_once("$D_R/lib/email_alert/_lib.php");

		$json = new Services_JSON();
		$email_ids = $_POST['emaillist'];
		$user_sess_id = $_POST['sessuserid'];
		$dailyfeed_email_category = ",7";
		$email_ids = $dailyfeed_email_category.",".$email_ids;

		$arrcategoryids = array(category_ids=>$email_ids);
		update_query("email_alert_categorysubscribe",$arrcategoryids,array('subscriber_id'=>$user_sess_id));

		$errMessage='Email Newsletters Updated.';

		$value=array('status'=>true,'msg'=>$errMessage);

		$output = $json->encode($value);
		echo strip_tags($output);

?>