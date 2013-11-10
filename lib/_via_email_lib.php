<?php
include_once("$D_R/lib/_via_exceptionhandler.php");
   class ViaEmail{
	protected $viaUrl=null;
	private $merchantLogin=null;
	private $merchantPassword=null;
	private $soapobj=null;

	public function __construct()
	{
		global $viaEmailUrl,$emailmerchantLogin,$emailmerchantPassword;
		try
		{
			$this->viaUrl=$viaEmailUrl;//'http://wspro.viaemanager.net/Email/v1.0/Email.asmx?mid=1901';
			$this->merchantLogin=$emailmerchantLogin;
			$this->merchantPassword=$emailmerchantPassword;
		}
		catch (Exception $objException)
		{
			echo "<br><font color='red'>Error Occurred.... Cannot connect with Web Service.</font><br>";
			exit;
		}
	}

	public function getMerchantIdent()
	{
		global $viaMerchantID;
		$merchantIdent=array(
		'MerchantID'=>$viaMerchantID,
		'UserName'=>$this->merchantLogin,
		'Password'=>$this->merchantPassword
		);
		return $merchantIdent;
	}
	protected function init(){
		$this->soapobj = new SoapClient( $this->viaUrl . "&WSDL", array('trace' => true, 'location' => $this->viaUrl));
		return $this->soapobj;
	}

	private function sendemail($message,$orderCodeIds)
	{
		global $D_R,$HTPFX,$HTHOST;

		$plan_code_list = implode("','",$orderCodeIds);

		$qry = "SELECT sco.subscription_id,sub.`email`,sub.`fname`,sub.`lname` FROM `subscription_cust_order` sco
LEFT JOIN `subscription` sub ON sub.`id`=sco.subscription_id
WHERE sco.recurly_plan_code IN ('".$plan_code_list."')
AND sco.recurly_current_period_ends_at >= DATE_FORMAT(now(),'%Y-%m-%d') AND sco.subscription_id <>'0'";
		$getResult=exec_query($qry,0);
		foreach($getResult as $key=>$val)
		{
			$email_listArr[] = $val['email'];
		}
		if(!empty($email_listArr))
		{
			$email_list = implode(',',$email_listArr);
		}

		$toemail = $email_list;
	 	$from = $message['FromAddress']; //  "support@minyanville.com";
	 	$subject= $message['Subject'];
	 	$body=$message['Body'];

	 	mymail($toemail,$from,$subject,$body);

	}

	public function emailDetails($from,$subject,$mailbody,$product){
		global $productOrder;
		$yesterday = date("Y-m-d\TH:i:s", strtotime("-3 hour", strtotime(date("Y-m-d H:i:s"))));
		$message=array(
					   'Name'=>'Minyanville Support',
					   'SubmitDate'=>$yesterday,
					   'Subject'=>$subject,
					   'Body'=>$mailbody,
					   'FromAddress'=>$from,
					   'IsHTML'=>true,
					   'IsTemplate'=>false
						);

		$orderCodeIds=explode(",",$productOrder[$product]);

		return $this->sendemail($message,$orderCodeIds);
	}

	function sendSoftTrialActivationEmail($fname,$viaid,$email,$activate='',$dfuser,$secId=NULL){
		global $softtrialActivationemail,$softtrailActivationSubject,$softtrialActivationFrom,$softtrialAfterActivationemail,$softtrailAfterActivationSubject;
		/* 1- dailyfeed user 2- topick alert 3- dailydigest*/
		$strurl = 'fname=' . $fname . '&code=' . md5($viaid) . '&email=' . $email. '&dfuser=' .md5($dfuser);
		if($dfuser=="2" && $secId!="")
		  {
		  $strurl = 'fname=' . $fname . '&code=' . md5($viaid) . '&email=' . $email. '&dfuser=' .md5($dfuser).'&sec_id='.$secId;
		  }
		if($viaid!='')
		{
			if($activate){
				mymail($email,$softtrialActivationFrom,$softtrailAfterActivationSubject,inc_web("$softtrialAfterActivationemail?$strurl"));
			}else{
				mymail($email,$softtrialActivationFrom,$softtrailActivationSubject,inc_web("$softtrialActivationemail?$strurl"));
			}
		}
	}


	function sendWelcomeEmail($fname,$email,$password){
		global $welcomeSubject,$welcomeTemplate,$softtrialActivationFrom;
		$strurl = 'firstname=' . $fname . '&email=' .$email. '&pwd=' . $password;
		mymail($email,$softtrialActivationFrom,$welcomeSubject,inc_web("$welcomeTemplate?$strurl"));
	}


	function sendSoftTrialRegistrationErrorEmail($email,$fname,$lname,$error){
	  global $tmplSoftTrialRegisterError,$fromSoftTrialErrorEmail,$toSoftTrialErrorEmail,$subjectSoftTrialErrorEmail;
	  $emlTemplate=$tmplSoftTrialRegisterError."?email=".$email.'&fname='.$fname.'&lname='.$lname.'&error='.urlencode($error);
	  mymail($toSoftTrialErrorEmail,$fromSoftTrialErrorEmail,$subjectSoftTrialErrorEmail,inc_web("$emlTemplate"));

	}

	function sendSofttrialWelcomeEmail($email,$pwd,$fname){
		global $fromSoftTrialWelcomeEmail,$subjectSoftTrialWelcomeEmail,$tmplSoftTrialWelcome;
		$emlTemplate=$tmplSoftTrialWelcome."?email=".$email.'&fname='.$fname.'&pwd='.$pwd;
		if($email){
        	mymail($email,$fromSoftTrialWelcomeEmail,$subjectSoftTrialWelcomeEmail,inc_web("$emlTemplate"));
		}

	}

	function sendSofttrialSemBuzzWelcomeEmail($email,$pwd,$fname){
		global $softtrialSemBuzzFrom,$welcomeSemBuzzSubject,$welcomeSemBuzzTemplate;
		$emlTemplate=$welcomeSemBuzzTemplate."?email=".$email.'&fname='.$fname.'&pwd='.$pwd;
		if($email){
		    mymail($email,$softtrialSemBuzzFrom,$welcomeSemBuzzSubject,inc_web("$emlTemplate"));
		}

	}

	function sendOneWeekBuzzTrialWelcomeEmail($email,$pwd,$fname){
		global $softtrialOneWeekBuzzFrom,$welcomeOneWeekBuzzSubject,$welcomeOneWeekBuzzTemplate;
		$emlTemplate=$welcomeOneWeekBuzzTemplate.'?email='.urlencode($email).'&fname='.urlencode($fname).'&pwd='.urlencode($pwd);
		if($email){
		    mymail($email,$softtrialOneWeekBuzzFrom,$welcomeOneWeekBuzzSubject,inc_web("$emlTemplate"));
		}

	}

}
?>