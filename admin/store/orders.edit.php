<?
$hide_ui=1;
global $cloudStorageTool,$HTPFX,$HTHOST,$D_R;
include("$ADMIN_PATH/_header.htm");
include_once("$D_R/lib/_cart.php");

/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$oMgr=new OrderManager();

$order=$oMgr->getOrder($id);

$udata=&$order;
foreach($order as $k=>$v){${$k}=$v;}
list($cc_mo,$cc_year)=explode("/",$cc_expire);
$cc_mo=intval($cc_mo);

if(count($_POST)){//user is editing the prices of the order
	$o=&$ord;
	//globalize this data to overwrite database. they're in edit mode
	foreach($o as $k=>$v){${$k}=$v;}
	$tax_total=round($o[tax_rate]*$o[subtotal],2);
	$total=round(
		floatval($tax_total)
		+floatval($o[shipping_charge])
		+floatval($o[subtotal])
	,2);
	$tax_rate=floatval($o[tax_rate]);
	$shipping_charge=$o[shipping_charge];
}

$default_message="
Thank you for shopping at http://$HTTP_HOST .

Your order (ID: $trans_code) has been sent.
";

if($shipping_track){
	$default_message.="\nYour Shipping Tracking Code is: $shipping_track\n";
}


if(!$date_modified)$date_modified=mysqlNow();


?>
<html>
<head>
<title>Edit Order</title>
<script src="/lib/_script.js" language="JavaScript1.2"></script>
<script>
window.focus();
var fields={
	"ord[b_fname]"   :["Billing first name", "getValByName"],
	"ord[b_lname]"   :["Billing last name"  , "getValByName"],
	"ord[b_email]"   :["Billing email"  , "validEmail"],
	"ord[b_address]" :["Billing address line 1","getValByName"],
	"ord[b_city]"    :["Billing city","getValByName"],
	"ord[b_state]"	 :["Billing state","selectedValue"],
	"ord[b_zip]"	 :["Billing zip code","validZip"],
	"ord[b_phone]"	 :["Billing phone number","validPhone"],
	"ord[s_fname]"   :["Shipping first name", "getValByName"],
	"ord[s_lname]"   :["Shipping last name"  , "getValByName"],
	"ord[s_address]" :["Shipping address line 1","getValByName"],
	"ord[s_city]"    :["Shipping city","getValByName"],
	"ord[s_state]"	 :["Shipping state","selectedValue"],
	"ord[s_zip]"	 :["Shipping zip code","validZip"]
};

function validateForm(){
	var err="";

/*	========= hold off on validation for now
	for(e in fields){
		var funcname=fields[e][1];
		if(! window[funcname](e) ){
			err+="  - Missing or invalid "+fields[e][0]+"\n";
		}
	}
	if( !validateCCNum(selectedValue("ord[cc_type]"),getValByName("ord[cc_num]")) )
		err+="  - Invalid credit card number\n";
	if(findObj("cc_mo").selectedIndex<1)
		err+="  - Invalid credit card expiration month\n";
	if(findObj("cc_year").selectedIndex<1)
		err+="  - Invalid credict card expiration year\n"
	if(err.length){
		alert("There was a problem with some of the info you provided:\n"+err);
		return;
	}*/
	findObj("buyform").action="orders.mod.php";
	findObj("buyform").submit();
}


function validEmail(frmObj){
	return valid_email(findObj(frmObj).value)
}

function reCalc(){
	findObj("buyform").action="orders.edit.php<?=qsa()?>#final";
	findObj("buyform").submit();
}

var txtlists="fname lname email address address2 city zip phone".split(" ");
var sellists="state country".split(" ");
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
function popWithCustomer(){
	findObj("mail_to").value="<?="$b_fname $b_lname <$b_email>"?>";
	findObj("mail_subject").value="Your order has been processed";
}
function popWithFulfillMent(){
	findObj("mail_to").value="<?=$STORE_FULFILLMENTCTR_PARAMS[toemail]?>";
	findObj("mail_subject").value="A order ID <?=$id?> has been updated";
}
function popWithSummary(){
	var txt=findObj("order_summary").innerText;
	findObj("mail_message").value=findObj("mail_message").value.replace(txt,"");
	if(findObj("addSummary").checked)
		findObj("mail_message").value+="\n"+txt+"\n";
}
function popWithAddress(){
	var txt=findObj("udata").innerText;
	findObj("mail_message").value=findObj("mail_message").value.replace(txt,"");
	if(findObj("addAddr").checked)
		findObj("mail_message").value+="\n"+txt+"\n";
}

