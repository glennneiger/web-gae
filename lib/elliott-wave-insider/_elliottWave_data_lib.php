<?php
include_once("$D_R/lib/_content_data_lib.php");
class elliottWaveData{
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

	function getElliottAlertDetails($id){
		$qryDetail = "select * from elliot_alert where id='".$id."'";
		$resDetail = exec_query($qryDetail,1);
		if(!empty($resDetail)){
			return $resDetail;
		}
	}

	function getElliottAlertIdByUrl($url){
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

	function getElliottAllAlertToEdit(){
		$qryAllAlert = "select id, title from elliot_alert";
		$resAllAlert = exec_query($qryAllAlert);
		if(!empty($resAllAlert)){
			return $resAllAlert;
		}
	}

	function getAllSection($secName)
	{
		$qryAllSec = "select id, section_name from elliot_section";
		if($secName!=''){
			$qryAllSec .= " where section_name='".$secName."'";
		}
		$resAllSec = exec_query($qryAllSec);
		if(!empty($resAllSec)){
			return $resAllSec;
		}
	}

	function getAllElliottCategory(){
		$qryAllCat = "SELECT ec.id, ec.category_name, COUNT(ea.id)  AS alertCount FROM `elliot_category` AS ec LEFT JOIN  `elliot_alert` AS ea ON FIND_IN_SET (ec.id,ea.category_id) AND ea.is_approved='1' AND is_live='1' AND is_deleted='0' AND (IF(ea.publish_date>ea.creation_date,ea.publish_date,ea.creation_date)>=('".mysqlNow()."'- INTERVAL 1 MONTH) AND IF(ea.publish_date>ea.creation_date,ea.publish_date,ea.creation_date)>='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH) WHERE ec.is_active='1' GROUP BY ec.id ORDER BY ec.category_name";
		$resAllCat = exec_query($qryAllCat);
		if(!empty($resAllCat)){
			return $resAllCat;
		}
	}

	function getAllAnalyst(){
		global $elliotAnalystGroup;
		$qryAllAnalyst = "SELECT temp.*, COUNT(ea.id) AS alertCount FROM ( SELECT C.id,C.name FROM `contributors` AS C,`contributor_groups_mapping` AS CGM WHERE CGM.`contributor_id`=C.id AND CGM.`group_id`='".$elliotAnalystGroup."') AS temp LEFT JOIN `elliot_alert` AS ea ON ea.contrib_id=temp.id AND ea.is_approved='1' AND is_live='1' AND is_deleted='0' AND (IF(ea.publish_date>ea.creation_date,ea.publish_date,ea.creation_date)>=('".mysqlNow()."'- INTERVAL 1 MONTH) AND IF(ea.publish_date>ea.creation_date,ea.publish_date,ea.creation_date)>='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH) GROUP BY temp.id ORDER BY temp.name";
		$resAllAnalyst = exec_query($qryAllAnalyst);
		if(!empty($resAllAnalyst)){
			return $resAllAnalyst;
		}
	}

	function getAnalystBio($contrib_id)
	{
		global $elliotAnalystGroup;
		$qryAnalyst = "SELECT C.id, C.name, C.description, C.disclaimer, C.editor_note, IF(logo_asset IS NULL, IF(bio_asset IS NULL, small_bio_asset,bio_asset),logo_asset) AS bio_image FROM `contributors`  AS C LEFT JOIN `contributor_groups_mapping` CGM ON CGM.`contributor_id`=C.id AND CGM.`group_id`='".$elliotAnalystGroup."' WHERE C.id='".$contrib_id."' AND CGM.`group_id`='".$elliotAnalystGroup."'";
		$resAnalyst = exec_query($qryAnalyst,1);
		if(!empty($resAnalyst)){
			return $resAnalyst;
		}
	}

	function getElliottCategoryDetails($cat_id)
	{
		$qryCatDetail = "SELECT id,category_name FROM `elliot_category` WHERE `id` IN (".$cat_id.")";
		$resCatDetail = exec_query($qryCatDetail);
		if(!empty($resCatDetail)){
			return $resCatDetail;
		}
	}

	function getAllAlerts($sectionName,$offset){
		global $elliottPostLimit;
		$offset=$offset*$elliottPostLimit;
		$qryAllAlerts = "SELECT e.id, e.title, IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date) publish_date, e.body FROM elliot_alert e
WHERE e.is_approved='1' AND e.is_live='1' AND e.is_deleted='0' AND (IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date)>=('".mysqlNow()."'- INTERVAL 1 MONTH)AND
IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date)>='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		if($sectionName!=""){

			$secId = $this->getAllSection($sectionName);
			$secId = $secId['0']['id'];
		}
        if($secId){
			$qryAllAlerts .=" AND FIND_IN_SET('".$secId."',e.section_id)";
		}
		$qryAllAlerts .= " ORDER BY publish_date DESC limit ".$offset.",".$elliottPostLimit;
		$resAllAlerts = exec_query($qryAllAlerts);
		foreach($resAllAlerts as $key=>$value){
			$resAllAlerts[$key]['url']=$this->getAlertUrl($value['id']);
		}
		if(!empty($resAllAlerts)){
			return $resAllAlerts;
		}
	}

	function getAlertUrl($id){
		$qry="select url from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}

	function getEwiSearchCount($q,$searchType){
	    $qrySearchCount = "select count(distinct eim.item_id) as count from ex_item_meta eim , elliot_alert e WHERE e.id=eim.item_id AND eim.is_live='1'";
		if($searchType=="archive" && $q!=''){
			$qrySearch .= " AND (DATE_FORMAT(eim.publish_date,'%Y/%c')='".$q."' AND eim.publish_date >='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		}else{
			$qrySearch .= "  AND (eim.publish_date >=('".mysqlNow()."'- INTERVAL 1 MONTH) AND eim.publish_date >='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		}

		if($searchType=="text" && $q!=''){
			//$qrySearchCount .= " AND MATCH(content) AGAINST ('\"$q\"' IN BOOLEAN MODE)";
			$qrySearchCount .= " AND content LIKE '%$q%'";
		}elseif ($searchType=="analyst"){
			$qrySearchCount .= " AND eim.author_id='".$q."'";
		}elseif ($searchType=="category"){
			$qrySearchCount .= " AND FIND_IN_SET('".$q."',e.category_id)";
		}
	    $qrySearchCount .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date desc ";
		$resSearchCount = exec_query($qrySearchCount,'1');
		if(!empty($resSearchCount)){
			return $resSearchCount['count'];
		}
	}

	function getEwiSearch($q,$offset,$searchType){
		global $elliottPostLimit;
		$offset=$offset*$elliottPostLimit;
		$qrySearch = "SELECT DISTINCT eim.item_id AS id, eim.url,eim.title, eim.publish_date, e.body AS body FROM ex_item_meta eim , elliot_alert e WHERE e.id=eim.item_id AND eim.is_live='1'";
		if($searchType=="archive" && $q!=''){
			$qrySearch .= " AND (DATE_FORMAT(eim.publish_date,'%Y/%c')='".$q."' AND eim.publish_date >='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		}else{
			$qrySearch .= "  AND (eim.publish_date >=('".mysqlNow()."'- INTERVAL 1 MONTH) AND eim.publish_date >='".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		}

		if($searchType=="text" && $q!=''){
			$qrySearch .= " AND content LIKE '%$q%'";
		}elseif ($searchType=="analyst"){
			$qrySearch .= " AND eim.author_id='".$q."'";
		}elseif ($searchType=="category"){
			$qrySearch .= " AND FIND_IN_SET('".$q."',e.category_id)";
		}
		$qrySearch .= " AND eim.item_type='".$this->contentType."' ORDER BY eim.publish_date DESC LIMIT ".$offset.",".$elliottPostLimit;

		$resSearch = exec_query($qrySearch);
		if(!empty($resSearch)){
			return $resSearch;
		}
	}

	function getEwiPostCount($secName){
		$qry="select count(e.id) count from elliot_alert e  where e.is_live='1' AND e.is_approved='1' and e.is_deleted='0' AND (IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date)>=('".mysqlNow()."'- INTERVAL 1 MONTH)  AND IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date)>= '".$_SESSION['ewiActivationDate']."' - INTERVAL 1 MONTH)";
		if($secName!=""){
			$secId = $this->getAllSection($secName);
			$secId = $secId['0']['id'];
		}
        if($secId){
			$qry .=" AND FIND_IN_SET('".$secId."',e.section_id)";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}

	function getAllAnalystBio($offset){
		global $elliotAnalystGroup, $elliottPostLimit;
		$qryAllAnalyst = "SELECT C.id, C.name, C.description FROM `contributors`  AS C LEFT JOIN `contributor_groups_mapping` CGM ON CGM.`contributor_id`=C.id AND CGM.`group_id`='".$elliotAnalystGroup."' WHERE CGM.`group_id`='".$elliotAnalystGroup."' ORDER BY C.name";
		$resAllAnalyst = exec_query($qryAllAnalyst);
		if(!empty($resAllAnalyst)){
			return $resAllAnalyst;
		}
	}

	function getEwiAnalystCount(){
		global $elliotAnalystGroup;
		$qryAnalystCount = "SELECT COUNT(C.id) AS contribCount FROM `contributors`  AS C LEFT JOIN `contributor_groups_mapping` CGM ON CGM.`contributor_id`=C.id AND CGM.`group_id`='".$elliotAnalystGroup."' WHERE CGM.`group_id`='".$elliotAnalystGroup."'";
		$resAnalystCount = exec_query($qryAnalystCount,1);
		if($resAnalystCount){
			return $resAnalystCount['contribCount'];
		}else{
			return false;
		}
	}
}