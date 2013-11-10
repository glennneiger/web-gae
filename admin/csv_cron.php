<?php
global $D_R;
$filePath=$D_R."/subs-cust-order.csv";
$row = 1;
$i=1;

if (($handle = fopen($filePath, "r")) !== FALSE) {
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	if($i==1)
	{
		foreach($data as $k=>$v)
		{
			$product_columns[]=$v;
		}
	}
	if($i>1)
	{
		$product_data = array();
		for($a=0;$a<18;$a++)
		{
			$product_data[$product_columns[$a]]=$data[$a];
		}
		insert_query("subscription_cust_order",$product_data);
	}
$i++;
}
fclose($handle);
}

?>