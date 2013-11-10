<?php
session_start();
global $D_R,$HTHOST,$HTPFX,$HTPFXSSL,$IMG_SERVER,$articleListingHomePageSize,$objCache;
$pageName='season_more_info';
$noDefaultLoad=TRUE;
global $cm8_ads_1x1_Text,$cm8_ads_MicroBar;
$bannername=$cm8_ads_1x1_Text;
$bannermicro=$cm8_ads_MicroBar;
$bannernameMR="MediumRectangle_300x250_300x600_bottom";
$pageJS=array('config','dailyfeed','jquery','global','featuredslider','mobileredirect','emailalert','Articles','stickycontent','sticky','stickyscroller','getset','fancybox','mailchimp','modernizr');
$pageCSS=array("global","layout","nav","codaslider","fontStylesheet","homepage","rightColumn","fancybox","mailchimp","sub_homepage");

$tradCalenArr = getKeyVal('tradingcalendar',$_SERVER['REQUEST_URI']);
$ticker = ($tradCalenArr['tradingcalendar']=="" ? $_GET['t'] : $tradCalenArr['tradingcalendar']);
if(empty($ticker)) { $ticker="AAPL"; }

include("_header.htm");
?>
<div class="shadow">
  <div id="content-container">
	<iframe id="SO-frame" src="http://kensho.com/so/ticker/<?=$ticker?>" scrolling="no" width="985" style="min-height:1800px;" frameborder="0"></iframe>
  </div>
</div>
<?php include("_footer.htm"); ?>