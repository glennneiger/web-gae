<?	
foreach($data as $k=>$v){ ${$k}=$v; }
$inv_items=count($inventory);
global $ADD_CHECK_JS;
?>
<?if(!$ADD_CHECK_JS){?>
<script>
function validateAddCheck(formname){
//tells if at least one checkbox is checked;
	for(i=0;i<document[formname].elements.length;i++){
		var el=document[formname].elements[i];
		if(stristr("check",el.type)){
			if(el.checked){
				return true;
			}
		}
	}
	alert("You haven't selected anything to add to your cart.");
	return false;
}
</script>
<?}
$ADD_CHECK_JS=1;
?>
<table border="0" cellpadding=0 cellspacing=0 border=0 width=100%>
<TR><TD height=10 colspan=10>&nbsp;</TD></TR>
<form method="post" onSubmit="return validateAddCheck(this.name)" action="<?=$redirect?>" name="addform<?=$prod_id?>">
<?refer()?>
<?input_hidden("action","addmany")?>
<?foreach($inventory as $row){?>
	<TR valign=top <?=($row[isInCart]||$row[nostock]?"style='background:#dedede'":"")?>>
	<TD align=center>
		<?if(!$row[isInCart] && !$row[nostock]){
			$empty_item=1;
			input_check("inv_ids[${row[id]}]",$inv_items===1);
		}else{?>
			&nbsp;
		<?}?>&nbsp;&nbsp;</TD>
	<TD><u>SKU</u>: <b><?=$row[sku]?></b>&nbsp;&nbsp;
		<?if(count($row[params])){?>
			<?foreach($row[params] as $k=>$v){?>
				<?=$k?>: <b><?=$v?></b>&nbsp;
			<?}?>
		<?}?>
	</TD>
	<TD><?if($row[weight]){?><u>Weight</u>: <b><?=$row[weight]?></b>&nbsp;&nbsp;<?}?></TD>
	<TD nowrap><b>$<?=$row[price]?></b>&nbsp;
		<?if($row[nostock]){?>
			<i>out of stock</i>
		<?}else{ $hasstock=1;} ?>
		
	</TD>
	</TR>
	<?if($row[description]){?>
		<TR valign=top height=45>
		<TD colspan=4 align=right><?=$row[description]?></TD>
		</TR>
	<?}?>
<?}?>
	<TR><TD height=10 colspan=10>&nbsp;</TD></TR>
	<TR>
	<TD colspan=10 nowrap>
	<?if($empty_item && $hasstock){?><input type="submit" value="add selected to cart" onmouseover="bOn(this)" onmouseout="bOff(this)">
	<?}?>
	<?if($num_items){?>
&nbsp;<input type="button" onclick="void(location.href='<?=$cartpage?>')" value="view cart/checkout" onmouseover="bOn(this)" onmouseout="bOff(this)">
<?}?>
</TD>
	</TR>

</form>
</table>

