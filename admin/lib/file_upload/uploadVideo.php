<?php
	set_time_limit(60*30 );//30 min
	ini_set('upload_max_filesize', '200M');
	ini_set('post_max_size', '200M');
	global $D_R,$Video_file_path;
	$maxSize = 209715200; // 200 MB
	//include_once($D_R."/lib/aws/_aws_upload.php");
	include_once($D_R."/lib/_image_rsync.php");
	$arAllowedExt = array("flv","mp4");
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
	$filePath=$D_R."/".$Video_file_path;

	$savePath = $filePath."/".$newFileName;

	$output	= copy($_FILES["Filedata"]["tmp_name"],$savePath);

/*	$file_path = $obAWSUpload->folder_name.$obAWSUpload->file_name;
	$obAWSUpload->getFile($file_path);*/

	if($output)
	{
		$obRsync = new ImageSync();
		$obRsync->uploadVideoAws("mvtv_video",$newFileName);
		echo "FILEID:" . $newFileName; // Return the file name to the script
		/*$obAWSUpload = new AWSUpload();
		$obAWSUpload->folder_name = $Video_file_path;
		//$obAWSUpload->folder_name = "mvtv/test/";
		$obAWSUpload->file_name = $newFileName;
		$obAWSUpload->file_type = $fileExt;
		$obAWSUpload->file_temp_name = $savePath;
		$is_uploaded = $obAWSUpload->uploadFile();

		if($is_uploaded)
		{
			echo "FILEID:" . $newFileName; // Return the file name to the script
		}
		else
		{
			echo 'Error in Video Upload to Amazon';
		}*/
	}
	else
	{
		echo 'Error in Video Upload';
	}
?>