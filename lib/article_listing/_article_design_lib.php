<?
global $D_R;
include_once("$D_R/lib/article_listing/_article_data_lib.php");
$objArticleData = new articleData();
class articleDesign{
	
	 function displayArticles($ID,$start,$end)
	    {
	      global $HTHOST,$HTPFX,$IMG_SERVER,$productItemType; 
		  $objArticleData = new articleData();
		  /******** ETF ********/
		  if($ID=="70")
		     {
		  ?>
<!--left contaner start from here-->

<div id="article-left">

<!--Add Display-->

<div class="etf_article">
<div class="etf_gap"></div>
<div class="etf_box">
<div class="etf_box_in">
<div class="eft_lft_div">
<img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/img_allETFarticles.gif" />
</div>
<div style="width:50%;float:right;">
<div class="etf_fb">
<a href="http://www.facebook.com/MinyanvilleMedia"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/sharingicon_fb.gif" border="0"  /></a>
</div>
<div class="etf_sc">
<a href="http://twitter.com/minyanville"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/sharingicon_twitter.gif" border="0" /></a>
</div>
<div class="etf_sc">

<img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/sharingicon_raddish.gif"  />
</div>
<div class="etf_sc">
<a href="<?=$HTPFX.$HTHOST;?>/rss/"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/sharingicon_rss.gif" border="0"  /></a>
</div>
</div>
</div>
</div>
</div>
<div style="height:40px;"></div>

<!--Add Display-->
  <div class="rss_common_main_new">
    <div id="latest-article">
	
	<?php
	$aricleLatestResult = $objArticleData->getLatestAricles($ID);
	 
	foreach ($aricleLatestResult as $row )
			 {
			
			 if (($row['contributor'] != '') && ($row['contributor'] != $row['author']))
				{
					$row['author'] = $row['contributor'];
				}
			  $currDate = date('M d, Y g:i a',strtotime($row['date']));
			  echo "<h1>";
			  echo  mswordReplaceSpecialChars($row['title']);  
			  echo "</h1>";
			  ?>
			  <br /><br />
			  <?php	
			  echo "<span>";
			  echo "By ";
			  echo "<strong class=\"author\"> <a href=\"/gazette/bios.htm?bio=". $row['author_id']."\" style=\"text-decoration:none;\">";
			  echo $row['author'].'</a></strong> '.$currDate;
			  echo "</span>";
			  echo "<br>";
			  echo "<br>";
			  echo strip_tags(substr($row['body'],0,200)); 
			  echo "<br>";
			  echo "<br>";
			  
			 }
	?>		
	</div>
    <div class="list-module-container">
	<div style="height:30px;"></div>
	<div style="width:48%;float:left;
border-right: 1px solid #e7e7e7;">
      <div class="listing-left-mod" id="mod-1" >
       
        <ul id="articleList">
          <?
            $i=0;
			$aricleResult = $objArticleData->getAllAricles($ID,$start,$end);
			foreach ($aricleResult as $row )
			{
				if (($row['contributor'] != '') && ($row['contributor'] != $row['author']))
				{
					$row['author'] = $row['contributor'];
				}

				if ($currDate != date('M d',strtotime($row['date'])))
				{
					$currDate = date('M d',strtotime($row['date']));
					if($i != 0)
					{
					?>
        </ul>
      </div>
      <!-- end left mod -->
      <? if ($i > 1 ) { ?>
      <? } ?>
      <div class="listing-left-mod">
        <ul id="articleList">
          <? } ?>
		  <!--
          <div class="listing-date">
            <? // $currDate; 
			/*
			if(!$_SESSION['AdsFree']) {
								?>
            (Sponsored By:
            <?php
					//		$objindustrybrains=new industrybrains();
					//		$objindustrybrains->displayads($pageName,"1x1");
					?>
            )
            <? } 
			*/
			?>
          </div>
		  -->
          <?	} 
				if($row['item_type']){
				
					if(in_array($row['item_type'],$productItemType)){
						$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
					}else{
						$mvp_logo = '';
					}
				}
				
				if(in_array($row['item_type'],$productItemType)){
					$url = $row['url'].'?camp=articlepremiumcontent&medium=articlelisting&from=minyanville';
				}else{
					$url = $row['url'];
				}
				
			?>
          <? if ($row['author'] == "Associated Press") { ?>
          <li style="list-style:none;">
		  <div class="left_div_b">
		  <img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/bluebullet.gif" class="blue_bullet"/> </div>
		  <div class="left_div_r"><a href= "<?=$url?>" >
            <?= mswordReplaceSpecialChars($row['title']); ?>
            </a>&nbsp;
            <?=$mvp_logo?>
            <br>
            <span>by <a>
            <?= $row['author']; ?>
            </a> at
            <?= date('g:i a',strtotime($row['date'])); ?>
            </span>
			</div>
			</li>
          <? } else { ?>
          <li style="list-style:none;">
		  <div class="left_div_b">
		  <img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/bluebullet.gif" class="blue_bullet"/>
		  </div>
		  <div class="left_div_r">
		  
		  <a href= "<?=$url?>">
            <?= mswordReplaceSpecialChars($row['title']); ?>
            </a>
            <?=$mvp_logo?>
            <br>
            <span>by <a href="/gazette/bios.htm?bio=<?= $row['author_id']; ?>">
            <?= $row['author']; ?>
            </a> at
            <?= date('g:i a',strtotime($row['date'])); ?>
            </span>
			</div>
			</li>
          <? } ?>
          <?
        		$i++;
         } ?>
        </ul>
		</div>
	  
      
      <div id="more-articles" style="width:50%"> More:
        <? if ($start > 0) { ?>
        <a href="<?= $PHP_SELF;?>?limit=<?=$start - 100;?>&sectionId=<?php echo $ID;?>">Recent Articles</a> |
        <? } ?>
        <a href="<?= $PHP_SELF;?>?limit=<?=$end;?>&sectionId=<?php echo $ID;?>">Past Articles</a> <br>
        <br>
        <div id="rssinfo" <? if ($_GET['limit'] != "") { echo "style='display:none;'"; } ?>><br>
        </div></div>
	  </div>
	  <div style="width:50%;float:left;padding:0 0 0 10px;">
	   <div><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/img_guidetoETF.gif" /></div>       <div style="height:10px;">&nbsp;</div>
	   <!---- Guid Shadow ETF ----->
	   
	   <div class="guid_shadow">
	        
			<div class="guid_shadow_content">
			   <div style="height:15px;">&nbsp;</div>
			  <!-- Guid ETF Content -->
			  
			  <div >
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Byroad Market Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			   <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Large Cap Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			   <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Mid Cap Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			   <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Small Cap Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			   <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Global Industry Sector Stocks</a></div>
			  </div> 
			   <div class="clr"></div>
			  
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Intl. & Emerging Marketing Industry Sector Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Us Industry Sector Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Global & International Stocks</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Emerging Markets </a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Long/Levraged ETFs and ETNs</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Short/Levraged ETFs and ETNs</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Speciality/Levraged ETFs and ETNs</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Fixed Income</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Commodity</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			   <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Currency</a></div>
			  </div> 
			  <div class="clr"></div>
			  <div>
			  <div class="guid_shadow_content_in"><img src="<?php echo $HTPFX.$HTHOST;?>/images/article_listing/yellowbullet.gif" class="yellow_bullet"/></div><div class="guid_shadow_content_a" > <a href="#"  style="text-decoration:none;">Unmanaged Baskets</a></div>
			  </div> 
			  
               <!-- Guid ETF Content -->
			  <div style="height:40px;">&nbsp;</div>
			   
			</div>
			</div>
	   <!---- Guid Shadow ETF ----->
	   </div>
    </div>
  </div>
</div>
<script>
var firstmod = $j("#mod-1").height();
var apmod = $j(".listing-right-mod").height();
firstmod = parseInt(firstmod);
apmod = parseInt(apmod);

if (firstmod > apmod) {
	$j("#mod-1").after("<div style=\'clear:both\'></div>");
}
	$j(".listing-left-mod").each( function(i) {
		if (i > 0) {
			$j(this).after("<div style=\'clear:both;\'></div>");
		}
	});

</script>
<!--left contaner end here-->
  <?php
   }
  else
   {
   ?>
   <!--left contaner start from here-->
<div id="article-left">
<div class="rss_common_main">
<div  class="footer_common_title" style="width:645px;">
	<div id="article-listing-title" class="sub_common_title"></div><div class="subscriptionsponsorship"><? CM8_ShowAd('PageTitleSponsorship'); ?></div>
</div>

<style>

/*ap styles */
#minyanville-article-header {
	font-size:15px;
	margin: 20px 0px 10px 0px;
	font-weight:bold;

}

.list-module-container {
	width:640px;
	min-height:470px;

}

#listing-rss {
	font-weight:bold;

}

.listing-left-mod {
	margin-top:10px;
	width:100%;
	float:left;
}



.listing-right-mod ul {
	margin-left:10px;
	border:none;
	width:240px;
}

.listing-right-mod ul li {
        margin-bottom:5px;
}


.listing-right-mod ul li span {
	color:#939393;
	font-size:11px;
}

.listing-right-mod ul li a {
	font-size:13px;
}

.listing-date {
	margin:10px 0px 10px 0px;

}

ul#articleList {
	border:none;
	padding-bottom:0;
	margin-bottom:0;

}

