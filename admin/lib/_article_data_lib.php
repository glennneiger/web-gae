<?
class ArticleData{

	function getArticleDetail(){
		$qry = "SELECT * FROM articles AS a WHERE a.id=".$this->id;
		$arArticle = exec_query($qry,1);
		foreach ($arArticle as $akey => $aval) {
			$this -> {$akey} = $aval;
		}
		$this->getArticleTags();
	}

	function prepareFormData($id)
	{
		$this->id=$id;
		$this->getArticleDetail($id);
		foreach ($this as $key => $val)
		{
			switch($key)
			{
				case "email_category_ids":
					$arArticle['email_category_ids'] =  explode(',',$val);
				break;
				case "subsection_ids":
					$arSectionSubsection = $this->getSectionSubSection($val);
					foreach ($arSectionSubsection as $arValue)
					{
						if($arValue['type'] == 'section')
						{
							$arArticle['section_ids'][] = $arValue['section_id'];
						}
						else
						{
							$arArticle['subsection_ids'][] = $arValue['section_id'];
						}
					}
				break;
				case "keyword":
					$arArticle[$key]= str_replace("-",",",$val);
				break;

				default:
				$arArticle[$key] = $val;
				break;
			}
		}
		if($arArticle['layout_type'] == 'live-blog')
		{

			$articleCoverData = $this->getArticleCoverLive();
			$this->cover_it_live			=	$articleCoverData['cover_it_live'];
			$this->contr_live_login_desc	=	$articleCoverData['contr_live_login_desc'];
			$arArticle['cover_it_live']			=	$this->cover_it_live;
			$arArticle['contr_live_login_desc']	=	$this->contr_live_login_desc;
		}

		$this->getArticleTags();
		$GNData	 =	$this->getGoogleNewsSynd($id);
		$arArticle['yahoo_full_body'] =	$this->getYahooFullBodySynd($id);
		if(!empty($GNData)){
			foreach($GNData as $key=>$val){
				$arArticle[$key]=$val;
			}

		}
		$arArticle['is_dailyfinance']= $this->getAolDailyFinance();
		$arArticle['is_nasdaqFeed']= $this->getNasdaqSynd();
		$arArticle['tag']= implode(",",$this->tags);
		$articlePageData=$this->getArticlePages();
		$audio = $this->getArticleAudio($this->id);
		if(!empty($audio))
		{
			foreach($audio as $aud){
				if($aud['item_key']=='audiofile'){
					$strPos = strpos($aud['item_value'],'audio/');
					$arArticle['audioFile'] = substr($aud['item_value'],$strPos+6);



				}
			}
		}
		$arArticle['total_pages']=count($articlePageData)>0?count($articlePageData):'1';
		unset($arArticle['body']);
		if(is_array($articlePageData)){
		foreach($articlePageData as $page){
			$arPages[]=$page['body'];
		}
		$arArticle['body'] = implode('{PAGE_BREAK}',$arPages);
		}

		return $arArticle;
	}

	function getYahooFullBodySynd($id)
	{
		$qryYahooVal="SELECT is_yahoo_full_body FROM content_syndication WHERE item_id='".$id."' and item_type='1' and syndication_channel='yahoo_full_body'";
		$resYahooFullVal=exec_query($qryYahooVal,1);
		if(!empty($resYahooFullVal))
		{
			return $resYahooFullVal['is_yahoo_full_body'];
		}else{
			return false;
		}
	}
	function getIcTag($id)
	{
	 	$getSql="SELECT AZ.ic_tag,AM.item_value FROM `article_meta` AM
LEFT JOIN `ad_zone` AZ ON AZ.id=AM.item_value
WHERE `item_key`='ad_zone' AND `item_type`='1'
 AND `item_id`='".$id."'";
		$getResult=exec_query($getSql,1);
		return $getResult;

	}

	function unlockArticle($id)
	{
		$qry = "SELECT CL.id FROM content_locking as CL WHERE item_id=".$id." AND item_type = 1";
		$res = exec_query($qry,1);
		$delRes = del_query('content_locking',id,$res['id']);
		return $delRes;
	}

