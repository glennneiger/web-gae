<?php
include_once("$D_R/lib/_content_data_lib.php");
class eduData{
	var $contentType,$contentId;
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

	function getEduAlertMetaData($id)
	{
		$qryDetail = "SELECT eim.`title`,eim.`description`,eim.`keywords`,eim.`publish_date`,eim.`url`, ea.edu_img, ea.is_live
			FROM `ex_item_meta` eim
			LEFT JOIN `edu_alerts` ea ON ea.`id`=eim.item_id
			WHERE item_id='".$id."' AND item_type='".$this->contentType."' ";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}
	
	
	function getEduAlertDetails($id){
		$qryDetail = "select e.* from edu_alerts e where e.id='".$id."' AND e.is_deleted='0'";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}
	
	function getEduAlertMenuType($id){
		$qryMenuDetail = "SELECT nav.`menu_name` FROM edu_alerts ed LEFT JOIN `edu_nav_category` nav ON nav.id IN (ed.`category_id`) WHERE ed.id='".$id."'";
		$resMenuDetail = exec_query($qryMenuDetail,1);
		if(!empty($resMenuDetail)){
			return $resMenuDetail;
		}
	}
	
	function getEduAlertIdByUrl($url){

	   $posQuery = strpos($url,"?");
	   if($posQuery>=0 && $posQuery!="")
	   {
	   		$url = substr($url, 0 ,$posQuery);
	   }
	   $qry="SELECT item_id FROM ex_item_meta WHERE LOWER(url) LIKE '%".$url."%' AND item_type='".$this->contentType."' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			return $result['item_id'];
		}
		else
		{
			return false;
		}
	}

	function getEduAllAlertToEdit(){
		$qryAllAlert = "select id, title from edu_alerts";
		$resAllAlert = exec_query($qryAllAlert);
		if(!empty($resAllAlert)){
			return $resAllAlert;
		}
	}

	function getAllEduCategory($onlyCat=null){
		$qryAllCat = "SELECT id, menu_name, menu_alias FROM edu_nav_category WHERE is_active='1'";
		if($onlyCat=="1"){
			$qryAllCat .= " AND is_category='1'";
		}
		$qryAllCat .= " ORDER BY menu_seq ASC ";
		$resAllCat = exec_query($qryAllCat);
		if(!empty($resAllCat)){
			return $resAllCat;
		}
	}
	
	function getEduCatByName($catName){
		if($catName=="How to Videos")
		{
			$catName="Trading Videos";
		}
		
		$qryByName = "SELECT id, menu_alias FROM edu_nav_category WHERE is_active='1' AND is_category='1' and menu_name='".$catName."'";
		$resByName = exec_query($qryByName,1);
		if(!empty($resByName)){
			return $resByName;
		}
	}

	function getEduAllAlerts($catName,$offset){
		global $eduPostLimit;
		$offset=$offset*$eduPostLimit;
		$qryAllAlerts = "SELECT e.id, e.title, IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date) publish_date, e.body, e.edu_img, e.is_video, e.eduVideo, c.name FROM edu_alerts e, `contributors` c WHERE e.is_approved='1' AND e.is_live='1' AND e.is_deleted='0' AND c.id=e.contrib_id";
		if($catName!=""){
			$catId = $this->getEduCatByName($catName);
			$catId = $catId['id'];
		}
        if($catId){
			$qryAllAlerts .=" AND FIND_IN_SET('".$catId."',e.category_id)";
		}
		$qryAllAlerts .= " ORDER BY publish_date DESC limit ".$offset.",".$eduPostLimit;
		$resAllAlerts = exec_query($qryAllAlerts);
		foreach($resAllAlerts as $key=>$value){
			$resAllAlerts[$key]['url']=$this->getEduAlertUrl($value['id']);
		}
		if(!empty($resAllAlerts)){
			return $resAllAlerts;
		}
	}

