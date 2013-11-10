<?php

//This script accepts the subscriber ID, pulls SID's bookmarks out of database and sends them back as html
//to be put in the 'bookMenu' div on buzz.php.

// Include DB connect script

// Get data from Post string
$SID = $_POST['sid'];
function truncate ($string, $max, $rep = '') {
   if (strlen($string)<=$max) {return $string;}
   $leave = $max - strlen ($rep);
   return substr_replace($string, $rep, $leave);
}

// Access database to cull bookmarks
$qry="SELECT buzzbanter.id, buzzbanter.author as author, buzzbanter.title, UNIX_TIMESTAMP(buzzbanter.date) AS udate, contributors.name as author2 " .
	"FROM buzzbanter,contributors,subscriber_bookmarks WHERE buzzbanter.contrib_id = contributors.id AND buzzbanter.id = subscriber_bookmarks.bbid " .
	"AND is_live='1' " .
	"AND show_in_app='1' " .
	"AND approved='1' " .
	"AND subscriber_bookmarks.sid = " . $SID . " " .
	"ORDER BY id DESC";
// Run through the results to write the HTML
// start the table

?>
<div class="topBorder"></div>
<?php

//submit the query
$rows = exec_query($qry);
$numrows = count($rows);

if ($numrows > 0) {
echo $div = ($numrows > 20) ? '<div style="width:329px;height:300px;scroll:auto">' : null;
?>
<table width="<?php echo ($div ? '309' : '330') ?>" cellpadding="0" cellspacing="0" border="0"<?php echo $tableStyle; ?>>
<?php
for ($i=0; $i<$numrows; $i++) {
	$row=$rows[$i];


if ($row['author2'] == "") {
	$author = $row['author'];
}
	else {
	$author = $row['author2'];
	}

//populate the table
?>
<tr<?php if (!($i % 2)) echo ' class="offset"'; ?> onclick="launchBookmark(<?=$row[id]?>, 'bookmark<?=$row[id]?>', 'bookmark')"  onmouseover="this.style.backgroundColor = 'cyan'" onmouseout="this.style.backgroundColor=''">
<td width="1%"><a name="bookmark<?=$row[id]?>" id="bookmark<?=$row[id]?>"></a></td>
<td width="28%"><?=date('h:i:s A', $row[udate])?></td>
<td width="28%"><?=ucwords(truncate($author,15,'...'))?></td>
<td width="40%"><?=strip_tags(truncate($row[title],23,'...'))?></td>
</tr>

<?php }
// close the table
echo '</table>' . ($div ? '</div>' : null);
} else {
echo "There are no bookmarks.";
}