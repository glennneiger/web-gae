<?php 
class cooperDesign{
	function displayCooperHeader($categoryName){
		global $HTPFX, $HTHOST, $objCooperData,$IMG_SERVER; 
		$getMenu = $objCooperData->getAllCooperCategory();?>
		<div class="cooper_logo"  id="cooper_backTop"><a href="<?php echo $HTPFX.$HTHOST;?>/cooper"><img src="<?php echo $IMG_SERVER;?>/images/cooper-redesign/jeffCooperLogo.png" title="logo"/></a></div>
		<div class="cooper_emailQust">Email your questions <a href="mailto:cooper@minyanville.com">cooper@minyanville.com</a></div>
		<div class="clr"></div>
		<div id="cooper_header">
	        <ul>
	        	<?php  foreach ($getMenu as $key=>$menu){
			    		if(strtolower($categoryName)===strtolower($menu['category_alias'])){?>
				        	<li class="active"><a href="<?php echo $HTPFX.$HTHOST.'/cooper/'.$menu['category_alias']?>"><?php echo $menu['category_name'];?></a></li>
			 	<?php  } else { ?>
				        	<li><a href="<?php echo $HTPFX.$HTHOST.'/cooper/'.$menu['category_alias']?>"><?php echo $menu['category_name'];?></a></li>
				        <?php } 
			        } ?>
	        </ul>
        	
    	</div>
	<?php }
	
	function displayCooperAlert($id,$alertDetails){
		global $HTPFX,$HTHOST,$objCooperData, $objCooperDesign,$IMG_SERVER;
		$categoryDetails = $objCooperData->getCooperCategoryDetails($alertDetails['redesign_cat_id']);
		foreach($categoryDetails as $k=>$v)
		{
			$url =$HTPFX.$HTHOST."/cooper/".$v['category_alias'];
			$categoryArr[]="<a href='".$url."'><span>".$v['category_name']."</span></a>";
		}
		$category = implode(", ", $categoryArr);
		?>
		<div class="cooper_lftPanel">
			<div class="cooper_alertDetailBox" >
				<h1><?=$alertDetails['title'];?></h1>
				<h3><?=$alertDetails['date'];?> 
					<span class="cooperPrint"><a href="javascript:cooperprint('<?=$id;?>');" target="_self"><img src="<?=$IMG_SERVER?>/images/icons/print-icon.gif" alt="Print" />  Print</a></span>
				</h3>
				<div id="cooper_post" ><?=$alertDetails['body'];?></div>
				<?php if(!empty($alertDetails['position'])){ ?>
							<div class="clr"></div>
							<div class="cooperPosition"><?php echo $alertDetails['position'];?></div>
				<?php } ?>
				<div class="clr"></div>
				<div class="cooper_alertBottom">
					<div class="cooper_category">Category: <?=$category?></div>
					<div class="cooper_goTop"><a href="#cooper_backTop">Back to Top</a></div>
				</div>
			</div>
		</div>
	<? }
	
