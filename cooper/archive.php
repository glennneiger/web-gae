<?php 
global $D_R,$HTPFX,$HTHOST;
require_once($D_R."/lib/cooper/_cooper_data_lib.php");
$objCooperData = new cooperData('cp_articles');
if(!empty($_POST)){
	$month=$_POST['month'];
	$year=$_POST['year'];
	$getMonthlyPost = $objCooperData->getCooperPostByMonthCount($month,$year);
	$allPostCount=0;
	$str='';
	foreach($getMonthlyPost as $k=>$v){
		$allPostCount=$allPostCount+$v['alertCount'];
	}
	foreach($getMonthlyPost as $k=>$v){
		$str.='<li><a href="'.$HTPFX.$HTHOST.'/cooper/'.$v['category_alias'].'/yr/'.$year.'/mo/'.$month.'">'.$v['category_name'].'('.$v['alertCount'].')</a></li>';
	}
	
	echo $str;
}


?>