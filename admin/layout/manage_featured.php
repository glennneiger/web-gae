<?php 
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Manage featured</title>
</head>
<body>
<form action="" name="Featured">	
<table width="100%" cellpadding="5" cellspacing="2" border="0">	
<tr>
<td valign="middle" colspan="5" align="left" class='table_row'><a href="manage_featured.php">Featured/Recent in vill Module</a></td>
</tr>
<tr><td width="105">Select Module:</td>
<td width="783">
<select name="moduletype" onchange="mangemenu('placeholder_modules','',this.value,'manage_displayfeatured.php');">
<option>--Select Module--</option>
<option value="Featured">Home Page - Featured Module</option>
<option value="Recent In Vil">Home Page - Recently in Ville</option>
</select>
<td width="49"></td>
</tr>	
<tr>
<td colspan="2" id="placeholder_modules"><td>				
</tr>	
</table>
</form>
<? include("$ADMIN_PATH/_footer.htm"); ?>