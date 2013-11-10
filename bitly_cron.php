<?php
ini_set('memory_limit', '64M');
global $IMG_SERVER,$HTPFX,$HTHOST,$D_R;
include_once($D_R.'/lib/_bitly_lib.php');

echo $qry="SELECT id,url FROM `ex_item_meta` WHERE (`item_type`='1' OR `item_type`='18')
AND (bitly_url IS NULL OR bitly_url='') AND is_live='1'";

$db=new dbObj($qry);
while($row=$db->getRow()){

	if ($row['url']!="")
	{
	 	echo $url = $HTPFX.$HTHOST.$row['url'];
		$objbitly = new bitly();
		$bitlyurl=$objbitly->shorten($url);
		echo "----";
		echo $articles['bitly_url']=$bitlyurl;
		echo "<br>";
		update_query("ex_item_meta",$articles,array(id=>$row[id]));
	}
}
echo "Url conversion is completed";

?>