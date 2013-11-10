<?
ob_start();
session_start();
if(isset($_GET['orderitemtype'])){
	 $orderItemType = strtoupper($_GET['orderitemtype']);
	 
} 
if(isset($_GET['frm'])&& $_GET['to']!=''){
	$frm = trim($_GET['frm']);
	$pfrm = getProdSubPriceVal($frm, $orderItemType);  
	$pfrm_product = $pfrm['0']['product'];
}
if(isset($_GET['to']) && $_GET['to']!='' ){
	$to = trim($_GET['to']);
	$pto = getProdSubPriceVal($to, $orderItemType); 
	$pto_product = $pto['0']['product'];
} 

if($_GET['viacancel']=='cancel'){
	$cancelVia = $_GET['viacancel'];	
}else{
	$cancelVia='';
}

/* Cancel dropdown list submission implemented*/
$showlist = false;
if($_GET['showlist']=='showlist'){
	$showlist = true;	
	 
}else{
	$showlist = false;
}

/*Call # button implemented*/
$showcall = false;
if($_GET['showcall']=='showcall'){
	$showcall = true;	
	 
}else{
	$showcall = false;
}


build_lang('subscription_product');
build_lang('manage_setting');
global $_SESSION, $lang, $viaProductsName,$viaPromoProductName; 
$btn ='';
$msg ='';
$id = $frm."_".$to;
$promoCode = $_SESSION['promoCodeValue'];
if(isset($_GET['case'])){
	$case = $_GET['case'];
	$msg = $lang[$case];	
	 
	switch ($case) {
		case 'already_in_cart':
		case 'already_adfree_added':
			$msg_confirm ='';
			$btn = 'checkout';	
		break;		
		case 'already_in_via':		
		case 'cancelled_in_via':
		case 'already_have_subscribed_product':
		case 'subscribed_already_in_via':
		case 'adfree_remove_cart' :
			$msg_confirm ='';
			$btn = 'ok';	
		break;		
		case 'counter_billed_already_in_via':
		//case 'change_billing_cycle':
		//case 'trial_already_in_via':
		case 'complementory_in_via':	
		case 'checkpayment_in_via':
		//case 'adfree_remove_cart' :
			$btn = 'confirm';
			$msg_confirm  = 'You are currently subscribed '.$pfrm_product. '. Would you like to upgrade to '.$pto_product. ' account? It will take effect at the end of your current month.';
			//$msg_confirm  = 'Do you want to update subscription from '.$pfrm_product.' to '.$pto_product;
		break;
		case 'change_billing_cycle':
			$btn = 'confirm';
			$msg_confirm ='You currently have '.$pfrm_product. ' in your cart. Would you like to have '.$pto_product. ' account?';
			break;
		case 'trial_already_in_via':
			//$btn = 'confirm';
			//$msg_confirm  = 'You are currently subscribed '.$pfrm_product. '. Would you like to initiate your paid subscription now?';
			$btn = 'okAdd';
			$msg_confirm  = 'You are currently subscribed '.$pfrm_product. '. The charge will immediately process because you are not eligible for another trial.';
			break;
		case 'trial_not_allowed' :
			$btn = 'okAddbtn';
			$msg_confirm  = 'You have previously received free trial for '.$pto_product.'. Now the charge will immediately process because you are not eligible for another trial.';
			break;
		case 'change_adfree_to_subscription':
			$msg_confirm ='';
			$btn = 'confirm';
			break;
		case 'promo_already_in_cart':
			$msg_confirm = $promoCode.' can only applicable for '.$viaPromoProductName[$promoCode].' purchase only.';
			$btn = 'checkout';
			break;	
		case 'call_cancel_subscription':
			$msg_confirm ='';
			$btn = 'ok';	

	}
}
 
if($msg_confirm ==''){
	 $msg = cartiboxmsg($msg , $pfrm_product , $pto_product );//preg_replace($patterns,$replacements,$msg );
}else{
	 $msg = $msg_confirm;
}
  if($_SESSION['LoggedIn']){
		$objVia=new Via();
		$arrayFields=array('customerIdVia'=>$_SESSION[viaid]);	
		if($objVia->viaException==''){			
		$custDetails=$objVia->getCustomersViaDetail($arrayFields);	  
			$_SESSION['viaServiceError'] = false;
		}else{
			//$_SESSION['viaServiceError'] = True;			
		}
			  
		$ccType=$custDetails->CustomerGetResult->Customer->ccType;	
		$custViaId = $_SESSION['viaid'];
	}
	$removeFromCart =   $frm; 	 
	if(is_array($_SESSION['products'][$frm])){
		$frmArr = array();
		if($_SESSION['products'][$frm]['sourceCodeId']==2){			
			$frmArr = $_SESSION['combo'];	
			$cancelVia = true;
			
		}else{
			$frmArr = $_SESSION['products'][$frm];			
		}
			$orderNumber = $frmArr['orderNumber'];
			$orderItemSeq = $frmArr['orderItemSeq'];
			$frm  = $frmArr['typeSpecificId'];
	
	}
	 $typeSpecificId = $frm; 
	 $refundAmount = 0.00;
	 $targetUrlval = '/subscription/register/';	?>	
	 <table  border="0" cellpadding="0" cellspacing="0" >
	  <tr>
		<td width="98%" align="right" valign="top">&nbsp;</td>
		<td width="2%" align="right" valign="top"><a href="javascript:validateloggedcart();iboxclose();"><img src="<?=$IMG_SERVER?>/images/redesign/login_close.jpg" border="0" align="right" /></a></td>
	  </tr> 
	  <tr>
		<td colspan="2"><div id = "confirm_msg"  <? if($showcall){ ?> style="display:none;"<?}?>><table width="100%" border="0" cellpadding="5" cellspacing="5" > <tr>
		<td colspan="2"><h5><?=$msg;?></h5></td>
	  </tr>
	  <tr>  <?=getconfirmationbtns($to, $frm, $btn, $case, $orderItemType); //same $orderItemType will work for add n remove?></tr>
  </table></div></td></tr>   

<!-- Here is added call Cancel DIV -->
   <tr><td>
  <div id="call_cancel"  <? if(!$showcall){ ?> style="display:none;"  <?}?> >
  <table  width="100%" border="0" cellpadding="10" cellspacing="5">
  <tr><td colspan="2" align="center"><h5><?=$lang['call_cancel_subscription'];?></h5></td></tr>
  <tr><td colspan="2" align="right"><a style="cursor:hand;cursor:pointer;" target="_self" onclick="removefrmcart('<?=$to?>','<?=$orderItemType?>');hideeleid('<?=$id?>');iboxclose();";><img src="<?=$IMG_SERVER?>/images/redesign/ok.gif" alt="add to cart" border="0" /></a></td></tr>
  </table>  
  </div>  
  </td></tr>  
 <!-- Here Ends call Cancel DIV -->
 </table>
		
	
	
	
	
	
	
	
	
 
 