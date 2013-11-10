<?php
//-----------------------------------------------------------------------------
// query the database for all the pages. returned as options
//-----------------------------------------------------------------------------
function createArticlesLink($id,$keyword=NULL,$blurb=NULL,$from=NULL,$page=NULL)
 {
  	if($blurb)
    {
	    $newline_char=array("\r\n", "\n", "\r");
	    $blurb=str_replace($newline_char," ",$blurb);
	}
	$urlPart = '';
	if($keyword)
	{
		$urlPart .= urlencode($keyword)."/";
	}
	if($blurb)
	{
		$urlPart .= urlencode($blurb)."/";
	}
	$link="/articles/".$urlPart."index/a/".$id;
	if($page)
		$link.="/p/".$page;
	if($from)
		$link.="/from/ameritrade";
	return $link;
 }
 function makeArticleslink($id,$keyword=NULL,$blurb=NULL,$from=NULL,$page=NULL)
 {
	$objCache = new Cache();
 	return $objCache->getItemLink($id,'1');
 }
  function makeRssArticleslink($id,$type,$keyword=NULL,$blurb=NULL,$from=NULL,$page=NULL)
 {
	$objCache = new Cache();
 	return $objCache->getItemLink($id,$type);
 }
 function makeArticleslinkcooper($id,$keyword=NULL,$blurb=NULL,$from=NULL,$page=NULL)
 {
	return "/cooper".createArticlesLink($id,$keyword,$blurb,$from,$page);
 }

 function getAuthorDisclaimercooper($id) {
	if ($id == "") {
		$id = 0;
	}
	$sql = "SELECT disclaimer from cp_contributors where id=$id";
	$results = exec_query($sql,1);

	if(isset($results)){
				return $results['disclaimer'];
	}else{
		return "";
	}

}
function createForm($formname,$email,$pwd,$path,$flag,$refer){
	$html.='<Form  name="'.$formname.'" method="post" action="'.$path.'">';
	$html.='<input type="hidden" name="email" value="'.$email.'">';
	$html.='<input type="hidden" name="password" value="'.$pwd.'">';
	$html.='<input type="hidden" name="refer" value="'.$refer.'">';
	$html.='<input type="hidden" name="flag" value="'.$flag.'">';
	$html.='</Form>';
	echo $html;
}
function getAuthorInfocooper($author) {
	//determine if the given string is a number (ID)
	if ($author == "") {
		return 0;
	}
	if (is_numeric($author)) {
		$where = "id=" . $author;
	} else {
		// or it might be a name
		$where = "name='" .$author ."'";
	}

	$sql = "select id, name from cp_contributors where " . $where;
	$results  = exec_query($sql,1);
	return $results;

}
function getAuthorInfojack($author) {
	//determine if the given string is a number (ID)
	if ($author == "") {
		return 0;
	}
	if (is_numeric($author)) {
		$where = "id=" . $author;
	} else {
		// or it might be a name
		$where = "name='" .$author ."'";
	}

	$sql = "select id, name from contributors where " . $where;
	$results  = exec_query($sql,1);
	return $results;

}
// this function will correctly display the author or contributor of an article
// if no contributor is given, or = " " then display a link to the author link. If contributor is
//this function will return the real author of between when passed author and contributor.
function returnRealAuthorcooper($a,$contrib) {

	if($contrib=="") {
		$author = getAuthorInfocooper($a);
		return $author;
	} else {
		$contributor = getAuthorInfocooper($contrib);
		if (count($contributor) ){
			return $contributor;  //listed contributor is a valid contributor
		} else {
			$FakeContributor['name'] = $contrib;
			$FakeContributor['id'] = "0";
			return $FakeContributor;  //listed contributor is not a real contributor.. ie Hoofy
		}
	}
}

// this function will correctly display the author or contributor of an article
// if no contributor is given, or = " " then display a link to the author link. If contributor is
//this function will return the real author of between when passed author and contributor.
function returnRealAuthorjack($a,$contrib) {

	if($contrib=="") {
		$author = getAuthorInfojack($a);
		return $author;
	} else {
		//$contributor = getAuthorInfocooper($contrib);
		$contributor = getAuthorInfojack($contrib);
		if (count($contributor) ){
			return $contributor;  //listed contributor is a valid contributor
		} else {
			$FakeContributor['name'] = $contrib;
			$FakeContributor['id'] = "0";
			return $FakeContributor;  //listed contributor is not a real contributor.. ie Hoofy
		}
	}
}
function getArticlecooper($articleid) {
	$sql = "select articles.id id, articles.title, articles.prof_id , contributors_ps.name author, articles.contributor, contributors_ps.disclaimer,articles.position, contrib_id authorid, date, blurb, body, position, character_text, article_categories.title category, keyword ,character_images.asset imageURL ";
	$sql .= "from cp_articles articles, cp_contributors contributors_ps, article_categories, cp_character_images character_images ";
	$sql .= "where articles.id=" . $articleid . " and articles.contrib_id = contributors_ps.id and(character_images.id=articles.character_img_id or articles.character_img_id=0) limit 1 ";


	$results = exec_query($sql);
	$article = array();
    foreach($results as $row){
		$article['id'] = $row['id'];
		$article['title'] = $row['title'];
		$article['author'] = $row['author'];
		$article['authorid'] = $row['authorid'];
		$article['date'] = date('M d, Y g:i a',strtotime($row['date']));	//$row['date'];
		$article['blurb'] = $row['blurb'];
		$article['body'] = $row['body'];
		$article['position'] = $row['position'];
		$article['imageURL'] = $row['imageURL'];
		$article['character_text'] = $row['character_text'];
		$article['category'] = $row['category'];
		$article['contributor'] = $row['contributor'];
		$article['disclaimer'] = $row['disclaimer'];
		$article['positions'] = $row['positions'];
		$article['keyword'] = $row['keyword'];
		$article['prof_id'] = $row['prof_id'];

		//restate the author id
		$tempAuthor = returnRealAuthorcooper($article['authorid'],$article['contributor']);
		$article['authorid'] = $tempAuthor['id'];
		$article['author'] = $tempAuthor['name'];
		//rewrite the disclaimer
		$article['disclaimer'] = getAuthorDisclaimercooper($article['authorid']);
	}
	return $article;
}
function getArticlejack($articleid) {
	$sql = "select articles.id id, articles.title, articles.prof_id , contributors_ps.name author, articles.contributor, contributors_ps.disclaimer,articles.position, contrib_id authorid, date, blurb, body, position, character_text, article_categories.title category, keyword ,character_images.asset imageURL ";
	$sql .= "from jack_articles articles, contributors contributors_ps, article_categories, cp_character_images character_images ";
	$sql .= "where articles.id=" . $articleid . " and articles.contrib_id = contributors_ps.id and(character_images.id=articles.character_img_id or articles.character_img_id=0) limit 1 ";
	$results = exec_query($sql);
	$article = array();
    foreach($results as $row){
		$article['id'] = $row['id'];
		$article['title'] = $row['title'];
		$article['author'] = $row['author'];
		$article['authorid'] = $row['authorid'];
		$article['date'] = date('M d, Y g:i a',strtotime($row['date']));	//$row['date'];
		$article['blurb'] = $row['blurb'];
		$article['body'] = $row['body'];
		$article['position'] = $row['position'];
		$article['imageURL'] = $row['imageURL'];
		$article['character_text'] = $row['character_text'];
		$article['category'] = $row['category'];
		$article['contributor'] = $row['contributor'];
		$article['disclaimer'] = $row['disclaimer'];
		$article['positions'] = $row['positions'];
		$article['keyword'] = $row['keyword'];
		$article['prof_id'] = $row['prof_id'];

		//restate the author id
		//$tempAuthor = returnRealAuthorcooper($article['authorid'],$article['contributor']);
		//$article['authorid'] = $tempAuthor['id'];
		//$article['author'] = $tempAuthor['name'];
		//rewrite the disclaimer
		//$article['disclaimer'] = getAuthorDisclaimercooper($article['authorid']);
	}
	return $article;
}
function getcooprearticle($strProfessorId){
	$sql="select articles.id,articles.keyword,articles.blurb, contrib_id, title, date, contributor, cp_contributors.name author, character_text talkbubble
from cp_articles articles
INNER JOIN cp_contributors ON articles.contrib_id=cp_contributors.id
where articles.prof_id='$strProfessorId' and approved='1' and is_live='1' and is_featured='0' order by date desc limit 10";
	$result  = exec_query($sql);
	return $result;
}
//display recent headline in a 2 column table
function displayRecentHeadlinescooperlogged($titlebar,$sql,$zoneID) {
    global $HTPFX,$HTHOST,$IMG_SERVER;
  	$results = exec_query($sql);
	$counter = 1;
	?>
<table border="0" width="100%" cellpadding="5" cellspacing="3"   align="left" style="clear:both;">
	<tr>
		<td  valign="top" width="49%">
		<?
            foreach($results as $row){
				$counter++;
				$realauthorInfo = returnRealAuthorcooper($row['author'],$row['contributor']);

				$realauthorname = $realauthorInfo['name'];
				//display the article title with link to article.php
				// also show date and author

				// the function make links of article according to the keywords and headlines
				//$link=makeArticleslink($row);?>
				<a class="articleLink" href= <?=$HTPFX.$HTHOST.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);?>><div class="cooper_market_heading"  ><?= $row['title'] ?></div></a><?

				echo "<div style='text-decoration:none;color: #083d70;'>" . $realauthorname ."</div>" . chr(13);
				echo  "<div class=\"cooper_recent_common_heading\">". $row[talkbubble] ."</div>". chr(13);

			  ?>
			 <!-- <a href= <?= $pfk.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);?> class="ReadMore">Read more...</a>-->
			 <br />
				  <?
				if ($counter > (count($results)/2)) {
					echo "</td>" . chr(13) . "<td align=\"center\" valign=\"middle\" width=\"1px\" background='". $IMG_SERVER. "/images/recent_div_line.gif' style=\"background-repeat:repeat-y\"></td><td width=\"50%\">";
					$counter=0;
				}
			}

		?>
		</td>
	</tr>
	</table>

	<?

}
//display recent headline in a 2 column table
function displayRecentHeadlinesjacklogged($titlebar,$sql,$zoneID) {
  	$results = exec_query($sql);
	$counter = 1;

	?>
<table border="0" width="100%" cellpadding="5" cellspacing="3"   align="left" style="clear:both;">
	<tr>
		<td  valign="top" width="49%">
		<?
            foreach($results as $row){
				$counter++;
				//$realauthorInfo = returnRealAuthorcooper($row['author'],$row['contributor']);
				$realauthorInfo = returnRealAuthorjack($row['author'],$row['contributor']);
				$realauthorname = $realauthorInfo['name'];
				//display the article title with link to article.php
				// also show date and author

				// the function make links of article according to the keywords and headlines
				//$link=makeArticleslink($row);?>
				<a class="articleLink" href= <?= $pfk.makeArticleslinkjack($row['id'],$row['keyword'],$row['blurb']);?>><div class="cooper_market_heading"  ><?= $row['title'] ?></div></a><?

				echo "<div style='text-decoration:none;color: #083d70;'>" . $realauthorname ."</div>" . chr(13);
				echo  "<div class=\"cooper_recent_common_heading\">". $row[talkbubble] ."</div>". chr(13);

			  ?>
			 <!-- <a href= <?= $pfk.makeArticleslinkjack($row['id'],$row['keyword'],$row['blurb']);?> class="ReadMore">Read more...</a>-->
			 <br />
				  <?
				if ($counter > (count($results)/2)) {
					echo "</td>" . chr(13) . "<td align=\"center\" valign=\"middle\" width=\"1px\" background='". $pfx. "/images/recent_div_line.gif' style=\"background-repeat:repeat-y\"></td><td width=\"50%\">";
					$counter=0;
				}
			}

		?>
		</td>
	</tr>
	</table>

	<?

}
function displayRecentHeadlinescooper($titlebar,$strProfessorId,$zoneID, $logincooper) {
	global $_SESSION,$HTPFX,$HTHOST;
	$result = getcooprearticle($strProfessorId);
	$counter = 1;
/*	$height=488;$width=532;
	$url="$HTPFX$HTHOST/subscription/register/iboxindex.htm";*/
	foreach ($result as $row){
		if($counter<=5){
			$realauthorInfo = returnRealAuthorcooper($row['author'],$row['contributor']);
			$realauthorname = $realauthorInfo['name'];
			if(!isset($_SESSION['LoggedIn']) ) {
			//	loginRedirection();
			// $targeturl=$pfk.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);
			// $linkId="navlink_".$row['id'];
			// $label= '<div class="cooper_recent_heading" >'.$row['title'].'</div>' ;
			// echo iboxCall($linkId,$label,$url,$height, $width,$targeturl);
			// echo '<div class="cooper_recent_heading" >'.$row['title'].'</div>' ;
			?>
            <a class="articleLink" href= <?= $pfk.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);?>><div class="cooper_recent_heading" ><?= $row['title'] ?></div></a>
            <?

			}
			elseif($_SESSION['LoggedIn'] && !$logincooper){
			?>
			<a class="articleLink" style="cursor:pointer;" onclick="Javascript:alert('Please register for Cooper. ');document.location.href='<?$HTPFX.$HTHOST?>/register/'"><div class="cooper_recent_heading" ><?= $row['title'] ?></div></a>
			<?
			}

			else  {
			?>
			<a class="articleLink" href= <?= $pfk.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);?>><div class="cooper_recent_heading" ><?= $row['title'] ?></div></a>
			<?
			}
			echo  "<div class=\"cooper_recent_common_heading\">". $row[talkbubble] ."</div>". chr(13);
		}
		$counter++;
	}
}

function get_article_title_cooper($articleid){
	$arrArticleIds=explode(",",$articleid);
	if(count($arrArticleIds)>1){
		return "Articles";
	}
	else{
		$qry="select title from cp_articles where id=$articleid";
		$result=exec_query($qry,0);
		if(isset($result)){
			$title = $result['0']['title'];
		}
		return $title;
	}
}
function get_article_title_jack($articleid)
{
	$arrArticleIds=explode(",",$articleid);
	if(count($arrArticleIds)>1){
		return "Articles";
	}
	else{
		$qry="select title from jack_articles where id=$articleid";
		$result=mysql_query($qry);
		if(isset($result)){
			$row=mysql_fetch_row($result);
		}
		return $row[0];
	}
}
/***********************************************************************/
function tagstockinfo($tag)
{
	$tag_query="SELECT CompanyName,exchange FROM ex_stock
	WHERE stocksymbol='".$tag."'";

	$tag_result=exec_query($tag_query,1);
	if(count($tag_result)){
	 $CompanyName=$tag_result['CompanyName'];
	 $exchange=$tag_result['exchange'];
	}
	return $tag_result;
}
function is_exchange($subscription_id)
{
	$exchange_query="SELECT s.is_exchange is_exchange,s.type stype FROM subscription s
	WHERE id='".$subscription_id."' and is_exchange='1'";

	$exchange_result=exec_query($exchange_query,1);
	if(count($exchange_result)){
	 $isExchange=1;
	}
	else
	{
	 $isExchange=0;
	}
	return $isExchange;
}

function checkblockip()
{
	$exchange_blockip="select BIP.ip from bocked_ip BIP, subscription SUB where SUB.is_blockip='1' and BIP.subscription_id=SUB.id";
	$exchange_blockipresult=exec_query($exchange_blockip);
	$countresult=count($exchange_blockipresult);
	$blockiparray='';
	if($countresult>0){
		foreach($exchange_blockipresult as $rowexchange_blockip)
		{
			if($blockiparray=='')
			{
				$blockiparray=$rowexchange_blockip['ip'];
			}
			else
			{
				$blockiparray.=",".$rowexchange_blockip['ip'];
			}
		}
	}
	return $blockiparray;
}

function getuserproductsub($email) {
		$qry="select id,prof_id,combo,combo_id,is_buzz from subscription_ps where account_status='enabled' and email='$email' order by prof_id desc";
		$subs_ps=exec_query($qry);
		$buzz=1;
		$corp=1;
		$quint=1;
		$productdetails=array();
		$sub_ps=$subs_ps[0];
		$subps=$subs_ps[1];

		if($subs_ps[0] && !$subs_ps[1]){

		    if($sub_ps['prof_id']==0 && $sub_ps['combo']==1 && $sub_ps['combo_id']==1){
			    $quint=0;
				$productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
			}elseif($sub_ps['prof_id']==0 && $sub_ps['combo']==1 && $sub_ps['combo_id']==2){
				$corp=0;
			    $productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
			}elseif($sub_ps['prof_id']==2 && $sub_ps['combo']==0 && $sub_ps['combo_id']==0){
			   $buzz=0;
			   $quint=0;
			   $productdetails['buzz']=$buzz;
			   $productdetails['coper']=$corp;
			   $productdetails['quint']=$quint;
			}elseif($sub_ps['prof_id']==3 && $sub_ps['combo']==0 && $sub_ps['combo_id']==0){
			   $buzz=0;
			   $corp=0;
			   $productdetails['buzz']=$buzz;
			   $productdetails['coper']=$corp;
			   $productdetails['quint']=$quint;
			}
		}elseif($subs_ps[0] && $subs_ps[1]) {
		  if($sub_ps['prof_id']==3 && $subps['prof_id']==2){
				$subqry="select id from subscription where email='$email'";
			    $sub=exec_query($subqry,1);
				if($sub){
				   	$productdetails['buzz']=$buzz;
					$productdetails['coper']=$corp;
					$productdetails['quint']=$quint;
				}else{
				    $buzz=0;
					$productdetails['buzz']=$buzz;
					$productdetails['coper']=$corp;
					$productdetails['quint']=$quint;
				}
		  }

		 return $productdetails;
         }else{

		    $subqry="select id from subscription where email='$email'";
			$sub=exec_query($subqry,1);
			if($sub) {
				$corp=0;
				$quint=0;
				$productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
				return $productdetails;
			}

		 }

 				$buzz=0;
				$corp=0;
				$quint=0;
				$productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
				return $productdetails;
	}

//-----------------------------------------------------------------------------
// For validating user previlages as per subscription ID
//-----------------------------------------------------------------------------

function check_exprevilages($subscription_id,$blockservice_id="")
{
	$exchange_prevquery="select ebs.subscription_id,ebs.value,ebs.blockservice_id,es.serviceid from
ex_blockservices ebs,
ex_services es
where ebs.blockservice_id=es.id
and ebs.subscription_id='".$subscription_id."' and ebs.blockservice_id='".$blockservice_id."' and ebs.value='on'";
	$exchange_prevresult=exec_query($exchange_prevquery,1);
	if(count($exchange_prevresult)){
		return true;
	}
}
function allPagesDropList() {

$pageList = exec_query("SELECT * from layout_pages ORDER BY name");
foreach($pageList as $row){
$pages .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>" . chr(13);
}

return $pages;
}


//-----------------------------------------------------------------------------
// query the database for all the columns from a requested page.
//-----------------------------------------------------------------------------

function getPageColumns($rPage) {
$sql = "SELECT * from layout_columns where pageID=" . $rPage . " order by name";
$columnList = exec_query($sql);
foreach($columnList as $row){
$columns[] = $row;
}

return $columns;
}

//-----------------------------------------------------------------------------
// send in page name, return id
//-----------------------------------------------------------------------------

function getPageId($pageName) {
	$sql = "SELECT id from layout_pages where name='". $pageName . "' limit 1";
	$result = exec_query($sql);
     foreach($result as $row){
		$id = $row['id'];
	}

	return $id; //return the id of the requested page.
}


//-----------------------------------------------------------------------------
// send in page id and column name , return id. used in request column.
//-----------------------------------------------------------------------------

function getColumnId($pageid, $columnName) {
	$sql = "SELECT id from layout_columns where pageID=$pageid and name='$columnName' limit 1";
	$result = exec_query($sql);
     foreach($result as $row){
		$id = $row['id'];
	}

	return $id;
}

//-----------------------------------------------------------------------------
// query the database for all the module. returned as options
//-----------------------------------------------------------------------------

function allModulesDropList() {

$moduleList = exec_query("SELECT id, uniqueName from layout_modules ORDER BY uniqueName");
foreach($moduleList as $row){
$options .= "<option value='" . $row['id'] . "'>" . $row['uniqueName'] . "</option>" . chr(13);
}

return $options;
}


function moduleExists($moduleid) {
	$module_data = array();
	$memCache = new memCacheObj();
	$key="module_".$moduleid;
	$module_data = $memCache->getKey($key);
	if(!$module_data){
		$module_data = array();
		$sql = "SELECT * from layout_modules where id=" . $moduleid;
		$res = exec_query($sql,1);
		$module_data = $res;
		// cache for 30 minutes
		//$memCache->setKey($key, $module_data, 1800);
	}
	return $module_data;
}



function getModules() {
$listmodules = exec_query("SELECT id, name FROM layout_modules");
foreach($listmodules as $row){
$modules[$row['id']] = $row['name'];
}

return $modules;
}

function getAuthorName($id) {
	$sql = "select name from contributors where id=". $id . " limit 1";
	$result = exec_query($sql,1);
	if($result)
		return $result['name'];
	return 0;
}

function getColumnModuleOrder($columnid) {
$moduleOrder = exec_query("SELECT moduleOrder from layout_columns where id=" . $columnid);
	foreach($moduleOrder as $row){
return $row['moduleOrder'];
}
}

function listModules($page,$column) {
	$listmodules = exec_query("SELECT moduleOrder from layout_columns WHERE layout_columns.id=$column and layout_columns.pageID=$page");
	foreach($listmodules as $row){
	$modOrder = $row['moduleOrder'];
	}

	return $modOrder;
}


function refresh_Column_Module_List($pageid,$columnName,$modulesArray,$extraData,$div) {

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}


	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);
	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}
		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}
				//display module type
				if ($dynamic==true) { ?>

					<div class="preview_module">
					<? if ($name != "") { ?>
										<div class="FeatureHeaderGrayBgDoubleHome">
											<?= $name; ?> </div>
						<? } ?>
					<?

					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}

					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						//reformat the date from the database
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						//if < (&lt;) or > (&gt;) then insert the link
						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . $pfx . "/layout/article.php?a=". $row['id'] . "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					<? if ($moduleCounter != 1) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=up&div=<?=$div;?>','<?=$div;?>')">up</a><? } ?>
					<? if ($moduleCounter < count($intoArray)) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=down&div=<?=$div;?>','<?=$div;?>')">down</a><? } ?>
					<a href="#" class="deleteModule" onclick="requestHTTP('module_remove_from_column.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&div=<?=$div;?>','<?=$div;?>')">remove</a>
					<a href="module_admin.php?moduleid=<?= $mod; ?>">edit</a>
					</div>
				<?
				} else {
				?>

				<div class="preview_module">

					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDoubleHome">
					<?= $name; ?> </div>
					<? } ?>
					<? if($div=="middlemoduleinner") {?>
					<div  class="middlemoduleinner">
					<? }?>
					<?= $htmlShow; ?><br>

					<? if ($moduleCounter != 1) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=up&div=<?=$div;?>','<?=$div;?>')">up</a><? } ?>
					<? if ($moduleCounter < count($intoArray)) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=down&div=<?=$div;?>','<?=$div;?>')">down</a><? } ?>
					<a href="#" class="deleteModule" onclick="requestHTTP('module_remove_from_column.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&div=<?=$div;?>','<?=$div;?>')">remove</a>
					<a href="module_admin.php?moduleid=<?= $mod; ?>">edit</a>
				</div>

				<?
				} //end display module

				//}
			}

		} else {
			echo "<font color=red>add modules below</font><br>";
		}

		echo "<br>";
		addModuleDropList($pageid,$columnid,$div);
		echo "<br>";
		echo "<a href='module_create.php'>Create New Module</a>";

}


//***********************************************************************************************
function refresh_Portlet_List($pageid,$columnName,$modulesArray,$extraData,$div) {

	$links['archive'] = $D_R . "/library/search.htm";
	$links['profiles'] = $D_R . "/gazette/bios.htm";
	$links['univ'] = $D_R . "/university/";

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}


	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);
	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);
					$title=$getModuleInfo['name'];
					unset($getModuleInfo['name']);
					//remove portlet from the uniquename of the module.
					$name = str_replace("portlet-","",$getModuleInfo['uniqueName']);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = $value;
								$$key = $value;
							}
						}

					}

				?>
				<?
	if ($name == "partners" || $name == "mvtv_sidebar" || $name == "creditcards") {
					?>

	                    <img src="<?=$IMG_SERVER;?>/images/portlet_<?= $name; ?>_top.gif" alt=""><br />
						<div id="portlet-<?= $name; ?>">
						<div id="portlet-<?= $name; ?>-main">
					<? } elseif ($name == "archive" || $name == "profiles" || $name == "univ")  { ?>
						<a href="<?= $links[$name]; ?>" style="padding:0px;margin:0px;text-decoration:none;cursor:hand">
	                    <!--<img style="padding:0px;margin:0px;" src="<?=$IMG_SERVER;?>/images/portlet_<?= $name; ?>_top.gif" alt="" />-->
	                    <div id="portlet-<?= $name; ?>">
	                    <div id="portlet-<?= $name; ?>-header">
	                    <div id="portlet-<?= $name; ?>-headertitle"><?=$title;?></div></div></a>
						<div id="portlet-<?= $name; ?>-main">

				<? } else { ?>

		  		<div id="portlet-general">
				<div id="portlet-general-main">
				<?}?>


				<?
				//display module type
				if ($dynamic==true) {

					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
					}




					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);


					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						//if < (&lt;) or > (&gt;) then insert the link
						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . $pfx . "/layout/article.php?a=". $row['id'] . "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					<? if ($moduleCounter != 1) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=up&div=<?=$div;?>','<?=$div;?>')">up</a><? } ?>
					<? if ($moduleCounter < count($intoArray)) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=down&div=<?=$div;?>','<?=$div;?>')">down</a><? } ?>
					<a href="#" class="deleteModule" onclick="requestHTTP('module_remove_from_column.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&div=<?=$div;?>','<?=$div;?>')">remove</a>
					<a href="module_admin.php?moduleid=<?= $mod; ?>">edit</a>

				<?
				} else {
				?>

					<?= $htmlShow; ?><br>
					<? if ($moduleCounter != 1) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=up&div=<?=$div;?>','<?=$div;?>')">up</a><? } ?>
					<? if ($moduleCounter < count($intoArray)) { ?><a href="#" class="reorder" onclick="requestHTTP('module_reorder.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&direction=down&div=<?=$div;?>','<?=$div;?>')">down</a><? } ?>
					<a href="#" class="deleteModule" onclick="requestHTTP('module_remove_from_column.php?pageid=<?=$pageid;?>&columnid=<?=$columnid;?>&moduleid=<?=$mod;?>&div=<?=$div;?>','<?=$div;?>')">remove</a>
					<a href="module_admin.php?moduleid=<?= $mod; ?>">edit</a>


				<?
				} //end display module
				?>
				</div>
				</div>
				<?
				//}
			}

		} else {
			echo "<font color=red>add modules below</font><br>";

		}

		echo "<br>";
		addModuleDropListPortlet($pageid,$columnid,$div);
		echo "<br>";
		echo "<a href='module_create.php'>Create New Module</a>";

}


function setNewModuleOrder($pageid, $columnid, $moduleOrder) {
	// $orderQuery = "UPDATE layout_columns SET moduleOrder='$moduleOrder' WHERE id=$columnid AND pageid=$pageid";
	//echo $orderQuery . "<br>";
	$modulecol['moduleOrder']=$moduleOrder;
	$moduleid[id]=$columnid;
	$moduleid[pageid]=$pageid;
	update_query("layout_columns",$modulecol,$moduleid);
	// mysql_query($orderQuery);
}


function articleInfo($articles) {
	$articleArray = explode(",",$articles);

	foreach($articleArray as $i => $value) {
		$theArticles[] = getArticle($articleArray[$i]);
	}
	//print_r($theArticles);
	return $theArticles;
}

