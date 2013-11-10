<?php

/*=============== Product configuration starts ==============*/
$viaPrdouctsarray = array(
'B&B_FT_Monthly'=>'1',
'B&B_FT_Annual'=>'3',
'JC_FT_Monthly'=>'10',
'JC_FT_Annual'=>'12',
'Flex_FT_Monthly'=>'5',
'Flex_FT_Annual'=>'8',
'OS_FT_Monthly'=>'25',
'OS_FT_Annual'=>'24',
'Jack_FT_Monthly'=>'36',
'Jack_FT_Annual'=>'37',
'ETF_FT_Monthly'=>'51',
'ETF_FT_Quart'=>'61',
'ETF_FT_Annualy'=>'60',
'ETF_FT1M_Annualy'=>'66',
'TheStockPlaybook_FT_Monthly'=>'73',
'TheStockPlaybook_FT_Quart'=>'72',
'TheStockPlaybook_FT_Annualy'=>'71',
'Ex_Email_Alert'=>'15',
'BuyHedge_FT_Monthly'=>'95',
'BuyHedge_FT_Quart'=>'96',
'BuyHedge_FT_Annualy'=>'91',
'BuyHedge_FT1M_Annualy'=>'105'
);

/*=============== Product configuration Ends   ==============*/


/*============ Products array ============================*/

/*============ Buzz Starts ============================*/
$viaProducts['BuzzMonthlyTrial']['orderClassId']=3;
$viaProducts['BuzzMonthlyTrial']['orderCodeId']=1;
$viaProducts['BuzzMonthlyTrial']['typeSpecificId']=1;
$viaProducts['BuzzMonthlyTrial']['price']=0.00;
$viaProducts['BuzzMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzQuartTrial']['orderClassId']=3;
$viaProducts['BuzzQuartTrial']['orderCodeId']=1;
$viaProducts['BuzzQuartTrial']['typeSpecificId']=130;
$viaProducts['BuzzQuartTrial']['price']=0.00;
$viaProducts['BuzzQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzAnnualTrial']['orderClassId']=3;
$viaProducts['BuzzAnnualTrial']['orderCodeId']=1;
$viaProducts['BuzzAnnualTrial']['typeSpecificId']=3;
$viaProducts['BuzzAnnualTrial']['price']=0.00;
$viaProducts['BuzzAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzMonthly']['orderClassId']=3;
$viaProducts['BuzzMonthly']['orderCodeId']=2;
$viaProducts['BuzzMonthly']['typeSpecificId']=2;
$viaProducts['BuzzMonthly']['plan_code']='buzz_mntly_stndrd_nft_via_01012012';
$viaProducts['BuzzMonthly']['price']=59.00;
$viaProducts['BuzzMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzQuarterly']['orderClassId']=3;
$viaProducts['BuzzQuarterly']['orderCodeId']=2;
$viaProducts['BuzzQuarterly']['typeSpecificId']=126;
$viaProducts['BuzzQuarterly']['plan_code']='buzz_qtrly_stdrd_nft_via_01012012';
$viaProducts['BuzzQuarterly']['price']=139.00;
$viaProducts['BuzzQuarterly']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzAnnual']['orderClassId']=3;
$viaProducts['BuzzAnnual']['orderCodeId']=2;
$viaProducts['BuzzAnnual']['typeSpecificId']=4;
$viaProducts['BuzzAnnual']['plan_code']='buzz_annl_stndrd_nft_via_01012012';
$viaProducts['BuzzAnnual']['price']=499.00;
$viaProducts['BuzzAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzFT3M-ST']['orderClassId']=3;
$viaProducts['BuzzFT3M-ST']['orderCodeId']=37;
$viaProducts['BuzzFT3M-ST']['typeSpecificId']=41;
$viaProducts['BuzzFT3M-ST']['price']=0;
$viaProducts['BuzzFT3M-ST']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzComplimentary']['orderClassId']=3;
$viaProducts['BuzzComplimentary']['orderCodeId']=12;
$viaProducts['BuzzComplimentary']['typeSpecificId']=16;
$viaProducts['BuzzComplimentary']['plan_code']='buzz_annl_comp_nft_via_01012012';
$viaProducts['BuzzComplimentary']['price']=0.00;
$viaProducts['BuzzComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzCK']['orderClassId']=3;
$viaProducts['BuzzCK']['orderCodeId']=20;
$viaProducts['BuzzCK']['typeSpecificId']=19;
$viaProducts['BuzzCK']['plan_code']='buzz_annl_stndrd_nft_via_01012012';
$viaProducts['BuzzCK']['price']=499.00;
$viaProducts['BuzzCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzST']['orderClassId']=3;
$viaProducts['BuzzST']['orderCodeId']=37;
$viaProducts['BuzzST']['typeSpecificId']=43;
$viaProducts['BuzzST']['price']=0.00;
$viaProducts['BuzzST']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzFT1M-ST']['orderClassId']=3;
$viaProducts['BuzzFT1M-ST']['orderCodeId']=37;
$viaProducts['BuzzFT1M-ST']['typeSpecificId']=49;
$viaProducts['BuzzFT1M-ST']['price']=0.00;
$viaProducts['BuzzFT1M-ST']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuzzFT1W-ST']['orderClassId']=3;
$viaProducts['BuzzFT1W-ST']['orderCodeId']=37;
$viaProducts['BuzzFT1W-ST']['typeSpecificId']=184;
$viaProducts['BuzzFT1W-ST']['price']=0.00;
$viaProducts['BuzzFT1W-ST']['orderItemType']='SUBSCRIPTION';

/******* Buzz Tool Config Start******/
$BANTER_URL="/banter/";
$BANTER_SIGNIN="/banter/singin.htm";
//keep banter part of regular subscription perms
$BANTER_ON=stristr($PHP_SELF,$BANTER_URL)||stristr($PHP_SELF, "/buzz/");
$BANTER_ALERT_FILE="$D_R/assets/data/banteralert.txt";
$BANTER_ALERT_MAX_AGE=(60*5);//5 minutes
$BANTER_LATEST_POST="$D_R/assets/data/latest_post.txt";
$BUZZLOGO_PATH = "assets/professorlogo/";
/******* Buzz Tool Config End******/

/*============ Buzz Ends ============================*/
/*============ Cooper Starts ============================*/

$viaProducts['CooperMonthlyTrial']['orderClassId']=4;
$viaProducts['CooperMonthlyTrial']['orderCodeId']=5;
$viaProducts['CooperMonthlyTrial']['typeSpecificId']=10;
$viaProducts['CooperMonthlyTrial']['price']=0.00;
$viaProducts['CooperMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperQuartTrial']['orderClassId']=4;
$viaProducts['CooperQuartTrial']['orderCodeId']=5;
$viaProducts['CooperQuartTrial']['typeSpecificId']=131;
$viaProducts['CooperQuartTrial']['price']=0.00;
$viaProducts['CooperQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperAnnualTrial']['orderClassId']=4;
$viaProducts['CooperAnnualTrial']['orderCodeId']=5;
$viaProducts['CooperAnnualTrial']['typeSpecificId']=12;
$viaProducts['CooperAnnualTrial']['price']=0.00;
$viaProducts['CooperAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperMonthly']['orderClassId']=4;
$viaProducts['CooperMonthly']['orderCodeId']=6;
$viaProducts['CooperMonthly']['typeSpecificId']=11;
$viaProducts['CooperMonthly']['plan_code']='cooper_mntly_stndrd_nft_via_01012012';
$viaProducts['CooperMonthly']['price']=99.00;
$viaProducts['CooperMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperQuarterly']['orderClassId']=4;
$viaProducts['CooperQuarterly']['orderCodeId']=6;
$viaProducts['CooperQuarterly']['typeSpecificId']=125;
$viaProducts['CooperQuarterly']['plan_code']='cooper_qrtly_stndrd_nft_via_01012012';
$viaProducts['CooperQuarterly']['price']=249.00;
$viaProducts['CooperQuarterly']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperAnnual']['orderClassId']=4;
$viaProducts['CooperAnnual']['orderCodeId']=6;
$viaProducts['CooperAnnual']['typeSpecificId']=13;
$viaProducts['CooperAnnual']['plan_code']='cooper_annl_stndrd_nft_via_01012012';
$viaProducts['CooperAnnual']['price']=799.00;
$viaProducts['CooperAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperComplimentary']['orderClassId']=4;
$viaProducts['CooperComplimentary']['orderCodeId']=13;
$viaProducts['CooperComplimentary']['typeSpecificId']=17;
$viaProducts['CooperComplimentary']['plan_code']='cooper_annl_comp_nft_via_01012012';
$viaProducts['CooperComplimentary']['price']=0;
$viaProducts['CooperComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperCK']['orderClassId']=4;
$viaProducts['CooperCK']['orderCodeId']=21;
$viaProducts['CooperCK']['typeSpecificId']=20;
$viaProducts['CooperCK']['plan_code']='cooper_annl_stndrd_nft_via_01012012';
$viaProducts['CooperCK']['price']=499.00;
$viaProducts['CooperCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperST']['orderClassId']=4;
$viaProducts['CooperST']['orderCodeId']=39;
$viaProducts['CooperST']['typeSpecificId']=44;
$viaProducts['CooperST']['price']=0.00;
$viaProducts['CooperST']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperFT1M-ST']['orderClassId']=4;
$viaProducts['CooperFT1M-ST']['orderCodeId']=39;
$viaProducts['CooperFT1M-ST']['typeSpecificId']=80;
$viaProducts['CooperFT1M-ST']['price']=0.00;
$viaProducts['CooperFT1M-ST']['orderItemType']='SUBSCRIPTION';

$viaProducts['CooperFT3M-ST']['orderClassId']=4;
$viaProducts['CooperFT3M-ST']['orderCodeId']=39;
$viaProducts['CooperFT3M-ST']['typeSpecificId']=81;
$viaProducts['CooperFT3M-ST']['price']=0.00;
$viaProducts['CooperFT3M-ST']['orderItemType']='SUBSCRIPTION';


