<?
class articleData{
	
	function getAllAricles($ID,$start,$end)
	   {
	   	global $D_R;
	   	include_once("$D_R/lib/_layout_design_lib.php");
	      if (empty($ID))
		      {
			  
	     $results = getArticleListing($start,$end);
	          }
		  else
		      {
 $sql ="SELECT articles.id id, articles.title, contributors.name author, articles.contributor, section.name section, section.section_id,articles.subsection_ids, articles.position, contrib_id author_id, 
IF(articles.publish_date,articles.publish_date,articles.date) AS date, EIT.url, body, POSITION, character_text FROM articles, section, contributors,ex_item_meta EIT WHERE articles.contrib_id = contributors.id AND FIND_IN_SET('" .$ID ."',articles.subsection_ids) AND articles.approved='1' AND articles.is_live='1' AND section.section_id = '" .$ID ."' AND EIT.item_id=articles.id AND EIT.item_type='1' ORDER BY date DESC LIMIT ".$start.",".$end;
		$results = exec_query($sql);
	           }
	      return $results;
	   }
	 function getLatestAricles($ID)
	   {
	      if (empty($ID))
		      {
		 $sql="SELECT A.id, A.title,A.body, C.NAME author, EIT.author_id, IF(A.publish_date,A.publish_date,A.DATE) AS DATE, EIT.url,EIT.item_type FROM articles A, contributors  C,ex_item_meta EIT WHERE A.contrib_id = C.id AND A.approved='1' AND A.is_live='1' AND EIT.item_type='1' AND A.id = EIT.item_id AND EIT.publish_date>('".mysqlNow()."' - INTERVAL 1 MONTH) ORDER BY DATE DESC LIMIT 0,1";
		 $results = exec_query($sql);
	     
	          }
		  else
		      {
 $sql ="SELECT articles.id id,articles.body body, articles.title, contributors.name author, articles.contributor, section.name section, section.section_id,articles.subsection_ids, articles.position, contrib_id author_id, 
IF(articles.publish_date,articles.publish_date,articles.date) AS date, EIT.url, body, POSITION, character_text FROM articles, section, contributors,ex_item_meta EIT WHERE articles.contrib_id = contributors.id AND FIND_IN_SET('" .$ID ."',articles.subsection_ids) AND articles.approved='1' AND articles.is_live='1' AND section.section_id = '" .$ID ."' AND EIT.item_id=articles.id AND EIT.item_type='1' ORDER BY date DESC LIMIT 0,1";
		$results = exec_query($sql);
	           }
	      return $results;
	   }
	   

}//class end
?>