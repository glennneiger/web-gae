<?php
class eduDesign{
	function displayEduHeader($sectionName){
		global $HTPFX,$HTHOST,$IMG_SERVER,$pageName,$arrEduPages,$objEduData;
		$getMenu = $objEduData->getAllEduCategory();?>
		<div class="header_wraper">
			<div class="logo"><a href="<?php echo $HTPFX.$HTHOST;?>/edu"><img src="<?php echo $IMG_SERVER;?>/images/education/edu-logo.png" title="logo"/></a></div>
			<div class="nav">
			    <ul>
			    	<?php  foreach ($getMenu as $key=>$menu){
			    		if(strtolower($sectionName)===strtolower($menu['menu_name'])){?>
				        	<li class="activeNav"><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias']?>"><?php echo $menu['menu_name'];?></a></li>
			        <?php } elseif (strtolower($menu['menu_name'])=="buzz & banter"){
		        				if(empty($_SESSION['Buzz'])){ ?>
									<li><a href="http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=buzz"><?php echo $menu['menu_name'];?></a></li>
							<?php } elseif(!empty($_SESSION['Buzz'])){
									$buzzLaunchUrl=buzzAppUrlEncryption(); ?>
									<li><a href="javascript:;" onclick="window.open('<?php echo $buzzLaunchUrl;?>',\'Banter\',\'width=455,height=708,resizable=yes,toolbar=no,scrollbars=no\'); banterWindow.focus();"><?php echo $menu['menu_name'];?></a></li>
						<?php }
			        } elseif (strtolower($menu['menu_name'])=="products"){ 
			        	$academyUrl='http://www.tradingacademy.net'; ?>
			        	<li>  <a href="javascript:displayLeavingWindow('<?php  echo $academyUrl ?>');"><?php echo $menu['menu_name'];?></a></li>
			 <?php  } else { ?>
				        	<li><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias']?>"><?php echo $menu['menu_name'];?></a></li>
				        <?php } 
			        } ?>
			    </ul>
			</div>
		</div>
		<?php  
		echo $this->eduProductFancyBox();
		displayLeaderboard($pageName);
	}
	
