<?php
global $recurlyApiKey,$privateKey;

// Required for the API - Prodution
$recurlyApiKey='16f21b3f898e44dea3999d4983a8dcac';
// Optional for Recurly.js:
$privateKey='1b697d9faf9f4134bcbc2eafd3a5d91c';
$subdomain = 'minyanville';

//Key for Free site
//$recurlyApiKey='8f1a8fe801b24e6a9207701bffe4b585';
//$privateKey = '915fc354c4934e9bbb98a7abafad4cc4';
//$subdomain = 'minyanvilleqa';

//if email password is blank
$globalPwd = "Minyan";
$pwdSubject = "Your Minyanville Account Password";

//Emails for duplicate email entry
$emailTo = "mvilsupport@mediaagility.com";
$emailFrom = "mvilsupport@mediaagility.com";
$emailSubject = "Duplicate Entry For Email";

$toRecurlyError = "mvilsupport@mediaagility.com";
$fromRecurlyError = "mvilsupport@mediaagility.com";
$subjectRecurlyError = "Recurly Pushnotification Error";

/* Random Product details for corss-sell page start*/
$productDesc['buzz']['planName']='Buzz & Banter';
$productDesc['buzz']['description']='Get savvy market commentary and must-have analysis from over 30 veteran traders in real-time, everyday! With the <b>Buzz & Banter</b>, you get a constant stream of detailed insights on the market&acute;s hottest, most profitable trends. The Buzz curates the crucial information you need and puts it right in front of you, making it easier than ever to make smart, money-making decisions FAST. Try it for free now!';
$productDesc['buzz']['planCode'] = 'buzz_mntly_stndrd_ft_crssll_06012012';
$productDesc['buzz']['trialImage'] = 'tryBuzz&Banter.jpg';

$productDesc['techstrat']['planName']='TechStrat';
$productDesc['techstrat']['description']='Jump on tomorrow&acute;s winning Tech stocks today, with this powerful tool from Sean Udall! The <b>TechStrat</b> gives you razor-sharp analysis, direct access to a Tech expert&acute;s open and closed positions, and dozens of money-making trade ideas on the hot Tech stocks poised for big moves. Try it for free now!';
$productDesc['techstrat']['planCode'] = 'tchstrt_mntly_stndrd_ft_crssll_06012012';
$productDesc['techstrat']['trialImage'] = 'tryTechStrat_new.png';

$productDesc['cooper']['planName']='Daily Market Report';
$productDesc['cooper']['description']='Follow along with Jeff Cooper, day and swing trading champion, as he puts his visionary methods of pattern analysis to work for you! Jeff&acute;s unique mastery of market cycles helps him and his <b>Daily Market Report</b> subscribers find high probability trade set-ups each and every trading day. Try it for free now!';
$productDesc['cooper']['planCode'] = 'cooper_mntly_stndrd_ft_crssll_06012012';
$productDesc['cooper']['trialImage'] = 'tryDailyMarket.png';

$productDesc['optionsmith']['planName']='OptionSmith';
$productDesc['optionsmith']['description']='Power up your portfolio with Steve Smith&acute;s market-busting options trades! Year after year the <b>OptionSmith</b> portfolio crushes the major indices, with a +95% total average return! Let Steve&acute;s razor-sharp market instincts and decades of options trading experience help you chose the right tools to make you the most money with options. Try it for free now!';
$productDesc['optionsmith']['planCode'] = 'optsmth_mntly_stndrd_ft_crssll_07062012';
$productDesc['optionsmith']['trialImage'] = 'tryOptionsmith.png';
/* Random Product details for corss-sell page End*/

$closedPlans['tchstrt_annl_199_nft_flash_06192012'] = 'http://mvp.minyanville.com/techstrat-landing-page-navbar/';

//$toExpireSubs="support@minyanville.com,mvilsupport@mediaagility.com";
$toExpireSubs = array("support@minyanville.com","mvilsupport@mediaagility.com","fiore@minyanville.com","gibbons@minyanville.com","anjali@minyanville.com");
$toExpireBccSubs="mvilsupport@mediaagility.com";
$fromExpireSubs="support@minyanville.com";
$subjectExpireSubs="List of Renewal";

$recurlyErrorArray=array();
$recurlyErrorArray['Number is not a valid credit card number.']="Credit card number is not valid.";
$recurlyErrorArray['Number is not a valid credit card number, number is expired or has an invalid expiration date.']='Credit card number is not valid and has an invalid expiration date.';
$recurlyErrorArray['Number is expired or has an invalid expiration date.']='Credit card number is expired or has an invalid expiration date.';
$recurlyErrorArray['Number is expired or has an invalid expiration date, zip is invalid.']='Zip is invalid.';

$scriptPlanCodeArray = array();
$scriptPlanCodeArray['buzz_mntly_stndrd_ft_ic120x60_1252012']='buzz_mntly_stndrd_ft_ic120x60_1252012';
$scriptPlanCodeArray['buzz_mntly_stndrd_ft_ic1x1text_1252012']='buzz_mntly_stndrd_ft_ic1x1text_1252012';
$scriptPlanCodeArray['buzz_mntly_stndrd_ft_ic300x250_1252012']='buzz_mntly_stndrd_ft_ic300x250_1252012';

?>