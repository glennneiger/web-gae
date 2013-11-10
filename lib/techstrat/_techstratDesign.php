<?
class techstartDesign{

	function displayTechStartHeader($categoryName){
	global $productTechStartName,$HTPFX,$HTHOST,$CONTRIBUTOR_ID,$IMG_SERVER;
	$classSelected="open_post";
	$classUnSelected="close_post";
	if($categoryName=="Open Position" || $categoryName=="Close Position"){
		$subCat = $categoryName;
		$categoryName = "Positions and Performance";
	}elseif($categoryName=="Positions and Performance"){
		$subCat = "Open Position";
	}
	?>
    	<h1><?=$productTechStartName;?></h1>
        <div class="tab_area">
            <div class="posttabs">
                <? if($categoryName=="") { ?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/all-posts
">All Posts</a></div>
                <? } else {?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/all-posts
">All Posts</a></div>
                <? }
				if($categoryName=="Trades & Ideas") {
				 ?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/trades-ideas">Trades and Ideas</a></div>
                <? } else { ?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/trades-ideas">Trades and Ideas</a></div>

                <? }
				if($categoryName=="Mailbag") {
				?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/mailbag">Mailbag</a></div>
                <? } else { ?>
					<div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/mailbag">Mailbag</a></div>
				<? }
				if($categoryName=="Research Tank") {
				?>
                <div class="<?=$classSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/research-tank">Research Tank</a></div>
                <? } else { ?>
                <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/research-tank">Research Tank</a></div>
                <? }
				if($categoryName=="Positions and Performance") {
					if($subCat=="Open Position"){?>
						<div class="techPosPerfSelected"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/positions-performance" class="<?=$classSelected;?>">Positions and Performance</a><br><span class="techOpenPositionSelected"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/open-positions">Open Positions</a></span>&nbsp;<span class="techClosePosition"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/closed-positions">Closed Positions</a></span></div>
				<? } elseif($subCat=="Close Position"){ ?>
						<div class="techPosPerfSelected"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/positions-performance" class="<?=$classSelected;?>">Positions and Performance</a><br><span class="techOpenPosition"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/open-positions">Open Positions</a></span>&nbsp;<span class="techClosePositionSelected"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/closed-positions">Closed Positions</a></span></div>
			<?		}
				} else {?>
                 <div class="<?=$classUnSelected;?>"><a href="<?=$HTPFX.$HTHOST;?>/techstrat/positions-performance">Positions and Performance</a></div>
                <? } ?>

            </div><!--Tabs end -->
            <div class="photo"><img src="<?=$IMG_SERVER;?>/images/techstrat/techstart.jpg" alt="" /><br /><span class="phototag"><a class="phototag" href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$CONTRIBUTOR_ID;?>">SEAN UDALL</a></span></div>
        </div>

	<?
	}



