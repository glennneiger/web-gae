<?php
global $D_R;
include_once($D_R."/lib/_includes.php");
session_start();

$user_id = $_SESSION['SID'];
$id = del_query('new_registeration','user_id',$user_id);	
?>