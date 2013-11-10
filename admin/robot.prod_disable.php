<?include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
/*=========DISABLE ACCOUNT ROBOT===================

will grab all non-gift disabled accounts whose disable date is >3days old and disables the accounts while sending user notification. these users must have a recurring charge associated with the account

--exclude 12 mo subscriptions
*/
echo "<b>DISABLE ROBOT ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";
$mailtitle="Subscription Disabled";
$mailmsg='<p>Your subscription to Jeff Cooper\'s   Daily Market Report has been disabled. We hope this was just a small oversight and you choose to continue your subscription.</p>
<p>To continue your subscription,  please click here or login to <a href="HTTP://'.$HTHOST.'" target="_blank">Minyanville.com</a> and click on "Manage Account". Choose the type of subscription you\'d like and be sure to click "Submit" at the end instead of "Change". Once that is processed correctly you should automatically have access to the product.</p>
<p>For any problems or questions, or if you believe you\'ve received this email in error, please contact Josh Sander at <a href="mailto:sander@minyanville.com" target="_blank"><u>sander@minyanville.com</u></a> or 212-991-9357.</p>';

$robot=new Robot();
$now=$robot->now;
$disabledate=$now-$RBT_GRACE_PERIOD;//disable date should be <= this date
$time_fmt = "%m/%d/%Y";
$qry="SELECT *, id subscription_id
	 FROM $robot->subspfTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND date_disabled!='0'
	 AND (FROM_UNIXTIME(date_disabled,'%Y-%m-%d') <= FROM_UNIXTIME($disabledate,'%Y-%m-%d')
	 OR date_disabled < 0)";

 
$db=new dbObj($qry);
while($row=$db->getRow()){
	$hasrecs=1;
	$robot->flushOutput();
	$robot->disableAccountProd(&$row, $mailtitle, $mailmsg);
	echo "${row[email]} disabled<br>";	
}


if(!$hasrecs){
	echo "<i>no accounts to disable</i><br>";
}

?>