/*function articleInfo($articles) {
	$articleArray = explode(",",$articles);

	foreach($articleArray as $i => $value) {

	//for($i=0;$i<count($articleArray);i++) {
		if ($i > 0) {
			$where .= " OR articles.id=" . $articleArray[$i];
		} else {
			$where = "articles.id=" . $articleArray[$i];
		}
	}

	$query = "SELECT articles.id, title, name author, date, character_text,body ";
	$query .= "from articles, contributors ";
	$query .= "where contrib_id = contributors.id and ";
	$query .= "($where)";
	echo $query;
	$results = mysql_query($query);
	$counter = 0;
	while($row = mysql_fetch_assoc($results)) {
		$theArticles[$counter]['id'] = $row['id'];
		$theArticles[$counter]['title'] = $row['title'];
		$theArticles[$counter]['author'] =  $row['author'];
		$theArticles[$counter]['date'] = date('F d, Y g:i a',strtotime($row['date']));
		$theArticles[$counter]['character_text'] = $row['blurb'];
		$theArticles[$counter]['fulltext'] = $row['body'];
		$theArticles[$counter]['imageURL'] = $row['imageURL'];
		$theArticles[$counter]['contributor'] = $row['contributor'];


		$counter++;
	}

	return $theArticles;
}*/

/*
	function will return the information about the articles .
	the returned content goes into the editor wysiwyg textbox.


*/

// used in the handpicked articles section of the module admin.
// this function will display the title, date, author and optional blurb or link the title to the articles page.


function addModuleDropList($page,$column,$div) {

 ?>
	<div class="bottomOfColumn">
		<form name="addModule<?= $column; ?>">

		<select name="moduleid" class="moduledropdown">
			<?= allModulesDropList(); ?>
		</select>
		<br>
		<input type="hidden" class="moduleDropList" name="columnid" value="<?= $div; ?>">
		<input type="button" class="addModuleButton" value="Add Module" onClick="requestHTTP('addModule.php?pageid=<?= $page; ?>&moduleid=' + document.addModule<?= $column; ?>.moduleid.options[document.addModule<?= $column; ?>.moduleid.selectedIndex].value + '&columnid=<?= $column; ?>&div=<?= $div; ?>','<?= $div; ?>')">

		</form>
	</div>
<?
}

function addModuleDropListPortlet($page,$column,$div) {

 ?>
	<div class="bottomOfColumn">
		<form name="addModule<?= $column; ?>">

		<select name="moduleid" class="moduledropdown">
			<?= allModulesDropList(); ?>
		</select>
		<br>
		<input type="hidden" class="moduleDropList" name="columnid" value="<?= $div; ?>">
		<input type="button" class="addModuleButton" value="Add Module" onClick="requestHTTP('addModulePortlet.php?pageid=<?= $page; ?>&moduleid=' + document.addModule<?= $column; ?>.moduleid.options[document.addModule<?= $column; ?>.moduleid.selectedIndex].value + '&columnid=<?= $column; ?>&div=<?= $div; ?>','<?= $div; ?>')">

		</form>
	</div>
<?
}

function slideshow_exists($slideshowid)
 {

   $results = exec_query("select * from slideshow where id=".$slideshowid." ");
   foreach($results as $row)
	   {
		$slideid[id]=$row[id];
	   }
   return $slideid;
   }

   function getFullslideshow($slideid) // slideshow with all slides together
    {
	   $sql = "select s.id id, s.title,s.total_slides, contributors.name author, s.contributor, s.contrib_id authorid, s.date, sc.body, sc.slideshow_id,sc.slide_title,sc.slide_no
	from slideshow s, contributors,slideshow_content sc
	where s.id=" . $slideid . "  and  sc.slideshow_id=s.id and  s.contrib_id = contributors.id order by slide_no";

	$results = exec_query($sql);
	$slide=$results;
	return($slide);
	 }



/****** get latest slide show as default for index page************/

function getlatestslideshow() { //slideshow with one slide at time

	$sql = "select s.id id, s.approved, s.title,s.total_slides, contributors.name author, s.contributor,
 s.contrib_id authorid, s.date, sc.body, sc.slideshow_id,sc.slide_title,sc.slide_no
	from slideshow s, contributors,slideshow_content sc
	where  s.date = (select max(ss.date) from slideshow ss where ss.approved='1') and slide_no = 1 and
 sc.slideshow_id=s.id and  s.contrib_id = contributors.id and s.approved='1' "; //


	$row = exec_query($sql,1);
	if(!empty($row))
	{
	//	while ($row = mysql_fetch_assoc($results)) {
			$slide['id'] = $row['id'];
			$slide['title'] = $row['title'];
			$slide['author'] = $row['author'];
			$slide['authorid'] = $row['authorid'];
			$slide['date'] = date('M d, Y g:i a',strtotime($row['date']));

			$slide['contributor'] = $row['contributor'];
			//slide content information
			$slide['body'] = $row['body'];
            $slide['slide_title'] = $row['slide_title'];
			$slide['slide_no'] = $row['slide_no'];
			$slide['total_slides'] = $row['total_slides'];
			$slide['slideshow_id'] = $row['slideshow_id'];
			//restate the author id
			$tempAuthor = returnRealAuthor($slide['authorid'],$slide['contributor']);
			$slide['authorid'] = $tempAuthor['id'];
			$slide['author'] = $tempAuthor['name'];
			$slide['approved'] = $tempAuthor['approved'];
		//}

		return $slide;
	} else {
		//return null
		return 0;
	}
}

function getTotalSlideCount($slideid,$slideshow_id_type,$slidetags=NULL){
	$total=0;
	$sql_total="select distinct(eit.item_id),s.title from ex_item_tags eit,slideshow s where (item_id!='$slideid' and item_type='$slideshow_id_type' and tag_id in($slidetags)) and s.approved='1' and s.id=eit.item_id";
	//$sql_total="select distinct(eit.item_id) from ex_item_tags eit,slideshow s where (item_id!='$slideid' and item_type='$slideshow_id_type' and tag_id in($slidetags)) and s.approved='1' and s.id=eit.item_id";
	//echo $sql_total;
	$results_count = exec_query($sql_total);
	if(count($results_count)){
		$total = count($results_count);
	}
	return $total;
}

function getSlideContent($totalslides,$slideTags,$slideid){
	global $IMG_SERVER;
	if($totalslides!=0){
			$slidecontent='<div id="mainSlideDiv">';
			for($i=0;$i<count($slideTags)-1;$i++){
				$title=$slideTags[$i]['title'];
				$id=$slideTags[$i]['item_id'];

				if($id == $slideid)
					$slidecontent.='<div class="mv_tumb_selected">';
				else
					$slidecontent.='<div class="mv_tumb">';

				$slidecontent.='<div style="border:solid 0px red; float:left;">
					<a href="'.$HTPFX.$HTHOST.'index.htm?a='.$id.'&preview=1" title="'.ucwords(strtolower($title)).'">
					<img src="';
				$image=$slideTags[$i]['slide_image'];
				if($image==''){
					$image="$IMG_SERVER/images/slideshow/default_images.gif";
				}
				$slidecontent.=$image;
				$slidecontent.='" width="70" height="62" align="bottom" border="0" />
					<div class="mv_tumbdiv">';
				$slidecontent.=ucwords(strtolower($title));
				$slidecontent.='</div></a></div>
						</div>';
			}
			$slidecontent.='</div>';
		}
		return $slidecontent;
	}



function getSlideContent_Latest($totalslides,$slideTags,$slideid){
	global $IMG_SERVER;
	if($totalslides!=0){
			$slidecontent='<div id="mv_tumb">';
			for($i=0;$i<count($slideTags)-1;$i++)
			{
				$title='';
				$title=$slideTags[$i]['title'];
				$id=$slideTags[$i]['item_id'];
				if($id == $slideid)
				$slidecontent.='<div class="mv_tumb_selected">';
				else
				$slidecontent.='<div class="mv_tumb">';

				$slidecontent.= '<a href="'.$HTPFX.$HTHOST.'index.htm?a='.$id.'&preview=1" title="'.ucwords(strtolower($title)).'">
					<img src="';
				$image=$slideTags[$i]['slide_image'];
				if($image==''){
					$image="$IMG_SERVER/images/slideshow/default_images.gif";
				}
				$slidecontent.=$image;
				$slidecontent.='" width="70" height="62" align="bottom" border="0" />
					<div class="mv_tumbdiv">';
				$slidecontent.=ucwords(strtolower($title));
				$slidecontent.='</div></a></div>';
			}
			$slidecontent.='</div>';
		}
		return $slidecontent;
	}

function getRelatedSlides($slideid,$offset,$limit=null,$slideshow_id_type){
				$slidesarray=array();
				$totalcount = 0;
				$slidesarray=getSlideShowRandm($slideid,$offset,$limit,$slideshow_id_type);
				$totalcount=getSlideCountRandm($slideid,$slideshow_id_type);
				$slidesarray['totalslides']=$totalcount;
				return $slidesarray;
}

function getSlideCountRandm($slideid,$slideshow_id_type){
	$results_cnt=array();
	//*** $sql_cnt="select distinct(s.id) from slideshow s,slideshow_content sc where s.id!='$slideid' and s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1'";
	$sql_cnt="select distinct(s.id) from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1'";
	$results_cnt = exec_query($sql_cnt);
	return count($results_cnt);
}
function getSlideShowRandm($slideid,$offset,$limit,$slideshow_id_type){
	$randomarray=array();
	//*** $sql="select distinct(s.id)as item_id,s.title,s.slide_thumbnail as slide_image from slideshow s,slideshow_content sc where s.id!='$slideid'  and s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY s.id limit $offset,$limit";
	$sql="select distinct(s.id)as item_id,s.title,s.slide_thumbnail as slide_image from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY s.id desc limit $offset,$limit";
	$results = exec_query($sql);
	$totalcount=count($results);
	if($totalcount>0){
	$randomarray=$results;
	}else{
		// No random results found
	}
	return $randomarray;
}

function getSlideShow($slideid,$slide_no,$preview) { //slideshow with one slide at time
	$sql = "select s.id id, s.title,s.total_slides, contributors.name author, s.contributor, contributors.disclaimer, s.contrib_id authorid, s.date, sc.body, sc.slideshow_id,sc.slide_title,sc.slide_no,sc.slide_image
	from slideshow s, contributors,slideshow_content sc
	where s.id='" . $slideid . "' and sc.slide_no='". $slide_no ."' and  sc.slideshow_id=s.id and  s.contrib_id = contributors.id ";

 if ($preview !='1' )
 {
	 $sql = $sql." and s.approved='1'";
 }
 $sql =  $sql." order by slide_no";

	$results = exec_query($sql);
	if (count($results) )
	{
		global $maximagewidth;
		global $maximageheight;

        foreach($results as $row){
			$slide['id'] = $row['id'];
			$slide['title'] = $row['title'];
			$slide['author'] = $row['author'];
			$slide['authorid'] = $row['authorid'];
			$slide['date'] = date('M d, Y g:i a',strtotime($row['date']));

			$slide['contributor'] = $row['contributor'];
			//slide content information
			$slide['body'] = $row['body'];
            $slide['slide_title'] = $row['slide_title'];
			$slide['slide_no'] = $row['slide_no'];
			$slide['total_slides'] = $row['total_slides'];
			$slide['slideshow_id'] = $row['slideshow_id'];
			$imageurl=$row['slide_image'];
			$slide['image'] = $imageurl;

			$imgproperties=getImageSize($imageurl);
			$imgwidth=$imgproperties[0];
			$imgheight=$imgproperties[1];
			if(($imgwidth>$maximagewidth)||($imgheight>$maximageheight)){
				if($imgwidth>$maximagewidth){
					$imgwidth=$maximagewidth;
				}
				if($imgheight>$maximageheight){
					$imgheight=$maximageheight;
				}
			}

			$slide['imgwidth'] = $imgwidth;
			$slide['imgheight'] = $imgheight;

			//restate the author id
			$tempAuthor = returnRealAuthor($slide['authorid'],$slide['contributor']);
			$slide['authorid'] = $tempAuthor['id'];
			$slide['author'] = $tempAuthor['name'];
		}

		return $slide;
	} else {
		//return null
		return 0;
	}
}

function get_itemtype_id($type){
	$typeid = exec_query("SELECT id FROM ex_item_type WHERE item_text='$type'",1);
	$typeid=implode(' ',$typeid);
	return $typeid;
}

/* used in gazette/news_views/index.php*/
function makeArticleslink_sql($id)
 {
	 $qry="SELECT keyword ,blurb FROM articles where id =" . $id . " ";
	 $results = exec_query($qry,1);
	  $res['keyword']=$results['keyword'];
	  $res['blurb']=$results['blurb'];
	  return $res;

  }

function getArticle($articleid) {
	global $D_R;
	include_once("$D_R/lib/_news.php");
	$sql = "select articles.id id, articles.title,articles.subsection_ids, contributors.name author,
articles.contributor, contributors.disclaimer,articles.position, contrib_id authorid, date, layout_type,
blurb, body, position,articles.approved, character_text, keyword ,character_images.asset imageURL,
branded_images.assets as brandedlogo,branded_images.url as logourl,branded_images.name as logoname,articles.branded_img_id,contributors.id as authorid,articles.featureimage
from contributors, character_images,articles LEFT JOIN branded_images
ON(branded_images.id=articles.branded_img_id)
where articles.id=".$articleid."
and articles.contrib_id = contributors.id
and (character_images.id=articles.character_img_id or articles.character_img_id=0) limit 1";

	$results = exec_query($sql);
 $article =array();
	if (count($results) == 1) {
       foreach($results as $row){
			$article['id'] = $row['id'];
			$article['title'] = $row['title'];
			$article['layout_type'] = $row['layout_type'];
			$article['author'] = $row['author'];
			$article['authorid'] = $row['authorid'];
			$article['date'] = date('M d, Y g:i a',strtotime($row['date']));	//$row['date'];
			$article['blurb'] = $row['blurb'];
			$article['brandedlogo'] = $row['brandedlogo'];
			$article['logourl'] = $row['logourl'];
			//$article['body'] = $row['body'];
                         $article['logoname'] = $row['logoname'];
                        $article['branded_img_id'] = $row['branded_img_id'];
			$recentArticlerowbody=get_full_article_body($articleid);  // fetch body from article_revision table.
			$countbody=count($recentArticlerowbody);
			for($i=0;$i<=$countbody-1;$i++){
			 $articlebody.=' '.$recentArticlerowbody[$i][body];

			}
			$article['body'] = $articlebody;
			$article['position'] = $row['position'];
			$article['imageURL'] = $row['imageURL'];
			$article['character_text'] = $row['character_text'];
			$article['category'] = $row['category'];
			$article['contributor'] = $row['contributor'];
			$article['disclaimer'] = $row['disclaimer'];
			$article['positions'] = $row['positions'];
			$article['keyword'] = $row['keyword'];
			$article['approved'] = $row['approved'];
			$article['featureimage'] = $row['featureimage'];

			//restate the author id
			$tempAuthor = returnRealAuthor($article['authorid'],$article['contributor']);
			$article['authorid'] = $tempAuthor['id'];
			$article['author'] = $tempAuthor['name'];
			//rewrite the disclaimer
			$article['disclaimer'] = getAuthorDisclaimer($article['authorid']);
		}


		//print_r($article);
		return $article;
	} else {
		return null;
		}
}

function getArticleRelatedLinks($articleid) {
        $sql = "select title, keyword,item_type, article_type, article_id, position from article_related_links where parent_article_id = '".$articleid."'  order by position";
        $results = exec_query($sql);
        return $results;

}


function getAuthorDisclaimer($id) {

	if ($id == "") {
		$id = 0;
	}

	$sql = "SELECT disclaimer from contributors where id=$id";
	$results = exec_query($sql);
    foreach($results as $row){
		return $row['disclaimer'];
	}

	if (count($results) == 0) {
		return "";
	}


}



//-----------------------------------------------
// publicly called functions
//-----------------------------------------------

function call_Column_Module_List($pageid,$columnName,$modulesArray,$extraData,$div) {
	global $SEC_TO_ZONE_OPENX,$ADS_SIZE,$HTPFX,$HTHOST;
	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}

	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}

				//special catch for articles' also by this author module where the author listed is not a real contributor.
				// i.e if hoofy writes an article, do not display 'also by this author' instead display recent headlines
				if (($dynamic==true) && ($extraData['category'] == "") && ($extraData['authorid'] == 0 || $extraData['authorid']=="")) {
					$sql = "select articles.id id, title, name author, date,articles.keyword keyword,blurb, contributor from articles, contributors where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and articles.is_public='1' order by date desc limit 4";
					$name = "Recent Headlines";
				}

				//display module type
				if ($dynamic==true) { ?>

					<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDoubleHome">
						<?= $name; ?></div>
						<? } ?>
						<? if($div=="middlemoduleinner") {?>
							<div  class="<?=$div?>">
						<? } else {?>
						<div  class="moduleinner">
						<? } ?>


				<?
					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}



					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . makeArticleslink($row['id'],$row['keyword'],$row['blurb']). "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>
				</div><br>
					</div>
				<?
				} else {

				?>

				<div class="modulebox">
					<? if ($name != "") {
						if ($SEC_TO_ZONE_OPENX["Module".$mod."88x31"]){
					?>

					<div class="FeatureHeaderGrayBgDoubleHomeWithAd">
                    <table cellpadding="0" cellspacing="0">
                    	<tr valign="top">
                        	<td width="99%" class="ModuleHeaderText"><?= $name; ?></td>
                            <td width="1%" valign="middle" align="center" style="padding-top:2px; vertical-align:middle" nowrap="nowrap"><? show_ads_openx("Module".$mod."88x31",$SEC_TO_ZONE_OPENX["Module".$mod."88x31"],$ADS_SIZE['MicroBar']);?></td>                        </tr>
                    </table>
				</div>
<? }else{ ?>
	<div class="FeatureHeaderGrayBgDoubleHome">
						<?= $name; ?> </div>
						<? }
							}?>
						<? if($div=="middlemoduleinner") {?>
							<div  class="<?=$div?>">
						<? } else {?>
						<div  class="moduleinner">
						<? } ?>
						<?= $htmlShow; ?></div>

					</div>


				<?
				} //end display module

				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";
		}


}

function call_Column_Module_List_Image($pageid,$columnName,$modulesArray,$extraData,$div) {

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}

	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
    foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}

				//special catch for articles' also by this author module where the author listed is not a real contributor.
				// i.e if hoofy writes an article, do not display 'also by this author' instead display recent headlines
				if (($dynamic==true) && ($extraData['category'] == "") && ($extraData['authorid'] == 0 || $extraData['authorid']=="")) {
					$sql = "select articles.id id, title, name author, date,articles.keyword keyword,blurb, contributor from articles, contributors where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and articles.is_public='1' order by date desc limit 4";
					$name = "Recent Headlines";
				}

				//display module type
				if ($dynamic==true) { ?>

					<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

				<?
					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}



					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . makeArticleslink($row['id'],$row['keyword'],$row['blurb']). "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					</div>
				<?
				} else {
				?>

				<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

						<div  class="moduleinnerimage"><?= $htmlShow; ?></div><br>

					</div>


				<?
				} //end display module

				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";
		}


}



function call_Column_Module_List_mailbag($pageid,$columnName,$modulesArray,$extraData,$div) {

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}

	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName =exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}

				//special catch for articles' also by this author module where the author listed is not a real contributor.
				// i.e if hoofy writes an article, do not display 'also by this author' instead display recent headlines
				if (($dynamic==true) && ($extraData['category'] == "") && ($extraData['authorid'] == 0 || $extraData['authorid']=="")) {
					$sql = "select articles.id id, title, name author, date,articles.keyword keyword,blurb, contributor from articles, contributors where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and articles.is_public='1' order by date desc limit 4";
					$name = "Recent Headlines";
				}

				//display module type
				if ($dynamic==true) { ?>

					<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

				<?
					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}



					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . makeArticleslink($row['id'],$row['keyword'],$row['blurb']). "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					</div>
				<?
				} else {
				?>

				<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>
						<div class="mailbox">
						<?= $htmlShow; ?></div><br>

					</div>


				<?
				} //end display module

				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";
		}


}

function call_Column_Module_List_featured($pageid,$columnName,$modulesArray,$extraData,$div) {

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}

	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}

				//special catch for articles' also by this author module where the author listed is not a real contributor.
				// i.e if hoofy writes an article, do not display 'also by this author' instead display recent headlines
				if (($dynamic==true) && ($extraData['category'] == "") && ($extraData['authorid'] == 0 || $extraData['authorid']=="")) {
					$sql = "select articles.id id, title, name author, date,articles.keyword keyword,blurb, contributor from articles, contributors where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and articles.is_public='1' order by date desc limit 4";
					$name = "Recent Headlines";
				}

				//display module type
				if ($dynamic==true) { ?>

					<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

				<?
					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}



					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . makeArticleslink($row['id'],$row['keyword'],$row['blurb']). "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					</div>
				<?
				} else {
				?>

				<div class="modulebox">


						<?= $htmlShow; ?><br>

					</div>


				<?
				} //end display module

				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";
		}


}



//***********************************************************************************************
function call_Portlet_List($pageid,$columnName,$modulesArray,$extraData,$div) {

	$links['archive'] = $D_R . "/library/search.htm";
	$links['profiles'] = $D_R . "/gazette/bios.htm";
	$links['univ'] = $D_R . "/university/";
	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}


	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);
					$title=$getModuleInfo['name'];
					unset($getModuleInfo['name']);
					//remove portlet from the uniquename of the module.
					$name = str_replace("portlet-","",$getModuleInfo['uniqueName']);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = $value;
								$$key = $value;
							}
						}

					}

				if ($name == "partners" || $name == "mvtv_sidebar" || $name == "creditcards") {
				?>

                    <img src="<?=$IMG_SERVER;?>/images/portlet_<?= $name; ?>_top.gif" alt=""><br />
					<div id="portlet-<?= $name; ?>">
					<div id="portlet-<?= $name; ?>-main">
				<? } elseif ($name == "archive" || $name == "profiles" || $name == "univ")  { ?>
					<a href="<?= $links[$name]; ?>" class="portletheadertext">
                    <div id="portlet-<?= $name; ?>">
                    <div id="portlet-<?= $name; ?>-header">
                    <div id="portlet-<?= $name; ?>-headertitle"><?=$title;?></div></div></a>
					<div id="portlet-<?= $name; ?>-main">
				<? } else { ?>
		  			<div id="portlet-general">
					<div id="portlet-general-main">

				<?}?>
				<?
				//display module type
				if ($dynamic==true) {

					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
					}

					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);


					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						//if < (&lt;) or > (&gt;) then insert the link
						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . $pfx . "/layout/article.php?a=". $row['id'] . "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}

				} else {
				?>
					<?= $htmlShow; ?><br>

				<?
				} //end display module
				?>
				</div>
				</div></div>
				<?
				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";

		}



}


//display the date

function displayDate() {
	$dateInfo = getDate();
	$todaysDate = $dateInfo['weekday'] . " " . $dateInfo['month'] . " " . $dateInfo['mday'] . ", " . $dateInfo['year'];
	return $todaysDate;
}

//returns the contents of the portlet module for a given name
function getPortlet($name) {
	$sql = "select * from layout_modules where uniqueName='portlet-" . $name . "' limit 1";
	$results = exec_query($sql);

	// get data out of query's results, store in array.
foreach($results as $row) {
		$portlet['id'] = $row['id'];
		$portlet['uniqueName'] = $row['uniqueName'];
		$portlet['type'] = $row['type'];
		$portlet['html'] = $row['html'];
		$portlet['sql'] = $row['sql'];
	}

	//send back the data in an array.
	return $portlet;
}


//admin side of the portlets
//returns the contents of the portlet module for a given name
function editPortlet($name) {
	$sql = "select * from layout_modules where uniqueName='portlet-" . $name . "' limit 1";
	$results = exec_query($sql);

	// get data out of query's results, store in array.
	foreach($results as $row){
		$portlet['id'] = $row['id'];
		$portlet['uniqueName'] = $row['uniqueName'];
		$portlet['type'] = $row['type'];
		$portlet['html'] = $row['html'];
		$portlet['sql'] = $row['sql'];
	}

	//send back the data in an array.
	return $portlet;
}

function textBubble($text) {
	return wordwrap($text, 23,"<br>",1);
}

//display recent headline in a 2 column table
function displayRecentHeadlines($titlebar,$sql,$zoneID) {

  	$results = exec_query($sql);
	$counter = 1;

	?>

<table align="left" border="0" width="98%" cellpadding="2" cellspacing="3">
	<tr>
	<td  width="50%" valign="top">
		<div class="template3_article_list">
			<ul>
		<?
            foreach($results as $row){
				$counter++;
				//$realauthorInfo = returnRealAuthor($row['author'],$row['contributor']);

				$realauthorInfo = returnRealAuthor($row['author'],$row['contrib_id']);
				$realauthorname = $realauthorInfo['name'];

				//display the article title with link to article.php also show date and author

				// the function make links of article according to the keywords and headlines
				//$link=makeArticleslink($row);?>
				<li>
				<a class="articleLink" href= <?= $pfk.makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?>><div class="cooper_market_heading"><?= $row['title'] ?></div></a><?

				echo "<h3>".$realauthorname."</h3>" . chr(13);
				echo  "<div class='cooper_recent_common_heading'>". $row[talkbubble] ."</div>". chr(13);
			  ?>
			  </li>
			 <!-- <a href= <?= $pfk.makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?> class="ReadMore">Read more...</a>-->
			 <!-- <br /> -->
				  <?
				if ($counter > (count($results)/2)) {
					echo "</td> </ul></div>	</div>" . chr(13) . "<td></td>
			<td width=\"50%\" valign=\"top\"><div style='border-left:solid 1px #6396C7;float:left;'><div class='template3_article_list'><ul> ";
					$counter=0;
				}
			}
		?>
		</td>
		</ul>
		</div>	</div>
	</tr>
	</table>
	<?
}
function displayRecentHeadlinesLayout($titlebar,$sql,$zoneID) {
	$results = exec_query($sql);
	$counter = 1;
	?>
</h2>
	<table border="0" width="405" cellpadding="2" cellspacing="3">
	<tr>
		<td width="48%">
		<?
				foreach($results as $row){
				$counter++;
				$realauthorInfo = returnRealAuthor($row['author'],$row['contributor']);

				$realauthorname = $realauthorInfo['name'];
				//display the article title with link to article.php
				// also show date and author

            ?>
				<a class="articleLink" href="article.php"><div class="NewsArticleTitle"><?= $row['title'] ?></div></a><?

				echo "<div class=\"AuthorName\">" . $realauthorname ."</div>" . chr(13);
				echo  "<div class=\"ArticleDescription\">". $row[talkbubble] ."</div>". chr(13);

			  ?>
			<!--  <a href= <?= $pfk.makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?> class="ReadMore">Read more...</a>-->
			<br />
 <?
				if ($counter > (count($results)/2)) {
					echo "</td>" . chr(13) . "<td align=\"center\" valign=\"middle\" width=\"1\" background='". $pfx. "/images/recent_div_line.gif' style=\"background-repeat:repeat-y\"></td><td width=\"48%\">";
					$counter=0;
				}
			}

		?>
		</td>
	</tr>
	</table>

	<?

}




function displayRecentCategoryArticle($category) {

	$sql = "SELECT articles.* , character_images.asset imageURL, contributors.name author,articles.position, contributors.disclaimer, article_categories.title category ";
	$sql .= "FROM article_categories, articles, contributors, character_images ";
	$sql .= "WHERE articles.contrib_id = contributors.id ";
	$sql .= "and articles.approved = '1' and articles.is_live='1' ";
	//$sql .= "and articles.is_public = '1' ";
	$sql .= "and character_images.id = articles.character_img_id ";
	$sql .= "and article_categories.name = '" . $category . "' ";
	$sql .= "and find_in_set( article_categories.id, articles.category_ids ) ";
	$sql .= "ORDER BY date ";
	$sql .= "desc ";
	$sql .= "LIMIT 1 ";
	$results = exec_query($sql);
    foreach($results as $row){
		$article = $row;
		//restate the author id
		$tempAuthor = returnRealAuthor($article['authorid'],$article['contributor']);

		$article['authorid'] = $tempAuthor['id'];
		$article['author'] = $tempAuthor['name'];
		//rewrite the disclaimer
		$article['disclaimer'] = getAuthorDisclaimer($article['authorid']);
		//print_r($article[0]);

	}

	echo displayArticleInfo2($article);
}
//-----------------------------------------------

