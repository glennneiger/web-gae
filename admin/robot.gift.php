<?include_once("$DOCUMENT_ROOT/lib/_includes.php");
include("$D_R/lib/_robots.php");
set_time_limit ( 60*30 );//1 hour
echo "<b>GIFT USER REMOVAL ".date("m/d/Y h:i:sa").($is_dev?" DEV SERVER":"")."</b><br>";
/*=========GIFT USER REMOVAL===================

grabs all disabled accounts who's lastbilled date was $RBT_GIFT_LENGTH minutes ago today
bills the order logs transaction and updates lastbilled date

--exclude 12 mo subscriptions
*/
set_time_limit ( 60*30 );//1 hour
$signup="$HTNOSSLDOMAIN/register/new";
$mailtitle="Subscription Expired";
$mailmsg="Your gift subscription has expired. If you wish to continue enjoying Minyanville, please ".href($signup,"sign up")."!<br><br>(<i>$signup</i>)";


$robot=new Robot();
$now=$robot->now;
$time1mo=$now-$RBT_GIFT_LENGTH;
$time3mo=$now-month(3);
$time6mo=$now-month(6);
$time12mo=$now-year();
$sqltime="UNIX_TIMESTAMP(FROM_UNIXTIME(s.date))";
$qry="SELECT s.*, s.id subscription_id,FROM_UNIXTIME(s.date)mdate,sk.key1
	 FROM $robot->subsTable s, subscription_keys sk
	 WHERE type='gift'
	 AND s.preferred_user='0'
	 AND s.account_status='enabled'
	 AND sk.user_id=s.id
	 AND( 
		  ( mid(sk.key1,1,3)='1mo' AND $sqltime < $time1mo )
       OR ( mid(sk.key1,1,3)='3mo' AND $sqltime < $time3mo )
	   OR ( mid(sk.key1,1,3)='6mo' AND $sqltime < $time6mo )
	   OR ( mid(sk.key1,1,3)='12m' AND $sqltime < $time12mo)
	)
	GROUP BY s.id
"; 
$db=new dbObj($qry);
while($row=$db->getRow()){
	$hasrecs=1;
	echo "removing <b>${row[email]}</b><br>";
	$robot->flushOutput();
	$robot->removeAccount($row,$mailtitle,$mailmsg,"/assets/MAEimage.jpg");
}

if(!$hasrecs){
	echo "<i>no expired gift users found</i><br>";
}

?>