	function restoreFormData($aid){
		global $D_R;
		$qry="SELECT * FROM articles_draft WHERE admin_id='".$aid."'";
		$arArticle=exec_query($qry);

		$articleData['id']=$arArticle[0]['id'];
		$artObj=new ArticleData();
		include_once($D_R."/admin/lib/_contributor_class.php");
		$objContributor=new contributor();
		$arrContributorDetails=$objContributor->getContributor($arArticle[0]['contrib_id']);

		$articleData['contributor']=stripslashes($arrContributorDetails['name']);

		$articleData['seo_title']=stripslashes($arArticle[0]['seo_title']);
		$articleData['title']=stripslashes($arArticle[0]['title']);
		$articleData['blurb']=stripslashes($arArticle[0]['blurb']);
		$articleData['character_text']=stripslashes($arArticle[0]['character_text']);
		$articleData['position']=stripslashes($arArticle[0]['position']);
		$articleData['note']=stripslashes($arArticle[0]['note']);
		$articleData['contrib_id']=$arArticle[0]['contrib_id'];
		$articleData['date']=$arArticle[0]['date'];
		$articleData['navigation_section_id']=$arArticle[0]['navigation_section_id'];
		$articleData['email_category_ids'] =  explode(',',$arArticle[0]['email_category_ids']);

		$arSectionSubsection = $artObj->getSectionSubSection($arArticle[0]['subsection_ids']);
		foreach ($arSectionSubsection as $arValue)
		{
			if($arValue['type'] == 'section')
			{
				$articleData['section_ids'][] = $arValue['section_id'];
			}
			else
			{
				$articleData['subsection_ids'][] = $arValue['section_id'];
			}
		}

		$articleData['keyword']= str_replace("-",",",$arArticle[0]['keyword']);


		$articleData['layout_type']=$arArticle[0][layout_type];
		$articleData['editor_note']=$arArticle[0][editor_note];
		$articleData['featureimage']=$arArticle[0][featureimage];
		$articleData['is_featured']=($arArticle[0][is_featured]=="1")?1:0;
		$articleData['is_buzzalert']=($arArticle[0][is_buzzalert]=="1")?1:0;
		$articleData['hosted_by']=$arArticle[0][hosted_by];
		$articleData['is_yahoofeed']=($arArticle[0][is_yahoofeed]=="1")?1:0;

		$articleData['sent']=($arArticle[0][sent])?1:0;
		$articleData['is_live']=$arArticle[0]['is_live'];
		// syndication check for street articles
		if($articleData['layout_type']== 'thestreet' || $articleData['layout_type']== 'realmoneysilver' || $articleData['layout_type']== 'observer')
		{
			$articleData['is_yahoofeed']=0;
		}

		$articleData['body']=stripslashes($arArticle[0]['body']);
		$articleData['tag']=stripslashes(trim($arArticle[0]['tags']));
		$articleData['keyword']=stripslashes(trim($arArticle[0]['keyword']));



		if($articleData['layout_type']== 'live-blog')
		{
			$articleData['cover_it_live']=	stripslashes(trim($arArticle[0]['cover_it_live']));
			$articleData['contr_live_login_desc']=	trim($arArticle[0]['contr_live_login_desc']);
		}

		/*Google Editor's Pick*/
		if($arArticle[0]['google_editors_pick'])
		$articleData['google_editors_pick']=	trim($arArticle[0]['google_editors_pick']);

		/*Google News Standout*/
		if($arArticle[0]['google_news_standout'])
		$articleData['google_news_standout']=	trim($arArticle[0]['google_news_standout']);

		if($arArticle[0]['streetfeed_type'])
		$articleData['streetfeed_type']=	trim($arArticle[0]['streetfeed_type']);



		//$arrFeed['source_link'] = $sourceURL['source_link'];
		return $articleData;
	}

	function getGoogleNewsSynd($articleID)
	{
		if($articleID)
		{
			$sqlArticleGN="select is_syndicated,syndication_channel from content_syndication where item_id='".$articleID."' and item_type='1' and is_syndicated='1' and (syndication_channel='google_editors_pick' or syndication_channel='google_news_standout' or syndication_channel='headline_syndicate' or syndication_channel='aafb_syndicate' )";

			$resArticleGN=exec_query($sqlArticleGN);

			if(!empty($resArticleGN))
			{
				foreach($resArticleGN as $key=>$val)
				{
					$synChannel[$val['syndication_channel']]=$val['is_syndicated'];
				}
			}

			if($resArticleGN && count($resArticleGN)>0)
			{
				return $synChannel;
			}
			else
			{
				return NULL;
			}
		}
	}

	function getArticleCoverLive()
	{
		if($this->id)
		{
			 $sqlArticleCoverScript="select cover_it_live,contr_live_login_desc from article_coveritlive where article_id=".$this->id;
			$resArticleCoverScript=exec_query($sqlArticleCoverScript);
			if($resArticleCoverScript)
			{
				return $resArticleCoverScript[0];
			}
		}
		else
		{
				return NULL;
		}
	}
	function getArticleTags(){
		if($this->id){
			$sqlGetArticleTags="select ET.tag,ET.id from ex_tags ET,ex_item_tags EIT
			where ET.id=EIT.tag_id and EIT.item_type='1' and EIT.item_id='".$this->id."'";
			$resGetArticleTags=exec_query($sqlGetArticleTags);
			$arTags = array();
			foreach($resGetArticleTags as $arTag)
			{
				$arTags[] = $arTag['tag'];
			}
			$this->tags=$arTags;
		}
		else
			return NULL;
	}

	function getSectionSubSection($stIds){

		$stIds = implode("','",explode(',',$stIds));
		$qry = "SELECT section_id,type FROM section where section_id IN('".$stIds."')";
		$res = exec_query($qry);
		return $res;
	}

	function getArticlePages(){

			$qry="select body,page_no,updated_date from article_revision where article_id='".$this->id."' and revision_number=(select max(revision_number) from article_revision where article_id='".$this->id."') order by page_no";
			$qrydata=exec_query($qry);
			if($qrydata){
			  return $qrydata;
			}

	}

	function setArticleBitlyUrl($articleid,$type_id,$url)
	{
		global $_SESSION,$D_R;
		$bitlyurl[bitly_url] = $url;
		update_query("ex_item_meta",$bitlyurl,array("item_id"=>$articleid));
	}

