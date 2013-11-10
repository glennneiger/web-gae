<?
class Robot{
	function Robot(){
		debug("ROBOT:Contructor");
		global $RBT_TRIAL_LENGTH,$RBT_RECURRING_RATE,$RBT_FROM_EMAIL,$REG_EML_TRIAL_REPLYTO;
		global $RBT_BILLING_DELAY, $RBT_BILLING_DELAY_YR,$REG_MONTHLY_EDU,$REG_MONTHLY_NEWPREM,$REG_CHARGE_YR2;
		global $RBT_REMOVED_SUBJECT, $RBT_REMOVED_TMPL;
		global $RBT_CC_FAIL_SUBJECT, $RBT_CC_FAIL_TMPL;
		global $RBT_DISABLED_SUBJECT, $RBT_DISABLED_TMPL;
		global $RBT_DOWNGRADE_SUBJECT, $RBT_DOWNGRADE_TMPL, $RBT_OPT_TMPL,$RBT_BEFORE_EXPIRY_TMPL,$RBT_AFTER_EXPIRY_TMPL;
		global $RBT_WARN_DISABLED_SUBJECT,$RBT_BEFORE_EXPIRY_SUBJECT,$RBT_AFTER_EXPIRY_SUBJECT;
		global $RBT_WARN_PROD_TMPL,$RBT_DISABLED_PROD_TMPL,$RBT_WARN_PROD_SUBJECT,$RBT_DISABLED_PROD_SUBJECT;

		// change for cron job expire trial (Cooper & Quint)
		global $RBT_BEFORE_EXPIRY_SUBJECT_CQ,$RBT_AFTER_EXPIRY_SUBJECT_CQ;
		global $RBT_AFTER_EXPIRY_TMPL_COOPER,$RBT_BEFORE_EXPIRY_TMPL_COOPER,$RBT_AFTER_EXPIRY_TMPL_QUINT,$RBT_BEFORE_EXPIRY_TMPL_QUINT;

		//--timers
		$this->trialLength=$RBT_TRIAL_LENGTH;//in seconds
		$this->recurringRate=$RBT_RECURRING_RATE;
		$this->billingDelay=$RBT_BILLING_DELAY;

		$this->subsTable="subscription";
		$this->subspfTable="subscription_ps";
		$this->transTable="subscription_trans";
		$this->giftTable="subscription_keys";
		$this->fromEmail=$RBT_FROM_EMAIL;
		$this->removeSubject=$RBT_REMOVED_SUBJECT;
		$this->removeTmpl=$RBT_REMOVED_TMPL;
		$this->ccFailSubject=$RBT_CC_FAIL_SUBJECT;
		$this->ccFailTmpl=$RBT_CC_FAIL_TMPL;
		$this->disabledSubject=$RBT_DISABLED_SUBJECT;
		$this->disabledTmpl=$RBT_DISABLED_TMPL;
		$this->warnDisabledSubject=$RBT_DISABLED_SUBJECT;
		$this->warntrialDisabled=$RBT_WARN_DISABLED_SUBJECT;
		$this->warnDisabledTmpl=$RBT_OPT_TMPL;
		$this->warnDisabledTmplprod=$RBT_WARN_PROD_TMPL;
		$this->DisabledTmplpod=$RBT_DISABLED_PROD_TMPL;
		$this->warnDisabledSubjectprod=$RBT_WARN_PROD_SUBJECT;
		$this->DisabledSubjectprod=$RBT_DISABLED_PROD_SUBJECT;
		$this->downgradeSubject=$RBT_DOWNGRADE_SUBJECT;
		$this->downgradeTmpl=$RBT_DOWNGRADE_TMPL;
		$this->optTmpl=$RBT_OPT_TMPL;
		$this->fromExpireEmail=$REG_EML_TRIAL_REPLYTO;
		$this->beforeExpiryTmpl=$RBT_BEFORE_EXPIRY_TMPL;
		$this->beforeExpirySubject=$RBT_BEFORE_EXPIRY_SUBJECT;
		$this->afterExpiryTmpl=$RBT_AFTER_EXPIRY_TMPL;
		$this->afterExpirySubject=$RBT_AFTER_EXPIRY_SUBJECT;
		$this->now=time();
		//future timestamp when they should be billed next
		$this->nextBillDate=$this->calcNextBill($this->now);
                // new changes for cron job expire trial (Cooper & Quint)
		$this->beforeExpiryTmpl_cooper_two_days=$RBT_BEFORE_EXPIRY_TMPL_COOPER;
		$this->afterExpiryTmpl_cooper_two_days=$RBT_AFTER_EXPIRY_TMPL_COOPER;
		$this->beforeExpirySubjectcq=$RBT_BEFORE_EXPIRY_SUBJECT_CQ;
		$this->beforeExpiryTmpl_quint_two_days=$RBT_BEFORE_EXPIRY_TMPL_QUINT;
		$this->afterExpiryTmpl_quint_two_days=$RBT_AFTER_EXPIRY_TMPL_QUINT;
		$this->afterExpirySubjectcq=$RBT_AFTER_EXPIRY_SUBJECT_CQ;
	}
	//to get buzz and banter subscribers
	//registered between a specific dates
	function getBnBSubscribers($strdate){
		$strQuery="SELECT concat(fname,' ',lname) name,email FROM subscription
		WHERE premium='1' and cancelsubs='0' and account_status='enabled'
		and (type='prem' || type='newyear' || type='trial') and FROM_UNIXTIME(date,'%Y-%m-%d') = '{$strdate}'
		ORDER BY email";
		$strResult=exec_query($strQuery);
		$bnbsubscribers="";
		$index=0;

		if(!$strResult) return;

		foreach($strResult as $key=>$val){

			$bnbsubscribers[$index][email]=$val[email];
			$bnbsubscribers[$index][name]=$val[name];
			$index++;
		}

		return $bnbsubscribers;
	}

