<?
	foreach($data as $k=>$v){ ${$k}=$v; }
	$add_txt=$isInCart?"update":"add to cart";	
?>
<?if(!$addable){?>
<?}else{
	?>

	<table cellpadding=0 cellspacing=0 border="0">
	<form method="post" action="<?=$redirect?>" name="addform">
	<?input_hidden("inv_id",$inv_id)?>
	<TR>
	<TD align="left" style="font-variant:small-caps;" valign="top">quantity:</TD>
	<TD align="left" valign="top" style="padding-left:5px;"><?input_numsonly("quantity",$quantity,2,2)?></TD>
	
	<TD align="left" valign="top" style="padding-left:5px;"><input type="image" style="cursor:pointer" src="<?=$IMG_SERVER?>/images/button_addtocart.gif" name="action:add" value="<?=$add_txt?>" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit">
	 </td>

	<?if($isInCart){?>
	<TD align="left" valign="top" style="padding-left:5px;"><input type="image" name="action:remove" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_delete.gif" value="del" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit"></TD> 
	<?}?>

	<?if($num_items && !$isInCart){?>
	<TD  align="right" valign="top" style="padding-left:5px;padding-right:4px;"><input type="image" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_viewcart.gif" onclick="void(location.href='<?=$cartpage?>')" value="view cart" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit"></td>
	<?}
	
	if($num_items && $isInCart){?>
	
	<!-- <TD align="left" style="padding-left:10px;"><input type="image" name="action:remove" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_delete.gif" value="del" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit></TD> --> 
	</TR>
	<TR>
	<TD colspan="3" align="right" valign="top" style="padding-top:4px; padding-right:4px;"><input type="image" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_viewcart.gif" onclick="void(location.href='<?=$cartpage?>')" value="view cart" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit"></td>
	<?}?>
	</TR>
	</form>
	</table>

<?}?>
