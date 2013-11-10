<?php

class mailChimp{

	function mailChimpListNames() {
	  global $mailChimpApiKey;
		$objMailChimp = new MCAPI($mailChimpApiKey);
		$getAllLists=$objMailChimp->lists($filters=array (), $start=0, $limit=25);
		if(!empty($getAllLists)){
			return $getAllLists;
		}else{
			return false;
		}
	}

	function mailChimpListGroupNames($list_id)
	{
		global $mailChimpApiKey;
		$objMailChimp = new MCAPI($mailChimpApiKey);
		$getAllGroupLists=$objMailChimp->listInterestGroupings($list_id);
		if(!empty($getAllGroupLists)){
			return $getAllGroupLists;
		}else{
			return false;
		}
	}

	function getMailChimpDataById($id){
		$sql="select listid,listname,imagename,group_name,group_id from fancybox_image where id='".$id."' or listid='".$id."'";
		$getResult=exec_query($sql,1);
		if(!empty($getResult)){
			return $getResult;

		}else{
			return false;
		}

	}

	function getMailChimpActiveList(){
		$qry="select listid,listname,imagename,group_name,group_id from fancybox_image where is_active='1'";
		$getResult=exec_query($qry,1);
		if(!empty($getResult)){
			return $getResult;
		}else{
			return false;
		}
	}

}


?>