	function sendBnBEmails($row,$tmpl){
		global $REG_EML_REPLYTO,$REG_EML_SUBJECT;
		$body=inc_web($tmpl);
		mymail("$row[name]<$row[email]>",$REG_EML_REPLYTO,$REG_EML_SUBJECT,$body);
	}

	function trialExpiry($row,$checkexpire=0){
		 	//function will send automatic mail of expiry of trial account
			  $id=$row[subscription_id]?$row[subscription_id]:$row[id];
				if(!$id)
				{
					debug("ROBOT:trialExpiy:passed bad argument");
					return;
				}
				//set up email template
				if ($checkexpire==0)
				{
					$params=array();
					$this->beforeExpiryContent=include2str($this->beforeExpiryTmpl,$params,1);
					$to="${row[fname]} ${row[lname]} <${row[email]}>";
					mymail($to,$this->fromExpireEmail,$this->beforeExpirySubject,$this->beforeExpiryContent );
				}
				else
				{
					$params=array();
					$this->afterExpiryContent=include2str($this->afterExpiryTmpl,$params,1);
					$to="${row[fname]} ${row[lname]} <${row[email]}>";
					mymail($to,$this->fromExpireEmail,$this->afterExpirySubject,$this->afterExpiryContent );
				}

			}
function trialExpiryproduct($row,$checkexpire=0,$Buzz=""){
		 	//function will send automatic mail of expiry of trial account
			    $id=$row[subscription_id]?$row[subscription_id]:$row[id];
				if(!$id)
				{
					debug("ROBOT:trialExpiy:passed bad argument");
					return;
				}
				//set up email template
				if ($checkexpire==0)
				{
					$params=array();
					 $to="${row[fname]} ${row[lname]} <${row[email]}>";
					if($row['prof_id']=='2'){
						$this->beforeExpiryContent=include2str($this->beforeExpiryTmpl_cooper_two_days,$params,1);
						mymail($to,$this->fromExpireEmail,$this->beforeExpirySubjectcq,$this->beforeExpiryContent );
					}
					if($row['prof_id']=='3'){
						$this->beforeExpiryContent=include2str($this->beforeExpiryTmpl_quint_two_days,$params,1);
						mymail($to,$this->fromExpireEmail,$this->beforeExpirySubjectcq,$this->beforeExpiryContent );
					}
					if($Buzz){
						$this->beforeExpiryContent=include2str($this->beforeExpiryTmpl,$params,1);
						mymail($to,$this->fromExpireEmail,$this->beforeExpirySubject,$this->beforeExpiryContent );
					}

				}
				else
				{
					$params=array();

					 $to="${row[fname]} ${row[lname]} <${row[email]}>";
					if($row['prof_id']=='2'){
						$this->afterExpiryContent=include2str($this->afterExpiryTmpl_cooper_two_days,$params,1);
						mymail($to,$this->fromExpireEmail,$this->afterExpirySubjectcq,$this->afterExpiryContent );
					}
					if($row['prof_id']=='3'){
						$this->afterExpiryContent=include2str($this->afterExpiryTmpl_quint_two_days,$params,1);
						mymail($to,$this->fromExpireEmail,$this->afterExpirySubjectcq,$this->afterExpiryContent );
					}
					if($Buzz){
						$this->afterExpiryContent=include2str($this->afterExpiryTmpl,$params,1);
						$to="${row[fname]} ${row[lname]} <${row[email]}>";
						mymail($to,$this->fromExpireEmail,$this->afterExpirySubject,$this->afterExpiryContent );
					}
				}

			}

