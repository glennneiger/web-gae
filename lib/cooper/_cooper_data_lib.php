<?php 
include_once("$D_R/lib/_content_data_lib.php");
class cooperData{
	var $contentType,$contentId;
	function __construct($type="",$id=""){
		$this->contentId=$id;
		if(is_numeric($type))
		{
			$this->contentType=$type;
		}
		else
		{
			if($type!=""){
				$sqlGetContentTypeId="select id,item_table from ex_item_type where item_text='".$type."' or item_table='".$type."'";
				$resGetContentTypeId=exec_query($sqlGetContentTypeId,1);
				$this->contentType=$resGetContentTypeId['id'];
				$this->contentTable=$resGetContentTypeId['item_table'];
			}
		}
	}
	
	function getCooperCategory($id){
		$qryDetail = "SELECT kc.`category_alias`  FROM cp_articles k LEFT JOIN `cooper_category` kc ON kc.id IN (k.`redesign_cat_id`) WHERE k.id='".$id."'";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}
	
	function getAllAlertToEdit(){
		$qryAllAlert = "select id, title from cp_articles";
		$resAllAlert = exec_query($qryAllAlert);
		if(!empty($resAllAlert)){
			return $resAllAlert;
		}
	}
	
	function getAllCooperCategory(){
		$qryCat = "SELECT kc.id, kc.category_name, kc.category_alias, COUNT(ka.id)  AS alertCount FROM cooper_category AS kc 
LEFT JOIN  cp_articles AS ka ON FIND_IN_SET (kc.id,ka.redesign_cat_id) AND ka.approved='1' AND ka.is_live='1' WHERE kc.is_active='1' GROUP BY kc.id ORDER BY kc.categoty_seq ASC";
		$resCat = exec_query($qryCat);
		if(!empty($resCat)){
			return $resCat;
		}
	}
	
	function getCooperCategoryDetails($cat_id)
	{
		$qryCatDetail = "SELECT id,category_name,category_alias FROM `cooper_category` WHERE `id` IN (".$cat_id.")";
		$resCatDetail = exec_query($qryCatDetail);
		if(!empty($resCatDetail)){
			return $resCatDetail;
		}
	}
	
	function getCooperCatByName($categoryName){
		$qryCatByName = "select id from cooper_category where category_alias='".$categoryName."' AND is_active='1'";
		$resCatByName = exec_query($qryCatByName,1);
		if(!empty($resCatByName)){
			return $resCatByName;
		}
	}
	
	function getAllAlerts($categoryName,$offset,$month,$year){
		global $cooperPostLimit;
		$offset=$offset*$cooperPostLimit;
		$qryCooperAllAlerts = "SELECT ka.id, ka.title, IF(ka.publish_date>ka.date,ka.publish_date,ka.date) publish_date, ka.body FROM cp_articles ka WHERE ka.approved='1' AND ka.is_live='1'";
		if($categoryName!=""){
			$catId = $this->getCooperCatByName($categoryName);
			$catId = $catId['id'];
		}
        if(!empty($catId) && $catId!='1'){
			$qryCooperAllAlerts .=" AND FIND_IN_SET('".$catId."',ka.redesign_cat_id)";
		}
		if($month>0){
			$mnthYr= $year.'-'.$month;
			$qryCooperAllAlerts .=" AND DATE_FORMAT(ka.date, '%Y-%m')='".$mnthYr."'";
		}
		$qryCooperAllAlerts .= " ORDER BY publish_date DESC limit ".$offset.",".$cooperPostLimit;
		$resCooperAllAlerts = exec_query($qryCooperAllAlerts);
		foreach($resCooperAllAlerts as $key=>$value){
			$resCooperAllAlerts[$key]['url']=$this->getCooperAlertUrl($value['id']);
		}
		if(!empty($resCooperAllAlerts)){
			return $resCooperAllAlerts;
		}
	}
	
