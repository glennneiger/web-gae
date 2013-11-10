<?php
global $D_R, $IMG_SERVER;
require_once($D_R."/lib/edu/_edu_data_lib.php");
$objEduData = new eduData('edu_alerts');
if(!empty($_POST)){
	$searchText = $_POST['searchText'];
	$glossSearch = $objEduData->getEduSearchGlossary($searchText);
	$arrFirstLetter = array();
	$arrFirstLetterContent = array();
	$str = '';
	$i=0;
	if(!empty($glossSearch)){
		foreach($glossSearch as $key=>$val){
			$firstLetter = strtoupper($val['name']['0']);
			if(!in_array($firstLetter, $arrFirstLetter)){
				$arrFirstLetter[$i]=$firstLetter;
			}
			$i++;
		}
		$str = '<div class="eduGlossSearchProg">&nbsp;</div><div class="eduGlossAlphabetsBar">
			<div class="eduGlossAlphabets" id="eduGlossAlphabets">';
				foreach ($arrFirstLetter as $key=>$val){ 
					$divId="alpha_".$val;
					if($key=="0"){ 
						$str .= '<div class="eduGlossAlphabetsIcon eduGlossAlphabetsIconActive" id="'.$divId.'"><a href="#'.$val.'" onClick="showActiveAlpha(\''.$divId.'\');">'.$val.'</a></div>';
					 }else{ 
						$str .= '<div class="eduGlossAlphabetsIcon" id="'.$divId.'"><a href="#'.$val.'" onClick="showActiveAlpha(\''.$divId.'\');">'.$val.'</a></div>';
					 } 
				 } 
			$str .= '</div>
		</div><div id="eduGlossContentContainer">';
			
		foreach ($glossSearch as $key=>$val) { 
			$firstLetter = strtoupper($val['name']['0']);
			if(!in_array($firstLetter, $arrFirstLetterContent)){
				$arrFirstLetterContent[]=$firstLetter;
				$str .= '<a id='.$firstLetter.'></a>';
			}
			$str .= '<div class="eduGlossContent">
						<div class="eduGlossTerm">'.ucwords($val['name']).'</div>
						<div class="eduGlossDef">'.ucfirst($val['value']).'</div>
					</div>';
		}
		$str.='</div>';
	}
	echo $str;
}
?>