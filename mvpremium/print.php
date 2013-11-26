<?
global $D_R,$HTPFX,$HTHOST,$CDN_SERVER;
$pageName = "dailyfeed";
$pageId=142;
include_once($D_R."/lib/MemCache.php");
include_once($D_R."/lib/_content_data_lib.php");
include_once($D_R."/lib/config/_article_config.php");
global $ga_account_id,$ga_profile_id,$HTPFXSSL,$HTHOST,$ua_profile_id;
global $cm8_ads_MediumRectangle;
$bannername=$cm8_ads_MediumRectangle;
$objContent=new Content('18',$_GET['d']);
$pageMetaData=$objContent->getMetaData();
$objMemcache= new Cache();
$title=ucwords(strtolower($pageMetaData['title']));
$section=ucwords(strtolower($pageMetaData['section']));
$titleMetaData=$title;
$titleMetaData.=' | MV Premium | Minyanville.com';



$pageCSS=array("ibox","global","layout","section","rightColumn","nav","optionsmith","dailyfeed");
if(isset($_GET['d']) && $_GET['d']!='')
{
include_once($D_R."/admin/lib/_dailyfeed_data_lib.php");
include_once($D_R."/lib/_dailyfeed_design_lib.php");
include_once($D_R."/lib/_article_design_lib.php");
$feedId		=	trim($_GET['d']);
$item_table	=	'daily_feed';
$objDailyfeed 	=	new Dailyfeed();
$feedViewer 	=	new dailyfeedViewer();
$feedData		=	$objDailyfeed->getDailyFeed($feedId);
if(!$feedData['id']){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	location("/errors/?code=404");
	exit;
}


$resource		=	$objDailyfeed->getResource($feedId,$item_type);
$objArticle		= new articleViewer();
?>
<html>
<head>
<title><?=mswordReplaceSpecialChars($titleMetaData)?></title>
<meta name="description" content="<?=mswordReplaceSpecialChars($pageMetaData['description'])?>" >
<meta name="keywords" content="<?=mswordReplaceSpecialChars($pageMetaData['keywords'])?>">
<link rel="canonical" href="<?=$HTPFX.$HTHOST.$pageMetaData['url']?>"/>
<meta name="robots" content="noindex, noodp, noydir"/>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("<?=$ga_account_id?>-<?=$ga_profile_id?>");
</script>
<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?=$ga_account_id?>-<?=$ua_profile_id?>');
</script>
<script type="text/javascript">
function trackTSClick(linkOut,url,label)
{
	pageTracker._trackEvent("Link Outs","Click",label);
	ga('send', 'event', 'Link Outs', 'Click',label);
	window.location =url;
}
</script>
<!-- Checkm8 script added to load ads in print page -->
<?
show_adds_checkmate('print');
?>
<!-- Checkm8 script ends -->
	<link href="<?=$CDN_SERVER?>/css/dailyfeed.1.21.css" type="text/css" rel="stylesheet" />
	<script language="javascript">
	<!--// <![CDATA[
	<?
	$path_info=pathinfo($_SERVER[PHP_SELF]); ?>
	  var OA_channel = '<?=substr($path_info[dirname],1)?>';
	// ]]>

	-->
	</script>
	<script src="<?=$CDN_SERVER?>/js/ads.js" type="text/javascript"></script>

	<script src="<?=$CDN_SERVER?>/js/print.js" type="text/javascript"></script>



</head>

<body onLoad="javascript:window.print();">
<title>MV Premium - Print</title>
<?php
if(count($feedData)>0)
{

 	$feedTitle		=	trim(mswordReplaceSpecialChars($feedData['title']));
	$feed_date_time = 	$feedData['creation_date'];
	$feed_date 		= 	substr($feed_date_time,0,10);

	if($feed_date == date(Y)."-".date(m)."-".date(d)) {
	$display_date = date('F j, Y h:i A',strtotime($feed_date_time));
	}
	else {
	$display_date = date('F j, Y h:i A',strtotime($feed_date_time));
	}
	$publication_date	=	date('Y-m-dTH:i',strtotime($feed_date_time ));
	$contributorName	=	trim($feedData['contributor']);
	$contributorId		=	trim($feedData['ContId']);
	//$source			=	trim($feedData['source']);
	$feed_excerpt		=	trim(mswordReplaceSpecialChars(strip_tags($feedData['excerpt'])));
	$feed_excerpt		=	$objArticle->filter_urls($feed_excerpt);

	$feed_body 			=	mswordReplaceSpecialChars($feedData['body']);
	$feed_body			=	$objArticle->filter_urls($feed_body);
	$feed_body          =    $objMemcache->buzzAdReplace($feed_body);
	//$topics			=	$objDailyfeed->getTopics($item_table,$feedId);
	$quick_Title		=	$objDailyfeed->getQuickTitleDailyFeed($feedId,'18');

	$quickTitle			=	$quick_Title['quick_title'];
	$topicsURL="";
  	$topicslink="";
	$topicsURL			=	$objDailyfeed->getTopicsURL($item_table,$feedId);
	$resource			=	$objDailyfeed->getResource($feedId,'18');

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

	/*$topicval=array();
	$topicval=explode(",",$topics);
	$topicslink="";
	foreach($topicval as $key=>$row){
		if($key>0){
			$topicslink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/dailyfeed//topic/'.str_replace(" ","-",trim($row)).'" target="_self">'.ucwords(strtolower($row)).'</a>';
		}else{
			$topicslink.='<a href="'.$HTPFX.$HTHOST.'/dailyfeed/topic/'.str_replace(" ","-",trim($row)).'" target="_self">'.ucwords(strtolower($row)).'</a>';
		}

	}*/
	$stock_tickers=$objDailyfeed->getTickersExchange($feedId,'18');
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
	$getImage			=	$objDailyfeed->getImageDailyFeed($feedId);
	$feedPosition		=	trim($feedData['position']);

	if($feedData['layout_type'] =='thestreet')
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
		else
		{
			if(count($getImage)>0)
			{ 	$imageURL	=	$getImage['url'];  } else  { $imageURL	=''; }
		}
	}
	elseif($feedData['layout_type'] =='observer')
	{
		$imageURL	=	$IMG_SERVER."/images/observer/dailyfeed_observer.png";
	}
	else
	{
		if(count($getImage)>0)
		{

				$imageURL	=	$getImage['url'];
		}
		else
		{
				$imageURL	='';
		}
	}
?>
<SCRIPT language="JavaScript" src="http://minyanvilledigital.checkm8.com/adam/cm8adam_1_call.js"></SCRIPT>
 <a href="<?=$HTPFX.$HTHOST.'/mvpremium/';?>" target="_self"><h1><img id="Image-Maps_1201006241603598" src="<?=$IMG_SERVER?>/images/DailyFeed/premium_email_banner.jpg" usemap="#Image-Maps_1201006241603598" border="0" width="560" height="58" alt="<?=$headerALTText; ?>"/></h1></a>
	<map id="_Image-Maps_1201006241603598" name="Image-Maps_1201006241603598">
	<area shape="rect" coords="283,28,333,49" href="<?=$HTPFX.$HTHOST?>/rss/dailyfeed.rss" alt="RSS" title="RSS"    />
	<area shape="rect" coords="332,30,527,51" href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/controlPanel.htm" alt="" title="" onClick="javascript:subscribeNewsletter();"    />
	</map>
<div class="middle-df-part">
<?
    $share_title = htmlspecialchars($feedData['title'],ENT_QUOTES);
    $share_body = htmlspecialchars(strip_tags(preg_replace('/<br \/?>/',' ',$feed['body'])),ENT_QUOTES);
    ?>
    <div class="middle-main-heading"><?=$feedTitle;?></div>
	<div class="submitted-by" style="border:1px #990066; padding-top:4px; margin-top:4px;"> By <a href="<?=$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$contributorId;?>" target="_self"><span><?=$contributorName;?></span></a></div>
	<div class="date_detail"><?=$display_date;?></div>
	<div class="content-area">
	<span class="content-area-detail-image">
	<?php if($quickTitle!='') { ?> <div class="quicktitle_print"><?=strtoupper(trim($quickTitle));?></div><? } ?>
	<?php
		if($imageURL!='')
		{ ?> <br><br><img src="<?=$imageURL;?>" border="0"  alt="DailyFeed" />  <?php } ?>
	</span>
	<span class="body_detail" style="vertical-align:top;">
		<?=$feed_body;?>
	</span>
	</div>
	</span>
	<?php if($feedPosition!='') { ?>
<div class="topic-area" id='position-area'>
 <span style="color:#acacac;"> POSITION:</span> <?=$feedPosition;?>
</div>
<?php } ?>
<!--<div class="topic-area">
<?php if($topics!='') { ?> <span style="color:#acacac;"> TAGS:&nbsp;</span><?=$topicslink;?>&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if($source!='') { ?> <span style="color:#acacac;">SOURCE:</span>&nbsp;<a href="<?=$feedData['source_link'];?>"><?=$source;?></a><?php } ?>
</div> -->
<div class="topic-area">
<?php if($topicsURL!='') { ?> <span style="color:#acacac;"> TAGS:&nbsp;</span><?=$topicslink;?>&nbsp;&nbsp; <?php } ?>
<?php if($resource!='') { ?>
<?php if($feedData['layout_type'] =='thestreet')
	{ ?>
	<span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp;<a href="#" onClick="javascript:trackTSClick('Link Outs','<?=$resource['source_link']."?puc=minyanvilletsc&amp;cm_ven=minyanvillets";?>','TheStreet Resource');"><?=$resource['source'];?></a>
	<?php
	}
	else
	{
	?>
	<span style="color:#acacac;">SOURCE:</span>&nbsp;&nbsp;<a href="<?=$resource['source_link'];?>"><?=$resource['source'];?></a><?php }
	}
	?>
</div>

<?php if($stock_tickers!='') { ?>
<div class="topic-area" id="stock-area">
<span style="color:#acacac;">TICKERS:</span>&nbsp;<?=$stocklink;?>&nbsp;&nbsp;</div>
<?php } ?>
  </div>
<!--Feed body Ends here-->
<br>
<?php
}
	$profileid=1;
	//googleanalytics($profileid,$is_ssl,"");
	googleanalytics();
?>

<? if(!$_SESSION['AdsFree']) { ?>
<!-- start Vibrant Media IntelliTXT script section -->
<script type="text/javascript" src="http://minyanville.us.intellitxt.com/intellitxt/front.asp?ipid=31585"></script>
<!-- end Vibrant Media IntelliTXT script section -->
<? } ?>

</body>
</html>
<?php
}
?>