<?php

/*
 *  PeerRemote class
 *  NuSOAP PHP class for interfacing with
 *  Peersuasion Remote (http://peersuasion.com)
 *
 * (C) 2005 Mass Transmit, LLC
 *
 * Requires NuSOAP (http://sourceforge.net/projects/nusoap)
 */

require_once(dirname(__FILE__)."/nusoap.php");

/* Constants */
define('WSDL', 'https://app.peersuasion.com/remote/soap.php?wsdl');

class PeerRemote {

  var $CampaignId  = null;
  var $ServicesToken = null;

  function PeerRemote($campaign_id, $services_token)
  {
  
    $this->CampaignId = 3; 					//$campaign_id;
    $this->ServicesToken = 'bull$&b34r$';   //$services_token;
    $this->Client = new soapclient(WSDL, true);
  }
  
  function _emptyRequest() {
    return array(
      'campaign_id'=>$this->CampaignId,
      'services_token'=>$this->ServicesToken
    );
  }


  function addUser($address) {
	/* $address = array(
          'email' => $email,
          'lastname' => $lastname,
          'firstname' => $firstname
        );
	*/
    $request = $this->_emptyRequest();
    $request['address'] = $address;
    return $this->Client->call("addUser", array("request"=>$request));

  }


  function checkReferralCode($referral_code) {

 	$errors=array(
		"REFERRAL_CODE_USED"=>"checkReferralCode: This referral code has already been used",
		"REFERRAL_CODE_INVALID"=>"checkReferralCode: Invalid referral code",
		"REFERRAL_ADDRESS_INVALID"=>"checkReferralCode: Referred user does not exist in the system",
		"REFERRAL_ADDRESS_REGISTERED"=>"checkReferralCode: Referred user has already registered",
		"REFERRAL_CODE_FAILURE"=>"checkReferralCode: Referral code was not a valid Peersuasion Remote code: $referral_code"
	);
    $request = $this->_emptyRequest();
    $request['referral_code'] = $referral_code;
    $res=$this->Client->call("checkReferralCode", array("request"=>$request));
	if(is_array($res)){
		if(isset($res["faultstring"])){
			if($errors[$res["faultstring"]])
				$res["errormsg"]=$errors[$res["faultstring"]];
			else
				$res["errormsg"]=$res["faultstring"];
		}
	}
	return $res;
  }

  function completeReferral($referral_token, $address = null) {

  	$errors=array(
		"REFERRAL_TOKEN_INVALID"=>"completeReferral: Referral token is not a valid Peersuasion Remote token",
		"REFERRAL_TOKEN_EXPIRED"=>"completeReferral: Referral token is older than 30 minutes",
		"REFERRAL_TOKEN_USED"=>"completeReferral: Referral code has already been used",
		"REFERRAL_ADDRESS_INVALID"=>"completeReferral: Referred user does not exist in the system",
		"REFERRAL_ADDRESS_REGISTERED"=>"completeReferral: Referred user has already registered",
		"NEW_EMAIL_EXISTS"=>"completeReferral: New email given for user already exists in system",
	);
	$request = $this->_emptyRequest();
    $request['referral_token'] = $referral_token;
    if ($address != null) $request['address'] = $address;
    $res=$this->Client->call("completeReferral", array("request"=>$request));
	if(isset($res[faultstring])){
		if($errors[$res[faultstring]])
			$res[errormsg]=$errors[$res[faultstring]];
		else
			$res[errormsg]=$res[faultstring];
	}

	return $res;
  }

  function getAPIVersion() {
  
    return $this->Client->call("getAPIVersion");
  
  }
  
  function getConversionCounts($days = 0) {
        
	$request = $this->_emptyRequest();
    $request['days'] = $days;
    return $this->Client->call("getConversionCounts", array("request"=>$request));

  }

  function sendReferral($referral_token, $from, $to, $message = null) {

    $request = $this->_emptyRequest();
    $request['to'] = $to;
    $request['from'] = $from;
    if ($message!= null) $request['message'] = $message;
    return $this->Client->call("sendReferralRequest", array("request"=>$request));

  }

  function userExists($email) {
  
    $request = $this->_emptyRequest();
    $request['email'] = $email;
    return $this->Client->call("userExists", array("request"=>$request));

  }
  
	function getReferrer($email) {
	  	$errors=array(
			"NO_COMPLETE_REFERRAL"=>"getReferrer: User has not completed a referral"
		);
		$request = $this->_emptyRequest();
		$request['email'] = $email;
		$res= $this->Client->call("getReferrer", array("request"=>$request));
		if(isset($res[faultstring])){
			if($errors[$res[faultstring]])
				$res[errormsg]=$errors[$res[faultstring]];
			else
				$res[errormsg]=$res[faultstring];
		}
		return $res;
	}

}
?>