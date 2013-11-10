<?
global $cloudStorageTool,$HTPFX,$HTHOST;
include("$ADMIN_PATH/_header.htm");

$postto= $HTPFX.$HTHOST."/admin/store/categories.mod.php";

$options = [ 'gs_bucket_name' => 'mvassets/temp' ];
$frm_url = $cloudStorageTool->createUploadUrl($postto, $options);

/*============set up permissions=================*/
$adminall=$AD->getPerm(array("admin_users_all", "shops_edit"));

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$store=new Store();
$store->admin=1;
$cat_id = $_GET['cat_id'];
if($cat_id){
	$cat=$store->getCategories($cat_id);
}else{//minyanville-specific functionality
	$cat=array_shift($store->getCategories());
	$cat_id=$cat[id];
}

?>
<script>
function confirmDelete(){
	theForm=document["theform"];
	if(confirm("Are you sure you want to delete this category?")){
		theForm["delcat"].value="true";
		theForm.submit();
		return;
	}
	theForm["delcat"].value="";
}

function clearTxt(srcObj){
	findObj(srcObj).value="";
}
function Save(frm){
	var err="";
	var thetitle=trim(getValByName("cat[title]"));
	if(!thetitle)
		err+=" Give the product a name\n";
	if(err.length){
		alert(err);
		return;
	}
	frm.submit()
}
function products(frm){
	findObj("refer").value="products.php";
	Save(frm);
}
</script>

<CENTER>

<?if($error){?><div class=error><?=strip($error)?></div><?}?>
<table><TR><TD align=right>
<form method=get name="navform">
Choose a category under which to edit products: 
<select name="cat_id" onChange="this.form.submit()">
<!--  	<option value="">---New Category----</option>  -->
	<?selectHashArr($store->getCategories(0,"title"), "id","title",$cat_id)?>
</select>

</form>
</TD></TR></table>


<FORM METHOD="POST" action="<?=$frm_url?>" ENCTYPE="multipart/form-data" name="theform">
<?input_hidden("cat_id")?>
<?input_hidden("delcat",0)?>
<?input_hidden("refer",$PHP_SELF)?>
<hr>


</div>
<table>
<TR valign=top>
<TD>
<?if($cat_id){?>
	<br><a href="products.php?cat_id=<?=$cat_id?>">Edit Products in this category</a> |
<?}?>
<!-- <a href="cat.arrange.php" target="_search">Arrange Categories</a> -->

<fieldset><legend >Category Description</legend>

<table>
<TR valign=top>
<TD align=right>Category Name:</td>
<TD><?input_text("cat[title]",$cat[title],"","","style=width:100%")?></TD>
</TR>
<TR>
<TD align=right>Category Description:</td>
<TD><textarea style="width:200;height:100" name="cat[description]"><?=$cat[description]?></textarea></TD>
</TR>
</table>
</fieldset>
<fieldset><legend>Category Image:</legend>
	<table cellpadding=0 cellspacing=2 border=0>
	<TR>
	<?if($cat[images][big]){?>
	<TD align=right>Category Image:</td>
	<TD nowrap><div style="border:1px black solid"><a href="<?=$cat[images][big]?>" target="_blank"><img src="<?=$cat[images][tn]?>" border=0></a><br>
	Delete this image <input type="checkbox" name="del[<?=$cat[id]?>]">
	</div>
	</td>
	<?}else{?>
	<TD ALIGN="RIGHT">Upload a Category Image <br>(JPG images only ):</td>
	<TD><input type=file name="big_image"></TD>
	<?}?>
	</TR>
	</table>
	</fieldset>
<fieldset><legend>Save your Work</legend>
	<br>&nbsp;
	<div align=right>
	<INPUT TYPE="button" VALUE="Save" onclick="Save(this.form)"> &nbsp; 
	<input type=reset value="Reset"> &nbsp; 
	<?if($cat[id]){?>
		<input type=button value="Edit Products" onClick="products(this.form)"> &nbsp; 
		<!-- <input type=button value="Delete Category" onClick="confirmDelete()"> &nbsp; --> 
		<?}?>
</div>
<br>&nbsp;
</fieldset>

<?include("./_footer.php")?>