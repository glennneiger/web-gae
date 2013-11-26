<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_cart.php");
	$store=new Store();
	$store->admin=1;
	
	foreach($prods as $ordr=>$id){
		if(!$id)continue;
		$store->setProduct(array(ordr=>($ordr+1)),$id);
	}

	location($refer.qsa(array(cat_id=>$cat_id)));
?>