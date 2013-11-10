<?php
global $HTPFX,$HTADMINHOST,$D_R,$HTHOST;
$CONTRIBUTOR_ID	=	'98';
$techStartItemsLimit	=	"8";
$techStartProdName	=	"TechStart";
$techStartPostLimit='8';
$productTechStartName="TechStrat Report By Sean Udall";
$tickerMentionedLimit="30";

/*email alert configuration*/
$techStratFrom="TechStrat Report <subscriptions@minyanville.com>";
$techStratFromName="TechStrat Report";
$techStratFromEmail="subscriptions@minyanville.com";
$techStratTemplate=$HTPFX.$HTADMINHOST."/emails/_eml_techstrat_alert.htm";

$maxPostDispLimit	=	'50';
$noPostText = "Welcome to Tech Strat. We will soon publish tech starategies in ";
$noPostMailbagText = 'Welcome to Tech Strat. We are waiting for your queries for technical strategy on current stock market. Write us on <a href="mailto:tech-strat@minyanville.com">tech-strat@minyanville.com</a>';

$techStratMemcacheExpire='1800';
$techPortfolioInception='521720.40';	//2012 Current Value of Portfolio
$transactioncnt=10;
?>