<?php
global $D_R;
ini_set('memory_limit', '64M');
include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_peter_tchir_config.php");
include($D_R."/lib/peter-tchir/_peterTchir_data_lib.php");

global $D_R,$peterTchirTemplate,$peterTchirFromName,$peterTchirFromEmail;
set_time_limit ( 60*30 );//1 hour
$alertTable="peter_alerts";

echo "Publishing articles started at : ".date("Y-m-d h:i")."<BR>\n";

$articles=array();

$qry="SELECT id,publish_date,title
FROM `peter_alerts` WHERE is_live!='1' AND is_approved='1' AND publish_date <= '".mysqlNow()."'
 AND publish_date >= '".mysqlNow()."'-INTERVAL 1 MONTH ORDER BY id  ";
$result = exec_query($qry);

foreach($result as $key=>$val)
{
	echo "Article ID : ".$val[id]." published<br>\n";
	
	$articles['is_sent']='0';
	$articles['publish_date']= $val[publish_date];
	$articles['is_live']= "1";
	$uid=update_query("peter_alerts",$articles,array(id=>$val[id]));
	
	$objContent = new Content('peter_alerts',$val[id]);
	$objContent->setPeterTchirMeta();
	
	$qryAlert="select is_sent,title from peter_alerts where id='".$val[id]."'";
	$sendEmailResult=exec_query($qryAlert,1);
	$sentEmail=$sendEmailResult['is_sent'];
	if($sentEmail=="0" || $sentEmail==""){
         update_query("peter_alerts",array(is_sent=>1),array(id=>$val[id]));
                        $from[$peterTchirFromEmail]= $peterTchirFromName;
                        $subject=trim(stripslashes($sendEmailResult['title']));
                        $msgfile="/tmp/spam_petertchir_".mrand().".eml";
                        $msghtmlfile="$D_R/assets/data/".basename($msgfile);
                        $msgurl=$peterTchirTemplate.qsa(array(id=>$val['id']));
                        $mailbody=inc_web($msgurl);
                        include_once("$D_R/lib/_user_controller_lib.php");
                        $userObj=new user();
                        $result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'peterTchir');
                        $error="Posts were changed and an email was sent to subscribers.";
	}
}

?>