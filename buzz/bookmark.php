<?php
include_once("$D_R/lib/_auth.php");
include_once("$D_R/lib/_module_design_lib.php");
global $IMG_SERVER,$HTPFX,$HTHOST;
$objSubProd= new subViewer();
session_start();

$USER = new user();
$sid = $USER->user_id;
$showFreeBuzz="";
if(!empty($_GET['from'])){
	$partnerParms="&from=".$_GET['from']."&medium=".$_GET['medium']."&camp=".$_GET['camp'];
}

if($_GET['from']=="yahoo" && $_GET['medium']=="portals" && $_GET['camp']=="syndication"){
	$_SESSION['buzzYahooSynd']=1;
}

/*check if buzz post syndicated to yahoo*/
$buzzId=$_GET['id'];
if($buzzId && $_GET['from']=="yahoo"){
	$buzzQry="select BB.id from buzzbanter BB,content_syndication CS where
BB.id=CS.item_id and BB.is_live='1' and BB.approved='1' and CS.item_type='2' and CS.syndication_channel='yahoo' and CS.is_syndicated='1' and BB.id='".$buzzId."'";
 $buzzResult=exec_query($buzzQry,1);
 if($buzzResult['id']){
 	$showFreeBuzz=1;
 }
}

if(!$sid && !$showFreeBuzz){
	header("LOCATION: ".$HTPFX.$HTHOST."/buzzbanter/?msg=NOLOGIN".$partnerParms,TRUE,301);
}elseif(!$_SESSION['Buzz'] && !$showFreeBuzz){
	header("LOCATION: ".$HTPFX.$HTHOST."/buzzbanter/?msg=NOACCESS".$partnerParms,TRUE,301);
}else{
	$font = $_GET['s'];
	$context = $_GET['context'];
	$bbid = $_GET['id'];
	$chars = $_GET['chars'];
	$DATE_STR="m/d/y";

	switch ($font) {
		case "s": $class = "sizeSmall"; break;
		case "m": $class = "sizeMedium"; break;
		case "l": $class = "sizeLarge";
		}

	$qry="SELECT distinct buzzbanter.id AS id,buzzbanter.image AS image, buzzbanter.title AS title, " .
			"buzzbanter.body AS body, buzzbanter.author AS author, contributors.name AS author2, buzzbanter.position AS position, branded_img_id brandedlogo, " .
			"contributors.logo_asset AS logo_asset, date_format(buzzbanter.date,'%r') AS mdate, " .
			"UNIX_TIMESTAMP(buzzbanter.date) AS udate, buzzbanter.login AS login " .
			"FROM buzzbanter,contributors WHERE buzzbanter.contrib_id = contributors.id " .
			"AND buzzbanter.id= ' " . $bbid . "' ";

	$rows = exec_query($qry);

	if (isset($rows[0])) {
		$row = $rows[0];
		// get branded logo
		$brandedlogoImage1='';
		$wBrandedLogo1='';
		$hBrandedLogo1='';
		if(trim($row['brandedlogo'])!=''){
			$sqlBrandedLogo='select url,assets1,assets2 from buzz_branded_images where id='.$row['brandedlogo'];
			$resultBrandedLogo=exec_query($sqlBrandedLogo,1);
			if($resultBrandedLogo['assets1']!=''){
				$brandedlogoImage1=$resultBrandedLogo['assets1'];
		}
	}
	$row['body']=change_ssl_url($row['body']);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Buzz and Banter</title>
<link rel="stylesheet" type="text/css" href="includes/bookmark.css" />
<link rel='stylesheet' href='<?=$HTPFX.$HTHOST?>/css/ibox.css' type='text/css' media='all' />
<script type="text/javascript" src="includes/prototype.js"></script>
<script type="text/javascript" src="includes/AnchorPosition.1.1.js"></script>
<script type="text/javascript" src="includes/buzz.js"></script>
<script src='<?=$HTPFX.$HTHOST?>/js/config.1.2.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/prototype.1.3.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/min/jquery-1.9.1.min.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/global.1.7.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/ibox.1.3.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/registration_ajax_1.20.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/ibox_registration.1.18.js' type='text/javascript' ></script>
<script src='<?=$HTPFX.$HTHOST?>/js/redesign.1.21.js' type='text/javascript' ></script>
<script type="text/javascript" language="javascript">

function disableselect(e){
	return false
}

function reEnable(){
	return true
}

if (typeof document.onselectstart!="undefined")
	document.onselectstart=new Function ("return false")
else {
	document.onmousedown=disableselect
	document.onmouseup=reEnable
}

function makeLinksIntoPopups() {
	var links = document.getElementsByTagName('a');
	for (var i=0; i<links.length-2; i++) {
		var name= "link" + i + "";
      	var url = links[i].getAttribute('href');
	    if (url.indexOf("bookmark.php") == -1) {
	        args = "javascript:launchPage('" + url + "','" + name + "',0);void(0);";
	  	} else {
	  	  	args = "javascript:launchBookmarkFromURL('" + url + "');void(0);";
	  	}
        links[i].href = args;
	}
}

// These functions add or delete bookmarks, then refresh the bookmark menu if it
// happens to exist, then tells the user what happened.
function deleteMe(id) {
	var url = 'bookmark_delete.php';
	var pars = 'sid=' + <?=$sid?> + '&bbid=' + id;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:finishDelete});
}