</script>

<style>
body{
	border:0px;
	margin:0px;
	overflow:auto;
}
.title{
	font-size:13px;
	font-weight:bold;
	color:#000;
}
#bcctype,#ccmo,#ccyear{
	width:90px;
}
pre{
	background:#fff;
	padding:4px;
	height:175;
	overflow:auto;
}
.error{color:red;font-weight:bold}

.error{color:red;font-weight:bold}
#mail_subject,#mail_from,#mail_to{width:100%}
</style>
</head>



<form method="post" action="orders.mod.php" name="buyform" id="buyform">
<?input_hidden("ord[date_modified]",mysqlNow())?>
<?input_hidden("id")?>
<?input_hidden("ACTION","editorder")?>
<?refer()?>
<!-- order info -->
	<fieldset><legend>Order Information</legend>
		<table width=95%>
		<TR style="background:buttonface"><TD colspan=6><b>YourPay&reg; Info:</b></TD></TR>
		<TR valign=top style="background:buttonface">
		<TD class="small" nowrap>
			Order ID:<br><b class="small"><?=$trans_id?></b>
		</TD>
		<TD class="small">
			Tracking Code:<br><b class="small"><?=$trans_code?></b>
		</TD>
		<TD class="small">
			Error Msg:<br><b class="small"><?=$trans_msg?$trans_msg:"<i>(none)</i>";?></b>
		</TD>
		</TR>
		<TR><TD colspan=6 style="background:ThreeDShadow"><b>MV Order Info</b></TD></TR>
		<TR valign=top style="background:ThreeDShadow">
		<TD>Order ID:<br><b><?=$id?></b></TD>
		<TD>Date:<br><b class="small"><?=$date_created?></b></TD>
		<TD>Modified:<br><b class="small"><?=$date_modified?></b></TD>
		</TR>
		<TR valign=top>
		<TD colspan=3><br><br>
		<b>Summary Sent to Customer</b>:
		<pre id="order_summary"><?=trim(strip($order_summary))?></pre>
		</TD>
		</TR>
		</table>
<!-- end order info -->
</fieldset>

<fieldset><legend>Shipping</legend>
		<table>
		<TR>
		<TD><b>Shipping Type</b>:<a name="final"></a></TD>
		<TD colspan=3><select name="ord[shipping_type]">
			<option value="">--select shipping service--</option>
			<?selectHash($STORE_SHIP_TYPES,$shipping_type)?>
		</select></TD>
		</TR>
		<TR>
		<TD><b>Tracking #</b>:</TD>
		<TD><?input_text("ord[shipping_track]",$shipping_track)?></TD>
		<TD><b>Weight</b>:</TD>
		<TD><?input_text("ord[shipping_weight]",$shipping_weight)?></TD>
		</TR>
		<TR>
		<TD><b>Charge</b>:</TD>
		<TD>$<?input_text("ord[shipping_charge]",dollarF($shipping_charge))?></TD>
		<TD>Was Shipped</TD>
		<TD><select name="ord[was_shipped]"><?selectHash(array("0"=>"No","1"=>"Yes"),$was_shipped)?></select></TD>
		</TR>
	</table>
	<br>

	<div class="title">Final Charges</div>
	<table>
			<TR>
		<TD><b>Tax:</b></TD>
		<TD>$<?input_numsonly("ord[tax_total]",dollarF($tax_total))?></TD>
		<TD><b>Tax Rate</b></TD>
		<TD>&nbsp;&nbsp;<?input_numsonly("ord[tax_rate]",$tax_rate)?></TD>
		</TR>

		<TR>
		<TD><b>Subtotal:</b></TD>
		<TD>$<?input_numsonly("ord[subtotal]",dollarF($subtotal),20,20)?></TD>
		<TD><b>Total</b></TD>
		<TD>$<?input_numsonly("ord[total]",dollarF($total),20,20,"onfocus=this.blur()")?></TD>
		</TR>
		<TR>
		<TD colspan=4 align=right><input type="button" onClick="reCalc()" value="Re Calculate"><br>
			<span class="error small">you still have to save your changes!</span>
		</TD>
		</TR>
	</table>



