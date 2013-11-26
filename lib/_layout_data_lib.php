<?php
include_once("$ADMIN_PATH/lib/_dailyfeed_data_lib.php");
include_once($D_R."/lib/config/_email_config.php");

function getTempModulesList($page_id,$placeholder) {
	$stQuery = "SELECT module_order_temp AS module_order FROM layout_placeholder WHERE page_id = '$page_id' AND placeholder='$placeholder'";
	$arModulesResult = exec_query($stQuery,1);
	return $arModulesResult;
}


function getModuleTemplateDetail($module_id)
{
	$stQuery = "SELECT lt.template_id,lt.design FROM layout_module AS lm, layout_template AS lt
				 WHERE lm.template_id = lt.template_id AND lm.module_id = '$module_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult;
}
function getTemplateDetail($template_id)
{
	$stQuery = "SELECT lt.template_id,lt.design FROM layout_template AS lt
				 WHERE lt.template_id = '$template_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult;
}
function getModuleComponent($module_id,$template_id)
{
	$stQuery = "SELECT ltc.component_name, lmc.component_type,lmc.content,lmc.target,lmc.link,lmc.content_sql
				FROM layout_template_component AS ltc LEFT JOIN layout_module_component AS lmc
				ON lmc.component_id = ltc.id AND lmc.module_id = '$module_id'
				WHERE ltc.template_id = '$template_id' GROUP BY lmc.component_id ";
	$arResult = exec_query($stQuery);
	return $arResult;
}
function getTempModuleComponent($module_id,$template_id)
{
	$stQuery = "SELECT ltc.component_name, lmc.component_type,lmc.content,lmc.target,lmc.link,lmc.content_sql
				FROM layout_template_component AS ltc LEFT JOIN layout_module_component_temp AS lmc
				ON lmc.component_id = ltc.id AND lmc.module_id = '$module_id'
				WHERE ltc.template_id = '$template_id' GROUP BY lmc.component_id ";


	$arResult = exec_query($stQuery);
	return $arResult;
}

function getEduArtUrl($id)
{
	global $D_R;
	include($D_R."/lib/config/_edu_config.php");
	global $eduItemMeta;
	
	$qryDetail = " SELECT url FROM ex_item_meta WHERE item_type='".$eduItemMeta."' AND item_id='".$id."'";
	$resDetail = exec_query($qryDetail,1);
	if(!empty($resDetail)){
		return $resDetail;
	}		
}

