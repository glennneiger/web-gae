<?
global $cloudStorageTool,$HTPFX,$HTHOST,$D_R;
include("$ADMIN_PATH/_header.htm");
include_once("$D_R/lib/_cart.php");

$postto= $HTPFX.$HTHOST."/admin/store/products.mod.php";

/*============set up permissions=================*/
$adminall=$AD->getPerm(array("admin_users_all", "shops_edit"));

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$store=new Store();
$store->admin=1;
if($store->isValidProductId($id)){
	$product=$store->getProducts($id);
}else{
	unset($id);
}

if($store->isValidCategoryId($cat_id)){
	$cat=$store->getCategories($cat_id);
	if(!$id){
		$product[cat_ids]=array($cat_id);
	}
}else{
	unset($cat_id);
}
$parameters=$store->getParameters();

?>
<script>
function confirmDelete(){
	theForm=document["product_form"];
	if(confirm("Are you sure you want to delete this product?")){
		theForm["deleteproduct"].value="true";
		theForm.submit();
		return;
	}
	theForm["deleteproduct"].value="";
}

function clearTxt(srcObj){
	findObj(srcObj).value="";
}
function Save(){
	var err="";
	if(!getValByName('prod[title]'))
		err+=" Give the product a name\n";
	if(err.length){
		alert(err);
		return;
	}
	findObj('product_form').submit()
		
}
function inventory(){
	findObj("refer").value="inventory.php";
	Save()
}
</script>

<CENTER>

<?if($error){?><div class=error><?=strip($error)?></div><?}?>




<table border=0><TR><TD align=right>
	<form method=get style="margin:0px;padding:0px">
	Choose a category under which to edit products: 
	<select name="cat_id" onChange="this.form.submit()">
		<option value="">---Products in all Categories----</option>
		<?selectHashArr($store->getCategories(0,"title"), "id","title",$cat_id)?>
	</select>

	</form>
</TD></TR>
<TR><TD>&nbsp;</TD></TR>
<TR><TD align=right>
	<form method=get style="margin:0px;padding:0px">
	Choose a product to edit: 
	<?input_hidden("cat_id")?>
	<select name="id" onChange="this.form.submit()">
		<option value="">----New Product----</option>
		<?selectHashArr($store->getProducts(0,$cat_id,0,"title"), "id","title",$id)?>
	</select>
	</form>
</TD></TR>
</table>


	
<FORM METHOD="POST" action="<?=$postto?>" ENCTYPE="multipart/form-data" name="product_form" style="margin:0px;padding:0px">
<?input_hidden("id")?>
<?input_hidden("edit",$id)?>
<?input_hidden("deleteproduct",0)?>
<?input_hidden("refer",$PHP_SELF)?>
<?input_hidden("cat_id")?>

<table border=0 width=450>
<TR valign=top>
<TD>
<br>
	<div style="text-align:left">
	<?if($id){?>
		<a href="inventory.php?id=<?=$id?>">Edit Inventory for this product</a> |
	<?}?>
	<?if($cat_id){?>
		<a href="categories.php?cat_id=<?=$cat_id?>">Edit category information</a> 
	<?}?>

<fieldset><legend >Product Description</legend>
		<table cellpadding=0 cellspacing=2 border=0 width=100%>
		<TR>
		<TD align=right nowrap>Product Name:</td>
		<td><?input_text("prod[title]",$product[title],"","","style=width:100%")?></TD>
		</TR>
		<TR>
		<TD align=right nowrap>Order in List:</td>
		<td><?input_numsonly("prod[ordr]",$product[ordr],3)?>
			<a href="arrange.php?cat_id=<?=$cat_id?>" target="_search">Arrange Products</a>
		</TD>
		</TR>
		<?foreach(range(1,$store->paramLength) as $i){?>
			<TR>
			<TD align=right>Property <?=$i?>:</TD>
			<TD>
			<select name="prod[param<?=$i?>_name]">
			<option value="">--Choose--</option>
			<?selectHashArr($parameters, "name","name",$product["param${i}_name"])?>
			</select>
			</TD>
			</tr>
		<?}?>
		<TR>
		<TD>&nbsp;</TD>
		<TD><a href="params.php" target="_search">Add Parameters</a></TD>
		</TR>
		<TR>
		<TR>
		<TD align=right nowrap>Minimum Purchase:</td>
		<td><?input_intonly("prod[min_purchase_count]",$product[min_purchase_count],2,2)?></TD>
		</TR>	
		<TR valign=top>
		<TD align=right nowrap>Long Description:</td>
		<TD><TEXTAREA NAME="prod[description]" style="width:100%;height:100px;"><?=htmlspecialchars($product[description])?></TEXTAREA>
		</TD>
		</TR>
		<TR valign=top>
		<TD align=right>Categories:</TD>
		<TD nowrap>
		<SELECT NAME="prod[cat_ids][]" multiple align=middle>
		<?selectHashArr($store->getCategories(0,"title"),"id","title",$product[cat_ids])?>
		</select>
		Use [ctrl] key to select multiple</TD>
		</TR>
<!-- 		<TR valign=top>
		<TD align=right>Related Products:</TD>
		<TD>
		<SELECT NAME="prod[prod_ids][]" multiple align=middle>
		<?/*selectHashArr($store->getOtherProductList($product[id]),"id","title",$product[prod_ids])*/?>
		</select><br>
		Use [ctrl] key to select multiple<br>&nbsp;
		</TD>
		</TR>  -->
		<TR>
		<TD nowrap>Show on web site?</TD>
		<TD>
			<?input_radio("prod[enabled]",1,$product[enabled])?>
			<label for="prod[enabled]1">Yes</label>
			<?input_radio("prod[enabled]",0,$product[enabled])?>
			<label for="prod[enabled]0">No</label>
		</TD>
		</TR>
		</table>
</fieldset>

<fieldset><legend>Product Image:</legend>
	<table cellpadding=0 cellspacing=2 border=0>
	<TR>
	<?if($product[images][tn]){?>
	<TD align=right>Product Image:</td>
	<TD nowrap><div style="border:1px black solid"><a href="<?=$product[images][big]?>" target="_blank"><img src="<?=$product[images][tn]?>" border=0></a><br>
	Delete this image <input type="checkbox" name="del[<?=$product[id]?>]">
	</div>
	</td>
	<?}else{?>
	<TD ALIGN="RIGHT">Upload a Product Image <br>(JPG images only ):</td>
	<TD><input type=file name="big_image" onmouseover="void(this.title=this.value)"></TD>
	<?}?>
	</TR>
	</table>
	</fieldset>

	<fieldset><legend>Save your Work</legend>
&nbsp;<br>
	<div align=right>
	<INPUT TYPE="button" VALUE="Save" onclick="Save()"> &nbsp; 
	<input type=reset value="Reset"> &nbsp; 
	<?if($product[id]){?>
	<input type=button value="Edit Inventory" onclick="inventory()"> &nbsp; 
	<input type=button value="Delete Product" onClick="confirmDelete()"> &nbsp;
	 <?}?>	

</div>
<br>&nbsp;
	</fieldset>
	
</td>
</tr>
</table>



</FORM>

<?include("./_footer.php")?>