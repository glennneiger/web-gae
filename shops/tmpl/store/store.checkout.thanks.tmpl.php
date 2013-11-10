<table align=center width="100%" style="border-collapse:collapse">

<TR valign=top>
<TD class="slimpadding" height=100>
<?if($error){?>
	<div class="error">
	Your transaction had problems that couldn't be recovered. <br><br>
	<b class="error"><?=strip($error)?></b>
		</div>
	<br>
	<br>
	If you continue having difficulty with your order, please contact your bank or <a href="mailto:sales@minyanville.com">sales@minyanville.com</a> for customer service assistance
	
<?}else{
	readfile("$D_R/assets/data/order_thanks.html");
}?>

</TD>
</TR>
</table>
