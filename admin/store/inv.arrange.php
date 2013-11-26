<?
global $D_R;
$hide_ui=1;
include_once("$D_R/lib/_cart.php");
include("./_header.php");

/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$store=new Store();
$store->admin=1;
$inventory=array();
$postto="inv.arrange.mod.php";
if($store->isValidProductId($id)){
	$inventory=$store->getInventory($id,0);
	foreach($inventory as $i=>$row){
		$inventory[$i][sku].=":".implode(" ",$row[params]);
	}
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
	lbSelectAll('inv[]');
	if(document.all)
		document.body.style.cursor="wait";
	setTimeout("findObj('theform').submit()",500);
}
</script>

<div class=error><?=strip($error)?></div>

<center>
<table border=0 cellpadding=0 cellspacing=0 border=0><TR><TD align=right>
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
<?if($id){?>
<form method="post" action="<?=$postto?>" name="theform">
<?input_hidden("refer",$PHP_SELF)?>
<?input_hidden("cat_id")?>
<?input_hidden("id")?>
<fieldset><legend>Arrange SKUs</legend>
<table width=100%>
<TR>
<TD width=95%>
<select name="inv[]" multiple size=10 style="width:100%">
<?selectHashArr($inventory,"id","sku")?>	
</select></TD>
<TD width=5%>
	<input type="button" value="&uarr;" onclick="lbMoveUp('inv[]')" title="Move Up"><br><br>
	<input type="button" value="&darr;" onclick="lbMoveDown('inv[]')" title="Move Down">

</TD>
</TR>
</table>

</fieldset>

<fieldset><legend>Save your Work</legend>
	&nbsp;<br><div align=right>
	<INPUT TYPE="button" VALUE="Save" onclick="Save()"> &nbsp; 
	<input type=reset value="Reset" onclick="location.replace(location.href)"> &nbsp; 
	</div><br>
	<span style="color:#ff0000">
		NOTE: refresh the product edit window to see your changes
	</span>		
	
	<br>&nbsp;
</fieldset>
</form>
<?}?>
<?include("./_footer.php")?>