<?
global $D_R;
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once($D_R."/lib/_cart.php");

$cart=new CartDisplay();
if(!count($cart->_data)){
	echo "<h1>There was an error with this order!</h1>";
	exit;
}
foreach($cart->_data as $k=>$v){
	${$k}=$v;
}
if(!$date_created){
	$date_created=date("m/j/Y h:i:s a");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>Minyanville.com Invoice</title>
<style>
body,td,p,i,b,font{font:normal 12px arial}
.items td{font-size:10px}
.big{font-size:20px;font-weight:bold}
b{font-weight:bold}
i{font-style:italic}
.border td,.theborder,.notbborder{border:1px black solid}
.notbborder,.border .notbborder{
	border-bottom-width:0px;
	border-top-width:0px;
}
.lite{
	color:#666;
	border-bottom:1px #666 solid;
	margin-bottom:7px;
	font-size:9px;
}
.lite b{
	color: 000;
	font-weight:normal;
	letter-spacing:-1px;
}
</style>
</head>

<body bgcolor="#ffffff" style="border:0px;margin:10px;overflow:auto">

<table width=640 bgcolor="#ffffff" align=center  cellpadding=20 class=theborder>
<TR>
<TD>
	<table width=100%>
	<TR valign=top>
	<TD width=50%>
		<img src="/assets/invoice_logo.gif" border=0><br>
		<b>Minyanville.com</b><br>
		<a href="mailto:<?=$STORE_CONFIRM_FROM?>"><?=$STORE_CONFIRM_FROM?></a><br>
		Fax: 805-681-8201
	</td>
	<TD width=50% align=right>
	    <span class=big>Invoice</span>
		<table cellpadding=5 cellspacing=0 class=border width=70%>
		<TR align=center>
		<TD>Date</td>
		</tr>
		<TR align=center>
		<TD nowrap><?=$date_created?> CST</td>
		</tr>
		</table>

	</td>
	</tr>
	</table>
	<p>



	<table width=100%>
	<TR valign=top>
	<TD width=50%>
		<table cellpadding=5 cellspacing=0 width=98% class=border>
		<TR>
		<TD>Bill To:</td>
		</tr>
		<TR>
		<TD>
		<div style="padding:2px">
			<div class="lite">(name)</div>
			<div class="lite">(street)</div>
			<div class="lite">(street)</div>
			<div class="lite">(city,state,zip)</div>
			<div class="lite">(country)</div>
			<div class="lite">(phone)</div>
			<div class="lite">(email)</div>
			<div class="lite">
			<table cellpadding=0 cellspacing=0 border=0>
			<TR valign=top>
			<TD class="lite" style="border-width:0px" nowrap>(credit card) </TD>
			<TD width=40 style="border-width:0px">&nbsp;</TD>
			<TD style="border-width:0px;font-weight:normal" align=center><b><?=implode("&nbsp;&nbsp;&nbsp;&nbsp; ",array_values($STORE_CC_TYPES))?></b></TD>
			</TR>
			</table>
			</div>
			<div class="lite">(cc#)</div>
			<div class="lite">(cc. security code)</div>
			<div class="lite">(cc expires)</div>
		</div>

		</td>
		</tr>
		</table>



	</td>
	<TD width=50% align=right>
		<table cellpadding=5 cellspacing=0 width=98% border=0 class=border>
		<TR>
		<TD>Ship To:</td>
		</tr>
		<TR>
		<TD>
	<div style="padding:2px">
			<div class="lite">(name)</div>
			<div class="lite">(street)</div>
			<div class="lite">(street)</div>
			<div class="lite">(city,state,zip)</div>
			<div class="lite">(country)</div>
			<div class="lite">(phone)</div>
		</div>

		</td>
		</tr>
		</table>

		<br>
		<table cellpadding=5 cellspacing=0 width=98% border=0 class=border>
		<TR>
		<TD>Shipping Service (circle one):</td>
		</tr>
		<TR height=40>
		<TD align=center class="lite">
			<b><?=implode("&nbsp;&nbsp;&nbsp;&nbsp;",array_values($STORE_SHIP_TYPES))?></b>
		</tr>
		</table>


	</td>
	</tr>
	</table>
 <br>


	<table width="100%" border="0" cellspacing="0" cellpadding="5" class=border>
	<tr>
	<td align="center" width="1%">Qty.</td>
	<td>Description</td>
	<td width="1%" nowrap>Each</td>
	<td width="1%">Amount</td>
	</tr>

	<?foreach($contents as $row){?>
		<tr valign="top">
		<td align=center><?=$row[quantity]?>&nbsp;</td>
		<td>
			<?=strip($row[title])?>     &nbsp;
			<?foreach((array)$row[params] as $k=>$v){
				echo " <b>$k</b>: $v&nbsp;";
			}?>
			<?if($row[min_purchase_count]>1){?>
			<br><i style="font-weight:bold">Mininum purchase is <?=$row[min_purchase_count]?> items</i>
			<?}?>
		</td>
		<td>$<?=dollars($row[price])?>&nbsp;</td>
		<td>$<?=dollars($row[sum])?>&nbsp;</td>
	</tr>
	<?}?>

	<tr valign="bottom" height=100>
	<td colspan=2 valign=top><b>Gift Card Message or General Instructions:</b><br>&nbsp;</td>
	<td align=right nowrap style="border-right-width:0px">
		Shipping:<br>
		Tax@<?=$STORE_TAX_RATE*100?>%:<br>
		<b>SubTotal</b>:<br>
		<b>Total:</b><br>
	</td>
	<TD style="border-left-width:0px">
		<div class="lite" style="padding:0px;margin:0px">&nbsp;</div>
		$<?=dollars($STORE_TAX_RATE*$total)?><br>
		$<?=dollars(($STORE_TAX_RATE*$total)+$total)?><br>
		<div class="lite" style="padding:0px;margin:0px">&nbsp;</div>
	</td>
	</tr>
	</table><br>
<b>Ground Shipping and Handling Charges </b><br>
Under $25:        $8.95 <br>
$25-$50:            $12.95 <br>
$50.01-$100:     $18.95 <br>
$100.01-$150:   $23.95 <br>
Over $150:          $27.95 <br>
<br><br>

Express Shipping: $22.95 + Standard Shipping Charge  <br>
Two-Day Shipping: $12.95 + Standard Shipping Charge <br>
<br><br>
<b>Express Delivery Information </b>
Orders received before 12 noon ET can be sent overnight express for an additional charge of $22.95 per shipping address, or two-day express for an additional $12.95 per shipping address of in stock items. Express shipping is not available for Saturday. Some items may not qualify.
</td>
</tr>
</table>

</body>
</html>


