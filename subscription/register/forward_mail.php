<?php
global $D_R,$CDN_SERVER;
include_once($D_R."/admin/lib/_article_data_lib.php");
$objArticle = new ArticleData();
switch($_REQUEST['action']){
	case "forwardmail" :
		 $mail = array();
		 $arr['friendEmail']=$_REQUEST['to'];
		 $arr['from'] =$_REQUEST['from'];
		 $arr['name']=$_REQUEST['name'];
		 $arr['subject']=$_REQUEST['subject'];
		 $arr['comment']=$_REQUEST['comment'];
		 $DailyDigest_id = $_REQUEST['id'];
		 $forward_id = $objArticle->addForwardDailyDigest($arr);
		 if($forward_id>0)
		 {
		 	$mail['result'] = sendDailyDigestEmail($arr['from'],$arr['subject'],$DailyDigest_id,$arr['friendEmail'],$arr['comment'],$arr['name']);
		 }
		 echo json_encode($mail);
	break;
}
?>