function displayRecentCategoryArticle2($category,$pageName,$modules) {

	$sql = "SELECT articles.* , character_images.asset imageURL, contributors.name author, contributors.disclaimer, articles.position, article_categories.title category ";
//	$sql = "select articles.id id, articles.title, contributors.name author, articles.contributor, contributors.disclaimer,articles.position, contrib_id authorid, date, blurb, body, position, character_text, article_categories.title category, character_images.asset imageURL ";
	$sql .= "FROM article_categories, articles, contributors, character_images ";
	$sql .= "WHERE articles.contrib_id = contributors.id ";
	$sql .= "and articles.approved = '1' articles.is_live='1' ";
	//$sql .= "and articles.is_public = '1' ";
	$sql .= "and character_images.id = articles.character_img_id ";
	$sql .= "and article_categories.name = '" . $category . "' ";
	$sql .= "and find_in_set( article_categories.id, articles.category_ids ) ";
	$sql .= "ORDER BY date ";
	$sql .= "desc ";
	$sql .= "LIMIT 1 ";

	$results = exec_query($sql);
    foreach($results as $row){
		$article = $row;
		//restate the author id
		$tempAuthor = returnRealAuthor($article['authorid'],$article['contributor']);
		$article['authorid'] = $tempAuthor['id'];
		$article['author'] = $tempAuthor['name'];
		//rewrite the disclaimer
		$article['disclaimer'] = getAuthorDisclaimer($article['authorid']);
	}

	echo displayArticleInfo($article,$pageName,$modules,$category);
}


//must pass function , article and correct page name
function displayArticleInfo($articles,$pageName, $modules,$category) {
	//print_r($options);
	if ($articles == 0) {
		$article['title'] = "Sorry no article could be found";
	} else {
		$article = $articles;
	}

?>
		<table id="news" cellpadding="0" cellspacing="0">
			  <tr>

			  <!-- left column-->
			  <td class="main-content" style="padding:0px; vertical-align:top;">
				<div id="left-content">
				<h1 class="bar"><?= $article['title']; ?></h1>
					<!-- begin subheader baloon -->
					<p class="header-separator">&nbsp;</p>
					<div id="articleOptions" style="size:xx-small;margin:0px;padding:3px 0 0 0;">
						 <p style="text-align:right"><a href="javascript:print(<?= $article['id']; ?>);" target="_self">print this page</a></p>
					</div>
					<div id="related-module">
						<?
						//get recent headlines for the point figure  or retail roundup
						$dynamicModuleInfo['category'] = $category;
						call_Column_Module_List($pageName,'column2',$modules,$dynamicModuleInfo,'right-content');

						?>
						</div>
						<!--spacer for fixed column width; do not delete -->
						<img src="http://storage.googleapis.com/mvassets/images/spacer.gif" width="186" height="1" alt="" />
					</div>
					<br>
				<!-- display the author, date, character_text, and character -->
					<table border="0" width="380">
						<tr>
							<td nowrap>
								<br>
								<?= displayAuthorLink($article['author'],$article['authorid']); ?>
								<!-- <h5><?= $article['author']; ?></h5> -->
								<h6> <?= date('M d, Y g:i a',strtotime($article['date'])); ?></h6>
							</td>

							<td width=1% nowrap valign=middle>
								<? if ($article['character_text'] != "") {
									showTalkBubble($article['character_text']);
								 } ?>
							</td>

							<td valign="bottom"><br><br>
								<img valign="bottom" src="<?= $pfx . $article['imageURL']; ?>" width="70" height="77">
							</td>
							</tr>
						</table>
					<div class="articleBody">
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



<?
}


function displayArticleInfo2($articles) {
	//print_r($options);
	if ($articles == 0) {
		$article['title'] = "Sorry no article could be found";
	} else {
		$article = $articles;
	}

?>
<table width="98%" ><tr><td>
		<table cellpadding="0" cellspacing="0">
			  <tr>

			  <!-- left column-->
			  <td style="padding:0px; vertical-align:top;">

				<h1 class="articletitle"><?= $article['title']; ?></h1>
					<!-- begin subheader baloon -->

					<div id="articleOptions" style="size:xx-small;margin:0px;padding:3px 0 0 0;">
						 <p style="text-align:right"><a href="javascript:print(<?= $article['id']; ?>);" target="_self">print this page</a></p>
					</div>
			<br></td></tr>
                    <tr><td>
				<!-- display the author, date, character_text, and character -->
					<table border="0">
						<tr>
						<td valign="bottom" width="1%"><br><br>
						  <img valign="bottom" src="<?= $IMG_SERVER . $article['imageURL']; ?>" width="70" height="77" /></td>
                            <td  nowrap valign=middle align="left">
							<? if ($article['character_text'] != "") {
									showTalkBubble($article['character_text']);
								 } ?>							</td></tr><tr>
                            <td  nowrap="nowrap" colspan="2">
								<br>
								<?= displayAuthorLink($article['author'],$article['authorid']); ?>
								<h6> <?= date('M d, Y g:i a',strtotime($article['date'])); ?></h6>							</td>
							</tr>
						</table>
					<div class="articleBody" style="border:none">
					<br>
					<?= $article['body']; ?>
					</div>
					<br><br>
					</td>
				</tr>
			</table>


				<!-- positions -->
				<div class="positions">
					<font color="red"><?= $article['position']; ?></font>
				</div>
				<br>
				<!-- disclaimer -->
				<div class="disclaimer" style="width:98%">
					<?= $article['disclaimer']; ?>
				</div>


</td></tr></table>
<?
}



function printArticleInfo($articles,$options) {


	if ($articles == 0) {
		return;
	}

		if (($options['fulltext'] == "on") || ($options == "fulltext")) {

			$info.="<h4>" .  $articles[0]['title']. "</h4>";
			$info .= displayAuthorLink($articles[0]['author'],$articles[0]['authorid']);
			//$info .= "<h5>" . $articles[0]['author'] . "</h5>" .chr(13);
			$info .= "<h6>" . $articles[0]['character_text'] . "</h6><br>" . chr(13);
			$info.=$articles[0]['body'] . "<br>";
			$curTitlelink = makeArticleslink($articles[0]['id'],$articles[0]['keyword'],$articles[0]['blurb']);
			$info.="<a href=\"".$curTitlelink  ."\"><h4 class=\"ReadMore\">Read more...</h4></a>";
		//	$info.="<a href=\"". $pfx . "/articles/index.php?a=" . $articles[0]['id'] ."\"><h4 class=\"ReadMore\">Read more...</h4></a>";

			//print_r($articles);
			return $info;

		} else {
			for($i=0;$i<count($articles);$i++) {
				if ($options['linkTitle'] == "on") {
					$curTitlelink = makeArticleslink($articles[$i]['id'],$articles[$i]['keyword'],$articles[$i]['blurb']);
				$curTitle = "<a class=\"articleLink\" href=\"".$curTitlelink."\"><h4>" . $articles[$i]['title'] . "</h4></a>";
				//	$curTitle = "<a class=\"articleLink\" href=\"" . $pfx . "/articles/".$articles[$i]['title']."/index/a/" . $articles[$i]['id'] . "\"><h4>" . $articles[$i]['title'] . "</h4></a>";
				} else {
					$curTitle = $articles[$i]['title'];
				}
				$info .= "<H4>" . $curTitle . "</H4>" . chr(13) . "<H5>" . $articles[$i]['author'] . "</H5>"  . chr(13) . "<H6>".$articles[$i]['character_text'] . "</H6>";

				//insert blurb if requested
				if ($options['blurb'] == "on") {
						$info .= "<P>" . $articles[$i]['blurb'] . "</P>";
				}

				$info.="<a href=\"".$curTitlelink."\"><h4 class=\"ReadMore\">Read more...</h4></a>";
				//$info.="<a href=\"". $pfx . "/articles/index.php?a=" . $articles[$i]['id'] ."\"><h4 class=\"ReadMore\">Read more...</h4></a>";
			}




		}

	return $info;
}
// this function will correctly display the author or contributor of an article
// if no contributor is given, or = " " then display a link to the author link. If contributor is
//this function will return the real author of between when passed author and contributor.
function returnRealAuthor($a,$contrib) {

	if($contrib=="") {
		$author = getAuthorInfo($a);
		return $author;
	} else {
		$contributor = getAuthorInfo($contrib);
		if ($contributor != 0){
			return $contributor;  //listed contributor is a valid contributor
		} else {
			$FakeContributor['name'] = $contrib;
			$FakeContributor['id'] = "0";
			return $FakeContributor;  //listed contributor is not a real contributor.. ie Hoofy
		}
	}
}
// this function will correctly display the author or contributor of an article
// if no contributor is given, or = " " then display a link to the author link. If contributor is
function displayAuthorLink($name,$id,$css=0) {
	global $HTHOST;
	//echo "funtion displayAuthorLink :  name = " . $name . "<br> id = ". $id;
	if (($id == "0") || ($id == "")) {
		$link = "<div>" . $name . "</div>";
	} else {
		if($css)
			$link ="<span class='post_time' >By </span><a href=\"http://" . $HTHOST . "/gazette/bios.htm?bio=" . $id . "\" >". $name . "</a>";
		else
			$link ="<span class='post_time' >By </span> <a href=\"http://" . $HTHOST . "/gazette/bios.htm?bio=" . $id . "\" class=\"\">". $name . "</a>";
	}

	return $link;
}



function getAuthorInfo($author) {
	//determine if the given string is a number (ID)
	if ($author == "") {
		return 0;
	}
	if (is_numeric($author)) {
		$where = "contributors.id=" . $author;
	} else {
		// or it might be a name
		$where = "contributors.name='" .$author ."'";
	}

	$sql = "select id, name from contributors where " . $where;
	$results = exec_query($sql);
	$author = 0;
	foreach($results as $row){
		$author = $row;
	}
	//print_r($author);
	return $author;

}


function getRecentBuzz($count) {
	$sql = "SELECT id, date, author, body ";
	$sql .= "FROM buzzbanter ";
	$sql .= "WHERE show_on_web='1'  and approved='1'";
	$sql .= "ORDER BY date desc limit " . $count;

	$results = exec_query($sql);
	foreach($results as $row){
		$row['date'] = date('g:i a',strtotime($row['date']));
		$buzz[] = $row;
	}

	return $buzz;
}

function stripOutImages($content) {

	$position = stripos($content,"<img");

	if (!position) {
		echo "no images.";
	} else {
		echo "image tag found at $position";
	}

}

function displayAdvertisingMod() {
	?>
				<!-- begin advertisment area-->
				<div>
					<br>
					<center>
					<?php
    if (@include(getenv('DOCUMENT_ROOT').'/admin/phpads/phpadsnew.inc.php')) {
        if (!isset($phpAds_context)) $phpAds_context = array();
        $phpAds_raw = view_raw ('zone:79', 0, '', '', '0', $phpAds_context);
        echo $phpAds_raw['html'];
    }
?>


					<div style="width:300px;">
				     For corporate subscriptions, advertisements or other info on Minyanville, please contact Kevin Wassong at <a href="mailto:kevin@minyanville.com">kevin@minyanville.com</a> or 212.991.6200.
				     </div>
				     <br>

				 </div>
				 <!-- end advertisment area-->
	<?
}

function showTalkBubble($character_Text) {

	$IMG_SERVER = $GLOBALS['IMG_SERVER'];
	?>

			<table border="0" cellpadding="0" cellspacing="0">
            <tr>
			              <td><img src="<?= $IMG_SERVER; ?>/images/top_left.gif" /></td>

			              <td background="<?= $IMG_SERVER; ?>/images/article_balloon_top.gif"> </td>
			              <td> <img src="<?= $IMG_SERVER; ?>/images/top_right.gif" /></td>
			             </tr>


            <tr>
			              <td  align="left" background="<?= $IMG_SERVER; ?>/images/article_left_bg.gif"><img src="<?= $IMG_SERVER; ?>/images/article_balloon_left.gif"  /></td>
			              <td  class="balloonText"> <?=$character_Text;?></td>
			              <td  align="left" background="<?= $IMG_SERVER; ?>/images/article_balloon_right.gif"></td>
            </tr>
            <tr>
			             <td><img src="<?= $IMG_SERVER; ?>/images/bottom_left.gif" /></td>

						 <td background="<?= $IMG_SERVER; ?>/images/article_balloon_bottom.gif"> </td>
			              <td> <img src="<?= $IMG_SERVER; ?>/images/bottom_right.gif" /></td>
            </tr>
          </table>

	<?
}

function displayShareBox($latest = true, $header_class='bar_new', $days = 1) {
	$sql = "select article_recent.id, article_recent.title,article_recent.keyword,article_recent.blurb, article_recent.total from article_recent ";
	$sql .= "order by total desc limit 5";

	$view_results = exec_query($sql);
        $show_view = (count($view_results) > 0) ? true : false;

        /* most emailed */
	$sql = "select articles.id, articles.title,articles.keyword,articles.blurb,count(tracking_email.time) as total from articles ";
    $sql .= "join tracking_email on articles.id = tracking_email.id ";
	$sql .= "where approved='1' and is_live='1' ";
    $sql .= "and tracking_email.type = 'article' ";
    $sql .= "and tracking_email.time between date_sub('".mysqlNow()."',interval {$days} day) and '".mysqlNow()."' ";
    $sql .= "group by tracking_email.id ";
	$sql .= "order by total desc limit 5";

	$email_results = exec_query($sql);
        $show_email = (count($email_results) > 0) ? true : false;

        if ($latest) {
        /* most recent */
	$sql = "select articles.id, articles.title,articles.keyword,articles.blurb from articles ";
	$sql .= "where approved='1' and is_live='1' ";
	//$sql .= "and (articles.id !=" . $article['id'] . ") ";
	$sql .= "order by date desc limit 4";

        $latest_results = exec_query($sql);
        $show_latest = (count($latest_results) > 0) ? true : false;
        } else $show_latest = false;

	if ($show_email || $show_view || $show_latest) {

          $tab_settings = array('view','email','latest');

          $tab_count = 0;
          if ($show_view) { $tab_count++; $tab_settings['view']['pos'] = $tab_count; }
          if ($show_email) { $tab_count++; $tab_settings['email']['pos'] = $tab_count; }
          if ($show_latest) { $tab_count++; $tab_settings['latest']['pos'] = $tab_count; }

          $tab_settings['view']['box'] = 'none';
          $tab_settings['email']['box'] = 'none';
          $tab_settings['latest']['box'] = 'none';

          if ($show_view) { $tab_settings['view']['tab'] = 'on'; $tab_settings['view']['box'] = 'block'; }
          elseif ($show_email) { $tab_settings['email']['tab'] = 'on'; $tab_settings['email']['box'] = 'block'; }
          elseif ($show_latest) { $tab_settings['latest']['tab'] = 'on'; $tab_settings['email']['box'] = 'block'; }

          $tabs = '';
          if ($show_view) $tabs .= '<td id="tab-tab-'.$tab_settings['view']['pos'].'" class="' . $tab_settings['view']['tab'] .'"><a href="#" onclick="return tabShow('.$tab_settings['view']['pos'].','.$tab_count.')">Most Read</a></td>';
          if ($show_email) $tabs .= '<td id="tab-tab-'.$tab_settings['email']['pos'].'" class="' . $tab_settings['email']['tab'] .'"><a href="#" onclick="return tabShow('.$tab_settings['email']['pos'].','.$tab_count.')">Emailed</a></td>';
          if ($show_latest) $tabs .= '<td id="tab-tab-'.$tab_settings['latest']['pos'].'" class="' . $tab_settings['latest']['tab'] .'"><a href="#" onclick="return tabShow('.$tab_settings['latest']['pos'].','.$tab_count.')">Latest</a></td>';
          for($i = $tab_count; $i < 3; $i++) {
            $tabs .= '<td class="blank">&nbsp;</td>';
          }

        print <<<HTML
<div class="related-module-article">
<div class="FeatureHeaderGrayBgDouble">
  What's Popular in the 'Ville</div>
  <table cellpadding="0" cellspacing="0" class="tab-container">
    <tr class="tab-bar">{$tabs}</tr>
    <tr><td colspan="3" class="box-container">

HTML;

        if ($show_view) {
          print <<<HTML
      <div id="tab-box-{$tab_settings['view']['pos']}" class="tab-box" style="display:{$tab_settings['view']['box']}">
        <h3>Most Read</h3>
        <ol>

HTML;
         foreach($view_results as $row){
            print '         <li><a href="' . makeArticleslink($row['id'],$row['keyword'],$row['blurb']) . '">' . $row['title'] . '</a></li>' . "\n";
          }

          print <<<HTML
        </ol>

HTML;
    print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML
      </div>

HTML;

        } // if show_view

        if ($show_email) {
          print <<<HTML
      <div id="tab-box-{$tab_settings['email']['pos']}" class="tab-box" style="display:{$tab_settings['email']['box']}">
        <h3>Emailed</h3>
        <ol>

HTML;

          foreach($email_results as $row){
            print '         <li><a href="' . makeArticleslink($row['id'],$row['keyword'],$row['blurb']) . '">' . $row['title'] . '</a></li>' . "\n";
          }

          print <<<HTML
        </ol>
HTML;
    print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML
      </div>

HTML;
       } // if show_email

        if ($show_latest) {
          print <<<HTML
      <div id="tab-box-{$tab_settings['latest']['pos']}" class="tab-box" style="display:{$tab_settings['latest']['box']}">
        <h3>Latest</h3>
        <ol>

HTML;

          foreach($latest_results as $row){
            print '         <li><a href="' . makeArticleslink($row['id'],$row['keyword'],$row['blurb']) . '">' . $row['title'] . '</a></li>' . "\n";
          }

          print <<<HTML
        </ol>

HTML;
    print show_ads_operative($zone_name,$tile120x30,"290x62");

print <<<HTML

      </div>

HTML;
       } // if show_latest

        print <<<HTML
    </td></tr>
  </table>
</div>
HTML;

        } // if show_*
}

function displayregisterbox($USER)
{
	global $D_R,$_COOKIE,$IMG_SERVER;
	// change for unsubscribe functionality for email alert
    $frompage=$_GET['from'];
	if($frompage!=="emailsetting"){
	$html='<form method="post" action="/auth-2.htm" name="signinform">';
	}else{
		$html='<form method="post" action="/auth-2.htm?from='.$frompage.'" name="signinform">';
	}
	// change end here
	if (!$USER->isAuthed) { // display log in form

		$html.='<div class="Loginbox">';
		$html.='<div class="LoginTitle" align="center">Log-in</div>';
		$html.='<input type="text" size="15" maxlength="255" style="text-align:left,padding-top:7px;width:155px;border-color:#718697;" name="email" onFocus="javascript:textboxToEmail(this)" value="email" id="email" />';
		$html.='<input type="text" size="15" name="password" maxlength="255" style="text-align:left;width:155px;border-color:#718697; margin-top:4px;" onFocus="javascript:textboxToPassword(this)" value=" password" id="password" />';
		$html.='<div>';
		//$html.='<input type="image" id="loginbutton" class="LoginButton" src="'.$IMG_SERVER.'/images/button_go.gif" alt="" disabled=true />';
       $html.='<img id="loginbutton" class="LoginButton" style="cursor:pointer; border:none;" src="'.$IMG_SERVER.'/images/button_go.gif" alt="" disabled=true onclick="javascript:validateUserId()">';
		$html.='</div>';
		$html.='<div class="LoginText">';
		//Modified by samir
		$html.='<input type="checkbox" style="border:none;margin-top:2px;" name="setcookie" id="autologinbox">';
		$html.='<p>Remember Me</p>';
		$html.='</div>';

    	$html.='<div class="LoginTextmain" style="width:95%;"><a href="/register/new">Register</a> | <a href="/register/forgotpass.htm">Forgot Password</a></div></div>';
		$html.='<div class="homeBuzzSignin"><a href="#"> <img src="'.$IMG_SERVER.'/images/button_signin_to_buzz.gif" alt="Sign in to the Buzz &amp; Banter" onClick="javascript:banterWindow=window.open(\''.$pfx.'/buzz/buzz.php\',\'Banter\',\'width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\');banterWindow.focus();"></a></div>';
		$html.='</form>';
	}
	else
	{

		$html.='<div valign="top" class="ControlPanel" style="padding-right:4px;" >';
		$html.=makecontrolpanel();
		$html.='</form>';
		$html.=' <form method="post" name="referralFrm" action="http://app.peersuasion.com/public/registration.php?q=4f83dc988bba42f42a12374d518fe38d" target="_blank">';
		$html.='<input type="hidden" name="case" value="submit">';
                $html.='<input type="hidden" name="firstname" value="'.$USER->fname.' ">';
                $html.='<input type="hidden" name="lastname" value="'.$USER->lname .'">';
                $html.='<input type="hidden" name="email" value="'.$USER->email.'">';
                $html.='<div class="homeBuzzSignin"> <input type="image" src="'.$IMG_SERVER.'/images/recruit_button.gif" alt="Recruit A Minyan" width="161" height="49" style="border:0px;"/></div></form>';
 		$html.='</div>';

		$html.='</div> ';
//		$html.='</div> ';
	}
	echo $html;
}

/*ControlPanel*/
function makecontrolpanel()
{
	global $D_R,$IMG_SERVER,$countrequest,$count_article,$count_msg,$USER,$page_config;
	$objaddrequest=new friends();
	$loginquint = $USER->is_quint();
	$objThread=new Thread();
	$countrequest = $objaddrequest->count_pending_request($USER->id);
	//$count_article=$objThread->count_all_article($USER->id);
	$count_msg=$objThread->count_msg($USER->id);
	$count_updates=$objThread->countUpdates($USER->id);

	$html.='<table border="0" align="center" width="160px;" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"style="border-left:1px solid #cccccc; border-right:1px solid #cccccc; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">';
	$html.='<tr>
				<td bgcolor="#ecf4f7" class="ControlPanel">&nbsp;'.ucwords(strtolower($USER->fname)).'\'s �Ville </a>		</td>
			</tr>';
	if($USER->is_exchangeuser()){

	$productinfo=getuserproductsub($USER->email);
	//Added by samir for validating user previlages
	$exchange_servicequery="select es.id as id from ex_services es where es.serviceid='all_services'";
	$exchange_serviceresult=exec_query($exchange_servicequery,1);
	if(count($exchange_serviceresult)>0){
		$user_serviceid=$exchange_serviceresult['id'];
	}
	$exchange_prevresult=check_exprevilages($USER->id,$user_serviceid);
	if($exchange_prevresult>0)
	{
		$chk='true';
		//$chkmsg=$lang['comment_prevmsg'];
	}
	else
	{
		$chk='';
		//$chkmsg='';
	}//ends previlages here
	if(($chk) && (($productinfo['buzz']!='0') || ($productinfo['coper']!='0') || ($productinfo['quint']!='0')))
	{

	$html.='<tr>
				<td style="padding-top:8;" valign="middle" id="rightcolumn" valign="bottom"><a href="'.$USER->manageUrl.'#">Manage Settings.</a>
				</td></tr>';
	}
	else
	{
	$html.='<tr>
				<td style="padding-top:8;" valign="middle" id="rightcolumn" valign="bottom"><a href="'.$page_config['privacy']['URL'].'">Manage Settings</a>
				</td></tr>';
	}
	$html.='	<tr>
					<td style="padding-top:4;" id="rightcolumn"><a href="/auth-2.htm?LOGOUT=1">Log-out</a></td></tr>';
	$html.='	<tr>
					<td style="padding-top:4;" id="inbox"><a href="/community/inbox.htm"><img src="'.$IMG_SERVER.'/images/community_images/arrow-right-bullet.gif" /> INBOX <span id="cntrl_pnl_inbox">';
					if($count_msg!=0){
					$html.='('.$count_msg.')';
					}
					 $html.='</span></a></td></tr>';
	$html.='	<tr>
					<td style="padding-top:4;" id="inbox"><a href="/community/home.htm"><img src="'.$IMG_SERVER.'/images/community_images/arrow-right-bullet.gif" /> REQUESTS<span id="cntrl_pnl_requests">';
					if($countrequest!=0){
					$html.='('.$countrequest.')';
					}
					$html.='</span></a></td></tr>';
	$html.='	<tr>
					<td style="padding-top:4px; padding-bottom:5px" id="inbox" style="padding-bottom:10px;"><a href="/community/home.htm"><img src="'.$IMG_SERVER.'/images/community_images/arrow-right-bullet.gif" /> UPDATES<span id="cntrl_pnl_updates">';
					if($count_updates!=0){
					$html.='('.$count_updates.')';
					}
					$html.='</span></a></td></tr>';
	}
	else{
	$html.='<tr>
				<td style="padding-top:8;" valign="middle" id="rightcolumn" valign="bottom"><a href="'.$USER->manageUrl.'#">Manage Settings.</a>
				</td></tr>';
	$html.='	<tr>
					<td style="padding-bottom:10px; padding-top:4px;" id="rightcolumn"><a href="/auth-2.htm?LOGOUT=1">Log-out</a></td></tr>';
	}
$html.='<tr><td style="padding-top:;"><img src="'.$IMG_SERVER.'/images/community_images/launch-the-Buzz.gif" style="cursor: pointer;" onClick="javascript:banterWindow=window.open(\''.$pfx.'/buzz/buzz.php\',\'Banter\',\'width=350,height=708,resizable=yes,toolbar=no,scrollbars=no\');banterWindow.focus();" /></td></tr>';
	$html.='</table>';
	return $html;

}


function displaysearchbox()
{
	 global $SEC_TO_ZONE,$IMG_SERVER,$SEC_TO_ZONE_OPENX,$HTPFX,$HTHOST;
	 $referer_url=parse_url($_SESSION['referer']);
	 list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
	 $html='<div class="SearchBG"><form method="post" name="searchFrm" action="'.$HTPFX.$HTHOST.'/library/search.htm?search=Article&advanced=1&mo=&day=&year=&contrib_id=&category_id=">';
	 $html.='<p class="SearchTitle">SEARCH';
	 $html.='<input type="text" name="q" value="Keywords or Stock Symbols" class="SearchField" align="left" onFocus="javascript:textboxClear(this)"/></p>';

	 $html.='<div style="margin-left:10px;">';
 	 echo $html;
	 if($domain!="ameritrade") {?><? show_ads_openx("Search88x31",$SEC_TO_ZONE_OPENX['Search88x31'],$ADS_SIZE['MicroBar']);?><? }?>
<input type="image" src="<?=$IMG_SERVER;?>/images/button_search_go_1.gif" alt=""  style="border:none; margin-bottom:2px; margin-left:3px;"></div></form></div>

<?
}

function displayRecentCatArticles($article) {


	$sql = "select articles.id, articles.title,articles.date,articles.keyword,articles.blurb from articles,contributors,article_categories ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and articles.contrib_id = contributors.id ";
	$sql .= "and (articles.id !=" . $article['id'] . ") ";
	$sql .= "and find_in_set( article_categories.id, articles.category_ids )";
	$sql .= "and (article_categories.title ='" . $article['category'] . "') ";
	$sql .= "order by date desc limit 4";


	$results = exec_query($sql);
	if (count($results) > 1) {

?>
<div id="related-module-article">

	<div class="">
		<h2 class="bar_new">More on <?= $article['category'];?></h2>
            <div id="rel1">
			<? foreach($results as $row) {
			?>
			<h4><a class="articleLink" href=<?=makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?>><?= $row['title'];?></a></h4>
			</br>
			<?
			}

			?>
			</div>
	</div>


</div>

<? }
	else {
	//do nothing.
	}
}


function displayRecentArticles($article) {


	$sql = "select articles.id, articles.title,articles.date,articles.keyword,articles.blurb from articles,contributors ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and articles.contrib_id = contributors.id ";
	$sql .= "and (articles.id !=" . $article['id'] . ") ";
	$sql .= "order by date desc limit 4";

	$results = exec_query($sql);
	if (count($results) > 1) {

?>
<div id="related-module-article">

	<div class="">
		<h2 class="bar_new">Minyanville Headlines</h2>
			<div id="rel1">
			<? foreach($results as $row) { ?>

			  <h4><a class="articleLink" href=<?=makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?>><?= $row['title'];?></a></h4>
			  </br>
			<?
			}

			?>
			</div>
	</div>


</div>

<? }
	else {
	//do nothing.
	}
}

