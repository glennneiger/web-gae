<?

	function get_full_article($article_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$ARTICLE_QUERY,$HTNOSSLDOMAIN,$ARTICLE_QUERY_EMAIL;


		if($_GET[AMADMIN])$approvedonly=0;
		if(!$article_id)return array();
			$qry="$ARTICLE_QUERY AND a.id='$article_id'";

		if($qrySubsection['email_category_ids']){
			$qry="$ARTICLE_QUERY_EMAIL AND a.id='$article_id'";
		}
		if($approvedonly)
			$qry.=" AND a.approved='1' and a.is_live='1'";


		$ret=exec_query($qry,1);
		if(count($ret)){
				if(!$ret[position])
					$ret[position]=$DEFAULT_NO_POS;
				$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
				$ret[article_url]=$HTNOSSLDOMAIN.makeArticleslink($ret['article_id'],$ret['keyword']);

			return $ret;
		}
	}

	function get_full_cp_article($article_id, $approvedonly=1){
			global $DEFAULT_NO_POS,$_GET,
					$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$ARTICLE_QUERY,$HTNOSSLDOMAIN;
			if($_GET[AMADMIN])$approvedonly=0;
			if(!$article_id)return array();
			$qry="select * from cp_articles CA where CA.id='$article_id'";
			if($approvedonly)
				$qry.=" AND CA.approved='1' and CA.is_live='1'";

			$ret=exec_query($qry,1);
			if(count($ret)){
				if(!$ret[position])
					$ret[position]=$DEFAULT_NO_POS;
				$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
				$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[article_id]}";
		}
		return $ret;
	}

	function get_full_article_body($article_id){
		$qry="select body,page_no,updated_date from article_revision where article_id='$article_id' and revision_number=(select max(revision_number) from article_revision where article_id='$article_id') order by page_no";
		$qrydata=exec_query($qry);
		foreach($qrydata as $key=>$val){
			$str = preg_replace('/<[^<|>]+?>/', '', $qrydata[$key]['body']);
		    $str = htmlentities($str, ENT_QUOTES, "UTF-8");
			$qrydata[$key]['bodyCount'] = str_word_count($qrydata[$key]['body']);
		}
		if($qrydata){
		  return $qrydata;
		}

	}

    function get_full_video($video_id,$approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		if($_GET[AMADMIN])$approvedonly=0;
		if(!$video_id)return array();
		$qry="SELECT * from mvtv where id='$video_id'";
		if($approvedonly)
			$qry.=" AND a.approved='1'";
		$ret=exec_query($qry,1);
		//if(count($ret)){
			//$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			//$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[video_id]}";
		//}
		return $ret;
	}
	function get_full_slideshow($slide_id, $approvedonly=1){
			global $_GET,
					$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
			if($_GET[AMADMIN])$approvedonly=0;
			if(!$slide_id)return array();
			$qry="SELECT s.*,sc.slideshow_id,sc.slide_title,sc.body
		     FROM
				 slideshow s,slideshow_content sc,contributors c
		     WHERE
				 sc.slideshow_id=s.id and
			     s.contrib_id=c.id ";
			$qry.=" AND s.id='$slide_id'";
			if($approvedonly)
				$qry.=" AND s.approved='1'";
			$ret=exec_query($qry,1);
			if(count($ret)){
				$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
				$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[id]}";
			}
			return $ret;
		}

	function get_full_slideshowcontent($slide_id, $approvedonly=1){
			global $_GET,
					$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$SLIDESHOWCONT_QUERY,$HTNOSSLDOMAIN;
			if($_GET[AMADMIN])$approvedonly=0;
			if(!$slide_id)return array();
			$qry = "SELECT s.id,sc.id,sc.slideshow_id,sc.slide_title,sc.body,sc.slide_image
		     FROM
				 slideshow s,slideshow_content sc,contributors c
		     WHERE
				 sc.slideshow_id=s.id and
			     s.contrib_id=c.id ";
			$qry.=" AND s.id='$slide_id'";
			if($approvedonly)
				$qry.=" AND s.approved='1'";
				$qry.="Order by slide_no";
			$ret=exec_query($qry);
			if(count($ret)){
				$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			}
			return $ret;
		}




	function get_full_thisweeks_articles($category_id=0,$approvedonly=1){
		global $_GET,$ARTICLE_QUERY,$ARTICLE_TABLE;
		global $HTNOSSLDOMAIN,$DEFAULT_NO_POS,$s;
		if($_GET[AMADMIN])$approvedonly=0;
		//GET the last day of articles written
		$params=array("");
		if($approvedonly)
			$params[]="approved='1' and is_live='1'";
		if($category_id)
			$params[]="find_in_set('$category_id',category_ids)";
		$qry="SELECT DATE_FORMAT(date,'%m/%Y %u')monthweek FROM $ARTICLE_TABLE a
				  WHERE 1 ".implode(" AND ",$params)." ORDER BY a.id DESC";
				  //don't order by `date` alias but the real date col a.date!
		$lastweek=exec_query($qry,1,"monthweek");
		//Using the last day get all the articles from that day
		//remove the first empty item from params
		array_shift($params);
		$params[]="DATE_FORMAT(date,'%m/%Y %u')='$lastweek'";
		$qry="$ARTICLE_QUERY AND ".implode(" AND ",$params);
		$qry.=" GROUP BY a.id ORDER BY a.date ASC";
		$ret=exec_query($qry);
		foreach($ret as $i=>$row){
			if(!$row[position])
				$row[position]=$DEFAULT_NO_POS;
			$row[datefmt]=date("m/d/Y g:ia",$row[udate]);
			$row[article_url]="$HTNOSSLDOMAIN${row[url]}?id=${row[article_id]}";
			$ret[$i]=$row;
		}
		return $ret;
	}
	
	function get_category_id($category_name){
		$category_name=lc($category_name);
		$qry="SELECT id FROM article_categories WHERE lower(name)='$category_name'";
		return exec_query($qry,1,"id");
	}
	function get_category($category_name){
		$category_name=lc($category_name);
		$qry="SELECT * FROM article_categories WHERE lower(name)='$category_name'";
		return exec_query($qry,1);
	}
	function get_all_categories(){
		$qry="SELECT * FROM article_categories ORDER by id";
		return exec_query($qry);
	}

	function get_article_subsections(){
		$qry="select * from section where subsection_type = 'article' and type='subsection' and is_active='1' order by name ";
		return exec_query($qry);
	}
	function get_all_email_categories(){
		$qry="select id,title from email_categories where visible='1' order by id";
		return exec_query($qry);
	}

	function get_category_title($id){
		$category_name=lc($category_name);
		$qry="select title from article_categories where id in (".$id.")";
		return exec_query($qry);
	}

	function getContribuotrDetails($name){
		$sqlGetContributor="select id,name from contributors where name = '$name'";
		$resGetContributor=exec_query($sqlGetContributor,1);
		if(isset($resGetContributor)){
			return $resGetContributor;
		}else {
			return NULL;
		}
	}
	/*ETF functions*/
	function get_full_etf_article($article_id, $approvedonly=1){
			global $DEFAULT_NO_POS,$HTTP_HOST,$HTNOSSLDOMAIN;
			if($_GET[AMADMIN])$approvedonly=0;
			if(!$article_id)return array();
			$qry="select * from etf_articles ETF where ETF.id='$article_id'";
			if($approvedonly)
				$qry.=" AND ETF.approved='1' and ETF.is_live='1'";
			$ret=exec_query($qry,1);
			if(count($ret)){
				if(!$ret[position])
					$ret[position]=$DEFAULT_NO_POS;
				$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
				$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[article_id]}";
		}
		return $ret;
	}
?>
