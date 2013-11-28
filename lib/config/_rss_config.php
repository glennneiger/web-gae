<?php
/*non-Minyanville contributors list*/
$smvcList="'243','246','253','274','284','292','294','309','334','363','368','370','376','383','441','452','477','481','512'";

$arFeedPartners = array( "c4ca4238a0b923820dcc509a6f75849b" => array("tracking_name"=>"etrade",
																	 "portal_name" => "E*Trade"), // Etrade
				 "c81e728d9d4c2f636f067f89cc14862c" =>  array("tracking_name"=>"thestreet",
																	 "portal_name" => "The Street"), // Street.com
				 "eccbc87e4b5ce2fe28308fd9f2a7baf3" => array("tracking_name"=>"ameritrade",
																	 "portal_name" => "TD"), // TD Trade Arcitecht
				 "a87ff679a2f3e71d9181a67b7542122c" => array("tracking_name"=>"lightspeed",
																	 "portal_name" => "LIGHTSPEED"),
				 "xFPialsyUx1DLy9UdQxNxHqhWQ9We4Xi" => array("tracking_name"=>"yahoolivestand",
																	 "portal_name" => "Yahoo Livestand "),
				 "f64c7eu4r6y3n7kack2bpwobgynmai5h" => array("tracking_name"=>"businessinsider",
																	 "portal_name" => "Business Insider"),
				 "c8emk6i3n4y4bkhykugw3936m8d2gop9" => array("tracking_name"=>"investors",
																	 "portal_name" => "Investor Business Daily"),
				"f468c507a1204d5e912d580e411ab36a" => array("tracking_name"=>"nasdaq",
																	 "portal_name" => "Nasdaq"),
				"e4da3b7fbbce2345d7772b0674a318d5" => array("tracking_name"=>"googlecurrents",
																	 "portal_name" => "googlecurrents"),
				"D3D9446802A44259755D38E6D163E820" => array("tracking_name"=>"buzzbanter",
																	 "portal_name" => "buzzbanter"),
				"241f4baccd80454d89cdf63d7af7920b" => array("tracking_name"=>"marketiq",
																	 "portal_name" => "marketiq"),
                "2HAthamarev5RAfU8aFEKepruJeFraZA" => array("tracking_name"=>"yahoo",
																	 "portal_name" => "yahoo"),
                "PraJ6keqe57stEzu2ethuyAdr6thU6up" => array("tracking_name"=>"yahooFull",
																	 "portal_name" => "yahooFull"),
				"qbvlNGTcW7e0FTJLHVIT2Mfpq20vVlMU" => array("tracking_name"=>"mvpremiumyahoo",
																	 "portal_name" => "mvpremiumyahoo"),
				"UwX8053S76293TY171Y33581HRn32U87" => array("tracking_name"=>"gravity",
																	 "portal_name" => "gravity")      
				);

// array key is autoincrement prepend with tracking name's first letter
$arHeadlineFeedPartners = array( "z1" => array("tracking_name"=>"zacks")
																	  // Zacks.com
								);
define('ARTICLE_ITEM_ID','1');
define('DAILYFEED_ITEM_ID','18');

$rssArrayForCache=array();
$rssArrayForCache[]='featuredarticles';
$rssArrayForCache[]='featuredarticlesdailyfeed';
$rssArrayForCache[]='nasdaq';
$rssArrayForCache[]='yahoo';
$rssArrayForCache[]='yahooFull';
$rssArrayForCache[]='mvpremiumyahoo';
$rssArrayForCache[]='googlecurrents';
$rssArrayForCache[]='gravity';
?>