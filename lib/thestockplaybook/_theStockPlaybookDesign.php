<?
class theStockPlaybookDesign{

	function displayleftColumn($arPD){

		global $IMG_SERVER,$HTHOST,$HTPFXSSL;

		?>
<div class="tsp_left_column1" style="padding-top: 8px;"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
<img style="cursor: pointer;"
	src="<?=$IMG_SERVER?>/images/subscription/tspb_14_long.jpg"
	class="freeimg" alt=""> </a> <? //$this->displayaddtocartvertical();?>
</div>

<div class="tsp_left_column2">
<table cellspacing="4" cellpadding="0" border="0" width="100%"
	class="tsp_growyourprofit">
	<tbody>
		<tr>
			<td colspan="2" class="tsp_growyourprofit_title"
				style="font-size: 21px;">Grow Your Portfolio With:</td>
		</tr>
		<tr>
			<td><img src="<?=$IMG_SERVER;?>/images/tsp/chart_Ico.png"></td>
			<td><strong>Trackable</strong> Portfolios with All Positions</td>
			</td>
		</tr>
		<tr>
			<td colspan="2"><img class="horizontal_divider"
				alt="horizontal divider"
				src="<?=$IMG_SERVER;?>/images/tsp/quick_link_divder.gif"></td>
		</tr>
		<tr>
			<td><img src="<?=$IMG_SERVER;?>/images/tsp/video_Ico.png"></td>
			<td><strong>Daily LIVE Videos</strong> Telling You What Dave is
			Buying &amp; Selling</td>
			</td>
		</tr>
		<tr>
			<td colspan="2"><img height="1" width="350" alt="horizontal divider"
				src="<?=$IMG_SERVER;?>/images/tsp/quick_link_divder.gif"></td>
		</tr>
		<tr>
			<td><img src="<?=$IMG_SERVER;?>/images/tsp/eMail_Ico.png"></td>
			<td><strong>Instant Email Notifications</strong> with portfolio
			changes</td>
			</td>
		</tr>
		<tr>
			<td colspan="2"><img height="1" width="350" alt="horizontal divider"
				src="<?=$IMG_SERVER;?>/images/tsp/quick_link_divder.gif"></td>
		</tr>
		<tr>
			<td><img src="<?=$IMG_SERVER;?>/images/tsp/20prc_Ico.png"></td>
			<td><strong>Over 20 Years</strong> of Professional Experience</td>
			</td>
		</tr>
		<tr>
			<td colspan="2"><img height="1" width="350" alt="horizontal divider"
				src="<?=$IMG_SERVER;?>/images/tsp/quick_link_divder.gif"></td>
		</tr>
		<tr>
			<td><img src="<?=$IMG_SERVER;?>/images/tsp/stop_Ico.png"></td>
			<td><strong><strong>Stop-loss, Breakthrough,</strong> and <strong>Pull-back</strong>
			Values</td>
			</td>
		</tr>

</table>
</div>
<div style="clear: both;"></div>
<!--Grow Portfolio Container Ends-->
<!--What can I Expect Container Starts-->
<div class="whatcaniexpect">
<div
	style="font-family: Arial; font-size: 21px; color: rgb(17, 59, 97);"><strong>What
Can I Expect?</strong></div>
The Stock Playbook provides the individual investor with a daily
briefing of the general markets and daily stock market ideas, either
long or short depending on the overall trend of the market.<br>
<br>
As a member of The Stock Playbook you will get:<br>
<br>
		<? if($this->variation==1) { ?>
<ul>
	<li><strong>Daily emails</strong> with a short recap on the days
	recommendations</li>
	<li><strong>Daily "live" videos</strong> with Dave's stock picks and
	trading commentary</li>
	<li>Daily <strong>detailed chart analysis</strong> of Dave's favorite
	investment ideas</li>
	<li>A <strong>market summary</strong> and look at the Dow, S&amp;P 500
	and Nasdaq</li>
	<li><strong>Bonus videos</strong> detailing earnings plays, stocks
	under $10 and stocks making new highs</li>
	<li>Expert <strong>entry points</strong>, exit points and stop loss
	levels to make money in any market</li>
	<li>Undiscovered gems just starting to hit the radars of professional
	money managers</li>
	<li><strong>Education</strong>... Watch over Dave's shoulder as he
	analyzes stocks</li>
	<li>Full access to The Stock Playbook <strong>archives</strong></li>
</ul>
<div class=variation1-leftcolumn"><br />
Dave's daily videos help you capitalize on market direction and educate
you on how to trade using fundamental and technical analysis. With so
many web sites claiming to be experts in market timing, Dave is the only
one who actually shows you why specific stocks and markets move higher
and lower.</div>
		<? }else{ ?>
<ul>
	<li><strong>Daily "live" videos</strong> with Dave's stock picks and
	trading commentary</li>
	<li>Daily <strong>detailed chart analysis</strong> of Dave's favorite
	investment ideas</li>
	<li><strong>Bonus videos</strong> detailing earnings plays, stocks
	under $10 and stocks making new highs</li>
	<li>Undiscovered gems just starting to hit the radars of professional
	money managers</li>
	<li>And Much More...</strong></li>
</ul>
<div>
<div class="tsp_highlight" style="text-align: center; width: 100%;">Sign
up for The Stock Playbook now...</div>
<div style="padding-top: 5px;"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
<img style="cursor: pointer;"
	src="<?=$IMG_SERVER?>/images/subscription/tspb_14_wide.jpg"
	class="freeimg" alt=""> </a></div>
</div>
		<? } ?> <br>
</div>
		<?
	}

