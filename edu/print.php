<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?
session_start();
handle_ssl();
global $IMG_SERVER,$HTPFX,$HTHOST,$CDN_SERVER;
$pageName = "eduPrint";
global $D_R;
include_once($D_R."/lib/_content_data_lib.php");
include_once("$D_R/lib/layout_functions.php");
$objContent=new Content('31',$_GET['a']);
$pageMetaData=$objContent->getMetaData();

$title=ucwords(strtolower($pageMetaData['title']));
$titleMetaData=$title;

if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$articleid = $_GET['a'];
	$qry = "SELECT e.id, e.title, e.category_id, enc.menu_name, IF(e.publish_date>e.creation_date,e.publish_date,e.creation_date) publish_date, e.body, e.edu_img, c.name FROM edu_alerts e, `contributors` c, edu_nav_category enc WHERE e.is_approved='1' AND e.is_live='1' AND e.is_deleted='0' 
AND c.id=e.contrib_id AND e.category_id=enc.id AND e.id='".$articleid."'";
	$article = exec_query($qry,1);
}
else
{
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	location("/errors/?code=404");
	exit;
}
//some old images will point to gazette/newsviews redirect this to articles/index.php
?>
<html>
<head>
<title><?=mswordReplaceSpecialChars($titleMetaData)?></title>
<meta name="description" content="<?=mswordReplaceSpecialChars($pageMetaData['description'])?>" >
<link rel="canonical" href="<?=$HTPFX.$HTHOST.$pageMetaData['url']?>"/>
<meta name="robots" content="noindex, noodp, noydir"/>

<!-- Checkm8 script added to load ads in print page -->
<? show_adds_checkmate('print'); ?>
<!-- Checkm8 script ends -->
<link rel="stylesheet" href="<?=$CDN_SERVER?>/css/edu.css">
<script language="javascript">
	<? $path_info=pathinfo($_SERVER[PHP_SELF]); ?>
	var OA_channel = '<?=substr($path_info[dirname],1)?>';
</script>
<script src="<?=$CDN_SERVER?>/js/ads.js" type="text/javascript"></script>
<script src="<?=$CDN_SERVER?>/js/print.js" type="text/javascript"></script>
</head>
<body>
<SCRIPT language="JavaScript" src="http://minyanvilledigital.checkm8.com/adam/cm8adam_1_call.js"></SCRIPT>
<? global $ga_account_id,$ga_profile_id,$ua_profile_id; ?>
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
	function trackTSClick(linkOut,url,label){
		pageTracker._trackEvent("Link Outs","Click",label);
		ga('send', 'event', 'Link Outs', 'Click',label);
		window.location =url;
	}
</script>
<div>
<div>
	<table border="0" bordercolor="white" cellpadding=0 cellspacing=0 width="100%">
		<tr><td colspan=4 align="right" class="bottom_common_link"><p><a href="#" onClick="javascript:window.print();">Print</a></p></td>
		<tr>
		<td colspan=3>
		<p><img src="<?= $IMG_SERVER; ?>/images/education/edu-logo.png"><? show_ads_openx("ArticlePrint728x90",$SEC_TO_ZONE_OPENX['ArticlePrint728x90'],$ADS_SIZE['LeaderBoard']); ?></p>
		</td>
	
		<td rowspan=4 align=right><? echo CM8_ShowAd($bannername); ?></td>
		</tr>
		<tr><td colspan=3  class="article_title" width=100% colspan=3><?= $article['title']; ?></td></tr>
		<tr height="10" style="float: left; margin:0px 0px 0px 5px;width:100%">
			<td nowrap colspan="3">
				<h3 style="padding:0px;"><?= displayAuthorLink($article['name'],$article['contrib_id']); ?>&nbsp;
				<span class="post_time"><?=strtoupper($article['publish_date']);?></span></h3>
			</td>
		</tr>
	</table>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="article_text_body"><br><?= $article['body']; ?></div>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<br>
<?php
	$profileid=1;
    //googleanalytics($profileid);
	googleanalytics();
?>
</body>
</html>