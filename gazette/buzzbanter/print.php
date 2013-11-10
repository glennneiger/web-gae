<?


include_once("./_authenticate.htm");
include("../../layout/dbconnect.php");
include("../..//lib/layout_functions.php");
$title= "Article Template";
$pageName = "article_template";
if(!$_GET[date]){
 	$datestr="date_format('".mysqlNow()."','%m/%d/%Y')";
	$datetime=time();
 }else{
 	$datestr="'${_GET[date]}'";
	$datetime=strtotime($_GET[date]);
 }


$qry="SELECT *,date_format(date,'%r') mdate,UNIX_TIMESTAMP(date)udate
	   FROM buzzbanter
	   WHERE date_format(date,'%m/%d/%Y')=$datestr
	   AND approved='1'
	   ORDER BY date DESC";

$has_postings=0;

list($m,$d,$y)=explode("|",date("n|j|Y"));
$archivelink=urlqsa("/gazette/buzzbanter/banter-search.htm?advanced=1&q=",array(),"*");
$printlink=urlqsa($PHP_SELF,array(PHPSESSID=>$PHPSESSID,mvpopup=>1));
//some old images will point to gazette/newsviews redirect this to articles/index.php

?>
<html>
<head>
	<link rel="stylesheet" href="../../css/print.1.2.css">
</head>

<body onLoad="javascript:window.print();">
<table id="fixed-center-area" cellpadding="0" cellspacing="0" border="0" width="631"><tr>
		<td colspan="3"><img src="<?=$pfx?>/images/bnb_header.gif" width="631" alt="" /></td>
		</tr>
		<tr>
		<td class="left-border">&nbsp;</td>
		<td width="617" style="background-color:#fff">
		  <div id="fixed-center-area-main">
		    <div id="fixed-center-area-content">
		    <br>
				<!--<h3> </h3>
			  <p class="double-separator">&nbsp;</p><br />-->


<h4><?=date("F j, Y",$datetime)?></h4>
<br>

<?

foreach(exec_query($qry) as $i=>$row){
	$has_postings=1;
	$has_image=is_file("$D_R${row[image]}");
	$has_author=$row[author];
?>

	<a class="anchor" name="banter<?= $row[id]; ?>">
			 <h2 class="bar"><?=date("g:i a",strtotime($row[mdate])); ?> </h2>

		<table cellpadding="0" cellspacing="0" class="bnb-content">
			   <tr>
			   <td class="left">
		<?if($has_image||$has_author){?>
			<?if(is_file("$D_R$row[image]")){?>
				<img src="<?=$IMG_SERVER?><?=$row[image]?>" border=0 width="126" height="140"><br />
			<?}?>
			<?if($row[author]){?>
			<h5><?=$row[author]?></h5>
			<?}?>
		<?}?>
		<span><?=$row[position]?></span>
				 </td>
			   <td>
			   <div class="banterClass">
			   <?
			   $row[body]= str_replace("color=red","color=black",$row[body]);
			   ?>
				<?=$row[body]?>
			   </div>
				</td>
			   </tr>
			   </table>
	</a>
<br><br>
<?}?>

<br>

</body>
</html>