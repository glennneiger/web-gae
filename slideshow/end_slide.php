 <?
include_once("../lib/_module_data_lib.php");
include_once("../lib/_redesign_design_lib.php");
global $slidesCnt,$endSlideCount,$IMG_SERVER;; // now its 4

$slide_title = $_GET['title'];
$slide_id = $_GET['a'];
$i="0";

$sql_cat_list2="select count(distinct(s.id)) num from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1'";
$result_cat_list2= exec_query($sql_cat_list2,1);
$numrows1=$result_cat_list2['num'];
$sql_cat_list1="select distinct(s.id)as item_id,s.title,s.slide_thumbnail as slide_image from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' and s.id<>".$slide_id." AND  s.id<".$slide_id."  ORDER BY s.id desc limit 0,".$endSlideCount;
$result_cat_list1= exec_query($sql_cat_list1);

if ($numrows1>0){
	foreach ($result_cat_list1 as $row)
			{
				if($i=="0")
				{
					$html.='<div class="Slide_end_container">
<div class="slide_head">
<div class="slide_title" ><img class="slide_icon" src="'.$IMG_SERVER.'/images/slideshow/slides.jpg"><a class="slide_text" href="'.$HTPFX.$HTHOST.'/slideshow/'.$slide_id.'/1">'.$slide_title.'</a></div>
<div class="next_slide" ><a href="'.$HTPFX.$HTHOST.'/slideshow/'.$row['item_id'].'/1"><span class="next_slide_text" > Next Slideshow</span> <img class="slide_next" src="'.$IMG_SERVER.'/images/slideshow/slide_next.jpg"></a> </div>
</div>
<div class="slide_thumbs" ><div class="s_top">';
				}
				$title=ucwords($row['title']);
				$id=$row['item_id'];
				$html.= '<div id="thumb'.$row["item_id"].'" class="slide_thumb"><div style="border:solid 0px red; float:left;">
				<a title="'.ucwords(strtolower($title)).'" href="'.$HTPFX.$HTHOST.'/slideshow/'.$row[item_id].'/'.$count.'"  >';
				$image=$row['slide_image'];
				if($image==''){ $image="$IMG_SERVER/images/slideshow/default_images.gif";}

				$html.= '<img width="100" height="90" border="0" align="bottom" style="width:70px: height:62px;border:1px solid #C9C9C9;" src="'.$image.'"><div class="mv_title">'.ucwords(strtolower($title)).'</div></a></div>';
				$html.= '</div>';
				if($i=="3")
				{
					$html.= '</div><div class="s_bottom">';
				}

			$i++;
			}
}
$html.= '</div>';
$html.='</div><div class="slide_bottom" ><a href="'.$HTPFX.$HTHOST.'/slideshow/'.$slide_id.'/1" > <span style="color:white;" > Restart slideshow </span> <img class="refresh_icon" src="'.$IMG_SERVER.'/images/slideshow/refresh.jpg"> </a></div>
</div>';

echo $html;