	function displayAboutDave(){
		global $IMG_SERVER;
		?>
<div class="aboutdave-container">
<div class="aboutdave">
<div class="aboutdave_header"><img hspace="8" alt="About dave"
	src="<?=$IMG_SERVER;?>/images/tsp/abtDaveHed_01.gif"></div>
<img height="80" align="left" width="78" alt="Dave Dispennette"
	src="<?=$IMG_SERVER;?>/images/tsp/davePic01.jpg">Dave Dispennette has
over 20 years experience navigating the stock market. He has spent the
last seven years as an independent portfolio manager.<br>
<strong>Prior to striking on his own, he was:</strong> <br>
Head Equity Strategist, Registered Options Principal, Partner of Concord
Equities' West Hampton, N.Y. office.<br>
<br>
Early in his career, he developed his proprietary system designed to
identify stocks and options poised for explosive moves which he now uses
in The Stock Playbook to present clear and concise instructions for each
trade. The proprietary system combines:<br>
<ul>
	<li>CANSLIM techniques</li>
	<li>Technical and fundamental analysis</li>
	<li>Timing indicators</li>
</ul>
<br>
</div>
</div>
		<?
	}

	function displayPerformanceChart(){
		global $IMG_SERVER;
		?>
<div class="performance_header"><img alt="Historical Gains and Losses"
	src="<?=$IMG_SERVER;?>/images/tsp/chartHed_01.gif"><br>
<br>
		<? if($this->variation==1) { ?> <img alt="Chart"
	src="<?=$IMG_SERVER;?>/images/tsp/stockbookChart_01.jpg"></div>
		<? }else{ ?>
<img
	alt="Chart" src="<?=$IMG_SERVER;?>/images/tsp/stockbookChart_02.jpg">
</div>
		<?
		}
	}

	function displayaddtocart(){
		global $lang, $IMG_SERVER,$D_R;
		include_once("$D_R/lib/thestockplaybook/_theStockPlaybookData.php");
		$objTheStockPlaybook = new theStockPlaybook();
		$arTheStockPlaybookProductType = $objTheStockPlaybook->getTheStockPlaybookProductType();
		$str = '<table width="100%" align="right" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td valign="top" class="tsp_subs_cart_container">';
		foreach($arTheStockPlaybookProductType as $arRow)
		{
			$str.='<div class="tsp_cart_variation1">
						<h2>'.strtoupper($arRow['product_type']).'</h2>
						<h1>'.$arRow['price_format'].'</h1>
						<div style="width:100%;background-color:#cccccc"><img id="trialshowbtn-'.$arRow['subscription_def_id'].'" src="'.$IMG_SERVER.'/images/tsp/add_to_cart.gif" alt="add to cart" border="0" onclick="checkcart(\''.$arRow['subscription_def_id'].'\',\''.$arRow['oc_id'].'\',\''.$arRow['orderItemType'].'\',\''.$arRow['google_analytics_product_name'].'\',\''.$arRow['product_type'].'\',\''.$arRow['google_analytics_action'].'\');" style="cursor:pointer;"/>
						<img id="trialhidebtn-'.$arRow['subscription_def_id'].'" src="'.$IMG_SERVER.'/images/tsp/add_to_cart.gif" alt="add to cart" border="0" style="display:none;"/>
						</div>
						</div>';
		}
		$str.='</td>
				  </tr>
				</table><div style="clear:both;"></div>
				';
		echo $str;
	}

