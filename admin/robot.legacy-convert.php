<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour

/*====================================
LEGACY ROBOT
converts premium + 12mo accounts to 1mo(w/ price adjustment). nulls out exiration date.
====================================*/
echo "<b>CONVERT LEGACY ROBOT: ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";
$robot=new Robot();
$now=$robot->now;
$qry="SELECT *,id subscription_id
	  FROM $robot->subsTable 
	  WHERE preferred_user='0'
	  AND account_status='enabled'
	  AND type='12mo'
	  AND expires!='0' 
	  AND premium='1'
	  AND UNIX_TIMESTAMP(FROM_UNIXTIME(expires))<= $now ";

$db=new dbObj($qry);
$mailtitle="Account Disabled";
$mailmsg="Your 1 year subscription to Minyanville.com has expired. We hope you will choose to continue enjoying Minyanville.  To re-subscribe, please go and ".href($MANAGE_URL,"mange your expired account")." to sign up for our monthly subscription.  We no longer offer an annual option.  Thank You!<br><br>
(<i>$MANAGE_URL</i>)";


while( $row=$db->getRow() ){
	$hasrecs=1;
	
	echo "converting ${row[email]}<br>";
	$robot->flushOutPut();
	$robot->convertAccount($row);
}

if(!$hasrecs){
	echo "<i>no legacy subscriptions found</i><br>";
}


?>