</fieldset>


<fieldset><legend>Billing Address</legend>
	<table width="368" cellspacing="0" cellpadding="2" border="0">
	 <tr>
		    <td nowrap><label for="ord[b_fname]">First Name</label></td>
		    <td><?input_text("ord[b_fname]",$b_fname)?><?err("b_fname")?></td>
			<td align="right" nowrap><label for="ord[b_lname]">Last Name</label></td>
		    <td><?input_text("ord[b_lname]",$b_lname)?><?err("b_lname")?></td>
		</tr>
		<tr>
		    <td><label for="ord[b_address]">Address 1</label></td>
		    <td><?input_text("ord[b_address]",$b_address)?><?err("b_address")?></td>
			<td align="right"><label for="ord[b_address2]">Address 2</label></td>
		    <td><?input_text("ord[b_address2]",$b_address2)?><?err("b_address2")?></td>
		</tr>
		<tr>
		    <td><label for="ord[b_city]">City</label></td>
		    <td><?input_text("ord[b_city]",$b_city)?><?err("b_city")?></td>
			<td align="right"><label for="ord[b_state]">State</label></td>
		    <td><select name="ord[b_state]" id="ord[b_state]">
					<option value="">--State--</option>
					<?display_states($b_state)?>
				</select><?err("b_state")?></td>
		</tr>
		<TR>
			<TD><label for="ord[b_zip]">Zip Code</label></TD>
			<TD><?input_text("ord[b_zip]",$b_zip,7,7)?><?err("b_zip")?></TD>
    		<TD align="right">Country</TD>
			<TD><select name="ord[b_country]" style="width:115px">
				<?display_countries($b_country)?>
				</select><?err("b_country")?>
			</TD>
		</TR>
		<TR>
			<TD><label for="[b_email]">Email</label></TD>
			<TD><?input_email("ord[b_email]",$b_email)?></TD>
			<TD align="right"><label for="ord[b_phone]">Phone</label></TD>
			<TD nowrap><?input_text("ord[b_phone]",$b_phone,10,20)?></TD>
		</TR>
	</table>
</fieldset>

<fieldset><legend>Shipping Address <input type="checkbox" onClick="shipSame()" id="shipsame" name="shipsame">
	<label for="shipsame" class="error small">Same as billing</label></legend>
		<table width="368" cellspacing="0" cellpadding="2" border="0">
 		<TR>
		 <tr>
		    <td nowrap><label for="ord[s_fname]">First Name</label></td>
		    <td><?input_text("ord[s_fname]",$s_fname)?><?err("s_fname")?></td>
			<td nowrap align="right"><label for="ord[s_lname]">Last Name</label></td>
		    <td><?input_text("ord[s_lname]",$s_lname)?><?err("s_lname")?></td>
		</tr>
		<tr>
		    <td nowrap><label for="ord[s_address]">Address 1</label></td>
		    <td><?input_text("ord[s_address]",$s_address)?><?err("s_address")?></td>
			<td align="right"><label for="ord[s_address2]">Address 2</label></td>
		    <td nowrap><?input_text("ord[s_address2]",$s_address2)?><?err("s_address2")?></td>
		</tr>
		<tr>
		    <td><label for="ord[s_city]">City</label></td>
		    <td><?input_text("ord[s_city]",$s_city)?><?err("s_city")?></td>
			<td align="right"><label for="ord[s_state]">State</label></td>
		    <td><select name="ord[s_state]" id="ord[s_state]">
					<option value="">--State--</option>
					<?display_states($s_state)?>
				</select><?err("s_state")?></td>
		</tr>
		<TR>
			<TD  nowrap><label for="ord[s_zip]">Zip Code</label></TD>
			<TD><?input_text("ord[s_zip]",$s_zip,7,7)?><?err("s_state")?></TD>
    		<TD align="right">Country</TD>
			<TD><select name="ord[s_country]" style="width:115px">
				<?display_countries($s_country)?>
				</select>
			</TD>
		</TR>
		<TR>
			<TD><label for="ord[s_email]">Email</label></TD>
			<TD><?input_email("ord[s_email]",$s_email)?><?err("s_email")?></TD>
			<TD align="right"><label for="ord[s_phone]">Phone</label></TD>
			<TD nowrap><?input_text("ord[s_phone]",$s_phone,10,20)?></TD>
		</TR>
	</table>
