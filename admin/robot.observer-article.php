<?
global $D_R,$observerURL,$contributorName;
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once($D_R.'/lib/config/_observer_config.php');
set_time_limit ( 60*30 );	//1 hour
require_once("$D_R/lib/magpierss/rss_fetch.inc");
include_once($D_R.'/lib/_convert_charset.class.php');
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
$url	= $observerURL;
global $imagePrePath;
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
		$guid					= 	addslashes($item['guid'][0]);
	}
	else
	{
		$guid					= 	addslashes($item['guid'][0]);
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
				//$article['title']			=	htmlentities(trim($title),ENT_QUOTES);
				//$article['title']			=	str_replace("&amp;amp;","&",$article['title']);
				$article['title']			=	parseSpecialChars(trim($title));

			}
			else
			{
				//$article['title']			=	htmlentities(trim($item['title'][0]),ENT_QUOTES);
				//$article['title']			=	str_replace("&amp;amp;","&",$article['title']);
				$article['title']			=	parseSpecialChars($item['title'][0]);
			}
			$article['title']				= 	addslashes(mswordReplaceSpecialChars(stripslashes($article['title'])));
			if(count($item['description'])>1)
			{
				$body='';
				for($i=0;$i<count($item['description']);$i++)
				{
					$body .=  trim($item['description'][$i]);

				}
				//$body				=	htmlentities(trim($body),ENT_QUOTES);
			}
			else
			{
				//$body		=	htmlentities(trim($item['description'][0]),ENT_QUOTES);
				$body		=	trim($item['description'][0]);
			}

			$body  		= 		$NewEncoding->Convert($body);
			$body  		= 		parseSpecialChars($body);
			//$searchImg			=	"/files/";
			//$replaceImg			=	$imagePrePath."files/";
			//$body				=	str_replace($searchImg,$replaceImg,$body);
			$body 				=	trim(parseSpecialChars($body));
			$desc				=	countLimitedWords(strip_tags(strip_image_tags($body)),'25');
			//echo "hello<br>";
			$article['body'] 	=	addslashes(mswordReplaceSpecialChars(stripslashes($body)));
			$article['character_text'] 	=	addslashes(mswordReplaceSpecialChars(stripslashes($desc)));

			//$article['body'] 		=	addslashes($body);
			$article['guid']  	=	$guid;
			$article['publish_date']	=	date('Y-m-d H:m:s',strtotime($item['pubdate'][0]));
			$article['date']			=	date('Y-m-d H:m:s',strtotime($item['pubdate'][0]));
			$article['contrib_name']	=  trim($contributorName);
			if(count($item['category'])>0)
			{
				$category	=	'';
				for($i=0;$i<count($item['category']);$i++)
				{
					$category .=  trim($item['category'][$i]).",";
				}
				$category	=	substr($category,0,-1);
			}
			else
			{
				$tickers	=	'';
			}
			$article['category']	=	addslashes($category);

			$article['approved']		=	'0';
			$article['sent']			=	'0';
			$article['is_live']			=	'0';
			$article['layout_type']		=	'observer';

			$id=insert_query("observer_articles",$article);
			if($id)
			{
			echo "<br>Observer: Article inserted Successfully!<br>";
			}
		}
		else
		{
			echo "<br>Observer: Article Already Exist!<br>";
		}
	}
}
function uniqueArticleCheck($guid)
{
	$qry="select a.id from observer_articles a where a.guid = '".$guid."'";
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
function strip_image_tags($str)
{
	$str = preg_replace("#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "", $str);
	$str = preg_replace("#<img\s+.*?src\s*=\s*(.+?).*?\>#", "", $str);

	return $str;
}

function countLimitedWords($body,$count="25"){
	    $result="";
		$result=$body;
		preg_match("/^(\S+\s+){0,$count}/",trim($result), $matches);
		return trim($matches[0]);
   }
function parseSpecialChars($body)
{
			/*$body 			= 	str_replace("‘", "'", $body);
			$body 			= 	str_replace("’", "'", $body);*/
			/*$body			= 	str_replace("”", '"', $body);
			$body 			= 	str_replace("“", '"', $body);*/
			$body 			= 	str_replace("–", "-", $body);
			$body 			= 	str_replace("…", "...", $body);
			$body			=	str_replace("/&ldquo;/",'&quot;', $body);
			$body			=	str_replace("/&rdquo;/",'&quot;', $body);
			$body			=	str_replace("&amp;amp;","&",$body);
			$body			=	str_replace("lt;","<",$body);
			$body			=	str_replace("gt;",">",$body);
			$body			=	str_replace("</strong>","</strong> ",$body);
			$body			=	str_replace("<strong>"," <strong>",$body);
			$body			=   str_replace("<em>",'&nbsp;<em>',$body);
			$body           =   str_replace("</em>",'</em> ',$body);
			$body			=	str_replace("</b>","</b> ",$body);
			$body			=	str_replace("<b>"," <b>",$body);
			//$body			=	str_replace("&quot;",'"',$body);
			$body			=	str_replace("&#039;&quot;",'"',$body);
			$body			=	str_replace("&quot;&#039;",'"',$body);
			$body			=	str_replace("&#039;","'&nbsp;",$body);
			return $body;
}
?>