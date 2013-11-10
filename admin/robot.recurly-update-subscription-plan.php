<?php
/*cron to update recurly plans to MV DB*/
global $D_R;
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");
global $recurlyApiKey; 
Recurly_Client::$apiKey = $recurlyApiKey;

$plans = Recurly_PlanList::get();

foreach ($plans as $plan) {

    $params['plan_name']=$plan->name;
    $params['plan_code']=$plan->plan_code;
    $params['plan_desc']=$plan->description;
    $params['plan_period']=$plan->plan_interval_length.' '.$plan->plan_interval_unit;
    $planCharge=$plan->unit_amount_in_cents['USD']->amount_in_cents;
    if($planCharge!=="0"){
        $planCharge=($planCharge/100);
    }
    
    $params['plan_charge']=$planCharge;  //amount_in_cents
    $planSetupFee=$plan->setup_fee_in_cents['USD']->amount_in_cents;
    if($planSetupFee!=="0"){
        $planSetupFee=($planSetupFee/100);
    }
    
    $params['plan_setup_fee']=$planSetupFee; //setup_fee_in_cents
    
    if($plan->trial_interval_length=="0"){
        $planFreeTrial="No Trial";
    }else{
        $planFreeTrial=$plan->trial_interval_length.' '.$plan->trial_interval_unit;                
    }
    $params['plan_free_trial']=$planFreeTrial;
    $condition['plan_code']=$plan->plan_code;
    $id=insert_or_update('recurly_plans',$params,$condition);
 
}


echo "<br>"."Data updated successfully";



?>