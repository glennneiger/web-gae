<?

	function get_full_qp_alerts($alert_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		if($_GET[AMADMIN])$approvedonly=0;
		if(!$alert_id)return array();
		$qry="select a.*, a.date udate from qp_alerts a where a.type = 'alert' and a.id='$alert_id'";
		if($approvedonly)
			$qry.=" AND a.approved='1' ";
		$ret=exec_query($qry,1);
		if(count($ret)){
			if(!$ret[position])
				$ret[position]=$DEFAULT_NO_POS;
			$ret[datefmt]=date("m/d/Y g:ia",strtotime($ret['udate']));
			$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[article_id]}";
		}
		return $ret;
	}
	
	function get_full_qp_blogs($alert_id, $approvedonly=1){
		global $DEFAULT_NO_POS,$_GET,
				$JOURNAL_URL,$NEWSVIEWS_URL,$HTTP_HOST,$HTNOSSLDOMAIN;
		if($_GET[AMADMIN])$approvedonly=0;
		if(!$alert_id)return array();
		$qry="select a.*,a.date udate from qp_alerts a where a.type = 'blogs' and a.id='$alert_id'";
		if($approvedonly)
			$qry.=" AND a.approved='1' ";
		$ret=exec_query($qry,1);
		if(count($ret)){
			if(!$ret[position])
				$ret[position]=$DEFAULT_NO_POS;
			$ret[datefmt]=date("m/d/Y g:ia",strtotime($ret['udate']));
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
		if($approvedonly)
			$qry.=" AND a.approved='1'";
		$ret=exec_query($qry,1);
		
		//if(count($ret)){		   
			//$ret[datefmt]=date("m/d/Y g:ia",$ret[udate]);
			//$ret[article_url]="$HTNOSSLDOMAIN${ret[url]}?id=${ret[video_id]}";
		//}
		return $ret;
	}
	
	function prepare_subs_list()
	{
		 
		$qry="select email from subscription_ps where account_status='enabled' and (trial_status<>'inactive' || trial_status is null) and (prof_id = '3' || combo_id = '2') order by email";
		
		$db=new dbObj($qry);

		while($row=$db->getRow())
		{
			if($to=='')
			{
				$to	= $row['email'];
			}
			else
			{
				$to = $to.",".$row['email'];
			}
		}

		if($to!="")  // send mail only if $to has email addresses
		{
			$listfile="/tmp/Quint_allsubs.{$prof_id}.spam_".mrand().".list";
					  $mylist=explode(",",$to);

				  	   foreach($mylist as $i=>$v)
						 {
							if(!($mylist[$i]=trim($v)))
							{
								unset($mylist[$i]);
								continue;
							}
						}
						 $mylist=implode("\n",$mylist);
						 write_file($listfile,$mylist);
			            unset($mylist);
        }
		return($listfile);	
	}
?>
