<?php
global $IMG_SERVER,$D_R;

$date = date('mdy');
$pathTypeArr = array("dailyfeed"=>"/assets/dailyfeed/uploadimage/".$date."/","buzz_charts_original"=>"/assets/buzzbanter/charts/original/".$date."/","buzz_charts_thumbnail"=>"/assets/buzzbanter/charts/thumbnail/".$date."/");

foreach($pathTypeArr as $key=>$val)
{
	$pathname=$D_R.$val;
	$mode="775";
	$createfolder=mkdir_recursive($pathname,$mode);
	chmod($pathname, 0777);
}
function mkdir_recursive($pathname,$mode)
{
	is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
	return is_dir($pathname) || @mkdir($pathname, $mode);
}

?>