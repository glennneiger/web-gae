<?php

//receive submitted form from preferences object on client
if ($_POST['sid']) {
	$sid = $_POST['sid'];
	$characters = $_POST['characters'];
	$alert = $_POST['alerts'];
	$window_size = $_POST['window_size'];
	$text_size = $_POST['text_size'];
	$auto_jump = $_POST['auto_jump'];
	$posts_per_page = $_POST['posts_per_page'];
	$cidString = explode(":",$_POST['cidString']);
	$shString = explode(":",$_POST['shString']);
}
//echo $_POST['cidString'];
//echo $_POST['shString'];
//echo $cidString[0];
//echo $shString[0];
//echo $cidString[1];
//echo $shString[1];

if(!is_array($prefs))$prefs=array();

$prefs[characters]=$characters;
$prefs[alert]=$alert;
$prefs[auto_jump]=$auto_jump;
$prefs[window_size]=$window_size;
$prefs[text_size]=$text_size;
$prefs[posts_per_page]=$posts_per_page;

insert_or_update("subscriber_buzzbanter_preferences", $prefs , array(ssid=>$sid) );

del_query(subscriber_contributor_filter, "subscriber_id", $sid);

//foreach teacher in form

	if($cidString && $shString){
		for ($i=0;$i<count($cidString);$i++) {
		//if teacher value is on, run insert query
			if ($shString[$i]=="0"){
				$subscriber_contributor_filter['contrib_id']=$cidString[$i];
				$subscriber_contributor_filter['subscriber_id']=$sid;
				insert_query("subscriber_contributor_filter", $subscriber_contributor_filter);
			}
		}
}
?>