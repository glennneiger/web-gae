<?
	//i am called from lib/_cart.php:StoreDisplay:displayPaging()
	global $p;
?>
<table cellpadding=0 cellspacing=0 border=0>
<TR valign=top>
<TD style="padding-right:5px"><?=href($data["prev"], "<",!$data["prev"],"class=altlinkoff")?></TD>
<td align=center>
<?foreach($data[links] as $href=>$txt){
	$lclass="class=altlink".($p==($txt-1)?"on":"off");
?>
	<span style="padding-right:5px"><?=href($href,$txt,0,$lclass)?></span>
<?}?>
</td>
<TD style="padding-right:5px"><?=href($data["next"],">",!$data["next"],"class=altlinkoff")?></TD>
</TR>
</table>