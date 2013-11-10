<?php
global $IMG_SERVER,$HTPFX,$HTHOST;
$realMoneySilverURL		=	'http://www.thestreet.com/feeds/rss/minyanville/kass/index.xml';
$thestreetURL			=	'http://www.thestreet.com/feeds/search/index.html?includeContent=true&feedId=822';
$RMSheadlineURL			= 'http://www.thestreet.com/feeds/search/headline/index.html?feedId=821';
$thestreetRelatedLinks	=	'http://www.thestreet.com/feeds/search/index.html?feedId=822';
$articleAdTS=array();

$articleAdTS['BUZZAD1']='<a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=articles&amp;utm_medium=subad&amp;utm_campaign=buzzbanter&amp;from=thestreet" class="lightsGal"><img height="123" align="right" width="221" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/Buzz Ad 30 Pro Traders.jpg" alt=""></a>';

$articleAdTS['COOPERAD1']='<a href="'.$HTPFX.$HTHOST.'/cooper?utm_source=articles&amp;utm_medium=subad&amp;utm_campaign=cooper&amp;from=thestreet" class="lightsGal"><img height="123" align="right" width="221" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/DailyMarketReport_ad2_221x123.jpg" alt=""></a>';

$articleAdTS['ETFAD1']='<a href="'.$HTPFX.$HTHOST.'/etf?utm_source=articles&amp;utm_medium=subad&amp;utm_campaign=etf&amp;from=thestreet"><img height="123" align="right" width="221" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub%20ads%20for%20Articles/ETF%20Ad%202b.jpg" alt="" /></a>';

$articleAdTS['OPTIONSMITHAD1']='<a href="'.$HTPFX.$HTHOST.'/optionsmith?utm_source=articles&amp;utm_medium=subad&amp;utm_campaign=optionsmith&amp;from=thestreet" class="lightsGal"><img width="200" height="112" align="right" alt="" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/optionsmith test ad.jpg"></a>';

$articleAdTS['STOCKPLAYBOOKAD1']='<a href="'.$HTPFX.$HTHOST.'/the-stock-playbook/?utm_source=articles&amp;utm_medium=subad&amp;utm_campaign=tsp&amp;from=thestreet" class="lightsGal"><img width="221" height="123" align="right" alt="" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/stockplaybook_221x123.jpg"></a>';

$articleAdTS['FLEXFOLIOAD1']='<a href="'.$HTPFX.$HTHOST.'/qp/?utm_source=articles&amp;utm_medium=Text&amp;utm_content=EveryTrade&amp;utm_campaign=FlexFolio&amp;from=thestreet" class="lightsGal"><img height="123" align="right" width="221" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/flexFolio_ad4_221x123.jpg" alt=""></a>';

$articleAdTS['BUZZ14DAYTRIAL']='<i>Follow the markets all day every day with a <a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=articles&amp;utm_medium=bottomtext&amp;utm_campaign=freetrial&amp;from=thestreet" class="lightsGal"><b>FREE    14 day trial</b> to Buzz &amp; Banter.</a>   Over 30 professional traders<a id="KonaLink4" target="undefined" class="kLink" style="text-decoration: underline ! important; position: static;" href="'.$HTPFX.$HTHOST.'/businessmarkets/articles/stocks-emerging-market-stocks-economies-investing/8/3/2010/id/29435?page=4#&amp;from=thestreet"><font color="#01509d" style="color: rgb(1, 80, 157) ! important; font-family: arial,helvetica,sans-serif,verdana; font-weight: 400; font-size: 15px; position: static;"><span class="kLink" style="color: rgb(1, 80, 157) ! important; font-family: arial,helvetica,sans-serif,verdana; font-weight: 400; font-size: 15px; position: relative;"></span></font></a>  share their ideas in  real-time. <a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=articles&amp;utm_medium=bottomtext&amp;utm_campaign=learnmore&amp;from=thestreet" class="lightsGal">Learn    more.</a></i>';