			function disableTrialAccount($table="",$row,$title="",$body="",$logo=0){
			//function will disable trial accounts which get expired
					$id=$row[subid]?$row[subscription_id]:$row[id];
					if(!$id){
						debug("ROBOT:disableTrialAccount:passed bad argument");
						return;
					}
					if($row['subid']){
						$sid=$row['subid'];
					}else{
						$sid=$row['id'];
					}
					$qry="select id from subscription_ps where account_status='enabled' and FROM_UNIXTIME(date_nextbill,'%Y-%m-%d')>FROM_UNIXTIME($this->now,'%Y-%m-%d') and type<>'trial' and subid='$sid'";

					$psqry=exec_query($qry);
					if($psqry && $table=='subscription'){
						$upd=array(
							premium=>0,
							notified_disabled=>1,
							notified_disabled_warn=>1,
							trial_status=>"expired");
							update_query($table,$upd, array(id=>$id));
					} else {
					$upd=array(
						account_status=>"disabled",
						date_disabled=>$this->now,
						premium=>0,
						notified_disabled=>1,
						notified_disabled_warn=>1,
						trial_status=>"expired");
					    update_query($table,$upd, array(id=>$id));
			}
				}

	function setSubType($type,$date=0){
		if($date==0)
			$date=time();
		if($type=="prem" || $type=="1mo"){
			$this->billingDelay = $GLOBALS[RBT_BILLING_DELAY];
			$this->recurringRate = $GLOBALS[RBT_RECURRING_RATE];
		}elseif($type=="newyear" ){
			//echo "newyear account type";
			$this->billingDelay = $GLOBALS[RBT_BILLING_DELAY_YR];
			$this->recurringRate = $GLOBALS[RBT_RECURRING_RATE_YR];
		}
		elseif($type=="edu"){
					$this->billingDelay = $GLOBALS[RBT_BILLING_DELAY];
					$this->recurringRate = $GLOBALS[RBT_RECURRING_RATE];
		}
		 elseif($type=="newyeardisc" ){
			//echo "newyear discount account type";
			$this->billingDelay = $GLOBALS[RBT_BILLING_DELAY_YR];
			$this->recurringRate = $GLOBALS[RBT_RECURRING_DISCOUNT_RT];
		}
		 /*if ($type=="newyeardisc")
		     {
		    $this->nextBillDate=$this->calcNextBill($date);
		    }
		    else
		    {*/
		    $this->nextBillDate=$this->calcNextBill($this->now);
		    //}
	}
	function setSubsType($type){ return $this->setSubType($type,$date); }//alias for spelling error, dummy

	function calcNextBill($date){
		$nextdate=($date+$this->recurringRate);
		return $nextdate;
	}
	function billOrder($row, $updateOnSuccess = 0){
		//2nd arg updates the date_lastbilled field to $this->now
		debug("ROBOT:billOrder((array)${row[id]}, $updateOnSuccess)");
		include_once(dirname(__FILE__)."/cart-yourpay/lpphp.php");
		$lphp=new lpphp();
		$res=$lphp->ApproveSale( $this->buildOrder($row) );
		$trans=$this->logTransaction($row[id],$row[email],"postauth",$row[recurring_charge], $res);
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		$old_sub=exec_query("SELECT * FROM $this->subsTable WHERE id='$id'",1);
		if($trans[success]){
			debug("ROBOT:billOrder:successful transaction");
			$upd=array(
				account_status => "enabled",
				date_disabled => 0,
				notified_disabled => 0,
				notified_disabled_warn => 0
		);
			//increment the billing cycles also. create if needed
			if($updateOnSuccess || !$old_sub[date_nextbill] || !$old_sub[date_lastbilled]){
				$upd[date_lastbilled]=$this->now;
				$upd[date_nextbill]  =$this->nextBillDate;

			}
			if(!$old_sub[date_billingstart])//create a billing start date if they didn't have one
				$upd[date_billingstart]=$this->now;
			update_query($this->subsTable, $upd ,array(id=>$id));
		}else{
			sendAlert_TransactionError($row[email],$row[tel],$trans[trans_type],$trans[trans_msg],$trans[amt_charged]);
			debug("ROBOT:billOrder:transaction didn't go through ${trans[trans_msg]}");
		}
		return $trans;
	}
	function capturePayment($row){
		debug("ROBOT:capturePayment((array)${row[id]})");
		include_once(dirname(__FILE__)."/cart-yourpay/lpphp.php");
		$lphp=new lpphp();
		$res=$lphp->CapturePayment( $this->buildOrder($row) );
		$trans=$this->logTransaction($row[id], $row[email], "preauth", $row[recurring_charge], $res );
		if($trans[success]){
			debug("ROBOT:capturePayment:successful pre-auth");
		}else{
			sendAlert_TransactionError($row[email],$row[tel],$trans[trans_type],$trans[trans_msg],$trans[amt_charged]);
			debug("ROBOT:capturePayment:unsuccessufl pre-auth:${trans[msg]}");
		}
		return $trans;
	}

