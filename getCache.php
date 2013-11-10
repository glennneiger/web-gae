<?php 
include_once($D_R.'/lib/MemCache.php');

echo $key = $_GET['key'];
$objCache= new Cache();
$data = $objCache->getKey($key);
htmlprint_r($data);


?>