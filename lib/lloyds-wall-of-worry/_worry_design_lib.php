<?
global $D_R,$CDN_SERVER;
include_once("$D_R/lib/lloyds-wall-of-worry/_worry_data_lib.php");
$objWorryData = new worryData();
class worryDesign{

	function displayRightColumn($pubDate,$currentWryCount){
	global $HTHOST,$HTPFX,$IMG_SERVER;
		$objWorryData = new worryData();
		$date = $objWorryData->getLatestWryDate();
		$pubDate=date("n/j/Y",strtotime($date));
		$wryResult = $objWorryData->getLatestWorry($pubDate);
		$wryCount = $currentWryCount;
	?>
	<div class="wall-right" id="wall-right">
		<div class="wall-right-box">
			<span>What is Lloyd's Wall of Worry?</span>&nbsp;Welcome to my at-a-glance guide to the issues facing investors this week -- a unique tool for traders and money managers. Typically the term "wall of worry," refers to the entire body of concerns influencing stock market action. When the wall is high, meaning the market is nervous, stocks tend to get cheaper.This wall of worry is even more specific. Every week I list the exact concerns in the marketplace and use the list to help me make buying and selling decisions. As I like to say, "Buy fear, sell cheer."

Scroll over each "worry" for additional comments. - <a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=367">Lloyd Khaner</a>
		</div>

		<div class="wall-share"><?=$this->getShareModule();?></div>
		<div style="clear:both;"></div>

		<div class="wall-right-middle">
			<p>LOW <span style="float:right; font-size:12px;">HIGH</span></p>
			<div class="wall-right-meter">
			<?=$this->getMeter($wryCount);?>
			</div>
			<span>Worry Meter</span> Once the dial rises above 15, start looking for deals. If the dial sinks below 10, consider selling; prices have likely peaked.
		</div>
		<div class="wall-right-box">
			<!--Archive Img Start-->
			<?
			if(!empty($wryResult['0'])) { ?>
		<div class="wry-archive-rght"><a href="<?=$HTPFX.$HTHOST?>/investing/lloyds-wall-of-worry/archive.htm">
			<?
			foreach ($wryResult as $key=>$val){ ?>
				<img src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['archive_img']?>" width="50px" height="42px" />
		<?	} ?>
		</a></div>
	<? }?>
		<!--Archive Img End-->
			<div style="clear:both;"></div>
			<a class="visit" href="<?=$HTPFX.$HTHOST?>/investing/lloyds-wall-of-worry/archive.htm"><span>Visit Lloyd's wall of worry Archive</span>&nbsp;Click here to visit past iterations of the Wall of Worry. </a>
		</div>
	</div>
<?	}


	function displayWall($wryResult){
	global $HTHOST,$HTPFX,$IMG_SERVER; ?>
		<div class="wall-left" >
		<!--<div>
			<div class="wall-timer">
			<? // this->displayTimer(); ?>
			</div>
			<div class="QE2-countdown">
<div>
<h2>Countdown to the First Deadline for Spending Cuts</h2>
<span>The debt drama continues. Now we've entered round two of what
looks to be at least a three-rounder, as congress must come up with
another $1.5 trillion in deficit reductions. Pressure is on the
bipartisan debt-reduction committee to make the cuts by Thanksgiving
Day. Failure to do so will automatically trigger a list of pre-set
draconian spending cuts that will hack into some prize areas of
government spending. Here we go again. Let the political games begin! </span></div>
			</div>
</div>-->
		<div style="clear:both;"></div>
			<div class="wall-title" id="wall-title"><img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/bnnr_LLOYDWallOfWorry.gif" width="436px" height="118px" /></div>
               
			<div class="wall-wry-box">
			<?
			
		if(!empty($wryResult['0'])){
				foreach ($wryResult as $key=>$val){
				$val['worry_summary']=htmlentities($val['worry_summary'],ENT_QUOTES);
					$i = $key+1;
					if($key=="2" || $key==$chkThirdWall+4){
					   $chkThirdWall=$key;

					?>
						<img id="third-img" class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
                     <?
					}elseif($i%4 =="0"){?>
						<img id="fourth-img" class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
				<?	} else { ?>
						<img class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
				<?		}
				}

			} ?>
			</div>

		</div>
<?	}

	function displayArchiveLeft(){
		global $HTHOST,$HTPFX,$IMG_SERVER;
		$objWorryData = new worryData();
		$date = $objWorryData->getLatestWryDate();
		$curretDate=$objWorryData->getCurrentWryDate();
		$curretWorryDate=date("n/j/Y",strtotime($curretDate));
		$pubDate=date("n/j/Y",strtotime($date));
		$wryByDate = $objWorryData->getLatestWorry($pubDate);
		$totalWorries = count($wryByDate);
	?>
		<div class="wall-left" >
			<div class="archive-top" >
				<div><a href="<?=$HTPFX.$HTHOST?>/investing/lloyds-wall-of-worry/<?=$curretWorryDate;?>/">RETURN TO THE WALL OF WORRY</a></div>
				<span class="archive-share"><?=$this->getShareModule();?></span>
			</div>
			<div class="archive-heading"><img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/headng_lloydWallofWorryArchive.gif" width="349px" height="123px" /></div>
	<?	if(!empty($wryByDate['0'])) { ?>
			<div class="wall-wry-box" id="archive-left-worry">
				<div class="click-here" id="archive-left-wry">click on a date to the right to see previous posts<br>
				you are currently viewing <span><?=date("F d,Y",strtotime($date));?> / <?=$totalWorries?> worries</span></div>
				<div style="clear:both;"></div>

			<? foreach ($wryByDate as $key=>$val){
					$val['worry_summary']=htmlentities($val['worry_summary'],ENT_QUOTES);
					$i = $key+1;
					if($key=="2" || $key==$chkThirdWall+4){
					   $chkThirdWall=$key;
					?>
						<img id="third-img" class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
                     <?
					}elseif($i%4 == 0){?>
						<img class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" id="fourth-img" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px" />
				<?	}else { ?>
						<img class="tTip" name="<?=$val['title']?>" title="<?=$val['worry_summary']?>" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$val['worry_img']?>" width="169px" height="144px"  />
				<?		}
				} ?>
			</div>
	<? } ?>
		</div>
<?	}

