<?php
global $D_R,$HTPFX,$HTHOST;
session_start();
if($_SESSION['peterTchir']!="1")
{
  Header( "Location: http://mvp.minyanville.com/peter-tchir-landing-page-navbar/?utm_source=navigation&utm_medium=website&utm_content=navigation&utm_campaign=petertchir");
  exit;
}
$pageName="peter-tchir";
include_once($D_R."/lib/config/_peter_tchir_config.php");
include_once($D_R."/lib/peter-tchir/_peterTchir_data_lib.php");
include_once($D_R."/lib/peter-tchir/_peterTchir_design_lib.php");
$pageJS=array("config","redesign","fancybox","peterTchir");
$pageCSS=array("ibox","global","layout","section","rightColumn","nav","minyanville","fancybox","peterTchir");
include($D_R."/_header.htm");
$objPeterData = new peterTchirData('peter_alerts');
$objPeterDesign = new peterTchirDesign();
global $objPeterData, $objPeterDesign, $pageName;

$p=0;
$offset=0;
if($_GET['p']){
	$p=$_GET['p'];
	$offset=$_GET['p'];
}
$categoryName="";
?>
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700' rel='stylesheet' type='text/css'>

<body>
<script language="javascript" type="text/javascript">
	function docKeyDown1(oEvent){ // controls keyboard navigation
	var oEvent = (typeof oEvent != "undefined")? oEvent : event;
	var oTarget = (typeof oEvent.target != "undefined")? oEvent.target : oEvent.srcElement;
	var intKeyCode = oEvent.keyCode;
	var inc;
	if(intKeyCode==13) {
	   if($F("optionsearch")){
		searchalert('optionsearch','<?=$contribid?>','optionsearch','<?=$oid?>');
		}
	}
	if(intKeyCode==17){	inc=0;}
	if (inc==0){
		if(intKeyCode==65 || intKeyCode==97 )	{
			return(false);   //in case of IE & firefox1.5
		}
	}



	}
	document.onselectstart=new Function("return false");
	document.onmousedown=new Function("return false");
	document.oncontextmenu=new Function("return false");
	document.onkeydown=docKeyDown1;

	function disableclick()
	{
		document.onmousedown=new Function("return false");void(0);
	}
	function enableclick()
	{
		document.onmousedown=new Function("return true");void(0);
	}
	</script>
<div id="ptc_wrapper">
	<?=$objPeterDesign->peterTchirHeader($categoryName);?>
	<div class="clr"></div>
	<div class="ptc_contentPanel">
		<?=$objPeterDesign->peterTchirLeftCol($categoryName,$offset,$p);?>
		<?=$objPeterDesign->peterTchirRightCol();?>
	</div>
	<div class="clr"></div>
</div>
</body>

<? include($D_R."/_footer.htm"); ?>
