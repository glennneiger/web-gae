<?php
global $D_R;
include_once($D_R."/lib/config/_server_config.php");
include_once("$D_R/lib/config/_rsync_config.php");
class ImageSync
{
	public $S4 ="10.0.0.196";
	public $S5 ="10.0.0.9";
	function ImageSync($showLogs=NULL)
	{
		$this->showLogs=$showLogs;
	}

	function setImageUploadPath($title=NULL){
	    global $D_R;
		$this->uploadImagesAws($title);
		$params['imagesource']=$D_R.'/assets/'.$this->sourceImage;
		$params['imagedestination']=$D_R.'/assets/'.$this->Image;
		$params['uploaded_on']=mysqlNow();
		$id=insert_query("img_upload_log",$params,$safe=0);
	     if($id){
		 	return $id;
		 }
	}


	function RsyncUploadImages()
	{
		global $SERVERS_ARRAY,$S5;
			foreach($SERVERS_ARRAY  as $SERVER)
			{
				if($_SERVER['SERVER_ADDR'] == $SERVER['serverIP'])
				{
					  /*check last updated Id*/
						$getUploadPath=$this->getImageUploadPath();
						if(is_array($getUploadPath)){
							foreach($getUploadPath as $uploadPath){
							    if($uploadPath['imagesource']!=="" && $uploadPath['imagedestination']!=="" && !is_null($uploadPath['imagesource']) && !is_null($uploadPath['imagedestination'])){
									$command = 'rsync -avz --timeout=30 -e "ssh -p 16098" root@'.$S5.':'.$uploadPath['imagesource'].' '.$uploadPath['imagedestination'];
									$stream=shell_exec($command);
									echo "<br>"."Folder id- ".$uploadPath['id'].' '."uploaded successfully";
								}
							}

							$this->writeLatImageUploadId($uploadPath['id']);
						}else{
							echo "<br>"."No record found to update image";
						}
				}
			}
	}

	function RsynchImages($title=NULL)
	{
		global $SERVERS_ARRAY;
		$this->fetImagePath($title);
		if($_SERVER['SERVER_ADDR'] == "10.0.0.196" || $_SERVER['SERVER_ADDR'] == "10.0.0.9")
		{
			foreach($SERVERS_ARRAY  as $SERVER)
			{
				$connection = ssh2_connect($SERVER['serverIP'], $SERVER['port']);
				if(ssh2_auth_password($connection,$SERVER['user'], $SERVER['password']))
				{
					if($_SERVER['SERVER_ADDR'] == $SERVER['serverIP'])
					{
						continue;
					}
					$command = 'rsync -avz --timeout=30 -e "ssh -p 16098" root@'.$_SERVER['SERVER_ADDR'].':'.$this->source.' '.$this->dest;
					$stream = ssh2_exec($connection, $command );
					if($this->showLogs){
						print "Sucessfully synced to ".$SERVER['serverIP']." on ".date('Y-m-d H:i');
					}
				}
				else
				{
					if($this->showLogs){
						print "Failed connecting to ".$SERVER['serverIP']." on ".date('Y-m-d H:i');
					}
				}
			}
		}
	}

	function fetImagePath($title)
	{
		switch($title)
		{
			case 'dailyfeed':
				$this->source = "/home/sites/minyanville/web/assets/dailyfeed/uploadimage";
				$this->dest = "/home/sites/minyanville/web/assets/dailyfeed";
			break;
			case 'bio_image':
				$this->source = "/home/sites/minyanville/web/assets/bios";
				$this->dest = "/home/sites/minyanville/web/assets";
			break;
			case 'buzz_logo':
				$this->source = "/home/sites/minyanville/web/assets/professorlogo";
				$this->dest = "/home/sites/minyanville/web/assets";
			break;
			case 'buzz_charts':
				$this->source = "/home/sites/minyanville/web/assets/buzzbanter/charts";
				$this->dest = "/home/sites/minyanville/web/assets/buzzbanter";
			break;
			case 'slideshow':
				$this->source = "/home/sites/minyanville/web/assets/slideshow";
				$this->dest = "/home/sites/minyanville/web/assets";
			break;
			case 'housingmarketreport':
				$this->source = "/home/sites/minyanville/web/assets/housingreport/pdf_performance";
				$this->dest = "/home/sites/minyanville/web/assets/housingreport";
			break;
			case 'wallofworry':
				$this->source = "/home/sites/minyanville/web/assets/lloyds-wall-of-worry";
				$this->dest = "/home/sites/minyanville/web/assets";
			break;
			default:
				$this->source = "/home/sites/minyanville/web/assets/FCK_Jan2011";
				$this->dest = "/home/sites/minyanville/web/assets";
			break;
		}
	}


