<?php
global $IMG_SERVER,$HTPFX,$HTHOST,$D_R;
//include_once("$D_R/lib/_disqus_lib.php");
include_once($D_R.'/lib/_bitly_lib.php');



class articleViewer
{


function get_data($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function FbcommentSEO()
{
	if(empty($page_url))
        {
                $article_url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $urlArr = explode('?', $article_url);
                $url = $urlArr[0];
        }
        else
        {
                $urlArr = explode('?', $page_url);
                $url = $urlArr[0];
        }
        ?>
	 <script>
		var seo_url = 'https://graph.facebook.com/comments/?ids=<?=$url?>';
		var message_list='';
		jQuery.ajax({
                url:seo_url,
                type: "GET",
                dataType: "jsonp",
                success: function (seo_data) {
                        var result = eval(seo_data);
                        jQuery.each( result, function( key, value ) {
                                jQuery.each(value.comments.data, function( k, val ) {
                                        message_list = message_list+','+ val.message;
                                });
                        });
                        jQuery('#fb_seo').html(message_list);
                }
                });
	</script>
        <?
}
function fbcommentNum($page_url=NULL)
{
	if(empty($page_url))
        {
                $article_url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $urlArr = explode('?', $article_url);
                $url = $urlArr[0];
        }
        else
        {
                $urlArr = explode('?', $page_url);
                $url = $urlArr[0];
        }

?>
        <script>

		var data_url = 'https://api.facebook.com/method/fql.query?format=json&query=select commentsbox_count from link_stat where url = "<?=$url?>"';

		jQuery.ajax({
		url:data_url,
		type: "GET",
		dataType: "jsonp",
		success: function (data) {
			var arData = eval(data);
			var comment_count =arData[0].commentsbox_count;
			if(comment_count!='0')
			{
				if(comment_count>'1')
				{
					var comment_html = '<a href="<?=$articleFullURL?>#fb-comments" onclick="goToComments();">'+comment_count+' Comments </a>'
					jQuery('#fb_count').replaceWith(comment_html);
				}
				else
				{
					var comment_html = '<a href="<?=$articleFullURL?>#fb-comments" onclick="goToComments();">'+comment_count+' Comment </a>'
					jQuery('#fb_count').replaceWith(comment_html);
				}
			}
			else
			{
				var comment_html = '<a href="<?=$articleFullURL?>#fb-comments" onclick="goToComments();">Post Comments </a>'
				jQuery('#fb_count').replaceWith(comment_html);
			}
		}

		});
        </script>

	<?
}

 function fbcommentLayout()
 {
 	$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 	$urlArr = explode('?', $url);
 	$url = $urlArr[0];
 	?>
<script>
		  (function(d, s, id) {
		  	  var js, fjs = d.getElementsByTagName(s)[0];
		  	  if (d.getElementById(id)) return;
		  	  js = d.createElement(s); js.id = id;
		  	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=139123259501339";
		  	  fjs.parentNode.insertBefore(js, fjs);
		  	}(document, 'script', 'facebook-jssdk'));
</script>
	<div class="fb-comments" id="fb-comments" >
		<fb:comments  href="<?php echo $url; ?>" width="612"></fb:comments>

	</div>
 	<?php

 }

 function articleLayoutNoPhoto($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
 	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$relatedLinks = $article->related_links;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$articlePages =  count($articlebody);
	$audioFile = $article->audioFile;
	$layoutType = $article->layout;
	//$objDisqus = new disqusSys();
	?>
	<!-- display related links or not -->
	<div id="article-left" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
    <h1><div class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
    <div id="article-byline" class="KonaFilter">By
    <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
      <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text);?>

    </div>
    <div style="clear:both;"></div>
    <? } ?>
    <div id="article-body" class="article_text_body KonaBody">

<!-- begin related links -->
<div id="related-links-container" class="KonaFilter">
<?php if ($articlePages < 2 || $page == "full"){
		if($articlebody['0']['bodyCount']>'170'){
			$showLeftCol = '1';
		}else{
			$showLeftCol = '0';
		}
	}else{
		if ($page == "") {
			$page = "1";
		}
		$pageAd = ($page -1);
		if($articlebody[$pageAd]['bodyCount']>'170'){
			$showLeftCol = '1';
		}else{
			$showLeftCol = '0';
		}
	}

	if($showLeftCol=="1"){
		$arrRightCols	=	array('show_MediumRectangle_300x250_inContent','daily_recap_module');
		include("../_leftcolumn.htm");
	} ?>
</div>
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody"  id="article_content">
<?
$this->showContribIntroText();
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0)
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}

		}

        $body = $NewEncoding->Convert($body);
        print $body;
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
        $this->showContribPostText();
    }
    echo "<style>#pagination-container {display:none;};</style>";
} else {
	if ($page == "") {
		$page = "1";
	}
	$page = ($page -1);
	$body = $articlebody[$page][body];
	$body = str_replace("https://image", "http://image", $body);
	if(substr_count($body,"{FLIKE}") > 0)
	{
		$body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
	}
	if(substr_count($body,"{AUDIO}") > 0)
	{
		if($audioFile!='')
		{
			$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
		}
		else {
			$body = str_replace("{AUDIO}"," ", $body);
		}
	}
	$body = $NewEncoding->Convert($body);
	print $body;
	$this->showContribPostText();
}
?>
</span>
</div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
 <!-- bottom modules -->
<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
?>
</div>
<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>



<!-- end bottom modules -->

<div style="clear:both;"></div>
<?
if($article->is_live=='1'){
	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
<!--Discus Comment-->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
    <div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }		//---------------- No Photo Layout Ends --------------------------------

 function articleLayoutLargePhoto($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$audioFile = $article->audioFile;
	$layoutType = $article->layout;
	?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
    <h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
    <div id="article-byline" class="KonaFilter">By
    <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text);?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
    <div id="article-body" class="article_text_body KonaBody">
          <div id="large-photo">
                <? echo $featureImage ?>
                <? if ($caption == "NULL") { echo "hello"; } ?>
                <div class="caption">
                    <?=$caption?>
                </div>
            </div>
 	<!-- begin related links -->
	<div id="related-links-container" class="KonaFilter">
	<div class="related-links-shadow">
    <div id="related-links-module">
        <ul id="related-article-list">
            <li class="more-on">
		<div id="grv-personalization-56"></div>
		<script type='text/javascript'>
		  //<![CDATA[
		    (function(){
		    window.gravityInsightsParams = {
		      'type': 'content',
		      'action': '',
		      'site_guid': 'f2da3b8baad72fa822dcde1dfc870b98'
		    };
		    var b,c,d,e,f,g,h,i,j;f=(c=!0===gravityInsightsParams.useGravityUserGuid?1:0)?"":gravityInsightsParams.user_guid||(null!=(h=/grvinsights=([^;]+)/.exec(document.cookie))?h[1]:void 0)||"";g=(d=(null!=(i=window.jQuery)?null!=(j=i.fn)?j.jquery:void 0:void 0)||"",b=gravityInsightsParams.ad||"","http://rma-api.gravity.com/v1/api/intelligence/wl?jq="+d+"&sg="+gravityInsightsParams.site_guid+"&ug="+f+"&ugug="+c+"&pl=56&id=grv-personalization-56&type=iframe&ad="+b);
		    window.grvMakeScript=function(k){var a;a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src=k;return a};e=document.getElementsByTagName("script")[0];g&&e.parentNode.insertBefore(window.grvMakeScript(g),e);})();
		  //]]>
		</script>
	    </li>
            <?
	    if($article->showBioLink){
	    ?>
            <li class="more-on more-author"><a href="/gazette/bios.htm?bio=<? echo $article->authorid?>"> More by <? echo $article->author?></a></li>
            <?
            echo $article->showAlso;
        	} ?>
        </ul>
        <div id="related-buzz-ad">
            <b><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">What's the Buzz?</a></b><br><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">30 top traders on these stocks and more</a>
        </div>
        <div id="related-stocks">
        </div>
        <div style="clear:both"></div>
        <div id="symbol-form">
                <table cellpadding="0" cellaspacing="0"><tr><td width="110px">
                <input id="stock-symbol" type="text" value="Symbols" onfocus="javascript:if(this.value=='Symbols') this.value=''; return false;"/>
                <img id="stock-submit" src="<?=$IMG_SERVER;?>/images/articles/stock-go-button.gif"/></td><td>
                <div id="symbol-form-ad">
                                 <?php CM8_ShowAd($cm8_ads_Button_160x30);  ?>
                </div>
                </td></tr></table>
        </div>
    </div>
</div>
</div>
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody" id="article_content">
<?
 $this->showContribIntroText();
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    $this->showContribPostText();
    echo "<style>#pagination-container {display:none;};</style>";
} else {
	if ($page == "") {
		$page = "1";
	}
	$page = ($page -1);
	$body = $articlebody[$page][body];
	$body = str_replace("https://image", "http://image", $body);
	if(substr_count($body,"{FLIKE}") > 0)
	{
		$body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
	}
	if(substr_count($body,"{AUDIO}") > 0)
	{
		if($audioFile!='')
		{
			$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
		}
		else {
			$body = str_replace("{AUDIO}"," ", $body);
		}

	}
	$body = $NewEncoding->Convert($body);
	print $body;
	$this->showContribPostText();
}
?>
</span>
</div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->

