<?php
global $D_R,$cloudStorageTool,$HTPFX,$HTHOST,$tempPath,$bucketPath;
include_once("$D_R/lib/json.php");
$action  = $_POST['text'];
$json = new Services_JSON();

function validateData($size,$allowedSize,$fileName,$arAllowedExt)
{ 
        $extArr = explode('.',$fileName);
        $ext = $extArr[count($extArr)-1];
        if($size=="0")
        {
            $error = "Invalid File";
        }
        if($size>$allowedSize && $allowedSize!="")
        {
            $error = "File size is too big.";
        }
        if(!in_array($ext,$arAllowedExt))
        {
            $error = "File type isn't allowed";
        }
        return $error;
}

function createBuzzThumbs($pathToImages,$pathToThumbs,$thumbWidth,$imagename)
	{

	  $fname=$imagename;

		// continue only if this is a JPEG image
		  // load image and get image size
		  $extension = pathinfo("{$pathToImages}{$fname}");
		 $extension = $extension[extension];
		  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG"){
		            $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
		          }

		          if($extension == "png") {
		            $img = imagecreatefrompng( "{$pathToImages}{$fname}" );
		          }

		          if($extension == "gif") {
		           	$img = imagecreatefromgif( "{$pathToImages}{$fname}" );
          }

		  $width = imagesx( $img );
		  $height = imagesy( $img );
		  // calculate thumbnail size
		  if($width>$thumbWidth)
		  {
                $new_width = $thumbWidth;
			  	$new_height = floor(($height * $thumbWidth )/$width );
		  }
		  else
		  {
			  $new_width = $width;
			  $new_height = $height;
		  }

		  // create a new temporary image
		  $tmp_img = imagecreate( intval($new_width), intval($new_height) );

		  if($extension == "gif")
		 {
			$trnprt_indx = imagecolortransparent($img);

      		// If we have a specific transparent color
			if ($trnprt_indx >= 0)
			{
				// Get the original image's transparent color's RGB values
				@$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($tmp_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

				// Completely fill the background of the new image with allocated color.
				imagefill($tmp_img, 0, 0, $trnprt_indx);

				// Set the background color for new image to transparent
				imagecolortransparent($tmp_img, $trnprt_indx);
			}
			//$transparent = imagecolorallocate($tmp_img, "255", "255", "255");
			//imagefill($tmp_img, 0, 0, $transparent);
		 }
		  // copy and resize old image into new image
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, intval($new_width),intval($new_height), intval($width), intval($height) );
		  // save thumbnail into a file
		  if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
		  {
		      $thumbImage = imagejpeg( $tmp_img, $pathToThumbs.$fname );
		  }else if($extension == "png") {
			 $thumbImage = imagepng( $tmp_img, $pathToThumbs.$fname );
		  }else if($extension == "gif") {
		      $thumbImage = imagegif( $tmp_img, $pathToThumbs.$fname );
          }
		  if($thumbImage)
		  {
		  	return true;
		  }
		  else
		  {
		  	return false;
		  }
}


