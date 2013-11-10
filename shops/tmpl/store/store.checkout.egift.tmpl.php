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
<table align=center width="100%" style="border:solid 1px #cccccc;">
<form action="store.checkout.process.php" name="theform" method="post">
<?input_hidden("step","egift")?>
<TR>
<TD colspan=6 class="slimpadding">
<a href="<?=$cart->carturl?>" >CART</a>
| <a href="<?=$cart->checkouturl?>" >CHECKOUT</a>
	| GIFT SUBSCRIPTION RECIPIENTS
</TD>
</tr>
<TR valign=top>

<TD class="slimpadding" colspan=2>
	Please enter the email address(es) of the person(s) you want to send the gift subscription to:
	<br><br>
	<?for($i=0;$i<$giftitem[quantity];$i++){?>
		<b>Recipient <?=$i+1?> email</b>:
		<?input_email("email[$i]",$giftitem[codes][$i][email])?><br><br>
	<?}?>
</td>

<TR>
<TD colspan=2 align=left>	&nbsp;<br>
	<img style="cursor;pointer;"  src="<?=$IMG_SERVER?>/images/button_continueshopping.gif" onclick="location.replace('store.checkout.process.php?step=continueshopping')" onmouseover="bOn(this)" onmouseout="bOff(this)">
	<img style="cursor;pointer;" hspace="5" src="<?=$IMG_SERVER?>/images/button_finalizeorder.gif" onclick="finalizeOrder(this)" onmouseover="bOn(this)" onmouseout="bOff(this)">
</TD>
</TR>
</form>
</table>