	function displayEduRightCol(){ 
		global $HTPFX, $HTHOST, $IMG_SERVER, $objEduData, $pageName; 
		$latestArticle = $objEduData->getEduLatestArticle();
		$countLatestArt = count($latestArticle);
		$getMenu = $objEduData->getAllEduCategory();
		$countMenuItem = count($getMenu); ?>
		<div class="eduRightPanel">
			<div class="eduStayConnecetd">
				<div style="width:300px;">
				<div id="heading">Stay Connected</div>
				<div class="eduSocialIcon">
					<ul>
						<li><a href="https://twitter.com/Minyanville" target="_blank" title="twitter"><img src="<?php echo $IMG_SERVER;?>/images/education/eduTw.png" alt="Twitter"></a></li>
						<li><a href="http://www.facebook.com/MinyanvilleMedia" target="_blank" title="facebook"><img src="<?php echo $IMG_SERVER;?>/images/education/eduFb.png" alt="Facebook"></a></li>
						<li><a href="https://plus.google.com/+minyanville" target="_blank"><img src="<?php echo $IMG_SERVER;?>/images/education/eduGplus.png" alt="g+"></a></li>
						<li><a href="http://www.minyanville.com/service/rss.htm" target="_blank"><img src="<?php echo $IMG_SERVER;?>/images/education/eduRss.png" alt="RSS"></a></li>
					</ul>
				</div>
				</div>
				<!--search box-->
				<div class="eduSearchWraper">
					<div class="eduSearchBox">
						<form name="frmEduSearch" id="frmEduSearch" action="<?php echo $HTPFX.$HTHOST;?>/edu/search.htm" method="get">
		            		<div class="eduSearchText">
			            		<input type="text" class="search" name="q" placeholder="Search..." value="" id="q" onmouseover="enableclick();return true;" onblur="disableclick();return false;" />
			            		<input type="button" class="submit" value="" name="" onclick="javascript:frmEduSearch.submit();" />
			            	</div>
		            	</form>
					</div>
				</div>
			</div>

			<div class="eduAds">
				<?php if($_SESSION['AdsFree']!="1"){
					CM8_ShowAd("MediumRectangle_Art_300x250_300x600");
				}?>					
			</div>
			<?php if($pageName!="edu-home"){ ?>
				<div class="eduRightBox">
					<h1>Latest Articles</h1>
					<?php  foreach($latestArticle as $key=>$val){
						if($key==$countLatestArt-1){ ?>
							<div class="eduRightList eduLastEle"><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>"><?php echo $val['title'];?></a></div>
						<?php  }else{?>
									<div class="eduRightList"><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>"><?php echo $val['title'];?></a></div>
						<?php  } 
					} ?>			
				</div>
				
				<div class="eduRightBox">
					<h1>Categories</h1>
					<?php  foreach($getMenu as $key=>$menu){
						if($key==$countMenuItem-1){ ?>
							<div class="eduRightList eduLastEle"><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias'];?>"><?php echo $menu['menu_name'];?></a></div>
						<?php  }else{ 
									if (strtolower($menu['menu_name'])=="buzz & banter"){
		        						if(empty($_SESSION['Buzz'])){ ?>
											<div class="eduRightList"><a href="http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module/?utm_source=Edu&utm_medium=website&utm_content=Edu&utm_campaign=buzz"><?php echo $menu['menu_name'];?></a></div>
										<?php } elseif(!empty($_SESSION['Buzz'])){
											$buzzLaunchUrl=buzzAppUrlEncryption(); ?>
											<div class="eduRightList"><a href="javascript:;" onclick="window.open('<?php echo $buzzLaunchUrl;?>',\'Banter\',\'width=455,height=708,resizable=yes,toolbar=no,scrollbars=no\'); banterWindow.focus();"><?php echo $menu['menu_name'];?></a></div>
										<?php }
			        				} elseif (strtolower($menu['menu_name'])=="products"){ 
			        					$academyUrl='http://www.tradingacademy.net'; ?>
			        					<div class="eduRightList"><a href="javascript:displayLeavingWindow('<?php  echo $academyUrl ?>');"><?php echo $menu['menu_name'];?></a></div>
			 					<?php }else{ ?>
									<div class="eduRightList"><a href="<?php echo $HTPFX.$HTHOST.'/edu/'.$menu['menu_alias'];?>"><?php echo $menu['menu_name'];?></a></div>
						<?php  }
						}
					  } ?>
				</div>
	
			<div class="eduAds">
				<?php if($_SESSION['AdsFree']!="1"){
						CM8_ShowAd("MediumRectangle_300x250_300x600");
				}?>	
			</div>
		<?php } ?>
	</div>
	<?php  }
	
	function displayEduLeftCatCol($sectionName, $p, $offset){
		global $HTPFX, $HTHOST, $IMG_SERVER, $objEduData, $objArticle; 
		$eduCatAllArt = $objEduData->getEduAllAlerts($sectionName,$offset); ?>
		<div class="eduLeftPanel">
			<div class="eduLeftArtList">
				<div class="eduCategoryHeading">
					<?php
						if($sectionName=="How to Trade")
						{
							echo "<h1>Trade Like a Pro:</h1>
						<p>Study How Professionals Successfully Trade and Make Money.</p>";
						}
						else if($sectionName=="How to Invest")
						{
							echo "<h1>Become a Smart Investor:</h1>
						<p>Learn the Best Ways to Grow Your Capital.</p>";						
						}
						else if($sectionName=="Trading Videos")
						{
							echo "<h1>Trading Videos:</h1>
						<p>Let Veteran Traders Show You How to Analyze the Market.</p>";
						}
						else if($sectionName=="Personal Finance")
						{
							echo "<h1>Personal Finance:</h1>
						<p>Sharpen Your Skills to Save and Plan for Retirement.</p>";
						}
					?>					
				</div>
				<?php if(!empty($eduCatAllArt)){
					foreach ($eduCatAllArt as $key=>$val) {
						if($val['edu_img']==false){
							$imgPath = $IMG_SERVER.'/images/education/edu_sample.jpg';
						} else {
							$imgPath = $IMG_SERVER.'/assets/edu/images/'.$val['edu_img'];
						} ?>
						<div class="eduCatArt">
							<div class="educatArtImg"><img src="<?php echo $imgPath;?>"></div>
							<div class="eduCatArtContent">
								<h1><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>"><?php echo $val['title'];?></a></h1>
								<p><?php echo $val['name'];?><br><?php echo date('D M d, Y h:i',strtotime($val['publish_date']));?> EDT</p>
								<div class="eduCatArtDesc"><?php
									$body = $this->getPartOfEduBody($val['body'],60);
									echo $body;?>..</div>
								<div class="eduReadMore"><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>">Read More</a></div>
							</div>
						</div>
					<?php }
						$this->displayEduPagination($sectionName,$p,$offset);
				} else{
					echo '<div class="eduNoPost"> No post has been posted in this Category</div>';
				} ?>
			</div>
			<div style="clear:both;"></div>
			<?php $this->eduLowerWidget();?>
		</div>
	<?php  }
	
