<?php
/******************************************
This class contains functions to communicate with
Via web service.
*******************************************/
include_once("$D_R/lib/_via_exceptionhandler.php");
include_once($D_R.'/lib/config/_google_referral_config.php');

class Via{

	//member variables

	// web service url
	public $viaUrl=null;
	// merchant Login id
	public $merchantLogin=null;
	// merchant password
	public $merchantPassword=null;
	// source code id
	public $sourceCodeId=null;
	// via id returned by Via
    public $customerIdVia=null;
	// application generated id
    public $customerIdExternal=null;
	// customer login id
    public $customerLogin=null;
	// customer password
    public $customerPassword=null;
	// customer email
    public $email=null;
	// customer login id
    public $loginId=null;
	// customer first name
    public $nameFirst=null;
	// customer last name
    public $nameLast=null;
	// class object
	public $viaObj=null;
	public $viaException  = null;


	// constructor - initializing via web service object
	public function __construct(){
		global $viaurl,$merchantlogin,$merchantpwd;
		try{
			// later on we can get following details from config file
			$this->viaUrl=$viaurl;
			$this->merchantLogin=$merchantlogin;//"minyanville";
			$this->merchantPassword=$merchantpwd;//"mini1900";

			// creating via web service object
			$this->viaObj = new SoapClient( $this->viaUrl . "&WSDL", array('trace' => true, 'location' => $this->viaUrl));
			$this->viaException ='';
			return $this->viaObj;

		}// end of try block
		catch (Exception $objException) {
			// Code to MV Error Handler
			$this->viaException = substr($objException->faultstring,0,10);
			return true;
		}// end of catch block

	}//end of constructor

// get custIdent element
	public function getCustIdent(){
			$custIdent=array(
				'merchantLogin'=>$this->merchantLogin,
				'merchantPassword'=>$this->merchantPassword,
				'sourceCodeId'=>$this->sourceCodeId,
				'customerIdVia'=>$this->customerIdVia,
			    'customerIdExternal'=>$this->customerIdExternal,
			    'customerLogin'=>$this->customerLogin,
			    'customerPassword'=>$this->customerPassword,
			    'email'=>$this->email,
				'loginId'=>$this->loginId,
				'nameLast'=>$this->nameLast,
				'nameFirst'=>$this->nameFirst
			);

			return $custIdent;

	}//end of function getCustIdent



