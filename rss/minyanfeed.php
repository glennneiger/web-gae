<?
header( 'Content-Type: application/rss+xml; charset=utf-8' );
include("$D_R/layout/dbconnect.php");
include_once("$D_R/lib/_misc.php");
include_once("$D_R/lib/config/_article_config.php");
include_once("$D_R/lib/config/_rss_config.php");
include_once("$D_R/admin/lib/_contributor_class.php");
global $HTPFX,$HTHOST;

$currentFilter=$_GET['from'];

$objCache = new Cache();
print $objCache->setMinyanFeedData($currentFilter);