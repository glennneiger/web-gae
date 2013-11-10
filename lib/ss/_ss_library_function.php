<?php
function MonthDays($someMonth, $someYear)
{
	return date("t", strtotime($someYear . "-" . $someMonth . "-01"));
}

function getThirdFriday($someMonth,$someYear)
{
	$j=1;
	$flag1=true;
	$first;
	for($i=1;$i<=MonthDays($someMonth,$someYear);$i++)
	{
		$resCheckDate=time(0, 0, 0, $someMonth , $i,$someYear);
		if(date("w",$resCheckDate)==5 and $flag1==true)
		{
			$first=$j;
			$flag1=false;
		}
		if(date("w",$resCheckDate)==5 and $j==($first+14))
		{
			$value=date("Y-m-d",$resCheckDate);
		}
		$j++;
	}
	return $value;
}
?>