	function displayPost($categoryName,$offSet,$techStartPostLimit){
	global $HTPFX,$HTHOST,$techStartPostLimit, $D_R;
	include_once($D_R."/lib/_content_data_lib.php");
	$objTechStartData= new techstartData("techstrat_posts","");
	$objContent= new Content();
	$offSet=$offSet*$techStartPostLimit;
	$getResult=$objTechStartData->getTechStartPostsData($categoryName,$offSet,$techStartPostLimit);
	if($getResult){
		foreach($getResult as $key=>$value){
		$value['body']=strip_tags($value['body'],'<img>');
		$value['body']=$objContent->getCountWords($value['body'],$count="100");
		$tag=$this->displayPostTags($value['id']);
		if($value['category_name']!='')
		{
			$catName	=	$value['category_name'];
		}

		$catName=$this->displayCategoryName($value['category_id']);

		if($key=="0"){
		?>
					<div class="first_postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>"><?=$value['title'];?></a></div>
						<div class="post_tags">
							<? if($catName) { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
                        <? if($tag) {?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
						</div>
						<div class="postcontainer"><?=$value['body'];?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>" target="_self"><?=strtoupper('Read More');?>....</a></span></div>
					</div><!--END of 1st Post -->
         <? } else { ?>
					<div class="postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>"><?=$value['title'];?></a></div>

							<div class="post_tags">
							<? if($catName) { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
                        <? if($tag) { ?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
							</div>
						<div class="postcontainer"><?=$value['body'];?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$value['url'];?>" target="_self"><?=strtoupper('Read More');?>....</a></span>
						</div>
						<div style="clear:both;"></div>

					</div>
			<?
		}
		}
		}else{
			$this->displayNoPostText($categoryName);
		}
	}

	function displayRightColumn(){
	global $IMG_SERVER,$HTPFX,$HTHOST,$performanceobj;
	$objTechStartData= new techstartData("techstrat_posts","");
	?>
		<div class="content_blnk">
                <div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/techstrat/mailbag_questions.gif" alt="" /></div>
                <div class="grey_bg">
                    <div class="mailbag_qust">Email your question: <a href="mailto:tech-strat@minyanville.com">tech-strat@minyanville.com</a></div>
                </div>
            </div><!--MailBag Question End -->
            <div class="content_blnk">
             	<div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/techstrat/techStrat-performance.gif" alt="" /></div>
             	<div class="grey_bg">
					<?=$performanceobj->displayTechPositionPerformanceRight();?>
				</div>
			</div>
            <div class="content_blnk">
                <div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/techstrat/search.gif" alt="" /></div>
                <div class="grey_bg">
                    <div class="searchpanel">
					<form name="frmSearch" id='frmSearch' action="<?=$HTPFX.$HTHOST.'/techstrat/search.htm';?>" method="get">
                    <input type="text" class="searchpanel" value="" name="q" id='q' onmouseover="enableclick();return true;" onblur="disableclick();return false;" /> <img src="<?=$IMG_SERVER;?>/images/techstrat/bttn_go.jpg" alt="" onclick="javascript:frmSearch.submit();"/>
					</form>
                    </div>
                </div>
            </div><!--Search End -->
            <div class="content_blnk">
                <?=$this->displayTickerMentioned();?>
            </div><!--Tickers Mentioned End -->
	<?
	}


	function displayTickerMentioned(){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	$objTechStartData= new techstartData("techstrat_posts","");
	$getResult=$objTechStartData->getTickerMentioned();
	/*$getResult[]=shuffle($getResult);*/
	if($getResult){
	?>
	<div class="rght_heading"><img src="<?=$IMG_SERVER;?>/images/techstrat/tickersmentioned.gif" alt="" /></div>
       <div class="grey_bg">
          <div class="tickers_mentioned">
                       <? foreach($getResult as $value){
							if(is_array($value)){
								echo '<a  href="'.$HTPFX.$HTHOST.'/techstrat/ticker/'.$value['ticker_id'].'" target="_self">'.strtoupper($value['stocksymbol']).' '.'('.$value['countticker'].')'.'</a><br />';
							}
						}
						?>

       </div>
    </div>
	<?
	}
	}
	function displaySearchTopicName($searchStr,$searchType)
	{
		global $HTPFX,$HTHOST;
		$objTechStartData= new techstartData("techstrat_posts","");
		$tagName		=	  $objTechStartData->getTagTickerName($searchType,$searchStr);
			if($searchType == 'tag')
			{
						$tagLabel	=	"Results for Tag :-&nbsp;".$tagName;
			}
			if($searchType == 'tid')
			{
						$tagLabel	=	"Results for Ticker :-&nbsp;".$tagName;
			}
			if($searchType == 'text')
			{
						$tagLabel	=	"Results for Search :-&nbsp;".$searchStr;
			}
	?>
	<? if($tagLabel!='') { ?><div class="showtagname"><?=$tagLabel;?></div><? }?>
	<?php
	}
	function displaySearchResult($searchStr,$searchType,$offset)
	{
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
		$objContent= new Content();
		global $HTPFX,$HTHOST,$IMG_SERVER;
		$objTechStartData= new techstartData("techstrat_posts","");
		if($searchType == 'text')
		{
		$searchResult = $objTechStartData->getTechStartSearch($searchStr,$searchType,$offset);
		}
		elseif($searchType == 'tag' or $searchType == 'tid')
		{
			$searchResult = $objTechStartData->getTechStartSearchByTopic($searchStr,$searchType,$offset);
		}

		if($searchResult && count($searchResult)>0)
		{
			foreach($searchResult as $key=>$value){

				//$value['body']=$objContent->getCountWords($value['body'],$count="100");
				$i=0;
				$url			=	  $objTechStartData->getPostUrl($value['id']);
				$tag			=	  $this->displayPostTags($value['id']);
				$value['body']=strip_tags($value['body'],'<img>');
				$description	=	  $objContent->getCountWords($value['body'],$count="100");
				// $categoryName	=	  $objTechStartData->getCategoryName($value['id']);
				if(!$value['category_id'] || $value['category_id']==""){
				    $value['category_id']=$objTechStartData->getCategoryByPostId($value['id']);
					$catName=$this->displayCategoryName($value['category_id']);
				}else{
					$catName=$this->displayCategoryName($value['category_id']);
				}

				if($key=="0"){
			?>
				<div class="first_postarea">
				<?php
				$this->displaySearchTopicName($searchStr,$searchType);
				?>
					<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
					<div class="postheading"><a href="<?=$HTPFX.$HTHOST.$url;?>"><?=$value['title'];?></a></div>
						<div class="post_tags">
							<? if($catName!='') { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
						 <? if($tag) {?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
						</div>
      					<div class="postcontainer"><?=$description;?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$url;?>" target="_self"><?=strtoupper('Read More');?>....</a></span></div>
					</div><!--END of 1st Post -->
         <? } else { ?>
					<div class="postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($value['publish_date']));?></div>
						<div class="postheading"><a href="<?=$HTPFX.$HTHOST.$url;?>"><?=$value['title'];?></a></div>
						<div class="post_tags">
							<? if($catName!='') { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
						 <? if($tag) {?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
						</div>
                      	<div class="postcontainer"><?=$description;?><span class="read_more"><a href="<?=$HTPFX.$HTHOST.$url;?>" target="_self"><?=strtoupper('Read More');?>....</a></span></div>
					</div>
			<?
			}
	   	}
	   }
	   else
		{
				echo "<div class='errormsg'>No Result Found!</div>";
		}
	}
	function displayPostDetail($id)
	{
		global $HTPFX,$HTHOST,$IMG_SERVER;
		$objTechStartData= new techstartData("techstrat_posts","");
		$searchResult = $objTechStartData->getPostData($id);
		if($searchResult && count($searchResult)>0)
		{
				$tag	=	$this->displayPostTags($id);
			//	$categoryName	=	  $objTechStartData->getCategoryName($id);

				$catName=$this->displayCategoryName($searchResult['category_id']);
				$position		=	$searchResult['position'];
				?>
					<div class="first_postarea">
						<div class="posttime"><?=date("M j, Y g:i A",strtotime($searchResult['publish_date']));?> &nbsp;&nbsp;
						<a style="text-decoration: none; color: rgb(8, 61, 112);" target="_self" href="javascript:techstratprint(<?=$id;?>);">
						<img hspace="5" height="14" border="0" width="16" alt="Print" src="<?=$IMG_SERVER;?>/images/icons/print-icon.gif" alt="Print">&nbsp;<span style="text-transform:capitalize;">Print</span></a></div>
						<div class="postheading"><?=$searchResult['title'];?></div>
						<div class="post_tags">
							<? if($catName!='') { ?>
							<div class="catLink">Category: <span><?=$catName;?></span></div>
							<? }?>
						 <? if($tag) {?>
							<div class="catLink">Tags: <span><?=$tag;?></span></div>
                        <? } ?>
						</div>
      					<div class="postcontainer"><?=$searchResult['body'];?></div>
						<? if($position!='') { ?>
						<div class="position_text"><?=$position;?></div>
						<? }?>
					</div><!--END of 1st Post -->

		<?php
	   }
	   else
		{
			echo "<div class='errormsg'>No Result Found!</div>";
		}
	}
	function displayPostTags($id)
	{
	    global $IMG_SERVER,$HTPFX,$HTHOST;
		$objTechStartData= new techstartData("techstrat_posts","");
		$getTags		=	$objTechStartData->getTechStartTopics($id);
		$tickerValue	=	$objTechStartData->getTechStartTicker($id);
			/*if($getTags!='')
			{
					$tagValue   	=   explode(',',$getTags);
			}*/
			$i=0;
			$tag="";
			if(is_array($getTags)){
				foreach($getTags as $row){

						if($i==0){
							$tag='<a class="post_tags" href="'.$HTPFX.$HTHOST.'/techstrat/tag'.trim($row['tagurl']).'">'.ucfirst($row['tagname']).'</a>';
						}else{
							$tag.=','.' <a class="post_tags" href="'.$HTPFX.$HTHOST.'/techstrat/tag'.trim($row['tagurl']).'">'.ucfirst($row['tagname']).'</a>';
						}
						$i++;

				}
			}

			if(is_array($tickerValue))
			{
				foreach($tickerValue as $key=>$value)
				{
						if($i==0){
							$tag='<a class="post_tags" href="'.$HTPFX.$HTHOST.'/techstrat/ticker/'.trim($value['id']).'">'.strtoupper($value['stocksymbol']).'</a>';
						}else{
							$tag.=','.' <a class="post_tags" href="'.$HTPFX.$HTHOST.'/techstrat/ticker/'.trim($value['id']).'">'.strtoupper($value['stocksymbol']).'</a>';
						}
						$i++;
				}
			}
			if($tag!='')
			{
				return $tag;
			}
			else
			{
				return false;
			}
	}

	/*************************************** Performance Page ******************************************/
	function displayPerformance()
	{
		global $HTPFX,$HTHOST;
		$objTechStartData= new techstartData("techstrat_posts","");
		//$getResult=$objTechStartData->getTechStartPostsData($categoryName,$offSet,$techStartPostLimit);
		//$tagValue=array();
		?>
		<div class="performance_postarea">
						<div class="postcontainer"><span id="embed_object"></span></div>
						<div style="clear:both;"></div>

					</div>

	<?php
	}

function displayPagination($catName,$p)				/******************  Pagination Code ************************/
	{
		global $techStartPostLimit;
		$objTechStartData= new techstartData("techstrat_posts","");
		$numPost=$objTechStartData->getTechStartPostCount($catName);
		?>
		<div>
		<? 	$contentCount=$techStartPostLimit;
			$this->showTechStartPagination($numPost,$contentCount,$p,$catName);
		?>
		</div>
	<? }

	function showTechStartPagination($numrows,$contentcount,$p,$catName){
	/*pagination start here*/
		global $HTPFX,$HTHOST;
	if($catName=="Trades & Ideas"){
	 	$url= $HTPFX.$HTHOST."/techstrat/trades-ideas/";
	}elseif($catName=="Mailbag"){
	 	$url= $HTPFX.$HTHOST."/techstrat/mailbag/";
	}elseif($catName=="Research Tank"){
	 	$url= $HTPFX.$HTHOST."/techstrat/research-tank/";
	}else{
	 	$url= $HTPFX.$HTHOST."/techstrat/all-posts/";
	}


		if($numrows>$contentcount) {
				 $totalRows = $countnum=ceil(($numrows/$contentcount));
		?>
		<div class="pagination">
			<?php
				if(!$p=="0")
				{
					$j=$p-1;
					if($j<1){
					?>
					<a  href="<?=$url?>">&lt; Previous Page</a>
				<?php }else{ ?>
					<a  href="<?=$url?>p/<?=$j?>">&lt; Previous Page</a>
				 <? }
				}
				$p=$p+1;
				 if($numrows>(($p)*$contentcount)){  ?>
				<span class="nxt"><a target="_parent" href="<?=$url?>p/<?=$p?>">Next Page &gt;</a></span>
				   <?php }
			?>
		</div>
		<?php  }  // for end
	}

	function displayProductLandingLeft(){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
    	<div id="dealarticle-left">
			<!-- Body content will display here -->
            <div class="NL_heading"><span class="techstartrpt">The TechStrat Report</span> <span class="wthtechstart">with Sean Udall</span><br />Join Sean Udall's new TechStrat Report newsletter with a FREE 14 Day Trial!</div><!--The TechStrat Report end -->
			<p class="dscrpt">Technology stocks are an integral and substantial portion of the market and market indices, providing an ever-changing landscape which creates an almost endless array of investment ideas and themes for both traders and investor alike, and Sean Udall has been covering the technology sector for X years! Members of Minyanville's Buzz &amp; Banter have been benefiting from Sean's calls for years, but now you can have regular, more in-depth access to his expertise to help you profit from some of the biggest and fastest moving stocks with a subscription to the TechStrat Report.</p>
            <h2 class="risk-free">With your risk-free 14 day trial you'll receive:</h2>
            <ul class="trial_detials">
                <li><strong>Trades &amp; Ideas:</strong> In-depth analysis and trade ideas on individual tech stocks poised for big moves, including entry points, targets and all follow-up trades. This includes Sean's longer-term key investment themes as well as shorter term technical trades and swing trades. Fully archived and organized by ticker to make what you're looking for easy to find.</li>
                <li><strong>Mailbag:</strong> Exclusive access to Sean's thoughts on what you want to know. Looking for more analysis on something Sean hasn't covered in Trades &amp; Ideas?  Just ask and he'll regularly reply in the Mailbag section.</li>
                <li><strong>Research Tank:</strong> Institutional quality analysis and valuation work on key technology stocks. Sean scours research commentary, industry publications and news universe of technology stocks and archives what's important in an easy to follow format. He does work so you don't have to, finding important drivers for individual stocks and the sector as a whole.</li>
                <li><strong>Positions &amp; Performance:</strong>  Full transparency of Sean's current open positions as well as closed positions and past performance.
Email alerts with every new post so you don't miss any key trades, analysis or important market moves.</li>
            </ul>
            <div class="section_14day_trial">
			<? $trialText="14 day FREE trial"; ?>
            	<div class="start14days">Start my <span class="redtxt"><?=getTechStartAddtoCartbtnsTrial("TechStart",$trialText,"subscription","subTechStart",$eventname="Subscription");?></span> today to begin receiving the <span class="greytxt">TechStrat Report by Sean Udall.</span></div>
                <div class="img_freetrial">
                	<?=getTechStartAddtoCartbtnsMonthly("TechStart","89_aftrfreetrial.gif","subscription","subTechStart",$eventname="Subscription");?>
                	<?=getTechStartAddtoCartbtnQuarter("TechStart","229_aftrfreetrial.gif","subscription","subTechStart",$eventname="Subscription");?>
                	<?=getTechStartAddtoCartbtnAnnual("TechStart","749_aftrfreetrial.gif","subscription","subTechStart",$eventname="Subscription");?>
                </div><!--Start Free Trials end -->
                <div class="alrtmsg">I understand that my credit card will not be charged until after my two week<br />free trial but will go into the subscription I choose above unless<br />I cancel within the trial period.<br /><br />
                <span class="qusts_conat">Questions? <br />Contact us at <a href="mailto:support@minyanville.com">support@minyanville.com</a> or 212-991-9357</span>
                </div><!--Trial msg end -->

            </div> <!--14 Days Free Trial end -->

		</div> <!-- Left side End -->
	<?
	}

	function displayProductLandingRight(){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
    	<div id="rightfreetrial_panel" class="konafilter">
        	<div class="freetrial_sec">
            	<div class="yestostart"><img src="<?=$IMG_SERVER;?>/images/techstrat/yestostrat.gif" width="315" height="45" alt=""/></div> <!--start my free trial today end -->
            	<div class="subscription_msg">I'd like to take a free trial and continue with the following subscription unless I choose to cancel during my 14 day trial period.</div>
                <div class="deals"> <!--Deal Subscription message end -->
                	<div class="gooddeals"><h2><img src="<?=$IMG_SERVER;?>/images/techstrat/gooddeal.gif" width="99" height="20" alt=""/></h2>24/7 Access to all TechStrat<br />Flexibility to Discontinue monthly</div>
                    <div class="deals_subscription">
                    <?=getTechStartAddtoCartbtnsMonthly("TechStart","89_rghtfreetrial.gif","subscription","subTechStart",$eventname="Subscription");?>
                    </div> <!--89 end -->
                </div> <!-- Good Deal Section end  -->
                <div class="clr"></div>
                <div class="deals">
                	<div class="gooddeals"><h2><img src="<?=$IMG_SERVER;?>/images/techstrat/greatdeal.gif" width="99" height="19" alt=""/></h2>24/7 Access to all TechStrat<br />15% off monthly price</div>
                    <div class="deals_subscription">

                    <?=getTechStartAddtoCartbtnQuarter("TechStart","229_rghtfreetrial.gif","subscription","subTechStart",$eventname="Subscription");?>

                    </div> <!--229 end -->
                </div> <!-- Great Deal Section end  -->
                <div class="clr"></div>
                <div class="deals">
                	<div class="gooddeals"><h2><img src="<?=$IMG_SERVER;?>/images/techstrat/bestdeal.gif" width="99" height="20" alt=""/></h2>24/7 Access to all TechStrat<br />30% off monthly price</div>
                    <div class="deals_subscription">
                     <?=getTechStartAddtoCartbtnAnnual("TechStart","749_rghtfreetrail.gif","subscription","subTechStart",$eventname="Subscription");?></div>
                     <!--749 end -->
                </div> <!-- Best Deal Section end  -->
            </div> <!--Free Trail end -->
        	<div class="seannote">
            	<h2>A Note from Sean</h2>If you trade, you need and want beta, broad trading ranges and sustainable trends. The tech space nearly always delivers this criteria in abundance and that's why I've focused much of my career on tech.  Since tech is about innovation and creative destruction you nearly always have new secular growth trends, lots of uncertainty about the future and huge disparity between winners and losers, which creates tremendous opportunities for those who feel they can create an edge over the masses.  I feel I can create that edge and look forward to sharing it with subscribers through the TechStrat Report.<br /><br />Best Regards,<br /><br /><em>Sean Udall<br />Author, TechStrat Report</em>
</div><!--sean note end -->
        </div><!--Rightside End -->
	<?
	}

function displaySearchPagination($q, $type, $p)
	{
		global $techStartItemsLimit;
		$objTechStartData= new techstartData("techstrat_posts","");
		$numPost=$objTechStartData->getTechStartSearchCount($q,$type);
		?>
		<div>
		<? 	$contentCount=$techStartItemsLimit;
			$this->showSearchUdallPagination($numPost,$contentCount,$p,$q,$type);
		?>
		</div>
 <? }

	function showSearchUdallPagination($numrows,$contentcount,$p,$wordtoSearch,$searchType){
	/*pagination start here*/
		global $HTPFX,$HTHOST;
		if($searchType=='text'){
			$url= $HTPFX.$HTHOST."/techstrat/search.htm?q=".$wordtoSearch;
	 	}elseif($searchType=='tag'){
			$url= $HTPFX.$HTHOST."/techstrat/tag/".$wordtoSearch."/";
		}elseif($searchType=='tid'){
			$url= $HTPFX.$HTHOST."/techstrat/ticker/".$wordtoSearch."/";
		}


		if($numrows>$contentcount) {
				 $totalRows = $countnum=ceil(($numrows/$contentcount));
		?>
		<div class="pagination">
			<?php
				if(!$p=="0")
				{
					$j=$p-1;
					if($j<1){
					?>
					<a  href="<?=$url?>">&lt; Previous Page</a>
				<?php }else{
						if($searchType=='text'){ ?>
							<a  href="<?=$url?>&p=<?=$j?>">&lt; Previous Page</a>
					 <?	}else { ?>
					 		<a  href="<?=$url?>p/<?=$j?>">&lt; Previous Page</a>
					<?	}
				  }
				}
				$p=$p+1;
				 if($numrows>(($p)*$contentcount)){
				 	if($searchType=='text'){ ?>
						<span class="nxt"><a target="_parent" href="<?=$url?>&p=<?=$p?>">Next Page &gt;</a></span>
					<? }else { ?>
						<span class="nxt"><a target="_parent" href="<?=$url?>p/<?=$p?>">Next Page &gt;</a></span>
				<?php	}
				    }
			?>
		</div>
		<?php  }  // for end
	}


	function displayProductLandingLeftV2($arPD){
	   global $IMG_SERVER,$HTPFX,$HTHOST,$HTPFXSSL;
	 ?>
		<div id="top_techstratsectionV2">
           <div class="techstratsean_headingV2">
           <img src="<?=$IMG_SERVER;?>/images/techstrat/techstrat_mainhead.gif" />
           <!--<span class="txttechV2">Tech</span><span class="txtstratV2">Strat</span><span class="wthseanudallV2">with Sean Udall</span><br />Sean Udall's New <span class="txtred_techV2">TechStrat</span> Report Will Make Winning Technology<br />Stocks An Integral Part of Your Portfolio -->
           </div><!--The TechStrat Report end -->

        	<div class="strip14dysV2"><?=getTechStartAddtoCartbtnsMonthly("TechStrat","startfree14daytrial_strip.jpg","subscription","subTechStrat",$eventname="Subscription");?></div><!--Strip 14 Days Trial end -->
            <div class="seanphotoV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/seanudall-home.jpg" width="181" height="152" alt=""/>	</div><!--Sean Photo end -->

        </div>

		<div id="dealarticle-leftV2">
			<!-- Body content will display here -->
			<p class="dscrptV2">Technology stocks are an integral and substantial portion of the market and market indices, providing an ever-changing landscape which creates an almost endless array of investment ideas and themes for both traders and investor alike, and Sean Udall has been covering the technology sector for 20 years!<br /><br />Members of Minyanville's Buzz &amp; Banter have been benefiting from Sean's calls for years, but now you can have regular, more in-depth access to his expertise to help you profit from some of the biggest and fastest moving stocks with a subscription to the TechStrat Report.
</p>
            <h2 class="risk-freeV2">With your risk-free 14 day trial you'll receive:</h2>
            <ul class="trial_detialsV2">
                <li><div class="dealiconV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/icon_graph.gif" width="39" height="34" alt=""/></div>
                <div class="deal_dscptV2"><strong>Trades &amp; Ideas:</strong> In-depth analysis and trade ideas on individual tech stocks poised for big moves, including entry points, targets and all follow-up trades. This includes Sean's longer-term key investment themes as well as shorter term technical trades and swing trades. Fully archived and organized by ticker to make what you're looking for easy to find.</div></li>
                <li><div class="dealiconV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/icon_letter.gif" width="48" height="42" alt=""/></div>
                <div class="deal_dscptV2"><strong>Mailbag:</strong> Exclusive access to Sean's thoughts on what you want to know. Looking for more analysis on something Sean hasn't covered in Trades &amp; Ideas?  Just ask and he'll regularly reply in the Mailbag section.</div></li>
                <li><div class="dealiconV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/icon_brain.gif" width="51" height="51" alt=""/></div>
                <div class="deal_dscptV2"><strong>Research Tank:</strong> Institutional quality analysis and valuation work on key technology stocks. Sean scours research commentary, industry publications and news universe of technology stocks and archives what's important in an easy to follow format. He does work so you don't have to, finding important drivers for individual stocks and the sector as a whole.</div></li>
                <li><div class="dealiconV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/icon_arrows.gif" width="33" height="43" alt=""/></div>
                <div class="deal_dscptV2"><strong>Positions &amp; Performance:</strong>  Full transparency of Sean's current open positions as well as closed positions and past performance. Email alerts with every new post so you don't miss any key trades, analysis or important market moves.</div></li>
            </ul>
            <div class="section_14day_trialV2">
			<? $trialText="14 day FREE trial"; ?>

            	<div class="start14daysV2">
                <img src="<?=$IMG_SERVER;?>/images/techstrat/strat14day_txt.gif" />
                <!--Start my <span class="redtxtV2"><?=getTechStartAddtoCartbtnsTrial("TechStrat",$trialText,"subscription","subTechStrat",$eventname="Subscription");?></span> today to begin<br />receiving the <span class="greytxtV2">TECHSTRAT Report by Sean Udall.</span>-->

                </div>
                <div class="img_freetrialV2">
                	<a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register" onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
                    <img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/subscription/ts_free.jpg" class="freeimg" alt="">
                    </a>
                </div><!--Start Free Trials end -->
                <div class="alrtmsgV2">I understand that my credit card will not be charged until after my two week<br />free trial but will go into the subscription I choose above unless<br />I cancel within the trial period.<br /><br />
                <span class="qusts_conatV2">Questions? <br />Contact us at <a href="mailto:support@minyanville.com">support@minyanville.com</a> or 212-991-9357</span>
                </div><!--Trial msg end -->

            </div> <!--14 Days Free Trial end -->

		</div>
 <?	}

	function displayProductLandingRightV2($arPD){
	global $IMG_SERVER,$HTPFX,$HTPFXSSL,$HTHOST;
	?>
		<div id="rightfreetrial_panelV2" class="konafilterV2">
        	<div class="freetrial_secV2">
            	<div class="yestostartV2"><img src="<?=$IMG_SERVER;?>/images/techstrat/yestostratV2.gif" width="344" height="47" alt=""/></div> <!--start my free trial today end -->
            	<div class="subscription_msgV2">I'd like to take a free trial and continue with the following subscription unless I choose to cancel during my 14 day trial period.</div>
                <div>
                <a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register" onclick="return checkcart('<?=$arPD['subscription_def_id']?>','<?=$arPD['oc_id']?>','<?=$arPD['orderItemType']?>','<?=$arPD['product_name']?>','<?=$arPD['product_type']?>','<?=$arPD['event_name']?>');">
                <img style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/subscription/ts_14_free.jpg" class="freeimg" alt="" >
                </a>
                </div>

            </div> <!--Free Trail end -->
        	<div class="seannoteV2">
            	<h2>A Note from Sean</h2>If you trade, you need and want beta, broad trading ranges and sustainable trends. The tech space nearly always delivers this criteria in abundance and that's why I've focused much of my career on tech.  Since tech is about innovation and creative destruction you nearly always have new secular growth trends, lots of uncertainty about the future and huge disparity between winners and losers, which creates tremendous opportunities for those who feel they can create an edge over the masses.  I feel I can create that edge and look forward to sharing it with subscribers through the TechStrat Report.<br /><br />Best Regards,<br /><br /><em>Sean Udall<br />Author, TechStrat Report</em>
</div><!--sean note end -->
        </div>
 <?	}

	function displayNoPostText($catName){
	global $noPostText, $noPostMailbagText;
		?>
		<div class="noPost">
		<? if($catName=="Mailbag") {
				echo $noPostMailbagText;
			}elseif($catName=="Research Tank" || $catName=="Trades & Ideas" || $catName=="Positions and Performance") {
				echo $noPostText.$catName.'.';
			} ?>

		</div>
	<? }

	function displayCategoryName($valueCategoryId){
	    $objTechStartData= new techstartData("techstrat_posts","");
		$catResult=$objTechStartData->getMultipleCategoryName($valueCategoryId);

		foreach($catResult as $keyval=>$catValue){
			switch($catValue['category_name']){
				case 'Trades & Ideas':
			$url= $HTPFX.$HTHOST."/techstrat/trades-ideas/";
					break;
				case 'Mailbag':
			$url= $HTPFX.$HTHOST."/techstrat/mailbag/";
					break;
				case 'Research Tank':
			$url= $HTPFX.$HTHOST."/techstrat/research-tank/";
					break;
				default:
			$url= $HTPFX.$HTHOST."/techstrat/all-posts/";
					break;
		}
			if($keyval=="0"){
				$catName='<a style="color:#002E5A;" href="'.$url.'">'.ucwords($catValue['category_name']).'</a>';
			}else{
				$catName.=','.' <a style="color:#002E5A;" href="'.$url.'">'.ucwords($catValue['category_name']).'</a>';
			}
		}
		return $catName;
	}

}

?>