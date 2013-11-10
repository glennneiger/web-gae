<?php

$qry="select ET.id,ET.author_id,A.contrib_id,ESM.subscription_id from ex_thread ET, articles A,ex_contributor_subscription_mapping ESM where ET.content_table='articles' and ET.content_id= A.id and ESM.contributor_id=A.contrib_id";
$result=exec_query($qry);

if($result){
	 foreach($result as $value) {
		  $table="ex_thread";
		  if($value['subscription_id']){
		  	$params['author_id']=$value['subscription_id'];
		  }else{
		  	$params['author_id']='12757';
		  }
		  $conditions['id']=$value['id'];
		  update_query($table,$params,$conditions,$keynames=array());
	 }
	 
	 echo "<br>"."Updated...";
}


?>