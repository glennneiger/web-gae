<?php
global $IMG_SERVER,$HTPFX,$HTHOST;
class dailyfeedViewer
{

function dailyfeedMiddleHead($topic,$source,$textval,$metatext)
{
global $dailyfeedhottopiccountmain;
$objCache= new Cache();
if($topic){
	$topicval="TAGS";
}else{
   $topicval="HOT TAGS";
}
	$hottopic=$objCache->getDailyfeedHotTopicsCache($dailyfeedhottopiccountmain);
?>

<div class="middle-heading-rss"> </div>
<div class="middle-heading"> <span >
  <?=$topicval;?>
  :</span>&nbsp;&nbsp; <span class="middle-heading-right">
 <? foreach($hottopic as $row){ ?>
  <span class="hot-tags"><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row['tagurl']);?>">
  <?=strtoupper($row['tagname']);?>
  </a></span>
 <? } ?>
  </span> </div>
 <?
 if($source)
 {
 	 $this->displayTextTags($textval,$metatext,'source');
 }
 elseif($topic)
 {
	 $this->displayTextTags($textval,$metatext,'topic');
 }
?>
 <div id="TD_ad" style="margin-left:450px;">
	 <?
	  if(!$_SESSION['AdsFree'] && $val == 0) { ?>
	<script language="javascript">
        CM8ShowAd("Button_160x30")
    </script>
    <? } ?>
</div>
<div class="page_heading">
  <?=strtoupper(date("F d, Y"));?>
</div>
<?php
}	//------------- Function dailyfeedMiddleHead Ends ---------------


function showHotTopic(){
global $dailyfeedhottopiccountmain;
$objCache= new Cache();
$hottopic=$objCache->getDFHotTopics();
$i=0;
?>
<div class="middle-heading"> <span > HOT TAGS:</span>&nbsp;&nbsp; <span class="middle-heading-right">
	<? foreach($hottopic as $row){
		if($i<$dailyfeedhottopiccountmain){
		?>
	 <!--<span class="hot-tags"><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tag/'.str_replace(" ","-",trim($row['tagname']));?>"><?=ucwords($row['tagname']);?></a></span>-->
  <span class="hot-tags"><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row['tagurl']);?>">
  <?=strtoupper($row['tagname']);?>
  </a></span>
 <? }
 $i++;
	} ?>
  </span> </span> </div>
 <div style="clear:both;"></div>
