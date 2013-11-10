<?php
	global $D_R;
	include_once($D_R."/lib/_image_rsync.php");
	$maxSize = 2097152;
	$arAllowedExt = array("jpg","jpeg","gif","png","bmp");
	include_once($D_R."/admin/lib/_admin_data_lib.php");
	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	ini_set("html_errors", "0");
	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "ERROR:invalid upload";
		exit(0);
	}
	$filesize = $_FILES["Filedata"]['size'];
	if($filesize < 1)
	{
		echo "File size is empty.";
		exit(0);
	}
	else if($filesize > $maxSize)
	{
		echo "File size is too big.";
		exit(0);
	}
	$fileName = strtolower($_FILES["Filedata"]['name']);
	$fileName = preg_replace('/\s/', '_', $fileName);
	$arFileDetail = preg_split("/\./",$fileName);
	$inParts = count($arFileDetail);
	$fileExt = $arFileDetail[$inParts-1];
	if(!in_array($fileExt,$arAllowedExt))
	{
		echo "File type isn't allowed";
		exit(0);
	}

	$front_name = substr($arFileDetail[0], 0, 15);
	$newFileName = $front_name."_".time().".".$fileExt;
	// Create folder of current date.
	$objfileUpload= new fileUpload();
	$filePath = $objfileUpload->createUploadFolder();
	$savePath = $filePath."/".$newFileName;

	$output	= copy($_FILES["Filedata"]["tmp_name"],$savePath);
	if($output)
	{
		$pathToImages=$D_R."/assets/buzzbanter/charts/original/".date('mdy').'/';
		$pathToThumbs=$D_R."/assets/buzzbanter/charts/thumbnail/".date('mdy').'/';
		$thumbWidth="165";
		$imageThumb = $objfileUpload->createThumbs( $pathToImages, $pathToThumbs, $thumbWidth,$newFileName);
		// This peice of code is creating a big problem while uploading charts. If we need to on the rsync we have to do it file wise rather than whole folder
		/*$obRsync = new ImageSync();
		$obRsync->setImageUploadPath("buzz_charts");*/
		$obRsync = new ImageSync();
		$obRsync->uploadAdminServerImages("buzz_charts_original",$newFileName);
		$obRsync->uploadAdminServerImages("buzz_charts_thumbnail",$newFileName);
		if($imageThumb)
		{
			echo "FILEID:" . $newFileName; // Return the file name to the script
		}
		else
		{
			echo 'Error in File Upload';
		}

	}
	else
	{
		echo 'Error in File Upload';
	}
?>