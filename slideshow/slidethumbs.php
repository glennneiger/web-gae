 <?
global $D_R, $HTPFX,$HTHOST,$IMG_SERVER;
include_once($D_R."/lib/_module_data_lib.php");
include_once($D_R."/lib/_redesign_design_lib.php");
global $slidesCnt; // now its 4	
include_once($D_R.'/lib/config/_slideshow_config.php');
$MAX=$slidesCnt;

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
		if($pageno!=0 || $pageno!=''){
		$count=$pageno; // here set the page number
		}else{
			$count=1; // here set the page number
		}
	}
}
else
{
	$count=$_GET['count'];
}
$start=($count-1)*$MAX;
$sql_cat_list2="select count(distinct(s.id)) num from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1'";
$result_cat_list2= exec_query($sql_cat_list2,1);
$numrows1=$result_cat_list2['num'];
$sql_cat_list1="select distinct(s.id)as item_id,s.title,s.slide_thumbnail as slide_image from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY s.id desc limit $start,$MAX";
$result_cat_list1= exec_query($sql_cat_list1);

$slidecontent='<div id="mainSlideDiv" class="mainslidedivs">';

if ($numrows1>0){
$slidecontent.='<div style="height:140px; float:left;">';
	foreach ($result_cat_list1 as $row1)
			{
				$title=ucwords($row1['title']);
				$id=$row1['item_id'];

				if($id == $_GET['a'])
					$slidecontent.='<div class="mv_tumb_selected" id="thumb_'.$row1["item_id"].'">';
				else
					$slidecontent.='<div class="mv_tumb" id="thumb_'.$row1["item_id"].'">';

				$slidecontent.='<div style="border:solid 0px red; float:left;">
				<a href="'.$HTPFX.$HTHOST.'/slideshow/'.$row1[item_id].'/'.$count.'" title="'.ucwords(strtolower($title)).'">
				<img  style="width:70px: height:62px;border:1px solid #C9C9C9;" src="';
				$image=$row1['slide_image'];
				if($image==''){ $image="$IMG_SERVER/images/slideshow/default_images.gif";}
				$slidecontent.=$image;
				$slidecontent.='" width="70" height="62" align="bottom" border="0" />';
				$slidecontent.='<div class="mv_tumbdiv">';
				$slidecontent.=ucwords(strtolower($title));
				$slidecontent.='</div></a></div></div>';
				}
				$slidecontent.='</div>';
				$slidecontent.='<img src="'.$IMG_SERVER.'/images/slideshow/line.jpg" >';
				$slidecontent.=make_ajax_pagination_slide("combo_tab$i","$HTPFX$HTHOST/slideshow/slidethumbs.php?i=$i",$count,$MAX,$numrows1);
}
else
	{
		$slidecontent='<p valign="middle" align="center" style="color:#FFF;">No Slideshow found...!</p>';
		echo $slidecontent."</div>";
		exit;
	}

//echo 
echo $slidecontent."</div>";
?>