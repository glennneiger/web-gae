<?php 
include_once("$D_R/lib/_content_data_lib.php");
class keeneData{
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
	
	function getKeeneAlertDetails($id){
		$qryDetail = "SELECT k.*,kc.`category_alias`  FROM keene_alerts k
			LEFT JOIN `keene_category` kc ON kc.id IN (k.`category_id`)
			WHERE k.id='".$id."'";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}
	
	function getAllAlertToEdit(){
		$qryAllAlert = "select id, title from keene_alerts";
		$resAllAlert = exec_query($qryAllAlert);
		if(!empty($resAllAlert)){
			return $resAllAlert;
		}
	}
	
	function getAllKeeneCategory(){
		$qryCat = "SELECT kc.id, kc.category_name, kc.category_alias, COUNT(ka.id)  AS alertCount FROM keene_category AS kc 
LEFT JOIN  keene_alerts AS ka ON FIND_IN_SET (kc.id,ka.category_id) AND ka.is_approved='1' AND ka.is_live='1' AND ka.is_deleted='0' 
WHERE kc.is_active='1' GROUP BY kc.id ORDER BY kc.categoty_seq ASC";
		$resCat = exec_query($qryCat);
		if(!empty($resCat)){
			return $resCat;
		}
	}
	
	function getKeeneCategoryDetails($cat_id)
	{
		$qryCatDetail = "SELECT id,category_name,category_alias FROM `keene_category` WHERE `id` IN (".$cat_id.")";
		$resCatDetail = exec_query($qryCatDetail);
		if(!empty($resCatDetail)){
			return $resCatDetail;
		}
	}
	
	function getKeeneCatByName($categoryName){
		$qryCatByName = "select id from keene_category where category_alias='".$categoryName."' AND is_active='1'";
		$resCatByName = exec_query($qryCatByName,1);
		if(!empty($resCatByName)){
			return $resCatByName;
		}
	}
	
	function getAllAlerts($categoryName,$offset){
		global $keenePostLimit;
		$offset=$offset*$keenePostLimit;
		$qryKeeneAllAlerts = "SELECT ka.id, ka.title, IF(ka.publish_date>ka.creation_date,ka.publish_date,ka.creation_date) publish_date, ka.body FROM keene_alerts ka WHERE ka.is_approved='1' AND ka.is_live='1' AND ka.is_deleted='0'";
		if($categoryName!=""){
			$catId = $this->getKeeneCatByName($categoryName);
			$catId = $catId['id'];
		}
        if($catId){
			$qryKeeneAllAlerts .=" AND FIND_IN_SET('".$catId."',ka.category_id)";
		}
		$qryKeeneAllAlerts .= " ORDER BY publish_date DESC limit ".$offset.",".$keenePostLimit;
		$resKeeneAllAlerts = exec_query($qryKeeneAllAlerts);
		foreach($resKeeneAllAlerts as $key=>$value){
			$resKeeneAllAlerts[$key]['url']=$this->getKeeneAlertUrl($value['id']);
		}
		if(!empty($resKeeneAllAlerts)){
			return $resKeeneAllAlerts;
		}
	}
	
	function getKeeneAlertUrl($id){
		$qry = "select url from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
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
	
	function getKeenePostCount($secName){
		$qryKeeneCount = "select count(ka.id) count from keene_alerts as ka  where ka.is_live='1' AND ka.is_approved='1' and ka.is_deleted='0'";
		if($categoryName!=""){
			$catId = $this->getKeeneCatByName($categoryName);
			$catId = $catId['id'];
		}
        if($catId){
			$qryKeeneCount .=" AND FIND_IN_SET('".$catId."',ka.category_id)";
		}
		$resKeeneCount = exec_query($qryKeeneCount,1);
		if($resKeeneCount){
			return $resKeeneCount['count'];
		}else{
			return false;
		}
	}
	
	function getKeeneSearchCount($q,$searchType){
	    $qrySearchCount = "select count(distinct eim.item_id) as count from ex_item_meta eim , keene_alerts as ka WHERE ka.id=eim.item_id AND eim.is_live='1'";
		if($searchType=="text" && $q!=''){
			//$qrySearchCount .= " AND content LIKE %".$q."%";
			$qrySearchCount .= " AND content LIKE '%$q%'";
		}
		
	    $qrySearchCount .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date desc ";
		$resSearchCount = exec_query($qrySearchCount,'1');
		if(!empty($resSearchCount)){
			return $resSearchCount['count'];
		}
	}

	function getKeeneSearch($q,$offset,$searchType){
		global $keenePostLimit;
		$offset=$offset*$keenePostLimit;
		$qrySearch = "SELECT DISTINCT eim.item_id AS id, eim.url,eim.title, eim.publish_date, ka.body AS body FROM ex_item_meta eim , keene_alerts as ka WHERE ka.id=eim.item_id AND eim.is_live='1'";

		if($searchType=="text" && $q!=''){
			$qrySearch .= " AND content LIKE '%$q%'";
			//$qrySearch .= " AND MATCH(content) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
		}
		$qrySearch .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date DESC LIMIT ".$offset.",".$keenePostLimit;
		$resSearch = exec_query($qrySearch);
		if(!empty($resSearch)){
			return $resSearch;
		}
	}
	
} //class end
?>