function finishDelete () {
	alert("Bookmark deleted.");
	refreshBookmarkOp();
}

function refreshBookmarkOp(req) {
	if(typeof(window.opener)!="undefined") {
		opener.refreshBookmarks();
	}
}

function addMe(id) { // fired from article
	var url = 'bookmark_add.php';
	var pars = 'sid=<?=$sid?>' + '&bbid=' + id;
	var myAjax4 = new Ajax.Request(url, {method: 'post', parameters: pars, onComplete:finishAddBookmark});
}

function finishAddBookmark () {
	alert("Bookmark added."); // or something else to tell user the operation was successful
	finishBookmarkOp();
}


// PRINTING MODULE
function printMe (id) {
	var url = "print.php?id=" + id + "&s=<?=$font?>&chars=<?=$chars?>";
	var printJob = new PopupWindow();
	printJob.setSize(450,600);
	printJob.setUrl(url);
	printJob.setWindowProperties("resizable=1,toolbar=0, location=0, menubar=0, directories=0, scrollbars=1");
	printJob.showPopupNoAnchor();
	return false;
}

</script>
</head>

<body class="<?=$class?>" onLoad="javascript:makeLinksIntoPopups();">
<div id="headerBK">
	<div class="title">BUZZ <?php echo strtoupper($context) ?></div>
	<div class="posted">Posted <?= date($DATE_STR,$row['udate']) ?></div>
</div>

<div id="content_buzz" class="bookmark">
<?
 if(!$_SESSION['Buzz'] && $showFreeBuzz) {
 ?>
<div class="subscribe_headline">Buzz & Banter is a real-time connection to some of the market's most astute traders throughout each trading day.</div>
<? } ?>
<div class="article">

	<div class="articleInfo">
<?php
	if (isset($chars) && $chars==1) {
		echo '<img class="character" src="' . $row['image'] .'" />';
	}

	if ($row['author2'] == "") {
		$author = $row['author'];
	}else {
		$author = $row['author2'];
	}
 ?>


		<p class="author"><?=$author?></p>
		<p class="time"><?=$row['mdate']?></p>
		<p class="positions"><?php echo $row['position']; ?></p>
	</div>
<div class="articleBody">
<?php
if(is_file($img4="$D_R$row[logo_asset]")){
	list($w,$h)=getimagesize($img4);
	do  {
		$w=ceil($w*.9);
		$h=ceil($h*.9);
	} while ($w > 180 || $h > 60);
		echo	'<img src="' . $row[logo_asset] . '" border=0  width="' . $w . '" height="' . $h . '" /> ';
}
if (strlen($row['title'])) echo '<h2>' . $row['title'] . '</h2>';
?>
<div class="show_print_bookmark">
<?
if($_SESSION['Buzz'])
{
	if ($context=='bookmark') {
		echo '<a id="deleteBookmark" class="icon overLeft" href="javascript:void(0);" onclick="javascript:deleteMe(' . $row['id'] . ');return false"><img src="images/bookmark_delete.gif"/></a>';
	} else {
		echo '<a class="overLeft icon" style="margin-top:16px;" href="javascript:void(0);" onclick="javascript:addMe(' . $row['id'] . ');return false"><img src="images/bookmark_add.gif"/></a>';
	}
}
?>
<a href="javascript:void(0);" id="printPost" class="icon" onClick="javascript:printMe(<?=$row['id'];?>);return false"><img src="images/print_post.gif" /></a><br/>
</div>

<div style="clear:both"></div>
<?
if (strlen($brandedlogoImage1)) echo '<a href="'.$resultBrandedLogo[url].'" ><img border="0" src="' . $brandedlogoImage1 . '"></a>';
echo $row['body'] . "<br /><br />".displayChartBuzzImages($row['id']).

'<div class="clear"></div>';
?>
</div>

</div>
<?
 if(!$_SESSION['Buzz'] && $showFreeBuzz) { ?>
<div class="subscribe_bttn"><?=$objSubProd->getAddtoCartbtnsTrial('BuzzBanter','bttn_buzzsubscribe.jpg', 'subscription','subBuzzBanter');?></div>
<? } ?>
</div>
<div id="footerBK">&copy;2011 Minyanville Publishing &amp; Multimedia LLC, All Rights Reserved.</div>
<script language="javascript">
function getWindowHeight() {
	var windowHeight=0;
	if (typeof(window.innerHeight)=='number') {
		windowHeight=window.innerHeight;
	} else {
		if (document.documentElement && document.documentElement.clientHeight) {
			windowHeight=document.documentElement.clientHeight;
		} else {
			if (document.body&&document.body.clientHeight) {
				windowHeight=document.body.clientHeight;
			}
		}
	}
	return windowHeight;
}
function resizeBookmark() {
	var a = getWindowHeight();
	if (a > 45) {
		var c = a - 43 + "px"; // articles div
		document.getElementById("content").style.height = c;
	}
}

window.onresize = resizeBookmark;
window.onload = function() {resizeBookmark; makeLinksIntoPopups();}
</script>
<? googleanalytics();
universalGoogleAnalytics(); ?>
</body>
</html>
<?php }

}

?>

