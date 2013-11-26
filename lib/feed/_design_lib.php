<?php
class feedViewer{
	function feedViewer($feedType,$partnerId=NULL,$authorId=NULL){
		$this->type=$feedType;
		$this->partnerId=$partnerId;
		$this->authorId=$authorId;
	}

	function displayMediaRSS($maxItemLimit,$category_id = NULL,$currentFilter=NULL){
		global $D_R,$HTPFX,$HTHOST;
		include_once($D_R."/lib/feed/_data_lib.php");
		$objFeed= new feed($this->type);
		$layout['small-photo']['width']="257px";
		$layout['small-photo']['height']="194px";
		$layout['large-photo']['width']="620px";
		$layout['large-photo']['height']="214px";
		$arMediaFeed = $objFeed->getMediaFeedData($maxItemLimit,$currentFilter);
		?>
<rss
	version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>Minyanville</title>
<link>
		<?=$HTPFX.$HTHOST;?>
</link>
<description>
The Trusted Choice for the Wall Street Voice
</description>

		<?
		$arFeedData = $objFeed->getMediaFeedData($maxItemLimit);
		foreach($arFeedData as $item)
		{
			$feature_image = "";
			$arImageInfo = array();
			$articleLink = $HTPFX.$HTHOST.makeArticleslink($item['id']);
			$pattern = '/<img[^>]+src[\\s=\'"]';
			$pattern .= '+([^"\'>]+)/is';
			if(preg_match($pattern,$item['featureimage'],$match))
			{
				$feature_image = $match[1];
				//$arImageInfo = getimagesize(urlencode($feature_image));
			}
			$description = mswordReplaceSpecialChars(strip_tags($item['description']));
			if(strlen($description)> 500)
			{
				$description = substr($description,0,500);
			}
			?>
<item>
<title><![CDATA[<?=$item['title']?>]]></title>
<author>
<![CDATA[<?=$item['author']?>]]>
</author>
<description>
<![CDATA[<?=$description?>]]>
</description>
<pubDate>
			<?=date('D, j M Y H:i:s',strtotime($item['pubDate'])) ?>
EST
</pubDate>
<guid isPermaLink="true">
<![CDATA[<?=$articleLink?>]]>
</guid>
<link>
<![CDATA[<?=$articleLink."?camp=syndication&medium=portals&from=yahoobuzz"?>]]>
</link>
<media:content
	url="<?=$feature_image?>"
	<? if($feature_image){ echo "width='".$layout[$item['layout_type']]['width']."' height='".$layout[$item['layout_type']]['height']."' type='jpeg'" ; }?> />
</item>
	<?
		}
		?>
</channel>
</rss>
		<?
	}
	function addFilter($filterName,$filterVal){
		$this->filter[$filterName]=$filterVal;
	}

