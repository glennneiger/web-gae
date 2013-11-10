<?php
session_start();
global $D_R;
include_once($D_R.'/lib/MemCache.php');
$objCache = new Cache();
$objMemCache = new memCacheObj();
if(!empty($_POST)){
	$pageid = $_POST['pageid'];
	$module = $_POST['module'];
	$objMemCache->deleteKey('module_442');
	echo $objCache->getPageModules($pageid,$module);
}
?>