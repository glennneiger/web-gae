<?php
	function showContentDesign($q,$contrib_id,$mo,$day,$year,$search,$showtabs,$object_type,$offset,$p,$subscription_id,$searchmsg,$title,$searchArchive){
			global $D_R;
			include_once($D_R."/lib/_content_data_lib.php");
			 $contentobj=new Content();
			 $objThread = new Thread();
			 # friends class object
			 $objFriends=new friends();
			 # get user's friends
			 $friends=$objFriends->get_friend_list($subscription_id,'','','');
			 global $contentcount,$maximagewidthvideo,$maximageheightvideo,$page_config;
	//		if(($q) || is_numeric($contrib_id) || ($mo) || ($day) || ($year) || ($title)){
				 $getresult=$contentobj->searchContent($q,$contrib_id,$mo,$day,$year,$object_type,$offset,$title,$searchArchive);
				 $getcount=$contentobj->searchContentCount($q,$contrib_id,$mo,$day,$year,$object_type,$offset,$title,$searchArchive);
				 $result=$getresult;
				 $numrows=$getcount['count'];
				 $DATE_STR="D M jS, Y";
	//		}
			if($offset==0 && ( strripos($q,"rss") !== false || strripos($title,"rss") !== false )) // special case for RSS
			{
			?>
                <div class="result_text_main">
                	<div style="float:left"><table><tr><td valign="top"><img src="<?=$IMG_SERVER;?>/images/mv_rss_icon.jpg" /></td>
                                <td><h1><a href="/service/rss.htm">Minyanville RSS Feeds</a></h1>
                               <div class="search_common_content">Subscribe to Minyanville's RSS (Really Simple Syndication) feeds to get news delivered directly to your desktop!</div></td></tr></table></div>
                </div>
            <?
				$searchmsg = "";
			}
            if($result){
				if(is_array($result)){
					foreach($result as $getvalue) {
							 $getresult=$contentobj->getMetaSearch($getvalue['object_id'],$getvalue['object_type']);
							 if($getvalue['object_type']=='2'){
							 	$currentDate = date('Y-m-d g:i a');
								$buzzDate = $getresult['pubDate'];
								$dateDiff = date_difftime($buzzDate,$currentDate);
								$dateDiffVal = intval($dateDiff['d']);
								if($dateDiffVal>=365){
								 	$buzzTitle=mswordReplaceSpecialChars($getresult['title']);
								 	$prodUrl=$contentobj->getFirstFiveWords($buzzTitle);
								 	$urlPubDate = date("n/j/Y",$getresult['date']);
								 	$getresult['url']=$HTPFX.$HTHOST."/buzz/buzzalert/".$prodUrl."/".$urlPubDate."/id/".$getvalue['object_id'];
								}
							 }
							 $objLogo=$contentobj->getObjectLogo($getvalue['object_id'],$getvalue['object_type']);
							if($getvalue['object_type']=="11"){
								//image code//
							/*	$imgproperties=getImageSize($getresult['stillfile']);
								$imgwidth=$imgproperties[0];
								$imgheight=$imgproperties[1];
								if(($imgwidth>$maximagewidthvideo)||($imgheight>$maximageheightvideo)){
									if($imgwidth>$maximagewidthvideo){
										$imgwidth=$maximagewidthvideo;
									}
									if($imgheight>$maximageheightvideo){
										$imgheight=$maximageheightvideo;
									}
								} */
							//image code //
							if($getresult['fox']){
								$mvtvauthor="HOOFY & BOO'S NEWS & VIEWS";
							}else{
								$mvtvauthor="WORLD IN REVIEW";
							}
							?>
								<div class="result_video_main">
									<div class="result_video_main_img"><img src="<?=$getresult['thumbfile'];?>" width="88px" height="66px"/></div>
									<div class="result_search_viedo_right">
										<div class="quint_date"><?=date($DATE_STR,$getresult['date']);?></div>
											<h1><img src="<?=$IMG_SERVER?>/images/video_logo.jpg" align="top" /><a href="<?=$getresult['url'];?>"><?=mswordReplaceSpecialChars($getresult['title']);?></h1></a>
											<h3><?=$mvtvauthor;?></h3>
									</div><br /><br />
									<div class="search_common_content"><?=html_entity_decode(mswordReplaceSpecialChars($getresult['description']));?></div>
								</div>

							<?php }elseif($getvalue['object_type']=="7"){
									$getresult=$contentobj->getProfileSearch($getvalue['object_id'],$getvalue['object_type'],$q);
									$getattribute=$contentobj->getAttributeProfile($getresult['id'],$q);

									?>
								      <div class="search_result_box">
											<table width="100%" border="0" cellpadding="0" cellspacing="">
											  <tr>
												<td align="left"><span class="search_head"><img src="<?=$IMG_SERVER;?>/images/community_images/friends.gif" /><a href="<?=$page_config[profile][URL]?>?userid=<?=$getresult['id'];?>"><?=ucwords(strtolower($getresult['author']))?></a></span></td>
												<?php
								if($getresult[id]!=$subscription_id && !$objThread->findKeyValuePair($friends,'subid',$getresult[id],false)){
								$rand=rand();
								$urlRequest=$HTPFX.$HTHOST."/community/Save.php";
								$urlRequest.="?Ptype=request";
								$urlRequest.="&sender_id=".$subscription_id;
								$urlRequest.="&receiver_id=".$getresult[id];
								?>
								<div align="right" id="<?=$rand?>"></div>
								<td width="12%" align="right" class="search_link"><a style='cursor:pointer' onclick="Javascript:preHttpRequest('<?=$rand?>','<?=$urlRequest?>');" style='padding-right:25px;'>Add to Friends</a></td>
								<?  } ?>
								</tr>

									<?php
									 if(is_msg_allowed($getresult[id],$subscription_id)=='true'){
										$compose=$page_config[compose][URL];
										$compose.="?from=search.htm";
										$compose.="&to=$getresult[id]";
									?>
									<tr>
									<?php
									//htmlprint_r($getattribute);
									if(isset($getattribute)){
										foreach($getattribute['match'] as $key=>$val){?>
										<tr>
										<td width="60%"  class="search_attribute_profile" colspan="0" align="left"><?=$val[attribute].':'.$val[value]?></td>
										<? if($key=="0"){ ?>
										<td  class="search_link" colspan="0" align="right"><a href="<?=$compose?>">Send a message</a></td>	                                 <? } ?>
										</tr>
										<?php } }else { ?>
										<tr>
											<td  class="search_link" colspan="0" align="right"><a href="<?=$compose?>">Send a message</a></td>                              </tr>
									<? }
									} ?>

											</table>
									</div>


								  <?php } else {
									if(!empty($getresult))
								  {   ?>

							<div class="result_text_main">
								<div style="float:left"><table><tr><td valign="top"><img src="<?=$objLogo;?>" /></td>
                                <td><h1><a href="<?=$getresult['url'];?>"><?=mswordReplaceSpecialChars($getresult['title']);?></a></h1>
                                <div class="quint_date"><?=date($DATE_STR,$getresult['date']);?></div>

								<h3><a href="<?=$HTPFX.$HTHOST?>/gazette/bios.htm?bio=<?=$getresult['author_id'];?>"><?=$getresult['author']?></a></h3>
								<div class="search_common_content"><?=html_entity_decode(mswordReplaceSpecialChars($getresult['description']));?></div></td></tr></table></div>
							</div>
						 <?php }  }
					  }
						}
				archivePagination($q,$contrib_id,$mo,$day,$year,$search,$showtabs,$object_type,$offset,$numrows,$contentcount,$p,$title,$searchArchive);
			}else{ ?>
				<div class="search_result_error"><? echo $searchmsg; ?></div>
			<?php }
  }
