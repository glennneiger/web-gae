<?
global $D_R,$thestreetURL;
ini_set('magic_quotes_runtime', 0);
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once($D_R.'/lib/config/_thestreet_config.php');
set_time_limit ( 60*30 );	//1 hour
require_once("$D_R/lib/magpierss/rss_fetch.inc");
include_once($D_R.'/lib/_convert_charset.class.php');
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
$url	= $thestreetURL;
if($url)
{
 $rss = fetch_rss($url);
 foreach ($rss->items as $item) 
 {
 	if(count($item['guid'])>1)
	{
		$guid ='';
		for($i=0;$i<count($item['guid']);$i++)
		{
			$guid .=  trim($item['guid'][$i]);
			
		}
		$guid					= 	htmlentities($item['guid'][0],ENT_QUOTES);
	}
	else
	{
		$guid					= 	htmlentities($item['guid'][0],ENT_QUOTES);
	}
	$chkUniqueArticle		=	uniqueArticleCheck($guid);
	if(!($chkUniqueArticle))
	{
			if(count($item['title'])>1)
			{
				$title ='';
				for($i=0;$i<count($item['title']);$i++)
				{
					$title .=  trim($item['title'][$i]);
					
				}
				$article['title']			=	htmlentities(trim($title),ENT_QUOTES);
				$article['seo_title']		=	htmlentities(trim($title),ENT_QUOTES);
				$article['title']			=	str_replace("&amp;amp;","&",$article['title']);
				$article['seo_title']		=	str_replace("&amp;amp;","&",$article['seo_title']);
			}
			else
			{
				$article['title']			=	htmlentities(trim($item['title'][0]),ENT_QUOTES);
				$article['seo_title']		=	 htmlentities(trim($item['title'][0]),ENT_QUOTES);
				
				$article['title']			=	str_replace("&amp;amp;","&",$article['title']);
				$article['seo_title']		=	str_replace("&amp;amp;","&",$article['seo_title']);
			}
			
			if(count($item['description'])>1)
			{
				$description='';
				for($i=0;$i<count($item['description']);$i++)
				{
					$description .=  trim($item['description'][$i]);
					
				}
				$description				=	htmlentities(trim($description),ENT_QUOTES);
			}
			else
			{
				$description		=	htmlentities(trim($item['description'][0]),ENT_QUOTES);
			}
			$description				=	str_replace("&amp;amp;","&",$description);
			$article['character_text'] 	=	htmlentities(trim($description),ENT_QUOTES);
			
			$article['guid']  	=	$guid;
			$article['publish_date']	=	date('Y-m-d H:m:s',strtotime($item['pubdate'][0]));
			$article['date']			=	date('Y-m-d H:m:s',strtotime($item['pubdate'][0]));
			$article['contrib_name']	=  htmlentities($item['dc']['contributor_name'][0],ENT_QUOTES);
			if(count($item['category'])>0)
			{
				$tickers	=	'';
				for($i=0;$i<count($item['category']);$i++)
				{
					$tickers .=  trim($item['category'][$i]).",";
				}
				$tickers	=	substr($tickers,0,-1);
			}
			else
			{
				$tickers	=	'';
			}
			$article['tickers']	=	htmlentities($tickers,ENT_QUOTES);
			
			
			
			if(count($item['topics_category'])>0)
			{
				$category	=	'';
				for($i=0;$i<count($item['topics_category']);$i++)
				{
					$category .=  trim($item['topics_category'][$i]).",";
				}
				$category	=	substr($category,0,-1);
			}
			else
			{
				$category	=	'';
			}
			$article['category']	=	htmlentities($category,ENT_QUOTES);
			
			
			if(count($item['content']['encoded'])>1)
			{
				$body='';
				for($i=0;$i<count($item['content']['encoded']);$i++)
				{
					$body .=  trim($item['content']['encoded'][$i]);
					
				}
			}
			else
			{
				$body		=	trim($item['content']['encoded'][0]);
			}
			
			$body  		= 		$NewEncoding->Convert($body);
			$body				=	str_replace("&amp;amp;","&",$body);
			$body				=	str_replace("&amp;lt;","<",$body);
			$body				=	str_replace("&amp;gt;",">",$body);
			$body				=	str_replace("</B>","</B> ",$body);
			$body				=	str_replace("<B>"," <B>",$body);
			$article['body'] 	=	addslashes($body);
			$article['approved']		=	'0';
			$article['sent']			=	'0';
			$article['is_live']			=	'0';
			$article['layout_type']		=	'thestreet';
			$article['article_type']	=	'thestreet';
			
			$id=insert_query("thestreet_articles",$article);
			if($id)
			{
			echo "<br>TheStreet: Article inserted Successfully!<br>";
			}
		}
		else
		{
			echo "<br>TheStreet: Article Already Exist!<br>";
		}
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
/*function uniqueArticleTitle($title,$feed_id = NULL)
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
}*/
?>