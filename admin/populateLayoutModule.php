<?php
global $_SESSION,$D_R;
include_once("$D_R/lib/_layout_design_lib.php");
// Get all active modules on the site
$stQuery="select distinct(module_id) from layout_module AS lm , layout_placeholder AS lp 
WHERE lm.module_id IN(lp.module_order)";
$arModulesResult = exec_query($stQuery);
$memCache = new memCacheObj();
// Set memcache object for each module.
foreach($arModulesResult as $arModule)
{
	$module_id = $arModule['module_id'];
	$stKey="module_".$module_id;
	$stModuleContent = renderTemplateContent($module_id);
	$memCache->setKey($stKey, $stModuleContent, '300');
}
?>