<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
/*=========DISABLE ACCOUNT ROBOT===================

will grab all non-gift disabled accounts whose disable date is >3days old and disables the accounts while sending user notification. these users must have a recurring charge associated with the account

--exclude 12 mo subscriptions
*/
echo "<b>DISABLE ROBOT ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";
$mailtitle="Account Disabled";
$mailmsg="Your account has been disabled. Please ".href($MANAGE_URL,"Manage Your Account")." to fix the problem<br>
(<i>$MANAGE_URL</i>)";

$robot=new Robot();
$now=$robot->now;
$disabledate=$now-$RBT_GRACE_PERIOD;//disable date should be <= this date
$time_fmt = "%m/%d/%Y";
$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND date_disabled!='0'
	 AND combo='0'
	 AND (FROM_UNIXTIME(date_disabled,'%Y-%m-%d') <= FROM_UNIXTIME($disabledate,'%Y-%m-%d')
	 OR date_disabled < 0)";


$db=new dbObj($qry);
while($row=$db->getRow()){
	$hasrecs=1;
	$robot->flushOutput();
	$robot->disableAccount(&$row, $mailtitle, $mailmsg);
	echo "${row[email]} disabled<br>";
}


if(!$hasrecs){
	echo "<i>no accounts to disable</i><br>";
}

?>