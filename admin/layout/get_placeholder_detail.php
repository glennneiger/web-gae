<?php 
global $D_R;
include_once("$D_R/lib/_layout_data_lib.php");
header('Content-Type: text/xml');  // for xml response
$arPlaceHolderResult= getPagePlaceHolderList($_POST['page_id']);
if($arPlaceHolderResult)
{	
	$stXML .="<?xml version='1.0' ?>";
	$stXML .='<placeholderdata>';
	foreach($arPlaceHolderResult as $arPlaceHolderRow)
	{
		$stXML .='<placeholder>';
		$stXML .= '<placeholderid>'.$arPlaceHolderRow[id].'-'.$arPlaceHolderRow[placeholder].'</placeholderid>';
		$stXML .= '<placeholdername><![CDATA['.$arPlaceHolderRow[placeholder].']]></placeholdername>';
		$stXML .='</placeholder>';					
		/*if($arPlaceHolderRow[module_order] != "")
		{
			$arModuleResult= getModuleList($arPlaceHolderRow[module_order]);
			foreach($arModuleResult as $arModuleRow)
			{
				$stXML .='<module'.$arPlaceHolderRow[id].'>';
				$stXML .= '<moduleid>'.$arModuleRow[module_id].'</moduleid>';
				$stXML .= '<modulename><![CDATA['.$arModuleRow[unique_name].']]></modulename>';
				$stXML .='</module'.$arPlaceHolderRow[id].'>';
			}
		}*/		
	}	
	$stXML .='</placeholderdata>';						
}
echo $stXML;
exit();
?>
