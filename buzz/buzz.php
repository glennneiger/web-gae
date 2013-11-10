<?php
header("Pragma: nocache\n");
header("cache-control: no-cache, must-revalidate, no-store\n\n");
header("Expires: 0");
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/gazette/buzzbanter/_authenticate.htm");
global $CDN_SERVER;
$zoneIDBuzzbanter=$SEC_TO_ZONE['BuzzBanter'];
$sid = $USER->user_id;

if(!empty($sid)){
	$buzzLaunchUrl=buzzAppUrlEncryption();
	header("Location: ".$buzzLaunchUrl);
	exit;
}

if (!$sid) {
	header("Location: http://www.minyanville.com");
	exit;
}

$qry2="SELECT ssid FROM subscriber_buzzbanter_preferences where ssid = " . $sid;
$res2=exec_query($qry2,1);

if (!count($res2))
{
//	initialize preferences

insert_query("subscriber_buzzbanter_preferences", array('ssid'=>$sid));


}

$qry = "SELECT * FROM subscriber_buzzbanter_preferences WHERE ssid=" . $sid;
$rows = exec_query($qry);
$row = ($rows) ? $rows[0] : null;
if ($row) {
	$characters = $row['characters'];
	$alerts = $row['alert'];
	$window_size = $row['window_size'];
	$text_size = $row['text_size'];
	$auto_jump = $row['auto_jump'];
	$posts_per_page = $row['posts_per_page'];
}