	function getCooperAlertUrl($id){
		global $cooperItemMeta;
		$qry = "select url from ex_item_meta where item_id='".$id."' and item_type='".$cooperItemMeta."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}
	
	function getKeeneAlertIdByUrl($url){
	   $qry="SELECT item_id FROM content_seo_url WHERE LOWER(url) LIKE '%".$url."%' AND item_type='".$this->contentType."' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			return $result['item_id'];
		}
		else
		{
			return false;
		}
	}
	
	function getCooperPostCount($categoryName,$month,$year){
		$qryCooperCount = "select count(ka.id) count from cp_articles as ka  where ka.is_live='1' AND ka.approved='1'";
		if($categoryName!=""){
			$catId = $this->getCooperCatByName($categoryName);
			$catId = $catId['id'];
		}
         if(!empty($catId) && $catId!='1'){
			$qryCooperCount .=" AND FIND_IN_SET('".$catId."',ka.redesign_cat_id)";
		}
		if($month>0){
			$mnthYr= $year.'-'.$month;
			$qryCooperCount .=" AND DATE_FORMAT(ka.date, '%Y-%m')='".$mnthYr."'";
		}
		$resCooperCount = exec_query($qryCooperCount,1);
		if($resCooperCount){
			return $resCooperCount['count'];
		}else{
			return false;
		}
	}
	
	function getCooperSearchCount($q,$searchType){
		global $cooperItemMeta;
	    $qrySearchCount = "select count(distinct eim.item_id) as count from ex_item_meta eim , cp_articles as ka WHERE ka.id=eim.item_id AND eim.is_live='1'";
		if($searchType=="text" && $q!=''){
			$qrySearchCount .= " AND MATCH(content) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
		}
		
	    $qrySearchCount .= " AND eim.item_type='".$cooperItemMeta."' ORDER BY eim.publish_date desc ";
		$resSearchCount = exec_query($qrySearchCount,'1');
		if(!empty($resSearchCount)){
			return $resSearchCount['count'];
		}
	}

	function getCooperSearch($q,$offset,$searchType){
		global $cooperPostLimit,$cooperItemMeta;
		$offset=$offset*$cooperPostLimit;
		$qrySearch = "SELECT DISTINCT eim.item_id AS id, eim.url,eim.title, eim.publish_date, ka.body AS body FROM ex_item_meta eim , cp_articles as ka WHERE ka.id=eim.item_id AND eim.is_live='1'";

		if($searchType=="text" && $q!=''){
			$qrySearch .= " AND MATCH(content) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
		}
		$qrySearch .= " AND eim.item_type='".$cooperItemMeta."' ORDER BY eim.publish_date DESC LIMIT ".$offset.",".$cooperPostLimit;
		$resSearch = exec_query($qrySearch);
		if(!empty($resSearch)){
			return $resSearch;
		}
	}
	
	function getCooperPostByMonthCount($month,$year){
		$mnthYr= $year.'-'.$month;
		$qry = "SELECT cc.id, cc.category_name, cc.category_alias, COUNT(cp.id)  AS alertCount FROM `cooper_category` AS cc LEFT JOIN  `cp_articles` AS cp ON FIND_IN_SET(cc.id,cp.`redesign_cat_id`) AND cp.approved='1' AND is_live='1' AND DATE_FORMAT(cp.date, '%Y-%m')='".$mnthYr."' WHERE cc.is_active='1' GROUP BY cc.id ORDER BY cc.category_name";
		$res = exec_query($qry);
		
		$qryAllPostCount="select COUNT(cp.id) as allPostCount from cp_articles cp where cp.approved='1' AND is_live='1' AND DATE_FORMAT(cp.date, '%Y-%m')='".$mnthYr."'";
		$resAllPostCount = exec_query($qryAllPostCount,1);
		if(!empty($res)){
			$res['0']['alertCount'] = $resAllPostCount['allPostCount'];
			return $res;
		}
	}
} //class end
?>