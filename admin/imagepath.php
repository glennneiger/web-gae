<?php
global $D_R, $HTPFX,$HTHOST,$StorageListPath,$CKRootPath;

function getFolderList($url)
{
   global $CKRootPath;
   $fileList = file_get_contents($url);
   $fileList = json_decode($fileList);
   foreach($fileList->prefixes as $k=>$v)
   {
            $name = str_replace($CKRootPath, "", $v);
            $folderArr = explode("/",$name);
            $fileArr['folderPath'] = $v;
            $folderArr = array_filter($folderArr);
            $fileArr['haschild'] = chkChild($url,$folderArr[0]);
            $fileListArr[$v] = $fileArr;
   }
   return $fileListArr;
}

function chkChild($url,$folder)
{
    $chkUrl = str_replace("&delimiter=/", "", $url);
    $chkUrl = $chkUrl.$folder."/&delimiter=/";
    $childList = file_get_contents($chkUrl);
    $childList = json_decode($childList);
    if(empty($childList->prefixes))
    {
            return false;
    }
    else
    {
            return true;
    }
}

function getFileList($url,$rootFolder)
{
   global $STORAGE_SERVER;
   $fileList = file_get_contents($url);
   $fileList = json_decode($fileList);
   foreach($fileList->items as $k=>$v)
   {
            $name = str_replace($rootFolder, "", $v->name);
            if(strpos($name, "/")=="")
            {
                    $fileArr['image'] = $STORAGE_SERVER."/".$rootFolder.$name;
                    $fileArr['name'] = $name;
                    $fileArr['thumb'] = $STORAGE_SERVER."/".$rootFolder.$name;
                    $fileArr['folder'] = "images";
            }
            $fileListArr[] = $fileArr;
   }
   if(!empty($fileList->prefixes))
   {
        foreach($fileList->prefixes as $key=>$val)
        {
            $name = str_replace($rootFolder, "", $val);
            $folderArr = explode("/",$name);
            $folderArr = array_filter($folderArr);
            $folArr['haschild'] = chkChild($url,$folderArr[0]);
            $folArr['folderPath'] = $val;
            $fileListArr['prefixes'][] = $folArr;
        }
   }
    
   return $fileListArr;
}

$action = $_GET['action'];

switch($action)
{
    case 'getFolder':
        $folderPathUrl = $StorageListPath."?prefix=".$CKRootPath."&delimiter=/";
        $folderList = getFolderList($folderPathUrl);
       
      // $folderList = array(array('folderPath'=>'assets/FCK_Jan2011/images/Anthony Shields/','haschild'=>'1'),                            array('folderPath'=>'assets/FCK_Jan2011/images/April2012/','haschild'=>'0'),           array('folderPath'=>'assets/FCK_Jan2011/images/Donn Fresard/','haschild'=>'1'),         array('folderPath'=>'assets/FCK_Jan2011/images/Just/','haschild'=>'1'));
        array_unshift($folderList,array('folderPath'=>'assets/FCK_Jan2011/images/','haschild'=>'1'));
                    
        $folderList = json_encode($folderList);
        echo $folderList;
        break;
    case 'getImages':
        $folder = $_GET['folder'];

        $filePathUrl = $StorageListPath."?prefix=".$folder."&delimiter=/";
        $fileList = getFileList($filePathUrl,$folder);
     //    $fileList = array(array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),'prefixes'=> array(array('folderPath'=>'assets/FCK_Jan2011/images/Anthony Shields/','haschild'=>'1'), array('folderPath'=>'assets/FCK_Jan2011/images/April2012/','haschild'=>'0'), array('folderPath'=>'assets/FCK_Jan2011/images/Donn Fresard/','haschild'=>'1'),array('folderPath'=>'assets/FCK_Jan2011/images/Just/','haschild'=>'1')));
        $fileList = json_encode($fileList);
        echo $fileList;
        break;
    default:
        global $CKRootPath;
        $filePathUrl = $StorageListPath."?prefix=".$CKRootPath."&delimiter=/";
        $fileList = getFileList($filePathUrl,$CKRootPath);
   //     $fileList = array(array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'),array('image'=>'http://localhost:8080/admin/assets/headerlogo.gif','thumb'=>'http://localhost:8080/admin/assets/headerlogo.gif','folder'=>'images','name'=>'headerlogo.gif'));
        $fileList = json_encode($fileList);
        echo($fileList);
        break;
}

?>