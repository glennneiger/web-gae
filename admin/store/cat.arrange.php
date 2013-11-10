<?
$hide_ui=1;
include("./_header.php");
$store=new Store();
$store->admin=1;
$postto="cat.arrange.mod.php";


?>
<script>
function Save(){
	lbSelectAll('cat[]');
	if(document.all)
		document.body.style.cursor="wait";
	setTimeout("findObj('theform').submit()",500);
}
</script>

<div class=error><?=strip($error)?></div>


<form method="post" action="<?=$postto?>" name="theform">
<?input_hidden("refer",$PHP_SELF)?>
<fieldset><legend>Arrange Categories</legend>
<table width=100%>
<TR>
<TD width=95%>
<select name="cat[]" multiple size=10 style="width:100%">
<?selectHashArr($store->getCategories(),"id","title")?>	
</select></TD>
<TD width=5%>
	<input type="button" value="&uarr;" onclick="lbMoveUp('cat[]')" title="Move Up"><br><br>
	<input type="button" value="&darr;" onclick="lbMoveDown('cat[]')" title="Move Down">
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