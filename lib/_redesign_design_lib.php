<?php
	function controlPannel(){
		global $lang,$HTPFXSSL,$HTHOST,$IMG_SERVER;
		$userid=$_SESSION['SID'];
		$objaddrequest=new friends();
		$objThread=new Thread();
		$countrequest = $objaddrequest->count_pending_request($userid);
		$count_msg=$objThread->count_msg($userid);
		$count_updates=$objThread->countUpdates($userid);
		$getfriendscount=$objaddrequest->get_friend_list_count($userid);
		$countfriends=$getfriendscount[0][count];
		$ownDiscussions=$objThread->show_discussions($userid,'own',"");
		$countdiscussion=count($ownDiscussions);
		$pageName="header";
		$optionsmith=$_SESSION['Optionsmith'];
		$buzz=$_SESSION['Buzz'];
		$cooper=$_SESSION['Cooper'];
		$flexfolio=$_SESSION['Flexfolio'];
		/*if($optionsmith && ($buzz || $cooper || $flexfolio )){
			$cpclass="control_panel";
			$cpclassinner="common_panel_inner";
		}else{
			$cpclass="control_panel_option";
			$cpclassinner="common_panel_inner_option";
		}*/
		$cpclass="control_panel_option";
		$cpclassinner="common_panel_inner_option";
?>
	<!--Control panel start from here-->
<div  class="main_nav_tabs">
	<div  id="maindiv">
		<div id="collaps_text" div="div" class="thelanguage">
			<div class="<?=$cpclass;?>">
				<!--Account area start from here-->
				<div class="account_name">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
					<td colspan="3" NOWRAP><?=$_SESSION['nameFirst']?>'s account
					<span class="log_out"><?= logout($userid,'log-out');?></span>
					<span class="common_panel_manage_heading"><a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/">MANAGE ACCOUNT SETTINGS</a></span>
					</td>
					<td  align="right" style="padding-right:3px;">
					<a href="" onClick="$$('div.d').each( function(e) { e.visualEffect('blind_up','d1',{duration:0.5}) }); return false;"><img src="<?=$IMG_SERVER?>/images/redesign/close.jpg"  hspace="10" vspace="2" border="0" onClick="getYourAccount(hide=1);"/></a></td>
  </tr>
</table>
</div>
					<?php
							//controlPannelExchange($countdiscussion,$countrequest,$count_msg,$count_updates,$countfriends,$cpclassinner);
							/*Account area end here*/
							/*Subscription area start from here*/
//							controlPannelSubscription($cpclassinner);
							/*Subscription area end here*/
							/*Watch list area start from here*/
					?>
					   <!--<div id="showwatchlist"></div>-->
					   <?
						// controlPannelWatchlist($userid,$pageName,$cpclassinner);
					?>
				<!--Watch list area end here-->
			</div>
		</div>
	</div>
</div>
<!--Control panel end here-->
<?php
	}

	function controlPannelExchange($countdiscussion,$countrequest,$count_msg,$count_updates,$countfriends,$cpclassinner){
			global $page_config;
?>
    <div class="common_panel">
		<div class="<?=$cpclassinner;?>">
			<div class="common_panel_header">exchange</div>
						<table  width="100%" border="0" cellspacing="0" cellpadding="0">
							  <tr >
								<td width="38%" class="panel_heading">discussions</td>
                                <td width="14%" class="panel_heading"><? if($countdiscussion)echo "(".$countdiscussion.")"; ?></td>
								<td width="48%"><div class="view_button"><a href="<?=$page_config['main_discussion']['URL'];?>">go</a></div></td>
							  </tr>
							  <tr class="highlight">
								<td class="panel_heading">updates</td>
                                <td class="panel_heading"><? if($count_updates)echo "(".$count_updates.")"; ?></td>
								<td><div class="view_button"><a href="<?=$page_config['home']['URL'];?>">go</a></div></td>
							  </tr>
							  <tr>
								<td class="panel_heading">requests</td><td class="panel_heading"><? if($countrequest)echo "(".$countrequest.")"; ?></td>
								<td><div class="view_button"><a href="<?=$page_config['home']['URL'];?>">go</a></div></td>
							  </tr>
							  <tr class="highlight">
								<td class="panel_heading">inbox</td><td class="panel_heading"><? if($count_msg)echo "(".$count_msg.")"; ?></td>
								<td><div class="view_button"><a href="<?=$page_config['inbox']['URL'];?>?userid=<?=$_SESSION['SID']?>">go</a></div></td>
							  </tr>
							  <tr>
								<td class="panel_heading">friends</td><td class="panel_heading"><? if($countfriends)echo "(".$countfriends.")"; ?></td>
								<td><div class="view_button"><a href="<?=$page_config['friends']['URL'];?>">go</a></div></td>
							  </tr>

							  <tr class="highlight">
								<td class="panel_heading">profile</td>
								<td>&nbsp;</td>
								<td><div class="view_button"><a href="<?=$page_config['profile']['URL'];?>">go</a></div></td>
							 </table>
					<div class="common_panel_manage_heading"><a href="<?=$page_config['privacy']['URL']?>">MANAGE EXCHANGE SETTINGS</a></div>
				</div>
			</div>

<?php	}

	function controlPannelSubscription($cpclassinner){
		$buzz=$_SESSION['Buzz'];
		$cooper=$_SESSION['Cooper'];
		$flexfolio=$_SESSION['Flexfolio'];
		$optionsmith=$_SESSION['Optionsmith'];
		$BMTP=$_SESSION['BMTP'];
		$Jack=$_SESSION['Jack'];
		$theStockPlayBook=$_SESSION['TheStockPlayBook'];
		build_lang('subscription_product');
	    global $lang,$HTPFX,$HTHOST;
	    $buzzLaunchUrl=buzzAppUrlEncryption();
?>
		<div class="common_panel">
			<div class="<?=$cpclassinner;?>">
				<div class="common_panel_header">Subscriptions</div>
				<table  width="100%" border="0" cellspacing="0" cellpadding="0">
				  <? if($buzz){ ?>
				  <tr>
						<td class="panel_heading">buzz &amp; banter</td>
						<td ><div class="view_button"><a style="cursor:pointer;"
  onClick="window.open('<?=$buzzLaunchUrl;?>',
  'Banter','width=455,height=708,resizable=yes,toolbar=no,scrollbars=no'); banterWindow.focus();"
  >launch</a></div></td>

					</tr>

				  <? }else{ ?>
					<tr>
						<td class="panel_heading" width="44%" valign="top">buzz &amp; banter</td>
						<td width="45%" valign="top">
							<div class="control_panel_cart">
							<table width="0%" align="right" border="0" cellpadding="0" cellspacing="0">
							 <tr><?  echo getaAddtoCartbtnsCP('buzzbanter','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
							</table>
							</div>
						</td>
					</tr>
				  <? }
				  if($flexfolio){ ?>
						<tr class="highlight">
							<td valign="top" class="panel_heading">flexfolio</td>
							<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/qp/index.htm">launch</a></div></td>
						</tr>
					<? }else{?>
						<tr class="highlight">
							<td valign="top" class="panel_heading">flexfolio</td>
							<td width="45%" valign="top">
								<div class="control_panel_cart">
								<table width="0%" align="right" border="0" cellpadding="0"  cellspacing="0">
								  <tr><?  echo getaAddtoCartbtnsCP('FlexFolio','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
								</table>
							</div>
							</td>
						</tr>
				<? }
				 if($optionsmith){ ?>
					<tr >
						<td valign="top" class="panel_heading">optionsmith</td>
						<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/optionsmith/index.htm">launch</a></div></td>
					</tr>
				<? }else{?>
					<tr>
						<td valign="top" class="panel_heading">optionsmith</td>
						<td width="45%" valign="top">
							<div class="control_panel_cart">
							<table width="0%" align="right" border="0" cellpadding="0"  cellspacing="0">
							   <tr><?  echo getaAddtoCartbtnsCP('optionsmith','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
							</table>
						</div>
						</td>
					</tr>
				<? } ?>
				  <? if($cooper){ ?>
					<tr class="highlight">
						<td valign="top" nowrap="nowrap" class="panel_heading">daily market <br />report</td>
						<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/cooper/">launch</a></div></td>
					</tr>
				  <? }else{?>
				  		<tr class="highlight">
						<td valign="top" class="panel_heading">daily market <br />report</td>
						<td width="45%" valign="top">
							<div class="control_panel_cart">
							<table align="right" width="0%" border="0" cellpadding="0"  cellspacing="0">
							  <tr><?  echo getaAddtoCartbtnsCP('Jeff Cooper','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
							</table>
						</div>
						</td>
					</tr>
				  <? }
				  if($Jack){ ?>
					<tr>
						<td valign="top" class="panel_heading">Lavery Insight</td>
						<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/laveryinsight/index.htm">launch</a></div></td>
					</tr>
				<? }
				if($BMTP){ ?>
					<tr class="highlight">

						<td valign="top" class="panel_heading">Bull Market Timer</td>
						<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/bmtp/index.htm">launch</a></div></td>
					</tr>
				<? } if($_SESSION['ETFTrader']){ ?>
				<tr>

						<td valign="top" class="panel_heading">Grail ETF & Equity Investor</td>
						<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/etf/home.htm">launch</a></div></td>
					</tr>
				<? }else{?>
					<tr>
						<td valign="top" class="panel_heading">Grail ETF & Equity Investor</td>
						<td width="45%" valign="top">
							<div class="control_panel_cart">
							<table width="0%" align="right" border="0" cellpadding="0"  cellspacing="0">
							  <tr><?  echo getaAddtoCartbtnsCP('etf','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
							</table>
							</div>
						</td>
					</tr>
				<? } if($theStockPlayBook){ ?>
								<tr>

										<td valign="top" class="panel_heading">The Stock PlayBook</td>
										<td><div class="view_button"><a href="<?=$HTPFX.$HTHOST?>/thestockplaybook/">Launch</a></div></td>
									</tr>
								<? }else{?>
									<tr>
										<td valign="top" class="panel_heading">The Stock PlayBook</td>
										<td width="45%" valign="top">
											<div class="control_panel_cart">
											<table width="0%" align="right" border="0" cellpadding="0"  cellspacing="0">
											  <tr><?  echo getaAddtoCartbtnsCP('TheStockPlayBook','get_gree_trial_bmtp.gif','SUBSCRIPTION');?></tr>
											</table>
											</div>
										</td>
									</tr>
				<? } ?>
				</table>
				<div class="common_panel_manage_heading"><a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register">MANAGE SUBSCRIPTION SETTINGS</a></div>
			</div>
		</div>
<?php	}

	function controlPannelWatchlist($userid,$pageName,$cpclassinner){
	    global $watchlistlimit,$page_config,$lang,$HTPFXSSL;
		build_lang($pageName);
		$watchlistmsg=$lang['watch_list'];
		$countst=getWatchlistTickerCount($userid);
		$countticker=$countst[count];
?>
		<div class="common_list_panel">
		<div class="<?=$cpclassinner;?>">
		<div class="common_panel_header">Watch list</div>
		<div id="tickerlist">
		<?
		   if($countticker)
		   {
		   		watchlistdata($userid,$countticker);

		   }
		   else
		   {
		   		echo "<br>",$watchlistmsg;
		   }

		?>
		</div>
				<div class="common_panel_manage_heading"><a href="<?=$page_config['profile']['URL'];?>">MANAGE WATCH LIST</a></div>
			</div>
		</div>


<?php	}

  function watchlistdata($userid,$countticker){
  	global $watchlistlimit; //$watchlistlimit=6;
		$getticker=getWatchlistTicker($userid,$offset,$watchlistlimit);
		//$countst=getWatchlistTickerCount($userid);
		// $countticker=$countst[count];
  ?>
  		<!--<table align="left" class="panel_heading" width="100%" border="0" cellspacing="0" cellpadding="0">-->
		<?php
		if($countticker>0){
			$count = 1;
			foreach($getticker as $value){
				$arDivId[] = $divId = 'watchlistdata_'.$count;
				$arTicker[]=$value['stocksymbol'];
				?><div class="boxContentOptions"><div id="<?=$divId?>" class="watchlist-images"></div></div>
				<?php /*?><tr>
					<td valign="top" nowrap="nowrap"><div id="<?=$divId?>">
					</div></td>
				</tr><?php */?>
			<?php $count++;}
		} ?>
		<script>addLoadEventWatchList(loadStockQuote,'<?=implode(",",$arTicker)?>','<?=implode(",",$arDivId)?>')</script>
		<!--</table>-->
	<?
  }


   function watchlistPagination($userid,$countticker,$offset,$watchlistlimit){
		global $IMG_SERVER;
   			$offsetnext=($offset+$watchlistlimit);
			$offsetdot=0;
			$countdot=floor($countticker/$watchlistlimit);
?>
   	<div class="control_panel_nev">
	        <? if($offset=="0"){ ?>
			<img src="<?=$IMG_SERVER?>/images/redesign/control_back.jpg" width="9" height="11"/>
			<?} else{?>
			<input type="image" style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/redesign/control_back.jpg" width="9" height="11" onClick="getPaginationWatchlist('<?=$userid?>','<?=$offsets=($offset-$watchlistlimit);?>');" />
			<? } ?>
<!--			 <input type="image" style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/redesign/control_selected.jpg" width="9" height="11" onClick="getPaginationWatchlist();"/>-->
			  <? for($i=0;$i<=$countdot-1;$i++){
			  		$offsetdot=$offsetdot+$watchlistlimit;
			  ?>
			  	 <input type="image" style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/redesign/control_hide.jpg" width="9" height="11" onClick="getPaginationWatchlist('<?=$userid?>','<?=$offsetdot;?>');" />
			  <? } ?>
			  <? if($offsetnext>=$countticker){?>
					<img src="<?=$IMG_SERVER?>/images/redesign/control_next.jpg" width="9" height="11" />
			 <? } else { ?>
			 		<input type="image" style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/redesign/control_next.jpg" width="9" height="11" onClick="getPaginationWatchlist('<?=$userid?>','<?=$offsetnext;?>');" />
			 <? } ?>
		</div>
<?
   }

   	function yourAccount($hide){
		global $HTPFX,$HTHOST,$IMG_SERVER;
		if($hide){ ?>
			<a onclick="$$('div.d').each( function(e) { e.visualEffect('blind_down',{duration:0.5}) }); return true;"><img src="<?=$IMG_SERVER?>/images/redesign/expand.gif" vspace="0" border="0" onclick="getYourAccount(hide=0);"/></a>
	  <? } else { ?>

			<a  onclick="$$('div.d').each( function(e) { e.visualEffect('blind_up','d1',{duration: 0.5}) }); return true;"><img src="<?=$IMG_SERVER?>/images/redesign/collapse.gif"  border="0" onclick="getYourAccount(hide=1);"/></a>
	<?	}
	}

	function headerSearch(){
	global $IMG_SERVER,$HTPFX,$HTHOST;
	?>
		<form method="get" name="frmsearch" action="<?=$HTPFX.$HTHOST?>/library/search.htm?search=1&stock=1" style="padding:0px; margin:0px;">
			<table  border="0" cellspacing="0" cellpadding="0" style="padding-top:2px;">
			  <tr>
				<!--<td width="22%" align="right"><h6>search:</h6></td>-->
				<td width="40%" valign="top">
				<input  class="search_input_box_home" type="text" name="q" id="search" onBlur="blurred(this)" onFocus="focused(this)" value="Search Keywords or Symbols" />
				</td>
				<td width="38%"  valzign="top" align="left"><input type="image" hspace="2" vspace="3" src="<?=$HTPFX.$HTHOST?>/images/navigation/go-btn.gif" border="0" valign="absmiddle" onclick="searchtextboxClear(this);" /></td>
			  </tr>
			</table>
	   </form>


<?	}


  function articlePagination($numpages,$articleid,$articlekeyword,$articleblurb,$count){
  global $HTPFX,$HTHOST;
  $p=$count;
  	if($numpages>1){
			$pagination=array();
			foreach(range(0,$numpages-1) as $i){
				$cond=($p==$i?1:0);
				$url=makeArticleslink($articleid,$articlekeyword,$articleblurb,NULL,$i);
				$pagination[]=href($url,$i+1,0,$cond,"article");
			}
			$pagination=implode("  ",$pagination);
	   }

	   //echo $pagination;

		$prev_page = $count - 1;
 	?>
	<div class="nav_bar">
		<table  border="0" cellspacing="0" cellpadding="0">
		  <tr>
		  <? if($prev_page >= 0) {
		 		 $urlprev=makeArticleslink($articleid,$articlekeyword,$articleblurb,NULL,$prev_page);  ?>
				<td class="priv_page"><a href="<?=$HTPFX.$HTHOST.$urlprev;?>">&laquo; previous page</a></td>
			<? } ?>
			<td class="center_page">pages:<span class="center_page_middle"><?=$pagination;?></span></td>
			<?
			    $next_page = $count + 1;
				if($next_page < $numpages) {
		   			$urlnext=makeArticleslink($articleid,$articlekeyword,$articleblurb,NULL,$next_page); ?>
			<td class="next_page"><a href="<?=$HTPFX.$HTHOST.$urlnext;?>">next page &#187;</a></td>
			<? } ?>
		  </tr>
		</table>
	</div>
 <? }

	function displayArticleTags($type,$taginfo){
	global $HTPFX,$HTHOST;
		$index=0;
		if(is_array($taginfo))
		foreach($taginfo as $tagkey=> $tagvalue)
		{
		  $tag=strtoupper($tagvalue['tag']);
		  $links='<a href="'.$HTPFX.$HTHOST."/library/search.htm".'?q='.$tag.'&oid='.$type.'&stock=1';
		  $links.='"'.'>'.$tag.'</a>';
		  if($index==0){
			 echo $links;
		  }
		  else{
			 echo ", ".$links;
		  }
		 $index++;
		}
	}

	function displayVideoTags($type,$taginfo,$pagesearch='VIDEO'){
    global $HTPFX,$HTHOST;
		if(is_array($taginfo))
		foreach($taginfo as $tagkey=> $tagvalue)
		{
			$tag=strtoupper($tagvalue['tag']);
			$links='<a href="'.$HTPFX.$HTHOST."/library/search.htm".'?q='.$tag.'&oid='.$type.'&stock=1';
			$links.='"'.'>'.$tag.'</a>';
			$taglink[]=$links;
		}
		$link = @implode(", ",$taglink);
		return $link;
	}

	function displayslideshowTags($type,$taginfo){
	global $HTPFX,$HTHOST;
		$index=0;
		if(is_array($taginfo))
		foreach($taginfo as $tagkey=> $tagvalue)
		{
		  $tag=strtoupper($tagvalue['tag']);
		  $links='<a href="'.$HTPFX.$HTHOST."/library/search.htm".'?q='.$tag.'&oid='.$type.'&stock=1';
		  $links.='"'.'>'.$tag.'</a>';
		  if($index==0){
			 echo $links;
		  }
		  else{
			 echo ", ".$links;
		  }
		 $index++;
		}
	}

	function showComment($discussionarticle,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$showcomment,$imagevalue,$sid,$eid,$urlPost,$rand=NULL,$threadid=NULL)
	{
	   global $HTPFX,$HTHOST,$IMG_SERVER;
		$objThread = new Thread();
		$objaddrequest=new friends();
		$objLink = new Exchange_Element();
		//$threadArray = $objThread->get_thread_on_object($articleid,$from);
		$threadid=$discussionarticle->threadid;
		$objThread->thread_id=$threadid;
		$url =$HTPFX.$HTHOST.'/articles/Post.php?subscription_id='.$subscription_id;
		$url.='&thread_id='.$threadid;
		$url.='&comment_id='.$rand;
		if($imagevalue=="1")
		{
			$commentimage='plush.gif';
			$imagevalue=0;
		}
		else
		{
			$commentimage='Dash.gif';
			$imagevalue=1;
		}
		if($discussionarticle->appcommentcount>1)
			$txtComments="Comments";
		else
			$txtComments="Comment";
		?>
	<div class="comment_article_main">
		<div class="comment_article_header">
			<span id="post_comment_heading"><?=$txtComments;?> (<?=$discussionarticle->appcommentcount;?>)</span><span style="padding-left:20px; vertical-align:middle;">
            <?php
			       if($subscription_id){
					$exchange_prevquery="select ebs.subscription_id,ebs.value,ebs.blockservice_id,es.serviceid from
					ex_blockservices ebs,ex_services es	where ebs.blockservice_id=es.id
					and ebs.subscription_id='".$subscription_id."' and 	ebs.value='on'";
					$exchange_prevresult=exec_query($exchange_prevquery);
					}
					$serviceidarray='';
					if(count($exchange_prevresult)){
						//**** foreach(exec_query($exchange_prevquery) as $rowexchange_prevquery){
						foreach($exchange_prevresult as $rowexchange_prevquery)
						{
							if($serviceidarray=="")
							{
								$serviceidarray=$rowexchange_prevquery['serviceid'];
							}
							else
							{
								$serviceidarray.=",".$rowexchange_prevquery['serviceid'];
							}
						}
					}
					if(ereg("all_services",$serviceidarray))
					{
						$chk='true';
					}
					else if(ereg("comment_posts",$serviceidarray))
					{
						$chk='true';
					}
					if(!$subscription_id <> "")
					{
					 $url=$HTPFXSSL.$HTHOST."/subscription/register/iboxindex.htm";$linkId="navlink";$label='<img src="'.$IMG_SERVER.'/images/redesign/post_comment.gif" align="absmiddle" vspace="2" border="0" style="padding-top:0px;cursor:pointer;" />';echo iboxCall($linkId,$label,$url);
					}
					else {
							if($chk)
							{
							?>
							<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif"  border="0" onclick="Javascript:checkprevilages('<?=$rand?>','<?=$url?>','<?=$chk?>');" vspace="2" align="absmiddle" style="padding-top:0px;cursor:pointer;"/>
							<?
							}
							else
							{
							?>
								<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif" border="0" onclick="Javascript:preHttpRequest('<?=$rand?>','<?=$url?>');" align="absmiddle" vspace="2" style="padding-top:0px;cursor:pointer;"/>
							<?php
							}
					}
					?></span>
			<?
				$discussionattr['q']=$threadid;
				$label='<span id="post_comment_small_heading">See All Comments &raquo;</span>';
				$tageturl=$page_config['single_discussion']['URL']."?".'q='.$threadid;
				iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$tageturl);

		if($appcommentcount!=='0'){
			$urlPost=$HTPFX.$HTHOST."/articles/Post.php";
		?>
			<span id="post_comment_small_heading"><img class="articleCommentbar" src="<?=$IMG_SERVER?>/images/community_images/<?=$commentimage;?>" onclick="showCommentbox('<?=$appcommentcount;?>','<?=$articleid;?>','<?=$subscription_id;?>','<?=$pageName;?>','<?=$profile_exchange;?>','<?=$from;?>','<?=$imagevalue?>','<?=$sid?>','<?=$eid?>','<?=$urlPost;?>');"></span>
		<?  } ?>
		</div>
	</div>

		<?
		if($showcomment)
		{
			getPostedComments($discussionarticle,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid);
		}
		?>
		<?
	}// end show comment function

	function getPostedComments($discussionarticle,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid){
	    global $page_config,$HTPFX,$HTHOST,$IMG_SERVER;
		$objThread = new Thread();
		$objaddrequest=new friends();
		$objLink = new Exchange_Element();
		$threadid=$discussionarticle->threadid;
		$targeturl="/community/discussion/discussion/".$threadid;
	?>
	<!--Main comment box start from here-->
	<!-- <div class="comment_article_main"> -->
	<?
    $rand=rand();
	$url =$HTPFX.$HTHOST.'/articles/Post.php?subscription_id='.$subscription_id;
	$url.='&thread_id='.$threadid;
	$url.='&comment_id='.$rand;
	$urlmessage='Post.php?from_subscription_id='.$subscription_id;
	$urlmessage.='&message_id='.$rand;
    build_lang($pageName);
    global $lang;
	$count = "5";
	$content_type=$from;
	$comments =$discussionarticle->comments;
	$index=0;
	if(is_array($comments))
   		 if($subscription_id){
			$ReportAbuse=$objaddrequest->CheckReportAbuse($subscription_id);
		}
	foreach($comments as $postkey=> $postval)
	{
		$commentid = $postval['postid'];
		$date=$postval['date'];
		$datevalue = $objThread->check_date($date);
		$firstname=$postval['fname'];
		$urlmessage.='&to_subscription_id='.$postval['subid'];
		$commentposterName=ucwords(strtolower($postval['name']));
		$posterFname=ucwords(strtolower($firstname));

?>
	<div class="comment_outer_box">


<table width="100%" border="0" cellpadding="0" cellpadding="0" class="comment_article_mainTable">
  <tr>
    <td rowspan="2" width="20%" valign="top"  bgcolor="#ECEAEB">
		<div class="comment_name_box">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
			  <tr>
				<td class="comment_heading">
				<?php
					$profileattr['userid']=$postval['subid'];
					$discussionattr['q']=$threadid;
					$label=$commentposterName;
					$targeturl=$HTPFX.$HTHOST.'/community/profile/userid/'.$profileattr['userid'];
					iboxcheckArticle('profile',$profileattr,$label,$sid,$eid,$targeturl);
				?>
				<?php // echo $commentposterName?></a></td>
			  </tr>
			  <tr><td><div class="error" id="sendrequest<?=$postkey?>"></div></td></tr>
			  <tr>
				<td class="comment_sub_heading">
			  <?php
			  if($subscription_id){
			  $chkFriend=is_friend($postval['subid'],$subscription_id);
			  }
		   	  if($chkFriend==0){
			    if(!$eid){
					// FRIEND Request
				    $label=$lang['Add_Friends'];
					$returnibox = iboxCheckAddtofriends($label);
					echo $returnibox;
				?>
				<br />
				<?php
				} else {
				?>
				  <a style="cursor: pointer;" onClick="exchangeuser('<? echo $profile_exchange;  ?>',<? echo $articleid;?>,<? echo $postval['subid'];?>,'<? echo $lang['Request_sent'];?>', '<?php echo $postval['subid']; ?>','<?php echo $postval['postid']; ?>','<? echo $subscription_id?>','<?=$postkey?>')"><?php echo $lang['Add_Friends'];?></a><br />
				<?php
				}
			} ?>
			</td>
			</tr>
			<tr>
				<td class="comment_sub_heading">
				<?php
				if($profile_exchange){
					$sendMessagecheck=is_msg_allowed($postval['subid'],$subscription_id);
					if($sendMessagecheck=='true'){
				?>
					<a href="<?=$page_config['compose']['URL']; ?>?&from=<?=$from;?>&a=<?=$articleid; ?>&to=<?=$postval['subid'];?> ">Send <?php echo $posterFname;?> a message</a><br />
				<?php  }
				}
				?>
				</td>
			</tr>
			<tr>
				<td class="comment_sub_heading">
				<?php
				  $searchattr['author_id']=$postval['subid'];
				  //$objLink->checkiBox('search',$searchattr);
				  $discussionattr['q']=$threadid;
				  $label='View Exchanges';
				  $targeturl = $HTPFX.$HTHOST.'/community/search/'.$postval['subid'];
				  iboxcheckArticle('search',$searchattr,$label,$sid,$eid,$targeturl);
				?></a>
				</td>
			</tr>
			</table>
		</div>
			</td>
			<td rowspan="2" width="15" valign="top"></td>
       <td rowspan="2" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>

			<td colspan="2" align="right"><div class="report_abuse" id="sendabuse<?=$postkey?>"></div></td>
		  </tr>
		  <tr>
			<td class="comment_date" width="400px"><?php echo $datevalue; ?></td>
			<td class="comment_date" nowrap="nowrap">
				<?php
			    $ReportAbuse=array();
				$strAttribute="q=".$threadid;

				if($ReportAbuse==NULL){
				$ReportAbuse=array();
				}
				if(($subscription_id > 0) && ($postval['subid']!=$subscription_id) && (!in_array($commentid,$ReportAbuse)))
				{
				?>
				<span><a style='cursor: pointer; font-size:10px; color:#0486B7; font:Arial, Helvetica, sans-serif;' onclick="preHttpRequestURL('<?=$profile_exchange;?>','<?=$subscription_id?>','<?=$commentid?>','<?=$article[id];?>','<?=$postval[subid];?>','single_discussion', '<?=$strAttribute;?>','<?=$postkey?>')">Report Abuse</a></span>

				<?php
				}
				?>
			</td>
		  </tr>
		  <tr>
			<td valign="top"><?=$postval['teasure'];?>
				<br />
				<?php
					// Read More Links
					$discussionattr['cmt']=$commentid;
					//$objLink->checkiBox('single_discussion',$discussionattr);
					$targeturlread=$HTPFX.$HTHOST."/community/discussion/discussion/".$threadid;
					$discussionattr['q']=$threadid;
					$label='<span class="comment_read_more">Read More</span>';
					iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturlread);
					//echo $postval['name']."</a>";
				?>
				<br />
			</td>
		  </tr>

		</table>


	</td>
  </tr>

</table>




	</div>
	<? }
	?>
<!-- </div> -->
<!--Main comment box end here-->
  <?
  }

  function discussArticle($subscription_id,$url,$rand,$threadid,$from)
  {
  global $page_config;
  $sid=$_SESSION['SID'];
  $eid=$_SESSION['SID'];
  ?>
 		<!--Post article section start from here-->
<table width="650" border="0" align="left" cellpadding="5" cellspacing="0">
   <tr><td colspan="2" align="left" ><div id="<?=$rand?>" ></div></td></tr>
   <tr class="common_box">
    <td width="63%" height="35" align="left">
	<?
		// discuss this slideshow and more on the mv exchange
		if ($from=='mvtv'){$labelOn='video';}elseif ($from=='slideshow'){$labelOn='slideshow';}else{$labelOn='article';}
		$discussionattr['q']=$threadid;
		$label='<h5>discuss this '.$labelOn.' and more on the mv exchange</h5>';
		$page_config['single_discussion']['URL']."?".'q='.$threadid;
		$targeturl="/community/discussion/discussion/".$threadid;
		iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturl);
	?>
	</td>
	<?
	 if(!$_SESSION['EID']) {
	 ?>
	<td width="37%" align="left"><?php $url="$HTPFX$HTHOST/subscription/register/iboxindex.htm";$linkId="navlink"; $tageturl=$page_config['single_discussion']['URL']."?".'q='.$threadid; $label='<img src="'.$IMG_SERVER.'/images/redesign/sign_up_button.gif" width="192" height="19" border="0" />';echo iboxCall($linkId,$label,$url,$height=488,$width=532,$tageturl);?></td>
	<? } ?>
  </tr>
</table>
<!--Post article section end here-->

<?
  }

   function displayRssEmailAlertbox($targeturl=NULL){
   	global $page_config,$IMG_SERVER;
   ?>
   	<table width="95%" border="0" cellspacing="2" cellpadding="0" align="center" class="rssAlert">
	  <tr>
		<td><img src="<?=$IMG_SERVER?>/images/redesign/rss_logo.jpg" alt="rss" width="23" height="23" border="0" /></td>
		<td class="quick_links" align="left" ><a href="<?=$HTPFX.$HTHOST?>/rss">add rss feed</a></td>
		<td><img src="<?=$IMG_SERVER?>/images/redesign/article_alert_logo.jpg" border="0" alt="article alert" width="23" height="18" /></td>
			<td class="quick_links" align="left"><a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/controlPanel.htm">free article alerts</a></td>
	  </tr>
	</table>
   <?
   }


function showFinancialContent($unique_stocks,$count,$showRelArticle,$showAlso)
{
$ShowStck = showFeaturedStocks($unique_stocks,$count);
/*$showRelArticle = showRelatedArticle($tags,$articleid);
htmlprint_r($showRelArticle);
$showAlos = showAlosBy($articleid,$author,$authorid);
htmlprint_r($showAlos);*/

$tab_settingsMarket = array('stocks','relatedarticles','alsoby');
$tab_settingsMarket['stocks']['box'] = 'none';
$tab_settingsMarket['relatedarticles']['box'] = 'none';
$tab_settingsMarket['alsoby']['box'] = 'none';
$tab_settingsMarket['stocks']['tab'] = 'selected'; $tab_settingsMarket['stocks']['box'] = 'block';
$tab_settingsMarket['relatedarticles']['tab'] = 'selected'; $tab_settingsMarket['relatedarticles']['box'] = 'block';
$tab_settingsMarket['alsoby']['tab'] = 'selected'; $tab_settingsMarket['alsoby']['box'] = 'block';

$tabMarket = '';
$tabMarket .= "<li><a class=\"selected\" id=\"mark1\" onclick=\"showHidediv('marketinner','mark',1,4);\">STOCKS</a></li>";
$tabMarket .= "<li><a id=\"mark2\" onclick=\"showHidediv('marketinner','mark',2,4);\">RELATED ARTICLES</a></li>";
$tabMarket .= "<li><a id=\"mark3\" onclick=\"showHidediv('marketinner','mark',3,4);\" title='Also By This Professor'>ALSO BY...</a></li>";
print <<<HTML
<div class="market_inner_container">
<div class="market_inner">
<ul class="idTabs">
{$tabMarket}
</ul>
HTML;

print <<<HTML
<div style="display: block;" id="marketinner1" class='market_inner_div'>
<span>
HTML;
print <<<HTML
HTML;
// add FC js here
 $showstock=showFeaturedStocks($unique_stocks,$count);
 echo $showstock;
print <<<HTML
</span>
HTML;
searchStocksTicker();
print <<<HTML
</div>

HTML;


print <<<HTML
<div style="display: none;" id="marketinner2" class='market_inner_div_cont'>
HTML;

print <<<HTML
HTML;
// related articles
	$showrel=$showRelArticle;
	if(is_array($showrel)){
	foreach($showrel as $row) {
		echo $row;
	}
	}

print <<<HTML
</div>

HTML;
print <<<HTML
<div style="display: none;" id="marketinner3" class='market_inner_div_cont'>


HTML;
print <<<HTML
HTML;
// Add also by here
$showby=$showAlso;
if(is_array($showby)){
foreach($showby as $row) {
		echo $row;
	}
}
print <<<HTML
</div>
HTML;
print <<<HTML
</div>
</div>
HTML;
}

function showSkyscraperAd()
{
global $cm8_ads_WideSkyscraper_160x600;
$bannername1=$cm8_ads_WideSkyscraper_160x600;

print <<<HTML
<div class="market_inner_skyscraperad">
<div class="market_inner" style="width:160px;">
HTML;



print <<<HTML
<div style="display: block;" id="marketinner1" class='market_inner_div'>
<span>
HTML;


print <<<HTML
</span>
HTML;

print <<<HTML
</div>
HTML;

print '<div align="right" >';
CM8_ShowAd($bannername1);
print '</div>';

print <<<HTML
</div>
</div>
HTML;

}
	function showFeaturedStocks($unique_stocks,$count){
		global $IMG_SERVER,$HTPFX,$HTHOST;
		if(!$count){
			$count=count($unique_stocks);
		}

			//$countst=count($unique_stocks);
			if($count>0){
     		  $counter = 1;

              $str='<table class="market_heading" width="100%" border="0" cellspacing="0" cellpadding="0">';
                foreach($unique_stocks as $value){
					$arDivId[] = $divId = 'showFeaturedStocks_'.$counter;
                   $str.='<tr>';
	                    $str.='<td colspan="4"  ><div id="'.$divId.'">';
					//	$str.='<script src="http://finance.minyanville.com/minyanville?Module=stockquote&Ticker='.$value.'&Output=JS"></script></td>';
					$str.='</div></td><td align="right"><a href="'.$HTPFX.$HTHOST.'/articles/tradenow.htm
"><img border="0" src="'.$IMG_SERVER.'/images/redesign/trade_now_01_sm.jpg"/></a></td></tr>';
					//$str.='</div></td></tr>';
					$counter++;
				}
				$str.='<script>addLoadEventStocks(loadStockQuote,\''.implode(",",$unique_stocks).'\',\''.implode(",",$arDivId).'\')</script>';
                $str.='</table>';
			}
			return $str;
	}

	function searchStocksTicker(){
	global $cm8_ads_MicroBar;
	$bannername=$cm8_ads_MicroBar;
	?>
		<form method="post" name="stockFrm" action="<?=$HTPFX?><?=$HTHOST?>/library/stockSearch.htm?search=stock" style="padding:0px; margin:0px;">
		<div class="market_search">
        <table width="0%" border="0" align="center"  cellspacing="5" cellpadding="0">
		  <tr>
			<td width="15%"><h6>SEARCH:</h6></td>
			<td width="15%"><input class="search_input_box_market" type="text" name="qs" id="search" value="Symbols" size="22"  onBlur="blurred(this)" onFocus="focused(this)"/>
			</td>
			<td width="70%" align="center"><input type="image" src="<?=$IMG_SERVER?>/images/redesign/go.jpg" border="0" align="absmiddle" onclick="searchtextboxClear(this);" /></td>
			<td><? CM8_ShowAd($bannername); ?></td>
		  </tr>
		</table>
        </div>
		</form>
		 <?
		}

	function showRelatedArticle($articleid){
	    $result=getRelatedArticles($articleid);
	    $str="";
		if (count($result) > 1) {
		 	 foreach($result as $row) {
				$str.='<li><a href='.makeArticleslink($row['id'],$row['keyword'],$row['blurb']).'>'.mswordReplaceSpecialChars($row['title']).'</a></li>';
			}
		 }
		return $str;
	}

	function showAlsoBy($articleid,$author,$authorid) {
		$results=getshowAlsoBy($articleid,$author,$authorid);
		$str="";
		if (count($results) > 0) {
			 foreach($results as $row) {
				$str.='<li><a href='.makeArticleslink($row['id']).'>'.mswordReplaceSpecialChars($row['title']).'</a></li>';
				 }
		 }
          return $str;
	}

	function showTalkBubbleArticle($character_Text) {
	?>
		<div class="bubble_container">

		<div class="bubble_pointer">
		</div>

        <table width="0%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td width="1%">&nbsp;</td>
    <td width="12" height="18" align="left" ><img src="<?=$IMG_SERVER?>/images/redesign/bubble_top_left.gif" width="12" height="20" /></td>
    <td width="93%"  class="buuble_top_middle"></td>
    <td width="12" height="18" align="right"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_top_right.gif" width="12" height="20" /></td>
  </tr>

  <tr>
    <td align="left" valign="top"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_arrow.gif" alt="" width="17" height="20" vspace="3" /></td>
    <td valign="top" class="buuble_left_middle"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_left_white.gif" /></td>
    <td class="bubble_text_main"><?=$character_Text;?><br/></td>
    <td valign="top" class="buuble_right_middle"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_right_white.gif"  /></td>  </tr>

  <tr>
    <td>&nbsp;</td>
    <td width="12" height="18" align="left" valign="bottom" class="buuble_left_middle"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_bottom_left.gif" width="12" height="18" /></td>
    <td height="18"  class="buuble_bottom_middle"></td>
    <td width="12" height="18" align="right" valign="bottom" class="buuble_right_middle"><img src="<?=$IMG_SERVER?>/images/redesign/bubble_bottom_right.gif" width="12" height="18" /></td>
  </tr>
</table>

	<?
	}



function quickLink ($linkHref, $desc, $accessKey, $linkTitle,$divid) {
   $theLink = "<td width='5' style='font-size:10px;'><a style=\"cursor:hand;cursor:pointer;font-size:10px;\" onclick=\"Javascript:getPhotos('".$divid."','".$linkHref."');\" title=\"". $desc ."\" accesskey=\"".
                   $accessKey ."\" >". $linkTitle ."</a></td>";
   return $theLink;

}
// google_like_pagination.php
function pagination($number, $show, $showing, $firstlink, $baselink, $seperator,$divid,$heighest,$max) {
	
	global $IMG_SERVER;
	$noofpage=ceil($number/$max);
	$number=$noofpage;
if($show<$heighest)
{
	$low=1;
	$high=$show;
}
else
{
	$disp = floor($show / 2);
	if ( $showing <= $disp)
	{
	$low=1;

	$high = ($low + $show) - 1;
	}
	elseif ( ($showing + $disp) > $number)
	{

	$high = $number;
	$low = ($number - $show) + 1;
	}

	else
	{

	$low = ($showing - $disp);
	$high = ($showing + $disp);
	}

}


    if ( ($showing - 1) > 0 ) :
        if ( ($showing - 1) == 1 ):
        $prev  = quickLink ($firstlink, 'Previous', '', '<img src="'.$IMG_SERVER.'/images/redesign/previous_slide_button.gif" border=0 />',$divid);
        else:
        $prev  = quickLink ($baselink . $seperator . ($showing - 1),
        'Previous', 'z', '<img src="'.$IMG_SERVER.'/images/redesign/previous_slide_button.gif" border=0 />',$divid);
        endif;
    else:
        $prev  = '<td ><img src="'.$IMG_SERVER.'/images/redesign/previous_slide_button.gif" border=0 /></td>';
    endif;


	$next  = ($showing + 1) <= $number ?
    quickLink ($baselink . $seperator . ($showing + 1),'Next', 'x', '<img src='.$IMG_SERVER.'/images/redesign/forward_slide_button.gif  border=0 />',$divid) : '<td valign=top><img src='.$IMG_SERVER.'/images/redesign/forward_slide_button.gif border=0 /></td>';

    $navi = '';
    // start the navi

        $navi .=  $prev ;


    foreach (range($low, $high) as $newnumber)
	{

        $link = ( $newnumber == 1 ) ? $firstlink : $baselink . $seperator . $newnumber;
        if ($newnumber > $number)
		{
			$navi .= '';
		}
        elseif ($newnumber == 0)
		{
			$navi .= '';
		}
        else
		{

			if( $newnumber == $showing )
			{
				$navi .="<td style='font-size:10px; color:red;'>".$newnumber."</td>" ;
			}


			else
			{
				//echo "<br>".
				$newlink="$newnumber";
				$navi .=quickLink ($link, 'Page '. $newnumber, '', $newlink,$divid) ;
			}
		}

	}


        $navi .= $next ;



    return $navi;

}
//******************************************************
function make_ajax_pagination_video($divid,$link,$count,$MAX,$numrows1)
{
	/*$strPage="<div class='sliding_combo_controller'><table width='0%' cellspacing='0' cellpadding='4' border='0' align='center'><tr>";
	$lastrowtfollowme=$MAX+$count;
	if($count > 0)
	{
		$prevpagetfollowme=$count - $MAX;
		$url=$link."&count=$prevpagetfollowme";
		$strPage.="<td  ><a onclick=\"Javascript:getPhotos('$divid','$url');\" style='cursor:pointer;' ><img src=$IMG_SERVER/images/redesign/previous_slide_button.gif border=0 /></a></td>";
	}
	else
	{
		$strPage.="<td  ><img src=$IMG_SERVER/images/redesign/previous_slide_button.gif border=0 /></td>";
	}

	for($pagelooptfollowme=1;$pagelooptfollowme<=ceil($numrows1/$MAX);$pagelooptfollowme++)
	{
		$pagedatatfollowme=($pagelooptfollowme-1)*$MAX;
		if($count!=$pagedatatfollowme)
		{
			$url=$link."&count=$pagedatatfollowme";
			$strPage.="<td width='5'><a onclick=\"Javascript:getPhotos('$divid','$url');\" style='cursor:pointer;'><img src=\"$IMG_SERVER/images/redesign/hide_slide_button.gif\" border=\"0\" /></a></td>";
		}
		else
		{
			$strPage.="<td ><img src=\"$IMG_SERVER/images/redesign/selected_slide_button.gif\" border=\"0\"/></td>";
		}
	}
	if($numrows1 > $lastrowtfollowme)
	{
			$url=$link."&count=$lastrowtfollowme";
			$strPage.="<td  ><a onclick=\"Javascript:getPhotos('$divid','$url');\" style='cursor:pointer;' ><img src=$IMG_SERVER/images/redesign/forward_slide_button.gif  border=0 /></a></td> ";
	}
	else
	{
			$strPage.="<td ><img src=$IMG_SERVER/images/redesign/forward_slide_button.gif border=0 /></td>";
	}
	$strPage.="</tr></table></div>";*/

	if(ceil($numrows1/$MAX)<20)
	{
		$show=ceil($numrows1/$MAX);
	}
	else
	{
		$show=20;
	}

	$seperator = '&count=';
	$strPage="<div class='sliding_combo_controller'><table width='0%' cellspacing='0' cellpadding='4'  border='0' align='center'><tr>";
	$strPage.=pagination($numrows1, $show, $count, $link, $link, $seperator,$divid,20,$MAX);
	$strPage.="</tr></table></div>";

return $strPage;

}//end of function function make_ajax_pagination_video()

function setRating($videoid,$rating_type,$voter_id,$object_id)
{
	$arData['item_id'] = $videoid;
	$arData['voter_id'] = $voter_id;
	$arData['object_id'] = $object_id;
	$arData['rating_type'] = $rating_type;

	$arUpdateCondition['item_id'] = $videoid;
	$arUpdateCondition['voter_id'] = $voter_id;

	insert_or_update("ex_item_rating",$arData,$arUpdateCondition);
}

function chkRating($videoid,$voter_id,$object_id)
{
	$sql_chk_vote="select voter_id,rating_type from ex_item_rating where item_id='$videoid' and voter_id='$voter_id' and object_id='".$object_id."'";
	$res_votes=num_rows($sql_chk_vote);
	return $res_votes;
}


function numVotes($videoid,$object_id)
{
	$sql_vote_count="select id,voter_id from ex_item_rating where item_id='$videoid' and object_id='".$object_id."'";
	$num_votes=num_rows($sql_vote_count);
	return $num_votes;
}

function getRating($id,$object_id)
{
	$stQuery = "SELECT (SELECT COUNT(voter_id) FROM ex_item_rating WHERE item_id = $id AND rating_type = '1' and object_id='".$object_id."') AS good_rating,
				(SELECT COUNT(voter_id) FROM ex_item_rating WHERE item_id = $id AND rating_type = '0' and object_id='".$object_id."') AS bad_rating";
	return exec_query($stQuery,1);
}

function voteCast($videoid,$rating_type,$voter_id,$object_id,$object_name)
{
       global $IMG_SERVER;  
	if($rating_type != NULL && $voter_id != NULL)
	{
		setRating($videoid,$rating_type,$voter_id,$object_id);
	}

	$arrayRating=getRating($videoid,$object_id);
	$goodRating=$arrayRating['good_rating'];
	$badRating=$arrayRating['bad_rating'];
	$total=$goodRating+$badRating;
	$rateResult='<table border="0" cellsapcing="0" cellpadding="0"><tr><td colspan="4">';

	if($badRating > $goodRating)
	{
		$rateResult.='<span class=Feedvotes>'.$badRating." of ".$total." (".round(($badRating*100)/$total)."%) didn't find this helpful</span>";
	}
	else if ($badRating < $goodRating)
	{
		$rateResult .= '<span class=Feedvotes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
	}
	else if ($badRating == $goodRating && $total > 0)
	{
		$rateResult .= '<span class=Feedvotes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
	}
	$rateResult .='</td></tr><tr><td>';

	$totalRating = chkRating($videoid,$voter_id,$object_id);
	$num_votes = numVotes($videoid,$object_id);
	if ($num_votes==1){$votes=' Vote';}else{$votes=' Votes';}

	if($totalRating < 1)
	{


		$finalResult = $rateResult.'<span class="ratethis">Rate this '.$object_name.':&nbsp;</span></td>
		<td><a  onclick="javascript:voteR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" width="31" height="30" border="0" align="absmiddle"/></a></td>
		<td><a  onclick="javascript:voteR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a></span></td>
		<td><span id="vote_rating_edit'.$videoid.'">';

		$finalResult.='<span class="Feedvotes">('.$num_votes.' '.$votes.')</span></td></tr></table>';
	}

	if($totalRating>0)
	{

		echo $rateResult.'
		<table width="245" align="left"  cellspacing="0" cellpadding="0">
		<tr id="vote_rating'.$videoid.'" style="display:none;">
		<td class="Ratefeed">Rate this '.$object_name.'</td>
		<td><a  onclick="javascript:voteR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" hspace="5"  width="31" height="30" border="0" align="absmiddle"/></a>
		<a  onclick="javascript:voteR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a>
		</td>
		<td class="votes">( '.$num_votes.''.$votes.')</td>
		</tr>
		</table>
		<table width=140 cellspacing=0 cellpadding=3 border=0>
		<tr id="vote_rating_edit'.$videoid.'">
		<td><a style="cursor:pointer;" onClick="javascript:editVote('.$videoid.');">Change Vote:</a></td>
		<td class="votes">('.$num_votes.''.$votes.')</td>
		</tr>
		</table>';

		//return $finalResult;
	}
	else
	{
		return $finalResult;
	}

}

function voteCastAV($videoid,$rating_type,$voter_id,$object_id,$object_name)
{

	if($rating_type != NULL && $voter_id != NULL)
	{
		setRating($videoid,$rating_type,$voter_id,$object_id);
	}

	$arrayRating=getRating($videoid,$object_id);
	$goodRating=$arrayRating['good_rating'];
	$badRating=$arrayRating['bad_rating'];
	$total=$goodRating+$badRating;


	if($badRating > $goodRating)
	{
		$rateResult='<span class=votes>'.$badRating." of ".$total." (".round(($badRating*100)/$total)."%) didn't find this helpful</span>";
	}
	else if ($badRating < $goodRating)
	{
		$rateResult = '<span class=votes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
	}
	else if ($badRating == $goodRating && $total > 0)
	{
		$rateResult = '<span class=votes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
	}


	$totalRating = chkRating($videoid,$voter_id,$object_id);
	$num_votes = numVotes($videoid,$object_id);
	if ($num_votes==1){$votes=' Vote';}else{$votes=' Votes';}

	if($totalRating < 1)
	{

		$finalResult = $rateResult.'<br><span >Rate this '.$object_name.':&nbsp;</span><br><a  onclick="javascript:voteR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" width="31" height="30" border="0" align="absmiddle"/></a><a  onclick="javascript:voteR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" >
		<img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a></span>
		<span id="vote_rating_edit'.$videoid.'" >';

		$finalResult.='
		<span class="votes">('.$num_votes.' '.$votes.')</span>';
	}

	if($totalRating>0)
	{

		echo $rateResult.'<br><span id="vote_rating'.$videoid.'" style="display:none;"><span>Rate this '.$object_name.'</span><br><a  onclick="javascript:voteR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" width="31" height="30" border="0" align="absmiddle"/></a>
		<a  onclick="javascript:voteR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a><font class="votes">( '.$num_votes.''.$votes.')</font></span><span id="vote_rating_edit'.$videoid.'"><a style="cursor:pointer;" onClick="javascript:editVote('.$videoid.');">Change Vote:</a><span class="votes">('.$num_votes.''.$votes.')</span>';

		//return $finalResult;
	}
	else
	{
		return $finalResult;
	}

}

function voteCastDailyFeed($videoid,$rating_type,$voter_id,$object_id,$object_name)
{

	if($rating_type != NULL && $voter_id != NULL)
		{
			setRating($videoid,$rating_type,$voter_id,$object_id);
		}

		$arrayRating=getRating($videoid,$object_id);
		$goodRating=$arrayRating['good_rating'];
		$badRating=$arrayRating['bad_rating'];
		$total=$goodRating+$badRating;


		if($badRating > $goodRating)
		{
			$rateResult='<span id="voteid'.$videoid.'" class=Feedvotes>'.$badRating." of ".$total." (".round(($badRating*100)/$total)."%) didn't find this helpful</span>";
		}
		else if ($badRating < $goodRating)
		{
			$rateResult = '<span id="voteid'.$videoid.'" class=Feedvotes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
		}
		else if ($badRating == $goodRating && $total > 0)
		{
			$rateResult = '<span id="voteid'.$videoid.'" class=Feedvotes>'.$goodRating." of ".$total." (".round(($goodRating*100)/$total)."%) found this helpful</span>";
		}

		$totalRating = chkRating($videoid,$voter_id,$object_id);
		$num_votes = numVotes($videoid,$object_id);
		if ($num_votes==1){$votes=' Vote';}else{$votes=' Votes';}

		if($totalRating < 1)
			{


				$finalResult = $rateResult.'
				<table align="left" width="245" cellspacing="0" cellpadding="0">
		        <tr><td class="Ratefeed">
				Rate this '.$object_name.':&nbsp;
				</td><td>
				<a  onclick="javascript:votefeedR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" width="31" height="30" border="0" align="absmiddle"/></a>
				</td><td>
				<a  onclick="javascript:votefeedR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" >
				<img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a></td>
				<td>
				<span id="vote_rating_edit'.$videoid.'" >';

				$finalResult.='
				<span class="votes">('.$num_votes.' '.$votes.')</span></td></tr></table>';
			}

			if($totalRating>0)
			{

			echo $rateResult.'<span id="vote_rating'.$videoid.'" style="display:none;">
								<table width=245 align="left"  cellspacing=0 cellpadding=0 border=0><tr>
								<td class="Ratefeed">Rate this '.$object_name.'</td>
								<td>	<a  onclick="javascript:votefeedR('.$videoid.',0,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_down.gif" hspace="5" width="31" height="30" border="0" align="absmiddle"/></a>
								<a  onclick="javascript:votefeedR('.$videoid.',1,\''.$voter_id.'\','.$object_id.',\''.$object_name.'\');" ><img src="'.$IMG_SERVER.'/images/redesign/vote_up.gif" width="31" height="30" border="0" align="absmiddle"/></a>
						</td>
								<td class="votes">( '.$num_votes.''.$votes.')</td>
								</tr></table>
							</span>
							<span id="vote_rating_edit'.$videoid.'">
								<table width=140 cellspacing=0 cellpadding=3 border=0><tr>
								<td><a style="cursor:pointer;" onClick="javascript:editVote('.$videoid.');">Change Vote:</a></td>
								<td  class="votes">('.$num_votes.''.$votes.')</td>
								</tr></table>
							 </span>';

				//return $finalResult;
			}
			else
			{
				return $finalResult;
	}
}

function getVotingStatus($voter_id,$videoid)
{
	$sql="select item_id from ex_item_rating where item_id='$videoid' and voter_id='$voter_id'";
	$result=exec_query($sql,1);
	if(count($result)>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function showCommentAV($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$showcomment,$imagevalue,$sid,$eid,$urlPost)
	{
	global $HTPFX,$HTHOST;
		$objThread = new Thread();
		$objaddrequest=new friends();
		$objLink = new Exchange_Element();
		$threadArray = $objThread->get_thread_on_object($articleid,$from);
		$threadid=$threadArray['id'];
		$objThread->thread_id=$threadid;
		if($imagevalue=="1")
		{
			$commentimage='plush.gif';
			$imagevalue=0;
		}
		else
		{
			$commentimage='Dash.gif';
			$imagevalue=1;
		}

		$commentsCount = $objThread->get_all_comments($objThread,$content_type='articles', 'teasure', 0, '',$user,$shift=0);
		$appcommentcount=count($commentsCount);
		if ($appcommentcount>1){$commentLabel='comments('.$appcommentcount.")";}else{$commentLabel='comment ('.$appcommentcount.")";}
		?>
	<div class="comment_article_main">
		<div class="comment_article_header">
			<span id="post_comment_heading"><?=$commentLabel?></span>
			<?
				$discussionattr['q']=$threadid;
				$label='<span id="post_comment_small_heading">See All Comments &raquo;</span>';
				$targeturl="/community/discussion/".$threadid;
				iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturl);

		if($appcommentcount!=='0'){
			$urlPost=$HTPFX.$HTHOST."/articles/Postav.php";
			if ($subscription_id!=""){

		?>
			<span id="post_comment_small_heading"><img style="cursor:pointer; padding-left:380px;" src="<?=$IMG_SERVER?>/images/community_images/<?=$commentimage;?>" onclick="showCommentbox('<?=$appcommentcount;?>','<?=$articleid;?>','<?=$subscription_id;?>','<?=$pageName;?>','<?=$profile_exchange;?>','<?=$from;?>','<?=$imagevalue?>','<?=$sid?>','<?=$eid?>','<?=$urlPost;?>');"></span>
		<?
			}
			else
			{
		?>
				<span id="post_comment_small_heading"><img style="cursor:pointer; padding-left:380px;" src="<?=$IMG_SERVER?>/images/community_images/<?=$commentimage;?>"></span>
		<?
			}
		} ?>
		</div>
	</div>

		<?

		if($showcomment)
		{
			getPostedCommentsAV($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid);
		}
		?>
		<?
	}// end show comment function

	function getPostedCommentsAV($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid){
	    global $page_config;
		$objThread = new Thread();
		$objaddrequest=new friends();
		$objLink = new Exchange_Element();
		$threadArray = $objThread->get_thread_on_object($articleid,$from);
		$threadid=$threadArray['id'];
		$objThread->thread_id=$threadid;

	?>
	<!--Main comment box start from here-->
	<!-- <div class="comment_article_mainTest"> -->
	<?
    $rand=rand();
	$url =$HTPFX.$HTHOST.'/articles/Postav.php?subscription_id='.$subscription_id;
	$url.='&thread_id='.$threadid;
	$url.='&comment_id='.$rand;
	$urlmessage='Postav.php?from_subscription_id='.$subscription_id;
	$urlmessage.='&message_id='.$rand;
    build_lang($pageName);
    global $lang;
	$count = "5";
	$content_type=$from;
	$comments = $objThread->get_all_comments($objThread,$content_type='articles', 'teasure', 0, $count,$user,$shift=0);

	$index=0;
	if(is_array($comments))
	foreach($comments as $postkey=> $postval)
	{
		$commentid = $postval['postid'];
		$date=$postval['date'];
		$datevalue = $objThread->check_date($date);
		$firstname=$objThread->get_username($postval['subid']);
		$urlmessage.='&to_subscription_id='.$postval['subid'];
		$commentposterName=ucwords(strtolower($postval['name']));
		$posterFname=ucwords(strtolower($firstname[0][fname]));

?>
	<div class="comment_outer_box">


<table width="100%" border="0" cellpadding="0" cellpadding="0" class="comment_article_mainTable">
  <tr>
    <td rowspan="2" width="20%" valign="top"  bgcolor="#ECEAEB">
		<div class="comment_name_box">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
			  <tr>
				<td class="comment_heading">
				<?php
					$profileattr['userid']=$postval['subid'];
					$discussionattr['q']=$threadid;
					$label=$commentposterName;
					$targeturl="/community/profile/userid/".$postval['subid'];
					iboxcheckArticle('profile',$profileattr,$label,$sid,$eid,$targeturl);
				?>
				<?php // echo $commentposterName?></a></td>
			  </tr>
			  <tr><td><div style="color:#FF0000" id="sendrequest<?=$postkey?>"></div></td></tr>
			  <tr>
				<td class="comment_sub_heading">
			  <?php

			  $chkFriend=is_friend($postval['subid'],$subscription_id);
		   	  if($chkFriend==0){
			    if(!$eid){
				    $label=$lang['AddFriend'];
					$returnibox = iboxCheckAddtofriends($label);
					echo $returnibox;
				?>
				<br />
				<?php
				} else {
				?>
				  <a style="cursor: pointer;" onClick="exchangeuser('<? echo $profile_exchange;  ?>',<? echo $articleid;?>,<? echo $postval['subid'];?>,'<? echo $lang['Request_sent'];?>', '<?php echo $postval['subid']; ?>','<?php echo $postval['postid']; ?>','<? echo $subscription_id?>','<?=$postkey?>')"><?php echo $lang['AddFriend'];?></a><br />
				<?php
				}
			} ?>
			</td>
			</tr>
			<tr>
				<td class="comment_sub_heading">
				<?php
				if($profile_exchange){
					$sendMessagecheck=is_msg_allowed($postval['subid'],$subscription_id);
					if($sendMessagecheck=='true'){

					 $sendattr['from']='mvtv';
					 $sendattr['a']=$articleid;
					  $sendattr['to']=$postval['subid'];
				  //$objLink->checkiBox('search',$searchattr);
					  $discussionattr['q']=$threadid;
					  $label='Send '.$posterFname.' a Message';
					  $targeturl="/community/sendmessage/".$articleid."/".$postval['subid'];
					  iboxcheckArticle('compose',$sendattr,$label,$sid,$eid,$targeturl);
				?>
					<!-- <a href="<?=$page_config['compose']['URL']; ?>?&from=<?=$from;?>&a=<?=$articleid; ?>&to=<?=$postval['subid'];?> ">Send <?php echo $posterFname;?> a message</a><br /> -->
				<?php  }
				}
				?>
				</td>
			</tr>
			<tr>
				<td class="comment_sub_heading">
				<?php
				  $searchattr['userid']=$postval['subid'];
				  $searchattr['type']=4;
				  //$objLink->checkiBox('search',$searchattr);
				  $discussionattr['q']=$threadid;
				  $label='View Exchanges';
				  $targeturl="/community/search/".$postval['subid'];
				  iboxcheckArticle('search',$searchattr,$label,$sid,$eid,$targeturl);
				?></a>
				</td>
			</tr>
			</table>
		   </div>
			</td>
			<td rowspan="2" width="15" valign="top"></td>
       <td rowspan="2" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		<td colspan="2" align="right"><div class="report_abuse" align="right" id="sendabuse<?=$postkey?>"></div></td>
		  </tr>
		  <tr>
			<td class="comment_date" width="400px"><?php echo $datevalue; ?></td>
			<td class="comment_date" nowrap="nowrap">
				<?php
			    $ReportAbuse=array();
				$ReportAbuse=$objaddrequest->CheckReportAbuse($subscription_id);
				$strAttribute="q=".$threadid;

				if($ReportAbuse==NULL){
				$ReportAbuse=array();
				}
				if(($subscription_id > 0) && ($postval['subid']!=$subscription_id) && (!in_array($commentid,$ReportAbuse)))
				{
				?>
				<span><a style='cursor: pointer; font-size:10px; color:#0486B7; font:Arial, Helvetica, sans-serif;' onclick="preHttpRequestURL('<?=$profile_exchange;?>','<?=$subscription_id?>','<?=$commentid?>','<?=$article[id];?>','<?=$postval[subid];?>','single_discussion', '<?=$strAttribute;?>','<?=$postkey?>')">Report Abuse</a></span>

				<?php
				}
				?>
			</td>
		  </tr>
		  <tr>
			<td valign="top"><?=$postval['teasure'];?>
				<br />
				<?php
					// Read More
					$discussionattr['cmt']=$commentid;
					//$objLink->checkiBox('single_discussion',$discussionattr);
					$discussionattr['q']=$threadid;
					$targeturl=$HTPFX.$HTHOST."/community/discussion/discussion/".$threadid;
					$label='<span class="comment_read_more">Read More</span>';
					iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturl);

					//echo $postval['name']."</a>";
				?>
				<br />
			</td>
		  </tr>

		</table>


	</td>
  </tr>
</table>
	</div>
	<? $index++;}
	?>
<!-- </div> -->
<!--Main comment box end here-->
  <?
  }

  function discussArticleAV($subscription_id,$url,$rand,$threadid,$from)
  {
  global $page_config,$HTPFX,$HTHOST;
  $sid=$_SESSION['SID'];
  $eid=$_SESSION['SID'];
  ?>
<!--Post article section start from here-->
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
  <tr>
    <td align="left" valign="bottom" style="cursor: pointer;">
					<?php
					$exchange_prevquery="select ebs.subscription_id,ebs.value,ebs.blockservice_id,es.serviceid from
					ex_blockservices ebs,ex_services es	where ebs.blockservice_id=es.id
					and ebs.subscription_id='".$subscription_id."' and 	ebs.value='on'";
					// echo "<br>",$exchange_prevquery;
					$exchange_prevresult=exec_query($exchange_prevquery);
					$serviceidarray='';
					if(count($exchange_prevresult)){
						//****foreach(exec_query($exchange_prevquery) as $rowexchange_prevquery){
						foreach($exchange_prevresult as $rowexchange_prevquery)
						{
							if($serviceidarray=="")
							{
								$serviceidarray=$rowexchange_prevquery['serviceid'];
							}
							else
							{
								$serviceidarray.=",".$rowexchange_prevquery['serviceid'];
							}
						}
					}
					if(ereg("all_services",$serviceidarray))
					{
						$chk='true';
					}
					else if(ereg("comment_posts",$serviceidarray))
					{
						$chk='true';
					}

					if(!$subscription_id <> "")
					{

					 $url=$HTPFXSSL.$HTHOST."/subscription/register/login.htm";
					 $label='<a href="'.$url.'"><img src="'.$IMG_SERVER.'/images/redesign/post_comment.gif" align="absmiddle" border="0" style="padding-top:0px;pointer:cursor;" /></a>';
					 echo $label;
					}
					else {
							if($chk)
							{
							?>
							<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif"  border="0" onclick="Javascript:checkprevilages('<?=$rand?>','<?=$url?>','<?=$chk?>');" align="baseline" style="padding-top:0px;pointer:cursor;"/>
							<?
							}
							else
							{
							?>
								<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif" border="0" onclick="Javascript:preHttpRequest('<?=$rand?>','<?=$url?>');" align="baseline" style="padding-top:0px;pointer:cursor;"/>
							<?php
							}
					}
					?>
	</td>
   </tr>
   <tr><td colspan="2" align="left" ><div id="<?=$rand?>" ></div></td></tr>
   <tr class="common_box">
    <td width="63%" height="35" align="left">
	<?
		if ($from=='mvtv'){$labelOn='video';}else{$labelOn='article';}
		$discussionattr['q']=$threadid;
		 $targeturl="/community/discussion/discussion/".$threadid;
		$label='<h5><a href="'.$targeturl.'">discuss this '.$labelOn.' and more on the mv exchange</a></h5>';

		// iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturl);
		echo $label;
	?>
	</td>
	<?
	 if(!$_SESSION['EID']) {
	  $targeturl=""
	 ?>
	<td width="37%" align="left"> <a href="<?=$HTPFXSSL.$HTHOST?>/subscription/register/login.htm" ><img src=<?=$IMG_SERVER?>"/images/redesign/sign_up_button.gif" width="192" height="19" border="0" /></a></td>
	<? } ?>
  </tr>
</table>
<!--Post article section end here-->

<?
  }

function showPartnerCenterArticle()
{
global $cm8_ads_Button_120x90_pos1,$cm8_ads_Button_120x90_pos2,$cm8_ads_Button_120x90_pos3,$cm8_ads_Button_120x90_pos4,$cm8_ads_Button_120x90_pos5,$cm8_ads_Button_120x90_pos6;
?>
<table cellpadding="5" width="0%" cellspacing="5" align="left">
<tr>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos1);?></td>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos2);?></td>
</tr>
</table>
<?
}


