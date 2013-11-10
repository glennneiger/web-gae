<?
//phpAdServe zone ID
global $D_R;
include_once($D_R."/lib/_content_data_lib.php");
session_start();
if($_SERVER['REQUEST_URI'])
{
        $articleArr = getKeyVal('a,p,from',$_SERVER['REQUEST_URI']);
}
$articleid = $articleArr['a'];
if(!$_SESSION['Cooper'])
{
	$param="";
	if($_SESSION['SID'])
	{
		$param ="&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
	}
	header( "Location: http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=cooper".$param);

}
$pageName = "cooperhome";
$zoneIDSky = $SEC_TO_ZONE['articletop'];
$zoneIDFooter = $SEC_TO_ZONE['articlefooter'];
$zoneIDFooter2 = $SEC_TO_ZONE['articlefooter2'];
$zoneIDBottom2 = $SEC_TO_ZONE['articlebottom2'];


$objContent = new Content('Cooper',$articleid);

include_once("$D_R/lib/layout_functions.php");
$pageJS=array("config","registration","iboxregistration","nav","search");
$pageCSS=array("global","rightColumn","nav","minyanville");
include("$D_R/_header.htm");
include_once("$D_R/lib/_auth.php");

build_lang('cooperhome');
global $ProfessorPerm,$lang,$HTPFXSSL,$HTHOST;

$USER=new user($_SESSION[EMAIL],$_SESSION[PASSWORD]);
$loginid = $USER->id;
//$strProfessorId =  getFlexfoliopageid('cooperhome');
$name="Jeffrey Cooper";
$strProfessorId=get_contributor_id_byname($name);
if(!$_SESSION['LoggedIn']){
	loginRedirection();
} else{
	$logincooper = $USER->is_cooper();
	if(!$logincooper){
		echo '<script>alert("Please register for Cooper. ");</script>';
		location($HTPFXSSL.$HTHOST."/subscription/register/");exit;
	}
}

if (($articleid != "") && (is_numeric($articleid))) {
	$articleid = $articleid;
	$article = getArticlecooper($articleid);
	if ($article != 0) {
		$articleSet = true;
	} else {
		$articleSet = false;
	}
}

//some old images will point to gazette/newsviews redirect this to articles/index.php
$article['body'] = str_replace("gazette/newsviews/?id","articles/index/a",$article['body']);
$article['body']=changeImageUrl($article['body']);
//those old links inside the body of the article shouldn't open in  a new window.
//$article['body'] = str_replace("_blank","_self",$article['body']);

//$article['link'] = $HTNOSSLDOMAIN . makeArticleslinkcooper($article['id'],$article['keyword'],$article['blurb']);

/* Static for cooper article*/
$article['authorid'] = 90;

?>

<!--Body area start from here-->
<div class="shadow">
<div id="content-container">
<!--left contaner start from here-->
<div class="subs_common_middle_container" >

