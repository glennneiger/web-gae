<?php
global $HTHOST,$HTPFX;
session_start();
$articleURL	=	makeArticleslink($_GET['a']);
if($articleURL !='')
{
	$newURL=$HTPFX.$HTHOST.$articleURL;
}
else
{
	set_sess_vars('notFound','1');
	location($HTPFX.$HTHOST);
	exit;
}
if($page)
{
	$newURL.="?page=".$page;
}
if($_GET['from'])
{
	if($page)
	{
		$newURL.="&from=".$_GET['from'];
	}else{
		$newURL.="?from=".$_GET['from'];
	}
}
Header( "HTTP/1.1 301 Moved Permanently" );
Header( "Location: ".$newURL );
exit;
$objContent=new Content('1',$_GET['a']);
include_once("../newpages/pages/includes/header-functions.htm");
include_once("../newpages/pages/includes/article-functions.htm");
include_once("../lib/MemCache.php");

$from='articles';
$imagevalue=0;
$showcomment=1;
$userid= $_SESSION['SID'];
global $arMenuDetail;
if($pageName=='article_template'){
    $aid=$_GET['a'];
    $secaid=getArticleSubsectionid($aid);
    $pageaDetail['id']=$secaid['page_id'];
    $pageDetail['id']=$pageaDetail['id'];
    $pageName=$secaid['article_pagename'];
     if($pageDetail['id']=="0"){
                $pageDetail['id']="56";
        }
}
$metadescription = substr($article->fullbody,0,200);
$metadescription = strip_tags($metadescription);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?=$titleMetaData?></title>
<meta name="description" content="<?=$pageMetaData['description']?>" >
<meta name="keywords" content="<?=$pageMetaData['keywords']?>">
<!-- legacy javascript needs to be optimized -->

<script src="/js/prototype.1.3.js" type="text/javascript"></script>
<script src="/js/config.1.2.js" type="text/javascript"></script>
<script src="/js/redesign.1.26.js" type="text/javascript"></script>
<script src="/js/Articles.1.9.js" type="text/javascript"></script>
<script src="/js/ibox.1.3.js" type="text/javascript"></script>
<script src="/js/ibox_registration.1.20.js" type="text/javascript"></script>


<script src="/js/ajax_basic_1.9.js" type="text/javascript"></script>


<script type="text/javascript" src="/newpages/js/jquery-1.3.1.min.js"></script>
<script type="text/javascript" src="/newpages/js/global.js"></script>
<script type="text/javascript" src="/newpages/js/article.js"></script>
<script type="text/javascript" src="/newpages/js/zoomimage.js"></script>

<link rel="stylesheet" href="/newpages/css/global.css" type="text/css" media="all" />
<link rel="stylesheet" href="/newpages/css/legacy.css" type="text/css" media="all" />
<link rel="stylesheet" href="/newpages/css/article.css" type="text/css" media="all" />
<link rel="stylesheet" media="screen" type="text/css" href="/newpages/css/zoomimage.css" />


<!-- legacy css -->
<link rel="stylesheet" media="screen" type="text/css" href="/css/ibox.1.1.css" />


<!-- checkm8 add stuff -->
<?
$featuredlinks=explode('/',$_GET['from']);
if($featuredlinks[1]=='Sponsored_Articles' & $featuredlinks[2]=='Yes' ){
        show_adds_checkmate($pageName,'Sponsored_Articles=1');
}
else{
        show_adds_checkmate($pageName);
}
?>
</head>

<body>

<script src="http://minyanvilledigital.checkm8.com/adam/cm8adam_1_call.js" type="text/javascript"></script>
<div id="outer-container">

<?
include_once("$D_R/newpages/pages/includes/header.htm");
include_once("$D_R/newpages/pages/articles/index.htm");
?>

<? include_once("$D_R/newpages/pages/includes/footer.htm"); ?>

</div> <!-- end outer container -->

<!-- Kontera ContentLink(TM);-->
<script type='text/javascript'>
var dc_AdLinkColor = '01509D' ;
var dc_PublisherID = 125012 ;
var dc_isBoldActive= 'no' ;
</script>
<script type='text/javascript' src='http://kona.kontera.com/javascript/lib/KonaLibInline.js'>
</script>
<!-- Kontera ContentLink(TM) -->

<? global $count;
//echo "count--------------------".$count;
?>

<!-- Start Quantcast tag -->
 <script type="text/javascript">
  var _qevents = _qevents || [];

  (function() {
   var elem = document.createElement('script');

   elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
   elem.async = true;
   elem.type = "text/javascript";
   var scpt = document.getElementsByTagName('script')[0];
   scpt.parentNode.insertBefore(elem, scpt);
  })();
</script>
<script type="text/javascript">
_qevents.push([{
qacct:"p-76i-akcf8qEJM",
labels: "<?=getquantcastlabel($pageName);?>"
}]);
</script>

<noscript>
<a href="http://www.quantcast.com/p-76i-akcf8qEJM" target="_blank"><img

src="//pixel.quantserve.com/pixel/p-76i-akcf8qEJM.gif" style="display: none;" border="0" height="1" width="1"

alt="Quantcast"/></a>
</noscript>
<!-- End Quantcast tag -->
<!-- google analytics tracking -->
<? googleanalytics(); ?>
</body>
</html>