function showPartnerCenter()
{
global $cm8_ads_Button_120x90_pos1,$cm8_ads_Button_120x90_pos2,$cm8_ads_Button_120x90_pos3,$cm8_ads_Button_120x90_pos4,$cm8_ads_Button_120x90_pos5,$cm8_ads_Button_120x90_pos6,$IMG_SERVER;
?>
<div class="right_common_head_home"><h2><img align="left" src="<?=$IMG_SERVER?>/images/home_redesign/mv_sidebar_title_ourpartners.png" alt="OUR PARTNERS"></h2>
<?=trade_now_right();?></div>
<div style="border:1px; border-bottom-color:#00000; border-style:solid;">
<table cellpadding="10px" cellspacing="5px" border="0">
<tr>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos1); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos2); ?></td>
</tr>
<tr>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos3); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos4); ?></td>
</tr>
<tr>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos5); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos6); ?></td>
</tr>
<tr>
<td colspan="2"><a class="brokerage_center" href="http://www.minyanville.com/articles/tradenow.htm">visit our <font style="color:red">new</font> brokerage center</a></td>
</tr>
</table></div>
<?
}

function showPartnerCenterHome()
{
global $cm8_ads_Button_120x90_pos1,$cm8_ads_Button_120x90_pos2,$cm8_ads_Button_120x90_pos3,$cm8_ads_Button_120x90_pos4,$cm8_ads_Button_120x90_pos5,$cm8_ads_Button_120x90_pos6;
?>
<table cellpadding="0" width="0%" cellspacing="0" align="left">
<tr>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos1);?></td>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos2);?></td>
</tr>
<tr>
<td colspan="2" height="26"><!--Empty td to provide space--></td>
</tr>
<tr>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos3);?></td>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos4);?></td>
</tr>
<tr>
<td colspan="2" height="26"><!--Empty td to provide space--></td>
</tr>
<tr>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos5);?></td>
<td><?=CM8_ShowAd($cm8_ads_Button_120x90_pos6);?></td>
</tr>

