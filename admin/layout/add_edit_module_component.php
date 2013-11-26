<?php
header("Content-Type: text/plain");
header("Pragma: nocache\n");
header("cache-control: no-cache, must-revalidate, no-store\n\n");
header("Expires: 0");
include_once("$D_R/lib/_includes.php");
include_once("$D_R/lib/json.php");
include_once("$D_R/lib/_content_data_lib.php");
include_once("$D_R/lib/_layout_data_lib.php");
include_once("$D_R/admin/FCKeditor/fckeditor.php") ;
$stAction = $_GET['action'];
$inComponentId = $_GET['component_id'];
$inModuleId = $_GET['module_id'];
$stComponentName = getTemplateComponentName($inComponentId);
$arComDetail = getComponentDetail($inModuleId,$inComponentId,$stAction);
$deleteComponent = false;
if(count($arComDetail)  > 0)
{
	$deleteComponent = true;
}
if($arComDetail['target'] == '_parent')
{
  $stParentWindow = "selected";
  $stNewWindow = "";
}
else
{
  $stParentWindow = "";
  $stNewWindow = "selected";
}
$stHTML = "";
global $HTPFX,$HTHOST;
//if($stComponentName == 'TEMPLATE1_FEATURE_TITLE')
if($stComponentName == 'TEMPLATE_FEATURE_TITLE')
{
	if($arComDetail['component_type'] == "static")
	{
		$displayTitle = "";
		$displayGraphics = "none";
		$titleSelected = "checked";
	}
	else
	{
		$graphicsSelected = "checked";
		$displayTitle = "none";
		$displayGraphics = "";
	}
?>
		<form name="frm" method="post" action="" >
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
        <table border="0" align="center" cellpadding="5" cellspacing="0" width="90%">
		<tr><td><h1>Feature Title/Graphics<hr class="hr_line" /></h1></td></tr>
		<tr  class="highlight_row"><td><input id="radFeature" name="radFeature[]" type="radio" value="title" onclick="$('feature_title').show();$('feature_graphics').hide();" <?=$titleSelected?> > Title</td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr  class="highlight_row"><td><input id="radFeature" name="radFeature[]" type="radio" value="graphics" onclick="$('feature_graphics').show();$('feature_title').hide();" <?=$graphicsSelected?> > Graphics</td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr class="highlight_row"><td id="feature_title" style="display:<?=$displayTitle?>;">Feature Title: <input class="admin_input" type="text" id="txtFeatureTitle" name="txtFeatureTitle" value="<?=htmlentities($arComDetail['content'])?>" /></td></tr>
		<tr  class="highlight_row"><td id="feature_graphics" style="display:<?=$displayGraphics?>;">Feature Graphics </td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr  class="highlight_row"><input type="button" style="margin-left:5px;" class="submit_button" name="btnSave" value="Save" onclick="saveFeatureTitle('<?=$stAction?>','<?=$inComponentId?>','<?=$inModuleId?>')" /></tr>
		</table></form>
<?
}
else if($stComponentName == 'TEMPLATE1_HEADLINE_ARTICLE' || $stComponentName == 'TEMPLATE1_ARTICLE_LIST' || $stComponentName == 'TEMPLATE2_FEATURE_ARTICLE' ||  $stComponentName == 'TEMPLATE2_ARTICLE_LIST' || $stComponentName == 'TEMPLATE3_BODY' || $stComponentName == 'TEMPLATE3_ARTICLE_LIST'  || $stComponentName == 'TEMPLATE6_BODY' || $stComponentName == 'TEMPLATE16_BODY' || $stComponentName == 'TEMPLATE7_ARTICLE_LIST'
|| $stComponentName == 'TEMPLATE9_MAIN_ARTICLE' || $stComponentName == 'TEMPLATE9_ARTICLE1' || $stComponentName == 'TEMPLATE9_ARTICLE2' || $stComponentName == 'TEMPLATE9_ARTICLE3' || $stComponentName == 'TEMPLATE10_ARTICLE_LIST1' || $stComponentName == 'TEMPLATE10_ARTICLE_LIST2' || $stComponentName == 'TEMPLATE12_ARTICLE_LIST' || $stComponentName == 'TEMPLATE13_ARTICLE_LIST' || $stComponentName == 'TEMPLATE14_ARTICLE_LIST' || $stComponentName == 'TEMPLATE15_ARTICLE_LIST' || $stComponentName == 'TEMPLATE17_ARTICLE_LIST' || $stComponentName == 'TEMPLATE18_ARTICLE_LIST' || $stComponentName == 'TEMPLATE20_ARTICLE_LIST' || $stComponentName == 'TEMPLATE21_ARTICLE_LIST' || $stComponentName == 'TEMPLATE22_ARTICLE' || $stComponentName == 'TEMPLATE23_ARTICLE_LIST' || $stComponentName == 'TEMPLATE24_ARTICLE_LIST')
{

	getSubsectionList(1);
	$arSelectedKey = array();
	$arSelectedValue = array();
	$staticSelected ="";
	$dynamicSelected ="";
	$displayCategory = "";
	$displayEduCategory="none";

	if($arComDetail['component_type'] == "custom_url")
	{
		$customURL = $arComDetail['content'];
		$showCustomURL ="";
		$displayStatic = "none";
		$displayDynamic = "none";
		$staticSelected = "checked";
		$customSelected = "checked";
	}
	else if($arComDetail['component_type'] == "static_list")
	{
		$arSelectedItem = explode(",",$arComDetail['content']);
		$obContent = new Content("","");
		if(count($arSelectedItem) > 0)
		{
			foreach($arSelectedItem as $stValue)
			{
				if($stValue != "")
				{
					$arItemDetail = explode(":",$stValue);
					if(is_numeric($arItemDetail[0])){
						$arItemData = $obContent->getMetaSearch($arItemDetail[0],$arItemDetail[1]);
					}else{
						$arItemData['title'] = $arItemDetail[1];
					}

					//$arItemData = getArticleDetail($arItemDetail[0]);
					$arSelectedKey[] = $stValue;
					$arSelectedValue[] = $arItemData['title'];
				}
			}
		}
		$showCustomURL ="none";
		$displayStatic = "";
		$displayDynamic = "none";
		$staticSelected = "checked";
		$mvSelected = "checked";
	}
	else if($arComDetail['component_type'] == "dynamic_list")
	{
		$showCustomURL ="none";
		$displayStatic = "none";
		$displayDynamic = "";
		$dynamicSelected = "checked";
		// Start of logic to explore the SQL query to generate selected options
		$selectedSql = $arComDetail['content_sql'];
		$arSelectedSql = explode("FROM",$selectedSql);
		$arSql = explode(' ', trim($arSelectedSql[1]));
		$stSelectedTable = $arSql[0];
		$inSelectedTable = getItemTableId($stSelectedTable);
		$pattern="/(.*)ORDER BY(.*)['DESC','ASC'](.*)/";
		preg_match($pattern, $selectedSql, $matches);
		$itemOrder=$matches[2];
		if($inSelectedTable == 6)
		{
			$arSBlogSql = explode("is_user_blog =",$selectedSql);
			$stBlogSql = trim($arSBlogSql[1]);
			if(substr($stBlogSql,1,1) == 1)
			{
				$inSelectedTable = 6; // Discussion
			}
			else
			{
				$inSelectedTable = 4; // blog
			}
		}elseif ($inSelectedTable == 32){
			$displayEduCategory="block";
		}
		$inSelectedCategory = "";
		if($stSelectedTable == 'articles' || $stSelectedTable=='article_recent' || $stSelectedTable=='article_emailed')
		{
			$inSectionPos = strpos($selectedSql,'FIND_IN_SET');
			if($inSectionPos !== false)
			{
				// Find selectedCategory
				$stSection = substr($selectedSql, $inSectionPos+12, 5);
				$arSectionId = explode(",",$stSection);
				$inSelectedCategory = $arSectionId[0];
			}
		}
		else
		{
			$displayCategory = "none";
		}
		$arSelectedLimit =explode("LIMIT 0,",$selectedSql);
	 	$inSelectedLimit = $arSelectedLimit[1];
		$arGetConribId = explode("AND itm.contrib_id = ",$selectedSql);
		$arConribId = explode("'",$arGetConribId[1]);
		if($inContribPos !== false)
		{
			// Find selected Author
			$contrib_id = $arConribId['1'];
		}

		// End of logic to explore the SQL query to generate selected options
	}
	else
	{
		$displayStatic = "";
		$displayDynamic = "none";
		$displayPopularObject = "none";
		$staticSelected = "checked";
	}
	$arArticleSectionKey = array();
	$arArticleSectionValue = array();
	$arArticleSection = getArticleCategory();
	foreach($arArticleSection as $arSection)
	{
		if($arSection['section_id'] == $inSelectedCategory)
		{
			$inSelectedCategory = $arSection['section_id'].":".$arSection['type']; // To show selected category while editing
		}
		$arArticleSectionKey[] = $arSection['section_id'].":".$arSection['type'];
		$arArticleSectionValue[] = $arSection['name'];
	}
	$arEduCategory = getEduCategory();
?>
	<form name="frm" method="post" action="" onsubmit="return false;">
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a href="#" onClick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a href="#" style="float:right" onClick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
    <table border="0" width="100% " cellpadding="3" cellspacing="3">
	<? if($stComponentName == 'TEMPLATE1_HEADLINE_ARTICLE' || $stComponentName == 'TEMPLATE2_FEATURE_ARTICLE' || $stComponentName == 'TEMPLATE3_BODY' || $stComponentName == 'TEMPLATE9_MAIN_ARTICLE' || $stComponentName == 'TEMPLATE9_ARTICLE1' || $stComponentName == 'TEMPLATE9_ARTICLE2' || $stComponentName == 'TEMPLATE9_ARTICLE3')
			{
		?>
				<tr><td><h1>Headline Article</h1><hr class="hr_line" /></td></tr>
		<?  }
			else
			{
		?>
			<tr><td><h1>Content Listing</h1><hr class="hr_line" /></td></tr>
			<tr><td><input id="radOption" name="radOption[]" type="radio" value="static_list" onclick="$('static_content').show();$('dynamic_content').hide();" <?=$staticSelected?>> Static</td></tr>
			<tr><td><input id="radOption" name="radOption[]" type="radio" value="dynamic_list" onclick="$('dynamic_content').show();$('static_content').hide();" <?=$dynamicSelected?>> Dynamic</td></tr>

		<?  }
		?>
			<? if($stComponentName == 'TEMPLATE9_MAIN_ARTICLE'){ ?>
            <tr><td><input id="radCustomURL" name="radCustomURL[]" type="radio" value="mvContent" onclick="$('static_content').show();$('customURL').hide();" <?=$mvSelected?>> Minyanville Content
			<input id="radCustomURL" name="radCustomURL[]" type="radio" value="customURL" onclick="$('customURL').show();$('static_content').hide();" <?=$customSelected?>> Custom URL</tr>
			<tr id="customURL" style="display:<?=$showCustomURL;?>;"><td>
			<table width="100%" border="0" align="left" cellspacing="0" cellpadding="2">
				<tr>
				<td>Launch URL: <input type="text" id="txtCustomURL" class="customURL_textbox" value="<?=$customURL?>" /></td>
				</tr>
			</table>
			</td></tr>
            <? } ?>
			<tr id="static_content" style="display:<?=$displayStatic?>;">
			<td>
			<table>
			<tr><td>
			 <table width="100%" border="0" align="left" cellspacing="0" cellpadding="2">
				  <tr class="highlight_row">
					<td class="common_heading">Search: <input type="text" id="txtKeyword" class="search_textbox" /></td>
				  	<td class="search_drop_down" nowrap="nowrap" >
						<? month_options("selMonth",$mo,"class='inputCombo'","selMonth") ?>
						<? day_options("selDate",$day,"class='inputCombo'","selDate",$valday) ?>
						<? year_options("selYear",$year,"class='inputCombo'",date("Y"),2002,"selYear") ?>
					</td>
				  </tr>
                  <tr class="highlight_row">
					<td><select name="selAuthor" id="selAuthor" class="admin_select">
					<option value="">-All Authors-</option>
						<? selectHashArr(get_active_contributors(),"id","name",$contrib_id)?>
					</select></td>
				   <td align="right"><input type="button" class="submit_button" value="search" onclick="searchItems()" /></td>
			</table>
			</td></tr>
			<tr><td id="search_result_container"></td></tr>
            <tr><td>
                <table width="100%" border="0" align="left" cellspacing="0" cellpadding="2">
                    <tr><td colspan="3"><b>Add Custom URL:</b></td></tr>
                    <tr><td width="20%"><b>Title:</b></td><td colspan="2"><input type="text" size="55" id="txtCustTitle" /></td></tr>
                    <tr><td width="20%"><b>URL:</b></td><td><input type="text" size="55" id="txtCustUrl" /></td><td><input class="submit_button" type="button" value="ADD" onclick="addCustomUrl();" /></td></tr>
                    <tr><td colspan="3" style="color:#FF0000;">Please add http:// before custom url.</td></tr>
                </table>
            </td></tr>
			<tr><td><select name="selItemList[]" MULTIPLE id="selItemList" style="width:200px; background:white;color:black;width:300px;" size="7" >
			<?

			foreach($arSelectedKey as $key => $value) // Display Aldeady Selected Values
			{
				echo "<option value='".$value."'>$arSelectedValue[$key]</option>";
			}
			?>
			</select></td></tr>
			<tr><td><a onclick="moveOptionsUp('selItemList')">up</a>&nbsp;&nbsp;<a onclick="moveOptionsDown('selItemList')">down</a></td></tr>
			<tr  class="highlight_row"><td><input type="button" name="removeItem" class="submit_button" value="Remove Selected" onclick="removeItems('selItemList')"></td></tr>
			</table>
			</td>
		</tr>
		<tr id="dynamic_content" style="display:<?=$displayDynamic?>;">
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr  class="highlight_row"><td colspan="4">Select criteria to generate dynamic content</td></tr>
				<tr><td>&nbsp;</td></tr>
                <tr  class="highlight_row"><td>Item Type</td><td id="article_section_header" style="display:<?=$displayCategory?>;">Item Category</td><td id="eduCatHeader" style="display:<?=$displayEduCategory?>;">Edu Category</td><td id="article_author_header" style="display:<?=$displayCategory?>;">Author</td><td>No. of Items</td><td>Order By</td></tr>
				<tr><td>&nbsp;</td></tr>
                <tr  class="highlight_row">
					<td><select name="selItemType" id="selItemType" onChange="displayArticleCategory(this.value);">
					<option value="1" <? if($inSelectedTable == 1){echo "selected";}?>>Article</option>
					<option value="11" <? if($inSelectedTable == 11){echo "selected";}?>>Videos</option>
					<option value="2" <? if($inSelectedTable == 2){echo "selected";}?>>BuzzPosts</option>
					<option value="4" <? if($inSelectedTable == 4){echo "selected";}?>>Discussions</option>
					<option value="6" <? if($inSelectedTable == 6){echo "selected";}?>>Blogs</option>
					<option value="18" <? if($inSelectedTable == 18){echo "selected";}?>>Daily Feed</option>
					<option value="10" <? if($inSelectedTable == 10){echo "selected";}?>>SlideShows</option>
					<option value="32" <? if($inSelectedTable == 32){echo "selected";}?>>Education center</option>
					</select></td>
					<td id="article_section" style="display:<?=$displayCategory?>;">
					<select name="selItemCat" id="selItemCat">
					<option value="">-select-</option>
					<?
					foreach($arArticleSectionKey as $key => $value) // Display Aldeady Selected Values
					{
						$selectedCat ="";
						if($value == $inSelectedCategory)
						{
							$selectedCat ="selected" ;
						}
					?>
						<option value="<?= $value ?>" <?=$selectedCat?>><?= $arArticleSectionValue[$key] ?></option>
					<?
					}
					?>
					</select></td>
					<td id="educategory" style="display:<?=$displayEduCategory?>;">
					<select name="selEduCat" id="selEduCat">
					<option value="">-select-</option>
					<?
					foreach($arEduCategory as $value) // Display Aldeady Selected Values
					{
						/*$selectedCat ="";
						if($value == $inSelectedCategory)
						{
							$selectedCat ="selected" ;
						}*/
					?>
						<option value="<?=$value['id']?>"><?=$value['menu_name']?></option>
					<?
					}
					?>
					</select></td>
					<td id="article_author" style="display:<?=$displayCategory?>;"><select name="selItemAuthor" id="selItemAuthor" class="admin_select" >
					<option value="">-All Authors-</option>
						<? selectHashArr(get_active_contributors(),"id","name",$contrib_id)?>
					</select></td>
					<td><select name="selItemNumber" id="selItemNumber">
					<? for($i=1;$i<=10;$i++)
					{
						$selectedLimit ="" ;
						if($i == $inSelectedLimit)
						{
							$selectedLimit ="selected" ;
						}
					?>
						<option value="<?= $i ?>" <?=$selectedLimit?>><?= $i ?></option>
					<? } ?>
					</select></td>
					<td><select name="selItemCriteria" id="selItemCriteria">
					<option value="date">Latest</option>
					<option value="mostpopular" <?if($stSelectedTable=='article_recent'){ echo "selected"; } ?>>Most Read</option>
					<option value="mostemailed" <?if($stSelectedTable=='article_emailed'){ echo "selected"; } ?>>Most Emailed</option>
					</select></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr><td><input type="button" name="btnSave"  style="margin-left:12px;" class="submit_button" value="Save" onclick="saveItemList('<?=$stAction?>','<?=$inComponentId?>','<?=$inModuleId?>')" /></td></tr>
	</table>
	</form>
<?
}
else if($stComponentName == 'TEMPLATE1_RIGHT_HEADING' || $stComponentName == 'TEMPLATE2_HEADER' || $stComponentName == 'TEMPLATE2_BODY' || $stComponentName == 'TEMPLATE3_HEADER' || $stComponentName == 'TEMPLATE4_TEXT1' || $stComponentName == 'TEMPLATE4_TEXT2' || $stComponentName == 'TEMPLATE5_HEADER' || $stComponentName == 'TEMPLATE7_HEADER')
{
?>
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
    <table border="0" cellpadding="5" cellspacing="5" width="100%">
		<tr><td  colspan="3"><h1>Title<hr class="hr_line" /></h1></td></tr>
        <tr><td valign="top">Text:</td><td> <textarea id="txtTitle" name="txtTitle" rows="5" cols="50"><?=$arComDetail['content']?></textarea></td></tr>
		<tr  class="highlight_row"><td colspan="2"><input class="submit_button" type="button"  name="btnSave" value="Save" onclick="saveTitle('<?=$stAction?>','<?=$inComponentId?>','<?=$inModuleId?>')" /></td></tr>
	</table>

<?
}
else if($stComponentName == 'TEMPLATE1_MORE_LINK' || $stComponentName == 'TEMPLATE2_MORE_LINK' || $stComponentName == 'TEMPLATE3_MORE_LINK' || $stComponentName == 'TEMPLATE7_MORE_LINK' || $stComponentName == 'TEMPLATE12_MORE_LINK' || $stComponentName == 'TEMPLATE13_FOOTER' || $stComponentName == 'TEMPLATE12_TITLE' || $stComponentName == 'TEMPLATE10_MORE_LINK'  ||$stComponentName == 'TEMPLATE18_FOOTER' || $stComponentName == 'TEMPLATE20_FOOTER' || $stComponentName == 'TEMPLATE15_FOOTER' || $stComponentName == 'TEMPLATE23_FOOTER')
{
?>
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a  style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
    <table border="0" cellpadding="3" cellspacing="3" width="100%">
	<tr><td  colspan="3"><h1>Create Links<hr class="hr_line" /></h1></td></tr>
    <tr  class="highlight_row"><td>Title: <input class="admin_input" type="text" id="txtTitle" name="txtTitle" value="<?=htmlentities($arComDetail['content'])?>" /></td></tr>
		<tr><td></td></tr>
        <tr  class="highlight_row"><td>Link: <input type="text" class="admin_input" id="txtLink" name="txtLink" value="<?=htmlentities($arComDetail['link'])?>" /></td></tr>
		<tr><td></td></tr>
        <tr  class="highlight_row"><td>Target: <select id="selLinkTarget" name="selLinkTarget">
						<option value="_blank" <?=$stNewWindow?>>New Window</option>
						<option value="_parent" <?=$stParentWindow?>>Parent Window</option>
						</select>
		<tr><td></td></tr>
        <tr><td><input type="button" class="submit_button" name="btnSave" value="Save" onclick="saveLink('<?=$stAction?>','<?=$inComponentId?>','<?=$inModuleId?>')" /></td></tr>
	</table>
<?
}
else if($stComponentName == 'TEMPLATE5_TEXT' || $stComponentName == 'TEMPLATE8_TEXT' || $stComponentName == 'TEMPLATE10_HEADER' ||  $stComponentName =='TEMPLATE11_HEADER' || $stComponentName == 'TEMPLATE12_HEADER' || $stComponentName == 'TEMPLATE13_HEADER' || $stComponentName == 'TEMPLATE14_HEADER' || $stComponentName == 'TEMPLATE15_HEADER' || $stComponentName == 'TEMPLATE17_HEADER' || $stComponentName == 'TEMPLATE11_TEXT' || $stComponentName == 'TEMPLATE18_HEADER'  || $stComponentName == 'TEMPLATE20_HEADER' || $stComponentName == 'TEMPLATE21_HEADER'  || $stComponentName == 'TEMPLATE22_HEADER'|| $stComponentName == 'TEMPLATE23_HEADER' || $stComponentName == 'TEMPLATE24_HEADER')
{
	if($stComponentName == 'TEMPLATE5_TEXT')
	{
		$inTemplateId = 5;
	}
	else if($stComponentName == 'TEMPLATE8_TEXT')
	{
		$inTemplateId = 8;
	}
	else if($stComponentName == 'TEMPLATE10_HEADER')
	{
		$inTemplateId = 10;
	}
	else if($stComponentName == 'TEMPLATE11_HEADER')
	{
		$inTemplateId = 11;
	}
	else if($stComponentName == 'TEMPLATE12_HEADER')
	{
		$inTemplateId = 12;
	}
	else if($stComponentName == 'TEMPLATE13_HEADER')
	{
		$inTemplateId = 13;
	}
	else if($stComponentName == 'TEMPLATE14_HEADER')
	{
			$inTemplateId = 14;
	}
	else if($stComponentName == 'TEMPLATE15_HEADER')
	{
				$inTemplateId = 15;
	}
	else if($stComponentName == 'TEMPLATE17_HEADER')
	{
					$inTemplateId = 17;
	}
	else if($stComponentName == 'TEMPLATE11_TEXT')
	{
		$inTemplateId = 11;
	}else if($stComponentName == 'TEMPLATE18_HEADER')
	{
		$inTemplateId = 18;
	}
	else if($stComponentName == 'TEMPLATE20_HEADER')
	{
				$inTemplateId = 20;
	}
	else if($stComponentName == 'TEMPLATE21_HEADER')
	{
				$inTemplateId = 21;
	}
	else if($stComponentName == 'TEMPLATE22_HEADER')
	{
				$inTemplateId = 22;
	}
	else if($stComponentName == 'TEMPLATE23_HEADER')
	{
				$inTemplateId = 23;
	}
	else if($stComponentName == 'TEMPLATE24_HEADER')
	{
				$inTemplateId = 24;
	}

?>
	<form name="frmFCK" action="" method="post">
	<input type="hidden" name="hidFCKContent" value="fck" />
	<input type="hidden" name="hidFCKAction" value="<?= $stAction?>" />
	<input type="hidden" name="hidFCKComponentId" value="<?= $inComponentId?>" />
	<input type="hidden" name="hidModuleId" value="<?= $inModuleId?>" />
	<input type="hidden" name="txtModuleName" value="" />
	<input type="hidden" name="selTemplate" value="<?= $inTemplateId?>" />
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
	<table border="0" cellpadding="5" cellspacing="5">
		<tr><td>Content:</td></tr>
		<tr><td>
	<?
	$oFCKeditor = new FCKeditor('txtFCKContent') ;
	$oFCKeditor->BasePath = '../FCKeditor/';
	$oFCKeditor->Width = "400px";
	$oFCKeditor->Height = "260px";
	$oFCKeditor->ToolbarSet = "Basic";
	$oFCKeditor->Value1 = $arComDetail['content'];
	//$oFCKeditor->Value =  stripslashes($moduleInfo['html']) . " " . $articles;
	$oFCKeditor->Create();
	?>
	</td></tr>
		<tr><td><input type="button" name="subSave" value="Save" onclick="document.frmFCK.txtModuleName.value=$('txtModuleName').value;document.frmFCK.submit();" /></td></tr>
	</table>
	</form>
<?
}
else if($stComponentName == 'TEMPLATE7_BODY')
{
?>
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
	<table border="0" cellpadding="5" cellspacing="5">
		<tr><td>Choose Author: <select id="selAuthor" name="selAuthor">
						<option value="">--All Authors--</option>
						<? selectHashArr(get_active_contributors(),"id","name",$arComDetail['content_sql']); ?>
						</select></td></tr>
		<tr><td>Description: <textarea id="txaDesc" name="txaDesc" rows="5" cols="50"><?=$arComDetail['content']?></textarea></td></tr>
		<tr><td><input type="button" name="btnSave" value="Save" onclick="saveAuthor('<?=$stAction?>','<?=$inComponentId?>','<?=$inModuleId?>')" /></td></tr>
	</table>
<?
}
else if($stComponentName == 'TEMPLATE1_FEATURE_TITLE' || $stComponentName == 'TEMPLATE2_GRAPHICS' || $stComponentName == 'TEMPLATE4_GRAPHICS1' || $stComponentName == 'TEMPLATE4_GRAPHICS2'
|| $stComponentName == 'TEMPLATE4_GRAPHICS3' || $stComponentName == 'TEMPLATE6_GRAPHICS' || $stComponentName == 'TEMPLATE16_GRAPHICS' || $stComponentName == 'TEMPLATE7_GRAPHICS' || $stComponentName == 'TEMPLATE9_GRAPHICS' || $stComponentName == 'TEMPLATE22_GRAPHICS')
{
	if($stComponentName == 'TEMPLATE1_FEATURE_TITLE')
	{
		$inTemplateId = 1;
	}
	else if($stComponentName == 'TEMPLATE2_GRAPHICS')
	{
		$inTemplateId = 2;
	}
	else if($stComponentName == 'TEMPLATE4_GRAPHICS1' || $stComponentName == 'TEMPLATE4_GRAPHICS2' || $stComponentName == 'TEMPLATE4_GRAPHICS3')
	{
		$inTemplateId = 4;
	}
	else if($stComponentName == 'TEMPLATE6_GRAPHICS')
	{
		$inTemplateId = 6;
	}
	else if($stComponentName == 'TEMPLATE7_GRAPHICS')
	{
		$inTemplateId = 7;
	}
	else if($stComponentName == 'TEMPLATE9_GRAPHICS')
	{
		$inTemplateId = 9;
	}
	else if($stComponentName == 'TEMPLATE16_GRAPHICS')
	{
			$inTemplateId = 16;
	}
	else if($stComponentName == 'TEMPLATE22_GRAPHICS')
	{
		$inTemplateId = 22;
	}	
?>

	<form name="frmFCK" action="" method="post">
	<input type="hidden" name="hidFCKContent" value="fck" />
	<input type="hidden" name="hidFCKAction" value="<?= $stAction?>" />
	<input type="hidden" name="hidFCKComponentId" value="<?= $inComponentId?>" />
	<input type="hidden" name="hidModuleId" value="<?= $inModuleId?>" />
	<input type="hidden" name="txtModuleName" value="" />
	<input type="hidden" name="selTemplate" value="<?= $inTemplateId?>" />
	<div class="close_div"><? if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } ?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
	<table border="0" cellpadding="5" cellspacing="5" width="100%">
		<tr><td>Choose Image:</td></tr>
		<tr><td>