	function displayaddtocartvertical(){
		global $lang, $IMG_SERVER,$D_R;
		include_once("$D_R/lib/thestockplaybook/_theStockPlaybookData.php");
		$objTheStockPlaybook = new theStockPlaybook();
		$arTheStockPlaybookProductType = $objTheStockPlaybook->getTheStockPlaybookProductType();
		?>
<table cellspacing="2" cellpadding="4" class="tsp_vertical_addtocart">
	<tbody>
	<? foreach($arTheStockPlaybookProductType as $arRow){ ?>
		<tr>
			<td bgcolor="#000066"><span
				style="text-transform: uppercase; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold;"><?=strtoupper($arRow['product_type']);?><br>
			<a style="font-size: 24px; color: rgb(255, 255, 255);" href="#"><strong><?=$arRow['price_format'];?></strong></a>
			</th>
			<td bgcolor="#cccccc" width="116"><img
				id="trialshowbtn-<?=$arRow['subscription_def_id'];?>"
				src="<?=$IMG_SERVER;?>/images/tsp/add_to_cart.gif" alt="add to cart"
				border="0"
				onclick="checkcart('<?=$arRow['subscription_def_id'];?>','<?=$arRow['oc_id'];?>','<?=$arRow['orderItemType'];?>','<?=$arRow['google_analytics_product_name'];?>','<?=$arRow['product_type'];?>','<?=$arRow['google_analytics_action'];?>');"
				style="cursor: pointer;" /> <img
				id="trialhidebtn-<?=$arRow['subscription_def_id'];?>"
				src="<?=$IMG_SERVER;?>/images/tsp/add_to_cart.gif" alt="add to cart"
				border="0" style="display: none;" /></td>

		</tr>
		<? } ?>
	</tbody>
</table>
		<?
	}

	function displayleftColumnBottom($arPD){

		global $IMG_SERVER,$HTHOST,$HTPFXSSL;
		if($this->variation==1) { ?>
<div class="variation1-leftcolumn">

<div style="width: 100%">
<div class="trytsp"><strong>Try The Stock Playbook FREE for 14 Days!</strong></div>
<div class="tsp_cartbox_variation1"><? $this->displayaddtocart(); ?></div>
<div style="clear: both;"></div>
<div
	style="font-family: Arial; font-size: 21px; color: rgb(17, 59, 97); padding-top: 10px;"><strong>What
Kind of Investments does The Stock Playbook cover?</strong></div>
<br>
The Stock Playbook covers the entire stock market looking for companies
that are growing in revenue and earnings. Dave finds stocks with more
buying than selling that are trading above average volume. <br>
<br>
<br>
If large caps are in favor over small cap stocks, Dave will focus on
them. He'll use ETF's to establish a position in a lagging industry
until a leader emerges or options as an insurance policy against a long
position or a hedge. Companies usually experience their largest stock
price appreciation within their first eight years of coming public, so
Dave also focuses on IPO's and new issues to find winners. If the market
is in a downtrend, Dave will look for stocks to short to make some money
during the down time, while we await the next leg up, which always
comes, sometimes sooner than later. <br>
<br>
Dave invests in what is working at any given moment and when it ceases
to work, we move on to the next idea.
<div style="padding-top: 10px;"><img align="left"
	alt="StockPlaybookLogo"
	src="<?=$IMG_SERVER;?>/images/tsp/stockPlaybookLogo_01.jpg">
<div class="tsp_cartbox_variation1"><? $this->displayaddtocart(); ?></div>

</div>
<div style="clear: both;"></div>
<br />
Take your FREE trial to The Stock Playbook on Minyanville today. You
have nothing to lose and can cancel at any time during your trial if
you're not satisfied. <strong>Please contact us at 212-991-9357 or <a
	href="mailto:support@minyanville.com">support@minyanville.com</a> with
any questions.</strong><br />
<br />
<br />
<br />
</div>
</div>
		<? }else{ ?>
<div class="variation2-leftcolumn"><img
	src="<?=$IMG_SERVER;?>/images/tsp/charts01.jpg" />
<div>
<div class="tsp_highlight"
	style="width: 100%; text-align: center; padding-top: 20px;">All
Subscriptions Come With A FREE 14 DAY TRIAL!<br />
<b><font style="color: orange;">REGISTER NOW!</font></b></div>
<div style="padding-left: 70px;"><img align="left"
	alt="StockPlaybookLogo"
	src="<?=$IMG_SERVER;?>/images/tsp/stockPlaybookLogo_01.jpg">
<div class="tsp_cartbox_variation2"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/"
	onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
<img style="cursor: pointer;"
	src="<?=$IMG_SERVER?>/images/subscription/tspb_14.jpg" class="freeimg"
	alt=""> </a></div>
</div>
</div>
<div style="clear: both;"></div>
<div style="width: 100%; text-align: center;"><br />
You have nothing to lose and can cancel at any time during your trial if
you're not satisfied.<br />
<b>Please contact us at 212-991-9357 or <a
	href="mailto:support@minyanville.com">support@minyanville.com</a> with
any questions.<br />
<br />
<br />
</b></div>
</div>
<div style="clear: both;"></div>
		<? }
	}


