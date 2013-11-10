<?php
global $D_R;
include_once("$D_R/lib/config/_release_script.php");
include_once("$D_R/build_manager.php");
echo 'value...'.$qaMinyanville;
?>
<html>
<head>
<title>Release Script </title>
<script language="javascript">
function showError(e){
	if(e.keyCode==9){
		alert('Please separate the files via enter.Space is not allowed');
	}
	if(e.keyCode==32){
		alert('Please separate the files via enter.Space is not allowed');
	}
}

</script>
<body>
<form name="form" method="post" >
<table>
	<tr>
	<td>select enviroment : </td>
	<td>&nbsp;</td>
	<td><select name="select">
	<option value="<?=$qaMinyanville; ?>">Minyanville QA</option>
	<option value="<?=$qaBuzzBanter; ?>">Buzz&Banter QA</option>
	</select></td>
	</tr>
	<tr>
	<td>File name : </td>
	<td>&nbsp;</td>
	<td><textarea name="textarea" onKeyDown="showError(event)" ></textarea></td>
	</tr>
	<tr><td colspan="3"><input type="checkbox" name="cleanCopy" value="1"/>Clean Copy</td></tr>
	<tr>
	<td colspan="3" align="center"><input type="submit" value="submit"   /></td>
	</tr>
</table>
</form>
</body>
</html>

<?
$dirPath = $_POST['select'];
$fileName = $_POST['textarea'];
//echo 'dir..'.$dirPath;
//echo '<br>files name..'.$fileName;
global $value,$dirPath,$fileName,$serverIp,$serverUser,$serverpwd,$serverDirUser,$serverDirPwd;
if($_POST['cleanCopy'] && $_POST['cleanCopy']== '1')
{
	$cleanCpy = '1';
}
else
{
	$cleanCpy = '0';
}
$objbuildManager = new buildManager($dirPath,$value,$cleanCpy);

//$objbuildManager->buildManager($dirPath,$value);
//echo '<br>ip...'.$serverIp;

/*
$fileList = explode("\n",$fileName);
if($dirPath){
	echo $dirPath;
	shell_exec("cd ".$dirPath);
	foreach($fileList as $key=>$value)
	{
		$value = trim($value);
		if(!empty($value))
		{
			
			
			//echo "<br>copy " .$value.' '. $value."." .date(mdY);
			$new = $dirPath.'/'.$value;
			if(!file_exists($new))
			{
				echo '<br>new File!!!!';
				echo 'file.....'.$value;
			}
			else{
				$exist = $dirPath.'/'.$value."." .date(mdY);
				if(!file_exists($exist))
				{
					echo '<br>notexist';
					shell_exec("move " .$value.' '. $value."." .date(mdY));
				}else{
					echo '<br>exist';
					shell_exec("copy " .$value.' '. $value."." .date(mdY).'.'.rand(10,99));
				}
			}
			$output = shell_exec("cvs update -d ".$value);
			echo "<br>cvs update ".$value;
			echo '<br>output...'.$output;
		}
	}
}
*/?>


