<?php
//This script accepts the subscriber ID, looks up preferences and pulls jump to post out of database.
// and sends the list back as html
//to be put in the 'postMenu' div on buzz.php.

$sid = $_POST['sid'];

function truncate ($string, $max, $rep = '') {
   if (strlen($string)<=$max) {return $string;}
   $leave = $max - strlen ($rep);
   return substr_replace($string, $rep, $leave);
}

$DATE_STR="D M jS, Y";
$qry="SELECT distinct buzzbanter_today.id AS id," . $charqry . " buzzbanter_today.title AS title, " .
	"buzzbanter_today.author AS author, " .
	"date_format(buzzbanter_today.date,'%r') AS mdate, " .
	"buzzbanter_today.date AS udate " .
	"FROM buzzbanter_today,contributors " .
	//"WHERE date_format(buzzbanter_today.date,'%m/%d/%y')=date_format('".mysqlNow()."','%m/%d/%y') " .
	"WHERE DATE_FORMAT(buzzbanter_today.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
	"AND buzzbanter_today.contrib_id = contributors.id ".
	"AND buzzbanter_today.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
	"AND buzzbanter_today.login != '(automated)' " .
	" AND buzzbanter_today.is_live='1' " .
	"AND buzzbanter_today.show_in_app='1' " .
	"AND buzzbanter_today.approved='1' " .
	"ORDER BY buzzbanter_today.date DESC ";
//submit the query
$rows = exec_query($qry);
$numrows = count($rows);
if ($numrows > 0) {
	$div = ($numrows > 20) ? '<div style="width:329px;height:300px;overflow:auto">' : null;
	echo $div . '<table width="' . ($div ? '309' : '329') . '" cellpadding="0" cellspacing="0" border="0">';
	for ($i=0; $i<$numrows; $i++) {
		$row=$rows[$i];
		//populate the table
		$post = '<tr ';
		if (!($i % 2)) $post .= ' class="offset" ';
		$post .= 'onclick="jumpToPost(' . $row['id'] . ')"  onmouseover="this.style.backgroundColor = \'cyan\'" onmouseout="this.style.backgroundColor=\'\'">' .
				 '<td width="1%"></td>' .
				 '<td width="24%">' . date('h:i:s A', strtotime($row['udate'])) . '</td>' .
				 '<td width="28%">' . ucwords(truncate($row['author'],15,'...')) . '</td>' .
				 '<td width="40%">' . strip_tags(truncate($row['title'],23,'...')) . '</td>' .
				 '</tr>';
		echo $post;
		$post = null;
	}
	// close the table
	echo '</table>' . ($div ? '</div>' : null);
} else {
	echo "There are no posts.";
}