<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");


$store=new Store();
$store->admin=1;

/*======= error handling ===========*/
if(!$store->isValidProductId($id)){
	$msg="You cannot edit inventory of a non-existent product!";
	location($refer.qsa(array(error=>$error)));
	exit;
}


/*========data formatting=======*/
$msg="";
$inv=&$_POST[inv];
$new=trim_arr($_POST["new"],1);
$newimage=$_FILES[newimage];
$newfiles=array();
if(!is_array($inv))$inv=array();
if(!is_array($del))$del=array();
if(!is_array($delfile))$delfile=array();
foreach($_FILES as $name=>$f){
	list($xx, $inv_id)=explode("_",$name,2);
	if( !$store->isValidInventoryId($inv_id) || !$f[size])continue;
	$newfiles[$inv_id]=$f;
}


/*========== file deletion ============*/
foreach($delfile as $inv_id=>$on){
	$store->deleteInventoryImages($inv_id);
}

/*========= inventory deletion ===========*/
foreach($del as $inv_id=>$on){
	$store->deleteInventoryItem($inv_id);
	unset($inv[$inv_id]);
}

/*=========== inventory edit ========*/
foreach($inv as $inv_id=>$row){
	if(!$row[sku])
		$row[sku]=$inv_id;
	$store->setInventory($row,$inv_id);
	if($newfiles[$inv_id])
		$store->createInventoryImages($inv_id, $newfiles[$inv_id] );
}
/*=========== inventory creation =====*/
if(count($new)){
	$new[product_id]=$id;
	$inv_id=$store->setInventory($new);
	if(!$new[sku])$store->setInventory(array(sku=>$inv_id),$inv_id);
	$store->createInventoryImages($inv_id, $newimage);
}

/*=========== redirection ===========*/

location( $refer.qsa(array(id=>$id, cat_id=>$cat_id, s=>$s, d=>$d,error=>$msg))  );
?>
