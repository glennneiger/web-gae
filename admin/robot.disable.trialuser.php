<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
$robot=new Robot();
$now=$robot->now;
$daynow=$now+day(1);
// $weeknow=$now-week(1);
$twodaynow=$now-day(2);

//*************Disable trial accounts, whose expire period ends.***//

$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND FROM_UNIXTIME(expires,'%Y-%m-%d') < FROM_UNIXTIME($now,'%Y-%m-%d')";
	 
$table=$robot->subsTable;
$db=new dbObj($qry);
while($row=$db->getRow())
{

	$hasrecs=1;
	$robot->disableTrialAccount($table,$row);
	echo "${row[email]} has been disabled<br>";

}
//************End disable Buzz & Banter trial account*************//

//***********Code start disable Jeff cooper and quint trial account***********//

$qry="SELECT *, id subscription_id
	 FROM $robot->subspfTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND (prof_id=2 or prof_id=3)
	 AND FROM_UNIXTIME(expires,'%Y-%m-%d') < FROM_UNIXTIME($now,'%Y-%m-%d')";
	 

$table=$robot->subspfTable;
$db=new dbObj($qry);
while($row=$db->getRow())
{
	$hasrecs=1;
	$robot->disableTrialAccount($table,$row);
	echo "${row[email]} has been disabled<br>";
}

//****************************End disable Jeff cooper and Quint trial account***********//
// no need to add extra code for quint trial

//*****************************End of disable trial account**************************//

//*************Query to retrieve trial users accounts, who had expired two days ago.***//
// for B & B
$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND trial_status='expired'
	 AND type='trial'
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($twodaynow,'%m/%d/%y')";
     $Buzz=1;
$db=new dbObj($qry);
while($row=$db->getRow())
{
	$hasrecs=1;
	$robot->trialExpiryproduct($row,1,$Buzz);
	echo "${row[email]} had already expired two days ago<br>";

}


//********************code start for jeff cooper & quint trial users accounts, who had expired two days ago.******************//
$qry="SELECT *, id subscription_id
	 FROM $robot->subspfTable
	 WHERE preferred_user='0'
	 AND trial_status='expired'
	 AND type='trial'
	 AND (prof_id=2 or prof_id=3)
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($twodaynow,'%m/%d/%y')";

$db=new dbObj($qry);
while($row=$db->getRow())
{
	$hasrecs=1;
	$robot->trialExpiryproduct($row,1);
	echo "${row[email]} had already expired two days ago<br>";

}

//*****************************End of sending one week expiry mail**************************//

//*************Query to retrieve trial users accounts, who are going to get expire after 2 days.***//
// for B & B
$qry="SELECT *, id subscription_id
	 FROM $robot->subsTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($daynow,'%m/%d/%y')";
$Buzz=1;	
$db=new dbObj($qry);
while($row=$db->getRow())
{

	$hasrecs=1;
	$upd=array(
			notified_disabled_warn=>1
	);
	update_query(subscription,$upd,array(id=>$row[id]));
	$robot->trialExpiryproduct($row,0,$Buzz);
	echo "${row[email]} will get expire after two days<br>";

}

//********************End of sending  Buzz & Banter 2 day expiry mail**************************//

$qry="SELECT *, id subscription_id
	 FROM $robot->subspfTable
	 WHERE preferred_user='0'
	 AND account_status='enabled'
	 AND trial_status='active'
	 AND type='trial'
	 AND expires!='0'
	 AND (prof_id=2 or prof_id=3)
	 AND FROM_UNIXTIME(expires,'%m/%d/%y') = FROM_UNIXTIME($daynow,'%m/%d/%y')";
	 
$db=new dbObj($qry);
while($row=$db->getRow())
{

	$hasrecs=1;
	$upd=array(
			notified_disabled_warn=>1
	);
	update_query(subscription_ps,$upd,array(id=>$row[id]));
	$robot->trialExpiryproduct($row,0);
	echo "${row[email]} will get expire after two days<br>";

}

//*****************************End of sending 2 day expiry mail**************************//

if(!$hasrecs){
	echo "No trial users found";
}

?>