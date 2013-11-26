<?
$hide_ui=1;
global $D_R;
include("./_header.php");
include_once("$D_R/lib/_cart.php");
$store=new Store();
$store->admin=1;
$postto="params.mod.php";
?>
<script>
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

</script>
<div class=error><?=strip($error)?></div>
<form method="post" action="<?=$postto?>" name="theform">
<?input_hidden("refer",$PHP_SELF)?>
<fieldset><legend>Edit Parameter Names</legend>
<table width=100%>
<TR valign=bottom bgcolor="#cccccc">
<TD bgcolor="#999999" nowrap width=1% align=center>
	Delete<br><input type="checkbox" name="checkstatus" onclick="checkAll()" align=top></TD>
<TD width=99%>Parameter Name</TD>
</TR>
<?foreach($store->getParameters() as $row){?>
<TR>
<TD bgcolor="#999999" align=center><input type="checkbox" name="del[<?=$row[id]?>]"></TD>
<TD><?input_text("param[${row[id]}][name]",$row[name],15,15)?></TD>
</TR>
<?}?>
</table>
</fieldset>
<fieldset><legend>Add a New Parameter</legend>
	<table width=100%>
		<TR>
		<TD>Parameter Name</TD>
		</TR>
		<TR>
		<TD><?input_text("new[name]","")?></TD>
		</TR>
	</table>
</fieldset>
<fieldset><legend>Save your Work</legend>
	&nbsp;<br><div align=right>
	<INPUT TYPE="submit" VALUE="Save"> &nbsp; 
	<input type=reset value="Reset"> &nbsp; 
	</div><br>
	<span style="color:#ff0000">
		NOTE: refresh the product edit window to see your changes
	</span>		
	
	<br>&nbsp;
</fieldset>
</form>
<?include("./_footer.php")?>