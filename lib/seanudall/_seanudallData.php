<? 
class seanudallData{
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
				$this->contentTable=$resGetContentTypeId['item_table'];
				$this->contribId=get_contributor_id_byname("Sean Udall");
			}
		}   

		function getSeanUdallPost($id){
		$qry="Select SP.id,SP.creation_date,SP.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,SP.title,SP.body,SP.publish_date,C.id as 'ContId',C.name contributor,SP.is_draft,SP.position,SP.category_id FROM seanudall_posts SP,contributors C WHERE C.id=SP.contrib_id and SP.id='".$id."'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

		function getDraftSeanPost(){
			$qry="SELECT id,title,creation_date FROM seanudall_posts where is_deleted='0' and (To_days(now()) - TO_DAYS(creation_date)<=30) and is_draft='1' ORDER BY creation_date DESC";
			$result=exec_query($qry);
			if($result){
				return $result;
			}else{
				return false;
			}
		}
		
		function getPostType(){
			$qryPostType="select * from seanudall_category";
			$resultPostType=exec_query($qryPostType);
			if($resultPostType){
				return $resultPostType;
			}else{
				return false;
			}
		}
		
	function insertPost($post){
			$post["creation_date"]=mysqlNow();
			$id=insert_query("seanudall_posts",$post);
			if($id){
				return $id;
			}else{
				return false;
			}
		}
		
		function updateSeanUdallPost($post,$id){
			unset($post['topic']);
			unset($post['ticker']);
			$post["updation_date"]=mysqlNow();
			update_query("seanudall_posts",$post,array(id=>$id));
		}
		
		function checkPostUniqueTitle($title,$post_id = NULL)
		{
			$qry="select id from seanudall_posts where title = '".$title."'";
			if($post_id != NULL)
			{
				$qry .= " AND id !=".$post_id;
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
		
		function setSeanUdallTickers($posttickers,$id){
			$this->id=$id;
			$this->tickers=array_unique(explode(",",trim($posttickers)));
			$sqlDelTickers="delete from ex_item_ticker where item_id='".$this->id."' and item_type='".$this->contentType."'";
			$resDelTickers=exec_query($sqlDelTickers);
			foreach($this->tickers as $ticker){
				if(trim($ticker)=="")
					continue;
				$sqlGetTicker="SELECT id from ex_stock where stocksymbol='".trim($ticker)."'";
				$resGetTicker=exec_query($sqlGetTicker,1);
				if(empty($resGetTicker['id'])){
					$getStockDetails=$this->getSeanUdallStockDetails(trim($ticker)); /*verify ticker from yahoo*/
					if($getStockDetails[0]){
						 $tickerId=$this->setSeanUdallStockTicker($getStockDetails); /*Insert data in the ex_stock table if verify from yahoo*/
					}
				}else{
					 $tickerId=$resGetTicker['id'];
				}
				$exItemTicker['ticker_id']=$tickerId;
				$exItemTicker['item_id']=$this->id;
				$exItemTicker['item_type']=$this->contentType;
				insert_query("ex_item_ticker",$exItemTicker);
			}
		}
		
		
		
	
		
   function deleteSeanUdallThread($keys){
	$qry="delete from ex_thread where find_in_set(content_id,'$keys') and content_table='".$this->contentTable."'";
	exec_query_nores($qry);
   }
   
   function deleteSeanUdallTopic($keys){
	  $qry="delete from ex_item_tags where  ind_in_set(item_id,'$keys') and item_type='".$this->contentType."'";
	  exec_query_nores($qry);
	}
	
	function deleteSeanUdallTickers($keys){
	    $qry="delete from ex_item_ticker where find_in_set(item_id,'$keys') and item_type='".$this->contentType."'";
		exec_query_nores($qry);
	}
	
	
	function getSeanUdallTickerStock($ticker){ /*search ticker in ex_stock table if not exist in tt_topic table*/
		$tickerval=explode(',',$ticker);
		$strval=0;
		foreach($tickerval as $row){
			$qry="select id,stocksymbol from ex_stock where stocksymbol='".$row."'";
			$getStockid=exec_query($qry,1);
			if(!$getStockid){
				$validateticker=$this->getSeanUdallStockDetails($row); /*varify ticker from yahoo*/
				if($validateticker[0]){
					 $insertTickerid=$this->setSeanUdallStockTicker($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
				}else{
					$strval=$row;
				}
			}
		}
	  return $strval;
   	}
   
     function getSeanUdallStockDetails($symbolname){ /*Validate ticker from Yahoo and if validate return value of ticker*/
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
	
	function setSeanUdallStockTicker($validateticker){ /*Insert data in ex_stock table*/
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
	
	function setSeanUdallTopic($posttopic,$id){
		global $D_R;
		include_once($D_R."/lib/_content_data_lib.php");
		$objContent = new Content();
		$sqlDelTopics="delete from ex_item_tags where item_id='".$this->id."' and item_type='".$this->contentType."'";
		$resDelTopics=exec_query($sqlDelTopics);
		$this->tags=explode(",",$posttopic);
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
			$exItemTag['item_type']=$this->contentType;
			insert_query("ex_item_tags",$exItemTag,array("tag_id"=>$exItemTag['tag_id'],"item_id"=>$this->id,"item_type"=>$this->contentType));
		}
	}
	
	function getFilterWords($body){
	    $title="";
		$title=strtolower(str_replace("-"," ",$body));
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
	
	/*function deleteSeanUdallTopic($id){
		$itemtagqry = "select id from ex_item_tags where item_id = '$id'";
		$getids=exec_query($itemtagqry);
		foreach($getids as $getidkey => $getidval)
		{
			del_query("ex_item_tags", "id", $getidval[id]);
		}

	}*/
	
	function updateSeanUdallThread($id,$authorid,$title,$teaser){
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
		update_query("ex_thread",$threadval,array(content_id=>$id,'content_table'=>$this->contentTable));
   }
   
   function getSeanUdallTopics($id){
		$tagquery="SELECT xbt.item_id, xbt.tag_id, xt.tag as tagname, xt.url as tagurl FROM ex_item_tags xbt, ex_tags xt where xbt.item_id='".$id."' and xt.id = xbt.tag_id and xbt.item_type ='".$this->contentType."'";
		$tagres = exec_query($tagquery);
		if($tagres)
		{
			return $tagres;
		}
		else
		{
			return false;
		}
		
		/*$pagetag[tag]="";
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

		return $pagetag[tag];*/

	}
	
	function getSeanUdallTicker($id){
		$qry="select ES.id,ES.stocksymbol from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$this->contentType."'";
		$result=exec_query($qry);
		if($result){
		   	return $result;
			//$data=implode(",",$val);
			//return $data;
		}else{
			return false;
		}
	}
	
	function setSeanUdallThread($id,$authorid,$title,$teaser){
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
		$threadval['content_table']=$this->contentTable;
		$threadval['content_id']=$id;
		$threadval['is_user_blog']=0;
		$threadval['approved']='1';
		$threadval['title']=htmlentities($title,ENT_QUOTES);
		$threadval['teaser']=$teaser;
		$threadvalue=insert_query('ex_thread',$threadval);
		return $threadvalue;
   }
	/**********************************  Performance Functions *************************************/
	function setPerformance($fileName){
		if($fileName!='')
		{
	    	$params['file_name']=$fileName;
			$id=insert_query("seanudall_performance",$params,$safe=0);
		}
	}
	function getPerformance()
	{
		global $D_R;
		$sqlRecentPerformance 	= "select file_name from seanudall_performance where file_name!='' order by id desc limit 0,1";
		$resultsPerformance		= exec_query($sqlRecentPerformance,1);
		if($resultsPerformance)
		{
			return $resultsPerformance['file_name'];
		}
		else
		{	
			return false;
		}
	}
	
	function uploadPerformanceSync($servername,$foldername){
       	global $D_R;
		foreach($servername as $value){
			shell_exec("ssh -p16098 root@".$value." -C 'rsync -avz -e \"ssh -p16098\" root@".$_SERVER['SERVER_ADDR'].":".$D_R."/assets/$foldername ".$D_R."/assets'");
		}
	}
	
	   /*Main pages function*/
   
   function getCategoryId($categoryName){
		$qry="select id from seanudall_category where category_name ='".$categoryName."'";
		$result=exec_query($qry,1);
		if(is_array($result)){
			return $result['id'];
		}
   }
   
   function getTopics($id){
		$tagquery="SELECT xbt.item_id, xbt.tag_id, xt.tag as tagname FROM ex_item_tags xbt, ex_tags xt where xbt.item_id='".$id."' and xt.id = xbt.tag_id and xbt.item_type ='".$this->contentType."'";
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
	
	function getTicker($id){
		$qry="select ES.stocksymbol,ES.exchange from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$this->contentType."'";
		$result=exec_query($qry);
		$pageTicker['ticker']="";
		foreach($result as $value)
		{
			if($pageTicker['ticker']=="")
			{
				$pageTicker['ticker'].= $value['stocksymbol'] ;
			}
			else
			{
				$pageTicker['ticker'].=",".$value['stocksymbol'] ;
			}
		}
		return $pageTicker['ticker'];
	}
	
	function getPostUrl($id){
		$qry="select url from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
		$result=exec_query($qry,1);
		if($result){
			return $result['url'];
		}
	}
   
   function getSeanUdallPostsData($categoryName,$offSet,$seanUdallPostLimit){
        global $seanUdallPostLimit;
		
   		$qry="select SP.id,SP.title,SP.body,SP.publish_date,SP.position from seanudall_posts SP,seanudall_category SC where SP.is_approved='1' and SP.is_deleted='0' and SP.is_live='1' and SP.is_draft='0' and SP.category_id=SC.id";
		if($categoryName){
			$qry .=" and SC.category_name='".$categoryName."'";
		}
		$qry .=" order by SP.publish_date desc limit ".$offSet.",".$seanUdallPostLimit."";

		$result=exec_query($qry);
		
		foreach($result as $key=>$value){
			$result[$key]['url']=$this->getPostUrl($value['id']);
		}
		if(is_array($result)){
			return $result;
		}
   }
   
   function getTickerMentioned(){
   global $tickerMentionedLimit;
   	$qry="select ET.ticker_id,ES.stocksymbol,count(ES.stocksymbol) countticker from ex_stock ES,ex_item_ticker ET,seanudall_posts SP where ES.id=ET.ticker_id and ET.item_type='".$this->contentType."' and SP.id=ET.item_id and SP.is_approved='1' and SP.is_live='1' and SP.is_deleted='0' and SP.is_draft='0'  group by ES.stocksymbol order by count(ES.stocksymbol) desc limit ".$tickerMentionedLimit."";
   $result=exec_query($qry);
   if(is_array($result))
   	return $result;
   }
		
/*For Pagination*/
	function getSeanUdallPostCount($catName){
		$qry="select count(SP.id) count from seanudall_posts SP,seanudall_category SC  where SP.category_id=SC.id
and SP.is_live='1' and SP.is_approved='1' and SP.is_draft='0' and SP.is_deleted='0'";
        if($catName){
			$qry .=" and SC.category_name='".$catName."'";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}
		
function getSeanUdallSearch($q,$type,$offset){
		global $seanUdallItemsLimit;
		$offset=$offset*$seanUdallItemsLimit;
   		//$q= mysql_escape_string($q);
		$qry="select distinct item_id as id ,item_type as object_type,title,content,publish_date,description as body from ex_item_meta WHERE  is_live='1'";
			if($type=='text' && $q!='')
			{
				$qry.=" and  content LIKE '%$q%'";
			}
			$qry.=" and author_id='".$this->contribId."' and item_type='".$this->contentType."' order by publish_date desc";
			$qry.=" limit $offset,$seanUdallItemsLimit";
			$result=exec_query($qry);
			if(count($result)>0)
			{			
				return $result;
			}
			else
			{
				return false;
			}
	}
	function getSeanUdallSearchByTopic($q,$type,$offset)
	{
		global $seanUdallItemsLimit;
		$offset=$offset*$seanUdallItemsLimit;
   		$q= mysql_escape_string($q);
		$qry="Select SP.id,SP.creation_date,SP.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,SP.title,SP.body,substring(SP.body,0,100) as description,SP.publish_date,C.id as 'ContId',C.name contributor,SP.is_draft,SP.position,SP.category_id FROM seanudall_posts SP,contributors C WHERE C.id=SP.contrib_id and SP.is_approved='1' and SP.is_deleted='0' and SP.is_live='1'";
	    if($type=='tag')
	    {
		  $itemid=$this->getTopicByTag($seanUdallItemsLimit,$q,$offset);
		  $qry.=" and SP.id in (".$itemid.") order by display_date desc";
		}
		elseif($type=='tid'){
		 $itemid=$this->getTopicByTicker($seanUdallItemsLimit,$q,$offset);
		 $qry.=" and SP.id  in (".$itemid.") order by display_date desc";
		}
		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function getTopicByTag($seanUdallItemsLimit,$tag,$offset)
	{
		$url='/'.$tag;
		$chkTag = substr($url,-1);
		if($chkTag != '/')
		{
			$url	= $url."/";
		}
		$qry="SELECT xbt.item_id FROM ex_item_tags xbt, ex_tags xt,seanudall_posts SP  where xt.id = xbt.tag_id and xbt.item_type ='".$this->contentType."' and SP.id=xbt.item_id and SP.is_approved='1' and SP.is_deleted='0' and xt.url='".$url."' order by xbt.item_id desc limit ".$offset.",".$seanUdallItemsLimit;
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
	function getTopicByTicker($seanUdallItemsLimit,$tid,$offset)
	{
		 $qry="select EIT.item_id from ex_item_ticker EIT,seanudall_posts SP where EIT.item_type='".$this->contentType."' and EIT.ticker_id='".$tid."' and EIT.item_id=SP.id and SP.is_approved='1' and SP.is_deleted='0' and SP.is_live='1' order by item_id desc limit ".$offset.",".$seanUdallItemsLimit;
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
	function getPostData($id)
	{
		$qry="Select SP.id,SP.creation_date,SP.updation_date,IF(publish_date = '0000-00-0000:00:00',creation_date,publish_date) AS display_date,SP.title,SP.body,SP.publish_date,C.id as 'ContId',C.name contributor,SP.is_draft,SP.position,SP.category_id FROM seanudall_posts SP,contributors C WHERE C.id=SP.contrib_id and SP.id='".$id."' and SP.is_approved='1' and SP.is_deleted='0' and SP.is_live='1'";
		$result=exec_query($qry,1);
		if($result){
			return $result;
		}else{
			return false;
		}
	
	}
	function getPostDetail($url)
	{
	   $qry="SELECT item_id FROM content_seo_url WHERE LOWER(url) LIKE '%".$url."%' AND item_type='".$this->contentType."' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			return $result['item_id'];
		}
		else
		{
			return false;
		}
	}
	
	function getCategoryName($id){
		$qry="select  SC.category_name from seanudall_posts SP,seanudall_category SC where SP.id='".$id."' and SC.id=SP.category_id";
		$result=exec_query($qry,1);
		if(is_array($result)){
			return $result['category_name'];
		}
	}
	
	function redirectLandingPage(){
		if(!$_SESSION['SeanUdall']){
			location($HTPFX.$HTHOST.'/seanudall/');
			exit;	
		}
	}
		
}
?>