	function getImageUploadPath(){
	    $latestUploadId=$this->readLastImageUploadId();
		$qry="select id,imagesource,imagedestination from img_upload_log where id>'".$latestUploadId."'";
		$result=exec_query($qry);
		if($result){
			return $result;
		}
	}

	function readLastImageUploadId(){
	    global $D_R;
		$myFile = $D_R."/assets/data/latest_image_uploadid.txt";
		$fh = fopen($myFile, 'r');
		$theData = fgets($fh);
		fclose($fh);
		return $theData;
	}

	function writeLatImageUploadId($stringData){
	  	global $D_R;
		$myFile = $D_R."/assets/data/latest_image_uploadid.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $stringData);
		fclose($fh);
	}


	function uploadImagesAws($title)
	{
		switch($title)
		{
			case 'dailyfeed':
				$this->sourceImage = "dailyfeed/uploadimage/".date('mdy');
				$this->destImage = "dailyfeed/uploadimage";
				$this->uploadImages();
			break;
			case 'bio_image':
				$this->sourceImage = "bios";
				$this->dest = "";
			break;
			case 'buzz_logo':
				$this->sourceImage = "professorlogo";
				$this->destImage = "";
				$this->uploadImages();
			break;
			case 'buzz_charts':
				$this->source = "buzzbanter/charts";
				$this->dest = "buzzbanter";
				$this->uploadImages();
			break;
			case 'slideshow':
				$this->sourceImage = "slideshow";
				$this->destImage = "";
				$this->uploadImages();
			break;
			case 'housingmarketreport':
				$this->sourceImage = "housingreport/pdf_performance";
				$this->destImage = "housingreport";
				$this->uploadImages();
			break;
			case 'wallofworry':
				$this->sourceImage = "lloyds-wall-of-worry";
				$this->destImage = "";
				$this->uploadImages();
			break;
			default:
				$this->sourceImage = "FCK_Jan2011";
				$this->destImage = "";
				$this->uploadImages();
			break;
		}
	}

	function uploadVideoAws($title,$file_name)
	{
		switch($title)
		{
			case 'mvtv_still':
					$this->sourceFile = "mvtv/stills/".$file_name;
					$this->destFolder = "mvtv/stills/";
					$this->uploadFiles();
			break;
			case 'mvtv_thumb':
					$this->sourceFile = "mvtv/thumbs/".$file_name;
					$this->destFolder = "mvtv/thumbs/";
					$this->uploadFiles();
			break;
			case 'mvtv_video':
					$this->sourceFile = "mvtv/videos/".$file_name;
					$this->destFolder = "mvtv/videos/";
					$this->uploadFiles();
			break;
			case 'mvtv_podcast':
					$this->sourceFile = "mvtv/videos/podcasting/".$file_name;
					$this->destFolder = "mvtv/videos/podcasting/";
					$this->uploadFiles();
			break;
		}
	}
	function synchBuzzFile()
	{
		$this->sourceFile = "assets/data/latest_post.txt";
		$this->destFolder = "assets/data/";
		$this->uploadFiles();
	}
	function uploadImages($source=NULL,$destination=NULL){
		global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
		switch($serverRsync){
			case "ec2-54-225-111-137.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$this->sourceImage.' minyanville@'.$serverS9PublicDns.':'.$D_R.'/assets/'.$this->destImage);

			break;
			case "ec2-54-225-111-153.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$this->sourceImage.' minyanville@'.$serverS8PublicDns.':'.$D_R.'/assets/'.$this->destImage);
			break;
		}
	}
	function uploadFiles(){
		global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
		switch($serverRsync){
			case "ec2-54-225-111-137.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/'.$this->sourceFile.' minyanville@'.$serverS9PublicDns.':'.$D_R.'/'.$this->destFolder);

			break;
			case "ec2-54-225-111-153.compute-1.amazonaws.com":
				shell_exec('rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/'.$this->sourceFile.' minyanville@'.$serverS8PublicDns.':'.$D_R.'/'.$this->destFolder);
			break;
		}
	}



	/*rsync upload single image*/

