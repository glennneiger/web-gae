<?php

$sid = $_POST['sid'];
$bbid = $_POST['bbid'];

// Do the necessary to add bbid-to-sid in bookmarks table
// Don't return anything 

$bookmark[sid] = $sid;
$bookmark[bbid] = $bbid;

$qry = "DELETE FROM subscriber_bookmarks WHERE sid=" . $sid . " AND bbid=" . $bbid;
exec_query($qry);
insert_query(subscriber_bookmarks,$bookmark);




?>