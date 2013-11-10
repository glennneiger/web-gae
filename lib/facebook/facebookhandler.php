<?php
global $D_R;
session_start();
include_once($D_R."/lib/facebook/_facebook_handler_lib.php");

$action = $_REQUEST['action'];
$vars = array();
$vars['SID'] = $_SESSION['SID'] ;
$vars['fbId'] = $_REQUEST['fbId'] ;
$vars['action'] = $action ;
$vars['publish_post'] = $_REQUEST['publish_post'] ;
$vars['follow_frnds'] = $_REQUEST['follow_frnds'] ;
$vars['email_alerts'] = $_REQUEST['email_alerts'] ;
$vars['frnd_list']= $_REQUEST['frndlist'];
$vars['fb_permission']= $_REQUEST['fb_permission'];
$vars['country']= $_REQUEST['country'];
$vars['firstname']= $_REQUEST['firstname'];
$vars['lastname']= $_REQUEST['lastname'];
$vars['pwd']= $_REQUEST['pwd'];
$vars['dailydigest']= $_REQUEST['dailydigest'];

$vars['email']= $_REQUEST['uid'];
$objhandler = new fbhandler($vars);


switch ($action){
	case "exists":
		$objhandler->$action();
		echo $objhandler->exists ;
	break;
	case "fbinsert":
		echo $objhandler->$action();

	break;
	case "autologin":
		if($objhandler->exists())			
		{
			echo $objhandler->autologin($fbId,0);
		}else{
			echo $objhandler->fbregisteration();
		}
		
	break;
	case "fbupdate":
		echo $objhandler->fbupdate();

	break;
	case "fbchkpublish":
		echo $objhandler->fbchkpublish();
	break;
	case "fbregisteration":
		echo $objhandler->fbregisteration();
	break;
	case "default":
		break;

}



?>