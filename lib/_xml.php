<?

class media {
	function media ($mediaArr) {
		if(count($mediaArr)){
	        foreach ($mediaArr as $k=>$v){
	            $this->$k = $mediaArr[$k];
			}
	    }
	}
}

function assocTags($tag_values,$push_arr=0) {
    for ($i=0; $i < count($tag_values); $i++){
		$tag = $tag_values[$i]["tag"];
		$val = $tag_values[$i]["value"];
		if($push_arr && $push_arr==$tag){
	        $tag_assoc[$tag][] = $val;
		}else{
			$tag_assoc[$tag] = $val;
		}
	}
    return new media($tag_assoc);
}

function read_db($filename,$group_name=0,$return_one=0) {
	if(stristr($filename,"<xml>")){
		$data=$filename;
	}else{
	    $data = implode("",file($filename));
	}

    $parser = xml_parser_create();
    xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
    xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
    xml_parse_into_struct($parser,
	 $data, 
	$values, 
	$tags);
	xml_parser_free($parser);
    # loop through the structures
    foreach ($tags as $key=>$val) {

        if ($key == "sub") {
            $ranges = $val;
            # each contiguous pair of array entries are the 
            # lower and upper range for each sub definition
			# lower & upper ranges represent nested tabs within 'sub'
            for ($i=0; $i < count($ranges); $i+=2) {
				$offset = $ranges[$i] + 1;
				$len = $ranges[$i + 1] - $offset;
				# slice out nested tags.
				$subcontents[] = assocTags(array_slice($values, $offset, $len),$group_name);
            }
        } else {
            continue;
        }
    }
    return $return_one?$subcontents[0]:$subcontents;
}


function save_xml($xml_obj,$filename,$return_str=0){
	global $DOCUMENT_ROOT;
	$contents = "";
	if(is_array($xml_obj)){
		foreach($xml_obj as $obj){
			$contents.="\t<sub>\n";
			foreach($obj as $tag=>$value){
				if(is_array($value)){
					foreach($value as $v){
						$contents.="\t\t<$tag>$v</$tag>\n";
					}
				}else{
					$contents.="\t\t<${tag}>$value</${tag}>\n";
				}
			}
			$contents.="\t</sub>\n";
		}
	}
	$contents="<xml>\n${contents}</xml>";
	if($return_str){
		return $contents;
	}else{
		include_once("$DOCUMENT_ROOT/lib/_misc.php");
		write_file($filename,$contents);
	}
}

function select_obj($sel,$from,$where,$where_eq,$return_array=0){
	/*select_obj("title",$epkobjs,"ticket","media/zips/Contact.zip")
	 like: SELECT title FROM epkobjs WHERE ticket='media/zips/Contact.zip'
	 **add support for OR-like syntax so condition can be a list
	*/
	$ret=array();
	foreach($from as $obj){
		if($obj->$where==$where_eq){
			$tmpret=($sel=="*"?$obj:$obj->$sel);
			if($return_array){
				$ret[]=$tmpret;
			}else{
				return $tmpret;
			}
		}
	}
	return $ret;
}

function del_obj($objs,$where,$where_eq){
	/*deletes an object from an xml array of objs based on 
	  comparison of $where and $where_eq
	  e.g.: del_obj($epkobjs,"ticket","media/zips/Contact.zip")
	  like: DELETE FROM epkobjs WHERE ticket='media/zips/Contact.zip'
	*/
	$ret = array();
	foreach($objs as $obj){
		if($obj->$where==$where_eq)	continue;
		$ret[]=$obj;
	}
	return $ret;
}

function get_values($objs,$keyname){
	/*returns values of keys that match $keyname*/
	$ret = array();
	foreach($objs as $obj){
		$ret[]=$obj->$keyname;
	}
	return $ret;
}

function sort_by_key($xmldb,$keyname){
	$ret = array();
	if(!is_array($xmldb))return $ret;
	
	foreach($xmldb as $obj){
		$ret[$obj->$keyname]=$obj;
	}
	ksort($ret);
	return $ret;
}

function sort_obj_array($a,$b,$desc=1){
	global $s;
	if(!$s)return;
	if($a->$s==$b->$s)return 0;
	if($desc)
		return ($a->$s > $b->$s)?1:-1;
	else
		return ($a->$s < $b->$s)?1:-1;
}

function sort_obj_array_desc($a,$b){
	return sort_obj_array($a,$b);
}
function sort_obj_array_asc($a,$b){
	return sort_obj_array($a,$b,0);
}

function sort_hash_array($a,$b,$desc=1){
	global $s;
	if(!$s)return;
	if($a[$s]==$b[$s])return 0;
	if($desc)
		return ($a[$s] > $b[$s])?1:-1;
	else
		return ($a[$s] < $b[$s])?1:-1;
}

function sort_hash_array_desc($a,$b){
	return sort_hash_array($a,$b);
}
function sort_hash_array_asc($a,$b){
	return sort_hash_array($a,$b,0);
}


include_once("$DOCUMENT_ROOT/lib/xmlize.php");
include_once("$DOCUMENT_ROOT/lib/_gallery.php");
?>