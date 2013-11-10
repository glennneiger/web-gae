<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php 
global $HTPFX,$HTHOST;
$rootFolder = "ckfinder/images/";
$url="https://www.googleapis.com/storage/v1beta2/b/mvassets/o?prefix=".$rootFolder."&delimiter=/";

$fileList = file_get_contents($url);
$fileList = json_decode($fileList);
foreach($fileList->prefixes as $k=>$v)
{
	$name = str_replace("ckfinder/images/", "", $v);
    $folderArr = explode("/",$name);
    $folderArr = array_filter($folderArr);
    $fileArr[$folderArr[0]]['folder'] = $folderArr[0];
    $fileArr[$folderArr[0]]['folderPath'] = $v;
    $currFolder = $folderArr[0];
    do{
    	
    	$chkUrl = "https://www.googleapis.com/storage/v1beta2/b/mvassets/o?prefix=".$rootFolder.$currFolder."/&delimiter=/";
    	$childList = file_get_contents($chkUrl);
       	$childList = json_decode($childList);
       	if(!empty($childList->prefixes))
       	{
       		$childArr = getFolderList($childList->prefixes);
			$fileArr[$folderArr[0]]['child']=$childArr;
       	}    	
    }while(chkChild($currFolder)==true);
    $fileListArr[] = $fileArr;
}       

function getFolderList($folderArray)
{
	foreach($folderArray as $key=>$val)
	{
		$name = str_replace("ckfinder/images/", "", $val);
	    $folderArr = explode("/",$name);
	    $fileArr['folderPath'] = $val;
	    $folderArr = array_filter($folderArr);
	    $fileArr['folder'] = $folderArr[0];
	    $childListArr[] = $fileArr;
	}
	return $childListArr;    
}

function chkChild($folder)
    {
   		$chkUrl = "https://www.googleapis.com/storage/v1beta2/b/mvassets/o?prefix=".$rootFolder.$folder."/&delimiter=/";
       	$childList = file_get_contents($chkUrl);
       	$childList = json_decode($childList);
       	if(empty($childList->prefixes))
       	{
       		$output['status']="1";
       		$output['value']=$childList->prefixes;
       	}
       	else
       	{
       		$output['status']="0";
       	}
    }
       htmlprint_r($fileListArr);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>PHP File Tree Demo</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link href="<?=$HTPFX.$HTHOST?>/css/fileTree.css" rel="stylesheet" type="text/css" media="screen" />
		
		<!-- Makes the file tree(s) expand/collapsae dynamically -->
		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/min/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="<?=$HTPFX.$HTHOST?>/js/min/jquery-ui.min.js"></script>
		<script type="text/javascript">
		$(document).ready( function() {
			// Hide all subfolders at startup
			$(".php-file-tree").find("UL").hide();
			
			// Expand/collapse on click
			$(".pft-directory A").click( function() {
				$(this).parent().find("UL:first").slideToggle("medium");
				if( $(this).parent().attr('className') == "pft-directory" ) return false;
			});
		});
		</script>
	</head>

	<body>
		<ul class="php-file-tree">
			<li class="pft-directory">
				<a href="#">images</a>
				<ul>
					<li class="pft-directory">
						<a href="#">excel docs</a>
						<ul>
							<li class="pft-file ext-xls">
								<a href="javascript:alert('You clicked on demo/documents/excel docs/accounting.xls');">accounting.xls</a>
							</li>
							<li class="pft-file ext-xls">
								<a href="javascript:alert('You clicked on demo/documents/excel docs/ar.xls');">ar.xls</a>
							</li>
						</ul>
					</li>
					<li class="pft-directory">
						<a href="#">excel docs</a>
						<ul>
							<li class="pft-file ext-xls">
								<a href="javascript:alert('You clicked on demo/documents/excel docs/accounting.xls');">accounting.xls</a>
							</li>
							<li class="pft-file ext-xls">
								<a href="javascript:alert('You clicked on demo/documents/excel docs/ar.xls');">ar.xls</a>
							</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</body>
	
</html>