function getTemplateList()
{
	$stQuery = "SELECT template_id,template_name,admin_design FROM layout_template where is_active='1'";
	$arModulesResult = exec_query($stQuery);
	return $arModulesResult;
}
function getTemplateComponentList()
{
	$stQuery = "SELECT id,template_id,component_name FROM layout_template_component";
	$arResult = exec_query($stQuery);
	return $arResult;
}
function getMaxTempModuleKey()
{
	$stQuery = "SELECT max(module_id) AS max_id FROM layout_module_component_temp";
	$arResult = exec_query($stQuery,1);
	if($arResult['max_id'] == NULL)
	{
		return 1;
	}
	else
	{
		return $arResult['max_id']+1;
	}
}
function getTemplateComponentName($component_id)
{
	$stQuery = "SELECT component_name FROM layout_template_component WHERE id= '$component_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult['component_name'];
}
function getComponentDetail($module_id,$component_id,$action)
{
	if($action == "add")
	{
		$stQuery = "SELECT component_type,content,target,link,content_sql FROM layout_module_component_temp WHERE module_id= '$module_id' AND component_id= '$component_id'";
	}
	else
	{
		$stQuery = "SELECT component_type,content,target,link,content_sql FROM layout_module_component WHERE module_id= '$module_id' AND component_id= '$component_id'";
	}
	$arResult = exec_query($stQuery,1);
	return $arResult;
}
function getItemTable($item_id)
{
	$stQuery = "SELECT item_table FROM ex_item_type WHERE id= '$item_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult['item_table'];

}
function getItemTableId($item_table)
{
	$stQuery = "SELECT id FROM ex_item_type WHERE item_table= '$item_table' ORDER BY id desc";
	$arResult = exec_query($stQuery,1);
	return $arResult['id'];

}
function saveComponent($arData)
{
	$arCondition = array("module_id" => $arData['module_id'],"component_id" => $arData['component_id']);
	$retunValue = insert_or_update('layout_module_component_temp',$arData,$arCondition);
}
function deleteComponent($module_id,$component_id)
{
	$stGetQuery = "SELECT module_component_id FROM layout_module_component_temp WHERE module_id =".$module_id." AND component_id =".$component_id;
	$arResult = exec_query($stGetQuery,1);
	$inCompId =  $arResult['module_component_id'];
	$retunValue = del_query('layout_module_component_temp','module_component_id',$inCompId);
}
function setModuleForEdit($module_id)
{
	$stGetQuery = "SELECT module_id,component_id,component_type,content,target,link,content_sql FROM layout_module_component WHERE module_id =".$module_id;
	$arResult = exec_query($stGetQuery);
	del_query("layout_module_component_temp", "module_id", $module_id);
	foreach($arResult as $arRow)
	{
		foreach($arRow as $key => $value)
		{
			$arRow[$key] = addslashes($value);
		}
		insert_query("layout_module_component_temp",$arRow);
	}
}
//Returns true if teh username already exists in our DB
function isModuleNameExists($module_name,$module_id = NULL)
{
	$stQuery = "SELECT module_id FROM layout_module WHERE unique_name = '$module_name'";
	if($module_id != NULL)
	{
		$stQuery .= " AND module_id != '$module_id'";
	}
	$arResult = exec_query($stQuery);
	if(count($arResult)>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function isModuleEmpty($action,$module_id)
{
/*	if($action == 'add')
	{
		$stTable = 'layout_module_component_temp';
	}
	else
	{
		$stTable = 'layout_module_component';
	}*/
	$stTable = 'layout_module_component_temp';
	$stQuery = "SELECT component_id FROM $stTable WHERE module_id = '$module_id'";
	$arResult = exec_query($stQuery);
	if(count($arResult) == 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function getModuleDetail($module_id)
{
	$stQuery = "SELECT unique_name,template_id FROM layout_module WHERE module_id = '$module_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult;
}
function addEditModule($action,$temp_module_id,$module_name,$template_id)
{
	if($action == 'add') // in case of add module
	{
		$arData['unique_name'] = $module_name;
		$arData['template_id'] = $template_id;
		$arData['last_updated'] = mysqlNow();
		$module_id = insert_query("layout_module",$arData);
	}
	else // for edit module
	{
		$module_id = $temp_module_id;
		$arData['unique_name'] = $module_name;
		$arData['last_updated'] = mysqlNow();
		update_query("layout_module",$arData,array(module_id => $module_id));
		//Delete Entries from module Component table
		del_query("layout_module_component", "module_id", $module_id);
	}

 	$sqlQuery = "SELECT component_id,component_type,content,target,link,content_sql FROM layout_module_component_temp WHERE module_id = '$temp_module_id'";
	$arResult = exec_query($sqlQuery);
	foreach($arResult as $arRow) // Move data from temp table to main module_component table
	{
	   foreach($arRow as $key => $value)
	   {
	   	 $arRow[$key] = addslashes($value);
	   }
	   $arRow['module_id'] = $module_id;
	   insert_query("layout_module_component", $arRow);
	}
	//Delete Entries from Temp table
	del_query("layout_module_component_temp", "module_id", $temp_module_id);
	$obCache = new Cache();
	if($template_id == 9 || $template_id == 19)
	{
		$obCache->setFeaturedSlider();
		$obCache->getFeaturedSliderNoAd();
	}
	else
	{
		$obCache->setPageModuleCache($module_id);
	}
	return $module_id;
}
function getArticleDetail($article_id)
{
	$stQuery = "SELECT ar.id,ar.keyword,ar.blurb,EIM.url,ar.title, ar.body ,ar.contrib_id AS author_id, con.name AS author,
	 ar.character_text, chi.asset, IF(ar.publish_date= '0000-00-0000:00:00',ar.date,ar.publish_date) 
	 AS publish_date, ar.body 
	 FROM `contributors` AS con, articles AS ar 
	 LEFT JOIN character_images AS chi ON ar.character_img_id = chi.id 
	 LEFT JOIN ex_item_meta AS EIM ON EIM.item_id=ar.id AND item_type='1'
	 WHERE ar.id='$article_id' AND ar.contrib_id = con.id";
	$arResult = exec_query($stQuery,1);
	return $arResult;
}
// Code for managing module Order
function getPageAlias($page_id)
{
	$stQuery = "SELECT alias  FROM layout_pages WHERE id = '$page_id'";
	$arResult = exec_query($stQuery,1);
	return $arResult['alias'];
}
function getLayoutEditablePages()
{
	$stQuery = "SELECT lp.id , if(lm.title != '',lm.title,lp.name) AS name FROM layout_pages AS lp LEFT JOIN layout_meta AS lm ON lp.id = lm.page_id WHERE lp.is_editable = '1' ORDER BY name";
	$arPageResult = exec_query($stQuery);
	return $arPageResult;
}
function getPageList()
{
	$stQuery = "SELECT id, name FROM layout_pages where is_editable = '1' order by name asc";
	$arPageResult = exec_query($stQuery);
	return $arPageResult;
}
function getPagePlaceHolderList($page_id)
{
	$stQuery = "SELECT id,placeholder, module_order FROM layout_placeholder WHERE page_id='$page_id'";
	$arPageResult = exec_query($stQuery);
	return $arPageResult;
}
function getModulesDetail($placeholder_id)
{
	$stQuery = "SELECT module_order FROM layout_placeholder WHERE id='$placeholder_id'";
	$arPlaceholderRow = exec_query($stQuery,1);
	return $arPlaceholderRow['module_order'];
}
function getTempModulesDetail($placeholder_id)
{
	$stQuery = "SELECT module_order_temp FROM layout_placeholder WHERE id='$placeholder_id'";
	$arPlaceholderRow = exec_query($stQuery,1);
	return $arPlaceholderRow['module_order_temp'];
}
function updateModuleOrder($placeholder_id,$type)
{
	if($type == 'temp')
	{
		$stModuleOrder = getModulesDetail($placeholder_id);
		$arModuleDetail = array(module_order_temp =>$stModuleOrder);
	}
	else
	{
		$stTempModuleOrder = getTempModulesDetail($placeholder_id);
		$arModuleDetail = array(module_order =>$stTempModuleOrder);
	}
	$retunValue = update_query('layout_placeholder',$arModuleDetail,array(id => $placeholder_id));
	return $retunValue;
}
function getModuleList($placeholder_id,$type)
{
	if($type == 'temp')
	{
		$colName = "module_order_temp";
	}
	else
	{
		$colName = "module_order";
	}
	$stQuery = "SELECT $colName AS module_order FROM layout_placeholder WHERE id='$placeholder_id'";
	$placeholderRow = exec_query($stQuery,1);
	if($placeholderRow['module_order'])
	{
		$arModules = explode(",",$placeholderRow['module_order']);
	}
	$arModuleResult = array();
	if(count($arModules) > 0)
	{
		foreach($arModules as $module_id)
		{
			$stQuery = "SELECT module_id,unique_name FROM layout_module WHERE module_id = '".$module_id."'";
			$arModuleResult[] = exec_query($stQuery,1);
		}
	}
	return $arModuleResult;
}
function updateTempModuleOrder($placeholder_id,$module_order)
{
	$arModuleDetail = array(module_order_temp =>$module_order);
	$retunValue = update_query('layout_placeholder',$arModuleDetail,array(id => $placeholder_id));
	return $retunValue;
}
function getAllModuleList()
{
	$stQuery = "SELECT module_id,unique_name FROM layout_module ORDER BY unique_name";
	$arModuleResult = exec_query($stQuery);
	return $arModuleResult;
}
function getProfessorDetail($contrib_id)
{
	$stQuery = "SELECT name,bio_asset FROM contributors WHERE id = $contrib_id";
	$arResult = exec_query($stQuery,1);
	return $arResult;

}

function getlayoutmodule()
{

	 $stQuery = "select module_id,unique_name,template_id,last_updated from layout_module order by module_id";
	 $arModuleResult = exec_query($stQuery);


	return $arModuleResult;
}
function get_Branded_Logo()
{

	 $stQuery = "select id,name,assets,url from branded_images  order by name";
	 $arModuleResult = exec_query($stQuery);


	return $arModuleResult;
}
function get_Buzz_Branded_Logo()
{

	 $stQuery = "select id,name,assets1,assets2,url from buzz_branded_images  order by name";
	 $arModuleResult = exec_query($stQuery);


	return $arModuleResult;
}
function get_Branded_Article($id)
{

	 $stQuery = "select id,title,contrib_id from  articles where branded_img_id='$id'";
	 $arModuleResult = exec_query($stQuery);
	 return $arModuleResult;
}
function get_Branded_Buzz($id)
{

	 $stQuery = "select id,title,contrib_id from buzzbanter where branded_img_id='$id'";
	 $arModuleResult = exec_query($stQuery);
	 return $arModuleResult;
}
//-----------------------------------------------
//  listing menu functions(3/12/2008)
//-----------------------------------------------


function getfootermenu($act,$id)
{
  if($act=='All')
   {
	   $sql    =   "select * from layout_menu where navigation_type='F' order by menuorder ASC";
	   $result  =  exec_query($sql);
   }
   elseif($act=='Selected')
    {
       $sql    =   "select * from layout_menu where navigation_type='F' and id ='$id' ";
      $result  =  exec_query($sql);
    }
	elseif($act=='active')
   {
	   $sql    =   "select m.title,p.alias,p.url from layout_menu as m left join layout_pages as p
                   on m.page_id = p.id where m.active='1' and  m.navigation_type='F'  order by
                   m.menuorder ASC ";
	   $result  =  exec_query($sql);
   }
	 if($result)
	 {
		 return $result;
	 }
	 else
	    {
		  return NULL;
	    }
 }
//-----------------------------------------------
//  listing submenu functions(3/12/2008)
//-----------------------------------------------
function getlayoutsubmenu($id){
$sql     =   "select  * from layout_menu where parent_id='$id'  ORDER BY menuorder ASC";
$result  =  exec_query($sql);
return $result;
}
function getnamesubmenu($id){
$sql     =   "select  title from layout_menu where parent_id='$id'";
$result  =  exec_query($sql);
return $result;
}
function getnamesubmenucount($id){
$sql     =   "select  title from layout_menu where parent_id='$id' and  active='1'";
$result  =  exec_query($sql);
return $result;
}
//-----------------------------------------------
//  listing submenu functions(3/12/2008)
//-----------------------------------------------
function updatemainmenu($post_id,$post_title){
$cacheHeader= new Cache();
$queryupdate = "UPDATE layout_menu SET
				title        ='$post_title'
			 "."WHERE  id    = '$post_id'";

@$result   =  exec_query($queryupdate) ;
$cacheHeader->deleteHeaderMainNavigationCache();
return $result;
}
//-----------------------------------------------
//  listing submenu functions(3/12/2008)
//-----------------------------------------------
function getsubmenu($id){
 @$sql     =   "select  * from layout_menu where id='$id'";
@$result  =  exec_query($sql);

return $result;
}
function gettitle($id){
 $sql     =   "select  title from layout_menu where id='$id'";
$result  =  exec_query($sql);

return $result;
}
//-----------------------------------------------
//  listing action  functions(3/12/2008)
//-----------------------------------------------

function mainmanuactionperform($id,$act,$pid){
$cacheHeader= new Cache();
$query1  = "select menuorder,id from layout_menu where parent_id='$pid' ORDER BY menuorder ASC";
$result1  =  exec_query($query1);
foreach($result1 as $res)
{
  $menu[$res['id']]  =   $res['menuorder'];
  $submenu[]         =   $res['id'];
}

 if($act=='up')
 {
   $uphold = upclicked($id,$menu,$submenu);
 }
elseif($act=='down')
{
 $downhold = downclicked($id,$menu,$submenu);
}
elseif($act=='del')
{
 $sql      =   "DELETE FROM layout_menu WHERE id='$id'";
 $result   =  exec_query($sql);
}
$cacheHeader->deleteHeaderMainNavigationCache();
return $result;
}
/////////////
function footerorder($id,$act){
$query1  = "select menuorder,id from layout_menu where navigation_type='F' ORDER BY menuorder ASC";
@$result1  =  exec_query($query1);
foreach($result1 as $res)
{
  $menu[$res['id']]  =   $res['menuorder'];
  $submenu[]         =   $res['id'];
}

 if($act=='up')
 {
     $uphold = upclicked($id,$menu,$submenu);
 }
elseif($act=='down')
{
  $downhold = downclicked($id,$menu,$submenu);
}
return $result;
}
/* function for up order */
function upclicked($id,$menu,$submenu)
{
 $order_u = "Select menuorder from layout_menu where id= '$id'";
 $result1  =  exec_query($order_u);
 if($result1[0]['menuorder'] !='1')
 {
   $tmp        =array_search($id,$submenu);
   $tmp1       =$tmp-1;
   $order_id   =$submenu[$tmp1];
   $t1          =$menu[$order_id];
   $order_id1   =$menu[$id];
      $sql     = "UPDATE layout_menu SET
				  menuorder     ='$t1'
                 "."WHERE  id   = '$id'";
	@$result    =  exec_query($sql);
     $sql1     = "UPDATE layout_menu SET
			      menuorder   ='$order_id1'
				"."WHERE  id   = '$order_id'";
	@$result1  =  exec_query($sql1);

	}
return;
}
/* function for down order */
function downclicked($id,$menu,$submenu)
{
  $tmp      =array_search($id,$submenu);
  $tmp1     =$tmp+1;
  $order_id =$submenu[$tmp1];
  $t1       =$menu[$order_id];
  $order_id1=$menu[$id];
   $sql     = "UPDATE layout_menu SET
				menuorder     ='$t1'
			 "."WHERE  id   = '$id'";
  @$result  =  exec_query($sql);
  $sql1     = "UPDATE layout_menu SET
			  menuorder     ='$order_id1'
			"."WHERE  id   = '$order_id'";
 @$result1  =  exec_query($sql1);
 return;
}
function getArticleCategory()
{
		$qry="select section_id,name,type from section where subsection_type = 'article' and is_active='1'";
		return exec_query($qry);
}

function getEduCategory()
{
		$qry="SELECT id, menu_name FROM edu_nav_category WHERE is_active='1' AND is_category='1'";
		return exec_query($qry);
}

function getSubsectionList($section_id)
{
	$qry="select lm.id as menu_id FROM section AS s , layout_menu AS lm
			WHERE s.page_id = lm.page_id AND s.section_id = '$section_id'";
	$arMenuId = exec_query($qry,1);

	$qry="select s.section_id,lm1.id FROM section AS s ,layout_menu AS lm1, layout_menu AS lm2
			WHERE s.page_id = lm1.page_id AND lm1.parent_id = lm2.id AND lm2.id = '".$arMenuId['menu_id']."'";
	return exec_query($qry);
}


function getMetalist($id){
  	$qry="SELECT LM.id,LM.page_id,LM.title,LM.metadesc,LM.metakeywords,LP.url FROM layout_pages AS LP LEFT JOIN layout_meta AS LM
ON LM.page_id = LP.id WHERE LP.id='$id'";
	$result=exec_query($qry,1);
	if(isset($result)){
		return $result;
	}
}
function searchObject($keyword,$contrib_id,$mo,$day,$year,$object_type,$offset)
{
	  global $contentcount;
	  $offset=$offset*$contentcount;
	  if(!$offset){
		$offset=0;
	  }
	  $qry="select item_id as object_id ,item_type as object_type,content from ex_item_meta ";
	  if($keyword){
			$qry.="  where title LIKE '%$keyword%'";
		}
		if(is_numeric($contrib_id))
		{
			 if($keyword){
				$qry.=" and author_id='$contrib_id'";
			 }else{
				$qry.=" where author_id='$contrib_id'";
			 }
		}
		if($mo){
			if(($keyword) || is_numeric($contrib_id)){
				$qry.=" and month(publish_date)='$mo'";
			}else {
				$qry.=" where month(publish_date)='$mo'";
			}
		}
		if($day){
			if(($keyword) || is_numeric($contrib_id) || ($mo)){
				$qry.=" and day(publish_date)='$day'";
			} else {
				$qry.=" where day(publish_date)='$day'";
			}
		}
		if($year){
			if(($keyword) || is_numeric($contrib_id) || ($mo) || ($day)){
				$qry.=" and year(publish_date)='$year'";
			}else {
				$qry.="  where year(publish_date)='$year'";
			}
		}
		if($object_type){
			if(($keyword) || is_numeric($contrib_id) || ($mo) || ($day) || ($year)){
				$qry.=" and item_type='$object_type'";
			}else {
				$qry.="  where item_type='$object_type'";
			}
		}
		$qry.=" and is_live='1' order by publish_date desc";
		if(($contentcount)||($offset!=0))
		{
		 $qry.=" limit $offset,$contentcount";
		}
		// echo "<br>",$qry;
		$result=exec_query($qry);
		if(isset($result)){
			return $result;
		}
}
function searchArticles($keyword,$contrib_id,$mo,$day,$year,$object_type,$offset)
{
	  global $contentcount;
	  $offset=$offset*$contentcount;
	  if(!$offset){
		$offset=0;
	  }
	  $qry="select art.id,art.title,con.name author,art.date from articles art, contributors AS con where art.contrib_id = con.id ";
	  if($keyword){
			$qry.=" AND art.title LIKE '%$keyword%'";
		}
		if(is_numeric($contrib_id))
		{
			$qry.=" AND art.contrib_id='$contrib_id'";

		}
		if($mo){
				$qry.=" AND month(date)='$mo'";
		}
		if($day){
				$qry.=" AND day(date)='$day'";
		}
		if($year){
				$qry.=" AND year(date)='$year'";
		}
		$qry.=" order by date desc";
		if(($contentcount)||($offset!=0))
		{
		 $qry.=" limit $offset,$contentcount";
		}
		$result=exec_query($qry);
		if(isset($result)){
			return $result;
		}
}
function build_admin_lang($page)
{
	$qryLang="select term,text from ex_admin_lang  where page_name='".$page."'";
	$resLang=exec_query($qryLang);
	foreach($resLang as $id=>$value)
	{
		$lang[$value['term']]=$value['text'];
	}
	return $lang;
}

/*function getDailyFeed_today()
{
	$stQuery = "SELECT id,title,body,source,source_link,title_link,creation_date,updation_date from daily_feed where is_approved='1' and is_deleted='0' order by creation_date desc limit 0,10";
	$FeedResult = exec_query($stQuery);

	return $FeedResult;
}*/

function dailyfeed_count()
{
	$query = "SELECT id,title,body,title_link,creation_date,updation_date from daily_feed where is_approved='1' and is_deleted='0' order by creation_date desc";
	$result = exec_query($query);
	$total_count = count($result);

	return $total_count;
}

function getURL($url)
{
$url = "http://".$url;
return $url;
}
function getFeed($feedid) {
$sql = "SELECT id,title,body from daily_feed where id='$feedid' limit 1";

	$results = exec_query($sql);
	$feed = array();
	if (count($results) == 1) {
	foreach($results as $row){
		$feed['id'] = $row['id'];
		$feed['title'] = $row['title'];
		$feed['body'] = $row['body'];
	}
return $feed;
} else {
return null;
}
}


function update_email_topic_alert($feed_uid,$sec_id) {
	$qry = "SELECT section_ids FROM email_alert_sectionsubscribe where subscriber_id = '$feed_uid'";
	$result=exec_query($qry,1);
	if(count($result) != '0') {
		$user_sec_ids= $result['section_ids'];

		$explode_sec_id = explode(",",$user_sec_ids);

		$count_sec_ids = count($explode_sec_id);
		for($i=0;$i<=$count_sec_ids;$i++) {
			if($explode_sec_id[$i] == $sec_id) {
			$sec_exists = 1;
			break;
			}
			else {
			$sec_exists = 0;
			}
		}

	  if($sec_exists != 1) {
			if($user_sec_ids != '') {
				$last_character = substr($user_sec_ids, -1);
				if($last_character == ",") {
				$user_sec_ids = $user_sec_ids.$sec_id.",";
				}
				else {
				$user_sec_ids = $user_sec_ids.",".$sec_id.",";
				}
				}
				else {
				$user_sec_ids = ",".$sec_id.",";
				}

			$email_alert="1";
			$arrcategoryids = array(section_ids=>$user_sec_ids,email_alert=>$email_alert);
			subscribeTopicAlertMailChimp($feed_uid,$sec_id,'0');
			update_query("email_alert_sectionsubscribe",$arrcategoryids,array('subscriber_id'=>$feed_uid));
		}

	 else {
		return "already subscribed";
	 }
 }
 else {
 insert_email_topic_alert($feed_uid,$sec_id);
 }

}

   function insert_email_topic_alert($uid,$sec_id) {
	$categors = ",".$sec_id.",";
	$email_alert = '1';
	$categoriesarr = array(subscriber_id=>$uid,section_ids=>$categors,email_alert=>$email_alert);
	$catps = insert_query(email_alert_sectionsubscribe, $categoriesarr);
	subscribeTopicAlertMailChimp($uid,$sec_id,'0');
}

   function topic_welcome_email($user_type,$feed_email,$password,$section_name) {

	global $HTHOST, $HTPFX, $HTADMINHOST, $FORGOTPASS_FROM;
		   $userObj = new user();

		       $user_email = $feed_email;
		       // $user_email = 'sudeer@minyanville.com';
			   if(strtolower($section_name)=="5 things"){
					$section_name="Five Things";
				}
			   $subject="Welcome to ".ucfirst($section_name)." article from minyanville";
			   $from=$FORGOTPASS_FROM;
$EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_topic_welcome.htm?user_type=$user_type&user_id=$feed_email&pwd=$password&sct_name=".urlencode($section_name);
		      mymail($user_email,$from,$subject,inc_web("$EML_TMPL"));
  }





function update_email_category_alert($feed_uid,$cat_id) {
	$qry = "select category_ids from email_alert_categorysubscribe where subscriber_id = '$feed_uid'";
	$result=exec_query($qry,1);
	if(count($result) != '0') {
		$user_cat_ids= $result['category_ids'];

		$explode_cat_id = explode(",",$user_cat_ids);

		$count_cat_ids = count($explode_cat_id);
		for($i=0;$i<=$count_cat_ids;$i++) {
			if($explode_cat_id[$i] == $cat_id) {
			$cat_exists = 1;
			break;
			}
			else {
			$cat_exists = 0;
			}
		}

	  if($cat_exists != 1) {
			if($user_cat_ids != '') {
				$last_character = substr($user_cat_ids, -1);
				if($last_character == ",") {
				$user_cat_ids = $user_cat_ids.$cat_id.",";
				}
				else {
				$user_cat_ids = $user_cat_ids.",".$cat_id.",";
				}
				}
				else {
				$user_cat_ids = ",".$cat_id.",";
				}

			$arrcategoryids = array(category_ids=>$user_cat_ids,email_alert=>$email_alert);
			update_query("email_alert_categorysubscribe",$arrcategoryids,array('subscriber_id'=>$feed_uid));
		}

	 else {
		return "already subscribed";
	 }
 }
 else {
 insert_email_category_alert($feed_uid);
 }

}

function insert_email_category_alert($uid) {
	$categors = ",7,";
	$email_alert = '0';
	$categoriesarr = array(subscriber_id=>$uid,category_ids=>$categors,email_alert=>$email_alert);
	$catps = insert_query(email_alert_categorysubscribe, $categoriesarr);
}

function gen_trivial_password($len = 6)
{
 $r = '';
 for($i=0; $i<$len; $i++)
	 $r .= chr(rand(0, 25) + ord('a'));
 return $r;
}

function checkuser_dfemail_subscribed($feed_uid) {


$qry = "select category_ids from email_alert_categorysubscribe where subscriber_id = '$feed_uid'";
	$result=exec_query($qry,1);
	$user_cat_ids= $result['category_ids'];

	$cat_id = '7';

	if($user_cat_ids != '') {
	$explode_cat_id = explode(",",$user_cat_ids);

	$count_cat_ids = count($explode_cat_id);
	for($i=0;$i<=$count_cat_ids;$i++) {
		if($explode_cat_id[$i] == $cat_id) {
		$cat_exists = 1;
		break;
		}
		else {
		$cat_exists = 0;
		}
	}
	}

	return $cat_exists;
}


function daily_feed_welcome_email($user_type,$feed_email,$password) {

	global $HTHOST, $HTPFX, $HTADMINHOST, $FORGOTPASS_FROM;
		   $userObj = new user();

		       $user_email = $feed_email;
		       // $user_email = 'sudeer@minyanville.com';
			   $subject="Welcome to The Daily Feed from Minyanville";
			   $from=$FORGOTPASS_FROM;
			   $EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_daily_feed_welcome.htm?user_type=$user_type&user_id=$feed_email&pwd=$password";
		   mymail($user_email,$from,$subject,inc_web("$EML_TMPL"));
  }

 function updateDailyDigestEmail($feed_uid) {
 	global $D_R,$mailChimpApiKey,$dailyDigestListId;
	$qry = "select recv_daily_gazette,email,fname,lname from subscription where id = '$feed_uid'";
	$result=exec_query($qry,1);
	if($result['recv_daily_gazette']!= '0') {
		return "already subscribed";
	 }
	 else
	 {
	 	$arData['recv_daily_gazette'] =1;
		$id=update_query("subscription",$arData,array(id => $feed_uid));
		if(!empty($id)){
			/********** Subscribe user in the mailchimp list **********/
			include_once($D_R."/lib/config/_mailchimp_config.php");
			include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
			include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
			$objApi = new MCAPI($mailChimpApiKey);
			$merge_vars = array("FNAME"=>$result['fname'], "LNAME"=>$result['lname']);
			try{
				$subscribeStatus = $objApi->listSubscribe($dailyDigestListId, $result['email'], $merge_vars, 'html', false, true, false, false);
			}catch(Exception $e){
				$from= 'it@minyanville.com';
				$to = 'anshul.budhiraja@mediaagility.com';
				$subject = 'Daily Digest MailChimp Exception';
 				$body = var_dump($e);
                mymail($to,$from,$subject,$body);

			}
		}
		return $id;
	 }
 }

function dailyDigestWelcomeEmail($user_type,$feed_email,$password) {
	   global $D_R,$HTHOST, $HTPFX, $HTADMINHOST, $dailyDigestFromEmail,$dailyDigestFromName, $dailyDigestFromSubject;

	   $user_email = $feed_email;
	   $subject="Welcome to Minyanville's Newsletters";
	   $from[$dailyDigestFromEmail]=$dailyDigestFromName;

	  //$EML_TMPL = $HTPFX.$HTHOST."/emails/_eml_newsletter_welcome.htm?user_type=$user_type&user_id=$feed_email&pwd=$password";
	  // mymail($user_email,$from,$subject,inc_web("$EML_TMPL"));
	  	$dailyDigestWelcomeTmpl = $HTPFX.$HTHOST."/emails/_eml_newsletter_welcome.htm";
 	$msgurl=$dailyDigestWelcomeTmpl.qsa(array(user_type=>$user_type,user_id=>$feed_email,pwd=>$password));
	  	$mailbody=inc_web($msgurl);
         mymail($to,$from,$subject,$mailbody);

}


?>
