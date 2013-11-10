<?
class user_test
{
	public $viaObj=null;
	public $userDetails;
	public function user_test($userid=null,$pwd=null)
	{
	}
	public function initSoapObject(){
		$this->viaObj=new Via();
	}
	
	public function login($uid,$pwd,$autologin=null)
	{
		global $_COOKIE,$BANTER_ON,$_SESSION;
		$this->email=lc(trim($uid));
		$this->password=trim($pwd);
		if(!$this->email || !$this->password)
		{
			set_sess_vars('isAuthed',0);
			return 0;
		}
		try
		{
			$this->initSoapObject();
			if(isset($uid) && isset($pwd) && isset($this->viaObj))
			{
				$userDetails=$this->viaObj->authenticateCustomer($uid,$pwd);
				if(is_object($userDetails))
				{
					$email = $userDetails->CustomerAuthenticationResult->customer->loginInfo->login;
					$via_id = $userDetails->CustomerAuthenticationResult->customerId;
					/* get the subscription table id for this viaid */
					
					$arrsubscription = array('via_id'=>$via_id);
					$subps_up = update_query(subscription,$arrsubscription,array('email'=>$email));
					$strQuery="select id from subscription where via_id=".$via_id;
					//$strQuery="select id from subscription where email='".$email."'";
					$resultUpdate=exec_query($strQuery,1);
					
					$final = "sub id=".$resultUpdate['id']."....Via ID:".$via_id;
				}
				else
				{						
					$final = "Login Error";
				}
			}
		}
		catch (Exception $exception)
		{
			$final =  "Via Error".$exception;
		}
		echo $final."<br>";
	}
}

$stQuery = "SELECT email,password FROM subscription WHERE ISNULL(via_id) LIMIT 0,100";
$result=exec_query($stQuery);
foreach($result as $row)
{
	$obj = new user_test();
	$obj->login($row['email'],$row['password']);
}
?>