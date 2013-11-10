<?php
set_time_limit(21600);
populateviadata();

function populateviadata()
{
//	$sqlMaxId="select max(old_customer_id) maxid from via_subscription";
$sqlMaxId="select count(distinct(old_customer_id)) maxid from via_subscription";
	$resMaxId=exec_query($sqlMaxId,1);
	if($resMaxId['maxid'])
		$maxid=$resMaxId['maxid'];
	else
		$maxid=0;
/*
	$sql="select id,fname,lname,prefix,email,address,address2,city,premium,
	state,zip,tel,password,cc_num,cc_expire,cc_type,type,account_status,
	DATE_FORMAT(from_unixtime(date) ,'%c/%e/%Y') start_date,
	DATE_FORMAT(from_unixtime(if(date_billingstart<>0,date_billingstart,NULL)) ,'%c/%e/%Y') billing_start,
	DATE_FORMAT(from_unixtime(if(date_lastbilled<>0,date_lastbilled,NULL)) ,'%c/%e/%Y') last_billing_date
	 from subscription where id>".$maxid." order by id asc limit 0,1000";
*/
        $sql="select max(id) id,fname,lname,prefix,email,address,address2,city,premium,
        state,zip,tel,password,cc_num,cc_expire,cc_type,type,account_status,date_disabled,
        DATE_FORMAT(from_unixtime(date) ,'%c/%e/%Y') start_date,cancelsubs,trial_status,
        DATE_FORMAT(from_unixtime(if(date_billingstart<>0,date_billingstart,NULL)) ,'%c/%e/%Y') billing_start,
        DATE_FORMAT(from_unixtime(if(date_lastbilled<>0,date_lastbilled,NULL)) ,'%c/%e/%Y') last_billing_date
         from subscription group by email order by email asc limit ".$maxid.",1000";

	$res=exec_query($sql);


	foreach($res as $value)
	{
		$via['old_customer_id']=$value['id'];
		$via['fname']=htmlentities($value['fname'],ENT_QUOTES);
		$via['lname']=htmlentities($value['lname'],ENT_QUOTES);
		$via['salutation']=$value['prefix'];
		$via['email']=$via['login']=$value['email'];
		if($value['address'])
		{
			$via['shipping_address1']=$via['billing_address1']= htmlentities($value['address'],ENT_QUOTES);
			$via['shipping_address2']=$via['billing_address2']=htmlentities($value['address2'],ENT_QUOTES);
			
			if($value['city']==''){
				$via['shipping_city']=$via['billing_city']='Unknown';
			}
			else{
				$via['shipping_city']=$via['billing_city']=htmlentities($value['city'],ENT_QUOTES);
			}
			if($value['state']==''){
				$via['shipping_state']=$via['billing_state']='XX';
			}
			else{
				$via['shipping_state']=$via['billing_state']=$value['state'];
			}
			$via['shipping_zip']=$via['billing_zip']=$value['zip'];
		}else
		{
			$via['shipping_address1']=$via['billing_address1']= "800 3rd Ave";
			$via['shipping_address2']=$via['billing_address2']="";
			$via['shipping_city']=$via['billing_city']="New York";
			$via['shipping_state']=$via['billing_state']="NY";
			$via['shipping_zip']=$via['billing_zip']="10022";
		}
		
		if($via['shipping_state']=="")
			$via['shipping_state']=$via['billing_state']="NY";
		
		$via['shipping_phone']=$via['billing_phone']=$value['tel'];
		$via['password']=$value['password'];
		
		
		if($value['cc_num']!='' && $value['cc_expire']!='' && $value['cc_type']!=''){
			$via['id_nbr']=$value['cc_num'];
			$via['credit_card_expire']=$value['cc_expire'];
			$via['payment_type']=getCCType($value['cc_type']);
		}
		else{
			$via['id_nbr']='';
			$via['credit_card_expire']='';
			$via['payment_type']='';		
		}
		
		populateExchange($via,$value);
		

		if($value['type']=='edu' ){
			//populateBuzz($via,$value);
			populateCooper($via,$value);
			populateFlexfolio($via,$value);
		}
		elseif($value['type']=='nonbuzz'){
				populateCooper($via,$value);
				populateFlexfolio($via,$value);
		}
		elseif($value['type']=='exchange'){
				populateCooper($via,$value);
				populateFlexfolio($via,$value);			
		}
		elseif($value['type']=='lmo'){
			// do nothing
		}
		else{
			$getPackage=populatePackage($via,$value);
			if($getPackage==0)
			{
				populateBuzz($via,$value);
				populateCooper($via,$value);
				populateFlexfolio($via,$value);
			}elseif($getPackage==1){
				populateFlexfolio($via,$value);
			}elseif($getpackage==2)
			{
				populateCooper($via,$value);
			}
		}
		
	}
}

