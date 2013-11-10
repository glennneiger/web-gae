<?
/**************************************
Script to download files from FTP serevr
**************************************/
set_time_limit(3600);

 $localserverpath = $D_R."/".$Video_file_path; 

//make FTP connection

$ftpconn =  ftp_connect($addr_ftp);    // ftp connection 
if($ftpconn){	
	$ftpserverpath = ftp_pwd($ftpconn);
	$ftplogin =  ftp_login($ftpconn,$user_ftp,$pwd_ftp);   //ftp login
	if($ftplogin){	
		$data = array();
		//ftp_chdir($ftpconn,'Minyanville');echo ftp_pwd($ftpconn);
		$filescount = ftp_nlist($ftpconn, './'); // ftp server files list	or count array 
		///echo '<pre>'	;	print_r($filescount);die();
		$data['uploaded_by'] = '(automated)';
		$data['creation_time'] = Date("Y-m-d H:i:s") ;	
		$data['toddvideo'] = '1' ;	
		for($ftpcp = 0 ;$ftpcp <count($filescount); $ftpcp++){
			$titlearr =  explode(".",$filescount[$ftpcp]);
			if($titlearr['0']!='processed'){
				$data['title']  = $titlearr['0']."_".Date("Ymd");
				$data['videofile']= $VIDEO_SERVER."/".$Video_file_path.$filescount[$ftpcp];
				$localserverfilename = $localserverpath.$filescount[$ftpcp];
				if (ftp_get($ftpconn, $localserverfilename, $filescount[$ftpcp], FTP_BINARY)) {
					echo "<pre>Successfully written to $localserverfilename \n";			 
					$inserid = insert_query("mvtv",$data);
					
						$filesprocess = "processed/".$filescount[$ftpcp];							
						if(ftp_rename ($ftpconn,$filescount[$ftpcp],$filesprocess)){								
							 echo "<pre>$filescount[$ftpcp] is moved successfully to Processed directory";
						}else{
							echo "There was a problem in $filescount[$ftpcp] file transfer to proccessed directory\n";
						}	 
					
				} else {
					echo "There was a problem in $filescount[$ftpcp] file upload\n";
					//die();
				} 
			}
		}

	}else{
		Echo "Sorry, FTP login failed for user $user_ftp";
	}
}else{
	Echo "Sorry, FTP server not found";
}
ftp_close($ftpconn);



?>