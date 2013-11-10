<?
class VIATest
{

public $viaObj=null;

	public function __construct()
	{
		global $viaurl,$merchantlogin,$merchantpwd;
		try{
			// later on we can get following details from config file
			$this->viaUrl=$viaurl;//'http://WSPro.ViaEmanager.net/v2.3/WSPro.asmx?mid=1901';
			$this->merchantLogin=$merchantlogin;//"minyanville";
			$this->merchantPassword=$merchantpwd;//"mini1900";

			// creating via web service object
			$this->viaObj = new SoapClient( $this->viaUrl . "&WSDL", array('trace' => true, 'location' => $this->viaUrl));
			$this->viaException ='';
			return $this->viaObj;

		}// end of try block
		catch (Exception $objException) 
		{
			// Code to MV Error Handler
			$this->viaException = substr($objException->faultstring,0,10);
			return true;
		}// end of catch block

	}//end of constructor
	
	function login($customerLogin,$customerPassword)
	{	
	try{
		$customerAuth=$this->viaObj->CustomerAuthentication(array('sCustLogin'=>$customerLogin,'sCustPassword'=>$customerPassword));
		htmlprint_r($customerAuth);
		}
		catch (Exception $objException) 
		{
			htmlprint_r($objException);
		}
		
	}

}	

$ob = new VIATest();

echo "<b>VIA response for user : kamal@minyanville.com / Password :minyan</b>";
$ob->login('kamal@minyanville.com','minyan');

echo "<b>VIA response for user : cbprf@st.net.au / Password :501492</b>";

$ob->login('cbprf@st.net.au','501492');
?>