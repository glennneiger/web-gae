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
		$colName= "start_date";
	}

	if($orderBy==""){
		$orderBy = "DESC";
	}

	$freeTrial = $_POST['freeTrial'];
	$inFreeTrial = $_POST['inFreeTrial'];

	$qry = "SELECT * FROM (SELECT ROUND(sco.price,2) AS price,'' AS trans_status,sco.id AS account_num,
CONCAT(s.fname ,' ',s.lname) AS account_name, s.email, s.id AS recurly_id, s.via_id, p.subGroup, p.subType AS term, p.recurly_plan_code AS planCode, DATE_FORMAT(sco.startDate,'%m/%d/%Y') AS start_date,
DATE_FORMAT(sco.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate,
DATE_FORMAT(sco.startDate,'%m/%Y') AS startDate, DATE_FORMAT(sco.expireDate,'%m/%d/%Y') AS end_date, DATE_FORMAT(sco.expireDate,'%m/%d/%Y') AS actual_end_date, IF(sco.orderStatus !='' ,'NA','NA') AS isCancel, sco.id AS id, IF(DATE_FORMAT(sco.recurly_expires_at,'%Y/%m/%d') !='' || DATE_FORMAT(sco.recurly_canceled_at,'%Y/%m/%d')!='','NA','NA') AS setToRenew, IF(sco.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'),'active','canceled') AS state, IF(DATE_FORMAT(sco.recurly_trial_started_at,'%Y/%m/%d')='','NA','NA') AS freeTrial, IF(DATE_FORMAT(sco.recurly_trial_ends_at,'%Y/%m/%d')='','NA','NA') AS inFreeTrial, sco.orderNumber AS orderNum FROM `subscription_cust_order` sco, subscription s, product p WHERE DATE_FORMAT(sco.expireDate,'%Y/%m/%d')>='".$fromDate."' AND DATE_FORMAT(sco.startDate,'%Y/%m/%d')<='".$toDate."' AND sco.subscription_id=s.id AND p.subscription_def_id=sco.typeSpecificId AND sco.typeSpecificId IS NOT NULL AND sco.typeSpecificId!='15' AND sco.orderStatus<>'CANCEL_CUSTOMER_REQUESTED'";
if($productName!='0'){
	$qry .= " AND p.subGroup='".$productName."'";
}
$qry .=" UNION SELECT * FROM (SELECT IF(st.amountInCents!='',IF(start_date=trialStartDate,'0',ROUND((st.amountInCents/100),2)),'0') AS price,st.status as trans_status,tempRecurly1.* FROM (SELECT sco.id AS account_num, CONCAT(s.fname ,' ',s.lname) AS account_name, s.email, s.id AS recurly_id, s.via_id, p.subGroup, p.subType AS term, scr.recurly_plan_code AS planCode, DATE_FORMAT(scr.recurly_current_period_started_at,'%m/%d/%Y') AS start_date,
DATE_FORMAT(scr.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate,
DATE_FORMAT(scr.recurly_current_period_started_at,'%m/%Y') AS startDate,
IF(scr.recurly_state='expired',DATE_FORMAT(scr.recurly_expires_at,'%m/%d/%Y'),
DATE_FORMAT(scr.recurly_current_period_ends_at,'%m/%d/%Y')) AS end_date, DATE_FORMAT(scr.recurly_current_period_ends_at,'%m/%d/%Y') AS actual_end_date, IF(sco.recurly_state='canceled' || sco.recurly_state='expired','YES','NO') AS isCancel, scr.id, IF(DATE_FORMAT(scr.recurly_canceled_at,'%Y/%m/%d')='0000/00/00' || DATE_FORMAT(scr.recurly_expires_at,'%Y/%m/%d')='0000/00/00','YES','NO') AS setToRenew, scr.recurly_state AS state, IF(DATE_FORMAT(scr.recurly_trial_started_at,'%Y/%m/%d')='0000/00/00','NO','YES') AS freeTrial, IF(DATE_FORMAT(scr.recurly_trial_ends_at,'%Y/%m/%d')>=DATE_FORMAT(NOW(),'%Y/%m/%d') && scr.recurly_state<>'expired','YES','NO') AS inFreeTrial, scr.recurly_uuid AS orderNum FROM subscription_cust_report scr, subscription s, product p, subscription_cust_order sco WHERE IF(scr.recurly_state='expired',DATE_FORMAT(scr.recurly_expires_at,'%Y/%m/%d'),
DATE_FORMAT(scr.recurly_current_period_ends_at,'%Y/%m/%d'))>'".$fromDate."' AND DATE_FORMAT(scr.recurly_current_period_started_at,'%Y/%m/%d')<='".$toDate."' AND scr.subscription_id=s.id AND scr.recurly_plan_code=p.recurly_plan_code AND scr.subscription_id=sco.subscription_id AND scr.recurly_plan_code=sco.recurly_plan_code AND p.subType<>'Payment By Check'";
if($productName!='0'){
	$qry .= " AND p.subGroup='".$productName."'";
}
$qry .= " ORDER BY scr.id DESC
)AS tempRecurly1
LEFT JOIN subscription_transaction st ON st.subscriptionId=tempRecurly1.orderNum AND tempRecurly1.recurly_id=st.subscription_id AND DATE_FORMAT(st.date,'%m/%Y')=tempRecurly1.startDate ORDER BY tempRecurly1.id DESC
)AS tempRecurly GROUP BY subGroup,email,planCode,start_date";

$qry .=" UNION SELECT IF(st.amountInCents!='',IF(start_date=trialStartDate || state='expired','0',ROUND((st.amountInCents/100),2)),'0') AS price,st.status as trans_status,tempRecurlyDb.* FROM (SELECT sco.id AS account_num, CONCAT(s.fname ,' ',s.lname) AS account_name, s.email, s.id AS recurly_id, s.via_id, p.subGroup, p.subType AS term, p.recurly_plan_code AS planCode, DATE_FORMAT(sco.recurly_current_period_started_at,'%m/%d/%Y') AS start_date,
DATE_FORMAT(sco.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate,
DATE_FORMAT(sco.recurly_current_period_started_at,'%m/%Y') AS startDate,
IF(sco.recurly_state='expired',DATE_FORMAT(sco.recurly_expires_at,'%m/%d/%Y'),
DATE_FORMAT(sco.recurly_current_period_ends_at,'%m/%d/%Y')) AS end_date, DATE_FORMAT(sco.recurly_current_period_ends_at,'%m/%d/%Y') AS actual_end_date,
IF(sco.recurly_state='canceled' || sco.recurly_state='expired','YES','NO') AS isCancel, sco.id AS id, IF(DATE_FORMAT(sco.recurly_expires_at,'%Y/%m/%d') !='' || DATE_FORMAT(sco.recurly_canceled_at,'%Y/%m/%d')!='','NA','NA') AS setToRenew, sco.recurly_state AS state, IF(DATE_FORMAT(sco.recurly_trial_started_at,'%Y/%m/%d')='0000/00/00','NO','YES') AS freeTrial, IF(DATE_FORMAT(sco.recurly_trial_ends_at,'%Y/%m/%d')>=DATE_FORMAT(NOW(),'%Y/%m/%d') && sco.recurly_state<>'expired','YES','NO') AS inFreeTrial, sco.recurly_uuid AS orderNum FROM subscription_cust_order sco,subscription s, product p WHERE IF(sco.recurly_state='expired',DATE_FORMAT(sco.recurly_expires_at,'%Y/%m/%d'),
DATE_FORMAT(sco.recurly_current_period_ends_at,'%Y/%m/%d'))>'".$fromDate."' AND DATE_FORMAT(sco.recurly_current_period_started_at,'%Y/%m/%d')<='".$toDate."' AND sco.subscription_id=s.id AND p.recurly_plan_code=sco.recurly_plan_code AND sco.recurly_plan_code IS NOT NULL AND sco.recurly_uuid='' AND p.subType<>'Payment By Check' AND sco.recurly_state<>'expired'";
if($productName!='0'){
	$qry .= " AND p.subGroup='".$productName."'";
}
$qry .= " )AS tempRecurlyDb
LEFT JOIN subscription_transaction st ON st.subscriptionId=tempRecurlyDb.orderNum
AND tempRecurlyDb.recurly_id=st.subscription_id";

$qry .= " ) AS temp1";

	if($freeTrial!='0' && $inFreeTrial!='0'){
		$qry .=" WHERE freeTrial='".$freeTrial."' AND inFreeTrial='".$inFreeTrial."'";
	}elseif ($freeTrial!='0'){
		$qry .=" WHERE freeTrial='".$freeTrial."'";
	}elseif ($inFreeTrial!='0'){
		$qry .=" WHERE inFreeTrial='".$inFreeTrial."'";
	}

	$qry .= " GROUP BY email,subGroup,state,actual_end_date ORDER BY ".$colName." ".$orderBy;

	$numRows = num_rows($qry);
	$numPages=ceil($numRows/$perPageRecord);
	$offset=($p*$perPageRecord);

	$qry .=" limit ".$offset.",".$perPageRecord;

	$res = exec_query($qry);
	//$numrows=exec_query("SELECT FOUND_ROWS() as c",1,"c");
	$currentPage = count($res);
	$mergeCount = $numRows;
	/*Pagination*/
	if($numPages>1){
		$pagination=array();
		foreach(range(0,$numPages-1) as $i){
			$text = $i+1;
			$link = "<span class='reportPagination' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$i\",\"\",\"\");'>$text</span>";
			$pagination[]= $link;
		}
		$pagination=implode(" | ",$pagination);
	}

	foreach ($res as $key=>$val){
		$subId = $val['recurly_id'];
		$qryChkCC = "SELECT id FROM `subscription_transaction` st WHERE st.subscription_id='".$subId."' AND DATE_FORMAT(st.date,'%Y/%m/%d') >=DATE_FORMAT('".$fromDate."','%m/%Y') AND DATE_FORMAT(st.date,'%Y/%m/%d')<=DATE_FORMAT('".$toDate."','%m/%Y')";
		$resChkCC = exec_query($qryChkCC,1);

		if($resChkCC['id']!=''){
			$res[$key]['validCC'] = "YES";
		}else{
			$res[$key]['validCC'] = "NO";
		}

		$orderNum = $val['orderNum'];
		$qryNotify ="select notificationType from subscription_cust_report where recurly_uuid='".$orderNum."' AND DATE_FORMAT(recurly_current_period_started_at,'%m/%d/%Y')='".$val['start_date']."'";
		$resNotify = exec_query($qryNotify,1);
		if($resNotify['notificationType']=="new_subscription_notification"){
			$res[$key]['chargeType'] = "NEW";
		}elseif($resNotify['notificationType']=="renewed_subscription_notification"){
			$res[$key]['chargeType'] = "RENEWAL";
		}else{
			$res[$key]['chargeType'] = "NA";
		}
	}
	if(empty($res)){
		$val=array('status'=>false,'body'=>'No Record Found.');
	}else{
		$reportBody = "<div class='dataInfo'>**NA - Either we don't have previous data or it is a VIA Record or the subscription added from MV DB not recurly.</div><div class='dataInfo'>**Total Purchase, total refund  and amount charged to clients dose not apply to product serach.</div><br/>";
		$reportBody .= "<table border='1' width='100%' align='center'>
		<tr>";
			if($colName=="account_num" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='account_num' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"account_num\",\"asc\",\"\");'><strong>MV Account Number</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='account_num' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"account_num\",\"desc\",\"\");'><strong>MV Account Number</strong></td>";
			}

			if($colName=="subGroup" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='subGroup' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"subGroup\",\"asc\",\"\");'><strong>Product</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='subGroup' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"subGroup\",\"desc\",\"\");'><strong>Product</strong></td>";
			}

			if($colName=="term" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='term' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"term\",\"asc\",\"\");'><strong>Term</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='term' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"term\",\"desc\",\"\");'><strong>Term</strong></td>";
			}

			if($colName=="start_date" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='start_date' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"start_date\",\"asc\",\"\");'><strong>Start Date (MM/DD/YYYY)</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='start_date' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"start_date\",\"desc\",\"\");'><strong>Start Date (MM/DD/YYYY)</strong></td>";
			}

			if($colName=="end_date" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='end_date' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"end_date\",\"asc\",\"\");'><strong>End Date (MM/DD/YYYY)</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='end_date' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"end_date\",\"desc\",\"\");'><strong>End Date (MM/DD/YYYY)</strong></td>";
			}

			if($colName=="setToRenew" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='setToRenew' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"setToRenew\",\"asc\",\"\");'><strong>Set To Renew</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='setToRenew' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"setToRenew\",\"desc\",\"\");'><strong>Set To Renew</strong></td>";
			}

			if($colName=="price" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='price' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"price\",\"asc\",\"\");'><strong>Purchase<br/>Price </strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='price' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"price\",\"desc\",\"\");'><strong>Purchase<br/>Price </strong></td>";
			}

			if($colName=="account_name" && $orderBy=="desc"){
				$reportBody .= "<td  id='account_name' class='labelHeading' nowrap onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"account_name\",\"asc\",\"\");'><strong>Account<br/>Name</strong></td>";
			}else{
				$reportBody .= "<td id='account_name' class='labelHeading' nowrap onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"account_name\",\"desc\",\"\");'><strong>Account<br/>Name</strong></td>";
			}

			if($colName=="email" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='email' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"email\",\"asc\",\"\");'><strong>Email</strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='email' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"email\",\"desc\",\"\");'><strong>Email</strong></td>";
			}

			if($colName=="recurly_id" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='recurly_id' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"recurly_id\",\"asc\",\"\");'><strong>Recurly ID</strong></td>";
			}else {
				$reportBody .= "<td class='labelHeading' nowrap id='recurly_id' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"recurly_id\",\"desc\",\"\");'><strong>Recurly ID</strong></td>";
			}

			if($colName=="via_id" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='via_id' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"via_id\",\"asc\",\"\");'><strong>VIA ID </strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='via_id' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"via_id\",\"desc\",\"\");'><strong>VIA ID </strong></td>";
			}
			$reportBody .= "<td class='labelHeadingNoSort' nowrap><strong>Valid CC in Recurly?</strong></td>";

			if($colName=="freeTrial" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='freeTrial' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"freeTrial\",\"asc\",\"\");'><strong>Free Trial </strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='freeTrial' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"freeTrial\",\"desc\",\"\");'><strong>Free Trial </strong></td>";
			}

			if($colName=="inFreeTrial" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='inFreeTrial' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"inFreeTrial\",\"asc\",\"\");'><strong>In Free Trial </strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='inFreeTrial' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"inFreeTrial\",\"desc\",\"\");'><strong>In Free Trial </strong></td>";
			}

			if($colName=="isCancel" && $orderBy=="desc"){
				$reportBody .= "<td class='labelHeading' nowrap id='isCancel' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"isCancel\",\"asc\",\"\");'><strong>Cancel/<br/>Expired </strong></td>";
			}else{
				$reportBody .= "<td class='labelHeading' nowrap id='isCancel' onClick='javascript:generateSubReport(\"subscriprionReport\",\"$p\",\"isCancel\",\"desc\",\"\");'><strong>Cancel/<br/>Expired </strong></td>";
			}

			$reportBody .= "<td class='labelHeadingNoSort' nowrap><strong>Charge Type</strong></td>
			<td class='labelHeadingNoSort' nowrap><strong>Revenues per day</strong></td>
			<td class='labelHeadingNoSort' nowrap><strong>Revenues Recognized</strong></td>
			<td class='labelHeadingNoSort' nowrap><strong>Revenues to be Recognized</strong></td>
		</tr>";
		$filterToDate = date('m/d/Y',strtotime($toDate));
		$filterFromDate = date('m/d/Y',strtotime($fromDate));
		$fromPrevDate = date('m/d/Y',strtotime('-1 day', strtotime($filterFromDate)));
	 	foreach($res as $key=>$value){

			/*find refund tracnsaction and set price according to refund*/
			    $qryRefund="SELECT id, ROUND((amountInCents/100),2) AS refundPrice FROM subscription_transaction WHERE ACTION='refund' AND subscriptionId='".$value['orderNum']."' AND subscription_id='".$value['recurly_id']."' AND DATE_FORMAT(date,'%m/%Y')='".$value['startDate']."'";
			    $getRefund=exec_query($qryRefund,1);
			    if(!empty($getRefund['id'])){
			    	if($value['price']>0){
						$value['price']=$value['price']-$getRefund['refundPrice'];
			    	}
			    }

			    /*Find void transaction and set price 0*/

			    $qryVoid="SELECT id FROM subscription_transaction WHERE STATUS='void' AND subscriptionId='".$value['orderNum']."' AND subscription_id='".$value['recurly_id']."' AND DATE_FORMAT(date,'%m/%Y')='".$value['startDate']."'";

			    $getVoidData=exec_query($qryVoid,1);

			    if(!empty($getVoidData['id'])){
			       $value['price']=0;
			    }

			if($value['state']=="expired" && $value['setToRenew']=="NO" && $value['trans_status']=="declined"){

			   $value['price']=0;
			}
	 		$startDate = $value['start_date'];
	 		$endDate="";
			if($value['end_date']=="00/00/0000" || $value['end_date']=="" ){
				$endDate = "00/00/0000";
			}else{
				$endDate = $value['end_date'];
			}

		 	$term = strtolower($value['term']);
		 	if($term=="soft trial" || $term=="complimentary" || $term==""){
		 		$revToBeRecog ="0";
		 		$revPerDay="0";
		 		$revRecog="0";
		 	}else{
				if($term=="monthly" || strpos($term,'monthly')!==false){
					$durOfProductInDays = "30";
				}elseif($term=="quaterly" || strpos($term,'quaterly')!==false){
					$durOfProductInDays = "90";
				}elseif($term=="annual" || strpos($term,'annual')!==false || $term=="payment by check"){
					$durOfProductInDays = "365";
				}elseif($term=="6 months"){
					$durOfProductInDays = "60";
				}

				if($term=="singles"){
		 			$revPerDay="0";
		 			$revToBeRecog = "0";
		 			$revRecog = $value['price'];
		 		}else{
					if(strtotime($startDate)>strtotime($fromPrevDate)){
						$calStartInPeriod = date_difftime($startDate,$filterToDate);
						$startInPeriod = intval($calStartInPeriod['d']+1);
					}else{
						$startInPeriod = "0";
					}

					if(strtotime($endDate)<strtotime($filterToDate)){
						$calEndInPeriod = date_difftime($filterFromDate,$endDate);
						$endInPeriod = intval($calEndInPeriod['d']+1);
					}else{
						$endInPeriod = "0";
					}

					$startEndFilterToDiff = date_difftime($filterToDate,$endDate);
					if (intval($startEndFilterToDiff['d'])<0){
						$startEndOutPeriod = "0";
					}else{
						$calStartEndOutPeriod = date_difftime($filterFromDate,$filterToDate);
						$startEndOutPeriod = intval($calStartEndOutPeriod['d']+1);
					}

				 	$totalOfInperiod = $startInPeriod+$endInPeriod;
					if(intval($totalOfInperiod)>0){
						$totalDays = $totalOfInperiod;
					}else{
						$totalDays = $startEndOutPeriod;
					}
					$revPerDayFloat=0;
					if($durOfProductInDays>0){
						if($value['price']!=="0"){
							$revPerDayFloat = $value['price']/$durOfProductInDays;
							$revPerDay = round($revPerDayFloat,2);
							$revRecog  = round($totalDays*$revPerDayFloat,2);
						}else{
							$revPerDay = $value['price'];
							$revRecog  = $value['price'];
						}
					}else{
						$revPerDay = $value['price'];
						$revRecog  = $value['price'];
					}

					if($startEndFilterToDiff['d']<0){
						$daysRemain = "0";
					}else{
						$daysRemainCal = date_difftime($filterToDate,$endDate);
						$daysRemain = $daysRemainCal['d'];
					}

			 		if($daysRemain=="0"){
						$revToBeRecog = "0";
					}else{
						if($revPerDayFloat>0){
							$revToBeRecog = round($daysRemain*$revPerDayFloat,2);
						}else{
							$revToBeRecog = round($daysRemain*$revPerDay,2);
						}
					}
		 		}
		 	}

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

			$reportBody .="
			<tr>
				<td>".$value['account_num']."</td>
				<td>".$value['subGroup']."</td>
				<td>".$value['term']."</td>
				<td>".$value['start_date']."</td>
				<td>".$endDate."</td>
				<td>".$value['setToRenew']."</td>";
				if($value['price']>0){
					$reportBody .="<td>".$value['price']."</td>";
				}else{
					$reportBody .="<td>0.00</td>";
				}
				$reportBody .="<td>".ucwords($value['account_name'])."</td>
				<td>".$value['email']."</td>
				<td>".$value['recurly_id']."</td>
				<td>".$value['via_id']."&nbsp;</td>
				<td>".$value['validCC']."</td>
				<td>".$value['freeTrial']."</td>
				<td>".$value['inFreeTrial']."</td>
				<td>".$value['isCancel']."</td>
				<td>".$value['chargeType']."</td>";
		 		if($revPerDay>0){
					$reportBody .="<td>$".$revPerDay."</td>";
				}else{
					$reportBody .="<td>$0.00</td>";
				}

		 		if($revRecog>0){
					$reportBody .="<td>$".$revRecog."</td>";
				}else{
					$reportBody .="<td>$0.00</td>";
				}

		 		if($revToBeRecog>0){
					$reportBody .="<td>$".$revToBeRecog."</td>";
				}else{
					$reportBody .="<td>$0.00</td>";
				}
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
	$freeTrial = $_POST['freeTrial'];
	$inFreeTrial = $_POST['inFreeTrial'];
if(empty($productName))	{
	/*Qry for Purchase Price*/
	$qryPurchasePrice="SELECT SUM(ROUND((amountInCents/100),2)) AS purchasePrice  FROM subscription_transaction WHERE ACTION='purchase'
AND STATUS='success'
AND DATE_FORMAT(DATE,'%Y/%m/%d')>='".$fromDate."'  AND
DATE_FORMAT(DATE,'%Y/%m/%d')<='".$toDate."'
AND transactionId NOT IN (SELECT transactionId FROM subscription_transaction WHERE STATUS='void')";
$getResultPurPrice=exec_query($qryPurchasePrice,1);
$purchasePrice=0;
if(!empty($getResultPurPrice)){
	$purchasePrice=$getResultPurPrice['purchasePrice'];
}

$qryRefund="SELECT SUM(ROUND((amountInCents/100),2)) AS refundPrice  FROM subscription_transaction WHERE ACTION='refund' AND STATUS='success' AND DATE_FORMAT(DATE,'%Y/%m/%d')>='".$fromDate."' AND DATE_FORMAT(DATE,'%Y/%m/%d')<='".$toDate."'";
$getResultRefund=exec_query($qryRefund,1);
$refundPrice=0;
if(!empty($getResultRefund)){
	$refundPrice=$getResultRefund['refundPrice'];
}

$amtChargeToClient=($purchasePrice-$refundPrice);

}

	$qry = "SELECT * FROM (SELECT ROUND(sco.price,2) AS price, s.email, s.id AS recurly_id, p.subGroup, p.subType AS term, DATE_FORMAT(sco.startDate,'%m/%d/%Y') AS start_date, DATE_FORMAT(sco.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate, DATE_FORMAT(sco.startDate,'%m/%Y') AS startDate, DATE_FORMAT(sco.expireDate,'%m/%d/%Y') AS end_date, DATE_FORMAT(sco.expireDate,'%m/%d/%Y') AS actual_end_date, sco.id AS id, IF(DATE_FORMAT(sco.recurly_trial_started_at,'%Y/%m/%d')='','NA','NA') AS freeTrial, IF(DATE_FORMAT(sco.recurly_trial_ends_at,'%Y/%m/%d')='','NA','NA') AS inFreeTrial, sco.orderNumber AS orderNum, IF(sco.orderStatus IN ('SHIPPED_COMPLETE','PARTIAL_SHIPMENT','ORDER_PLACED'),'active','canceled') AS state FROM subscription_cust_order sco, subscription s, product p WHERE DATE_FORMAT(sco.expireDate,'%Y/%m/%d')>='".$fromDate."' AND DATE_FORMAT(sco.startDate,'%Y/%m/%d')<='".$toDate."' AND sco.subscription_id=s.id AND p.subscription_def_id=sco.typeSpecificId AND sco.typeSpecificId IS NOT NULL AND sco.typeSpecificId!='15' AND sco.orderStatus<>'CANCEL_CUSTOMER_REQUESTED'";
	if($productName!='0'){
		$qry .= " AND p.subGroup='".$productName."'";
	}
	$qry .=" UNION SELECT * FROM (SELECT IF(st.amountInCents!='',IF(start_date=trialStartDate,'0',ROUND((st.amountInCents/100),2)),'0') AS price,tempRecurly1.* FROM (SELECT s.email, s.id AS recurly_id, p.subGroup, p.subType AS term, DATE_FORMAT(scr.recurly_current_period_started_at,'%m/%d/%Y') AS start_date,
DATE_FORMAT(scr.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate,
DATE_FORMAT(scr.recurly_current_period_started_at,'%m/%Y') AS startDate,
IF(scr.recurly_state='expired',DATE_FORMAT(scr.recurly_expires_at,'%m/%d/%Y'),
DATE_FORMAT(scr.recurly_current_period_ends_at,'%m/%d/%Y')) AS end_date, DATE_FORMAT(scr.recurly_current_period_ends_at,'%m/%d/%Y') AS actual_end_date, scr.id, IF(DATE_FORMAT(scr.recurly_trial_started_at,'%Y/%m/%d')='0000/00/00','NO','YES') AS freeTrial, IF(DATE_FORMAT(scr.recurly_trial_ends_at,'%Y/%m/%d')>=DATE_FORMAT(NOW(),'%Y/%m/%d') && scr.recurly_state<>'expired','YES','NO') AS inFreeTrial, scr.recurly_uuid AS orderNum, scr.recurly_state AS state FROM subscription_cust_report scr, subscription s, product p, subscription_cust_order sco WHERE IF(scr.recurly_state='expired',DATE_FORMAT(scr.recurly_expires_at,'%Y/%m/%d'),
DATE_FORMAT(scr.recurly_current_period_ends_at,'%Y/%m/%d'))>'".$fromDate."' AND DATE_FORMAT(scr.recurly_current_period_started_at,'%Y/%m/%d')<='".$toDate."' AND scr.subscription_id=s.id AND scr.recurly_plan_code=p.recurly_plan_code AND scr.subscription_id=sco.subscription_id AND scr.recurly_plan_code=sco.recurly_plan_code AND p.subType<>'Payment By Check'";
if($productName!='0'){
		$qry .= " AND p.subGroup='".$productName."'";
	}
$qry .= " ORDER BY scr.id DESC
)AS tempRecurly1
LEFT JOIN subscription_transaction st ON st.subscriptionId=tempRecurly1.orderNum
AND tempRecurly1.recurly_id=st.subscription_id AND DATE_FORMAT(st.date,'%m/%Y')=tempRecurly1.startDate ORDER BY tempRecurly1.id DESC
)AS tempRecurly GROUP BY subGroup,email,start_date";

	$qry .=" UNION SELECT IF(st.amountInCents!='',IF(start_date=trialStartDate || state='expired','0',ROUND((st.amountInCents/100),2)),'0') AS price,tempRecurlyDb.* FROM (SELECT s.email, s.id AS recurly_id, p.subGroup, p.subType AS term, DATE_FORMAT(sco.recurly_current_period_started_at,'%m/%d/%Y') AS start_date,
DATE_FORMAT(sco.recurly_trial_started_at,'%m/%d/%Y') AS trialStartDate, DATE_FORMAT(sco.recurly_current_period_started_at,'%m/%Y') AS startDate, IF(sco.recurly_state='expired',DATE_FORMAT(sco.recurly_expires_at,'%m/%d/%Y'), DATE_FORMAT(sco.recurly_current_period_ends_at,'%m/%d/%Y')) AS end_date, DATE_FORMAT(sco.recurly_current_period_ends_at,'%m/%d/%Y') AS actual_end_date, sco.id AS id, IF(DATE_FORMAT(sco.recurly_trial_started_at,'%Y/%m/%d')='0000/00/00','NO','YES') AS freeTrial, IF(DATE_FORMAT(sco.recurly_trial_ends_at,'%Y/%m/%d')>=DATE_FORMAT(NOW(),'%Y/%m/%d') && sco.recurly_state<>'expired','YES','NO') AS inFreeTrial, sco.recurly_uuid AS orderNum, sco.recurly_state AS state FROM subscription_cust_order sco,subscription s, product p WHERE IF(sco.recurly_state='expired',DATE_FORMAT(sco.recurly_expires_at,'%Y/%m/%d'), DATE_FORMAT(sco.recurly_current_period_ends_at,'%Y/%m/%d'))>='".$fromDate."' AND DATE_FORMAT(sco.recurly_current_period_started_at,'%Y/%m/%d')<='".$toDate."' AND sco.subscription_id=s.id AND p.recurly_plan_code=sco.recurly_plan_code AND sco.recurly_plan_code IS NOT NULL AND sco.recurly_uuid='' AND p.subType<>'Payment By Check' AND sco.recurly_state<>'expired'";
if($productName!='0'){
		$qry .= " AND p.subGroup='".$productName."'";
	}
$qry .= " )AS tempRecurlyDb
LEFT JOIN subscription_transaction st ON st.subscriptionId=tempRecurlyDb.orderNum
AND tempRecurlyDb.recurly_id=st.subscription_id";

	$qry .= " ) AS temp1";

	if($freeTrial!='0' && $inFreeTrial!='0'){
		$qry .=" WHERE freeTrial='".$freeTrial."' AND inFreeTrial='".$inFreeTrial."'";
	}elseif ($freeTrial!='0'){
		$qry .=" WHERE freeTrial='".$freeTrial."'";
	}elseif ($inFreeTrial!='0'){
		$qry .=" WHERE inFreeTrial='".$inFreeTrial."'";
	}

	$qry .= " GROUP BY email,subGroup,state,actual_end_date";

	$res = exec_query($qry);
	$totalSubscriber = count($res);

	if(!empty($res)){
	 	$filterToDate = date('m/d/Y',strtotime($toDate));
		$filterFromDate = date('m/d/Y',strtotime($fromDate));
		$fromPrevDate = date('m/d/Y',strtotime('-1 day', strtotime($filterFromDate)));
		$totalRevRecog = "0";
		$totalRevToBeRecog = "0";
		$monthlySubCount = "0";
		$quartrSubCount = "0";
		$annualSubCount = "0";
		$softTrialSubCount = "0";
		$compSubCount = "0";
		$singleSubCount = "0";
		$halfYearlySubCount = "0";
	 	foreach($res as $key=>$value){
			if($value['state']=="expired" && $value['setToRenew']=="NO"){
			   $value['price']=0;
			}
	 		$startDate = $value['start_date'];
	 		$endDate="";
			if($value['end_date']=="00/00/0000" || $value['end_date']=="" ){
				$endDate = "00/00/0000";
			}else{
				$endDate = $value['end_date'];
			}

		 	$term = strtolower($value['term']);
		 	if($term=="soft trial" || $term=="complimentary" || $term==""){
		 		if($term=="soft trial"){
					$softTrialSubCount=$softTrialSubCount+1;
		 		}elseif($term=="complimentary"){
					$compSubCount=$compSubCount+1;
		 		}
		 		$revToBeRecog ="0";
		 		$revPerDay="0";
		 		$revRecog="0";
		 	}else{
				if($term=="monthly" || strpos($term,'monthly')!==false){
					$durOfProductInDays = "30";
					$monthlySubCount = $monthlySubCount+1;
				}elseif($term=="quaterly" || strpos($term,'quaterly')!==false){
					$durOfProductInDays = "90";
					$quartrSubCount = $quartrSubCount+1;
				}elseif($term=="annual" || strpos($term,'annual')!==false || $term=="payment by check"){
					$durOfProductInDays = "365";
					$annualSubCount = $annualSubCount+1;
				}elseif($term=="6 months"){
					$durOfProductInDays = "60";
					$halfYearlySubCount = $halfYearlySubCount+1;
				}elseif($term=="singles"){
		 			$singleSubCount=$singleSubCount+1;
		 		}

		 		if($term=="singles"){
		 			$revRecog = $value['price'];
		 			$revToBeRecog = "0";
		 		}else{
					if(strtotime($startDate)>strtotime($fromPrevDate)){
						$calStartInPeriod = date_difftime($startDate,$filterToDate);
						$startInPeriod = intval($calStartInPeriod['d']+1);
					}else{
						$startInPeriod = "0";
					}

					if(strtotime($endDate)<strtotime($filterToDate)){
						$calEndInPeriod = date_difftime($filterFromDate,$endDate);
						$endInPeriod = intval($calEndInPeriod['d']+1);
					}else{
						$endInPeriod = "0";
					}

					$startEndFilterToDiff = date_difftime($filterToDate,$endDate);
					if (intval($startEndFilterToDiff['d'])<0){
						$startEndOutPeriod = "0";
					}else{
						$calStartEndOutPeriod = date_difftime($filterFromDate,$filterToDate);
						$startEndOutPeriod = intval($calStartEndOutPeriod['d']+1);
					}

				 	$totalOfInperiod = $startInPeriod+$endInPeriod;
					if(intval($totalOfInperiod)>0){
						$totalDays = $totalOfInperiod;
					}else{
						$totalDays = $startEndOutPeriod;
					}
					if($durOfProductInDays>0){
						if($value['price']!=="0"){
							$revPerDayFloat = $value['price']/$durOfProductInDays;
							$revPerDay = round($revPerDayFloat,2);
							$revRecog  = round($totalDays*$revPerDayFloat,2);
						}else{
							$revPerDay = $value['price'];
							$revRecog  = $value['price'];
						}
					}else{
						$revPerDay = $value['price'];
						$revRecog  = $value['price'];
					}

					if($startEndFilterToDiff['d']<0){
						$daysRemain = "0";
					}else{
						$daysRemainCal = date_difftime($filterToDate,$endDate);
						$daysRemain = $daysRemainCal['d'];
					}

			 		if($daysRemain=="0"){
						$revToBeRecog = "0";
					}else{
						if($revPerDayFloat>0){
							$revToBeRecog = round($daysRemain*$revPerDayFloat,2);
						}else{
							$revToBeRecog = round($daysRemain*$revPerDay,2);
						}
					}
		 		}
		 	}

		 	$totalRevRecog = $totalRevRecog+$revRecog;
		 	$totalRevToBeRecog = $totalRevToBeRecog+$revToBeRecog;

	 	}
		$daysInPeriod = date_difftime($filterFromDate,$filterToDate);
		$daysInPeriod = $daysInPeriod['d']+1;
		$totalPayingSubcribers = intval($monthlySubCount)+intval($quartrSubCount)+intval($annualSubCount)+intval($halfYearlySubCount);
	 	$summaryBody = "<table border='1px solid' width='100%' align='center'>
			<tr>
				<td class='summaryWidth'>Period</td>
				<td class='summaryWidthCount'>".$filterToDate." - ".$filterFromDate."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Monthly</td>
				<td class='summaryMiddleWidth'>".$monthlySubCount."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryWidth'>Soft Trial</td>
				<td class='summaryWidthCount'>".$softTrialSubCount."</td>
			</tr>
			<tr>
				<td class='summaryWidth'>Days in Period</td>
				<td class='summaryWidthCount'>".$daysInPeriod."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Quarterly</td>
				<td class='summaryMiddleWidth'>".$quartrSubCount."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryWidth'>Complimentary</td>
				<td class='summaryWidthCount'>".$compSubCount."</td>
			</tr>
			<tr>
				<td class='summaryWidth'>Revenue Recognized</td>
				<td class='summaryWidthCount'>$".number_format($totalRevRecog)."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Annual</td>
				<td class='summaryMiddleWidth'>".$annualSubCount."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryWidth'>Housing Market Singles</td><td class='summaryWidthCount'>".$singleSubCount."</td>
			</tr>
			<tr>
				<td class='summaryWidth'>Revenue to be Recognized</td>
				<td class='summaryWidthCount'>$".number_format($totalRevToBeRecog)."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Half Yearly</td>
				<td class='summaryMiddleWidth'>".$halfYearlySubCount."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryWidth'>Total Paying Subscribers</td>
				<td class='summaryWidthCount'>".$totalPayingSubcribers."</td>
			</tr>";

if(empty($productName))	{

			$summaryBody .="<tr>
				<td class='summaryWidth'>Total Purchases</td>
				<td class='summaryWidthCount'>$".number_format($purchasePrice)."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryMiddleWidthHead'>Total Refund</td>
				<td class='summaryMiddleWidth'>$".number_format($refundPrice)."</td>
				<td class='summaryWidthBlank'>&nbsp;</td><td class='summaryWidth'>Amount Charged to Clients</td>
				<td class='summaryWidthCount'>$".number_format($amtChargeToClient)."</td>
			</tr>";
}

		$summaryBody .="</table>";


		$val=array('status'=>true,'body'=>$summaryBody);
	}
	$output = $json->encode($val);
	echo $output;
	exit;
}
?>