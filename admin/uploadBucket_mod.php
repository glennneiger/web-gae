<?php
global $HTPFX,$HTHOST;

$json = new Services_JSON();

$action = $_POST['select'];
switch($action)
{
	case 'Radio':
		$fileName  = str_replace(" ","_",$_FILES['fileInput']['name']);
		$imgpath =  "/radio/".$fileName;
      	move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
		$status = "1";
		break;
	case 'MoneyShow':
		$fileName  = str_replace(" ","_",$_FILES['fileInput']['name']);
		$imgpath =  "/MoneyShow/".$fileName;
      	move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
		$status = "1";
		break;
	case 'Zack':
		$fileName  = str_replace(" ","_",$_FILES['fileInput']['name']);
		$imgpath =  "/Zack/".$fileName;
      	move_uploaded_file($_FILES['fileInput']['tmp_name'],"gs://mvassets".$imgpath);
		$status = "1";
		break;
	default:
		$status = "0";
        break;
}

header( "HTTP/1.1 301 Moved Permanently" );
header( "Location: ".$HTPFX.$HTHOST."/admin/uploadBucket.php?status=".$status);
exit;

?>