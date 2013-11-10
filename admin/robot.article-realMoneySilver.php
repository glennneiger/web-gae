<?
global $D_R,$realMoneySilverURL;
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once($D_R.'/lib/config/_thestreet_config.php');
require_once($D_R.'/lib/rss_php.php');
include_once($D_R.'/lib/_convert_charset.class.php');
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
set_time_limit ( 60*30 );	//1 hour
$rss = new rss_php;
$url	= $realMoneySilverURL;
if($url)
{
 $rss->load($url);
 $items = $rss->getItems(); #returns all rss items
 foreach ($items as $item ) 
 {
		$guid					= 	htmlentities($item['guid'],ENT_QUOTES);
		$chkUniqueArticle		=	uniqueArticleCheck($guid);
		if($chkUniqueArticle)
		{
			echo "Real Money Silver: Article Already Exist!";
		}
		else
		{
			$article['guid'] 			= 	htmlentities($item['guid'],ENT_QUOTES);
			$title						=	trim($item['title']);
			$title						=	str_replace("&amp;amp;","&",$title);
			$article['title'] 			= 	htmlentities($title,ENT_QUOTES);
			$article['article_type'] 	= 	'realmoneysilver';
			$article['contrib_name']	=	htmlentities(strip_tags($item['byline']),ENT_QUOTES);	
			$article['seo_title']		=	htmlentities($title,ENT_QUOTES);
			$article['approved']		=	'0';
			$article['sent']			=	'0';
			$article['is_live']			=	'0';
			$article['date']			=	date('Y-m-d H:m:s',strtotime($item['pubDate']));
			
			$description  				= 	$NewEncoding->Convert(trim($item['description']));
			$description				=	str_replace("&amp;amp;","&",$description);
			$article['character_text'] 	=	htmlentities(trim($description),ENT_QUOTES);
			
			$body						=   trim($item['content:encoded']);
			/*$body  						= 	$NewEncoding->Convert($body);
			$body						=	str_replace("&amp;amp;","&",$body);
			$article['body']			=	htmlentities($body,ENT_QUOTES);*/
			
			$body  		= 		$NewEncoding->Convert($body);
			$body				=	str_replace("&amp;amp;","&",$body);
			$body				=	str_replace("&amp;lt;","<",$body);
			$body				=	str_replace("&amp;gt;",">",$body);
			$body				=	str_replace("</B>","</B> ",$body);
			$body				=	str_replace("<B>"," <B>",$body);
			$article['body'] 	=	addslashes($body);
			
			$article['publish_date']	=	date('Y-m-d H:m:s',strtotime($item['pubDate']));
			$article['layout_type']		=	'thestreet';
			//htmlprint_r($article);
			$id=insert_query("thestreet_articles",$article);
			
			if($id!='')
			{
			echo "<br>Real Money Silver: Article inserted Successfully!<br>";
			}
		}
  }	
}
function uniqueArticleTitle($title,$feed_id = NULL)
{
		$qry="select id from thestreet_articles where title = '".$title."'";
		if($feed_id != NULL)
		{
			$qry .= " AND id !=".$feed_id;
		}
		$result=exec_query($qry,1);
		if(count($result) >  0)
		{
			return true;
		}
		else
		{
			return false;
		}
}

function uniqueArticleCheck($guid)
{
	$qry="select a.id from thestreet_articles a where a.guid = '".$guid."'";
	$result=exec_query($qry,1);
	if(count($result) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>