	function getEduAlertUrl($id){
		$qry="select url from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}

	function getEduSearchCount($q,$searchType){
	    $qrySearchCount = "select count(distinct eim.item_id) as count from ex_item_meta eim , edu_alerts e WHERE e.id=eim.item_id AND eim.is_live='1'";

		if($searchType=="text" && $q!=''){
			//$qrySearchCount .= " AND content LIKE '%".$q."%' ";
			$qrySearchCount .="AND content LIKE '%$q%'";
		}
		 
	    $qrySearchCount .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date desc ";
		$resSearchCount = exec_query($qrySearchCount,'1');
		if(!empty($resSearchCount)){
			return $resSearchCount['count'];
		}
	}

	function getEduSearch($q,$offset,$searchType){
		global $eduPostLimit;
		$offset=$offset*$eduPostLimit;
		$qrySearch = "SELECT DISTINCT eim.item_id AS id, eim.url,eim.title, eim.publish_date, e.body AS body, e.edu_img FROM ex_item_meta eim , edu_alerts e WHERE e.id=eim.item_id AND eim.is_live='1'";

		if($searchType=="text" && $q!=''){
			$qrySearch .= " AND content LIKE '%$q%'";
		
		}
		
		$qrySearch .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date DESC LIMIT ".$offset.",".$eduPostLimit;

		$resSearch = exec_query($qrySearch);
		if(!empty($resSearch)){
			return $resSearch;
		}
	}

	function getEduPostCount($catName){
		$qry="select count(e.id) count from edu_alerts e  where e.is_live='1' AND e.is_approved='1' and e.is_deleted='0'";
		if($catName!=""){
			$catId = $this->getEduCatByName($catName);
			$catId = $catId['id'];
		}
        if($catId){
			$qry .=" AND FIND_IN_SET('".$catId."',e.category_id)";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}
	
	function getEduLatestArticle(){
		$qryLatestArticle = "SELECT e.id, e.title, csu.url FROM edu_alerts e, content_seo_url csu 
WHERE e.is_approved='1' AND e.is_live='1' AND e.is_deleted='0' AND e.id=csu.item_id AND csu.item_type='".$this->contentType."' ORDER BY id DESC LIMIT 5";
		$resLatestArticle = exec_query($qryLatestArticle);
		if($resLatestArticle){
			return $resLatestArticle;
		}else{
			return false;
		}
	}
	
	function getContributorById($contrib_id){
		$qryContrib = "SELECT name FROM contributors WHERE id='".$contrib_id."'";
		$resContrib =  exec_query($qryContrib,1);
		if($resContrib){
			return $resContrib['name'];
		}else{
			return false;
		}
	}
	
	function getEduGlossary(){
		$qryDict = "SELECT * FROM edu_glossary ed ORDER BY ed.name ASC";
		$resDict = exec_query($qryDict);
		if($resDict){
			return $resDict;
		}else{
			return false;
		}
	}
	
	function getEduGlossaryById($id) {
		$qryDict = "SELECT * FROM edu_glossary ed where id='".$id."'";
		$resDict = exec_query($qryDict,1);
		if($resDict){
			return $resDict;
		}else{
			return false;
		}
	}
	
	function getEduSearchGlossary($searchText){
		$qrySearchGloss = "SELECT * FROM edu_glossary ed  WHERE ed.value LIKE '%".$searchText."%' OR ed.name  LIKE '%".$searchText."%' ORDER BY ed.name ASC";
		$resSearchGloss = exec_query($qrySearchGloss);
		if($resSearchGloss){
			return $resSearchGloss;
		}else{
			return false;
		}
	}
	
	function getEduProduct($getLatest){
		$qryEduProd = "SELECT * FROM edu_product ed ORDER BY";
		if($getLatest=='1'){
			$qryEduProd .= " ed.id DESC limit 3";
		}else{
			$qryEduProd .= " ed.title ASC";
		}
		$resEduProd = exec_query($qryEduProd);
		if($resEduProd){
			return $resEduProd;
		}else{
			return false;
		}
	}
	
	function getEduProductById($id) {
		$qryEduProd = "SELECT * FROM edu_product ed where id='".$id."'";
		$resEduProd = exec_query($qryEduProd,1);
		if($resEduProd){
			return $resEduProd;
		}else{
			return false;
		}
	}
}