	function billOrderProd($row, $updateOnSuccess = 0){
			//2nd arg updates the date_lastbilled field to $this->now
			debug("ROBOT:billOrder((array)${row[id]}, $updateOnSuccess)");
			include_once(dirname(__FILE__)."/cart-yourpay/lpphp.php");
			$lphp=new lpphp();
			$res=$lphp->ApproveSale( $this->buildOrder($row) );
			$trans=$this->logTransaction($row[subid],$row[email],"postauth",$row[recurring_charge], $res);
			$id=$row[subscription_id]?$row[subscription_id]:$row[id];
			$old_sub=exec_query("SELECT * FROM $this->subspfTable WHERE id='$id'",1);
			if($trans[success]){
				debug("ROBOT:billOrder:successful transaction");
				$upd=array(
					account_status => "enabled",
					date_disabled => 0,
					notified_disabled => 0,
					notified_disabled_warn => 0
			);
				//increment the billing cycles also. create if needed
				if($updateOnSuccess || !$old_sub[date_nextbill] || !$old_sub[date_lastbilled]){
					$upd[date_lastbilled]=$this->now;
					$upd[date_nextbill]  =$this->nextBillDate;
				}
				if(!$old_sub[date_billingstart])//create a billing start date if they didn't have one
					$upd[date_billingstart]=$this->now;

				update_query($this->subspfTable, $upd ,array(id=>$id));

				if($row[is_buzz]=="1" && $row[combo]=="1")
				{
				  $qry ="SELECT * FROM $this->subsTable WHERE id='$row[subid]'";
				  $db=new dbObj($qry);
				  while($row=$db->getRow()){
				  $this->setSubsType($row[type]);
				  $this->nextBillDate=$this->calcNextBill($row[date_nextbill]);
				  $upd[date_nextbill]  = $this->nextBillDate;
				  update_query($this->subsTable, $upd ,array(id=>$row[id]));
				}
				}

			}else{
				debug("ROBOT:billOrder:transaction didn't go through ${trans[trans_msg]}");
			}
			return $trans;
		}

	function logTransaction($id, $email, $trans_type, $amount_charged, $result){
		debug("ROBOT:logTransaction($id, $email, $trans_type, $amount_charged, $result)");
		$trans=$this->convertTransResult($result);
		$trans[subscription_id]	= $id;
		$trans[email]			= $email;
		$trans[trans_type]		= $trans_type;
		$trans[amt_charged]		= $amount_charged;
		$log_id=insert_query($this->transTable, $trans);
		$trans[id]=$log_id;
		return $trans;
	}

