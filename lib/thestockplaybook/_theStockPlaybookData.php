<?
class theStockPlaybook{

	function getTheStockPlaybookProductType($google_analytics_action=NULL)
	{
		$theStockPlaybookSubscriptionDefIds = getprodsubdefid('TheStockPlayBook',''); // Defined in lib/_module_design_lib
		$arProductResult= getProdSubPriceVal($theStockPlaybookSubscriptionDefIds,'SUBSCRIPTION'); // Defined in lib/_module_data_lib
		$arProductDetail = array();
		foreach ($arProductResult as $arProductRow)
		{
			if(strstr($arProductRow['product'], 'Monthly'))
			{
				$product_type ='Monthly';
			}
			elseif(strstr($arProductRow['product'], 'Annual'))
			{
				$product_type ='Annual';
			}
			elseif(strstr($arProductRow['product'], 'Quarterly'))
			{
				$product_type ='Quarterly';
			}
			$arProductRow['price_format']='$'.intval($arProductRow['price']);
			$arProductRow['product_type'] = $product_type;
			$arProductRow['google_analytics_product_name'] = 'subTSP';
			$arProductRow['google_analytics_action'] = $google_analytics_action;
			$arProductDetail[$product_type] = $arProductRow;
		}
		return $arProductDetail;
	}

}
?>