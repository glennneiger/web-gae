<?php 
if($_POST['action'] == 'add')
{
	echo renderTemplateContent($_POST['module_id'],$_POST['template_id']);
}
else
{
	echo renderTemplateContent($_POST['module_id']);
}