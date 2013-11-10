<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Buzz and Banter</title>
</head>
<body>
<?php
//including in authentication for pages
include_once("$DOCUMENT_ROOT/lib/_includes.php");
//$_SESSION['from_content']=true;
global $IMG_SERVER,$from_content;$from_content = true;
//include_once("$D_R/gazette/buzzbanter/_authenticate.htm");
$browser = '';
$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
if (stristr($navigator_user_agent, "msie"))
{
	$browser = 'msie';
}
elseif (stristr($navigator_user_agent, "gecko"))
{
	$browser = 'mozilla';
}
elseif (stristr($navigator_user_agent, "mozilla/4"))
{
	$browser = 'ns4';
}

// This script accepts SID and Page variables then pulls the appropriate page from the database.
// Sends html back to buzz.php and populates the 'articles' div.
$sid = $page = $latest = $latestAnchor = $postsOnLast = $filter = $numPages = $fetch = null;
$sid = $_POST['sid'];
$page = $_POST['page']; // will be set on "load page" request
$bbid = $_POST['bbid']; // will be set on "jump to post" request
$latest = $_POST['latest']; // will be set on "jump to latest" request
$timestamp = $_POST['udate']; // will be set on "jump to time stamp" request
$chars = $_POST['chars']; // right now "show characters" is sent from client

$howmany = $fetch = $_POST['posts']; // posts per page - fetch will change if latest or bbid are set
if ($chars == 1) {
	$charqry = " buzzbanter.image AS image,";
}
if ($timestamp != null) {
		$qry="SELECT COUNT(distinct buzzbanter.id) AS count " .
		"FROM buzzbanter_today buzzbanter,contributors " .
		"WHERE DATE_FORMAT(buzzbanter.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
		"AND buzzbanter.contrib_id = contributors.id ".
		"AND buzzbanter.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
        "AND buzzbanter.is_live='1' AND buzzbanter.approved='1' " .
        "AND buzzbanter.show_in_app='1' " .
        "AND buzzbanter.date <= '" . $timestamp . "'";

    $rows = exec_query($qry);
    $row = $rows[0];
    $numrows = $row['count'];
    $page = floor(($numrows + $howmany - 1) / $howmany);
    echo '<div id="latest"></div>';
}


// if $bbid is not null then this is a "jump to post"
// need to get post's page number before moving on. Must run the exact same query as below in order to get accurate result
if ($bbid != null) {
  $rows = exec_query('SELECT date FROM buzzbanter WHERE id=' . $bbid);
  if ($rows && $rows[0] && ($date = $rows[0]['date'])) {
		 $qry="SELECT COUNT(distinct buzzbanter.id) AS count " .
		"FROM buzzbanter_today buzzbanter,contributors " .
        //"WHERE date_format(buzzbanter.date,'%m/%d/%y')=date_format('".mysqlNow()."','%m/%d/%y')  " .
		"WHERE DATE_FORMAT(buzzbanter.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
		"AND buzzbanter.contrib_id = contributors.id ".
		"AND buzzbanter.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
        "AND buzzbanter.is_live='1' AND buzzbanter.approved='1' " .
        "AND buzzbanter.show_in_app='1' " .
        "AND buzzbanter.date <= '" . $date . "'";

    $rows = exec_query($qry);
    $row = $rows[0];
    $numrows = $row['count'];
    $page = floor(($numrows + $howmany - 1) / $howmany);
  } else
    $page = 0;
  //$fetch = $numrows - ($page - 1) * $howmany;
}

