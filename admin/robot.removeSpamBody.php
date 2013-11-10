<? 
global $D_R;
include_once("$DOCUMENT_ROOT/lib/_includes.php");
include_once("$D_R/lib/_db.php");

$qry = 'SELECT * FROM articles WHERE body LIKE "%213.182.197.229%" OR title LIKE "%213.182.197.229%"';
$result = exec_query($qry);

foreach($result as $key=>$value){
	preg_match("|(<iframe [^>]*(src='[^']+')[^>]>*)|", $value['body'], $body ,PREG_OFFSET_CAPTURE);
	$endPosBody = $body[0][1];
	if($endPosBody>0){
		$value['body'] = substr($value['body'],0,$endPosBody);
	}
	
	preg_match("|(<iframe [^>]*(src='[^']+')[^>]>*)|", $value['title'], $title ,PREG_OFFSET_CAPTURE);
	$endPosTitle = $title[0][1];
	if ($endPosTitle>0){
		$value['title'] = substr($value['title'],0,$endPosTitle);
	}
	

	//$value['body'] = str_replace($spamText,"", $value['body']);
	//$value['title'] = str_replace($spamText,"", $value['title']);
	
	$value['body']=addslashes(mswordReplaceSpecialChars(stripslashes($value['body'])));	
	$value['title']=addslashes(mswordReplaceSpecialChars(stripslashes($value['title'])));
	
	$params['body']=$value['body'];
	$params['title']=$value['title'];
	$conditions['id']=$value['id'];

	$id=update_query('articles',$params,$conditions,$keynames=array());

	echo "<br>";
	echo "<br>".'id-- '.$value['id'];
	echo "<br>";
}

?>