<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>
<? $from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }		//---------------- Large Photo Layout Ends --------------------------------

 function articleLayoutSmallPhoto($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
 	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$audioFile = $article->audioFile;
	$layoutType = $article->layout;
	?>

	<div id="article-left" itemscope itemtype="http://schema.org/Article" >
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
    <h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
   <div id="article-byline" class="KonaFilter">By
    <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>

	<? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
    <div id="article-body" class="article_text_body KonaBody">
      <!-- begin related links -->
	<div id="related-links-container" class="KonaFilter">
        <div id="small-photo">
            <? echo $featureImage ?>
            <? if ($caption != NULL or $caption != "") {  ?>
                <div class="caption">
                                <?=$caption?>
                            </div>
            <? } ?>
    </div>
    <div style="clear:both;"></div>

	<div class="related-links-shadow">
    <div id="related-links-module">
       <ul id="related-article-list">
        <li class="more-on">
		<div id="grv-personalization-56"></div>
		<script type='text/javascript'>
		  //<![CDATA[
		    (function(){
		    window.gravityInsightsParams = {
		      'type': 'content',
		      'action': '',
		      'site_guid': 'f2da3b8baad72fa822dcde1dfc870b98'
		    };
		    var b,c,d,e,f,g,h,i,j;f=(c=!0===gravityInsightsParams.useGravityUserGuid?1:0)?"":gravityInsightsParams.user_guid||(null!=(h=/grvinsights=([^;]+)/.exec(document.cookie))?h[1]:void 0)||"";g=(d=(null!=(i=window.jQuery)?null!=(j=i.fn)?j.jquery:void 0:void 0)||"",b=gravityInsightsParams.ad||"","http://rma-api.gravity.com/v1/api/intelligence/wl?jq="+d+"&sg="+gravityInsightsParams.site_guid+"&ug="+f+"&ugug="+c+"&pl=56&id=grv-personalization-56&type=iframe&ad="+b);
		    window.grvMakeScript=function(k){var a;a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src=k;return a};e=document.getElementsByTagName("script")[0];g&&e.parentNode.insertBefore(window.grvMakeScript(g),e);})();
		  //]]>
		</script>
	</li>
        <?  if($article->showBioLink){	?>
            <li class="more-on more-author"><a href="/gazette/bios.htm?bio=<? echo $article->authorid?>"> More by <? echo $article->author?></a></li>
            <?
            echo $article->showAlso;
        	} ?>
        </ul>
        <div id="related-buzz-ad">
            <b><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">What's the Buzz?</a></b><br><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">30 top traders on these stocks and more</a>
        </div>
        <div id="related-stocks">
        </div>
        <div style="clear:both"></div>
        <div id="symbol-form">
                <table cellpadding="0" cellaspacing="0"><tr><td width="110px">
                <input id="stock-symbol" type="text" value="Symbols" onfocus="javascript:if(this.value=='Symbols') this.value=''; return false;"/>
                <img id="stock-submit" src="<?=$IMG_SERVER;?>/images/articles/stock-go-button.gif"/></td><td>
                <div id="symbol-form-ad"><?php CM8_ShowAd($cm8_ads_Button_160x30); ?></div>
                </td></tr></table>
        </div>
    </div>
</div>
</div>
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody"  id="article_content">
<?
 $this->showContribIntroText();
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    $this->showContribPostText();
    echo "<style>#pagination-container {display:none;};</style>";
} else {
        if ($page == "") {
			$page = "1";
		}
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        $this->showContribPostText();
     }
?>
</span>
</div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
    foreach ($articlebody as $entry) {
		$currentPage = $page + 1;
		if ($currentPage == $i) {
		   echo "<li class='page-on'>$i</li>";
		} else {
		   echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
		}
    $i++;
	}
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->
<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>
<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
<? } ?>
</div>
<!--Discus Comment-->
  <!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }	//---------------- Small Photo Layout Ends --------------------------------

 function articleLayoutNoRelated($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
 	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
 	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$audioFile = $article->audioFile;
	$layoutType = $article->layout;
 ?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article" >
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
    <h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
	<div id="article-byline" class="KonaFilter">By
    <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author"><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author">'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>

	<? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
    <div id="article-body" class="article_text_body KonaBody">
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody" id="article_content">
<?
 $this->showContribIntroText();
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    $this->showContribPostText();
    echo "<style>#pagination-container {display:none;};</style>";
} else {
                    if ($page == "") {
        $page = "1";
    }
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        $this->showContribPostText();
     }
?>
</span></div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->
<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>

<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }				//---------------- No Related Layout Ends --------------------------------

 function articleLayoutNoByline($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
 	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
 	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$audioFile = $article->audioFile;
	$layoutType = $article->layout;
 ?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article" >
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
    <h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
    <div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
    <div id="article-body" class="article_text_body KonaBody">
	<!-- begin related links -->
	<div id="related-links-container" class="KonaFilter">
	<div class="related-links-shadow">
    <div id="related-links-module">
        <ul id="related-article-list">
         <li class="more-on">
		<div id="grv-personalization-56"></div>
		<script type='text/javascript'>
		  //<![CDATA[
		    (function(){
		    window.gravityInsightsParams = {
		      'type': 'content',
		      'action': '',
		      'site_guid': 'f2da3b8baad72fa822dcde1dfc870b98'
		    };
		    var b,c,d,e,f,g,h,i,j;f=(c=!0===gravityInsightsParams.useGravityUserGuid?1:0)?"":gravityInsightsParams.user_guid||(null!=(h=/grvinsights=([^;]+)/.exec(document.cookie))?h[1]:void 0)||"";g=(d=(null!=(i=window.jQuery)?null!=(j=i.fn)?j.jquery:void 0:void 0)||"",b=gravityInsightsParams.ad||"","http://rma-api.gravity.com/v1/api/intelligence/wl?jq="+d+"&sg="+gravityInsightsParams.site_guid+"&ug="+f+"&ugug="+c+"&pl=56&id=grv-personalization-56&type=iframe&ad="+b);
		    window.grvMakeScript=function(k){var a;a=document.createElement("script");a.type="text/javascript";a.async=!0;a.src=k;return a};e=document.getElementsByTagName("script")[0];g&&e.parentNode.insertBefore(window.grvMakeScript(g),e);})();
		  //]]>
		</script>
	</li>
        <? if($article->showBioLink){	?>
            <li class="more-on more-author"><a href="/gazette/bios.htm?bio=<? echo $article->authorid?>"> More by <? echo $article->author?></a></li>
            <?
            echo $article->showAlso;
        	} ?>
        </ul>
        <div id="related-buzz-ad">
            <b><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">What's the Buzz?</a></b><br><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">30 top traders on these stocks and more</a>
        </div>
        <div id="related-stocks">
        </div>
        <div style="clear:both"></div>
        <div id="symbol-form">
                <table cellpadding="0" cellaspacing="0"><tr><td width="110px">
                <input id="stock-symbol" type="text" value="Symbols" onfocus="javascript:if(this.value=='Symbols') this.value=''; return false;"/>
                <img id="stock-submit" src="<?=$IMG_SERVER;?>/images/articles/stock-go-button.gif"/></td><td>
                <div id="symbol-form-ad">
                                 <?php CM8_ShowAd($cm8_ads_Button_160x30); ?>
                </div>
                </td></tr></table>
        </div>
    </div>
</div>
</div>
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody" id="article_content">
<?
 $this->showContribIntroText();
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
		echo $body;
        //print html_entity_decode($body);
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    $this->showContribPostText();
    echo "<style>#pagination-container {display:none;};</style>";
} else {
                    if ($page == "") {
        $page = "1";
    }
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
		echo $body;
		$this->showContribPostText();
        //print html_entity_decode($body);
     }
?>
</span>
</div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->
<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>

