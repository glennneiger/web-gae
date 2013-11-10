<?php
global $D_R;
include_once($D_R."/admin/lib/_admin_data_lib.php");

$str=strip_tags($_REQUEST['str']);
if($str)
{
	$ticker= new Ticker();
	$data=$ticker->getTickerFromBody($str);

	if(count($data))
	{
		$arr=array();
		foreach($data as $key=>$val)
		{
			if(!in_array($val['stocksymbol'], $arr))
			{
				$imp.=$val['stocksymbol'].",";
				$arr[]=$val['stocksymbol'];
			}
		}
	}

	echo rtrim($imp,",");
}
?>