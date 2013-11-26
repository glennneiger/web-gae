<?php
ini_set('magic_quotes_runtime',0);
global $D_R;
include_once($D_R."/lib/_includes.php");
include_once($D_R."/lib/_user_data_lib.php");
include_once($D_R."/lib/recurly/_recurly_data_lib.php");
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
include_once($D_R."/lib/config/_products_config.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R.'/lib/config/_syndication_config.php');
include_once($D_R."/lib/recurly/recurly.php");
include_once($D_R."/lib/config/_coreg_config.php");
include_once($D_R."/lib/config/_email_config.php");

global $D_R,$recurlyApiKey,$privateKey,$coRegList,$confCompany;
Recurly_Client::$apiKey = $recurlyApiKey;
$objUserData= new userData();

foreach($coRegList as $key){
	switch($key){
		case "moneyShow":
			$moneyShowHost = $confCompany['moneyShow']['host'];
			$moneyShowUser = $confCompany['moneyShow']['loginUser'];
			$moneyShowPass = $confCompany['moneyShow']['loginPwd'];
			$moneyShowFilePath = $confCompany['moneyShow']['filePath'];
			$moneyShowPort = $confCompany['moneyShow']['port'];
			$moneyShowBackup = $confCompany['moneyShow']['backupPath'];
			//$moneyShowFileName = $confCompany['moneyShow']['fileName'];
			break;

		case "zacks":
			$moneyShowHost = $confCompany['zacks']['host'];
			$moneyShowUser = $confCompany['zacks']['loginUser'];
			$moneyShowPass = $confCompany['zacks']['loginPwd'];
			$moneyShowFilePath = $confCompany['zacks']['filePath'];
			$moneyShowPort = $confCompany['zacks']['port'];
			$moneyShowBackup = $confCompany['zacks']['backupPath'];
			//$moneyShowFileName = $confCompany['zacks']['fileName'];
			break;
		case "optIntelligence":
			$moneyShowHost = $confCompany['optIntelligence']['host'];
			$moneyShowUser = $confCompany['optIntelligence']['loginUser'];
			$moneyShowPass = $confCompany['optIntelligence']['loginPwd'];
			$moneyShowFilePath = $confCompany['optIntelligence']['filePath'];
			$moneyShowPort = $confCompany['optIntelligence']['port'];
			$moneyShowBackup = $confCompany['optIntelligence']['backupPath'];
			//$moneyShowFileName = $confCompany['optIntelligence']['fileName'];
			break;

		case "investingMedia":
			$moneyShowHost = $confCompany['investingMedia']['host'];
			$moneyShowUser = $confCompany['investingMedia']['loginUser'];
			$moneyShowPass = $confCompany['investingMedia']['loginPwd'];
			$moneyShowFilePath = $confCompany['investingMedia']['filePath'];
			$moneyShowPort = $confCompany['investingMedia']['port'];
			$moneyShowBackup = $confCompany['investingMedia']['backupPath'];
			//$moneyShowFileName = $confCompany['investingMedia']['fileName'];;
			break;
	}
	$conn_id = ftp_connect($moneyShowHost);
	//$conn_id=1;
	if($conn_id){
	$loginResult = ftp_login($conn_id, $moneyShowUser, $moneyShowPass);

	//$moneyShowFilePath=$D_R."/assets/moneyshow/";

	$ftpDirPath='ftp://'.$moneyShowUser.':'.$moneyShowPass.'@'.$moneyShowHost.$moneyShowFilePath;
	//$ftpDirPath=$moneyShowFilePath;
	$moneyShowFileName="";

	if(is_dir($ftpDirPath)) {
	      if($dh = opendir($ftpDirPath)) {
	        while(($fileName = readdir($dh)) !== false) {
				$fileExt = preg_replace('/^.*\.([^.]+)$/D', '$1', $fileName);
				$getfileExt=strtolower($fileExt);
				if($getfileExt=="csv"){
					$moneyShowFileName=$fileName;
				}
			}
		 }

	    if(!empty($moneyShowFileName)){
		$ftpPath='ftp://'.$moneyShowUser.':'.$moneyShowPass.'@'.$moneyShowHost.$moneyShowFilePath.$moneyShowFileName;
	    //$ftpPath=$moneyShowFilePath.$moneyShowFileName;

		$filename = fopen($ftpPath, "r");
	    if($filename)
	     {
		    $i=0;
	     	while (($data = fgetcsv($filename,0, ",")) !== FALSE)
	         {
					 if($i==0){
						foreach($data as $key=>$value){
							 switch(trim($value)){
							 case "Last Name" :
								$lastNameKey=$key;

							 break;
							 case "First Name" :
								$firstNameKey=$key;

							 break;
							 case "Address" :
								$addressKey=$key;

							 break;
							 case "City" :
								$cityKey=$key;

							 break;
							 case "State" :
								$stateKey=$key;

							 break;
							 case "Zip Code" :
								$zipCodeKey=$key;

							 break;
							 case "Email Address" :
								$emailKey=$key;

							 break;
						 }
					 }

				 }

				if($i>="1"){

				     if($data[$emailKey]!=""){
						 $email=$data[$emailKey];
						 $pwd="minyan";
						 $firstname=$data[$firstNameKey];
						 $lastname=$data[$lastNameKey];
						 $address=(!empty($data[$addressKey]))?$data[$addressKey]:"257 Park Avenue";
						 $city=(!empty($data[$cityKey]))?$data[$cityKey]:"New York City";
						 $state=(!empty($data[$stateKey]))?$data[$stateKey]:"NY";
						 $zipCode=(!empty($data[$zipCodeKey]))?$data[$zipCodeKey]:"10010";
						 $promocode="MONEY";
						 $rememeber="1";
						 $objUserData->moneyShowUserRegister($email,$firstname,$lastname,$address,$city,$state,$zipCode);
					 }
				 }

			$i++;
			}
			 fclose($filename);

			 /*backup file*/

			 $chkftp=ftpPut($ftpPath, $moneyShowUser, $moneyShowPass, $moneyShowHost,$moneyShowFilePath.$moneyShowBackup);
			 if($chkftp){
			 	echo "<br>"."File copy to backup folder";
				if($moneyShowFileName){
					if(ftp_delete($conn_id,$moneyShowFilePath.$moneyShowFileName)) {
					 echo "<br>"."$moneyShowFileName deleted successfully\n";
					} else {
					 echo "<br>"."could not delete $moneyShowFileName\n";
					}
				}

			 }
	     }
	     else
	     {
	         echo "<br>"."Invalid File or File not exist at ftp site";
	     }

	}

		}else{
				 echo "<br>"."Invalid Directory or directory not exist";

		}

	}else{

	echo "<br>"."FTP connection failed.";

	}
}

?>