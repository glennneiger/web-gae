<?
header('Content-type: text/xml');
include("$D_R/layout/dbconnect.php");
echo "<?xml version=\"1.0\"?>"; ?>
<rss xmlns:media="http://search.yahoo.com/mrss" xmlns:av="http://www.searchvideo.com/schemas/av/1.0" version="2.0">
<channel>
<title>Minyanville</title>
<description>Minyanville's Hoofy &amp; Boo</description>
<?
$id='72';
$sql_videos = "SELECT * FROM mvtv where id=".$id;
$results_videos = exec_query($sql_videos);
foreach($results_videos as $id=>$videoitem){
?>
<delete>
<PID><?=$videoitem['id']?></PID>
</delete>
<?
}
?>
</channel>
</rss>