	function displayCooperRightCol($getMonth=0,$getYear=0){
		global $IMG_SERVER, $objCooperData, $HTPFX, $HTHOST,$archiveStartYear,$archiveStartMonth,$CDN_SERVER;
			$curMonth = date('n');
			$curYear = date('Y');
		?>
			<div class="cooper_rght_panel">
				<div class="cooper_box_rght hr">
	            	<h2>Search</h2>
	                <div class="clr"></div>
	                <form name="frmCooperSearch" id="frmCooperSearch" action="<?=$HTPFX.$HTHOST?>/cooper/search.htm" method="get">
	            		<input type="text" name="q" value="Search..." id="q" onFocus="if(this.value=='Search...')this.value=''" onmouseover="enableclick();return true;" onblur="if(this.value=='')this.value='Search...'" />
	            		<input type="button" value="" name="" onclick="javascript:frmCooperSearch.submit();" />
	            	</form>
            	</div>
				<div class="cooper_box_rght hr">
	            	<a href="<?php echo $HTPFX.$HTHOST?>/cooper/jeff-cooper/"><h2>About Jeff Cooper</h2></a>
            	</div>
            	<div class="cooper_box_rght hr">
	            	<div class="cooper_user_guide"><a href="http://mvp.minyanville.com/support-jeffcooper/"  target="_blank">User Guide</a></div>
            	</div>
              	<div class="cooper_box_rght hr">
	            	<h2>Archives</h2>
	            	<div class="clr"></div>
	            	<script>
	            		jQuery.noConflict();
						jQuery(function() {
							var d = new Date();
							var y = d.getFullYear();
							var m = d.getMonth()+1;
							var archiveMnth = '<?=$getMonth?>';
							var archiveYear = '<?=$getYear?>';
							if(archiveMnth==0){
								jQuery('#yr'+y+'Mnth').css("display","block");
								jQuery('#mnth'+m+y+'Catg').css("display","block");
							}else{
								jQuery('#yr'+archiveYear+'Mnth').css("display","block");
								jQuery('#mnth'+archiveMnth+archiveYear+'Catg').css("display","block");
							}
							
					 		jQuery('.yrList' ).click(function() {
								var id=this.id;
								if ( jQuery( '#'+id+'Mnth' ).is( ":hidden" ) ) {
				 			    	jQuery( '#'+id+'Mnth' ).show(700);
				 			  	} else {
				 			    	jQuery( '#'+id+'Mnth' ).slideUp(700);
				 			  	}
					 		});
					 		jQuery('.mnthList').click(function() {
					 			var id=this.id;
					 			if ( jQuery( '#'+id+'Catg' ).is( ":hidden" ) ) {
						 			jQuery( '#'+id+'Catg' ).show(700);
					 			} else {
					 			    jQuery( '#'+id+'Catg' ).slideUp(700);
					 			}
					 		});
						});
					</script>
	            	 <ul id="cooperArchive" class="cooperArchive">
	            	<?php for($year=$curYear;$year>=$archiveStartYear;$year--){ ?>
							<ul>
								<li>
									<div id="yr<?=$year?>" class="yrList">
										<div class="yearHeading"><?php echo $year;?></div>
										<div class="cooperListImg"><img src="<?=$IMG_SERVER?>/images/cooper-redesign/left-cross.png"></div></div>
										
										<div id="yr<?=$year?>Mnth" class="inner">
                          				<ul>
                            				<li>
												<?php if($year==$getYear && $year!=$curYear){
													for($month=12;$month>=$archiveStartMonth;$month--){
														$monthName= date('F',mktime(0,0,0,$month));
														$mon = sprintf("%02s", $month);?>
														<div id="mnth<?=$month.$year?>" class="mnthList" onclick="javascript:showCooperPostsByMonths('<?=$mon?>','<?=$year?>');">
															<div class="monthHeading"><?=$monthName.' '.$year;?></div>
															<div class="cooperMnthListImg"><img src="<?=$IMG_SERVER?>/images/cooper-redesign/left-cross.png"></div>
														</div>
														<div id="mnth<?=$month.$year?>Catg" class="innerCat">
							                                <ul id="showMonthlyWisePost">
							                                	<?php if($mon==$getMonth){
																 	$getMonthlyPost = $objCooperData->getCooperPostByMonthCount($month,$year);
																	foreach($getMonthlyPost as $k=>$v){ ?>
																		<li><a href="<?=$HTPFX.$HTHOST?>/cooper/<?=$v['category_alias']?>/yr/<?=$year?>/mo/<?=$mon?>"><?php echo $v['category_name'].'('.$v['alertCount'].')'?></a></li>
																	<?php }
																}?>
							                                </ul>
							                              </div>
													<? }
												}elseif($year==$curYear){
													for($month=$curMonth;$month>=$archiveStartMonth;$month--){
														$monthName= date('F',mktime(0,0,0,$month));
														$mon = sprintf("%02s", $month); ?>
															<div id="mnth<?=$month.$year?>" class="mnthList" onclick="javascript:showCooperPostsByMonths('<?=$mon?>','<?=$year?>');">
																<div class="monthHeading"><?=$monthName.' '.$year;?></div>
																<div class="cooperMnthListImg"><img src="<?=$IMG_SERVER?>/images/cooper-redesign/left-cross.png"></div>
															</div>
															 <div id="mnth<?=$month.$year?>Catg" class="innerCat">
															 	<ul id="showMonthlyWisePost">
															 		<?php 
															 		if($mon==$curMonth){
																 		$getMonthlyPost = $objCooperData->getCooperPostByMonthCount($month,$year);
																		foreach($getMonthlyPost as $k=>$v){ ?>
																				<li><a href="<?=$HTPFX.$HTHOST?>/cooper/<?=$v['category_alias']?>/yr/<?=$year?>/mo/<?=$mon?>"><?php echo $v['category_name'].'('.$v['alertCount'].')'?></a></li>
																		<?php }
																	}elseif($mon==$getMonth){
																		$getMonthlyPost = $objCooperData->getCooperPostByMonthCount($month,$year);
																		foreach($getMonthlyPost as $k=>$v){ ?>
																				<li><a href="<?=$HTPFX.$HTHOST?>/cooper/<?=$v['category_alias']?>/yr/<?=$year?>/mo/<?=$mon?>"><?php echo $v['category_name'].'('.$v['alertCount'].')'?></a></li>
																		<?php }
																	}?>
															 	</ul>
								                              </div>
													<? } 
												}else{
													for($month=12;$month>=$archiveStartMonth;$month--){
														$monthName= date('F',mktime(0,0,0,$month));
														$mon = sprintf("%02s", $month); ?>
														<div id="mnth<?=$month.$year?>" class="mnthList" onclick="javascript:showCooperPostsByMonths('<?=$mon?>','<?=$year?>');">
															<div class="monthHeading"><?=$monthName.' '.$year;?></div>
															<div class="cooperMnthListImg"><img src="<?=$IMG_SERVER?>/images/cooper-redesign/left-cross.png"></div>
														</div>
															<div id="mnth<?=$month.$year?>Catg" class="innerCat">
							                                <ul id="showMonthlyWisePost">&nbsp;</ul>
							                              </div>
													<? } 
												}?>
											</li>
										</ul>
									</div>
								</li>
							</ul>
					<?php } ?>
				</ul>
				</div>
	            <div class="cooper_box_rght">
	            	<h2>Cooper's Books and DVDs</h2>
	            	<p>Learn more about the techniques and strategies that Jeff  has developed through the years to trade successfully.</p>
	            	<?php echo $this->cooperProductFancyBox();
	            	$produrl1='http://www.invest-store.com/cgi-bin/minyanville-bin/moreinfo.cgi?item=5288549';
	            	$produrl2='http://www.invest-store.com/cgi-bin/minyanville-bin/moreinfo.cgi?item=5197574';
	            	$produrl3='http://www.invest-store.com/cgi-bin/minyanville-bin/moreinfo.cgi?item=3156887';
	            	$produrl4='http://www.invest-store.com/cgi-bin/minyanville-bin/moreinfo.cgi?item=3156889';
	            	?>
	            	<div id="ca-container" class="ca-container"><div class="ca-wrapper">
		            	<div class="ca-item fade">
							<div class="ca-item-main">
		            			<img src="<?=$IMG_SERVER?>/images/cooper-redesign/idts_book_img.png" onClick="javascript:displayCooperLeavingWindow('<?=$produrl1?>');" />
		            			<div class="cooperBuyNow" onClick="javascript:displayCooperLeavingWindow('<?=$produrl1?>');">Buy Now</div>
		            		</div>
		            	</div>
		            	<div class="ca-item fade">
							<div class="ca-item-main">
		            			<img src="<?=$IMG_SERVER?>/images/cooper-redesign/sstcmm_book_img.png" onClick="javascript:displayCooperLeavingWindow('<?=$produrl2?>');" />
		            			<div class="cooperBuyNow" onClick="javascript:displayCooperLeavingWindow('<?=$produrl2?>');">Buy Now</div>	
		            		</div>
		            	</div>
		            	<div class="ca-item fade">
							<div class="ca-item-main">
		            			<img src="<?=$IMG_SERVER?>/images/cooper-redesign/hrtu_book_img.png" onClick="javascript:displayCooperLeavingWindow('<?=$produrl3?>');" />
		            			<div class="cooperBuyNow" onClick="javascript:displayCooperLeavingWindow('<?=$produrl3?>');">Buy Now</div>
		            		</div>
		            	</div>
		            	<div class="ca-item fade">
							<div class="ca-item-main">
		            			<img src="<?=$IMG_SERVER?>/images/cooper-redesign/hrtuII_book_img.png" onClick="javascript:displayCooperLeavingWindow('<?=$produrl4?>');" />
		            			<div class="cooperBuyNow" onClick="javascript:displayCooperLeavingWindow('<?=$produrl4?>');">Buy Now</div>
		            		</div>
		            	</div>
	            	</div>
	            	<script type="text/javascript" src="<?=$CDN_SERVER?>/js/jquery.easing.1.3.js"></script>
	            	<script src="<?=$CDN_SERVER?>/js/jquery.scrollCooper.js"></script>
					<script>
						jQuery( function() {
							jQuery('#ca-container').scrollCooper();
						} );
					</script>
	            </div></div>
			</div>
	<?php }
	
