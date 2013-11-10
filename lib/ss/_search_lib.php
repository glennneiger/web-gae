<?

function makeAlertlink($id,$recent_id)
 {
    if($id) {
		$link="/optionsmith/post/id/".$id;
	}
	if($recent_id) {
		$link="/optionsmith/post/id/".$id;
	}
	return $link;
 }
 
function getalert($id) {
	
	$sql = "select title,body,UNIX_TIMESTAMP(date) udate,type from ss_alerts where id='$id'";
	$results = exec_query($sql,1);
	if (count($results['body']) == 1) {
		return $results;
	} else {
		return 0;
	}
}

function makevideolink($id)
 {
    if($id) {
		$link="/qp/mvtv/video/id/".$id;
	}

	return $link;
 }
 
 
 function makemvtvstillContainer($id,$offset,$download=0)
{

?>
  <div id="NewsViewsTab"><br />
  <div class="MvtvArticleModuleHeaders">Episodes</div>
  <div class="mvtvThumbnailContainer">
    <table border="0" cellspacing="0" cellpadding="0">
	 <?
    $sql="SELECT id,title,stillfile FROM qp_mvtv WHERE approved='1' ORDER BY creation_time DESC LIMIT ".$offset.",9";
	$results = exec_query($sql);
	$stillrows=mysql_num_rows($results)/3;
	$count=0;
	//while ($row = mysql_fetch_assoc($results)) {
	foreach($results as $row){
		$mvtv[$count]['title']=$row['title'];
		$mvtv[$count]['stillfile']=$row['stillfile'];
		$mvtv[$count]['id']=$row['id'];
		//$mvtv[$count]['videofile_wmv']=$row['videofile_wmv'];
		$count++;
		}
  ?>
      <tr>
        <td width="10">&nbsp;</td>
        <td>
	<table width="312" border="0" cellspacing="0" cellpadding="0">
	<?
	$count=0;
	for($i=0;$i<$stillrows;$i++)
	{ ?>
	<tr>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['stillfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['stillfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['stillfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
	</tr>
	<? $count-=3;?>
	<tr>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
	</tr>

   	 <?if($mvtv[$count]){ ?><tr> <td height="8" colspan="3" valign="middle"><img src="<?$IMG_SERVER;?>/images/mvtv_horizontal_line.gif" width="313" height="1" /></td></tr><? } ?>

	<? } ?>
    </table>
		</td>
        <td width="10">&nbsp;</td>
      </tr>
</table>
<div>Pages<br /><?
    	$sql="SELECT id,title,stillfile FROM qp_mvtv WHERE approved='1' ORDER BY creation_time DESC";
  	$numrows=num_rows($sql);
  	$numPages = floor(($numrows + 9 - 1) / 9);
  	for($i=0;$i<$numPages;$i++)
  	{
  		if($offset==$i*9)
  			echo '<font color="#000000">'.($i+1).'</font>';
  		else{
  	?>
  		<a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?id='.$id.'&offset='.($i*9) ?>"><?=$i+1?></a>
  <?	}}
    ?>
  </div>
  </div></div>
 
<? }

function mOverarchive($upstate,$downstate,$link,$downif=0,$addlimg="",$addllink=""){
	global $ABS_PATHS,$D_R;
	$PATH='/images/quint_images/';
	$downstate="$PATH$downstate";
	$upstate=($downif?$downstate:"$PATH$upstate");
	list($w,$h)=@getimagesize("$D_R$upstate");
	if(!stristr($addlimg,"width"))
		$addlimg.=" width=$w";
	if(!stristr($addlimg,"height"))
		$addlimg.=" height=$h";

	mouseover($upstate,$downstate,$link,$addlimg,$addllink);
}

function displayblogssearchbox()  // show search box in the footer
{   
         global $HTPFX,$HTHOST;
	$USER=new user($_SESSION[EMAIL],$_SESSION[PASSWORD]);
	$id = $USER->id;
	$loginoption = $USER->is_option();
	if($loginoption)
	{
		$html='<div><form method="post" name="searchFrm" action="'.$HTPFX.$HTHOST.'/qp/search_alerts.htm">';
		$html.='<h2>alert search</h2>';
		$html.='<table width="100%" border="0" cellpadding="5" cellspacing="5">';
  		$html.='<tr>';
	    $html.='<td width="74%"><input type="text" name="q" class="quint_alert_input"/></td';
		$html.='<td width="26%" valign="top"><input type="image" src="'.$IMG_SERVER.'/images/go.jpg" border="0" /></td>';	
		$html.='</tr>';
		$html.='</table>';
	 $html.='</form></div>';
	 
		echo $html;
	}
}

?>