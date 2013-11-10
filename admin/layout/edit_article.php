<?
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
include("$D_R/lib/_layout_data_lib.php");
$id = $_GET['id'];
$Branded_Article = get_Branded_Article($id);
?>
<table width="100%"  cellpadding="0" cellspacing="0" border="0">	
<tr class="admin_module_head">
<td valign="middle"  align="left" class="admin_module_head">List Article</td>
<td valign="middle"  align="left" class="admin_module_head" colspan="5">&nbsp;</td>
<td valign="middle"  align="right"  class="admin_module_head"><a href="<?=$HTPFX.$HTHOST?>/admin/layout/listbrandedlogo.htm" 
style="text-decoration:none">List Branded Logo</a>
</td>
</tr>
<tr>
<td colspan="7">
<table width="100%" border="0" cellpadding="2" cellspacing="2" class="admin_container">
<tr>
   <td valign="middle" align="center" colspan="3"><font color="#FF0000">Remove Branded Logo From Articles</font></td>
  </tr>
  <tr>
   <td width="15%" valign="middle" class="admin_module_head" align="left">S.NO</td>
    <td width="58%" valign="middle" class="admin_module_head" align="left">Article</td>
     <td width="27%" valign="middle" class="admin_module_head" align="left">Action</td>
  </tr>
   <? if(count($Branded_Article)>0)
  {
  $i=1;
   foreach($Branded_Article as $module)
   {
  ?>
  <tr class="highlight_row" >
  <td width="15%" valign="middle" align="left"><?=$i ?></td>
    <td width="58%" valign="middle" ><?= trim(htmlentities($module['title'])) ?></td>
     <td width="27%" valign="middle">
<a  href="<?=$HTPFX.$HTHOST?>/admin/articles.htm?contributor_id=<?=$module['contrib_id'] ?>&id=<?=$module['id'] ?>">edit</a></td>

  </tr>
  <? $i++;
   }
   } 
  ?>
  </table>
<td>				
</tr>	
</table>
<? include("$ADMIN_PATH/_footer.htm"); ?>	