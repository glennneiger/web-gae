<?
global $cart,$D_R,$IMG_SERVER;
?>
	<div class="padding">
	<table border="0">
	<?foreach($data as $i=>$row){?>
	<TR valign=top>
	<TD align="center" nowrap width="250" height="250">
	<img src="<?=$IMG_SERVER.$row[images][big]?>" border=0>
	</TD>
	<TD width=10></TD>
	<TD valign="top"><h5><?=strip($row[title])?></h5>
		<p>DESCRIPTION: <?=strip($row[description])?></p>
		<?if($row[min_purchase_count]>1){?>
			<h5>Minimum purchase is <?=$row[min_purchase_count]?> items</h5>
		<?}?>

	<?$cart->displayShopper2("$STORE_TEMPLATE_DIR/store.addcartcheck.tmpl.php",$row[inventory])?>

	</TD>
	</TR>
	<tr>
	<td colspan="3" style="padding:10px 0px;">
	<div class="shop_cart_divider"></div>
	</td>
	</tr>
	<?}?>
	</table>
	</div>