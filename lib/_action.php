<?php
class Action{
	function trigger($event,$data=NULL){
		$this->data=$data;
		switch($event){
			case 'articlePublish':
				$this->articlePublish();
				break;
			case 'articleUpdate':
				$this->articleUpdate();
				break;
			case 'articleDelete':
				$this->articleDelete();
				break;
			case 'setYahooCache':
				$this->setYahooCache();
				break;
			case 'buzzDataPublish':
				$this->buzzDataPublish($event);
				break;
			case 'buzzDataUpdate':
				$this->buzzDataUpdate();
				break;
			case 'buzzDataDelete':
				$this->buzzDataDelete();
				break;
			case 'dailyFeedDataUpdate':
				$this->dailyFeedDataUpdate();
				break;
			case 'dailyFeedDataDelete':
				$this->dailyFeedDataDelete();
				break;
			case 'CategoryMostReadUpdate':
				$this->CategoryMostReadUpdate();
				break;
			case 'CategoryMostReadDelete':
				$this->CategoryMostReadDelete();
				break;
			case 'buzzDataDelete':
				//$this->articleDelete();
				break;
			case 'dailyFeedListUpdate':
				$this->dailyFeedListUpdate();
				break;
			case 'orderPurchase':
				$this->orderPurchase($data);
		}
	}
	function articlePublish(){
		global $articleListingHomePageSize,$articleListingPageSize,$D_R;
		include_once $D_R.'/lib/config/_article_config.php';
		include_once("$D_R/admin/lib/_article_data_lib.php");
		include_once("$D_R/lib/MemCache.php");
		
		$objArticleData = new ArticleData();
		$objCache= new Cache();
		$obArtilceCache=new ArticleCache();
		$obArtilceCache->setLatestArticles();
		$objCache->setArticleApproveCache($this->data);
		$objCache->setPageMetaDataCache('1',$this->data);
		$objCache->setMostRecentArticles();
		$objCache->setArticleContributors();
		$objCache->deleteFeedForPartners();			/*All Feeds*/
		$objCache->deleteFeedPartnerData();			/*td_tradeArchitect.rss*/
		$this->flushArticleLayoutModule();
		$this->topicCacheUpdate();
		$yahooFullBody = $objArticleData->getYahooFullBodySynd($this->data);
		if($yahooFullBody=="1")
		{
			$sql="select * from articles where id='".$this->data."'";
			$result = exec_query($sql);
			$this->setYahooCache($result);
		}
		
	}
	function articleUpdate(){
		global $articleListingHomePageSize,$articleListingPageSize,$D_R;
		include_once $D_R.'/lib/config/_article_config.php';
		include_once("$D_R/admin/lib/_article_data_lib.php");
		
		$objArticleData = new ArticleData();
		$objCache= new Cache();
		$objArtilceCache = new ArticleCache();
		$memObj = new memCacheObj();
		$data = $objCache->setArticleCache($this->data);
		$objCache->setPageMetaDataCache('1',$this->data);
		$objCache->setMostRecentArticles();
		$objArtilceCache->setLatestArticles();
		$objCache->setArticleContributors();
		$objArtilceCache->setLatestPremiumContent();
		$objCache->setTickerListCache($this->data,'1',$data->stocks,$data->title);
		$objCache->setMostRead('1,18',$limit=5);
		$this->flushArticleLayoutModule();
		$this->topicCacheUpdate();
		$objCache->deleteFeedForPartners();			/*All Feeds*/
		$objCache->deleteFeedPartnerData();			/*td_tradeArchitect.rss*/
		$objCache->deleteArticleContentCache($this->data);
		$yahooFullBody = $objArticleData->getYahooFullBodySynd($this->data);
		if($yahooFullBody=="1")
		{
			$sql="select * from articles where id='".$this->data."'";
			$result = exec_query($sql);
			$this->setYahooCache($result);
		}
	}
	
	function setYahooCache($data)
	{
		global $HTPFX,$HTHOST;
	
		$objCache= new Cache();
		$objCache->setKeyRelatedMatchCache($data['id'],$data['keyword']);		
		$objCache->setSyndRelatedArticlesCache($data['id'], '');		
		setYahooOutboundDataCache($data['id']);	
	}
	
