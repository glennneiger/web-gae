 <?
include_once("../lib/_module_data_lib.php");
include_once("../lib/_redesign_design_lib.php");
include_once($D_R."/lib/videos/_data_lib.php");
global $VIDEOHOST;
$objPlayer = new player();
$catName=strtolower($_GET['catname']);

switch($catName){
	case 'hoofyandboo':
		$catName=strtolower("HOOFY & BOO");
                break;
        case 'toddharrisontv':
		$catName=strtolower("TODD HARRISON TV");
                break;
        case 'popbiz':
		$catName=strtolower("POP BIZ");
                break;
        case 't3live':
		$catName=strtolower("T3 Live");
                break;
	case 'specials':
		$catName=strtolower("Specials");
                break;
	default:
	        $catName=strtolower("HOOFY & BOO");
	break;
}

if(empty($_GET['section_id'])){
	$objCatId=$objPlayer->getVideoCatId($catName);
	$sectionid=$objCatId['section_id'];
}else{
	$sectionid=$_GET['section_id'];
}


$MAX=20;
$j=1;
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
$section_id=$sectionid;
$start=($count-1)*$MAX;
$sql_cat_list2="select  count(*) num from mvtv m where is_live='1' and approved='1' and cat_id='".$sectionid."' and publish_time < '".mysqlNow()."' order by publish_time desc";
$result_cat_list2= exec_query($sql_cat_list2,1);
$numrows1=$result_cat_list2['num'];

$sqlVideoList="select m.id,m.title,m.thumbfile,ex.url from mvtv m, ex_item_meta as ex where ex.item_id = m.id and ex.item_type = 11 and m.approved='1' and m.is_live='1' and cat_id='".$sectionid."' order by m.publish_time desc limit $start,$MAX";

$arVideoList= exec_query($sqlVideoList);
?>
<table width="100%" cellspacing='0' cellpadding='0' border='0' align='left'>
<?if ($numrows1>0){?>
<tr>
	<td valign="top">
		<table border="0" cellpadding="0" cellspacing="0" height="165" align="left" class="video_container_table">

			<?
			foreach ($arVideoList as $arVideo)
			{

                        if ($j == 1) {
                        ?>
                           <tr>

                        <? }
				$title=ucwords(str_replace("_","\n",$arVideo['title']));
				$len=strlen($title);
				if ($len > 31){$title=substr($title,0,30);}
			?>
				<td valign="top">
				<div id="setLetf" class="video_container"><a target="_parent" href='<?=$HTPFX.$VIDEOHOST.$arVideo['url']?>?page=<?=$count?>&section_id=<?=$section_id?>&preroll=0'>
				<img height='104px' width='138px'  src="<?=$arVideo['thumbfile']?>" title="<?=$arVideo['title']?>" /></a></div>
				<div class="combo_bottom_title" ><a target="_parent" href='<?=$HTPFX.$VIDEOHOST.$arVideo['url']?>?page=1&section_id=<?=$section_id?>'><?=$title?></a></div></td>
		<?
         if ($j == 4) {
             $j = 1;
             ?>
             </tr>
        <?
        }
        else {
           $j++;
        }
        ?>
			<?
			}
			?>

		</table>
	</td>
</tr>
<tr>
	<td align='center' valign="top">
	<?
		echo make_ajax_pagination_video("combo_tab",$HTPFX.$VIDEOHOST."/mvtv/videotabscat.php?section_id=".$section_id."&i=".$i,$count,$MAX,$numrows1);
	?>
	</td>
</tr>
<?} else {?>
<tr><td height="150">
No Video found...!
</td></tr>
<?}?>
</table>
