<?php

class mvAuthors{
    
function authorAuthentication($postData){
  $keyDecrypt='4u6rg1MA5S8eB0n';
  $token='buzzbanterPi1WhVZ1xj';
    $encryptedData=trim($postData['token']);
    $data=base64_decode($encryptedData);
    $decrypted_data =substr(trim(mcrypt_ecb(MCRYPT_BLOWFISH,$keyDecrypt, $data, MCRYPT_DECRYPT)),0,20);
  if($token==$decrypted_data){
        $type=$postData['type'];
	
	if(empty($type)){
            $result='Fail';	
	    $error='Invalid type';
	    $this->setResponse($result,$error);
	    
	}else{
	    $this->displayAuthors($type);    
	}
  }else{
            $result='Fail';	
	    $error='Invalid token';
	    $this->setResponse($result,$error);
  }
}

    
function displayAuthors($type){
    switch($type){
        case "all":
            $getSql="SELECT id,name FROM `contributors` WHERE suspended='0' ORDER BY id";
            
        break;
        case "buzzbanter":
            $getSql="SELECT C.id,C.name FROM contributors C, contributor_groups_mapping CGM,contributor_groups CG
WHERE C.id=CGM.contributor_id AND CGM.group_id=CG.id AND CG.group_name='Buzz & Banter' ORDER BY C.name";
            
        break;
        
    }
    
    $getResult=exec_query($getSql);
    if(!empty($getResult)){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	echo "<authorinfo>";
        foreach($getResult as $value){
		?>
		<author>
		    <authorid><?=$value['id'];?></authorid>
		    <authorname><?=$value['name'];?></authorname>
		</author>
            <?		
            
        }
	?>
	</authorinfo>
	<?
        
        
    }else{
        $result='Fail';	
	$error='No data found.';
	$this->setResponse($result,$error);
        
    }
    
}
  
    function setResponse($result,$error){
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		?>
		<info>
		    <result><?=$result;?></result>
		    <error><?=$error;?></error>
		</info>
    <?		
    }
    
}



?>