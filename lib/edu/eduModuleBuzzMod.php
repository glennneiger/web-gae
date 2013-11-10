<?php
global $D_R,$HTPFX,$HTHOST,$IMG_SERVER;
include_once($D_R.'/lib/MemCache.php');
include($D_R."/lib/config/_edu_config.php");
include_once $D_R.'/lib/_layout_design_lib.php';
global $eduItemMeta;
$objCache = new Cache();
$objMemCache = new memCacheObj();
$action = $_POST['action'];
switch($action)
{
	case 'realbuzz':	
		if(!empty($_POST)){
			$pageid = $_POST['pageid'];
			$module = $_POST['module'];
			$objMemCache->deleteKey('module_454');
			echo $objCache->getPageModules($pageid,$module);
		}
	break;
	case 'scrollModule':
		$moduleId = $_POST['module_id'];
		$len = $_POST['len'];
		$data = $objCache->getScrollModuleContent($len,$moduleId);
		if(!empty($data))
		{
			foreach($data as $key=>$val)
			{
				$qryDetail = " SELECT url FROM ex_item_meta WHERE item_type='".$eduItemMeta."' AND item_id='".$id."'";
				$resUrl = exec_query($qryDetail,1);		
				$eduUrl = getEduArtUrl($val['item_id']);
				$body=getPartOfBody($val['body'],'16');
				$imgPath = $IMG_SERVER.'/assets/edu/images/'.$val['edu_img'];
				$sqlResult .= '<li class="scrollCon'.$module_id.'" ><div class="eduArtListDetail">
				<h3><a href="'.$resUrl['url'].'">'.mswordReplaceSpecialChars($val['item_title']).'</a></h3>
				<p class="eduArtListAuthor">
					<a href="'.$HTPFX.$HTHOST.'/gazette/bios.htm?bio="'.$val['authorId'].'>'.$val['authorname'].'</a><br>'.date("D F j, Y, g:i A",strtotime($val['created_on'])).' EDT</p>
				<div class="eduNthArtDesc">
					<div class="artImgBox">
						<img src="'.$imgPath.'">
					</div>	
					<p>'.$body.'</p><br>
				</div></div><div style="clear:both;"></div></li>';
			}		
			$result['status']=true;
			$result['html']=$sqlResult;
		}
		else
		{
			$result['status']=false;
		}		
		echo json_encode($result);	
			
	break;
	default:
	break;
}
?>