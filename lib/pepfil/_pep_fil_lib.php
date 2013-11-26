<?
class pepFil{
	var $contentType,$contentId;
	function __construct($type="",$id=""){
		$this->contentId=$id;
		if(is_numeric($type))
		{
			$this->contentType=$type;
		}
		else
		{
			if($type!=""){
				$sqlGetContentTypeId="select id,item_table from ex_item_type where item_text='".$type."' or item_table='".$type."'";
				$resGetContentTypeId=exec_query($sqlGetContentTypeId,1);
				$this->contentType=$resGetContentTypeId['id'];
				$this->contentTable=$resGetContentTypeId['item_table'];
				$this->contribId=get_contributor_id_byname("Pep & Fil");
			}
		}
	}

	function getReportDetail($url)
	{
	   $qry="SELECT item_id FROM content_seo_url WHERE LOWER(url) LIKE '%".$url."%' AND item_type='".$this->contentType."' order by item_id desc";
	   $result=exec_query($qry,1);
		if($result){
			return $result['item_id'];
		}
	}

	function getArticleCatList()				//***********************Category List **********************/
	{
		 	$qry="select id,article_type from pep_fil_articletype order by id";
			$result=exec_query($qry);
			if(isset($result)){
				return $result;
			}
			else
			{
				return false;
			}
	}
	function setPepFilArticle($feed){		//***********************Insert the Article**********************/
	if(count($feed)>0)
	{
		$feed["creation_date"]=mysqlNow();
		$id=insert_query("pep_fil_articles",$feed);
		if($id){
			return $id;
		}else{
			return false;
		}
	}
	}
	
	function updatePepFilArticle($feed,$id){		//***********************Update the article **********************/
	   	//unset($feed['ticker']);
		if($id!='')
		{
		$feed["updation_date"]=mysqlNow();
		update_query("pep_fil_articles",$feed,array(id=>$id));
	}
	}
	
	/********************************  functions used for Admin pages ******************************************/
	
	function getPepFilArticle($id){
	$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type from pep_fil_articles a where  a.id='".$id."'";
		$result=exec_query($qry,1);
		if($result){
		  	return $result;
		}else{
			return false;
		}
	}
	
