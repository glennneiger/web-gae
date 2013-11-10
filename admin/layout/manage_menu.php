<?php
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
global $is_ssl;
$pageno=$_GET[p];
if($pageno==""){
$pageno=1;
}else{
$pageno=$pageno+1;
}
$page = 'manage_menu';
if($_REQUEST['mode']=='edit' and $_POST['id']!='')
{
   $post_title  = addslashes(ucwords($_POST['title']));
    $post_id    = $_POST['id'];
    $sql     =   "select  title from layout_menu where title='$post_title'";
   @$result  =  exec_query($sql);
  $numcount = count($result);
  // for else if  condition
   $sql1     =   "select  title from layout_menu where title='$post_title' and id='$post_id'";
   @$result1  =  exec_query($sql1);
  $numcount1 = count($result1);
  if($numcount=='0')
  {

  $update      = updatemainmenu($post_id,$post_title);
  print"<script language='javascript'>window.location='manage_menu.php';</script>";
  }
  elseif($numcount1 =='1')
  {
  $update      = updatemainmenu($post_id,$post_title);
  print"<script language='javascript'>window.location='manage_menu.php';</script>";
  }
  else
  {
      $message =  build_admin_lang($page);

     echo  '<p align="center" > <font color="#FF0000">'.$message['EDIT_MODULE'].'</font></p>' ;
  }

}
elseif($_REQUEST['mode']!='' and $_GET['id']!='')
{
 $action = mainmanuactionperform($_GET['id'],$_REQUEST['mode'],$_REQUEST['pid']);
}
$arPageResult = getlayoutmenu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Manage Section Categories</title>
<script language ="javascript">
function validate()
{

        if(trim(document.menu.title.value)=='')
		{
		     alert('Please Enter Title.');
			document.menu.title.focus();
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
</head>
<body>
<form action="manage_menu.php" method="post" name="menu"  onSubmit="return validate();">
<table width="100%" cellpadding="2" cellspacing="2" border="0" class="admin_container" >
<tr>
<td valign="middle" colspan="1" align="left" class="admin_module_head"><a href="manage_menu.php">Menu Category </a></td>
<td valign="middle" align="right" class="admin_module_head" colspan="2"><a href="manage_footermenu.php">Quick Links</a>
 </td>

  </tr>
<tr  >
	<td width="31%" valign="middle" class="admin_module_head">Title</td>
	<td width="26%" valign="middle" class="admin_module_head">No Of Sub Menu Item(s)</td>
	<td width="16%" valign="middle" class="admin_module_head">Action </td>
  </tr>
  <tr >
    <td ><?
   if(sizeof($arPageResult)>0)
   {
     foreach($arPageResult as $arPageRow)
    {
	  $contsubtitle = getnamesubmenucount($arPageRow ['id']);
	  $subtitle = getnamesubmenu($arPageRow ['id']);
	  $subtitle1 ='';
	 for ($i=0; $i<count($subtitle);$i++ )
	 {
	   if($i==(count($subtitle)-1))
	  {
	    $subtitle1 .= $subtitle[$i]['title'];
	  }
	  else
	  {
	     $subtitle1 .= $subtitle[$i]['title'].',';
	  }

	 }

   ?>
   <tr  class="highlight_row" >
   <? if($_GET['mode']=='edit' and $_GET['id']==$arPageRow ['id']){?>
   <td valign="middle"><input  type="text" name='title' value="<?= stripslashes($arPageRow ['title']) ?>" id="title" size="50"/></td>
   <input type="hidden" value="<?=$arPageRow ['id'] ?>" id="id" name="id" />
   <input type="hidden" value="edit" id="mode" name="mode" />
   <? } else{?>
   <td valign="middle"><?=$arPageRow ['title'] ?></td>
   <? }?>
<td valign="middle"><font color="#000099"><?= count($contsubtitle) ?></font>&nbsp;&nbsp;&nbsp;<a href="manage_submenu.php?id=<?=$arPageRow ['id'] ?>">[<?=stripslashes($subtitle1) ?>]</a></td>
	<? if($_GET['mode']=='edit' and $_GET['id']==$arPageRow ['id']){?>
	<td width="12%" valign="middle"><input type="submit" name="update"  value="update" class="submit_button"/>&nbsp;&nbsp;<a href="manage_menu.php?id=<?=$arPageRow ['id'] ?>&mode=up&pid=0" >up</a>&nbsp;&nbsp;<a href="manage_menu.php?id=<?=$arPageRow ['id'] ?>&mode=down&pid=0">down</a></td>
	<? } else{?>
	<td width="15%" valign="middle"><a href="manage_menu.php?id=<?=$arPageRow ['id'] ?>&mode=edit">edit</a>&nbsp;&nbsp;<a href="manage_menu.php?id=<?=$arPageRow ['id'] ?>&mode=up&pid=0" >up</a>&nbsp;&nbsp;</a>&nbsp;&nbsp;<a href="manage_menu.php?id=<?=$arPageRow ['id'] ?>&mode=down&pid=0">down</a></td>
	<? }?>
  </tr>
  <? }}?></td>
  </tr>
</table>
</form>
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
