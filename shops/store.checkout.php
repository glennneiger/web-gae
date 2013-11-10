<?
global $D_R;
include_once($D_R."/lib/_cart.php");
$cart=new CartDisplay();
//resurrect cart from a post
$cart->acceptCartData();

//change cart url to checkout redirector so it can update the cart data on the unsecure server
$cart->carturl="store.checkout.process.php?step=backtocart";
$step = $_GET['step'];
$SECTION="shops";
$pageName ="shops";
$pageJS=array("config","ibox","registration","iboxregistration","nav","search");
$pageCSS=array("ibox","global","rightColumn","nav","minyanville");
include("../_header.htm");

$HEADER="/assets/checkoutbanner.jpg";
$zoneIDSky = $SEC_TO_ZONE['shopstop'];
$zoneIDFooter = $SEC_TO_ZONE['shopsfooter'];
$zoneIDFooter2 = $SEC_TO_ZONE['shopsfooter2'];
$page_navigation=$NAVIGATION["COMMUNITY"];

include("$D_R/lib/layout_functions.php");
 ?>
<div class="shadow">
<div id="content-container">
<div id="article-left">
	<div class="shop_main_container">
		<div class="footer_common_title">Shopping <br/><span class="current_date"><?=displaydate();?></span> </div>
   		<table cellpadding="0" cellspacing="0" border="0" width="100%" align="left">
		<tr><td>
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="650" height="280" id="SnowShopsxmas" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="wmode" value="transparent">
		<param name="movie" value="../flash/SnowShopsxmas.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="../flash/SnowShopsxmas.swf" quality="high" wmode="transparent" bgcolor="#ffffff" width="650" height="280" name="SnowShopsxmas" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>&nbsp;

		</td></tr>

   		<tr><td><div class="shop_head">Shopping Cart </div></td></tr>
   		<tr><td>

	<?
		switch($step)
		{
   	case "confirm":
   		include("$STORE_TEMPLATE_DIR/store.checkout.confirm.tmpl.php");
   		break;
   	case "thanks":
   		include("$STORE_TEMPLATE_DIR/store.checkout.thanks.tmpl.php");
   		break;
   	case "egift":
   		include("$STORE_TEMPLATE_DIR/store.checkout.egift.tmpl.php");
   		break;
   	default:
   		include("$STORE_TEMPLATE_DIR/store.checkout.form.tmpl.php");
   }
   ?>

   </td>
   		 </tr>
		</table>

</div>
</div>
<?php
$arrRightCols	=	array('show_video','show_industrybrains','show_mostpopular','email_alert','show_rss_emailalert','show_localguides');
include("../_rightcolumn.htm"); ?>
</div><!--Content contaner end here-->
</div> <!-- shadow end -->
<? include("../_footer.htm"); ?>