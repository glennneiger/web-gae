<?
header('Content-type: text/xml');
$currentFilter=$_GET['from'];
include_once($D_R."/lib/feed/_data_lib.php");
$objFeed= new feed('1',NULL);
$articleData = $objFeed->getFeedData(15,NULL,$currentFilter);
?>
<rss version="2.0">
<channel>
<title>Minyanville</title>
<description>The Trusted Choice for the Wall Street Voice</description>
<link>http://www.minyanville.com</link>
<copyright>2007 Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
<?
foreach ($articleData as $article) {
?>
     <item>
        <title> <?php echo htmlentities($article['title']); ?></title>
		<description><?=htmlentities($article['desc']);?></description>
        <link><?=$article['link'];?></link>
        <pubDate><?=$article['pubDate']; ?> EDT</pubDate>
		<tickers>
		<?
			foreach($article['ticker'] as $value)
			{
		?>
			<ticker><?=$value?></ticker>
		<? }
		?></tickers>
		<author><?php echo $article['author'];?></author>
     </item>
<?
		}
?>

</channel>
</rss>
