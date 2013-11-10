<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
$robot=new Robot();
$now=$robot->now;
$daynow=$now+day(1);
$weeknow=$now-week(1);



//*************Disable trial accounts, whose expire period ends.***//

$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND FROM_UNIXTIME(expires,'%Y-%m-%d') < FROM_UNIXTIME($now,'%Y-%m-%d')";

$db=new dbObj($qry);
while($row=$db->getRow())
{

	$hasrecs=1;
	$robot->disableTrialAccount($row);
	echo "${row[email]} has been disabled<br>";

}

//*****************************End of disable trial account**************************//

//*************Query to retrieve trial users accounts, who had expired one week ago.***//

$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='disabled'
	 AND trial_status='expired'
	 AND type='trial'
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($weeknow,'%m/%d/%y')";

$db=new dbObj($qry);
while($row=$db->getRow())
{
	$hasrecs=1;
	$robot->trialExpiry($row,1);
	echo "${row[email]} had aleday expired one week ago<br>";

}

//*****************************End of sending one week expiry mail**************************//

//*************Query to retrieve trial users accounts, who are going to get expire after 2 days.***//

$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($daynow,'%m/%d/%y')";

$db=new dbObj($qry);
while($row=$db->getRow())
{

	$hasrecs=1;
	$upd=array(
			notified_disabled_warn=>1
	);
	update_query(subscription,$upd,array(id=>$row[id]));
	$robot->trialExpiry($row,0);
	echo "${row[email]} will get expire after two days<br>";

}

//*****************************End of sending one day expiry mail**************************//

if(!$hasrecs){
	echo "No trial users found";
}

?>