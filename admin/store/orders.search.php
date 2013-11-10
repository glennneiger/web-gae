<?
$hide_ui=1;
include("./_header.php");
/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$addrlist=array(
	fname=>"First Name",
	lname=>"Last Name",
	email=>"Email",
	phone=>"Tel.",
	address=>"Address line 1",
	address2=>"Address line 2",
	city=>"City",
	state=>"State",
	zip=>"Zip Code"
);
$cccocols=array(
	trans_id=>"OrderID",
	trans_code=>"trackingID"
);
if(!$pagesize)$pagesize=20;
?>

<style>
body{
	margin:3px;
}
</style>

<script>
function Search(){
	document["theform"].submit();
}
function Cancel(){
	window.close();
}

</script>
<fieldset><legend>Click the checkbox next to the item you want to search.</legend>

<table width=100%>
<form method="get" name="theform" target="mainwindow" action="orders.php">
<?input_hidden("id")?>
<TR>
<TD colspan=4><label for="bydate"><b>Order Date</b></label></TD>
</TR>
<TR valign=top>
<TD><?input_check("bydate",$bydate)?></TD>
<TD>
	<table style="border:0;padding:0">
	<TR>
	<TD>From </TD>
	<TD> <?month_options("bmo",$bmo)?> <?day_options("bday",$bday)?>
	 <?year_options("byr",$byr,"",2003,date("Y"))?></TD>
	</TR>
	<TR>
	<TD align=right>to </TD>
	<TD><?month_options("emo",$emo)?> <?day_options("eday",$eday)?>
	 <?year_options("eyr",$eyr,"",2003,date("Y"))?></TD>
	</TR>
	</table>
</TD>
</TR>

<TR>
<TD colspan=4 style="padding-top:10px"><label for="bybill"><b>Billing Address</b></label></TD>
</TR>
<TR valign=top>
<TD><?input_check("bybill")?></TD>
<TD><select name="billcol">
	<?foreach($addrlist as $k=>$v){?>
		<?$sel=($billcol=="b_$k"?" selected":"");?>
		<option value="<?="b_$k"?>"><?=$v?></option>
	<?}?>
</select> is <?input_text("billrow")?>
</TD>
</TR>

<TR>
<TD colspan=4 style="padding-top:10px"><label for="byship"><b>Shipping Address</b></label></TD>
</TR>
<TR valign=top>
<TD><?input_check("byship")?></TD>
<TD><select name="shipcol">
	<?foreach($addrlist as $k=>$v){?>
		<?$sel=($shipcol=="s_$k"?" selected":"");?>
		<option value="<?="s_$k"?>"><?=$v?></option>
	<?}?>
</select> is <?input_text("shiprow")?>
</TD>
</TR>

<label for="byccname">
<TR>
<TD colspan=4 style="padding-top:10px"><label for="byccname"><b>Credit Card</b></label></TD>
</TR>
<TR valign=top>
<TD><?input_check("byccname")?></TD>
<TD>
	<table style="border:0px;padding:0px">
	<TR>
	<TD><label for="ccname">Type</label></TD>
	<TD>
		<select name="ccname">
		<?selectHash($STORE_CC_TYPES,$ccname)?>
		</select>
	</TD>
	</TR>
	<TR>
	<TD><label for="ccnum">Number</label></TD>
	<TD><?input_text("ccnum")?></TD>
	</TR>
	<TR>
	<TD><label for="ccexpmo">Expiry</label></TD>
	<TD><?month_options("ccexpmo")?> 
		<?year_options("ccexpyear",$ccexpyear,0,date("Y")+5,2003)?>
	</TD>
	</TR>
	</table>

</TD>
</TR>
</label>
<TR><TD colspan=4><div class=boxheader><?=spacer(1,2)?></div><br></TD></TR>
<label for="bystatus">
<TR>
<TD colspan=4><b>Order Status</b></TD>
</TR>
<TR>
<TD><?input_check("bystatus")?></TD>
	<TD><select name="status">
		<?selectHash($STORE_STATUS_TYPES,$status)?>
	</select></TD>
</TR>
</label>

<label for="bywasshipped">
<TR>
<TD colspan=4><b>Shipped Status</b></TD>
</TR>
<TR>
<TD><?input_check("bywasshipped")?></TD>
	<TD><select name="was_shipped">
		<?selectHash(array("0"=>"No","1"=>"Yes"),$was_shipped)?>
	</select></TD>
</TR>
</label>


<TR>
<TD colspan=4 style="padding-top:10px"><label for="byccproc"><b>YourPay&copy; Tracking</b></label></TD>
</TR>
<TR valign=top>
<TD><?input_check("byccproc")?></TD>
<TD><select name="ccproccol">
	<?selectHash($cccocols,$ccproccol)?>
</select> is <?input_text("ccprocrow")?>
</TD>
</TR>

<TR><TD colspan=4><div class=boxheader><?=spacer(1,2)?></div><br></TD></TR>
<label for="numrecs">
<TR>
<TD colspan=4><b>Number of records to show at a time</b></TD>
</TR>
<TR>
<TD>&nbsp;</TD>

<TD><?input_intonly("pagesize",$pagesize,3)?></TD>
</TR>
</label>






<TR><TD colspan=4><div class=boxheader><?=spacer(1,2)?></div><br>
<center>
<span class="button" onclick="Search()">search</span>
</center>

</TD></TR>
</form>
</table>
</fieldset>
<?include("./_footer.php");?>