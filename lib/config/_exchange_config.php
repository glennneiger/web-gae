<?php
global $lang, $EX_INVALIDLOGIN, $HTPFX, $HTHOST, $page_config,$D_R,$HTADMINHOST ;
$SUGGESTIONTICKERTALKJSSCRIPT=$HTPFX.$HTHOST."/assets/tickertalk/js/suggestion_tt.js";
$SUGGESTIONJSSCRIPT=$HTPFX.$HTHOST."/js/suggestion.js";
$STOCKSUGGESTIONJSSCRIPT=$HTPFX.$HTHOST."/js/stock_suggestion.js";
$LATESTARTICLEJSSCRIPT= $HTPFX.$HTHOST."/assets/data/latestArticle.txt";
$SUBSCRIBEDBLOGSJSSCRIPT= $HTPFX.$HTHOST."/assets/data/subscribedBlogs.txt";
$timeinterval="30 MINUTE";
$ACTBJSSCRIPT=$HTPFX.$HTHOST."/js/actb.js";
$STOCKACTBJSSCRIPT=$HTPFX.$HTHOST."/js/stock_actb.js";
$ACTB_COMMONJSSCRIPT=$HTPFX.$HTHOST."/js/actb_common.js";
$TEASURE_COUNT=250;
$relatedExchangeDuration="14 day";
$relatedExchangeOffset = 0;
$relatedExchangeLimit = 5;
$DATETIMELIMIT['INTERVAL']='7';
$DATETIMELIMIT['UNIT']='DAY';
$blogOffset=0;
$blogLimit=3;
$SHOW_RECNT_BLOG=10;
$defaultSudId=12757;
$page_config['exchange_search']['URL']=$HTPFX.$HTHOST."/community/search.htm";
$page_config['single_discussion']['URL']=$HTPFX.$HTHOST."/community/Discussion.htm";
$page_config['main_discussion']['URL']=$HTPFX.$HTHOST."/community/discussion-home.htm";
$page_config['profile']['URL']=$HTPFX.$HTHOST."/community/profile/index.htm";
$page_config['blog_comment']['URL']=$HTPFX.$HTHOST."/community/blog_comments.htm";
$page_config['blog_entry']['URL']=$HTPFX.$HTHOST."/community/blog_entry.htm";
$page_config['compose_message']['URL']=$HTPFX.$HTHOST."/community/compose.htm";
$page_config['login']['URL']=$HTPFXSSL.$HTHOST."/subscription/register/login.htm";
$page_config['welcome']['URL']=$HTPFX.$HTHOST."/community/register/welcome.htm";
$page_config['home']['URL']=$HTPFX.$HTHOST."/community/home.htm";
$page_config['friends']['URL']=$HTPFX.$HTHOST."/community/friends.htm";
$page_config['community_save']['URL']=$HTPFX.$HTHOST."/community/Save.php";
$page_config['blog']['URL']="http://blogs.minyanville.com/";
$page_config['inbox']['URL']=$HTPFX.$HTHOST."/community/inbox.htm";
$page_config['compose']['URL']=$HTPFX.$HTHOST."/community/compose.htm";
$page_config['article']['URL']=$HTPFX.$HTHOST."/articles/index.php";
$page_config['search']['URL']=$HTPFX.$HTHOST."/community/search.htm";
$page_config['privacy']['URL']=$HTPFX.$HTHOST."/community/profile/privacy.htm";
$page_config['register_manage']['URL']=$HTPFXSSL.$HTHOST."/subscription/register/";
$page_config['register_new']['URL']=$HTPFXSSL.$HTHOST."/subscription/register/";
$page_config['buzz']['URL']=$HTPFX.$HTHOST."/buzz/print.php";
$page_config['friends']['URL']=$HTPFX.$HTHOST."/community/friends.htm";
$page_config['emailsetting']['URL']=$HTPFXSSL.$HTHOST."/subscription/register/controlPanel.htm";
$page_config['flexfolio']['URL']=$HTPFX.$HTHOST."/qp/";
$page_config['cooper']['URL']=$HTPFX.$HTHOST."/cooper/";
$page_config['buzzbanter']['URL']=$HTPFX.$HTHOST."/buzz/";

