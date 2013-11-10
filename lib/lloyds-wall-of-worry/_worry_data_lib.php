<?
class worryData{
	
	function prepareWorryData($data){
	$params['title'] = $data['title'];
	$params['worry_img'] = $data['worryImgName'];
	$params['archive_img'] = $data['archiveImgName'];
	if($data['worryid']){
	    $conditions['id']=$data['worryid'];
		$id = update_query("upload_worry",$params,$conditions,$keynames=array());
	}else{
		$id = insert_query("upload_worry",$params);	
	}

		if($id){
			return $id;
		}
	}
	
	function prepareAppWry($data,$wryItemId=""){
		// $params['id'] = $wryItemId;
		if($data['publish_date']=="" || $data['publish_date']=="0000-00-00 00:00:00" )
		{
			$params['publish_date'] =  date('Y-m-d h:i:s');
		}	
		else
		{
			$params['publish_date'] = $data['publish_date'];			
		}	
		$params['updation_date'] = date('Y-m-d h:i:s');
		$params['worry_sequence'] = $data['sequence'];
		$params['is_draft'] = '0';
		$params['is_approve'] = '1';
		$params['is_live'] = '1';
		$params['is_deleted'] = '0';
		
		if($wryItemId){
		    $conditions['id']=$wryItemId;
			$id=update_query("worry_alert",$params,$conditions,$keynames=array());
			return $wryItemId;
		}else{
		  $params['creation_date'] =mysqlNow(); 
		  $id=insert_query("worry_alert",$params,$safe=0);
			return $id;
		}
		
	}
	
	function addWryItems($data,$itemId){
	 $sql="select id,title as worry_title from upload_worry where id in (".$data['sequence'].")";
	 $getResult=exec_query($sql);
		$arrItems = array();
		foreach($getResult as $key=>$val){
			$arrItems['worry_item_id'] = $itemId;
			$arrItems['worry_id '] = $val['id'];
			$arrItems['worry_title '] = $val['worry_title'];
			foreach($data['summary'] as $sumKey=>$sumVal){
				if($val['id'] == $sumKey){
					$arrItems['worry_summary '] = $sumVal;
				}
				$arrItems['worry_summary ']=addslashes(mswordReplaceSpecialChars(stripslashes($arrItems['worry_summary '])));
			} // foreach summary
			$id = insert_or_update("worry_items",$arrItems,array('worry_item_id'=>$itemId,'worry_id'=>$val['id']));
			
			
			
		}//foreach title
		return 	$id;
	}
	
	function prepareDraftWry($data,$wryItemId=""){
		$params['updation_date'] = date('Y-m-d h:i:s');
		if($data['publish_date']=="" || $data['publish_date']=="0000-00-00 00:00:00" )
		{
			$params['publish_date'] =  date('Y-m-d h:i:s');
		}	
		else
		{
			$params['publish_date'] = $data['publish_date'];			
		}
		$params['worry_sequence'] = $data['sequence'];
		$params['is_draft'] = '1';
		$params['is_approve'] = '0';
		$params['is_live'] = '0';
		$params['is_deleted'] = '0';
		if($wryItemId){
		    $conditions['id']=$wryItemId;
			$id=update_query("worry_alert",$params,$conditions,$keynames=array());
			return $wryItemId;
		}else{
		    $id=insert_query("worry_alert",$params,$safe=0);
			return $id;
		}
	}
	
