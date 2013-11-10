<?php

class sendBuzzSandBoxEmail{
    
function buzzBanterSandBoxDataEncryption($postData){
  $keyDecrypt='9x8bjT555Hlm4k1sykj2R';
  $keydataCheck='Lt6Lc0wBLy1TV52S306Pnt';
    $encryptedData=trim($postData['keydataCheck']);
    $data=base64_decode($encryptedData);
    $decrypted_data =substr(trim(mcrypt_ecb(MCRYPT_BLOWFISH,$keyDecrypt, $data, MCRYPT_DECRYPT)),0,22);
   // htmlprint_r($decrypted_data);
  if($keydataCheck==$decrypted_data){
        $from=$postData['from'];
	$to=$postData['to'];
	$subject=trim(stripslashes($postData['subject']));
	$mailBody=$postData['mailBody'];
	
	if(empty($from)){
	    $result='Fail';
	    $error='From address not found';
	   $this->setResponse($result,$error);
	}elseif(empty($to)){
	    $result='Fail';
	    $error='To address not found';
	   $this->setResponse($result,$error);
	    
	}elseif(empty($subject)){
	    $result='Fail';
	    $error='Subject not found';
	    $this->setResponse($result,$error);
	}elseif(empty($mailBody)){
	    $result='Fail';
	    $error='Mailbody not found';
	    $this->setResponse($result,$error);
	}else{
	    $this->sendBuzzBanterSandBoxEmail($from,$to,$subject,$mailBody);    
	}
  }else{
            $result='Fail';	
	    $error='Invalid Key';
	    $this->setResponse($result,$error);
  }
}


    

function sendBuzzBanterSandBoxEmail($from,$to,$subject,$mailBody){
	global $D_R;
	//$from[$fromSoftTrialWelcomeEmail]=$fromSoftTrialWelcomeEmail;
	/*require_once $D_R.'/lib/swift/lib/swift_required.php';
	$mailer = Swift_MailTransport::newInstance();
	$message = Swift_Message::newInstance();
	$message->setSubject($subject);
	$message->setBody($mailBody, 'text/html');
	$message->setSender($from);
	$message->setTo($to);*/
	$getSenddata= mymail($to,$from,$subject,$mailBody);
	//$getSenddata=$mailer->send($message);
	if(!$getSenddata || empty($getSenddata)){
	    $result='Fail';
	    $error='Unable to send email';
	    $this->setResponse($result,$error);
	}else{
	    $result='Success';
	    $error='';
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