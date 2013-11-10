<?php
global $D_R,$HTPFX,$HTHOST;
$csv = $D_R."/Buzzexpired6.27.csv";
$product="buzz";
$qryGetProductSubDefId="SELECT subscription_def_id FROM product WHERE subGroup='".$product."' AND subscription_def_id IS NOT NULL";
$resGetProductSubDefId=exec_query($qryGetProductSubDefId);
foreach($resGetProductSubDefId as $key=>$val)
{
        $productSubDefIdArr[] = $val['subscription_def_id'];
}
$subDefIds=implode("','",$productSubDefIdArr);
$qryGetProductRecurlyPlanCode="SELECT recurly_plan_code FROM product WHERE subGroup='".$product."'";
$resGetProductRecurlyPlanCode=exec_query($qryGetProductRecurlyPlanCode);
foreach($resGetProductRecurlyPlanCode as $key1=>$val1)
{
        $productRecurlyPlanCodeArr[] = $val1['recurly_plan_code'];
}
$planCodes=implode("','",$productRecurlyPlanCodeArr);
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
    $batch[] = $val['email'];
}
$row="0";
if (($handle = fopen($csv, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($row>="1")
        {
            if(in_array($data[1],$batch))
            {
                $data[11]="Active";
            }
            else
            {
                $data[11]="Expired";
            }
        }
        $userData[] = $data;
        $row++;
    }
    fclose($handle);
}
$fileName = 'Buzz expired 6.27-Updated.csv';

	csv_header($fileName,"text/csv");
	$datestr="%m/%d/%Y";
	data2csv($userData);

?>