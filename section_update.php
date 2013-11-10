<?php
global $D_R;
ini_set('memory_limit', '64M');

echo $qry="SELECT * FROM articles WHERE publish_date <= '".mysqlNow()."' AND publish_date >= '".mysqlNow()."'-INTERVAL 1 MONTH ORDER BY id ";

$articleList=exec_query($qry);
foreach($articleList as $key=>$val)
{
	if(!empty($val['navigation_section_id']) || $val['navigation_section_id']!="0")
	{
		$sectionArr = explode(',',$val['subsection_ids']);
		if(!in_array($val['navigation_section_id'],$sectionArr))
		{
			$sectionArr[] = $val['navigation_section_id'];
			$newSectionArr = implode(',',$sectionArr);
			$articles['subsection_ids'] = $newSectionArr;
			update_query("articles",$articles,array(id=>$val[id]));
		}
	}
}

?>