	function theStockPlayBookHeader(){
		global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;
		$arPDM = getFreeTrailProductDetail('thestockplaybookmonthly','subscription'); // Normal Monthly
		$arPDM['product_name'] = 'subTSP';
		$arPDM['product_type'] = 'Monthly';
		$arPD['event_name'] = 'Landed';
		?>
<div class="header_ads"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDM['subscription_def_id']?>','<?=$arPDM['oc_id']?>','<?=$arPDM['orderItemType']?>','<?=$arPDM['product_name']?>','<?=$arPDM['product_type']?>','<?=$arPD['event_name']?>');"><img
	src="<?=$IMG_SERVER;?>/images/tspb/stockPlaybook_header_946x129.jpg" width="941"
	height="129" alt=""></a></div>
<div style="clear: both;"></div>
		<?
	}

	function theStockPlayBookLeftContant(){
		global $IMG_SERVER,$HTPFX,$HTHOST;
		?>
<div class="spb_lftarea">
<div id="expctarea">
<h1>What Can I Expect?</h1>
The Stock Playbook provides the individual investor with a daily
briefing of the general markets and daily stock market ideas, either
long or short depending on the overall trend of the market. Dave
Dispennette, CEO of the Stock Playbook has over 20 years experience
navigating the stock market. He has spent the past seven years managing
his own portfolio, as well as the portfolios of friends and family.</div>
<div class="clr"></div>
<div class="head_spbmembr">As a member of The Stock Playbook you will
get:</div>
<ul id="spb_member">
	<li>Daily "live" videos with Dave's stock picks and trading commentary</li>
	<li>Daily detailed chart analysis of Dave's favorite investment ideas</li>
	<li>Bonus videos detailing earnings plays, stocks under $10 and stocks
	making new highs</li>
	<li>Undiscovered gems just starting to hit the radars of professional
	money managers</li>
	<li>And Much More...</li>
</ul>
<div class="spb_portfolio">
<h1>Grow Your Portfolio With:</h1>
<div class="portfolio_subcategory"><img
	src="<?=$IMG_SERVER;?>/images/tspb/icon_trackableportfolios.jpg"
	width="49" height="51" align="left" />Trackable Portfolios with All
Positions</div>
<div class="portfolio_subcategory"><img
	src="<?=$IMG_SERVER;?>/images/tspb/icon_livevideo.jpg" width="49"
	height="45" align="left" />Daily LIVE Videos Telling You What Dave is
Buying &amp; Selling</div>
<div class="portfolio_subcategory"><img
	src="<?=$IMG_SERVER;?>/images/tspb/icon_instantemail.jpg" width="49"
	height="29" align="left" />Instant Email Notifications with portfolio
changes</div>
<div class="portfolio_subcategory"><img
	src="<?=$IMG_SERVER;?>/images/tspb/icon_proffexp.jpg" width="49"
	height="48" align="left" />Over 20 Years of Professional Experience</div>
<div class="portfolio_subcategory"><img
	src="<?=$IMG_SERVER;?>/images/tspb/icon_stoploss.jpg" width="49"
	height="45" align="left" />Stop-loss, Breakthrough, and Pull-back
Levels</div>
</div>
<!--Growth Portfolio container END -->
<div class="clr"></div>
<div class="testmhead">Testimonials</div>
<div class="clr"></div>
<div class="testmltxt">I've been following The Stock Playbook for almost
2 years now, and I'm constantly amazed by the solid stock picks it
recommends. Clearly, a lot of time and research goes into the
selections, and I base many of my trading decisions on the
recommendations made in The Stock Playbook.<br />
-Edgar Ayerbe, Raleigh, NC<br />
<br />
Thanks for adding stocks under $10 to your newsletter. I like to play
with the smaller priced stocks and you have given me a few gems...NSI
now NTRI,ICCA and my favorite IFO, which I bought for $8.50 and sold at
$20 in less than two months.<br />
-Mike Samuelson, Retired and loving life in Sedona, AZ</div>
</div>
<!--Left container END -->


		<?
	}

