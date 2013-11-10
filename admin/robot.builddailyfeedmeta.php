<?php
set_time_limit(60*30 );//1 hour
include_once("../lib/_db.php");
include_once("../lib/_content_data_lib.php");
include_once("../lib/layout_functions.php");

$objcontent=new Content();

//Build Daily Feed Meta Data
			/*$sqlDailyFeedMeta="SELECT DF.id,DF.body,DF.excerpt,DF.title,DF.updation_date,DATE_FORMAT(DF.updation_date,'%c/%e/%Y') format_updation_date,DF.is_approved,DF.contrib_id,C.name author_name,DF.source from daily_feed DF,contributors C WHERE DF.is_deleted='0' and C.id=DF.contrib_id order by id desc";*/
			$sqlDailyFeedMeta="SELECT DF.id,DF.body,DF.excerpt,DF.title,DF.updation_date,DATE_FORMAT(DF.updation_date,'%c/%e/%Y') format_updation_date,DF.is_approved,DF.contrib_id,C.name author_name from daily_feed DF,contributors C WHERE DF.is_deleted='0' and C.id=DF.contrib_id order by id desc";
			
			$resDailyFeedMeta=exec_query($sqlDailyFeedMeta);
			foreach($resDailyFeedMeta as $value)
			{
				$metaData['item_id']=$value['id'];
				/*$metaData['is_live']=$value['is_approved'];
				$metaData['item_type']="18";
				$metaData['title']=clean_title_url($value['title']);
				$metaData['publish_date']=$value['updation_date'];
				$metaData['author_id']=$value['contrib_id'];
				$metaData['author_name']=$value['author_name'];
				$metaData['resource']=$value['source'];*/
		
				/*$valuebody=strip_tag_style($value['body']);*/
				
				/*if($value['excerpt']==""){
					$metaData['description']=htmlentities(substr(strip_tags($valuebody),0,70),ENT_QUOTES);	
				}else{
					$metaData['description']=htmlentities(strip_tags($value['excerpt']),ENT_QUOTES);
				}*/

				
				$sqlGetTags="SELECT ET.tag tag,ET.type_id is_ticker, EIT.tag_id from ex_tags ET, ex_item_tags EIT where EIT.tag_id=ET.id and EIT.item_type='18' and EIT.item_id='".$value['id']."'";
				$resGetTags=exec_query($sqlGetTags);
				$keywordsName=array();
				if($resGetTags){
					// unset($keywords);
					unset($tickers);
					unset($keywordsName);
					foreach($resGetTags as $valueTags)
					{
						//if($valueTags['is_ticker'])
						//	$tickers[]=$valueTags['tag_id'];
					//	$keywords[]=$valueTags['tag_id'];
						$keywordsName[]=$valueTags['tag'];
					}
					//$metaData['tickers']=implode(",",$tickers);
					$metaData['keywords']=implode(",",$keywordsName);
				}	
					/*if(is_array($tickers)){
						$tickerval=implode(",",$tickers);
					}else{
						$tickerval="";
					}*/
					
				/*	$metaData['content']=$tickerval. " \n " .strip_tags($value['excerpt']). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
					
				}else{
					$metaData['content']=strip_tags($value['excerpt']). " \n " .strip_tags($value['body']). " \n " .$value['title']. " \n " .$value['author_name'];
				}*/
				/*$resgetticker="select ES.stocksymbol from ex_item_ticker EIT, ex_stock ES where ES.id=EIT.ticker_id and  EIT.item_id='".$value['id']."'";
				
				$getTicker=exec_query($resgetticker);
				$tickerName=array();
				foreach($getTicker as $valueTicker){
					$tickerName[]=$valueTicker['stocksymbol'];
				}
				$metaData['tickers']=implode(",",$tickerName);*/
				/*$titleurl=getFirstFiveWords($value['title']);
				$metaData['url']="/dailyfeed/".strtolower($titleurl).'/';
				
				$idcontentseo=$objcontent->updateContentSeoUrl($value['id'],"18",$metaData['url']);
				if($idcontentseo){ // content already live not update meta url
					unset($metaData['url']);
				}*/
				
				/*if($metaData['tickers']){
					if($metaData['keywords']){
						$metaData['keywords']=$metaData['keywords'].','.$metaData['tickers'];
					}else{
						$metaData['keywords']=$metaData['tickers'];
					}
				}
				
				if($metaData['keywords']){
					$metaData['keywords']=$metaData['keywords'].','.$metaData['resource'];
				}else{
					$metaData['keywords']=$metaData['resource'];
				}*/
				
				/*$metaData['content']=htmlentities($metaData['content'],ENT_QUOTES);*/
				if($value['id'])
				{
					//htmlprint_r($metaData);
					$id=insert_or_update("ex_item_meta",$metaData,array('item_id' =>$value['id'], 'item_type' => 18));
					echo "<br>".'id------'.$id.'---------'.$value['id'];
				}
			
			unset($metaData);
			}
			
			
   function getFirstFiveWords($body){
        $title="";
		$title=strtolower($body);
		$title=$title." &nbsp; ";
   	 	$pos=0;
		$pos=strpos($title,":");
		if($pos){
			$title=trim(substr($title,$pos + 1));
		}
		preg_match("/^(\S+\s+){0,5}/",trim($title), $matches);
		$matches[0]=mswordReplaceSpecialChars($matches[0]);
		$matches[0]=clean_title_url($matches[0]);
		$matches[0]=str_replace(" ","-",trim($matches[0]));
		return trim($matches[0]);
   }
   
   function clean_title_url($text)
	{
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
	$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	$text = str_replace("-", " ", $text);
	return $text;
	}


?>