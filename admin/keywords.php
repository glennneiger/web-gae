<?php
// Load the AlchemyAPI module code.
//include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
include "lib/AlchemyAPI_CURL.php";
include "lib/AlchemyAPIParams.php";



// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("../lib/config/api_key.php");


// Extract topic keywords from a web URL.
//http://www.techcrunch.com/
//$result = $alchemyObj->URLGetRankedKeywords("http://dev.minyanville.com/dailyfeed/");
//echo "$result<br/><br/>\n";


$str=strip_tags($_REQUEST['str']);

if($str=='')
	$str="Test";

$result = $alchemyObj->TextGetRankedKeywords($str);

function xml2assoc($xml, $name)
{
//    print "<ul>";

    $tree = null;
  //  print("I'm inside " . $name . "<br>");

    while($xml->read())
    {
        if($xml->nodeType == XMLReader::END_ELEMENT)
        {
        //    print "</ul>";
            return $tree;
        }

        else if($xml->nodeType == XMLReader::ELEMENT)
        {
            $node = array();

        //    print("Adding " . $xml->name ."<br>");

			$node['tag'] = $xml->name;

            if($xml->hasAttributes)
            {
                $attributes = array();
                while($xml->moveToNextAttribute())
                {
               //     print("Adding attr " . $xml->name ." = " . $xml->value . "<br>");
                    $attributes[$xml->name] = $xml->value;
                }
                $node['attr'] = $attributes;
            }

            if(!$xml->isEmptyElement)
            {
                $childs = xml2assoc($xml, $node['tag']);
                $node['childs'] = $childs;
            }

          //  print($node['tag'] . " added <br>");
            $tree[] = $node;
        }

        else if($xml->nodeType == XMLReader::TEXT)
        {
            $node = array();
            $node['text'] = $xml->value;
            $tree[] = $node;
         //   print "text added = " . $node['text'] . "<br>";
        }
    }

   // print "returning " . count($tree) . " childs<br>";
   // print "</ul>";

    return $tree;
}


$xml = new XMLReader();
$xml->xml($result);


$assoc = xml2assoc($xml, "root");
$xml->close();


$str='';
$prev='';
$flag=0;
function callBack($arr,$str,$prev)
{
	global $flag,$str,$prev;
	$prevFlag=0;
	if(sizeof($arr)>0 && is_array($arr))
	{
		foreach($arr as $key=>$val)
		{
			if(!is_array($val))
			{
				if($val=='keywords')
					$flag=1;

				if($key=='text' && $flag==1)
				{
					if(!is_numeric($val))
					{
						$prev=$val;
					}
					else
					{
						if($val>0.50)
							$prevFlag=1;
					}

					if($prevFlag==1 && $prev!='')
					{
						$str=$str.$prev.",";
					}

					return $str;
					die;

				}
			}
			callBack($val,$str,$prev);
		}
	}
}

callBack($assoc,$str,$prev);

echo  rtrim($str,",");

// Load a HTML document to analyze.
//$htmlFile = file_get_contents("data/mine.html");


// Extract topic keywords from a HTML document.
//$result = $alchemyObj->HTMLGetRankedKeywords($htmlFile, "data/mine.html","json");
//echo "$result<br/><br/>\n";


?>
