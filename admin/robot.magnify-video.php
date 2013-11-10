<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once("$D_R/lib/config/_video_config.php");
//set_time_limit ( 60*30 );	//1 hour
require_once("$D_R/lib/magpierss/rss_fetch.inc");
include_once($D_R.'/lib/_convert_charset.class.php');
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
$url = $videoUrl;
if($url){
	$rssVideo = fetch_rss($url);
	foreach($rssVideo->items as $item){
	//htmlprint_r($item);
		if(count($item['guid'])>1)
		{
			$guid ='';
			for($i=0;$i<count($item['guid']);$i++)
			{
				$guid .=  trim($item['guid'][$i]);
				
			}
			$guid = htmlentities($item['guid'][0],ENT_QUOTES);
		}
		else
		{
			$guid = htmlentities($item['guid'][0],ENT_QUOTES);
		}
		
		if(count($item['pubdate'])>1)
		{
			$pubDate = '';
			for($i=0;$i<count($item['pubdate']);$i++){
				$pubDate .=  trim($item['pubdate'][$i]);
			}
			$video['publish_time'] = date("Y-m-j G:i:s", strtotime($pubDate));
		}else{
			$video['publish_time'] = date("Y-m-j G:i:s", strtotime($item['pubdate'][0]));
		}
		
		$video['creation_time'] = $video['publish_time'];
		$video['updation_time'] = $video['publish_time'];
		
		foreach($item['media']['credit'] as $creditKey){
			if(count($creditKey) > 1){
				$uploader = '';
				for($i=0;$i<count($creditKey);$i++){
					$uploader .=  trim($creditKey[$i]);
				}
				$video['uploaded_by'] = htmlentities($creditKey,ENT_QUOTES);
			}else{
				$video['uploaded_by'] = htmlentities($item['media']['credit'][0],ENT_QUOTES);
			}
		}
		
		foreach($item['media']['keywords'] as $key){
			if(count($key) > 1){
				$keyword = '';
				for($i=0;$i<count($key);$i++){
					$keyword .=  trim($keyword[$i]);
				}
				$video['keywords'] = htmlentities($keyword,ENT_QUOTES);
			}else{
				$video['keywords'] = htmlentities($item['media']['keywords'][0],ENT_QUOTES);
			}
		}
		
		if(count($item['description'])>1)
		{
			$body='';
			for($i=0;$i<count($item['description']);$i++)
			{
				$body .=  trim($item['description'][$i]);
				
			}
		}
		else
		{
			$body =	trim($item['description'][0]);
		}
		
		$body  =  $NewEncoding->Convert($body);
		$body  =  parseSpecialChars($body);
		$body  =  trim(parseSpecialChars($body));
		$video['description']  = addslashes(mswordReplaceSpecialChars(stripslashes($body)));
		
		if(count($item['link'])>1)
		{
			$link = '';
			for($i=0;$i<count($item['link']);$i++){
				$link .=  trim($item['link'][$i]);
			}
			$video['permalink'] = $link;
		}else{
			$video['permalink'] = $item['link'][0];
		}
		
		
		if(count($item['media']['player_url'])>1)
		{
			$playerUrl = '';
			for($i=0;$i<count($item['media']['player_url']);$i++){
				$playerUrl .=  trim($item['media']['player_url'][$i]);
			}
			$videofile = $playerUrl;
		}else{
			$videofile =$item['media']['player_url'];
		}
		
		$video['videofile'] = $videofile."/file/flv";
		$video['podcasturl'] = $videofile."/file/mp4";

		if(count($item['title']) > 1){
			$title = '';
			for($i=0;$i<count($item['title']);$i++){
				$title .=  trim($item['title'][$i]);
			}
			$video['title'] = parseSpecialChars(trim($title));
		}else{
			$video['title'] = parseSpecialChars($item['title'][0]);
		}
		$video['title']	= htmlentities($video['title'],ENT_QUOTES);
		
		$video['approved'] = '1';
		$video['is_live']= '1';
		$video['brand_id']= '1';
		$video['videofile_wmv']= '';
		$video['yahoo']= '0';
		$video['fox']= '0';
		$video['aol']= '1';
		$video['duration']= '0';
		$video['podcastsize']= '0';
		$video['ameritrade']= '0';
		$video['toddvideo']= '0';
		
		if(count($item['category']) > 1){
			$catId = '';
			for($i=0;$i<count($item['category']);$i++){
				$catId .=  getCatId($item['category'][$i]);
			}
			$video['cat_id'] = $catId;
		}else{
			$video['cat_id'] = getCatId($item['category'][0]);
		}
		
		if(count($item['media']['thumb_url'])>1)
		{
			$thumbUrl = '';
			for($i=0;$i<count($item['media']['thumb_url']);$i++){
				$thumbUrl .=  trim($item['media']['thumb_url'][$i]);
			}
			$video['thumbfile'] = $thumbUrl;
		}else{
			$video['thumbfile'] =$item['media']['thumb_url'];
		}
		
		$video['stillfile'] = $video['thumbfile'];
		
	//htmlprint_r($video);
		$chkunique = chkunique($guid);
		if(!($chkunique)){
			$id=insert_query("mvtv",$video);
			if($id)
			{
				echo "Video inserted Successfully!!!!!!".$guid."<br>";
			}
		}
		
	}
}

function chkunique($guid){

	$qry = "select id from mvtv where permalink like '".$guid."'";
	$result = exec_query($qry,1);
	if(count($result)>0){
		return true;
	} else {
		return false;
	}
}

function parseSpecialChars($body)
{
	$body = str_replace("–", "-", $body);
	$body = str_replace("…", "...", $body);
	$body =	str_replace("/&ldquo;/",'&quot;', $body);
	$body =	str_replace("/&rdquo;/",'&quot;', $body);
	$body =	str_replace("&amp;amp;","&",$body);
	$body =	str_replace("lt;","<",$body);
	$body =	str_replace("gt;",">",$body);
	$body =	str_replace("</strong>","</strong> ",$body);
	$body =	str_replace("<strong>"," <strong>",$body);
	$body = str_replace("<em>",'&nbsp;<em>',$body);
	$body = str_replace("</em>",'</em> ',$body);
	$body =	str_replace("</b>","</b> ",$body);
	$body =	str_replace("<b>"," <b>",$body);
	$body =	str_replace("&#039;&quot;",' "',$body);
	$body =	str_replace("&quot;&#039;",'" ',$body);
	$body =	str_replace("&#039;","'&nbsp;",$body);
	return $body;
}

function getCatId($catName){
	$catName = explode("," , $catName);
	$catName = $catName[0];
	$catName = str_replace("-"," ",$catName);
	$qryCatID = "select section_id from section where name='".$catName."'";
	$result = exec_query($qryCatID,1);
	$result = $result['section_id'];
	//$result = implode(",",$result);
	if($result){
		return $result;
	}else{
		return false;
	}
}


?>