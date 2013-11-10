<?php
include_once($D_R.'/lib/_bitly_lib.php');
Class Content{
	var $contentType,$contentId;
	//function Content($type,$id){
	function __construct($type="",$id=""){
		$this->contentId=$id;
		if(is_numeric($type))
		{
			$this->contentType=$type;
		}
		else
		{
			$sqlGetContentTypeId="select id,item_table from ex_item_type where item_text='".$type."' or item_table='".$type."'";
			$resGetContentTypeId=exec_query($sqlGetContentTypeId,1);
			$this->contentType=$resGetContentTypeId['id'];
			$this->contentTable=$resGetContentTypeId['item_table'];
		}
	}

	public function getMaxMetaObjectID()
	{
		$sqlGetMaxObjectID="SELECT max(item_id) maxid from ex_item_meta EIM where EIM.item_type='".$this->contentType."'";
		$resGetMaxObjectID=exec_query($sqlGetMaxObjectID,1);
		if($resGetMaxObjectID['maxid'])
			return $resGetMaxObjectID['maxid'];
		else
			return '0';
	}

	public function setMetaData($maxObjectID)
	{
		global $HTHOST,$HTPFX;
		switch($this->contentType){
		case "1":
			$this->metaData=$this->setArticleMeta($maxObjectID);
		break;
		case "23":
			$this->metaData=$this->setActiveInvestorMeta($maxObjectID);
		break;
		case "13":
			$this->metaData=$this->setFlexfolioMeta($maxObjectID);
		break;
		case "4":
			$this->metaData=$this->setDiscussionMeta($maxObjectID);
		break;
		case "6":
			$this->metaData=$this->setBlogMeta($maxObjectID);
		break;
		case "2":
			$this->metaData=$this->setBuzzMeta($maxObjectID);
		break;
		case "10":
			$this->metaData=$this->setSlideshowMeta($maxObjectID);
		break;
		case "11":
			$this->metaData=$this->setVideoMeta($maxObjectID);
		break;
		case "7":
			$this->metaData=$this->setProfileMeta($maxObjectID);
			break;
		case "14":
			$this->metaData=$this->setCooperMeta($maxObjectID);
			break;
		case "15":
			$this->metaData=$this->setOptionSmithMeta($maxObjectID);
			break;
		case "16":
			$this->metaData=$this->setBMTPMeta($maxObjectID);
			break;
		case "17":
			$this->metaData=$this->setJackNewsMeta($maxObjectID);
			break;
		case "18":
			$this->metaData=$this->setDailyFeedMeta($maxObjectID);
			break;
		case "20":
			$this->metaData=$this->setETFTraderMeta($maxObjectID);
			break;
		case "22":
			$this->metaData=$this->setTechStartMeta($maxObjectID);
			break;
		case "27":
			$this->metaData=$this->setGaryKMeta($maxObjectID);
			break;
		case "26":
			$this->metaData=$this->setWallOfWorryMeta($maxObjectID);
			break;


		}
		return $this->contentLink;
	}

	public function setFlexfolioMeta($maxObjectID)
	{
		$sqlGetContributor="select id,name from contributors where name = 'Quint Tatro'";
		$resGetContributor=exec_query($sqlGetContributor,1);
		$sqlArticleMeta="SELECT QA.id,body,QA.title,IF( QA.publish_date> QA.date, QA.publish_date, QA.date) publish_date,DATE_FORMAT(IF(QA.publish_date>QA.date,QA.publish_date,QA.date),'%c/%e/%Y') format_publish_date,QA.is_live,QA.approved from qp_alerts QA WHERE  QA.item_type = '".$this->contentType."' AND QA.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."' AND is_live = '1') order by id asc limit 0,1000";
		$resArticleMeta=exec_query($sqlArticleMeta);
		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			if($value['is_live'] && $value['approved'])
			{
				$metaData['is_live']=1;
			}
			else{
				$metaData['is_live']=0;
			}
			$metaData['item_type']=$this->contentType;
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['publish_date']=$value['publish_date'];
			$metaData['description']=htmlentities(substr(strip_tags($value['body']),0,70),ENT_QUOTES);
			$metaData['author_id']=$resGetContributor['id'];
			$metaData['author_name']=$resGetContributor['name'];
			$metaData['section']=strtoupper('flexfolio');
			$metaData['url']="/qp/?id=".$value['id'];
			$metaData['keywords']='';
			$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
			$resGetTags=exec_query($sqlGetTags);
			if($resGetTags){
				foreach($resGetTags as $valueTags)
				{
					if($valueTags['is_ticker'])
						$tickers[]=$valueTags['tag_id'];
					$keywords[]=$valueTags['tag_id'];
					$keywordsName[]=$valueTags['tag'];
				}
				$metaData['tickers']=implode(",",$tickers);

				$metaData['keywords']=implode(",",$keywords);

				$metaData['content']=implode(",",$keywords). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}else{


				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}
				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);

			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
			insert_or_update("ex_item_meta",$metaData,array('item_id' => $value['id'], 'item_type' => $this->contentType));
			unset($metaData);
			unset($keywords);
			unset($tickers);
			unset($keywordsName);
		}
	}

	public function setActiveInvestorMeta($maxObjectID)
	{
		$sqlGetContributor="select id,name from contributors where name = 'Terry Woo & Matt Theal'";
		$resGetContributor=exec_query($sqlGetContributor,1);
		$sqlArticleMeta="SELECT QA.id,body,QA.title,IF( QA.publish_date> QA.date, QA.publish_date, QA.date) publish_date,DATE_FORMAT(IF(QA.publish_date>QA.date,QA.publish_date,QA.date),'%c/%e/%Y') format_publish_date,QA.is_live,QA.approved from qp_alerts QA WHERE  QA.item_type = '".$this->contentType."' AND QA.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."' AND is_live = '1') order by id asc limit 0,1000";
		$resArticleMeta=exec_query($sqlArticleMeta);
		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			if($value['is_live'] && $value['approved'])
			{
				$metaData['is_live']=1;
			}
			else{
				$metaData['is_live']=0;
			}
			$metaData['item_type']=$this->contentType;
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['publish_date']=$value['publish_date'];
			$metaData['description']=htmlentities(substr(strip_tags($value['body']),0,70),ENT_QUOTES);
			$metaData['author_id']=$resGetContributor['id'];
			$metaData['author_name']=$resGetContributor['name'];
			$metaData['section']=strtoupper('Active Investor');
			$metaData['url']="/active-investor/post/id/".$value['id']."/";
			$metaData['keywords']='';
			$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			if(strlen($metaData['keywords'])>0){
				$metaData['keywords'].=','.$metaData['section'];
			}
			else{
				$metaData['keywords'].=$metaData['section'];
			}
			$arrKeys=make_stock_array($value['body']);
			$strKeys=implode(',',$arrKeys);
			if(strlen($strKeys)>0){
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$strKeys;
				}
				else{
					$metaData['keywords'].=$strKeys;
				}
			}
			$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
			$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);
			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
			insert_or_update("ex_item_meta",$metaData,array('item_id' => $value['id'], 'item_type' => $this->contentType));
			unset($metaData);
		}
	}

	public function setOptionSmithMeta($maxObjectID = NULL)
	{
		$sqlGetContributor="select id,name from contributors where name = 'Steve Smith'";
		$resGetContributor=exec_query($sqlGetContributor,1);
		if($maxObjectID)
		{
			$sqlArticleMeta="SELECT SS.id,body,SS.title,IF( SS.publish_date> SS.date, SS.publish_date, SS.date) publish_date,DATE_FORMAT(IF(SS.publish_date>SS.date,SS.publish_date,SS.date),'%c/%e/%Y') format_publish_date,SS.is_live,SS.approved from ss_alerts SS  WHERE id='".$maxObjectID."'";
		}
		else
		{
			$sqlArticleMeta="SELECT SS.id,body,SS.title,IF( SS.publish_date> SS.date, SS.publish_date, SS.date) publish_date,DATE_FORMAT(IF(SS.publish_date>SS.date,SS.publish_date,SS.date),'%c/%e/%Y') format_publish_date,SS.is_live,SS.approved from ss_alerts SS WHERE SS.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."' AND is_live = '1') order by id asc limit 0,1000";
		}
		$resArticleMeta=exec_query($sqlArticleMeta);
		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			$metaData['item_type']=$this->contentType;
			if($value['is_live'] && $value['approved'])
			{
				$metaData['is_live']=1;
			}
			else{
				$metaData['is_live']=0;
			}
			$metaData['title']=addslashes(mswordReplaceSpecialChars(stripslashes(($value['title']))));
			$metaData['seo_title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['title'])));
			$metaData['section']=strtoupper('optionsmith');
			$metaData['publish_date']=$value['publish_date'];
			$metaData['description']==addslashes(mswordReplaceSpecialChars(stripslashes(substr(strip_tags($value['body']),0,70))));
			$metaData['author_id']=$resGetContributor['id'];
			$metaData['author_name']=$resGetContributor['name'];
			$metaData['url']="/optionsmith/post/id/".$value['id'];
			$metaData['keywords']='';
			$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
			$resGetTags=exec_query($sqlGetTags);
			if($resGetTags){
				foreach($resGetTags as $valueTags)
				{
					if($valueTags['is_ticker'])
						$tickers[]=$valueTags['tag_id'];
					$keywords[]=$valueTags['tag_id'];
					$keywordsName[]=$valueTags['tag'];
				}
				$metaData['tickers']=implode(",",$tickers);
				$metaData['keywords']=implode(",",$keywords);

				$metaData['content']=implode(",",$keywords). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}else{

				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}

			$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
			$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);

			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
			insert_or_update("ex_item_meta",$metaData,array('item_id' => $value['id'], 'item_type' => $this->contentType));
			unset($metaData);
			unset($keywords);
			unset($tickers);
			unset($keywordsName);
		}
	}


 public function setProfileMeta($maxObjectID)
{
        $sqlSubMeta="select S.id,EUP.id profile_id,concat(fname,' ',lname) name from subscription S,ex_user_profile EUP where S.id=EUP.subscription_id and S.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."') order by S.id asc limit 0,1000";
		$resSubMeta=exec_query($sqlSubMeta);
		if($resSubMeta){
			foreach($resSubMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['name'],ENT_QUOTES);
				$metaData['url']="/community/profile/?id=".$value['id'];
				$sqlGetattr="Select value from ex_profile_attribute_mapping where profile_id='".$value['profile_id']."'";
				$resGetattr=exec_query($sqlGetattr);
				 $metaData['content']=$value['name'];
				foreach($resGetattr as $valueattr)
				{
					$metaData['content'].=$valueattr['value']." \n";
				}
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				insert_query("ex_item_meta",$metaData);
				unset($metaData);
			}
		}
}
	function createUrlFormat($sectionids,$navigation_section_id=NULL){
		$strQuery="SELECT article_url,section_id FROM section WHERE (TYPE='section' OR TYPE='subsection') AND ";
		if(!empty($navigation_section_id)){
			$strQuery.= "section_id in ('".$navigation_section_id."')";
		}else{
			$strQuery.= "section_id in ('".$sectionids."')";
		}
		$strSection=exec_query($strQuery,1);
		if(!empty($strSection['article_url'])){
			return $strSection['article_url'];
		}else{
			$strUrl="/business-news/";
			return $strUrl;
		}
	}

	function getSection($sectionids){
		$strTitle='';
		global $default_section;
		$strQuery="SELECT section_id,name FROM section WHERE (TYPE='section' OR TYPE='subsection') AND section_id in ('$sectionids')";

		$strRequest=exec_query($strQuery);
			$strKey=1000;
			foreach($strRequest as $key=>$val) {
				if($val['section_id']<$strKey) $strTitle=$val['name'];
			}

		if(trim($strTitle)==''){
			$strTitle=$default_section;
		}

		return $strTitle;
	}

	function createTitleFormat($sectionids){
		$strTitle='';
		$strQuery="SELECT NAME FROM section WHERE (TYPE='section' OR TYPE='subsection') AND section_id in ('$sectionids')";
		$strRequest=exec_query($strQuery,1);
		if($strRequest){
			$strTitle=' | '.$strRequest['NAME'].' | Minyanville\'s Wall Street';
		}
		return $strTitle;
	}

	public function setIcTagMeta($id,$tag_id)
	{
		$qry = "SELECT id FROM `article_meta` WHERE `item_key`='ad_zone' AND `item_id`='".$id."'";
		$resIcTag = exec_query($qry);

		if($resIcTag)
		{
			$ictag['item_value']=$tag_id;
			update_query("article_meta",$ictag,array('id' => $resIcTag[0]['id']));
		}
		else
		{
			$ictag['item_key']="ad_zone";
			$ictag['item_value']=$tag_id;
			$ictag['item_type']="1";
			$ictag['item_id']=$id;
			insert_query("article_meta",$ictag);
		}
		$objCache= new Cache();
		$objCache->setArticleIcTag($id);
	}

	public function setArticleMeta($maxObjectID = NULL)
	{
		global $default_keywords,$default_description,$default_section,$HTPFX,$HTHOST;

		if($this->contentId)
		{
			 $sqlArticleMeta="SELECT A.id,body,A.title,A.is_yahoofeed,A.seo_title,A.contrib_id author_id,IF(A.publish_date>A.date,A.publish_date,A.date) publish_date,DATE_FORMAT(IF(A.publish_date>A.date,A.publish_date,A.date),'%c/%e/%Y') format_publish_date,C.name author_name,A.subsection_ids,A.navigation_section_id,A.character_text blurb,A.keyword,A.is_live,A.approved from articles A,contributors C WHERE  A.contrib_id=C.id and A.id =".$this->contentId;
		}
		else
		{
			 $sqlArticleMeta="SELECT A.id,body,A.title,A.is_yahoofeed,A.seo_title,A.contrib_id author_id,IF(A.publish_date>A.date,A.publish_date,A.date) publish_date,DATE_FORMAT(IF(A.publish_date>A.date,A.publish_date,A.date),'%c/%e/%Y') format_publish_date,C.name author_name,A.subsection_ids,A.navigation_section_id,A.character_text blurb,A.keyword,A.is_live,A.approved from articles A,contributors C WHERE  A.contrib_id=C.id and A.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."') order by id desc limit 0,1000";
		}
		$resArticleMeta=exec_query($sqlArticleMeta);

		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			$metaData['item_type']=$this->contentType;
			$strSectionName='';
			if($value['subsection_ids']){
				$strSectionName=$this->getSection($value['subsection_ids']);
			}
			if(trim($strSectionName)=='') $strSectionName=$default_section;

			$metaData['title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['title'])));
			$metaData['seo_title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['seo_title'])));
			$metaData['section']=strtoupper($strSectionName);
			$metaData['publish_date']=$value['publish_date'];
			$valuebody=strip_tag_style($value['body']);
			if(trim($value['blurb'])==''){
				$value['blurb']=substr(strip_tags($valuebody),0,100);
			}
			$metaData['description']=addslashes(mswordReplaceSpecialChars(stripslashes($value['blurb'])));
			$metaData['author_id']=$value['author_id'];
			if($value['is_live'] && $value['approved'])
						{
							$metaData['is_live']=1;
						}
						else{
							$metaData['is_live']=0;
			}
			$metaData['keywords']=addslashes(mswordReplaceSpecialChars(stripslashes(str_replace("-",",",$value['keyword']))));
			$metaData['author_name']=addslashes(mswordReplaceSpecialChars(stripslashes($value['author_name'])));
			$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";

			$resGetTags=exec_query($sqlGetTags);
			$urlFormat=$this->createUrlFormat($value['subsection_ids'],$value['navigation_section_id']);
			$metaData['url']=$urlFormat."articles/";
			$value['keyword']=urlencode($value['keyword']);
			$value['keyword']=str_replace('+','-',$value['keyword']);
			if($value['keyword']){
				$keyArray=explode('-',$value['keyword']);
				$keyIndex=0;
				$strKeys='';
				$tmpArrKeys=array();

					foreach($keyArray as $key=>$val){

						if($keyIndex==6) break;
						if(strlen(trim($val))>0){
							if($keyIndex<6){
								$tmpArrKeys[]=$val;
							}
							$keyIndex++;
						}

					}

					$strKeys=implode('-',$tmpArrKeys);
					if($value['is_yahoofeed']=="1")
					{
						$url = HTPFX.$HTHOST.$metaData['url'].urlencode($strKeys)."/".$value['format_publish_date']."/id/".$value['id']."?camp=syndication&medium=portals&from=yahoo";
						while(strlen($url)>180)
						{
							array_pop($tmpArrKeys);
							$strKeys=implode('-',$tmpArrKeys);
							$url = HTPFX.$HTHOST.$metaData['url'].urlencode($strKeys)."/".$value['format_publish_date']."/id/".$value['id']."?camp=syndication&medium=portals&from=yahoo";
						}
					}
				unset($tmpArrKeys);
				$metaData['url'].=urlencode($strKeys)."/";
			}

			$metaData['url'].=$value['format_publish_date']."/id/".$value['id'];
			$metaData['url']=clean_url($metaData['url']);

			if($resGetTags){
				foreach($resGetTags as $valueTags)
				{
						$tickers[]=$valueTags['tag'];

					$keywords[]=$valueTags['tag'];
					$keywordsName[]=$valueTags['tag'];
				}

				$metaData['tickers']=implode(",",$tickers);

				$metaData['content']=implode(",",$keywords). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}else{
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			}
			
			$sqlGetSec="select name from section S where id in(A.subsection_ids)";
			$resgetSec=exec_query($sqlGetSec);
			foreach($resgetSec as $value)
			{
				$metaData['content']=$metaData['content']." \n ".$value['name'];
			}
			if(strlen($metaData['keywords'])>0){
				$metaData['keywords'].=','.$metaData['section'];
			}
			else{
				$metaData['keywords'].=$metaData['section'];
			}
			$arrKeys=make_stock_array($value['body']);
			$strKeys=implode(',',$arrKeys);
			if(strlen($strKeys)>0){
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$strKeys;
				}
				else{
					$metaData['keywords'].=$strKeys;
				}
			}
			$metaData['content'] = addslashes(mswordReplaceSpecialChars(stripslashes($metaData['content'])));
			
			
			if($this->contentId)
			{
				$sqlIsNew="SELECT id FROM content_seo_url WHERE item_id='".$this->contentId."' AND item_type='".$this->contentType."'";
				$resIsNew=exec_query($sqlIsNew,1);
				if(count($resIsNew)>0){
					unset($metaData['url']);
					insert_or_update("ex_item_meta",$metaData,array('item_id' => $this->contentId, 'item_type' => 1));
				}else{
					insert_or_update("ex_item_meta",$metaData,array('item_id' => $this->contentId, 'item_type' => 1));
				}
			}
			else
			{
				insert_query("ex_item_meta",$metaData);
			}
			
			unset($metaData);
			unset($keywords);
			unset($tickers);
			unset($keywordsName);
		}
	}

	public function setBuzzMeta($maxObjectID){
		if($this->contentId){
			 $sqlBuzzMeta="SELECT B.id, body, B.title, B.contrib_id author_id, IF(B.publish_date>B.date,B.publish_date,B.date) publish_date, DATE_FORMAT(IF(B.publish_date>B.date,B.publish_date,B.date),'%c/%e/%Y')
format_publish_date, C.name author_name, B.is_live, B.approved FROM buzzbanter B, `contributors` C
WHERE B.contrib_id=C.id AND login!='(automated)' AND B.id=".$this->contentId;
		}else{
			$sqlBuzzMeta="SELECT B.id,body,B.title,B.contrib_id author_id,IF(B.publish_date>B.date,B.publish_date,B.date) publish_date,DATE_FORMAT(IF(B.publish_date>B.date,B.publish_date,B.date),'%c/%e/%Y') format_publish_date,C.name author_name, B.is_live,B.approved from buzzbanter B,`contributors` C WHERE  B.contrib_id=C.id and login!='(automated)' and B.id not in (select item_id from ex_item_meta where is_live='1' AND item_type='".$this->contentType."') order by id asc limit 0,1000";
		}
			$resBuzzMeta=exec_query($sqlBuzzMeta);
			foreach($resBuzzMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['section']=strtoupper('buzz');
				if($value['is_live'] && $value['approved'])
				{
					$metaData['is_live']=1;
				}
				else{
					$metaData['is_live']=0;
				}
				$metaData['publish_date']=$value['publish_date'];
				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
				$metaData['author_id']=$value['author_id'];
				$metaData['author_name']=$value['author_name'];
				$metaData['keywords']='';
				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);
				if($resGetTags){
					foreach($resGetTags as $valueTags)
					{
						$keywords[]=$valueTags['tag_id'];
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['keywords']=implode(",",$keywords);
					$sqlGetTicker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
					$getTicker=exec_query($sqlGetTicker);
					$tickerName=array();
					foreach($getTicker as $valueTicker){
						$tickerName[]=$valueTicker['stocksymbol'];
					}
					$metaData['tickers']=implode(",",$tickerName);

					$metaData['content']=implode(",",$keywords). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{


					$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}

				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);

				$metaData['url']="/buzz/bookmark.php?id=".$value['id'];
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
				unset($keywords);
				unset($tickers);
				unset($keywordsName);
			}
	}

	public function setCooperMeta($maxObjectID)
		{
			if($this->contentId)
			{
			 $sqlArticleMeta="SELECT A.id,body,A.title,2 author_id,
			 IF(A.publish_date>A.date,A.publish_date,A.date) publish_date,
			 DATE_FORMAT(IF(A.publish_date>A.date,A.publish_date,A.date),'%c/%e/%Y') format_publish_date,
			 'Jeff Cooper' author_name,A.character_text blurb,A.keyword,A.is_live,
			 A.approved from cp_articles A WHERE  A.id =".$this->contentId;
			}
			else
			{
			 $sqlArticleMeta="SELECT A.id,body,A.title,2 author_id,
			 IF(A.publish_date>A.date,A.publish_date,A.date) publish_date,
			 DATE_FORMAT(IF(A.publish_date>A.date,A.publish_date,A.date),'%c/%e/%Y') format_publish_date,
			'Jeff Cooper' author_name,A.character_text blurb,A.keyword,A.is_live,
			 A.approved FROM cp_articles A WHERE
			 A.id NOT IN (SELECT item_id FROM ex_item_meta WHERE item_type='".$this->contentType."' AND is_live = '1')
			 ORDER BY id DESC LIMIT 0,100";
			}
			$resArticleMeta=exec_query($sqlArticleMeta);
			foreach($resArticleMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['section']=strtoupper('cooper');
				if($value['is_live'] && $value['approved'])
				{
					$metaData['is_live']=1;
				}
				else{
					$metaData['is_live']=0;
				}
				$metaData['publish_date']=$value['publish_date'];
				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
				$metaData['author_id']='90';
				$metaData['author_name']=$value['author_name'];
				$metaData['url']=makeArticleslinkcooper($value['id'],$value['keyword']);
				$metaData['keywords']=$value['keyword'];
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}


				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);

				insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
	}

	public function setDiscussionMeta($maxObjectID=0)
	{

		$sqlDiscussionMeta="select ET.*,concat(S.fname,' ',S.lname) author_name,DATE_FORMAT(ET.created_on,'%c/%e/%Y') format_publish_date  from ex_thread ET,subscription S where S.id=ET.author_id and ET.approved='1' and ET.is_user_blog='0'";
		$sqlDiscussionMeta.=" and ET.id>'".$maxObjectID."'";
		$sqlDiscussionMeta.=" order by id asc limit 0,1000";
		$resDiscussionMeta=exec_query($sqlDiscussionMeta);
		foreach($resDiscussionMeta as $value)
		{
			$sqlGetBody="select * from ".$value['content_table']." where id='".$value['content_id']."'";
			$resGetBody=exec_query($sqlGetBody,1);
			$metaData['item_id']=$value['id'];
			$metaData['item_type']=$this->contentType;
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['publish_date']=$value['created_on'];
			$valuebody=strip_tag_style($value['body']);
			$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
			$metaData['author_id']=$value['author_id'];
			$metaData['author_name']=$value['author_name'];
			$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
			$resGetTags=exec_query($sqlGetTags);
			if($resGetTags){
				foreach($resGetTags as $valueTags)
				{
					if($valueTags['is_ticker'])
						$tickers[]=$valueTags['tag'];
					$keywords[]=$valueTags['tag'];
					$keywordsName[]=$valueTags['tag'];
				}
				$metaData['tickers']=implode(",",$tickers);
				$metaData['keywords']=implode(",",$keywords);
				$metaData['content']=implode(",",$keywords). " \n " .strip_tags($resGetBody['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				$metaData['url']="/community/".urlencode($value['keyword'])."/".$value['format_publish_date']."/discussion/".$value['id'];
			}else{
				$metaData['content']=strip_tags($resGetBody['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				$metaData['url']="/community/".$value['format_publish_date']."/discussion/".$value['id'];
			}
			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
			insert_query("ex_item_meta",$metaData);
			unset($metaData);
			unset($keywords);
			unset($tickers);
			unset($keywordsName);
		}
	}




	public function setBlogMeta($maxObjectID=0)
	{

			$sqlBlogMeta="select ET.*,concat(S.fname,' ',S.lname) author_name,DATE_FORMAT(ET.created_on,'%c/%e/%Y') format_publish_date  from ex_thread ET,subscription S where S.id=ET.author_id and ET.approved='1' and ET.is_user_blog='1'";
			$sqlBlogMeta.=" and not in (select item_id from ex_item_meta where item_type='".$this->contentType."') ";
			$sqlBlogMeta.=" order by id asc limit 0,1000";
			$resBlogMeta=exec_query($sqlBlogMeta);
			foreach($resBlogMeta as $value)
			{
				$sqlGetBody="select * from ".$value['content_table']." where id='".$value['content_id']."'";
				$resGetBody=exec_query($sqlGetBody,1);
				$metaData['item_id']=$value['id'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['publish_date']=$value['created_on'];
				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,200),ENT_QUOTES);
				$metaData['author_id']=$value['author_id'];
				$metaData['author_name']=$value['author_name'];
				$metaData['url']="/community/blog_comments.htm?blog_id=".$value['id'];
				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);
				if($resGetTags){
					foreach($resGetTags as $valueTags)
					{
						if($valueTags['is_ticker'])
							$tickers[]=$valueTags['tag'];
						$keywords[]=$valueTags['tag'];
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['tickers']=implode(",",$tickers);
					$metaData['keywords']=implode(",",$keywords);
					$metaData['content']=implode(",",$keywords). " \n " .strip_tags($resGetBody['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{
					$metaData['content']=strip_tags($resGetBody['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				insert_query("ex_item_meta",$metaData);
				unset($metaData);
				unset($keywords);
				unset($tickers);
				unset($keywordsName);
			}
	}

	public function setSlideshowMeta($maxObjectID){
		if($this->contentId){

		$sqlSlideshowMeta="SELECT id,title,contributor author,publish_date,approved
FROM slideshow WHERE  id='".$this->contentId."'";
		} else {
			$sqlSlideshowMeta="SELECT id,title,contributor author,publish_date,approved
FROM slideshow  WHERE  id NOT IN (SELECT item_id FROM ex_item_meta WHERE item_type='".$this->contentType."') order by id asc limit 0,1000";
		}
		$resSlideshowMeta=exec_query($sqlSlideshowMeta);

		foreach($resSlideshowMeta as $value){
			$metaData['item_id']=$value['id'];
			$metaData['is_live']=$value['approved'];
			$metaData['item_type']=$this->contentType;
			$metaData['publish_date']=$value['publish_date'];
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['author_name']=$value['author'];
			$metaData['keywords']='';
			$strQuery="SELECT slide_title FROM slideshow_content WHERE slideshow_id='".$value['id']."' ORDER BY id ASC";
			$strResult=exec_query($strQuery,1);

			$metaData['description']='';
			$metaData['section']='';
			if($strResult){
				$metaData['section']=$strResult['slide_title'];
				$metaData['description']=$strResult['slide_title'];
			}
			$metaData['url']="/slideshow/".$value['id'];
			$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
			$resGetTags=exec_query($sqlGetTags);
			if($resGetTags){
				foreach($resGetTags as $valueTags)
				{

					if($valueTags['is_ticker'])
						$tickers[]=$valueTags['tag'];

						$keywords[]=$valueTags['tag'];
						$keywordsName[]=$valueTags['tag'];
				}
				//htmlprint_r($keywords);
				$metaData['tickers']=implode(",",$tickers);
				$metaData['keywords']=implode(",",$keywords);
				$metaData['content']=strip_tags($value['description']). " \n " .$value['title']. " \n " .$value['author_name']. " \n " .$metaData['keywords']."\n".$metaData['tickers'];
				}else{
					$metaData['content']=strip_tags($value['description']). " \n " .$value['title']. " \n " .$value['author_name'];
				}
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);
				$metaData['section']=htmlentities($metaData['section'],ENT_QUOTES);

				if($this->contentId)
				{
					insert_or_update("ex_item_meta",$metaData,array('item_id' => $this->contentId, 'item_type' => 10));
				}
				else
				{
					insert_query("ex_item_meta",$metaData);
				}
				unset($metaData);
				unset($keywords);
				unset($tickers);
				unset($keywordsName);
		}

	}
	public function setVideoMeta($maxObjectID=NULL)
	{
		global $VIDEOHOST;
		   if($this->contentId)
		{
		$sqlVideoMeta="select id,keywords,description,title,IF(publish_time>creation_time,publish_time,creation_time) publish_date,DATE_FORMAT(IF(publish_time>creation_time,publish_time,creation_time),'%c/%e/%Y') format_publish_date,cat_id,is_live,approved from mvtv where id=".$this->contentId;
		} else {
			$sqlVideoMeta="select id,keywords,description,title,IF(publish_time>creation_time,publish_time,creation_time) publish_date,DATE_FORMAT(IF(publish_time>creation_time,publish_time,creation_time),'%c/%e/%Y') format_publish_date,cat_id,is_live,approved from mvtv where id not in (select item_id from ex_item_meta where item_type='".$this->contentType."') order by id asc limit 0,1000";
		}

			$resVideoMeta=exec_query($sqlVideoMeta);
			foreach($resVideoMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				if($value['is_live'] && $value['approved'])
				{
					$metaData['is_live']=1;
				}
				else{
					$metaData['is_live']=0;
				}
				$metaData['item_type']=$this->contentType;
				if($value['cat_id']){
					$strSectionName=$this->getSection($value['cat_id']);
				}

				$metaData['section']=$strSectionName;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);

				$metaData['publish_date']=$value['publish_date'];
				$valuebody=strip_tag_style($value['description']);
				$metaData['description']=htmlentities(strip_tags($valuebody),ENT_QUOTES);
				$metaData['author_name']="Hoofy & Boo";
				$metaData['keywords']=$value['keywords'];
				$metaData['keywords']=str_replace('-',',',$metaData['keywords']);

				$metaData['url']=$this->getVideoURL($value['id']);

				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);
				if($resGetTags){
					unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						if($valueTags['is_ticker'])
							$tickers[]=$valueTags['tag'];
						$keywords[]=$valueTags['tag'];
						$keywordsName[]=$valueTags['tag'];
					}
				$metaData['tickers']=implode(",",$tickers);
				$metaData['content']=strip_tags($value['description']). " \n " .$value['title']. " \n " .$value['author_name']. " \n " .$value['keywords']."\n".$metaData['keywords']."\n".$metaData['tickers'];
				}else{
					$metaData['content']=strip_tags($value['description']). " \n " .$value['title']. " \n " .$value['author_name']. " \n " .$value['keywords'];
				}
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}

				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);
				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}
				else if(count($resIsPublished) == 0 && $metaData['is_live']==1)
				{
					// set content Seo URL
					$this->updateContentSeoUrl($this->contentId,$this->contentType,$metaData['url']);

				}
					if($this->contentId)
					{
						insert_or_update("ex_item_meta",$metaData,array('item_id' => $this->contentId, 'item_type' => 11));
					}
					else
					{
				insert_query("ex_item_meta",$metaData);
					}
				unset($metaData);
			}
			return true;
	}

	public function setBMTPMeta($maxObjectID)
	{

		$sqlGetContributor="select id,name from contributors where name = 'Smita Sadana'";
		$resGetContributor=exec_query($sqlGetContributor,1);
		$sqlArticleMeta="SELECT SS.id,body,SS.title,SS.date as publish_date,SS.is_live,SS.approved from bmtp_alerts SS WHERE SS.id not in (select item_id from ex_item_meta where item_type='".$this->contentType."') order by id asc limit 0,1000";
		$resArticleMeta=exec_query($sqlArticleMeta);
		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			$metaData['is_live']=$value['is_live'];
			$metaData['item_type']=$this->contentType;
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['section']=strtoupper('bull market');
			$metaData['publish_date']=$value['publish_date'];
			$valuebody=strip_tag_style($value['body']);
			$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
			$metaData['author_id']=$resGetContributor['id'];
			$metaData['author_name']=$resGetContributor['name'];
			$metaData['url']="/bmtp/alert.htm?id=".$value['id'];

			$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}

			$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
			$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);

			insert_query("ex_item_meta",$metaData);
			unset($metaData);
		}
	}

	public function setJackNewsMeta($maxObjectID)
	{

		$sqlArticleMeta="SELECT CA.id,body,CA.title,CA.keyword,CA.contrib_id author_id,IF(CA.publish_date>CA.date,CA.publish_date,CA.date) publish_date,DATE_FORMAT(IF(CA.publish_date>CA.date,CA.publish_date,CA.date),'%c/%e/%Y') format_publish_date,CC.name author_name,CA.is_live,CA.approved from jack_articles CA,contributors CC WHERE  CA.contrib_id=CC.id and CA.id>'".$maxObjectID."' order by id asc limit 0,1000";
			$resArticleMeta=exec_query($sqlArticleMeta);
			foreach($resArticleMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				if($value['is_live'] && $value['approved'])
				{
					$metaData['is_live']=1;
				}
				else{
					$metaData['is_live']=0;
				}
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['section']=strtoupper('jack Lavery');
				$metaData['publish_date']=$value['publish_date'];
				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
				$metaData['author_id']=$value['author_id'];
				$metaData['author_name']=$value['author_name'];
				$metaData['url']=makeArticleslinkjack($value['id'],$value['keyword']);
				$metaData['keywords']=$value['keyword'];
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}

				$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
				$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);
				insert_query("ex_item_meta",$metaData);
				unset($metaData);
			}
	}

	public function setDailyFeedMeta($maxObjectID = NULL)
	{
		global $HTPFX,$HTHOST;
		if($this->contentId)
		{
			 $sqlDailyFeedMeta="SELECT DF.id,DF.body,DF.excerpt,DF.title,
 IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS publish_date
 ,DF.is_approved,DF.is_live,DF.contrib_id,C.name author_name FROM daily_feed DF,
 contributors C WHERE DF.is_deleted='0' AND C.id=DF.contrib_id AND DF.id ='".$this->contentId."'";
		}
		else
		{
			$sqlDailyFeedMeta="SELECT DF.id,DF.body,DF.excerpt,DF.title,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS publish_date,DF.is_approved,DF.is_live,DF.contrib_id,C.name author_name FROM daily_feed DF,contributors C WHERE DF.is_deleted='0'  AND C.id=DF.contrib_id AND DF.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."') ORDER BY id ASC LIMIT 0,1000";
		}

		$resDailyFeedMeta=exec_query($sqlDailyFeedMeta);
			foreach($resDailyFeedMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['is_live'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['title'])));
				$metaData['publish_date']=$value['publish_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes($value['author_name']);

				$valuebody=strip_tag_style($value['body']);
				if($value['excerpt']==""){
					$metaData['description']=substr(strip_tags($valuebody),0,150);
				}else{
					$metaData['description']=strip_tags($value['excerpt']);
				}
				$metaData['description'] = addslashes(mswordReplaceSpecialChars(stripslashes($metaData['description'])));

				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);
				$keywordsName=array();
				if($resGetTags){
					// unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['keywords']=implode(",",$keywordsName);
					$metaData['content']=strip_tags($value['excerpt']). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{
					$metaData['content']=strip_tags($value['excerpt']). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}
				$metaData['content'] = addslashes($metaData['content']);
				$metaData['content'] = addslashes(mswordReplaceSpecialChars(stripslashes($metaData['content'])));
				$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."'";

				$getTicker=exec_query($resgetticker);
				$tickerName=array();
				foreach($getTicker as $valueTicker){
					$tickerName[]=$valueTicker['stocksymbol'];
				}
				$metaData['tickers']=implode(",",$tickerName);

				$metaData['url']=$this->getDailyFeedURL($value['id']);

				$metaData['keywords']=addslashes($metaData['keywords']);

				$sqlResource	=	"select source from ex_source where item_id='".$value['id']."' and item_type='18'";
				$resultResource	=	exec_query($sqlResource,1);
				if($resultResource && count($resultResource)>0)
				{
					$metaData['resource']=addslashes($resultResource['source']);
				}
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);
				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}
				
				insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}

		}


   public function setHousingreportMeta($maxObjectID = NULL)
	{
		global $HTPFX,$HTHOST;
		if($this->contentId)
		{
		$sqlDailyFeedMeta="SELECT DF.id,DF.keyword,DF.body,DF.article_title as title,DF.date,DATE_FORMAT(DF.date,'%c/%e/%Y') format_updation_date,DF.approved,DF.contrib_id,C.name author_name FROM housingreport_articles DF,`contributors` C WHERE  C.id=DF.contrib_id
AND DF.id ='".$this->contentId."'";
		}
		else
		{
		$sqlDailyFeedMeta="SELECT DF.id,DF.keyword,DF.body,DF.article_title as title,DF.date,DATE_FORMAT(DF.date,'%c/%e/%Y') format_updation_date,DF.approved,DF.contrib_id,C.name author_name FROM housingreport_articles DF,`contributors` C WHERE C.id=DF.contrib_id AND DF.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."') ORDER BY id ASC LIMIT 0,1000";
		}

		$resDailyFeedMeta=exec_query($sqlDailyFeedMeta);
			foreach($resDailyFeedMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['approved'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['publish_date']=$value['date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes($value['author_name']);
				//$metaData['resource']=addslashes($value['source']);

				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(strip_tags($valuebody),ENT_QUOTES);
				$metaData['keywords']=$value['keyword'];



				$urltitle=$this->getFirstFiveWords($value['title']);

				$metaData['url']="/housing-market-report/".$urltitle.'/';
				$metaData['url']=$this->getUniqueURL($metaData['url']);

				$metaData['keywords']=addslashes($value['keyword']);

								$metaData['content']=htmlentities($metaData['description'],ENT_QUOTES);
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);
				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}
				insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}

		}

		 public function deleteHousingreportMeta($maxObjectID = NULL)
	{
		global $HTPFX,$HTHOST;
		if($this->contentId)
		{
		$qry="delete from ex_item_meta where item_id=".$this->contentId." and item_type=".$this->contentType."";
		exec_query_nores($qry);
		}
	}


	 public function setHousingreportPdfMeta($maxObjectID = NULL)
	{
		global $HTPFX,$HTHOST;
		if($this->contentId)
		{
		$sqlHMPdfFeedMeta="select id,title from housingreport_performance where id ='".$this->contentId."'";
		}
		else
		{
		$sqlHMPdfFeedMeta="select id,title from housingreport_performance where id NOT IN (SELECT item_id FROM ex_item_meta WHERE 			 item_type='".$this->contentType."') ORDER BY id ASC LIMIT 0,1000";
		}

		$resHmrMeta=exec_query($sqlHMPdfFeedMeta);
			foreach($resHmrMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$urltitle=$this->getFirstFiveWords($value['title']);
				$metaData['url']="/housing-market-report/pdf/".$urltitle.'/';
				$metaData['url']=$this->getUniqueURL($metaData['url']);
				$metaData['content']=$metaData['title'];
				$objbitly= new bitly();
				insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}

		}


	/******************************** Set PepFil Meta Data **********************************/
	/*public function setPepfilMeta($maxObjectID = NULL)
		{
		if($this->contentId)
		{
		 $sqlPepfilMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date  from pep_fil_articles a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id ='$this->contentId'";
		}
		else
		{
		 $sqlPepfilMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from pep_fil_articles a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."') order by id asc limit 0,1000";
		}

		$resPepfilMeta=exec_query($sqlPepfilMeta);
			foreach($resPepfilMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['is_approved'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['publish_date']=$value['updation_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes($value['author_name']);
				//$metaData['resource']=addslashes($value['source']);

				$valuebody=strip_tag_style($value['body']);
				//$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,150),ENT_QUOTES);
				if($value['article_type'] == '2')
				{
					$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,150),ENT_QUOTES);
				}
				else
				{
					$metaData['description']=htmlentities($value['title'],ENT_QUOTES);
				}

				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."' and item_type='".$this->contentType."'";

				$getTicker=exec_query($resgetticker);
				$tickerName=array();
				foreach($getTicker as $valueTicker){
					$tickerName[]=$valueTicker['stocksymbol'];
				}
				$metaData['tickers']=implode(",",$tickerName);

				$urltitle=$this->getFirstFiveWords($value['title']);
				if($value['article_type'] == '1')
				{
					$url="/pepfil/report/".$urltitle.'/';
				}
				elseif($value['article_type'] == '2')
	{
					$url="/pepfil/mailbag/".$urltitle.'/';
				}
				elseif($value['article_type'] == '3')
		{
					$url="/pepfil/livehchat/".$urltitle.'/';
				}
				$metaData['url']=$url;

				//$metaData['keywords']=addslashes($metaData['keywords']);
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);
				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}
			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
	}*/
	/******************************** Set PepFil Meta Data **********************************/
	/*****************************  Set Sean Udall Meta Data ********************************/
	public function setTechStartMeta($maxObjectID = NULL)
		{
		if($this->contentId)
		{
		 $sqlTechStartMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date  from techstrat_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id ='".$this->contentId."'";
		}
		else
		{
		 $sqlTechStartMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from techstrat_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."' AND is_live = '1') order by id asc limit 0,1000";
		}
		$resTechStartMeta=exec_query($sqlTechStartMeta);
			foreach($resTechStartMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['is_approved'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['title'])));
				$metaData['publish_date']=$value['publish_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes(mswordReplaceSpecialChars(stripslashes($value['author_name'])));

				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=addslashes(mswordReplaceSpecialChars(stripslashes(substr(strip_tags($valuebody),0,150))));

				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);

				$keywordsName=array();
				if($resGetTags){
					// unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['keywords']=addslashes(mswordReplaceSpecialChars(stripslashes(implode(",",$keywordsName))));
					$metaData['content']=implode(",",$keywordsName). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{
					$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}
				$metaData['content'] = addslashes(mswordReplaceSpecialChars(stripslashes($metaData['content'])));


				$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."' and item_type='".$this->contentType."'";
				$getTicker=exec_query($resgetticker);
				if($getTicker)
				{
					$tickerName=array();
					foreach($getTicker as $valueTicker){
						$tickerName[]=$valueTicker['stocksymbol'];
					}
					$metaData['tickers']=implode(",",$tickerName);
				}
				$url=$this->getTechStratUrl($value['id']);
				$metaData['url']=$url;
                $metaData['content']=implode(",",$tickerName). " \n " .$metaData['content'];
				//$metaData['keywords']=addslashes($metaData['keywords']);
				$metaData['content']=strip_tags($metaData['content']);
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);

	}
	}
	/******************************************  Sean Udall Meta Data Ends ************************************/


	/*****************************  Set GaryK Meta Data ********************************/
	public function setGaryKMeta($maxObjectID = NULL)
		{
		if($this->contentId)
		{
		 $sqlGaryKMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from garyk_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id ='".$this->contentId."'";
		}
		else
		{
		 $sqlGaryKMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from garyk_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."') order by id asc limit 0,1000";
		}
		$resGaryKMeta=exec_query($sqlGaryKMeta);
			foreach($resGaryKMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['is_approved'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
				$metaData['publish_date']=$value['creation_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes($value['author_name']);

				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,150),ENT_QUOTES);
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];

				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);

				$keywordsName=array();
				if($resGetTags){
					// unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['keywords']=implode(",",$keywordsName);
					$metaData['content']=implode(",",$keywordsName). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{
					$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}


				$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."' and item_type='".$this->contentType."'";
				$getTicker=exec_query($resgetticker);
				if($getTicker)
				{
					$tickerName=array();
					foreach($getTicker as $valueTicker){
						$tickerName[]=$valueTicker['stocksymbol'];
					}
					$metaData['tickers']=implode(",",$tickerName);
				}
				$url=$this->getGaryKUrl($value['id']);
				$metaData['url']=$url;
                $metaData['content']=implode(",",$tickerName). " \n " .$metaData['content'];
				//$metaData['keywords']=addslashes($metaData['keywords']);
				$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));

			unset($metaData);

	}
	}
	/******************************************  Sean Udall GaryK Ends ************************************/














	public function setETFTraderMeta($maxObjectID)
	{
		$sqlArticleMeta="SELECT CA.id,body,CA.title,CA.keyword,CA.contrib_id author_id,CA.date publish_date,DATE_FORMAT(CA.date,'%c/%e/%Y') format_publish_date,CC.name author_name,CA.approved from etf_articles CA,contributors CC WHERE  CA.contrib_id=CC.id and CA.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1'
 AND item_type='".$this->contentType."') order by id asc limit 0,1000";
		$resArticleMeta=exec_query($sqlArticleMeta);
		foreach($resArticleMeta as $value)
		{
			$metaData['item_id']=$value['id'];
			$metaData['is_live']=$value['approved'];
			$metaData['item_type']=$this->contentType;
			$metaData['title']=htmlentities($value['title'],ENT_QUOTES);
			$metaData['section']=strtoupper('etf trader');
			$metaData['publish_date']=$value['publish_date'];
			$valuebody=strip_tag_style($value['body']);
			$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);
			$metaData['author_id']=$value['author_id'];
			$metaData['author_name']=$value['author_name'];
			$metaData['url']=makeArticleslinkETF($value['id'],$value['keyword']);
			$metaData['keywords']=$value['keyword'];
			$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
				if(strlen($metaData['keywords'])>0){
					$metaData['keywords'].=','.$metaData['section'];
				}
				else{
					$metaData['keywords'].=$metaData['section'];
				}
				$arrKeys=make_stock_array($value['body']);
				$strKeys=implode(',',$arrKeys);
				if(strlen($strKeys)>0){
					if(strlen($metaData['keywords'])>0){
						$metaData['keywords'].=','.$strKeys;
					}
					else{
						$metaData['keywords'].=$strKeys;
					}
				}

			$metaData['keywords']=htmlentities($metaData['keywords'],ENT_QUOTES);
			$metaData['description']=htmlentities($metaData['description'],ENT_QUOTES);
			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
			unset($metaData);
		}
	}


	public function setWallOfWorryMeta($maxObjectID)
	{
		global $HTPFX,$HTHOST;
		if($this->contentId)
		{
			$sqlWorryMeta="select worry_sequence,publish_date,creation_date,DATE_FORMAT(IF(publish_date>creation_date,publish_date,creation_date),'%c/%e/%Y') format_publish_date from worry_alert where id ='".$this->contentId."'";

			$resWorryMeta=exec_query($sqlWorryMeta,1);
		}


		if(is_array($resWorryMeta)){
			$worrySummary="select worry_summary from worry_items where worry_item_id='".$this->contentId."' and worry_id in (".$resWorryMeta['worry_sequence'].")";
			$getWorrySummary=exec_query($worrySummary);
		}
			foreach($getWorrySummary as $value)
			{
				$metaData['content'].=' '.$value['worry_summary'];

			}

			$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);
			$metaData['item_id']=$this->contentId;
			$metaData['item_type']=$this->contentType;
			$metaData['title']="Lloyds Wall of Worry - ".$resWorryMeta['format_publish_date'];
			$metaData['url']="/investing/lloyds-wall-of-worry/".$resWorryMeta['format_publish_date']."/";
			$metaData['author_id']="367";
			$metaData['author_name']="Lloyd Khaner";
			if($resWorryMeta['publish_date']=="0000-00-00 00:00:00"){
				$metaData['publish_date']=$resWorryMeta['creation_date'];
			}else{
				$metaData['publish_date']=$resWorryMeta['publish_date'];
			}
			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);

	}




	function getMetaDataAnalytics($pageName){
		$sqlGetMeta="SELECT title title,metakeywords keywords,metadesc description
FROM layout_meta,layout_pages WHERE layout_meta.page_id=layout_pages.id AND layout_pages.name='$pageName'";
		$resGetMeta=exec_query($sqlGetMeta,1);
		$strMetaTag='';
		if($resGetMeta){
			$strMetaTag="<meta name='TITLE' content='".$resGetMeta[title]."'>";
			$strMetaTag.="<meta name='description' content='".$resGetMeta[description]."'>";
			$strMetaTag.="<meta name='keywords' content='".$resGetMeta[keywords]."'>";
		}
		return $strMetaTag;
	}

	 function getMetaData()
 	 {
			global $default_section,$default_description,$default_keywords,$D_R,$HTPFX,$HTHOST,$pageName,$article;

			$obMemCache= new Cache();
			$resGetMeta = $obMemCache->getPageMetaDataCache($this->contentType,$this->contentId);

			   $resGetMeta['keywords']=$resGetMeta['keywords'];
			   $resGetMeta['topic']=$resGetMeta['keywords'];
			   $resGetMeta['is_live']=$resGetMeta['is_live'];
			   $resGetMeta['publish_date']=$resGetMeta['publish_date'];
			   $resGetMeta['resource']=$resGetMeta['resource'];
			   $resGetMeta['url']=$resGetMeta['url'];

			   $resGetMeta['keywords']=str_replace('-',',',$resGetMeta['keywords']);

			   if(trim($resGetMeta['description'])=='') {
			   		$resGetMeta['description']=$default_description;
				}
			   // echo "neha".$resGetMeta['keywords'];
			   if(trim($resGetMeta['section'])=='' && $resGetMeta['item_type']=='1') $resGetMeta['section']=$default_section;
			   if(empty($resGetMeta['title'])){
			   		switch($this->contentType){
						case '21':
							include_once($D_R."/admin/lib/_contributor_class.php");
							$objContributor=new contributor();
							$contributorDetails=$objContributor->getContributor($this->contentId);
							if($contributorDetails['has_bio']=='0' or $contributorDetails['suspended']=='1' ){
								$objPage= new Page("contributor");
								$pageMetaData=$objPage->getMetaData();
								$resGetMeta['title']=$pageMetaData['title'];
								$resGetMeta['description']=$pageMetaData['description'];
								$resGetMeta['keywords']=$pageMetaData['keywords'];
							}else{
								$resGetMeta['title']=$contributorDetails['name']." - Minyanville's Wall Street";
								$resGetMeta['description']=substr(strip_tags($contributorDetails['description']),0,250);
								$resGetMeta['keywords']=$contributorDetails['name'];
							}
							$resGetMeta['url']='/gazette/bios.htm';
							break;
				   }
			   }
			   if($this->contentType == '18')
			   {
					if($resGetMeta['keywords']!='')
					{
						if($resGetMeta['tickers']){
							$resGetMeta['keywords']=$resGetMeta['keywords'].','.$resGetMeta['tickers'];
						}
						if($resGetMeta['resource']) {
							$resGetMeta['keywords']=$resGetMeta['keywords'].','.$resGetMeta['resource'];
						}
					}
					else
					{

						if($resGetMeta['tickers'] && $resGetMeta['resource']){
							$resGetMeta['keywords'] = $resGetMeta['tickers'].",".$resGetMeta['resource'];
						}
						elseif($resGetMeta['tickers']){
							$resGetMeta['keywords'] = $resGetMeta['tickers'];
						}
						elseif($resGetMeta['resource']) {
							$resGetMeta['keywords'] .= $resGetMeta['resource'];
						}
					}
			   }
			   if($article->no_follow_tag=="1")
			   	{
			   		$resGetMeta['robots']="INDEX,NOFOLLOW,noodp,noydir";
			   	}
			   	else
			   	{
			   		 $resGetMeta['robots']="noodp,noydir";
			   	}

			   if($this->contentType == '22')
			   {
			   global $categoryName,$pageName,$searchType,$search;
			       if($pageName=="techstrat_post"){
					   $titleText=$resGetMeta['title'];
					   switch($categoryName){
						  case 'Trades & Ideas':
							  $resGetMeta['title']="Trades and Ideas: ".$titleText." TechStrat Report - Minyanville's Wall Street";
							  $resGetMeta['description']=$titleText." -TechStrat with Sean Udall - Trade and Ideas - Minyanville's Wall Street";
						  break;
						  case 'Mailbag':
								$resGetMeta['title']="Mailbag: ".$titleText." TechStrat Report - Minyanville's Wall Street";
								$resGetMeta['description']=$titleText." -TechStrat with Sean Udall - MailBag - Minyanville's Wall Street";
						  break;
						  case 'Research Tank':
								$resGetMeta['title']="Research and Discussion: ".$titleText." TechStrat Report - Minyanville's Wall Street";
								$resGetMeta['description']=$titleText." - TechStrat with Sean Udall - Research Tank - Minyanville's Wall Street";
						  break;
					   }
					 $resGetMeta['robots']="NOINDEX,noodp,noydir";
				   }elseif($pageName=="techstrat_search"){
				  switch($searchType){

							case 'text':
								$resGetMeta['title']=$search." - Search of Sean Udall's TechStrat Report -
Minyanville's Wall Street";
								$resGetMeta['description']="TechStrat with Sean Udall - Product Search - Minyanville's Wall Street";
								$resGetMeta['keywords']=$search;
								$resGetMeta['url']="/techstrat/";

							break;
							case 'tag':
							    if(strpos($search,'/')){
									$search=substr($search,0,-1);
								}
								$resGetMeta['title']=$search." Information of Sean Udall's TechStrat Report - Minyanville's Wall Street";
								$resGetMeta['description']="TechStrat with Sean Udall - $search information - Minyanville's Wall Street";
								$resGetMeta['keywords']=$search;
								$resGetMeta['url']="/techstrat/tag/".$search;

							break;
							case 'tid':
							    $qry="select stocksymbol from ex_stock where id='".$search."'";
								$getSymbol=exec_query($qry,1);

								$resGetMeta['title']=$getSymbol['stocksymbol']." Information of Sean Udall's TechStrat Report - Minyanville's Wall Street";
								$resGetMeta['description']="TechStrat with Sean Udall - ".$getSymbol['stocksymbol']." information - Minyanville's Wall Street";
								$resGetMeta['keywords']=$getSymbol['stocksymbol'];
								$resGetMeta['url']="/techstrat/ticker/".$search;

							break;
							default:
							$resGetMeta['title']="Search of Sean Udall's TechStrat Report -
Minyanville's Wall Street";
								$resGetMeta['description']="TechStrat with Sean Udall - Product Search - Minyanville's Wall Street";
								$resGetMeta['keywords']=$search;
								$resGetMeta['url']="/techstrat/";


						}
						$resGetMeta['robots']="NOINDEX,noodp,noydir";

				   }


			   }
			   $page_url=$HTPFX.$HTHOST.$resGetMeta['url'];
			   if($page_url!=$_SERVER['SCRIPT_URI']){

			   	if($article->no_follow_tag=="1")
			   	{
			   		$resGetMeta['robots']="INDEX,NOFOLLOW,noodp,noydir";
			   	}
			   	else
			   	{
			   		$resGetMeta['robots']="NOINDEX,noodp,noydir";
			   	}
			   }
			   if(trim($resGetMeta['keywords'])=='') $resGetMeta['keywords']=$default_keywords;

			   return $resGetMeta;
	}
    function setMeta($pageId)
	{
		global $HTHOST,$HTPFX;
		$sqlGetMeta="select tag from ex_item_tags EIT,ex_tags ET where ET.id=EIT.tag_id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$this->contentId."' limit 0,6";
		$resGetMeta=exec_query($sqlGetMeta);
		$keywords="stock";
		foreach($resGetMeta as $vaule)
			$keywords.="-".$value['tag'];
		$sqlContentDetails="select * from '".$this->contentTable."' where id='".$this->contentId."'";
		$resContentDetails=exec_query($sqlContentDetails,1);
		$sqlGetPageDetails="select name,alias,url from layout_pages where id='".$pageId."'";
		$resGetPageDetails=exec_query($sqlGetPageDetails,1);
		setContentformat($resContentDetails);
		$parameterURL=buildParameterURL();
		$metaURL=$HTPFX.$HTHOST."/".$keywords."/".$this->publishTime."/".$resGetPageDetails.$parameterURL;
		$EIM['item_id']=$this->contentId;
		$EIM['item_type']=$this->contentType;
		$EIM['title']=$this->title;
		$EIM['desc']=$this->desc;
		$EIM['url']=$metaURL;
		$EIM['keywords']=$this->keywords;
		$EIM['tickers']=$this->tickers;
		insert_query('ex_item_meta',$EIM);
		return $EIM;
	}
	 function buildParameterURL()
	{

	}

	 function setContentformat($contentdetails)
	{
	}

	 function getMetaURL($pageId)
	{
		$sqlGetMetaURL="select * from content_metaurl where ";
		return $metaURL;
	}

	function __destruct(){
	}

	function getSearchTitleCount($q,$contrib_id,$mo,$day,$year,$object_type,$offset)
	{
	  $q=htmlentities($q,ENT_QUOTES);
		$sqlGetSearchTitleCount="select count(*) num from ex_item_meta where is_live='1' and MATCH(title) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
		if(is_numeric($contrib_id)){
				    $sqlGetSearchTitleCount.=" and author_id='$contrib_id'";
			}
			if($mo){
					$sqlGetSearchTitleCount.=" and month(publish_date)='$mo'";
			}
			if($day){
					$sqlGetSearchTitleCount.=" and day(publish_date)='$day'";
			}
			if($year){
					$sqlGetSearchTitleCount.=" and year(publish_date)='$year'";
			}
			if($object_type){
					$sqlGetSearchTitleCount.=" and item_type='$object_type'";
			}

		$resGetSearchTitleCount=exec_query($sqlGetSearchTitleCount,1);
		return $resGetSearchTitleCount['num'];
	}

	function getSearchTitleContent($q,$contrib_id,$mo,$day,$year,$object_type,$offset)
	{
		  global $contentcount;
		 //  $offset=$offset*$contentcount;  /* code comment - offset already count no need to count again */
		  if(!$offset){
		  	$offset=0;
		  }
		  $q=htmlentities($q,ENT_QUOTES);

		$sqlGetSearchTitleContent="select item_id as object_id ,item_type as object_type,content,publish_date from ex_item_meta where  is_live='1' and MATCH(title) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
		if(is_numeric($contrib_id)){
				    $sqlGetSearchTitleContent.=" and author_id='$contrib_id'";
			}
			if($mo){
					$sqlGetSearchTitleContent.=" and month(publish_date)='$mo'";
			}
			if($day){
					$sqlGetSearchTitleContent.=" and day(publish_date)='$day'";
			}
			if($year){
					$sqlGetSearchTitleContent.=" and year(publish_date)='$year'";
			}
			if($object_type){
					$sqlGetSearchTitleContent.=" and item_type='$object_type'";
			}

			if(empty($mo) && empty($day) && empty($year)){
			    $sqlGetSearchTitleContent.=" and publish_date>=('".mysqlNow()."' - INTERVAL 1 YEAR)";
			}

			$sqlGetSearchTitleContent.=" order by publish_date desc";

			if(($contentcount)||($offset!=0))
			{

			 $sqlGetSearchTitleContent.=" limit $offset,$contentcount";
			}
			$resGetSearchTitleContent=exec_query($sqlGetSearchTitleContent);
			return $resGetSearchTitleContent;
	}

	function searchContent($q,$contrib_id,$mo,$day,$year,$object_type,$offset,$title,$searchArchive=null){
		  global $contentcount;
		  $result=array();
		  $resSearchTitleContent=array();
		  $offset=$offset*$contentcount;
		  if(!$offset){
		  	$offset=0;
		  }
			$qry="select item_id as object_id ,item_type as object_type,content,publish_date from ex_item_meta WHERE  is_live='1' ";
			if($q){
				$qry.=" AND content LIKE '%$q%' ";
			} elseif($title) {
				$qry.=" and  instr(LOWER(title),'".htmlentities($title,ENT_QUOTES)."')";
			}
			if(is_numeric($contrib_id) && $object_type != '23'){
				    $qry.=" and author_id='$contrib_id'";
			}
			if($mo){
					$qry.=" and month(publish_date)='$mo'";
			}
			if($day){
					$qry.=" and day(publish_date)='$day'";
			}
			if($year){
					$qry.=" and year(publish_date)='$year'";
			}
			
			if($object_type){
					if($object_type == '23')
					{
						$qry.=" and item_type IN('23',13)";
					}
					else
					{
					$qry.=" and item_type='$object_type'";
					}
			}
			if(empty($mo) && empty($day) && empty($year)){
				if($searchArchive=="1"){
			    	$qry.=" and publish_date<>''";
				}else{
					$qry.=" and publish_date>=('".mysqlNow()."' - INTERVAL 1 YEAR)";
				}
			}


			 $qry.=" AND item_type NOT IN('10') order by publish_date desc";
			$qry.=" limit $offset,$contentcount";
			 $result=exec_query($qry);
			if(isset($result)){
				return $result;
			}
	}

	function searchContentCount($q,$contrib_id,$mo,$day,$year,$object_type,$offset,$title,$searchArchive=null){
		//$title=mysql_real_escape_string($title);
        //echo $q."---";
		
		//echo $q=mysql_real_escape_string($q);
		$qry="select count(id) as count from ex_item_meta WHERE  is_live='1' ";
			if(($q)){
			
				//$qry.=" and MATCH(content) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
				$qry.=" and content LIKE '%$q%'";
			} elseif($title) {
				$qry.=" and  instr(LOWER(title),'".htmlentities($title,ENT_QUOTES)."')";
			}
			if(is_numeric($contrib_id) && $object_type != '23'){
				    $qry.=" and author_id='$contrib_id'";
			}
			if($mo){
					$qry.=" and month(publish_date)='$mo'";
			}
			if($day){
					$qry.=" and day(publish_date)='$day'";
			}
			if($year){
					$qry.=" and year(publish_date)='$year'";
			}
			
			if($object_type){
					if($object_type == '23')
					{
						$qry.=" and item_type IN('23',13)";
					}
					else
					{
					$qry.=" and item_type='$object_type'";
					}
			}
			if(empty($mo) && empty($day) && empty($year)){
				if($searchArchive=="1"){
			    	$qry.=" and publish_date<>''";
				}else{
					$qry.=" and publish_date>=('".mysqlNow()."' - INTERVAL 1 YEAR)";
				}
			}

			$qry.=" AND item_type NOT IN('10') order by publish_date desc";
		 	$result=exec_query($qry,1);
			if(isset($result)){
				return $result;
			}
	}

	function getMetaSearch($object_id,$object_type){
			if($object_type=='11')
			{
				$sqlGetMeta="select EIM.item_id,EIM.item_type,mvtv.title,EIM.description,EIM.keywords,
EIM.tickers,EIM.author_id,EIM.publish_date as date,EIM.author_name author,
EIM.author_name,EIM.url,mvtv.stillfile,mvtv.thumbfile from ex_item_meta EIM,mvtv  where EIM.item_id=mvtv.id
and EIM.item_id='".$object_id."' and EIM.item_type='$object_type'";
			} else {
				$sqlGetMeta="select item_id,item_type,title,description,keywords,
			tickers,author_id,publish_date as date,author_name author,
			author_name,url from ex_item_meta where item_id='".$object_id."' and item_type='$object_type'";

			}
	   		$getresult=exec_query($sqlGetMeta,1);
		if(isset($getresult)){
			$getresult['pubDate'] = $getresult['date'];
			$getresult['date'] = strtotime($getresult['date']);
			return $getresult;
		}
	}
	
	function getEduBody($id)
	{
		$sqlGetMeta="select body from edu_alerts where id='".$id."' ";
	   	$getresult=exec_query($sqlGetMeta,1);
		if(isset($getresult)){
			return $getresult;
		}
	}
	
	function getEduCategory($id)
	{
		$sqlGetMeta="SELECT env.`menu_name` FROM `edu_alerts` ea,`edu_nav_category` env
WHERE ea.id='".$id."' AND FIND_IN_SET(env.id,ea.category_id)";
	   	$getresult=exec_query($sqlGetMeta);
	   	foreach($getresult as $k=>$v)
	   	{
	   		$categoryArr[] = $v['menu_name'];
	   	}
	   	$categoryList = implode(" , ",$categoryArr);
		if(isset($categoryList)){
			return $categoryList;
		}
	}
	
	function getProfileSearch($object_id,$object_type,$q){
			$qry="select S.id,S.id authorid,concat(S.fname,' ',(case when S.lname is null then '' else lname end)) author from subscription S,ex_item_meta EIM  where S.id=EIM.item_id and EIM.item_type='$object_type' and EIM.item_id='$object_id'";
			$result=exec_query($qry,1);
			if(isset($result)){
				return $result;
			}
	}

	function getAttributeProfile($authorid,$val){
    		$match=array();
			if($val){
				$qry="select EPAE.attribute,EPA.value from ex_profile_attribute_mapping EPA,ex_user_profile EUP,ex_profile_attribute EPAE where EPA.profile_id=EUP.id and EPA.attribute_id = EPAE.id and EUP.subscription_id='$authorid' and ( LOWER(EPA.value) like '%$val%')";
				$profileAttribute=exec_query($qry);
				$match=$profileAttribute;
				$matchcount=0;
				$matchcount=count($profileAttribute);
				$valProfile['matchcount']=$matchcount;
				$valProfile['match']=$match;
				if(isset($valProfile)){
				  return $valProfile;
				}
			}

	}

	function getObjectLogo($objectID,$objectType)
	{
		global $IMG_SERVER;
		switch($objectType)
		{
			case '1':
				$objLogo=getArticleSecLogo($objectID);
				break;
			case '2':
				$objLogo=$IMG_SERVER."/images/logo/search_buzz.gif";
				break;
			case '6':
				$objLogo=$IMG_SERVER."/images/logo/search_blog.gif";
				break;
			case '7':
				$objLogo=$IMG_SERVER."/images/community_images/friends.gif";
				break;
			case '13':
				$objLogo=$IMG_SERVER."/images/logo/search_flexfolio.gif";
				break;
			case '14':
				$objLogo=$IMG_SERVER."/images/logo/search_cooper.gif";
				break;
			case '15':
				$objLogo=$IMG_SERVER."/images/logo/search_optionsmith.gif";
				break;
			case '16':
				$objLogo=$IMG_SERVER."/images/logo/bmtp_search_logo.gif";
				break;
			case '17':
				$objLogo=$IMG_SERVER."/images/logo/jack_search_logo.gif";
				break;
			case '18':
				$objLogo=$IMG_SERVER."/images/logo/mv_premium_logo.jpg";
				break;
			case '20':
				$objLogo=$IMG_SERVER."/images/logo/etf_search_logo.gif";
				break;
			case '22':
				$objLogo=$IMG_SERVER."/images/logo/techstrat.jpg";
				break;
			case '23':
				$objLogo=$IMG_SERVER."/images/logo/active_investor.png";
				break;
			case '24':
				$objLogo=$IMG_SERVER."/images/logo/housing_market.gif";
				break;
			case '26':
				$objLogo=$IMG_SERVER."/images/logo/wall_off_worry.png";
				break;
			case '27':
				$objLogo=$IMG_SERVER."/images/logo/garyk_logo.png";
				break;
			case '28':
				$objLogo=$IMG_SERVER."/images/logo/buyhedge_logo.gif";
				break;
			case '29':
				$objLogo=$IMG_SERVER."/images/logo/pT_searchIcon.png";
				break;
			case '30':
				$objLogo=$IMG_SERVER."/images/logo/ewi_searchIcon.gif";
				break;
			case '31':
				$objLogo=$IMG_SERVER."/images/logo/keene_searchIcon.gif";
				break;
			default:
				$objLogo=NULL;
		}
		return 	$objLogo;

	}

	function removeUnapprovedItems($itemid,$tablename){
	    $qryItemtype="select id from ex_item_type where item_table='".$tablename."'";
		$getItemtype=exec_query($qryItemtype,1);
		//$qry="delete from ex_item_meta where find_in_set(item_id,'$itemid') and item_type='".$getItemtype['id']."'";
		//exec_query_nores($qry);
		$params['is_live']="0";
		$conditions['item_type']=$getItemtype['id'];
		$conditions['item_id']=$itemid;
		update_query('ex_item_meta',$params,$conditions,$keynames=array());

	}
	function getDailyFeedURL($item_id)
	{
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='18'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$postQuery = "SELECT id, IF(publish_date!='0000-00-00 00:00:00',publish_date,creation_date) publish_date,title from daily_feed where id =".$item_id;
			$postRow = exec_query($postQuery,1);
			$urltitle=$this->getFirstFiveWords($postRow['title']);
			$url="/mvpremium/".date('Y/m/d',strtotime($postRow['publish_date'])).'/'.$urltitle.'/';
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}
	function getVideoURL($item_id)
	{
		$sql="select url from content_seo_url where item_id='".$item_id."' and item_type='11'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$postQuery = "SELECT id,title from mvtv where id =".$item_id;
			$postRow = exec_query($postQuery,1);
			$urltitle=$this->getFirstFiveWords($postRow['title']);
			$url="/video/".$urltitle.'/';
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}

	function getTechStratUrl($item_id)
	{
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='22'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$postQuery = "SELECT id,publish_date,title,category_id from techstrat_posts where id =".$item_id;
			$postRow = exec_query($postQuery,1);
			$objContent = new Content('techstrat_posts',$fid);
			$urltitle=$objContent->getFirstFiveWords($postRow['title']);
			$postCategory=explode(',',$postRow['category_id']);
			$categoryId=$postCategory['0'];
			switch($categoryId)
			{
					case '1':
						$cat="trades-ideas";
					break;
					case '2':
						$cat="mailbag";
					break;
					case '3':
						$cat="research-tank";
					break;
			}
			$url = "/techstrat/".date('Y/m/d',strtotime($postRow['publish_date']))."/".$cat."/".$urltitle;
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}

	function getBuyHedgeUrl($item_id)
	{
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
	  $sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='".$this->contentType."'";
	  $row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
		  $postQuery = "SELECT id,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,title,category_id from buyhedge_posts where id =".$item_id;

			$postRow = exec_query($postQuery,1);
			$objContent = new Content('buyhedge_posts',$fid);
			$urltitle=$objContent->getFirstFiveWords($postRow['title']);
			$postCategory=explode(',',$postRow['category_id']);
			$categoryId=$postCategory['0'];
			switch($categoryId)
			{
					case '1':
						$cat="features";
					break;
					case '2':
						$cat="trading-with-etfs";
					break;
					case '3':
							$cat="investing-with-etfs";
						break;
					case '4':
							$cat="hedging-with-etfs";
						break;
					case '5':
							$cat="tips-and-advice";
						break;
					default:
						$cat="features";
			}
	        $url = "/buyhedge/".date('Y/m/d',strtotime($postRow['display_date']))."/".$cat."/".$urltitle.'/';
		$url = $this->getUniqueURL($url);
		}
		return $url;
	}


    function getGaryKUrl($item_id)
	{
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
	  $sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='27'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
		  $postQuery = "SELECT id,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,title,category_id from garyk_posts where id =".$item_id;
			$postRow = exec_query($postQuery,1);
			$objContent = new Content('garyk_posts',$fid);
			$urltitle=$objContent->getFirstFiveWords($postRow['title']);
			$postCategory=explode(',',$postRow['category_id']);
			$categoryId=$postCategory['0'];
			switch($categoryId)
			{
					case '3':
						$cat="reports";
					break;
					case '4':
						$cat="mailbag";
					break;
					default:
						$cat="reports";
			}
	       $url = "/garyk/".date('Y/m/d',strtotime($postRow['display_date']))."/".$cat."/".$urltitle.'/';
		$url = $this->getUniqueURL($url);
		}
		return $url;
	}

	/* Keene on Options URL*/
	function getKeeneAlertUrl($item_id)
	{
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='".$this->contentType."'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$qryAlert = "SELECT id,IF(publish_date>creation_date,publish_date,creation_date) publish_date,title,category_id from keene_alerts where id =".$item_id;
			$resAlert = exec_query($qryAlert,1);
			$urltitle = $this->getFirstFiveWords($resAlert['title']);
			$postCategory = explode(',',$resAlert['category_id']);
			$categoryId = $postCategory['0'];
			switch($categoryId)
			{
					case '1':
						$cat="trade-updates";
					break;
					case '2':
						$cat="weekly-recap";
					break;
					case '4':
						$cat="resources";
					break;
					default:
					break;
			}
			$url = "/keene/".date('Y/m/d',strtotime($resAlert['publish_date']))."/".$cat."/".$urltitle;
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}
	
	/*Peter Tchir URL*/
	function getPeterTchirAlertUrl($item_id)
	{
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='".$this->contentType."'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$qryAlert = "SELECT id,IF(publish_date>creation_date,publish_date,creation_date) publish_date,title,category_id from peter_alerts where id =".$item_id;
			$resAlert = exec_query($qryAlert,1);
			$urltitle = $this->getFirstFiveWords($resAlert['title']);
			$postCategory = explode(',',$resAlert['category_id']);
			$categoryId = $postCategory['0'];
			switch($categoryId)
			{
					case '1':
						$cat="weekly-report";
					break;
					case '2':
						$cat="intra-week-updates";
					break;
			}
			$url = "/tchir-fixed-income/".date('Y/m/d',strtotime($resAlert['publish_date']))."/".$cat."/".$urltitle;
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}

	/*Elliot Wave URL*/
	function getElliottWaveAlertUrl($item_id)
	{
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='".$this->contentType."'";
		$row = exec_query($sql,1);
		if($row['url'])
		{
			$url = $row['url'];
		}
		else
		{
			$qryAlert = "SELECT id,IF(publish_date>creation_date,publish_date,creation_date) publish_date,title,section_id from elliot_alert where id =".$item_id;
			$resAlert = exec_query($qryAlert,1);
			$urltitle = $this->getFirstFiveWords($resAlert['title']);
			$postSection = explode(',',$resAlert['section_id']);
			$sectionId = $postSection['0'];
			switch($sectionId)
			{
					case '1':
						$sec="home";
					break;
					case '2':
						$sec="extras";
					break;
			}
			$url = "/ewi/".date('Y/m/d',strtotime($resAlert['publish_date']))."/".$sec."/".$urltitle;
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}

	function getEduAlertUrl($item_id){
		$sql="select url from ex_item_meta where item_id='".$item_id."' and item_type='".$this->contentType."'";
		$row = exec_query($sql,1);
		if($row['url']){
			$url = $row['url'];
		}else{
			$qryAlert = "SELECT ea.id,IF(ea.publish_date>ea.creation_date,ea.publish_date,ea.creation_date) publish_date,ea.title,ea.category_id from edu_alerts ea where ea.id='".$item_id."'";
			$resAlert = exec_query($qryAlert,1);
			$urltitle = $this->getFirstFiveWords($resAlert['title']);
			$postCat = explode(',',$resAlert['category_id']);
			if(!empty($postCat)){
				$getAlias = "select ec.menu_alias from edu_nav_category ec where ec.id='".$postCat['0']."'";
				$resAlias = exec_query($getAlias,1);
			}
			$url = "/edu/".date('Y/m/d',strtotime($resAlert['publish_date']))."/".$resAlias['menu_alias']."/".$urltitle;
			$url = $this->getUniqueURL($url);
		}
		return $url;
	}


   function getFirstFiveWords($body){
	    $title="";
		$title=strtolower(str_replace("-"," ",$body));
		$title=$title." &nbsp; ";
		preg_match("/^(\S+\s+){0,5}/",trim($title), $matches);
		//$matches[0]=str_replace(" ","-",trim($matches[0]));
		$matches[0]=mswordReplaceSpecialChars($matches[0]);
		$matches[0]=$this->clean_title_url($matches[0]);
		return trim($matches[0]);
       }

   function getUniqueURL($url){
   		if(substr($url, -1)=="/")
   		{
   			$matchurl=substr($url,0,-1);
   		}else{
   			$matchurl=$url;
   		}
		$sqlCheckUniqueURL="SELECT * FROM ex_item_meta WHERE url REGEXP '".$matchurl."[0-9]*'";
		if($this->contentId){
			$sqlCheckUniqueURL.=" AND item_id!='".$this->contentId."' and item_type='".$this->contentType."'";
		}
		$resCheckUniqueURL=exec_query($sqlCheckUniqueURL);
		if(count($resCheckUniqueURL)>0){
			$uniqueURL=$matchurl."-".count($resCheckUniqueURL);
		}else{
			$uniqueURL=$matchurl;
		}
		if(substr($url, -1)=="/")
		{
		   	$uniqueURL.="/";
   		}
   		return $uniqueURL;
   }

    function getCountWords($body,$count="25"){
	    $result="";
		$result=strip_tags($body);
		preg_match("/^(\S+\s+){0,$count}/",trim($result), $matches);
		return trim($matches[0]);
   }

	# This function makes any text into a url frienly
	# This script is created by wallpaperama.com
	function clean_title_url($text)
	{
	$code_entities_match = array(' ','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=','--');
	$code_entities_replace = array('-','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','-');
	$text = str_replace($code_entities_match, $code_entities_replace, trim($text));
	return $text;
	}


	function updateContentSeoUrl($item_id,$item_type,$url){
	    $sql="select id from content_seo_url where item_id='".$item_id."' and item_type='".$item_type."'";
		$result=exec_query($sql,1);
		if(!$result['id']){
			$pars["item_id"]=$item_id;
			$pars["item_type"]=$item_type;
			$pars["url"]=$url;
			$pars["time"]=mysqlNow();
			$id=insert_query("content_seo_url",$pars);
		}else{ // content already live - not update meta url=- return id to check
			return $result['id'];
		}
	}


	function getTableData($id,$tableName){
	    if($id){
			$qry="select * from $tableName where id='".$id."'";
			$result=exec_query($qry,1);
			switch($this->contentType)
			{
				case '18':
					$objDailyFeed = new Dailyfeed();
					$result[topic]=$objDailyFeed->getTopics('daily_feed',$id);
					$result[ticker]=$objDailyFeed->getTickers($id,$this->contentType);
					$sourceURL	=	$objDailyFeed->getResource($id,$this->contentType);
					$result[source]			=	$sourceURL['source'];
					$result[source_link]		=	$sourceURL['source_link'];
					break;
				case '23':
					$objPepfil 		= new pepFil('pep_fil_articles');
					$result[ticker]	= $objPepfil->getPepFilTickers($id);
					break;
				case '22':
					$objTechStart 		= new techstartData('techstrat_posts');
					$result[topic]		= $objTechStart->getTechStartTopics($id);
					$result[ticker]		= $objTechStart->getTechStartTicker($id);
					break;
				default:
				$result=NULL;
			}
		}
		if(is_array($result)){
			return $result;
		}
		return 0;
	}

	function userAuditAlerts($itemId,$currentValue,$updatedValue,$action){
		$params['user_id']=$_SESSION['AID'];
		$params['item_id']=$itemId;
		$params['item_type']=$this->contentType;
		$params['current_value']=htmlentities(print_r($currentValue,true),ENT_QUOTES);
		$params['updated_value']=htmlentities(print_r($updatedValue,true),ENT_QUOTES);
		$params['action']=$action;
		if($itemId){
			$id=insert_query("user_audit_alerts",$params,$safe=0);
		}
	}
	//function for Premium content in article stream Start
	function updatePremiumContent($article,$id){
		$article['item_id'] = $id;
		insert_or_update('ex_item_premium_content',$article,array(item_id=>$id));
		$obArticleCache = new ArticleCache();
		$obArticleCache->setLatestPremiumContent();
	}

	function insertPremiumContent($post_premium,$id){
		$post_premium['item_id'] = $id;
		insert_query('ex_item_premium_content',$post_premium,$safe=0);
		$obArticleCache = new ArticleCache();
		$obArticleCache->setLatestPremiumContent();
	}

	function getSyndicate($item_id,$item_type){
		$qry = "select id from ex_item_premium_content where item_id='".$item_id."' and item_type='".$item_type."'";
		$result = exec_query($qry,1);
		if($result){
			return $result['id'];
		}else{
			return false;
		}
	}

	function setSyndicate($item_id,$item_type){
		$qry = "select id from ex_item_premium_content where item_id='".$item_id."' and item_type='".$item_type."' and is_deleted='0'";
		$result = exec_query($qry,1);
		if($result){
			return $result['id'];
		}else{
			return false;
		}
	}

	function deletePremiumContent($item_id,$item_type){
		$qry = "DELETE FROM ex_item_premium_content WHERE item_id='".$item_id."' and item_type='".$item_type."'";
		exec_query($qry);
		$obArticleCache = new ArticleCache();
		$obArticleCache->setLatestPremiumContent();
	}


	function  getContentMetaFromURL($url){
		$sqlGetContentMeta="SELECT * FROM ex_item_meta WHERE url = '".$url."' order by id desc";
		$resGetContentMeta=exec_query($sqlGetContentMeta,1);
		return $resGetContentMeta;
	}

	//function for Premium content in article stream End


	public function setbuyhedgeMeta($maxObjectID = NULL)
		{
		if($this->contentId)
		{
		 $sqlBuyHedgeMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from buyhedge_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id ='".$this->contentId."'";
		}
		else
		{
		 $sqlBuyHedgeMeta="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.category_id,C.name author_name,DATE_FORMAT(a.updation_date,'%c/%e/%Y') format_updation_date from buyhedge_posts a, contributors C where a.is_deleted='0' and C.id=a.contrib_id and a.id NOT IN (SELECT item_id FROM ex_item_meta WHERE is_live='1' AND item_type='".$this->contentType."') order by id asc limit 0,1000";
		}
		$resBuyHedgeMeta=exec_query($sqlBuyHedgeMeta);
			foreach($resBuyHedgeMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				$metaData['is_live']=$value['is_approved'];
				$metaData['item_type']=$this->contentType;
				$metaData['title']=addslashes(mswordReplaceSpecialChars(strip_tags($value['title'])));
				$metaData['publish_date']=$value['creation_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=addslashes($value['author_name']);

				$valuebody=strip_tag_style($value['body']);
				$metaData['description']=addslashes(mswordReplaceSpecialChars(strip_tags(substr(strip_tags($valuebody),0,150))));
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];

				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='".$this->contentType."' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);

				$keywordsName=array();
				if($resGetTags){
					// unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						$keywordsName[]=$valueTags['tag'];
					}
					$metaData['keywords']=implode(",",$keywordsName);
					$metaData['content']=implode(",",$keywordsName). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}else{
					$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}


				$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."' and item_type='".$this->contentType."'";
				$getTicker=exec_query($resgetticker);
				if($getTicker)
				{
					$tickerName=array();
					foreach($getTicker as $valueTicker){
						$tickerName[]=$valueTicker['stocksymbol'];
					}
					$metaData['tickers']=implode(",",$tickerName);
				}
				$url=$this->getBuyHedgeUrl($value['id']);

				$metaData['url']=$url;
                $metaData['content']=implode(",",$tickerName). " \n " .$metaData['content'];
				//$metaData['keywords']=addslashes($metaData['keywords']);
				$metaData['content']=addslashes(mswordReplaceSpecialChars(strip_tags($metaData['content'])));
				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);
				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

			insert_or_update("ex_item_meta",$metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));

			unset($metaData);

	}
	}

	/*Elliott Wave meta Data */
	public function setElliottWaveMeta($maxObjectID = NULL){
		if($this->contentId){
 		 	$sqlElliottMeta=" SELECT a.id, a.creation_date, IF(a.publish_date>a.creation_date,a.publish_date,a.creation_date) publish_date, a.title, a.body, a.is_approved, a.is_deleted, a.contrib_id, a.is_live, a.section_id, a.category_id, C.name author_name, DATE_FORMAT(a.publish_date,'%c/%e/%Y') format_updation_date FROM elliot_alert a, `contributors` C WHERE a.is_deleted='0' AND C.id=a.contrib_id AND a.id ='".$this->contentId."'";
		}
		$resEliottMeta=exec_query($sqlElliottMeta);
		if(!empty($resEliottMeta)){
			foreach($resEliottMeta as $value){
				$metaData['item_id'] = $value['id'];
				$metaData['is_live'] = $value['is_approved'];
				$metaData['item_type'] = $this->contentType;
				$metaData['title'] = $value['title'];
				$metaData['publish_date'] = $value['publish_date'];
				$metaData['author_id'] = $value['contrib_id'];
				$metaData['author_name'] = $value['author_name'];
				$body = strip_tags(strip_tag_style($value['body']));
				$words=explode(" ",$body,31);
		        unset($words[30]);
		        $body=implode(" ",$words);
				$metaData['description'] = $body;
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];

				$url=$this->getElliottWaveAlertUrl($value['id']);
				$metaData['url']=$url;
				$metaData['content']=strip_tags($metaData['content']);

				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);

				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

				insert_or_update("ex_item_meta", $metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
		}
	}

	/*Peter Tchir meta Data */
	public function setPeterTchirMeta($maxObjectID = NULL){
		if($this->contentId){
 			$sqlPeterMeta=" SELECT a.id, a.creation_date, IF(a.publish_date>a.creation_date,a.publish_date,a.creation_date) publish_date, a.title, a.body, a.is_approved, a.is_deleted, a.contrib_id, a.position, a.is_live, a.category_id, C.name author_name, DATE_FORMAT(a.publish_date,'%c/%e/%Y') format_updation_date FROM peter_alerts a, `contributors` C WHERE a.is_deleted='0' AND C.id=a.contrib_id AND a.id ='".$this->contentId."'";
		}
		$resPeterMeta=exec_query($sqlPeterMeta);
		if(!empty($resPeterMeta)){
			foreach($resPeterMeta as $value){
				$metaData['item_id'] = $value['id'];
				$metaData['is_live'] = $value['is_approved'];
				$metaData['item_type'] = $this->contentType;
				$metaData['title'] = $value['title'];
				$metaData['publish_date'] = $value['publish_date'];
				$metaData['author_id'] = $value['contrib_id'];
				$metaData['author_name'] = $value['author_name'];
				$body = strip_tags(strip_tag_style($value['body']));
				$words=explode(" ",$body,31);
		        unset($words[30]);
		        $body=implode(" ",$words);
				$metaData['description'] = $body;
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];

				$url=$this->getPeterTchirAlertUrl($value['id']);
				$metaData['url']=$url;
				$metaData['content']=strip_tags($metaData['content']);

				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);

				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

				insert_or_update("ex_item_meta", $metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
		}
	}
	/*Keene on Options meta Data */
	public function setKeeneMeta($maxObjectID = NULL){
		$this->contentId;
		
		if($this->contentId){
 			$sqlKeeneMeta=" SELECT a.id, a.creation_date, IF(a.publish_date>a.creation_date,a.publish_date,a.creation_date) publish_date, a.title, a.body, a.is_approved, a.is_deleted, a.contrib_id, a.is_live, a.category_id, C.name author_name, DATE_FORMAT(a.publish_date,'%c/%e/%Y') format_updation_date FROM keene_alerts a, `contributors` C WHERE a.is_deleted='0' AND C.id=a.contrib_id AND a.id ='".$this->contentId."'";
		}
		$resKeeneMeta=exec_query($sqlKeeneMeta);
		if(!empty($resKeeneMeta)){
			foreach($resKeeneMeta as $value){
				$metaData['item_id'] = $value['id'];
				$metaData['is_live'] = $value['is_approved'];
				$metaData['item_type'] = $this->contentType;
				$metaData['title'] = $value['title'];
				$metaData['publish_date'] = $value['publish_date'];
				$metaData['author_id'] = $value['contrib_id'];
				$metaData['author_name'] = $value['author_name'];
				$body = strip_tags(strip_tag_style($value['body']));
				$words=explode(" ",$body,31);
		        unset($words[30]);
		        $body=implode(" ",$words);
				$metaData['description'] = $body;
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];

				$url=$this->getKeeneAlertUrl($value['id']);
				$metaData['url']=$url;
				$metaData['content']=strip_tags($metaData['content']);

				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);

				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

				insert_or_update("ex_item_meta", $metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
		}
	}
	
