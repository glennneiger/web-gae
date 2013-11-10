<?
global $D_R;
include_once($D_R."/lib/techstrat/_techstratData.php");
include_once($D_R."/lib/techstrat/_techstratDesign.php");
include_once($D_R."/lib/config/_techstrat_config.php");
$objTechStartDesign= new techstartDesign();

function embedTechStartObject($file_path,$width,$height)
{
	global $HTPFX,$HTADMINHOST;
	if($file_path){
		$absolute_file_path = $HTPFX.$HTADMINHOST.$file_path;
	}

	$arPathParts = pathinfo($file_path);
	$file_name = $arPathParts['basename'];
	$file_extension = $arPathParts['extension'];
	if($file_extension == 'pdf')
	{
		$type = "application/pdf";
	}
	else if($file_extension == 'xls')
	{
		$type = "application/vnd.ms-excel";
	}
//	$str ='<iframe src="'.$absolute_file_path.'#toolbar=1&amp;navpanes=0&amp;scrollbar=1" style="width:'.$width.'px; height:'.$height.'px;" frameborder="1"></iframe>';
	$str ='<iframe src="http://docs.google.com/gview?url='.$absolute_file_path.'&embedded=true" style="width:'.$width.'px; height:'.$height.'px;" frameborder="1"></iframe>';
	return $str;
}
	
	$stPageName = $_POST['page_name'];
	if($stPageName == 'techstrat_performance')
	{
		$objTechStartData= new techstartData("techstrat_posts");
		$resultPerformance = $objTechStartData->getPerformance();
        $file_path="";
		if($resultPerformance)
		{
			$file_path  = "/assets/techstrat/pdf_performance/".$resultPerformance;
			$width = $_POST['width'];
			$height = $_POST['height'];
	 		echo embedTechStartObject($file_path,$width,$height);
		}else{
			$str=$objTechStartDesign->displayNoPostText("Positions and Performance");
			echo $str;		
		}
	}
?>