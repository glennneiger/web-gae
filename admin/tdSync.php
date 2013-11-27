<?php
global $D_R;
include_once("$D_R/lib/json.php");
$json = new Services_JSON();

$type=$_GET['type'];
$partner = $_GET['partner'];

if($partner=="buzzMinyan761")
{
	switch($type)
	{
		case "contributor":
			$buzz_id = $_GET['buzz_id'];
			$sql="select bb.id,cn.name from buzzbanter
			   AS bb, contributors AS cn where bb.contrib_id = cn.id AND bb.id ='".$buzz_id."' limit 1";
			$result = exec_query($sql);
			$data['data'] = $result;
		break;
		case "charts":
			$buzz_id = $_GET['buzz_id'];
			$sql="select * from item_charts where item_id='".$buzz_id."' and item_type='2'";
			$result = exec_query($sql);
			$data['data'] = $result;
		break;
		case "buzzData":
			$td_buzz_id = $_GET['td_buzz_id'];
		 	$sql="SELECT id,date,updated,is_live,show_in_app,show_on_web,author,login,image,position,body,approved,
			title,contrib_id,publish_date,section_id FROM buzzbanter where id > $td_buzz_id and is_live='1' 
			and approved='1' ";
			$result = exec_query($sql);
			$data['data'] = $result;
		break;
		case "buzzTemp":
			$td_buzz_id = $_GET['td_buzz_id'];
			$td_buzz = $_GET['td_buzz'];
		 	$sql="SELECT id,date,updated,is_live,show_in_app,show_on_web,author,login,image,position,body,approved,title,
			contrib_id,publish_date,section_id FROM buzzbanter where id <= $td_buzz_id and updated >'$td_buzz' and is_live='1'
			 and approved='1' ";
			$result = exec_query($sql);
			$data['data'] = $result;
		break;
		default:
			$result="";
	}
	$output = $json->encode($data);
	echo $output;
}


?>