/* Education center meta Data */
	public function setEduMeta($maxObjectID = NULL){
		if($this->contentId){
 		 	$sqlEduMeta="SELECT a.id, a.creation_date, IF(a.publish_date>a.creation_date,a.publish_date,a.creation_date) publish_date, a.title, a.body, a.is_approved, a.is_deleted, a.contrib_id, a.is_live, a.category_id, C.name author_name, DATE_FORMAT(a.publish_date,'%c/%e/%Y') format_updation_date FROM edu_alerts a, `contributors` C WHERE a.is_deleted='0' AND C.id=a.contrib_id AND a.id ='".$this->contentId."'";
		}
		$resEduMeta=exec_query($sqlEduMeta);
		if(!empty($resEduMeta)){
			foreach($resEduMeta as $value){
				$metaData['item_id'] = $value['id'];
				$metaData['is_live'] = $value['is_approved'];
				$metaData['item_type'] = $this->contentType;
				$metaData['title'] = $value['title'];
				$metaData['publish_date'] = $value['publish_date'];
				$metaData['author_id'] = $value['contrib_id'];
				$metaData['author_name'] = $value['author_name'];
				$body = strip_tags(strip_tag_style($value['body']));
				$words=explode(" ",$body,31);
		        unset($words[30]);
		        $body=implode(" ",$words);
		        $categoryList = $this->getEduCategory($value['id']);
				$metaData['description'] = $body;
				$metaData['content']=strip_tags($value['body']). " \n " .$value['title']. " \n ".$categoryList." \n " .$value['author_name'];

				$url=$this->getEduAlertUrl($value['id']);
				$metaData['url']=$url;
				$metaData['content']=strip_tags($metaData['content']);

				$sqlIsURLExist="select url from ex_item_meta where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsURLExist=exec_query($sqlIsURLExist);

				$sqlIsPublished="select id from content_seo_url where item_type='".$this->contentType."' and item_id='".$this->contentId."'";
				$resIsPublished=exec_query($sqlIsPublished,1);

				if($resIsPublished['id'] && count($resIsURLExist)){ // content already live not update meta url
					unset($metaData['url']);
				}

				insert_or_update("ex_item_meta", $metaData, array("item_id"=>$metaData['item_id'],"item_type"=>$this->contentType));
				unset($metaData);
			}
		}
	}
}

