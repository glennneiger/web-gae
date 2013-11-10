<?php

$qry="select id,url from ex_item_meta where item_type='18'";
$getResult=exec_query($qry);

if(!empty($getResult)){

    foreach($getResult as $row){
        $getNewUrl=str_replace('dailyfeed','mvpremium',$row['url']);
        echo "<br>".$getNewUrl;
        $params['url']=$getNewUrl;
        $conditions['id']=$row['id'];
        $conditions['item_type']='18';
        $id=update_query('ex_item_meta',$params,$conditions,$keynames=array());
    }

}

$qrySeo="select id,url from content_seo_url where item_type='18'";
$getResultSeo=exec_query($qrySeo);

if(!empty($getResultSeo)){

    foreach($getResultSeo as $row){
        $getNewSeoUrl=str_replace('dailyfeed','mvpremium',$row['url']);
        echo "<br>".$getNewSeoUrl;
        $params['url']=$getNewSeoUrl;
        $conditions['id']=$row['id'];
        $conditions['item_type']='18';
        $id=update_query('content_seo_url',$params,$conditions,$keynames=array());
    }

}


?>