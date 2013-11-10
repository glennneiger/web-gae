<?php
global $HTPFX,$HTHOST,$D_R;
include_once($D_R."/lib/config/_email_config.php");
include_once($D_R."/lib/_user_data_lib.php");
class tr3scoData{

	function __construct(){
	}

	public function getBuzzInfo($id)
	{
		$objTr3sco= new tr3scoData();
		$userInfoArr = array();
		$qry="SELECT 'buzz' AS subGroup,IF(DATE_FORMAT(sco.recurly_trial_ends_at,'%Y/%m/%d')>=DATE_FORMAT(NOW(),'%Y/%m/%d') &&
sco.recurly_state<>'expired','1','0') AS inFreeTrial ,
IF(DATE_FORMAT(sco.recurly_trial_started_at,'%Y/%m/%d')='0000/00/00','0','1') AS freeTrial,
IF((sco.recurly_state<>'expired' &&
 sco.recurly_current_period_ends_at >= DATE_FORMAT(NOW(),'%Y-%m-%d')) ||
 ((expireDate >= DATE_FORMAT(NOW(),'%Y-%m-%d') || expireDate='0000-00-00 00:00:00') &&
  orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')),'1','0') AS state
 FROM subscription_cust_order sco
 LEFT JOIN product p ON p.recurly_plan_code=sco.`recurly_plan_code` AND p.is_active='1'
 WHERE
 (p.subGroup='buzz' OR sco.typeSpecificId=16)
  AND `subscription_id`='".$id."'
  GROUP BY subGroup,sco.typeSpecificId,state,
  sco.recurly_current_period_ends_at ORDER BY state DESC ";
		$userProductInfoArr=exec_query($qry,1);
		if(!empty($userProductInfoArr))
		{
			$productInfo[$userProductInfoArr['subGroup']]['status']=$userProductInfoArr['state'];
			if($userProductInfoArr['state']=="0")
			{
				$productInfo[$userProductInfoArr['subGroup']]['status']="0";
				if($userProductInfoArr['inFreeTrial']=="0" && $userProductInfoArr['freeTrial']=="1")
				{
					$productInfo[$userProductInfoArr['subGroup']]['message']="Your free trial has expired. Please email support@minyanville.com, or call 888-489-4880 to continue using Buzz & Banter.";
				}
				else
				{
					$productInfo[$userProductInfoArr['subGroup']]['message']="Your subscription has expired. Please email support@minyanville.com, or call 888-489-4880 to continue using Buzz & Banter.";
				}
			}
			else {
				$productInfo['buzz']['status']='1';
                $productInfo['buzz']['message']="Login was successful";
            }
		}
		else
		{
			$addPlan = $objTr3sco->addSubscription($id);
	    	if($addPlan>0)
	    	{
	    		$productInfo['buzz']['status']='1';
                $productInfo['buzz']['message']="Login was successful";
	    	}
	    	else
	    	{
	    		$productInfo['buzz']['status']='0';
                $productInfo['buzz']['message']="Login was successful. But the trial could not be added";
	    	}
		}

		return $productInfo;
	}

	function addSubscription($subscription_id)
	{
		$recurly_plan_code = "buzz_60days_freenoar_nft_via_10042013";
		$qryChkPlan = "SELECT *  FROM `subscription_cust_order` WHERE `subscription_id`='".$subscription_id."'    AND recurly_plan_code='".$recurly_plan_code."' AND  (((expireDate >= DATE_FORMAT(now(),'%Y-%m-%d') OR expireDate='0000-00-00 00:00:00') AND  orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))
  OR (recurly_state<>'expired' and recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d')))";
		$resChkPlan = exec_query($qryChkPlan,1);
		if(empty($resChkPlan))
		{
			$qryChkPlanCode = "select * from product where recurly_plan_code='".$recurly_plan_code."'";
			$resChkPlanCode = exec_query($qryChkPlanCode,1);
			if($resChkPlanCode['id']!=''){
				$paramsCust['subscription_id'] = $subscription_id;
				$paramsCust['recurly_plan_code'] = $resChkPlanCode['recurly_plan_code'];
				$paramsCust['recurly_plan_name'] = $resChkPlanCode['product'];
				$paramsCust['recurly_uuid'] = $resChkPlanCode['recurly_uuid'];
				$paramsCust['orderNumber'] = $resChkPlanCode['recurly_uuid'];
				$paramsCust['recurly_state'] = "Active";
				$paramsCust['recurly_quantity'] = "1";
				$paramsCust['recurly_total_amount_in_cents'] = $resChkPlanCode['recurly_total_amount_in_cents'];
				$endDate = date("Y-m-d H:m:s",strtotime("+2 month",strtotime(mysqlNow())));
				$paramsCust['recurly_activated_at'] =mysqlNow();
				$paramsCust['recurly_current_period_started_at'] =mysqlNow();
				$paramsCust['recurly_current_period_ends_at'] =$endDate;
				$paramsCust['recurly_trial_started_at'] =mysqlNow();
				$paramsCust['recurly_trial_ends_at'] =$endDate;
				$insertId = insert_query("subscription_cust_order",$paramsCust);
				if($insertId){
					$status="1";

					$qry = "select email,fname,lname,password from subscription where id='".$subscription_id."'";
					$res = exec_query($qry,1);

					global $HTPFX,$HTHOST,$D_R;

				   	$email=$res['email'];
				   	$objUserData = new userData();
				   	$password = $objUserData->decryptUserPassword($res['password']);
				   	$from['subscriptions@minyanville.com']="Minyanville";
			   		$subject="Your Minyanville Account Password";
			   					$msgurl=$HTPFX."minyanville:fE8Gnnhn3TI8L4f@".$HTHOST."/emails/_eml_new_pass.htm?email=".$email."&password=".$password;
				  	$mailbody=inc_web($msgurl);
				  	 mymail($email,$from,$subject,$mailbody);
			 		/*require_once $D_R.'/lib/swift/lib/swift_required.php';
					$mailer = Swift_MailTransport::newInstance();
					$message = Swift_Message::newInstance();
					$message->setSubject($subject);
					$message->setBody($mailbody, 'text/html');
					$message->setSender($from);
					$message->setTo($email);
					$mailer->send($message);*/
				}
			}
		}else{
			$status="0";
		}
		return $status;
	}

}?>