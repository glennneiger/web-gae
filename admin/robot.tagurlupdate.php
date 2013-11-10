<?php
set_time_limit(60*30 );//1 hour
include_once("../lib/_db.php");
include_once("../lib/_content_data_lib.php");
include_once("../lib/layout_functions.php");

	$objcontent=new Content();

	//Query for Empty URLs
	$sqlTagURL="SELECT t.id,t.tag FROM ex_tags t where (t.url is NULL or t.url='') order by t.id asc limit 1000 ";
	$resTagURL=exec_query($sqlTagURL);
	foreach($resTagURL as $value)
	{
				$tagurl=getFilterWords(trim($value['tag']));
				$metaData['url']="/".strtolower($tagurl).'/';
				if($value['id'] && trim($value['tag'])!='' )
				{
					$id=update_query("ex_tags",$metaData,array('id' =>$value['id']));
					echo "<br>".'id------'.$metaData['url'].'---------'.$value['id'];
				}
	}
			
	function getFilterWords($tag){
	    $title="";
		$title=strtolower(str_replace("-"," ",$tag));
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
			
   /*function getFirstFiveWords($body){
        $title="";
		$title=strtolower($body);
		$title=$title." &nbsp; ";
   	 	$pos=0;
		$pos=strpos($title,":");
		if($pos){
			$title=trim(substr($title,$pos + 1));
		}
		preg_match("/^(\S+\s+){0,5}/",trim($title), $matches);
		$matches[0]=mswordReplaceSpecialChars($matches[0]);
		$matches[0]=clean_title_url($matches[0]);
		$matches[0]=str_replace(" ","-",trim($matches[0]));
		return trim($matches[0]);
   }*/
   
   function clean_title_url($text)
	{
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
	$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	$text = str_replace("-", " ", $text);
	return $text;
	}


?>