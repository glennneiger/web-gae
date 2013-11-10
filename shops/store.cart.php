<?
//include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_includes.php");
	$SECTION="shops";
	$HEADER="/assets/checkoutbanner.jpg";
	$zoneIDSky = $SEC_TO_ZONE['shopstop'];
	$zoneIDFooter = $SEC_TO_ZONE['shopsfooter'];
	$zoneIDFooter2 = $SEC_TO_ZONE['shopsfooter2'];
$page_navigation=$NAVIGATION["COMMUNITY"];
$pageName ="shops";
$pageJS=array("config","ibox","registration","iboxregistration","nav","search");
$pageCSS=array("ibox","global","rightColumn","nav","minyanville");
include("../_header.htm");
	$data=$cart->getCartData();
?>

<div class="shadow">
<div id="content-container">
<div id="article-left">
<div class="shop_main_container">
<div class="footer_common_title">Shopping <br/><span class="current_date"><?=displaydate();?></span> </div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="650" height="280" id="SnowShopsxmas" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="wmode" value="transparent">
<param name="movie" value="../flash/SnowShopsxmas.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="../flash/SnowShopsxmas.swf" quality="high" wmode="transparent" bgcolor="#ffffff" width="650" height="280" name="SnowShopsxmas" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>&nbsp;</td> </tr>

  <tr>
  <td><form method="post" action="<?=$cart->redirect?>" style="margin:0px;padding:0px;" name="cartform">
<?refer()?>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
    <td colspan="6">
     <div class="shop_head">Shopping Cart</div></td></tr>
<?if(!count($data[contents])){?>
<TR>
		<TD class="padding" colspan="6" height="200" valign="top" style="padding-top:10px;padding-left:10px;">Your cart is empty! <a href="<?=$cart->last_product?>">Click here</a> to return.</TD>
</TR>
<?}else{?>
	<TR>
						<TD class="slimpadding"><h5>Delete</h5></TD>
						<TD  class="slimpadding"><h5>Qty</h5></TD>
						<TD class="slimpadding"><h5>Item #</h5></TD>
						<TD class="slimpadding"><h5>Description</h5></TD>
						<TD class="slimpadding"><h5>Unit Price</h5></TD>
						<TD class="slimpadding"><h5>Qty. Price</h5></TD>
	</TR>
	<?foreach($data[contents] as $row){?>
							<TR style='background:#eff3f6'>
							<TD valign="top" width="2%" class="slimpadding"><?input_check("del[${row[id]}]")?></TD>
							<TD width="2%" valign="top" class="slimpadding"><?input_numsonly("upd[${row[id]}]",$row[quantity],1,3)?></TD>
							<TD valign="top" class="slimpadding"><?=$row[sku]?>&nbsp;</td>
							<TD valign="top" class="slimpadding"><?=strip($row[title])?></TD>
							<TD valign="top" class="slimpadding">$<?=dollars($row[price])?></TD>
							<TD valign="top" class="slimpadding">$<?=dollars($row[sum])?></TD>
		</TR>
	<?}?>

	<TR>
						<TD colspan=4 align="left" style="padding-top:5px;">
							<input type="image" style="cursor:pointer" src="<?=$IMG_SERVER?>/images/button_update.gif" name="action:update_cart" value="update" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit">
							<input type="image" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_continueshopping.gif" onclick="location.replace('<?=$cart->last_product?>')"  value="continue shopping" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)">
							<input type="image" style="cursor:pointer"  src="<?=$IMG_SERVER?>/images/button_clearcart.gif" name="action:update_clear" value="clear cart" class="button" onmouseover="bOn(this)" onmouseout="bOff(this)" type="submit">
	</TD>
						<TD colspan="2" align="right nowrap"  style="padding-top:5px;">	Total Price: <b>$<?=dollars($data[total])?></TD>
	</TR>
	<TR>
		  <td colspan="4">&nbsp;</td>
						  <td colspan="2" align="left" nowrap="nowrap" valign="top">
						  <input type="image" style="cursor:pointer" src="<?=$IMG_SERVER?>/images/button_checkout.gif" name="action:update_checkout" value="checkout" class="button" onmouseover="bOn(this)" vspace="5" onmouseout="bOff(this)" type="submit"></td>
	  </TR>
	</TR>
<?}?>
</table>
<?if(count($data[contents]) ){?>
	<center>
	<div style="padding: 10px; text-align: left;">
		<?if(!$cart->hasGiftOnly()){?>

		<?}?>
	<br>
					If you do not wish to use our secure credit card shopping option, you can fill out an <a href="store.print.php" target="_blank" style="text-decoration:underline">order form</a> and fax it to us.  We will then contact you with the order total and with information on how to send your check or money order
					</div>	</center>

<?}?>
</form>

	</td>
		 </tr>
  <tr>
    <td>&nbsp;</td>
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