<?php
//global $IMG_SERVER;
class OuterTemplate
{

	public $pageName;

	function __construct($pageName)
	{
	$this->pageName = $pageName;
	}
	function displayFooter()
	{
		$this->refreshAds();
		switch($this->pageName)
		{
			case 'sslanding':
				$this->optionsmithFooter();
				break;
			case 'garyk_landing':
				$this->garyKfooter();
			break;
			case 'ai_landing':
				$this->aifooter();
			break;
			case 'buyhedge_post':
				if(!$_SESSION['buyhedge'])
				{
					$this->BHPfooter();
				}
				else
				{
					$this->defaultFooter();
				}
			break;
			case 'buyandhedge_landing':
				if($_SESSION['nameFirst'] && $_SESSION['buyhedge'])
				{
					$this->defaultFooter();
				}
			break;
			case 'subscription_product_registration':
				$this->displayFunnelFooter();
			break;
			case 'subscription_product_welcome':
			case 'cross_sell':
			case 'funnel_error':
				$this->blankFooter();
				break;
			case 'buyhedge_landing':
			case 'housingMarketReport_landing':
			case 'buzzbanter':
			case 'techstrat_landing':
			case 'thestockplaybook':
				break;
			case 'edu-home':
			case 'eduTrade':
			case 'eduAlert':
			case 'eduInvest':
			case 'eduVideo':
			case 'eduGlossary':
			case 'eduPerFinance':
			case 'eduSearchAlert':
				$this->eduSiteFooter();
				break;
			default:
				$this->defaultFooter();
				break;
		}
	}
	function refreshAds(){
		global $adsRendered;
		if(is_array($adsRendered)){
			echo '<script language="javascript">adBanners="'.implode(',', $adsRendered).'";</script>';
		}
	}

