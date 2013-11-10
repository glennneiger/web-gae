<?php
Class Sphinx {
	function __construct(){
		global $D_R;
		include_once $D_R.'/lib/config/_sphinx.php';
		global $sphinxServer,$sphinxPort;
		$this->sphinx = new SphinxClient();
		$this->sphinx->SetMaxQueryTime(10000);
		$this->sphinx->SetMatchMode(SPH_MATCH_EXTENDED2);
		$this->sphinx->SetArrayResult(true);
		$this->sphinx->SetSortMode(SPH_SORT_TIME_SEGMENTS);
		$this->sphinx->SetFieldWeights(array('title' => 10, 'content' => 40));
	}

	function getSearchResults($q,$contrib_id,$mo,$day,$year,$object_type,$offset=1,$title=NULL){
		global $contentcount;
		if(!empty($contrib_id)){
			$this->sphinx->SetFilter('author_id', array($contrib_id));
		}
		if(!empty($object_type)){
			$this->sphinx->SetFilter('item_type', array($object_type));
		}
		if(!empty($title)){
			$this->sphinx->SetFilter('title', array($title));
		}
		if(!empty($day)){
			$this->sphinx->SetFilter('day', array($day));
		}
		if(!empty($mo)){
			$this->sphinx->SetFilter('month', array($mo));
		}
		if(!empty($year)){
			$this->sphinx->SetFilter('year', array($year));
		}
		$this->sphinx->setLimits($offset,$contentcount);
		$result= $this->sphinx->Query($q);
		if(!empty($result['matches'])){
			foreach($result['matches'] as $value){
				$IDs[]=$value['id'];
			}
		}
		$IDs = implode(",",$IDs);
		$this->lastQueryMatchCount=$result['totals'];
     	$sqlSearchResults = "SELECT * FROM `ex_item_meta` WHERE `id` IN ($IDs) ORDER BY FIELD(`id`,$IDs)";
     	$resSearchResults = exec_query($sqlSearchResults);
     	if(!empty($resSearchResults)){
     		return $resSearchResults;
     	}else{
     		return NULL;
     	}
	}
}
?>