<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$DOCUMENT_ROOT/lib/_db.php");

?>


<?
	$links = $_POST['link-position'];
//	print_r($_POST);
	$parentId = $_POST['parent'];
?>
<br><br>

<?

function insertLinks($links, $parentId) {
while ($relatedlinks = current($links)) {
	$related = key($links);
	$position = $links[$related];
	$sql="UPDATE article_related_links set position = '".$position."' where parent_article_id='".$parentId."' and article_id='".$related."'";
	exec_query_nores($sql);
    next($links);
}
}

insertLinks($links, $parentId);
echo "<script>window.location='/admin/related-articles/index.htm?id=' + ".$parentId.";</script>";
?>
