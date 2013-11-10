<?php
global  $HTPFX,$HTHOST;
include_once("$ADMIN_PATH/lib/_dailyfeed_data_lib.php");
include_once("$ADMIN_PATH/lib/_article_data_lib.php");
$objDailyFeed= new Dailyfeed('daily_feed',"");
$objArticle= new ArticleData();
// load and instantiate JSON services object
//require_once("scripts/json.php");
//$json = new Services_JSON();

// get the susbriber id from the POST string
$sid = $_POST['sid'];

// get the author and id for the latest post according to preferences
$qry="SELECT buzzbanter.id AS id, buzzbanter.title AS title, contributors.name AS author, buzzbanter.login AS login, buzzbanter.position AS position " .
	"FROM buzzbanter_today buzzbanter,contributors " .
	"WHERE  buzzbanter.contrib_id = contributors.id ".
	"AND buzzbanter.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
	"AND date_format(date,'%m/%d/%y')=date_format('".mysqlNow()."','%m/%d/%y') " .
	"AND is_live='1' " .
	"AND show_in_app='1' "  .
	"AND approved='1' " .
	"ORDER BY date DESC LIMIT 1 ";

$rows = exec_query($qry);
$row = $rows[0];
switch ($row['login']) {
	case '(automated)':
		$url=$HTPFX.$HTHOST.$objArticle->getArticleUrl($row['position']);
		$article_id = $row['position'];
		$post_id = $row['id'];
		$author  = $row['author'];
		$title  = addslashes($row['title']);
		$type = "ARTICLE";
		break;
	case '(feed_automated)':
        $url=$HTPFX.$HTHOST.$objDailyFeed->getDailyFeedUrl($row['position']);
		$article_id = $row['position'];
		$post_id = $row['id'];
		$author  = $row['author'];
		$title  = addslashes($row['title']);
		$type = "DAILYFEED";
		break;
	default:
		$article_id = "none";
		$post_id = $row['id'];
		$author = $row['author'];
		$title  = addslashes($row['title']);
		$type = "POST";
}
echo "{ID: ";
echo $post_id;
echo ", author: '";
echo $author;
echo "', title: '" . $title ;
echo "', type: '" . $type ;
if($url!="")
 {
echo "', url: '" .$url ;
 }
echo "', articleID: '" . $article_id;
echo "'}";

?>