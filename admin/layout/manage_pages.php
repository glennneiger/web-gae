<?php 
$arPageResult = getLayoutEditablePages();
global $is_ssl;
$pageno=$_GET[p];
if($pageno==""){
$pageno=1;
}else{
$pageno=$pageno+1;
}
foreach($arPageResult as $arPageRow)
{
	$arPageId[] = $arPageRow['id'];
	$arPageName[] = $arPageRow['name'];
}
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
?>			
<table width="100%" cellpadding="0" cellspacing="1" border="0" class="admin_container">
<tr><td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="admin_module_head">Manage Pages</td>
  <td align="right" class="admin_module_head"><a href='add_edit_module.php'>Create Module</a></td></tr>
 
</table>
</td></tr>	
<tr><td align="right" colspan="2" valign="top"></td></tr>
<tr><td colspan="2"><?=adminMenuItems()?></td></tr>
<tr class="highlight_row"><td width="150">Select Page:</td>
<td> <select id="selPage" onChange="getPlaceholderList(this.value);" class="admin_select">
	<option value="">-select page-</option>
	<?
	foreach($arPageId as $key => $page_id)
	{
	?>
		<option value="<?= $page_id ?>"><?= $arPageName[$key] ?></option>
	<?
	}
	?>
	</select>
</td></tr>	
<td colspan="2"></td>
<tr class="highlight_row"><td>Select Placeholder:</td>
<td><select id="selPlaceholder" onChange="mangeModules(this.value);" class="admin_select">
<option value="">-select placeholder-</option>	
</select>
</td>
</tr>	
<tr>
<td colspan="2" id="placeholder_modules"><td>				
</tr>	
</table>
<? include("$ADMIN_PATH/_footer.htm"); ?>