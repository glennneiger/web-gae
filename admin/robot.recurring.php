<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
$msgdate=date("m/d/Y");
$mailtitle="Account Will Be Disabled";
$mailmsg="<b>Date</b>: $msgdate
<br><br>
We're sorry, but there was a problem with your credit card this month.  In order to continue enjoying Minyanville, you will need to ".href($MANAGE_URL,"manage your account")." and correct the credit card problem or your account will be disabled 3 days from the date of this email.
<br><br>
<b>Manage Account</b>: $MANAGE_URL";
$robot=new Robot();
$now=$robot->now;

//*************Query to retrieve enabled prem,newyear accounts whose billing date has been reached***//

$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND recurring_charge>0
	 AND account_status='enabled'
	 AND (type='prem' OR type='newyear')
	 AND date_nextbill!='0'
	 AND date_disabled='0'
	 AND combo='0'
	 AND FROM_UNIXTIME(date_nextbill,'%Y-%m-%d')<=FROM_UNIXTIME($now,'%Y-%m-%d')";


$db=new dbObj($qry);
while($row=$db->getRow()){
  	$hasrecs=1;
	$robot->setSubsType($row[type]);
	$total=$row[recurring_charge];
	$trans=$robot->billOrder($row ,1);
	if(!$trans[success]){//transaction failed disable account
		$robot->warnDisableAccount(&$row, $mailtitle, $mailmsg,"/assets/MAEimage.jpg");
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