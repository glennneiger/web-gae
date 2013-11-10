<?php
class subRedesign{
	public function displaySubPage(){
		global $IMG_SERVER,$objSubViewer,$lang,$HTPFX,$HTHOST; ?>
		<div class="subRedesignLogo"><img src="<?=$IMG_SERVER;?>/images/subscription/mvp_mainHeader_454x70.png" /></div>
		<div class="subRedesignProductLogosBox">
			<div id="ca-container" class="ca-container">
				<div class="ca-wrapper">
					<div class="ca-item ca-item-9 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('keene');?>
						</div>
					</div>
					<div class="ca-item ca-item-1 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('elliottWave');?>
						</div>
					</div>
					<div class="ca-item ca-item-2 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('peter-tchir');?>
						</div>
					</div>
					<div class="ca-item ca-item-3 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('buzzbanter');?>
						</div>
					</div>
					<div class="ca-item ca-item-4 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('cooperhome');?>
						</div>
					</div>
					<div class="ca-item ca-item-5 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('sshome');?>
						</div>
					</div>
					<div class="ca-item ca-item-6 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('techStrat');?>
						</div>
					</div>
					<div class="ca-item ca-item-7 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('thestockplaybook');?>
						</div>
					</div>
					<div class="ca-item ca-item-8 fade">
						<div class="ca-item-main">
							<?=$this->learnMoreSubRedesignBanner('adfree');?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/jquery.easing.1.3.js"></script>
		<!-- the jScrollPane script -->
		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/jquery.mousewheel.js"></script>
		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/jquery.contentcarousel.js"></script>
		<script type="text/javascript">
			$('#ca-container').contentcarousel();
		</script>

		<div class="subRedesignDiscountMain">
			<div class="subRedesignDiscount">
				<div class="subRedesignDiscountText"><p>Call us today to see if you qualify for a discount on one or more of our Premium Subscription Products!</p></div>
			</div>

		</div>

		<div class="subRedesignProductDescMain">
			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subKeeneTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>By identifying unusual options activity, Andrew Keene's trading insights minimize investor risk.<br/><br/>Gain an edge, whether the market is moving up, down, or sideways.</p></div>
				<div class="subRedesignProductDescBottom"> <?=$objSubViewer->learnMoreSubscription('keene');?></div>
			</div>
			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subWaveTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>Stay on top of the most important financial, economic & social trends and themes unfolding globally.<br/><br/>Daily market insights from the global leader in Elliott wave analysis since 1979.</p></div>
				<div class="subRedesignProductDescBottom"> <?=$objSubViewer->learnMoreSubscription('elliottWave');?></div>
			</div>

			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/mpv_pT_hdr.png" /></div>
				<div class="subRedesignProductDescMid"><p>Fixed income investing strategy from bond market master Peter Tchir.<br/><br/>Harness low-cost ETFs and winning market commentary to generate income and capital gains.</p></div>
				<div class="subRedesignProductDescBottom"> <?=$objSubViewer->learnMoreSubscription('peter-tchir');?></div>
			</div>
		</div>

		<div class="subRedesignProductDescMain">
			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subBuzzTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>30 professional traders share their trades, ideas & analysis in real-time.<br/><br/>Equities, fixed income, ETF, commodities commentary & more straight to your desktop.</p></div>
				<div class="subRedesignProductDescBottom"> <?=$objSubViewer->learnMoreSubscription('buzzbanter');?></div>
			</div>


			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subJeffTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>Day and swing trading setups each day featuring entries, targets and stops from the creator of the Hit &amp; Run trading strategy.<br/><br/>
			Market commentary and strategy with technical analysis charts daily.</p></div>
				<div class="subRedesignProductDescBottom"><?=$objSubViewer->learnMoreSubscription('cooperhome');?></div>
			</div>
			
		     <div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subOptionTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>Access veteran options trader Steve Smith's options portfolio and get email alerts with every trade.<br/><br/>Strategy with every trade and regular portfolio reviews. Portfolio is +104% since inception and +28% in 2011.</p></div>
				<div class="subRedesignProductDescBottom"><?=$objSubViewer->learnMoreSubscription('sshome');?></div>
			</div>
		</div>
       <div class="subRedesignProductDescMain" >
      		<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subTechTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>Sean Udall finds technology stocks poised for big moves and provides frequent tech commentary.<br/><br/>
				Trade strategies, tech research library, exclusive Q&amp;A with Sean.</p></div>
				<div class="subRedesignProductDescBottom"><?=$objSubViewer->learnMoreSubscription('techStrat');?></div>
			</div>

			<div class="subRedesignProductDesc">
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subStockTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>Daily videos detailing Dave's market view, portfolio strategy, earnings plays &amp; stocks under $10.<br/><br/>Access to Dave's portfolio that has averaged +41% annually since 2004.</p></div>
				<div class="subRedesignProductDescBottom"><?=$objSubViewer->learnMoreSubscription('thestockplaybook');?></div>
			</div>
			
			<div class="subRedesignProductDesc" >
				<div class="subRedesignProductDescTop"><img src="<?=$IMG_SERVER;?>/images/subscription/subAdsFreeTop.png" /></div>
				<div class="subRedesignProductDescMid"><p>For less than $2 a week, Minyanville will give you a site that is <u>100% ad  free</u>.<br/><br/>
No pop-ups, pre-rolls, drop-downs, big boxes, interstitials, roll-overs, text-links or banners. </p></div>
				<div class="subRedesignProductDescBottom"><?=$objSubViewer->learnMoreSubscription('adfree');?></div>
			</div>
		</div>


		<div class="subRedesignFooter">
			<p><span class="green">Customer Support:</span><br/>
			If you have a question or would like more information about any of our products and services, you may email us at <span class="orange"><a href="mailto:support@minyanville.com">support@minyanville.com</a></span> or <span class="orange">call us at 212-991-9357.</span><br/>We are available <strong>Monday - Friday, 9:00am to 6:00pm EST</strong>. Typically we respond to emails and calls the same business day.<br/><span class="orange">&copy;<?=date('Y')?> Minyanville Media, Inc. All Rights Reserved.</span></p>
		</div>
	<? }

function learnMoreSubRedesignBanner($p){
	global $HTPFXSSL,$HTPFX,$HTHOST;
	$str = '';

	$param="";
	if($_SESSION['SID']){
		$param = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
	}

	switch ($p){
		case 'peter-tchir' :
			$url  = getpageurl('peter-tchir');
			if($_SESSION['peterTchir']){
					  $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			 }else{
			  $str .='<a href="
http://mvp.minyanville.com/peter-tchir-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=peterTchir'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			 }
		break;

		case 'buzzbanter' :
			$url  = getpageurl('buzzbanter');
			$str .='<a href="http://mvp.minyanville.com/buzz-banter-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=buzz'.$param.'" target="_self"><div class="ca-icon"></div></a>';
		break;

		case 'cooperhome':
			$url  = getpageurl('cooperhome');
			if($_SESSION['Cooper']){
			  $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			}else{
			  $str .='<a href="http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=cooper'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			}
		break;

		case 'sshome':
			$url  = getpageurl('sshome');
			if($_SESSION['Optionsmith']){
				$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			}else{
				$str .='<a href="http://mvp.minyanville.com/optionsmith-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=optionsmith'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			}
		break;

		case 'thestockplaybook':
			$url  = getpageurl('thestockplaybook');
			if($_SESSION['TheStockPlayBook']){
				$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			}else{
				$str .='<a href="http://mvp.minyanville.com/stock-playbook-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=thestockplaybook'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			}
		break;

		case 'adfree':
			$url  = getpageurl('adfree');
			$str .='<a href="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=adsfree'.$param.'" target="_self"><div class="ca-icon"></div></a>';
		break;

		case 'techStrat':
			$url  = getpageurl('techstrat_landing');
			if($_SESSION['TechStrat']){
				$str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			}else{
				$str .='<a href="http://mvp.minyanville.com/techstrat-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=techstrat'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			}
		break;
		case 'elliottWave' :
			$url  = getpageurl('elliott-wave');
			if($_SESSION['ElliottWave']){
					  $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			 }else{
			  $str .='<a href="
http://mvp.minyanville.com/ewi-landing-page-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=elliottwave'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			 }
		break;
		case 'keene' :
			$url  = getpageurl('keene');
			if($_SESSION['keeen']){
					  $str .='<a href="'.$HTPFX.$HTHOST.$url['alias'].'" target="_self"><div class="ca-icon"></div></a>';
			 }else{
			  $str .='<a href="
http://mvp.minyanville.com/keene-landing-page-navbar/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=keene'.$param.'" target="_self"><div class="ca-icon"></div></a>';
			 }
		break;

	}
	return $str;
	}
}
?>