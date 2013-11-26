<?

 function send_approved_article_mail($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$contrib_id,$category_ids,$subsectionid,$size_emailalert)
   {
    	global $HTPFX,$HTHOST,$D_R;
    	$interest='';
    	$category_id = explode(",", $category_ids);
    	if(in_array("5",$category_id))
    	{
    		$interest[]="Trading Radar";
    	}

    	$sec_id = explode(',',$subsectionid);
		$sec_id = array_filter($sec_id);
		$sec_id = implode("','",$sec_id);
		$sqlSec = "SELECT `name` FROM section WHERE is_active='1' AND TYPE='subsection' AND subsection_type='article'
		 AND section_id IN ('".$sec_id."') ";
		$userSecResult=exec_query($sqlSec);
		foreach($userSecResult as $k=>$v)
		{
			$interest[] = $v['name'];
		}
		$interest = implode(',',$interest);
		
		$msgfile="/tmp/spam_".mrand().".eml";   // mrand function in _misc.php give getmicrotime();
		$msghtmlfile="$D_R/assets/data/".basename($msgfile);
		$msgurl=$SPAM_EML_SUBS_ALERT_TMPL."?aid=$aid";   // $SPAM_EML_SUBS_ALERT_TMPL in lib/_exchange_config.php
		//write out file
		$mailbody=inc_web($msgurl);
		sendTopicMailChimp($atitle,$mailbody, $interest);
		sendAuthorMail($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$contrib_id,$category_ids,$subsectionid,$size_emailalert);
		
   }
   
   function sendAuthorMail($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$contrib_id,$category_ids,$subsectionid,$size_emailalert)
   {
		global $HTPFX,$HTHOST,$D_R,$SPAM_EML_SUBS_ALERT_TMPL;

		$category_ids="5";	
		$contribStr=" FIND_IN_SET($contrib_id,author_id)";
		
		$categoryid=explode(",",$category_ids);
		if(count($categoryid)==1){
			$catStr=" FIND_IN_SET($categoryid[0],category_ids)";
		}else{
		    $countCat=count($categoryid);
			foreach($categoryid as $keyCat=>$catrow){
				$catStr.=" FIND_IN_SET($catrow,category_ids)";
				if($keyCat!=$countCat-1){
					$catStr.=" or";
				}
			}
		}

		$subsection_id=explode(",",$subsectionid);
		if(count($subsection_id)==1){
			$subsectionStr=" FIND_IN_SET($subsection_id[0],section_ids)";
		}else{
		    $countSec=count($subsection_id);
		    foreach($subsection_id as $keySec=>$secVal){
		    	
				$subsectionStr.=" FIND_IN_SET($secVal,section_ids)";
				if($keySec!=$countSec-1){
					$subsectionStr.=" or";
				}
			}
		}

		$qry="select S.email from subscription S,email_alert_authorsubscribe EA where S.id=EA.subscriber_id
		 and EA.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and EA.subscriber_id in
		  (select subscriber_id from email_alert_authorsubscribe where ".$contribStr.")";
	
		if($categoryid[0]!=""){
		$qry .=" AND EA.subscriber_id NOT IN(
		select S.id from subscription S,email_alert_categorysubscribe EC where S.id=EC.subscriber_id 
		and EC.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and EC.subscriber_id in 
		(select subscriber_id from email_alert_categorysubscribe where ".$catStr."))";
		}
		if($subsection_id[0]!=""){
		$qry .=" AND EA.subscriber_id NOT IN( select S.id from subscription S,email_alert_sectionsubscribe ES where S.id=ES.subscriber_id 
		and ES.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and ES.subscriber_id in 
		(select subscriber_id from email_alert_sectionsubscribe where ".$subsectionStr."))";
		}

	   	$emaildata=exec_query($qry);
   		foreach($emaildata as $key=>$row)
   		{
				$to[] = $row['email'];	   		
	   	}
	   	if(!$aid)
			return;
	   	$to = implode(',',$to);    
		$from= $NOTIFY_JOURNAL_FROM;  // mail to Minyanville Mailing List <support@minyanville.com>
		$subject=$atitle;
		$msgfile="/tmp/spam_".mrand().".eml";   // mrand function in _misc.php give getmicrotime();
		$msghtmlfile="$D_R/assets/data/".basename($msgfile);
		$msgurl=$SPAM_EML_SUBS_ALERT_TMPL."?aid=$aid";   // $SPAM_EML_SUBS_ALERT_TMPL in lib/_exchange_config.php
		//write out file
		$mailbody=inc_web($msgurl);
		mymail($to,$from,$subject,$mailbody);
   }
   function send_approved_article_mail_old($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$contrib_id,$category_ids,$subsectionid,$size_emailalert)
   {
    global $HTPFX,$HTHOST,$D_R;

	$contribStr=" FIND_IN_SET($contrib_id,author_id)";
		$categoryid=explode(",",$category_ids);
		if(count($categoryid)==1){
			$catStr=" FIND_IN_SET($categoryid[0],category_ids)";
		}else{
		    $countCat=count($categoryid);
			foreach($categoryid as $keyCat=>$catrow){
				$catStr.=" FIND_IN_SET($catrow,category_ids)";
				if($keyCat!=$countCat-1){
					$catStr.=" or";
				}
			}
		}

		$subsection_id=explode(",",$subsectionid);
		if(count($subsection_id)==1){
			$subsectionStr=" FIND_IN_SET($subsection_id[0],section_ids)";
		}else{
		    $countSec=count($subsection_id);
		    foreach($subsection_id as $keySec=>$secVal){
		    	
				$subsectionStr.=" FIND_IN_SET($secVal,section_ids)";
				if($keySec!=$countSec-1){
					$subsectionStr.=" or";
				}
			}
		}


	$qry="select S.id, S.email from subscription S,email_alert_authorsubscribe EA where S.id=EA.subscriber_id
	 and EA.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and EA.subscriber_id in
	  (select subscriber_id from email_alert_authorsubscribe where ".$contribStr.")";

if($categoryid[0]!=""){
$qry .=" union
select S.id, S.email from subscription S,email_alert_categorysubscribe EC where S.id=EC.subscriber_id 
and EC.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and EC.subscriber_id in 
(select subscriber_id from email_alert_categorysubscribe where ".$catStr.")";
}
if($subsection_id[0]!=""){
$qry .=" union select S.id, S.email from subscription S,email_alert_sectionsubscribe ES where S.id=ES.subscriber_id 
and ES.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and ES.subscriber_id in 
(select subscriber_id from email_alert_sectionsubscribe where ".$subsectionStr.")";
}

$emaildata=exec_query($qry);
$numrows=count($emaildata);
//$emailsize=ceil($numrows/$size_emailalert);
$leftrows=$numrows;
$size=$size_emailalert;
		foreach($emaildata as $key=>$row){
			  if($to==''){
				$to	= $row['email'];
			  }else{
				$to = $to.",".$row['email'];
			  }
	         // file size > no. of emails in the file list and no. of records left < no. of emails in the file list
			 if(($numrows>$size_emailalert) && ($key==$size-1) && ($leftrows>$size_emailalert)){
				$listfile=emaillist($to);
				$leftrows=$numrows-$key;
				$size=$size + $size_emailalert;
				callbulkmailer($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$listfile);
				$to='';
			  }
			  // file size > no. of emails in the file list and no. of records left < no. of emails in the file list
			  if(($numrows>$size_emailalert) && ($key==$numrows-1) && ($leftrows<=$size_emailalert)){
				$listfile=emaillist($to);
				callbulkmailer($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$listfile);
				$to='';
			  }
			  // file size < no. of emails in the file list
			  if(($numrows<=$size_emailalert) && ($key==$numrows-1)){
				$listfile=emaillist($to);
				callbulkmailer($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$listfile);
			  }
		 }

   }

