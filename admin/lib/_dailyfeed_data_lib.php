<?
global $D_R;
include_once($D_R.'/lib/_action.php');
include_once("$D_R/lib/config/_rsync_config.php");
class Dailyfeed{

    var $contentType,$contentId;
	function __construct($type="",$id=""){
		$this->contentId=$id;
		if(is_numeric($type))
		{
			$this->contentType=$type;
		}
		else
		{
			$sqlGetContentTypeId="select id,item_table from ex_item_type where item_text='".$type."' or item_table='".$type."'";
			$resGetContentTypeId=exec_query($sqlGetContentTypeId,1);
			$this->contentType=$resGetContentTypeId['id'];
		}
	}

	function getDailyFeed($id){
	$qry="Select DF.is_buzzalert,DF.id,DF.creation_date,DF.updation_date,DF.publish_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,DF.title,DF.excerpt,DF.body,DF.publish_date,C.id as 'ContId',C.name contributor,DF.is_draft,DF.is_live,DF.position,DF.is_yahoofeed,C.bio_asset,DF.layout_type FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.id='".$id."'";
		$result=exec_query($qry,1);
		if($result){
		   $quicktitle=$this->getQuickTitleDailyFeed($id,"18");
		   $result['quick_title']=$quicktitle['quick_title'];
		   $result['is_featured']='0';
		   $arFeaturedFeed = $this->getFeaturedFeed();
		   $arFeedSynd = $this->getFeedGoogleSynd($id);
		   if(count($arFeaturedFeed) > 0)
		   {
		   		$result['is_featured']=$arFeaturedFeed['is_syndicated'];
		   }
		   if(!empty($arFeedSynd))
		   {
		   		$result['synd']=$arFeedSynd;
		   }
			return $result;
		}else{
			return false;
		}
	}

	function getFeedGoogleSynd($id)
	{
	   $qry="SELECT syndication_channel,is_syndicated FROM  content_syndication WHERE item_id='".$id."' AND item_type='18' ";
	   $result=exec_query($qry);
		if($result){
			foreach($result as $key=>$val)
			{
					$synd[$val['syndication_channel']]=$val['is_syndicated'];
			}
			return $synd;
		}

	}

