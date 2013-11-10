<?php
global $D_R;
include_once($D_R."/lib/config/_sitemap_config.php");
include_once($D_R."/lib/_image_rsync.php");
include_once("$D_R/lib/config/_rsync_config.php");
class SiteMap{
	function generateSiteMap($section=NULL){
		global $D_R,$HTPFX,$HTHOST,$prioity,$changefreq,$sitemapfile;
		if(substr($section,0,1)=="'"){
			$section=substr(substr($section,1),0,-1);
			$findDirSection=substr($section,strpos($section,",")+2);
		}else{
			$findDirSection=$section;
		}
		$strSubSections="";
		if($section){
			$sitemap="<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

			$sqlSectionID="SELECT section_id FROM section WHERE `name` = '".$section."' AND is_active='1' ";
			$resSectionID=exec_query($sqlSectionID,1);

			if ($resSectionID) {
				$sqlSubSections="SELECT UPPER(`name`) name FROM section WHERE is_active='1' AND parent_section='".$resSectionID['section_id']."'";
				$resSubSections=exec_query($sqlSubSections);
				if ($resSubSections){
					$index=0;

					foreach ($resSubSections as $key => $val){
						if ($index>0) $strSubSections.=",";
						$strSubSections.="'$val[name]'";
						$index++;
					}
				}
			}
			if ($strSubSections!="") $strSubSections.=",'$section'";
			else $strSubSections.="'$section'";
			//$section.= $strSubSections;
			$sqlGetAllSectionArticles="select id,url from ex_item_meta where is_live='1' and item_type='1' and section in (".$strSubSections.") order by item_id desc";
			$resGetAllSectionArticles=exec_query($sqlGetAllSectionArticles);

			foreach($resGetAllSectionArticles as $article){
				$sitemap.="<url>\n";
				$sitemap.="<loc>http://".$HTHOST.$article['url']."</loc>\n";
				$sitemap.="<changefreq>".$changefreq."</changefreq>\n";
				$sitemap.="<lastmod>".date('Y-m-d')."</lastmod>\n";
				$sitemap.="<priority>".$prioity."</priority>\n";
				$sitemap.="</url>\n";
			}
			$sitemap.="</urlset>";

			file_put_contents($sitemapfile[$findDirSection],$sitemap);
		}
	}

function generateDailyFeedSiteMap($item,$item_type,$product=null){
		global $D_R,$HTPFX,$HTHOST,$prioity,$changefreq,$sitemapfile;
		include_once("$D_R/lib/_content_data_lib.php");
		if($item){
			$sitemap="<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
			$sqlGenerateSitemapXML="select id,url,title,publish_date,item_id from ex_item_meta where is_live='1' and item_type='".$item_type."'";
			if($product=="1"){
				$sqlGenerateSitemapXML.= " AND publish_date<=('".mysqlNow()."'-INTERVAL 1 YEAR)";
			}
			$sqlGenerateSitemapXML.="order by item_id desc";

			$resGenerateSitemapXML=exec_query($sqlGenerateSitemapXML);
			foreach($resGenerateSitemapXML as $row){
				if($item_type=='2'){
				 	$currentDate = date('Y-m-d g:i a');
					$buzzDate = $row['publish_date'];
					$dateDiff = date_difftime($buzzDate,$currentDate);
					$dateDiffVal = intval($dateDiff['d']);
					if($dateDiffVal>=365){
					 	$buzzTitle=mswordReplaceSpecialChars($row['title']);
					 	$objContent	= new Content();
					 	$prodUrl=$objContent->getFirstFiveWords($buzzTitle);
					 	$urlPubDate = date("n/j/Y",strtotime($row['publish_date']));
					 	$row['url'] = "/buzz/buzzalert/".$prodUrl."/".$urlPubDate."/id/".$row['item_id'];
					}
				 }
				$sitemap.="<url>\n";
				$sitemap.="<loc>http://".$HTHOST.$row['url']."</loc>\n";
				$sitemap.="<changefreq>".$changefreq."</changefreq>\n";
				$sitemap.="<lastmod>".date('Y-m-d')."T".date('H:i:s')."</lastmod>\n";
				$sitemap.="<priority>".$prioity."</priority>\n";
				$sitemap.="</url>\n";
			}
			$sitemap.="</urlset>";
			file_put_contents($sitemapfile[$item],$sitemap);
		}
	}

}


