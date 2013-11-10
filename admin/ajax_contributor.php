<?php
$cont_id = $_POST['cont_id'];
include_once($D_R."/admin/lib/_contributor_class.php");
$json = new Services_JSON();
$obCont = new contributor();
$arCont = $obCont->getContributor($cont_id);
$output = $json->encode($arCont);
echo $output;
?>