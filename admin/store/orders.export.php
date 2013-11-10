<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
	$qry="SELECT 
		id 'OrderID', trans_id 'TransID', trans_code 'TransTrack', 
		b_fname 'BillingFname', b_lname 'BillingLname', b_email 'BillingEmail', 
		b_phone 'BillingPhone', 
		b_address 'BillingAddress', b_address2 'BillingAddress2', 
		b_city 'BillingCity', b_state 'BillingState', b_zip 'BillingZip', 
		b_country 'BillingCountry', 
		s_fname 'ShippingFname', s_lname 'ShippingLname', 
		s_email 'ShippingEmail', s_phone 'ShippingPhone', 
		s_address 'ShippingAddress', 
		s_address2 'ShippingAddress2', s_city 'ShippingCity', s_state 'ShippingState', 
		s_zip 'ShippingZip', s_country 'ShippingCountry', 
		cc_type 'CCType', cc_num 'CCNum', cc_expire 'CCExp', cc_cvv2 'CCVV2', 
		shipping_type 'ShippingType', shipping_track 'ShippingTrack', 
		shipping_charge 'ShippingCharge', shipping_weight 'ShippingWeight', 
		date_format(date_created,'%m/%d/%Y %r') 'DateCreated', 
		date_format(date_modified,'%m/%d/%Y %r') 'DateModified', 
		tax_rate 'TaxRate', tax_total 'TaxTotal', discount 'Discount', 
		subtotal 'SubTotal', total 'GrandTotal', 
		order_comments 'Comments', '#' AS 'SKUS',
		order_summary 'Summary' 
		FROM $ORDER_TABLE ";

$formats=qw("xml csv");

if(!$format || !in_array($format,$formats))
	$format="csv";
$filename="MVOrders".date("m-d-Y");

if($format=="csv"){
	csv_header($filename.".csv");
/*	qry2csv($qry);
	exit;*/
	$db=new dbObj($qry);
	$first=0;
	while($row=$db->getRow()){
		$qry="SELECT order_code c FROM $ORDER_TABLE WHERE id='$row[OrderID]'";
		$code=munserial(exec_query($qry,1,"c"));
		$skus=extract_key($code, "sku");
		$row[SKUS]=implode("\n",$skus);
		if(!$first){//column headings
			echo csv_line(array_keys($row));
			$first=1;
		}
		echo csv_line($row);
	}
	exit();
}


if($format=="xml"){
	attachment_header($filename.".xml","text/xml");
	qry2xml($qry,0,"order");
	exit();
}



?>