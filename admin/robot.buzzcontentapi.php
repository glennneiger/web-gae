<?php
global $D_R;
 ini_set('max_execution_time', 5000);
include_once($D_R.'/lib/_action.php');
$objAction= new Action();

$buzzSql="SELECT id,buzzid FROM buzz_content_api WHERE sent='0' AND is_deleted='0' order by id desc limit 5";
$getResult=exec_query($buzzSql);
if(!empty($getResult)){
    foreach($getResult as $val){
        $id=$val['buzzid'];
        $objAction->trigger('buzzDataPublish',$id);
    }
}

?>