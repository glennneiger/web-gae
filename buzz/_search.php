<?php

function truncate ($string, $max = 200, $rep = '') {
   $leave = $max - strlen ($rep);
   return substr_replace($string, $rep, $leave);
}

$DATE_STR="D M jS, Y";
$qry="SELECT DISTINCT buzzbanter.id, buzzbanter.title, buzzbanter.body, buzzbanter.author as bbauthor,date_format(date,'%r')mdate,UNIX_TIMESTAMP(date)udate, contributors.name as author,
	  concat('http://$HTTP_HOST/buzz/print.php?date=',date_format(date,'%m/%d/%Y'))url
	  FROM buzzbanter, contributors WHERE buzzbanter.contrib_id = contributors.id
	  AND is_live='1'
	  AND approved='1'
	  AND show_on_web='1' ";

		$q = lc($_POST['q']);
		$mo = $_POST['mo'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$author = $_POST['author'];
		if ($mo == '-Month-') { $mo = null; }
		if ($day== '-Day-')   { $day= null; }
		if ($year=='-Year-')  { $year=null; }
		if ($author == '--All Authors--') { $author = null; }
		if(strlen($q)>0){
			$qry.=" AND (instr(LOWER(body),'".lc($q)."')\n";
			$qry.=" OR instr(LOWER(title),'".lc($q)."'))\n";
			$getresults=1;
		}
		if($mo){
			$qry.=" AND (  month(date)='$mo' ) \n";
			$getresults=1;
		}
		if($day){
			$qry.=" AND ( dayofmonth(date)='$day'  )\n";
			$getresults=1;
		}
		if($year){
			$qry.=" AND (  year(date)='$year' )\n";
			$getresults=1;
		}
		if($author){
			$qry.="  AND ( LOWER(contributors.name)='".lc($author)."' OR LOWER(buzzbanter.author)='".lc($author)."' ) \n";
			$getresults=1;
		}


		$d="DESC";
		if(!$getresults){
			//$getresults=1;
			$qry.=" GROUP BY DATE_FORMAT(date,'%m/%d/%y') ";
			$datesonly=1;
		}

		$qry.=" ORDER BY date DESC LIMIT 50 ";

		$rows = exec_query($qry);

// BUZZ AND BANTER SEARCH IF USER IS LOGGED IN (won't be called if user not logged in!)
//		if ($USER->isAuthed) {
echo '<div id="heading">Search Results</div><div class="results">';
//echo $qry;
if($getresults){
		if(count($rows)==0){
			echo "Couldn't find anything based on your search terms.";
		} else {
			foreach ($rows as $row) {
				$anchor = 'bookmark' . $row['id'] . 't';
				$link = '<a class="bookAnchor" name="' . $anchor . '" id="' . $anchor . '" href="javascript:void(0);" onclick="launchBookmark(\'' . $row['id'] . '\', \'' . $anchor . '\',\'search\');return false">';
				echo '<p>';
				if (strlen($row[title])) {
					echo $link . ucwords($row[title]) . '</a><br />';
					echo '<a class="bookAnchor" href="javascript:void(0);" onclick="launchPage(\'' . $row[url] . '\');">' . date($DATE_STR,$row[udate]) . '</a><br/>';
				} else {
					//echo $link . date($DATE_STR,$row[udate]) . '</a><br />';
					echo '<a class="bookAnchor" href="javascript:void(0);" onclick="launchPage(\'' . $row[url] . '\');">' . date($DATE_STR,$row[udate]) . '</a><br/>';
				}
				if ($row[author] == "")
					echo 'By ' . ucwords($row[bbauthor]) . '<br />';
				else
					echo 'By ' . ucwords($row[author]) . '<br />';

				echo truncate(strip_tags($row[body]),300,'...') . '<br />';
				$anchor = 'bookmark' . $row['id'] . 'b';
				$link = '<a class="bookAnchor" name="' . $anchor . '" id="' . $anchor . '" href="javascript:void(0);" onclick="launchBookmark(\'' . $row['id'] . '\', \'' . $anchor . '\',\'search\');return false">';
				echo $link . 'view...</a>';
				echo '</p>';
				echo '<div class="articleDivider"></div>';
			}
		}
} elseif ($datesonly) {
	if(count($rows)==0){
		echo "Couldn't find anything based on your search terms.";
	} else {
		foreach ($rows as $row) {
		echo '<a class="bookAnchor" href="javascript:void(0);" onclick="launchPage(\'' . $row[url] . '\');">' . date($DATE_STR,$row[udate]) . '</a><br/>';
		}
	}
	//do something
}
echo '</div>';

?>

