<?global $cart,$D_R,$IMG_SERVER;?>
	<div class="padding">
	<table border="0">
	<?foreach($data as $i=>$row){?>
	<TR valign=top>
	<TD width=1% align=center nowrap height="250">
	<img src="<?=$IMG_SERVER.$row[images][big]?>" border=0>
	</TD>
	<TD width=10>&nbsp;</TD>
	<TD width=99%>
		<div class=title><?=strip($row[title])?></div><br>
		<u>DESCRIPTION</u>: <?=strip($row[description])?><br>
		<?if($row[min_purchase_count]>1){?>
			<b><i>Minimum purchase is <?=$row[min_purchase_count]?> items</i></b>
		<?}?>
		<br>
	<?$cart->displayShopper2("$STORE_TEMPLATE_DIR/store.addcartcheck.tmpl.php",&$row[inventory])?>

	</TD>
	</TR>
	<tr>
	<td colspan="3" style="padding:18px 0px;">
		<p class="simple-separator">&nbsp;</p>
	</td>
	</tr>
	<?}?>
	</table>
	</div>