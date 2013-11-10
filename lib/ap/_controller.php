<?php
require_once($D_R.'/lib/config/_ap_config.php');
require_once($D_R.'/lib/ap/_model.php');
require_once($D_R.'/lib/magpierss/rss_fetch.inc');
require_once($D_R.'/lib/xml/NITF.php');
class apNewsController extends apNews{
	function parseRssFeed(){
	  global $feedURL,$user,$password,$D_R,$HTPFX;
  		$this->feedURL=$feedURL;
		$rssDetails = fetch_rss($this->feedURL,$user,$password);
		$items=$rssDetails->items;
		foreach($items as $item)
		{
			$this->NITFURL=$item['link_enclosure'][0];
			$articleData=$this->parseNIFTFeed();
			$ap_id=explode(":",$item['id'][0]);
			$articleData['ap_id']=end($ap_id);
			$articleData['date']=$item['published'][0];
			$this->setArticle($articleData);
		}
	}
	function parseNIFTFeed()
	{
		$objNIFT=& new XML_NITF();
		$objNIFT->setInputFile($this->NITFURL);
		$xResult = $objNIFT->parse();
		$articleData['headline']=htmlentities($objNIFT->getHeadline(),ENT_QUOTES);
		$articleData['author']=htmlentities($objNIFT->getByline(),ENT_QUOTES);
		$arrContent=$objNIFT->getContent();
		$articleData['body']="";
		foreach($arrContent as $paragraph){
			$articleData['body'].="<p>".$paragraph."</p>";
		}

		$articleData['body']=htmlspecialchars($articleData['body'],ENT_QUOTES);
		$articleData['body']=str_replace("—","-",$articleData['body']);
		$articleData['location']=$objNIFT->getLocation();
		$articleData['distributor']=htmlentities($objNIFT->getDistributor(),ENT_QUOTES);
		return $articleData;
	}

	function setArticle($articleDetails){
		$sqlGetArticle="SELECT ID FROM ap_articles WHERE ap_id='".$articleDetails['ap_id']."' OR headline='".$articleDetails['headline']."'";
		$resGetArticle=exec_query($sqlGetArticle,1);
		if(!$resGetArticle)
		{
		    $qry="insert into ap_articles (ap_id, headline, distributor, location,date, body, author)
	values('".$articleDetails['ap_id']."','".$articleDetails['headline']."','".$articleDetails['distributor']."','".$articleDetails['location']."', CONVERT_TZ('".$articleDetails['date']."','GMT','EST'),'".$articleDetails['body']."','".$articleDetails['author']."')";
		    $insertionid=exec_query($qry);
		}	
	}

	function getArticles($maxArticles=NULL,$days=NULL)
	{
		$sqlGetArticles="SELECT * FROM ap_articles";
		if($maxArticles)
		{
			$sqlGetArticles.=" ORDER BY date desc LIMIT 0,".$maxArticles;
		}else if($days){
			$sqlGetArticles.=" WHERE date>('".mysqlNow()."' - interval ".$days." day) ORDER BY date desc";
		}else{
			$sqlGetArticles.=" ORDER BY date desc LIMIT 0,10";
		}
		$resGetArticles=exec_query($sqlGetArticles);
		return $resGetArticles;
	}
// calls an ap article in a standard template - dan 

        function getSingleArticle($articleId)
        {
                $sqlGetSingleArticle="SELECT * FROM ap_articles WHERE AP_ID='".$articleId."'";
                $resGetSingleArticle=exec_query($sqlGetSingleArticle);
                return $resGetSingleArticle;

        }
}
?>
