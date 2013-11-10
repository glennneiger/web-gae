<?include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour

/*====================================
STALE USERS
this looks for users whose accounts have been disabled for more than three days
- removes users whose accounts have been disabled for more than x days and removes them
- removes all references to the account
====================================*/
echo "<b>STALE USERS ROBOT ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";

$robot=new Robot();
$now=$robot->now;
$killdate=$now-$RBT_STALE_LENGTH;
$qry="SELECT *,id subscription_id 
	  FROM $robot->subsTable 
	  WHERE account_status='disabled'
	  AND preferred_user='0'
	  AND date_disabled!='0'
	  AND UNIX_TIMESTAMP(FROM_UNIXTIME(date_disabled)) <= $killdate";


$db=new dbObj($qry);

while($row=$db->getRow()){
	$hasrecs=1;
	echo "removed: <b>${row[email]}</b><br>";
	$robot->flushOutput();
	$trans=$robot->runStaleTransaction($row);
}

if(!$hasrecs){
	echo "<i>no stale users found</i><br>";
}
?>