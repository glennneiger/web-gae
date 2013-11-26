<?php
global $IMG_SERVER,$HTPFX,$D_R,$HTHOST;
session_start();
include_once("$D_R/lib/json.php");
class regitrationProductData{

	function newThreeStepRegisteration() {
	global $HTPFX,$HTHOST,$_SESSION,$viaDefaultAddr;

		$this->SID = $_SESSION['SID'];
		$this->email=$_SESSION['new_register_account']['viauserid'];
		$this->firstname=$_SESSION['new_register_account']['firstname'];
		$this->lastname=$_SESSION['new_register_account']['lastname'];
		$this->country=$_SESSION['new_register_billing']['country'];
		$this->dailyDigest='1';
		$this->pwd=$_SESSION['new_register_account']['viapass'];

	$json = new Services_JSON();
	$userObj=new user();
	$viaEmailObj = new ViaEmail();

		// login information
		$loginInfo=array(
			'login'=>$this->email,
			'password'=>$this->pwd
		);
		$addresses=$viaDefaultAddr;
		$objVia = new Via();
		// integrate auxilary field
		$account_activated=1; /*set account activation to via- 0,1*/
		$auxInfo=$objVia->setAuxilaryFields($account_activated,$temp_orders="");

		// integrate customer information
		$custInfo=array(
			'loginInfo'=>$loginInfo,
			'addresses'=>$addresses,
			'email'=>$this->uid,
			'nameFirst'=>$this->firstname,
			'nameLast'=>$this->lastname,
			'email'=>$this->email,
			'auxFields'=>$auxInfo
		);
		// cart details with exchange
		$orderDetails=array();

		$orderDetails['OrderItem'][0]['orderClassId']=9;
		$orderDetails['OrderItem'][0]['orderCodeId']=9;
		$orderDetails['OrderItem'][0]['sourceCodeId']=1;
		$orderDetails['OrderItem'][0]['orderItemType']='SUBSCRIPTION';
		$orderDetails['OrderItem'][0]['typeSpecificId']=15;
		$orderDetails['OrderItem'][0]['price']=0;
		$orderDetails['OrderItem'][0]['paymentAccountNumb']=1;
		$orderDetails['OrderItem'][0]['qty']=1;

		$cartDetails=array(
			'billDate'=>date('Y-m-d'),
			'items'=>$orderDetails
		);
		// set user name and password
		$objVia->nameFirst=$this->firstname;
		$objVia->nameLast=$this->lastname;

		// send request to via
		// defined in /lib/_via_data_lib.php
		$hardtrial=1;
		$customerDetails=$objVia->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial);
		// via responded successfully
		if($customerDetails){
			$via_id=$objVia->customerIdVia;
			// insert record to minyanville db
			// defined in /lib/_via_data_lib.php
			$insertedId=$objVia->insertBasicUser($this->dailydigest);

			RegisterUser($insertedId);

			$qry= "select id from subscription where email='".$this->email."'";
			$rslt= exec_query($qry,1);

			$fbUserMapping['subscription_id'] = $rslt['id'];

			//$queryInsertFbUser= insert_query('fb_user',$fbUserMapping);

			/* Insert into ex_user_profile table */
			$subarray = array('subscription_id'=>$insertedId);
			$profileid = insert_query('ex_user_profile', $subarray);
			// login new user to system
			$loginInfo=$userObj->login($this->email,$this->pwd);
				// account created successfully
				$value=array(
					'status'=>true,
					'firstname'=>ucwords($this->firstname),
					'lastname'=>ucwords($this->lastname)
				);
			}

			// minyanville db insertion failed
			else{
				$errorObj=new ViaException();
				// message handling
				//echo "MVIL DB insertion failed";
				$errMessage=$errorObj->getExactCustomError($customerDetails);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request. Please check your data.';
				}

				$value=array(
					'status'=>false,
					'msg'=>$errMessage
				);
			}

		// generate array that can be used with js
		// defined in /lib/json.php
		//$output = $json->encode($value);
		//echo strip_tags($output);
			if($value['status']){
				$viaEmailObj->sendWelcomeEmail($this->firstname,$this->email,$this->pwd);
				location($HTPFX.$HTHOST);
				//return $this->autologin($this->fbId,1);
			}

		}

		function autologin($fbId, $isRegister){
		$objUser = new user();
		$json = new Services_JSON();
		set_sess_vars('userFacebookId',$this->fbId);
		/*if($_SESSION['SID']){
			return 'true';
		}*/
		// get via id from facebook id
		$strQuery="SELECT S.id as SID, S.via_id AS VIAID FROM subscription S,fb_user FU WHERE FU.fbuser_id='".$this->fbId."' AND S.id=FU.subscription_id ORDER BY S.id DESC";
		$result=exec_query($strQuery,1);
		if(!$result){
			return false;
		}
		else{
			// autologin user basis of his details
				$status=$objUser->loginByViaId($result['VIAID']);
				if($status){
					$result= array(
						'resultStatus' => true,
						'isRegister' => $isRegister
					);
					$output = $json->encode($result);
					return $output;
					//return true;
				}else{
					return false;
				}
			}
	}

	function setProductArr(){ ?>
	<script>
		var subdefArr = new Array();
		var index = 0;
	</script>
	<? foreach($_SESSION['viacart']['SUBSCRIPTION'] as $arSubProduct)
	{
		$subdefId = $arSubProduct['subscription_def_id'];
		?>
		<script language="javascript">
			subdefArr[index] = '<?=$subdefId?>';
			index++;
		</script>
	<?
	}
}

}

?>