<?
global $D_R;
include_once($D_R."/lib/_cart.php");
//include_once("$DOCUMENT_ROOT/lib/_includes.php");
//include_once("$D_R/lib/_includes.php");
	$cart=new CartDisplay();

	//this will only renew things if the cart is empty and there's a string posted
	$cart->acceptCartData();


/*============= redirects =================*/
if($step=="backtocart"){
	$cart->sendCartDataRedirect( $cart->carturl  );


}elseif($step=="continueshopping"){
	$cart->sendCartDataRedirect( $cart->last_product  );

/*=========== step1 validation/xFormation =============*/
}elseif(!$step){
//assume no step is 1st checkout page. don't feel right
	if(!$cart->num_items){
		$cart->sendCartDataRedirect($cart->carturl."?error=your cart is empty");
		exit();
	}
	//write_file("./order.hash",mserial($_POST));//write out form data for dev
	/*===========format data:UGLY need to wrap=======================*/
	$ord[cc_expire]=sprintf("%02d/%04d",$exp_mo,$exp_year);
	$ord[order_comments]=strip_tags($ord[order_comments]);
	$ord[ip]=$REMOTE_ADDR;
	$cart->setUserData($ord);//sets all subordinate totals for cart
	//should do data validation here -- too heavy reliance on JS
	$cart->validateOrder();
	//passed validation go to confirmation page
	if($cart->hasGiftSubs()){
		$cart->generateGiftCodes();
		$nextstep="egift";
	}else{
		$nextstep="confirm";
	}
	location($cart->checkouturl.qsa(array(step=>$nextstep)));
	exit();

}elseif($step=="finished"){
		$cart->completeCheckout();

/*====if they purchased a gift subscription they need to put email addresses w/ each item==*/
}elseif($step=="egift"){
	$cart->saveGiftCodeMails($_POST[email]);
	location($cart->checkouturl.qsa(array(step=>"confirm")));
}



?>