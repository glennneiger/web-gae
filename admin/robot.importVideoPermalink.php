<?php
include_once("../lib/_db.php");
global $D_R;
	$filename = fopen("$D_R/assets/data/minyanville_ingest_file_w_permalinks.csv", "r");
    if($filename)
     {
     	while (($data = fgetcsv($filename, 1000, ",")) !== FALSE)
         {
		 	$sqlcategory 		= "select section_id as 'section_id' from section where name='".$data[7]."'";
			$resultcategory			= exec_query($sqlcategory,1);
			if($resultcategory && count($resultcategory)>0)
			{
				$category	=	$resultcategory['section_id'];
			}
			if($category)
			{
	            $params['permalink']=$data[10];
				$conditions['videofile']=addslashes($data[6]);
				$conditions['cat_id']=$category;
			    $id=update_query('mvtv',$params,$conditions,$keynames=array());
			}
         }
          fclose($filename);
          echo "Successfully Imported";
     }
     else
     {
         echo "Invalid File";
     }    
fclose($filename);
?>