function prepareRelatedArticlesBox($article,$strProfessorId,$oid) {
	build_lang('cooperhome');
	global $lang,$HTPFX,$HTHOST;
	$sql = "select id, title,keyword,blurb,date from cp_articles ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and prof_id = " . $article['prof_id'] . " ";
	$sql .= "and contributor = '" . $article['author'] . "' ";
	if ($article['authorid'] != 0) {
		$sql .= "or (contrib_id = " . $article['authorid'] . " and contributor=NULL) ";
	}
	$sql .= "and (id !=" . $article['id'] . ") ";
	 $sql .= "order by date desc limit 6";

	$results = exec_query($sql);
	if (count($results) > 1) {?>
		<div class="right_common_container">
			<div class="right_common_head"><h2><?=$lang['Related_Article_Title']; ?></h2></div>
			<div class="cooper_report_block_main">
				<? foreach($results as $row) { ?>
				<div class="recent_heading"><a class="articleLinkProf" href=<?=$HTPFX.$HTHOST.makeArticleslinkcooper($row['id'],$row['keyword'],$row['blurb']);?>><?= $row['title'];?></a></div>
				<?}?>
				<div class="jack_full_archive"><span style="cursor:pointer;" onclick="searchalert('coopersearch','<?=$strProfessorId?>','cooperhome','<?=$oid?>');">See Full Archive >></span></div>
			</div>
		</div>
	<? }else {return '';}
}
function prepareRelatedJackArticlesBox($article,$strProfessorId,$oid) {
	global $lang;
	build_lang('jack_home');

	$sql = "select id, title,keyword,blurb,date from jack_articles ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and prof_id ='".$strProfessorId."' ";
	$sql .= "and contributor = '" . $article['author'] . "' ";
	if ($article['authorid'] != 0) {
		$sql .= "or (contrib_id = " . $article['authorid'] . " and contributor=NULL) ";
	}
	$sql .= "and (id !=" . $article['id'] . ") ";
	 $sql .= "order by date desc limit 6";
	$results = exec_query($sql);
	if (count($results) > 0) {?>
		<div class="right_common_container">
			<div class="right_common_head"><h2><?=$lang['Related_Article_Title']; ?></h2></div>
			<div class="cooper_report_block_main">
				<? foreach($results as $row) { ?>
				<div class="recent_heading"><a class="articleLinkProf" href=<?=makeArticleslinkjack($row['id'],$row['keyword'],$row['blurb']);?>><?= $row['title'];?></a></div>
				<?}?>
				<div class="jack_full_archive"><span style="cursor:pointer;" onclick="searchalert('jacksearch','<?=$strProfessorId?>','jack_home','<?=$oid?>');">See Full Archive >></span></div>
			</div>
		</div>
	<? }else {return '';}
}
function prepareRelatedArticlesBox_old($article) {

	$sql = "select id, title,keyword,blurb,date from articles ";
	$sql .= "where approved='1' and is_live='1' ";
	$sql .= "and contributor = '" . $article['author'] . "' ";
	if ($article['authorid'] != 0) {
		$sql .= "or (contrib_id = " . $article['authorid'] . " and contributor=NULL) ";
	}
	$sql .= "and (id !=" . $article['id'] . ") ";
	$sql .= "order by date desc limit 6";
	$results = exec_query($sql);
	if (count($results) > 1) {

?>

	<div class="right_common_head"><h2>Also by this Professor</h2></div>
			 	<div class="cooper_report_block_main">
		 	<? foreach($results as $row) { ?>
				<div class="recent_heading"><a class="articleLinkProf" href=<?=makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?>><?= $row['title'];?></a></div>

		<?}

			?>           </div></div>
<? }
	else {
	//do nothing.
	}
}


function showgoogleads($width=468,$height=60)
{

?>
	<table align="center" cellspacing="0" cellpadding="0">
	<tr><td>
	<script type="text/javascript"><!--
		google_ad_client = "pub-3503511063624925";
		google_ad_width = <?=$width?>;
		google_ad_height = <?=$height?>;
		google_ad_format = "<?=$width?>x<?=$height?>_as";
		google_ad_type = "text";
		google_ad_channel = "";
		google_color_border = "666666";
		google_color_bg = "FFFFFF";
		google_color_link = "4C4C4C";
		google_color_text = "000000";
		google_color_url = "008000";
		google_ui_features = "rc:6";
	//-->
	</script>
	<script type="text/javascript"
		  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</td></tr></table>
<?
}

function makesharevillebox($article)
{
?>
	<div class="FeatureHeaderGrayBgDouble">Share the 'Ville</div>
	<div class="Sharebox">
       <table width="100%" cellpadding="0" cellspacing="3">
                                            <tr>
                                              <td><a href="/share/email.htm?type=article&id=<?= $article[id] ?>"><img src="<?=$IMG_SERVER;?>/images/icons/email-icon.gif" width="17" height="12" alt="" style="margin-right:17px" /> Email</a></td>
                                              <td><a href="http://del.icio.us/post" onclick="window.open('http://del.icio.us/post?v=4&noui&jump=close&url=<?= urlencode($article['link']) ?>&title=<?= urlencode($article['title']) ?>', 'delicious','toolbar=no,width=700,height=400'); return false;"><img src="<?=$IMG_SERVER;?>/images/icons/delicious-logo-small.gif" width="16" height="16" alt="" />&nbsp; Del.icio.us</a></td>
                                            </tr>
                                            <tr>
					      <td><a href="javascript:print(<?= $article[id]; ?>);" target="_self"><img src="<?=$IMG_SERVER;?>/images/icons/print-icon.gif" width="16" height="14" alt="" style="margin-right:18px" /> Print</a></td>
                                              <td><a href="http://digg.com/submit" onclick="window.open('http://digg.com/submit?phase=2&url=<?= urlencode($article['link']) ?>&title=<?= urlencode($article['title']) ?>&bodytext=<?= urlencode($article['blurb']) ?>&topic=business_finance', 'digg','toolbar=no,resizeable=1,scrollbars=1,width=975,height=750'); return false;"><img src="<?=$IMG_SERVER;?>/images/icons/digg-logo-small.gif" width="16" height="16" alt="" /> &nbsp;Digg</a></td>
                                            </tr>
                                            <tr>
                                              <td><a href="/rss/"><img src="<?=$IMG_SERVER;?>/rss/rss-logo-small.gif" width="27" height="15" alt="" /> &nbsp; Subscribe</a></td>
                                              <td><a href="http://www.facebook.com/posted.php" onclick="window.open('http://www.facebook.com/sharer.php?src=bm&v=4&i=1176837783&u=<?= urlencode($article['link']) ?>&t=<?= urlencode($article['title']) ?>', 'sharer','toolbar=no,resizeable=1,scrollbars=1,width=626,height=436'); return false;"><img src="<?=$IMG_SERVER;?>/images/icons/facebook-logo-small.gif" width="16" height="16" alt="" />&nbsp; Facebook</a></td>
                                            </tr>
                                            </table>
</div>
<?
}