/* Mail Id configurations for Cooper Start */
$NOTIFY_JOURNAL_FROM_CP="Cooper Alert <subscriptions@minyanville.com>";
$NOTIFY_JOURNAL_FROM_CP_NAME = "Cooper Alert";
$NOTIFY_JOURNAL_FROM_CP_EMAIL = "subscriptions@minyanville.com";
/* Mail Id configurations for Cooper End */

/*============ Cooper Ends ============================*/
/*============ Flexfolio Starts ============================*/

$viaProducts['FlexfolioMonthlyTrial']['orderClassId']=5;
$viaProducts['FlexfolioMonthlyTrial']['orderCodeId']=3;
$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId']=5;
$viaProducts['FlexfolioMonthlyTrial']['price']=0.00;
$viaProducts['FlexfolioMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioAnnualTrial']['orderClassId']=5;
$viaProducts['FlexfolioAnnualTrial']['orderCodeId']=3;
$viaProducts['FlexfolioAnnualTrial']['typeSpecificId']=8;
$viaProducts['FlexfolioAnnualTrial']['price']=0.00;
$viaProducts['FlexfolioAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioMonthly']['orderClassId']=5;
$viaProducts['FlexfolioMonthly']['orderCodeId']=4;
$viaProducts['FlexfolioMonthly']['typeSpecificId']=6;
$viaProducts['FlexfolioMonthly']['plan_code']='activeinvestr_mntly_stndrd_nft_via_04012011';
$viaProducts['FlexfolioMonthly']['price']=59.00;
$viaProducts['FlexfolioMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioAnnual']['orderClassId']=5;
$viaProducts['FlexfolioAnnual']['orderCodeId']=4;
$viaProducts['FlexfolioAnnual']['typeSpecificId']=9;
$viaProducts['FlexfolioAnnual']['plan_code']='activeinvestr_annl_stndrd_nft_via_04012011';
$viaProducts['FlexfolioAnnual']['price']=499.00;
$viaProducts['FlexfolioAnnual']['orderItemType']='SUBSCRIPTION';


$viaProducts['FlexfolioComplimentary']['orderClassId']=5;
$viaProducts['FlexfolioComplimentary']['orderCodeId']=14;
$viaProducts['FlexfolioComplimentary']['typeSpecificId']=18;
$viaProducts['FlexfolioComplimentary']['plan_code']='activeinvestr_annl_comp_nft_via_04012011';
$viaProducts['FlexfolioComplimentary']['price']=0;
$viaProducts['FlexfolioComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioCK']['orderClassId']=5;
$viaProducts['FlexfolioCK']['orderCodeId']=22;
$viaProducts['FlexfolioCK']['typeSpecificId']=21;
$viaProducts['FlexfolioCK']['plan_code']='activeinvestr_annl_stndrd_nft_via_04012011';
$viaProducts['FlexfolioCK']['price']=499.00;
$viaProducts['FlexfolioCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioST']['orderClassId']=5;
$viaProducts['FlexfolioST']['orderCodeId']=40;
$viaProducts['FlexfolioST']['typeSpecificId']=45;
$viaProducts['FlexfolioST']['price']=0.00;
$viaProducts['FlexfolioST']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioFT1M-ST']['orderClassId']=5;
$viaProducts['FlexfolioFT1M-ST']['orderCodeId']=40;
$viaProducts['FlexfolioFT1M-ST']['typeSpecificId']=85;
$viaProducts['FlexfolioFT1M-ST']['price']=0.00;
$viaProducts['FlexfolioFT1M-ST']['orderItemType']='SUBSCRIPTION';

$viaProducts['FlexfolioFT3M-ST']['orderClassId']=5;
$viaProducts['FlexfolioFT3M-ST']['orderCodeId']=40;
$viaProducts['FlexfolioFT3M-ST']['typeSpecificId']=86;
$viaProducts['FlexfolioFT3M-ST']['price']=0.00;
$viaProducts['FlexfolioFT3M-ST']['orderItemType']='SUBSCRIPTION';

/* Mail Id configurations for Flexfolio Start */
$NOTIFY_JOURNAL_FROM_QP="Active Investor Alert <subscriptions@minyanville.com>";
/* Mail Id configurations for Flexfolio End */
/* Portfolio Configuration Start*/
//$inceptionValue=106835.17;
//$inceptionValue=116032.41; // Vijay's change
$inceptionValue=93205.10;   // Afaque's change
$flexFolio_memcache_expire="1800";
/* Portfolio Configuration End */

/*============ Flexfolio Ends ============================*/
/*============ Option Smith Starts ============================*/

$viaProducts['OptionsmithMonthlyTrial']['orderClassId']=14;
$viaProducts['OptionsmithMonthlyTrial']['orderCodeId']=25;
$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId']=25;
$viaProducts['OptionsmithMonthlyTrial']['price']=0.00;
$viaProducts['OptionsmithMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithAnnualTrial']['orderClassId']=14;
$viaProducts['OptionsmithAnnualTrial']['orderCodeId']=25;
$viaProducts['OptionsmithAnnualTrial']['typeSpecificId']=24;
$viaProducts['OptionsmithAnnualTrial']['price']=0.00;
$viaProducts['OptionsmithAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithMonthly']['orderClassId']=14;
$viaProducts['OptionsmithMonthly']['orderCodeId']=23;
$viaProducts['OptionsmithMonthly']['typeSpecificId']=23;
$viaProducts['OptionsmithMonthly']['plan_code']='optsmth_mntly_stndrd_nft_via_01012012';
$viaProducts['OptionsmithMonthly']['price']=39.00;
$viaProducts['OptionsmithMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithAnnual']['orderClassId']=14;
$viaProducts['OptionsmithAnnual']['orderCodeId']=23;
$viaProducts['OptionsmithAnnual']['typeSpecificId']=22;
$viaProducts['OptionsmithAnnual']['plan_code']='optsmth_annl_stndrd_nft_via_01012012';
$viaProducts['OptionsmithAnnual']['price']=349.00;
$viaProducts['OptionsmithAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithComplimentary']['orderClassId']=14;
$viaProducts['OptionsmithComplimentary']['orderCodeId']=24;
$viaProducts['OptionsmithComplimentary']['typeSpecificId']=26;
$viaProducts['OptionsmithComplimentary']['plan_code']='optsmth_annl_comp_nft_via_01012012';
$viaProducts['OptionsmithComplimentary']['price']=0.00;
$viaProducts['OptionsmithComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithCK']['orderClassId']=26;
$viaProducts['OptionsmithCK']['orderCodeId']=24;
$viaProducts['OptionsmithCK']['typeSpecificId']=27;
$viaProducts['OptionsmithCK']['plan_code']='optsmth_annl_stndrd_nft_via_01012012';
$viaProducts['OptionsmithCK']['price']=349.00;
$viaProducts['OptionsmithCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithST']['orderClassId']=14;
$viaProducts['OptionsmithST']['orderCodeId']=42;
$viaProducts['OptionsmithST']['typeSpecificId']=47;
$viaProducts['OptionsmithST']['price']=0.00;
$viaProducts['OptionsmithST']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithFT1M-ST']['orderClassId']=14;
$viaProducts['OptionsmithFT1M-ST']['orderCodeId']=42;
$viaProducts['OptionsmithFT1M-ST']['typeSpecificId']=87;
$viaProducts['OptionsmithFT1M-ST']['price']=0.00;
$viaProducts['OptionsmithFT1M-ST']['orderItemType']='SUBSCRIPTION';

$viaProducts['OptionsmithFT3M-ST']['orderClassId']=14;
$viaProducts['OptionsmithFT3M-ST']['orderCodeId']=42;
$viaProducts['OptionsmithFT3M-ST']['typeSpecificId']=88;
$viaProducts['OptionsmithFT3M-ST']['price']=0.00;
$viaProducts['OptionsmithFT3M-ST']['orderItemType']='SUBSCRIPTION';


/* Mail  configurations for OptionSmith Start */
$NOTIFY_JOURNAL_FROM_SS="OptionSmith Alert <subscriptions@minyanville.com>";/* Mail Id configurations for Flexfolio End */
$NOTIFY_JOURNAL_FROM_SS_NAME="OptionSmith Alert";
$NOTIFY_JOURNAL_FROM_SS_EMAIL="subscriptions@minyanville.com";
$SPAM_EML_SINGLE_STEVE_ALERT_TMPL=$HTPFX.$HTADMINHOST."/emails/_eml_option_alert.htm";
/* Mail  configurations for OptionSmith End*/

/* Portfolio Configuration Start*/
$contractqty=100;
$transtypearray = array(0=>'Buy',1=>'Sell',2=>'SS',3=>'BTC');
$inceptionDateValue=704.56;
$optionSmith_memcache_expire="1800";
global $optionPortfolioInception2010;
// $optionPortfolioInception2010=108930.00;
//$optionPortfolioInception2010=151925.00; // Vijay's change
$optionPortfolioInception2010=108043.70; //2012 Current Value of OptionSmith
/* Portfolio Configuration End */



/*============ OptionSmith Ends ============================*/
/*============ BMTP Starts ============================*/

$viaProducts['BMTP']['orderClassId']=16;
$viaProducts['BMTP']['orderCodeId']=31;
$viaProducts['BMTP']['typeSpecificId']=14;
$viaProducts['BMTP']['price']=249.00;
$viaProducts['BMTP']['orderItemType']='PRODUCT';

$viaProducts['BMTPComplimentary']['orderClassId']=16;
$viaProducts['BMTPComplimentary']['orderCodeId']=36;
$viaProducts['BMTPComplimentary']['typeSpecificId']=15;
$viaProducts['BMTPComplimentary']['price']=0.00;
$viaProducts['BMTPComplimentary']['orderItemType']='PRODUCT';

$viaProducts['BMTPCK']['orderClassId']=16;
$viaProducts['BMTPCK']['orderCodeId']=38;
$viaProducts['BMTPCK']['typeSpecificId']=17;
$viaProducts['BMTPCK']['price']=249.00;
$viaProducts['BMTPCK']['orderItemType']='PRODUCT';

$viaProducts['BMTPAlert']['orderClassId']=15;
$viaProducts['BMTPAlert']['orderCodeId']=27;
$viaProducts['BMTPAlert']['typeSpecificId']=28;
$viaProducts['BMTPAlert']['price']=79.00;
$viaProducts['BMTPAlert']['orderItemType']='SUBSCRIPTION';

