<?php

class homeDesign{

	public function homeNewsletter(){
	global $IMG_SERVER, $HTPFX,$HTHOST;
	$email=$_SESSION['email'];
	if($email=="")
	{
		$email="Enter email";
	}
	$userid = $_SESSION['SID'];
		?>
	<div id="newsletter-main-section">
	<div id="newsletter-div">
		<div id="newsletter-div1">
		<?
		if($_SESSION['AdsFree']!="1")
		{
			echo '<div id="Ad_234x20" >';
			CM8_ShowAd("Ad_234x20");
			echo '</div>';
		}
		?>
		</div>
	</div>
</div>
   <?php
	}

	public function homeAllArticles(){
		
	global $IMG_SERVER, $HTPFX,$HTHOST,$articleListingHomePageSize;
	$obArtilceCache=new ArticleCache();
	$arLatestArticles = $obArtilceCache->getFreeAndPremiumArticles();
	print '<div class="hmArticleListing">
			<ul id="all-articles-home">
		<div  class="allArticlesImg"><a	href="'.$HTPFX.$HTHOST.'/articles/articlelisting.htm"><h3 class="new-head">ALL ARTICLES</h3></a></div>';

		print '<div style="clear: both;"></div>';
		foreach($arLatestArticles as $index => $arArticle)
		{
			if($index >= $articleListingHomePageSize) // Show only required number of records
			{
				break;
			}
			$mvp_logo = "";
			$url = $arArticle['url'];
			if($arArticle['item_type'] != "1")
			{
				$url .= '?camp=articlepremiumcontent&medium=home&from=minyanville';
				$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
			}
			echo "<li id='all-articles-bullet' ><a id='all-articles-text' href='".$HTPFX.$HTHOST.$url."'>".$arArticle['title']."</a>".$mvp_logo."<br />
<a id='all-articles-contrib' href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arArticle['author_id']."'>".$arArticle['author']."</a></li>";
		}
		//print getArticleLanding(0, $articleListingHomePageSize);
		print '</ul>
		<div>
		<span  id="all-articles-more">
		<a href="'.$HTPFX.$HTHOST.'/articles/articlelisting.htm">More</a>
		<span id="raquo-right" >&raquo;</span>
		</span></div></div>';
	 }

	public function homeRecentTweets(){ ?>
		<div class="homeTwitterWidget">
	<? showTwitterWidget(); ?>
	</div>
	<? }
	
	public function freeModuleFancyBox()
	{
		global $IMG_SERVER; ?>
		<div><a id="freeModulePopUpWindow" href="#freeModule"></a></div>
		<div style="display:none;">
			<div id="freeModule" class="freeModulePopUp">
				<div class="freeModuleClosebttn">
					<img style="margin:-15px 0px 0px -5px;" onClick="closeFreeReportFancyBox();" align="right" src="<?php echo $IMG_SERVER;?>/images/fancybox/bnb_closeBtn.png" alt="" />
				</div>
				<div class="freeModulePopUpText">Thank you very much;<br/> you're only a step away from<br/> downloading your reports.</div>
				<div class="freeModuleLink">
					<div class="freeModuleLinkBox">You will receive a download link right in your email<br/> inbox for each of the free reports that you choose.</div>
				</div>
			</div>
		</div>
	<?php 
	}
	
