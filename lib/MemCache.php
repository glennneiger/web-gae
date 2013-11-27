<?php
class memCacheObj{
	var $cacheObj;
	function memCacheObj()
	{
		global $MEMCACHE_SERVERS;
		$this->cacheObj = new Memcache();
		foreach($MEMCACHE_SERVERS as $server){
			$this->cacheObj->addServer ( $server,'11111',1);
		}
	}
	function setKey($key,$value,$duration)
	{
		if(is_string($key))
		{
			$this->cacheObj->set($key, $value,0, $duration);// was set($key, $value, $duration)
		}
	}
	function getKey($key)
	{
		return $this->cacheObj->get($key);
	}

	function deleteKey($key)
	{
		$this->cacheObj->delete($key);
	}

	/*replace() should be used to replace value of existing item with key . In case if item with such key doesn't exists, Memcache::replace() returns FALSE */
	/*3rd parameter - Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib).*/

	function updateKey($key,$value,$duration){
		return $this->replace($key,$value,false,$duration);
	}

	function getExtendedCacheStats(){
		return $this->cacheObj->getExtendedStats();
	}
}

class Cache extends memCacheObj {

	public function setArticleCache($id){
	 global $articleCacheExpiry,$D_R;
	 include_once $D_R.'/admin/lib/_article_data_lib.php';
	 include_once("$D_R/lib/_news.php");
	$objArticle = new ArticleData();
	 $article=$this->getArticleData($id);
	 if($article){
	 	$objThread = new Thread();
	 	$tags = $objThread->get_tags_on_objects($id,'1');
	 	$secaid=getArticleSubsectionid($id);
	 	$pageaDetail['id']=$secaid['page_id'];
	 	$pageDetail['id']=$pageaDetail['id'];
	 	if($pageDetail['id']=="0"){
	 		$pageDetail['id']="56";
	 	}
	 	$secaid=$pageDetail['id'];
	 	$revisionbody=get_full_article_body($id);
	 	$countpage=count($revisionbody);
	 	include_once $D_R.'/lib/videos/_data_lib.php';
	 	$obPlayer = new player();
	 	if($article['editor_note'] != '')
	 	{
	 		$article['editor_note']=str_replace('INSERT NAME',$article['author'],$article['editor_note']);
	 		$revisionbody[0]['body'] = "<i>".stripslashes($article['editor_note'])."</i><br /><br />".$revisionbody[0]['body'];
	 	}
	 	$this->date=date('M d, Y g:i a',strtotime(($article['publish_date']=="0000-00-00 00:00:00" || $article['publish_date']=="")?$article['date']:$article['publish_date']));
	 	for($i=0; $i<$countpage; $i++){
	 		$revisionbody[$i]['body']=make_stock_links($revisionbody[$i]['body'],$this->date);
	 		$revisionbody[$i]['body']=$this->buzzAdReplace($revisionbody[$i]['body']);
	 		$revisionbody[$i]['body']=$obPlayer->replaceVideoCode($revisionbody[$i]['body']);
	 		$arBody[$i] = $revisionbody[$i]['body'];
	 	}
	 	$this->id=$article['id'];
	 	$this->title=$article['title'];
	 	$this->author=$article['author'];
	 	$this->authorid=$article['authorid'];
	 	$this->contribIntro=$article['intro'];
	 	$this->contribPostBody=$article['postcopy'];
	 	$this->contribTwitter=$article['twitter_username'];
	 	$this->fullbody=implode("",$arBody);
	 	$this->body=$revisionbody;
	 	$this->tags=$tags;
	 	$this->keyword=$article['keyword'];
	 	$this->g_plus_link = $article['g_plus_link'];
	 	$this->bulrb=$article['blurb'];
	 	$this->stocks=make_stock_array($article['body'],'',$this->date);
	 	$this->featureimage=$article['featureimage'];
	 	$this->layout=$article['layout_type'];
	 	if($article['is_audio']=="1")
	 	{
		 	$audio = $objArticle->getArticleAudio($this->id);
	 		foreach($audio as $aud){
				if($aud['item_key']=='audiofile'){
					$this->audioFile = $aud['item_value'];
				}elseif($aud['item_key']=='radiofile'){
					$this->radioFile = $aud['item_value'];
				}
	 		}
	 	}
	 	else
	 	{
	 		$this->audioFile="";
	 		$this->radioFile="";
	 	}
	 	$this->approve=$article['approved'];
	 	$this->character_text=$article['character_text'];
	 	$this->position=$article['position'];
	 	$this->disclaimer=$article['disclaimer'];
	 	$this->is_live=$article['is_live'];
	 	$this->ic_tag = $this->getCacheIcTag($this->id);
		$this->no_follow_tag=$article['no_follow_tag'];
	 	$this->showBioLink=$article['has_bio'];
	 	$this->parentid=getSectionid($secaid);
	 	$this->securl=getSubsectionUrl($secaid);
	 	$this->item_type_id=$item_type_id;
	 	$showRelArticle = showRelatedArticle($id);
	 	$showAlsoBy = showAlsoBy($id,$article['author'],$article['authorid']);
	 	$this->showRelArticle=$showRelArticle;
	 	$this->showAlso=$showAlsoBy;
	 	$this->syndicationChannels=$objArticle->getGoogleNewsSynd($this->id);
		$articleSectionDetail=getArticleSubsectionid($id);
		$parentSection=$articleSectionDetail['parent_section'];
		$getChckMateData=$this->getParentArticleData($parentSection);
		$this->pageName=$articleSectionDetail['name'];
		if($articleSectionDetail['topic_page_id']){
		$this->pageId=$articleSectionDetail['topic_page_id'];
		$this->checkM8ArticlepageId=$articleSectionDetail['topic_page_id'];
		}else{
			$this->pageId=$articleSectionDetail['page_id'];
			$this->checkM8ArticlepageId=$articleSectionDetail['page_id'];
		}
		$this->articlePagename=$articleSectionDetail['article_pagename'];
		$this->checkM8ArticlepageName=$articleSectionDetail['name'];
		$this->checkM8articlePagename=$articleSectionDetail['article_pagename'];
		$this->stop_autorefresh=$article['stop_autorefresh'];
	 	if($this->is_live && $this->approve){
	 		$this->setKey("article_".$id,$this,$articleCacheExpiry);
	 	}
	 	return $this;
	 }
	}

	function getProductArray()
	{
		global $productArrCacheExpiry;
		$productArr = $this->getKey("productArr_");
		if(!empty($productArr))
		{
			return $productArr;
		}
		else
		{
			return $this->setProductArray();
		}
	}

	function setProductArray()
	{
		global $productArrCacheExpiry;
		$getSql="SELECT `product`,`recurly_plan_code`,`subscription_def_id`,`subGroup`,`subType` FROM `product` WHERE subGroup<>'' AND subGroup IS NOT NULL AND recurly_plan_code NOT IN ('buzz_60days_freenoar_nft_via_10042013') GROUP BY `product`,recurly_plan_code";
		$getResult=exec_query($getSql);
		if(!empty($getResult)){
			foreach($getResult as $key=>$val)
			{
				$productArr[$val['subGroup']]['plan_code'][]=$val['recurly_plan_code'];
				$productArr[$val['subGroup']]['typeSpecificId'][]=$val['subscription_def_id'];
			}
			$this->setKey("productArr_",$productArr,$productArrCacheExpiry);
		}
		return $productArr;
	}

	function getParentArticleData($parentSection){
		$sqlGetSection="select page_id,name,article_pagename from section where section_id='".$parentSection."'";
		$resGetSection= exec_query($sqlGetSection,1);
		if($resGetSection){
			return $resGetSection;
		}else{
			return false;
		}

	}

	function buzzAdReplace($body){
		global $articleAd,$_SESSION,$D_R;
		include_once $D_R.'/lib/config/_article_config.php';
		foreach($articleAd as $key=>$value){
			$patterns[$key]="/{".$key."}/";
			if (!$_SESSION['AdsFree']){
				$replacements[$key]=$articleAd[$key];
			}else{
				$replacements[$key]='';
			}
		}
		$articleBody=preg_replace($patterns,$replacements,$body);
		return $articleBody;
	}

	function replaceBlankArticleAd($body){
		global $articleAd,$_SESSION;
		foreach($articleAd as $key=>$value){
			$patterns[$key]="/{".$key."}/";
		}
		$articleBody=preg_replace($patterns,$replacements,$body);
		return $articleBody;
	}


	function getArticleData($id){
		$sql = "SELECT A.id id,A.category_ids,A.no_follow_tag,A.title, A.layout_type, A.subsection_ids, C.name author,
	A.contributor, C.disclaimer,C.has_bio,C.intro,C.postcopy,C.g_plus_link,C.twitter_username,A.position, A.contrib_id authorid, A.date,A.is_audio, A.publish_date,
	A.blurb, A.body, A.position,A.approved,A.is_live,A.character_text, A.keyword ,A.stop_autorefresh,
	C.id AS authorid, A.featureimage, A.editor_note
	FROM contributors C,articles A WHERE A.id='".$id."' AND A.contrib_id = C.id LIMIT 1";
		$result=exec_query($sql,1);
		return $result;
	}

	public function getKeyRelatedMatchCache($article_id,$keywords)
	{
		$articleKeyRelatedMatch= $this->getKey("article_keymatch_".$article_id);
		if(!empty($articleKeyRelatedMatch))
		{
			return $articleKeyRelatedMatch;
		}
		else
		{
			return $this->setKeyRelatedMatchCache($article_id,$keywords);
		}
	}
	
	public function getOutboundLinksCache($articleBody,$linkCount,$keywords,$tagarray,$id)
	{
		$outboundLinks = $this->getKey("article_outbound_".$id);
		if(!empty($outboundLinks))
		{
			return $outboundLinks;
		}
		else
		{
			return $this->setOutboundLinksCache($articleBody,$linkCount,$keywords,$tagarray,$id);
		}
	}
	
	public function setOutboundLinksCache($articleBody,$linkCount,$keywords,$tagarray,$id)
	{
		global $outboundArticleCacheExpiry;
	 	$outboundArticle = addOutboundLinks($articleBody,$linkCount,$keywords,$tagarray);
		$setcacheOutboundArticle=$this->setKey("article_outbound_".$id,$outboundArticle,$outboundArticleCacheExpiry);
		return $setcacheOutboundArticle;
		
	}
	
	
	public function setKeyRelatedMatchCache($article_id,$keywords)
	{
		global $articleKeyMatchCacheExpiry;
	 	$articleKeyMatch = getKeyRelatedMatch($article_id,$keywords);
		$setcacheArticleKeyMatch=$this->setKey("article_keymatch_".$article_id,$articleKeyMatch,$articleKeyMatchCacheExpiry);
		return $setcacheArticleKeyMatch;
		
	}

	public function getSyndRelatedArticlesCache($id,$syndicationChannel)
	{
		$articleRelated = $this->getKey("article_related_".$syndicationChannel."_".$id);
		if(!empty($articleRelated))
		{
			return $articleRelated;
		}
		else
		{
			return $this->setSyndRelatedArticlesCache($id, $syndicationChannel);
		}
	}
	
	public function setSyndRelatedArticlesCache($id, $syndicationChannel)
	{
		global $relatedArticleCacheExpiry;
	 	$relatedArticle = getSyndRelatedArticles($id, $syndicationChannel);
		$setcacheRelatedArticle=$this->setKey("article_related_".$syndicationChannel."_".$id,$relatedArticle,$relatedArticleCacheExpiry);
		return $setcacheRelatedArticle;
		
	}
	
	public function getArticleCache($id){
		$article = $this->getKey("article_".$id);
		if(!empty($article))
		{
			return $article;
		}
		else
		{
			return $this->setArticleCache($id);
		}
	}
	//--------------- Dailyfeed Cache ------------------
	public function getDailyfeedCache($url){
		$dailyfeed = $this->getKey($url);
		if(!is_object($dailyfeed)){
			$dailyfeed=$this->setDailyfeedCache($url);
		}
		return $dailyfeed;
	}

	public function getMailChimpActiveList()
	{
		$mailchimp = $this->getKey("mailchimp");
		if(empty($mailchimp)){
			$mailchimp=$this->setMailChimpCache();
		}
		return $mailchimp;
	}

	public function setMailChimpCache()
	{
		global $MailChimpCacheExpiry;
	 	$qry="select listid,listname,imagename from fancybox_image where is_active='1'";
		$getResult=exec_query($qry,1);
		$setcachemailchimp=$this->setKey("mailchimp",$getResult,$MailChimpCacheExpiry);
		return $getResult;
	}

	public function getBitlyUrlCache($url,$articleid,$type_id){
		$bitlyurl = $this->getKey("bitlyurl_".$articleid."_".$type_id);
		if(empty($bitlyurl)){
			$bitlyurl=$this->setBitlyUrlCache($url,$articleid,$type_id);
		}
		return $bitlyurl;
	}

	function setBitlyUrlCache($url,$articleid,$type_id){
		global $D_R;
		include_once $D_R.'/admin/lib/_article_data_lib.php';
		$qryGetBitlyUrl='SELECT bitly_url FROM ex_item_meta WHERE item_id = "'.$articleid.'" AND item_type="'.$type_id.'" ';
		if(empty($bitlyurl))
		{
			$objArticle = new ArticleData();
			$objbitly = new bitly();
			$bitlyurl=$objbitly->shorten($url);
			$objArticle->setArticleBitlyUrl($articleid,$type_id,$bitlyurl);
			$setcachebitlyurl=$this->setKey("bitlyurl_".$articleid."_".$type_id,$bitlyurl,$bitlyCacheExpiry);
		}
		else
		{
			$setcachebitlyurl=$this->setKey("bitlyurl_".$articleid."_".$type_id,$bitlyurl,$bitlyCacheExpiry);
		}
		return $bitlyurl;
	}

