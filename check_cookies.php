<?php
session_start();
setcookie("CHECK", "OK");
$pageJS=array("config","registration","iboxregistration","creditcard","nav");
$pageCSS=array("global","layout","section","rightColumn","nav");
include("_header.htm");
?>

<div class="shadow">
<div id="content-container">
	<div style="text-align:center;">
		<h1 style="padding-left:5px;">
		<?
		if($_COOKIE['CHECK']=="OK")
		{
			echo '<span style=""> Your cokies are enabled </span>';
		}
		else
		{
			echo '<span style=""> Your cokies are disabled </span>';
		}
		?>
		</h1>
	</div>
</div>
<? include("_footer.htm"); ?>