	function showFacebookMetaData($page_name,$pageMetaData)
	{

	global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
	$fb_image =  $IMG_SERVER."/images/mv_social_icon.jpg";
	$fb_sitename = "Minyanville";
	$fb_userid = "152555418092812";
	$fb_desc = mswordReplaceSpecialChars($pageMetaData['description']);
	$fb_url = $pageMetaData['url']==""?$_SERVER['SCRIPT_URI']:$HTPFX.$HTHOST.$pageMetaData['url'];
	$objCache = new Cache();
	$rsMetaType=$pageMetaData['fbmeta_type'];

	$fb_type = $rsMetaType['fbmeta_type'];
	$fb_title = isset($pageMetaData['content_title'])?$pageMetaData['content_title']:str_replace(" - Minyanville.com","",$pageMetaData['title']);
	$fb_title = htmlentities(mswordReplaceSpecialChars($fb_title));
	switch($fb_type)
	{
		case "author":
			if($_GET['bio'] && $_GET['bio']!=''){
				include_once($D_R."/admin/lib/_contributor_class.php");
				$objCont = new contributor();
				$contName = $objCont->getContributor($_GET['bio']);
				if($contName['bio_asset'] != '')
				{
					$fb_image =$HTPFX.$HTHOST.$contName['bio_asset'];
				}
			}
			$fb_title= $feedData->title;
		break;
		case "product":
			if(substr_count($page_name,"techstrat"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/techstrat_strip.gif";
			}
			elseif(substr_count($page_name,"buzzbanter"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/buzz&banter.jpg";
			}
			elseif(substr_count($page_name,"cooper"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/jeffcooper_dailymarketreport.png";
			}
			elseif(substr_count($page_name,"ss"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/optionsmith.png";
			}
			elseif(substr_count($page_name,"thestockplaybook"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/stockplaybook.png";
			}
			elseif(substr_count($page_name,"qp"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/flexfolio.png";
			}
			elseif(substr_count($page_name,"etf"))
			{
				$fb_image = $IMG_SERVER."/images/subscription/grailETF&equityinvestor.png";
			}

		break;
		case "buzz_article":
			$fb_type =  "article";
		break;
		case "article":
			if($page_name=="dailyfeed_article"){
				if($pageMetaData['mvPremiumImg']!='')
					$fb_image=$pageMetaData['mvPremiumImg'];
			}
			elseif($page_name=="article_template"){
				if($pageMetaData['articleImg']!='')
					$fb_image=$pageMetaData['articleImg'];
			}
		break;
		case "" :
			$fb_type = "other";
		break;

	}
	$purifier = new HTMLPurifier($config);
	$fb_url = recursive_array_replace($purifier,urldecode($fb_url));
?>
	<meta property="og:title" content="<?=$fb_title?>"/>
    <meta property="og:type" content="<?=$fb_type?>"/>
    <meta property="og:url" content="<?=$fb_url?>"/>
    <meta property="og:image" content="<?=$fb_image?>"/>
    <meta property="og:site_name" content="<?=$fb_sitename?>"/>
    <meta property="og:description" content="<?=$fb_desc?>"/>
    
    <meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@minyanville">
	<meta name="twitter:title" content="<?=$fb_title?>">
	<meta name="twitter:image" content="<?=$fb_image?>">
	<meta name="twitter:description" content="<?=$fb_desc?>">
<?
}


	function footerDesclaimer()
	{
		global $mobile_itemType,$mobile_itemID;
		?>
		<div class="footerText" ><a href="#">&copy;<?=date('Y');?> Minyanville Media, Inc. All Rights Reserved</a></div>
		<?
	}
	function optionsmithFooter()
	{
?>
<div id="footer_subscription">
<div id="option_footer_container">
        <p>
        Money back guarantee offer applies only to new OptionSmith subscribers. If you have previously had a subscription or trial to OptionSmith, your subscription will begin upon completion of registration and be subject to regular renewal terms. You may cancel the subscription as of your next renewal date but no refund will be granted.
        </p>
<p>
During your registration you will be asked to choose between a monthly or annual subscription. If you choose a monthly subscription you will pay $39.00 per month.  If the money back guarantee is applicable you may contact us within 14 days to receive a full refund for any reason. Your subscription will automatically renew each month unless you call to cancel before a new monthly charge occurs. You will be notified if the current monthly price changes at any time. If you choose an annual subscription, you will pay $349.00 for a year. If the money back guarantee is applicable you may contact us within 14 days to receive a full refund for any reason. Unless you call to cancel before the current subscription term ends, your subscription will automatically renew at the same rate each year.
</p>
<p>
Minyanville's OptionSmith portfolio is a model portfolio of stocks and options chosen by the authors in accordance with their stated investment strategy. Your actual results may differ from results reported for the model portfolio for many reasons, including, without limitation: (i) performance results for the model portfolio do not reflect actual trading commissions that you may incur; (ii) performance results for the model portfolio do not account for the impact, if any, of certain market factors, such as lack of liquidity, that may affect your results; (iii) the stocks chosen for the model portfolio may be volatile, and although the "purchase" or "sale" of a security in the model portfolio will not be effected in the model portfolio until confirmation that the email alert has been sent to all subscribers, delivery delays and other factors may cause the price you obtain to differ substantially from the price at the time the alert was sent; and (iv) the prices of stocks in the model portfolio at the point in time you begin subscribing to Minyanville's OptionSmith may be higher than such prices at the time such stocks were chosen for inclusion in the model portfolio. Past results are not necessarily indicative of future performance.
</p>
</div>
</div>
<?
	}
	function defaultFooter()
	{
global $HTPFXNSSL,$HTHOST,$HTPFX,$IMG_SERVER,$productUrl;
	if($_SESSION['SID'])
	{
	   $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
	}
	?>
		 <div id="footer">
             <div id="footer-container">
            <div id="footer_top">
                <div id="footer_more">
                <div style="float:left;">
                <div class="footer_header" style="width:145px;">More From Minyanville</div>
                <div style="width:315px;" class="footer_bar">&nbsp;</div>
                </div>

                <div class="footer_link_block" style="margin-right:20px;">
                <ul>
                <li><a href="<?=$HTPFX.$HTHOST ?>/business-news">Business News</a></li>
                <li><a href="<?=$HTPFX.$HTHOST ?>/trading-and-investing/">Trading and Investing</a></li>
                <li><a href="<?=$HTPFX.$HTHOST ?>/sectors/">Sectors</a></li>
                <li><a href="<?=$HTPFX.$HTHOST ?>/special-features">Special Features</a></li>
                <li><a href="<?=$HTPFX.$HTHOST ?>/mvpremium">MV PREMIUM</a></li>
				<li><a href="<?=$HTPFX.$HTHOST ?>/edu">MV Education Center</a></li>
                 <li><a href="<?=$HTPFX.$HTHOST?>/video/">Video</a></li>
		 <li>
		<?php
			echo '<a href="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=adsfree'.$param.'">Ad Free Minyanville</a>';
		?>
	    </li>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/book-store">Minyanville Book Store</a></li>
                </ul>
                </div>
                <div class="footer_link_block" style="margin-right:20px;">
                <ul>

            <li>
		<?php
			echo '<a href="http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=buzz'.$param.'">Buzz &amp; Banter</a>';

		?>
	    </li>
	    <li>
		<?php if($_SESSION['keene']){
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/keene/">Keene On Options</a>';
		}else{
			echo '<a href="http://mvp.minyanville.com/keene-landing-page-navbar/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=keene'.$param.'">Keene On Options</a>';
		} ?>
	    </li>
	    <li>
		<?php
		if($_SESSION['ElliottWave'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/ewi/">Elliott Wave Insider</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/ewi-landing-page-homepage-module/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=elliottwave'.$param.'">Elliott Wave Insider</a>';
		}
		?>
	     </li>
	     <li>
		<?php
		if($_SESSION['peterTchir'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/tchir-fixed-income/">Tchir&#39;s Fixed Income Report</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/peter-tchir-landing-page-homepage-module">Tchir&#39;s Fixed Income Report</a>';
		}
		?>
	     </li>
	     <li>
		<?php
		if($_SESSION['Cooper'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/cooper/">Cooper\'s Market Report</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-homepage-module/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=cooper'.$param.'">Cooper\'s Market Report</a>';
		}
		?>
	     </li>
	    <li>
		<?php
		if($_SESSION['TechStrat'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/techstrat/">TechStrat Report</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/techstrat-landing-page-homepage-module?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=techstrat'.$param.'">TechStrat Report</a>';
		}
		?>
	     </li>
	     <li>
		<?php
		if($_SESSION['Optionsmith'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/optionsmith/">OptionSmith</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/optionsmith-landing-page-homepage-module/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=optionsmith'.$param.'">OptionSmith</a>';
		}
		?>
	     </li>
	    <li>
		<?php
		if($_SESSION['TheStockPlayBook'])
		{
			echo '<a href="'.$HTPFXNSSL.$HTHOST.'/the-stock-playbook/">The Stock Playbook</a>';
		}
		else
		{
			echo '<a href="http://mvp.minyanville.com/stock-playbook-landing-page-homepage-module/?utm_source=Website Footer&utm_medium=Website&utm_content=Website Footer&utm_campaign=thestockplaybook'.$param.'">The Stock Playbook</a>';
		}
		?>
	     </li>



                 </ul>
                </div>

                <div class="footer_link_block">
                <ul>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/site-index">Sitemap</a></li>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/gazette/bios.htm">Contributor Bios</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/library/dictionary.htm">Directory of Terms</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/library/search.htm">Archive</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/subscription/register/controlPanel.htm">Email Alerts</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/service/rss.htm">RSS Feeds</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/subscription">Minyanville Subscriptions</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/shops/harrisons.htm">Minyanville Merchandise</a></li>
                 </ul>
                </div>
                </div>
                <div id="footer_company">
                <div style="float:left;">
                <div class="footer_header" style="width:60px;">Company</div>
                <div style="width:240px;" class="footer_bar">&nbsp;</div>
                </div>

                <div class="footer_link_block" style="margin-right:20px;">
                <ul>
                <li><a target="_blank" href="http://minyanland.com/">MinyanLand</a></li>
                <li><a target="_blank" href="http://www.minyanvillemedia.com/">Minyanville Media</a></li>
                <li><a target="_blank" href="http://www.buzzandbanter.com/">Buzz and Banter.com</a></li>
                <li><a target="_blank" href="http://www.rpfoundation.org/">Ruby Peck Foundation</a></li>
                <li><a target="_blank" href="http://minyanvillemedia.com/">About Us</a></li>
                <!-- <li><a target="_blank" href="<?=$HTPFXNSSL.$HTHOST;?>/ads/docs/MediaKit.pdf">Advertise</a></li> -->
                <li><a target="_blank" href="<?=$HTPFXNSSL.$HTHOST;?>/ads/">Advertise</a></li>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/contact.htm">Contact Us</a></li>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/help.htm">Help</a></li>
                </ul>
                </div>
                <div class="footer_link_block">
                <ul>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/privacy.htm">Privacy Policy</a></li>
                <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/substerms.htm">Terms and Conditions</a></li>
                 <li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/disclaimers.htm">Disclaimers</a></li>
                 </ul>
                </div>
                </div>
                <div id="footer_social">
                <a href="<?=$HTPFXNSSL.$HTHOST;?>"><img src="<?=$IMG_SERVER;?>/images/footer/mvFooter_logo.png" alt="" /></a>
                <ul>
                <li class="footer_bar"><div>&nbsp;</div></li>
                <li>
                    <ul class="footer_social_icons">
                    <li class="footer_social_img"><a target="_blank" href="http://www.facebook.com/MinyanvilleMedia"><img src="<?=$IMG_SERVER;?>/images/footer/footerFcbk_icon.png" alt="" /></a></li>
                    <li class="footer_social_text"><a target="_blank" href="http://www.facebook.com/MinyanvilleMedia">Follow Minyanville on Facebook</a></li>
                    </ul>

                </li>
                <li class="footer_bar"><div>&nbsp;</div></li>
                <li>
                    <ul class="footer_social_icons">
                    <li class="footer_social_img"><a href="https://twitter.com/intent/follow?original_referer=&region=follow_link&screen_name=minyanville&tw_p=followbutton&variant=2.0" target="_blank" id="follow-button" title="Follow @minyanville on Twitter"><img src="<?=$IMG_SERVER;?>/images/footer/footerTwtr_icon.png" alt="" /></a></li>
                    <li class="footer_social_text"><a href="https://twitter.com/intent/follow?original_referer=&region=follow_link&screen_name=minyanville&tw_p=followbutton&variant=2.0" target="_blank" id="follow-button" title="Follow @minyanville on Twitter">Follow minyanville on Twitter</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></li>
                    </ul>
                </li>
                <li class="footer_bar"><div>&nbsp;</div></li>
                <li>
                    <ul class="footer_social_icons">
                    <li class="footer_social_img"><a target="_blank" href="http://www.linkedin.com/company/minyanville-media-inc"><img src="<?=$IMG_SERVER;?>/images/footer/footerLinkedIn_icon.png" alt="" /></a></li>
                    <li class="footer_social_text"><a target="_blank" href="http://www.linkedin.com/company/minyanville-media-inc">Follow Minyanville on Linkedin</a></li>
                    </ul>
                </li>
				<li class="footer_bar"><div>&nbsp;</div></li>
				<li>
                    <ul class="footer_social_icons">
                    <li class="footer_social_img"><a target="_blank" href="<?=$HTPFXNSSL.$HTHOST ?>/service/rss.htm"><img src="<?=$IMG_SERVER;?>/images/footer/RSS_icon_24X24.png" alt="" /></a></li>
                    <li class="footer_social_text"><a target="_blank" href="<?=$HTPFXNSSL.$HTHOST ?>/service/rss.htm">Subscribe to Our RSS Feed</a></li>
                    </ul>
                </li>
                </ul>
                </div>
            </div>
            <div id="footer_partner">
            <div class="footer_header" style="width:60px">Partners</div>
            <div style="width:880px" class="footer_bar">&nbsp;</div>
            </div>
            <div>
            <ul class="footerImg footerImg_first">
            <li><a rel="nofollow" target="_blank" href="http://moneycentral.msn.com/home.asp"><img src="<?=$IMG_SERVER;?>/images/footer/msnMoney_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://finance.yahoo.com/"><img src="<?=$IMG_SERVER;?>/images/footer/yahooFinance_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.dailyfinance.com/"><img src="<?=$IMG_SERVER;?>/images/footer/dailyFinance_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.marketwatch.com/"><img src="<?=$IMG_SERVER;?>/images/footer/marketwatch_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" href="http://www.tdameritrade.com/"><img src="<?=$IMG_SERVER;?>/images/footer/tdAmeritrade_logo.gif" alt="" /></a></li>
            <li rel="nofollow" style="margin-right:0px;"><a target="_blank" href="http://www.etrade.com "><img src="<?=$IMG_SERVER;?>/images/footer/etrade_logo_logo.gif" alt="" /></a></li>
            </ul>
            <ul class="footerImg footerImg_sec">
            <li rel="nofollow" style="margin-left:11px;"><a target="_blank" href="http://www.benzinga.com/"><img src="<?=$IMG_SERVER;?>/images/footer/benzinga_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://t3live.com/"><img src="<?=$IMG_SERVER;?>/images/footer/t3Live_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.amny.com/"><img src="<?=$IMG_SERVER;?>/images/footer/amNy_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.ritholtz.com/blog/"><img src="<?=$IMG_SERVER;?>/images/footer/theBigPicture_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.moneyshow.com/?blogroll=minyanville"><img src="<?=$IMG_SERVER;?>/images/footer/moneyshow_logo.gif" alt="" /></a></li>
            <li><a rel="nofollow" target="_blank" href="http://www.oilprice.com/?blogroll=minyanville"><img src="<?=$IMG_SERVER;?>/images/footer/oilprice_logo.gif" alt="" /></a></li>
            </ul>
            </div>
            </div>
            <?=$this->footerDesclaimer();?>
        </div>
		<?
	}
	function displayMSN()
	{

		if($_GET['from'] == 'msn' || $_GET['from'] == 'MSN')
		{
		?>
 <div style="height:45px; border-bottom:3px solid #0066CC; background:#FFFFFF;">
		<div style="float:left;" class="msnHeader">
			<img border="0" src="http://logo.msn.com/as/shell/lg/pos/en-us_pos.gif" alt="MSN Logo" height="35" width="118" />
			<a href="http://moneycentral.msn.com/" style="font-size:22px;">Money</a>
		</div>
		<div style="float:right;padding-top:15px;;padding-right:2px;"  class="msnHeader">
			<a href="http://moneycentral.msn.com/">MSN Money Homepage</a>&nbsp;

			<select id="navsel">
					<option value="">More...</option>
					<option value="http://moneycentral.msn.com/investor/home.asp">MSN Money Investing</option>
					<option value="http://news.moneycentral.msn.com/newscenter/newscenter.aspx">MSN Money News Center</option>
					<option value="http://moneycentral.msn.com/personal-finance/default.aspx">MSN Money Personal Finance</option>
			</select>
			<input type="button" value="Go" onClick="navigate()" style="background:#ffffff url(http://stc.msn.com/br/gbl/css/6/decoration/search.gif) 0px 45%;border:1px solid #2a5597;color:#07519a;font-size:11px;width:22px;"/>

		</div>
 	</div>
		<?
		}
	}
	function autoRefresh()
	{
		$arrPages[]='articlelisting';
		$arrPages[]='automotive';
		$arrPages[]='biotech/pharma';
		$arrPages[]='bonds';
		$arrPages[]='business news';
		$arrPages[]='commodities';
		$arrPages[]='companies';
		$arrPages[]='credit/debt';
		$arrPages[]='currencies';
		$arrPages[]='dailyfeed';
		$arrPages[]='emerging markets';
		$arrPages[]='energy';
		$arrPages[]='etfs';
		$arrPages[]='financial planning';
		$arrPages[]='investing';
		$arrPages[]='macro outlook';
		$arrPages[]='markets';
		$arrPages[]='options';
		$arrPages[]='people';
		$arrPages[]='precious metals';
		$arrPages[]='products';
		$arrPages[]='real estate';
		$arrPages[]='products';
		$arrPages[]='retirement planning';
		$arrPages[]='savings';
		$arrPages[]='specialfeatures';
		$arrPages[]='special-features';
		$arrPages[]='sports business';
		$arrPages[]='stocks';
		$arrPages[]='stocks and markets';
		$arrPages[]='taxes';
		$arrPages[]='technology';
		$arrPages[]='wall of worry';
		$arrPages[]='buyhedgehome';


		global $pageName;

		//if($pageName=='home' || $pageName=='articlelisting' || $pageName=='markets' || $pageName=='investing' || $pageName=='special-features')
		if(in_array(strtolower($pageName),$arrPages))
		{
		?>
		<script language="javascript" >refreshTime=420000; RefreshPage(1,refreshTime);</script>
		<?
		}
		else if($pageName=='home')
		{
		?>
			<script language="javascript" >refreshTime=420000; RefreshPage(1,refreshTime);</script>
		<?
		}
		else if(strstr(strtolower($pageName),'article'))
		{
		?>
			<script language="javascript" >refreshTime=480000; RefreshPage(1,refreshTime);</script>
		<?
		}
		else if($pageName=='audiovideo' || $pageName=='t3live' || $pageName=='hoofyboo' || $pageName=='popbiz' || $pageName=='toddharrisontv' || $pageName=='specials') {

		// Page refresh time changed from 7 (420000) min to 5 (300000) min for article pages  on kelly's request on Nov  15, 2011
			if($_SESSION['AdsFree'])
			{
				?>
				<script language="javascript" >refreshTime=420000; RefreshPage(0,refreshTime);</script>
				<?
			}
			else
			{
				?>
				<script language="javascript" >refreshTime=420000; RefreshPage(1,refreshTime);</script>
				<?
			}
		}

	}
	function displayLoginDiv()
	{
		global $HTPFXSSL,$HTHOST,$HTPFX,$IMG_SERVER;
		?>
		<div id="header_no_nav">
        <div id="subscription_logo">
        <img src="<?=$IMG_SERVER;?>/images/subscription/mvp_mainHeader_409x58.png" alt="" />
        </div>
        <div id="header-no-nav-container" style="float:right;">
		<?  if($_SESSION['SID']) { ?>
			<div class="welcome-user-no-nav">
						<div class="welcomeName">Welcome <?= ucwords($_SESSION['nameFirst']); ?> &nbsp;| </div>
						<div class="header_log_out">
						<a id="<?=$_SESSION['user_id']?>" target="_self" href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/loginAjax.php?type=logout"> &nbsp;Logout &nbsp;</a> |
						</div>
						<div id="youraccount">
							 <a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/manage.htm">&nbsp;My Account</a>
                             <? if($_SESSION['Buzz']){ ?>
                             <a style="cursor:pointer;text-decoration:none;margin-left:5px;" onClick="window.open('<?=$HTPFX.$HTHOST?>/buzz/buzz.php',
  'Banter','width=350,height=708,resizable=yes,toolbar=no,scrollbars=no'); banterWindow.focus();"><span class="top_login_no_nav"><b>|&nbsp;&nbsp;Launch Buzz &amp; Banter</b></span></a>
                             <? }?>
						</div>
					</div>

			<? } else { ?>
				<div class="signin-register-no-nav">
					<span id="logIn" ><a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/login.htm" >Already a subscriber? <span class="top_login_no_nav">Login to access</span></a></span><br /><span class="green_text">Get started today by calling 212-991-9357</span>
				</div>
			<? } ?>

			</div>
            </div>
	<?
	}
	function footerSupportText()
	{
	?>
    	<div id="os_support">
	        <div id="os_desclaimer">
	        &copy;<?=date('Y');?> Minyanville Media, Inc. All Rights Reserved
	        </div>
	        <div id="os_questions">
	        <b>Questions or More Information</b><br />
	        For any inquiries, issues or cancellation please email <a class="green_text_bold" href="mailto:support@minyanville.com">support@minyanville.com</a> or call <span class="green_text_bold">212-991-9357</span>
	        </div>
	        <div id="os_customer_support">
	        <b>Customer Support</b><br />
	        <span class="green_text_bold">Monday - Friday:</span>
	        <span>9:00AM - 6:00PM EST</span><br />
	        <span>We normally respond to emails the same business day. If you need immediate assistance please call 212-991-9357 </span>
	        </span></span>
	        </div>
        </div>
    <?
	}

function footerBuyHedgerSupportText()
	{
	?>
    	<div id="os_support" style="margin: 40px 0 0 17px;width: 810px">
	        <div id="os_desclaimer" style="width: 246px;">
	        &copy;<?=date('Y');?> Minyanville Media, Inc. All Rights Reserved
	        </div>
	        <div id="os_questions" style=" margin-right: 20px;width: 246px;">
	        <b>Questions or More Information</b><br />
	        For any inquiries, issues or cancellation please email <a class="green_text_bold" href="mailto:support@minyanville.com">support@minyanville.com</a> or call <span class="green_text_bold">212-991-9357</span>
	        </div>
	        <div id="os_customer_support" style="margin: 0 0 0 20px;width: 248px;">
	        <b>Customer Support</b><br />
	        <span class="green_text_bold">Monday - Friday:</span>
	        <span>9:00AM - 6:00PM EST</span><br />
	        <span>We normally respond to emails the same business day. If you need immediate assistance please call 212-991-9357 </span>
	        </span></span>
	        </div>
        </div>
    <?
	}

function footerBuyAndHedgerSupportText()
	{
	?>
    	<div id="os_support" style="margin: 40px 0 0 17px;width: 810px">
	        <div id="os_desclaimer" style="width: 246px;">
	        &copy;<?=date('Y');?> Minyanville Media, Inc. All Rights Reserved
	        </div>
	        <div id="os_questions" style=" margin-right: 20px;width: 246px;">
	        <b>Questions or More Information</b><br />
	        For any inquiries, issues or cancellation please email <a class="green_text_bold" href="mailto:support@minyanville.com">support@minyanville.com</a> or call <span class="green_text_bold">212-991-9357</span>
	        </div>
	        <div id="os_customer_support" style="margin: 0 0 0 20px;width: 248px;">
	        <b>Customer Support</b><br />
	        <span class="green_text_bold">Monday - Friday:</span>
	        <span>9:00AM - 6:00PM EST</span><br />
	        <span>We normally respond to emails the same business day. If you need immediate assistance please call 212-991-9357 </span>
	        </span></span>
	        </div>
        </div>
    <?
	}

	function footerNoNav(){
	?>
		<!--Minyanville Footer Starts-->
		<div id="footer-container-nonav">
		<div id="footer-nonav">
				<ul id="footer-links-nonav">
						<li><a target="_blank" href="http://minyanvillemedia.com/">About Us</a></li>
						<li><a target="_blank" href="<?=$HTPFXNSSL.$HTHOST;?>/ads/">Advertise</a></li>
						<li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/contact.htm">Contact Us</a></li>
						<li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/help.htm">Help</a></li>
						<li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/privacy.htm">Privacy Policy</a></li>
						<li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/substerms.htm">Subscription and Terms of Use</a></li>
						<li><a href="<?=$HTPFXNSSL.$HTHOST ?>/company/disclaimers.htm">Disclaimers</a></li>
						 <li><a href='<?=$HTPFXNSSL.$HTHOST ?>/site-index/' class='footer_links'>Site Map</a></li>
						  <li><a href='<?=$HTPFXNSSL.$HTHOST ?>/service/rss.htm' class='footer_links'>RSS</a></li>
				</ul>
		</div>
		</div> <!--outer contaner end here-->
	<?
	}

	function garyKfooter()
	{
?>
<div id="footer_subscription">
<div id="option_footer_container">
        <p>
        Money back guarantee offer applies only to new Equity Trading Setups subscribers. If you have previously had a subscription or trial to Equity Trading Setups, your subscription will begin upon completion of registration and be subject to regular renewal terms. You may cancel the subscription as of your next renewal date but no refund will be granted.
        </p>
<p>
During your registration you will be asked to choose between a monthly, quarterly or annual subscription. If you choose a monthly subscription you will pay $129.00 per month, if you choose a quarterly subscription you will pay $339.00 for a three month term, if you choose an annual subscription you will pay $999.00 for a year.  If the money back guarantee is applicable you may contact us within 14 days to receive a full refund for any reason. Your subscription will automatically renew at the conclusion of the chosen billing cycle unless you call to cancel before the next billing date occurs. You will be notified if the current price changes at any time.
</p>
<p>
Gary Kaltbaum owns Kaltbaum Capitlal Management, LLC("KCM") an investment adviser registered with the U.S. Securities and Exchange Commission. The opinions expressed within Equity Trading Setups are those of Mr. Kaltbaum and may not reflect those of Minyanville Media, Inc. The information offered in this publication is general information that does not take into account the individual circumstances, financial situations or individual needs of an investor. The information herein has been obtained from sources belived to be reliable, but we cannot assure its accuracy or completeness. Neither the information nor any opinion expressed constitutes a solicitation for the purchanse or sale of any security. Any reference to past performance is not to be implied or construed as a guarantee of future results.
</p>
</div>
</div>
<?
	}


function TSPfooter()
	{
		?>
		<div class="tsp_footer_container">&nbsp;</div>
<?php
	}

function  BHPfooter()
{
?>
	<div id="footer_subscription">
	<div id="bhp_footer_container">
	        *This offer applies to only annual subscriptions to Buy & Hedge ETF strategies and is subject
	        to change any time. Your free book wil be shipped within 4-5 weeks after your paid subscription
	        begins.<br>
			To receive the Free book your subscription must be fully paid and active for at least 30 days.
			Void where prohibited.
	</div>
	</div>
<?
}

function AIfooter()
	{
?>
<div id="footer_subscription">
<div id="ai_footer_container">
        Free trial offer applies only to new Active
Investors subscribers. If you choose a monthly subscription, you will
pay $59.00 per month following your free trial, if a trial is applicable
to you, unless you contact us to cancel prior to the end of the 14 day
trial. Your subscription will automatically renew each month unless you
call to cancel before a new monthly charge occurs. You will be notified
if the current monthly price changes at any time. If you choose an
annual subscription, you will pay $499.00 for a year unless you call us
to cancel within your 14-day free-trial period. Unless you call to
cancel before the current subscription term ends, your subscription will
automatically renew at the same rate each year.<br>
<br>
Minyanville's Active Investor contains the authors' own opinions, and
none of the information contained therein constitutes a recommendation
that any particular security, portfolio of securities, transaction, or
investment strategy is suitable for any specific person. You further
understand that Messr. Theal and Woo will not advise you personally
concerning the nature, potential, value or suitability of any particular
security, portfolio of securities, transaction, investment strategy or
other matter. To the extent any of the information contained in
Minyanville's Active Investor may be deemed to be investment advice,
such information is impersonal and not tailored to the investment needs
of any specific person.<br>
<br>
MInyanville's Active Investor portfolio is a model portfolio of stocks
chosen by the authors in accordance with their stated investment
strategy. Your actual results may differ from results reported for the
model portfolio for many reasons, including, without limitation: (i)
performance results for the model portfolio do not reflect actual
trading commissions that you may incur; (ii) performance results for the
model portfolio do not account for the impact, if any, of certain market
factors, such as lack of liquidity, that may affect your results; (iii)
the stocks chosen for the model portfolio may be volatile, and although
the "purchase" or "sale" of a security in the model portfolio will not
be effected in the model portfolio until confirmation that the email
alert has been sent to all subscribers, delivery delays and other
factors may cause the price you obtain to differ substantially from the
price at the time the alert was sent; and (iv) the prices of stocks in
the model portfolio at the point in time you begin subscribing to
Minyanville's Active Investor may be higher than such prices at the time
such stocks were chosen for inclusion in the model portfolio. Past
results are not necessarily indicative of future performance.
</div>
</div>
<?
}
	function displayFunnelFooter()
	{
		global $IMG_SERVER,$HTPFX,$HTHOST; ?>
		<div class="funnel_footer">
			<div class="funnel_footer_logo"><img src="<?=$IMG_SERVER;?>/images/buzzad/buzz&banterlogo_mvil.png" alt="" /></div>
			<div class="funnel_footer_info">
				<div class="funnel_terms"><a href="<?=$HTPFX.$HTHOST?>/company/substerms.htm">Terms of Use</a>&nbsp;&nbsp;<span class="funnel_policy"><a href="<?=$HTPFX.$HTHOST?>/company/privacy.htm">Privacy Policy</a></span></div>
				<br><div class="funnel_address">Minyanville Media, Inc. - 708 Third Avenue, 6th Floor, New York, NY 10017 - 212.991.6200 - www.minyanville.com</div>
			</div>
		</div>
	<? }

	function displayFunnelHeader()
	{
		global $IMG_SERVER;
		?>
		<div class="funnel_header">
        	<div class="subscription_funnel_logo">
        		<img src="<?=$IMG_SERVER;?>/images/subscription/mvp_mainHeader_409x58.png" alt="" />
        	</div>
			<div class="funnel_header_info">
				Get Started Now! Call (212) 991-9357 or email <a href="mailto:support@minyanville.com">support@minyanville.com</a>
			</div>
		</div>
	<?
	}

	function blankFooter(){
	}
	
	function eduSiteFooter(){
		global $HTPFX,$HTHOST,$IMG_SERVER,$objEduData;
		$getMenu = $objEduData->getAllEduCategory(); 
		$countMenuItem = count($getMenu); ?>
		<div class="eduFooterContainer">
			<div class="eduFooterWraper">
				<div class="eduFooterNav">
					<ul>
						<?php  foreach ($getMenu as $key=>$menu){ 
							if ($key==$countMenuItem-1){ ?>
				        		<li><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias']?>"><?php echo $menu['menu_name'];?></a></li>
			        		<?php  } elseif (strtolower($menu['menu_name'])=="buzz & banter"){
		        						if(empty($_SESSION['Buzz'])){ ?>
											<li><a href="http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=buzz" target="_blank"><?php echo $menu['menu_name'];?></a></li>
									<?php } elseif(!empty($_SESSION['Buzz'])){
											$buzzLaunchUrl=buzzAppUrlEncryption(); ?>
											<li><a href="javascript:;" onclick="window.open('<?php echo $buzzLaunchUrl;?>','Banter','width=455,height=708,resizable=yes,toolbar=no,scrollbars=no'); "><?php echo $menu['menu_name'];?></a></li>
									<?php } ?>
									<li><img src="<?php echo $IMG_SERVER;?>/images/education/footer-li-line.png"></li>
						 <?php } elseif (strtolower($menu['menu_name'])=="products"){ 
			        			$academyUrl='http://www.tradingacademy.net'; ?>
			        			<li>  <a class="productFancy" href="" onclick="javascript:displayLeavingWindow('<?php  echo $academyUrl ?>');"><?php echo $menu['menu_name'];?></a></li>
			        			<li><img src="<?php echo $IMG_SERVER;?>/images/education/footer-li-line.png"></li>
			 				<?php  }else { ?>
			        			<li><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias']?>"><?php echo $menu['menu_name'];?></a></li>
			        			<li><img src="<?php echo $IMG_SERVER;?>/images/education/footer-li-line.png"></li>
			        		<?php } 
			        	} ?>
					</ul>
				</div>

				<div class="eduFooterLogo">
					<div class="eduFooterImg" ><img src="<?php echo $IMG_SERVER;?>/images/education/footer_logo.png" title="logo"></div>
					<div class="eduCopyRight">&copy;<?php echo date('Y');?> Minyanville Media, Inc.<br>
All Rights Reserved</div>
			</div>
		</div>
	</div>
<?php }
}
?>