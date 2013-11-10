<?
$hide_ui=1;
include("./_header.php");

/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$store=new Store();
$store->admin=1;
$postto="arrange.mod.php";
if(!$cat_id)$cat_id=exec_query("SELECT * FROM $store->cTable ORDER BY ordr",1,"id");
$products="SELECT id,title 
		   FROM $store->pTable WHERE find_in_set('$cat_id',cat_ids)
		   ORDER BY ordr";
?>
<script>
function Save(){
	lbSelectAll('prods[]');
	if(document.all)
		document.body.style.cursor="wait";
	setTimeout("findObj('theform').submit()",500);
}
</script>

<div class=error><?=strip($error)?></div>

	<form method=get>
	Choose a category to arrange products
	<select name="cat_id" onChange="this.form.submit()">
		<?selectHashArr($store->getCategories(0,"title"), "id","title",$cat_id)?>
	</select>

	</form>

<form method="post" action="<?=$postto?>" name="theform">
<?input_hidden("refer",$PHP_SELF)?>
<?input_hidden("cat_id")?>
<fieldset><legend>Arrange Products</legend>
<table width=100%>
<TR>
<TD width=95%>
<select name="prods[]" multiple size=10 style="width:100%">
<?selectHashArr(exec_query($products),"id","title")?>	
</select></TD>
<TD width=5%>
	<input type="button" value="&uarr;" onclick="lbMoveUp('prods[]')" title="Move Up"><br><br>
	<input type="button" value="&darr;" onclick="lbMoveDown('prods[]')" title="Move Down">

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
<?include("./_footer.php")?>