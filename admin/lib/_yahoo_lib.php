<?php
class YahooSyndication{

function generateYahooXml($id,$recentArticlerow,$item_text,$syndchannel){
	$issynd=1;
	$minyanhosted=1;
	$date =	$recentArticlerow['publish_date'];
	$uid = gmdate("Ymd",strtotime($date));
	$uid = $uid.$id.'dailyfeed';
	$syndchannel = 'yahoo';
	$item_text = 'Feeds';
	insertSyndication($id,$item_text,$syndchannel,$issynd,$minyanhosted);
	echo "Feed for Article id :".$uid." has generated<br>";
}
	
	
function generateYahooXml_old($id,$recentArticlerow,$item_text,$syndchannel){
	global $HTNOSSLDOMAIN,$D_R;
	include_once("$D_R/lib/_content_data_lib.php");
	$objDailyfeed	=	new Dailyfeed(18,$id);
	$objContent		= 	new Content();
	$objCache =  new Cache();
	global $HTPFX,$HTHOST;
	$title				=	htmlentities(stripslashes($recentArticlerow['title']), ENT_QUOTES);
	$stock_tickers		=	$objDailyfeed->getTickersExchange($id,'18');
	$date				=	$recentArticlerow['publish_date'];

	$urlName = $objContent->getDailyFeedURL($id);
	$url 				=   $HTNOSSLDOMAIN.$urlName."?camp=syndication&amp;medium=portals&amp;from=yahoo";
	$url				=	str_replace("https:","http:",$url);
	if($recentArticlerow['excerpt'])
 	{
		$description	=	trim($recentArticlerow['excerpt']);
	}
	else
	{
	    $description 	= 	substr(trim(strip_tags($recentArticlerow['body'])),0,400);
	}
	$description	=	htmlentities($description, ENT_QUOTES);
	$authorID		=	$recentArticlerow['contrib_id'];
	$authorName		=	$objDailyfeed->getAuthor($authorID);
	$author			=	htmlentities(stripslashes($authorName), ENT_QUOTES);
	$uid			=	gmdate("Ymd",strtotime($date));
	$uid			=	$uid.$id.'dailyfeed';
	$item_text		=	'Feeds';

	$body = $objCache->buzzAdReplace($recentArticlerow['body']);
	$body = strip_tag_style($body);
	$body = htmlentities($body,ENT_QUOTES);
	$regex = '/<object(.*?)<\/object>/i';
	$body = preg_replace($regex, '', $body);
	$body = str_replace('<strong','<b',$body);
	$body = str_replace('</strong>','</b>',$body);
	$body = str_replace('<em','<i',$body);
	$body = str_replace('</em>','</i>',$body);
	if(substr_count($body,"{FLIKE}") > 0)
	{
		$body = str_replace("{FLIKE}","", $body);
	}
	$body = utf8_encode(mswordReplaceSpecialChars($body));

	$strfeed='';
	$strfeed='<?xml version="1.0" encoding="ISO-8859-1"?>
	<article>';
	if(is_array($stock_tickers)){
		$strfeed.='<tickers>';
		foreach($stock_tickers as $key=>$row){
			$strfeed.='<ticker>'.strtoupper($row['exchange']).":".strtoupper($row['stocksymbol']).'</ticker>';
		}
		$strfeed.='</tickers>';
	}
	if(strtotime(mysqlNow()) < strtotime($date))
	{
		$date = mysqlNow();
	}
	$strfeed.='	<title>'.$title.'</title>
	  <subtitle></subtitle>
	  <description>'.$description.'</description>
	  <publicationdate>'.$date.'</publicationdate>
	  <url>'.$url.'</url>
	  <byline>'.$author.'</byline>
	  <body><![CDATA['.$body.']]></body>
	  <uid>'.$uid.'</uid>
	  <related></related>
	</article>';
	$this->ftpYahooXML($id,$item_text,$uid,$strfeed,$title);
}


function generateBuzzYahooXml($id,$feedData,$itemType,$syndchannel,$verifyticker=null){
	global $HTNOSSLDOMAIN,$HTPFX,$HTHOST,$D_R;
	include_once("$D_R/lib/_content_data_lib.php");
	$objTicker	=	new Ticker();
	$objContributor= new contributor();
	$objContent		= 	new Content();
	$objBuzz= new Buzz();
	$title				=	htmlentities(stripslashes($feedData['title']), ENT_QUOTES);
	$stock_tickers		=	$objTicker->getTickersExchange($id,$itemType);
	if(count($stock_tickers)<7){
	    $bodyTicker=$objTicker->getTickerFromBody($feedData['body']);
	    if(is_array($stock_tickers) && is_array($bodyTicker)){
			$stockTickers=array_merge($stock_tickers,$bodyTicker);
		}
		elseif (count($bodyTicker) < 1){
			$stockTickers=$stock_tickers;
		}
		else{
			$stockTickers=$bodyTicker;
		}
		$stockTickers=$objTicker->array_unique_multidimensional($stockTickers);
	}else{
		$stockTickers=$stock_tickers;
	}
	$stockTickers=array_slice($stockTickers,0,7);
	if(count($stockTickers)<1){
	  /*send email if contain only 1 invalid ticker and yahoo feed not generate*/
	 	$typeEmail="BuzzBanter";
        $titleEmail=$title;
        $objBuzz->sendYahooInvalidTickerEmail($titleEmail,$typeEmail,$verifyticker,$isYahoo);
		return false;
	}
	if($verifyticker){
	    /*send email if contain invalid ticker(s) and yahoo feed generate*/
		$typeEmail="BuzzBanter";
        $titleEmail=$title;
		$isYahoo=1;
        $objBuzz->sendYahooInvalidTickerEmail($titleEmail,$typeEmail,$verifyticker,$isYahoo);
	}

	$date				=	$feedData['publish_date'];
	$urlTitle=$objContent->getFirstFiveWords($title);
	$urlDate=date("m/d/Y",strtotime($date));
	$url=$HTNOSSLDOMAIN.'/buzz/buzzalert/'.$urlTitle.'/'.$urlDate.'/id/'.$id.'?camp=syndication&amp;medium=portals&amp;from=yahoo';
	$url				=	str_replace("https:","http:",$url);
	if($feedData['excerpt'])
 	{
		$description	=	trim($feedData['excerpt']);
	}
	else
	{
	    $description 	= 	substr(trim(strip_tags($feedData['body'])),0,400);
	}
	$description	=	htmlentities($description, ENT_QUOTES);
	$authorID		=	$feedData['contrib_id'];
	$authorName		=	$objContributor->getContributor($authorID,$name=NULL);
	$author			=	htmlentities(stripslashes($authorName['name']), ENT_QUOTES);
	$uid			=	gmdate("Ymd",strtotime($date));
	$uid			=	$uid.$id.'buzz';
	$item_text		=	'Buzz Banter';

	$body = strip_tag_style($feedData['body']);
	$body = htmlentities($body,ENT_QUOTES);
	$regex = '/<object(.*?)<\/object>/i';
	$body = preg_replace($regex, '', $body);
	$body = str_replace('<strong','<b',$body);
	$body = str_replace('</strong>','</b>',$body);
	$body = str_replace('<em','<i',$body);
	$body = str_replace('</em>','</i>',$body);
	$body = utf8_encode(mswordReplaceSpecialChars($body));

	$strfeed='';
	$strfeed.='<?xml version="1.0" encoding="ISO-8859-1"?>';
	$strfeed.='<article>';
	if(is_array($stockTickers)){
		$strfeed.='<tickers>';
		foreach($stockTickers as $key=>$row){
			$strfeed.='<ticker>'.strtoupper($row['exchange']).":".strtoupper($row['stocksymbol']).'</ticker>';
		}
		$strfeed.='</tickers>';
	}
	$strfeed.='	<title>'.$title.'</title>
	  <subtitle></subtitle>
	  <description>'.$description.'</description>
	  <publicationdate>'.$date.'</publicationdate>
	  <url>'.$url.'</url>
	  <byline>'.$author.'</byline>
	  <body><![CDATA['.$body.']]></body>
	  <uid>'.$uid.'</uid>
	  <related></related>
	</article>';
	$this->ftpYahooXML($id,$item_text,$uid,$strfeed,$title);
}

function generateBlogsYahooXml($id,$blogPost,$itemType,$syndchannel){
	global $HTPFX,$HTHOST;
	$title				=	htmlentities(stripslashes($blogPost['title']), ENT_QUOTES);
	$stock_tickers		=	$blogPost['tags'];
	$date				=	$blogPost['publish_date'];
	$url 				=   $blogPost['url']."?camp=syndication&amp;medium=portals&amp;from=yahoo";
	$url				=	str_replace("https:","http:",$url);
	if($blogPost['excerpt'])
 	{
		$description	=	trim($blogPost['excerpt']);
	}
	else
	{
	    $description 	= 	substr(trim(strip_tags($blogPost['body'])),0,400);
	}
	$description	=	htmlspecialchars($description, ENT_QUOTES);
	$author			=	htmlentities(stripslashes($blogPost['author']), ENT_QUOTES);
	$uid			=	gmdate("Ymd",strtotime($date));
	$uid			=	$uid.$id.'blogs';
	$item_text		=	'Blogs';
	$strfeed='';
	$strfeed.='<?xml version="1.0" encoding="ISO-8859-1"?>';
	$strfeed.='<article>';
	if(is_array($stock_tickers)){
		$strfeed.='<tickers>';
		foreach($stock_tickers as $key=>$row){
			$strfeed.='<ticker>'.strtoupper($row).'</ticker>';
		}
		$strfeed.='</tickers>';
	}
	$strfeed.='	<title>'.$title.'</title>
	  <subtitle></subtitle>
	  <description>'.$description.'</description>
	  <publicationdate>'.$date.'</publicationdate>
	  <url>'.$url.'</url>
	  <byline>'.$author.'</byline>
	  <body></body>
	  <uid>'.$uid.'</uid>
	  <related></related>
	</article>';
	$this->ftpYahooXML($id,$item_text,$uid,$strfeed,$title);
}

function ftpYahooXML($feedId,$item_text,$uid,$strfeed,$title)
{
	global $D_R;
	include_once($D_R.'/lib/config/_syndication_config.php');
	global $yahoouser,$yahoopass,$yahoohost,$yahoopath,$D_R,$feed_error_template,$NOTIFY_FEED_ERROR_TO,$NOTIFY_FEED_ERROR_FROM,$NOTIFY_FEED_ERROR_SUBJECT;
	$feedName=$D_R."/assets/yahoofeed/minyanville/".$uid.".xml";
	$feedFile=fopen($feedName,"w+");
	fwrite($feedFile,$strfeed);
	fclose($feedFile);
	chmod($feedName, 0777);

	$localPath=$D_R."/assets/yahoofeed/minyanville/".$uid.".xml";
	$host=$yahoohost;
	$user=$yahoouser;
	$pass=$yahoopass;
	$path=$yahoopath;

	/*$host='ftp.minyanville.com';
	$user='dave';
	$pass='@Gi3~5!R';
	$path='/home/sites/minyanville/tsp/';*/

	$chkftp=$this->ftpPut($localPath, $user, $pass, $host,$path);
	//$chkftp =1;
	if($chkftp){
		$issynd=1;
		$minyanhosted=1;
		$syndchannel = 'yahoo';
		insertSyndication($feedId,$item_text,$syndchannel,$issynd,$minyanhosted);
		echo "Feed for Article id :".$uid." has generated<br>";
	}
	else {
		$to=$NOTIFY_FEED_ERROR_TO;
		$from=$NOTIFY_FEED_ERROR_FROM;
		$subject=$NOTIFY_FEED_ERROR_SUBJECT.'-'.$title;
		$file=$localPath;
		//$strurl = 'title=' . urlencode($title) . '&syndchannel=' . urlencode($syndchannel);
		$strurl = 'title=' . urlencode($title) . '&syndchannel=yahoo';
	 	mymail($to,$from,$subject,inc_web("$feed_error_template?$strurl"),$text,$file);
	}
}
function ftpPut($localPath, $user, $pass, $host,$path, $port=21){
	//NOTE: improve to accept globs
	//--run commands like mkdir rm, etc
	//puts localPath's filename to $path on remote host
	//$path should be directory

	// debug("ftpPut($localPath, $user, $pass, $host,$path, $port)");
	if(!$port)$port=21;
	if(!is_file($localPath)){
		$this->debug("ftpPut:$localPath doesn't exist");
		return 0;
	}

	$ftp=ftp_connect($host,$port);
	if(! ($ftp=ftp_connect($host, $port)) ){
		$this->debug("ftpPut:couldn't connect to $host:$port");
		return 0;
	}
	if( (!$login=ftp_login($ftp, $user, $pass))){
		$this->debug("ftpPut: couldn't log in with $user:$pass");
		ftp_close($ftp);
		return 0;
	}
	// turn passive mode on
	ftp_pasv($ftp, true);
	$path="$path/".basename($localPath);

	if(! ($upload=ftp_put($ftp, $path, $localPath, FTP_BINARY)) ){
		$this->debug("ftpPut: couldn't upload $localPath to $path");
		ftp_close($ftp);
		return 0;
	}
	$this->debug("ftpPut: successfully uploaded $localPath to $path");
	ftp_close($ftp);
	return 1;
}

function ftpDel($user, $pass, $host,$path, $port=21){
	//puts localPath's filename to $path on remote host
	//$path should be directory
	$this->debug("ftPdel($user, $pass, $host,$path, $port=21)");
	if(!$port)$port=21;

	$ftp=ftp_connect($host,$port);
	if(! ($ftp=ftp_connect($host, $port)) ){
		$this->debug("ftpPut:couldn't connect to $host:$port");
		return 0;
	}
	if( (!$login=ftp_login($ftp, $user, $pass))){
		$this->debug("ftpDel: couldn't log in with $user:$pass");
		ftp_close($ftp);
		return 0;
	}

	if(! $removal=ftp_delete($ftp, $path) ){
		$this->debug("ftpPut: couldn't remove $path");
		ftp_close($ftp);
		return 0;
	}
	$this->debug("ftpDel: successfully removed $path");
	ftp_close($ftp);
	return 1;
}
	function debug($msg=""){
		global $DEBUG,$DEBUGGING_ON;
		if(!$DEBUGGING_ON)
			return;
		$DEBUG[]="$msg\n";
	}

	function checkSyndication($itemid,$itemtype,$syndchannel){
	$getSyndicate="select id,is_syndicated from content_syndication where item_id='".$itemid."' and is_syndicated='1' and item_type='".$itemtype."' and syndication_channel='".$syndchannel."'";
	$getValue=exec_query($getSyndicate,1);
	if(isset($getValue)){
		return $getValue;
	}else{
		return 0;
	}

	}

}		//------- Class Ends ----------------
?>
