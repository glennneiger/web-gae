<?php
global $cloudStorageTool,$HTPFX,$HTHOST;

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

switch($action)
{
    case 'getUploadUrl':
        $posturl  = $_POST['posturl'];
        $handler = $HTPFX.$HTHOST.$posturl ;
        $options = [ 'gs_bucket_name' => 'mvassets/temp'];
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
    	copy("gs://mvassets/spacer.gif","gs://mvassets/".$path.$newFolderName."/");
		$value['status']="1";
        $value['msg']="Uploaded Sucessfully";
        $value['path']=$path;
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
             move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
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
            $thumbImgpath =  "/assets/buzzbanter/charts/thumbnail/".date('mdy').'/'.$newFileName;
            copy($_FILES['fileInput']['tmp_name'], "gs://mvassets".$thumbImgpath);
            move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
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
            move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
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
            move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$eduImgPath.$eduImgName);
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
            move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
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
            move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
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