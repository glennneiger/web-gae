<?php

	function getWatchlistTicker($userid,$offset,$watchlistlimit){
		$qry="select s.stocksymbol as stocksymbol from ex_user_stockwatchlist sw,ex_stock s where sw.stock_id = s.id and sw.subscription_id='$userid' order by stocksymbol limit 0,$watchlistlimit";
		//echo '<br>wlQuery-----'.$qry;
		$result=exec_query($qry);
		if(isset($result)){
			return $result;
		}
	}

	function getWatchlistTickerCount($userid){
		$qry="select count(s.stocksymbol) as count from ex_user_stockwatchlist sw,ex_stock s where sw.stock_id = s.id and sw.subscription_id='$userid' order by stocksymbol";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function getArticleSubsectionid($id){
		$defaultsection['name']="MARKETS ";
		$defaultsection['page_id']=181;
		$defaultsection['article_pagename']='article_markets';
		$defaultsection['parent_section']='49';
		$qryGetSubSectionids="select navigation_section_id,subsection_ids from articles where id='$id'";
		$resGetSubSectionids=exec_query($qryGetSubSectionids,1);
		if(!empty($resGetSubSectionids['navigation_section_id']))
		{
			$navigationSectionId=$resGetSubSectionids['navigation_section_id'];
		}	
		else if(!empty($resGetSubSectionids['subsection_ids']))
		{
			$arSectionIds = explode(',',$resGetSubSectionids['subsection_ids']);
			$navigationSectionId = $arSectionIds[0];
		}
		$arSubSection = array();
		if($navigationSectionId)
		{
			$qryGetSubSection="select page_id,topic_page_id,name,article_pagename,parent_section from section where section_id ='".$navigationSectionId."' and type in ('subsection') and is_active='1'";
			$arSubSection=exec_query($qryGetSubSection,1);
		}
		if(count($arSubSection) > 0 && !empty($arSubSection['parent_section']))
		{
			return $arSubSection;
		}
		else
		{
			return $defaultsection;
		}
	}
	function getVideoSubsectionid($id,$section_id = NULL){

		$qry="select page_id,name,section_id,name from section where section_id=(select substring_index(cat_id, ',', 1) from mvtv where id='$id')";
		//$qry="select page_id,name,section_id,name from section where section_id=".$section_id;

		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}


	function getVideoSubsectionUrl($id){
		$qry="select id,name,url,alias from layout_pages where id='$id'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function getVideoSubsectionName($pagename){
		$qry="select id,name,url,alias from layout_pages where name='$pagename'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function getvideoSectionid($id){
		$qry="select section_id,name from section where page_id='$id'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function getSectionid($id){
		$qry="select parent_id,id from layout_menu where page_id='$id'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function getSubsectionUrl($id){
		$qry="select id,url,alias from layout_pages where id='$id'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result;
		}
	}

	function iboxcheckArticle($pageid,$attributes,$label,$sid,$eid,$targeturl){
		global $page_config,$HTPFX,$HTHOST,$HTPFXSSL;
		$strAttribute="&";
		if(is_array($attributes))
		foreach($attributes as $id=>$value)
		{
			$strAttribute.=$id."=".$value."&";
		}
		/*if(!$eid)
		{
		   $url=$HTPFXSSL.$HTHOST.'/subscription/register/iboxindex.htm';
		   $linkId='navlinkarticle';
		   $strLink = iboxCall($linkId,$label,$url,$height=488,$width=532,$targeturl);
		}
		elseif($eid && $sid)
		{
			$Link=$page_config[$pageid]['URL']."?".$strAttribute;
			$strLink = "<a href=".$Link.">$label</a>";
		}*/
		$Link=$page_config[$pageid]['URL']."?".$strAttribute;
		$strLink = "<a href=".$Link.">$label</a>";
		echo $strLink;


	}

	function iboxCheckAddtofriends($label)
	{
	  	global $HTPFX,$HTHOST,$HTPFXSSL;
		$url=$HTPFXSSL.$HTHOST."/subscription/register/login.htm";
		$ret='<a href="'.$url.'">'.$label.'</a>';
		return $ret;
	}


