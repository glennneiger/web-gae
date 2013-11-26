<?
//phpAdServe zone ID
global $D_R,$HTPFX,$HTHOST;
include_once($D_R."/lib/_content_data_lib.php");
include_once("$D_R/lib/layout_functions.php");
session_start();
if($_SERVER['REQUEST_URI'])
{
        $articleArr = getKeyVal('a,p,from',$_SERVER['REQUEST_URI']);
}

$articleid = ($_GET['a']=="" ? $articleArr['a'] :$_GET['a']);

if (($articleid != "") && (is_numeric($articleid))) {
	$article = getArticlecooper($articleid);
	$currentDate = date('M d, Y g:i a');
	$cooperDate = $article['date'];
	$dateDiff = date_difftime($cooperDate,$currentDate);
	$dateDiffVal = intval($dateDiff['d']);
	$olderThanYr='0';
	if($dateDiffVal>=365){
		$olderThanYr='1';
	}
	if ($article != 0) {
		$articleSet = true;
	} else {
		$articleSet = false;
	}
}

if(!$_SESSION['Cooper'] && $olderThanYr=='0')
{
	$param="";
	if($_SESSION['SID'])
	{
		$param ="&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
	}
	header( "Location: http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=cooper".$param);

}
$pageName = "cooperhome";
$objContent = new Content('Cooper',$articleid);
require_once($D_R."/lib/config/_cooper_config.php");
require_once($D_R."/lib/cooper/_cooper_data_lib.php");
require_once($D_R."/lib/cooper/_cooper_design_lib.php");
include_once($D_R."/layout/layout_functions.php");

$pageJS=array("config","redesign","fancybox","cooperRedesign","modernizr");
$pageCSS=array("ibox","global","layout","section","rightColumn","nav","minyanville","fancybox","cooperRedesign","sub_homepage");
include($D_R."/_header.htm");
$objCooperData = new cooperData('cp_articles');
$objCooperDesign = new cooperDesign();
global $ProfessorPerm,$lang,$HTPFXSSL,$HTHOST,$IMG_SERVER,$objCooperData, $objCooperDesign, $pageName;

if(empty($articleid)) {
	header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
	location("/errors/?code=404");
	exit;
}


$alertDetails = $objCooperData->getCooperCategory($articleid);
$categoryName = $alertDetails['category_alias'];

//some old images will point to gazette/newsviews redirect this to articles/index.php
$article['body'] = str_replace("gazette/newsviews/?id","articles/index/a",$article['body']);
$article['body']=changeImageUrl($article['body']);

?>
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
<script type="text/javascript">jQuery.noConflict() ;</script>
<script language="javascript" type="text/javascript">
	var startTimer;
	jQuery(document).ready(function(){
		jQuery.fancybox.init();
		jQuery('div.ca-item-main').fancybox({
			showCloseButton : false,
	        type: 'inline',
	        content: '#cooperLeavingWindow',
	        overlayOpacity : 0.8,
			overlayColor : '#000',
			onClosed : function(){
				//Close timer
				try{
					clearTimeout(startTimer);
					startTimer = null;
				}catch(err){
					
				}
			}
	    });
		jQuery('#fancybox-wrap').css('top','100px');
	});
	
	function docKeyDown1(oEvent){ // controls keyboard navigation
		var oEvent = (typeof oEvent != "undefined")? oEvent : event;
		var oTarget = (typeof oEvent.target != "undefined")? oEvent.target : oEvent.srcElement;
		var intKeyCode = oEvent.keyCode;
		var inc;
		if(intKeyCode==13) {
		   if($F("coopersearch")){
			searchalert('coopersearch','<?=$strProfessorId?>','cooperhome','<?=$oid?>');
			}
		}
		if(intKeyCode==17){	inc=0;}
		if (inc==0){
			if(intKeyCode==65 || intKeyCode==97 )	{
				return(false);   //in case of IE & firefox1.5
			}
		}
	}
	document.onselectstart=new Function("return false");
	document.onmousedown=new Function("return false");
	document.oncontextmenu=new Function("return false");
	document.onkeydown=docKeyDown1;

	function disableclick() {
		document.onmousedown=new Function("return false");void(0);
	}
	
	function enableclick() {
		document.onmousedown=new Function("return true");void(0);
	}
	
	function cooperprint(articleid) {
		window.open(host + "/cooper/articles/print.php?a="+articleid,"print","width=600,height=550,resizable=yes,toolbar=no,scrollbars=yes");
	}
</script>

<div id="cooper_wrapper">
	<?php $objCooperDesign->displayCooperHeader($categoryName); ?>
	<div class="clr"></div>
	<div class="cooper_contentPanel">
		<?php echo $objCooperDesign->displayCooperAlert($articleid,$article);?>
		<?php echo $objCooperDesign->displayCooperRightCol();?>
	</div>
	<div class="clr"></div>
</div>
</body>
<? include($D_R."/_footer.htm"); ?>