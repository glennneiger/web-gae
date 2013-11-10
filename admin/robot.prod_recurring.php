<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
$msgdate=date("m/d/Y");
$mailtitle="Subscription Will Be Disabled";
$mailmsg='<p>Unfortunately we had problems   charging your credit card while trying to renew your subscription to Jeff Cooper\'s Daily Market Report. These problems could be caused by an expired card on file or the card being decline for some reason.</p>
<p>To update your credit card information, please click here, or login to <a href="HTTP://'.$HTHOST.'" target="_blank">Minyanville.com</a> and click "Manage Account" Be sure to click "Submit" at the end and not "Change"</p>
<p>If your payment information is not update, your account will be disabled within 3 days from the date of this email.</p>
<p>For any problems or questions, or if you believe you\'ve received this email in error, please contact Josh Sander at <a href="mailto:sander@minyanville.com" target="_blank"><u>sander@minyanville.com</u></a> or 212-991-9357.</p>';

$robot=new Robot();
$now=$robot->now;
//*************Query to retrieve enabled prem,newyear accounts whose billing date has been reached***//

$qry="SELECT *, id subscription_id
	 FROM subscription_ps
	 WHERE preferred_user='0'
	 AND recurring_charge>0
	 AND account_status='enabled'
	 AND (type='prem' OR type='newyear')
	 AND date_nextbill!='0'
	 AND date_disabled='0'
	 AND FROM_UNIXTIME(date_nextbill,'%Y-%m-%d')<=FROM_UNIXTIME($now,'%Y-%m-%d')";

$db=new dbObj($qry);
while($row=$db->getRow()){
  	$hasrecs=1;
	$robot->setSubsType($row[type]);
	$total=$row[recurring_charge];
	$trans=$robot->billOrderProd($row ,1);
	if(!$trans[success]){//transaction failed disable account
		$robot->warnDisableAccountProd(&$row, $mailtitle, $mailmsg,"/assets/MAEimage.jpg");
		echo "${row[email]} flagged for suspension: ${trans[trans_msg]}<br>";
	}else{

		if($row[recurring_charge]<>$REG_CHARGE_YR2 && $row[type]=="newyear")
		{
			$upd=array(
						recurring_charge=>$REG_CHARGE_YR2
			);
			update_query(subscription,$upd,array(id=>$row[id]));
		}
		echo "${row[email]} billed: ${trans[trans_id]}<br>";
	}
}

if(!$hasrecs){
	echo "No recurring subscriptions found";
}

?>