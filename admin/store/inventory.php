<?	include("./_header.php");

/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$store=new Store();
$store->admin=1;

$inventory=array();
$postto="inventory.mod.php";
if(!$s)$s="ordr";
if($store->isValidProductId($id)){
	$inventory=$store->getInventory($id,0,$s,$d);
	$product=$store->getProducts($id,$cat_id);
	$ptitle=$product[title];
	$cattitle=$store->getCategories($cat_id);
	$cattitle=$cattitle[title];
}else{
	unset($id);
}
?>
<script>
function Save(){
	findObj("theform").submit();
}
function products(){
	findObj("refer").value="products.php";
	Save();
}
function checkAll(){
	var frm=findObj("theform");
	var checktarget=findObj("checkstatus").checked;
	for(i=0;i<frm.elements.length;i++){
		var el=frm.elements[i];
		if(!el.name)continue;
		if(el.type!="checkbox")continue;
		if( stristr("del[",el.name)){
			el.checked=checktarget;
		}
	}
}
function setFoc(){
	findObj("new[sku]").focus();
}
<?if($id){?>window.onload=setFoc;<?}?>
</script>
<!-- =================== navigation ==================== -->

<center>
<table border=0><TR><TD align=right>
	<form method=get>
	Choose a category:
	<select name="cat_id" onChange="this.form.submit()">
		<option value="">---Products in all Categories----</option>
		<?selectHashArr($store->getCategories(0,"title"), "id","title",$cat_id)?>
	</select>

	</form>
</TD></TR>

<TR><TD align=right>
	<form method=get>
	Choose a product to edit SKUS: 
	<?input_hidden("cat_id")?>
	<select name="id" onChange="this.form.submit()">
		<option value="">--Select a product---</option>
		<?selectHashArr($store->getProducts(0,$cat_id,0,"title"), "id","title",$id)?>
	</select>
	</form>
</TD></TR>
</table>
</center>

<!-- ==================== label ======================== -->



<?if(!$id){ //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!ghost inventory
	include("./_footer.php");
	exit;
}//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!?>

<form name="theform" action="<?=$postto?>" method="post" enctype="multipart/form-data">
<?
	input_hidden("id");
	input_hidden("cat_id");
	input_hidden("refer",$PHP_SELF);
	input_hidden("s");
	input_hidden("d");
	input_hidden("showthumbs");
?>
<!-- =================== inventory edit ==================== -->
<table width=450 align=center><TR><TD>
<br>

	<?if($cat_id){?><a href="categories.php?cat_id=<?=$cat_id?>">Edit category information</a> |<?}?>
	<?if($id){?><a href="products.php?id=<?=$id?>">Edit genderal info for this product</a> |<?}?>
	<a href="products.php?cat_id=<?=$cat_id?>">Create a new product</a>
<?if(count($inventory)){?>
|	<a href="inv.arrange.php?cat_id=<?=$cat_id?>&id=<?=$id?>" target="_search">Arrange SKUs for this product</a>
<?}?>

