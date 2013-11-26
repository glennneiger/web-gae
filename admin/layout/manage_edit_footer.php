<?php
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
global $D_R;
include_once("$D_R/lib/_layout_data_lib.php");
include_once("$D_R/lib/_layout_design_lib.php");
global $is_ssl;
$pageno=$_GET[p];
if($pageno==""){
$pageno=1;
}else{
$pageno=$pageno+1;
}
$Page = 'manage_footer_link';
################# Request id ##############
$pid = is_numeric($_REQUEST['id']) ?  $_REQUEST['id'] : '0';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language ="javascript">
function validate()
{

        if(trim(document.submenu.txttitle.value)=='')
		{
		     alert('Please Enter Title.');
			document.submenu.txttitle.focus();
			return false;
		}
		if(trim(document.submenu.txtpageid.value)=='')
		{
		     alert('Please Enter Page Url.');
			document.submenu.txtpageid.focus();
			return false;
		}
		if(trim(document.submenu.txtpageid.value)=='')
		{
		     alert('Please Enter Page Url.');
			document.submenu.txtpageid.focus();
			return false;
		}
		 return true;
}
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}

function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
</script>
<title>Edit Footer</title>
</head>
<body>
<? if(isset($_GET['msg']) and $_GET['msg']=='ero') {
$message =  build_admin_lang($page);

echo '<p align="center"> <font color="#FF0000">'.$message['EDIT_MODULE'].'</font></p>';
 }
 ?>
<table width="100%" cellpadding="0" cellspacing="10" border="0" class="admin_container">
<tr><td>
<?= editfooter($pid);?>
</td></tr>
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
