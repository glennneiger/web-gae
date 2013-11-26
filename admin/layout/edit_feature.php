<?php 
include_once("AjaxFileUploader.inc.php");
 $stAction      = $_REQUEST['action'];
 $Id            = $_REQUEST['module_id'];
if($stAction=='edit' )
{
$sql="select object_id,object_type,image_path from homepage_module where id ='$Id'";
$result= exec_query($sql);
@$imgpath = $result[0]['image_path'];
if($result[0]['object_type']=='1')
	{

	 $a= $result[0]['object_id'];
	  $sql_a="select id,title,date from articles where id ='$a'";

	 @$result_a= exec_query($sql_a);
	}
else
	{
	 $a= $result[0]['object_id'];
	 $sql_a="select id, title,publish_time from mvtv where id ='$a'";
	 @$result_a= exec_query($sql_a);
	}
	if($stAction=='edit')
	{
foreach($result_a as $res)
{
   @$value = $res['id'].":".$result[0]['object_type'];
   @$arSelectedValue[$key] =  $res['title'];

}
}
}
?>
<form name="frm" method="post" action=""   onsubmit="return false;">
<input type="hidden" name="action" value="<?=$_REQUEST['action'] ?>"  />
<input type="hidden" name="id" value="<?=$_GET['id'] ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td  valign="middle" align="right"><a style="float:right" onclick="iboxclose();"><img src="<?=$HTPFX.$HTHOST?>/admin/assets/login_close.jpg" border="0" align="right"/></a></td></tr>
<tr><td>
 <table width="90%" border="0" cellspacing="0" cellpadding="2" align="center">

<tr><td colspan="3" valign="middle">&nbsp;</td></tr>	
 <tr class="highlight_row">
<td colspan="6" class="common_heading" valign="middle">Search: <input type="text" id="txtKeyword" class="search_textbox" /></td>
				  </tr>
				  <tr class="highlight_row" >					
					<td nowrap="nowrap" valign="middle">sort by:</td>
					<td class="search_drop_down" nowrap="nowrap" valign="middle">
						<? month_options("selMonth",$mo,"class='inputCombo'","selMonth") ?>
						<? day_options("selDate",$day,"class='inputCombo'","selDate",$valday) ?>
						<? year_options("selYear",$year,"class='inputCombo'",date("Y"),2002,"selYear") ?>
					</td>
					<td valign="middle"><select name="selAuthor" id="selAuthor">
					<option value="">-All Authors-</option>
						<? selectHashArr(get_active_contributors(),"id","name",$contrib_id)?>
					</select></td>
				  </tr>
				  <tr class="highlight_row">
				<td colspan="3" align="right" valign="middle"><input type="button" value="search" onClick="searchrecent();" 
				 class="submit_button" style="cursor: pointer;"/></td>
			</table></td></tr>
			 <tr><td valign="middle"><table width="90%" border="0" cellspacing="0" cellpadding="2" align="center" >
			<tr><td valign="middle"><div id="search_result_container"></div></td></tr>
			<? htmlprint_r($arSelectedKey);?>		
			<tr><td style="padding-left:10px" valign="middle">
			<select name="selItemList" MULTIPLE id="selItemList" style="width:200px; background:white;color:black;width:300px;"               size="7" ><? 
			      if($value!='')
				  {
			      echo "<option value='$value'>$arSelectedValue[$key]</option>";
				  }
			?>
			</select>
			</td></tr>
			<tr class="highlight_row"><td valign="middle" align="left" style="padding-left:10px">
			<input type="button" name="removeItem" value="Remove Selected" onclick="removeItems('selItemList')" class="submit_button" style="cursor: pointer;">
			</td></tr></table>
			</form>
			<tr><td>
			<table align="center"  width="90%" ><tr><td  valign="middle">
			<input type="hidden" id="hidImageAction" value="<?=$stAction?>" />
			<div>
			<? $ajaxFileUploader = new AjaxFileuploader($uploadDirectory="$D_R/assets/featureimage");	
	      	 echo $ajaxFileUploader->showFileUploader('imageuplode1',$stAction);?>
		   </div></td></tr>
	    <tr><td  valign="middle" align="left" height="60"  style="padding-left:180px">
	<? if($stAction=='add'){ ?>
			&nbsp;&nbsp;<input type="button" name="Submit" value="Add Article/Video" onclick="save_featured_Component('<?=$_REQUEST['action']?>','')" class="submit_button" style="cursor: pointer;">
			<? } else{?>
			&nbsp;&nbsp;<input type="button" name="Update" value="Update Article/Video"  onclick=" save_featured_Component('<?=$_REQUEST['action']?>','<?=$_REQUEST['module_id'] ?>')" class="submit_button" style="cursor: pointer;">
			<? }
/*delete cache homepage*/
$qry="Select LP.page_id, LG.name as pagename from layout_placeholder LP,layout_pages LG where find_in_set('".$_REQUEST['module_id']."',module_order)
 and LP.page_id=LG.id";
$pagename=exec_query($qry,1);

if($pagename['pagename']=='home'){
	$objcache= new Cache();
    $objcache->deleteCacheHomePage();	
}
?>
			</td></tr>
			</table>