$EX_INVALIDLOGIN="<tr><td>Invalid login.</td></tr>";
$ColumnUserName = 'Name';
$DATETIMELIMIT['INTERVAL']='7';
$DATETIMELIMIT['UNIT']='DAY';
$profileMatchingFields[]='Occupation';
$profileMatchingFields[]='General Interests';
$profileMatchingFields[]='Investment Interests';
$profileMatchingFields[]='Company';
$profileMatchingFields[]='Previous Companies';

//Email Settings(ex_email_template)
$ColumnEmailSetting['friendRequest'] = 'On';
$ColumnEmailSetting['ReportAbuse'] = 'On';
$ColumnEmailSetting['NewMessage'] = 'On';
$ColumnEmailSetting['ReplyDirectPost'] = 'On';
$ColumnEmailSetting['NewArticlesRelatedWatchlist'] = 'On';
$ColumnEmailSetting['SubscribedBlogsAlert'] = 'Off';

//Privacy Setting(ex_profile_privacy)
$ColumnPrivacySetting['Profileinformation'] = 'All';
$ColumnPrivacySetting['Workinformation'] = 'All';
$ColumnPrivacySetting['Investinginformation'] = 'All';
$ColumnPrivacySetting['Generalinformation'] = 'All';
$ColumnPrivacySetting['Messagesfrom'] = 'All';
$ColumnPrivacySetting['Friendrequestsfrom'] = 'All';
$ColumnPrivacySetting['Subscriptionsfrom'] = 'All';
$MODERATOR_EMAIL="Minyanville Exchange <support@minyanville.com>";
$EXNG_PAGE_MSG_COUNT=5; // count for diplay no. of message per page
$EXNG_CHAR_COUNT=80;    // count for diplay no. of characters in a message
$EXNG_FRIENDS_COUNT=5; //count to display no. of friends.
$EXNG_PAGE_COUNT=25;//count of number of records per page on discussion
$frndrequestlmt=10;
$artclelmt=10;
$bloglmt=10;
$newrecmndeddiscusionlmt=10;
$newreplyonartlmt=10;
$newreplyonbloglmt=10;
$suggestedartlmt=10;
$peopleexchnglmt=10;
//Added for profile
$wordwrapdefault=10;
$profilenavlmt=20;//this is for edit watchlist and friendlist navigation limit
$friendnavlmt=10;//this is for friend record end limit
$watchlistnavlmt=10;//this is for watchlist record end limit
$watchlistoffsetlmt=0;//this is for start offset limit of watchlist
$friendoffsetlmt=0;//this is for start offset limit of friend
$watchlistwordlimit=15;
//
$character_images=array('boo'=>'boo.gif',
						'hoofy'=>'hoofy.gif','daisy'=>'daisy.gif','sammy'=>'sammy.gif','snapper'=>'snapper.gif');
$blogsubscrcnt=5;// this is for blogs you follows in profile page

//QPM
$SPAM_EML_SINGLE_ALERT_TMPL=$HTPFX.$HTADMINHOST."/emails/_eml_single_alert.htm";
// for email alert
$SPAM_EML_SUBS_ALERT_TMPL=$HTPFX.$HTADMINHOST."/emails/_eml_email_alert.htm";
// size of bulk mailer for email alert
$size_emailalert=30;
//Added for visitor count in exchange purpose
$pageNamearr=array('profile','single_discussion','main_discussion','exchange_search','ibox_registration','friends','home','compose','register','exchange_subscribers','exchange_registration','blog','blog_entry','blog_comments','inbox','community','exchange_privacy','emailsetting');
// Authors not show for email alert
$authornotstr="81,52,73,68,71,75,29,102,5,101,83,9,42,72,40";
// code for random image display for article page for email alert
$exchange_email_images=array('emailalert'=>'email_alert/emailalerts.gif',
						'discuss'=>'community_images/Click-Here-to-Discuss.gif');

// code for random display for home page for email alert
$home_emailalert_swf=array('email_alert'=>'email_alert',
						'home_mvtv'=>'home_mvtv');


?>