<?
}
function dailyfeedContent($feedDataArr)
{
 global $HTPFX,$HTHOST,$IMG_SERVER,$show_df_news_signup,$df_item_id,$D_R;
include_once($D_R."/lib/_content_data_lib.php");
 $objDailyfeed = new Dailyfeed("daily_feed","");
 $objMemcache= new Cache();
 $objContent = new Content("daily_feed","");

 if($feedDataArr!='')
 {

  $feedcache= new Cache();

  foreach($feedDataArr as $val=>$feedData)
  {

  	$feedId			=	$feedData['id'];
	$feedTitle		=	trim(mswordReplaceSpecialChars($feedData['title']));
	$display_date = date('F j, Y h:i A',strtotime($feedData['display_date']));
	$contributorName	=	trim($feedData['contributor']);
	$contributorId		=	trim($feedData['ContId']);
	$feed_excerpt		=	 trim(mswordReplaceSpecialChars(strip_tags($feedData['excerpt'])));
	if($feed_excerpt){
		$feed_body 			=	$feed_excerpt;
	}
	else{
		$feed_body 			=	strip_tags(htmlentities(mswordReplaceSpecialChars(strip_tags(substr($feedData['body'],0,400)))),ENT_QUOTES);
	}
	$arQuickTitle	    =	$objMemcache->getQuickTitleDailyFeedCache($feedId,$df_item_id);
	$quickTitle			=	$arQuickTitle['quick_title'];
	$arImage			=	$objMemcache->getImageDailyFeedCache($feedId);
	$imageURL	=	$arImage['url'];
	$feed_is_live		=	$feedData->is_live;

	$tickerArr = $feedcache->getTickersCache($feedId,'18',$feed_is_live);
	$tickers = $feedcache->getTickerListCache($feedId,'18',$tickerArr,$feedTitle,$feed_is_live);
	$urltitle=$objMemcache->getDailyFeedUrlCache($feedId);
	$fullUrl=$HTPFX.$HTHOST.$urltitle;
	if(substr_count($feed_body,"{FLIKE}") > 0)
	{
		$feed_body = str_replace("{FLIKE}","", $feed_body);
	}
	$feed_body =    $objMemcache->buzzAdReplace($feed_body);
	$sourceDailyFeed=$objDailyfeed->getResource($feedId,"18");
	$sourceDailyFeedUrl=$HTPFX.$HTHOST."/mvpremium/source".$sourceDailyFeed['url'];
	if($feedData['layout_type'] == 'dailyfeed')
	{
?>
<div class="content-area">
  <div class="feed-post-main">
    <div class="middle-main-heading">
      <h2><a href="<?=$fullUrl;?>" target="_self"><?=$feedTitle;?></a></h2>
    </div>
    <div class="feed-post-container">
      <div class="quick-title">
	<?php if ($quickTitle!='') {
		$urlquicktitle=$objContent->getFirstFiveWords($quickTitle);
			?>
        <div class="quicktitle">
          <?=strtoupper(trim($quickTitle));?>
        </div>
        <? } ?>
	<?php
	if($imageURL !='')
	{ ?>
		<a href="<?=$fullUrl;?>" target="_self" class="content-area-image"><img src="<?=$imageURL;?>" border="0" width="140px" /></a>
<?php } ?>
</div>
      <div class="submitted-by">
        <div class="dalyfd_artcltim">By <a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self">
          <?=$contributorName;?>
          </a>&nbsp;&nbsp;
          <?=$display_date;?>
        </div>
        <p class="excerpt_body">
          <?=$feed_body;?>
        </p>
        <p class="read_more"><span><a href="<?=$fullUrl;?>" target="_self">Read More</a></span><span class="read_more_arr">&nbsp;&raquo;</span></p>
</div>
</div>
    <div class="clr"></div>
    <div class="soure-area">
      <div class="dfLandingSource">

		<div class="df-source" >
          <div class="sourcedotted"></div>
          Source
		 </div>
        <div class="clr"></div>
        <?php if($sourceDailyFeed['source']) { ?>
        <div class="df-source-text"><a href="<?=$sourceDailyFeedUrl;?>">
          <?=$sourceDailyFeed['source'];?>
          </a></div>
        <?php } ?>
      </div>
<div id="share_icons" class="topic-area">
        <div class="df-comment-landing">
          <div class="cmmttxt">
            <?php
            	$objArticle			=   new articleViewer();
            	$comment_num = $objArticle->fbcommentNum($fullUrl);
            	if($comment_num<1)
            	{
            		echo "0";
            	}
            	else {
            		echo $comment_num;
            	}
            	?>
          </div>
          <a onclick="goToComments();" href="<?=$fullUrl?>#fb-comments">
          <div class="cmmt-bttn">Comment</div>
          </a> </div>

       <div class="dfLandingShare">
          <ul>
            <li id="social_icon_twitter_horizon" style="margin: 9px 9px 0 0;"><span class='st_twitter_hcount' displayText='Tweet' st_title = "<?=$feedTitle.$tickers;?>" st_url="<?=$fullUrl;?>" st_via="minyanville"></span></li>
			<li id="social_icon_fb_horizon" style="margin: 9px 9px 0 0;"><span class='st_facebook_hcount' displayText='Facebook' st_title="<?=$feedTitle;?>" st_url="<?=$fullUrl?>"></span></li>
			<li id="social_icon_google_horizon" style=" margin: 2px 7px 0 0;"><span class='st_googleplus_large' displayText='Google +1' st_title="<?=$feedTitle;?>" st_url="<?=$fullUrl?>"></span></li>
			<li id="social_icon_in_horizon" style=" margin: 2px 7px 0 0;"><span class='st_linkedin_large' displayText='LinkedIn' st_title="<?=$feedTitle;?>" st_url="<?=$fullUrl?>"></span></li>
			<li id="social_icon_st_horizon" style=" margin: 2px 7px 0 0;"><span class='st_sharethis_large' displayText='ShareThis' st_title="<?=$feedTitle;?>" st_url="<?=$fullUrl?>"></span></li>
			<li id="social_icon_email_horizon" style=" margin: 2px 7px 0 0;"><span class='st_email_large' displayText='Email' st_title="<?=$feedTitle;?>" st_url="<?=$fullUrl?>"></span></li>
			<li id="social_icon_rss_horizon" style=" margin: 2px 7px 0 0;"><a href="<?=$HTPFX.$HTHOST?>/service/rss.htm" target="_blank"><img src="<?=$IMG_SERVER;?>/images/articles/RSS_icon_32X32.png"></a></li>

          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
	}
  	elseif($feedData['layout_type'] == 'thestreet')
	{
		if(stristr($quickTitle,'TheStreet.com'))
		{
			$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_thestreetlogo.gif";
		}
		elseif(stristr($quickTitle,	'RealMoney.com'))
		{
			$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_Realmoneylogo.gif";
		}
		elseif(stristr($quickTitle,'MainStreet.com'))
		{
			$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_mainStreetlogo.gif";
		}
?>
    <div class="content-area">
      <div class="feed-post-main-gray">
        <div class="middle-main-heading">
          <h2><a href="<?=$fullUrl;?>" target="_self">
        <?=$feedTitle;?>
        </a></h2>
    </div>
    <div  class="feed-post-container">
      <div class="quick-title">
	<?php if ($quickTitle!='') {
		$urlquicktitle=$objContent->getFirstFiveWords($quickTitle);
			?>
        <div class="quicktitle">
          <?=strtoupper(trim($quickTitle));?>
        </div>
        <? } ?>
	<?php
		if($imageURL!='')
		{ ?>
        <a href="<?=$fullUrl;?>" target="_self" class="content-area-image"><img src="<?=$imageURL;?>" border="0" width="142px" /></a>
        <?php } ?>
      </div>
      <div class="submitted-by">
        <div class="dalyfd_artcltim">By <a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self">
          <?=$contributorName;?>
          </a>&nbsp;&nbsp;
          <?=$display_date;?>
        </div>
        <p class="excerpt_body">
          <?=$feed_body;?>
        </p>
        <p class="read_more"><span><a href="<?=$fullUrl;?>" target="_self">Read More</a></span><span class="read_more_arr">&nbsp;&raquo;</span></p>
        </div>
        </div>

	<div class="clr"></div>

    <div class="soure-area">
      <div class="dfLandingSource">
        <div class="df-source" >
          <div class="sourcedotted"></div>
          Source
		 </div>
		<div class="clr"></div>
        <?php if($sourceDailyFeed['source']) { ?>
        <div class="df-source-text"><a href="<?=$sourceDailyFeedUrl;?>">
          <?=$sourceDailyFeed['source'];?>
          </a></div>
        <?php } ?>
      </div>
<div id="share_icons" class="topic-area">
        <div class="df-comment-landing">
          <div class="cmmttxt">
            	<?php
            	$objArticle			=   new articleViewer();
            	echo $comment_num = $objArticle->fbcommentNum($fullUrl);  ?>
          </div>
          <a onclick="goToComments();" href="<?=$fullUrl?>#fb-comments">
          <div class="cmmt-bttn">Comment</div>
          </a> </div>
        <div class="dfLandingShare">
<ul>
            <li> <span class="st_sharethis_hcount" displayText="Share" st_url="<?=$fullUrl?>"></span> </li>
            <li id="social_icon_twitter_hori" style="padding-top:1px;"> <a href="http://twitter.com/shareurl?url=<?=urlencode($fullUrl);?>" data-text="<?=htmlentities($feedTitle);?>| The Daily Feed | Minyanville.com" class="twitter-share-button" data-url="<?=urlencode($fullUrl);?>" data-count="horizontal"></a> </li>
<li id="social_icon_flike_hori" style="padding-top:1px;">
              <iframe src="http://www.facebook.com/plugins/like.php?href=<?=urlencode($fullUrl);?>&amp;layout=button_count&amp;show_faces=false&amp;width=86&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:60px; height:21px;vertical-align:middle;" allowTransparency="true"></iframe>
            </li>
            <li style="width:60px;">
              <g:plusone  size="medium"></g:plusone>
</li>
</ul>
</div>
      </div>
    </div>
  </div>
</div>
	<?
	} // end else
	?>
<script type="text/javascript">
      window.___gcfg = {
        lang: 'en-US'
      };

      (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
      })();
    </script>
	<?php
	if($val=='2' && !$_SESSION['AdsFree'])
	{
?>
<div class="content-area-advertisement">
<div class="advertisement_landing">Advertisement</div>
  <div class="dailyfeed_advertisement_area">
    <? CM8_ShowAd('468x60'); ?>
  </div>
</div>
<?php
	}

  } // End Foreach
 }   //--------------- If block ends ----------
}	 //------------- Function dailyfeedContent Ends ---------------

