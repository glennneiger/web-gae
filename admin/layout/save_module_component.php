<?php
global $D_R;
include_once("$D_R/lib/json.php");
include_once("$D_R/lib/_layout_data_lib.php");
$json = new Services_JSON();
$stAction = $_POST['action'];
if($stAction  == 'delete')
{
	deleteComponent($_POST['module_id'],$_POST['component_id']);
	exit();
}
	$arData['module_id'] = $_POST['module_id'];
	$arData['component_id'] = $_POST['component_id'];
	if(isset($_POST['component_type']))
	{
		$arData['component_type'] = $_POST['component_type'];
	}
	else
	{
		$arData['component_type'] = 'static';
	}
	$stComponentName =getTemplateComponentName($_POST['component_id']);
	if($stComponentName == 'TEMPLATE1_HEADLINE_ARTICLE' || $stComponentName == 'TEMPLATE1_ARTICLE_LIST' || $stComponentName == 'TEMPLATE2_FEATURE_ARTICLE' || $stComponentName == 'TEMPLATE2_ARTICLE_LIST' || $stComponentName == 'TEMPLATE3_ARTICLE_LIST' || $stComponentName == 'TEMPLATE3_BODY' || $stComponentName == 'TEMPLATE6_BODY' || $stComponentName == 'TEMPLATE16_BODY' || $stComponentName == 'TEMPLATE7_ARTICLE_LIST'
	|| $stComponentName == 'TEMPLATE9_MAIN_ARTICLE' || $stComponentName == 'TEMPLATE9_ARTICLE1' || $stComponentName == 'TEMPLATE9_ARTICLE2' || $stComponentName == 'TEMPLATE9_ARTICLE3' || $stComponentName == 'TEMPLATE10_ARTICLE_LIST1' || $stComponentName == 'TEMPLATE10_ARTICLE_LIST2' || $stComponentName == 'TEMPLATE12_ARTICLE_LIST' || $stComponentName == 'TEMPLATE13_ARTICLE_LIST' || $stComponentName == 'TEMPLATE14_ARTICLE_LIST' || $stComponentName == 'TEMPLATE15_ARTICLE_LIST' || $stComponentName == 'TEMPLATE17_ARTICLE_LIST' || $stComponentName == 'TEMPLATE18_ARTICLE_LIST' || $stComponentName == 'TEMPLATE20_ARTICLE_LIST' || $stComponentName == 'TEMPLATE21_ARTICLE_LIST' || $stComponentName == 'TEMPLATE22_ARTICLE' || $stComponentName == 'TEMPLATE23_ARTICLE_LIST' || $stComponentName == 'TEMPLATE24_ARTICLE_LIST')
	{
		if($arData['component_type'] == "custom_url")
		{
			$arData['content'] = $_POST['custom_url'];
		}
		else if($arData['component_type'] == "static_list") // User select Items manually
		{
			$arData['content'] = $_POST['selected_items'];
		}
		else // Create Dynamic query
		{
			$inItemType = $_POST['item_type'];
			$inItemCat= $_POST['item_cat'];
			$inItemAuthor= $_POST['item_author'];
			$inItemNumber = $_POST['item_number'];
			$stItemOrder = $_POST['item_order'];
			$stItemTable = getItemTable($inItemType);
			if($inItemType == 1 || $inItemType == 2 ) // Article or Buzz Posts
			{
				if($stItemOrder=='mostemailed'){
					$stItemTable='article_emailed';
				}elseif($stItemOrder=='mostpopular'){
					$stItemTable='article_recent';
				}
				$stDynamicQuery = "SELECT itm.id AS item_id, $inItemType AS item_type,itm.keyword,itm.blurb,EIM.url,
				 itm.title AS item_title,
				itm.contrib_id AS author_id,ct.name AS author_name,'contributor' AS author_type, itm.date AS created_on";
				if($stItemOrder=='mostpopular' || $stItemOrder=='mostemailed'){
					$stDynamicQuery .=",total ";
				}
				$stDynamicQuery .=" FROM $stItemTable AS itm , `contributors` AS ct, ex_item_meta EIM
				WHERE itm.contrib_id = ct.id AND EIM.item_id = itm.id AND EIM.item_type='$inItemType' AND  itm.is_live = '1' AND itm.approved ='1'";
				if($inItemType == 1 && $inItemCat != "")
				{
					$arSectionDetail = explode(":",$inItemCat);
					$inSectionId = $arSectionDetail[0];
					$stSectionType = $arSectionDetail[1];
					$stDynamicQuery .= " AND FIND_IN_SET($inSectionId,itm.subsection_ids)";
				}
				if($inItemType == 1 && $inItemAuthor != "")
				{
					$stDynamicQuery .= " AND itm.contrib_id = '$inItemAuthor'";
				}
				if($inItemType == 2){
					$stDynamicQuery .= " AND itm.show_on_web='1'";
				}
				
			}
			elseif($inItemType == 11) // Videos
			{
				$stDynamicQuery = "SELECT itm.id AS item_id, $inItemType AS item_type, itm.title AS item_title,
				'' AS author_id, itm.uploaded_by AS author_name,'' AS author_type, publish_time AS created_on FROM $stItemTable AS itm
				WHERE itm.is_live = '1' AND itm.approved ='1'";
			}
			elseif($inItemType == 4 || $inItemType == 6)
			{
				if($inItemType == 4) // Discussion
				{
					$isBlog = '0';
				}
				else // Blog
				{
					$isBlog = '1';
				}
				$stDynamicQuery = "SELECT itm.id AS item_id, $inItemType AS item_type, itm.title AS item_title,
				itm.author_id , CONCAT(sub.fname,' ',sub.lname) AS author_name, 'subscriber' AS author_type,itm.created_on FROM $stItemTable AS itm , subscription AS sub
				WHERE itm.author_id = sub.id  AND approved = '1' AND is_user_blog = '".$isBlog."'";
			}
			elseif($inItemType == 18)
			{

				$stDynamicQuery="SELECT itm.id AS item_id, $inItemType AS item_type,EIM.url,EQT.quick_title,EII.url image_url,itm.title AS
				 item_title,itm.body,itm.title_link,itm.contrib_id,itm.publish_date AS 
				 created_on, C.name authorname, itm.excerpt 
				FROM $stItemTable AS itm,contributors C ,ex_item_meta EIM, ex_item_image EII, ex_quick_title  EQT
				 WHERE itm.is_approved = '1' AND EIM.item_id = itm.id AND EIM.item_type='$inItemType' 
				 AND EII.item_id = itm.id AND EII.item_type='$inItemType' 
				 AND EQT.item_id = itm.id AND EQT.item_type='$inItemType' 
				  AND itm.is_deleted = '0' and 
				 itm.is_live='1' and itm.contrib_id=C.id";

			}elseif($inItemType == 32){
				$stDynamicQuery="SELECT itm.id AS item_id, $inItemType AS item_type, itm.title AS item_title,itm.body,itm.contrib_id,itm.publish_date as created_on, C.name authorname, C.id as authorId, itm.edu_img FROM $stItemTable AS itm,`contributors` C  WHERE itm.is_approved = '1' AND itm.is_deleted = '0' and itm.is_live='1' and itm.contrib_id=C.id";
				if(!empty($inItemCat)){
					 $stDynamicQuery .=" AND FIND_IN_SET('".$inItemCat."',itm.category_id)";
				}
			}
			$stDynamicQuery .= " ORDER BY";
			if($stItemOrder == 'author')
			{
				$stDynamicQuery .= " author_name";
			}
			elseif($stItemOrder=='mostpopular' || $stItemOrder=='mostemailed'){
				$stDynamicQuery .= " total DESC";
			}else{
				if($inItemType == 18){
					$stDynamicQuery .= " itm.publish_date DESC,creation_date DESC ";
				}
				else
				{
					$stDynamicQuery .= " created_on DESC";
				}
			}
			$stDynamicQuery .= " LIMIT 0,$inItemNumber";

			$arData['content_sql'] = addslashes($stDynamicQuery);
		}
	}
	else if($stComponentName == 'TEMPLATE1_RIGHT_HEADING' || $stComponentName == 'TEMPLATE2_HEADER' || $stComponentName == 'TEMPLATE2_BODY' || $stComponentName == 'TEMPLATE3_HEADER' || $stComponentName == 'TEMPLATE4_TEXT1' || $stComponentName == 'TEMPLATE4_TEXT2' || $stComponentName == 'TEMPLATE5_HEADER' || $stComponentName == 'TEMPLATE7_HEADER')
	{
		$arData['content'] = $_POST['title'];
	}
	else if($stComponentName == 'TEMPLATE1_MORE_LINK' || $stComponentName == 'TEMPLATE2_MORE_LINK' || $stComponentName == 'TEMPLATE3_MORE_LINK' || $stComponentName == 'TEMPLATE7_MORE_LINK' || $stComponentName == 'TEMPLATE12_MORE_LINK' || $stComponentName == 'TEMPLATE13_FOOTER'  || $stComponentName == 'TEMPLATE12_TITLE' || $stComponentName == 'TEMPLATE10_MORE_LINK' || $stComponentName == 'TEMPLATE18_FOOTER' || $stComponentName == 'TEMPLATE15_FOOTER' || $stComponentName == 'TEMPLATE20_FOOTER' || $stComponentName == 'TEMPLATE23_FOOTER')
	{
		$arData['content'] = $_POST['title'];
		$arData['link'] = $_POST['hyperlink'];
		$arData['target'] = $_POST['target'];
	}
	else if($stComponentName == 'TEMPLATE5_TEXT' || $stComponentName == 'TEMPLATE8_TEXT' || $stComponentName == 'TEMPLATE10_HEADER'  ||  $stComponentName =='TEMPLATE11_HEADER' ||  $stComponentName =='TEMPLATE12_HEADER' ||  $stComponentName =='TEMPLATE13_HEADER' ||  $stComponentName =='TEMPLATE14_HEADER' || $stComponentName =='TEMPLATE15_HEADER' || $stComponentName =='TEMPLATE17_HEADER' || $stComponentName == 'TEMPLATE11_TEXT' ||  $stComponentName =='TEMPLATE18_HEADER' || $stComponentName =='TEMPLATE20_HEADER' || $stComponentName =='TEMPLATE21_HEADER' || $stComponentName =='TEMPLATE22_HEADER' || $stComponentName =='TEMPLATE23_HEADER' || $stComponentName =='TEMPLATE24_HEADER')
	{
		$arData['content'] = $_POST['fckcontent'];
	}
	else if($stComponentName == 'TEMPLATE7_BODY')
	{
		$arData['content'] = $_POST['author_desc'];
		$arData['content_sql'] = $_POST['author'];
	}
	else if($stComponentName == 'TEMPLATE1_FEATURE_TITLE' || $stComponentName == 'TEMPLATE2_GRAPHICS' || $stComponentName == 'TEMPLATE4_GRAPHICS1' || $stComponentName == 'TEMPLATE4_GRAPHICS2'
	|| $stComponentName == 'TEMPLATE4_GRAPHICS3' || $stComponentName == 'TEMPLATE6_GRAPHICS' || $stComponentName == 'TEMPLATE16_GRAPHICS' || $stComponentName == 'TEMPLATE7_GRAPHICS' || $stComponentName == 'TEMPLATE9_GRAPHICS' || $stComponentName == 'TEMPLATE22_GRAPHICS')
	{
		$arData['content'] = $_POST['fckcontent'];
	}
	elseif($stComponentName == 'TEMPLATE19_TEXT'){
		$arContent =array(
		 	'content' => $_POST['content'],
			'timeDuration' => $_POST['timeDuration'],
			'frequency' => $_POST['frequency']
		);
		$arData['content'] =  $json->encode($arContent);
	}
	saveComponent($arData);
?>
