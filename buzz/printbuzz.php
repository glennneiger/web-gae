<?
session_start();
global $D_R;
include_once($D_R."/lib/_content_data_lib.php");
include_once("$D_R/lib/config/_article_config.php");
handle_ssl();
global $IMG_SERVER,$HTPFX,$HTHOST,$default_section;
$pageName = "buzz_alert";
global $cm8_ads_MediumRectangle;
$bannername=$cm8_ads_MediumRectangle;

$objContent=new Content('1',$_GET['a']);
//$pageMetaData=$objContent->getMetaData();
//htmlprint_r($pageMetaData);
$bbid=$_GET['id'];
$qry="SELECT distinct buzzbanter.id AS id,buzzbanter.image AS image, buzzbanter.title AS title, " .
				"buzzbanter.body AS body,buzzbanter.contrib_id, buzzbanter.author AS author, contributors.name AS author2, buzzbanter.position AS position, branded_img_id brandedlogo, " .
				"contributors.logo_asset AS logo_asset, buzzbanter.date AS mdate, " .
				"UNIX_TIMESTAMP(buzzbanter.date) AS udate, buzzbanter.login AS login " .
				"FROM buzzbanter,contributors WHERE buzzbanter.contrib_id = contributors.id " .
				"AND buzzbanter.id= ' " . $bbid . "' ";
$rows = exec_query($qry,1);
if(!is_array($rows)){
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	location("/errors/?code=404");
	exit;
}


$title="Buzz and Banter-".' '.ucwords(strtolower($rows['title']));
$titleMetaData=$title;
$titleMetaData.=' | Minyanville.com';


?>
<html>
<head>
<title><?=mswordReplaceSpecialChars($titleMetaData)?></title>
<!--<meta name="description" content="<?=mswordReplaceSpecialChars($pageMetaData['description'])?>" >
<meta name="keywords" content="<?=mswordReplaceSpecialChars($pageMetaData['keywords'])?>">
<link rel="canonical" href="<?=$HTPFX.$HTHOST.$pageMetaData['url']?>"/>-->
<meta name="robots" content="noindex, noodp, noydir"/>

<!-- Checkm8 script added to load ads in print page -->
<?
show_adds_checkmate('print');
?>
<!-- Checkm8 script ends -->
	<link rel="stylesheet" href="../css/print.1.1.css">
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

<div>

	<table border="0" bordercolor="white" cellpadding=0 cellspacing=0 width="100%">
	<tr>
    	<td colspan=4 align="right" class="bottom_common_link"><p><a href="#" onClick="javascript:window.print();">Print</a></p></td>
	<tr>
	<td>
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"><? show_ads_openx("ArticlePrint728x90",$SEC_TO_ZONE_OPENX['ArticlePrint728x90'],$ADS_SIZE['LeaderBoard']); ?></p>
	</td>

	<td rowspan=4 align=right><? echo CM8_ShowAd($bannername); ?></td>
	</tr>
	<tr>
    	<td  class="article_title" width=100%><?= $rows['title']; ?></td>
     </tr>
	<tr>
		<td nowrap>
			<h3><?= displayAuthorLink($rows['author'],$rows['contrib_id']); ?>&nbsp;
	<span class="post_time"><?=date("M d, Y H:i a",strtotime($rows['mdate']));?></span></h3>
		</td>
	</tr>
	</table>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="article_text_body">
<br>
<?= $rows['body']; ?>
</div>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<br>
<?php
	$profileid=1;
    //googleanalytics($profileid);
	googleanalytics();
	universalGoogleAnalytics();

?>
</body>
</html>