function showSearchDesign($q,$contrib_id,$mo,$day,$year,$search,$showtabs,$object_type,$offset,$subscription_id,$searchmsg,$title,$searchArchive){
	global $arSearchItems,$IMG_SERVER;
		if($object_type=="7"){
			$disable="disabled";
            $disable_author="disabled";
		}else if($object_type=="13"||$object_type=="14" || $object_type=="6" || $object_type=="15" || $object_type=="16" || $object_type=="17" || $object_type=="20") {
		  $disable_author="disabled";
		  $disable="";
		}else {
			$disable="";
		}
	?>
		<form method="get" action="<?= $PHP_SELF; ?>" name="searchform" >
			<input type="hidden" value="1" name="search" />
			<input type="hidden" value="1" name="send" />
   			<input type="hidden" value="<?=$object_type;?>" name="oid"  id="oid" />
            <table class="search_header" width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
				  <tr>
					<td colspan="6" class="common_heading">your search</td>
				  </tr>
				  <tr>
				<td class="search_input_box" width="10%"><span>Search Keyword</span></td>
					<td colspan="2"><input  class="textinput" style="width:135px;" type="text" name="q" id="qsearch" value="<?=$q;?>"  onFocus="searchtitleboxempty();" onclick="searchtitleboxempty();" onkeyup="javascript:searchEnterKeyChk(event);" /></td>
					<td><span style="padding-left:0px;">or &nbsp; Title</span></td>
 					<td><input  class="textinput" style="width:135px;" type="text" name="title" id="titlesearch" onFocus="searhboxempty();" value="<?=$title;?>" onclick="searhboxempty();" onkeyup="javascript:searchEnterKeyChk(event);" /></td>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td><span>Sort By :</span></td>
					<td><?month_options("mo",$mo,"class='search_drop_day'","mo",$disable)?></td>
					<td><?day_options("day",$day,"class='search_drop_day'","day",$disable)?></td>
					<td><?year_options("year",$year,"class='search_drop_day'",date("Y"),2002,"year",$disable)?></td>
					<td><select name="contrib_id" id="contrib_id" class="search_author_drop_down" <?=$disable_author?> >
					<option>-All Authors-</option>
						<?
						$arContributor = get_active_contributors();
						foreach($arContributor as $key => $arCont)
						{
							 if(strtolower($arCont['name']) == strtolower('Todd Harrison')
							 || strtolower($arCont['name']) == strtolower('Kevin Depew')
							 || strtolower($arCont['name']) == strtolower('Jeff Macke'))
							 {
							 	$arTopThree[$key]=$arCont;
							 }
						}
						$arContributor = array_diff_assoc($arContributor,$arTopThree);
						$arContributor = array_merge(array_reverse($arTopThree),$arContributor);
                        selectHashArr($arContributor,"id","name",$contrib_id,$disable)?>
					</select></td>
					<td align="right" ><img src="<?=$IMG_SERVER?>/images/search.jpg" align="right" onclick="submitSearch('archive');"/> </td>
                    </tr>
                    <tr><td colspan="6">
                    <input type="hidden" name="searchArchive" id="searchArchive" value="<?=$searchArchive?>">
					<!-- <img src="<?=$IMG_SERVER?>/images/search_entire_archive.jpg" onclick="submitSearch('archive');" > -->
					 </td>
                    </tr>
			</table>
			</form>
			<div id="search_tab" class="search_result_container" >
		<?php

			foreach($arSearchItems as $stItemType => $arSearchItem)
			{
				($stItemType == $object_type)?$className = 'search_nav_tab_selected':$className = 'search_nav_tab';
				if($arSearchItem["is_product"] == '1' && $contrib_id=="")
				{
					if($stItemType=="30"){
						$contribid = '';
					}else{
						$contribid=get_contributor_id_byname($arSearchItem["author_name"]);
					}
				}
				else
				{
					$contribid = '';
				}
				?>
			<div id="searchtab_<?=$stItemType?>" class="<?=$className;?>"><a onclick="getSearchResult('<?=$stItemType;?>','<?=$searchmsg?>','<?=$contribid?>');"><?=$arSearchItem["display_name"]?></a></div>
			<?
			}
			?>
	    </div>
            <div style="height:13px;" id="searchprogress" class="search_result_error">&nbsp;</div>
<?
}
	function archivePagination($q,$contrib_id,$mo,$day,$year,$search,$showtabs,$object_type,$offset,$numrows,$contentcount,$p,$title,$searchArchive=null){
	 $shownum=10;
	/*pagination start here*/
	 if(!is_numeric($contrib_id)){
	 	$contrib_id="";
	 }

	 if($searchArchive!='1' || $searchArchive==''){
	 	$searchArchive='0';
	 }

						if($numrows>$contentcount) {
								 //$countnum=10;
								 $countnum=ceil(($numrows/$contentcount));
								 // if($shownum<$numrows){
								 if(($shownum<$countnum) && ($offset+ $shownum < $countnum)){
								 	$countnum=$shownum + $p;
								 }
						?>
						<div  class="seacrh_pagination">
							<table  border="0" cellspacing="0" cellpadding="0">
								<tr>
							<?php
								if(!$p=="0"){  ?>
								<td ><a style="text-decoration:none;" href="<?$HTPFX.$HTHOST?>/library/search.htm?p=<?=$j?>&search=<?=$search?>&q=<?=$q?>&title=<?=$title?>&mo=<?=$mo?>&day=<?=$day?>&year=<?=$year?>&contrib_id=<?=$contrib_id?>&oid=<?=$object_type?>&searchArchive=<?=$searchArchive?>">&laquo;</a></td>
							<?php }

								for($i=$p; $i<=$countnum-1; $i++) {
									$j=$i;

									if($offset==$i){
								 ?>
									<td width="20"><?=($i+1);?></td>
								<?php } else {
								?>
							<td ><a href="<?$HTPFX.$HTHOST?>/library/search.htm?p=<?=$i?>&search=<?=$search?>&q=<?=$q?>&title=<?=$title?>&mo=<?=$mo?>&day=<?=$day?>&year=<?=$year?>&contrib_id=<?=$contrib_id?>&oid=<?=$object_type?>&searchArchive=<?=$searchArchive?>"><?=($i+1);?></a></td>
							<?php	}
							  }
													 $p=$p+1;
													 if($numrows>=(($p)*$contentcount)){  ?>
								<td ><a style="text-decoration:none;" href="<?$HTPFX.$HTHOST?>/library/search.htm?p=<?=$p?>&search=<?=$search?>&q=<?=$q?>&title=<?=$title?>&mo=<?=$mo?>&day=<?=$day?>&year=<?=$year?>&contrib_id=<?=$contrib_id?>&oid=<?=$object_type?>&searchArchive=<?=$searchArchive?>">&raquo;</a></td>
							<?php }
							 ?>
								</tr>
							</table>
						</div>
						<?php  }  // for end
						/*pagination end here*/
	}
?>