<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }				//---------------- No Byline Layout Ends --------------------------------

 function articleLayoutLiveBlog($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
 {
	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$coveritLiveData	=	 $this->getCoverItLiveData($articleid);
	$layoutType = $article->layout;

 ?>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
	<div id="article-left" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
	<div id='article-heading'>
		<div id='article-heading-left'>
    		<h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
		</div>
		<div id='article-heading-right' style="float:right; vertical-align:top;">
			<img src="<?=$IMG_SERVER;?>/images/articles/cover_it_live.jpg">
		</div>
	</div>
	<div style="clear:both"></div>
    <div id="article-byline" class="KonaFilter">By
    <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <!--<div id="article-dek" class="KonaFilter"><? echo mswordReplaceSpecialChars($article->character_text); ?></div>-->

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>

    <div id="article-body" class="article_text_body KonaBody">
        <div id='live-blog'>
		<div id="live-blog-left">
		<?php
			$contributorInfo = $this->getContributorImg($article->authorid);
			if($contributorInfo)
			{
				if($contributorInfo['bio_asset'] != "" && $contributorInfo['bio_asset'] != NULL ){
						$thumbImg	=	$IMG_SERVER.$contributorInfo['bio_asset'];
					}
					else {
						$thumbImg	=	$IMG_SERVER."/assets/bios/no-photo.jpg";
						}
			}
			else
			{
						$thumbImg	=	$IMG_SERVER."/assets/bios/no-photo.jpg";
			}
		?>
		<img src="<?=$thumbImg;?>" border=0  title="<?=ucwords($article->author);?>" width="78px">
		</div>
		<div id="live-blog-right">
		<div id='live-blog-dek'>
			<?=mswordReplaceSpecialChars($coveritLiveData['contr_live_login_desc']);?>
		</div>
		<div id='article-coverlive-icons'>
		<?php
			if($contributorInfo['email'] != ""){
			echo "<div style='float:left; width=300px; padding-right:15px;'>";
			echo "<img src='".$IMG_SERVER."/images/articles/icon-mail.gif'>&nbsp;";
			echo "<a href='mailto:".$contributorInfo['email']."'>Send ".$article->author." Questions</a>";
			echo "</div>&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if($contributorInfo['twitter_username'] != ""){
			echo "<div  style='float:left; width=300px;'>";
			echo "<img src='".$IMG_SERVER."/images/articles/icon-twitter.gif'>&nbsp;";
			echo "<a href='http://twitter.com/".$contributorInfo[twitter_username]."'>Follow ".$article->author." on Twitter</a>";
			echo "</div>";
			}
			?>
		</div>
		</div>
		</div>
		<div style="clear:both;"></div>
		<br />
		<!-- begin related links -->
		<div id="related-links-container" class="KonaFilter" style="display:none;">
		<div class="related-links-shadow">
    	<div id="related-links-module">
        <ul id="related-article-list">
        <? $totalRelatedLinks = count($relatedArticles);
                 if ($totalRelatedLinks != 0) {
        ?>
        <li class="more-on">Related Articles</li>
        <? foreach ($relatedArticles as $relatedArticle) {
                 echo "<li><a href='".$HTPFX.$HTHOST.getItemURL($relatedArticle['item_type'],$relatedArticle['article_id'])."'>".mswordReplaceSpecialChars($relatedArticle['title'])."</a></li>";
                            }
            ?>
        <? } if($article->showBioLink){	?>
            <li class="more-on more-author"><a href="/gazette/bios.htm?bio=<? echo $article->authorid?>"> More by <? echo $article->author?></a></li>
            <?
            foreach ($moreByArticles as $moreByArticle) {
                echo "<li><a href='".makeArticleslink($moreByArticle['id'])."'>".mswordReplaceSpecialChars($moreByArticle['title'])."</li>";
                $i++;
            }
        }
        ?>
        </ul>
        <div id="related-buzz-ad">
            <b><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">What's the Buzz?</a></b><br><a href="<?=$HTPFX.$HTHOST;?>/buzzbanter/">30 top traders on these stocks and more</a>
        </div>
        <div id="related-stocks">
        </div>
        <div style="clear:both"></div>
        <div id="symbol-form">
                <table cellpadding="0" cellaspacing="0"><tr><td width="110px">
                <input id="stock-symbol" type="text" value="Symbols" onfocus="javascript:if(this.value=='Symbols') this.value=''; return false;"/>
                <img id="stock-submit" src="<?=$IMG_SERVER;?>/images/articles/stock-go-button.gif"/></td><td>
                <div id="symbol-form-ad">
                                 <?php CM8_ShowAd($cm8_ads_Button_160x30); ?>
                </div>
                </td></tr></table>
        </div>
    </div>
</div>
</div>
<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div id="articleBodyContent">
<span itemprop="articleBody" id="article_content">
<?
 $this->showContribIntroText();
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    $this->showContribPostText();
    echo "<style>#pagination-container {display:none;};</style>";
} else {
                    if ($page == "") {
        $page = "1";
    }
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
		if(substr_count($body,"{AUDIO}") > 0 )
		{
			if($audioFile!='')
			{
				$body = str_replace("{AUDIO}","<div>".displayRadioInArticle($audioFile,$layoutType)."</div>", $body);
			}
			else
			{
				$body = str_replace("{AUDIO}"," ", $body);
			}
		}
        $body = $NewEncoding->Convert($body);
        print $body;
        $this->showContribPostText();
}
?>
</span></div>
<div style="clear:both;height:10px;"></div>

<div style="clear:both"></div>
<div id='cover-it-live-tool'>
<? if($coveritLiveData['cover_it_live']!='')
{	echo $coveritLiveData['cover_it_live'];	}	?>
</div>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<!--<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>-->
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->

<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
?>
</div>

<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
 <?php
 }				//---------------- Live Blog Layout Ends ------------------------------
 function articleLayoutTheStreet($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
  {
	global $thestreetRelatedLinks,$RMSheadlineURL,$IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
    $rss = new rss_php;
	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$articleType	= $this->getTheStreetArticle($articleid);
	// For Strret Articles added mannually from create article Interface
	if(!$articleType)
	{
		$articleType['thestreet_article_type'] = $article->layout;
	}
	if($articleType)
	{
		if($articleType['thestreet_article_type'] == 'realmoneysilver')
		{
			$theStreetLogo	=	'realMoney.jpg';
			$authorlink1	=	'http://secure2.thestreet.com/cap/prm.do?OID=015582&amp;puc=minyanvilletsc&amp;cm_ven=minyanvillets';
			$authorlink2	=	'http://realmoneypro.thestreet.com/?puc=minyanvilletsc&amp%3bcm_ven=minyanvillets';
			$rmsCache				= 	new Cache();
			$rmsRelatedLinkId		=	"thestreet_2";
			$rmsrelatedLinks	=	$rmsCache->getRealMoneySilverCache($rmsRelatedLinkId,$RMSheadlineURL,$rss);
		}
		elseif($articleType['thestreet_article_type'] == 'thestreet')
		{
			$theStreetLogo			=	'thestreetlogo_1.jpg';
			$authorlink				=	'http://www.thestreet.com?puc=minyanvilletsc&amp;cm_ven=minyanvillets';
			$relatedLinksCache_TS	= 	new Cache();
			$tsRelatedLinkId		=	"thestreet_1";
			$relatedLinksTS				=	$relatedLinksCache_TS->getTheStreetCache($tsRelatedLinkId,$thestreetRelatedLinks,$rss);
		}
	}
	$facebookImage = $IMG_SERVER.'/images/thestreet/'.$theStreetLogo;

 ?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article" >
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" ><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
	<div class="street_article_body" style="padding:5px 5px 0px 5px;">
	<?php
	if($articleType['thestreet_article_type'] == 'thestreet')
	{
	?>
	<div id='article-heading'>
		<div id='thestreet-heading-left'>
    		<h1><div class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
		</div>
		<div id='thestreet-heading-right' style="vertical-align:top;">
		<a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$authorlink;?>','TheStreet Logo');">
		<img src="<?=$IMG_SERVER;?>/images/thestreet/<?=$theStreetLogo;?>"  /></a>
		</div>
	</div>
	<div style="clear:both"></div>
    <div id="article-byline" class="KonaFilter">By
	<a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$authorlink;?>','TheStreet Auhtor');">
    <? echo '<span itemprop="author" >'.$article->author.'</span>'; ?></a>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
    <?php
	}
	if($articleType['thestreet_article_type'] == 'realmoneysilver')
	{?>
	<div id='article-heading'>
		<div id='thestreet-heading-left'>
    		<h1><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></h1>
		</div>
		<div id='thestreet-heading-right' style="vertical-align:top;">
		<a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$authorlink2;?>','RealMoneySilver Logo');">
		<img src="<?=$IMG_SERVER;?>/images/thestreet/<?=$theStreetLogo;?>"  /></a>
		</div>
	</div>
	<div style="clear:both"></div>
    <div id="article-byline" class="KonaFilter">By
	<?
	$expAuthor	=	explode(",",$article->author);
	$authorName	=	$expAuthor[0];
	$authorSite	=   $expAuthor[1];
	?>
	<a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$authorlink1;?>','RealMoneySilver Author');"><?=$authorName;?></a> ,
	<a href="#" onclick="javascript:trackTSClick('Link Outs','<?=$authorlink2;?>','RealMoneySilver Author');"><?=$authorSite;?></a>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
	<?php
	}
	?>
	<!--<div id="article-dek" class="KonaFilter"><? echo mswordReplaceSpecialChars($article->character_text); ?></div>-->

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
	</div>
	<div id="article-body-theStreet" class="article_text_body KonaBody" style="padding-top:0px; vertical-align:top; margin-top:0px;">

<style>
#one-page {
margin:8px 20px 0px 15px;
float:left;

}
</style>
<!-- end related links -->
<div class="street_article_body" style="padding:5px 5px 0px 5px;" id="articleBodyContent">
<span itemprop="articleBody" id="article_content">
<?php
global $IMG_SERVER;
	$strRelatedLinks='';
	if($articleType['thestreet_article_type'] == 'realmoneysilver')
	{
		if(count($rmsrelatedLinks)>0)
		{
			$strRelatedLinks	=   "<div class='ts-article-right-module'><div class='ts-heading-right-box'><img src='".$IMG_SERVER."/images/thestreet/related_links.gif' alt='Related Links' /></div><div class='ts-display-product-module-shadow'><div  class='ts-module-body'><table width='100%' border='0' cellpadding='2' cellspacing='2'>";
			foreach($rmsrelatedLinks as $key=>$value)
			{
				$strRelatedLinks .= "<tr><td><a href='#' onclick=\"javascript:trackTSClick('Link Outs','".$value."','RealMoneySilver Link');\"><span style='color:#00509e;'>".$key."</span></a></td>	</tr>";
			}
			$strRelatedLinks .="</table></div></div></div>";
		}
	}
	elseif($articleType['thestreet_article_type'] == 'thestreet')
	{
		if(count($relatedLinksTS)>0)
		{
			$strRelatedLinks	= "<div class='ts-article-right-module'><div class='ts-heading-right-box'><img src='".$IMG_SERVER."/images/thestreet/related_links.gif' alt='Related Links' /></div><div class='ts-display-product-module-shadow' ><div class='ts-module-body'><table width='100%' border='0' cellpadding='2' cellspacing='2'>";
			foreach($relatedLinksTS as $key=>$value)
			{
			$strRelatedLinks .= "<tr><td><a href='#' onclick=\"javascript:trackTSClick('Link Outs','".$value."','TheStreet Link');\"><span style='color:#00509e;'>".$key."</span></a></td>	</tr>";
	 		}
			$strRelatedLinks .="</table></div></div></div>";
		}
	}
?>
<?
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];

        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
        $body  = $NewEncoding->Convert($body);

 		/*** the third occurance ***/
		//preg_match_all($search, $body, $matches);

		$decodeBodyPos='';
		$body=str_replace("&amp;amp;","&",$body);
		$body=str_replace("&#039;&quot;",'"',$body);
		$body=str_replace("&quot;&#039;",'"',$body);
		$decodedBody= html_entity_decode($body);

		$decodeBody	=	$this->filter_urls($decodedBody);

		$search="<P/>";
		$string=$decodeBody;
		$offset=3;
		$decodeBodyPos=strposOffset($search, $string, $offset);
		if($decodeBodyPos=='')
		{
			$search="</p>";
			$offset=2;
			$decodeBodyPos=strposOffset($search, $string, $offset);
		}

		if($decodeBodyPos>0)
		{
		$decodedBody1=substr($decodeBody,0,$decodeBodyPos);
		$decodedBody3=substr($decodeBody,$decodeBodyPos);
		$decodedBody=$decodedBody1.$strRelatedLinks.$decodedBody3;
		}
		else
		{
		$decodedBody=$strRelatedLinks.$decodeBody;

		}
		echo html_entity_decode($decodedBody);
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    echo "<style>#pagination-container {display:none;};</style>";
 }
 else
 {
        if ($page == "") {
    	    $page = "1";
 	    }
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
	    $body  = $NewEncoding->Convert($body);

		$decodeBodyPos='';
		$body=str_replace("&amp;amp;","&",$body);
		$body=str_replace("&#039;&quot;",'"',$body);
		$body=str_replace("&quot;&#039;",'"',$body);
		$decodedBody= html_entity_decode($body);
		$decodeBody	=	$this->filter_urls($decodedBody);

		$search="<P/>";
		$string=$decodeBody;
		$offset=3;
		$decodeBodyPos=strposOffset($search, $string, $offset);
		if($decodeBodyPos=='')
		{
			$search="</p>";
			$offset=2;
			$decodeBodyPos=strposOffset($search, $string, $offset);
		}


		if($decodeBodyPos>0)
		{
		$decodedBody1=substr($decodeBody,0,$decodeBodyPos);
		$decodedBody3=substr($decodeBody,$decodeBodyPos);
		$decodedBody=$decodedBody1.$strRelatedLinks.$decodedBody3;
		}
		else
		{
		$decodedBody=$strRelatedLinks.$decodeBody;
		}
		echo html_entity_decode($decodedBody);
}
?>
</span>
</div>
<div style="clear:both;"></div>
<div>
</td>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
          foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
                 }
                $i++;
                }
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<!--<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>-->
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->

