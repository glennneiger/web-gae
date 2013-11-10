<?
//cart is instantiated in this. i'm just an include

$postto="store.checkout.process.php";
$ord=$cart->userdata;

if(/*$is_dev*/0){
	$ord=munserial(read_file("./order.hash"));
	list($emo,$eyr)=array($ord[exp_mo],$ord[exp_year]);
	$ord=$ord["ord"];
	$ord[cc_expire]=sprintf("%02d/%04d",$emo,$eyr);

}
list($exp_mo,$exp_year)=explode("/",$ord[cc_expire]);

$USER = new user();
$objVia=new Via();
$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);
$custDetails=$objVia->getCustomersViaDetail($arrayFields);
//$email= $custDetails->CustomerGetResult->Customer->loginInfo->login;
//$firstname=$custDetails->CustomerGetResult->Customer->nameFirst;

/*========specific to MV prepop with user info===========*/
if(is_object($USER)){

	//$bill=$USER->getInfo("bill");
	//$ship=$USER->getInfo("ship");

		$bill = array();
	$ship = array();

	/** Assignment of Billing Information into Array $bill*/
	
	$bill[via_id]= $custDetails->CustomerGetResult->Customer->loginInfo->customerIdVia;
	$bill[address1]= $custDetails->CustomerGetResult->Customer->loginInfo->address1;
	$bill[address2]= $custDetails->CustomerGetResult->Customer->addresses->address2;
	$bill[city]= $custDetails->CustomerGetResult->Customer->addresses->city;
	$bill[country]= $custDetails->CustomerGetResult->Customer->loginInfo->country;
	$bill[state]= $custDetails->CustomerGetResult->Customer->addresses->state;
	$bill[zip]= $custDetails->CustomerGetResult->Customer->loginInfo->zip;
	$bill[cc_types]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ePaymentTypes;
	$bill[cc_num]= $custDetails->CustomerGetResult->Customer->paymentAccounts->accountNumber;
	$bill[cc_expire]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ccExpire;
	$bill[cc_cvv2]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ccVerificationValue;
	$bill[lname]=$custDetails->CustomerGetResult->Customer->nameLast;
	$bill[fname]=$custDetails->CustomerGetResult->Customer->nameFirst;
	$bill[tel]=$custDetails->CustomerGetResult->Customer->phone;
	$bill[email]=$custDetails->CustomerGetResult->Customer->email;
	
	/** Assignment of Shipping Information into Array $ship*/

	$ship[via_id]= $custDetails->CustomerGetResult->Customer->loginInfo->customerIdVia;
	$ship[address1]= $custDetails->CustomerGetResult->Customer->loginInfo->address1;
	$ship[address2]= $custDetails->CustomerGetResult->Customer->addresses->address2;
	$ship[city]= $custDetails->CustomerGetResult->Customer->addresses->city;
	$ship[country]= $custDetails->CustomerGetResult->Customer->loginInfo->country;
	$ship[state]= $custDetails->CustomerGetResult->Customer->addresses->state;
	$ship[zip]= $custDetails->CustomerGetResult->Customer->loginInfo->zip;
	$ship[cc_types]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ePaymentTypes;
	$ship[cc_num]= $custDetails->CustomerGetResult->Customer->paymentAccounts->accountNumber;
	$ship[cc_expire]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ccExpire;
	$ship[cc_cvv2]= $custDetails->CustomerGetResult->Customer->paymentAccounts->ccVerificationValue;
	$ship[lname]=$custDetails->CustomerGetResult->Customer->nameLast;
	$ship[fname]=$custDetails->CustomerGetResult->Customer->nameFirst;
	$ship[tel]=$custDetails->CustomerGetResult->Customer->phone;
	$ship[email]=$custDetails->CustomerGetResult->Customer->email;
	
	foreach($bill as $k=>$v){
		if(!$ord["b_$k"])
			$ord["b_$k"]=$v;
	}
	foreach($ship as $k=>$v){
		if(!$ord["s_$k"])
			$ord["s_$k"]=$v;
	}
	if(!$ord[b_address])$ord[b_address]=$bill[address1];
	if(!$ord[address2])	$ord[address2]=$bill[address2];
	if(!$ord[s_address])$ord[s_address]=$ship[address1];
	if(!$ord[s_address2])$ord[s_address2]=$ship[address2];
	if(!$ord[b_phone]){
		$tel=preg_replace("/[^\d]/","",$USER->tel);
		$ord[b_phone]=$tel;
	}
	if(!$ord[b_email])
		$ord[b_email]=$USER->email;
	//exit;
}
/*========/specific to MV ===========*/

