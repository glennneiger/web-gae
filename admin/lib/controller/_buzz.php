<?php
global $D_R;
$chart_id=$_REQUEST['chartId'];
$buzz_id=$_REQUEST['buzzId'];
include_once($D_R."/admin/lib/_admin_data_lib.php");
$objBuzz= new Buzz();
$objBuzz->removeChart($buzz_id,$chartId);
?>