<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>

<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>

<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>

<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
</div>
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>

 <?php
 }		//---------------- TheStreet Layout Ends --------------------------------

  function articleLayoutObserver($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page)
  {
	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	//$commentArray = $this->getArticleLayoutComments($objThread ,$articlecache,$articleid,$article);
	//$appcommentcount = count($commentArray);
	?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article" >
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
	<div class="KonaFilter" ><?=showBreadCrum($breadcrum); ?></div>
	<div id="article-content">
	<div class="observer_article_body" style="padding:5px 3px 0px 3px;">
	<div id='article-heading'>
		<div id='observer-heading-left'>
    		<h1><div class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
		</div>
		<div id='observer-heading-right' style="vertical-align:top;">
			<a href="http://www.observer.com/"><img src="<?=$IMG_SERVER;?>/images/observer/logo_observer.png" alt="The New York Observer" /></a>
		</div>
	</div>
	<div style="clear:both"></div>
    <div id="article-byline" class="KonaFilter">By
	 <? if(!empty($article->g_plus_link))
    {
    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
    }
    else if($article->showBioLink){ ?>
        <span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
    <? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
    <? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>

    <? $this->iconBar($articleid,'top');?>

    <div style="clear:both;"></div>
     <? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text); ?>
    </div>
    <div style="clear:both;"></div>
    <? } ?>
	</div>
	<div id="article-body-observer" class="article_text_body KonaBody" style="padding-top:0px; vertical-align:top; margin-top:0px;">
	 <style>
	#one-page {
	margin:8px 20px 0px 15px;
	float:left;
	}
	</style>
<!-- end related links -->
<div class="observer_article_body" style="padding:5px 5px 0px 5px;" id="articleBodyContent">
<span itemprop="articleBody"  id="article_content">
<?
global $IMG_SERVER;
 $articlePages =  count($articlebody);
 if ($articlePages < 2 || $page == "full")
 {
    foreach ($articlebody as $keyIndex => $entry)
    {
        $body = $entry['body'];

        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
        $body  = $NewEncoding->Convert($body);

		$decodedBody= html_entity_decode($body);
		echo html_entity_decode($decodedBody);
        if($keyIndex != $articlePages-1)
        {
            print "<br /><br />";
        }
    }
    echo "<style>#pagination-container {display:none;};</style>";
 }
 else
 {
        if ($page == "") {
    	    $page = "1";
 	    }
        $page = ($page -1);
        $body = $articlebody[$page][body];
        $body = str_replace("https://image", "http://image", $body);
        if(substr_count($body,"{FLIKE}") > 0)
        {
            $body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
        }
	    $body  = $NewEncoding->Convert($body);
		$decodedBody= html_entity_decode($body);
		echo $decodedBody;
}
?>
</span>
</div>
<div style="clear:both;"></div>
<div>
</td>
<div style="clear:both;"></div>
<div id="pagination-container" class="KonaFilter">
<div class="prev-next">
<?
if ($page == "" || $page == "0") {
    echo "&lt; Previous";
} else {
    echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
}
?>
</div>
<ul id="page-numbers">
<?
 $i = 1;
 foreach ($articlebody as $entry) {
 $currentPage = $page + 1;
 if ($currentPage == $i) {
    echo "<li class='page-on'>$i</li>";
 } else {
                    echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
        }
    $i++;
}
?>
</ul>
<div class="prev-next">
<?
$nextPage = $page + 2;
if ($nextPage > $articlePages) {
    echo "Next &gt;";
} else {
    echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
}
?>
</div>
<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
</div>
<div style="clear:both;"></div>
<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
<!--<div id="twitter-follow">
<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
</div>-->
<div style="clear:both;"></div>
<div id="article-disclaimer" class="KonaFilter">
<? echo $article->disclaimer ?>
</div>
<? if(!$_SESSION['AdsFree']) { ?>
    <div id="text-ad-container">
    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
    </div>
    <div style="clear:both;"></div>
  <?  } ?>
  <!-- bottom modules -->
<div style="margin:10px 0 0 0;">
<?
$this->iconBar($articleid,'bottom');
$this->emailBar();
?>
<? if(!$_SESSION['AdsFree']) { ?>
     <div id="text-ad-container">
    <?php CM8_ShowAd('adbladeNewCode'); ?>
    </div>
    <div style="clear:both;"></div>
 <? } ?>
<div id="bottom-module-container" style="float:left" class="KonaFilter">
<?=$this->getWidget($articleid);?>
</div> <!-- bottom module container -->
<?
$this->bottomIconWidget();
 ?>
</div>

<?
$from = $_GET['from'];
if ($from == 'yahoo') { ?>
<div id="return-to">
    <a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
</div>
<div style="clear:both;"></div>
<? } ?>
    </div>


<!-- end bottom modules -->

<div style="clear:both;"></div>
<? if($article->is_live=='1'){ 	?>
<div id="fbcomment_layout">
<div id="fb_seo">&nbsp;</div>
<?php $this->FbcommentSEO();
$this->fbcommentLayout();
?>
</div>
<? } ?>
<!--Discus Comment-->
<!-- <div id="disqus_thread" class="disqusComment"></div> -->
</div> <!-- end article-content -->
</div> <!-- end article-left -->
</div>
<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php
 }		//---------------- Observer Layout Ends --------------------------------


 function getArticleLayoutTags($objThread,$articleid)			// TAG CODE
 {
	$item_type_id=$objThread->get_object_type('Articles');
	$tags = $objThread->get_tags_on_objects($articleid, $item_type_id);
	if(is_array($tags))
	{
		foreach($tags as $key=>$val)
		{
			$thread['tag_id']=$val[id];
			$thread['item_id']=$threadid;
			$thread['item_type']="4";
			$threadTagqry = "select id from ex_item_tags where tag_id='$val[id]' and item_id = '$threadid'";
			$threadTagres=exec_query($threadTagqry);
			$count=count($threadTagres);
			if($count=='0'){
				$threadTag=insert_query("ex_item_tags", $thread);
			}
		}
	}
}
function getArticleLayoutComments($objThread ,$articlecache,$articleid,$article)	// GET ARTICLES Comments
{
	$threadArray = $objThread->get_thread_on_object($articleid);
	$this->threadid =$threadid = $threadArray['id'];
	if($threadid=="" and $article->approve=='1')
	{
		global $defaultSudId;
		$currDate=date('Y-m-d, H:i:s');
		$threadtitle=htmlentities($article->title,ENT_QUOTES);
		$threadval[title]=$threadtitle;
		$threadval[thread_posts]='0';
		$threadval[approved]='1';
		$threadQryresult = "SELECT character_text as body,section_id as section_id FROM `articles` WHERE id ='$article->id'";
		$threadQry=exec_query($threadQryresult,1);
		$threadbody=htmlentities($threadQry[body],ENT_QUOTES);
		$threadval[teaser] = $threadbody;
		$threadval[section_id] = $threadQry[section_id];
			$threadqry = "SELECT subscription_id as subid FROM `ex_contributor_subscription_mapping` WHERE contributor_id='$article->authorid'";
			$threadQry=exec_query($threadqry,1);
		$threadval[author_id]=$threadQry[subid];
			if(!$threadval[author_id]){
			$threadval[author_id]=$defaultSudId;
			}
		$threadval[created_on]=$currDate;
		$threadval[content_table]=$itemtype;
		$threadval[content_id]=$article->id;
		$threadval[is_user_blog]=0;
		if($threadval[content_id]){
			$threadid=insert_query("ex_thread",$threadval);
		}
	}
	$discussionarticle=$articlecache->getArticleDiscussionCache($threadid,$articleapprove=1);
	if(!is_object($discussionarticle)){  // if memcache expires set cache again
		$discussionarticle=$articlecache->setArticleDiscussionCache($threadid,$articleapprove=1);
	}
	$commentArray = $discussionarticle->comments;
	return $commentArray;
}