$articleAdTS['FLEXFOLIOTEXT']='<i><b>Get access to Quint&acute;s portfolio! </b>Take a </i><a href="'.$HTPFX.$HTHOST.'/qp?utm_source=articles&amp;utm_medium=bottomtext&amp;utm_campaign=freetrial&amp;from=thestreet" class="lightsGal"><b><i>FREE 14 day trial</i></b></a><i> to Minyanville&acute;s </i><a href="'.$HTPFX.$HTHOST.'/qp?utm_source=articles&amp;utm_medium=bottomtext&amp;utm_campaign=name&amp;from=thestreet" class="lightsGal"><i>FlexFolio by Quint Tatro</i></a><i>. Receive email</i><a target="undefined" href="'.$HTPFX.$HTHOST.'/businessmarkets/articles/china-Shanghai-Stock-Index-traders-short/3/4/2010/id/27133?page=2#&amp;from=thestreet" style="position: static; text-decoration: underline ! important;" id="KonaLink3" class="kLink"><font color="#01509d" style="font-weight: 400; font-size: 15px; color: rgb(1, 80, 157) ! important; font-family: arial,helvetica,sans-serif,verdana; position: static;"><span style="font-weight: 400; font-size: 15px; color: rgb(1, 80, 157) ! important; font-family: arial,helvetica,sans-serif,verdana; position: relative;" class="kLink"></span></font></a><i> alerts with every trade, interactive strategy sessions and much more. FlexFolio is <b>beating the S&amp;P 500 by 31%</b> since inception. </i><a href="'.$HTPFX.$HTHOST.'/qp?utm_source=articles&amp;utm_medium=bottomtext&amp;utm_campaign=learnmore&amp;from=thestreet" class="lightsGal"><i>Learn more</i></a><i>.</i>';

$articleAdTS['ETFTEXT'] ='Trade ETFs? Take a <a href="'.$HTPFX.$HTHOST.'/etf?utm_source=article&utm_medium=bottomtext&utm_campaign=freetrial&amp;from=thestreet" class="lightsGal"><b>FREE 14 day trial</b> to the Grail ETF & Equity Investor</a> newsletter. Receive specific trades and strategies across many sectors. <a href="'.$HTPFX.$HTHOST.'/etf?utm_source=article&utm_medium=bottomtext&utm_campaign=learnmore&amp;from=thestreet">Learn more.</a>';

$articleAdTS['COOPERTEXT'] ='Editor\'s Note: The following is a free edition of <a href="'.$HTPFX.$HTHOST.'/cooper?utm_source=article&utm_medium=toptext&utm_campaign=cooper&amp;from=thestreet">Jeff Cooper\'s Daily Market Report</a>. For a two-week FREE trial of his daily commentary and nightly day and swing trading picks, <a href="'.$HTPFX.$HTHOST.'/cooper?utm_source=article&utm_medium=toptext&utm_campaign=clickhere&amp;from=thestreet">click here.</a>';

$articleAdTS['COOPER14DAYTRIAL'] = 'Get Jeff\'s commentary plus day &amp; swing trading ideas each day with a <a href="'.$HTPFX.$HTHOST.'/cooper?utm_source=JCarticle&utm_medium=bottomtext&utm_campaign=cooper&amp;from=thestreet"><b>FREE 14 day trial to Jeff Cooper\'s Daily Market Report.</b></a>
';

$articleAdTS['OPTIONSMITHTEXT']='For more from Steve Smith, take a <a href="'.$HTPFX.$HTHOST.'/optionsmith/?utm_source=ArticleBottomSS&utm_medium=Text&utm_content=SpecificTrades&utm_campaign=OptionSmith&amp;from=thestreet"><b>FREE 14-day trial to OptionSmith</b></a> and get his specific options trades emailed to you along with exclusive access to his full portfolio. <a href="'.$HTPFX.$HTHOST.'/optionsmith/?utm_source=ArticleBottomSS&utm_medium=Text&utm_content=SpecificTrades&utm_campaign=OptionSmith&amp;from=thestreet">Learn more.</a>';

