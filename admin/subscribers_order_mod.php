<?php
global $D_R;
include_once($D_R."/lib/config/_email_config.php");
if(!empty($_POST)){
	$qryChkPlanCode = "select id, recurly_plan_desc from product where recurly_plan_code='".$_POST['recurly_plan_code']."'";
	$resChkPlanCode = exec_query($qryChkPlanCode,1);
	if($resChkPlanCode['id']!=''){
		if(strtolower($resChkPlanCode['recurly_plan_desc'])==strtolower($_POST['recurly_plan_name'])){
			$paramsCust['subscription_id'] = $_POST['subscription_id'];
			$paramsCust['recurly_plan_code'] = $_POST['recurly_plan_code'];
			$paramsCust['recurly_plan_name'] = $_POST['recurly_plan_name'];
			$paramsCust['recurly_uuid'] = $_POST['recurly_uuid'];
			$paramsCust['orderNumber'] = $_POST['recurly_uuid'];
			$paramsCust['recurly_state'] = $_POST['recurly_state'];
			$paramsCust['recurly_quantity'] = $_POST['recurly_quantity'];
			$paramsCust['recurly_total_amount_in_cents'] = $_POST['recurly_total_amount_in_cents'];
			$paramsCust['recurly_activated_at'] = $_POST['recurly_activated_at'];
			$paramsCust['recurly_current_period_started_at'] = $_POST['recurly_current_period_started_at'];
			$paramsCust['recurly_current_period_ends_at'] = $_POST['recurly_current_period_ends_at'];
			$paramsCust['recurly_trial_started_at'] = $_POST['recurly_trial_started_at'];
			$paramsCust['recurly_trial_ends_at'] = $_POST['recurly_trial_ends_at'];
			$insertId = insert_query("subscription_cust_order",$paramsCust);
			if($insertId){
				$msg = 'Product has been Added Successfully.';

				$qry = "select email,fname,lname from subscription where id='".$_POST['subscription_id']."'";
				$res = exec_query($qry,1);

				$qryProd = "select subGroup from product where recurly_plan_code='".$_POST['recurly_plan_code']."'";
				$resProd = exec_query($qryProd,1);

				/*global $addProductFromAdminSubject,$addProductFromAdminEmail,$addProductFromAdminName;

				$user_email = $res['email'];
			   	$subject= $addProductFromAdminSubject;
			   	$from[$addProductFromAdminEmail]=$addProductFromAdminName;
			   	$userName = $res['fname']." ".$res['lname'];

			  	$welcomeTmpl = $HTPFX.$HTHOST."/emails/_eml_welcomeProductFromAdmin.htm";
		 		$msgurl=$welcomeTmpl.qsa(array(user_name=>$userName,product=>$resProd['subGroup']));
			  	$mailbody=inc_web($msgurl);

		 		require_once $D_R.'/lib/swift/lib/swift_required.php';
				$mailer = Swift_MailTransport::newInstance();
				$message = Swift_Message::newInstance();
				$message->setSubject($subject);
				$message->setBody($mailbody, 'text/html');
				$message->setSender($from);
				$message->setTo($user_email);
				$mailer->send($message);*/
			}
		}else{
			$msg = 'Plan Name is not similar as recurly';
		}
	}else{
		$msg = 'Either you have entered an incorrect Plan Code or Plan Code does not Exists.';
	}
}

$bounceback = './subscribers.order.htm';

location($bounceback.qsa(array(id=>$_POST['subscription_id'],error=>$msg)));
?>