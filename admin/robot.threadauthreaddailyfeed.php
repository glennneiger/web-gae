<?

include_once("$DOCUMENT_ROOT/lib/_includes.php");
global $defaultSudId; 

$qry="select DF.id,DF.contrib_id,DF.excerpt,DF.body from ex_thread ET, daily_feed DF where ET.content_table='daily_feed' and ET.content_id=DF.id";
$result=exec_query($qry);

foreach($result as $row){
	$sql="select subscription_id from ex_contributor_subscription_mapping where contributor_id='".$row['contrib_id']."'";
	$getSubID=exec_query($sql,1);
    if($getSubID['subscription_id']){
		$parms['author_id']=$getSubID['subscription_id'];
	}else{
		$parms['author_id']=$defaultSudId;
	}
	
	
	if($row['excerpt']){
		$teaser = addslashes(substr($row['excerpt'],0,400));
	}else{
		$teaser = addslashes(substr($row['body'],0,400));
	}

	$parms['teaser']=$teaser;
	
	update_query("ex_thread",$parms,array(content_id=>$row['id'],content_table=>'daily_feed'));	
	
	
	
	echo "<br>".'-ContibID-'.$row['contrib_id'].'---SubID-'.$parms['author_id'];
}

?>
