<?php
include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_edu_config.php");
global $D_R, $eduItemMeta, $VIDEO_SERVER,$bucketPath;

if(!empty($_POST)){
	/* Publish Alerts */
	if(!empty($_FILES['eduImgFile']['name'])){
		$eduImgPath="/assets/edu/images/";
		$eduImgName = rand().'-'.str_replace(" ","_",$_FILES['eduImgFile']['name']);
		move_uploaded_file($_FILES["eduImgFile"]["tmp_name"], $bucketPath.$eduImgPath.$eduImgName);
	}
	if(!empty($_FILES['eduVidFile']['name'])){
		$eduImgPath="/assets/edu/video/";
		$eduImgName = rand().'-'.str_replace(" ","_",$_FILES['eduImgFile']['name']);
		move_uploaded_file($_FILES["eduImgFile"]["tmp_name"], $bucketPath.$eduImgPath.$eduImgName);
	}
	if($_POST['submit_type']== 'save'){
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['creation_date'] = $_POST['alert']['date'];
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));
		if(!empty($eduImgName)){
			$params['edu_img'] = $eduImgName;
		}
		$eduVideoFile = $_POST['alert']['eduVideo'];
		if(!empty($eduVideoFile)){
			$params['is_video'] = '1';
			$params['eduVideo'] = $VIDEO_SERVER."assets/edu/video/".$eduVideoFile;
		}else{
			$params['is_video'] = '0';
			$params['eduVideo'] = '';
		}
		$params['layoutType'] = $_POST['alert']['layoutType'];
		if($_POST['id']==""){
			$id = insert_query('edu_alerts',$params);
		}else{
			$update_id = update_query('edu_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('edu_alerts',$id);
		$objContent->setEduMeta();
		if(!empty($update_id)){
			$msg = "Alert has been saved Successfully.";
		}
		$bounceback = 'edu-create.htm';
		location($bounceback.qsa(array(id=>$id,message=>$msg)));
	}

	if($_POST['submit_type']== 'publish')
	{
		$catId = implode(",",$_POST['alert']['category_id']);
		$params['id'] = $_POST['id'];
		$params['category_id'] = $catId;
		$params['is_approved'] = "1";
		$params['is_live'] = "1";
		$params['creation_date'] = $_POST['alert']['date'];
		$params['publish_date'] = $_POST['alert']['date'];
		$params['contrib_id'] = $_POST['alert']['contributor_id'];
		$params['title'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['title'])));
		$params['body'] = addslashes(mswordReplaceSpecialChars(stripslashes($_POST['alert']['body'])));
		if(!empty($eduImgName)){
			$params['edu_img'] = $eduImgName;
		}
		$eduVideoFile = $_POST['alert']['eduVideo'];
		if(!empty($eduVideoFile)){
			$params['is_video'] = '1';
			$params['eduVideo'] = $VIDEO_SERVER."assets/edu/video/".$eduVideoFile;
		}else{
			$params['is_video'] = '0';
			$params['eduVideo'] = '';
		}
		$params['layoutType'] = $_POST['alert']['layoutType'];
		if($_POST['id']==""){
			$id = insert_query('edu_alerts',$params);
		}else{
			$update_id = update_query('edu_alerts',$params,array(id=>$_POST['id']));
			$id=$_POST['id'];
		}
		$objContent = new Content('edu_alerts',$id);
		$url=$objContent->getEduAlertUrl($id);
		$objContent->updateContentSeoUrl($id,$eduItemMeta,$url);
		$objContent->setEduMeta();
		$qry="select is_sent,title from edu_alerts where id='".$id."'";
			$sendEmailResult=exec_query($qry,1);
			$sentEmail=$sendEmailResult['is_sent'];
			/*if($sentEmail=="0" || $sentEmail==""){
				update_query("edu_alerts",array(is_sent=>1),array(id=>$id));
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

			}*/
		if(!empty($id) || !empty($update_id)){
			$msg = "Alert is live now.";
		}
		$bounceback = 'edu-approve.htm?viewapproved=1';
		location($bounceback.qsa(array(message=>$msg)));
	}

	/* Delete Alerts */
	if($_POST['submit_type']== 'delete'){
		$params['is_deleted'] = '1';
		update_query('edu_alerts', $params,array(id=>$_POST['id']));
		$bounceback = 'edu-approve.htm';
		location($bounceback.qsa(array(id=>$_POST['id'],message=>$msg)));
	}
}
?>