	function CategoryMostReadUpdate()
	{
		$objCache= new Cache();
		$objCache->setCategoryMostReadCache();
	}
	function dailyFeedListUpdate()
	{
		$objCache= new Cache();
		$obDFCache=new DailyFeedCache();
		$objCache->setDailyFeedListCache();
		$objCache->setDailyFeedCountCache();
        $objCache->setMostRead('18',$limit=3);
		$obDFCache->setLatestDailyfeed();
		$this->flushDailyFeedLayoutModule();
	}
	function dailyFeedDataUpdate()
	{
		$objCache= new Cache();
		$obDFCache=new DailyFeedCache();
		$data = $objCache->setDailyfeedCache($this->data);
		$objCache->setImageDailyFeedCache($this->data);
		$objCache->setQuickTitleDailyFeedCache($this->data,$item_type='18');
		$objCache->setPageMetaDataCache('18',$this->data);
		$tickerArr = $objCache->setTickersCache($this->data,'18');
		$objCache->setTickerListCache($this->data,'18',$tickerArr,$data->title);
		$obDFCache->setLatestDailyfeed();
		$this->flushDailyFeedLayoutModule();
	}
	function dailyFeedDataDelete()
	{
		$objCache= new Cache();
		$obDFCache=new DailyFeedCache();
		$objCache->setDailyFeedListCache();
		$objCache->setDailyFeedCountCache();
		$objCache->deleteDailyFeedUrlCache($this->data);
		$obDFCache->setLatestDailyfeed();
		$this->flushDailyFeedLayoutModule();
	}
	function articleDelete(){
		global $articleListingHomePageSize,$articleListingPageSize;
		$objCache= new Cache();
		$obArtilceCache=new ArticleCache();
		$obArtilceCache->setLatestArticles();
		$objCache->setArticleContributors();
		$this->topicCacheUpdate();
		$objCache->deleteArticleCache($this->data);
		$objCache->setMostRecentArticles();
		$this->flushArticleLayoutModule();
	}

	function orderPurchase($prodData){
		switch($prodData['oc_id']){
			case  '26':
				$this->HRMOrderPurchase($prodData);
				break;
		}
	}

	function HRMOrderPurchase($prodData){
		global $D_R,$housingmarket_from,$_SESSION,$SYSADMIN_EMAIL;
		include_once $D_R.'/lib/housingreport/_housingreport_data.php';
		include_once $D_R.'/lib/config/_housingmarket_config.php';
		$objHD= new HousingData();
		$issueDetails=$objHD->getIssueDetailsBySubDefID($prodData['subscription_def_id']);
		$result = $objHD->sendHousingMarketReportEmail($housingmarket_from, $issueDetails,$prodData['subscription_def_id'], $_SESSION['email']);
		if(!$result){
			mymail($SYSADMIN_EMAIL, $housingmarket_from, "Error Sending HMR Report to ".$_SESSION['email'], "There was an error sending HMR report for  ".$prodData['title']." to this user please send him the report ASAP.");
		}
	}
	function flushDailyFeedLayoutModule()
	{
		$stQuery = "SELECT module_id FROM layout_module WHERE template_id IN('13','18')";
		$arModulesResult = exec_query($stQuery);
		$objCache= new Cache();
		foreach($arModulesResult as $arModule)
		{
			$module_id = $arModule['module_id'];
			$objCache->setPageModuleCache($module_id);
		}
	}
	function flushArticleLayoutModule()
	{
		$stQuery = "SELECT module_id FROM layout_module WHERE template_id IN('15','14')";
		$arModulesResult = exec_query($stQuery);
		$objCache= new Cache();
		foreach($arModulesResult as $arModule)
		{
			$module_id = $arModule['module_id'];
			$objCache->deletePageModuleCache($module_id);
		}
	}
	function topicCacheUpdate(){
		$obArticleCache = new ArticleCache();
		if(is_array($this->data))
		{
			$arArticleIds = $this->data;
		}
		else
		{
			$arArticleIds[0] = $this->data;
				}
		foreach($arArticleIds as $article_id)
		{
			$sql="select subsection_ids from articles where id='".$article_id."'";
			$arSections=exec_query($sql,1);
            if(is_array($arSections) && $arSections['subsection_ids'] != ",")
			{
				$arSectionId=explode(",",$arSections['subsection_ids']);
				foreach($arSectionId as $sec_id){
					$obArticleCache->setLatestSectionArticles($sec_id);
			}
			}
		}
	}


	function buzzDataPublish($event){

		$this->googleAppContentApi($event);

	}

	function buzzDataUpdate(){
		$this->googleAppContentApi($event);
	}

	function buzzDataDelete(){
		$this->googleContentApiDelete();
	}

