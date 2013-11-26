<?php
global $D_R,$ADMIN_PATH,$HTPFX,$HTHOST,$HTADMINHOST,$CDN_SERVER;
if($_GET['id']=="" || $_GET['edit']=="1" )
{
	include($D_R."/admin/_header.htm");
}
if($_GET['mail']!='1'){
?>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/min/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/config.1.2.js"></script>
<script type="text/javascript" src="<?=$CDN_SERVER?>/js/daily_digest.1.5.js"></script>
<link href="<?=$CDN_SERVER?>/css/email-digest.css" rel="stylesheet" type="text/css">
<?php
}
include($D_R."/admin/lib/_article_data_lib.php");
include($D_R."/admin/ss/ss_classes/class_Performance.php");
include($D_R."/emails/daily_digest.htm");
if($_GET['id']=="" || $_GET['edit']=="1" )
{
	include($D_R."/admin/_footer.htm");
}
?>