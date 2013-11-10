<?
global $D_R;
set_time_limit(60*30 );//1 hour
ini_set(“memory_limit”,”128M”);

$sql="SELECT item_id,url FROM ex_item_meta WHERE is_live='1' AND item_type='1'";
$res=exec_query($sql);
foreach($res as $val){
	//add Article URl in content_seo_url table
	insert_query("content_seo_url",array("item_id"=>$val['item_id'],"item_type"=>"1","url"=>$val['url'],"time"=>mysqlNow()));
}
?>