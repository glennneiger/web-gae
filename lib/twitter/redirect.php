<?php

/* Start session and load lib */
session_start();
global $HTPFX,$HTHOST,$D_R;
include_once($D_R.'/lib/twitter/twitteroauth/twitteroauth.php');
include_once($D_R.'/lib/twitter/config.php');

/* Create TwitterOAuth object and get request token */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

/* Get request token */
$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

/* Save request token to session */
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

/* If last connection fails don't display authorization link */
switch ($connection->http_code) {
  case 200:
    /* Build authorize URL */
    $url = $connection->getAuthorizeURL($token);
    header('Location: ' . $url); 
    break;
  default:
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
    break;
}
