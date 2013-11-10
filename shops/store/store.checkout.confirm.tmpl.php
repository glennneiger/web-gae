<?
	$udata=$cart->_data[userdata];
	$contents=$cart->getCartData();
	$contents=$contents[contents];
	foreach($udata as $k=>$v){
		$udata[$k]=strip($v);
	}


?>
<script>
function finalizeOrder(theButton){
	if(document.all){
		if(typeof(theButton.disabled)!="undefined" && typeof(theButton.value!="undefined")){
			theButton.value="processing...";
			theButton.disabled=true;
		}
	}
	location.replace('store.checkout.process.php?step=finished');
}
</script>
<table align=center width="100%" style="border-collapse:collapse">
<TR>
<TD colspan=6 style="color:#FFF;font-weight:bold" class="padding"> 
<a href="<?=$cart->carturl?>" style="color:#fff">CART</a>
	| <a href="<?=$cart->checkouturl?>" style="color:#fff">CHECKOUT</a>
	| CONFIRM
</TD>
</tr>
<TR valign=top>
<TD class="padding">
	<div class="nofill"><B>Billing Info:</b></div>
	<?=$udata[b_fname]?> <?=$udata[b_lname]?><br>
	<?=$udata[b_address].($udata[b_address2]?"<br>${udata[b_address2]}":"")?><br>
	<?=$udata[b_city] .($udata[b_state]?",${udata[b_state]} ":" ") .$udata[b_zip]?><br>
	<?=$udata[b_country]?><br>
	tel: <?=$udata[b_phone]?><br>
	<?=$udata[b_email]?><br>
	<?=uc($udata[cc_type])?>: <?=$cart->hideCCNum($udata[cc_num])?><br>Expires:<?=$udata[cc_expire]?>
	</div>
	
	

</TD>
<TD class="padding">
	<div>
	<div class="nofill"><B>Shipping Info:</b></div>
	<?=$udata[s_fname]?> <?=$udata[s_lname]?>
	<?=$udata[s_address].($udata[s_address2]?"<br>${udata[s_address2]}":"")?><br>
	<?=$udata[s_city] .($udata[s_state]?",${udata[s_state]} ":" ") .$udata[s_zip]?><br>
	<?=$udata[s_country]?><br>
	<?=$udata[s_email]?><br>
	tel: <?=$udata[s_phone]?><br>
	<br>
	</div>
	
	<div class="nofill"><B>Gift Card Message or General Instructions:</b></div>
	<div>
		<?=strip($udata[order_comments])?>
	</div>
</TD>
</TR>
<TR>
<TR>
	<TD align=right colspan=2 class="padding"><b>Ship Via: <?=$udata[shipping_type]?></b></TD>
</TR>
</TR>
<TR>
<TD colspan=2 align=right height=30 class="contact"><input type="button" class="button" onclick="location.replace('<?=$cart->checkouturl?>')" value="edit" onmouseover="bOn(this)" onmouseout="bOff(this)"></TD>
</TR>
<TR>
	<TD colspan=2 class="padding">
		<b>Order Summary:</b>
		<table style="border-collapse:collapse">
		<TR>
		<TD width=1%><b>Qty.</b>&nbsp;&nbsp;</TD>
		<!-- <TD width=1% nowrap>item #</TD> -->
		<TD width=95%><b>Description</b></TD>
		<TD width=1% nowrap><b>Unit Price</b></TD>
		<TD width=1% nowrap><b>Qty. Price</b></TD>
		</TR>
		<TR>
		<?foreach($contents as $row){?>
			<TR valign=top class=padding>
			<TD><?=$row[quantity]?></TD>
			<!-- <TD><?=$row[sku]?>&nbsp;</td> -->
			<TD><?=$row[title]?>
				<?if( $cart->isGiftSubsTitle($row[title]) ){?>
				<div style="padding-left:10px">
					<?foreach($row[codes] as $i=>$code){?>
						<b>Recipient <?=$i+1?></b>:<?=$code[email]?><br>
					<?}?>
					<input class="button"
		type="button" onclick="location.replace('<?=$cart->checkouturl?>?step=egift')" value="edit recipient(s)"  onmouseover="bOn(this)" onmouseout="bOff(this)">
				<?}?>
				</div>
			</TD>
			<TD>$<?=dollars($row[price])?></TD>
			<TD>$<?=dollars($row[sum])?></TD>
			</TR>
		<?}?>
		<?if($cart->giftwrap){?>
		<TR class="padding">
			<TD colspan=3 align=right class="nofill">Gift Wrap:</TD>
			<TD>$<?=dollars($cart->giftwrap)?></TD>
		</TR>
		<?}?>
		<?if($cart->giftcard){?>
		<TR class="padding">
			<TD colspan=3 align=right class="nofill">Gift Card:</TD>
			<TD>$<?=dollars($cart->giftcard)?></TD>
		</TR>
		<?}?>
		<TR class="padding">
			<TD colspan=3 align=right class="nofill">Total:</TD>
			<TD>$<?=dollars($cart->total)?></TD>
		</TR>
		<TR class="padding">
			<TD colspan=3 align=right class="nofill">Tax<?
				if($cart->tax){ echo " @".$cart->tax*100 ."%"; }
			?>:</TD>
			<TD>$<?=dollars($cart->taxAmount)?></TD>
		</TR>
		<TR class="padding">
			<TD colspan=3 align=right class="nofill">Shipping:</TD>
			<TD>$<?=dollars($cart->shipping)?></TD>
		</TR>	
		<TR class="padding">
			<TD colspan=3 align=right class="nofill"><b>Grand Total</b>:</TD>
			<TD><b>$<?=dollars($cart->gTotal)?></b></TD>
		</TR>
		<TR>
		<TD colspan=4 align=right height=30 class="contact"><input class="button"
		type="button" onclick="location.replace('<?=$cart->carturl?>')" value="edit"  onmouseover="bOn(this)" onmouseout="bOff(this)">
		</TD>
		</TR>
		</table>
	</TD>
</TR>

<TR>
<TD colspan=2 align=right>	&nbsp;<br>
	<input class="button" type="button" onclick="location.replace('store.checkout.process.php?step=continueshopping')" value="continue shopping" onmouseover="bOn(this)" onmouseout="bOff(this)">
	<input class="button" type="button" onclick="finalizeOrder(this)" value="finalize order" onmouseover="bOn(this)" onmouseout="bOff(this)">
</TD>
</TR>
</table>