	function showFeed($maxItemLimit,$currentFilter=NULL){

		global $D_R,$articleAd,$HTPFX,$HTHOST;
		include_once($D_R."/lib/feed/_data_lib.php");
		$objFeed= new feed($this->type,$this->partnerId);
		$feedData = $objFeed->getFeedData($maxItemLimit,$currentFilter,$this->filter);
		//htmlprint_r($feedData);
		if(!empty($feedData[0]['pubDate'])){
			$LBD= date("D, j M Y H:i:s",strtotime($feedData[0]['pubDate']));
		}else{
			$LBD=date("D, j M Y H:i:s",strtotime(mysqlNow()));
		}
		if ($feedData){
			echo createXML($feedData,'channel');
		}
		return $strFeedData;
	}
	function showRSS($maxItemLimit,$currentFilter=NULL){
		global $D_R,$articleAd,$HTPFX,$HTHOST,$arFeedPartners,$IMG_SERVER;
		include_once($D_R."/lib/feed/_data_lib.php");
		$objFeed= new feed($this->type,$this->partnerId);
		$feedData = $objFeed->getFeedData($maxItemLimit,$currentFilter,$this->filter);
		if(!empty($feedData[0]['pubDate'])){
			$LBD= date("D, j M Y H:i:s",strtotime($feedData[0]['pubDate']));
		}else{
			$LBD=date("D, j M Y H:i:s",strtotime(mysqlNow()));
		}

		$string='';

		$string.='<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	';
	if($this->type=="nasdaq")
	{
		$string.='xmlns:media="http://search.yahoo.com/mrss/"
		xmlns:nasdaq="http://nasdaq.com/reference/feeds/1.0/"';
	}
	else if($this->type=="googlecurrents")
	{
		$string.='';
	}
	else {
		$string.='xmlns:tickersdata="'.$HTPFX.$HTHOST.'/rss/tickerrss.htm"
		xmlns:tickersval="'.$HTPFX.$HTHOST.'/rss/tickerrss.htm"
		xmlns:media="http://search.yahoo.com/mrss/"';
		if(trim($this->partnerId)!='PraJ6keqe57stEzu2ethuyAdr6thU6up' && trim($this->partnerId)!='qbvlNGTcW7e0FTJLHVIT2Mfpq20vVlMU' && trim($this->partnerId)!='2HAthamarev5RAfU8aFEKepruJeFraZA' && trim($this->partnerId)!='UwX8053S76293TY171Y33581HRn32U87')
		{
			$string.=' xmlns:nasdaq="http://www.nasdaq.com/reference/feeds/1.0" ';
		}
	}
	
	
	$string.='>
<channel>
<atom:link href="'.urlencode($HTPFX.$HTHOST.$_SERVER['REQUEST_URI']).'" rel="self" type="application/rss+xml" />
<title>Minyanville</title>
			<description>The Trusted Choice for the Wall Street Voice</description>
			<link>'.$HTPFX.$HTHOST.'</link>
			<copyright>'.date('Y').' Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
			<ttl>1</ttl>
		  	<lastBuildDate>'.$LBD.' EST</lastBuildDate>';
	
		if($this->type=="yahoo" || $this->type=="yahooFull" || $this->type=="mvpremiumyahoo")
		{
			$string.='<image>
'.$IMG_SERVER.'/images/home_redesign/mv_logo_site.png
</image>
<webMaster>it@minyanville.com</webMaster>
<generator>Minyanville</generator>';
		}
	
		$pattern=array();
		if(count($articleAd))
		{
			foreach($articleAd as $key=>$value){
				$patterns[]="/{".$key."\}/";
			}
		}
		if(count($feedData))
		{
			foreach($feedData as $item){
				$item['content']=preg_replace($patterns," ",$item['content']);
				// Modify Content for Light Speed Buzz Article Alert
				if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c' && $item['type']=='article'){
					// Remove    href="javascript:void(0);"
					// Add       target="_blank"
					// Remove    onclick="launchPage(......)"
					$searchString = array('href="javascript:void(0);"','onClick','onclick','launchPage(\'',')');
					$replaceString   = array('target="_blank"','href','href','','');
					$item['content'] = str_replace($searchString, $replaceString, $item['content']);
					$parts = explode('"',$item['content']);
					$keyIndex=0;
					foreach ($parts as $key => $val){
						if (strstr($val,$HTPFX.$HTHOST)){
							$keyIndex=$key;
							$extractPart = explode("'",$val);
						}
					}
					if (trim($extractPart[0])!=''){
						$parts[$keyIndex]=$extractPart[0];
						$finalStr = implode('"',$parts);
						$item['content']= $finalStr;
					}
				}
				else if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c' && $item['type']=='buzz'){
					// Make all links to open in a new window
					$searchString = array('href=');
					$replaceString   = array('target="_blank" href=');
					$item['content'] = str_replace($searchString, $replaceString, $item['content']);
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////

				$string.='<item>
				<title><![CDATA['.utf8_decode($item['title']).']]></title>';
				if(trim($this->partnerId)=='PraJ6keqe57stEzu2ethuyAdr6thU6up')
				{
					$string.='<link><![CDATA['.$item['link'].'?camp=syndication&medium=hostedportals&from=yahoo]]></link>';
				}
				else if(trim($this->partnerId)=='2HAthamarev5RAfU8aFEKepruJeFraZA')
				{	
					$string.='<link><![CDATA['.$item['link'].'?camp=syndication&medium=portals&from=yahoo]]></link>';
				}
				else
				{
					$string.='<link>'.$item['link'].'</link>';
				}
				$string.='<pubDate>'.date('D, j M Y H:i:s',strtotime($item['pubDate'])).' EST</pubDate>';
				if(trim($this->partnerId)=='f468c507a1204d5e912d580e411ab36a' || trim($this->partnerId)=='qbvlNGTcW7e0FTJLHVIT2Mfpq20vVlMU' || trim($this->partnerId)=='PraJ6keqe57stEzu2ethuyAdr6thU6up' || trim($this->partnerId)=='2HAthamarev5RAfU8aFEKepruJeFraZA'){
					$string.='<guid isPermaLink="false" >'.$item['guid'].'</guid>';
				}else{
					$string.='<guid isPermaLink="true">'.$item['link'].'</guid>';
				}

				// Add Type element for lightspeed
				if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c'){
					$string.='<type>'.$item['type'].'</type>';
				}
				///////////////////////////////////////////////////////////////////////////////////////////////////////

				// Add Icon for lightspeed
				if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c'){
					$string.='<icon>'.$item['icon'].'</icon>';
				}
				///////////////////////////////////////////////////////////////////////////////////////////////////////
				if (trim($this->partnerId)=='e4da3b7fbbce2345d7772b0674a318d5' && $this->type=='googlecurrents')
				{
					$relatedArticles = getSyndRelatedArticles($item['guid'],'aafb_syndicate');
					$relatedLinks = "<p><strong>Related Articles</strong><ul>";
					foreach($relatedArticles as $key=>$val)
					{
						$relatedLinks.="<li><a href='".$HTPFX.$HTHOST.$val['url']."'>".$val['title']."</a></li>";
					}
					$relatedLinks.="</ul></p>";
					$body = $this->addPartnerParam($item['desc']);
					$string.='<description><![CDATA['.utf8_encode($body.$relatedLinks).']]></description>';
				}elseif ($this->type=='gravity'){
					$string.='<description><![CDATA['.utf8_encode($this->addPartnerParam($item['content'])).']]></description>';
				}else {
					$string.='<description><![CDATA['.utf8_encode($this->addPartnerParam($item['desc'])).']]></description>';
				}
				