	function getDailyfeedData($urlorid){

		$qryGetDailyFeed="SELECT DF.id,DF.creation_date,DF.updation_date,IF(DF.publish_date = '0000-00-0000:00:00',DF.creation_date,DF.publish_date) AS display_date,DF.title, DF.excerpt,DF.body,DF.publish_date,C.id AS 'ContId',C.g_plus_link,C.name contributor,DF.is_draft,DF.position,
		DF.is_live,DF.is_deleted,DF.is_approved,DF.layout_type,EIM.url FROM daily_feed DF,contributors C,
		ex_item_meta EIM
		WHERE DF.id=EIM.item_id AND C.id=DF.contrib_id  AND EIM.item_type='18'
		AND DF.is_deleted='0' ";
		if(is_numeric($urlorid))
		{
			$qryGetDailyFeed .=" AND DF.id='".$urlorid."'";
		}
		else
		{
			$qryGetDailyFeed .=" AND (EIM.url like '%".$urlorid."%' or url like '%".$urlorid."/%')";
		}

		$resGetDailyFeed=exec_query($qryGetDailyFeed,1);

		return $resGetDailyFeed;
	}
	public function setDailyfeedCache($urlorid){
		global $dailyfeedCacheExpiry,$D_R;
		include_once($D_R.'/lib/_exchange_lib.php');
		include_once($D_R.'/admin/lib/_dailyfeed_data_lib.php');
		$dailyfeed=$this->getDailyfeedData($urlorid);
		$objDailyfeed = new Dailyfeed();
		if($dailyfeed){
			$this->id=$dailyfeed['id'];
			$objThread = new Thread();
			$threadArray = $objThread->get_thread_on_object($this->id,'daily_feed');
			$item_type_id=$objThread->get_object_type("Feeds");
			$this->title=$dailyfeed['title'];
			$this->display_date=$dailyfeed['display_date'];
			$this->creation_date=$dailyfeed['creation_date'];
			$this->updation_date=$dailyfeed['updation_date'];
			$this->updation_date=$dailyfeed['publish_date'];
			$this->excerpt=$dailyfeed['excerpt'];
			/*replace ads in df body*/
			$this->body=$this->buzzAdReplace($dailyfeed['body']);
			// $this->body=$dailyfeed['body'];
			$this->body =make_stock_links($this->body,$this->display_date);
			$this->contributor=$dailyfeed['contributor'];
			$this->ContId=$dailyfeed['ContId'];
			$this->discussion_id=$threadArray['id'];
			$this->position=$dailyfeed['position'];
			$this->is_draft=$dailyfeed['is_draft'];
			$this->g_plus_link = $dailyfeed['g_plus_link'];
			$this->is_live=$dailyfeed['is_live'];
			$this->is_approved=$dailyfeed['is_approved'];
			$this->synd=$objDailyfeed->getFeedGoogleSynd($this->id);
			$this->layout_type=$dailyfeed['layout_type'];
			$this->url=$dailyfeed['url'];
			$this->topicsURL = $objDailyfeed->getTopicsURL('daily_feed',$dailyfeed['id']);
			$this->image = $objDailyfeed->getImageDailyFeed($dailyfeed['id']);
			$this->quick_Title= $objDailyfeed->getQuickTitleDailyFeed($dailyfeed['id'],'18');
			$this->tickers= $objDailyfeed->getTickersExchange($dailyfeed['id'],'18');
			$this->resource= $objDailyfeed->getResource($dailyfeed['id'],'18');
			if($this->is_live && $this->is_approved){
				$setcachedailyfeed=$this->setKey($this->url,$this,$dailyfeedCacheExpiry);
			}
			return $this;
		}
	}

	function deleteDailyFeed($id){
		global $D_R;
		include_once $D_R.'/admin/lib/_dailyfeed_data_lib.php';
		$objDailyfeed = new Dailyfeed();
		$url=$objDailyfeed->getDailyFeedUrl($id);
		$this->deleteKey($url);
	}
	function getArticleDiscussionData($threadid,$articleapprove){

		$sql="select ps.teasure teasure, pt.body body, sub.fname fname,sub.lname lname,concat(sub.fname,' ',sub.lname) name,sub.email email, ps.created_on date, ps.title title,ps.pcomment_id pcomment_id, ps.id postid, sub.id subid,ps.approved,ps.suspended from ex_thread th, ex_post ps,ex_post_content pt, subscription sub where th.id = ps.thread_id and ps.poster_id =sub.id and ps.id=pt.post_id and th.id = '".$threadid."' AND ps.suspended='0' order by date desc";
		$result=exec_query($sql);
		$comments=array_reverse($result);
		return $comments;
	}

	public function getArticleDiscussionCache($threadid){
		$comment = $this->getKey("threadid_".$threadid);
		if(!is_object($comment)){
			$comment=$this->setArticleDiscussionCache($threadid,$articleapprove=1);
		}
		return $comment;
	}

	function setArticleDiscussionCache($threadid,$articleapprove=1){
		global $articleCacheExpiry;
		$objThread = new Thread();
		$this->appcommentcount = $objThread->count_all_comments($threadid);
		$this->threadid=$threadid;
		$comments=$this->getArticleDiscussionData($threadid,$articleapprove);
		$this->comments=$comments;
		$setcachecomment=$this->setKey("threadid_".$threadid,$this,$articleCacheExpiry);
		return $this;
	}
	
	function getArticlePageCount($articleId){
		$qry="SELECT DISTINCT(`page_no`) as pageCount FROM `article_revision` WHERE article_id='".$articleId."'";
		$res=exec_query($qry);
		if(!empty($res)){
			return $res;
		}else{
			return false;
		}
	}

	public function deleteArticleCache($articleids){
		if(is_array($articleids)){
			foreach($articleids as $value){
				$this->setKey("article_".$value,"","0");
				$this->deleteArticleContentCache($articleids);
			}
		}else{
			$this->setKey("article_".$articleids,"","0");
			$this->deleteArticleContentCache($articleids);
		}
	}

	public function deleteArticleContentCache($articleId){
		$pageId = $this->getArticlePageCount($articleId);
		foreach($pageId as $k=>$page){
			$this->setKey("articleContent_".$articleId."_1_".$page['pageCount'],"","0");
		}
		if(count($pageId)>1){
			$this->setKey("articleContent_".$articleId."_1_full","","0");
		}
	}
	
	public function setArticleApproveCache($articleids){
		if(is_array($articleids)){
			foreach($articleids as $value){
				$this->setArticleCache($value);
			}
		}else{
			$this->setArticleCache($articleids);
		}
	}

	public function deleteDiscussionArticleCache($discussionid){
		$this->cacheObj->delete("threadid_".$discussionid);
	}

	public function setFooterQuickLinksCache(){
	 global $footerCacheExpiry,$D_R;
	 include_once("$D_R/lib/_layout_data_lib.php");
	 $arFooterLinks = getfootermenu('active','');
	 $this->arFooterLinks=$arFooterLinks;
	 $this->setKey("mv_nav_footer",$this,$footerCacheExpiry);
	 return $this;
	}

	public function getFooterQuickLinksCache(){
		$getFooterCache=$this->getKey("mv_nav_footer");
		if(!is_object($getFooterCache)){
			$getFooterCache=$this->setFooterQuickLinksCache();
		}
		return $getFooterCache;
	}

	public function deleteFooterQuickLinksCache(){
		$this->cacheObj->delete("mv_nav_footer");
		$this->setFooterQuickLinksCache();
	}

	public function setHeaderMainNavigationCache(){
	 global $headerCacheExpiry;
	 $menu = getLayoutMenuData();
	 $this->menu=$menu;
	 if(!empty($this->menu)){
	 	$this->setKey("mv_nav_header",$this,$headerCacheExpiry);
	 }
	 return $this;
	}

	public function getHeaderMainNavigationCache(){
		$getHeaderNavCache=$this->getKey("mv_nav_header");
		if(!is_object($getHeaderNavCache)){
			$getHeaderNavCache=$this->setHeaderMainNavigationCache();
		}
		return $getHeaderNavCache;
	}

	public function deleteHeaderMainNavigationCache(){
		$this->cacheObj->delete("mv_nav_header");
		$this->setHeaderMainNavigationCache();
	}


	function setCacheBlogPage()
	{
		$url = "http://blogs.minyanville.com/feed/";
		$rss = fetch_rss($url);
		$this->content=$rss;
		$this->setKey("mv_blog",$rss,'5');
		return $this;
	}

	function getCacheBlogPage(){
		$getdata=$this->getKey("mv_blog");
		if(!is_object($getdata)){
			$getdata=$this->setCacheBlogPage();
		}
		return $getdata;

	}

	function deleteCacheHomePage(){
		$this->deleteKey("mv_home");
	}

	function setCacheMostEmailArticle(){
		global $mostemailarticleExpiry;
		$this->arrMostEmailedArt=mostEmailedArticle(7);
		$this->setKey("article_mostemail",$this,$mostemailarticleExpiry);
		return $this;
	}

	function getCacheMostEmailArticle(){
		$getmostemailart=$this->getKey("article_mostemail");
		if(!is_object($getmostemailart)){
			$getmostemailart=$this->setCacheMostEmailArticle();
		}
		return $getmostemailart;
	}

	function deleteCacheMostEmailArticle(){
		$this->deleteKey("article_mostemail");
		$this->setCacheMostEmailArticle();
	}


	function setCacheMostRead(){
		global $mostreadExpiry;
		$this->arrMostRead=getMostViewdAtriclesDailyfeed();
		$this->setKey("articledailyfeed_mostread",$this->arrMostRead,$mostreadExpiry);
		return $this->arrMostRead;
	}

	function getCacheMostRead(){
		$getmostread=$this->getKey("articledailyfeed_mostread");
		if(!is_array($getmostread)){
			$getmostread=$this->setCacheMostRead();
		}
		return $getmostread;
	}

	function deleteCacheMostRead(){
		$this->deleteKey("articledailyfeed_mostread");
		$this->setCacheMostRead();
	}



	function setCachePlaylist($catId=NULL,$curVideoId=NULL){
		global $D_R,$playlistExpiry;
		include_once($D_R.'/lib/videos/_data_lib.php');
		$this->playlist=getVideoPlaylist($catId,$curVideoId);
		$this->setKey("playlist".$catId,$this,$playlistExpiry);
		return $this;
	}

	function getCachePlaylist($catId=NULL,$curVideoId=NULL){
		$playlist=$this->getkey("playlist".$catId);
		if(!is_object($playlist)){
			$playlist=$this->setCachePlaylist($catId,$curVideoId);
		}
		return $playlist;
	}

	function deleteCachePlaylist($catId){
		$this->deleteKey("playlist".$catId);
		$this->setCachePlaylist($catId);
	}



	function setCacheBreadCrum($pageId){
		global $D_R,$BreadCrumExpiry;
		include_once($D_R.'/lib/_redesign_data_lib.php');
		$breadcrum[]=array("title"=>"Minyanville","alias"=>"/","page_id"=>"43");
		$resBreadCrum=getBreadCrum($pageId,"page",$breadcrum);
		if(!empty($resBreadCrum)){
			$this->setKey("BreadCrum".$pageId,$resBreadCrum,$BreadCrumExpiry);
		}
		return $resBreadCrum;
	}

	function getCacheBreadCrum($pageId){
		$breadCrum=$this->getkey("BreadCrum".$pageId);
		if(!empty($breadCrum)){
			return $breadCrum;
		}else{
			return $this->setCacheBreadCrum($pageId);
		}
	}

	function deleteCacheBreadCrum($pageId){
		$this->deleteKey("BreadCrum".$pageId);
		$this->setCacheBreadCrum($pageId);
	}
	public function getTheStreetCache($tsRelatedLinkId,$headlineURL,$rss)
	{
		$ts_article = $this->getKey($tsRelatedLinkId);
		if(!is_object($ts_article)){
			$ts_article=$this->setTheStreetCache($tsRelatedLinkId,$headlineURL,$rss);
		}
		return $ts_article;
	}
	public function setTheStreetCache($tsRelatedLinkId,$headlineURL,$rss)
	{
	 global $thestreetCacheExpiry;
	 $rss->load($headlineURL);
	 $items = $rss->getItems(); #returns all rss items
	 $i=1;
	 if(count($items)>0)
	 {
			$strRelatedLinks = array();
			foreach($items as $item )
			{
				if($i<4)
				{
					$title	=	$item['title'];
					$link	=	$item['link'];
					$strRelatedLinks[$title]	=	$link;
					$i++;
				}
			}
			$this->strRelatedLinksTS	= $strRelatedLinks;
		}
		$setcacheTheStreet=$this->setKey($tsRelatedLinkId,$this->strRelatedLinksTS,$thestreetCacheExpiry);
		return $this->strRelatedLinksTS;
	}


	public function getRealMoneySilverCache($rmsRelatedLinkId,$headlineURL,$rss)
	{
		$ts_article = $this->getKey($rmsRelatedLinkId);
		if(!is_object($ts_article)){
			$ts_article=$this->setRealMoneySilverCache($rmsRelatedLinkId,$headlineURL,$rss);
		}
		return $ts_article;
	}
	public function setRealMoneySilverCache($rmsRelatedLinkId,$headlineURL,$rss)
	{
	 global $thestreetCacheExpiry;
	 $rss->load($headlineURL);
	 $items = $rss->getItems(); #returns all rss items
	 $i=1;
	 if(count($items)>0)
	 {
	 	//$strRelatedLinks	=	'';
			$strRelatedLinks = array();
			foreach($items as $item )
			{
				if($i<4)
				{
					$title	=	$item['title'];
					$link	=	$item['link'];
					//$strRelatedLinks .= "<div><a href='".$item['link']."'>".$item['title']."</a></div>";
					$strRelatedLinks[$title]	=	$link;
					$i++;
				}
			}
			$this->strRelatedLinksRMS	= $strRelatedLinks;
		}
		$setcacheRMS=$this->setKey($rmsRelatedLinkId,$this->strRelatedLinksRMS,$thestreetCacheExpiry);
		return $this->strRelatedLinksRMS;
	}

