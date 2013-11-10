<?php
session_start();
if($_POST['show']=="1"){
  $appcommentcount=$_POST['appcommentcount'];
  $articleid=$_POST['articleid'];
  $subscription_id=$_POST['subscription_id'];
  $pageName=$_POST['pageName'];
  $profile_exchange=$_POST['profile_exchange'];
  $from=$_POST['from'];
  $showcomment=$_POST['showcomment'];
  $imagevalue=$_POST['imagevalue'];
  $sid=$_POST['sid'];
  $eid=$_POST['eid'];
  $urlPost=$_POST['urlPost'];
  showComment($appcommentcount,$articleid,$subscription_id,$pageName,$profile_exchange,$from,$showcomment,$imagevalue,$sid,$eid,$urlPost);
    
}else{
	# getting bad words from profanity filter file.
	$fileProfanity = "../js/profanity.txt";
	
	$handle = fopen ($fileProfanity, "r");
	$badwords = fread ($handle, filesize ($fileProfanity));
	fclose ($handle);

	$thread_id=$_GET[thread_id];	
	$comment_id=$_GET[comment_id];	
	$subscription_id=$_GET[subscription_id];
	$slideshowTitle=$_GET[slideshowTitle];
	$slideshowTitle = str_replace("'","",$slideshowTitle);
	$sourceURL=$_GET[sourceURL];
	$sourceURL=$sourceURL.'?camp=syndication&medium=portals&from=facebook';
	$sessionName= $_SESSION['nameFirst'].' '.$_SESSION['nameLast'];
	
	$from_subscription_id=$_GET[from_subscription_id];	
	$message_id=$_GET[message_id];
	$to_subscription_id=$_GET[to_subscription_id];	
        $conv_ids=$_GET[conv_ids];	
	$strinbox=$_GET[inbox];	
	if($strinbox){
		$title=$_GET[title];	
 	} else {
		$title="";
		$body="";
	}
	if($message_id==""){	

		$strHtml="<Form name='frmPost".$comment_id."' method='post' class='PostComment_section' >";
		$strHtml.="<input type='hidden' id='subscription_id".$comment_id."' value='".$subscription_id."'/>";
		$strHtml.="<input type='hidden' id='thread_id".$comment_id."' value='".$thread_id."'/>";
		$strHtml.="<input type='hidden' id='comment_id".$comment_id."' value='".$comment_id."'/>";
		$strHtml.="";
		$strHtml.="Subject:";
		$strHtml.="<BR>";
		$strHtml.="<input type='text' id='txtTitle".$comment_id."' value='$title' align='center' style='border:#999999 solid 1px; width:340px;'/>";
		$strHtml.="<BR>";
		$strHtml.="Comment:";
		$strHtml.="<BR>";
		$strHtml.="<textarea id='txtBody".$comment_id."' rows='10' cols='40' style='border:#999999 solid 1px; width:340px;'>$body</textarea>";
		$strHtml.="<BR>";
			
		$strHtml.="<img style='cursor:pointer;' src='$IMG_SERVER/images/community_images/submit-1.gif' vspace='5' border='0' 
					align='left' onclick=\"streamPublish('$sessionName', '','$slideshowTitle', '$sourceURL', 'Share with your friends','$comment_id','','$badwords');\" />";
		
		$strHtml.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/cancel.gif' vspace='5' border='0' hspace='10' align='left' onclick='doCancel(".$comment_id.");'/>";			
		$strHtml.="</form>";
	}
	else{
		$strHtml="<form name='frmPost".$message_id."' method='post'>";
		$strHtml="<input type='hidden' id='from_subscription_id".$message_id."' value='".$from_subscription_id."'/>";
		$strHtml.="<input type='hidden' id='to_subscription_id".$message_id."' value='".$to_subscription_id."'/>";
		$strHtml.="<input type='hidden' id='message_id".$message_id."' value='".$message_id."'/>";
		if($strinbox){	
			$strHtml.="<input type='hidden' size='50' id='txtTitle".$message_id."' value='$title' align='center'/>";
		} else {
        $strHtml.="<BR>";
		$strHtml.="Subject:";
		$strHtml.="<BR>";
		$strHtml.="<input type='text' size='50' id='txtTitle".$message_id."' value='$title' align='center'/>";
		$strHtml.="<BR>";
		$strHtml.="Messsage:";
		$strHtml.="<BR>";
                    }
		$strHtml.="<textarea cols='56' rows='10'  id='txtBody".$message_id."' class='textcontro2' style='width:470px;' >$body</textarea>";
		$strHtml.="<BR>";
		if($strinbox){	
			$strHtml.="<div style='width:470px; float:left; clear:both;'> <img style='cursor:pointer;' src='$IMG_SERVER/images/community_images/SendReply.gif' 
				 onclick=\" Javascript:postHttpRequest($message_id,1,'$badwords','$conv_ids','$strinbox')\" align='right' vspace='10'/></div>";
                       $strHtml.="<BR>";
		} else {	
		$strHtml.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/submit-1.gif' width='70' height='17' 
					align='left' onclick=\" Javascript:postHttpRequest($message_id,1,'$badwords','$strfriend','$strinbox')\"/>";
		
		$strHtml.="<img style='cursor:pointer' src='$IMG_SERVER/images/community_images/Cancel.gif' width='70' 		
					height='17' align='left' onclick='doCancel(".$message_id.");'/>";
                    }    	
        $strHtml.="</form>";
	}
	if($strinbox) {
	?>
	    <div style='padding-left:20px'> <?php echo $strHtml; ?></div>
		
	<? } else { ?>
		<div style='padding-left:10px'> <?php echo $strHtml; ?></div>
	<? } 
}
?>	
		
	
