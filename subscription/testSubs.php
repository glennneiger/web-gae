<?php


include_once("$D_R/_minyanville_header.htm");
include_once("$D_R/lib/_via_data_lib.php");

htmlprint_r($_SESSION);
$viaId = $_SESSION['viaid'];
$viaObj = new Via();
$response = $viaObj->customerSubInfo($viaId);
htmlprint_r($response);
htmlprint_r($_SESSION);

//htmlprint_r($_SESSION['products']);
?>