	function getAllArticlesCache($start=0,$limit){
		$allArticlesProducts = $this->getKey("allarticles_".$start."_".$limit);
		if(!empty($allArticlesProducts)){
			return 	$allArticlesProducts;
		}else{
			return $this->setAllArticlesCache($start, $limit);
		}
	}

	function setAllArticlesCache($start,$limit){
		global $allArticleCacheExpiry;
		$qryArticleProducts = "SELECT item_id id,title,`author_name` author,`author_id`,`publish_date`,url,`item_type` FROM ex_item_meta EIT
WHERE item_type IN('1') AND is_live='1' AND publish_date > ('".mysqlNow()."' - INTERVAL 1 WEEK)
UNION
SELECT item_id id,title,`author_name` author,`author_id`,`publish_date`,url,`item_type` FROM ex_item_meta EIT
WHERE publish_date> ('".mysqlNow()."' - INTERVAL 1 WEEK) AND item_id IN
(SELECT EIPC.item_id FROM `ex_item_premium_content` EIPC WHERE EIPC.DATE> ('".mysqlNow()."' - INTERVAL 1 WEEK))
ORDER BY publish_date DESC LIMIT ".$start.",".$limit;
		$resArticlesProducts=exec_query($qryArticleProducts);
		if(!empty($resArticlesProducts)){
			$keyName="allarticles_".$start."_".$limit;
			$this->setKey($keyName,$resArticlesProducts,$allArticleCacheExpiry);
		}
		return $resArticlesProducts;
	}

	function deleteAllArticlesCache($start=0,$limit){
		$this->deleteKey("allarticles".$start."_".$limit);
	}

	function getMostRead($itemType=1,$limit=3){
		$resMostReadDailyFeed = $this->getKey("popular_".str_replace(',','_', $itemType)."_".$limit);
		if(!empty($resMostReadDailyFeed)){
			return 	$resMostReadDailyFeed;
		}else{
			return $this->setMostRead($itemType,$limit);
		}
	}

       function setMostRead($itemType=1,$limit){
        global $D_R,$MostReadCacheExpiry,$article_most_read_days;

		$context =
		    array("http"=>
		      array(
		        "method" => "get",
		        "header" => "custom-header: custom-value\r\n" ,
		                    "custom-header-two: custome-value-2\r\n"
		      )
		    );
		    
		$context = stream_context_create($context);
		$url = "http://api.chartbeat.com/live/toppages/v3/?apikey=c6e7a6c28565da05b0a469871e43d4b7&host=minyanville.com&limit=25";
		$results = file_get_contents($url, false, $context);
		
		$resArtId = array();
        if(!empty($results->pages))
        {
        	$c="0";
	        foreach($results->pages as $key=>$val)
	        {
	        	if($c<5)
	        	{
		            $artUrl = explode('?',$val->path);
		            $qry='SELECT item_id,url,title,author_id,author_name FROM ex_item_meta WHERE is_live="1" AND item_type IN (1,18) AND url = "'.$artUrl[0].'" ';
		            $resultAr=exec_query($qry,1);
		            if(!empty($resultAr))
		            {
		            	if(!in_array($resultAr['item_id'],$resArtId))
		            	{
			                $resMostReadChart[]=$resultAr;
			                $resArtId[] = $resultAr['item_id'];
			                $c++;
		            	}
		            }
	        	}
	         }
         }
         $countArt = sizeof($resMostReadChart);

         if($countArt<5)
         {
         	  if(!empty($resArtId))
         	  {
         	  	$listArtId = implode("','",$resArtId);
         	  }
         	  else
         	  {
         	  	$listArtId='';
         	  }
           	  $limit= intval(5)-intval($countArt);

	          $qry="SELECT url,title,author_id,author_name FROM ex_item_meta WHERE is_live='1' AND
	          item_id NOT IN ('".$listArtId."') AND item_type='1'
	ORDER BY publish_date DESC LIMIT ".$limit;
	          $resMostReadDetails=exec_query($qry);

	          if(!empty($resMostReadChart))
	          {
	         	$resMostReadDetails = array_merge($resMostReadChart,$resMostReadDetails);
	          }
         }
         else
         {
         	$resMostReadDetails = $resMostReadChart;
         }

         if(is_array($resMostReadDetails)){
                $this->setKey("popular_".str_replace(',','_', $itemType)."_".$limit, $resMostReadDetails, $MostReadCacheExpiry);
                }
         return  $resMostReadDetails;
       }

	function getTickersCache($id,$item_type='18',$is_live)
	{
		$resTickers = $this->getKey("tickers_".$item_type."_".$id);
		if(!empty($resTickers) && $is_live=="1"){
			return 	$resTickers;
		}else{
			return $this->setTickersCache($id,$item_type);
		}
	}
	function setTickersCache($id,$item_type)
	{
		global $tickersExpiry,$D_R;
		$keyName= "tickers_".$item_type."_".$id;
		switch($item_type){
			case "18":
				include_once($D_R."/admin/lib/_dailyfeed_data_lib.php");
				$objDailyFeed= new Dailyfeed();
				$tickers_list = $objDailyFeed->getTickersExchange($id,$item_type);
				foreach($tickers_list as $key=>$val)
				{
					$resTickers[]=$val['stocksymbol'];
				}
				break;
		}
		if(count($resTickers) > 0){
			$this->setKey($keyName,$resTickers,$tickersExpiry);
		}
		return $resTickers;
	}

	function getTickerListCache($id,$item_type='18',$tickerArr,$title,$is_live)
	{
		$resTickerList = $this->getKey("tickerlist_".$item_type."_".$id);
		if(!empty($resTickerList) && $is_live=="1"){
			return 	$resTickerList;
		}else{
			return $this->setTickerListCache($id,$item_type,$tickerArr,$title);
		}
	}
	function setTickerListCache($id,$item_type,$tickerArr,$title)
	{
		global $tickersExpiry,$D_R;
		$keyName= "tickerlist_".$item_type."_".$id;
		$tickerArr = array_slice($tickerArr, 0, 5);
		$tickerArr= str_replace("^","",$tickerArr);
		$tickerList = implode(' $',$tickerArr);
		$share_content=$title." $".$tickerList." http://shar.es/viuUj  via @minyanville";
		while(strlen($share_content)>140 && !empty($tickerArr))
		{
		 	array_pop($tickerArr);
		 	$tickerList = implode(' $',$tickerArr);
		 	$share_content=$title." $".$tickerList." http://shar.es/viuUj  via @minyanville";
		}
		if(empty($tickerArr))
		{
		 	$resTickerList ="";
		}
		else
		{
		 	$resTickerList =" $".$tickerList;
		}

		if(count($resTickerList) > 0){
			$this->setKey($keyName,$resTickerList,$tickersExpiry);
		}
		return $resTickerList;
	}

	function getMostPopularDFSources(){
		$resMostPopularDFSources = $this->getKey("mostPopularDFSources");
		if(!empty($resMostPopularDFSources)){
			return 	$resMostPopularDFSources;
		}else{
			return $this->setMostPopularDFSources();
		}
	}

	function setMostPopularDFSources(){
		global $dailyfeedtopsources,$dailyfeed_source_days,$dfMostPopularSourcesCacheExpiry;
		$sqlMostPopularDFSources="SELECT d.id,s.source, count(s.source) countsource,
	s.url, s.source_link  FROM daily_feed d,ex_source s where d. is_deleted='0' and
	(To_days('".mysqlNow()."') - TO_DAYS(d.updation_date)<='".$dailyfeed_source_days."')
	and d.id = s.item_id and s.item_type ='18' and d.is_approved='1' and d.is_live='1'
	 group by s.source order by countsource desc limit 0,".$dailyfeedtopsources;
		$resMostPopularDFSources=exec_query($sqlMostPopularDFSources);
		if(is_array($resMostPopularDFSources)){
			$this->setKey("mostPopularDFSources", $resMostPopularDFSources, $dfMostPopularSourcesCacheExpiry);
		}
		return $resMostPopularDFSources;
	}

	function getMostPopularDFCountributors(){
		$resMostPopularDFCountributors = $this->getKey("mostPopularDFContibutors");
		if(!empty($resMostPopularDFCountributors)){
			return 	$resMostPopularDFCountributors;
		}else{
			return $this->setMostPopularDFConuntributors();
		}
	}

	function setMostPopularDFConuntributors(){
		global $dailyfeedcontributor,$dailyfeed_contributors_days,$dfMostPopularContributorCacheExpiry;
		$qryMostPopularDFContributors="SELECT C.name,DF.contrib_id, count(DF.contrib_id) countcontribid FROM daily_feed DF,contributors C
	   	where DF.is_deleted='0' and DF.is_approved='1' and C.id=DF.contrib_id and
	   	(To_days('".mysqlNow()."') - TO_DAYS(DF.updation_date)<='".$dailyfeed_contributors_days."')
	   	 group by DF.contrib_id order by countcontribid desc limit 0,".$dailyfeedcontributor;
		$resMostPopularDFContributors=exec_query($qryMostPopularDFContributors);
		if(!empty($resMostPopularDFContributors)){
			$this->setKey("mostPopularDFContibutors", $resMostPopularDFContributors, $dfMostPopularContributorCacheExpiry);
		}
		return $resMostPopularDFContributors;
	}

	function getMostPopularDFTickers(){
		$resMostPopularDFTickers = $this->getKey("mostPopularDFTickers");
		if(!empty($resMostPopularDFTickers)){
			return 	$resMostPopularDFTickers;
		}else{
			return $this->setMostPopularDFTickers();
		}
	}

	function setMostPopularDFTickers(){
		global $dailyfeed_tickers_days,$dailyfeedstockcount,$dfMostPopularTickerCacheExpiry;
		$sqlMostPopularDFTickers= "select DF.id, ES.stocksymbol,ET.ticker_id,count(ES.stocksymbol) countticker from
		ex_stock ES,ex_item_ticker ET, daily_feed DF where ES.id=ET.ticker_id and DF.is_deleted='0'
		and (To_days('".mysqlNow()."') - TO_DAYS(DF.updation_date)<='".$dailyfeed_tickers_days."') and DF.is_draft='0'
		and DF.is_approved='1' and DF.is_live='1' and ET.item_id =DF.id and ET.item_type='18'
		group by ES.stocksymbol order by countticker limit 0,".$dailyfeedstockcount;
		$resMostPopularDFTickers=exec_query($sqlMostPopularDFTickers);
		if(!empty($resMostPopularDFTickers)){
			$this->setKey("mostPopularDFTickers", $resMostPopularDFTickers, $dfMostPopularTickerCacheExpiry);
		}
		return $resMostPopularDFTickers;
	}

	function getDFHotTopics(){
		$resDFHotTopics = $this->getKey("mostPopularDFTopics");
		if(!empty($resDFHotTopics)){
			return 	$resDFHotTopics;
		}else{
			return $this->setDFHotTopics();
		}
	}