	function convertTransResult($trans){
		//takes return from yourpay API transaction return
		debug("ROBOT:convertTransResult($trans)");
		return array(
			success		=> $trans[statusCode]?1:0,
			trans_id  	=> $trans[orderID],
			trans_msg	=> $trans[statusMessage],
			trans_code	=> $trans[trackingID]
		);
	}
	function buildOrder($row){
 		$row["subtotal"] = $row["recurring_charge"];
 		$row["amount"] = $row["recurring_charge"];
 		$row["total"] = $row["recurring_charge"];

		$map=array(
			b_email=>"email",b_fname=>"fname",	b_lname=>"lname",
			b_address=>"address",b_address2=>"address2",
			b_city=>"city",b_state=>"state",
			b_zip=>"zip",b_country=>"country",
			ip=>"ip",
			#tax_total=>"0",total=>"recurring_charge",subtotal=>"recurring_charge",
			tax_total=>"0"
			#amount=>"recurring_charge"
		);
		foreach($map as $new_key=>$old_key){
			$row[$new_key]=$row[$old_key];
			unset($row[$old_key]);
		}
		$row[trans_id]=Cart::getOrderId("SUB_");//unique order id
		return yourpayOrder($row);
	}
	function runStaleTransaction($row){
		debug("ROBOT:->runStaleTransaction($subs_id)");
		//this notifies the user, removes the account(completely+links)
		//frees up the gift code (if applicable)

		//cache template in memory in case another one wants to use it.
		if(!$this->removeTmplContent)
			$this->removeTmplContent=include2str($this->removeTmpl);

		//notify user of account removal
		$toemail="${row[fname]} ${row[lname]} <${row[email]}>";
		mymail($toemail,$this->fromEmail,$this->removeSubject,$this->removeTmplContent);

		//remove subscription and links from database
		del_query($this->subsTable, "id",$row[id]);
		del_query($this->transTable,"subscription_id",$row[id]);
		del_query($this->giftTable,"user_id",$row[id]);
	}
	function removeAccount($row,$title,$body,$logo=0){
		debug("ROBOT:removeAccount($row,$title,$body)");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:removeAccount:passed bad argument");
			return;
		}
		if(!$this->removedContent){
			$params=array(title=>$title,body=>$body);
			if($logo)$params["HEADER"]=$logo;
			$this->removedContent=include2str($this->optTmpl,$params,1);
		}
		$to="${row[fname]} ${row[lname]} <${row[email]}>";
		mymail($to,$this->fromEmail, $title, $this->removedContent );
		//remove subscription and links from database
		del_query($this->subsTable, "id",$id);
		del_query($this->transTable,"subscription_id",$id);
		del_query($this->giftTable,"user_id",$id);
	}

	function disableAccount($row,$title="",$body=""){
		debug("ROBOT:disableAccount($row,$title,$body)");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:disableAccount:passed bad argument");
			return;
		}
		$upd=array(
			account_status=>"disabled",
			date_disabled=>$this->now,
			notified_disabled=>1,
			notified_disabled_warn=>1
		);
		if(!$row[notified_disabled]){
			if(!$this->disabledContent)
				$this->disabledContent=include2str($this->warnDisabledTmpl,array(title=>$title,body=>$body),1);
			$to="${row[fname]} ${row[lname]} <${row[email]}>";
			mymail($to,$this->fromEmail,$this->disabledSubject,	$this->disabledContent );
			$upd[notified_disabled]=1;
		}
		update_query($this->subsTable,$upd, array(id=>$id));
	}

	function disableAccountProd($row,$title="",$body=""){
global $HTHOST, $HTPFX;
			debug("ROBOT:disableAccount($row,$title,$body)");
			$id=$row[subscription_id]?$row[subscription_id]:$row[id];
			if(!$id){
				debug("ROBOT:disableAccount:passed bad argument");
				return;
			}
			$upd=array(
				account_status=>"disabled",
				date_disabled=>$this->now,
				notified_disabled=>1,
				notified_disabled_warn=>1
			);
			if(!$row[notified_disabled]){
		//			$this->disabledContent=include2str($this->DisabledTmplpod,array(title=>$title,body=>$body,prof_id=>$row[prof_id],combo_id=>$row[combo_id]),1);
$prof_id=$row['prof_id'];
$combo_id=$row['combo_id'];
$path="/emails/_eml_cooper_disable.htm";
$DIS_EML_TMPL=$HTPFX.$HTHOST.$path;
				$to="${row[fname]} ${row[lname]} <${row[email]}>";
$template="$DIS_EML_TMPL?prof_id=$prof_id&combo_id=$combo_id";
				$to="${row[fname]} ${row[lname]} <${row[email]}>";
				mymail($to,$this->fromEmail,$this->DisabledSubjectprod,inc_web($template));
				$upd[notified_disabled]=1;
			}
			$old_sub=exec_query("SELECT * FROM $this->subsTable WHERE id='$row[subid]'",1);
			if(($row['date_nextbill']==$old_sub['date_nextbill']) && $row[is_buzz]=="1")
			{
					update_query($this->subsTable, $upd ,array(id=>$row[subid]));
			}
			else{
				$subs['recurring_charge']=getSubsTotal($old_sub);
				$subs['combo']='0';
				$subs['cancelsubs']='0';
				update_query($this->subsTable, $subs ,array(id=>$row[subid]));
			}
			update_query($this->subspfTable,$upd, array(id=>$id));
	}

	function warnDisableAccount($row,$title="",$body="",$logo=0){
	//function will set the disable date
	//robot scripts pick up subs that have a disable date of >=3 days
		debug("ROBOT:warnDisableAccount($row,$title,$body)");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:warnDisableAccount:passed bad argument");
			return;
		}
		$upd=array(	date_disabled=>$this->now) ;
		//set up email template
		if(!$row[notified_disabled_warn]){
			if(!$this->warnDisabledContent){
				$params=array(title=>$title,body=>$body);
				if($logo)$params["HEADER"]=$logo;
				$this->warnDisabledContent=include2str($this->warnDisabledTmpl,$params,1);
			}
			$to="${row[fname]} ${row[lname]} <${row[email]}>";
			mymail($to,$this->fromEmail,$this->warnDisabledSubject,	$this->warnDisabledContent );
			$upd[notified_disabled_warn]=1;
		}
		//update data
		update_query($this->subsTable,$upd, array(id=>$id));
	}

	function warnDisableAccountProd($row,$title="",$body="",$logo=0){
global $HTPFX,$HTHOST;
		//function will set the disable date
		//robot scripts pick up subs that have a disable date of >=3 days
			debug("ROBOT:warnDisableAccount($row,$title,$body)");
			$id=$row[subscription_id]?$row[subscription_id]:$row[id];
			if(!$id){
				debug("ROBOT:warnDisableAccount:passed bad argument");
				return;
			}
			$upd=array(	date_disabled=>$this->now) ;
			//set up email template
			if(!$row[notified_disabled_warn]){
					$params=array(title=>$title,body=>$body,prof_id=>'3',combo_id=>$row[combo_id]);									if($logo)$params["HEADER"]=$logo;
					//$this->warnDisabledContent=include2str($this->warnDisabledTmplprod,$params,1);
$prof_id=$row['prof_id'];
$combo_id=$row['combo_id'];
$path="/emails/_eml_cooper_warndisable.htm";
$DIS_EML_TMPL=$HTPFX.$HTHOST.$path;
				$to="${row[fname]} ${row[lname]} <${row[email]}>";
$template="$DIS_EML_TMPL?prof_id=$prof_id&combo_id=$combo_id";
				mymail($to,$this->fromEmail,$this->warnDisabledSubjectprod,inc_web($template));
				$upd[notified_disabled_warn]=1;
			}
			//update data
			$old_sub=exec_query("SELECT * FROM $this->subsTable WHERE id='$row[subid]'",1);
			if(($row['date_nextbill']==$old_sub['date_nextbill']) && $row[is_buzz]=="1")
			{
				$upd['combo']='0';
				$upd['cancelsubs']='1';
				update_query($this->subsTable, $upd ,array(id=>$row[subid]));
			}else{
				$subs['recurring_charge']=getSubsTotal($old_sub);
				$subs['combo']=0;
				update_query($this->subsTable, $subs ,array(id=>$row[subid]));
			}
			update_query($this->subspfTable,$upd, array(id=>$id));
	}

	function enableAccount($row){
		debug("ROBOT:enableAccount($row)");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:disableAccount:passed bad argument");
			return;
		}
		$upd=array(
			date_disabled=>0,
			account_status=>"enabled",
			notified_disabled=>0,
			notified_disabled_warn=>0
		);
		update_query($this->subsTable,$upd, array(id=>$id));
	}
	function downgradeAccount($row,$title="",$body="",$logo=""){
		global $REG_PREMIUM_CHARGE,$REG_PREMIUM_CHARGE_YR;
		$charge=$row[type]=="12mo"?$REG_PREMIUM_CHARGE_YR:$REG_PREMIUM_CHARGE;
		debug("ROBOT:downgradeAccount($row,$title,$body)");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:disableAccount:passed bad argument");
			return;
		}
		//set up email
		if(!$this->downgradeContent){
			$params=array(title=>$title,body=>$body);
			if($logo)
				$params["HEADER"]=$logo;
			$this->downgradeContent=include2str($this->downgradeTmpl,$params,1);
		}
		$to="${row[fname]} ${row[lname]} <${row[email]}>";
		mymail($to,$this->fromEmail,$title,	$this->downgradeContent );
		//update db
		$upd=array(
			premium=>0,
			recurring_charge=>$row[recurring_charge]-$charge
		);
		if($upd[recurring_charge]<0)$upd[recurring_charge]=0;

		update_query($this->subsTable,$upd, array(id=>$id));
	}
	function convertAndDisableAccount($row,$title="",$body="",$logo=0){
		//function converts a 12mo account to prem, disables account,
		//notifies user, and resets billing charge
		debug("ROBOT:convertAndDisableAccount($row,$title,$body,$logo)");
		$charges=getSubsCharges("prem");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:convertAndDisableAccount:passed bad argument");
			return;
		}
		//set up email
		if(!$this->convertContent){
			$params=array(title=>$title,body=>$body);
			if($logo)$params["HEADER"]=$logo;
			$this->convertContent=include2str($this->optTmpl,$params,1);
		}
		$to="${row[fname]} ${row[lname]} <${row[email]}>";
		mymail($to,$this->fromEmail,$title,$this->convertContent);
		//update user data
		$upd=array(
			type=>"prem",
			premium=>"1",
			recurring_charge=>$charges[monthly]+$charges[premium],
			date_disabled=>$this->now,
			notified_disabled=>1,
			notified_disabled_warn=>0,
			account_status=>"disabled",
			expires=>0
		);
		update_query($this->subsTable,$upd,array(id=>$id));
	}

	function convertAccount($row){
		//function converts a 12mo account to 1mo, disables account,
		// and resets billing charge
		//does not do any notification!!
		debug("ROBOT:convertAccount($row)");
		$charges=getSubsCharges("prem");
		$id=$row[subscription_id]?$row[subscription_id]:$row[id];
		if(!$id){
			debug("ROBOT:disableAccount:passed bad argument");
			return;
		}
		//set up email
		$upd=array(
			type=>"prem",
			premium=>"1",//--new accounts have premium services automagically
			recurring_charge=>$charges[monthly]+$charges[premium],
			date_nextbill=>$this->now,
			date_disabled=>0,
			notified_disabled=>0,
			notified_disabled_warn=>0,
			expires=>0
		);
		update_query($this->subsTable,$upd,array(id=>$id));
	}


	function runInitialTransaction($row){
		debug("ROBOT:runInitialTransaction($row)");
		if(!count($row))
			return 0;

	}
	function flushOutput(){
		//this just spits some data to the screen to appear as if it's thinking
		flush(); echo str_repeat("\n",256); flush();
	}
	function isCloseToCycle($nextBillDate){
		debug("ROBOT:isCloseToCycle($nextBillDate)");
		//function tells if $this->now is within $this->billingDelay from $nextBillDate
		$diff=$nextBillDate - $this->now;
		if($diff<=0){
			debug("ROBOT:isCloseToCycle: nextBilldate is in the past: $diff");
			//date is in the past
			return 0;
		}
		if($diff <= $this->billingDelay){
			//nextBillDate is within $billingDelay timeframe
			debug("ROBOT:isCloseToCycle: nextBillDate >=$this->billingDelay secs.");
			return 1;//it's close to the cycle!
		}
		if( $diff > $this->billingDelay ){
			//next billdate is greater than billingDelay
			debug("ROBOT:isCloseToCycle: nextBillDate > $this->billingDelay secs.");
			return 0;
		}
		return 0;
	}
}

