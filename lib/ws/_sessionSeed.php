<?php
session_start();
class sessionSeed{
	function sessionSeed($CDI,$vendorId=NULL,$enableHash=TRUE){
		$this->CDI=$CDI;
		$this->vendorId=$vendorId;
		if(strlen($this->CDI)>'11'){
			$this->enableHash=$enableHash;
		}else{
			$this->enableHash='';
		}
	}

	function isActiveSession(){
		$qryGetActiveSession="SELECT UA.id,UA.products,CONCAT(S.fname,' ',S.lname) NAME,S.fname, S.lname,S.email FROM user_audit UA,subscription S
		WHERE UA.subid=S.id AND TIME >UNIX_TIMESTAMP('".mysqlNow()."' - INTERVAL 8 HOUR) AND duration IS NULL";
		if($this->enableHash){
			$qryGetActiveSession.=" AND MD5(subId)='".$this->CDI."'";
		}else{
			$qryGetActiveSession.=" AND subId='".$this->CDI."'";
		}
		$resGetActiveSession=exec_query($qryGetActiveSession,1);
		if(count($resGetActiveSession)>0){
			return $resGetActiveSession;
		}else{
			return FALSE;
		}
	}

	function displayResponse()
	{
		$usrSession=$this->isActiveSession();
		$allowedProducts=explode(",",$usrSession['products']);
		echo "<?xml version=\"1.0\"?>";
?>
		<minyanville>
		<? if($usrSession==FALSE){ ?>
			<result>FAIL</result>
			<error>User session not found</error>
			<errorPage><path>http://www.minyanville.com/error/?403</path></errorPage>
		<? }else{ ?>
			<result>OK</result>
			<sessionSeed>
				<custInfo>
					<email><?=$usrSession['email'];?></email>
					<name>
						<first><?=$usrSession['fname'];?></first>
						<last><?=$usrSession['lname'];?></last>
					</name>
				</custInfo>
				<custProducts>
					<? foreach($allowedProducts as $product){ ?>
					<ProductId><?=$product?></ProductId>
					<? } ?>
				</custProducts>
			</sessionSeed>
<?	} ?>
	</minyanville>
<?
	}

	function displayMagnifyResponse()
	{
		$usrSession=$this->isActiveSession();
		if($usrSession){
		$handle  = $usrSession['fname']." ".$usrSession['lname'];
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		?>
		<userinfo>
			<id><?=$this->CDI;?></id>
			<handle><?=$handle;?></handle>
			<email><?=$usrSession['email'];?></email>
			<name>
				<first><?=$usrSession['fname'];?></first>
				<last><?=$usrSession['lname'];?></last>
			</name>
			<photo></photo>
		</userinfo>
	<?php
		}
		else
		{
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
			echo "<error>Invalid login</error>";
		}
	}
}
?>