?>
<script src="/lib/_script.js"></script>
<script>
var txtlists="fname lname email address address2 city zip phone".split(" ");
var sellists="state country".split(" ");
var requiredtxt={
	"b_fname":["Please enter a billing first name",0],
	"b_lname":["Please enter a billing last name",0],
	"b_address":["Please enter a billing address line 1",0],
	"b_city":["Please enter a billing city",0],
	"b_zip":["Please enter a valid billing zip code","validZip2"],
	"b_country":["Please enter a billing country",0],
	"billingPhone":["Please provide a valid billing phone number","validPhone"],
	"b_email":["Please enter your email address","validEmailValue"],
	"s_fname":["Please enter a shipping first name",0],
	"s_lname":["Please enter a shipping last name",0],
	"s_address":["Please enter a shipping address line 1",0],
	"s_city":["Please enter a shipping city",0],
	"s_zip":["Please enter a valid shipping zip code","validZip2"],
	"s_email":["Please enter a valid shipping email address","validShipEmail"],
	"s_country":["Please enter a shipping country",0],
	"shippingPhone":["Please provide a valid shipping phone number","validPhone"],
	"no_arg_1":["Please enter a valid credit card number","validCard"],
	"no_arg_1a":["Please enter a valid credit card security code","validCode"],
	"no_arg_2":["Please enter a valid credit card expiration date","validExp"]

};
function validateForm(){
	selectCountry('s');selectCountry('b');//if they've chosen a state choose US
	deselectState('s');deselectState('b');//if not us deselect state
	var err="";
	for(e in requiredtxt){
		var fieldname="ord["+e+"]";
		var msg=requiredtxt[e][0];
		var callback=requiredtxt[e][1];
		if(!callback)callback="getValByName";
		if(!window[callback](fieldname))
			err+=" - "+msg+"\n";
	}
	if(selectedText("ord[b_country]").toLowerCase()=="united states"
		&& !selectedValue("ord[b_state]"))
				err+=" - Please select a billing state\n";
	if(selectedText("ord[s_country]").toLowerCase()=="united states"
		&& !selectedValue("ord[s_state]"))
				err+=" - Please select a shipping state\n";

/*	if(!selectedValue("ord[b_state]"))
		err+=" - Please select a billing state\n";
	if(!selectedValue("ord[s_state]"))
		err+=" - Please select a shipping state\n";		*/
	if(err.length){
		alert(err);
		return;
	}
	if(!shipIsSame()){
		var msg="You have indicated that you want your shipping and billing information to be the same "
			   +"however your form data appears different. Do you want to proceed?";
		if(!confirm(msg))
			return;
	}

	setTimeout("findObj('theform').submit()",750)
	//findObj("theform").submit();
}
function validShipEmail(){
	if(!findObj("ord[s_email]").value)
		return 1;
	if(!validEmailValue("ord[s_email]")){
		return 0;
	}
	return 1;
}
function validZip2(zipObj){
	//convert US zips to numbers only
	var isUS=0;
	if(stristr("b_",zipObj)){
		if(selectedText("ord[b_country]").toLowerCase()=="united states"){
			isUS=1;
			findObj(zipObj).value=intonly(findObj(zipObj).value);
		}
	}
	if(stristr("s_",zipObj)){
		if(selectedText("ord[s_country]").toLowerCase()=="united states"){
			isUS=1;
			findObj(zipObj).value=intonly(findObj(zipObj).value);
		}
	}
	//no zip codes should be less than 5 digits
	if(isUS){
		if(getValByName(zipObj).length!=5)
			return 0;
	}else{
		if(getValByName(zipObj).length<4)
			return 0;
	}
	return 1;
}