// class end 

function getSubsCharges($type="prem"){
	//returns hash of charging parameters
	global $REG_PREMIUM_CHARGE, $REG_PREMIUM_CHARGE_YR,$REG_PREMIUM_CHARGE_EDU;
	global $REG_MONTHLY,$REG_MONTHLY_YR,$REG_MONTHLY_EDU;
	global $REG_MONTHLY_NEWPREM,$REG_PREMIUM_CHARGE_NEWPREM;
	global $REG_CHARGE_YR2,$REG_CHARGE_YR_DISCOUNT;
	$default_type="prem";
	$types=array(
		"1mo"=>array(
			monthly => $REG_MONTHLY,
			premium => $REG_PREMIUM_CHARGE,
			annual  => 0
		),
		"edu"=>array(
			monthly => $REG_MONTHLY_EDU,
			premium => $REG_PREMIUM_CHARGE_EDU,
			annual  => 0
		),
		"12mo"=>array(
			monthly => $REG_MONTHLY_YR,
			premium => $REG_PREMIUM_CHARGE_YR,
			annual  => 0
		),
		"prem"=>array(//--includes premium
			monthly => $REG_MONTHLY_NEWPREM,
			premium => 0,
			annual  => 0
		),
		"newyear"=>array(
			monthly=>0,
			premium=>0,
			annual=>$REG_CHARGE_YR2
		)/*,
		"newyeardisc"=>array(
			monthly=>0,
			premium=>0,
			annual=>$REG_CHARGE_YR_DISCOUNT
		)*/
	);
	if(!$type || !$types[$type]){
		$ret=$types[$default_type];
	}else{
		$ret=$types[$type];
	}
	$ret[total]=($ret[monthly]+$ret[premium]+$ret[annual]);
	return $ret;
}

