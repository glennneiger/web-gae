<?php
	global $D_R,$Still_file_path,$Thumb_file_path;
	//include_once($D_R."/lib/aws/_aws_upload.php");
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

	$filePathStill=$D_R."/".$Still_file_path;
	$filePathThumb=$D_R."/".$Thumb_file_path;
	$savePath = $filePathStill."/".$newFileName;

	$output	= copy($_FILES["Filedata"]["tmp_name"],$savePath);
	if($output)
	{
		$thumbWidth="165";
		$imageThumb = $objfileUpload->createThumbs($filePathStill, $filePathThumb, $thumbWidth,$newFileName);

		if($imageThumb)
		{
			$obRsync = new ImageSync();
			$obRsync->uploadVideoAws("mvtv_still",$newFileName);
			$obRsync->uploadVideoAws("mvtv_thumb",$newFileName);

			echo "FILEID:" . $newFileName; // Return the file name to the script

			/*$obAWSUpload = new AWSUpload();
			$obAWSUpload->file_type = $fileExt;
			//$obAWSUpload->folder_name = "mvtv/test";
			$obAWSUpload->folder_name = $Still_file_path;
			$obAWSUpload->file_name = $newFileName;
			$obAWSUpload->file_temp_name =$savePath;
			$is_still_uploaded = $obAWSUpload->uploadFile();
			if(!$is_still_uploaded)
			{
				echo 'Error in Uploading Still image to Amazon';
			}
			else
			{
				//$obAWSUpload->folder_name = "mvtv/test/thumb";
				$obAWSUpload->folder_name = $Thumb_file_path;
				$obAWSUpload->file_temp_name =$filePathThumb.$newFileName;
				$is_thumb_uploaded = $obAWSUpload->uploadFile();
				if(!$is_thumb_uploaded)
				{
					echo 'Error in Uploading Thumb image to Amazon';
				}
				else
				{
					echo "FILEID:" . $newFileName; // Return the file name to the script
				}
			}*/
		}
		else
		{
			echo 'Error in Creating Thumb File';
		}
	}
	else
	{
		echo 'Error in File Upload';
	}
?>