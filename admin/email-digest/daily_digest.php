<?php
global $D_R,$ADMIN_PATH,$HTPFX,$HTADMINHOST;
include_once($ADMIN_PATH."/_header.htm");
if($_GET['mail']!='1'){
?>
<script type="text/javascript" src="<?php echo $HTPFX.$HTADMINHOST ?>/js/min/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $HTPFX.$HTADMINHOST ?>/js/config.1.2.js"></script>
<script type="text/javascript" src="<?php echo $HTPFX.$HTADMINHOST ?>/js/daily_digest.1.5.js"></script>
<link href="<?php echo $HTPFX.$HTADMINHOST ?>/css/email-digest.css" rel="stylesheet" type="text/css">
<?php
}
include($D_R."/admin/lib/_article_data_lib.php");
include($D_R."/admin/email-digest/daily_digest.htm");
include_once("$ADMIN_PATH/_footer.htm");
?>