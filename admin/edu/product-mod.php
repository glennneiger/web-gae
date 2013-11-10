<?php
include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_edu_config.php");
include_once($D_R."/lib/_image_rsync.php");

global $D_R, $eduItemMeta;

$bounceback="./edu-product.htm";

if(!count($_POST)){
	location($bounceback.qsa(array(error=>"No changes were made",id=>$id)));
	exit;
}

$product = $_POST['product'];

if(!empty($_POST)){
	if(!empty($_FILES['eduproductImg']['name'])){
		$eduImgPath="/assets/edu/images/";
		$eduproductImg = rand().'-'.str_replace(" ","_",$_FILES['eduproductImg']['name']);
		move_uploaded_file($_FILES["eduproductImg"]["tmp_name"], "gs://mvassets".$eduImgPath.$eduproductImg);
	}
	if($_POST['actionType']=='save'){
		if($_POST['id']==''){
			if(!empty($eduproductImg)){
				$product['image'] = $eduproductImg;
			}
			$id = insert_query("edu_product",$product);
			location($bounceback.qsa(array(error=>"Product has been Added",id=>$id)));
			exit;
		}else{
			if(!empty($eduproductImg)){
				$product['image'] = $eduproductImg;
			}
			update_query( "edu_product", $product, array(id=>$_POST['id']) );
			location($bounceback.qsa(array(error=>"Your changes were saved",id=>$id)));
			exit;
		}
	}elseif($_POST['actionType']=='delete'){
		del_query("edu_product","id",$_POST['id']);
		location($bounceback.qsa(array(error=>"Product details has been removed",id=>$_POST['id'])));
		exit;
	}
}


?>