switch($action)
{
    case 'getUploadUrl':
        $posturl  = $_POST['posturl'];
        $handler = $HTPFX.$HTHOST.$posturl ;
        $options = [ 'gs_bucket_name' => $tempPath];
        $upload_url = $cloudStorageTool->createUploadUrl($handler , $options);
        if($upload_url!="")
        {
            $value['status']="1";
            $value['url']=$upload_url;
        }
        else{
            $value['status']="0";
        }
        break;
    case 'ckeditorAddFolder':
    	$path = $_POST['selectedfolder'];
    	$newFolderName = $_POST['newFolderName'];
    	copy($bucketPath."/spacer.gif",$bucketPath."/".$path.$newFolderName."/");
		$value['status']="1";
        $value['msg']="Uploaded Sucessfully";
        $value['path']=$path;
    	break;
    case 'radioUpload':
    	$filePath = $_POST['filePath'];
    	$val = $_POST['val'];
    	$fname = $_POST['fname'];
    	$uploadFilePath = $_POST['uploadFilePath'];
    	copy($bucketPath."/".$filePath,$uploadFilePath);
    	createRadioArt($fname,$val);
		$value['status']="1";
        $value['msg']="Uploaded Sucessfully";
        $value['path']=$uploadFilePath;
    	break;
    case 'ckeditorUpload':
    	$arAllowedExt = explode(",",$_POST['allowedExtensions']);
    	if($_POST['selectedfolder']=="/")
    	{
    		$uploadFolder = $_POST['defaultFolder'];
    	}
    	else {
    		$uploadFolder = "/".$_POST['selectedfolder'];
    	}

    	$error = validateData($_FILES['fileInput']['size'],'',$_FILES['fileInput']['name'],$arAllowedExt);
    	if($error == "")
        {
        	 $filename = str_replace(" ","_",$_FILES['fileInput']['name']);
        	 $imgpath =  $uploadFolder.$filename;
             move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$imgpath);
             $value['status']="1";
             $value['msg']="Uploaded Sucessfully";
             $value['file']=$filename; 
        }
    	break;
    case 'buzzUpload':
        $arAllowedExt = array("jpg","jpeg","gif","png","bmp");
        $error = validateData($_FILES['fileInput']['size'],'2097152',$_FILES['fileInput']['name'],$arAllowedExt);
        if($error == "")
        {
            $date = date('mdYHis');
            $extArr = explode('.',$_FILES['fileInput']['name']);
            $fileExt = array_pop($extArr);
            $filename = implode(".",$extArr);
            $front_name = str_replace(" ","_",substr($filename, 0, 15));
            $newFileName = $front_name."_".time().".".$fileExt;
            $imgpath =  "/assets/buzzbanter/charts/original/".date('mdy').'/'.$newFileName;
            $pathimg =  "/assets/buzzbanter/charts/original/".date('mdy').'/';
            $thumbImgpath =  "/assets/buzzbanter/charts/thumbnail/".date('mdy').'/';
            move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$imgpath);
            $thumbWidth="165";
            createBuzzThumbs($bucketPath.$pathimg,$bucketPath.$thumbImgpath,$thumbWidth,$newFileName);
            $value['status']="1";
            $value['msg']="Uploaded Sucessfully";
            $value['file']=$newFileName;
        }
        else
        {
            $value['status']="0";
            $value['msg']=$error;
        }
        break;
    case 'eduUploadVideo':
 		$arAllowedExt = array("mp3");
        $error = validateData($_FILES['fileInput']['size'],'209715200',$_FILES['fileInput']['name'],$arAllowedExt);
        if($error == "")
        {
            $date = date('mdYHis');
            $extArr = explode('.',$_FILES['fileInput']['name']);
            $fileExt = array_pop($extArr);
            $filename = implode(".",$extArr);
            $front_name = str_replace(" ","_",substr($filename, 0, 15));
            $newFileName = $front_name."-".$date.".".$fileExt;
            $imgpath =  "/assets/edu/video/".$newFileName;
            move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$imgpath);
            $value['status']="1";
            $value['msg']="Uploaded Sucessfully";
            $value['file']=$newFileName;
        }
        else
        {
            $value['status']="0";
            $value['msg']=$error;
        }
    	break;
    case 'eduUploadImage':
 		$arAllowedExt = array("jpg","jpeg","gif","png","bmp");
        $error = validateData($_FILES['fileInput']['size'],'209715200',$_FILES['fileInput']['name'],$arAllowedExt);
        if($error == "")
        {
            $eduImgPath="/assets/edu/images/";
			$eduImgName = rand().'-'.str_replace(" ","_",$_FILES['fileInput']['name']);
            move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$eduImgPath.$eduImgName);
            $value['status']="1";
            $value['msg']="Uploaded Sucessfully";
            $value['file']=$newFileName;
        }
        else
        {
            $value['status']="0";
            $value['msg']=$error;
        }
    	break;
    case 'artUpload':
        $arAllowedExt = array("mp3");
        $error = validateData($_FILES['fileInput']['size'],'209715200',$_FILES['fileInput']['name'],$arAllowedExt);
        if($error == "")
        {
            $date = date('mdYHis');
            $extArr = explode('.',$_FILES['fileInput']['name']);
            $fileExt = array_pop($extArr);
            $filename = implode(".",$extArr);
            $front_name = str_replace(" ","_",substr($filename, 0, 15));
            $newFileName = $front_name."-".$date.".".$fileExt;
            $imgpath =  "/assets/audio/".$newFileName;
            move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$imgpath);
            $value['status']="1";
            $value['msg']="Uploaded Sucessfully";
            $value['file']=$newFileName;
        }
        else
        {
            $value['status']="0";
            $value['msg']=$error;
        }
        break;
    case 'feedUpload':
        $arAllowedExt = array("jpg","jpeg","gif","png","bmp");
        $fileName  = str_replace(" ","_",$_FILES['fileInput']['name']);
        $imgpath =  "/assets/dailyfeed/uploadimage/".date('mdy')."/".$fileName;
        $error = validateData($_FILES['fileInput']['size'],'2097152',$_FILES['fileInput']['name'],$arAllowedExt);
        if($error == "")
        {
            move_uploaded_file($_FILES['fileInput']['tmp_name'],$bucketPath.$imgpath);
            $value['status']="1";
            $value['msg']="Uploaded Sucessfully";
            $value['file']=$_FILES['fileInput']['name'];
            $value['image_directory_name'] = date('mdy');
        }
        else
        {
            $value['status']="0";
            $value['msg']=$error;
        }
        break;
    
    default:
        $value['status']="0";
        $value['msg']="Invalid Action";
        break;
}

$output = $json->encode($value);
echo strip_tags($output);


?>