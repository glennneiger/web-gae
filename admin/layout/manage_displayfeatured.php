<?
// Post value
$action     = $_REQUEST['action']=='HOME_PAGE_FEATURED_MODULE' ?  'Featured' : 'Recent';
$module_id  = $_REQUEST['module_id'];
$method     = $_REQUEST['method'];

/*delete cache homepage*/
$objcache= new Cache();
// action module 
if($method=='del')
{
  $sql="DELETE FROM homepage_module WHERE id='$module_id'";
   @$result= exec_query($sql);
   $objcache->deleteCacheHomePage();	
}
elseif($method=='save')
{
 $sql       = "UPDATE homepage_module SET 
			    commit_status     ='P'				   									  
             "."WHERE  id   = '$module_id'";
@$result   =  exec_query($sql);
$objcache->deleteCacheHomePage();	
}
elseif($method=='up')
{
 $holdup = up($module_id);
 $objcache->deleteCacheHomePage();	
}
elseif($method=='down')
{
 $holddown = down($module_id);
 $objcache->deleteCacheHomePage();	
}
################# Result #####################################
$arrfeature = getFeatured($action);
####################################################
// function for up order
function up($id)
{
 $query1   = "select order_type,id from homepage_module where module_type='Recent' ORDER BY order_type ASC";
 $result1  =  exec_query($query1);
// for each
foreach($result1 as $res)
{
  $menu[$res['id']]  =   $res['order_type'];
  $submenu[]         =   $res['id'];
}
 $order_u = "Select order_type from homepage_module where id= '$id'";
 @$result1  =  exec_query($order_u);
 if($result1[0]['order_type'] !='1')
 {
    $tmp        =array_search($id,$submenu);
    $tmp1       =$tmp-1;
    $order_id   =$submenu[$tmp1];
    $t1          =$menu[$order_id];
    $order_id1   =$menu[$id];
      $sql       = "UPDATE homepage_module SET 
				   order_type     ='$t1'				   									  
                   "."WHERE  id   = '$id'";
	@$result     =  exec_query($sql);
    $sql1        = "UPDATE homepage_module SET 
			       order_type   ='$order_id1'				   									  
				  "."WHERE  id   = '$order_id'";
	@$result1   =  exec_query($sql1);
	}

return;
}
// for down link
/* function for down order */
function down($id)
{
 $query1   = "select order_type,id from homepage_module where module_type='Recent' ORDER BY order_type ASC";
 $result1  =  exec_query($query1);
// for each
foreach($result1 as $res)
{
  $menu[$res['id']]  =   $res['order_type'];
  $submenu[]         =   $res['id'];
}
  $tmp      =array_search($id,$submenu);
  $tmp1     =$tmp+1;
  $order_id =$submenu[$tmp1];
  $t1       =$menu[$order_id];
  $order_id1=$menu[$id];
  @$sql     = "UPDATE homepage_module SET 
			   order_type     ='$t1'				   									  
			 "."WHERE  id   = '$id'";
  @$result  =  exec_query($sql);
  $sql1     = "UPDATE homepage_module SET 
			  order_type     ='$order_id1'				   									  
			"."WHERE  id   = '$order_id'";
 @$result1  =  exec_query($sql1);
 return;
} 
?>
<? if($_REQUEST['action']=='HOME_PAGE_FEATURED_MODULE'){?>
<table width="100%" cellpadding="5" cellspacing="2" border="0" class="admin_container">	
	<tr >
		<td class="admin_module_head">Featured Slide</td>
		<td class="admin_module_head">Actions</td>
		<? if(count($arrfeature)>0){
		 foreach($arrfeature as $res)
		 {
		?>
	   </tr>
		<tr  class="highlight_row">
		<td><?= $res['object_title'] ?></td>
		<td><a href="JavaScript:void(0);"  onClick="openAddEditfeaturedComponent('edit','<?= $res['id'] ?>')">edit</a>
		<? if($res['commit_status'] =='T'){ ?>
		&nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0);"  onClick="mangemenuarticle('placeholder_modules','<?= $res['id'] ?>','HOME_PAGE_FEATURED_MODULE','manage_displayfeatured.php','save')")>save</a>
		<? }?></td>
	</tr>
	
	
	<? } 
	if(count($arrfeature)>'0')
	{
	?>
	 </tr>
		<tr class="highlight_row">
		<td align="left" valign="middle" colspan="2">
		<input type="button" name="Preview" value="Preview module"  onclick="preview('<?= $HTPFX.$HTHOST ?>/home.htm?act=Preview&mod=featured');" class="submit_button"/>
		
		</td>
	</tr>
	<?
	}
	} else { ?>
	</tr>
		<tr class="highlight_row">
		<td colspan="3" align="center" class="no_data"><?= NO_MODULE_FOR_PLACEHOLDER ?></td>
		
	</tr>
	<? }?>
</table>
<? }else{?>
<table width="100%" cellpadding="5" cellspacing="2" border="0" class="admin_container">	
	<tr>
		<td width="35%" class="admin_module_head">RECENTLY IN THE VILLE</td>
		<td width="30%" class="admin_module_head">Actions</td>
	</tr>
	<? if(count($arrfeature)>0){
	 foreach($arrfeature as $res)
		 {
	?>
		<tr class="highlight_row">
		<? if($res['object_type']=='1'){?>
		<td><?= $res['title'] ?></td>
		<? } else{?>
		<td><?= $res['vtitle'] ?></td>
		<? }?>
		<td>
		<a href="JavaScript:void(0);"  onClick="openAddEditfeaturedComponent('edit','<?= $res['id'] ?>')">edit</a>
		&nbsp;&nbsp;
		<a href="JavaScript:void(0);"  
		onClick="mangemenuarticle('placeholder_modules','<?= $res['id'] ?>','Recent In Vil','manage_displayfeatured.php','del')">delete</a>
		&nbsp;&nbsp;
		<a href="JavaScript:void(0);"  
		onClick="mangemenuarticle('placeholder_modules','<?= $res['id'] ?>','Recent In Vil','manage_displayfeatured.php','up')">up</a>&nbsp;&nbsp;
		<a href="JavaScript:void(0);"  
		onClick="mangemenuarticle('placeholder_modules','<?= $res['id'] ?>','Recent In Vil','manage_displayfeatured.php','down')">down</a>
		<? if($res['commit_status'] =='T'){ ?>
		&nbsp;&nbsp;
		<a href="JavaScript:void(0);"  onClick="mangemenuarticle('placeholder_modules','<?= $res['id'] ?>','Recent In Vil','manage_displayfeatured.php','save')">save</a>
		<? }?>
		</td>
	</tr>
	<? } ?>
	 <? } else{ ?>
		<tr class="highlight_row">
	<td colspan="3" align="center" class="no_data"><?= NO_MODULE_FOR_PLACEHOLDER ?></td>
	</tr>
	<? }?>
</table>
<table >
 </tr>
 <tr class="highlight_row">
<td align="left" valign="middle" colspan="2">
<input type="button" name="Preview" value="Preview module"  onclick="preview('<?= $HTPFX.$HTHOST ?>/index.htm?act=Preview&mod=invill');" class="submit_button" style="cursor: pointer;"/>
&nbsp;&nbsp;&nbsp;
<a  onClick="openAddEditfeaturedComponent('add','')"  ><input type="button" name="add" 
value="Add Article/Video"  style="cursor: pointer;" class="submit_button"/ ></a>
</td>
</tr>
</table>
<? }

?>