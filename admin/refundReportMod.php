<?php
global $D_R,$recurlyApiKey,$IMG_SERVER;
set_time_limit ( 600*300 );
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/recurly/recurly.php");
include_once("$D_R/lib/registration/_report_design_lib.php");

$objReportDesign=new reportDesign();
$objRecurly = new recurlyData();
Recurly_Client::$apiKey = $recurlyApiKey;
$json = new Services_JSON();

if(!empty($_POST) && $_POST['type']=="activeUsers"){
	$productName = $_POST['product'];
	$fromDate = $_POST['fromDate'];
	$toDate = $_POST['toDate'];
	$p = $_POST['p'];
	$perPageRecord = intval('100');
	$colName = $_POST['colName'];
	$orderBy = $_POST['orderBy'];
	if($colName==""){
		$colName= "refundDate";
	}

	if($orderBy==""){
		$orderBy = "DESC";
	}

	$formatToDate = date("Y-m-d", strtotime($toDate));

	$qry="SELECT st.subscription_id AS recurlyId, st.subscriptionId AS orderId, DATE_FORMAT(st.date,'%m/%d/%Y') AS refundDate, ROUND((st.amountInCents/100),2) AS refundPrice, s.email, CONCAT(s.fname ,' ',s.lname) AS accountName, s.via_id, sco.id AS accountNum, p.subGroup, p.subType AS term, ROUND((sco.recurly_total_amount_in_cents/100),2) AS purchasePrice,  DATE_FORMAT(sco.recurly_current_period_started_at,'%m/%d/%Y') AS startDate
FROM subscription_transaction AS st, subscription AS s, subscription_cust_order AS sco, product p
WHERE DATE_FORMAT(st.date,'%Y/%m/%d')>='".$fromDate."' AND
DATE_FORMAT(st.date,'%Y/%m/%d')<='".$toDate."' AND st.action='refund' AND st.subscription_id=s.id
AND st.subscriptionId=sco.recurly_uuid AND st.subscription_id=sco.subscription_id
AND sco.recurly_plan_code=p.recurly_plan_code";
	if($productName!='0'){
		$qry .= " AND p.subGroup='".$productName."'";
	}
	$qry .= " GROUP BY subGroup,email,purchasePrice ORDER BY ".$colName." ".$orderBy;
	$numRows = num_rows($qry);
	$numPages=ceil($numRows/$perPageRecord);
	$offset=($p*$perPageRecord);

	$qry .=" limit ".$offset.",".$perPageRecord;

	$res = exec_query($qry);
	$currentPage = count($res);
	$mergeCount = $numRows;
	/*Pagination*/
	if($numPages>1){
		$pagination=array();
		foreach(range(0,$numPages-1) as $i){
			$text = $i+1;
			$link = "<span class='reportPagination' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$i\",\"\",\"\");'>$text</span>";
			$pagination[]= $link;
		}
		$pagination=implode(" | ",$pagination);
	}

	if(empty($res)){
		$val=array('status'=>false,'body'=>'No Record Found.');
	}else{
		$reportBody .= "<table border='1' width='100%' align='center'><tr>";
		if($colName=="accountNum" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='accountNum' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"accountNum\",\"asc\",\"\");'><strong>MV Account Number</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='accountNum' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"accountNum\",\"desc\",\"\");'><strong>MV Account Number</strong></td>";
		}

		if($colName=="subGroup" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='subGroup' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"subGroup\",\"asc\",\"\");'><strong>Product</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='subGroup' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"subGroup\",\"desc\",\"\");'><strong>Product</strong></td>";
		}

		if($colName=="term" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='term' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"term\",\"asc\",\"\");'><strong>Term</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='term' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"term\",\"desc\",\"\");'><strong>Term</strong></td>";
		}

		if($colName=="startDate" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='startDate' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"startDate\",\"asc\",\"\");'><strong>Subscription Start Date (MM/DD/YYYY)</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='startDate' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"startDate\",\"desc\",\"\");'><strong>Subscription Start Date (MM/DD/YYYY)</strong></td>";
		}

		if($colName=="refundDate" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='refundDate' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"refundDate\",\"asc\",\"\");'><strong>Refund Date (MM/DD/YYYY)</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='refundDate' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"refundDate\",\"desc\",\"\");'><strong>Refund Date (MM/DD/YYYY)</strong></td>";
		}

		if($colName=="purchasePrice" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='purchasePrice' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"purchasePrice\",\"asc\",\"\");'><strong>Purchase<br/>Price </strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='purchasePrice' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"purchasePrice\",\"desc\",\"\");'><strong>Purchase<br/>Price </strong></td>";
		}

		if($colName=="refundPrice" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='refundPrice' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"refundPrice\",\"asc\",\"\");'><strong>Refund<br/>Price </strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='refundPrice' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"refundPrice\",\"desc\",\"\");'><strong>Refund<br/>Price </strong></td>";
		}

		if($colName=="accountName" && $orderBy=="desc"){
			$reportBody .= "<td  id='accountName' class='refundLabelHeading' nowrap onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"accountName\",\"asc\",\"\");'><strong>Account<br/>Name</strong></td>";
		}else{
			$reportBody .= "<td id='accountName' class='refundLabelHeading' nowrap onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"accountName\",\"desc\",\"\");'><strong>Account<br/>Name</strong></td>";
		}

		if($colName=="email" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='email' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"email\",\"asc\",\"\");'><strong>Email</strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='email' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"email\",\"desc\",\"\");'><strong>Email</strong></td>";
		}

		if($colName=="recurlyId" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='recurlyId' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"recurlyId\",\"asc\",\"\");'><strong>Recurly ID</strong></td>";
		}else {
			$reportBody .= "<td class='refundLabelHeading' nowrap id='recurlyId' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"recurlyId\",\"desc\",\"\");'><strong>Recurly ID</strong></td>";
		}

		if($colName=="via_id" && $orderBy=="desc"){
			$reportBody .= "<td class='refundLabelHeading' nowrap id='via_id' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"via_id\",\"asc\",\"\");'><strong>VIA ID </strong></td>";
		}else{
			$reportBody .= "<td class='refundLabelHeading' nowrap id='via_id' onClick='javascript:generateRefundReport(\"viewRefundReport\",\"$p\",\"via_id\",\"desc\",\"\");'><strong>VIA ID </strong></td>";
		}
		$reportBody .= "</tr>";
		$filterToDate = date('m/d/Y',strtotime($toDate));
		$filterFromDate = date('m/d/Y',strtotime($fromDate));
		$fromPrevDate = date('m/d/Y',strtotime('-1 day', strtotime($filterFromDate)));
	 	foreach($res as $key=>$value){
	 		$startDate = $value['startDate'];
		 	$term = strtolower($value['term']);

			if($value['term']=="Quaterly"){
				$value['term']="Quarterly ";
			}

			switch($value['term']){
				case 'Quaterly':
					$value['term']="Quarterly ";
				break;
				case 'Monthly Trial':
					$value['term']="Monthly";
				break;
				case 'Quaterly Trial':
					$value['term']="Quarterly ";
				break;
				case 'Annual Trial':
					$value['term']="Annual ";
				break;
			}

			$reportBody .="<tr>
				<td>".$value['accountNum']."</td>
				<td>".$value['subGroup']."</td>
				<td>".$value['term']."</td>
				<td>".$value['startDate']."</td>
				<td>".$value['refundDate']."</td>";
				if($value['purchasePrice']>0){
					$reportBody .="<td>".$value['purchasePrice']."</td>";
				}else{
					$reportBody .="<td>0.00</td>";
				}
				$reportBody .="<td>".$value['refundPrice']."</td>
				<td>".ucwords($value['accountName'])."</td>
				<td>".$value['email']."</td>
				<td>".$value['recurlyId']."</td>
				<td>".$value['via_id']."&nbsp;</td>";
			$reportBody .="</tr>";
		}
		$reportBody .="</table>";
		if($pagination){
			$reportBody .= "<div style='clear:left;'></div><hr/>".$pagination;
		}

		$val=array('status'=>true,'body'=>$reportBody,'count'=>$mergeCount,'currentPageCount'=>$currentPage);
	}
	$output = $json->encode($val);
	echo $output;
	exit;
 }