	function checkPepFilArticleTitle($title,$feed_id = NULL)
	{
		$qry="select id from pep_fil_articles where title = '".$title."'";
		if($feed_id != NULL)
		{
			$qry .= " AND id !=".$feed_id;
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
	function getArticlesList($cat)
	{
		$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,at.article_type from pep_fil_articletype at,pep_fil_articles a where a.article_type=at.id and at.id = $cat order by a.id asc";
			$result=exec_query($qry);
			if(isset($result)){
				return $result;
			}
			else
			{
				return false;
			}
	}
	function setPepFilTickers($feedtickers,$id){
				$this->id=$id;
				$this->tickers=array_unique(explode(",",trim($feedtickers)));
				$sqlDelTickers="delete from ex_item_ticker where item_id='".$this->id."' and item_type='".$this->contentType."'";
				$resDelTickers=exec_query($sqlDelTickers);
				foreach($this->tickers as $ticker){
					if(trim($ticker)=="")
						continue;
					$sqlGetTicker="SELECT id from ex_stock where stocksymbol='".trim($ticker)."'";
					$resGetTicker=exec_query($sqlGetTicker,1);
					if(empty($resGetTicker['id'])){
						$getStockDetails=$this->getPepFilStockdetails(trim($ticker)); /*verify ticker from yahoo*/
						if($getStockDetails[0]){
							 $tickerId=$this->setPepFilStockTicker($getStockDetails); /*Insert data in the ex_stock table if verify from yahoo*/		}
					}else{
						 $tickerId=$resGetTicker['id'];
					}
					$exItemTicker['ticker_id']	=	$tickerId;
					$exItemTicker['item_id']	=	$this->id;
					$exItemTicker['item_type']	=	$this->contentType;
					insert_query("ex_item_ticker",$exItemTicker);
				}
	}
	
	function getPepFilTickerStock($ticker){ /*search ticker in ex_stock table if not exist in tt_topic table*/
	    $tickerval=explode(',',$ticker);
		$strval=0;
		foreach($tickerval as $row){
		$qry="select id,stocksymbol from ex_stock where stocksymbol='".$row."'";
   		$getStockid=exec_query($qry,1);
		if(!$getStockid){
				$validateticker=$this->getPepFilStockdetails($row); /*varify ticker from yahoo*/
			if($validateticker[0]){
				 $insertTickerid=$this->setPepFilStockTicker($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
			}else{
					$strval=$row;
			}
      }
	  }
	  return $strval;
   }

	 function getPepFilStockdetails($symbolname){ /*Validate ticker from Yahoo and if validate return value of ticker*/
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
	function setPepFilStockTicker($validateticker){ /*Insert data in ex_stock table*/
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
	/******************************** End - functions used for Admin pages ******************************************/
	
	function getPepFilTickers($id){
	$qry="select ES.stocksymbol from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$this->contentType."'";
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
	
	
	function getMostRecentPDF($id=''){
	$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.is_sent from pep_fil_articles a where a.article_type='1' and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' ";
	if($id!='')
	{
		$qry .= " and a.id=".$id ;
	}
	else
	{
	
		$qry .= " and a.body!='' order by a.publish_date desc limit 1 ";
	}
	$result=exec_query($qry);
	if($result){
			$tickers 	=	$this->getPepFilTickers($result[0]['id']);
			$result['tickers']	= $tickers;
		  	return $result;
		}else{
			return false;
		}

	}
	function getPepFilAllReport($pepfilItemsLimit,$offset,$tag)
	{
	  $offset=$offset*$pepfilItemsLimit;
	  if($tag){
	  		$articleCat ='1';
	  		$result=$this->getPepfilReportsTag($pepfilItemsLimit,$tag,$offset,$articleCat);
			return $result;
	  }
	  else
	  {
			$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type,IF(a.publish_date = '0000-00-0000:00:00',a.creation_date,a.publish_date) AS display_date ,C.id as 'ContId',C.name contributor from pep_fil_articles a,contributors C where a.body!='' and a.article_type='1' and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' and a.contrib_id=C.id  order by a.publish_date desc limit " .$offset.",".$pepfilItemsLimit;
		$result=exec_query($qry);
		if($result){
		  	return $result;
		}else{
			return false;
		}
	 }
	}
	function getPepfilMailbag($pepfilItemsLimit,$offset,$tag)
	{
	  $offset=$offset*$pepfilItemsLimit;
	  if($tag){
	  		$articleCat ='2';
	  		$result=$this->getPepfilReportsTag($pepfilItemsLimit,$tag,$offset,$articleCat);
			return $result;
	  }
	  else
	  {
			$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type,IF(a.publish_date = '0000-00-0000:00:00',a.creation_date,a.publish_date) AS display_date ,C.id as 'ContId',C.name contributor from pep_fil_articles a,contributors C where a.article_type='2' and a.body!='' and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' and a.contrib_id=C.id  order by a.publish_date desc limit " .$offset.",".$pepfilItemsLimit;
		$result=exec_query($qry);
		if($result){
		  	return $result;
		}else{
			return false;
		}
	}
	}
	
	
	function getPepfilReportsTag($pepfilItemsLimit,$tag,$offset,$articleCat)
	{
		$qry="select a.id,a.creation_date,a.updation_date,a.title,a.body,a.is_approved,a.is_deleted,a.publish_date,a.contrib_id,a.position,a.is_draft,a.admin_id,a.is_live,a.article_type,IF(a.publish_date = '0000-00-0000:00:00',a.creation_date,a.publish_date) AS display_date ,C.id as 'ContId',C.name contributor from pep_fil_articles a,contributors C where a.article_type='".$articleCat."' and a.body!='' and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' and a.contrib_id=C.id";
		
		if($tag){
		
		 $itemid=$this->getPepfilTickerItemid($pepfilItemsLimit,$tag,$offset,$articleCat);
		 $qry.=" and a.id in (".$itemid.") order by display_date desc";
		}
		$result=exec_query($qry);
		if($result){
		  	return $result;
		}else{
			return false;
		}
	}
	function getPepfilTickerItemid($pepfilItemsLimit,$tag,$offset,$articleCat)
	{
	   	$qry="SELECT xbt.item_id FROM ex_item_ticker xbt, ex_stock xt,pep_fil_articles a where a.id=xbt.item_id and xbt.ticker_id=xt.id and xbt.item_type ='".$this->contentType."' and a.article_type='".$articleCat."' and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' and xt.stocksymbol='".$tag."' order by xbt.item_id desc limit ".$offset.",".$pepfilItemsLimit;
			
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
	function setMailbagThread($id,$authorid,$title,$teaser){
		global $defaultSudId;
		
		$qry="select id from ex_thread where content_table='".$this->contentTable." and content_id='".$id."'";
	    $result=exec_query($qry,1);
   	    if(!$result['id'])
		{
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
	   else
	   {
	 	 	return $result['id'];
	   }
   }

   function updateMailbagThread($id,$authorid,$title,$teaser){
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

   function deleteMailbagThread($keys)
	{
		$qry="delete from ex_thread where find_in_set(content_id,'$keys') and content_table='".$this->contentTable."'";
		exec_query_nores($qry);
	}
	
	function deletePepFilTickers($keys){
	    $qry="delete from ex_item_ticker where find_in_set(item_id,'$keys') and item_type='".$this->contentType."'";
		exec_query_nores($qry);
	}
	
	function postMailbagComment()
	{
		
	}
	
	
	function getDraftPepFilArticles($cat)
	{
		$qry="SELECT id,title,contrib_id,creation_date FROM pep_fil_articles where is_deleted='0' and (To_days('".mysqlNow()."') - TO_DAYS(creation_date)<=30) and article_type='$cat' ORDER BY creation_date DESC";
		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	/********************************************** Mailer Functions *************************************/
	function sendMailToSubscribers($keys,$CONTRIBUTOR_ID)
	{
			/*Send email to subscribers using via*/
         	global $PEPFIL_FROM, $PEPFIL_ALERT_TMPL;
			$from= $PEPFIL_FROM;
			$subject=htmlentities($this->get_article_title_pepfil($keys),ENT_QUOTES);
			$msgfile="/tmp/pepfil_spam_".mrand().".eml";
			$msghtmlfile="$D_R/assets/data/".basename($msgfile);
			$msgurl=$ETF_ALERT_TMPL.qsa(array(aid=>$keys,prof_id=>$CONTRIBUTOR_ID));
			$mailbody=inc_web($msgurl);
			include_once("$D_R/lib/_via_email_lib.php");
			//$ViaEmailObj=new ViaEmail();
			//$result = $ViaEmailObj->emailDetails($from,$subject,utf8_encode($mailbody),'Pep&Fil');
			//$message="The articles were changed and an email was sent to subscribers.";
			$feed_mail["is_sent"]=1;
			$this->updatePepFilArticle($feed_mail,$keys);
	}
	
	function get_article_title_pepfil($articleid)
	{
		$arrArticleIds=explode(",",$articleid);
		if(count($arrArticleIds)>1){
			return "Articles";
		}
		else
		{
			$qry="select title from pep_fil_articles where id=$articleid";
			$result=exec_query($qry,1);
			if(isset($result)){
				return $result['title'];
			}
		}
	}
	/********************************************** End Mailer Functions *************************************/
	function get_tag_cloud($TAGLIMIT)
	{
	 $sql_tickers  = "select distinct ticker_id,count(ticker_id) as 'countTickers' from ex_item_ticker where item_type='23' group by(ticker_id) order by ticker_id desc limit 0,$TAGLIMIT";
	 $result_tickers  = exec_query($sql_tickers);
	 if($result_tickers){
		$val=array();
		foreach($result_tickers as $row)
		{
			$stock_id		=	$row['ticker_id'];
			$stockCount		=	$row['countTickers'];
			$sql_stock		=	"select stocksymbol from ex_stock where id=$stock_id";
			$result_stock   = 	exec_query($sql_stock,1);
			if($result_stock && count($result_stock)>0)
			{
				$stockName	=	$result_stock['stocksymbol'];
			}
			$val[$stockName] = $stockCount;
		}
	  ksort($val);
	  return $val; 
	}	
   }		
	//********************************************** Performance Sheet Code ****************************/
	function setPerformance($fileName){
	    $params['file_name']=$fileName;
		$id=insert_query("pep_fil_performance",$params,$safe=0);

	}
	
	function uploadPerformanceSync($servername,$foldername){
       	global $D_R;
		foreach($servername as $value){
			shell_exec("ssh -p16098 root@".$value." -C 'rsync -avz -e \"ssh -p16098\" root@".$_SERVER['SERVER_ADDR'].":".$D_R."/assets/$foldername ".$D_R."/assets'");
		}
	}
	function getPerformance()
	{
		global $D_R;
		$sqlRecentPerformance 	= "select file_name from pep_fil_performance order by id desc limit 0,1";
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
	
	function getPepFilSearch($q,$title){
	    global $pepfilSearchContentCount;
		$qry="select count(id) as count from ex_item_meta WHERE  is_live='1' ";
			if(($q)){
				//$qry.=" and content LIKE %".$q."%";
				$qry.=" and content LIKE '%$q%'";
			} elseif($title) {
				$qry.=" and  instr(LOWER(title),'".htmlentities($title,ENT_QUOTES)."')";
			}
			$qry.=" and author_id='".$this->contribId."' and item_type='".$this->contentType."' order by publish_date desc";
			$qry.=" limit $offset,$pepfilSearchContentCount";
			$result=exec_query($qry);
	}
	
	function setPepfilItemTracking($item_id,$item_type){
	   	$params["item_id"]=$item_id;
		$params["item_type"]=$item_type;
		$params["time"]=mysqlNow();
		$id=insert_query("ex_item_tracking_view",$params,$safe=0);
		if(isset($id)){
			return $id;
		}
   }
	
function getPepfilProductType($google_analytics_action=NULL){
		$pepfilDefIds = getprodsubdefid('pepfil',''); // Defined in lib/_module_design_lib
		$arProductResult= getProdSubPriceVal($pepfilDefIds,'SUBSCRIPTION'); // Defined in lib/_module_data_lib
		$arProductDetail = array();
		foreach ($arProductResult as $arProductRow)
		{
			if(strstr($arProductRow['product'], '3 Months'))
			{
				$product_type ='Quarterly';
			}
			elseif(strstr($arProductRow['product'], 'Annual'))
			{
				$product_type ='Annual';
			}
			elseif(strstr($arProductRow['product'], '6 Months'))
			{
				$product_type ='BiAnnual';
			}
			$arProductRow['price_format']='$'.intval($arProductRow['price']);
			$arProductRow['product_type'] = $product_type;
			$arProductRow['google_analytics_product_name'] = 'subPepFil';
			$arProductRow['google_analytics_action'] = $google_analytics_action;
			$arProductDetail[$product_type] = $arProductRow;
		}
		return $arProductDetail;
	}
	
	function getPepfilAllArticleCount($pepfilItemsLimit,$tag)
	{
		if($tag){
			$qry="SELECT count(xit.item_id) count FROM ex_item_ticker xit, ex_stock xs,pep_fil_articles a where xs.id = xit.ticker_id and xit.item_type ='".$this->contentType."' and a.id=xit.item_id and a.is_approved='1' and a.is_live='1' and a.is_deleted='0' and xs.stocksymbol='".$tag."'";
		}else{
			$qry="Select count(a.id) count FROM pep_fil_articles a,contributors C 
WHERE C.id=a.contrib_id and a.is_approved='1' and a.is_deleted='0' and a.is_live='1' and a.article_type='1'and a.body!=''";
		}
		$result=exec_query($qry,1);
		if($result){
			return $result['count'];
		}else{
			return false;
		}
	}
	
	function getPepfilReportTitleLink($id){
		$qryTitle = "select * from content_seo_url where item_id='".$id."' and item_type='".$this->contentType."'";
		$resultTitle = exec_query($qryTitle,1);
		if($resultTitle){
			return $resultTitle['url'];
		}else{
			return false;
		}
	}
	
}

?>