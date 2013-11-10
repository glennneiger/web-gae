<?php
function displayCurrentDate() {
	$dateInfo = getDate();
	$todaysDate = $dateInfo['weekday'] . " " . $dateInfo['month'] . " " . $dateInfo['mday'] . ", " . $dateInfo['year'];
	return $todaysDate;
}


function getAllArticles($start,$end) {
$sql = "select articles.id id, articles.title, contributors.name author, articles.contributor, contributors.disclaimer,
                articles.position, contrib_id authorid,IF(publish_date,publish_date,date) as date, blurb, keyword,body, position, character_text from articles, contributors where
                articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' ORDER BY date DESC LIMIT " . $start . "," . $end;

$results = exec_query($sql);
//print_r($results);
 	foreach($results as $article) {
		//$url = makeArticleslink($article['id']);
		$link_info= makeArticleslink_sql($article['id']);
		$linkurl=makeArticleslink($article['id'],$link_info['keyword'],$link_info['blurb']);

		$headline = mswordReplaceSpecialChars($article['title']);
		$author = $article['author'];
		echo "<li><a href='".$linkurl."'>".$headline."</a><br>".$author."</li>";
	}
}

function getAllArticlesBySubSection($section, $start,$end) {
$sql = "select id, title, contributor, contrib_id, keyword from articles where approved = '1' and is_live = '1' and (subsection_ids like '".$section.",%' or subsection_ids = '".$section."' or subsection_ids like '%,".$section."' or subsection_ids like '%,".$section.",%') ORDER BY date DESC LIMIT ".$start.", ".$end;
$results = exec_query($sql);
//print_r($results);
        foreach($results as $article) {
                //$url = makeArticleslink($article['id']);
				$link_info= makeArticleslink_sql($article['id']);
				$linkurl=makeArticleslink($article['id'],$link_info['keyword'],$link_info['blurb']);

                $headline = mswordReplaceSpecialChars($article['title']);
                $author = $article['contributor'];
                echo "<li><a href='".$linkurl."'>".$headline."</a><br>".$author."</li>";
        }
}

function getAllDailyFeed($start,$end) {
$sql = "select title, id, source from daily_feed where is_approved = '1' ORDER BY creation_date DESC LIMIT " . $start . "," . $end;
$results = exec_query($sql);
//print_r($results);
        foreach($results as $article) {
                $url = "/dailyfeed/index.htm#".$article['id'];
                $headline = "<div id='df-kicker'><a href='".$url."'>".mswordReplaceSpecialChars($article['title'])."</a>";
		$headline = str_replace(":", ":</div><a href='".$url."'>", $headline);
                $author = $article['source'];
                echo "<li>".$headline."<br>".$author."</li>";
        }
}

