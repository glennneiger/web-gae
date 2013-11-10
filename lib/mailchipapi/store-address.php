<?php
/*///////////////////////////////////////////////////////////////////////
Part of the code from the book
Building Findable Websites: Web Standards, SEO, and Beyond
by Aarron Walter (aarron@buildingfindablewebsites.com)
http://buildingfindablewebsites.com
Distrbuted under Creative Commons license
http://creativecommons.org/licenses/by-sa/3.0/us/
///////////////////////////////////////////////////////////////////////*/
global $D_R;
include_once($D_R."/lib/config/_mailchimp_config.php");
include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
function storeAddress(){
	global $mailChimpApiKey,$mailChimpListId,$D_R,$fancyBoxListId;
	$objMailChimpData= new mailChimp();
	$fancyBoxName=$_GET['fancyboxName'];
	$getMailChimpData=$objMailChimpData->getMailChimpActiveList();
	if($getMailChimpData['group_name']!="" && $getMailChimpData['group_id']!="")
	{
		$merge_vars = array(
		   'GROUPINGS'=>array(
                        array('id'=>$getMailChimpData['group_id'], 'groups'=>$getMailChimpData['group_name']),
                        )
		 );
	}
	else
	{
		$merge_vars="";
	}
	//$mailChimpListId=$getMailChimpData['listid'];
	$mailChimpListId=$fancyBoxListId[$fancyBoxName];
	// Validation
	if(!$_GET['email']){ return "No email address provided"; }
	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
		return "Email address is invalid";
	}
	require_once($D_R.'/lib/mailchipapi/MCAPI.class.php');
	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api = new MCAPI($mailChimpApiKey);
	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
	$list_id = $mailChimpListId;
	if($api->listSubscribe($list_id, $_GET['email'], $merge_vars) === true) {
		// It worked!
		return 'Success! Check your email to confirm sign up.';
	}else{
		// An error ocurred, return error message
		return $api->errorMessage;
	}
}
// If being called via ajax, autorun the function
if($_GET['ajax']){ echo storeAddress(); }
?>