	function getDailyFeedDetails($title){
   	   $qry="SELECT item_id FROM ex_item_meta WHERE (url like '%".$title."%' or url like '%".$title."/%') AND item_type='18' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			return $result['item_id'];
		}
	}
	function setDailyFeed($feed){
		$feed["creation_date"]=mysqlNow();
		unset($feed['topic']);
		unset($feed['ticker']);
		//unset($feed['resources']);
		$id=insert_query("daily_feed",$feed);
		if($id){
			return $id;
		}else{
			return false;
		}
	}

	function updateDailyFeed($feed,$id){
	   	unset($feed['topic']);
		unset($feed['ticker']);
		$feed["updation_date"]=mysqlNow();
		update_query("daily_feed",$feed,array(id=>$id));

	}
	function getResource($id,$item_type){
		$qry="select source,url,source_link from ex_source where item_id='".$id."' and item_type='".$item_type."'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}
	}

	function setResource($id,$feedsource,$feedsource_link,$item_type)
	{
		global $D_R;
		include_once("$D_R/lib/_content_data_lib.php");
	 	if($feedsource == "")
		{
			return;
		}
		$objContent = new Content();
		$params['item_id']=$id;
		$feedsource	=	addslashes(mswordReplaceSpecialChars(stripslashes($feedsource)));
		$params['source']=htmlentities($feedsource,ENT_QUOTES);
		$params['item_type']=$item_type;
		//$feedsource=$objContent->getFirstFiveWords($feedsource);
		$sourceurl=$this->getFilterWords(trim($feedsource));
		$url="/".strtolower($sourceurl).'/';
		$params['url']=trim($url);
		$params['source_link']=trim($feedsource_link);
		$condition=array("item_id"=>$id,item_type=>$item_type);
		$id=insert_or_update('ex_source',$params,$condition);
		if($id){
			return $id;
		}
	}

	function deleteResource($feedId)
	{
		 $qry="delete from ex_source where item_id=".$feedId." and item_type='18'";
		 exec_query_nores($qry);
	}


	function setTickers($feedtickers,$id,$item_type='18'){
				$this->id=$id;
				$this->tickers=array_unique(explode(",",trim($feedtickers)));
				$sqlDelTickers="delete from ex_item_ticker where item_id='".$this->id."' and item_type='18'";
				$resDelTickers=exec_query($sqlDelTickers);
				foreach($this->tickers as $ticker){
					if(trim($ticker)=="")
						continue;
					$sqlGetTicker="SELECT id from ex_stock where stocksymbol='".trim($ticker)."'";
					$resGetTicker=exec_query($sqlGetTicker,1);
					if(empty($resGetTicker['id'])){
						$getStockDetails=$this->getstockdetails(trim($ticker)); /*verify ticker from yahoo*/
						if($getStockDetails[0]){
							 $tickerId=$this->settStockTicker($getStockDetails); /*Insert data in the ex_stock table if verify from yahoo*/
						}
					}else{
						 $tickerId=$resGetTicker['id'];
					}
					$exItemTicker['ticker_id']=$tickerId;
					$exItemTicker['item_id']=$this->id;
					$exItemTicker['item_type']='18';
					insert_query("ex_item_ticker",$exItemTicker);
				}
	}
	function getTickers($id,$item_type){
		$qry="select ES.stocksymbol from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$item_type."'";
		$result=exec_query($qry);
		if($result){
		$val=array();
		   foreach($result as $row){
			  $val[]= $row['stocksymbol'];
		   }
			$data=implode(",",$val);
			return $data;
		}else{
			return false;
		}
	}

	function getTickersExchange($id,$item_type){
		$qry="select ES.id,ES.stocksymbol,ES.exchange from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$item_type."'";
		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	function deleteTickers($keys){
	    $qry="delete from ex_item_ticker where find_in_set(item_id,'$keys') and item_type='18'";
		exec_query_nores($qry);
	}

	function getDailyFeedList($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid,$category)
	{
	   $offset=$offset*$dailyfeedLandingItems;
		/*if($topic || $source || $cid || $tid){
				return $result;
		}else
		*/
		if($category){
		    if($offset){$category=$category.'/';}
		    $url='/dailyfeed/category/'.$category;
			$result=$this->getDailyFeedQuickTitleListByCategory($url,$offset);
			return $result;
		}else{
			$qry="Select DF.id,DF.creation_date,DF.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,DF.title,DF.excerpt,DF.body,DF.publish_date,C.id as 'ContId',C.name contributor,DF.is_draft,DF.position,DF.layout_type FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' order by display_date DESC limit " .$offset.",".$dailyfeedLandingItems;



		$result=exec_query($qry);
			if($result){
				return $result;
			}else{
				return false;
			}
		}
	}

	function getDailyFeedCount($dailyfeedLandingItems,$topic,$source,$cid,$tid,$category)
	{
		if($topic){
			$topic	= "/".$topic;
			$chkTag = substr($topic,-1);
			if($chkTag != '/')
			{
				$topic	= $topic."/";
			}
		    //$topic=str_replace("-"," ",$topic);
			$qry="SELECT count(xbt.item_id) count FROM ex_item_tags xbt, ex_tags xt,daily_feed DF  where xt.id = xbt.tag_id and xbt.item_type ='18' and DF.id=xbt.item_id and DF.is_approved='1' and DF.is_live='1' and DF.is_deleted='0' and xt.url='".$topic."'";

		}elseif($source){
			$source	= "/".$source;
			$chkStr = substr($source,-1);
			if($chkStr != '/')
			{
				$source	= $source."/";
			}
			$qry="SELECT count(s.id) count FROM ex_source s ,daily_feed DF where DF.id=s.item_id and s.item_type ='18' and DF.is_approved='1' and DF.is_deleted='0' and DF.is_live='1' and s.url='".$source."'";
		}elseif($cid){
		   $qry="Select count(DF.id) count FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' and DF.contrib_id='".$cid."'";
		}elseif($tid){
		   $qry="select count(item_id) count from ex_item_ticker where ticker_id='".$tid."' and item_type='18'";
		}elseif($category){
		    $url='/dailyfeed/category/'.$category;
		   $qry="SELECT count(quick_title) count FROM ex_quick_title EQT,daily_feed DF WHERE LOWER(EQT.url) = '".$url."' AND EQT.item_type='18' and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' and DF.id=EQT.item_id
";
		}else{
			$qry="Select count(DF.id) count FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1'";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}

	function getTopics($item_table,$id){
		$pageitem=exec_query("SELECT id FROM ex_item_type WHERE item_table='".$item_table."'",1);
	    
		$tagquery="SELECT xbt.item_id, xbt.tag_id, xt.tag as tagname FROM ex_item_tags xbt, ex_tags xt where xbt.item_id='".$id."' and xt.id = xbt.tag_id and xbt.item_type ='".$pageitem[id]."'";
		$pagetag[tag]="";
		$tagres = exec_query($tagquery);
		foreach($tagres as $tagkey => $tagvalue)
		{
			if($pagetag[tag]=="")
			{
				$pagetag[tag].= $tagvalue['tagname'] ;
			}
			else
			{
				$pagetag[tag].=",".$tagvalue['tagname'] ;
			}
		}
		return $pagetag[tag];

	}

	function getTopicsURL($item_table,$id){
		$pageitem=exec_query("SELECT id FROM ex_item_type WHERE item_table='".$item_table."'",1);

		$tagquery="SELECT xbt.item_id, xbt.tag_id, xt.tag as tagname,xt.url as tagurl FROM ex_item_tags xbt, ex_tags xt where xbt.item_id='".$id."' and xt.id = xbt.tag_id and xbt.item_type ='".$pageitem[id]."'";
		//$pagetag[tag]="";
		$tagres = exec_query($tagquery);
		if($tagres && count($tagres)>0)
		{
			$pagetag = array();
			foreach($tagres as $tagkey => $tagvalue)
			{
				$pagetag[$tagvalue[tagname]]	=	 $tagvalue[tagurl];
			}
			return $pagetag;
		}
		else{
			return false;
		}
	}

	function setTopic($feedtopic,$id){
			global $D_R;
			include_once("$D_R/lib/_content_data_lib.php");
			$objContent = new Content();
			$sqlDelTopics="delete from ex_item_tags where item_id='".$this->id."' and item_type='18'";
			$resDelTopics=exec_query($sqlDelTopics);
			$this->tags=explode(",",$feedtopic);
			$this->id=$id;
			foreach($this->tags as $tag){
				if(trim($tag)=="")
					continue;
				$sqlGetTag="SELECT id from ex_tags where tag='".trim($tag)."'";
				$resGetTag=exec_query($sqlGetTag,1);
				if(empty($resGetTag['id'])){
					$isStock=is_stock(trim($tag));
					$tagurl=$this->getFilterWords(trim($tag));
					$url="/".strtolower($tagurl).'/';
					$tagId=insert_query("ex_tags",array("tag"=>trim($tag),"type_id"=>count($isStock),"url"=>trim($url)));
				}else{
					$tagId=$resGetTag['id'];
				}
				$exItemTag['tag_id']=$tagId;
				$exItemTag['item_id']=$this->id;
				$exItemTag['item_type']='18';
				insert_query("ex_item_tags",$exItemTag,array("tag_id"=>$exItemTag['tag_id'],"item_id"=>$this->id,"item_type"=>"18"));
			}
	}

	function deleteTopic($id){
		$itemtagqry = "select id from ex_item_tags where item_id = '$id'";
		$getids=exec_query($itemtagqry);
		foreach($getids as $getidkey => $getidval)
		{
			del_query("ex_item_tags", "id", $getidval[id]);
		}

	}

	function getTopicByTag($dailyfeedLandingItems,$topic,$offset){
	    //$topic=str_replace("-"," ",$topic);
		$url='/'.$topic;
		$chkTag = substr($url,-1);
		if($chkTag != '/')
		{
			$url	= $url."/";
		}
		$qry="SELECT xbt.item_id FROM ex_item_tags xbt, ex_tags xt,daily_feed DF  where xt.id = xbt.tag_id and xbt.item_type ='18' and DF.id=xbt.item_id and DF.is_approved='1' and DF.is_deleted='0' and xt.url='".$url."' order by xbt.item_id desc limit ".$offset.",".$dailyfeedLandingItems;
		$itemvalue=array();
		$result=exec_query($qry);
		foreach($result as $row){ $itemvalue[]=$row['item_id'];}
		if($itemvalue){
			$result=implode(",",$itemvalue);
			return $result;
		}else{
			return false;
		}
	}

	function getResourceBySource($dailyfeedLandingItems,$source,$offset){
	    //$topic=str_replace("-"," ",$topic);
		$url='/'.$source;
		$chkStr = substr($source,-1);
		if($chkStr != '/')
		{
			$url	= $url."/";
		}
		$qry="SELECT s.item_id FROM ex_source s ,daily_feed DF  where  DF.id=s.item_id and s.item_type ='18' and DF.is_approved='1' and DF.is_deleted='0' and s.url='".$url."' order by s.item_id desc limit ".$offset.",".$dailyfeedLandingItems;

		$itemvalue=array();
		$result=exec_query($qry);
		foreach($result as $row){ $itemvalue[]=$row['item_id'];}
		if($itemvalue){
			$result=implode(",",$itemvalue);
			return $result;
		}else{
			return false;
		}
	}

	function getTickerItemid($dailyfeedLandingItems,$tid,$offset){
	    $qry="select item_id from ex_item_ticker where item_type='18' and ticker_id='".$tid."' order by item_id desc limit ".$offset.",".$dailyfeedLandingItems;
		$itemvalue=array();
		$result=exec_query($qry);
		foreach($result as $row){ $itemvalue[]=$row['item_id'];}
		if($itemvalue){
			$result=implode(",",$itemvalue);
			return $result;
		}else{
			return false;
		}

	}

	function getDailyFeedByTopic($dailyfeedLandingItems,$topic,$offset,$source,$cid,$tid)
	{
	   $qry="Select DF.id,DF.creation_date,DF.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,DF.title,DF.excerpt,DF.body,DF.publish_date,C.id as 'ContId',C.name contributor,DF.is_draft,DF.position,DF.layout_type FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1'";
	    if($topic)
	    {
			$itemid=$this->getTopicByTag($dailyfeedLandingItems,$topic,$offset);
			if(!empty($itemid)){
		  		$qry.=" and DF.id in (".$itemid.") order by display_date desc";
			}else{
				$qry.=" order by display_date desc limit 10";
			}
		}elseif($source){
		  	$itemid=$this->getResourceBySource($dailyfeedLandingItems,$source,$offset);
			if(!empty($itemid)){
		  		$qry.=" and DF.id in (".$itemid.") order by display_date desc";
			}else{
				$qry.=" order by display_date desc limit 10";
			}
		}elseif($cid){
		 	$qry.=" and DF.contrib_id='".$cid."' order by display_date desc limit ".$offset.','.$dailyfeedLandingItems;
		}elseif($tid){
		 	$itemid=$this->getTickerItemid($dailyfeedLandingItems,$tid,$offset);
			if(!empty($itemid)){
		  		$qry.=" and DF.id in (".$itemid.") order by display_date desc";
			}else{
				$qry.=" order by display_date desc limit 10";
			}
		}

		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}

	}


	function getDraftDailyFeed(){
		$qry="SELECT id,title FROM daily_feed where is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(creation_date)<=30) and is_draft='1' ORDER BY creation_date DESC";
		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	function setImageDailyFeed($item_id,$url,$dfimagename,$item_type){
	    global $IMG_SERVER;
		$value[item_id] = $item_id;
		$value[url] = $IMG_SERVER."/assets/dailyfeed/uploadimage/".date('mdy').'/'.$url;
		$value[name] = $dfimagename;
		$value[item_type] = $item_type;

		$id=insert_query("ex_item_image", $value);
	}

	function updateImageDailyFeed($item_id,$url,$dfimagename,$item_type){

	global $IMG_SERVER;
		$value[item_id] = $item_id;
		$value[url] =$IMG_SERVER.$url;
		$value[name] = $dfimagename;
		$value[item_type] = $item_type;
		$condition=array(item_id=>$item_id);
		if($dfimagename){
		 	$id=insert_or_update('ex_item_image',$value,$condition);
		}
	}

	function getImageDailyfeedByid($imageid,$id){
		$qryImage="select id,url,name from ex_item_image where id='".$imageid."' and item_type='18'";
		$resultImage=exec_query($qryImage,1);
		if($resultImage){
		    $params['item_id']=$id;
			$params['url']=$resultImage['url'];
			$params['name']=$resultImage['name'];
			$params['item_type']='18';
			$condition=array("item_id"=>$id,item_type=>'18');
		    $id=insert_or_update('ex_item_image',$params,$condition);
		    //$this->updateImageDailyFeed($id,$resultImage['url'],$resultImage['name'],$item_type='18');
			//return $resultImage;
		}else{
			return false;
		}

	}

	function getImageDailyFeed($feedId){
	$qryImage="select id,url,name from ex_item_image where item_id='".$feedId."' and item_type='18'";
	$resultImage=exec_query($qryImage,1);
		if($resultImage){
			return $resultImage;
		}else{
			return false;
		}
	}

	function getAllImagesDailyFeed(){
	$qryImage="SELECT id,url, name FROM ex_item_image WHERE item_type='18' GROUP BY (name) ORDER BY name";
	$resultImage=exec_query($qryImage);
		if($resultImage){
			return $resultImage;
		}else{
			return false;
		}
	}

	function removeImage($itemtype,$imageid){
		$qry="delete from ex_item_image where id='".$imageid."' and item_type='".$itemtype."'";
		exec_query_nores($qry);
	}


	function getAuthor($cid){
		$qry="select id,name from contributors where id='".$cid."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['name'];
		}else{
			return false;
		}
	}

	function getTicker($tid){
		$qry="select stocksymbol,companyName from ex_stock where id='".$tid."'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	 function getTickerStock($ticker){ /*search ticker in ex_stock table if not exist in tt_topic table*/
	    $tickerval=explode(',',$ticker);
		$invalidTicker=array();
		foreach($tickerval as $row){
			$qry="select id,stocksymbol from ex_stock where stocksymbol='".$row."'";
			$getStockid=exec_query($qry,1);
				if(!$getStockid){
						$validateticker=$this->getstockdetails($row); /*varify ticker from yahoo*/
					if($validateticker[0]){
						 $insertTickerid=$this->settStockTicker($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
					}else{
							$invalidTicker[]=$row;
					}
			   }
		  }
	  return $invalidTicker;

   }

   function getstockdetails($symbolname){ /*Validate ticker from Yahoo and if validate return value of ticker*/
	$tickersymbol=$symbolname;
		if (isset($tickersymbol))
		{
			$open = @fopen("http://download.finance.yahoo.com/d/quotes.csv?s=$tickersymbol&f=sl1d1t1c1ohgvnx&e=.csv", "r");
			$read = @fread($open, 2000);
			@fclose($open);
			unset($open);
			$read = str_replace("\"", "", $read);
			$read = explode(",", $read);
			IF ($read[1] == 0)
			{
				return 0;
			}ELSE{
				return $read;
			}
		}
	}

	function settStockTicker($validateticker){ /*Insert data in ex_stock table*/
		$stocktabldata=array(stocksymbol=>$validateticker[0],
							CompanyName=>addslashes(trim($validateticker[9])));
		$stocktabldata['stocksymbol']=strtoupper(trim($validateticker[0]));
		$tickerexchange=trim($validateticker[10]);
		if($tickerexchange=="NYSE"){
			$stocktabldata['exchange']="NYSE";
		}else{
			$stocktabldata['exchange']="NASDAQ";
		}
		$stocktabldata['SecurityName']=addslashes(trim($validateticker[9]));
		$stocktabldata['CompanyName']=addslashes(trim($validateticker[9]));
		$sid=insert_query("ex_stock",$stocktabldata);
		if($sid){
			return $sid;
		}else{
			return 0;
		}

	}

	function stockList($feedId,$body,$item_type)
	{
		 $qry="select ES.stocksymbol,ES.exchange from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$feedId."' and ET.item_type='".$item_type."'";
		$result=exec_query($qry);
		if($result){
		$val=array();
		   foreach($result as $row){
			  $val[]= $row['exchange'].":".$row['stocksymbol'];
		   }
		$data=implode(",",$val);
		}
		$unique_stocks=  make_stock_array($body);
		if(count($unique_stocks)>0)
		{
			$stock_list = '';
			foreach($unique_stocks as $key=>$Stock)
			{

				$sqlExchange = "Select exchange from ex_stock where stocksymbol='".$Stock."'";
				$resultExchange = exec_query($sqlExchange);
				if(count($resultExchange)>0)
				{
					$exchange	=	$resultExchange[0]['exchange'];
					if($stock_list == "")
					{
						$stock_list	=	$exchange.":".$Stock;
					}
					else
					{
						$stock_list.=",".$exchange.":".$Stock;
					}
				}
			}
		}
		else
		{
			$stock_list	="";
		}
		if($data!='' && $stock_list!='')
		{
			$stocks			=	 $metaarray.",".$stock_list	;
			$explode_arr	=	 explode(",",$stocks);
			$uni_arrKeys	= 	array_unique($explode_arr);
			$stockList		= 	implode(",",$uni_arrKeys);
		}
		elseif($data!='' && $stock_list=='')
		{
			$stockList	=	 $data;
		}
		elseif($data=='' && $stock_list!='')
		{
			$stockList	=	 $stock_list;
		}
		else
		{
			$stockList	 ='';
		}
	return $stockList;
	}

	function getFeedData($id)
	{
$qry="Select DF.id,DF.creation_date,DF.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS publish_date,DF.title,DF.excerpt,DF.body,DF.publish_date,DF.contrib_id,DF.is_draft,DF.position,DF.is_approved,DF.is_live,DF.is_draft,DF.layout_type FROM daily_feed DF WHERE DF.id='".$id."' and DF.is_live='1' and DF.is_approved='1'";

		$result=exec_query($qry,1);
		if($result && count($result)>0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	function getModalDailyFeedPopup(){
	global $IMG_SERVER;
	$upload_url = "/admin/lib/uploadFileBucket.php";
	?>
	<div id="boxes">
		<div id="dialog" class="window">
			<div  class="daily_feed_pop_up">
			<table width="502px" border="0" height="174px" border="0" style="margin:-30px 0px 0px 0px;" cellpadding="0" cellspacing="0">
			  <tr >
				<td width="152px" style="vertical-align:middle;"><div id="showerr" style="color:#000;display:block;font-family:Arial,Helvetica,sans-serif;font-size:11px;text-align:left;">&nbsp;</div>
							<div id="enable_submit" class="talkreplyLeft">
								<div id="uploaded_image">&nbsp;</div>
									<div class="btn_addchart" style="">
									<input style="float:left;" type="file" name="dailyFeedFile" id="dailyFeedFile" onClick="uploadDFFile('dailyFeedFile','<?=$upload_url?>');"> &nbsp;&nbsp;&nbsp;&nbsp;<div id="output"></div>
								    <input type="hidden" name="chkImage" id="chkImage">
								</div></div>
								
				</td>
				</tr>
				<tr>				
				<td style="vertical-align:middle; padding-top:20px;">&nbsp; Or  &nbsp;
				<?
							$getImage=$this->getAllImagesDailyFeed();
							?>
								<select id="dailyfeedimage" name="dailyfeedimage" onchange="dailyFeedImageselect();">
								<option value="" onmouseover="previewDFimage('');">--Choose an image--</option>
									<?
									foreach($getImage as $row){
										$sel=(trim($getImage[name])==trim($row[name])?" selected":"");
										// $disp=$row[name];
										//$style="style='heigth:50px;'";
									?>
									<option onmouseover="previewDFimage('<?=$row['url']?>');" value="<?=$row[id]?>"><?=$row['name'];?></option>
									<?}?>
							</select>
					</td>
                    <td width="80px">
                    <img width="80" height="80" style="display:none;" id="image_preview" src="" />
                    </td>
				  </tr>
			 <tr>

				<td  colspan="3"><div style="line-height:27px; font-weight:bold;">
			Give the Image a descriptive name (so that it can be found in dropdown for future use):
			</div></td>
			  </tr>
			  <tr>

				<td colspan="3"><input name="imagenamedf" id="imagenamedf" value="" type="text" size="92" style="border:1px solid black;" /></td>
			  </tr>
			  <tr>
				<td colspan="3">&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="3"><div class="bottom_section">
								<div class="close" style="cursor:pointer;">
									<img src="<?=$IMG_SERVER?>/images/DailyFeed/bttn_uploadnow.jpg" alt="Insert Image" onclick="setImage();"/>&nbsp;<span><img name="can" id="can"  style="border:none;" src="<?=$IMG_SERVER?>/images/DailyFeed/bttn_cancel.jpg" title="Cancel" onclick="cancelImage();" ></span>
								</div>
			</td>

			  </tr>
			</table>
			</div>
		</div>

		<div style="float:left;" id="inlineBox">
			<div id="jcropcontainer" class="window"   >
				<img id="cropbox" />
			</div>
	</div>

		<input type="hidden" id="cropped" name="cropped" value="0" />
	</div>

    <?
	}


	function getHotTopics($dailyfeedhottopiccount){
	global $dailyfeed_source_days;
	$sql="SELECT xt.tag as tagname,xt.url as tagurl, count(xt.tag) counttag FROM ex_item_tags xbt, ex_tags xt,daily_feed d where is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(updation_date)<='".$dailyfeed_source_days."') and is_approved='1' and is_live='1' and xbt.item_id =d.id and xt.id = xbt.tag_id and xbt.item_type ='18' group by xt.tag order by counttag desc limit ".$dailyfeedhottopiccount;

			$results=exec_query($sql);
			return $results;
	}


  function getTopSources(){
     global $dailyfeedtopsources,$dailyfeed_topics_days,$dailyfeed_source_days;
	//------------------- New Source Table Changes -------------------------
	$sql="SELECT d.id,s.source, count(s.source) countsource, s.url, s.source_link  FROM daily_feed d,ex_source s where d. is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(d.updation_date)<='".$dailyfeed_topics_days."') and d.id = s.item_id and s.item_type ='18' and d.is_approved='1' and d.is_live='1' group by s.source order by countsource desc limit ".$dailyfeedtopsources;

			$results=exec_query($sql);
			return $results;

   }


   function getContributors(){
	   global $dailyfeedcontributor,$dailyfeed_contributors_days;
	   $qry="SELECT C.name,DF.contrib_id, count(DF.contrib_id) countcontribid FROM daily_feed DF,contributors C where DF.is_deleted='0' and DF.is_approved='1' and C.id=DF.contrib_id and (To_days('".mysqlNow()."') - TO_DAYS(DF.updation_date)<='".$dailyfeed_contributors_days."') group by DF.contrib_id order by countcontribid desc limit 0,".$dailyfeedcontributor;

	   $result=exec_query($qry);
	   if($result){
			return $result;
		}
		return false;
   }

   function getPopularStocks(){
   	    global $dailyfeed_tickers_days,$dailyfeedstockcount;
		$sql= "select DF.id, ES.stocksymbol,ET.ticker_id,count(ES.stocksymbol) countticker from ex_stock ES,ex_item_ticker ET, daily_feed DF where ES.id=ET.ticker_id and DF.is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(DF.updation_date)<='".$dailyfeed_tickers_days."') and DF.is_draft='0' and DF.is_approved='1' and DF.is_live='1' and ET.item_id =DF.id and ET.item_type='18' group by ES.stocksymbol order by countticker limit 0,".$dailyfeedstockcount;

			$results=exec_query($sql);
			return $results;
	}

   function getMostReadItem($item_type){
   		global $dailyfeedMostReadCacheExpiry;
	   $obMemCache = new memCacheObj();
	$objPopularFeed = $obMemCache->getKey("popular_dailyfeed");
	if(!is_array($objPopularFeed))
	{
	  global $dailyfeed_most_read_count,$dailyfeed_most_read_days;
  $sql="select DF.id,ETV.item_id,DF.title, count(ETV.item_id) countitem from ex_item_tracking_view ETV, daily_feed DF where ETV.item_type='".$item_type."' and  TIME >= DATE_SUB('".mysqlNow()."',INTERVAL 4 HOUR) and DF.id=ETV.item_id AND DF.creation_date >= DATE_SUB('".mysqlNow()."',INTERVAL 7 DAY) group by ETV.item_id order by countitem desc limit 0,".$dailyfeed_most_read_count;
		$results=exec_query($sql);
		$obMemCache->setKey("popular_dailyfeed",$results,$dailyfeedMostReadCacheExpiry);
		$objPopularFeed = $results;
	}
	return $objPopularFeed;
   }

  function setDailyFeedThread($id,$authorid,$title,$teaser){
		global $defaultSudId;
		$sqlAuthSubs =" select subscription_id from ex_contributor_subscription_mapping where contributor_id='".$authorid."'";
		$results=exec_query($sqlAuthSubs,1);
		if($results && count($results)>0)
		{
			$threadval['author_id']	=	$results['subscription_id'];
		}
		else
		{
			$threadval['author_id']		=	$defaultSudId;
		}
		$threadval['created_on']=mysqlNow();
		$threadval['content_table']="daily_feed";
		$threadval['content_id']=$id;
		$threadval['is_user_blog']=0;
		$threadval['approved']='1';
		$threadval['title']=htmlentities($title,ENT_QUOTES);
		$threadval['teaser']=$teaser;
		$threadvalue=insert_query('ex_thread',$threadval);
		return $threadvalue;
   }

   function updateDailyFeedThread($id,$authorid,$title,$teaser){
   		global $defaultSudId;
		$sqlAuthSubs =" select subscription_id from ex_contributor_subscription_mapping where contributor_id='".$authorid."'";
		$results=exec_query($sqlAuthSubs,1);
		if($results && count($results)>0)
		{
			$threadval['author_id']	=	$results['subscription_id'];
		}
		else
		{
			$threadval['author_id']		=	$defaultSudId;
		}
	   	$threadval['updated_on']=mysqlNow();
		$threadval['title']=$title;
		$threadval['teaser']=$teaser;
		update_query("ex_thread",$threadval,array(content_id=>$id,'content_table'=>'daily_feed'));
   }

   function deleteDailyFeedThread($keys){
	$qry="delete from ex_thread where find_in_set(content_id,'$keys') and content_table='daily_feed'";
	exec_query_nores($qry);
   }

   /*for old feeds*/
   function setDailyFeedThreadDetailPage($id,$authorid,$title,$teaser){
	   $qry="select id from ex_thread where content_table='daily_feed' and content_id='".$id."'";
	   $result=exec_query($qry,1);
	  if(!$result['id']){
				$threadid = $this->setDailyFeedThread($id,$authorid,$title,$teaser);
			return $threadid;
	   }
	   else
	   {
	   	return $result['id'];
	   }
	  /*$qry="select id from ex_thread where content_table='daily_feed' and content_id='".$id."'";
	  $result=exec_query($qry,1);
	  if(!$result['id']){
	   		$threadid = $this->setDailyFeedThread($id,$authorid,$title);
		}*/
   }

   function getMostRecentComment(){
    global $dailyfeed_most_read_count;
   	$qry="SELECT ET.content_id,EP.teasure,ET.title FROM ex_thread ET,ex_post EP,ex_post_content EPC WHERE ET.id=EP.thread_id AND
EP.id=EPC.post_id AND ET.content_table='daily_feed' and ET.title <>'' and EP.suspended='0' order by EP.post_time desc limit ".$dailyfeed_most_read_count;
        $result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
   }

   function getUniqueSources(){
   		/*$sqlUniqueSource="SELECT DISTINCT(source) FROM  daily_feed ORDER BY source ASC";*/

		$sqlUniqueSource="SELECT DISTINCT(s.source), s.url FROM  ex_source s, daily_feed d  where s.item_type='18' and d.is_live='1' and d.is_approved='1' and d.id=s.item_id  AND s.source!='' ORDER BY source ASC ";
   		$resUniqueSource=exec_query($sqlUniqueSource);
   		return $resUniqueSource;
   	}

   	function getUniqueTags(){
   		//	$sqlUniqueTags="SELECT DISTINCT(ET.tag) FROM ex_item_tags EIT,ex_tags ET WHERE ET.id=EIT.tag_id AND EIT.item_type='18' ORDER BY tag asc";
	$sqlUniqueTags="SELECT DISTINCT (ET.tag),ET.url FROM ex_item_tags EIT,ex_tags ET, daily_feed DF WHERE ET.id=EIT.tag_id AND DF.id=EIT.item_id and EIT.item_type='18' and DF.is_live='1' and DF.is_approved='1' ORDER BY ET.tag asc";
   			$resUniqueTags=exec_query($sqlUniqueTags);
   			return $resUniqueTags;
	}
	function getUniqueTickers(){

	 $sqlUniqueTags="SELECT ES.stocksymbol as ticker ,ET.ticker_id FROM ex_stock ES,ex_item_ticker ET, daily_feed DF WHERE ES.id=ET.ticker_id AND DF.is_deleted='0' AND DF.is_draft='0' AND DF.is_approved='1' AND DF.is_live='1' AND ET.item_id =DF.id AND ET.item_type='18' GROUP BY ES.stocksymbol ORDER BY ES.stocksymbol  ASC";
   			$resUniqueTags=exec_query($sqlUniqueTags);
   			return $resUniqueTags;
	}
	function getTitle($title){
		$pos=strpos($title,":");
		$title=substr($title,$pos + 1);
		$qry="select id from daily_feed where title like '%".$title."%'";
		$result=exec_query($qry,1);
		if($result){
			return $result['id'];
		}
	}

	function checkUniqueTitle($url,$feed_id = NULL)
	{
		//$qry="select id from daily_feed where title = '".$title."'";
		$qry="select id from ex_item_meta where url='".$url."' and item_type='18'";
		if($feed_id != NULL)
		{
			$qry .= " AND item_id !=".$feed_id;
		}
		$result=exec_query($qry,1);
		if(count($result) >  0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function getAlldailyfeedUrl($feedDataArr)
	   {
	     $feedCount=count($feedDataArr);
		 for($i=0;$i<$feedCount;$i=$i+1)
		   {
		    $id[]=$feedDataArr[$i]['id'];
		   }
		 $item_id=implode(',',$id);
        $qry="select url,item_id,bitly_url from ex_item_meta where item_id in (".$item_id.") and item_type='18'";
		 $result=exec_query($qry);
		 foreach($result as $row)
		   {
		     $durl[$row['item_id']]['url']=$row['url'];
			 $durl[$row['item_id']]['bitly_url']=$row['bitly_url'];
		   }
		   return  $durl;
	   }



	function getDailyFeedUrl($id){
		$qry="select url from ex_item_meta where item_id='".$id."' and item_type='18'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}

	function getDailyFeedBitlyUrl($id){
		$qry="select bitly_url from ex_item_meta where item_id='".$id."' and item_type='18'";
		$result=exec_query($qry,1);
		if($result){
			return $result['bitly_url'];
		}
	}

	function getQuickTitleDailyFeed($id,$item_type){
		$qry="select quick_title,url from ex_quick_title where item_id='".$id."' and item_type='".$item_type."'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}
	}


	function getDailyFeedQuickTitleListByCategory($url,$offset){
	   global $dailyfeedLandingItems;
   	   $qry="SELECT quick_title FROM ex_quick_title WHERE LOWER(url) = '".$url."' AND item_type='18' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			$sql="SELECT EQT.item_id FROM ex_quick_title EQT,daily_feed DF WHERE EQT.quick_title = '".$result['quick_title']."' AND EQT.item_type='18'and EQT.item_id=DF.id and DF.is_approved='1' and DF.is_deleted='0' and DF.is_live='1' order by EQT.item_id desc limit ".$offset.",".$dailyfeedLandingItems;
			$getresult=exec_query($sql);
			if($getresult){
				foreach($getresult as $row){ $itemvalue[]=$row['item_id'];}
					if($itemvalue){
						$itemid=implode(",",$itemvalue);
						$qry="Select DF.id,DF.creation_date,DF.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,DF.title,DF.excerpt,DF.body,DF.publish_date,C.id as 'ContId',C.name contributor,DF.is_draft,DF.position FROM daily_feed DF,contributors C WHERE C.id=DF.contrib_id and DF.is_approved='1' and DF.is_deleted='0' and is_live='1' and DF.id in (".$itemid.") order by display_date desc";

						$getfeed=exec_query($qry);
						return $getfeed;
					}else{
						return false;
					}
				}
			}
	}


	function setQuickTitleDailyFeed($id,$quicktitle,$item_type){
		global $D_R;
		include_once("$D_R/lib/_content_data_lib.php");
	    $objContent = new Content();
		$params['item_id']=$id;
		$params['quick_title']=$quicktitle;
		$params['item_type']=$item_type;
		$quicktitle=$objContent->getFirstFiveWords($quicktitle);
		$params['url']="/dailyfeed/category/".$quicktitle.'/';
		$condition=array("item_id"=>$id,item_type=>$item_type);
		$id=insert_or_update('ex_quick_title',$params,$condition);
		if($id){
			return $id;
		}
	}
	 function getFilterWords($body){
	    $title="";
		$title=strtolower(str_replace("-"," ",$body));
		//$title=$title." &nbsp; ";
		/*preg_match("/^(\S+\s+){0,1000}/",trim($title), $matches);
		$matches[0]=str_replace(" ","-",trim($matches[0]));
		$matches[0]=mswordReplaceSpecialChars($matches[0]);
		$matches[0]=$this->clean_title_url($matches[0]);
		return trim($matches[0]);*/

		$title=str_replace(" ","-",trim($title));
		$title=mswordReplaceSpecialChars($title);
		$title=$this->clean_title_url($title);
		return trim($title);

   }

	# This function makes any text into a url frienly
	# This script is created by wallpaperama.com
	function clean_title_url($text)
	{
	$text=strtolower($text);
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
	$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
	}
	function getSyndicationValue($feedId)
	{
	   $qry="SELECT is_yahoofeed FROM daily_feed WHERE id='".$feedId."'";
	   $result=exec_query($qry,1);
	   if($result)
	   {
	   	return $result['is_yahoofeed'];
		}
		else
		{
			return false;
		}
	}
	function sendMailContent($id){
		$qry = "select id, title, body, publish_date, contrib_id from daily_feed where is_approved='1'and is_live='1' and is_deleted='0' and id='".$id."'";
		$result=exec_query($qry,1);
		if($result){
		    $dfTitle=$result['title'];
			$contribId=$result['contrib_id'];
			$dfId=$result['id'];
			$this->sendDailyFeedArticleMail($NotifyDailyFeedTo,$dailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$contribId,$dfSizeEmailAlert);
		}

	}

	function sendDailyFeedArticleMail($NotifyDailyFeedTo,$NotifyDailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$contribId,$dfSizeEmailAlert)
	{
		global $HTPFX,$HTHOST,$D_R,$NotifyDailyFeedTo,$NotifyDailyFeedFrom,$dfSizeEmailAlert,$DAILYFEED_EML_TMPL;
		include_once("$D_R/lib/email_alert/_lib.php");
		$qry="select S.id, S.email from subscription S,email_alert_authorsubscribe EA where S.id=EA.subscriber_id and EA.email_alert='1' and (S.trial_status<>'inactive' or S.trial_status is null) and EA.subscriber_id in (select subscriber_id from email_alert_authorsubscribe where author_id regexp ',$contribId,')";

		$result = exec_query($qry);
		$numrows = count($result);
		$leftrows=$numrows;
		$size=$dfSizeEmailAlert;
		if($numrows != 0)
		{
			foreach($result as $key=>$value)
			{
				if($to==''){
					$to	= $value['email'];
			  	}else{
					$to = $to.",".$value['email'];
			  	}
				if(($numrows>$dfSizeEmailAlert) && ($key==$size-1) && ($leftrows>$dfSizeEmailAlert)){
					$fileList=$this->dfEmailList($to);
					$leftrows=$numrows-$key;
					$size=$size + $dfSizeEmailAlert;
					$this->callbulkmailer($NotifyDailyFeedTo,$dailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$fileList);
					$to='';
			    }
			  // file size > no. of emails in the file list and no. of records left < no. of emails in the file list
			  	if(($numrows>$dfSizeEmailAlert) && ($key==$numrows-1) && ($leftrows<=$dfSizeEmailAlert)){
					$fileList=$this->dfEmailList($to);
					$this->callbulkmailer($NotifyDailyFeedTo,$dailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$fileList);
					$to='';
			  	}
			  // file size < no. of emails in the file list
			    if(($numrows<=$dfSizeEmailAlert) && ($key==$numrows-1)){
					$fileList=$this->dfEmailList($to);
					$this->callbulkmailer($NotifyDailyFeedTo,$dailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$fileList);
			  	}
				//$this->sendMail($to,'support@minyanville.com',$dfTitle,$dfId);
			}
		}
	}

	/*function sendMail($NotifyDailyFeedTo,$NotifyDailyFeedFrom,$dfTitle,$dfId)
	{
		global $HTPFX,$HTHOST,$D_R;
		$to=$NotifyDailyFeedTo;
		$from=$NotifyDailyFeedFrom;
		$subject=$dfTitle;
		$id=$dfId;
		$DAILYFEED_EML_TMPL=$HTPFX.$HTHOST."/emails/_eml_daily_feed_update.htm";
		echo "<br>",$DAILYFEED_EML_TMPL;
		if($id){
			mymail($email,$from,$subject,inc_web("$DAILYFEED_EML_TMPL?id=$id"));
		}
	}*/

	function dfEmailList($to){
	global $D_R;
	if($to!="")  // send mail only if $to has email addresses
	{
		$fileList="/tmp/email_alert._".mrand().".list";
		  $myList=explode(",",$to);
		   foreach($myList as $i=>$v)
			 {
				if(!($myList[$i]=trim($v)))
				{
					unset($myList[$i]);
					continue;
				}
			 }
			 $myList=implode("\n",$myList);
			 write_file($fileList,$myList);
			unset($myList);
	}
	 return($fileList);
	}

	function callbulkmailer($NotifyDailyFeedTo,$NotifyDailyFeedFrom,$dfTitle,$DAILYFEED_EML_TMPL,$dfId,$fileList){
		global $D_R,$dailyFeedFrom;
		if(!$dfId)
			return;
		$to=$NotifyDailyFeedTo;      // mail to support@minyanville.com
		$NotifyDailyFeedFrom = $dailyFeedFrom;
		$from= $NotifyDailyFeedFrom;  // mail to Minyanville Mailing List <support@minyanville.com>
		$subject=$dfTitle;
		$msgfile="/tmp/_".mrand().".eml";   // mrand function in _misc.php give getmicrotime();
		$msghtmlfile="$D_R/assets/data/".basename($msgfile);
		$msgurl=$DAILYFEED_EML_TMPL."?id=$dfId";   // $DAILYFEED_EML_TMPL in lib/_exchange_config.php
		//write out file
		$mailbody=inc_web($msgurl);
		//write out file as web page
		write_file($msghtmlfile,$mailbody);
		write_file($msgfile,mymail2str($to,$from,$subject,$mailbody));
		/*------send message------------------*/
		bulk_mailer($fileList,$msgfile,$from);
	}

	function prepareFormData($id){ // for existing feed
		$this->id=$id;
		$arrFeed=$this->getDailyFeed($id);
		$arrFeed['topic']=$this->getTopics('daily_feed',$id);
		$arrFeed['ticker']=$this->getTickers($id,$this->contentType);
		$sourceURL=$this->getResource($id,$this->contentType);
		$arrFeed['source'] = $sourceURL['source'];
		$arrFeed['source_link'] = $sourceURL['source_link'];
		return $arrFeed;
	}

	function restoreFormData($aid){ // for existing feed
		$qry="SELECT * FROM daily_feed_draft WHERE admin_id='".$aid."'";
		$arr=exec_query($qry);

		$arrFeed['id']=$arr[0]['id'];
		$arrFeed['title']=stripslashes($arr[0]['title']);
		$arrFeed['excerpt']=stripslashes($arr[0]['excerpt']);
		$arrFeed['layout_type']=$arr[0]['layout_type'];
		$arrFeed['body']=$arr[0]['body'];

		$arrFeed['contrib_id']=$arr[0]['contrib_id'];

		$objContrib= new contributor();
		$contribName=$objContrib->getContributor($arr[0]['contrib_id'],$name=NULL);
		$arrFeed['contributor']=$contribName['name'];

		$arrFeed['position']=stripslashes($arr[0]['position']);
		//$currenttime=time(date("G"),date("i"),0,date("m"),date("j"),date("Y"));
		$arrFeed['publish_date']=$arr[0]['publish_date'];

		$arrFeed['quick_title']=stripslashes($arr[0]['quicktitle']);


		$arrFeed['is_buzzalert']=($arr[0][is_buzzalert]=="1")?1:0;
		$arrFeed['topic']=$arr[0]['feedtopic'];
		$arrFeed['ticker']=$arr[0]['feedticker'];
		$arrFeed['source']=$arr[0]['feedsource'];
		$arrFeed['source_link']=$arr[0]['feedsource_link'];
		$arrFeed['is_yahoofeed'] = ($arr[0][is_yahoofeed]=="1")?1:0;
		$arrFeed['is_approved']=0;
		$arrFeed['is_live']=0;
		$arrFeed['is_draft']=0;

		$arrFeed['imagenamedf']=$arr[0]['imagenamedf'];

		if($arr[0]['dailyfeedimage'])
		{
			$arrFeed['chart1']=$arr[0]['chart1'];
			$arrFeed['dailyfeedimage']=$arr[0]['dailyfeedimage'];

		}

		//$arrFeed['source_link'] = $sourceURL['source_link'];

		return $arrFeed;
	}

	function prepareDailyFeeddata($data){ // new feed
		//htmlprint_r($data);
		$this->creation_date=$data['feed']['creation_date'];
		$this->title=addslashes(mswordReplaceSpecialChars(stripslashes($data['feed']['title'])));
		$this->excerpt=addslashes(mswordReplaceSpecialChars(stripslashes($data['feed']['excerpt'])));
		$this->layout_type=$data['feed']['layout_type'];
		$this->body=addslashes(mswordReplaceSpecialChars(stripslashes($data['feed']['body'])));
		$this->contrib_id=$data['feed']['contrib_id'];
		$this->position=addslashes(mswordReplaceSpecialChars(stripslashes($data['feed']['position'])));
		$currenttime=time(date("G"),date("i"),0,date("m"),date("j"),date("Y"));
		$this->publish_date=time($data['hour'],$data['minute'],0,$data['mo'],$data['day'],$data['year']);
		$this->chart1=$data['chart1'];
		$this->inputvalue=$data['inputvalue'];
		$this->quicktitle=addslashes(mswordReplaceSpecialChars(stripslashes($data['quicktitle'])));
		$this->dailyfeedimage=$data['dailyfeedimage'];
		$this->imagenamedf=$data['imagenamedf'];
        $this->is_buzzalert=($data['feed'][is_buzzalert]=="1")?1:0;
        $this->feedtopic=$data['feedtopic'];
		$this->feedticker=$data['feedticker'];
		$this->feedsource=$data['feedsource'];
		$this->feedsource_link=$data['feedsource_link'];
		$this->google_editors_pick = ($data['google_editors_pick']=="1")?1:0;
		$this->google_news_standout = ($data['google_news_standout']=="1")?1:0;
		$this->is_yahoofeed = ($data['feed'][is_yahoofeed]=="1")?1:0;
		$this->is_featured=($data['is_featured']=="1")?1:0;
		$this->is_approved=0;
		$this->is_live=0;
		$this->is_draft=0;
		if($data['inputvalue']=='approve')
		{
			$this->is_approved=1;
			$this->is_live=1;
		}
		if($data['inputvalue']=='draft')
		{
			$this->is_draft=1;
		}
		if($this->publish_date > $currenttime){
			$this->is_live=0;
		}
		$this->publish_date=mysqlNow($this->publish_date);
		//verify Invalid ticker
		if($this->is_yahoofeed && $this->is_live)
		{
		$verifyticker=$this->getTickerStock($this->feedticker);
		if($verifyticker){ //  New ticker not verified from Yahoo & Feed is not approved
			$this->is_draft=0;
			$this->is_approved=0;
			$this->error=1;
			$this->errorMsg="Invalid ticker";
			if($this->is_yahoofeed){
				$typeEmail="DailyFeed";
				$titleEmail=$this->title;
				$objBuzz= new Buzz();
				$objBuzz->sendYahooInvalidTickerEmail($titleEmail,$typeEmail,$verifyticker,$isYahoo);
			}
		}
		}

	}

	function setDailyFeedData($id=NULL){ // insert in tables
		
		global $D_R;
		include_once("$D_R/lib/_content_data_lib.php");
		$objYahoo = new YahooSyndication();	
		$feedData=get_object_vars($this);
		$feed=$feedData;
		unset($feed['contentType']);
		unset($feed['contentId']);
		unset($feed['chart1']);
		unset($feed['inputvalue']);
		unset($feed['quicktitle']);
		unset($feed['dailyfeedimage']);
		unset($feed['imagenamedf']);
		unset($feed['feedtopic']);
		unset($feed['feedticker']);
		unset($feed['feedsource_link']);
		unset($feed['feedsource']);
		unset($feed['is_featured']);
		unset($feed['google_editors_pick']);
		unset($feed['google_news_standout']);
		if($feedData['excerpt'])
		{
			$thread_teaser=substr(trim(addslashes(mswordReplaceSpecialChars(stripslashes($feedData['excerpt'])))),0,400);
		}
		else
		{
			$thread_teaser = substr(trim(addslashes(mswordReplaceSpecialChars(stripslashes($feedData['body'])))),0,400);
		}
		
		if(empty($id)){ // insert data in dailyfeed table
			$id=$this->setDailyFeed($feed);

			if($id){
				$itemId=$id;
				$this->setDailyFeedThread($id,$feedData['contrib_id'],$feedData['title'],$thread_teaser);
				$this->setTopic($feedData['feedtopic'],$id);
				$this->setTickers($feedData['feedticker'],$id);
				$this->setQuickTitleDailyFeed($id,$feedData['quicktitle'],$this->contentType);
				$this->setResource($id,$feedData['feedsource'],$feedData['feedsource_link'],$this->contentType);
				if($feedData['dailyfeedimage']){
					$this->getImageDailyfeedByid($feedData['dailyfeedimage'],$id);
				}else{
				   if($feedData['imagenamedf']){
						$this->setImageDailyFeed($id,$feedData['chart1'],$feedData['imagenamedf'],$this->contentType);
				   }
				}
				if($feedData['is_yahoofeed'] == '1' && $feedData['is_live'] == "1" && $feedData[is_approved] == "1" )
				{
					$objYahoo->generateYahooXml($id,$feed,'Feeds','yahoo');
				}
				$this->setFeedGoogleSynd($feed_editor_pick, $feed_google_standout,$id);

				$objContent = new Content($this->contentType,$id);
				$url = $objContent->getDailyFeedURL($id);
				$objContent->setDailyFeedMeta();
				$this->setFeaturedFeed();
				$this->error=1;
				/***** Publish Data ******/
				if($this->is_live=="1" &&  $this->is_buzzalert=="1")
				  {
				     $this->synidicateFeed($id);
				  }
				/***** Publish Data ******/
				$this->errorMsg="Your content was submitted";
			}
		}else{ // update data in dailyfeed table
			$this->id=$id;
			$this->updateDailyFeed($feed,$id);
			$this->updateDailyFeedThread($id,$feedData['contrib_id'],$feedData['title'],$thread_teaser);
			if($feedData['dailyfeedimage']){
				$this->getImageDailyfeedByid($feedData['dailyfeedimage'],$id);
			}else{
			   $urlimage=$IMG_SERVER."/assets/dailyfeed/uploadimage/".date('mdy').'/'.$feedData['chart1'];
			   if($feedData['imagenamedf']){
					$this->updateImageDailyFeed($id,$urlimage,$feedData['imagenamedf'],$this->contentType);
			   }
			}
			if($feedData['feedticker']){
				$this->setTickers($feedData['feedticker'],$id);
			}else{
				$this->deleteTickers($id);

			}

			if($feedData['feedtopic']!=""){
				$this->setTopic($feedData['feedtopic'],$id);
			}
			else
			{
				$this->deleteTopic($id);
			}
			$this->setQuickTitleDailyFeed($id,$feedData['quicktitle'],$this->contentType);
			$this->setResource($id,$feedData['feedsource'],$feedData['feedsource_link'],$this->contentType);

			if($feedData['is_yahoofeed'] == '1' && $feedData['is_live'] == "1" && $feedData[is_approved]== "1" )
			{
				$YahooSyndId		=	getSyndication($id,$this->contentType,'yahoo');
				if($YahooSyndId && $YahooSyndId['id']!='')
				{
				}
				else
				{
					$objYahoo->generateYahooXml($id,$feed,'Feeds','yahoo');
				}
			}
			if(!$this->id){
				$this->error=1;
				$this->errorMsg="There is an error while saving your article. <br />Here are the details of the error:<br>".mysql_error();
				return;
			}
			$objContent = new Content($this->contentType,$id);
			$url = $objContent->getDailyFeedURL($id);
			$objContent->updateContentSeoUrl($id,"18",$url);
			$objContent->setDailyFeedMeta();
			$this->setFeaturedFeed();
			$this->setFeedGoogleSynd($feed_editor_pick, $feed_google_standout,$id);

            /*Meccache object use for home page Dailyfeed section*/
			if($feedData['is_live']!="1" && $this->is_live=="1" &&  $this->is_buzzalert=="1")
				  {
				      $this->synidicateFeed($id);
				  }
			$this->errorMsg="The post was updated";
			}
	}

	function setFeedGoogleSynd()
	{
		$GNArticle['item_id']	= $this->id;
		$GNArticle['item_type']	='18';

		$GNArticle['syndicated_on'] = date("Y-m-d H:i:s");
		$GNArticle['is_hosted_by_minyanville']	= '18';
		$GNArticle['syndication_updated_on'] = date("Y-m-d H:i:s");

		if($this->google_editors_pick =="1")
		{
			$GNArticle['syndication_channel']	= 'google_editors_pick';
			$GNArticle['is_syndicated'] = $this->google_editors_pick;

			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_editors_pick','item_id'=>$this->id,'item_type'=>'18'));
		}
		else
		{
			if(empty($this->google_editors_pick))
			{
				$GNArticle['syndication_channel']	= 'google_editors_pick';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_editors_pick','item_id'=>$this->id,'item_type'=>'18'));
			}
		}
		if($this->google_news_standout =="1")
		{
			$GNArticle['syndication_channel']	= 'google_news_standout';
			$GNArticle['is_syndicated'] = $this->google_news_standout;
			insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_news_standout','item_id'=>$this->id,'item_type'=>'18'));
		}
		else
		{
			if(empty($this->google_news_standout))
			{
				$GNArticle['syndication_channel']	= 'google_news_standout';
				$GNArticle['is_syndicated'] = '0';

				insert_or_update("content_syndication",$GNArticle,array('syndication_channel'=>'google_news_standout','item_id'=>$this->id,'item_type'=>'18'));
			}
		}

	}

	function getFeaturedFeed()
	{
		$syndication_channel = 'ts_featured';
		$sqlCheckSyndication="SELECT id,is_syndicated FROM content_syndication WHERE item_id='".$this->id."' and item_type='".$this->contentType."' and syndication_channel='".$syndication_channel."'";
		$arSyndResult = exec_query($sqlCheckSyndication,1);
		return $arSyndResult;
	}
	function setFeaturedFeed()
	{
		$syndication_channel = 'ts_featured';
		$arSyndResult = $this->getFeaturedFeed();
		if(count($arSyndResult) > 0)
		{
			if(($this->is_featured == '1' && $arSyndResult['is_syndicated'] == '0') || ($this->is_featured == '0' && $arSyndResult['is_syndicated'] == '1'))
			{
				$arSyndData['is_syndicated'] = $this->is_featured;
				update_query("content_syndication",$arSyndData,array('id' => $arSyndResult['id']));
			}
		}
		else
		{
			if($this->is_featured == '1')
			{
				$arSyndData['item_id'] = $this->id;
				$arSyndData['item_type'] = $this->contentType;
				$arSyndData['syndication_channel'] = $syndication_channel;
				$arSyndData['is_syndicated'] = '1';
				$arSyndData['syndicated_on'] = mysqlNow();
				insert_query("content_syndication",$arSyndData);
			}
		}

	}
	/****************************/
      function synidicateFeed($feed_id)
      {
	  //this just posts to the banter app now. much much lighter.
	   $feed=$this->getDailyFeed($feed_id);

	   if($feed)
	    {


	    global $HTPFX,$HTHOST;
	//buzz and banter alert
         if($feed[publish_date]=="0000-00-00 00:00:00")
		    {
			   $date=$feed[creation_date];
			}
		 else
		    {
			   $date=$feed[publish_date];
			}

	   $msg='<font color="#ff6001"><b>New MV Premium at '.date("g:i a",strtotime($date)).
	     '</font><br /> <a href="javascript:void(0);"' .
	     ' onclick="launchPage(\'' . $HTPFX.$HTHOST.$this->getDailyFeedUrl($feed_id) .'\',\'dailyfeed'. $feed_id . '\',0)"
	   >' .
	     mswordReplaceSpecialChars($feed[title]) .'</a><br /> By ' .$feed[contributor] . '</b>';
	 if($feed[category_code]!="schoolhouse")
	     {
		$this->alertFeed_Banter($msg,1,$feed[contributor],$feed_id,$feed[title],$feed[ContId],$date);
	     }
        }
     }
     function alertFeed_Banter($msg="",$showInChat=0,$contributor=null,$feed_id=null,$title,$contrib_id,$date){
	  debug("alertFeed_Banter($msg,$showInChat)");
	  global $BANTER_ALERT_FILE;
	  $objAction= new Action();
	  write_file($BANTER_ALERT_FILE,$msg);
	  if($showInChat){
		$ins=array(
			body=>addslashes(strip($msg)),
			is_live=>1,
			"date"=>$date,
			"updated"=>$date,
			"publish_date"=>$date,
			 show_in_app=>1,
			 show_on_web=>0,
			 approved=>1,
			author=>$contributor,
			position=>$feed_id,
			login=>"(feed_automated)",
			title=>addslashes(strip($title)),
			contrib_id=>$contrib_id);
		$buzzId=insert_query("buzzbanter",$ins);
		$buzzIns=$ins;
		$buzzIns['id']=$buzzId;

		insert_query("buzzbanter_today",$buzzIns);
		$this->writeLatestFeed_BanterPostJSON();
		$objAction->trigger('buzzDataPublish',$buzzId);
	    }
       }
    function writeLatestFeed_BanterPostJSON()
       {
	global $BANTER_LATEST_POST,$BANTER_LATEST_S4, $BANTER_LATEST_S5, $HTPFX,$HTADMINHOST;
	//write_file($BANTER_LATEST_POST, time());
	$writeAdmin= file_get_contents($HTPFX.$HTADMINHOST."/admin/writeBanterAlert.php?timestamp=".time());
	return $writeAdmin;
      }

      /****************************/



	function deleteDailyFeeddata($id){
		
		global $D_R;
		include_once("$D_R/lib/_content_data_lib.php");
	   $objcontent=new Content("daily_feed","");
	   $objCache= new Cache();
	   if($id){
		$feed_remove["is_deleted"]=1;
		$this->updateDailyFeed($feed_remove,$id);
		$objcontent->removeUnapprovedItems($id,'daily_feed');
		$this->deleteDailyFeedThread($id);
		$this->deleteTickers($id);
		$this->deleteResource($id);
		$objCache->deleteDailyFeed($id);
		/*Used for HP DailyFeed.*/
		$objCache->deleteKey("module_339");
		$objCache->deleteKey("module_351");
		$err="The feed was removed";
		unset($id);
	 }
	}

	function prepareFeedPostdata($data){
	  global $IMG_SERVER;
	    // $pagedata=$data;
		$objContrib= new contributor();

		$pagedata['quick_title']=$data['quicktitle'];
		$pagedata['title']=$data['feed']['title'];
		$pagedata['title']=$data['feed']['title'];
		$pagedata['excerpt']=$data['feed']['excerpt'];
		$pagedata['layout_type']=$data['feed']['layout_type'];
		$pagedata['body']=$data['feed']['body'];
		$contribName=$objContrib->getContributor($data['feed']['contrib_id'],$name=NULL);
		$pagedata['contributor']=$contribName['name'];

		$pagedata['position']=$data['feed']['position'];
		$pagedata['topic']=$data['feedtopic'];
		$pagedata['ticker']=$data['feedticker'];
		$pagedata['source']=$data['feedsource'];
		$pagedata['source_link']=$data['feedsource_link'];
        if($data['feed']['is_yahoofeed']){
			$pagedata['is_yahoofeed']=1;
		}
	    if($data['feed']['is_buzzalert']==1){
			$pagedata['is_buzzalert']=1;
		}
		 if($data['is_featured']==1){
			$pagedata['is_featured']=1;
		}

		$pagedata['chart1']=$IMG_SERVER."/assets/dailyfeed/uploadimage/".date('mdy').'/'.$data['chart1'];
		return $pagedata;

	}


}

//------------------------------------- New Class Start Here ---------------------------------------------//
Class fileUploadDailyFeed {
	function showUploadImage(){
	  ?>
	  <div style="float:left;margin-top:1px;">
	  <input name="urlupload" type="text" value="paste image url here" id="urlupload" onfocus="focusedsymbol(this)" onblur="blurredsymbol(this)" />
	  </div><div class="btn_upload ">
	  <img src="<?=$IMG_SERVER?>/images/tickertalk/uploadChart.jpg" border="0" align="absmiddle" vspace="0" alt="Upload" onclick="setuploadchart();"/>
	  </div>
	  <?
	}

	function setUploadUrlImage($url){
	   global $D_R;
		$maxSize = 2097152;
		$arAllowedExt = array("jpg","jpeg","gif","png","bmp");
		$arImageData = getimagesize($url);
		$is_allowed = false;
		if(isset($arImageData['mime']))
		{
			$arFileDetail = explode("/",$arImageData['mime']);
			$fileExt = $arFileDetail[1];
			if(in_array($fileExt,$arAllowedExt))
			{
				$is_allowed = true;
			}
		}
		if(!$is_allowed)
		{
			echo "File type isn't allowed";
			exit(0);
		}
		$newFileName = "url_".rand(2,4)."_".time().".".$fileExt;

		// Create folder of current date.
		$filePath = $this->createUploadFolder();
		$savePath = $filePath."/".$newFileName;
		$pathToImages=$D_R."/assets/dailyfeed/uploadimage/".date('mdy').'/';
		//$pathToThumbs=$D_R."/assets/dailyfeed/uploadimage/thumbnail/".date('mdy').'/';

		$data = file_get_contents($url);
		$fp = fopen($pathToImages.$newFileName, 'w+');
		fwrite($fp, $data);
		fclose($fp);
		//$thumbWidth="165";
		//$imageThumb = $this->createThumbs($pathToImages, $pathToThumbs, $thumbWidth,$newFileName);
		$result=$this->uploadFTPServer($newFileName);
	    echo "FILEID:" . $newFileName; // Return the file name to the script

	}

	function createUploadFolder()
		{
			global $IMG_SERVER,$D_R;
			$pathname=$D_R."/assets/dailyfeed/uploadimage/".date('mdy');
			$mode="775";
			$createfolder=$this->mkdir_recursive($pathname,$mode);
			//$paththumbnail=$D_R."/assets/dailyfeed/uploadimage/thumbnail/".date('mdy');
			//$createfolder=$this->mkdir_recursive($paththumbnail,$mode);
			chmod($pathname, 0777);
			//chmod($paththumbnail, 0777);
			return $pathname;
		}

		public function mkdir_recursive($pathname,$mode)
		{
			is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
			return is_dir($pathname) || @mkdir($pathname, $mode);
	}

	public function uploadFTPServer($file_name)
		{
		global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
		$foldername="dailyfeed/uploadimage";
		switch($serverRsync){
			case "ec2-54-225-111-137.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$foldername.' minyanville@'.$serverS9PublicDns.':'.$D_R.'/assets/dailyfeed');

			break;
			case "ec2-54-225-111-153.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$foldername.' minyanville@'.$serverS8PublicDns.':'.$D_R.'/assets/dailyfeed');
			break;
		}
	}

	function createThumbs($pathToImages,$pathToThumbs,$thumbWidth,$imagename)
		{
		  // open the directory
		  $dir = opendir($pathToImages);
		  $fname=$imagename;
		  // loop through it, looking for any/all JPG files:
			// parse path for the extension
			$info = pathinfo($pathToImages . $fname);
			// continue only if this is a JPEG image
			  // load image and get image size
			  $extension = pathinfo("{$pathToImages}{$fname}");
			 $extension = $extension[extension];
			  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){
			            $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
			          }

			          if($extension == "png") {
			            $img = imagecreatefrompng( "{$pathToImages}{$fname}" );
			          }

			          if($extension == "gif") {
			           	$img = imagecreatefromgif( "{$pathToImages}{$fname}" );
	          }

			  $width = imagesx( $img );
			  $height = imagesy( $img );
			  // calculate thumbnail size
			  if($width>$thumbWidth)
			  {
	                $new_width = $thumbWidth;
				  	$new_height = floor(($height * $thumbWidth )/$width );
			  }
			  else
			  {
				  $new_width = $width;
				  $new_height = $height;
			  }

			  // create a new temporary image
			  $tmp_img = imagecreatetruecolor( $new_width, $new_height );

			  if($extension == "gif")
			 {
				$trnprt_indx = imagecolortransparent($img);

	      		// If we have a specific transparent color
				if ($trnprt_indx >= 0)
				{
					// Get the original image's transparent color's RGB values
					@$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

					// Allocate the same color in the new image resource
					$trnprt_indx    = imagecolorallocate($tmp_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

					// Completely fill the background of the new image with allocated color.
					imagefill($tmp_img, 0, 0, $trnprt_indx);

					// Set the background color for new image to transparent
					imagecolortransparent($tmp_img, $trnprt_indx);
				}
				//$transparent = imagecolorallocate($tmp_img, "255", "255", "255");
				//imagefill($tmp_img, 0, 0, $transparent);
			 }
			  // copy and resize old image into new image
			  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			  // save thumbnail into a file
			  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
			  {
			      $thumbImage = imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
			  }else if($extension == "png") {
				 $thumbImage = imagepng( $tmp_img, "{$pathToThumbs}{$fname}" );
			  }else if($extension == "gif") {
			      $thumbImage = imagegif( $tmp_img, "{$pathToThumbs}{$fname}" );
	          }
		  	  // close the directory
			  closedir( $dir );
			  if($thumbImage)
			  {
			  	return true;
			  }
			  else
			  {
			  	return false;
			  }
	}
}


?>