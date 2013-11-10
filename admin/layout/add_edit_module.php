<?php
// Only save final module if form is not submited from FCK editor ibox

if(isset($_POST['hidModuleId']) && !isset($_POST['hidFCKContent']))
{
	$arLang = build_admin_lang('manage_module');
	$stActionStatus = saveModule($_POST['hidModuleAction'],$_POST['hidModuleId'],$_POST['txtModuleName'],$_POST['selTemplate']); // function defined in _layout_design_lib
	if(is_int($stActionStatus))
	{
		if($_POST['hidModuleAction'] == 'add')
		{
			$msg = 'ADD_MODULE';
		}
		else
		{
			$msg = 'EDIT_MODULE';
		}
	}
	else
	{
		$stMessage = $arLang[$stActionStatus];
	}
	if(is_int($stActionStatus)) // Return Module id on Sucess
	{
		header("Location:add_edit_module.php?module_id=".$stActionStatus."&msg=".$msg);
	}
}
if(isset($_GET['msg']))
{
	$arLang = build_admin_lang('manage_module');
	$stMessage = $arLang[$_GET['msg']];
}
if(!$_GET['module_id'])  // Add a Module
{
	$action = "add";
	if(isset($_POST['hidModuleId']))
	{
		$module_id = $_POST['hidModuleId'];
		$module_template_id = $_POST['selTemplate'];
	}
	else
	{
		$module_id = getMaxTempModuleKey();
	}
	$select_template ="";
	$module_name =  $_POST['txtModuleName'];

}
else  // Edit a Module
{
	$action = "edit";
	$module_id = $_GET['module_id'];
	$select_template = "disabled";
	$arModuleDetail = getModuleDetail($module_id);
	$module_name =$arModuleDetail['unique_name'];
	$module_template_id = $arModuleDetail['template_id'];
	// Insert Record into Temp Table
	setModuleForEdit($module_id);
}
$arComponentResult = getTemplateComponentList();
foreach($arComponentResult as $arComponentRow)
{
	$arComponentId[] = "<a style='font-weight:normal;font-size:10px;cursor:pointer;' onclick=\"openAddEditComponent('add','".$arComponentRow['id']."','".$module_id."');\">Add/Edit Component</a>";
	$arComponentName[] = "[".$arComponentRow['component_name']."]";
}
$arTemplateResult = getTemplateList();
foreach($arTemplateResult as $arTemplateRow)
{
	$arTemplateId[] = $arTemplateRow['template_id'];
	$arTemplateName[] = $arTemplateRow['template_name'];
	$arTemplateDesign[] = str_replace($arComponentName,$arComponentId,$arTemplateRow['admin_design']);
}
?>
<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/admin/ckeditor/ckeditor.js"></script>
<?
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");
?>
	<form name="frmModule" method="post" action="" onsubmit="javascript:return saveModule();" style="padding:0px;
    margin:0px;">
	<input type="hidden" name="hidModuleId" value="<?= $module_id?>" />
	<input type="hidden" name="hidModuleAction" value="<?= $action?>" />
	<table border="0" cellpadding="0" cellspacing="1" width="100%" align="left" class="admin_container">
    <tr><td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="admin_module_head" >Create Module</td><td class="admin_module_head" align="right"><a href='manage_pages.php' style=" cursor: pointer; text-decoration:none">Manage Pages</a>&nbsp;&nbsp;<a href='delete_module.php' style=" cursor: pointer; text-decoration:none">Delete Module(s)</a></td></tr>
	</table>
    <tr><td colspan="2"><font color="#FF0000"><?= $stMessage?></font></td></tr>
	<tr class="highlight_row"><td width="20%">Module Name:</td><td> <input type="text" class="admin_input" id="txtModuleName" name="txtModuleName" value="<?= $module_name?>"></td></tr>
    <tr ><td width="20%" colspan="2"></td></tr>
	<tr class="highlight_row"><td>Select Template:</td>
	<td>
	<? if($action == "edit"){?>
	<input type="hidden" value="<?=$module_template_id?>" name="selTemplate" >
	<? }?>
	<select class="admin_select" name="selTemplate" id="selTemplate" <?= $select_template?> onChange="loadTemplate(this.value);">
	<option value="">-select template-</option>
	<?
	foreach($arTemplateId as $key => $template_id)
	{
		if(isset($module_template_id) && $module_template_id == $template_id)
		{
			$template_selected = "selected";
		}
		else
		{
			$template_selected = "";
		}
	?>
		<option value="<?= $template_id ?>" <?= $template_selected ?>><?= $arTemplateName[$key] ?></option>
	<?
	}
	?>
	</select></td></tr>
	<tr><td valign="top" colspan="2">
	<table id="selected_template" border="0" cellpadding="0" cellspacing="0">
	<?
	foreach($arTemplateId as $key => $template_id)
	{
		if(isset($module_template_id) && $module_template_id == $template_id)
		{
			$show_template = "display:;";
		}
		else
		{
			$show_template = "display:none;";
		}
	?>
		<tr id="template<?= $template_id ?>" style=" <?= $show_template ?>"><td><?= $arTemplateDesign[$key] ?></td></tr>
	<?
	}
	?>
	</table>
	</td>
	</tr>
    <?
    if($module_template_id=="19"){
	 ?>
	   <tr>
	   <td class="adUnitSlide" align="left" valign="top" colspan="2">
       <span>For preview of Ad Unit please refresh page.</span>
	   <? echo displayAdUnitSlide(); ?>
       </td>
       </tr>
	<?
	}
	?>
    <tr>
		<td id="final_content" align="left" valign="top" colspan="2"></td>
    </tr>
	<tr><td colspan="2"><input type="submit" class="submit_button" name="sub" value="Save" /></td></tr>
	</table>
	</form>
<? include("$ADMIN_PATH/_footer.htm");
// Special Condition for FCK editor as its not possible to get FCK editor using Javascript
if(isset($_POST['hidFCKContent']))
{
	$content = str_replace("\r\n","",$_POST['txtFCKContent']);
	$content = str_replace("\n","",$content);
	$pars = "fckcontent=".addslashes($content);
	$action = $_POST['hidFCKAction'];
	$component_id = $_POST['hidFCKComponentId'];?>
	<script type="text/javascript">saveModuleComponent('<?=$action?>','<?=$component_id?>','<?=$module_id?>','<?=$pars?>');</script>
<?
}
if($_GET['module_id'])
{
?>
<script type="text/javascript">
previewModule("add",<?=$module_id?>);
</script>
<?
}
else if($_POST['hidModuleId'])
{
?>
<script type="text/javascript">
previewModule("add",<?=$_POST['hidModuleId']?>);
</script>
<?
}
?>