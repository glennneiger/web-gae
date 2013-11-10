<?
function layoutmenuitems($pageid = NULL,$getHeaderMenu){
   //$menu = getLayoutMenuData();
   $menu = $getHeaderMenu->menu;
   global $arMenuDetail,$HTPFX,$HTHOST;
   $arMenuDetail =  array();
   if(count($menu)>0)
   {
		foreach($menu as $menuRow)
		{
			$arMenuDetail;

			if(substr($menuRow['alias'],0,1) == '/')
			{
				$stURLPrefix = $HTPFX.$HTHOST;
			}
			else
			{
				$stURLPrefix = "http://";
			}
			if($menuRow['parent_id'] == 0)
			{
				$arMenuDetail[$menuRow['id']]['title'] = $menuRow['title'];
				$arMenuDetail[$menuRow['id']]['url'] = $stURLPrefix.$menuRow['alias'];
				$arMenuDetail[$menuRow['id']]['page_id'] = $menuRow['page_id'];
				if($menuRow['page_id'] == $pageid) // If selected page id is menu's page id
				{
					$arMenuDetail[$menuRow['id']]['selected'] = 1;
				}
			}
			else
			{
				$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['title'] = $menuRow['title'];
				$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['url'] = $stURLPrefix.$menuRow['alias'];
				$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['page_id'] = $menuRow['page_id'];
				if($menuRow['page_id'] == $pageid) // If selected page id is sub menu's page id
				{
					// Parent Menu of selected submenu should also be selected
					$arMenuDetail[$menuRow['parent_id']]['selected'] = 1;
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['selected'] = 1;
				}
			}
		}
		//echo "<pre>";
		//print_r($arMenuDetail);
		$div = layoutmenuhtml($arMenuDetail);
	}else{
	 	 $div = 'Sorry no menu items found';
	}
	return $div ;
}

function layoutmenuhtml($menuitems ){
	global $HTPFX,$HTHOST;
	$div = '';
	$display = "none";
	$subdiv  = '';
	if(count($menuitems )>0)
	{
		$maindiv  = '<div class="main_navigation"><div id="ddtabs3" class="solidblockmenu">';
		foreach ($menuitems as $id=>$item )
		{
			if(stristr($item['url'],"http")===FALSE)
				$item['url']=$HTPFX.$HTHOST.$item['url'];
			$maindiv .= '<div id="tab_'.$id.'"';
			if($item['selected']==1)
			{
				$maindiv .= 'class="tab_'.$id.'_selected">';
				$display = "block";
			}else{
				$maindiv .= 'class="tab_'.$id.'">';
				$display = "none";
			}
			/** Do not Erase --- $item['0']  is number of subitems in the main menu item **/
			$maindiv .= '<a target="_parent" href="'.$item['url'].'" rel="sb'.$id.'"  onmouseover="showsubmenu ('.$id.','.count($menuitems).');  ">'.$item['title'].'</a>';
			$maindiv .='</div>';
			$subdiv .= showsubmenuitems($item,$id, $display );
		}
		$maindiv .= '</div>';
		 $div = $maindiv.$subdiv."</div>";
	}	else{
	   echo $menuitems; //MVIL Error handler will be included
	   exit;

	}
	return $div;
 }

function showsubmenuitems($item,$id, $display )
{
	global $HTPFX,$HTHOST;

	foreach ($item as $var=>$val)
	{
		  $subdiv_start ='';$subdivs ='';$subdiv_end='';
		  if(is_array($val))
		  {
			$subdiv_start .= '<div  class="sub_tab_'.$id.'"  id= "sub_tab_'.$id.'" style="display:'.$display.'">';
			$count = 1;
			foreach($val as $subitems=>$subdata)
			{
				if(stristr($subdata['url'],"http")===FALSE)
				$subdata['url']=$HTPFX.$HTHOST.$subdata['url'];
				//	$subdata['url']=$subdata['url'];


				$itemid = $id.'_'.$count;
				$subdivs  .= '<div  id= "sub_tab_item_'.$itemid.'" ';
				if($subdata['selected']==1){
					$subdivs  .= 'class="sub_nav_tab_'.$id.'_selected">';
				}else{
					$subdivs  .= 'class="sub_nav_tab_'.$id.'">';
				}
					$subdivs  .= '<a target="_parent" href="'.$subdata['url'].'" onmouseover="showsubmenuitem('.$id.', '.$count.' ,'.count($val).');">'.$subdata['title'].'</a></div>';
				$count++;
			}

			$subdiv_end .='</div>';
		  }
		  $subdiv .= $subdiv_start.$subdivs.$subdiv_end;
	}
	return $subdiv;
}
function adminMenuItems(){
   $menu = getLayoutMenuData();
   global $arMenuDetail;
   $arMenuDetail =  array();
   if(count($menu)>0)
   {
		foreach($menu as $menuRow)
		{
			$arMenuDetail;
			if($menuRow['parent_id'] == 0)
			{
				$arMenuDetail[$menuRow['id']]['title'] = $menuRow['title'];
				$arMenuDetail[$menuRow['id']]['page_id'] = $menuRow['page_id'];
			}
			else
			{
				$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['title'] = $menuRow['title'];
				$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['page_id'] = $menuRow['page_id'];

			}
		}
		$div = adminMenuHtml($arMenuDetail);
	}else{
	 	 $div = 'Sorry no menu items found';
	}
	return $div ;
}
function adminMenuHtml($menuitems )
{
	$div = '';
	$display = "none";
	$subdiv  = '';
	if(count($menuitems )>0)
	{
		$maindiv  = '<div class="admin_main_navigation"><div class="admin_blockmenu">';
		foreach ($menuitems as $id=>$item )
		{
			$maindiv .= '<div id="tab_'.$id.'" class="admin_menu_tab">';
			/** Do not Erase --- $item['0']  is number of subitems in the main menu item **/
			$maindiv .= '<a target="_parent" rel="sb'.$id.'"  onClick="showAdminSubmenu ('.$id.','.count($menuitems).','.$item['page_id'].');  ">'.$item['title'].'</a>';
			$maindiv .='</div>';
			$subdiv .= adminSubmenuItems($item,$id);
		}
		$maindiv .= '</div>';
		 $div = $maindiv.$subdiv."</div>";
	}	else{
	   echo $menuitems; //MVIL Error handler will be included
	   exit;

	}
	return $div;
 }
function adminSubmenuItems($item,$id)
{
	foreach ($item as $var=>$val)
	{
		  $subdiv_start ='';$subdivs ='';$subdiv_end='';
		  if(is_array($val))
		  {
			$subdiv_start .= '<div class="admin_sub_menu"  id= "sub_tab_'.$id.'" style="display:none">';
			$count = 1;
			foreach($val as $subitems=>$subdata)
			{
				$itemid = $id.'_'.$count;
				$subdivs  .= '<div id= "sub_tab_item_'.$itemid.'" class="admin_menu_tab"> ';
				$subdivs  .= '<a target="_parent" onClick="showAdminSubmenuItem('.$id.', '.$count.' ,'.count($val).','.$subdata['page_id'].');">'.$subdata['title'].'</a></div>';
				$count++;
			}
			$subdiv_end .='</div>';
		  }
		  $subdiv .= $subdiv_start.$subdivs.$subdiv_end;
	}
	return $subdiv;
}


/*	Function display each product price for Subs & Prods,
	@ param - pname is the pre defined product name
*/	function getProdSubPrice($pName, $orderItemType ){
		$subdefids = getprodsubdefid($pName,true);
		$price= getProdSubPriceVal($subdefids, $orderItemType );
		$show = true;
		foreach ($price as $data){
			if(intval($data['price'])>0){
				if(strstr($data['product'], 'Monthly') ){$str_mon ='<td align="center" valign="top">$'.intval($data['price']).'</td>';}
				elseif(strstr($data['product'], 'Annual')){$str_ann ='<td align="center" valign="top">$'.intval($data['price']).'</td>';}
				elseif(count($price)==1 && $data['orderItemType'] =='PRODUCT'){$str_mon ='<td align="center" valign="top">$'.intval($data['price']).'</td>';}
				elseif(count($price)==2 && $show){
					$priceVal = intval($price['0']['price'] )+intval($price['1']['price']);
					$priceDisplay = '<h1>$'.intval($price['1']['price']).' + $'.intval($price['0']['price']).' =</h1>';
					$str_mon ='<td align="center"  valign="top"  >'.$priceDisplay.'</td>';
					$str_mon .='<td align="left" width="80" valign="top">&nbsp;$'.$priceVal.'</td>';
					$show = false;}
			}
		 }

		 $str = $str_mon.$str_ann;
		 return $str;
	}



	/*Function returns table product.subscription_Defined_id for given product monthly & annula subs
	@ param - pname is the pre defined product name
	@param flag true -return sting of ids , false return array of data
	*/
	function getprodsubdefid($pName,$flag){

		global $viaProducts;
		$str ="";
		$pName = strtolower(stripslashes($pName));
		switch ($pName){
			case 'buzzbanter':
			case 'buzz & banter annual':
			case 'buzz & banter monthly':
			case 'buzz & banter quarterly':
			 	$str = $viaProducts['BuzzMonthly']['typeSpecificId'].",".$viaProducts['BuzzQuarterly']['typeSpecificId'].",".$viaProducts['BuzzAnnual']['typeSpecificId'].",".$viaProducts['BuzzFT3M-ST']['typeSpecificId'];
				break;
			case 'jeff cooper':
			case 'jeff cooper daily market report':
			case 'jeff cooper daily market report (annual)':
			case 'jeff cooper daily market report (monthly)':
			case 'jeff cooper daily market report (quarterly)':
				$str = $viaProducts['CooperMonthly']['typeSpecificId'].','.$viaProducts['CooperQuarterly']['typeSpecificId'].','.$viaProducts['CooperAnnual']['typeSpecificId'];
				break;
			case 'flexfolio':
			case 'flexfolio monthly':
			case 'flexfolio annual':
			case 'flex folio':
				$str = $viaProducts['FlexfolioMonthly']['typeSpecificId'].','.$viaProducts['FlexfolioAnnual']['typeSpecificId'];
				break;
			case 'optionsmith':
			case 'optionsmith monthly':
			case 'optionsmith annual':
			case 'option smith':
				$str = $viaProducts['OptionsmithMonthly']['typeSpecificId'].','.$viaProducts['OptionsmithAnnual']['typeSpecificId'];
				break;
			case 'bull market timer':
				$str = $viaProducts['BMTP']['typeSpecificId'];
				break;
			case 'bmtppkg':
				$str = $viaProducts['BMTP']['typeSpecificId'].','.$viaProducts['BMTPAlert']['typeSpecificId'];
				break;
			case 'bull market timer update':
				$str = $viaProducts['BMTPAlert']['typeSpecificId'];
				break;
			case "jack":
			case "lavery insight - annual":
			case "lavery insight - monthly":
			case "lavery insight - (monthly)":
			case "lavery insight - (annual)":
				$str = $viaProducts['JackMonthly']['typeSpecificId'].','.$viaProducts['JackAnnual']['typeSpecificId'];
				break;
			case "etf":
			case "grail etf & equity investor annual":
			case "grail etf & equity investor monthly":
			case "grail etf & equity investor 3 months":
			case "grail etf & equity investor (monthly)":
			case "grail etf & equity investor (annual)":
			case "grail etf & equity investor (3 months)":
				$str = $viaProducts['ETFMonthly']['typeSpecificId'].','.$viaProducts['ETFQuart']['typeSpecificId'].','.$viaProducts['ETFAnnual']['typeSpecificId'];
				break;
			case "buyhedge":
			case "buy and hedge annual":
			case "buy and hedge monthly":
			case "buy and hedge 3 months":
			case "buy and hedge (monthly)":
			case "buy and hedge (annual)":
			case "buy and hedge (3 months)":
				$str = $viaProducts['BuyHedgeMonthly']['typeSpecificId'].','.$viaProducts['BuyHedgeQuart']['typeSpecificId'].','.$viaProducts['BuyHedgeAnnual']['typeSpecificId'];
				break;
			case "thestockplaybook":
			case "the stock playbook annual":
			case "the stock playbook monthly":
			case "the stock playbook 3 months":
			case "the stock playbook (monthly)":
			case "the stock playbook (annual)":
			case "the stock playbook (3 months)":
			case "the stock playbook quarterly":
				$str = $viaProducts['TheStockPlaybookMonthly']['typeSpecificId'].','.$viaProducts['TheStockPlaybookQuart']['typeSpecificId'].','.$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'];
				break;
			case "thestockplaybookpremium":
			case "the stock playbook- premium monthly":
			case "the stock playbook- premium quarterly":
			case "the stock playbook- premium annual":
				$str = $viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'].','.$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'].','.$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'].','.$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'].','.$viaProducts['TheStockPlaybookQuart']['typeSpecificId'].','.$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'];
				break;
			case "ad free":
			case "ad free monthly":
			case "ad free minyanville":
			case "ad free minyanville complimentary":
				$str = $viaProducts['AdFreeMonthly']['typeSpecificId'];
				break;

			case "techstrat":
			case "tech strat":
			case "techstrat annual":
			case "techstrat monthly":
			case "techstrat quarterly":
				$str = $viaProducts['TechStratMonthly']['typeSpecificId'].','.$viaProducts['TechStratQuarterly']['typeSpecificId'].','.$viaProducts['TechStratAnnual']['typeSpecificId'];
				break;

		    case "gary k's equity trading setups":
			case "gary k's equity trading setups":
			case "gary k's equity trading setups / annual":
			case "gary k's equity trading setups / monthly":
			case "gary k's equity trading setups / quarterly":
			case "gary k's equity trading setups / (annual)":
			case "gary k's equity trading setups / (monthly)":
			case "gary k's equity trading setups / (quarterly)":
			case "garyk":
			$str = $viaProducts['GaryKMonthly']['typeSpecificId'].','.$viaProducts['GaryKQuarterly']['typeSpecificId'].','.$viaProducts['GaryKAnnual']['typeSpecificId'];
				break;

			case "housing market report":
			case "housingmarketreport":
			case "housing market report quarterly":
			case "housing market report 6 months":
			case "housing market report 6months":
			case "housing market report annual":
				$str = $viaProducts['Housing3Months']['typeSpecificId'].','.$viaProducts['Housing6Months']['typeSpecificId'].','.$viaProducts['HousingAnnual']['typeSpecificId'];
				break;

			case "lasvegas":
			case "las vegas":
				$str = $viaProducts['LasVegas']['typeSpecificId'];
				break;

			case "chicago":
				$str = $viaProducts['Chicago']['typeSpecificId'];
				break;

			case "phoenix":
				$str = $viaProducts['Phoenix']['typeSpecificId'];
				break;

			case "washingtondc":
			case "washingtond.c.":
			case "washington d c":
			case "washington d.c.":
				$str = $viaProducts['WashingtonDC']['typeSpecificId'];
				break;

			case "sandiego":
			case "san diego":
				$str = $viaProducts['SanDiego']['typeSpecificId'];
				break;

			case "newyorkmetro":
			case "new york metro":
				$str = $viaProducts['NewYorkMetro']['typeSpecificId'];
				break;

			case 'default':
				$str = "";
				break;
		}
			return  $str ;
	}

	/*Function returns table product.subscription_Defined_id for given productCOmplementary  subs
	@ param - pname is the pre defined product name
	*/
	function getComplementoryproddefids($pName){
		global $viaProducts;

		$str ="";
		$pName = strtolower($pName);
		switch ($pName){
			case 'buzzbanter':
			case 'buzz & banter annual':
			case 'buzz & banter monthly':
				$str = $viaProducts['BuzzComplimentary']['typeSpecificId'];
				break;
			case 'jeff cooper':
			case 'jeff cooper daily market report':
			case 'jeff cooper daily market report (annual)':
			case 'jeff cooper daily market report (monthly)':
				$str = $viaProducts['CooperComplimentary']['typeSpecificId'];
				break;
			case 'flexfolio':
			case 'flexfolio monthly':
			case 'flexfolio annual':
			case 'flex folio':
				$str = $viaProducts['FlexfolioComplimentary']['typeSpecificId'];
				break;
			case 'optionsmith':
			case 'optionsmith monthly':
			case 'optionsmith annual':
			case 'option smith':
				$str = $viaProducts['OptionsmithComplimentary']['typeSpecificId'];
				break;
		    case "bear market updates - trial (2 months) to ann":
			case "bull market timer update":
				$str = $viaProducts['BMTPAlertComplimentary']['typeSpecificId'];
				break;
			case 'bull market timer':
				$str = $viaProducts['BMTPComplimentary']['typeSpecificId'];
				break;
			case "jack":
			case "lavery insight - annual":
			case "lavery insight - monthly":
			case "lavery insight - (monthly)":
			case "lavery insight - (annual)":
				$str = $viaProducts['JackComplimentary']['typeSpecificId'];
				break;
			case "etf":
			case "grail etf & equity investor annual":
			case "grail etf & equity investor monthly":
			case "grail etf & equity investor 3 months":
			case "grail etf & equity investor (monthly)":
			case "grail etf & equity investor (annual)":
			case "grail etf & equity investor (3 months)":
				$str = $viaProducts['ETFComplimentary']['typeSpecificId'];
				break;
			case "buyhedge":
			case "buy and hedge annual":
			case "buy and hedge monthly":
			case "buy and hedge 3 months":
			case "buy and hedge (monthly)":
			case "buy and hedge (annual)":
			case "buy and hedge (3 months)":
				$str = $viaProducts['BuyHedgeComplimentary']['typeSpecificId'];
				break;
			case "thestockplaybook":
			case "the stock playbook annual":
			case "the stock playbook monthly":
			case "the stock playbook 3 months":
			case "the stock playbook (monthly)":
			case "the stock playbook (annual)":
			case "the stock playbook (3 months)":
				$str = $viaProducts['TheStockPlaybookComplimentary']['typeSpecificId'];
				break;
			case "thestockplaybookpremium":
			case "the stock playbook- premium monthly":
			case "the stock playbook- premium quarterly":
			case "the stock playbook- premium annual":
				$str = $viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId'];
				break;

			case "housing market report":
			case "housingmarketreport":
			case "housing market report quarterly":
			case "housing market report 6 months":
			case "housing market report 6months":
			case "housing market report annual":
				$str = $viaProducts['HousingComplimentary']['typeSpecificId'];
				break;
			case 'default':
				$str = "";
				break;
		}
			return  $str ;

	}
	/*Function returns table product.subscription_Defined_id for given product Trail-Annual & Monthly  subs
	@ param - pname is the pre defined product name
	*/
	function gettrialproddefids($pName){
		global $viaProducts;
		$str ="";
		$pName = strtolower($pName);
		switch ($pName){
			case 'buzzbanter':
			case 'buzz & banter annual':
			case 'buzz & banter monthly':
			case 'buzz & banter quarterly':
				$str = $viaProducts['BuzzMonthlyTrial']['typeSpecificId'].','.$viaProducts['BuzzQuartTrial']['typeSpecificId'].','.$viaProducts['BuzzAnnualTrial']['typeSpecificId'].','.$viaProducts['BuzzScott']['typeSpecificId'].','.$viaProducts['BuzzST']['typeSpecificId'];
				break;
			case 'jeff cooper':
			case 'jeff cooper daily market report':
			case 'jeff cooper daily market report (annual)':
			case 'jeff cooper daily market report (monthly)':
			case 'jeff cooper daily market report (quarterly)':
				$str = $viaProducts['CooperMonthlyTrial']['typeSpecificId'].','.$viaProducts['CooperQuartTrial']['typeSpecificId'].','.$viaProducts['CooperAnnualTrial']['typeSpecificId'].','.$viaProducts['CooperST']['typeSpecificId'];
				break;
			case 'flexfolio':
			case 'flexfolio monthly':
			case 'flexfolio annual':
			case 'flex folio':
				$str = $viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'].','.$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'].','.$viaProducts['FlexfolioST']['typeSpecificId'];
				break;
			case 'optionsmith':
			case 'optionsmith monthly':
			case 'optionsmith annual':
			case 'optionsmith':
				$str = $viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'].','.$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'].','.$viaProducts['OptionsmithST']['typeSpecificId'];
				break;
			case "bear market updates - trial (2 months) to ann":
			case "bull market timer update":
				$str = $viaProducts['BMTPAlertTrial']['typeSpecificId'];
				break;
			case "jack":
            case "lavery insight - annual":
			case "lavery insight - monthly":
			case "lavery insight - (monthly)":
			case "lavery insight - (annual)":
				$str = $viaProducts['JackMonthlyTrial']['typeSpecificId'].','.$viaProducts['JackAnnualTrial']['typeSpecificId'].','.$viaProducts['JackST']['typeSpecificId'];
				break;
			case "etf":
			case "grail etf & equity investor annual":
			case "grail etf & equity investor monthly":
			case "grail etf & equity investor 3 months":
			case "grail etf & equity investor (monthly)":
			case "grail etf & equity investor (annual)":
			case "grail etf & equity investor (3 months)":
				$str = $viaProducts['ETFMonthlyTrial']['typeSpecificId'].','.$viaProducts['ETFQuartTrial']['typeSpecificId'].','.$viaProducts['ETFAnnualTrial']['typeSpecificId'].','.$viaProducts['ETFST']['typeSpecificId'].",".$viaProducts['ETFS1MT']['typeSpecificId'];
				break;
			case "thestockplaybook":
			case "the stock playbook annual":
			case "the stock playbook monthly":
			case "the stock playbook 3 months":
			case "the stock playbook (monthly)":
			case "the stock playbook (annual)":
			case "the stock playbook (3 months)":
				$str = $viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'].','.$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'].','.$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'].','.$viaProducts['TheStockPlaybookST']['typeSpecificId'];
				break;
			case "thestockplaybookpremium":
			case "the stock playbook- premium monthly":
			case "the stock playbook- premium quarterly":
			case "the stock playbook- premium annual":
					$str = $viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'].','.$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'].','.$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'];

			break;
			case "techstrat":
			case "tech strat":
			case "techstrat annual":
			case "techstrat monthly":
			case "techstrat quarterly":
			case "techstrat (annual)":
			case "techstrat (monthly)":
			case "techstrat (quarterly)":

				$str = $viaProducts['TechStratMonthlyTrial']['typeSpecificId'].','.$viaProducts['TechStratQuarterTrial']['typeSpecificId'].','.$viaProducts['TechStratAnnualTrial']['typeSpecificId'];
				break;


		    case "gary k's equity trading setups":
			case "gary k's equity trading setups":
			case "gary k's equity trading setups / annual":
			case "gary k's equity trading setups / monthly":
			case "gary k's equity trading setups / quarterly":
			case "gary k's equity trading setups / (annual)":
			case "gary k's equity trading setups / (monthly)":
			case "gary k's equity trading setups / (quarterly)":

				$str = $viaProducts['GaryKMonthlyTrial']['typeSpecificId'].','.$viaProducts['GaryKQuarterTrial']['typeSpecificId'].','.$viaProducts['GaryKAnnualTrial']['typeSpecificId'];
				break;
			case "buyhedge":
			case "buy and hedge  annual":
			case "buy and hedge monthly":
			case "buy and hedge 3 months":
			case "buy and hedge (monthly)":
			case "buy and hedge (annual)":
			case "buy and hedge (3 months)":
				$str = $viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId'].','.$viaProducts['BuyHedgeQuartTrial']['typeSpecificId'].','.$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'].','.$viaProducts['BuyHedgeST']['typeSpecificId'].",".$viaProducts['BuyHedgeS1MT']['typeSpecificId'];
				break;

			case 'default':
				$str = "";
				break;
		}
			return  $str ;
	}
	/*Function returns table product.subscription_Defined_id for given productCOmplementary  subs
	@ param - pname is the pre defined product name
	*/
	function getCKDefIds($pName){
		global $viaProducts;
		$str ="";
		$pName = strtolower($pName);
		switch ($pName){
			case 'buzzbanter':
			case 'buzz & banter annual':
			case 'buzz & banter monthly':
				$str = $viaProducts['BuzzCK']['typeSpecificId'];
				break;
			case 'jeff cooper':
			case 'jeff cooper daily market report':
			case 'jeff cooper daily market report (annual)':
			case 'jeff cooper daily market report (monthly)':
				$str = $viaProducts['CooperCK']['typeSpecificId'];
				break;
			case 'flexfolio':
			case 'flexfolio monthly':
			case 'flexfolio annual':
			case 'flex folio':
				$str = $viaProducts['FlexfolioCK']['typeSpecificId'];
				break;
			case 'optionsmith':
			case 'optionsmith monthly':
			case 'optionsmith annual':
			case 'option smith':
				$str = $viaProducts['OptionsmithCK']['typeSpecificId'];
				break;
			case "bear market updates - trial (2 months) to ann":
			case "bull market timer update":
				$str = $viaProducts['BMTPAlertCK']['typeSpecificId'];
				break;
			case 'bull market timer':
				$str = $viaProducts['BMTPCK']['typeSpecificId'];
				break;
			case "jack":
			case "lavery insight - annual":
			case "lavery insight - monthly":
			case "lavery insight - (monthly)":
			case "lavery insight - (annual)":
				$str = $viaProducts['JackCK']['typeSpecificId'];
				break;
			case "etf":
			case "grail etf & equity investor annual":
			case "grail etf & equity investor monthly":
			case "grail etf & equity investor 3 months":
			case "grail etf & equity investor (monthly)":
			case "grail etf & equity investor (annual)":
			case "grail etf & equity investor (3 months)":
				$str = $viaProducts['ETFCK']['typeSpecificId'];
				break;
			case "thestockplaybook":
			case "the stock playbook annual":
			case "the stock playbook monthly":
			case "the stock playbook 3 months":
			case "the stock playbook (monthly)":
			case "the stock playbook (annual)":
			case "the stock playbook (3 months)":
				$str = $viaProducts['TheStockPlaybookCK']['typeSpecificId'];
				break;
			case "thestockplaybookpremium":
			case "the stock playbook- premium monthly":
			case "the stock playbook- premium quarterly":
			case "the stock playbook- premium annual":
				$str = $viaProducts['TheStockPlaybookPremiumCK']['typeSpecificId'];
				break;
	                case "buyhedge":
			case "buy and hedge annual":
			case "buy and hedge monthly":
			case "buy and hedge 3 months":
			case "buy and hedge (monthly)":
			case "buy and hedge (annual)":
			case "buy and hedge (3 months)":
				$str = $viaProducts['BuyHedgeCK']['typeSpecificId'];
				break;
			case 'default':
				$str = "";
				break;
		}
			return  $str ;

	}
	function getActiveViaProdsCount($oc_id){
		$count=0;
		$sdefid = '';
		$tempSdefid = '';
		$tempSdefidAutoRenew = '';
		if(count($_SESSION['products'])>0){
			foreach($_SESSION['products'] as $arrVal){
				foreach($arrVal as $arr){
				if($arr['orderClassId']==$oc_id){
					$count = $count+1;
					if($arr['auto_renew']==1){
						$tempSdefidAutoRenew = $arr['typeSpecificId'];
					}

				}

				}//foreach
			}//for each 1
		}
		 if($count>1 && $tempSdefidAutoRenew!=''){
				 $sdefid = $tempSdefidAutoRenew;
		 }
		return $sdefid;
	}
	/* Function checks if for given sub_def_id whether Via is having same product or counter billed in Via subs prods
	*/
	function checkpremiumprods($ids,$idval, $orderItemType){
		$id ='';
		$prodstatus = getProdViaStatus($ids, $orderItemType); //htmlprint_r($ids);htmlprint_r($prodstatus);
			if(count($prodstatus[$orderItemType])){
				foreach($prodstatus[$orderItemType] as $key=>$val){
					/**if($key==$idval){
						//echo  $idval.','.$sdefid;
						return $idval;
					}***/
					// new implemaentd on 19 mar 09
					if(in_array($key,$ids)){
						return $key;
					}

				}
			}

			return $id;
	}
	/*Function checks if for given sub_def_id whether any of Trail is alreday in Via Prods List or not
	*/
	function checktrialprods($pName,$idval, $orderItemType){
		$id = '';
		$arr = gettrialproddefids($pName);
		$ids = explode(",",$arr);
		$prodstatus = getProdViaStatus($ids, $orderItemType);
			if(count($prodstatus[$orderItemType])){
				 foreach($prodstatus[$orderItemType] as $key=>$val){
				 	if(in_array($key,$ids )){
						return $key;
					}

				}
			}
			return $id;
	}
	/*Function checks if for given sub_def_id whether any of Trail is alreday in Via Prods List or not
	*/
	function checkComplementoryProds($pName,$idval, $orderItemType){
		$ids['0'] = getComplementoryproddefids($pName);

		$prodstatus = getProdViaStatus($ids, $orderItemType);
			if(count($prodstatus[$orderItemType])){
				 foreach($prodstatus[$orderItemType] as $key=>$val){
				 	if(in_array($key,$ids )){
						return $key;
					}

				}
			}
			return $id;
	}


		/*Function checks if for given sub_def_id whether any of Cheque by Payment is alreday in Via Prods List or not
	*/
	function checkCKProds($pName,$idval, $orderItemType){
		$ids['0'] = getCKDefIds($pName);
		$prodstatus = getProdViaStatus($ids, $orderItemType);
			if(count($prodstatus)){
				 foreach($prodstatus as $key=>$val){
				 	if(in_array($key,$ids )){
						return $key;
					}

				}
			}
			return $id;
	}

	/*Function checks if for given orderClassId whether any of Cancelled is alreday in Via Cancel List or not
	*/

	function checkcancelledprods($cancelprods, $oc_id){
		if(count($cancelprods)>0){
			foreach($cancelprods as $tag=>$val){
				if($oc_id == $val['orderClassId']){
					return $val['typeSpecificId'];
				}

			}
		}
		return '';
	}
	/*returns flag to check if for given oc_id with auto_renew=do_not_renew any prod exists whose expiry date is > today's date
	*/
	function getProdStartDate($oc_id){ //echo $oc_id;
		$d = date('Y-m-d');
		$exists = false;   //flag to check if for given oc_id with auto_renew=do_not_renew any prod exists whose expiry date is > today's date
		if(count($_SESSION['products'])>0){
			foreach($_SESSION['products'] as $key=>$arr){
				if($arr['orderClassId']==$oc_id && $arr['auto_renew'] =='0'){
					$dval = substr($arr['expireDate'],0,10);
					if($dval>$d){
						$d = $dval;
						$exists  = true;
					}

				}
			}

		}
	return $exists ;
}
	/*
	function checks if logged in user is having for given array of prod premium sub_def_ids
	in Via subs prods list & set key=>value to sub_def_id=>1
	*/
    function getProdViaStatus($arr, $orderItemType){
		$exists = array();
		$orderItemType = strtoupper($orderItemType);
		if($_SESSION['LoggedIn']){
			$existing_products = $_SESSION['products'][$orderItemType];

			if(count($existing_products)){
				foreach ($existing_products as $key=>$val){
					if($val['sourceCodeId']==2){
						if(is_array($_SESSION['products'][$val['typeSpecificId']])){
							if($val['auto_renew']==1){  //directly check if active prd exists with aut_renew=Auto_renew
							$exists[$val['typeSpecificId']] = '1';
							}else if(getProdStartDate($val['orderClassId'])==true){ //check if non -expired cancelled prod exists with aut_renew=do_not_renew
								$exists[$val['typeSpecificId']] = '1';
							}
						}

					}else{
						if(in_array($val['typeSpecificId'],$arr)){ //sourceCodeId = 2 is for combo
							if($orderItemType=='PRODUCT'){ //directly check if active prd exists with aut_renew=Auto_renew
								$exists[$val['orderItemType']][$val['typeSpecificId']] = '1';
							}elseif($val['auto_renew']==1 ){
								$exists[$val['orderItemType']][$val['typeSpecificId']] = '1';
							}elseif($orderItemType=='SUBSCRIPTION'){ //
								$exists[$val['orderItemType']][$val['typeSpecificId']] = '1';
							}
					}
				}//else

				//}//foreach existing product
				}//foreach
			}//count
		}//session
		//  htmlprint_r($exists);
		return $exists;
	}


	/*	Function render Add To Cart Button with Click event
	*/
	function getaAddtoCartbtns($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods

		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
				    $producttype="Monthly";
					$str_mon = '<td ><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="cursor:pointer;" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
					</td>';
					}elseif(strstr($data['product'], 'Annual') ){
				$producttype="Annual";
					$str_ann = '<td ><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" style="cursor:pointer;"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
					</td>';
					 }else{
				    $producttype='Video';
					$str_mon = '<td align="center"><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="cursor:pointer;" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
					</td>';
}
				}

			}
		}

		$str = $str_mon.$str_ann;
		return $str;
}
function getAddtoCartRadiobtnMon($pName, $orderItemType, $optchkid){
        global $pageName;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
					 	if($id == $optchkid){
							$optchk = "checked";
						}else{
							$optchk = "unchecked";
						}

						$pName = str_replace(' ','',$pName);

					$str_mon = '<input type="radio" name="'.$pName.'" id="'.$pName.'Mon" style="cursor:pointer;" '.$optchk.' onclick="updateCartRadio (\''.$id .'\',\''.$data['orderItemType'].'\',\''.$data['oc_id'].'\',\''.addslashes($pName).'\',\''.$pageName.'\');" />';
					}
				}

			}
		}
		return $str_mon;
}

