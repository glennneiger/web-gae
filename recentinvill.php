
<script language="javascript">
stepcarouse2.setup({
	galleryid: 'mygallery1', //id of carousel DIV
	beltclass: 'belt1', //class of inner "belt" DIV containing all the panel DIVs
	panelclass: 'panel1', //class of panel DIVs each holding content
	autostep: {enable:false, moveby:1, pause:500},
	panelbehavior: {speed:1200, wraparound:false, persist:false},
	defaultbuttons: {enable: false, moveby: 1, leftnav: ['/images/leftnav.gif', 0, 0], rightnav: ['/images/rightnav.gif', 0, 0]},
	statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
	contenttype: ['inline'] //content setting ['inline'] or ['external', 'path_to_external_file']
})
function select3div(id, count)
{
  var n=navigator.appName;
  var id  = id;
  var sid  ='id'+id;
  var cnt  = count;
  
  if(n=="Netscape")
    {
     var display ="table-cell";
    }
    else
    {
     var display ="inline";
     }
	 if(id=='1')
	 {
	    document.getElementById(id).style.display = display;
	    document.getElementById(sid).style.display ="none" ;
		for (i=2;i<=cnt;i++)
         {
               document.getElementById(i).style.display ="none" ;
	           document.getElementById('id'+i).style.display =display ;
          }

	 }
	 for (j=2; j<=cnt; j++)
	 {
	 if(id==j)
	 {

	    document.getElementById(id).style.display = display;
	    document.getElementById(sid).style.display = "none";
		for (i=1;i<id;i++)
         {
               document.getElementById(i).style.display ="none" ;
		       document.getElementById('id'+i).style.display =display ;
          }
		
		for (i=id+1;i<=cnt;i++)
         {
               document.getElementById(i).style.display ="none" ;
	           document.getElementById('id'+i).style.display =display ;
          }
	 }
	 
	 }
}

</script>

<?
if($_GET['act']=='Preview' and $_GET['mod']=='invill')
	{
$sql_cat_list="select a.title as article,a.id as articleid,a.keyword,a.character_text,
                 h.object_type,m.id as vid,m.title,h.id as hid,h.image_path,
                 m.thumbfile from homepage_module as h left join  articles as a on h.object_id = a.id 
                 left join mvtv as m on h.object_id = m.id
                 where h.module_type='Recent' order by h.order_type";
@$result_cat_list= exec_query($sql_cat_list);
}
// for normal query 
else
{
 $sql_cat_list="select a.title as article,a.id as articleid,a.keyword,a.character_text, 
                 h.object_type,m.id as vid,m.title,h.id as hid,h.image_path,
                 m.thumbfile from homepage_module as h left join  articles as a on h.object_id = a.id 
                 left join mvtv as m on h.object_id = m.id
                 where h.module_type='Recent' and  h.commit_status='P' order by h.order_type";
@$result_cat_list= exec_query($sql_cat_list);

}// end of loop
$defaultimage = $IMG_SERVER."/images/redesign/images5.jpeg";
$value_f = count($result_cat_list)/3 ;
 $cnt    = round($value_f);
 if($value_f >$cnt)
 {
  $count = $cnt+1;
 }
 elseif($value_f < $cnt)
 {
  $count = $cnt;
 }
 else
 {
   $count = $cnt;
 }
 
?>
<div class="slideController">
<div class="amexCustomUnitLeft">
<div class="amexCustomUnitLeftInner" ><img alt="Recently in the ville" src="<?=$HTPFX.$HTHOST?>/images/home_redesign/recentlyville.jpg"/></div><div class="amexCustomUnitRight" ><? CM8_ShowAd("PageTitleSponsorship");?></div>
</div>
<div class="slideControllerRight">
<table align="center" border="0" cellpadding="0" cellspacing="5"><tr>
<td align="justify" valign="middle" ><a  onclick="stepcarouse2.stepBy('mygallery1',-1)"  style="cursor: pointer;" >
<img hspace="5"  src='<?=$IMG_SERVER?>/images/previous_slide_button.jpg' border=0 /></a> 
</td>
<? for($m=1; $m<=$count; $m++){?>
<td align="left" valign="middle"  id="<?=$m ?>" style="display:none" >
<a onclick="stepcarouse2.stepTo('mygallery1',<?=$m ?>),select3div(<?=$m ?>,<?=$count ?>)"  style="cursor: pointer;" ><img src=<?=$IMG_SERVER?>/images/selected_slide_button.jpg border=0 /></a></td>
<td align="left" valign="middle" id="id<?=$m ?>"  style="display:table-cell"><a onclick="stepcarouse2.stepTo('mygallery1',<?=$m ?>),select3div(<?=$m ?>,<?=$count ?>)"  style="cursor: pointer;" ><img src=<?=$IMG_SERVER?>/images/hide_slide_button.jpg border=0 /></a></td>
<? }?>
<td align="left" width="15" valign="middle"><a onclick="stepcarouse2.stepBy('mygallery1',1)"  style="cursor: pointer;" >
<img hspace="5"  src=<?=$IMG_SERVER?>/images/forward_slide_button.jpg border=0 /></a>
</td>
</tr>
</table>
<!-- <img align="right" src="images/control.jpg"/>
 --></div>

