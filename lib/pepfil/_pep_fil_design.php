<?php
global $D_R;
include_once("$D_R/lib/pepfil/_pep_fil_lib.php");
$objDataPepfil= new pepFil();
Class viewPepFill
{
	function pepfilProductHeader($pageName)
	{
		global $PepFilProdName;
		/*if($pageName=='')
		{
			$pageName = 'pepfil_mostrecent';
		}*/
		switch ($pageName){
      	  case 'pepfil_mostrecent':
			 $hm = "selected";
		     break;
		  case 'pepfil_allreports':
			 $v1 = "selected";
			// $width = "Width:927px;";
			 break;
		  case 'pepfil_mailbag':
			$v2 = "selected";	
			// $width = "Width:927px;";
			 break;
		  case 'pepfil_performance':
			$v3 = "selected";
			// $width = "Width:927px;";
			break;
 		  /*default:
			$hm = "selected";
			break;*/
	}
	?>
	<div class="middle-heading">
		<div class="pepfilProdName"><?=$PepFilProdName;?></div>
		 <div class="pepfilHeader">
		 <ul>
		 <li> <a href="/pepfil/mostrecent.htm" class="<?=$hm?>">Most Recent</a></li>
		 <li><a href="/pepfil/allreports.htm" class="<?=$v1?>">All Reports</a></li>
		 <li><a href="/pepfil/mailbag.htm" class="<?=$v2?>">Mailbag & Chats</a></li>
		 <li><a href="/pepfil/performance.htm" class="<?=$v3?>">How We've Done</a></li>
		</ul>
	</div>
	</div>	
	 <div style="clear:both;"></div>
<?
	}
	function displayPepfilLanding()			/******************  Product Landing page ************************/
	{
	}
	
	function displayPDFReport($articleId,$pdfData)	/**************  Display most recent pdf ************************/
	{
	global $HTPFX,$HTHOST,$IMG_SERVER;
	$objPepfil = new pepFil('pep_fil_articles');
	$feedType			=	'18';
    $pdfTitle			=	trim(mswordReplaceSpecialChars($pdfData->title));
	$pdf_date_time 		=	$pdfData->updation_date;
	$pdf_date 			= 	substr($pdf_date_time,0,10);
	$display_date 		=   strtoupper(date('M j, Y h:i A',strtotime($pdfData->display_date)));
	$contributorName	=	trim($pdfData->contributor);
	$contributorId		=	trim($pdfData->contrib_id);
	$pdf_name 			=	$pdfData->body;
	$pdfPosition		=	trim($pdfData->position);
	$stock_tickers		=	$objPepfil->getPepFilTickers($articleId);
	?>
	<div class="pepfil-datetime"><?=$display_date;?></div>
	<div class="pepfil-title"><?=$pdfTitle;?></div>
	
		<?php
		if($stock_tickers!='')
		{
			$arrStock	= explode(",",$stock_tickers);
			foreach($arrStock as $key=>$row)
			{
				if($key>0){
					$stocklink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/pepfil/tag/'.$row.'" target="_self">'.strtoupper($row).'</a>';
				}else{
					$stocklink.=' '.'<a href="'.$HTPFX.$HTHOST.'/pepfil/tag/'.$row.'" target="_self">'.strtoupper($row).'</a>';
				}
			}
		?>
		<?php if(is_array($arrStock)){ ?> 
		<div class="pepfil-tags-heading"><span class="pepfil-datetime">TAGS:&nbsp;</span>
		<span class='pepfil-tags'><?=$stocklink;?></span></div>
		<?php
		}
		}
		?>
	<?php
	}
	
	function displayPepfilSearch($str)		/******************  Search Result ************************/
	{

	}
	function displayDetailPDF()				/******************  Display detail page of particular PDF************************/
	{
		
	}
	function displayPepfilPDFList()				/******************  Recent 8 posted PDFs ************************/
	{
		//$objData 	=	new pepFil();
		//$objData->getPepFilAllReport();
	}
	function pepfilPagination($offset,$tag,$p)				/******************  Pagination Code ************************/
	{
		global $objDataPepfil,$pepfilItemsLimit,$pepfilPaginationNo;
		$numArticle=$objDataPepfil->getPepfilAllArticleCount($pepfilItemsLimit,$tag);
		?>
		<div>
		<? 	$contentCount=$pepfilItemsLimit;
			$this->showPepfilPagination($offset,$numArticle,$contentCount,$p,$pepfilPaginationNo,$tag);
		?>
		</div>
	<? }
	function pepfilTagCloud($taglimit)		/******************  Tag Cloud ************************/
	{
		
	}
	
	function displayMailbag()				/******************  Mailbag Posts ************************/
	{
		
	}
	
	function displayMailbagPost()			/******************  Mailbag detail post ************************/
	{
		
	}
	function displayMailbagComments()		/******************  Mailbag Post comments ************************/
	{
	
	}
	
	function displayPerformancePDF()		/******************  Performance PDF ************************/
	{
	
	}
	
	function displayPepfilAddtoCart(){
		global $lang, $IMG_SERVER,$D_R,$objDataPepfil;
		$arPepfilProductType = $objDataPepfil->getPepfilProductType();
		$str = '<div>';
		foreach($arPepfilProductType as $arRow)
		{
			$str.='<div class="tsp_cart_variation1">
				<h2>'.strtoupper($arRow['product_type']).'</h2>
				<h1>'.$arRow['price_format'].'</h1>
				<div style="width:100%;background-color:#cccccc"><a style="cursor:pointer;" onclick="checkcart(\''.$arRow['subscription_def_id'].'\',\''.$arRow['oc_id'].'\',\''.$arRow['orderItemType'].'\',\''.$arRow['google_analytics_product_name'].'\',\''.$arRow['product_type'].'\',\''.$arRow['google_analytics_action'].'\');"><img src="'.$IMG_SERVER.'/images/tsp/add_to_cart.gif" alt="add to cart" border="0" /></a></div>
				</div>';
		}
		$str.='</div><div style="clear:both;"></div>';
	echo $str;
	}
	
	function displayPepfilAddtoCartVertical(){
		global $lang, $IMG_SERVER,$D_R,$objDataPepfil;
		$arPepfilProductType = $objDataPepfil->getPepfilProductType();
		?>
<table cellspacing="2" cellpadding="4" class="tsp_vertical_addtocart">
<tbody>
<? foreach($arPepfilProductType as $arRow){ ?>
		<tr>
		<td bgcolor="#000066"><span style="text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;font-size: 11px; font-weight: bold;"><?=strtoupper($arRow['product_type']);?><br><a style="font-size: 24px; color: rgb(255, 255, 255);" href="#"><strong><?=$arRow['price_format'];?></strong></a></th>
		<td bgcolor="#cccccc" width="116"><a style="cursor:pointer;" onclick="checkcart('<?=$arRow['subscription_def_id'];?>','<?=$arRow['oc_id'];?>','<?=$arRow['orderItemType'];?>','<?=$arRow['google_analytics_product_name'];?>','<?=$arRow['product_type'];?>','<?=$arRow['google_analytics_action'];?>');"><img src="<?=$IMG_SERVER;?>/images/tsp/add_to_cart.gif" alt="add to cart" border="0" /></a></td>
		</tr>
<? } ?>
</tbody></table>
		<?
	}
	
	function displayAllReports($tag,$offset){
	global $objDataPepfil,$pepfilItemsLimit;
		$allArticle=$objDataPepfil->getPepFilAllReport($pepfilItemsLimit,$offset,$tag); 
		foreach($allArticle as $row){ ?>
			<div class="allarticle">
			<div class="pepfil-datetime"><?=strtoupper(date('M j, Y h:i A',strtotime($row[display_date])))?></div>
			<? $title=$objDataPepfil->getPepfilReportTitleLink($row[id]); 
			$titleLink= '<a href="'.$HTPFX.$HTHOST.$title.'">'.$row[title].'</a>'; 
			?>
			<div class="pepfilAllReportTitle"><?=$titleLink?></div>
			<? $allTag = $objDataPepfil->getPepFilTickers($row[id]); 
				if($allTag!='')
				{
			 	$arrTag	= explode(",",$allTag);
					$tagLink="";
					foreach($arrTag as $key=>$value)
				{
					if($key>0){
							$tagLink.=','.' '.'<a href="'.$HTPFX.$HTHOST.'/pepfil/tag/'.$value.'" target="_self">'.strtoupper($value).'</a>';
					}else{
							$tagLink.=' '.'<a href="'.$HTPFX.$HTHOST.'/pepfil/tag/'.$value.'" target="_self">'.strtoupper($value).'</a>';
					}
				}
			?>
				<? if(is_array($arrTag)){ ?>
						<div class="pepfil-tags-heading"><span class="pepfil-datetime">TAGS:&nbsp;</span>
						<span class='pepfil-tags'><?=$tagLink;?></span></div>
					<? } ?>
				<? } ?>
			</div>
		<? }
	}
	
	function showPepfilPagination($offset,$numrows,$contentcount,$p,$shownum,$tag){
	/*pagination start here*/
		global $HTPFX,$HTHOST;
		if($tag){
			$chkTag = substr($tag,-1);
				if($chkTag != '/')
				{
					$tag	= $tag."/";
				}
		$url=$HTPFX.$HTHOST."/pepfil/tag/".$tag;
		}else{
			$url=$HTPFX.$HTHOST."/pepfil/";
		}
	 
		if($numrows>$contentcount) {
				 $totalRows = $countnum=ceil(($numrows/$contentcount));
				 if(($shownum<$countnum) && ($offset+ $shownum < $countnum)){
					$countnum=$shownum + $p;
				 }
		?>
		<div class="pepfilPagination">
			<table  border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
			<?php
				if(!$p=="0")
				{
				$j=$p-1;
				if($j<1){
				?>
				<td ><a  href="<?=$url?>allreports.htm">First Page</a></td>
				<td ><a target="_parent" style="text-decoration:none;" href="<?=$url?>allreports.htm">&laquo;</a></td>
				<? }else{ ?>
				<td ><a  href="<?=$url?>">First Page</a></td>
				<td ><a target="_parent" style="text-decoration:none;" href="<?=$url?>allreports.htm/?p=<?=$j?>">&laquo;</a></td>
			<?php }
			 }
				for($i=$p; $i<=$countnum-1; $i++) {
					$j=$i;
	
					if($offset==$i){
				 ?>
					<td style="padding-left:12px;padding-right:12px;"><?=($i+1);?></td>
				<?php } else {
				?>
			<td ><a target="_parent" href="<?=$url?>allreports.htm/?p=<?=$i?>"><?=($i+1);?></a></td>
			<?php	}
			  }
									 $p=$p+1;
									 if($numrows>(($p)*$contentcount)){  ?>
				<td ><a target="_parent" style="text-decoration:none;" href="<?=$url?>allreports.htm/?p=<?=$p?>">&raquo;</a></td>
				<td ><a target="_parent" href="<?=$url?>allreports.htm/?p=<?=($totalRows-1)?>">Last Page</a></td>                     		<?php }
			?>
				</tr>
			</table>
		</div><br /><br /><br />
		<?php  }  // for end
	}

}
?>