function validPhone(numberToValidate){
	var prefix=stristr("ship",numberToValidate)?"s_":"b_";
	var frmName=prefix+"phone";
	var frmVal=intonly( getValByName("ord["+frmName+"]") );
	var isUS=selectedText("ord["+prefix+"country]").toLowerCase()=="united states";
	findObj("ord["+frmName+"]").value=frmVal;
	if(isUS){
		if(frmVal.length!=10)
			return 0;
	}else{
		if(frmVal.length<10)
			return 0;
	}
	return 1;
}

function validCard(){
	return validateCCNum(selectedValue("ord[cc_type]"),getValByName("ord[cc_num]"));
}
function validCode(){
	var val=getValByName("ord[cc_cvv2]");
	if(val.length<3)return 0;
	return 1;

}
function validExp(){
	var mo=selectedValue("exp_mo");
	var yr=selectedText("exp_year");
	yr=yr.substring(2);
	return isValidExpDate(mo+"/"+yr);
}
function shipSame(){
	if(!findObj("shipsame").checked)return;
	for(i=0;i<txtlists.length;i++){
		var nname=txtlists[i];
		findObj("ord[s_"+nname+"]").value=findObj("ord[b_"+nname+"]").value;
	}
	for(i=0;i<sellists.length;i++){
		var nname=sellists[i];
		findObj("ord[s_"+nname+"]").selectedIndex=findObj("ord[b_"+nname+"]").selectedIndex;
	}
}
function shipIsSame(){
	if(!findObj("shipsame").checked)return 1;//return true because user doesn't care
	for(i=0;i<txtlists.length;i++){
		var nname=txtlists[i];
		if(findObj("ord[s_"+nname+"]").value!=findObj("ord[b_"+nname+"]").value)
			return 0;
	}
	for(i=0;i<sellists.length;i++){
		var nname=sellists[i];
		if(findObj("ord[s_"+nname+"]").selectedIndex!=findObj("ord[b_"+nname+"]").selectedIndex)
			return 0;
	}
	return 1;
}

function deselectState(prefix){
	//return;//no international
	if(!prefix)prefix="b";
	var statelist="ord["+prefix+"_state]";
	var counlist="ord["+prefix+"_country]";
	if(selectedText(counlist).toLowerCase()!="united states"){
		findObj(statelist).selectedIndex=0;
	}
}
function selectCountry(prefix){
	//return;//no international
	if(!prefix)prefix="b";
	var statelist="ord["+prefix+"_state]";
	var counlist="ord["+prefix+"_country]";
	if(findObj(statelist).selectedIndex>0){
		goToText(counlist,"United States");
	}
}
function returnToCart(){
	location.replace("<?=$cart->carturl?>");
}
function continueShopping(){
	location.replace("<?=$cart->last_product?>");
}

</script>

<form method="post" action="<?=$postto?>" name="theform" style="margin:0px;padding:0px">
<?input_hidden("refer")?>
<?input_hidden("curscreen")?>
<?input_hidden("cartdata")?>
<div class="checkout_body">
<table align="left" width="100%" style="border:solid 1px #cccccc; margin-bottom:10px;" border="0"  cellpadding="0" cellspacing="0">
<?if($error){?>
<TR>
<TD colspan="6" style="font-size:11px" align="left">
	<table border="0" width="100%" align="left" style="border-bottom:solid 1px #cccccc;" cellpadding="8" cellspacing="0">
	<TR>
	<TD class="error">
		<b>We encountered an error while processing your order:<br>
		<?=strip($error)?> </b>
	</TD>
	</TR>
	</table>
</TD>
</TR>
<?}?>
<TR valign=top>
<TD class="slimpadding">

