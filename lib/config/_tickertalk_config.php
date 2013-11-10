<?php
/******* Ticker talk Configuration Start**********************/
global $HTDOMAIN;
$tickerchatcharlimit="180";
$autoFillCount="4";
$toTtReportAbuse="Minyanville <moderator@minyanville.com>";
$fromTtReportAbuse="Minyanville <support@minyanville.com>";
$subjectTtReportAbuse="Report Abuse Request";
$tickerTalkReportAbuseTemplate="$HTDOMAIN/emails/_eml_ticker_report_abuse_alert.htm";
$arMVListedTicker=array('1'=>'GOOG','2'=>'GE','3'=>'C');

$viaMaintainenceMsg = 'We are under maintenance, will be back shortly';
$tickerChatMemcacheLimit = 100;
$toFeedBack="Minyanville <feedback@minyanville.com>";
$subjectFeedBack="sent a feedback";
$feedBackTemplate="$HTDOMAIN/emails/_eml_ticker_feedback.htm";
// S5 Server Settings for Production
$tickerFTPServer = "10.0.0.9";
$tickerFTPUser = "tickertalk";
$tickerFTPPassword = ")%<1d#I";

$fromTtreply="Minyanville <subscriptions@minyanville.com>";
$subjectTtreply="Sent a reply on post";
$tickerTalkReplyTemplate="$HTDOMAIN/emails/_eml_ticker_reply.htm";
/******* Ticker talk Configuration Ends**********************/
?>