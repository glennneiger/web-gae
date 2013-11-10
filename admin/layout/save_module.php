<?
 // action type  
$stAction = $_POST['action'];
######################################## action type edit case ##########################################################
$arObjectType  = explode(":", $_POST['selItemList']);
$arData['object_id']=$arObjectType[0];
$arData['object_title']=$arObjectType[0];
$arData['object_type']=$arObjectType[1];
$arData['commit_status'] = 'T';
if(isset($_POST['image_name']))
{
 $arData['image_path'] =  $_POST['image_name'];
}
if($stAction=='edit' ) // for edit
{ 
 $retunValue = update_query('homepage_module',$arData,array(id => $_POST['id'])); 
}
else // for add
{ 	
	  $sql="select max(order_type) as value from homepage_module";
	  $result= exec_query($sql);	
	  $arData['module_type']='Recent';
	  $arData['order_type'] = $result[0]['value']+1;	 	 
	  $id=insert_query("homepage_module",$arData);	  
 }
?>