<?
	//i am called from lib/_cart.php:StoreDisplay:displayProduct()
	//convert inventory into color 
	foreach($data as $k=>$v){${$k}=$v;}
	unset($data);

?>
<?if(!$hasProducts){?>
	<table width=100% class="filledbox" height=220>
	<TR><TD align=center><b>No product to display</b></TD></TR>
	</table>
<?}else{?>
<table width=100% class="filledbox" cellspacing=0>
<TR class="nofill">
<TD style="padding:3px 10px 3px 10px"><?=$title?></TD>
</TR>
<TR>
	<TD height=220 align=center><img src="<?=$images[big]?>" border=0 vspace=3 hspace=3></TD>
</TR>
<TR class="nofill">
	<TD style="padding:10px">
	<?=($price?"<b>$price</b><br>":"")?>
	<?if($addl_descr){?>
		<?=nl2br($addl_descr)?><br><br>
	<?}?>	
	<?if($description){?>
		<?=nl2br($description)?>
	<?}?>
	
	
	</TD>
</TR>
<?if(count($options)){?>
<TR>
<form>
<TD align=center>
	<?input_hidden("prod_id")?>
	<?input_hidden("cat_id")?>
	<?input_hidden("p")?>
	<select name="inv_id" onchange="this.form.submit()" style="font:nomal 10px courier">
	<?selectHash($options,$inv_id)?>
	</select>
</TD>
</form>
</TR>




<?}elseif(count($inventory)==1 && count($params=$inventory[ key($inventory) ][params])){
	//one product to display -- engine defaults to 1st product_id
?>


<TR>
<TD style="font-size:10px;">
	<table align=center cellpadding=0 cellspacing=0 border=0>
	<TR>
	<?foreach(array_keys($params) as $col){?>
		<TD style="font-size:9px;border:0px"><?=ucwords($col)?></TD>
	<?}?>
	</TR>
	<TR>
	<?foreach($params as $k=>$v){?>
		<TD style="font-size:9px;border:0px"><b><?=$v?></b>&nbsp;</TD>
	<?}?>
	</TR>
	</table>
	
	
</TD>
</TR>




<?}elseif(!count($inventory)){?>
	<TR>
	<TD align=center style="font-size:10px">
		<b>this product is not for sale</b>
	</td>
	</tr>
<?}?>
</table>
<?}?>