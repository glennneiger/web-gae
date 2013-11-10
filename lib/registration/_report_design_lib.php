<?php
global $D_R;
class reportDesign{
	function showDateDropDown(){
		$dat = array("","01","02","03","04","05","06","07","08","09");
		for($date=1;$date<=31;$date++){
			if($date>9){ ?>
				<option value="<?=$date;?>"><?=$date;?></option>
		<? 	} else { ?>
				<option value="<?=$dat[$date];?>"><?=$date;?></option>
		<? 	}
		}
	}

	function showMonthDropDown(){
		$month_val = array("","01","02","03","04","05","06","07","08","09","10","11","12");
		$month_names = array("","January","February","March","April","May","June","July","August","September","October","November","December");
		for($mnth=1;$mnth<=12;$mnth++){ ?>
			<option value="<?=$month_val[$mnth];?>"><?=$month_names[$mnth];?></option>
	<? }
	}

	function showYearDropDown(){
		$currentYear = date("Y");
		for ($j=2009; $j<=$currentYear; $j++){ ?>
			<option value="<?=$j;?>"><?=$j;?></option>
	<? }
	}
	
	function showYearDropDownMarketingFashboard(){
		$currentYear = date("Y");
		for ($j=2012; $j<=$currentYear; $j++){ ?>
			<option value="<?=$j;?>"><?=$j;?></option>
	<? }
	}

	function filterOut($arr,$key1,$value1,$key2,$value2){
		if($arr[$key1]==$value1){
			if($key2!=''){
				if($arr[$key2]==$value2){
					$res = 0;
				}
			}else{
				$res = 0;
			}
		}else {
			$res = 1;
		}
		return $res;
	}
}	//class end
?>