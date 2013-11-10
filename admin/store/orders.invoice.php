<?
global $D_R;
include_once($D_R."/lib/_cart.php");
include("./_header.php");
/*============set up permissions=================*/
$adminall=$AD->getPerm("admin_users_all");

/*=============throw error if they're not allowed anything on this page====*/
if(!$adminall){
	admin_error("You're not allowed to use this page.");
}

$oMgr=new OrderManager();

foreach($oMgr->getOrder($id) as $k=>$v){
	${$k}=$v;
}
if(!$date_created){
	$date_created=date("m/j/Y h:i:s a");
}
$cc_num=CartDisplay::hideCCNum($cc_num);

$cart=munserial($order_code);

if(!count($cart)){
	echo "<h1>there was an error with this order</h1>";
	exit;
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
	</td>
	<TD width=50% align=right>
	    <span class=big>Invoice</span>
		<table cellpadding=5 cellspacing=0 class=border width=70%>
		<TR align=center>
		<TD>Date</td>
		<TD>Invoice #</td>
		</tr>
		<TR align=center>
		<TD nowrap><?=$date_created?> CST</td>
		<TD><?=$id?></td>
		</tr>
		<?if($trans_id || $trans_code){?>
			<TR>
			<TD colspan=2 nowrap>
<!-- 				<?if($trans_id){?>
					<b>Order ID:</b> <?=$trans_id?><br>
				<?}?> -->
				<?if($trans_code){?>
					<b>Tracking ID:</b> <?=$trans_code?>
				<?}?>
			</TD>
			</TR>
		<?}?>
		</table>

	</td>
	</tr>
	</table>
	<p>



	<table width=100%>
	<TR valign=top>
	<TD width=50%>
		<table cellpadding=5 cellspacing=0 width=90% class=border>
		<TR>
		<TD>Bill To:</td>
		</tr>
		<TR>
		<TD><?=strip("$b_fname $b_lname")?><br />
			<?=strip($b_address)?><br />
			<?if($b_address2){?><?=$b_address2?><br /><?}?>
			<?=$b_city?>
			<?=$b_state?>,
			<?=$b_zip?><br />
			<?=$b_country?><br />
			Tel: <?=$b_phone?><br />
			Email: <a href="mailto:<?=$b_email?>"><?=$b_email?></a><br>
			CC : <?=$cc_type?><br>
			CC# : <?=$cc_num?><br>
			CC Expiration: <?=$cc_expire?>

		</td>
		</tr>
		</table>



	</td>
	<TD width=50% align=right>
		<table cellpadding=5 cellspacing=0 width=90% border=0 class=border>
		<TR>
		<TD>Ship To:</td>
		</tr>
		<TR>
		<TD><?="$s_fname $s_lname"?><br />
			<?=$s_address?><br />
			<?if($s_address2){?><?=$s_address2?><br /><?}?>
			<?=$s_city?>
			<?=$s_state?>, <?=$s_zip?><br />
			<?=$s_country?><br />
			Tel: <?=$s_phone?><br />
			Email: <a href="mailto:<?=$s_email?>"><?=$s_email?></a><br>
		</td>
		</tr>
		</table>

		<br><br>
		<table cellpadding=5 cellspacing=0 width=90% border=0 class=border>
		<TR>
		<TD>Shipping Service:</td>
		</tr>
		<TR>
		<TD><?=$shipping_type?> &nbsp;
		<?=$shipping_track?>
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

	<?foreach($cart as $row){?>
		<tr valign="top"  class=notbborder>
		<td align=center class=notbborder><?=$row[quantity]?>&nbsp;</td>
		<td  class=notbborder>
			<?=strip($row[title])?>     &nbsp;
			<?foreach((array)$row[params] as $k=>$v){
				echo " <b>$k</b>: $v&nbsp;";
			}?>
		</td>
		<td class=notbborder>$<?=dollars($row[price])?>&nbsp;</td>
		<td class=notbborder>$<?=dollars($row[sum])?>&nbsp;</td>
	</tr>
	<?}?>

	<tr valign="top">
	<td colspan=2><b>Comments:</b><br> <?=$admin_comments?>&nbsp;</td>
	<td align=right nowrap style="border-right-width:0px">subtotal: <br>
tax:<br>
shipping: <br>
<b>Total</b>:</td>
	<TD style="border-left-width:0px">$<?=$subtotal?><br>
	 $<?=dollars($tax_total)?><br>
	 $<?=dollars($shipping_charge)?><br>
	<b>$<?=dollars($total)?></b></td>
	</tr>
	</table>
</td>
</tr>
</table>

</body>
</html>