if ($latest==1) {
  $qry="SELECT COUNT(distinct buzzbanter.id) AS count " .
	"FROM buzzbanter_today buzzbanter,contributors " .
	//"WHERE date_format(buzzbanter.date,'%m/%d/%y')=date_format('".mysqlNow()."','%m/%d/%y') " .
	"WHERE DATE_FORMAT(buzzbanter.date,'%m/%d/%y') = IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
	"AND buzzbanter.contrib_id = contributors.id ".
	"AND buzzbanter.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
	"AND buzzbanter.is_live='1' " .
	"AND buzzbanter.show_in_app='1' " .
	"AND buzzbanter.approved='1' " .
	"ORDER BY buzzbanter.date ASC ";
  $rows = exec_query($qry);
  $row = $rows[0];
  $numrows = $row['count'];
  $page = floor(($numrows + $howmany - 1) / $howmany);
  $numPages = $page;
  $fetch = $numrows - ($page - 1) * $howmany;
  echo '<div id="latest"></div>';
}
//then move on with the content
$offset = ($page * $howmany) - $howmany;
if ($offset<0) $offset=0;
$limit = "LIMIT " . $offset . ", " . $fetch;

	$qry="SELECT distinct buzzbanter_today.id AS id, " .
	"buzzbanter_today.title AS title, " .
	"buzzbanter_today.body AS body, " .
	"buzzbanter_today.author AS author, " .
	"buzzbanter_today.contrib_id as contrib_id, " .
	"buzzbanter_today.branded_img_id as brandedlogo, " .
	"buzzbanter_today.image as image, " .
	"buzzbanter_today.position AS position,  " .
	"date_format(buzzbanter_today.date,'%r') AS mdate, " .
	"UNIX_TIMESTAMP(buzzbanter_today.date) AS udate, " .
	"buzzbanter_today.date AS date, " .
	"buzzbanter_today.login AS login " .
	"FROM buzzbanter_today,contributors " .
	"WHERE DATE_FORMAT(buzzbanter_today.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
	"AND buzzbanter_today.contrib_id = contributors.id ".
	"AND buzzbanter_today.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
	"AND buzzbanter_today.is_live='1' " .
	"AND buzzbanter_today.show_in_app='1' " .
	"AND buzzbanter_today.approved='1' " .
	"ORDER BY buzzbanter_today.date ASC " . $limit;
$rows = exec_query($qry);
$numrows = count($rows);

