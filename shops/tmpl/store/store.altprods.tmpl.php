<?global $cart,$D_R;?>
	<div class=padding>
	<table>
	<?foreach($data as $i=>$row){
	?>
	<TR valign=top>
	<TD width=1% align=center nowrap>
	<img src="<?=$row[images][big]?>" border=0>
	</TD>
	<TD width=10>&nbsp;</TD>
	<TD width=99%>
		<div class=title><?=strip($row[title])?></div><br>
		<u>DESCRIPTION</u>: <?=strip($row[description])?><br>
		<?if($row[min_purchase_count]>1){?>
			<b><i>Minimum purchase is <?=$row[min_purchase_count]?> items</i></b>
		<?}?>	
		<br>
		
	<?$cart->displayShopper("$STORE_TEMPLATE_DIR/store.addcart.tmpl.php",$row[inventory][0][id])?>
	</TD>
	</TR>
	<TR><TD colspan=3><hr></TR>
	<?}?>
	</table>
	</div>