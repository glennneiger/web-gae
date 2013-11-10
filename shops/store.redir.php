<?
global $D_R;
include_once($D_R."/lib/_cart.php");

$cart=new CartDisplay();
/*---------------------
actions ($action):
	add     : add an item to the cart.needs an $inv_id and a quantity
	remove  : needs an $inv_id
	accept  : needs $cartdata (mserialized) and  a redir
			: otherwise it will go the last product
	update_ : cart mods. done with $del or $upd arrays
	update_clear    : remove all items in the cart
	update_checkout : saves everything and goes to checkout page
---------------------*/

/*=========== var set-up ============*/
if(!$refer=base64_decode($refer)){

	$refer=$cart->last_product;
	if(!$refer)
		$refer="./store.php";//dumb
}
//cheat submit buttons into overwriting $action
if(count($subs=parse_submit_buttons()) ){
	if($subs[action])$action=$subs[action];
}

if(!$action){

	location($refer);
}


/*=================== actions for script =================*/
//add one sku
if($action=="add" || $action=="add_y"){
	$cart->add($inv_id, $quantity);
}
//add many skus
if($action=="addmany"){
	foreach((array)$inv_ids as $inv_id=>$on){//
		$cart->add($inv_id, 1);
	}
}
//remove one sku
if($action=="remove"){
	$cart->remove($inv_id);
}

//remove entire cart
if($action=="removeall"){
	$cart->removeAll();
}
//handle stale cart
if($action=="killstale"){

}

//update cart with an mserialized post w/ $cartdata
if($action=="accept"){//posting
	$cart->acceptCartData();
}

//cart update -- usually coming from the shopping cart page
if(stristr($action,"update_")){


//quantity updates
	foreach( (array)$upd as $id=>$quan){
		$cart->add($id,$quan);
	}

//removing skus
	foreach( (array)$del as $id=>$on){
		$cart->remove($id);
	}
//remove all
	if($action=="update_clear"){//empty out cart
		$cart->removeAll();
	}
//go to user data form
	if($action=="update_checkout_y" || $action=="update_checkout"){//persist cart data to checkout url


		if($cart->num_items){

			$cart->goToCheckOut();
			exit();
		}
	}
}



/*==================== misdirection ====================*/
location($refer);
?>