if ($numrows > 0) {
	for ($i=$numrows - 1;$i>=0;$i--) {
		$row = $rows[$i];
		// get branded logo
		$brandedlogoImage1='';
		$wBrandedLogo1='';
		$hBrandedLogo1='';

		if(trim($row['brandedlogo'])!=''){
			$sqlBrandedLogo='select url,assets1,assets2 from buzz_branded_images where id='.$row['brandedlogo'];
			$resultBrandedLogo=exec_query($sqlBrandedLogo,1);
			if($resultBrandedLogo['assets1']!=''){
				$brandedlogoImage1=$resultBrandedLogo['assets1'];
			}


		}
		//////////////////////////////////////////
		if ($i==($numrows-1)) {
			$article .= '<div id="topPage" style="display:none;">' . $row['date'] . "</div>";
		}
		if ($i==($numrows-1) && $latest==1)
			$article .= '<div id="latestPostID" style="display:none;">' . $row['id'] . '</div>';
		if($row['login'] != '(automated)' && $row['login'] != '(feed_automated)'){
			if ($browser=="mozilla")
			{
				$article .= '<table id="post' . $row[id] . '" class="article" border="0" cellspacing="0" cellpadding="0">';
  				$article .= '<tr><td valign="top"><table class="articleInfo" border="0" cellspacing="0" cellpadding="0">';
  			}
  			else
  			{
  				$article .= '<table id="post' . $row[id] . '" class="article">';
  				$article .= '<tr><td valign="top"><table class="articleInfo">';
  			}

			if($chars==1){
				if(is_file($img="$D_R$row[image]")){
					list($w,$h)=getimagesize($img);
					$w=ceil($w*.66);
					$h=ceil($h*.66);
					$article .= '<tr><td><img class="character" src="' . $IMG_SERVER . $row[image] . '" border=0 width="' . $w . '" height="' . $h . '"  /></td></tr>';
				}
			}
			$article .= '<tr><td><p class="author">' . $row[author] . '</p></td></tr>' .
						'<tr><td><p class="time">' . $row[mdate] . '</p></td></tr>' .
						'<tr><td><p class="positions">' . $row[position] . '</p></td></tr></table></td>';
			if ($browser=="mozilla")
			{

				$article .= '<td valign="top"><table align="left"  border="0" cellspacing="0" cellpadding="0" class="articleBody"' . $charstyle . '>';
			}
			else
			{
				$article .= '<td valign="top"><table align="left" class="articleBody"' . $charstyle . '>';
			}


			$article .= (strlen($row[title])) ? '<tr><td colspan="2"><h2>' . $row[title] . '</h2></td></tr>' : '<tr><td colspan="2"><h2>' . $row[title] . '</h2></td></tr>';
			//////// Start Branded Logo //////////////
			$article .='<tr>';
			$article .= (strlen($brandedlogoImage1)) ? '<td colspan="2"><a href="'.$resultBrandedLogo[url].'" ><img border="0" src="' . $brandedlogoImage1 . '"></a></td>' : '<td>&nbsp;</td>';
			$article .='</tr>';
			//////// End Branded Logo //////////////
			$article .= (strlen($row[body])) ? '<tr><td colspan="2">'. strip($row[body]) . '</td></tr>'.
					'<tr><td class="clear"></td></tr>' .
					'<tr><td class="clear">'.displayChartBuzzImages($row['id']).'</td></tr>' .
					'<tr><td><a class="addBookmark" href="javascript:void(0);" onclick="javascript:addBookmark(' . $row[id] . ')"><img src="images/bookmark_add.gif"/></a>' .
					'<a name="postPrint' . $row[id] . '" id="' . $row[id] . '" href="javascript:void(0);" onclick="printPostID(' . $row[id] . ', \'postPrint' . $row[id] . '\');" class="icon"><img src="images/print_post.gif" /></a></td></tr>' .
					'<tr><td class="clear"></td></tr></table></td></tr></table>' : '<tr><td colspan="2">'. strip($row[body]) . '</td></tr>'.

					'<tr><td class="clear"></td></tr>' .
					'<tr><td><a class="addBookmark" href="javascript:void(0);" onclick="javascript:addBookmark(' . $row[id] . ')"><img src="images/bookmark_add.gif"/></a>' .
					'<a name="postPrint' . $row[id] . '" id="' . $row[id] . '" href="javascript:void(0);" onclick="printPostID(' . $row[id] . ', \'postPrint' . $row[id] . '\');" class="icon"><img src="images/print_post.gif" /></a></td></tr>' .
					'<tr><td class="clear"></td></tr></table></td></tr></table>';

		}
		elseif (!$has_image && !$has_author && strlen($row[body])>0)
		{
			$article .= '<table class="inlinePostAlert" id="post' . $row[id] . '"><tr><td>' . strip($row[body]).'</td></tr></table>';
		}

	 $article .= (strlen($article)) ? '<div class="articleDivider"></div>' : null;
	 echo $article;
	 $article = "";
	}
	echo '<div id="pageNo" style="visibility:hidden;">' . $page . '</div>';
}
if (count($rows) < 1 && $page <= 1) {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr><td>&nbsp;</td></tr>
  			<tr>
    			<td align="center">
    			<div style="font-size:12px; font-family:Arial;">
    			Welcome to the Buzz and Banter</div>
	 			<div style="font-size:12px; font-family:Arial;">
        		<p align="left">The Buzz and Banter is Minyanville\'s premier community tool for investors.</p>
        		<p align="left">During market hours, the Buzz and Banter provides insight and ideas that help our members better assimilate the movements of the markets. We\'ll see you bright and early. </p>
    			</div></td>
  			</tr>
  			</table>';
echo '<div class="progress" style="visibility:hidden;">There are no posts to display.</div>';
echo '<div id="pageNo" style="visibility:hidden;">' . $page . '</div>';
	//echo 'Page: ' . $page;
	//if ($page == null) echo "null";
}
?>
</body>