//**********************************************************************
function get_video_category_tabs($type,$subsection_type)
{
	//$sql="select * from section where type='$type' and subsection_type='$subsection_type' limit 0,5"; //OLD QUERY
$sql="select s.* from section AS s,layout_menu As l where s.type='subsection' and s.subsection_type='video' and l.active = '1' and l.navigation_type ='H' and s.page_id = l.page_id order by l.menuorder ASC";
	$result_cat= exec_query($sql);
	if(isset($result_cat)){
			return $result_cat;

		}
}
//**********************************************************************


	function getFlexfoliopageid($pname){
		$qry = "Select id from layout_pages where name ='".$pname."'";
		$result=exec_query($qry,1);
			if(isset($result)){
				return $result['id'];
			}

	}

	function getshowRelatedArticle($tags,$articleid){
	  global $relatedarticlelimit;
	  if(is_array($tags)){
		  foreach($tags as $value){
			$tag_id[]=$value['id'];
		  }
	    $tagid=implode(",",$tag_id);
		$sql="select distinct(item_id) from ex_item_tags where item_type='1' and tag_id in ($tagid) and item_id<>'$articleid'";
		$itemid=exec_query($sql);
		if(isset($itemid)){
		  foreach($itemid as $row){
			$aid[]=$row['item_id'];
		  }
			$aid=implode(",",$aid);
		    $qry="select id,title,keyword,blurb,date from articles where id in ($aid) limit $relatedarticlelimit;";
			$result=exec_query($qry);
		}
	}
		if(isset($result)){
			return $result;
		}else{
			$sqlGetLatest="select id,title,keyword,blurb,date from articles where id not in('".$articleid."') and approved='1' and is_live='1' and sent='1' order by id desc limit ".$relatedarticlelimit;
			$resGetLatest=exec_query($sqlGetLatest);
			return $resGetLatest;
		}
	}

	function getRelatedArticles($articleid){
		$sql="select distinct(item_id),A.id,A.title,A.keyword,A.blurb from ex_item_tags EITOutter, articles A where A.is_live = '1' AND A.approved = '1' AND A.id=EITOutter.item_id and item_type='1' and item_id!='".$articleid."' and A.publish_date > (now()- Interval 1 month) and tag_id in (select tag_id from ex_item_tags EIT where EIT.item_id='".$articleid."' and EIT.item_type='1') order by item_id desc limit 0,3";
		$result=exec_query($sql);
		return $result;
	}

	function getshowAlsoBy($articleid,$author,$authorid){
		global $alsobylimit;
		$sql = "select id, title,keyword,blurb,date from articles ";
		$sql .= "where approved='1' and is_live='1' ";
		$sql .= "and contributor = '" . $author . "' ";
		if ($article['authorid'] != 0) {
			$sql .= "or (contrib_id = " . $authorid . " and contributor=NULL) ";
		}
		$sql .= "and (id !=" . $articleid . ") ";
		$sql .= "order by date desc limit $alsobylimit";
		$results = exec_query($sql);
		if(isset($results)){
			return $results;
		}

	}

	function getBandMvalue(){
			$filename="http://download.finance.yahoo.com/d/quotes.csv?s=%5EIXIC&f=sl1d1t1c1ohgv&e=.csv";
			$open = file($filename);
			//fclose($filename);
			if(isset($open)){
				$totfetched=count($open);
				for($k=0;$k<$totfetched;$k++){
					$read = $open[$k];
					$read = str_replace("\"", "", $read);
					$read = explode(",", $read);
				}
			}
			return $read;
		}

 /****
 Function is remaed, as it clashes with the layout/layout_functions.pgp getModules() fucntion
 */
		function getModules_1() {
$listmodules = exec_query("SELECT id, name FROM layout_modules");
foreach($listmodules as $row){
$modules[$row['id']] = $row['name'];
}

return $modules;
}
 function obejctid($pname){
	$qry = "select id from ex_item_type where item_table='".$pname."'";
		$result=exec_query($qry,1);
		if(isset($result)){
			return $result['id'];
		}
}


function getWidgetBreakes($body,$string)
{
	$arr = explode($string,$body );
	return count($arr);
}


function getWidgetPos($body,$offset)
{
	$posbr=strposOffset('<br />', $body, $offset);
	if($posbr)
		return $posbr;
	else
		return strlen($body);
}

function strposOffset($search, $string, $offset)
{
    /*** explode the string ***/
    $arr = explode($search, $string);
    /*** check the search is not out of bounds ***/
    switch( $offset )
    {
        case $offset == 0:
        return false;
        break;

        case $offset > max(array_keys($arr)):
        return false;
        break;

        default:
        return strlen(implode($search, array_slice($arr, 0, $offset)));
    }
}

function getSection($id)
{
	$sqlGetSec="select S.section_id,S.name,S.description,S.page_id,LP.name pagename,LP.alias url from section S,layout_pages LP where S.page_id=LP.id and S.section_id='".$id."'";
	$resGetSec=exec_query($sqlGetSec,1);
	if(count($resGetSec)>0)
		return $resGetSec;
	else
		return NULL;
}

function getArticleSecLogo($articleID)
{
	global $IMG_SERVER;
	$sqlGetSubSec="select  subsection_ids from articles where id='".$articleID."'";
	$resGetSubSec=exec_query($sqlGetSubSec,1);
	$sqlGetSec="select parent_id,id from layout_menu where page_id in(select page_id from section where section_id in (".$resGetSubSec['subsection_ids'].") and subsection_type='article') ";
	$resGetSec=exec_query($sqlGetSec,1);
	if(!isset($resGetSec['parent_id']))
		$sec=NULL;
	elseif($resGetSec['parent_id']=='0')
		$sec=$resGetSec['id'];
	else
		$sec= $resGetSec['parent_id'];
	switch($sec)
	{
		case '1':
		case '49':
			$secLogo=$IMG_SERVER."/images/logo/markets_Ico_01.jpg";
			break;
		case '2':
		case '50':
			$secLogo=$IMG_SERVER."/images/logo/investing_Ico_01.jpg";
			break;
		case '51':
			$secLogo=$IMG_SERVER."/images/logo/specFeat_Ico_01.jpg";
			break;
		default:
			$secLogo=$IMG_SERVER."/images/logo/markets_Ico_01.jpg";
	}
	return $secLogo;
}