function emaillist($to){
	if($to!="")  // send mail only if $to has email addresses
		{
			$listfile="/tmp/email_alert.spam_".mrand().".list";

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

    function callbulkmailer($NOTIFY_JOURNAL_TO,$NOTIFY_JOURNAL_FROM,$atitle,$SPAM_EML_SUBS_ALERT_TMPL,$aid,$listfile){
		global $D_R;
		if(!$aid)
			return;
		$to=$NOTIFY_JOURNAL_TO;      // mail to support@minyanville.com
		$from= $NOTIFY_JOURNAL_FROM;  // mail to Minyanville Mailing List <support@minyanville.com>
		$subject=$atitle;
		$msgfile="/tmp/spam_".mrand().".eml";   // mrand function in _misc.php give getmicrotime();
		$msghtmlfile="$D_R/assets/data/".basename($msgfile);
		$msgurl=$SPAM_EML_SUBS_ALERT_TMPL."?aid=$aid";   // $SPAM_EML_SUBS_ALERT_TMPL in lib/_exchange_config.php
		//write out file
		$mailbody=inc_web($msgurl);
		//write out file as web page
		write_file($msghtmlfile,$mailbody);
		write_file($msgfile,mymail2str($to,$from,$subject,$mailbody));
		/*------send message------------------*/
		bulk_mailer($listfile,$msgfile,$from);
	}

	function displayDateun() {
		$dateInfo = getDate();
		$todaysDate =$dateInfo['month'] . " " . $dateInfo['mday'] . ", " . $dateInfo['year'];
		return $todaysDate;
	}

	function makearticlelink($id) {
	       global $HTPFX,$HTHOST,$D_R;
    		$link=$HTPFX.$HTHOST."/articles/index.php?a=".$id;
			return $link;
	}


	function getproduct($email) {
		$qry="select id,prof_id,combo,combo_id,is_buzz from subscription_ps where  email='$email' order by prof_id desc";
		$subs_ps=exec_query($qry);
        $subqry="select id from subscription where   (type='prem' or type='trial' or type='newyear') and email='$email'";
		$subbuzz=exec_query($subqry,1);
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
			   if($subbuzz) {
			   		$buzz=1;
			   }else {
			   		$buzz=0;
			   }
			   $quint=0;
			   $productdetails['buzz']=$buzz;
			   $productdetails['coper']=$corp;
			   $productdetails['quint']=$quint;
			}elseif($sub_ps['prof_id']==3 && $sub_ps['combo']==0 && $sub_ps['combo_id']==0){
			   if($subbuzz) {
			   		$buzz=1;
			   }else {
			   		$buzz=0;
			   }
			   $corp=0;
			   $productdetails['buzz']=$buzz;
			   $productdetails['coper']=$corp;
			   $productdetails['quint']=$quint;
			}
			return $productdetails;
		}elseif($subs_ps[0] && $subs_ps[1]) {
		  if($subps['prof_id']==0 && $subps['combo']==1 && $subps['combo_id']==1 && $subps['is_buzz']==1){

				$productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
			}elseif($subps['prof_id']==0 && $subps['combo']==1 && $subps['combo_id']==2 && $subps['is_buzz']==1){
			    $productdetails['buzz']=$buzz;
				$productdetails['coper']=$corp;
				$productdetails['quint']=$quint;
			}elseif($sub_ps['prof_id']==2 && $sub_ps['combo']==0 && $sub_ps['combo_id']==0){
			   $buzz=0;
			   $quint=0;
			   $productdetails['buzz']=$buzz;
			   $productdetails['coper']=$corp;
			   $productdetails['quint']=$quint;
			}
		  if($sub_ps['prof_id']==3 && $subps['prof_id']==2){
				$subqry="select id from subscription where   premium='1' and email='$email'";
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
		    $subqry="select id from subscription where   premium='1' and email='$email'";
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


function getContributorsArray($authorstr="",$authornotstr=""){
		$config_arr=array("Todd Harrison");
		if(!$authorstr){
			$authorid=$authornotstr;
		}else {
            $strauth=explode(",",$authorstr);
			foreach($strauth as $key=>$value){
			   if($value==1){
			     $matchkey=$key;
			   }
			}
			if (array_key_exists($matchkey, $strauth)) {
			   $my_key=0;
			}
			$authorid=$authorstr.','.$authornotstr;
		}

		$sql_authors="select id,name,SUBSTRING_INDEX(name,' ',-1)`lname` from contributors where id not in($authorid) and suspended= 0 order by lname";
		$getauthors_arr=exec_query($sql_authors);
		$displayarr=array();
		foreach($getauthors_arr as $key=>$value){
		$displayarr[$value['id']]=$value['name'];
		}

		$keyarra=array();
		for($i=0;$i<count($config_arr);$i++){
			$key=array_search($config_arr[$i],$displayarr);
			$keyarra[$key]=$config_arr[$i];
		}
		$result_authors=$keyarra+$displayarr;


		$getauthors_arr_filterd=array();
		$l=0;
		foreach($result_authors as $ket=>$vat){
		$getauthors_arr_filterd[$l]['id']=$ket;
		$getauthors_arr_filterd[$l]['name']=$vat;
		$l++;
		}

		if(array_key_exists($my_key, $getauthors_arr_filterd)) {
        unset($getauthors_arr_filterd[$my_key]);
   		 }
		return $getauthors_arr_filterd;
}

function send_unsubscribe_mail($email) {
	   global $HTHOST, $HTPFX, $HTADMINHOST, $REG_EML_REPLYTO;
	   $userObj = new user();

	       $result_subs=exec_query("select id, fname, lname from subscription where email='$email'",1);
		   $id = md5($result_subs['id']);
		   $subject="Minyanville Newsletter Unsubscription";
		   $from=$REG_EML_REPLYTO;
		   $UNSUBSCRIBE_EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_unsubscribe_confirmation.htm";
		   mymail($email,$from,$subject,inc_web("$UNSUBSCRIBE_EML_TMPL?unsub_id=$id"));
		   $tech_subject = $email." Unsubscribed - for FOLLOWUP";
		   mymail('mvil@ebusinessware.com',$from,$tech_subject,inc_web("$UNSUBSCRIBE_EML_TMPL?unsub_id=$id"));
   }

function unSubscribe($email) {
	$result_subs=exec_query("select id, fname, lname from subscription where email='$email'",1);
	$sub_id = $result_subs['id'];

	$arrsubscription = array('email_alert'=>0);
	update_query("email_alert_authorsubscribe",$arrsubscription,array('subscriber_id'=>$sub_id));
	update_query("email_alert_sectionsubscribe",$arrsubscription,array('subscriber_id'=>$sub_id));
	update_query("email_alert_categorysubscribe",$arrsubscription,array('subscriber_id'=>$sub_id));

	$arrsubscription = array('recv_daily_journal'=>0,'recv_daily_gazette'=>0,'recv_promo'=>0);
	update_query("subscription",$arrsubscription,array('id'=>$sub_id));
  }

  function send_scottrade_buzz_welcome_email($id) {
	global $HTHOST, $HTPFX, $HTADMINHOST, $REG_EML_REPLYTO;
		   $userObj = new user();

		       $result_subs=exec_query("select email from subscription where id='$id'",1);
			   $user_email = $result_subs['email'];
			   $subject="Welcome to Scottrade Buzz & Banter";
			   $from=$REG_EML_REPLYTO;
			   $EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_scottrade_buzz_welcome.htm";
		   mymail($user_email,$from,$subject,inc_web("$EML_TMPL?userid=$id"));
  }

  function send_schwab_buzz_welcome_email($id) {
  	global $HTHOST, $HTPFX, $HTADMINHOST, $REG_EML_REPLYTO;

  		       $result_subs=exec_query("SELECT * FROM sub_leads WHERE id='".$id."'",1);
  			   $user_email = $result_subs['email'];
  			   $subject="Special Offer from Minyanville and Charles Schwab";
  			   $from=$REG_EML_REPLYTO;
  			$EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_schwab_welcome.htm";
  		   mymail($user_email,$from,$subject,inc_web("$EML_TMPL?name=".$result_subs['fname']." ".$result_subs['lname']));
  }
?>