	public function homeProducts(){
	global $IMG_SERVER, $HTPFX,$HTHOST,$HTPFXNSSL,$productUrl,$CDN_SERVER;
	if($_SESSION['SID'])
	{
	   $param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
	}
	$str='<div class="product-spacer-parent">
	<div id="products-div">
	<div style="clear:both;">
		<div id="products-heading">
			<a href="'.$HTPFX.$HTHOST.'/subscription/"><h3>SUBSCRIPTION PRODUCTS</h3></a>
		</div>
	</div>

	<div style="clear:both;">
			<div class="product_slide">
				<div id="cbp-fwslider" class="cbp-fwslider">
					<ul>
						<li>';
						if($_SESSION['Buzz']=="1"){
							$buzzLaunchUrl=buzzAppUrlEncryption();
							$str.='<a href="javascript:;" onclick="window.open(\''.$buzzLaunchUrl.'\',\'Banter\',\'width=455,height=708,resizable=yes,toolbar=no,scrollbars=no\'); banterWindow.focus();">';
						}else{
							$str.='<a href="'.$productUrl['Homepage_Subscription_Buzz'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/1.jpg" alt="img01"/> <!-- Frame A -->
									<div class="figcaption"><strong>Buzz &amp; Banter</strong><br />Todd Harrison and 30 top traders share their ideas and insights in real-time throughout the trading day.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->';
						if($_SESSION['keene']=="1"){
							$url  = getpageurl('keene');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						}else{
							$str.='<a href="'.$productUrl['Homepage_Subscription_Keene'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/keeneHome.jpg" alt="img02"/> <!-- Frame A -->
									<div class="figcaption"><strong>Keene On Options</strong><br />Andrew Keene, one of the CBOE\'s most recognized faces in the media, delivers his daily options trading ideas.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->';
						if($_SESSION['Cooper']=="1") {
							$url  = getpageurl('cooperhome');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="'.$productUrl['Homepage_Subscription_Cooper'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/2.jpg" alt="img02"/> <!-- Frame A -->
									<div class="figcaption"><strong>Jeff Cooper\'s Report</strong><br />Get day and swing trading setups from the creator of the Hit &amp; Run trading strategy. Also receive Jeff\'s outlook daily.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->
						</li><!-- ADS GROUP END-->
						<li>';
						if($_SESSION['ElliottWave']=="1") {
							$url  = getpageurl('elliott-wave');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="'.$productUrl['Homepage_Subscription_ElliottWave'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/4.jpg" alt="img04"/> <!-- Frame A -->
									<div class="figcaption"><strong>Elliott Wave Insider</strong><br />The Insider brings you a daily pick from the desks of 20+ market veterans and social mood experts at Elliott Wave International.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->';
						if($_SESSION['peterTchir']=="1") {
							$url  = getpageurl('peter-tchir');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="http://mvp.minyanville.com/peter-tchir-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=peterTchir'.$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/3.jpg" alt="img03"/> <!-- Frame A -->
									<div class="figcaption"><strong>Tchir\'s Fixed Income</strong><br />Peter Tchir gives you the inside scoop on the bond market, along with a complete fixed income investment plan.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->';
						if($_SESSION['TechStrat']=="1") {
							$url  = getpageurl('techstrat_landing');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="'.$productUrl['Homepage_Subscription_TechStrat'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/5.jpg" alt="img05"/> <!-- Frame A -->
									<div class="figcaption"><strong>TechStrat</strong><br />Get daily insights &amp; ideas from tech expert Sean Udall, so you can jump on tomorrow\'s winning stocks today!</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->
						</li><!-- ADS GROUP END-->
						<li>';
						if($_SESSION['Optionsmith']=="1") {
							$url  = getpageurl('sshome');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="'.$productUrl['Homepage_Subscription_OptionSmith'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/6.jpg" alt="img06"/> <!-- Frame A -->
									<div class="figcaption"><strong>OptionSmith</strong><br />Get access to Steve Smith\'s  trading ideas and OptionSmith portfolio, backed up by decades of real-world options experience.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->';
						if($_SESSION['TheStockPlayBook']=="1") {
							$url  = getpageurl('thestockplaybook');
							$str.='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self">';
						} else {
							$str.='<a href="'.$productUrl['Homepage_Subscription_Stock_Playbook'].$param.'">';
						}
						$str.='<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/7.jpg" alt="img07"/> <!-- Frame A -->
									<div class="figcaption"><strong>The Stock Playbook</strong><br />Get daily videos detailing Dave Dispennette\'s market view, portfolio strategy, earnings plays &amp; stocks under $10.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->
							<a href="'.$productUrl['Homepage_Subscription_Add_Free'].$param.'">
							<div class="figure cap-bot">
									<img src="'.$IMG_SERVER.'/images/home_redesign/8.jpg" alt="img08"/> <!-- Frame A -->
									<div class="figcaption"><strong>Ad Free Minyanville</strong><br />For a very low monthly fee of $8 you can view the web the way you want to reclaim valuable screen space.</div> <!-- Frame B -->
								</div>
							</a> <!-- AD END-->
						</li><!-- ADS GROUP END-->
					</ul>
				</div>
			<script src="'.$CDN_SERVER.'/js/jquery.cbpFWSlider.min.js"></script>
			<script>
				 jQuery.noConflict();
                 jQuery( function() {
                      jQuery( "#cbp-fwslider" ).cbpFWSlider();

                 } );
			</script>
	</div>
	</div><div class="partner_center"><center>';
    print $str;
    CM8_ShowAd("button_partnerCenter");
    $str='</center></div></div>
	</div>';
	print $str;
	}



	public function homeBloggerPosts(){
	global $IMG_SERVER, $HTPFX,$HTHOST,$D_R;/*<img src="'.$IMG_SERVER.'/images/redesign/mv_homePage_bloggercommunity.gif"
		alt="Bloggers Community" class="bloggerImage">*/
	include_once("$D_R/lib/_layout_design_lib.php");
	print '<div class="bloggerContainer"><ul id="all-articles-home">
	<div class="bloggerContainerHead"><a href="http://blogs.minyanville.com/"><h3 class="new-head">BLOGGER COMMUNITY</h3></a></div>
            ';
              print getBlogPostLayout();

     print    '</ul>
            <span id="all-articles-more" ><a href="http://blogs.minyanville.com/">More</a> <span id="raquo-right" >&raquo;</span></span> </div>';


	}

  function homeSponsoredLinks(){
	  $str.='<div class="article-right-module">';
	  $str.='<script language="javascript">CM8ShowAd("Sponsored_300x250");</script>';
	  $str.='</div>';
	  echo $str;
  }


}

################## HOME PAGE DESIGN CLASS ####################

?>