function makemvtvThumbnailContainer($offset,$download=0)
{
?>
<div id="NewsViewsTab"><a href="http://www.foxbusiness.com"><img src="<?$IMG_SERVER;?>/images/mvtv_foxbusiness.jpg" width="323" height="74" /></a><br />
  <table cellspacing="0" cellpadding="0">
    <tr>
      <td><img src="<?$IMG_SERVER;?>/images/mvtv_tabs_newsviews_on.gif" /></td>
      <td><a href="javasacript:;" onmousedown="document.getElementById('NewsViewsTab').style.display='none'; document.getElementById('WorldReviewTab').style.display='block'"><img src="<?$IMG_SERVER;?>/images/mvtv_tabs_worldreview_off.gif" width="161" height="38" border="0" /></a></td>
    </tr>
  </table>

  <div class="MvtvArticleModuleHeaders">Episodes</div>
  <div class="mvtvThumbnailContainer">
    <table border="0" cellspacing="0" cellpadding="0">
	 <?
    $sql="SELECT * FROM mvtv WHERE approved='1' and publish_time<'".mysqlNow()."' and fox='1' ORDER BY publish_time DESC LIMIT ".$offset.",9";
	$results = exec_query($sql);
	$thumbrows=count($results)/3;
	$count=0;
	foreach($results as $row){
		$mvtv[$count]['title']=$row['title'];
		$mvtv[$count]['thumbfile']=$row['thumbfile'];
		$mvtv[$count]['id']=$row['id'];
		$mvtv[$count]['videofile_wmv']=$row['videofile_wmv'];
		$count++;
		}
  ?>
      <tr>
        <td width="10">&nbsp;</td>
        <td>
	<table width="312" border="0" cellspacing="0" cellpadding="0">
	<?
	$count=0;
	for($i=0;$i<$thumbrows;$i++)
	{ ?>
	<tr>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>"><img src="<?=$mvtv[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
	</tr>
	<? $count-=3;?>
	<tr>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtv[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset; ?>">&gt;<?= $mvtv[$count]['title']; ?></a><? } ?><? if($download && $mvtv[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtv[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
	</tr>

   	 <?if($mvtv[$count]){ ?><tr> <td height="8" colspan="3" valign="middle"><img src="<?$IMG_SERVER;?>/images/mvtv_horizontal_line.gif" width="313" height="1" /></td></tr><? } ?>

	<? } ?>
    </table>
		</td>
        <td width="10">&nbsp;</td>
      </tr>
</table>
<div>Pages<br /><?
    	$sql="SELECT * FROM mvtv WHERE approved='1' and publish_time<'".mysqlNow()."' and fox='1' ORDER BY publish_time";
  	$numrows=num_rows($sql);
  	$numPages = floor(($numrows + 9 - 1) / 9);
  	for($i=0;$i<$numPages;$i++)
  	{
  		if($offset==$i*9)
  			echo '<font color="#000000">'.($i+1).'</font>';
  		else{
  	?>
  		<a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?offset='.($i*9) ?>"><?=$i+1?></a>
  <?	}}
    ?>
  </div>
  </div></div>

<div id="WorldReviewTab" style="display:none"><a href="http://finance.yahoo.com/video/provider/minyanville"><img src="<?$IMG_SERVER;?>/images/mvtv_yahoofinance.jpg" width="323" height="74" /></a><br />
  <table cellspacing="0" cellpadding="0">
    <tr>
      <td><a href="javasacript:;" onmousedown="document.getElementById('WorldReviewTab').style.display= 'none'; document.getElementById('NewsViewsTab').style.display='block'"><img src="/images/mvtv_tabs_newsviews_off.gif" border="0" /></a></td>
      <td><img src="<?$IMG_SERVER;?>/images/mvtv_tabs_worldreview_on.gif" width="161" height="38" border="0" /></td>
    </tr>
  </table>
  <div class="MvtvArticleModuleHeaders">Episodes</div>
  <div class="mvtvThumbnailContainer">
    <table border="0" cellspacing="0" cellpadding="0">
	<?

   $sqlreviewTab="SELECT * FROM mvtv WHERE approved='1' and publish_time<'".mysqlNow()."' and yahoo='1' ORDER BY  publish_time DESC LIMIT ".$offset.",9";

	$resultsreviewTab = exec_query($sqlreviewTab);
	$thumbrows_review=count($resultsreviewTab)/3;
	$count=0;
	foreach($resultsreviewTab as $row){
		$mvtvreview[$count]['title']=$row['title'];
		$mvtvreview[$count]['thumbfile']=$row['thumbfile'];
		$mvtvreview[$count]['id']=$row['id'];
		$mvtvreview[$count]['videofile_wmv']=$row['videofile_wmv'];
		$count++;
		}
  ?>
      <tr>
        <td width="10">&nbsp;</td>
        <td>
	<table width="312" border="0" cellspacing="0" cellpadding="0">
	<?
	$count=0;
	for($i=0;$i<$thumbrows_review;$i++)
	{ ?>
	<tr>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtvreview[$count]['id'].'&offset='.$offset.'&t=1'; ?>"><img src="<?=$mvtvreview[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtvreview[$count]['id'].'&offset='.$offset.'&t=1'; ?>"><img src="<?=$mvtvreview[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtvreview[$count]['id'].'&offset='.$offset.'&t=1'; ?>"><img src="<?=$mvtvreview[$count]['thumbfile'];?>" width="104" height="107" /></a><? } $count++;?></td>
	</tr>
	<? $count-=3;?>
	<tr>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtvreview[$count]['id'].'&offset='.$offset.'&t=1'; ?>">&gt;<?= $mvtvreview[$count]['title']; ?></a><? } ?><? if($download && $mvtvreview[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtvreview[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtvreview[$count]['id'].'&offset='.$offset.'&t=1'; ?>">&gt;<?= $mvtvreview[$count]['title']; ?></a><? } ?><? if($download && $mvtvreview[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtvreview[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
		<td><? if($mvtvreview[$count]) { ?><a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?videoid='.$mvtv[$count]['id'].'&offset='.$offset.'&t=1'; ?>">&gt;<?= $mvtvreview[$count]['title']; ?></a><? } ?><? if($download && $mvtvreview[$count]['videofile_wmv']!="") { ?><br /><br /><a href="<?=$mvtvreview[$count]['videofile_wmv'];?>">Click Here To Download</a><? } $count++;?></td>
	</tr>

   	 <?if($mvtvreview[$count]){ ?><tr> <td height="8" colspan="3" valign="middle"><img src="<?$IMG_SERVER;?>/images/mvtv_horizontal_line.gif" width="313" height="1" /></td></tr><? } ?>

	<? } ?>
    </table>
		</td>
        <td width="10">&nbsp;</td>
      </tr>
</table>
<div>Pages<br /><?
    	$sqlreviewpages="SELECT * FROM mvtv WHERE approved='1' and publish_time<'".mysqlNow()."' and yahoo='1' ORDER BY  publish_time";
  	$numrowsreview=num_rows($sqlreviewpages);
  	$numPagesreview = floor(($numrowsreview + 9 - 1) / 9);
  	for($i=0;$i<$numPagesreview;$i++)
  	{
  		if($offset==$i*9)
  			echo '<font color="#000000">'.($i+1).'</font>';
  		else{
  	?>
  		<a href="<?=$HTPFX.$HTHOST.$MVTVURL.'?offset='.($i*9).'&t=1' ?>"><?=$i+1?></a>
  <?	}}
    ?>
  </div>
  </div></div>

<? }
function showhomemvtv()
{
	global $HTPFX,$HTHOST;
	$sql="select * from mvtv where approved='1' and publish_time<'".mysqlNow()."' order by RAND() desc limit 0,1";
	$results=exec_query($sql);
	?>
	<!-- ********  MVTV  ********  -->
	<div class="">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td height="5" align="center" valign="bottom">
	<!--
	<script type="text/javascript">
	AC_FL_RunContent( 'codebase','http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0','wmode','transparent','width','306','height','272','src','<?=$HTPFX?><?=$HTHOST?>/home_videoplayer?videoURL=<?=$results[0]['videofile'];?>&title=<?=$results[0]['title'];?>&still=<?=$results[0]['stillfile'];?>','quality','high','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','<?=$HTPFX?><?=$HTHOST?>/home_videoplayer?videoURL=<?=$results[0]['videofile'];?>&title=<?=$results[0]['title'];?>&still=<?=$results[0]['stillfile'];?>' ); //end AC code
	</script><noscript>
	-->
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="297" height="279">
	<param name="movie" value="<?=$HTPFX?><?=$HTHOST?>/home_load/home_mvtv.swf?caption2=<?=$results[0]['title'];?>&still2=<?=$results[0]['stillfile'];?>&videoid=<?=$results[0]['id'];?>">
	<param name="quality" value="high">
	<param name="wmode" value="transparent">
	<embed src="<?=$HTPFX?><?=$HTHOST?>/home_load/home_mvtv.swf?caption2=<?=$results[0]['title'];?>&still2=<?=$results[0]['stillfile'];?>&videoid=<?=$results[0]['id'];?>" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" wmode="transparent" width="297" height="279"></embed>
	</object>
	<!--</noscript>-->
	</td>
	</tr>
	</table>
	</div>
<!-- ********  MVTV end ********  -->
	<?
}

function refresh_Column_Module_List_Image($pageid,$columnName,$modulesArray,$extraData,$div) {

	//if $pageid is not numeric, then query the database for the id with the given name.
	if (!is_numeric($pageid)) {
		$pageid = getPageId($pageid);
	}

	// get the column id with the given column name.
	if (!is_numeric($columnName)) {
		//request column id from function because column name was submitted to function.
		$columnid = getColumnId($pageid, $columnName);
	} else {
		//$columnName is actually submitted as id
		$columnid = $columnName;

	}

	//get modules
	$modList = listModules($pageid,$columnid);
	$intoArray = explode(",",$modList);

	//get column name for the available information columnid and pageid
	$getColumnName = exec_query("SELECT name FROM layout_columns WHERE id=" . $columnid . " AND pageid=" . $pageid);
	foreach($getColumnName as $row){
		$columnName = $row['name'];
	}

		if ($intoArray[0] != "") {
		//if (count($intoArray) != 0) {
			//echo "contents of the array = " . print_r(array_values($intoArray));
			$moduleCounter = 0;
			foreach($intoArray as $mod) {
				$dynamic = false;
				$moduleCounter++;
				//if ($showType == "show")  {
					$getModuleInfo = moduleExists($mod);

					if ($getModuleInfo != false) {
						foreach ($getModuleInfo as $key => $value) {
							//determine the type of the module . dynamic or static
							if (($key =="type") && ($value=="dynamic")) {
								$dynamic = true;
							}

							if ($key == "html") {
								$previewItems[$key] = htmlentities($value);
								//$$key = htmlentities($value);
								$htmlShow = stripslashes($value);
							} else {
								$previewItems[$key] = stripslashes($value);
								$$key = stripslashes($value);
							}
						}

					}

				//special catch for articles' also by this author module where the author listed is not a real contributor.
				// i.e if hoofy writes an article, do not display 'also by this author' instead display recent headlines
				if (($dynamic==true) && ($extraData['category'] == "") && ($extraData['authorid'] == 0 || $extraData['authorid']=="")) {
					$sql = "select articles.id id, title, name author, date,articles.keyword keyword,blurb, contributor from articles, contributors where articles.contrib_id = contributors.id and articles.approved='1' and articles.is_live='1' and articles.is_public='1' order by date desc limit 4";
					$name = "Recent Headlines";
				}

				//display module type
				if ($dynamic==true) { ?>

					<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

				<?
					//if extraData is an array, then extra information is needed for the dynamic modules.
					if (is_array($extraData)) {
						$authorid = $extraData['authorid'];
						$articleid = $extraData['id'];
						$category = $extraData['category']; //used for modules that display most recent articles from a specific category
					} else {


					}



					//search for variables and replace with the current data
					//this is used with page / article specific dynamic modules.
					$sql = str_replace("AUTHOR_ID", $authorid, $sql);
					$sql = str_replace("ARTICLE_ID",$articleid, $sql);
					$sql = str_replace("CATEGORY",$category, $sql);

					$results = exec_query($sql);
					foreach($results as $row){
						//reformat the date from the database
						$authorInfo= returnRealAuthor($row['author'],$row['contributor']);
						$row['author'] = $authorInfo['name'];
						$row['date'] = date('F d, Y g:i a',strtotime($row['date']));
						$tempHTML = $htmlShow;

						//loop through each field returned from the query, replace [keywords] with matching field names, ie. [title] replaced with $title, [author] replaced with $author
						foreach($row as $field => $value) {
							$tempHTML = str_replace("[".$field."]",$value,$tempHTML);
						}

						$tempHTML = str_replace("&lt;","<a class=\"articleLink\" href=\"" . makeArticleslink($row['id'],$row['keyword'],$row['blurb']). "\">",$tempHTML);
						$tempHTML = str_replace("&gt;","</a>",$tempHTML);
						echo $tempHTML;

					}
				?>

					</div>
				<?
				} else {
				?>

				<div class="modulebox">
					<? if ($name != "") { ?>
					<div class="FeatureHeaderGrayBgDouble">
						<?= $name; ?> </div>
						<? } ?>

						<div  class="moduleinnerimage"><?= $htmlShow; ?></div><br>

					</div>


				<?
				} //end display module

				//}
			}

		} else {
			//echo "<font color=red>add modules below</font><br>";
		}
}

function showtradingcenter()
{
    global $_SESSION,$HTPFX,$HTHOST;
	$referer_url=parse_url($_SESSION['referer']);
	list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
	if($domain!="ameritrade") {
?>
<iframe width="306px;" height="185px;" frameborder="0" scrolling="no" title="Trading Center" src="<?=$HTPFX.$HTHOST?>/layout/trading_center.php"></iframe>
<?	}
}


function showbigcharts()
{
	global $SEC_TO_ZONE,$_SESSION,$SEC_TO_ZONE_OPENX,$HTPFX,$HTHOST;
	$referer_url=parse_url($_SESSION['referer']);
	list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
?>
<div class="HeaderGrayBg160" style="clear: both; width: 174px;">Market</div>
<? if($domain!="ameritrade") {?><div align="center"><b>Sponsored By:</b><br><? show_ads_openx("BigCharts",$SEC_TO_ZONE_OPENX['BigCharts'],"");/*show_ads_openads($SEC_TO_ZONE['BigCharts'])*/?></div><br /><? }?>
<iframe width="173px" height="320px" frameborder="0" scrolling="no" title="Trading Center" src="http://minyanville.investorsintelligence.com/minyanville/charts.html"></iframe>
<br />
<?
}
/*

function showindustrybrains()
{
global $_SESSION;
$referer_url=parse_url($_SESSION['referer']);
list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
if($domain!="ameritrade") {
?>
<br />
<div class="HeaderGrayBg468">Minyanville MarketPlace</div>
<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=468&amp;num=5&amp;layt=3&amp;fmt=simp"></script>
<?
}}

function showindustrybrains_hp()
{
global $_SESSION;
$referer_url=parse_url($_SESSION['referer']);
list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
if($domain!="ameritrade") {
?>
<br />
<div class="HeaderGrayBg468">Minyanville MarketPlace</div>
<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=468_HP&amp;num=5&amp;layt=3&amp;fmt=simp"></script>
<?
}}

function showindustrybrains_ap()
{
global $_SESSION;
$referer_url=parse_url($_SESSION['referer']);
list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
if($domain!="ameritrade") {
?>
<br />
<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=468_ARTICLE&amp;num=5&amp;layt=3&amp;fmt=simp"></script>
<?
}}

*/

function showcurrencyfeed(){
		global $SEC_TO_ZONE;
?>
 <div align="center"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="160" height="200" id="main" align="middle">
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="http://www.gftforex.com/quoteboard/main.swf?theInitFile=http://www.gftforex.com/quoteboard/10012007-minyanville.xml" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />
	<embed src="http://www.gftforex.com/quoteboard/main.swf?theInitFile=http://www.gftforex.com/quoteboard/10012007-minyanville.xml" quality="high" bgcolor="#ffffff" width="160" height="200" name="main" align="middle" valign="top" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object></div><div align="center"><? show_ads_openads($SEC_TO_ZONE['ForexCharts'])?></div>
<?
}

function showmarketwatch()
{
	global $D_R,$IMG_SERVER,$MarketWatch;
	require_once("$D_R/lib/rss_read.class.php");
	$RSSread = new RSSread(1);
	$RSSconf["rssFeed"] = $MarketWatch["TopStories"];
	$RSSconf["numPosts"] =4;
	$RSSconf["postLinkTarget"] = "_blank";
	$RSSread->ScriptConfig($RSSconf);
	$RSSread->getPosts();
?>
<script type="text/javascript">
function focused(obj) {
    if (obj.value == obj.defaultValue) {
        obj.value = "";
    }
}

function blurred(obj) {
    if (obj.value == "") {
        obj.value = obj.defaultValue;
    }
}
</script>
          <div class="marketwatch">
    <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="7" rowspan="5">&nbsp;</td>
      <td><img src="<?=$IMG_SERVER;?>/images/mw-head-trans24.png" alt="" /></td>
      <td width="7" rowspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td>
        <table border='0'><tr><td valign="top"> <?input_text("quote","Symbol Search",13,"","class=\"marketwatchForm\" onBlur=\"blurred(this)\" onFocus=\"focused(this)\"")?></td><td valign="baseline"><input type="image" src="<?=$IMG_SERVER;?>/images/marketwatch_gobutton.gif" alt="go" class="LoginButton" onclick="javascript:openmarketwatchquotes();" /></td></tr></table></td>
      </tr>
    <tr><td height="7px"></td></tr>
      <tr><td><center><h6 class="marketwatchTopStories">TOP STORIES </h6> </center></td></tr>
    <tr>
      <td>

      <? foreach($RSSread->rssData as $id=>$value){ ?>
       <a href="<?=$value['link']?>" target="_blank"><?=$value['title']?></a>
      <? }?>
</td>
      </tr>
  </table>
  </div>
<?
}


function showfoxnews()
{
	global $D_R,$IMG_SERVER,$MarketWatch,$FoxBusiness;
	require_once("$D_R/lib/rss_read.class.php");
	$RSSread = new RSSread(1);
	$RSSconf["rssFeed"] = $FoxBusiness["TopStories"];
	$RSSconf["numPosts"] =4;
	$RSSconf["postLinkTarget"] = "_blank";
	$RSSread->ScriptConfig($RSSconf);
	$RSSread->getPosts();
?>
    <div class="foxbusiness">
    <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3" style="background-image:url(http://storage.googleapis.com/mvassets/images/Minyanville_FXB_header_new.jpg);height:33px;"></td>
    </tr>
    <tr><td height="7px"></td></tr>
      <tr><td><center><h6 class="foxbussinessTopStories">TOP STORIES </h6> </center></td></tr>
    <tr>
      <td>
      <? foreach($RSSread->rssData as $id=>$value){ ?>
       <a href="<?=$value['link']?>" target="_blank"><?=$value['title']?></a>
      <? }?>
</td>
      </tr>
  </table>
  </div>
<?
}

//Added by samir for Profile Friend Information
function viewfriend($subid,$subuserid)
{
  global $friendnavlmt,$friendoffsetlmt,$page_config;
  $friendurl="profile_edit.htm";
  $profiletype="friend";
  $pagetype="Profile";
	$profileID= new Thread();
	$type=$profileID->get_object_type($pagetype);
  $viewfriend = "select f.friend_subscription_id as friend_subscription_id, s.fname as fname, s.lname as lname from ex_user_friends f,subscription s ";
  $viewfriend .= "where f.friend_subscription_id = s.id ";
  $viewfriend .= "and f.subscription_id='".$subid."'";
  $resviewfriend = exec_query($viewfriend);
  $viewfriendno = count($resviewfriend);
  $viewfrndliststr='';

  $viewfrndliststr.="<tbody id='hidfriend'>";
  $viewfrndliststr.="<tr>";
  $viewfrndliststr.="<td valign='middle' width='34'>";
  if(($subid!=$subuserid)&&($viewfriendno>0))
  {
   $viewfrndliststr.="<a onClick='viewfulllist(\"".$friendurl."\",\"".$profiletype."\",\"".$subid."\");' style='cursor: pointer;'>";
   }
   $viewfrndliststr.="<img src='".$IMG_SERVER."/images/community_images/friends.gif' width='31' height='25' valign='absmiddle'/>";
  if(($subid!=$subuserid)&&($viewfriendno>0))
  {
   $viewfrndliststr.="</a>";
   }

   $viewfrndliststr.="</td>";
	if(($subid==$subuserid)&&($viewfriendno>0))
  {
    $viewfrndliststr.="<td align='left' class='Profilewatchlist' style='vertical-align:bottom;'>
					      Friends (".$viewfriendno.") </td> <td align='left' width='10' style='vertical-align:bottom;'><span id='Profileedit2'><a style='cursor: pointer;' onDblClick='Javascript:editProfileInfo(\"".$friendurl."\",\"".$profiletype."\");' onClick='Javascript:editProfileInfo(\"".$friendurl."\",\"".$profiletype."\");'>Edit</a></span> </td>";
	}
	else
	{
      $viewfrndliststr.="<td align='left' class='Profilewatchlist' style='vertical-align:bottom;'>";
	  if($viewfriendno>0)
      {
        $viewfrndliststr.="<a href=".$page_config['friends']['URL']."".'?id'."".'='."".$subid.">";
      }
		$viewfrndliststr.="Friends (".$viewfriendno.")&nbsp;";
	  if($viewfriendno>0)
      {
        $viewfrndliststr.="</a>";
      }
	$viewfrndliststr.="<span class='profilesugg'></span> </td>";
  }
  $viewfrndliststr.="</tr>";
  if($viewfriendno > 0)
  {
  	$viewfriend=$viewfriend." order by f.id desc limit $friendoffsetlmt,$friendnavlmt";
	$viewfrndliststr.="<tr>";
	$viewfrndliststr.="<td colspan='3'><table style='padding-top:10px; padding-bottom:10px;' class='profileAppl' width='100%' border='0' cellspacing='0' cellpadding='0'>";
	$viewfrndliststr.="<tr>";
	$viewfrndliststr.="<td class='profilewatchlistcontent'>";
  	foreach(exec_query($viewfriend) as $viewfriendrow)
  	{
		$field='';
		$friend_subscription_id=$viewfriendrow['friend_subscription_id'];
		$val=ucwords(strtolower($viewfriendrow['fname']." ".$viewfriendrow['lname']));
		$friendslink=$page_config['profile']['URL']."?userid=".$friend_subscription_id;
       	$viewfrndliststr.="<span id='profilewatchlistcontent'><a href='".$friendslink."'>".$val."</a></span><br/>";
    }
    $viewfrndliststr.="</td>";
	$viewfrndliststr.="</tr>";
	if($viewfriendno>$friendnavlmt)
	{
	   if(($subid!=$subuserid)&&($viewfriendno>0))
      {
	$viewfrndliststr.="<tr><td class='profilewatchlistcontent'><span id='Profileedit1'><a href=".$page_config['friends']['URL']."".'?id'."".'='."".$subid.">View All Friends</a></span></td></tr>";
	 } else {
	$viewfrndliststr.="<tr><td class='profilewatchlistcontent'><span id='Profileedit1'><a onClick='viewfulllist(\"".$friendurl."\",\"".$profiletype."\",\"".$subid."\");' style='cursor:pointer;'>View All Friends</a></span></td></tr>";
		}
		}
	$viewfrndliststr.="</table>";
	$viewfrndliststr.="</td>";
	$viewfrndliststr.="</tr>";
	}
	$viewfrndliststr.="</tbody>";
	return $viewfrndliststr;
}

//Added by samir for Profile watchlist Information
function viewwatchlist($subid,$subuserid)
{
  global $watchlistnavlmt,$watchlistwordlimit,$watchlistoffsetlmt;
  $pagetype="Profile";
  $profileID= new Thread();
  $type=$profileID->get_object_type($pagetype);

  $watchlisturl="profile_edit.htm";
  $profiletype="watchlist";
  $viewwatchlist = "select s.stocksymbol as stocksymbol, s.exchange as exchange, s.SecurityName as SecurityName from ex_user_stockwatchlist sw,ex_stock s ";
  $viewwatchlist .= "where sw.stock_id = s.id ";
  $viewwatchlist .= "and sw.subscription_id='".$subid."'";

  $resviewwatchlist = exec_query($viewwatchlist);
  $viewwatchlistno = count($resviewwatchlist);
  $viewwatcliststr='';

  $viewwatcliststr="<tbody id='watchlist'>";
  $viewwatcliststr.="<tr>";
  $viewwatcliststr.="<td width='33'>";
  if(($subid!=$subuserid)&&($viewwatchlistno>0)){
  	$viewwatcliststr.="<a onClick='viewfulllist(\"".$watchlisturl."\",\"".$profiletype."\",\"".$subid."\");' style='cursor:pointer;'>";
  }
  $viewwatcliststr.="<img src='".$IMG_SERVER."/images/community_images/watchlist.gif' width='33' height='18' valign='absmiddle'/>";
  if(($subid!=$subuserid)&&($viewwatchlistno>0)){
  $viewwatcliststr.="</a>";
  }
  $viewwatcliststr.="</td>";
	if(($subid==$subuserid))
  {
	  $viewwatcliststr.="<td  class='Profilewatchlist' nowrap='nowrap' style='vertical-align:bottom;'>Watchlist (".$viewwatchlistno.")</td> <td  align='left' style='vertical-align:bottom;'><span id='Profileedit2'><a style='cursor: pointer;' onClick='editProfileInfo(\"".$watchlisturl."\",\"".$profiletype."\");'>&nbsp;Edit</a></span></td>";
	}
	else
	{
	   $viewwatcliststr.="<td class='Profilewatchlist' nowrap='nowrap' style='vertical-align:bottom;'>";
	   if($viewwatchlistno>0){
		$viewwatcliststr.="<a onClick='viewfulllist(\"".$watchlisturl."\",\"".$profiletype."\",\"".$subid."\");' style='cursor:pointer;'>";
	   }
	   $viewwatcliststr.="Watchlist (".$viewwatchlistno.")&nbsp;";
	   if($viewwatchlistno>0){
		$viewwatcliststr.="</a>";
	   }
	   $viewwatcliststr.="<span class='profilesugg'></span></td>";
  }



  $viewwatcliststr.="</tr>";
  if($viewwatchlistno > 0)
  {
  	$viewwatchlist=$viewwatchlist." order by sw.id desc limit $watchlistoffsetlmt,$watchlistnavlmt";
	$viewwatcliststr.="<tr>";
  $viewwatcliststr.="<td colspan='3'>";
	$viewwatcliststr.="<table width='100%' class='profileAppl'>";
  $viewwatcliststr.= "<tr>";
  $viewwatcliststr.= "<td  valign='top'>";


 // if($viewwatchlistno > 0)
//  {
  	//$viewwatchlist=$viewwatchlist." order by sw.id desc limit $watchlistoffsetlmt,$watchlistnavlmt";
  	foreach(exec_query($viewwatchlist) as $viewwatchlistrow)
  	{
       $stockname=$viewwatchlistrow["stocksymbol"];
       $SecurityName=$viewwatchlistrow["SecurityName"];
	   $field='stockwatchlist';
	   $val=$viewwatchlistrow["stocksymbol"];
	   $stockdisplaylviewname=linkdata($val,$type,$field);
	   $stockstring=$stockname." - ".$SecurityName;
	   $stockstringcount=strlen($stockstring);
	   //echo $stockstringcount;
	   $stocknamecount=strlen($stockname);
	   //echo $stocknamecount;

       $viewwatcliststr.= "<span id='profilewatchlistcontent' class='profilewatchlistcontent'>".$stockdisplaylviewname."</span><span class='profilestockcontent'> - ";
	   if($stockstringcount > $watchlistwordlimit){
	   $substrendlmt=$watchlistwordlimit-($stocknamecount+4);
	   $viewwatcliststr.= substr($SecurityName,0,$substrendlmt)."...";
	   }else{

	   $viewwatcliststr.= $SecurityName;
	   }
	   $viewwatcliststr.= "</span><br/>";
    }
//  }
  $viewwatcliststr.= "</td>";
	$viewwatcliststr.= "</tr>";
	if($viewwatchlistno>$watchlistnavlmt){
	$viewwatcliststr.= "<tr><td class='profilewatchlistcontent'><span id='Profileedit1'><a onClick='viewfulllist(\"".$watchlisturl."\",\"".$profiletype."\",\"".$subid."\");' style='cursor:pointer;'>View Full Watchlist</a></span></td></tr>";
		}
	$viewwatcliststr.= "</table>";
	$viewwatcliststr.= "</td>";
	$viewwatcliststr.= "</tr>";
	}
  $viewwatcliststr.= "</tbody>";
  return $viewwatcliststr;
}

function profileattval($id,$subc_id)
{
	$sqlprofile ="select pav.value as value
from ex_profile_attribute_mapping pav,
ex_profile_attribute pa,
ex_user_profile eup,
subscription s
where pav.attribute_id = pa.id
and pav.profile_id =eup.id
and eup.subscription_id=s.id
and pa.id='".$id."'
and s.id='".$subc_id."'";
    $resprofile = exec_query($sqlprofile);
    $profile_rows = count($resprofile);
    if($profile_rows > 0)
    {
    	foreach(exec_query($sqlprofile) as $rowprofile)
    	{
         	$profileval=$rowprofile["value"];

      	}
    }
	return $profileval;
}


function updateProfile($subid,$viewer_id="")
{
	global $lang;
	$pageName = "profile";
	build_lang($pageName);

	//when watchlist crossbutn clicked then it has some value
	$mode_get=$_GET['watchlistmode'];
	$mode_frnd=$_GET['friendlistmode'];

   $sqlprofile = "select p.id as profile_id from ex_user_profile p,subscription s ";
    $sqlprofile .= "where p.subscription_id = s.id ";
    $sqlprofile .= "and s.id='".$subid."'";
    $resprofile = exec_query($sqlprofile);
    $profile_rows = count($resprofile);
    if($profile_rows > 0)
    {
    	foreach(exec_query($sqlprofile) as $rowprofile)
    	{
         	$Profile_id=$rowprofile["profile_id"];
      	}
    }
    $sqlprofileattr="select id as attr_id, attributeidentifier, attribute from ex_profile_attribute order by id";
    $resultsprofileattr = exec_query($sqlprofileattr);
    $profileattr_rows = count($resultsprofileattr);
					//start here if($_GET['mode']!='edit'){
					if($_GET['mode']!='edit'){
							  echo "<table width='100%' border='0' cellspacing='3' cellpadding='3'>";
							  echo "<tr><td class='profilepage' style='color:#083D70;margin-bottom:5px;' colspan='2'>";
							  echo $lang['profileedit_message'];
							  echo "</td></tr>";
              $attribut="";
              $val="";
              $URL='profile_edit.htm';
              $profiletype="profile";

              if($profileattr_rows > 0)
              {
              	foreach(exec_query($sqlprofileattr) as $rowprofileattr)
              	{
                    $profileattr=$rowprofileattr['attribute'];
					$profileattrident=$rowprofileattr['attributeidentifier'];
                    $profileattrid=$rowprofileattr['attr_id'];
                  	$profileval=profileattval($profileattrid,$subid);
					if($profileattr=='DOB')
					{
						$dobid=$rowprofileattr['attr_id'];
						//$profileval
						$dob=explode("/",$profileval);
										echo "<tr><td class='profilepage'>Birthday: </td>";
						//echo "<td class='profileDesc' nowrap>";
						echo "<td nowrap>";
						echo "<div nowrap align='left'>";
?>						<SELECT id="month" NAME="month" onChange="populate2(profileedit);" class="textcontrolprofilemonth">
						<Option value="">-Month-</Option>
						<Option value="1" <? if($dob['1']=="1"){echo "selected";}?>>Jan</Option>
						<Option value="2" <? if($dob['1']=="2"){echo "selected";}?>>Feb</Option>
						<Option value="3" <? if($dob['1']=="3"){echo "selected";}?>>Mar</Option>
						<Option value="4" <? if($dob['1']=="4"){echo "selected";}?>>Apr</Option>
						<Option value="5" <? if($dob['1']=="5"){echo "selected";}?>>May</Option>
						<Option value="6" <? if($dob['1']=="6"){echo "selected";}?>>Jun</Option>
						<Option value="7" <? if($dob['1']=="7"){echo "selected";}?>>Jul</Option>
						<Option value="8" <? if($dob['1']=="8"){echo "selected";}?>>Aug</Option>
						<Option value="9" <? if($dob['1']=="9"){echo "selected";}?>>Sept</Option>
						<Option value="10" <? if($dob['1']=="10"){echo "selected";}?>>Oct</Option>
						<Option value="11" <? if($dob['1']=="11"){echo "selected";}?>>Nov</Option>
						<Option value="12" <? if($dob['1']=="12"){echo "selected";}?>>Dec</Option>
						</SELECT>
<?
						echo day_options('day',$dob['0'],'class="textcontrolprofileday"','day');
						echo year_options2('year',$dob['2'],'class="textcontrolprofileyear"','1900',date('Y'));
						echo "</div></td></tr>";
					}
					else if($profileattr=='City')
					{
						echo "<tr><td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td></tr>";
						echo "<tr><td style='font-size:13px;font-weight:bold;' colspan='2'>Location: </td></tr>";
						echo "<tr><td class='profilepage' colspan='2'></td></tr>";
						echo "<tr><td class='profilepage'>";
						echo "City:</td><td><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'></td></tr>";
					}
					else if($profileattr=='State')
					{
						echo "<tr><td class='profilepage'> State:</td><td><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");'class='profiletextcontro'></td></tr>";
					}
					else if($profileattr=='Country')
					{
						echo "<tr><td class='profilepage'>Country:</td><td><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'>";
						echo "</td></tr>";
					}
					else if($profileattr=='College')
					{
						echo "<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td></tr>";
						echo "<tr><td style='font-size:13px;font-weight:bold;' colspan='2'>Education: </td></tr>";
						echo "<tr><td class='profilepage' colspan='2'></td></tr>";
						echo "<tr><td class='profilepage'>College: </td><td class='profileDesc'>";
						echo "<input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'></td></tr>";
					}
					else if($profileattr=='Years')
					{
						echo "<tr><td class='profilepage'>Year: </td><td class='profileDesc'><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'>";
						echo "</td></tr>";
						echo "<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td></tr>";
					}
					else if($profileattrident=='Trade')
					{
						echo "<tr><td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td></tr>";
						echo "<tr><td class='profilepage'>".$profileattr."</td>";
						echo "<td class='profileDesc'><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'></td></tr>";
					}
					else
					{
						echo "<tr><td class='profilepage'>".$profileattr.":</td>";
						echo "<td class='profileDesc'><input type='text' value='".$profileval."' id='".$profileattrid."' onBlur='validate_Tag(\"".$profileattrid."\");' class='profiletextcontro'></td></tr>";
					}
                  	$attribut=$attribut."~".$profileattrid;
                }
              }
              echo "<tr><td><input type='hidden' value='".$attribut."' id='attribut'></td>";
			  echo "<tr><td><input type='hidden' value='".$dobid."' id='dobid'></td>";
              echo "<tr><td colspan='2'><img src='".$IMG_SERVER."/images/community_images/submit_additional.gif' onClick='editProfileInfo(\"".$URL."\",\"".$profiletype."\" );' style='cursor: pointer;'>";
			  $closeURL=$URL."?mode=edit";
              echo "&nbsp;<img src='".$IMG_SERVER."/images/community_images/cancel_additional.gif' onClick='requestProfileInfo(\"".$closeURL."\");' style='cursor: pointer;'></td></tr>";
              echo "</table>";
							}//   ends here if($_GET['mode']!='edit'){
	else{
              //update starts here
              $attribute=$_GET["attribute"];//This is for getting dynamical attribute name
              $attval=$_GET["attval"];//This is for getting dynamical values from attribute

              //This is developed for Updating Profile Information
              $profile_attr = explode("~", $attribute);
              $Profile_attrval=explode("~", $attval);

              for($i = 1; $i < count($profile_attr); $i++)
              {
                 $sqlprofileselect = "select * from ex_profile_attribute_mapping ";
                 $sqlprofileselect .= "where attribute_id ='$profile_attr[$i]' ";
                 $sqlprofileselect .= "and profile_id ='$Profile_id'";
                 $resultsprofileselect = exec_query($sqlprofileselect);
				 if($resultsprofileselect){
                 $sqlprofileupdate = "UPDATE ex_profile_attribute_mapping SET value='".htmlentities($Profile_attrval[$i],ENT_QUOTES)."', updated_on='".date('Y-m-d H:i:s')."' ";
                 $sqlprofileupdate .= "where attribute_id ='$profile_attr[$i]' ";
                 $sqlprofileupdate .= "and profile_id ='$Profile_id'";
                 $resultsprofileupdate = exec_query($sqlprofileupdate);
				 }
				 else
				 {
/*					used before  for saving all fields*/
					$profiledata=array(
  		  				profile_id=>$Profile_id,
  						attribute_id=>$profile_attr[$i],
						value=>htmlentities($Profile_attrval[$i],ENT_QUOTES),
						created_on=>date('Y-m-d H:i:s')
  					);
    				insert_query("ex_profile_attribute_mapping",$profiledata);
				 }
              }
              //This is developed for displaying Updated Profile Information
			  if($mode_get=='edit'){
			   getProfileInfo($subid,$viewer_id,$mode_get);
			  }else if($mode_frnd=='edit'){
				  //echo "Inside update function ................$subid";
				  getProfileInfo($subid,$viewer_id,'editfrnd');
			  }else{
			  // while not watclist and $_GET['mode']=edit
			//  function getProfileInfo($subscription_id,$viewer_id,$mode_get='')
				$strdisply=getProfileInfo($subid,$subid);
				echo $strdisply;
			  }
              //update ends here
    }

}



function is_view_allowed($profile_attr,$viewer_id){

}
function linkdata($strdata,$type,$field){
$index=0;
$link='';
$strdata = explode(",", $strdata);
$strcount=count($strdata);
	for($i=0;$i<=$strcount;$i++)
	{
		if($strdata[$i]!=""){
			$link=makelinkis($strdata[$i],$type,$field);
			if($index==0){
				$strtags.=$link;
			}
			else{
				$strtags.=', '.$link;
			}
		}
		$index++;
	}
	return $strtags;
}

function linkdatas($strdata,$type,$field){
$index=0;
$link='';
$strdata = explode(",", $strdata);
$strcount=count($strdata);
	for($i=0;$i<=$strcount;$i++)
	{
		if($strdata[$i]!=""){
			$link=makelinkis($strdata[$i],$type,$field);
			if($index==0){
				$strtags.=$link;
			}
			else{
				$strtags.='<span class=profilesug>,</span> '.$link;
			}
		}
		$index++;
	}
	return $strtags;
}

function makelinkis($keyword,$type,$field="")
{
	global $page_config;
	$taglink='<a href="'.$page_config['exchange_search']['URL'].'?q='.trim($keyword).'&type='.$type;
	if($field!=""){
		$taglink.='&advanced=1&field='.$field;
	}
	$taglink.='">'.$keyword.'</a>';
	return $taglink;
}
//For adding page break
function wrapword($str, $strlimit="")
{
	global $wordwrapdefault;
	$default=$wordwrapdefault;
	$length=strlen($str);
	if($strlimit)
	{
		if($length>$strlimit)
		{
		  //return wordwrap($str, $strlimit, "\n", true);
		  $str = html_entity_decode( $str ); //first decode
		  $out = wordwrap( $str, $strlimit, "\n", true ); //now wordwrap
		  $out = htmlentities($out); //re-encode the entities
		  $out = str_replace( htmlentities("\n"), " ", $out); //put back the break
		  return html_entity_decode($out);
		}
		else
		{
			return $str;
		}
	}
	else
	{
		if($length>$default)
		{
			//return wordwrap($str, $default, "\n", true);
		  $str = html_entity_decode( $str ); //first decode
		  $out = wordwrap( $str, $default, "\n", true ); //now wordwrap
		  $out = htmlentities($out); //re-encode the entities
		  $out = str_replace( htmlentities("\n"), " ", $out); //put back the break
		  return html_entity_decode($out);
		}
		else
		{
			return $str;
		}
	}
}
function getProfileID($subscription_id)
{
	$qryGetProfileID="select EUP.id as profile_id
from subscription S,
ex_user_profile EUP
where S.id=EUP.subscription_id
and S.id='$subscription_id'";
	$resGetProfileID=exec_query($qryGetProfileID);
	foreach($resGetProfileID as $profileIDrow)
	{
	 	$profile_id=$profileIDrow['profile_id'];
	}
	return $profile_id;
}

function formatdateday($day)
{
	if($day=='1')
	{
		$day_display="1st";
	}
	elseif($day=='2')
	{
		$day_display="2nd";
	}
	else if($day=='3')
	{
		$day_display="3rd";
	}
	else if($day=='4')
	{
		$day_display="4th";
	}
	else if($day=='5')
	{
		$day_display="5th";
	}
	else if($day=='6')
	{
		$day_display="6th";
	}
	else if($day=='7')
	{
		$day_display="7th";
	}
	else if($day=='8')
	{
		$day_display="8th";
	}
	elseif($day=='9')
	{
		$day_display="9th";
	}
	else if($day=='10')
	{
		$day_display="10th";
	}
	else if($day=='11')
	{
		$day_display="11th";
	}
	else if($day=='12')
	{
		$day_display="12th";
	}
	else if($day=='13')
	{
		$day_display="13th";
	}
	else if($day=='14')
	{
		$day_display="14th";
	}
	else if($day=='15')
	{
		$day_display="15th";
	}
	else if($day=='16')
	{
		$day_display="16th";
	}
	else if($day=='17')
	{
		$day_display="17th";
	}
	else if($day=='18')
	{
		$day_display="18th";
	}
	else if($day=='19')
	{
		$day_display="19th";
	}
	else if($day=='20')
	{
		$day_display="20th";
	}
	else if($day=='21')
	{
		$day_display="21st";
	}
	else if($day=='22')
	{
		$day_display="22nd";
	}
	else if($day=='23')
	{
		$day_display="23rd";
	}
	else if($day=='24')
	{
		$day_display="24th";
	}
	else if($day=='25')
	{
		$day_display="25th";
	}
	else if($day=='26')
	{
		$day_display="26th";
	}
	else if($day=='27')
	{
		$day_display="27th";
	}
	else if($day=='28')
	{
		$day_display="28th";
	}
	else if($day=='29')
	{
		$day_display="29th";
	}
	else if($day=='30')
	{
		$day_display="30th";
	}
	else if($day=='31')
	{
		$day_display="31st";
	}
	return $day_display;
}
function formatdatemonth($month)
{
	if($month=='1')
	{
		$month_display="January";
	}
	else if($month=='2')
	{
		$month_display="February";
	}
	else if($month=='3')
	{
		$month_display="March";
	}
	else if($month=='4')
	{
		$month_display="April";
	}
	else if($month=='5')
	{
		$month_display="May";
	}
	else if($month=='6')
	{
		$month_display="June";
	}
	else if($month=='7')
	{
		$month_display="July";
	}
	else if($month=='8')
	{
		$month_display="August";
	}
	else if($month=='9')
	{
		$month_display="September";
	}
	else if($month=='10')
	{
		$month_display="October";
	}
	else if($month=='11')
	{
		$month_display="November";
	}
	else if($month=='12')
	{
		$month_display="December";
	}
	return $month_display;

}

function getProfileVisit($profileid, $visit_day, $visit_hour)
{
	$qryGetProfileVisit="select id, visitors from ex_profileview_stat_summary where profile_id='".$profileid."' and day='".$visit_day."' and hour='".$visit_hour."'";
	$resGetProfileVisit=exec_query($qryGetProfileVisit);
	foreach($resGetProfileVisit as $profileVisitrow)
	{
	 	$profile_id=$profileVisitrow['id'];
		$visitors=$profileVisitrow['visitors'];
	}
	return $visitors;
}
function getProfileattrID($fieldname)
{
	$qryGetProfileattrID="select id from ex_profile_attribute where attributeidentifier='".$fieldname."'";
	$resGetProfileattrID=exec_query($qryGetProfileattrID);
	foreach($resGetProfileattrID as $profileattrrow)
	{
	 	$profileattr_id=$profileattrrow['id'];
	}
	return $profileattr_id;
}

function defaultprofile($subscription_id,$visitor="")
{
	global $lang;
	$pagetype="Profile";
	$userprofile=get_user_profile($subscription_id);
	$profileID= new Thread();
	$type=$profileID->get_object_type($pagetype);

	$qryGetProfile="select EPA.attribute as attribute, EPAM.attribute_id as attribute_id, EPAM.value
from subscription S,
ex_user_profile EUP,
ex_profile_attribute_mapping EPAM,
ex_profile_attribute EPA
where S.id=EUP.subscription_id
and EUP.id=EPAM.profile_id
and EPAM.attribute_id=EPA.id
and S.id='$subscription_id'";
	$resGetProfile=exec_query($qryGetProfile);
	if($resGetProfile){

		$qryGetProfileBasic="select EPA.attribute as attribute, EPA.attributeidentifier as attributeidentifier, EPA.section as section, EPAM.attribute_id as attribute_id, EPAM.value
from subscription S,
ex_user_profile EUP,
ex_profile_attribute_mapping EPAM,
ex_profile_attribute EPA
where S.id=EUP.subscription_id
and EUP.id=EPAM.profile_id
and EPAM.attribute_id=EPA.id
and EPA.section='Basic'
and S.id='$subscription_id' order by EPA.id";
	$resGetProfileBasic=exec_query($qryGetProfileBasic);
	//htmlprint_r($resGetProfileBasic);exit;
    foreach($resGetProfileBasic as $profileviewrow)
	{
			  	if($profileviewrow['value']){
					if($profileviewrow['attribute']=='DOB')
					{
						//$type='7';
						$field=$profileviewrow['attribute_id'];
						$val=$profileviewrow['value'];
						$birthdatearr=explode('/',$val);
						$defultprofstr.="<tr><td class='profilepage'>Birthday: </td>";
						//$defultprofstr.="<td class='profileDesc' id='Profileedit1' nowrap><a href='#'>".date("F jS, Y", time(0, 0, 0, $birthdatearr['1'], $birthdatearr['0'], $birthdatearr['2']))."</a></td></tr>";
						$defultprofstr.="<td class='profileDesc' style='color:#062f56;' nowrap>";
						if($birthdatearr['1']!=""){
						$defultprofstr.=formatdatemonth($birthdatearr['1'])."&nbsp;";
						}
						if($birthdatearr['0']!=""){
						$defultprofstr.=formatdateday($birthdatearr['0']);
						}
						if(($birthdatearr['2']!="")&&(($birthdatearr['1']!="")||($birthdatearr['0']!=""))){
						$defultprofstr.=", ";
						}
						if($birthdatearr['2']!=""){
						$defultprofstr.=$birthdatearr['2'];
						}
					}
					else if($profileviewrow['attribute']=='City' || $profileviewrow['attribute']=='State' || $profileviewrow['attribute']=='Country')
					{

						if($showlocation!=1)
						{

							$field=$profileviewrow['attribute_id'];
							$attrid_city=wrapword(getProfileattrID('City'),30);
							$attrid_state=wrapword(getProfileattrID('State'),30);
							$attrid_country=wrapword(getProfileattrID('Country'),30);
							$tagstodisplayCity=linkdata($userprofile['City'],$type,$attrid_city);
							$tagstodisplayState=linkdata($userprofile['State'],$type,$attrid_state);
							$tagstodisplayCountry=linkdata($userprofile['Country'],$type,$attrid_country);
							$defultprofstr.="<tr><td class='profilepage'>Location: </td>";
							$defultprofstr.="<td class='profileDesc' id='Profileedit1'>";
							if($tagstodisplayCity){$defultprofstr.=$tagstodisplayCity;}
							if($tagstodisplayCity && ($tagstodisplayState || $tagstodisplayCountry)){$defultprofstr.=", ";}
							if($tagstodisplayState){$defultprofstr.=$tagstodisplayState;}
							if($tagstodisplayState && $tagstodisplayCountry){$defultprofstr.=", ";}
							if($tagstodisplayCountry){$defultprofstr.=$tagstodisplayCountry;}
							$defultprofstr.="</td></tr>";
						}
						$showlocation=1;
					}
					else if($profileviewrow['attribute']=='College' || $profileviewrow['attribute']=='Years')
					{

						if($showEducation!=1)
						{
							$field=$profileviewrow['attribute_id'];
							$attrid_College=wrapword(getProfileattrID('College'),30);
							$attrid_Years=getProfileattrID('Years');
							$tagstodisplayCollege=linkdata($userprofile['College'],$type,$attrid_College);
							$tagstodisplayYears=linkdata($userprofile['Years'],$type,$attrid_Years);
							$defultprofstr.="<tr><td class='profilepage'>Education: </td>";
							$defultprofstr.="<td class='profileDesc' id='Profileedit1'>";
							if($tagstodisplayCollege){$defultprofstr.=$tagstodisplayCollege;}
							if($tagstodisplayCollege && $tagstodisplayYears){$defultprofstr.=",";}
							if($tagstodisplayYears){$defultprofstr.=$tagstodisplayYears;}
							$defultprofstr.="</td></tr>";
							$defultprofstr.="<tr>";
							$defultprofstr.="<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td>";
							$defultprofstr.="</tr>";
						}
						$showEducation=1;
					}
					else if($profileviewrow['attributeidentifier']=='Website')
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						if(ereg("http://", $profileviewrow['value']))
						{
							$linkval=$profileviewrow['value'];
						}
						else
						{
							$linkval="http://".$profileviewrow['value'];
						}
						//$tagstodisplay=linkdata($val,$type,$field);
						$defultprofstr.="<tr>";
						$defultprofstr.="<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						$defultprofstr.="<td class='profileDesc' width='40%' id='Profileedit1'><a href='".$linkval."' target='_blank'>".$val."</a></td>";
						$defultprofstr.="</tr>";
					}
					else if($profileviewrow['attributeidentifier']=='Trade')
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						$tagstodisplay=linkdata($val,$type,$field);
						$defultprofstr.="<tr>";
						$defultprofstr.="<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td>";
						$defultprofstr.="</tr>";
						$defultprofstr.="<tr>";
						$defultprofstr.="<td class='profilepage'>Trades:</td>";
						$defultprofstr.="<td class='profileDesc' width='40%' id='Profileedit1'>".$tagstodisplay."</td>";
						$defultprofstr.="</tr>";
					}
					else
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						$tagstodisplay=linkdata($val,$type,$field);
						$defultprofstr.="<tr>";
						$defultprofstr.="<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						$defultprofstr.="<td class='profileDesc' id='Profileedit1'>".$tagstodisplay."</td>";
						$defultprofstr.="</tr>";
					}
					}
	}

		$qryGetProfileLifestyle="select EPA.attribute as attribute, EPA.section as section, EPAM.attribute_id as attribute_id, EPAM.value
from subscription S,
ex_user_profile EUP,
ex_profile_attribute_mapping EPAM,
ex_profile_attribute EPA
where S.id=EUP.subscription_id
and EUP.id=EPAM.profile_id
and EPAM.attribute_id=EPA.id
and EPA.section='Lifestyle'
and S.id='$subscription_id' order by EPA.id";

	$resGetProfileLifestyle=exec_query($qryGetProfileLifestyle);
foreach($resGetProfileLifestyle as $profileviewrow)
	{				if($profileviewrow['value']){
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'],30);
						$tagstodisplay=linkdata($val,$type,$field);
						$defultprofstr.="<tr>";
						$defultprofstr.="<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						$defultprofstr.="<td class='profileDesc' width='40%' id='Profileedit1'>".$tagstodisplay."</td>";
						$defultprofstr.="</tr>";
					}
	}


  }
  else
  {
              $defultprofstr.="<tr>";
              $defultprofstr.="<td class='profilepage'><br />";
			  if($visitor=='1'){
			  	//global $lang;
	$pageName = "profile";
	build_lang($pageName);
			  	$defultprofstr.=$lang['profileerrorvisitor_message'];
			  }
			  else
			  {
			   		//global $lang;
	$pageName = "profile";
	build_lang($pageName);
			   	$defultprofstr.=$lang['profileerror_message'];
			  }
			  $defultprofstr.="</td>";
              $defultprofstr.="<td class='profileDesc'><br />&nbsp;</td>";
			  $defultprofstr.="</tr>";

  }//Profile Information Ends here.
	return $defultprofstr;
}


	function showprofil($subscription_id,$viewer_id,$mode_get=''){
				$textstr="<table width='100%' align='center' border='0' cellspacing='0' cellpadding='10'>";
				$textstr.=defaultprofile($subscription_id);
				return $textstr."</table>";
}

function getProfileInfo($subscription_id,$viewer_id,$mode_get='')
{

	if($mode_get==''){
	global $lang;
  $pagetype="Profile";
	$userprofile=get_user_profile($subscription_id);
	$profileID= new Thread();
	$type=$profileID->get_object_type($pagetype);
	echo "<table width='100%' align='center' border='0' cellspacing='10' cellpadding='10'>";
	//This has written for getting Profile Information
														if($subscription_id!=$viewer_id){//start if($subscription_id!=$viewer_id){
															echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='10'>";

			$qryGetPrivacy="select EPA.attribute as attribute, EPA.section as section, EPAM.attribute_id as attribute_id, EPAM.value,EPP.enabled privacy
		from subscription S,
		ex_user_profile EUP,
		ex_profile_attribute_mapping EPAM,
		ex_profile_attribute EPA,
		ex_privacy_attribute EPRA,

		ex_profile_privacy EPP
		where S.id=EUP.subscription_id
		and EUP.id=EPAM.profile_id
		and EPAM.attribute_id=EPA.id
		and EPA.privacy_group=EPRA.id
		and EPP.subscription_id=S.id
		and EPA.privacy_group=EPP.privacy_attribute_id
		and S.id='$subscription_id'";
		$resGetPrivacy=exec_query($qryGetPrivacy);
															if($resGetPrivacy){//start if($resGetPrivacy){
			$qryGetPrivacybasic="select distinct EPA.attribute as attribute,EPA.attributeidentifier as attributeidentifier, EPA.section as section, EPAM.attribute_id as attribute_id, EPAM.value,EPP.enabled privacy
		from subscription S,
		ex_user_profile EUP,
		ex_profile_attribute_mapping EPAM,
		ex_profile_attribute EPA,
		ex_privacy_attribute EPRA,
		ex_profile_privacy EPP
		where S.id=EUP.subscription_id
		and EUP.id=EPAM.profile_id
		and EPAM.attribute_id=EPA.id
		and EPA.privacy_group=EPRA.id
		and EPP.subscription_id=S.id
		and EPA.privacy_group=EPP.privacy_attribute_id
		and EPA.section='Basic'
		and S.id='$subscription_id'";
		$resGetPrivacybasic=exec_query($qryGetPrivacybasic);
			$viewstatus=0;
			$is_friend=is_friend($subscription_id,$viewer_id);
	  		foreach($resGetPrivacybasic as $profileviewrow)
	  		{
				if($profileviewrow['privacy']=='All' || ($profileviewrow['privacy']=='Only Friends' and $is_friend)){
				if($profileviewrow['section']=='Basic'){
					if($profileviewrow['value']){
					if($profileviewrow['attribute']=='DOB')
					{
						//$type='7';
						$field=$profileviewrow['attribute_id'];
						$val=$profileviewrow['value'];
						$birthdatearr=explode('/',$val);
						echo "<tr><td class='profilepage'>Birthday: </td>";
						//echo "<td class='profileDesc' id='Profileedit1' nowrap><a href='#'>".date("F jS, Y", time(0, 0, 0, $birthdatearr['1'], $birthdatearr['0'], $birthdatearr['2']))."</a></td></tr>";
						echo "<td class='profileDesc' style='color:#062f56;' nowrap>";
						if($birthdatearr['1']!=""){
						echo formatdatemonth($birthdatearr['1'])."&nbsp;";
						}
						if($birthdatearr['0']!=""){
						echo formatdateday($birthdatearr['0']);
						}
						if(($birthdatearr['2']!="")&&(($birthdatearr['1']!="")||($birthdatearr['0']!=""))){
						echo ", ";
						}
						if($birthdatearr['2']!=""){
						echo $birthdatearr['2'];
						}

					}
					else if($profileviewrow['attribute']=='City' || $profileviewrow['attribute']=='State' || $profileviewrow['attribute']=='Country')
					{

						if($showlocation!=1)
						{
							$field=$profileviewrow['attribute_id'];
							$attrid_city=getProfileattrID('City');
							$attrid_state=getProfileattrID('State');
							$attrid_country=getProfileattrID('Country');
							$tagstodisplayCity=linkdata(wrapword($userprofile['City'],30),$type,$attrid_city);
							$tagstodisplayState=linkdata(wrapword($userprofile['State'],30),$type,$attrid_state);
							$tagstodisplayCountry=linkdata(wrapword($userprofile['Country'],30),$type,$attrid_country);
							echo "<tr><td class='profilepage'>Location: </td>";
							//echo "<td class='profileDesc' id='Profileedit1'>".wordwrap($tagstodisplayCity, 20, "\n", true).",".wordwrap($tagstodisplayState, 20, "\n", true).",".wordwrap($tagstodisplayCountry, 20, "\n", true)."</td></tr>";
							echo "<td class='profileDesc' id='Profileedit1'>";
							if($tagstodisplayCity){echo $tagstodisplayCity;}
																				if($tagstodisplayState&&($tagstodisplayCity!="")){echo ",".$tagstodisplayState;}else{echo $tagstodisplayState;}
																				if($tagstodisplayCountry&&($tagstodisplayCity!=""||$tagstodisplayState!="")){echo ",".$tagstodisplayCountry;}else{echo $tagstodisplayCountry;}
							echo "</td></tr>";
						}
						$showlocation=1;
					}
					else if($profileviewrow['attribute']=='College' || $profileviewrow['attribute']=='Years')
					{

						if($showEducation!=1)
						{
							$field=$profileviewrow['attribute_id'];
							$attrid_College=getProfileattrID('College');
							$attrid_Years=getProfileattrID('Years');
							$tagstodisplayCollege=linkdata(wrapword($userprofile['College'], 30),$type,$attrid_College);
							$tagstodisplayYears=linkdata(wrapword($userprofile['Years'], 30),$type,$attrid_Years);
							echo "<tr><td class='profilepage'>Education: </td>";
							//echo "<td class='profileDesc' id='Profileedit1'>".wordwrap($tagstodisplayCity, 20, "\n", true).",".wordwrap($tagstodisplayState, 20, "\n", true).",".wordwrap($tagstodisplayCountry, 20, "\n", true)."</td></tr>";
							echo "<td class='profileDesc' id='Profileedit1'>";
							if($tagstodisplayCollege){echo $tagstodisplayCollege;}
																				if($tagstodisplayYears&&($tagstodisplayCollege!="")){echo ",".$tagstodisplayYears;}else{echo $tagstodisplayYears;}
							echo "</td></tr>";
							echo "<tr>";
							echo "<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td>";
							echo "</tr>";
						}
						$showEducation=1;
					}
					else if($profileviewrow['attributeidentifier']=='Website')
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						if(ereg("http://", $profileviewrow['value']))
						{
							$linkval=$profileviewrow['value'];
						}
						else
						{
							$linkval="http://".$profileviewrow['value'];
						}
						//$tagstodisplay=linkdata($val,$type,$field);
						echo "<tr>";
						echo "<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						echo "<td class='profileDesc' width='40%' id='Profileedit1'><a href='".$linkval."' target='_blank'>".$val."</a></td>";
						echo "</tr>";
					}
					else if($profileviewrow['attributeidentifier']=='Trade')
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						$tagstodisplay=linkdata($val,$type,$field);
						echo "<tr>";
						echo "<td class='profilepage' colspan=2><hr size='1px' color='#CCCCCC' /></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td class='profilepage'>Trades:</td>";
						echo "<td class='profileDesc' id='Profileedit1'>".$tagstodisplay."</td>";
						echo "</tr>";
					}
					else
					{
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'], 30);
						$tagstodisplay=linkdata($val,$type,$field);
						echo "<tr>";
						echo "<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						echo "<td class='profileDesc' id='Profileedit1'>".$tagstodisplay."</td>";
						echo "</tr>";
					}
					}
				}
		 		}//added for error handling
		 		else
		 		{
            echo "<tr>";
            echo "<td class='profilepage' colspan=2><br />";
            if($viewstatus!=1){
            echo $lang['profileerrorauth_message'];
            }
            $viewstatus=1;
            echo "</td></tr>";
        }
	  		}

//Added for lifestyle
			$qryGetPrivacyLifestyle="select EPA.attribute as attribute, EPA.section as section, EPAM.attribute_id as attribute_id, EPAM.value,EPP.enabled privacy
		from subscription S,
		ex_user_profile EUP,
		ex_profile_attribute_mapping EPAM,
		ex_profile_attribute EPA,
		ex_privacy_attribute EPRA,
		ex_profile_privacy EPP
		where S.id=EUP.subscription_id
		and EUP.id=EPAM.profile_id
		and EPAM.attribute_id=EPA.id
		and EPA.privacy_group=EPRA.id
		and EPP.subscription_id=S.id
		and EPA.privacy_group=EPP.privacy_attribute_id
		and EPA.section='Lifestyle'
		and S.id='$subscription_id'";
		$resGetPrivacyLifestyle=exec_query($qryGetPrivacyLifestyle);
	  		$is_friend=is_friend($subscription_id,$viewer_id);
			foreach($resGetPrivacyLifestyle as $profileviewrow)
	  		{
		 		if($profileviewrow['privacy']=='All' || ($profileviewrow['privacy']=='Only Friends' and $is_friend)){
				if($profileviewrow['section']=='Lifestyle'){
					if($profileviewrow['value']){
						$field=$profileviewrow['attribute_id'];
						$val=wrapword($profileviewrow['value'],30);
						$tagstodisplay=linkdata($val,$type,$field);
						echo "<tr>";
						echo "<td class='profilepage'>".$profileviewrow['attribute'].":</td>";
						echo "<td class='profileDesc' id='Profileedit1'>".$tagstodisplay."</td>";
						echo "</tr>";
					}
				}

		 		}
		 		else
		 		{
            echo "<tr>";
            echo "<td class='profilepage' colspan=2><br />";
            if($viewstatus!=1){
            echo $lang['profileerrorauth_message'];
            }
            $viewstatus=1;
																echo "</td></tr>";
        }
	  		}
															}// end if($resGetPrivacy){
	  else
	  {
	  		defaultprofile($subscription_id,1);
	  }//Profile Information Ends here.
															echo "</table>";
														}//end  if($subscription_id!=$viewer_id){
  else
  {
		$def="<table width='100%' align='center' border='0' cellspacing='0' cellpadding='10' >";
		$def.=defaultprofile($subscription_id);
		return $def."</table>";
  }
															}//end of if($mode_get=='')
	else if($mode_get=='edit'){
		$viewwatcliststr='<table width="100%" border="0" cellspacing="3" cellpadding="1">';
		$viewwatcliststr.=viewwatchlist($subscription_id,$viewer_id);
		$viewwatcliststr.='</table>';
		$finalstr='';
		$leftcontent=showprofil($subscription_id,$viewer_id,$mode_get);
		$finalstr="~start~".$viewwatcliststr."~end~".$leftcontent;
		echo $finalstr;
	}//else if($mode_get=='edit'){
	else if($mode_get=='editfrnd'){
		$viewfrndliststr='<table border="0" width="100%" cellspacing="3" cellpadding="1">';
		$viewfrndliststr.=viewfriend($subscription_id,$viewer_id);
		$viewfrndliststr.='</table>';
		$finalstr1='';
		$leftcontent1=showprofil($subscription_id,$viewer_id);
		$finalstr1="~start~".$viewfrndliststr."~end~".$leftcontent1;
		echo $finalstr1;
	}//else if($mode_get=='edit'){

}
function updateFriend($subid,$offset="",$limit="",$viewfull="",$deleted_record="",$recordtotal="",$friendname="")
{
   	global $page_config,$lang;
	$pagetype="Profile";
	$profileID= new Thread();
	$type=$profileID->get_object_type($pagetype);
  if($_GET['mode']=='delete')
  {

	  $deletefriend = "Delete from ex_user_friends ";
      $deletefriend .= "where friend_subscription_id = '".$_GET['sid']."'";
      $deletefriend .= "and subscription_id='".$subid."'";

      $resdeletefriend = exec_query($deletefriend);

	  $deletefriend_rev = "Delete from ex_user_friends ";
      $deletefriend_rev .= "where friend_subscription_id = '".$subid."'";
      $deletefriend_rev .= "and subscription_id='".$_GET['sid']."'";

      $resdeletefriend_rev = exec_query($deletefriend_rev);

  		$pageName = "profile";
		build_lang($pageName);
		echo "<table class='profileAppl' style='margin-top:5px;margin-left:4px;' width='100%' border='0' cellspacing='15' cellpadding='0'>";
		echo "<tr>";
		echo "<td style='font: Arial, Helvetica, sans-serif;color:red;font-size:13px;'>";
		echo $friendname." ".$lang['message_frienddelete'];
		echo "</td></tr></table>";


	  if($offset!='0')
	  {
		  $deleted_record=$deleted_record+1;
		  if($deleted_record==$recordtotal)
		  {
			$offset=$offset-$limit;
			$recordtotal="";
		  }
		  else
		  {
			$offset=$offset;
		  }
	 }
  }

  $friendurl="friend_edit.htm";
  $viewfriend = "select f.friend_subscription_id as friend_subscription_id, s.fname as fname, s.lname as lname from ex_user_friends f,subscription s ";
  $viewfriend .= "where f.friend_subscription_id = s.id ";
  $viewfriend .= "and f.subscription_id='".$subid."'";
  $resviewfriend = exec_query($viewfriend);
  $viewfriendno = count($resviewfriend);
  echo "<table class='profileAppl' style='margin-top:38px;margin-left:4px;' width='100%' border='0' cellspacing='5' cellpadding='0'>";
  $closeURL="profile_edit.htm?mode=edit&friendlistmode=edit&userid=".$subid;
  echo "<tr><td colspan=2 valign='top'><img src='".$IMG_SERVER."/images/community_images/btn_cross.gif' onClick='refreshdiv(\"".$closeURL."\",\"viewfriend\");' align='right' style='cursor: pointer;'></td></tr>";

  if($viewfriendno > 0)
  {
  	$viewfriend=$viewfriend." limit $offset,$limit";
	$resultviewfriend = exec_query($viewfriend);
  	$totalfriendno = count($resultviewfriend);
	if($recordtotal)
	{
		$recordtotal=$recordtotal;
	}
	else
	{
		$recordtotal=$totalfriendno;
	}

	$frienddeleteurl=$friendurl."?Pcount=$offset&recordtotal=$recordtotal&deleted_record=$deleted_record&limit=2";

  	foreach(exec_query($viewfriend) as $viewfriendrow)
  	{

	   $field='';
       $firstname=$viewfriendrow["fname"];
       $lastname=$viewfriendrow["lname"];
       $friend_subscription_id=$viewfriendrow["friend_subscription_id"];
       $val=ucwords(strtolower($firstname." ".$lastname));
	   $friendslink=$page_config['profile']['URL']."?userid=".$friend_subscription_id;
   echo "<tr>";
	     echo "<td class='profilewatchlistcontent' style='vertical-align:middle;'><span id='Profileedit2'><a href='".$friendslink."'>".$val."</a></span></td>";
		if($viewfull!='yes')
		{
	     echo "<td class='profileDesc' style='vertical-align:middle;'><span id='Profileedit2'><a style='cursor: pointer;' onClick='deleteProfileInfo(\"".$frienddeleteurl."\",\"".$friend_subscription_id."\",\"".$val."\");'>Remove</a></span></td>";
		 }
	     echo "</tr>";
    }
	$paginationwatchlist_url="profile_edit.htm?type=friend&userid=".$subid."&viewfull=".$viewfull;
		echo "<tr>";
	 echo "<td class='profileDesc' colspan=2 style='text-align:center;'>";
	 echo $profileID->make_ajax_pagination('txtHint',$paginationwatchlist_url,$offset,$limit,$viewfriendno,'friend');
	echo "</td></tr>";

  }
  else
  {
       echo "<tr>";
	     echo "<td class='profileDesc'>No friends on list!</td>";
	     echo "</tr>";
	     $closeURL="profile_edit.htm?mode=edit";
       //echo "<tr><td class='profileDesc'>&nbsp;<img src='".$IMG_SERVER."/images/community_images/cancel_additional.gif' onClick='requestProfileInfo(\"".$closeURL."\");' style='cursor:pointer;'></td></tr>";

  }
	echo "</table>";
}

function getwatchlistInfo($subid,$offset="",$limit="",$viewfull="",$deleted_record="",$recordtotal=""){
  $pagetype="Profile";
  $profileID= new Thread();
  $type=$profileID->get_object_type($pagetype);
  $watchlisturl="watchlist_edit.htm";
  $viewwatchlist = "select sw.id as stockid, sw.stock_id as stock_id, s.stocksymbol as stocksymbol, s.exchange as exchange, s.SecurityName as SecurityName from ex_user_stockwatchlist sw,ex_stock s ";
  $viewwatchlist .= "where sw.stock_id = s.id ";
  $viewwatchlist .= "and sw.subscription_id='".$subid."'";

  $resviewwatchlist = exec_query($viewwatchlist);
  $viewwatchlistno = count($resviewwatchlist);
  echo "<table class='profileAppl'";
  if(($_GET['mode']=='save')||($_GET['mode']=='delete')){
    echo "style='margin-left:4px;margin-right:0px;margin-bottom:10px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
  }
  else
  {
	echo "style='margin-top:38px; margin-left:4px;margin-right:0px;margin-bottom:10px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
  }
  $closeURL="profile_edit.htm?mode=edit&watchlistmode=edit&userid=".$subid;
  echo "<tr><td colspan=4 valign='top'><img src='".$IMG_SERVER."/images/community_images/btn_cross.gif' onClick='refreshdiv(\"".$closeURL."\",\"watchlistdiv\");' align='right' style='cursor: pointer;'></td></tr>";
  if($viewwatchlistno > 0)
  {
  	$viewwatchlist=$viewwatchlist." limit $offset,$limit";
	//$watchlistdeleteurl=$watchlisturl."?Pcount=$_GET[Pcount]&deleted_record=$deleted_record&limit=2";
	//$watchlisturl.="?Pcount=$_GET[Pcount]&limit=2";
	//echo $viewwatchlist;
	$showwatchlist = exec_query($viewwatchlist);
	$showwatchlistno = count($showwatchlist);
	if($recordtotal)
	{
		$recordtotal=$recordtotal;
	}
	else
	{
		$recordtotal=$showwatchlistno;
	}
	$watchlistdeleteurl=$watchlisturl."?Pcount=$offset&recordtotal=$recordtotal&deleted_record=$deleted_record&limit=2";
	foreach(exec_query($viewwatchlist) as $viewwatchlistrow)
  	{
       $stockname=$viewwatchlistrow["stocksymbol"];
       $SecurityName=$viewwatchlistrow["SecurityName"];
       $stock_id=$viewwatchlistrow["stock_id"];
       $stockid=$viewwatchlistrow["stockid"];
	   					$field='stockwatchlist';
						$val=$viewwatchlistrow["stocksymbol"];
						$stockdisplaylname=linkdata($val,$type,$field);
       	echo "<tr>";
	     echo "<td class='profilewatchlistcontent' width='30%' style='vertical-align:middle;'><span id='Profileedit2'>".$stockdisplaylname."</span></td>";
	     echo "<td class='profilestockcontent' width='60%' style='vertical-align:middle;'>".wordwrap($SecurityName, 18, "\n", true)."</td>";
		if($viewfull!='yes'){
	     //echo "<td class='profileDesc' width='10%' style='vertical-align:middle;'><span id='Profileedit2'><a style='cursor: pointer;' onClick='deleteProfileInfo(\"".$watchlistdeleteurl."\",".$stockid.");'>Remove</a></span></td>";
	     echo "<td class='profileDesc' width='10%' style='vertical-align:middle;'><span id='Profileedit2'><a style='cursor: pointer;' onClick='deleteProfileInfo(\"".$watchlistdeleteurl."\",".$stockid.",\"".$stockname."\");'>Remove</a></span></td>";

		 }
	     echo "</tr>";
    }
	//$watchlisturl.="?Pcount=$_GET[Pcount]&limit=2";
	$watchlisturl.="?Pcount=$offset&limit=2";
	/*
	Hided for hiding add button functionality
	if($viewfull!='yes'){
	echo "<tr>";
		 echo "<td class='profileDesc' colspan=2><img src='".$IMG_SERVER."/images/community_images/addstock.gif' width='95' height='20' style='padding-top:10px;cursor: pointer;' onClick='addProfileInfo(\"".$watchlisturl."\",\"".$stock_id."\" );'/>";
	  echo "</tr>";
	}*/
	$pagination_url="profile_edit.htm?type=watchlist&userid=".$subid."&viewfull=".$viewfull;
	echo "<tr>";
	 echo "<td class='profileDesc' colspan=2 style='text-align:center;'>";
	 echo $profileID->make_ajax_pagination('txtHint',$pagination_url,$offset,$limit,$viewwatchlistno,'stock');
	echo "</td></tr>";
  }
  else
  {
       //$closeURL="profile_edit.htm?mode=edit";
	   $watchlisturl.="?Pcount=$offset&limit=2";
	   echo "<tr>";
	     echo "<td class='profileDesc'>No Watchlist!</td>";
	     echo "</tr>";
       /*echo "<tr>";
	     echo "<td class='profileDesc' colspan=2><img src='".$IMG_SERVER."/images/community_images/addstock.gif' width='95' height='20' style='padding-top:10px;cursor: pointer;' onClick='addProfileInfo(\"".$watchlisturl."\",\"".$subid."\" );'/>";*/
  }
	echo "</table>";

}
function updateWatchlist($subid,$offset="",$limit="",$viewfull="",$deleted_record="",$recordtotal="",$stockname="")
{
	global $lang;
  if($_GET['mode']=='save')
  {
    $stock=is_stock($_GET['val']);
	if(!$stock){
    echo "<table class='profileAppl' style='margin-top:38px;margin-left:4px;margin-right:0px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
	echo "<tr>";
	echo "<td style='font: Arial, Helvetica, sans-serif;color:red;font-size:13px;'>This is not valid stock!</td></tr></table>";
	}
	else
	{
		$validid=is_repeatstock($stock['id'],$subid);
		if($validid){
		$stockdata=array(
			  subscription_id=>$subid,
				stock_id=>$stock['id']
			);
		$stockdata=insert_query("ex_user_stockwatchlist",$stockdata);

		echo "<table class='profileAppl' style='margin-top:38px; margin-left:4px;margin-right:0px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
		echo "<tr>";
		echo "<td style='font: Arial, Helvetica, sans-serif;color:red;font-size:13px;'>Stock Successfully Added!</td></tr></table>";
		}
		else
		{
		echo "<table class='profileAppl' style='margin-top:38px; margin-left:4px;margin-right:0px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
		echo "<tr>";
		echo "<td style='font: Arial, Helvetica, sans-serif;color:red;font-size:13px;'>Oops! Stock already exists!</td></tr></table>";
		}
	}

  }
  elseif($_GET['mode']=='delete')
  {
      $deletewatchlist = "Delete from ex_user_stockwatchlist ";
      $deletewatchlist .= "where id = '".$_GET['sid']."'";
      $deletewatchlist .= "and subscription_id='".$subid."'";

      $resdeletewatchlist = exec_query($deletewatchlist);
  		$pageName = "profile";
		build_lang($pageName);
		echo "<table class='profileAppl' style='margin-top:38px; margin-left:4px;margin-right:0px;' width='300' border='0' cellspacing='5' cellpadding='0'>";
		echo "<tr>";
		echo "<td style='font: Arial, Helvetica, sans-serif;color:red;font-size:13px;'>";
		echo $stockname." ".$lang['message_stockdelete'];
		echo "</td></tr></table>";
	  if($offset!='0')
	  {
		  $deleted_record=$deleted_record+1;
		  if($deleted_record==$recordtotal)
		  {
			$offset=$offset-$limit;
			$recordtotal="";
		  }
		  else
		  {
			$offset=$offset;
		  }
	 }

  }

  if($viewfull!='yes'){
/*  elseif($_GET['mode']=='add')
  {*/
    getwatchlistInfo($subid,$offset,$limit);
	//getwatchlistInfo($subid,$offset,$limit,$viewfull,$deleted_record,$recordtotal);
	$URL="watchlist_edit.htm?Pcount=$offset&limit=$limit";
    $profiletype='watchlist';
    echo "<table class='profileAppl' style='margin-top:1px;margin-left:4px;' width='300' border='0' cellspacing='15' cellpadding='0'>";
    echo "<tr><td class='profilepage'>Stock Name</td>";
	//echo "<td><input type='text' value='' id='stocks' onblur='autosuggest();'></td></tr>";
    echo "<td>";
	input_text('stocks','', 0, 255);
	echo "</td></tr>";
     //echo "<td class='profilepage'>".input_text('stocks',$stocktag[tag],0,255,'style=width:45%')."</td></tr>";
    //$profile_show.= "<td class='profileDesc'><select id='stockid' selected>";

    //if($viewstocklistno > 0)
    //{
     //	foreach(exec_query($viewstocklist) as $rowprofileattr)
     //	{
    //    $profileattr=$rowprofileattr['stock'];
        //$profileattrid=$rowprofileattr['attr_id'];
       	//$profileval=$rowprofileattr['value'];

      //  $profile_show.= "<option value='\"".$profileattr."\"'>".$profileattr."</option>";

        //<input type='text' value='' id='stockid' name='stockid'></td></tr>";
       // $attribut=$attribut."~".$profileattrid;
     // }
   // }
    //$profile_show.= "</select>";
    //$profile_show.= "<tr><td><input type='hidden' value='".$viewstocklistno."' id='attribut12'></td>";


    //echo "<tr><td><input type='button' value='Submit' onClick='saveProfileInfo(\"".$URL."\",\"".$profiletype."\" );'></td>";

    echo "<tr><td colspan=2><img src='".$IMG_SERVER."/images/community_images/add.gif' onClick='saveProfileInfo(\"".$URL."\",\"".$profiletype."\" );' style='cursor: pointer;'>";

	$closeURL=$URL."&mode=edit";
			  //$profile_show.= "&nbsp;<input type='button' value='Close' onClick='requestProfileInfo(\"".$closeURL."\");'></td>";
    echo "&nbsp;<img src='".$IMG_SERVER."/images/community_images/cancel_additional.gif' onClick='requestProfileInfo(\"".$closeURL."\");' style='cursor: pointer;'></td></tr>";

    //$profile_show.= "<td>&nbsp;</td></tr>";

    echo "</table>";

    //echo $profile_show;
    //hided this exit for viewing stocklist
    exit;
    //updateWatchlist($subid);


  }
		getwatchlistInfo($subid,$offset,$limit,$viewfull,$deleted_record,$recordtotal);

}
function is_repeatstock($stockid,$profileid)
{
  $validwatchlistid = "select id from ex_user_stockwatchlist where subscription_id='".$profileid."' and stock_id='".$stockid."'";
  $resvalidwatchlistid = exec_query($validwatchlistid);
  $validwatchlistidno = count($resvalidwatchlistid);
  if(isset($resvalidwatchlistid)){
  	if($validwatchlistidno > 0)
  	{
		return false;
  	}
	else
	{
		return true;
	}
  }
}

function getPrivacy($subid)
{
$sqluserprivacy = "select id,subscription_id,privacy_attribute_id,enabled from ex_profile_privacy where subscription_id='".$subid."'";
$resultsuserprivacy = exec_query($sqluserprivacy);
$userprivacy_rows = count($resultsuserprivacy);
if($userprivacy_rows > 0)
{

?>
  <table width="667" cellspacing="0" cellpadding="0" border="0" align="left">
            <tr>
              <td colspan="4" class="formContentHeading" align="left">Defaults</td>
            </tr>
         <tr>
              <td class="formContent" style="text-align:left; padding-left:5px;">Make all profile information visible only to:</td>
              <td colspan="3" style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="default" name="default" onchange="checkdefault();">
					   <option value="" ></option>
					   <option value="All" >All</option>
					   <option value="Only Friends">Only Friends</option>
					   <option value="None">None</option>
			   		</select>

			  </td>
            </tr>
            <tr>
              <td colspan="4"><hr size="1px" color="#CCCCCC" /></td>
              </tr>
			  <tr>
              <td colspan="4" class="formContentHeading">Profile content </td>

            </tr>
<?
	$sqlprivacyattr = "select id, attribute_name from ex_privacy_attribute where section='Profile Content'";
    $resultsprivacyattr = exec_query($sqlprivacyattr);
    $privacyattr_rows = count($resultsprivacyattr);
	if($privacyattr_rows > 0)
    {
   		foreach(exec_query($sqlprivacyattr) as $rowprivacyattr)
        {

					$privacyattr=$rowprivacyattr['attribute_name'];
                    $privacyattr_id=$rowprivacyattr['id'];
					$enabledcontent=getprivacysettingInfo($subid, $privacyattr_id);
?>
            <tr>
                 <td width="50%" style="text-align:left; padding-left:5px;" class="formContent"><?echo $privacyattr;?></td>
              <td colspan="3" style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $privacyattr_id;?>" name="<?echo $privacyattr_id;?>" onchange="checkprofile();">
			<option value="All" <?if($enabledcontent=="All"){echo "selected";}?>>All</option><option value="Only Friends" <?if($enabledcontent=="Only Friends"){echo "selected";}?>>Only Friends</option>
			<option value="None" <?if($enabledcontent=="None"){echo "selected";}?>>None</option>
			   		</select>
		  	  </td>

            </tr>
<? 		}
?>			<tr>
              <td colspan="4"><hr size="1px" color="#CCCCCC" /></td>
            </tr>
<?  }
?>
		<tr>
              <td colspan="4" class="formContentHeading">Contact settings </td>
            </tr>
			<tr>              </tr>
<?
	$sqlprivacyattr = "select id, attribute_name from ex_privacy_attribute where section='Contact Settings'";
    $resultsprivacyattr = exec_query($sqlprivacyattr);
    $privacyattr_rows = count($resultsprivacyattr);
	if($privacyattr_rows > 0)
    {
   		foreach(exec_query($sqlprivacyattr) as $rowprivacyattr)
        {

					$privacyattr=$rowprivacyattr['attribute_name'];
                    $privacyattrbid=$rowprivacyattr['id'];
					$enabledcontact=getprivacysettingInfo($subid, $privacyattrbid);
?>
			<tr>
              <td style="text-align:left; padding-left:5px;" class="formContent"><?echo $privacyattr;?></td>
              <td colspan="2"  style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $privacyattrbid;?>" name="<?echo $privacyattrbid;?>" onchange="checkprofile();">
			<option value="All" <?if($enabledcontact=="All"){echo "selected";}?>>All</option><option value="Only Friends" <?if($enabledcontact=="Only Friends"){echo "selected";}?>>Only Friends</option>
			<option value="None" <?if($enabledcontact=="None"){echo "selected";}?>>None</option>
			   		</select>
              </td>
            </tr>

<? 		}
     }
?>
			<tr>
              <td width="150"><input type="hidden" name="id" value="<?echo $subid;?>" /></td>
              <td width="150"><input type="hidden" name="mode" value="save" /></td>
            </tr>
			<tr>
              <td>&nbsp;</td>
              <td colspan="2" style="padding-left:21px; text-align:left;" width="15%"><input type="image" style="cursor:pointer;" src="<?=$IMG_SERVER?>/images/community_images/save_button.gif" align="left" onclick="javascript:document.form1.submit();" /></td>
      		  <td width="25%">&nbsp;</td>
            </tr>
			<tr>
              <td colspan="4">&nbsp;</td>
            </tr>
          </table>
<?
}
else
{
?>
<table width="667" cellspacing="0" cellpadding="0" border="0" align="left">
            <tr>
              <td colspan="4" class="formContentHeading">Defaults</td>
            </tr>
          <tr>
              <td class="formContent" style="text-align:left; padding-left:5px;">Make all profile information visible only to:</td>
              <td colspan="3" style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="default" name="default" onchange="checkdefault();">
					   <option value="" ></option>
					   <option value="All">All</option>
					   <option value="Only Friends">Only Friends</option>
					   <option value="None">None</option>
			   		</select>
			  </td>
            </tr>
            <tr>
              <td colspan="4"><hr size="1px" color="#CCCCCC" /></td>
			  <tr>
   				<td colspan="4" class="formContentHeading">Profile content </td>
            </tr>
<?
  	$sqlprivacyattr = "select id,attribute_name,attribute from ex_privacy_attribute where section='Profile Content'";
    $resultsprivacyattr = exec_query($sqlprivacyattr);
    $privacyattr_rows = count($resultsprivacyattr);
	if($privacyattr_rows > 0)
    {
   		foreach(exec_query($sqlprivacyattr) as $rowprivacyattr)
        {
                    $privacyattr=$rowprivacyattr['attribute_name'];
                    $privacyattrid=$rowprivacyattr['id'];
					$attribute=$rowprivacyattr['attribute'];
?>
            <tr>
              <td width="50%" style="text-align:left; padding-left:5px;" class="formContent"><?echo $privacyattr;?></td>
              <td colspan="3" style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $privacyattrid;?>" name="<?echo $privacyattrid;?>">
					   <option value="All" <? if($ColumnPrivacySetting[$attribute]=="All"){echo "selected";} ?>>All</option>
					   <option value="Only Friends" <? if($ColumnPrivacySetting[$attribute]=="Only Friends"){echo "selected";} ?>>Only Friends</option>
					   <option value="None" <? if($ColumnPrivacySetting[$attribute]=="None"){echo "selected";} ?>>None</option>
			   		</select>
		  	  </td>
            </tr>
<?      } ?>
			<tr>
              <td colspan="4"><hr size="1px" color="#CCCCCC" /></td>
            </tr>
<?      }
?>
		<tr>
          <td colspan="4" class="formContentHeading">Contact settings </td>
            </tr>
			<tr>              </tr>
<?
  	$sqlprivacyattr = "select id,attribute_name,attribute from ex_privacy_attribute where section='Contact Settings'";
    $resultsprivacyattr = exec_query($sqlprivacyattr);
    $privacyattr_rows = count($resultsprivacyattr);
	if($privacyattr_rows > 0)
    {
   		foreach(exec_query($sqlprivacyattr) as $rowprivacyattr)
        {
                    $privacyattr=$rowprivacyattr['attribute_name'];
                    $privacyattrid=$rowprivacyattr['id'];
					$attribute_cont=$rowprivacyattr['attribute'];
?>


			<tr>
              <td style="text-align:left; padding-left:5px;" class="formContent"><?echo $privacyattr;?></td>
              <td colspan="2"  style="padding-left:20px; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $privacyattrid;?>" name="<?echo $privacyattrid;?>">
					   <option value="All" <? if($ColumnPrivacySetting[$attribute_cont]=="All"){echo "selected";} ?>>All</option>
					   <option value="Only Friends" <? if($ColumnPrivacySetting[$attribute_cont]=="Only Friends"){echo "selected";} ?>>Only Friends</option>
					   <option value="None" <? if($ColumnPrivacySetting[$attribute_cont]=="None"){echo "selected";} ?>>None</option>
			   		</select>
              </td>
            </tr>
<? 	    }
     }
?>
			<tr>
              <td width="150"><input type="hidden" name="id" value="<?echo $subid;?>" /></td>
              <td width="150"><input type="hidden" name="mode" value="save" /></td>
            </tr>
			<tr>
              <td>&nbsp;</td>
              <td colspan="2" style="padding-left:21px; text-align:left;" width="15%"><!--<input type='submit' value='Submit'>--><img src="<?=$IMG_SERVER?>/images/community_images/save_button.gif" align="left" onclick="javascript:document.form1.submit();" /></td>
      		  <td width="25%">&nbsp;</td>
            </tr>
			<tr>
              <td colspan="4">&nbsp;</td>
            </tr>
          </table>
<?
}

}

function getprivacysettingInfo($subid, $att_id)
{
	$sqluserprivacy = "select enabled from ex_profile_privacy where subscription_id='".$subid."' and privacy_attribute_id='".$att_id."'";
	$resultsuserprivacy = exec_query($sqluserprivacy);
	if($resultsuserprivacy)
	{
   		foreach(exec_query($sqluserprivacy) as $rowprivacyattr)
        {
            $privacyattr_enabled=$rowprivacyattr['enabled'];
			return $privacyattr_enabled;
		}
	}
	else
	{
	 	return false;
	}

}
function getemailsettingInfo($subid, $attr_id)
{
	$sqlemailuserprivacy = "select alert from ex_user_email_settings where subscription_id='".$subid."' and email_id='".$attr_id."'";
	$resultsemailuserprivacy = exec_query($sqlemailuserprivacy);
	if($resultsemailuserprivacy)
	{
   		foreach(exec_query($sqlemailuserprivacy) as $rowemailprivacyattr)
        {
            $emailprivacyattr_alert=$rowemailprivacyattr['alert'];
			return $emailprivacyattr_alert;
		}
	}
	else
	{
	 	return false;
	}

}
function getemailsetting($subid)
{
$sqlemailuserprivacy = "select id,subscription_id,email_id,alert from ex_user_email_settings where subscription_id='".$subid."'";
$resultsemailuserprivacy = exec_query($sqlemailuserprivacy);
$emailuserprivacy_rows = count($resultsemailuserprivacy);
if($emailuserprivacy_rows > 0)
{
?>
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
            <td  colspan="4" class="formContentHeading" style="padding-left:8px; padding-top:10px;">Exchange Defaults</td>
            </tr>
         <tr>
        <td colspan="4" style="padding-left:8px; padding-top:5px; padding-bottom:5px;">
			 Make all email information :
					<select id="default" name="default" onchange="emailprivacysetting();">
					   <option value="" ></option>
					   <option value="On" >On</option>
					   <option value="Off">Off</option>
			   		</select>
			  </td>
             </tr>
            <tr>
              <td colspan="4" colspan="3"><hr size="1px" color="#CCCCCC" /></td>
             </tr>
            <tr>
              <!--<td width="150">&nbsp;</td>-->
              <td colspan="4" class="formContent" style="padding-left:8px;">Notify me via email when I get...</td>
            </tr>
<?
$sqlemailprivacyattr = "select id, description,event from ex_email_template where privacy='1'";
    $resultsemailprivacyattr = exec_query($sqlemailprivacyattr);
    $emailprivacyattr_rows = count($resultsemailprivacyattr);
   		foreach(exec_query($sqlemailprivacyattr) as $rowemailprivacyattr)
        {
			$emailprivacyattr=$rowemailprivacyattr['description'];
            $emailprivacyattr_id=$rowemailprivacyattr['id'];
			$alert=getemailsettingInfo($subid, $emailprivacyattr_id);
			$event_privacy=$rowemailprivacyattr['event'];
?>
            <tr>
              <td style="text-align:left; padding-left:8px;" class="formContent"><?echo $emailprivacyattr;?></td>
              <td  colspan="3" style="padding-left:25; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $emailprivacyattr_id;?>" name="<?echo $emailprivacyattr_id;?>" onchange="checkprofile();">
			<option value="On"
			<? if($alert)
			   {
				 if($alert=="On")
				 {
				   echo "selected";
				 }
			   }
			   else if($ColumnEmailSetting[$event_privacy]=="On")
			   {
			     echo "selected";
			   }
			?>>On</option>
			<option value="Off"
			<? if($alert)
			   {
				 if($alert=="Off")
				 {
				   echo "selected";
				 }
			   }
			   else if($ColumnEmailSetting[$event_privacy]=="Off")
			   {
			     echo "selected";
			   }

			?>>Off</option>
			   		</select>
		  	  </td>
            </tr>
			<tr>
           <td colspan="4">&nbsp;</td>
            </tr>

<?     }
?>
			<tr>
              <td width="150"><input type="hidden" name="id" value="<?echo $subid;?>" /></td>
              <td width="150"><input type="hidden" name="mode" value="save" /></td>
            </tr>
			<tr>
              <td width>&nbsp;</td>
              <td width="30%">&nbsp;</td>
            </tr>
			<tr>
              <!--<td colspan="4">&nbsp;</td-->
            </tr>
          </table>
<?
}
else
{
?>
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td colspan="4" class="formContentHeading" style="padding-left:8px; padding-top:10px;">Defaults</td>
            </tr>
         <tr>
              <td colspan="4" style="padding-left:8px; padding-top:5px; padding-bottom:5px;">Make all email information :
					<select id="default" name="default" onchange="emailprivacysetting();">
						<option value="" ></option>
             			<option value="On" >On</option>
					   <option value="Off">Off</option>
			   		</select>
			  </td>
            </tr>
            <tr>
              <td colspan="4" colspan="3"><hr size="1px" color="#CCCCCC" /></td>
             </tr>
            <tr>
              <td colspan="4" class="formContent" style="padding-left:8px;">Email Settings: </td>
            </tr>
<?
	  $sqlemailprivacyattr = "select id,description,event from ex_email_template where privacy='1'";
    $resultsemailprivacyattr = exec_query($sqlemailprivacyattr);
    $emailprivacyattr_rows = count($resultsemailprivacyattr);
	if($emailprivacyattr_rows > 0)
    {
   		foreach(exec_query($sqlemailprivacyattr) as $rowemailprivacyattr)
        {

					$emailprivacyattr=$rowemailprivacyattr['description'];
                    $emailprivacyattr_id=$rowemailprivacyattr['id'];
					$event=$rowemailprivacyattr['event'];
?>
            <tr>
               <td style="text-align:left; padding-left:8px;" class="formContent"><?echo $emailprivacyattr;?></td>
				<td  colspan="3" style="padding-left:25; padding-top:5px; padding-bottom:5px;">
					<select id="<?echo $emailprivacyattr_id;?>" name="<?echo $emailprivacyattr_id;?>" onchange="checkprofile();">
					<option value="On" <? if($ColumnEmailSetting[$event]=="On"){echo "selected";} ?>>On</option>
					<option value="Off" <? if($ColumnEmailSetting[$event]=="Off"){echo "selected";} ?>>Off</option>
			   		</select>
		  	  </td>
            </tr>
			<tr>
           <td colspan="4">&nbsp;</td>
            </tr>
<?	}

     }
?>
			<tr>
              <td width="150"><input type="hidden" name="id" value="<?echo $subid;?>" /></td>
              <td width="150"><input type="hidden" name="mode" value="save" /></td>
            </tr>
			<tr>
              <td>&nbsp;</td>
      		  <td width="30%">&nbsp;</td>
            </tr>
          </table>

<?
}
}

function getemailalertstting($subscriber_id,$authornotstr){
?>
<?
	$categoryqry="select category_ids from email_alert_categorysubscribe where subscriber_id='$subscriber_id'";
	$categoryid=exec_query($categoryqry,1);
	$strcat=substr($categoryid['category_ids'],0,-1);
	$categorystr=substr($strcat,1);
	$categoryids = explode(",",$categorystr);
?>
<table width="50%" border="0" cellspacing="0" cellpadding="0" align="left">
            <tr>
              <td colspan="2" class="formContentHeading" style="padding-left:8px; padding-top:10px;">Newsletter Defaults</td>
            </tr>
            <tr>
              <td colspan="2"  class="formContent" style="padding-left:8px;">Subscribe me to all newsletter emails
				<select name="selectall" id="selectall" onchange="newsletteremail();">
                <!--<option selected="selected" value="" ></option>-->
					<option value="" ></option>
					<option value="On" >On</option>
					<option value="Off">Off</option>
                </select>
			 </td>
            </tr>
            <tr>
				<td colspan="2" ><hr size="1px" color="#CCCCCC" /></td>
             </tr>
			 <tr>
              <td colspan="2" class="formContent" style="padding-left:8px;">I wish to receive...</td>
            </tr>
			<tr>
              <td colspan="2" >
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <?
                $qrydigest="select recv_daily_gazette,recv_promo from subscription where id=$subscriber_id";
				$digest=exec_query($qrydigest,1);
				$digestpromo=$digest;
				/* if(!$digest['recv_promo']){
					$qrydigest="select id,recv_promo from subscription_ps where subid=$subscriber_id";
					$digestpromo=exec_query($qrydigest,1);
					if(!$digestpromo){
						$digestpromo=$digest;
					}
				}*/
			  ?>
				<tr>
				  <td valign="top" style="padding-left:8px;">
				  <select id="digest" name="digest">
				    <option value="On" <? if($digest['recv_daily_gazette']=='1'){echo "selected";} ?>>On</option>
					<option value="Off" <? if($digest['recv_daily_gazette']=='0'){echo "selected";} ?>>Off</option>
                  </select></td>
                  <td valign="top" style="padding-left:6px;"><span class="formContent">Minyanville Digest</span> (daily) -An end of day synopsis of all the articles published on Minyanville.com for the day.</td>
				  </tr>
				  <tr>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
	               </tr>

				<?
			  	$qry="select id,title,frequency,description from email_categories where visible='1'";
				foreach(exec_query($qry) as $row) {
					if(in_array($row['id'],$categoryids)){
						$selected="On";
					}else {
						$selected="Off";
					}
				?>
				  <tr>
                  <td valign="top" style="padding-left:8px;">
				  <select id="category" name="category[<?=$row['id']?>]">
                    <option value="on" <? if($selected=="On"){echo "selected";} ?>>On</option>
					<option value="off" <? if($selected=="Off"){echo "selected";} ?>>Off</option>
                  </select></td>
                  <td valign="top" style="padding-left:6px;"><span class="formContent"><?=$row['title'];?></span> (<?=$row['frequency'];?>) -<?=$row['description'];?></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
				<? } ?>
				<!--new code added for recv_promo-->
			<tr>
				  <td valign="top" style="padding-left:8px;">
				  <select id="recv_promo" name="recv_promo">
				    <option value="On" <? if($digestpromo['recv_promo']=='1'){echo "selected";} ?>>On</option>
					<option value="Off" <? if($digestpromo['recv_promo']=='0'){echo "selected";} ?>>Off</option>
                  </select></td>
                  <td valign="top" style="padding-left:6px;"><span class="formContent">Minyanville News and Events</span> (daily) -From time to time, Minyanville may send you email to keep you informed about what's going on in Minyanville, including new products and services, featured stories, changes to our web site, Todd Harrison appearances and more..</td>
				  </tr>
				  <tr>
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
	               </tr>
			<!--new code added for recv_promo-->
			</table>
			</td>
			</tr>

			<tr>
			<td colspan="2" style="padding-left:60px;"><span class="formContent">Custom Professor Articles -- </span>Select the professor's whose articles you'd like to receive via email the second they're published.</td>
            </tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<?
				$authorqry="select author_id from email_alert_authorsubscribe where subscriber_id=$subscriber_id";
				$authorid=exec_query($authorqry,1);
				$strauth=substr($authorid['author_id'],0,-1);
				$authorstr=substr($strauth,1);
				$qrychk="select id,name from contributors where id in($authorstr)";
			?>
			<tr>
              <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
			  <?
				$i=0;
				$j=0;
				$k=0;
				$displayauthorarr=array();
				foreach(exec_query($qrychk) as $row) {
					$displayauthorarr[$row['id']]=$row['name'];
				}
				$totrec=count($displayauthorarr);
				$onlyindex=array();
				$onlyindex=array_keys($displayauthorarr);
				if($totrec%3){
					// $totalrows=(($totrec-1)/3)+$totrec%3;
					$totalrows=round((($totrec-1)/3)+1);
				}else{
					$totalrows=(($totrec)/3)+$totrec%3;
				}
			    if($totrec=='1'){ ?>
					<tr>
					<td width="7%" valign="top"><input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j];?>]" id="author1" value="<?=$onlyindex[$j];?>"checked="checked"></td>
				  <td width="25%"><span class="style6"><?=$displayauthorarr[$onlyindex[$j]]; ?></span></td>
				<? }else{
				for($i=0;$i<$totalrows;$i++){
					$j=$j+$i;
              if($displayauthorarr[$onlyindex[$j]]){
			  ?>
					<tr>
					<td width="7%" valign="top"><input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j]?>]" id="author1[]" value="<?=$onlyindex[$j];?>"checked="checked"></td>
				  <td width="25%" valign="top"><span class="style6"><?=$displayauthorarr[$onlyindex[$j]]; ?></span></td>

				  <? }
				   if($displayauthorarr[$onlyindex[$j+1]]){
				   ?>
				  <td width="7%" valign="top"><input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j+1];?>]" id="author1[]" value="<?=$onlyindex[$j+1];?>"checked="checked"></td>
				  <td width="30%"  valign="top"><span class="style6"><?=$displayauthorarr[$onlyindex[$j+1]]; ?><?// $j=$i+1;?></span></td>
				  <? }
				 if($displayauthorarr[$onlyindex[$j+2]]){
				  ?>
				  <td width="7%" valign="top"><input style="border:0px;" type="checkbox" name="author[<?=$onlyindex[$j+2];?>]" id="author1[]" value="<?=$onlyindex[$j+2];?>"checked="checked"></td>
				  <td width="40%"  valign="top"><span class="style6"><?=$displayauthorarr[$onlyindex[$j+2]]; ?><? $j=$i+2+$k;?></span></td>
				  </tr>
				  <?  }
				  $k=$k+1;
				   ?>
		<?  } }?>


             </table></td>
            </tr>
			<?

			// $qry="select id,name from contributors where id not in($authorstr) and id not in ($authornotstr)";
			// $author_list=exec_query($qry);
			global $D_R;
			include_once("$D_R/lib/email_alert/_lib.php");
			$author_list=getContributorsArray($authorstr,$authornotstr);
			?>
			<tr>
			<td colspan="2" style="padding-bottom:10px; padding-top:10px; padding-left:8px;"><span class="formContent">Add Authors</center></span></td>
			</tr>
			<tr>
			<td colspan="2" align="left" style="padding-left:2px;">
				<table cellspacing="0" cellpadding="0" border="0" width="350px">
				<tr vAlign="bottom">
				<td valign="bottom" style=" padding-left:8px;">
				<select name="authors[]" multiple align="absmiddle" size="8">
				<?selectHashArr($author_list,"id","name");?>
				</select>
				</td>
				<td width="200px" align="left" valign="top" style="padding-bottom:10px;">[ctrl]+[click] to select multiple</td>
				</tr></table></td></tr></table>