	function theStockPlayBookRightContant(){
		global $IMG_SERVER,$HTPFX,$HTHOST;
		?>
<div class="spb_rghtarea">
<div class="stckplayrturn">
<h1>The Stock Playbook<br />
Returns</h1>
<img src="<?=$IMG_SERVER;?>/images/tspb/spb_returns.jpg" width="266"
	height="215" /></div>
<div class="marktindex">
<h1>Historical Gains Compared To Market Indexes</h1>
<img src="<?=$IMG_SERVER;?>/images/tspb/spb_gain.jpg" width="246"
	height="215" /></div>
<div class="clr"></div>
<div class="membrshiplevel_head">The Stock Playbook offers 2 Membership
Levels</div>
<div class="spb_resultarea">
<div class="spbblank">&nbsp;</div>
<div class="spb_head">The Stock Playbook</div>
<div class="spbpremium_head">The Stock Playbook Premium</div>
<div class="clr"></div>
<div class="spb_options">
<div class="stckrslttext">The Stock Playbook's Nightly Video</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">"New Highs" Video</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Focus Lists Review Video</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Access to Membership Site including Monthly
Conference Calls, Specials, and Savings on courses</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Stocks Under $10 Video</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Earnings Playbook Video and Calendar</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Large Cap Focus List</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">IPO's and New Issues Focus List</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Shorts Focus List</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Stocks Under $10 Focus List</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">Growth Stocks Focus List</div>
<div class="corrctsign_lft"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
<div class="spb_options">
<div class="stckrslttext">The Stock Playbook's Proprietary Locker Room
for intra-day alerts giving you unparallelled trading ideas,
opportunities and different approaches coming directly from Dave
throughout the trading day.</div>
<div class="corrctsign_lft">&nbsp;</div>
<div class="corrctsign_rght"><img
	src="<?=$IMG_SERVER;?>/images/tspb/spb_correctsign.gif" width="17"
	height="18" /></div>
</div>
		<?
		$this->theStockPlayBookPriceArea();
		?></div>
<!--SPB Options Right area END --></div>
<!--Right container END -->

		<?
	}

