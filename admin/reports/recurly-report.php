<?php
global $D_R;
set_time_limit(0);
ini_set('memory_limit','256M');
global $D_R,$recurlyApiKey;
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");

$offset = $_GET['offset'];

$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

$qry="select id,subscription_id,date,amountInCents from subscription_transaction where date >='2011-07-01'
AND date <= '2012-07-31' and status='success' and action!='refund' limit ".$offset.",100";

$getResult=exec_query($qry);

    foreach($getResult as $key=>$row){
        // htmlprint_r($row);
           $sql="select fname,lname,email,via_id from subscription where id='".$row['subscription_id']."'";

            $getSubsData=exec_query($sql,1);
            $newdate = strtotime ( '-1 day' , strtotime ( $row['date'] ) ) ;
            $newdate = date ( "Y-m-d" , $newdate );
           echo  $sql="select id,recurly_plan_code,recurly_plan_name,recurly_total_amount_in_cents,recurly_canceled_at,
recurly_activated_at,recurly_canceled_at,recurly_expires_at,recurly_current_period_started_at,
recurly_current_period_ends_at,recurly_trial_started_at,recurly_trial_ends_at
 from subscription_cust_order where subscription_id='".$row['subscription_id']."'
and (DATE_FORMAT(recurly_current_period_started_at,'%Y-%m-%d')=DATE_FORMAT('".$row['date']."','%Y-%m-%d')
or DATE_FORMAT(recurly_activated_at,'%Y-%m-%d')=DATE_FORMAT('".$row['date']."','%Y-%m-%d')
or DATE_FORMAT(recurly_activated_at,'%Y-%m-%d')=DATE_FORMAT('".($newdate)."','%Y-%m-%d')
or DATE_FORMAT(recurly_trial_started_at,'%Y-%m-%d')=DATE_FORMAT('".$row['date']."','%Y-%m-%d') )
and recurly_total_amount_in_cents='".$row['amountInCents']."'";

        $getCustResult=exec_query($sql,1);

            $sqlProd="select subGroup,subType,recurly_plan_period from product where recurly_plan_code='".$getCustResult['recurly_plan_code']."'";

            $getProdDetail=exec_query($sqlProd,1);
        //    htmlprint_r($getProdDetail);

            $arr[$key]['Id']=$getCustResult['id'];
            $arr[$key]['Product']=Ucfirst($getProdDetail['subGroup']);
            if($getProdDetail['subType']==""){
                $term=$getProdDetail['recurly_plan_period'];
            }else{
                $term=$getProdDetail['subType'];
            }
            $arr[$key]['term']=$term;
            //echo "<br>".date('m/d/Y',strtotime($getCustResult['recurly_current_period_started_at']));
            if($getCustResult['recurly_current_period_started_at']=="" || $getCustResult['recurly_current_period_started_at']=="0000-00-00 00:00:00")
            {
            	 $arr[$key]['Start Date']=date('m/d/Y',strtotime($row['date']));
            }
            else {
            $arr[$key]['Start Date']=date('m/d/Y',strtotime($getCustResult['recurly_current_period_started_at']));
            }



            if((empty($getCustResult['recurly_canceled_at']) && empty($getCustResult['recurly_expires_at'])) || ($getCustResult['recurly_canceled_at']=="0000-00-00 00:00:00" && $getCustResult['recurly_expires_at']=="0000-00-00 00:00:00")){
                $newdate = "";
                $arr[$key]['End Date']=$newdate;
                $arr[$key]['Set To Renew']="Y";

            }else{
                $newdate=date ( 'm/d/Y',strtotime($getCustResult['recurly_current_period_ends_at']));
                $arr[$key]['End Date']=$newdate;
                $arr[$key]['Set To Renew']="N";
            }


            if($getCustResult['recurly_total_amount_in_cents']=="")
            {
            	 $arr[$key]['Purchase Price']=($row['amountInCents']/100);
            }
            else
            {
            	 $arr[$key]['Purchase Price']=($getCustResult['recurly_total_amount_in_cents']/100);
            }

            $arr[$key]['Account Name']=$getSubsData['fname'].' '.$getSubsData['lname'];
            $arr[$key]['Email']=$getSubsData['email'];
            $arr[$key]['Recurly ID']=$row['subscription_id'];
            $arr[$key]['VIA ID']=$getSubsData['via_id'];

            try {
			 	$billing_info = Recurly_BillingInfo::get($row['subscription_id']);
			 	if($billing_info->last_four>0)
			 	{
			 		$arr[$key]['Valid CC in Recurly? ']="Y";
			 	}
			 	else
			 	{
			 		$arr[$key]['Valid CC in Recurly? ']="N";
			 	}

			} catch (Recurly_NotFoundError $e) {
				$arr[$key]['Valid CC in Recurly? ']="N";
			}
			if($row['id']=="716")
            {
            	htmlprint_r($arr);
            	die;
            }



    }

csv_header("recurly-subscription.csv");
$datestr="%m/%d/%Y";
data2csv($arr);

?>