	function setArticle($id=NULL){

		global $_SESSION,$D_R,$VIDEO_SERVER;
		include_once($D_R.'/lib/_exchange_lib.php');
		include_once("$D_R/lib/_content_data_lib.php");
		$article=get_object_vars($this);

		$audioFile = $article['audioFile'];
		unset($article['yahoo_full_body']);
		unset($article['headline_syndicate']);
		unset($article['aafb_syndicate']);
		unset($article['is_nasdaqFeed']);
		unset($article['pages']);
		unset($article['tags']);
		unset($article['canonical_url']);
		unset($article['keywords']);
		unset($article['tickers']);
		unset($article['cover_it_live']);
		unset($article['contr_live_login_desc']);
		unset($article['streetfeed_type']);
		unset($article['google_editors_pick']);
		unset($article['google_news_standout']);
		unset($article['audioFile']);

		if(is_array($this->keywords)){
			$article['keyword']=implode("-",$this->keywords);
		}
		if(empty($id)){
			$this->id=insert_query("articles",$article);
		}else{
			$this->id=$id;
			update_query("articles",$article,array("id"=>$id));
		}
		if(!empty($this->id) && $audioFile!="0"){
			if($audioFile!="")
			{
				$article_meta['item_id'] = $this->id;
			 	$article_meta['item_type'] = "1";
			 	$article_meta['item_key'] = "audiofile";
			 	$article_meta['item_value'] = $VIDEO_SERVER."assets/audio/".$audioFile;
 				insert_or_update("article_meta",$article_meta,array("item_id"=>$this->id,"item_key"=>'audiofile',"item_type"=>'1'));
			}
			else
			{
				$del_qry = "DELETE FROM article_meta WHERE item_id='".$this->id."' AND item_key='audiofile'; ";
                $result=exec_query($del_qry);
			}
		}
		if(!$this->id){
			$this->error=1;
			$this->errorMsg="There is an error while saving your article. <br />Here are the details of the error:<br>".mysql_error();
			return;
		}
		$lastPageRevision=$this->getArticlePageRevision();
		if(is_array($this->pages)){
			foreach($this->pages as $page_no=>$page){

				$articleRevision['article_id']=$this->id;
				$articleRevision['page_no']=$page_no+1;
				$articleRevision['revision_number']=$lastPageRevision+1;
				$articleRevision['body']=$page;
				$articleRevision['updated_by']=$_SESSION[AUSER];
				$articleRevision['updated_date']=mysqlNow();
				//htmlprint_r($articleRevision);
				if($articleRevision['body']!=""){
					$revision_id=insert_query("article_revision",$articleRevision);
				}
				if(empty($revision_id))
				{
					$this->error=1;
					$this->errorMsg="There was an error saving different pages. <br />Here are the details of the error:<br />".mysql_error();
					return;
				}
			}
		}
		$this->setArticleTags();
		if($this->layout_type == 'live-blog')
		{
			$this->setCoverItLive();
		}

		$this->setGoogleSynd();
	    $this->setAolDailyFinanceSynd();
	    $this->setNasdaqSynd();
		$obContent = new Content(1,$this->id);
		$obContent->setArticleMeta();
		if($_POST['articles']['ic_tag']!="")
		{
			$obContent->setIcTagMeta($this->id,$_POST['articles']['ic_tag']);
		}
		//create thread object
				$objThread = new Thread();
				$threadId=$objThread->post_thread($this->id);
				if(empty($threadId)){
					$this->error=1;
					$this->errorMsg="There was an error while creating the discussion thread from this artcle. <br />Here are the details of the error:<br />".mysql_error();
					return;
		}
		if($this->is_live == 1) // Update cache only if its a live article
		{
		include_once $D_R.'/lib/_action.php';
		$objAction= new Action();
		$objAction->trigger('articleUpdate',$this->id);
		}
		$this->errorMsg="Article has been saved";
		return;
	}