function getAddtoCartRadiobtnsAnn($pName, $orderItemType, $optchkid){


		global $pageName;
		$subdefids = getprodsubdefid(stripslashes($pName),true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Annual') ){
					 	if($id == $optchkid){
							$optchk = "checked";
						}else{
							$optchk = "unchecked";
						}

						$pName = str_replace(' ','',$pName);

					$str_ann = '<input type="radio" name="'.$pName.'" id="'.$pName.'Ann" style="cursor:pointer;" '.$optchk.'  onclick="updateCartRadio (\''.$id .'\',\''.$data['orderItemType'].'\',\''.$data['oc_id'].'\',\''.addslashes($pName).'\',\''.$pageName.'\');" />';
					 }
				}

			}
		}

		return $str_ann;
}

function getAddtoCartRadiobtnsQuatr($pName, $orderItemType,$optchkid){
		global $pageName;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if((strstr($data['product'], 'Quarterly')) || (strstr($data['product'], '3 Months'))){
					 	if($id == $optchkid){
							$optchk = "checked";
						}else{
							$optchk = "unchecked";
						}

						$pName = str_replace(' ','',$pName);

					$str_ann = '<input type="radio" name="'.$pName.'" id="'.$pName.'Qutr" style="cursor:pointer;" '.$optchk.' onclick="updateCartRadio (\''.$id .'\',\''.$data['orderItemType'].'\',\''.$data['oc_id'].'\',\''.addslashes($pName).'\',\''.$pageName.'\');" />';
					 }
				}

			}
		}

		return $str_ann;
}

	function getAddtoCartRadiobtnsHalfYearly($pName, $orderItemType,$optchkid){
		global $pageName;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], '6 Months')){
					 	if($id == $optchkid){
							$optchk = "checked";
						}else{
							$optchk = "unchecked";
						}

						$pName = str_replace(' ','',$pName);

					$str_ann = '<input type="radio" name="'.$pName.'" id="'.$pName.'Halfyearly" style="cursor:pointer;" '.$optchk.' onclick="updateCartRadio (\''.$id .'\',\''.$data['orderItemType'].'\',\''.$data['oc_id'].'\',\''.addslashes($pName).'\',\''.$pageName.'\');" />';
					 }
				}

			}
		}

		return $str_ann;
	}

/*	Function render Add To Cart Button for Optionsmith Landing PAge */
	function getSmithAddtoCartbtnsMonthly($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$HTPFXSSL;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
				    $producttype="Monthly";
					$str_mon = '
					<a id="trialshowbtn-'.$id.'" href="'.$HTPFXSSL.$HTHOST.'/subscription/register"  onclick="return checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');">
					<img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/optionsmith/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;" /></a>
					<a id="trialhidebtn-'.$id.'" style="display:none;">
					<img  src="'.$IMG_SERVER.'/images/optionsmith/'.$img.'" alt="add to cart" border="0" /></a>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getSmithAddtoCartbtnAnnual($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$HTPFXSSL;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Annual') ){
				    $producttype="Annual";
					$str_mon = '<div class="cartImage">
					<a id="trialshowbtn-'.$id.'" href="'.$HTPFXSSL.$HTHOST.'/subscription/register" onclick="return checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" >
					<img href="'.$HTPFXSSL.$HTHOST.'/subscription/register" src="'.$IMG_SERVER.'/images/optionsmith/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;"/></a>
					<a id="trialhidebtn-'.$id.'" style="display:none;">
					<img href="'.$HTPFXSSL.$HTHOST.'/subscription/register" src="'.$IMG_SERVER.'/images/optionsmith/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;"/></a>
					</div>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getSmithAddtoCartbtnsTrial($pName, $text, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$HTPFXSSL;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
				    $producttype="Monthly";
					$str_mon = '<a href="'.$HTPFXSSL.$HTHOST.'/subscription/register" style="cursor:pointer;color:inherit;"  onclick="return checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');">'.$text.'</a>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	/*TechStrat Add to cart function start*/

	function getTechStartAddtoCartbtnsMonthly($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$IMG_SERVER,$HTPFXSSL;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
				    $producttype="Monthly";
					$str_mon = '<a id="trialshowbtn-'.$id.'" href="'.$HTPFXSSL.$HTHOST.'/subscription/register" onclick="return checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" ><img  src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;" /></a>
					<a id="trialhidebtn-'.$id.'" style="display:none;"><img  src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;" /></a>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getTechStartAddtoCartbtnAnnual($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Annual') ){
				    $producttype="Annual";
					$str_mon = '<img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" style="cursor:pointer;" />
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0" style="display:none;"/>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getTechStartAddtoCartbtnQuarter($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Quarterly') ){
				    $producttype="Quarterly";
					$str_mon = '<img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" style="cursor:pointer;"/></a>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/techstrat/'.$img.'" alt="add to cart" border="0" style="display:none;"/>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getTechStartAddtoCartbtnsTrial($pName, $text, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				//if(intval($data['price'])>0){
					 $id = trim($data['subscription_def_id']);
					 if(strstr($data['product'], 'Monthly') ){
				    $producttype="Monthly";
					$str_mon = '<a style="cursor:pointer;"  onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');";>'.$text.'</a>';
					}
				//}
			}
		}
		$str = $str_mon;
		return $str;
	}

	/*techstrat Add to cart function end*/


	/*	Function render Add To Cart Button with Click event to add monthy subscription from CP	*/
	function getaAddtoCartbtnsCP($pName,$img, $orderItemType){

		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods

		$orderItemType = strtoupper($orderItemType);
		if(count($prods)){
			foreach($prods as $data){
				 $id = trim($data['subscription_def_id']);
				 if(strstr($data['product'], 'Monthly') ){
					$str_mon = '<td ><a style="cursor:pointer;" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\');";><img src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" /></a></td>';

			}
		}
		}
		$str = $str_mon;
		return $str;
	}




	function getaAddtoCartbtns_buzz($pName, $img,$orderItemType,$productName="",$eventname=""){
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids,$orderItemType );
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr,$orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		 if(count($prods)){
			foreach($prods as $data){
				if(intval($data['price'])>0){
	 if(strstr($data['product'], 'Monthly') ){
			 	$producttype="Monthly";
			 }else{
			 	$producttype="Annual";
			 }
					 $id = trim($data['subscription_def_id']);
					 if(!array_key_exists($id,$prodstatus )){
				 	$str .= '<td height="40px"  valign="top"><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');" style="cursor:hand;cursor:pointer;"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
					</td>';
					 }else{
						$str .= '<td><a style="cursor:hand;cursor:pointer;">
						<img src="'.$IMG_SERVER.'/images/redesign/add_to_cart_hide.gif" alt="add to cart" border="0" /></a></td>';
					 }
				}

			}
		}
		return $str;
	}
	/*
	function ppulates  buttons on cart IBOC confirm.php
	*/
	function getconfirmationbtns($to, $frm, $btn, $case, $orderItemType){
		$str = '';
         global $HTHOST,$HTPFX,$HTPFXSSL;
		//check wether $frm is in session & on clicking Yes display cancel screen
		$arr = array ('0'=>$frm);

		$exists  = getProdViaStatus($arr, $orderItemType);
		if($cancel=='cancel'){
			$cancel = 'true';
		} else{
		$cancel = 'false';
		if($exists[$frm]=='1'){
				$cancel = 'true';
			}
		}
		if($btn=='confirm'){
			if(!$_SESSION['LoggedIn']){
				$str = '<td align="right" colspan="2"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:confirmchoice(\'true\','.$to.','.$frm.',\''.$orderItemType.'\');iboxclose();";><img src="'.$IMG_SERVER.'/images/redesign/yes.gif" alt="add to cart" border="0" hspace="10" /></a>';
			}elseif(!$_SESSION['viaServiceError']){
				$str = '<td  align="right" colspan="2"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:cancelViaProd('.$frm.','.$to.',\'relocate\',\''.$case.'\',\''.$orderItemType.'\'); ";><img src="'.$IMG_SERVER.'/images/redesign/yes.gif" alt="add to cart" border="0" hspace="10"/></a> ';
			}elseif($_SESSION['viaServiceError']==true){
				global $viaMaintainenceMsg ;
				$str = '<td  align="right" colspan="2"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:alert(\''. $viaMaintainenceMsg.'\');iboxclose();return false; ";><img src="'.$IMG_SERVER.'/images/redesign/yes.gif" alt="add to cart" border="0" hspace="10"/></a> ';
			}

		$str .= '<a style="cursor:hand;cursor:pointer;" target="_self" onclick="iboxclose();validateloggedcart()";><img src="'.$IMG_SERVER.'/images/redesign/no.gif" alt="add to cart" border="0" hspace="10"/></a></td>';
		}elseif($btn=='ok'){
			$str .= '<td colspan="2" align="right"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="iboxclose();";><img src="'.$IMG_SERVER.'/images/redesign/ok.gif" alt="add to cart" border="0" /></a></td>';
		}elseif($btn=='checkout'){
			$str .= '<td colspan="2" align="right"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript: alreadyCartCheckout(\''.$HTPFXSSL.$HTHOST.'\');";><img src="'.$IMG_SERVER.'/images/redesign/checkout_button.jpg" alt="validate cart" border="0" /></a></td>';
		}elseif($btn=='okAdd'){
			$str .= '<td colspan="2" align="right"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:cancelViaProd('.$frm.','.$to.',\'relocate\',\''.$case.'\',\''.$orderItemType.'\');";><img src="'.$IMG_SERVER.'/images/redesign/ok.gif" alt="add to cart" border="0" /></a></td>';
		}elseif($btn=='okAddbtn'){
			$str .= '<td colspan="2" align="right"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:addProductChoice('.$to.',\''.$orderItemType.'\');iboxclose();";><img src="'.$IMG_SERVER.'/images/redesign/ok.gif" alt="add to cart" border="0" /></a></td>';
		}

		return $str;
	}






	/*	Function render B&B subscription contents*/
	function getBuzzBantersubsView(){
		build_lang('subscription_product');
		global $lang;
		$str = '<div class="fisrt_subs_container">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="39%" rowspan="2" valign="top">
			 <img src="'.$IMG_SERVER.'/images/redesign/subs_buzzbanter.jpg" alt="Buzz &amp;  Banter" width="375" height="54" />
			  '.$lang['BuzzBanter_desc'].learnmore('buzzbanter').'

				</td>
			<td width="61%" valign="top"><table width="150" border="0" cellpadding="0" cellspacing="0" class="add_to_cart">
			  <tr>
				<td width="123" class="subs_recurring">'.$lang['Monthly'].'</td>
				<td width="1px" rowspan="3"><img src="'.$IMG_SERVER.'/images/redesign/cart_divider.jpg" width="1" height="70" /></td>
				<td width="444" class="subs_recurring">'.$lang['Annual'].'</td>
			  </tr>
			  <tr>'.getProdSubPrice('buzzbanter', 'subscription').'</tr>
			  <tr>'.getaAddtoCartbtns('buzzbanter','add_to_cart.gif','subscription','subBuzzBanter').'</tr>
			</table></td>
		  </tr>
		  <tr>
			<td valign="top">
		   <div class="trial_box">
		   <span class="trial_heading">14 DAY FREE TRIAL INCLUDED<br/>Questions? Call
			  '.$lang['BuzzBanter_call'].' </span> '.alreadyamember('buzzbanter').'

			</div>
			</td>
			</tr>
		</table>
		</div>';
		return $str;
	}
	/*fucntion dipalys learn more link for each product*/
	function learnmore($p){
		$str = '';
		switch ($p){
			case 'buzzbanter' :
				$url  = getpageurl('buzzbanter');
				$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'cooperhome':
				$url  = getpageurl('cooperhome');
				$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'qphome':
			$url  = getpageurl('qphome');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'sshome':
			$url  = getpageurl('sshome');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'bmtp_home':
			$url  = getpageurl('bmtp_home');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'jack_home':
			$url  = getpageurl('jack_home');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'etf_home':
			$url  = getpageurl('etf_home');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'buyhedge_home':
			$url  = getpageurl('buyhedge_home');
			$str .='<div class="subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'thestockplaybook':
				$url  = getpageurl('thestockplaybook');
				$str .='<div class="tsp_subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
			case 'adfree':
				$url  = getpageurl('thestockplaybook');
				$str .='<div class="tsp_subs_more"><a href="'.$url['alias'].'" target="_self">';
				break;
		}
		$str .='learn more &#187;</a></div>';
		return $str;

	}
	/*	Function displays already a member link for products subs
	for not logged in users redirect to Product home page
	*/
	function alreadyamember($pname){
		global $_SESSION,$HTPFXSSL,$HTHOST;
		$str ='';
		$result = getpageurl($pname);
		if($result['alias']){
			$targeturl = $result['alias'];
		}else{
			$targeturl ='';
		}

	if($pname=='buzzbanter' ){
		$link = $HTPFX.$HTHOST."/buzz/buzz.php";
		$str .= '<div class="subs_small_heading">';
		$str.= "<a style='cursor:pointer;' onClick='window.open(\"".$link."\",
\"Banter\",\"width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\");banterWindow.focus();'
>already a Subscriber? log-in</a>";
	}else{
		if(!$_SESSION['LoggedIn']){
			$height=495;$width=532;
			$url=$HTPFXSSL.$HTHOST."/subscription/register/iboxindex.htm";$linkId="navlink_1";$label='already a Subscriber? log-in';
			$str = '<div class="subs_small_heading">';
			$str.= iboxCall($linkId,$label,$url,$height,$width, $targeturl);
			$str.='</div>';//<a href="#">already a member? log-in </a>
		}else{
			$link = getPageLink($pname, $targeturl);
			$str .= '<div class="subs_small_heading">';
			$str.= '<a href="'.$link.'">already a Subscriber? log-in </a>';
			$str.='</div>';
		}


	}
		return $str;
	}


	/*
		retuirns already a member logged in redirction link

	*/
	function getPageLink($pname, $targeturl){
		$user=new user();
		$link= $HTPFXSSL.$HTHOST.'/subscription/register/';

		switch ($pname){
				case 'buzzbanter':
				if($user->is_buzz()){
					$link= '';
				}
				break;
			case 'cooperhome':
				if($user->is_cooper()){
					$link= $HTPFX.$HTHOST.$targeturl;
				}
				break;
			case 'qphome':
				if($user->is_quint()){
					$link= $HTPFX.$HTHOST.$targeturl;
				}
				break;

			case 'sshome':
				if($user->is_option()){
					$link= $HTPFX.$HTHOST.$targeturl;
				}
			case 'bmtp_home':
				if($user->is_bmtp()){
					$link= $HTPFX.$HTHOST.$targeturl;
				}
			case 'jack_home':
				if($user->is_jack()){
					$link= $HTPFX.$HTHOST.$targeturl;
				}

				break;

			}

		return $link;

	}

	/*	Function render jeff Cooper subscription contents
	*/
	function getJeffCoopersubsView(){
	    build_lang('subscription_product');
		global $lang;
		$str = '<div class="secon_subs_container">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="39%" rowspan="2" valign="top">
			  <img src="'.$IMG_SERVER.'/images/redesign/subs_jeff_cooper.jpg" alt="Jeff cooper Market Report" />
			  '.$lang['JeffCoopet_desc'].learnmore('cooperhome').'

			<td width="61%" valign="top"><table width="150" border="0" align="center" cellpadding="0" cellspacing="0" class="add_to_cart_cooper">
			  <tr>
				<td width="123" class="subs_recurring">'.$lang['Monthly'].'</td>
				<td width="1px" rowspan="3"><img src="'.$IMG_SERVER.'/images/redesign/cart_divider.jpg" width="1" height="70" /></td>
				<td width="444" class="subs_recurring">'.$lang['Annual'].'</td>
			  </tr>
			  <tr>'.getProdSubPrice('Jeff Cooper','subscription').'</tr>
			  <tr>'.getaAddtoCartbtns('Jeff Cooper','add_to_cart.gif', 'subscription','subCooper').'</tr>
			</table></td>
		  </tr>

		  <tr>
			<td valign="top">
			<div class="trial_box">
			<div class="trial_heading_blue">14 DAY FREE TRIAL INCLUDED<br/>Questions? Call
			 '.$lang['JeffCoopet_call'].'</div> '.alreadyamember('cooperhome').'
			</div>	</td>
			</tr>
		</table>
		</div>';
		return $str;
	}




	/*	Function render FlexFoloi subscription contents
	*/
	function getFlefFoliosubsView(){
		global $lang;
		$str = '<div class="third_subs_container">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="39%" rowspan="2" valign="top">
			 <img src="'.$IMG_SERVER.'/images/redesign/subs_flexfolio.jpg" alt="FlexFolio" />
			  '.$lang['Flexfolio_desc'].learnmore('qphome').'
				</td>
			<td width="61%" valign="top"><table width="150" border="0" align="center" cellpadding="0" cellspacing="0" class="add_to_cart_folio">
			  <tr>
				<td width="123" class="subs_recurring">'.$lang['Monthly'].'</td>
				<td width="1px" rowspan="3"><img src="'.$IMG_SERVER.'/images/redesign/cart_divider.jpg" width="1" height="70" /></td>
				<td width="444" class="subs_recurring">'.$lang['Annual'].'</td>
			  </tr>
			  <tr>'.getProdSubPrice('FlexFolio','subscription').'</tr>
			  <tr>'.getaAddtoCartbtns('FlexFolio','add_to_cart.gif', 'subscription','subFlexFolio').'</tr>
			</table></td>
		  </tr>
		  <tr>
			<td  valign="top">
			 <div class="trial_box">
			 <div class="trial_heading_green">
			 14 DAY FREE TRIAL INCLUDED<br/>Questions? Call
			 '.$lang['Flexfolio_call'].' </div>'.alreadyamember('qphome').'
			</div>
			</td>
			  </tr>
		</table>
		</div>';
		return $str;
	}

	/*	Function render optionsmith subscription contents
	*/
	function getOptionSmithsubsView(){
		global $lang;

		$str = '<div class="smith_subs_container">
		<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="39%" rowspan="2" valign="top">
			 <img width="370" src="'.$IMG_SERVER.'/images/redesign/optionsmith_subscription.jpg" alt="Option Smith" />
			  '.$lang['Optionsmith_desc'].learnmore('sshome').'
				</td>
			<td width="61%" valign="top" class="smith_cart"><table  border="0" align="right" cellpadding="0" cellspacing="0" class="option_add_to_cart_box">
			  <tr>
				<td width="123" class="subs_recurring">'.$lang['Monthly'].'</td>
				<td width="1px" rowspan="3"><img src="'.$IMG_SERVER.'/images/redesign/cart_divider.jpg" width="1" height="70" /></td>
				<td width="444" class="subs_recurring">'.$lang['Annual'].'</td>
			  </tr>
			  <tr>'.getProdSubPrice('OptionSmith','subscription').'</tr>
			  <tr>'.getaAddtoCartbtns('OptionSmith','add_to_cart.gif', 'subscription','subOptionSmith').'</tr>
			</table></td>
		  </tr>
		  <tr>
			<td  valign="top">
			 <div class="trial_box">
			 <div class="trial_heading_blue">
			 14 DAY FREE TRIAL INCLUDED<br/>Questions? Call
			 '.$lang['Optionsmith_call'].' </div>'.alreadyamember('sshome').'
			</div>
			</td>
			  </tr>
		</table>
		</div>';
		return $str;
	}
	/*	Function render optionsmith subscription contents
	*/
	function getJacksubsView(){
		global $lang, $IMG_SERVER;

		$str = '<div class="smith_subs_container">
		<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="39%" rowspan="2" valign="center">
			 <img width="370" src="'.$IMG_SERVER.'/images/jack/subs_cart_lavery_logo.jpg" alt="Jack" />
			  '.$lang['Jack_desc'].learnmore('jack_home').'
				</td>
			<td width="61%" valign="top" class="smith_cart"><table  border="0" align="right" cellpadding="0" cellspacing="0" class="option_add_to_cart_box">
			  <tr>
				<td width="123" class="subs_recurring">'.$lang['Monthly'].'</td>
				<td width="1px" rowspan="3"><img src="'.$IMG_SERVER.'/images/redesign/cart_divider.jpg" width="1" height="70" /></td>
				<td width="444" class="subs_recurring">'.$lang['Annual'].'</td>
			  </tr>
			  <tr>'.getProdSubPrice('jack','subscription').'</tr>
			  <tr>'.getaAddtoCartbtns('jack','add_to_cart.gif', 'subscription','subLaveryInsight').'</tr>
			</table></td>
		  </tr>
		  <tr>
			<td  valign="top">
			 <div class="trial_box">
			 <div class="trial_heading_blue">
			 14 DAY FREE TRIAL INCLUDED<br/>Questions? Call
			 '.$lang['Optionsmith_call'].' </div>'.alreadyamember('jack_home').'
			</div>
			</td>
			  </tr>
		</table>
		</div>';
		return $str;
	}

