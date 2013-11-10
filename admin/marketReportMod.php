<?php
global $D_R,$IMG_SERVER;
set_time_limit ( 600*300 );
include_once("$D_R/lib/registration/_report_design_lib.php");
$objReportDesign=new reportDesign();
$json = new Services_JSON();

if(!empty($_REQUEST) && $_REQUEST['type']=="marketReport"){
        $p = $_POST['p'];
	$productName = $_POST['product'];
	$fromDate = $_POST['fromDate'];
	$toDate = $_POST['toDate'];
        $colName=$_POST['colName'];
        $orderBy=$_POST['orderBy'];
        // find months diffrenece	
	$dateFrom=date("Y-m-d",strtotime($fromDate));
	$dateTo=date("Y-m-d",strtotime($toDate));
	$date_diff=strtotime($dateTo)-strtotime($dateFrom);
	$monthDiff=floor(($date_diff)/2628000);
	if($monthDiff>6){
		$status='error';
		$msg="Please select date range in between six months";
		$val=array('status'=>$status,'msg'=>$msg);
		$output = $json->encode($val);
		echo $output;
		exit;	
	}
	
        switch($colName){
            case 'day':
                $colName=" DATE_FORMAT(date,'%d')";
                break;
            case 'month':
                $colName=" DATE_FORMAT(date,'%m')";
                break;
            case 'year':
                $colName=" DATE_FORMAT(date,'%Y')";
                break;
            case 'monthyear':
                $colName=" DATE_FORMAT(date,'%m/%Y')";
                break;
        }
        if($colName==""){
		$colName= "date";
	}

	if($orderBy==""){
		$orderBy = "ASC";
	}
       
	$qryMarket = "SELECT productName, productTerm, productType, trialType, utmSource, utmContent, pageName, date, COUNT(pageName) AS cnt FROM registrationTrackingPixel AS rtp WHERE DATE_FORMAT(date,'%Y/%m/%d')>='".$fromDate."' AND DATE_FORMAT(date,'%Y/%m/%d')<='".$toDate."'";
	if($productName!='0'){
		$qryMarket .=" and productName='".$productName."'";
	}
	$qryMarket .=" GROUP BY pageName, utmSource, trialType, productType, productTerm,productName,date_format(date,'%Y-%m-%d') HAVING cnt>0";
        
        $qryMarket .=" ORDER BY ".$colName." ".$orderBy."";
	
	//echo "<br>".$qryMarket;

	$resMarket = exec_query($qryMarket);
	
$productArray = array();
$i=0;
foreach($resMarket as $value){
	$value['date']=date('Y-m-d',strtotime($value['date']));
	$productArray[$value['date']][$value['productName']][$value['utmSource']][$value['utmContent']][$value['productTerm']][$value['productType']][$value['trialType']][$value['pageName']]=$value['cnt'];
	
	switch($value['pageName']){
		case 'Landing Page'	:
			$value['date']=date('Y-m-d',strtotime($value['date']));
			$landingPageCount[$value['productName']][$value['utmSource']][$value['utmContent']][$value['date']]+=$value['cnt'];
			break;
		case 'Step 2':
			$value['date']=date('Y-m-d',strtotime($value['date']));
			$step2Count[$value['productName']][$value['utmSource']][$value['utmContent']][$value['date']]+=$value['cnt'];
			break;
	}
	
}

$countProdut=0;

	if(empty($productArray)){
		$val=array('status'=>false,'body'=>'No Record Found.');
	}else{
		
		$reportBody = "<table border='1' width='100%' align='center'>
		<tr>";
                
			if($colName=="productName" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='productName' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productName\",\"asc\");'><strong>Product</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='productName' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productName\",\"desc\");'><strong>Product</strong></td>";
			}
                        if($colName=="productTerm" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='productTerm' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productTerm\",\"asc\");'><strong>Term</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='productTerm' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productTerm\",\"desc\");'><strong>Term</strong></td>";
			}
                        if($colName=="productType" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='productType' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productType\",\"asc\");'><strong>Type</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='productType' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"productType\",\"desc\");'><strong>Type</strong></td>";
			}
                        if($colName=="trialType" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='trialType' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"trialType\",\"asc\");'><strong>FT/NFT</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='trialType' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"trialType\",\"desc\");'><strong>FT/NFT</strong></td>";
			}
                     
                     if($colName=="utmSource" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='utmSource' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"utmSource\",\"asc\");'><strong>Source</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='utmSource' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"utmSource\",\"desc\");'><strong>Source</strong></td>";
			}
                        if($colName=="utmContent" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='utmContent' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"utmContent\",\"asc\");'><strong>Placement</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='utmContent' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"utmContent\",\"desc\");'><strong>Placement</strong></td>";
			}
                        
			$reportBody .=" 
			<td nowrap><strong>Landing Page</strong></td>
			<td nowrap><strong>Step 2</strong></td>
			<td nowrap><strong>Registration Page</strong></td>
			<td nowrap><strong>Cross-Sell</strong></td>
			<td nowrap><strong>Welcome Page</strong></td>";
                        
                        if($colName=="day" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='day' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"day\",\"asc\");'><strong>Day</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='day' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"day\",\"desc\");'><strong>Day</strong></td>";
			}
                        if($colName=="month" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='month' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"month\",\"asc\");'><strong>Month</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='month' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"month\",\"desc\");'><strong>Month</strong></td>";
			}
                        if($colName=="year" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='year' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"year\",\"asc\");'><strong>Year</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='year' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"year\",\"desc\");'><strong>Year</strong></td>";
			}
                        if($colName=="monthyear" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='monthyear' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"monthyear\",\"asc\");'><strong>Month Year</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='monthyear' onClick='javascript:generateMarketingReport(\"marketingDashboard\",\"$p\",\"monthyear\",\"desc\");'><strong>Month Year</strong></td>";
			}
                        
			$reportBody .="</tr>";

foreach($productArray as $keyDate=>$prodDate){
		foreach($prodDate as $productKey=>$val){
                        $registrationPageCount="";
                        $crossSellPageCount="";
                        $welcomePageCount="";
			$landingPageCountVal="";
			$step2PageCountVal="";
			foreach($val as $sourceKey=>$sourceRow){
				foreach($sourceRow as $contentKey=>$contentVal){
					foreach($contentVal as $termKey=>$termVal){
						foreach($termVal as $typeKey=>$typeValue){
							foreach($typeValue as $nftKey=>$nftValue){
                                                        
							$registrationPageCount="";
							$crossSellPageCount="";
							$welcomePageCount="";
							foreach($nftValue as $keyCount=>$pageCountVal){
								$dateValue=$keyDate;
								
			$landingPageCountVal="";
			$step2PageCountVal="";
			
			if(array_key_exists($sourceKey,$landingPageCount[$productKey])){
				foreach($landingPageCount[$productKey][$sourceKey] as $landingPageKey=>$landingPageVal){
					
					$landingPageCountVal=$landingPageVal[date('Y-m-d',strtotime($keyDate))];
				}
			}
			
			$step2PageCountVal="";
			if(array_key_exists($sourceKey,$step2Count[$productKey])){
				foreach($step2Count[$productKey][$sourceKey] as $step2CountKey=>$step2CountVal){
					$step2PageCountVal=$step2CountVal[date('Y-m-d',strtotime($keyDate))];
				}
			}
								
							
								switch($keyCount){
									case 'subscription_product_registration':
										$registrationPageCount=$pageCountVal;
										break;
									case 'cross_sell':
										$crossSellPageCount=$pageCountVal;
										break;
									case 'subscription_product_welcome':
										$welcomePageCount=$pageCountVal;
										break;
								}
								
			
										
							}
                        if(!empty($productKey)){
				$countProdut=$countProdut+1;
							$reportBody .="
			<tr>
				<td>".$productKey."&nbsp;</td>
				<td>".$termKey."&nbsp;</td>
				<td>".$typeKey."&nbsp;</td>
				<td>".$nftKey."&nbsp;</td>
				<td>".urldecode($sourceKey)."&nbsp;</td>
				<td>".urldecode($contentKey)."&nbsp;</td>
				<td>".$landingPageCountVal."&nbsp;</td>
				<td>".$step2PageCountVal."&nbsp;</td>
				<td>".$registrationPageCount."&nbsp;</td>
				<td>".$crossSellPageCount."&nbsp;</td>
				<td>".$welcomePageCount."&nbsp;</td>
				<td>".date("d",strtotime($dateValue))."</td>
				<td>".date("F",strtotime($dateValue))."</td>
				<td>".date("Y",strtotime($dateValue))."</td>
				<td>".date("F Y",strtotime($dateValue))."</td>
			</tr>";
			}
								
								
							}
						}
					}
					
				}
				
				
			}
			
						
		}
}
		$reportBody .="</table>";
		/*echo "<br>".$reportBody;
		exit;*/
		$val=array('status'=>true,'body'=>$reportBody,'count'=>$countProdut);
	}
	$output = $json->encode($val);
	echo $output;
	exit;

}

?>