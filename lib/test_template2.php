<?php
global $D_R;
include_once($D_R."/lib/config/_mailchimp_config.php");
include_once($D_R."/lib/mailchipapi/MCAPI.class.php");
include_once($D_R."/lib/mailchipapi/mailchimp_data_lib.php");
require_once($D_R.'/lib/mailchipapi/store-address.php');
$objMailChimp= new mailChimp();

$qry="SELECT `id`,`subject` FROM `daily_digest` WHERE id='15'";
$result = exec_query($qry,1);

$url="http://admin.minyanville.com/admin/email-digest/send_digest.php?id=333&mail=1";
$mailbody=inc_web($url);
$objApi = new MCAPI($mailChimpApiKey);

/********** Create a template **********/
/* $types = array('user'=>true, 'gallery'=>true);
$tem = $objApi->templateAdd('Article Template', 'dgsgdsgds') ;
htmlprint_r($tem); */

/********** list of template **********/
/* $types = array('user'=>true, 'gallery'=>true);
$tem = $objApi->templates($types);
htmlprint_r($tem); */

/********** members in a list **********/
/*
$tem = $objApi->listMembers('ecf2df01e7');
htmlprint_r($tem); */



/********** Update a template **********/
/* $values = array("html"=>$mailbody);
$objApi->templateUpdate("9225", $values); */

/********** Get all Lists **********/
/* $arLists = $objApi->lists();
htmlprint_r($arLists);  */

/******** Get all Groups in a List   ******************/
/*
$res = $objApi->listInterestGroupings("38d7c14aa9");
htmlprint_r($res);  
*/
/********** Update a campaign **********/
/* $objApi->campaignUpdate("435bee9d0b", "auto_footer", false); */
/*$frr =$objApi->campaignUpdate("2ff7bd850f", "name", "dfsdfsdfsdfddsfsdfsdfsdf");
$r =$objApi->campaignUpdate("2ff7bd850f", "subject", "nidhasifd f");
htmlprint_r($r);
htmlprint_r($frr);*/

/********** Get all Campaigns **********/
 /*$resCampaign = $objApi->campaigns();
htmlprint_r($resCampaign);*/

/********** Subscribe batch of users in the list **********/
/*$optin = false; //yes, send optin emails
$up_exist = true; // yes, update currently subscribed users
$replace_int = true; // no, add interest, don't replace
$batch[] = array('EMAIL'=>'nidhi.singh@mediaagility.co.in', 'FNAME'=>'Nidhi','LNAME'=>'Singh');
$batch[] = array('EMAIL'=>'nsyr0801@gmail.com',  'FNAME'=>'Nidhi','LNAME'=>'Singh');
$batch[] = array('EMAIL'=>'budhiraja.anshul@gmail.com', 'FNAME'=>'Anshul', 'LNAME'=>'Budhiraja');
$res = $objApi->listBatchSubscribe("9fabeaa689",$batch,$optin, $up_exist, $replace_int);
htmlprint_r($res); */

/********** Un-subscribe batch of users in the list **********/

/*$emails = array('nidhi.singh@mediaagility.co.in', 'nsyr0801@gmail.com');
$res = $objApi->listBatchUnsubscribe("9fabeaa689", $emails, false, false,false);
htmlprint_r($res); */

/********** update user in list *********/
/*$merge_vars = array("FNAME"=>'nidhi', "LNAME"=>'Singh');
$retval = $objApi->listUpdateMember('9fabeaa689', 'nsyr0801@gmail.com', $merge_vars, 'html', true);
$retval = $objApi->listUpdateMember('9fabeaa689', 'nidhi.singh@mediaagility.co.in', $merge_vars, 'html', true);
htmlprint_r($retval);
htmlprint_r($objApi->errorCode); */



/********** Send Campaign *********/
/*$resSend = $objApi->campaignSendNow('4f75da5c56');
htmlprint_r($resSend);*/

/*$res = $objApi->listUnsubscribe("9fabeaa689", 'nidhgi.singh@mediaagility.co.in', false,false , false);
htmlprint_r($res);*/

/******************** Subscribe a user in List   *************************/


/*$merge_vars = array("FNAME"=>'nidhi', "LNAME"=>'Singh',
                             'INTERESTS'=>'Fixed Income');
$resSend = $objApi->listSubscribe("38d7c14aa9", 'nidhi.singh@mediaagility.com', $merge_vars, 'html', false, true, false, false);
htmlprint_r($resSend); */

/*
$resSend = $objApi->listMemberInfo("38d7c14aa9", 'nitin.gupta@mediaagility.com');
htmlprint_r($resSend);
*/

/*
$merge_vars = array("FNAME"=>'nidhi', "LNAME"=>'Singh',
                             'INTERESTS'=>'Global Markets');
$resSend = $objApi->listSubscribe("38d7c14aa9", 'nidhi.singh@mediaagility.com', $merge_vars, 'html', false, true, true, false);
htmlprint_r($resSend); */

/**************** Create a segment Test ***************************/ 
/*
 $conditions = array();
$conditions[] = array('field'=>'interests', 'op'=>'one', 'value'=>'Trading Radar,RANDOM THOUGHTS');
$opts = array('match'=>'any', 'conditions'=>$conditions);
$res = $objApi->campaignSegmentTest("38d7c14aa9", $opts);
htmlprint_r($res); 

*/


/********* Get List of merge fields in a list ***************************/
/*
$res = $objApi->listMergeVars("38d7c14aa9");
htmlprint_r($res); */

/********** Create a campaign **********/
/*
 $conditions = array();
$conditions[] = array('field'=>'interests', 'op'=>'one', 'value'=>'Trading Radar,RANDOM THOUGHTS');

$segment_opts = array('match'=>'any', 'conditions'=>$conditions);
 $options = array('list_id'=>'38d7c14aa9','subject'=>'nidhi & bh $ email','from_email'=>'support@minyanville.com',
 'from_name'=>'Minyanville','to_name'=>'nidhi','template_id'=>'9225');
$content = array('html'=>$mailbody,'text'=>'ddsads');
$res = $objApi->campaignCreate("regular", $options, $content, $segment_opts, $type_opts=NULL);
htmlprint_r($res);  
*/
/****************** Add Interest Groups **************************/
/*
 $resSend = $objApi->listInterestGroupAdd('38d7c14aa9','Trading Radar','1669');
htmlprint_r($resSend); 
*/
 




















?>