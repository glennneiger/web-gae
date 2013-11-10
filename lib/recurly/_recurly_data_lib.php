<?php
global $D_R;
include_once ($D_R."/lib/_user_controller_lib.php");
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
include_once ($D_R."/lib/_user_data_lib.php");
include_once($D_R."/lib/_user_controller_lib.php");
class recurlyData{

    function getAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId){
        try {
            $account = Recurly_Account::get($subId);
                $result = $this->updateAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId);
          } catch (Recurly_NotFoundError $e) {
                $result = $this->createAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId);
          }
		return $result;
    }

    function createAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId){
        /*$subId is the account code for recurly*/
        try{
            $account = new Recurly_Account($subId);
            $account->username = $userName;
            $account->email = $email;
            $account->first_name = ucfirst(strtolower($firstName));
            $account->last_name = ucfirst(strtolower($lastName));
            $account->company_name = $companyName;
            $account->create();
            $success='1';
        }catch(Exception $e){
        	$success='0';
            $this->catchRecurlyError($subId,$viaId,$email,$e);
        }

		return $success;
    }

    function updateAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId){
        try{
            $account = new Recurly_Account($subId);
            $account->username = $userName;
            $account->email = $email;
            $account->first_name = ucfirst(strtolower($firstName));
            $account->last_name = ucfirst(strtolower($lastName));
            $account->company_name = $companyName;
            $account->update();
            $success='1';
        }catch(Exception $e){
        	$success='0';
            $this->catchRecurlyError($subId,$viaId,$email,$e);
        }


    }
