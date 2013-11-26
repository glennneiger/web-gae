<?php
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
global $D_R;
include_once("$D_R/lib/_layout_design_lib.php");
global $is_ssl;
$pageno=$_GET[p];
if($pageno==""){
$pageno=1;
}else{
$pageno=$pageno+1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Manage footer</title>
</head>
<body>
<table width="100%" cellpadding="5" cellspacing="2" border="0" class="admin_container">
<tr>
<td valign="middle" colspan="2" align="left" class="admin_module_head"><a href="manage_footermenu.php">Quick Links
</a></td><td valign="middle"  align="left" class="admin_module_head"><a href="manage_menu.php">Menu Category
</a></td>
</tr>
<tr >
	<td class="admin_module_head">Title</td>
	<td class="admin_module_head">Published </td>
	<td class="admin_module_head">Action </td>
  </tr>
  <tr><td id="placeholder_modules"><?=displayfooter() ?></td></tr>
</table>
<? include("$ADMIN_PATH/_footer.htm"); ?>
<?
/*
if($pageName=="article_template"){
$tracking_name = substr($article['date'],0,12)."-".$article['title'].'_Page_'.$pageno;
}else{
$tracking_name = "";
}
if($domain=='ameritrade'){
$profileid=1;
googleanalytics($profileid,$tracking_name);
}
if($exchangepage=='true')
{
$profileid=2;
googleanalytics($profileid,$tracking_name);
}
$profileid=1;
googleanalytics($profileid,$tracking_name);
*/
?>
</body>
</html>
