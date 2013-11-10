<?php

define('ga_email','analytics@minyanville.com');
define('ga_password','al3WI6G8');
define('ga_profile_id','25995847');

$gaDimensions     = array('pagePath','pageTitle');
$gaMetrics        = array('pageviews','uniquePageviews');    
$gaTermFrom       = date("Y-m-d",strtotime(mysqlNow($date)." -1 day"));
$gaTermUntil      = date("Y-m-d",strtotime(mysqlNow($date)));
$gaArticleFilter = 'ga:pagepath=~/dailyfeed/ || ga:pagepath=~/trading-and-investing/ || ga:pagepath=~/businessmarkets/ || ga:pagepath=~/business-news/ || ga:pagepath=~/sectors/ || ga:pagepath=~/special-features/';
$gaArticleSectionFilter = 'ga:pagepath=~/trading-and-investing/ || ga:pagepath=~/businessmarkets/ || ga:pagepath=~/business-news/ || ga:pagepath=~/sectors/ || ga:pagepath=~/special-features/';
$gaDailyFeedFilter = 'ga:pagepath=~/dailyfeed/';
$gaSort           = '-pageviews'; // Sorted by desc. pageview count
$gaArticleMaxResults     = 5; // First n entries
$gaDailyFeedMaxResults     = 3; // First n entries
$gaArticleSectionMaxResults=3;

?>