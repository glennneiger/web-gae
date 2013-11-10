<?php
include_once($D_R."/admin/lib/_article_data_lib.php");
include_once($D_R."/lib/config/_temp_gauge_config.php");
//require_once $D_R.'/lib/swift/lib/swift_required.php';
//$mailer = Swift_MailTransport::newInstance();
//$message = Swift_Message::newInstance();
set_time_limit(3600);

/*=============== write out mailing list for enabled users only=========================*/
$listemail=$EMAIL_LISTS[gazette];
if(!$is_dev){
	$qryemail="SELECT fname,lname,email FROM subscription WHERE recv_daily_gazette='1'";
	$fp=fopen($listemail,"w+");
	$db=new dbObj($qryemail);
	while($row=$db->getRow()){
		$mail="${row[fname]} ${row[lname]} <${row[email]}>\n";
		$email_listArr[$row['email']]= $row[fname]." ".$row[lname];
		echo htmlentities("writing $mail")."<br>";
		fwrite($fp,$mail,4096);
	}
}

//set up email message file
$to[$NOTIFY_JOURNAL_TO_EMAIL]=$NOTIFY_JOURNAL_TO_NAME;
$from[$NOTIFY_JOURNAL_FROM_EMAIL]=$NOTIFY_JOURNAL_FROM_NAME;
 $subject=html_entity_decode($_POST['subject']);
$msgfile="/tmp/".date("mdY")."_digest.eml";
 $msgurl=$HTPFX."minyanville:W5s46PZ261HSHt4@".$HTADMINHOST."/admin/temp-gauge/send_temp_gauge.php?id=".$_POST['id']."&mail=1";
$msghtmlfile="$D_R/assets/data/".basename($msgfile);
//the file has already been created. assume it was sent also. NO SPAM!
//write out file
$mailbody=inc_web($msgurl);

$message->setSubject($subject);
$message->setBody($mailbody, 'text/html');
$message->setFrom($from);

$failedRecipients = array();
	$numSent = 0;

foreach ($email_listArr as $address => $name)
{
  if (is_int($address)) {
    $message->setTo($name);
  } else {
  $message->setTo(array($address => $name));
  }
 // $numSent += $mailer->send($message, $failedRecipients);
 $numSent += mymail($to,$from,$subject,$mailbody);
}

//write out file as web page
write_file($msghtmlfile,$mailbody);
//write out file to email format
write_file($msgfile, mymail($to,$from,$subject,$mailbody) );


?>