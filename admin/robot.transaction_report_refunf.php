<?php
set_time_limit(0);
ini_set('memory_limit','256M');
global $D_R,$recurlyApiKey;
$offset = $_GET['offset'];

$sqlGetVIATrans="SELECT * FROM subscription_transaction_via WHERE creation_date >='2011-07-01'
AND creation_date <= '2012-07-31' AND payment_clear_status = '3' AND base_amount<0 ";
$resGetVIATrans=exec_query($sqlGetVIATrans);
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");

$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

foreach($resGetVIATrans as $key=>$transaction)
{
	$sqlGetTransDetails="SELECT SCO.id,subscription_id,viaid,typeSpecificId,price,description,startDate,
	expireDate,billDate,orderStatus
FROM subscription_cust_order_via SCO,subscription_transaction_via T WHERE SCO.viaid=T.customer_id AND
 (DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.billDate,'%Y-%m-%d')
 or DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.startDate,'%Y-%m-%d'))
AND round(SCO.price,2)='".$transaction['base_amount']."' and SCO.viaid=".$transaction['customer_id']." ORDER BY SCO.orderNumber DESC" ;
	$resGetTransDetails=exec_query($sqlGetTransDetails,1);
	if($resGetTransDetails){
			$sql="select fname,lname,email,via_id from subscription where id='".$resGetTransDetails['subscription_id']."'";

           //echo "<br>".$sql;

            $getSubsData=exec_query($sql,1);

            $sqlProd="select subGroup,subType,recurly_plan_period from product where subscription_def_id='".$resGetTransDetails['typeSpecificId']."'";
            //echo "<br>".$sqlProd;
            $getProdDetail=exec_query($sqlProd,1);
        //    htmlprint_r($getProdDetail);
            $arr[$key]['trans_ID']=$transaction['id'];
            $arr[$key]['order_ID']=$resGetTransDetails['id'];
            $arr[$key]['Product']=Ucfirst($getProdDetail['subGroup']);
            if($getProdDetail['subType']==""){
                $term=$getProdDetail['recurly_plan_period'];
            }else{
                $term=$getProdDetail['subType'];
            }



            $arr[$key]['Term']=$term;
            $arr[$key]['Start Date']=date ( 'm/d/Y',strtotime($resGetTransDetails['startDate']));
            $arr[$key]['End Date']=date ( 'm/d/Y',strtotime($resGetTransDetails['expireDate']));

            $arr[$key]['Purchase Price']=$resGetTransDetails['price'];
            $arr[$key]['Account Name']=$getSubsData['fname'].' '.$getSubsData['lname'];
            $arr[$key]['Email']=$getSubsData['email'];
            $arr[$key]['Recurly ID']=$resGetTransDetails['subscription_id'];
            $arr[$key]['VIA ID']=$getSubsData['via_id'];

		try {
			 	$billing_info = Recurly_BillingInfo::get($resGetTransDetails['subscription_id']);
			 	if($billing_info->last_four>0)
			 	{
			 		$arr[$key]['Set To Renew']="Y";
			 		$arr[$key]['Valid CC in Recurly? ']="Y";
			 	}
			 	else
			 	{
			 		$arr[$key]['Set To Renew']="N";
			 		$arr[$key]['Valid CC in Recurly? ']="N";
			 	}

			} catch (Recurly_NotFoundError $e) {
				$arr[$key]['Set To Renew']="N";
				$arr[$key]['Valid CC in Recurly? ']="N";
			}

			//echo "<tr>";
			//echo "<td>".$transaction['customer_id']."</td>";
			//echo "<td>".$resGetTransDetails['typeSpecificId']."</td>";
			//echo "<td>".$resGetTransDetails['price']."</td>";
			//echo "<td>".$resGetTransDetails['description']."</td>";
			//echo "<td>".$resGetTransDetails['startDate']."</td>";
			//echo "<td>".$resGetTransDetails['expireDate']."</td>";
			//echo "<td>".$resGetTransDetails['orderStatus']."</td>";
			//echo "</tr>";

	}else{
	$sqlGetTransDetails_amount="SELECT SCO.id,subscription_id,viaid,typeSpecificId,price,description,startDate,
	expireDate,billDate,orderStatus
FROM subscription_cust_order_via SCO,subscription_transaction_via T WHERE SCO.viaid=T.customer_id AND
 (DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.billDate,'%Y-%m-%d')
 or DATE_FORMAT('".$transaction['creation_date']."','%Y-%m-%d')=DATE_FORMAT(SCO.startDate,'%Y-%m-%d'))
 and SCO.viaid=".$transaction['customer_id']." GROUP BY SCO.orderNumber ORDER BY SCO.orderNumber DESC" ;
	$resGetTransDetails_amount=exec_query($sqlGetTransDetails_amount,1);
	if($resGetTransDetails_amount){
			$sql="select fname,lname,email,via_id from subscription where id='".$resGetTransDetails_amount['subscription_id']."'";

           //echo "<br>".$sql;

            $getSubsData=exec_query($sql,1);

            $sqlProd="select subGroup,subType,recurly_plan_period from product where subscription_def_id='".$resGetTransDetails_amount['typeSpecificId']."'";
            //echo "<br>".$sqlProd;
            $getProdDetail=exec_query($sqlProd,1);
        //    htmlprint_r($getProdDetail);
            $arr[$key]['trans_ID']=$transaction['id'];
            $arr[$key]['order_ID']=$resGetTransDetails_amount['id'];
            $arr[$key]['Product']=Ucfirst($getProdDetail['subGroup']);
            if($getProdDetail['subType']==""){
                $term=$getProdDetail['recurly_plan_period'];
            }else{
                $term=$getProdDetail['subType'];
            }



            $arr[$key]['Term']=$term;
            $arr[$key]['Start Date']=date ( 'm/d/Y',strtotime($resGetTransDetails_amount['startDate']));
            $arr[$key]['End Date']=date ( 'm/d/Y',strtotime($resGetTransDetails_amount['expireDate']));

            $arr[$key]['Purchase Price']=$transaction['base_amount'];
            $arr[$key]['Account Name']=$getSubsData['fname'].' '.$getSubsData['lname'];
            $arr[$key]['Email']=$getSubsData['email'];
            $arr[$key]['Recurly ID']=$resGetTransDetails_amount['subscription_id'];
            $arr[$key]['VIA ID']=$getSubsData['via_id'];

		try {
			 	$billing_info = Recurly_BillingInfo::get($resGetTransDetails_amount['subscription_id']);
			 	if($billing_info->last_four>0)
			 	{
			 		$arr[$key]['Set To Renew']="Y";
			 		$arr[$key]['Valid CC in Recurly? ']="Y";
			 	}
			 	else
			 	{
			 		$arr[$key]['Set To Renew']="N";
			 		$arr[$key]['Valid CC in Recurly? ']="N";
			 	}

			} catch (Recurly_NotFoundError $e) {
				$arr[$key]['Set To Renew']="N";
				$arr[$key]['Valid CC in Recurly? ']="N";
			}

			//echo "<tr>";
			//echo "<td>".$transaction['customer_id']."</td>";
			//echo "<td>".$resGetTransDetails['typeSpecificId']."</td>";
			//echo "<td>".$resGetTransDetails['price']."</td>";
			//echo "<td>".$resGetTransDetails['description']."</td>";
			//echo "<td>".$resGetTransDetails['startDate']."</td>";
			//echo "<td>".$resGetTransDetails['expireDate']."</td>";
			//echo "<td>".$resGetTransDetails['orderStatus']."</td>";
			//echo "</tr>";

	}
	}
}



csv_header("VIA-active-subscription-07/01/2011-07-31-2012.xls");
$datestr="%m/%d/%Y";
data2csv($arr);


?>
