<?
    $qry="SELECT id,orderNumber, count(orderNumber) as cnt FROM subscription_cust_order where orderNumber<>'0' 
    group by orderNumber,typeSpecificId HAVING cnt>1 limit 8000";
    $getResult=exec_query($qry);
    
    foreach($getResult as $row){
               
               if($row['cnt']>=2){
                  htmlprint_r($row);
                  $param='id';
                  $value=$row['id'];
                  del_query('subscription_cust_order',$param,$value,$optimize=0);
               }
    }

?>