function getArticlesbyAuthor($author, $limit) {
	$sql = "SELECT articles.id id, articles.title, articles.keyword FROM articles WHERE contrib_id = '".$author."' and approved = '1'and is_live='1' order by date desc limit ".$limit;
	$results = exec_query($sql);
    return $results;
}
function getLatestModule()
{

global $IMG_SERVER;
$sql_cat_list="select a.title as article,a.id as articleid,a.keyword,a.character_text,
                 h.object_type,m.id as vid,m.title,h.id as hid,h.image_path,
                 m.thumbfile from homepage_module as h left join  articles as a on h.object_id = a.id
                 left join mvtv as m on h.object_id = m.id
                 where h.module_type='Recent' and  h.commit_status='P' order by h.order_type limit 3";
$result_cat_list= exec_query($sql_cat_list);
//print_r($result_cat_list);
	 foreach ($result_cat_list as $articles) {
		$title =  $articles['article'];
		$image =  $articles['image_path'];
		$keywords = $articles['keyword'];
		$keywords = str_replace("'", "", "$keywords");
		$id =  $articles['articleid'];
		$dek = $articles['character_text'];
		echo "<li>";
		echo "<a href='".makeArticleslink($id)."'><img src='".$IMG_SERVER."/assets/featureimage/".$image."' width='79'/></a>";
		echo "<div class='subscriptions-header'>";
		echo "<a href='".makeArticleslink($id)."'>".mswordReplaceSpecialChars($title)."</a>";
		echo "</div>";
		echo "<div class='subscriptions-dek'>";
		echo "<a href='".makeArticleslink($id)."'>".mswordReplaceSpecialChars($dek)."</a>";
		echo "</div>";
		echo "</li>";
	}
}

	function getContributorImg($authorid)
	{
		/*$sql = "SELECT email,small_bio_asset,twitter_username FROM contributors WHERE id = ".$authorid." and has_bio = '1' and suspended='0' ";*/
		$sql = "SELECT email,small_bio_asset,bio_asset,twitter_username FROM contributors WHERE id = ".$authorid." and suspended='0' ";
		$results = exec_query($sql,1);
		return $results;
	}

	function getCoverItLiveData($articleid)
	{
		$sql = "SELECT cover_it_live,contr_live_login_desc FROM article_coveritlive WHERE article_id = '".$articleid."'";
		$results = exec_query($sql,1);
		return $results;
	}

	function getTheStreetArticle($articleid)
	{
		$sql = "SELECT thestreet_article_type FROM article_thestreet WHERE article_id = '".$articleid."'";
		$results = exec_query($sql,1);
		return $results;
	}

  function filter_urls($body)
  {
   	$tag_start='<a';
	$tag_close='</a>';
	$tag_start_end='>';
	$length_end_tag=strlen($tag_close);

	$offsetposition=0;
	$offset_quotes=0;
	$counter=0;
	//array initialized for file
	$field=array();
	// CSV white listed file is read
	$body_in_small	= strtolower($body);
	//finds the  occurence of href tag in the body
    $no_of_links=substr_count($body_in_small,$tag_start);
	$findAnchor=array();
	$replacedAnchor=array();
	 if ($no_of_links>0)
	 {
	 	while($counter<$no_of_links)
		{
			  // finds the posistion of each link in the body
			  $tag_start_pos=strpos($body_in_small,$tag_start,$offsetposition);
			  // finds the position of lenght of href tag in the body
			  $tag_close_pos=strpos($body_in_small,$tag_close,$offsetposition);
			  // echo "<br>tag_close_pos=".$tag_close_pos."<br>";
			  // extract the text from url

			  $anchorBody = substr($body,$tag_start_pos,$tag_close_pos - $tag_start_pos + $length_end_tag);


			  //echo "<br>"."anchorBody-- ".$anchorBody;

			  $findAnchor[$counter]=$anchorBody;

			  $replaced_anchor=$this->change_mvil_link($anchorBody,$body);

			//  echo "<br>".'replaced_anchor---- '.$replaced_anchor;

			  $replacedAnchor[$counter]=$replaced_anchor;

			  $offsetposition=$tag_close_pos+1;

			  $counter++;

		}
		$replacedBody= str_replace($findAnchor,$replacedAnchor,$body);
		return $replacedBody;
	 }
	 else
	 {
		return $body;
	 }
  }

  function change_mvil_link($anchorBody,$body)
  {
	$updated_anchor=$this->change_anchor($anchorBody);
	return $updated_anchor;
	// return str_replace($anchorBody,$updated_anchor,$body);
  }

  function change_anchor($anchorBody)
  {

	$url_patteren='#<a\s+href=[\'"]([^\'"]+)[\'"]\s*>((?:(?!</a>).)*)</a>#i';
	preg_match_all($url_patteren, $anchorBody, $matches);
	/*echo "<pre>";
	print_r($matches);
	echo "</pre>";*/
	$startpos = strpos($matches[1][0], "http://www.thestreet.com");
	$passVar=substr($matches[1][0], $startpos-1,1);
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "www.thestreet.com");
		$passVar=substr($matches[1][0], $startpos-8,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "http://thestreet.com");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "https://www.thestreet.com");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "http://www.realmoneysilver.com");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "http://secure2.thestreet.com");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "http://www.mainstreet.com");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[1][0], "http://www.thestreet.com/");
		$passVar=substr($matches[1][0], $startpos-1,1);
	}
	if(!$startpos)
		return $anchorBody;

	/*$endpos=strpos(substr($matches[1][0], $startpos, strlen($matches[1][0])-$startpos),$passVar);

	if(!$endpos)
			$endpos=strpos(substr($matches[1][0], $startpos, strlen($matches[1][0])-$strpos),'\'');*/

	$endpos=strlen($matches[1][0]);


	$rest ='http://'.substr($matches[1][0], $startpos, $endpos);
	//echo $rest."<br>";
	$str_hash_pos=strpos($rest,'#');

	$value="";

	if($str_hash_pos){
		$value=substr($rest,0,$str_hash_pos)."?puc=minyanvilletsc&amp;cm_ven=minyanvillets".substr($rest,$str_hash_pos,strlen($rest)-$str_hash_pos);
	}elseif(strpos($rest,'?')){
	    $value= $rest."&puc=minyanvilletsc&amp;cm_ven=minyanvillets";
	}elseif(strpos($rest,'/articles/') and strpos($rest,'/id/')){
	    $value= $rest."?puc=minyanvilletsc&amp;cm_ven=minyanvillets";
	}
	else{
		$value= $rest."?puc=minyanvilletsc&amp;cm_ven=minyanvillets";
    }


	$replacedLink="<a href='#' onclick=\"javascript:trackTSClick('Link Outs','".$value."','TheStreet Link');\">";


	//$pattern='/<a.*?href=[""\'](.*)[""\'](.*)>$/';
	$pattern = '#<a\s+href=[\'"]([^\'"]+)[\'"]\s*>#i';

	$anchorBodyReplaced=preg_match($pattern, $anchorBody,$matches);
	$replacedLink=$replacedLink.substr($matches[2],1);
	$anchorBodyReplaced=preg_replace($pattern, $replacedLink, $anchorBody);
	return $anchorBodyReplaced;
	}

	function replaceVideoEmbedCode($body){
		global $STORAGE_SERVER;
	    $findString=$STORAGE_SERVER.'/mvtv/videos';
		$findPos=strpos($body,$findString);
		if($findPos){
			$videoText=substr($body,$findPos);
			$findUrlPos=strpos($videoText,'"');
			$videoUrl=substr($videoText,0,$findUrlPos);
			$qry="select permalink from mvtv where videofile='".$videoUrl."'";
			$pattern="<script[^>]*>.*</script>";
		}elseif(strpos($body,'mvtv/player/MVTV')){
			$findSwfPos=strpos($body,'swf?vid=');
			$videoText=substr($body,$findSwfPos);
			$videoId=substr($videoText,strpos($videoText,'=')+1,strpos($videoText,'timestamp=')-13);
			$qry="select permalink from mvtv where id='".$videoId."'";
			$pattern="<object[^>]*>.*</object>";
		}
		$result=exec_query($qry,1);
		if($result['permalink'] && ($findPos || $findSwfPos)){
			$PERMALINK=$result['permalink'];
			$PLAYERWIDTH="600";
			$PLAYERHEIGHT="500";
			$replaceText='<iframe src="'.$PERMALINK.'/player?layout=compact" width="'.$PLAYERWIDTH.'" height="'.$PLAYERHEIGHT.'" frameborder="0" scrolling="no"></iframe>';
			$body = eregi_replace($pattern,$replaceText,$body);
		}
		return $body;
	}
	function getArticleBitlyUrl($id){
		$qry="select bitly_url from ex_item_meta where item_id='".$id."' and item_type='1'";
		$result=exec_query($qry,1);
		if($result){
			return $result['bitly_url'];
		}
	}

	function getWidget($id)
	{
		$article_ads = array("OUTBRAIN", "NRELATE");
        $article_rand_ads = array_rand($article_ads, 1);
        if($article_ads[$article_rand_ads]=="OUTBRAIN")
		{
			$this->outbrainWidget();
		}
		else
		{
			$this->nRelateWidget($id);
		}
	}

	function nRelateWidget($id)
	{
		?>
		<!-- Placeholder -->
		<div id="nrelate_related_placeholder"></div>
		<!-- JS Code -->
		<script type="text/javascript">var nr_domain = "www.minyanville.com"; </script>
		<script async data-nrelate-options="{ plugins : { related : { 'widget_id' : '488', 'page_type' : 'article', 'geo' : '', 'article_id' : '<?=$id?>' } } }" id="nrelate_loader_script" type="text/javascript" 
			src="http://static.nrelate.com/common_js/0.52.1/loader.min.js"></script>
		<?
	}

