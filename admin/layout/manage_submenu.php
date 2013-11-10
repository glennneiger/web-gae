<?php 
include("$ADMIN_PATH/_header.htm");
include("$ADMIN_PATH/layout/layout_includes.php");

################# Request id ##############
$pid = is_numeric($_REQUEST['id']) ?  $_REQUEST['id'] : '0';
#######################
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Manage Sub Section Categories</title>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="10" border="0" class="admin_container">
<tr><td><?= displaysubmenu($pid);?>
</td></tr>
</table>
<? include("$ADMIN_PATH/_footer.htm"); ?>
</body>
</html>
