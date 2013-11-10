<?php

class ViaCart  { 
	public  $items;	 
	public  $CART;
	/**
	* void Cart
	* + Instantiates the class, sets the cart name
	* + to your cart name and sets the items array for use
	* -----
	* @param string : The name of the cart
	**/ 
	public function __construct($cname){ 	    			
		 if (!empty($_SESSION['viacart'])) {
			$this->items = $_SESSION['viacart'];
			$this->CART = $_SESSION['viacart'];
		} else {
			$this->items = array();
		}
		
	}

	/**
	* void add_item
	* + This function adds the items to the shopping cart array
	* + and serializes them
	* -----
	* @param int : ID of the item
	* @param string : Name of the item
	* @param int : Quantity of the item
	* @param double : Price of the item
	* @param string : Additional Notes
	**/
	public function add_item($id, $pdata, $q=1){
		 $orderItemType = $pdata['orderItemType'];		    
		 if (!$this->in_cart($id, $orderItemType)) {		 	
			$this->items[$orderItemType][$id] = $pdata;
 			$this->update_cart($id,$pdata, $orderItemType);
	}
	}


	/**
	* void update_cart
	* + This function updates the shopping cart, if a certain
	* + quantity for an item is 0, it removes it from the cart
	* -----
	* @param
	**/
	public function update_cart($id,$p,$orderItemType){ 
		session_start();
		global $_SESSION;		
		//$viacart = array();
		foreach ($this->items as $k=>$v) if ($v['quantity'] == 0) unset($this->items[$k]);
		if($p==''){	 
			 unset($_SESSION['viacart'][$orderItemType][$id]);	
			
		}else{
			$_SESSION['viacart'][$orderItemType][$id]= $p;//AME,$temp,time()+30*24*60*60);
		} 
		$this->CART = $_SESSION['viacart'];
	}
	/**
	* void remove_item
	* + This function removes an item from the cart with the
	* + specified "id" and "quantity" and updates the cart afterwards
	* -----
	* @param int : ID of the item to be removed
	* @param int : Quantity of the item to be removed
	**/
	public function remove_item($id, $orderItemType,$q=1){	 
			if(	is_array($this->CART[$id])){
			 unset($this->CART[$id]);
			 unset($_SESSION['viacart'][$id]);}
			 
	/*if($_SESSION['promoCodeSourceCode'] !=''){
		unset($_SESSION['promoCodeSourceCode']);
		unset($_SESSION['promoCodeValue']);
	}*/

		/*if ($q > $cq) 
			$this->items[$id]['quantity'] = 0;
		else
			$this->items[$id]['quantity'] -= $q;
		if ($cq == 0) */ 
			
		$this->update_cart($id,'',$orderItemType);
	}

	/**
	* void update_quantity
	* + This function updates the quantity of a given item
	* + with the specified "id" and quantity", if the given
	* + quantity is 0 or less, the item is removed from the cart
	* -----
	* @param int : ID of the item to be updated
	* @param int : New quantity of the item
	**/
	public function update_quantity($id, $nq){
		if (!$this->in_cart($id)) return;
		$this->items[$id]['quantity'] = $nq;
		$this->update_cart();
	}
	//htmlprint_r($_SESSION['viacart']);
			/**foreach($_SESSION['viacart'] as $key=>$val){
				if (!$val){
					unset($_SESSION['viacart'][$key]);
				}
			}**/
	
	/**
	* boolean in_cart
	* + This function returns whether the given item exists
	* + on the shopping cart
	* -----
	* @param int : ID of the item to be checked
	**/
	public function in_cart($id, $orderItemType){
		session_start();
		global $_SESSION,$productAd,$viaProducts,$arrcases;
		$orderItemType = trim($orderItemType);
		if(count($_SESSION['viacart'][$orderItemType] )){			 		
			 foreach($_SESSION['viacart'][$orderItemType] as $cartitems){
				if( (trim($cartitems['subscription_def_id']) == trim($id)) && (strtolower($cartitems['orderItemType'])==strtolower($orderItemType)) ) {					   
				  return true;				
				}				
			 }
			  return  false;
			}
			 return  false;
	}

	/**
	* int item_count
	* + This item returns the total number of items in the cart
	* -----
	* @param
	**/
	public function item_count(){
		$count = 0;
		foreach ($this->items as $v) $count += $v['quantity'];
		return $count;
	}

	

	/**
	* double total_price
	* + This function returns the total price of all the items
	* + in the cart
	* -----
	* @param
	**/
	public function total_price(){
		$total = 0;
		foreach ($this->items as $v) $total += $v['quantity']*$v['price'];
		return $total;
	}

	/**
	* void empty_cart
	* + This function empties the shoppig cart of all its items
	* -----
	* @param
	**/
	public function empty_cart(){
		foreach ($this->items as $k=>$v) unset($this->items[$k]);
		$this->update_cart();
	}

	/**
	* boolean is_zero
	* + This function checks whether there is a 0 value
	* + from all the arguments given to it
	* -----
	* @param mixed : Set of numbers => is_zero(3,0,2)
	**/
	public function is_zero(){
		foreach (func_get_args() as $arg) if ((int)$arg != 0) return false;
		return true;
	}
	/**
	* boolean is_zero
	* + This function checks whether there is a 0 value
	* + from all the arguments given to it
	* -----
	* @param mixed : Set of numbers => is_zero(3,0,2)
	**/
	public function get_cart(){	
	$this->update_cart(NULL,NULL);
		return $this->CART;
	}
	
	public function get_removeProduct($orderItemType,$oc_id){
		foreach($_SESSION['viacart'][$orderItemType] as $key){
			if($key['oc_id'] == $oc_id){
				$removeid = $key['subscription_def_id'];
			}
		}
		return $removeid;
	}
}















?>