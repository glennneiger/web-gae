<?php
class contributor{
	function getContributor($id=NULL,$name=NULL){
		if($id){
			$sqlGetContributor="SELECT * FROM contributors WHERE id='".$id."'";
			$resGetContributor=exec_query($sqlGetContributor,1);
		}elseif($name){
			$sqlGetContributor="SELECT * FROM contributors WHERE LCASE(name)='".strtolower($name)."'";
			$resGetContributor=exec_query($sqlGetContributor,1);
		}else{
			return NULL;
		}
		return $resGetContributor;
	}
	function setContributor($contributor){
		$contributorDetails=$this->getContributor(NULL,$contributor['name']);
		if($contributorDetails){
			return $contributorDetails['id'];
		}else{
			if(!$contributor['has_bio'])
				$contributor['has_bio']='0';
			echo $id=insert_query("contributors",$contributor);
			return $id;
		}
	}

	function getAllGroupsList(){
		$sqlGroupList="SELECT * FROM contributor_groups WHERE is_active='1' order by id asc";
		$resGroupList=exec_query($sqlGroupList);
		return $resGroupList;
	}

	function getContributorGroupsList($contribId){
		$sqlContributorGroups="SELECT CGM.group_id FROM contributor_groups_mapping CGM,contributor_groups CG
		WHERE CG.id=CGM.group_id AND CG.is_active='1' AND contributor_id='".$contribId."'";
		$resContributorGroups=exec_query($sqlContributorGroups);
		return $resContributorGroups;
	}

	function getContributorMeta($contribId){
		$sqlContributorMeta="SELECT title,description,author_name FROM ex_item_meta WHERE item_type='21' AND item_id='".$contribId."'";
		$resContributorMeta=exec_query($sqlContributorMeta,1);
		return $resContributorMeta;
	}
	function getContributorsByGroup($groupId)
	{
		$sqlContributorByGroup="SELECT c.id, c.user_id, c.bio_asset, c.logo_asset, c.description, c.disclaimer,
			  c.name, c.email,c.suspended, c.has_bio,c.small_bio_asset FROM contributors c, contributor_groups_mapping cg 
			  where c.suspended='0' and c.has_bio='1' and c.id=cg.contributor_id and cg.group_id='".$groupId."' ORDER BY c.name asc";
		$resContributorByGroup=exec_query($sqlContributorByGroup);
		return $resContributorByGroup;
	}
	
	function getExcludedPartnerId(){
		$getFilterContribId="SELECT CGM.contributor_id FROM contributor_groups_mapping CGM,
	contributor_groups CG WHERE CG.id=CGM.group_id AND CG.group_name in ('Exclude from Partner Feeds') order by CGM.contributor_id";
		$getFilterResult=exec_query($getFilterContribId);
		$contribFilterId="";
		if($getFilterResult){
			foreach($getFilterResult as $key=>$value){
				if($key=="0"){
					$contribFilterId=$value['contributor_id'];
				}else{
					$contribFilterId.=','.$value['contributor_id'];
				}
			}
		}
		return $contribFilterId;
	}
	function getContributorsID($sectionId)
	  {
	     $sql ="SELECT CAS.id,C.id,CAS.section_id,C.name,C.twitter_username,CG.group_name,C.email FROM 
contributors_articles_section CAS,contributors C,contributor_groups_mapping CGM,contributor_groups CG 
WHERE CAS.contrib_id=C.id AND CGM.contributor_id=C.id AND CG.id=CGM.group_id 
AND CAS.section_id=".$sectionId." AND C.suspended='0' AND C.has_bio='1' GROUP BY C.id";
		 $result = exec_query($sql);
		 return $result;	
	  }

function getArticleContributorsID($sectionId)
	  {
	     $sql =" SELECT A.id,A.contrib_id,C.name,C.twitter_username,C.email,
 IF(A.publish_date,A.publish_date,A.date) displayDate
 FROM articles A, `contributors` C
 WHERE A.contrib_id=C.id
 AND A.approved='1' AND A.is_live='1' ORDER BY displayDate DESC
 LIMIT 0,16";

		 $result = exec_query($sql);
		 return $result;
	  }

}

?>