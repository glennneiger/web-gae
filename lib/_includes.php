<?
require_once 'google/appengine/api/mail/Message.php';
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;
use google\appengine\api\mail\Message;

$mailClass = new Message();
$cloudStorageTool = new CloudStorageTools();

ini_set("sendmail_path","/usr/sbin/sendmail -t -i -ob");
//ini_set("session.cookie_domain",".minyanville.com");
date_default_timezone_set('America/New_York');
global $config,$mailClass,$cloudStorageTool,$imageClass;
$D_R=$_SERVER['DOCUMENT_ROOT'];

include_once("$D_R/lib/_auth.php");
include_once("$D_R/lib/_db.php");
include_once("$D_R/lib/_misc.php");
include_once("$D_R/lib/_state.php");
include_once("$D_R/lib/_exchange_config.php");
include_once("$D_R/lib/_exchange_lib.php");
include_once("$D_R/lib/_minyanville.php");
include_once("$D_R/lib/_constants.php");
include_once("$D_R/lib/json.php");
include_once("$D_R/lib/MemCache.php");
include_once("$D_R/lib/_module_design_lib.php");
include_once("$D_R/lib/_module_data_lib.php");
include_once("$D_R/lib/_redesign_design_lib.php");
include_once("$D_R/lib/_redesign_data_lib.php");
include_once("$D_R/lib/_cart.php");
include_once("$D_R/lib/_layout_design_lib.php");
include_once("$D_R/lib/_layout_data_lib.php");
include_once("$D_R/lib/layout_functions.php");
include_once("$D_R/lib/config/_cache_config.php");
include_once("$D_R/lib/_outer_design_lib.php");
include_once ("$D_R/lib/_user_controller_lib.php");

try{
	include_once($D_R."/lib/htmlpurifier4.4/library/HTMLPurifier.includes.php");
	include_once($D_R."/lib/htmlpurifier4.4/library/HTMLPurifier.autoload.php");
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Core.Encoding', 'UTF-8');
	$purifier = new HTMLPurifier($config);
	if(!(stristr($_SERVER["REQUEST_URI"],"admin/") || stristr($_SERVER["REQUEST_URI"],"/emails/")))
	{
		if(!empty($_POST)|| !empty($_REQUEST) || !empty($_GET) || !empty($_COOKIE))
		{
			$_GET = recursive_array_replace($purifier,$_GET);
			$_POST = recursive_array_replace($purifier,$_POST);
			$_REQUEST = recursive_array_replace($purifier,$_REQUEST);
			$_SERVER[REQUEST_URI] = recursive_array_replace($purifier,$_SERVER[REQUEST_URI]);
			$_SERVER['SCRIPT_URI']= recursive_array_replace($purifier,$_SERVER[SCRIPT_URI]);
			$_COOKIE = recursive_array_replace($purifier,$_COOKIE);
		}
	}
}catch(Exception $e){

}


ini_set('magic_quotes_runtime',1);

?>
