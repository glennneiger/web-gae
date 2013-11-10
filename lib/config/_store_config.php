<?php
/*================ ecommerce ===============*/
	$is_store=stristr($PHP_SELF,"/shops/");
	$DEBUGGING_ON=$is_dev;
	$SECURE_URL=($is_dev
					?"https://$HTHOST:8443/shops"
					:"https://$HTHOST/shops"
	);
	$UNSECURE_URL=($is_dev
			? "http://$HTHOST:8000/shops"
			: "http://$HTHOST/shops"
	);

	$CAT_TABLE   = "s_categories";
	$ORDER_TABLE = "s_orders";
	$PROD_TABLE  = "s_products";
	$PROMO_TABLE = "s_promocodes";
	$SHIP_TABLE  = "s_shipping";
	$INV_TABLE   = "s_inventory";
	$PARAM_TABLE = "s_params";
	$P_IMG_PATH  = "$PATH_FR/assets/catalog";
	$STORE_PAGE_SIZE=8000;
	$STORE_URL="$UNSECURE_URL/store.php";
	$STORE_REDIR = "/shops/store.redir.php";
	$STORE_CART  = "$UNSECURE_URL/store.cart.php";
	$STORE_CHECKOUT_URL = "$SECURE_URL/store.checkout.php";
	$STORE_THANKS_URL = "$UNSECURE_URL/store.checkout.php?step=thanks";
	$STORE_TEMPLATE_DIR="$D_R$PATH_FR/shops/tmpl/store";
	$STORE_CC_TYPES=array(
		"visa"=>"Visa",
		"mastercard"=>"Master Card",
		"amex"=>"American Express",
		"discover"=>"Discover"
	);
	$STORE_SHIP_TYPES=array(
		"ground"=>"Ground Service",
		"2-day"=>"2 Day Service",
		"express"=>"Express",
	);
	$STORE_SHIP_RATES=array(
		"ground"=>0,
		"2-day"=>9.95,
		"express"=>20.95
	);
	$STORE_SHIP_RANGES=array(//keys are cart totals, vals are charges
		25=>8.95,
		50=>12.95,
		100=>18.95,
		150=>23.95,
		1000000=>27.95
	);
	$STORE_STATUS_TYPES=array(
		'fulfilled'=>"Fulfilled",
		'backorderded'=>"Back Ordered",
		'unfulfilled'=>"Unfulfilled"
	);
	$STORE_TRANS_MOD=array(
		"type"=>"yourpay",
		"config"=>array(
			"host"=>"staging.linkpt.net",
			"port"=>"1139",
			"storename"=>"1001103337",
			"result"=>"GOOD",
			"keyfile"=>"$D_R/lib/cart-yourpay/certs/".($is_dev?"test":"minyanville").".pem"
		)
	);
	$STORE_GIFT_STR="egift subs";
	$STORE_FROM_ZIP="94521";
	$STORE_TAX_STATE="CA";
	$STORE_TAX_RATE=0.0825;
	$STORE_CONFIRM_EMAIL="$STORE_TEMPLATE_DIR/store.orderconfirm.eml.php";
	$STORE_CONFIRM_FROM="sales@minyanville.com";
	$STORE_CONFIRM_SUBJECT="Your order was received";
	$STORE_ADMIN_URL="$HTNOSSLDOMAIN/admin/store";
	$STORE_MAX_AGE=30;//(60*60)*1;//one hour
	$STORE_NOTIFY_LOWSTOCK=array(
		"dodecrement"=>1,
		"donotify"=>1,
		"stockno"=>10,
		"toemail"=>($is_dev?"mvil@minyanville.com":"sales@minyanville.com"),
		"fromemail"=>"Minyanville Store <info@minyanville.com>",
		"subject"=>"A product on minyanville has hit low stock",
		"tmpl"=>"$STORE_TEMPLATE_DIR/store.notify.lowstock.tmpl.php"
	);
	$STORE_FULFILLMENTCTR_PARAMS=array(
		"dosend"=>1,
		"toemail"=>($is_dev?"mvil@minyanville.com":"sales@minyanville.com"),
		"fromemail"=>"Minyanville Store <sales@minyanville.com>",
		"subject"=>"An order has been placed on Minyanville.com",
		"tmpl"=>"$STORE_TEMPLATE_DIR/store.fullfillment.eml.tmpl.php"
	);
	$USE_MAGICK=1;
	$BIG_SIZE=array(250,250);
	$THUMB_SIZE=array(60,60);

	$STORE_FATAL_ERROR_MSGS=array(
		"duplicate transaction"
	);
/*===================== mv cusomizations to store =================*/
	$STORE_CAT_FILES=array(
		1=>"/shops/spritedsweets.htm",/*lara misnamed!!*/
		2=>"/shops/toddostoys.htm",
		3=>"/shops/harrisons.htm",
		4=>"/shops/gallery.htm"
	);
	$STORE_CAT_NAMES=array(
		"/shops/spritedsweets.htm"=>"Spirited Sweets",/*lara misnamed!!*/
		"/shops/toddostoys.htm"=>"Toddo's Toys",
		"/shops/harrisons.htm"=>"Harrison's Dept. Store",
		"/shops/gallery.htm"=>"Gallery for the Arts"
	);

?>