/*	Function render Add To Cart Button with Click event for BMTp prod+Susb together
	*/
	function getaAddtoCartbtnsBmtp($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		$orderItemType = "SUBSCRIPTION'".","."'PRODUCT";

		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		foreach($prods as $data){
			 $orderItemTypesVal[] = strtoupper($data['orderItemType']);
			 $subdefidsVals[] = strtoupper($data['subscription_def_id']);
			 $oc_idsVal[]= strtoupper($data['oc_id']);
		 }
		 $orderItemTypes = implode("-",$orderItemTypesVal);
		 $subdefidsVal = implode("-",$subdefidsVals);
		 $oc_ids = implode("-",$oc_idsVal);
		$producttype="VideoAlert";
		$str .= '<td align="center" colspan="2"><a  style="cursor:pointer;" onclick="toaddcheckcart (\''.$subdefidsVal .'\',\''.$oc_ids.'\',\''.$orderItemTypes.'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\');"><img src="'.$IMG_SERVER.'/images/redesign/'.$img.'" alt="add to cart" border="0" hspace="5" /></a></td>';
	return $str;
	}


	function getBMTPsubsView(){
	global $lang, $IMG_SERVER;
	$str = '<div class="bmtp_subs_container">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td valign="top"><table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td width="80%" valign="top"><img  src="'.$IMG_SERVER.'/images/bmtp/bmtp_subs_logo.gif" vspace="10" hspace="10" alt="Bull Market Tracker" vspace="8" /></td>

			<td valign="top" colspan="2" class="bmtp_cart">
				<table width="100%" border="0" cellspacing="3" cellpadding="0" class="bmtp_add_to_cart_subsbox">
				<tr>
				<td class="bmtp_recurring" width="45%" valign="top">'.$lang['videoonly'].'</td>
				</tr>
				<tr>'.getProdSubPrice('bull market timer','product').'</tr>
				  <tr>'.getaAddtoCartbtns('bull market timer','add_to_cart.gif', 'product','subBMTP').'</tr>
				  <tr>
				  <td colspan="2" class="bmtp_recurring_bottom">money back guarantee </td>
				  </tr>
				  </table>
				  </td>
				  </tr>
				  </table>
	</td>
	</tr>
	<tr><td valign="top" colspan="2"><table width="99%" border="0" cellspacing="0" cellpadding="0" align="right">
	  <tr><td width="59%">'.$lang['bmtp_desc'].learnmore('bmtp_home').'</td>
			<td width="40%"><div class="trial_box"><div class="trial_heading_blue">
			7 Day Money Back Guarantee<br/>Questions? Call
			'.$lang['Optionsmith_call'].' </div>'.alreadyamember('bmtp_home').'
			</div></td>
	</tr></table>
	</td></tr></table></div>';
	return $str;
}
function getETFsubsView(){
		global $lang, $IMG_SERVER,$D_R;
		include_once("$D_R/lib/etf/_etf_lib.php");
		$objETFTrader = new Etftrader();
		$arETFProductType = $objETFTrader->getETFProductType('Subscription');
		$str = '<div class="etf_subs_container">
		<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td colspan="2"><img height="47" width="461" alt="ETF" src="'.$IMG_SERVER.'/images/etf/ETF_header_cell.jpg"/></td>
		  </tr>
		  <tr>
			<td rowspan="2" valign="top" class="etf_subs_left">
			  '.$lang['etf_desc'].learnmore('etf_home').'
			</td>
			<td valign="top" class="etf_subs_cart_container">';
		foreach($arETFProductType as $arRow)
		{
			$str.='<div class="left_etf_cart">
				<h2>'.strtoupper($arRow['product_type']).'</h2>
				<h1>'.$arRow['price_format'].'</h1>
				<div><a style="cursor:pointer;" onclick="checkcart(\''.$arRow['subscription_def_id'].'\',\''.$arRow['oc_id'].'\',\''.$arRow['orderItemType'].'\',\''.$arRow['google_analytics_product_name'].'\',\''.$arRow['product_type'].'\',\''.$arRow['google_analytics_action'].'\');"><img src="'.$IMG_SERVER.'/images/etf/add_to_cart.jpg" alt="add to cart" border="0" /></a></div>
				</div>';
		}
		$str.='</td>
		  </tr>
		  <tr>
			<td  valign="top">
			<div class="etf_trial_box">
				<div class="etf_subs_trial">
				 CLICK ABOVE AND GET A FREE 14 DAY TRIAL<br/>Questions? Contact us at
				 '.$lang['Optionsmith_call'].' or
				 <br>
				 <span><a href="mailto:support@minyanville.com">support@minyanville.com</a></span>
				 </div>
				 '.alreadyamember('etf_home').'
				</div>
			</td>
			</tr>
		</table>
		</div>';
		return $str;
	}

function getBuyHedgesubsView(){
		global $lang, $IMG_SERVER,$D_R;
		include_once("$D_R/lib/etf/_etf_lib.php");
		$objETFTrader = new Etftrader();
		$arETFProductType = $objETFTrader->getETFProductType('Subscription');
		$str = '<div class="etf_subs_container">
		<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  	<td colspan="2"><img height="47" width="461" alt="ETF" src="'.$IMG_SERVER.'/images/etf/ETF_header_cell.jpg"/></td>
		  </tr>
		  <tr>
			<td rowspan="2" valign="top" class="etf_subs_left">
			  '.$lang['etf_desc'].learnmore('etf_home').'
			</td>
			<td valign="top" class="etf_subs_cart_container">';
		foreach($arETFProductType as $arRow)
		{
			$str.='<div class="left_etf_cart">
				<h2>'.strtoupper($arRow['product_type']).'</h2>
				<h1>'.$arRow['price_format'].'</h1>
				<div><a style="cursor:pointer;" onclick="checkcart(\''.$arRow['subscription_def_id'].'\',\''.$arRow['oc_id'].'\',\''.$arRow['orderItemType'].'\',\''.$arRow['google_analytics_product_name'].'\',\''.$arRow['product_type'].'\',\''.$arRow['google_analytics_action'].'\');"><img src="'.$IMG_SERVER.'/images/etf/add_to_cart.jpg" alt="add to cart" border="0" /></a></div>
				</div>';
		}
		$str.='</td>
		  </tr>
		  <tr>
			<td  valign="top">
			<div class="etf_trial_box">
				<div class="etf_subs_trial">
				 CLICK ABOVE AND GET A FREE 14 DAY TRIAL<br/>Questions? Contact us at
				 '.$lang['Optionsmith_call'].' or
				 <br>
				 <span><a href="mailto:support@minyanville.com">support@minyanville.com</a></span>
				 </div>
				 '.alreadyamember('etf_home').'
				</div>
			</td>
			</tr>
		</table>
		</div>';
		return $str;
	}

function getTheStockPlayBooksubsView(){
		global $lang, $IMG_SERVER,$D_R;
		include_once("$D_R/lib/thestockplaybook/_theStockPlaybookData.php");
		$objTheStockPlaybook = new theStockPlaybook();
		$arTheStockPlaybookProductType = $objTheStockPlaybook->getTheStockPlaybookProductType();
		$str = '<div class="tsp_subs_container">
		<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td rowspan="2" valign="top" class="tsp_subs_left"><img alt="The Stock Playbook" src="'.$IMG_SERVER.'/images/tsp/tsp_header_cell.jpg"/>
			'.$lang['thestockplaybook_desc'].'
			</td>
			<td valign="top" class="tsp_subs_cart_container">';
		foreach($arTheStockPlaybookProductType as $arRow)
		{
			$str.='<div class="tsp_cart">
				<h2>'.strtoupper($arRow['product_type']).'</h2>
				<h1>'.$arRow['price_format'].'</h1>
				<div><a style="cursor:pointer;" onclick="checkcart(\''.$arRow['subscription_def_id'].'\',\''.$arRow['oc_id'].'\',\''.$arRow['orderItemType'].'\',\''.$arRow['google_analytics_product_name'].'\',\''.$arRow['product_type'].'\',\''.$arRow['google_analytics_action'].'\');"><img src="'.$IMG_SERVER.'/images/etf/add_to_cart.jpg" alt="add to cart" border="0" /></a></div>
				</div>';
		}
		$str.='</td>
		  </tr>
		  <tr>
			<td  valign="top">
			<div class="tsp_trial_box">
				<div class="tsp_subs_trial">
				 CLICK ABOVE AND GET A FREE 14 DAY TRIAL<br/>Questions? Contact us at
				 '.$lang['Optionsmith_call'].' or
				 <br>
				 <span><a href="mailto:support@minyanville.com">support@minyanville.com</a></span>
				 </div>
				 '.alreadyamember('thestockplaybook').'
				</div>'.learnmore('thestockplaybook').'
			</td>
			</tr>
		</table>
		</div>';
		return $str;
	}

	/*Function render INTRO TEXT on sub&prod page*/
	function displayIntrotext (){
		global $lang,$_SESSION;
		If(count($_SESSION['viacart'])>0){
			$str = '<div class="intro_title_cart">';
		}else{
			$str = '<div class="intro_title">';
		}

		$str .= 'Subscription Products That Give You an Edge</div>'.$lang['INTRO_COPY'].'</div>';
		return $str;
	}



	/*function populates all teh mVIL offred products*/

	function rendersubscriptionprods($lang){
	?>

		 <div class="subscription_container" >
		 <div class="subscriptionsponsorship"><?=CM8_ShowAd('PageTitleSponsorship');?></div>
	<?
		/**<!--Step subs box start from here-->**/
		$str  = getBuzzBantersubsView();
		$str .=getETFsubsView();
		$str .=getTheStockPlayBooksubsView();
		$str .=getJeffCoopersubsView();
		$str .=getOptionSmithsubsView();
		$str .=getFlefFoliosubsView();

		//$str .=getJacksubsView();
		//$str .=getBMTPsubsView();
		/**<!--Step subs box end  here-->**/
		$str .='</div>';
		return $str;
	}

	function cartiboxmsg($msg='', $pfrm_product='', $pto_product=''){
		$patterns = array();
		$patterns['0'] = '/product_replace/';
		$patterns['1'] = '/billing_replace/';
		$patterns['2'] = '/frm_product/';
		$patterns['3'] = '/to_product/';
		$patterns['4'] = '/14 Day Free Trial /';
		$patterns['5'] = '/Complimentary /';
		$patterns['6'] = "/Lavery's Newsletter -  /";
		$patterns['7'] = "/LAvery's Newsletter -  /";

		$replacements = array();
		$replacements['0'] = $pfrm_product;
		$replacements['1'] = '';
		$replacements['2'] = $pfrm_product;
		$replacements['3'] = $pto_product;
		$replacements['4'] = '';
		$replacements['5'] = '';
		$replacements['6'] = '';
		$replacements['7'] = '';
	 	$msg = preg_replace($patterns,$replacements,$msg );
		return $msg;
	}

	/*
	function ppulates  buttons on cart IBOX confirm.php*/
	function getinlineconfirmation($frm, $to, $btn, $id,$case,$frmOrderItemType){
	global $IMG_SERVER;
	  	$str = '';
		$targetUrlval = '/subscription/register/';
		if($btn=='confirm' && $case =='call_cancel_subscription'){
			$str = '<td><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:callCancelfromInline('.$frm.','.$to.',\''.$case.'\',\''.$frmOrderItemType.'\');";><img src="'.$IMG_SERVER.'/images/registration/bttn_yes.jpg" alt="add to cart" border="0" /></a></td>';
		}else{
			$str = '<td><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:cancelViaProd('.$to.','.$frm.',\'NULL\',\''.$case.'\',\''.$frmOrderItemType.'\');";><img src="'.$IMG_SERVER.'/images/registration/bttn_yes.jpg" alt="add to cart" border="0" /></a></td>';
		}
		//over write Yes button action if Via Service is down
		if($_SESSION['viaServiceError']==true){
				global $viaMaintainenceMsg ;
				$str = '<td><a style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:alert(\''. $viaMaintainenceMsg.'\');return false; ";><img src="'.$IMG_SERVER.'/images/registration/bttn_yes.jpg" alt="add to cart" border="0" /></a></td>';
			}

		$str .= '<td><a  style="cursor:hand;cursor:pointer;" target="_self" onclick="removefrmcart(\''.$frm.'\',\''.$frmOrderItemType.'\');hideeleid(\''.$id.'\');;"><img src="'.$IMG_SERVER.'/images/registration/bttn_no.jpg" alt="add to cart" border="0" /></a></td>';
		$str .= '<div>';
		return $str;
	}

	function getInlineConfirmRemoveAndHide($frm, $to, $btn, $id,$case,$frmOrderItemType){
	global $IMG_SERVER;
		$str = '';
		$targetUrlval = '/subscription/register/';
		$str .= '<td><a  style="cursor:hand;cursor:pointer;" target="_self" onclick="javascript:cancelViaProd('.$to.','.$frm.',\'NULL\',\''.$case.'\',\''.$frmOrderItemType.'\');"><img src="'.$IMG_SERVER.'/images/registration/bttn_ok.jpg" alt="add to cart" border="0" /></a></td>';
		return $str;
	}

	function getInlineAdsFreeConfirmation($to,$id,$frmOrderItemType){
	global $IMG_SERVER;
		$str = '';
		$targetUrlval = '/subscription/register/';
		$str .= '<td><a  style="cursor:hand;cursor:pointer;" target="_self" onclick="removefrmcart(\''.$to.'\',\''.$frmOrderItemType.'\');hideeleid(\''.$id.'\');;"><img src="'.$IMG_SERVER.'/images/registration/bttn_ok.jpg" alt="add to cart" border="0" /></a></td>';
		return $str;
	}

	function getInlineConfirmAndHide($id){
	global $IMG_SERVER;
		$str = '';
		$targetUrlval = '/subscription/register/';
		$str .= '<td><a  style="cursor:hand;cursor:pointer;" target="_self" onclick="hideeleid(\''.$id.'\');"><img src="'.$IMG_SERVER.'/images/registration/bttn_ok.jpg" alt="add to cart" border="0" /></a></td>';
		return $str;
	}

	function getInlineConfirmAndSetSess($id){
	global $IMG_SERVER;
		$str = '';
		$targetUrlval = '/subscription/register/';
		 //$str .= '<td><a  style="cursor:hand;cursor:pointer;" target="_self" onclick="hideeleid(\''.$id.'\');"><img src="'.$IMG_SERVER.'/images/registration/bttn_ok.jpg" alt="add to cart" border="0" /></a></td>';
		set_sess_vars('ShowOkfreeTrial','1');
		//set_sess_vars('ShowOkfreeTrial','0');
		return $str;
	}

	function getcartaction($arr,$ocID=NULL){
		build_lang('subscription_product');
		global $lang,$viaProductsName,$viaOrderClassId;
		if(array_key_exists($ocID,$viaProductsName)){
			$prodName=$viaProductsName[$ocID];
		}
		$msg = '';
		$reset_cart_item = false;

		$case = $arr['case'];
		if(count($arr)){
		foreach($arr as $key=>$val){
				if($key!='case' && $key!='orderItemType'){
				$frm = $val;
				$to  = $key;
			}
		}
		}
		$id = $frm."_".$to;
		$promoCode = $_SESSION['promoCodeValue'];

		$pfrm = getProdSubPriceVal($frm, $arr['orderItemType']);
		$pto = getProdSubPriceVal($to, $arr['orderItemType']);
		switch ($case) {
			case 'already_in_via':
			case 'already_have_subscribed_product':
				$msg = cartiboxmsg($lang[$case], $pfrm['0']['product'], $pto['0']['product']);
				//$msg .= getInlineAdsFreeConfirmation($frm,$id,$pfrm['0']['orderItemType']);
				$reset_cart_item = 1;
			break;

			case 'promo_already_in_cart':
				$msg = $promoCode.' can only applicable for Buzz & banter purchase only';
				// $msg .= getInlineConfirmAndHide($id);
				//$reset_cart_item = 1;
			break;

			case 'trial_not_allowed' :
			        $_SESSION['ShowOkfreeTrial']=0;
				if($_SESSION['ShowOkfreeTrial']!='1'){
					$msg = '<div id="'.$id.'"><div class="add_to_cart_dis" style="margin-bottom:5px;">';
					$msg .= 'You have previously received a free trial to '.$prodName.'. Now the charge will immediately process because you are not eligible for another trial.';
					//$msg .= getInlineConfirmAndSetSess($id);
					$msg .= '</div></div>';
				}

				if(in_array($ocID,$viaOrderClassId)){
					$msg ='';
				}
			break;

			case 'counter_billed_already_in_via':
			case 'trial_already_in_via':
			case 'complementory_in_via':
			case 'checkpayment_in_via':
			case 'call_cancel_subscription':
			case 'adfree_remove_cart':
				$msg = '<div id="'.$id.'"><div class="add_to_cart_dis">';
				if($case!='call_cancel_subscription'){
				$msg .= cartiboxmsg($lang[$case], $pfrm['0']['product'], $pto['0']['product'], $id);
				}
				$from .= cartiboxmsg($pfrm['0']['product']);
				if ($case!='adfree_remove_cart'){
				if($frm==$to){
					$msg  .= 'Do you want to update subscription to '.$pto['0']['product'].'';
				}else{
					if($case == 'trial_already_in_via'){
						//$msg .=' '.'Would you like to initiate your paid subscription now?';
						$msg .=' '.'The charge will immediately process because you are not eligible for another trial.';
					}elseif($case=='counter_billed_already_in_via'){
						$msg .=' '.'Would you like to upgrade to '.$pto['0']['product'].' account? It will take effect at the end of your current month.';
					}else{
						$msg  .= 'Do you want to update subscription from '.$pfrm['0']['product'].' to '.$pto['0']['product'].'';
					}
				}
				}
				$msg  .="</div><div style='margin-top:5px;float: left;margin-bottom: 10px;margin-top: 5px;width: 100%;'>";
				if($case =='trial_already_in_via'){
				  //$msg .= getInlineConfirmRemoveAndHide($to,$frm, 'confirm', $id, $case,$pfrm['0']['orderItemType']);
				}elseif($case!='adfree_remove_cart' ){
				 $msg .= getinlineconfirmation($to,$frm, 'confirm', $id, $case,$pfrm['0']['orderItemType']);
				}else{
				///$msg .= getInlineAdsFreeConfirmation($frm,$id,$pfrm['0']['orderItemType']);
				}
				$msg .='</div></div>';

				if($case == 'trial_already_in_via' && (in_array($ocID,$viaOrderClassId))){
					$msg ='';
				}

			break;
		}
        $status = array('msg'=>$msg , 'reset_cart_item'=>$reset_cart_item);
		return $status ;
	}

	/*	function add valid new  products into yourcart area*/
	function addProdInCart($product, $price, $typeSpecificId, $orderItemType,$orderClassId,$redirectCart){
		global $lang, $show_disclaimers,$viaProducts,$promoCodeSourceCodeNoFreeTrial,$pageName,$show_free_trial,$viaOrderClassId;
		if(in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial)){
			$show_free_trial = "showPrice";
			$show_disclaimers = 0;
		}elseif(!isset($_SESSION['viaid']) && !(in_array($orderClassId,$viaOrderClassId))){
			$show_free_trial = "showTrial";
			$show_disclaimers = 1;
		}else{
			$arOrderClassId = array();

			if(is_array($_SESSION['products']['SUBSCRIPTION']))
			{
			foreach($_SESSION['products']['SUBSCRIPTION'] as $arProduct){
				$arOrderClassId[] = $arProduct['orderClassId'];
			}
			}

			if($_SESSION['cancelledOrdersStatus']==''){
				$objVia=new Via();
				$cancelledOrderStatus = $objVia->get_cancelled_order_status_with_ocId($_SESSION["viaid"]);
				set_sess_vars("cancelledOrdersStatus",$cancelledOrderStatus);
			}
			$show_price_value = "";
			if(array_key_exists($orderClassId,$_SESSION['cancelledOrdersStatus'])){
				$searchWord = '/^cancel/';
				preg_match($searchWord, strtolower($_SESSION['cancelledOrdersStatus'][$orderClassId]), $matches, PREG_OFFSET_CAPTURE);
				if((!empty($matches)) || ($_SESSION['cancelledOrdersStatus'][$orderClassId] == "SHIPPED_COMPLETE") ){
					$show_price_value = "showPrice";
				}
			}


			if($show_price_value ==''){
				if((in_array($orderClassId,$viaOrderClassId)) || (in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial))){
					$show_free_trial = "showPrice";
				}elseif($orderStatus !="" && (($pageName=="subscription_product_billing") || ($pageName=="subscription_product_order"))){
					$show_free_trial = "showPrice";
				}
				elseif(count($arOrderClassId) > 0 && in_array($orderClassId,$arOrderClassId))
				{
					$show_free_trial = "showPrice";
				}
				elseif(($pageName=="subscription_product_billing") || ($pageName=="subscription_product_order")){
					$show_free_trial = "showBoth";
					$show_disclaimers = 1;
				}
				else
				{
					$show_free_trial = "showTrial";
					$show_disclaimers = "1";
				}
			}else{
				$show_free_trial = $show_price_value;
				$show_disclaimers = "0";
			}
		}
		$strFreeTrial = "";

		if($show_free_trial == "showBoth" ){
			$strFreeTrial = '<div class="usercart_freetrial">
			<div class="usrcart_txt">'.$product.' 14 Day Free Trial</div>
			<div class="usrcart_price">$0.00<br /><span style="cursor:pointer;" class="usrcart_remove_freetrial" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
			$str ='<div class="usercart">
			<div class="usrcart_txt">'.$product.'</div>
			<div class="usrcart_price"><font style="color:red">* </font>$'.$price.'<br /><span style="cursor:pointer;" class="usrcart_remove" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
			$str=$strFreeTrial.$str;
		}elseif($show_free_trial == "showPrice" ){
			$str ='<div class="usercart">
			<div class="usrcart_txt">'.$product.'</div>
			<div class="usrcart_price">$'.$price.'<br /><span style="cursor:pointer;" class="usrcart_remove" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
		}elseif($show_free_trial == "showTrial" ){
			$str = '<div class="usercart_freetrial">
			<div class="usrcart_txt">'.$product.' 14 Day Free Trial</div>
			<div class="usrcart_price">$0.00<br /><span style="cursor:pointer;" class="usrcart_remove_freetrial" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
		}
		return $str;
	}


