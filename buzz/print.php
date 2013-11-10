<?php
include_once("$D_R/gazette/buzzbanter/_authenticate.htm");
global $IMG_SERVER;
$sid=$_SESSION['SID'];
if($sid){
$font = $_GET['s'];
$bbid = $_GET['id'];
$chars = $_GET['chars'];
$date = $_GET['date'];

if ($date != null) {
	$font = 'm';
	$chars = 1;
	$filter = "AND date_format(buzzbanter.date,'%m/%d/%Y') = '" . $date . "' ";
}
$DATE_STR="m/d/y";
$tableName = "buzzbanter";
switch ($font) {
	case 's':
		$body = 'sizeSmall';
		break;
	case 'm':
		$body = 'sizeMedium';
		break;
	case 'l':
		$body = 'sizeLarge';
}

if ($chars ==1) {
	switch ($font) {
		case 's': $h=180;break;
		case 'm': $h=210;break;
		case 'l': $h=240;break;
	}
} elseif ($chars == 0) {
	switch ($font) {
		case 's': $h=90; break;
		case 'm': $h=120;break;
		case 'l': $h=140;break;
	}
}
//if bbid is null then print all posts else just for single id
if (!$bbid && !$date){
	$filter = "";
	$tableName = "buzzbanter_today AS buzzbanter";
}
elseif ($date) {$filter = "AND date_format(buzzbanter.date,'%m/%d/%Y') = '" . $date . "' ";}
elseif ($bbid) {$filter="AND buzzbanter.id=' " . $bbid . "' ";}
$limit = "LIMIT 200 ";


$qry="SELECT buzzbanter.id AS id, buzzbanter.image AS image, buzzbanter.title AS title, " .
	"buzzbanter.body AS body, buzzbanter.author AS bbauthor, contributors.name AS author, buzzbanter.position AS position, buzzbanter.branded_img_id brandedlogo, " .
	"contributors.logo_asset AS logo_asset, date_format(buzzbanter.date,'%r') AS mdate, " .
	"UNIX_TIMESTAMP(buzzbanter.date) AS udate " .
	"FROM ".$tableName.",contributors WHERE buzzbanter.contrib_id = contributors.id " .
	"AND is_live='1' " .
	"AND show_in_app='1' " .
	"AND approved='1' " .
	"AND buzzbanter.login!='(automated)' " . $filter . " " .
	"ORDER BY date DESC " . $limit;
/*
$qry="SELECT buzzbanter.id AS id, buzzbanter.image AS image, buzzbanter.title AS title, " .
	"buzzbanter.body AS body, buzzbanter.author AS bbauthor, buzzbanter.position AS position, " .
	" date_format(buzzbanter.date,'%r') AS mdate, " .
	"UNIX_TIMESTAMP(buzzbanter.date) AS udate " .
	"FROM buzzbanter_today buzzbanter WHERE  is_live='1' " .
	"AND show_in_app='1' " .
	"AND approved='1' " .
	"AND buzzbanter.login!='(automated)' " . $filter . " " .
	"ORDER BY date DESC " . $limit;
*/

$rows = exec_query($qry);

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Buzz and Banter</title>
<link rel="stylesheet" type="text/css" href="includes/bookmark.css" />
<style type="text/css">
	.article {margin-right:15px;}
	a.overLeft {margin-left:-17px; margin-bottom:10px;}
	a.overLeft img, a.overLeft:visited img {border:none;}
</style>
<script type="text/javascript" src="includes/prototype.js"></script>
<script type="text/javascript" src="includes/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="includes/AnchorPosition.1.1.js"></script>
<script type="text/javascript" src="includes/buzz.js"></script>
<?php
if (!$bbid and !$date) {
echo '<script language="JavaScript">
window.moveTo(0,0);
window.resizeTo(screen.width,(screen.height-75));
</script>';}




?>
<script language="JavaScript">
	var omitformtags=["input", "textarea", "select"]

	omitformtags=omitformtags.join("|")

	function disableselect(e){
		if (omitformtags.indexOf(e.target.tagName.toLowerCase())==-1)
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
</script>
</head>

<body class="<?= $body; ?>" <?=$onload?>>
<img align=left src="<?= $IMG_SERVER; ?>/images/logo.gif" hspace="10" vspace="10"/>
<a href="javascript:print();"><img border=0 src="images/print_all.gif" hspace="30" vspace="30"/></a>
<br clear=both>
<div id="content" class="bookmark" <?php echo 'style="height:100%;overflow:visible"';?>>

<?php
foreach ($rows as $row) {

	if ($row['author'] == "") {
	$author = $row['bbauthor'];
	}
	else {
	$author = $row['author'];
	}
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
?>

<div class="article">

	<div class="articleInfo">
		<img class="character" src="<?=$IMG_SERVER?><?=$row['image']?>" />
		<p class="author"><?=$author?></p>
		<p class="time"><?=$row['mdate']?></p>
		<p class="positions"><?php echo $row['position']; ?></p>
		<br clear=both>
	</div>
<div class="articleBody">
<?php
	if ($chars==1) {
			if(is_file($img4="$D_R$row[logo_asset]")){
				list($w,$h)=getimagesize($img4);
				do  {
					$w=ceil($w*.9);
					$h=ceil($h*.9);
				} while ($w > 180 || $h > 60);
				echo '<img src="' . $row['logo_asset'] . '" border=0  width="' . $w . '" height="' . $h . '" /> ';
			}
	}
$strbranded='';
	if(strlen($brandedlogoImage1)){
		$strbranded='<a href="'.$resultBrandedLogo[url].'" ><img border="0" src="' . $brandedlogoImage1 . '"></a><br><br>';
	}
echo '
<h2>' . $row['title'] . '</h2>' .
$strbranded .
$row['body'] . '<br /><br />' . displayChartBuzzImages($row['id']).
'</div>
</div><br clear=both>
';
if (strlen($row['body']) < 50)
{
echo "<br><br><br><br>";
}
if (strlen($row['body']) < 200)
{
echo "<br><br><br><br>";
}
}?>

<div id="footerBK" style="position:relative">
&copy;2008 Minyanville Publishing &amp; Multimedia LLC, All Rights Reserved.</div>
</div>
<? if ($date == null) {?>
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
	if (a > 115) {
		var c = a - 110 + "px"; // content div
		document.getElementById("content").style.height = c;
	}
}

window.onresize = resizeBookmark;
window.onload = function() {resizeBookmark; makeLinksIntoPopupsPr();<? if (!$date) echo 'print();';?>}
</script>
<? }else{ ?>
<script language="javascript">
window.onload = function() {makeLinksIntoPopupsPr();}
</script>
		<?php }


} else {
?>
<table width="100%">
	<tr>
		<td height="200" align="center"><div align="center" style="color:#FF0000">Your session is expired. Please login to
		<a href="<?=$HTNOSSLDOMAIN?>" target="_new">www.minyanville.com</a>
		</div></td>
	</tr>
</table>
<?
} ?>
</body>
</html>