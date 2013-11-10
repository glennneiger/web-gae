<?
	foreach($data as $k=>$v){ ${$k}=$v; }
	$add_txt=$isInCart?"update":"add to cart";	
?>
<?if(!$addable){?>
<?}else{?>

	<table cellpadding=0 cellspacing=0>
	<form method="post" action="<?=$redirect?>" name="addform">
	<?input_hidden("inv_id",$inv_id)?>
	<TR>
	<TD align=right style="font-variant:small-caps">quantity:&nbsp;</TD>
	<TD align=right><?input_numsonly("quantity",$quantity,2,2)?>&nbsp;</TD>
	<TD align=right><input type="submit" name="action:add" value="<?=$add_txt?>" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)"></TD>
	<?if($isInCart){?>
		<TD align=right>&nbsp;<input type="submit" name="action:remove" value=del class="button" onmouseover="bOn(this)" onmouseout="bOff(this)"></TD>
	<?}?>
	<?if($num_items){?>
	<TD align=right>&nbsp;<input type="button" onclick="void(location.href='<?=$cartpage?>')" value="view cart" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)"></TD>
	<?}?>
	</TR>
	</form>
	</table>

<?}?>