<!-- ========================= billing =================== -->
<table border="0" cellspacing="5">
<TR><TD colspan=2><span class="common_shop_head">Billing Information</span><br>
 <span class="error">Billing info. must match bank info on your account</span></TD></TR>
<TR>
<TD>*First Name<br><?input_wordsonly("ord[b_fname]",$ord[b_fname])?>
</TD>
<TD>*Last Name<br><?input_wordsonly("ord[b_lname]",$ord[b_lname])?></TD>
</TR>
<TR>
<TD colspan="2">*Address Line 1:<br><?input_text("ord[b_address]",$ord[b_address],55,255)?></TD>
</TR>
<TR>
<TD colspan="2">Address Line 2:<br><?input_text("ord[b_address2]",$ord[b_address2],55,255)?></TD>
</TR>
<TR>
<TD>*City: <br><?input_text("ord[b_city]",$ord[b_city])?></TD>
<TD nowrap valign="top"><table><tr><td width="70px"><span>*State</span></td><td align="left"> *Zip Code</td></tr></table>
	<select name="ord[b_state]" style="width:80px; font-size:10px;" onchange="selectCountry('b')">
	<option value="">-State-</option>
	<?display_states($ord[b_state])?>
	</select>
	<?input_text("ord[b_zip]",$ord[b_zip],5,10)?>
</TD>
</TR>
 <TR>
<TD colspan=2>*Country<br>
	<select name="ord[b_country]" style="width:150px; font-size:10px;" onchange="deselectState('b')">
		<?display_countries($ord[b_country])?>
	</select>
</TD>
</TR> <!--<?input_hidden("ord[b_country]","United States")?>-->
<TR>
<TD colspan=2 nowrap>*Home Phone Number:<br><?input_intonly("ord[b_phone]",$ord[b_phone],20,20)?></TD>
</TR>
<TR>
<TD colspan=2>*Email Address: &nbsp;<span class="error">Valid email address required for order confirmation</span><br><?input_email("ord[b_email]",$ord[b_email],45,255)?></TD>
</TR>
</table>

</TD>
<TD class="slimpadding">

<!-- ========================= shipping =================== -->

<table cellspacing="5">
<TR><TD colspan=2><span class="common_shop_head">Shipping Information</span>  &nbsp;
	<input type="checkbox" onclick="shipSame()" id="shipsame" name="shipsame">
	<label for="shipsame" class="small" style="color:#ff0000">Same as billing</label>
</TD></TR>
<TR>
<TD>*First Name<br><?input_wordsonly("ord[s_fname]",$ord[s_fname])?>
</TD>
<TD>*Last Name<br><?input_wordsonly("ord[s_lname]",$ord[s_lname])?></TD>
</TR>
<TR>
<TD colspan=2>*Address Line 1:<br><?input_text("ord[s_address]",$ord[s_address],45,255)?></TD>
</TR>
<TR>
<TD colspan=2>Address Line 2:<br><?input_text("ord[s_address2]",$ordb[s_address2],45,255)?></TD>
</TR>
<TR>
<TD>*City: <br><?input_text("ord[s_city]",$ord[s_city])?></TD>
<TD nowrap><table><tr><td width="70px"><span>*State</span></td><td align="left"> *Zip Code</td></tr></table>
	<select name="ord[s_state]" style="width:80px; font-size:10px;" onchange="selectCountry('s')">
	<option value="">-State-</option>
	<?display_states($ord[s_state])?>
	</select>
	<?input_text("ord[s_zip]",$ord[s_zip],5,10)?>
</TD>
</TR>
 <TR>
