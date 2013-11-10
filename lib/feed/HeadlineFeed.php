<?php
class HeadlineFeed
{
	public $partnerId;
	public $feedType;
	public $maxLimit;
	public $partnerName;
		
	function __construct()
	{					
		
	}	
	function setFeedParameters()
	{		
		$this->setPartnerName();
		$this->setFeedData();
	}
	function setPartnerName()
	{
		global $arHeadlineFeedPartners; // defined in lib/config/_rss_config.php
		if($this->partnerId && in_array($this->partnerId, array_keys($arHeadlineFeedPartners)))
		{
			$this->partnerName = $arHeadlineFeedPartners[$this->partnerId]['tracking_name'];
		}
		else
		{	
			$this->partnerName = false;
		}
	}
	function setFeedData()
	{
		switch($this->feedType)
		{
			// constant used in this function are defined in _rss_config.php
			case 'ardf':	
			$this->itemType = constant("ARTICLE_ITEM_ID").",".constant("DAILYFEED_ITEM_ID");
			$this->getFeedResult();
			break;					
			case 'df':
			$this->itemType = DAILYFEED_ITEM_ID;
			$this->getFeedResult();
			break;
			case 'ar':
			$this->itemType = ARTICLE_ITEM_ID;
			$this->getFeedResult();
			break;
			default:
			$this->itemType = false;
			break;			
		}
	}
	function getFeedResult()
	{		
		$sql = "SELECT item_id, title, author_name as author, publish_date as pubDate, description, tickers,content, url from ex_item_meta WHERE item_type IN (".$this->itemType.") AND is_live = '1' ORDER BY publish_date DESC LIMIT 0,".$this->maxLimit;
		$this->arFeedResult = exec_query($sql);								
	}
	function showFeedTemplate()
	{
		header('Content-type: text/xml');
		echo "<?xml version='1.0' encoding='UTF-8'?>";
?>		
        <rss version="2.0">
		<channel>
		<title>Minyanville Headlines</title>
		<description>The Trusted Choice for the Wall Street Voice</description>
		<link>http://www.minyanville.com</link>
		<copyright><?=date('Y')?> Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
        <lastBuildDate><?=date('D, j M Y H:i:s',strtotime($this->arFeedResult[0]['pubDate'])) ?> EDT</lastBuildDate>
		<image><url>http://storage.googleapis.com/mvassets/images/mvLogo.png</url><title>Minyanville Headlines</title><link>http://www.minyanville.com</link><width>144</width><height>88</height></image>
		<? $this->showFeedResult(); ?>
		</channel>
		</rss>
<?
	}	
	function showFeedResult()
	{
		global $HTPFX,$HTHOST;		
		foreach($this->arFeedResult as $arFeed)
		{
			$pubDate=date('D, j M Y H:i:s',strtotime($arFeed['pubDate']));
			$link = $HTPFX.$HTHOST.$arFeed['url'];
			
			$arTickers = "";
			$arBodyTickers=make_stock_array($arFeed['content']);
			
			if(count($arBodyTickers) > 0)
			{
				$arTickers = array_unique($arBodyTickers);
				//$arTickers = explode(",",strtoupper($arFeed['tickers']));
			}
?>
			<item>
			<title><![CDATA[<?=$arFeed['title']?>]]></title>
			<link><![CDATA[<?=$link.$this->getTrackingParam()?>]]></link>
			<description><![CDATA[<?=$arFeed['description']?>]]></description>
			<author><?=$arFeed['author']?></author>		
			<? if(is_array($arTickers)) 
			{
			foreach($arTickers as $ticker) { 	?>
				<category domain="stocksymbol"><?=$ticker;?></category>
			<? } 
			}?>
			<guid isPermaLink="true"><?=$link?></guid>
			<pubDate><?=$pubDate;?> EDT</pubDate>				
		 </item>
<?
		}		
	}
	function getTrackingParam()
	{		
		return "?camp=syndication&medium=portals&from=".$this->partnerName;		
	}
}
?>