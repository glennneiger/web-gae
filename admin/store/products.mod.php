<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");

$store=new Store();
$store->admin=1;


/*========data formatting=======*/
$msg="";
$prod=&$_POST[prod];
$image=&$_FILES[big_image];
if(!is_array($prod[cat_ids]))$prod[cat_ids]=array();
if(is_array($del))$del=key($del);
$prod[cat_ids]=implode(",",trim_arr($prod[cat_ids],1));
$prod[prod_ids]=implode(",",trim_arr($prod[prod_ids],1));
$prod=trim_arr($prod);
/*========== file deletion ============*/
if($del){
	$store->deleteProductImages($del);
	$msg="The image was remvoed<br>";;
}

/*============== database insert/update ===========*/
if(!$deleteproduct){
	if($id){
		$msg.="The product was updated<br>";
	}else{
		$msg.="The product was created<br>";
	}
	$id=$store->setProduct($prod, $id);//if id isn't there it should create it
	if($id && $image)	$store->createProductImages($id, $image );
}else{
	$store->deleteProduct($id);
	unset($id);
	$msg="The product and it's associated images were removed<br>";
}


/*=========== redirection ===========*/
//they changed the category id so use the first one they chose 4 redir
if($cat_id){
	$cats=explode(",",$prod[cat_ids]);
	if(!count($prod[cat_ids])){
		unset($cat_id);
	}elseif(!in_array($cat_id, $cats)){
		unset($cat_id);
	}else{
		$cat_id=$cats[0];
	}
}
location( $refer.qsa(array(id=>$id, cat_id=>$cat_id, error=>$msg))  );
?>
