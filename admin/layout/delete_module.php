<?php 
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Delete Layout Tool</title>
</head>
<body>
<form action="" name="module">	
<table width="100%" cellpadding="5" cellspacing="0" border="0"  align="center">	
<tr >
<td valign="middle" colspan="3" align="left" class="admin_module_head">Delete Module</td>
<td valign="middle" colspan="3" align="right" class="admin_module_head"><a href='add_edit_module.php' style=" cursor: pointer; text-decoration:none">Create Module</a></td>
</tr>
<tr>
<td colspan="6" id="placeholder_modules"><? include("manage_delete_module.php") ?><td width="1%">				
</tr>	
</table>
</form>
<? include("$ADMIN_PATH/_footer.htm"); ?>