switch ($text_size) {
	case 'm': $bodyClass = 'sizeMedium';break;
	case 'l': $bodyClass = 'sizeLarge'; break;
	default : $bodyClass = 'sizeSmall'; break;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Buzz & Banter</title>
<link rel="stylesheet" type="text/css" href="<?php echo $CDN_SERVER; ?>/buzz/includes/buzzMainContent.1.2.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $CDN_SERVER; ?>/buzz/includes/buzzTDContent.css" />
<script type="text/javascript" src="<?php echo $CDN_SERVER; ?>/buzz/includes/prototype.js"></script>
<script type="text/javascript" src="<?php echo $CDN_SERVER; ?>/buzz/includes/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?php echo $CDN_SERVER; ?>/buzz/includes/AnchorPosition.1.1.js"></script>
<script type="text/javascript" src="<?php echo $CDN_SERVER; ?>/buzz/includes/buzz.1.20.js"></script>
<!-- google anaytics  code -->
<? googleanalytics();
universalGoogleAnalytics(); ?>
<!--  <script src="<?=$pfx?>/js/ads.js" type="text/javascript"></script>  -->
<script type="text/javascript" language="javascript">
	var sid = <?=$sid?>;
	var characters = <?=$characters?>;
	var alerts = <?=$alerts?>;
	var window_size = '<?=$window_size?>';
	var text_size = '<?=$text_size?>';
	var auto_jump = <?=$auto_jump?>;
	var posts_per_page = <?=$posts_per_page?>;
	var numTeachers = null;
	var latestPost = 0;
	var latestID = null;
	var currentPage = 1;
	var nav = null;
	var prefs = {reload: 0, changed: 0};
	var post = {author: null, type: null, ID: null, title: null, articleID: null};
	var lastPage = 0;
	var jumpToId = null;
	var jump = {gotoPos: null, oldHeight: null};
	var postID = null;
	var author = null;
	var buzzWidth = null;
	var firstLoad = 0;
	var latestTimeStamp = 0;
	var topPage = null;

</script>
<!-- Begin comScore Tag -->
<script>document.write(unescape("%3Cscript src='" + (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js' %3E%3C/script%3E"));</script>
<script>COMSCORE.beacon({c1:2,c2:7716423,c3:"",c4:"",c5:"",c6:"",c15:""});</script>
<noscript><img src="http://b.scorecardresearch.com/p?c1=2&c2=7716423&c3=&c4=&c5=&c6=&c15=&cj=1" /></noscript>
<!-- End comScore Tag -->
</head>

<body class="<?=$bodyClass?>" onload="init();">
<div id="logoBar"><a href="javascript:void(0);" onclick="javascript:launchPage('http://www.minyanville.com','homepage');return false"><img src="/buzz/images/logo.gif" /></a></div>
<div id="flashBar"><div id="flashLasso" style="height:36px; ">
<object id="movie22" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="300" height="36" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="buzz.swf" /><param name="menu" value="false" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />
<embed src="buzz.swf" name="movie22" swLiveConnect="true" menu="false" quality="high" bgcolor="#ffffff" width="300" height="36" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</div></div>
<div id="dropdownBar"><div id="dropdownCenterer">
	<div id="jumpToPost"></div>
	<div id="bookmark"></div>
</div></div>
<div id="tabBar">
	<div id="tabBuzz" class="buzzOn"></div>
	<div id="tabSearch" class="searchOff"></div>
</div>
<div id="outerOuterBorder"><div id="innerOuterBorder">

<div id="content" class="mainLayer">


	<div id="articles"></div>

	<div id="shwprogressdata"  style="display:inline;"> </div>

	<div id="navigation"></div>
</div>

<!-- closes content mainLayer -->


<div id="search" class="mainLayer" style="display:none; ">
<table><form id="searchForm" name="searchForm" onsubmit="return false">
	<TR valign=top>
	  <TD width=1% nowrap><span class="number">1.</span> Keyword:<br>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and/or</TD>
  	  <TD width=99%>
		<input type="text" class="textinput"  size="20" maxlength="255" name="q" value="" id="q"  />
	  </TD>
	</TR>
	<TR valign=top>
		<TD rowspan=2><span class="number">2.</span> Date:</TD>
		<TD><?month_options("mo","","","mo")?>
			&nbsp;&nbsp;
			<?day_options("day","","","day")?>
			&nbsp;&nbsp;
			<?year_options2("year",$year,"",2002,date("Y"))?>
		</TD>
	</TR>
	<TR>
		<TD>
		<select name="author" id="author">
			<option value="">--All Authors--</option>
				<?php selectHashArr(get_buzzactive_contributors(),"name","name",$author); //works even without including layout_functions! ?>
			</select>	</TD></TR><TR>
	<TD><div class="number">3.</div></TD>
	<TD><input type="button" value="Search Buzz & Banter" id="searchButton" /></td>
			</TR></form>
	  </table>

<div id="searchResults"></div>

</div>

<?php include_once("_preferences.php"); ?>
<!-- footer starts here -->
<div class="clear"></div>
<div id="footerContainer">
	<div class="yahooWrapper"><div id="shwprogressdataval"  style="display:inline;"> </div>
		<div class="yahooGrayLine"></div>
		<div id="yahooSearch" align="center">
			<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
						    <td align="left" width="130px">
						    <td nowrap align="left" width="65px" style="font-size:12px; font-family:Arial;">Get Quotes</td>
						    <td width="90px">
						    <form name="yahoo" id="yahoo" method="get" onSubmit="popWin();return false">
						    <input align="left" type="text" class="symbol" id="s" name="symbol"/>
						    <input type="hidden" id="yahooLookup" />
						    </form>
						    </td>
						    <td style="padding-left: 0px;">
						    <a class="btnLink1" id="dd" onClick="popWin();return false" href="#" style="width: 18px;" stylename="Priority">
						    <div id="div" class="btnPriorityOnOuter1" style="width: 28px;">
						    <div id="div2" class="btnPriorityOnMiddle" style="width: 26px;">
						    <div id="div3" class="btnPriorityOnInner" style="width: 24px; font-size: 11px; line-height: 15px;">Go</div>
						    </div>
						    </div>
						    </a>
						    </td>
			  			</tr>
  			</table>
		</div>
	</div>
	<div id="featureBarLasso">
	<div id="featureBar">
		<div id="editPrefs" class="prefButtonOn"></div>
		<div id="viewFeatures"></div>
	</div>
	</div>
	<div id="bottomLinks">
		<a href="javascript:void(0);" onclick="javascript:launchPage('http://www.minyanville.com/company/contact.htm','contact',0);">Contact</a> &bull;
		<a href="javascript:void(0);" onclick="javascript:launchPage('http://www.minyanville.com/company/disclaimers.htm','disclaimers',0);">Disclaimers</a> &bull;
		<a href="javascript:void(0);" onclick="javascript:launchPage('http://www.minyanville.com/company/substerms.htm','substerms',0);">Subscription &amp; Terms of Use</a>
	</div>
	<div id="footer">&copy;<?=date('Y');?> Minyanville Media, Inc. All Rights Reserved.</div>
</div> <!-- closes footerContainer div -->
</div></div> <!-- closes the border divs -->

<?
####### get latest trading radar article #####
$tradingradararticle = getLatestCategoryArticle(11); ?>
<!-- Dropdown menu layers start here -->
<div id="postMenu" class="popupMenu" style="display:none; "></div>
<div id="bookMenu" class="popupMenu" style="display:none; "></div>
<div id="viewPopup" style="display:none; ">
	<ul>
		<li><a href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/markets','markets',1);">Markets</a></li>
        <li><a href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/investing/','investing',1);">Investing </a></li>
        <li><a href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/dailyfeed','dailyfeed',1);">Daily Feed</a></li>
		<li><a class="offset" href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/library/dictionary.htm','dictionary',1);">Dictionary</a></li>
		<!-- <li><a href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/retailroundup/','retailroundup',1);">Retail Roundup</a></li>-->
		<!--<li><a href="javascript:void(0);" onclick="launchPage('<?=$HTPFX.$HTHOST?>/articles/index.php?a=<?=$tradingradararticle[id];?>','tradingradar',1);">Trading Radar</a></li>-->
		<li><a class="offset" href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/library/search.htm','libsearch',1)">Search the Archive</a></li>
		<li><a  href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/gazette/bios.htm','professors',1)">Meet our Professors</a></li>
		<li><a class="offset" href="javascript:void(0);" onclick="popupViewer(2);toggleViewPopup();">Our Town</a></li>
		<li><a href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/company/help.htm','help',1)">Help</a></li>
		<li><a class="offset" href="javascript:void(0);" onclick="launchPage('http://www.minyanville.com/articles/index.php?a=11179','faq',1)">Buzz FAQ</a></li>
		<? if(date('w')==1) { ?>
				<li><a href="javascript:void(0);" onclick="javascript:launchPage('/buzz/print.php?date=<?=date('m/d/Y',(time()-(3*86400)))?>','yesterday',1);">Yesterday&rsquo;s Buzz</a></li>

		<? }else { ?>
				<li><a href="javascript:void(0);" onclick="javascript:launchPage('/buzz/print.php?date=<?=date('m/d/Y',(time()-86400))?>','yesterday',1);">Yesterday&rsquo;s Buzz</a></li>
		<? } ?>
	</ul>
</div>
</body>
</html>
