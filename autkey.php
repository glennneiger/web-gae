<?php
global $D_R,$HTPFX,$HTHOST;
/*
 * Copyright 2012 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once "$D_R/google-api-php-client/src/Google_Client.php";


// Set your client id, service account name, and the path to your private key.
// For more information about obtaining these keys, visit:
// https://developers.google.com/console/help/#service_accounts
const CLIENT_ID = '949072678711-fm6eol1qrmgb0biinntv438f4c8sdnit.apps.googleusercontent.com';
const SERVICE_ACCOUNT_NAME =  '949072678711-fm6eol1qrmgb0biinntv438f4c8sdnit@developer.gserviceaccount.com';

// Make sure you keep your key.p12 file in a secure location, and isn't
// readable by others.
//const KEY_FILE = 'gs://mvassets/3d883f39abc5141fc7685eca141a7adb67f70766-privatekey.p12';
const KEY_FILE = '3d883f39abc5141fc7685eca141a7adb67f70766-privatekey.p12';
$client = new Google_Client();
//$client->setApplicationName("Google Prediction Sample");

// Set your cached access token. Remember to replace $_SESSION with a
// real database or memcached.
//session_start();


 //$client->setAccessToken($_SESSION['token']);


// Load the key in PKCS 12 format (you need to download this from the
// Google API Console when the service account was created.


$key = file_get_contents($HTPFX.$HTHOST."/".KEY_FILE);
htmlprint_r($key);
die;

$client->setClientId(CLIENT_ID);
//$client->setAssertionCredentials(new Google_AssertionCredentials(
    SERVICE_ACCOUNT_NAME,
    array('https://www.googleapis.com/auth/devstorage.full_control'),
    $key)
);
$service['scope'] =  'https://www.googleapis.com/auth/devstorage.full_control';
echo $client->authenticate();
echo "--------";
echo $client->getAccessToken();
htmlprint_r($_SESSION);
echo "dhinka chik,x x,a ";
//htmlprint_r($client->generateAssertion(generateAssertion()));
//$client->setAccessToken(json_encode('asdfasfkkljlkasfaklfjasfk'));


//$result = $service->hostedmodels->predict($id, $input);
//print '<h2>Prediction Result:</h2><pre>' . print_r($result, true) . '</pre>';

// We're not done yet. Remember to update the cached access token.
// Remember to replace $_SESSION with a real database or memcached.