<?if(count($inventory)){?>
<fieldset>
	<legend>Edit Skus</legend>
	<table>
	<TR style="background:#999999" valign=bottom>
	<TD bgcolor="#999999" nowrap>
	Delete<br><input type="checkbox" name="checkstatus" onclick="checkAll()" align=top></TD>
	<TD><a href="<?=$PHP_SELF.qsa(array(s=>"sku", d=>!$d))?>">SKU</a></TD>
	<?foreach(range(1,3) as $prm){
		$pkey="param${prm}_name";
		if(!$product[$pkey])continue;
	?><TD style="background:#ccc"><a href="<?=$PHP_SELF.qsa(array(s=>"param$prm", d=>!$d))?>"><b><?=ucwords($product[$pkey])?></b></a></TD>
	<?}?>	
	<TD><a href="<?=$PHP_SELF.qsa(array(s=>"stock", d=>!$d))?>">Stock</a></TD>
	<TD><a href="<?=$PHP_SELF.qsa(array(s=>"weight", d=>!$d))?>">Weight</a></TD>
	<TD><a href="<?=$PHP_SELF.qsa(array(s=>"retail", d=>!$d))?>">Price</a></TD>
	<TD><a href="<?=$PHP_SELF.qsa(array(s=>"description", d=>!$d))?>">Addl Descr.</a></TD>	
<!-- 	<TD>Image</TD> -->
	</TR>
	<?foreach($inventory as $row){	
		$inv_id=$row[id];
	?>	
		<TR style="background:#eee" valign=top>
		<TD bgcolor="#999999"><input type="checkbox" name="del[<?=$inv_id?>]"></TD>
		<TD><?input_text("inv[$inv_id][sku]",$row[sku])?></TD>
		<?foreach(range(1,3) as $prm){
			$pkey="param${prm}_name";
			if(!$product[$pkey]){
				input_hidden("inv[$inv_id][param$prm]","");
				continue;
			}?>
		<TD style="background:#ccc"><?input_text("inv[$inv_id][param$prm]",$row["param$prm"],15,25)?></TD>
	<?}?>
		<TD><?input_text("inv[$inv_id][stock]",$row[stock],5)?></TD>
		<TD><?input_text("inv[$inv_id][weight]",$row[weight],10)?></TD>
		<TD><?input_numsonly("inv[$inv_id][retail]",dollarf($row[retail]),8)?></TD>
		<TD><textarea name="inv[<?=$inv_id?>][description]" style="width:100px;height:30px"><?=$row[description]?></textarea></TD>
<!-- 		<TD nowrap>
			<?if($row[images][big]){?>
				<div style="border:1px #ccc solid;background:#fff">
				<a href="<?=$row[images][big]?>" target="_search">
				<img src="/lib/richedit/images/tool-image.gif" 
				border=0 alt="view image" align=absmiddle style="padding-right:10px"></a>
				<input type="checkbox" 
					name="delfile[<?=$inv_id?>]" 
					id="delfile[<?=$inv_id?>]">
						<label for="delfile[<?=$inv_id?>]">Delete</label>
				</div>
			<?}else{?>
				<input type="file" name="file_<?=$inv_id?>" style="width:100px" onblur="void(this.title=this.value)">
			<?}?>
		</TD> -->
		</TR>
	<?}?>
	</table>
</fieldset>
<?}?>
<!-- =================== inventory creation ==================== -->
<fieldset>
	<legend>Enter a New SKU</legend>
	<table>
	<TR style="background:#eee" valign=bottom>
	<TD>SKU</TD>
	<?foreach(range(1,$store->paramLength) as $prm){
			$pkey="param${prm}_name";
			if(!$product[$pkey])continue;?>
		<TD style="background:#ccc"><b><?=ucwords($product[$pkey])?></b></TD>
	<?}?>
	<TD># In Stock</TD>
	<TD>Weight</TD>
	<TD>Price</TD>
	<TD>Addl. Descr.</TD>
<!-- 	<TD>Image</TD> -->
	</TR>
	<TR valign=top>
	<TD><?input_text("new[sku]")?></TD>
	<?foreach(range(1,$store->paramLength) as $prm){
		if(!$product["param${prm}_name"])continue;
	?><TD style="background:#ccc"><?input_text("new[param$prm]","",15,25)?></TD>
	<?}?>
	<TD><?input_text("new[stock]","",5)?></TD>
	<TD><?input_text("new[weight]","",10)?></TD>
	<TD><?input_numsonly("new[retail]","",8)?></TD>
	<TD><textarea name="new[description]" style="width:100px;height:30px"></textarea></TD>
<!-- 	<TD><input type="file" name="newimage" style="width:100px" onblur="void(this.title=this.value)"></TD> -->
	</tr>
	</table>
</fieldset>

<!-- ================ save everything ============= -->

<fieldset><legend>Save your Work</legend>
	&nbsp;<br><div align=right>
	<INPUT TYPE="button" VALUE="Save" onclick="Save()"> &nbsp; 
	<input type=reset value="Reset"> &nbsp; 
	<?if($id){?>
		<input type="button" value="Edit Product" onclick="products()">
	<?}?>
	</div><br>&nbsp;
</fieldset>


</form>
</TD></TR></table>

<?include("./_footer.php");?>