</fieldset>
<fieldset><legend>Billing Information</legend>

	<table width="368" cellspacing="0" cellpadding="2" border="0">
 		 <tr>
		 	<TD><label for="ord[cc_type]">Card Type</label></TD>
			<TD><select name="ord[cc_type]" id="ord[cc_type]">
				<option value="">-Card Type-
				<?selectHash($STORE_CC_TYPES,$cc_type)?>
	</select><?err("cc_type")?></TD>
			<TD nowrap><label for="ord[cc_num]">Card No.</label></TD>
			<TD nowrap><?input_numsonly("ord[cc_num]",$cc_num)?><?err("cc_num")?> CVV2 <?=input_numsonly("ord[cc_cvv2]",$cc_cvv2,5,5)?></TD>
		 </tr>
		  <tr>
		 	<TD nowrap><label for="ccmo">Card Exp. Month</label></TD>
			<TD><?month_options("cc_mo",$cc_mo,'id="ccmo"')?></TD>
			<TD><label for="ccyear">Year</label></TD>
			<TD><select name="cc_year" id="ccyear">
				<option value="">--Year--
			<?foreach(range(2007,2020) as $yr){?>
				<option value="<?=$yr?>"<?=($cc_year==$yr?" selected":"")?>><?=$yr?></option>
			<?}?>
			</select><?err("cc_mo")?></TD>
		 </tr>
	</table>
</fieldset>


<fieldset><legend>Order Comments</legend>
	<table width=90% align=center>
	<TR valign=top>
	<TD colspan=2>
	<b>Add comment to this order</b><br>
	<textarea name="ord[admin_comments]" style="width:100%;height:100"><?=$admin_comments?></textarea></TD>
	</TR>
	<TR>
	<TD width=5%><b>Status</b></TD>
	<TD width=95%><select name="ord[status]">
		<?selectHash($STORE_STATUS_TYPES,$status)?>
		</select></TD>
	</TR>
	</table>
</fieldset>

<fieldset><legend>Send an Email <?input_check("sendmessage")?></legend>
	<table width=90% align=center style="display:expression(findObj('sendmessage').checked?'block':'none')">
	<TR>
	<TD>Recipients</TD>
	<TD>
		<?input_radio("pop_to","cust",0,"onclick='popWithCustomer()'")?>
		<label for="pop_tocust">To Customer</label> &nbsp;
		<?input_radio("pop_to","fulfill",0,"onclick='popWithFulfillMent()'")?>
		<label for="pop_tofulfill">To Fulfillment Center</label> &nbsp;
	</TD>
	</TR>
	<TR>
	<TD align="right">To Email:</TD>
	<TD><?input_text("mail_to")?></TD>
	</TR>
	<tr>
	<td width=1% nowrap align=right>Your Email:</td>
	<td width=99%><?input_text("mail_from",$STORE_CONFIRM_FROM)?></td>
	</tr>
	<tr>
	<td nowrap align=right>Email Subject:</td>
	<td><?input_text("mail_subject","Your Order Has Been Filled")?></td>
	</tr>
	<TR valign=top>
	<TD colspan=2>
	<b>Message to send</b> :
		<?input_check("addAddr",0,"onclick=popWithAddress()")?> <label for="addAddr">Add Customer Address</label>
		<?input_check("addSummary",0,"onclick=popWithSummary()")?> <label for="addSummary">Add Order Summary</label> <br>
<textarea name="mail_message" style="width:100%;height:200"><?=$default_message?></textarea>
	(This will get sent when you click "Save Changes")
	</TD>
	</TR>

	</table>
</fieldset>


	<fieldset><legend>Save Your Work</legend>
	<div align=right>
	<input type="button" onClick="window.close()" value="Close"></TD>
	<input type="button" onClick="validateForm()" value="Save Changes">
	</div>

	</fieldset>



</form>
<pre id="udata" style="display:none"><?include("$STORE_TEMPLATE_DIR/store.udata.tmpl.php")?></pre>

<?include("./_footer.php")?>
