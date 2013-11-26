<?
 header('Content-type: text/xml');
$currentFilter=$_GET['from'];
if(empty($currentFilter)){
   $currentFilter="dailyfinace";
}
include_once($D_R."/lib/feed/_data_lib.php");
$objFeed= new feed('1',NULL);
$articleData = $objFeed->getFeedData(15,NULL,$currentFilter);

?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<atom:link href="<?=$HTPFX.$HTHOST?>/rss/minyan_dailyfinance_feed.php" rel="self" type="application/rss+xml" />
<title>Minyanville</title>
<description>The Trusted Choice for the Wall Street Voice</description>
<link>http://www.minyanville.com</link>
<copyright>2007 Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
<?
foreach ($articleData as $article) {
	$pubDate=date('D, j M Y H:i:s',strtotime($article['pubDate']));
?>
     <item>
        <title><![CDATA[<?php echo htmlentities(utf8_decode($article['title'])); ?>]]></title>
		<description><![CDATA[<?=htmlentities($article['desc']);?>]]></description>
        <link><?=$article['link'];?></link>
        <pubDate><?=$pubDate; ?> EST</pubDate>
		<?
			foreach($article['ticker'] as $value)
			{
		?>
			<category domain="stocksymbol"><?=$value?></category>
		<? }
		?>
		<author><?php echo $article['author'];?></author>
		<guid isPermaLink="true"><?=$article['link'];?></guid>
     </item>
<?
	}
?>

</channel>
</rss>