function getPageNameSectionId($pName){
	$sql="select parent_id from layout_menu where page_id =(select id  from layout_pages where name ='".$pName."') and active = 1 and navigation_type='H'";
	$result = exec_query($sql,1);
	//return $result['parent_id']=='1'? $result['parent_id']:NULL;
	return $result['parent_id'];
}

function getVidoeCategoryName($vid) {

$sql = "select name from section as s left join mvtv m on m.cat_id = s.section_id where m.id = '".$vid."'";
$result = exec_query($sql,1);
return $result['name'];
}
function getSession_fromdb($userid) {

$sql = "select session_id from subscription where id = '".$userid."'";
$result = exec_query($sql,1);
return $result['session_id'];
}

function getProductType($subdefid){
	//$qry="SELECT subType from product where subscription_def_id='".$subdefid."'";
	$qry="SELECT subType from product where (subscription_def_id='".$subdefid."' OR recurly_plan_code='".$subdefid."')";
	$result=exec_query($qry,1);
	return $result['subType'];

}

function getMenuItems(){
   $qry="SELECT l.title,lp.alias FROM layout_menu AS l LEFT JOIN layout_pages as lp ON l.page_id = lp.id
			WHERE l.navigation_type ='H' AND parent_id='0' AND l.active = 1 order by l.parent_id,l.menuorder Asc";
	$result=exec_query($qry);
	if(isset($result)){
		return $result;
	}else {
		return false;

	}
}

function getBreadCrum($id,$type='page',$breadcrum){
	if($type=='page'){
		global $objCache;
		if(!is_object($objCache)){
			$objCache = new Cache();
		}
		$resGetParent=$objCache->getParentMenu($id);
		if(count($resGetParent)>0){
			array_push($breadcrum,$resGetParent);
			if($resGetParent['parent_id']>0){
				$sqlGetMenuPageID="select page_id from layout_menu where id='".$resGetParent['parent_id']."'";
				$resGetMenuPageID=exec_query($sqlGetMenuPageID,1);
				$resGetParentPageId=$objCache->getParentMenu($resGetMenuPageID['page_id']);
				if(count($resGetParentPageId)>0){
					return getBreadCrum($resGetParentPageId['page_id'],'page',$breadcrum);
				}
			}else{
				return $breadcrum;
			}
		}else{
			return $breadcrum;
		}
	}
}

function getSectionName($id){
	$sqlGetSectionName='select * from section where section_id="'.$id.'"';
	$resGetSectionName=exec_query($sqlGetSectionName,1);
	return $resGetSectionName['name'];

}

function getParentSectionId($sectionId){
	$getSql="select B.page_id from section A, section B where A.parent_section=B.section_id and A.section_id='".$sectionId."'";
	$getResult=exec_query($getSql,1);
	if($getResult){
		return $getResult;
	}else{
		return false;
	}
	
}

function loginRedirection(){
    if(!$_SESSION['AID'])
	  {
	location($HTPFXSSL.$HTHOST.'/subscription/register/login.htm');
	exit;
      }
}

function getContributorSubsId($authorid)
{
	global $defaultSudId;
	$sqlAuthSubs =" select subscription_id as subid from ex_contributor_subscription_mapping where contributor_id='".$authorid."'";
	$results=exec_query($sqlAuthSubs,1);
	if($results && count($results)>0)
	{
		$authorId		=	$results['subid'];
	}
	else
	{
		$authorId		=	$defaultSudId;
	}
	return $authorId;
}

function getSyndRelatedArticles($articleid,$syndicationChannel){
	
	$sqlRelatedSynd = "SELECT DISTINCT(eit.item_id), a.id, a.title, eim.url,IF(a.publish_date = '0000-00-0000:00:00',a.date,a.publish_date) AS pubDate FROM ex_item_tags AS eit, articles AS a LEFT JOIN ex_item_meta AS eim ON  eim.item_id=a.id LEFT JOIN content_syndication AS cs ON  cs.item_id=a.id WHERE a.is_live = '1' AND a.approved = '1' AND a.id=eit.item_id AND eit.item_type='1' AND eit.item_id!='".$articleid."' AND eit.tag_id IN (SELECT tag_id FROM ex_item_tags AS et WHERE et.item_id='".$articleid."' AND et.item_type='1') AND eim.item_type='1' AND eim.item_id!='".$articleid."' AND cs.item_id!='".$articleid."' ";
	if(!empty($syndicationChannel))
	{
		$sqlRelatedSynd .= " AND  cs.syndication_channel ='".$syndicationChannel."'";
		if($syndicationChannel=="nasdaq"){
			$sqlRelatedSynd .= " AND cs.is_nasdaqFeed='1'";
		}
	}
	$sqlRelatedSynd .=" ORDER BY pubDate DESC LIMIT 0,3";
	$resRelatedSynd = exec_query($sqlRelatedSynd);
	return $resRelatedSynd;
}

?>