	function displayArchiveRight(){
	global $HTHOST,$HTPFX,$IMG_SERVER;
	$currentMonth = date("F");
	?>
        <div class="wall-right" id="wall-right">
		<div class="archive-right" id="archive-right">

			<? 	$objWorryData = new worryData();
				$archive_month = $objWorryData->getArchive();
				if(!empty($archive_month)) {
					foreach($archive_month as $key=>$value){
						if($currentMonth == $value['month']){ ?>
							<div class="month-name-visible"  onclick="closeArchive();">
								<img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/arrowdown.gif" width="9px" height="8px" />
								<span><?=$value['month']?>,<?=$value['year']?></span>
							</div>
						<?
						$allArchive = $objWorryData->getArchivesByMonth($value['month'],$value['year']);
						foreach($allArchive as $worry){
						?>
						<div class="archives" onclick="displayArchiveWry('<?=$worry['0']['publish_date']?>');">
						<?
							  foreach($worry as $key=>$wry){
									$i = $key+1;
									if($i%4 == 0){
						?>
							<img id="archive-fourth-img" src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$wry['archive_img']?>" width="50px" height="42px" />
							<?
									}else {
							?>
							<img src="<?=$IMG_SERVER?>/assets/lloyds-wall-of-worry/<?=$wry['archive_img']?>" width="50px" height="42px" />
						<?
									}
								}
						?>
							<div style="clear:both;"></div>
							<div class="month-detail"><?=date("F d, Y",strtotime($worry['0']['publish_date']))?></div>
							</div>
							<?
							}
						}else {
					?><div style="clear:both;"></div>
						<div class="month-name-hidden" id="month-name-hidden" onclick="openArchive('<?=$value["month"]?>','<?=$value["year"]?>');" >
							<img src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/arrowrght.gif" width="8px" height="9px" />
							<span><?=$value['month'].','.$value['year'];?></span>
						</div>
				<?		}
					}
				} else {
					echo '<div style="padding-left:10px;">There are no Worries.</div>';
				}?>
		</div>
        </div>
<?	}

		function getShareModule(){			//Share Module
		global $IMG_SERVER,$pageName;

		if ($pageName == "lloyds-wall-of-worry-landing"){ ?>
				<!-- ADDTHIS BUTTON BEGIN -->
			<a href="http://www.addthis.com/bookmark.php?v=250"
			class="addthis_button"><img
			src="<?=$IMG_SERVER?>/images/llyods-wall-of-worry/sharing_rit.gif"
			width="245" height="16" border="0" alt="Share" /></a>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
		<!-- ADDTHIS BUTTON END -->

	<? } else{ ?>
		<!-- ADDTHIS BUTTON BEGIN -->
	<a href="http://www.addthis.com/bookmark.php?v=250"
			class="addthis_button"><img
			src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif"
			width="125" height="16" border="0" alt="Share" /></a>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
			<!-- ADDTHIS BUTTON END -->
	<?	} ?>
  <? }

	function getMeter($totalWorries){
		for($i=5; $i<=35; $i++){
			if($i%5 == 0){
				if($i == $totalWorries){ ?>
					<span id="txt"><?=$i?><div id="arrow">&nbsp;</div></span>

			<?	}else { ?>
					<span id="txt"><?=$i?></span>
			<?  }
		 	}else{
				if($i == $totalWorries){ ?>
					<span id="dotclass">.<div id="arrow">&nbsp;</div></span>
			<?	}else { ?>
					<span id="dotclass">.</span>
			<?  }
			}
		}
	}

	function displayTimer(){
		global $HTPFX,$HTHOST;
	?>
		<script language="JavaScript">
		TargetDate = "11/24/2011 00:00 AM";
		BackColor = "palegreen";
		ForeColor = "navy";
		CountActive = true;
		CountStepper = -1;
		LeadingZero = true;
		<!--'<div class="time-val"><div>%%MO%%</div><span>MONTHS</span></div>'+ -->
		DisplayFormat ='<div class="time-val"><div>%%MO%%</div><span>MONTHS</span></div>'+
						'<div class="time-val"><div>%%D%%</div><span>DAYS</span></div>'+
						'<div class="time-val"><div>%%H%%</div><span>HOURS</span></div>'+
						'<div class="time-val"><div>%%M%%</div><span>MINUTES</span></div>'+
						'<div class="time-val"><div>%%S%%</div><span>SECONDS</span></div>';
		<!--DisplayFormat = "%%MO%% Month, %%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";-->
		FinishMessage = '<div class="time-val"><div>00</div><span>MONTHS</span></div>'+
						'<div class="time-val"><div>00</div><span>DAYS</span></div>'+
						'<div class="time-val"><div>00</div><span>HOURS</span></div>'+
						'<div class="time-val"><div>00</div><span>MINUTES</span></div>'+
						'<div class="time-val"><div>00</div><span>SECONDS</span></div>';
		</script>
		<script type="text/javascript" src="<?=$CDN_SERVER?>/js/worry-timer.1.4.js"></script>
<?	}
} // class End

?>