<?
session_start();

include_once("$D_R/lib/_content_data_lib.php");
include($D_R."/lib/config/_edu_config.php");
include($D_R."/lib/edu/_edu_data_lib.php");
global $D_R, $eduItemMeta;

$bounceback="edu-approve.htm".qsa(array(viewapproved=>$viewapproved,error=>"-"));

$objEduData = new eduData('edu_alerts');
$objremove= new Content("edu_alerts","");
$alertTable="edu_alerts";
$viewapproved =	trim($_REQUEST['viewapproved']);


$approve = $_REQUEST['approve'];
$publishdatefield = $_REQUEST['publishdatefield'];
$delarticle	= $_REQUEST['delarticle'];

if(!count($approve) && !count($delarticle)){
	location($bounceback.urlencode("No changes were made"));
	exit;
}
if(is_array($delarticle)){
	$keys = implode(",",array_keys($delarticle));
	$qry="update edu_alerts set is_deleted='1' WHERE find_in_set(id,'$keys') LIMIT ".count($delarticle);
	exec_query($qry);
	$qryDel="update ex_item_meta set is_live='0' WHERE find_in_set(item_id,'$keys') and item_type='".$eduItemMeta."'";
	exec_query($qryDel);
}

if(is_array($approve)){
	foreach($approve as $article_id => $status)
    {
    	    if($viewapproved!="1"){
           $publish_date_stamp=strtotime($publishdatefield[year][$article_id]."-".$publishdatefield[mo][$article_id]."-".$publishdatefield[day][$article_id]." ".$publishdatefield[hour][$article_id].":".$publishdatefield["min"][$article_id]);
    	    	    	    	
		//	$publish_date_stamp=mktime($publishdatefield[hour][$article_id],$publishdatefield["min"][$article_id],0, $publishdatefield[mo][$article_id],$publishdatefield[day][$article_id],$publishdatefield[year][$article_id]);
			if($publish_date_stamp)
				{
					$publish_date=mysqlNow($publish_date_stamp);
					if($publish_date_stamp > time())
					{
						$is_live = 0;
					}
					else
					{
						$is_live = 1;
					}
				}
				else
				{
					$publish_date = date('Y-m-d H:i:s');
					$is_live = 1;
				}
				$qry = "UPDATE edu_alerts SET is_approved='".($viewapproved?0:1)."' ,publish_date = '".$publish_date."' , is_live='".$is_live."'";
		}
		else {
			$is_live="0";
			$qry = "UPDATE edu_alerts SET is_approved='".($viewapproved?0:1)."' , is_live='".$is_live."'";
		}


		//echo $viewapproved;
		//exit();
		if(!$viewapproved){
			$qry.=", creation_date='".mysqlNow()."' WHERE id='".$article_id."' AND is_approved='0' LIMIT ".count($approve);
		}


		if($viewapproved=='1'){
			$qry.=", creation_date='".mysqlNow()."' WHERE id='".$article_id."' AND is_approved='1' LIMIT ".count($approve);
			/*remove unapprove article from object search*/
			//$objremove->removeUnapprovedItems($keys,$alertTable);
		}

		exec_query($qry);

		$objContent = new Content('edu_alerts',$article_id);
		$objContent->setEduMeta();

		/* set data in meta table and content seo*/
		if($is_live){
			/*Start sending email to subscriber using Via*/
			$url=$objContent->getEduAlertUrl($article_id);
			$objContent->updateContentSeoUrl($article_id,$objEduData->contentType,$url);

	        $qry="select is_sent,title from edu_alerts where id='".$article_id."'";
			$sendEmailResult=exec_query($qry,1);
			$sentEmail=$sendEmailResult['is_sent'];
			/*if($sentEmail=="0" || $sentEmail==""){
				update_query("edu_alerts",array(is_sent=>1),array(id=>$article_id));
	            $from[$elliottWaveFromEmail]= $elliottWaveFromName;
	            $subject=trim(stripslashes($sendEmailResult['title']));
	            $msgfile="/tmp/spam_elliottwave_".mrand().".eml";
	            $msghtmlfile="$D_R/assets/data/".basename($msgfile);
	            $msgurl=$elliottWaveTemplate.qsa(array(id=>$article_id));
	            $mailbody=inc_web($msgurl);
	            include_once("$D_R/lib/_user_controller_lib.php");
	            $userObj=new user();
	            $result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'elliottwave');
	            $error="Posts were changed and an email was sent to subscribers.";

			}*/
		}
    }
}
if(count($delarticle))
{
	location($bounceback.urlencode("The Posts were deleted"));
exit();
}
if(!$viewapproved)
{
	location($bounceback.urlencode("The Posts were changed"));
}
else
{
	location($bounceback.urlencode("The posts were changed and are now not live on the site"));

}
exit;
?>