<TD colspan=2>*Country<br>
	<select name="ord[s_country]" style="width:150px; font-size:10px;" onchange="deselectState('s')">
		<?display_countries($ord[s_country])?>
	</select>
</TD>
</TR> <!--<?input_hidden("ord[s_country]","United States")?>-->
<TR>
<TD colspan=2 nowrap>*Ship To Phone Number:<br>
	<?input_intonly("ord[s_phone]",$ord[s_phone],20,20)?>
</TD>
</TR>
<TR>
<TD colspan=2>Ship To Email Address:<br><?input_email("ord[s_email]",$ord[s_email],45,255)?></TD>
</TR>
</table>

</TD>
</TR>

<TR valign=top>
<TD class="slimpadding">

	<!-- ================== payment ======================= -->
 	<table cellspacing="5">
	<TR>
	<TD colspan=2><span class="common_shop_head">Payment Options</span></TD>
	</TR>
	<TR>
	<TD colspan=2>*Credit Card Type<br>
		<select name="ord[cc_type]" style="width:80px; font-size:10px;"><?selectHash($STORE_CC_TYPES,$ord[cc_type])?></select>
	</TD>
	</tr>
	<TR>
	<TD colspan=2>*Expiration date<br>
		<?month_options("exp_mo",$exp_mo);
		?>/<?year_options("exp_year",$exp_year,0, date("Y")+7,date("Y"));?>
	</TD>
	</TR>
	<TR>
	<TD>*Credit Card Number<br><?input_intonly("ord[cc_num]",$ord[cc_num],25,16)?></TD>
	<TD>&nbsp;*Security Code<br>&nbsp;
	<?input_intonly("ord[cc_cvv2]",$ord[cc_cvv2],4,4)?> <a href="javascript:;" class="blue_link" onclick="openCVV2()">What's this?</a>
	</TD>
	</TR>
	</table>


</TD>
<TD class="slimpadding">


	<?if(!$cart->hasGiftOnly()){?>
 	<table>
	<TR>
	<TD><span class="common_shop_head">Shipping Options</span></TD>
	</TR>
	<TR>
	<TD>Shipping Type<br>
	<select name="ord[shipping_type]">
		<?selectHash($STORE_SHIP_TYPES,$ord[shipping_type])?>
	</select></TD>
	</TR>
	</table>
	<?}?>
	<br>
	<table>
	<TR>
	<TD><b>Gift Card Message or General Instructions</b></TD>
	</TR>
	<TR>
	<TD><textarea name="ord[order_comments]" style="width:250;height:50"><?=strip($ord[order_comments])?></textarea></TD>
	</TR>
	</table>
</TD>
</TR>

<TR>
<TD colspan=6 class="slimpadding"><table width="0%" border="0" cellpadding="0" cellspacing="0" align="right">
  <tr>
    <td><b>*</b><span class="error">Required Field</span></td>
    <td><img src="<?=$IMG_SERVER?>/images/button_continueshopping.gif"  onclick="continueShopping()" hspace="10" vspace="10" onmouseover="bOn(this)" onmouseout="bOff(this)" style="cursor:pointer;"></td>
    <td><img src="<?=$IMG_SERVER?>/images/button_confirmorder.gif" style="cursor:pointer;"  onclick="validateForm()" vspace="10" onmouseover="bOn(this)" onmouseout="bOff(this)"></td>
  </tr>
</table></TD>
</tr>
</TR>

</table>
<div>
<br>
<div class="slimpadding">
<span class="common_shop_head">Express Delivery Information </span><br>
Orders received before 12 noon ET can be sent overnight express for an additional charge of $22.95 per shipping address, or two-day express for an additional $12.95 per shipping address of in stock items. Express shipping is not available for Saturday. Some items may not qualify.
</form>
</div>
<!-- IE 5.5 can't seem to successfully requuest in secure mode -->
<script src="store.checkout.process.php?step=nothing"></script>