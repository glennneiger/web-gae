<?
function mostViewedArticle()
{
	$sqlMostViewded = "select article_recent.id, article_recent.title,article_recent.keyword,article_recent.blurb,
						article_recent.total from article_recent order by total desc limit 5";
	$resultMostViewed = exec_query($sqlMostViewded);

	if($resultMostViewed)
	{
		return $resultMostViewed;
	}
	else
	{
		return NULL;
	}
}

function mostEmailedArticle($days = 1)
{
	$sqlMostEmailed = "select articles.id, articles.title,articles.keyword,articles.blurb,count(tracking_email.time) as total
					   from articles join tracking_email on articles.id = tracking_email.id where approved='1' and is_live='1'
					   and tracking_email.type = 'article' and tracking_email.time between date_sub('".mysqlNow()."',interval $days day)
					   and '".mysqlNow()."' group by tracking_email.id order by total desc limit 5";

	$resultMostEmailed = exec_query($sqlMostEmailed);

	if($resultMostEmailed)
	{
		return $resultMostEmailed;
	}
	else
	{
		return NULL;
	}
}

function mostRecenetArticle()
{
	$sqlRecenetArticle = "select articles.id, articles.title,articles.keyword,articles.blurb from articles where approved='1' and
						  is_live='1' order by date desc limit 4";
	$resultRecenetArticle = exec_query($sqlRecenetArticle);

	if($resultRecenetArticle)
	{
		return $resultRecenetArticle;
	}
	else
	{
		return NULL;
	}
}

function makeArticleslink($id,$keyword,$blurb,$from=NULL,$page=NULL)
{
	global $D_R;
	include_once($D_R.'/lib/_content_data_lib.php');
	$objArticle= new Content("1",$id);
	$objArticle->getMetaData();
	return $objArticle->url;
 }
?>