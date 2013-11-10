<?php
set_time_limit(60*30 );//1 hour
include_once("../lib/_db.php");
include_once("../lib/_content_data_lib.php");
include_once("../lib/layout_functions.php");

	$objcontent=new Content();
	
	//Query for Empty URLs
	//$sqlSourceURL="SELECT id,source,source_link FROM daily_feed d where d.is_deleted='0' and  d.is_approved='1' and (source!='' or source is not NULL) order by d.id asc";
	$sqlSourceURL="SELECT id,source,source_link FROM daily_feed d where (source!='' or source is not NULL) order by d.id asc";
	$resSourceURL=exec_query($sqlSourceURL);
	if($resSourceURL && count($resSourceURL)>0)
	{
	foreach($resSourceURL as $value)
	{
				$resourceurl=getFilterWords(trim($value['source']));
			 	$metaData['url']="/".strtolower($resourceurl).'/';
				$metaData['source']=addslashes(mswordReplaceSpecialChars(stripslashes($value['source'])));
				$metaData['source_link']=trim($value['source_link']);
				$metaData['item_id']=trim($value['id']);
				$metaData['item_type']='18';
				
				if($value['id'] && trim($value['source']))
				{
					$id=insert_query("ex_source",$metaData,array('id' =>$value['id'],'item_type'=>'18'));
					echo "<br>".'id------'.$metaData['url'].'---------'.$value['source'];
				}
	}
	}
	else
	{
		echo "No Record Found!";
	}
   function getFilterWords($source){
  	    $title="";
		$title=strtolower(str_replace("-"," ",$source));
   	 	$pos=0;
		$pos=strpos($title,":");
		if($pos){
			$title=trim(substr($title,$pos + 1));
		}
		//$title=$title." &nbsp; ";
		/*preg_match("/^(\S+\s+){0,1000}/",trim($title), $matches);
		$matches[0]=str_replace(" ","-",trim($matches[0]));
		$matches[0]=mswordReplaceSpecialChars($matches[0]);
		$matches[0]=$this->clean_title_url($matches[0]);
		return trim($matches[0]);*/
			
		$title=mswordReplaceSpecialChars($title);
		$title=clean_title_url($title);
		$title=str_replace(" ","-",trim($title));
		return trim($title);
   }
   
    function clean_title_url($text)
	{
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
	$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	$text = str_replace("-", " ", $text);
	return $text;
	}


?>