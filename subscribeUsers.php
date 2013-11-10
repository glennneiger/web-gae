<?php
global $D_R,$mailChimpApiKey,$productListId;
require_once($D_R."/lib/config/_mailchimp_config.php");
require_once($D_R."/lib/mailchipapi/MCAPI.class.php");
require_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");

$product = $_GET['product'];  // Get the product

/**********Get subscription_def_id of the product **********/
$qryGetProductSubDefId="SELECT subscription_def_id FROM product WHERE subGroup='".$product."' AND subscription_def_id IS NOT NULL";
$resGetProductSubDefId=exec_query($qryGetProductSubDefId);
foreach($resGetProductSubDefId as $key=>$val)
{
        $productSubDefIdArr[] = $val['subscription_def_id'];
}
$subDefIds=implode("','",$productSubDefIdArr);

/**********Get plan code of the product **********/
$qryGetProductRecurlyPlanCode="SELECT recurly_plan_code FROM product WHERE subGroup='".$product."'";
$resGetProductRecurlyPlanCode=exec_query($qryGetProductRecurlyPlanCode);
foreach($resGetProductRecurlyPlanCode as $key1=>$val1)
{
        $productRecurlyPlanCodeArr[] = $val1['recurly_plan_code'];
}
$planCodes=implode("','",$productRecurlyPlanCodeArr);

/**********Get subscribed users of the product **********/
$qry= "SELECT S.`email`,S.`fname`,S.`lname` FROM
`subscription_cust_order` SCO , `subscription` S
WHERE
SCO.recurly_state<>'expired' AND SCO.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d') AND SCO.recurly_plan_code
IN('".$planCodes."') AND SCO.subscription_id=S.id
UNION
SELECT S.`email`,S.`fname`,S.`lname` FROM
`subscription_cust_order` SCO , `subscription` S
WHERE
SCO.subscription_id=S.id AND SCO.typeSpecificId IN
('".$subDefIds."') AND ((SCO.expireDate >= DATE_FORMAT(NOW(),'%Y-%m-%d') OR SCO.expireDate='0000-00-00 00:00:00')
 AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'))";

$getResult=exec_query($qry,0);
foreach($getResult as $key=>$val)
{
    $email_listArr[$val['email']] = $val['fname']." ".$val['lname'];
    $batch[] = array('EMAIL'=>$val['email'], 'FNAME'=>$val['fname'],'LNAME'=>$val['lname']);
}

htmlprint_r($batch);

$objApi = new MCAPI($mailChimpApiKey);

/********** Subscribe batch of users in the list **********/
$optIn = false; //yes, send optin emails
$upExist = true; // yes, update currently subscribed users
$replaceInt = false; // no, add interest, don't replace
$res = $objApi->listBatchSubscribe($productListId[$product],$batch,$optIn, $upExist, $replaceInt);
htmlprint_r($res);



?>