<?php
global $IMG_SERVER;
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
<table border="0" cellpadding="5" cellspacing="0" border="0" width="100%">

<form method="post" onSubmit="return validateAddCheck(this.name)" action="<?=$redirect?>" name="addform<?=$prod_id?>">
<?refer()?>
<?input_hidden("action","addmany")?>
<?foreach($inventory as $row){?>
	<TR valign=top <?=($row[isInCart]||$row[nostock]?"style='background:#eff3f6'":"")?> height=25>
	<TD><center>
		<?if(!$row[isInCart] && !$row[nostock]){
			$empty_item=1;
			input_check("inv_ids[${row[id]}]",$inv_items===1);
		}else{?>
			&nbsp;
		<?}?></center></TD>
	<TD> SKU:<b><?=$row[sku]?></b>
		<?if(count($row[params])){?>
			<?foreach($row[params] as $k=>$v){?>
				<?=$k?>: <b><?=$v?></b>&nbsp;
			<?}?>
		<?}?>

	<?if($row[weight]){?>Weight: <b><?=$row[weight]?></b><?}?>&nbsp;
	<b>$<?=$row[price]?></b>&nbsp;per bag
		<?if($row[nostock]){?>
			<i>out of stock</i>
		<?}else{ $hasstock=1;} ?>

	</TD>
	</TR>
	<?if($row[description]){?>
		<TR valign=top height="30">
		<TD colspan=4 align=left><p><?=$row[description]?></p></TD>
		</TR>
	<?}?>
<?}?>
	<TR>
	<TD colspan=10 nowrap>
	<?if($empty_item && $hasstock){?>
	<img style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_addtocart.gif" onclick="addform<?=$prod_id?>.submit();" onmouseover="bOn(this)" onmouseout="bOff(this)">
	<?}?>
	<?if($num_items){?>
    <img  style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_viewcart.gif" onclick="void(location.href='<?=$cartpage?>')" onmouseover="bOn(this)" onmouseout="bOff(this)">
<?}?>
</TD>
	</TR>

</form>
</table>