function outbrainWidget()
{
global $HTPFX,$HTHOST,$articleFullURL;
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
padding: 10px 0 20px 0;
}
.outbrain-recommendationsFieldset, .outbrain-recommendationsFieldset-ie, .outbrain-recommendationsFieldset-sc {
border: medium none !important;
padding-left:0px !important;
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
<div class="OUTBRAIN" data-src="DROP_PERMALINK_HERE" data-widget-id="AR_2" data-ob-template="minyanville" ></div>
<script type="text/javascript" src="http://widgets.outbrain.com/outbrain.js"></script>

<?
}

function emailBar()
{
global $IMG_SERVER;

$readonly ='';
if($_SESSION['SID']!="")
{
	$readonly="readonly";
}

if($_SESSION['email']!="")
{
	$email = $_SESSION['email'];
}
else
{
	$email = "enter your email";
}

?>

<div class="main_outer">
	<div class="nLS-com">
		<h3 class="leadHed"><span class="green-txt">Busy?</span> Subscribe to our free newsletter!</h3>

		<div class="news_bottom_inner">
		<input type="text" onfocus="clearNewsletterText('user_email','enter your email');" id="user_email" class="enter_name"  value="<?php echo $email; ?>" <?=$readonly?> />
		</div>
		
		<input type="hidden" name="subscriber_id" id="subscriber_id" value="<?=$_SESSION['user_id'];?>">
		<!-- <div class="btm_submit_btn"> --><a class="mvSignupBtn" id="submit_btn" onclick="sendNewsletter(); ">Submit</a><!-- </div> -->
	</div>
</div>
<?	
}
function iconBar($articleid,$position)
{
global $HTPFX,$HTHOST,$IMG_SERVER,$articleFullURL,$gaTrackingTicker,$article_title,$article_is_live;
$articleCache= new Cache();
$tickers = $articleCache->getTickerListCache($articleid,'1',$gaTrackingTicker,$article_title,$article_is_live);
?>
<div id="toolbox">
        <div class="comment-heading-left">
        <ul>
		<li id="social_icon_twitter_horizon" style="margin: 9px 9px 0 0;"><span class='st_twitter_hcount' displayText='Tweet' st_url="<?=$articleFullURL?>"  st_title = "<?=$article_title.$tickers;?>" st_via="minyanville" ></span></li>
		<li id="social_icon_fb_horizon" style="margin: 9px 9px 0 0;"><span class='st_facebook_hcount' displayText='Facebook' st_url="<?=$articleFullURL?>" ></span></li>
		<li id="social_icon_google_horizon" style=" margin: 2px 7px 0 0;"><span class='st_googleplus_large' displayText='Google +1' st_url="<?=$articleFullURL?>"  ></span></li>
		<li id="social_icon_in_horizon" style=" margin: 2px 7px 0 0;"><span class='st_linkedin_large' displayText='LinkedIn' st_url="<?=$articleFullURL?>" ></span></li>
		<li id="social_icon_st_horizon" style=" margin: 2px 7px 0 0;"><span class='st_sharethis_large' displayText='ShareThis' st_url="<?=$articleFullURL?>" ></span></li>
		<li id="social_icon_email_horizon" style=" margin: 2px 7px 0 0;"><span class='st_email_large' displayText='Email' st_url="<?=$articleFullURL?>" ></span></li>
		<li id="social_icon_rss_horizon" style=" margin: 2px 7px 0 0;"><a href="<?=$HTPFX.$HTHOST?>/service/rss.htm" target="_blank"><img src="<?=$IMG_SERVER;?>/images/articles/RSS_icon_32X32.png"></a></li>


        <!-- <li>
       	<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?=urlencode($articleFullURL)?>" send="true" height="22" width="350" show_faces="false" font=""></fb:like>
        </li> -->
        </ul>
        </div>
        <div style="float:right; margin: 5px 0 0;">
         <div id="comments-ticker">
         <a target="_blank" href="/articles/print.php?a=<?=$articleid ?>"><img src="<?=$IMG_SERVER?>/assets/dailyfeed/print_image.jpg" alt="Print" align="top" style="text-decoration:none;">PRINT</a>
            <img src="<?=$IMG_SERVER;?>/images/articles/comments-bubble.gif" align="top">
	  	  	<div id="fb_count" style="display:none">&nbsp;</div>
                    <?php $this->fbcommentNum(); ?>
          </div>
        </div>
    </div>
	<?
	if($position=="top")
	{
	?>
	    <!-- Share this Code Start -->
		<script type="text/javascript">var switchTo5x=true;</script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript">stLight.options({publisher: "c33749e3-a8dd-48d6-af73-7568c530a7f8",onhover: false}); </script>
		<!-- Share this Code End -->
	<?
	}
}
function bottomIconWidget()
{
global $HTPFX,$HTHOST,$IMG_SERVER,$articleFullURL;
?>
<div class="more-author" style="float:left;"><?php CM8_ShowAd("PostArticleBody_610x183"); ?></div>
<div id="article_bottom_widget">
	<center>
	    <div id="bottom_icons_comments">
	    <?php   $comment_num = $this->fbcommentNum(); ?>
	    <div style="float:right;" id="comments-ticker-bottom">
		</div>
	     <div style="float:right;" id="comments-bubble-bottom">
		<!--<img src="<?=$IMG_SERVER;?>/images/articles/comments-bubble.gif"> -->
	     </div>
	    </div>
	    <div id="TD_Bottom_ad">
	         <? CM8_ShowAd("partnerCenter"); ?>
	    </div>
	</center>
</div>
<?
}

	function articleNewsletter(){
	?>


	<?php
	}

	 function articleHeader($sectionName){
   	?>
	<div class="section_title_inner">
		<div class="section_name">All <?=$sectionName;?> Articles</div>
	</div>
	<?
   }


	function articleSharingArea($sectionId=NULL){
		global $IMG_SERVER;
	?>
<div class="section_newsletter_sharing">
	<div class="toplineFreeNewsletter">
		<a href="http://www.facebook.com/MinyanvilleMedia" target="_blank"><img	src="<?=$IMG_SERVER?>/images/topic/etf_iconFB.gif" border="0" /></a>
		<a href="http://twitter.com/minyanville" target="_blank"><img src="<?=$IMG_SERVER?>/images/topic/etf_iconTwitter.gif" border="0" /></a>

		<a href="http://www.linkedin.com/company/minyanville-media-inc"><img
		src="<?=$IMG_SERVER?>/images/topic/etf_iconIN.gif" border="0" /></a>
		<a href="http://www.reddit.com/domain/minyanville.com"><img
		src="<?=$IMG_SERVER?>/images/icons/icon_reddit.jpg" border="0" /></a>
		<a target="_blank" href="<?=$HTPFX.$HTHOST ?>/service/rss.htm"><img src="<?=$IMG_SERVER;?>/images/topic/RSS_icon_22X22.jpg" alt="" /></a>

	</div>
</div>

	<?
	}

	function articleSubscribeNewsLetter($subSectionData,$sectionId){
	$email=$_SESSION['email'];
	if($email=="")
	{
		$email="Input your email here";
	}
	?>
	<div class="freenewsletter">
	    <?php $this->articleSharingArea($sectionId); ?>
		<!--Sharing Icon Area End -->
		<div class="section_signup">
		<?php
			  $userid = $_SESSION['SID'];
			  ?>
			<input class="inputTextEmail" type="text" onblur="javascript:onBlurrGetText(this);" onfocus="javascript:onFocusRemoveText(this);" name="feed_email" id="feed_email" value="<?php echo $email;?>" >
			<input type="hidden"  name="feed_subscriber_id" id="feed_subscriber_id" value="<?php echo $userid; ?>">

			<input class="inputtextSubmit" type="button" onclick="emailNewsLetter('feed_email','feed_subscriber_id','dailydigest');" value="Submit" />
		</div>
		<!--Get Free MVIL End -->
		<div class="section_signup_text">Get <span>FREE MINYANVILLE</span><br />
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

	function sectionSponsoredLinks(){
	  $str.='<div class="sponsoredlinks_hp">';
	  $str.='<script language="javascript">CM8ShowAd("Sponsored_205x375");</script> ';
	  $str.='</div>';
	  echo $str;

	}

	function subscriptionBar($email,$userid)
	{
	?><div class="shadow"><div class="all-articles-bar-container">
			<div class="all-articles-bar">
			  <div class="all-articles-bar-heading1"> All Articles </div>
			  <div  class="all-articles-bar-heading2">
				<div class="all-free-articles-left">
				  <div class="all-free-articles-text"> Get&nbsp;<a >FREE ARTICLES</a><br>
					Newsletters and Alerts</div>
				  <div  class="all-free-articles-component">
					<input type="text" size="34" id="dailydigest_email" name="dailydigest_email" value="<?php echo $email;?>" onfocus="javascript:onFocusRemoveText(this);" onblur="javascript:onBlurrGetText(this);" class="inputTextEmail">
				<input type="hidden" id="dailydigest_subscriber_id" name="dailydigest_subscriber_id" value="<?php echo $userid; ?>">
				<input type="button" value="Submit" onclick="emailNewsLetter('dailydigest_email','dailydigest_subscriber_id','dailydigest');" class="inputtextSubmit" />
				</div>
				</div>
			  </div>
			</div>
		  </div>
		  <div id="content-container">
   <?php
	}
	function ArticleListingPageLeft()
	{
		global $HTPFX,$HTHOST,$IMG_SERVER;
		$obArtilceCache=new ArticleCache();
		$arLatestArticles = $obArtilceCache->getFreeAndPremiumArticles();
		$obDFCache=new DailyFeedCache();
		$arLatestDF = $obDFCache->getLatestDailyfeed();
		$i = 0;
	?>
		<div id="article-left" itemscope itemtype="http://schema.org/Article" >
		<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    	<meta itemprop="keywords" content="<?=$article_keywords;?>" />
	    <div class="all-articles-main-container">
        <div class="all-articles-left-container">
            <div class="all-articles-left-img"><h3 class="new-head">ALL ARTICLES</h3></div>
            <ul id="all-articles">
           	<div id="article_block_<?=$i?>">
            <?
			$currDate = "";
			foreach($arLatestArticles as $index => $arArticle)
			{
				$url = $HTPFX.$HTHOST.$arArticle['url'];
				if($currDate != date('M d',strtotime($arArticle['publish_date'])))
				{
					$currDate = date('M d',strtotime($arArticle['publish_date']));
					if($i!= 0 && $i%3 == 0)
					{
					?>
                        <div id="article_block_load<?=$i?>"><a class="load_more" onclick="$j('#article_block_load<?=$i?>').hide();$j('#article_block_<?=$i?>').show();">Load More &raquo;</a></div>
                        </div><div style="display:none;" id="article_block_<?=$i?>">
                   <?
					}
					?>
                    <div class="all-articles-left-date"><?=$currDate?></div>
					<?
					$i++;
				}
				$mvp_logo ="";
				if($arArticle['item_type'] != "1")
				{
					$url .= '?camp=articlepremiumcontent&medium=articlelisting&from=minyanville';
					$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
				}
			?>
            <li id="all-articles-bullet"><a href= "<?=$url?>"><?=mswordReplaceSpecialChars($arArticle['title']); ?></a><?=$mvp_logo ?>
           <br><span><a id="all-articles-contrib" href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$arArticle['author_id'] ?>"><?=$arArticle['author'] ?></a></span>
            </li>
			<?
            }
			?>
            <div><a class="load_more" href="<?=$HTPFX.$HTHOST?>/library/search.htm?oid=1">Go to Archive &raquo;</a></div>
            </div>
            </ul>
         </div>
          <div class="all-articles-left-right">
            <div class="all-articles-right-img"><h3 class="new-head">ALL MV PREMIUM</h3></div>
             <ul id="all-articles">
            <?
			$currDate = "";
			foreach($arLatestDF as $index => $arDF)
			{
				$url = $HTPFX.$HTHOST.$arDF['url'];
				if ($currDate != date('M d',strtotime($arDF['publish_date'])))
				{
					$currDate = date('M d',strtotime($arDF['publish_date']));
				?>
					<div class="all-articles-left-date"><?=$currDate?></div>
				<?
				}
			?>
            <li id="all-articles-bullet"><a href= "<?=$url?>"><?=mswordReplaceSpecialChars($arDF['title']); ?></a>
           <br><span><a id="all-articles-contrib" href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$arDF['author_id'] ?>"><?=$arDF['author'] ?></a></span>
            </li>
			<?
            }
			?>
            <div><a class="load_more" href="<?=$HTPFX.$HTHOST?>/library/search.htm?oid=18">Go to Archive &raquo;</a></div>
            </ul>
            </div>
        </div>
        <div class="all-articles-top-bottom"></div>
        </div>
	<?
    }

	function articleLeftContainer($results,$sql,$start,$end,$mon,$yr,$qryString,$p)
	{
		global $cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$productItemType;


		?><div id="article-left" itemscope itemtype="http://schema.org/Article" >
	      <div class="all-articles-main-container">
        <div class="all-articles-left-container">
          <div  >
            <div class="all-articles-left-img" >

				<h3 class="new-head">ALL ARTICLES</h3>

			</div>
            <ul id="all-articles">
              <?
            $i=0;
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
					  </div>
					  <? if ($i > 1 ) { ?>
					  <? } ?>
					  <div >
						<ul id="all-articles">
						  <? } ?>
              <div class="all-articles-left-date">

			  <?php echo $currDate;/* if(!$_SESSION['AdsFree']) {

					?>(Sponsored By)<br />
					<span class="all-articles-cm8-add">
<script language="javascript">CM8ShowAd("1x1_Text");</script>
</span></div>
                <?php

					 } */?></div>


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
              <li id="all-articles-bullet"><a href= "<?=$url?>" >
                <?= mswordReplaceSpecialChars($row['title']); ?>
                </a>&nbsp;
                <?=$mvp_logo?>
                <br>
                <span><a id="all-articles-contrib">
                <?= $row['author']; ?>
                </a></span></li>
              <? } else { ?>
              <li id="all-articles-bullet"><a href= "<?=$url?>">
                <?= mswordReplaceSpecialChars($row['title']); ?>
                </a>
                <?=$mvp_logo?>
                <br>
                <span><a id="all-articles-contrib" href="/gazette/bios.htm?bio=<?= $row['author_id']; ?>">
                <?= $row['author']; ?>
                </a></span></li>
              <? } ?>
              <?
        		$i++;
         } ?>
            </ul>
          </div>
        </div>
        <div class="all-articles-left-right">
          <div  >
            <div class="all-articles-right-img" ><!-- <img src="/images/redesign/AA_alldailyfeeds.jpg">  -->

				<h3 class="new-head">ALL MV PREMIUMS</h3>

			</div>
            <?php

			global $dailyfeedLandingItems,$dailyfeedTopicInterval;
			$objDailyfeed 	=	new Dailyfeed("daily_feed","");
			$objCache= new Cache();
			$feedDataArr=$objCache->getAllArticlesDailyFeedListCache($end,$start,$mon,$yr);
			?>
            <ul id="all-articles">
              <?
            $i=0;
			foreach ($feedDataArr as $row )
			{
				$urltitle=$objCache->getDailyFeedUrlCache($row['id']);
				$url=$HTPFX.$HTHOST.$urltitle;

				if (($row['contributor'] != '') && ($row['contributor'] != $row['author']))
				{
					$row['author'] = $row['contributor'];
				}

				if ($currDate != date('M d',strtotime($row['display_date'])))
				{
					$currDate = date('M d',strtotime($row['display_date']));
					if($i != 0)
					{
					?>
            </ul>
          </div>
          <? if ($i > 1 ) { ?>
          <? } ?>
          <div >
            <ul id="all-articles">
              <? } ?>
              <div class="all-articles-right-date">
			  <?php echo $currDate; /*if(!$_SESSION['AdsFree']) {

					?>(Sponsored By)<br />
					<span class="all-articles-cm8-add">
<script language="javascript">CM8ShowAd("1x1_Text");</script>
</span></div>
                <?php
					 } */
					 ?></div>

              <?	}

				if($row['item_type']){
					if(in_array($row['item_type'],$productItemType)){
						$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
					}else{
						$mvp_logo = '';
					}
				}


			?>
              <? if ($row['author'] == "Associated Press") { ?>
              <li id="all-articles-bullet"><a href= "<?=$url?>" >
                <?= mswordReplaceSpecialChars($row['title']); ?>
                </a>&nbsp;
                <?=$mvp_logo?>
                <br>
                <span><a id="all-articles-contrib">
                <?= $row['contributor']; ?>
                </a></span></li>
              <? } else { ?>
              <li id="all-articles-bullet"><a href= "<?=$url?>">
                <?= mswordReplaceSpecialChars($row['title']); ?>
                </a>
                <?=$mvp_logo?>
                <br>
                <span><a id="all-articles-contrib" href="/gazette/bios.htm?bio=<?= $row['ContId']; ?>">
                <?= $row['contributor']; ?>
                </a></span></li>
              <? } ?>
              <?
        		$i++;
         }

		?>
            </ul>
          </div>
        </div>



		<div class="all-articles-listing-pagination-container" >
        <div class="all-articles-listing-pagination">
        <?php

	/*##################*/

	$numrows=$objCache->getArticlesCountCache($sql);

	if($numrows>ARTICLELIMIT)
	{
		$totalRows = $countnum=ceil(($numrows/ARTICLELIMIT));

		if(($shownum<$countnum) && ($offset+ $shownum < $countnum))
		{
			 $countnum=$shownum + ($p+1);
		}

		$url=$qryString;


		?>

			<div class="dailyfeed_pagination_new all-articles-set-width" >

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
					<a target="_parent" href="<?php echo $url?>&p=<?php echo $p-1?>"><span class="page-arr">&laquo;</span> <span  class="page-prev">Previous</span></a>
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

						if($i<=$totalRows)
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
							&nbsp;<a target="_parent" href="<?php echo $url?>&p=<?php echo $i?>"><span ><?php echo $i;?></span></a>
							<?php
							}
							?>
							<span <?php if($i<$max && $i<$totalRows){?>class="line-border" <?php }?>>&nbsp;</span>
							<?php
						}
					}

					$next=$p+1;

					if($totalRows>$p)
					{
					?>
					<a target="_parent" href="<?php echo $url?>&p=<?php echo $next?>"><span class="page-next">Next</span> <span class="page-arr">&raquo;</span></a>
				<?php }
					else
					{
					?>
					<span class="page-next">Next</span> <span class="page-arr">&raquo;</span>
					<?php
					}
				?>

				</div>
				<div class="dailyfeed_pagination_right">Page <?php echo $p;?> of <?php echo $totalRows;?></div>
			</div>
			<br /><br /><br />
			<?php  }
