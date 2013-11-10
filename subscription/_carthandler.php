<?php

session_start();
include_once("$D_R/lib/_module_data_lib.php");
include_once("$D_R/lib/_module_design_lib.php");
include_once("$D_R/lib/_via_data_lib.php");
include_once("$D_R/lib/_via_controller_lib.php");
include_once("$D_R/subscription/cart.php");
include_once("$D_R/lib/registration/_register_product_design_lib.php");

global $_SESSION, $arrcases, $promoCodeSourceCodeNoFreeTrial,$pageName;
$objRegDesign= new regitrationProductDesign();

if(isset($_POST['action'])){ $action  = $_POST['action'];}
else{$action = 'default';}
if(isset($_POST['sdefid'])){ $sdefid  = trim($_POST['sdefid']);}
else{$sdefid = '';}
if(isset($_POST['orderItemType'])){ $orderItemType  = strtoupper($_POST['orderItemType']);}
else{$orderItemType = '';}

$objcart = new ViaCart('cart');
$case = '';	

validate_cart();

 $productName=$_POST['productName'];
 $producttype=$_POST['producttype']; 	
$pdata = getProdSubPriceVal($sdefid, $orderItemType); /*renderyourcart is defined in lib/_module_data_ilb.phjp*/
$pdata[0]['productName']=$productName;
$pdata[0]['producttype']=$producttype;