    function getArticleUrl($id){
		$qry="select url from ex_item_meta where item_id='".$id."' and item_type='1'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	  }

	function setGoogleSynd()
	{
		$GNArticle['item_id']	= $this->id;
		$GNArticle['item_type']	='1';

		$GNArticle['syndicated_on'] = date("Y-m-d H:i:s");
		$GNArticle['is_hosted_by_minyanville']	= '1';
		$GNArticle['syndication_updated_on'] = date("Y-m-d H:i:s");

		if($this->google_editors_pick =="1")
		{
			$GNArticle['syndication_channel']	= 'google_editors_pick';
			$GNArticle['is_syndicated'] = $this->google_editors_pick;

			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_editors_pick','item_id'=>$this->id,'item_type'=>'1'));

		}
		else
		{
			if(empty($this->google_editors_pick))
			{
				$GNArticle['syndication_channel']	= 'google_editors_pick';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_editors_pick','item_id'=>$this->id,'item_type'=>'1'));
			}
		}

		if($this->google_news_standout =="1")
		{
			$GNArticle['syndication_channel']	= 'google_news_standout';
			$GNArticle['is_syndicated'] = $this->google_news_standout;
			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_news_standout','item_id'=>$this->id,'item_type'=>'1'));
		}
		else
		{
			if(empty($this->google_news_standout))
			{
				$GNArticle['syndication_channel']	= 'google_news_standout';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_news_standout','item_id'=>$this->id,'item_type'=>'1'));
			}
		}

		if($this->headline_syndicate =="1")
		{
			$GNArticle['syndication_channel']	= 'headline_syndicate';
			$GNArticle['is_syndicated'] = $this->headline_syndicate;
			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'headline_syndicate','item_id'=>$this->id,'item_type'=>'1'));
		}
		else
		{
			if(empty($this->headline_syndicate))
			{
				$GNArticle['syndication_channel']	= 'headline_syndicate';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'headline_syndicate','item_id'=>$this->id,'item_type'=>'1'));
			}
		}

		if($this->aafb_syndicate =="1")
		{
			$GNArticle['syndication_channel']	= 'aafb_syndicate';
			$GNArticle['is_syndicated'] = $this->aafb_syndicate;
			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'aafb_syndicate','item_id'=>$this->id,'item_type'=>'1'));
		}
		else
		{
			if(empty($this->aafb_syndicate))
			{
				$GNArticle['syndication_channel']	= 'aafb_syndicate';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'aafb_syndicate','item_id'=>$this->id,'item_type'=>'1'));
			}
		}

		if($this->yahoo_full_body =="1")
		{
			$GNArticle['syndication_channel']	= 'yahoo_full_body';
			$GNArticle['is_syndicated'] = '0';
			$GNArticle['is_yahoo_full_body'] = $this->yahoo_full_body;
			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'yahoo_full_body','item_id'=>$this->id,'item_type'=>'1'));
		}
		else
		{
			if(empty($this->yahoo_full_body))
			{
				$GNArticle['syndication_channel']	= 'yahoo_full_body';
				$GNArticle['is_syndicated'] = '0';
				$GNArticle['is_yahoo_full_body']='0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'yahoo_full_body','item_id'=>$this->id,'item_type'=>'1'));
			}
		}
	}

	function setCoverItLive()
	{
		$sqlIsNew="SELECT id FROM article_coveritlive WHERE article_id='".$this->id."'";
		$resIsNew=exec_query($sqlIsNew,1);
		if(count($resIsNew)>0)
		{
			$liveArticle['cover_it_live']= $this->cover_it_live;
			$liveArticle['contr_live_login_desc']= $this->contr_live_login_desc;
			update_query("article_coveritlive",$liveArticle,array('article_id' => $this->id));
		}
		else
		{
			$liveArticle['cover_it_live']= $this->cover_it_live;
			$liveArticle['contr_live_login_desc']= $this->contr_live_login_desc;
			$liveArticle['article_id']= $this->id;
			insert_query("article_coveritlive",$liveArticle);
		}

	}
	function setArticleTags(){
		$sqlDelTags="delete from ex_item_tags where item_id='".$this->id."' and item_type='1'";
		$resDelTags=exec_query($sqlDelTags);
		foreach($this->tags as $tag){
			if(trim($tag)=="")
					continue;
			$sqlGetTag="SELECT id from ex_tags where tag='".trim($tag)."'";
			$resGetTag=exec_query($sqlGetTag,1);
			if(empty($resGetTag['id'])){
				$isStock=is_stock(trim($tag));
				$tagId=insert_query("ex_tags",array("tag"=>trim($tag),"type_id"=>count($isStock)));

			}else{
				$tagId=$resGetTag['id'];
			}
			$exItemTag['tag_id']=$tagId;
			$exItemTag['item_id']=$this->id;
			$exItemTag['item_type']='1';
			insert_query("ex_item_tags",$exItemTag);
		}
	}

	function getArticlePageRevision(){
		$sqlGetPageRevision="SELECT MAX(revision_number) number FROM article_revision WHERE article_id='".$this->id."'";
		$resGetPageRevision=exec_query($sqlGetPageRevision,1);
		if(empty($resGetPageRevision['number']))
			return 0;
		else
			return $resGetPageRevision['number'];

	}

	function approve(){
	}

	function disapprove(){
	}

	function syndicate(){
	}

	function prepareArticleData($data){
		global $D_R;
		include_once($D_R."/admin/lib/_contributor_class.php");
		$objContributor=new contributor();
		$arrContributorDetails=$objContributor->getContributor($data['contrib_id']);
		$this->contributor=addslashes(mswordReplaceSpecialChars(stripslashes($arrContributorDetails['name'])));
		if(empty($data['contrib_id'])){
			$this->error=1;
			$this->errorMsg="Contributor is mandatory.";
			return;
		}
		if(($data['layout_type'] == 'live-blog') && ($data['cover_it_live']=='')){
			$this->error=1;
			$this->errorMsg="Live Blog Script is mandatory for Live Blog Layout.";
			return;
		}
		$this->seo_title=addslashes(mswordReplaceSpecialChars(stripslashes($data[seo_title])));
		$this->title=addslashes(mswordReplaceSpecialChars(stripslashes($data[title])));
		$this->blurb=addslashes(mswordReplaceSpecialChars(stripslashes($data[blurb])));
		$this->character_text=addslashes(mswordReplaceSpecialChars(stripslashes($data[character_text])));
	
		$this->position=addslashes(mswordReplaceSpecialChars(stripslashes($data[position])));
		$this->note=addslashes(mswordReplaceSpecialChars(stripslashes($data[note])));

		$this->contrib_id=$data['contrib_id'];
		$this->date=$data['date'];
		if(is_array($data[email_category_ids])){
			$this->email_category_ids=implode(",",$data[email_category_ids]);
		}else{
			$this->email_category_ids=$data[email_category_ids];
		}
		if($data[subsection_ids]){
			$this->subsection_ids=implode(",",$data[subsection_ids]);
		}else{
			$this->subsection_ids=$data['navigation_section_id'][0];
		}
		$this->navigation_section_id=$data['navigation_section_id'][0];
		$sectionId=$this->getArticleSectionDetail($this->navigation_section_id);
		$this->subsection_ids=$this->subsection_ids.','.$sectionId['parent_section'].','.$this->navigation_section_id;
		$this->section_id=$sectionId['parent_section'];
		$this->layout_type=$data[layout_type];
		if($data['layout_type']=="radio" || (!empty($data['audioFile'])))
		{
			$this->is_audio="1";
		}
		else
		{
			$this->is_audio="0";
		}
		$this->editor_note=$data[editor_note];
		$this->featureimage=$data[featureimage];
		$this->is_featured=($data[is_featured]=="1")?1:0;
		$this->is_buzzalert=($data[is_buzzalert]=="1")?1:0;
		$this->hosted_by=$data[hosted_by];
		$this->yahoo_full_body = ($data[yahoo_full_body]=="1")?1:0;
		$this->is_yahoofeed=($data[is_yahoofeed]=="1")?1:0;
		$this->no_follow_tag=($data[no_follow_tag]=="1")?1:0;
		$this->is_dailyfinance=($data[is_dailyfinance]=="1")?1:0;
		$this->stop_autorefresh=($data[stop_autorefresh]=="1")?1:0;
		$this->is_nasdaqFeed=($data[is_nasdaqFeed]=="1")?1:0;
	 	$this->headline_syndicate=($data[headline_syndicate]=="1")?1:0;
		$this->aafb_syndicate=($data[aafb_syndicate]=="1")?1:0;
		$this->sent=($data[sent])?1:0;
		$this->is_live=$data['is_live'];
		// syndication check for street articles
		if($this->layout_type == 'thestreet' || $this->layout_type == 'realmoneysilver' || $this->layout_type == 'observer')
		{
			$this->is_yahoofeed=0;
		}
		$this->pages = explode('{PAGE_BREAK}',$data['body']);
		$this->body=implode("<br/><br/>",$this->pages);
		$this->tags=explode(",",addslashes(mswordReplaceSpecialChars(stripslashes(trim($data['tag'])))));
		$this->keywords=explode(",",addslashes(mswordReplaceSpecialChars(stripslashes(trim($data['keyword'])))));
		if($this->layout_type == 'live-blog')
		{
			$this->cover_it_live			=	addslashes(mswordReplaceSpecialChars(stripslashes(trim($data['cover_it_live']))));
			$this->contr_live_login_desc 	=	trim($data['contr_live_login_desc']);
		}

		/*Google Editor's Pick*/
		$this->google_editors_pick	=	trim($data['google_editors_pick']);

		/*Google News Standout*/
		$this->google_news_standout	=	trim($data['google_news_standout']);

		$this->streetfeed_type	=	trim($data['streetfeed_type']);

		$this->is_nasdaqFeed = trim($data['is_nasdaqFeed']);

		$this->getArticleTickers();
		$this->audioFile = $data[audioFile];
		if($this->is_yahoofeed && count($this->tickers)<1){
			$this->error=1;
			$this->errorMsg="There is an error in saving your data, Please associate atleast one ticker with the article to syndicate it to yahoo finance.";
				include_once($D_R."/admin/lib/_admin_data_lib.php");
				$typeEmail="Article";
				$titleEmail=$this->title;
				$objBuzz= new Buzz();
				$objBuzz->sendYahooInvalidTickerEmail($titleEmail,$typeEmail);
		}
	}

	function getArticleTickers(){
		$tickers=make_stock_array(strip_tags($this->body));
		foreach($this->tags as $tag){
			if(is_stock($tag)){
				array_push($tickers,$tag);
			}
		}
		foreach($this->keywords as $keyword){
			if(is_stock($keyword)){
				array_push($tickers,$keyword);
			}
		}
		$this->tickers=array_unique($tickers);
	}
	function getAuthorAtricles($contributor_id,$limit)
	{
		$articlelist = "SELECT a.category_ids, a.subsection_ids,DATE_FORMAT(a.date,'%m/%d/%Y %r')date,
			a.id,a.title,c.name,a.contrib_id,a.note,a.body,a.contributor
			FROM articles a,contributors c
			WHERE a.contrib_id=c.id and c.id=$contributor_id ORDER BY a.date DESC limit 0,".$limit;
		return exec_query($articlelist);

	}
	function getTagsOnArticles($aid,$type)
	{
		$sqlrticleTags="select ET.tag,ET.id from ex_tags ET,ex_item_tags EIT where ET.id=EIT.tag_id and EIT.item_type='".$type."' and EIT.item_id='".$aid."'";
		$resArticleTags=exec_query($sqlrticleTags);
		if($resArticleTags && count($resArticleTags)>0)
		{
			return $resArticleTags;
		}
		else
		{
			return false;
		}
	}
	function getEditorNote()
	{
		$sql="SELECT id,title, text from editor_notes order by title asc";
		$res=exec_query($sql);
		$arNote = array();
		foreach($res as $key => $row)
		{
			$arNote[$key]['title'] = $row['title'];
			$arNote[$key]['id'] = $row['id'];
			$arNote[$key]['text'] = htmlentities($row['text']);
		}
		return $arNote;

	}

	function getAllAricles($ID,$start,$end)
	   {
	      if (empty($ID))
		      {

	     $results = getArticleListing($start,$end);
	          }
		  else
		      {
 $sql ="SELECT articles.id id, articles.title, contributors.name author, articles.contributor, section.name section, section.section_id,articles.subsection_ids, articles.position, contrib_id author_id,
IF(articles.publish_date,articles.publish_date,articles.date) AS date, EIT.url, body, POSITION, character_text FROM articles, section, contributors,ex_item_meta EIT WHERE articles.contrib_id = contributors.id AND FIND_IN_SET('" .$ID ."',articles.subsection_ids) AND articles.approved='1' AND articles.is_live='1' AND section.section_id = '" .$ID ."' AND EIT.item_id=articles.id AND EIT.item_type='1' ORDER BY date DESC LIMIT ".$start.",".$end;
		$results = exec_query($sql);
	           }
	      return $results;
	   }


	 function getLatestAricles($ID)
	   {
	      if (empty($ID))
		      {
		 $sql="SELECT A.id, A.title,A.body, C.NAME author, EIT.author_id, IF(A.publish_date,A.publish_date,A.DATE) AS DATE, EIT.url,EIT.item_type FROM articles A, contributors  C,ex_item_meta EIT WHERE A.contrib_id = C.id AND A.approved='1' AND A.is_live='1' AND EIT.item_type='1' AND A.id = EIT.item_id AND EIT.publish_date>('".mysqlNow()."' - INTERVAL 1 MONTH) ORDER BY DATE DESC LIMIT 0,1";
		 $results = exec_query($sql);

	          }
		  else
		      {
 $sql ="SELECT articles.id id,articles.body body, articles.title, contributors.name author, articles.contributor, section.name section, section.section_id,articles.subsection_ids, articles.position, contrib_id author_id,
IF(articles.publish_date,articles.publish_date,articles.date) AS date, EIT.url, body, POSITION, character_text FROM articles, section, contributors,ex_item_meta EIT WHERE articles.contrib_id = contributors.id AND FIND_IN_SET('" .$ID ."',articles.subsection_ids) AND articles.approved='1' AND articles.is_live='1' AND section.section_id = '" .$ID ."' AND EIT.item_id=articles.id AND EIT.item_type='1' ORDER BY date DESC LIMIT 0,1";
		$results = exec_query($sql);
	           }
	      return $results;
	   }

	 function getMostUsedTicker($ID)
	   {

	  $sql ="select ET.tag,A.id, count(ET.tag) counttag from articles A, ex_item_tags EIT, ex_tags ET where EIT.item_type='1' and EIT.item_id=A.id and ET.type_id='1' and FIND_IN_SET('".$ID."', A.subsection_ids) and EIT.tag_id= ET.id group by ET.tag order by counttag desc limit 12
";
		$results = exec_query($sql);
		 return $results;
	   }

	   function getArticleSubSectionID($subSection){
           $subSectionHyphen=str_replace("-"," ",$subSection);
			$subSectionWithSlash=str_replace("-","/",$subSection);
			$qry="select section_id,name,page_id,topic_page_id from section where subsection_type = 'article' and type='subsection' and is_active='1' and name='".$subSectionHyphen."' or name ='".$subSectionWithSlash."' ";
			$result=exec_query($qry,1);
			if($result){
				return $result;
			}else{
				return false;
			}
	   }

	   function getLatestArticleforSubSection($sectionId){

       if($_REQUEST['y']!="" && $_REQUEST['m']!="")
		 {
		   $appQry="and MONTH(articles.DATE)='".$_REQUEST['m']."' AND YEAR(articles.DATE)='".$_REQUEST['y']."'";
		 }
	   		$latest_sql = "select articles.id id, articles.title,articles.character_text, contributors.name author, articles.contributor,
contrib_id authorid, IF(publish_date,publish_date,date) as date from articles, contributors where
articles.contrib_id = contributors.id and FIND_IN_SET('" .$sectionId ."', articles.subsection_ids)
and articles.approved='1' and articles.is_live='1' $appQry ORDER BY date DESC LIMIT 0,1 ";
		$arLatest = exec_query($latest_sql,1);
		if($arLatest){
			return $arLatest;
		}else{
			return false;
		}
}

	   function getArticleSubsectionListing($sectionId){
	     if($_REQUEST['y']!="" && $_REQUEST['m']!="")
		 {
		   $appQry="and MONTH(articles.DATE)='".$_REQUEST['m']."' AND YEAR(articles.DATE)='".$_REQUEST['y']."'";
		 }
	   		$sql = "select articles.id id, articles.title, contributors.name author, articles.contributor,
contrib_id authorid, IF(publish_date,publish_date,date) as date from articles, contributors where
articles.contrib_id = contributors.id and FIND_IN_SET('" .$sectionId ."', articles.subsection_ids)
and articles.approved='1' and articles.is_live='1' $appQry ORDER BY date DESC limit 1,20";
		$results = exec_query($sql);
		if(is_array($results)){
			return $results;
		}else{
			return false;
		}
	   }

	   function getArticleSectionDetail($sectionId){
			$qry="select name,parent_section,page_id from section where section_id=".$sectionId;
			$result=exec_query($qry,1);
			if($result){
				return $result;
			}else{
				return false;
			}
		}

		function showNavigationListCombo($data,$selectedVal){
			if(is_array($data)){
				foreach($data as $row){
					if($row['parent_section']=='49')
					{
						$marketSubSec[$row['section_id']]=$row['name'];
					}else if($row['parent_section']=='50'){
						$investingSubSec[$row['section_id']]=$row['name'];
					}else if($row['parent_section']=='51'){
						$specilFeaturesSubSec[$row['section_id']]=$row['name'];
					}else if($row['parent_section']=='90'){
						$sectorSubSec[$row['section_id']]=$row['name'];
					}
				}
				?>
				<option value='49' disabled="disabled">Business News</option>
				<?php
				foreach($marketSubSec as $key=>$val){
			  		$sel=($selectedVal==$key?" selected":"");
					?>
<option value="<?=$key?>" <?=$sel?>>&nbsp;&nbsp;<?=$val?></option>
<?
	} ?>
	<option value='50' disabled="disabled">Trading and Investing</option>
				<?php
				foreach($investingSubSec as $key=>$val){
			  		$sel=($selectedVal==$key?" selected":"");
					?>
<option value="<?=$key?>" <?=$sel?>>&nbsp;&nbsp;<?=$val?></option>
<?
	} ?>

	<option value='90' disabled="disabled">Sectors</option>
				<?php
				foreach($sectorSubSec as $key=>$val){
			  		$sel=($selectedVal==$key?" selected":"");
					?>
<option value="<?=$key?>" <?=$sel?>>&nbsp;&nbsp;<?=$val?></option>
<?
	} ?>


	<option value='51' disabled="disabled">Special Features</option>
				<?php
				foreach($specilFeaturesSubSec as $key=>$val){
			  		$sel=($selectedVal==$key?" selected":"");
					?>
<option value="<?=$key?>" <?=$sel?>>&nbsp;&nbsp;<?=$val?></option>
<?
	} ?>
	<?php

			}
		}