</table>
<?
}


function section_showPartnerCenter()
{
global $cm8_ads_Button_120x90_pos1,$cm8_ads_Button_120x90_pos2,$cm8_ads_Button_120x90_pos3,$cm8_ads_Button_120x90_pos4,$cm8_ads_Button_120x90_pos5,$cm8_ads_Button_120x90_pos6;
?>
<div class="section_partner_center_head"><h2>our partners</h2></div>
<div style="border:0px; border-bottom-color:#00000; border-style:solid;">
<table align=center cellpadding="10px" cellspacing="5px" border="0">
<tr>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos1); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos2); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos3); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos4); ?></td>
</tr>
<tr>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos5); ?></td>
<td><? CM8_ShowAd($cm8_ads_Button_120x90_pos6); ?></td>
</tr>
</table></div>
<?
}


//**********************************************************************************
// Registration and Manage settings functions
function displayCooperCombo(){
	global $_SESSION;
	global $lang;
	global $viaProducts,$HTPFXSSL,$HTHOST;
	$pageId='manage_setting';
	build_lang($pageId);

	if($_SESSION['combo']['typeSpecificId']==$viaProducts['CooperCombo']['typeSpecificId'] && $_SESSION['combo']['auto_renew']==1) { ?>
	<tr>
	<td width="40%" align="left" class="subs_step_head">Buzz &amp; Banter with Cooper</td>
	  <td width="40%" align="left"><label>Next bill: <span class="subs_step_head">$<?=$viaProducts['CooperCombo']['price']?> on <?=date('d-M-Y',strtotime(substr($_SESSION['combo']['expireDate'],0,10)))?></span> </label></td>
	<?php
	$height=488;$width=532;
	$strLink="orderNumber=".$_SESSION['combo']['orderNumber'];
	$strLink.="&orderItemSeq=".$_SESSION['combo']['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$_SESSION['combo']['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
	<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	}
}

function displayFlexFolioCombo(){
	global $_SESSION;
	global $lang;
	global $viaProducts,$HTPFXSSL,$HTHOST;
	$pageId='manage_setting';
	build_lang($pageId);

	if($_SESSION['combo']['typeSpecificId']==$viaProducts['FlexfolioCombo']['typeSpecificId'] && $_SESSION['combo']['auto_renew']==1) { ?>
	<tr>
	<td width="40%" align="left" class="subs_step_head">Buzz &amp; Banter with FlexFolio</td>
	<td width="40%" align="left"><label>Next bill: <span class="subs_step_head">$<?=$viaProducts['FlexfolioCombo']['price']?> on <?=date('d-M-Y',strtotime(substr($_SESSION['combo']['expireDate'],0,10)))?> </span></label></td>
	<?php
	$height=488;$width=532;
	$strLink="orderNumber=".$_SESSION['combo']['orderNumber'];
	$strLink.="&orderItemSeq=".$_SESSION['combo']['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$_SESSION['combo']['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
	<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription']; //=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	}
}

function displayBuzzBanter(){
	global $_SESSION;
	global $lang;
	global $viaProducts;
	$pageId='manage_setting';
	build_lang($pageId);
	$url  = getpageurl('buzzbanter');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's buzz product
	$buzzValues=$objVia->getBuzz($productsSession);
	// get buzz charges
	$buzzCharges=$objVia->getBuzzCharges();
	if (!$_SESSION['combo']) {
		if(count($buzzValues)>0){ // User has any type of buzz subscription
		//$buzzValues['price']=$buzzPrice[$buzzValues['typeSpecificId']];
		if(round($buzzValues['price'])==0){
			if($buzzValues['typeSpecificId']==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzQuartTrial']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($buzzValues['typeSpecificId'],$buzzValues['sourceCodeId']);
				$buzzValues['price']=$price;
			}
		}
		if(!$buzzValues['expireDate']) unset($buzzValues['price']);
	?>
	<? if(($buzzValues['typeSpecificId']==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzAnnualTrial']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzQuartTrial']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzMonthly']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzQuarterly']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzAnnual']['typeSpecificId']) && !$buzzValues['expireDate']) { ?>
	<tr>
	<td rowspan="2" align="left" valign="middle"  width="40%" class="subs_step_hide_heading">Buzz &amp; Banter </td>
	<td rowspan="2" valign="middle"  width="45%"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=BuzzBanter">Learn More</a> </td>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top"  width="5%" class="subs_manage_recurring">monthly $<?=$viaProducts['BuzzMonthly']['price']?> </td>
	<td valign="top"  width="5%" class="subs_manage_recurring">quarterly $<?=$viaProducts['BuzzQuarterly']['price']?> </td>
	<td valign="top"  width="10%" class="subs_manage_recurring">annual $<?=$viaProducts['BuzzAnnual']['price']?> </td>
	</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top" align="center"><img style="cursor:pointer;"  onclick="Javascript:checkcart('<?=$viaProducts['BuzzMonthly']['typeSpecificId']?>','<?=$viaProducts['BuzzMonthly']['orderClassId']?>','<?=$viaProducts['BuzzMonthly']['orderItemType']?>','subBuzzBanter','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" border="0" hspace="5" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;"  onclick="Javascript:checkcart('<?=$viaProducts['BuzzQuarterly']['typeSpecificId']?>','<?=$viaProducts['BuzzQuarterly']['orderClassId']?>','<?=$viaProducts['BuzzQuarterly']['orderItemType']?>','subBuzzBanter','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" border="0" hspace="5" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['BuzzAnnual']['typeSpecificId']?>','<?=$viaProducts['BuzzAnnual']['orderClassId']?>','<?=$viaProducts['BuzzAnnual']['orderItemType']?>','subBuzzBanter','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"  /></td>
	</tr>
	</table>
	</td>
	</tr>
	<? } else { ?>
	<tr>
	<td width="40%" align="left" ><span class="subs_step_head">Buzz &amp; Banter </span></td>
<?php	if($buzzValues['typeSpecificId']==$viaProducts['BuzzComplimentary']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzCK']['typeSpecificId'] || $buzzValues['typeSpecificId']==$viaProducts['BuzzScott']['typeSpecificId']  ){ ?>
	<td width="40%" align="left">&nbsp; </td>
<?php } else { ?>
	<td width="40%" align="left">Next bill: <span class="subs_step_head">$<?=number_format($buzzValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($buzzValues['expireDate'],0,10)))?></span> </td>
<?php } ?>
<?php
	$height=488;$width=532;
	$strLink="orderNumber=".$buzzValues['orderNumber'];
	$strLink.="&orderItemSeq=".$buzzValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$buzzValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
	<td width="1%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($buzzValues)==0){
	$url  = getpageurl('buzzbanter');
	?>
	<tr>
	<td rowspan="2" align="left" valign="middle"  width="40%" class="subs_step_hide_heading">Buzz &amp; Banter </td>
	<td rowspan="2" valign="middle"  width="45%"><a href="<?=$url['alias']?>">Learn More</a> </td>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top"  width="5%" class="subs_manage_recurring">monthly $<?=$viaProducts['BuzzMonthly']['price']?> </td>
	<td valign="top"  width="5%" class="subs_manage_recurring">quarterly $<?=$viaProducts['BuzzQuarterly']['price']?> </td>
	<td valign="top"  width="10%" class="subs_manage_recurring">annual $<?=$viaProducts['BuzzAnnual']['price']?> </td>
	</tr>
	</table>
	</td>
	<tr>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top" align="center"><img style="cursor:pointer;"  onclick="Javascript:checkcart('<?=$viaProducts['BuzzMonthly']['typeSpecificId']?>','<?=$viaProducts['BuzzMonthly']['orderClassId']?>','<?=$viaProducts['BuzzMonthly']['orderItemType']?>','subBuzzBanter','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" border="0" hspace="5" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;"  onclick="Javascript:checkcart('<?=$viaProducts['BuzzQuarterly']['typeSpecificId']?>','<?=$viaProducts['BuzzQuarterly']['orderClassId']?>','<?=$viaProducts['BuzzQuarterly']['orderItemType']?>','subBuzzBanter','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" border="0" hspace="5" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['BuzzAnnual']['typeSpecificId']?>','<?=$viaProducts['BuzzAnnual']['orderClassId']?>','<?=$viaProducts['BuzzAnnual']['orderItemType']?>','subBuzzBanter','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"  /></td>
	</tr>
	</table>
	</td>
	</tr>
	<?php	} ?>
	<?php }


}

function displayCooper(){
	global $_SESSION;
	global $lang;
	global $viaProducts;
	$pageId='manage_setting';
	build_lang($pageId);
	$url  = getpageurl('cooperhome');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's cooper product
	$cooperValues=$objVia->getCooper($productsSession);
	// get cooper charges
	$cooperCharges=$objVia->getCooperCharges();

	 if (!$_SESSION['combo'] || $_SESSION['combo']['typeSpecificId']!=$viaProducts['CooperCombo']['typeSpecificId']) {
		if(count($cooperValues)>0){ // User has any type of cooper subscription
		//$cooperValues['price']=$cooperPrice[$cooperValues['typeSpecificId']];
		if(round($cooperValues['price'])==0){

			if($cooperValues['typeSpecificId']==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperQuartTrial']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($cooperValues['typeSpecificId'],$cooperValues['sourceCodeId']);
				$cooperValues['price']=$price;
			}
		}
		if(!$cooperValues['expireDate']) unset($cooperValues['price']);
	?>
	<?php if(($cooperValues['typeSpecificId']==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperQuartTrial']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperAnnualTrial']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperMonthly']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperQuarterly']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperAnnual']['typeSpecificId']) && !$cooperValues['expireDate']) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading" >Jeff Cooper's Daily <br/>Market Report </td>
	<td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=DailyMarketReport">Learn More </td>
	<td colspan=2>
	<table>
	<tr>
	<td  valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['CooperMonthly']['price']?> </td>
	<td  valign="top" class="subs_manage_recurring">quarterly $<?=$viaProducts['CooperQuarterly']['price']?> </td>
	<td  valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['CooperAnnual']['price']?> </td>
	</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top" align="center" ><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperMonthly']['typeSpecificId']?>','<?=$viaProducts['CooperMonthly']['orderClassId']?>','<?=$viaProducts['CooperMonthly']['orderItemType']?>','subCooper','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td valign="top" align="center" ><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperQuarterly']['typeSpecificId']?>','<?=$viaProducts['CooperQuarterly']['orderClassId']?>','<?=$viaProducts['CooperQuarterly']['orderItemType']?>','subCooper','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperAnnual']['typeSpecificId']?>','<?=$viaProducts['CooperAnnual']['orderClassId']?>','<?=$viaProducts['CooperAnnual']['orderItemType']?>','subCooper','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	</tr>
	</table>
	</td>
	</tr>
	<?php }else { ?>
	<tr>
	<td width="26%" align="left" class="subs_step_head">Jeff Cooper's Daily <br/>Market Report </td>
<?php if($cooperValues['typeSpecificId']==$viaProducts['CooperComplimentary']['typeSpecificId'] || $cooperValues['typeSpecificId']==$viaProducts['CooperCK']['typeSpecificId'] ) {?>
	<td width="40%" align="left">&nbsp;</td>
<?php } else { ?>
	<td width="40%" align="left">Next bill: <span class="subs_step_head">$<?=number_format($cooperValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($cooperValues['expireDate'],0,10)))?></span></td>
<?php } ?>
	<?php
	$height=488;$width=532;
	$strLink="orderNumber=".$cooperValues['orderNumber'];
	$strLink.="&orderItemSeq=".$cooperValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$cooperValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";
	$label='Cancel Subscription';
	?>
	<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>

	<?php	if(count($cooperValues)==0){ ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading" >Jeff Cooper's Daily <br/>Market Report </td>
	<td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=DailyMarketReport">Learn More </td>
	<td colspan=2>
	<table>
	<tr>
	<td  valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['CooperMonthly']['price']?> </td>
	<td  valign="top" class="subs_manage_recurring">quarterly $<?=$viaProducts['CooperQuarterly']['price']?> </td>
	<td  valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['CooperAnnual']['price']?> </td>
	</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<table>
	<tr>
	<td valign="top" align="center" ><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperMonthly']['typeSpecificId']?>','<?=$viaProducts['CooperMonthly']['orderClassId']?>','<?=$viaProducts['CooperMonthly']['orderItemType']?>','subCooper','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td valign="top" align="center" ><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperQuarterly']['typeSpecificId']?>','<?=$viaProducts['CooperQuarterly']['orderClassId']?>','<?=$viaProducts['CooperQuarterly']['orderItemType']?>','subCooper','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['CooperAnnual']['typeSpecificId']?>','<?=$viaProducts['CooperAnnual']['orderClassId']?>','<?=$viaProducts['CooperAnnual']['orderItemType']?>','subCooper','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	</tr>
	</table>
	</td>
	</tr>
<?php
	}
	}
}

function displayFlexFolio(){
	global $_SESSION;
	global $lang;
	global $viaProducts;
	$pageId='manage_setting';
	build_lang($pageId);
	$url  = getpageurl('qphome');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's flexfolio product
	$flexfolioValues=$objVia->getFlexFolio($productsSession);
	// get flexfolio charges
	$flexfolioCharges=$objVia->getFlexFolioCharges();

	 if (!$_SESSION['combo'] || $_SESSION['combo']['typeSpecificId']!=$viaProducts['FlexfolioCombo']['typeSpecificId']) {
	 if(count($flexfolioValues)>0){ // // User has any type of flexfolio subscription
		//$flexfolioValues['price']=$flexfolioPrice[$flexfolioValues['typeSpecificId']];
		if(round($flexfolioValues['price'])==0){
			if($flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($flexfolioValues['typeSpecificId'],$flexfolioValues['sourceCodeId']);
				$flexfolioValues['price']=$price;
			}
		}
		if(!$flexfolioValues['expireDate']) unset($flexfolioValues['price']);
	?>
	<?php if(($flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'] || $flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioMonthly']['typeSpecificId'] || $flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioAnnual']['typeSpecificId']) && !$flexfolioValues['expireDate']) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Active Investor</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=ActiveInvestor">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['FlexfolioMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['FlexfolioAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['FlexfolioMonthly']['typeSpecificId']?>','<?=$viaProducts['FlexfolioMonthly']['orderClassId']?>','<?=$viaProducts['FlexfolioMonthly']['orderItemType']?>','subFlexFolio','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['FlexfolioAnnual']['typeSpecificId']?>','<?=$viaProducts['FlexfolioAnnual']['orderClassId']?>','<?=$viaProducts['FlexfolioAnnual']['orderItemType']?>','subFlexFolio','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php } else { ?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Active Investor</td>
<?php if($flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioComplimentary']['typeSpecificId'] || $flexfolioValues['typeSpecificId']==$viaProducts['FlexfolioCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($flexfolioValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($flexfolioValues['expireDate'],0,10)))?></span> </label></td>
<?php } ?>
	<?php
	$height=488;$width=532;
	$strLink="orderNumber=".$flexfolioValues['orderNumber'];
	$strLink.="&orderItemSeq=".$flexfolioValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$flexfolioValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($flexfolioValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Active Investor</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=ActiveInvestor">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['FlexfolioMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['FlexfolioAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['FlexfolioMonthly']['typeSpecificId']?>','<?=$viaProducts['FlexfolioMonthly']['orderClassId']?>','<?=$viaProducts['FlexfolioMonthly']['orderItemType']?>','subFlexFolio','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['FlexfolioAnnual']['typeSpecificId']?>','<?=$viaProducts['FlexfolioAnnual']['orderClassId']?>','<?=$viaProducts['FlexfolioAnnual']['orderItemType']?>','subFlexFolio','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php	} ?>
	<?php	}
}

function displayOptionSmith(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('sshome');
	$objVia=new Via();

	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's optionsmith product
	$optionsmithValues=$objVia->getOptionSmith($productsSession);
	// get optionsmith charges
	$optionsmithCharges=$objVia->getOptionsmithCharges();

	if(count($optionsmithValues)>0){ // // User has any type of optionsmith subscription

		if(round($optionsmithValues['price'])==0){
			if($optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($optionsmithValues['typeSpecificId'],$optionsmithValues['sourceCodeId']);
				$optionsmithValues['price']=$price;
			}
		}
		if(!$optionsmithValues['expireDate']) unset($optionsmithValues['price']);
	?>
	<?php if( ($optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'] || $optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithMonthly']['typeSpecificId'] || $optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithAnnual']['typeSpecificId']) && !$optionsmithValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">OptionSmith</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=OptionSmith">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['OptionsmithMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['OptionsmithAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['OptionsmithMonthly']['typeSpecificId']?>','<?=$viaProducts['OptionsmithMonthly']['orderClassId']?>','<?=$viaProducts['OptionsmithMonthly']['orderItemType']?>','subOptionSmith','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['OptionsmithAnnual']['typeSpecificId']?>','<?=$viaProducts['OptionsmithAnnual']['orderClassId']?>','<?=$viaProducts['OptionsmithAnnual']['orderItemType']?>','subOptionSmith','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">OptionSmith </td>
<?php if($optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithComplimentary']['typeSpecificId'] || $optionsmithValues['typeSpecificId']==$viaProducts['OptionsmithCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($optionsmithValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($optionsmithValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$optionsmithValues['orderNumber'];
	$strLink.="&orderItemSeq=".$optionsmithValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$optionsmithValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($optionsmithValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">OptionSmith</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=OptionSmith">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['OptionsmithMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['OptionsmithAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['OptionsmithMonthly']['typeSpecificId']?>','<?=$viaProducts['OptionsmithMonthly']['orderClassId']?>','<?=$viaProducts['OptionsmithMonthly']['orderItemType']?>','subOptionSmith','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['OptionsmithAnnual']['typeSpecificId']?>','<?=$viaProducts['OptionsmithAnnual']['orderClassId']?>','<?=$viaProducts['OptionsmithAnnual']['orderItemType']?>','subOptionSmith','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php	}
}

function displayBMTP(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('bmtp_home');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['PRODUCT'];

	// get user's BMTP  product
	$BTMPProduct=$objVia->getBMTPProduct($productsSession);
	if($BTMPProduct==false){
		// display final HTML
		?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Bull Market Timer</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=BMTP">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">$<?=$viaProducts['BMTP']['price']?> </td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['BMTP']['typeSpecificId']?>','<?=$viaProducts['BMTP']['orderClassId']?>','<?=$viaProducts['BMTP']['orderItemType']?>','subBMTP','Video','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	</tr>
<?
		// terminate here
	}

	if($BTMPProduct==true){
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];

	// get user's BMTP subscription
	$BMTPValues=$objVia->getBMTP($productsSession);
	// get BMTP charges
	$BMTPCharges=$objVia->getBMTPCharges();

	if(count($BMTPValues)>0){ // // User has any type of BMTP subscription

		if(round($BMTPValues['price'])==0){
			if($BMTPValues['typeSpecificId']==$viaProducts['BMTPAlertTrial']['typeSpecificId'] || $BMTPValues['typeSpecificId']==$viaProducts['BMTPAlertComplimentary']['typeSpecificId'] || $BMTPValues['typeSpecificId']==$viaProducts['BMTPAlertCK']['typeSpecificId']){
				$price=$objVia->getActualPrice($BMTPValues['typeSpecificId'],$BMTPValues['sourceCodeId']);
				$BMTPValues['price']=$price;
			}
		}
		if(!$BMTPValues['expireDate']) unset($BMTPValues['price']);

	?>
	<?php	}

}
}
function displayJack(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('jack_home');
	$objVia=new Via();

	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's optionsmith product
	$jackValues=$objVia->getJack($productsSession);
	// get optionsmith charges
	$jackCharges=$objVia->getJackCharges();

	if(count($jackValues)>0){ // // User has any type of optionsmith subscription

		if(round($jackValues['price'])==0){
			if($jackValues['typeSpecificId']==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($jackValues['typeSpecificId'],$jackValues['sourceCodeId']);
				$jackValues['price']=$price;
			}
		}
		if(!$jackValues['expireDate']) unset($jackValues['price']);
	?>
	<?php if( ($jackValues['typeSpecificId']==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackAnnualTrial']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackMonthly']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackAnnual']['typeSpecificId']) && !$jackValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Lavery Insight</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=LaveryInsight">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['JackMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['JackAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['JackMonthly']['typeSpecificId']?>','<?=$viaProducts['JackMonthly']['orderClassId']?>','<?=$viaProducts['JackMonthly']['orderItemType']?>','subLaveryInsight','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['JackAnnual']['typeSpecificId']?>','<?=$viaProducts['JackAnnual']['orderClassId']?>','<?=$viaProducts['JackAnnual']['orderItemType']?>','subLaveryInsight','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Lavery Insight</td>
<?php if($jackValues['typeSpecificId']==$viaProducts['JackComplimentary']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($jackValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($jackValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$optionsmithValues['orderNumber'];
	$strLink.="&orderItemSeq=".$optionsmithValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$optionsmithValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($jackValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Lavery Insight</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=LaveryInsight">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['JackMonthly']['price']?> </td>
	<td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['JackAnnual']['price']?></td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['JackMonthly']['typeSpecificId']?>','<?=$viaProducts['JackMonthly']['orderClassId']?>','<?=$viaProducts['JackMonthly']['orderItemType']?>','subLaveryInsight','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['JackAnnual']['typeSpecificId']?>','<?=$viaProducts['JackAnnual']['orderClassId']?>','<?=$viaProducts['JackAnnual']['orderItemType']?>','subLaveryInsight','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
	</tr>
	<?php	}
}

function displayETF(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('etf_home');
	$objVia=new Via();

	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's etf product
	$etfValues=$objVia->getETF($productsSession);
	// get etf charges
	$etfCharges=$objVia->getETFCharges();

	if(count($etfValues)>0){ // // User has any type of etf subscription

		if(round($etfValues['price'])==0){
			if($etfValues['typeSpecificId']==$viaProducts['ETFMonthlyTrial']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFQuartTrial']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($etfValues['typeSpecificId'],$etfValues['sourceCodeId']);
				$etfValues['price']=$price;
			}
		}
		if(!$etfValues['expireDate']) unset($etfValues['price']);
	?>
	<?php if( ($etfValues['typeSpecificId']==$viaProducts['ETFMonthlyTrial']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFQuartTrial']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFAnnualTrial']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFMonthly']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFQuart']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFAnnual']['typeSpecificId']) && !$etfValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Grail ETF & Equity Investor</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=ETFInvestor">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; monthly $<?=$viaProducts['ETFMonthly']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; 3 Months $<?=$viaProducts['ETFQuart']['price']?></span>





	  </td>
</tr>
	  <tr>
	 	<td colspan="2">

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFMonthly']['typeSpecificId']?>','<?=$viaProducts['ETFMonthly']['orderClassId']?>','<?=$viaProducts['ETFMonthly']['orderItemType']?>','subETF','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFQuart']['typeSpecificId']?>','<?=$viaProducts['ETFQuart']['orderClassId']?>','<?=$viaProducts['ETFQuart']['orderItemType']?>','subETF','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>



<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['ETFAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFAnnual']['typeSpecificId']?>','<?=$viaProducts['ETFAnnual']['orderClassId']?>','<?=$viaProducts['ETFAnnual']['orderItemType']?>','subETF','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>



	  </td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Grail ETF & Equity Investor</td>
<?php if($etfValues['typeSpecificId']==$viaProducts['ETFComplimentary']['typeSpecificId'] || $etfValues['typeSpecificId']==$viaProducts['ETFCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($etfValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($etfValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$optionsmithValues['orderNumber'];
	$strLink.="&orderItemSeq=".$optionsmithValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$optionsmithValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($etfValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Grail ETF & Equity Investor</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=ETFInvestor">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['ETFMonthly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">3 Months $<?=$viaProducts['ETFQuart']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['ETFAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFMonthly']['typeSpecificId']?>','<?=$viaProducts['ETFMonthly']['orderClassId']?>','<?=$viaProducts['ETFMonthly']['orderItemType']?>','subETF','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFQuart']['typeSpecificId']?>','<?=$viaProducts['ETFQuart']['orderClassId']?>','<?=$viaProducts['ETFQuart']['orderItemType']?>','subETF','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['ETFAnnual']['typeSpecificId']?>','<?=$viaProducts['ETFAnnual']['orderClassId']?>','<?=$viaProducts['ETFAnnual']['orderItemType']?>','subETF','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}


function displayTheStockPlayBook(){
	global $_SESSION,$lang,$viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('thestockplaybook');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's TheStockplaybook product
	$theStockPlabookValues=$objVia->getTheStockPlayBook($productsSession);
	// get TheStockplaybook charges
	$theStockPlabookCharges=$objVia->getTheStockPlaybookCharges();

	if(count($theStockPlabookValues)>0){ // // User has any type of etf subscription

		if(round($theStockPlabookValues['price'])==0){
			if($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($theStockPlabookValues['typeSpecificId'],$theStockPlabookValues['sourceCodeId']);
				$theStockPlabookValues['price']=$price;
			}
		}
		if(!$theStockPlabookValues['expireDate']) unset($theStockPlabookValues['price']);
	?>
	<?php if( ($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookQuart']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookAnnual']['typeSpecificId']) && !$theStockPlabookValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">The Stock Playbook</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TheStockPlaybook">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; monthly $<?=$viaProducts['TheStockPlaybookMonthly']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; 3 Months $<?=$viaProducts['TheStockPlaybookQuart']['price']?></span>





	  </td>
</tr>
	  <tr>
	 	<td colspan="2">

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookMonthly']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookMonthly']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookMonthly']['orderItemType']?>','subTheStockPlayBook','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookQuart']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookQuart']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookQuart']['orderItemType']?>','subTheStockPlayBook','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>



<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TheStockPlaybookAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookAnnual']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookAnnual']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookAnnual']['orderItemType']?>','subTheStockPlayBook','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>



	  </td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">The Stock Playbook</td>
<?php if($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookComplimentary']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($theStockPlabookValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($theStockPlabookValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$theStockPlabookValues['orderNumber'];
	$strLink.="&orderItemSeq=".$theStockPlabookValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$theStockPlabookValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($theStockPlabookValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">The Stock Playbook</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TheStockPlaybook">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['TheStockPlaybookMonthly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">3 Months $<?=$viaProducts['TheStockPlaybookQuart']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TheStockPlaybookAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookMonthly']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookMonthly']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookMonthly']['orderItemType']?>','subTheStockPlaybook','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookQuart']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookQuart']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookQuart']['orderItemType']?>','subTheStockPlaybook','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookAnnual']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookAnnual']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookAnnual']['orderItemType']?>','subTheStockPlaybook','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}


function displayTheStockPlayBookPremium(){
	global $_SESSION,$lang,$viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('thestockplaybook');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's TheStockplaybook product
	$theStockPlabookValues=$objVia->getTheStockPlayBookPremium($productsSession);
	// get TheStockplaybook charges
	$theStockPlabookCharges=$objVia->getTheStockPlaybookPremiumCharges();
	if(count($theStockPlabookValues)>0){ // // User has any type of TSPB Premium subscription
		if(round($theStockPlabookValues['price'])==0){
			if($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($theStockPlabookValues['typeSpecificId'],$theStockPlabookValues['sourceCodeId']);
				$theStockPlabookValues['price']=$price;
			}
		}
		if(!$theStockPlabookValues['expireDate']) unset($theStockPlabookValues['price']);
	?>
	<?php if(($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId']) && !$theStockPlabookValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">The Stock Playbook Premium</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TheStockPlaybook">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; monthly $<?=$viaProducts['TheStockPlaybookPremiumMonthly']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; 3 Months $<?=$viaProducts['TheStockPlaybookPremiumQuart']['price']?></span>
</td>
</tr>
	  <tr>
	 	<td colspan="2">

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumMonthly']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType']?>','subTheStockPlayBook','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumQuart']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumQuart']['orderItemType']?>','subTheStockPlayBook','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>



<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TheStockPlaybookAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumAnnual']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumAnnual']['orderItemType']?>','subTheStockPlayBook','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>



	  </td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">The Stock Playbook Premium</td>
<?php if($theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId'] || $theStockPlabookValues['typeSpecificId']==$viaProducts['TheStockPlaybookPremiumCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($theStockPlabookValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($theStockPlabookValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$theStockPlabookValues['orderNumber'];
	$strLink.="&orderItemSeq=".$theStockPlabookValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$theStockPlabookValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($theStockPlabookValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">The Stock Playbook Premium</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TheStockPlaybook">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['TheStockPlaybookPremiumMonthly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">3 Months $<?=$viaProducts['TheStockPlaybookPremiumQuart']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TheStockPlaybookPremiumAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumMonthly']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType']?>','subTheStockPlaybook','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumQuart']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumQuart']['orderItemType']?>','subTheStockPlaybook','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId']?>','<?=$viaProducts['TheStockPlaybookPremiumAnnual']['orderClassId']?>','<?=$viaProducts['TheStockPlaybookPremiumAnnual']['orderItemType']?>','subTheStockPlaybook','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}


function displayAdFree(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('adFree');
	$objVia=new Via();

	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's optionsmith product
	$adFreeValues=$objVia->getAdFree($productsSession);
	// get optionsmith charges
	$adFreeCharges=$objVia->getAdFreeCharges();

	if(count($adFreeValues)>0){ // // User has any type of optionsmith subscription

		/*if(round($adFreeValues['price'])==0){
			if($adFreeValues['typeSpecificId']==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $jackValues['typeSpecificId']==$viaProducts['JackAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($jackValues['typeSpecificId'],$jackValues['sourceCodeId']);
				$jackValues['price']=$price;
			}
		}*/
		if(!$adFreeValues['expireDate']) unset($adFreeValues['price']);
	?>
	<?php if( ($adFreeValues['typeSpecificId']==$viaProducts['AdFreeMonthly']['typeSpecificId'] ) && !$adFreeValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Ad Free</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=AdFree">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['AdFreeMonthly']['price']?> </td>
		  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['AdFreeMonthly']['typeSpecificId']?>','<?=$viaProducts['AdFreeMonthly']['orderClassId']?>','<?=$viaProducts['AdFreeMonthly']['orderItemType']?>','subAdFree','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>

	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Ad Free</td>
<?php if($adFreeValues['typeSpecificId']==$viaProducts['AdFreeComplimentary']['typeSpecificId'] ) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($adFreeValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($adFreeValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$optionsmithValues['orderNumber'];
	$strLink.="&orderItemSeq=".$optionsmithValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$optionsmithValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($adFreeValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">The Ads Free</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?trial=hard&utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=AdFree">Learn More</a></td>
<td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['AdFreeMonthly']['price']?> </td>
	  </tr>
	  <tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['AdFreeMonthly']['typeSpecificId']?>','<?=$viaProducts['AdFreeMonthly']['orderClassId']?>','<?=$viaProducts['AdFreeMonthly']['orderItemType']?>','subAdFree','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>

	</tr>
	<?php	}
}

function displayTechStrat(){
	global $_SESSION,$lang,$viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('techstrat_landing');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's Tech Start product
	$techStratValues=$objVia->getTechStrat($productsSession);
	// get Tech Start charges
	$techStratCharges=$objVia->getTechStratCharges();

	if(count($techStratValues)>0){ // // User has any type of etf subscription

		if(round($techStratValues['price'])==0){
			if($techStratValues['typeSpecificId']==$viaProducts['TechStratMonthlyTrial']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratQuarterTrial']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($techStratValues['typeSpecificId'],$techStratValues['sourceCodeId']);
				$techStratValues['price']=$price;
			}
		}
		if(!$techStratValues['expireDate']) unset($techStratValues['price']);
	?>
	<?php if( ($techStratValues['typeSpecificId']==$viaProducts['TechStratMonthlyTrial']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratQuarterTrial']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratAnnualTrial']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratMonthly']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratQuarterly']['typeSpecificId'] || $techStratValues['typeSpecificId']==$viaProducts['TechStratAnnual']['typeSpecificId']) && !$techStratValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">TechStrat  Report</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TechStrat">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; monthly $<?=$viaProducts['TechStratMonthly']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; quarterly $<?=$viaProducts['TechStratQuarterly']['price']?></span>





	  </td>
</tr>
	  <tr>
	 	<td colspan="2">

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratMonthly']['typeSpecificId']?>','<?=$viaProducts['TechStratMonthly']['orderClassId']?>','<?=$viaProducts['TechStratMonthly']['orderItemType']?>','subTechStrat','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratQuarterly']['typeSpecificId']?>','<?=$viaProducts['TechStratQuarterly']['orderClassId']?>','<?=$viaProducts['TechStratQuarterly']['orderItemType']?>','subTechStrat','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>



<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TechStratAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratAnnual']['typeSpecificId']?>','<?=$viaProducts['TechStratAnnual']['orderClassId']?>','<?=$viaProducts['TechStratAnnual']['orderItemType']?>','subTechStrat','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>



	  </td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">TechStrat  Report</td>
<?php if($techStratValues['typeSpecificId']==$viaProducts['TechStratComplimentary']['typeSpecificId'] ) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($techStratValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($techStratValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$techStratValues['orderNumber'];
	$strLink.="&orderItemSeq=".$techStratValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$techStratValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($techStratValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">TechStrat  Report</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=TechStrat">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['TechStratMonthly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">quarterly $<?=$viaProducts['TechStratQuarterly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['TechStratAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratMonthly']['typeSpecificId']?>','<?=$viaProducts['TechStratMonthly']['orderClassId']?>','<?=$viaProducts['TechStratMonthly']['orderItemType']?>','subTechStrat','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratQuarterly']['typeSpecificId']?>','<?=$viaProducts['TechStratQuarterly']['orderClassId']?>','<?=$viaProducts['TechStratQuarterly']['orderItemType']?>','subTechStrat','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['TechStratAnnual']['typeSpecificId']?>','<?=$viaProducts['TechStratAnnual']['orderClassId']?>','<?=$viaProducts['TechStratAnnual']['orderItemType']?>','subTechStrat','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}

function displayHousingReport(){
	global $_SESSION,$lang,$viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('housingMarketReport_landing');
	$objVia=new Via();
	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's TheStockplaybook product
	$housingMarketReportValues=$objVia->getHousingReport($productsSession);
	// get TheStockplaybook charges
	$housingMarketReportCharges=$objVia->getHousingReportCharges();

	if(count($housingMarketReportValues)>0){ // // User has any type of etf subscription

		if(!$housingMarketReportValues['expireDate']) unset($housingMarketReportValues['price']);
	?>
	<?php if( ($housingMarketReportValues['typeSpecificId']==$viaProducts['Housing3Months']['typeSpecificId'] || $housingMarketReportValues['typeSpecificId']==$viaProducts['Housing6Months']['typeSpecificId'] || $housingMarketReportValues['typeSpecificId']==$viaProducts['HousingAnnual']['typeSpecificId']) && !$housingMarketReportValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Housing Market Report</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=HousingMarketReport">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; 3 Months $<?=$viaProducts['Housing3Months']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; 6 Months $<?=$viaProducts['Housing6Months']['price']?></span>

	  </td>
</tr>
	  <tr>
	 	<td colspan="2">
	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['Housing3Months']['typeSpecificId']?>','<?=$viaProducts['Housing3Months']['orderClassId']?>','<?=$viaProducts['Housing3Months']['orderItemType']?>','subHousingMarketReport','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['Housing6Months']['typeSpecificId']?>','<?=$viaProducts['Housing6Months']['orderClassId']?>','<?=$viaProducts['Housing6Months']['orderItemType']?>','subHousingMarketReport','HalfYearly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>

<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['HousingAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['HousingAnnual']['typeSpecificId']?>','<?=$viaProducts['HousingAnnual']['orderClassId']?>','<?=$viaProducts['HousingAnnual']['orderItemType']?>','subHousingMarketReport','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>
	  </td>
	</tr>
	<?php } else { ?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Hosing Market Report</td>

		<?php if($housingMarketReportValues['typeSpecificId']==$viaProducts['HousingComplimentary']['typeSpecificId'] ) {?>
				<td width="33%" align="left">&nbsp;</td>
		<?php } else {?>
				<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($housingMarketReportValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($housingMarketReportValues['expireDate'],0,10)))?></span> </label></td>
		<?php }






	 $height=488;$width=532;
	$strLink="orderNumber=".$housingMarketReportValues['orderNumber'];
	$strLink.="&orderItemSeq=".$housingMarketReportValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$housingMarketReportValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription'; ?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($housingMarketReportValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Housing Market Report</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=HousingMarketReport">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">3 Months $<?=$viaProducts['Housing3Months']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">6 Months $<?=$viaProducts['Housing6Months']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">Annual $<?=$viaProducts['HousingAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['Housing3Months']['typeSpecificId']?>','<?=$viaProducts['Housing3Months']['orderClassId']?>','<?=$viaProducts['Housing3Months']['orderItemType']?>','subHousingMarketReport','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['Housing6Months']['typeSpecificId']?>','<?=$viaProducts['Housing6Months']['orderClassId']?>','<?=$viaProducts['Housing6Months']['orderItemType']?>','subHousingMarketReport','HalfYearly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['HousingAnnual']['typeSpecificId']?>','<?=$viaProducts['HousingAnnual']['orderClassId']?>','<?=$viaProducts['HousingAnnual']['orderItemType']?>','subHousingMarketReport','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}



function displayGaryK(){
	global $_SESSION;
	global $lang;
	global $viaProducts;

	$pageId='manage_setting';
	build_lang($pageId);
	$url=getpageurl('garyk_landing');
	$objVia=new Via();

	$productsSession=$_SESSION['products']['SUBSCRIPTION'];
	// get user's GaryK product
	$garyKValues=$objVia->getGaryK($productsSession);
	// get garyK charges
	$garyKCharges=$objVia->getGaryKCharges();

	if(count($garyKValues)>0){ // // User has any type of garyK subscription

		if(round($garyKValues['price'])==0){
			if($garyKValues['typeSpecificId']==$viaProducts['GaryKMonthlyTrial']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKQuarterTrial']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKAnnualTrial']['typeSpecificId']){
				$price=$objVia->getActualPrice($garyKValues['typeSpecificId'],$garyKValues['sourceCodeId']);
				$garyKValues['price']=$price;
			}
		}
		if(!$garyKValues['expireDate']) unset($garyKValues['price']);
	?>
	<?php if( ($garyKValues['typeSpecificId']==$viaProducts['GaryKMonthlyTrial']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKQuarterTrial']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKAnnualTrial']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKMonthly']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKQuarterly']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKAnnual']['typeSpecificId']) && !$garyKValues['expireDate']) { ?>
<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Gary Kaltbaum's Equity Trading Setups</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=garyk">Learn More</a></td>
	  <td colspan="2" align="center">
	<span class="subs_manage_recurring"> &nbsp; &nbsp; monthly $<?=$viaProducts['GaryKMonthly']['price']?> </span>
	<span valign="top" align="right" class="subs_manage_recurring">&nbsp; &nbsp; 3 Months $<?=$viaProducts['GaryKQuarterly']['price']?></span>





	  </td>
</tr>
	  <tr>
	 	<td colspan="2">

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKMonthly']['typeSpecificId']?>','<?=$viaProducts['GaryKMonthly']['orderClassId']?>','<?=$viaProducts['GaryKMonthly']['orderItemType']?>','subGaryK','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="14" />
	  </span>

	  <span>
	  <img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKQuarterly']['typeSpecificId']?>','<?=$viaProducts['GaryKQuarterly']['orderClassId']?>','<?=$viaProducts['GaryKQuarterly']['orderItemType']?>','subGaryK','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" />
	  </span>



<table width="0%" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['GaryKAnnual']['price']?></td>
  </tr>
  <tr>
    <td  valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKAnnual']['typeSpecificId']?>','<?=$viaProducts['GaryKAnnual']['orderClassId']?>','<?=$viaProducts['GaryKAnnual']['orderItemType']?>','subGaryK','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" hspace="12"/></td>
  </tr>
</table>



	  </td>
	</tr>
	<?php } else {?>
	<tr>
		<td width="26%" align="left" class="subs_step_head">Gary Kaltbaum's Equity Trading Setups</td>
<?php if($garyKValues['typeSpecificId']==$viaProducts['GaryKComplimentary']['typeSpecificId'] || $garyKValues['typeSpecificId']==$viaProducts['GaryKCK']['typeSpecificId']) {?>
		<td width="33%" align="left">&nbsp;</td>
<?php } else {?>
		<td width="33%" align="left"><label>Next bill: <span class="subs_step_head">$<?=number_format($garyKValues['price'],2,'.','')?> on <?=date('d-M-Y',strtotime(substr($garyKValues['expireDate'],0,10)))?></span> </label></td>
<?php }
	$height=488;$width=532;
	$strLink="orderNumber=".$optionsmithValues['orderNumber'];
	$strLink.="&orderItemSeq=".$optionsmithValues['orderItemSeq'];
	$strLink.="&payType=".$ccType;
	$refundAmount='0.00';
	$strLink.="&refundAmount=".$refundAmount;// to be calculated
	$strLink.="&refundToCustId=".$_SESSION['viaid'];
	$strLink.="&typeSpecificId=".$optionsmithValues['typeSpecificId'];

	$url=$HTPFXSSL.$HTHOST."/subscription/register/cancelproducts.htm?$strLink";$linkId="navlink_1";$label='Cancel Subscription';
	?>
<td width="41%" colspan="2" align="center" valign="top"><?=$lang['call_cancel_subscription'];//=iboxCall($linkId,$label,$url,$height,$width, $targeturl)?> </td>
	</tr>
	<?php	} ?>
	<?php	} ?>
	<?php	if(count($garyKValues)==0) { ?>
	<tr>
	<td rowspan="2" valign="middle" align="left" class="subs_step_hide_heading">Gary Kaltbaum's Equity Trading Setups</td>
	  <td rowspan="2" valign="middle"><a href="<?=$url['alias']?>?utm_source=SubPageLearnMore&utm_medium=Text&utm_content=LearnMore&utm_campaign=garyk">Learn More</a></td>
	  <td colspan="2">
	  	<table><tr>
		  <td valign="top" class="subs_manage_recurring">monthly $<?=$viaProducts['GaryKMonthly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">3 Months $<?=$viaProducts['GaryKQuarterly']['price']?> </td>
		  <td valign="top" class="subs_manage_recurring">annual $<?=$viaProducts['GaryKAnnual']['price']?></td>
	  </tr></table>
	  </td>
	</tr>
	<tr>
	   <td colspan="2">
	  	<table><tr>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKMonthly']['typeSpecificId']?>','<?=$viaProducts['GaryKMonthly']['orderClassId']?>','<?=$viaProducts['GaryKMonthly']['orderItemType']?>','subGaryK','Monthly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
	  <td width="20%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKQuarterly']['typeSpecificId']?>','<?=$viaProducts['GaryKQuarterly']['orderClassId']?>','<?=$viaProducts['GaryKQuarterly']['orderItemType']?>','subGaryK','Quarterly','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg" /></td>
		<td width="21%" valign="top" align="center"><img style="cursor:pointer;" border="0" onclick="Javascript:checkcart('<?=$viaProducts['GaryKAnnual']['typeSpecificId']?>','<?=$viaProducts['GaryKAnnual']['orderClassId']?>','<?=$viaProducts['GaryKAnnual']['orderItemType']?>','subGaryK','Annual','Register');" src="<?=$HTPFX.$HTHOST?>/images/redesign/add_to_cart_manage_setting.jpg"/></td>
		</tr></table>
	  </td>
	</tr>
	<?php	}
}


//******************************************************
function make_ajax_pagination_slide($divid,$link,$count,$MAX,$numrows1)
{
	if(ceil($numrows1/$MAX)<20)
	{
		$show=ceil($numrows1/$MAX);
	}
	else
	{
		$show=20;
	}
	$seperator = '&count=';
	$strPage="<div class='sliding_slides_controller'><table width='0%' cellspacing='0' cellpadding='4'  border='0' align='center'><tr>";
	$strPage.=slideshowpagination($numrows1, $show, $count, $link, $link, $seperator,$divid,20,$MAX);
	$strPage.="</tr></table></div>";
	return $strPage;
}//end of function function make_ajax_pagination_video()

// google_like_pagination.php
function slideshowpagination($number, $show, $showing, $firstlink, $baselink, $seperator,$divid,$heighest,$max) {
	global $IMG_SERVER;
	$noofpage=ceil($number/$max);
	$number=$noofpage;
	if($show<$heighest)
	{
		$low=1;
		$high=$show;
	}
	else
	{
		$disp = floor($show / 2);
		if ( $showing <= $disp)
		{
			$low=1;

			$high = ($low + $show) - 1;
		}
		elseif ( ($showing + $disp) > $number)
		{

			$high = $number;
			$low = ($number - $show) + 1;
		}

		else
		{

			$low = ($showing - $disp);
			$high = ($showing + $disp);
		}

	}
	if ( ($showing - 1) > 0 ) :
	if ( ($showing - 1) == 1 ):
	$prev  = slideshowquickLink ($firstlink, 'Previous', '', '<img height="10" width="9" src="'.$IMG_SERVER.'/images/slideshow/bottom_pre_hide_button.gif" border=0 />',$divid);
	else:
	$prev  = slideshowquickLink ($baselink . $seperator . ($showing - 1),'Previous', 'z', '<img height="10" width="9" src="'.$IMG_SERVER.'/images/slideshow/bottom_pre_hide_button.gif" border=0 />',$divid);
	endif;
	else:
	$prev  = '<td ><img height="10" width="9" src="'.$IMG_SERVER.'/images/slideshow/bottom_pre_button.gif" border=0 /></td>';
	endif;

	$next  = ($showing + 1) <= $number ? slideshowquickLink ($baselink . $seperator . ($showing + 1),'Next', 'x', '<img height="10" width="9" src='.$IMG_SERVER.'/images/slideshow/bottom_next_button.gif  border=0 />',$divid) : '<td ><img height="10" width="9" src='.$IMG_SERVER.'/images/slideshow/bottom_next_hide_button.gif border=0 /></td>';
	$navi = '';
	// start the navi

	$navi .=  $prev ;


	foreach (range($low, $high) as $newnumber)
	{

		$link = ( $newnumber == 1 ) ? $firstlink : $baselink . $seperator . $newnumber;
		if ($newnumber > $number)
		{
			$navi .= '';
		}
		elseif ($newnumber == 0)
		{
			$navi .= '';
		}
		else
		{
			if( $newnumber == $showing )
			{
				$navi .="<td style='color:#07c5fa;'>".$newnumber."</td>" ;
			}


			else
			{
				//echo "<br>".
				$newlink="$newnumber";
				$navi .=slideshowquickLink ($link, 'Page '. $newnumber, '', $newlink,$divid) ;
			}
		}

	}


	$navi .= $next ;



	return $navi;

}
//******************************************************
function slideshowquickLink ($linkHref, $desc, $accessKey, $linkTitle,$divid) {
	$theLink = "<td width='5'><a style=\"cursor:hand;cursor:pointer;\" onclick=\"Javascript:getSlidePhotos('".$divid."','".$linkHref."');\" title=\"". $desc ."\" accesskey=\"".$accessKey ."\" >". $linkTitle ."</a></td>";
	return $theLink;
}

function showSlideshowComment($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$showcomment,$imagevalue,$sid,$eid,$urlPost,$rand=NULL,$threadid=NULL,$slideshowTitle,$sourceURL)
{
	global $HTPFX,$HTHOST,$HTPFXSSL;
	$objThread = new Thread();
	$objaddrequest=new friends();
	$objLink = new Exchange_Element();
	$threadArray = $objThread->get_thread_on_object($articleid,$from);
	$threadid=$threadArray['id'];
	$objThread->thread_id=$threadid;
	$url =$HTPFX.$HTHOST.'/articles/Post.php?subscription_id='.$subscription_id;
	$url.='&thread_id='.$threadid;
	$url.='&comment_id='.$rand;
	$url.='&slideshowTitle='.$slideshowTitle;
	$url.='&sourceURL='.$sourceURL;

	if($imagevalue=="1")
	{
		$commentimage='plush.gif';
		$imagevalue=0;
	}
	else
	{
		$commentimage='Dash.gif';
		$imagevalue=1;
	}
	if($appcommentcount>1)
		$txtComments="Comments";
	else
		$txtComments="Comment";
	?>
			<div class="comment_article_main">
			<div class="comment_article_header">
			<span id="post_comment_heading"><?=$txtComments;?> (<?=$appcommentcount?>)</span><span style="padding-left:20px; vertical-align:middle;">
		<?php
		$exchange_prevquery="select ebs.subscription_id,ebs.value,ebs.blockservice_id,es.serviceid from
		ex_blockservices ebs,ex_services es	where ebs.blockservice_id=es.id
		and ebs.subscription_id='".$subscription_id."' and 	ebs.value='on'";
					// echo "<br>",$exchange_prevquery;
	$exchange_prevresult=exec_query($exchange_prevquery);
	$serviceidarray='';
	if(count($exchange_prevresult)){
						//**** foreach(exec_query($exchange_prevquery) as $rowexchange_prevquery){
		foreach($exchange_prevresult as $rowexchange_prevquery)
		{
			if($serviceidarray=="")
			{
				$serviceidarray=$rowexchange_prevquery['serviceid'];
			}
			else
			{
				$serviceidarray.=",".$rowexchange_prevquery['serviceid'];
			}
		}
	}
	if(ereg("all_services",$serviceidarray))
	{
		$chk='true';
	}
	else if(ereg("comment_posts",$serviceidarray))
	{
		$chk='true';
	}
	if(!$subscription_id <> "")
	{
		$label='<a href="'.$HTPFXSSL.$HTHOST.'/subscription/register/login.htm"><img src="'.$IMG_SERVER.'/images/redesign/post_comment.gif" align="absmiddle" vspace="2" border="0" style="padding-top:0px;cursor:pointer;" /></a>';
		echo $label;

	}
	else {
		if($chk)
		{
			?>
					<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif"  border="0" onclick="Javascript:checkprevilages('<?=$rand?>','<?=$url?>','<?=$chk?>');" vspace="2" align="absmiddle" style="padding-top:0px;cursor:pointer;"/>
				<?
		}
		else
		{
			?>
					<img src="<?=$IMG_SERVER?>/images/redesign/post_comment.gif" border="0" onclick="Javascript:preHttpRequest('<?=$rand?>','<?=$url?>');" align="absmiddle" vspace="2" style="padding-top:0px;cursor:pointer;"/>
				<?php
		}
	}
	?></span>
			<?
			$discussionattr['q']=$threadid;
			$discussionattr['slideid']=$articleid;
	$label='<span id="post_comment_small_heading">See All Comments &raquo;</span>';
	$tageturl=$page_config['single_discussion']['URL']."?".'q='.$threadid."/slideid/".$articleid;


	iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$tageturl);

	if($appcommentcount!=='0')
	{
		$urlPost=$HTPFX.$HTHOST."/articles/Post.php";
		?>
				<span id="post_comment_small_heading"><img class="articleCommentbar" src="<?=$IMG_SERVER?>/images/community_images/<?=$commentimage;?>" onclick="showCommentbox('<?=$appcommentcount;?>','<?=$articleid;?>','<?=$subscription_id;?>','<?=$pageName;?>','<?=$profile_exchange;?>','<?=$from;?>','<?=$imagevalue?>','<?=$sid?>','<?=$eid?>','<?=$urlPost;?>');"></span>
			<?  } ?>
			</div>
			</div>

			<?
			if($showcomment)
		{
			getSlideshowPostedComments($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid);
		}
		?>
				<?
}// end show comment function

function getSlideshowPostedComments($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$sid,$eid){
	global $page_config;
	$objThread = new Thread();
	$objaddrequest=new friends();
	$objLink = new Exchange_Element();
	$threadArray = $objThread->get_thread_on_object($articleid,$from);
	$threadid=$threadArray['id'];
	$objThread->thread_id=$threadid;
	$targeturl="/community/discussion/discussion/".$threadid;

	?>
			<!--Main comment box start from here-->
			<!-- <div class="comment_article_main"> -->
							<?
							$rand=rand();
	$url =$HTPFX.$HTHOST.'/articles/Post.php?subscription_id='.$subscription_id;
	$url.='&thread_id='.$threadid;
	$url.='&comment_id='.$rand;
	$urlmessage='Post.php?from_subscription_id='.$subscription_id;
	$urlmessage.='&message_id='.$rand;
	build_lang($pageName);
	global $lang;
	$count = "5";
	$content_type=$from;
	$comments = $objThread->get_all_comments($objThread,$content_type='articles', 'teasure', 0, $count,$user,$shift=0);
	$index=0;
	if(is_array($comments))

		foreach($comments as $postkey=> $postval)
		{
			$commentid = $postval['postid'];
			$date=$postval['date'];
			$datevalue = $objThread->check_date($date);
			$firstname=$objThread->get_username($postval['subid']);
			$urlmessage.='&to_subscription_id='.$postval['subid'];
			$commentposterName=ucwords(strtolower($postval['name']));
			$posterFname=ucwords(strtolower($firstname[0][fname]));

			?>
					<div class="comment_outer_box">


							   <table width="100%" border="0" cellpadding="0" cellpadding="0" class="comment_article_mainTable">
				<tr>
				<td rowspan="2" width="20%" valign="top"  bgcolor="#ECEAEB">
				<div class="comment_name_box">
						   <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
				<tr>
				<td class="comment_heading">
						  <?php
						  $profileattr['userid']=$postval['subid'];
			$discussionattr['q']=$threadid;
			$discussionattr['slideid']=$articleid;
			$label=$commentposterName;
			$targeturl=$HTPFX.$HTHOST.'/community/profile/userid/'.$profileattr['userid']."/slideid/".$articleid; // used
			$targeturlprofile=$HTPFX.$HTHOST.'/community/profile/userid/'.$profileattr['userid'];
			iboxcheckArticle('profile',$profileattr,$label,$sid,$eid,$targeturlprofile);
			?>
					<?php // echo $commentposterName?></a></td>
					</tr>
					<tr><td><div class="error" id="sendrequest<?=$postkey?>"></div></td></tr>
				<tr>
				<td class="comment_sub_heading">
						  <?php
						  $chkFriend=is_friend($postval['subid'],$subscription_id);
			if($chkFriend==0){
				if(!$eid){
					// FRIEND Request
					$label=$lang['Add_Friends'];
					$returnibox = iboxCheckAddtofriends($label);
					echo $returnibox;
					?>
							<br />
							<?php
				} else {
					?>
							<a style="cursor: pointer;" onClick="exchangeuser('<? echo $profile_exchange;  ?>',<? echo $articleid;?>,<? echo $postval['subid'];?>,'<? echo $lang['Request_sent'];?>', '<?php echo $postval['subid']; ?>','<?php echo $postval['postid']; ?>','<? echo $subscription_id?>','<?=$postkey?>')"><?php echo $lang['Add_Friends'];?></a><br />
						<?php
				}
			} ?>
				</td>
					</tr>
					<tr>
					<td class="comment_sub_heading">
							  <?php
							  if($profile_exchange){
				$sendMessagecheck=is_msg_allowed($postval['subid'],$subscription_id);
				if($sendMessagecheck=='true'){
					?>
							<a href="<?=$page_config['compose']['URL']; ?>?&from=<?=$from;?>&a=<?=$articleid; ?>&to=<?=$postval['subid'];?> ">Send <?php echo $posterFname;?> a message</a><br />
									<?php  }
			}
			?>
					</td>
					</tr>
					<tr>
					<td class="comment_sub_heading">
					 <?php
					 //***$searchattr['author_id']=$postval['subid'];
					 $discussionattr['q']=$threadid;
					 $searchattr['userid']=$postval['subid'];
					 $searchattr['type']=4;
					 $label='View Exchanges';
					 $targeturl = $HTPFX.$HTHOST.'/community/search/'.$postval['subid'];
					 iboxcheckArticle('search',$searchattr,$label,$sid,$eid,$targeturl);
			?></a>
					</td>
					</tr>
					</table>
					</div>
					</td>
					<td rowspan="2" width="15" valign="top"></td>
				<td rowspan="2" valign="top">
									   <table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>

				<td colspan="2" align="right"><div class="report_abuse" id="sendabuse<?=$postkey?>"></div></td>
				</tr>
				<tr>
				<td class="comment_date" width="400px"><?php echo $datevalue; ?></td>
				<td class="comment_date" nowrap="nowrap">
				<?php
				$ReportAbuse=array();
			$ReportAbuse=$objaddrequest->CheckReportAbuse($subscription_id);
			$strAttribute="q=".$threadid;

			if($ReportAbuse==NULL){
				$ReportAbuse=array();
			}
			if(($subscription_id > 0) && ($postval['subid']!=$subscription_id) && (!in_array($commentid,$ReportAbuse)))
			{
				?>
						<span><a style='cursor: pointer; font-size:10px; color:#0486B7; font:Arial, Helvetica, sans-serif;' onclick="preHttpRequestURL('<?=$profile_exchange;?>','<?=$subscription_id?>','<?=$commentid?>','<?=$article[id];?>','<?=$postval[subid];?>','single_discussion', '<?=$strAttribute;?>','<?=$postkey?>')">Report Abuse</a></span>

					<?php
			}
			?>
					</td>
					</tr>
					<tr>
					<td valign="top"><?=$postval['teasure'];?>
										<br />
										<?php
					// Read More Links
										$discussionattr['cmt']=$commentid;
					//$objLink->checkiBox('single_discussion',$discussionattr);
			$targeturlread=$HTPFX.$HTHOST."/community/discussion/discussion/".$threadid;
			$discussionattr['q']=$threadid;
			$label='<span class="comment_read_more">Read More</span>';
			iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturlread);
					//echo $postval['name']."</a>";
			?>
					<br />
					</td>
					</tr>

					</table>


					</td>
					</tr>

					</table>




					</div>
					<? }
	?>
			<!-- </div> -->
			<!--Main comment box end here-->
			<?
}
function discussSlideshowArticle($subscription_id,$url,$rand,$threadid,$from,$slideid)
{
	global $page_config;
	$sid=$_SESSION['SID'];
	$eid=$_SESSION['SID'];
	?>
			<!--Post article section start from here-->
			<table width="650" border="0" align="left" cellpadding="5" cellspacing="0">
		<tr><td colspan="2" align="left" ><div id="<?=$rand?>" ></div></td></tr>
		<tr class="common_box">
				  <td width="63%" height="35" align="left">
		<?
		// discuss this slideshow and more on the mv exchange
		if ($from=='mvtv'){$labelOn='video';}elseif ($from=='slideshow'){$labelOn='slideshow';}else{$labelOn='article';}
	$discussionattr['q']=$threadid;
	$discussionattr['slideid']=$slideid;
	$label='<h5>discuss this '.$labelOn.' and more on the mv exchange</h5>';
	$page_config['single_discussion']['URL']."?".'q='.$threadid;
	$targeturl="/community/discussion/discussion/".$threadid;
	iboxcheckArticle('single_discussion',$discussionattr,$label,$sid,$eid,$targeturl);
	?>
	</td>
	<?
	if(!$_SESSION['EID'])
	{
		?>
	<td width="37%" align="left"><a href="<?=$targeturl?>"><img src="<?=$IMG_SERVER?>/images/redesign/sign_up_button.gif" width="192" height="19" border="0" /></a></td>
	<?
	}
	?>
	</tr>
	</table>
	<!--Post article section end here-->
	<?
}
function displayTremorPlayer($pageName,$tremorplayer=0)
{
	 global $D_R;
	 include_once($D_R.'/lib/videos/_data_lib.php');
	 $playlist=getVideoPlaylist();

	 $isameritrade = $playlist[0][ameritrade];
	 $videofilepath = $playlist[0][videofile];
	 $stillfilepath =  $playlist[0][stillfile];
	 $videoid=$playlist[0]['id'];
	 $videotitle = substr($playlist[0][title],0,30);
	 echo '<div class="watch_video">';
	 if($pageName == 'home')
	 {
		 echo '<div class="FeatureVideotitle">&nbsp;</div>';
	 }
	 showtremorplayer($videofilepath,$stillfilepath,"False",TRUE,TRUE,$videoid);
	 if($pageName == 'home')
	 {
		 echo '<div class="FeatureVideobottom">&nbsp;</div>';

	 }

	 echo '</div>';
}

function renderContextWebTracking($pageName){
global $HTPFX;
switch($pageName){
	case 'options':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL2" width="1" height="1">';
		break;
	case 'stockideas':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL3" width="1" height="1"> ';
		break;
	case 'advancetrading':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL4" width="1" height="1"> ';
		break;
	case 'economy':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL5" width="1" height="1"> ';
		break;
	case 'familyfinance':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL6" width="1" height="1"> ';
		break;
	case 'fiscalhygiene':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL7" width="1" height="1"> ';
		break;
	case 'mindyourbusiness':
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL8" width="1" height="1"> ';
		break;
	default:
		echo '<img src="'.$HTPFX.'bh.contextweb.com/bh/set.aspx?action=add&advid=1271&token=MNVL1" width="1" height="1"> ';
	}
}
 function setSessionReferal($frm, $httpRef){
	session_start();
	if(strtolower($frm) == 'google' || preg_match('/google.com/',$httpRef)){
		 $_SESSION['refererSourceId'] ='www.google.com';
	}
	if(strtolower($frm) == 'yahoo' || preg_match('/yahoo.com/',$httpRef)){
			 $_SESSION['refererSourceId'] ='www.yahoo.com';
	}
	if(strtolower($frm) == 'bing' || preg_match('/bing.com/',$httpRef)){
				 $_SESSION['refererSourceId'] ='www.bing.com';
	}

 }

 function doclix_ads() {
  $tag='<script type="text/javascript">
                         doclix_pid = 16286;
                         doclix_cid = 408;
                         doclix_w = 600;
                         doclix_h = 145;
               </script>
               <script type="text/javascript" src="http://ads.doclix.com/adserver/serve/js/doclix_synd_ifrm.js" charset="utf-8"></script>';
 	return $tag;
  }

  function displayNavigationold(){
  	$objCacheHeader = new Cache();
	$getHeaderNavCache=$objCacheHeader->getHeaderMainNavigationCache();
  	?>
  	<div id="main-navigation-container">

	<ul id="main-navigation">
		<li id="businessmarket" class="nav-left"><a target="_parent" href="/section/businessmarket/">Business & Markets</a></li>
		<li id="lifemoney"><a target="_parent" href="/section/lifemoney/">Life & Money</a></li>
		<li id="audiovideo"><a target="_parent" href="/audiovideo/">Video</a></li>
		<li id="exchange_home"><a target="_parent" href="/community/home.htm">Community</a></li>
		<li id="subscription_product" class="green-nav-item nav-right"><a target="_parent" href="/subscription/">Subscriptions & Products</a></li>
	</ul>

	<div style="clear:both;"></div>

	<div id="sub-navigation-container">


	<ul id="sub-businessmarket" class="sub-nav">
		<li id="stockideas"><a target="_parent" href="/subsection/stockideas/">Stock Ideas</a></li>
		<li id="options"><a target="_parent" href="/subsection/options/">Options</a></li>
		<li id="economy"><a target="_parent" href="/subsection/economy/">Economy</a></li>
		<li id="advancetrading"><a target="_parent" href="/subsection/advancetrading/">Advanced Training</a></li>
		<li id="fivethings"><a target="_parent" href="/subsection/fivethings/">5 Things</a></li>
		<li id="randomthoughts"><a target="_parent" href="/subsection/randomthoughts/">Random Thoughts</a></li>
		<li id="twowaystoplay"><a target="_parent" href="/subsection/twowaystoplay/">2 Ways 2 Play</a></li>
		<li id="tradenow"><a target="_parent" href="/articles/tradenow.htm">Brokerage Center</a></li>
	</ul>

	<ul id="sub-lifemoney" class="sub-nav">
		<li id="familyfinance"><a target="_parent" href="/section/familyfinance/">Family & Finance</a></li>
		<li id="features"><a target="_parent" href="/subsection/features/">Features</a></li>
		<li id="mindyourbusiness"><a target="_parent" href="/subsection/mindyourbusiness/">Mind Your Business</a></li>
		<li id="fiscalhygiene"><a target="_parent" href="/subsection/fiscalhygiene/">Fiscal Hygiene</a></li>
		<li id="umv"><a target="_parent" href="/subsection/umv/">UMV</a></li>
		<li id="whyshouldicare"><a target="_parent" href="/subsection/whyshouldicare/">Why Should I Care?</a></li>
		<li id="economicsnapshot"><a target="_parent" href="/subsection/economicsnapshot/">Economic Snapshot</a></li>
	</ul>


	<ul id="sub-audiovideo" class="sub-nav">
		<li id="hoofyboo"><a target="_parent" href="/audiovideo/hoofyboo/">Hoofy & Boo</a></li>
		<li id="mackeunmuzzled"><a target="_parent" href="/audiovideo/mackeunmuzzled/">Macke Unmuzzled</a></li>
		<li id="kevindepew"><a target="_parent" href="/audiovideo/kevindepew/">Kevin Depew</a></li>
		<li id="toddharrisontv"><a target="_parent" href="/audiovideo/toddharrisontv/">Todd Harrison TV</a></li>
		<li id="specials"><a target="_parent" href="/audiovideo/specials/">Specials</a></li>
	</ul>

	<ul id="sub-exchange_home" class="sub-nav">
		<li id="active_discussion"><a target="_parent" href="/community/active-discussion.htm">Most Active Discussions</a></li>
		<li><a target="_parent" href="/community/home.htm">Your Updates</a></li>
		<li><a target="_parent" href="/community/friends.htm">Your Friends</a></li>
		<li><a target="_parent" href="/community/profile/">Your Profile</a></li>
	</ul>

	<ul id="sub-subscription_product" class="sub-nav">
		<li id="jackhome"><a target="_parent" href="/laveryinsight/index.htm">Lavery Insight</a></li>
		<li id="bmtp_home"><a target="_parent" href="/bmtp/index.htm">Bull Market Timer</a></li>
		<li id="sshome"><a target="_parent" href="/optionsmith/">Optionsmith</a></li>
		<li id="cooperhome"><a target="_parent" href="/cooper/">Cooper's Market Report</a></li>
		<li id="etf"><a target="_parent" href="/etf/index.htm">ETF Investor</a></li>
		<li id="qphome"><a target="_parent" href="/qp/">Flexfolio</a></li>
		<li id="buzzbanter"><a target="_parent" href="/buzzbanter/">Buzz & Banter</a></li>
		<!--li id="shops"><a target="_parent" href="/shops/">Merchandise</a></li-->
	</ul>

</div> <!-- end sub navigation container -->
<div style="clear:both;"></div>
</div> <!-- end main navigation container -->
  	<?

  }


function displayNavigation($pageid = NULL,$getHeaderMenu,$sectionId){
   if($sectionId){
 		 $getSectionPageId=getParentSectionId($sectionId);
		 $pageid=$getSectionPageId['page_id'];
   }

   $menu = $getHeaderMenu->menu;
   global $arMenuDetail,$HTPFX,$HTHOST,$HTPFXNSSL,$pageUrlArr;
   $arMenuDetail =  array();
   if(count($menu)>0)
   {
		foreach($menu as $menuRow)
		{
			$arMenuDetail;

			if(substr($menuRow['alias'],0,1) == '/')
			{
				$stURLPrefix = $HTPFXNSSL.$HTHOST;
			}
			else
			{
				$stURLPrefix = $HTPFXNSSL;
			}
			if($menuRow['parent_id'] == 0)
			{
				$arMenuDetail[$menuRow['id']]['title'] = $menuRow['title'];
				$arMenuDetail[$menuRow['id']]['url'] = $stURLPrefix.$menuRow['alias'];
				$arMenuDetail[$menuRow['id']]['page_id'] = $menuRow['page_id'];
				if($menuRow['page_id'] == $pageid) // If selected page id is menu's page id
				{
					$arMenuDetail[$menuRow['id']]['selected'] = 1;
				}
			}

			else
			{
				if($menuRow['parent_id'] == 5)
				{
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['title'] = $menuRow['title'];
					//$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['url'] = $stURLPrefix.$menuRow['alias'];
					$session_key = $pageUrlArr[$menuRow['alias']]['key'];

					if ($_SESSION['SID']!=''){
						if($menuRow['alias']=="/book-store")
						{
							$url=$stURLPrefix.$menuRow['alias'];
						}
						else
						{
							if($_SESSION[$session_key])
							{
								if($menuRow['alias']!="/buzzbanter/" && $menuRow['alias']!="/housing-market-report/" && $menuRow['alias']!="/ad-free/" )
								{
									$url=$stURLPrefix.$menuRow['alias'];
								}
								else{
									$url = $pageUrlArr[$menuRow['alias']]['url'].'&email='.$_SESSION['EMAIL'].'&first_name='.$_SESSION['nameFirst'];
								}

							}
							else
							{
								$getVariables = "&email=".$_SESSION['EMAIL']."&first_name=".$_SESSION['nameFirst'];
								$url = $pageUrlArr[$menuRow['alias']]['url'].$getVariables;
							}
						}
					} else
					{
						if($menuRow['alias']!="/book-store")
						{
							$url = $pageUrlArr[$menuRow['alias']]['url'];
						}
						else
						{
							$url = $menuRow['alias'];
						}
					}
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['url'] = $url;
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['page_id'] = $menuRow['page_id'];
					if($menuRow['page_id'] == $pageid) // If selected page id is sub menu's page id
					{
						// Parent Menu of selected submenu should also be selected
						$arMenuDetail[$menuRow['parent_id']]['selected'] = 1;
						$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['selected'] = 1;
					}
				}
				else
				{
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['title'] = $menuRow['title'];
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['url'] = $stURLPrefix.$menuRow['alias'];
					$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['page_id'] = $menuRow['page_id'];
					if($menuRow['page_id'] == $pageid) // If selected page id is sub menu's page id
					{
						// Parent Menu of selected submenu should also be selected
						$arMenuDetail[$menuRow['parent_id']]['selected'] = 1;
						$arMenuDetail[$menuRow['parent_id']]['sub_menu'][$menuRow['id']]['selected'] = 1;
					}
				}
			}
		}
		//echo "<pre>";
		//print_r($arMenuDetail);

		$div = displayNavigationMenu($pageid,$arMenuDetail);
	}else{
	 	 $div = 'Sorry no menu items found';
	}
	return $div ;
}

 function displayNavigationMenu($pageid,$menuitem){
  global $pageName,$IMG_SERVER,$HTPFXNSSL,$HTHOST,$HTPFX;
	$str="";
	$str.='<ul id="nav">';
	foreach ($menuitem as $key=>$item)
	{
		$submenupageid="";
		if(is_array($item[sub_menu])){
			foreach($item[sub_menu] as $value){
			  if($pageid==$value['page_id']){
					$submenupageid=1;
				}
			}
		}
	if(($pageid==$item['page_id'])||($submenupageid=="1")){
			if($item['page_id'] == 54)
			{
			$classselect="downsubs";
			}
			else
			{
			$classselect="downselect";
			}

	}else{
		$classselect="";
	}

	   if($item['sub_menu']){
		$buzzLaunchUrl=buzzAppUrlEncryption();
			   $strClass = "";
			   $strWidth = "";
			   $mainClass="";
			   if($item['page_id'] == 54)
			   {
			   		$mainClass = "class='subscriptionnav'";
			   		$strClass = "class='mvp'";
					$strWidth = "style='width:242px;margin:0;left:-98px;'";
			   }

				$classsubmenu="sub";
				if(strtolower($item['page_id'])=="54")
				{
				$str.='<li '.$mainClass.'><a href="'.$item['url'].'" class="'.$classselect.' subs-mvp" style="padding-left:57px;"><div>'.$item['title'].'</div></a>';
				}
				 else
				 {
				 	$str.='<li><a href="'.$item['url'].'" class="'.$classselect.'"><div><img src="'.$IMG_SERVER.'/images/navigation/nav_arrow.png" style="height:10px; width:8px;" />'.$item['title'].'</div></a>';
				 }

			   $str.='<ul class="'.$classsubmenu.'" id="sub_menu" '.$strWidth.' style="border-right:2px solid #000;">';
	    foreach($item['sub_menu'] as $value)
			   {
					if(strtolower($value['title']) == "ad free minyanville" || strtolower($value['title']) == "minyanville book store" || strtolower($value['title'])=="buzz and banter" || strtolower($value['title'])=="tchir's fixed income report" || strtolower($value['title'])=="cooper's market report" || strtolower($value['title'])=="techstrat report" ||strtolower($value['title'])=="optionsmith" || strtolower($value['title'])=="the stock playbook" || strtolower($value['title'])=="housing market report" || strtolower($value['title'])=="elliott wave insider" || strtolower($value['title'])=="keene on options")
					{
						$str.='<li><a href="'.$value['url'].'" style="width:220px;padding-right:8px;" >'.ucwords($value['title']).'</a></li>';
					}
				elseif(strtolower($value['title']) =="launch buzz and banter"){
					//new buzz app url code start
					if(!$_SESSION['SID']){
						$str.='<li><a href="'.$HTPFX.$HTHOST.'/subscription/register/login.htm" style="width:220px;padding-right:8px;"  >'.ucwords($value['title']).'</a></li>';
					}elseif(!empty($_SESSION['Buzz'])){
						$str.='<li><a href="javascript:;" onclick="window.open(\''.$buzzLaunchUrl.'\',\'Banter\',\'width=455,height=708,resizable=yes,toolbar=no,scrollbars=no\'); banterWindow.focus();" style="width:220px;padding-right:8px;" >'.ucwords($value['title']).'</a></li>';
					}

					//new buzz app url code end

					}

					else
					{

						$str.='<li><a href="'.$value['url'].'" '.$strClass.' >'.ucwords($value['title']).'</a></li>';
					}
			   }
				$str.='</ul>';
				$str.='</li>';

	   }else{
	      $str.='<li><a href="'.$item['url'].'"  class="'.$classselect.'"><div>'.$item['title'].'</div></a>';
	   	 $str.='</li>';
	   }
    }
	 /*if($_SESSION['SID']){
	 	$str.='<li><a href="#" class="nav-mvp" style="padding-left:45px;"><div onClick="javascript:banterWindow=window.open(\''.$pfx.'/buzz/buzz.php\',\'Banter\',\'width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\');banterWindow.focus();">LAUNCH BUZZ</div></a></li>';
	 }else{
	   		$str.='<li class="top"><a href="'.$HTPFXNSSL.$HTHOST.'/buzzbanter" class="nav-mvp" style="padding-left:45px;"><div class="down">BUZZ & BANTER</div></a></li>';
	 }*/
	 $str.='</ul>';
  echo $str;
}


function displayLeaderboard($pageName){
   global $pageId,$cm8_ads_MediumRectangle,$cm8_ads_Leaderboard,$cm8_ads_Button_227x90,$productPages,$arrEduPages;
	 if(in_array($pageName,$productPages))
	 {
	 	// Do nothing
	 }
	 elseif($pageName=="ticker"){ ?>
       	<div class="header_ads"><!-- FINANCIALCONTENT ADCODE 1 --></div>
<?  } else{
		global $cm8_ads_Leaderboard;
	    $bannername=$cm8_ads_Leaderboard;
	    if(in_array($pageName,$arrEduPages)){ ?>
			<div class="eduHeaderAds"><? CM8_ShowAd($bannername); ?></div>
	   <?  }else{ ?>
			<div class="header_ads"><? CM8_ShowAd($bannername); ?></div>
<?		}
	 }
}


function displayQuickLinks(){
?>
<div id="quick-links">
<h3><img src="/newpages/images/homepage/hdr-quick-links.gif" alt="Quick Links"></h3>
        <ul>
       	<?
       		$cacheFooter= new Cache();
			$getFooterNavCache=$cacheFooter->getFooterQuickLinksCache();
			foreach($getFooterNavCache->arFooterLinks as $link){
				echo '<li><a href="'.$HTPFX.$HTHOST.$link['alias'].'">'.strtoupper($link['title']).'</a></li>';
			}
		?>
        </ul>
</div>
<?
}

function displayLocalGuide(){
?>
<div id="local-container">
<ul class="local-column">
    <li><a href="http://local.minyanville.com/Alabama-nAlabama.html" title="Alabama Local Guides"class="dmcontent_link">Alabama</a></li>
    <li><a href="http://local.minyanville.com/Alaska-nAlaska.html" title="Alaska Local Guides"class="dmcontent_link">Alaska</a></li>
    <li><a href="http://local.minyanville.com/Arizona-nArizona.html" title="Arizona Local Guides"class="dmcontent_link">Arizona</a></li>
    <li><a href="http://local.minyanville.com/Arkansas-nArkansas.html" title="Arkansas Local Guides"class="dmcontent_link">Arkansas</a></li>
    <li><a href="http://local.minyanville.com/California-nCalifornia.html" title="California Local Guides"class="dmcontent_link">California</a></li>
    <li><a href="http://local.minyanville.com/Colorado-nColorado.html" title="Colorado Local Guides"class="dmcontent_link">Colorado</a></li>
    <li><a href="http://local.minyanville.com/Connecticut-nConnecticut.html" title="Connecticut Local Guides"class="dmcontent_link">Connecticut</a></li>
    <li><a href="http://local.minyanville.com/DC-nDC.html" title="DC Local Guides"class="dmcontent_link">DC</a></li>
    <li><a href="http://local.minyanville.com/Delaware-nDelaware.html" title="Delaware Local Guides"class="dmcontent_link">Delaware</a></li>
    <li><a href="http://local.minyanville.com/Florida-nFlorida.html" title="Florida Local Guides"class="dmcontent_link">Florida</a></li>
    <li><a href="http://local.minyanville.com/Georgia-nGeorgia.html" title="Georgia Local Guides"class="dmcontent_link">Georgia</a></li>
    <li><a href="http://local.minyanville.com/Hawaii-nHawaii.html" title="Hawaii Local Guides"class="dmcontent_link">Hawaii</a></li>
    <li><a href="http://local.minyanville.com/Idaho-nIdaho.html" title="Idaho Local Guides"class="dmcontent_link">Idaho</a></li>
    <li><a href="http://local.minyanville.com/Illinois-nIllinois.html" title="Illinois Local Guides"class="dmcontent_link">Illinois</a></li>
    <li><a href="http://local.minyanville.com/Indiana-nIndiana.html" title="Indiana Local Guides"class="dmcontent_link">Indiana</a></li>
    <li><a href="http://local.minyanville.com/Iowa-nIowa.html" title="Iowa Local Guides"class="dmcontent_link">Iowa</a></li>
    <li><a href="http://local.minyanville.com/Kansas-nKansas.html" title="Kansas Local Guides"class="dmcontent_link">Kansas</a></li>
    <li><a href="http://local.minyanville.com/Kentucky-nKentucky.html" title="Kentucky Local Guides"class="dmcontent_link">Kentucky</a></li>
</ul>
<ul class="local-column">
    <li><a href="http://local.minyanville.com/Louisiana-nLouisiana.html" title="Louisiana Local Guides"class="dmcontent_link">Louisiana</a></li>
    <li><a href="http://local.minyanville.com/Maine-nMaine.html" title="Maine Local Guides"class="dmcontent_link">Maine</a></li>
    <li><a href="http://local.minyanville.com/Maryland-nMaryland.html" title="Maryland Local Guides"class="dmcontent_link">Maryland</a></li>
    <li><a href="http://local.minyanville.com/Massachusetts-nMassachusetts.html" title="Massachusetts Local Guides"class="dmcontent_link">Massachusetts</a></li>
    <li><a href="http://local.minyanville.com/Michigan-nMichigan.html" title="Michigan Local Guides"class="dmcontent_link">Michigan</a></li>
    <li><a href="http://local.minyanville.com/Minnesota-nMinnesota.html" title="Minnesota Local Guides"class="dmcontent_link">Minnesota</a></li>
    <li><a href="http://local.minyanville.com/Mississippi-nMississippi.html" title="Mississippi Local Guides"class="dmcontent_link">Mississippi</a></li>
    <li><a href="http://local.minyanville.com/Missouri-nMissouri.html" title="Missouri Local Guides"class="dmcontent_link">Missouri</a></li>
    <li><a href="http://local.minyanville.com/Montana-nMontana.html" title="Montana Local Guides"class="dmcontent_link">Montana</a></li>
    <li><a href="http://local.minyanville.com/Nebraska-nNebraska.html" title="Nebraska Local Guides"class="dmcontent_link">Nebraska</a></li>
    <li><a href="http://local.minyanville.com/Nevada-nNevada.html" title="Nevada Local Guides"class="dmcontent_link">Nevada</a></li>
    <li><a href="http://local.minyanville.com/New_Hampshire-nNew+Hampshire.html" title="New Hampshire Local Guides"class="dmcontent_link">New Hampshire</a></li>
    <li><a href="http://local.minyanville.com/New_Jersey-nNew+Jersey.html" title="New Jersey Local Guides"class="dmcontent_link">New Jersey</a></li>
    <li><a href="http://local.minyanville.com/New_Mexico-nNew+Mexico.html" title="New Mexico Local Guides"class="dmcontent_link">New Mexico</a></li>
    <li><a href="http://local.minyanville.com/New_York-nNew+York.html" title="New York Local Guides"class="dmcontent_link">New York</a></li>
    <li><a href="http://local.minyanville.com/North_Carolina-nNorth+Carolina.html" title="North Carolina Local Guides"class="dmcontent_link">North Carolina</a></li>
    <li><a href="http://local.minyanville.com/North_Dakota-nNorth+Dakota.html" title="North Dakota Local Guides"class="dmcontent_link">North Dakota</a></li>
</ul>
<ul class="local-column">
    <li><a href="http://local.minyanville.com/Ohio-nOhio.html" title="Ohio Local Guides"class="dmcontent_link">Ohio</a></li>
    <li><a href="http://local.minyanville.com/Oklahoma-nOklahoma.html" title="Oklahoma Local Guides"class="dmcontent_link">Oklahoma</a></li>
    <li><a href="http://local.minyanville.com/Oregon-nOregon.html" title="Oregon Local Guides"class="dmcontent_link">Oregon</a></li>
    <li><a href="http://local.minyanville.com/Pennsylvania-nPennsylvania.html" title="Pennsylvania Local Guides"class="dmcontent_link">Pennsylvania</a></li>
    <li><a href="http://local.minyanville.com/Rhode_Island-nRhode+Island.html" title="Rhode Island Local Guides"class="dmcontent_link">Rhode Island</a></li>
    <li><a href="http://local.minyanville.com/South_Carolina-nSouth+Carolina.html" title="South Carolina Local Guides"class="dmcontent_link">South Carolina</a></li>
    <li><a href="http://local.minyanville.com/South_Dakota-nSouth+Dakota.html" title="South Dakota Local Guides"class="dmcontent_link">South Dakota</a></li>
    <li><a href="http://local.minyanville.com/Tennessee-nTennessee.html" title="Tennesee Local Guides"class="dmcontent_link">Tennesee</a></li>
    <li><a href="http://local.minyanville.com/Texas-nTexas.html" title="Texas Local Guides"class="dmcontent_link">Texas</a></li>
    <li><a href="http://local.minyanville.com/Utah-nUtah.html" title="Utah Local Guides"class="dmcontent_link">Utah</a></li>
    <li><a href="http://local.minyanville.com/Vermont-nVermont.html" title="Vermont Local Guides"class="dmcontent_link">Vermont</a></li>
    <li><a href="http://local.minyanville.com/Virginia-nVirginia.html" title="Virginia Local Guides"class="dmcontent_link">Virginia</a></li>
    <li><a href="http://local.minyanville.com/Washington-nWashington.html" title="Washington Local Guides"class="dmcontent_link">Washington</a></li>
    <li><a href="http://local.minyanville.com/West_Virginia-nWest+Virginia.html" title="West Virginia Local Guides"class="dmcontent_link">West Virginia</a></li>
    <li><a href="http://local.minyanville.com/Wisconsin-nWisconsin.html" title="Wisconsin Local Guides"class="dmcontent_link">Wisconsin</a></li>
    <li><a href="http://local.minyanville.com/Wyoming-nWyoming.html" title="Wyoming Local Guides"class="dmcontent_link">Wyoming</a></li>
</ul>
<div style="clear:both;"></div>
<div id="see-all-local"><a href="http://local.minyanville.com/" title=" Local Guides"class="dmcontent_link">See All Local Guides</a></div><br>
</div>
<?
}

function displayProduct(){
global $IMG_SERVER,$HTHOST,$HTPFX;

if($_SESSION['SID']){
	$emailtext=$_SESSION['email'];
}else{
	$emailtext="Enter email Address";
}
?>

<div class="article-right-module-header">
	<img src="/newpages/images/hdr-subscription-center.gif"><br>
</div>
<div class="display-product-module-shadow">
<div class="display_product_body">
	<div id="display_product_top">
		<div class="display_product_heading">ARTICLE ALERTS</div>
			<input  name="emailAddress" id="emailAddress" size="30" value="<?=$emailtext;?>" class="article_input_box" align="left" type="text" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);">
	   <a id="navlink_go" class="display_product_go_img" href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/iboxindex.htm?from=emailAlert" rel="ibox;height=495;width=532;targeturl=/subscription/register/controlPanel.htm;"><img src="<?=$IMG_SERVER?>/images/navigation/go-btn.jpg" /></a>

    <div class="display_product_middle" >
		<div class="display_product_heading">SUBSCRIBE</div>
			<a href="<?=$HTPFX.$HTHOST;?>/rss/"><img src="<?=$IMG_SERVER?>/images/navigation/rss_logo.gif"/></a>&nbsp; &nbsp; &nbsp;<a href="http://www.facebook.com/MinyanvilleMedia" target="_blank"><img src="<?=$IMG_SERVER?>/images/navigation/facebook-logo-f.gif"/></a>&nbsp; &nbsp; &nbsp; <a href="http://twitter.com/minyanville" target="_blank"><img src="<?=$IMG_SERVER?>/images/navigation/twitter.gif" /></a>
	</div>

     <div>
     <div class="display_product_heading">PREMIUM PRODUCTS</div>
  		<div><a href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		<div id="display_product_bottom">  <a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter">Buzz & Banter</a> </div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter">Real-time trading ideas from Todd Harrison & over 30 other professional traders</a></div>
		<hr class="display_product_hr">
    	</div>

		<div><a href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>

			<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook">The Stock Playbook</a></div>
				<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook">Dave Dispennette shares his trading ideas in nightly videos & access to his portfolio which was +59% in '09.</a></div>
				<hr class="display_product_hr">
		</div>
    	<div><a href="<?=$HTPFX.$HTHOST?>/etf/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ETFLogo&utm_campaign=ETFInvestor"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		<div id="display_product_bottom">  <a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/etf/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ETFLogo&utm_campaign=ETFInvestor">Grail ETF & Equity Investor </a> </div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/etf/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ETFLogo&utm_campaign=ETFInvestor">Identify early trend changes in ETFs and ride them to profits.</a></div>
		<hr class="display_product_hr">
       	</div>


		<div><a href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
	<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport">Jeff Cooper's Market Report</a></div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport">Market analysis with day & swing trading setups from expert trader Jeff Cooper.</a></div>
		<hr class="display_product_hr">
    </div>
	<div><a href="<?=$HTPFX.$HTHOST?>/qp/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>

  	<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/qp/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor">FlexFolio Trade Alerts</a></div>
    	<div class="display_product_bottom_b"><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/qp/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor">An equity portfolio traded by Quint Tatro. Email alerts with every trade.</a></div>
		<hr class="display_product_hr">
    </div>
   		 <div><a href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		 <div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith">OptionSmith by Steve Smith</a> </div>
    	 <div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith">Steve Smith's options portfolio, trade alerts, strategies and analysis.</a></div>
    	 </div>
	</div>
	</div>
</div>
</div>
<?
}

function displayProduct_whitebg(){
global $IMG_SERVER,$HTHOST,$HTPFX;

if($_SESSION['SID']){
	$emailtext=$_SESSION['email'];
}else{
	$emailtext="Enter email Address";
}
?>

<div class="article-right-module-header">
	<img src="<?=$IMG_SERVER?>/images/hdr-subscription-center-white.gif"><br>
</div>
<div class="display-product-module-shadow">
<div class="display_product_body">
	<div id="display_product_top">
		<div class="display_product_heading">ARTICLE ALERTS</div>
			<input  name="emailAddress" id="emailAddress" size="30" value="<?=$emailtext;?>" class="article_input_box" align="left" type="text" onfocus="javascript:onFocusRemoveText(this);" onBlur="javascript:onBlurrGetText(this);">
	   <a id="navlink_go" class="display_product_go_img" href="<?=$HTPFXSSL.$HTHOST;?>/subscription/register/controlPanel.htm"><img src="<?=$IMG_SERVER?>/images/navigation/go-btn.jpg" /></a>

    <div class="display_product_middle">
		<div class="display_product_heading">SUBSCRIBE</div>
			<a href="<?=$HTPFX.$HTHOST;?>/rss/"><img src="<?=$IMG_SERVER?>/images/navigation/rss_logo.gif"/></a>&nbsp; &nbsp; &nbsp;<a href="http://www.facebook.com/MinyanvilleMedia" target="_blank"><img src="<?=$IMG_SERVER?>/images/navigation/facebook-logo-f.gif"/></a>&nbsp; &nbsp; &nbsp; <a href="http://twitter.com/minyanville" target="_blank"><img src="<?=$IMG_SERVER?>/images/navigation/twitter.gif" /></a>
	</div>

     <div>
     <div class="display_product_heading">PREMIUM PRODUCTS</div>
  		<div><a href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		<div id="display_product_bottom">  <a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter">Buzz & Banter</a> </div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/buzzbanter/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=BuzzLogo&utm_campaign=BuzzBanter">Real-time trading ideas from Todd Harrison & over 30 other professional traders</a></div>
		<hr class="display_product_hr">
    	</div>

		<div><a href="<?=$HTPFX.$HTHOST?>/housing-market-report/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=HousingMarketReportLogo&utm_campaign=HousingMarketReport"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/bttnlearnmore.gif" /></a>
  		 <div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/housing-market-report/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=HousingMarketReportLogo&utm_campaign=HousingMarketReport">Housing Market Report</a> </div>
    	 <div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/housing-market-report/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=HousingMarketReportLogo&utm_campaign=HousingMarketReport">Actionable housing data, analysis, and specific advise from Keith Jurow you won't find anywhere else to help you make smarter real estate investment decisions.</a></div>
		 <hr class="display_product_hr">
    	 </div>

 		<div><a href="<?=$HTPFX.$HTHOST?>/techstrat/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TechStratLogo&utm_campaign=TechStrat"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		 <div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/techstrat/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TechStratLogo&utm_campaign=TechStrat">TechStrat Report by Sean Udall</a> </div>
    	 <div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/techstrat/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TechStratLogo&utm_campaign=TechStrat">Trades, ideas, analysis and research archives focusing on technology sector stocks. </a></div>
		 <hr class="display_product_hr">
    	 </div>

		<div><a href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>

			<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook">The Stock Playbook</a></div>
				<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/the-stock-playbook/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=TheStockPlaybookLogo&utm_campaign=TheStockPlaybook">Dave Dispennette shares his trading ideas in nightly videos & access to his portfolio which was +59% in '09.</a></div>
				<hr class="display_product_hr">
		</div>
    	<div><a href="https://www.mptrader.com/cgi-bin/fbuser?ref=mvp"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		<div id="display_product_bottom">  <a class="display_product_a" href="https://www.mptrader.com/cgi-bin/fbuser?ref=mvp">MPTrader ETF & Stock Diary</a> </div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="https://www.mptrader.com/cgi-bin/fbuser?ref=mvp">ETF and stock trading commentary and technical analysis throughout the trading day from veteran trader Mike Paulenoff. </a></div>
		<hr class="display_product_hr">
       	</div>


		<div><a href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
	<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport">Jeff Cooper's Market Report</a></div>
    	<div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/cooper/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=CooperLogo&utm_campaign=DailyMarketReport">Market analysis with day & swing trading setups from expert trader Jeff Cooper.</a></div>
		<hr class="display_product_hr">
    </div>
	<div><a href="<?=$HTPFX.$HTHOST?>/active-investor/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>

  	<div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/active-investor/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor">Active Investor</a></div>
    	<div class="display_product_bottom_b"><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/active-investor/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=ActiveInvestorLogo&utm_campaign=ActiveInvestor">A portfolio with trade alerts looking for growth stocks for intermediate to long-term active investors.</a></div>
		<hr class="display_product_hr">
    </div>
   		 <div><a href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/free_trail_btn.jpg" /></a>
  		 <div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith">OptionSmith by Steve Smith</a> </div>
    	 <div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/optionsmith/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=OptionSmithLogo&utm_campaign=OptionSmith">Steve Smith's options portfolio, trade alerts, strategies and analysis.</a></div>
		 <hr class="display_product_hr">
    	 </div>
		 <div><a href="<?=$HTPFX.$HTHOST?>/ad-free/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=AdsFreeLogo&utm_campaign=AdsFree"><img class="display_product_trail_img" src="<?=$IMG_SERVER?>/images/navigation/buy_now_btn.jpg" /></a>
  		 <div id="display_product_bottom"><a class="display_product_a" href="<?=$HTPFX.$HTHOST?>/ad-free/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=AdsFreeLogo&utm_campaign=AdsFree">Ads Free Minyanville</a> </div>
    	 <div class="display_product_bottom_b" ><a class="display_product_bottom_b" href="<?=$HTPFX.$HTHOST?>/ad-free/?utm_source=HomePageSubProducts&utm_medium=Logo&utm_content=AdsFreeLogo&utm_campaign=AdsFree">Read Minyanville with no advertising. </a></div>
    	 </div>
	</div>
	</div>
</div>
</div>
<?
}

function showBreadCrum($breadcrum,$articleSubSection='')
{

	global $IMG_SERVER,$HTHOST,$HTPFX,$HTPFXNSSL;
	$temparray[]=$breadcrum[0];
	unset($breadcrum[0]);
		$breadcrum=array_reverse($breadcrum);
		$breadcrum=array_merge($temparray,$breadcrum);
			  ?>
<div class="section_page" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"  >
<?
		foreach($breadcrum as $key=>$link){
			if(substr($link['alias'],0,1) == '/')
			{
				$stURLPrefix = $HTPFXNSSL.$HTHOST;
			}
			else
			{
				$stURLPrefix = $HTPFXNSSL;
			}
			if($key==count($breadcrum)-1){
		?>
		<a class="section_links" href="<?=$stURLPrefix.$link['alias']?>"  itemprop="url"  ><span itemprop="title"><?=ucwords($link['title']);?></span></a>
				<?

			}else{
		?>
		<a class="section_links" href="<?=$stURLPrefix.$link['alias']?>"  itemprop="url" ><span itemprop="title"><?=ucwords($link['title']);?></span></a> >
		<?
	}
			}
?>
</div>
	<?
	}

function displayChartBuzzImages($buzzid){
	$qry="select id,original_url,thumb_url from item_charts where item_id='".$buzzid."' and item_type='2'";
	$result=exec_query($qry);
	if($result){
	  $str="<table cellspacing='0' cellpadding='0'>";
	  foreach($result as $row){
		  $str.='<tr><td colspan="2"><a href="'.$row['original_url'].'" ><img src="'.$row['thumb_url'].'" /></a></td></tr>'.'<br>';
		  $str.='<tr><td colspan="2"><a href="'.$row['original_url'].'" >Click here to enlarge</a></td></tr>'.'<br>';
	  }
	  $str.='</table>';
	}
	return $str;
}

function displayCustomHeader($pageName){
		global $IMG_SERVER;
		?>
		<div class="header_product_ads"><?
		echo "<img width='728' heigh='90' alt='The Stock Plabook Header' src='".$IMG_SERVER."/images/tsp/tspLogoHeader.jpg'>";
		?>
		</div>
		<?
}

function sponsoredLinksIndexPage(){
echo '<script language="javascript">CM8ShowAd("Sponsored_205x375")</script> ';
}


function buzzAppUrlEncryption(){
 global $D_R,$buzzAppEncryptionKey,$buzzAppLaunchUrl;
 include_once($D_R."/lib/config/_buzzapp_config.php");
 $userID=$_SESSION['SID'];
 $data ="userid=".$userID."&usertimestamp=".gmdate('YmdHis');
 $encrypted_data = mcrypt_ecb(MCRYPT_BLOWFISH,$buzzAppEncryptionKey, $data, MCRYPT_ENCRYPT);
 $encryptedData=base64_encode($encrypted_data);
 $launchUrl=$buzzAppLaunchUrl.'?'.$encryptedData;
 // echo "<br>".$launchUrl;
 if(!empty($launchUrl)){
	return $launchUrl;
 }

}

?>
