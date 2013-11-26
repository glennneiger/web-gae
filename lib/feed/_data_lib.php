<?php
include_once("$D_R/admin/lib/_contributor_class.php");
include_once("$D_R/admin/lib/_article_data_lib.php");
Class feed{
	function feed($type,$partnerId=NULL,$authorId=NULL){
		$this->partnerId=$partnerId;
		$this->authorId=$authorId;
		switch ($type)
		{
			case 'featuredarticles':
			case 'featuredarticlesdailyfeed':
			case 'nasdaq':
			case 'yahoo':
			case 'yahooFull':
			case 'mvpremiumyahoo':
			case 'googlecurrents':
			case 'gravity':
			$this->type=$type;
			break;
			case 'articlebuzz':
			$this->type='1-2';
			break;
			default:
			$sqlGetItemType="select id from ex_item_type where item_text='".$type."' or item_table='".$type."'";
			$resGetItemType=exec_query($sqlGetItemType,1);
			if(count($resGetItemType)>0){
				$this->type=$resGetItemType['id'];
			}else{
				$this->type='1';
			}
		}
	}
	function getMediaFeedData($maxItems,$category_id = NULL)
	{
		return $this->getArticleMediaFeedData($maxItems);
	}
	function getArticleMediaFeedData($limit,$currentFilter=NULL)
	{
		global $HTPFX,$HTHOST,$smvcList;
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author,
		IF(publish_date,publish_date,date) pubDate,IF(character_text != '',character_text,body) as description,featureimage,layout_type
		FROM articles,contributors WHERE articles.contrib_id = contributors.id AND articles.approved='1' and articles.is_live='1'";

		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}
		$sqlGetArticles .= " ORDER BY pubDate DESC LIMIT 0,".$limit;
		return exec_query($sqlGetArticles);
	}
	function getFeedData($maxItems,$currentFilter=NULL,$filters=NULL){
		global $partnerIdYahooLiveStand;
		switch($this->type){
			case '1':
				// get feed for yahoo livestand
				if(isset($this->partnerId) && trim(strtolower($this->partnerId))==trim(strtolower($partnerIdYahooLiveStand))){
					return $this->getArticleFeedDataYahooLiveStand($maxItems,$currentFilter, $filters);
				}
				// get feed for everyone else
				else if($filters=="dailyfinace" || isset($this->partnerId) && trim(strtolower($this->partnerId))==trim(strtolower('241f4baccd80454d89cdf63d7af7920b'))){
				    return $this->getArticleDailyFinaceData($maxItems,$currentFilter,$filters);
				}
				else{
					return $this->getArticleFeedData($maxItems,$currentFilter,$filters);
				}
				break;
			case '2':
				if(trim($this->partnerId)=='a87ff679a2f3e71d9181a67b7542122c'){ // for light speed
					return $this->getBuzzFeedDataLightSpeed($maxItems,$currentFilter, $filters);
				}
				else{ // for every other buzz feeds
					return $this->getBuzzFeedData($maxItems,$currentFilter, $filters);
				}
				break;
			case '1-2':
				return $this->getArticleBuzzFeedData($maxItems,$currentFilter, $filters);
				break;
			case 'featuredarticles':
				return $this->getStreetFeaturedArticles($maxItems,$currentFilter,$filters);
				break;
			case 'featuredarticlesdailyfeed':
				return $this->getFeaturedArtilcesDailyfeed($maxItems,$currentFilter,$filters);
				break;
			case 'nasdaq':
				return $this->getArticleNasdaqFeedData($maxItems,$currentFilter,$filters);
				break;
			case 'mvpremiumyahoo':
				return $this->getMvPremiumYahooFeedData($maxItems,$currentFilter,$filters);
			case 'googlecurrents':
				return $this->getArticleGoogleCurrentsFeedData($maxItems,$currentFilter,$filters);
				break;
			case 'yahoo':
				return $this->getArticleYahooFeedData($maxItems,"",$currentFilter,$filters);
				break;
			case 'yahooFull':
				return $this->getArticleYahooFeedData($maxItems,"1",$currentFilter,$filters);
				break;
			case 'gravity':
			default:
				return $this->getArticleFeedData($maxItems,$currentFilter,$filters);
		}
	}
	function getFeaturedArtilcesDailyfeed($maxItems,$currentFilter,$filters)
	{
		global $HTPFX,$HTHOST;
		$sql = "SELECT A.body AS artilce_body, D.body AS dailyfeed_body, EM.item_id, EM.item_type, EM.title, EM.author_name author,EM.author_id,
				EM.publish_date AS pubDate ,EM.url,EM.description
				FROM content_syndication AS CS, ex_item_meta AS EM
				LEFT JOIN articles A ON (A.id=EM.item_id  AND EM.item_type='1')
				LEFT JOIN daily_feed D ON (D.id = EM.item_id AND EM.item_type='18')
				WHERE CS.item_id = EM.item_id AND CS.item_type = EM.item_type AND EM.is_live = '1' AND
				CS.is_syndicated = '1' AND CS.syndication_channel = 'ts_featured' AND CS.item_type IN ('1','18')";
			if($currentFilter){
				$objContrib= new contributor();
					$contribFilterId=$objContrib->getExcludedPartnerId();
				$sql .= " and EM.author_id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
			contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId))";
			}
			$sql .= " ORDER BY pubDate DESC LIMIT 0,".$maxItems;

		$results = exec_query($sql);
		$i=0;
		foreach($results as $article){
			$articleFeedData[$i]['title']=$article['title'];
			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$HTPFX.$HTHOST.$article['url'];
			$articleFeedData[$i]['guid']=$HTPFX.$HTHOST.$article['url'];
			$articleFeedData[$i]['pubDate']=$article['pubDate'];
			$articleFeedData[$i]['language']='en';
			$articleFeedData[$i]['desc']=$article['description'];
			if($article['item_type'] == 1)
			{
				$story_content = $article['artilce_body'];
			}
			else
			{
				$story_content = $article['dailyfeed_body'];
			}
			$articleFeedData[$i]['content']=mswordReplaceSpecialChars($story_content);
			$stocksArr=make_stock_array($story_content);
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$i++;
		}
		return $articleFeedData;
	}
	function getStreetFeaturedArticles($maxItems,$currentFilter,$filters)
	{
		global $HTPFX,$HTHOST;
		$sql = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer,IF(articles.publish_date = '0000-00-00 00:00:00',articles.date, articles.publish_date) pubDate,articles.date,articles.character_text, articles.body,articles.subsection_ids FROM articles, contributors, content_syndication c where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and c.is_streetfeed='1' and c.syndication_channel='ts_featured' and c.item_id=articles.id and c.item_type='1'";
	if($currentFilter){
		$objContrib= new contributor();
			$contribFilterId=$objContrib->getExcludedPartnerId();
		$sql .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
	contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId))";
	}
	$sql .= " ORDER BY pubDate DESC LIMIT 0,".$maxItems;
	$results = exec_query($sql);
	$i=0;
	foreach($results as $article){
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['guid']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleFeedData[$i]['pubDate']=$article['date'];
			}else{
				$articleFeedData[$i]['pubDate']=$article['pubDate'];
			}
			$articleFeedData[$i]['language']='en';
			$articleFeedData[$i]['desc']=$article['character_text'];
			$articleFeedData[$i]['content']=mswordReplaceSpecialChars($article['body']);
			$stocksArr=make_stock_array($article['body']);
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	}
	function getArticleFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST,$IMG_SERVER;
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer,articles.
		publish_date pubDate,articles.date,articles.character_text, articles.body,articles.subsection_ids,articles.is_marketwatch marketwatch,
		articles.is_fox fox,articles.is_msnfeed msn, articles.is_yahoofeed yahoo";
		$sqlGetArticles .= " from articles, contributors ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1'";
		if($this->authorId){
			$sqlGetArticles .= " and articles.contrib_id='".$this->authorId."'";
		}
		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}
		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetArticles .= " and B.date>'".mysqlNow()."' - interval 2 month having articles.publish_date > '".mysqlNow()."' - interval ".$val." minute ";
					break;
				}
			}
		}
		if($this->partnerId=="c4ca4238a0b923820dcc509a6f75849b"){
			$sqlGetArticles .= " and articles.id<>'42565'";
		}

		$sqlGetArticles .= " ORDER BY articles.publish_date DESC,articles.date DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){

			$tag = getTag($article['body'],'img');
			$articleImg = $tag[0]['src'];
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			if(empty($articleImg))
			{
				$articleFeedData[$i]['thumbnail_image']=$IMG_SERVER."/images/defaultarticlelist.png";
			}
			else
			{
				$articleFeedData[$i]['thumbnail_image']=$articleImg;
			}
			$articleFeedData[$i]['link']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['guid']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleFeedData[$i]['pubDate']=$article['date'];
			}else{
				$articleFeedData[$i]['pubDate']=$article['pubDate'];
			}
			$articleFeedData[$i]['language']='en';
			$articleFeedData[$i]['desc']=$article['character_text'];
			$articleFeedData[$i]['content']=mswordReplaceSpecialChars($article['body']);
			$tags_qry="select ES.stocksymbol ticker from ex_item_tags EIT,ex_tags ET,ex_stock ES where EIT.tag_id=ET.id and ET.tag=ES.stocksymbol and EIT.item_id=".$article['id']." and EIT.item_type=1 GROUP BY ES.stocksymbol";
			$stocksArr=exec_query($tags_qry);
			if($article['marketwatch']){
				$articleFeedData[$i]['syndications'][]="Marketwatch";
			}
			if($article['fox']){
							$articleFeedData[$i]['syndications'][]="Fox Business";
			}
			if($article['msn']){
							$articleFeedData[$i]['syndications'][]="MSN";
			}
			if($article['yahoo']){
							$articleFeedData[$i]['syndications'][]="Yahoo Finance";
			}
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value['ticker'];
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	}

	function getArticleDailyFinaceData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST,$IMG_SERVER;

		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer,IF(articles.publish_date = '0000-00-00 00:00:00',articles.date, articles.publish_date) pubDate,articles.date,articles.character_text, articles.body,articles.subsection_ids,articles.is_marketwatch marketwatch,
		articles.is_fox fox,articles.is_msnfeed msn, articles.is_yahoofeed yahoo";
		$sqlGetArticles .= " from articles, contributors,content_syndication ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and content_syndication.item_type='1'
