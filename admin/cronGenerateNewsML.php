<?php
header('Content-type: text/xml;charset=utf-8');
include("$D_R/layout/dbconnect.php");
include("$D_R/lib/layout_functions.php");
include("$D_R/lib/email_alert/_lib.php");
include_once($D_R.'/lib/config/_syndication_config.php');

global $HTPFX,$HTHOST;

include_once("$DOCUMENT_ROOT/lib/_includes.php");
global $LATESTARTICLE, $MODERATOR_EMAIL, $NEWSMLTIMEINTERVAL;

$handle=fopen($LATESTARTICLE,"r");
$read=fread($handle,64);
fclose($handle);
$newArticleQuery = "SELECT * FROM articles";
if($read)
{
	$newArticleQuery.=" where id > $read and approved='1' and is_msnfeed='1'";
}
else
{
	$newArticleQuery.=" where approved='1' and is_msnfeed='1' and date > '".mysqlNow()."' - INTERVAL $NEWSMLTIMEINTERVAL";
} 
$newArticleQuery.=" order by id";

$latestArticle="";
foreach(exec_query($newArticleQuery) as $recentArticlerow)
{
	$latestArticle=generateNewsML($recentArticlerow);
}
if(num_rows($newArticleQuery)>0){
	$fFile=fopen($LATESTARTICLE,"w+");
	fwrite($fFile,$latestArticle);
	fclose($fFile);
}

?>
