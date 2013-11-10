<?
session_start();
global $_SESSION;
include_once("$D_R/lib/_includes.php");
$id=$_GET[videoid];
$referer_url=parse_url($_SESSION['referer']);
list($subdomain, $domain, $domaintype) = explode(".", $referer_url['host']);
if($domain=="ameritrade"){
	$permission=0;
}
else{
	$permission=1;
}
$strQuery="select concat('title',id,'=',title) title, concat('videoURL',id,'=',videofile) video, concat('still',id,'=',stillfile) still from mvtv where id=$id";


$strResult=exec_query($strQuery,1);

$content="";

if($strResult && count($strResult)>0){
	$index=0;

	$content.="&$strResult[title]&$strResult[video]&$strResult[still]&permission=$permission";



}
else{
	$content.="title$id=&videoURL$id=&still$id=";
}
echo $content;
?>
