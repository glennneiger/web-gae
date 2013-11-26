<?
session_start();
global $HTPFX,$HTHOST,$CDN_SERVER;
include_once("$D_R/lib/_auth.php");
include_once($D_R."/lib/techstrat/_techstratDesign.php");
include_once($D_R."/lib/techstrat/_techstratData.php");
include_once($D_R."/lib/config/_techstrat_config.php");
$objTechStart = new techstartData("techstrat_posts");
$objTechStart->redirectLandingPage();

$objTechStartDesign= new techstartDesign();


$pageName="techstrat_print";
if(($_GET['a'] != "") && (is_numeric($_GET['a'])))
{
		$searchResult 	=  $objTechStart->getPostData($_GET['a']);
		$tag			=	$objTechStartDesign->displayPostTags($_GET['a']);
		$catName		=  $objTechStartDesign->displayCategoryName($searchResult['category_id']);
		$id				=	trim($_GET['a']);
		$position		=	$searchResult['position'];
}
?>
<html>
<head>
	<link rel="stylesheet" href="<?=$CDN_SERVER?>/css/techstrat.1.5.css">
	<script language="javascript" type="text/javascript">
	function docKeyDown1(oEvent){ // controls keyboard navigation
	var oEvent = (typeof oEvent != "undefined")? oEvent : event;
	var oTarget = (typeof oEvent.target != "undefined")? oEvent.target : oEvent.srcElement;
	var intKeyCode = oEvent.keyCode;

	if(intKeyCode==17)
	{
		inc=0;

	}
	if (inc==0)
	{
		if(intKeyCode==65 || intKeyCode==97 )
		{

			return(false);   //in case of IE & firefox1.5

		}
	}

	}
	document.onselectstart=new Function("return false");
	document.onmousedown=new Function("return false");
	document.oncontextmenu=new Function("return false");
	document.onkeydown=docKeyDown1;

	function disableclick()
	{
		document.onmousedown=new Function("return false");void(0);
	}
	function enableclick()
	{
		document.onmousedown=new Function("return true");void(0);
	}
</script>
</head>
<body onLoad="javascript:window.print();">
<div id="header">
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"></p>
</div>
<div class="left_contant">
	<div class="print_container">
		<div class="sub_techstrat_home_title">TechStrat with Sean Udall</div>
		<div class="posttime"><?=date("M j, Y g:i A",strtotime($searchResult['publish_date']));?></div>
						<div class="postheading"><?=$searchResult['title'];?></div>
						<div style="clear:both;"></div>
						<div class="post_tags">
							<? if($catName!='') { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
						 <? if($tag) {?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
						</div>
      					<div class="postcontainer"><?=$searchResult['body'];?></div>
						<? if($position!='') { ?>
						<div class="position_text"><?=$position;?></div>
						<?php }?>
	</div>
</div>
<br>
</body>
</html>
