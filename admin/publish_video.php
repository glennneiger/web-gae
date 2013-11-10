<?
global $D_R;
include_once("$D_R/lib/_content_data_lib.php");
echo "Publishing Video started at : ".date("Y-m-d h:i")."<BR>\n";

$qry="SELECT id FROM mvtv WHERE approved = '1' and is_live = '0' AND publish_time <= '".mysqlNow()."' ORDER BY id ";
$arVideoResult = exec_query($qry);

foreach($arVideoResult as $arVideo)
{
	$arData['is_live'] = 1;
	$item_id = $arVideo['id'];
	update_query(mvtv,$arData,array(id=>$item_id));
	$obContent = new Content(11,$item_id);
	$obContent->setVideoMeta();
	echo "Video with id =".$item_id." is published";
}
?>