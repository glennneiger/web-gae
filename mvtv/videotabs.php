 <?
include_once("../lib/_module_data_lib.php");
include_once("../lib/_redesign_design_lib.php");
global $VIDEOHOST;
$MAX=4;
if ($_GET['i']=="")
{
	$i=1;
}
else
{
	$i=$_GET['i'];
}
if ($_GET['count']=="" or $_GET['count']==0)
{
	if($_GET['page'] != "")
	{
		$count=$_GET['page'];
	}
	else
	{
		$count=1;
	}
}
else
{
	$count=$_GET['count'];
}
$start=($count-1)*$MAX;
$sql_cat_list2="select  count(*) num from mvtv m where is_live='1' and id<> '".$current_video_id."' and approved='1' and publish_time < '".mysqlNow()."' order by publish_time desc";
$result_cat_list2= exec_query($sql_cat_list2,1);
$numrows1=$result_cat_list2['num'];

$sqlVideoList="select m.id,m.title,m.thumbfile,ex.url from mvtv m, ex_item_meta as ex where ex.item_id = m.id and ex.item_type = 11 and m.approved='1' and m.is_live='1' and m.id<> '".$current_video_id."' and m.publish_time < '".mysqlNow()."' order by m.publish_time desc limit $start,$MAX";

$arVideoList= exec_query($sqlVideoList);
?>
<table width="100%" cellspacing='0' cellpadding='0' border='0' align='left'>
<?if ($numrows1>0){?>
<tr>
	<td valign="top">
		<table border="0" cellpadding="0" cellspacing="0" height="165" align="left" class="video_container_table">
		<tr>
			<?
			foreach ($arVideoList as $arVideo)
			{
				$title=ucwords(str_replace("_","\n",$arVideo['title']));
				$len=strlen($title);
				if ($len > 31){$title=substr($title,0,30);}
			?>
				<td valign="top">
				<div id="setLetf" class="video_container"><a target="_parent" href='<?=$HTPFX.$VIDEOHOST.$arVideo['url']?>?page=<?=$count?>&section_id=<?=$section_id?>'>
				<img height='104px' width='138px'  src="<?=$arVideo['thumbfile']?>" title="<?=$arVideo['title']?>" /></a></div>
				<div class="combo_bottom_title" ><a target="_parent" href='<?=$HTPFX.$VIDEOHOST.$arVideo['url']?>?page=<?=$count?>&section_id=<?=$section_id?>'><?=$title?></a></div></td>
			<?
			}
			?>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td align='center' valign="top">
	<?
		echo make_ajax_pagination_video("combo_tab",$HTPFX.$VIDEOHOST."/mvtv/videotabs.php?section_id=".$section_id."&i=".$i,$count,$MAX,$numrows1);
	?>
	</td>
</tr>
<?} else {?>
<tr><td height="150">
No Video found...!
</td></tr>
<?}?>
</table>
