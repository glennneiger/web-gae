<?php

$qry="select id,recurly_plan_code from product";

$getResult=exec_query($qry);
foreach($getResult as $row){
   // htmlprint_r($row);
    if(!empty($row['recurly_plan_code'])){
        
        echo "<br>".$row['recurly_plan_code'];
        $pos = strpos($row['recurly_plan_code'],'stndrd');
        echo "<br>".'pos-- '.$pos;
        if(strpos($row['recurly_plan_code'],'stndrd')>1){
            $params['recurly_plan_type']='standard';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'stdrd')>1){
            $params['recurly_plan_type']='standard';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'comp')>1){
            $params['recurly_plan_type']='complimentary';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'freenoar')>1){
            $params['recurly_plan_type']='free trial no auto renew';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'199')>1){
            $params['recurly_plan_type']='$199';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'299')>1){
            $params['recurly_plan_type']='$299';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }elseif(strpos($row['recurly_plan_code'],'20off')>1){
            $params['recurly_plan_type']='20% off';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }
        elseif(strpos($row['recurly_plan_code'],'30off')>1){
            $params['recurly_plan_type']='30% off';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }
        elseif(strpos($row['recurly_plan_code'],'31off')>1){
            $params['recurly_plan_type']='31% off';
            $conditions['id']=$row['id'];
            update_query('product',$params,$conditions,$keynames=array());
        }
        
        
    }
    
}


?>
