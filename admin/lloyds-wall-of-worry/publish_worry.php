<?
global $D_R;
include_once($D_R."/lib/_includes.php");
include_once("$D_R/lib/_content_data_lib.php");
set_time_limit ( 60*30 );//1 hour

echo "Publishing Wall of Worry started at : ".date("Y-m-d h:i")."<BR>\n";

$qry="SELECT id,is_approve,publish_date FROM worry_alert WHERE is_live!='1' and is_draft='0' and is_approve='1' AND publish_date <= '".mysqlNow()."' ORDER BY id";
$getResult=exec_query($qry);
if(is_array($getResult)){
	foreach($getResult as $value){
		$params['is_live'] = '1';
		$conditions['id']=$value['id'];
		update_query("worry_alert",$params,$conditions,$keynames=array());
		$objContent = new Content('worry_alert',$value['id']);	
	    $objContent->setWallOfWorryMeta();
	}
}

?>