	function displayCooperCatLeft($categoryName,$offset,$p,$month,$year){
		global $objCooperData,$HTPFX,$HTHOST;
		$getAllAlerts = $objCooperData->getAllAlerts($categoryName,$offset,$month,$year);
		?>
		<div class="cooper_lftPanel">
			<? if(!empty($getAllAlerts)){
					foreach($getAllAlerts as $key=>$val){ ?>
						<div class="cooper_alertBox">
							<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
							<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
							<div class="clr"></div>
						</div>
				<? }
					$this->displayCooperPagination($categoryName,$p,$month,$year);
			   }else{
					echo '<div class="no_post"> There are currently no posts in this category.</div>';
				}
		?>
		</div>
	<? }

	function displayCooperSearch($search,$offset,$searchType,$p){
		global $objCooperData,$HTPFX,$HTHOST;
		$numrows = $objCooperData->getCooperSearchCount($search,$searchType);
		$getAllAlerts = $objCooperData->getCooperSearch($search,$offset,$searchType);
		?>
		<div class="cooper_lftPanel">
		<? if(!empty($getAllAlerts)){
				foreach($getAllAlerts as $key=>$val){ ?>
					<div class="cooper_alertBox">
						<h1><a href="<?=$HTPFX.$HTHOST.$val['url'];?>"><?=$val['title'];?></a></h1>
						<h3><?=date('F d, Y',strtotime($val['publish_date']));?></h3>
						<?php $body = substr(strip_tags($val['body']),0,322);
		                            $body=$body." ..."; ?>
						<div id="cooper_alert_body" ><?=$body;?></div>
						<div class="clr"></div>
					</div>
			<? 	}
				$this->showCooperSearchPagination($numrows,$p,$searchType,$search);
			}
			else {
			?>
				<div class="no_result"> No Result Found</div>
			<?php
			}
			 ?>
		</div>
	<? }

