<?php
global $D_R;
set_time_limit(0);
ini_set('memory_limit','256M');

require_once($D_R."/lib/recurly/recurly.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once ($D_R."/lib/recurly/_recurly_data_lib.php");
global $recurlyApiKey;
Recurly_Client::$apiKey = $recurlyApiKey;
$objRecurlyData = new recurlyData();
$offset = $_GET['offset'];

$qry = "select id from subscription limit ".$offset.",5000";
$res = exec_query($qry);
if(empty($res)){
	echo 'No record Found in this range<br>';
}else{
	foreach($res as $key=>$val){
		try{
			$subscriptions = Recurly_SubscriptionList::getForAccount($val['id']);
			foreach ($subscriptions as $subscription) {
				$arr = $objRecurlyData->parseRecurlyXml($subscription);
				if(empty($arr)){
					echo 'No Subscription for subscription id '.$val['id'].'<br>';
				}else{
					$chkDup ="SELECT id FROM subscription_cust_report WHERE subscription_id='".$val['id']."' AND recurly_state='".$arr['state']."' AND recurly_plan_code='".$arr['plan_code']."' AND recurly_uuid='".$arr['uuid']."'";
					$resDup = exec_query($chkDup,1);
					if($resDup['id']==''){
						$params['subscription_id'] = $val['id'];
						$params['recurly_plan_code'] = $arr['plan_code'];
						$params['recurly_uuid'] = $arr['uuid'];
						$params['recurly_state'] = $arr['state'];
						$params['recurly_quantity'] = $arr['quantity'];
						$params['recurly_total_amount_in_cents'] = $arr['unit_amount_in_cents'];
						$params['recurly_activated_at'] = $arr['activated_at'];
						$params['recurly_canceled_at'] = $arr['canceled_at'];
						$params['recurly_expires_at'] = $arr['expires_at'];
						$params['recurly_current_period_started_at'] = $arr['current_period_started_at'];
						$params['recurly_current_period_ends_at'] = $arr['current_period_ends_at'];
						$params['recurly_trial_started_at'] = $arr['trial_started_at'];
						$params['recurly_trial_ends_at'] = $arr['trial_ends_at'];
						$params['notificationType'] = 'updated_from_mv_db';

						$id=insert_query('subscription_cust_report',$params,$safe=0);
					    if(!empty($id)){
					    	echo 'Record insert for subscription id '.$val['id'].'<br>';
					    }else{
					    	echo 'Record Fail for subscription id '.$val['id'].'<br>';
					    }
					}else{
						echo 'Record Already Exists for subscription id '.$val['id'].'<br>';
					}
				}
			}
		}catch (Exception $e){
			echo $e->getMessage();
			echo '<br>';
		}
	}
}
?>