<?php
if(!empty($_POST)){
	if($_POST['existingPlans']=='0'){
		$chkDup = "select id from product where recurly_plan_code='".$_POST['planCode']."' and subGroup='".$_POST['product']."'";
		$resDup = exec_query($chkDup,1);
		if($resDup['id']==''){
			$message = addProductRecord($_POST,'insert');
		}else{
			$message = 'Subscription Plan Already Exists.';
		}
	}else {
		$message = addProductRecord($_POST,'update');
	}
}

function addProductRecord($postArray,$action){
	$params['product'] = $postArray['planName'];
	$params['orderItemType'] = "SUBSCRIPTION";
	$params['subType'] = $postArray['planterm'];
	$params['recurly_plan_code'] = $postArray['planCode'];
	$params['subGroup'] = $postArray['product'];
	$params['recurly_plan_desc'] = $postArray['planName'];
	$params['recurly_plan_period'] = $postArray['planPeriod'];
	$params['recurly_plan_charge'] = $postArray['planCharge'];
	$params['recurly_plan_setup_fee'] = $postArray['planSetupFee'];
	$params['recurly_plan_free_trial'] = $postArray['planTrial'];
	$params['recurly_plan_promotional_headline'] = $postArray['recurly_plan_promotional_headline'];
	$params['recurly_plan_promotional_features'] = $postArray['recurly_plan_promotional_features'];
	$params['recurly_plan_promotional_desc'] = $postArray['recurly_plan_promotional_desc'];
	$params['recurly_plan_feature_headline'] = $postArray['recurly_plan_feature_headline'];
	$params['recurly_plan_type'] = $postArray['planType'];
	$params['is_active'] = '1';

	if($action=="insert"){
		$insertId =insert_query("product",$params);
		if($insertId){
			$strUpdate = 'Plan has been added Successfully.';
		}
	}elseif($action=="update"){
		if($_POST['existingPlans']==$_POST['planCode']){
			$updatedId = update_query("product",$params,array(recurly_plan_code=>$postArray['planCode']));
		}else{
			$updatedId = update_query("product",$params,array(recurly_plan_code=>$postArray['existingPlans']));
		}
		if($updatedId){
			$strUpdate = 'Plan has been updated Successfully.';
		}else{
			$strUpdate = 'Nothing to update in Plan.';
		}
	}
	return $strUpdate;
}

$bounceback = './add_subscription_plan.htm';

location($bounceback.qsa(array(product=>$_POST['product'],plan=>$_POST['planCode'],error=>$message)));
?>