	function googleAppContentApi($event){
			 $getBuzzSql="SELECT id, position,body,is_live,image,date,author,section_id,show_on_web,approved,title,show_in_app,
updated,login,contrib_id,publish_date,branded_img_id FROM `buzzbanter` WHERE id='".$this->data."'";

			$getBuzzResult=exec_query($getBuzzSql,1);
			$id=$getBuzzResult['id'];
			$position=urlencode($getBuzzResult['position']);
			$bodyBuzz=$this->displayContentApiBrandedLogo($getBuzzResult['branded_img_id']).$getBuzzResult['body'].$this->displayContentApiChartBuzzImages($id);

			$body=urlencode($bodyBuzz);

			$is_live=urlencode($getBuzzResult['is_live']);
			$image=$getBuzzResult['image'];
			$isDeleted=0;
			$date=$getBuzzResult['date'];
			$author=urlencode($getBuzzResult['author']);
			$section_id=$getBuzzResult['section_id'];
			$show_on_web=$getBuzzResult['show_on_web'];
			$approved=$getBuzzResult['approved'];
			$title=urlencode($getBuzzResult['title']);
			$show_in_app=$getBuzzResult['show_in_app'];
			$updated=$getBuzzResult['updated'];
			$login=urlencode($getBuzzResult['login']);
			$contrib_id=$getBuzzResult['contrib_id'];
			if(empty($getBuzzResult['publish_date']) || $getBuzzResult['publish_date']=="0000-00-00 00:00:00"){
				$publish_date=$getBuzzResult['date'];
			}else{
				$publish_date=$getBuzzResult['publish_date'];
			}

		   $this->setStatusBuzzContentAPI($id,$publish_date,$isDeleted);
		   $url="http://buzz.minyanville.com/saveBuzzPost.action";
		    $postData = 'id='.$id.'&position='.$position.'&body='.$body.'&isLive='.$is_live.'&image='.$image.'&isDeleted='.$isDeleted.'&date='.$date.'&author='.$author.'&sectionId='.$section_id.'&showOnWeb='.$show_on_web.'&approved='.$approved.'&title='.$title.'&showInApp='.$show_in_app.'&updated='.$updated.'&login='.$login.'&contribId='.$contrib_id.'&publishDate='.$publish_date.'&priority='.$priority;
		try{
		
			$optsApi = array( 
				'http'=>array( 
				'method'=>'POST', 
				'content'=> $postData
				) 
			);
			$contextApi = stream_context_create($optsApi); 
			$ret= file_get_contents($url,false,$contextApi);
			$output = json_decode($ret);
			$buzzStatus=$output->buzzStatus;
			$this->setBuzzContentAPI($buzzStatus,$id,$publish_date,$isDeleted);
			
		}catch(Exception $e)
		{

		}
	}


	function googleContentApiDelete(){
		 $url="http://buzz.minyanville.com/deleteBuzz.action";
		 $id=$this->data;
		 $postData = 'id='.$id;
		 $isDeleted=1;
		try{

			$optsApi = array( 
				'http'=>array( 
				'method'=>'POST', 
				'content'=> $postData
				) 
			);
			$contextApi = stream_context_create($optsApi); 
			$ret= file_get_contents($url,false,$contextApi);
			$output = json_decode($ret);
			$buzzStatus=$output->buzzDeleted;
			$this->deleteBuzzContentApi($buzzStatus,$id,$isDeleted);
		}catch(Exception $e)
		{

		}

	}

	function setBuzzContentAPI($buzzStatus,$id,$publish_date,$isDeleted){
		global $D_R;
		If($buzzStatus=="Success"){
			$params['buzzid']=$id;
			$params['buzz_date']=$publish_date;
			$params['syndicate_date']=mysqlNow();
			$params['sent']=2;
			$params['is_deleted']=$isDeleted;
			$condition['buzzid']=$id;
			$id=insert_or_update('buzz_content_api',$params,$condition);
		}else{
			$params['buzzid']=$id;
			$params['buzz_date']=$publish_date;
			$params['syndicate_date']=mysqlNow();
			$params['sent']=0;
			$params['is_deleted']=$isDeleted;
			$condition['buzzid']=$id;
			$id=insert_or_update('buzz_content_api',$params,$condition);
		}
	}

	function setStatusBuzzContentAPI($id,$publish_date,$isDeleted){
			$params['buzzid']=$id;
			$params['buzz_date']=$publish_date;
			$params['syndicate_date']=mysqlNow();
			$params['sent']=1;
			$params['is_deleted']=$isDeleted;
			$condition['buzzid']=$id;
			$id=insert_or_update('buzz_content_api',$params,$condition);
	}

	function deleteBuzzContentApi($buzzStatus,$id,$isDeleted){
		If($buzzStatus=="true"){
			$params['buzzid']=$id;
			$params['syndicate_date']=mysqlNow();
			$params['sent']=1;
			$params['is_deleted']=$isDeleted;
			$condition['buzzid']=$id;
			$id=insert_or_update('buzz_content_api',$params,$condition);
		}

	}


	function displayContentApiBrandedLogo($brandedlogoid){
		if(!empty($brandedlogoid)){
		$qry="select assets1,url from buzz_branded_images where id='".$brandedlogoid."'";
		$result=exec_query($qry,1);
		$str="";
		if($result){
			$str='<div><a href="'.$result['url'].'" ><img border="none" src="'.$result['assets1'].'" /></a></div>'.'<br>';
		}
		return $str;
		}
	}


	function displayContentApiChartBuzzImages($buzzid){
		$qry="select id,original_url,thumb_url from item_charts where item_id='".$buzzid."' and item_type='2'";
		$result=exec_query($qry);
		$str="";
		if($result){
		 $str.='<div>';
		  foreach($result as $row){
			  $str.='<br><a href="'.$row['original_url'].'" ><img src="'.$row['thumb_url'].'" /></a>'.'<br /><br />';
			  $str.='<a href="'.$row['original_url'].'" >Click here to enlarge</a>'.'<br>';
		  }
		  $str.='</div>';
		}
		return $str;
	}

}
?>