<?php
/***
$this->getMessage()
final function getMessage();                // message of exception
final function getCode();                   // code of exception
final function getFile();                   // source filename
final function getLine();                   // source line
final function getTrace();                  // an array of the backtrace()
final function getTraceAsString();          // formated string of trace
**/
class userException extends Exception{
private $errorsresultset;
	public function __construct($message=null, $code = 0) {
		// some code
        // make sure everything is assigned properly
		$this->apierrors();
        parent::__construct($message, $code);
    }

	// custom string representation of object
    public function __toString() {
	    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

	public function apierrors($errorstring=null)
	{
		$objCache=new Cache();
		$resultGetViaErrors=$objCache->getViaErrors();
		$errorsresultsets=array();
		foreach($resultGetViaErrors as $key=>$value){
			$errorsresultsets[$value['errorCode']][$value['errorName']]=$value['customemessage'];
		}
		//echo $errorsresultset['CustomerAddOrderAddPaymentAdd failed']['Customer login already in use'];
		$this->errorsresultset=$errorsresultsets;
		return $errorsresultsets;
	}

	/** Aswini **/
	function getExactCustomError($errstr)
	{
		$message=explode('~',$errstr);
		if(($message[1]==''))
		{
			$message[1]=trim($message[0]); // as we are saving in db Invalid Customer Login Invalid Customer Login
		}
		return trim($this->errorsresultset[trim($message[0])][trim($message[1])]);
	}

	public function userLogTransaction($operation,$request,$response,$email=NULL,$viaid=NULL){
		$transactiondate=date('Y-m-d H:i:s');
		global $dbLogsservwrite,$dbLogsuserwrite,$dbLogspasswrite,$dbLogsnamewrite;
		$tmpDBObject=get_connection($dbLogsservwrite,$dbLogsuserwrite,$dbLogspasswrite,$dbLogsnamewrite);

		$request = $operation.'\n'.$request; // to be stored in transaction_logs.request field
		$success=1; // to be stored in transaction_logs.success field

		$logdata=array(
					   'user_id'=>$viaid,
					   'request'=>addslashes($request),
					   'response'=>addslashes($response),
					   'transaction_date'=>$transactiondate,
					   'success'=>$success,
					   'error_msg'=>$error_msg,
					   'email'=>$email,
					   'ip_address'=>$_SERVER['REMOTE_ADDR'],
					   'from_url'=>$_SERVER['PHP_SELF']
						);
						insert_query_logs("transaction_logs", $logdata);
		unset($tmpDBObject);
	}
	/** Aswini **/
	public function parseapierrors($errorstring,$operation,$viaRequest)
	{
		global $_SESSION,$D_R,$HTADMINHOST,$HTPFX;
		global $errViaMessage;
		global $dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite;
		$tmpDBObject=get_connection($dbservwrite,$dbuserwrite,$dbpasswrite,$dbnamewrite);

		$errViaMessage=$errorstring->message;
		$res = explode("--->",$errorstring->message);
		$raw_errorstr=$res[1]; // raw string data
		$splitedarray = explode("at",$raw_errorstr);
		$exacterror = explode("\.|:",$splitedarray[0]);
		$errorcode=$exacterror[0];
		$errorName=$exacterror[2];
		$erros = trim(trim($errorcode)."~".trim($errorName));
		$originalerrorresponse = $errorstring->message; // to be stored in transaction_logs.response field
		$request = "OP For: ".$operation; // to be stored in transaction_logs.request field
		$request .=" ".$viaRequest;
		$transactiondate = date('Y-m-d H:i:s'); // to be stored in transaction_logs.transaction_date field
		$success=0; // to be stored in transaction_logs.success field
		$error_msg = $this->getExactCustomError($erros); // to be stored in transaction_logs.error_msg field
		$logdata=array(
					   request=>$request,
					   response=>mysql_real_escape_string($originalerrorresponse,$tmpDBObject),
					   transaction_date=>mysql_real_escape_string($transactiondate,$tmpDBObject),
					   success=>$success,
					   email=>$_SESSION['email'],
					   'user_id'=>$_SESSION['viaid'],
					   error_msg=>$error_msg,
					   'ip_address'=>$_SERVER['REMOTE_ADDR'],
					   'from_url'=>$_SERVER['PHP_SELF']
						);
		$logid=insert_query_logs("transaction_logs", $logdata);

// Send mvil group a failure email
   global $HTPFX,$HTHOST,$CREDIT_CARD_PAYMENT_ERROR_FROM,$MINYANVILLE_ERROR_EMAIL,$HTADMINHOST;
   $MINYANVILLE_EMAIL='subscriptions@minyanville.com';
   $VIAERROR_EML_TMPL=$HTPFX.$HTADMINHOST."/emails/_eml_transactionfail_alert.htm";

  // $HTADMINHOST

   if($_SESSION['email']!='')
   {
	   $subject="Via Subs Error - ".$_SESSION['email'].' - '.$error_msg;
   }
   else
   {
	   $subject="Via Subs Error - ".$error_msg;
   }

   $msgfile="/tmp/spam_viaerror_".mrand().".eml";   // mrand function in _misc.php give getmicrotime();
   $msghtmlfile="$D_R/assets/data/".basename($msgfile);
   $mailbody=inc_web("$VIAERROR_EML_TMPL?log_id=$logid");
   write_file($msghtmlfile,$mailbody);

   mymail($MINYANVILLE_ERROR_EMAIL,$CREDIT_CARD_PAYMENT_ERROR_FROM,$subject,inc_web("$VIAERROR_EML_TMPL?log_id=$logid"));
   return $erros;
}
}
?>
