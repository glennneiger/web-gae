<?php
global $D_R;
set_time_limit ( 60*30 );//1 ho
include_once($D_R."/lib/_includes.php");
include_once("$D_R/lib/_user_data_lib.php");
echo "<br>"."Please run this cron job only once";
$objUserData= new userData();
$qry="select id,email,password from subscription where password<>'' and updated='0' limit 5000";
$getResult=exec_query($qry);

if(!empty($getResult)){
    //htmlprint_r($getResult);
    foreach($getResult as $row){
        $getPasswd=base64_decode($row['password']);
        $getEncryptedPaswd=$objUserData->encryptUserPassword($getPasswd);
        $params['password']=$getEncryptedPaswd;
        $params['updated']=1;
        $conditions['id']=$row['id'];
        update_query('subscription',$params,$conditions,$keynames=array());
        echo "<br>".'user id-- '.$row['id'].' '.'updated';
    }
    
}

?>