<?
session_start();
global $HTPFX,$HTHOST;
include_once("$D_R/lib/_auth.php");
include_once("$D_R/lib/layout_functions.php");
$USER=new user();
$isLogin = $USER->id;
$logincooper = $USER->is_cooper();
?>
<?php
if(!$isLogin)
{
		echo '<script>alert("Please register for Cooper.");</script>';
		location("$HTPFX$HTHOST/cooper/");exit;
}
elseif(!$logincooper)
{
		echo '<script>alert("Please register for Cooper.");</script>';
		location("$HTPFX$HTHOST/cooper/");exit;
}
else
{
	if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$articleid = $_GET['a'];
	$article = getArticlecooper($articleid);
		/*if ($article != 0) {
			$articleSet = true;
		} else {
			$articleSet = false;
		}*/
	}
}
$pageName = "article_template";
?>
<html>
<head>
	<script language="javascript" type="text/javascript">
	function docKeyDown1(oEvent){ // controls keyboard navigation
	var oEvent = (typeof oEvent != "undefined")? oEvent : event;
	var oTarget = (typeof oEvent.target != "undefined")? oEvent.target : oEvent.srcElement;
	var intKeyCode = oEvent.keyCode;

	if(intKeyCode==17)
	{
		inc=0;

	}
	if (inc==0)
	{
		if(intKeyCode==65 || intKeyCode==97 )
		{

			return(false);   //in case of IE & firefox1.5

		}


	}

	}
	document.onselectstart=new Function("return false");
	document.onmousedown=new Function("return false");
	document.oncontextmenu=new Function("return false");
	document.onkeydown=docKeyDown1;

	function disableclick()
	{
		document.onmousedown=new Function("return false");void(0);
	}
	function enableclick()
	{
		document.onmousedown=new Function("return true");void(0);
	}
</script>
</head>

<body onLoad="javascript:window.print();">
<div id="header">
	<p><img src="<?= $IMG_SERVER; ?>/images/logo.gif"></p>
	<br>
	<h4><?= $article['title']; ?></h4>
    <?= $article['date'];?>
</div>
<p class="simple-separator">&nbsp;</p>
<div class="cooper_article_main">
<br>
<?= $article['body']; ?>
</div>
<br><br>


<!-- positions -->
<div class="positions">
<font color="red"><?= $article['position']; ?></font>
</div>
<br>
<!-- disclaimer -->
<div class="disclaimer">

<?= $article['disclaimer']; ?>
</div>
<br>

</body>
</html>