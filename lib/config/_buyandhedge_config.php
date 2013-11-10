<?php
global $HTPFX,$HTADMINHOST,$D_R,$HTHOST;
$CONTRIBUTOR_ID	="('453','454','667')"; /******For DIV **********/
$BIO_ID	="667";

$getbuyhedgeItemsLimit	=	"8";
$buyhedgeProdName	=	"ETF Strategies by ";
$buyhedgePostLimit='8';
$tickerMentionedLimit="10";
$transtypearray = array(0=>'Buy',1=>'Sell',2=>'SS',3=>'BTC');

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

$buyHedge_memcache_expire="30";

$landingContent=array();

$landingContent[0]['catId']=2;
$landingContent[0]['contentHeading1']="Trading";
$landingContent[0]['contentHeading2']="With ETFS";
$landingContent[0]['seeMoreUrl']="/buyhedge/trading-with-etfs/";
$landingContent[1]['catId']=3;
$landingContent[1]['contentHeading1']="Investing";
$landingContent[1]['contentHeading2']="With ETFS";
$landingContent[1]['seeMoreUrl']="/buyhedge/investing-with-etfs/";
$landingContent[2]['catId']=4;
$landingContent[2]['contentHeading1']="Hedging";
$landingContent[2]['contentHeading2']="With ETFS";
$landingContent[2]['seeMoreUrl']="/buyhedge/hedging-with-etfs/";
$landingContent[3]['catId']=5;
$landingContent[3]['contentHeading1']="Target";
$landingContent[3]['contentHeading2']="Portfolios";
$landingContent[3]['seeMoreUrl']="/buyhedge/target-portfolios/";

$tickerCountBuyHedge="30";
$tickerMentionedLimit="30";

$tickerPageLimit="5";

$buyhedgeCatLimit=2;

$contentcount='2';
$shownum='2';

$buyhedgeTradingPostLimit=3;

global $bhPortfolioInception2012;
$bhPortfolioInception2012=100000.00; // Afaque's change

?>