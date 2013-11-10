<?
include_once("../lib/_includes.php");
include_once("../lib/_db.php");
include_once("../lib/MemCache.php");
include_once("../lib/_content_data_lib.php");
include_once("$D_R/lib/techstrat/_techstratData.php");
include_once("$D_R/lib/config/_techstrat_config.php");
$objTechStartData= new techstartData("techstrat_posts");
global $techStratFromName,$techStratFromEmail, $techStratTemplate;
set_time_limit ( 60*30 );//1 hour
echo "Publishing TechStrat Report started at : ".date("Y-m-d h:i")."<BR>\n";

$qry="select id,publish_date,title,category_id,is_sent from techstrat_posts where is_approved='1' and is_deleted='0' and is_draft='0' and is_live='0' and publish_date <= '".mysqlNow()."' ORDER BY id";
$result=exec_query($qry);

if($result){
	 foreach($result as $row){
		$par['is_live']='1';
		$par['is_sent']='1';
		update_query("techstrat_posts",$par,array(id=>$row['id']));

		$objContent = new Content("techstrat_posts",$row['id']);
		$premium_value = $objContent->getSyndicate($row['id'],$objContent->contentType);
		if($premium_value!=''){
			$par_premium['is_live'] = '1';
			update_query("ex_item_premium_content",$par_premium,array(item_id=>$row['id']));
		}
		$url=$objContent->getTechStratUrl($row['id']);
		$objContent->updateContentSeoUrl($id,"22",$row['id'],$url);
		$objContent->setTechStartMeta();
		if($row['id'] && ($row['is_sent']=='0' || $row['is_sent']=='')){
            $from[$techStratFromEmail]= $techStratFromName;
            $subject=trim(stripslashes($row['title']));
            $msgfile="/tmp/spam_techstrat_".mrand().".eml";
            $msghtmlfile="$D_R/assets/data/".basename($msgfile);
            $msgurl=$techStratTemplate.qsa(array(id=>$row['id']));
            $mailbody=inc_web($msgurl);
            include_once("$D_R/lib/_user_controller_lib.php");
			$userObj=new user();
			$result = $userObj->emailDetails($from,$subject,utf8_encode($mailbody),'TechStrat');
		}
	}

}

?>
