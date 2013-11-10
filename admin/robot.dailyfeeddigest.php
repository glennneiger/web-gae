<?include_once("$DOCUMENT_ROOT/lib/_includes.php");
set_time_limit(3600);
//don't send on weekends
/*if(in_array(lc(date("D")),qw("sat sun")) ){
	exit();
}*/

/*$stQuery = "SELECT id,title,body,source,source_link,title_link,creation_date,updation_date from daily_feed where is_approved='1' and is_deleted='0' order by creation_date desc limit 10";*/

$stQuery = "SELECT id,title,body,title_link,creation_date,updation_date from daily_feed where is_approved='1' and is_live='1' and is_deleted='0' 
and DATE_FORMAT(publish_date,'%m/%d/%Y') = DATE_FORMAT('".mysqlNow()."','%m/%d/%Y') order by creation_date desc limit 10";
if(!count(exec_query($stQuery))){
	echo "No posts made today.";
	exit();
}



/*=============== write out mailing list for enabled users only=========================*/
//$listemail=$EMAIL_LISTS[daily_feed];
$listemail="/home/sites/minyanville/scripts/recv_daily_feed.list";

$daily_feed_category_id = '7';

$qryemail="select fname,lname,email from subscription S,email_alert_categorysubscribe EC where S.id=EC.subscriber_id
and EC.email_alert='1' and EC.subscriber_id in (select subscriber_id from email_alert_categorysubscribe where category_ids regexp ',$daily_feed_category_id,') order by email";

	$fp=fopen($listemail,"w+");
	$db=new dbObj($qryemail);
	while($row=$db->getRow()){
		//$email = "sudeer@minyanville.com";
		$mail="${row[fname]} ${row[lname]} <${row[email]}>\n";
		echo htmlentities("writing $mail")."<br>";
		fwrite($fp,$mail,4096);
	}



//set up email message file
$to=$NOTIFY_JOURNAL_TO;
$from=$NOTIFY_JOURNAL_FROM;

$latest_post_title =  "SELECT title from daily_feed where is_approved='1' and is_deleted='0' and is_live='1' order by publish_date desc,creation_date desc limit 1";
$title_qry = exec_query($latest_post_title,1);

$subject_title = $title_qry['title'];


$subject="The Daily Feed - ".date("m/d/y")." - ".$subject_title;



$msgfile="/tmp/".date("Ymd")."_df_digest.eml";
$msgurl="$HTPFX$HTADMINHOST/emails/_eml_daily_feed_digest.htm";
$msghtmlfile="$D_R/assets/data/".basename($msgfile);
//the file has already been created. assume it was sent also. NO SPAM!

//write out file
$mailbody=inc_web($msgurl);

//write out file as web page
write_file($msghtmlfile,$mailbody);

//write out file to email format
write_file($msgfile, mymail2str($to,$from,$subject,$mailbody));

//spam to everyone.
bulk_mailer($listemail,$msgfile,$from);
?>
