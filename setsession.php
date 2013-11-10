<?php
session_start();
$localSessionId=session_id();
$_SESSION['localsession']=$localSessionId;
if(isset($_SESSION['localsession']) && $_SESSION['localsession']==$localSessionId){
	echo '1';
}
else{
	echo '0';
}
?>