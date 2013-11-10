<?
//insert a row for each contributor for a new subscriber
function initialize_subscriber($sid) {

$qry = "SELECT contributors.id as cid " .
" FROM contributors " .
" WHERE contributors.name <> '' order by contributors.name";

$db2 = new dbObj($qry);
//echo $qry;

while ($row = $db2->getRow()) {
$teachers[cid]=$row['cid'];
$teachers[sid]=$sid;
insert_query("subscribers_to_contributors", $teachers);
}

//insert a row into the preferences table for this user, if it doesn't already exist
	

//	initialize preferences
// db gives defaults to all values
$prefs[ssid]=$sid;
insert_query("subscriber_buzzbanter_preferences",$prefs);
	

return 0;
}

//initalize all subscribers for a new contributor
function initialize_contributor($cid){

$qry = "SELECT id as sid " .
" FROM subscription ";

$db3 = new dbObj($qry);
//echo $qry;

while ($row = $db3->getRow()) {
$teachers[sid]=$row['sid'];
$teachers[cid]=$cid;
insert_query(subscribers_to_contributors, $teachers);
}

return 0;
}

?>