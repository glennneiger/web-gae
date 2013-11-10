<?php
	global $D_R,$HTPFX,$HTHOST,$HTADMINHOST;
	include_once($D_R."/lib/_image_rsync.php");
	
	$maxSize = 2097152;
	$arAllowedExt = array("jpg","jpeg","gif","png","bmp");
	include_once($D_R."/admin/lib/_dailyfeed_data_lib.php");
	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	ini_set("html_errors", "0");

	if(isset($_REQUEST['cropthis']) && $_REQUEST['cropthis']!='')
	{
		//$targ_w = $targ_h = 150;

		$targ_w = $_REQUEST['w'];
		$targ_h = $_REQUEST['h'];
		$jpeg_quality = 90;
		$png_quality = 9;
		
		//$src = 'demo_files/flowers.jpg';
		$src = $_REQUEST['cropthis'];

		$path_parts = pathinfo($src);
		

		function LoadImage($imgname,$type)
		{
			/* Attempt to open */
			if($type=='1')
				$img_r = @imagecreatefromjpeg($imgname);

			if($type=='2')
				$img_r = @imagecreatefromgif($imgname);

			if($type=='3')
				$img_r = imagecreatefrompng($imgname);

			/* See if it failed 
			if(!$img_r)
			{
				
				//$im  = imagecreatetruecolor(150, 30);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
				$bgc = imagecolorallocate($dst_r, 255, 255, 255);
				$tc  = imagecolorallocate($dst_r, 0, 0, 0);

				imagefilledrectangle($dst_r, 0, 0, $targ_w, $targ_h, $bgc);

				
				imagestring($dst_r, 1, 5, 5, 'Error loading ' . $imgname, $tc);
			}
*/
			return $img_r;
		}

		switch(strtolower($path_parts['extension']))
		{
			case 'jpeg':
			case 'jpg':
				//$img_r = imagecreatefromjpeg($src);
				$img_r = LoadImage($src,'1');
				break;
			case 'gif':
				$img_r = LoadImage($src,'2');
				break;
			case 'png':
				$img_r = LoadImage($src,'3');
				break;
			default:
				$img_r = LoadImage($src,'1');
				break;

		}

		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

		$samp=imagecopyresampled($dst_r,$img_r,0,0,$_REQUEST['x'],$_REQUEST['y'],
		$targ_w,$targ_h,$_REQUEST['w'],$_REQUEST['h']);
		
		$src=str_replace($HTPFX.$HTADMINHOST,$D_R,$src);
		$path_parts = pathinfo($src);
		
		chmod($path_parts['dirname'],0777);

	
		$srcNew=str_replace($path_parts['filename'],$path_parts['filename']."-cropped",$src);
		$path_parts = pathinfo($srcNew);
		
		switch(strtolower($path_parts['extension']))
		{
			case 'jpeg':
			case 'jpg':
				$create=imagejpeg($dst_r,$srcNew,$jpeg_quality);
				break;
			case 'gif':
				$create=imagegif($dst_r,$srcNew,$jpeg_quality);
				break;
			case 'png':
				$create=imagepng($dst_r,$srcNew,$png_quality);
				break;
			default:
				$create=imagejpeg($dst_r,$srcNew,$jpeg_quality);
				break;

		}
		$obRsync = new ImageSync();
		$obRsync->uploadAdminServerImages("dailyfeed",$path_parts['basename']);
		echo $path_parts['basename'];
		die;
	}
	else
	{
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
	$objfileUpload= new fileUploadDailyFeed();
	$filePath = $objfileUpload->createUploadFolder();
	$savePath = $filePath."/".$newFileName;

	$output	= copy($_FILES["Filedata"]["tmp_name"],$savePath);
	if($output)
	{
		$pathToImages=$D_R."/assets/dailyfeed/uploadimage/".date('mdy').'/';
		//$result=$objfileUpload->uploadFTPServer($newFileName);

		// Rsync images to all the server.
		
		$obRsync = new ImageSync();
		//$obRsync->setImageUploadPath("dailyfeed");
		$obRsync->uploadAdminServerImages("dailyfeed",$newFileName);
		
		list($width, $height, $type, $attr)= getimagesize($savePath); 
		echo "FILEID:".$newFileName."~width:".$width."~height:".$height;

	}
	else
	{
		echo 'Error in File Upload';
	}
	}
?>