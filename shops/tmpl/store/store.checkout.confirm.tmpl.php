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
<table  cellpadding="0" cellspacing="0" width="100%" style="border:solid 1px #cccccc; " border="0">
<TR>
<TD colspan=6  class="slimpadding" style="padding-top:5px; padding-bottom:5px;">
<a href="<?=$cart->carturl?>"  >CART</a>
	| <a href="<?=$cart->checkouturl?>"  >CHECKOUT</a>
	| CONFIRM
</TD>
</tr>
<TR valign=top>
<TD class="slimpadding">
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
<TD class="slimpadding">
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
	<TD height="20" align="left" colspan=2 class="slimpadding" ><b>Ship Via: <?=$udata[shipping_type]?>&nbsp;</b></TD>
</TR>
</TR>
<TR>
<TD colspan=2 style="background-color:#EFF3F6; padding-top:5px;  padding-bottom:5px;" ><img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/button_edit.gif" hspace="5" align="left" onclick="location.replace('<?=$cart->checkouturl?>')"  onmouseover="bOn(this)" onmouseout="bOff(this)"></TD>
</TR>
<TR>
	<TD colspan=2 >
		<span class="slimpadding"><b >Order Summary:</b></span>
		<table cellpadding="0" cellspacing="0" border="0">
		<TR>
		<TD  class="slimpadding" width=1% class="padding"><b>Qty.</b>&nbsp;&nbsp;</TD>
		<!-- <TD width=1% nowrap>item #</TD> -->
		<TD  class="slimpadding" width=95%><b>Description</b></TD>
		<TD class="slimpadding" width=1% nowrap><b>Unit Price &nbsp;</b></TD>
		<TD  class="slimpadding" width=1% nowrap><b>Qty. Price &nbsp;</b></TD>
		</TR>
		<TR>
		<?foreach($contents as $row){?>
			<TR valign=top  class="slimpadding">
			<TD  class="slimpadding"><?=$row[quantity]?></TD>
			<!-- <TD><?=$row[sku]?>&nbsp;</td> -->
			<TD><?=$row[title]?>
				<?if( $cart->isGiftSubsTitle($row[title]) ){?>
				<div >
					<?foreach($row[codes] as $i=>$code){?>
						<b>Recipient <?=$i+1?></b>:<?=$code[email]?><br>
					<?}?>
					<img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/button_editrecipient.gif" align="left" onclick="location.replace('<?=$cart->checkouturl?>?step=egift')" value="edit recipient(s)"   onmouseover="bOn(this)" onmouseout="bOff(this)">
				<?}?>
				</div>
			</TD>
			<TD>$<?=dollars($row[price])?></TD>
			<TD>$<?=dollars($row[sum])?></TD>
			</TR>
		<?}?>
		<?if($cart->giftwrap){?>
		<TR >
			<TD colspan=3 align=right class="nofill">Gift Wrap:</TD>
			<TD>$<?=dollars($cart->giftwrap)?></TD>
		</TR>
		<?}?>
		<?if($cart->giftcard){?>
		<TR >
			<TD colspan=3 align=right class="nofill">Gift Card:</TD>
			<TD>$<?=dollars($cart->giftcard)?></TD>
		</TR>
		<?}?>
		<TR class="padding">
			<TD class="slimpadding" colspan=3 align="left" class="nofill">Total:</TD>
			<TD>$<?=dollars($cart->total)?></TD>
		</TR>
		<TR c>
			<TD class="slimpadding" colspan=3 align=left class="nofill">Tax<?
				if($cart->tax){ echo " @".$cart->tax*100 ."%"; }
			?>:</TD>
			<TD>$<?=dollars($cart->taxAmount)?></TD>
		</TR>
		<TR >
			<TD class="slimpadding" colspan=3 align=left class="nofill">Shipping:</TD>
			<TD>$<?=dollars($cart->shipping)?></TD>
		</TR>
		<TR >
			<TD colspan=3 align="left" class="slimpadding"><b>Grand Total</b>:</TD>
			<TD><b>$<?=dollars($cart->gTotal)?></b></TD>
		</TR>
		<TR>
		<TD colspan=4 style="background-color:#EFF3F6; padding-top:5px;  padding-bottom:5px;" ><img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/button_edit.gif" align="left" onclick="location.replace('<?=$cart->carturl?>')" value="edit" hspace="5"  onmouseover="bOn(this)" onmouseout="bOff(this)">
		</TD>
		</TR>
		</table>
	</TD>
</TR>

<TR>
<TD colspan=2 align=left>	&nbsp;<br>
	<img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/button_continueshopping.gif" onclick="location.replace('store.checkout.process.php?step=continueshopping')" value="continue shopping" onmouseover="bOn(this)" onmouseout="bOff(this)" hspace="5">
	<img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/button_finalizeorder.gif"  onclick="finalizeOrder(this)" value="finalize order" onmouseover="bOn(this)" onmouseout="bOff(this)">
</TD>
</TR>
</table>
