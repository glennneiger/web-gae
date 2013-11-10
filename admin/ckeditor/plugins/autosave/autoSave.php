<?php
	session_start();
	$autoSave['text']= addslashes(mswordReplaceSpecialChars(stripslashes($_POST['content'])));
	$autoSave['userId']= $_SESSION['AID'];	
	insert_or_update('drafts',$autoSave, array('userId'=>$_SESSION['AID']));	
	$result['status']='ok';
	echo json_encode($result);
?>