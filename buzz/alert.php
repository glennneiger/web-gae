<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Buzz Alert</title>
<style type="text/css">
body {
	text-align:center; 
	color: white;
	background-color:red;
	font:12px/16px Arial, Helvetica, sans-serif;
	font-weight:bold;
	margin:0 0 0 0;
	cursor:pointer;
	}
	
img {width:200px; height:23px; margin-bottom:15px;}
</style>
<script type="text/javascript">
window.focus();
setTimeout('self.close()',5000);

</script>
</head>
<body onclick="javascript:opener.focus();opener.jumpToLatestPost();self.close();">
<img src="images/alert_header.gif" />
NEW POST BY<br />
<?= strtoupper($_GET['author']); ?>
</body>
</html>
