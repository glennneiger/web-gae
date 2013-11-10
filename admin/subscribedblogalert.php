<?php
include_once("$DOCUMENT_ROOT/lib/_includes.php");

global $SUBSCRIBEDBLOGSJSSCRIPT, $MODERATOR_EMAIL, $timeinterval;

$handle=fopen($SUBSCRIBEDBLOGSJSSCRIPT,"r");
$read=fread($handle,64);
echo $read;
	$subscribedBlogsQuery = "select S.id subId, ET.title, ET.id blogId, ET.author_id from subscription S, ex_blog_subscribed EBS,
							 ex_thread ET where S.id=user_id and EBS.subscribed_to=ET.author_id and ET.is_user_blog=1";

		if($read){
			$subscribedBlogsQuery.=" and ET.id > $read order by ET.id desc";
			}
		else
		{
			$subscribedBlogsQuery.=" and ET.created_on > '".mysqlNow()."' - INTERVAL $timeinterval;";
		}

$newBlog="";
foreach(exec_query($subscribedBlogsQuery) as $row)
{
	if($newBlog==""){
   		$newBlog.= $row['blogId'];
		$latestBlog = $newBlog;
		$subscriber.= $row['subId'];
	}
	else{
		$newBlog.= "','".$row['blogId'];
		$subscriber.= "','".$row['subId'];
	}
}

$subscriber="('".$subscriber."')";

if(num_rows($subscribedBlogsQuery)>0){
$fFile=fopen($SUBSCRIBEDBLOGSJSSCRIPT,"w+");
fwrite($fFile,$latestBlog);
fclose($fFile);
}

/*=============== write out mailing list for related users only=========================*/
/*$SCRIPT_BLOG_DIR="/home/sites/minyanville/scripts";*/

$EMAIL_LIST=array(
			"article" => "$D_R/assets/data/recv_daily_blogs.list");

$list=$EMAIL_LIST[article];
	$Userqry="select fname,lname,email from subscription S, ex_user_email_settings EUES where S.id = EUES.subscription_id and
			EUES.alert = 'ON' and EUES.email_id = 6 and S.id in $subscriber order by fname,lname";

	if(num_rows($Userqry)>0){
	$fp=fopen($list,"w+");
	$db=new dbObj($Userqry);
	while($row=$db->getRow()){
		$mail="${row[fname]} ${row[lname]} <${row[email]}>\n";
		echo htmlentities("writing $mail")."<br>";
		fwrite($fp,$mail,4096);
	}

//set up email message file
$NOTIFY_ARTICLE_TMPL="$HTNOSSLDOMAIN/emails/_eml_post_notify.htm";
$NOTIFY_ARTICLE_FROM="Minyanville <support@minyanville.com>";
$NOTIFY_ARTICLE_SUBJECT="New Blog on Minyanville.com";
$to=$MODERATOR_EMAIL;
$from=$NOTIFY_ARTICLE_FROM;

/*$event=NewArticlesRelatedWatchlist;
$emailBody=getAlertBody($event);*/

$subject="The Exchange: People you have subscribed to, have updated their blog.";
$msgfile="/tmp/".date("mdY").rand()."_blog.eml";
$msgurl=$HTPFX.$HTHOST."/emails/_eml_blogs_alert.htm";
$msghtmlfile="$D_R/assets/data/".basename($msgfile);

//the file has already been created. assume it was sent also. NO SPAM!
if(is_file($msgfile) || is_file($msghtmlfile) ){
	echo "<br><b>FATAL ERROR! </b>$msgfile has already been sent! No spam.";
	exit();
}

//write out file
$mailbody=inc_web($msgurl);

//write out file as web page
write_file($msghtmlfile,$mailbody);

//write out file to email format
write_file($msgfile, mymail2str($to,$from,$subject,$mailbody) );


//spam to everyone.
//bulk_mailer($list,$msgfile,$from);
}

?>