function getCCType($cc_type)
{
	Switch($cc_type){
		Case 'amex':
			return 'AX';
			break;
		Case 'mastercard':
			return 'MC';
			break;
		Case  'visa':
			return 'VS';
			break;
		Case 'discover':
			return 'DS';
	}
}
function populateExchange($via,$subscriber)
{
	$via['order_code_id'] = "9";
	$via['source_code_id'] = "1";
	$via['subscription_def_id'] = "15";
	if($subscriber['last_blling_date'])
		$via['order_date']=$via['start_date']=$subscriber['last_blling_date'];
	elseif($subscriber['billing_start'])
		$via['order_date']=$via['start_date']=$subscriber['billing_start'];
	else
		$via['order_date']=$via['start_date']=$subscriber['start_date'];
	
	unset($via['account_status']);
	unset($via['premium']);
	unset($via['cancelsubs']);
	insert_query('via_subscription',$via);
}



function populatePackage($via,$subscriber)
{
	$sql="SELECT SP.combo_id,
	DATE_FORMAT(from_unixtime(SP.date) ,'%c/%e/%Y') start_date,SP.cancelsubs cancelsubs,SP.date_disabled date_disabled,
		DATE_FORMAT(from_unixtime(if(SP.date_billingstart<>0,SP.date_billingstart,NULL)) ,'%c/%e/%Y') billing_start,
		DATE_FORMAT(from_unixtime(if(SP.date_lastbilled<>0,SP.date_lastbilled,NULL)) ,'%c/%e/%Y') last_billing_date
	 from subscription S,subscription_ps SP where S.id=SP.subid and SP.account_status='enabled' and SP.combo='1' and S.id='".$subscriber['id']."' order by SP.id desc";
	$res=exec_query($sql,1);
	if($res)
	{
		$via['source_code_id'] = "2";
		
		if($res['cancelsubs']=='1' && $res['date_disabled']>0){
			$via['renewal_status']='1';
			$via['auto_payment']='0';
		}
		else{
			$via['renewal_status']='';
			$via['auto_payment']='';
		}
		
		
		if($res['combo_id']=='1')
		{
			$via['order_code_id'] = "11";
			$via['pkg_def_id'] = "2";
		}elseif($res['combo_id']=='2'){
			$via['order_code_id'] = "10";
			$via['pkg_def_id'] = "1";
		}
		if($res['last_billing_date'])
			$via['order_date']=$via['start_date']=$res['last_billing_date'];
		elseif($res['billing_start'])
			$via['order_date']=$via['start_date']=$res['billing_start'];
		else
			$via['order_date']=$via['start_date']=$res['start_date'];
		unset($via['account_status']);
		unset($via['premium']);	
		unset($via['cancelsubs']);	
		unset($via['date_disabled']);
		
		insert_query('via_subscription',$via);
		return $res['combo_id'];
	}
	else{
		return 0;
	}
}



