<?php
include_once($D_R.'/lib/_exchange_lib.php');
require_once($D_R.'/lib/ap/_view.php');
$pageType = "apArticle";
$pageName = "article_template";
$AParticleId = $_GET["a"];

$objapArticleController=new apNewsView();
$articleArray = $objapArticleController->showSingleArticle($AParticleId);
$articleArray = $articleArray[0];

$headline = $articleArray['headline'];
$body = $articleArray['body'];
$bodytag = html_entity_decode($body);
$location = $articleArray['location'];
$author = $articleArray['author'];
$articleDate = date('M d, Y g:i a',strtotime($articleArray['date']));
$pageId=2;
$ib_tracking_zone="article";
include("../_minyanville_header.htm");
include_once("../lib/layout_functions.php");

global $cm8_ads_MicroBar,$cm8_ads_MediumRectangle,$cm8_ads_1x1_Text,$cm8_ads_Button_160x30,$cm8_ads_Leaderboard;
$title= $headline;

$subscription_id=$_SESSION['SID'];
	if($subscription_id==""){
		$subscriptionid=$_SERVER['REMOTE_ADDR'];
	}else{
		$subscriptionid=$subscription_id;
	}
	$parentid=getSectionid($secaid['page_id']);
	if($parentid['parent_id']==0)
		$parentid['parent_id']=$parentid['id'];
	$securl=getSubsectionUrl($secaid['page_id']);
?>
	<script src="<?=$HTPFX.$HTHOST?>/js/friends.js" type="text/javascript"></script>
	<script src="<?=$HTPFX.$HTHOST?>/js/Articles.1.9.js" type="text/javascript"></script>
	<script src="<?=$HTPFX.$HTHOST?>/js/AC_RunActiveContent.js" type="text/javascript"></script>
<?
if($subscription_id){
		$profile_exchange=$_SESSION['EID'];
}
$isexchangeuser=0;
$isemailalerts=0;
$isexchangeuser=$USER->is_exchange;
$userid=$USER->id;
$userid1=0;
$isloggedin=0;
$userEmail='Enter email Address';
if(isset($userid)){
	$isloggedin=1;
	$userEmail=$USER->email;
}else{
	$isloggedin=0;
}
if((isset($userid))&&($userid!='')){
	$isemailalerts=$USER->is_emailalerts($userid);
}

//modules are in associative array - key (id)  => value[name]
$modules = getModules();
$objThread = new Thread();
$objaddrequest=new friends();
$friends=$objaddrequest->get_friend_list($subscription_id,'','','');
$objLink = new Exchange_Element();

//get article information
if (($_GET['a'] != "") && (is_numeric($_GET['a']))) {
	$articleid = $_GET['a'];

	//$article = getArticle($articleid); /* call twice*/

	if ($article != 0) {
		$articleSet = true;

		if (!$USER->isAuthed) {
			$loggedin = "no";
		} else {
			$loggedin = "yes";
		}

               // logArticleView($article['id']);

	} else {
		$articleSet = false;
	}
}
$threadArray = $objThread->get_thread_on_object($articleid);
$item_type_id=$objThread->get_object_type($itemtype);
$tags = $objThread->get_tags_on_objects($articleid, $item_type_id);
	$count=$_GET[p];
	if(!$count){
		$count=0;
	}
$article['link'] = $HTNOSSLDOMAIN . makeArticleslink($article['id'],$article['keyword'],$article['blurb']);