	function theStockPlayBookPriceArea(){
		global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;
		$arPDM = getFreeTrailProductDetail('thestockplaybookmonthly','subscription'); // Normal Monthly
		$arPDM['product_name'] = 'subTSP';
		$arPDM['product_type'] = 'Monthly';

		$arPDQ = getFreeTrailProductDetail('thestockplaybookquart','subscription'); // Normal Quaterly
		$arPDQ['product_name'] = 'subTSP';
		$arPDQ['product_type'] = 'Quaterly';

		$arPDA = getFreeTrailProductDetail('TheStockPlaybookAnnual','subscription'); // Normal Annually
		$arPDA['product_name'] = 'subTSP';
		$arPDA['product_type'] = 'Annually';


		$arPDPM = getFreeTrailProductDetail('thestockplaybookpremiummonthly','subscription'); // Premium Monthly
		$arPDPM['product_name'] = 'subTSP-P';
		$arPDPM['product_type'] = 'Monthly';

		$arPDPQ = getFreeTrailProductDetail('thestockplaybookpremiumquart','subscription'); // Premium Quaterly
		$arPDPQ['product_name'] = 'subTSP-P';
		$arPDPQ['product_type'] = 'Quaterly';

		$arPDPA = getFreeTrailProductDetail('thestockplaybookpremiumannual','subscription'); // Premium Annually
		$arPDPA['product_name'] = 'subTSP-P';
		$arPDPA['product_type'] = 'Annually';

		?>
<div class="yescount"><img
	src="<?=$IMG_SERVER;?>/images/tspb/img_yescountme.jpg" width="230"
	height="65" align="left" /> <a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDM['subscription_def_id']?>','<?=$arPDM['oc_id']?>','<?=$arPDM['orderItemType']?>','<?=$arPDM['product_name']?>','<?=$arPDM['product_type']?>','<?=$arPD['event_name']?>');">
<img src="<?=$IMG_SERVER;?>/images/tspb/spb_14dayfreetrial.jpg"
	width="157" height="62" align="left" class="spb_freetrial" /> </a> <a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDPM['subscription_def_id']?>','<?=$arPDPM['oc_id']?>','<?=$arPDPM['orderItemType']?>','<?=$arPDPM['product_name']?>','<?=$arPDPM['product_type']?>','<?=$arPD['event_name']?>');">
<img src="<?=$IMG_SERVER;?>/images/tspb/spb_14dayfreetrial.jpg"
	width="157" height="62" align="left" class="spb_freetrial" /> </a></div>
<div class="freetrl_area"><img
	src="<?=$IMG_SERVER;?>/images/tspb/img_prcingfreetrial.jpg" width="230"
	height="96" />
<div class="pckgtim">
<div class="tim_grybox"><a
	herf="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDM['subscription_def_id']?>','<?=$arPDM['oc_id']?>','<?=$arPDM['orderItemType']?>','<?=$arPDM['product_name']?>','<?=$arPDM['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDM['price']?>
Monthly</a></div>

<div class="tim_grybox"><a
	herf="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDQ['subscription_def_id']?>','<?=$arPDQ['oc_id']?>','<?=$arPDQ['orderItemType']?>','<?=$arPDQ['product_name']?>','<?=$arPDQ['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDQ['price']?>
Quarterly</a></div>


<div class="tim_grybox"><a
	herf="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDA['subscription_def_id']?>','<?=$arPDA['oc_id']?>','<?=$arPDA['orderItemType']?>','<?=$arPDA['product_name']?>','<?=$arPDA['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDA['price']?>
Annually</a></div>
</div>


<div class="pckgtim">
<div class="tim_grybox"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDPM['subscription_def_id']?>','<?=$arPDPM['oc_id']?>','<?=$arPDPM['orderItemType']?>','<?=$arPDPM['product_name']?>','<?=$arPDPM['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDPM['price']?>
Monthly</a></div>

<div class="tim_grybox"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDPQ['subscription_def_id']?>','<?=$arPDPQ['oc_id']?>','<?=$arPDPQ['orderItemType']?>','<?=$arPDPQ['product_name']?>','<?=$arPDPQ['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDPQ['price']?>
Quarterly</a></div>

<div class="tim_grybox"><a
	href="<?=$HTPFXSSL.$HTHOST?>/subscription/register"
	onclick="return checkcart('<?=$arPDPA['subscription_def_id']?>','<?=$arPDPA['oc_id']?>','<?=$arPDPA['orderItemType']?>','<?=$arPDPA['product_name']?>','<?=$arPDPA['product_type']?>','<?=$arPD['event_name']?>');">$<?=$arPDPA['price']?>
Annually</a></div>
</div>
</div>
		<?
	}

}
?>