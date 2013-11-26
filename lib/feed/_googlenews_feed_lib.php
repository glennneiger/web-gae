<?php
include_once("$D_R/admin/lib/_dailyfeed_data_lib.php");
include_once("$D_R/lib/config/_article_config.php");
$objCache = new Cache();
Class googleFeed											// Class Start
{
	function googleFeed($item_type){						// Constructor Call
		if(!is_numeric($item_type)) {
			$sqlItemType="select id from ex_item_type where item_text='".$item_type."' or item_table='".$item_type."'";
			$resItemType=exec_query($sqlItemType,1);
			if(count($resItemType)>0){
				$this->type=$resItemType['id'];
			}else{
				$this->type='1';
			}
		}
		else{
			$this->type=$type;
		}
	}
	function getGoogleDailyFeedData($limit)
	{
		global $HTPFX,$HTHOST;
		/*$sqlGoogleDailyFeeds = "SELECT D.id,D.title,D.body,D.source,D.source_link,D.title_link,D.creation_date,D.updation_date from daily_feed D where  D.is_approved='1' and D.is_live='1' and D.is_deleted='0'  and  D.updation_date  BETWEEN DATE_SUB(now() ,INTERVAL 2 day ) AND now() order by D.updation_date desc LIMIT 0,".$limit;*/
		$sqlGoogleDailyFeeds = "SELECT D.id,D.title,D.body,D.title_link,D.creation_date,if(D.publish_date,D.publish_date,D.updation_date) updation_date from daily_feed D where  D.is_approved='1' and D.is_live='1' and D.is_deleted='0'  and  publish_date  BETWEEN DATE_SUB('".mysqlNow()."' ,INTERVAL 2 day ) AND '".mysqlNow()."' order by D.publish_date desc LIMIT 0,".$limit;
		return exec_query($sqlGoogleDailyFeeds);
	}

	function getGoogleArticlesData($limit,$filter=NULL)
	{
		global $HTPFX,$HTHOST;

		$sqlGoogleArticlesFeeds = "SELECT A.id,A.title,EIM.description,A.body,A.date as 'articleDate',A.blurb,A.keyword,A.subsection_ids from articles A, ex_item_meta EIM  where A.approved='1' and EIM.item_id=A.id AND EIM.item_type='1' and A.is_live='1' and  A.date  BETWEEN DATE_SUB('".mysqlNow()."',INTERVAL 2 day ) AND '".mysqlNow()."'  order by A.date desc LIMIT 0,".$limit;
		switch($filter){
			case 'google_editors_pick':

		 	$sqlGoogleArticlesFeeds = "SELECT * FROM (SELECT articles.id id,EIM.item_type, articles.title, contributors.name author,EIM.description,articles.body as body, IF(articles.publish_date,articles.publish_date,articles.date) AS `pub_date` FROM articles, contributors, content_syndication CS, ex_item_meta EIM WHERE articles.contrib_id = contributors.id AND EIM.item_id=articles.id AND EIM.item_type='1' AND articles.approved='1' AND articles.is_live='1' AND  articles.id=CS.item_id AND CS.item_type='1' AND CS.is_syndicated='1' AND CS.syndication_channel='".$filter."'  		 	UNION
     SELECT daily_feed.id id,EIM.item_type, daily_feed.title,
   contributors.name author,EIM.description,daily_feed.body AS body,
   IF(daily_feed.publish_date,daily_feed.publish_date,daily_feed.creation_date) AS `pub_date` FROM `daily_feed`, `contributors`, content_syndication CS, ex_item_meta EIM
   WHERE daily_feed.contrib_id = contributors.id AND EIM.item_id=daily_feed.id AND
   EIM.item_type='18' AND daily_feed.is_approved='1' AND daily_feed.is_live='1' AND
   daily_feed.id=CS.item_id AND CS.item_type ='18' AND CS.is_syndicated='1' AND
    CS.syndication_channel='google_editors_pick' ORDER BY `pub_date` DESC LIMIT 0,".$limit.") tbl WHERE tbl.pub_date > ('".mysqlNow()."' - INTERVAL 7 DAY)";
			break;

		}
		return exec_query($sqlGoogleArticlesFeeds);
	}

	function getGoogleArticleDailyfeedData($limit)
	{


		   $sql="SELECT * FROM (SELECT '18' AS item_type,D.id,D.title,D.body,D.updation_date creation_date FROM daily_feed D WHERE D.is_approved='1' AND D.is_live='1' AND D.is_deleted='0' AND D.updation_date BETWEEN DATE_SUB('".mysqlNow()."' ,INTERVAL 2 DAY ) AND '".mysqlNow()."'
UNION
SELECT '1' AS item_type, A.id,A.title,A.body,A.date AS creation_date FROM articles A WHERE A.approved='1' AND A.is_live='1' AND A.date BETWEEN DATE_SUB('".mysqlNow()."',INTERVAL 2 DAY ) AND '".mysqlNow()."'
UNION
SELECT '2' as item_type, B.id,B.title,B.body ,B.updated AS creation_date from buzzbanter B,content_syndication CS where CS.item_id=B.id AND CS.item_type='2' AND CS.is_syndicated='1' AND B.approved='1' and B.is_live='1' and B.updated BETWEEN DATE_SUB('".mysqlNow()."' ,INTERVAL 2 day ) AND '".mysqlNow()."'
) AS alias ORDER BY creation_date DESC LIMIT 0,".$limit;
		//echo "<br>".'sql-- '.$sql;
		return exec_query($sql);

	}

	function getGoogleBnBData($limit)
	{
		global $HTPFX,$HTHOST;
		$sqlGoogleBnBFeeds = "SELECT B.id,B.title,B.updated 'bbDate',B.body from buzzbanter B,content_syndication CS  where CS.item_id=B.id AND CS.item_type='2' AND CS.is_syndicated='1' AND B.approved='1' and B.is_live='1' and  B.updated  BETWEEN DATE_SUB('".mysqlNow()."' ,INTERVAL 2 day ) AND '".mysqlNow()."' order by B.updated desc LIMIT 0,".$limit;
		/*$sqlGoogleBnBFeeds = "SELECT  B.id,B.title,B.updated 'bbDate',B.body from buzzbanter B  where B.approved='1' order by B.updated desc LIMIT 0,".$limit;*/
		//echo "<br>".$sqlGoogleBnBFeeds;
		return exec_query($sqlGoogleBnBFeeds);
	}

	function getArticleKeywords($feedid)
	{
		global $HTPFX,$HTHOST;
		/*$sqlFeedsKeywords = "select t.tag from ex_item_tags i,ex_tags t where i.item_id=".$feedid." and t.id=i.tag_id and i.item_type=".$this->type;*/
		$sqlFeedsKeywords = "Select keywords from ex_item_meta where item_type=1 and item_id= ".$feedid;
		return exec_query($sqlFeedsKeywords);
	}
	function getFeedKeywords($feedid,$itemType="18")
	{
	$sqlFeedsKeywords = "SELECT xt.tag as tagname FROM ex_item_tags xbt, ex_tags xt where xbt.item_id=".$feedid." and xt.id = xbt.tag_id and xbt.item_type ='".$itemType."'";
	/*$sqlFeedsKeywords = "select t.tag from ex_item_tags i,ex_tags t where i.item_id=".$feedid." and t.id=i.tag_id and i.item_type=".$this->type;*/
	return exec_query($sqlFeedsKeywords);
	}

	function getConributorName($feedid)
	{
	$sqlContr	=	"SELECT C.name as 'contrName' from daily_feed D,contributors C where D.contrib_id=C.id and D.id=".$feedid;
	return exec_query($sqlContr,1);
	}

	function getBuzzConributorName($buzzId)
	{
	$sqlContr	=	"SELECT C.name as 'contrName' from buzzbanter B,contributors C where B.contrib_id=C.id and B.id=".$buzzId;
	return exec_query($sqlContr,1);
	}




	function displaySitemap($limit,$feedType)
	{
		global $D_R,$HTPFX,$HTHOST,$marketArticleKeys,$investingArticleKeys,$specialArticleKeys,$dailyFeedKeys,$buzzBanterKeys,$HTNOSSLDOMAIN;
		$feedType = (int) $feedType;
		$objDailyfeed= new Dailyfeed();
		include_once($D_R."/lib/_content_data_lib.php");
		$objContent		= 	new Content();
		switch($feedType)
		{
			case 18:
			$arrGoogleFeedResult = $this->getGoogleDailyFeedData($limit);

			if(count($arrGoogleFeedResult)>0)
			{
				//header('Content-Type: application/xml');
				header('Content-type: text/xml');
				header('Pragma: public');
				header('Cache-control: private');
				header('Expires: -1');

				echo "<?xml version='1.0' encoding='UTF-8'?>\n";
			?>
		<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'  	xmlns:n='http://www.google.com/schemas/sitemap-news/0.9'>
			<?php
				foreach($arrGoogleFeedResult as $item)
				{
					$stock_tickers		=	'';
					$feedid				=	$item['id'];
					$arrKeys			=   $this->getFeedKeywords($feedid,"18");
					$feedBody			=	trim($item['body']);
					$keywords			=	'';
					$getsource			=	$objDailyfeed->getResource($feedid,'18');
					$source				=   article_body_word_replace(trim($getsource['source']));
					if(count($arrKeys)>0)
					{
							for($i=0;$i<count($arrKeys);$i++)
							{
								$keywords .=	$arrKeys[$i]['tagname'].",";
							}
							$keywords	=	substr(rtrim($keywords),0,-1);
							$keywords	=	article_body_word_replace(trim($keywords));
					}
					else
					{
						$keywords		=	"";
					}
					$stock_tickers		=   $this->stockList($feedid,$feedBody,$feedType);
					if($stock_tickers !='')
					{
						$exp_stockLst	=	explode(",",$stock_tickers);
						foreach($exp_stockLst as $key=>$val)
						{
							$exp_val	=	explode(":",$val);
							$strList	=	$exp_val[1].",".$strList;
						}
						$strList	=	substr(rtrim($strList),0,-1);
						$strList	=	article_body_word_replace(trim($strList));
					}

					$updationDate		=	$item['updation_date'];

					$title				=	article_body_word_replace(trim($item['title']));
					$contributor		=	$this->getConributorName($feedid);
					$publicationName	=	'Minyanville.com';
					$link				= 	$HTPFX.$HTHOST.$objDailyfeed->getDailyFeedUrl($feedid);
					//$link				.=	"&amp;camp=syndication&amp;medium=SiteMap&amp;from=googlenews";
					if($strList!='')
					{
						if($keywords != '')
						{
							$keywords	=	$keywords.",".$strList;
						}
						else
						{
							$keywords	=	$strList;
						}
					}
					if($source!='')
					{
						if($keywords != '')
						{
							$keywords	=	$keywords.",".$source;
						}
						else
						{
							$keywords	=	$source;
						}
					}
					if(count($contributor)>0)
					{
						if($keywords != '')
						{
							$keywords	=	$keywords.",".$contributor['contrName'];
						}
						else
						{
							$keywords	=	$contributor['contrName'];
						}
					}
					if($keywords!='')
					{
						$exp_keys		= explode(",",$keywords);
						$uni_arrKeys	= array_unique($exp_keys);
						$keywordStr		= implode(",",$uni_arrKeys);
					}
					else
					{
						$keywordStr	=	"";
					}
				?>

					<url>
					<loc><?=$link;?></loc>
					<n:news>
						<n:publication>
							<n:name><![CDATA[<? echo $publicationName;?>]]></n:name>
							<n:language>en</n:language>
						</n:publication>
						<n:publication_date><?php echo date('c',strtotime($updationDate)); ?></n:publication_date>
						<n:title><![CDATA[<? echo mswordReplaceSpecialChars($title);?>]]></n:title>
						<n:keywords><![CDATA[<?php echo $keywordStr;?>]]></n:keywords>
						<n:stock_tickers><![CDATA[<?php echo $stock_tickers;?>]]></n:stock_tickers>
				  </n:news>
				</url>
			<?php
			}
			?>
			</urlset>
			<?php
			}
			else
			{
				echo "No Data Found within Last two days!";
			}
			break;
			case 1:
			$arrGoogleArticlesResult = $this->getGoogleArticleDailyfeedData($limit);
			if(count($arrGoogleArticlesResult)>0)
			{
				//header('Content-Type: application/xml');
				header('Content-type: text/xml');
				header('Pragma: public');
				header('Cache-control: private');
				header('Expires: -1');

				echo "<?xml version='1.0' encoding='UTF-8'?>\n";
			?>
			<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'  	xmlns:n='http://www.google.com/schemas/sitemap-news/0.9'>
			<?php
				foreach($arrGoogleArticlesResult as $item)
				{
					$articleId				=	$item['id'];
					$feedBody			=	trim($item['body']);
					$stock_tickers		=	'';
					$keywords		=	'';
					if($item['item_type'] == 1)
					{
						$arrKeys	=   $this->getArticleKeywords($articleId);
					}
					else
					{
					    $itemType=$item['item_type'];
						$arrKeys = $this->getFeedKeywords($articleId,$itemType);
					}
					$stock_tickers		=   $this->stockList($articleId,$feedBody,$item['item_type']);
					if(count($arrKeys)>0)
					{
						if($item['item_type'] == 1)
						{
							$keywords		=	article_body_word_replace(trim($arrKeys[0]['keywords']));
						}
						else
						{
							if($item['item_type'] ==18){
								$getsource			=	$objDailyfeed->getResource($feedid,'18');
								$source				=   article_body_word_replace(TRIM($getsource['source']));
								if(count($arrKeys)>0)
								{
										for($i=0;$i<count($arrKeys);$i++)
										{
											$keywords .=	$arrKeys[$i]['tagname'].",";
										}
										$keywords	=	substr(rtrim($keywords),0,-1);
										$keywords	=	article_body_word_replace(trim($keywords));
								}
								else
								{
									$keywords		=	"";
								}
							}
							if($stock_tickers !='')
							{
								$exp_stockLst	=	explode(",",$stock_tickers);
								foreach($exp_stockLst as $key=>$val)
								{
									$exp_val	=	explode(":",$val);
									$strList	=	$exp_val[1].",".$strList;
								}
								$strList	=	substr(rtrim($strList),0,-1);
								$strList	=	article_body_word_replace(trim($strList));
							}
							if($item['item_type']==18){
								$contributor		=	$this->getConributorName($articleId);
							}elseif($item['item_type']==2){
								$contributor		=	$this->getBuzzConributorName($articleId);
							}
							if($item['item_type']==2 && $keywords==""){
								$keywords=$buzzBanterKeys;
							}
							if($strList!='')
							{
								if($keywords != '')
								{
									$keywords	=	$keywords.",".$strList;
								}
								else
								{
									$keywords	=	$strList;
								}
							}
							if($source!='')
							{
								if($keywords != '')
								{
									$keywords	=	$keywords.",".$source;
								}
								else
								{
									$keywords	=	$source;
								}
							}
							if(count($contributor)>0)
							{
								if($keywords != '')
								{
									$keywords	=	$keywords.",".$contributor['contrName'];
								}
								else
								{
									$keywords	=	$contributor['contrName'];
								}
							}
						}
					}
					$articleDate		=	$item['creation_date'];
					$title				=	article_body_word_replace(trim($item['title']));
					if($item['item_type'] == 2){
						$urlTitle=$objContent->getFirstFiveWords($title);
						$urlDate=date("m/d/Y",strtotime($item['creation_date']));
						$linkurl='/buzz/buzzalert/'.$urlTitle.'/'.$urlDate.'/id/'.$articleId.'?camp=syndication&amp;medium=SiteMap&amp;from=googlenews';
					}else{
						$linkurl            =   getItemURL($item['item_type'],$articleId);
					}
					//$linkurl			.=  "?camp=syndication&amp;medium=SiteMap&amp;from=googlenews";
					$publicationName	=	'Minyanville.com';
					$keywordStr = "";
					if($keywords!='')
					{
						$exp_keys		= explode(",",$keywords);
						$uni_arrKeys	= array_unique($exp_keys);
						$keywordStr		= implode(",",$uni_arrKeys);
					}
				?>

					<url>
					<loc><?=$HTPFX.$HTHOST.$linkurl;?></loc>
					<n:news>
						<n:publication>
							<n:name><![CDATA[<? echo $publicationName;?>]]></n:name>
							<n:language>en</n:language>
						</n:publication>
						<n:publication_date><?php echo date('c',strtotime($articleDate)); ?></n:publication_date>
						<n:title><![CDATA[<? echo mswordReplaceSpecialChars($title);?>]]></n:title>
						<n:keywords><![CDATA[<?php echo $keywordStr;?>]]></n:keywords>
						<n:stock_tickers><![CDATA[<?php echo $stock_tickers;?>]]></n:stock_tickers>
					</n:news>
				</url>
			<?php
			}
			?>
			</urlset>
			<?php
			}
			else
			{
				echo "No Data Found within Last two days!";
			}
			break;
			case 2:
			$arrGoogleBuzzBanter = $this->getGoogleBnBData($limit);
			if(count($arrGoogleBuzzBanter)>0)
			{
				//header('Content-Type: application/xml');
				header('Content-type: text/xml');
				header('Pragma: public');
				header('Cache-control: private');
				header('Expires: -1');

				echo "<?xml version='1.0' encoding='UTF-8'?>\n";
			?>
			<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'  	xmlns:n='http://www.google.com/schemas/sitemap-news/0.9'>
			<?php
				foreach($arrGoogleBuzzBanter as $item)
				{
					$bbId				=	$item['id'];
					$arrKeys			=   $this->getFeedKeywords($bbId,"2");
					if(count($arrKeys)>0)
					{
							for($i=0;$i<count($arrKeys);$i++)
							{
								$keywords .=	$arrKeys[$i]['tagname']." , ";
							}

						$keywords	=	substr(rtrim($keywords),0,-1);
						$keywords	=	article_body_word_replace(trim($keywords));
						if($buzzBanterKeys !='')
						{
							$keywords	=	$keywords.",".$buzzBanterKeys;
						}
					}
					else
					{
						$keywords		=	$buzzBanterKeys;
						//$keywords		=	"";
					}
					$exp_keys			= explode(",",$keywords);
					$uni_arrKeys		= array_unique($exp_keys);
					$keywordStr			= implode(",",$uni_arrKeys);
					$bbBody				=	trim($item['body']);
					$stock_tickers		=	'';
					$bbDate				=	$item['bbDate'];
					$title				=	article_body_word_replace(trim($item['title']));
					$urlTitle=$objContent->getFirstFiveWords($title);
					$urlDate=date("m/d/Y",strtotime($bbDate));
					$linkURL='/buzz/buzzalert/'.$urlTitle.'/'.$urlDate.'/id/'.$bbId.'?camp=syndication&amp;medium=SiteMap&amp;from=googlenews';
					/*$linkURL			=	"/buzz/bookmark.php?id=".$bbId;
					$linkURL			.=  "&amp;camp=syndication&amp;medium=SiteMap&amp;from=googlenews";*/
					$publicationName	=	'Minyanville.com';
					$stock_tickers		= $this->stockList($bbId,$bbBody,$feedType);
				?>

					<url>
					<loc><?=$HTPFX.$HTHOST.$linkURL;?></loc>
					<n:news>
						<n:publication>
							<n:name><![CDATA[<? echo $publicationName;?>]]></n:name>
							<n:language>en</n:language>
						</n:publication>
						<n:publication_date><?php echo date('c',strtotime($bbDate)); ?></n:publication_date>
						<n:title><![CDATA[<? echo mswordReplaceSpecialChars($title);?>]]></n:title>
						<n:keywords><![CDATA[<?php echo $keywordStr;?>]]></n:keywords>
						<n:stock_tickers><![CDATA[<?php echo $stock_tickers;?>]]></n:stock_tickers>
					</n:news>
				</url>
			<?php
			}
			?>
			</urlset>
			<?php
			}
			else
			{
				echo "No Data Found within Last two days!";
			}
			break;
		}
    }
	function stockList($feedId,$body,$feedType)
	{
		global $D_R;
		include_once($D_R."/admin/lib/_admin_data_lib.php");
		$objTicker	=	new Ticker();
	   	$resStockTickers =	$objTicker->getTickersExchange($feedId,$feedType);
	   	if($resStockTickers)
		{
			for($i=0;$i<count($resStockTickers);$i++)
			{
				$stock_tickers[]=$resStockTickers[$i]['exchange'].":".$resStockTickers[$i]['stocksymbol'];
		   	}
   		}
		if(count($stock_tickers)<7){
		   $unique_stocks=  make_stock_array($body);
			if(count($unique_stocks)>0)
			{
				foreach($unique_stocks as $key=>$Stock)
				{

					$sqlExchange = "Select exchange from ex_stock where stocksymbol='".$Stock."'";
					$resultExchange = exec_query($sqlExchange);
					if(count($resultExchange)>0)
					{
						$exchange	=	$resultExchange[0]['exchange'];
						$stock_list[]	=	$exchange.":".$Stock;
					}
				}
			}
			else
			{
				$stock_list	="";
			}
		}
		if(is_array($stock_tickers) and is_array($stock_list)){
			$stockList=array_merge($stock_tickers,$stock_list);
			$stockList=array_unique($stockList);
		}elseif(is_array($stock_tickers)){
			$stockList=$stock_tickers;
		}elseif(is_array($stock_list)){
			$stockList=$stock_list;
		}else{
			$stockList="";
		}
		if(is_array($stockList)){
			$stockList=implode(",",$stockList);
		}
		return $stockList;
	}

	function displayEditorPick(){
		global $HTPFX,$HTHOST,$IMG_SERVER;
		$resEPData = $this->getGoogleArticlesData(15,'google_editors_pick');
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		$LBD= date("D, j M Y H:i:s",strtotime($resEPData[0]['pub_date']));
		?>
<rss version="2.0" xmlns:atom10='http://www.w3.org/2005/Atom'>
<channel>
	<ttl>1</ttl>
	<lastBuildDate><?php echo $LBD;?> EST</lastBuildDate>
	<link><?php echo $HTPFX.$HTHOST; ?></link>
 <description>Minyanville's best articles</description>
 <title>Minyanville's Editor's Picks</title>
 <image>
    <url><?php echo $HTPFX.$HTHOST; ?>/images/rss/mvLogo_PNG_125x40.png</url>
    <title>Minyanville's editor's picks</title>
    <link><?php echo $HTPFX.$HTHOST; ?></link>
 </image>
 <?php
 foreach($resEPData as $story){
		 	$numWords = 100;
			$articlebody=strip_tag_style($story['body']);
			$articlebody=replaceArticleAds($articlebody);
			$tempBody = htmlentities(mswordReplaceSpecialChars(strip_tags($articlebody,'ENT_QUOTES')));
			$bodyArray = str_word_count($tempBody,1);
			$bodyArray = preg_split("/[\s,]+/",$tempBody);
			for($i=0;$i<$numWords;$i++) {
				$body .= $bodyArray[$i] . " ";
			}
			$body = utf8_encode($body);
 ?>
 <item>
   <title><![CDATA[<?php echo mswordReplaceSpecialChars($story['title']); ?>]]></title>
		   <link><?php
		   $objCache = new Cache();
		   		$url = $objCache->getItemLink($story['id'],$story['item_type']);
		      echo $HTPFX.$HTHOST.$url;
		      if($story['item_type']=="1")
		      {
		      	echo "/";
		      }
		   ?>?camp=syndication&amp;medium=editors-pick&amp;from=googlenews</link>
		   <description><![CDATA[<?php echo $body; ?>]]></description>
   <author><?php echo mswordReplaceSpecialChars($story['author']); ?></author>
   <pubDate><?php echo date('D, j M Y H:i:s',strtotime($story['pub_date'])); ?> EST</pubDate>
 </item>
 <?php }
 ?>
 </channel>
</rss>
		<?php
 }
}																// Class End here
?>