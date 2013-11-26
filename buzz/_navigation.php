<?php
//This script accepts the subscriber ID, looks up preferences and pulls the navigation pages out of database.
// and sends the list back to be put in the 'navigation' div on buzz.php.

// load and instantiate JSON services object
global $D_R;
include_once("$D_R/lib/json.php");

$json = new Services_JSON();


// Get data from Post string
$sid = $_POST['sid'];
$page = strip_tags($_POST['page']) + 0;
$howmany = $_POST['posts'];
if ($page == null || $page == -1) {$page = 1;}

if ($page > 0) {
	// Access database to cull look up preferences and then generate page list

 $qry="SELECT COUNT(distinct buzzbanter.id) AS count " .
	"FROM buzzbanter_today buzzbanter,contributors " .
	//"WHERE date_format(buzzbanter.date,'%m/%d/%y')=date_format('".mysqlNow()."','%m/%d/%y') " .
	"WHERE DATE_FORMAT(buzzbanter.date,'%m/%d/%y') =  IF(DAYOFWEEK('".mysqlNow()."') = '7',DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 28 HOUR),'%m/%d/%y') ,DATE_FORMAT(DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR),'%m/%d/%y')) ".
	"AND buzzbanter.contrib_id = contributors.id ".
	"AND buzzbanter.contrib_id NOT IN(SELECT SCF.contrib_id FROM subscriber_contributor_filter SCF WHERE SCF.subscriber_id='".$sid."')".
	"AND buzzbanter.is_live='1' " .
	"AND buzzbanter.show_in_app='1' " .
	"AND buzzbanter.approved='1' " .
	"ORDER BY buzzbanter.date ASC ";

	$rows = exec_query($qry);
	$row = $rows[0];
	$numrows = $row['count'];
    $numPages = floor(($numrows + $howmany - 1) / $howmany);
    if ($numPages==0) $numPages = 1;
	//$numPages = ceil($numrows / $howmany);
	$prevPage = ($page>0) ? $page - 1 : null;
	$nextPage = ($page<$numPages) ? $page + 1 : null;
	$noPrev = ($page==1) ? true : false;
	$noNext = ($page==$numPages) ? true : false;
	if ($noPrev) {
		$start = 1;
		$end = ($numPages > 5) ? 5 : $numPages;
		}
	if ($noNext) {
		$start = $numPages - 4;
		if ($start < 1) $start = 1;
		$end = $numPages;
		}
	if (!$noPrev && !$noNext) {
		if ($page > 5 && $page > $numPages - 5) {
			$start = $numPages - 4;
			$end = $numPages;
		} elseif ($page == 2 && $numPages >= 5) {
			$start = 1;
			$end = 5;
			//second from last case
		} elseif ($numPages - $page == 1) {
			$start = ($page <= 4) ? 1 : $page - 3;
			$end = $numPages;
			//third from last case
		} elseif ($numPages - $page == 2) {
			$start = ($page <= 3) ? 1 : $page - 2;
			$end = $numPages;
		} else {
			$start = ($page <= 4) ? 1 : $page - 2;
			if ($page > ($numPages - 2)) $end = $numPages;
			else $end = $page + 2;
		}
	}
} else { //there are no posts yet so don't trouble the database
	$numrows = 0;
	$numPages = 0;
	$prevPage = null;
	$nextPage = null;
	$noPrev = true;
	$noNext = true;
	$start = 0;
	$end = 0;
}
/*
if ($noPrev==true) $noPrev = "true"; else $noPrev = "false";
if ($noNext==true) $noNext = "true"; else $noNext = "false";
//if ($nextPage == null) $nextPage = 0;
//if ($prevPage == null) $prevPage = 0;
$output = "{'lastPage': " . $numPages . ", ";
$output .= "'noPrev': " . $noPrev . ", ";
$output .= "'noNext': " . $noNext . ", ";
$output .= "'startPage': " . $start . ", ";
$output .= "'endPage': " . $end . ", ";
$output .= "'page': " . $page . ", ";
$output .= "'prevPage': " . $prevPage . ", ";
$output .= "'nextPage': " . $nextPage . "}";

*/
$value =  array('lastPage'=>$numPages, 'noPrev'=>$noPrev, 'noNext'=>$noNext,
			    'startPage'=>$start, 'endPage'=>$end, 'page'=>$page,
			    'prevPage'=>$prevPage, 'nextPage'=>$nextPage);
$output = $json->encode($value);

echo strip_tags($output);




//echo $navigation;
//$db->free();