Class fileUpload {
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
		$pathToImages=$D_R."/assets/buzzbanter/charts/original/".date('mdy').'/';
		$pathToThumbs=$D_R."/assets/buzzbanter/charts/thumbnail/".date('mdy').'/';

		$data = file_get_contents($url);
		$fp = fopen($pathToImages.$newFileName, 'w+');
		fwrite($fp, $data);
		fclose($fp);

		$thumbWidth="165";
		$imageThumb = $this->createThumbs($pathToImages, $pathToThumbs, $thumbWidth,$newFileName);
		//$this->uploadFTPServer($newFileName);
		$obRsync = new ImageSync();
		//$obRsync->setImageUploadPath("buzz_charts");
		$obRsync->uploadAdminServerImages("buzz_charts_original",$newFileName);
		$obRsync->uploadAdminServerImages("buzz_charts_thumbnail",$newFileName);
		if($imageThumb)
		{
		  echo "FILEID:" . $newFileName; // Return the file name to the script
		}
		else
		{
			echo 'Error in File Upload';
		}

	}

	function createUploadFolder()
		{
			global $IMG_SERVER,$D_R;
			$pathname=$D_R."/assets/buzzbanter/charts/original/".date('mdy');
			$mode="775";
			$createfolder=$this->mkdir_recursive($pathname,$mode);
			$paththumbnail=$D_R."/assets/buzzbanter/charts/thumbnail/".date('mdy');
			$createfolder=$this->mkdir_recursive($paththumbnail,$mode);
			chmod($pathname, 0777);
			chmod($paththumbnail, 0777);
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
		$foldername="buzzbanter/charts";
		switch($serverRsync){
			case "ec2-54-225-111-137.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$foldername.' minyanville@'.$serverS9PublicDns.':'.$D_R.'/assets/buzzbanter');

			break;
			case "ec2-54-225-111-153.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$foldername.' minyanville@'.$serverS8PublicDns.':'.$D_R.'/assets/buzzbanter');
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


class Buzz{
		function addChart($buzzId,$filename){
			global $IMG_SERVER;
			$chart['item_type']='2';
			$chart['item_id']=$buzzId;
			$chart['original_url']=$IMG_SERVER."/assets/buzzbanter/charts/original/".date('mdy').'/'.$filename;
			$chart['thumb_url']=$IMG_SERVER."/assets/buzzbanter/charts/thumbnail/".date('mdy').'/'.$filename;
			$current_size = getimagesize($chart['thumb_url']);
			$chart['thumb_width']=$current_size['0'];
			$chart['thumb_height']=$current_size['1'];
			insert_query("item_charts",$chart);
		}

		function removeChart($buzzId,$chartId){
			$qryRemoveChart="delete from item_charts where item_type='2' and item_id='".$buzzId."' and id='".$chartId."'";
			exec_query($qryRemoveChart);
		}

		function getCharts($buzzId)
		{
			$sqlGetCharts="select * from item_charts where item_type='2' and item_id='".$buzzId."'";
			$resGetCharts=exec_query($sqlGetCharts);
			return $resGetCharts;
		}

		function sendBuzzYahooSyndication($id){
		/*buzz approved send feed to yahoo*/
		$objYahoo = new YahooSyndication();
        $sqlYahoo="select BB.id,BB.title,BB.date,BB.body,BB.contrib_id from buzzbanter BB,content_syndication CS where
BB.id=CS.item_id and BB.is_live='1' and BB.approved='1' and CS.item_type='2' and CS.syndication_channel='yahoo' and CS.is_syndicated='0' and BB.id='".$id."'";
       $getYahooSynd=exec_query($sqlYahoo,1);
       if($getYahooSynd){
            $yahoooFeedData=array();
            $yahoooFeedData['title']=$getYahooSynd['title'];
            $yahoooFeedData['publish_date']=$getYahooSynd['date'];
            $yahoooFeedData['body']=$getYahooSynd['body'];
            $yahoooFeedData['contrib_id']=$getYahooSynd['contrib_id'];
            $itemType="2";
            $objYahoo->generateBuzzYahooXml($id,$yahoooFeedData,$itemType,"yahoo");
       }

	 }

	 function sendYahooInvalidTickerEmail($title,$typeEmail,$verifyticker,$isYahoo){
	     global $yahooInvalidTickerTo,$yahooInvalidTickerFrom,$yahooInvalidTickerBuzzSubject,$yahooInvalidTickerTemplate,$yahooInvalidTickerDailyFeedSubject,$yahooInvalidTickerArticleSubject;
		 switch($typeEmail){
		 	case "BuzzBanter":
				$subject=$yahooInvalidTickerBuzzSubject.' '.$title;
			break;
			case "DailyFeed":
				$subject=$yahooInvalidTickerDailyFeedSubject.' '.$title;
			break;
			case "Article":
				$subject=$yahooInvalidTickerArticleSubject.' '.$title;
			break;
		 }
		 $invalidTicker=implode(",",$verifyticker);
		 $yahooInvalidTickerTemplate=$yahooInvalidTickerTemplate.'?title='.urlencode($title).'&type='.$typeEmail.'&invalidTicker='.$invalidTicker.'&isyahoo='.$isYahoo;
		 mymail($yahooInvalidTickerTo,$yahooInvalidTickerFrom,$subject,inc_web("$yahooInvalidTickerTemplate"));
	 }
}

class Ticker{
	 function getTickerStock($ticker){ /*search ticker in ex_stock table if not exist in tt_topic table*/
	    $tickerval=explode(',',$ticker);
		$invalidTicker=array();
		foreach($tickerval as $row){
			$qry="select id,stocksymbol from ex_stock where stocksymbol='".trim($row)."'";
			$getStockid=exec_query($qry,1);
			if(!$getStockid){
					$validateticker=$this->getstockdetails($row); /*varify ticker from yahoo*/
				if($validateticker[0]){
					 $insertTickerid=$this->settStockTicker($validateticker); /*Insert data in the ex_stock table if verify from yahoo*/
				}else{
						$invalidTicker[]=trim($row);
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

	function deleteTickers($keys,$item_type){
	    $qry="delete from ex_item_ticker where find_in_set(item_id,'$keys') and item_type='".$item_type."'";
		exec_query_nores($qry);
	}

	function setTickers($tickers,$id,$item_type){
				$this->id=$id;
				$this->tickers=array_unique(explode(",",trim($tickers)));
				$sqlDelTickers="delete from ex_item_ticker where item_id='".$this->id."' and item_type='".$item_type."'";
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
					$exItemTicker['item_type']=$item_type;
					insert_query("ex_item_ticker",$exItemTicker);
				}
	}

	function getTickers($id,$item_type){
		if($item_type=='1'){
			$qry="SELECT ES.stocksymbol FROM ex_stock ES,ex_item_tags EIT, ex_tags ET
WHERE ES.stocksymbol=ET.tag AND ET.id=EIT.tag_id AND ET.type_id='1'
 AND EIT.item_id='".$id."' AND EIT.item_type='".$item_type."'";
		}else{
			$qry="select ES.stocksymbol from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$item_type."'";
		}
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
		if($item_type=='1'){
			$qry="SELECT ES.id,ES.stocksymbol,ES.exchange FROM ex_stock ES,ex_item_tags EIT, ex_tags ET
WHERE ES.stocksymbol=ET.tag AND ET.id=EIT.tag_id
 AND EIT.item_id='".$id."' AND EIT.item_type='".$item_type."' GROUP BY ES.stocksymbol LIMIT 0,7";
		}else{
			$qry="select ES.id,ES.stocksymbol,ES.exchange from ex_stock ES,ex_item_ticker ET where ES.id=ET.ticker_id and ET.item_id='".$id."' and ET.item_type='".$item_type."' order by ET.id limit 0,7" ;
		}
		$result=exec_query($qry);
		if($result){
			return $result;
		}else{
			return false;
		}
	}

	function getTickerFromBody($body){
		 //$pattern = '/\(([A-Z^]{1,5})\)/';
		 $pattern = "/\(((\b)?(\^)?[A-Za-z0-9-:. ]{1,20}\b)\)/";
		 preg_match_all($pattern, $body, $stocks_matches);
		 $uniqueStocks=array();
		 foreach($stocks_matches[1] as $id=>$value)
		 {
			$pos = strpos($value,':');
			if($pos!=false){
		 		$value = substr($value,$pos+1);
			   $stockVal=$this->validateStock($value);
			   if($stockVal){
			   		$uniqueStocks[$id]=array(id=>$stockVal['id'],stocksymbol=>$value,exchange=>$stockVal['exchange']);

			   }
			}
		 }
		 $uniqueStocks=array_slice($uniqueStocks,0,6);
		 return $uniqueStocks;
	}

	function validateStock($stock)
	{
		$stockQry="select id,exchange from ex_stock where stocksymbol='$stock'";
		$stocks = exec_query($stockQry,1);
		if($stocks['id']!==""){
			return $stocks;
		}
		return false;
	}


	function array_unique_multidimensional($input)
	{
		$serialized = array_map('serialize', $input);
		$unique = array_unique($serialized);
		return array_intersect_key($input, $unique);
	}

}


?>