 /*** Deepiksa 5/12/2009
	Function defined to get special case Customer orderItemType == PRODUCTS information
  ****/
	public function customerSubInfo($viaId){
		global $operations;
		$operation = $operations['CustomerSubInfo'];
		$logObj=new ViaException();

		$custIdent=$this->getCustIdent();
		$bEmanagerIds = true;
		$customerIds  = $viaId;
		$orderItemTypeList = 'PRODUCT';
		$orderStatus = 'ALL';

		try {
			$orders = $this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>$bEmanagerIds,'customerIds'=>$customerIds,'orderItemTypeList'=>$orderItemTypeList,'orderStatus'=>$orderStatus));
 		 	return ($orders);

		}catch (Exception $exception ) {
			try
			{

				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerSubInfo'],$viaRequest);
				return $getExactError;
			}
		}

	}

	 // this fuction will send subscription emails

	 public function getHousingReprtSubscriptionEmails()
       	 {
	    $custIdent=$this->getCustIdent();
		 try
		  {
		  /*
		  Remove From Query
		  order_item.customer_id, order_item.orderhdr_id,
		  */
		 $sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'customer_address.email','tableList'=>'order_item join customer_address on
order_item.customer_id = customer_address.customer_id ','whereClause'=>'order_code_id = 67 and order_status = 5 and payment_status = 1','groupBy'=>'customer_address.email'));
	     }
	catch( SoapFault $exception )
	 {
        echo $exception->faultstring, "<br>";
        return array(null,null);
    }
      $xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
      $xmlSimp = new SimpleXMLElement($xmlDoc);
      $schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
      $dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
      return $dataRows;
}



	// this fuction will add a new customer record to Via
	// merchantIdentity >>>>>>>>> merchant login and password
	// customerDetails >>>>>>>>>>> customer record
	public function addCustomer($customerDetails,$hardtrial,$dfuser=NULL){
			global $operations;
			$operation = $operations['CustomerAdd'];
			$logObj=new ViaException();

		$custIdent=$this->getCustIdent();

		try {

			// adding a new customer
			$newCustRecord=$this->viaObj->CustomerAdd(array('custIdent'=>$custIdent,'cust'=>$customerDetails));
			$this->customerIdVia=$newCustRecord->CustomerAddResult->customer->loginInfo->customerIdVia;
			$this->customerLogin=$newCustRecord->CustomerAddResult->customer->loginInfo->login;
			$this->email=$newCustRecord->CustomerAddResult->customer->loginInfo->login;
			$this->loginId=$newCustRecord->CustomerAddResult->customer->loginInfo->loginId;

			$this->nameFirst= $newCustRecord->CustomerAddResult->customer->nameFirst;
			$this->nameLast= $newCustRecord->CustomerAddResult->customer->nameLast;

			set_sess_vars('firstname',$this->nameFirst);
			set_sess_vars('lastname',$this->nameLast);
			if($hardtrial){  //set for hardtrial
				set_sess_vars('email',$this->customerLogin);
				set_sess_vars('viaid',$this->customerIdVia);
            }else{ // set for softtrial
				set_sess_vars('via_inactive_id',$this->customerIdVia);
			}

			// Maintain the transaction log
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$this->customerLogin,$this->customerIdVia);
				/*send soft trial activation email to activate account of softtrials*/
			if((!$hardtrial || $hardtrial="")&&($this->customerIdVia)){
				$ViaEmail= new ViaEmail();
				$ViaEmail->sendSoftTrialActivationEmail($this->nameFirst,$this->customerIdVia,$this->customerLogin,$activate,$dfuser);
            }
			return true;
			exit;
			//return true;

		}// end of try block
		catch (Exception $exception ) {
			try
			{

				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerAdd'],$viaRequest);
				return $getExactError;
			}
		}// end of catch block

	}// end of  function addCustomer


	// to add customer record with his order
	// custIdent >>>>>>>>>>>> merchant login and password
	// cust >>>>>>>>>>>>>>>>>>> customer information
	// cart >>>>>>>>>>>>>>>>>>> order information
	function addCustomerAndOrder( $cust,$viacart,$hardtrial) {
		 global $operations, $_SESSION;
         $operation = $operations['CustomerAddOrderAdd'];
     	 $logObj=new ViaException();
		 $viacart= $this->setRefSourceCode($viacart, false) ;
		try {
			$custIdent=$this->getCustIdent();
			// add new record of customer as well as his order
			$custOrderDetails=$this->viaObj->CustomerAddOrderAdd(array('custIdent'=>$custIdent,'cust'=>$cust,'cart'=>$viacart));
			$this->customerIdVia=$custOrderDetails->CustomerAddOrderAddResult->customer->loginInfo->customerIdVia;
			$this->customerLogin=$custOrderDetails->CustomerAddOrderAddResult->customer->loginInfo->login;
			$this->email=$custOrderDetails->CustomerAddOrderAddResult->customer->loginInfo->login;
			$this->loginId=$custOrderDetails->CustomerAddOrderAddResult->customer->loginInfo->loginId;
			/* Added by Aswini start */
			$this->nameFirst= $custOrderDetails->CustomerAddOrderAddResult->customer->nameFirst;
			$this->nameLast= $custOrderDetails->CustomerAddOrderAddResult->customer->nameLast;
			/* Added by Aswini end */
			set_sess_vars('firstname',$this->nameFirst);
			set_sess_vars('lastname',$this->nameLast);
			if($hardtrial){  //set for hardtrial
				set_sess_vars('email',$this->customerLogin);
				set_sess_vars('viaid',$this->customerIdVia);
            }else{ // set for softtrial
				set_sess_vars('via_inactive_id',$this->customerIdVia);
			}
			/* Added by deepika 5/12/2009 */
		$products = $custOrderDetails->CustomerAddOrderAddResult->orders;
						//set user orderItmType = PRODUCT info in session

						$productsArr=array();
						if(count($products)>1){
							foreach($products as $key => $value)
							{
								$productsArr[$value->orderItemType][$value->typeSpecificId]=array('orderNumber'=>$value->orderNumber,'orderItemSeq'=>$value->orderItemSeq,'orderClassId'=>$value->orderClassId,'orderCodeId'=>$value->orderCodeId,'sourceCodeId'=>$value->sourceCodeId,'typeSpecificId'=>$value->typeSpecificId,'startDate'=>$value->startDate,'expireDate'=>$value->expireDate,'billDate'=>$value->billDate,'description'=>$value->description,'price'=>$value->price,'auto_renew'=>$paymentRenew[$value->auto_renew],'subscriptionId'=>$value->subscriptionId,'orderItemType'=>$value->orderItemType,'n_issues_left'=>$value->n_issues_left);
							}
						}elseif(count($products)==1){
							//echo $products->typeSpecificId;
							$productsArr[$products->orderItemType][$products->typeSpecificId]=array('orderNumber'=>$products->orderNumber,'orderItemSeq'=>$products->orderItemSeq,'orderClassId'=>$products->orderClassId,'orderCodeId'=>$products->orderCodeId,'sourceCodeId'=>$products->sourceCodeId,'typeSpecificId'=>$products->typeSpecificId,'startDate'=>$products->startDate,'expireDate'=>$products->expireDate,'billDate'=>$products->billDate,'description'=>$products->description,'price'=>$products->price,'auto_renew'=>$paymentRenew[$products->auto_renew],'subscriptionId'=>$products->subscriptionId,'orderItemType'=>$products->orderItemType,'n_issues_left'=>$products->n_issues_left);
						}

					if($hardtrial){
						set_sess_vars("products",$productsArr);
					}

			/*ends  by deepika*/
			// Maintain the transaction log
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$this->customerLogin,$this->customerIdVia);

			/*send soft trial activation email to activate account of softtrials*/
			if((!$hardtrial || $hardtrial="")&&($this->customerIdVia)){
				$ViaEmail= new ViaEmail();
				$ViaEmail->sendSoftTrialActivationEmail($this->nameFirst,$this->customerIdVia,$this->customerLogin);
            }

			return true;
		}// end of try block
		catch (Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,119);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerAddOrderAdd'],$viaRequest);
				return $getExactError;
			}
		}
	}// end of function addCustomerAndOrder
	// to add customer record with his order and payment details
	// custIdent >>>>>>>>>>>>>>> merchant login and password
	// cust >>>>>>>>>>>>>>>>>>>>>> customer information along with payment details
	// cart >>>>>>>>>>>>>>>>>>>>>> order information
	public function addCustomerAndOrderAndPayment($cust,$viacart) {
			global $operations;
			$operation = $operations['CustomerAddOrderAddPaymentAdd'];
			$logObj=new ViaException();
			 $viacart= $this->setRefSourceCode($viacart, false) ;
		try {
			$custIdent=$this->getCustIdent();
			// add new record of customer as well as his order
			$custOrderPaymentDetails=$this->viaObj->CustomerAddOrderAddPaymentAdd(array('custIdent'=>$custIdent,'cust'=>$cust,'cart'=>$viacart));
			$this->customerIdVia = $custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->loginInfo->customerIdVia;
			$this->email = $custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->loginInfo->login;
			$this->nameFirst= $custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->nameFirst;
			$this->nameLast= $custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->nameLast;
			session_start();
			set_sess_vars('viaid',$this->customerIdVia);
			set_sess_vars('email',$this->email);
			set_sess_vars('firstname',$this->nameFirst);
			set_sess_vars('lastname',$this->nameLast);
			// Maintain the transaction log
			/* Set GA Ecommerce Tracking */
			$this->addGATrans($custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->orderSummary->orderhdr_id,$custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->addresses->city,$custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->addresses->state,$custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult->customer->addresses->country,$custOrderPaymentDetails->CustomerAddOrderAddPaymentAddResult,$viacart);
			/* Set GA Ecommerce Tracking */
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$this->email,$this->customerIdVia);
			return true;

		}// end of try block
		catch (Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,101);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerAddOrderAddPaymentAdd'],$viaRequest);
				return $getExactError;
			}
			return false;
			exit;
		}// end of catch block

	}// end of function addCustomerAndOrderAndPayment


	// To add order for customer
	// custIdent  >>>>>>>>>>>>>> merchant login, merchantPassword and customerIdVia
	// cart >>>>>>>>>>>>>>>>>>>>>> order information, typical cart details
	function addOrder($viacart){
		global $D_R;
		include_once($D_R.'/lib/config/_products_config.php');
		global $_SESSION,$viaProducts, $operations,$productAd;
		$operation = $operations['OrderAdd'];
		$logObj=new ViaException();
    	$viacart= $this->setRefSourceCode($viacart, false) ;
/*
		$index=0;
		foreach($viacart as $key=>$val){
			$status[$index]=$val['typeSpecificId'];
			$index++;
		}
*/
		try {
			$custIdent=$this->getCustIdent();
			// place order along with payment details
			$orderPaymentDetails=$this->viaObj->OrderAdd(array('custIdent'=>$custIdent,'cart'=>$viacart));
			$this->addGATrans($orderPaymentDetails->OrderAddResult->orderSummary->orderhdr_id,$orderPaymentDetails->OrderAddResult->customer->addresses->city,$orderPaymentDetails->OrderAddResult->customer->addresses->state,$orderPaymentDetails->OrderAddResult->customer->addresses->country,$orderPaymentDetails->OrderAddResult,$viacart);
			$products=array();
			if(!$orderPaymentDetails->OrderAddResult->orders){
				$products=array();
			}

			if(count($orderPaymentDetails->OrderAddResult->orders)>1){
				foreach($orderPaymentDetails->OrderAddResult->orders as $key=>$val){
					$products['orderNumber']=$val->orderNumber;
					$products['orderItemSeq']=$val->orderItemSeq;
					$products['orderClassId']=$val->orderClassId;
					$products['orderCodeId']=$val->orderCodeId;
					$products['orderItemType']=$val->orderItemType;
					$products['typeSpecificId']=$val->typeSpecificId;
					$products['startDate']=$val->startDate;
					$products['expireDate']=$val->expireDate;
					$products['billDate']=$val->billDate;
					$products['price']=$val->price;
					$products['qty']=$val->qty;
					$products['sourceCodeId']=$val->sourceCodeId;
					$products['description']=$val->description;
					$products['subscriptionId']=$val->subscriptionId;

					if($val->auto_renew=='AUTO_RENEW'){
						$products['auto_renew']=1;
					}
					else{
						$products['auto_renew']=0;
					}

					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]=$products;

					if($val->typeSpecificId==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzAnnual']['typeSpecificId']){
						$_SESSION['Buzz']=1;
						$buzzFlag=array('premium'=>1);
						$this->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
					}
					elseif($val->typeSpecificId==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperAnnual']['typeSpecificId']) {
						$_SESSION['Cooper']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioAnnual']['typeSpecificId']) {
						$_SESSION['Flexfolio']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithAnnual']['typeSpecificId']) {
						$_SESSION['Optionsmith']=1;
					}
					// BMTP product
					elseif($val->orderItemType==$viaProducts['BMTP']['orderItemType'] && $val->typeSpecificId==$viaProducts['BMTP']['typeSpecificId'] ) {
						$_SESSION['BMTP']=1;
					}
					// BMTP subscription
					elseif(($val->orderItemType==$viaProducts['BMTPAlert']['orderItemType']) && ($val->typeSpecificId==$viaProducts['BMTPAlertTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlert']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlertComplimentary']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlertCK']['typeSpecificId'] )) {
						$_SESSION['BMTPAlert']=1;
					}
					// Grail ETF subscription
					elseif($val->typeSpecificId==$viaProducts['ETFMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFQuart']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFComplimentary']['typeSpecificId']) {
						$_SESSION['ETFTrader']=1;
					}
					// Grail BuyHedge-ETF subscription
					elseif($val->typeSpecificId==$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuyHedgeQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFQuart']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['ETFComplimentary']['typeSpecificId']) {
						$_SESSION['buyhedge']=1;
					}
					// The Stockplaybook subscription
					elseif($val->typeSpecificId==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookQuart']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookComplimentary']['typeSpecificId']) {
						$_SESSION['TheStockPlayBook']=1;
					}


					// The Stockplaybook Premium subscription
					elseif($val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId']) {
						$_SESSION['TheStockPlayBookPremium']=1;
					}

					// The AdFree subscription
					elseif($val->typeSpecificId==$viaProducts['AdFreeMonthly']['typeSpecificId']|| $val->typeSpecificId==$viaProducts['AdFreeComplimentary']['typeSpecificId'] ) {
						$_SESSION['AdsFree']=1;
					}
					//TechStrat Subscription
					elseif($val->typeSpecificId==$viaProducts['TechStratMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratQuarterTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratComplimentary']['typeSpecificId']) {
						$_SESSION['TechStrat']=1;
					}


						//GaryK Subscription
					elseif($val->typeSpecificId==$viaProducts['GaryKMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKQuarterTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKComplimentary']['typeSpecificId']) {
						$_SESSION['GaryK']=1;
					}



					// Housing Report
					elseif($val->typeSpecificId==$viaProducts['Housing3Months']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['Housing6Months']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['HousingAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['HousingComplimentary']['typeSpecificId']) {
						$_SESSION['HousingReport']=1;
					}
					//Housing Market Single Issue
					elseif($val->typeSpecificId==$viaProducts['LasVegas']['typeSpecificId']) {
						$_SESSION['HS-LasVegas']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Chicago']['typeSpecificId']) {
						$_SESSION['HS-Chicago']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Phoenix']['typeSpecificId']) {
						$_SESSION['HS-Phoenix']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['WashingtonDC']['typeSpecificId']) {
						$_SESSION['HS-WashingtonDC']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['SanDiego']['typeSpecificId']) {
						$_SESSION['HS-SanDiego']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['NewYorkMetro']['typeSpecificId']) {
						$_SESSION['HS-NewYorkMetro']=1;
					}elseif($val->typeSpecificId==$viaProducts['Miami']['typeSpecificId']) {
						$_SESSION['HS-Miami-DadeCo']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Atlanta']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['AtlantaComp']['typeSpecificId']) {
						$_SESSION['HS-Atlanta']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Dallas']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['DallasComp']['typeSpecificId']) {
						$_SESSION['HS-Dallas']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['LosAngles']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['LosAnglesComp']['typeSpecificId']) {
						$_SESSION['HS-LosAngles']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Minneapolis']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['MinneapolisComp']['typeSpecificId']) {
						$_SESSION['HS-Minneapolis']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Portland']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['PortlandComp']['typeSpecificId']) {
						$_SESSION['HS-Portland']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Orlendo']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OrlendoComp']['typeSpecificId']) {
						$_SESSION['HS-Orlendo']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['Seattle']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['SeattleComp']['typeSpecificId']) {
						$_SESSION['HS-Seattle']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['SanFrancisco']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['SanFranciscoComp']['typeSpecificId']) {
						$_SESSION['HS-SanFrancisco']=1;
					}
					elseif($val->typeSpecificId==$viaProducts['LongIsland']['typeSpecificId']) {
						$_SESSION['HS-LongIsland']=1;
					}
					// For Housing Market Single Issue End

					/*Add ads free if user subscribe to Annual product*/
					if(in_array($val->typeSpecificId,$productAd)){
						$_SESSION['AdsFree']='1';
					}
				}
			}
			else{
					$products['orderNumber']=$orderPaymentDetails->OrderAddResult->orders->orderNumber;
					$products['orderItemSeq']=$orderPaymentDetails->OrderAddResult->orders->orderItemSeq;
					$products['orderClassId']=$orderPaymentDetails->OrderAddResult->orders->orderClassId;
					$products['orderCodeId']=$orderPaymentDetails->OrderAddResult->orders->orderCodeId;
					$products['orderItemType']=$orderPaymentDetails->OrderAddResult->orders->orderItemType;
					$products['startDate']=$orderPaymentDetails->OrderAddResult->orders->startDate;
					$products['expireDate']=$orderPaymentDetails->OrderAddResult->orders->expireDate;
					$products['billDate']=$orderPaymentDetails->OrderAddResult->orders->billDate;
					$products['typeSpecificId']=$orderPaymentDetails->OrderAddResult->orders->typeSpecificId;
					$products['price']=$orderPaymentDetails->OrderAddResult->orders->price;
					$products['qty']=$orderPaymentDetails->OrderAddResult->orders->qty;
					$products['sourceCodeId']=$orderPaymentDetails->OrderAddResult->orders->sourceCodeId;
					$products['description']=$orderPaymentDetails->OrderAddResult->orders->description;
					$products['subscriptionId']=$orderPaymentDetails->OrderAddResult->orders->subscriptionId;

					if($orderPaymentDetails->OrderAddResult->orders->auto_renew=='AUTO_RENEW'){
						$products['auto_renew']=1;
					}
					else{
						$products['auto_renew']=0;
					}

					$_SESSION['products'][$orderPaymentDetails->OrderAddResult->orders->orderItemType][$orderPaymentDetails->OrderAddResult->orders->typeSpecificId]=$products;

					if(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['BuzzMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BuzzMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BuzzAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BuzzAnnual']['typeSpecificId'])){
						$_SESSION['Buzz']=1;
						$buzzFlag=array('premium'=>1);
						$this->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
					}
					elseif(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['CooperMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['CooperMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['CooperAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['CooperAnnual']['typeSpecificId'])) {
						$_SESSION['Cooper']=1;
					}
					elseif(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['FlexfolioMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['FlexfolioMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['FlexfolioAnnual']['typeSpecificId'])) {
						$_SESSION['Flexfolio']=1;
					}
					elseif(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['OptionsmithMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['OptionsmithMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['OptionsmithAnnual']['typeSpecificId'])) {
						$_SESSION['Optionsmith']=1;
					}

					// BMTP product
					elseif($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['BMTP']['orderItemType'] && $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BMTP']['typeSpecificId'] ) {
						$_SESSION['BMTP']=1;
					}
					// BMTP subscription
					elseif(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['BMTPAlert']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BMTPAlert']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertCK']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertComplimentary']['typeSpecificId'])) {
						$_SESSION['BMTPAlert']=1;
					}
					// jack
					elseif(($orderPaymentDetails->OrderAddResult->orders->orderItemType==$viaProducts['JackMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['JackMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['JackAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddResult->orders->typeSpecificId==$viaProducts['JackAnnual']['typeSpecificId'])) {
						$_SESSION['Jack']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['ETFMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['ETFQuartTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFQuartTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['ETFAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFAnnualTrial']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['ETFMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['ETFQuart']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFQuart']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['ETFAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['ETFComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['ETFComplimentary']['typeSpecificId'])))
					{
						$_SESSION['ETFTrader']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['BuyHedgeMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeQuartTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeQuartTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeQuart']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeQuart']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['BuyHedgeComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['BuyHedgeComplimentary']['typeSpecificId'])))
					{
						$_SESSION['buyhedge']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['TheStockPlaybookMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookQuartTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookQuart']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookQuart']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookComplimentary']['typeSpecificId'])))
					{
						$_SESSION['TheStockPlayBook']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumQuartTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'])
					|| (($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumQuart']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TheStockPlaybookPremiumComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId']))))
					{
						$_SESSION['TheStockPlayBookPremium']=1;
					}
					elseif(($products['orderItemType'] == $viaProducts['AdFreeMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['AdFreeMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['AdFreeComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['AdFreeComplimentary']['typeSpecificId']))
					{
						$_SESSION['AdsFree']=1;
					}
					//TechStrat
					elseif($products['orderItemType'] == $viaProducts['TechStratMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TechStratQuarterTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratQuarterTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['TechStratAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratAnnualTrial']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TechStratMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TechStratQuarterly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratQuarterly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TechStratAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['TechStratComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['TechStratComplimentary']['typeSpecificId'])))
					{
						$_SESSION['TechStrat']=1;
					}


					//GaryK
					elseif($products['orderItemType'] == $viaProducts['GaryKMonthlyTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKMonthlyTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['GaryKQuarterTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKQuarterTrial']['typeSpecificId']
					|| ($products['orderItemType'] == $viaProducts['GaryKAnnualTrial']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKAnnualTrial']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['GaryKMonthly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKMonthly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['GaryKQuarterly']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKQuarterly']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['GaryKAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['GaryKComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['GaryKComplimentary']['typeSpecificId'])))
					{
						$_SESSION['GaryK']=1;
					}


					//Housing Report
					elseif(($products['orderItemType'] == $viaProducts['Housing3Months']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Housing3Months']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['Housing6Months']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Housing6Months']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['HousingAnnual']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['HousingAnnual']['typeSpecificId'])
					|| ($products['orderItemType'] == $viaProducts['HousingComplimentary']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['HousingComplimentary']['typeSpecificId']))
					{
						$_SESSION['HousingReport']=1;
					}
					//Housing Market Single Issue
					elseif($products['orderItemType'] == $viaProducts['LasVegas']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['LasVegas']['typeSpecificId']){
						$_SESSION['HS-LasVegas']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Chicago']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Chicago']['typeSpecificId']){
						$_SESSION['HS-Chicago']=1;
					}elseif($products['orderItemType'] == $viaProducts['Phoenix']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Phoenix']['typeSpecificId']){
						$_SESSION['HS-Phoenix']=1;
					}elseif($products['orderItemType'] == $viaProducts['WashingtonDC']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['WashingtonDC']['typeSpecificId']){
						$_SESSION['HS-WashingtonDC']=1;
					}elseif($products['orderItemType'] == $viaProducts['SanDiego']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['SanDiego']['typeSpecificId']){
						$_SESSION['HS-SanDiego']=1;
					}elseif($products['orderItemType'] == $viaProducts['NewYorkMetro']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['NewYorkMetro']['typeSpecificId']){
						$_SESSION['HS-NewYorkMetro']=1;
					}elseif($products['orderItemType'] == $viaProducts['Miami']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Miami']['typeSpecificId']){
						$_SESSION['HS-Miami-DadeCo']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Atlanta']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Atlanta']['typeSpecificId']){
						$_SESSION['HS-Atlanta']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Dallas']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Dallas']['typeSpecificId']){
						$_SESSION['HS-Dallas']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['LosAngles']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['LosAngles']['typeSpecificId']){
						$_SESSION['HS-LosAngles']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Minneapolis']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Minneapolis']['typeSpecificId']){
						$_SESSION['HS-Minneapolis']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Portland']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Portland']['typeSpecificId']){
						$_SESSION['HS-Portland']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Orlendo']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Orlendo']['typeSpecificId']){
						$_SESSION['HS-Orlendo']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['Seattle']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['Seattle']['typeSpecificId']){
						$_SESSION['HS-Seattle']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['SanFrancisco']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['SanFrancisco']['typeSpecificId']){
						$_SESSION['HS-SanFrancisco']=1;
					}
					elseif($products['orderItemType'] == $viaProducts['SanFrancisco']['orderItemType'] && $products['typeSpecificId'] == $viaProducts['LongIsland']['typeSpecificId']){
						$_SESSION['HS-LongIsland']=1;
					}
					 // For Housing Market Single Issue End

					/*Add ads free if user subscribe to Annual product*/
					if(in_array($products['typeSpecificId'],$productAd)){
						$_SESSION['AdsFree']='1';
					}
			}
			// Maintain the transaction log
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return true;

		}// end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['OrderAdd'],$viaRequest);
				return $getExactError;
			}

		}// end of catch block

	}//end of function addOrder

	 function setRefSourceCode($viacart,$editFlag = false){
		global $_SESSION,$refSourceCodes;
		session_start();
		if(!$editFlag){
			$orderDetails = $viacart['items']['OrderItem'];
			$pattern = '/'.$refSourceCodes['google']['pattern'].'/i';
			$pattern_yahoo = '/'.$refSourceCodes['yahoo']['pattern'].'/i';
			$pattern_bing = '/'.$refSourceCodes['bing']['pattern'].'/i';
			foreach($orderDetails as $key=>$val){
				if($val['sourceCodeId'] == '1' && preg_match($pattern, $_SESSION['refererSourceId'])){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['google']['code'];
				}
				elseif($val['sourceCodeId'] == '1' && preg_match($pattern_yahoo, $_SESSION['refererSourceId'])){
									$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['yahoo']['code'];
				}
				elseif($val['sourceCodeId'] == '1' && preg_match($pattern_bing, $_SESSION['refererSourceId'])){
									$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['bing']['code'];
				}elseif($val['sourceCodeId'] == '1' &&  $_SESSION['thestreet']){
							$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['thestreet']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['digibuzz']){
							$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['digibuzz']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['articlePremium']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['premiumArticle']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['buzzYahooSynd']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['buzzyahoosynd']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['buzzGoogleSynd']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['buzzgooglesynd']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['googleAdWordlead']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['googleadwordlead']['code'];
				}
			}

		}else{
				if($val['sourceCodeId'] == '1' && preg_match($pattern, $_SESSION['refererSourceId'])){
					$viacart['sourceCodeId'] = $refSourceCodes['google']['code'];
				}
				elseif($val['sourceCodeId'] == '1' && preg_match($pattern_yahoo, $_SESSION['refererSourceId'])){
					$viacart['sourceCodeId'] = $refSourceCodes['yahoo']['code'];
				}
				elseif($val['sourceCodeId'] == '1' && preg_match($pattern_bing, $_SESSION['refererSourceId'])){
					$viacart['sourceCodeId'] = $refSourceCodes['bing']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['thestreet']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['thestreet']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['digibuzz']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['digibuzz']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['articlePremium']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['premiumArticle']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['buzzYahooSynd']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['buzzyahoosynd']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['buzzGoogleSynd']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['buzzgooglesynd']['code'];
				}
				elseif($val['sourceCodeId'] == '1' &&  $_SESSION['googleAdWordlead']){
					$viacart['items']['OrderItem'][$key]['sourceCodeId'] = $refSourceCodes['googleadwordlead']['code'];
				}

		}

	return $viacart;

	}
	// To place order and payment details for a customer
	// custIdent >>>>>>>>>>>> merchant login, password and customer peyment details
	// cart >>>>>>>>>>>>>>>>>>> order information along with payment details
	function addOrderAndPayment ( $viacart) {
		global $_SESSION,$viaProducts;
		global $operations;
		$operation = $operations['OrderAddPaymentAdd'];
		$logObj=new ViaException();
		$viacart= $this->setRefSourceCode($viacart, false) ;
/*
		$index=0;
		foreach($viacart as $key=>$val){
			$status[$index]=$val['typeSpecificId'];
			$index++;
		}
*/
		try {
			$custIdent=$this->getCustIdent();
			$index=0;
			$subscriptionId = 0;
			foreach($viacart['items'] as $key => $val){
				// check if user has same order ever before and get the subscription id of latest one.
				$subscriptionId=$this->getLatestSubscriptionId($val[$index]['customerIdVia'],$val[$index]['orderClassId']);

				if($subscriptionId >0){
					$viacart['items']['OrderItem'][$index]['subscriptionId']=$subscriptionId;
					$subscriptionId = 0 ;
				}
				$index++;
			}
			// place order along with payment details
			$orderPaymentDetails=$this->viaObj->OrderAddPaymentAdd(array('custIdent'=>$custIdent,'cart'=>$viacart));

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);

			$products=array();
			if(!$orderPaymentDetails->OrderAddPaymentAddResult->orders){
				$products=array();
			}
			//set product
			$strProducts=array();
			$strProducts=$this->resetProductSession($this->customerIdVia);
			$this->addGATrans($orderPaymentDetails->OrderAddPaymentAddResult->orderSummary->orderhdr_id,$orderPaymentDetails->OrderAddPaymentAddResult->customer->addresses->city,$orderPaymentDetails->OrderAddPaymentAddResult->customer->addresses->state,$orderPaymentDetails->OrderAddPaymentAddResult->customer->addresses->country,$orderPaymentDetails->OrderAddPaymentAddResult,$viacart);

			if(count($orderPaymentDetails->OrderAddPaymentAddResult->orders)>1){
				foreach($orderPaymentDetails->OrderAddPaymentAddResult->orders as $key=>$val){
					$products['orderNumber']=$val->orderNumber;
					$products['orderItemSeq']=$val->orderItemSeq;
					$products['orderClassId']=$val->orderClassId;
					$products['orderCodeId']=$val->orderCodeId;
					$products['orderItemType']=$val->orderItemType;
					$products['typeSpecificId']=$val->typeSpecificId;
					$products['startDate']=$val->startDate;
					$products['expireDate']=$val->expireDate;
					$products['billDate']=$val->billDate;
					$products['price']=$val->price;
					$products['qty']=$val->qty;
					$products['sourceCodeId']=$val->sourceCodeId;
					$products['description']=$val->description;
					$products['subscriptionId']=$val->subscriptionId;

					if($val->auto_renew=='AUTO_RENEW'){
						$products['auto_renew']=1;
					}
					else{
						$products['auto_renew']=0;
					}
					$products['n_issues_left']=$val->n_issues_left;

					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]=$products;

					if(($val->orderItemType==$viaProducts['BuzzMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BuzzAnnual']['typeSpecificId'])){
						$_SESSION['Buzz']=1;
						$buzzFlag=array('premium'=>1);
						$this->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
					}
					elseif(($val->orderItemType==$viaProducts['CooperMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperQuartTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['CooperAnnual']['typeSpecificId'])) {
						$_SESSION['Cooper']=1;
					}
					elseif(($val->orderItemType==$viaProducts['FlexfolioMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['FlexfolioAnnual']['typeSpecificId'])) {
						$_SESSION['Flexfolio']=1;
					}
					// for option smith product
					elseif(($val->orderItemType==$viaProducts['OptionsmithMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OptionsmithAnnual']['typeSpecificId'])) {
						$_SESSION['Optionsmith']=1;
					}
					// BMTP product
					elseif($val->orderItemType==$viaProducts['BMTP']['orderItemType'] && $val->typeSpecificId==$viaProducts['BMTP']['typeSpecificId'] ) {
						$_SESSION['BMTP']=1;
					}
					// BMTP susbcription
					elseif(($val->orderItemType==$viaProducts['BMTPAlert']['orderItemType']) && ($val->typeSpecificId==$viaProducts['BMTPAlertTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlert']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlertCK']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['BMTPAlertComplimentary']['typeSpecificId'])) {
						$_SESSION['BMTPAlert']=1;
					}
					// for jack product
					elseif(($val->orderItemType==$viaProducts['JackMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['JackAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['JackMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['JackAnnual']['typeSpecificId'])) {
						$_SESSION['Jack']=1;
					}

					// for The StockPlaybook
					elseif(($val->orderItemType==$viaProducts['TheStockPlaybookMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'])) {
						$_SESSION['TheStockPlayBook']=1;
					}
					//for TSPB Premium
					elseif(($val->orderItemType==$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'])) {
						$_SESSION['TheStockPlayBookPremium']=1;
					}
					// for ads free product
					elseif($val->typeSpecificId==$viaProducts['AdFreeMonthly']['typeSpecificId'] )
					{
						$_SESSION['AdsFree']=1;
					}
					// for TechStrat
					elseif(($val->orderItemType==$viaProducts['TechStratMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['TechStratMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratQuarterTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['TechStratAnnual']['typeSpecificId'])) {
						$_SESSION['TechStrat']=1;
					}

						// for GaryK
					elseif(($val->orderItemType==$viaProducts['GaryKMonthly']['orderItemType']) && ($val->typeSpecificId==$viaProducts['GaryKMonthlyTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKQuarterTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKAnnualTrial']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKMonthly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKQuarterly']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['GaryKAnnual']['typeSpecificId'])) {
						$_SESSION['GaryK']=1;
					}


					// for Housing Report
					elseif(($val->orderItemType==$viaProducts['Housing3Months']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Housing3Months']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['Housing6Months']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['HousingAnnual']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['HousingComplimentary']['typeSpecificId'])) {
						$_SESSION['HousingReport']=1;
					}
					// for Housing Report Single Issues
					elseif(($val->orderItemType==$viaProducts['LasVegas']['orderItemType']) && ($val->typeSpecificId==$viaProducts['LasVegas']['typeSpecificId'] )) {
						$_SESSION['HS-LasVegas']=1;
					}elseif(($val->orderItemType==$viaProducts['Chicago']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Chicago']['typeSpecificId'] )) {
						$_SESSION['HS-Chicago']=1;
					}elseif(($val->orderItemType==$viaProducts['Phoenix']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Phoenix']['typeSpecificId'] )) {
						$_SESSION['HS-Phoenix']=1;
					}elseif(($val->orderItemType==$viaProducts['WashingtonDC']['orderItemType']) && ($val->typeSpecificId==$viaProducts['WashingtonDC']['typeSpecificId'] )) {
						$_SESSION['HS-WashingtonDC']=1;
					}elseif(($val->orderItemType==$viaProducts['SanDiego']['orderItemType']) && ($val->typeSpecificId==$viaProducts['SanDiego']['typeSpecificId'] )) {
						$_SESSION['HS-SanDiego']=1;
					}elseif(($val->orderItemType==$viaProducts['NewYorkMetro']['orderItemType']) && ($val->typeSpecificId==$viaProducts['NewYorkMetro']['typeSpecificId'] )) {
						$_SESSION['HS-NewYorkMetro']=1;
					}elseif(($val->orderItemType==$viaProducts['Miami']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Miami']['typeSpecificId'] )) {
						$_SESSION['HS-Miami-DadeCo']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Atlanta']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Atlanta']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['AtlantaComp']['typeSpecificId'] )) {
						$_SESSION['HS-Atlanta']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Dallas']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Dallas']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['DallasComp']['typeSpecificId'] )) {
						$_SESSION['HS-Dallas']=1;
					}
					elseif(($val->orderItemType==$viaProducts['LosAngles']['orderItemType']) && ($val->typeSpecificId==$viaProducts['LosAngles']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['LosAnglesComp']['typeSpecificId'] )) {
						$_SESSION['HS-LosAngles']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Minneapolis']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Minneapolis']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['MinneapolisComp']['typeSpecificId'] )) {
						$_SESSION['HS-Minneapolis']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Portland']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Portland']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['PortlandComp']['typeSpecificId'] )) {
						$_SESSION['HS-Portland']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Orlendo']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Orlendo']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['OrlendoComp']['typeSpecificId'] )) {
						$_SESSION['HS-Orlendo']=1;
					}
					elseif(($val->orderItemType==$viaProducts['Seattle']['orderItemType']) && ($val->typeSpecificId==$viaProducts['Seattle']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['SeattleComp']['typeSpecificId'] )) {
						$_SESSION['HS-']=1;
					}
					elseif(($val->orderItemType==$viaProducts['SanFrancisco']['orderItemType']) && ($val->typeSpecificId==$viaProducts['SanFrancisco']['typeSpecificId'] || $val->typeSpecificId==$viaProducts['Comp']['typeSpecificId'] )) {
						$_SESSION['HS-SanFrancisco']=1;
					}
					elseif($val->orderItemType==$viaProducts['LongIsland']['orderItemType'] && $val->typeSpecificId==$viaProducts['LongIsland']['typeSpecificId']) {
						$_SESSION['HS-LongIsland']=1;
					}
					//for Housing Report Single Issues End
				}
			}
			else{
					$products['orderNumber']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderNumber;
					$products['orderItemSeq']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemSeq;
					$products['orderClassId']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderClassId;
					$products['orderCodeId']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderCodeId;
					$products['orderItemType']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType;
					$products['startDate']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->startDate;
					$products['expireDate']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->expireDate;
					$products['billDate']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->billDate;
					$products['typeSpecificId']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId;
					$products['price']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->price;
					$products['qty']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->qty;
					$products['sourceCodeId']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->sourceCodeId;
					$products['description']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->description;
					$products['subscriptionId']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->subscriptionId;

					if($orderPaymentDetails->OrderAddPaymentAddResult->orders->auto_renew=='AUTO_RENEW'){
						$products['auto_renew']=1;
					}
					else{
						$products['auto_renew']=0;
					}

					$products['n_issues_left']=$orderPaymentDetails->OrderAddPaymentAddResult->orders->n_issues_left;

					$_SESSION['products'][$orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType][$orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId]=$products;

					if( ($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['BuzzMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzQuartTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzQuarterly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BuzzAnnual']['typeSpecificId'])){
						$_SESSION['Buzz']=1;
						$buzzFlag=array('premium'=>1);
						$this->setBuzzInDb($_SESSION['viaid'],$buzzFlag);
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['CooperMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperQuartTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperQuarterly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['CooperAnnual']['typeSpecificId'])) {
						$_SESSION['Cooper']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['FlexfolioMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['FlexfolioMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['FlexfolioAnnual']['typeSpecificId'])) {
						$_SESSION['Flexfolio']=1;
					}
					// for optionsmith
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['OptionsmithMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['OptionsmithMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['OptionsmithAnnual']['typeSpecificId'])) {
						$_SESSION['Optionsmith']=1;
					}
					// BMTP product
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['BMTP']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTP']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPCK']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPComplimentary']['typeSpecificId'] )) {
						$_SESSION['BMTP']=1;
					}
					// BMTP subscription
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['BMTPAlert']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPAlert']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertCK']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['BMTPAlertComplimentary']['typeSpecificId'] )) {
						$_SESSION['BMTPAlert']=1;
					}
					// for jack
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['JackMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['JackMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['JackMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['JackAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['JackAnnual']['typeSpecificId'])) {
						$_SESSION['Jack']=1;
					}
					// for TSPB
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['TheStockPlaybookMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'])) {
						$_SESSION['TheStockPlayBook']=1;
					}
					// for TSPB Premium
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'])) {
						$_SESSION['TheStockPlayBookPremium']=1;
					}

					// for ads free product
					elseif($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['AdFreeMonthly']['typeSpecificId'])
					{
						$_SESSION['AdsFree']=1;
					}
					// for TechStrat
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['TechStratMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratQuarterTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratQuarterly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['TechStratAnnual']['typeSpecificId'])) {
						$_SESSION['TechStrat']=1;
					}

				   	// for GaryK
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['GaryKMonthly']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKMonthlyTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKQuarterTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKAnnualTrial']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKMonthly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKQuarterly']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['GaryKAnnual']['typeSpecificId'])) {
						$_SESSION['GaryK']=1;
					}




					// for Housing Report
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Housing3Months']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Housing3Months']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Housing6Months']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['HousingAnnual']['typeSpecificId'])) {
						$_SESSION['HousingReport']=1;
					}
					//for Housing Report Single Issue
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['LasVegas']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['LasVegas']['typeSpecificId'])) {
						$_SESSION['HS-LasVegas']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Chicago']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Chicago']['typeSpecificId'])) {
						$_SESSION['HS-Chicago']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Phoenix']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Phoenix']['typeSpecificId'])) {
						$_SESSION['HS-Phoenix']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['WashingtonDC']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['WashingtonDC']['typeSpecificId'])) {
						$_SESSION['HS-WashingtonDC']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['SanDiego']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['SanDiego']['typeSpecificId'])) {
						$_SESSION['HS-SanDiego']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['NewYorkMetro']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['NewYorkMetro']['typeSpecificId'])) {
						$_SESSION['HS-NewYorkMetro']=1;
					}elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Miami']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Miami']['typeSpecificId'])) {
						$_SESSION['HS-Miami-DadeCo']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Atlanta']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Atlanta']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['AtlantaComp']['typeSpecificId'])) {
						$_SESSION['HS-Atlanta']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Dallas']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Dallas']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['DallasComp']['typeSpecificId'])) {
						$_SESSION['HS-Dallas']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['LosAngles']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['LosAngles']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['LosAnglesComp']['typeSpecificId'])) {
						$_SESSION['HS-LosAngles']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Minneapolis']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Minneapolis']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['MinneapolisComp']['typeSpecificId'])) {
						$_SESSION['HS-Minneapolis']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Portland']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Portland']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['PortlandComp']['typeSpecificId'])) {
						$_SESSION['HS-Portland']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Orlendo']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Orlendo']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['OrlendoComp']['typeSpecificId'])) {
						$_SESSION['HS-Orlendo']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['Seattle']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['Seattle']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['SeattleComp']['typeSpecificId'])) {
						$_SESSION['HS-Seattle']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['SanFrancisco']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['SanFrancisco']['typeSpecificId'] || $orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['SanFranciscoComp']['typeSpecificId'])) {
						$_SESSION['HS-SanFrancisco']=1;
					}
					elseif(($orderPaymentDetails->OrderAddPaymentAddResult->orders->orderItemType==$viaProducts['LongIsland']['orderItemType']) && ($orderPaymentDetails->OrderAddPaymentAddResult->orders->typeSpecificId==$viaProducts['LongIsland']['typeSpecificId'])) {
						$_SESSION['HS-LongIsland']=1;
					}
					//for Housing Report Single Issue End
			}
			// Maintain the transaction log

			return true;

		}// end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['OrderAddPaymentAdd'],$viaRequest);
				return $getExactError;
			}

		}// end of catch block

	}// end of function addOrderAndPayment


	// This function will get all orders of the user on the same orderClassId
	// and return the subscription id of the latest order
	// it will return 0 in case of no matched orders
	function getLatestSubscriptionId($viaId,$orderClassId){
		$subscriptionId=0;

		$custIdent=$this->getCustIdent();
		try{
			$orders=$this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>true,'customerIds'=>$viaId,'orderItemTypeList'=>null,'orderStatus'=>'ALL'));

			// no order found
			if(!$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders){
				return $subscriptionId;
			}
			// orders found
			else{
				// set order expire date to null
				$orderDate='';
				$tempOrderDate = '';
				$tempOsubscriptionId ='';
				foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){
					// order ofwhich are not canceled - most likely to be used for setting subscriptionId
					if($orderClassId == $val->orderClassId && $val->cancel_reason=='' && $val->subscriptionId){
						if($val->expireDate >$tempOrderDate  &&  $val->subscriptionId > $tempOsubscriptionId){
							$tempOrderDate = $val->expireDate;
							$tempOsubscriptionId = $val->subscriptionId;
						}
					}elseif($orderClassId == $val->orderClassId && $val->subscriptionId){ //get camcelled ones if acive are not found
						if($val->subscriptionId > $tempOsubscriptionId) {
							 $tempOsubscriptionId = $val->subscriptionId;
						}
					}
				}

			  	$orderDate=$val->tempOrderDate;
			 	$subscriptionId=$tempOsubscriptionId;
			}
		}
		catch(ViaException $ee){
			return $subscriptionId;
		}
		return $subscriptionId;
	}


	// this function sets auto_renew 1 or 0 to all previous products on the same
	// subscription_Id, orderClassId in the cart.
	function setProductTrail($orderClassId,$subscriptionId,$typeSpecificId,$auto_renew,$orderItemType){
		global $_SESSION;
		foreach($_SESSION['products'][$orderItemType] as $key => $val){

			if($val['orderClassId']==$orderClassId && $val['typeSpecificId']==$typeSpecificId && $val['subscriptionId']==$subscriptionId ){
				$result=$this->editOrder( $val,$auto_renew );
			}
		}
	}

	// To edit existing order
	// custIdent >>>>>>>>>>>>>>>>>> merchant login and password
	// orderItemEdits >>>>>>>>>>> customer via id , order number, item sequence number and other details
	// $orderArray-----> $_SESSION['products'][typeSpecificId] // subscription
	// $orderArray-----> $_SESSION['combo'] // package
	function editOrder( $orderArray,$auto_renew ) {
			global $viaProducts;
			global $operations;
			global $_SESSION;
			$operation = $operations['OrderItemEdit'];
			$logObj=new ViaException();
			$custIdent=$this->getCustIdent();

		if($orderArray['orderClassId']== $viaProducts['CooperCombo']['orderClassId'] ||$orderArray['orderClassId']== $viaProducts['FlexfolioCombo']['orderClassId']){
			$orderItemType='BASIC_PACKAGE';
		}
		else{
			$orderItemType=$orderArray['orderItemType'];
		}

		$orderItemEdits=array(
		    'orderNumber'=>$orderArray['orderNumber'],
		    'orderItemSeq'=>$orderArray['orderItemSeq'],
		    'orderClassId'=>$orderArray['orderClassId'],
		    'orderCodeId'=>$orderArray['orderCodeId'],
		    'sourceCodeId'=>$orderArray['sourceCodeId'],
		    'orderItemType'=>$orderItemType,
			'subscriptionId'=>$orderArray['subscriptionId'],
		    'typeSpecificId'=>$orderArray['typeSpecificId'],
		    'qty'=>0,
		    'auto_renew'=>$auto_renew
		);
		 $viacart= $this->setRefSourceCode($orderItemEdits, true) ;


		try {

			// editing orders
			$editOrderDetails=$this->viaObj->OrderItemEdit(array('custIdent'=>$custIdent,'orderItemEdits'=>$orderItemEdits));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
				if($auto_renew=='AUTO_RENEW'){
					$auto_renew_flag=1;
				}
				elseif($auto_renew=='DO_NOT_RENEW'){
					$auto_renew_flag=0;
				}
			$_SESSION['products'][$orderItemEdits['orderItemType']][$orderItemEdits['typeSpecificId']]['auto_renew']=$auto_renew_flag;
			//return substr($editOrderDetails->OrderItemEditResult->expireDate,0,7);
			return true;
		}// end of try block
		catch ( Exception $exception ) {
			//return false;
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['OrderItemEdit'],$viaRequest);
				return $getExactError;
			}

		}//end of catch block

	}// end of function editOrder


	// get order price with shipping and taxes
	// custIdent >>>>>>>> merchant login and password
	// cart >>>>>>>>>>>>>>> order details
	function getOrderPrice ($dummyViaId,$viacart) {
		global $viaProducts;
		global $operations;
		$operation = $operations['OrderPriceGet'];
		$logObj=new ViaException();
		$index=0;
		$dt=date('Y-m-d');
		$OrderItem=array();
		if(count($viacart)==0){
			return $OrderItem;
		}



		foreach($viacart as $key=>$val){
			if($val['oc_id']== $viaProducts['CooperCombo']['orderClassId'] ||$val['oc_id']== $viaProducts['FlexfolioCombo']['orderClassId']){
				$orderItemType='BASIC_PACKAGE';
			}
			else{
				$orderItemType=$val['orderItemType'];
			}
			$OrderItem[$index]['customerIdVia']=$dummyViaId;
			$OrderItem[$index]['orderClassId']= $val['oc_id'];
			$OrderItem[$index]['orderCodeId']= $val['order_code_id'];
			$OrderItem[$index]['sourceCodeId']= $val['source_code_id'];
			$OrderItem[$index]['orderItemType']=$orderItemType;
			$OrderItem[$index]['typeSpecificId']= $val['subscription_def_id'];
			$OrderItem[$index]['paymentAccountNumb']= 1;
			$OrderItem[$index]['qty']= 1;
			$index++;
		}

		$arrCart=array(
			'billDate'=>$dt,
			'items'=>array(
			'OrderItem'=>$OrderItem
			)
		);

		try {

			$custIdent=$this->getCustIdent();
			$custIdent['customerIdVia']=$dummyViaId;

			// get complete order price
			$orderPrice=$this->viaObj->OrderPriceGet(array('custIdent'=>$custIdent,'cart'=>$arrCart));

			//return $orderPrice->OrderPriceGetResult->orders->typeSpecificId;
			$result=array();

			// no result found
			if(!isset($orderPrice->OrderPriceGetResult->orders)){
				return $result;
			}
			//htmlprint_r($orderPrice->OrderPriceGetResult->orders);
			// mutile items in cart
			if(count($orderPrice->OrderPriceGetResult->orders)>1){

				foreach($orderPrice->OrderPriceGetResult->orders as $key=>$val){
					$result[$val->orderItemType][$val->typeSpecificId]=$val->price;
				}

			}
			// single item in cart
			else{
				$result[$orderPrice->OrderPriceGetResult->orders->orderItemType][$orderPrice->OrderPriceGetResult->orders->typeSpecificId]=$orderPrice->OrderPriceGetResult->orders->price;
			}

			// order net payable price
			if($orderPrice->OrderPriceGetResult->orderSummary){
				$result['netPrice']=$orderPrice->OrderPriceGetResult->orderSummary->ccTotalAmount;
			}

			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$orderPrice->OrderPriceGetResult->customer->email,$orderPrice->OrderPriceGetResult->customer->customerIdVia);
			return $result;
		}//end of try block
		catch ( Exception $exception ) {

			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{

				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['OrderPriceGet'],$viaRequest);
				return $getExactError;
			}

		}//end of catch block

	}//end of function getOrderPrice


	function getUnitBasedOrder($subdefIdArr) {
	session_start();
		global $viaProducts,$globaldummyViaId ;
		global $operations;
		$operation = $operations['OrderUnitsAdjust'];
		$logObj=new ViaException();
		if($_SESSION['LoggedIn']){
			 $dummyViaId = $_SESSION['viaid'];
		}else{
			$dummyViaId = $globaldummyViaId;
		}

		$dt=date('Y-m-d');

		if(count($subdefIdArr)==0){
			return $OrderItem;
		}

		$OrderItem=array();
		$OrderItem['customerIdVia']=$dummyViaId;
		$OrderItem['orderNumber']=$subdefIdArr['orderNumber'];
		$OrderItem['orderItemSeq']=$subdefIdArr['orderItemSeq'];
		$OrderItem['orderClassId']= $subdefIdArr['orderClassId'];
		$OrderItem['orderCodeId']= $subdefIdArr['orderCodeId'];
		$OrderItem['sourceCodeId']= $subdefIdArr['sourceCodeId'];
		$OrderItem['orderItemType']=$subdefIdArr['orderItemType'];
		$OrderItem['typeSpecificId']= $subdefIdArr['typeSpecificId'];
		$OrderItem['price']= $subdefIdArr['price'];
		$OrderItem['paymentAccountNumb']= 1;
		$OrderItem['qty']= -2;
		$OrderItem['startDate']= $subdefIdArr['startDate'];
		$OrderItem['billDate']= $subdefIdArr['billDate'];
		$OrderItem['description']= $subdefIdArr['description'];
		$OrderItem['subscriptionId']=$subdefIdArr['subscriptionId'];
		$OrderItem['auto_renew']= "DO_NOT_RENEW";
		try {

			$custIdent=$this->getCustIdent();
			$custIdent['customerIdVia']=$dummyViaId;

			$unitDeatils = $this->viaObj->OrderUnitsAdjust(array('custIdent'=>$custIdent,'cust'=>$dummyViaId,'oi'=>$OrderItem,'unit_type_id'=>'NULL'));
			// Maintain the transaction log
			/*$issueDetails =array();
			foreach ($unitDeatils->OrderUnitsAdjustResult->orders as $key=>$value){
				if($value['typeSpecificId'] == $subdefIdArr['typeSpecificId']){
					$issueDetails[] = $unitDeatils->OrderUnitsAdjustResult->orders[$key];
				}
			}*/
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return $unitDeatils;
		}//end of try block
		catch ( Exception $exception ) {

			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{

				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['OrderUnitsAdjust'],$viaRequest);
				return $getExactError;
			}

		}//end of catch block

	}//end of function getUnitBasedOrder

	// get list of orders on which amounts are due to pay
	// custIdent >>>>>>>>>>>>>>>>>>>>>>>>>>>>>> merchant login and password and customer via id
	// includeCancelledItems >>>>>>>>>>>>>> set to true if list of orders include called ones
	//  itemsWithBalanceDueOnly >>>>>>>> set to true if list of orders include those orders on which amount is due
	function getUnbilledOrders( $custIdent,$includeCancelledItems=false,$itemsWithBalanceDueOnly=false) {
			global $operations;
			$operation = $operations['OrderItemsPaymentBalance'];
			$logObj=new ViaException();

		try {

			// get orders those amount is not yet paid
			$unbilledOrders=$this->viaObj->OrderItemsPaymentBalance(array('custIdent'=>$custIdent,'includeCancelledItems'=>$includeCancelledItems,'itemsWithBalanceDueOnly'=>$itemsWithBalanceDueOnly));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$customerAuth->CustomerAuthenticationResult ->customer->email,$customerAuth->CustomerAuthenticationResult ->customer->customerIdVia);
			return $unbilledOrders;

		}//end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,1);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['OrderItemsPaymentBalance'],$viaRequest);

				return $getExactError;
			}
		}//end of catch block

	}// end of function getUnbilledOrders


	// to cancel order of customer
	// custIdent >>>>>>>>>>>>>>>>> merchant login , password and customer via id
	// orderNo >>>>>>>>>>>>>>>>>> number of order which needs to cancel
	// orderItemSeq >>>>>>>>>>> order's Item number which needs to cancel
	// payType >>>>>>>>>>>>>>>>>> mode of payment for this order
	// refundAmount >>>>>>>>>> amount to get refund after cancellation
	// refuncToCustId >>>>>>>> customer id to which redund amount will be credited
	function cancelOrder($orderNo,$orderItemSeq,$payType,$refundAmount,$refundToCustId,$cancelReason) {
		$custIdent=$this->getCustIdent();
			global $operations;
			$operation = $operations['orderCancel'];
			$logObj=new ViaException();
		try {

			// cancel order
			$orderCancel=$this->viaObj->orderCancel(array('custIdent'=>$custIdent,'orderNumber'=>$orderNo,'orderItemSeq'=>$orderItemSeq,'payType'=>$payType,'refundAmount'=>$refundAmount,'refundToCustId'=>$refundToCustId,'cancelReason'=>$cancelReason));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return true;

		}// end of try block
		catch (Exception $exception)
		{
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['orderCancel'],$viaRequest);
				return $getExactError;
			}
		}//end of catch block

	}// end of function cancelOrder


	// this wil set products session
	function resetProductSession($viaId){
		global $viaProducts,$_SESSION;
		$custIdent=$this->getCustIdent();

		try{
			$orders=$this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>true,'customerIds'=>$viaId,'orderItemTypeList'=>null,'orderStatus'=>'ALL'));

			if(!$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders){
				return false;
			}


			foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){

				if($val->orderStatus=='ORDER_PLACED' && $val->orderItemType=='PRODUCT'){

					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderNumber']=$val->orderNumber;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderItemSeq']=$val->orderItemSeq;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderClassId']=$val->orderClassId;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderCodeId']=$val->orderCodeId;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['orderItemType']=$val->orderItemType;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['typeSpecificId']=$val->typeSpecificId;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['startDate']=$val->startDate;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['price']=$val->price;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['qty']=$val->qty;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['sourceCodeId']=$val->sourceCodeId;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['description']=$val->description;
					$_SESSION['products'][$val->orderItemType][$val->typeSpecificId]['auto_renew']=0;

					if($val->typeSpecificId==$viaProducts['BMTP']['typeSpecificId']){
						$_SESSION['BMTP']=1;
					}

				}

			}// end of loop

		}
		catch(ViaException $ee){
			return false;
		}
	}
	// get cancelled orders of customer
	function get_cancelled_orders($viaid){
		$custIdent=$this->getCustIdent();
		try{
			$orders=$this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>true,'customerIds'=>$viaid,'orderItemTypeList'=>null,'orderStatus'=>'ALL'));

			if(!$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders){
				return false;
			}
			$cancelledOrders=array();
			$index=0;

			foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){
				//if($val->orderStatus=='CANCEL_CUSTOMER_REQUESTED'){
					$cancelledOrders[$index]=$val->orderClassId;
					$index++;
				//}
			}
			return $cancelledOrders;
		}
		catch (Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				global $operations;
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerSubInfoReq']);
				return $getExactError;
			}
		}
	}//end of function get_cancelled_orders()


	function get_cancelled_order_status($viaid,$orderClassId){
		$custIdent=$this->getCustIdent();
		try{
			$orders=$this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>true,'customerIds'=>$viaid,'orderItemTypeList'=>null,'orderStatus'=>'ALL'));

			if(!$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders){
				return false;
			}
			foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){
					if ($val->orderClassId == $orderClassId){
						$searchWord = '/^cancel/';
						preg_match($searchWord, strtolower($val->orderStatus), $matches, PREG_OFFSET_CAPTURE);
						if((!empty($matches)) || ($val->orderStatus == "SHIPPED_COMPLETE") ){
							return true;
						}
					}
			}
		}
		catch (Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				global $operations;
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerSubInfoReq']);
				return $getExactError;
			}
		}
	}//end of function get_cancelled_order_status()

	//get status of the products with oc_id
	function get_cancelled_order_status_with_ocId($viaid){
		$custIdent=$this->getCustIdent();
		try{
			$orders=$this->viaObj->CustomerSubInfoReq(array('custIdent'=>$custIdent,'bEmanagerIds'=>true,'customerIds'=>$viaid,'orderItemTypeList'=>null,'orderStatus'=>'ALL'));
			if(!$orders->CustomerSubInfoReqResult->CustomerSubInfo->orders){
				return false;
			}

			$cancelledOrdersStatus=array();
			$index=0;

			foreach($orders->CustomerSubInfoReqResult->CustomerSubInfo->orders as $key=>$val){
				if(!array_key_exists($val->orderClassId,$cancelledOrdersStatus)){
					$cancelledOrdersStatus[$val->orderClassId]=$val->orderStatus;
				}
			}
			return $cancelledOrdersStatus;
		}
		catch (Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				global $operations;
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerSubInfoReq']);
				return $getExactError;
			}
		}
	}//end of function get_cancelled_order_status()

	// pass order class id and viaid
	function validate_premium($oc_id,$viaid){

		global $viaProducts;
		$cancelledProducts=array();

		$cancelledProducts=$this->get_cancelled_orders($viaid);

		// check for buzz if ever had in combo
		if($oc_id==$viaProducts['BuzzMonthly']['orderClassId'] && (in_array($viaProducts['CooperCombo']['orderClassId'],$cancelledProducts) && in_array($viaProducts['FlexfolioCombo']['orderClassId'],$cancelledProducts))){
			return true;
		}

		// check for cooper if ever had in combo
		if($oc_id==$viaProducts['CooperMonthly']['orderClassId'] && (in_array($viaProducts['CooperCombo']['orderClassId'],$cancelledProducts))){
			return true;
		}

		// check for flexfolio if ever had in combo
		if($oc_id==$viaProducts['FlexfolioMonthly']['orderClassId'] && (in_array($viaProducts['FlexfolioCombo']['orderClassId'],$cancelledProducts))){
			return true;
		}
		if($cancelledProducts && in_array($oc_id,$cancelledProducts)){
			return true;
		}
		else{
			return false;
		}
	}

	function getTrial($typeSpecificId){
		global $viaProducts;
		$trialArray= array();

		switch($typeSpecificId){

			// Buzz Monthly

			case $viaProducts['BuzzMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BuzzMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BuzzMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BuzzMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BuzzMonthlyTrial']['price'];
				break;

			// Buzz Quarterly

			case $viaProducts['BuzzQuarterly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BuzzQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BuzzQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BuzzQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BuzzQuartTrial']['price'];
				break;

			// Buzz Annual

			case $viaProducts['BuzzAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BuzzAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BuzzAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BuzzAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BuzzAnnualTrial']['price'];
				break;

			// Cooper Monthly

			case $viaProducts['CooperMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['CooperMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['CooperMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['CooperMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['CooperMonthlyTrial']['price'];
				break;

			case $viaProducts['CooperQuarterly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['CooperQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['CooperQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['CooperQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['CooperQuartTrial']['price'];
				break;

			// Cooper Annual

			case $viaProducts['CooperAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['CooperAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['CooperAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['CooperAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['CooperAnnualTrial']['price'];
				break;

			// Quint Monthly

			case $viaProducts['FlexfolioMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['FlexfolioMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['FlexfolioMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['FlexfolioMonthlyTrial']['price'];
				break;

			// Quint Annual

			case $viaProducts['FlexfolioAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['FlexfolioAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['FlexfolioAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['FlexfolioAnnualTrial']['price'];
				break;

			// Optionsmith Monthly
			case $viaProducts['OptionsmithMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['OptionsmithMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['OptionsmithMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['OptionsmithMonthlyTrial']['price'];
				break;

			// Optionsmith Annual
			case $viaProducts['OptionsmithAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['OptionsmithAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['OptionsmithAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['OptionsmithAnnualTrial']['price'];
				break;
			// BMTP Trial
			case $viaProducts['BMTPAlert']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BMTPAlertTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BMTPAlertTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BMTPAlertTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BMTPAlertTrial']['price'];
				break;
			// jack Monthly
			case $viaProducts['JackMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['JackMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['JackMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['JackMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['JackMonthlyTrial']['price'];
				break;

			// Jack Annual
			case $viaProducts['JackAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['JackAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['JackAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['JackAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['JackAnnualTrial']['price'];
				break;

			// ETF Monthly
			case $viaProducts['ETFMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['ETFMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['ETFMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['ETFMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['ETFMonthlyTrial']['price'];
				break;

			// ETF Quarterly
			case $viaProducts['ETFQuart']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['ETFQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['ETFQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['ETFQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['ETFQuartTrial']['price'];
				break;

			// ETF Annualy
			case $viaProducts['ETFAnnual']['typeSpecificId']:
			$trialArray['orderClassId']=$viaProducts['ETFAnnualTrial']['orderClassId'];
			$trialArray['orderCodeId']=$viaProducts['ETFAnnualTrial']['orderCodeId'];
			$trialArray['typeSpecificId']=$viaProducts['ETFAnnualTrial']['typeSpecificId'];
			$trialArray['price']=$viaProducts['ETFAnnualTrial']['price'];
			break;

			// BuyHedge Monthly
			case $viaProducts['BuyHedgeMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BuyHedgeMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BuyHedgeMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BuyHedgeMonthlyTrial']['price'];
				break;


			// BuyHedge Quarterly
			case $viaProducts['BuyHedgeQuart']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['BuyHedgeQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['BuyHedgeQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['BuyHedgeQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['BuyHedgeQuartTrial']['price'];
				break;

			// BuyHedge Annualy
			case $viaProducts['BuyHedgeAnnual']['typeSpecificId']:
			$trialArray['orderClassId']=$viaProducts['BuyHedgeAnnualTrial']['orderClassId'];
			$trialArray['orderCodeId']=$viaProducts['BuyHedgeAnnualTrial']['orderCodeId'];
			$trialArray['typeSpecificId']=$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'];
			$trialArray['price']=$viaProducts['BuyHedgeAnnualTrial']['price'];
			break;


			// The Srockplaybook Monthly
			case $viaProducts['TheStockPlaybookMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookMonthlyTrial']['price'];
				break;

			// The Srockplaybook Quarterly
			case $viaProducts['TheStockPlaybookQuart']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookQuartTrial']['price'];
				break;

			// The Srockplaybook Annualy
			case $viaProducts['TheStockPlaybookAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookAnnualTrial']['price'];
			break;

			// The Srockplaybook Monthly Premium
			case $viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['price'];
				break;

			// The Srockplaybook Quarterly Premium
			case $viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookPremiumQuartTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookPremiumQuartTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookPremiumQuartTrial']['price'];
				break;

			// The Srockplaybook Annualy Premium
			case $viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TheStockPlaybookPremiumAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TheStockPlaybookPremiumAnnualTrial']['price'];
			break;

			// The TechStrat Monthly
			case $viaProducts['TechStratMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TechStratMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TechStratMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TechStratMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TechStratMonthlyTrial']['price'];
				break;

			// The TechStrat Quarterly
			case $viaProducts['TechStratQuarterly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TechStratQuarterTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TechStratQuarterTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TechStratQuarterTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TechStratQuarterTrial']['price'];
				break;

			// The TechStrat Annualy
			case $viaProducts['TechStratAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['TechStratAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['TechStratAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['TechStratAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['TechStratAnnualTrial']['price'];
			break;

			// The Garyk Monthly
			case $viaProducts['GaryKMonthly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['GaryKMonthlyTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['GaryKMonthlyTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['GaryKMonthlyTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['GaryKMonthlyTrial']['price'];
				break;

			// The GaryK Quarterly
			case $viaProducts['GaryKQuarterly']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['GaryKQuarterTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['GaryKQuarterTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['GaryKQuarterTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['GaryKQuarterTrial']['price'];
				break;

			// The GaryK Annualy
			case $viaProducts['GaryKAnnual']['typeSpecificId']:
				$trialArray['orderClassId']=$viaProducts['GaryKAnnualTrial']['orderClassId'];
				$trialArray['orderCodeId']=$viaProducts['GaryKAnnualTrial']['orderCodeId'];
				$trialArray['typeSpecificId']=$viaProducts['GaryKAnnualTrial']['typeSpecificId'];
				$trialArray['price']=$viaProducts['GaryKAnnualTrial']['price'];
			break;



		}
		return $trialArray;
	}

	// returns array of buzz subscription charges
	function getBuzzCharges(){
		global $viaProducts;
		$buzzCharges=array(
			$viaProducts['BuzzMonthly']['typeSpecificId']=>'$'.$viaProducts['BuzzMonthly']['price'],
			$viaProducts['BuzzMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['BuzzMonthly']['price'],
			$viaProducts['BuzzQuarterly']['typeSpecificId']=>'$'.$viaProducts['BuzzQuarterly']['price'],
			$viaProducts['BuzzQuartTrial']['typeSpecificId']=>'$'.$viaProducts['BuzzQuartTrial']['price'],
			$viaProducts['BuzzAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['BuzzAnnual']['price'],
			$viaProducts['BuzzAnnual']['typeSpecificId']=>'$'.$viaProducts['BuzzAnnual']['price']
		);
		return $buzzCharges;
	}

	// returns array of Cooper subscription charges
	function getCooperCharges(){
		global $viaProducts;
		$cooperCharges=array(
			$viaProducts['CooperMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['CooperMonthly']['typeSpecificId']['price'],
			$viaProducts['CooperMonthly']['typeSpecificId']=>'$'.$viaProducts['CooperMonthly']['typeSpecificId']['price'],
			$viaProducts['CooperQuartTrial']['typeSpecificId']=>'$'.$viaProducts['CooperMonthly']['typeSpecificId']['price'],
			$viaProducts['CooperQuarterly']['typeSpecificId']=>'$'.$viaProducts['CooperMonthly']['typeSpecificId']['price'],
			$viaProducts['CooperAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['CooperAnnual']['typeSpecificId']['price'],
			$viaProducts['CooperAnnual']['typeSpecificId']=>'$'.$viaProducts['CooperAnnual']['typeSpecificId']['price']
		);
		return $cooperCharges;
	}

	// returns array of buzz subscription charges
	function getFlexFolioCharges(){
		global $viaProducts;
		$flexfolioCharges=array(
			$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['FlexfolioMonthly']['typeSpecificId']['price'],
			$viaProducts['FlexfolioMonthly']['typeSpecificId']=>'$'.$viaProducts['FlexfolioMonthly']['typeSpecificId']['price'],
			$viaProducts['FlexfolioAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['FlexfolioAnnual']['typeSpecificId']['price'],
			$viaProducts['FlexfolioAnnual']['typeSpecificId']=>'$'.$viaProducts['FlexfolioAnnual']['typeSpecificId']['price']
		);

		return $flexfolioCharges;
	}

	// returns array of optionsmith subscription charges
	function getOptionSmithCharges(){
		global $viaProducts;
		$optionsmithCharges=array(
			$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['OptionsmithMonthly']['price'],
			$viaProducts['OptionsmithMonthly']['typeSpecificId']=>'$'.$viaProducts['OptionsmithMonthly']['price'],
			$viaProducts['OptionsmithAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['OptionsmithAnnual']['price'],
			$viaProducts['OptionsmithAnnual']['typeSpecificId']=>'$'.$viaProducts['OptionsmithAnnual']['price']
		);
		return $optionsmithCharges;
	}
	// returns array of BMTP subscription charges
	function getBMTPCharges(){
		global $viaProducts;
		$BMTPCharges=array(
			$viaProducts['BMTPAlertTrial']['typeSpecificId']=>'$'.$viaProducts['BMTPAlert']['typeSpecificId']['price'],
			$viaProducts['BMTPAlert']['typeSpecificId']=>'$'.$viaProducts['BMTPAlert']['typeSpecificId']['price'],
			$viaProducts['BMTPCKAlert']['typeSpecificId']=>'$'.$viaProducts['BMTPCKAlert']['typeSpecificId']['price'],
			$viaProducts['BMTPComplimentaryAlert']['typeSpecificId']=>'$'.$viaProducts['BMTPComplimentaryAlert']['typeSpecificId']['price']
		);
		return $BMTPCharges;
	}
	// returns array of jack subscription charges
	function getJackCharges(){
		global $viaProducts;
		$jackCharges=array(
			$viaProducts['JackMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['JackMonthly']['price'],
			$viaProducts['JackMonthly']['typeSpecificId']=>'$'.$viaProducts['JackMonthly']['price'],
			$viaProducts['JackAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['JackAnnual']['price'],
			$viaProducts['JackAnnual']['typeSpecificId']=>'$'.$viaProducts['JackAnnual']['price']
		);
		return $jackCharges;
	}
	// returns array of jack subscription charges
	function getETFCharges(){
		global $viaProducts;
		$etfCharges=array(
			$viaProducts['ETFMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['ETFMonthly']['price'],
			$viaProducts['ETFMonthly']['typeSpecificId']=>'$'.$viaProducts['ETFMonthly']['price'],
			$viaProducts['ETFQuartTrial']['typeSpecificId']=>'$'.$viaProducts['ETFQuart']['price'],
			$viaProducts['ETFQuart']['typeSpecificId']=>'$'.$viaProducts['ETFQuart']['price'],
			$viaProducts['ETFAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['ETFAnnual']['price'],
			$viaProducts['ETFAnnual']['typeSpecificId']=>'$'.$viaProducts['ETFAnnual']['price']
		);
		return $etfCharges;
	}

		function getBuyHedgeCharges(){
		global $viaProducts;
		$BuyHedgeCharges=array(
			$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeMonthly']['price'],
			$viaProducts['BuyHedgeMonthly']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeMonthly']['price'],
			$viaProducts['BuyHedgeQuartTrial']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeQuart']['price'],
			$viaProducts['BuyHedgeQuart']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeQuart']['price'],
			$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeAnnual']['price'],
			$viaProducts['BuyHedgeAnnual']['typeSpecificId']=>'$'.$viaProducts['BuyHedgeAnnual']['price']
		);
		return $BuyHedgeCharges;
	}


	function getGaryKCharges(){
		global $viaProducts;
		$garyKCharges=array(
			$viaProducts['GaryKMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['GaryKMonthlyTrial']['price'],
			$viaProducts['GaryKMonthly']['typeSpecificId']=>'$'.$viaProducts['GaryKMonthly']['price'],
			$viaProducts['GaryKQuarterTrial']['typeSpecificId']=>'$'.$viaProducts['GaryKQuarterTrial']['price'],
			$viaProducts['GaryKQuarterly']['typeSpecificId']=>'$'.$viaProducts['GaryKQuarterly']['price'],
			$viaProducts['GaryKAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['GaryKAnnualTrial']['price'],
			$viaProducts['GaryKAnnual']['typeSpecificId']=>'$'.$viaProducts['GaryKAnnual']['price']
		);
		return $garyKCharges;
	}

	// returns array of ETf subscription charges


	function getTheStockPlaybookCharges(){
			global $viaProducts;
			$theStockPlayBookCharges=array(
				$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookMonthly']['price'],
				$viaProducts['TheStockPlaybookMonthly']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookMonthly']['price'],
				$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookQuart']['price'],
				$viaProducts['TheStockPlaybookQuart']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookQuart']['price'],
				$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookAnnual']['price'],
				$viaProducts['TheStockPlaybookAnnual']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookAnnual']['price']
			);
			return $theStockPlayBookCharges;
	}

	function getTheStockPlaybookPremiumCharges(){
			global $viaProducts;
$theStockPlayBookCharges=array(
$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumMonthly']['price'],
$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumMonthly']['price'],
$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumQuart']['price'],
$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumQuart']['price'],				$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumAnnual']['price'],
$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId']=>'$'.$viaProducts['TheStockPlaybookPremiumAnnual']['price']
);
			return $theStockPlayBookPremiumCharges;
	}

	// returns array of The Stockplaybook subscription charges

	function getAdFreeCharges(){
			global $viaProducts;
			$adFreeCharges=array(
				$viaProducts['AdFreeMonthly']['typeSpecificId']=>'$'.$viaProducts['AdFreeMonthly']['price'],
			);
			return $AdFreeCharges;
	}
	// returns array of AdFree subscription charges

		function getTechStratCharges(){
			global $viaProducts;
			$TechStratCharges=array(
				$viaProducts['TechStratMonthlyTrial']['typeSpecificId']=>'$'.$viaProducts['TechStratMonthly']['price'],
				$viaProducts['TechStratMonthly']['typeSpecificId']=>'$'.$viaProducts['TechStratMonthly']['price'],
				$viaProducts['TechStratQuarterTrial']['typeSpecificId']=>'$'.$viaProducts['TechStratQuarterly']['price'],
				$viaProducts['TechStratQuarterly']['typeSpecificId']=>'$'.$viaProducts['TechStratQuarterly']['price'],
				$viaProducts['TechStratAnnualTrial']['typeSpecificId']=>'$'.$viaProducts['TechStratAnnual']['price'],
				$viaProducts['TechStratAnnual']['typeSpecificId']=>'$'.$viaProducts['TechStratAnnual']['price']
			);
			return $TechStratCharges;
	}

	function getHousingReportCharges(){
		global $viaProducts;
		$HousingReportCharges=array(
			$viaProducts['Housing3Months']['typeSpecificId']=>'$'.$viaProducts['Housing3Months']['price'],
			$viaProducts['Housing6Months']['typeSpecificId']=>'$'.$viaProducts['Housing6Months']['price'],
			$viaProducts['HousingAnnual']['typeSpecificId']=>'$'.$viaProducts['HousingAnnual']['price']
		);
		return $HousingReportCharges;
	}

	function getHousingSingleCharges(){
		global $viaProducts;
		$HousingSingleCharges=array(
			$viaProducts['LasVegas']['typeSpecificId']=>'$'.$viaProducts['LasVegas']['price'],
			$viaProducts['Chicago']['typeSpecificId']=>'$'.$viaProducts['Chicago']['price'],
			$viaProducts['Phoenix']['typeSpecificId']=>'$'.$viaProducts['Phoenix']['price'],
			$viaProducts['WashingtonDC']['typeSpecificId']=>'$'.$viaProducts['WashingtonDC']['price'],
			$viaProducts['SanDiego']['typeSpecificId']=>'$'.$viaProducts['SanDiego']['price'],
			$viaProducts['NewYorkMetro']['typeSpecificId']=>'$'.$viaProducts['NewYorkMetro']['price'],
			$viaProducts['Miami']['typeSpecificId']=>'$'.$viaProducts['Miami']['price'],
			$viaProducts['Atlanta']['typeSpecificId']=>'$'.$viaProducts['Atlanta']['price'],
			$viaProducts['Dallas']['typeSpecificId']=>'$'.$viaProducts['Dallas']['price'],
			$viaProducts['LosAngles']['typeSpecificId']=>'$'.$viaProducts['LosAngles']['price'],
			$viaProducts['Minneapolis']['typeSpecificId']=>'$'.$viaProducts['Minneapolis']['price'],
			$viaProducts['Portland']['typeSpecificId']=>'$'.$viaProducts['Portland']['price'],
			$viaProducts['Orlendo']['typeSpecificId']=>'$'.$viaProducts['Orlendo']['price'],
			$viaProducts['Seattle']['typeSpecificId']=>'$'.$viaProducts['Seattle']['price'],
			$viaProducts['SanFrancisco']['typeSpecificId']=>'$'.$viaProducts['SanFrancisco']['price'],
			$viaProducts['LongIsland']['typeSpecificId']=>'$'.$viaProducts['LongIsland']['price']
		);
		return $HousingSingleCharges;
	}

	// returns empty array if user does not has buzz
	// returns his buzz order details if user has buzz
	function getBuzz($ProductsSession){
		$buzzValues=array();

		global $viaProducts;
		$buzzValues[0]=$viaProducts['BuzzMonthlyTrial']['typeSpecificId'];
		$buzzValues[1]=$viaProducts['BuzzMonthly']['typeSpecificId'];
		$buzzValues[2]=$viaProducts['BuzzAnnualTrial']['typeSpecificId'];
		$buzzValues[3]=$viaProducts['BuzzAnnual']['typeSpecificId'];
		$buzzValues[4]=$viaProducts['BuzzComplimentary']['typeSpecificId'];
		$buzzValues[5]=$viaProducts['BuzzCK']['typeSpecificId'];
	    $buzzValues[6]=$viaProducts['BuzzScott']['typeSpecificId'];
		$buzzValues[7]=$viaProducts['BuzzQuartTrial']['typeSpecificId'];
		$buzzValues[8]=$viaProducts['BuzzQuarterly']['typeSpecificId'];

		$buzzStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $buzzStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		// check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$buzzValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$buzzStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}
		// check for do_not_renew
		if(count($buzzStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$buzzValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$buzzStatus=$val;
				}
			}
		}
		return $buzzStatus;
	}

	// returns empty array if user does not has cooper
	// returns his buzz order details if user has cooper
	function getCooper($ProductsSession){
		global $viaProducts;
		$CooperValues=array();

		$CooperValues[0]=$viaProducts['CooperMonthlyTrial']['typeSpecificId'];
		$CooperValues[1]=$viaProducts['CooperAnnualTrial']['typeSpecificId'];
		$CooperValues[2]=$viaProducts['CooperMonthly']['typeSpecificId'];
		$CooperValues[3]=$viaProducts['CooperAnnual']['typeSpecificId'];
		$CooperValues[4]=$viaProducts['CooperComplimentary']['typeSpecificId'];
		$CooperValues[5]=$viaProducts['CooperCK']['typeSpecificId'];
		$CooperValues[6]=$viaProducts['CooperQuartTrial']['typeSpecificId'];
		$CooperValues[7]=$viaProducts['CooperQuarterly']['typeSpecificId'];

		$CooperStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $CooperStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		// check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$CooperValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$CooperStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}


			}
		}
		// check for do_not_renew
		if(count($CooperStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$CooperValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);

					$CooperStatus=$val;
				}
			}
		}
		return $CooperStatus;
	}

	// returns empty array if user does not has FlexFolio
	// returns his buzz order details if user has FlexFolio
	function getFlexFolio($ProductsSession){
		global $viaProducts;
		$FlexFolioValues=array();

		$FlexFolioValues[0]=$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId'];
		$FlexFolioValues[1]=$viaProducts['FlexfolioAnnualTrial']['typeSpecificId'];
		$FlexFolioValues[2]=$viaProducts['FlexfolioMonthly']['typeSpecificId'];
		$FlexFolioValues[3]=$viaProducts['FlexfolioAnnual']['typeSpecificId'];
		$FlexFolioValues[4]=$viaProducts['FlexfolioComplimentary']['typeSpecificId'];
		$FlexFolioValues[5]=$viaProducts['FlexfolioCK']['typeSpecificId'];

		$FlexFolioStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $FlexFolioStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$FlexFolioValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$FlexFolioStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($FlexFolioStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$FlexFolioValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$FlexFolioStatus=$val;
				}
			}
		}
		return $FlexFolioStatus;
	}
	// returns empty array if user does not has Optionsmith
	// returns his Optionsmith order details if user has Optionsmith
	function getOptionSmith($ProductsSession){
		global $viaProducts;
		$OptionSmithValues=array();

		$OptionSmithValues[0]=$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId'];
		$OptionSmithValues[1]=$viaProducts['OptionsmithAnnualTrial']['typeSpecificId'];
		$OptionSmithValues[2]=$viaProducts['OptionsmithMonthly']['typeSpecificId'];
		$OptionSmithValues[3]=$viaProducts['OptionsmithAnnual']['typeSpecificId'];
		$OptionSmithValues[4]=$viaProducts['OptionsmithComplimentary']['typeSpecificId'];
		$OptionSmithValues[5]=$viaProducts['OptionsmithCK']['typeSpecificId'];

		$OptionSmithStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $OptionSmithStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$OptionSmithValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$OptionSmithStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($OptionSmithStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$OptionSmithValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$OptionSmithStatus=$val;
				}
			}
		}
		return $OptionSmithStatus;
	}
	function getBMTPProduct($ProductsSession){
		global $viaProducts;
		$BMTPValues=array();

		$BMTPValues[0]=$viaProducts['BMTP']['typeSpecificId'];

		$BMTPStatus=false;

		if(!$ProductsSession || count($ProductsSession)==0){
			return $BMTPStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$BMTPValues) ){
				$BMTPStatus=true;
			}
		}

		return $BMTPStatus;

	}

	// returns empty array if user does not has BMTP
	// returns his BMTP  order details if user has BMTP
	function getBMTP ($ProductsSession){
		global $viaProducts;


		$BMTPValues=array();
		$BMTPValues[0]=$viaProducts['BMTPAlertTrial']['typeSpecificId'];
		$BMTPValues[1]=$viaProducts['BMTPAlert']['typeSpecificId'];
		$BMTPValues[2]=$viaProducts['BMTPCKAlert']['typeSpecificId'];
		$BMTPValues[3]=$viaProducts['BMTPComplimentaryAlert']['typeSpecificId'];

		$BMTPStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $BMTPStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$BMTPValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$BMTPStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($BMTPStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$BMTPValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$BMTPStatus=$val;
				}
			}
		}
		return $BMTPStatus;
	}
	function getJack($ProductsSession){
		global $viaProducts;
		$JackValues=array();

		$JackValues[0]=$viaProducts['JackMonthlyTrial']['typeSpecificId'];
		$JackValues[1]=$viaProducts['JackAnnualTrial']['typeSpecificId'];
		$JackValues[2]=$viaProducts['JackMonthly']['typeSpecificId'];
		$JackValues[3]=$viaProducts['JackAnnual']['typeSpecificId'];
		$JackValues[4]=$viaProducts['JackComplimentary']['typeSpecificId'];
		$JackValues[5]=$viaProducts['JackCK']['typeSpecificId'];

		$JackStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $JackStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$JackValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$JackStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($JackStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$JackValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$JackStatus=$val;
				}
			}
		}
		return $JackStatus;
	}

	function getETF($ProductsSession){
		global $viaProducts;
		$ETFValues=array();

		$ETFValues[0]=$viaProducts['ETFMonthlyTrial']['typeSpecificId'];
		$ETFValues[1]=$viaProducts['ETFQuartTrial']['typeSpecificId'];
		$ETFValues[2]=$viaProducts['ETFAnnualTrial']['typeSpecificId'];
		$ETFValues[3]=$viaProducts['ETFMonthly']['typeSpecificId'];
		$ETFValues[4]=$viaProducts['ETFQuart']['typeSpecificId'];
		$ETFValues[5]=$viaProducts['ETFAnnual']['typeSpecificId'];
		$ETFValues[6]=$viaProducts['ETFComplimentary']['typeSpecificId'];
		$ETFValues[7]=$viaProducts['ETFCK']['typeSpecificId'];
		$ETFValues[8]=$viaProducts['ETFST']['typeSpecificId'];
		$ETFValues[9]=$viaProducts['ETFST1M']['typeSpecificId'];

		$ETFStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $ETFStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$ETFValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$ETFStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($ETFStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$ETFValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$ETFStatus=$val;
				}
			}
		}
		return $ETFStatus;
	}

	function getBuyHedge($ProductsSession){
		global $viaProducts;
		$BuyHedgeValues=array();

		$BuyHedgeValues[0]=$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId'];
		$BuyHedgeValues[1]=$viaProducts['BuyHedgeQuartTrial']['typeSpecificId'];
		$BuyHedgeValues[2]=$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId'];
		$BuyHedgeValues[3]=$viaProducts['BuyHedgeMonthly']['typeSpecificId'];
		$BuyHedgeValues[4]=$viaProducts['BuyHedgeQuart']['typeSpecificId'];
		$BuyHedgeValues[5]=$viaProducts['BuyHedgeAnnual']['typeSpecificId'];
		$BuyHedgeValues[6]=$viaProducts['BuyHedgeComplimentary']['typeSpecificId'];
		$BuyHedgeValues[7]=$viaProducts['BuyHedgeCK']['typeSpecificId'];
		$BuyHedgeValues[8]=$viaProducts['BuyHedgeST']['typeSpecificId'];
		//$BuyHedgeValues[9]=$viaProducts['BuyHedgeST1M']['typeSpecificId'];

		$BuyHedgeStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $BuyHedgeStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$BuyHedgeValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$BuyHedgeStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($BuyHedgeStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$BuyHedgeValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$BuyHedgeStatus=$val;
				}
			}
		}
		return $BuyHedgeStatus;
	}

	function getGaryK($ProductsSession){
		global $viaProducts;
		$GaryKValues=array();

		$GaryKValues[0]=$viaProducts['GaryKMonthlyTrial']['typeSpecificId'];
		$GaryKValues[1]=$viaProducts['GaryKQuarterTrial']['typeSpecificId'];
		$GaryKValues[2]=$viaProducts['GaryKAnnualTrial']['typeSpecificId'];
		$GaryKValues[3]=$viaProducts['GaryKMonthly']['typeSpecificId'];
		$GaryKValues[4]=$viaProducts['GaryKQuarterly']['typeSpecificId'];
		$GaryKValues[5]=$viaProducts['GaryKAnnual']['typeSpecificId'];
		$GaryKValues[6]=$viaProducts['GaryKComplimentary']['typeSpecificId'];
		$GaryKValues[7]=$viaProducts['GaryKCK']['typeSpecificId'];
		$GaryKValues[8]=$viaProducts['GaryKST']['typeSpecificId'];
		$GaryKStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $GaryKStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$GaryKValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$GaryKStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($GaryKStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$GaryKValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$GaryKStatus=$val;
				}
			}
		}
		return $GaryKStatus;
	}

	function getTheStockPlayBook($ProductsSession){
			global $viaProducts;
			$TheStockPlaybookValues=array();

			$TheStockPlaybookValues[0]=$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId'];
			$TheStockPlaybookValues[1]=$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId'];
			$TheStockPlaybookValues[2]=$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId'];
			$TheStockPlaybookValues[3]=$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'];
			$TheStockPlaybookValues[4]=$viaProducts['TheStockPlaybookQuart']['typeSpecificId'];
			$TheStockPlaybookValues[5]=$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'];
			$TheStockPlaybookValues[6]=$viaProducts['TheStockPlaybookComplimentary']['typeSpecificId'];
			$TheStockPlaybookValues[7]=$viaProducts['TheStockPlaybookCK']['typeSpecificId'];
			$TheStockPlaybookValues[8]=$viaProducts['TheStockPlaybookST']['typeSpecificId'];
			$TheStockPlaybookValues[9]=$viaProducts['TheStockPlaybookST1M']['typeSpecificId'];

			$TheStockPlaybookStatus=array();

			if(!$ProductsSession || count($ProductsSession)==0){
				return $TheStockPlaybookStatus;
			}
			$maxExpireDate=array();
			$dateCurrent=date('Y-m-d');
			//check for auto_renew
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$TheStockPlaybookValues) && $val['auto_renew']==1){
					$maxExpireDate[$key]=substr($val['expireDate'],0,10);
					if($maxExpireDate[$key]>$dateCurrent){
						$TheStockPlaybookStatus=$val;
						$dateCurrent=$maxExpireDate[$key];
					}
				}
			}

			// check for do_not_renew
			if(count($TheStockPlaybookStatus)==0){
				foreach($ProductsSession as $key=>$val){
					if(in_array($key,$TheStockPlaybookValues) && $val['auto_renew']==0){
						unset($val['expireDate']);
						unset($val['price']);
						$TheStockPlaybookStatus=$val;
					}
				}
			}
			return $TheStockPlaybookStatus;
	}


	function getTheStockPlayBookPremium($ProductsSession){
			global $viaProducts;
			$TheStockPlaybookValues=array();
			$TheStockPlaybookValues[0]=$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId'];
			$TheStockPlaybookValues[1]=$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId'];
			$TheStockPlaybookValues[2]=$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId'];
			$TheStockPlaybookValues[3]=$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'];
			$TheStockPlaybookValues[4]=$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'];
			$TheStockPlaybookValues[5]=$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'];
			$TheStockPlaybookValues[6]=$viaProducts['TheStockPlaybookPremiumComplimentary']['typeSpecificId'];
			$TheStockPlaybookValues[7]=$viaProducts['TheStockPlaybookPremiumCK']['typeSpecificId'];
			$TheStockPlaybookStatus=array();

			if(!$ProductsSession || count($ProductsSession)==0){
				return $TheStockPlaybookStatus;
			}
			$maxExpireDate=array();
			$dateCurrent=date('Y-m-d');
			//check for auto_renew
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$TheStockPlaybookValues) && $val['auto_renew']==1){
					$maxExpireDate[$key]=substr($val['expireDate'],0,10);
					if($maxExpireDate[$key]>$dateCurrent){
						$TheStockPlaybookStatus=$val;
						$dateCurrent=$maxExpireDate[$key];
					}
				}
			}

			// check for do_not_renew
			if(count($TheStockPlaybookStatus)==0){
				foreach($ProductsSession as $key=>$val){
					if(in_array($key,$TheStockPlaybookValues) && $val['auto_renew']==0){
						unset($val['expireDate']);
						unset($val['price']);
						$TheStockPlaybookStatus=$val;
					}
				}
			}
			return $TheStockPlaybookStatus;
	}


	function getAdFree($ProductsSession){
		global $viaProducts;
		$AdFreeValues=array();

		$AdFreeValues[0]=$viaProducts['AdFreeMonthly']['typeSpecificId'];
		$AdFreeValues[1]=$viaProducts['AdFreeComplimentary']['typeSpecificId'];
		$AdFreeStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $AdFreeStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$AdFreeValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$AdFreeStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($AdFreeStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$AdFreeValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$AdFreeStatus=$val;
				}
			}
		}
		return $AdFreeStatus;
}

function getTechStrat($ProductsSession){
		global $viaProducts;
		$TechStratValues=array();

		$TechStratValues[0]=$viaProducts['TechStratMonthlyTrial']['typeSpecificId'];
		$TechStratValues[1]=$viaProducts['TechStratQuarterTrial']['typeSpecificId'];
		$TechStratValues[2]=$viaProducts['TechStratAnnualTrial']['typeSpecificId'];
		$TechStratValues[3]=$viaProducts['TechStratMonthly']['typeSpecificId'];
		$TechStratValues[4]=$viaProducts['TechStratQuarterly']['typeSpecificId'];
		$TechStratValues[5]=$viaProducts['TechStratAnnual']['typeSpecificId'];
		$TechStratValues[6]=$viaProducts['TechStratComplimentary']['typeSpecificId'];

		$TechStratStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $TechStratStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$TechStratValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$TechStratStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($TechStratStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$TechStratValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$TechStratStatus=$val;
				}
			}
		}
		return $TechStratStatus;
	}

	function getHousingReport($ProductsSession){
		global $viaProducts;
		$HousingReportValues=array();

		$HousingReportValues[0]=$viaProducts['Housing3Months']['typeSpecificId'];
		$HousingReportValues[1]=$viaProducts['Housing6Months']['typeSpecificId'];
		$HousingReportValues[2]=$viaProducts['HousingAnnual']['typeSpecificId'];
		$HousingReportValues[2]=$viaProducts['HousingComplimentary']['typeSpecificId'];

		$HousingReportStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $HousingReportStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$HousingReportValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$HousingReportStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($HousingReportStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$HousingReportValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$HousingReportStatus=$val;
				}
			}
		}
		return $HousingReportStatus;
	}

	function getHousingSingleIssue($ProductsSession){
		global $viaProducts;
		$HSIssueValues=array();

		$HSIssueValues[0]=$viaProducts['LasVegas']['typeSpecificId'];
		$HSIssueValues[1]=$viaProducts['Chicago']['typeSpecificId'];
		$HSIssueValues[2]=$viaProducts['Phoenix']['typeSpecificId'];
		$HSIssueValues[3]=$viaProducts['WashingtonDC']['typeSpecificId'];
		$HSIssueValues[4]=$viaProducts['SanDiego']['typeSpecificId'];
		$HSIssueValues[5]=$viaProducts['NewYorkMetro']['typeSpecificId'];
		$HSIssueValues[6]=$viaProducts['Miami']['typeSpecificId'];
		$HSIssueValues[7]=$viaProducts['Atlanta']['typeSpecificId'];
		$HSIssueValues[8]=$viaProducts['Dallas']['typeSpecificId'];
		$HSIssueValues[9]=$viaProducts['LosAngles']['typeSpecificId'];
		$HSIssueValues[10]=$viaProducts['Minneapolis']['typeSpecificId'];
		$HSIssueValues[11]=$viaProducts['Portland']['typeSpecificId'];
		$HSIssueValues[12]=$viaProducts['Orlendo']['typeSpecificId'];
		$HSIssueValues[13]=$viaProducts['Seattle']['typeSpecificId'];
		$HSIssueValues[14]=$viaProducts['SanFrancisco']['typeSpecificId'];
		$HSIssueValues[15]=$viaProducts['LongIsland']['typeSpecificId'];


		$HSIssueStatus=array();

		if(!$ProductsSession || count($ProductsSession)==0){
			return $HSIssueStatus;
		}
		$maxExpireDate=array();
		$dateCurrent=date('Y-m-d');
		//check for auto_renew
		foreach($ProductsSession as $key=>$val){
			if(in_array($key,$HSIssueValues) && $val['auto_renew']==1){
				$maxExpireDate[$key]=substr($val['expireDate'],0,10);
				if($maxExpireDate[$key]>$dateCurrent){
					$HSIssueStatus=$val;
					$dateCurrent=$maxExpireDate[$key];
				}
			}
		}

		// check for do_not_renew
		if(count($HSIssueStatus)==0){
			foreach($ProductsSession as $key=>$val){
				if(in_array($key,$HSIssueValues) && $val['auto_renew']==0){
					unset($val['expireDate']);
					unset($val['price']);
					$HSIssueStatus=$val;
				}
			}
		}
		return $HSIssueStatus;
	}

	function getActualPrice($typeSpecificId,$sourceCodeId){
		global $_SESSION,$viaProducts;
		$strViaCart=array();
		// buzz monthly
		if($typeSpecificId==$viaProducts['BuzzMonthlyTrial']['typeSpecificId']){

			$id=$viaProducts['BuzzMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuzzMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuzzMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuzzMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		if($typeSpecificId==$viaProducts['BuzzQuartTrial']['typeSpecificId']){

			$id=$viaProducts['BuzzQuarterly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuzzQuarterly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuzzQuarterly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuzzQuarterly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// buzz annual
		elseif($typeSpecificId==$viaProducts['BuzzAnnualTrial']['typeSpecificId']){

			$id=$viaProducts['BuzzAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuzzAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuzzAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuzzAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;

		}
		// cooper Quarterly
		elseif($typeSpecificId==$viaProducts['CooperQuartTrial']['typeSpecificId']){

			$id=$viaProducts['CooperQuarterly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['CooperQuarterly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['CooperQuarterly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['CooperQuarterly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		// cooper monthly
		elseif($typeSpecificId==$viaProducts['CooperMonthlyTrial']['typeSpecificId']){

			$id=$viaProducts['CooperMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['CooperMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['CooperMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['CooperMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// cooper annual
		elseif($typeSpecificId==$viaProducts['CooperAnnualTrial']['typeSpecificId']){

			$id=$viaProducts['CooperAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['CooperAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['CooperAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['CooperAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// flexfolio monthly
		elseif($typeSpecificId==$viaProducts['FlexfolioMonthlyTrial']['typeSpecificId']){

			$id=$viaProducts['FlexfolioMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['FlexfolioMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['FlexfolioMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['FlexfolioMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// flexfolio annual
		elseif($typeSpecificId==$viaProducts['FlexfolioAnnualTrial']['typeSpecificId']){

			$id=$viaProducts['FlexfolioAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['FlexfolioAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['FlexfolioAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['FlexfolioAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// optionsmith monthly
		elseif($typeSpecificId==$viaProducts['OptionsmithMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['OptionsmithMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['OptionsmithMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['OptionsmithMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['OptionsmithMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// optionsmith annual
		elseif($typeSpecificId==$viaProducts['OptionsmithAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['OptionsmithAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['OptionsmithAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['OptionsmithAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['OptionsmithAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// BMTP alert
		elseif($typeSpecificId==$viaProducts['BMTPAlertTrial']['typeSpecificId']){
			$id=$viaProducts['BMTPAlert']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BMTPAlert']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BMTPAlert']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BMTPAlert']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// jack monthly
		elseif($typeSpecificId==$viaProducts['JackMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['JackMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['JackMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['JackMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['JackMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// jack annual
		elseif($typeSpecificId==$viaProducts['JackAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['JackAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['JackAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['JackAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['JackAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// etf monthly
		elseif($typeSpecificId==$viaProducts['ETFMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['ETFMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['ETFMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['ETFMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['ETFMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// etf quarterly
		elseif($typeSpecificId==$viaProducts['ETFQuartTrial']['typeSpecificId']){
			$id=$viaProducts['ETFQuart']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['ETFQuart']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['ETFQuart']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['ETFQuart']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// etf annually
		elseif($typeSpecificId==$viaProducts['ETFAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['ETFAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['ETFAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['ETFAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['ETFAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		// BuyHedge monthly
		elseif($typeSpecificId==$viaProducts['BuyHedgeMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['BuyHedgeMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuyHedgeMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuyHedgeMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuyHedgeMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// BuyHedge quarterly
		elseif($typeSpecificId==$viaProducts['BuyHedgeQuartTrial']['typeSpecificId']){
			$id=$viaProducts['BuyHedgeQuart']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuyHedgeQuart']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuyHedgeQuart']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuyHedgeQuart']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// BuyHedge annually
		elseif($typeSpecificId==$viaProducts['BuyHedgeAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['BuyHedgeAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['BuyHedgeAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['BuyHedgeAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['BuyHedgeAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}


		// The StockPlayBook monthly
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// The StockPlayBook quarterly
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookQuartTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookQuart']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookQuart']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookQuart']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookQuart']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// The StockPlayBook annually
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		// The StockPlayBook Premium monthly
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookPremiumMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookPremiumMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookPremiumMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookPremiumMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookPremiumMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// The StockPlayBook Premium quarterly
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookPremiumQuartTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookPremiumQuart']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookPremiumQuart']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookPremiumQuart']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookPremiumQuart']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		// The StockPlayBook Premium annually
		elseif($typeSpecificId==$viaProducts['TheStockPlaybookPremiumAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['TheStockPlaybookPremiumAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TheStockPlaybookPremiumAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TheStockPlaybookPremiumAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TheStockPlaybookPremiumAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		//TechStrat Monthly
		elseif($typeSpecificId==$viaProducts['TechStratMonthlyTrial']['typeSpecificId']){
			$id=$viaProducts['TechStratMonthly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TechStratMonthly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TechStratMonthly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TechStratMonthly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		//TechStrat Quarterly
		elseif($typeSpecificId==$viaProducts['TechStratQuarterTrial']['typeSpecificId']){
			$id=$viaProducts['TechStratQuarterly']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TechStratQuarterly']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TechStratQuarterly']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TechStratQuarterly']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}
		//TechStrat Annual
		elseif($typeSpecificId==$viaProducts['TechStratAnnualTrial']['typeSpecificId']){
			$id=$viaProducts['TechStratAnnual']['typeSpecificId'];
			$strViaCart[1]['oc_id']=$viaProducts['TechStratAnnual']['orderClassId'];
			$strViaCart[1]['order_code_id']=$viaProducts['TechStratAnnual']['orderCodeId'];
			$strViaCart[1]['orderItemType']=$viaProducts['TechStratAnnual']['orderItemType'];
			$strViaCart[1]['source_code_id']=$sourceCodeId;
			$strViaCart[1]['subscription_def_id']=$id;
		}

		$price=$this->getOrderPrice ($_SESSION['viaid'],$strViaCart);
		if(empty($price[$strViaCart[1]['orderItemType']][$id])){
			return 0;
		}
		else{
			return $price[$strViaCart[1]['orderItemType']][$id];
		}
	}


	// to add a new payment account
	// custIdent >>>>>>>>>>>>>>>>> merchant login, password and customer via id
	// paymentAcnt >>>>>>>>>>>>> payment account details
	function addPaymentAccount ( $paymentAcnt ) {
			global $operations;
			$operation = $operations['PaymentAccountAdd'];
			$logObj=new ViaException();
		try {
			$custIdent=$this->getCustIdent();
			unset($custIdent['email']);
			unset($custIdent['password']);
			//add a payment account
			$paymentAccountDetails=$this->viaObj->PaymentAccountAdd(array('custIdent'=>$custIdent,'paymentAcnt'=>$paymentAcnt));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return true;

		}//end of try block
		catch (Exception $exception){
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['PaymentAccountAdd'],$viaRequest);

				return $getExactError;
			}
		}//end of catch block

	}//end of function addPaymentAccount


	// to edit peyment account details
	// custIdent >>>>>>>>>>>>>>>>>>>> merchant login,password and customer via id
	// paymentAcnt >>>>>>>>>>>>>>>> payment account detail which has to be updated
	function updatePaymentAccount ( $paymentAcnt ) {
			global $operations;
			$operation = $operations['PaymentAccountEdit'];
			$logObj=new ViaException();


		try {

			$custIdent=$this->getCustIdent();
			// edit payment account
			$updatePaymentDetails=$this->viaObj->PaymentAccountEdit(array('custIdent'=>$custIdent,'paymentAcnt'=>$paymentAcnt));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return true;

		}//end of try block
		catch (Exception $exception){
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['updatePaymentAccount'],$viaRequest);
				return $getExactError;
			}
		}//end of catch block

	}//end of function updatePaymentAccount


	// to make a payment
	// custIdent >>>>>>>>>>>>>>>>>>>>>> merchant login, password and customer via id
	// orderItems >>>>>>>>>>>>>>>>>>>> list of items on which peyment is applied to
	// paymentType >>>>>>>>>>>>>>>>>> type of payment is being made
	// paymentAccountNumb >>>>>>> payment account number in case of paymentType is null
	// ccNumber >>>>>>>>>>>>>>>>>>>>>> credit card number if paymentType is CC
	// ccExpireDate >>>>>>>>>>>>>>>>> credit card expire date if paymentType is CC
	// ccVerificationValue >>>>>>>>> in case of paymentType is CC
	// checkNumber >>>>>>>>>>>>>>>>> if cheque number if paymentType is CK
	function addPayment ( $custIdent,$orderItems,$paymentType=null,$paymentAccountNumb=null,$ccNumber=null,$ccExpireDate=null,$ccVerificationValue=null,$checkNumber=null ) {

		try{

			// make a payment
			$paymentDetails=$this->viaObj->PaymentAdd(array('custIdent'=>$custIdent,'orderItems'=>$orderItems,'paymentType'=>$paymentType,'paymentAccountNumb'=>$paymentAccountNumb,'ccNumber'=>$ccNumber,'ccExpireDate'=>$ccExpireDate,'ccVerificationValue'=>$ccVerificationValue,'checkNumber'=>$checkNumber));
			return true;

		}//end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$getExactError = $ee->parseapierrors($ee,$operations['PaymentAdd']);
				return $getExactError;
			}


		}//end of catch block

	}//end of function addPayment


	// to process shopping cart
	// custIdent >>>>>>>>>>>>> merchant login, password and customer via id
	// cart >>>>>>>>>>>>>>>>>>>> order details
	function processShoppingCart( $custIdent,$viacart){

		try {

			// processing a shopping cart
			$shoppingCartProcessDetails=$this->viaObj->ProcessShoppingCart(array('custIdent'=>$custIdent,'cart'=>$viacart));
			return $shoppingCartProcessDetails;

		}//end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$getExactError = $ee->parseapierrors($ee,$operations['ProcessShoppingCart']);
				return $getExactError;
			}



		}// end of catch block
	}//end of function processShoppingCart


	// this function will authenticate existing user with customer login id and password
	// customerLogin >>>>>>>>>> customer login id
	// customerPassword >>>>> customer password
	public function authenticateCustomer($customerLogin,$customerPassword){
			global $operations;
			$operation = $operations['CustomerAuthentication'];
			$logObj=new ViaException();

		try{
		$customerAuth=$this->viaObj->CustomerAuthentication(array('sCustLogin'=>$customerLogin,'sCustPassword'=>$customerPassword));
		// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$customerAuth->CustomerAuthenticationResult ->customer->email,$customerAuth->CustomerAuthenticationResult ->customer->customerIdVia);
		return $customerAuth;
		}
		catch (Exception $fault)
		{
			try
			{
				throw new ViaException($fault->faultstring,11);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['CustomerAuthentication'],$viaRequest);
				return $getExactError;
			}
		}




	}// end of  function authenticateCustomer


	// to update customer inormation
	// custIdent >>>>>>>>>>>>>>>>>>> merchant login and password and customer via id
	// custUpdates >>>>>>>>>>>>>>>> customer information which has to be updated
	public function updateCustomer ( $custUpdates ) {
			global $operations;
			$operation = $operations['CustomerUpdate'];
			$logObj=new ViaException();
		$custIdent=$this->getCustIdent();
		try {
			$customerUpdateDetails=$this->viaObj->CustomerUpdate(array('custIdent'=>$custIdent,'custUpdates'=>$custUpdates));
			// Maintain the transaction log
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return true;
		}
		catch (Exception $exception ) {


			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();

				$getExactError = $ee->parseapierrors($ee,$operations['CustomerUpdate'],$viaRequest);
				return $getExactError;
			}
		}//end of catch block

	}//end of function updateCustomer


	// to reset customer password and send an email to customer
	// custIdent >>>>>>>>>>>>>>>>> merchant login and password along with customer via id
	public function resetCustomerPassword($FieldsArray){
		try {
			$custIdent=$this->getCustIdent();
			$filterarray =array('merchantLogin' => $custIdent['merchantLogin'],'merchantPassword' => $custIdent['merchantPassword']);
			$custIdent=array_merge($filterarray,$FieldsArray);
			//htmlprint_r($custIdent);exit;
			// reset customer login password
			$customerPasswordReset=$this->viaObj->CustomerLoginReset(array('custIdent'=>$custIdent));
			// $customerPasswordReset->CustomerLoginResetResult
			return $customerPasswordReset->CustomerLoginResetResult;

		}//end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				global $operations;
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerLoginReset']);
				return $getExactError;
			}
		}//end of catch block

	}//end of function resetCustomerPassword

	public function populateCountry($country){
		$country=strtoupper($country);
		try{
			$stateCountries = $this->viaObj->StateCountryList();

			$countryStatus=array();
			// get state country list
			$temp=$stateCountries->StateCountryListResult->country_state_codes;

			$countryArray=array();
			// regenerate array
			foreach($temp as $key=>$val){
				$countryArray[$key][country]=strtoupper($val->country);
				$countryArray[$key][state]=strtoupper($val->state);
			}
			// sort aray by country
			asort($countryArray);

			// generate country combo
			foreach($countryArray as $key => $val ){
				$select='';
				if(!in_array($val[country],$countryStatus)){
					if($val[state]==$country) $select='selected';
					if($val[state]!=="CLEARED"){
						$strCombo.="<option $select value=$val[state]>$val[country]</option>";
					}
					$countryStatus[$val[state]]=$val[country];
				}

			}
			return $strCombo;
		}
		catch(Exception $exception){
			return false;
		}
	}

		public function populateCountryProfile($country=NULL){
		$country=strtoupper($country);
		try{
			$stateCountries = $this->viaObj->StateCountryList();
			$countryStatus=array();
			// get state country list
			$temp=$stateCountries->StateCountryListResult->country_state_codes;
			$countryArray=array();
			// regenerate array
			foreach($temp as $key=>$val){
				$countryArray[$key][country]=strtoupper($val->country);
				$countryArray[$key][state]=strtoupper($val->state);
			}
			// sort aray by country
			asort($countryArray);
			// generate country combo
			$strCombo="";
			foreach($countryArray as $key => $val ){
				$select='';
				//htmlprint_r($val[country]);
				if(!in_array($val[country],$countryStatus)){
				// echo "<br>".$val[state].'--'.$country;
					if($val[state]!==$country)
				//	$strCombo.='<a onclick="javascript:setValues(\'countrySelect\',\'NY\',\'country\','');" href="#">'.$val[country].'</a>';

				$strCombo.='<a style="mouse:pointer;" onclick="javascript:setValues(\'countrySelect\',\''.$val[state].'\',\'country\',\''.$val[country].'\')" href="javascript:void(0);" >'.$val[country].'</a>';

					/*$select='selected';
					$strCombo.="<option $select value=$val[state]>$val[country]</option>";*/
					$countryStatus[$val[state]]=$val[country];
				}

			}
			return $strCombo;
		}
		catch(Exception $exception){
			return false;
		}
	}


	// To get list of all products and their prices
	// CustIdent >>>>>>>>>> merchantLogin and password
	// sourceCodeId >>>>> source code id]
	// state >>>>>>>>>>>>>>>>> state code
	public function getPriceList($custIdent,$sourceCodeId,$state=null){

		try{

			// get list of products and prices
			$priceList=$this->viaObj->PriceListGet(array('custIdent'=>$custIdent,'sourceCodeId'=>$sourceCodeId,'state'=>$state));

			foreach($priceList->PriceListGetResult->OrderClass as $key => $val){

				foreach($val->items as $key=>$val){
					$products[$val->typeSpecificId][typeSpecificId]=$val->typeSpecificId;
					$products[$val->typeSpecificId][description]=$val->description;
					$products[$val->typeSpecificId][price]=$val->price;
				}
			}
			return $products;

		}// end of try block
		catch ( Exception $exception) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$getExactError = $ee->parseapierrors($ee,$operations['PriceListGet']);
				return $getExactError;
			}

		}// end of catch block

	}// end of function getPriceList


	// to get data from database
	// custIdent >>>>>>>>>>>>>>>>>>> merchant login and password
	// columnList >>>>>>>>>>>>>>>>>> list of columns to be selected
	// tableList >>>>>>>>>>>>>>>>>>>>> list of tables
	// whereClause >>>>>>>>>>>>>>>> condition, matching fields
	// groupBy >>>>>>>>>>>>>>>>>>>>>> group by condition
	// orderBy >>>>>>>>>>>>>>>>>>>>>> order by condition
	public function sqlSelect ($custIdent,$columnList,$tableList,$whereClause=null,$groupBy=null,$orderBy=null) {

		try {

			// executing sql query
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>$columnList,'tableList'=>$tableList,'whereClause'=>$whereClause,'groupBy'=>$groupBy,'orderBy'=>$orderBy));
			return $sqlResult;

		}//end of try block
		catch ( Exception $exception ) {
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$getExactError = $ee->parseapierrors($ee,$operations['SQLSelect']);
				return $getExactError;
			}
		}//end of catch block

	}//end of function sqlSelect

	//get all products and set session to null for all prodcuts
	public function getProducts(){
		$result=array();
		// code to get all products
		$strQuery="select oc_id,order_code_id,rate_class_id,order_code,product,subscription_def_id,package_id,
source_code_id,source_code from product where not isnull(oc_id) order by id";
		$result=exec_query($strQuery);

		if($result){
			return $result;
		}
		else{
			return $result;
		}
	}//end of function getProducts

	// get product details
	public function getProductDetails($productId,$productType){
		//code to be build
		$strQuery="select oc_id,order_code_id,rate_class_id,order_code,product,subscription_def_id,package_id,
source_code_id,source_code from product where ";
		if($productType=='combo'){
			$strQuery.=" package_id=".$productId;
		}
		else{
			$strQuery.=" subscription_def_id=".$productId;
		}

		$result=exec_query($strQuery,1);

		if($result){
			return $result;
		}
		else{
			return false;
		}
	}//end of function getProductDetails
	public function insertBasicUser($alerts){
	if($alerts==""){
		$alerts=0;
	}
		$values=array(
			'via_id'=>$this->customerIdVia,
			'email'=>$this->email,
			'recv_daily_gazette'=>$alerts,
			'fname'=>$this->nameFirst,
			'lname'=>$this->nameLast
		);


		//if viaid or email is not set
		if(!$this->customerIdVia || !$this->email){
			return false;
			exit;
		}
		// add record to mv db
		$result=insert_query('subscription',$values);
		return $result;
	}//end of function insertBasicUser

	public function setBuzzInDb($viaid,$buzzFlag){
		$viaid=array(
			'via_id'=>$viaid
		);

		$result=update_query('subscription',$buzzFlag,$viaid);
	}

	public function updateUserDetails($viaid,$userDetails){

		$viaid=array(
			'via_id'=>$viaid
		);

		$result=update_query('subscription',$userDetails,$viaid);

		if($result!=0){
			$_SESSION['nameFirst']=$userDetails['fname'];
			$_SESSION['nameLast']=$userDetails['lname'];
			return true;
		}
		else{
			false;
		}
	}


	public function validate_cart(){
		global $_SESSION;
		if(!$_SESSION['viacart']){
			return false;
			exit;
		}
		// subscription
		foreach($_SESSION['viacart'] as $key=>$val){
			if (!$val){
				unset($_SESSION['viacart'][$key]);
			}
		}
		// product
		foreach($_SESSION['viacart'] as $key=>$val){
			if (!$val){
				unset($_SESSION['viacart'][$key]);
			}
		}
		return $_SESSION['viacart'];
	}

/* Start Defined By Aswini on 12'th Dec 2008 */
// this function returns the list of customers those are matching with provided fields
// objMerchant object >>>>>>>>>>>>>>>>> merchat login, merchant password and  customer fields
// on which matching customer records will return from Via web service.

	public function getCustomersExistence($FieldsArray){
		$customerList = array();
		try{
			$custIdent=$this->getCustMerchant();
			$custIdent=array_merge($custIdent,$FieldsArray);

		// get list of customers those are matched with provided fields
			$customerList=$this->viaObj->CustomerGet(array('custIdent'=>$custIdent));

		// if valid response returned
			if($customerList && $customerList->CustomerGetResult){
				$this->customerIdVia=$customerList->CustomerGetResult->Customer->loginInfo->customerIdVia;
				return $this->customerIdVia;
			}
		// if empty response returned
			else{
				$this->customerIdVia=$customerList->CustomerGetResult->Customer->loginInfo->customerIdVia;
				return $this->customerIdVia;
			}

		}// end of try block
		catch (Exception $exception)
		{

			try
			{
				throw new ViaException($exception->faultstring,19);
			}
			catch(ViaException $ee)
			{
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerGet']);
				return $getExactError;
			}

		}
	}// end of catch block

	public function getCustMerchant(){
		$custIdent=array('merchantLogin'=>$this->merchantLogin,'merchantPassword'=>$this->merchantPassword);
		return $custIdent;
	}//end of function getCustMerchant

// Author: Aswini 17th Dec'2008

public function getCustomersViaDetail($FieldsArray)
{
			global $operations;
			$operation = $operations['CustomerGet'];
			$logObj=new ViaException();
		$customerList = array();
		try{
			$custIdent=$this->getCustMerchant();
			$custIdent=array_merge($custIdent,$FieldsArray);
			$customerList=$this->viaObj->CustomerGet(array('custIdent'=>$custIdent));
			// Maintain the transaction log

			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);

			if($customerList && $customerList->CustomerGetResult)
			{
				return $customerList;
			}
			else{
				return $customerList;
			}
		}
		catch (Exception $exception)
		{
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerGet'],$viaRequest);

				return $getExactError;
			}

		}
}

public function getCustomerSubInfoReq ($viaId,$FieldsArray)
{
			global $operations;
			$operation = $operations['CustomerSubInfoReq '];
			$logObj=new ViaException();
			$customerList = array();
		try{
			$custIdent=$this->getCustMerchant();
			$custIdent=array_merge(array('customerIdVia'=>$viaId),$custIdent);
			$subInfo['custIdent']=$custIdent;
			$arrSubInfo=array_merge($subInfo,$FieldsArray);
			$orderList=$this->viaObj->CustomerSubInfoReq ($arrSubInfo);
			// Maintain the transaction log
			$logObj->viaLogTransaction($operation,$this->viaObj->__getLastRequest(),$this->viaObj->__getLastResponse(),$_SESSION['email'],$_SESSION['viaid']);
			return $orderList;
		}
		catch (Exception $exception)
		{
			try
			{
				throw new ViaException($exception->faultstring,115);
			}
			catch(ViaException $ee)
			{
				$viaRequest=$this->viaObj->__getLastRequest();
				$getExactError = $ee->parseapierrors($ee,$operations['CustomerSubInfoReq'],$viaRequest);

				return $getExactError;
			}
		}
}

function getReferralCode($referralCode){
	$strQuery="select source_code_id from discount where source_code=$referralCode";
	$result=exec_query($strQuery,1);
	if($result){
		return $result;
	}
	else{
		return $result;
	}
}

public function getProductDetailsArray($productIds,$productType=null){
	//code to be build
	$strQuery="select oc_id,order_code_id,rate_class_id,order_code,product,subscription_def_id,package_id,price,source_code_id,source_code,1 as qty from product where ";
	if($productType=='combo'){
		$strQuery.=" package_id=".$productId;
	}
	else{
		$productIds=str_replace(',',"','",$productIds);
		$strQuery.=" subscription_def_id in ('".$productIds."') and orderItemType='".$productType."'";
	}
	$result=exec_query($strQuery);
	if($result){
		return $result;
	}
	else{
		return $result;
	}
}//end of function getProductDetailsArray

/*Set customer order in sub_temp_orders and will place after activation*/
	public function setCustomerOrderTemp($subid,$viadefid,$viaorderid,$viasourcecodeid){
	    $params['subscription_id']=$subid;
		$params['via_subscription_def_id']= $viadefid;
		$params['via_order_id']=$viaorderid;
		$params['via_source_code_id']=$viasourcecodeid;
		$params['is_activated']=0;
	    $id=insert_query("sub_temp_orders",$params,$safe=0);
		if(isset($id)){
			return $id;
		}else{
			return 0;
		}
	}

/*get customer order from sub_temp_orders*/
	public function getCustomerOrderTemp($temporders){

	$getTempOrder=explode(",",$temporders);
	foreach($getTempOrder as $key=>$value){
		$posval= strpos($value, '-');
		$subiddef = "'".substr($value,'0',$posval)."'";
		$sourceIdCode = substr($value,$posval+1);

		if($key>0){
			$subdefid.=','.$subiddef;
			$sourceCodeId.=','.$sourceIdCode;
		}else{
			$subdefid=$subiddef;
			$sourceCodeId=$sourceIdCode;
		}
	}
	$qry="select oc_id,order_code_id,source_code_id,orderItemType,subscription_def_id from product where subscription_def_id in (".$subdefid.") and orderItemType='SUBSCRIPTION' order by subscription_def_id;";
		$result=exec_query($qry);
		$sourceid=explode(",",$sourceCodeId);
		$key=0;
		foreach($result as $key=>$value){
			$value['source_code_id']=$sourceid[$key];
			$getresult[] =$value;
		}
		if(isset($getresult)){
			return $getresult;
		}else{
			return 0;
		}
	}

	public function setOrderAfterAccountActivation($temporders,$subid,$email,$fname){
		$getorder=$this->getCustomerOrderTemp($temporders);
		$orderDetails=array();
		$prodCount=0;
		foreach($getorder as $value){
			$orderDetails['OrderItem'][$prodCount]['orderClassId']=$value['oc_id'];
			$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$value['order_code_id'];
			$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$value['source_code_id'];
			$orderDetails['OrderItem'][$prodCount]['orderItemType']=$value['orderItemType'];
			$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$value['subscription_def_id'];
			$orderDetails['OrderItem'][$prodCount]['price']=0;
			$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
			$orderDetails['OrderItem'][$prodCount]['qty']=1;
			$prodCount++;
		}
		$cartDetails=array(
			'billDate'=>date('Y-m-d'),
			'items'=>$orderDetails
		);
		$addorder=$this->addOrder($cartDetails);
		if($addorder=="1"){ // send product email except exchange
		   foreach($getorder as $key=>$value){
			   if($key>0){
					$this->sendProductsWelcomeEmails($subid,$email,$value['subscription_def_id'],$fname);
			   }
		  }
		}
		return $addorder;
	}

	function sendProductsWelcomeEmails($subid,$recipient_email,$subdefid,$name){
         $objViaCtrl= new ViaController();
        /*Buzz softtrial welcome email*/
		if($subdefid=="43"){
			$objViaCtrl->sendSoftTrialEmails($subid,$recipient_email,'buzzST',$pd, $name);
		}
		/*Cooper softtrial welcome email*/
		if($subdefid=="44"){
			$objViaCtrl->sendSoftTrialEmails($subid,$recipient_email,'cooperST',$pd,$name);
		}
		/*OptionSmith softtrial welcome email*/
		if($subdefid=="47"){
			$objViaCtrl->sendSoftTrialEmails($subid,$recipient_email,'optionsmithST',$pd,$name);
		}
		/*LaveryInsight softtrial welcome email*/
		if($subdefid=="46"){
			$objViaCtrl->sendSoftTrialEmails($subid,$recipient_email,'jacklaveryST',$pd,$name);
		}
		/*FlexFolio softtrial welcome email*/
		if($subdefid=="45"){
			$objViaCtrl->sendSoftTrialEmails($subid,$recipient_email,'flexfolioST',$pd,$name);
		}
	}


	 function setAuxilaryFields($account_activated,$temp_orders=""){
			$auxfiled=array();
			$auxfiled['0']['name']='account_activated';
			$auxfiled['0']['value']=$account_activated;
			$auxfiled['1']['name']='temp_orders';
			$auxfiled['1']['value']=$temp_orders;
			$auxilaryfield=array('field'=>$auxfiled);
			return $auxilaryfield;
	}

	function addGATrans($orderid,$city=NULL,$state=NULL,$country=NULL,$viaResult,$viacart){
		global $_SESSION;
		$totAmount=0;

		foreach($viaResult->orders as $item){
			foreach($viacart['items']['OrderItem'] as $cart_item){
				if($cart_item['typeSpecificId']==$item->typeSpecificId){
					$i=count($_SESSION['ecommerceTracking']['items']);
					$_SESSION['ecommerceTracking']['items'][$i+1]['id']=$item->orderNumber;            // order ID - required
					$_SESSION['ecommerceTracking']['items'][$i+1]['SKU']=$item->typeSpecificId;  // SKU/code - required
					$_SESSION['ecommerceTracking']['items'][$i+1]['name']=$item->description;           // product name
					$_SESSION['ecommerceTracking']['items'][$i+1]['category']=getProductType($item->typeSpecificId)." - Subscription";        // category or variation
					$_SESSION['ecommerceTracking']['items'][$i+1]['price']=$item->price;      // unit price - required
					$_SESSION['ecommerceTracking']['items'][$i+1]['quantity']=1;             // quantity - required
					$totAmount+=$_SESSION['ecommerceTracking']['items'][$i+1]['price'];
				}
			}
		}
		$_SESSION['ecommerceTracking']['trans']['id']=$orderid;            // order ID - required
		$_SESSION['ecommerceTracking']['trans']['store']="Minyanville Main Site";  // affiliation or store name
		$_SESSION['ecommerceTracking']['trans']['total']=$totAmount;           // total - required
		$_SESSION['ecommerceTracking']['trans']['city']=$city;        // city
		$_SESSION['ecommerceTracking']['trans']['state']=$state;      // state or province
      	$_SESSION['ecommerceTracking']['trans']['country']=$country;             // country
	}

	/*register moneyshow user*/
	function registerMoneyShowUser($email,$pwd,$firstname,$lastname,$address,$city,$state,$zipCode,$promocode,$rememeber){
           global $viaMaintainenceMsg,$viaProducts,$viaDefaultAddr ;
		   $userObj=new user();
		   $objViaCtrl=new ViaController();
		   $objViaEmail= new ViaEmail();
		   $errMessage='';
			if($this->viaException!=''){
			   $errMessage=$viaMaintainenceMsg;
			   $objViaEmail->sendSoftTrialRegistrationErrorEmail($email,$firstname,$lastname,$errMessage);
			  // exit;

			}
			$errorObj=new ViaException();
			// check login availability
			$fieldsArray['customerLogin']=$email;
			// function is defined in class user and script /lib/_via_controller_lib.php
			$userExistanceStatus=$userObj->checkUserViaAvailibilityByEmail($fieldsArray);
			if($userExistanceStatus!=''){
				$errMessage='A Minyanville account already exists for the above email address.';
				$objViaEmail->sendSoftTrialRegistrationErrorEmail($email,$firstname,$lastname,$errMessage);
				// exit;
			}else{

			// login information
			$loginInfo=array(
				'login'=>$email,
				'password'=>$pwd
			);

			// default address
			//$addresses=$viaDefaultAddr;

			// integrate customer information
			$account_activated=1; /*set account activation to via- 0,1*/
			$auxInfo=$this->setAuxilaryFields($account_activated,$temp_orders="");

			$stateCountries = $this->viaObj->StateCountryList();
			$temp=$stateCountries->StateCountryListResult->country_state_codes;

			$countryStateArray=array();
			// regenerate array
			foreach($temp as $key=>$val){
				$countryStateArray[$key]=strtoupper($val->state);
			}
			$stateKey = array_search($state,$countryStateArray);
			if($stateKey=="" || $state==""){
				$state="NY";
			}

			$addresses=array(
				'typeOfAddr'=>"Billing",
				'address1'=>$address,
				'city'=>$city,
				'state'=>$state,
				'zip'=>$zipCode
			);
			$custInfo=array(
				'loginInfo'=>$loginInfo,
				'addresses'=>$addresses,
				'email'=>$email,
				'nameFirst'=>$firstname,
				'nameLast'=>$lastname,
				'email'=>$email,
                'auxFields'=>$auxInfo
			);
			// cart details with exchange
			$orderDetails=array();
			$prodCount=0;
			$orderDetails['OrderItem'][$prodCount]['orderClassId']=9;
			$orderDetails['OrderItem'][$prodCount]['orderCodeId']=9;
			$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=1;
			$orderDetails['OrderItem'][$prodCount]['orderItemType']='SUBSCRIPTION';
			$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=15;
			$orderDetails['OrderItem'][$prodCount]['price']=0;
			$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
			$orderDetails['OrderItem'][$prodCount]['qty']=1;
			// get promo code status
				if($promocode){
					$promoCodeStatus=getPromoStatus($promocode);
					if ($promoCodeStatus['source_code_id']){
						$sourceCodeId=$promoCodeStatus['source_code_id'];
					}
					else
					{
						 $errMessage="Invalid Promo Code";
						 $objViaEmail->sendSoftTrialRegistrationErrorEmail($email,$firstname,$lastname,$errMessage);
						//  exit;
					}
				}
				else
				{
					$sourceCodeId=1;
				}

				$prodCount++;
				$orderDetails['OrderItem'][$prodCount]['orderClassId']=$viaProducts['BuzzFT1M-ST']['orderClassId'];
				$orderDetails['OrderItem'][$prodCount]['orderCodeId']=$viaProducts['BuzzFT1M-ST']['orderCodeId'];
				$orderDetails['OrderItem'][$prodCount]['sourceCodeId']=$sourceCodeId;
				$orderDetails['OrderItem'][$prodCount]['orderItemType']=$viaProducts['BuzzFT1M-ST']['orderItemType'];
				$orderDetails['OrderItem'][$prodCount]['typeSpecificId']=$viaProducts['BuzzFT1M-ST']['typeSpecificId'];
				$orderDetails['OrderItem'][$prodCount]['price']=$viaProducts['BuzzFT1M-ST']['price'];
				$orderDetails['OrderItem'][$prodCount]['paymentAccountNumb']=1;
				$orderDetails['OrderItem'][$prodCount]['qty']=1;

			$cartDetails=array(
				'billDate'=>date('Y-m-d'),
				'items'=>$orderDetails
			);
			// set user name and password
			$objVia->nameFirst=$firstname;
			$objVia->nameLast=$lastname;
			// send request to via
			// defined in /lib/_via_data_lib.php
			$customerDetails=$this->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=1 );

			// via responded successfully
			if($customerDetails=='true'){
				$via_id=$this->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				$alerts=1;
				$insertedId=$this->insertBasicUser($alerts);
				if(!$insertedId){
					$qrysubid="select id from subscription where via_id='".$via_id."'";
					$getsubid=exec_query($qrysubid,1);
					$insertedId=$getsubid['id'];
				}

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				if($insertedId){
					RegisterUser($insertedId);
					/* Insert into ex_user_profile table */
					$subarray = array('subscription_id'=>$insertedId);
					$profileid = insert_query('ex_user_profile', $subarray);
					$objViaEmail->sendSofttrialWelcomeEmail($email,$pwd,$firstname);
				}



				// account created successfully
				} // minyanville db insertion failed
				else{

					// message handling
					//echo "MVIL DB insertion failed";
					$errMessage=$errorObj->getExactCustomError($customerDetails);
					if($errMessage==''){
						$pattern = '/Error:(.*)/';
						preg_match($pattern, $errViaMessage, $matches);
						$errMessage=$matches[1];
					}
					if($errMessage==''){
						$errMessage='An error occurred while processing your request.';
						$objViaEmail->sendSoftTrialRegistrationErrorEmail($email,$firstname,$lastname,$errMessage);
						// exit;
					}
				}

			}
	}
//Chhandayan User
	function registerDisqusUser($email){
           global $viaMaintainenceMsg,$viaProducts,$viaDefaultAddr ;
		   $userObj=new user();
		   $objViaCtrl=new ViaController();
		   $errorObj=new ViaException();

		   $pwd = 'minyan';
			// login information
			$loginInfo=array(
				'login'=>$email,
				'password'=>$pwd
			);

			// default address
			$addresses=$viaDefaultAddr;
			$firstname = "Guest";
			$lastName = "Minyan";

			// integrate customer information
			$account_activated=1; /*set account activation to via- 0,1*/
			$auxInfo=$this->setAuxilaryFields($account_activated,$temp_orders="");

			$custInfo=array(
				'loginInfo'=>$loginInfo,
				'addresses'=>$addresses,
				'email'=>$email,
				'nameFirst'=>$firstname,
				'nameLast'=>$lastname,
				'email'=>$email,
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
			$objVia->nameFirst=$firstname;
			$objVia->nameLast=$lastname;
			// send request to via
			// defined in /lib/_via_data_lib.php
			$customerDetails=$this->addCustomerAndOrder( $custInfo,$cartDetails,$hardtrial=1 );
			// via responded successfully
			if($customerDetails=='true'){
				$via_id=$this->customerIdVia;
				// insert record to minyanville db
				// defined in /lib/_via_data_lib.php
				$alerts=1;
				$insertedId=$this->insertBasicUser($alerts);
				if(!$insertedId){
					$qrysubid="select id from subscription where via_id='".$via_id."'";
					$getsubid=exec_query($qrysubid,1);
					$insertedId=$getsubid['id'];
				}

				/* Insert into ex_user_email_settings + ex_profile_privacy tables */
				if($insertedId){
					RegisterUser($insertedId);
					/* Insert into ex_user_profile table */
					$subarray = array('subscription_id'=>$insertedId);
					$profileid = insert_query('ex_user_profile', $subarray);
				}
				// account created successfully
			} // minyanville db insertion failed
			else{
				// message handling
				//echo "MVIL DB insertion failed";
				$errMessage=$errorObj->getExactCustomError($customerDetails);
				if($errMessage==''){
					$pattern = '/Error:(.*)/';
					preg_match($pattern, $errViaMessage, $matches);
					$errMessage=$matches[1];
				}
				if($errMessage==''){
					$errMessage='An error occurred while processing your request.';
					// exit;
				}
			}

	}

	public function getSubscriptionEmailList($orderClassId)
	{
		$custIdent=$this->getCustIdent();
		try
		{
		/*
		 Remove From Query
		 order_item.customer_id, order_item.orderhdr_id,
		 */
		//$qryCust = 'order_code_id =67 and payment_status = 1';
		$qryCust = 'order_code_id in ('.$orderClassId.') and order_status = 5 and payment_status = 1';
		$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'customer_address.email','tableList'=>'order_item join customer_address on
		order_item.customer_id = customer_address.customer_id ','whereClause'=>$qryCust,'groupBy'=>'customer_address.email'));
		}
		catch( SoapFault $exception )
		{
		echo $exception->faultstring, "<br>";
		return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
		//return $sqlResult;
	}

	function setSubscriptionCustIfo($getSubCustInfo){

		$table="subscription_cust_info";
		$params['viaid']=$getSubCustInfo->customerIdVia;
		$params['nameFirst']=$getSubCustInfo->nameFirst;
		$params['nameLast']=$getSubCustInfo->nameLast;
		$params['company']=$getSubCustInfo->company;
		$params['email']=$getSubCustInfo->email;
		$params['phoneFax']=$getSubCustInfo->phoneFax;
		$id=insert_query($table,$params,$safe=0);
	}

	function setSubscriptionCustAddress($getSubCustInfo){
		$table="subscription_cust_address";
		$params['viaid']=$getSubCustInfo->customerIdVia;
		$params['address1']=$getSubCustInfo->addresses->address1;
		$params['address2']=$getSubCustInfo->addresses->address2;
		$params['address3']=$getSubCustInfo->addresses->address3;
		$params['city']=$getSubCustInfo->addresses->city;
		$params['state']=$getSubCustInfo->addresses->state;
		$params['zip']=$getSubCustInfo->addresses->zip;
		$params['country']=$getSubCustInfo->addresses->country;
		$params['typeOfAddr']=$getSubCustInfo->addresses->typeOfAddr;

		$id=insert_query($table,$params,$safe=0);
	}

	function setSubscriptionCustOrder($getSubCustOrder,$subId){
		$oCidArray=array();
		$oCidArray['0']=11;
		$oCidArray['1']=10;
		$table="subscription_cust_order_via";

		if(is_array($getSubCustOrder)){
			foreach($getSubCustOrder as $row){
			if(!in_array($row->orderClassId,$oCidArray)){
					$params['viaid']=$row->customerIdVia;
					$params['subscription_id']=$subId;
					$params['po_number']=$row->po_number;
					$params['orderNumber']=$row->orderNumber;
					$params['orderItemSeq']=$row->orderItemSeq;
					$params['orderClassId']=$row->orderClassId;
					$params['orderCodeId']=$row->orderCodeId;
					$params['sourceCodeId']=$row->sourceCodeId;
					$params['orderItemType']=$row->orderItemType;
					$params['typeSpecificId']=$row->typeSpecificId;
					$params['price']=$row->price;
					$params['paymentAccountNumb']=$row->paymentAccountNumb;
					$params['qty']=$row->qty;
					$params['startDate']=$row->startDate;
					$params['expireDate']=$row->expireDate;
					$params['billDate']=$row->billDate;
					$params['description']=$row->description;
					$params['orderStatus']=$row->orderStatus;
					$params['paymentStatus']=$row->paymentStatus;
					$params['subscriptionId']=$row->subscriptionId;
					$params['cancel_reason']=$row->cancel_reason;
					$params['n_issues_left']=$row->n_issues_left;
					$params['item_url']=$row->item_url;
					$params['image_url']=$row->image_url;
					$params['auto_renew']=$row->auto_renew;
					$params['receiver_customer_id']=$row->receiver_customer_id;
					$params['donor_id']=$row->donor_id;
					$params['donor_addr_id']=$row->donor_addr_id;
					$params['renewal_notices_to_id']=$row->renewal_notices_to_id;
					$params['renewal_notices_to_addr_id']=$row->renewal_notices_to_addr_id;
					$params['amount_paid']=$row->amount_paid;
					$params['amount_due']=$row->amount_due;
					$params['installment_plan_id']=$row->installment_plan_id;
					$params['rate_class_id']=$row->rate_class_id;
					$params['rate_class_seq']=$row->rate_class_seq;
					$params['agency_id']=$row->agency_id;
					$id=insert_query($table,$params,$safe=0);

			}
			}
		}else{

					$params['viaid']=$getSubCustOrder->customerIdVia;
					$params['subscription_id']=$subId;
					$params['po_number']=$getSubCustOrder->po_number;
					$params['orderNumber']=$getSubCustOrder->orderNumber;
					$params['orderItemSeq']=$getSubCustOrder->orderItemSeq;
					$params['orderClassId']=$getSubCustOrder->orderClassId;
					$params['orderCodeId']=$getSubCustOrder->orderCodeId;
					$params['sourceCodeId']=$getSubCustOrder->sourceCodeId;
					$params['orderItemType']=$getSubCustOrder->orderItemType;
					$params['typeSpecificId']=$getSubCustOrder->typeSpecificId;
					$params['price']=$getSubCustOrder->price;
					$params['paymentAccountNumb']=$getSubCustOrder->paymentAccountNumb;
					$params['qty']=$getSubCustOrder->qty;
					$params['startDate']=$getSubCustOrder->startDate;
					$params['expireDate']=$getSubCustOrder->expireDate;
					$params['billDate']=$getSubCustOrder->billDate;
					$params['description']=$getSubCustOrder->description;
					$params['orderStatus']=$getSubCustOrder->orderStatus;
					$params['paymentStatus']=$getSubCustOrder->paymentStatus;
					$params['subscriptionId']=$getSubCustOrder->subscriptionId;
					$params['cancel_reason']=$getSubCustOrder->cancel_reason;
					$params['n_issues_left']=$getSubCustOrder->n_issues_left;
					$params['item_url']=$getSubCustOrder->item_url;
					$params['image_url']=$getSubCustOrder->image_url;
					$params['auto_renew']=$getSubCustOrder->auto_renew;
					$params['receiver_customer_id']=$getSubCustOrder->receiver_customer_id;
					$params['donor_id']=$getSubCustOrder->donor_id;
					$params['donor_addr_id']=$getSubCustOrder->donor_addr_id;
					$params['renewal_notices_to_id']=$getSubCustOrder->renewal_notices_to_id;
					$params['renewal_notices_to_addr_id']=$getSubCustOrder->renewal_notices_to_addr_id;
					$params['amount_paid']=$getSubCustOrder->amount_paid;
					$params['amount_due']=$getSubCustOrder->amount_due;
					$params['installment_plan_id']=$getSubCustOrder->installment_plan_id;
					$params['rate_class_id']=$getSubCustOrder->rate_class_id;
					$params['rate_class_seq']=$getSubCustOrder->rate_class_seq;
					$params['agency_id']=$getSubCustOrder->agency_id;
					$id=insert_query($table,$params,$safe=0);
		}

	}

	function setSubscriptionCustAddressFromVia($getSubCustInfo){
		$table="subscription_cust_address";
		$getAddress=$getSubCustInfo->addresses[0];
		$params['viaid']=$getSubCustInfo->customerIdVia;
		$params['address1']=$getAddress->address1;
		$params['address2']=$getAddress->address2;
		$params['address3']=$getAddress->address3;
		$params['city']=$getAddress->city;
		$params['state']=$getAddress->state;
		$params['zip']=$getAddress->zip;
		$params['country']=$getAddress->country;
		$params['typeOfAddr']=$getAddress->typeOfAddr;
		$condition['viaid']=$getSubCustInfo->customerIdVia;
		$id=insert_or_update($table,$params,$condition);
	}


	public function getSubscriptionTrancactionViaDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		  $limit = 'customer_id > '.$start.'  and customer_id<'.$end;

		try
		{
	$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'payment','whereClause'=>$limit));
		 //$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'payment_reversed_payment'));
	//
		}
		catch( SoapFault $exception )
		{
		echo $exception->faultstring, "<br>";
		return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
		//return $sqlResult;
	}

	public function getPaymentReversedViaDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		$limit = 'original_customer_id > '.$start.'  and original_customer_id<'.$end;

		try
		{
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'payment_reversed_payment','whereClause'=>$limit));
		}
		catch( SoapFault $exception )
		{
			echo $exception->faultstring, "<br>";
			return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
	}

	public function getSubscriptionDefViaDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		$limit = 'subscription_def_id > '.$start.'  and subscription_def_id<'.$end;
		try
		{
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'subscription_def','whereClause'=>$limit));
		}
		catch( SoapFault $exception )
		{
			echo $exception->faultstring, "<br>";
			return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
	}

	public function getCustomerDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		$limit = 'customer_id > '.$start.'  and customer_id<'.$end;
		try
		{
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'customer','whereClause'=>$limit));
		}
		catch( SoapFault $exception )
		{
			echo $exception->faultstring, "<br>";
			return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
	}

	public function getCustomerAddresDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		$limit = 'customer_id > '.$start.'  and customer_id<'.$end;
		try
		{
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'customer_address','whereClause'=>$limit));
		}
		catch( SoapFault $exception )
		{
			echo $exception->faultstring, "<br>";
			return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
	}

public function getRenewalHistoryDetail($start,$end)
	{
		$custIdent=$this->getCustIdent();
		$limit = 'subscrip_id > '.$start.'  and subscrip_id<'.$end;
		try
		{
			$sqlResult=$this->viaObj->SQLSelect(array('custIdent'=>$custIdent,'columnList'=>'','tableList'=>'renewal_history ','whereClause'=>$limit));
		}
		catch( SoapFault $exception )
		{
			echo $exception->faultstring, "<br>";
			return array(null,null);
		}
		$xmlDoc = "<SQLSelectResult xmlns:diffgr='urn:schemas-microsoft-com:xml-diffgram-v1' xmlns:xs='http://www.w3.org/2001/XMLSchema'>\n" . $sqlResult->SQLSelectResult->any . "\n</SQLSelectResult>\n";
		$xmlSimp = new SimpleXMLElement($xmlDoc);
		$schema = $xmlSimp->xpath( 'xs:schema/xs:element/xs:complexType/xs:choice/xs:element/xs:complexType/*');
		$dataRows = $xmlSimp->xpath( 'diffgr:diffgram/DocumentElement/*');
		return $dataRows;
	}

}// end of class Via
?>