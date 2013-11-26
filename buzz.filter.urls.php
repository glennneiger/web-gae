<?php
include_once("../lib/_includes.php");
set_time_limit ( 60 *30);//1/2 hour
include_once("../lib/json.php");
$json = new Services_JSON();
$readfile = file("../data/urls_for_td.csv");


function objectToArray($d) {
	  if (is_object($d)) {
		  // Gets the properties of the given object
		  // with get_object_vars function
		  $d = get_object_vars($d);
	  }

	  if (is_array($d)) {
		  /*
		  * Return array converted to object
		  * Using __FUNCTION__ (Magic constant)
		  * for recursive call
		  */
		  return array_map(__FUNCTION__, $d);
	  }
	  else {
		  // Return array
		  return $d;
	  }
  }
  
  function refineData($data)
  {
	  $data = str_replace("\n","(nn)",$data);
	  $data = str_replace("\t","(tt)",$data);
	  $data = str_replace("\r","(rr)",$data);
	  $data = stripslashes($data);
	  $data = str_replace("(nn)","\n",$data);
	  $data = str_replace("(tt)","\t",$data);
	  $data = str_replace("(rr)","\r",$data);
	  return $data;
  }
function fill_rows($row_buzz,$ins_upd)
{
          $row_buzz_today = $row_buzz;

		  // Insert contributor name in buzzbanter_today table
		  if($row_buzz['author'] == "") // If Post is not an article as in case of atricle author name is already their
		  {
			  /* $row_contributor_name=exec_query_mvmaster("select bb.id,cn.name from buzzbanter
			   AS bb, contributors AS cn where bb.contrib_id = cn.id AND bb.id ='$row_buzz[id]'",1);*/
			  $row_contributor_name= file_get_contents("http://www.minyanville.com/admin/tdSync.php?partner=buzzMinyan761&type=contributor&buzz_id=".$row_buzz[id]);
			  $row_contributor_name =json_decode(refineData($row_contributor_name));
			  $row_contributor_name = objectToArray($row_contributor_name->data);
			  $row_buzz_today['client_id'] = '0'; // 0 is the client id for minyanville.
			  $row_buzz_today['author'] = $row_contributor_name['name'];
		  }
		  // Data for search table item_post_data
			$arItemPostData['item_type'] = 'mvbuzz';
		  	$arItemPostData['item_id'] = $row_buzz[id];
			$arItemPostData['author_id'] = $row_buzz['contrib_id'];
			$arItemPostData['author'] = $row_buzz_today['author'];
			$arItemPostData['title'] = $row_buzz['title'];
			$arItemPostData['body'] = $row_buzz['body'];
			$arItemPostData['content'] = $row_buzz['body']." ".$row_buzz['title']." ".$row_buzz_today['author'];
			$arItemPostData['created_on'] = $row_buzz['date'];
			$arItemPostData['is_live'] = $row_buzz['is_live'];
			$arItemPostData['approved'] = $row_buzz['approved'];
			$arItemPostData['content']=strip_tags($arItemPostData['content']);

	 	  insert_or_update("buzzbanter_td", $row_buzz,array("id"=>$row_buzz[id]));
		  insert_or_update("buzzbanter_today", $row_buzz_today,array("id"=>$row_buzz[id],client_id=>0));
		  insert_or_update("item_post_data", $arItemPostData,array("item_id"=>$row_buzz[id],item_type=>'mvbuzz'));
		  syncBuzzCharts($row_buzz['id']);
}

function has_whitelist($url,$readfile,$field)
{
	for ($k=0; $k<=count($readfile)-1; $k++)
	 {
	 	$field[] = split(",",$readfile[$k]);
	 }

	 for ($i=1; $i<count($field[0]);$i++)
	  {
		 $url_exists=substr_count(trim($url),trim($field[0][$i]));

		 if ($url_exists>0)
		 	return $url_exists=1;
		 else
		    $url_exists=0;
	  }
}

function change_minyanville_to_https($url)
{

 $url_minyanville=substr_count(trim($url),"http://image.minyanville.com");

 if($url_minyanville)
 {
   $https_url=str_replace("http://image.minyanville.com","https://image.minyanville.com",$url);
  }

  return $https_url;
}

function change_minyanvillebookmark_to_tdbookmark($url)
{
	$url_minyanville=substr_count(trim($url),"http://www.minyanville.com/buzz/bookmark.php");

	if($url_minyanville)
	{
		$td_url=str_replace("http://www.minyanville.com/buzz/bookmark.php","https://minyanville.ameritrade.com/buzz/bookmark.php",$url);
	}
	return $td_url;
}