<? }

function updateemailPrivacy($subid, $arremail)
{
	$len=count($arremail);
	$sqlemailupdateprivacy = "select id,subscription_id,email_id,alert from ex_user_email_settings where subscription_id='".$subid."'";
	$resultsemailupdateprivacy = exec_query($sqlemailupdateprivacy);
	$emailupdateprivacy_rows = count($resultsemailupdateprivacy);
	if($emailupdateprivacy_rows > 0)
	{
		foreach($arremail as $id=>$value)
		{
			if(($id!='mode')&&($id!='default')&&($id!='id')){
			$strprivacy[subscription_id]=$subid;
			$strprivacy[email_id]=$id;
			$strprivacy[alert]=$value;
			$strPrivacyId[subscription_id]=$subid;
			$strPrivacyId[email_id]=$id;
			update_query('ex_user_email_settings',$strprivacy,$strPrivacyId);
			}
		}
	}
	else
	{
		foreach($arremail as $id=>$value)
		{
			if(($id!='mode')&&($id!='default')&&($id!='id')){
			$privacydata=array(
			subscription_id=>$subid,
			email_id=>$id,
			alert=>$value
			);
			insert_query("ex_user_email_settings",$privacydata);
			}
		}
	}
}

function updatePrivacy($subid,$arr)
{
	$len=count($arr);
	$sqlupdateprivacy = "select id,subscription_id,privacy_attribute_id,enabled from ex_profile_privacy where subscription_id='".$subid."'";
	$resultsupdateprivacy = exec_query($sqlupdateprivacy);
	$updateprivacy_rows = count($resultsupdateprivacy);
	if($updateprivacy_rows > 0)
	{
		foreach($arr as $id=>$value)
		{
			if(($id!='mode')&&($id!='default')&&($id!='id')){
			$strprivacy[subscription_id]=$subid;
			$strprivacy[privacy_attribute_id]=$id;
			$strprivacy[enabled]=$value;
			$strPrivacyId[subscription_id]=$subid;
			$strPrivacyId[privacy_attribute_id]=$id;
			update_query('ex_profile_privacy',$strprivacy,$strPrivacyId);
			}
		}
	}
	else
	{
		foreach($arr as $id=>$value)
		{
			if(($id!='mode')&&($id!='default')&&($id!='id')){
			$privacydata=array(
			subscription_id=>$subid,
			privacy_attribute_id=>$id,
			enabled=>$value
			);
			insert_query("ex_profile_privacy",$privacydata);
			}
		}

	}
}	//Privacy setting ends here
function displayPeopleExchange($subscription_id=NULL)
{
	global $profileMatchingFields,$lang,$page_config,$peopleexchnglmt,$pageName;

	//print_r($profileMatchingFields);
	$objfriend=new friends();
	$friends=$objfriend->get_friend_list($subscription_id);
	$friendslist=array();
	foreach($friends as $frnds=>$value)
	{
		$temparr=$friends[$frnds];
		$frndid_get=$temparr[subid];
		$friendslist[]=$frndid_get;
	}
    $profile=array();
	$profile=get_user_profile($subscription_id);
	if($profile)
		$objTemp= new Exchange_Element();
	if(is_array($profile)) {
	foreach($profile as $id=>$value)
	{
		if(in_array($id,$profileMatchingFields))
		{
			$tagnames = $value;
			//$tagnames = $objTemp->normalize_tag($tagnames);
			$tagnames = $objTemp->tag_space($tagnames);
			$final_tags[$id]=$tagnames;
		}
	}
	}
    if(is_array($final_tags)) {
	foreach($final_tags as $id=>$value){
		if(is_array($value))
		foreach($value as $innerkey=>$innervalue)
		{
			if(($id!='') && ($id!='stock')){
				$sqlMatchedProfile="select concat(S.fname,' ',S.lname) name,S.id,EPAM.value as val from subscription S,ex_user_profile EUP,ex_profile_attribute_mapping EPAM, ex_profile_attribute EPA where S.id=EUP.subscription_id and EUP.id=EPAM.profile_id and EPAM.attribute_id=EPA.id and (EPA.attribute='".$id."' and upper(EPAM.value) like '%".$innervalue."%')";
			}else{
			$sqlMatchedProfile="select concat(S.fname,' ',S.lname) name,S.id from subscription S,ex_user_profile EUP,ex_profile_attribute_mapping EPAM, ex_profile_attribute EPA where S.id=EUP.subscription_id and EUP.id=EPAM.profile_id and EPAM.attribute_id=EPA.id and (EPA.attribute='".$id."' and upper(EPAM.value) like '%".$innervalue."%')";
			}
			$resMatchedProfile=exec_query($sqlMatchedProfile);
			foreach($resMatchedProfile as $MatchedProfileid=>$MatchedProfilevalue)
			{
				$matchedProfile[$MatchedProfilevalue['id']][$id][]=$innervalue;
				//if($id=='Occupation'){
				if(($id!='') && ($id!='stock')){
					if($id=='General Interests'){
						$matchedGeneralInterests[$MatchedProfilevalue['id']]=$MatchedProfilevalue['val'];
					}else if($id=='Previous Companies'){
						$matchedPreviousCompanies[$MatchedProfilevalue['id']]=$MatchedProfilevalue['val'];
					}else if($id=='Investment Interests'){
						$matchedInvestmentInterests[$MatchedProfilevalue['id']]=$MatchedProfilevalue['val'];
					}else if($id=='Occupation'){
						$matchedOccupation[$MatchedProfilevalue['id']]=$MatchedProfilevalue['val'];
					}else if($id=='Company'){
						$matchedCompany[$MatchedProfilevalue['id']]=$MatchedProfilevalue['val'];
					}
				}
			}
		}
	}
	}
	$matched_watchlist = match_watchlist($subscription_id);
	if(is_array($matched_watchlist)){
	foreach($matched_watchlist as $id=>$value)
	{
		foreach($value as $innerkey=>$innervalue)
		{
			$matchedProfile[$id]['stock'][]=$innervalue;
		}
	}
	}
	?>
			<script src="<?=$HTPFX.$HTHOST?>/js/Discussion.1.2.js" type="text/javascript"></script>
		<table border="0" width="100%" style="border-left:1px solid #cccccc; padding:0px; margin:0px; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; margin:0px; text-align:left;" cellpadding="0" cellspacing="0" align="left">
		<tr>
		<td valign="top">
				   <table cellpadding="0" cellspacing="0" width="100%" border="0" align="left">
		<tr>
		<td><table width="100%" style="padding:0px; margin:0px; border-bottom:1px solid #cccccc;  padding:0px;margin:0px;" cellpadding="0" cellspacing="0" align="center">
		<tr align="center" valign="top">
								  <td valign="top" class="ProfileBlogs"><?=$lang['PeopleExchangeTitle']?></td>
		</tr>
		</table>
		</td>
		</tr>
		<?
		if(count($matchedProfile)>=1){
		?>
				<tr><td style="padding-left:10px;" height="41" class="SearchText"><br /><?=$lang['PeopleExchangeDesc']?><br /><br /></td></tr>
			<tr><td style="padding-left:10px;">
						  <?
	}else{
		?>
				</table>
				</td></tr>
				<tr>
				<td style="padding-bottom:10px;padding-top:10px;padding-left:10px;" colspan="2" class="SuggestedArticleBox"><?=$lang['NoPeopleExchange']?></td></tr>
			</table>
			<?
			return;
	}
		$count=0;
		$lmt=0;
		if(array_key_exists($subscription_id, $matchedProfile)){
			unset($matchedProfile[$subscription_id]);
		}
		if($peopleexchnglmt>count($matchedProfile)){
			$peopleexchnglmt=count($matchedProfile);
		}
		$matchedProfile_rand_keys=array_rand($matchedProfile,$peopleexchnglmt);
		if(isset($matchedProfile_rand)){unset($matchedProfile_rand);}
		foreach($matchedProfile_rand_keys as $keys)
		{
			$matchedProfile_rand[$keys]=$matchedProfile[$keys];
		}
		foreach($matchedProfile_rand as $id=>$value)
		{
			if($id!=$subscription_id){
			$lmt++;
			if($lmt<=$peopleexchnglmt){
				if(!in_array($id,$friendslist)){
					if($id!=$subscription_id){
						$count++;
						$subscriptionInfo=getSubscriptionInfo($id);
						?>
								<table border="0" width="100%" border color=red cellspacing="0" cellpadding="0"><tr><td>
							<span class="SearchBlogsYouFollow"><a href="<?=$page_config['profile']['URL']."?userid=".$id;?>" class="SuggestedArticleBox1"><?=ucwords(strtolower($subscriptionInfo['fname']." ".$subscriptionInfo['lname']))?></a><br /></td></tr>
							<tr><td><span class="searchSugg">
												<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<?

							foreach($value as $typekey=>$typevalue)
						{
							$tags='';
							foreach ($typevalue as $tagkey=>$tagvalue)
							{
								if($tags==''){
									$tags=makelinks($tagvalue,7,$typekey);
								}else{
									$tags=$tags."<span class=profilesug>, </span>".makelinks($tagvalue,7,$typekey);
								}
							}
							echo '<tr><td class="typedisplay" width="100%">';
							if($typekey=='stock'){$typekey='Watchlist';}


							if(($typekey!='') && ($typekey!='Watchlist')){
								$field=0;
								if($typekey=='Company'){
									$field=getProfileattrID('Company');
									$tagsval='';
									$tags='';
									$tagsval=$matchedCompany[$id];
								}else if($typekey=='General Interests'){
									$field=getProfileattrID('GenInt');
									$tagsval='';
									$tags='';
									$tagsval=$matchedGeneralInterests[$id];
								}else if($typekey=='Previous Companies'){
									$field=getProfileattrID('PreComp');
									$tagsval='';
									$tags='';
									$tagsval=$matchedPreviousCompanies[$id];

								}else if($typekey=='Investment Interests'){
									$field=getProfileattrID('InvInt');
									$tagsval='';
									$tags='';
									$tagsval=$matchedInvestmentInterests[$id];

								}else if($typekey=='Occupation'){
								$field=getProfileattrID($typekey);
								$tagsval='';
								$tags='';
									$tagsval=$matchedOccupation[$id];

								}
								$tags=linkdatas($tagsval,7,$field);
								echo $typekey.":<span class=ocuupation> $tags</span></td></tr>";
							}else{
							echo $typekey.": $tags</td>";
							echo '</tr>';
						}
						}
						?>
								</table></span></td></tr>
								<tr><td>
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
							<td width="33%"><div  class="SearchBlogsYouFollowp">
							<a href="<?=$page_config['profile']['URL']."?userid=".$id;?>" class="SearchBlogsYouFollowp">View Profile</a></div></td>
							<?
							$urlRequest=$page_config['community_save']['URL'];
						$urlRequest.="?Ptype=request";
						$urlRequest.="&sender_id=".$subscription_id;
						$urlRequest.="&receiver_id=".$id;
						?>
								<td width="33%"><div  id="AddFriend<?=$id?>"><a style="cursor:pointer;" onclick="preHttpRequest('AddFriend<?=$id?>','<?=$urlRequest?>','0');
						" class="SearchBlogsYouFollowp">
								Add to Friends</a> </div></td>
								<td width="33%"><div>
										  <?
										  $peopleid=$id;
						$userloginid=$subscription_id;
						$cansendmsg='';
						$cansendmsg=is_msg_allowed($peopleid,$userloginid);
						if($cansendmsg=='true'){
							?>
									<a href="<?=$page_config['compose_message']['URL']?>?to=<?=$id?>&from=<?=$pageName?>" class="SearchBlogsYouFollowp">Send a message</a><?}?>
								</div></td>
								</tr></table>
								</td></tr></table><br />
								<?
					}
				}
			}//ends here if($lmt<=$peopleexchnglmt){
			}
		}
		?>
				</td>
				</tr>
				</table>
				</td>
				</tr>
				</table>
				<?
				unset($friendslist);
				unset($matchedProfile_rand_keys);
				unset($matchedProfile_rand);
}

