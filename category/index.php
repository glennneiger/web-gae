<?php
if($_SERVER['REQUEST_URI'])
{
	$pageNameArr= explode('/',$_SERVER['REQUEST_URI']);
	$pageName = $pageNameArr[1];
	if($pageName == "special-features")
	{
		$pageName = "specialfeatures";
	}
}
$pageId=184;
global $cm8_ads_Button_160x30,$cm8_ads_MediumRectangle,$D_R;
include($D_R."/lib/includes.php");
include_once($D_R."/lib/_article_design_lib.php");
include_once($D_R."/lib/_disqus_lib.php");
include_once($D_R."/lib/disqus/disqusapi/disqusapi.php");
include_once($D_R."/lib/_misc.php");
include_once($D_R."/lib/config/_cache_config.php");
include_once($D_R."/admin/lib/_article_data_lib.php");
$page_name = "category";
$noDefaultLoad=TRUE;
$pageJS=array('jquery',"config","global","nav","redesign",'dailyfeed','Articles','mobileredirect','emailalert');
$pageCSS=array("global","layout","section","rightColumn","nav","global","fontStylesheet");
$objCache = new Cache();
$objArticle = new articleViewer();
$subSection=strtolower($pageName);

if($subSection=="specialfeatures"){
	$subSection="special-features";
}
$subSectionData =$objCache->getTopicSectionCache($subSection);
$sectionId = $sectionIdTopic = $subSectionData['section_id'];
$subSectionName=$subSectionData['name'];
include("../_header.htm");
$bannernameMR =$cm8_ads_MediumRectangle;
$bannername=$cm8_ads_Button_160x30;
?>
<div class="shadow">
<div id="content-container">
<?=showBreadCrum($breadcrum); ?>
<div class="section_title_main">
	<?php $objArticle->articleHeader($pageDetail['page_title']); ?>

	<div class="free_min">
		<?php $objArticle->articleSubscribeNewsLetter($subSectionData,$sectionIdTopic); ?>
	</div>
</div>

<div id="article-left">
	<!--<div class="shadow">-->
	<div id="homepage-content">
    <? if($pageName == 'specialfeatures'){ $margin= '12px';}else{$margin= '112px';} ?>
	<div style="clear:both;"></div>

<div id="homepage-top">
<div class="section-two-column">
<div class="allArtcl-lft-module" id="all-articles-module">
<?php echo $objCache->getPageModules($pageDetail['id'],'Column1'); ?>
</div>

<div class="allArtcl-rght-module" id="all-articles-module">
<?php echo $objCache->getPageModules($pageDetail['id'],'Column2'); ?>
</div>
</div>
<div class="top-module">
<?php echo $objCache->getPageModules($pageDetail['id'],'Column3'); ?>
</div>
</div> <!-- end homepage-top -->
<div style="clear:both;"></div>
</div> <!-- home content div ends -->
</div>
<?

$itemType=1;
$sectionId=$sectionIdTopic;
global $arrRightCols;
$arrRightCols	=	array('show_300x250','sponsoredlinks','most_commented','most_read','most_tickers','financial_product_guide','show_MediumRectangle_300x250');
include("../_rightcolumn.htm");
?>
</div><!-- Content Container-->
</div> <!-- end shadow -->
<?php include("../_footer.htm"); ?>