function getTodaysArticleData(){

			$sqlGetTodayArticle="SELECT item_id,title,description,author_name,url
			FROM ex_item_meta EIM
			WHERE is_live='1' AND item_type='1' AND DATE_FORMAT(publish_date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y'))";
			$resGetTodayArticle=exec_query($sqlGetTodayArticle);
			return $resGetTodayArticle;
		}
function addTodaysDailyDigest($arr){
			 $daily_digest['item_ids']=$arr['sel_article'];
			 $daily_digest['module1_detail']=addslashes($arr['module1_detail']);
			 $daily_digest['module2_detail']=addslashes($arr['module2_detail']);
			 $daily_digest['subject']=addslashes($arr['subject']);
			 $id=insert_query("daily_digest",$daily_digest);
			 return $id;
		}
function addTemperatureGauge($arr){
			 $temp_gauge['tg_desc']=addslashes(mswordReplaceSpecialChars(stripslashes($arr['desc'])));
			 $temp_gauge['subject']=addslashes(mswordReplaceSpecialChars(stripslashes($arr['subject'])));
			 $temp_gauge['gauge']=$arr['gauge'];
			 $temp_gauge['link']=$arr['link'];
			 $id=insert_query("temp_gauge",$temp_gauge);
			 return $id;
		}
function addForwardDailyDigest($arr){
			 $daily_digest['user_name']=$arr['name'];
			 $daily_digest['user_email']=$arr['from'];
			 $daily_digest['friend_email']=$arr['friendEmail'];
			 $daily_digest['comment']=$arr['comment'];
			 $id=insert_query("forward_friend",$daily_digest);
			 return $id;
		}

function editTodaysDailyDigest($arr){
			 $id=$arr['id'];
			 $daily_digest['item_ids']=$arr['sel_article'];
			 $daily_digest['module1_detail']=$arr['module1_detail'];
			 $daily_digest['module2_detail']=$arr['module2_detail'];
			 $daily_digest['subject']=$arr['subject'];
			 $id=update_query("daily_digest",$daily_digest,array("id"=>$id));
			 return $id;
		}

function sendDailyDigest($arr)
{
	 $id=$arr['id'];
	 $daily_digest['is_sent']="1";
	 $id=update_query("daily_digest",$daily_digest,array("id"=>$id));
	 return $id;
}
function editTemperatureGauge($arr){
			 $id=$arr['id'];
			 $temp_gauge['tg_desc']=addslashes(mswordReplaceSpecialChars(stripslashes($arr['desc'])));
			 $temp_gauge['subject']=addslashes(mswordReplaceSpecialChars(stripslashes($arr['subject'])));
			 $temp_gauge['gauge']=$arr['gauge'];
			 $temp_gauge['link']=$arr['link'];
			 $id=update_query("temp_gauge",$temp_gauge,array("id"=>$id));
			 return $id;
		}
function getDailyDigest($id){
			if($id=="")
			{
				$sqlGetDailyDigest="SELECT id,subject,sent_on FROM daily_digest ORDER BY id DESC LIMIT 10 ;";
			}
			else
			{
				$sqlGetDailyDigest="SELECT * from daily_digest where id=".$id;
			}
			$resGetDailyDigest=exec_query($sqlGetDailyDigest);
			return $resGetDailyDigest;
		}

function getTempGauge($id=NULL){
			if($id)
			{
				$sqlGetTempGauge="SELECT * from temp_gauge where id=".$id;
			}
			else
			{
				$sqlGetTempGauge="SELECT id,`subject`,sent_on FROM temp_gauge ORDER BY id DESC LIMIT 10 ;";

			}
			$resGetTempGauge=exec_query($sqlGetTempGauge);
			return $resGetTempGauge;
		}

function getTempGaugeData($id=NULL){
		if($id)
		{
			$sqlGetTempGauge="SELECT * from temp_gauge where id=".$id;
		}
		else
		{
			$sqlGetTempGauge="SELECT * FROM temp_gauge ORDER BY id DESC LIMIT 1";
		}
		$resGetTempGauge=exec_query($sqlGetTempGauge);
		return $resGetTempGauge;
}



		function getAolDailyFinance(){
			$sqlIsNew="SELECT id FROM content_syndication WHERE item_id='".$this->id."' and item_type='1' and syndication_channel='dailyfinance' and is_syndicated='1'";
			$resIsNew=exec_query($sqlIsNew,1);
			if(count($resIsNew)>0)
			{
				return true;
			}else{
				return false;
			}

		}




	function setAolDailyFinanceSynd()
	{
		if($this->is_dailyfinance =="1")
		{
			 $sqlIsNew="SELECT id FROM content_syndication WHERE item_id='".$this->id."' and item_type='1' and syndication_channel='dailyfinance'";
			$resIsNew=exec_query($sqlIsNew,1);
			if(count($resIsNew)>0)
			{
				$dailyFinanceArticle['item_id']	= $this->id;
				$dailyFinanceArticle['item_type']	='1';
				$dailyFinanceArticle['syndication_channel']="dailyfinance";
				$dailyFinanceArticle['is_syndicated'] = $this->is_dailyfinance;
				$dailyFinanceArticle['syndicated_on'] = date("Y-m-d H:i:s");
				update_query("content_syndication",$dailyFinanceArticle,array('id' => $resIsNew['id']));
			}
			else
			{
				$dailyFinanceArticle['item_id']	= $this->id;
				$dailyFinanceArticle['item_type']	='1';
				$dailyFinanceArticle['syndication_channel']="dailyfinance";
				$dailyFinanceArticle['is_syndicated'] =$this->is_dailyfinance;
				$dailyFinanceArticle['syndicated_on'] = date("Y-m-d H:i:s");
				insert_query("content_syndication",$dailyFinanceArticle);
			}
		}
		else
		{
			 $sqlIsNew="SELECT id FROM content_syndication WHERE item_id='".$this->id."' and item_type='1' and syndication_channel='dailyfinance'";
			$resIsNew=exec_query($sqlIsNew,1);
			if(count($resIsNew)>0)
			{
				$dailyFinanceArticle['item_id']	= $this->id;
				$dailyFinanceArticle['item_type']	='1';
				$dailyFinanceArticle['syndication_channel']="dailyfinance";
				$dailyFinanceArticle['is_syndicated'] = $this->is_dailyfinance;
				$dailyFinanceArticle['syndicated_on'] = date("Y-m-d H:i:s");
				update_query("content_syndication",$dailyFinanceArticle,array('id' => $resIsNew['id']));
			}
		}
	}

	function getArticleAudio($articleId){
		$qryAudio = "SELECT item_value,item_key FROM article_meta WHERE item_id='".$articleId."' AND item_key IN ('radiofile','audiofile') AND item_type='1'";
		$resAudio = exec_query($qryAudio);
		if(!empty($resAudio)){
			return $resAudio;
		}
	}

	function setNasdaqSynd()
	{
		$nasdaqArt['item_id'] = $this->id;
		$nasdaqArt['item_type']	='1';
		$nasdaqArt['syndicated_on'] = date("Y-m-d H:i:s");
		$nasdaqArt['syndication_updated_on'] = date("Y-m-d H:i:s");
		$nasdaqArt['syndication_channel']	= 'nasdaq';

		if($this->is_nasdaqFeed=="1")
		{
			$nasdaqArt['is_nasdaqFeed'] = $this->is_nasdaqFeed;
			insert_or_update("content_syndication",$nasdaqArt,array('syndication_channel'=>'nasdaq','item_id'=>$this->id,'item_type'=>'1'));

		}
		else
		{
			if(empty($this->is_nasdaqFeed))
			{
				$nasdaqArt['is_nasdaqFeed'] = '0';
				insert_or_update("content_syndication",$nasdaqArt,array('syndication_channel'=>'nasdaq','item_id'=>$this->id,'item_type'=>'1'));
			}
		}

	}

	function getNasdaqSynd(){
		$qryNasdaqVal="SELECT is_nasdaqFeed FROM content_syndication WHERE item_id='".$this->id."' and item_type='1' and syndication_channel='nasdaq'";
		$resNasdaqVal=exec_query($qryNasdaqVal,1);
		if(!empty($resNasdaqVal))
		{
			return $resNasdaqVal['is_nasdaqFeed'];
		}else{
			return false;
		}
	}

}//class end
?>