function populateBuzz($via,$subscriber)
{
	if($subscriber['account_status']=='enabled' && $subscriber['premium']=='1')
	{
		if($subscriber['type']=='trial' && $subscriber['trial_status']=='inactive'){
			return 0;
		}
		$via['source_code_id'] = "1";
		
		if($subscriber['cancelsubs']=='1' && $subscriber['date_disabled']>0){
			$via['renewal_status']='1';
			$via['auto_payment']='0';
		}
		else{
			$via['renewal_status']='';
			$via['auto_payment']='';
		}
		
		if($subscriber['type']=='gift' || $subscriber['type']=='corp')
		{
			$via['order_code_id'] = "12";
			$via['subscription_def_id'] = "16";
		}elseif($subscriber['type']=='prem'){
			$via['order_code_id'] = "2";
			$via['subscription_def_id'] = "2";
		}elseif($subscriber['type']=='newyear'){
			$via['order_code_id'] = "2";
			$via['subscription_def_id'] = "4";
		}elseif($subscriber['type']=='trial' ){
				$via['order_code_id'] = "1";
				$via['subscription_def_id'] = "1";
		}
		if($subscriber['last_billing_date'])
			$via['order_date']=$via['start_date']=$subscriber['last_billing_date'];
		elseif($subscriber['billing_start'])
			$via['order_date']=$via['start_date']=$subscriber['billing_start'];
		else
			$via['order_date']=$via['start_date']=$subscriber['start_date'];
		
		unset($via['account_status']);
		unset($via['premium']);	
		unset($via['cancelsubs']);	
		unset($via['date_disabled']);
		unset($via['trial_status']);
		
		insert_query('via_subscription',$via);
	}
	else
		return 0;
}

function populateCooper($via,$subscriber)
{
	$sql="SELECT SP.combo_id,SP.type,
	DATE_FORMAT(from_unixtime(SP.date) ,'%c/%e/%Y') start_date,SP.cancelsubs cancelsubs,SP.date_disabled date_disabled,SP.trial_status trial_status,
	DATE_FORMAT(from_unixtime(if(SP.date_billingstart<>0,SP.date_billingstart,NULL)) ,'%c/%e/%Y') billing_start,
	DATE_FORMAT(from_unixtime(if(SP.date_lastbilled<>0,SP.date_lastbilled,NULL)) ,'%c/%e/%Y') last_billing_date
	 from subscription S,subscription_ps SP where S.id=SP.subid and SP.account_status='enabled' and SP.combo='0' and SP.prof_id='2' and S.id='".$subscriber['id']."' order by SP.id desc";
	$res=exec_query($sql,1);
	if($res)
	{
		if($res['type']=='trial' && $res['trial_status']=='inactive'){
			return 0;
		}
		$via['source_code_id'] = "1";
		
		if($res['cancelsubs']=='1' && $res['date_disabled']>0){
			$via['renewal_status']='1';
			$via['auto_payment']='0';
		}
		else{
			$via['renewal_status']='';
			$via['auto_payment']='';
		}
		
/************************************************
commented by afaque on 6 jan 2009
		if($subscriber['type']=='gift')
		{
			$via['order_code_id'] = "13";
			$via['subscription_def_id'] = "17";
		}elseif($subscriber['type']=='prem'){
			$via['order_code_id'] = "6";
			$via['subscription_def_id'] = "11";
		}elseif($subscriber['type']=='newyear'){
			$via['order_code_id'] = "6";
			$via['subscription_def_id'] = "13";
		}elseif($subscriber['type']=='trial'){
			$via['order_code_id'] = "5";
			$via['subscription_def_id'] = "10";
		}
***********************************************/

/**********************************************
added by afaque on 6 jan 2009
**********************************************/
		if($res['type']=='gift' || $res['type']=='corp')
		{
			$via['order_code_id'] = "13";
			$via['subscription_def_id'] = "17";
		}elseif($res['type']=='prem'){
			$via['order_code_id'] = "6";
			$via['subscription_def_id'] = "11";
		}elseif($res['type']=='newyear'){
			$via['order_code_id'] = "6";
			$via['subscription_def_id'] = "13";
		}elseif($res['type']=='trial'){
			$via['order_code_id'] = "5";
			$via['subscription_def_id'] = "10";
		}
/*************************************************/		
		if($res['last_billing_date'])
			$via['order_date']=$via['start_date']=$res['last_billing_date'];
		elseif($res['billing_start'])
			$via['order_date']=$via['start_date']=$res['billing_start'];
		else
			$via['order_date']=$via['start_date']=$res['start_date'];
		unset($via['account_status']);
		unset($via['premium']);		
		unset($via['cancelsubs']);	
		unset($via['date_disabled']);
		unset($via['trial_status']);		
		insert_query('via_subscription',$via);
	}else{
		return  0;
	}
}