	function eduLowerWidget(){
		global $objArticle;?>
		<div class="eduOutbrainWidget"><?php $objArticle->outbrainWidget();?></div>
		<div style="clear:both;"></div>
		<div class="eduTdAdWidget">
		<div id="article_bottom_widget">
				<center>
				    <div id="TD_Bottom_ad" style="margin:0 0 -4px;">
				         <? CM8_ShowAd("partnerCenter"); ?>
				    </div>
				</center>
		</div></div>
		<div style="clear:both;"></div>
		<?php if(!$_SESSION['AdsFree']) { ?>
     		 <div id="text-ad-container">
			    <?php CM8_ShowAd('adbladeNewCode'); ?>
		    </div>
    		<div style="clear:both;"></div>
 		<?php } 
	}
	
	function displayEduPagination($secName,$p,$offset){
		global $objEduData, $eduPostLimit;
		$numPost = $objEduData->getEduPostCount($secName);
		$contentCount=$eduPostLimit;
		$this->showEduPagination($numPost,$contentCount,$p,$secName,$offset); 
	}

	function showEduPagination($numrows,$contentcount,$p,$catName,$offset){
		global $HTPFX,$HTHOST,$objEduData,$eduPagination;
		$catUrlAlias = $objEduData->getEduCatByName($catName);
		$url= $HTPFX.$HTHOST."/edu/".$catUrlAlias['menu_alias']."/";
		if($numrows>$contentcount) { 
			$countnum = ceil(($numrows/$contentcount));
			$totalRows = $countnum;
			if(($eduPagination<$countnum) && ($offset+ $eduPagination < $countnum)){
				$countnum = $eduPagination + ($p+1);
			} ?>
			<div class="eduPagination">
			<?php if($p!="1"){
					$j=$p-1;
					if($j==1){ ?>
						<div id="eduPrev" class="eduNextPrevPage"><a href="<?php echo $url;?>">&laquo; Previous Page</a></div>
					<?php } else { ?>
						<div id="eduPrev" class="eduNextPrevPage"><a href="<?php echo $url.'p/'.$j;?>">&laquo; Previous Page</a></div>
					<?php }
				}
				$min=(floor($p/$eduPagination)*$eduPagination)+1;
				$max=(ceil($p/$eduPagination))*$eduPagination;
				if($p%10==0){
					$min=((floor($p/$eduPagination)-1)*$eduPagination)+1;
				}
				echo "<div class='eduPaginationList'><ul>";
				for($i=$min; $i<=$max; $i++){
					$j=$i;
					if($i<=$totalRows){
						if($i==$p){ ?>
    						<li class="eduActivePage"><?php echo $i;?></li>
    				<?php } else { 
    						if($i==1){ ?>
    							<li><a target="_parent" href="<?php echo $url;?>"><?php echo $i;?></a></li>
    					<?php } else { ?>
    						<li><a target="_parent" href="<?php echo $url."p/".$i;?>"><?php echo $i;?></a></li>
    				<?php 	}	
    					} 
					}
				}
				echo "</ul></div>";
				$next=$p+1;
				if($totalRows-1>=$p){  ?>
					<div id="eduNext" class="eduNextPrevPage"><a target="_parent" href="<?php echo $url.'p/'.$next;?>">Next Page &raquo;</a></div>
				<?php } ?>
			</div>
	<?php  }
	}
	
