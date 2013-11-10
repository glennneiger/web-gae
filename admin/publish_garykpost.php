<?
include_once("../lib/_includes.php");
include_once("../lib/_db.php");
include_once("../lib/MemCache.php");
include_once("../lib/_content_data_lib.php");
include_once("$D_R/lib/garyk/_garykData.php");
include_once("$D_R/lib/config/_garyk_config.php");
$objGaryKData= new garykData("garyk_posts");
global $garyKFrom, $garyKTemplate;

set_time_limit ( 60*30 );//1 hour
echo "Publishing GaryK Report started at : ".date("Y-m-d h:i")."<BR>\n";

 $qry="select id,publish_date,title,category_id,is_sent from garyk_posts where is_approved='1' and is_deleted='0' and is_draft='0' and is_live='0' and publish_date <= '".mysqlNow()."' ORDER BY id";

$result=exec_query($qry);
if($result){
	 foreach($result as $row){
		$par['is_live']='1';
		$par['is_sent']='1';
		update_query("garyk_posts",$par,array(id=>$row['id']));

		$objContent = new Content("garyk_posts",$row['id']);
		$url=$objContent->getGaryKUrl($row['id']);
		$objContent->updateContentSeoUrl($row['id'],"27",$url);
		$objContent->setGaryKMeta();
		if($row['id'] && ($row['is_sent']=='0' || $row['is_sent']=='')){
            $from= $garyKFrom;
            $subject=trim(stripslashes($row['title']));
            $msgfile="/tmp/spam_garyk_".mrand().".eml";
            $msghtmlfile="$D_R/assets/data/".basename($msgfile);
            $msgurl=$garyKTemplate.qsa(array(id=>$row['id']));
            $mailbody=inc_web($msgurl);

              include_once("$D_R/lib/_user_controller_lib.php");
			  $userObj=new user();
			  $result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'GaryK');
		}
	}

}

?>
