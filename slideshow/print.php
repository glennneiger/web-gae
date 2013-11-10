<?
global $D_R, $HTPFX,$HTHOST;
//phpAdServe zone ID
$zoneIDSky = 118;
$zoneIDSign = 119;


include($D_R."/layout/dbconnect.php");
include($D_R."/lib/layout_functions.php");
$title= "Article Template";
$pageName = "article_template";

//modules are in associative array - key (id)  => value[name]
$modules = getModules();

//get article information
if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$slideshow_id = $_GET['a'];

	if($_GET['slide_no'] =="")
	 {
     $slide_no = 1;
	 }
	 elseif(($_GET['slide_no'] !=="") && (is_numeric($_GET['slide_no'])))
	 {
	  $slide_no = $_GET['slide_no'];
	 }

	$slide = getFullslideshow($slideshow_id);

	if ($slideshow != 0) {
		$slideSet = true;

		if (!$USER->isAuthed) {
			$loggedin = "no";
		} else {
			$loggedin = "yes";
		}

	} else {
		$slideSet = false;
	}
}

//some old images will point to gazette/newsviews redirect this to articles/index.php
$slide['body'] = str_replace("gazette/newsviews/?id","slideshow/index.php?a",$slide['body']);

//those old links inside the body of the article shouldn't open in  a new window.
//$article['body'] = str_replace("_blank","_self",$article['body']);
?>
<html>
<head>
	<link rel="stylesheet" href="<?=$HTPFX.$HTHOST?>/css/print.1.2.css">
</head>

<body onLoad="javascript:window.print();">
<div id="header">
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"></p>
	<br>
	<h4><?= $slide[0]['title']; ?></h4>

</div>
<p class="simple-separator">&nbsp;</p>

<table width="100%" >
<? for($i=0;$i<$slide[0]['total_slides'];$i++) { ?>
<tr> <td>
    <h4><?= $slide[$i]['slide_title']; ?></h4>
    </td> </tr>
    <tr> <td>
	<div class="articleBody">
	<br>
	 <p> <?= $slide[$i]['body']; ?></p>
	</div> </td> </tr>

	<tr> <td>
	<p class="simple-separator">&nbsp;</p></td> </tr>
<? } ?>
</table>

</body>
</html>