	function setDFHotTopics(){
		global $dailyfeed_source_days,$dailyfeedhottopiccount,$dfMostPopularTopicsCacheExpiry;
		$sqlDFHotTopics="SELECT xt.tag as tagname,xt.url as tagurl, count(xt.tag) counttag FROM ex_item_tags xbt, ex_tags xt,daily_feed d
		where is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(updation_date)<='".$dailyfeed_source_days."')
		and is_approved='1' and is_live='1' and xbt.item_id =d.id and xt.id = xbt.tag_id and xbt.item_type ='18'
		group by xt.tag order by counttag desc limit ".$dailyfeedhottopiccount;
		$resDFHotTopics=exec_query($sqlDFHotTopics);
		if(!empty($resDFHotTopics)){
			$this->setKey("mostPopularDFTopics", $resDFHotTopics, $dfMostPopularTopicsCacheExpiry);
		}
		return $resDFHotTopics;
	}

	function getViaErrors(){
		$resultGetViaErrors = $this->getKey("ViaErrors");
		if(!empty($resultGetViaErrors)){
			return 	$resultGetViaErrors;
		}else{
			return $this->setViaErrors();
		}
	}

	function setViaErrors(){
		global $viaerrorCacheExpiry;
		$qrygetViaErrors="SELECT id,errorCode,errorName,customemessage from viaerrormaster";
		$resultGetViaErrors=exec_query($qrygetViaErrors);
		if(!empty($resultGetViaErrors)){
			$this->setKey("ViaErrors", $resultGetViaErrors, $viaerrorCacheExpiry);
		}
		return $resultGetViaErrors;
	}

	function getFeaturedSlider(){
		$getFeaturedSlides = $this->getKey("featuredModule");
		if(!empty($getFeaturedSlides)){
			return 	$getFeaturedSlides;
		}else{
			return $this->setFeaturedSlider();
		}
	}

	function setFeaturedSlider(){
		global $D_R,$homeFeaturedSliderCacheExpiry;
		include_once $D_R.'/lib/_layout_design_lib.php';
		$getFeaturedSlides=renderFeatureModuleSlides();
		if(!empty($getFeaturedSlides)){
			$this->setKey("featuredModule", $getFeaturedSlides, $homeFeaturedSliderCacheExpiry);
		}
		return $getFeaturedSlides;
	}
	function getFeaturedSliderNoAd(){
		$getFeaturedSlides = $this->getKey("featuredModuleNoAd");
		if(!empty($getFeaturedSlides)){
			return 	$getFeaturedSlides;
		}else{
			return $this->setFeaturedSliderNoAd();
		}
	}
	function getBuyHedgeFeaturedSliderNoAd(){
		//$getFeaturedSlides = $this->getKey("featuredBuyHedgeModuleNoAd");
		if(!empty($getFeaturedSlides)){
			return 	$getFeaturedSlides;
		}else{
			return $this->setBuyHedgeFeaturedSliderNoAd();
		}
	}
	function setFeaturedSliderNoAd(){
		global $D_R,$homeFeaturedSliderCacheExpiry;
		include_once $D_R.'/lib/_layout_design_lib.php';
		$getFeaturedSlides=renderFeatureModuleSlides('no_ad');
		if(!empty($getFeaturedSlides)){
			$this->setKey("featuredModuleNoAd", $getFeaturedSlides, $homeFeaturedSliderCacheExpiry);
		}
		return $getFeaturedSlides;
	}
	function setBuyHedgeFeaturedSliderNoAd(){
		global $D_R,$homeFeaturedSliderCacheExpiry;
		include_once $D_R.'/lib/_layout_design_lib.php';
		$getFeaturedSlides=renderFeatureModuleSlidesBuyHedge();
		if(!empty($getFeaturedSlides)){
			$this->setKey("featuredBuyHedgeModuleNoAd", $getFeaturedSlides, $homeFeaturedSliderCacheExpiry);
		}
		return $getFeaturedSlides;
	}
	function getPageModules($page_id,$placeholder){

		$stModuleList = $this->getModulesListCache($page_id,$placeholder);
		$arModules = explode(",",$stModuleList);
		$strContent = "";
		foreach($arModules as $module_id)
		{
			$stModuleContent = $this->getPageModuleCache($module_id);
			if(isset($_SESSION['AdsFree']) && strpos($stModuleContent,"CM8ShowAd"))
			{
				// don't render ad module for ad free users
			}
			else
			{
			$strContent .= $stModuleContent;
			}
		}
		return $strContent;
	}
	function getModulesListCache($page_id,$placeholder)
	{
		$key = "module_order_".$page_id."_".$placeholder;
		$module_order = $this->getKey($key);
		if($module_order == "")
		{
			return $this->setModuleListCache($page_id,$placeholder);
		}
		return $module_order;
	}
	function setModuleListCache($page_id,$placeholder)
	{
		global $pageModulesListCacheExpiry;
		$key = "module_order_".$page_id."_".$placeholder;
		$stQuery = "SELECT module_order FROM layout_placeholder WHERE page_id = '$page_id' AND placeholder='$placeholder'";
		$arModulesResult = exec_query($stQuery,1);
		$module_order = $arModulesResult['module_order'];
		if($module_order != "")
		{
		$this->setKey($key,$module_order,$pageModulesListCacheExpiry);
		}
		return $module_order;
	}
	
	function getEduSliderModuleCache($placeHolder){
		$this->deleteKey('eduFeaturedModule');
		$getEduFeaturedSlides = $this->getKey("eduFeaturedModule");
		if(!empty($getEduFeaturedSlides)){
			return 	$getEduFeaturedSlides;
		}else{
			return $this->setEduSliderModuleCache($placeHolder);
		}
	}

	function setEduSliderModuleCache($placeHolder){
		global $D_R,$homeFeaturedSliderCacheExpiry;
		include_once $D_R.'/lib/_layout_design_lib.php';
		$getEduFeaturedSlides = renderEduFeatureModuleSlides($placeHolder);
		if(!empty($getEduFeaturedSlides)){
			$this->setKey("eduFeaturedModule", $getEduFeaturedSlides, $homeFeaturedSliderCacheExpiry);
		}
		return $getEduFeaturedSlides;
	}
	
	function getScrollModuleContent($offset,$module_id)
	{
		global $D_R,$eduScrollLimit;
		include($D_R.'/lib/config/_edu_config.php');
		$stKey="scroll_module_".$offset."_".$eduScrollLimit."_".$module_id;
		$resultContent = $this->getKey($stKey);
		if($resultContent == "")
		{
			return $this->setScrollModuleContent($offset,$module_id,$eduScrollLimit);
		}
		return $resultContent;
	}
	
	function setScrollModuleContent($offset,$module_id,$eduScrollLimit)
	{
		global $D_R,$scrollContentCacheExpiry;
		$stKey="scroll_module_".$offset."_".$eduScrollLimit."_".$module_id;
		$qry="SELECT `content_sql` FROM `layout_module_component` WHERE `module_id`='".$module_id."' AND `component_type`='dynamic_list'";
		$result=exec_query($qry,1);
		
		$sqlContent = substr($result['content_sql'], 0, (strlen($result['content_sql'])-3)); 
		$sqlContent =$sqlContent." ".$offset.",".$eduScrollLimit;
		$resultContent=exec_query($sqlContent);		
		if($resultContent != "")
		{
			$this->setKey($stKey,$resultContent, $scrollContentCacheExpiry);
		}
		return $resultContent;
	}
	
	function getPageModuleCache($module_id)
	{
		$stKey="module_".$module_id;
		$module = $this->getKey($stKey);
		if($module == "")
		{
			return $this->setPageModuleCache($module_id);
		}
		return $module;

	}
	function setPageModuleCache($module_id)
	{
		global $D_R,$pageModulesCacheExpiry;
		include_once("$D_R/lib/_layout_design_lib.php");
		$stKey="module_".$module_id;
		include_once $D_R.'/lib/_layout_design_lib.php';
		$module=renderTemplateContent($module_id);
		if($module != "")
		{
			$this->setKey($stKey,$module, $pageModulesCacheExpiry);
		}
		return $module;
	}
	function deletePageModuleCache($module_id)
	{
		$stKey="module_".$module_id;
		$this->setKey($stKey,"",0);
	}
	function getLatestWorryDate(){
		$worryCurrentDate = $this->getKey("worryCurrentDate");
		if(!empty($worryCurrentDate)){
			return 	$worryCurrentDate;
		}else{
			return $this->setLatestWorryDate();
		}
	}

	function setLatestWorryDate(){
		global $D_R,$latestWorryExpiry;
		include_once $D_R.'/lib/lloyds-wall-of-worry/_worry_data_lib.php';
		$objWorryData = new worryData();
		$worryCurrentDate=$objWorryData->getCurrentWryDate();
		$worryCurrentDate=date("n/j/Y",strtotime($worryCurrentDate));
		if(!empty($worryCurrentDate)){
			$this->setKey("worryCurrentDate", $worryCurrentDate, $latestWorryExpiry);
		}
		return $worryCurrentDate;
	}

	function getMostRecentArticles(){
		$getMostRecentArticles = $this->getKey("mostRecentArticles");
		if(!empty($getMostRecentArticles)){
			return 	$getMostRecentArticles;
		}else{
			return $this->setMostRecentArticles();
		}
	}

	function setMostRecentArticles(){
		global $D_R,$mostRecentArticlesCacheExpiry;
		$getMostRecentArticles=getMostRecent();
		if(!empty($getMostRecentArticles)){
			$this->setKey("mostRecentArticles", $getMostRecentArticles, $mostRecentArticlesCacheExpiry);
		}
		return $getMostRecentArticles;
	}

	function getBuzzData($offset,$fetch,$limit){
		$allBuzzData = $this->getKey("allbuzzpost_".$offset."_".$fetch);
		if(!empty($allBuzzData)){
			return 	$allBuzzData;
		}else{
			return $this->setBuzzDataCache($offset,$fetch,$limit);
		}
	}

	function setBuzzDataCache($offset,$fetch,$limit){
		global $allBuzzeCacheExpiry;
	 $qry="SELECT distinct buzzbanter_today.id AS id, " .
	"buzzbanter_today.title AS title, " .
	"buzzbanter_today.body AS body, " .
	"buzzbanter_today.author AS author, " .
	"buzzbanter_today.contrib_id as contrib_id, " .
	"buzzbanter_today.branded_img_id as brandedlogo, " .
	"buzzbanter_today.image as image, " .
	"buzzbanter_today.position AS position,  " .
	"date_format(buzzbanter_today.date,'%r') AS mdate, " .
	"UNIX_TIMESTAMP(buzzbanter_today.date) AS udate, " .
	"buzzbanter_today.date AS date, " .
	"buzzbanter_today.login AS login " .
	"FROM buzzbanter_today,contributors " .
	"WHERE DATE_FORMAT(buzzbanter_today.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
	"AND buzzbanter_today.contrib_id = contributors.id ".
	"AND buzzbanter_today.is_live='1' " .
	"AND buzzbanter_today.show_in_app='1' " .
	"AND buzzbanter_today.approved='1' " .
	"ORDER BY buzzbanter_today.date ASC " . $limit;

	 $resBuzzData = exec_query($qry);

		if(!empty($resBuzzData)){
			$keyName="allbuzzpost_".$offset."_".$fetch;
			$this->setKey($keyName,$resBuzzData,$allBuzzeCacheExpiry);
		}
		return $resArticlesProducts;
	}

	function deleteBuzzDataCache($offset,$fetch){
		$keyName="allbuzzpost_".$offset."_".$fetch;
		$this->cacheObj->delete();
	}
	function getTopicPageLatestArticleCache($subSection){
		$key="topiclatestarticle_".$subSection;
		$resLatestArticle = $this->getKey($key);
		if(!empty($resLatestArticle)){
			return 	$resLatestArticle;
		}else{
			return $this->setTopicPageLatestArticleCache($subSection);
		}

	}
	function setTopicPageLatestArticleCache($subSection){
		global $topicCacheExpiry;
		$key="topiclatestarticle_".$subSection;
		$subSectionHyphen=str_replace("-"," ",$subSection);
		$subSectionWithSlash=str_replace("-","/",$subSection);
		$qry="select section_id,name,page_id,topic_page_id from section where subsection_type = 'article' and type='subsection' and is_active='1' and (name='".$subSectionHyphen."' or name ='".$subSectionWithSlash."') ";
		$result=exec_query($qry,1);
		if(!empty($result)){
			$this->setKey($key,$result,$topicCacheExpiry);
		}
		return $result;

	}
	function deleteTopicPageLatestArticleCache($subSection){
		$key="topiclatestarticle_".$subSection;
		$this->deleteKey($key);
	}
	function getPageMetaDataCache($item_type,$item_id)
	{
		$key="PageMeta_".$item_type."_".$item_id;
		$arPageMeta = $this->getKey($key);

		if(!empty($arPageMeta)){
			return 	$arPageMeta;
		}else{
			return $this->setPageMetaDataCache($item_type,$item_id);
		}
	}
	function getUniqueTitleCache($item_type,$item_id)
	{
		$key="uniquetitle_".$item_type."_".$item_id;
		$arUnqTitle = $this->getKey($key);

		if(!empty($arUnqTitle)){
			return 	$arUnqTitle;
		}else{
			return $this->setUniqueTitleCache($item_type,$item_id);
		}
	}

	function setUniqueTitleCache($item_type,$item_id)
	{
		global $unqTitleCacheExpiry;
		$key="uniquetitle_".$item_type."_".$item_id;
		$qryTitle=" SELECT COUNT(id) as 'count' FROM ex_item_meta WHERE item_type='".$item_type."' AND item_id<>'".$item_id."'
 AND title=(SELECT title FROM ex_item_meta WHERE item_type='".$item_type."' AND item_id='".$item_id."')";
		$resultTitle=exec_query($qryTitle);
		if(!empty($resultTitle)){
			$this->setKey($key,$resultTitle[0],$unqTitleCacheExpiry);
		}
		return $resultTitle[0];
	}

	function setPageMetaDataCache($item_type,$item_id)
	{
		global $metaDataCacheExpiry;
		$key="PageMeta_".$item_type."_".$item_id;
		$sqlGetMeta="Select IF(seo_title='' OR ISNULL(seo_title),title,seo_title) AS title, title AS content_title, keywords, description, url, section, item_type, is_live, publish_date, author_name, resource, tickers, is_title_duplicate from ex_item_meta where item_type='".$item_type."' and item_id='".$item_id."'";
		$arMetaData=exec_query($sqlGetMeta,1);
		if($item_type=="1"){
			$getArticleBody="SELECT id,date,body FROM `articles` WHERE id='".$item_id."'";
			$resArticleBody=exec_query($getArticleBody,1);
			$tag = getTag($resArticleBody['body'],'img');
			$arMetaData['articleImg'] = $tag[0]['src'];
			$arMetaData['date'] = $resArticleBody['date'];
			$getSection="select name from section S,articles A where S.section_id=A.navigation_section_id and A.id='".$item_id."'";
			$getSectionResult=exec_query($getSection,1);
			$arMetaData['section']=$getSectionResult['name'];
		}

		if($item_type=="18"){
			$qryMetaImage = "SELECT url FROM ex_item_image where item_type='".$item_type."' and item_id='".$item_id."'";
			$resMetaImage = exec_query($qryMetaImage,1);
			$arMetaData['mvPremiumImg'] = $resMetaImage['url'];
			$arMetaData['date'] = $feedData->creation_date;
		}

		if(is_array($arMetaData))
		{
			$this->setKey($key,$arMetaData,$metaDataCacheExpiry);
		}
		return $arMetaData;
	}

	function getDailyFeedListCache($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid,$category)
	{
		global $dailyfeedLandingItems;
		if($topic || $source || $cid || $tid){
			return $this->setDailyFeedListCache($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid,$category);
		}else{
			$key="dailyfeedlist";
			$resDailyFeedList = $this->getKey($key);
			if(!empty($resDailyFeedList)){
				return 	$resDailyFeedList;
			}else{
				return $this->setDailyFeedListCache($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid,$category);
			}
		}
	}
	function setDailyFeedListCache($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid,$category){

		global $D_R,$dailyfeedLandingItems,$dailyFeedListingCacheExpiry;
		include_once($D_R.'/admin/lib/_dailyfeed_data_lib.php');
		$obj_daily=new Dailyfeed("daily_feed","");
		$offset=$offset*$dailyfeedLandingItems;
		if($topic || $source || $cid || $tid){
			$result=$obj_daily->getDailyFeedByTopic($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid);
			return $result;
		}elseif($category){
			if($offset){$category=$category.'/';}
			$url='/dailyfeed/category/'.$category;
			$result=$obj_daily->getDailyFeedQuickTitleListByCategory($url,$offset);
			return $result;
		}else{
			$key="dailyfeedlist";
			$qry="Select DF.id,DF.creation_date,DF.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,DF.title,DF.excerpt,DF.body,DF.publish_date,C.id as 'ContId',C.name contributor,DF.is_draft,DF.position,DF.layout_type FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' order by display_date DESC limit 0,".$dailyfeedLandingItems;
		}
		$result=exec_query($qry);
		$this->setKey($key,$result,$topicCacheExpiry);
		return $result;
	}

	function getDailyFeedCountCache()
	{
		$key="dailyfeedCount";
		$resDailyFeedCount = $this->getKey($key);
		if(!empty($resDailyFeedCount)){
			return 	$resDailyFeedCount;
		}else{
			return $this->setDailyFeedCountCache();
		}
	}
	function setDailyFeedCountCache(){

	 global $topicCacheExpiry,$dailyFeedListingCacheExpiry;
	 $key="dailyfeedCount";
	 $qry="Select count(DF.id) count FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1'";
	 $result=exec_query($qry,1);
	 $count = $result['count'];
	 if(!empty($result)){
			$this->setKey($key,$count,$topicCacheExpiry);
		}
		return $count;
	}

	function getDailyFeedCountTopicSourceCache($dailyfeedLandingItems,$topic,$source,$cid,$tid,$category){
		if($topic){
			$key="dailyFeedCount_topic_".$topic;
		}elseif($source){
			$key="dailyFeedCount_source_".$source;
		}elseif($cid){
			$key="dailyFeedCount_cid_".$cid;
		}elseif($tid){
			$key="dailyFeedCount_tid_".$tid;
		}elseif($category){
			$key="dailyFeedCount_category_".$category;
		}else{
			$key="dailyFeedCount_count";
		}

		$resDailyFeedCount = $this->getKey($key);
		if(!empty($resDailyFeedCount)){
			return 	$resDailyFeedCount;
		}else{
			return $this->setDailyFeedCountTopicSourceCache($dailyfeedLandingItems,$topic,$source,$cid,$tid,$category,$key);
		}
	}

	function setDailyFeedCountTopicSourceCache($dailyfeedLandingItems,$topic,$source,$cid,$tid,$category,$key){
		global $dfCountCache;
		if($topic){
			$topic	= "/".$topic;
			$chkTag = substr($topic,-1);
			if($chkTag != '/')
			{
				$topic	= $topic."/";
			}
			$qry="SELECT count(xbt.item_id) count FROM ex_item_tags xbt, ex_tags xt,daily_feed DF  where xt.id = xbt.tag_id and xbt.item_type ='18' and DF.id=xbt.item_id and DF.is_approved='1' and DF.is_live='1' and DF.is_deleted='0' and xt.url='".$topic."'";
			$key="dailyFeedCount_topic_".$topic;
		}elseif($source){
			$source	= "/".$source;
			$chkStr = substr($source,-1);
			if($chkStr != '/')
			{
				$source	= $source."/";
			}
			$qry="SELECT count(s.id) count FROM ex_source s ,daily_feed DF where DF.id=s.item_id and s.item_type ='18' and DF.is_approved='1' and DF.is_deleted='0' and DF.is_live='1' and s.url='".$source."'";
			$key="dailyFeedCount_source_".$source;
		}elseif($cid){
			$qry="Select count(DF.id) count FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' and DF.contrib_id='".$cid."'";
			$key="dailyFeedCount_cid_".$cid;
		}elseif($tid){
			$qry="select count(item_id) count from ex_item_ticker where ticker_id='".$tid."' and item_type='18'";
			$key="dailyFeedCount_tid_".$tid;
		}elseif($category){
			$url='/dailyfeed/category/'.$category;
			$qry="SELECT count(quick_title) count FROM ex_quick_title EQT,daily_feed DF WHERE LOWER(EQT.url) = '".$url."' AND EQT.item_type='18' and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' and DF.id=EQT.item_id
";
			$key="dailyFeedCount_category_".$category;
		}else{
			$qry="Select count(DF.id) count FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1'";
			$key="dailyFeedCount_count";
		}
		$result=exec_query($qry,1);
		if(!empty($result)){
			$count=$result['count'];
			$this->setKey($key,$count,$dfCountCache);
			return $count;

		}
		return false;
	}


	function getImageDailyFeedCache($feedId)
	{
		$key="imageDailyfeed_".$feedId;
		$resImageDailyFeed = $this->getKey($key);
		if(!empty($resImageDailyFeed)){
			return 	$resImageDailyFeed;
		}else{
			return $this->setImageDailyFeedCache($feedId);
		}
	}
	function setImageDailyFeedCache($feedId)
	{
		global $topicCacheExpiry;
		$key="imageDailyfeed_".$feedId;
		$qryImage="select id,url,name from ex_item_image where item_id='".$feedId."' and item_type='18'";
		$resultImage=exec_query($qryImage,1);
		if(!empty($resultImage)){
			$this->setKey($key,$resultImage,$topicCacheExpiry);
		}
		if($resultImage){
			return $resultImage;
		}else{
			return false;
		}
	}
	function getQuickTitleDailyFeedCache($feedId,$item_type)
	{
		$key="quicktitledailyfeed_".$feedId;
		$resQuickTitleFeed = $this->getKey($key);
		if(!empty($resQuickTitleFeed)){
			return 	$resQuickTitleFeed;
		}else{
			return $this->setQuickTitleDailyFeedCache($feedId,$item_type);
		}
	}

	function setQuickTitleDailyFeedCache($id,$item_type)
	{
		global $topicCacheExpiry;
		$key="quicktitledailyfeed_".$id;
		$qry="select quick_title,url from ex_quick_title where item_id='".$id."' and item_type='".$item_type."'";
		$result=exec_query($qry,1);
		if(!empty($result)){
			$this->setKey($key,$result,$topicCacheExpiry);
		}
		return $result;
	}
	function getTopicsURLCache($item_table,$feedId)
	{
		global $topicCacheExpiry;
		$key="topicurl_".$feedId;
		$resTopicurl = $this->getKey($key);
		if(!empty($resTopicurl))
		{
			return 	$resTopicurl;
		}
		else
		{
			return $this->setTopicsURLCache($feedId,$item_table);
		}

	}

	function setTopicsURLCache($feedId,$item_table){


		global $topicCacheExpiry;
		$key="topicurl_".$feedId;

		$pageitem=exec_query("SELECT id FROM ex_item_type WHERE item_table='".$item_table."'",1);

		$tagquery="SELECT xbt.item_id, xbt.tag_id, xt.tag as tagname,xt.url as tagurl FROM ex_item_tags xbt, ex_tags xt where xbt.item_id='".$id."' and xt.id = xbt.tag_id and xbt.item_type ='".$pageitem[id]."'";
		$tagres = exec_query($tagquery);
		if($tagres && count($tagres)>0)
		{
			$pagetag = array();
			foreach($tagres as $tagkey => $tagvalue)
			{
				$pagetag[$tagvalue[tagname]]	=	 $tagvalue[tagurl];
			}

			if(!empty($tagquery)){
				$this->setKey($key,$pagetag,$topicCacheExpiry);
			}
			return $pagetag;
		}
		else{
			return false;
		}

	}
	function deleteDailyFeedCountCache(){
		$key="dailyfeedCount";
		$this->deleteKey($key);
	}

	function deleteDailyFeedListCache(){
		$key="dailyfeedlist";
		$this->deleteKey($key);
	}

	function deleteImageDailyFeedCache($feedId)
	{
		$key="imageDailyfeed_".$feedId;
		$this->deleteKey($key);
	}

	function deleteQuickTitleDailyFeedCache($feedId)
	{
		$key="quicktitledailyfeed_".$feedId;
		$this->deleteKey($key);
	}

	function deleteResourcefeedcache($feedId){
		$this->deleteKey("resourcefeedcache_".$feedId);
	}

	function deleteDftickerstxchange($feedId){
		$this->deleteKey("tickerstxchange_".$feedId);
	}

	function deleteDftopicurl($feedId){
		$this->deleteKey("topicurl_".$feedId);
	}


	function getDailyfeedHotTopicsCache()
	{
		global $dailyfeedLandingItems;
		$key="dailyfeedhottopic";
		$resDailyFeedList = $this->getKey($key);
		if(!empty($resDailyFeedList)){
			return 	$resDailyFeedList;
		}else{
			return $this->setDailyfeedHotTopicsCache();
		}
	}
	function setDailyfeedHotTopicsCache(){
		global $dailyfeed_source_days,$topicCacheExpiry,$dailyfeedhottopiccount;
		$key="dailyfeedhottopic";
		$sql="SELECT xt.tag as tagname,xt.url as tagurl, count(xt.tag) counttag FROM ex_item_tags xbt, ex_tags xt,daily_feed d where is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(updation_date)<='".$dailyfeed_source_days."') and is_approved='1' and is_live='1' and xbt.item_id =d.id and xt.id = xbt.tag_id and xbt.item_type ='18' group by xt.tag order by counttag desc limit ".$dailyfeedhottopiccount;
		$results=exec_query($sql);
		$this->setKey($key,$results,$topicCacheExpiry);
		return $results;
	}

	function deleteDailyfeedHotTopicsCache(){
		$key="dailyfeedhottopic";
		$this->deleteKey($key);
	}

	function getDailyFeedUrlCache($id)
	{
		$key="dailyfeedurl_".$id;
		$resDailyFeedList = $this->getKey($key);
		if(!empty($resDailyFeedList)){
			return 	$resDailyFeedList;
		}else{
			return $this->setDailyFeedUrlCache($id);
		}
	}
	function setDailyFeedUrlCache($id){
		$key="dailyfeedurl_".$id;
		$qry="select url from ex_item_meta where item_id='".$id."' and item_type='18'";
		$result=exec_query($qry,1);
		$this->setKey($key,$result['url'],$topicCacheExpiry);
		return $result['url'];
	}

	function deleteDailyFeedUrlCache($id){
		$key="dailyfeedurl_".$id;
		$this->setKey($key,'false');
		$qry="select url from ex_item_meta where item_id='".$id."' and item_type='18'";
		$result=exec_query($qry,1);
		$this->setKey($result['url'],'false');
	}

	function getItemIdByURL($itemURL,$ItemType)
	{
		$key = "url_item_id_".$ItemType."_".$itemURL;
		$resItemLink = $this->getKey($key);
		if(!empty($resItemLink)){
			return 	$resItemLink;
		}else{
			return $this->setItemIdByURL($itemURL,$ItemType);
		}
	}
	function setItemIdByURL($itemURL,$ItemType){
		global $itemLinkCacheExpiry,$D_R;
		include_once $D_R.'/lib/config/_cache_config.php';
		$key = "url_item_id_".$ItemType."_".$itemURL;
		$sqlItemURL= "SELECT item_id from content_seo_url WHERE item_type = '".$ItemType."' AND url = '".$itemURL."'";
		$resItemURL= exec_query($sqlItemURL,1);
		if(!empty($resItemLink['item_id'])){
			$this->setKey($key,$resItemLink['item_id'],$itemLinkCacheExpiry);
		}
		return $resItemURL['item_id'];
	}
	function getItemLink($itemID,$ItemType){
		$resItemLink = $this->getKey("url_".$ItemType."_".$itemID);
		if(!empty($resItemLink)){
			return 	$resItemLink;
		}else{
			return $this->setItemLink($itemID,$ItemType);
		}
	}

	function setItemLink($itemID,$ItemType){
		global $itemLinkCacheExpiry,$D_R;
		include_once $D_R.'/lib/config/_cache_config.php';
		if($ItemType!="" && $itemID!=""){
			$sqlItemLink = "SELECT url,is_live from ex_item_meta WHERE item_type = '".$ItemType."' AND item_id = ".$itemID;
			$resItemLink = exec_query($sqlItemLink,1);
		}
		if(!empty($resItemLink['url']) && $resItemLink['is_live']=='1'){
			$this->setKey("url_".$ItemType."_".$itemID,$resItemLink['url'],$itemLinkCacheExpiry);
		}
		return $resItemLink['url'];
	}
	function getBlogPostCache()
	{
		$key="blogposts";
		$resBlogPosts = $this->getKey($key);
		if(!empty($resBlogPosts)){
			return $resBlogPosts;
		}else{
			return $this->setBlogPostCache();
		}
	}
	function setBlogPostCache(){
		global $blogsPostLimit,$blogsCacheExpiry;
		$blogsPostLimit=10;
		$key="blogposts";
		$qry="select title,url,author_name from blogs_posts order by publish_date desc limit ".$blogsPostLimit;
		$result=exec_query($qry);
		$this->setKey($key,$result,$blogsCacheExpiry);
		return $result;
	}

	function deleteBlogPostCache(){
		$key="blogposts";
		$this->deleteKey($key);
	}
	function getTopicSectionCache($subSection,$sectionName=NULL){
		$key="topiclatestarticle_".$subSection."_".$sectionName;
		$resLatestArticle = $this->getKey($key);
		if(!empty($resLatestArticle)){
			return 	$resLatestArticle;
		}else{
			return $this->setTopicSectionArticleCache($subSection,$sectionName);
		}

	}

	function setTopicSectionArticleCache($subSection,$sectionName){
		global $topicCacheExpiry;
		$subSectionHyphen=str_replace("-"," ",$subSection);
		$subSectionWithSlash=str_replace("-","/",$subSection);
		$key="topiclatestarticle_".$subSection."_".$sectionName;
		$qry="select section_id,name,page_id,topic_page_id from section where subsection_type = 'article' and is_active='1' and (name='".$subSectionHyphen."' or name='".$subSectionWithSlash."') order by section_id desc";
		$result=exec_query($qry,1);
		if(!empty($result)){
			$this->setKey($key,$result,$topicCacheExpiry);
		}
		return $result;

	}

	function deleteTopicSectionArticleCache($subSection,$sectionName){
		$key="topiclatestarticle_".$subSection."_".$sectionName;
		$this->deleteKey($key);
	}


	function getArticlesCountCache($qry)
	{
		$key="ArticlesCount";
		$resArticlesCount = $this->getKey($key);
		if(!empty($resArticlesCount)){
			return 	$resArticlesCount;
		}else{
			return $this->setArticlesCountCache($qry);
		}
	}

	function setArticlesCountCache($qry){
	 global $topicCacheExpiry,$dailyFeedListingCacheExpiry;
	 $key="ArticlesCount";

	 $count=getNumRowsNoLimit($qry);

	 if(!empty($result)){
			$this->setKey($key,$count,$topicCacheExpiry);
		}
		return $count;
	}

	function deleteArticlesCountCache(){
		$key="ArticlesCount";
		$this->deleteKey($key);
	}

	############  GET ALL ARTICLES DATA #############

	function getAllArticlesListingCache($start=0,$limit,$mon,$yr){
		$allArticlesListing = $this->getKey("allarticleslisting_".$start."_".$limit."_".$mon."_".$yr);
		if(!empty($allArticlesListing)){
			return 	$allArticlesListing;
		}else{
			return $this->setAllArticlesListingCache($start, $limit,$mon,$yr);
		}
	}

	function setAllArticlesListingCache($start,$limit,$mon,$yr){
		global $allArticleCacheExpiry;

		$append='';
		if($mon!='' && $yr!='' )
		{
			$append.=" WHERE (MONTH(tbl.`date`)='".$mon."' AND YEAR(tbl.`date`)='".$yr."') ";
		}

		$qryArticleListing = "SELECT * FROM (SELECT articles.id id, articles.title, contributors.name author,articles.subsection_ids,articles.contrib_id author_id, IF(articles.publish_date,articles.publish_date,articles.date) AS `date`, EIT.url  FROM articles, contributors,ex_item_meta EIT WHERE articles.contrib_id = contributors.id AND articles.approved='1' AND articles.is_live='1' AND EIT.item_id=articles.id AND EIT.item_type='1' ORDER BY date DESC LIMIT ".$start.",".$limit.") tbl  ".$append;

		$resArticlesListing=exec_query($qryArticleListing);

		if(!empty($resArticlesListing)){
			$keyName="allarticleslisting_".$start."_".$limit."_".$mon."_".$yr;
			$this->setKey($keyName,$resArticlesListing,$allArticleCacheExpiry);
		}
		return $resArticlesListing;
	}

	function deleteAllArticlesListingCache($start=0,$limit){
		$this->deleteKey("allarticleslisting".$start."_".$limit);
	}


	function getDFDisqusPostCount($itemId,$fullUrl){
		$key="dfDisqusPostCount".'_'.$itemId;
		$resDFCount = $this->getKey($key);
		if(!empty($resDFCount)){
			return $resDFCount;
		}else{
			return $this->setDFDisqusPostCount($itemId,$fullUrl);
		}

	}

	function setDFDisqusPostCount($itemId,$fullUrl){
		global $dailyFeedListingCacheExpiry;
		$objDisqus = new disqusSys();
		$key="dfDisqusPostCount".'_'.$itemId;
		$countDisqusDF=$objDisqus->getDiqusCommentCount($fullUrl);
		$commentCountDF=$countDisqusDF->posts;
		if(!empty($commentCountDF)){
			$this->setKey($key,$commentCountDF,$dailyFeedListingCacheExpiry);
		}else{
			$commentCountDF=0;
		}
		return $commentCountDF;
	}

	function getPageDetails($pageID,$topicPageID=NULL){
		$resPageDetails = $this->getKey("pageDetails_".$pageID);
		if(!empty($resPageDetails)){
			return 	$resPageDetails;
		}else{
			return $this->setPageDetails($pageID,$topicPageID);
		}

	}

	function setPageDetails($pageID,$topicPageID=NULL){
		global $D_R,$pageDetailsCacheExpiry;


		if($topicPageID=="")
		{
			$sqlPageDetails = "SELECT lp.id,s.name AS page_title FROM layout_pages AS lp LEFT JOIN section AS s ON lp.id = s.page_id
					 WHERE lp.id='".$pageID."'";
		}
		else
		{
			$sqlPageDetails = "SELECT lp.id,s.name AS page_title FROM layout_pages AS lp LEFT JOIN section AS s ON lp.id = s.topic_page_id
					 WHERE lp.id='".$pageID."'";

		}
		$resPageDetails = exec_query($sqlPageDetails,1);
		if(!empty($resPageDetails)){
			$this->setKey("pageDetails_".$pageID,$resPageDetails,$pageDetailsCacheExpiry);
		}
		return $resPageDetails;
	}

	function getLang($pageID){
		$resLang = $this->getKey("lang_".$pageID);
		if(!empty($resLang)){
			return 	$resLang;
		}else{
			return $this->setLang($pageID);
		}
	}

	function setLang($pageID){
		global $D_R,$pagelangCacheExpiry;
		$sqlLang="select EL.term,text from ex_lang EL where page_id='".$pageID."'";
		$resLang=exec_query($sqlLang);
		foreach($resLang as $id=>$value)
		{
			$lang[$value['term']]=$value['text'];
		}
		if(!empty($lang)){
			$this->setKey("lang_".$pageID,$lang,$pagelangCacheExpiry);
		}
		return $lang;
	}

	function getPageMeta(){
		global $objPage;
		$resPageMeta = $this->getKey("pageMeta_".$objPage->id);
		if(!empty($resPageMeta)){
			return 	$resPageMeta;
		}else{
			return $this->setPageMeta();
		}
	}

	function setPageMeta(){
		global $D_R,$pageMetaCacheExpiry,$objPage;
		$resPageMeta = $objPage->getMetaData();
		if(!empty($resPageMeta)){
			$this->setKey("pageMeta_".$objPage->id,$resPageMeta,$pageMetaCacheExpiry);
		}
		return $resPageMeta;
	}

	function getDFPageMeta($metatext){
		global $objPage,$topic,$source,$cid,$tid;
		$key="";
		if($topic){
			$key="topic_".$topic;
		}elseif($source){
			$key="source_".$source;
		}elseif($cid){
			$key="countib_".$cid;
		}elseif($tid){
			$key="ticker_".$tid;
		}
		$resDFPageMeta = $this->getKey("dfPageMeta_".$key.$objPage->id);
		if(!empty($resDFPageMeta)){
			return 	$resDFPageMeta;
		}else{
			return $this->setDFPageMeta($metatext);
		}
	}

	function setDFPageMeta($metatext){
		global $D_R,$pageDFMetaCacheExpiry,$objPage,$topic,$source,$cid,$tid;
		$key="";
		if($topic){
			$key="topic_".$topic;
		}elseif($source){
			$key="source_".$source;
		}elseif($cid){
			$key="countib_".$cid;
		}elseif($tid){
			$key="ticker_".$tid;
		}
		$resDFPageMeta = $objPage->getMetaDataDailyFeed($metatext);
		if(!empty($resDFPageMeta)){
			$this->setKey("dfPageMeta_".$key.$objPage->id,$resDFPageMeta,$pageDFMetaCacheExpiry);
		}
		return $resDFPageMeta;
	}

	function getCM8Cat($pageName,$topic_page_id=NULL){
		$resCM8Cat = $this->getKey("cm8cat".$topic_page_id."_".$pageName);
		if(!empty($resCM8Cat)){
			return 	$resCM8Cat;
		}else{
			return $this->setCM8Cat($pageName,$topic_page_id);
		}
	}

	function setCM8Cat($pageName,$topic_page_id){
		global $D_R,$CM8CatCacheExpiry;
		$resCM8Cat = getCheckmateCat($pageName,$topic_page_id);
		if(!empty($resCM8Cat)){
			$this->setKey("cm8cat".$topic_page_id."_".$pageName,$resCM8Cat,$CM8CatCacheExpiry);
		}
		return $resCM8Cat;
	}

	function getParentMenu($pageID){
		$resParentMenu = $this->getKey("parentMenu_".$pageID);
		if(!empty($resParentMenu)){
			return 	$resParentMenu;
		}else{
			return $this->setParentMenu($pageID);
		}
	}

	function setParentMenu($pageID){
		global $parentMenuCacheExpiry;
		$sqlGetParent="SELECT LM.title,LP.alias,LM.page_id,LM.parent_id FROM layout_menu LM,layout_pages LP
		WHERE LP.id=LM.page_id AND LM.page_id='".$pageID."'";
		$resGetParent=exec_query($sqlGetParent,1);
		if(!empty($resGetParent)){
			$this->setKey("parentMenu_".$pageID,$resGetParent,$parentMenuCacheExpiry);
		}
		return $resGetParent;
	}


	function getMostCommented(){
		$resMostCommented = $this->getKey("mostCommented");
		if(!empty($resMostCommented)){
			return 	$resMostCommented;
		}else{
			return $this->setMostCommented();
		}
	}

	function setMostCommented(){
		global $D_R,$MostCommentedCacheExpiry;
		include_once($D_R."/lib/_disqus_lib.php");
		include_once($D_R."/lib/_content_data_lib.php");
		$obj_disqus=new disqusSys();
		$forums=$obj_disqus->disqusMostComment();
		$objContent=new Content();
		foreach ($forums as $key=>$forum){
			$url=parse_url($forum->identifiers[0]);
			$contentMetaDetails=$objContent->getContentMetaFromURL($url['path']);
			$forums[$key]->author_name=$contentMetaDetails['author_name'];
		}
		if(!empty($forums)){
			$this->setKey("mostCommented",$forums,$MostCommentedCacheExpiry);
		}
		return $forums;
	}

	############# Archive MemCache ##################

	function getArchiveCache($sectionId,$month,$year)
	{
		$key="ArchiveModule_".$sectionId."_".$month."_".$year;
		$resArchiveModule = $this->getKey($key);
		if(!empty($resArchiveModule)){
			return $resArchiveModule;
		}else{
			return $this->setArchiveCache($sectionId,$month,$year);
		}
	}

	function setArchiveCache($sectionId,$month,$year){
		global $ArchiveCacheExpiry;
		$key="ArchiveModule_".$sectionId."_".$month."_".$year;

		$append.=" (MONTH(tbl.`date`)='".$month."' AND YEAR(tbl.`date`)='".$year."') ";

		if($sectionId!='articles')
		{
			$qry = "SELECT COUNT(*) 'numCount' FROM  articles  WHERE FIND_IN_SET('".$sectionId."',subsection_ids) AND approved='1' AND is_live='1' AND
(MONTH(IF(articles.publish_date,articles.publish_date,articles.date))='".$month."' AND YEAR(IF(articles.publish_date,articles.publish_date,articles.date))='".$year."')" ;
		}
		else
		{
			$qry = "SELECT COUNT(*) 'numCount' FROM  articles  WHERE approved='1' AND is_live='1'  AND
(MONTH(IF(articles.publish_date,articles.publish_date,articles.date))='".$month."' AND YEAR(IF(articles.publish_date,articles.publish_date,articles.date))='".$year."') ";
		}

		$result=exec_query($qry);
		$this->setKey($key,$result,$ArchiveCacheExpiry);
		return $result;
	}

	############# Archive MemCache ##################


	function getLayoutPageDetails($pageName,$topicPageID)
	{
		$key="LayoutPageDetails_".$pageName."_".$topicPageID;
		$resLayoutPageDetails = $this->getKey($key);
		if(!empty($resLayoutPageDetails)){
			return $resLayoutPageDetails;
		}else{
			return $this->setLayoutPageDetails($pageName,$topicPageID);
		}
	}

	function setLayoutPageDetails($pageName,$topicPageID){
		global $LayoutPageDetailsExpiry;
		$key="LayoutPageDetails_".$pageName."_".$topicPageID;

		if($topicPageID!="")
		{
			$sqlGetPageId="Select id from layout_pages where id='".$topicPageID."'";
		}
		else
		{
			$sqlGetPageId="Select id from layout_pages where name='".$pageName."' or id='".$pageName."'";
		}

		$result=exec_query($sqlGetPageId,1);
		$this->setKey($key,$result,$LayoutPageDetailsExpiry);
		return $result;
	}

	function getCacheIcTag($id)
	{
		$key="article_ictag_".$id;
		$resIcTag= $this->getKey($key);
		if(!empty($resIcTag)){
			return $resIcTag;
		}else{
			return $this->setArticleIcTag($id);
		}
	}

	function setArticleIcTag($id)
	{
		 global $D_R,$ArticleIcTagExpiry;
		 $key="article_ictag_".$id;
		 include_once $D_R.'/admin/lib/_article_data_lib.php';
		 $objArticle = new ArticleData();
		 $result = $objArticle->getIcTag($id);
		 if(!empty($result)){
			$this->setKey($key,$result['ic_tag'],$ArticleIcTagExpiry);
		}
		return $result['ic_tag'];
	}

	function getTradCalenStock()
	{
		$key="trad_calen_stock";
		$resStock= $this->getKey($key);

		if(!empty($resStock)){
			return $resStock;
		}else{
			return $this->setTradCalenStock($stock);
		}
	}

	function setTradCalenStock($stock=NULL)
	{
		global $tradCalenStockExpiry;
		$key="trad_calen_stock";
		if(empty($stock))
		{
			$stock="AAPL";
		}

		if(!empty($stock)){
			$this->setKey($key,$stock,$tradCalenStockExpiry);
		}
		return $stock;
	}

	function getIcTags()
	{
		$key="Ic_tags";
		$resIcTag= $this->getKey($key);
		if(!empty($resIcTag)){
			return $resIcTag;
		}else{
			return $this->setIcTags();
		}
	}

	function setIcTags()
	{
		global $IcTagExpiry;
		$key="Ic_tags";
		$sql = "SELECT id,ic_tag FROM ad_zone";
		$result = exec_query($sql);
		if(!empty($result)){
			$this->setKey($key,$result,$IcTagExpiry);
		}
		return $result;
	}

	function getLayoutFbMeta($page_name)
	{
		$key="LayoutFbMeta_".$page_name;
		$resLayoutFbMeta = $this->getKey($key);
		if(!empty($resLayoutFbMeta)){
			return $resLayoutFbMeta;
		}else{
			return $this->setLayoutFbMeta($page_name);
		}
	}

	function setLayoutFbMeta($page_name){
		global $LayoutFbMetaExpiry;
		$key="LayoutFbMeta_".$page_name;
		$sql = "SELECT fbmeta_type from layout_pages WHERE name ='".$page_name."'";
		$result = exec_query($sql,1);
		if(!empty($result)){
			$this->setKey($key,$result,$LayoutFbMetaExpiry);
		}
		return $result;


	}


	function getArticleContributors()
	{
		$resArticleContributors = $this->getKey('articleContibutors_');
		if(!empty($resArticleContributors)){
			return $resArticleContributors;
		}else{
			return $this->setArticleContributors();
		}
	}

	function setArticleContributors(){
		global $D_R,$ArticleContributorsCacheExpiry;
		include_once($D_R."/admin/lib/_contributor_class.php");
		$obj_contributor=new contributor();
		$articleContributorsResults=$obj_contributor->getArticleContributorsID();
		if(!empty($articleContributorsResults)){
			$this->setKey('articleContibutors_',$articleContributorsResults,$ArticleContributorsCacheExpiry);
		}
		return $articleContributorsResults;
	}

	function getSectionContributors($sectionID)
	{
		$resSectionContributors = $this->getKey('sectionContibutors_'.$sectionID);
		if(!empty($resSectionContributors)){
			return $resSectionContributors;
		}else{
			return $this->setSectionContributors($sectionID);
		}
	}

	function setSectionContributors($sectionID){
		global $D_R,$SectionContributorsCacheExpiry;
		include_once($D_R."/admin/lib/_contributor_class.php");
		$obj_contributor=new contributor();
		$contributorsResults=$obj_contributor->getContributorsID($sectionID);
		if(!empty($contributorsResults)){
			$this->setKey('sectionContibutors_'.$sectionID,$contributorsResults,$SectionContributorsCacheExpiry);
		}
		return $contributorsResults;
	}
	############ Extracting Feeds ###########

	function getArticleCommentData()
	{
		$key="ArticleCommentData";
		$resArticleCommentData = $this->getKey($key);
		if(!empty($resArticleCommentData))
		{
			return $resArticleCommentData;
		}
		else
		{
			return $this->setArticleCommentData();
		}
	}

	function setArticleCommentData(){
		global $ArticleCommentDataExpiry,$D_R,$HTHOST,$HTPFX;
		$key="ArticleCommentData";

		$qry = "SELECT a.id AS articleId,eth.id threadId , a.title AS title, a.body AS content,
 a.date AS publish_date, cs.url AS link,COUNT(ep.id) numcomment
 FROM articles a, ex_thread eth, content_seo_url cs ,ex_post ep
 WHERE a.id = eth.content_id AND a.id=cs.item_id AND ep.thread_id=eth.id AND cs.item_type='1'
 AND a.is_live='1' AND a.approved='1' AND eth.content_table='articles'
 GROUP BY ep.thread_id HAVING numcomment>0 ORDER BY a.id DESC";
		$list =exec_query($qry);
		$LBD= date("Y-m-d H:i:s",strtotime($list[0]['publish_date']));

		$result= '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:dsq="http://www.disqus.com/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:wp="http://wordpress.org/export/1.0/">
  <channel>
  <ttl>1</ttl>
  <lastBuildDate>'.$LBD.' EST</lastBuildDate>';


		foreach($list as $keyVal){
		//$content	=	mswordReplaceSpecialChars(strip_tags($key['content']));
		$content = html_entity_decode($keyVal['content'],ENT_QUOTES);
		//$title	=	mswordReplaceSpecialChars(strip_tags($key['title']));
		$title = html_entity_decode($keyVal['title'],ENT_QUOTES);
		$title=str_replace("&","&amp;",$title);


		$result.='
		<item>
			<!-- title of article -->
			<title>'.strip_tags($title).'</title>
			<!-- absolute URI to article -->
			<link>'.$HTPFX.$HTHOST.$keyVal['link'].'</link>
			<!-- thread body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
			<content:encoded><![CDATA['.$title.']]></content:encoded>
			 <!-- value used within disqus_identifier; usually internal identifier of article -->
			<dsq:thread_identifier>'.$HTPFX.$HTHOST.$keyVal['link'].'</dsq:thread_identifier>
			<!-- creation date of thread (article) -->
			<wp:post_date_gmt>'.date("Y-m-d H:i:s",strtotime($keyVal['publish_date'])).'</wp:post_date_gmt>
			<wp:comment_status>open</wp:comment_status>';

			$cmntQry = "SELECT ep.id AS cmntId, CONCAT(sub.fname,' ',sub.lname) AS cmntAuthor,sub.email AS email, ep.poster_ip AS IP, epc.body AS cmntContent, ep.approved AS approved, ep.created_on AS cmntDate FROM ex_post ep, ex_post_content epc, subscription sub,ex_thread eth WHERE ep.thread_id='".$key['threadId']."' AND ep.thread_id=eth.id AND ep.poster_id=sub.id AND ep.id = epc.post_id";
			$comment = exec_query($cmntQry);

			foreach($comment as $cmnt){
			$cmntContent	=	mswordReplaceSpecialChars(strip_tags($cmnt['cmntContent']));
			$cmntContent = html_entity_decode($cmntContent,ENT_QUOTES);

			$result.='<wp:comment>
				<!-- internal id of comment -->
				<wp:comment_id>'.$cmnt['cmntId'].'</wp:comment_id>
				<!-- author display name -->
				<wp:comment_author>'.$cmnt['cmntAuthor'].'</wp:comment_author>
				<!-- author email address -->
				<wp:comment_author_email>'.$cmnt['email'].'</wp:comment_author_email>
				<!-- author url, optional -->
				<wp:comment_author_url></wp:comment_author_url>
				<!-- author ip address -->
				<wp:comment_author_IP>'.$cmnt['IP'].'</wp:comment_author_IP>
				<!-- comment datetime -->
				<wp:comment_date_gmt>'.date("Y-m-d H:i:s",strtotime($cmnt['cmntDate'])).'</wp:comment_date_gmt>
				<!-- comment body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
				<wp:comment_content><![CDATA['.$cmntContent.']]></wp:comment_content>
				<!-- is this comment approved? 0/1 -->
				<wp:comment_approved>1</wp:comment_approved>
				<!-- parent id (match up with wp:comment_id) -->
				<wp:comment_parent>'.$keyVal['threadId'].'</wp:comment_parent>
			</wp:comment>';
			 }
		$result.='</item>';

		}
		$result.='</channel>
</rss>	';

		$this->setKey($key,$result,$ArticleCommentDataExpiry);
		return $result;
	}


	function getFeedCommentData()
	{
		$key="FeedCommentData";
		$resFeedCommentData = $this->getKey($key);
		if(!empty($resFeedCommentData))
		{
			return $resFeedCommentData;
		}
		else
		{
			return $this->setFeedCommentData();
		}
	}

	function setFeedCommentData(){
		global $FeedCommentDataExpiry,$D_R,$HTHOST,$HTPFX;
		$key="FeedCommentData";

		$qry = "SELECT df.id AS feedId,eth.id threadId , df.title AS title, df.body AS content,
IF(df.publish_date,df.publish_date,df.creation_date) AS publish_date, cs.url AS link,COUNT(ep.id) numcomment
FROM daily_feed df, ex_thread eth, content_seo_url cs,ex_post ep  WHERE
eth.content_id=df.id AND cs.item_id=df.id AND ep.thread_id=eth.id AND cs.item_type='18' AND df.is_live='1'
AND df.is_approved='1' AND is_deleted='0' AND eth.content_table='daily_feed'
 GROUP BY ep.thread_id HAVING numcomment>0 ORDER by eth.id desc";
		$list =exec_query($qry);

		$LBD= date("Y-m-d H:i:s",strtotime($list[0]['publish_date']));

		$result= '<?xml version="1.0" encoding="UTF-8"?>
		<rss version="2.0"
		  xmlns:content="http://purl.org/rss/1.0/modules/content/"
		  xmlns:dsq="http://www.disqus.com/"
		  xmlns:dc="http://purl.org/dc/elements/1.1/"
		  xmlns:wp="http://wordpress.org/export/1.0/">
		<channel>
		<ttl>1</ttl>
		  <lastBuildDate>'.$LBD.' EST</lastBuildDate>';

		foreach($list as $keyVal){
		$content	=	strip_tags($keyVal['content']);
		$content = html_entity_decode($content,ENT_QUOTES);
		$content=str_replace("&","&amp;",$content);
		$title=str_replace("&","&amp;",$keyVal['title']);

		$result.= '<item>
		<!-- title of article -->
		<title>'.strip_tags($title).'</title>
		<!-- absolute URI to article -->
		<link>'.$HTPFX.$HTHOST.$keyVal['link'].'</link>
		<!-- thread body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
		<content:encoded><![CDATA['.$title.']]></content:encoded>
		 <!-- value used within disqus_identifier; usually internal identifier of article -->
      	<dsq:thread_identifier>'.$HTPFX.$HTHOST.$keyVal['link'].'</dsq:thread_identifier>
		<!-- creation date of thread (article) -->
     	<wp:post_date_gmt>'.gmdate("Y-m-d H:i:s",strtotime($keyVal['publish_date'])).'</wp:post_date_gmt>
		<wp:comment_status>open</wp:comment_status>';

		$cmntQry = "SELECT ep.id AS cmntId, CONCAT(sub.fname,' ',sub.lname) AS cmntAuthor,sub.email AS email, ep.poster_ip AS IP, epc.body AS cmntContent, ep.approved AS approved, ep.created_on AS cmntDate FROM ex_post ep, ex_post_content epc, subscription sub,ex_thread eth WHERE ep.thread_id='".$keyVal['threadId']."' AND ep.thread_id=eth.id AND ep.poster_id=sub.id AND ep.id = epc.post_id";
		$comment = exec_query($cmntQry);

		 foreach($comment as $cmnt){
		 	$cmntContent	=	strip_tags($cmnt['cmntContent']);
		 	$cmntContent = html_entity_decode($cmntContent,ENT_QUOTES);

			$result.='  <wp:comment>
					<!-- internal id of comment -->
					<wp:comment_id>'.$cmnt['cmntId'].'</wp:comment_id>
					<!-- author display name -->
					<wp:comment_author>'.$cmnt['cmntAuthor'].'</wp:comment_author>
					<!-- author email address -->
					<wp:comment_author_email>'.$cmnt['email'].'</wp:comment_author_email>
					<!-- author url, optional -->
			        <wp:comment_author_url></wp:comment_author_url>
					<!-- author ip address -->
					<wp:comment_author_IP>'.$cmnt['IP'].'</wp:comment_author_IP>
					<!-- comment datetime -->
					<wp:comment_date_gmt>'.gmdate("Y-m-d H:i:s",strtotime($cmnt['cmntDate'])).'</wp:comment_date_gmt>
					<!-- comment body; use cdata; html allowed (though will be formatted to DISQUS specs) -->
					<wp:comment_content><![CDATA['.$cmntContent.']]></wp:comment_content>
					<!-- is this comment approved? 0/1 -->
					<wp:comment_approved>1</wp:comment_approved>
					<!-- parent id (match up with wp:comment_id) -->
					 <wp:comment_parent>0</wp:comment_parent>
			  </wp:comment>';
			  }
			$result.='</item>';
		 }

		$result.='</channel>
</rss>';

		$this->setKey($key,$result,$FeedCommentDataExpiry);
		return $result;
	}


	function getFeedPartnerData($type=NULL,$partnerId=NULL,$maxItemLimit=NULL,$currentFilter=NULL,$durationFilter=NULL,$startTimeFilter=NULL)
	{
		$keyCache="FeedPartnerData_".$partnerId."_type_".$type;
		if(!empty($maxItemLimit)){
			$keyCache.="_limit_".$maxItemLimit;
		}
		if(!empty($currentFilter)){
			$keyCache.="_curfilter_".$currentFilter;
		}
		if(!empty($startTimeFilter)){
			$keyCache.="_startTime_".$startTimeFilter;
		}
		if(!empty($durationFilter)){
			$keyCache.="_duration_".$durationFilter;
		}
		$resFeedPartnerData = $this->getKey($keyCache);
		if(!empty($resFeedPartnerData))
		{
			return $resFeedPartnerData;
		}
		else
		{
			return $this->setFeedPartnerData($type,$partnerId,$maxItemLimit,$currentFilter,$durationFilter,$startTimeFilter);
		}
	}

	function setFeedPartnerData($type,$partnerId,$maxItemLimit,$currentFilter,$durationFilter,$startTimeFilter){
		global $FeedPartnerDataExpiry,$D_R,$HTHOST,$HTPFX;
	$keyCache="FeedPartnerData_".$partnerId."_type_".$type;
		$objFeedView= new feedViewer($type,$partnerId);
		if(!empty($maxItemLimit)){
			$keyCache.="_limit_".$maxItemLimit;
		}
		if(!empty($currentFilter)){
			$keyCache.="_curfilter_".$currentFilter;
		}
		if(!empty($startTimeFilter)){
			$keyCache.="_startTime_".$startTimeFilter;
			$objFeedView->addFilter('startTime',$startTimeFilter);
		}
		if(!empty($durationFilter)){
			$keyCache.="_duration_".$durationFilter;
			$objFeedView->addFilter('durationFilter',$durationFilter);
		}
		$result=$objFeedView->showRSS($maxItemLimit,$currentFilter);
		$this->setKey($keyCache,$result,$FeedPartnerDataExpiry);
		return $result;
	}

	############ Extracting Feeds ###########


	############ Extracting RSS #############

	function getAuthorRSSData($type=NULL,$partnerId=NULL,$maxItemLimit=NULL,$tid=NULL,$authorId=NULL)
	{
		$keyCache="AuthorRSSData_".$partnerId."_".$type."_".$maxItemLimit."_".$tid."_".$authorId;
		$resAuthorRSSData = $this->getKey($keyCache);
		if(!empty($resAuthorRSSData))
		{
			return $resAuthorRSSData;
		}
		else
		{
			return $this->setAuthorRSSData($type,$partnerId,$maxItemLimit,$tid,$authorId);
		}
	}

	function setAuthorRSSData($type=NULL,$partnerId=NULL,$maxItemLimit=NULL,$tid=NULL,$authorId=NULL){
		global $AuthorRSSDataDataExpiry,$D_R,$HTHOST,$HTPFX;
		$keyCache="AuthorRSSData_".$partnerId."_".$type."_".$maxItemLimit."_".$tid."_".$authorId;

		$objFeedView= new feedViewer($type,$partnerId,$authorId);
		$result=$objFeedView->showAuthorRSS($maxItemLimit,$tid,$authorId);

		$this->setKey($keyCache,$result,$AuthorRSSDataExpiry);
		return $result;
	}

	function getMinyanFeedData($currentFilter=NULL)
	{
		$keyCache="MinyanFeedData_".$currentFilter;
		$resMinyanFeedData = $this->getKey($keyCache);
		if(!empty($resMinyanFeedData))
		{
			return $resMinyanFeedData;
		}
		else
		{
			return $this->setMinyanFeedData($currentFilter);
		}
	}

	function setMinyanFeedData($currentFilter=NULL){
		global $MinyanFeedDataExpiry,$D_R,$HTHOST,$HTPFX,$IMG_SERVER;
		$objContrib= new contributor();

		$keyCache="MinyanFeedData_".$currentFilter;

		$sql = "select articles.id id, articles.title, contributors.name author, articles.contributor, 
		contrib_id authorid, articles.publish_date, blurb,EIM.description as 'desc', body,  character_text ";
		$sql .= "from articles, contributors, ex_item_meta EIM ";
		$sql .= "where EIM.item_id=articles.id and articles.contrib_id = contributors.id and EIM.item_type=1
		 and articles.approved='1' and articles.is_live='1' and articles.date>('".mysqlNow()."' - interval 1 month)  ";

		if($currentFilter){
			$contribFilterId=$objContrib->getExcludedPartnerId();
		$sql .= " and contributors.id IN (SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
		contributor_groups CG WHERE CG.id=CGM.group_id AND CGM.contributor_id NOT IN ($contribFilterId))";
		$urlPostFix = "?from=".$currentFilter;
		}
		$sql .= "ORDER BY articles.publish_date DESC LIMIT 0,15";

		$results = exec_query($sql);

		$LBD= date("D, j M Y H:i:s",strtotime($results[0]['publish_date']));

		$result= '<rss version="2.0">
		<channel>
		<title>Minyanville.com - All Articles</title>
		<description>The Trusted Choice for the Wall Street Voice</description>
		<link>http://www.minyanville.com</link>
		<copyright>2007 Minyanville Publishing and Multimedia, LLC. All Rights Reserved</copyright>
		<ttl>1</ttl>
		<image>
    	<url>'.$IMG_SERVER.'/images/rss/mvLogo_googleNews_PNG_110x40.png</url>
   		<title>Minyanville.com - All Articles</title>
    	<link>'.$HTPFX.$HTHOST.'</link>
 		</image>
		<lastBuildDate>'.$LBD.' EST</lastBuildDate>';

		$showAd=FALSE;
		foreach ($results as $resVal) {
		$numWords = 100;
		if($resVal['desc']=="")
		{
			$articlebody=strip_tag_style($resVal['body']);
			$articlebody=replaceArticleAds($articlebody);
			$tempBody = html_entity_decode(mswordReplaceSpecialChars(strip_tags($articlebody)),ENT_QUOTES );
			$bodyArray = str_word_count($tempBody,1);
				$bodyArray = preg_split("/[\s,]+/",$tempBody);
			for($i=0;$i<$numWords;$i++) {
				$body .= $bodyArray[$i] . " ";
			}
		}
		else
		{
			$articlebody=strip_tag_style($resVal['desc']);
			$articlebody=replaceArticleAds($articlebody);
			$tempBody = html_entity_decode(mswordReplaceSpecialChars(strip_tags($articlebody)),ENT_QUOTES );
			$body =$tempBody;
		}
		$body = utf8_encode($body);
		$resVal['title']=utf8_encode($resVal['title']);

		$result.= ' <item>
        <title><![CDATA[ '.htmlentities(strip_tags($resVal['title'])).']]></title>
		<description><![CDATA['.$body.']]></description>
       	 <link>'.$HTPFX.$HTHOST.makeArticleslink($resVal['id']).$urlPostFix.'</link>
		 <author>'.$resVal['author'].'</author>
        <pubDate>'.date('D, j M Y H:i:s',strtotime($resVal['publish_date'])).' EST</pubDate>
        <guid isPermaLink="true">'.$HTPFX.$HTHOST.makeArticleslink($resVal['id']).'</guid>
	     </item>';
		$body = "";
		}

		$result.='</channel>
