<?php 
global $_SESSION,$D_R;
include_once("$D_R/lib/_layout_design_lib.php");
if($_POST['action'] == 'add')
{
	echo renderTemplateContent($_POST['module_id'],$_POST['template_id']);
}
else
{
	echo renderTemplateContent($_POST['module_id']);
}