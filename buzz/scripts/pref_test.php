<?

$sid=18;

del_query(subscribers_to_contributors, "sid", $sid);

//foreach teacher in form

$qry = "SELECT contributors.id as cid " .
" FROM contributors " .
" WHERE contributors.name <> '' order by contributors.name";

$db2 = new dbObj($qry);

while ($row = $db2->getRow()) {
			$teachers[cid]=$row['cid'];
			$teachers[sid]=$sid;
			insert_query(subscribers_to_contributors, $teachers);
			}		
?>