$pages=count($articlebody);
$fbody='';
for ($u=0; $u<$pages; $u++)
{
    $fbody.= $articlebody[$u][body];

}
$unique_stocks= make_stock_array ($fbody);
$articlebody[$count][body] =make_stock_links($articlebody[$count][body]);
$articlebody[$count][body] = change_ssl_url($articlebody[$count][body]);
$threadid=$threadArray['id'];
$appcommentcount = $objThread->count_all_comments($threadid,$user=0,$content_id="");


	if($threadid=="" and $article['approved']=='1'){
	global $defaultSudId;
	$currDate=date('Y-m-d, H:i:s');
	$threadtitle=htmlentities($article[title],ENT_QUOTES);
	$threadval[title]=$threadtitle;
	$threadval[thread_posts]='0';
	$threadval[approved]='1';
		$threadQryresult = "SELECT character_text as body,section_id as section_id FROM `articles` WHERE id ='$article[id]'";
		$threadQry=exec_query($threadQryresult,1);
	$threadbody=htmlentities($threadQry[body],ENT_QUOTES);
	$threadval[teaser] = $threadbody;
	$threadval[section_id] = $threadQry[section_id];
		$threadqry = "SELECT subscription_id as subid FROM `ex_contributor_subscription_mapping` WHERE contributor_id
	='$article[authorid]'";
		$threadQry=exec_query($threadqry,1);
	$threadval[author_id]=$threadQry[subid];
		if(!$threadval[author_id]){
		$threadval[author_id]=$defaultSudId;
		}
	$threadval[created_on]=$currDate;
	$threadval[content_table]=$itemtype;
	$threadval[content_id]=$article[id];
	$threadval[is_user_blog]=0;
			if($threadval[content_id]){
				$threadid=insert_query("ex_thread",$threadval);
			}
	}

	if(is_array($tags))
	{
	foreach($tags as $key=>$val){
		$thread['tag_id']=$val[id];
		$tagid=$thread['tag_id'];
		$thread['item_id']=$threadid;
		$thread['item_type']="4";
		$threadTagqry = "select id from ex_item_tags where tag_id='$val[id]' and item_id = '$threadid'";
		$threadTagres=exec_query($threadTagqry);
		$count=count($threadTagres);
		if($count=='0'){
			$thread['item_id']=$threadid;
			$thread['tag_id']=$val[id];
			$thread['item_type']="4";
			$threadTag=insert_query("ex_item_tags", $thread);
		}
	}
	}
	?>
<style>
h3 {
	margin-top:10px;

}

.article_text_body p {
	margin-bottom:15px;
	font-size:13px;
	line-height:16px;


}

</style>

<title>$headline - Minyanville</title>
<!--Body area start from here-->
<!--left contaner start from here-->
<div class="left_contant">
<!--Family left content start from here-->
<div class="left_contant_lifenmoney">
<div class="article_title">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td valign="top"><?=$headline;?>
	<h3>Associated Press
	<span class="post_time"><?
					echo $articleDate;
						?></span></h3>

	</td>
  </tr>

</table>
</div>
<div class="article_comments">
<!--table align="left" width="60%"  border="0" cellspacing="0" cellpadding="0">
			  <tr>
    <td align="left" width="77"><img src="<?= $IMG_SERVER . str_replace("characters","characters/",$article['imageURL']); ?>" alt="<?=$article['title'];?>" width="70" height="77"/></td>
    <td>
	<? if ($article['character_text'] != "") {
		showTalkBubbleArticle($article['character_text']);
	} ?>
    </td>
			  </tr>
          <tr><td></td><td ><h2><?
								    	global $_SESSION;
										$bannername=$cm8_ads_1x1_Text;
										$referer_url=parse_url($_SESSION['referer']);
										list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
								if($domain!="ameritrade") {
									CM8_ShowAd($bannername);
								 } ?></h2></td></tr>

</table-->
<!-- Two way area start from here-->
<?
	$count=$_GET['p'];
	$author=$article['author'];
	$authorid=$article['authorid'];

?>
<!-- Two way area end here-->
</div>

<div class="article_text_body">
 <!--div class="fedex_article" ><? $bannername_print_share='235x166_PrintShare'; CM8_ShowAd($bannername_print_share); ?>
</div-->
<? echo $bodytag; ?>
<?
	if(!$count){
		$count=0;
		}
	$getWidgetPos=getWidgetPos($articlebody[$count][body],5);
 ?>

