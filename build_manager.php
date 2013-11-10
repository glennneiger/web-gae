<?
global $D_R;
include_once("$D_R/lib/config/_release_script.php");
include_once("$D_R/release_script.php");


class buildManager
{
	function buildManager($enviroment,$value,$cleanCpy)
	{
		global $qaServer,$qaUser,$qaPwd,$qaDirUser,$qaDirPwd;
		global $serverIp,$serverUser,$serverpwd,$serverDirUser,$serverDirPwd;
		
		$this->getServerDetails($enviroment);
		$this->createBackUp($value,$cleanCpy);
		$this->rollUp($value);
	}
	
	function getServerDetails($enviroment)
	{
		global $dirPath,$qaMinyanville,$qaServer,$qaUser,$qaPwd,$qaDirUser,$qaDirPwd; 
		global $serverIp,$serverUser,$serverpwd,$serverDirUser,$serverDirPwd;
	
		if($dirPath==$qaMinyanville || $qaBuzzBanter){
			$serverIp = $qaServer;
			$serverUser = $qaUser;
			$serverpwd = $qaPwd;
			$serverDirUser = $qaDirUser;
			$serverDirPwd = $qaDirPwd;
		}
	}
	
	function createBackUp($value,$cleanCpy)
	{
		global $fileName,$dirPath;
		$fileList = explode("\n",$fileName);
		//echo '<br>list..';
		//htmlprint_r($fileList);
		shell_exec("cd ".$dirPath);
		foreach($fileList as $key=>$value)
		{
			$value = trim($value);
			//htmlprint_r($value);
			if(!empty($value))
			{
				//echo "<br>copy " .$value.' '. $value."." .date(mdY);
				$new = $dirPath.'/'.$value;
				if(!file_exists($new))
				{
					//echo '<br>new File!!!!';
					//echo '****** file.....'.$value;
				}
				else
				{
					$exist = $dirPath.'/'.$value."." .date(mdY);
					if($cleanCpy=='0'){
						if(!file_exists($exist))
						{
							//echo '<br>notexist';
							shell_exec("copy " .$value.' '. $value."." .date(mdY));
						}else{
							//echo '<br>exist';
							shell_exec("copy " .$value.' '. $value."." .date(mdY).'.'.rand(10,99));
						}
					}else{
						if(!file_exists($exist))
						{
							//echo '<br>notexist';
							shell_exec("move " .$value.' '. $value."." .date(mdY));
						}else{
							//echo '<br>exist';
							shell_exec("move " .$value.' '. $value."." .date(mdY).'.'.rand(10,99));
						}
					}
				 }
			}
		}
	}
	
	function rollUp($value){
		global $value;
		shell_exec("cvs update -d ".$value);
	}
	
	/*function rollBack(){
	
		
	}*/
	
}

?>