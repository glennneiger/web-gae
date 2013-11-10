<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");

if(!$refer)$refer=base64_encode("./");
$refer=base64_decode($refer);

if(!$id || !$ACTION){
	location($refer);
	exit;
}
if(!is_array($id))$id=array($id);


foreach($id as $i){
/*=============== remove orders ================*/
	if($ACTION=="remove"){
		del_query($ORDER_TABLE,"id",$i);

/*===============edit orders ===================*/
	}elseif($ACTION=="editorder"){
		$ord[cc_expire]=sprintf("%02d/%4d",$cc_mo,$cc_year);
		update_query("$ORDER_TABLE",$ord,array(id=>$i));
		/*===customer notification=========*/
		if($sendmessage){
			mymail($mail_to,$mail_from,$mail_subject,nl2br($mail_message));
		}


/*============== ship and charge orders ============*/
	}elseif($ACTION=="shiporder"){
		echo "<body style=cursor:wait>Processing...</body>".str_repeat("\n",1000);
		flush();
		$res=yourpayApproveSales($id);
		$redir=array(refer=>$refer,res=>mserial($res));
		persist("orders.charge.result.php",$redir);
		exit();
		break;
	}
}

location($refer);


?>