/*##################*/

	?>
        </div>
      </div>

      </div>
      <div class="all-articles-top-bottom"></div>
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
   <?php
	}

	function showContribIntroText(){
		global $article;
		if(!empty($article->contribIntro)){
			echo $article->contribIntro."<br /><br />";
		}
	}

	function showContribPostText(){
		global $article;
		if(!empty($article->contribPostBody)){
			echo "<br /><br />".$article->contribPostBody."<br /><br />";
		}
		/*if(!empty($article->contribTwitter)){
			echo '<i>Twitter: </i><a target="_blank" href="http://twitter.com/#%21/'.$article->contribTwitter.'"><i>@'.$article->contribTwitter.'</i></a>';
		}*/
	}

	function showArticlePostBodyAd(){
		echo "<br/>". CM8_ShowAd('Post_Article_Body')."<br/>";
	}

	function articleLayoutRadio($articlecache,$NewEncoding,$articleid,$article,$breadcrum,$page){
 	global $IMG_SERVER,$HTPFX,$HTHOST,$gaTrackingAuthor,$articleFullURL,$cm8_ads_1x1_Text,$cm8_ads_1x1_Text_Ad,$cm8_ads_Button_160x30,$article_keywords;
 	$objThread = new Thread();
	$articlebody = $article->body;
	$featureImage = $article->featureimage;
	$caption = $article->caption;
	$stocks = $article->stocks;
	$gaTrackingAuthor=$article->author;
	$audioFile = $article->audioFile;
	$radioFile = $article->radioFile;
	$layoutType = $article->layout;
 ?>
	<div id="article-left" itemscope itemtype="http://schema.org/Article">
	<meta itemprop="articleSection" content="<?=$article->pageName;?>" />
    <meta itemprop="keywords" content="<?=$article_keywords;?>" />
		<div class="KonaFilter" style="padding-top:5px;"><?=showBreadCrum($breadcrum); ?></div>
		<div id="article-content">
			<h1><div  class="KonaFilter"><? echo '<span itemprop="name" >'.mswordReplaceSpecialChars($article->title).'</span>'; ?></div></h1>
			<div id="article-byline" class="KonaFilter">By
				<?if(!empty($article->g_plus_link))
				    {
				    	echo ' <span itemprop="author" ><a href="'.$article->g_plus_link.'?rel=author">'.$article->author.'</a></span>';
				    }
				    else  if($article->showBioLink){ ?>
					<span itemprop="author" ><a href="/gazette/bios.htm?bio=<? echo $article->authorid ?>"><? echo $article->author; ?></a></span>
				<? }else{ echo '<span itemprop="author" >'.$article->author.'</span>'; }?>
					<? echo '<span itemprop="datePublished">'.$article->date.'</span>' ?></div>
			<div id="article-dek" class="KonaFilter"><h2><? echo '<span itemprop="description" >'.mswordReplaceSpecialChars($article->character_text).'</span>'; ?></h2></div>
			<? $this->iconBar($articleid,'top');?>
			<div style="clear:both;"></div>
			<? if(!$_SESSION['AdsFree']) { ?>
				<div id="text-ad-container"><?php CM8_ShowAd($cm8_ads_1x1_Text); ?> </div>
				<div style="clear:both;"></div>
			<? } ?>
			<div id="article-body" class="article_text_body KonaBody">
			<style>
			#one-page {
				margin:8px 20px 0px 15px;
				float:left;
			}
			</style>
