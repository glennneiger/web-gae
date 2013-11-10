<?php
/**
 * Script for validating and authorizing request coming from Zepinvest site
 * 
 * 1) User will be clicking the link available at Zepinvest site. The format of 
 *    the URL will be - http://www.minyanville.com/buzz/buzz_zepinvest.php?u=88ajjauIaka&i=jaka8akakIkkaiOo
 * 
 *    Parameter details:
 *    u = DES encrypted Zepinvest user UserID
 *    i = DES encrypted timestamp. The format of timestamp will of format ISO 8601
 * 2) User record will be validated with Zepinvest site.
 * 3) If details get validated, session will be initiated.
 * 
 * 
 */

///////CONSTANTS///////
$ZEPINVEST_AUTH_URL = "http://localhost/buzz_zepinvest/validation.php"; //ZepInvest URL which will be used for validating request
$URL_VALIDITY = "2400"; //Time duration in minutes for which URL will be consider valid
$KEY = "Minyanville.com"; //Secret key

//On initialization script will call validateRequest() function
validateRequest();


/**
 * Validating all the ZepInvest incoming request
 * 
 * @param URL
 * 
 * @author Maverick
 *
 */
function validateRequest()
{
	global $ZEPINVEST_AUTH_URL, $KEY;
	$userID 	= $_GET['u'];
	$timestamp 	= $_GET['i'];
	
	$userID 	= urlencode(mcrypt_ecb (MCRYPT_DES, $key, $userID, MCRYPT_ENCRYPT));
	$timestamp 	= urlencode(mcrypt_ecb (MCRYPT_DES, $key, $timestamp, MCRYPT_ENCRYPT));
	
	//Initiate CURL connection
	$ch = curl_init($ZEPINVEST_AUTH_URL);
	curl_setopt($ch,CURLOPT_HTTPGET,1);
	
	$result = curl_exec($ch);
	
	//Close CURL connection
	curl_close();
	
	if(strcmp(strtolower($result),"<response><isvalid>true</isvalid></response>") == 0) //If user authenticated
	{
		initiateSession($userID);
	} else
	{
		displayErrorMsg("Authentication Failed.");
	}
	print($result);
	//Close CURL connection

	
	
		
}

/**
 * Initiate user session 
 * 
 * 
 * @author Maverick
 *
 * @param $userID Zepinvest User unique ID
 * 
 */
function initiateSession($userID)
{
	//Initialize the session
	session_start();
	
	//Set session info
	set_sess_vars('userID',$userID);
}

/**
 * Render error messages
 *
 * @author Maverick
 * 
 * @param $msg Error Message
 */
function displayErrorMsg($msg)
{
	
	echo "<center><h3>".$msg."</h3></center>";
}
?>