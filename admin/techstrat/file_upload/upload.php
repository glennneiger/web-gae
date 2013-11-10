<?php
	global $D_R,$servername;
	include_once("$D_R/lib/techstrat/_techstratData.php");
	$objSean= new techstartData("techstrat_posts");
	$maxSize = 2097152;
//	$arAllowedExt = array("pdf","xls");
	$arAllowedExt = array("pdf");
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
		
	$front_name =rand();
	$newFileName = "performance_".$front_name.".".$fileExt;
	// Create folder of current date.
	$filePath ="$D_R/assets/techstrat/pdf_performance";
	$savePath = $filePath."/".$newFileName;
						
	$output	= copy($_FILES["Filedata"]["tmp_name"],$savePath);
	if($output)
	{	   
	   $id=$objSean->setPerformance($newFileName); 
	   if($id){
	        
			$foldername="techstrat/pdf_performance";
			$objSean->uploadPerformanceSync($servername,$foldername);
			echo 'PDF uploaded successfully';
		}
	}
	else
	{
		echo 'Error in File Upload';
	}		
?>