	function displayEduLeftArtCol($id){ 
		global $HTPFX, $HTHOST, $IMG_SERVER, $objEduData, $objArticle,$pageurl; 
		$eduArtDetail = $objEduData->getEduAlertDetails($id);
		$getArtContrib = $objEduData->getContributorById($eduArtDetail['contrib_id']);
		if($eduArtDetail['edu_img']==false){
			$imgPath = $IMG_SERVER.'/images/education/edu_sample.jpg';
		} else {
			$imgPath = $IMG_SERVER.'/assets/edu/images/'.$eduArtDetail['edu_img'];
		}
		
		if($eduArtDetail['publish_date']=="" || $eduArtDetail['publish_date']=="0000-00-00 00:00:00"){
			$publish_date=$eduArtDetail['creation_date'];
		}else{
			$publish_date=$eduArtDetail['publish_date'];
		} ?>
		<div class="eduLeftPanel">
			<div class="eduLeftArtList">
				<div class="eduArticleShortDesc">
					<div class="eduArticleImg"><img src="<?php echo $imgPath;?>"></div>
					<div class="eduArtSocialSharing">
						<h1><?php echo $eduArtDetail['title'];?></h1>
						<p><?php echo $getArtContrib;?><br>
						<?php echo date('D M d, Y h:i',strtotime($publish_date));?> EDT</p>
						<div class="eduArtSocialIcon">
							<ul>
								<li><span class="st_facebook_large" displayText='Facebook' st_url="<?=$HTPFX.$HTHOST.$pageurl?>" ></span></li>
								<li><span class="st_twitter_large" displayText='Tweet' st_via="" st_msg="<?=$eduArtDetail['title'].' via @minyanville';?>" st_url="<?=$HTPFX.$HTHOST.$pageurl?>"></span></li>
								<li><span class='st_googleplus_large' displayText='Google +1' st_url="<?=$HTPFX.$HTHOST.$pageurl?>"  ></span></li>
								<li><span class="st_email_large" displayText='Email' st_url="<?=$HTPFX.$HTHOST.$pageurl?>"></span></li>

								<li style="margin: 0px 0px 0px 5px;" ><a href="#fb-comments" ><img src="<?php echo $IMG_SERVER;?>/images/education/edu-email.png" title="comments"></a></li>
								<li style="margin: 0px 0px 0px 10px;"><a href="<?php echo $HTPFX.$HTHOST;?>/edu/print.php?a=<?php echo $id;?>"  target="_blank"><img src="<?php echo $IMG_SERVER;?>/images/education/edu-print.png" title="print"></a></li>
							</ul>
							<!-- Share this Code Start -->
							<script type="text/javascript">var switchTo5x=true;</script>
							<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
							<script type="text/javascript">stLight.options({publisher: "c33749e3-a8dd-48d6-af73-7568c530a7f8",onhover: false}); </script>
							<!-- Share this Code End -->
						</div>
					</div>
				</div>
				<div class="eduArtContent">
					<?php if($eduArtDetail['layoutType']=='showAd'){ ?>
						<div class="eduInContentAd"><?php CM8_ShowAd("MediumRectangle_300x250_inContent");?></div>
					<?php } ?>
					<?php if($eduArtDetail['is_video']=='1'){ 
						$body = str_replace("{VIDEO}","<div>".displayRadioInArticle($eduArtDetail['eduVideo'],'')."</div>", $eduArtDetail['body']);
					} else {
						$body = $eduArtDetail['body'];
					}	?>
					<div class="eduArtBody" id="eduArtBody"><?php echo $body;?></div>
				</div>
				<div id="fbcomment_layout">
					<div id="fb_seo">&nbsp;</div>
					<?php $objArticle->FbcommentSEO();
					$objArticle->fbcommentLayout(); ?>
				</div>
			</div>
				<div style="clear:both;"></div>
			<div id="bottom-module-container" style="float:left;padding:20px 0 20px 0;border:none;" class="konafilter">
				<?=$objArticle->getWidget($id);?>
			</div> <!-- bottom module container -->
			<div id="article_bottom_widget">
				<center>
				    <div id="TD_Bottom_ad" style="margin:0 0 -4px;">
				         <? CM8_ShowAd("partnerCenter"); ?>
				    </div>
				</center>
			</div>
			<? if(!$_SESSION['AdsFree']) { ?>
			 <div id="text-ad-container">
		    	<?php CM8_ShowAd('adbladeNewCode'); ?>
		     </div>
		       <div style="clear:both;"></div>
		    <? } ?>
		</div>
<?php }

