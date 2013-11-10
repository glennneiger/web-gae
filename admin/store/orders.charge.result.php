<?include("./_header.php");
$res=munserial($res);
?>
<br>
<fieldset><legend>Order Processing Results:</legend>
<table>
<TR bgcolor="#cccccc">
<TD bgcolor="#eeeeee"><b>Result</b></td>
<TD><b>Transaction ID:</b></TD>
<TD><b>Message</b></TD>
</TR>
<?foreach($res as $row){?>
	<TR bgcolor="#cccccc">
	<TD bgcolor="#eeeeee"><?=$row[error]?"<b>ERROR!</b>":"SUCCESS"?></TD>
	<TD><?=$row[id]?></TD>
	<TD><?=$row[msg]?></TD>
	</TR>
<?}?>
</table>
<HR />
<a href="<?=$refer?>">Return to Order Page</a>
</fieldset>
<?include("./_footer.php")?>