				if(trim($this->partnerId)=='PraJ6keqe57stEzu2ethuyAdr6thU6up' || trim($this->partnerId)=='qbvlNGTcW7e0FTJLHVIT2Mfpq20vVlMU' || trim($this->partnerId)=='2HAthamarev5RAfU8aFEKepruJeFraZA')
				{
					$item[articleImg] = str_replace('"', '', $item[articleImg]);
					$item[articleImg] = str_replace("'", "", $item[articleImg]);					
					$string.='<enclosure url="'.$item[articleImg].'"  type="image/jpeg" />';
				}
				
				if(!empty($item['authorId'])){
					$string.='<author><link>'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$item['authorId'].'</link>
					<name>';
					$string.='<![CDATA['.utf8_encode($item['author']).']]>';
					$string.='</name></author>';
				}else{
						$string.='<author><![CDATA['.utf8_encode($item['author']).']]></author>';

				}
				if($this->type!='gravity'){
					$string.='<content:encoded><![CDATA['.$this->addPartnerParam($item['content']).']]></content:encoded>';
				}

				// Positions
				if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c'){
					$string.='<position>'.$item['position'].'</position>';
				}

				if($this->type=="nasdaq")
				{
					$itemTicker = implode(',',$item[ticker]);
					$string.='<nasdaq:tickers>'.$itemTicker.'</nasdaq:tickers>';
				}
				else if($this->type=="googlecurrents")
				{
					if($item[ticker])
					{
						foreach($item[ticker] as $ticker) {
							$string.='<category domain="stocksymbol">'.$ticker.'</category>';
						}
					}
				}
				else
				{
					$string.='<tickersdata:tickers>';
					if($item[ticker])
					{
						foreach($item[ticker] as $ticker) {

							$string.='<tickersval:ticker><![CDATA[Nasdaq:'.$ticker.']]></tickersval:ticker>';
						}
					}
					$string.='</tickersdata:tickers>';
					$string.='<syndications><portal>'.$arFeedPartners[$this->partnerId]['portal_name'].'</portal></syndications>';
				}

				if(trim($this->partnerId) == "D3D9446802A44259755D38E6D163E820" || trim($this->partnerId) == "241f4baccd80454d89cdf63d7af7920b")
				{
					$image_url = $item['thumbnail_image'];
					$string.="<media:group> <media:thumbnail url='".$image_url."' height='360' width='480' /></media:group>";

				}

				if(is_array($item[category]))
				{
					foreach($item[category] as $category) {
						$string.='<category><![CDATA['.$category.']]></category>';
					}
				}
				if (trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c'){
					$string.='<charts>';
					if(is_array($item[charts]))
					{
						foreach($item[charts] as $charts) {
							$string.='<chart><![CDATA['.$charts.']]></chart>';
						}
					}
					$string.='</charts>';
				}

				if(trim($this->partnerId)=='f468c507a1204d5e912d580e411ab36a'){
					$syndRelatedArt = getSyndRelatedArticles($item['guid'],trim($this->type));
					if(!empty($syndRelatedArt)){
						foreach($syndRelatedArt as $synd) {
							$url = $HTPFX.$HTHOST.$synd['url'];
							$string.='<nasdaq:partnerlink url="'.$url.'"><![CDATA['.utf8_encode($synd["title"]).']]></nasdaq:partnerlink>';
						}
					}
				}
				$string.='</item>';
			}
		}
		$string.='</channel>
			</rss>';
		return $string;
	}
	function addPartnerParam($input)
	{
		global $arFeedPartners;
		// Regular exp for Article links
		$regexp = "<a\s[^>]*href=(\"??)(http:\/\/www.minyanville.com[^\" >]*?\/id\/[0-9]*)\\1[^>]*>(.*)<\/a>";
		preg_match_all("/$regexp/siU", $input, $matches);
		$trackParameter="camp=syndication&medium=feed&from=".$arFeedPartners[$this->partnerId]['tracking_name'];
		/*switch($this->partnerId){

			case 'c4ca4238a0b923820dcc509a6f75849b':
				$trackParameter="camp=syndication&medium=feed&from=etrade";
				break;
			case 'c81e728d9d4c2f636f067f89cc14862c':
				$trackParameter="camp=syndication&medium=feed&from=thestreet";
				break;
			case 'eccbc87e4b5ce2fe28308fd9f2a7baf3':
				$trackParameter="camp=syndication&medium=feed&from=ameritrade";
				break;
			case 'a87ff679a2f3e71d9181a67b7542122c':
				$trackParameter="camp=syndication&medium=feed&from=lightspeed";
				break;
			default:
				$trackParameter="camp=syndication&medium=feed&from=etrade";
				break;
		}*/
		if(count($matches[2]) > 0)
		{
			foreach($matches[2] as $str)
			{
				$match[] = '"'.$str.'"';
				$replace[] = '"'.$str.'?'.$trackParameter.'"';;
			}
		}
		return $input = str_replace($match,$replace,$input);
	}
	function displayDailyfeedMediaRSS($maxItemLimit,$topic,$category_id = NULL,$currentFilter)
	{
		global $D_R,$HTPFX,$HTHOST;
		include_once($D_R."/lib/feed/_data_lib.php");
		include_once("$D_R/admin/lib/_dailyfeed_data_lib.php");
		$objDailyfeed= new Dailyfeed();
		$objFeed= new feed($this->type);
		$arMediaFeed = $objFeed->getDailyFeedMediaData($maxItemLimit,$topic,$currentFilter);

		?>
<rss
	version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title>Minyanville</title>
<link>
		<?=$HTPFX.$HTHOST;?>
</link>
<description>
The Daily Feed of Stock Market News
</description>
		<?
		foreach($arMediaFeed as $item)
		{
			$feature_image = "";
			$feedid		= $item['id'];
			$urltitle=$objDailyfeed->getDailyFeedUrl($feedid);
			$feedLink	= $HTPFX.$HTHOST.$urltitle;
			$image	= 	$objFeed->getFeedImage($feedid,$this->type);
			if(count($image)>0)
			{
				$imageURL	=	$HTPFX.$HTHOST.$image['url'];
			}
			else
			{
				$imageURL	='';
			}
			$description = article_body_word_replace(strip_tags($item['description']));
			if(strlen($description)> 500)
			{
				$description = substr($description,0,500);
			}
			?>
<item>
<title><![CDATA[<?=article_body_word_replace($item['title']);?>]]></title>
<author>
<![CDATA[<?=article_body_word_replace($item['author']);?>]]>
</author>
<description>
<![CDATA[<?=$description;?>]]>
</description>
<pubDate>
			<?=date('D, j M Y H:i:s',strtotime($item['pubDate'])) ?>
EST
</pubDate>
<guid isPermaLink="true">
<![CDATA[<?=$feedLink;?>]]>
</guid>
<!--<link><![CDATA[<?=$feedLink."?camp=syndication&medium=portals&from=yahoobuzz"?>]]></link>-->
<media:content
	url="<?=$imageURL;?>" />
</item>
			<?
		}
		?>
</channel>
</rss>
		<?

	}

	function displayVideoMediaRSS($maxItemLimit,$category_id = NULL)
	{
		global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
		include_once($D_R."/lib/feed/_data_lib.php");
		$objFeed= new feed($this->type);
		$videoMediaRss = $objFeed->getVideoMediaRssData($maxItemLimit);
		?>
<rss version="2.0"
	xmlns:media="http://search.yahoo.com/mrss/"
	xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
		<?
		if(count($videoMediaRss)>0)
		{
			foreach($videoMediaRss as $videoItem)
			{
				$videoid		= $videoItem['id'];
				$videotitle		= mswordReplaceSpecialChars(strip_tags($videoItem['title']));
				$description 	= mswordReplaceSpecialChars(strip_tags($videoItem['description']));
				$publish_date	= date("c",strtotime($videoItem['publish_time']));

				if($videoItem['podcasturl']!='' && $videoItem['podcasturl'] != NULL)
				{
					$videoURL	=	$videoItem['podcasturl'];
				}
				else
				{
					$videoURL	=	$videoItem['videofile'];
				}
				$videoExt 		= substr($videoURL, strrpos($videoURL, '.') + 1);
				$mimeType		= $objFeed->getMimeType($videoExt);
				if($mimeType!='')
				{
					$videoType	=	$mimeType;
				}
				else
				{
					$videoType	=	"";
				}
				$videoDuration	=	$videoItem['duration'];
				$videoImage		=	$videoItem['thumbfile'];
				?>
<item>
<title><?=$videotitle;?></title>
<description>
<![CDATA[<?=$description;?>]]>
</description>
<link>
				<?=$HTPFX.$HTHOST."/audiovideo/".htmlentities($videoid)."/";?>
</link>
<guid isPermaLink="true">
<![CDATA[<?=$HTPFX.$HTHOST."/audiovideo/".htmlentities($videoid)."/";?>]]>
</guid>
<pubDate>
				<?=$publish_date;?>
</pubDate>
<media:content url="<?=$videoURL;?>" type="<?=$videoType;?>"
	duration="<?=$videoDuration;?>" medium="video" isDefault="true">
	<media:title>
	<?=$videotitle;?>
	</media:title>
	<media:description>
		<![CDATA[<?=$description;?>]]>
	</media:description>
	<media:thumbnail url="<?=$videoImage;?>" />
</media:content>
</item>
	<?
			}	}	?>
</channel>
</rss>
			<?

	}

	function showAuthorRSS($maxItemLimit,$tid=NULL){
		global $D_R,$articleAd,$HTPFX,$HTHOST;
		include_once($D_R."/lib/_content_data_lib.php");
		include_once($D_R."/lib/feed/_data_lib.php");
		$objContributor= new contributor();
		$objContent= new Content();
		$objMemcache= new Cache();
		$objFeed= new feed($this->type,$this->partnerId,$this->authorId);
		$contribDetail=$objContributor->getContributor($this->authorId,$name=NULL);
		$authorName=$contribDetail['name'];
		?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<atom:link
	href="<?=$HTPFX.$HTHOST;?>/rss/author.rss?authorid=<?=$this->authorId;?>"
	rel="self" type="application/rss+xml" />
<title>Minyanville - <?=$authorName;?> RSS</title>
<description>
The Trusted Choice for the Wall Street Voice
</description>
<link>
		<?=$HTPFX.$HTHOST;?>
</link>
<copyright>
		<?=date('Y');?>
Minyanville Publishing and Multimedia, LLC. All Rights Reserved
</copyright>
		<?
		$feedArticleData = $objFeed->getFeedData($maxItemLimit,$tid);
		$feedDAilyFeedData = $objFeed->getDailyFeedRssData($maxItemLimit);
		$feedData=array_merge($feedArticleData,$feedDAilyFeedData);
		$feedData=SortDataSet($feedData,"pubDate", $bDescending = true);
		if(count($feedData)>$maxItemLimit){
			for($i=$maxItemLimit; $i<=count($feedData); $i++){
				unset($feedData[$i]);
			}

		}
		$pattern=array();
		foreach($articleAd as $key=>$value){
			$patterns[]="/{".$key."\}/";
		}


		foreach($feedData as $item){
			$item['content']=preg_replace($patterns," ",$item['content']);
			$item['content']=$objContent->getCountWords($item['content'],$count="100");
			$item['content']=htmlentities(mswordReplaceSpecialChars(strip_tags($item['content'],'ENT_QUOTES')));
			$value['content']=$objMemcache->replaceBlankArticleAd($value['content']);
			$value['content']=str_replace("{FLIKE}"," ",$value['content']);
			?>
<item>
<title><![CDATA[<?=utf8_decode($item['title']); ?>]]></title>
<link>
			<?=htmlentities($item['link']);?>
</link>
<pubDate>
			<?=date('D, j M Y H:i:s',strtotime($item['pubDate'])) ?>
EST
</pubDate>
<guid isPermaLink="true">
			<?=htmlentities($item['link']);?>
</guid>
<description>
<![CDATA[<?=utf8_encode(html_entity_decode($this->addPartnerParam($item['content'])));?> ]]>
</description>
<content:encoded>
	<![CDATA[<?=html_entity_decode($this->addPartnerParam($item['content']));?> ]]>
</content:encoded>
			<? if($this->type=="articles"){ ?>
<wfw:commentRss>
<?=htmlentities($item['commentRSS']);?>
</wfw:commentRss>
<? foreach($item[category] as $category) { 	?>
<category>
<![CDATA[<?=$category;?>]]>
</category>
<? }
			}?>
</item>
			<?
		}
		?>
</channel>
</rss>
		<?
	}
}
?>