<?	//i am called from lib/_cart.php:StoreDisplay:displayThumbs()
	$cols=2;
?>

<table border=0 cellspacing=5>
<?foreach($data as $i=>$prod){
	$imgclass="imgThumbBox".($prod[on]?"on":"off");
	echo openTR($i,$cols);
?>	<TD align=center width=70 height=70 class="<?=$imgclass?>">
		<?if(!$prod[on]){?><a href="<?=$prod["link"]?>"><?}?><img src="<?=$prod[images][tn]?>" border=<?=intval($prod[on])?> vspace=3 hspace=3></a>
</TD>
<?=closeTR($i,$cols,count($data))?>
<?}?>
</table>