</rss>';
		$this->setKey($keyCache,$result,$MinyanFeedDataExpiry);
		return $result;
	}

	############ Extracting RSS #############
}
class ArticleCache extends memCacheObj
{
	function getLatestArticles()
	{
		$arItems = $this->getKey("latest_mv_articles");
		if(!empty($arItems)){
			return 	$arItems;
		}else{
			return $this->setLatestArticles();
		}
	}
	function setLatestArticles()
	{
		global $allArticleCacheExpiry;
		$keyName="latest_mv_articles";
		$qryItems = "SELECT item_id id,title,author_name author,author_id,publish_date,url,item_type FROM ex_item_meta EIT
WHERE item_type IN('1') AND is_live='1' AND publish_date > ('".date('Y-m-d',strtotime(mysqlNow()))."' - INTERVAL 2 WEEK) order by publish_date DESC";
		$resItems = exec_query($qryItems);
		if(!empty($resItems)){
			$this->setKey($keyName,$resItems,$allArticleCacheExpiry);
		}
		return $resItems;
	}
	
	function getLatestPremiumContent()
	{
		$arItems = $this->getKey("latest_premium_articles");
		if(!empty($arItems)){
			return 	$arItems;
		}else{
			return $this->setLatestPremiumContent();
		}
	}
	function setLatestPremiumContent()
	{
		global $premiumContentCacheExpiry;
		$keyName="latest_premium_articles";
		$qryItems = "SELECT EIT.item_id id,EIT.title,EIT.author_name author,EIT.author_id,EIT.publish_date,EIT.url,EIT.item_type
					FROM ex_item_premium_content EIPC LEFT JOIN ex_item_meta EIT ON EIT.item_id = EIPC.item_id AND  EIT.item_type = EIPC.item_type
					WHERE EIT.is_live='1' AND EIT.publish_date > ('".date('Y-m-d',strtotime(mysqlNow()))."' - INTERVAL 2 WEEK) order by EIT.publish_date DESC";
		$resItems = exec_query($qryItems);
		if(!empty($resItems)){
			$this->setKey($keyName,$resItems,$premiumContentCacheExpiry);
		}
		return $resItems;
	}
	function getFreeAndPremiumArticles()
	{
		$arLatestArticles = $this->getLatestArticles(); // get List of free articles
		$arLatestPremiumArticles = $this->getLatestPremiumContent(); // List of prmium content
		$arAllArticles = array_merge($arLatestArticles,$arLatestPremiumArticles);
		foreach($arAllArticles as $arArticle)
		{
			$arSortingKey[] = $arArticle['publish_date'];
		}
		if(!empty($arAllArticles) && is_array($arAllArticles))
		{
			array_multisort($arSortingKey, SORT_DESC, $arAllArticles);
		}
		return $arAllArticles;
	}
	function getLatestSectionArticles($section_id)
	{
		$arItems = $this->getKey("latest_section_articles_".$section_id);
		if(!empty($arItems)){
			return 	$arItems;
		}else{
			return $this->setLatestSectionArticles($section_id);
		}
	}
	function setLatestSectionArticles($section_id)
	{
		if($section_id)
		{
			global $topicCacheExpiry;
			$keyName= "latest_section_articles_".$section_id;

			$qryArticles = "SELECT a.id, IF(a.publish_date,a.publish_date,a.date) AS display_date,
			EIM.title,EIM.description,EIM.author_name author,EIM.author_id,EIM.publish_date,EIM.url
			 FROM articles a
			LEFT JOIN ex_item_meta AS EIM ON EIM.item_id=a.id AND EIM.item_type='1'
			WHERE a.is_live = '1' AND a.approved = '1' AND IF(a.publish_date,a.publish_date,a.date)>=('".mysqlNow()."' - INTERVAL 1 YEAR)
			AND FIND_IN_SET('".$section_id."',a.subsection_ids) ORDER BY display_date DESC LIMIT 0,31 ";

			$arArticles = exec_query($qryArticles);
			if(count($arArticles) > 0){
				$this->setKey($keyName,$arArticles,$topicCacheExpiry);
			}
			return $arArticles;
		}
	}
	function getArchiveSectionArticles($section_id,$month,$year)
	{
		$arItems = $this->getKey("archive_section_articles_".$section_id."_".$month."_".$year);
		if(!empty($arItems)){
			return 	$arItems;
		}else{
			return $this->setArchiveSectionArticles($section_id,$month,$year);
		}
	}
	function setArchiveSectionArticles($section_id,$month,$year)
	{
		global $topicCacheExpiry;
		$keyName= "archive_section_articles_".$section_id."_".$month."_".$year;
		$qryArticles = "SELECT id, IF(publish_date,publish_date,date) AS display_date FROM articles WHERE is_live = '1' AND FIND_IN_SET('".$section_id."',subsection_ids) AND MONTH(`date`)='".$month."' AND YEAR(`date`)='".$year."' ORDER BY display_date DESC LIMIT 0,31";
		$resItems = exec_query($qryArticles);
		if(count($resItems) > 0)
		{
			foreach($resItems as $arItems)
			{
				$arArticleIds[] = $arItems['id'];
			}
			$stArticles = implode(',',$arArticleIds);
			$qryArticle = "SELECT item_id id,title,description,author_name author,author_id,publish_date,url FROM ex_item_meta EIT
			WHERE item_type IN('1') and item_id IN (".$stArticles.") ORDER BY publish_date desc";
			$arArticles = exec_query($qryArticle);
		}
		if(count($arArticles) > 0){
			$this->setKey($keyName,$arArticles,$topicCacheExpiry);
		}
		return $arArticles;
	}
	
