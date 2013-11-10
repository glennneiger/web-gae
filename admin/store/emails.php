<?
$postto="tmpl.mod.php";
include("./_header.php");

$basekey="emails/";
$contentGroups=getSContentNames($basekey);

if(!$group)$group=key($contentGroups);//default to a group 2 disallow new page
$pagedata=getSContent("$basekey$group",1);
$group=strip($group);


$img_dir="$PATH_FR/assets";

?>


<center>
<fieldset>
<form method="get">
<legend>
	<b>Now Editing:</b> </TD>
	<select name="group" onchange="this.form.submit()">
<!-- 		<option value="">-- New Page --</option> -->
		<?selectHash($contentGroups,$group)?>
	</select>
</legend>
</form>
<table align=center width=600 border=0>
<form method="post" action="<?=$postto?>" name="theform">
<input type="hidden" name="delete" value="0">
<?input_hidden("group")?>
<?input_hidden("basename","$basekey$group")?>
<?input_hidden("basekey")?>
<?input_hidden("bounceback",$PHP_SELF)?>
<?input_hidden("new[keyname]",cleanfilename($pagedata[title]))?>
<?input_hidden("new[modified]",time())?>
<TR valign=top>
<TD>
 <?input_text("new[title]",$pagedata[title])?> 
<!-- (use this to make new) Template Title:<?input_hidden("new[title]",$pagedata[title])?>-->
Email Header Text:<br>
<?rich_editor("new[header]",$pagedata["header"],200,200)?>
</TD>
</TR>
<TR>
<TD nowrap>
	------------------------------------
	<br>(dynamic content)<br>
	------------------------------------
</TD>
</TR>
<TR valign=top>
<TD>Email Footer Text:<br>
<?rich_editor("new[footer]",$pagedata["footer"],200,200)?>
</TD>
</TR>


<?input_hidden("new[is_live]",1)?>



</tr>
</table>
</fieldset>
<fieldset><legend>Save Your Work</legend>
<div align=right>
	<input type="button" value="Save" onclick="Save()" name="save" disabled>&nbsp;
	<input type="reset" value="Reset" name="reset" disabled>&nbsp;

</div>
</fieldset>
</form>

</center>
<?include("./_footer.php")?>
