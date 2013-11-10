<?
session_start();
global $D_R;
include_once($D_R."/lib/_content_data_lib.php");
include_once("$D_R/lib/config/_article_config.php");
handle_ssl();
global $IMG_SERVER,$HTPFX,$HTHOST,$default_section;
$pageName = "article_template";
global $cm8_ads_MediumRectangle;
$bannername=$cm8_ads_MediumRectangle;

$objContent=new Content('1',$_GET['a']);
$pageMetaData=$objContent->getMetaData();
if(!$pageMetaData['section'] && $objContent->contentType=='1'){
	$pageMetaData['section']=$default_section;
}
$title=ucwords(strtolower($pageMetaData['title']));
$section=ucwords(strtolower($pageMetaData['section']));
$titleMetaData=$title;
if(!empty($section))
	$titleMetaData.=' | '.$section;
$titleMetaData.=' | Minyanville.com';



//modules are in associative array - key (id)  => value[name]
$modules = getModules();
//get article information
if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$articleid = $_GET['a'];
	$article = getArticle($articleid);
	if ($article != 0) {
		$articleSet = true;

		if (!$USER->isAuthed) {
			$loggedin = "no";
		} else {
			$loggedin = "yes";
		}

	} else {
		$articleSet = false;
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		location("/errors/?code=404");
		exit;
	}
	}
else
{
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	location("/errors/?code=404");
	exit;
}
//some old images will point to gazette/newsviews redirect this to articles/index.php
$article['body'] = str_replace("gazette/newsviews/?id","articles/index.php?a",$article['body']);
$article['body']= replaceArticleAds($article['body']);
//those old links inside the body of the article shouldn't open in  a new window.
//$article['body'] = str_replace("_blank","_self",$article['body']);
?>
<html>
<head>
<title><?=mswordReplaceSpecialChars($titleMetaData)?></title>
<meta name="description" content="<?=mswordReplaceSpecialChars($pageMetaData['description'])?>" >
<meta name="keywords" content="<?=mswordReplaceSpecialChars($pageMetaData['keywords'])?>">
<link rel="canonical" href="<?=$HTPFX.$HTHOST.$pageMetaData['url']?>"/>
<meta name="robots" content="noindex, noodp, noydir"/>

<!-- Checkm8 script added to load ads in print page -->
<?
show_adds_checkmate('print');
?>
<!-- Checkm8 script ends -->
	<link rel="stylesheet" href="../css/print.1.2.css">
	<script language="javascript">
	<!--// <![CDATA[
	<?
	$path_info=pathinfo($_SERVER[PHP_SELF]); ?>
	  var OA_channel = '<?=substr($path_info[dirname],1)?>';
	// ]]>

	-->
	</script>
	<!-- <script src="<?=$pfx?>/js/ads.js" type="text/javascript"></script>  -->

	<script src="../js/print.js" type="text/javascript"></script>
