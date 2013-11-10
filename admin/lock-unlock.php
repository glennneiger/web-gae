<?php
session_start();
global $D_R;
include_once("$D_R/lib/_includes.php");
include_once("$D_R/lib/_db.php");
$q = strtolower( $_REQUEST["aid"] );
if (!$q) return;

$queryString=$_REQUEST['aid'];

$qry = "SELECT CL.id,author_id,AU.name FROM content_locking as CL ,admin_users as AU WHERE author_id = AU.id AND item_id=".$queryString." AND item_type = 1";
$res = exec_query($qry,1);

if($res['id'])
{
	update_query('content_locking',array('last_visit'=>mysqlNow()),array('author_id'=> $_SESSION['AID'],'item_id'=> $queryString));
}
else
{
	insert_query('content_locking',array('item_id' => $queryString, 'item_type' => '1' , 'author_id' => $_SESSION['AID'],'last_visit'=>mysqlNow()));
}

?>