	function getArticleContent($articleUrl,$articleId,$itemType,$page){
		global $D_R;
		if($page==""){
			$page=1;
		}
		$contentKey = 'articleContent_'.$articleId.'_'.$itemType.'_'.$page;
			
		$articleHtm = $this->getKey($contentKey);
		if(!empty($articleHtm)){
			return $articleHtm;
		}else{
			return $this->setArticleContent($articleUrl,$articleId,$itemType,$page);
		}
	}
	
	function setArticleContent($articleUrl,$articleId,$itemType,$page){
		global $articleContentCacheExpiry,$D_R;
		$keyName = 'articleContent_'.$articleId.'_'.$itemType.'_'.$page;
		
		$this->setKey("cacheRequest",'1',$articleContentCacheExpiry);
		$resArticleHtm = stripslashes(file_get_contents($articleUrl,1));
		if(!empty($resArticleHtm)){
			$this->setKey($keyName,$resArticleHtm,$articleContentCacheExpiry);
			$this->setKey("cacheRequest",'0',$articleContentCacheExpiry);
		}
		return $resArticleHtm;
	}
	
}
class DailyFeedCache extends memCacheObj
{
	function getLatestDailyfeed()
	{
		$arItems = $this->getKey("latest_mv_df");
		if(!empty($arItems)){
			return 	$arItems;
		}else{
			return $this->setLatestDailyfeed();
		}
	}
	function setLatestDailyfeed()
	{
		global $allArticleCacheExpiry;
		$keyName="latest_mv_df";
		$qryItems = "SELECT item_id id,title,author_name author,author_id,publish_date,url,item_type FROM ex_item_meta EIT
WHERE item_type IN('18') AND is_live='1' AND publish_date > ('".date('Y-m-d',strtotime(mysqlNow()))."' - INTERVAL 2 WEEK) order by publish_date DESC";
		$resItems = exec_query($qryItems);
		if(!empty($resItems)){
			$this->setKey($keyName,$resItems,$allArticleCacheExpiry);
		}
		return $resItems;
	}
}
?>