	 function showCooperSearchPagination($numrows,$p,$searchType,$wordToSearch){
		global $HTPFX,$HTHOST,$cooperPostLimit;
		$contentcount = $cooperPostLimit; 	
	 	$url= $HTPFX.$HTHOST."/cooper/search.htm?q=".$wordToSearch;
		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="cooper_pagination">
				<? if(!$p=="0"){
						$j=$p-1;
						if($j<1){ ?>
							<a href="<?=$url?>"><div class="cooperPageButton prv_button">&laquo; Previous Page</div></a>
						<? }else{ ?>
							<a target="_parent" href="<?=$url?>&p=<?=$j?>"><div class="cooperPageButton prv_button">&laquo; Previous Page</div></a>
				  		<?php }
					}
					$p=$p+1;
					if($numrows>(($p)*$contentcount)){ ?> 
						<a target="_parent" href="<?=$url?>&p=<?=$p?>"><div class="cooperPageButton nxt_button">Next Page &raquo;</div></a>
				   <?php } ?>
			</div>
			<?  }
	 }

	function displayCooperPagination($categoryName,$p,$month,$year){
		global $objCooperData,$cooperPostLimit;
		$numPost = $objCooperData->getCooperPostCount($categoryName,$month,$year); ?>
		<div>
			<? 	$contentCount=$cooperPostLimit;
				$this->showCooperPagination($numPost,$contentCount,$p,$categoryName,$month,$year); ?>
		</div>
	<? }

	function showCooperPagination($numrows,$contentcount,$p,$categoryName,$month,$year){
		global $HTPFX,$HTHOST,$objCooperData;
		$url= $HTPFX.$HTHOST."/cooper/".$categoryName."/";
		if($month>0){
			$url= $HTPFX.$HTHOST."/cooper/".$categoryName."/yr/".$year."/mo/".$month."/";
		}

		if($numrows>$contentcount) {
			$totalRows = $countnum=ceil(($numrows/$contentcount)); ?>
			<div class="cooper_pagination">
			<? if(!$p=="0"){
					$j=$p-1;
					if($j<1){?>
						<a href="<?=$url?>"><div class="cooperPageButton prv_button">&laquo; Previous Page</div></a>
					<? } else { ?>
						<a href="<?=$url?>p/<?=$j?>"><div class="cooperPageButton prv_button">&laquo; Previous Page</div></a>
					<? }
				}
				$p=$p+1;
				if($numrows>(($p)*$contentcount)){  ?>
					<a target="_parent" href="<?=$url?>p/<?=$p?>"><div class="cooperPageButton nxt_button">Next Page &raquo;</div></a>
				<? } ?>
			</div>
	<?  }
	}
	
	function displayCooperBio(){
		global $contributorName,$IMG_SERVER; ?>
		<div class="cooper_lftPanel">
			<div class="cooper_alertDetailBox">
				<h1 class="cooper_about"><?php echo $contributorName;?></h1>
				<div class="cooper_bioDesc">
					<span class="cooper_bioImage"><img src="<?php echo $IMG_SERVER?>/images/cooper-redesign/jeffCooper_bio_img.jpg"></span>
					<div>After graduating from New York University, Jeff Cooper moved to Los Angeles where he and a partner started Dogtown Skates. Jeff began his trading career at Drexel Burnham in 1981 in Beverly Hills.</div><br/><div>After a few years there, he left to work for his father's private hedge fund. In 1986, Jeff went out on his own choosing to trade exclusively for himself. After establishing a successful career as a private trader, he went on to write two best selling books: Hit and Run Trading, The Short-Term Trader's Bible, and Hit and Run Trading 2, Capturing Explosive Short-Term Moves In Stocks as well as a course, Jeff Cooper On Dominating The Day Trading Market.</div> <br /><div>In addition to his books, Jeff has also released a DVD, Intra-Day Trading Strategies: Proven Steps to Trading Profits and a DVD course, Unlocking the Profits of the New Swing Chart Method (with David Reif) which presents the first unified market theory and a practical explanation of the Gann Square of Nine Calculator, one of the most powerful tools for forecasting time and price.</div> <br /><div>After co-founding a financial markets internet site in 1999, Jeff moved on to write a column and a trading service at TheStreet.com until 2006.</div><br /> <div>Today, Jeff consults and trades for himself from his home in Malibu, California where he lives with his wife and daughter.</div><br/><div>Jeff welcomes comments and feedback at <a href="mailto:cooper@minyanville.com" class="bioEmail">cooper@minyanville.com</a>.</div>
				</div>
			</div>
			<div class="clr"></div>
			<div class="cooper_alertBottom">
				<div class="cooper_goTop"><a href="#cooper_backTop">Back to Top</a></div>
			</div>
		</div>
		<?
	}
	
	function cooperProductFancyBox(){
		global $IMG_SERVER; 
		$redirectUrl = 'http://www.invest-store.com/';?>
		<div><a id="cooperProdPopUpWindow" href="#cooperLeavingWindow"></a></div>
		<div style="display:none;">
			<div id="cooperLeavingWindow" class="cooperProductPopUp">
				<div class="cooperClosebttn">
					<img style="margin:-15px 0px 0px -5px;" align="right" onclick="javascript:closeCooperFancyBox();" src="<?php echo $IMG_SERVER;?>/images/fancybox/bnb_closeBtn.png" alt="" />
					<div class="cooperCloseText"><span style="text-decoration:underline;"> Close</span><span> or Esc key</span></div>
				</div>
				<div class="cooperProdPopUpText">You are now leaving the<br/>  minyanville.com web server.</div>
				<div class="cooperProdPopUpItatlicText">Thank you very much for visiting!</div>
				<div class="cooperProdLink">
					<div class="cooperProdLinkBox">You will now access<br/> http://www.invest-store.com/</div>
				</div>
				<div class="cooperProdPopUpHopeTxt">We hope your visit was informative and enjoyable.</div>
			</div>
		</div>
		<input type="hidden" id="sessionTimeoutCountdown" value="">
<?php }
} //class end
?>