/*once all data update to recurly discard this function*/
    function updateBillingInfo($subId,$firstName,$lastName,$address1,$address2,$city,$state,$country,$zip,$phone,$ccNumber,$cVV,$ccMonth,$ccYear,$viaId,$email){
        try{
            $billing_info = new Recurly_BillingInfo();
            $billing_info->account_code = $subId;
            $billing_info->first_name = $firstName;
            $billing_info->last_name = $lastName;
            $billing_info->address1 = $address1;
            $billing_info->address2 = $address2;
            $billing_info->city = $city;
            $billing_info->state = $state;
            $billing_info->country = $country;
            $billing_info->zip = $zip;
            $billing_info->phone = $phone;
            $billing_info->number = $ccNumber;
            /*$billing_info->verification_value = $cVV;*/
            /*$currentYear=date("Y",mktime());
            $currentMonth=date("m",mktime());*/

            $billing_info->year = $ccYear;
            $billing_info->month = $ccMonth;
            $billing_info->update();
            $params['updated']=1;
            $conditions['viaid']=$viaId;
             update_query('subscription_cust_address',$params,$conditions,$keynames=array());
        }catch(Exception $e){
            $this->catchRecurlyError($subId,$viaId,$email,$e);
        }
    }

      function setBillingInfoToRecurly($subId,$firstName,$lastName,$address1,$address2,$city,$state,$country,$zip,$phone,$ccNumber,$cVV,$ccMonth,$ccYear,$viaId,$email){
    	global $recurlyErrorArray;
        try{
            $billing_info = new Recurly_BillingInfo();
            $billing_info->account_code = $subId;
            $billing_info->first_name = $firstName;
            $billing_info->last_name = $lastName;
            $billing_info->address1 = $address1;
            $billing_info->address2 = $address2;
            $billing_info->city = $city;
            $billing_info->state = $state;
            $billing_info->country = $country;
            $billing_info->zip = $zip;
            $billing_info->phone = $phone;
	        if(preg_match("/^[0-9]+$/",$ccNumber)){
	   			$billing_info->number = $ccNumber;
				$billing_info->year = $ccYear;
            	$billing_info->month = $ccMonth;
			}else{

			}
            try{
            	$billing_info->update();
            	$success['status']='1';
            	return $success;
            }catch(Exception $e){
            	$str = $e->getMessage();
            	$success['status']='0';
				if(array_key_exists($str,$recurlyErrorArray)){
					$str = $recurlyErrorArray[$str];
				}
            	$success['msg']= $str;
            	$this->catchRecurlyError($subId,$viaId,$email,$e);
            	return $success;
        	}
        }catch(Exception $e){
            $this->catchRecurlyError($subId,$viaId,$email,$e);
            $success['status']='0';
            $success['msg']= $str;
            return $success;
        }
    }


    function addSubscriptionToAccount($subId,$planCode,$qty,$couponCode,$requestType){
	    session_start();
            $subscription = new Recurly_Subscription();
            $subscription->plan_code = $planCode;
            $subscription->coupon_code = $couponCode;
            $subscription->currency = 'USD';
            $account = new Recurly_Account();
            $account->account_code = $subId;
            $subscription->account = $account;
            try{
            	$subscription->create();
            	$subscribedOrderDetails = $this->getOrderDetailsOfAccount($subId);
            	foreach($subscribedOrderDetails as $subRow){
            		$prodState = strtolower($subRow->state);
	            	if($subRow->plan->plan_code==$planCode && $prodState=="active"){
	            		$currentDateDB = date('Y-m-d');
	            		$qry = "SELECT id,recurly_plan_free_trial,subType FROM product WHERE recurly_plan_code='".$planCode."'";
	            		$result = exec_query($qry,1);
	            		if($result['recurly_plan_free_trial']=="7 days"){
							$endDateDb = date('Y-m-d',strtotime('+7 day', strtotime($currentDateDB)));
	            		}elseif ($result['recurly_plan_free_trial']=="14 days"){
	            			$endDateDb = date('Y-m-d',strtotime('+14 day', strtotime($currentDateDB)));
	            		}elseif ($result['subType']=="Monthly"){
	            			$endDateDb = date('Y-m-d',strtotime('+30 day', strtotime($currentDateDB)));
	            		}elseif ($result['subType']=="Annual"){
	            			$endDateDb = date('Y-m-d',strtotime('+365 day', strtotime($currentDateDB)));
	            		}elseif ($result['subType']=="Quaterly"){
	            			$endDateDb = date('Y-m-d',strtotime('+90 day', strtotime($currentDateDB)));
	            		}

	            		$paramsCust['subscription_id'] = $subId;
						$paramsCust['recurly_plan_code'] = $planCode;
						$paramsCust['recurly_plan_name'] = $subRow->plan->name;
						$paramsCust['recurly_uuid'] = $subRow->uuid;
						$paramsCust['orderNumber'] = $subRow->uuid;
						$paramsCust['recurly_state'] = $subRow->state;
						$paramsCust['recurly_quantity'] = $subRow->quantity;
						$paramsCust['recurly_total_amount_in_cents'] = $subRow->unit_amount_in_cents;
						$paramsCust['recurly_activated_at'] = $currentDateDB;
						$paramsCust['recurly_current_period_started_at'] = $currentDateDB;
						$paramsCust['recurly_current_period_ends_at'] = $endDateDb;
						$paramsCust['recurly_trial_started_at'] = $currentDateDB;
						$paramsCust['recurly_trial_ends_at'] = $endDateDb;
						$conditionsCust['recurly_plan_code']=$planCode;
						$conditionsCust['subscription_id']=$subId;
						$conditionsCust['recurly_state']='active';
						$id=insert_or_update('subscription_cust_order',$paramsCust,$conditionsCust);

	            		$objFunnelData = new registrationFunnelData();
						$planDetail = $objFunnelData->getPlanDetails($planCode);

	            		$planCharge = $subRow->unit_amount_in_cents/100;
	            		if($requestType!='addSubscription'){
		            		if($_SESSION['welcomeVisitCount']>=1){
								unset($_SESSION['recently_added']);
		            		}
	            		}

		            	$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $planCode;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['expireDate'] = $subRow->trial_ends_at->date;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['description'] = $subRow->plan->name;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderNumber'] = $subRow->uuid;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = $subRow->coupon_code;
						$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
	            		$_SESSION['recently_added']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];


	            		$_SESSION['products']['SUBSCRIPTION'][$planCode]['typeSpecificId'] = $planCode;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['expireDate'] = $subRow->trial_ends_at->date;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['description'] = $subRow->plan->name;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['price'] = $planCharge;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderNumber'] = $subRow->uuid;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['sourceCodeId'] = $subRow->coupon_code;
						$_SESSION['products']['SUBSCRIPTION'][$planCode]['orderItemType'] = 'SUBSCRIPTION';
	            		$_SESSION['products']['SUBSCRIPTION'][$planCode]['planGroup'] = $planDetail['plan_group'];

	            		$_SESSION['recentPlanCode'] = $planCode;

	            		$objUser = new user;
						$productstatusarray = $objUser->getSubcriptionProductDetails($subId);
						if($productstatusarray['Buzz']==1){
						    $countBuzzContributor=$objUser->countBuzzSubscriber();
							if($countBuzzContributor==1){
								set_sess_vars("buzzSubscriberContributor","1");
							}
						}
						if(is_array($productstatusarray))
						{
							foreach($productstatusarray as $key => $value)
							{
								set_sess_vars($key,$value);

							}
						}
						$billingInfo = Recurly_BillingInfo::get($subId);
						$objUserData = new userData;
						$objUserData->addGATrans($subRow->uuid,$planCode,$subRow->plan->name,$planCharge,$billingInfo->city,$billingInfo->state,$billingInfo->country);
	            	}
            	}
            	$result['status']= '1';
            }catch (exception  $e){
            	$message = 'There was an error while adding subscription to account code - '.$subId;
            	$message .= '<br> Here is a var dump of data.<pre>'.var_export($subscription,true).'</pre>';
				//$this->sentRecurlyErrorEmails("Add Subscription Plan",$message);
				$result['status']= '0';
				//$result['error'] = 'There was an error while adding subscription to your account, please contact support at support@minyanville.com for assistance';
            	$result['error'] = $e->getMessage();
            }
            return json_encode($result);
    }

    function updateAccountWithSubscription(){
			$subscription = new Recurly_Subscription();
			$subscription->plan_code = 'bmtu_annl_comp_nft_via_01012012';
			$subscription->currency = 'USD';
			$account = new Recurly_Account();
			$account->account_code = '1';
			$account->email = 'vijay.tomar@mediaagility.com';
			$account->first_name = 'Vijay';
			$account->last_name = 'Tomar';
			$billing_info = new Recurly_BillingInfo();
			$billing_info->number = '4012888888881881';
			$billing_info->month = 10;
			$billing_info->year = 2014;
			$account->billing_info = $billing_info;
			$subscription->account = $account;
			$subscription->create();

    }

    function closeAccount(){
        $account = Recurly_Account::get('1');
        $account->close();

    }

    function getViaCustInfo($startSubId=NULL){
    	$sql="select S.id,S.via_id,SCU.nameFirst,SCU.nameLast,SCU.company,SCU.email,SCU.phone,SCU.phonefax from subscription S, subscription_cust_info SCU where S.via_id=SCU.viaid and SCU.updated='0' limit ";
    	if(!empty($startSubId)){
    		$sql.=$startSubId.",";
    	}
    	$sql.=" 5000";
       $getResult=exec_query($sql);
       try{
            if(!empty($getResult)){
                foreach($getResult as $row){
                     $subId=$row['id'];
                     $userName=$row['email'];
                     $email=$row['email'];
                     $firstName=$row['nameFirst'];
                     $lastName=$row['nameLast'];
                     $companyName=$row['company'];
                     $viaId=$row['via_id'];
                     $phone=$row['phone'];
                     /*Check account in recurly*/
                     $accountCheck=$this->getAccount($subId,$userName,$email,$firstName,$lastName,$companyName,$viaId);

                        $params['updated']=1;
                        $conditions['viaid']=$viaId;
                        update_query('subscription_cust_info',$params,$conditions,$keynames=array());
                        echo "<br>".$subId.' updated successfully';
                }
            }
       }catch(Error $e){
            echo "<br>".$e;
       }



    }



    function getViaCustAddress($viaId){
        $sql="select S.id subid,S.email,SCI.nameFirst,SCI.nameLast,SCI.phone,SCA.address1,SCA.address2,SCA.address3,SCA.city,SCA.state,SCA.zip,SCA.country,SCA.typeofAddr
from subscription_cust_address SCA, subscription S,subscription_cust_info SCI
where SCA.viaid=S.via_id and SCI.viaid=SCA.viaid and SCA.updated='0' and SCA.viaid='".$viaId."'";
        $getResult=exec_query($sql,1);
        if(!empty($getResult)){
            return $getResult;
        }else{
             return false;
        }

    }


    function getViaCustOrder($viaId){

         $sql="select SCO.viaid,P.order_code,P.product,SCO.price,SCO.orderItemType,P.subType,SCO.qty,SCO.startDate,SCO.expireDate,SCO.billDate,
SCO.description,SCO.orderStatus,SCO.paymentStatus,SCO.n_issues_left,SCO.rate_class_seq,P.recurly_plan_code
from subscription_cust_order SCO, product P
where SCO.orderClassId=P.oc_id
and SCO.orderCodeId=P.order_code_id and P.order_code_id<>'9'
and SCO.typeSpecificId=P.subscription_def_id
and (DATE_FORMAT(SCO.expireDate,'%Y-%m-%d')>=DATE_FORMAT(now(),'%Y-%m-%d') or SCO.expireDate='0000-00-00 00:00:00')
and SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')
and SCO.viaid='".$viaId."' group by order_code";

        $getResult=exec_query($sql);
        if(!empty($getResult)){
            return $getResult;
        }else{
             return false;
        }
    }

    function updateSubscriptionPlanToRecurly(){

        $subscription = new Recurly_Subscription();
         $sql="select SCO.id,SCO.viaid,SCO.subscription_id,P.order_code,P.product,SCO.price,SCO.orderItemType,P.subType,SCO.qty,SCO.startDate,SCO.expireDate,SCO.billDate,
SCO.description,SCO.orderStatus,SCO.paymentStatus,SCO.n_issues_left,SCO.rate_class_seq,P.recurly_plan_code
from subscription_cust_order SCO, product P
where SCO.orderClassId=P.oc_id
and SCO.orderCodeId=P.order_code_id and P.order_code_id<>'9' and P.order_code_id<>'3'
and P.order_code_id<>'14'
and P.order_code_id<>'34'
and P.order_code_id<>'47'
and P.order_code_id<>'77'
and P.order_code_id<>'71'
and P.order_code_id<>'81'
and SCO.subscription_id not in (8525,8856,11956,18265,28572,28661,38742,43281,43404,30450,51989,63503,61651)
and SCO.typeSpecificId=P.subscription_def_id
and (DATE_FORMAT(SCO.expireDate,'%Y-%m-%d')=DATE_FORMAT('2012-06-11','%Y-%m-%d'))
and SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')
and SCO.viaid<>'0' limit 2000";

        $getCustSubscriptions=exec_query($sql);

            if(!empty($getCustSubscriptions)){
                foreach($getCustSubscriptions as $subRow){
                    try{
                        $subscription->plan_code =$subRow['recurly_plan_code'];
                        $subscription->currency = 'USD';
                        if($subRow['qty']=="0"){
                            $qty=1;
                        }else{
                            $qty=$subRow['qty'];
                        }
                        $subscription->quantity=$qty;

                        $subscription->activated_at=$subRow['startDate'].'-05:00';
                        $subscription->expires_at=$subRow['expireDate'].'-05:00';
                        $currentDate=date("Y-m-d",time());
                        $planDate=date("Y-m-d",strtotime($subRow['billDate']));

                        $subscription->trial_started_at=$subRow['startDate'].'-05:00';
			if($subRow['expireDate']=="0000-00-00 00:00:00"){
			    $subscription->trial_ends_at=$subRow['billDate'].'-05:00';
			}else{
			    $subscription->trial_ends_at=$subRow['expireDate'].'-05:00';
			}

                        $account = new Recurly_Account();
                        $account->account_code = $subRow['subscription_id'];
                        $subscription->account = $account;
                        $subscription->create();
                        $params['updated']=1;
                        $conditions['id']=$subRow['id'];
                        $conditions['viaid']=$subRow['viaid'];
                        update_query('subscription_cust_order',$params,$conditions,$keynames=array());
                    }catch(Exception $e){
                        echo "<br>".$e;
			$subject="Billing Info Updated- ".$subRow['subscription_id'];
			$this->sentRecurlyErrorEmails($subject,$e);
                    }
                }
            }

    }

    function catchRecurlyError($subId,$viaId,$email,$error){
        $params['subid']=$subId;
        $params['viaid']=$viaId;
        $params['email']=$email;
        $params['error']=$error;
        $id=insert_query('recurly_error',$params,$safe=0);
        if(!empty($id)){
            return true;
        }else{
            return false;
        }
    }

    function setRecurlyUsersData($notification){
       $notificationType=$notification->type;

        switch($notificationType){
	    case "new_account_notification":
                  $this->setNewAccountNotification($notification);
		break;
            case "canceled_account_notification":
                   $this->setCanceledAccountNotificatio($notification);
		break;
            case "billing_info_updated_notification":
                    $this->setBillingInfoUpdatedNotification($notification);
		break;
            case "reactivated_account_notification":
                    $this->setReactivatedAccountNotification($notification);
		break;
            case "new_subscription_notification":
                    $this->setNewSubscriptionNotification($notification);
		break;
            case "updated_subscription_notification":
                    $this->setUpdatedSubscriptionNotification($notification);
		break;
            case "canceled_subscription_notification":
                    $this->setCanceledSubscriptionNotification($notification);
		break;
            case "expired_subscription_notification":
                    $this->setExpiredSubscriptionNotification($notification);
		break;
            case "renewed_subscription_notification":
                    $this->setRenewedSubscriptionNotification($notification);
		break;
            case "successful_payment_notification":
                    $this->setSuccessfulPaymentNotification($notification);
		break;
            case "failed_payment_notification":
                    $this->setFailedPaymentNotification($notification);
		break;
            case "successful_refund_notification":
                    $this->setSuccessfulRefundNotification($notification);
		break;
            case "void_payment_notification":
                    $this->setVoidPaymentNotification($notification);
		break;

        }
    }


    function setNewAccountNotification($notification){
        try{
	    $notificationType=$notification->type;
	    $accountCode=$notification->account->account_code;
	    $getXml = Recurly_Account::get($accountCode);
	    $getData=$this->getRecurlyXMLData($getXml);
	    $getData['account_code']=$accountCode;
	    $params['id']=trim($getData['account_code']);
	    $createdDateTime=substr(trim($getData['created_at']),0,-5);

	    $hour=date("H",strtotime($createdDateTime));
	    $minute=date("i",strtotime($createdDateTime));
	    $second=date("s",strtotime($createdDateTime));
	    $month=date("m",strtotime($createdDateTime));
	    $day=date("d",strtotime($createdDateTime));
	    $year=date("Y",strtotime($createdDateTime));
	    $params['date']=time($hour,$minute,$second,$month,$day,$year);
            $params['email']=trim($getData['email']);
            $params['fname']=trim($getData['first_name']);
            $params['lname']=trim($getData['last_name']);
            $params['account_status']='enabled';
            $params['company']=$companyName;
            $condition=array('id' => $accountCode);
	    $id=insert_or_update('subscription',$params,$condition);

	    $this->setRecurlySubscriptionReportData($getData,$notificationType);

        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

    function setCanceledAccountNotificatio($notification){
        try{
	    $notificationType=$notification->type;
            $accountCode=$notification->account->account_code;
            $params['account_status']='disabled';
            $conditions['id']=$accountCode;
            update_query('subscription',$params,$conditions,$keynames=array());

	    $getXml = Recurly_Account::get($accountCode);
	    $getData=$this->getRecurlyXMLData($getXml);
	    $this->setRecurlySubscriptionReportData($getData,$notificationType);

        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setBillingInfoUpdatedNotification($notification){
        try{

	    $notificationType=$notification->type;
	    $accountCode=$notification->account->account_code;
	    $getXml = Recurly_BillingInfo::get($accountCode);
	    $getData=$this->getRecurlyXMLData($getXml);
	    $getData['account_code']=$accountCode;
            $params['id']=$accountCode;
            $params['fname']=trim($getData['first_name']);
            $params['lname']=trim($getData['last_name']);
            $params['address']=trim($getData['address1']);
            $params['address2']=trim($getData['address2']);
            $params['city']=trim($getData['city']);
            $params['state']=trim($getData['state']);
            $params['zip']=trim($getData['zip']);
            $params['account_status']='enabled';
            $params['country']=trim($getData['country']);
	    $params['ip']=trim($getData['ip_address']);
            $params['tel']=$getData['phone'];
            $conditions['id']=$accountCode;
            update_query('subscription',$params,$conditions,$keynames=array());

	    $this->setRecurlySubscriptionReportData($getData,$notificationType);

        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setReactivatedAccountNotification($notification){
       try{

	    $notificationType=$notification->type;
	    $accountCode=$notification->account->account_code;
	    $getXml = Recurly_Account::get($accountCode);
	    $getData=$this->getRecurlyXMLData($getXml);
	    $getData['account_code']=$accountCode;

	    $email=trim($getData['email']);
	    $firstName=trim($getData['first_name']);
	    $lastName=trim($getData['last_name']);
	    $companyName=trim($getData['company_name']);

	    $params['id']=$accountCode;
	    $params['email']=$email;
	    $params['fname']=$firstName;
	    $params['lname']=$lastName;
	    $params['company']=$companyName;
	    $params['account_status']='enabled';
	    $conditions['id']=$accountCode;
	    update_query('subscription',$params,$conditions,$keynames=array());
	    $this->setRecurlySubscriptionReportData($getData,$notificationType);

	$planCode=trim($getData['plan_code']);
        $planName=$notification->subscription->plan->name;
        $planUuid=trim($getData['uuid']);
        $planState=trim($getData['state']);
        $quantity=trim($getData['quantity']);
        $totalAmountInCents=trim($getData['unit_amount_in_cents']);
        $activatedAt=trim($getData['activated_at']);
        $currentPeriodStartedAt=trim($getData['current_period_started_at']);
        $currentPeriodEndsAt=trim($getData['current_period_ends_at']);


        $paramsCust['recurly_plan_code']=$planCode;
        $paramsCust['recurly_plan_name']=$planName;
        $paramsCust['recurly_uuid']=$planUuid;
        $paramsCust['recurly_state']=$planState;
        $paramsCust['recurly_quantity']=$quantity;
        $paramsCust['recurly_total_amount_in_cents']=$totalAmountInCents;
        $paramsCust['recurly_activated_at']=$activatedAt;
        $paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
        $paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
        $conditionsCust['recurly_plan_code']=$planCode;
        $conditionsCust['subscription_id']=$accountCode;
        update_query('subscription_cust_order',$paramsCust,$conditionsCust,$keynames=array());
	$this->setRecurlySubscriptionData($notification);
       }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

    function setNewSubscriptionNotification($notification){
        try{

		$notificationType=$notification->type;
		$accountCode=$notification->account->account_code;
		$subscriptionUuid=$notification->subscription->uuid;
		$getXml = Recurly_Subscription::get($subscriptionUuid);
		$getData=$this->getRecurlyXMLData($getXml);
		$getData['account_code']=$accountCode;
	        $planCode=trim($getData['plan_code']);
		$planName=$notification->subscription->plan->name;
		$planUuid=trim($getData['uuid']);
		$planState=trim($getData['state']);
		$quantity=trim($getData['quantity']);
		$totalAmountInCents=trim($getData['unit_amount_in_cents']);
		$activatedAt=trim($getData['activated_at']);
		$currentPeriodStartedAt=trim($getData['current_period_started_at']);
		$currentPeriodEndsAt=trim($getData['current_period_ends_at']);
		$trialStartedAt=trim($getData['trial_started_at']);
		$trialEndsAt=trim($getData['trial_ends_at']);

		$paramsCust['subscription_id']=$accountCode;
		$paramsCust['recurly_plan_code']=$planCode;
		$paramsCust['recurly_plan_name']=$planName;
		$paramsCust['recurly_uuid']=$planUuid;
		$paramsCust['orderNumber']=$planUuid;
		$paramsCust['recurly_state']=$planState;
		$paramsCust['recurly_quantity']=$quantity;
		$paramsCust['recurly_total_amount_in_cents']=$totalAmountInCents;
		$paramsCust['recurly_activated_at']=$activatedAt;
		$paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
		$paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
		$paramsCust['recurly_trial_started_at']=$trialStartedAt;
		$paramsCust['recurly_trial_ends_at']=$trialEndsAt;
		$conditionsCust['recurly_plan_code']=$planCode;
		$conditionsCust['subscription_id']=$accountCode;
		$conditionsCust['recurly_state']='active';
		insert_or_update('subscription_cust_order',$paramsCust,$conditionsCust);
		// subscribe user in mailchimp

		global $D_R,$prodList,$mailchimpProductSubGroupArr,$mailChimpApiKey,$productListId,$mailChimpProduct;
		include_once($D_R."/lib/config/_mailchimp_config.php");

		$sql = "SELECT subGroup FROM `product` WHERE `recurly_plan_code`='".$planCode."'";
		$prod_result = exec_query($sql,1);
		if(!empty($prod_result))
		{
		    if(in_array($prod_result['subGroup'],$mailchimpProductSubGroupArr))
		    {
			$sql_user = "SELECT email,fname,lname FROM subscription WHERE id='".$accountCode."'";
			$user_result = exec_query($sql_user,1);
			$resSub = subscribeUser($user_result['email'],$prod_result['subGroup'],$user_result['email'],$user_result['email']);
			if($resSub != "1")
			{
				$to="noel@minyanville.com,philip@minyanville.com,nidhi.singh@mediaagility.co.in";
				$from="support@minyanville.com";
				$subject="Please subscribe the user in MailChimp";
				$msg = "Please subscribe the user ".$user_result['email']." with first name '".$user_result['fname']."' and last name '".$user_result['lname']."' in the List '".$prodList[$prod_result['subGroup']]."' in MailChimp";
				mymail($to,$from,$subject,$msg);
			}
		    }
		}
		$this->setRecurlySubscriptionData($notification);

        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

    function setUpdatedSubscriptionNotification($notification){
        try{
	    $notificationType=$notification->type;
	    $accountCode=$notification->account->account_code;
	    $subscriptionUuid=$notification->subscription->uuid;
	    $getXml = Recurly_Subscription::get($subscriptionUuid);
	    $getData=$this->getRecurlyXMLData($getXml);
	    $getData['account_code']=$accountCode;

        $planCode=trim($getData['plan_code']);
        $planName=$notification->subscription->plan->name;
        $planUuid=trim($getData['uuid']);
        $planState=trim($getData['state']);
        $quantity=trim($getData['quantity']);
        $totalAmountInCents=trim($getData['unit_amount_in_cents']);
        $activatedAt=trim($getData['activated_at']);
        $currentPeriodStartedAt=trim($getData['current_period_started_at']);
	$currentPeriodEndsAt=trim($getData['current_period_ends_at']);
        $trialStartedAt=trim($getData['trial_started_at']);
        $trialEndsAt=trim($getData['trial_ends_at']);

        $paramsCust['recurly_plan_code']=$planCode;
        $paramsCust['recurly_plan_name']=$planName;
        $paramsCust['recurly_uuid']=$planUuid;
	$paramsCust['orderNumber']=$planUuid;
        $paramsCust['recurly_state']=$planState;
        $paramsCust['recurly_quantity']=$quantity;
        $paramsCust['recurly_total_amount_in_cents']=$totalAmountInCents;
        $paramsCust['recurly_activated_at']=$activatedAt;
        $paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
        $paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
        $paramsCust['recurly_trial_started_at']=$trialStartedAt;
        $paramsCust['recurly_trial_ends_at']=$trialEndsAt;
        $conditionsCust['recurly_plan_code']=$planCode;
        $conditionsCust['subscription_id']=$accountCode;
        update_query('subscription_cust_order',$paramsCust,$conditionsCust,$keynames=array());
	$this->setRecurlySubscriptionData($notification);
        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setCanceledSubscriptionNotification($notification){
        try{

	$accountCode=$notification->account->account_code;
	$subscriptionUuid=$notification->subscription->uuid;
	$getXml = Recurly_Subscription::get($subscriptionUuid);
	$getData=$this->getRecurlyXMLData($getXml);
	$getData['account_code']=$accountCode;

        $planCode=trim($getData['plan_code']);
        $planState=trim($getData['state']);
        $activatedAt=trim($getData['activated_at']);
        $canceledAt=trim($getData['canceled_at']);
        $expiresAt=trim($getData['expires_at']);
        $currentPeriodStartedAt=trim($getData['current_period_started_at']);
        $currentPeriodEndsAt=trim($getData['current_period_ends_at']);

        $paramsCust['recurly_plan_code']=$planCode;
        $paramsCust['recurly_state']=$planState;
        $paramsCust['recurly_activated_at']=$activatedAt;
        $paramsCust['recurly_canceled_at']=$canceledAt;
        $paramsCust['recurly_expires_at']=$expiresAt;
        $paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
        $paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
        $conditionsCust['recurly_plan_code']=$planCode;
        $conditionsCust['subscription_id']=$accountCode;
        update_query('subscription_cust_order',$paramsCust,$conditionsCust,$keynames=array());
	$this->setRecurlySubscriptionData($notification);
        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

    function setExpiredSubscriptionNotification($notification){
        try{
	$accountCode=$notification->account->account_code;
	$subscriptionUuid=$notification->subscription->uuid;
	$getXml = Recurly_Subscription::get($subscriptionUuid);
	$getData=$this->getRecurlyXMLData($getXml);
	$getData['account_code']=$accountCode;

        $planCode=trim($getData['plan_code']);
        $planName=$notification->subscription->plan->name;
        $planUuid=trim($getData['uuid']);
        $planState=trim($getData['state']);
        $quantity=trim($getData['quantity']);
        $totalAmountInCents=trim($getData['unit_amount_in_cents']);
        $activatedAt=trim($getData['activated_at']);
        $canceledAt=trim($getData['canceled_at']);
        $expiresAt=trim($getData['expires_at']);
        $currentPeriodStartedAt=trim($getData['current_period_started_at']);
        $currentPeriodEndsAt=trim($getData['current_period_ends_at']);
        $paramsCust['recurly_plan_code']=$planCode;
        $paramsCust['recurly_plan_name']=$planName;
        $paramsCust['recurly_uuid']=$planUuid;
        $paramsCust['recurly_state']=$planState;
        $paramsCust['recurly_quantity']=$quantity;
        $paramsCust['recurly_total_amount_in_cents']=$totalAmountInCents;
        $paramsCust['recurly_activated_at']=$activatedAt;
        $paramsCust['recurly_canceled_at']=$canceledAt;
        $paramsCust['recurly_expires_at']=$expiresAt;
        $paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
        $paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
        $conditionsCust['recurly_plan_code']=$planCode;
        $conditionsCust['subscription_id']=$accountCode;
        update_query('subscription_cust_order',$paramsCust,$conditionsCust,$keynames=array());

		// un-subscribe user in mailchimp

        global $D_R,$prodList,$mailchimpProductSubGroupArr,$mailChimpApiKey,$productListId,$mailChimpProduct;
	include_once($D_R."/lib/config/_mailchimp_config.php");

        $sql = "SELECT subGroup FROM `product` WHERE `recurly_plan_code`='".$planCode."'";
		$prod_result = exec_query($sql,1);
		if(!empty($prod_result))
		{
		    if(in_array($prod_result['subGroup'],$mailchimpProductSubGroupArr))
		    {
				$sql_user = "SELECT email,fname,lname FROM subscription WHERE id='".$accountCode."'";
				$user_result = exec_query($sql_user,1);
				$resUnSub = unSubscribeUser($user_result['email'],$prod_result['subGroup']);
				if($resUnSub != "1")
				{
					$to="noel@minyanville.com,philip@minyanville.com,nidhi.singh@mediaagility.co.in";
					$from="support@minyanville.com";
					$subject="Please un-subscribe the user in MailChimp";
					$msg = "Please un-subscribe the user ".$user_result['email']." from the List '".$prodList[$prod_result['subGroup']]."' in MailChimp";
					mymail($to,$from,$subject,$msg);
				}
		    }
		}
		if($prod_result['subGroup']=='keene'){
			$objRegistrationFunnelData = new registrationFunnelData();
			$chkAlertPref = $objRegistrationFunnelData->getAlertPreference($accountCode);
			if($chkAlertPref['alert_pref']=='sms' || $chkAlertPref['alert_pref']=='both'){
				include_once($D_R.'/lib/aws-sns/lib/amazonsns.class.php');
				include_once($D_R."/lib/config/_aws_config.php");
				$objAmazonSNS = new AmazonSNS($snsAccessKeyId, $snsSecretAccessKey);
				$mobileNum= str_replace('-','',$chkAlertPref['mobile_num']);
				try{
					$subscriberList = $objAmazonSNS->listSubscriptionsByTopic($topicName);
					foreach($subscriberList as $k=>$v){
						if($v['Endpoint']==$mobileNum){
							$subscriptionArn=$v['SubscriptionArn'];
							$objAmazonSNS->unsubscribe($subscriptionArn);
						}
						break;
					}
				}catch(Exception $e){
					$to="anshul.budhiraja@mediaagility.co.in";
					$from="support@minyanville.com";
					$subject="Please un-subscribe the user from Keene Text Alerts";
					$msg = var_dump($e);
					mymail($to,$from,$subject,$msg);
				}
			}
		}
		
		$this->setRecurlySubscriptionData($notification);
        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }


    function setRenewedSubscriptionNotification($notification){
        try{
        $accountCode=$notification->account->account_code;
	$subscriptionUuid=$notification->subscription->uuid;
	$getXml = Recurly_Subscription::get($subscriptionUuid);
	$getData=$this->getRecurlyXMLData($getXml);
	$getData['account_code']=$accountCode;

        $planCode=trim($getData['plan_code']);
        $planName=$notification->subscription->plan->name;
        $planUuid=trim($getData['uuid']);
        $planState=trim($getData['state']);
        $quantity=trim($getData['quantity']);
        $totalAmountInCents=trim($getData['unit_amount_in_cents']);
        $activatedAt=trim($getData['activated_at']);
        $currentPeriodStartedAt=trim($getData['current_period_started_at']);
        $currentPeriodEndsAt=trim($getData['current_period_ends_at']);
        $paramsCust['subscription_id']=$accountCode;
        $paramsCust['recurly_plan_code']=$planCode;
        $paramsCust['recurly_plan_name']=$planName;
        $paramsCust['recurly_uuid']=$planUuid;
        $paramsCust['recurly_state']=$planState;
        $paramsCust['recurly_quantity']=$quantity;
        $paramsCust['recurly_total_amount_in_cents']=$totalAmountInCents;
        $paramsCust['recurly_activated_at']=$activatedAt;
        $paramsCust['recurly_current_period_started_at']=$currentPeriodStartedAt;
        $paramsCust['recurly_current_period_ends_at']=$currentPeriodEndsAt;
        $paramsCust['recurly_trial_started_at']=$trialStartedAt;
        $paramsCust['recurly_trial_ends_at']=$trialEndsAt;
        $conditionsCust['recurly_plan_code']=$planCode;
        $conditionsCust['subscription_id']=$accountCode;
        insert_or_update('subscription_cust_order',$paramsCust,$conditionsCust);
	$this->setRecurlySubscriptionData($notification);
        }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setSuccessfulPaymentNotification($notification){
       try{
            $accountCode=$notification->account->account_code;
            $subscription_id=$accountCode;
            $transactionId=$notification->transaction->id;
            $invoiceId=$notification->transaction->invoice_id;
            $invoiceNumber=$notification->transaction->invoice_number;
            $subscriptionId=$notification->transaction->subscription_id;
            $action=$notification->transaction->action;
            $date=$notification->transaction->date;
            $amountInCents=$notification->transaction->amount_in_cents;
            $status=$notification->transaction->status;
            $message=$notification->transaction->message;
            $reference=$notification->transaction->reference;
            $source=$notification->transaction->source;
            $cvvResult=$notification->transaction->cvv_result;
            $avsResult=$notification->transaction->avs_result;
            $avsResultStreet=$notification->transaction->avs_result_street;
            $avsResultPostal=$notification->transaction->avs_result_postal;
            $test=$notification->transaction->test;
            $voidable=$notification->transaction->voidable;
            $refundable=$notification->transaction->refundable;
	    $notificationType=$notification->type;
            $paramsTrans['subscription_id']=$subscription_id;
            $paramsTrans['transactionId']=$transactionId;
            $paramsTrans['invoiceId']=$invoiceId;
            $paramsTrans['invoiceNumber']=$invoiceNumber;
            $paramsTrans['subscriptionId']=$subscriptionId;
            $paramsTrans['action']=$action;
            $paramsTrans['date']=$date;
            $paramsTrans['amountInCents']=$amountInCents;
            $paramsTrans['status']=$status;
            $paramsTrans['message']=$message;
            $paramsTrans['reference']=$reference;
            $paramsTrans['source']=$source;
            $paramsTrans['cvvResult']=$cvvResult;
            $paramsTrans['avsResult']=$avsResult;
            $paramsTrans['avsResultStreet']=$avsResultStreet;
            $paramsTrans['avsResultPostal']=$avsResultPostal;
            $paramsTrans['test']=$test;
            $paramsTrans['voidable']=$voidable;
            $paramsTrans['refundable']=$refundable;
	    $paramsTrans['notificationType']=$notificationType;
            $id=insert_query('subscription_transaction',$paramsTrans,$safe=0);
       }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

    function setFailedPaymentNotification($notification){
         try{
            $accountCode=$notification->account->account_code;
            $subscription_id=$accountCode;
            $transactionId=$notification->transaction->id;
            $invoiceId=$notification->transaction->invoice_id;
            $invoiceNumber=$notification->transaction->invoice_number;
            $subscriptionId=$notification->transaction->subscription_id;
            $action=$notification->transaction->action;
            $date=$notification->transaction->date;
            $amountInCents=$notification->transaction->amount_in_cents;
            $status=$notification->transaction->status;
            $message=$notification->transaction->message;
            $reference=$notification->transaction->reference;
            $source=$notification->transaction->source;
            $cvvResult=$notification->transaction->cvv_result;
            $avsResult=$notification->transaction->avs_result;
            $avsResultStreet=$notification->transaction->avs_result_street;
            $avsResultPostal=$notification->transaction->avs_result_postal;
            $test=$notification->transaction->test;
            $voidable=$notification->transaction->voidable;
            $refundable=$notification->transaction->refundable;
            $notificationType=$notification->type;
            $paramsTrans['subscription_id']=$subscription_id;
            $paramsTrans['transactionId']=$transactionId;
            $paramsTrans['invoiceId']=$invoiceId;
            $paramsTrans['invoiceNumber']=$invoiceNumber;
            $paramsTrans['subscriptionId']=$subscriptionId;
            $paramsTrans['action']=$action;
            $paramsTrans['date']=$date;
            $paramsTrans['amountInCents']=$amountInCents;
            $paramsTrans['status']=$status;
            $paramsTrans['message']=$message;
            $paramsTrans['reference']=$reference;
            $paramsTrans['source']=$source;
            $paramsTrans['cvvResult']=$cvvResult;
            $paramsTrans['avsResult']=$avsResult;
            $paramsTrans['avsResultStreet']=$avsResultStreet;
            $paramsTrans['avsResultPostal']=$avsResultPostal;
            $paramsTrans['test']=$test;
            $paramsTrans['voidable']=$voidable;
            $paramsTrans['refundable']=$refundable;
	    $paramsTrans['notificationType']=$notificationType;
            $id=insert_query('subscription_transaction',$paramsTrans,$safe=0);
       }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setSuccessfulRefundNotification($notification){
         try{
            $accountCode=$notification->account->account_code;
            $subscription_id=$accountCode;
            $transactionId=$notification->transaction->id;
            $invoiceId=$notification->transaction->invoice_id;
            $invoiceNumber=$notification->transaction->invoice_number;
            $subscriptionId=$notification->transaction->subscription_id;
            $action=$notification->transaction->action;
            $date=$notification->transaction->date;
            $amountInCents=$notification->transaction->amount_in_cents;
            $status=$notification->transaction->status;
            $message=$notification->transaction->message;
            $reference=$notification->transaction->reference;
            $source=$notification->transaction->source;
            $cvvResult=$notification->transaction->cvv_result;
            $avsResult=$notification->transaction->avs_result;
            $avsResultStreet=$notification->transaction->avs_result_street;
            $avsResultPostal=$notification->transaction->avs_result_postal;
            $test=$notification->transaction->test;
            $voidable=$notification->transaction->voidable;
            $refundable=$notification->transaction->refundable;
	    $notificationType=$notification->type;
            $paramsTrans['subscription_id']=$subscription_id;
            $paramsTrans['transactionId']=$transactionId;
            $paramsTrans['invoiceId']=$invoiceId;
            $paramsTrans['invoiceNumber']=$invoiceNumber;
            $paramsTrans['subscriptionId']=$subscriptionId;
            $paramsTrans['action']=$action;
            $paramsTrans['date']=$date;
            $paramsTrans['amountInCents']=$amountInCents;
            $paramsTrans['status']=$status;
            $paramsTrans['message']=$message;
            $paramsTrans['reference']=$reference;
            $paramsTrans['source']=$source;
            $paramsTrans['cvvResult']=$cvvResult;
            $paramsTrans['avsResult']=$avsResult;
            $paramsTrans['avsResultStreet']=$avsResultStreet;
            $paramsTrans['avsResultPostal']=$avsResultPostal;
            $paramsTrans['test']=$test;
            $paramsTrans['voidable']=$voidable;
            $paramsTrans['refundable']=$refundable;
	    $paramsTrans['notificationType']=$notificationType;
            $id=insert_query('subscription_transaction',$paramsTrans,$safe=0);
       }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setVoidPaymentNotification($notification){
         try{
            $accountCode=$notification->account->account_code;
            $subscription_id=$accountCode;
            $transactionId=$notification->transaction->id;
            $invoiceId=$notification->transaction->invoice_id;
            $invoiceNumber=$notification->transaction->invoice_number;
            $subscriptionId=$notification->transaction->subscription_id;
            $action=$notification->transaction->action;
            $date=$notification->transaction->date;
            $amountInCents=$notification->transaction->amount_in_cents;
            $status=$notification->transaction->status;
            $message=$notification->transaction->message;
            $reference=$notification->transaction->reference;
            $source=$notification->transaction->source;
            $cvvResult=$notification->transaction->cvv_result;
            $avsResult=$notification->transaction->avs_result;
            $avsResultStreet=$notification->transaction->avs_result_street;
            $avsResultPostal=$notification->transaction->avs_result_postal;
            $test=$notification->transaction->test;
            $voidable=$notification->transaction->voidable;
            $refundable=$notification->transaction->refundable;
	    $notificationType=$notification->type;
            $paramsTrans['subscription_id']=$subscription_id;
            $paramsTrans['transactionId']=$transactionId;
            $paramsTrans['invoiceId']=$invoiceId;
            $paramsTrans['invoiceNumber']=$invoiceNumber;
            $paramsTrans['subscriptionId']=$subscriptionId;
            $paramsTrans['action']=$action;
            $paramsTrans['date']=$date;
            $paramsTrans['amountInCents']=$amountInCents;
            $paramsTrans['status']=$status;
            $paramsTrans['message']=$message;
            $paramsTrans['reference']=$reference;
            $paramsTrans['source']=$source;
            $paramsTrans['cvvResult']=$cvvResult;
            $paramsTrans['avsResult']=$avsResult;
            $paramsTrans['avsResultStreet']=$avsResultStreet;
            $paramsTrans['avsResultPostal']=$avsResultPostal;
            $paramsTrans['test']=$test;
            $paramsTrans['voidable']=$voidable;
            $paramsTrans['refundable']=$refundable;
	    $paramsTrans['notificationType']=$notificationType;
            $id=insert_query('subscription_transaction',$paramsTrans,$safe=0);
       }catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function sentRecurlyErrorEmails($subject,$message){
        global $toRecurlyError,$fromRecurlyError,$subjectRecurlyError;
        $to=$toRecurlyError;
        $from=$fromRecurlyError;
        $subject=$subjectRecurlyError.'-'.$subject;
        mymail($to,$from,$subject,$message,$text="",$file="",$ftype="application/octet-stream",$return_str=0);
    }

    function getOrderDetailsOfAccount($subId){
	 	$subscriptions = Recurly_SubscriptionList::getForAccount($subId);
	 	return $subscriptions;
	 }

	 function productAdd($subId, $firstName, $lastName, $email, $address, $country, $city, $state, $zip, $phoneNum, $cardType, $cardNum, $expMnth, $expYr, $cvv, $couponCode, $planCode, $planGroup,$pref)
	 {
	 	global $REG_EML_REPLYTO,$globalPwd,$pwdSubject,$HTPFX,$HTHOST;
	 	$objRegistrationFunnelData = new registrationFunnelData();
	 	$sql="select id,email,password from subscription where email='".$email."'";
		$chkEmail=exec_query($sql,1);
		if(!empty($chkEmail['email'])){		//existing user
			$subId = $chkEmail['id'];
			$chkProductDetails = $objRegistrationFunnelData->chkProductExistence($planCode,$subId);
			if($chkProductDetails=='0'){
				$addProdSub = $this->addSubscriptionFromFunnel($subId,$firstName,$lastName,$email,$address,$country,$city,$state,$zip,$phoneNum,$cardType,$cardNum,$expMnth,$expYr,$cvv,$couponCode,$planCode);
				if($addProdSub['status']=='1'){
					$loginSystem = new user();
					$objUser= new userData();
					if($chkEmail['password']==''){
					    $userPaswd=$objUser->encryptUserPassword($globalPwd);
						$userData['password'] = $userPaswd;
						$recordId = update_query("subscription", $userData, array(email=>$email,id=>$subId));
						$isLoggedIn=$loginSystem->login($email,$globalPwd);
					}else{
						$pwd = $objUser->decryptUserPassword($chkEmail['password']);
						$isLoggedIn=$loginSystem->login($email,$pwd);
					}
					
					$subscribeToMail = '1';
					if($pref!='' && $planGroup=='keene'){
						$prefId = $objRegistrationFunnelData->setAlertPreference($subId,$pref,'');	
						if($pref=='sms'){
							$subscribeToMail='0';
						}
					}
					global $D_R,$prodList,$mailchimpProductSubGroupArr,$mailChimpApiKey,$productListId,$mailChimpProduct;
					include_once($D_R."/lib/config/_mailchimp_config.php");
				    if(in_array($planGroup,$mailchimpProductSubGroupArr) && $subscribeToMail=='1'){
						$resSub = subscribeUser($email,$planGroup,$firstName,$lastName);
						if($resSub != "1"){
							$to="noel@minyanville.com,philip@minyanville.com,nidhi.singh@mediaagility.co.in";
							$from="support@minyanville.com";
							$subject="Please subscribe the user in MailChimp";
							$msg = "Please subscribe the user ".$email." with first name '".$firstName."' and last name '".$lastName."' in the List '".$prodList[$planGroup]."' in MailChimp";
							mymail($to,$from,$subject,$msg);
						}
				    }
					/*if($isLoggedIn==true){
						$this->setProductInSession($subId);
					}*/
					$result['status'] = true;
					return $result;
				}else{
					$result['status'] = false;
					$result['msg'] = $addProdSub['msg'];
					return $result;
				}
			}else{
				$result['status'] = false;
				$result['msg'] = 'You already have subscribed to this Product.';
				return $result;
			}
		}else {	//new user
			$addProdSub = $this->addSubscriptionFromFunnel($subId,$firstName,$lastName,$email,$address,$country,$city,$state,$zip,$phoneNum,$cardType,$cardNum,$expMnth,$expYr,$cvv,$couponCode,$planCode);
			if($addProdSub['status']=='1'){
				$objUser= new userData();
			/* After Recurly Insertion done check insertion in MVIL DB Start  */
			    $userPaswd=$objUser->encryptUserPassword($globalPwd);
			    $qryDetails = "select id,email from subscription where email='".$email."'";
			    $chkDetails =  exec_query($qryDetails,1);
    if ($chkDetails['id']==''){
					$userdata=array('email'=>$email,'password'=>$userPaswd,'id'=>$subId,'tel'=>$phoneNum,'fname'=>$firstName,'lname'=>$lastName,'address'=>$address,'city'=>$city,'state'=>$state,'zip'=>$zip);
					$recordId = insert_query("subscription",$userdata);
			    }else{
					$userData['password'] = $userPaswd;
					$recordId = update_query("subscription", $userData, array(email=>$email,id=>$subId,'tel'=>$phoneNum,'fname'=>$firstName,'lname'=>$lastName,'address'=>$address,'city'=>$city,'state'=>$state,'zip'=>$zip));
			    }
			    $EML_TMPL=$HTPFX."minyanville:fE8Gnnhn3TI8L4f@".$HTHOST."/emails/_eml_new_pass.htm?email=".$email."&password=".$globalPwd;
           		mymail($email,$REG_EML_REPLYTO,$pwdSubject,inc_web($EML_TMPL));

				if(isset($recordId))
				{
					/* After insertion in subscription table insert the data into
						1. email_alert_categorysubscribe
						2. email_alert_authorsubscribe
						3. ex_user_profile
						4. ex_user_email_settings
						5. ex_profile_privacy
						RegisterUser($subid)
						tables then go for login */

					$authemail_alert=0;
					$email_alert=0;
					$emailalertsArray = array('subscriber_id'=>$subId, 'category_ids'=>$catgors, 'email_alert'=>$email_alert);
					insert_or_update('email_alert_categorysubscribe', $emailalertsArray, array('subscriber_id'=>$subId));

					$authorsrray=array('subscriber_id'=>$subId, 'author_id'=>$_POST['authorCategories'], 'email_alert'=>$authemail_alert);
					insert_or_update('email_alert_authorsubscribe', $authorsrray, array('subscriber_id'=>$subId));

					RegisterUser($subId); // defined in _exchange_lib.php

					$subarray = array('subscription_id'=>$subId);
					$profileid = insert_query('ex_user_profile', $subarray);
					
					$subscribeToMail = '1';
					if($pref!='' && $planGroup=='keene'){
						$prefId = $objRegistrationFunnelData->setAlertPreference($subId,$pref,'');	
						if($pref=='sms'){
							$subscribeToMail='0';
						}
					}
					global $D_R,$prodList,$mailchimpProductSubGroupArr,$mailChimpApiKey,$productListId,$mailChimpProduct;
					include_once($D_R."/lib/config/_mailchimp_config.php");
				    if(in_array($planGroup,$mailchimpProductSubGroupArr) && $subscribeToMail=='1'){
						$resSub = subscribeUser($email,$planGroup,$firstName,$lastName);
						if($resSub != "1"){
							$to="noel@minyanville.com,philip@minyanville.com,nidhi.singh@mediaagility.co.in";
							$from="support@minyanville.com";
							$subject="Please subscribe the user in MailChimp";
							$msg = "Please subscribe the user ".$email." with first name '".$firstName."' and last name '".$lastName."' in the List '".$prodList[$planGroup]."' in MailChimp";
							mymail($to,$from,$subject,$msg);
						}
				    }
				}

				$loginSystem = new user();
				$isLoggedIn=$loginSystem->login($email,$globalPwd);
				/*if($isLoggedIn==true){
					$this->setProductInSession($subId);
				}*/
				$result['status'] = true;
				return $result;
			}else{
				$result['status'] = false;
				$result['msg'] = $addProdSub['msg'];
				return $result;
			}
		}
	 }

	 function getUserActiveSubsPlan($id){
		$qry="select SCO.viaid,P.recurly_plan_code as via_plan_code,SCO.recurly_plan_code,P.subGroup
from subscription_cust_order SCO, product P where SCO.orderClassId=P.oc_id and SCO.orderCodeId=P.order_code_id and P.order_code_id<>'9' and SCO.typeSpecificId=P.subscription_def_id and  (((SCO.expireDate > NOW() OR SCO.expireDate='0000-00-00 00:00:00') AND  SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT')) OR SCO.recurly_state='active')and SCO.subscription_id='".$id."'";
		$getResult=exec_query($qry);
		if(!empty($getResult)){
		    return $getResult;
		}else{
		    return false;
		}

	 }


	 function updateDoNotAutoRenewPlanToRecurly(){

        $subscription = new Recurly_Subscription();
         $sql="select SCO.id,SCO.viaid,SCO.subscription_id,P.order_code,P.product,SCO.price,SCO.orderItemType,P.subType,SCO.qty,SCO.startDate,SCO.expireDate,SCO.billDate,
SCO.description,SCO.orderStatus,SCO.paymentStatus,SCO.n_issues_left,SCO.rate_class_seq,P.recurly_plan_code
from subscription_cust_order_via SCO, product P
where SCO.orderClassId=P.oc_id
and SCO.orderCodeId=P.order_code_id and P.order_code_id<>'9' and P.order_code_id<>'3'
and P.order_code_id<>'14'
and P.order_code_id<>'34'
and P.order_code_id<>'47'
and P.order_code_id<>'77'
and P.order_code_id<>'71'
and P.order_code_id<>'81'
and SCO.typeSpecificId=P.subscription_def_id
and (DATE_FORMAT(SCO.expireDate,'%Y-%m-%d')>=DATE_FORMAT('2012-06-11','%Y-%m-%d'))
and SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')
and SCO.viaid<>'0' and SCO.auto_renew='DO_NOT_RENEW' limit 5000";

        $getCustSubscriptions=exec_query($sql);

            if(!empty($getCustSubscriptions)){
                foreach($getCustSubscriptions as $subRow){
                    try{
			$subId=$subRow['subscription_id'];
                       // $subscription->plan_code =$subRow['recurly_plan_code'];
                        $subscriptions = Recurly_SubscriptionList::getForAccount($subId);
			foreach ($subscriptions as $subscription) {
			  $planCode=$subscription->plan->plan_code;
			  $uuid=$subscription->uuid;
			  if($planCode==$subRow['recurly_plan_code']){
			    $subscription = Recurly_Subscription::get($uuid);
			    $subscription->cancel();
			  }
			}


                    }catch(Exception $e){
                        echo "<br>".$e;
			$subject="Plan Updated- ".$subRow['subscription_id'];
			$this->sentRecurlyErrorEmails($subject,$e);
                    }
                }
            }

    }

    function parseRecurlyXml($result){
	$chunks = explode(',', $result);
	$resultAsArray = array();
	foreach ($chunks as $value) {
	    $parts = explode('=', $value);
	    $tmpKey = trim($parts[0]);
	    $tmpValue = str_replace('>','',$parts[1]);
	    $tmpValue = str_replace('"','',$tmpValue);
	    $resultAsArray[$tmpKey] = $tmpValue;
	}
	if(!empty($resultAsArray)){
	    return $resultAsArray;
	}
    }

    function getRecurlyXMLData($getXml){
	 $newstring = str_replace(", ", "&", $getXml);
	 $newstring = str_replace('"'," ", $newstring);
	 $newstring = str_replace('>'," ", $newstring);
	 parse_str($newstring, $myarray);
	 if(is_array($myarray)){
	    return $myarray;
	 }

    }

    function setRecurlySubscriptionReportData($getData,$notificationType){
	try{
	    $params['subscription_id']=$getData['account_code'];
	    $params['userName']=trim($getData['username']);
	    $params['email']=trim($getData['email']);
	    $params['firstName']=trim($getData['first_name']);
	    $params['lastName']=trim($getData['last_name']);
	    $params['accountState']=trim($getData['state']);
	    $params['companyName']=trim($getData['company_name']);
	    $params['accountCreatedAt']=trim($getData['created_at']);

	    $params['address1']=trim($getData['address1']);
	    $params['address2']=trim($getData['address2']);
	    $params['city']=trim($getData['city']);
	    if(trim($getData['state'])!=="active"){
		$params['state']=trim($getData['state']);
	    }else{
		$params['state']="";
	    }
	    $params['zip']=trim($getData['zip']);
	    $params['country']=trim($getData['country']);
	    $params['ipAddress']=trim($getData['ip_address']);
	    $params['ipAddressCountry']=trim($getData['ip_address_country']);
	    $params['notificationType']=$notificationType;
	    $id=insert_query('subscription_cust_report',$params,$safe=0);
	    if(!empty($id)){
		return $id;
	    }
	}catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }
    }

    function setRecurlySubscriptionData($notification){
	try{
	    $notificationType=$notification->type;
	    $subscriptionUuid=$notification->subscription->uuid;
	    $subscriptions = Recurly_Subscription::get($subscriptionUuid);
	    $getSubscriptionData=$this->parseRecurlyXml($subscriptions);
	    $params['subscription_id']=$notification->account->account_code;
	    $params['recurly_plan_code']=$getSubscriptionData['plan_code'];
	    $params['recurly_uuid']=$getSubscriptionData['uuid'];
	    $params['recurly_state']=$getSubscriptionData['state'];
	    $params['recurly_quantity']=$getSubscriptionData['quantity'];
	    $params['recurly_total_amount_in_cents']=$getSubscriptionData['unit_amount_in_cents'];
	    $params['recurly_activated_at']=$getSubscriptionData['activated_at'];
	    $params['recurly_canceled_at']=$getSubscriptionData['canceled_at'];
	    $params['recurly_expires_at']=$getSubscriptionData['expires_at'];
	    $params['recurly_current_period_started_at']=$getSubscriptionData['current_period_started_at'];
	    $params['recurly_current_period_ends_at']=$getSubscriptionData['current_period_ends_at'];
	    $params['recurly_trial_started_at']=$getSubscriptionData['trial_started_at'];
	    $params['recurly_trial_ends_at']=$getSubscriptionData['trial_ends_at'];
	    $params['notificationType']=$notificationType;
	    $id=insert_query('subscription_cust_report',$params,$safe=0);
	    if(!empty($id)){
		return $id;
	    }
	}catch(Exception $e){
            $subject=$notification->type;
            $this->sentRecurlyErrorEmails($subject,$e);
        }

    }

function addSubscriptionFromFunnel($subId,$firstName,$lastName,$email,$address,$country,$city,$state,$zip,$phoneNum,$cardType,$cardNum,$expMnth,$expYr,$cvv,$couponCode,$planCode){
	global $globalPwd;
		$successAcc = $this->getAccount($subId,$email,$email,$firstName,$lastName,'','');
		if($successAcc=="1"){
			$paramsAcc['id']=$subId;
            $paramsAcc['email']=$email;
            $paramsAcc['fname']=$firstName;
            $paramsAcc['lname']=$lastName;
            $paramsAcc['account_status']='enabled';
            $condition=array('id' => $subId);
	    	$id=insert_or_update('subscription',$paramsAcc,$condition);
		}

		$billInfo = $this->setBillingInfoToRecurly($subId,$firstName,$lastName,$address,'',$city,$state,$country,$zip,$phoneNum,$cardNum,$cvv,$expMnth,$expYr,'',$email);
		if ($billInfo['status']=='1'){
            $paramsBill['address']=$address;
            $paramsBill['address2']='';
            $paramsBill['city']=$city;
            $paramsBill['state']=$state;
            $paramsBill['zip']=$zip;
            $paramsBill['country']=$country;
            $paramsBill['tel']=$phoneNum;
            $condition=array('id' => $subId);
 			update_query('subscription',$paramsBill,$condition,$keynames=array());

			$addSub = $this->addSubscriptionToAccount($subId,$planCode,'1',$couponCode,'registration');
			$addSub = json_decode($addSub);
			if($addSub->status=='1'){
				$success['status'] = '1';
			}else{
				$success['status'] = '0';
				$success['msg'] = $addSub->error;
			}
		}else{
			$success['status'] = '0';
			$success['msg'] = $billInfo['msg'];
		}
		return $success;
    }
} //class end

?>