$articleAdTS['BUZZTEXT']='Follow Todd and over 30 professional traders as they share their ideas in real-time with a <a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=ToddArticle&utm_medium=bottomtext&utm_campaign=freetrial&amp;from=thestreet">FREE 14 day trial to Buzz & Banter.</a>';

$articleAdTS['ETFADTEXT'] = 'Editor\'s Note: The following is from the <a href="'.$HTPFX.$HTHOST.'/etf?utm_source=RCarticle&utm_medium=toptext&utm_campaign=grail&amp;from=thestreet">Grail ETF & Equity Investor</a> newsletter.';

$articleAdTS['ETF14DAYTRIAL'] = 'For more from Ron & Denny take a FREE 14-day trial to the Grail ETF & Equity Investor newsletter. They use their proprietary trend and timing indicators to find subscribers ETFs poised for big moves. Learn more.';

$articleAdTS['TODDBUZZ']='<a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=THarticle&utm_medium=subad&utm_campaign=toddbuzz&amp;from=thestreet" class="lightsGal"><img width="200" height="112" align="right" alt="" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/Todd 30 Pro Traders Buzz.jpg"></a>';

$articleAdTS['DEPEWBUZZ']='<a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=KDarticle&utm_medium=subad&utm_campaign=depewbuzz&amp;from=thestreet" class="lightsGal"><img width="200" height="112" align="right" alt="" src="'.$IMG_SERVER.'/assets/FCK_May2009/Image/Sub ads for Articles/follow_KevinD_221x123.jpg"></a>';

$articleAdTS['ETFTRIAL']='For more on ETFs take a <a href="'.$HTPFX.$HTHOST.'/etf?utm_source=ETFarticle&amp;utm_medium=bottomtext&amp;utm_campaign=freetrial&amp;from=thestreet" target="_blank"><strong>FREE 14 day trial</strong> to the Grail ETF &amp; Equity Investor</a> newsletter. Ron Coby &amp; Denny Lamson use their proprietary Grail indicator to find ETFs poised for big moves. <a href="'.$HTPFX.$HTHOST.'/etf?utm_source=ETFarticle&amp;utm_medium=bottomtext&amp;utm_campaign=learnmore&amp;from=thestreet" target="_blank">Learn more</a>.';


$articleAdTS['TOPTEXT']= 'Below are some of the most-discussed charts on Minyanville\'s <a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=buzzcharts&utm_medium=toptext&utm_campaign=name&amp;from=thestreet
">Buzz & Banter</a>, presented without bias, claim, or injury. For in-depth commentary, analysis, and trading ideas in these stocks and much more, <a href="'.$HTPFX.$HTHOST.'/buzzbanter?utm_source=buzzcharts&utm_medium=toptext&utm_campaign=name&amp;from=thestreet
">take a free two week trial</a> to Buzz & Banter.';

$articleAdTS['FESTIVUS']='<a href="http://www.rpfoundation.org/events.asp"><img alt="" src="'.$IMG_SERVER.'/images/festivus_reg.jpg"></a>';

$articleDisclaimer="<i>Minyanville articles solely reflects the analysis of or opinion about the performance of securities and financial markets by the writers. The views expressed by the writers are not necessarily the views of Minyanville Media, Inc. or members of its management. Nothing contained in the articles is intended to constitute a recommendation or advice addressed to an individual investor or category of investors to purchase, sell or hold any security, or to take any action with respect to the prospective movement of the securities markets or to solicit the purchase or sale of any security. Any investment decisions must be made by the reader either individually or in consultation with his or her investment professional. Minyanville writers and staff may trade or hold positions in securities that are discussed in articles. Writers of articles are required to disclose whether they have a position in any stock or fund discussed in an article, but are not permitted to disclose the size or direction of the position. Nothing in the articles is intended to solicit business of any kind for a writer&acute;s business or fund. Minyanville management and staff as well as contributing writers will not respond to emails or other communications requesting investment advice.<br /></i>";


?>