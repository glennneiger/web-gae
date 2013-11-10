<?
	//i am called from lib/_cart.php:StoreDisplay:displayCategories()
?>

<table>
<TR>
<?foreach($data as $cat){?>	
<TD><a class="link<?=($cat[on]?"on":"off")?>" href="<?=$cat["link"]?>" style="font-weight:bold"><?=uc($cat[title])?></a> | </TD>
<?}?>
</TR>
</table>