function preparecategoryArticlesBox($category_id,$title,$num_start=0,$no_articles,$is_more_article=1) {
	global $HTHOST,$HTPFX;
	$qry_article="select id,title,contrib_id authorid,keyword,blurb,character_text talkbubble,contributor author from articles where ";
	if($category_id){
		$qry_article.="instr(CONCAT(',',articles.category_ids,','),',$category_id,')>0 and ";
	}
	$qry_article.="is_featured<>'1' and approved='1' and is_live='1' order by date Desc limit $num_start,$no_articles";
	$res_article=exec_query($qry_article);
?>
<div>
	<div>
	<?
		foreach($res_article as $row){ ?>
	<div  class="moduleinner"><a class="articleLinkNew" href= <?= $pfk.makeArticleslink($row['id'],$row['keyword'],$row['blurb']);?>>
	<div class="NewsArticleTitle"><h4><?= $row['title'] ?></h4></div></a><?
				echo "<div class=\"AuthorName\" style=\"font-weight:bold;\">"; ?> <?= displayAuthorLink($row['author'],$row['authorid']); ?> <? echo "</div>" . chr(13);
				echo  "<div class=\"ArticleDescription\"><h6>". $row[talkbubble] ."<h6></div><br />". chr(13);
				?></div><?
				}?>
	</div>
</div>
</div>
<?
}
function googleAdWordTracking($prod, $trialType){
global $HTHOST,$HTPFX;
?>
<div style="display:none;"> <iframe src ="<?=$HTPFX.$HTHOST?>/subscription/register/googleadword.htm?prod=<?=$prod?>&type=<?=$trialType?>" width="0" height="0" ></iframe></div>
<?
}


