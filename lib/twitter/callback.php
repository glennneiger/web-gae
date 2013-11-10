<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
global $HTPFX,$HTHOST,$D_R;
include_once($D_R.'/lib/twitter/twitteroauth/twitteroauth.php');
include_once($D_R.'/lib/twitter/config.php');
build_lang("tickertalk");
/* If the oauth_token is old redirect to the connect page. */

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: "'.$HTPFX.$HTHOST.'"/lib/twitter/clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  $getfollowers=$connection->getTwitterFollowers();
  if($getfollowers){
    // $message="I just joined ticker talk on minyanville.com.Check this out in www.minyanville.com";
	 $message=$lang['directmsgtwitter'];
  	 $connection->sendTwitterDirectMessage($getfollowers,$message);
  }
	//header('Location: http://www.qa.minyanville.com/index.htm'); 
	location($HTPFX.$HTHOST.'/index.htm'); 
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: "'.$HTPFX.$HTHOST.'"/lib/twitter/clearsessions.php');
}
