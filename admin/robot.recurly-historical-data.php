<?php
global $D_R;
set_time_limit(60*3600);//1 hour
include_once($D_R."/lib/_includes.php");
include_once($D_R."/lib/_db.php");
include_once($D_R."/lib/config/_db_config.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
global $recurlyApiKey;
$limit=$_GET['limit'];
if(empty($limit)){
    $limit=0;
}

$objRecurly= new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;

//$subscriptionId='6615';
//$qry="select distinct(subscription_id) from subscription_cust_order where subscription_id='".$subscriptionId."'";
$qry="select distinct(subscription_id) from subscription_cust_order where subscription_id<>'0' order by id asc limit ".$limit.",1000";

$getResult=exec_query($qry);
if(is_array($getResult)){
    foreach($getResult as $value){
        $subscriptionId=$value['subscription_id'];
        try{
            $transactions = Recurly_TransactionList::getForAccount($subscriptionId);
            foreach ($transactions as $key=>$transaction) {
                //print "Transaction: $transaction\n";
             $getSubscriptionData=$objRecurly->getRecurlyXMLData($transaction->subscription);
                $getSubsUuid=0;
                foreach($getSubscriptionData as $subdata){
                    $getSubsUuid=trim(substr($subdata['subscription'],41));
                }
                if(!empty($getSubsUuid)){
                    $getData=$objRecurly->getRecurlyXMLData($transaction);
                    $invoiceStatus=$getData['status'];
                    $transactionCreatedAt=trim($getData['created_at']);
                    
                    $sql="select id,recurly_state from subscription_cust_report where subscription_id='".$subscriptionId."' and recurly_uuid='".$getSubsUuid."' and ( 
    date_format(recurly_current_period_started_at,'%Y-%m-%d')=date_format('".$transactionCreatedAt."','%Y-%m-%d') or date_format(recurly_canceled_at,'%Y-%m-%d')=date_format('".$transactionCreatedAt."','%Y-%m-%d'))";
   
                  $getSqlResult=exec_query($sql,1);
                  if(empty($getSqlResult['id'])){
                     $subscriptions = Recurly_Subscription::get($getSubsUuid);
                     $getSubsData=$objRecurly->parseRecurlyXml($subscriptions);
                     /*Insert data in subscription_cust_report table*/

// insert if no free trial entry in data base
                        $qryTrial="select A.id from subscription_cust_report A, subscription_cust_report B where A.subscription_id='".$subscriptionId."' 
and A.recurly_state='active' 
and A.recurly_current_period_started_at=B.recurly_trial_started_at 
and A.recurly_activated_at=B.recurly_current_period_started_at 
and A.recurly_plan_code='".$getSubsData['plan_code']."'
and A.recurly_uuid='".$getSubsData['uuid']."'";

$getResultTrial=exec_query($qryTrial,1);
                          if(empty($getResultTrial) && !empty($getSubsData['trial_started_at'])){
                            $params['subscription_id']=$subscriptionId;
                            $params['recurly_plan_code']=$getSubsData['plan_code'];
                            $params['recurly_uuid']=$getSubsData['uuid'];
                            $params['recurly_state']='active';
                            $params['recurly_quantity']=$getSubsData['quantity'];
                            $params['recurly_total_amount_in_cents']=$getSubsData['unit_amount_in_cents'];
                            $params['recurly_activated_at']=$getSubsData['activated_at'];
                            $params['recurly_canceled_at']="";
                            $params['recurly_expires_at']="";
                            $params['recurly_current_period_started_at']=$getSubsData['trial_started_at'];
                            $params['recurly_current_period_ends_at']=$getSubsData['trial_ends_at'];
                            $params['recurly_trial_started_at']=$getSubsData['trial_started_at'];
                            $params['recurly_trial_ends_at']=$getSubsData['trial_ends_at'];
                            $params['notificationType']='updated_from_mv_db';
                            //echo "<br>".'insert in table report --';
                            //htmlprint_r($params);
                            $id=insert_query('subscription_cust_report',$params,$safe=0);
                            echo "<br>"." insert if no free trial entry in database ".$id;
                    }

                     
                    if(trim($invoiceStatus)=="success"){ // insert if status is success and no entry in database
                        $qryPlanCode="select subType from product where recurly_plan_code='".$getSubsData['plan_code']."'";
                        $getResultPlanCode=exec_query($qryPlanCode,1);
                        
                        
                        
                        if(!empty($getResultPlanCode)){
                           $planSubType=$getResultPlanCode['subType'];
                           $currentPeriodEndsAt="";
                           $createdDateTime="";
                           $transactionCreatedAtDate="";
                           switch($planSubType){
                                case 'Monthly Trial':
                                case 'Monthly':
                                        $createdDateTime=substr(trim($transactionCreatedAt),0,-5);
                                        $transactionCreatedAtDate=strtotime(date("Y-m-d H:i:s", strtotime($createdDateTime)) . " +1 month");
                                        $currentPeriodEndsAt=date("Y-m-d H:i:s",$transactionCreatedAtDate);
                                    break;
                                case 'Quaterly Trial':
                                case 'Quaterly':
                                         $createdDateTime=substr(trim($transactionCreatedAt),0,-5);
                                        $transactionCreatedAtDate=strtotime(date("Y-m-d H:i:s", strtotime($createdDateTime)) . " +3 month");
                                        $currentPeriodEndsAt=date("Y-m-d H:i:s",$transactionCreatedAtDate);
                                    break;
                                case 'Annual Trial':
                                case 'Annual':
                                       $createdDateTime=substr(trim($transactionCreatedAt),0,-5);
                                        $transactionCreatedAtDate=strtotime(date("Y-m-d H:i:s", strtotime($createdDateTime)) . " +1 Year");
                                        $currentPeriodEndsAt=date("Y-m-d H:i:s",$transactionCreatedAtDate);
                                    break;
                           }
                           
                            
                        }
                        
                        $qryprod="select id from subscription_cust_report where 
subscription_id='".$subscriptionId."' and recurly_uuid='".$getSubsData['uuid']."' and
date_format(recurly_current_period_ends_at,'%Y-%m-%d') > date_format('".$createdDateTime."','%Y-%m-%d') 
and date_format(recurly_current_period_ends_at,'%Y-%m-%d') < date_format('".$currentPeriodEndsAt."','%Y-%m-%d')";

                         $getResultProd=exec_query($qryprod,1);
                         if(empty($getResultProd['id'])){
                        
                            $params['subscription_id']=$subscriptionId;
                            $params['recurly_plan_code']=$getSubsData['plan_code'];
                            $params['recurly_uuid']=$getSubsData['uuid'];
                            $params['recurly_state']='active';
                            $params['recurly_quantity']=$getSubsData['quantity'];
                            $params['recurly_total_amount_in_cents']=$getSubsData['unit_amount_in_cents'];
                            $params['recurly_activated_at']=$getSubsData['activated_at'];
                            $params['recurly_canceled_at']="";
                            $params['recurly_expires_at']="";
                            $params['recurly_current_period_started_at']=$transactionCreatedAt;
                            $params['recurly_current_period_ends_at']=$currentPeriodEndsAt;
                            $params['recurly_trial_started_at']=$getSubsData['trial_started_at'];
                            $params['recurly_trial_ends_at']=$getSubsData['trial_ends_at'];
                            $params['notificationType']='updated_from_mv_db';
                           // echo "<br>".'insert in table report --';
                            $id=insert_query('subscription_cust_report',$params,$safe=0);
                            echo "<br>"."Insert if status is success and no entry in database ".$id;
                         }
                    }elseif(trim($invoiceStatus)=="declined"){ // insert if status is declined and no entry in database  declined
                        
                        //echo "<br>".'recurly_state---'.$getSqlResult['recurly_state'];
                        
                        if(trim($getSqlResult['recurly_state'])!=="expired"){
                            $params['subscription_id']=$subscriptionId;
                            $params['recurly_plan_code']=$getSubsData['plan_code'];
                            $params['recurly_uuid']=$getSubsData['uuid'];
                            $params['recurly_state']=$getSubsData['state'];
                            $params['recurly_quantity']=$getSubsData['quantity'];
                            $params['recurly_total_amount_in_cents']=$getSubsData['unit_amount_in_cents'];
                            $params['recurly_activated_at']=$getSubsData['activated_at'];
                            $params['recurly_canceled_at']=$getSubsData['canceled_at'];
                            $params['recurly_expires_at']=$getSubsData['expires_at'];
                            $params['recurly_current_period_started_at']=$getSubsData['current_period_started_at']; 
                            $params['recurly_current_period_ends_at']=$getSubsData['current_period_ends_at'];
                            $params['recurly_trial_started_at']=$getSubsData['trial_started_at'];
                            $params['recurly_trial_ends_at']=$getSubsData['trial_ends_at'];
                            $params['notificationType']='updated_from_mv_db';
                            $id=insert_query('subscription_cust_report',$params,$safe=0);
                            echo "<br>"."Insert if status is declined and no entry in database ".$id;
                        
                        }
                        
                        
                     
                    }
                    
                     
                  }
                }
            }    
        }catch(Exception $e){
            htmlprint_r($e);
        }
        
    }    
  
}

?>