<?= substr($articlebody[$count][body],0,$getWidgetPos); ?>

<?
if($parentid['parent_id']!="4")
	//showFinancialContent($unique_stocks,$count,$articleid,$author,$authorid,$tags);
//$getBRCount=getWidgetBreakes($articlebody[$count][body],'<br />');

//if($getBRCount>10)
//	showSkyscraperAd();
?>
<?= substr($articlebody[$count][body],$getWidgetPos); ?>
<? if($parentid['parent_id']=="4") {?>
<br /><br />
<i>Your kids can learn about money while having fun in <a href="http://www.minyanland.com"><b>MinyanLand.com</b></a>.</i>
<? } ?>
     </div>

<!--Pagination start from here-->
<div>
<table align="left" width="100%"  border="0" cellspacing="0" cellpadding="0">
     <tr><td colspan="2" align="right">
	 <? if($_GET['from']=="yahoo" or $_GET['from']=="YAHOO"){ ?>
					<a href="http://finance.yahoo.com"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/fi/gr/yahoo_buttons/yfprplbtn_130x30.gif" align="middle" /> </a>
		<? } ?></td></tr>
     <tr>
    <td>
		<!--start article pagination-->
				<?
			if(count($articlebody)){
				$numpages=count($articlebody);
			} else {
				$numpages=0;
			}
			if($numpages>1){
				articlePagination($numpages,$articleid,$article['keyword'],$article['blurb'],$count);
		}
		?>
		<!--end article pagination-->

					</td>
    <!--<td align="right"  class="bottom_common_link"><a style="font-weight:bold;" href="<?=$HTPFX.$HTHOST;?><?=$securl['alias']?>">more <?=$secaid['name']?> articles</a> &#187;</td>-->
				  </tr>
</table></div>
<!--Pagination end here-->

<!--Rate this article area start from here-->
<!--Rate this article area end here-->
<?
$from='articles';
$imagevalue=0;
$showcomment=1;
?>

<!--Article bottom main start from here-->
<div class="article_bottom_main">
<div>
<em>
Associated Press text, photo, graphic, audio and/or video material shall not be published, broadcast, rewritten for broadcast or publication or redistributed directly or indirectly in any medium. Neither these AP materials nor any portion thereof may be stored in a computer except for personal and non-commercial use.  Users may not download or reproduce a substantial portion of the AP material found on this web site.  AP will not be held liable for any delays, inaccuracies, errors or omissions therefrom or in the transmission or delivery of all or any part thereof or for any damages arising from any of the foregoing.
</em>
</div>
			</div>
<!--Article bottom main end here-->
			</div>
<!--left contaner end here-->


</div>





<!--right contaner start from here-->
<div class="right_contant" >
<?
$Right_panel_video  ='true';
$Right_panel_member ='false';
$Right_panel_financialcontent='true';
$Right_panel_right_ads='true';
$Right_panel_polupararticle='true';
$Right_panel_featuredprof='true';
$featuredProfId=$article['authorid'];
$Right_panel_emailalertbox='true';
$Right_panel_Rssemailalertbox='true';
$Right_panel_right_ads=='true';
$Right_panel_gpom = 'true';
$Right_panel_tickertalk = 'true';
$bannername=$cm8_ads_MediumRectangle;
CM8_ShowAd($bannername);
// show_ads_operative($zone_name,$tile300x250,$ADS_SIZE['MediumRectangle'],"","");
include_once("../_minyanville_right.htm");?>
<script type="text/javascript">
var dc_PublisherID = 48414;
var dc_AdLinkColor = 'blue';
var dc_isBoldActive = 'no';
</script>
<script type="text/javascript" SRC="http://kona.kontera.com/javascript/lib/KonaLibInline.js"></script>
<!--right contaner end here-->
</div>
<? include("../_minyanville_footer.htm"); ?>
