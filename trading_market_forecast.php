<?php
session_start();
global $D_R,$HTHOST,$HTPFX,$HTPFXSSL,$IMG_SERVER,$articleListingHomePageSize,$objCache;
$pageName='season_market_forecast';
$noDefaultLoad=TRUE;
global $cm8_ads_1x1_Text,$cm8_ads_MicroBar;
$bannername=$cm8_ads_1x1_Text;
$bannermicro=$cm8_ads_MicroBar;
$bannernameMR="MediumRectangle_300x250_300x600_bottom";
$pageJS=array('config','dailyfeed','jquery','global','featuredslider','mobileredirect','emailalert','Articles','stickycontent','sticky','stickyscroller','getset','fancybox','mailchimp','modernizr');
$pageCSS=array("global","layout","nav","codaslider","fontStylesheet","homepage","rightColumn","fancybox","mailchimp","sub_homepage");

include("_header.htm");
?>
<div class="shadow">
  <div id="content-container">
	<iframe width="985" scrolling="no" frameborder="0" id="MF-frame" src="http://kensho.com/mf/s-and-p-500/next-week/?utm_source=mville" style="min-height:800px;"></iframe>
  </div>
</div>
<?php include("_footer.htm"); ?>