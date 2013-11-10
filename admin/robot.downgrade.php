<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour

/*====================================
ACCOUNT DOWNGRADE ROBOT
takes 1yr subscribers w/ premium services and downgrades their accounts to remove/maintain premium services. tries to run the charges and if the charge fails it removes premium services from their accounts and sets their recurring rate to (recurring_charge-$REG_PREMIUM_CHARGE or 0)

if the charge goes through it just changes the date_nextbill field & logs transaction
====================================*/
echo "<b>DOWNGRADE ROBOT - 12mo BILLING: ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";
$robot=new Robot();
$now=$robot->now;
$qry="SELECT *,id subscription_id
	  FROM $robot->subsTable 
	  WHERE preferred_user='0'
	  AND account_status='enabled'
	  AND type='12mo'
	  AND premium='1'
	  AND recurring_charge>0
	  AND date_nextbill!='0' 
	  AND UNIX_TIMESTAMP(FROM_UNIXTIME(date_nextbill)) <= $now ";

$db=new dbObj($qry);
$mailtitle="Account Downgraded";
$mailmsg="We're sorry, there was a problem with your credit card this month.  Your premium services have been disabled.  To re-instate your premium services, please ".href($MANAGE_URL,"manage your account")." and  update your credit card information. Thank you!
<br><br>
(<i>$MANAGE_URL</i>)
";

while( $row=$db->getRow() ){
	$hasrecs=1;
	$trans=$robot->billOrder($row, 1);//2nd flag updates date_lastbilled field if successful
	if(!$trans[success]){//transaction failed downgrade account, notify user
		$robot->downgradeAccount(&$row,$mailtitle, $mailmsg,"/assets/MAEimage.jpg");
		echo "${row[email]} downgraded: ${trans[msg]}<br>";
	}else{
		echo "${row[email]} billed: ${trans[trans_id]}<br>";
	}
	$robot->flushOutPut();
}

if(!$hasrecs){
	echo "<i>No accounts to downgrade</i><br>";
}



?>