<?php
include_once("$D_R/lib/_content_data_lib.php");
class peterTchirData{
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
			$this->contribId=get_contributor_id_byname("Peter Tchir");
		}
	}

	function getAllCategory($catName=null){
		$qryCat = "select * from peter_category";
		if($catName!=''){
			$qryCat .= " where category_name='".$catName."'";
		}
		$resCat = exec_query($qryCat);
		if(!empty($resCat)){
			return $resCat;
		}
	}

	function getAlertDetails($id){
		$qryDetail = "select * from peter_alerts where id='".$id."'";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}

	function getAllAlertToEdit(){
		$qryAllAlert = "select id, title from peter_alerts";
		$resAllAlert = exec_query($qryAllAlert);
		if(!empty($resAllAlert)){
			return $resAllAlert;
		}
	}

	function prepareMapData($data){
		$params['title'] = $data['title'];
		$params['mapImg'] = $data['heatMapImgName'];
		if($data['heatMapId']){
		    $conditions['id']=$data['heatMapId'];
			$id = update_query("peter_heatmap",$params,$conditions,$keynames=array());
		}else{
			$id = insert_query("peter_heatmap",$params);
		}

		if(!empty($id)){
			return true;
		}
	}

	function getAllHeapMaps() {
		$qryMap = "select * from peter_heatmap";
		$resMap = exec_query($qryMap);
		if(!empty($resMap)){
			return $resMap;
		}
	}

	function getMapById($id){
		$qryMap = "select * from peter_heatmap where id='".$id."'";
		$resMap = exec_query($qryMap,1);
		if(!empty($resMap)){
			return $resMap;
		}
	}

	function getLatestHeatMap(){
		$qryLatestMap = "select * from peter_heatmap order by id desc limit 1";
		$resLatestMap = exec_query($qryLatestMap,1);
		if(!empty($resLatestMap)){
			return $resLatestMap;
		}
	}

	function getAlertUrl($id){
		$qry="select url from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}

	function getAllAlerts($catName,$offset){
		global $peterPostLimit;
		$offset=$offset*$peterPostLimit;
		if($catName!=""){
			$catId = $this->getAllCategory($catName);
			$catId = $catId['0']['id'];
		}
		$qryAlerts = "SELECT p.id, p.title, IF(p.publish_date>p.creation_date,p.publish_date,p.creation_date) publish_date, p.body, p.position FROM peter_alerts p
WHERE p.is_approved='1' AND p.is_live='1' AND is_deleted='0'";
		if($catId!=''){
			$qryAlerts .= " AND FIND_IN_SET('".$catId."',p.category_id)";
		}
		$qryAlerts .= " ORDER BY publish_date DESC limit ".$offset.",".$peterPostLimit;
		$resAlerts = exec_query($qryAlerts);
		foreach($resAlerts as $key=>$value){
			$resAlerts[$key]['url']=$this->getAlertUrl($value['id']);
		}
		if(!empty($resAlerts)){
			return $resAlerts;
		}
	}

	function getPeterSearchCount($q){
	    $qrySearchCount = "select count(distinct item_id) as count from ex_item_meta WHERE is_live='1' AND content LIKE '%$q%' AND author_id='".$this->contribId."' AND item_type='".$this->contentType."' ORDER BY publish_date desc ";
		$resSearchCount = exec_query($qrySearchCount,'1');
		if(!empty($resSearchCount)){
			return $resSearchCount['count'];
		}
	}

	function getPeterSearch($q,$offset){
		global $peterPostLimit;

		$qrySearch = "SELECT DISTINCT eim.item_id AS id, eim.url,eim.title,
eim.publish_date, p.body AS body FROM ex_item_meta eim , `peter_alerts` p
 WHERE p.id=eim.item_id AND eim.is_live='1' AND content LIKE '%$q%'
AND eim.author_id='".$this->contribId."' AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date DESC LIMIT ".$offset.",".$peterPostLimit;

		$resSearch = exec_query($qrySearch);
		if(!empty($resSearch)){
			return $resSearch;
		}
	}

	function getPeterPostCount($catName){
		$qry="select count(p.id) count from peter_alerts p  where p.is_live='1' AND p.is_approved='1' and p.is_deleted='0'";
		if($catName!=""){
			$catId = $this->getAllCategory($catName);
			$catId = $catId['0']['id'];
		}
        if($catId){
			$qry .=" AND FIND_IN_SET('".$catId."',p.category_id)";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}

	function getAlertIdByUrl($url){
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

	function getPeterBio(){
		global $contributorId;
		$qryBio = "SELECT `description` FROM `contributors` WHERE id='".$contributorId."'";
		$resBio = exec_query($qryBio,1);
		if(!empty($resBio)){
			return $resBio['description'];
		}
	}
} //class end
?>