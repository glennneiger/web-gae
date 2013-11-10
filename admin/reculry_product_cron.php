<?php
set_time_limit(0);
ini_set('memory_limit','256M');
$qry = "SELECT * FROM subscription_cust_address order by viaid";
$result=exec_query($qry);
if(!empty($result)){
	 foreach($result as $row){
	 	if($row['address3']!=''){
	 		$address2 = $row['address2'].','.$row['address3'];
	 	}else{
	 		$address2 =$row['address2'];
	 	}
  		$productArr=array();
 		$productArr['address']=$row['address1'];
 		$productArr['address2']=$address2;
 		$productArr['city']=$row['city'];
 		$productArr['state']=$row['state'];
 		$productArr['zip']=$row['zip'];
 		$productArr['country']=$row['country'];

 		update_query("subscription",$productArr,array("via_id"=>$row['viaid']));
 		echo 'viaid---'.$row['viaid'].'--updated<br>';
	 }
	 echo "Data updated";
}
?>