and content_syndication.syndication_channel='dailyfinance' and content_syndication.is_syndicated='1' and content_syndication.item_id=articles.id";

		$sqlGetArticles .= " ORDER BY pubDate DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['guid']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleFeedData[$i]['pubDate']=$article['date'];
			}else{
				$articleFeedData[$i]['pubDate']=$article['pubDate'];
			}
			$tag = getTag($article['body'],'img');
			$articleImg = $tag[0]['src'];
			if(empty($articleImg))
			{
				$articleFeedData[$i]['thumbnail_image']=$IMG_SERVER."/images/defaultarticlelist.png";
			}
			else
			{
				$articleFeedData[$i]['thumbnail_image']=$articleImg;
			}

			$articleFeedData[$i]['language']='en';
			$articleFeedData[$i]['desc']=$article['character_text'];
			$articleFeedData[$i]['content']=mswordReplaceSpecialChars($article['body']);
			$tags_qry="select ES.stocksymbol ticker from ex_item_tags EIT,ex_tags ET,ex_stock ES where EIT.tag_id=ET.id and ET.tag=ES.stocksymbol and EIT.item_id=".$article['id']." and EIT.item_type=1 GROUP BY ES.stocksymbol";
			$stocksArr=exec_query($tags_qry);

			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value['ticker'];
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}

		return $articleFeedData;
	}

	function getArticleFeedDataYahooLiveStand($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST;
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer,date_format(articles.
		publish_date,'%a, %d %b %Y %H:%i:%s EST') pubDate,date_format(articles.date,'%a, %d %b %Y %H:%i:%s %p EST'),articles.character_text, articles.body,articles.subsection_ids,articles.is_marketwatch marketwatch,
		articles.is_fox fox,articles.is_msnfeed msn, articles.is_yahoofeed yahoo";
		$sqlGetArticles .= " from articles, contributors ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1'";
		if($this->authorId){
			$sqlGetArticles .= " and articles.contrib_id='".$this->authorId."'";
		}
		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}
		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetArticles .= " and B.date>'".mysqlNow()."' - interval 2 month having articles.publish_date > '".mysqlNow()."' - interval ".$val." minute ";
					break;
				}
			}
		}
		else
		{
			$sqlGetArticles .= " and articles.date>('".mysqlNow()."' - interval 1 month) ";
		}
		$maxItemLimit=50;
		$sqlGetArticles .= " ORDER BY articles.publish_date DESC,articles.date DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		$articleFeedData['title']='Minyanville';
		$articleFeedData['link']='http://www.minyanville.com';
		$articleFeedData['description']='Yahoo LiveStand Feed';
		$articleFeedData['language']='en-US';
		foreach($resGetArticles as $article){
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['dc:creator']=$article['author'];
			$articleFeedData[$i]['link']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['guid']=$HTPFX.$HTHOST.makeArticleslink($article['id']);
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleFeedData[$i]['pubDate']=$article['date'];
			}else{
				$articleFeedData[$i]['pubDate']=$article['pubDate'];
			}

			$articleFeedData[$i]['description']=$article['character_text'];

			$articleFeedData[$i]['content:encoded']=mswordReplaceSpecialChars(replaceArticleAds($article['body']));
			/*
			$stocksArr=make_stock_array($article['body']);
			if($article['marketwatch']){
				$articleFeedData[$i]['syndications'][]="Marketwatch";
			}
			if($article['fox']){
							$articleFeedData[$i]['syndications'][]="Fox Business";
			}
			if($article['msn']){
							$articleFeedData[$i]['syndications'][]="MSN";
			}
			if($article['yahoo']){
							$articleFeedData[$i]['syndications'][]="Yahoo Finance";
			}
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}

			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			*/
			$i++;
		}
		return $articleFeedData;
	}

	function getArticleBuzzFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST,$smvcList;
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticleBuzz = "SELECT EIT.item_id,EIT.title,EIT.author_name author,EIT.author_id, EIT.publish_date,
		EIT.tickers,A.body articlebody,B.body buzzbody,B.login,EIT.item_type,EIT.url,EIT.description FROM ex_item_meta EIT
