<?
global $D_R;
include_once("$D_R/lib/_includes.php");
include_once("$D_R/lib/_db.php");
include_once("$D_R/admin/lib/_yahoo_lib.php");
require_once("$D_R/lib/magpierss/rss_fetch.inc");
include_once($D_R.'/lib/_convert_charset.class.php');
$NewEncoding = new ConvertCharset('iso-8859-1', 'utf-8', 1);	//set to 1 for numeric entities instead of regular chars
$objYahoo = new YahooSyndication();

$url = "http://blogs.minyanville.com/feed/";
$url = $url."?time=".time(); // Append dummy parameter to the feed URL in order to avoid cache 
$qry = "select id from ex_item_type where item_text='Blogs'";
$result = exec_query($qry,1);
$item_type = $result['id'];
$blog_yahoo = array();
if($url){
	$rss = fetch_rss($url);
	date_default_timezone_set('America/New_York');
	foreach($rss->items as $item){
		if(is_array($item['category'])){
			if(in_array('yahoo finance',$item['category'])){
				if(!empty($item['tags'])){
					$k='0';
					foreach($item['tags'] as $key=>$val){
						$tag = strpos($val,':');
						if($tag){
							$blog_yahoo['tags'][$k] =$val;
							$k++;
						}
					}
				}
				if(!empty($blog_yahoo['tags'])){
					$id= $item['guid']['0'];
					$checkSyndication=$objYahoo->checkSyndication($id,$item_type,$syndchannel="yahoo");
					if(!$checkSyndication || $checkSyndication['is_syndicated']=="0"){
						foreach($item['title'] as $key=>$val){
							$blog_yahoo['title'] .= $val;
						}

						foreach($item['link'] as $key=>$val){
							$blog_yahoo['url'] .= $val;
						}

						foreach($item['pubdate'] as $key=>$val){
							date_default_timezone_set('America/New_York');
							$blog_yahoo['publish_date'] .= date("Y-m-d H:i:s",strtotime($val));
						}

						foreach($item['dc']['creator'] as $key=>$val){
							$blog_yahoo['author'] .= $val;
						}
						$blog_yahoo['body'] = $item['content']['encoded']['0'];

						$objYahoo->generateBlogsYahooXml($id,$blog_yahoo,$item_type,$syndchannel='yahoo');
						$blog_yahoo='';
					}//check sundication
					else{
						$blog_yahoo='';
						echo 'feed already generated for --'. $id.'<br>';
					}
				}//blog tag
			}//category if
		}
	} // foreach
} // if
?>