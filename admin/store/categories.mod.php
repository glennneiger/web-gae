<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");


$store=new Store();

$cat = $_POST['cat'];
$cat_id = $_POST['cat_id'];
$delcat = $_POST['delcat'];
$refer = $_POST['refer'];
$store->admin=1;
$image=$_FILES[big_image];


if(!$cat_id){
	if(count_rows($store->cTable,array(title=>$cat[title])) ){
		$msg="That category already exists";
		location("$refer?id=$id&msg=$msg");
		exit;
	}
}

/*========== file deletion ============*/
if($del=@key($del)){
	$store->deleteCategoryImages($del);
	$msg="The image was remvoed<br>";;
}

if(!$delcat){
	$cat_id=$store->setCategory($cat,$cat_id);
	if($cat_id && $image)	
		$store->createCategoryImages($cat_id, $image );
	$msg="The cateogry was updated<br>";
}else{
	$store->deleteCategory($cat_id);
	unset($cat_id);
	$msg="The category was removed";
}
location("$refer?cat_id=$cat_id&error=$msg");

?>