/* add product in cart for version 2 START */
	function addProdInCartV2($product, $price, $typeSpecificId, $orderItemType,$orderClassId,$redirectCart){
		global $lang, $show_disclaimers,$viaProducts,$show_free_trial;
		if($_SESSION['SID'] == '' && $orderClassId != $viaProducts['AdFreeMonthly']['orderClassId']){
			$show_free_trial = "showTrial";
			$show_disclaimers = 0;
		}
		elseif(!isset($_SESSION['viaid']) && !(in_array($orderClassId,$viaOrderClassId)))
		{
			$show_free_trial = "showTrial";
			$show_disclaimers = 1;
		}
		else{
			$arOrderClassId = array();
			foreach($_SESSION['products']['SUBSCRIPTION'] as $arProduct)
			{
				$arOrderClassId[] = $arProduct['orderClassId'];
			}
			if((in_array($orderClassId,$viaOrderClassId)) || (in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial))){
				$show_free_trial = "showPrice";
			}
			elseif(count($arOrderClassId) > 0 && in_array($orderClassId,$arOrderClassId))
			{
				$show_free_trial = "showPrice";
			}
			else
			{
				$show_free_trial = "showTrial";
				$show_disclaimers = "showTrial";
			}
		}
		$strFreeTrial = "";
		if($show_free_trial == "showTrial")
		{
			$str = '<div class="usercart_freetrial">
			<div class="usrcart_txt">'.$product.' 14 Day Free Trial</div>
			<div class="usrcart_price">$0.00<br /><span style="cursor:pointer;" class="usrcart_remove_freetrial" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
		}elseif($show_free_trial == "showPrice"){
			$str ='<div class="usercart">
			<div class="usrcart_txt">'.$product.'</div>
			<div class="usrcart_price">$'.$price.'<br /><span style="cursor:pointer;" class="usrcart_remove" onclick="removefrmcart('.$typeSpecificId.',\''.$orderItemType.'\',\''.$redirectCart.'\');">Remove</span></div></div>';
		}
		return $str;
}


	/* add product in cart for version 2 END */

	/*	function populate Cart Payment Details*/
	function showPaymentInfo($total,$discount , $netpayable, $divider  ){
	global $show_disclaimers,$show_free_trial;
			$_SESSION['showcheckout']= false;
		/*if($divider && $total>0){
			$str = '<tr><td colspan="2" valign="top"><hr color="#CCCCCC"/></td></tr>';
		}*/
		if($total>0){
			$_SESSION['showcheckout']= true;
		}
		if( $total>=0){
		$str.='<div class="usercart">
                	<div class="usrcart_txt">Total</div>
                    <div class="usrcart_price">';
                    if($show_disclaimers == 1 && $show_free_trial !=="showTrial")
					{
					     $str.='<font color="red" >* </font>&nbsp;';
	                }

						$str.='$'.$total.'.00</div></div>';
		}
		if($show_free_trial =="showBoth" || $show_free_trial =="showPrice"){
			if($discount>0 && $total>$discount){
					$str.='<div class="usercart"><div class="usrcart_txt">Discount</div>
						<div class="usrcart_price">$'.number_format($discount, 2, '.', ',').'</div></div>';
					 $str.='<div class="usercart"><div class="usrcart_txt">Net Payable</div>
						<div class="usrcart_price">';
						if($show_free_trial =="showTrial")
						{
							$str.='<font color="red" >*</font>&nbsp;';
						}
						$str.='$'.number_format($netpayable, 2, '.', ',').'</div></div>';
			}
		}

				 return $str;
	}
	/* payment display for version 2 in case of step 1 */
	function showPaymentInfoV2(){
		global $viaProducts;
		$str.='<div class="usercart">
                	<div class="usrcart_txt">Total</div>
                    <div class="usrcart_price">';
		foreach($_SESSION['viacart']['SUBSCRIPTION'] as $key=>$val){
			if($val['oc_id'] == $viaProducts['AdFreeMonthly']['orderClassId']){
				$adfree_price = 1;
			}
		}
		if($adfree_price == 1){
			$str.='$8.00';
		}else{
			$str.='$0.00';
	                }

		$str.='</div></div>';
				 return $str;
	}

	/*	function populate Cart products with status msg after logged in*/
	function showExistsMsg($product, $action){
		//$str = '<tr><td colspan="2" valign="top"><hr color="#CCCCCC"/></td></tr>';
		//$str = ' <tr><td colspan="2" >'.$product.'</td></tr>';
		$str .=' <div>'.$action.'</div>'.'<br>';
		return $str;

	}
	/*	function all the Annula prods to calculate Discount*/
	function  getViaAnuualProds(){
		global $viaProducts;
		$arr = getViaAnnual();

		//premium+Trail Annual
		return $arr;
	}
	/*	function returns count of number of prods valid for discount
		---first find in nnual Prods lis
		---then ckeck for combos
		---then search in Products session->for auto_renew=1 && auto_renew=0 set orderClassId array to not to consider Session{viacart] prds that have alreday been considered in SESSION[products]
		---then consider Session{viacart] that are in Cart & NOT in Session[prods] --To avoid duplicate considerations of Prds say B&B Annual in  Session[prods] also added in Session[viacart] --and alreday considered in orderClassId array

	*/
	function getActiveProdsForDis($prods){
		$count =0;
		$viaAnuualProds = getViaAnuualProds();
		if(count($prods)>0){
			foreach($prods as $key=>$val){
				 if(in_array($val, $viaAnuualProds)){
					$count++;
				 }

			}
		}else{
		 if(count($_SESSION['combo'])>0 ){//&& $_SESSION['combo']['auto_renew']==1
			$count = $count + 2;
		 }
		 $orderClassIdsArr = array();
		 if(count($_SESSION['products']['SUBSCRIPTION'])>0){
		   foreach($_SESSION['products']['SUBSCRIPTION'] as $arr){
					if ((in_array($arr['typeSpecificId'],$viaAnuualProds)) && ($arr['sourceCodeId']!='2') && (!in_array($arr['orderClassId'],$orderClassIdsArr) )){ //auto_renew=1 && auto_renew=0 both are considered for Discounts Calculation provided existes in SESSION{products] array
						$count++;
						$orderClassIdsArr[$arr['orderClassId']] = $arr['orderClassId'];
					}

				}
		 }
		 if(count($_SESSION['viacart']['SUBSCRIPTION'])>0){
				foreach($_SESSION['viacart']['SUBSCRIPTION'] as $arr){
					if(in_array($arr['subscription_def_id'],$viaAnuualProds)){	//if in annual prods list
						if (!is_array($_SESSION['products'][$arr['subscription_def_id']])  &&	($_SESSION['products'][$arr['subscription_def_id']]['sourceCodeId']!='2')){	 //if not in Via Prdocts Session
							if(!in_array($arr['oc_id'],$orderClassIdsArr)) {  //disount considers only one annual prod for one orderClassId
								$count++;
							}
						}
					}


				}
			}
		}
		return $count;
	}
	/*	function validate cart with prods valid for discounts e.g not monthly already in combo etc
	Say user Adds B&B annual in cart
     User adds B&B Monthly & we set B&B annual auto_renew=0 & not expired yet.
     User adds B&B Annual& we set B&B Montly auto_renew=0 & not expired yet.
	**** We wud have to remove if exists in session [prods] for prods in cart to cal souce_code_id***
	*/
	function getCartWithDiscounts($viacartVal, $sourceCodeId, $promoSourceCodeIdOnMonthly){//htmlprint_r($viacart);
		$getDisPriceArr = array();
		$viaAnuualProds = getViaAnuualProds();
		$orderItemTypes = array('SUBSCRIPTION','PRODUCT');

		foreach ($orderItemTypes as $orderItemTypeVal){
			$viacart = $viacartVal[$orderItemTypeVal];
		if(count($viacart)>0){
			foreach($viacart as $key=>$val){
				if(in_array($viacart[$key]['subscription_def_id'],$viaAnuualProds)){
					// for annual cart products
					if( (!is_array($_SESSION['products'][$viacart[$key]['subscription_def_id']]) || ($_SESSION['products'][$orderItemTypeVal][$viacart[$key]['subscription_def_id']]['auto_renew'] != 1) ) && ($_SESSION['products'][$orderItemTypeVal][$viacart[$key]['subscription_def_id']]['sourceCodeId']!='2') ){ //annual is not in session & also not in combo
						$getDisPriceArr[$orderItemTypeVal][$viacart[$key]['subscription_def_id']] = $viacart[$key];
						$getDisPriceArr[$orderItemTypeVal][$viacart[$key]['subscription_def_id']]['source_code_id'] = $sourceCodeId ;
						$_SESSION['viacart'][$orderItemTypeVal][$key]['source_code_id'] = $sourceCodeId ;
					}	else{   //annual products in cart && also in session product
						$_SESSION['viacart'][$orderItemTypeVal][$key]['source_code_id'] = 1 ;
					}
				} else{  // for monthly cart products
					$getDisPriceArr[$orderItemTypeVal][$viacart[$key]['subscription_def_id']] = $viacart[$key];
					if($promoSourceCodeIdOnMonthly){ //here if valid promoocde is added it is applied on Montly subs also
						$getDisPriceArr[$orderItemTypeVal][$viacart[$key]['subscription_def_id']]['source_code_id'] = $sourceCodeId ;
						$_SESSION['viacart'][$orderItemTypeVal][$key]['source_code_id'] =$sourceCodeId ;
					}else{ //if no promo then discount only on Annual subs
						$getDisPriceArr[$orderItemTypeVal][$viacart[$key]['subscription_def_id']]['source_code_id'] = 1 ;
						$_SESSION['viacart'][$orderItemTypeVal][$key]['source_code_id'] = 1 ;
					}


				}
			}
		}

		}

		//$viacart = $viacart['SUBSCRIPTION'];

		return $getDisPriceArr;
	}
	/**
		function uopdate Session cart with valid discounts
	**/
	function updateCartDiscouns($viaCartVal, $result){
		$discount = 0;
		$orderItemTypes = array('SUBSCRIPTION', 'PRODUCT');
		foreach($orderItemTypes as $orderItemType){
			$viacart = $viaCartVal[$orderItemType];
		if(count($viacart)){
			foreach($viacart as $key=>$val){
					if(is_array($result[$orderItemType]) && count($result[$orderItemType])>0){
						$viacart[$key]['discountedPrice'] = $result[$orderItemType][$key] ;
						$_SESSION['viacart'][$orderItemType][$key]['discountedPrice'] = $result[$orderItemType][$key] ;
						$discount += $viacart[$key]['price']-$viacart[$key]['discountedPrice'];
					}else{
						$viacart[$key]['discountedPrice'] = $viacart[$key]['price'] ;
						$_SESSION['viacart'][$orderItemType][$key]['discountedPrice'] = $viacart[$key]['price'] ;

				}
			}

				$viaCartVal[$orderItemType] = $viacart;
		}


		}
	$_SESSION['discountVal']= $discount;
		return $viaCartVal;
	}
	/*	function uopdate Trial registration prods with valid discounts*/
	function updateTrialDiscouns($viacart, $result){
		if(count($viacart)){
			foreach($viacart as $key=>$val){
				if(is_array($result) && count($result)>0){
					$viacart[$key]['discountedPrice'] = $result[$val['subscription_def_id']] ;
				}
			}
		}

		return $viacart;
	}
	/*	function returns corrospinding premium sub_def_id for trail sub_def_ids required for IBOX trail registration*/
	function getTrialProdPremium(){
		global $viaProducts, $viaPrdouctsarray;

		$arr = array($viaProducts['BuzzMonthlyTrial']['typeSpecificId']=>$viaProducts['BuzzMonthly']['typeSpecificId'],
			$viaProducts['BuzzAnnualTrial']['typeSpecificId']=>$viaProducts['BuzzAnnual']['typeSpecificId'],
			$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId']=>$viaProducts['FlexfolioMonthly']['typeSpecificId'],
			$viaProducts['FlexfolioAnnualTrial']['typeSpecificId']=>$viaProducts['FlexfolioAnnual']['typeSpecificId'],
			$viaProducts['CooperMonthlyTrial']['typeSpecificId']=>$viaProducts['CooperMonthly']['typeSpecificId'],
			$viaProducts['CooperAnnualTrial']['typeSpecificId']=>$viaProducts['CooperAnnual']['typeSpecificId'],
			$viaPrdouctsarray['Ex_Email_Alert']=>$viaPrdouctsarray['Ex_Email_Alert'],
			$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId']=>$viaProducts['OptionsmithMonthly']['typeSpecificId'],
			$viaProducts['OptionsmithAnnualTrial']['typeSpecificId']=>$viaProducts['OptionsmithAnnual']['typeSpecificId'],
$viaProducts['JackMonthlyTrial']['typeSpecificId']=>$viaProducts['JackMonthlyTrial']['typeSpecificId'],
			$viaProducts['JackAnnualTrial']['typeSpecificId']=>$viaProducts['JackAnnualTrial']['typeSpecificId']

		);
		return $arr;
	}
/*	function returns trail reg prods with valid source code ids e.g. montghly =1*/
	function getTrialsWithDiscounts($getDisPriceArrVal, $sourceCodeId){
		$getDisPriceArr = array();
		$viaAnuualProds = getViaAnuualProds();
		if(count($getDisPriceArrVal)>0){
			foreach($getDisPriceArrVal as $key=>$val){
				$getDisPriceArr[$key] = $getDisPriceArrVal[$key];
				if(in_array($key,$viaAnuualProds)){
					$getDisPriceArr[$key]['source_code_id'] = $sourceCodeId ;
				}else{
					$getDisPriceArr[$key]['source_code_id'] = 1 ;
				}
				$getDisPriceArr[$key]['discountedPrice'] = $getDisPriceArr[$key]['price'] ;
			}
		}
		return $getDisPriceArr;

	}

	/*	function returns prods in cart or regidtratuion with final billable formats*/
	function getPayableProds($ids=null, $viacart=null){//$ids ='3,8,10';
		global $globaldummyViaId,$viaProducts,$D_R;
		include_once("$D_R/lib/_via_data_lib.php");
		$objVia = new Via();
		if($_SESSION['LoggedIn'] && strtoupper($_SESSION['ccType'])!='NILL' &&  $_SESSION['ccType']!=''){
			 $dummyViaId = $_SESSION['viaid'];
		}else{
			$dummyViaId = $globaldummyViaId;
		}
		$prods = array();
		if($ids !=''){
			$prods = explode(",",$ids);
		}

		if(count($_SESSION['viacart']['SUBSCRIPTION'])>0 ){				//for credit29 promocode
			foreach($_SESSION['viacart']['SUBSCRIPTION'] as $arrCart){
				if($arrCart['oc_id'] == $viaProducts['Housing3Months']['orderClassId']){
					if(count($_SESSION['products']['SUBSCRIPTION'])>0 ){
						foreach($_SESSION['products']['SUBSCRIPTION'] as $arr){
							if($arr['orderClassId'] == $viaProducts['HousingSingle']['orderClassId']){
								$currentDate = strtotime(date("Y-m-d"));
								$expireDate = substr($arr['startDate'],'0','10');
								$expireDate = strtotime(date("Y-m-d", strtotime($expireDate)) . " +30 days");
								if($expireDate >= $currentDate){
									set_sess_vars("promoCodeValue",'credit29');
									set_sess_vars("validPromoCode",true);
			  						set_sess_vars("promoCodeSourceCode",'41');
								}
							}
						}
					}
				}
			}
		}

		session_start();
		$promoSourceCodeIdOnMonthly = false;
		if($_SESSION['promoCodeValue'] && $_SESSION['validPromoCode'] ){
			$sourceCodeId = $_SESSION['promoCodeSourceCode'];
			$promoSourceCodeIdOnMonthly = true;
		}else{
			$activeprods = getActiveProdsForDis($prods);
			$result  = getSourceCode($activeprods);
			$sourceCodeId = $result['source_code_id'];
		}

		if($ids==null){
			$getDisPriceArr    = getCartWithDiscounts($viacart, $sourceCodeId, $promoSourceCodeIdOnMonthly );
			if($objVia->viaException ==''){
			$result = $objVia->getOrderPrice ($dummyViaId,$getDisPriceArr['SUBSCRIPTION']);
				$_SESSION['viaServiceError'] = false;
			}else{
				$_SESSION['viaServiceError'] = True;
			}
			$_SESSION['netPrice'] = $result['netPrice'];
			$viacart = updateCartDiscouns($viacart, $result);
		}else{
			$prodsPremDefIds = getTrialProdPremium();
			foreach($prods as $key=>$val){
				$pdataTrial = getProdSubPriceVal($val,'SUBSCRIPTION');// all the prods added in IBOX are only SUBSCRIPTION
				$pdataPremium = getProdSubPriceVal($prodsPremDefIds[$val],'SUBSCRIPTION');
				$getDisPriceArrVal[$val] = array(
				'oc_id' =>$pdataTrial['0']['oc_id'],
				'product' =>$pdataTrial['0']['product'],
				'order_code' =>$pdataTrial['0']['order_code'],
				'subscription_def_id' =>$pdataPremium['0']['subscription_def_id'],
				'price' =>$pdataPremium['0']['price'],
				'order_code_id' =>$pdataPremium['0']['order_code_id'],
				'orderItemType' =>'SUBSCRIPTION'
				);
			}
			/**$getDisPriceArr    = getTrialsWithDiscounts($getDisPriceArrVal, $sourceCodeId);htmlprint_r($getDisPriceArr);
			$result = $objVia->getOrderPrice ($dummyViaId,$getDisPriceArr);htmlprint_r($result);
			$trialCart = updateTrialDiscouns($getDisPriceArr, $result); htmlprint_r($trialCart); ***/
			$trialCart  =  getTrialsWithDiscounts ($getDisPriceArrVal, $sourceCodeId);
		}

		if($ids==null){
			return $viacart;
		}else{
			return $trialCart;
		}
	}
/*Function sets subscriptionId for all viacrt prods for which SESSION['products'] is having products with same orderClassId irrespectiove of auto_rnew flag.  TBD for logged in user

*/
	function updCartToViaSubscriptionId($viacart){
		$updViacart = array();
		$updViacart = $viacart;
		$orderItemTypes = array('SUBSCRIPTION','PRODUCT');
		if(count($viacart)>0 && ( count($_SESSION['products']['PRODUCT']) || count($_SESSION['products']['SUBSCRIPTION']) )){

           foreach ($orderItemTypes as $orderItemType){
			   if(count($viacart[$orderItemType]) && count($_SESSION['products'][$orderItemType]) ){
				   foreach($viacart[$orderItemType] as $cartArr){
					   foreach($_SESSION['products'][$orderItemType] as $prdArr){
					if($cartArr['oc_id']==$prdArr['orderClassId']){
							$updViacart[$orderItemType][$cartArr['subscription_def_id']]['subscriptionId'] = $prdArr['subscriptionId'];
							$_SESSION['viacart'][$orderItemType][$cartArr['subscription_def_id']]['subscriptionId'] = $prdArr['subscriptionId'];
					}

				}

			}
		}
		  }
		}


		return $updViacart;
}
/*	function populate products from cart into yourcart area*/
	function populatecart($viacart, $validatecart,$getpcval,$redirectCart){
	global $pageName, $promoCodeSourceCodeNoFreeTrial,$viaPromoProductName,$show_free_trial,$viaOrderClassId;
		build_lang('subscription_product');
		global $lang;
		$str = '';
		$pcount  = 0;
		$updatedmsg ='';
		$total=  0.00;
		$divider = true;
		$_SESSION['viaServiceError'] = false;

		//getsourcecodeid
		$ids = null;
		// make discount calculations & net price
		$viacart = getPayableProds($ids, $viacart);
		$netpayable = $_SESSION['netPrice'];
		$viacart = updCartToViaSubscriptionId($viacart);

		if(count($viacart['SUBSCRIPTION']) || count($viacart['PRODUCT'])  ){
			foreach($viacart as $orderTypeArr){
					if(count($orderTypeArr)>0){
				foreach( $orderTypeArr as $data){
				if(count($data)>0){
					//check if validate cart array is verified
					if(count($validatecart)>0){
						$action = array();
						if(is_array( $validatecart[$data['orderItemType']])){
							$action = getcartaction($validatecart[$data['orderItemType']][$data['subscription_def_id']]);//get case related cart validation action
						}
						if(!$action['reset_cart_item']){
						/*if($_SESSION['SID'] == '' && $_SESSION['promoCodeSourceCode']==''){
							$str.= addProdInCartV2($data['product'], $data['price'], $data['subscription_def_id'], $data['orderItemType'],$data['oc_id'],$redirectCart);
							}else{*/
								$str.= addProdInCart($data['product'], $data['price'], $data['subscription_def_id'], $data['orderItemType'],$data['oc_id'],$redirectCart);
							//}

							if($show_free_trial=="showTrial"){
								$data['price']='00.00';
							}
							$total +=$data['price'];
							if($action['msg']!=''){
								$divider = false;
							}else{
								$divider = true;
							}

							//$str.=$action['msg'];
							$pcount++ ;
						}elseif ($action['msg']!=='') {
							 //remove added product from viacart that is alreday in via witha ny of billing cycle
							// $updatedmsg .=    showExistsMsg ($data['product'], $action['msg']);
							/* if($updatedmsg){
		     						$str .= '<div class="cart_error_msg">'.$updatedmsg.'</div>';
							 }*/
						}
					}else{
						/*if($_SESSION['SID'] == '' && $_SESSION['promoCodeSourceCode']==''){
							$str.= addProdInCartV2($data['product'], $data['price'], $data['subscription_def_id'], $data['orderItemType'],$data['oc_id']);
							}else{*/
						$str.= addProdInCart($data['product'], $data['price'], $data['subscription_def_id'], $data['orderItemType'],$data['oc_id']);
							//}
						$total +=$data['price'];
						$pcount++ ;
					}

				}
				}//for each data
					}//if
		  	}//for each

				$discount = 0;
				if($netpayable>0){
					//$discount = $total - $netpayable;
					//promocode section added if theer is net papayable amount in cart
					if(!empty($_SESSION['viacart']) && $_GET['pcode'] !='' && !(in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial)))
					{
						$str.='<div id="buzz40error" class="buzz40error">'.$_GET["pcode"].' can only applicable for '.$viaPromoProductName[$_GET["pcode"]].' purchase only.';
						// $str .= '<span>'.getInlineConfirmAndHide("buzz40error").'</span>';
						$str .='</div>';
						if($_SESSION['SID'] == '' || $_SESSION['discountVal'] == 0){
							$str.='<div class="clr"></div>';
						}else{
							$str.= getPrmomoCode($divider,$getpcval);
						}
					}else{
					    if(($show_free_trial == "showBoth" || $show_free_trial =="showPrice") && $_SESSION['discountVal'] > 0){
						 	$str.= getPrmomoCode($divider,$getpcval);
						}
					}
				}
				/*if($_SESSION['SID'] == '' && $_SESSION['promoCodeSourceCode']==''){
					$str.= showPaymentInfoV2();
				}else{*/
				 	$str.=  showPaymentInfo($total,$_SESSION['discountVal'] , $netpayable, $divider );
				//}
		}else {
				 $str .= '<div>'.$lang['empty_cart'].'</div>';
		}
		/*if($updatedmsg){
		     $str .= '<div class="cart_error_msg">'.$updatedmsg.'</div>';
		}*/
		return $str;
	}
	/*
	function render PromoCode section
	*/
	function getPrmomoCode($divider,$getpcval){
		global $lang,$pageName;
		$str ='';
		build_lang('subscription_product');
		if($divider){
			$str  .='<tr><td colspan="3" valign="top"><hr color="#CCCCCC"/></td></tr>';
		}
		if($getpcval=="1"){
			$str  .='<div id = "err_promocode" class="promocode_error">Please enter valid PromoCode</div>';
		}else{
			$str  .='<div id = "err_promocode" class="promocode_error">&nbsp;</div>';
		}
		if($_SESSION['promoCodeValue'] ){//promoCodeValue
			$pcodeValue = $_SESSION['promoCodeValue'];
		}else{
			$pcodeValue = $lang['EnterPromoCode'];
		}
		$str.='<div class="promocode">
                	<div style="float:left;">PROMO CODE<br /><span class="ifablcl">(if applicable)</span></div>
                    <div class="usrcart_price"><input type="text" name="promocode" id="promocode" size="20" maxlength="20" value="'.$pcodeValue.'" onclick="javascript:this.value=\'\'" /></div>
                </div><br />';
		$str.='<div class="clr"></div>';
        $str.='<div class="bttnsubmit"><a href="javascript://"><img width="69" height="28" src="'.$IMG_SERVER.'/images/registration/bttn_submit.jpg" alt="Submit Promo Code" onclick="Javascript:applyPromoCode(\'err_promocode\',\''.$lang['EnterPromoCode'].'\',\''.$pageName.'\');" /></a>
                <a href="javascript://"><img onclick="Javascript:resetPromoCode(\'err_promocode\');" width="55" height="22" src="'.$IMG_SERVER.'/images/registration/bttn_reset.gif" id="btnreset" alt="Reset"/></a></div>';

		return $str;
	}
	/*
	function unset all teh removed acrt items sub_def_id items keys
	*/
	 function validate_cart(){
		if(count($viacart)){
			foreach($viacart as $key){
				if(count($key)){
					foreach($key as $type=>$val){
				if (!$val){
							unset($viacart[$key]);
						}
				}

				}

			}
		}
		return $_SESSION['viacart'];
	}

	/*Function render user your cart section*/
	function renderyourcart($viacart, $validatecart = NULL,$getpcval=NULL,$redirectCart= NULL,$replaceId=NULL){
		global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;
		global $show_disclaimers,$pageName,$show_free_trial;
		$checkoutViacart = $_SESSION['checkoutViacart'] ;
		$checkViaMaintenence = false;
		validate_cart();

		$str = '<div id="yourcart">
		<input type="hidden" name="removeId" id="removeId" value="'.$replaceId.'" />
		<div  class="yourcart">
		<h1><img width="20" height="19" src="'.$IMG_SERVER.'/images/registration/icon_cart.gif" alt=""/>Your Cart</h1>';
		if($validatecart==NULL && $_SESSION['LoggedIn']){
			$validatecart = validatecartlogged();
		}
		$str .= populatecart($viacart, $validatecart,$getpcval,$redirectCart);
		$str.='<br style="clear:both;" />';

		if($checkoutViacart == 'checkoutViacart' &&( count($viacart['SUBSCRIPTION'])>0 || count($viacart['PRODUCT'])>0) && $_SESSION['showcheckout'] )
		{
			if($_SESSION['LoggedIn']){
				$link = $HTPFXSSL.$HTHOST.'/subscription/register/billing';
			}else{
				$link = $HTPFXSSL.$HTHOST.'/subscription/register/';
			}
			if($_SESSION['viaServiceError']){
				global $viaMaintainenceMsg ;
				$viaError .=$viaMaintainenceMsg;
			}

			if($pageName=='subscription_product_order'){
				$str.='<img src="'.$IMG_SERVER.'/images/registration/checkout.png" alt="Checkout"  border="0" width="86" height="25" hspace="5" onclick="javascript:final_registration();" style="cursor:pointer;" />';
			}else{
				$str.='<a style="cursor:pointer;" href="'.$link.'"><img src="'.$IMG_SERVER.'/images/registration/checkout.png" alt="Checkout"  border="0" width="86" height="25" hspace="5"  /></a>';
			}
			$checkViaMaintenence = true;

		}else{
			 if(count($viacart['SUBSCRIPTION'])>0){
				$str.='<a style="cursor:pointer;" href="'.$HTPFXSSL.$HTHOST.'/subscription/" ><img src="'.$IMG_SERVER.'/images/registration/bttn_addanotherproduct.jpg" alt="Add Another Product"  border="0" width="164" height="28" /></a>';
			}else{
				$str.='<a style="cursor:pointer;" href="'.$HTPFXSSL.$HTHOST.'/subscription/" ><img src="'.$IMG_SERVER.'/images/registration/addproduct.jpg" alt="Add Product"  border="0" width="110" height="28" /></a>';
			}
		}

		$str .=$viaError;
		$str .='</div>';
		if($pageName =='subscription_product_billing')
		{
			$cartMsg=displayCartMsg($viacart, $validatecart,$getpcval,$redirectCart);
			$str .='<div class="StarNote">'.$cartMsg.'</div>';
		}
		if($show_disclaimers == 1 && $show_free_trial !=="showTrial")
		{
			$str .='<div class="StarNote"><strong><font color="red">*</font>&nbsp;<i>Your free trial will start immediately, but your credit card will only be charged after 14 days if you do not cancel your subscription.</i></strong></div>';
		}
		$str .='</div>';
		return $str;
	}

	/*Function render rendermerchandiseshop*/
	function rendermerchandiseshop(){
		$str = '<div class="right_common_container">
		<div class="right_common_head">
		<h2>merchandise</h2>
		</div>
		<img src="'.$IMG_SERVER.'/images/redesign/shop_ad.jpg" />
		</div>';
		return $str;
	}

	 function iboxCall($id,$label,$url,$height=495,$width=532,$tageturl=NULL){
		global $HTPFX,$HTHOST;
		$urlpass='<a id="'.$id.'" href="'.$url.'" rel="ibox;targeturl='.$tageturl.';height='.$height.';width='.$width.'"
		onClick="init_ibox();">'.$label.'</a>';
		return $urlpass;
		}

	function logout($id,$label){
		global $HTPFX,$HTHOST,$HTPFXSSL;
		$urlpass="<a id=$id' href='javascript:void(0);' target='_self' onClick='javascript:logout($id);'>$label";
		return "$urlpass</a>";
	}