<?if($_SESSION['LoggedIn'] ){
$oid = obejctid('Cooper');

?>


 <script language="javascript" type="text/javascript">
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

function disableclick()
{
	document.onmousedown=new Function("return false");void(0);
}
function enableclick()
{
	document.onmousedown=new Function("return true");void(0);
}

function cooperprint(articleid) {
	window.open(host + "/cooper/articles/print.php?a="+articleid,"print","width=600,height=550,resizable=yes,toolbar=no,scrollbars=yes");
}
</script>
<div class="left_contant">

<!---Cooper main start from here-->
<div class="cooper_container" >
<div class="cooper_common_title"><?= $article['title']; ?></div>
<!--Cooper sub heading area start from here-->

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
    <td colspan="2" ><?=displayAuthorLink($article['author'],$article['authorid']); ?>&nbsp;<?= $article['date'];?><a href="javascript:cooperprint(<?= $articleid; ?>);" target="_self" style='text-decoration:none;color: #083d70;'><img src="<?=$IMG_SERVER?>/images/icons/print-icon.gif"  width="16" hspace="5" height="14" border="0"alt="Print" />Print</a>   </td>
    </tr>
  <tr>
    <td colspan="2"  >&nbsp;
	</td>
    </tr>
  	<tr><td valign="bottom"> <?
		if ($article['character_text'] != "") {	?>
			<div class="balloonContainer"><table align="left" width="60%" border="0" cellspacing="0" cellpadding="0">
			<tr><td valign="top"><img src="<?= $IMG_SERVER . str_replace("characters","characters/",$article['imageURL']); ?>"   width="70" height="77" /></td>
			<td><?  showTalkBubbleArticle($article['character_text']); ?></td></tr></table></div>
	<?
		}?>
		</tr></table></div>
      </td>
	  </tr>
    </table></td>
  </tr>
</table>

<!--Cooper sub heading area end here-->

<!--Cooper Article area start from here-->
			<div class="cooper_article_main">
			 <table width="590" align="left" border="0" cellspacing="0" cellpadding="15">
			  <tr><td   valign="top" align="left"><?=$article['body'] ?></td> </tr>
			  <tr><td   valign="top" align="left"><font color="red"><?= $article['position']; ?></font></td></tr>
			  <tr><td   valign="top" align="left"><?=$article['disclaimer']; ?></td></tr>
			</table>
			</div>
<!--Cooper Article area start from here-->

<!--Cooper register area start from here-->



</div>
<!---Cooper main end here-->

</div><!--left contaner end here-->

<!--right contaner start from here-->
<div class="right_contant"  >
	<? prepareRelatedArticlesBox($article,$strProfessorId,$oid);


	if($_SESSION['LoggedIn']){
		?><!--Cooper Recent Report box end here-->

		<div class="search_alert_cooper">
		<table width="300" border="0" cellpadding="3" cellspacing="5">
		  <tr>
		  <td colspan="2" align="center"><h2>alerts search</h2> </td>
		  <tr>
			<td ><input name="coopersearch"  id ="coopersearch" class="quint_alert_input" type="text"  onmouseover="enableclick();return true;"  /></td>
			<td valign="top"><a href="#"><img src="<?=$IMG_SERVER?>/images/redesign/go.jpg" border="0"   onclick="searchalert('coopersearch','<?=$strProfessorId?>','cooperhome','<?=$oid?>');"/></a></td>
			<tr><td colspan="2" align="center"><a href="#" onclick="searchalert('coopersearch','<?=$strProfessorId?>','cooperhome','<?=$oid?>');" class="go_to_archive">go to archive</a></td></tr>
		  </tr>
		</table>

		</div>
		<?} ?>	<!--Rss and Article alert start from here-->
	<table width="100%" border="0" cellspacing="5" cellpadding="0" align="left">
	  <tr>
		<td><img src="<?=$IMG_SERVER?>/images/rss_logo.jpg" alt="Rss" /></td>
		<td class="quick_links"><a href="<?$HTPFX.$HTHOST?>/rss/">add rss feed</a></td>
		<td><img src="<?=$IMG_SERVER?>/images/article_alert_logo.jpg" alt="Free article" /></td>
		<td class="quick_links"><?if(!$loginid ){
		$url=$HTPFXSSL.$HTHOST."/subscription/register/iboxindex.htm";$linkId="navlink";$label='free article alerts';echo iboxCall($linkId,$label,$url);
		}else{
			echo  '<a href="'.$HTPFXSSL.$HTHOST.'/subscription/register/controlPanel.htm">free article alerts</a>';
		}

		?></td>
	  </tr>
	</table>

	<!--Rss and Article alert start from here-->

<?}?>
</div>
<script>
$('logouttarget').value=window.location.pathname;
</script>

<!--right contaner end here-->
</div><!--Content contaner end here-->
</div> <!-- shadow end -->
<? include("$D_R/_footer.htm"); ?>