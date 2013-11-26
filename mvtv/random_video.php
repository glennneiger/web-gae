<?
global $bucketName;
$strQuery="select concat('title',id,'=',title) title, concat('videoURL',id,'=',videofile) video, concat('still',id,'=',stillfile) still from mvtv ORDER BY RAND() LIMIT 0,1";

$strResult=exec_query($strQuery,1);

$content="";

if($strResult && count($strResult)>0){
	$index=0;

	$arrPart=explode($bucketName,$strResult[video]);
	$strResult[video]=$arrPart[0]."edge.minyanville.com".$arrPart[1];

	$content.="&$strResult[title]&$strResult[video]&$strResult[still]";

}
else{
	$content.="title$id=&videoURL$id=&still$id=";
}
echo $content;
?>
