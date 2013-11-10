<?php
class TopicArticle{

   function topicheader($subSectionDataname){
   	$disaplayHeader = ucwords(strtolower($subSectionDataname));
   	if(strtolower($subSectionDataname) == 'etfs')
   	{
   		$disaplayHeader = "ETF";
   	}
   	?>
	<div class="eft_articles_1">
		<div class="efts_a">
	<?
	if($subSectionDataname=="Radio")
	{
		echo "All ".$disaplayHeader." Shows";
	}
	else
	{
		echo "All ".$disaplayHeader." Articles";
	}
	?>
	</div>
	</div>
	<?
   }


	function topicSharingArea($sectionId){
	global $IMG_SERVER;
	?>
			<div class="topic_newsletter_sharing">
			<div class="toplineFreeNewsletter"><a
				href="http://www.facebook.com/MinyanvilleMedia" target="_blank"><img
				src="<?=$IMG_SERVER?>/images/topic/etf_iconFB.gif" border="0" /></a> <a
				href="http://twitter.com/minyanville" target="_blank"><img
				src="<?=$IMG_SERVER?>/images/topic/etf_iconTwitter.gif" border="0" /></a>

			<a href="http://www.linkedin.com/company/minyanville-media-inc"><img
				src="<?=$IMG_SERVER?>/images/topic/etf_iconIN.gif" border="0" /></a>
				<a href="<?=$HTPFX.$HTHOST?>/rss/articlerss.rss?sec_id=<?php echo $sectionId;?>" target="_blank"> <img
				src="<?=$IMG_SERVER?>/images/topic/etf_iconRSS.gif" border="0" /></a>
			</div>
		</div>

	<?
	}

	function topicSubscribeNewsLetter($subSectionData,$sectionId){
	$email=$_SESSION['email'];
	if($email=="")
	{
		$email="Input your email here";
	}
	?>
	<div class="freenewsletter">
	    <?php $this->topicSharingArea($sectionId); ?>
		<!--Sharing Icon Area End -->
		<div class="topic_signup">
		<?php
			  $userid = $_SESSION['SID'];
			  ?>
			<input class="inputTextEmail" id="section_email" type="text" name="section_email" value="<?php echo $email;?>" onfocus="javascript:if(this.value=='Input your email here') this.value=''; return false;" onblur="javascript:if(this.value=='') this.value='Input your email here'; return false;" size="36" onkeyup="javascript:topicEmailEnterKeyChk(event,'section_email','section_subscriber_id','section_id','section_name');"/>
			<input id="section_subscriber_id" type="hidden" name="section_subscriber_id" value="<?php echo $userid ;?>"><input id="section_id" type="hidden" name="section_id" value="<?php echo $sectionId  ;?>"><input id="section_name" type="hidden" name="section_name" value="<?php echo $subSectionData['name'];?>"><input class="inputtextSubmit" type="button" onclick="iboxTopicemail('section_email','section_subscriber_id','section_id','section_name');" value="Submit" />
		</div>
		<!--Get Free MVIL End -->
		<div class="topic_signup_text">Get <span>FREE MINYANVILLE</span><br />
		Newsletters & Alerts &nbsp; <?php
		$email=$_SESSION['email'];
		if($email=="")
		{
			$email="Input your email here";
		}
		?>
		</div><!--Input Area End -->

	</div>

	<?
	}


	function topicRecommendation(){
	?>
		<div class="recommendationsmaindiv">
		<div class="recommendationsinnerdiv"><h3 class="new-head-right">Recommendations</h3></div>
		<iframe	src="http://www.facebook.com/plugins/recommendations.php?site=minyanville.com&amp;width=312&amp;height=300&amp;header=false&amp;colorscheme=light&amp;font=arial&amp;border_color=%23dedede"
			scrolling="no" frameborder="0"
			style="border: none; overflow: hidden; width: 312px; height: 300px;"
			allowTransparency="true"></iframe>
		</div>
	<?
	}


	function topsyTopic(){
	?>
	<div class="topcitopsy">
		<script type="text/javascript" class="tpsy_sm">
		var TPSY_sm = [].concat(TPSY_sm || [], {
		width: '312',
		height: '400',
		title: 'Recent Tweets',
		query: 'minyanville',
		shell_bg_color: '#002E5A',
		link_color: '#002E5A',
		show_bottom_ad: true,
		show_nick: true,
		nick: '@minyanville',
		apikey: 'tlQjPUNZJmICbGaw17s8EqtV',
		type: 'search'
		});
		</script>
		<script type="text/javascript" src="http://cdn.topsy.com/social-modules/widget_loader.js"></script>
	</div>
	<?
	}

	function latestArticle($arLatest,$subSectionName){
	global $HTPFX,$HTHOST;
	?>
	 <div id="latest-article">
		<?
		if($arLatest){
			?>
		<h1><div><a href="<?= $HTPFX.$HTHOST.$arLatest['url']?>"><?=mswordReplaceSpecialChars($arLatest['title']); ?></a></div></h1>
		<div id="article-byline">By <a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$arLatest['author_id']?>"><?=$arLatest['author']?></a>
			<?= date('M d, Y g:i a',strtotime($arLatest['publish_date'])); ?>
		</div>
		<div id="article-dek">
			<h2><? echo mswordReplaceSpecialChars($arLatest['description']); ?>&nbsp;<a style="font-weight:bold;color:#074C99;font-size:12px;"  href="<?= $HTPFX.$HTHOST.$arLatest['url']?>">
		<?
			if($subSectionName=="Radio")
			{
				echo "Listen to this radio show";
			}
			else
			{
				echo "Read this article";
			}

		?>
		</a></h2>
		</div>
		<?
		}
		?>
	</div>
	<?
	}

	function topicLatestArticle($arArticles){
	?>
		<div id="articleList">
			<?
		if(!empty($arArticles))
		{
			foreach ($arArticles as $key => $arArticle)
			{
				if($key == 0)
				{
					continue;
				}
				?>
				<div class="articletitle">
				<a href="<?= $HTPFX.$HTHOST.$arArticle['url']?>"><?= mswordReplaceSpecialChars($arArticle['title']); ?></a>
				<br>
				<div class="author-article">
				<a class="author-article" href="<?=$HTPFX.$HTHOST;?>/gazette/bios.htm?bio=<?= $arArticle['author_id']; ?>"><?= $arArticle['author']; ?></a>
				</div>
				</div>
				<?
			}
		}
			?>
		</div>
		<?
	}

	function topicSponsoredLinks(){
	?>
	<div class="sponsoredtopic_links">
		<?php sponsoredLinksIndexPage(); ?>
	</div>
	<?php
	}
}
?>