function populateFlexfolio($via,$subscriber)
{
	$sql="SELECT SP.type,
	DATE_FORMAT(from_unixtime(SP.date) ,'%c/%e/%Y') start_date,SP.cancelsubs cancelsubs,SP.date_disabled date_disabled,SP.trial_status trial_status,
	DATE_FORMAT(from_unixtime(if(SP.date_billingstart<>0,SP.date_billingstart,NULL)) ,'%c/%e/%Y') billing_start,
	DATE_FORMAT(from_unixtime(if(SP.date_lastbilled<>0,SP.date_lastbilled,NULL)) ,'%c/%e/%Y') last_billing_date
	 from subscription S,subscription_ps SP where S.id=SP.subid and SP.account_status='enabled' and SP.combo='0' and SP.prof_id='3' and S.id='".$subscriber['id']."' order by SP.id desc";
	$res=exec_query($sql,1);
	
	if($res)
	{
		if($res['type']=='trial' && $res['trial_status']=='inactive'){
			return 0;
		}	
		$via['source_code_id'] = "1";
		
		if($res['cancelsubs']=='1' && $res['date_disabled']>0){
			$via['renewal_status']='1';
			$via['auto_payment']='0';
		}
		else{
			$via['renewal_status']='';
			$via['auto_payment']='';
		}
		
		
/******************************************************	
commented by afaque on 6 jan 2008
		if($subscriber['type']=='gift')
		{
			$via['order_code_id'] = "14";
			$via['subscription_def_id'] = "18";
		}elseif($subscriber['type']=='prem'){
			$via['order_code_id'] = "4";
			$via['subscription_def_id'] = "6";
		}elseif($subscriber['type']=='newyear'){
			$via['order_code_id'] = "4";
			$via['subscription_def_id'] = "9";
		}elseif($subscriber['type']=='trial'){
				$via['order_code_id'] = "3";
				$via['subscription_def_id'] = "5";
		}
***********************************************************/			
/**********************************************************
added by afaque on 6 jan 2009
**********************************************************/
		if($res['type']=='gift' || $res['type']=='corp')
		{
			$via['order_code_id'] = "14";
			$via['subscription_def_id'] = "18";
		}elseif($res['type']=='prem'){
			$via['order_code_id'] = "4";
			$via['subscription_def_id'] = "6";
		}elseif($res['type']=='newyear'){
			$via['order_code_id'] = "4";
			$via['subscription_def_id'] = "9";
		}elseif($res['type']=='trial' && $res['trial_status']!='inactive'){
				$via['order_code_id'] = "3";
				$via['subscription_def_id'] = "5";
		}
/**************************************************************/
		if($res['last_billing_date'])
			$via['order_date']=$via['start_date']=$res['last_billing_date'];
		elseif($res['billing_start'])
			$via['order_date']=$via['start_date']=$res['billing_start'];
		else
			$via['order_date']=$via['start_date']=$res['start_date'];
		
		unset($via['account_status']);
		unset($via['premium']);
		unset($via['cancelsubs']);	
		unset($via['date_disabled']);
		unset($via['trial_status']);		
		insert_query('via_subscription',$via);
	}else{
		return  0;
	}
}
?>