function googleanalytics($profileid="",$tracking_name="",$isCustomTracking=NULL,$pageName=NULL){

	global $is_analytics_exec,$HTPFX,$_SESSION,$HTHOST;
	global $domain,$ga_account_id,$ga_profile_id,$ga_main_domain,$gaTrackingAuthor,$gaTrackingTicker;
	$ecommerceTracking=$_SESSION['ecommerceTracking'];
	$products=$_SESSION['activeProducts'];
	$userid=$_SESSION['user_id'];
	$pagenameArray=array();
	/*add condition for ga.js call in page*/
	$pagenameArray[0]="sign-up";
	$pagenameArray[1]="login";

	if(!in_array($pageName,$pagenameArray)){

?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
	<?
	}
?>
<script type="text/javascript">
try {
<? if(!in_array($pageName,$pagenameArray)){ ?>
var pageTracker = _gat._getTracker("<?=$ga_account_id?>-<?=$ga_profile_id?>");
<? } ?>
pageTracker._setDomainName("<?=$ga_main_domain?>");
<?
if((isset($_GET['from']) && $_GET['from'] != ""))
{
	if(!$_GET['camp']){
		$_GET['camp']='syndication';
	}if(!$_GET['medium'])
	{
		$_GET['medium']='portals';
	}
?>
pageTracker._setCampNameKey("camp"); // name
pageTracker._setCampMediumKey("medium"); // medium
pageTracker._setCampSourceKey("from");
<? } if(!empty($userid)){ ?>
pageTracker._setCustomVar(1, "UserId", "<?=$userid;?>", 1);
<? } if(!empty($products)){ ?>
pageTracker._setCustomVar(2, "ActiveProducts", "<?=$products;?>", 2);
<? }
if(!empty($gaTrackingTicker)){
	if(is_array($gaTrackingTicker))
	{
		$gaTrackingTicker = implode(",",$gaTrackingTicker);
	}
?>
pageTracker._setCustomVar(4, "Ticker", "<?=$gaTrackingTicker;?>", 3);
<?
}
if(!empty($gaTrackingAuthor)){ ?>
pageTracker._setCustomVar(5, "ContentAuthor", "<?=$gaTrackingAuthor;?>", 3);
<?
}
if($isCustomTracking){
	if($pageName=="error"){
?>
pageTracker._trackPageview(<?=$tracking_name;?>);
<?
}else{
?>
pageTracker._trackPageview("<?=$tracking_name;?>");
<?
	}
}else{
?>
pageTracker._trackPageview();
<? }if($ecommerceTracking['trans']['id'] && ($HTHOST=="www.minyanville.com" || $HTHOST=="qa.minyanville.com")){ ?>
pageTracker._addTrans(
      "<?=$ecommerceTracking['trans']['id'];?>",            // order ID - required
      "<?=$ecommerceTracking['trans']['store'];?>",  // affiliation or store name
      "<?=$ecommerceTracking['trans']['total'];?>",           // total - required
      "<?=$ecommerceTracking['trans']['city'];?>",        // city
      "<?=$ecommerceTracking['trans']['state'];?>",      // state or province
      "<?=$ecommerceTracking['trans']['country'];?>"              // country
    );

   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each
  <? foreach($ecommerceTracking['items'] as $item){ ?>
	   pageTracker._trackPageview("welcome-<?=$item['SKU'];?>");
	   pageTracker._addItem(
		  "<?=$item['id'];?>",           // order ID - necessary to associate item with transaction
		  "<?=$item['SKU'];?>",           // SKU/code - required
		  "<?=$item['name'];?>",        // product name
		  "<?=$item['category'];?>",   // category or variation
		  "<?=$item['price'];?>",          // unit price - required
		  "<?=$item['quantity'];?>"               // quantity - required
	   );
	<? } ?>
   pageTracker._trackTrans(); //submits transaction to the Analytics servers
<?
unset($_SESSION['ecommerceTracking']);
}
if($_SESSION['freeUserRegistration']){
 ?>
	pageTracker._trackPageview('Free-Membership-Signup');
 <?
 	unset($_SESSION['freeUserRegistration']);
 } ?>
} catch(err) {}</script>
<?php
}

/* used for GA*/
function createNomenCleature($productName,$trialtype,$producttype,$eventname,$source,$keyword,$from){
	$str="";
	$str .=$productName;
	if($trialtype){
		$str .='-'.trim($trialtype);
	}
	if($producttype){
		$str .='-'.trim($producttype);
	}
	if($eventname){
		$str .='-'.trim($eventname);
	}
/*	if($source){
		$str .='-'.$source;
	}*/
	if($keyword){
		$str .='-'.trim($keyword);
	}
	if($from){
		$str .='-'.trim($from);
	}

	return $str;
}
function getSlideShowTags($slideshowid,$item_type){
$res_tags_array=array();
$sql="select tag_id from ex_item_tags where item_type='$item_type' and item_id='$slideshowid'";
$res_tags_array=exec_query($sql);
return $res_tags_array;
}



function displaySmartStopAds()
{
	$rand=rand(1,2);
	if(rand==1)
	{
?>
	<table border="0" cellpadding="0" cellspacing="0" style="width: 298px; border: 1px solid black">
	    <tr>
	      <td>
	        <img src="<?=$IMG_SERVER?>/images/smartstop/MinyanvilleTop2.jpg" alt="SmartStops" /></td>
	    </tr>
	    <tr>
	      <td style="background-color: black; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;
	        padding-right: 5px; border-top: 1px solid white">
	        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%">
	          <tr>
	            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
	              <input id="ss_symbol2" type="text" value="Enter Symbol" style="width: 90px" onfocus="javascript:this.select();" />
	            </td>
	            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
	              <input type="button" value="Go" style="width: 30px" onclick="javascript:window.location='http://smartstops.net/PublicPages/SmartStopsOnDemand.aspx?partner=Minyanville&ad=1&symbol='+document.getElementById('ss_symbol2').value;" />
	            </td>
	            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
	              <p style="padding: 0px; margin: 0px; color: #ffd838; font-family: Arial; font-size: 10pt;
	                font-weight: bold; text-align: center">
	                Get Today's SmartStop</p>
	            </td>
	          </tr>
	        </table>
	      </td>
	    </tr>
	    <tr>
	      <td>
	        <img src="<?=$IMG_SERVER?>/images/smartstop/MinyanvilleBottom.jpg" alt="SmartStops" /></td>
	    </tr>
  </table>
<?
	} else{
?>
<table border="0" cellpadding="0" cellspacing="0" style="width: 298px; border: 1px solid black">
    <tr>
      <td>
        <img src="<?=$IMG_SERVER?>/images/smartstop/MinyanvilleTop1.jpg" alt="SmartStops" /></td>
    </tr>
    <tr>
      <td style="background-color: black; padding-top: 5px; padding-bottom: 5px; padding-left: 5px;
        padding-right: 5px; border-top: 1px solid white">
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%">

          <tr>
            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
              <input id="ss_symbol1" type="text" value="Enter Symbol" style="width: 90px" onfocus="javascript:this.select();" />
            </td>
            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
              <input type="button" value="Go" style="width: 30px" onclick="javascript:window.location='http://smartstops.net/PublicPages/SmartStopsOnDemand.aspx?partner=Minyanville&ad=1&symbol='+document.getElementById('ss_symbol1').value;" />
            </td>
            <td align="center" valign="middle" style="padding-left: 3px; padding-right: 3px">
              <p style="padding: 0px; margin: 0px; color: #ffd838; font-family: Arial; font-size: 10pt;
                font-weight: bold; text-align: center">

                Get Today's SmartStop</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>

        <img src="<?=$IMG_SERVER?>/images/smartstop/MinyanvilleBottom.jpg" alt="SmartStops" /></td>
    </tr>
  </table>
<?
	}
}

function getLatestRandomSlideShow($slideid)
{
	// $randomslide = "select distinct(s.id)as item_id from slideshow s,slideshow_content sc where s.id!='$slideid'  and s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY RAND() limit 0,1";
	$randomslide = "select distinct(s.id)as item_id from slideshow s,slideshow_content sc where s.id!='$slideid'  and s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' and s.id < '$slideid' order by s.id desc limit 0,1";
	$slidesarray = exec_query($randomslide,1);
	$slideidget = $slidesarray['item_id'];
	if(!$slideidget){
		$latestslide="select distinct(s.id)as item_id from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' order by s.id desc limit 1";
		$slidesarray = exec_query($latestslide,1);
		$slideidget = $slidesarray['item_id'];
	}
	$slidePosition = getPageNoofSlideshow($slideidget);
	return $slideidget."~".$slidePosition."~1";
}

function getPageNoofSlideshow($slideshowid)
{
	global $slidesCnt; // now its 4
	$sql="select s.id as id from slideshow s,slideshow_content sc where s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY s.id desc";
	$slidesarray = exec_query($sql);
	foreach($slidesarray as $resultid)
	{
		$resslidesarray[]=$resultid['id'];
	}
	//htmlprint_r($resslidesarray);

	if(in_array($slideshowid,$resslidesarray))
	{
		$index = array_search($slideshowid,$resslidesarray);
		$page = ceil(($index+1)/$slidesCnt);

		if($page==0)
		{
			$page = $page+1;
		}
	}
	else
	{
		$page =1; // when the slideshow is not approved by default we will select the first page
	}
	return $page;
}

/* This function is defined to get the single related slideshow randomly and used in slideshow/navigate.php file */
function getLatestRelatedSlideShow($slideid)
{
	$slideshow_id_type=get_itemtype_id("slideshow");// defined inside layout functions
	$tagsAssociated=getSlideShowTags($slideid,$slideshow_id_type);
	$tagsAssociatedIds="";

	foreach($tagsAssociated as $key=>$val)
	{
		if($tagsAssociatedIds==''){
			$tagsAssociatedIds=implode(",", $val);
		}else{
			$tagsAssociatedIds.=",".implode(",", $val);
		}
	}
	$slideTags=getSingleRelatedSlide($slideid,0,$configSlideCnt,$slideshow_id_type,$tagsAssociatedIds);// returns an array that relates to slide id passed
	return $slideTags;
}

function getSingleRelatedSlide($slideid,$offset,$limit=null,$slideshow_id_type,$slidetags){
	if($slidetags=='')
	{
		$goForRandom=1;
	}
	else
	{
		$goForRandom=0;
	}
	$slidesarray=array();
	$totalcount = 0;
	if(isset($slideid) && ($goForRandom==0))
	{
		$sql="select distinct(eit.item_id) as item_id from ex_item_tags eit,slideshow s,slideshow_content sc where (item_id!='$slideid' and item_type='$slideshow_id_type' and tag_id in($slidetags)) and s.approved='1' and s.id=eit.item_id and s.id=sc.slideshow_id order by RAND(),s.date desc limit 0,1";
		$slidesarray = exec_query($sql,1);
		return $slidesarray['item_id']."~1";
	}
	else if(isset($slideid) && ($goForRandom==1))
	{
		// will come to this part
		$sql="select distinct(s.id)as item_id from slideshow s,slideshow_content sc where s.id!='$slideid'  and s.approved='1' and s.id=sc.slideshow_id and sc.slide_no='1' ORDER BY RAND() limit 0,1";
		$slidesarray = exec_query($sql,1);
		return $slidesarray['item_id']."~1";
	}
	else
	{
		return "noslide~0";
	}
}

function display_featured_contributors()
{
	global $IMG_SERVER;
	$toddLatestArticle=getLatestContributorArticle(1);
	$mackeyLatestArticle=getLatestContributorArticle(48);
	$dephewLatestArticle=getLatestContributorArticle(15);


?>
<table border="0" cellspacing="0" cellpadding="0" style="width:462px;">
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=1"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_01.jpg" alt="Todd Harrison" width="150" height="87"></a></th>
    <th scope="col">&nbsp;</th>
    <th scope="col"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=48"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_02.jpg" width="150" height="87" alt="Jeff Macke"></a></th>
    <th scope="col">&nbsp;</th>
    <th scope="col"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=15"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_03.jpg" width="150" height="87" alt="Kevin Depew"></a></th>
  </tr>
  <tr>
 <td style="width:20px;">&nbsp;</td>
    <td bgcolor="006699"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=1"><div id="homefeturedcontributor"><?=substr($toddLatestArticle['title'],0,35)."...";?><div style="text-align:right; vertical-align:bottom"></div></div></a></td>
    <td style="width:20px;">&nbsp;</td>
    <td bgcolor="006699"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=48"><div id="homefeturedcontributor"><?=substr($mackeyLatestArticle['title'],0,35)."...";?><div style="text-align:right; vertical-align:bottom"></div></div></a></td>
    <td style="width:20px;">&nbsp;</td>
    <td bgcolor="006699"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=15"><div id="homefeturedcontributor"><?=substr($dephewLatestArticle['title'],0,35)."...";?><div style="text-align:right; vertical-align:bottom"></div></div></a></td>
  </tr>
  </table>
<table width="462px" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th scope="col"><img src="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/cHS1_00.jpg" width="4px" height="16" /></th>
    <th width="150" height="16" background="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/bi1.jpg" scope="col"><div align="right" style="padding-right:10px;"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=1"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_04.jpg" width="44" height="16"></a></div></th>
    <th scope="col"><img src="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/cHS1_00.jpg" width="4px" height="16" /></th>
    <th width="150" height="16" background="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/bi1.jpg" scope="col"><div align="right" style="padding-right:10px;"><a style="text-decoration:none" href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=48"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_04.jpg" width="44" height="16"></a></div></th>
    <th scope="col"><img src="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/cHS1_00.jpg" width="4px" height="16" /> </th>
    <th width="150" height="16" background="https://admin.minyanville.com/assets/FCK_Aug2007/Image/freddy/November08/bi1.jpg"  scope="col"><div align="right" style="padding-right:10px;"><a style="text-decoration:none"href="<?=$HTPFX.$HTHOST?>/news_views/contributor.htm?cid=15"><img src="<?=$IMG_SERVER?>/assets/FCK_Aug2007/Image/freddy/November08/cHS1_04.jpg" width="44" height="16"></a></div></th>
  </tr>
</table>
  <tr><td colspan="6" style="height:10px;"></td></tr>
</table>

<?
}

function displayindustrybrains300x250($tracking_zone)
{
	switch($tracking_zone)
	{
		case "article":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_ARTICLE_WHITE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
		case "av":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_AUDIO_VIDEO_WHITE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
		case "businessmarket":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_BUSINESS_MARKETS_WHITE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
		case "lifemoney":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_LIFE_MONEY_BLUE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
		case "familyfinance":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_FAMILY_FINANCE_WHITE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
		case "home":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_300X250_HP_WHITE&amp;num=4&amp;layt=8&amp;fmt=simp"></script>';
			break;
	}
	return $tag;
}

function displayindustrybrains650x250($tracking_zone)
{
	switch($tracking_zone)
	{
		case "article":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_ARTICLE_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
		case "av":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_AUDIO_VIDEO_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
		case "businessmarket":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_BUSINESS_MARKETS_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
		case "lifemoney":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_LIFE_MONEY_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
		case "familyfinance":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_FAMILY_FINANCE_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
		case "home":
			$tag='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MINYANVILLE_ROS_650X250_HP_WHITE&amp;num=5&amp;layt=10&amp;fmt=simp"></script>';
			break;
	}
	return $tag;
}

function universalGoogleAnalytics($profileid="",$tracking_name="",$isCustomTracking=NULL,$pageName=NULL){

	global $is_analytics_exec,$HTPFX,$_SESSION,$HTHOST;
	global $domain,$ga_account_id,$ua_profile_id,$ga_main_domain,$gaTrackingAuthor,$gaTrackingTicker;
	$ecommerceTracking=$_SESSION['ecommerceTracking'];
	$products=$_SESSION['activeProducts'];
	$userid=$_SESSION['user_id'];
	$pagenameArray=array();
	/*add condition for ga.js call in page*/
	$pagenameArray[0]="login";

	if(!in_array($pageName,$pagenameArray)){?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', '<?=$ga_account_id?>-<?=$ua_profile_id?>');
		</script>
	<? } ?>
	<script type="text/javascript">
		try {
			<? if((isset($_GET['from']) && $_GET['from'] != "")){
				if(!$_GET['camp']){
					$_GET['camp']='syndication';
				}
				if(!$_GET['medium']){
					$_GET['medium']='portals';
				} ?>
				/*pageTracker._setCampNameKey("camp"); // name
				pageTracker._setCampMediumKey("medium"); // medium
				pageTracker._setCampSourceKey("from");*/
			<? }

			if(!empty($userid)){ ?>
				var dimensionValue = '<?=$userid;?>';
				ga('set', 'dimension1', dimensionValue);
			<? }
			if(!empty($products)){ ?>
				var dimensionValue = '<?=$products;?>';
				ga('set', 'dimension2', dimensionValue);
			<? }
			if(!empty($gaTrackingTicker)){
				if(is_array($gaTrackingTicker)){
					$gaTrackingTicker = implode(",",$gaTrackingTicker);
				}?>
				ga('set', 'dimension3', '<?=$gaTrackingTicker;?>');
			<? }
			if(!empty($gaTrackingAuthor)){ ?>
				ga('set', 'dimension4', '<?=$gaTrackingAuthor;?>');
			<? }
			if($isCustomTracking){
				if($pageName=="error"){ ?>
					ga('send', 'pageview', '<?=$tracking_name;?>');
				<? }else{ ?>
					ga('send', 'pageview', '<?=$tracking_name;?>');
				<? }
			}else{?>
				ga('send', 'pageview');
			<? }

			if($ecommerceTracking['trans']['id'] && ($HTHOST=="www.minyanville.com" || $HTHOST=="qa.minyanville.com")){ ?>
				ga('require', 'ecommerce', 'ecommerce.js');
				ga('ecommerce:addTransaction', {
				  'id': '<?=$ecommerceTracking['trans']['id'];?>',	// Transaction ID. Required.
				  'affiliation': '<?=$ecommerceTracking['trans']['store'];?>',   // Affiliation or store name.
				  'revenue': '<?=$ecommerceTracking['trans']['total'];?>'             // Grand Total.
				});

			   // add item might be called for every item in the shopping cart
			   // where your ecommerce engine loops through each item in the cart and
			   // prints out _addItem for each
			  <? foreach($ecommerceTracking['items'] as $item){ ?>
				  ga('ecommerce:addItem', {
					  'id': '<?=$item['id'];?>',                     // Transaction ID. Required.
					  'name': '<?=$item['name'];?>',    // Product name. Required.
					  'sku': '<?=$item['SKU'];?>',                 // SKU/code.
					  'category': '<?=$item['category'];?>',         // Category or variation.
					  'price': '<?=$item['price'];?>',                 // Unit price.
					  'quantity': '<?=$item['quantity'];?>'                   // Quantity.
					});
				<? } ?>
				ga('ecommerce:send'); //submits transaction to the Analytics servers
				<?	unset($_SESSION['ecommerceTracking']);
			}

			if($_SESSION['freeUserRegistration']){ ?>
				ga('send', 'pageview', 'Free-Membership-Signup');
				<? unset($_SESSION['freeUserRegistration']);
			} ?>
		} catch(err) {}
	</script>
<?php } ?>
