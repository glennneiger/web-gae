<?php
global $D_R,$CDN_SERVER;
include_once($D_R.'/lib/_includes.php');
session_start();
$localSessionId=session_id();
$_SESSION['localsession']=$localSessionId;
?>
<link rel="stylesheet" href="<?=$CDN_SERVER?>/css/ibox.1.1.css" type="text/css" />
<link href="<?=$CDN_SERVER?>/css/main.css" rel="stylesheet" type="text/css" />
<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="<?=$CDN_SERVER?>js/AC_RunActiveContent.js" language="javascript"></script>

<script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js.");
	} else {
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0',
			'width', '955',
			'height', '600',
			'src', 'MillionMFinal_v1',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'MillionMFinal_v1',
			'bgcolor', '#042749',
			'name', 'MillionMFinal_v1',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', 'MillionMFinal_v1',
			'salign', ''
			); //end AC code
	}
</script>
<table align="center" width="50%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" border="0">
	<tr>
		<td>
			<center>
			<!--	<noscript> -->
					<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="955" height="600" id="MillionMFinal_v1" align="middle">
				      <param name="allowScriptAccess" value="sameDomain" />
				      <param name="allowFullScreen" value="false" />
				      <param name="movie" value="MillionMFinal_v1.swf" />
				      <param name="quality" value="high" />
				      <param name="bgcolor" value="#042749" />	
						<embed src="MillionMFinal_v1.swf" quality="high" bgcolor="#042749" width="955" height="600" name="MillionMFinal_v1" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />    
					</object> 
 				<!--</noscript> -->
			</center>
		</td>
	</tr>
</table>