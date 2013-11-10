<?php
$qry = "SELECT DISTINCT(item_id) FROM article_meta WHERE item_key IN ('radiofile','audiofile')";
$res = exec_query($qry);
if(!empty($res)){
	foreach($res as $key){
		$params['is_audio']='1';
		$id = update_query('articles', $params, array(id=>$key['item_id']));
		if(empty($id)){
			echo 'Table has been updated for Article Id:'.$key['item_id'].'.<br>';
		}
	}
}
?>