<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$DOCUMENT_ROOT/lib/_db.php");
$parentId = $_POST['parentId'];
$date = $_POST['date'];
$title = $_POST['title'];
$relatedId = $_POST['relatedId'];
$item_type = $_POST['item_type'];

$sql['parent_article_id'] = $parentId;
$sql['article_type'] = 'mv';
$sql['article_id'] = $relatedId;
$sql['item_type'] = $item_type;
$sql['title'] = $title; 	
	$relatedarticle=insert_query("article_related_links", $sql);
echo "<script>window.location='/admin/related-articles/index.htm?id=' + ".$parentId.";</script>";

?>
