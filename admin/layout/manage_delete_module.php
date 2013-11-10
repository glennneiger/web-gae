<?
include_once("$D_R/lib/_layout_data_lib.php");
$page = 'Layout Module';
// $_REQUEST variable
$module_id = $_REQUEST['module_id'];
$method    = $_REQUEST['method'];
 if($method=='del')
 {
   $query   = "select DISTINCT(page_id) from layout_placeholder WHERE FIND_IN_SET($module_id,module_order)";
   @$result1  =  exec_query($query);
 
   if(count($result1)>0)
   {
    $pagetitle ='';
     foreach($result1 as $pages)
	 {
	    $pages =$pages['page_id'];
	    $pagename         = "SELECT lp.id , if(lm.title != '',lm.title,lp.name) AS name FROM layout_pages AS lp LEFT JOIN layout_meta AS lm ON lp.id = lm.page_id WHERE lp.id IN($pages)";
	    $pagenameresult   =  exec_query($pagename);
	    $pagetitle[]       =  $pagenameresult[0]['name'];
	  }
	    @$page_title       =   implode(', ', $pagetitle);
		if($page_title=='')
		{
		  $page_title="Some page(s)";
		}
		else
		{
		 $page_title = "'$page_title'";
		}
	   $message1 =  "<b>This module is active on $page_title, Please first remove it from there.</b>";
      
   }
   else
   {
        $sql="DELETE FROM layout_module WHERE module_id='$module_id'";
        @$result= exec_query($sql);
	    $sql_module="DELETE FROM layout_module_component WHERE module_id='$module_id'";
        @$result_module= exec_query(sql_module);
	    //$message =  build_admin_lang($page);
		/*$memCache = new memCacheObj();
		$stKey="module_".$module_id;
		$memCache->setKey($stKey, "");*/
		$message2 =  "<b>This module is deleted successfully.</b>";
   }
 }
 
// for get  module 
$getlayoutmodule = getlayoutmodule(); 
?>
<table width="100%" border="0" cellpadding="2" cellspacing="2" class="admin_container" >
<? if(isset($message1)){ ?>
<tr>
    <td colspan="3" valign="middle" align="center"><font  color="#FF0000"><?=$message1 ?></font></td>
    
  </tr>
  <? } ?>
  <? if(isset($message2)){ ?>
<tr>
    <td colspan="3" valign="middle" align="center"><font   color="#0033FF"><?=$message2 ?></font></td>
  </tr>
  <? } ?>
  <tr>
  
    <td width="10%" valign="middle" class="admin_module_head" align="left">S.No</td>
    <td width="79%" valign="middle" class="admin_module_head" align="left">Module Name</td>
     <td width="11%" valign="middle" class="admin_module_head" align="left">Action</td>
  </tr>
  <? if(count($getlayoutmodule)>0)
  {
  $i=1;
   foreach($getlayoutmodule as $module)
   { 
  ?>
  <tr class="highlight_row" >
   <td width="10%" valign="middle" ><?= $i ?></td>
    <td width="79%" valign="middle" ><?= htmlentities($module['unique_name']) ?></td>
     <td width="11%" valign="middle">
	 <a  href="#" onClick="module_confirm('placeholder_modules','<?= $module['module_id'] ?>','','manage_delete_module.php','del')" >delete</a></td>
  </tr>
  <? $i++;}} ?>
</table>