function getSubsTotal($sub){
	$type=$sub[type];
	if($sub[type]=="1mo" && $sub[premium])
		$type="prem";
	$charges=getSubsCharges($type);
	if($sub[type]=="1mo" && !$sub[premium]){
		$charges[total]=$charges[monthly];
	}
	return $charges[total];
}

function getSubsTotalProf($sub,$profid){

	$type=$sub[type];
	$charges=getSubsChargesProf($type,$profid);
	return $charges;
}


function getSubsChargesProf($type="prem",$profid){

if($type=="prem")
{
	$rs = exec_query("select recurring_charge_mon from product where id= '$profid'" ,1);
	$ret=$rs[recurring_charge_mon];

}
else
{
	$rs = exec_query("select recurring_charge_annual from product where id= '$profid'" ,1);
	$ret=$rs[recurring_charge_annual];
}

return $ret;
}


function getSubsTotalCombo($sub,$profid){

	$type=$sub[type];
	$charges=getSubsChargesCombo($type,$profid);
	return $charges;
}


function getSubsChargesCombo($type="newyear",$profid){

if($type=="prem")
{
	$rs = exec_query("select recurring_charge from option_deals where id= '$profid'" ,1);
	$ret=$rs[recurring_charge];

}
else
{
	$rs = exec_query("select recurring_charge from option_deals where id= '$profid'" ,1);
	$ret=$rs[recurring_charge];

}
return $ret;
}


