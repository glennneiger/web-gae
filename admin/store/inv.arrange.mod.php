<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
	$store=new Store();
	$store->admin=1;
	
	foreach($inv as $ordr=>$iid){
		if(!$iid)continue;
		$store->setInventory(array(ordr=>($ordr+1)),$iid);
	}


	
	location($refer.qsa(array(cat_id=>$cat_id,id=>$id)));
?>