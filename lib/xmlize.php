<?function xml_depth($vals, &$i) { 
	$children = array(); 
	if ($vals[$i]['value']) array_push($children, $vals[$i]['value']); 
	while (++$i < count($vals)) { 
		switch ($vals[$i]['type']) { 
			case 'cdata': 
				array_push($children, $vals[$i]['value']); 
 			break; 
			case 'complete': 
				$tagname = $vals[$i]['tag'];
				$size = sizeof($children["$tagname"]);
				$children[$tagname][$size]["#"] = $vals[$i]['value'];
				if($vals[$i]["attributes"]) {
					$children[$tagname][$size]["@"] 
									= $vals[$i]["attributes"];
				}
			break; 
			case 'open': 
				$tagname = $vals[$i]['tag'];
				$size = sizeof($children["$tagname"]);
				if($vals[$i]["attributes"]) {
					$children["$tagname"][$size]["@"] 
									= $vals[$i]["attributes"];
					$children["$tagname"][$size]["#"] = xml_depth($vals, $i);
				} else {
					$children["$tagname"][$size]["#"] = xml_depth($vals, $i);
				}
			break; 
			case 'close':
				return $children; 
			break;
		} 
	} 
	return $children;
}

function xmlize($data){
	$vals = $index = $array = array();
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $vals, $index);
	xml_parser_free($parser);
	$i = 0; 
	$tagname = $vals[$i]['tag'];
	$array[$tagname]["@"] = $vals[$i]["attributes"];
	$array[$tagname]["#"] = xml_depth($vals, $i);
	return $array;
}

function unxmlize($data){
	$str="";
	if(is_array($data)){
		foreach($data as $k=>$v){
			if(preg_match("/^[a-z_]+/i",$k)){
				foreach($v as $i=>$subtags){
					$attrs="";
					if(is_array($subtags["@"])){
						$attrs=array();
						foreach($subtags["@"] as $at=>$val){
							$val=str_replace("'","&#39;",htmlentities($val));
							$attrs[]="$at=\"$val\"";
						}
						$attrs=" ".implode(" ",$attrs);
					}
					$str.="\n<$k$attrs>".unxmlize($subtags["#"])."</$k>\n";
				}
			}
		}
	}else{
		$str=htmlentities($data);
	}
	return $str;
}

?>