</head>
<body>
<SCRIPT language="JavaScript" src="http://minyanvilledigital.checkm8.com/adam/cm8adam_1_call.js"></SCRIPT>
<?php
if($article['layout_type']=='thestreet')	//----------- For layout type 'thestreet'
{
include("$D_R/lib/_article_design_lib.php") ;
$objArticleViewer	=	new articleViewer();

$articleType	= $objArticleViewer->getTheStreetArticle($articleid);
if($articleType)
{
	if($articleType['thestreet_article_type'] == 'realmoneysilver')
	{
		//$theStreetLogo	=	'realmoneysilverlogo.gif';
		$authorlink1	=	'http://secure2.thestreet.com/cap/prm.do?OID=015582&amp;puc=minyanvilletsc&amp;cm_ven=minyanvillets';
		$authorlink2	=	'http://www.realmoneysilver.com?puc=minyanvilletsc&amp;cm_ven=minyanvillets';
	}
	if($articleType['thestreet_article_type'] == 'thestreet')
	{
		//$theStreetLogo			=	'thestreetlogo.gif';
		$authorlink				=	'http://www.thestreet.com?puc=minyanvilletsc&amp;cm_ven=minyanvillets';
	}
}

global $ga_account_id,$ga_profile_id,$ua_profile_id;
?>
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
<div>

	<table border="0" bordercolor="white" cellpadding=0 cellspacing=0 width="100%">
	<tr><td colspan=4 align="right" class="bottom_common_link"><p><a href="#" onClick="javascript:window.print();">Print</a></p></td>
	<tr>
	<td colspan=3>
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"><? show_ads_openx("ArticlePrint728x90",$SEC_TO_ZONE_OPENX['ArticlePrint728x90'],$ADS_SIZE['LeaderBoard']); ?></p>
	</td>

	<td rowspan=4 align=right><? echo CM8_ShowAd($bannername); ?></td>
	</tr>
	<tr><td  class="article_title" width=100% colspan=3><? echo mswordReplaceSpecialChars($article['title']); ?></td></tr>
	<tr height="10" style="float: left; margin:0px 0px 0px 5px;width:100%">
		<td nowrap colspan="3">
			<h3 style="padding:0px;">
			<?php
			if($articleType['thestreet_article_type'] == 'thestreet')
			{
			?>
			<a href="#" onClick="javascript:trackTSClick('Link Outs','<?=$authorlink;?>','TheStreet Auhtor');"> <? echo $article['author'] ?></a>
			<?php
			}
			elseif($articleType['thestreet_article_type'] == 'realmoneysilver')
			{
			$expAuthor	=	explode(",",$article['author']);
			$authorName	=	$expAuthor[0];
			$authorSite	=   $expAuthor[1];
			?>
			<a href="#" onClick="javascript:trackTSClick('Link Outs','<?=$authorlink1;?>','RealMoneySilver Author');"><span class="post_time" >By </span> <?=$authorName;?></a><span style="color:#5792CA;">,</span><a href="#" onClick="javascript:trackTSClick('Link Outs','<?=$authorlink2;?>','RealMoneySilver Author');"><?=$authorSite;?></a>
			<?php
			}
			?>&nbsp;<span class="post_time"><?=strtoupper($article['date']);?></span></h3>
		</td>
	</tr>
	<tr style="float: left; margin: 10px 4px;width:95%">
		<td width="" align="left">
			<div id="article-dek" class="konafilter"><h2><? echo mswordReplaceSpecialChars($article['character_text']); ?></h2></div>
		</td>
	</tr>
	</table>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="article_text_body">
<br>
<?
$body	=	trim($article['body']);
$body	=	str_replace("&#039;&quot;",'"',$body);
$body	=	str_replace("&quot;&#039;",'"',$body);
$body	= 	html_entity_decode($body);
$body	=	$objArticleViewer->filter_urls($body);
echo html_entity_decode($body);
?>
</div>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<div>
  <?= $article['position']; ?>
 </div><br>
<div><?= $article['disclaimer']; ?></div>
<br>
<?php
	$profileid=1;
    //googleanalytics($profileid);
	googleanalytics();
?>
<?php
}
elseif($article['layout_type']=='observer')
{
?>
<div>

	<table border="0" bordercolor="white" cellpadding=0 cellspacing=0 width="100%">
	<tr><td colspan=4 align="right" class="bottom_common_link"><p><a href="#" onClick="javascript:window.print();">Print</a></p></td>
	<tr>
	<td colspan=3>
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"><? show_ads_openx("ArticlePrint728x90",$SEC_TO_ZONE_OPENX['ArticlePrint728x90'],$ADS_SIZE['LeaderBoard']); ?></p>
	</td>

	<td rowspan=4 align=right><? echo CM8_ShowAd($bannername); ?></td>
	</tr>
	<tr><td  class="article_title" width=100% colspan=3><? echo mswordReplaceSpecialChars($article['title']); ?></td></tr>


	<tr height="10" style="float: left; margin:0px 0px 0px 5px;width:100%">
		<td nowrap colspan="3">
			<h3 style="padding:0px;"><?= displayAuthorLink($article['author'],$article['authorid']); ?>&nbsp;
			<span class="post_time"><?=strtoupper($article['date']);?></span></h3>
			</td>
	</tr>
	<tr style="float: left; margin: 10px 4px;width:95%">
		<td width="" align="left">
			<div id="article-dek" class="konafilter"><h2><? echo mswordReplaceSpecialChars($article['character_text']); ?></h2></div>
		</td>
	</tr>
	</table>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="article_text_body">
<br>
<?
$body	=	trim($article['body']);
//$body	=	$objArticleViewer->filter_urls($body);
echo html_entity_decode($body);
?>
</div>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<div>
  <?= $article['position']; ?>
 </div><br>
<div><?=$article['disclaimer']; ?></div>
<br>
<?php
	$profileid=1;
    //googleanalytics($profileid);
	googleanalytics();
?>
<?php
}
else		//----------- For All other Layouts
{
?>
<div>

	<table border="0" bordercolor="white" cellpadding=0 cellspacing=0 width="100%">
	<tr><td colspan=4 align="right" class="bottom_common_link"><p><a href="#" onClick="javascript:window.print();">Print</a></p></td>
	<tr>
	<td colspan=3>
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"><? show_ads_openx("ArticlePrint728x90",$SEC_TO_ZONE_OPENX['ArticlePrint728x90'],$ADS_SIZE['LeaderBoard']); ?></p>
	</td>

	<td rowspan=4 align=right><? echo CM8_ShowAd($bannername); ?></td>
	</tr>
	<tr><td colspan=3  class="article_title" width=100% colspan=3><?= $article['title']; ?></td></tr>
	<tr height="10" style="float: left; margin:0px 0px 0px 5px;width:100%">
		<td nowrap colspan="3">
			<h3 style="padding:0px;"><?= displayAuthorLink($article['author'],$article['authorid']); ?>&nbsp;
	<span class="post_time"><?=strtoupper($article['date']);?></span></h3>
		</td>
	</tr>
	<tr style="float: left; margin: 10px 4px; width:95%">
		<td width="" align="left">
			<div id="article-dek" class="konafilter"><h2><? echo mswordReplaceSpecialChars($article['character_text']); ?></h2></div>
		</td>
	</tr>
	</table>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="article_text_body">
<br>
<?= $article['body']; ?>
</div>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<div>
  <?= $article['position']; ?>
 </div><br>
<div><?= $article['disclaimer']; ?></div>
<br>
<?php
	$profileid=1;
    //googleanalytics($profileid);
	googleanalytics();
}
?>
</body>
</html>