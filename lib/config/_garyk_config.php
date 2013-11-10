<?php
global $HTPFX,$HTADMINHOST,$D_R,$HTHOST;
$CONTRIBUTOR_ID	=	'541'; /******For DIV **********/

$getGaryKItemsLimit	=	"8";
$GaryKProdName	=	"Gary K's Equity Trading Setups";
$garyKPostLimit='8';
$tickerMentionedLimit="30";

/*email alert configuration*/
$garyKFrom="Gary K's Trading Setups <subscriptions@minyanville.com>";
$garyKTemplate=$HTPFX.$HTADMINHOST."/emails/_eml_garyk_alert.htm";

$maxPostDispLimit	=	'50';
$noPostText = "Welcome to Gary K's Equity Trading Setups. Gary will soon publish starategies in ";
$noPostMailbagText = "Have a question for Gary?  Send it to <a href='mailto:garyk@minyanville.com'>garyk@minyanville.com</a> and I'll answer it. All mailbag responses will be posted on this page.";
?>