<?php
header('Content-type: text/xml');
include("$D_R/layout/dbconnect.php");
include("$D_R/lib/layout_functions.php");
include_once("$D_R/lib/_misc.php");
include_once("$D_R/lib/config/_rss_config.php");
include_once("$D_R/admin/lib/_contributor_class.php");
global $HTPFX,$HTHOST;
$objContrib= new contributor();
$currentFilter=$_GET['from'];
?>
<rss version="2.0">
<channel>
<title>Minyanville</title>
<description>The Trusted Choice for the Wall Street Voice</description>
<link>http://www.minyanville.com</link>
<copyright>2007 Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
<?
$sql = "select articles.id id, articles.title, articles.keyword ,contributors.name author, articles.contributor, contributors.disclaimer,articles.position, contrib_id authorid, date, blurb, body, position, character_text ";
$sql .= "from articles, contributors ";
$sql .= "where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1'";
if($currentFilter){
    $contribFilterId=$objContrib->getExcludedPartnerId();
	$sql .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
$urlPostFix = "?from=".$currentFilter;
}
$sql .= "ORDER BY date DESC LIMIT 0,15";
$results = exec_query($sql);
$LBD= date("D, j M Y H:i:s",strtotime($results[0]['date']));

?>
<ttl>1</ttl>
<lastBuildDate><?php echo $LBD;?> EST</lastBuildDate>
<?php
foreach($results as $result)
{
	$numWords = 100;
	 $articlebody=strip_tag_style($result['body']);
	//limit the first 100 words for the descriptions
        	$tempBody = htmlentities(strip_tags($articlebody,'ENT_QUOTES'));
	//$bodyArray = str_word_count($tempBody,1);
        $bodyArray = preg_split("/[\s,]+/",$tempBody);
	for($i=0;$i<$numWords;$i++) {
		$body .= $bodyArray[$i] . " ";
	}

	if (count($bodyArray) > 100) {
		$body .= "...";
	}
?>
     <item>
        <title><![CDATA[<?=htmlentities(strip_tags($result['title'])); ?>]]></title>
		<description><![CDATA[<?=$body;?>]]></description>
      	 <link><?=$HTPFX.$HTHOST.makeArticleslink($result['id']).$urlPostFix;?></link>
		 <author><![CDATA[<?=$result['author'];?>]]></author>
        <pubDate><?=date('D, j M Y H:i:s',strtotime($result['date'])) ?> EST</pubDate>
        <category domain="keywords"><![CDATA[<?=str_replace("-",",",$result['keyword']);?>]]></category>
		<category domain="tickers"><? $tags_qry="select concat(ES.exchange,':',ES.stocksymbol) ticker from ex_item_tags EIT,ex_tags ET,ex_stock ES where EIT.tag_id=ET.id and ET.tag=ES.stocksymbol and EIT.item_id=".$result['id']." and EIT.item_type=1 and ET.type_id=1 GROUP BY ES.stocksymbol ";
			$ticker_result=exec_query($tags_qry);
			$tickers=array();
			if(!empty($ticker_result))
			{
				$ticker_result = array_slice($ticker_result, 0, 5);
				foreach($ticker_result as $id=>$value)
				{
					$tickers[]=$value['ticker'];
				}
			}
			echo implode(",",$tickers);	?></category>
     </item>
<?
	$body = "";
	}
?>
</channel>
</rss>