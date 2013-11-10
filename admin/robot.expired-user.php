<?php
global $D_R,$toExpireSubs,$fromExpireSubs,$subjectExpireSubs,$toExpireBccSubs;
include_once($D_R."/lib/_includes.php");
include_once($D_R."/lib/config/_recurly_config.php");

$qry="select SCO.viaid,SCO.subscription_id,S.email,P.product,SCO.price,SCO.startDate,SCO.expireDate,
SCO.billDate, SCO.description,SCO.orderStatus,SCO.paymentStatus,
P.recurly_plan_code from subscription_cust_order SCO, product P,subscription S
where SCO.orderClassId=P.oc_id and SCO.orderCodeId=P.order_code_id
and P.order_code_id<>'9' and P.order_code_id<>'3' and
 P.order_code_id<>'14' and P.order_code_id<>'34' and P.order_code_id<>'47'
and P.order_code_id<>'77' and P.order_code_id<>'71' and P.order_code_id<>'81'
 and SCO.typeSpecificId=P.subscription_def_id and S.id=SCO.subscription_id
and (DATE_FORMAT(SCO.expireDate,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d'))
and SCO.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED')
 and SCO.viaid<>'0' and SCO.updated='0' limit 1000";

$getResult=exec_query($qry);
if(!empty($getResult)){
	$msg="";
	$msg.='<table border="1">';
	$msg.='<tr><td>Email</td><td>Cuatomer Via Id</td><td>Recurly Account Code</td><td>Product</td><td>Price</td><td>Start Date</td><td>Expire Date</td><td>Bill Date</td><td>Description</td></tr>';
	foreach($getResult as $row){
		$msg.='<tr>';
		$msg.='<td>'.$row['email'].'</td>';
		$msg.='<td>'.$row['viaid'].'</td>';
		$msg.='<td>'.$row['subscription_id'].'</td>';
		$msg.='<td>'.$row['product'].'</td>';
		$msg.='<td>'.$row['price'].'</td>';
		$msg.='<td>'.$row['startDate'].'</td>';
		$msg.='<td>'.$row['expireDate'].'</td>';
		$msg.='<td>'.$row['billDate'].'</td>';
		$msg.='<td>'.$row['description'].'</td>';
		$msg.='</tr>';
	}
	$msg.='</table>';
	$bodyMessage=$msg;
}else{
	$bodyMessage="No user found today.";
}
	$to=$toExpireSubs;
	$from=$fromExpireSubs;
	$subject=$subjectExpireSubs.' - '.date('M d, Y');
	$body=$bodyMessage;
    mymail($to,$from,$subject,$body,$text="",$file="",$ftype="application/octet-stream",$return_str=0,$bcc=null);
	
?>