<div id="articleBodyContent">
			<span itemprop="articleBody"  id="article_content">
				<? $this->showContribIntroText();
				$articlePages =  count($articlebody);
				if ($articlePages < 2 || $page == "full") {
					foreach ($articlebody as $keyIndex => $entry)
					{
					$body = $entry['body'];
					$body = str_replace("https://image", "http://image", $body);
					if(substr_count($body,"{FLIKE}") > 0)
					{
						$body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
					}
					if(substr_count($body,"{RADIO}") > 0)
					{
						$body = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile,$layoutType)."</div>", $body);
					}
					$body = $NewEncoding->Convert($body);
					print $body;
					if($keyIndex != $articlePages-1)
					{
						print "<br /><br />";
					}
				}
				$this->showContribPostText();
				echo "<style>#pagination-container {display:none;};</style>";
			} else {
				if ($page == "") {
					$page = "1";
				}
				$page = ($page -1);
				$body = $articlebody[$page][body];
				$body = str_replace("https://image", "http://image", $body);
				if(substr_count($body,"{FLIKE}") > 0)
				{
					$body = str_replace("{FLIKE}","<div>".displaySmallFlike($articleFullURL,'return')."</div>", $body);
				}
				if(substr_count($body,"{RADIO}") > 0)
				{
					$body = str_replace("{RADIO}","<div>".displayRadioInArticle($radioFile)."</div>", $body);
				}
				$body = $NewEncoding->Convert($body);
				print $body;
				$this->showContribPostText();
			} ?>
		</span></div>
		<div style="clear:both;"></div>
		<div id="pagination-container" class="KonaFilter">
			<div class="prev-next">
				<? if ($page == "" || $page == "0") {
					echo "&lt; Previous";
				} else {
					echo  "<a href='".$articleFullURL."?page=$page'>&lt; Previous</a>";
				} ?>
			</div>
			<ul id="page-numbers">
				<? $i = 1;
				foreach ($articlebody as $entry) {
					$currentPage = $page + 1;
					if ($currentPage == $i) {
					   echo "<li class='page-on'>$i</li>";
					} else {
						echo "<li><a href='".$articleFullURL."?page=$i'>$i</a></li>";
					}
					$i++;
                } ?>
			</ul>
			<div class="prev-next">
				<? $nextPage = $page + 2;
				if ($nextPage > $articlePages) {
					echo "Next &gt;";
				} else {
					echo "<a href='".$articleFullURL."?page=$nextPage'>Next &gt;</a>";
				} ?>
			</div>
			<div id='one-page'><a href='<?=$articleFullURL?>?page=full'><img src='<?=$IMG_SERVER;?>/images/articles/one-page-logo.gif'></a> <a href='<?=$articleFullURL?>?page=full'>View As One Page</a></div>
		</div>
		<div style="clear:both;"></div>
		<div id="stock-position" class="KonaFilter"> <? echo $article->position; ?></div>
		<div id="disclaimer-link"><a href="javascript://" onclick="showDisclaimer();">Click Here to read the disclaimer &gt;</a></div>
		<div id="twitter-follow">
		<a target="_blank" href="http://twitter.com/minyanville"><img src="<?=$IMG_SERVER;?>/images/articles/icon-twitter.gif"></a> <div><a target="_blank" href="http://twitter.com/minyanville">Follow Us On Twitter</a></div>
		</div>
		<div style="clear:both;"></div>
		<div id="article-disclaimer" class="KonaFilter"> <? echo $article->disclaimer ?> </div>

		<? if(!$_SESSION['AdsFree']) { ?>
		    <div id="text-ad-container">
		    <?php CM8_ShowAd($cm8_ads_1x1_Text_Ad); ?>
		    </div>
		    <div style="clear:both;"></div>
		<?  } ?>
		<!-- bottom modules -->
		<div style="margin:10px 0 0 0;">
			<?
			$this->iconBar($articleid,'bottom');
			$this->emailBar();
			?>
				<? if(!$_SESSION['AdsFree']) { ?>
				<div id="text-ad-container">
				   <?php CM8_ShowAd('adbladeNewCode'); ?>
				   </div>
				   <div style="clear:both;"></div>
				<? } ?>

			<div id="bottom-module-container" style="float:left" class="KonaFilter"> <?=$this->getWidget($articleid);?> </div> <!-- bottom module container -->
			<?
			$this->bottomIconWidget();
			?>
		</div>

		<? $from = $_GET['from'];
		if ($from == 'yahoo') { ?>
			<div id="return-to">
				<a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" /></a>
			</div>
			<div style="clear:both;"></div>
		<? } ?>
    </div>

	<!-- end bottom modules -->
	<div style="clear:both;"></div>
	<? if($article->is_live=='1'){ 	?>
		<div id="fbcomment_layout">
			<div id="fb_seo">&nbsp;</div>
			<?php
			$this->FbcommentSEO();
			$this->fbcommentLayout(); ?>
		</div>
	<? } ?>
	<!--Discus Comment-->
	<!-- <div id="disqus_thread" class="disqusComment"></div> -->
	</div> <!-- end article-content -->
	</div> <!-- end article-left -->
	<div id="stock-tags" style="display:none;"><?=implode(",",$stocks) ?></div>
<?php }
} //------------ Class End ----------
?>