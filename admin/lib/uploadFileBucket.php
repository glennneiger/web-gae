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

function createRadioArt($fname,$val){
	$fileUrl = file_get_contents($StorageListPath."?prefix=assets/radio/".fname);
		 	//$fileUrl = file_get_contents($StorageListPath."?prefix=assets/radio/Nov2113165232.mp3");
			$bucketFileData = json_decode($fileUrl);
			htmlprint_r($bucketFileData);
			$bucketFileData = $bucketFileData->items;
			htmlprint_r($bucketFileData);
			if(!empty($bucketFileData))
			{			
			 	$bucketFileTime = $bucketFileData[0]->generation;
				$modify_time = date('F d  H:i:s',$bucketFileTime);
				$article['contrib_id'] ="809";
				$article['approved']="0";
				$article['sent']="0";
				$article['is_public']="0";
				$article['position']="No positions in stocks mentioned.";
				$article['date']=date('Y-m-d H:i:s');
				$article['title']="Money Matters Radio File(".$val->name.") uploaded on ".$modify_time;
				$article['body']="<br />
{RADIO}<br />
<br />
<br />
<em>Twitter: <a href='https://twitter.com/moneymattersfn'>@MoneyMattersFN</a></em>";
				$article['contributor']="Money Matters Radio";
				$article['keyword']="";
				$article['is_live']="";
				$article['publish_date']="";
				$article['section_id']="51";
				$article['navigation_section_id']="98";
				$article['is_marketwatch']="0";
				$article['is_fox']="0";
				$article['hosted_by']="Minyanville";
				$article['subsection_ids']="93,98,51";
				$article['is_msnfeed']="0";
				$article['is_yahoofeed']="0";
				$article['is_buzzalert']="0";
				$article['layout_type']="radio";
				htmlprint_r($article);

				$id=insert_query("articles",$article);
				if($id>0)
				{
					 $fileUrl = file_get_contents("http://admin01a.minyanville.com/admin/robot.RemoveFileBucket.php?type=radio&file=".$fname);
					 $bucketRemoveData = json_decode($fileUrl); 
					 htmlprint_r($bucketRemoveData);
					 die;		 
					if($upload=="1")
					{
						$article_id[] = $id;
						$article_revision['article_id'] = $id;
						$article_revision['revision_number'] = "1";
						$article_revision['body'] = "<br />
		{RADIO}<br />
		<br />
		<br />
		<em>Twitter: <a href='https://twitter.com/moneymattersfn'>@MoneyMattersFN</a></em>";
						$article_revision['updated_by'] = "mediaagility";
						$article_revision['updated_date'] = date('Y-m-d H:i:s');
						$article_revision['page_no'] ="1";

						htmlprint_r($article_revision);
						insert_query("article_revision",$article_revision);

						$article_meta['item_id'] =$id;
						$article_meta['item_type'] ="1";
						$article_meta['item_key'] ='radiofile';
						$article_meta['item_value'] =$VIDEO_SERVER."assets/radio/".$date.".mp3";
						htmlprint_r($article_meta);
						insert_query("article_meta",$article_meta);
						$obContent = new Content(1,$id);
						$obContent->setArticleMeta();
					}
					else
					{
						del_query('articles','id',$id);
						$to="nidhi.singh@mediaagility.co.in";
						$from=$NOTIFY_FEED_ERROR_FROM;
						$subject="Radio Artcile Not Posted";
						$msg = "Article has been deleted for file ".$val->name." having article id ".$id;
						mymail($to,$from,$subject,$msg);
					}
				}
				else
				{
					$to="nidhi.singh@mediaagility.co.in";
					$from=$NOTIFY_FEED_ERROR_FROM;
					$subject="Radio Artcile Not Posted";
					$msg = "radio not posted for ".$val->name;
					mymail($to,$from,$subject,$msg);
				}
			}
			if(is_array($article_id)){
				$articleList = implode(",",$article_id);
				$to="nidhi.singh@mediaagility.co.in,news@minyanville.com";
				$from=$NOTIFY_FEED_ERROR_FROM;
				$subject="Radio Artcile Posted";
				$strurl = "?article_id=".$articleList."&type=radio";
				mymail($to,$from,$subject,inc_web($feed_error_template.$strurl));
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
            $thumbImgpath =  "/assets/buzzbanter/charts/thumbnail/".date('mdy').'/'.$newFileName;
            copy($_FILES['fileInput']['tmp_name'], $bucketPath.$thumbImgpath);
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