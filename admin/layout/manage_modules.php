<?php
global $HTPFX, $HTHOST,$HTADMINHOST, $HTADMINHOSTPREVIW;
$pageAlias= str_replace(".htm","",getPageAlias($_POST['page_id']));
$pageURL = $HTPFX.$HTADMINHOSTPREVIW.$pageAlias."?preview=1";

if(isset($_POST['action']) && $_POST['action'] != 'save')
{
	// call function to add/delete/rearange module in a placeholder
	manageModulesForPlaceholder($_POST['place_holder'],$_POST['module_id'],$_POST['action']);
	$arModuleResult = getModuleList($_POST['place_holder'],'temp');
}
else
{
	if($_POST['action'] == 'save')
	{
		updateModuleOrder($_POST['place_holder'],'actual');
		$objcache= new Cache();
		$sql = "SELECT placeholder from layout_placeholder where id = '".$_POST['place_holder']."'";
		$arPlaceholder = exec_query($sql,1);
		if($arPlaceholder['placeholder'] == "Home Page Featured Module")
		{
			$objcache->setFeaturedSlider();
			$objcache->setFeaturedSliderNoAd();
		}
		else
		{
			$objcache->setModuleListCache($_POST['page_id'],$arPlaceholder['placeholder']);
		}
	}
	else
	{
		updateModuleOrder($_POST['place_holder'],'temp');
	}
	$arModuleResult = getModuleList($_POST['place_holder'],'actual');
}
foreach($arModuleResult as $arModuleRow)
{
	$arModuleId[] = $arModuleRow['module_id'];
	$arModuleName[] = $arModuleRow['unique_name'];
}
$arMoldueListResult = getAllModuleList();
foreach($arMoldueListResult as $arModuleListRow)
{
	if(is_array($arModuleId) && in_array($arModuleListRow['module_id'],$arModuleId) === true)
	{
	}
	else
	{
		$arAvailableModuleId[] = $arModuleListRow['module_id'];
		$arAvailableModuleName[] = $arModuleListRow['unique_name'];
	}
}
?>
<table width="100%" cellpadding="0" cellspacing="1" border="0" class="admin_container">
	<tr >
		<td class="admin_module_head">Module Name</td>
		<td class="admin_module_head">Actions</td>
	</tr>
	<?
	if(is_array($arModuleResult) && count($arModuleResult) > 0)
	{
		foreach($arModuleResult as $arModuleRow)
		{
 	?>
            <tr class="highlight_row">
				<td><?= $arModuleRow['unique_name']?></td>
				<td><a target="_blank" href="<?=$HTPFX.$HTADMINHOST?>/admin/layout/add_edit_module.php?module_id=<?=$arModuleRow['module_id']?>">edit</a>&nbsp;&nbsp;<a href="#" onClick="mangeModules(<?= $_POST['place_holder']?>,<?= $arModuleRow['module_id']?>,'delete');">delete</a>&nbsp;&nbsp;<a href="#" onClick="mangeModules(<?= $_POST['place_holder']?>,<?= $arModuleRow['module_id']?>,'up');">up</a>&nbsp;&nbsp;<a href="#" onClick="mangeModules(<?= $_POST['place_holder']?>,<?= $arModuleRow['module_id']?>,'down');">down</a></td>
			</tr>
	<?
		}
	}
	else
	{
		$arLang = build_admin_lang('manage_pages');
	?>
		<tr><td align="center" class="no_data" colspan="3"><?=$arLang['NO_MODULE_FOR_PLACEHOLDER'] ?></td></tr>
	<?
	}
	?>
	<? if(is_array($arAvailableModuleId)){?>
    <tr class="highlight_row">
	<td colspan="3"><select id="selAvailableModule" >
		<?
		foreach($arAvailableModuleId as $key => $module_id)
		{
		?>
			<option value="<?= $module_id ?>"><?= $arAvailableModuleName[$key] ?></option>
		<?
		}
		?>
		</select>&nbsp;&nbsp;<input type="button" class="submit_button" value="add module" onClick="mangeModules(<?= $_POST['place_holder']?>,$('selAvailableModule').value,'add');" />
	</td>
	</tr>
	<? } ?>
	<tr class="highlight_row"><td colspan="3"><input type="button" class="submit_button" value="preview module" onclick="window.open('<?=$pageURL?>');" /> <input type="button" class="submit_button" value="save module" onClick="mangeModules(<?= $_POST['place_holder']?>,'','save');" /></td></tr>
</table>