function renderPageModules($page_id,$placeholder)
{
	global $pageModulesCacheExpiry;
	$memCache = new Cache();
	if(isset($_GET['preview']) && $_GET['preview'] == 1)
	{
		$arModuleResult = getTempModulesList($page_id,$placeholder);
	}
	else
	{
		$arModuleResult['module_order'] = $memCache->getModulesListCache($page_id,$placeholder);
	}
	if(!$arModuleResult || $arModuleResult['module_order'] == "")
	{
		return;
	}
	else
	{
		$arModules = explode(",",$arModuleResult['module_order']);
		$strContent = "";
		foreach($arModules as $module_id)
		{
			if(isset($_GET['preview']) && $_GET['preview'] == 1)
			{
				$strContent .= renderTemplateContent($module_id);
			}
			else
			{
				// Implementation of Cache
				$stModuleContent = $memCache->getPageModuleCache($module_id);
				$strContent .= $stModuleContent;
			}

		}
	}
	return $strContent;

}
function renderTemplateContent($module_id,$template_id = NULL)
{
	global $HTPFX,$HTHOST,$HTPFXNSSL,$IMG_SERVER,$VIDEO_SERVER,$D_R;
	include_once($D_R."/lib/_content_data_lib.php");
    $objMemcache= new Cache();
	if($template_id == NULL)
	{
		$arTemplateDetail = getModuleTemplateDetail($module_id);
		$template_id = $arTemplateDetail['template_id'];
		$strHTML = $arTemplateDetail['design'];
		// To get Layout Component of the Module
		$arModuleComponent = getModuleComponent($module_id,$template_id);
	}
	else
	{
		$arTemplateDetail = getTemplateDetail($template_id);
		$strHTML = $arTemplateDetail['design'];
		// To get Layout Component of the Module
		$arModuleComponent = getTempModuleComponent($module_id,$template_id);
	}
	foreach($arModuleComponent as $componentDetail)
	{

		// Component is not added by the editorial
		if($componentDetail['component_type'] == "")
		{
			// To ommit the content Div If no content is there
			$strDiv = '<div class="'.strtolower($componentDetail['component_name']).'">['.$componentDetail['component_name'].']</div>';
			$strHTML = str_replace($strDiv,"",$strHTML);
			$arReplacing[] = "/\[".$componentDetail['component_name']."\]/";
			$arReplacement[] = "";
		}
		else if($componentDetail['component_type'] == 'custom_url')
		{
			$slide_article_link = $componentDetail['content'];
		}
		else if($componentDetail['component_type'] == 'static_list')
		{
			global $HTPFX, $HTHOST;
			$obContent = new Content("","");
			$stContent = "";
			if($componentDetail['component_name'] == 'TEMPLATE9_ARTICLE1' ){
				$stContent = "<h1>Also:</h1>";
			}
			$arSelectedItem = array();
			$arSelectedItem = explode(",",$componentDetail['content']);
			if(count($arSelectedItem) > 0)
			{
				$i=0;
				foreach($arSelectedItem as $arValue)
				{
					if($arValue != "")
					{
						$arItemDetail = explode(":",$arValue);
						// Fetch Data from Article TAble rather than ex_item_meta table
						if($arItemDetail[1] == 1)
						{
							$arItemData = getArticleDetail($arItemDetail[0]);
							if($componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST'){
								$body = getPartOfBody($arItemData['body'],'20');
							}else{
								$arItemData['description'] = $arItemData['character_text'];
							}
						}
						else
						{
							$arItemData = $obContent->getMetaSearch($arItemDetail[0],$arItemDetail[1]);
						}
						if($componentDetail['component_name'] == 'TEMPLATE1_HEADLINE_ARTICLE' || $componentDetail['component_name'] == 'TEMPLATE2_FEATURE_ARTICLE' || $componentDetail['component_name'] == 'TEMPLATE3_BODY'  || $componentDetail['component_name'] == 'TEMPLATE6_BODY' || $componentDetail['component_name'] == 'TEMPLATE16_BODY')
						{
							$stContent .="<div class='".$componentDetail[component_name]."_title'><a href='".$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0'])."'>".mswordReplaceSpecialChars($arItemData['title'])."</a></div>";
							$stContent .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'].">".$arItemData['author']."</a></div><div class='".$componentDetail[component_name]."_desc'>".$arItemData['description'].'</div>';
							if($componentDetail['component_name'] == 'TEMPLATE6_BODY' || $componentDetail['component_name'] == 'TEMPLATE16_BODY' ||$componentDetail['component_name'] == 'TEMPLATE1_HEADLINE_ARTICLE')
							{
								$stContent .="<a href='".$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0'])."'>Read More &raquo;</a>";
							}
						}
						elseif($componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST'){
							if($i!='2'){
								$liClass = " class='edu_li'";
							}else{
								$liClass = " class='edu_wli'";
							}
							$stContent .="<li".$liClass."><div><p class='edu_title'><a href='".$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0'])."'>".mswordReplaceSpecialChars($arItemData['title'])."</a></p><p class='edu_author'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'].">".$arItemData['author']."</a></p><p class='edu_author'>".date('D F j, Y, g:i A',strtotime($arItemData['publish_date']))." EDT</p></div><p class='edu_img'><img src='".$IMG_SERVER."/images/education/edu_sample.jpg' /></p><p class='edu_desc'>".$body."</p></li>";
						}
						else if($componentDetail['component_name'] == 'TEMPLATE22_ARTICLE')
						{
							$artBody = $obContent->getEduBody($arItemDetail[1]);
							$body=getPartOfBody($artBody['body'],'25');
							$stContent .= '<br><a href="'.$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0']).'?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=edu" >
							<span class="article_title">'.mswordReplaceSpecialChars($arItemData['title']).'</span> <p class="article_desc">'.mswordReplaceSpecialChars($body).'</p></a>';
						}
						else
						{
							if($componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST1')
							{
							$stContent .="<li><h1><a href='".$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0'])."'>".mswordReplaceSpecialChars($arItemData['title'])."</a></h1>";

							}
							else if($componentDetail['component_name'] == 'TEMPLATE9_ARTICLE1')
							{
							if(is_numeric($arItemDetail[0])){
								$stContent .="<h2><a href='".$HTPFX.$HTHOST."".getItemURL($arItemDetail[1],$arItemDetail['0'])."'>".mswordReplaceSpecialChars($arItemData['title'])."</a></h2>";
							}else{
								$stContent .="<h2><a href='".$HTPFX.$arItemDetail[0]."'>".mswordReplaceSpecialChars($arItemDetail[1])."</a></h2>";
							}
							}
							else if($componentDetail['component_name'] == 'TEMPLATE9_MAIN_ARTICLE')
							{
								$slide_article_link = $HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0']);
							}
							else
							{
							$stContent .="<li><a href='".$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail['0'])."'>".mswordReplaceSpecialChars($arItemData['title'])."</a>";
							}
							if($componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST2' || $componentDetail['component_name'] == 'TEMPLATE9_ARTICLE1' || $componentDetail['component_name'] == 'TEMPLATE22_ARTICLE' )
							{
							}
						    if($componentDetail['component_name'] == 'TEMPLATE14_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE15_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE17_ARTICLE_LIST')
							{
								$stContent .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'].">".$arItemData['author']."</a></div>";
							}elseif ($componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST'){
								$stContent .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'].">".$arItemData['author']."</a> - ".date('D F j, Y, g:i A',strtotime($arItemData['date']))." EDT</div>";
							}
							else
							if($arItemDetail[1] != 11 && ($componentDetail['component_name'] != 'TEMPLATE9_ARTICLE1' ||  $componentDetail['component_name'] != 'TEMPLATE22_ARTICLE')) // Not to display author name for video
							{
								$stContent .="<h3><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'].">".$arItemData['author']."</a></h3>";
							}
							$stContent .="</li>";
						}
					}
					$i++;
				}
				if($componentDetail['component_name'] == 'TEMPLATE1_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE2_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE3_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE7_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST1' || $componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST2' || $componentDetail['component_name'] == 'TEMPLATE14_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE15_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE17_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE24_ARTICLE_LIST')
				{
					$stContent = "<ul>".$stContent."</ul>";
				}
			}
			$arReplacing[] = "/\[".$componentDetail['component_name']."\]/";
			$arReplacement[] = $stContent;
		}
		else if($componentDetail['component_type'] == 'dynamic_list')
		{

			$sqlResult = "";
			// Fetch Result set from the dynamic query for the component
				if($module_id=="343" && $componentDetail['component_name']=="TEMPLATE15_ARTICLE_LIST")
			 {
			    $objCache= new Cache();
				$arQueryResultRead =$objCache->getMostRead('1',$limit=5);
				foreach($arQueryResultRead as $key=>$itemVal){
					$arQueryResult[$key]['item_id']=$itemVal['item_id'];
					$arQueryResult[$key]['item_title']=$itemVal['title'];
					$arQueryResult[$key]['item_type']=1;
				}

			}
			 else
			 {

			$arQueryResult = exec_query($componentDetail['content_sql']);
			 }
			 $arQueryResultCount = count($arQueryResult);
			if($arQueryResult && $arQueryResultCount > 0)
			{
				if($componentDetail['component_name'] != 'TEMPLATE6_BODY' && $componentDetail['component_name'] != 'TEMPLATE16_BODY' && $componentDetail['component_name'] !== 'TEMPLATE22_ARTICLE' && $componentDetail['component_name'] !== 'TEMPLATE23_ARTICLE_LIST')
				{
				$sqlResult .="<ul>";
				}
				foreach($arQueryResult as $arQuerykey=>$arQueryRow)
				{
					if($componentDetail['component_name'] == 'TEMPLATE6_BODY' || $componentDetail['component_name'] == 'TEMPLATE16_BODY')
					{
						$obContent = new Content("","");
						if($arQueryRow['item_type'] == 1)
						{
							$arItemData = getArticleDetail($arQueryRow['item_id']);
							$arItemData['description'] = $arItemData['character_text'];
						}
						else
						{
							$arItemData = $obContent->getMetaSearch($arQueryRow['item_id'],$arQueryRow['item_type']);
						}
						$sqlResult .="<h1><a href='".$HTPFX.$HTHOST.getItemURL($arQueryRow['item_type'],$arQueryRow['item_id'])."'>".mswordReplaceSpecialChars($arQueryRow['item_title'])."</a></h1>";
						$sqlResult .="<h3><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['author_id'].">".$arQueryRow['author_name']."</a></h3><h4>".$arItemData['description']."</h4>";
						$sqlResult .="<a href='".$HTPFX.$HTHOST.getItemURL($arQueryRow['item_type'],$arQueryRow['item_id'])."'>READ MORE &raquo;</a>";

					}
					else
					{
						$sourceLink = "";
						if($componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST1')
						{
							$sqlResult .="<li><h1><a href='".$HTPFX.$HTHOST.getItemURL($arQueryRow['item_type'],$arQueryRow['item_id'])."'>".mswordReplaceSpecialChars($arQueryRow['item_title'])."</a></h1>";

						}
						else
						{
							$target = getItemURL($arQueryRow['item_type'],$arQueryRow['item_id']);
							if($arQueryRow['item_type'] == 18)
							{

								if(substr($arQueryRow['source_link'],0,4) != "http")
								{
									$arQueryRow['source_link'] = "http://".$arQueryRow['source_link'];
								}
								$sourceLink = "<h3><a href=".$target.">".$arQueryRow['source']."</a></h3>";
							}

							if($componentDetail['component_name'] == 'TEMPLATE13_ARTICLE_LIST')
							{
							  $objDailyfeed= new Dailyfeed();
							  $getImage=$objDailyfeed->getImageDailyFeed($arQueryRow['item_id']);

							   $quick_Title = $objDailyfeed->getQuickTitleDailyFeed($arQueryRow['item_id'],'18');
							   if($arQueryRow['layout_type'] == 'thestreet'){
										if(stristr($quick_Title['quick_title'],'TheStreet.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_thestreetlogo.gif";
										}
										elseif(stristr($quick_Title['quick_title'],	'RealMoney.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_Realmoneylogo.gif";
										}
										elseif(stristr($quick_Title['quick_title'],'MainStreet.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_mainStreetlogo.gif";
										}
										else
										{
											$imageURL	=	$getImage['url'];
										}
									}
									else
									{
										$imageURL	=	$getImage['url'];
								     }
 if(!($arQuerykey%2)){
 	$classDFLi="dailyfeedHomeItemContainerright tenMarginRt";
 }else{
 	$classDFLi="dailyfeedHomeItemContainerright tenPaddLt dfBorderLt";
 }
							 $sqlResult .="<li>";
							 $sqlResult .="<div class='".$classDFLi."'>";
							   if($quick_Title['quick_title']){
									if($imageURL!=''){
										$sqlResult .="<div class='quicktitle-dailyfeed-image'>".strtoupper(trim($quick_Title['quick_title']))."</div>";

									}else{
										$sqlResult .="<div class='quicktitle-dailyfeed'>".strtoupper(trim($quick_Title['quick_title']))."</div>";
									}
								 }
						 	   if($imageURL!=''){
									$sqlResult .="<div class='dailyfeed-area-image'><a href='".$HTPFXNSSL.$HTHOST.$target."'><img src=".$imageURL." border='0' width='140px' height='86' alt='DailyFeed' /></a></div>";
								 }
							   $headline =mswordReplaceSpecialChars($arQueryRow['item_title']);
							   $sqlResult .="<div class='title-desc-dailyfeed'>";
							   if($imageURL!=''){
							   	$sqlResult .="<div class='title-dailyfeed'><a href='".$HTPFXNSSL.$HTHOST.$target."'>".$headline."</a></div>";
							   }else{
							   	 $sqlResult .="<div class='title-dailyfeed-mvhome'><a href='".$HTPFXNSSL.$HTHOST.$target."'>".$headline."</a></div>";
							   }
							   $sqlResult .="<div class='author-dailyfeed'><a href='".$HTPFXNSSL.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['contrib_id']."'>".ucwords (trim($arQueryRow['authorname']))."</a></div>";

							  $sqlResult .="<div class='desc-dailyfeed-home'>".$arQueryRow['excerpt']."</div>";
								$sqlResult .="</div>";
								$sqlResult .="<div class='readmore-dailyfeed'><a href='".$HTPFXNSSL.$HTHOST.$target."'>READ MORE..</a></div>";

							 $sqlResult .="</div>";

							 /* End 2 column here */
							  $sqlResult .="</li>";

							}


							elseif($componentDetail['component_name'] == 'TEMPLATE18_ARTICLE_LIST')
							{
							    /**********************************************************************************/
								  $objDailyfeed= new Dailyfeed();
								  $getImage=$objDailyfeed->getImageDailyFeed($arQueryRow['item_id']);

								   $quick_Title = $objDailyfeed->getQuickTitleDailyFeed($arQueryRow['item_id'],'18');
								    if($arQueryRow['layout_type'] == 'thestreet'){
										if(stristr($quick_Title['quick_title'],'TheStreet.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_thestreetlogo.gif";
										}
										elseif(stristr($quick_Title['quick_title'],	'RealMoney.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_Realmoneylogo.gif";
										}
										elseif(stristr($quick_Title['quick_title'],'MainStreet.com'))
										{
											$imageURL	=	$IMG_SERVER."/assets/dailyfeed/thumb_mainStreetlogo.gif";
										}
										else
										{
											$imageURL	=	$getImage['url'];
										}
									}
									else
									{
										$imageURL	=	$getImage['url'];
								    }
								  $sqlResult .="<li>";
								 if($quick_Title['quick_title']){
									if($imageURL!=''){
										$sqlResult .="<div class='quicktitle-dailyfeed-image-market'>".mswordReplaceSpecialChars(strtoupper(trim($quick_Title['quick_title'])))."</div>";
									}else{
										$sqlResult .="<div class='quicktitle-dailyfeed-market'>".mswordReplaceSpecialChars(strtoupper(trim($quick_Title['quick_title'])))."</div>";
									}
								 }
								 if($imageURL!=''){
									$sqlResult .="<a href='".$target."'><span class='dailyfeed-area-image-market'><img src=".$imageURL." border='0' width='140px' alt='DailyFeed' /></span></a>";
								 }

								 $headline = "<div id='title-dailyfeed-market'><a href='".$target."'>".mswordReplaceSpecialChars($arQueryRow['item_title'])."</a></div>";
								$sqlResult .="<a href='".$target."'>".$headline."</a>";
								$sqlResult .="<div class='author-dailyfeed-market'><a class='author-dailyfeed-market' href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['contrib_id']."'>".trim($arQueryRow['authorname'])."</a></div>";
								$sqlResult .="<div class='market-df-body'>";
								$sqlResult.=$arQueryRow['excerpt'];
								$sqlResult .="</div>";
								$sqlResult .="<div id='readmore-dailyfeed-market'><a href='".$HTPFXNSSL.$HTHOST.$target."'>READ MORE..</a></div>";

							/****************************************************************************************/
							}
							elseif ($componentDetail['component_name'] == 'TEMPLATE22_ARTICLE'){
								$body=getPartOfBody($arQueryRow['body'],'25');
								$sliderUrl = getEduArtUrl($arQueryRow['item_id']);
								$sqlResult .= '<br><a href="'.$HTPFX.$HTHOST.$sliderUrl['url'].'?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=edu" ><span class="article_title">'.mswordReplaceSpecialChars($arQueryRow['item_title']).'</span>
<p class="article_desc">'.mswordReplaceSpecialChars($body).'</p></a>';
							}elseif ($componentDetail['component_name'] == 'TEMPLATE23_ARTICLE_LIST'){
								if($arQueryRow['edu_img']==false){
									$imgPath = $IMG_SERVER.'/images/education/edu_sample.jpg';
								} else {
									$imgPath = $IMG_SERVER.'/assets/edu/images/'.$arQueryRow['edu_img'];
								}
								$eduUrl = getEduArtUrl($arQueryRow['item_id']);
								if($arQuerykey=="0"){
									$body=getPartOfBody($arQueryRow['body'],'25');
									$sqlResult .= '<div class="eduArtList"><div class="eduArtListDetail">
										<h3><a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'">'.mswordReplaceSpecialChars($arQueryRow['item_title']).'</a></h3>
										<p class="eduArtListAuthor"><a href="'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio="'.$arQueryRow['authorId'].'">'.$arQueryRow['authorname'].'</a><br>'.date("D F j, Y, g:i A",strtotime($arQueryRow['created_on'])).' EDT</p>
										<div class="eduFirstArtImg"><a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'"><img src="'.$imgPath.'"></a></div>
										<p class="eduFirstArtDesc"><a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'">'.$body.'</a></p>
										</div></div>';
								}else{
									$body=getPartOfBody($arQueryRow['body'],'16');
									if($arQuerykey=="1"){
										$sqlResult .= '<div class="eduNthArtContainer"><div class="eduNthArtBox"><ul id="list'.$module_id.'">';
									}
									$sqlResult .= '<li class="scrollCon'.$module_id.'" ><div class="eduArtListDetail">
										<h3><a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'">'.mswordReplaceSpecialChars($arQueryRow['item_title']).'</a></h3>
										<p class="eduArtListAuthor">
											<a href="'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio="'.$arQueryRow['authorId'].'>'.$arQueryRow['authorname'].'</a><br>'.date("D F j, Y, g:i A",strtotime($arQueryRow['created_on'])).' EDT</p>
										<div class="eduNthArtDesc">
											<div class="artImgBox">
												<a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'"><img src="'.$imgPath.'"></a>
											</div>	
											<p><a href="'.$HTPFX.$HTHOST.$eduUrl['url'].'">'.$body.'</a></p><br>
										</div></div><div style="clear:both;"></div></li>';
									if($arQuerykey==$arQueryResultCount-1){
										$sqlResult .= '</ul></div></div>';
									}
									?>
									
									<?php 
								}
																
							}else{
								if($componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE24_ARTICLE_LIST'){
									session_start();
									if(empty($_SESSION['Buzz'])){
										$sqlResult.='<li><a href="http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=buzz"  >'.mswordReplaceSpecialChars($arQueryRow['item_title']).'</a></li>';
									}elseif(!empty($_SESSION['Buzz'])){
										$buzzLaunchUrl=buzzAppUrlEncryption();
										$sqlResult.='<li><a href="javascript:;" onclick="window.open(\''.$buzzLaunchUrl.'\',\'Banter\',\'width=455,height=708,resizable=yes,toolbar=no,scrollbars=no\'); banterWindow.focus();">'.mswordReplaceSpecialChars($arQueryRow['item_title']).'</a></li>';
									}
								}else{
									$sqlResult .="<li><a href='".$HTFPX.$target."'>".mswordReplaceSpecialChars($arQueryRow['item_title'])."</a>".$sourceLink;
								}
							}
						}
						if($componentDetail['component_name'] == 'TEMPLATE10_ARTICLE_LIST2' || $arQueryRow['item_type'] == 18)
						{
                        }
						if($componentDetail['component_name'] == 'TEMPLATE14_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE15_ARTICLE_LIST'  || $componentDetail['component_name'] == 'TEMPLATE17_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST'  || $componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE22_ARTICLE' || $componentDetail['component_name'] == 'TEMPLATE23_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE24_ARTICLE_LIST'  || $arQueryRow['item_type'] == 18)
						{
							//$sqlResult .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['author_id'].">".$arQueryRow['author_name']."</a></div>";
						}
						else if($arQueryRow['item_type'] != 11) // Not to display author name for video
						{
							$sqlResult .="<h3><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['author_id'].">".$arQueryRow['author_name']."</a></h3>";
						}
						if($componentDetail['component_name'] == 'TEMPLATE14_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE15_ARTICLE_LIST'  || $componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST'){
							if($arQueryRow['item_type'] == 11){
							switch($arQueryRow['author_name']){
							case 'HOOFY & BOO':
								$videoUrl=$HTPFX.'videos.minyanville.com/video/hoofyandboo';
								break;
							case 'TODD HARRISON TV':
								$videoUrl=$HTPFX.'videos.minyanville.com/video/toddharrisontv';
								break;
							case 'POP BIZ':
								$videoUrl=$HTPFX.'videos.minyanville.com/video/popbiz';
								break;
							case 'T3 Live':
								$videoUrl=$HTPFX.'videos.minyanville.com/video/t3live';
								break;
								case 'Specials':
								$videoUrl=$HTPFX.'videos.minyanville.com/video/specials';
								break;
							default:
								$videoUrl=$HTPFX.'videos.minyanville.com/video/';
							break;
							}
							$sqlResult .="<div class='author-by'><a href=".$videoUrl.">".$arQueryRow['author_name']."</a></div>";
							}else{
								if($componentDetail['component_name'] == 'TEMPLATE21_ARTICLE_LIST'){
									$sqlResult .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['author_id'].">".$arQueryRow['author_name']."</a> - ".date('D F j, Y, g:i A',strtotime($arQueryRow['created_on']))." EDT</div>";
								}else{
									$sqlResult .="<div class='author-by'><a href=".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arQueryRow['author_id'].">".$arQueryRow['author_name']."</a></div>";
								}
							}
				}
						if($componentDetail['component_name'] != 'TEMPLATE22_ARTICLE' && $componentDetail['component_name'] != 'TEMPLATE23_ARTICLE_LIST'){
							$sqlResult .="</li>";
						}
					}
				}

				if($componentDetail['component_name'] != 'TEMPLATE6_BODY' && $componentDetail['component_name'] != 'TEMPLATE16_BODY' && $componentDetail['component_name'] != 'TEMPLATE22_ARTICLE' && $componentDetail['component_name'] && 'TEMPLATE23_ARTICLE_LIST')
				{
					$sqlResult .="</ul>";
				}

				if(($componentDetail['component_name'] == 'TEMPLATE15_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE20_ARTICLE_LIST' || $componentDetail['component_name'] == 'TEMPLATE23_ARTICLE_LIST') && (!strpos($componentDetail['content_sql'],'ORDER BY total DESC')) && (!strpos($componentDetail['content_sql'],'article_emailed')) && (!strpos($componentDetail['content_sql'],'article_recent'))){
					$findSectionIDPos=strpos($componentDetail['content_sql'],"FIND_IN_SET(");
					$findSectionId=substr($componentDetail['content_sql'],$findSectionIDPos + 12,2);
				}
			}
			$arReplacing[] = "/\[".$componentDetail['component_name']."\]/";
			$arReplacement[] = $sqlResult;
		}
		else if($componentDetail['component_type'] == 'author_detail')
		{
			$stContent = "";
			$arAuthorResult = getProfessorDetail($componentDetail['content_sql']);
			$stContent ='<h3><a href='.$HTPFX.$HTHOST.'/gazette/bios.htm?bio='.$componentDetail['content_sql'].'>'.$arAuthorResult['name'].'</a></h3>
							<span>'.$componentDetail['content'].'</span>';
			$arReplacing[] = "/\[".$componentDetail['component_name']."\]/";
			$arReplacement[] = $stContent;
		}
		else
		{
			$arReplacing[] = "/\[".$componentDetail['component_name']."\]/";
			if($componentDetail['link'] != "")
			{
				$arReplacement[] = "<a href='".$componentDetail['link']."' target='".$componentDetail['target']."'>".$componentDetail['content']."</a>";
			}
			else
			{
				$arReplacement[] = $componentDetail['content'];
				if($componentDetail['component_name'] == 'TEMPLATE19_TEXT'){
					$arcontDecode[]="";
					$arcont[]="";
					$json = new Services_JSON();
					$arcontDecode[] =  $json->decode($arReplacement['0'],true);
					$arcont[] = $arcontDecode['1'] -> content;
					$arReplacement['0'] = $arcont['1'];
				}
			}
		}
	}

	if(!empty($arReplacing)){
		foreach($arReplacing as $key => $value)
		{
			$arReplacement[$key]=preg_match_char($arReplacement[$key]);
			// special case for home page slide show module
			if($value == '/\[TEMPLATE9_GRAPHICS\]/')
			{
				$arReplacement[$key] = "<a href='".$slide_article_link."'>".$arReplacement[$key]."</a>";
			}
		}
		$strHTML = preg_replace($arReplacing,$arReplacement,$strHTML);
	}
	return $strHTML;

}
//for Feature article
function renderFeatureModule()
{
$arrFeature = getfeaturearticle();
?>
<div id="mygallery" class="stepcarousel">
<div class="belt">
<?
 $i = 1;
 foreach($arrFeature as $Feature)
 {
    $article_v = getarticle_video($Feature['object_type'], $Feature['object_id']);
    $tagarticle= gettagarticle($article_v[0]['id'],$Feature['object_type']);
	if($Feature['object_type']=='1')
	{
	 $readmore =  makeArticleslinkTemp($article_v[0]['id'],$article_v[0]['keyword'],$article_v[0]['blurb'],$from=NULL,$page=NULL);
	 $urlauthor = $article_v[0]['contrib_id'];
	 $authorurl = $HTPFX.$HTHOST."/gazette/bios.htm?bio=$urlauthor";
	}
	else
	{
	 $videoid  =  $article_v[0]['id'];

	  $readmore  =  $HTPFX.$HTHOST."/mvtv/audio_video.htm?videoid=$videoid";
	  $urlauthor = '#';
	  $authorurl = '#';
	  $sectionid =getVideoSubsectionid($videoid);
      $pageDetail['id']=$sectionid['page_id'];
      $section_id=$sectionid['section_id'];
      $sec_name=$sectionid['name'];




	}
	 if($article_v[0]['featureimage']!='')
	 {
	   $img       = $IMG_SERVER.$article_v[0]['featureimage'];
	  }
	  else
	  {
	   $img ="images/feature_slide.jpg";
	  }
    // Code for Selected Slide
	$stClassSlide1 = $stClassSlide2 = $stClassSlide3 = $stClassSlide4 =	$stClassSlide5 =  'feature_nav_button';
	switch($i)
	{
	case '1':
	$stClassSlide1 = "feature_nav_button_selected";
	break;
	case '2':
	$stClassSlide2 = "feature_nav_button_selected";
	break;
	case '3':
	$stClassSlide3 = "feature_nav_button_selected";
	break;
	case '4':
	$stClassSlide4 = "feature_nav_button_selected";
	break;
	case '5':
	$stClassSlide5 = "feature_nav_button_selected";
	break;
	}
	$i++;
?>
<div class="panel">
<table  cellpadding="0" cellpadding="0" border="0">
<tr><td>
<div class="feature_slideshow_container" >
<div class="feature_top_container">
<div class="feature_slideshow_left" ><img src="<?=$img ?>"  border="0"  /></div>
<div class="feature_slideshow_right">
<div style="width:auto; float:right; z-index:1000;">
<a href="JavaScript:void(0);" onclick="javascript:stepcarousel.stepTo('mygallery',5)"><div class="<?=$stClassSlide5?>">5</div></a>
<a href="JavaScript:void(0);" onclick="javascript:stepcarousel.stepTo('mygallery', 4)"><div class="<?=$stClassSlide4?>">4</div></a>
<a href="JavaScript:void(0);" onclick="javascript:stepcarousel.stepTo('mygallery', 3)"><div class="<?=$stClassSlide3?>">3</div></a>
<a href="JavaScript:void(0);" onclick="javascript:stepcarousel.stepTo('mygallery', 2)"><div class="<?=$stClassSlide2?>">2</div></a>
<a href="JavaScript:void(0);" onclick="javascript:stepcarousel.stepTo('mygallery', 1)"><div class="<?=$stClassSlide1?>">1</div></a>
</div>
<div class="time_feature"><?=date("F j, Y",strtotime($article_v[0]['date']));?></div>
<span id="main_heading"><?= stripcslashes(mswordReplaceSpecialChars($article_v[0]['title']))?></span>
<br />
<?
// for contributer id
  if($Feature['object_type']=='1')
  {
      $qry      =  "Select name from contributors where id='".$article_v[0]['contrib_id']."'";
	  $artag    =  exec_query($qry);
	  $artag1   =  $artag[0]['name'];
	}
	else
	{
	 $artag1 = $sec_name;
	}

?>

<span class="byline_heading"><h3><a href="<?=$authorurl?>"><?=$artag1 ?></a></h3></span>
<br />
<? if($article_v[0]['blurb']!=''){$body =stripcslashes($article_v[0]['blurb']); }else{$body ="&nbsp;";}
    $body1 = substr($body, 0,30);
?>
<span class="main_sub_heading"><?=$body1?></span>
<br />
<h5><a href="<?=$readmore ?>">read more &#187;</a></h5>
</div>
</div>
<div class="feature_bottom_container">
<?
foreach($tagarticle as $tagarticle)
{
 if(strlen($tagarticle['title'])>35)
 {
  $subtitle = substr($tagarticle['title'],0,34)."..";
 }
  else
  {
   $subtitle = $tagarticle['title'];
  }
   if($Feature['object_type']=='1')
   {
   $urlauthorsecond = $tagarticle['contrib_id'];
   $authorurlsecond = $HTPFX.$HTHOST."/gazette/bios.htm?bio=$urlauthorsecond";
   $contid = $tagarticle['contributor'];
   }
   else
   {
    $urlauthorsecond='#';
	$sectionid =getVideoSubsectionid($tagarticle['id']);
      $pageDetail['id']=$sectionid['page_id'];
      $section_id=$sectionid['section_id'];
      $sec_name=$sectionid['name'];
	   $contid = $sec_name;
   }
 ?>
<div class="feature_title_slide"><?= mswordReplaceSpecialChars($subtitle);?>
<div id="feature_small_heading"><a href="<?=$authorurlsecond ?>"><?=$contid;?></a></div></div>
<? }?>

</div>
</div>
<div class="feature_slideshow_container_bg"></div>
</td></tr></table>
</div>

<?
}// end of for loop
?>
</div>
</div>

<?
}
function manageModulesForPlaceholder($place_holder_id,$module_id,$action)
{
	$stModuleOrder = getTempModulesDetail($place_holder_id,$type);
	if($stModuleOrder)
	{
		$arModules = explode(",",$stModuleOrder);
	}
	if($action == "add")
	{
		$arModules[] = $module_id;
	}
	else if($action == "delete")
	{
		$arModules = array_diff($arModules, array($module_id));
	}
	else if($action == "up")
	{
		$inPosition = array_search($module_id, $arModules);
		if($inPosition && isset($arModules[$inPosition-1]))
		{
			$tempVal = $arModules[$inPosition];
			$arModules[$inPosition] = $arModules[$inPosition-1];
			$arModules[$inPosition-1] = $tempVal;

		}
	}
	else if($action == "down")
	{
		$inPosition = array_search($module_id, $arModules);
		if($inPosition !== true && isset($arModules[$inPosition+1]))
		{
			$tempVal = $arModules[$inPosition];
			$arModules[$inPosition] = $arModules[$inPosition+1];
			$arModules[$inPosition+1] = $tempVal;

		}
	}
	if(is_array($arModules))
	{
		$stModuleOrder = implode(",",$arModules);
	}
	return updateTempModuleOrder($place_holder_id,$stModuleOrder);
}
function saveModule($action,$module_id,$module_name,$template_id)
{
	if(isModuleEmpty($action,$module_id))
	{
		return "EMPTY_MODULE";
	}
	if($action == 'edit') // Edit a module
	{
		if(isModuleNameExists($module_name,$module_id))
		{
			return "MODULE_NAME_EXIST";
		}
	}
	else  // Add a Module
	{
		if(isModuleNameExists($module_name))
		{
			return "MODULE_NAME_EXIST";
		}
	}
	$module_id = addEditModule($action,$module_id,$module_name,$template_id);
	return (int)$module_id;
}

//-----------------------------------------------
// display submenu(3/12/2008)
//-----------------------------------------------
 function  displaysubmenu($id)
 {
 	global $IMG_SERVER;
       $pid = $id;
	   if($_REQUEST['mode']!='' and $_GET['aid']!='')
          {
             $action = mainmanuactionperform($_GET['aid'],$_REQUEST['mode'],$_REQUEST['id']);
           }
	       $arrlistsubmenu = getlayoutsubmenu($pid);
	       $title          = gettitle($pid);
	  if(sizeof($arrlistsubmenu)>0)
	  {
			$content   ="<table width='100%' cellpadding='5' cellspacing='2' border='0'>
			            <tr ><td valign='middle' colspan='5' align='left' class='admin_module_head'><a href='manage_menu.php'>Menu                          Category&nbsp;</a>--->&nbsp;
						<a href='manage_submenu.php?id=".$_GET['id']."'>".ucwords(strtolower($title[0]['title'])) ."</a> </td></tr>
						  <tr><td valign='middle' class='admin_module_head'>Title</td>
						 <td valign='middle' class='admin_module_head'>Published </td>
						 <td valign='middle' class='admin_module_head'>Action</td></tr>";
			foreach($arrlistsubmenu as $listsubmenu)
			{
				$content   .="<tr class='highlight_row'><td valign='middle' >".ucfirst(stripslashes($listsubmenu['title']))."</td>";
				if($listsubmenu['active']=='1')
				{
				  $img = "<img src='".$IMG_SERVER."/images/redesign/tick.png'  />";
				}
				else
				{
				  $img = "<img src='".$IMG_SERVER."/images/redesign/publish_x.png'  />";
				}
				 if($listsubmenu['menuorder']!='')
				 {
				    $order = $listsubmenu['menuorder'];
				 }
				 else
				 {
				    $order = 'Not Set';
				 }
					$content   .="<td valign='middle' >".$img ."</td>";
					$content   .="<td valign='middle'><a href='edit_submenu.php?id=".$listsubmenu['id']."&pid=".$_GET['id']."'>
					              edit</a>&nbsp;&nbsp;<a  href='manage_submenu.php?aid=".$listsubmenu['id'].                                 "&mode=up&id=".$_GET['id']."'>up</a>&nbsp;&nbsp;
								  <a  href='manage_submenu.php?aid=".$listsubmenu['id']."&mode=down&id=".$_GET['id']."'>down
								  </a></td></tr>";

			}
			    $content   .="</table>";
		        return  $content;
	   }
	    else
			  {
				 return NULL;
			  }

}
//-----------------------------------------------
// edit submenu(3/12/2008)
//-----------------------------------------------
function editsubmenu($pid)
{
  $arrresult  = getsubmenu($pid );
  $title          = gettitle($_GET['pid']);
  if(isset($_POST['Update']) and $_POST['Update']=='update')
  {
    $post_title                  = addslashes(htmlentities($_POST['txttitle']));
    $editsubmenucol['title']     = addslashes(htmlentities($_POST['txttitle']));
	$editsubmenucol['page_id']   = $_POST['txtpageid'];
	$editsubmenucol['parent_id'] = $_POST['txtparentid'];
	$editsubmenucol['active']    = $_POST['txtpublished'];
	$editsubmenuid['id']         = $_POST['id'];
	// for validation
	 $sql_1     =   "select  title from layout_menu where title='$post_title' and parent_id='".$_POST['parentid']."'";
   @$result_1  =  exec_query($sql_1);
  $numcount_1 = count($result_1);
  // for else if  condition
   $sql1_1     =   "select  title from layout_menu where title='$post_title' and id='".$_POST['id']."'";
   @$result1_1  =  exec_query($sql1_1);
  $numcount1_1 = count($result1_1);
  if($numcount_1=='0')
  {
  $update      = $update =update_query("layout_menu",$editsubmenucol,$editsubmenuid);
  print"<script language='javascript'>window.location='manage_submenu.php?id=".$_POST['parentid']."';</script>";
  }
  elseif($numcount1_1 =='1')
  {
  $update      = $update =update_query("layout_menu",$editsubmenucol,$editsubmenuid);
  print"<script language='javascript'>window.location='manage_submenu.php?id=".$_POST['parentid']."';</script>";
  }
  else
  {
   print"<script language='javascript'>
   window.location='edit_submenu.php?id=".$_REQUEST['id']."&pid=".$_REQUEST['pid']."&msg=ero';
   </script>";

  }
  }
 $arrtitle_s =  stripslashes($arrresult[0]['title']);
$content      ="<form action='edit_submenu.php' method='post' name='submenu' onSubmit='return validate();'>
                <input type='hidden' name='id' value='".$pid ."' id='id' />
				<input type='hidden' name='parentid' value='".$arrresult[0]['parent_id'] ."' id='parentid' />
                <table width='100%' cellpadding='5' cellspacing='2' border='0'>
				<tr ><td valign='middle' colspan='5' align='left' class='admin_module_head'><a href='manage_menu.php'>
				Main Category</a>&nbsp;---><a href='manage_submenu.php?id=".$_GET['pid']."'>".
				ucwords(strtolower(stripslashes($title[0]['title']) ))."</a></td></tr>
				<tr class='highlight_row'><td width='17%'  align='right' valign='middle'>
				ID&nbsp;:&nbsp;</td><td width='83%' class='highlight_row'>". $_REQUEST['id']."</td></tr>
                <tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Title&nbsp;:&nbsp;</td>
                <td width='83%' align='left' valign='middle'><input type='text' name='txttitle' id='txttitle'
			     value=\"$arrtitle_s\" /></td></tr>";
$page         =  'select  name,id from layout_pages order by name';
$pageresult   =   exec_query($page);
$content     .="<tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Page Url&nbsp;:&nbsp;</td>
                <td width='83%' align='left' valign='middle' class='highlight_row'><select name='txtpageid'>

                <option  value=''>--Select--</option>";
                foreach($pageresult as $res)
				 {

                   if($res['id']== $arrresult[0]['page_id']){$select =  'selected';} else{$select ='';}
$content    .="<option  value='".$res['id'] ."' $select >".$res['name']." </option>";
                 }
$content   .="</select></td></tr>";
$content   .="<tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Parent Item&nbsp;:&nbsp;</td>
              <td width='83%' align='left' valign='middle' class='highlight_row'>";

$parent_menu        = "select  title,id from layout_menu where  parent_id='0' and active='1' order by title";
$parent_result      =  exec_query($parent_menu);
$content   .="<select name='txtparentid'><option  value=''>--Select--</option>";
foreach($parent_result as $parent) {
                  if($parent['id']== $arrresult[0]['parent_id']){$selected = 'selected';} else{$selected ='';}
$content   .="<option  value='".$parent['id'] ."'$selected >".$parent['title']."</option>";
			   }
$content   .="</select></td></tr><tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Published&nbsp;:&nbsp;</td>";

              if($arrresult[0]['active']=='1'){$checked = 'checked';}elseif($arrresult[0]['active']=='0'){$check ='checked';}
$content   .="<td width='83%' align='left' valign='middle' class='highlight_row'><input name='txtpublished' type='radio' value='1' $checked/>
               &nbsp;Yes &nbsp;&nbsp;<input name='txtpublished' type='radio' value='0' $check>&nbsp;No</td></tr>
              <tr><td  colspan='2' valign='middle' align='center' ><input type='submit' name='Update' value='update'
			  class='submit_button'/>
			  &nbsp;&nbsp;&nbsp;<input type='button' name='Back' value='Back' onClick='moveprev("."1".");' class='submit_button' />
			  </td></tr></table></form>	";

return  $content;
}
//-----------------------------------------------
// edit submenu(3/12/2008)
//-----------------------------------------------

function displayfooter()
{
    if($_REQUEST['mode']!='' and $_GET['aid']!='')
          {
             $action = footerorder($_GET['aid'],$_REQUEST['mode']);
           }
    $footer =  getfootermenu('All','');
	 foreach($footer as $footer)
	 {

       $content  .=     "<tr  class='highlight_row'>
	                    <td valign='middle'>".stripslashes($footer['title'])."</td>";
					       if($footer['active']=='1')
				           {
				             $img = "<img src='../http://storage.googleapis.com/mvassets/images/redesign/tick.png'  />";
				            }
				          else
				               {
				                 $img = "<img src='../http://storage.googleapis.com/mvassets/images/redesign/publish_x.png'  />";
				               }

	                $content  .= "<td valign='middle'>".$img." </td>

	                             <td valign='middle'><a href='manage_edit_footer.php?id=".$footer['id']."'>
					             edit</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href='manage_footermenu.php?aid=".$footer['id'].                                   "&mode=up'>up</a>&nbsp;&nbsp;
			                     <a  href='manage_footermenu.php?aid=".$footer['id']."&mode=down'>down
					            </a></td></tr>";
	}
return  $content;
}

function editfooter($pid)
{
  $cacheFooter= new Cache();
  $arrresult  = getfootermenu('Selected',$pid);

  if(isset($_POST['Update']) and $_POST['Update']=='update')
  {
     $post_title =  addslashes(htmlentities(strtoupper($_POST['txttitle'])));
    $editsubmenucol['title']     = addslashes(htmlentities(strtoupper($_POST['txttitle'])));
	$editsubmenucol['page_id']   = $_POST['txtpageid'];
	$editsubmenucol['active']    = $_POST['txtpublished'];
	$editsubmenuid['id']         = $_POST['id'];
	 $sql_1     =   "select  title from layout_menu where title='$post_title' and  Isnull(parent_id)";
     @$result_1  =  exec_query($sql_1);
     $numcount_1 = count($result_1);
   // for else if  condition
    $sql1_1     =   "select  title from layout_menu where title='$post_title' and id='".$_POST['id']."'";
    @$result1_1  =  exec_query($sql1_1);
   $numcount1_1 = count($result1_1);
  if($numcount_1=='0')
  {
    $update =update_query("layout_menu",$editsubmenucol,$editsubmenuid);
    print"<script language='javascript'>window.location='manage_footermenu.php';</script>";
  }
  elseif($numcount1_1 =='1')
  {
    $update =update_query("layout_menu",$editsubmenucol,$editsubmenuid);
    print"<script language='javascript'>window.location='manage_footermenu.php';</script>";
  }
  else
  {
   print"<script language='javascript'>
   window.location='manage_edit_footer.php?id=".$_REQUEST['id']."&msg=ero';
   </script>";

  }


    }
	 $valuetitle =  stripslashes($arrresult[0]['title']);
$content      ="<form action='manage_edit_footer.php' method='post' name='submenu' onSubmit='return validate();'>
                <input type='hidden' name='id' value='".$pid ."' id='id' />
                <table width='100%' cellpadding='5' cellspacing='2' border='0'>
                <tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Title&nbsp;:&nbsp;</td>
                <td width='83%' align='left' valign='middle'><input type='text' name='txttitle' id='txttitle'
			     value=\"$valuetitle\"  maxlength='200'/></td></tr>";
$page         =  'select  name,id from layout_pages order by name';
$pageresult   =   exec_query($page);
$content     .="<tr class='highlight_row'><td width='17%'  align='right' valign='middle'>Page Url&nbsp;:&nbsp;</td>
                <td width='83%' align='left' valign='middle'><select name='txtpageid'>

                <option  value=''>--Select--</option>";
                foreach($pageresult as $res)
				 {

                   if($res['id']== $arrresult[0]['page_id']){$select =  'selected';} else{$select ='';}
$content    .="<option  value='".$res['id'] ."' $select >".$res['name']." </option>";
                 }
$content   .="</select></td></tr>";
$content   .="</select></td></tr><tr class='highlight_row'><td width='17%' align='right' valign='middle'>Published&nbsp;:&nbsp;</td>";

              if($arrresult[0]['active']=='1'){$checked = 'checked';}elseif($arrresult[0]['active']=='0'){$check ='checked';}
$content   .="<td width='83%' align='left' valign='middle'><input name='txtpublished' type='radio' value='1' $checked/>
               &nbsp;Yes &nbsp;&nbsp;<input name='txtpublished' type='radio' value='0' $check>&nbsp;No</td></tr>
              <tr><td  colspan='2' valign='middle' align='center' ><input type='submit' name='Update' value='update' class='submit_button'/>
			  &nbsp;&nbsp;&nbsp;<input type='button' name='Back' value='Back' onClick='editmenu();' class='submit_button'/>
			  </td></tr></table></form>	";
  $cacheFooter->deleteFooterQuickLinksCache();
return  $content;

}

 function  footerdisplay($arFooterLinks)
 {
   global $HTHOST,$HTPFX,$IMG_SERVER;
  //$arFooterLinks = getfootermenu('active','');
	$i =1;
	foreach($arFooterLinks->arFooterLinks as $key => $arFooterItem)
	{
		$rel = "";
		 if(substr($arFooterItem['alias'],0,1) == "/")
		 {
			 $stURL = $HTPFX.$HTHOST.$arFooterItem['alias'];
		 }
		 else
		 {
			$stURL = "http://".$arFooterItem['alias'];
		 }
		 $content   .="<span><a href='".$stURL."' rel='".$rel."'>".stripslashes($arFooterItem['title'])."</a></span> ";
		if($i != count($arFooterLinks))
		 {
		 	$content   .="&nbsp;<img src='".$IMG_SERVER."/images/redesign/quick_link_divder.gif'>&nbsp;&nbsp;";
		 }
		 $i++;
	}
	return  $content;
 }

 function make_ajax_pagination_photo($divid,$link,$count,$MAX,$numrows1)
{

	$strPage="<div class='sliding_controller'><table  cellspacing='5' cellpadding='1' border='0' align='center'><tr>";
	$lastrowtfollowme=$MAX+$count;
	if($count > 0)
	{
		$prevpagetfollowme=$count - $MAX;
		$url=$link."?count=$prevpagetfollowme";
		$strPage.="<td><a onclick=\"Javascript:getPhotos('$divid','$url');\" style='cursor:pointer;' ><img src=$IMG_SERVER/images/previous_slide_button.jpg border=0 /></a></td>";
	}
	else
	{
		$strPage.="<td><img src=$IMG_SERVER/images/previous_slide_button.jpg border=0 /></td>";
	}

	for($pagelooptfollowme=1;$pagelooptfollowme<=ceil($numrows1/$MAX);$pagelooptfollowme++)
	{
		$pagedatatfollowme=($pagelooptfollowme-1)*$MAX;
		if($count!=$pagedatatfollowme)
		{
			$url=$link."?count=$pagedatatfollowme";
			$strPage.="<td><a onclick=\"Javascript:getPhotos('$divid','$url');\"  style='cursor:pointer;'><img src=\"$IMG_SERVER/images/hide_slide_button.jpg\" border=\"0\" /></a></td>";
		}
		else
		{
			$strPage.="<td><img src=\"$IMG_SERVER/images/selected_slide_button.jpg\" border=\"0\"/></td>";
		}
	}
	if($numrows1 > $lastrowtfollowme)
	{
			$url=$link."?count=$lastrowtfollowme";
			$strPage.="<td><a onclick=\"Javascript:getPhotos('$divid','$url');\" style='cursor:pointer;' ><img src=$IMG_SERVER/images/forward_slide_button.jpg border=0 /></a></td>";
	}
	else
	{
			$strPage.="<td><img src=$IMG_SERVER/images/forward_slide_button.jpg border=0 /></td>";
	}
	$strPage.="</tr></table></div>";

return $strPage;

}//end of function function make_ajax_pagination_photo()

 function getLayoutPages($metalist,$pageid){
 global $HTPFX,$HTHOST;
	$page_id=$pageid;
 	$title=$metalist['title'];
	$description=$metalist['metadesc'];
	$keyword=$metalist['metakeywords'];
	$page_url = $metalist['url'];
	if($page_url !="")
	{
	$page_url=$HTPFX.$HTHOST.$page_url;
	}

 ?>
		<table width="200px" border="0" cellpadding="8" cellspacing="0">
		<tr>
			<td colspan="2"><div style="color:#FF0000;" id="showmsg"></div></td>
		</tr>
		  <tr>
			<td nowrap="nowrap">Page Name:</td>
			<td><select name="pageid" id="pageid" style="width:300px;" onChange="returnpagename()">
							<option>-All Pages-</option>
				<? selectHashArr(getPageList(),"id","name",$page_id); ?>
				</select></td>
		  </tr>
		 <? if($page_url !="") {?>
          <tr>
			<td nowrap="nowrap">Page URL:</td>
			<td><a href="<?=$page_url?>" target="_blank"><?=$page_url?></a></td>
		  </tr>
          <? } ?>
          <tr>
			<td nowrap="nowrap">Title:</td>
			<td><?input_text("title",$title,"15","2000","style=width:300px;")?></td>
		  </tr>
		  <tr>
			<td nowrap="nowrap">Keyword:</td>
			<td><?input_text("keyword",$keyword,"15","2000","style=width:300px;")?></td>
		  </tr>
		  <tr>
			<td>Description:</td>
			<td><?input_text("description",$description,"15","2000","style=width:300px;")?></td>
		  </tr>
		   <tr>
			<td></td>
			<td><input type="submit" name="save" value="save" onclick="insertPageData();" /></td>
		  </tr>
		</table>

 <?
 }





function renderFeatureModule_2()
{
$arrFeature = getfeaturearticle();
?><!-- <div id="mygallery" class="stepcarousel">
<div class="belt"> -->
<?
 $i = 1;
$data ='';
//$wrapperDivStart = '<div id="deepika"><div id="wrapper">';
//$sliderDivStart = '  <div id="slider">';
 //$navigatorStart = '<ul class="navigation">';
 foreach($arrFeature as $Feature)
 {
    $article_v = getarticle_video($Feature['object_type'], $Feature['object_id']);
    $tagarticle= gettagarticle($article_v[0]['id'],$Feature['object_type']);
	if($Feature['object_type']=='1')
	{
	 $readmore =  makeArticleslinkTemp($article_v[0]['id'],$article_v[0]['keyword'],$article_v[0]['blurb'],$from=NULL,$page=NULL);
	 $urlauthor = $article_v[0]['contrib_id'];
	 $authorurl = $HTPFX.$HTHOST."/gazette/bios.htm?bio=$urlauthor";
	}
	else
	{
	 $videoid  =  $article_v[0]['id'];

	  $readmore  =  $HTPFX.$HTHOST."/mvtv/audio_video.htm?videoid=$videoid";
	  $urlauthor = '#';
	  $authorurl = '#';
	  $sectionid =getVideoSubsectionid($videoid);
      $pageDetail['id']=$sectionid['page_id'];
      $section_id=$sectionid['section_id'];
      $sec_name=$sectionid['name'];
	}
	 if($article_v[0]['featureimage']!='')
	 {
	   $img       = $IMG_SERVER.$article_v[0]['featureimage'];
	  }
	  else
	  {
	   $img ="images/feature_slide.jpg";
	  }
    // Code for Selected Slide
	$stClassSlide1 = $stClassSlide2 = $stClassSlide3 = $stClassSlide4 =	$stClassSlide5 =  'feature_nav_button';
	switch($i)
	{
	case '1':
	$stClassSlide1 = "feature_nav_button_selected";
	break;
	case '2':
	$stClassSlide2 = "feature_nav_button_selected";
	break;
	case '3':
	$stClassSlide3 = "feature_nav_button_selected";
	break;
	case '4':
	$stClassSlide4 = "feature_nav_button_selected";
	break;
	case '5':
	$stClassSlide5 = "feature_nav_button_selected";
	break;
	}

//$navigatorLi .= '<li><a href="#nav_'.$i.'">'.$i.'</a></li>';

$data .= '<div class="panel" id="nav_'.$i.'"<img src="images/featureslide.jpg" border="0" /></div>';

 $i++;

}// end of for loop

//$navigatorEnd  = '</ul>';
//$leftScroll= '<img src="images/slideprivButtom.jpg" class="scrollButtons left"/>';
$scrollContainerStart = '<div class="scroll"><div class="scrollContainer">';
$scrollContainerEnd= ' </div></div>';
//$rightScroll = '<img src="images/slidenextButton.jpg" class="scrollButtons right"/>';
//$shade = ' <div id="shade"></div>';
//$wrapperAndsliderClose = ' </div></div>';


//return $data;
$content = $scrollContainerStart.$data.$scrollContainerEnd;
	//$wrapperDivStart.$sliderDivStart.$navigatorStart.$navigatorLi.$navigatorEnd.$leftScroll.$scrollContainerStart.$data.$scrollContainerEnd.$rightScroll.$shade.$wrapperAndsliderClose;




return $content;

}
function renderFeatureModuleSlidesBuyHedge(){
	 global $HTPFX,$HTHOST,$IMG_SERVER, $D_R;
include_once($D_R."/lib/_content_data_lib.php");
// Get List of feature articles

$sqlGetFeaturedArticles="SELECT id,title,body,image_name img_name,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS DATE FROM buyhedge_posts WHERE category_id IN (1) AND is_approved='1' AND is_deleted='0' AND is_sent='1' AND is_draft='0' ORDER BY `date` DESC LIMIT 3";
$resGetFeaturedArticles=exec_query($sqlGetFeaturedArticles);

$dataContent='';
$i=1;


foreach($resGetFeaturedArticles as $key => $val){

	$objContent = new Content("buyhedge");
	$link = $HTPFX.$HTHOST.$objContent->getBuyHedgeUrl($val['id']);
	$bodyPost=$objContent->getCountWords(strip_tags($val['body']),50);
	$bodyPost.='&nbsp;<a href="'.$link.'" target="_parent">more &raquo; </a>';
	$dataContent.='<div class="panel" ""'.$slideClass.'" id="feature-item-'.$i.'"/>

	<div class="main_banner">
            <div class="main_banner_left">
                <div class="latest_news">Feature Articles</div>
                <div class="text_area">
					<div class="text_area_heading"><a href="'.$link.'" >'.$val[title].'</a></div>
					<div class="text_area_content">'.$bodyPost.' </div>
                </div>

            </div>

            <div class="main_banner_right">';
			if($val['img_name']!="")
            {
                $dataContent.='<img src="'.$IMG_SERVER.'/assets/buyhedge/featured/'.$val[img_name].'" />';
            }
            else
            {
            	$dataContent.='<img src="'.$IMG_SERVER.'/images/buyhedge/buyhedge220x220.jpg" />';
            }
			$dataContent.='</div>

    </div>
</div>
	';
	if ($i==1) $strClass="featurenav current";
	else $strClass="featurenav";
	$liLinks .= '<div onClick="Javascript:doEffect('.$i.');" class ="'.$strClass.'" id="featurenav_'.$i.'" >'.$i.'</div>';//class="nav_numbers"
	$i++;
}
					$navBar ='<div class="mainfeature_pagination">

							'.$liLinks.'
				   		<div class="nav_see_all">
						<span style="margin-right:10px"><a style="color:#FBC134;" href="'.$HTPFX.$HTHOST.'/buyhedge/allpost.htm">See All &raquo; </a></span>
						</div>
						</div>';


$featureMain ='
							 <div id="main-feature" class="featureSlideMainETF">
							 <div class="main-feature-holder">
								 <div class="feature-wrapper">
									 <div class="display-holder">
									'.$dataContent.'
									</div>
								</div>
							'.$navBar. //Don't change the position of navigation bar slider will stop working
							'</div>
					</div>';

	$featureMain .='<INPUT TYPE="hidden" id="adslidetime" name="adslidetime" VALUE="'.$arslideDecode['1']->timeDuration.'">';
	$featureMain .='<INPUT TYPE="hidden" id="adUnitRefreshCheck" name="adUnitRefreshCheck" VALUE="1">';
	$featureMain .='<INPUT TYPE="hidden" id="adUnitReplayTime" name="adUnitReplayTime" VALUE="0">';
	$featureMain .='<INPUT TYPE="hidden" id="slider_ad_frequency" name="slider_ad_frequency" VALUE="'.$arslideDecode['1']->frequency.'">';
   return	$featureMain ;
}

function renderEduFeatureModuleSlides($placeHolder){

	global $IMG_SERVER;
	
	$sqlGetModules="Select module_order from layout_placeholder where placeholder='".$placeHolder."'";
	$resGetModules=exec_query($sqlGetModules,1);
	$modules=explode(",",$resGetModules['module_order']);
	
	$str= '<div class="container"><div id="slides" style="width:620px; height:350px; float:left;">';
	foreach ($modules as $module){
		$str .= "<div class='figure cap-bot'>".renderTemplateContent($module)."</div>";
	}
	$str .= '<a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"><img src="'.$IMG_SERVER.'/images/education/backArrow.png"></i></a>
      <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"><img src="'.$IMG_SERVER.'/images/education/fwdArow.png"></i></a>  </div>  </div>';
	return $str; 
}

function renderFeatureModuleSlides($preview=false){
	 global $HTPFX,$HTHOST,$IMG_SERVER, $D_R;
	include_once($D_R."/lib/_content_data_lib.php");
	if($preview === true){
		$sqlGetModules="Select module_order_temp from layout_placeholder where placeholder='Home Page Featured Module'";
		$resGetModules=exec_query($sqlGetModules,1);
		$modules=explode(",",$resGetModules['module_order_temp']);
	}else{
		$sqlGetModules="Select module_order from layout_placeholder where placeholder='Home Page Featured Module'";
		$resGetModules=exec_query($sqlGetModules,1);
		$modules=explode(",",$resGetModules['module_order']);
	}

	$img_pattern = '/<img[^>]+src[\\s=\'"]+([^"\'>]+)/is';
	$i=0;
	foreach($modules as $module)
	{
		$sqlMod="select template_id from layout_module where module_id ='".$module."'";
		$modResult=exec_query($sqlMod,1);
		if($preview === true){
				$moduleData=getTempModuleComponent($module,$modResult['template_id']);
			}else{
				$moduleData=getModuleComponent($module,$modResult['template_id']);
			}
		if($modResult['template_id'] ==  '19' && $preview == 'no_ad') // don't fetch ad module
		{
			continue;
		}
		foreach($moduleData as $component)
		{
			switch($component['component_name'])
			{
				case 'TEMPLATE9_GRAPHICS':
					preg_match($img_pattern,$component['content'],$match);
					$slides[$i]['FeaturedImage']=$match[1];
					break;
				case 'TEMPLATE22_GRAPHICS':
					preg_match($img_pattern,$component['content'],$match);
					$slides[$i]['FeaturedImage']=$match[1];
					break;
				case 'TEMPLATE9_MAIN_ARTICLE':
					if($component['component_type'] == 'custom_url')
					{
						if(strpos($component['content'],"http://") === false)
						{
							$component['content'] = "http://".$component['content'];
						}
						$slides[$i]['FeaturedArticleLink']=$component['content'];
					}
					else
					{
						$main_article=explode(",",$component['content']);
						$arItemDetail=explode(":",$main_article[0]);
						if($arItemDetail[1] == 1)
						{
							$arItemData = getArticleDetail($arItemDetail[0]);
							$arItemData['description'] = $arItemData['character_text'];
						}
						else
						{
							$obContent = new Content("","");
							$arItemData = $obContent->getMetaSearch($arItemDetail[0],$arItemDetail[1]);
						}
						$slides[$i]['FeaturedArticleTitle']=$arItemData['title'];
						$slides[$i]['FeaturedArticleAuthor']=$arItemData['author'];
						$slides[$i]['FeaturedArticleAuthorLink']=$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'];
						$slides[$i]['FeaturedArticleDesc']=$arItemData['description'];
						$slides[$i]['FeaturedArticleLink']=$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail[0]);
					}
					break;
				case 'TEMPLATE22_MAIN_ARTICLE':
					if($component['component_type'] == 'custom_url')
					{
						if(strpos($component['content'],"http://") === false)
						{
							$component['content'] = "http://".$component['content'];
						}
						$slides[$i]['FeaturedArticleLink']=$component['content'];
					}
					else
					{
						$main_article=explode(",",$component['content']);
						$arItemDetail=explode(":",$main_article[0]);
						if($arItemDetail[1] == 1)
						{
							$arItemData = getArticleDetail($arItemDetail[0]);
							$arItemData['description'] = $arItemData['character_text'];
						}
						else
						{
							$obContent = new Content("","");
							$arItemData = $obContent->getMetaSearch($arItemDetail[0],$arItemDetail[1]);
						}
						$slides[$i]['FeaturedArticleTitle']=$arItemData['title'];
						$slides[$i]['FeaturedArticleAuthor']=$arItemData['contributor'];
						$slides[$i]['FeaturedArticleAuthorLink']=$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'];
						$slides[$i]['FeaturedArticleDesc']=$arItemData['description'];
						$slides[$i]['FeaturedArticleLink']=$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail[0]);
					}
					break;
				case 'TEMPLATE9_ARTICLE1':
					$component['content'] = substr($component['content'],0,-1);
					$article1=explode(",",$component['content']);
					$j = 1;
					 foreach($article1 as $IdPair)
					 {
					 	$arItemDetail=explode(":",$IdPair);
						if($arItemDetail[1] == 1)
						{
							$arItemData = getArticleDetail($arItemDetail[0]);
							$arItemData['description'] = $arItemData['character_text'];
							$arItemData['link']=$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail[0]);
						}elseif(!is_numeric($arItemDetail[0])){
							$arItemData['title']=$arItemDetail[1];
							if(strpos($arItemDetail[0],"minyanville.com")){
								$arItemData['link']=$HTPFX.$arItemDetail[0];
							}else{
								$arItemData['link']=$HTPFX.$arItemDetail[0];
							}
						}
						else
						{
							$obContent = new Content("","");
							$arItemData = $obContent->getMetaSearch($arItemDetail[0],$arItemDetail[1]);
							$arItemData['link']=$HTPFX.$HTHOST.getItemURL($arItemDetail[1],$arItemDetail[0]);
						}
						if(strlen($arItemData['title'])>42)
						$arItemData['title']=substr($arItemData['title'],0,40)."...";
						$slides[$i]['RelatedArticle'.$j.'Title']=$arItemData['title'];
						$slides[$i]['RelatedArticle'.$j.'Author']=$arItemData['contributor'];
						$slides[$i]['RelatedArticle'.$j.'AuthorLink']=$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$arItemData['author_id'];
						$slides[$i]['Related1Article'.$j.'Desc']=$arItemData['description'];
						$slides[$i]['RelatedArticle'.$j.'Link']=$arItemData['link'];
						$j ++;
					}
					break;

				case 'TEMPLATE19_TEXT':
					$arslideDecode[]="";
							$json = new Services_JSON();
							$arslideDecode[] =  $json->decode($component['content'],true);
							$slides[$i]['FeaturedSlide'] = $arslideDecode['1']->content;
					break;
			}
		}
		$i++;
	}

	$i =1;
	$liLinks='';
	foreach($slides as $id=>$slide){

		$liLinks .= '<div id="featurenav_'.$i.'" class="featurenav">'.$i.'</div>';
		$data .= '<div class="panel" ""'.$slideClass.'" id="feature-item-'.$i.'"/>';
		if($slide['FeaturedSlide'] !=""){
				$data .= '<div id="feature_ad">'.$slide['FeaturedSlide'].'</div>';
		}else{
		$data .='<img border="0" width="653" height="258" src="'.$slide['FeaturedImage'].'" usemap="#Map_'.$i.'" >';
		$data .='<map name="Map_'.$i.'" id="Map_'.$i.'"><area shape="poly" coords="402,122" href="'.$slide['FeaturedArticleLink'].'" /><area shape="poly" coords="496,163" href="'.$slide['FeaturedArticleLink'].'" /><area shape="poly" coords="4,130,5,4,668,3,349,133" href="'.$slide['FeaturedArticleLink'].'"  alt="Feature" /><area shape="poly" coords="659,132,664,252,352,251,351,136,663,6,669,9,668,245" href="'.$slide['FeaturedArticleLink'].'" /><area shape="poly" coords="658,249,658,256,351,255,354,240,354,98,651,8" href="'.$slide['FeaturedArticleLink'].'" /></map>';

		$data .= '<div class="Alsoby"><div id="also-header">Also:</a></div>';

		$data .='<div class="also-link"><a href="'.$slide['RelatedArticle1Link'].'">'.$slide['RelatedArticle1Title'].'</a></div>';
		$data .='<div class="also-link"><a href="'.$slide['RelatedArticle2Link'].'">'.$slide['RelatedArticle2Title'].'</a></div>';
		$data .='<div class="also-link"><a href="'.$slide['RelatedArticle3Link'].'">'.$slide['RelatedArticle3Title'].'</a></div>';
		$data .='</div>';
		}
		/*
		if($i==1)
				{
					$data .= '<div class="FeaturedSponsorShip"><A target="_blank" href="http://minyanville.checkm8.com/adam/ep/click/minyanville_main_site.home/Featured_Sponsorship?cat=minyanville_main_site.home">
<IMG src="http://minyanville.checkm8.com/adam/noscript?cat=minyanville_main_site.home&format=Featured_Sponsorship" target="_blank" border=0></A></div>';
		} */
		$data .='</div>';


 		$i++;
	}
	$navBar ='
						<div class="mainfeature_pagination">
							<div class="prev" id="stripNavL"><img class="left" style= "cursor:pointer;"src="'.$IMG_SERVER.'/images/prev-slide.png" alt="Previous Slide" /></div>
							'.$liLinks.'
   							<div class="next" id="stripNavR"><img class="right" style= "cursor:pointer;" src="'.$IMG_SERVER.'/images/next-slide.png" alt="Next Button" />
							</div>
							<div class="slide_play_pause">
							<span><a class="pause" href="javascript:void(0)" style="display: inline;"><img class="right" style= "cursor:pointer;" src="'.$IMG_SERVER.'/images/slide-pause.png" alt="Pause" title="Pause" align="top" /></a>
						<a class="play" href="javascript:void(0)" style="display: none;"><img class="right" style= "cursor:pointer;" src="'.$IMG_SERVER.'/images/next-slide.png" alt="Play" title="Play" align="top" /></a>
						</span>
							</div>
				   		<div class="slide_page_right">
						<span style="margin-right:10px"><a href="'.$HTPFX.$HTHOST.'/articles/articlelisting.htm">See All Articles &raquo; </a></span><span style="margin-right:10px"><a href="'.$HTPFX.$HTHOST.'/audiovideo/">See All Videos &raquo;</a></span><span><a href="'.$HTPFX.$HTHOST.'/gazette/bios.htm">Our Contributors &raquo;</a></span></div>
						</div>';
   $featureMain ='
							 <div id="main-feature" class="featureSlideMain">
							 <div class="main-feature-holder">
								 <div class="feature-wrapper">
									 <div class="display-holder">
									'.$data.'
									</div>
								</div>
							'.$navBar. //Don't change the position of navigation bar slider will stop working
							'</div>
					</div>';
	$featureMain .='<INPUT TYPE="hidden" id="adslidetime" name="adslidetime" VALUE="'.$arslideDecode['1']->timeDuration.'">';
	$featureMain .='<INPUT TYPE="hidden" id="adUnitRefreshCheck" name="adUnitRefreshCheck" VALUE="1">';
	$featureMain .='<INPUT TYPE="hidden" id="adUnitReplayTime" name="adUnitReplayTime" VALUE="0">';
	$featureMain .='<INPUT TYPE="hidden" id="slider_ad_frequency" name="slider_ad_frequency" VALUE="'.$arslideDecode['1']->frequency.'">';

   return	$featureMain ;

}

function displayAdUnitSlide(){
show_adds_checkmate("home",$profile="");
?>
<script src="http://minyanvilledigital.checkm8.com/adam/cm8adam_1_call.js" type="text/javascript"></script>

<script language="javascript">CM8ShowAd("682x260")</script>
<?
}

function getArticleLanding($start=0,$end){
	global $IMG_SERVER, $productItemType,$HTPFX,$HTHOST;
	$objCache=new Cache();
	$result=$objCache->getAllArticlesCache($start,$end);
	if($result){
		foreach ($result as $key=>$val){
			$title = mswordReplaceSpecialChars($val['title']);

			if(in_array($val['item_type'],$productItemType)){
				$url = $val['url'].'?camp=articlepremiumcontent&medium=home&from=minyanville';
			}else{
				$url = $val['url'];
			}

			if(in_array($val['item_type'],$productItemType)){
				$mvp_logo = '<img src="'.$IMG_SERVER.'/images/navigation/mvp_icon.jpg" style="margin:0px 0px 0px 2px;" />';
			}else{
				$mvp_logo = '';
			}
			echo "<li id='all-articles-bullet' ><a id='all-articles-text' href='".$HTPFX.$HTHOST.$url."'>".$title."</a>".$mvp_logo."<br />
<a id='all-articles-contrib' href='".$HTPFX.$HTHOST."/gazette/bios.htm?bio=".$val['author_id']."'>".$val['author']."</a></li>";

		}
	}else{
		return false;
	}
}

function getProductQuery(){
	global $D_R;
	include_once("$D_R/lib/techstrat/_techstratData.php");
	$objTechStartData= new techstartData("techstrat_posts","");
	$qry ="SELECT EPC.item_id AS id, TP.title, C.NAME,EIT.author_id,IF(TP.publish_date,TP.publish_date,TP.creation_date) AS date, EIT.url,EIT.item_type FROM techstrat_posts TP, ex_item_premium_content EPC, contributors C, ex_item_meta EIT WHERE TP.id = EPC.item_id AND TP.contrib_id = C.id AND EIT.item_id=TP.id AND TP.is_approved='1' AND TP.is_live='1' AND TP.is_deleted='0' AND EPC.is_live='1' AND EIT.item_type='".$objTechStartData->contentType."' AND EPC.item_id = EIT.item_id";

	return $qry;
}

function getArticleListing($start,$end){
	$qryArticle = "SELECT A.id, A.title, C.NAME author, EIT.author_id, IF(A.publish_date,A.publish_date,A.DATE) AS date, EIT.url,EIT.item_type FROM articles A, contributors C,ex_item_meta EIT WHERE A.contrib_id = C.id AND A.approved='1' AND A.is_live='1' AND EIT.item_type='1' AND A.id = EIT.item_id";
	$qryProduct = getProductQuery();

	$qryMerge = $qryArticle." UNION ".$qryProduct." ORDER BY date DESC LIMIT ".$start.",".$end;
	$result = exec_query($qryMerge);
	if($result){
		return $result;
	}else{
		return false;
	}
}



function getBlogPostLayout(){
	global $IMG_SERVER, $HTPFX,$HTHOST;
	$objCache=new Cache();
	$result=$objCache->getBlogPostCache();


	if($result){
		foreach ($result as $key=>$val){
			$title 	= mswordReplaceSpecialChars($val['title']);
			$url 	= $val['url'];
			$author = mswordReplaceSpecialChars($val['author_name']);
			echo "<li id='all-articles-bullet' ><a id='all-articles-text' href='".$url."'>".$title."</a><br />
<a id='all-articles-contrib'>".$author."</a></li>";

		}
	}else{
		return false;
	}
}

function getPartOfBody($articleBody,$numOfWords){
	$firstLinePattern = '/^(.*)$/m';
	$articleBody = strip_tags($articleBody);
	$body = mswordReplaceSpecialChars($articleBody);
	$wordCount = count(str_word_count($body,1));
	if($wordCount>$numOfWords){
		$words=explode(" ",$body,$numOfWords+1);
		unset($words[$numOfWords]);
		$body=implode(" ",$words);
		$body=$body." ...";
	}
	return $body;
}

?>
