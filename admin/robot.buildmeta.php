<?php
set_time_limit(60*5);//1 hour
global $D_R;
include_once("$D_R/lib/_content_data_lib.php");
include_once("../lib/layout_functions.php");

//Build Article Meta Data
$objArticle = new Content('Articles');
$maxObjectID = $objArticle->getMaxMetaObjectID();
$objArticle->setMetaData($maxObjectID);
unset($objArticle);
unset($maxObjectID);

//Build Active Investor Meta Data
$objFlexArticle = new Content('Active Investor');
$maxObjectID = $objFlexArticle->getMaxMetaObjectID();
$objFlexArticle->setMetaData($maxObjectID);
unset($objFlexArticle);
unset($maxObjectID);
/*
//Build Flexfolio Meta Data
$objFlexArticle = new Content('Flexfolio');
$maxObjectID = $objFlexArticle->getMaxMetaObjectID();
$objFlexArticle->setMetaData($maxObjectID);
unset($objFlexArticle);
unset($maxObjectID);
*/
//Build Option Smith Meta Data
$objOptionSmith = new Content('OptionSmith');
$maxObjectID = $objOptionSmith->getMaxMetaObjectID();
$objOptionSmith->setMetaData($maxObjectID);
unset($objOptionSmith);
unset($maxObjectID);

/*
//Build Discussion Meta Data
$objDiscussion = new Content('Discussions');
$maxObjectID = $objDiscussion->getMaxMetaObjectID();
$objDiscussion->setMetaData($maxObjectID);
unset($objDiscussion);
unset($maxObjectID);


//Build Blog Meta Data
$objBlog = new Content('Blogs');
$maxObjectID = $objBlog->getMaxMetaObjectID();
$objBlog->setMetaData($maxObjectID);
unset($objBlog);
unset($maxObjectID);
*/

//Build Buzz & Banter Meta Data
$objBuzz = new Content('Buzz Banter');
$maxObjectID = $objBuzz->getMaxMetaObjectID();
$objBuzz->setMetaData($maxObjectID);
unset($objBuzz);
unset($maxObjectID);
/*
//Build Videos Meta Data
$objVideos = new Content('Videos');
$maxObjectID = $objVideos->getMaxMetaObjectID();
$objVideos->setMetaData($maxObjectID);
unset($objVideos);
unset($maxObjectID);
*/
//Build Cooper Meta Data
$objCooper = new Content('Cooper');
$maxObjectID = $objCooper->getMaxMetaObjectID();
$objCooper->setMetaData($maxObjectID);
unset($objCooper);
unset($maxObjectID);

//Build Profile Meta Data
$objProfile = new Content('Profile');
$maxObjectID = $objProfile->getMaxMetaObjectID();
$objProfile->setMetaData($maxObjectID);
unset($objProfile);
unset($maxObjectID);
/*
//Build Option Smith Meta Data
$objBMTP = new Content('BMTP');
$maxObjectID = $objBMTP->getMaxMetaObjectID();
$objBMTP->setMetaData($maxObjectID);
unset($objBMTP);
unset($maxObjectID);


//Build Jack News Meta Data
$objjacknews = new Content('Jack NewsLetter');
$maxObjectID = $objjacknews->getMaxMetaObjectID();
$objjacknews->setMetaData($maxObjectID);
unset($objjacknews);
unset($maxObjectID);
*/

//Build Daily Feed Meta Data
$objdailyfeed = new Content('Feeds');
$maxObjectID = $objdailyfeed->getMaxMetaObjectID();
$objdailyfeed->setMetaData($maxObjectID);
unset($objdailyfeed);
unset($maxObjectID);
/*
//Build ETF Trader Meta Data
$objETFTrader = new Content('ETF Trader');
$maxObjectID = $objETFTrader->getMaxMetaObjectID();
$objETFTrader->setMetaData($maxObjectID);
unset($objdailyfeed);
unset($maxObjectID);

//Build Slideshow Meta Data
$objSlideshow = new Content('Slideshow');
$maxObjectID = $objSlideshow->getMaxMetaObjectID();
$objSlideshow->setMetaData($maxObjectID);

unset($objArticle);
unset($maxObjectID);
*/
?>