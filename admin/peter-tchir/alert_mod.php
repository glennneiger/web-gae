<?php
global $D_R;
include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_peter_tchir_config.php");

global $D_R,$peterTchirTemplate,$peterTchirFromName,$peterTchirFromEmail;
if(!empty($_POST)){
	/* Publish Alerts */
	if($_POST['submit_type']== 'save'){ 
	
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['creation_date'] = $_POST['alert']['date'];
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));
		$params['position'] = $_POST['alert']['position'];
		if($_POST['id']==""){
			$id = insert_query('peter_alerts',$params);
		}else{
			$update_id = update_query('peter_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('peter_alerts',$id);
		$objContent->setPeterTchirMeta();
		$msg = "Alert has been saved Successfully.";
		$bounceback = 'alert.htm';
		location($bounceback.qsa(array(id=>$id,message=>$msg)));
	}

	if($_POST['submit_type']== 'publish')
	{
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['is_approved'] = "1";
		$params['is_live'] = "1";
		$params['creation_date'] = $_POST['alert']['date'];
		$params['publish_date'] = $_POST['alert']['date'];
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));
		$params['position'] = $_POST['alert']['position'];
		if($_POST['id']==""){
			$id = insert_query('peter_alerts',$params);
		}else{
			$update_id = update_query('peter_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('peter_alerts',$id);
		$url=$objContent->getPeterTchirAlertUrl($id);
		$objContent->updateContentSeoUrl($id,'29',$url);
		$objContent->setPeterTchirMeta();
		$qry="select is_sent,title from peter_alerts where id='".$id."'";
			$sendEmailResult=exec_query($qry,1);
			$sentEmail=$sendEmailResult['is_sent'];
			if($sentEmail=="0" || $sentEmail==""){
				update_query("peter_alerts",array(is_sent=>1),array(id=>$id));
	            $from[$peterTchirFromEmail]= $peterTchirFromName;
	            $subject=trim(stripslashes($sendEmailResult['title']));
	            $msgfile="/tmp/spam_petertchir_".mrand().".eml";
	            $msghtmlfile="$D_R/assets/data/".basename($msgfile);
	            $msgurl=$peterTchirTemplate.qsa(array(id=>$id));
	            $mailbody=inc_web($msgurl);
	            include_once("$D_R/lib/_user_controller_lib.php");
	            $userObj=new user();
	            $result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'peterTchir');
	            $error="Posts were changed and an email was sent to subscribers.";

			}
		if(!empty($update_id) || !empty($id)){
			$msg = "Alert is live now.";
		}
		$bounceback = 'approve-alert.htm?viewapproved=1';
		location($bounceback.qsa(array(message=>$msg)));
	}

	/* Delete Alerts */
	if($_POST['submit_type']== 'delete'){
		$params['is_deleted'] = '1';
		update_query('peter_alerts', $params,array(id=>$_POST['id']));
		$bounceback = 'approve-alert.htm?viewapproved=1';
		$msg = "Article has been deleted";
		location($bounceback.qsa(array(id=>$_POST['id'],message=>$msg)));
	}
}
?>