<?php
include_once("../layout/dbconnect.php");
include_once("../lib/layout_functions.php");
if($_POST['requestForses']==1)
{
if($_POST['ses']!=''){
 session_start();
 $_SESSION['defaulttime']=$_POST['ses'];
 mcookie($_POST['coksid'],$_POST['ses']);
 echo "true";
 exit;
}else if($_POST['ses']==''){
	echo "false";
	exit;
}
}
if($_POST['requestForSlide']==1)
{
	$slideshowtype=$_POST['slideshowtype'];
	$slideTags=array();
	$relatedTags=$_POST['relatedTags'];
	$slideTags=getRelatedSlides($_POST['slideID'],$_POST['offset'],$_POST['limit'],$slideshowtype,$relatedTags);
	$totalslides=$slideTags['totalslides'];
	echo getSlideContent($totalslides,$slideTags,$_POST['slideID']);
}
else if($_POST['requestForadds']==1)
{
	$ord=$_POST['ord'];
	global $_SESSION,$cm8_ads_MediumRectangle;
	$pageName="SlideShow";
	$bannername =$cm8_ads_Leaderboard;
	show_adds_iframe_checkmate($pageName,$bannername);


}
else if($_POST['requestForRightadds']==1)
{
	$ord=$_POST['ord'];
	global $_SESSION,$cm8_ads_MediumRectangle;
	$pageName="SlideShow";
	$bannername =$cm8_ads_MediumRectangle;
	show_adds_iframe_checkmate($pageName,$bannername,"250","300");

}
else if($_POST['requestForReload']==1){
	//$slideshowtodisplay=getLatestRelatedSlideShow(intval($_POST['curnt_slideshowid']));
	$curnt_slideshowid=$_POST['curnt_slideshowid'];
	$slideshowtodisplay= getLatestRandomSlideShow($curnt_slideshowid);
	if($slideshowtodisplay!=''){
		echo $slideshowtodisplay;
	}else{
		echo "noslide~0";
	}
}else{
$Slideid = $_GET['a'];
$slide_no = $_GET['slide_no'];
$preview = $_GET['preview']?$_GET['preview']:0;
$lastpreview=$_GET['lastpreview'];
$firstpreview=$_GET['firstpreview'];

if (($_GET['a'] != "") && (is_numeric($_GET['a'])))
{
	$slideid = $_GET['a'];
	if($_GET['slide_no'] =="")	 {
     $slide_no = 1;
	 }
	 elseif(($_GET['slide_no'] !=="") && (is_numeric($_GET['slide_no'])))
	 {
	  $slide_no = $_GET['slide_no'];
	 }
	$slide = getSlideShow($slideid,$slide_no,$preview);
	//htmlprint_r($slide);

}
$slide['body'] = str_replace("gazette/newsviews/?id","slideshow/index.php?a",$slide['body']);
$imgURL=$slide['image'];
if($imgURL=='')
{
	$imgURL='No Slide Image';
}
$hasNext  = 'N';
$hasPrev  = 'N';
//$pattern='/(\'|")/';
//$slide['slide_title'] = preg_replace($pattern,"",$slide['slide_title']);
//$slide['slide_title'] = ucwords(strtolower($slide['slide_title']));
$hasNext=($slide['total_slides']!=$slide_no)?'Y':'N';
$hasPrev=($slide_no==1)?'N':'Y';
$returntext = '';
$returntext = '~'.$hasPrev.'~'.$hasNext.'~'.$_GET['slide_no'].'~'.$slide['slide_title'].'~'.$slide['body'].'~'.$lastpreview.'~'.$firstpreview.'~'.$imgURL.'~'.$slide['imgwidth'].'~'.$slide['imgheight'];
echo $returntext;
}
?>