	function updateWry($approve,$viewapproved,$publishdatefield){
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
		foreach($approve as $wry_id => $status)
		{
			if($viewapproved=='1')
			{
				$params['is_approve'] = '0';
				$params['is_live'] = '0';
				
				$id = update_query("worry_alert",$params,array(id=>$wry_id));
				$objContent = new Content('worry_alert',$wry_id);	
				$objContent->removeUnapprovedItems($wry_id,"worry_alert");
			}
			else
			{
				/*$publish_date_stamp = mktime($publishdatefield[hour][$wry_id],$publishdatefield["min"][$wry_id],0, $publishdatefield[mo][$wry_id],$publishdatefield[day][$wry_id],$publishdatefield[year][$wry_id]);*/
				$publish_date_stamp = strtotime($publishdatefield[year][$wry_id]."-".$publishdatefield[mo][$wry_id]."-".$publishdatefield[day][$wry_id]." ".$publishdatefield[hour][$wry_id].":".$publishdatefield["min"][$wry_id].":00");
				if($publish_date_stamp)
				{
					$publish_date=mysqlNow($publish_date_stamp);
					if($publish_date_stamp > time())
					{
						$params['is_live'] = 0;
					}
					else
					{
						$params['is_live'] = 1;
					}
					$params['publish_date'] = $publish_date;
				}
				else
				{
					// $publish_date = '0000-00-00 00:00:00';
					$params['is_live'] = 1;
				}
				
				
				$params['is_draft'] = '0';
				$params['is_approve'] = '1';
				
				$id = update_query("worry_alert",$params,array(id=>$wry_id));
				$objContent = new Content('worry_alert',$wry_id);	
				$objContent->setWallOfWorryMeta();
			}
		}
		return $id;
	}
	
	function deleteWry($arrDel){
		$keys = implode(",",array_keys($arrDel));
		$qry="update worry_alert set is_deleted='1' WHERE find_in_set(id,'$keys') LIMIT ".count($arrDel);
		$id = exec_query($qry);
		if($id){
			return $id;
		}
	}
	
	function getAllWorrys(){
		$qry="select * from upload_worry order by title";
		$result=exec_query($qry);
		if($result){
			return $result;
		}
		return false;
	}
	
	function getWorrysById($id){
		$qry="select * from upload_worry where id='".$id."'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}
		return false;
	}
        
        function deteteWorryItem($id,$worrySequence,$worryId){
		if($id && $worryId){
			$params['worry_sequence']=$worrySequence;
			update_query("worry_alert",$params,array(id=>$id));
			$qry="delete from worry_items where worry_item_id='".$id."' and worry_id='".$worryId."'";
			$result=exec_query($qry);
		}
	}
	
	function dragWorry($id,$worrySequence){
		if($id){
			$params['worry_sequence']=$worrySequence;
			update_query("worry_alert",$params,array(id=>$id));
		}
	
	}
	