function change_anchor($body)
{		
	$url_patteren='/(<a.*?href=[""\'](.*)[""\'].*>(.*?)<\/a>)/';
	preg_match_all($url_patteren, $body, $matches);

	$startpos = strpos($matches[0][0], "https://www.minyanville.com");
	$passVar=substr($matches[0][0], $startpos-1,1);
	if(!$startpos)
	{

		$startpos = strpos($matches[0][0], "www.minyanville.com");
		$passVar=substr($matches[0][0], $startpos-8,1);
	}
	if(!$startpos)
	{
		$startpos = strpos($matches[0][0], "http://minyanville.com");
		$passVar=substr($matches[0][0], $startpos-1,1);
	}
	if(!$startpos)
		return $body;

	$endpos=strpos(substr($matches[0][0], $startpos, strlen($matches[0][0])-$strpos),$passVar);
	if(!$endpos)
			$endpos=strpos(substr($matches[0][0], $startpos, strlen($matches[0][0])-$strpos),'\'');
	$rest = substr($matches[0][0], $startpos, $endpos);
	$str_hash_pos=strpos($rest,'#');
	if($str_hash_pos)
		$body=str_replace($rest, substr($rest,0,$str_hash_pos)."?from=ameritrade&camp=syndication&medium=portals".substr($rest,$str_hash_pos,strlen($rest)-$str_hash_pos), $body);
	elseif(strpos($rest,'?'))
		$body=str_replace($rest, $rest."&from=ameritrade&camp=syndication&medium=portals", $body);
	elseif(strpos($rest,'/articles/') and strpos($rest,'/id/'))
		$body=str_replace($rest, $rest."?from=ameritrade&camp=syndication&medium=portals", $body);
	else
		$body=str_replace($rest, $rest."?from=ameritrade&camp=syndication&medium=portals", $body);
	return $body;
}

/*
This function remaps the URL's of contributors private sites to their Bio pages
*/

function isprofessors_url($url,$field)
{
	global $TD;
	foreach ($TD['contributor'] as $index=>$value)
	{
		$url_exists=substr_count(trim($url),trim($value));
		if ($url_exists==1)
		{
			$qry="select c.id from admin_users au,contributors c where au.id=c.user_id and username='".$index."'";
			$result=exec_query($qry);
			if($result)
				return $result[0]['id'];
			else
				return 1;
		}
	}
	return NULL;

}

function change_mvil_link($anchor,$body)
{
	$updated_anchor=change_anchor($anchor);
	return str_replace($anchor,$updated_anchor,$body);
}

/*
This function filters the url's of the system
*/
function filter_urls($row_buzz,$ins_upd)
{
	global $TD,$readfile;
	$tag_start='<a';
	$tag_close='</a>';
	$tag_start_end='>';
	$length_end_tag=strlen($tag_close);

	$offsetposition=0;
	$offset_quotes=0;
	$counter=0;
	 //array initialized for file
	$field=array();
	 // CSV white listed file is read

	

	 $https_mvil_url=change_minyanville_to_https($row_buzz[body]);

			       if($https_mvil_url)
				    {
					  $row_buzz[body]=$https_mvil_url;//http image.minyanville changed to https image.minyanville
					}
	 $mvilBookmark=change_minyanvillebookmark_to_tdbookmark($row_buzz[body]);
	 if($mvilBookmark)
	 {
	 	$row_buzz[body]=$mvilBookmark;//Minyavnille Bookmark changed to Td bookmarks
	 }
	 $replaced_body=$row_buzz[body];
	 $body_in_small=strtolower($row_buzz[body]);

     //finds the  occurence of href tag in the body
     $no_of_links=substr_count($body_in_small, $tag_start);

	 if ($no_of_links>0)
	  {

	   while($counter<$no_of_links)
		{
			  // finds the posistion of each link in the body
			  $tag_start_pos=strpos($body_in_small,$tag_start,$offsetposition);

			  // finds the position of lenght of href tag in the body
			  $tag_close_pos=strpos($body_in_small,$tag_close,$offsetposition);
			 // echo "<br>tag_close_pos=".$tag_close_pos."<br>";

			  // extract the text from url
			  $body = substr($row_buzz[body],$tag_start_pos,$tag_close_pos - $tag_start_pos + $length_end_tag);
			  $bios_id=isprofessors_url($body,$field);

			 if ($bios_id !=NULL)
			 {
				   	// extract the text between the links for eg <'a href'>
				   	// this text to be extracted<'/a'>

					  $tag_start_end_pos=strpos($body,$tag_start_end,0);

					  $body_small=strtolower($body);
					 // $tag_close_pos=strpos($body_small,$tag_close,0);
					  $extracted_body=substr($body,$tag_start_end_pos + 1,$tag_close_pos - $tag_start_end_pos - 1);

					// Replacement body for exsisting URL's
					  $replacement_body="<a href='".$TD['BIOS_URL']."?bio=".$bios_id."'>".$extracted_body."</a>";


					 // replaces the current URL to their Bio page*/
				  $replaced_body= str_replace($body,$replacement_body,$replaced_body);


			 }
			 else
			 {

				 $matched=has_whitelist($body,$readfile,$field);
				 if ($matched !=1)
				 {

			       // extract the text between the links for eg <'a href'>
			       // this text to be extracted<'/a'>

					  $tag_start_end_pos=strpos($body,$tag_start_end,0);

					  $body_small=strtolower($body);
					//  $tag_close_pos=strpos($body_small,$tag_close,0);

					  $extracted_body=substr($body,$tag_start_end_pos + 1,$tag_close_pos - $tag_start_end_pos - 1);

				   // replaces the body  from <a till </a> tag with only text*/
				      $replaced_body= str_replace($body,$extracted_body,$replaced_body);
				 }
				 $replaced_body=change_mvil_link($body,$replaced_body);
			}
			  $offsetposition=$tag_close_pos+1;
			 // echo "<br>offsetposition=".$offsetposition."<br>";
			  $counter++;
		  }
         $row_buzz[body]=$replaced_body;

          //inserts or updates in table buzzbanter in TD
          fill_rows($row_buzz,$ins_upd);
	  }
	  else
	   {
          fill_rows($row_buzz,$ins_upd);

	   }
	   //end of db getRow
}       // end of function

