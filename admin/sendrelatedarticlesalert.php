<?php
include_once("$DOCUMENT_ROOT/lib/_includes.php");
global $LATESTARTICLEJSSCRIPT, $MODERATOR_EMAIL, $timeinterval;
$handle=fopen($LATESTARTICLEJSSCRIPT,"r");
$read=fread($handle,64);
$newArticleQuery  = "select S.id subId, ET.tag tag, A.id articleId, A.title articletitle from articles A, ex_item_tags EIT,ex_tags ET,ex_stock
					ES,ex_user_stockwatchlist
					EUW,subscription S where
					A.id=EIT.item_id and
					EIT.item_type=1 and
					EIT.tag_id=ET.id and
					ET.type_id=1 and
					ET.tag=ES.stocksymbol and
					ES.id=EUW.stock_id and
					EUW.subscription_id=S.id";
if($read){
	$newArticleQuery.=" and A.id > $read group by subid,articleid desc  order by A.id Desc ";
}
else
{
	$newArticleQuery.=" and A.date > '".mysqlNow()."' - INTERVAL $timeinterval group by subid,articleid  order by A.id Desc";
}

$newArticle="";
foreach(exec_query($newArticleQuery) as $row)
{
	if($newArticle==""){
   		$newArticle.= $row['articleId'];
		$latestArticle = $newArticle;
		$subscriber.= $row['subId'];
		//Added by samir
		$newArticletitle.= $row['articletitle'];
		$newArticletag.= $row['tag'];
	
	}
	else{
		$newArticle.= ",".$row['articleId'];
		//$subscriber.= "','".$row['subId'];
		//Added by samir
		$subscriber.= ",".$row['subId'];
		$newArticletitle.= ",".$row['articletitle'];
		$newArticletag.= ",".$row['tag'];
	}
}
//$subscriber="('".$subscriber."')";
if(num_rows($newArticleQuery)>0){
	$fFile=fopen($LATESTARTICLEJSSCRIPT,"w+");
	fwrite($fFile,$latestArticle);
	fclose($fFile);
}
/*=============== write out mailing list for related users only=========================*/
$subscriberarray=explode(",",$subscriber);
$newArticletitlearray=explode(",",$newArticletitle);
$newArticlearray=explode(",",$newArticle);
$latestarticlecnt=count($subscriberarray);
for($i=0;$i<$latestarticlecnt;$i++)
{
	$watchlist="";
	$watchlistqry="select S.id,EUW.stock_id,ES.stocksymbol from ex_stock ES,ex_user_stockwatchlist EUW,subscription S
where ES.id=EUW.stock_id and EUW.subscription_id=S.id and S.id='$subscriberarray[$i]'";
	if(num_rows($watchlistqry)>0)
	{
		foreach(exec_query($watchlistqry) as $rowwatchlistqry)
		{
			if($watchlist==""){
   				$watchlist.= $rowwatchlistqry['stocksymbol'];
			}
			else
			{
				$watchlist.= ",".$rowwatchlistqry['stocksymbol'];
			}
		}
	}
	$watchlistarray=explode(",",$watchlist);
	$articletaglist="";
	$articletaglistqry="select ET.tag tag, A.id articleId, A.title articletitle from articles A, ex_item_tags EIT,ex_tags ET
where A.id=EIT.item_id and EIT.item_type=1 and EIT.tag_id=ET.id and ET.type_id=1 and A.id='$newArticlearray[$i]'";
	if(num_rows($articletaglistqry)>0)
	{
		foreach(exec_query($articletaglistqry) as $rowarticletaglistqry)
		{
			if($articletaglist==""){
   				$articletaglist.= $rowarticletaglistqry['tag'];
			}
			else
			{
				$articletaglist.= ",".$rowarticletaglistqry['tag'];
			}
		}
	}
	$articletaglistarray=explode(",",$articletaglist);
	$mailtaglist="";
	for($j=0;$j<count($watchlistarray);$j++)
	{
		if(in_array($watchlistarray[$j],$articletaglistarray))
		{
			if($mailtaglist=="")
			{
				$mailtaglist.= $watchlistarray[$j];			
			}
			else
			{
				if(!in_array($watchlistarray[j],$mailtaglist))
				{			
					$mailtaglist.= ",".$watchlistarray[$j];
				}
			}
		}
	}
	$Userqry="select S.id, fname, lname, email from subscription S, ex_user_email_settings EUES where S.id = EUES.subscription_id and	EUES.alert = 'ON' and EUES.email_id = 5 and S.id='$subscriberarray[$i]' order by fname,lname";
	if(num_rows($Userqry)>0){
		foreach(exec_query($Userqry) as $row)
		{
			$to=$row['id'];
			$event = 'NewArticlesRelatedWatchlist';
			$resEmail=getAlertStatus($event, $to);
			//$emailBody=getAlertBody($event, NULL, $to, NULL, NULL, NULL, NULL, NULL,$newArticletitlearray[$i],$watchlistarray[$i]);
			$sendEmail=sendAlert($to, NULL, $resEmail, $event, NULL, NULL, NULL, NULL,$newArticletitlearray[$i],$mailtaglist);
		}
	}
}
/*=============== write out mailing list for related users only=========================*/
/*$Userqry="select S.id, fname, lname, email from subscription S, ex_user_email_settings EUES where S.id = EUES.subscription_id and
			EUES.alert = 'ON' and EUES.email_id = 5 and S.id in $subscriber order by fname,lname";
if(num_rows($Userqry)>0){
	foreach(exec_query($Userqry) as $row)
	{
		$to=$row['id'];
		$event = 'NewArticlesRelatedWatchlist';
		$resEmail=getAlertStatus($event, $to);
		$emailBody=getAlertBody($event, NULL, $to, NULL, NULL, NULL, NULL, NULL);
		$sendEmail=sendAlert($to, NULL, $resEmail, $event, NULL, NULL, NULL, NULL);
	}
}*/
?>