$viaProducts['BMTPAlertTrial']['orderClassId']=15;
$viaProducts['BMTPAlertTrial']['orderCodeId']=30;
$viaProducts['BMTPAlertTrial']['typeSpecificId']=33;
$viaProducts['BMTPAlertTrial']['price']=0.00;
$viaProducts['BMTPAlertTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BMTPAlertComplimentary']['orderClassId']=15;
$viaProducts['BMTPAlertComplimentary']['orderCodeId']=28;
$viaProducts['BMTPAlertComplimentary']['typeSpecificId']=29;
$viaProducts['BMTPAlertComplimentary']['price']=0.00;
$viaProducts['BMTPAlertComplimentary']['orderItemType']='SUBSCRIPTION';


$viaProducts['BMTPAlertCK']['orderClassId']=15;
$viaProducts['BMTPAlertCK']['orderCodeId']=29;
$viaProducts['BMTPAlertCK']['typeSpecificId']=30;
$viaProducts['BMTPAlertCK']['price']=79.00;
$viaProducts['BMTPAlertCK']['orderItemType']='SUBSCRIPTION';

/*BMTP Email Configuration Start*/
$NOTIFY_JOURNAL_FROM_BMTP="Bull Market Timer Update <subscriptions@minyanville.com>";
$SPAM_EML_BMTP_ALERT_TMPL=$HTPFX.$HTHOST."/emails/_eml_bmtp_alert.htm";
/*BMTP Email Configuration End*/

/*============ BMTP Ends ============================*/
/*============ Lavery Insight Starts ============================*/

$viaProducts['JackMonthlyTrial']['orderClassId']=17;
$viaProducts['JackMonthlyTrial']['orderCodeId']=33;
$viaProducts['JackMonthlyTrial']['typeSpecificId']=36;
$viaProducts['JackMonthlyTrial']['price']=0.00;
$viaProducts['JackMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackAnnualTrial']['orderClassId']=17;
$viaProducts['JackAnnualTrial']['orderCodeId']=33;
$viaProducts['JackAnnualTrial']['typeSpecificId']=37;
$viaProducts['JackAnnualTrial']['price']=0.00;
$viaProducts['JackAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackMonthly']['orderClassId']=17;
$viaProducts['JackMonthly']['orderCodeId']=32;
$viaProducts['JackMonthly']['typeSpecificId']=34;
$viaProducts['JackMonthly']['price']=29.00;
$viaProducts['JackMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackAnnual']['orderClassId']=17;
$viaProducts['JackAnnual']['orderCodeId']=32;
$viaProducts['JackAnnual']['typeSpecificId']=35;
$viaProducts['JackAnnual']['price']=259.00;
$viaProducts['JackAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackComplimentary']['orderClassId']=17;
$viaProducts['JackComplimentary']['orderCodeId']=34;
$viaProducts['JackComplimentary']['typeSpecificId']=38;
$viaProducts['JackComplimentary']['price']=0.00;
$viaProducts['JackComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackCK']['orderClassId']=17;
$viaProducts['JackCK']['orderCodeId']=35;
$viaProducts['JackCK']['typeSpecificId']=39;
$viaProducts['JackCK']['price']=259.00;
$viaProducts['JackCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['JackST']['orderClassId']=17;
$viaProducts['JackST']['orderCodeId']=41;
$viaProducts['JackST']['typeSpecificId']=46;
$viaProducts['JackST']['price']=0.00;
$viaProducts['JackST']['orderItemType']='SUBSCRIPTION';

/* EMail configurations for Lavery Insight Start */
$contributorsArray['jack']=167;
$NOTIFY_JOURNAL_FROM_JACK="The Lavery Insight <subscriptions@minyanville.com>";
$SPAM_EML_JACK_ALERT_TMPL=$HTPFX.$HTHOST."/emails/_eml_jack_alert.htm";
$SPAM_EML_JACK_MULTIPLE_ARTICLE_TMPL="$HTDOMAIN/emails/_eml_jack_alert.htm";
/*============ EMail configurations for Lavery Insight End ============================*/


/*============ Lavery Insight Ends ============================*/


/*============ Combos with Buzz Starts ============================*/


$viaProducts['CooperCombo']['orderClassId']=11;
$viaProducts['CooperCombo']['orderCodeId']=11;
$viaProducts['CooperCombo']['typeSpecificId']=2;
$viaProducts['CooperCombo']['price']=999.00;
$viaProducts['CooperCombo']['orderItemType']='BASIC_PACKAGE';

$viaProducts['FlexfolioCombo']['orderClassId']=10;
$viaProducts['FlexfolioCombo']['orderCodeId']=10;
$viaProducts['FlexfolioCombo']['typeSpecificId']=1;
$viaProducts['FlexfolioCombo']['price']=799.00;
$viaProducts['FlexfolioCombo']['orderItemType']='BASIC_PACKAGE';
/*============ Combos with Buzz Ends ============================*/


/*============ ETF Starts ============================*/

$viaProducts['ETFMonthlyTrial']['orderClassId']=19;
$viaProducts['ETFMonthlyTrial']['orderCodeId']=44;
$viaProducts['ETFMonthlyTrial']['typeSpecificId']=51;
$viaProducts['ETFMonthlyTrial']['price']=0.00;
$viaProducts['ETFMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFQuartTrial']['orderClassId']=19;
$viaProducts['ETFQuartTrial']['orderCodeId']=44;
$viaProducts['ETFQuartTrial']['typeSpecificId']=61;
$viaProducts['ETFQuartTrial']['price']=0.00;
$viaProducts['ETFQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFAnnualTrial']['orderClassId']=19;
$viaProducts['ETFAnnualTrial']['orderCodeId']=44;
$viaProducts['ETFAnnualTrial']['typeSpecificId']=60;
$viaProducts['ETFAnnualTrial']['price']=0.00;
$viaProducts['ETFAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFMonthly']['orderClassId']=19;
$viaProducts['ETFMonthly']['orderCodeId']=43;
$viaProducts['ETFMonthly']['typeSpecificId']=50;
$viaProducts['ETFMonthly']['plan_code']='tchstrt_mntly_stndrd_nft_via_01012012';
$viaProducts['ETFMonthly']['price']=79.00;
$viaProducts['ETFMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFQuart']['orderClassId']=19;
$viaProducts['ETFQuart']['orderCodeId']=43;
$viaProducts['ETFQuart']['typeSpecificId']=58;
$viaProducts['ETFQuart']['plan_code']='tchstrt_qrtly_stndrd_nft_via_01012012';
$viaProducts['ETFQuart']['price']=199.00;
$viaProducts['ETFQuart']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFAnnual']['orderClassId']=19;
$viaProducts['ETFAnnual']['orderCodeId']=43;
$viaProducts['ETFAnnual']['typeSpecificId']=56;
$viaProducts['ETFAnnual']['plan_code']='tchstrt_annl_stndrd_nft_via_01012012';
$viaProducts['ETFAnnual']['price']=699.00;
$viaProducts['ETFAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFComplimentary']['orderClassId']=19;
$viaProducts['ETFComplimentary']['orderCodeId']=47;
$viaProducts['ETFComplimentary']['typeSpecificId']=54;
$viaProducts['ETFComplimentary']['plan_code']='tchstrt_annl_comp_nft_via_01012012';
$viaProducts['ETFComplimentary']['price']=0;
$viaProducts['ETFComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFCK']['orderClassId']=19;
$viaProducts['ETFCK']['orderCodeId']=48;
$viaProducts['ETFCK']['typeSpecificId']=55;
$viaProducts['ETFCK']['price']=499.00;
$viaProducts['ETFCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFST']['orderClassId']=19;
$viaProducts['ETFST']['orderCodeId']=46;
$viaProducts['ETFST']['typeSpecificId']=53;
$viaProducts['ETFST']['price']=0.00;
$viaProducts['ETFST']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFST1M']['orderClassId']=19;
$viaProducts['ETFST1M']['orderCodeId']=46;
$viaProducts['ETFST1M']['typeSpecificId']=66;
$viaProducts['ETFST1M']['price']=0.00;
$viaProducts['ETFST1M']['orderItemType']='SUBSCRIPTION';

$viaProducts['ETFST3M']['orderClassId']=19;
$viaProducts['ETFST3M']['orderCodeId']=46;
$viaProducts['ETFST3M']['typeSpecificId']=84;
$viaProducts['ETFST3M']['price']=0.00;
$viaProducts['ETFST3M']['orderItemType']='SUBSCRIPTION';


$contributorsArray['etf']=202;

/*email configuration for ETF*/
$ETF_FROM="Grail ETF and Equity Investor <subscriptions@minyanville.com>";
$ETF_ALERT_TMPL=$HTPFX.$HTHOST."/emails/_eml_etf_alert.htm";

/*Array for direct url regitration for campaing*/

/*============ The Stockplaybook Starts ============================*/

$viaProducts['TheStockPlaybookMonthlyTrial']['orderClassId']=20;
$viaProducts['TheStockPlaybookMonthlyTrial']['orderCodeId']=49;
$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId']=73;
$viaProducts['TheStockPlaybookMonthlyTrial']['price']=0.00;
$viaProducts['TheStockPlaybookMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookQuartTrial']['orderClassId']=20;
$viaProducts['TheStockPlaybookQuartTrial']['orderCodeId']=49;
$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId']=72;
$viaProducts['TheStockPlaybookQuartTrial']['price']=0.00;
$viaProducts['TheStockPlaybookQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookAnnualTrial']['orderClassId']=20;
$viaProducts['TheStockPlaybookAnnualTrial']['orderCodeId']=49;
$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId']=71;
$viaProducts['TheStockPlaybookAnnualTrial']['price']=0.00;
$viaProducts['TheStockPlaybookAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookMonthly']['orderClassId']=20;
$viaProducts['TheStockPlaybookMonthly']['orderCodeId']=52;
$viaProducts['TheStockPlaybookMonthly']['typeSpecificId']=78;
$viaProducts['TheStockPlaybookMonthly']['plan_code']='tsp_mntly_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookMonthly']['price']=99.00;
$viaProducts['TheStockPlaybookMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookQuart']['orderClassId']=20;
$viaProducts['TheStockPlaybookQuart']['orderCodeId']=52;
$viaProducts['TheStockPlaybookQuart']['typeSpecificId']=77;
$viaProducts['TheStockPlaybookQuart']['plan_code']='tsp_qtrly_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookQuart']['price']=249.00;
$viaProducts['TheStockPlaybookQuart']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookAnnual']['orderClassId']=20;
$viaProducts['TheStockPlaybookAnnual']['orderCodeId']=52;
$viaProducts['TheStockPlaybookAnnual']['typeSpecificId']=76;
$viaProducts['TheStockPlaybookAnnual']['plan_code']='tsp_annl_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookAnnual']['price']=899.00;
$viaProducts['TheStockPlaybookAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookComplimentary']['orderClassId']=20;
$viaProducts['TheStockPlaybookComplimentary']['orderCodeId']=51;
$viaProducts['TheStockPlaybookComplimentary']['typeSpecificId']=75;
$viaProducts['TheStockPlaybookComplimentary']['plan_code']='tsp_annl_comp_nft_via_01012012';
$viaProducts['TheStockPlaybookComplimentary']['price']=0;
$viaProducts['TheStockPlaybookComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookCK']['orderClassId']=20;
$viaProducts['TheStockPlaybookCK']['orderCodeId']=53;
$viaProducts['TheStockPlaybookCK']['typeSpecificId']=79;
$viaProducts['TheStockPlaybookCK']['price']=499.00;
$viaProducts['TheStockPlaybookCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookST']['orderClassId']=20;
$viaProducts['TheStockPlaybookST']['orderCodeId']=50;
$viaProducts['TheStockPlaybookST']['typeSpecificId']=70;
$viaProducts['TheStockPlaybookST']['price']=0.00;
$viaProducts['TheStockPlaybookST']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybook1MST']['orderClassId']=20;
$viaProducts['TheStockPlaybook1MST']['orderCodeId']=50;
$viaProducts['TheStockPlaybook1MST']['typeSpecificId']=82;
$viaProducts['TheStockPlaybook1MST']['price']=0.00;
$viaProducts['TheStockPlaybook1MST']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybook3MST']['orderClassId']=20;
$viaProducts['TheStockPlaybook3MST']['orderCodeId']=50;
$viaProducts['TheStockPlaybook3MST']['typeSpecificId']=83;
$viaProducts['TheStockPlaybook3MST']['price']=0.00;
$viaProducts['TheStockPlaybook3MST']['orderItemType']='SUBSCRIPTION';