<?
		$oFCKeditor = new FCKeditor('txtFCKContent') ;
		$oFCKeditor->BasePath = '../FCKeditor/';
		$oFCKeditor->Width = "400px";
		$oFCKeditor->Height = "260px";
		$oFCKeditor->ToolbarSet = "Image";
		$oFCKeditor->Value1 = $arComDetail['content'];
		$oFCKeditor->Create();
?>
		</td></tr>
		<tr><td><input type="button" name="subSave" value="Save" onclick="document.frmFCK.txtModuleName.value=$('txtModuleName').value;document.frmFCK.submit();"/></td></tr>
	</table>
	</form>

<?
}
elseif($stComponentName == 'TEMPLATE19_TEXT'){
$arslideDecode="";
$json = new Services_JSON();
$arslideDecode[] =  $json->decode($arComDetail['content'],true);
$contentAd=$arslideDecode[0]->content;
$timeDuration=$arslideDecode[0]->timeDuration;
$frequencyCap=$arslideDecode[0]->frequency;

?>
	<form name="frmtemp19" action="" method="post">
	<div class="close_div"><? /*if($deleteComponent){?><div class="delete_comp"><a onclick="deleteComponent('<?=$inComponentId?>','<?=$inModuleId?>');">Delete Component</a></div><? } */?><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></div>
	<div class="temp19main">
		<div class="temp_cont"><span>Content : </span> <textarea rows="5" cols="50" name="txtcontent" id="txtcontent" ><?=$contentAd;?></textarea></div>
		<div class="temp_cont"><span>Duration : </span> <input type="text" name="timeDuration" id="timeDuration" value="<?=$timeDuration;?>"  /> in sec </div>
		<div class="temp_cont"><span>Frequency Cap : </span> <input type="text" name="freq"  id="freq" value="<?=$frequencyCap;?>" /> in number </div>
		<div class="save19bttn"><input type="button" name="subSave" value="Save" onclick="savetemp19('<?=$stAction?>','<?=$inComponentId?>','<?= $inModuleId?>');" /></div>
	</div>
	</form>
<? }
else
{
?>
	<table border="0" cellpadding="5" cellspacing="5">
		<tr><td><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></td></tr>
	</table>
<?
}
?>