Class Page{
	function __construct($pageName,$topicPageID=NULL){

		$objCache = new Cache();
		$res=$objCache->getLayoutPageDetails($pageName,$topicPageID);

		$this->id=$res['id'];

	}
	 function getMetaURL()
	{
			global $HTHOST,$HTPFX;
			$sqlGetMeta="Select metakeywords,url from layout_meta where page_id='".$this->id."'";
			$resGetMeta=exec_query($sqlGetMeta,1);
			if(!$resGetMeta)
				return $HTPFX.$HTHOST;
			$keywords=substr($resGetMeta['metakeywords'],0,50);
			$keywords = trim($keywords);
			$pat[0] = "/^\s+/";
			$pat[1] = "/\s{2,}/";
			$pat[2] = "/\s+\$/";
			$pat[3] = "/,/";
			$rep[0] = "-";
			$rep[1] = "-";
			$rep[2] = "-";
			$rep[3] = "-";
			$keywords = preg_replace($pat,$rep,$keywords);
			$metaURL=$HTPFX.$HTHOST."/".$keywords."/".$resGetMeta['url'];
			return $metaURL;
	}

	 function getMetaData()
	{
	  global $HTPFX,$HTHOST;
	 	$sqlGetMeta="Select LM.title,LM.metakeywords keywords,LM.metadesc description, LM.show_canonical, LP.alias,LM.robots
					from layout_meta LM,layout_pages LP where LM.page_id=LP.id and LM.page_id='".$this->id."'";

		$resGetMeta=exec_query($sqlGetMeta,1);
		if($resGetMeta['alias']){
			$page_url=$HTPFX.$HTHOST.$resGetMeta['alias'];
		}else{
			$page_url=$HTPFX.$HTHOST.'/';
		}
		if($page_url!=$_SERVER['SCRIPT_URI']){
			$resGetMeta['robots']="NOINDEX,noodp,noydir";
		}
		return $resGetMeta;
	}

	function getMetaDataArticleListing()
	{
	    $result=$this->getMetaData();
		if($_GET['section']){
			$sqlTitle = "select name as section_name from section where section_id =".$_GET['section'];
			$getResult=exec_query($sqlTitle,1);

			if($getResult){
				 $result['title']="All Latest ".$getResult['section_name']. " Stories";
				 $result['alias']=$_SERVER['REQUEST_URI'];
			}
		}
		return $result;
	}

	 function getMetaDataDailyFeed($metatext)
	{
		global $HTPFX,$HTHOST,$topic,$source,$cid,$tid;
		$sqlGetMeta="Select LM.title,LM.metakeywords keywords,LM.metadesc description, LM.show_canonical, LP.alias,LM.robots
					from layout_meta LM,layout_pages LP where LM.page_id=LP.id and LM.page_id='".$this->id."'";
		$resGetMeta=exec_query($sqlGetMeta,1);
		if($resGetMeta){
			if($metatext){
				$resGetMeta['title']=$resGetMeta['title'].' - '.htmlentities(ucwords($metatext)).' - '.'Minyanville\'s Wall Street';
				$resGetMeta['keywords']="";
				$resGetMeta['description']=$resGetMeta['description'].' - '.htmlentities(ucwords($metatext)).' - The Daily Feed';
			}else{
				$resGetMeta['title']=htmlentities($resGetMeta['title']).' - '.'Minyanville\'s Wall Street';
				$resGetMeta['keywords']="";
			}
		}


		if($topic){
		    $resGetMeta['alias']='/mvpremium/tag/'.$topic;
		}elseif($source){
			$resGetMeta['alias']='/mvpremium/source/'.$source;
		}elseif($cid){
			$resGetMeta['alias']='/mvpremium/cid/'.$cid;
		}elseif($tid){
			$resGetMeta['alias']='/mvpremium/tid/'.$tid;
		}

		$page_url=$HTPFX.$HTHOST.$resGetMeta['alias'];
		if($page_url!=$_SERVER['SCRIPT_URI']){
 			$resGetMeta['robots']="NOINDEX,noodp,noydir";
		}
		return $resGetMeta;
	}


	function getMetaDataRegWelcome($orderStatus)
	{
			global $viaProductsName,$_SESSION;
			if($orderStatus==0){
				$resGetMeta['title']="Account information has been updated successfully - Minyanville.com";
			}elseif($orderStatus==1){
				$prodPurchased="";
				foreach($_SESSION['recently_added']['SUBSCRIPTION'] as $prodSession){
					if(array_key_exists($prodSession['oc_id'],$viaProductsName)){
						if($prodPurchased==""){
							$prodPurchased=$viaProductsName[$prodSession['oc_id']];
						}else{
							$prodPurchased.=' , '.$viaProductsName[$prodSession['oc_id']];
						}
					}
				}
				$resGetMeta['title']="Thank you for your Purchase of ".$prodPurchased." - Minyanville.com";
			}
			$resGetMeta['keywords']="";
			$resGetMeta['description']="";
			return $resGetMeta;
	}

	function getPageDetail($topicPageID=NULL)
	{
		$objCache = new Cache();
		return $objCache->getPageDetails($this->id,$topicPageID);
	}
	 function build_lang()
	{
		$objCache = new Cache();
		$this->lang=$objCache->getLang($this->id);
	}


	function getMetaSearch($q,$title,$contrib_id,$object_type){
	    global $D_R, $arSearchItems;
		$resGetMeta['title']="Minyanville Stories";
		$resGetMeta['description']="Latest Minyanville Articles";
		if($object_type){
			$resGetMeta['title']="Minyanville ".ucwords(strtolower($arSearchItems[$object_type]['display_name']));
			$resGetMeta['description']="Latest Minyanville ".ucwords(strtolower($arSearchItems[$object_type]['display_name']));
		}
		$resGetMeta['keywords']="Minyanville";


		if($contrib_id && $contrib_id!="-All Authors-" && ($q || $title)){
		    include_once($D_R."/admin/lib/_contributor_class.php");
			$objContributor=new contributor();
			$contributorDetails=$objContributor->getContributor($contrib_id);
			$resGetMeta['title']=ucwords(strtolower($resGetMeta['title'].' '.'on '.$q.$title.' by '.$contributorDetails['name']));
			$resGetMeta['keywords']=ucwords(strtolower($resGetMeta['keywords'].', '.$contributorDetails['name'].', '.$q.$title));
			$resGetMeta['description']=ucwords(strtolower($resGetMeta['description'].' '.'on '.$q.$title.' by '.$contributorDetails['name']));
		}elseif($q || $title){
			$resGetMeta['title']=ucwords(strtolower($resGetMeta['title'].' '.'on '.$q.$title));
			$resGetMeta['keywords']=ucwords(strtolower($resGetMeta['keywords'].', '.$q.$title));
			$resGetMeta['description']=ucwords(strtolower($resGetMeta['description'].' '.'on '.$q.$title));
       }elseif($contrib_id && $contrib_id!="-All Authors-"){
				include_once($D_R."/admin/lib/_contributor_class.php");
				$objContributor=new contributor();
				$contributorDetails=$objContributor->getContributor($contrib_id);
			$resGetMeta['title']=ucwords(strtolower($resGetMeta['title'].' '.'by '.$contributorDetails['name']));
			$resGetMeta['keywords']=ucwords(strtolower($resGetMeta['keywords'].', '.$contributorDetails['name']));
			$resGetMeta['description']=ucwords(strtolower($resGetMeta['description'].' '.'by '.$contributorDetails['name']));
       }
		return $resGetMeta;

	}

	function __destruct(){
	}

}
?>
