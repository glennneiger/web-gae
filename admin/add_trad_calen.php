<?
global $ADMIN_PATH,$HTPFX,$HTHOST,$CDN_SERVER;
$page="trad_calen";
include($ADMIN_PATH."/_header.htm");
$objCache= new Cache();
if(!$AD->getPerm('add_trad_calen')){
	admin_error("You're not allowed to use this page.");
}
if($_POST['action']=="addTicker")
{
	$result = $objCache->setTradCalenStock($_POST['stocksymbol']);
	$msg = "Stock has been changed";
}

$curStock = $objCache->getTradCalenStock();
?>
<script src="<?=$CDN_SERVER?>/js/min/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/jquery.ticker.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$CDN_SERVER?>/css/jquery.autocomplete.css" />
<script type="text/javascript">
function submitTradCalenForm(){
	jQuery('#error_ticker').empty();
	if(jQuery('#stocksymbol').val()==''){
		alert('Please Enter Ticker Value.');
		return false;
	}
	jQuery('#addticker').submit();

}
</script>
<div class="add_ticker">
<h1 class="tickerHeader">Trading Calendar</h1>
<?if(!empty($msg)){?><div class="error" id="error_ticker"><?=$msg?><br/></div><?}?>
<center>
<form id="addticker"  name="addticker" method="post" enctype="multipart/form-data">
<table align=left border=0>
<TR>
<TD align=left colspan="2"><b>Current Stock Symbol</b>: <?=$curStock?></TD>
</TR>
<TR colspan ="2">
</TR>
<TR>
<TD align=left><b>Change Stock Symbol</b>:</TD>
<td align="left" valign="top">
<?input_text("stocksymbol","",0,255," style='width:100%'")?>
<input type="hidden" name="action" id="action" value="addTicker">
</td>
<td><input type="button" value="Change stock" onClick="submitTradCalenForm();" style="cursor:pointer;" /></td>
</TR>
</table>
</form>
</center><br>
<?include($ADMIN_PATH."/_footer.htm")?>