	function displayEduLeftGlossaryCol(){ 
		global $IMG_SERVER, $objEduData;
		$glossaryList = $objEduData->getEduGlossary();?>
		<div class="eduLeftPanel">
			<div id="eduGlossary">
				<div id="eduGlossHeader">
				   <div id="eduGlossBookImg"><img src="<?php echo $IMG_SERVER;?>/images/education/book.PNG" alt=""/></div>
				   <div class="eduGlossHeading">&#160;Glossary</div>
				   <div class="eduGlossSearch">
				   		<input id="eduGlossSearchText" type="text" value="" title="search" placeholder="Search the glossary ..." onkeypress="if(event.keyCode==13){searchEduGlossary();}">
				   		<img id="eduGlossSearchIcon" src="<?php echo $IMG_SERVER;?>/images/education/serach-glossary.png" alt="" onclick="javascript:searchEduGlossary();" />
					</div>
				</div>
				<div id="eduGlossBody">
				<div class="eduGlossSearchProg">&nbsp;</div>
				<div class="eduGlossAlphabetsBar">
					<div class="eduGlossAlphabets" id="eduGlossAlphabets">
						<?php foreach (range('A', 'Z') as $i){ 
							$divId="alpha_".$i;
							if($i=="A"){ ?>
								<div class="eduGlossAlphabetsIcon eduGlossAlphabetsIconActive" id="<?php echo $divId;?>"><a href="<?php echo '#'.$i;?>" onClick="showActiveAlpha('<?php echo $divId;?>');"><?php echo $i;?></a></div>
							<?php }else{ ?>
								<div class="eduGlossAlphabetsIcon" id="<?php echo $divId;?>"><a href="<?php echo '#'.$i;?>" onClick="showActiveAlpha('<?php echo $divId;?>');"><?php echo $i;?></a></div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				
				<div id="eduGlossContentContainer">
					<?php $arrFirstLetter = array();
					foreach ($glossaryList as $key=>$val) { 
						$firstLetter = strtoupper($val['name']['0']);
						if(!in_array($firstLetter, $arrFirstLetter)){
							$arrFirstLetter[]=$firstLetter;?>
							<a id="<?php echo $firstLetter;?>"></a>
						<?php } ?>
						<div class="eduGlossContent">
							<div class="eduGlossTerm"><?php echo ucwords($val['name']);?></div>
							<div class="eduGlossDef"><?php echo ucfirst($val['value']);?></div>
						</div>
					<?php } ?>
				</div></div>			
			</div>
			<div style="clear:both;"></div>
			<?php $this->eduLowerWidget();?>
		</div>
	<?php }
	
	function displayMvpSubscription(){ 
		global $productUrlArr;
		?>
		<div class="eduMvpBox">
			<div class="eduModuleheading"><a href="<?php echo $HTPFX.$HTHOST;?>/subscription/" >MVP Subscriptions</a></div>
				<div class="eduMvpContent">
					<?php 
					if($_SESSION['ElliottWave']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/ewi/';	
					}
					else
					{
						$url=$productUrlArr['ElliottWave'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">Elliott Wave Insider</a>
						<p>Market analysis from the Elliott Wave pioneers.</p>
					</div>
					<?php 
					if($_SESSION['peterTchir']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/tchir-fixed-income/';	
					}
					else
					{
						$url=$productUrlArr['peterTchir'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">Peter Tchir's Fixed Income Repor</a>
						<p><a href="<?php echo $url; ?>">The inside scoop on the bond market.</a></p>
					</div>
					<?php 
					if($_SESSION['Cooper']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/cooper/';	
					}
					else
					{
						$url=$productUrlArr['Cooper'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">Jeff Cooper's Daily Market Report</a>
						<p><a href="<?php echo $url; ?>">Day and swing trading setups daily, with market commentary from an expert.</a></p>
					</div>
					<?php 
					if($_SESSION['ElliottWave']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/ewi/';	
					}
					else
					{
						$url=$productUrlArr['ElliottWave'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">TechStrat Report</a>
						<p><a href="<?php echo $url; ?>">Get daily insights & ideas from tech expert Sean Udall, so you can jump on tomorrow's winning stocks today!</a></p>
					</div>
					<?php 
					if($_SESSION['Optionsmith']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/optionsmith/home.htm';	
					}
					else
					{
						$url=$productUrlArr['Optionsmith'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">Optionsmith by Steve Smith</a>
						<p><a href="<?php echo $url; ?>">Options trading strategies and portfolio reviews with veteran options trader Steve Smith.</a></p>
					</div>
					<?php 
					if($_SESSION['TheStockPlayBook']=="1")
					{
						$url =  $HTPFX.$HTHOST.'/thestockplaybook/home.htm';	
					}
					else
					{
						$url=$productUrlArr['TheStockPlayBook'];
					}
					?>
					<div class="eduMvpListing"><a href="<?php echo $url; ?>">The Stock Playbook</a>
						<p><a href="<?php echo $url; ?>">Get Daily Videos detailing Dave Dispennette's market view, portfolio strategy & stocks under $10.</a></p>
					</div>
				</div>
		</div>
	<?php }

	function displayEduProducts(){
		global $IMG_SERVER, $objEduData; 
		$eduProdlist = $objEduData->getEduProduct('1');
		$academyUrl='http://www.tradingacademy.net'; ?>
		<div class="eduProducts">
			<div class="eduModuleheading">Products</div>
				<div id="eduSubMain">
				<ul>
					<?php if(!empty($eduProdlist)){
						foreach ($eduProdlist as $key=>$val){ ?>
						<li>
							<div class="eduProdImg" onClick="javascript:displayLeavingWindow('<?php  echo $val['productUrl'] ?>');"><img src="<?php echo $IMG_SERVER;?>/assets/edu/images/<?php echo $val['image'];?>" alt=""/></div>
							<div class="eduProdDetail">
								<div class="euProdTitle" onClick="javascript:displayLeavingWindow('<?php  echo $val['productUrl'] ?>');"><?php echo $val['title'];?></div>
								<div class="eduProdType" onClick="javascript:displayLeavingWindow('<?php  echo $academyUrl ?>');">Online Trading Academy</div>
								<div class="eduProdPrice" onClick="javascript:displayLeavingWindow('<?php  echo $val['productUrl'] ?>');">$<?php echo $val['price'];?></div>
							</div>
							<div class="eduProdDesc eduMargin1" onClick="javascript:displayLeavingWindow('<?php  echo $val['productUrl'] ?>');"><?php echo $val['description'];?></div>
							<div align="center" class="eduBuyNow"><div class="eduBuyNowBttn" onClick="javascript:displayLeavingWindow('<?php  echo $val['productUrl'] ?>');">Buy Now</div></div>
						</li>
					<?php }
					} ?>
				</ul>
			</div>
		</div>
	<?php }
	
	function eduProductFancyBox(){
		global $IMG_SERVER; ?>
		<div><a id="eduProdPopUpWindow" href="#eduLeavingWindow"></a></div>
		<div style="display:none;">
			<div id="eduLeavingWindow" class="eduProductPopUp">
				<div class="eduClosebttn">
					<img style="margin:-15px 0px 0px -5px;" align="right" src="<?php echo $IMG_SERVER;?>/images/fancybox/bnb_closeBtn.png" alt="" onclick="javascript:redirectToProduct();"/>
					<div class="eduCloseText"><span style="text-decoration:underline;"> Close</span><span> or Esc key</span></div>
				</div>
				<div class="eduProdPopUpText">You are now leaving the<br/>  MV Education Center web server.</div>
				<div class="eduProdPopUpItatlicText">Thank you very much for visiting!</div>
				<div class="eduProdLink">
					<div class="eduProdLinkBox">You will now access<br/> http://www.tradingacademy.net/</div>
				</div>
				<div class="eduProdPopUpHopeTxt">We hope your visit was informative and enjoyable.</div>
			</div>
		</div>
<?php }

	function eduSearch($search,$offset,$searchType,$p){
		global $objEduData,$HTPFX,$HTHOST;
		$numrows = $objEduData->getEduSearchCount($search,$searchType);
		$getAllAlerts = $objEduData->getEduSearch($search,$offset,$searchType);
		?>
		<div class="eduLeftPanel">
			<div class="eduLeftArtList">
				<div class="eduCategoryHeading">
					<h1>Serach Result for '<?php echo ucwords($search);?>'</h1>
				</div>
				<?php if(!empty($getAllAlerts)){
					foreach ($getAllAlerts as $key=>$val) {
						if($val['edu_img']==false){
							$imgPath = $IMG_SERVER.'/images/education/edu_sample.jpg';
						} else {
							$imgPath = $IMG_SERVER.'/assets/edu/images/'.$val['edu_img'];
						} ?>
						<div class="eduCatArt">
							<div class="educatArtImg"><img src="<?php echo $imgPath;?>"></div>
							<div class="eduCatArtContent">
								<h1><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>"><?php echo $val['title'];?></a></h1>
								<p><?php echo $val['name'];?><br><?php echo date('D M d, Y h:i',strtotime($val['publish_date']));?> EDT</p>
								<div class="eduCatArtDesc"><?php
									$body = $this->getPartOfEduBody($val['body'],60);
									echo $body;?>..</div>
								<div class="eduReadMore"><a href="<?php echo $HTPFX.$HTHOST.$val['url'];?>">Read More</a></div>
							</div>
						</div>
					<?php }
						$this->showEduSearchPagination($numrows,$p,$searchType,$search);
				} else{
					echo '<div class="eduNoPost"> No Result Found!!!</div>';
				} ?>
			</div>
			<div style="clear:both;"></div>
			<?php $this->eduLowerWidget();?>
		</div>
	<? }
	
	function getPartOfEduBody($articleBody,$numOfWords){
                $firstLinePattern = '/^(.*)$/m';
                $articleBody = strip_tags($articleBody);
                $body = mswordReplaceSpecialChars($articleBody);
                $wordCount = count(str_word_count($body,1));
                if($wordCount>$numOfWords){
                        $words=explode(" ",$body,$numOfWords+1);
                        unset($words[$numOfWords]);
                        $body=implode(" ",$words);
                        $body=$body." ...";
                }
                return $body;
        }
	

	 function showEduSearchPagination($numrows,$p,$searchType,$wordToSearch){
		global $HTPFX,$HTHOST,$eduPostLimit,$eduPagination;
		$contentcount = $eduPostLimit;
		$url= $HTPFX.$HTHOST."/edu/search.htm?q=".$wordToSearch;
		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="eduPagination"><ul>
			<?php if($p!="1"){
					$j=$p-1;
					if($j==1){ ?>
						<li class="eduNextPrevPage"><a href="<?php echo $url;?>">&laquo; Previous Page</a></li>
					<?php } else { ?>
						<li class="eduNextPrevPage"><a href="<?php echo $url.'&p='.$j;?>">&laquo; Previous Page</a></li>
					<?php }
				}
				$min=(floor($p/$eduPagination)*$eduPagination)+1;
				$max=(ceil($p/$eduPagination))*$eduPagination;
				if($p%10==0){
					$min=((floor($p/$eduPagination)-1)*$eduPagination)+1;
				}

				for($i=$min; $i<=$max; $i++){
					$j=$i;
					if($i<=$totalRows){
						if($i==$p){ ?>
    						<li class="eduActivePage"><?php echo $i;?></li>
    				<?php } else { 
    						if($i==1){ ?>
    							<li><a target="_parent" href="<?php echo $url;?>"><?php echo $i;?></a></li>
    					<?php } else { ?>
    						<li><a target="_parent" href="<?php echo $url."&p=".$i;?>"><?php echo $i;?></a></li>
    				<?php 	}	
    					} 
					}
				}
				
				$next=$p+1;
				if($totalRows-1>=$p){  ?>
					<li class="eduNextPrevPage"><a target="_parent" href="<?php echo $url.'&p='.$next;?>">Next Page &raquo;</a></li>
				<?php } ?>
			</ul></div>
		<?  }
	}

}
?>