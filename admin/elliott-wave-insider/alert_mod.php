<?php
include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_elliottWave_config.php");

global $D_R,$elliottWaveTemplate,$elliottWaveFromName,$elliottWaveFromEmail;
if(!empty($_POST)){
	/* Publish Alerts */
	if($_POST['submit_type']== 'save'){

		$catId = implode(",",$_POST['alert']['category_id']);
		$secId = implode(",",$_POST['alert']['section_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['section_id'] = $secId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['creation_date'] = $_POST['alert']['date'];
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));

		if($_POST['id']==""){
			$id = insert_query('elliot_alert',$params);
		}else{
			$update_id = update_query('elliot_alert',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('elliot_alert',$id);
		$objContent->setElliottWaveMeta();
		if(!empty($update_id)){
			$msg = "Alert has been saved Successfully.";
		}
		$bounceback = 'alert.htm';
		location($bounceback.qsa(array(id=>$id,message=>$msg)));
	}

	if($_POST['submit_type']== 'publish')
	{
		$catId = implode(",",$_POST['alert']['category_id']);
		$secId = implode(",",$_POST['alert']['section_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['section_id'] = $secId;
		$params['is_approved'] = "1";
		$params['is_live'] = "1";
		$params['creation_date'] = $_POST['alert']['date'];
		$params['publish_date'] = $_POST['alert']['date'];
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));

		if($_POST['id']==""){
			$id = insert_query('elliot_alert',$params);
		}else{
			$update_id = update_query('elliot_alert',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('elliot_alert',$id);
		$url=$objContent->getElliottWaveAlertUrl($id);
		$objContent->updateContentSeoUrl($id,'30',$url);
		$objContent->setElliottWaveMeta();
		$qry="select is_sent,title from elliot_alert where id='".$id."'";
			$sendEmailResult=exec_query($qry,1);
			$sentEmail=$sendEmailResult['is_sent'];
			if($sentEmail=="0" || $sentEmail==""){
				update_query("elliot_alert",array(is_sent=>1),array(id=>$id));
	            $from[$elliottWaveFromEmail]= $elliottWaveFromName;
	            $subject=trim(stripslashes($sendEmailResult['title']));
	            $msgfile="/tmp/spam_elliottwave_".mrand().".eml";
	            $msghtmlfile="$D_R/assets/data/".basename($msgfile);
	            $msgurl=$elliottWaveTemplate.qsa(array(id=>$id));
	            $mailbody=inc_web($msgurl);
	            include_once("$D_R/lib/_user_controller_lib.php");
	            $userObj=new user();
	            $result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'elliottwave');
	            $error="Posts were changed and an email was sent to subscribers.";

			}
		if(!empty($id) || !empty($update_id)){
			$msg = "Alert is live now.";
		}
		$bounceback = 'approve-alert.htm?viewapproved=1';
		location($bounceback.qsa(array(message=>$msg)));
	}

	/* Delete Alerts */
	if($_POST['submit_type']== 'delete'){
		$params['is_deleted'] = '1';
		update_query('elliot_alert', $params,array(id=>$_POST['id']));
		$delParam['is_live'] = '0';
		update_query('ex_item_meta',$delParam,array(id=>$_POST['id'],item_type=>'30'));
		$bounceback = 'alert.htm';
		$bounceback = 'approve-alert.htm';
		location($bounceback.qsa(array(id=>$_POST['id'],message=>$msg)));
	}
}
?>