/*** funstion returns old rates**/
function getSubsOldCharges($type){

	global $REG_MONTHLY_OLDPREM,$REG_CHARGE_YR_DISCOUNT;
	if ($type == "prem"){
		$amt_charges=$REG_MONTHLY_OLDPREM;
	} else if ($type="newyear"){
		$amt_charges=$REG_CHARGE_YR_DISCOUNT;
	}

	return $amt_charges;
}

function getSubsTrialDiscCharges($sub){
	global $REG_MONTHLY_NEWPREM,$REG_CHARGE_YR_DISCOUNT;
	$type=$sub[type];
	if ($type == "prem"){
		$amt_charges=$REG_MONTHLY_NEWPREM;
	} else if ($type="newyear"){
		$amt_charges=$REG_CHARGE_YR_DISCOUNT;
	}

	return $amt_charges;
}

/**  added  to convert the date in to Unixtime stamp **/
function convert_date_to_unix($thisdate)
   {
   $date_in_unix=exec_query("SELECT UNIX_TIMESTAMP('$thisdate') as dateinunix",1);
   return $date_in_unix[dateinunix];
   }

/**  added  to check whether the user has availed discount or not **/
function is_discount_availed($id,$reg_date)
 {

  global $REG_CHARGE_YR_DISCOUNT,$RBT_EXISTING_USR_DATE,$RBT_DISCOUNT_EXP_DATE;
  $discount_avail=0;
  $current_date= time() ;

  $discount_availed_trans=exec_query("SELECT amt_charged FROM subscription_trans WHERE subscription_id='$id' and amt_charged=$REG_CHARGE_YR_DISCOUNT and success='1'",1);

	    if($discount_availed_trans[amt_charged]==$REG_CHARGE_YR_DISCOUNT)
			 $discount_avail=1;
		 else
			 $discount_avail=0;

     return $discount_avail;
  }

///** checks whther the user is existing or not 1.e before registration date before 30 sep**/
   function is_existing_user($date)
    {
	  global $RBT_EXISTING_USR_DATE,$RBT_DISCOUNT_EXP_DATE;
	  $current_date= time() ;
	  if (midnight($date) <= convert_date_to_unix($RBT_EXISTING_USR_DATE) &&
	  midnight($current_date) <= convert_date_to_unix($RBT_DISCOUNT_EXP_DATE))
		   $flag_existing_usr=1;
		   else
	        $flag_existing_usr=0;

    return  $flag_existing_usr;
   }

/**check whether to give discount option for trial users or not**/
 function is_discount_trial($type)
    {
	  global $RBT_TRIAL_DISC_DATE;
	  $current_date= time() ;
	  if ($type == "trial" && midnight($current_date) <= convert_date_to_unix($RBT_TRIAL_DISC_DATE))
		   $flag_disc_trial=1;
		   else
	       $flag_disc_trial=0;

    return $flag_disc_trial;
   }

 function retrieve_products_body($prof_id,$combo_id)
 {
 	if($prof_id){
		$bodyQry = exec_query("select name, description from product where id = '$prof_id'" ,1);}
	elseif($combo_id){
		$bodyQry = exec_query("select description from option_deals where id = '$combo_id'" ,1);}
	return $bodyQry;
	}

 function sendAlert_TransactionError($loginEmail,$phone,$transType,$errorMessage,$amount)
 {
	global $CREDIT_CARD_PAYMENT_ERROR_TO, $CREDIT_CARD_PAYMENT_ERROR_FROM,$TransactionError_EmailAlert_Template;
	$errorCode='';
	$errorMsg='';
	if($errorMessage=='<')
	{
		$errorMsg="Your pay server did not respond";
		$errorCode='';
	}
	else
	{
		list($errorCode,$errorMsg)=explode(":", $errorMessage, 2);
	}
	$EML_TMPL=$TransactionError_EmailAlert_Template."?loginEmail=$loginEmail&phone=$phone&transType=$transType&errorCode=$errorCode&errorMsg=".urlencode(trim($errorMsg))."&amount=".$amount."";
	$to=$CREDIT_CARD_PAYMENT_ERROR_TO;
	$from=$CREDIT_CARD_PAYMENT_ERROR_FROM;
	$subject="Transaction Error - ".$loginEmail." - $".$amount;
	mymail($to,$from,$subject,inc_web("$EML_TMPL"));
 }
?>
