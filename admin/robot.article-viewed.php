<?include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$DOCUMENT_ROOT/lib/_db.php");
set_time_limit ( 60*30 );//1 hour
$days=1;

echo "Inserting Records";

del_query("article_recent","1","1");
del_query("article_emailed","1","1");
$sqlGetSections="SELECT section_id FROM section where is_active='1' and subsection_type='article'";
$resGetSections=exec_query($sqlGetSections);

foreach($resGetSections as $sec){
	 $sql = "select articles.id, articles.title,articles.contrib_id,articles.date,articles.subsection_ids,articles.is_live,articles.approved,articles.keyword,articles.blurb,count(tracking_view.time) as total from articles ";
	$sql .= "join tracking_view on articles.id = tracking_view.id ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and tracking_view.type = 'article' ";
	$sql .= "and find_in_set('".$sec['section_id']."',subsection_ids) ";
	$sql .= "and tracking_view.time between date_sub('".mysqlNow()."',interval 2 hour) and '".mysqlNow()."' ";
	$sql .= "group by tracking_view.id ";
	$sql .= "order by total desc limit 10";

	foreach(exec_query($sql) as $id=>$value)
	{
		$articlerecent['id']=$value[id];
		$articlerecent['title']=htmlentities($value["title"],ENT_QUOTES);
		$articlerecent['keyword']=htmlentities($value["keyword"],ENT_QUOTES);
		$articlerecent['blurb']=htmlentities($value["blurb"],ENT_QUOTES);
		$articlerecent['total']=$value[total];
		$articlerecent['subsection_ids']=$value['subsection_ids'];
		$articlerecent['contrib_id']=$value[contrib_id];
		$articlerecent['is_live']=$value[is_live];
		$articlerecent['approved']=$value[approved];
		$articlerecent['date']=$value[date];
		$cond['id']=$value['id'];

		$artclerecentdata=insert_or_update("article_recent",$articlerecent,$cond);
	}
}


foreach($resGetSections as $sec){
	$sql = "select articles.id, articles.title,articles.contrib_id,articles.date,articles.subsection_ids,articles.is_live,articles.approved,articles.keyword,articles.blurb,count(TE.time) as total from articles ";
	$sql .= "join tracking_email TE on articles.id = TE.id ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and TE.type = 'article' ";
	$sql .= "and find_in_set('".$sec['section_id']."',subsection_ids) ";
	$sql .= "and TE.time between date_sub('".mysqlNow()."',interval 7 day) and '".mysqlNow()."' ";
	$sql .= "group by TE.id ";
	$sql .= "order by total desc limit 10";

	foreach(exec_query($sql) as $id=>$value)
	{
		$articlerecent['id']=$value[id];
		$articlerecent['title']=htmlentities($value["title"],ENT_QUOTES);
		$articlerecent['keyword']=htmlentities($value["keyword"],ENT_QUOTES);
		$articlerecent['blurb']=htmlentities($value["blurb"],ENT_QUOTES);
		$articlerecent['total']=$value[total];
		$articlerecent['subsection_ids']=$value['subsection_ids'];
		$articlerecent['contrib_id']=$value[contrib_id];
		$articlerecent['is_live']=$value[is_live];
		$articlerecent['approved']=$value[approved];
		$articlerecent['date']=$value[date];
		$cond['id']=$value['id'];

		$artclerecentdata=insert_or_update("article_emailed",$articlerecent,$cond);
	}
}

echo "Insertion Done";

?>