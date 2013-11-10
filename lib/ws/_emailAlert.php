<?php
class emailAlert{
	function emailAlert($product){
		global $productOrder;
		$this->productName=$product;
		$this->productids=$productOrder[$product];
	}

	function sendEmail($subject,$body){
		
			if(strlen($body) <1)
			{
				$result['error']="Body of email message can't be blank";
				$this->displayResponse($result);
				return false;
			}elseif(strlen($body) <1){
				$result['error']="Subject of email message can't be blank";
				$this->displayResponse($result);
				return false;
			}
			global $D_R,$THESTOCKPLAYBOOK_FROM, $THESTOCKPLAYBOOK_ALERT_TMPL,$THESTOCKPLAYBOOK_FROM_NAME,$THESTOCKPLAYBOOK_FROM_EMAIL;
			include_once("$D_R/lib/_user_controller_lib.php");
			$from[$THESTOCKPLAYBOOK_FROM_EMAIL]= $THESTOCKPLAYBOOK_FROM_NAME;
			$subject=htmlentities($subject,ENT_QUOTES);
			$msgfile="/tmp/spam_".mrand().".eml";
			$msghtmlfile="$D_R/assets/data/".basename($msgfile);
			$postdata=array(title=>$subject,body=>$body);

			$crl = curl_init($THESTOCKPLAYBOOK_ALERT_TMPL);
			curl_setopt($crl, CURLOPT_POST , 1);
			curl_setopt ($crl, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt ($crl, CURLOPT_VERBOSE,1);
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($crl, CURLOPT_TIMEOUT, 60);
			$mailbody=curl_exec($crl);

			$userObj=new user();
			if(strtoupper($this->productName)=="THESTOCKPLAYBOOK"){
			$result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'THESTOCKPLAYBOOK');
			}elseif(strtoupper($this->productName)=="THESTOCKPLAYBOOKPREMIUM"){
				$result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'THESTOCKPLAYBOOKPREMIUM');
			}
			$this->displayResponse($result);
	}

	function displayResponse($result){
		echo "<?xml version=\"1.0\"?>";
?>
		<minyanville>
		<? if((!is_object($result) && isset($result['error']) ) or $result->CreateNewEmailMessageResult->Error){ ?>
			<result>FAIL</result>
		<? if($result['error']){ ?>
			<error><?=$result['error'];?></error>
		<? } ?>
			<error>There was an error in sending email to subscribers</error>
		<? }else{ ?>
			<result>OK</result>
			<emailAlert>
				<EmailId><?=$result->CreateNewEmailMessageResult->EmailBodyID;?></EmailId>
				<TotalRecipients><?=$result->CreateNewEmailMessageResult->TotalRecipients;?></TotalRecipients>
				<body><![CDATA[<?=$result->CreateNewEmailMessageResult->Body;?>]]></body>
			</emailAlert>
<?	} ?>
	</minyanville>
<?

	}
}

?>