switch ($action){	
	case "addtocart":
		 $result = $objcart->add_item($sdefid, $pdata['0']);	//	echo $sdefid;
		 $result='ga'.$result;
		 break;
	case "deletecart":		
	
		$objcart->remove_item($sdefid, $orderItemType);
		// to remove BMTP susb+Prod if BMTP prod is rempved
			if($sdefid==$viaProducts['BMTP']['typeSpecificId'] &&  $orderItemType==$viaProducts['BMTP']['orderItemType'] ){
				$objcart->remove_item($viaProducts['BMTPAlert']['typeSpecificId'], $viaProducts['BMTPAlert']['orderItemType']);
			}
			
			if($sdefid==$viaProducts['BMTP']['typeSpecificId'] &&  $orderItemType==$viaProducts['BMTP']['orderItemType'] ){
				$objcart->remove_item($viaProducts['BMTPAlert']['typeSpecificId'], $viaProducts['BMTPAlert']['orderItemType']);
			}
		if(count($_SESSION['viacart'][$orderItemType])>0 && is_array($_SESSION['viacart'][$orderItemType][$sdefid])){
			unset($_SESSION['viacart'][$orderItemType][$sdefid]);
			unset($_SESSION['viacart'][$viaProducts['BMTPAlert']['orderItemType']][$viaProducts['BMTPAlert']['typeSpecificId']]);
		}
			
		    if($_POST['redirectCart'] && count($_SESSION['viacart']['SUBSCRIPTION'])=="0"){
				return false;
			}else{
    	validate_cart();
			}
    	
		break;	
	case "confirmcart":
		$removeid = $_POST['remove'];
	 	$addid = $_POST['add'];		 
		$orderItemType = strtoupper($_POST['orderItemType']);

		if(count($_SESSION['viacart'][$orderItemType])>0 && is_array($_SESSION['viacart'][$orderItemType][$removeid])){
			unset($_SESSION['viacart'][$orderItemType][$removeid]);
		}
		$pdata = getProdSubPriceVal($addid, $orderItemType);  

		$result = $objcart->add_item($addid, $pdata['0']); 
    	validate_cart();

		break;
	case "checkcart":   // if made changes to array then also copy in /subsrciption/confirm.php

		 /*google analytics code*/
		$productName=$_POST['productName'];
		$trialtype="HardTrial";  
		$producttype=$_POST['producttype']; 
		$eventname=$_POST['eventtype']; 
		$source=$_SERVER['HTTP_HOST'];
		$keyword=$_POST['keyword'];
			if($_POST['from']){
				$from=$_POST['from'];
			}else{
				$from=substr($_SERVER['HTTP_REFERER'],7); 
			}

		$objViaController = new ViaController();
		  $pName = trim($pdata['0']['product']); 
		  $idAdFree=$viaProducts['AdFreeMonthly']['typeSpecificId'];
		  $adFreeOrderItemType=$viaProducts['AdFreeMonthly']['orderItemType'];
		 $oc_id  = $pdata['0']['oc_id'];
		 $orderItemType = $pdata['0']['orderItemType'];
		 $prodsubids = str_replace("'","",getprodsubdefid($pName,true));
		 $ids = explode(",",$prodsubids );
		 if(count($ids)){
			foreach ($ids as $idval){ 
				$idval = trim($idval);    
				   if(($objcart->in_cart($idval, $orderItemType)) && ($sdefid!=$idval)){	 
				   		$case = $arrcases['change_billing_cycle'];					 
						echo  trim($idval.','.$sdefid.','.$case);
						return true;
					}elseif(($objcart->in_cart($idval, $orderItemType)) && ($sdefid==$idval)){		 
				   		$case =	$arrcases['already_in_cart'] ;
						echo  trim($sdefid.', ,'.$case);
						return true;
					}elseif($sdefid==$viaProducts['AdFreeMonthly']['typeSpecificId']){
							if(!$objViaController->isSetAdsFree()){
								$case =	$arrcases['add_to_cart'];
								$eventname=$eventname.'Addtocart';
								echo  trim($sdefid.','.$sdefid.','.$case);
							}
							return true;
					}elseif(($objcart->in_cart($idAdFree, $adFreeOrderItemType)) && in_array($sdefid,$productAd)){					
						$case = $arrcases['change_adfree_to_subscription'];					 
						echo  trim($idAdFree.','.$sdefid.','.$case);
						return true;
					}elseif(in_array($_SESSION['promoCodeSourceCode'], $promoCodeSourceCodeNoFreeTrial)){
						$case = $arrcases['promo_already_in_cart'];
						echo  trim($sdefid.', ,'.$case);
						return true;
					}elseif($_SESSION['LoggedIn']){
							$flag = false;	
							//check if for gicen oc_id there exists >1 products in VIA subscription
							$result = getActiveViaProdsCount($oc_id);
							if($result!=''){
								$flag = true;
								$case = $arrcases['call_cancel_subscription'];
							}
							//check whether this product exists in Via ACTIVE prods or its complementary	
							if (!$flag) {												
								$result = checkpremiumprods($ids, $idval, $orderItemType);
								if(($result!='')&& ($result !=$sdefid)){								 
									$flag = true;
									$case = $arrcases['counter_billed_already_in_via'];
								}elseif(($result!='')&& ($result ==  $sdefid)){								 
									$flag = true;
									$case = $arrcases['already_in_via'];
								}
							}
							//check whether this product exists in Via TRIAL prods or its complementary						
							if (!$flag) {
								$result = checktrialprods($pName, $idval, $orderItemType);
									if ($result!=''){
										$flag = true;
										$case = $arrcases['trial_already_in_via'];
									}
							}
							
							if (!$flag){	//check whether user ever has this product
								if($_SESSION['cancelledOrdersStatus']==''){
									$objVia=new Via();
									$cancelledOrderStatus = $objVia->get_cancelled_order_status_with_ocId($_SESSION["viaid"]);
									set_sess_vars("cancelledOrdersStatus",$cancelledOrderStatus);
								}
								
								if(array_key_exists($oc_id,$_SESSION['cancelledOrdersStatus'])){
									$searchWord = '/^cancel/';
								preg_match($searchWord, strtolower($_SESSION['cancelledOrdersStatus'][$oc_id]), $matches, PREG_OFFSET_CAPTURE);
								if((!empty($matches)) || ($_SESSION['cancelledOrdersStatus'][$oc_id] == "SHIPPED_COMPLETE") ){
									$flag = true;
									$case = $arrcases['trial_not_allowed'];
								}
								}
							}
													
							if (!$flag) {	//check whether this product exists in Via Complemetary prods preffred users
								$result = checkComplementoryProds($pName, $idval, $orderItemType);
								if ($result!=''){
									$flag = true;
									$case = $arrcases['complementory_in_via'];
								}
							}
							

							//check whether this product exists in Via payment by cheque prods preffred users

							if (!$flag) {
								$result = checkCKProds($pName, $idval, $orderItemType);
								if ($result!=''){
									$case = $arrcases['checkpayment_in_via'];
									echo  trim($sdefid.','.$case);
									return true;
								}
							}
							
							if(!$flag){	 								
								foreach ($ids as $idval){
									if($sdefid==$idval){
										if($objcart->in_cart($idval, $orderItemType)){ 
											$case =	$arrcases['already_in_cart'] ;
											echo  trim($sdefid.', ,'.$case) ;
											return true;										
										}
									}else{
										if($objcart->in_cart($idval, $orderItemType)){ 
											$case = $arrcases['change_billing_cycle'];					 
											echo  trim($idval.','.$sdefid.','.$case);
											return true;										
										}

									}
								}
								
								$case = $arrcases['add_to_cart'];
								$eventname=$eventname.'Addtocart';
								echo  trim($sdefid.','.$sdefid.','.$case);						
								
							}else{
								echo  trim($result.','.$sdefid.','.$case);
							}
							return true;			
								
													}
								 	
			}//for loo[p	 								
								$case =	$arrcases['add_to_cart'];
								$eventname=$eventname.'Addtocart';
								echo  trim($sdefid.','.$sdefid.','.$case);
								return true;
										  
					} //if loop
		 	
		break;	
		
	case "validateloggedcart":	
		$validatedcart = validatecartlogged();
		
		break;	
	case "submitcart":	
		$flag = false;
		$trialtype="HardTrial"; 
		$eventname='Register';
		if(count($_SESSION['viacart'])>0){ 
			foreach($_SESSION['viacart'] as $viaCartArr){
			foreach($viaCartArr as $viacartval){
				/*google analytics code on register submit*/
				$productName=$viacartval['productName'];
				$producttype=$viacartval['producttype']; 
				$source=$_SERVER['HTTP_HOST'];
				$keyword=$_POST['keyword'];
					if($_POST['from']){
						$from=$_POST['from'];
					}else{
						$from=substr($_SERVER['HTTP_REFERER'],7);
					}
				
				
				if($viacartval['discountedPrice']!='' or $viacartval['discountedPrice']>0){					
					$pName = trim($viacartval['product']);
					$orderItemType = $viacartval['orderItemType'];
					$prodsubids = str_replace("'","",getprodsubdefid($pName,true));
					$ids = explode(",",$prodsubids ); 
					foreach ($ids as $idval){ 
						//check if via has premium prod for the 
						$result = checkpremiumprods($ids, $idval, $orderItemType);
						 if(($result!='')&& ($result != $idval) && is_array($_SESSION['viacart'][$orderItemType][$idval]) ){								 
							$flag = true;
							$case = $arrcases['counter_billed_already_in_via'];
						} //check whether this product exists in Via TRIAL prods or its complementary			
						if (!$flag) {
							$result = checktrialprods($pName, $idval, $orderItemType);
							if ($result!=''){
								$flag = true;
								$case = $arrcases['trial_already_in_via'];
							}
						}//check whether this product exists in Via Complemetary prods preffred users			
							if (!$flag) {
								$result = checkComplementoryProds($pName, $idval, $orderItemType);
								if ($result!=''){
									$flag = true;
									$case = $arrcases['complementory_in_via'];
								}
							}
						//check whether this product exists in Via payment by cheque prods preffred users

							if (!$flag) {
								$result = checkCKProds($pName, $idval,$orderItemType);
								if ($result!=''){
									$flag = true;
									$case = $arrcases['checkpayment_in_via'];
								}
							}
					}//for each ids

				}// if discount price
			}//for each
			}//for each outer
		}// if session count

		if($flag ){
			echo $case.',';
		}else{
			echo 'true';
		}		
		return true;
		break;	
	case "validatePromoCode":		 
		 $pCode  = trim(stripslashes($_POST['pCode']));
		 $result = getPromoStatus($pCode);
         set_sess_vars("promoCodeValue",$pCode);
		 if($result['source_code_id']!=''){
			 //echo ($result['source_code_id']);
			  set_sess_vars("validPromoCode",true);			 
			  set_sess_vars("promoCodeSourceCode",$result['source_code_id']);
			  //return true;
		 }else{
			 unset($_SESSION['validPromoCode']);
			 //unset($_SESSION['promoCodeValue']);
			 unset($_SESSION['promoCodeSourceCode']);			 
			 echo 'error';
			 return false;
		 }		
		break;	
	case "resetPromoCode":		 		 		  
			 unset($_SESSION['validPromoCode']);
			 unset($_SESSION['promoCodeValue']);
			 unset($_SESSION['promoCodeSourceCode']);		  		
		break;
	case "editProdSubs":
		//Cart subscription cycle is editted to newly added prod		
		//get old prods subscriptionID & send with old prod
		//set autorenew of old to DoNOtrenew

		$from  = $_POST['frm'];
		$to  = $_POST['to'];
		
		if(is_array($_SESSION['products'][$orderItemType][$from]) && $_SESSION['products'][$orderItemType][$from]['auto_renew']==1){  
			$subscriptionId = $_SESSION['products'][$orderItemType][$from]['subscriptionId'];
			$viaObj = new Via();
			$response = $viaObj->editOrder($_SESSION['products'][$orderItemType][$from],'DO_NOT_RENEW');  //send Via request to set DoNotRenew for existing Via prod
			if($response!='true'){ //if don_not_renew is failed
					echo 'Edit request failed';
			}else{ //if don_not_renew is a success then set subscriptionId in viacart for new product
				$result=$viaObj->setProductTrail($_SESSION['products'][$orderItemType][$from]['orderClassId'],$_SESSION['products'][$orderItemType][$from]['subscriptionId'],$_SESSION['products'][$orderItemType][$from]['typeSpecificId'],'DO_NOT_RENEW', $orderItemType);
				if($to){ /* $to only exist in case of same product added and edited for not renewal*/
				if(is_array($_SESSION['viacart'][$orderItemType][$to])){
					$_SESSION['viacart'][$orderItemType][$to]['subscriptionId'] = $subscriptionId;
				}else{
					 $pdata = getProdSubPriceVal($to, $orderItemType); 
					 $result = $objcart->add_item($to, $pdata['0']);
					 $_SESSION['viacart'][$orderItemType][$to]['subscriptionId'] = $subscriptionId;
				}
				}

				

			}
		}else{  //handles if product is in session with auto_renew=0 & also in acrt & user tries to change billing cyclee
			if(is_array($_SESSION['viacart'][$orderItemType][$from])){
				$objcart->remove_item($from, $orderItemType);
				if(count($_SESSION['viacart'][$orderItemType])>0 && is_array($_SESSION['viacart'][$orderItemType][$from])){
					unset($_SESSION['viacart'][$orderItemType][$from]);
				}	
			}	
			$pdata = getProdSubPriceVal($to, $orderItemType);  
			$objcart->add_item($to, $pdata['0']); 
	
		} 

		//unset product if already exists Product in inline validations with same oc_id && order_code_id with different subscription_def_id ( Monthly or Annual)

		$oc_id = $_SESSION['viacart'][$orderItemType][$to]['oc_id'];
		$order_code_id = $_SESSION['viacart'][$orderItemType][$to]['order_code_id'];

		foreach($_SESSION['viacart'][$orderItemType] as $arr){ 
			if($arr['oc_id'] == $oc_id && $arr['order_code_id'] == $order_code_id && $arr['subscription_def_id']!= $to ){ 
				unset($_SESSION['viacart'][$orderItemType][$arr['subscription_def_id']]);
			}
		}


		break;
		
	case "updateCart":
		$to  = $_POST['subdefId'];
		$oc_id = $_POST['oc_id'];
		$orderItemType = $_POST['orderItemType'];	
		$pageName=$_POST['pageName'];
		$pdata = getProdSubPriceVal($to, $orderItemType);  
		$frmUpdate = $objcart->get_removeProduct($orderItemType,$oc_id);
		$objcart->add_item($to, $pdata['0']); 
		$objcart->remove_item($frmUpdate, $orderItemType);
		if(count($_SESSION['viacart'][$orderItemType])>0 && is_array($_SESSION['viacart'][$orderItemType][$frmUpdate])){
			unset($_SESSION['viacart'][$orderItemType][$frmUpdate]);
		}
		break;
	case "updateCartAddmultipleProduct":
		$to  = $_POST['subdefId'];
		$oc_id = $_POST['oc_id'];
		$orderItemType = $_POST['orderItemType'];	
		$pageName=$_POST['pageName'];
		$removeProduct=$_POST['removeproduct'];
		$pdata = getProdSubPriceVal($to, $orderItemType);  
		if($removeProduct){
			$frmUpdate = $objcart->get_removeProduct($orderItemType,$oc_id);
			$objcart->remove_item($frmUpdate, $orderItemType);
		}else{
			$objcart->add_item($to, $pdata['0']); 
		}
		
		if(count($_SESSION['viacart'][$orderItemType])>0 && is_array($_SESSION['viacart'][$orderItemType][$frmUpdate])){
			unset($_SESSION['viacart'][$orderItemType][$frmUpdate]);
		}
		break;	
	
	case "updateProductDiv":
		return productPriceDisplay();
		break;
		
	case "updateProductBillingDiv":
		return $objRegDesign->userproductinfo();
		break;
		
	case "addProduct" :
		$to  = $_POST['add'];
		$orderItemType = $_POST['orderItemType'];	
		$pdata = getProdSubPriceVal($to, $orderItemType); 
		$objcart->add_item($to, $pdata['0']);
		set_sess_vars('ShowOkfreeTrial','1');
		break;
		
	case "default":
	    $pageName=$_POST['pageName'];
		break;
}  

if($action != 'addtocart')
{
$str .= renderyourcart($_SESSION['viacart'], $validatedcart,'',$_POST['redirectCart'],$frmUpdate); 
}
if($action == 'addtocart' && $_SESSION['viaid']){
	echo $_SESSION['viaid'];
}
/*renderyourcart is defined in lib/_module_design_ilb.phjp*/
echo $str ;
?>