function productPriceDisplay(){
	global $_SESSION,$viaProducts,$viaProductsName,$viaProductDiscount;
	echo '<div class="productError" id="productdiv">&nbsp;</div>';
	foreach($_SESSION['viacart']['SUBSCRIPTION'] as $orderClassId){
		switch($orderClassId['oc_id']){
			case '3':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('buzzbanter','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['BuzzMonthly']['price'].'</div>
				</div>';
				$strPrice_quar ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('buzzbanter','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['BuzzQuarterly']['price'].'</div>';
				if(($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']) || $_SESSION['discountVal'] == 0){
					$strPrice_quar .='<div class="productDisplayDiscount"> '.$viaProductDiscount['BuzzQuart'].' off monthly</div>';
				}
				$strPrice_quar .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('buzzbanter','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['BuzzAnnual']['price'].'</div>';
				if(($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']) || $_SESSION['discountVal'] == 0){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['BuzzAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_quar.$strPrice_ann;
				break;

				case '23':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('TechStrat','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['TechStratMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('TechStrat','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['TechStratQuarterly']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode']=='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TechStratQuart'].' off monthly</div>';
				}
				$strPrice_qua .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('TechStrat','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['TechStratAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TechStratAnnual'].'  off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;

			case '20':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('TheStockPlayBook','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['TheStockPlaybookMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('TheStockPlayBook','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['TheStockPlaybookQuart']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TheStockPlayBookQuart'].' off monthly</div>';
				}
				$strPrice_qua .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('TheStockPlayBook','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['TheStockPlaybookAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TheStockPlayBookAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;

			case '19':
			 	$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('etf','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['ETFMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('etf','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['ETFQuart']['price'].'</div>
				</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('etf','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['ETFAnnual']['price'].'</div>
				</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;
			  case '22':
			 	$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('buyhedge','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['BuyHedgeMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('buyhedge','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['BuyHedgeQuart']['price'].'</div>
				</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('buyhedge','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['BuyHedgeAnnual']['price'].'</div>
				</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;

			case '4':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('Jeff Cooper','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['CooperMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('Jeff Cooper','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['CooperQuarterly']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['CooperQuart'].' off monthly</div>';
				}
				$strPrice_qua .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('Jeff Cooper','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['CooperAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['CooperAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;

			case '5':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('FlexFolio','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['FlexfolioMonthly']['price'].'</div>
				</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('FlexFolio','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['FlexfolioAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
				$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['FlexfolioAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_ann;
				break;

			case '14':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('optionsmith','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['OptionsmithMonthly']['price'].'</div>
				</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('optionsmith','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['OptionsmithAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
				$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['OptionSmithAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_ann;
				break;

			case '21':
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio"><input type="radio" checked="checked" DISABLED /></div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['AdFreeMonthly']['price'].'</div>
				</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon;
				break;

			case '25':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('housing market report','subscription',$optchkid).'</div>
				<div class="productDisplayType"> 3 Months $'.$viaProducts['Housing3Months']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_mon .='<div class="productDisplayDiscount"> '.$viaProductDiscount['Housing3Months'].' discount to per issue price</div>';
				}
				$strPrice_mon .='</div>';

				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsHalfYearly('housing market report','subscription',$optchkid).'</div>
				<div class="productDisplayType"> 6 Months $'.$viaProducts['Housing6Months']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['Housing6Months'].' discount to per issue price</div>';
				}
				$strPrice_qua .='</div>';

				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('housing market report','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['HousingAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['HousingAnnual'].' discount to per issue price</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;
			case '27':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('TheStockPlayBookPremium','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['TheStockPlaybookPremiumMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('TheStockPlayBookPremium','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['TheStockPlaybookPremiumQuart']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TheStockPlaybookPremiumQuart'].' off monthly</div>';
				}
				$strPrice_qua .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('TheStockPlayBookPremium','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['TheStockPlaybookPremiumAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['TheStockPlaybookPremiumAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;


			case '28':
				$optchkid = $orderClassId['subscription_def_id'];
				$productName = $viaProductsName[$orderClassId['oc_id']];
				$strPrice_name = '<div class="prodBillingDisp"><div class="productDisplayName">'.$productName.' : </div>';
				$strPrice_mon ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnMon('gary k\'s equity trading setups','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Monthly $'.$viaProducts['GaryKMonthly']['price'].'</div>
				</div>';
				$strPrice_qua ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsQuatr('gary k\'s equity trading setups','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Quarterly $'.$viaProducts['GaryKQuarterly']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_qua .='<div class="productDisplayDiscount"> '.$viaProductDiscount['GaryKQuart'].' off monthly</div>';
				}
				$strPrice_qua .='</div>';
				$strPrice_ann ='<div class="productDisplay">
				<div class="productDisplayRadio">'.getAddtoCartRadiobtnsAnn('gary k\'s equity trading setups','subscription',$optchkid).'</div>
				<div class="productDisplayType"> Annual $'.$viaProducts['GaryKAnnual']['price'].'</div>';
				if($_SESSION['promoCodeSourceCode'] =='' && $orderClassId['source_code_id'] != $_SESSION['promoCodeSourceCode']){
					$strPrice_ann .='<div class="productDisplayDiscount"> '.$viaProductDiscount['GaryKAnnual'].' off monthly</div>';
				}
				$strPrice_ann .='</div></div>';
				$strPrice = $strPrice_name.$strPrice_mon.$strPrice_qua.$strPrice_ann;
				break;

			case '26':
				$strPrice = "";
				break;

			case 'default':
				$strPrice = "";
				break;

			}
			echo $strPrice;
	}
}

function renderPoluparArticle()
{
	global $HTPFX,$HTHOST,$HTPFXSSL;
	//$arrMostViewedArt = mostViewedArticle();
	$arrMostViewedArt =getMostViewdAtriclesDailyfeed();
	$showViewArt = (($arrMostViewedArt) == NULL) ? false : true;

	//$arrMostEmailedArt = mostEmailedArticle($days = 1);
	$objcache=new Cache();
	$mostEmailedArt=$objcache->getCacheMostEmailArticle();
    $arrMostEmailedArt= $mostEmailedArt->arrMostEmailedArt;
	$showEmailArt = (($arrMostEmailedArt) == NULL) ? false : true;

	$arrMostRecentArt = mostRecenetArticle();
	$showLatestArt = (($arrMostRecentArt) == NULL) ? false : true;

	if ($showViewArt || $showEmailArt || $showLatestArt)
	{
		$tab_settings = array('view','email','latest');

        $tab_count = 0;
        if ($showViewArt) { $tab_count++; $tab_settings['view']['pos'] = $tab_count; }
        if ($showEmailArt) { $tab_count++; $tab_settings['email']['pos'] = $tab_count; }
        if ($showLatestArt) { $tab_count++; $tab_settings['latest']['pos'] = $tab_count; }

        $tab_settings['view']['box'] = 'none';
        $tab_settings['email']['box'] = 'none';
        $tab_settings['latest']['box'] = 'none';

		if($showViewArt) { $tab_settings['view']['tab'] = 'selected'; $tab_settings['view']['box'] = 'block'; }
		elseif($showEmailArt) { $tab_settings['email']['tab'] = 'selected'; $tab_settings['email']['box'] = 'block'; }
        elseif($showLatestArt) { $tab_settings['latest']['tab'] = 'selected'; $tab_settings['latest']['box'] = 'block'; }

        $tabs = '';
		$sumTab=0;
		$sumDiv=0;
        if($showViewArt)
		{
			$sumTab++;
			$tabs .= "<li><a  class=\"selected\" id=\"mr".$sumTab."\" onclick=\"showHidediv('pop_ville','mr',".$sumTab.",".$tab_count.");\">Most Read</a></li>";
		}
        if($showEmailArt)
		{
			$sumTab++;
			$tabs .= "<li><a id=\"mr".$sumTab."\" onclick=\"showHidediv('pop_ville','mr',".$sumTab.",".$tab_count.");\">Emailed</a></li>";
		}
        if($showLatestArt)
		{
			$sumTab++;
			$tabs .= "<li><a id=\"mr".$sumTab."\" onclick=\"showHidediv('pop_ville','mr',".$sumTab.",".$tab_count.");\">Latest</a></li>";
		}


        print <<<HTML

			<div class="right_common_container">
				<div class="right_common_head_home">  <h2><img src="$HTPFX$HTHOST/images/home_redesign/whatpopin.jpg"  alt="What's popular in the ville" align="left"/></h2></div>
			<div class="pop_ville">
		<ul class="idTabs">
		{$tabs}
		</ul>

HTML;

        if ($showViewArt) {
			$sumDiv++;
          print <<<HTML
      <div style="display: block;" id="pop_ville$sumDiv" class='pop_ville_div'>

		<ol>

HTML;
		foreach($arrMostViewedArt as $row)
		{
			print ' <li> <a href="' .getItemURL($row['item_type'],$row['id']). '">' . mswordReplaceSpecialChars($row['title']) . '</a></li>' . "\n";
        }

		 print <<<HTML

		</ol>

HTML;

		//daily_article_index();
   //  print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML
      </div>

HTML;

        }

        if ($showEmailArt) {
			$sumDiv++;
          print <<<HTML
      <div style="display: none;" id="pop_ville$sumDiv" class='pop_ville_div'>

		<ol>
HTML;

        foreach($arrMostEmailedArt as $row)
		{
			print '<li><a href="' .getItemURL($row['item_type'],$row['id']). '">' . $row['title'] . '</a></li>' . "\n";
        }

          print <<<HTML
       	</ol>

HTML;
		//daily_article_index();
   // print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML
      </div>

HTML;
       } // if show_email

        if ($showLatestArt) {
			$sumDiv++;
          print <<<HTML
      <div style="display: none;" id="pop_ville$sumDiv" class='pop_ville_div'>

	   <ol>

HTML;

		foreach($arrMostRecentArt as $row){

			print '<li><a href="' .getItemURL($row['item_type'],$row['id']). '">' . $row['title'] . '</a></li>' . "\n";
          }

          print <<<HTML
       	</ol>

HTML;

		//daily_article_index();

          print <<<HTML

HTML;
	 //  print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML

      </div>

HTML;

       }
echo '</div>';
daily_article_index();
echo '</div>';


/*
        print <<<HTML
   </div>
</div>
HTML;
*/
	}
}

function renderFinancialContentFinanaceMvil()
{
	global $HTPFX,$HTHOST,$HTPFXSSL;
	$tab_settingsMarket = array('currency','commodity','summary','movers');

    $tab_settingsMarket['summary']['box'] = 'none';
    $tab_settingsMarket['commodity']['box'] = 'none';
    $tab_settingsMarket['currency']['box'] = 'none';
	$tab_settingsMarket['movers']['box'] = 'none';

	$tab_settingsMarket['summary']['tab'] = 'selected'; $tab_settingsMarket['summary']['box'] = 'block';
	$tab_settingsMarket['commodity']['tab'] = 'selected'; $tab_settingsMarket['commodity']['box'] = 'block';
    $tab_settingsMarket['currency']['tab'] = 'selected'; $tab_settingsMarket['currency']['box'] = 'block';
	$tab_settingsMarket['movers']['tab'] = 'selected'; $tab_settingsMarket['movers']['box'] = 'block';

	$tabMarket = '';

     $tabMarket .= "<li class=\"hover-on\"><a class=\"selected\" id=\"3\" onclick=\"showHidediv('market','mar',3,4);\">SUMMARY</a></li>";
	$tabMarket .= "<li ><a id=\"1\" onclick=\"showHidediv('market','mar',1,4);\">CURRENCIES</a></li>";
    $tabMarket .= "<li><a id=\"2\" onclick=\"showHidediv('market','mar',2,4);\">COMMODITIES</a></li>";
        $tabMarket .= "<li style='width:65px;'><a id=\"4\" onclick=\"showHidediv('market','mar',4,4);\">MOVERS</a></li>";

     ?>

                        <div class="right_common_container">
                        <div class="market">
                        <ul class="idTabs"><?php echo $tabMarket; ?>
                        </ul>
        <div class="article-right-module-shadow">
        <div id="market-div-container" style="padding:5px 0px 5px 0px">
      <div style="display: none;" id="market1" class='market_div'>
		<script src="http://finance.minyanville.com/minyanville?Module=currency&Output=JS"></script>
      </div>
      <div style="display: none;" id="market2" class='market_div'>
		<script src="http://finance.minyanville.com/minyanville?Module=commodities&Output=JS"></script>
      </div>
      <div style="display: block;" id="market3" class='market_div'>
			 <script src="http://finance.minyanville.com/minyanville?Module=watchlist&Tickers=$COMP+$NYA+$SPX+$RUT+$XAX&Output=JS"></script>
      </div>
      <div style="display: none;" id="market4" class='market_div'>
		<script src="http://studio-5.financialcontent.com/minyanville?Module=marketmovers&Output=JS"></script>
      </div>
	</div>
	</div>
	  </div><div></div>
</div>
<?php
}


function renderFeaturedProf($featuredProfId,$selectedTenProfIds)
{
    $selected_id =array();
	global $IMG_SERVER,$HTPFX,$HTHOST;
	$featProfId = (($featuredProfId) != '') ? $featuredProfId : $GLOBALS['featuredProfssId'];
	$selectedTenProfId		= $GLOBALS['selectedTenProfIds'];
	$selectedProf = selectedTenProfId($selectedTenProfId);
	$featuredProf_2 = featuredProfessor($featProfId);
	$selectedProf1 = selectedTenProfId($selectedTenProfId);
	 //$agent = $_SERVER['HTTP_USER_AGENT'];
	// print_r($agent);
	print '<div class="right_common_container">';
    print '<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr bgcolor="#ECEAEA">
	<div class="right_common_head_home"><h2><img align="left" alt="Our Professors" src="'.$HTPFX.$HTHOST.'/images/home_redesign/ourprofessors.jpg"/></h2></div>
    ';
	print '<td width="170" valign="top" bgcolor="#ECEAEA" id="Pof_id"  style="display:block">';
		print '<div class="professors_profile">
	           <div class="professors_head">';
			   $j=0;
	foreach($featuredProf_2 as $fprof1){
	if($j==0)
	{
		$pathImage = $_SERVER['DOCUMENT_ROOT']."/".$fprof1[bio_asset];
		$fileExist = file_exists($pathImage);

		if($fileExist && $fprof1[bio_asset])
		{
			print '<img src="'.$IMG_SERVER.$fprof1['bio_asset'].'" align="left" hspace="0" width= "48" height= "48" vspace="0" style="margin-right:4px;">';
		}
		else
		{
			print '<img src="'.$IMG_SERVER.'/assets/bios/mvPro_48x48.jpg" align="left" hspace="0" width= "48" height= "48" vspace="0" style="margin-right:4px;">';
		}
		$bioAnchor = $HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$featProfId;

		//print $fprof['name'].'<a href=""'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$featProfId.'"><h3>Bio</h3></a></div><div class="professors_details">' ;

		print $fprof1['name'].'<a href="'.$bioAnchor.'"  style="text-decoration:none;" ><h3>Bio</h3></a></div><div class="professors_details">' ;
	}

	//print "<a href='$HTPFX$HTHOST/news_views/contributor.htm?cid=".$featProfId."'' class='professors_details'>".$fprof['title'].'</a><br/>';

	print"<a class=\"professors_details\" href=\"" . makeArticleslinkVille($fprof1['articleid'],$fprof1['keyword'],$fprof1['blurb']). "\">".$fprof1['title']."</a><br/>";

	$j++;
}
		print <<<HTML

	</div>

HTML;

	$moreSuffix  = " &#187;";
	print "<a  style='border:hidden' href ='".$HTPFX.$HTHOST."/gazette/contributor.htm?cid=".$featProfId ."'><div class='more_from'>More from ".$fprof1['name'].$moreSuffix."</div><br/></a>";


	print'</h3>';

	print'</div>';
		print'</td>';
	///
	// end
	///////
	foreach($selectedProf1 as $key=>$value){
	$featuredProf = featuredProfessor($value['id']);
	$pofid = $value['id'];
	$selected_id[] = $value['id'];


	print '<td width="170" valign="top" bgcolor="#ECEAEA" id="Pof'.$pofid.'"  style="display:none">';
		print '<div class="professors_profile">
	           <div class="professors_head">';
			   $i=0;
	foreach($featuredProf as $fprof){
	if($i==0)
	{
		$pathImage = $_SERVER['DOCUMENT_ROOT']."/".$fprof[bio_asset];
		$fileExist = file_exists($pathImage);

		if($fileExist && $fprof[bio_asset])
		{
			print '<img src="'.$IMG_SERVER.$fprof['bio_asset'].'" align="left" hspace="0" width= "48" height= "48" vspace="0" style="margin-right:4px;">';
		}
		else
		{
			print '<img src="'.$IMG_SERVER.'/assets/bios/mvPro_48x48.jpg" align="left" hspace="0" width= "48" height= "48" vspace="0" style="margin-right:4px;">';
		}
		$bioAnchor = $HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$pofid;

		//print $fprof['name'].'<a href=""'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$featProfId.'"><h3>Bio</h3></a></div><div class="professors_details">' ;

		print $fprof['name'].'<a href="'.$bioAnchor.'" style="border:hidden"><h3>Bio</h3></a></div><div class="professors_details">' ;
	}

	//print "<a href='$HTPFX$HTHOST/news_views/contributor.htm?cid=".$featProfId."'' class='professors_details'>".$fprof['title'].'</a><br/>';

	print"<a class=\"professors_details\" href=\"" . makeArticleslinkVille($fprof['articleid'],$fprof['keyword'],$fprof['blurb']). "\">".$fprof['title']."</a><br/>";

	$i++;
}
		print <<<HTML

	</div>

HTML;

	$moreSuffix  = " &#187;";
	print "<a   style='border:hidden' href ='".$HTPFX.$HTHOST."/gazette/contributor.htm?cid=".$pofid ."'><div class='more_from'>More from ".$fprof['name'].$moreSuffix."</div><br/></a>";


	print'</h3>';

	print'</div>';
		print'</td>';

		}
	print'<td width="150" valign="top" bgcolor="#FFFFFF"><div class="professors_links">
	 <h3>';
  $comma_separated = count($selected_id);
  $comma_separated1 =implode('~',$selected_id);
	foreach($selectedTenProfId as $selectedProfsessors)
	{
		foreach($selectedProf as $key=>$selectedProfVal)
		{
			if($selectedProfsessors==$selectedProf [$key]['id'])
			{
			 // href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$selectedProf [$key]['id']."'
			   $value = $selectedProf [$key]['id'];
				echo "<h3><a  onclick=\"select_td('$value', '$comma_separated1', '$comma_separated')\">".$selectedProf [$key]['name']."</a></h3>";
			}
		}
	}

	$anchor = $HTPFX.$HTHOST.'/gazette/bios.htm';
	$moreSyn  = "More &#187;";

	print <<<HTML
		</h3>

	<span class="more">	<a href="$anchor" >$moreSyn</a></span>

	</div></td>
	  </tr>
	</table>

	</div>
HTML;
}

function daily_feed_newsletter_alert_top() {

$userEmail = $_SESSION['email'];
$userid = $_SESSION['SID'];

$userEmailVal = ($userEmail == '') ? '' : "readonly";
?>

			<span><img src="<?=$IMG_SERVER;?>/images/DailyFeed/SignUphead.gif" align="absmiddle"  alt="Sign up for Daily Feed Newsletter" border="0"/></span>
			<span>
			<?if($userEmailVal==''){
				echo '<input type="text" id="feed_email" name="feed_email" value="Enter Email Address" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);">';
				echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="">';
			}
			else
			{
				echo '<input type="text" id="feed_email" name="feed_email" value="'.$userEmail.'" "'.$userEmailVal.'">';
				echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="'.$userid.'">';
			}
			?>
			<img style="cursor:pointer;" src="<?=$IMG_SERVER;?>/images/DailyFeed/feedSubmit.gif" align="absmiddle" alt="Submit" border="0" hspace="2" onclick="iboxFeedemail('feed_email','feed_subscriber_id');"></span>
 <? }

function daily_feed_newsletter_alert_bottom() {

$userEmail = $_SESSION['email'];
$userid = $_SESSION['SID'];

$userEmailVal = ($userEmail == '') ? '' : "readonly";
?>

	<span><img src="<?=$IMG_SERVER;?>/images/DailyFeed/signUpbottom.gif" align="top"  alt="Sing up for Daily Feed Newsletter" border="0"/></span>
	<span>
	<?if($userEmailVal==''){
		echo '<input type="text" id="feed_email_bottom" name="feed_email_bottom" value="Enter Email Address" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);">';
		echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="">';
	}
	else
	{
		echo '<input type="text" id="feed_email_bottom" name="feed_email_bottom" value="'.$userEmail.'" "'.$userEmailVal.'">';
		echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="'.$userid.'">';
	}
	?>
	<img style="cursor:pointer;" src="<?=$IMG_SERVER;?>/images/DailyFeed/feedSubmit.gif" align="absmiddle" alt="Submit" border="0" hspace="10" onclick="iboxFeedemail('feed_email_bottom','feed_subscriber_id');"></span>

<? }

function daily_feed_newsletter_alert_home() {
global $HTPFX,$HTHOST,$IMG_SERVER;
$userEmail = $_SESSION['email'];
$userid = $_SESSION['SID'];

$userEmailVal = ($userEmail == '') ? '' : "readonly";
?>
<div id="newsletter-signup-header"><h2><img src="<?=$IMG_SERVER?>/images/headers/hdr-newsletter-signup.gif" alt="Sign up for news letter" /></h2></div>
<div id="newsletter-signup-input">
	<?if($userEmailVal==''){
		echo '<input type="text" id="feed_email" name="feed_email" value="Enter Email Address" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);">';
		echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="">';
	}
	else
	{
		echo '<input type="text" id="feed_email" name="feed_email" value="'.$userEmail.'" "'.$userEmailVal.'">';
		echo '<input type="hidden" id="feed_subscriber_id" name="feed_subscriber_id" value="'.$userid.'">';
	}
	?>
	<img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/headers/newsletter-submit.gif" alt="Submit" vspace="0" align="absmiddle" border="0" onclick="iboxFeedemail('feed_email','feed_subscriber_id');"></div>
<? }

function emailAlert($isloggedin,$userEmail,$targeturl)
{
	global $HTPFXSSL,$HTHOST,$IMG_SERVER;
	$checkLogedIn = $isloggedin;
	$urlpass = $HTPFXSSL.$HTHOST."/subscription/register/controlPanel.htm";

	$userEmailVal = ($userEmail == '') ? '' : "readonly";

	$mailAlertFun = '<div class="article_alert" align="center">';

	$mailAlertFun .='<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
	$mailAlertFun .='<tr>';

	if($checkLogedIn)
	{
		$mailAlertFun .='<td colspan="2" width="20%" align="center"><a href="'.$targeturl.'" ><img src="'.$IMG_SERVER.'/images/article_alert_bg.jpg" border="0"></a></td> </tr>';
	}
	else
	{
		$mailAlertFun .='<td colspan="2" width="20%" align="center"><a id="navlink_1" href="'.$urlpass.'"><img src="'.$IMG_SERVER.'/images/article_alert_bg.jpg" onClick="javascript:manilupateClickEmailAlert(\''.$urlpass.'\', '.$checkLogedIn.');" border="0"></a></td> </tr>';
	}

	$mailAlertFun .='<tr>';

	if($userEmailVal=='')
	{
		$mailAlertFun .='<td width="90%" align="right"><input type="text" name="emailAddress" id="emailAddress" size="16" value="Enter email Address" class="article_input_box" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);"></td>';
	}
	else
	{
		$mailAlertFun .='<td width="90%" align="right"><input type="text" name="emailAddress" id="emailAddress" size="16" value="'.$userEmail.'" "'.$userEmailVal.'" class="article_input_box"></td>';
	}
	$mailAlertFun .='<td width="10%" align="right">';

	if($checkLogedIn)
	{
		$mailAlertFun .= '<a href="'.$targeturl.'"><img src="'.$IMG_SERVER.'/images/article_alert_go.jpg" hspace="11" border="0"></a></td>';
	}
	else
	{
		$HTPFXSSL.$HTHOST."/subscription/register/controlPanel.htm";
		$mailAlertFun .='<a id="navlink_go" href="'.$urlpass.'"><img src="'.$IMG_SERVER.'/images/article_alert_go.jpg" hspace="11" border="0" onClick="javascript:manilupateClickEmailAlert(\''.$urlpass.'\', '.$checkLogedIn.');"></a></td>';
	}
	$mailAlertFun .='</tr>';
	$mailAlertFun .='</table>';
	$mailAlertFun .='</div>';

	return $mailAlertFun;
}


function validatecartlogged(){
		global $_SESSION, $arrcases,$viaProducts,$HTPFXSSL;

		validate_cart();
		$inlinemsg ='';
		$objViaController = new ViaController();
		if(  count($_SESSION['viacart']) && count($_SESSION['products'])  ){
			$validatedcart = array();
			foreach($_SESSION['viacart'] as $cartProdVals){
				foreach($cartProdVals as $cartprod){
				$flag = false;
				$sdefid = $cartprod['subscription_def_id'];
				$oc_id = $cartprod['oc_id'];
				$orderItemType = strtoupper($cartprod['orderItemType']);
				$pName = trim($cartprod['product']);
			    $prodsubids = str_replace("'","",getprodsubdefid($pName,true));
				$ids = explode(",",$prodsubids );
				$callCancel = false;
				 foreach ($ids as $idval){
						$idval = trim($idval);
						 //check whether this product exists in Via ACTIVE prods or its complementary
						  	if(!$flag){
							 $result = checkpremiumprods($ids, $idval, $orderItemType);
							 if($result !=''){
								 if($result == $sdefid ){
									$case = $arrcases['already_in_via'];
									$validatedcart[$orderItemType][$sdefid][$sdefid]=$sdefid;
									$flag = true;
								 }elseif($sdefid == $idval  && $result!=$sdefid ){
									 $case = $arrcases['counter_billed_already_in_via'];
									 $validatedcart[$orderItemType][$sdefid][$sdefid]=$result;
									 $flag = true;
									 $callCancel = true;
																 }
							 }elseif($sdefid==$viaProducts['AdFreeMonthly']['typeSpecificId']){
								 $adsFreemsg=$objViaController->isValidateAdsFree();
								if(!$adsFreemsg){
									$case =	$arrcases['add_to_cart'];
									$validatedcart[$orderItemType][$sdefid]['case']=$case;

									return true;
								}else{
									$case =	$adsFreemsg;
									$validatedcart[$orderItemType][$sdefid][$sdefid]=$sdefid;
									$validatedcart[$orderItemType][$sdefid]['case']=$case;
									$flag = true;
								}


								}
							}

						 //check whether this product exists in Via TRIAL prods or its complementary
						 if (!$flag) {
							$result = checktrialprods($pName,$sdefid, $orderItemType);
							if ($result!=''){
								$flag = true;
								$case = $arrcases['trial_already_in_via'];
								$validatedcart[$orderItemType][$sdefid][$sdefid]=$result;
								$callCancel = true;
							}
						}

						if (!$flag){	//check whether user ever has this product
							if($_SESSION['cancelledOrdersStatus']==''){
								$objVia=new Via();
								$cancelledOrderStatus = $objVia->get_cancelled_order_status_with_ocId($_SESSION["viaid"]);
								set_sess_vars("cancelledOrdersStatus",$cancelledOrderStatus);
							}
							if(array_key_exists($oc_id,$_SESSION['cancelledOrdersStatus'])){
								$searchWord = '/^cancel/';
								preg_match($searchWord, strtolower($_SESSION['cancelledOrdersStatus'][$oc_id]), $matches, PREG_OFFSET_CAPTURE);
								if((!empty($matches)) || ($_SESSION['cancelledOrdersStatus'][$oc_id] == "SHIPPED_COMPLETE")){
									$flag = true;
									$case = $arrcases['trial_not_allowed'];
									$validatedcart[$orderItemType][$sdefid][$sdefid]=$sdefid;
									$callCancel = true;
								}
							}
						}

						if (!$flag) {
							$result = checkComplementoryProds($pName, $idval, $orderItemType);
							if ($result!=''){
								$flag = true;
								$case = $arrcases['complementory_in_via'];
								$validatedcart[$orderItemType][$sdefid][$sdefid]=$result;
								$callCancel = true;
							}
						}

						if (!$flag) {
							$result = checkCKProds($pName, $idval, $orderItemType);
							if ($result!=''){
								$flag = true;
								$case = $arrcases['checkpayment_in_via'];
								$validatedcart[$orderItemType][$sdefid][$sdefid]=$result;
								$callCancel = true;
							}
						}
						//check if for gicen oc_id there exists >1 products in VIA subscription
					if($callCancel){
							$result = getActiveViaProdsCount($oc_id);
								if($result!='' && $idval == $result){
									$flag = true;
									$case = $arrcases['call_cancel_subscription'];
										//$existingIdval = $_SESSION['pro']
									$validatedcart[$orderItemType][$sdefid][$sdefid]=$result;
								}

						}

						if (!$flag){
							$case =	$arrcases['add_to_cart'];
						}

					$validatedcart[$orderItemType][$sdefid]['case']=$case;//=array('case'=>$case, $sdefid =>$idval);
					$validatedcart[$orderItemType][$sdefid]['orderItemType']=$orderItemType;
				 }//foreach2
			} //for each 1

			}
		}
		return $validatedcart;

}




function renderFinancialContent()
{
global $HTPFX,$HTHOST,$HTPFXSSL;
$tab_settingsMarket = array('summary','commodity','currency','movers');

$tab_settingsMarket['summary']['box'] = 'none';
$tab_settingsMarket['commodity']['box'] = 'none';
$tab_settingsMarket['currency']['box'] = 'none';
$tab_settingsMarket['movers']['box'] = 'none';

$tab_settingsMarket['summary']['tab'] = 'selected'; $tab_settingsMarket['summary']['box'] = 'block';
$tab_settingsMarket['commodity']['tab'] = 'selected'; $tab_settingsMarket['commodity']['box'] = 'block';
$tab_settingsMarket['currency']['tab'] = 'selected'; $tab_settingsMarket['currency']['box'] = 'block';
$tab_settingsMarket['movers']['tab'] = 'selected'; $tab_settingsMarket['movers']['box'] = 'block';

$tabMarket = '';

$tabMarket .= "<li><a class=\"selected\" id=\"mar1\" onclick=\"showHidediv('market','mar',1,4);\">SUMMARY</a></li>";
$tabMarket .= "<li><a id=\"mar2\" onclick=\"showHidediv('market','mar',2,4);\">COMMODITIES</a></li>";
$tabMarket .= "<li><a id=\"mar3\" onclick=\"showHidediv('market','mar',3,4);\">CURRENCIES</a></li>";
$tabMarket .= "<li><a id=\"mar4\" onclick=\"showHidediv('market','mar',4,4);\">MOVERS</a></li>";

?>

<div class="right_common_container">
<div class="right_common_head_home"> <h2><img align="left" alt="Market" src="<?=$HTPFX.$HTHOST?>/images/home_redesign/markets.jpg"/></h2>
<?=trade_now_right();?></div>
<div class="market">
<ul class="idTabs">
<?=$tabMarket?>
</ul>
<div style="display: block;" id="market1" class='market_div'></div>
<div style="display: none;" id="market2" class='market_div'></div>
<div style="display: none;" id="market3" class='market_div'></div>
<div style="display: none;" id="market4" class='market_div'></div>
</div><div></div>
</div>
<script>loadMarket()</script>
<?
}

function daily_article_index() {
global $IMG_SERVER;
?>
<div style='clear:both;'>
	<div  id="daily-article-index-right">
		<span ><a href="<?php echo $HTPFX.$HTHOST;?>/articles/articlelisting.htm">See All Articles</a>&nbsp;<span id='raquo-right' >&raquo;</span></span>
</div>
</div>
<?php
}


function showDirectoryMbox() {
global $IMG_SERVER;
?>
<style type="text/css">
.dmcontent_body {overflow-y: auto; text-align: left;font-family: Arial, Sans-Serif; line-height: 18px; font-size: 12px; margin-left: auto; margin-right: auto;width: 298px;height: 229px;color: #A5A5A5;background-color: #FFFFFF;}
.dmcontent_link {margin:0px;font-weight:normal;text-align:left;padding-top:2px;padding-bottom:2px;color:#083d70;}
</style>
<div class="right_common_container">
<div class="right_common_head_home"><h2><img align="left" alt="Minyanville Local Guides" src="<?=$IMG_SERVER?>/images/home_redesign/mv_localguides.gif"/></h2></div>
<div class="dmcontent_body">
&nbsp;<a href="http://local.minyanville.com/Alabama-nAlabama.html" title="Alabama Local Guides"class="dmcontent_link">Alabama</a><br />
&nbsp;<a href="http://local.minyanville.com/Alaska-nAlaska.html" title="Alaska Local Guides"class="dmcontent_link">Alaska</a><br />
&nbsp;<a href="http://local.minyanville.com/Arizona-nArizona.html" title="Arizona Local Guides"class="dmcontent_link">Arizona</a><br />
&nbsp;<a href="http://local.minyanville.com/Arkansas-nArkansas.html" title="Arkansas Local Guides"class="dmcontent_link">Arkansas</a><br />
&nbsp;<a href="http://local.minyanville.com/California-nCalifornia.html" title="California Local Guides"class="dmcontent_link">California</a><br />
&nbsp;<a href="http://local.minyanville.com/Colorado-nColorado.html" title="Colorado Local Guides"class="dmcontent_link">Colorado</a><br />
&nbsp;<a href="http://local.minyanville.com/Connecticut-nConnecticut.html" title="Connecticut Local Guides"class="dmcontent_link">Connecticut</a><br />
&nbsp;<a href="http://local.minyanville.com/DC-nDC.html" title="DC Local Guides"class="dmcontent_link">DC</a><br />
&nbsp;<a href="http://local.minyanville.com/Delaware-nDelaware.html" title="Delaware Local Guides"class="dmcontent_link">Delaware</a><br />
&nbsp;<a href="http://local.minyanville.com/Florida-nFlorida.html" title="Florida Local Guides"class="dmcontent_link">Florida</a><br />
&nbsp;<a href="http://local.minyanville.com/Georgia-nGeorgia.html" title="Georgia Local Guides"class="dmcontent_link">Georgia</a><br />
&nbsp;<a href="http://local.minyanville.com/Hawaii-nHawaii.html" title="Hawaii Local Guides"class="dmcontent_link">Hawaii</a><br />
&nbsp;<a href="http://local.minyanville.com/Idaho-nIdaho.html" title="Idaho Local Guides"class="dmcontent_link">Idaho</a><br />
&nbsp;<a href="http://local.minyanville.com/Illinois-nIllinois.html" title="Illinois Local Guides"class="dmcontent_link">Illinois</a><br />
&nbsp;<a href="http://local.minyanville.com/Indiana-nIndiana.html" title="Indiana Local Guides"class="dmcontent_link">Indiana</a><br />
&nbsp;<a href="http://local.minyanville.com/Iowa-nIowa.html" title="Iowa Local Guides"class="dmcontent_link">Iowa</a><br />
&nbsp;<a href="http://local.minyanville.com/Kansas-nKansas.html" title="Kansas Local Guides"class="dmcontent_link">Kansas</a><br />
&nbsp;<a href="http://local.minyanville.com/Kentucky-nKentucky.html" title="Kentucky Local Guides"class="dmcontent_link">Kentucky</a><br />
&nbsp;<a href="http://local.minyanville.com/Louisiana-nLouisiana.html" title="Louisiana Local Guides"class="dmcontent_link">Louisiana</a><br />
&nbsp;<a href="http://local.minyanville.com/Maine-nMaine.html" title="Maine Local Guides"class="dmcontent_link">Maine</a><br />
&nbsp;<a href="http://local.minyanville.com/Maryland-nMaryland.html" title="Maryland Local Guides"class="dmcontent_link">Maryland</a><br />
&nbsp;<a href="http://local.minyanville.com/Massachusetts-nMassachusetts.html" title="Massachusetts Local Guides"class="dmcontent_link">Massachusetts</a><br />
&nbsp;<a href="http://local.minyanville.com/Michigan-nMichigan.html" title="Michigan Local Guides"class="dmcontent_link">Michigan</a><br />
&nbsp;<a href="http://local.minyanville.com/Minnesota-nMinnesota.html" title="Minnesota Local Guides"class="dmcontent_link">Minnesota</a><br />
&nbsp;<a href="http://local.minyanville.com/Mississippi-nMississippi.html" title="Mississippi Local Guides"class="dmcontent_link">Mississippi</a><br />
&nbsp;<a href="http://local.minyanville.com/Missouri-nMissouri.html" title="Missouri Local Guides"class="dmcontent_link">Missouri</a><br />
&nbsp;<a href="http://local.minyanville.com/Montana-nMontana.html" title="Montana Local Guides"class="dmcontent_link">Montana</a><br />
&nbsp;<a href="http://local.minyanville.com/Nebraska-nNebraska.html" title="Nebraska Local Guides"class="dmcontent_link">Nebraska</a><br />
&nbsp;<a href="http://local.minyanville.com/Nevada-nNevada.html" title="Nevada Local Guides"class="dmcontent_link">Nevada</a><br />
&nbsp;<a href="http://local.minyanville.com/New_Hampshire-nNew+Hampshire.html" title="New Hampshire Local Guides"class="dmcontent_link">New Hampshire</a><br />
&nbsp;<a href="http://local.minyanville.com/New_Jersey-nNew+Jersey.html" title="New Jersey Local Guides"class="dmcontent_link">New Jersey</a><br />
&nbsp;<a href="http://local.minyanville.com/New_Mexico-nNew+Mexico.html" title="New Mexico Local Guides"class="dmcontent_link">New Mexico</a><br />
&nbsp;<a href="http://local.minyanville.com/New_York-nNew+York.html" title="New York Local Guides"class="dmcontent_link">New York</a><br />
&nbsp;<a href="http://local.minyanville.com/North_Carolina-nNorth+Carolina.html" title="North Carolina Local Guides"class="dmcontent_link">North Carolina</a><br />
&nbsp;<a href="http://local.minyanville.com/North_Dakota-nNorth+Dakota.html" title="North Dakota Local Guides"class="dmcontent_link">North Dakota</a><br />
&nbsp;<a href="http://local.minyanville.com/Ohio-nOhio.html" title="Ohio Local Guides"class="dmcontent_link">Ohio</a><br />
&nbsp;<a href="http://local.minyanville.com/Oklahoma-nOklahoma.html" title="Oklahoma Local Guides"class="dmcontent_link">Oklahoma</a><br />
&nbsp;<a href="http://local.minyanville.com/Oregon-nOregon.html" title="Oregon Local Guides"class="dmcontent_link">Oregon</a><br />
&nbsp;<a href="http://local.minyanville.com/Pennsylvania-nPennsylvania.html" title="Pennsylvania Local Guides"class="dmcontent_link">Pennsylvania</a><br />
&nbsp;<a href="http://local.minyanville.com/Rhode_Island-nRhode+Island.html" title="Rhode Island Local Guides"class="dmcontent_link">Rhode Island</a><br />
&nbsp;<a href="http://local.minyanville.com/South_Carolina-nSouth+Carolina.html" title="South Carolina Local Guides"class="dmcontent_link">South Carolina</a><br />
&nbsp;<a href="http://local.minyanville.com/South_Dakota-nSouth+Dakota.html" title="South Dakota Local Guides"class="dmcontent_link">South Dakota</a><br />
&nbsp;<a href="http://local.minyanville.com/Tennessee-nTennessee.html" title="Tennesee Local Guides"class="dmcontent_link">Tennesee</a><br />
&nbsp;<a href="http://local.minyanville.com/Texas-nTexas.html" title="Texas Local Guides"class="dmcontent_link">Texas</a><br />
&nbsp;<a href="http://local.minyanville.com/Utah-nUtah.html" title="Utah Local Guides"class="dmcontent_link">Utah</a><br />
&nbsp;<a href="http://local.minyanville.com/Vermont-nVermont.html" title="Vermont Local Guides"class="dmcontent_link">Vermont</a><br />
&nbsp;<a href="http://local.minyanville.com/Virginia-nVirginia.html" title="Virginia Local Guides"class="dmcontent_link">Virginia</a><br />
&nbsp;<a href="http://local.minyanville.com/Washington-nWashington.html" title="Washington Local Guides"class="dmcontent_link">Washington</a><br />
&nbsp;<a href="http://local.minyanville.com/West_Virginia-nWest+Virginia.html" title="West Virginia Local Guides"class="dmcontent_link">West Virginia</a><br />
&nbsp;<a href="http://local.minyanville.com/Wisconsin-nWisconsin.html" title="Wisconsin Local Guides"class="dmcontent_link">Wisconsin</a><br />
&nbsp;<a href="http://local.minyanville.com/Wyoming-nWyoming.html" title="Wyoming Local Guides"class="dmcontent_link">Wyoming</a><br />
&nbsp;<a href="http://local.minyanville.com/" title=" Local Guides"class="dmcontent_link">Local Guides</a><br />
</div>
</div>

<?
}
function getrandnum(){
	return date('Ymd_His').time().rand(1,15);
}

function trade_now_right() {
?>
<div class="right_trade_now_link"><a href="<?=$HTPFX.$HTHOST;?>/articles/tradenow.htm">trade now</a></div>
<?
}

function displayCoopertestimonials() {
?>
<div class="right_common_container">
<div class="right_common_head">
<!--
Change : Link has been removed.
Request: QA
Date: 23 Oct 2009
 -->
<!-- <a href="http://www.invest-store.com/minyanville/cooper.html"><h2>Subscriber Testimonials</h2></a> -->
<h2>Subscriber Testimonials</h2>
</div>
<div class="cooper_testimonial_main">
<span class="cooper_testimonials">First, I must say you are a legend in my book and I have high regards for your work as I have followed you for years. I want to thank you for your brilliant contributions you have made to the trading world and you have certainly had a profound impact on my career as a trader.
<br><i>- D. Hansen</i></br>
</span><br>
<br />
<span class="cooper_testimonials">When I embarked on trading I was not at all sure I could do well enough to make a living at it. Thanks to your suggestions and strategies I no longer have that doubt. My portfolio rose 13.88% in my first five days as a trader and would have risen considerably more if I hadn't tried a few of my own ideas that didn't work out! Henceforth, I'm going to stick to the script and to the ideas provided by your service. If I do, I know I will succeed. That's a good feeling.
<br><i>- Eich</i></br>
</span><br>


<br />
<span class="cooper_testimonials"> Jeff, your commentaries just keep getting better. Today's was superb. I look forward to reading them more than the nightly picks which I make money on. You have a fascinating history.
<br><i>- M. Bradley</i></br>
</span><br>

<br />
<span class="cooper_testimonials">Jeff, I just wanted to send you a quick note to say that you rule. I have subscribed to some market publications in my life, but Minyanville and Daily Market Report is by far, hands down, the best. Keep up the good work! Warm regards,
<br><i>- J. Hopper</i></br>
</span><br>

<br />
</div>
</div>
<?
}

class subViewer{
	function displayProducts(){
	global $lang,$IMG_SERVER; ?>
		<div class="subscriptionProductsUpperContainer" >
		<?	$this->displayBuzzBanter($lang);
			$this->displayCooper($lang);
			$this->displayETF($lang); ?>
		</div>
		<div class="subscriptionProductsLowerContainer" >
		<?	$this->displayStockPlayBook($lang);
			$this->displayFlexFolio($lang);
			$this->displayOptionSmith($lang); ?>
		</div>
	<? }

	function displayBuzzBanter($lang)
	{
		global $IMG_SERVER,$HTPFX,$HTHOST;
	 ?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_buzzNbanter_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['BuzzBanter_desc']; ?>
			</div>
			<div class="productImage">
				<?=$this->getAddtoCartbtnsTrial('BuzzBanter','bttn_start14DayTrial.jpg', 'subscription','subBuzzBanter');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('buzzbanter'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayFlexFolio($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productMiddleConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/flexfolio.png" /></div>
			<div class="productDesc">
				<?=$lang['Flexfolio_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('FlexFolio','bttn_start14DayTrial.jpg', 'subscription','subFlexFolio');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('qphome'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

<?	}

	function displayETF($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_mpMikePaulenoffs_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['etf_desc']; ?>
			</div>
			<div class="productImage">
				<div class="productTrialImage">
                <a href="https://www.mptrader.com/cgi-bin/fbuser?ref=mvp" style="cursor: pointer;"><img border="0" alt="add to cart" src="<?=$IMG_SERVER;?>/images/subscription/bttn_start14DayTrial.jpg"></a>
                </div>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('etf_home'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayGaryk($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_gkets_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['garyk_desc']; ?>
			</div>
			<div class="productImage">
				<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('gary k\'s equity trading setups','startRiskFreeTrial_btn.png', 'subscription','subGaryK');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('garyk'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>
		</div>

	<? }

	function displayOptionSmith($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_optionsmith_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['Optionsmith_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('OptionSmith','startRiskFreeTrial_btn.png', 'subscription','subOptionSmith');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('sshome'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

<?	}

	function displayStockPlayBook($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_StockPlaybook_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['thestockplaybook_desc']; ?>
			</div>
			<div class="productImage">
				<div class="productTrialImage">
			  <?php
			  $param="";
			  if($_SESSION['SID'])
			  {
				      $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
			  }
			  ?>
<a onclick="javascript;" href="http://mvp.minyanville.com/stock-playbook-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=thestockplaybook<?=$param?>">
<img border="0" style="cursor: pointer;" alt="add to cart" src="<?=$IMG_SERVER;?>/images/subscription/bttn_start14DayTrial.jpg"></a>
</div>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('thestockplaybook'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayCooper($lang)
	{
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productMiddleConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/jeffcooper_dailymarketreport.png" /></div>
			<div class="productDesc">
				<?=$lang['JeffCoopet_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('Jeff Cooper','bttn_start14DayTrial.jpg', 'subscription','subCooper');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('cooperhome'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayAdsFree($lang){
		global $IMG_SERVER,$HTPFX,$HTHOST;
		$url  = getpageurl('adfree');
	?>
		<div class="adFreeProductContainer">
		<div class="adFreeTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adFreeTop.jpg" /></div>
		<div class="adFreeLogo">
		<a href="<?=$url['alias'];?>" target="_self">
		<img src="<?=$IMG_SERVER;?>/images/subscription/readad_freeMV.png" /></a></div>
		<div class="adFreeText_bottom">
			<?=$lang['adFree_desc']; ?>
		</div>
		<div class="adFreeTextImage">
			<div class="adFreeText1"><?=$lang['adFree_desc1']; ?></div>
			<?= $this->getAddtoCartbtnsAdFree('Ad Free','ad-free.png', 'subscription','subAdFree');?>
		</div>
		<div class="adFreeBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adfreeBottom.jpg" /></div>
	</div>

	<? }

	function displayTechStrat($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_techstrat_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['techStrat_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('techstrat','bttn_start14DayTrial.jpg', 'subscription','subTechStrat');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('techStrat'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayBuyHedge($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_buyAndHedge_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['buyhedge_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('buyhedge','bttn_start14DayTrial.jpg', 'subscription','subBuyHedge');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('buyhedge'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayHousingReport($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/msp_mvp_kjHousingMarketReport_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['housingmarket_desc']; ?>
			</div>
			<div class="productImage">
			  <?php
			  $param="";
			  if($_SESSION['SID'])
			  {
				       $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
			  }
			  ?>
				<div class="productTrialImage">
                <a href="http://mvp.minyanville.com/housing-market-report-step-2-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=housingmarket/<?=$param?>" style="cursor: pointer;">
                <img border="0" alt="add to cart" src="<?=$IMG_SERVER;?>/images/subscription/bttn_subscribeNow.jpg"></a>
                </div>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('housingMarketReport'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	function displayDiscount($lang){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="offImg"><img src="<?=$IMG_SERVER;?>/images/subscription/getOff.gif" /></div>
			<div class="offDesc">Subscribe to any 2 of the above products on an annual basis and receive 20% off the already low price.</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }

	 function learnMoreSubscription($p){
		  global $HTPFXSSL,$HTPFX,$HTHOST;
		  $str = '';

		  $param="";
		  if($_SESSION['SID']){
			   $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
		  }
		  $inputClass="subRedesignStartTrial";

	 switch ($p){
	 	  case 'peter-tchir' :
			   $url  = getpageurl('peter-tchir');
			   if($_SESSION['peterTchir']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
			   		$str .='<a href="http://mvp.minyanville.com/peter-tchir-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=peterTchir'.$param.'" target="_self">';
			   }
		  break;
		  case 'buzzbanter' :
			   $url  = getpageurl('buzzbanter');
			   $str .='<a href="http://mvp.minyanville.com/buzz-banter-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=buzz'.$param.'" target="_self">';
		  break;
		  case 'cooperhome':
			   $url  = getpageurl('cooperhome');
			   if($_SESSION['Cooper']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=cooper'.$param.'" target="_self">';
			   }
		  break;
		  case 'qphome':
			   $url  = getpageurl('qphome');
			   $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
	      break;
		  case 'sshome':
			   $url  = getpageurl('sshome');
			   if($_SESSION['Optionsmith']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="http://mvp.minyanville.com/optionsmith-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=optionsmith'.$param.'" target="_self">';
			   }
		  break;
		  case 'etf_home':
			   $url  = getpageurl('etf_home');
			   $str .='<a href="https://www.mptrader.com/cgi-bin/fbuser?ref=mvp" target="_self">';
		  break;
		  case 'buyhedge_home':
			   $url  = getpageurl('buyhedge_home');
			   if($_SESSION['buyhedge']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="http://mvp.minyanville.com/buy-hedge-landing-page-subscriptions-page/'.$param.'" target="_self">';
			   }

		  break;
		  case 'thestockplaybook':
			   $url  = getpageurl('thestockplaybook');
			   if($_SESSION['TheStockPlayBook']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="http://mvp.minyanville.com/stock-playbook-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=thestockplaybook'.$param.'" target="_self">';
			   }
		  break;
		  case 'adfree':
			   $url  = getpageurl('adfree');
			   $str .='<a href="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=adsfree'.$param.'" target="_self">';
			   $inputClass = "subRedesignStartAdsFree";
		  break;
		  case 'techStrat':
			   $url  = getpageurl('techstrat_landing');
			   if($_SESSION['TechStrat'])
			   {
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
			    $str .='<a href="http://mvp.minyanville.com/techstrat-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=techstrat'.$param.'" target="_self">';
			   }
		  break;
		  case 'housingMarketReport':
			   $url  = getpageurl('housingMarketReport_landing');
			   $str .='<a href="http://mvp.minyanville.com/housing-market-report-landing-page-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=housingmarket'.$param.'" target="_self">';
		  break;
		  case 'garyk':
			   $url  = getpageurl('garyk_landing');
			   $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
		  break;
	      case 'buyhedge':
			   $url  = getpageurl('buyhedge_landing');
			   if($_SESSION['buyhedge']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="http://mvp.minyanville.com/buy-hedge-landing-page-subscriptions-page/'.$param.'" target="_self">';
			   }
		  break;
		  case 'elliottWave':
			   $url  = getpageurl('elliott-wave');
			   if($_SESSION['ElliottWave']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="
http://mvp.minyanville.com/ewi-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=elliottwave'.$param.'" target="_self">';
			   }
		  break;
		  case 'keene':
			   $url  = getpageurl('keene');
			   if($_SESSION['keene']){
					$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
			   }else{
					$str .='<a href="
http://mvp.minyanville.com/keene-landing-page-navbar/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=keene'.$param.'" target="_self">';
			   }
		  break;
	 }
	$str .='<input name="" type="button" class="'.$inputClass.'"/></a>';
	return $str;
	}

/*	Function render Add To Cart Button with Click event for ADFREE  */
	function getAddtoCartbtnsAdFree($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
		global $HTHOST,$HTPFX,$IMG_SERVER,$HTPFXSSL,$trialUrlArr;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);   //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
					 $id = trim($data['subscription_def_id']);
				$producttype="Monthly";
					if($_SESSION['SID'])
				       {
					      $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
				       }

					$str_mon = '<div class="adFreeImage1">

					<a href="'.$trialUrlArr[$productName].$param.'"  >
					<img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;"/>
					</a>
					<a id="trialhidebtn-'.$id.'" style="display:none;">
					<img  src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" />
					</a>
					</div>';
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getAddtoCartbtnsTrial($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
	    global $HTHOST,$HTPFXSSL,$IMG_SERVER,$trialUrlArr;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);  //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		if($_SESSION['SID'])
		 {
			  $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
		 }
		 if(count($prods)){
			foreach($prods as $data){
				$id = trim($data['subscription_def_id']);
				if(strstr($data['product'], 'Monthly') ){
				$str_mon = '<div class="productTrialImage">
				<a href="'.$trialUrlArr[$productName].$param.'"  id="trialshowbtn-'.$id.'" >
				<img  src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;"/>
				</a>
				<a href="" id="trialhidebtn-'.$id.'" style="display:none;">
				<img  src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" />
				</a>
				</div>';
				}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function getAddtoCartbtnsTrialHousing($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
	    global $HTHOST,$HTPFX,$HTPFXSSL,$IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);  //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				$id = trim($data['subscription_def_id']);
				if(strstr($data['product'], 'Quarterly') ){
				$str_mon = '<div class="productTrialImage">
				<a href="'.$HTPFXSSL.$HTHOST.'/subscription/register" id="trialshowbtn-'.$id.'" onclick="return checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\''.$productName.'\',\''.$producttype.'\',\''.$eventname.'\'); ">
				<img  src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0"  style="cursor:pointer;"/>
				</a>
				<a id="trialhidebtn-'.$id.'" style="display:none;"/>
				<img  src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" >
				</a>
				</div>';
				}
			}
		}
		$str = $str_mon;
		return $str;
	}

	function displayOfferings(){
	global $IMG_SERVER;
	?>
		<div class="servicesContainer">
		<div class="leftOfferImage">
			<img src="<?=$IMG_SERVER;?>/images/subscription/serviceContainerLeft.jpg" border="0" />
		</div>
		<div class="dayTrial">
			<div class="dayTrialImage"><img src="<?=$IMG_SERVER;?>/images/subscription/img_14days.jpg" /></div>
			<div class="dayTrialBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/free_trials.jpg" /></div>
			<div class="dayTrialText">ON MOST OF MINYANVILLE'S SUBSCRIPTION PRODUCTS</div>
		</div>
		<div class="discountContainer">
			<div class="discountImage"><img src="<?=$IMG_SERVER;?>/images/subscription/discountImg.jpg"></div>
			<div class="discountBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/discount.jpg" /></div>
			<div class="dayTrialText">WHEN YOU SUBSCRIBE TO ANY 2 ANNUAL PRODUCTS</div>
		</div>
		<div class="moreSavingsContainer">
			<div class="moreSavingBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/more_savings.jpg" /></div>
			<div class="moreSavingsText">WHEN YOU BUY 3 + ANNUAL PRODUCTS</div>
		</div>
		<div class="rightOfferImage">
			<img src="<?=$IMG_SERVER;?>/images/subscription/serviceContainerRight.jpg" border="0" />
		</div>
		</div>
<?	}

	function displayOfferingsV1(){
	global $IMG_SERVER;
	?>
		<div class="servicesContainer">
		<div class="leftOfferImage">
			<img src="<?=$IMG_SERVER;?>/images/subscription/serviceContainerLeft.jpg" border="0" />
		</div>
		<div class="dayTrialV1">
			<div class="dayTrialImage"><img src="<?=$IMG_SERVER;?>/images/subscription/img_14days.jpg" /></div>
			<div class="dayTrialBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/free_trials.jpg" /></div>
			<div class="dayTrialText">ON MOST OF MINYANVILLE'S SUBSCRIPTION PRODUCTS</div>
		</div>
		<div class="discountContainerV1">
			<div class="discountImage"><img src="<?=$IMG_SERVER;?>/images/subscription/discountImg.jpg"></div>
			<div class="discountBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/discount.jpg" /></div>
			<div class="dayTrialText">WHEN YOU SUBSCRIBE TO ANY 2 ANNUAL PRODUCTS</div>
		</div>
		<div class="moreSavingsContainerV1">
			<div class="moreSavingBoldText"><img src="<?=$IMG_SERVER;?>/images/subscription/more_savings.jpg" /></div>
			<div class="moreSavingsText">WHEN YOU BUY 3 + ANNUAL PRODUCTS</div>
		</div>
		<div class="rightOfferImage">
			<img src="<?=$IMG_SERVER;?>/images/subscription/serviceContainerRight.jpg" border="0" />
		</div>
		</div>
<?	}

	function displayfeaturedProduct(){
	global $IMG_SERVER;
	?>
		<div class="subscriptionFeaturedArea"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_subscriptions_hdr_939x259_073112.jpg" usemap="#featuredArea" border="0" />
		<map name="featuredArea">
			<area shape="poly" href="<?= $this->learnMoreFeatured('buzzbanter'); ?>"  coords="40,91,39,241,137,240,135,99,49,84,38,92,38,92" target="" />
			<area shape="poly" href="<?= $this->learnMoreFeatured('cooperhome'); ?>" coords="192,92,191,240,290,239,289,102,202,84,193,93" target="" />
			<area shape="poly" href="<?= $this->learnMoreFeatured('sshome'); ?>" coords="344,94,346,239,442,238,441,101,354,85,344,94"  target="" />
			<area shape="poly" href="<?= $this->learnMoreFeatured('techstrat'); ?>" coords="499,92,499,240,595,240,594,100,507,85,498,93"  target="" />
			<area shape="poly"  href="<?= $this->learnMoreFeatured('thestockplaybook'); ?>" coords="653,91,650,238,748,239,749,100,662,84,653,92"  target="" />
			<area shape="poly" href="<?= $this->learnMoreFeatured('housingmarket'); ?>"  coords="804,90,804,242,902,242,901,100,814,84,804,92"  target="" />

		</map>
	</div>
<? }

	function displayContactFooter(){ ?>
		<div class="productFooter">
			<div class="productFooter1">
				<div class="productFooterBoldText">&copy;<?=date('Y')?> Minyanville Media, Inc. All Rights Reserved</div>
                <div class="productFooterText">*This offer does not apply to the Ad-Free subscription</div>
			</div>
			<div class="productFooter2">
				<div class="productFooterBoldText">Questions or More Information</div>
				<div class="productFooterText">
					For any inquiries, issues or cancellation please email <a href="mailto:support@minyanville.com" class="productGreenText">support@minyanville.com</a> or call <br><font class="productGreenText">212-991-9357</font>
				</div>
			</div>
			<div class="productFooter3">
				<div class="productFooterBoldText">Customer Support</div>
				<div class="productFooterText">
					<font class="productGreenText">Monday - Friday:</font> 9:00AM - 6:00PM EST<br />
			We normally respond to emails the same business day. If you need immediate assistance please call 212-991-9357.
				</div>
			</div>
		</div>
<? }

	function displayProductsV2(){
	global $lang; ?>
		<div class="allSubscriptionProductContainer" >
			<div class="subscriptionLeftContainer">
				<div class="subscriptionLeftUpperContainer">
					<div class="leftBox"><? $this->displayBuzzBanter($lang); ?> </div>
					<div class="RightBox"><? $this->displayCooperV2($lang); ?> </div>

				</div>

				<div class="subscriptionLeftMiddleContainer">
					<div class="leftCenterBox"><? $this->displayTechStrat($lang); ?> </div>
						<div class="rightCenterBox"><? $this->displayStockPlayBook($lang); ?> </div>
				</div>

				<div class="subscriptionMiddleContainer">
					<div class="leftBox"><? $this->displayHousingReport($lang); ?> </div>
					<div class="leftBox"><? $this->displayDiscount($lang); ?> </div>
				</div>

			</div>
			<div class="subscriptionRightContainer">
			     <div class="leftBox"><? $this->displayOptionSmith($lang); ?></div>
			     <div class="upperBox"><? $this->displayAdsFreeV2($lang); ?> </div>
			</div>
		</div>
<? }

	function displayCooperV2($lang)
	{
	global $IMG_SERVER;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_jcDailyMarketReport_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['JeffCoopet_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('Jeff Cooper','bttn_start14DayTrial.jpg', 'subscription','subCooper');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('cooperhome'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

	<? }
	function displayFlexFolioV2($lang){
	global $IMG_SERVER;
	?>
		<div class="productConatiner">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="productContentContainer">
			<div class="productLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_activeInvestor_hdr.png" /></div>
			<div class="productDesc">
				<?=$lang['Flexfolio_desc']; ?>
			</div>
			<div class="productImage">
				<?= $this->getAddtoCartbtnsTrial('FlexFolio','bttn_start14DayTrial.jpg', 'subscription','subFlexFolio');?>
				<div class="productLearnMoreImage"><?= $this->learnMoreSubscription('qphome'); ?></div>
			</div>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>

<?	}
	function displayAdsFreeV2($lang){
		global $lang,$IMG_SERVER,$HTPFXSSL,$HTHOST,$HTPFX;
		$url  = getpageurl('adfree');

	 ?>
		<div class="rtSidebarAdFreeBkg">
		<div class="productTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
		<div class="adFreeLogo">
	     <?php
	     if($_SESSION['SID'])
		{
		 $param = "?email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
		}
		echo '<a href="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=adsfree'.$param.'" target="_self">';

	     ?>

			  <img src="<?=$IMG_SERVER;?>/images/subscription/mainSubsProd_readadfree.jpg" /></a></div>
		<div class="textDiv">
			<div class="adFreeText"><?=$lang['adFree_desc']; ?></div>
			<div class="textV2"><?=$lang['adFree_desc1']; ?></div>
			<?= $this->getAddtoCartbtnsAdFree('Ad Free','mainSubsProd_yesadfree.jpg', 'subscription','subAdFree');?>
		</div>
		<div class="productBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
	</div>
<? }

	function displayAdsFreeContactFooter(){ ?>
		<div class="adFreeFooter">
			<div class="adFreeFooter1">
				<div class="adFreeFooterBoldText">&copy;2010 Minyanville Media, Inc. All Rights Reserved</div>
			</div>
			<div class="adFreeFooter2">
				<div class="adFreeFooterBoldText">Questions or More Information</div>
				<div class="adFreeFooterText">
					For any inquiries, issues or cancellation please email <font class="adFreeGreenText">support@minyanville.com</font> or call <br><font class="adFreeGreenText">212-991-9357</font>
				</div>
			</div>
			<div class="adFreeFooter3">
				<div class="adFreeFooterBoldText">Customer Support</div>
				<div class="adFreeFooterText">
					<font class="adFreeGreenText">Monday - Friday:</font> 9:00AM - 6:00PM EST<br />
	We normally respond to emails the same business day. If you need immediate assistance please call 212-991-9357.
				</div>
			</div>
	</div>
<? }

	function displayAdsFreeLanding($lang){
	global $lang,$IMG_SERVER;
	 ?>
		<div class="adFreeContent">
		<div class="adFreeTopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adFreeTop.jpg" /></div>
		<div class="adFreeText">
			<div class="adFreeContentText"><?=$lang['adFreelanding_desc']; ?></div>
			<div class="adFreeTextImage">
			<div class="adFreeText1"><?=$lang['adFreelanding_desc1']; ?></div>
			<?= $this->getAddtoCartbtnsAdFree('Ad Free','ad-free.png', 'subscription','subAdFree');?>
			</div>
		</div>
		<div class="adFreeBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adfreeBottom.jpg" /></div>
	</div>
<? }

		function learnMoreFeatured($p){
	             global $HTPFX,$HTHOST;
		$str = '';
		 if($_SESSION['SID'])
		{
		 $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
		}
		switch ($p){
		case 'buzzbanter' :
			$url  = getpageurl('buzzbanter');
			  $str .='http://mvp.minyanville.com/buzz-banter-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=buzz'.$param;

			break;
		case 'etf' :
			$url  = getpageurl('buyhedge_landing');
			if($_SESSION['buyhedge'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/buy-hedge-landing-page-subscriptions-page/'.$param;
			}
			break;
		case 'techstrat':
			$url  = getpageurl('techstrat_landing');
			if($_SESSION['TechStrat'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/techstrat-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=techstrat'.$param;
			}
			break;
		case 'cooperhome':
			$url  = getpageurl('cooperhome');
			if($_SESSION['Cooper'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=cooper'.$param;
			}
			break;
		case 'qphome':
		$url  = getpageurl('qphome');
		$str .=$url['alias'];
			break;
		case 'sshome':
		$url  = getpageurl('sshome');
		if($_SESSION['Optionsmith'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/optionsmith-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=optionsmith'.$param;
			}
			break;
		case 'etf_home':
		$str .='https://www.mptrader.com/cgi-bin/fbuser?ref=mvp';
			break;
	        case 'buyhedge_home':
	               $url  = getpageurl('buyhedge_landing');
			if($_SESSION['buyhedge'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/buy-hedge-landing-page-subscriptions-page/'.$param;
			}
			break;
		case 'thestockplaybook':
			$url  = getpageurl('thestockplaybook');
			if($_SESSION['TheStockPlayBook'])
			{
			  $str .=$HTPFX.$HTHOST.$url['alias'];
			}
			else
			{
			  $str .='http://mvp.minyanville.com/stock-playbook-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=thestockplaybook'.$param;
			}
			break;
		case 'garyk':
			$url  = getpageurl('garyk_landing');
			$str .=$url['alias'];
			break;
		case 'adsfree':
			$url  = getpageurl('adsfree');
			  $str .='http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=adsfree'.$param;

			break;
		case 'housingmarket':
			$url  = getpageurl('housingMarketReport_landing');

			  $str .='http://mvp.minyanville.com/housing-market-report-landing-page-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=housingmarket'.$param;
			break;
	}
	return $str;
	}

}

class newControlPanel{
	function displayLogInDiv($target){
	global $HTPFX,$HTPFXNSSL,$HTPFXSSL,$HTHOST,$_COOKIE,$IMG_SERVER;
	if($target==''){
		$targeturl=$_SERVER['HTTP_REFERER'];
	}else{
		$targeturl = $target;
	}

	?>
		<div id="loginmv" class="logIn">
		<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/newLoginTop.png" /></div>
		<div class="logInContent">
			<div class="logInHeading">Subscriber Login</div>
			<div class="inputBox">

                    <div class="login-error" id="loginErrorMsgs" ></div>
                    <input type="hidden" name="targeturl" id="targeturl" value="<?=$targeturl;?>" />
						<div class="inputLabel">Email Address</div>
						<div class="inputImage"><input type="text" name="luseremailadd" id="luseremailadd" onblur="javascript:chkSpaceNull('luseremailadd',this.value,'Email Address');" class="login_input_box" tabindex=1/></div>
					<?php $forgotpassObj = new forgotPassFancyBoxDesign();
						 echo $forgotpassObj->displayFancyBoxPopup(); ?>
						<div class="inputLabel">Password<span><a href="JavaScript:void(0);" target="_self" onclick="javascript:forgotPassFancyBox();">[ Forgot Password ]</a></span></div>
						<div class="inputImage"> <input type="password" onblur="javascript:chkSpaceNull('luserpassword',this.value,'Password');" onfocus="javascript:checkPasswordField('luserpassword');" class="login_input_box"  name="luserpassword" id="luserpassword" onkeyup="javascript:loginEnterKeyChk(event,'log-in');" tabindex=2 />
                        </div>

					<div class="rememberMe">
                        <div class="remembermeInput"><input type="checkbox" value="checkbox" name="checkbox" id="lautologin" checked="checked" tabindex=3></div>
                        <div class="remembermetext">Remember me</div>
                    </div>
			</div>

			<div class="buttonImage"><img onclick="javascript:iboxLogin('luseremailadd','luserpassword','lautologin','loginErrorMsgs');" src="<?=$IMG_SERVER;?>/images/subscription/bttn_login.jpg" tabindex=4 onkeyup="javascript:loginEnterKeyChk(event,'log-in');" /></div>
		</div>
		<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/newLoginBottom.png" /></div>
	</div>
<?	}

	function displayProfileDiv(){
	global $HTPFX,$HTPFXNSSL,$HTHOST,$IMG_SERVER;
	$objVia=new Via();
	?>
	<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/loginTop.jpg" /></div>
	<div class="logInContent">
		<div class="logInHeading"><font class="boldText">Create a Profile</font><font class="plainText"> or <a href="<?=$HTPFXNSSL.$HTHOST?>/subscription/register/login.htm"><u>Log in</u></a></font>
		</div>
        <div class="register_error" id="regerror"></div>
		<div class="inputBoxProfile">
				<div class="inputField">
				<div class="inputLabel">First Name :</div>
				<div class="inputImageProfile">
                <input type="text" onmouseover="void(this.title=this.value)" title="" id="firstname" onclick="void(this.value=wordsonly(this.value))" onkeypress="get_register_keys(event);" class="register_common_input" value="" name="firstname" maxlength="255" size="20">
                </div>
				</div>

				<div class="inputField">
				<div class="inputLabel">Last Name :</div>
				<div class="inputImageProfile"><input type="text" onmouseover="void(this.title=this.value)" title="" id="lastname" onclick="void(this.value=wordsonly(this.value))" onkeypress="get_register_keys(event);" class="register_common_input" value="" name="lastname" maxlength="255" size="20">
                </div>
				</div>

				<div class="inputField">
				<div class="inputLabel">Email Address :</div>
				<div class="inputImageProfile"><input type="text" onmouseover="void(this.title=this.value)" title="" id="viauserid" onclick="void(this.value=email_format(this.value))" onkeypress="get_register_keys(event);" class="register_common_input" value="" name="viauserid" maxlength="255" size="20"></div>
				</div>

				<div class="inputField">
				<div class="inputLabel">Password :</div>
				<div class="inputImageProfile"><input type="password" id="viapass" onkeypress="get_register_keys(event);" class="register_common_input" value="" name="viapass" maxlength="255" size="20"></div>
				</div>

				<div class="inputField">
				<div class="inputLabel">Retype Password :</div>
				<div class="inputImageProfile"><input type="password" id="viarepass" onkeypress="get_register_keys(event);" class="register_common_input" value="" name="viarepass" maxlength="255" size="20"></div>
				</div>

				<div class="inputField">
					<div class="inputLabel">Country :</div>
					<div class="inputImageCountry" id="countrySelect"  onclick="javascript:openoptiondiv('country');">USA</div>
					<div id="country" class="countryDropdown" style="display: none;">
							<?php echo $objVia->populateCountryProfile(); ?>
					</div>
                </div>

				<div class="radioBox">
                <div class="receive-newseletter">Yes, I'd like to receive:</div>
					<div class="checkboxDiv">
                        <div class="checkboxinput"><input type="checkbox" id="dailydigest" name="dailydigest" checked="checked" /></div>
                        <div class="newslttrcat">Minyanville Daily Digest - The complete list of all articles published on Minyanville each day</div>
                    </div>
					<div class="checkboxDiv">
                        <div class="checkboxinput"><input type="checkbox" id="dailyfeed" name="dailyfeed" checked="checked" /></div>
                        <div class="newslttrcat">The Daily Feed - News around the web you may have missed, with a unique spin.</div>
                    </div>
				</div>
                <div style="clear:both;"></div>
				<div class="mvpolicy">By clicking the button below, you agree to the <a href="<?=$HTPFXNSSL.$HTHOST?>/company/substerms.htm" target="_blank">Terms of Services</a> and <a href="<?=$HTPFXNSSL.$HTHOST?>/company/privacy.htm" target="_blank">Privacy Policy</a></div>
		</div>
		<div class="buttonImage"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_signup.jpg" onclick="javascript:registration_validate('loginErrorMsgs');" /></div>
	</div>
	<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/loginBottom.jpg" /></div>
<?	}

	function displayFacebookDiv(){
	global $HTPFX,$HTHOST,$IMG_SERVER;
	?>
		<div class="facebook">
			<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/newLoginTop.png" /></div>
			<div class="logInContent">
			   <div class="logInHeading">Comment with Facebook</div>
			   <input type="hidden" name="targeturl" id="targeturl" value="<?=$_SERVER['HTTP_REFERER'];?>" />
			   <div class="fbContent">
					<div id="fberror" class="fbError"></div>
					<div class="fbFont">You must login with your facebook account to comment on articles.</div>
					<div class="fbButton">
						    <fb:login-button autologoutlink="false"  perms="email,user_birthday,status_update,publish_stream" onlogin="login();" ></fb:login-button>
					</div>
			   </div>
			</div>
			<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/newLoginBottom.png" /></div>
		</div>
<?	}

	function displayMinyanvilleIntro(){
	global $HTPFX,$HTHOST,$IMG_SERVER;
	?>
		<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/loginTop.jpg" /></div>
		<div class="logInContent">
			<div class="logInHeading"><font class="boldText">Why Sign Up to Minyanville?</font></div>
		<div class="mvilBold"><b>Minyanville is a collective platform where people who seek useful, unbiased information come to learn, laugh and connect.</b></div>
		<div class="mvilDesc">
		By signing up for the free site, users will immediately raise their level of financial awareness by immersing within a community of highly insightful professionals who deliver astute financial commentary comfortably laced with both humor and humanity. <br />
		<br />
		Join today and embolden your confidence to navigate the business and financial markets assertively and confidently.<br />
		<br /></div>
		</div>
		<div class="loginTopBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/loginBottom.jpg" /></div>
<?	}

	function displaySubscriptionBox(){
		global $HTPFX,$HTHOST,$IMG_SERVER,$trialUrlCpArr;
		$buzzLaunchUrl=buzzAppUrlEncryption();
	?>
		<div class="subscriptionBox">
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
			<?php
			if($_SESSION['SID'])
			  {
			   $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
			  }
			  ?>
			<div class="boxContent">
				<div class="boxContentHeading">Subscription</div>
				<div class="boxContentOptions">
					<div class="optionFont">Buzz & Banter</div>
					<? if($_SESSION['Buzz']){ ?>
					<div class="optionImage"><a style="cursor:pointer;" onClick="window.open('<?=$buzzLaunchUrl?>',
  'Banter','width=350,height=708,resizable=yes,toolbar=no,scrollbars=no'); banterWindow.focus();"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
  					<? }else{


						   echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subBuzzBanter].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
					//echo $this->getAddtoCartNewCP('buzzbanter','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>
				
				<div class="boxContentOptions">
					<div class="optionFont">Keene On Options</div>
					<? if($_SESSION['keene']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/keene/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						        echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subKeene].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('TechStrat','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>
				
				<div class="boxContentOptions">
					<div class="optionFont">Elliott Wave Insider</div>
					<? if($_SESSION['ElliotWave']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/ewi/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						        echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subElliottWave].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('TechStrat','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">TechStrat Report</div>
					<? if($_SESSION['TechStrat']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/techstrat/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						        echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subTechStrat].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('TechStrat','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>

                <div class="boxContentOptions">
					<div class="optionFont">Tchir's Fixed Income Report</div>
					<? if($_SESSION['peterTchir']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/tchir-fixed-income/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						       echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subPeterTchir].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('TheStockPlayBook','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>
				<div class="boxContentOptions">
					<div class="optionFont">Daily Market Report</div>
					<? if($_SESSION['Cooper']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/cooper/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						           echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subCooper].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('Jeff Cooper','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">OptionSmith</div>
					<? if($_SESSION['Optionsmith']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/optionsmith/index.htm"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						     echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subOptionSmith].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
					 //echo $this->getAddtoCartNewCP('optionsmith','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">The Stock Play Book</div>
					<? if($_SESSION['TheStockPlayBook'] || $_SESSION['TheStockPlayBookPremium']){ ?>
						<div class="optionImage"><a href="<?=$HTPFX.$HTHOST?>/thestockplaybook/"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_launch.jpg" /></a></div>
					<? }else {
						       echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subStockPlayBook].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCP('TheStockPlayBook','bttn_getfreetrial.jpg','SUBSCRIPTION');
					} ?>
				</div>

                <div class="boxContentOptions">
					<div class="optionFont">Ad Free Minyanville</div>
					<? if($_SESSION['AdsFree']){ ?>
						<div class="optionImage"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_getfreetrial_disabled.jpg" /></div>
					<? }else {
						    echo  '<div class="optionImage"><a   href="'.$trialUrlCpArr[subAdFree].$param.'" style="cursor:pointer;"/> <img src="'.$IMG_SERVER.'/images/subscription/bttn_getfreetrial.jpg" alt="add to cart" border="0" /> </a> </div>';
						//echo $this->getAddtoCartNewCPAdsFree('Ad Free','bttn_getitnow.jpg','SUBSCRIPTION');
					} ?>
				</div>

				<div class="optionManage"><a href="<?=$HTPFX.$HTHOST?>/subscription/register/manage.htm">Manage Subscription Settings</a></div>

			</div>
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
		</div>
<?  }

	function displayExchangeBox(){
	global $page_config,$_SESSION,$IMG_SERVER; ?>
		<div class="exchangeBox">
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
			<div class="boxContent">
				<div class="boxContentHeading">Exchange</div>
				<div class="boxContentOptions">
					<div class="optionFont">Blogs</div>
					<div class="optionImage"><a href="<?=$page_config['blog']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>
				<div class="boxContentOptions">
					<div class="optionFont">Discussions <? if($countDiscussion) echo "(".$countDiscussion.")"; ?></div>
					<div class="optionImage"><a href="<?=$page_config['main_discussion']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">Updates <? if($countUpdates)echo "(".$countUpdates.")"; ?></div>
					<div class="optionImage"><a href="<?=$page_config['home']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">Requests <? if($countRequest)echo "(".$countRequest.")"; ?></div>
					<div class="optionImage"><a href="<?=$page_config['home']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">Inbox <? if($countMsg)echo "(".$countMsg.")"; ?></div>
					<div class="optionImage"><a href="<?=$page_config['inbox']['URL'];?>?userid=<?=$_SESSION['SID']?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">Friends <? if($countFriends)echo "(".$countFriends.")"; ?></div>
					<div class="optionImage"><a href="<?=$page_config['friends']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="boxContentOptions">
					<div class="optionFont">Profile</div>
					<div class="optionImage"><a href="<?=$page_config['profile']['URL'];?>"><img src="<?=$IMG_SERVER;?>/images/subscription/bttn_go.jpg" /></a></div>
				</div>

				<div class="optionManageExchange"><a href="<?=$page_config['privacy']['URL']?>">Manage Exchange Settings</a></div>

			</div>
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
		</div>
<?	}

	function displayWatchListBox($userid){
		global $watchlistlimit,$page_config,$lang,$watchlistmsg,$IMG_SERVER;
		$countst=getWatchlistTickerCount($userid);
		$countticker=$countst[count];
	 ?>
		<div class="watchListBox">
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productTop.jpg" /></div>
			<div class="boxContent">
				<div class="boxContentHeading">Watch List</div>
				 <? if($countticker)
		   			{
		   				watchlistdata($userid,$countticker);

		   			}else{ ?>
				<div class="boxContentOptions" style="height:32px;"><?=$watchlistmsg;?></div>
				<div class="boxContentOptions" style="height:32px;">&nbsp;</div>
				<div class="boxContentOptions" style="height:32px;">&nbsp;</div>
				<div class="boxContentOptions" style="height:32px;">&nbsp;</div>
				<div class="boxContentOptions" style="height:32px;">&nbsp;</div>
				<div class="boxContentOptions" style="height:32px;">&nbsp;</div>
				<? } ?>
				<div style="clear:both;"></div>
				<div class="optionManageWatchList"><a href="<?=$page_config['profile']['URL'];?>">Manage Watch List</a></div>

			</div>
			<div class="topBottomImage"><img src="<?=$IMG_SERVER;?>/images/subscription/productBottom.jpg" /></div>
		</div>

<?	}

	function displayNewsletterBox($subscriber_id,$authornotstr){
	  global $IMG_SERVER,$D_R;
	  include_once("$D_R/lib/email_alert/_lib.php");

		$categoryqry="select category_ids from email_alert_categorysubscribe where subscriber_id='$subscriber_id'";
		$categoryid=exec_query($categoryqry,1);
		$strcat=substr($categoryid['category_ids'],0,-1);
		$categorystr=substr($strcat,1);
		$categoryids = explode(",",$categorystr);

		$qrydigest="select recv_daily_gazette,recv_promo from subscription where id=$subscriber_id";
		$digest=exec_query($qrydigest,1);
		$digestpromo=$digest;

		$qry="select id,title,frequency,description from email_categories where visible='1'";
		$nwslrtResult=exec_query($qry);
	?>
    <form id="form1" name="form1" method="post" action="controlPanel_mod.php">
    <?
		$this->displaySaveButton();
	?>

	<div class="newsletterBox">
		<div class="newslettertopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adFreeTop.jpg" /></div>

		<div class="nwsltrBoxContent">
			<div class="boxContentHeading">My Newsletters</div>
			<!--Left-->
			<div class="innerNewsletterContainer">
				<div class="nwsltrGreenText">Daily</div>
				<div class="newsletterOptions">
					<div class="nwsltrDrop">
					<select id="digest" name="digest">
				    <option value="On" <? if($digest['recv_daily_gazette']=='1'){echo "selected";} ?>>On</option>
					<option value="Off" <? if($digest['recv_daily_gazette']=='0'){echo "selected";} ?>>Off</option>
					</select>
					</div>
					<div class="nwsltrHeading">Minyanville Digest</div>
					<div class="mwsltrDesc">An end of day synopsis of all articles published on minyanville.com	for the day.</div>
				</div>

				<? foreach($nwslrtResult as $row){
						if($row['frequency'] == "daily"){
							if(in_array($row['id'],$categoryids)){
								$selected="On";
							}else {
								$selected="Off";
							}
				?>
				<div class="newsletterOptions">
					<div class="nwsltrDrop">
				  <select id="category_daily" name="category[<?=$row['id']?>]">
                    <option value="on" <? if($selected=="On"){echo "selected";} ?>>On</option>
					<option value="off" <? if($selected=="Off"){echo "selected";} ?>>Off</option>
					</select>
					</div>
					<div class="nwsltrHeading"><!--Five Things You Need To Know--><?=$row['title'];?></div>
					<div class="mwsltrDesc"><!--An in-depth look at issues affecting financial markets in Kevin Depew's trademark intelligent and witty manner.--><?=$row['description'];?></div>
				</div>
				<?	}
				 } ?>

				<div class="newsletterOptions">
					<div class="nwsltrDrop">
					<select id="recv_promo" name="recv_promo">
						<option value="On" <? if($digestpromo['recv_promo']=='1'){echo "selected";} ?>>On</option>
						<option value="Off" <? if($digestpromo['recv_promo']=='0'){echo "selected";} ?>>Off</option>
					</select>
					</div>
					<div class="nwsltrHeading">Minyanville News and Events</div>
					<div class="mwsltrDesc">From time to time, Minyanville may send you emails to keep you informed about what's going on in Minyanville, including new products and services, featured stories, changes to our website, Todd Harrison appearences and more..</div>
			</div>


			<div class="nwsltrGreenText">Topics</div>
				<div class="newsletterOptions">
					<div class="nwsltrDrop">
					<select id="custom" name="custom" onchange="settopicbox(this.value)">
						<option value="true">ON</option>
						<option value="">OFF</option>
					</select>
					</div>
			     </div>

				<?
				$sectionqry="select section_ids from email_alert_sectionsubscribe where subscriber_id=$subscriber_id";
				$sectionid=exec_query($sectionqry,1);
				$strsec=substr($sectionid['section_ids'],0,-1);
				$sectionstr=substr($strsec,1);
				$qrysec="select section_id,name from section where section_id in($sectionstr)";

				?>
				<div class="subscribeAuthor">
				<?
				$i=0;
				$j=0;
				$k=0;
				$displaysectionarr=array();
				foreach(exec_query($qrysec) as $row) {
					$displaysectionarr[$row['section_id']]=$row['name'];
				}
				$totrec=count($displaysectionarr);
				$onlyindex=array();
				$onlyindex=array_keys($displaysectionarr);
				if($totrec%3){
					// $totalrows=(($totrec-1)/3)+$totrec%3;
					$totalrows=round((($totrec-1)/3)+1);
				}else{
					$totalrows=(($totrec)/3)+$totrec%3;
				}

				if($totrec=='1'){ ?>
				<div class="subscribeAuthorData" id="topic_checkbox">
				<input style="border:0px;" type="checkbox" name="topic[<?=$onlyindex[$j];?>]" class="topic_select" id="topic" value="<?=$onlyindex[$j];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displaysectionarr[$onlyindex[$j]]; ?></div>
				  </div>
				<? }else{
				for($i=0;$i<$totalrows;$i++){
					$j=$j+$i;
              	if($displaysectionarr[$onlyindex[$j]]){
			  	?>
				<div class="subscribeAuthorData">
				<input style="border:0px;" type="checkbox" name="topic[<?=$onlyindex[$j];?>]" class="topic_select" id="topic" value="<?=$onlyindex[$j];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displaysectionarr[$onlyindex[$j]]; ?></div></div>

				  <? }
				   if($displaysectionarr[$onlyindex[$j+1]]){
				   ?>
				   <div class="subscribeAuthorData">
				  <input style="border:0px;" type="checkbox" name="topic[<?=$onlyindex[$j+1];?>]" class="topic_select" id="topic" value="<?=$onlyindex[$j+1];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displaysectionarr[$onlyindex[$j+1]]; ?></div></div>
				  <? }
				 if($displaysectionarr[$onlyindex[$j+2]]){
				  ?>
				  <div class="subscribeAuthorData">
				  <input style="border:0px;" type="checkbox" name="topic[<?=$onlyindex[$j+2];?>]" class="topic_select" id="topic" value="<?=$onlyindex[$j+2];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displaysectionarr[$onlyindex[$j+2]]; ?><? $j=$i+2+$k;?></div></div>
				  <?  }
				  $k=$k+1;
				   ?>
		<?  } }?>
				</div>

			<div class="subscribeAuthor">
					<div class="selectAuthorText">
					<font style="white-space:nowrap"><b>Ctrl+Click to select multiple topics</b></font><br />
					</div>





                   <?php
				   if($sectionstr)
				     {
					   $query="and section_id not in(".$sectionstr.")";
					 }
				   $qrySection="select * from section where is_active='1' and type='subsection' and subsection_type='article' ".$query;
				   $resSection=exec_query($qrySection);

                   ?>
					<div class="selectAuthorList">
						<select name="topics[]" multiple align="absmiddle" size="8" style="width:200px;">
						<? selectHashArr($resSection,"section_id","name");?>
						</select>
					</div>
				</div>




			</div>
			<!--Right-->
			<div class="innerNewsletterContainer">
				<div class="nwsltrGreenText">Weekly</div>
				<? foreach($nwslrtResult as $row){
						if($row['frequency'] == "weekly"){
							if(in_array($row['id'],$categoryids)){
								$selected="On";
							}else {
								$selected="Off";
							}
				?>
				<div class="newsletterOptions">
					<div class="nwsltrDrop">
				  	<select id="category_weekly" name="category[<?=$row['id']?>]">
						<option value="on" <? if($selected=="On"){echo "selected";} ?>>On</option>
						<option value="off" <? if($selected=="Off"){echo "selected";} ?>>Off</option>
					</select>
					</div>
					<div class="nwsltrHeading"><!--Trading Radar--><?= $row['title'] ?></div>
					<div class="mwsltrDesc"><!--Your roadmap to all the events that will affect financial markets in the week ahead.--><?=$row['description'];?></div>
				</div>
				<? 		}
					} ?>

				<!--<div class="nwsltrGreenText">Monthly</div>-->
				<? foreach($nwslrtResult as $row){
						if($row['frequency'] == "monthly"){
							if(in_array($row['id'],$categoryids)){
								$selected="On";
							}else {
								$selected="Off";
							}
				?>

				<div class="newsletterOptions">
					<div class="nwsltrDrop">
				  	<select id="category_monthly" name="category[<?=$row['id']?>]">
                    	<option value="on" <? if($selected=="On"){echo "selected";} ?>>On</option>
						<option value="off" <? if($selected=="Off"){echo "selected";} ?>>Off</option>
					</select>
					</div>
						<div class="nwsltrHeading"><!--Retail Round-Up--><?= $row['title'] ?></div>
					<div class="mwsltrDesc"><?=$row['description'];?><!--Minyanville seniore columnist and CNBC Fast Mony anchor,
Jeff Macke. provides his insight, wit and analysis on the retail sector's Monthly Same Store Sales Report.-->
</div>
				</div>
				<? 		}
				} ?>
				<div class="nwsltrGreenText">Custom Alerts</div>
				<div class="newsletterOptions">
					<div class="nwsltrDrop">
					<select id="custom" name="custom">
						<option value="on">ON</option>
						<option value="off">OFF</option>
					</select>
					</div>
					<div class="nwsltrHeading" style="color:#103c61;">Custom Professor Articles</div>
					<div class="mwsltrDesc" style="color:#103c61;">Select the Professors whose articles you'd like to receive via email the second they're published</div>
				</div>
				<?
				$authorqry="select author_id from email_alert_authorsubscribe where subscriber_id=$subscriber_id";
				$authorid=exec_query($authorqry,1);
				$strauth=substr($authorid['author_id'],0,-1);
				$authorstr=substr($strauth,1);
				$qrychk="select id,name from contributors where id in($authorstr)";

				?>
				<div class="subscribeAuthor">
				<?
				$i=0;
				$j=0;
				$k=0;
				$displayauthorarr=array();
				foreach(exec_query($qrychk) as $row) {
					$displayauthorarr[$row['id']]=$row['name'];
				}
				$totrec=count($displayauthorarr);
				$onlyindex=array();
				$onlyindex=array_keys($displayauthorarr);
				if($totrec%3){
					// $totalrows=(($totrec-1)/3)+$totrec%3;
					$totalrows=round((($totrec-1)/3)+1);
				}else{
					$totalrows=(($totrec)/3)+$totrec%3;
				}

				if($totrec=='1'){ ?>
				<div class="subscribeAuthorData">
				<input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j];?>]" id="author1" value="<?=$onlyindex[$j];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displayauthorarr[$onlyindex[$j]]; ?></div>
				  </div>
				<? }else{
				for($i=0;$i<$totalrows;$i++){
					$j=$j+$i;
              	if($displayauthorarr[$onlyindex[$j]]){
			  	?>
				<div class="subscribeAuthorData">
				<input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j]?>]" id="author1[]" value="<?=$onlyindex[$j];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displayauthorarr[$onlyindex[$j]]; ?></div></div>

				  <? }
				   if($displayauthorarr[$onlyindex[$j+1]]){
				   ?>
				   <div class="subscribeAuthorData">
				  <input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j+1];?>]" id="author1[]" value="<?=$onlyindex[$j+1];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displayauthorarr[$onlyindex[$j+1]]; ?></div></div>
				  <? }
				 if($displayauthorarr[$onlyindex[$j+2]]){
				  ?>
				  <div class="subscribeAuthorData">
				  <input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j+2];?>]" id="author1[]" value="<?=$onlyindex[$j+2];?>"checked="checked">
				  <div class="subscribeAuthorName"><?=$displayauthorarr[$onlyindex[$j+2]]; ?><? $j=$i+2+$k;?></div></div>
				  <?  }
				  $k=$k+1;
				   ?>
		<?  } }?>
				</div>
				<? $author_list=getContributorsArray($authorstr,$authornotstr);
				 ?>
				<div class="subscribeAuthor">
					<div class="selectAuthorText">
					<font style="white-space:nowrap"><b>Ctrl+Click to select multiple Professors</b></font><br />Professors are active professionals in financial markets. They are full-time traders and investors, not full time writers, who offer intraday observations, thoughts and ideas as the markets move.
					</div>

					<div class="selectAuthorList">
						<select name="authors[]" multiple align="absmiddle" size="8" style="width:200px;">
						<? selectHashArr($author_list,"id","name");?>
						</select>
					</div>
				</div>

			</div>
				<div class="nwsltrSaveButton"><input type="image" src="<?=$IMG_SERVER?>/images/subscription/bttn_save.jpg" align="right" onclick="form1.submit();" alt="Save" /></div>
		</div>
		<div class="newslettertopImage"><img src="<?=$IMG_SERVER;?>/images/subscription/adfreeBottom.jpg" /></div>
        </form>
	</div>
<?	}

	function displayWelcomeText(){?>
		<div class="welcome">
		  <div class="welcomeText">Welcome <?= $_SESSION['nameFirst']; ?>!</div>
		  <div class="logOut"><a id="<?=$_SESSION['user_id']?>" target="_self" href="<?=$HTPFX.$HTHOST?>/subscription/register/loginAjax.php?type=logout">Log out</a> </div>
    	</div>
<?	}

	function displaySaveButton(){
	global $HTPFX,$HTHOST; ?>
	<div class="saveButton">
   <!-- <div class="saveButtonImage"><input type="image" src="<?=$IMG_SERVER?>/images/subscription/bttn_save.jpg" alt="Save" align="right" onclick="form1.submit();"/></div>-->
		<div class="saveButtonText"><a href="<?=$HTPFX.$HTHOST?>/subscription/register/manage.htm">Manage Account Settings</a></div>

	</div>
<?	}

	function displaySettingsDiv(){
		$userid=$_SESSION['SID'];
		$objaddrequest=new friends();
		$objThread=new Thread();
		$countRequest = $objaddrequest->count_pending_request($userid);
		$countMsg=$objThread->count_msg($userid);
		$countUpdates=$objThread->countUpdates($userid);
		$getfriendscount=$objaddrequest->get_friend_list_count($userid);
		$countFriends=$getfriendscount[0][count];
		$ownDiscussions=$objThread->show_discussions($userid,'own',"");
		$countDiscussion=count($ownDiscussions);


	 ?>
	<div class="settingsBox">
		<? $this->displaySubscriptionBox();
		// $this->displayExchangeBox($countDiscussion, $countRequest, $countMsg, $countUpdates, $countFriends);
		$this->displayWatchListBox($userid); ?>
	</div>
<?	}

	function getAddtoCartNewCP($pName,$img, $orderItemType){
       global $IMG_SERVER, $trialUrlArr;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType); //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		if(count($prods)){

			foreach($prods as $data){
				 $id = trim($data['subscription_def_id']);
				 if(strstr($data['product'], 'Monthly') ){

					$str_mon = '<div class="optionImage"><a  href="'.$trialUrlArr[$product_name].'" ><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart"  border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\'sub'.$pName.'\',\'Monthly\',\'Control Panel\');" style="cursor:pointer;"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" style="display:none;"/> </a>
					</div>';

			}
		}
		}
		$str = $str_mon;
		return $str;
	}

	function getAddtoCartNewCPHousing($pName,$img, $orderItemType){
       global $IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType); //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		if(count($prods)){

			foreach($prods as $data){
				 $id = trim($data['subscription_def_id']);
				 if(strstr($data['product'], 'Quarterly')){
					$str_mon = '<div class="optionImage"><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\',\'sub'.$pName.'\',\'Monthly\',\'Control Panel\');" style="cursor:pointer;"/>
					<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
					</div>';

			}
		}
		}
		$str = $str_mon;
		return $str;
	}

	function getAddtoCartNewCPAdsFree($pName,$img, $orderItemType){
        global $IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType); //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		if(count($prods)){

			foreach($prods as $data){
				 $id = trim($data['subscription_def_id']);
				 if($data['product']){
					$str_mon = '<div class="optionImage"><a style="cursor:pointer;" onclick=" checkcart (\''.$id .'\',\''.$data['oc_id'].'\',\''.$data['orderItemType'].'\');";><img src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" /></a></div>';

			}
		}
		}
		$str = $str_mon;
		return $str;
	}

}

function displayCartMsg($viacart, $validatecart,$getpcval,$redirectCart){
	global $pageName, $promoCodeSourceCodeNoFreeTrial,$viaPromoProductName,$viaNotShowCartMessage;
	build_lang('subscription_product');
	global $lang;
	$str = '';
	$pcount  = 0;
	$updatedmsg ='';
	$total=  0.00;
	$divider = true;
	$_SESSION['viaServiceError'] = false;
	//getsourcecodeid
	$ids = null;
	// make discount calculations & net price
	$viacart = getPayableProds($ids, $viacart);
	$netpayable = $_SESSION['netPrice'];
	$viacart = updCartToViaSubscriptionId($viacart);
	if(count($viacart['SUBSCRIPTION']) || count($viacart['PRODUCT'])  ){
		foreach($viacart as $orderTypeArr){
			if(count($orderTypeArr)>0){
				foreach( $orderTypeArr as $data){
					if(count($data)>0){
						//check if validate cart array is verified
						if(count($validatecart)>0){
							$action = array();
							if(is_array($validatecart[$data['orderItemType']])){
							$ocID=$data['oc_id'];
							$action = getcartaction($validatecart[$data['orderItemType']][$data['subscription_def_id']],$ocID);//get case related cart validation action
							$str.=$action['msg'];
							}
						}
					}
				}
			}
		}
	}
	return $str;
}


function getYourCartActionMsg($arr,$ocID=NULL){
		build_lang('subscription_product');
		global $lang,$viaProductsName,$viaOrderClassId;
		if(array_key_exists($ocID,$viaProductsName)){
			$prodName=$viaProductsName[$ocID];
		}
		$msg = '';
		$reset_cart_item = false;

		$case = $arr['case'];
		if(count($arr)){
		foreach($arr as $key=>$val){
				if($key!='case' && $key!='orderItemType'){
				$frm = $val;
				$to  = $key;
			}
		}
		}
		$id = $frm."_".$to;
		$promoCode = $_SESSION['promoCodeValue'];

		$pfrm = getProdSubPriceVal($frm, $arr['orderItemType']);
		$pto = getProdSubPriceVal($to, $arr['orderItemType']);
		switch ($case) {

			case 'trial_not_allowed' :
			        $_SESSION['ShowOkfreeTrial']=0;
				if($_SESSION['ShowOkfreeTrial']!='1'){

					$msg = '<div id="'.$id.'"><div class="add_to_cart_dis" style="margin-bottom:5px;">';
					$msg .= 'You have previously received a free trial to '.$prodName.'. Now the charge will immediately process because you are not eligible for another trial.';
					//$msg .= getInlineConfirmAndSetSess($id);
					$msg .= '</div></div>';
				}
				if(in_array($ocID,$viaOrderClassId)){
					$msg ='';
				}
			break;

			case 'trial_already_in_via':
				$msg = '<div id="'.$id.'"><div class="add_to_cart_dis">';
				if($case!='call_cancel_subscription'){
				$msg .= cartiboxmsg($lang[$case], $pfrm['0']['product'], $pto['0']['product'], $id);
				}
				$from .= cartiboxmsg($pfrm['0']['product']);
				if ($case!='adfree_remove_cart'){
				if($frm==$to){
					$msg  .= 'Do you want to update subscription to '.$pto['0']['product'].'';
				}else{
					if($case == 'trial_already_in_via'){
						//$msg .=' '.'Would you like to initiate your paid subscription now?';
						$msg .=' '.'The charge will immediately process because you are not eligible for another trial.';
					}elseif($case=='counter_billed_already_in_via'){
						$msg .=' '.'Would you like to upgrade to an '.$pto['0']['product'].' account? It will take effect at the end of your current month.';
					}else{
						$msg  .= 'Do you want to update subscription from '.$pfrm['0']['product'].' to '.$pto['0']['product'].'';
					}
				}
				}
				$msg  .="</div><div style='margin-top:5px;float: left;margin-bottom: 10px;margin-top: 5px;width: 100%;'>";
				if($case =='trial_already_in_via'){
				  //$msg .= getInlineConfirmRemoveAndHide($to,$frm, 'confirm', $id, $case,$pfrm['0']['orderItemType']);
				}elseif($case!='adfree_remove_cart' ){
				 $msg .= getinlineconfirmation($to,$frm, 'confirm', $id, $case,$pfrm['0']['orderItemType']);
				}else{
				///$msg .= getInlineAdsFreeConfirmation($frm,$id,$pfrm['0']['orderItemType']);
				}
				$msg .='</div></div>';

				if($case =='trial_already_in_via' && (in_array($ocID,$viaOrderClassId))){
					$msg ='';
				}
			break;
		}
        $status = array('msg'=>$msg , 'reset_cart_item'=>$reset_cart_item);
		return $status ;
	}

	function getFreeTrailProductDetail($pName,$orderItemType){
		global $viaProducts;
		$str ="";
		$pName = strtolower($pName);
		switch ($pName){
			case 'buzzbanter':
			 	$str = $viaProducts['BuzzMonthly']['typeSpecificId'];
				break;
			case 'jeffcooper':
				$str = $viaProducts['CooperMonthly']['typeSpecificId'];
				break;
			case 'flexfolio':
				$str = $viaProducts['FlexfolioMonthly']['typeSpecificId'];
				break;
			case 'optionsmith':
				$str = $viaProducts['OptionsmithMonthly']['typeSpecificId'];
				break;
			case "etf":
				$str = $viaProducts['ETFMonthly']['typeSpecificId'];
				break;
			case "thestockplaybookmonthly":
				$str = $viaProducts['TheStockPlaybookMonthly']['typeSpecificId'];
				break;
			case "thestockplaybookquart":
				$str = $viaProducts['TheStockPlaybookQuart']['typeSpecificId'];
				break;
			case "thestockplaybookannual":
				$str = $viaProducts['TheStockPlaybookAnnual']['typeSpecificId'];
				break;
			case "thestockplaybookpremiummonthly":
				$str = $viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'];
				break;
            case "thestockplaybookpremiumquart":
				$str = $viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'];
				break;
			case "thestockplaybookpremiumannual":
				$str = $viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'];
				break;
			case "ad free":
				$str = $viaProducts['AdFreeMonthly']['typeSpecificId'];
				break;
			case "techstrat":
				$str = $viaProducts['TechStratMonthly']['typeSpecificId'];
				break;
			case "garyk":
				$str = $viaProducts['BuyHedgeMonthly']['typeSpecificId'];
				break;
			case "buyhedge":
				$str = $viaProducts['BuyHedgeMonthly']['typeSpecificId'];
				break;
			case 'default':
				$str = "";
				break;
		}


		$arProductDetails = getProdSubPriceVal($str, $orderItemType);
		return $arProductDetails[0];
	}

	function getAddtoCartBuzzBanterTrial($pName, $img, $orderItemType,$productName="",$eventname="Subscription"){
	    global $HTHOST,$HTPFX,$IMG_SERVER;
		$subdefids = getprodsubdefid($pName,true);
		$prods= getProdSubPriceVal($subdefids, $orderItemType);
		$str = '';
		$subdefids = str_replace("'","",$subdefids);
		$arr = explode(",",$subdefids);
		$prodstatus = getProdViaStatus($arr, $orderItemType);  //check wether given sub_def_id or its counter is already in Via Prods
		$orderItemType = strtoupper($orderItemType);
		 if(count($prods)){
			foreach($prods as $data){
				$id = trim($data['subscription_def_id']);
				if(strstr($data['product'], 'Monthly') ){
				$str_mon = '<div class="productTrialImage"><img id="trialshowbtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" style="cursor:pointer;"/>
				<img id="trialhidebtn-'.$id.'" src="'.$IMG_SERVER.'/images/subscription/'.$img.'" alt="add to cart" border="0" style="display:none;"/>
				</div>';
				}
			}
		}
		$str = $str_mon;
		return $str;
	}



	function mvFacebookRecommendation(){
	global $HTPFX;
	?>
		<div class="recommendationsmaindiv">
		<div class="recommendationsinnerdiv"><h3 class="new-head-right">Recommendations</h3>
</div>
		<fb:recommendations site="www.minyanville.com" app_id="139123259501339" width="300" height="300" header="false" linktarget="_top" border_color="#dedede" font="arial" max_age="7"></fb:recommendations>
		</div>
	<?
	}


	function mvTopsyTweet(){
		?>
<div class="recent-tweets"><script type="text/javascript"
	class="tpsy_sm">
  var TPSY_sm = [].concat(TPSY_sm || [], {
    width: '312',
    height: '300',
    title: 'What&#39;s being said about Minyanville',
    query: 'minyanville',
    shell_bg_color: '#002E5A',
    link_color: '#002E5A',
    show_bottom_ad: true,
    show_nick: true,
    nick: '@minyanville',
    apikey: 'tlQjPUNZJmICbGaw17s8EqtV',
    type: 'search'
  });
</script> <script type="text/javascript" src="http://cdn.topsy.com/social-modules/widget_loader.js"></script></div>
		<?php

	}
	
	function showFreeReportModule(){
		global $IMG_SERVER;	?>
	     <div class="rightShelf">
		<div class="frm-box">
			<div class="frm-shelf">
				<div class="freeReportImg"><img src="<?php echo $IMG_SERVER?>/images/newsletter/freeReport.png" /></div>
				<form class="frm-form clearfix">
		           <table id="frm-table">
		                <thead>
		                    <tr>
		                        <th colspan="2"></th>
		                    </tr>
		                </thead>
		                <tfoot>
		                    <tr>
		                        <th colspan="2"></th>
		                    </tr>
		                </tfoot>
		                <tbody>
		                    <tr>
		                        <td class="check"><input id="check_1" name="check_1" type="checkbox" value="1" AUTOCOMPLETE=OFF checked  />
		                        	<label for="check_1"><span></span></label>
		                        </td>
		                        <td>Trend Hunting for Hot Stocks</td>
		                    </tr>
		                    <tr>
		                        <td class="check"><input id="check_2" name="check_2" type="checkbox" value="2" AUTOCOMPLETE=OFF checked />
		                        	<label for="check_2"><span class="freeReportCheck"></span></label>
		                        </td>
		                        <td>$250 Billion Reasons to Get Into Fixed Income ETFs</td>
		                    </tr>
		                    <tr>
		                        <td class="check frm-no-brdr"><input id="check_3" name="check_3" type="checkbox" value="3" AUTOCOMPLETE=OFF checked />
		                        	<label for="check_3"><span class="freeReportCheck"></span></label>
		                        </td>
		                        <td class="frm-no-brdr">5 Winning Options Strategies From Andrew Keene's Playbook</td>
		                    </tr>
		                </tbody>
		            </table>
					<div class="fieldName">
						<div class="freeReportName"><img src="<?php echo $IMG_SERVER;?>/images/newsletter/freReportNameIcon.png" class="freeReportMsgIcons" />
					       <?php if($_SESSION['first_name']==""){
								    echo '<input type="text" name="inputName" id="fmrName" onfocus="if(this.value==\'Enter your name\')this.value=\'\'" onblur="if(this.value==\'\')this.value=\'Enter your name\'" value="Enter your name">';
							} else {
									echo '<input type="text" name="inputName" id="fmrName" value="'.$_SESSION['first_name'].' '.$_SESSION['last_name'].'" readonly >';
							} ?>
						</div>
					</div>
					<div class="fieldEmail">
						<div class="freeReportEmail"><img src="<?php echo $IMG_SERVER?>/images/newsletter/freReportMsg.png" class="freeReportUsrIcons" />
							<?php if($_SESSION['email']==""){
									echo '<input type="text" name="inputEmail" id="fmrEmail" onfocus="if(this.value==\'Enter your email\')this.value=\'\'" onblur="if(this.value==\'\')this.value=\'Enter your email\'" value="Enter your email">';
							} else {
									echo '<input type="text" name="inputEmail" id="fmrEmail" value="'.$_SESSION['email'].'" readonly >';
							} ?>
				       </div>				
					</div>
				</form>
				<div class="freeReportFooterImg"><img src="<?php echo $IMG_SERVER?>/images/newsletter/freReportImg.png" name="submit" id="fmrSubmit" onclick="submitFreeReport();" /></div>
				<div class="frm-disclaimer">Minyanville will send you information about its products.<br/> Unsubscribe at any time.</div>
				<div style="clear:both;"></div>
			</div>
		</div>
	</div>	     
      <?	     
	}
	
	function showTwitterWidget(){
	global $HTPFX;
		?>
	<div class="twitterWidget">
	<style>
	.twtr-widget h4 {
    font-size: 16px !important;
font-family:"lucida grande",lucida,tahoma,helvetica,arial,sans-serif !important;
font-weight:bold;
}
	</style>
		<script src="<?php echo $HTPFX;?>widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'search',
  search: '@minyanville',
  interval: 30000,
  title: '',
  subject: 'What\'s being said about Minyanville',
  width: 'auto',
  height: 300,
  theme: {
    shell: {
	background: '#8ec1da',
	color: '#ffffff'
    },
    tweets: {
    	background: '#ffffff',
    	color: '#444444',
    	links: '#1985b5'
    }
  },
  features: {
	  scrollbar: true,
	  loop: false,
	  live: true,
	  behavior: 'all'
  }
}).render().start();
</script></div>
	<?  }
	function mostPopularTickers($itemType,$sectionId){
	global $D_R;
	switch($itemType){
	  case '18':
		$objCache= new Cache();
	    $result=$objCache->getMostPopularDFTickers();
		?>
		<div class="article-right-module">
			<div class="article-right-module-header pad10">
				<h3 class="new-head-right">TICKERS</h3>
			</div>

			<div class="article-right-module-shadow">
				<div class="most-commented-tickers">
					<div class="most-ticker">
					<?php
					foreach ($result as $row )
					{
					?>
						<div class="ticker_in">
							<a href="<?=$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['ticker_id'];?>" class="ticker_in_t" target="_blank">
							<?php
							echo strtoupper($row['stocksymbol']);
							?>
							</a>
						</div>
					<?php
					}
					?>
					</div>
					<div class="allTickers">
						<a href="<?=$HTPFX.$HTHOST?>/mvpremium/list.htm?type=ticker" class="seeAllTicker">See All Tickers &raquo; </a>
					</div>
				</div>

			</div>
		</div>
		<?
		break;
		default:
			include_once($D_R."/admin/lib/_article_data_lib.php");
		?>
		<!-- most emailed/viewed/latest module -->
		<div class="article-right-module">
			<div class="article-right-module-header pad10">
				<h3 class="new-head-right">TICKERS</h3>
			</div>
			<div class="article-right-module-shadow">
				<div class="most-commented-tickers">
					<div class="most-ticker">
					<?php
					$obj_articleData=new ArticleData();
					$tickerResult=$obj_articleData->getMostUsedTicker($sectionId);
					foreach ($tickerResult as $row )
					{
					?>
						<div class="ticker_in">
							<a href="http://finance.minyanville.com/minyanville/quote?Symbol=<?php echo strtoupper($row['tag']);?>" class="ticker_in_t" target="_blank">
							<?php
							echo strtoupper($row['tag']);
							?>
							</a>
						</div>
					<?php
					}
					?>
					<!--<div style="float:left;width:100%">
						<a href="http://finance.minyanville.com/minyanville/" class="seeAllTicker">See All Tickers &raquo; </a>
					</div>
					<div class="dis_clear"></div> -->
					</div>
					<div style="padding:6px 0px 6px 0px; float:left;">
						<a href="http://finance.minyanville.com/minyanville/" class="seeAllTicker">See All Tickers &raquo; </a>
					</div>
				</div>
			</div>
		</div>
		<?
		}
	}


?>
