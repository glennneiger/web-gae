<?
global $HTPFX,$HTHOST,$D_R;
include_once("$D_R/lib/_layout_data_lib.php");
$page = 'Layout Module';
// $_REQUEST variable
$module_id = $_REQUEST['module_id'];
$method    = $_REQUEST['method'];
$action    = $_REQUEST['action'];
$assets    = $_REQUEST['assets'];
$cnt       = 0;
 if($method=='dellogo')
 {
    $query   = "select id  from  articles where  branded_img_id='$module_id';";
    @$result1  =  exec_query($query);
   if(count($result1)>0)
   {
    $Branded_Article = get_Branded_Article($module_id);
	$string ="<div><table width='100%' border='0' cellpadding='1' cellspacing='1'>
	          <tr ><td valign='middle' align='left'  colspan='2'><font color='#FF0000'><b>First Delete Article Branded Logo
			   From The Following Article(s)</b></font></td>
			  <td valign='middle' align='right'><a onclick='displayRow()'>Hide Article(s)</a></td>
              </tr>";

	if(count($Branded_Article)>0)
  {
  $j=1;
   foreach($Branded_Article as $module)
   {
   $string .="<tr class='highlight_row'><td width='15%' valign='middle' align='left'>$j</td>
              <td valign='middle' width='58%' align='left'>".trim(htmlentities($module['title']))."</td>
              <td width='27%' valign='middle' align='left'>
			  <a  href='".$HTPFX.$HTHOST."/admin/articles.htm?contributor_id=".$module['contrib_id'] ."&id=".$module['id']. "'
			   TARGET='_blank'>
			   edit</a></td></tr>";

			   $j++;
   }
   }
    $string .=" </table></div>";
   }
    else
    {
           $query1   = "DELETE FROM branded_images WHERE id='$module_id'";
           @$result   =  exec_query($query1);
		   if(isset($result))
		  {
		      @unlink($D_R.$assets);
		      $message =  "<font  color='#0033FF'>$action deleted successfully.</font>";
		  }
   }
 }
// for get  module
$getlayoutmodule = get_Branded_Logo();
?>
<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/layout.1.13.js"></script>
<table width="100%" border="0" cellpadding="2" cellspacing="2" class="admin_container">
  <? if(isset($message)){ ?>
<tr>
    <td colspan="3" valign="middle" align="center"><b><?=$message ?></b></td>
  </tr>
  <? } ?>
  <tr>
   <td width="15%" valign="middle" class="admin_module_head" align="left">S.NO</td>
    <td width="58%" valign="middle" class="admin_module_head" align="left">Article Branded Logo</td>
     <td width="27%" valign="middle" class="admin_module_head" align="left">Action</td>
  </tr>
  <? if(count($getlayoutmodule)>0)
  {
  $i=1;
   foreach($getlayoutmodule as $module)
   {
  ?>
  <tr class="highlight_row" >
  <td width="15%" valign="middle" align="left"><?=$i ?></td>
    <td width="58%" valign="middle" ><?= trim(htmlentities($module['name'])) ?></td>
     <td width="27%" valign="middle">
	 <a onClick="disp_confirm('placeholder_modules','<?= $module['id'] ?>','<?=$module['name']?>','listbrandedlogo.php','dellogo','<?= $module['assets'] ?>')" >delete</a></td>
  </tr>
  <? if(isset($string) and  $module['id']== $module_id)
  {
  ?>
  <tr  id="hide" >
 <td colspan="3"><?=$string ?></td>
  </tr>
  <? } ?>
  <? $i++;
  }
  }
  ?>
</table>