function dailyfeedCommentBlock($feedId,&$feedcache)
{
	$qry="select id from ex_thread where content_table='daily_feed' and content_id='".$feedId."'";
	$result=exec_query($qry,1);
	if($result['id']){
	$discussionComments=$feedcache->getArticleDiscussionCache($result['id'],$articleapprove=1);
	 if(!is_object($discussionarticle)){  // if memcache expires set cache again
		  $discussionComments=$feedcache->setArticleDiscussionCache($result['id'],$articleapprove=1);
	  }
	 $commentArray 	= $discussionComments->comments;
	 $commentTotal	= count($commentArray);
	 return $commentTotal;
	}else{
		return false;
	}
}
function dailyfeedSignUp()
{
?>
	<br />
	<div class="FeedSignUpSearch" style="text-align:left;">
	<?daily_feed_newsletter_alert_bottom();?>
	</div>
<?php
}
 //------------------------ Function detailFeedContent End -----------------

 function showDailyFeedPagination($offset,$numrows,$contentcount,$p,$shownum,$tag,$source,$cid,$tid,$category){
	/*pagination start here*/
	global $HTPFX,$HTHOST;
	if($tag){
		$chkTag = substr($tag,-1);
			if($chkTag != '/')
			{
				$tag	= $tag."/";
			}
	$url=$HTPFX.$HTHOST."/mvpremium/tag/".$tag;
	}elseif($source){
		$chkStr = substr($source,-1);
			if($chkStr != '/')
			{
				$source	= $source."/";
			}
		$url=$HTPFX.$HTHOST."/mvpremium/source/".$source;
	}elseif($cid){
		$url=$HTPFX.$HTHOST."/mvpremium/cid/".$cid."/";
	}elseif($tid){
		$url=$HTPFX.$HTHOST."/mvpremium/tid/".$tid."/";
	}elseif($category){
		$url=$HTPFX.$HTHOST."/mvpremium/category/".$category;
	}else{
		$url=$HTPFX.$HTHOST."/mvpremium/";
	}
	 if(!is_numeric($contrib_id)){
	 	$contrib_id="";
	 }
						if($numrows>$contentcount) {
								 $totalRows = $countnum=ceil(($numrows/$contentcount));
								 if(($shownum<$countnum) && ($offset+ $shownum < $countnum)){
								 $countnum=$shownum + ($p+1);

								 }
						?>
<div class="dailyfeed_pagination_new" >
  <div class="dailyfeed_pagination_left" >
							<?php
							if($p=='' || $p==0)
								{
								$p=1;
							}

								if($p==1){
								?>
    <span class="page-arr">&laquo;</span> <span  class="page-prev">Previous</span>
    <?php
								}else{
								?>
    <a target="_parent" href="<?php echo $url?>p/<?php echo $p-1?>"><span class="page-arr">&laquo;</span> <span  class="page-prev">Previous</span></a>
							<?php }


								$min=(floor($p/10)*10)+1;
								$max=(ceil($p/10))*10;

								if($p%10==0)
								{
									$min=((floor($p/10)-1)*10)+1;
							 }

								for($i=$min; $i<=$max; $i++)
								{
									$j=$i;

									if($i<$totalRows)
									{
										if($i==$p)
										{
										?>
    &nbsp;<span class="curr-page" ><?php echo $i;?></span>
    <?php
										}
										else
										{
										?>
    &nbsp;<a target="_parent" href="<?php echo $url?>p/<?php echo $i?>"><span ><?php echo $i;?></span></a>
    <?php
										}
								 ?>
    <span <?php if($i<$max && $i<$totalRows-1){?>class="line-border" <?php }?>>&nbsp;</span>
    <?php
									}
							  	}

								$next=$p+1;

								if($totalRows-1>$p)
								{
								?>
    <a target="_parent" href="<?php echo $url?>p/<?php echo $next?>"><span class="page-next">Next</span> <span class="page-arr">&raquo;</span></a>
							<?php	}
								else
								{
								?>
    <span class="page-next">Next</span> <span class="page-arr">&raquo;</span>
    <?php
							  }
							?>
  </div>
  <div class="dailyfeed_pagination_right">Page <?php echo $p;?> of <?php echo $totalRows-1;?></div>
</div>
<br />
<br />
<br />
<?php  }
	}

   function showTopSources(){
   	global $IMG_SERVER;
       $objCache= new Cache();
       $result=$objCache->getMostPopularDFSources();
   	?>
	<div class="article-right-module" style="margin-top:20px;">
  <div class="heading-right-box"><h3 class="new-head-right">TOP SOURCES</h3></div>
		<div class="display-product-module-shadow" >
			<div class="display_product_body">
				<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
				<table width="100%" border="0" cellpadding="4" cellspacing="4">
				<? foreach($result as $key=>$row){
				   if($row['id']!='')
				   {
				   	$key=$key + 1;
				 	if($key % 2) { ?>
			     <tr>
            <td><a href="<?=$HTPFX.$HTHOST.'/mvpremium/source'.trim($row['url']);?>" ><span style="color:#00509e;">
              <?=ucwords(trim($row['source']))?>
              </span></a></td>
				<? } else {?>
            <td><a href="<?=$HTPFX.$HTHOST.'/mvpremium/source'.trim($row['url']);?>"><span style="color:#00509e;">
              <?=ucwords(trim($row['source']))?>
              </span></a></td>
					</tr>
				<? }
				 }
				}
				?>
				<tr>
					<td colspan="2" ><a href="<?=$HTPFX.$HTHOST?>/mvpremium/list.htm?type=source"><span style="color:#00509e;">See all sources &raquo;</span></a></td>
				</tr>
				</table>
				</div>
			</div>
		</div>
	</div>
	<?
   }


   function showTopics(){
   global $dailyfeedhottopiccount,$IMG_SERVER;
   $objCache= new Cache();
   $result=$objCache->getDFHotTopics();
   ?>
<div class="article-right-module">
  <div class="heading-right-box"><h3 class="new-head-right">TOPICS</h3></div>
   	<div class="display-product-module-shadow" >
		<div class="display_product_body">
			<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
			<table width="100%" border="0" cellpadding="4" cellspacing="4">
				<? foreach($result as $key=>$row) {

				$key=$key + 1;
				  if($key % 2) { ?>
				     <tr>
            <td ><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row['tagurl']);?>" ><span style="color:#00509e;">
              <?=strtoupper($row['tagname'])?>
              </span></a></td>
				<? } else {?>
            <td><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row['tagurl']);?>"><span style="color:#00509e;">
              <?=strtoupper($row['tagname'])?>
              </span></a></td>
					</tr>
				<? }
				}
				?>
				<tr>
					<td colspan="2" ><a href="<?=$HTPFX.$HTHOST?>/mvpremium/list.htm?type=tag"><span style="color:#00509e;">See all tags &raquo;</span></a></td>
				</tr>
			</table>
			</div>
		</div>
	</div>
   </div>
   <?
   }

   function showContributor(){
   global $IMG_SERVER;
   $objCache= new Cache();
   $result=$objCache->getMostPopularDFCountributors();
   ?>
<div class="article-right-module">
  <div class="heading-right-box"><h3 class="new-head-right">CONTRIBUTORS</h3></div>
			<div class="display-product-module-shadow" >
				<div class="display_product_body">
					<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
					  <table width="100%" border="0" cellpadding="4" cellspacing="4">
					 <? foreach($result as $key=>$row) {
							$key=$key + 1;
							  if($key % 2) { ?>
								 <tr>
            <td ><a href="<?=$HTPFX.$HTHOST.'/mvpremium/cid/'.$row['contrib_id'];?>" ><span style="color:#00509e;">
              <?=ucwords($row['name'])?>
              </span></a></td>
							<? } else {?>
            <td><a href="<?=$HTPFX.$HTHOST.'/mvpremium/cid/'.$row['contrib_id'];?>"><span style="color:#00509e;">
              <?=ucwords($row['name'])?>
              </span></a></td>
								</tr>
							<? }
							}
							?>
							<tr>
								<td colspan="2" ><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm"><span style="color:#00509e;">See all contributors &raquo;</span></a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
   <?
   }


    function showPopularStocks(){
    global $IMG_SERVER;
    $objCache= new Cache();
    $result=$objCache->getMostPopularDFTickers();
   ?>
<div class="article-right-module" >
  <div class="heading-right-box"><h3 class="new-head-right">MOST POPULAR TICKERS</h3></div>
		<div class="article-right-module-header" style="margin-bottom:0px;"></div>
			<div class="display-product-module-shadow" >
				<div class="display_product_body">
					<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
					  <table width="100%" border="0" cellpadding="4" cellspacing="4">
					 <? foreach($result as $key=>$row) {
							$key=$key + 1;
							  if($key % 2) { ?>
								 <tr>
            <td ><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['ticker_id'];?>" ><span style="color:#00509e;">
              <?=strtoupper($row['stocksymbol'])?>
              </span></a></td>
							<? } else {?>
            <td><a href="<?=$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['ticker_id'];?>"><span style="color:#00509e;">
              <?=strtoupper($row['stocksymbol'])?>
              </span></a></td>
								</tr>
							<? }
							}
							?>
                            <tr>
                                <td colspan="2" ><a href="<?=$HTPFX.$HTHOST?>/mvpremium/list.htm?type=ticker"><span style="color:#00509e;">See all tickers &raquo;</span></a></td>
                            </tr>
						</table>
					</div>
				</div>
			</div>
		</div>
   <?
   }

   function showMostRead(){
   global $IMG_SERVER;
   $objCache=new Cache();
   $result=$objCache->getMostRead('18');
   ?>
<div class="article-right-module">
  <div class="heading-right-box"><h3 class="new-head-right">MOST READ</h3></div>
		<div class="display-product-module-shadow" >
			<div class="display_product_body">
				<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
				<table width="100%" border="0" cellpadding="4" cellspacing="4">
				<? foreach($result as $row) { ?>
				<tr>
            <td><a href="<?=$HTPFX.$HTHOST.$row['url'];?>"> <span style="color:#00509e;">
              <?=$row['title'];?>
              </span></a></td>
				</tr>
				<? } ?>
				</table>
			</div>
		</div>
	</div>
	</div>
   <?
   }

   function showMostRecentComment(){
   global $IMG_SERVER;
   		$objDailyfeed = new Dailyfeed();
		$result=$objDailyfeed->getMostRecentComment();
   ?>
   	<div class="article-right-module" style="margin-top:20px;">
  <div class="heading-right-box"> <img src="<?=$IMG_SERVER?>/assets/dailyfeed/most-recent.gif" alt="MOST RECENT COMMENTS" /> </div>
		<div class="display-product-module-shadow" >
			<div class="display_product_body">
				<div id="display_product_top" style="padding:0 11px 10px;font-size:11px; color:#00509e;">
					<table width="100%" border="0" cellpadding="4" cellspacing="4">
					   <?
					   if(is_array($result)){
					    foreach($result as $row) {
						$urltitle=$objDailyfeed->getDailyFeedUrl($row['content_id']);
						?>
						<tr>
            <td><span class="most-recent-box-heading">"
              <?=substr($row['teasure'],0,100);?>
              "</span> <br/>
              <span class="most-recent-box-subheading">From: </span><a href="<?=$HTPFX.$HTHOST.$urltitle.'?comment=1'?>"><span style="color:#00509e;">
              <?=$row['title'];?>
              </span></a></td>
						</tr>
						<? }
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
   <?
   }

   function showImageDFImap($topic=NULL,$source=NULL){
  	global $HTHOST,$HTPFX,$IMG_SERVER;
   if($topic)
   {

	$topic=str_replace("/","",$topic);
    $headerALTText = "The Daily Feed - ".strtoupper($topic);
   }
   else
   {
   	$headerALTText = "The Daily Feed";
   }
   if($topic){
        $topic=str_replace("/","",$topic);
   		$rssurl=$HTPFX.$HTHOST.'/rss/dailyfeed.rss?tag='.$topic;
   	}elseif($source){
	       $source=str_replace("/","",$source);
   			$rssurl=$HTPFX.$HTHOST.'/rss/dailyfeed.rss?source='.$source;
   	}else{
   			$rssurl=$HTPFX.$HTHOST.'/rss/dailyfeed.rss';
	}
   ?>
    <div class="dailyfeedlogo">
  <div class="dailyfeedlogoimage"> <a href="<?=$HTPFX.$HTHOST.'/mvpremium/';?>" target="_self"><img src="<?=$IMG_SERVER?>/images/DailyFeed/mvPremium_landing_banner.png" border="0" width="314" height="40" alt="<?=$headerALTText; ?>"/></a> </div>
  <div class="rsslogo"><a href="<?=$rssurl;?>"><img src="<?=$IMG_SERVER;?>/images/DailyFeed/dailyfeedrss.jpg"/></a></div>
</div>
   <?
   }

   function displayTextTags($textval,$metatext,$type){
       if($textval){
	   $metatext=str_replace("-"," ",$metatext);
	   $metatext=str_replace("/"," ",$metatext);
   ?>
<div class="content_text_tag"> <span>
  <?=$textval;?>
  :</span>&nbsp;&nbsp;
		 <? if ($type == 'source')  {
		 	 	echo ucwords($metatext);	}
			elseif($type=='topic') {
			 	echo strtoupper($metatext);	}
		 ?>
  		</div>
   <?
   	}
   }
   function detailStoryPageHeader($feedId)
   {
   	global $D_R;
	include_once($D_R."/lib/_content_data_lib.php");
   	$item_table = 'daily_feed';
	$pageName = "dailyfeed_article";
	$objContent 	= 	new Content($item_table,$feedId);
	$pageJS=array("config","registration","jquery","iboxregistration","creditcard","nav","redesign","scriptaculous",'dailyfeed','ajax','articleComment','Articles','zoomimage');
	$pageCSS=array("global","layout","rightColumn","nav","dailyfeed",'zoomimage');
	$show_canonical="1";
	include("../_header.htm");
   }
   function detailStoryContentBlock($feedId,$feedData,$commentTotal,$fullUrl)
   {

	$objDailyfeed 		=	new Dailyfeed();
	$bitlyUrl= $objDailyfeed->getDailyFeedBitlyUrl($feedId);
	if($feedData->layout_type == 'dailyfeed')
	{
	global $IMG_SERVER,$gaTrackingTicker,$cm8_ads_1x1_Text,$article_keywords;

   	$feedType			=	'18';
    $feedTitle			=	trim(mswordReplaceSpecialChars($feedData->title));
	$feed_date_time 	=	$feedData->updation_date;
	$feed_date 			= 	substr($feed_date_time,0,10);
	$display_date 		=   date('F j, Y h:i A',strtotime($feedData->display_date));
	$contributorName	=	trim($feedData->contributor);
	$contributorId		=	trim($feedData->ContId);
	$feed_excerpt		=	trim(htmlentities(mswordReplaceSpecialChars(strip_tags($feedData->excerpt))),ENT_QUOTES);
	$feed_body 			=	$feedData->body;
	$topicsURL="";
  	$topicslink="";
  	$df_gplus_link		=	$feedData->g_plus_link;
	$topicsURL			=	$objDailyfeed->getTopicsURL('daily_feed',$feedId);
	$getImage			=	$objDailyfeed->getImageDailyFeed($feedId);
	$quick_Title		=	$objDailyfeed->getQuickTitleDailyFeed($feedId,'18');
	$quickTitle			=	$quick_Title['quick_title'];
	$feedPosition		=	trim($feedData->position);
	$stock_tickers		=	$objDailyfeed->getTickersExchange($feedId,'18');
	$resource			=	$objDailyfeed->getResource($feedId,'18');
	$keywords			=	'';
	$feed_is_live		=	$feedData->is_live;

	if($source!='')
	{
		if($strKeywords=='')
		{
			$strKeywords	=	$source;
		}
		else
		{
			$strKeywords	=	$strKeywords.",".$source;
		}
	}
	if($stock_tickers!='')
	{
		if($strKeywords=='') { $strKeywords	=	$stock_tickers; }
		else  { $strKeywords	=	$strKeywords.",".$stock_tickers; }
	}

	if(count($getImage)>0)
	{ 	$imageURL	=	$getImage['url'];  } else  { $imageURL	=''; }

	if($topicsURL && count($topicsURL)>0)
	{
		$i=0;
		foreach($topicsURL as $key=>$row){
		if($i>0){
		$topicslink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}else{
		$topicslink.='<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}
		$i++;
	}
	}
	$stockval=array();
	$stocklink="";
	if(is_array($stock_tickers)){
		foreach($stock_tickers as $key=>$row){
			if($key>0){
				$stocklink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}else{
				$stocklink.=' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}
			$stocks[] = strtoupper($row['stocksymbol']);
		}
	}
	if(is_array($stocks))
	{
		$stocks = implode(",",$stocks);
	}
	$gaTrackingTicker = $stocks;

   ?>
<div class="middle-main-heading-detail" >
  <h1>
    <span itemprop="name" ><?=$feedTitle;?></span>
  </h1>
</div>
<meta itemprop="articleSection" content="mvPremium" />
<meta itemprop="keywords" content="<?=$article_keywords;?>" />
<div class="submitted-by-detail" style="float:left;width:470px;"> By <span itemprop="author" >
<? if(!empty($df_gplus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$df_gplus_link.'?rel=author">'.$contributorName.'</a></span>';
    }
    else {
?>
			<a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self">
			  <?=$contributorName;?>
			  </a></span>
<?  } ?>
&nbsp;&nbsp;
  <span itemprop="datePublished"><?=$display_date;?></span>
</div>
<div id="TD_ad">
	 <?
	  if(!$_SESSION['AdsFree'] && $val == 0) { ?>
	<script language="javascript">
        CM8ShowAd("Button_160x30")
    </script>
    <? } ?>
</div>
<div class="comment-heading-detail">
<?
$detailStoryPage=1;
$this->topIconBar($feedId,$fullUrl,$detailStoryPage,$feed_is_live);
?>
<div class="content-area-articles" style="border:none;">
  <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    	<?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
<div id="maincontainer_dailyfeed"  class="KonaBody"><span class="content-area-detail-image">
      <?php if($quickTitle!='') { ?>
      <div class="quicktitle">
        <div class="konafilter">
          <?=strtoupper(trim($quickTitle));?>
        </div>
      </div>
      <? } ?>
<?php
if($getImage['url']!='')
{ ?>
	<img src="<?=$imageURL;?>" border="0" width="140px" style="border:1px solid #CCC" alt="DailyFeed" />
<?php } ?>
</span>
<div class="body_detail" itemprop="articleBody" style="vertical-align:top;">
<?
 if(substr_count($feed_body,"{FLIKE}") > 0)
{
	$feed_body = str_replace("{FLIKE}","<div>".displaySmallFlike($bitlyUrl,'return')."</div>", $feed_body);
}
?>
<?=$feed_body;?>
      </div>
    </div>
</div>
<?php if($feedPosition!='') { ?>
<div style="clear:both;"></div>
  <div class="topic-area" id='position-area'> <span style="color:#acacac;"> POSITION:&nbsp;</span>
    <?=$feedPosition;?>
</div>
<?php } ?>
<div class="topic-area">
    <?php if($topicsURL!='') { ?>
    <span style="color:#acacac;"> TAGS:&nbsp;</span>
    <?=$topicslink;?>
    &nbsp;&nbsp;
    <?php } ?>
    <?php if($resource!='') { ?>
    <span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp;<a href="<?=$resource['source_link'];?>">
    <?=$resource['source'];?>
    </a>
    <?php } ?>
</div>
  <?php if($stock_tickers!='') { ?>
  <div class="topic-area" id="stock-area"><span style="color:#acacac;">TICKERS:</span>&nbsp;
    <?=$stocklink;?>
    &nbsp;&nbsp;</div>
  <?php } ?>
</div>
<div style="clear:both;"></div>
<?php
	}
	elseif($feedData->layout_type == 'thestreet')
	{
	global $IMG_SERVER;
	$objArticle			=   new articleViewer();
   	$feedType			=	'18';
    $feedTitle			=	trim(mswordReplaceSpecialChars($feedData->title));
	$feed_date_time 	=	$feedData->updation_date;
	$feed_date 			= 	substr($feed_date_time,0,10);
	$display_date 		=   date('F j, Y h:i A',strtotime($feedData->display_date));
	$contributorName	=	trim($feedData->contributor);
	$contributorId		=	trim($feedData->ContId);
	$feed_excerpt		=	trim(htmlentities(mswordReplaceSpecialChars(strip_tags($feedData->excerpt))),ENT_QUOTES);
	//$feed_excerpt		=	$objArticle->filter_urls($feed_excerpt);

	$feed_body 			=	trim(htmlentities(mswordReplaceSpecialChars($feedData->body)));

	$feed_body			=	html_entity_decode($objArticle->filter_urls($feed_body));

	$topicsURL="";
  	$topicslink="";
	$topicsURL			=	$objDailyfeed->getTopicsURL('daily_feed',$feedId);
	$getImage			=	$objDailyfeed->getImageDailyFeed($feedId);
	$quick_Title		=	$objDailyfeed->getQuickTitleDailyFeed($feedId,'18');
	$quickTitle			=	$quick_Title['quick_title'];
	$feedPosition		=	trim($feedData->position);
	$stock_tickers		=	$objDailyfeed->getTickersExchange($feedId,'18');
	$resource			=	$objDailyfeed->getResource($feedId,'18');
	$keywords			=	'';
	$feed_is_live		=	$feedData->is_live;

	if(stristr($quickTitle,'TheStreet.com'))
	{
		$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_thestreetlogo.gif";
	}
	elseif(stristr($quickTitle,	'RealMoney.com'))
	{
		$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_Realmoneylogo.gif";
	}
	elseif(stristr($quickTitle,'MainStreet.com'))
	{
		$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_mainStreetlogo.gif";
	}
	else
	{
		if(count($getImage)>0)
		{ 	$imageURL	=	$getImage['url'];  } else  { $imageURL	=''; }
	}

	if($source!='')
	{
		if($strKeywords=='')
		{
			$strKeywords	=	$source;
		}
		else
		{
			$strKeywords	=	$strKeywords.",".$source;
		}
	}
	if($stock_tickers!='')
	{
		if($strKeywords=='') { $strKeywords	=	$stock_tickers; }
		else  { $strKeywords	=	$strKeywords.",".$stock_tickers; }
	}



	if($topicsURL && count($topicsURL)>0)
	{
		$i=0;
		foreach($topicsURL as $key=>$row){
		if($i>0){
		$topicslink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}else{
		$topicslink.='<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}
		$i++;
	}
	}
	$stockval=array();
	$stocklink="";
	if(is_array($stock_tickers)){
		foreach($stock_tickers as $key=>$row){
			if($key>0){
				$stocklink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}else{
				$stocklink.=' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}

		}
	}
   ?>
   <br />
   <div class='thestreetBlock'>
  <div class="middle-main-heading">
    <h1>
      <?=$feedTitle;?>
    </h1>
  </div>
  <div class="submitted-by" style="float:left;width:460px;"> By <span><a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self">
    <?=$contributorName;?>
    </a></span>&nbsp;&nbsp;
    <?=$display_date;?>
  </div>
    <div id="TD_ad">
	 <?
	  if(!$_SESSION['AdsFree'] && $val == 0) { ?>
	<script language="javascript">
        CM8ShowAd("Button_160x30")
    </script>
    <? } ?>
  </div>
  <div class="comment-heading">
    <div class="commentsharingbar">
      <div class="comment-heading-right"> <img src="<?=$IMG_SERVER?>/images/articles/comments-bubble.gif" border="0" alt="comment" align="top" />
      <?php
     $objArticle =   new articleViewer();
     $comment_num = $this->fbcommentNum(); ?>
            <a href="<?=$articleFullURL?>#fb-comments" onclick="goToComments();">
            <?php
		    if($comment_num>0)
		    {
		    	if($comment_num>1)
			{
				echo $comment_num." Comments";
			}
			else
			{
				echo $comment_num." Comment";
			}
		    }
		    else
		    {
		    	echo "Post Comments";
		    }
		    ?>
		    </a>
      </div>
      <div class="comment-heading-left">
        <ul>
          <li style="padding-top:3px;"><a href="javascript:dailyfeedStoryPrint(<?=$feedId;?>);"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/TS_print_image.jpg"  border="0" style="vertical-align:middle" alt="Print" align="top"/> </a></li>
<li>
            <div id="fb-root"></div>
            <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
            <fb:like href="<?=urlencode($HTPFX.$HTHOST.$fullUrl)?>" send="true" height="22" width="450" show_faces="false" font=""></fb:like>
</li>
</ul>
</div>
</div>
    <div class="content-area-articles" style="border:none;"> <span class="content-area-detail-image">
      <?php if($quickTitle!='') { ?>
      <div class="quicktitle">
        <?=strtoupper(trim($quickTitle));?>
      </div>
      <? } ?>
      <img src="<?=$imageURL;?>" border="0" width="142px" alt="DailyFeed" /> </span>
<div class="body_detail" style="vertical-align:top;">
<?=$feed_body;?>
</div>
</div>
<?php if($feedPosition!='') { ?>
<div style="clear:both;"></div>
    <div class="topic-area" id='position-area'> <span style="color:#acacac;"> POSITION:&nbsp;</span>
      <?=$feedPosition;?>
</div>
<?php } ?>
<div class="topic-area">
      <?php if($topicsURL!='') { ?>
      <span style="color:#acacac;"> TAGS:&nbsp;</span>
      <?=$topicslink;?>
      &nbsp;&nbsp;
      <?php } ?>
<?php /*if($resource!='') { ?> <span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp;<a href="<?=$resource['source_link'];?>"><?=$resource['source'];?></a><?php } */?>
</div>
    <?php if($resource!='') { ?>
    <span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp; <a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$resource['source_link']."?puc=minyanvilletsc&amp;cm_ven=minyanvillets";?>','TheStreet Resource');">
    <?=$resource['source'];?>
    </a>
    <?php } ?>
    <?php if($stock_tickers!='') { ?>
    <div class="topic-area" id="stock-area"><span style="color:#acacac;">TICKERS:</span>&nbsp;
      <?=$stocklink;?>
      &nbsp;&nbsp;</div>
    <?php } ?>
</div>
   </div>
<div style="clear:both;"></div>
<?php
	}
	else
	{
   	global $IMG_SERVER;
    $objDailyfeed 		=	new Dailyfeed();
   	$feedType			=	'18';
    $feedTitle			=	trim(mswordReplaceSpecialChars($feedData->title));
	$feed_date_time 	=	$feedData->updation_date;
	$feed_date 			= 	substr($feed_date_time,0,10);
	$display_date 		=   date('F j, Y h:i A',strtotime($feedData->display_date));
	$contributorName	=	trim($feedData->contributor);
	$contributorId		=	trim($feedData->ContId);
	$feed_excerpt		=	trim(htmlentities(mswordReplaceSpecialChars(strip_tags($feedData->excerpt))),ENT_QUOTES);
	$feed_body 			=	$feedData->body;
	$topicsURL="";
  	$topicslink="";
	$topicsURL			=	$objDailyfeed->getTopicsURL('daily_feed',$feedId);
	$getImage			=	$objDailyfeed->getImageDailyFeed($feedId);
	$quick_Title		=	$objDailyfeed->getQuickTitleDailyFeed($feedId,'18');
	$quickTitle			=	$quick_Title['quick_title'];
	$feedPosition		=	trim($feedData->position);
	$stock_tickers		=	$objDailyfeed->getTickersExchange($feedId,'18');
	$resource			=	$objDailyfeed->getResource($feedId,'18');
	$keywords			=	'';
	$feed_is_live		=	$feedData->is_live;

	if($source!='')
	{
		if($strKeywords=='')
		{
			$strKeywords	=	$source;
		}
		else
		{
			$strKeywords	=	$strKeywords.",".$source;
		}
	}
	if($stock_tickers!='')
	{
		if($strKeywords=='') { $strKeywords	=	$stock_tickers; }
		else  { $strKeywords	=	$strKeywords.",".$stock_tickers; }
	}

	if(count($getImage)>0)
	{ 	$imageURL	=	$getImage['url'];  } else  { $imageURL	=''; }

	if($topicsURL && count($topicsURL)>0)
	{
		$i=0;
		foreach($topicsURL as $key=>$row){
		if($i>0){
		$topicslink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}else{
		$topicslink.='<a href="'.$HTPFX.$HTHOST.'/mvpremium/tag'.trim($row).'" target="_self">'.strtoupper($key).'</a>';
		}
		$i++;
	}
	}
	$stockval=array();
	$stocklink="";
	if(is_array($stock_tickers)){
		foreach($stock_tickers as $key=>$row){
			if($key>0){
				$stocklink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}else{
				$stocklink.=' '.'<a href="'.$HTPFX.$HTHOST.'/mvpremium/tid/'.$row['id'].'" target="_self">'.strtoupper($row['exchange'].':'.$row['stocksymbol']).'</a>';
			}

		}
	}
   ?>
<div class="middle-main-heading">
  <h1>
    <?=$feedTitle;?>
  </h1>
</div>
<div class="submitted-by" style="float:left;width:470px;"> By <span><a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self">
  <?=$contributorName;?>
  </a></span>&nbsp;&nbsp;
  <?=$display_date;?>
</div>
<div id="TD_ad">
	 <?
	  if(!$_SESSION['AdsFree'] && $val == 0) { ?>
	<script language="javascript">
        CM8ShowAd("Button_160x30")
    </script>
    <? } ?>
</div>
<div class="comment-heading">
<?
$detailStoryPage=0;
$this->topIconBar($feedId,$fullUrl,$detailStoryPage,$feed_is_live);
?>
  <div class="content-area-articles" style="border:none;"> <span class="content-area-detail-image">
    <?php if($quickTitle!='') { ?>
    <div class="quicktitle">
      <?=strtoupper(trim($quickTitle));?>
    </div>
    <? } ?>
<?php
if($getImage['url']!='')
{ ?>
	<img src="<?=$imageURL;?>" border="0" width="142px" alt="DailyFeed" />
<?php } ?>
</span>
<div class="body_detail" style="vertical-align:top;">
<?=$feed_body;?>
</div>
</div>
<?php if($feedPosition!='') { ?>
<div style="clear:both;"></div>
  <div class="topic-area" id='position-area'> <span style="color:#acacac;"> POSITION:&nbsp;</span>
    <?=$feedPosition;?>
</div>
<?php } ?>
<div class="topic-area">
    <?php if($topicsURL!='') { ?>
    <span style="color:#acacac;"> TAGS:&nbsp;</span>
    <?=$topicslink;?>
    &nbsp;&nbsp;
    <?php } ?>
    <?php if($resource!='') { ?>
    <span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp;<a href="<?=$resource['source_link'];?>">
    <?=$resource['source'];?>
    </a>
    <?php } ?>
</div>
  <?php if($stock_tickers!='') { ?>
  <div class="topic-area" id="stock-area"><span style="color:#acacac;">TICKERS:</span>&nbsp;
    <?=$stocklink;?>
    &nbsp;&nbsp;</div>
  <?php } ?>
</div>
<div style="clear:both;"></div>
<?php
  }
 }
   function detailStoryPageIcons($feedId,$fullUrl,$feedTitle,$feed_excerpt,$layout)
   {
   global $HTPFX,$HTHOST,$IMG_SERVER;
   $feedTitle=urlencode($feedTitle);
   $objDailyfeed 		=	new Dailyfeed();
   $bitlyUrl= $objDailyfeed->getDailyFeedBitlyUrl($feedId);
   if($layout=='thestreet')
   {
   ?>
   <a href="javascript:dailyfeedStoryPrint(<?=$feedId;?>);"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/TS_print_image.jpg"  border="0" style="vertical-align:middle" alt="Print"/></a>&nbsp;<a href="<?=$HTPFX.$HTHOST?>/rss/dailyfeed.rss"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/rss-image.jpg" style="vertical-align:middle" alt="RSS"/></a>&nbsp;&nbsp;&nbsp;<span style="color:#acacac">|</span>&nbsp;
SHARE THIS ARTICLE: <a href="<?=$HTPFX.$HTHOST?>/share/daily_feed_email.htm?id=<?=$feedId;?>" ><img src="<?=$IMG_SERVER?>/assets/dailyfeed/TS_message_image.jpg" style="vertical-align:middle" alt="Email"/></a> <a href="http://www.facebook.com/sharer.php?u=<? echo $bitlyUrl ?>&t=<? echo $feedTitle ?>" target="_blank"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/TS_facebook_image.jpg" style="vertical-align:middle" alt="Share on Facebook"/></a> <a><img onclick="callBitlyApi('<?=$feedTitle?>','<?=$fullUrl?>');" src="<?=$IMG_SERVER?>/images/icons/TS_twitter.gif" style="vertical-align:middle" alt="Share on Twitter"/></a>
<?

?>
<a target="_blank" rel="nofollow" href="http://digg.com/submit?url=<? echo $bitlyUrl; ?>&title=<?=$feedTitle;?>"><img style="border:none; vertical-align:middle; padding-left:2px;" src="<?=$IMG_SERVER;?>/images/icons/TS_digg.gif" alt="Post to Digg" ></a> <a target="_blank" rel="nofollow" href="http://www.reddit.com/submit?url=<? echo $bitlyUrl; ?>&title=<?=$feedTitle;?>"><img style="border:none;vertical-align:middle;" src="<?=$IMG_SERVER;?>/images/icons/TS_reddit.gif" alt="Post to Reddit"></a> <a href="http://del.icio.us/post" onclick="window.open('http://del.icio.us/post?v=4&noui&jump=close&url=<?=urlencode($bitlyUrl); ?>&title=<?= urlencode($feedTitle); ?>', 'delicious','toolbar=no,width=700,height=400'); return false;"><img src="<?=$IMG_SERVER;?>/images/icons/TS_delicious.gif" style="vertical-align:middle; border:none;" width="16" height="16" alt="Delicious" /></a>
   <?php
   }
   else
   {
   ?>
   <a href="javascript:dailyfeedStoryPrint(<?=$feedId;?>);"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/print_image.jpg"  border="0" style="vertical-align:middle" alt="Print"/></a>&nbsp;<a href="<?=$HTPFX.$HTHOST?>/rss/dailyfeed.rss"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/rss-image.jpg" style="vertical-align:middle" alt="RSS"/></a>&nbsp;&nbsp;&nbsp;<span style="color:#acacac">|</span>&nbsp;
SHARE THIS ARTICLE: <a href="<?=$HTPFX.$HTHOST?>/share/daily_feed_email.htm?id=<?=$feedId;?>" ><img src="<?=$IMG_SERVER?>/assets/dailyfeed/message_image.jpg" style="vertical-align:middle" alt="Email"/></a> <a href="http://www.facebook.com/sharer.php?u=<? echo $bitlyUrl ?>&t=<? echo $feedTitle ?>" target="_blank"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/facebook_image.jpg" style="vertical-align:middle" alt="Share on Facebook"/></a> <a><img onclick="callBitlyApi('<?=$feedTitle?>','<?=$fullUrl?>');" src="<?=$IMG_SERVER?>/images/icons/twitter.gif" style="vertical-align:middle" alt="Share on Twitter"/></a> <a target="_blank" rel="nofollow" href="http://digg.com/submit?url=<? echo $bitlyUrl; ?>&title=<?=$feedTitle;?>"><img style="border:none; vertical-align:middle; padding-left:2px;" src="<?=$IMG_SERVER;?>/images/icons/digg.gif" alt="Post to Digg" ></a> <a target="_blank" rel="nofollow" href="http://www.reddit.com/submit?url=<? echo $bitlyUrl; ?>&title=<?=$feedTitle;?>"><img style="border:none;vertical-align:middle;" src="<?=$IMG_SERVER;?>/images/icons/reddit.gif" alt="Post to Reddit"></a> <a href="http://del.icio.us/post" onclick="window.open('http://del.icio.us/post?v=4&noui&jump=close&url=<?=urlencode($bitlyUrl); ?>&title=<?= urlencode($feedTitle); ?>', 'delicious','toolbar=no,width=700,height=400'); return false;"><img src="<?=$IMG_SERVER;?>/images/icons/delicious.gif" style="vertical-align:middle; border:none;" width="16" height="16" alt="Delicious" /></a>
   <?php
   }
   }

   function dailyfeedRightModules($objfeed=NULL)
   {
   global $D_R;
   ?>
   	<div id="article-right" class="konafilter" style="min-height:816px;">
  <div class="article-right-module" style="padding-top:10px;">
    <? CM8_ShowAd('MediumRectangle_300x250_300x600'); ?>
</div>
    <?
	$this->showMostRead();
	$this->showTopSources();
    $this->showContributor();
	$this->showPopularStocks();
	$this->showTopics(); // most popular tags
	?>
	</div>
	<?php
   }
 function outbrainWidget($baseUrl)
{
global $HTPFX,$HTHOST;
?>
<style>
#outbrain-us,#outbrain-partners
{
padding:0px;
color:#002e5a;
margin:0px;
text-transform:uppercase;
font-size:16px;
font-weight:bold;
}
.div-wrapper ul li {
color: #999999;
text-decoration:none;
list-style:outside;
padding:5px 0px 5px 0px;
fonnt-size:12px;
line-height:15px;
}
.outbrain_rec_li a
{
color: #01509D;
font-weight: bold;text-decoration:none;
font-size:12px;
}
.rec-src-link
{
font-size:12px;
line-height:19px;
}
.outbrain_column
{
width:300px;
font-family:arial,helvetica,sans-serif,verdana;
}
#outbrain_hook_minyanville_0
{
padding-top:5px;
}
.outbrain-recommendationsFieldset
{
border:0px;
border-width:0px;
}
.outbrain-recommendationsFieldset-ie
{
border:0px;
border-top:0px;
}
.outbrain_dual_reg_ul_class recommendations_ul_ie
{
padding:0px;
}
#outbrain-org
{
border-right:1px solid #999999;
margin-right:10px;
}
#bottom-module-container {
border-top: 1px dotted #999999;
border-bottom: 1px dotted #999999;
padding: 20px 0 20px 0;
margin-bottom:20px;
}
.outbrain-recommendationsFieldset, .outbrain-recommendationsFieldset-ie, .outbrain-recommendationsFieldset-sc {
border: medium none !important;
padding-left:0px !important;
padding-top:0px !important;
}
.div-wrapper ul {
padding: 0px !important;
padding-left:15px !important;
}
#bottom-container
{
float:left;
}
</style>
<script type="text/JavaScript">
var OB_permalink= '<?=$HTPFX.$HTHOST.$baseUrl?>';
var OB_Template="minyanville";
var OB_widgetId= 'AR_1';
var OB_langJS ='http://widgets.outbrain.com/lang_en.js';
if ( typeof(OB_Script)!='undefined' )
OutbrainStart();
else
{
var OB_Script = true;
var str = "<script src='http://widgets.outbrain.com/outbrainWidget.js'; type='text/javascript'></"+"script>";
document.write(str);
}
</script>
<?
}
function topIconBar($feedId,$fullUrl,$detailStoryPage,$feed_is_live)
{
global $IMG_SERVER,$df_title,$tickerArr;
$feedcache= new Cache();
$tickers = $feedcache->getTickerListCache($feedId,'18',$tickerArr,$df_title,$feed_is_live);
?>

<div class="commentsharingbar">

<div id="horiz_share_icon" style="margin:0px;padding:0px;">
  <ul>
    <li id="social_icon_twitter_horizon" style="margin: 9px 9px 0 0;"><span class='st_twitter_hcount' displayText='Tweet' st_url="<?=$fullUrl;?>" st_title = "<?=$df_title.$tickers;?>" st_via="minyanville"></span></li>
	<li id="social_icon_fb_horizon" style="margin: 9px 9px 0 0;"><span class='st_facebook_hcount' displayText='Facebook' st_url="<?=$fullUrl?>"></span></li>
	<li id="social_icon_google_horizon" style=" margin: 2px 7px 0 0;"><span class='st_googleplus_large' displayText='Google +1' st_url="<?=$fullUrl?>"></span></li>
	<li id="social_icon_in_horizon" style=" margin: 2px 7px 0 0;"><span class='st_linkedin_large' displayText='LinkedIn' st_url="<?=$fullUrl?>"></span></li>
	<li id="social_icon_st_horizon" style=" margin: 2px 7px 0 0;"><span class='st_sharethis_large' displayText='ShareThis' st_url="<?=$fullUrl?>"></span></li>
	<li id="social_icon_email_horizon" style=" margin: 2px 7px 0 0;"><span class='st_email_large' displayText='Email' st_url="<?=$fullUrl?>"></span></li>
	<li id="social_icon_rss_horizon" style=" margin: 2px 7px 0 0;"><a href="<?=$HTPFXNSSL.$HTHOST ?>/service/rss.htm" target="_blank"><img src="<?=$IMG_SERVER;?>/images/articles/RSS_icon_32X32.png"></a></li>

  </ul>
</div>
<!-- Share this Code Start -->
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="
http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher: "c33749e3-a8dd-48d6-af73-7568c530a7f8",onhover: false}); </script>

<!-- Share this Code End -->
  <div style="float:right;">
      <div class="comment-heading-right">
      <a href="javascript:dailyfeedStoryPrint(<?=$feedId;?>);"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/print_image.jpg"  border="0" style="vertical-align:middle" alt="Print" align="top"/> </a>
      <img src="<?=$IMG_SERVER?>/images/articles/comments-bubble.gif" border="0" alt="comment" align="absmiddle" />
     <?php
     $objArticle =   new articleViewer();
     $comment_num = $objArticle->fbcommentNum(); ?>
            <a href="<?=$articleFullURL?>#fb-comments" onclick="goToComments();">
            <?php
		    if($comment_num>0)
		    {
		    	if($comment_num>1)
			{
				echo $comment_num." Comments";
			}
			else
			{
				echo $comment_num." Comment";
			}
		    }
		    else
		    {
		    	echo "Post Comments";
		    }
		    ?>
		    </a>
      </div>
  </div>
  <div class="comment-heading-left">
    <ul>
      <li style="padding-top:3px;"></li>
      <?php if(!$detailStoryPage) { ?>
      <li id="social_icon_google_horizon" style="margin-right:5px;"><span class="st_plusone_hcount" displayText="Share" st_url="<?=$fullUrl?>"></span></li>
      <? } ?>
    </ul>
  </div>
</div>
<?
}

  function dfFaceBookRecommendation(){
  	mvFacebookRecommendation();
  }

  function dfTopsyTweet(){
  	mvTopsyTweet();
  }

} //------------ Class End ----------
?>