ul#articleList li {
	margin-bottom:5px;
}

ul#articleList li span {
        color:#939393;
	font-size:11px;
}

ul#articleList li span a {
	font-size:11px;
	text-decoration:none;
}

ul#articleList li span a:hover {
	text-decoration:underline;

}
</style>

	  <div id="listing-rss">
        	Subscribe to minyanville article RSS: <a href="<?=$HTPFX.$HTHOST;?>/rss/"><?=$HTPFX.$HTHOST;?>/rss/</a>
      	  </div>

<div class="list-module-container">



	<div class="listing-left-mod" id="mod-1" >
                <div id="minyanville-article-header">
			<img src="/images/hdr-allarticles-long.gif">
                        <!--Minyanville Articles -->
                </div>
			<ul id="articleList">
			<?
            $i=0;
			$results = $objArticleData->getAllAricles($ID,$start,$end);
			foreach ($results as $row )
			{
				if (($row['contributor'] != '') && ($row['contributor'] != $row['author']))
				{
					$row['author'] = $row['contributor'];
				}

				if ($currDate != date('M d',strtotime($row['date'])))
				{
					$currDate = date('M d',strtotime($row['date']));
					if($i != 0)
					{
					?>
					</ul>
					</div><!-- end left mod -->
					<? if ($i > 1 ) { ?>
					 <? } ?>
					<div class="listing-left-mod">
					<ul id="articleList">
					<? }
					 ?>
                    
					<div class="listing-date"><?= $currDate; if(!$_SESSION['AdsFree']) {
								?>

                       (Sponsored By:
					<?php
							$objindustrybrains=new industrybrains();
							$objindustrybrains->displayads($pageName,"1x1");
					?>

                       )
<? } ?></div>

			<?	} 
				if($row['item_type']){
					if(in_array($row['item_type'],$productItemType)){
						$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
					}else{
						$mvp_logo = '';
					}
				}
				
				if(in_array($row['item_type'],$productItemType)){
					$url = $row['url'].'?camp=articlepremiumcontent&medium=articlelisting&from=minyanville';
				}else{
					$url = $row['url'];
				}
				
			?>
				
			

			<? if ($row['author'] == "Associated Press") { ?>
                    <li><a href= "<?=$url?>" ><?= mswordReplaceSpecialChars($row['title']); ?></a>&nbsp;<?=$mvp_logo?><br><span>by <a><?= $row['author']; ?></a> at <?= date('g:i a',strtotime($row['date'])); ?></span></li>
			<? } else { ?>
                    <li><a href= "<?=$url?>"><?= mswordReplaceSpecialChars($row['title']); ?></a>  <?=$mvp_logo?><br><span>by <a href="/gazette/bios.htm?bio=<?= $row['author_id']; ?>"><?= $row['author']; ?></a> at <?= date('g:i a',strtotime($row['date'])); ?></span></li>
			<? } ?>
				<?
        		$i++;
         } ?>

			</ul>

	</div>


<div style="clear:both;"></div>

<div id="more-articles">
                         More:
                        <? if ($start > 0) { ?>
                        <a href="<?= $PHP_SELF;?>?limit=<?=$start - 100;?>">Recent Articles</a> |
                        <? } ?>
                        <a href="<?= $PHP_SELF;?>?limit=<?=$end;?>">Past Articles</a>
                        <br>
                        <br>
            <div id="rssinfo" <? if ($_GET['limit'] != "") { echo "style='display:none;'"; } ?>><br>
                        </div>

</div>

</div>



</div>
</div>

<script>
var firstmod = $j("#mod-1").height();
var apmod = $j(".listing-right-mod").height();
firstmod = parseInt(firstmod);
apmod = parseInt(apmod);

if (firstmod > apmod) {
	$j("#mod-1").after("<div style=\'clear:both\'></div>");
}
	$j(".listing-left-mod").each( function(i) {
		if (i > 0) {
			$j(this).after("<div style=\'clear:both;\'></div>");
		}
	});

</script>

<!--left contaner end here-->
   <?php
   
   } 
	  	}
	
      
} // class End

?>
