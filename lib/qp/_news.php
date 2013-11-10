<?

	function get_full_qp_alerts($alert_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		if($_GET[AMADMIN])$approvedonly=0;
		if(!$alert_id)return array();
		$qry="select a.*, UNIX_TIMESTAMP(a.date)udate from qp_alerts a where a.type = 'alert' and a.id='$alert_id'";
		if($approvedonly)
			$qry.=" AND a.approved='1' ";
		$ret=exec_query($qry,1);
		if(count($ret)){
			if(!$ret[position])
				$ret[position]=$DEFAULT_NO_POS;
			$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[article_id]}";
		}
		return $ret;
	}
	
	function get_full_qp_blogs($alert_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		if($_GET[AMADMIN])$approvedonly=0;
		if(!$alert_id)return array();
		$qry="select a.*, UNIX_TIMESTAMP(a.date)udate from qp_alerts a where a.type = 'blogs' and a.id='$alert_id'";
		if($approvedonly)
			$qry.=" AND a.approved='1' ";
		$ret=exec_query($qry,1);
		if(count($ret)){
			if(!$ret[position])
				$ret[position]=$DEFAULT_NO_POS;
			$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[article_id]}";
		}
		return $ret;
	}
	
    function get_full_qp_video($video_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		//if($_GET[AMADMIN])$approvedonly=0;
		//if(!$video_id)return array();
		$qry="SELECT * from qp_mvtv where id='$video_id'";
		htmlprint_r($ret);
		if($approvedonly)
			$qry.=" AND a.approved='1'";
		$ret=exec_query($qry,1);
		
		//if(count($ret)){		   
			//$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			//$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[video_id]}";
		//}
		return $ret;
	}
?>