/*email configuration for The Stock Playbook*/
$THESTOCKPLAYBOOK_FROM="The Stock Playbook <subscriptions@minyanville.com>";
$THESTOCKPLAYBOOK_FROM_NAME = "The Stock Playbook";
$THESTOCKPLAYBOOK_FROM_EMAIL = "subscriptions@minyanville.com";
$THESTOCKPLAYBOOK_ALERT_TMPL=$HTPFX.$HTHOST."/emails/_eml_thestockplaybook_alert.htm";

/*============ The Stockplaybook Ends ============================*/

/*============ The AdFree Starts ============================*/

$viaProducts['AdFreeMonthly']['orderClassId']=21;
$viaProducts['AdFreeMonthly']['orderCodeId']=54;
$viaProducts['AdFreeMonthly']['typeSpecificId']=89;
$viaProducts['AdFreeMonthly']['plan_code']='adsfree_mntly_stndrd_nft_via_01012012';
$viaProducts['AdFreeMonthly']['price']=8.00;
$viaProducts['AdFreeMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['AdFreeComplimentary']['orderClassId']=21;
$viaProducts['AdFreeComplimentary']['orderCodeId']=55;
$viaProducts['AdFreeComplimentary']['typeSpecificId']=90;
$viaProducts['AdFreeComplimentary']['plan_code']='adsfree_annl_comp_nft_via_01012012';
$viaProducts['AdFreeComplimentary']['price']=0;
$viaProducts['AdFreeComplimentary']['orderItemType']='SUBSCRIPTION';

/*============ The AdFree Ends ============================*/


/*Array for direct url regitration for campaing*/


$productdef=array();
$productdef[]="2"; // buzz monthly
$productdef[]="4"; //buzz annual
$productdef[]="22"; //OptionSmith annual
$productdef[]="89"; //AdFree monthly
$productdef[]="23"; //OptionSmith trial
$productdef[]="6"; //Flexfolio trial
$productdef[]="50"; //etf trial
$productdef[]="78"; //stockPlayBook trial
$productdef[]="11"; //Cooper trial
$productdef[]="109"; //TechStrat trial
/**** Array to check paid product ****/
$productAd=array();
$productAd[]="buzz_annl_stndrd_nft_via_01012012"; // buzz annual
$productAd[]="cooper_annl_stndrd_nft_via_01012012"; //cooper annual
$productAd[]="activeinvestr_annl_stndrd_nft_via_04012011"; //flexfolio annual
$productAd[]="optsmth_annl_stndrd_nft_via_01012012"; //optionSmith annual
$productAd[]="grailetf_annl_stndrd_nft_via_01012012"; //ETF annual
$productAd[]="tsp_annl_stndrd_nft_via_01012012"; //theStockPlayBook annual
$productAd[]="tchstrt_annl_stndrd_nft_via_01012012"; //TechStrat Annual
$productAd[]="hmr_annl_stndrd_nft_via_01012012"; //Housing Report Annual
$productAd[]="buyhdg_annl_stndrd_nft_via_01012012"; //BUY&Hedge Annual

$productAd[]="buzz_annl_stndrd_ft_via_01012012"; // buzz annual trial
$productAd[]="cooper_annl_stndrd_ft_via_01012012"; //cooper annual trial
$productAd[]="activeinvestr_annl_stndrd_ft_via_04012011"; //flexfolio annual trial
$productAd[]="optsmth_annl_stndrd_nft_via_01012012"; //optionSmith annual trial
$productAd[]="grailetf_annl_stndrd_nft_via_01012012"; //ETF annual trial
$productAd[]="tsp_annl_stndrd_ft_via_01012012"; //theStockPlayBook annual trial
$productAd[]="tchstrt_annl_stndrd_ft_via_0102012"; //TechStrat annual trial
$productAd[]="buyhdg_annl_stndrd_ft_via_01012012"; //Buy&Hedge annual trial

$productAd[]="buzz_annl_stndrd_nft_via_01012012"; // buzz  CK
$productAd[]="cooper_annl_stndrd_nft_via_01012012"; //cooper  CK
$productAd[]="activeinvestr_annl_stndrd_nft_via_04012011"; //flexfolio  CK
$productAd[]="optsmth_annl_stndrd_nft_via_01012012"; //optionSmith  CK
$productAd[]="grailetf_annl_stndrd_nft_via_01012012"; //ETF  CK
$productAd[]="tsp_annl_stndrd_nft_via_01012012"; //theStockPlayBook  CK
$productAd[]="buyhdg_annl_stndrd_nft_via_01012012"; //Buy Hedge  CK

$productAd[]="4"; // buzz annual
$productAd[]="13"; //cooper annual
$productAd[]="9"; //flexfolio annual
$productAd[]="22"; //optionSmith annual
$productAd[]="56"; //ETF annual
$productAd[]="76"; //theStockPlayBook annual
$productAd[]="108"; //TechStrat Annual
$productAd[]="117"; //Housing Report Annual
$productAd[]="99"; //BUY&Hedge Annual

$productAd[]="3"; // buzz annual trial
$productAd[]="12"; //cooper annual trial
$productAd[]="8"; //flexfolio annual trial
$productAd[]="24"; //optionSmith annual trial
$productAd[]="60"; //ETF annual trial
$productAd[]="71"; //theStockPlayBook annual trial
$productAd[]="94"; //TechStrat annual trial
$productAd[]="91"; //Buy&Hedge annual trial

$productAd[]="19"; // buzz  CK
$productAd[]="20"; //cooper  CK
$productAd[]="22"; //flexfolio  CK
$productAd[]="27"; //optionSmith  CK
$productAd[]="55"; //ETF  CK
$productAd[]="79"; //theStockPlayBook  CK
$productAd[]="97"; //Buy Hedge  CK
/*****Email Configuration for TechStrat***********/
$contributorsArray['tech-strat']=98;


/*=============TechStrat Configuration Start===============*/
$viaProducts['TechStratMonthlyTrial']['orderClassId']=23;
$viaProducts['TechStratMonthlyTrial']['orderCodeId']=64;
$viaProducts['TechStratMonthlyTrial']['typeSpecificId']=103;
$viaProducts['TechStratMonthlyTrial']['price']=0.00;
$viaProducts['TechStratMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratQuarterTrial']['orderClassId']=23;
$viaProducts['TechStratQuarterTrial']['orderCodeId']=64;
$viaProducts['TechStratQuarterTrial']['typeSpecificId']=106;
$viaProducts['TechStratQuarterTrial']['price']=0.00;
$viaProducts['TechStratQuarterTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratAnnualTrial']['orderClassId']=23;
$viaProducts['TechStratAnnualTrial']['orderCodeId']=64;
$viaProducts['TechStratAnnualTrial']['typeSpecificId']=94;
$viaProducts['TechStratAnnualTrial']['price']=0.00;
$viaProducts['TechStratAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratMonthly']['orderClassId']=23;
$viaProducts['TechStratMonthly']['orderCodeId']=61;
$viaProducts['TechStratMonthly']['typeSpecificId']=109;
$viaProducts['TechStratMonthly']['plan_code']='tchstrt_mntly_stndrd_nft_via_01012012';
$viaProducts['TechStratMonthly']['price']=89.00;
$viaProducts['TechStratMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratQuarterly']['orderClassId']=23;
$viaProducts['TechStratQuarterly']['orderCodeId']=61;
$viaProducts['TechStratQuarterly']['typeSpecificId']=110;
$viaProducts['TechStratQuarterly']['plan_code']='tchstrt_qrtly_stndrd_nft_via_01012012';
$viaProducts['TechStratQuarterly']['price']=229.00;
$viaProducts['TechStratQuarterly']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratAnnual']['orderClassId']=23;
$viaProducts['TechStratAnnual']['orderCodeId']=61;
$viaProducts['TechStratAnnual']['typeSpecificId']=108;
$viaProducts['TechStratAnnual']['plan_code']='tchstrt_annl_stndrd_nft_via_01012012';
$viaProducts['TechStratAnnual']['price']=749.00;
$viaProducts['TechStratAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratComplimentary']['orderClassId']=23;
$viaProducts['TechStratComplimentary']['orderCodeId']=65;
$viaProducts['TechStratComplimentary']['typeSpecificId']=113;
$viaProducts['TechStratComplimentary']['plan_code']='tchstrt_annl_comp_nft_via_01012012';
$viaProducts['TechStratComplimentary']['price']=0.00;
$viaProducts['TechStratComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratST']['orderClassId']=23;
$viaProducts['TechStratST']['orderCodeId']=63;
$viaProducts['TechStratST']['typeSpecificId']=107;
$viaProducts['TechStratST']['price']=0.00;
$viaProducts['TechStratST']['orderItemType']='SUBSCRIPTION';

$viaProducts['TechStratCK']['orderClassId']=23;
$viaProducts['TechStratCK']['orderCodeId']=62;
$viaProducts['TechStratCK']['typeSpecificId']=111;
$viaProducts['TechStratCK']['price']=749.00;
$viaProducts['TechStratCK']['orderItemType']='SUBSCRIPTION';

/*=============TechStrat Configuration End=================*/


/*****Email Configuration for Garyk***********/
$contributorsArray['garyk']=414;


/*=============TechStrat Configuration Start===============*/
$viaProducts['GaryKMonthlyTrial']['orderClassId']=28;
$viaProducts['GaryKMonthlyTrial']['orderCodeId']=73;
$viaProducts['GaryKMonthlyTrial']['typeSpecificId']=138;
$viaProducts['GaryKMonthlyTrial']['price']=0.00;
$viaProducts['GaryKMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKQuarterTrial']['orderClassId']=28;
$viaProducts['GaryKQuarterTrial']['orderCodeId']=73;
$viaProducts['GaryKQuarterTrial']['typeSpecificId']=139;
$viaProducts['GaryKQuarterTrial']['price']=0.00;
$viaProducts['GaryKQuarterTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKAnnualTrial']['orderClassId']=28;
$viaProducts['GaryKAnnualTrial']['orderCodeId']=73;
$viaProducts['GaryKAnnualTrial']['typeSpecificId']=137;
$viaProducts['GaryKAnnualTrial']['price']=0.00;
$viaProducts['GaryKAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKMonthly']['orderClassId']=28;
$viaProducts['GaryKMonthly']['orderCodeId']=76;
$viaProducts['GaryKMonthly']['typeSpecificId']=142;
$viaProducts['GaryKMonthly']['plan_code']='garyk_mntly_stndrd_nft_via_010102012';
$viaProducts['GaryKMonthly']['price']=129.00;
$viaProducts['GaryKMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKQuarterly']['orderClassId']=28;
$viaProducts['GaryKQuarterly']['orderCodeId']=75;
$viaProducts['GaryKQuarterly']['typeSpecificId']=143;
$viaProducts['GaryKQuarterly']['plan_code']='garyk_qrtly_stndrd_nft_via_01012012';
$viaProducts['GaryKQuarterly']['price']=339.00;
$viaProducts['GaryKQuarterly']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKAnnual']['orderClassId']=28;
$viaProducts['GaryKAnnual']['orderCodeId']=74;
$viaProducts['GaryKAnnual']['typeSpecificId']=141;
$viaProducts['GaryKAnnual']['plan_code']='garyk_annl_stndrd_nft_via_01012012';
$viaProducts['GaryKAnnual']['price']=999.00;
$viaProducts['GaryKAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKComplimentary']['orderClassId']=28;
$viaProducts['GaryKComplimentary']['orderCodeId']=77;
$viaProducts['GaryKComplimentary']['typeSpecificId']=146;
$viaProducts['GaryKComplimentary']['plan_code']='garyk_annl_comp_nft_via_0102012';
$viaProducts['GaryKComplimentary']['price']=0.00;
$viaProducts['GaryKComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKST']['orderClassId']=140;
$viaProducts['GaryKST']['orderCodeId']=78;
$viaProducts['GaryKST']['typeSpecificId']=140;
$viaProducts['GaryKST']['price']=0.00;
$viaProducts['GaryKST']['orderItemType']='SUBSCRIPTION';

$viaProducts['GaryKCK']['orderClassId']=28;
$viaProducts['GaryKCK']['orderCodeId']=79;
$viaProducts['GaryKCK']['typeSpecificId']=144;
$viaProducts['GaryKCK']['price']=999.00;
$viaProducts['GaryKCK']['orderItemType']='SUBSCRIPTION';

/*=============Garyk Configuration End=================*/


/* ------------Housing Report ------------- */
$viaProducts['Housing3Months']['orderClassId']=25;
$viaProducts['Housing3Months']['orderCodeId']=67;
$viaProducts['Housing3Months']['typeSpecificId']=115;
$viaProducts['Housing3Months']['plan_code']='hmr_qrtly_stndrd_nft_via_01012012';
$viaProducts['Housing3Months']['price']=79.00;
$viaProducts['Housing3Months']['orderItemType']='SUBSCRIPTION';

$viaProducts['Housing6Months']['orderClassId']=25;
$viaProducts['Housing6Months']['orderCodeId']=67;
$viaProducts['Housing6Months']['typeSpecificId']=116;
$viaProducts['Housing6Months']['plan_code']='hmr_6months_stndrd_nft_via_01012012';
$viaProducts['Housing6Months']['price']=129.00;
$viaProducts['Housing6Months']['orderItemType']='SUBSCRIPTION';

$viaProducts['HousingAnnual']['orderClassId']=25;
$viaProducts['HousingAnnual']['orderCodeId']=67;
$viaProducts['HousingAnnual']['typeSpecificId']=117;
$viaProducts['HousingAnnual']['plan_code']='hmr_annl_stndrd_nft_via_01012012';
$viaProducts['HousingAnnual']['price']=199.00;
$viaProducts['HousingAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['HousingComplimentary']['orderClassId']=25;
$viaProducts['HousingComplimentary']['orderCodeId']=71;
$viaProducts['HousingComplimentary']['typeSpecificId']=134;
$viaProducts['HousingComplimentary']['plan_code']='hmr_annl_comp_nft_via_01012012';
$viaProducts['HousingComplimentary']['price']=0.00;
$viaProducts['HousingComplimentary']['orderItemType']='SUBSCRIPTION';

/* ------------Housing Report End ------------- */

/* ------------Housing Market Single Issue Start----------- */
$viaProducts['HousingSingle']['orderClassId']=26;

$viaProducts['LasVegas']['orderClassId']=26;
$viaProducts['LasVegas']['orderCodeId']=69;
$viaProducts['LasVegas']['typeSpecificId']=118;
$viaProducts['LasVegas']['plan_code']='hmrlasvegas_1rprt_stndrd_nft_via_01012012';
$viaProducts['LasVegas']['price']=99.00;
$viaProducts['LasVegas']['orderItemType']='SUBSCRIPTION';

$viaProducts['Miami']['orderClassId']=26;
$viaProducts['Miami']['orderCodeId']=69;
$viaProducts['Miami']['typeSpecificId']=119;
$viaProducts['Miami']['plan_code']='hmrmiami_1rprt_stndrd_nft_via_01012012';
$viaProducts['Miami']['price']=99.00;
$viaProducts['Miami']['orderItemType']='SUBSCRIPTION';

$viaProducts['Chicago']['orderClassId']=26;
$viaProducts['Chicago']['orderCodeId']=69;
$viaProducts['Chicago']['typeSpecificId']=120;
$viaProducts['Chicago']['plan_code']='hmrchicago_1rprt_stndrd_nft_via_01012012';
$viaProducts['Chicago']['price']=99.00;
$viaProducts['Chicago']['orderItemType']='SUBSCRIPTION';

$viaProducts['Phoenix']['orderClassId']=26;
$viaProducts['Phoenix']['orderCodeId']=69;
$viaProducts['Phoenix']['typeSpecificId']=121;
$viaProducts['Phoenix']['plan_code']='hmrphnx_1rprt_stndrd_nft_via_01012012';
$viaProducts['Phoenix']['price']=99.00;
$viaProducts['Phoenix']['orderItemType']='SUBSCRIPTION';

$viaProducts['WashingtonDC']['orderClassId']=26;
$viaProducts['WashingtonDC']['orderCodeId']=69;
$viaProducts['WashingtonDC']['typeSpecificId']=122;
$viaProducts['WashingtonDC']['plan_code']='hmrwdc_1rprt_stndrd_nft_via_01012012';
$viaProducts['WashingtonDC']['price']=99.00;
$viaProducts['WashingtonDC']['orderItemType']='SUBSCRIPTION';

$viaProducts['SanDiego']['orderClassId']=26;
$viaProducts['SanDiego']['orderCodeId']=69;
$viaProducts['SanDiego']['typeSpecificId']=123;
$viaProducts['SanDiego']['price']=99.00;
$viaProducts['SanDiego']['orderItemType']='SUBSCRIPTION';

$viaProducts['NewYorkMetro']['orderClassId']=26;
$viaProducts['NewYorkMetro']['orderCodeId']=69;
$viaProducts['NewYorkMetro']['typeSpecificId']=124;
$viaProducts['NewYorkMetro']['plan_code']='hmrnyc_1rprt_stndrd_nft_via_01012012';
$viaProducts['NewYorkMetro']['price']=99.00;
$viaProducts['NewYorkMetro']['orderItemType']='SUBSCRIPTION';

$viaProducts['Atlanta']['orderClassId']=26;
$viaProducts['Atlanta']['orderCodeId']=69;
$viaProducts['Atlanta']['typeSpecificId']=155;
$viaProducts['Atlanta']['price']=99.00;
$viaProducts['Atlanta']['orderItemType']='SUBSCRIPTION';

$viaProducts['AtlantaComp']['orderClassId']=26;
$viaProducts['AtlantaComp']['orderCodeId']=69;
$viaProducts['AtlantaComp']['typeSpecificId']=156;
$viaProducts['AtlantaComp']['price']=0.00;
$viaProducts['AtlantaComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['Dallas']['orderClassId']=26;
$viaProducts['Dallas']['orderCodeId']=69;
$viaProducts['Dallas']['typeSpecificId']=158;
$viaProducts['Dallas']['price']=99.00;
$viaProducts['Dallas']['orderItemType']='SUBSCRIPTION';

$viaProducts['DallasComp']['orderClassId']=26;
$viaProducts['DallasComp']['orderCodeId']=69;
$viaProducts['DallasComp']['typeSpecificId']=162;
$viaProducts['DallasComp']['price']=0.00;
$viaProducts['DallasComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['LosAngles']['orderClassId']=26;
$viaProducts['LosAngles']['orderCodeId']=69;
$viaProducts['LosAngles']['typeSpecificId']=160;
$viaProducts['LosAngles']['price']=99.00;
$viaProducts['LosAngles']['orderItemType']='SUBSCRIPTION';

$viaProducts['LosAnglesComp']['orderClassId']=26;
$viaProducts['LosAnglesComp']['orderCodeId']=69;
$viaProducts['LosAnglesComp']['typeSpecificId']=161;
$viaProducts['LosAnglesComp']['price']=0.00;
$viaProducts['LosAnglesComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['Minneapolis']['orderClassId']=26;
$viaProducts['Minneapolis']['orderCodeId']=69;
$viaProducts['Minneapolis']['typeSpecificId']=163;
$viaProducts['Minneapolis']['price']=99.00;
$viaProducts['Minneapolis']['orderItemType']='SUBSCRIPTION';

$viaProducts['MinneapolisComp']['orderClassId']=26;
$viaProducts['MinneapolisComp']['orderCodeId']=69;
$viaProducts['MinneapolisComp']['typeSpecificId']=165;
$viaProducts['MinneapolisComp']['price']=0.00;
$viaProducts['MinneapolisComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['Portland']['orderClassId']=26;
$viaProducts['Portland']['orderCodeId']=69;
$viaProducts['Portland']['typeSpecificId']=168;
$viaProducts['Portland']['price']=99.00;
$viaProducts['Portland']['orderItemType']='SUBSCRIPTION';

$viaProducts['PortlandComp']['orderClassId']=26;
$viaProducts['PortlandComp']['orderCodeId']=69;
$viaProducts['PortlandComp']['typeSpecificId']=169;
$viaProducts['PortlandComp']['price']=0.00;
$viaProducts['PortlandComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['Orlendo']['orderClassId']=26;
$viaProducts['Orlendo']['orderCodeId']=69;
$viaProducts['Orlendo']['typeSpecificId']=170;
$viaProducts['Orlendo']['price']=99.00;
$viaProducts['Orlendo']['orderItemType']='SUBSCRIPTION';

$viaProducts['OrlendoComp']['orderClassId']=26;
$viaProducts['OrlendoComp']['orderCodeId']=69;
$viaProducts['OrlendoComp']['typeSpecificId']=171;
$viaProducts['OrlendoComp']['price']=0.00;
$viaProducts['OrlendoComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['Seattle']['orderClassId']=26;
$viaProducts['Seattle']['orderCodeId']=69;
$viaProducts['Seattle']['typeSpecificId']=172;
$viaProducts['Seattle']['price']=99.00;
$viaProducts['Seattle']['orderItemType']='SUBSCRIPTION';

$viaProducts['SeattleComp']['orderClassId']=26;
$viaProducts['SeattleComp']['orderCodeId']=69;
$viaProducts['SeattleComp']['typeSpecificId']=173;
$viaProducts['SeattleComp']['price']=0.00;
$viaProducts['SeattleComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['SanFrancisco']['orderClassId']=26;
$viaProducts['SanFrancisco']['orderCodeId']=69;
$viaProducts['SanFrancisco']['typeSpecificId']=176;
$viaProducts['SanFrancisco']['price']=99.00;
$viaProducts['SanFrancisco']['orderItemType']='SUBSCRIPTION';

$viaProducts['SanFranciscoComp']['orderClassId']=26;
$viaProducts['SanFranciscoComp']['orderCodeId']=69;
$viaProducts['SanFranciscoComp']['typeSpecificId']=177;
$viaProducts['SanFranciscoComp']['price']=0.00;
$viaProducts['SanFranciscoComp']['orderItemType']='SUBSCRIPTION';

$viaProducts['LongIsland']['orderClassId']=26;
$viaProducts['LongIsland']['orderCodeId']=69;
$viaProducts['LongIsland']['typeSpecificId']=188;
$viaProducts['LongIsland']['plan_code']='hmrlongisd_1rprt_stndrd_nft_via_01012012';
$viaProducts['LongIsland']['price']=99.00;
$viaProducts['LongIsland']['orderItemType']='SUBSCRIPTION';

/* ------------Housing Market Single Issue End----------- */

/*TheStockPlayBook Premium Start*/
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderCodeId']=85;
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId']=178;
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['price']=0.00;
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookPremiumQuartTrial']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumQuartTrial']['orderCodeId']=85;
$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId']=179;
$viaProducts['TheStockPlaybookPremiumQuartTrial']['price']=0.00;
$viaProducts['TheStockPlaybookPremiumQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderCodeId']=85;
$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId']=180;
$viaProducts['TheStockPlaybookPremiumAnnualTrial']['price']=0.00;
$viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderItemType']='SUBSCRIPTION';


$viaProducts['TheStockPlaybookPremiumMonthly']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumMonthly']['orderCodeId']=70;
$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId']=127;
$viaProducts['TheStockPlaybookPremiumMonthly']['plan_code']='tsp_mntly_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookPremiumMonthly']['price']=199.00;
$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookPremiumQuart']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumQuart']['orderCodeId']=70;
$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId']=128;
$viaProducts['TheStockPlaybookPremiumQuart']['plan_code']='tsp_qtrly_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookPremiumQuart']['price']=499.00;
$viaProducts['TheStockPlaybookPremiumQuart']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookPremiumAnnual']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumAnnual']['orderCodeId']=70;
$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId']=129;
$viaProducts['TheStockPlaybookPremiumAnnual']['plan_code']='tsp_annl_stndrd_nft_via_01012012';
$viaProducts['TheStockPlaybookPremiumAnnual']['price']=1899.00;
$viaProducts['TheStockPlaybookPremiumAnnual']['orderItemType']='SUBSCRIPTION';


$viaProducts['TheStockPlaybookPremiumComplimentary']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumComplimentary']['orderCodeId']=81;
$viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId']=151;
$viaProducts['TheStockPlaybookPremiumComplimentary']['plan_code']='tsp_annl_comp_nft_via_01012012';
$viaProducts['TheStockPlaybookPremiumComplimentary']['price']=0;
$viaProducts['TheStockPlaybookPremiumComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['TheStockPlaybookPremiumCK']['orderClassId']=27;
$viaProducts['TheStockPlaybookPremiumCK']['orderCodeId']=82;
$viaProducts['TheStockPlaybookPremiumCK']['typeSpecificId']=154;
$viaProducts['TheStockPlaybookPremiumCK']['price']=1899.00;
$viaProducts['TheStockPlaybookPremiumCK']['orderItemType']='SUBSCRIPTION';

/*TheStockPlayBook Premium End*/


/*============ Buy Hedge- ETF Product ============================*/

$viaProducts['BuyHedgeMonthlyTrial']['orderClassId']=22;
$viaProducts['BuyHedgeMonthlyTrial']['orderCodeId']=59;
$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId']=95;
$viaProducts['BuyHedgeMonthlyTrial']['price']=0.00;
$viaProducts['BuyHedgeMonthlyTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeQuartTrial']['orderClassId']=22;
$viaProducts['BuyHedgeQuartTrial']['orderCodeId']=59;
$viaProducts['BuyHedgeQuartTrial']['typeSpecificId']=96;
$viaProducts['BuyHedgeQuartTrial']['price']=0.00;
$viaProducts['BuyHedgeQuartTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeAnnualTrial']['orderClassId']=22;
$viaProducts['BuyHedgeAnnualTrial']['orderCodeId']=59;
$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId']=91;
$viaProducts['BuyHedgeAnnualTrial']['price']=0.00;
$viaProducts['BuyHedgeAnnualTrial']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeMonthly']['orderClassId']=22;
$viaProducts['BuyHedgeMonthly']['orderCodeId']=56;
$viaProducts['BuyHedgeMonthly']['typeSpecificId']=190;
$viaProducts['BuyHedgeMonthly']['plan_code']='buyhdg_mntly_stndrd_nft_via_01012012';
$viaProducts['BuyHedgeMonthly']['price']=79.00;
$viaProducts['BuyHedgeMonthly']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeQuart']['orderClassId']=22;
$viaProducts['BuyHedgeQuart']['orderCodeId']=56;
$viaProducts['BuyHedgeQuart']['typeSpecificId']=102;
$viaProducts['BuyHedgeQuart']['plan_code']='buyhdg_qrtly_stndrd_nft_via_01012012';
$viaProducts['BuyHedgeQuart']['price']=199.00;
$viaProducts['BuyHedgeQuart']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeAnnual']['orderClassId']=22;
$viaProducts['BuyHedgeAnnual']['orderCodeId']=56;
$viaProducts['BuyHedgeAnnual']['typeSpecificId']=99;
$viaProducts['BuyHedgeAnnual']['plan_code']='buyhdg_annl_stndrd_nft_via_01012012';
$viaProducts['BuyHedgeAnnual']['price']=699.00;
$viaProducts['BuyHedgeAnnual']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeComplimentary']['orderClassId']=22;
$viaProducts['BuyHedgeComplimentary']['orderCodeId']=60;
$viaProducts['BuyHedgeComplimentary']['typeSpecificId']=101;
$viaProducts['BuyHedgeComplimentary']['plan_code']='buyhdg_annl_comp_nft_via_01012012';
$viaProducts['BuyHedgeComplimentary']['price']=0;
$viaProducts['BuyHedgeComplimentary']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeCK']['orderClassId']=22;
$viaProducts['BuyHedgeCK']['orderCodeId']=57;
$viaProducts['BuyHedgeCK']['typeSpecificId']=97;
$viaProducts['BuyHedgeCK']['price']=499.00;
$viaProducts['BuyHedgeCK']['orderItemType']='SUBSCRIPTION';

$viaProducts['BuyHedgeST']['orderClassId']=22;
$viaProducts['BuyHedgeST']['orderCodeId']=58;
$viaProducts['BuyHedgeST']['typeSpecificId']=105;
$viaProducts['BuyHedgeST']['price']=0.00;
$viaProducts['BuyHedgeST']['orderItemType']='SUBSCRIPTION';



/*Via products name on the basis of sub_def_id(orderClassId)*/

$viaProductsNameHousing['118']="Las Vegas";
$viaProductsNameHousing['119']="Miami-Dade Co.";
$viaProductsNameHousing['120']="Chicago";
$viaProductsNameHousing['121']="Phoenix";
$viaProductsNameHousing['122']="Washington D.C.";
$viaProductsNameHousing['123']="San Diego";
$viaProductsNameHousing['124']="New York Metro";
$viaProductsNameHousing['155']="Atlanta";
$viaProductsNameHousing['158']="Dallas";
$viaProductsNameHousing['160']="Los Angeles";
$viaProductsNameHousing['163']="Minneapolis";
$viaProductsNameHousing['168']="Portland";
$viaProductsNameHousing['170']="Orlando";
$viaProductsNameHousing['172']="Seattle";
$viaProductsNameHousing['176']="San Francisco";
$viaProductsNameHousing['188']="Long Island";

/*Via products name on the basis of oc_id(orderClassId)*/

$viaProductsName['buzz']="Buzz & Banter";
$viaProductsName['techstrat']="TechStrat Report";
/*$viaProductsName['garyk']="Gary K's Equity Trading Setups";*/
$viaProductsName['cooper']="Jeff Cooper's Daily Market Report";
$viaProductsName['optionsmith']="OptionSmith";
$viaProductsName['thestockplaybook']="The Stock Playbook";
$viaProductsName['adsfree']="Ads Free";
//$viaProductsName['housingmarket']="Housing Market Report";
$viaProductsName['peterTchir']="Peter Tchir's Fixed Income Report";
$viaProductsName['ElliottWave']="Elliott Wave Insider";
$viaProductsName['keene']="Keene On Options";
/*$viaProductsName['buyhedge']="ETF Strategies from Buy & Hedge";*/

/* Via Products Discount */
$viaProductDiscount['BuzzAnnual'] = "30%";
$viaProductDiscount['BuzzQuart'] = "21%";
$viaProductDiscount['CooperAnnual'] = "33%";
$viaProductDiscount['CooperQuart'] = "16%";
$viaProductDiscount['OptionSmithAnnual'] = "25%";
$viaProductDiscount['FlexfolioAnnual'] = "30%";
$viaProductDiscount['TechStratQuart'] = "14%";
$viaProductDiscount['TechStratAnnual'] = "30%";
$viaProductDiscount['TheStockPlayBookQuart'] = "16%";
$viaProductDiscount['TheStockPlayBookAnnual'] = "24%";

$viaProductDiscount['GaryKQuart'] = "14%";
$viaProductDiscount['GaryKAnnual'] = "30%";

$viaProductDiscount['Housing3Months'] = "55%";
$viaProductDiscount['Housing6Months'] = "65%";
$viaProductDiscount['HousingAnnual'] = "72%";
$viaProductDiscount['TheStockPlaybookPremiumQuart'] = "17.5%";
$viaProductDiscount['TheStockPlaybookPremiumAnnual'] = "25%";

$promoCodeSourceCodeNoFreeTrial = array();
$promoCodeSourceCodeNoFreeTrial[] = 34;
$promoCodeSourceCodeNoFreeTrial[] = 20;
$promoCodeSourceCodeNoFreeTrial[] = 43;
$promoCodeSourceCodeNoFreeTrial[] = 49;
$promoCodeSourceCodeNoFreeTrial[] = 50;

/*send email for moneyshow co-reg deal error*/
$toSoftTrialErrorEmail="mvil@ebusinessware.com";
$fromSoftTrialErrorEmail="support@minyanville.com";
$subjectSoftTrialErrorEmail="SoftTrial Registration Error- MoneyShow";
$tmplSoftTrialRegisterError=$HTPFX.$HTADMINHOST."/emails/_eml_softtrial_registration_error.htm";

/*send email for moneyshow co-reg deal error*/
$fromSoftTrialWelcomeEmail="subscriptions@minyanville.com";
$subjectSoftTrialWelcomeEmail="Welcome to Your Free 14-Day Buzz & Banter Trial";
$tmplSoftTrialWelcome="/emails/_eml_welcomeMoneyShowSoftTrial.htm";

/* Product name  on the basis of promo code source code id*/
$viaPromoProductName['BUZZ40'] = "Buzz & Banter" ;
$viaPromoProductName['PINN20'] = "Buzz & Banter" ;

/* Order Class id for which trial not allowed */
$viaOrderClassId = array();
$viaOrderClassId[] = "21"; //AdsFree Minyanville
$viaOrderClassId[] = "25"; //Housing Report
$viaOrderClassId[] = "26"; //Housing Report Single Issue
$viaOrderClassId[] = "14"; //Option smith
$viaOrderClassId[] = "28"; //GarryK

$viaMoneyBackClassId[] = "14";
$viaMoneyBackClassId[] = "28"; //GarryK

$moneyBackSealText = "Start your subscription today and receive our 14 day money back guarantee. If you're not 100% satisfied with your subscription, simply contact us within 14 days and receive a full refund - guaranteed.</a>
";

$welcomeSemBuzzSubject="Welcome to Buzz and Banter";
$welcomeSemBuzzTemplate=$HTDOMAIN.'/emails/_eml_sembuzz_welcome.htm';
$softtrialSemBuzzFrom="Minyanville <subscriptions@minyanville.com>";

$welcomeOneWeekBuzzSubject="Welcome to Buzz and Banter";
$welcomeOneWeekBuzzTemplate=$HTPFX.$HTADMINHOST.'/emails/_eml_buzzbanter_oneweektrial_welcome.htm';
$softtrialOneWeekBuzzFrom="Minyanville <subscriptions@minyanville.com>";

/* Email configuration on product base */
$productOrder=array('flexfolio'=>'3,4,14,22,40,activeinvestr_mntly_stndrd_nft_via_04012011,activeinvestr_annl_comp_nft_via_04012011,activeinvestr_annl_stndrd_nft_via_04012011',

					"cooper"=>"5,6,13,21,39,cooper_mntly_stndrd_nft_via_01012012,cooper_annl_comp_nft_via_01012012,cooper_annl_stndrd_nft_via_01012012",

					'buzz'=>'1,2,12,20,37,buzz_mntly_stndrd_nft_via_01012012,buzz_annl_comp_nft_via_01012012,buzz_annl_stndrd_nft_via_01012012',

					'optionsmith'=>'23,24,25,26,42,optsmth_mntly_stndrd_nft_via_01012012,optsmth_annl_comp_nft_via_01012012,optsmth_annl_stndrd_nft_via_01012012',
					'BMTP'=>'27,28,29,30',

					'JACK'=>'32,33,34,35,41',

					'ETF'=>'43,44,46,47,48,tchstrt_mntly_stndrd_nft_via_01012012,tchstrt_annl_comp_nft_via_01012012,tchstrt_qrtly_stndrd_nft_via_01012012,tchstrt_annl_stndrd_nft_via_01012012',

					'THESTOCKPLAYBOOK'=>'49,50,51,52,53,tsp_mntly_stndrd_nft_via_01012012,tsp_qtrly_stndrd_nft_via_01012012,tsp_annl_stndrd_nft_via_01012012,tsp_annl_comp_nft_via_01012012',

					'TechStrat'=>'61,62,63,64,65,tchstrt_mntly_stndrd_nft_via_01012012,tchstrt_qrtly_stndrd_nft_via_01012012,tchstrt_annl_stndrd_nft_via_01012012,tchstrt_annl_comp_nft_via_01012012',

					"Housing"=>"67,71,hmr_qrtly_stndrd_nft_via_01012012,hmr_annl_comp_nft_via_01012012",

					'GaryK'=>'73,74,75,76,77,78,garyk_mntly_stndrd_nft_via_010102012,garyk_qrtly_stndrd_nft_via_01012012,garyk_annl_stndrd_nft_via_01012012,garyk_annl_comp_nft_via_0102012',

					'THESTOCKPLAYBOOKPREMIUM'=>'70,81,82,85,86,tsp_mntly_stndrd_nft_via_01012012,tsp_qtrly_stndrd_nft_via_01012012,tsp_annl_stndrd_nft_via_01012012,tsp_annl_comp_nft_via_01012012',
                                     'BUYHEDGE'=>'56,56,57,58,59,60,buyhdg_mntly_stndrd_nft_via_01012012,buyhdg_qrtly_stndrd_nft_via_01012012m,buyhdg_annl_stndrd_nft_via_01012012,buyhdg_annl_comp_nft_via_01012012'
                                        );

$productUrl['Homepage_Subscription_Buzz']='http://mvp.minyanville.com/buzz-banter-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=buzz';
$productUrl['Homepage_Subscription_OptionSmith']='http://mvp.minyanville.com/optionsmith-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=optionsmith';
$productUrl['Homepage_Subscription_Cooper']='http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=cooper';
$productUrl['Homepage_Subscription_TechStrat']='http://mvp.minyanville.com/techstrat-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=techstrat';
$productUrl['Homepage_Subscription_Stock_Playbook']='http://mvp.minyanville.com/stock-playbook-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=thestockplaybook';
$productUrl['Homepage_Subscription_Housing_Market_Report']='http://mvp.minyanville.com/housing-market-report-landing-page-site/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=housingmarket';
$productUrl['Homepage_Subscription_Buy_Hedge']='http://mvp.minyanville.com/buy-and-hedge-landing-page-homepage-module/';
$productUrl['Homepage_Subscription_Add_Free']='http://mvp.minyanville.com/ads-free-landing-page-site/';
$productUrl['Homepage_Subscription_PeterTchir']='http://mvp.minyanville.com/peter-tchir-landing-page-homepage-module';
$productUrl['Homepage_Subscription_ElliottWave']='http://mvp.minyanville.com/ewi-landing-page-homepage-module/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=elliottwave';
$productUrl['Homepage_Subscription_Keene']='http://mvp.minyanville.com/keene-landing-page-navbar/?utm_source=Homepage Modules&utm_medium=website&utm_content=Homepage Modules&utm_campaign=keene';

$pageUrlArr['/buzzbanter/']['key']="Buzz";
$pageUrlArr['/buzzbanter/']['url']="http://mvp.minyanville.com/buzz-banter-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=buzz";
$pageUrlArr['/cooper/']['key']="Cooper";
$pageUrlArr['/cooper/']['url']="http://mvp.minyanville.com/jeff-coopers-daily-market-report-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=cooper";
$pageUrlArr['/buyhedge/']['key']="buyhedge";
$pageUrlArr['/buyhedge/']['url']="http://mvp.minyanville.com/buy-hedge-landing-page-navigation-bar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=buy&hedge";
$pageUrlArr['/optionsmith/']['key']="Optionsmith";
$pageUrlArr['/optionsmith/']['url']="http://mvp.minyanville.com/optionsmith-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=optionsmith";
$pageUrlArr['/techstrat/']['key']="TechStrat";
$pageUrlArr['/techstrat/']['url']="http://mvp.minyanville.com/techstrat-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=techstrat";
$pageUrlArr['/the-stock-playbook/']['key']="TheStockPlayBook";
$pageUrlArr['/the-stock-playbook/']['url']="http://mvp.minyanville.com/stock-playbook-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=thestockplaybook";
$pageUrlArr['/housing-market-report/']['key']="HousingReport";
$pageUrlArr['/housing-market-report/']['url']="http://mvp.minyanville.com/housing-market-report-landing-page-site/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=housingmarket";
$pageUrlArr['/ad-free/']['key']="AdsFree";
$pageUrlArr['/ad-free/']['url']="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=adsfree";
$pageUrlArr['/tchir-fixed-income/']['key']="peterTchir";
$pageUrlArr['/tchir-fixed-income/']['url']="http://mvp.minyanville.com/peter-tchir-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=peterTchir";
$pageUrlArr['/ewi/']['key']="ElliottWave";
$pageUrlArr['/ewi/']['url']="http://mvp.minyanville.com/ewi-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=elliottwave";
$pageUrlArr['/keene/']['key']="keene";
$pageUrlArr['/keene/']['url'] = "http://mvp.minyanville.com/keene-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=keene";


$trialUrlArr['subBuzzBanter']="http://mvp.minyanville.com/buzz-banter-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=buzz";
$trialUrlArr['subCooper']="http://mvp.minyanville.com/jeff-coopers-daily-market-report-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=cooper";
$trialUrlArr['subOptionSmith']="http://mvp.minyanville.com/optionsmith-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=optionsmith";
$trialUrlArr['subTechStrat']="http://mvp.minyanville.com/techstrat-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=techstrat";
$trialUrlArr['subAdFree']="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=adsfree";
$trialUrlArr['subBuyHedge']="http://mvp.minyanville.com/buy-hedge-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=buy&hedge";
$trialUrlArr['subStockPlayBook']="http://mvp.minyanville.com/stock-playbook-step-2-subscriptions-page/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=thestockplaybook";
$trialUrlArr['subHousingReport']="http://mvp.minyanville.com/housing-market-report-step-2-site/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=housingmarket";
$trialUrlArr['subKeene']="http://mvp.minyanville.com/keene-step-2/?utm_source=Subscriptions Page&utm_medium=website&utm_content=Subscriptions Page&utm_campaign=keene";

$productStep2Url['buzz'] = "http://mvp.minyanville.com/buzz-banter-step-2-navigation-bar/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=buzz";
$productStep2Url['techstrat'] = "http://mvp.minyanville.com/techstrat-step-2-navigation-bar/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=techstrat";
$productStep2Url['cooper'] = "http://mvp.minyanville.com/jeff-coopers-daily-market-report-step-2-navigation-bar/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=cooper";
$productStep2Url['optionsmith'] = "http://mvp.minyanville.com/optionsmith-step-2-navigation-bar/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=optionsmith";
$productStep2Url['thestockplaybook'] = "http://mvp.minyanville.com/stock-playbook-step-2-navigation-bar/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=thestockplaybook";
$productStep2Url['housingmarket'] = "http://mvp.minyanville.com/housing-market-report-step-2-site/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=housingmarket";
$productStep2Url['buyhedge'] = "http://mvp.minyanville.com/buy-hedge-step-2-navigation-bar/";
$productStep2Url['adsfree'] = "http://mvp.minyanville.com/ads-free-step-2-site/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=adsfree";
$productStep2Url['peterTchir']="http://mvp.minyanville.com/peter-tchir-landing-page-subscriptions-page/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=petertchir";
$productStep2Url['ElliottWave']="http://mvp.minyanville.com/ewi-landing-page-subscriptions-page/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=elliottwave";
$productStep2Url['keene'] = "http://mvp.minyanville.com/keene-step-2/?utm_source=My Account Page&utm_medium=Website&utm_content=My Account Page&utm_campaign=keene";
/*For CP*/
$trialUrlCpArr['subBuzzBanter']="http://mvp.minyanville.com/buzz-banter-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=buzz";
$trialUrlCpArr['subCooper']="http://mvp.minyanville.com/jeff-coopers-daily-market-report-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=cooper";
$trialUrlCpArr['subOptionSmith']="http://mvp.minyanville.com/optionsmith-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=optionsmith";
$trialUrlCpArr['subTechStrat']="http://mvp.minyanville.com/techstrat-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=techstrat";
$trialUrlCpArr['subAdFree']="http://mvp.minyanville.com/ads-free-landing-page-site/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=adsfree";
$trialUrlCpArr['subBuyHedge']="http://mvp.minyanville.com/buy-hedge-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=buy&hedgee";
$trialUrlCpArr['subStockPlayBook']="http://mvp.minyanville.com/stock-playbook-step-2-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=thestockplaybook";
$trialUrlCpArr['subHousingReport']="http://mvp.minyanville.com/housing-market-report-step-2-site/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=housingmarket";
$trialUrlCpArr['subPeterTchir']="http://mvp.minyanville.com/peter-tchir-landing-page-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=petertchir";
$trialUrlCpArr['subElliottWave']="http://mvp.minyanville.com/ewi-landing-page-subscriptions-page/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=elliottwave";
$trialUrlCpArr['subKeene']="http://mvp.minyanville.com/keene-step-2/?utm_source=Control Panel&utm_medium=Website&utm_content=Control Panel&utm_campaign=keene";


$prodList['buzz']= "Buzz";
$prodList['cooper']="Cooper";
$prodList['optionsmith']="Optionsmith";
$prodList['techstrat']="Techstrat";
$prodList['stockplaybook']="Stock Playbook";
$prodList['housingmarket']="Housing Market";
$prodList['peterTchir']="Peter Tchir Fixed Income Report";

$productPages= array();
$productPages[] ='subscription_product';
$productPages[] ='buzzbanter';
$productPages[] ='thestockplaybook';
$productPages[] ='toodbook';
$productPages[] ='qphome';
$productPages[] ='cooperhome';
$productPages[] ="sshome";
$productPages[] ="bmtp_home";
$productPages[] ="jack_home";
$productPages[] ="etf_home";
$productPages[] ="adsFree";
$productPages[] ="techstrat_landing";
$productPages[] ="techstrat_tradeideas";
$productPages[] ="techstrat_mailbag";
$productPages[] ="techstrat_researchtank";
$productPages[] ="techstrat_performance";
$productPages[] ="techstrat_post";
$productPages[] ="techstrat_search";
$productPages[] ="techstrat_allpost";
$productPages[] ="housingMarketReport_landing";
$productPages[] ="housingMarketReport_allpost";
$productPages[] ="housingMarketReport_post";
$productPages[] ="housingMarketReport_archive";
$productPages[] ="housingMarketReport_search";
$productPages[] ="housingMarketReport_report";
$productPages[] ="housingMarketReport_freeReport";
$productPages[] ="garyk_landing";
$productPages[] ="garyk_allpost";
$productPages[] ="garyk_mailbag";
$productPages[] ="garyk_performance";
$productPages[] ="garyk_reports";
$productPages[] ="garyk_search";
$productPages[] ="garyk_post";
$productPages[] ="buyhedge_post";
$productPages[] ="buyhedge_landing";
$productPages[] ="buyhedge_allpost";
$productPages[] ="buyhedge_trading";
$productPages[] ="buyhedge_investing";
$productPages[] ="buyhedge_heading";
$productPages[] ="buyhedge_targetportfolios";
$productPages[] ="buyhedge_search";
$productPages[] ="buyhedge_ticker";
$productPages[] ="controlPanel";
$productPages[] ="buyhedge_features";
$productPages[] ="manage_setting";
$productPages[] ="login";
$productPages[] ="techstrat_openposition";
$productPages[] ="techstrat_closeposition";
$productPages[] ="peter-tchir";
$productPages[] ="peterTchirAllPost";
$productPages[] ="peterTchirWeeklyReport";
$productPages[] ="peterTchirIntraWeek";
$productPages[] ="peterTchirBio";
$productPages[] ="peterTchirSearch";
$productPages[] ="peterTchirAlert";
$productPages[] ="elliott-wave";
$productPages[] ="elliott-wave-search";
$productPages[] ="elliott-wave-extras";
$productPages[] ="elliott-wave-analysts";
$productPages[] ="elliott-wave-alert";
$productPages[] ="elliott-wave-bio";
$productPages[] ="keene";
$productPages[] ="keeneBios";
$productPages[] ="keeneAlert";
$productPages[] ="keeneSearchAlert";
?>