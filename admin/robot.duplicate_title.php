<?php
set_time_limit (0);
ini_set('memory_limit','256M');
$item_type=$_GET['item_type'];
$qry="SELECT id,title,COUNT(title) AS cnt FROM ex_item_meta WHERE item_type='".$item_type."' AND is_live='1'
GROUP BY title HAVING cnt >=2";
$result = exec_query($qry);
if(!empty($result)){
	foreach($result as $key=>$val){
		$qryDuplicate = "select id,item_id from ex_item_meta where title='".$val['title']."' AND is_title_duplicate='0'";
		$resDuplicate = exec_query($qryDuplicate);
		if(!empty($resDuplicate)){
			foreach($resDuplicate as $keyDup){
				$params['is_title_duplicate']='1';
				$id = update_query('ex_item_meta', $params, array(item_id=>$keyDup['item_id'],item_type=>$item_type));
				if(!empty($id)){
					echo 'Duplicate Flag has been updated for Article Id:'.$keyDup['item_id'].'<br/>';
				}
			}
		}
	}
}else{
	echo 'No Result Found';
}
?>