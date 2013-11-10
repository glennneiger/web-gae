<?
$giftitem=$cart->getGiftSubsItem();

?>
<script src="/lib/_script.js"></script>
<script>
function finalizeOrder(theButton){
	for(i=0;i<<?=$giftitem[quantity]?>;i++){
		formname="email["+i+"]";
		if(!validEmailValue(formname)){
			alert("Please enter <?=$giftitem[quantity]?> valid email address(es)");
			return;
		}
	}
	findObj("theform").submit();
}
</script>
<table align=center width=650 style="border-collapse:collapse" class="leftnavbg">
<form action="store.checkout.process.php" name="theform" method="post">
<?input_hidden("step","egift")?>
<TR class="regseparator padding">
<TD colspan=6 style="color:#FFF;font-weight:bold" class="padding"> 
<a href="<?=$cart->carturl?>" style="color:#fff">CART</a>
	| <a href="<?=$cart->checkouturl?>" style="color:#fff">CHECKOUT</a>
	| GIFT SUBSCRIPTION RECIPIENTS
</TD>
</tr>
<TR valign=top>

<TD class="padding" colspan=2>
	Please enter the email address(es) of the person(s) you want to send the gift subscription to:
	<br><br>
	<?for($i=0;$i<$giftitem[quantity];$i++){?>
		<b>Recipient <?=$i+1?> email</b>:
		<?input_email("email[$i]",$giftitem[codes][$i][email])?><br><br>
	<?}?>
</td>

<TR>
<TD colspan=2 align=right>	&nbsp;<br>
	<input class="button" type="button" onclick="location.replace('store.checkout.process.php?step=continueshopping')" value="continue shopping" onmouseover="bOn(this)" onmouseout="bOff(this)">
	<input class="button" type="button" onclick="finalizeOrder(this)" value="confirm order" onmouseover="bOn(this)" onmouseout="bOff(this)">
</TD>
</TR>
</form>
</table>
