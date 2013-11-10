<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$DOCUMENT_ROOT/lib/_db.php");

?>


<?
	$related = $_GET['related'];
	$parent = $_GET['parent'];
?>
<br><br>

<?


del_query("article_related_links","id",$related);	
	echo "<script>window.location='/admin/related-articles/index.htm?id=' + ".$parent.";</script>";

?>