	function uploadAdminServerImages($type,$fileName){
		switch($type)
		{
			case 'dailyfeed':
				$this->sourceImageAdmin = "dailyfeed/uploadimage/".date('mdy')."/".$fileName;
				$this->destImageAdmin = "dailyfeed/uploadimage/".date('mdy');
				$this->uploadImagesAdmin();
			break;
			case 'bio_image':
				$this->sourceImageAdmin = "bios/".$fileName;
				$this->destImageAdmin = "bios";
			break;
			case 'article_audio':
				$this->sourceImageAdmin = "audio/".$fileName;
				$this->destImageAdmin = "audio";
				$this->uploadImagesAdmin();
				break;
			case 'buzz_logo':
				$this->sourceImageAdmin = "professorlogo/".$fileName;
				$this->destImageAdmin = "professorlogo";
				$this->uploadImagesAdmin();
			break;
			case 'branded_images':
				$this->sourceImageAdmin = "branded_images/".$fileName;
				$this->destImageAdmin = "branded_images";
				$this->uploadImagesAdmin();
			break;
			case 'buzz_charts_original':
				$this->sourceImageAdmin = "buzzbanter/charts/original/".date('mdy')."/".$fileName;
				$this->destImageAdmin = "buzzbanter/charts/original/".date('mdy');
				$this->uploadImagesAdmin();
			break;
			case 'buzz_charts_thumbnail':
				$this->sourceImageAdmin = "buzzbanter/charts/thumbnail/".date('mdy')."/".$fileName;
				$this->destImageAdmin = "buzzbanter/charts/thumbnail/".date('mdy');
				$this->uploadImagesAdmin();
			break;
			case 'slideshow':
				$this->sourceImageAdmin = "slideshow";
				$this->destImageAdmin = "";
				$this->uploadImagesAdmin();
			break;
			case 'housingmarketreport':
				$this->sourceImageAdmin = "housingreport/pdf_performance/".$fileName;
				$this->destImageAdmin = "housingreport/pdf_performance";
				$this->uploadImagesAdmin();
			break;
			case 'wallofworry':
				$this->sourceImageAdmin = "lloyds-wall-of-worry/".$fileName;
				$this->destImageAdmin = "lloyds-wall-of-worry";
				$this->uploadImagesAdmin();
			break;
			case 'peterTchir':
				$this->sourceImageAdmin = "peter-tchir/".$fileName;
				$this->destImageAdmin = "peter-tchir";
				$this->uploadImagesAdmin();
			break;
			case 'edu':
				$this->sourceImageAdmin = "edu/images/".$fileName;
				$this->destImageAdmin = "edu/images";
				$this->uploadImagesAdmin();
			break;
			case 'edu_video':
				$this->sourceImageAdmin = "edu/video/".$fileName;
				$this->destImageAdmin = "edu/video";
				$this->uploadImagesAdmin();
			break;
			default:
				$this->sourceImageAdmin = "FCK_Jan2011";
				$this->destImageAdmin = "";
				$this->uploadImagesAdmin();
			break;
		}


	}


	function uploadImagesAdmin($source=NULL,$destination=NULL){
		global $D_R,$serverRsync,$serverS8PublicDns,$serverS9PublicDns;
		switch($serverRsync){

				case "ec2-54-225-111-137.compute-1.amazonaws.com":
				try{
					/*s8.minyanville.com */
					$command='rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$this->sourceImageAdmin.' minyanville@'.$serverS9PublicDns.':'.$D_R.'/assets/'.$this->destImageAdmin;
					$stream=shell_exec($command);
					if(empty($stream)){
					   $this->setImageUploadErrorLog();
					}
				}catch(Exception $e)
				{
					$to="nidhi.singh@mediaagility.co.in";
					$from='support@minyanville.com';
					$subject="Error in uploading buzz chart on server s9.minyanville.com";
					$mesage = "Error in uploading buzz chart ".$this->sourceImageAdmin;
					mymail($to,$from,$subject,$mesage);
				}
				break;
				case "ec2-54-225-111-153.compute-1.amazonaws.com":
				try{
					/*s9.minyanville.com*/
					$command='rsync -avz --timeout=30 -e "ssh -p 16098 -i /home/sites/minyanville/.minyanville" '.$D_R.'/assets/'.$this->sourceImageAdmin.' minyanville@'.$serverS8PublicDns.':'.$D_R.'/assets/'.$this->destImageAdmin;
					$stream=shell_exec($command);
					if(empty($stream)){
					   $this->setImageUploadErrorLog();
					}
				}catch(Exception $e)
				{
					$to="nidhi.singh@mediaagility.co.in";
					$from='support@minyanville.com';
					$subject="Error in uploading buzz chart on server s8.minyanville.com";
					$mesage = "Error in uploading buzz chart ".$this->sourceImageAdmin;
					mymail($to,$from,$subject,$mesage);
				}
				break;

		}
	}


	function setImageUploadErrorLog(){
		        global $serverRsync;
			$params['imagesource']=$this->sourceImageAdmin;
			$params['imagedestination']=$this->destImageAdmin;
			$params['uploaded_on']=mysqlNow();
			$params['server']=$serverRsync;
			$params['sent']=0;
			$id=insert_query('image_upload_log',$params,$safe=0);

	}
}
?>