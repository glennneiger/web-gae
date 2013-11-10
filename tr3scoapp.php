<?php
global $D_R;
include_once($D_R."/lib/config/_tr3sco_config.php");
include_once($D_R."/lib/_tr3sco_data_lib.php");
include_once($D_R."/lib/_user_data_lib.php");
include_once($D_R."/lib/registration/_register_funnel_data_lib.php");
require_once($D_R."/lib/recurly/recurly.php");
include_once($D_R."/lib/config/_recurly_config.php");
include_once($D_R."/lib/_user_controller_lib.php");
include_once ($D_R."/lib/recurly/_recurly_data_lib.php");
$objData = new userData();
$objUser = new User();
$objTr3sco= new tr3scoData();
$objFunnelData = new registrationFunnelData();
Recurly_js::$privateKey = $privateKey;
Recurly_Client::$apiKey = $recurlyApiKey;
$recurlyData = new recurlyData();

global $tr3scoAppEncryptionKey;

$partner = substr($_GET['val'], 0, 11);
if($partner=="TR3SCO_223_")
{

	$data = substr($_GET['val'], 11,strlen($_GET['val']));
	$data = str_replace(" ","+",$data);
	$decryptedData = base64_decode($data);
	$decrypted_data = mcrypt_ecb(MCRYPT_BLOWFISH,$tr3scoAppEncryptionKey, $decryptedData, MCRYPT_DECRYPT);

	$decrypted_data = explode('&',$decrypted_data);

	foreach($decrypted_data as $key=>$val)
	{
		$dataArr = explode("=",$val);
		$userArr[$dataArr[0]]=$dataArr[1];
	}
	if(!empty($userArr))
	{
		if(strtolower($userArr['Action'])=="login")
		{
			$userExist = $objUser->checkUserAvailablity($userArr['User_Email_Id']);
			if(!empty($userExist))
			{
				$userData = $objData->CustomerAuthentication($userArr['User_Email_Id'],$userArr['User_Pass']);
				if(!empty($userData))
				{
					$productRes = $objTr3sco->getBuzzInfo($userData['id']);
                    $result['status']='1';
                    $product['buzz']=$productRes['buzz']['status'];
                    $result['product'] = $product;
                    $result['message']=$productRes['buzz']['message'];
				}
				else
				{
					$result['status']="0";
					$result['message']="Wrong Password";
				}
			}
			else
			{
				$result['status']="0";
				$result['message']="Your email ID does not exist. Please email support@minyanville.com, or call 888-489-4880 to continue using Buzz & Banter.";
			}
		}
		else if(strtolower($userArr['Action'])=="registration")
		{
			$userData = $objUser->checkUserAvailablity($userArr['User_Email_Id']);
			if(empty($userData))
			{
				$subId=$objFunnelData->getAccountCode();
				$accountSuccess = $recurlyData->createAccount($subId,$userArr['User_Email_Id'],$userArr['User_Email_Id'],'','','','');
				 if(empty($userArr['User_Pass']))
				 {
				 	$pass="minyan";
				 }
				 else {
				 	$pass=$userArr['User_Pass'];
				 }
			     $password=$objData->encryptUserPassword($pass);
			     $userdata=array('email'=>$userArr['User_Email_Id'],'account_status'=>'enabled','password'=>$password,'recv_daily_gazette'=>'0','id'=>$subId);
			    $insertid = insert_query("subscription",$userdata);
				if(!empty($insertid))
			    {
			    	$addPlan = $objTr3sco->addSubscription($insertid);
			    	if($addPlan>0)
			    	{
			    		$result['status']="1";
			    		$result['message']="Registration was successful and now you can login and access Buzz & Banter trial for 60 days";
			    	}
			    	else
			    	{
			    		$result['status']="0";
			    		$result['message']="Registration was successful. But the product could not be added";
			    	}
			    }
			    else
			    {
			    	$result['status']="0";
			    	$result['message']="An Error Has Occurred. Please email support@minyanville.com, or call 888-489-4880 to continue using Buzz & Banter.";
			    }
			}
			else
			{
				$result['status']="0";
				$result['message']="Your email ID already exists. Please email support@minyanville.com, or call 888-489-4880 to continue using Buzz & Banter.";
			}
		}
		else
		{
			$result['status']="0";
			$result['message']="Enter a valid Action";
		}
		echo json_encode($result);
	}
}
?>