<?php
/**
 * This file uploads a file in the back end, without refreshing the page
 */
@session_start();
$dirName="$D_R/assets/featureimage";
if ($_POST['id']) 
{
	//$uploadFile=$_GET['dirname']."/".$_FILES[$_POST['id']]['name']; for security reasons,  hardcode the name of the directrory.
	@mkdir($dirName,0777);

	$uploadFile="$dirName/".$_POST['hidImagePrefix'].$_FILES[$_POST['id']]['name'];
	
	if(!is_dir($dirName)) {
		echo '<script> alert("Failed to find the final upload directory: $dirName);</script>';
	}
	else if (!copy($_FILES[$_POST['id']]['tmp_name'],$uploadFile)) {	
		echo '<script> alert("Failed to upload file":$uploadFile);</script>';
	}	
}
else 
{
	// for secority reason either remove the extentions or rectrict uploaded not to upload / run scripts like file.php else they can misuse the disk space 
	//$uploadFile=$_GET['dirname']."/".$_GET['filename']; // removed for security reasons (happend with my demo )
	$uploaded_file_name = $_GET['image_prefix'].$_GET['filename'];
	$uploadFile="$dirName/".$uploaded_file_name;
	if (file_exists($uploadFile)) {
		echo "<b><font color='#0000FF'>File uploaded successfully.</font></b><input type='hidden' id='hidUploadedImageName' value='".$uploaded_file_name."' />";	
	}
	else
	 {
		echo "<img src='loading.gif' alt='loading...' />";
	}
}
?>