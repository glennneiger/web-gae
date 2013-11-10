<?php
global $HTPFX,$HTADMINHOST,$D_R,$HTHOST;
$CONTRIBUTOR_ID	="('453','454','667')"; /******For DIV **********/

$getbuyhedgeItemsLimit	=	"4";
$buyhedgeProdName	=	"Buy and Hedge";
$buyhedgePostLimit='4';
$tickerMentionedLimit="30";

/*email alert configuration*/
$buyhedgeFrom="Buy and Hedge <subscriptions@minyanville.com>";
$buyhedgeFromName="Buy and Hedge";
$buyhedgeFromEmail="subscriptions@minyanville.com";
$buyhedgeTemplate=$HTPFX.$HTADMINHOST."/emails/_eml_buyhedge_alert.htm";
$maxPostDispLimit	=	'50';

$thumbWidthFeatured="220";
$thumbHeigthFeatured="220";
$thumbWidth="80";
$thumbHeigth="60";
$thumbSectionWidth="150";
$thumbSectionHeigth="150";

$landingContent=array();

$landingContent[0]['catId']=2;
$landingContent[0]['contentHeading1']="Trading";
$landingContent[0]['contentHeading2']="With ETFs";
$landingContent[0]['seeMoreUrl']="/buyhedge/trading-with-etfs/";
$landingContent[1]['catId']=3;
$landingContent[1]['contentHeading1']="Investing";
$landingContent[1]['contentHeading2']="With ETFs";
$landingContent[1]['seeMoreUrl']="/buyhedge/investing-with-etfs/";
$landingContent[2]['catId']=4;
$landingContent[2]['contentHeading1']="Hedging";
$landingContent[2]['contentHeading2']="With ETFs";
$landingContent[2]['seeMoreUrl']="/buyhedge/hedging-with-etfs/";
$landingContent[3]['catId']=5;
$landingContent[3]['contentHeading1']="Target";
$landingContent[3]['contentHeading2']="Portfolios";
$landingContent[3]['seeMoreUrl']="/buyhedge/target-portfolios/";

$tickerCountBuyHedge="30";

$tickerPageLimit="5";

$buyhedgeCatLimit=2;

$contentcount='2';
$shownum='2';

$buyhedgeTradingPostLimit=3;

?>