function syncBuzzCharts($buzzID){
	/*$sqlGetCharts="select * from item_charts where item_id='".$buzzID."' and item_type='2' ";
	$resGetCharts=exec_query_mvmaster($sqlGetCharts); */
	$resGetCharts= file_get_contents("http://www.minyanville.com/admin/tdSync.php?partner=buzzMinyan761&type=charts&buzz_id=".$buzzID);
	  $resGetCharts = json_decode(refineData($resGetCharts));
	  $resGetCharts = objectToArray($resGetCharts->data);
	foreach ($resGetCharts as $value){
		insert_or_update("item_charts", $value,array("id"=>$value['id']));
	}
}



$change=0;
	/*** latest post in TD  whether it exists in mvil***/
$row_buzz=exec_query("SELECT max(id) as latest_post_id,max(updated) as latest_post FROM buzzbanter_td",1);
$td_buzz=$row_buzz[latest_post];
echo $td_buzz_id=$row_buzz[latest_post_id];
if($td_buzz_id==NULL)
	$td_buzz_id=0;
/** to insert the records  whose ID is greater than the max id in minyaniville*/
/*$row_new=exec_query_mvmaster("SELECT id,date,updated,is_live,show_in_app,show_on_web,author,login,image,position,body,approved,title,contrib_id
,publish_date,section_id FROM buzzbanter where id > '$td_buzz_id' and is_live='1'"); */
$row_new= file_get_contents("http://www.minyanville.com/admin/tdSync.php?partner=buzzMinyan761&type=buzzData&td_buzz_id=".$td_buzz_id);
$row_new = json_decode(refineData($row_new));
htmprint_r($row_new);
$row_new = objectToArray($row_new->data);
die;
foreach($row_new as $key=>$value)
{
     filter_urls($value,true);
	 $change=1;
}
/* $row_new_tmp=exec_query_mvmaster("SELECT id,date,updated,is_live,show_in_app,show_on_web,author,login,image,position,body,
  approved,title,contrib_id,publish_date,section_id FROM buzzbanter where id <= '$td_buzz_id' and updated >'$td_buzz' and is_live='1'");  */
$row_new_tmp= file_get_contents("http://www.minyanville.com/admin/tdSync.php?partner=buzzMinyan761&type=buzzTemp&td_buzz_id=".$td_buzz_id."&td_buzz=".$td_buzz);
$row_new_tmp = json_decode(refineData($row_new_tmp));
$row_new_tmp = objectToArray($row_new_tmp->data);
foreach($row_new_tmp as $row_new)
{
	filter_urls($row_new,false);
	$change=1;
}
if($change)
{
	writeLatestBanterPostJSON(0);
}
?>