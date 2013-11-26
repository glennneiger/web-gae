<?php
global $HTPFX,$HTHOST,$cloudStorageTool,$tempPath,$CDN_SERVER;

$handler = $HTPFX.$HTHOST."/admin/uploadBucket_mod.php";
$options = [ 'gs_bucket_name' => $tempPath ];
$frm_url = $cloudStorageTool->createUploadUrl($handler , $options);

$status = $_GET['status'];

?>
<script src="<?=$CDN_SERVER?>/js/min/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
function checkUpload()
{
	var partnerId = $('#partnerId').val();
	var fileInput = $('#fileInput').val();
	if(partnerId=="")
	{
		alert('Please select a partnaer Id');
		return false;
	}
	if(fileInput=="")
	{
		alert('Please select a file');
		return false;		
	}	
	$('#uploadFrm').submit();	
}
</script>
<html>
<body>
<form enctype="multipart/form-data" method="post" name="uploadFrm" id="uploadFrm" action="<?=$frm_url?>" >
<table border="0">
   <tr><td  style="font-size: 20px;font-weight: bold;color: 24478a;"><center><b>Upload File to Bucket</b></center></td>
   <td colspan="2"></td></tr>
   <tr><td colspan="3" style="color:red;">
   <?php
   if($status=="1")
   { echo "Uploaded Succesfully"; }
   else if($status=="0")
   { echo "Invalid Action";  }
   ?></td></tr>
	<tr>
		<td style="padding-bottom:20px;">Select partner : </td>
		<td style="padding-bottom:20px;">&nbsp;</td>
		<td style="padding-bottom:20px;">
			<select name="select" style="width:auto;" id="partnerId">
				<option value="">--Select Partner--</option>
				<option value="Radio">Radio</option>
				<option value="MoneyShow">MoneyShow</option>
				<option value="Zack">Zack</option>
			</select>
		</td>
	</tr>
	<tr>
	<td style="padding-bottom:20px;">Upload File : </td>
	<td style="padding-bottom:20px;">&nbsp;</td>
	<td style="padding-bottom:20px;"><input type="file" name="fileInput" id="fileInput"></td>
	</tr>
	<td colspan="3" align="center"><input type="button" value="submit" onClick="checkUpload();"  /></td>
	</tr>
</table>
</form>
</body>
</html>