elseif(!empty($_POST) && $_POST['type']=="showSummary"){
	$productName = $_POST['product'];
	$fromDate = $_POST['fromDate'];
	$toDate = $_POST['toDate'];
	$formatToDate = date("Y-m-d", strtotime($toDate));

	$qry = "SELECT ROUND((st.amountInCents/100),2) AS refundPrice, s.email,
ROUND((sco.recurly_total_amount_in_cents/100),2) AS purchasePrice, p.subType AS term
FROM subscription_transaction AS st, subscription AS s, subscription_cust_order AS sco, product p
WHERE DATE_FORMAT(st.date,'%Y/%m/%d')>='".$fromDate."' AND
DATE_FORMAT(st.date,'%Y/%m/%d')<='".$toDate."' AND st.action='refund' AND st.subscription_id=s.id
AND st.subscriptionId=sco.recurly_uuid AND st.subscription_id=sco.subscription_id
AND sco.recurly_plan_code=p.recurly_plan_code";
	if($productName!='0'){
		$qry .= " AND p.subGroup='".$productName."'";
	}
	$qry .= " GROUP BY subGroup,email,purchasePrice";
	$res = exec_query($qry);
	$totalSubscriber = count($res);
	$totPurchasePrice=0;
	$totRefundPrice=0;
	if(!empty($res)){
	 	$filterToDate = date('m/d/Y',strtotime($toDate));
		$filterFromDate = date('m/d/Y',strtotime($fromDate));
	 	foreach($res as $key=>$value){
		 	$term = strtolower($value['term']);
			if($term=="monthly" || strpos($term,'monthly')!==false){
				$monthlySubCount = $monthlySubCount+1;
			}elseif($term=="quaterly" || strpos($term,'quaterly')!==false){
				$quartrSubCount = $quartrSubCount+1;
			}elseif($term=="annual" || strpos($term,'annual')!==false || $term=="payment by check"){
				$annualSubCount = $annualSubCount+1;
			}elseif($term=="6 months"){
				$halfYearlySubCount = $halfYearlySubCount+1;
			}elseif($term=="singles"){
	 			$singleSubCount=$singleSubCount+1;
	 		}

	 		$totPurchasePrice = $totPurchasePrice+$value['purchasePrice'];
	 		$totRefundPrice = $totRefundPrice+$value['refundPrice'];
	 	}
		$daysInPeriod = date_difftime($filterFromDate,$filterToDate);
		$daysInPeriod = $daysInPeriod['d']+1;
	 	$summaryBody = "<table border='1px solid' width='100%' align='center'>
			<tr>
				<td class='refundSummaryWidth'>Period</td>
				<td class='refundSummaryWidthCount'>".$filterToDate." - ".$filterFromDate."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Monthly</td>
				<td class='summaryMiddleWidth'>".$monthlySubCount."</td>
			</tr>
			<tr>
				<td class='refundSummaryWidth'>Days in Period</td>
				<td class='refundSummaryWidthCount'>".$daysInPeriod."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Quarterly</td>
				<td class='summaryMiddleWidth'>".$quartrSubCount."</td>
			</tr>
			<tr>
				<td class='refundSummaryWidth'>Total Refund Price</td>
				<td class='refundSummaryWidthCount'>$".number_format($totRefundPrice)."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Annual</td>
				<td class='summaryMiddleWidth'>".$annualSubCount."</td>
			</tr>
		</table>";
		$val=array('status'=>true,'body'=>$summaryBody);
	}else{
		$val=array('status'=>false,'body'=>'No Record Found.');
	}
	$output = $json->encode($val);
	echo $output;
	exit;
}
?>