	function getArchive(){
		$qry = "SELECT DISTINCT MONTHNAME(publish_date) as month,YEAR(publish_date) as year FROM worry_alert WHERE is_live='1'AND is_approve='1' AND is_deleted='0' order by publish_date desc";
		$result = exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function getMonthlyArchive(){
		$qry ="SELECT DISTINCT WA.id,WI.worry_id,WU.archive_img, IF(WA.publish_date,WA.publish_date,WA.creation_date) AS publish_date FROM worry_alert WA, worry_items WI, upload_worry WU WHERE WA.id = WI.worry_item_id AND WI.worry_id = WU.id AND is_live='1'AND is_approve='1' AND is_deleted='0' AND YEAR(WA.publish_date)='2011' AND MONTHNAME(WA.publish_date)='May'";
		$result = exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	function getLatestWryDate(){
		$qryLatestDate = "SELECT id,IF(publish_date,publish_date,creation_date) AS publish_date FROM worry_alert WHERE is_live='1'AND is_approve='1' AND is_deleted='0' AND is_draft='0' ORDER BY publish_date DESC LIMIT 1,1";
		$resLatestDate = exec_query($qryLatestDate,1);
		if($resLatestDate['id']!=''){
			return $resLatestDate['publish_date'];
		}else{
			return false;
		}
	}
	
	function getCurrentWryDate(){
		$qryLatestDate = "SELECT id,IF(publish_date,publish_date,creation_date) AS publish_date FROM worry_alert WHERE is_live='1'AND is_approve='1' AND is_deleted='0' AND is_draft='0' ORDER BY publish_date DESC LIMIT 1";
		$resLatestDate = exec_query($qryLatestDate,1);
		if($resLatestDate['id']!=''){
			return $resLatestDate['publish_date'];
		}else{
			return false;
		}
	}

	function getLatestWorry($pubDate){
		$qryLatestId = "SELECT id,IF(publish_date,publish_date,creation_date) AS publish_date,worry_sequence FROM worry_alert WHERE  is_deleted='0' and DATE_FORMAT(IF(publish_date>creation_date,publish_date,creation_date),'%c/%e/%Y')='".$pubDate."'"; 
		if(!$_SESSION['AMADMIN']){
			$qryLatestId .= " and is_live='1'AND is_approve='1'  AND is_draft='0'  ";
		}
		$resLatestId = exec_query($qryLatestId,1);
		if($resLatestId['id']){
			$latestWry = $this->getWorryDetails($resLatestId);
			return $latestWry;
		}else{
			return false;
		}
	}
	
	function getWorryByDate($date){
		$qryWryDate = "SELECT id,IF(publish_date,publish_date,creation_date) AS publish_date,worry_sequence FROM worry_alert WHERE is_live='1'AND is_approve='1' AND is_deleted='0' AND publish_date='".$date."' LIMIT 1";
		$resWryDate = exec_query($qryWryDate,1);
		if($resWryDate['id']!=''){
			$wryByDate = $this->getWorryDetails($resWryDate);
			return $wryByDate;
		}else{
			return false;
		}

	}
	
	function getWorryDetails($arrResult){
		$sequence = explode(",",$arrResult['worry_sequence']);
		$allWorry=array();
		foreach($sequence as $key=>$val){
			$qryWry ="SELECT DISTINCT WI.worry_id,WU.title,WU.worry_img,WU.archive_img,WI.worry_summary, IF(WA.publish_date,WA.publish_date,WA.creation_date) AS publish_date FROM worry_alert WA, worry_items WI, upload_worry WU WHERE WA.id = WI.worry_item_id AND WI.worry_id = WU.id AND WA.id='".$arrResult['id']."' AND WI.worry_id='".$val."'";
			$resWry = exec_query($qryWry,1);
			$allWorry[] = $resWry;
		}
		
		if($allWorry){
			return $allWorry;
		}else{
			return false;
		}
	}
	
	function getArchivesByMonth($month,$year){
		$qryMonth = "SELECT id,IF(publish_date,publish_date,creation_date) AS publish_date,worry_sequence FROM worry_alert WHERE MONTHNAME(publish_date) = '".$month."' AND YEAR(publish_date)='".$year."' AND is_live='1' AND is_approve='1' AND is_deleted='0' AND is_draft='0' ORDER BY publish_date DESC ";
		$resMonth = exec_query($qryMonth);
		if(!empty($resMonth)){
			$monthlyWry = array();
			foreach($resMonth as $key){
				$monthlyWry[] = $this->getWorryDetails($key);
			}
			
			return $monthlyWry;
			
		}else{
			return false;
		}
		
	}
	
	function getDraftWallofWorry($id){
		$sql="select * from worry_alert where is_deleted='0' and is_approve='0' and is_draft='1' and id<>'".$id."'";
		//echo "<br>".$sql;
		$result=exec_query($sql);
		if(is_array($result)){
			return$result;
		}
		return false;
	}
	
	function getCurrentDayWorry(){
		$qry="select id,worry_sequence from worry_alert where DATE_FORMAT(publish_date,'%c/%e/%Y') = DATE_FORMAT('".mysqlNow()."','%c/%e/%Y')";
		$getResult=exec_query($qry,1);
		if(is_array($getResult)){
			return $getResult;
		}
		return false;
	}
	
	function addWorry($id,$worryId,$title,$worrySequence){
		$params['worry_item_id'] = $id;
		$params['worry_id'] = $worryId;
		$params['worry_title'] = $title;
		$params['date'] =mysqlNow();
		insert_query("worry_items",$params);
		$this->dragWorry($id,$worrySequence);
	}
	
	function updateSummary($id,$worryId,$summary){
		$params['worry_summary'] = $summary;
		update_query("worry_items",$params,array(worry_item_id=>$id,worry_id=>$worryId));
	}
}//class end
?>