</div>


 <div class="Reacentslidecontainer">
<div id="mygallery1" class="stepcarouse2">
<div class="belt1">
<? if(count($result_cat_list)>0){

?>
<? for($i=1; $i<=$count;$i++){
$t =$i;
if($i==1)
{
$k=0;
$b=0;
}
else
{
$k=($i-1)*3;
$b=($i-1)*3;
}

?>
<div class="panel1">
<table width="98%" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr>
  <?
  $t =$i; 
  for($j=$k;$j<$i*3;$j++)
  {
      if($result_cat_list[$j]['object_type']=='1')
      {
       if($result_cat_list[$j]['image_path']!='') // for image  articel
         {
           $imgpath = $IMG_SERVER."/assets/featureimage/".$result_cat_list[$j]['image_path'];
           }
            else
                 {
                  $imgpath = $defaultimage;
                 } // articel
  // for url article
  $readmore =  makeArticleslink($result_cat_list[$j]['articleid'],$result_cat_list[$j]['keyword'],
               $result_cat_list[$j]['character_text'],$from=NULL,$page=NULL);
   $urlpath = $readmore;
  // title
  }// end for articles
   else
  {
   if($result_cat_list[$j]['image_path']!='')
	  {
	   $imgpath = $IMG_SERVER."/assets/featureimage/".$result_cat_list[$j]['image_path'];
	  }
	  else
	  {
	   $imgpath = $defaultimage;
	  }// for image
	  // for url 
	  $urlpath = $HTPFX.$HTHOST.'/mvtv/audio_video.htm?videoid='.$result_cat_list[$j]['vid'];
	  }
  // now for video 
     ?>
	 <? if($result_cat_list[$j]['hid']!=''){?>
	<td valign="middle" align="center" >
	<div class="recent_slide_inner"><a href="<?= $urlpath  ?>"><img src="<?=$imgpath ?>" height="156" width="205"  border="0"/>
	</a></div>
	</td>
	<? } else{?>
	<td valign="middle" align="center" >
	<div class="recent_slide_display_inner">&nbsp;</div>
	</td>
	<? }?>
	<?
	} 
	?>
  </tr>
  <tr>
    <? 
  for($a=$b;$a<$i*3;$a++)
  {
      if($result_cat_list[$a]['object_type']=='1')
      {
  // for url article
  $readmore =  makeArticleslink($result_cat_list[$a]['articleid'],$result_cat_list[$a]['keyword'],
               $result_cat_list[$a]['character_text'],$from=NULL,$page=NULL);
   $urlpath = $readmore;
  // title
  
      if($result_cat_list[$a]['hid']!=''){
	  //echo $result_cat_list[$a]['hid'];
	  if($result_cat_list[$a]['article']!='')
	  {
	  $subtitle = substr($result_cat_list[$a]['article'],0,30)."..";
	  }
	  else
	  {
	   $subtitle='&nbsp;';
	  }
	 $contenet = "<td valign='middle' align='center'  class='recent_slide_title'>
	   <table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td  valign='top' align='center' >&nbsp;</td>
	   <td valign='middle' align='center' height='24'> <a href='".$HTPFX.$HTHOST.$urlpath."'>".$subtitle."</a></td>
	   </tr></table>
	  </td>";
	 }
	 else
	 {
	 
	    $contenet = "<td valign='middle' align='center'  class='recent_slide_title'>
	   <table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td  valign='top' align='center' >&nbsp;</td>
	   <td valign='middle' align='center' height='24'> &nbsp;</td>
	   </tr></table>
	  </td>";
	 }
	 echo $contenet;  
  }// end for articles
   if($result_cat_list[$a]['object_type']!='1')
  {
	 // for url
	 if($result_cat_list[$a]['hid']!='')
	 { 
	  if($result_cat_list[$a]['title']!='')
	  {
	  $subtitle = substr($result_cat_list[$a]['title'],0,30)."..";
	  }
	  else{
	  $subtitle='&nbsp;';
	  }
	  $urlpath = $HTPFX.$HTHOST.'/mvtv/audio_video.htm?videoid='.$result_cat_list[$a]['vid'];
	   $contenet = "<td valign='middle' align='center'  class='recent_slide_title'>
	   <table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td  valign='top' align='right' >
	   <a href=".$urlpath."><img src='".$IMG_SERVER."/images/home_redesign/video_logo.jpg' hspace='0' border='0' ></a></td>
	   <td valign='middle' align='left'><a href=".$urlpath.">".$subtitle."</a></td>
	   </tr></table>
	  </td>";
	  }
	  else
	  {
	    $contenet = "<td valign='middle' align='center'  class='recent_slide_title'>
	   <table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td  valign='top' align='center' >&nbsp;</td>
	   <td valign='middle' align='left'>&nbsp;</td>
	   </tr></table>
	  </td>";
	  }
	   echo $contenet;
	  }
  // now for video
 
     ?>

	<?
	} 
	?>
  </tr>
</table>
</div>
<? }?>
<? } else{?>
<p align="center"><div class="recent_slide_title">No Photo  found...!</div></p>
<? }?>
</div>
</div>
</div>
<script>select3div(1,<?=$count ?>)</script>