LEFT JOIN articles A ON (A.id=EIT.item_id  AND EIT.item_type='1')
LEFT JOIN buzzbanter B ON (EIT.item_id=B.id AND EIT.item_type='2')
WHERE 	EIT.item_type IN ('1','2') and EIT.is_live='1' and EIT.publish_date>'".mysqlNow()."' - interval 1 week ";

		if($currentFilter){
			$sqlGetArticleBuzz .= " AND EIT.author_id NOT IN ($contribFilterId) ";
		}
		$flagDuration=false;
		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'startTime':
						$startTime=$val;
						$sqlGetBuzzFilter = " and EIT.publish_date < '".$startTime."'";
						break;
					case 'durationFilter':
						if(empty($startTime))
						{
							$startTime=mysqlNow();
						}
						$flagDuration=true;
						$sqlGetBuzzFilter = " and EIT.publish_date < '".$startTime."' and EIT.publish_date > '".$startTime."' - interval ".$val." minute ";
						break;
				}
			}
		}		
		$sqlGetArticleBuzz.= $sqlGetBuzzFilter;
		$sqlGetArticleBuzz .= " ORDER BY  EIT.publish_date DESC ";
		if(!$flagDuration){
			$sqlGetArticleBuzz.="LIMIT 0,".$maxItemLimit;
		}

	
		$resGetArticleBuzz=exec_query($sqlGetArticleBuzz);
		$i=0;
		foreach($resGetArticleBuzz as $articleBuzz){
			if($articleBuzz['item_type']=='2' && strpos($articleBuzz['login'],'automated')>0)
				continue;
			$articleBuzzFeedData[$i]['title']=html_entity_decode($articleBuzz['title']);
			$articleBuzzFeedData[$i]['author']=$articleBuzz['author'];
			$articleBuzzFeedData[$i]['link']=$HTPFX.$HTHOST.$articleBuzz['url'];
			$articleBuzzFeedData[$i]['guid']=$HTPFX.$HTHOST.$articleBuzz['url'];
			$articleBuzzFeedData[$i]['pubDate']=$articleBuzz['publish_date'];
			$articleBuzzFeedData[$i]['language']='en';
			$articleBuzzFeedData[$i]['desc']=html_entity_decode(html_entity_decode($articleBuzz['description']));
			$articleBuzzFeedData[$i]['authorId']=$articleBuzz['author_id'];
			switch($articleBuzz['item_type'])
			{
				case '1':
					$articleBuzzFeedData[$i]['content']=mswordReplaceSpecialChars($articleBuzz['articlebody']);
					$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$articleBuzz['subsection_ids'].") ";
					$resGetCategory=exec_query($sqlGetCategory);
					foreach($resGetCategory as $id=>$value)
					{
						$articleBuzzFeedData[$i]['category'][]=$value['name'];
					}
					break;
				case '2':
					$articleBuzzFeedData[$i]['content']=mswordReplaceSpecialChars($articleBuzz['buzzbody']);
					$articleBuzzFeedData[$i]['content'].='<div>'.displayChartBuzzImages($articleBuzz['item_id']).'</div>';
					$articleBuzzFeedData[$i]['category'][]='Stocks And Markets';
					break;
			}
			$articleBuzzFeedData[$i]['content']=html_entity_decode($articleBuzzFeedData[$i]['content']);
			$articleBuzzFeedData[$i]['content']=$this->replaceImgStyleAttr($articleBuzzFeedData[$i]['content']);
			$stocksArr=explode(',',$articleBuzz['tickers']);
			if(is_array($stocksArr)){
				foreach($stocksArr as $id=>$value)
				{
					$articleBuzzFeedData[$i]['ticker'][]=$value;
				}
			}
			$i++;
		}
		return $articleBuzzFeedData;
	}

	function getBuzzFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
			global $HTPFX,$HTHOST,$IMG_SERVER,$smvcList;
			$objContrib= new contributor();
			$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetBuzz = "SELECT B.id id, B.title,B.image, C.name author, C.disclaimer,IF(B.publish_date,B.publish_date,B.date) pubDate, B.body
					FROM buzzbanter B, contributors C
					WHERE B.contrib_id = C.id AND B.approved='1' AND B.is_live='1' and login !='(automated)' ";
					if($currentFilter){
			$sqlGetBuzz .= " and C.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
					}
		$flagDuration=false;
		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetBuzz .= " and B.date>'".mysqlNow()."' - interval 2 month having pubDate > '".mysqlNow()."' - interval ".$val." minute ";
						$flagDuration=true;
						break;
						}
						}
					}
				else
					{
						$sqlGetBuzz .= " and B.date>('".mysqlNow()."' - interval 1 month) ";
					}
		$sqlGetBuzz .= " ORDER BY pubDate DESC ";
		if(!$flagDuration){
			$sqlGetBuzz.= " LIMIT 0,".$maxItemLimit;
		}
					$resGetBuzz=exec_query($sqlGetBuzz);
					$i=0;
					foreach($resGetBuzz as $buzz){
						$BuzzFeedData[$i]['title']=$buzz['title'];
						$BuzzFeedData[$i]['author']=$buzz['author'];
						$BuzzFeedData[$i]['link']=$HTPFX.$HTHOST."/buzz/bookmark.php?id=".$buzz['id'];
						$BuzzFeedData[$i]['guid']=$HTPFX.$HTHOST."/buzz/bookmark.php?id=".$buzz['id'];
						$BuzzFeedData[$i]['pubDate']=$buzz['pubDate'];
						$BuzzFeedData[$i]['language']='en';
						if(empty($buzz['image']))
						{
							$BuzzFeedData[$i]['thumbnail_image']=$IMG_SERVER."/images/defaultarticlelist3.png";
						}
						else
						{
							$BuzzFeedData[$i]['thumbnail_image']=$IMG_SERVER.$buzz['image'];
						}
						$BuzzFeedData[$i]['desc']=substr(strip_tags($buzz['body']),0,150);
						$BuzzFeedData[$i]['content']=mswordReplaceSpecialChars($buzz['body']);
						$BuzzFeedData[$i]['content'].='<div>'.displayChartBuzzImages($buzz['id']).'</div>';
						$sqlGetTicker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and EIT.item_type='2' and EIT.item_id='".$buzz['id']."'";
						$getTicker=exec_query($sqlGetTicker);
						$stocksArr=make_stock_array($buzz['body']);
						if($buzz['marketwatch']){
							$BuzzFeedData[$i]['syndications'][]="Marketwatch";
						}
						if($buzz['fox']){
							$BuzzFeedData[$i]['syndications'][]="Fox Business";
						}
						if($buzz['msn']){
							$BuzzFeedData[$i]['syndications'][]="MSN";
						}
						if($buzz['yahoo']){
							$BuzzFeedData[$i]['syndications'][]="Yahoo Finance";
						}
						if(is_array($stocksArr)){
							foreach($stocksArr as $id=>$value)
							{
								$BuzzFeedData[$i]['ticker'][]=$value;
							}
						}
						if(is_array($getTicker))
							foreach($getTicker as $id=>$value)
							{
								$BuzzFeedData[$i]['ticker'][]=$value['stocksymbol'];
							}
						$BuzzFeedData[$i]['category'][]='Stocks And Markets';
						$i++;
					}
		return $BuzzFeedData;
	}
	function getBuzzFeedDataLightSpeed($maxItemLimit,$currentFilter=NULL,$filters=NULL){
			global $HTPFX,$HTHOST,$IMG_SERVER,$smvcList;

			$objContrib= new contributor();
			$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetBuzz = "SELECT B.id id, B.position position,B.title,B.image,B.login, C.id user_id,C.name author, C.disclaimer,IF(B.publish_date,B.publish_date,B.date) pubDate, B.body
					FROM buzzbanter B, contributors C
					WHERE B.contrib_id = C.id AND B.approved='1' AND B.is_live='1'
					AND DATE_FORMAT(B.date,'%Y-%m-%d') = DATE_FORMAT('".mysqlNow()."','%Y-%m-%d')";

					if($currentFilter){
			$sqlGetBuzz .= " and C.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
					}
		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetBuzz .= " and B.date>'".mysqlNow()."' - interval 2 month having pubDate > '".mysqlNow()."' - interval ".$val." minute ";
						break;
						}
						}
					}
					else
					{
						$sqlGetBuzz .= " and B.date>('".mysqlNow()."' - interval 1 month) ";
					}
					if (!$maxItemLimit) $maxItemLimit="250";
					$sqlGetBuzz .= "ORDER BY pubDate DESC LIMIT 0,".$maxItemLimit;
					$resGetBuzz=exec_query($sqlGetBuzz);
					$i=0;
					foreach($resGetBuzz as $buzz){

						$BuzzFeedData[$i]['title']=$buzz['title'];
						if (trim($buzz['login'])=='(automated)'){

							$BuzzFeedData[$i]['type']='article';
						}
						else{
							$BuzzFeedData[$i]['type']='buzz';
						}
						if ($buzz['image']!=''){
							$BuzzFeedData[$i]['icon']=$IMG_SERVER.$buzz['image'];
						}
						else{
							$BuzzFeedData[$i]['icon']='';
						}
						$BuzzFeedData[$i]['author']='<a target="_blank" href="'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$buzz['user_id'].'&from=lightspeed">'.$buzz['author'].'</a>';

						$BuzzFeedData[$i]['link']=$HTPFX.$HTHOST."/buzz/bookmark.php?id=".$buzz['id'];
						$BuzzFeedData[$i]['guid']=$HTPFX.$HTHOST."/buzz/bookmark.php?id=".$buzz['id'];
						$BuzzFeedData[$i]['pubDate']=$buzz['pubDate'];
						$BuzzFeedData[$i]['language']='en';
						$BuzzFeedData[$i]['desc']=substr(strip_tags($buzz['body']),0,150);
						$BuzzFeedData[$i]['content']=mswordReplaceSpecialChars($buzz['body']);
						if (!is_numeric($buzz['position'])){
							$BuzzFeedData[$i]['position']=mswordReplaceSpecialChars($buzz['position']);
						}
						else{
							$BuzzFeedData[$i]['position']="";
						}
						//$BuzzFeedData[$i]['content'].='<div>'.displayChartBuzzImages($buzz['id']).'</div>';

						$qryCharts="select id,original_url,thumb_url from item_charts where item_id='".$buzz['id']."' and item_type='2'";
						$resultCharts=exec_query($qryCharts);
						if($resultCharts){

							foreach ($resultCharts as $key => $val) {
								$BuzzFeedData[$i]['charts'][]='<a target="_blank" href="'.$val['original_url'].'" ><img src="'.$val['thumb_url'].'" /></a><br/><a target="_blank" href="'.$val['original_url'].'" >Click here to enlarge</a>';
							}
						}

						$sqlGetTicker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and EIT.item_type='2' and EIT.item_id='".$buzz['id']."'";
						$getTicker=exec_query($sqlGetTicker);
						$stocksArr=make_stock_array($buzz['body']);
						if($buzz['marketwatch']){
							$BuzzFeedData[$i]['syndications'][]="Marketwatch";
						}
						if($buzz['fox']){
							$BuzzFeedData[$i]['syndications'][]="Fox Business";
						}
						if($buzz['msn']){
							$BuzzFeedData[$i]['syndications'][]="MSN";
						}
						if($buzz['yahoo']){
							$BuzzFeedData[$i]['syndications'][]="Yahoo Finance";
						}
						if(is_array($stocksArr)){
							foreach($stocksArr as $id=>$value)
							{
								$BuzzFeedData[$i]['ticker'][]=$value;
							}
						}
						if(is_array($getTicker))
							foreach($getTicker as $id=>$value)
							{
								$BuzzFeedData[$i]['ticker'][]=$value['stocksymbol'];
							}
						$BuzzFeedData[$i]['category'][]='Stocks And Markets';
						$i++;
					}


		return $BuzzFeedData;
	}

	function getCommentFeedData($maxItemLimit,$tid){
			global $HTPFX,$HTHOST,$smvcList;
					$sqlGetComments = "SELECT EP.id id, EP.title, CONCAT(S.fname,' ',S.lname) author,
					created_on pubDate, EPC.body FROM ex_post EP, ex_post_content EPC, subscription S
					WHERE EP.poster_id = S.id AND EP.id=EPC.post_id AND EP.suspended='0' ";
					$sqlGetComments.="AND EP.thread_id='".$tid."' ";
					$sqlGetComments .= "ORDER BY pubDate DESC LIMIT 0,".$maxItemLimit;
					$resGetComments=exec_query($sqlGetComments);
					$i=0;
					foreach($resGetComments as $comment){
						$commentFeedData[$i]['title']=$comment['title'];
						$commentFeedData[$i]['author']=$comment['author'];
						$commentFeedData[$i]['link']=$HTPFX.$HTHOST."/community/discussion/discussion/".$tid."#cmt".$comment['id']."";
						$commentFeedData[$i]['guid']=$HTPFX.$HTHOST."/community/discussion/discussion/".$tid."#cmt".$comment['id']."";
						$commentFeedData[$i]['pubDate']=$comment['pubDate'];
						$commentFeedData[$i]['language']='en';
						$commentFeedData[$i]['desc']=substr(strip_tags($comment['body']),0,150);
						$commentFeedData[$i]['content']=mswordReplaceSpecialChars($comment['body']);
						$stocksArr=make_stock_array($comment['body']);
						if($comment['marketwatch']){
							$commentFeedData[$i]['syndications'][]="Marketwatch";
						}
						if($comment['fox']){
							$commentFeedData[$i]['syndications'][]="Fox Business";
						}
						if($comment['msn']){
							$commentFeedData[$i]['syndications'][]="MSN";
						}
						if($comment['yahoo']){
							$commentFeedData[$i]['syndications'][]="Yahoo Finance";
						}
						foreach($stocksArr as $id=>$value)
						{
							$commentFeedData[$i]['ticker'][]=$value;
						}
						$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
						$resGetCategory=exec_query($sqlGetCategory);
						foreach($resGetCategory as $id=>$value)
						{
							$commentFeedData[$i]['category'][]=$value['name'];
						}
						$i++;
					}
		return $commentFeedData;
	}

	function getDailyFeedMediaData($limit,$topic,$currentFilter=NULL)
	{
		global $HTPFX,$HTHOST;
		$objContrib= new contributor();
		if($topic!='')
		{
			$getTopic		=	"SELECT id FROM ex_tags where tag = '".$topic."'";
			$resultTopic	=	 exec_query($getTopic,1);
			if($resultTopic && count($resultTopic)>0)
			{
			$tagId	=	$resultTopic ['id'];
			$sqlGoogleDailyFeeds	=	"select D.id,D.title,D.body,C.name author,
		IF(D.publish_date,D.publish_date,D.updation_date) pubDate, D.body as description  from daily_feed D, ex_item_tags T,contributors C
		 where D.is_approved='1' and D.is_deleted='0' and  D.contrib_id=C.id  and T.item_id= D.id and T.tag_id = ".$tagId." and T.item_type='18'";
				if(currentFilter){
				    $contribFilterId=$objContrib->getExcludedPartnerId();
					$sqlGoogleDailyFeeds .= " and C.id NOT IN ($contribFilterId)) ";
				}
			}
		}
		else
		{
		 $sqlGoogleDailyFeeds = "select D.id,D.title,D.body,C.name author,IF(D.publish_date,D.publish_date,D.updation_date) pubDate,
		  D.body as description FROM daily_feed D,contributors C WHERE D.is_approved='1' and D.is_deleted='0' and  D.contrib_id=C.id $filter";
			 if($currentFilter){
			        $contribFilterId=$objContrib->getExcludedPartnerId();
					$sqlGoogleDailyFeeds .= " and C.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId ))";
				}

		}
		$sqlGoogleDailyFeeds .="ORDER BY pubDate DESC LIMIT 0,".$limit;
		return exec_query($sqlGoogleDailyFeeds);
	}

	function getFeedImage($feedid,$feedType)
	{
	 $sqlFeedImage = "select M.id,M.url,M.name FROM ex_item_image M WHERE item_id=".$feedid." and item_type=".$feedType." LIMIT 1";
	 return exec_query($sqlFeedImage,1);
	}
	function getVideoMediaRssData($maxLimit)
	{
	$sql_videos = " SELECT id,publish_time,description,videofile,stillfile,thumbfile,title,videofile_wmv,duration,podcasturl,podcastsize FROM mvtv WHERE approved='1' and is_live ='1' order by id desc limit 0,".$maxLimit;
		$results_videos = exec_query($sql_videos);
		if($results_videos)
		{
			return $results_videos;
		}
		else
		{
			return false;
		}
	}
	function getMimeType($videoExt)
	{
		$sql_mime = "SELECT mine_type from ex_mime_type where mime_name='".$videoExt."'";
		$results_mine = exec_query($sql_mime,1);
		if($results_mine)
		{
			return $results_mine['mine_type'];
		}	else	{
			return false; }
    }


	function getDailyFeedRssData($limit)
	{
		global $HTPFX,$HTHOST;
		$objDailyfeed= new Dailyfeed();
		$objMemcache= new Cache();
			$sqlGoogleDailyFeeds = "select D.id,D.title,D.body,C.name author,IF(D.publish_date,D.publish_date,D.updation_date) pubDate,
			D.excerpt as description FROM daily_feed D,contributors C WHERE D.is_approved='1' and D.is_deleted='0' and  D.contrib_id=C.id
			 and D.creation_date>('".mysqlNow()."' - interval 1 month) ";
			if($this->authorId){
				$sqlGoogleDailyFeeds .= " and D.contrib_id='".$this->authorId."'";
			}
		 	$sqlGoogleDailyFeeds .= " ORDER BY pubDate DESC LIMIT 0,".$limit;
			$result=exec_query($sqlGoogleDailyFeeds);

		foreach($result as $key=>$value){
		  $urlTitle=$HTPFX.$HTHOST.$objDailyfeed->getDailyFeedUrl($value['id']);
		  $result[$key]['link']=$urlTitle;
		  $result[$key]['guid']=$urlTitle;
		  $result[$key]['language']='en';
		  $value['body']=$objMemcache->replaceBlankArticleAd($value['body']);
		 // if($value['description']==""){
		  	$value['description']=$value['body'];
		 // }
		  $result[$key]['desc']=htmlentities(strip_tags($value['description'],'ENT_QUOTES'));
		  $result[$key]['content']= htmlentities(strip_tags($value['body'],'ENT_QUOTES'));
		}
		return $result;
	}

	function replaceImgStyleAttr($body){
		$body=preg_replace_callback('/<img[^>]+>/i',"changeImageStyle", $body);
		return $body;
	}

	function getMvPremiumYahooFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL)
	{
		global $HTPFX,$HTHOST;
		$objArticle	= new ArticleData();
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		
		$sqlGetArticles ="SELECT daily_feed.id id, daily_feed.`excerpt` AS character_text, daily_feed.title, contributors.name author, 
contributors.disclaimer, daily_feed.publish_date pubDate,
IF(daily_feed.publish_date = '0000-00-0000:00:00',daily_feed.`creation_date`,daily_feed.publish_date) AS display_date,
daily_feed.creation_date AS 'date', daily_feed.body, '' AS subsection_ids, '' AS is_audio 
FROM `daily_feed`, `contributors`, content_syndication AS cs 
 WHERE daily_feed.contrib_id = contributors.id AND daily_feed.`is_approved`='1'
  AND daily_feed.is_live='1'
AND cs.item_id=daily_feed.id  AND daily_feed.is_yahoofeed='1' AND cs.syndication_channel='yahoo' AND cs.`is_syndicated`='1'  
		";  
		
		
		 $sqlGetArticles .= " ORDER BY display_date DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){
			$articleFullURL = $HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$articleFullURL;
			$articleFeedData[$i]['guid']=$article['id'];
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleDate=$article['date'];
			}else{
				$articleDate=$article['pubDate'];
			}
			$articleFeedData[$i]['pubDate'] = $articleDate;
			$articleFeedData[$i]['language']='en';
			if($article['is_audio']=='1'){
				$audio = $objArticle->getArticleAudio($article['id']);
				foreach($audio as $aud){
					if($aud['item_key']=='audiofile'){
						$audioFile = $aud['item_value'];
					}elseif($aud['item_key']=='radiofile'){
						$radioFile = $aud['item_value'];
					}
				}
			}else{
				$audioFile="";
				$radioFile="";
			}
			$articleBody = mswordReplaceSpecialChars($article['body']);
			$articleBody = replaceArticleAds($articleBody);
			$articleBody = str_replace('<b ','<strong ',$articleBody);
			$articleBody = str_replace('<b>','<strong>',$articleBody);
			$articleBody = str_replace('</b>','</strong>',$articleBody);
			$articleBody = str_replace('<i>','<em>',$articleBody);
			$articleBody = str_replace('<i ','<em ',$articleBody);
			$articleBody = str_replace('</i>','</em>',$articleBody);
			if(substr_count($articleBody,"{FLIKE}") > 0){
				$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
			}
			if(substr_count($articleBody,"{AUDIO}") > 0)
			{
				if($audioFile!='')
				{
					$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{AUDIO}"," ", $articleBody);
				}
			}
			if(substr_count($articleBody,"{RADIO}") > 0)
			{
				if($radioFile!='')
				{
					$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{RADIO}"," ", $articleBody);
				}
			}
			$tag = getTag($article['body'],'img');

			if($tag[0]['src']!="")
			{
				$articleFeedData[$i]['articleImg'] = $tag[0]['src'];
			}
			else
			{
				$articleFeedData[$i]['articleImg'] = $IMG_SERVER."images/mv_social_icon.jpg";
			}

			$articleFeedData[$i]['desc']=$articleBody;
			$articleFeedData[$i]['content']=$article['character_text'];
			$stocksArr=make_stock_array($article['body'],'',$articleDate);
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	} 	
	
	function getArticleYahooFeedData($maxItemLimit,$yahooFullBodySyndication,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST;
		$objMemcache= new Cache();
		$objArticle	= new ArticleData();
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select '1' type,articles.id id, articles.character_text, articles.title, contributors.name author, 
		contributors.disclaimer, articles.publish_date pubDate,
		IF(articles.publish_date = '0000-00-0000:00:00',articles.date,articles.publish_date) AS display_date,
		articles.date, articles.body, articles.subsection_ids, articles.is_audio";
		$sqlGetArticles .= " from articles, `contributors`, content_syndication AS cs ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1'
		AND cs.item_id=articles.id  AND articles.date > ('".mysqlNow()."' - INTERVAL 1 MONTH)  ";
		if($yahooFullBodySyndication=="1")
		{
			$sqlGetArticles .= " AND articles.is_yahoofeed='0' AND cs.syndication_channel='yahoo_full_body' AND cs.`is_syndicated`='1' ";
		}
		else{
			$sqlGetArticles .= " AND articles.is_yahoofeed='1' AND cs.syndication_channel='yahoo' AND cs.`is_syndicated`='1' ";
		}
		
		if($this->authorId){
			$sqlGetArticles .= " and articles.contrib_id='".$this->authorId."'";
		}
		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}

		$sqlGetArticles .=" UNION SELECT '18' type, daily_feed.id id, daily_feed.`excerpt` AS character_text, daily_feed.title, contributors.name author, 
	contributors.disclaimer, daily_feed.publish_date pubDate,
	IF(daily_feed.publish_date = '0000-00-0000:00:00',daily_feed.`creation_date`,daily_feed.publish_date) AS display_date,
	daily_feed.creation_date AS 'date', daily_feed.body, '' AS subsection_ids, '' AS is_audio 
	FROM `daily_feed`, `contributors`, content_syndication AS cs 
	 WHERE daily_feed.contrib_id = contributors.id AND daily_feed.`is_approved`='1'
	  AND daily_feed.is_live='1'
	AND cs.item_id=daily_feed.id  AND daily_feed.is_yahoofeed='1' AND cs.syndication_channel='yahoo' AND cs.`is_syndicated`='1' 
	AND  daily_feed.creation_date > ('".mysqlNow()."' - INTERVAL 1 MONTH) ";  
		
		$sqlGetArticles .= " ORDER BY display_date DESC LIMIT 0,".$maxItemLimit;

		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){
			$articleFullURL = $HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$tag = getTag($article['body'],'img');
			if($tag[0]['src']!="")
			{
				$articleFeedData[$i]['articleImg'] = $tag[0]['src'];
			}
			else
			{
				$articleFeedData[$i]['articleImg'] = $IMG_SERVER."images/mv_social_icon.jpg";
			}
			
			if($yahooFullBodySyndication == "1")
			{
				$relatedArticles = $objMemcache->getSyndRelatedArticlesCache($article['id'],"");
		
				$relatedLinks = "<p><strong>Related Articles</strong><ul>";
				$relatedArticleArr[]=$article['id'];
				if(!empty($relatedArticles))
				{
					foreach($relatedArticles as $key=>$val)
					{
						$relatedArticleArr[]=$val['item_id'];
						$relatedLinks.="<li><a href='".$HTPFX.$HTHOST.$val['url']."?camp=syndication&medium=hostedportals&from=yahoo'>".$val['title']."</a></li>";
					}
					$relatedLinks.="</ul></p>";
		
				}
				else
				{
					$relatedKeyArticles =  $objMemcache->getKeyRelatedMatchCache($article['id'],$article['keyword']);
					foreach($relatedKeyArticles as $k=>$v)
					{
						$relatedArticleArr[]=$v['item_id'];
						$relatedLinks.="<li><a href='".$HTPFX.$HTHOST.$v['url']."?camp=syndication&medium=hostedportals&from=yahoo'>".$v['title']."</a></li>";
					}
					$relatedLinks.="</ul></p>";
				}
			}

			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$articleFullURL;
			$articleFeedData[$i]['character_text']=$article['character_text '];
			$articleFeedData[$i]['guid']=$article['id'];
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleDate=$article['date'];
			}else{
				$articleDate=$article['pubDate'];
			}
			$articleFeedData[$i]['pubDate'] = $articleDate;
			$articleFeedData[$i]['language']='en';
			if($article['is_audio']=='1'){
				$audio = $objArticle->getArticleAudio($article['id']);
				foreach($audio as $aud){
					if($aud['item_key']=='audiofile'){
						$audioFile = $aud['item_value'];
					}elseif($aud['item_key']=='radiofile'){
						$radioFile = $aud['item_value'];
					}
				}
			}else{
				$audioFile="";
				$radioFile="";
			}
			$articleBody=$article['body'];
			$articleBody = mswordReplaceSpecialChars($articleBody);
			$articleBody = replaceArticleAds($articleBody);
			$regex = '/<object(.*?)<\/object>/i';
			$articleBody = preg_replace($regex, '', $articleBody);
			$articleBody = str_replace('<strong','<b',$articleBody);
			$articleBody = str_replace('</strong>','</b>',$articleBody);
			$articleBody = str_replace('<em','<i',$articleBody);
			$articleBody = str_replace('</em>','</i>',$articleBody);
			if(substr_count($articleBody,"{FLIKE}") > 0){
				$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
			}
			if(substr_count($articleBody,"{AUDIO}") > 0)
			{
				if($audioFile!='')
				{
					$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{AUDIO}"," ", $articleBody);
				}
			}
			if(substr_count($articleBody,"{RADIO}") > 0)
			{
				if($radioFile!='')
				{
					$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{RADIO}"," ", $articleBody);
				}
			}
						
			if($yahooFullBodySyndication == "1")
			{
				preg_match_all('/\<a[^>]+>/i', $articleBody, $matches);
				$urlArr = array_map('removeParameters',$matches[0]);
				$linkCount = count(array_unique($urlArr));
				if($linkCount<4)
				{
					$articleBody = $objMemcache->getOutboundLinksCache($articleBody,$linkCount,$recentArticlerow['keyword'],$tagarray,$article['id']);
				}
				if(!empty($relatedArticles) || !empty($relatedKeyArticles))
				{
					$articleBody .= $relatedLinks;
				}
			}		  

			$articleFeedData[$i]['desc']=$articleBody;
			$articleFeedData[$i]['content']=$article['character_text'];
			
			if($article['type']=="1")
			{
				$gettag		=	$objArticle->getTagsOnArticles($article['id'],'1');
				/*$gettag=$tagobj->get_tags_on_objects($recentArticlerow['id'],'1');*/
				$tagarray=array();
				 foreach($gettag as $tagvalue)
				{
				    $validatetag=is_stock($tagvalue['tag']);
					if($validatetag['exchange']){ // if entry in ex_stock table
				 	$tagarray[]=$tagvalue['tag'];
					}
					else // Verify from Yahoo
					{
						$validateticker=getstockdetailsfromYahoo($tagvalue['tag']); /*varify ticker from yahoo*/
						if($validateticker[0])
						{
							 $insertTickerid=settStockTickerforYahoo($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
							 $tagarray[]=$tagvalue['tag'];
						}
					}
				 }
				 $unique_stocks=array_unique($tagarray);
			}
			else
			{
				global $D_R;
				include_once("$D_R/lib/_content_data_lib.php");
				$objDailyfeed	=	new Dailyfeed(18,$id);
				$unique_stocks		=	$objDailyfeed->getTickersExchange($id,'18');
			}
			 
			foreach($unique_stocks as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	}

	function getArticleNasdaqFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST;
		$objArticle	= new ArticleData();
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer, articles.publish_date pubDate, articles.date, articles.character_text, articles.body, articles.subsection_ids, articles.is_audio";
		$sqlGetArticles .= " from articles, `contributors`, content_syndication AS cs ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' AND cs.item_id=articles.id AND cs.is_nasdaqFeed='1'";
		if($this->authorId){
			$sqlGetArticles .= " and articles.contrib_id='".$this->authorId."'";
		}
		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}

		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetArticles .= " and B.date>'".mysqlNow()."' - interval 2 month having articles.publish_date > '".mysqlNow()."' - interval ".$val." minute ";
					break;
				}
			}
		}
		else
		{
			$sqlGetArticles .= " and articles.date>('".mysqlNow()."' - interval 1 month)";	
		}

		$sqlGetArticles .= " ORDER BY articles.publish_date DESC,articles.date DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){
			$articleFullURL = $HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			$articleFeedData[$i]['link']=$articleFullURL;
			$articleFeedData[$i]['guid']=$article['id'];
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleDate=$article['date'];
			}else{
				$articleDate=$article['pubDate'];
			}
			$articleFeedData[$i]['pubDate'] = $articleDate;
			$articleFeedData[$i]['language']='en';
			if($article['is_audio']=='1'){
				$audio = $objArticle->getArticleAudio($article['id']);
				foreach($audio as $aud){
					if($aud['item_key']=='audiofile'){
						$audioFile = $aud['item_value'];
					}elseif($aud['item_key']=='radiofile'){
						$radioFile = $aud['item_value'];
					}
				}
			}else{
				$audioFile="";
				$radioFile="";
			}
			$articleBody = mswordReplaceSpecialChars($article['body']);
			$articleBody = replaceArticleAds($articleBody);
			$articleBody = str_replace('<b ','<strong ',$articleBody);
			$articleBody = str_replace('<b>','<strong>',$articleBody);
			$articleBody = str_replace('</b>','</strong>',$articleBody);
			$articleBody = str_replace('<i>','<em>',$articleBody);
			$articleBody = str_replace('<i ','<em ',$articleBody);
			$articleBody = str_replace('</i>','</em>',$articleBody);
			if(substr_count($articleBody,"{FLIKE}") > 0){
				$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
			}
			if(substr_count($articleBody,"{AUDIO}") > 0)
			{
				if($audioFile!='')
				{
					$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{AUDIO}"," ", $articleBody);
				}
			}
			if(substr_count($articleBody,"{RADIO}") > 0)
			{
				if($radioFile!='')
				{
					$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{RADIO}"," ", $articleBody);
				}
			}
			$articleFeedData[$i]['desc']=$articleBody;
			$articleFeedData[$i]['content']=$article['character_text'];
			$stocksArr=make_stock_array($article['body'],'',$articleDate);
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	}

