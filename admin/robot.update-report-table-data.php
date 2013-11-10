<?php
global $D_R;
set_time_limit(60*3600);//1 hour
include_once($D_R."/lib/_includes.php");
include_once($D_R."/lib/_db.php");
include_once($D_R."/lib/config/_db_config.php");

$qry="SELECT `id`,`subscription_id`,`recurly_plan_code`,`recurly_uuid`,`recurly_state`,`recurly_quantity`,`recurly_total_amount_in_cents`,`recurly_activated_at`,`recurly_canceled_at`,`recurly_expires_at`,`recurly_current_period_started_at`,`recurly_current_period_ends_at`,`recurly_trial_started_at`,`recurly_trial_ends_at` FROM subscription_cust_report_temp";

$getResult=exec_query($qry);
    if(!empty($getResult)){
        
        foreach($getResult as $value){
        $sql="SELECT id FROM subscription_cust_report WHERE
        subscription_id='".$value['subscription_id']."'
        and recurly_plan_code='".$value['recurly_plan_code']."'
        and recurly_uuid='".$value['recurly_uuid']."'
        and recurly_state='".$value['recurly_state']."'
        and recurly_quantity='".$value['recurly_quantity']."'
        and recurly_total_amount_in_cents='".$value['recurly_total_amount_in_cents']."'
        and recurly_activated_at='".$value['recurly_activated_at']."'
        and recurly_canceled_at='".$value['recurly_canceled_at']."'
        and recurly_expires_at='".$value['recurly_expires_at']."'
        and recurly_expires_at='".$value['recurly_expires_at']."'
        and recurly_current_period_started_at='".$value['recurly_current_period_started_at']."'
        and recurly_current_period_ends_at='".$value['recurly_current_period_ends_at']."'
        and recurly_trial_started_at='".$value['recurly_trial_started_at']."'
        and recurly_trial_ends_at='".$value['recurly_trial_ends_at']."'
        ";
        $getSqlResult=exec_query($sql,1);
        if(empty($getSqlResult['id'])){
        
                                $params['subscription_id']=$value['subscription_id'];
                                $params['recurly_plan_code']=$value['recurly_plan_code'];
                                $params['recurly_uuid']=$value['recurly_uuid'];
                                $params['recurly_state']=$value['recurly_state'];
                                $params['recurly_quantity']=$value['recurly_quantity'];
                                $params['recurly_total_amount_in_cents']=$value['recurly_total_amount_in_cents'];
                                $params['recurly_activated_at']=$value['recurly_activated_at'];
                                $params['recurly_canceled_at']=$value['recurly_canceled_at'];
                                $params['recurly_expires_at']=$value['recurly_expires_at'];
                                $params['recurly_current_period_started_at']=$value['recurly_current_period_started_at']; 
                                $params['recurly_current_period_ends_at']=$value['recurly_current_period_ends_at'];
                                $params['recurly_trial_started_at']=$value['recurly_trial_started_at'];
                                $params['recurly_trial_ends_at']=$value['recurly_trial_ends_at'];
                                $params['notificationType']='updated_from_mv_db';
                                $id=insert_query('subscription_cust_report',$params,$safe=0);
                                echo "<br>".'Record inserted successfuly - '.$id;
        }                    
        
    }

}

?>