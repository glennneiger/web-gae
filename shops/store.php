<?
$SEC="store";
//phpAdServe zone ID
$zoneIDSky = $SEC_TO_ZONE['shopstop'];
$zoneIDFooter = $SEC_TO_ZONE['shopsfooter'];
$zoneIDFooter2 = $SEC_TO_ZONE['shopsfooter2'];
$page_navigation=$NAVIGATION["COMMUNITY"];

$PAGE="store";
$pageName ="shops";
$pageJS=array("config","ibox","registration","iboxregistration","nav","search");
$pageCSS=array("ibox","global","rightColumn","nav","minyanville");
include("../_header.htm");
include_once("$D_R/lib/layout_functions.php");

$store=new StoreDisplay();
//$cart is created in the _header.php file- needed for persistent cart msg
$cart->setLastProductUrl();
$prod_id=$store->prod_id;
$cat_id=$store->cat_id;
?>
<div class="shadow">
<div id="content-container">
<div id="article-left">
	<div class="shop_main_container">
		<div class="footer_common_title">Shopping<br/><span class="current_date"><?=displaydate();?></span> </div>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" align="left">
				<tr><td>
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="650" height="280" id="SnowShopsxmas" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="wmode" value="transparent">
				<param name="movie" value="../flash/SnowShopsxmas.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="../flash/SnowShopsxmas.swf" quality="high" wmode="transparent" bgcolor="#ffffff" width="650" height="280" name="SnowShopsxmas" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
				</object>&nbsp;

				</td></tr>
				<tr><td ><div class="shop_head"><?= $PAGE;?></div></td></tr>
  <tr><td clas="articleBodyshops">
  <?if($store->catHasProducts($cat_id)){?>
  	<div >
  		<?$store->displayAllProducts("$STORE_TEMPLATE_DIR/store.prods.tmpl.php")?>

  	</div>
				<div><P><?readfile("$D_R/assets/data/shipping_info.html")?></P></div>
  <?}?>
  		</td>
  		 </tr>
  		</table>
  <!-- end content area -->
</div>
</div>
<?php
$arrRightCols	=	array('show_video','show_industrybrains','show_mostpopular','email_alert','show_rss_emailalert','show_localguides');
include("../_rightcolumn.htm"); ?>
</div><!--Content contaner end here-->
</div> <!-- shadow end -->
<? include("../_footer.htm"); ?>