<?php
set_time_limit(60*30 );//1 hour
include_once("../lib/_db.php");
include_once("../admin/lib/_dailyfeed_data_lib.php");
include_once("../lib/layout_functions.php");
include_once("../lib/_content_data_lib.php");
$objContent = new Content();
$qry="select id,title from daily_feed order by id asc";
$result=exec_query($qry);
if($result){
  
  foreach($result as $row){
  	$pos=0;
	$quicktitle="";
	$title="";
	$pos=strpos($row['title'],":");
	if($pos){
	        $quicktitle=trim(substr($row['title'],0,$pos));
			$title=trim(substr($row['title'],$pos + 1));
			
		if($quicktitle){
			$params['item_id']=$row['id'];
			
			$params['quick_title']=addslashes(mswordReplaceSpecialChars(stripslashes($quicktitle)));
			$params['item_type']='18';
			$quicktitle=$objContent->getFirstFiveWords($quicktitle);
			$params['url']="/dailyfeed/category/".$quicktitle.'/';
			$condition=array("item_id"=>$row['id'],item_type=>'18');
			$id=insert_or_update('ex_quick_title',$params,$condition);
			if($title){
				$dailyfeed['title']=addslashes(mswordReplaceSpecialChars(stripslashes($title)));;
				update_query("daily_feed",$dailyfeed,array(id=>$row['id']));				
			}
			
		}
	}
	echo "<br>".$row['id'].'-----quicktitle-'. $quicktitle.'    ----- title'.$title;
	
	}
	
  
  }
		
?>