function getArticleGoogleCurrentsFeedData($maxItemLimit,$currentFilter=NULL,$filters=NULL){
		global $HTPFX,$HTHOST;
		$objArticle	= new ArticleData();
		$objContrib= new contributor();
		$contribFilterId=$objContrib->getExcludedPartnerId();
		$sqlGetArticles = "select articles.id id, articles.title, contributors.name author, contributors.disclaimer, articles.publish_date pubDate, articles.date, articles.character_text, `contributors`.email as author_email,articles.body, articles.subsection_ids, articles.is_audio";
		$sqlGetArticles .= " from articles, `contributors`, content_syndication AS cs ";
		$sqlGetArticles .= " where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' AND cs.item_id=articles.id AND cs.syndication_channel='aafb_syndicate'
  AND cs.is_syndicated='1' ";
		if($this->authorId){
			$sqlGetArticles .= " and articles.contrib_id='".$this->authorId."'";
		}
		if($currentFilter){
			$sqlGetArticles .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId)) ";
		}

		if(!empty($filters)){
			foreach($filters as $key=>$val){
				switch($key){
					case 'durationFilter':
						$sqlGetArticles .= " and B.date>'".mysqlNow()."' - interval 2 month having articles.publish_date > '".mysqlNow()."' - interval ".$val." minute ";
					break;
				}
			}
		}

		$sqlGetArticles .= " ORDER BY articles.publish_date DESC,articles.date DESC LIMIT 0,".$maxItemLimit;
		$resGetArticles=exec_query($sqlGetArticles);
		$i=0;
		foreach($resGetArticles as $article){
			$articleFullURL = $HTPFX.$HTHOST.makeArticleslink($article['id']);
			$articleFeedData[$i]['title']=mswordReplaceSpecialChars($article['title']);
			$articleFeedData[$i]['author']=$article['author'];
			if(empty($article['author_email']))
			{
				$author_email = 'editor@minyanville.com';
			}
			else
			{
				$author_email = $article['author_email'];
			}
			$articleFeedData[$i]['author_email']=$author_email;
			$articleFeedData[$i]['link']=$articleFullURL;
			$articleFeedData[$i]['guid']=$article['id'];
			if($article['pubDate']=="" || $article['pubDate']=="0000-00-00 00:00:00"){
				$articleDate=$article['date'];
			}else{
				$articleDate=$article['pubDate'];
			}
			$articleFeedData[$i]['pubDate'] = $articleDate;
			$articleFeedData[$i]['language']='en';
			if($article['is_audio']=='1'){
				$audio = $objArticle->getArticleAudio($article['id']);
				foreach($audio as $aud){
					if($aud['item_key']=='audiofile'){
						$audioFile = $aud['item_value'];
					}elseif($aud['item_key']=='radiofile'){
						$radioFile = $aud['item_value'];
					}
				}
			}else{
				$audioFile="";
				$radioFile="";
			}
			$articleBody = mswordReplaceSpecialChars($article['body']);
			$articleBody = replaceArticleAds($articleBody);
			$articleBody = "<p>".$articleBody."</p>";
			$pPattern = '#(?:<br\s*/?>\s*?){2,}#';
			$articleBody = preg_replace($pPattern, "</p> <p>", $articleBody);
			$emptyPPattern  = "/<p[^>]*>[\s|&nbsp;]*<\/p>/";
			$articleBody = preg_replace($emptyPPattern, " ", $articleBody);
			$articleBody = str_replace('<b ','<strong ',$articleBody);
			$articleBody = str_replace('<b>','<strong>',$articleBody);
			$articleBody = str_replace('</b>','</strong>',$articleBody);
			$articleBody = str_replace('<i>','<em>',$articleBody);
			$articleBody = str_replace('<i ','<em ',$articleBody);
			$articleBody = str_replace('</i>','</em>',$articleBody);
			if(substr_count($articleBody,"{FLIKE}") > 0){
				$articleBody = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $articleBody);
			}
			if(substr_count($articleBody,"{AUDIO}") > 0)
			{
				if($audioFile!='')
				{
					$articleBody = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{AUDIO}"," ", $articleBody);
				}
			}
			if(substr_count($articleBody,"{RADIO}") > 0)
			{
				if($radioFile!='')
				{
					$articleBody = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $articleBody);
				}
				else
				{
					$articleBody = str_replace("{RADIO}"," ", $articleBody);
				}
			}
			$articleFeedData[$i]['desc']=$articleBody;
			$articleFeedData[$i]['content']=$article['character_text'];
			$stocksArr=make_stock_array($article['body'],'',$articleDate);
			foreach($stocksArr as $id=>$value)
			{
				$articleFeedData[$i]['ticker'][]=$value;
			}
			$sqlGetCategory="SELECT name FROM section WHERE section_id IN (".$article['subsection_ids'].") ";
			$resGetCategory=exec_query($sqlGetCategory);
			foreach($resGetCategory as $id=>$value)
			{
				$articleFeedData[$i]['category'][]=$value['name'];
			}
			$i++;
		}
		return $articleFeedData;
	}
}
function changeImageStyle($matches){
	$matches=preg_replace_callback('/style=("[^"]*")/i',"changeWidthHeight", $matches);
	return $matches[0];
}
function changeWidthHeight($matches){
	$heightWidth=explode(';',str_replace('"','',$matches[1]));
	foreach($heightWidth as $attribute){
		if(stristr(strtolower ($attribute),'width')){
			$width=explode(':',$attribute);
		}elseif(stristr(strtolower ($attribute),'height')){
			$height=explode(':',$attribute);
		}
	}
	$newHeightWidth="width='".trim($width[1])."' height='".trim($height[1])."'";
	return $newHeightWidth;
}

?>