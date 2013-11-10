<?

include_once(dirname(__FILE__)."/_includes.php");
/*================================================================
  ======================= data layer of store ====================
  ================================================================*/

class Store{
	function Store(){
		global $PROD_TABLE,$ORDER_TABLE,$CAT_TABLE;
		global $PROMO_TABLE,$SHIP_TABLE,$INV_TABLE, $PARAM_TABLE;
		global $P_IMG_PATH, $D_R, $BIG_SIZE,$THUMB_SIZE, $STORE_PAGE_SIZE,$PATH_FR;
		global $STORE_NOTIFY_LOWSTOCK,$STORE_ADMIN_URL;
		$this->pTable=$PROD_TABLE;//products
		$this->oTable=$ORDER_TABLE;//orders
		$this->cTable=$CAT_TABLE;//categories
		$this->sTable=$SHIP_TABLE;//shipping
		$this->paramTable=$PARAM_TABLE;
		$this->iTable=$INV_TABLE;
		$this->promoTable=$PROMO_TABLE;//promo
		$this->imgPath=$PATH_FR.$P_IMG_PATH;//path to product images
		$this->imgDir=$D_R.$P_IMG_PATH;
		$this->pageSize=$STORE_PAGE_SIZE;
		$this->paramLength=3;
		$this->onlyEnabled=0;
		$this->paging=0;
		$this->adminUrl=$STORE_ADMIN_URL;
		$this->notifyParams=$STORE_NOTIFY_LOWSTOCK;
		$this->images=array(
			"big"=>$BIG_SIZE,
			"tn"=>$THUMB_SIZE
		);
		$this->imgPrefixes=array_keys($this->images);
		//imgPath= $imgDir/$type/$prod_id-$prefix.jpg -- type=[products|categories|inventory]
		$this->imgPathFormat="$this->imgDir/%s/%d-%s.jpg";//see getImgDir method
		$this->paramFields=array();
		foreach(range(1,$this->paramLength) as $i){
			$this->paramFields["param${i}"]="param${i}_name";
		}
	}
	function getParameters($id=0){
		debug("STORE:->getParameters($id)");
		$qry="SELECT * FROM $this->paramTable";
		if($id){
			$qry.=" WHERE id='$id'";
			return exec_query($qry,1,"name");
		}
		return exec_query($qry);
	}
	function getProducts($prod_id=0,$cat_id=0,$pageno=0,$s="ordr"){
		debug("STORE:->getProducts($prod_id,$cat_id,$pageno)");
		//if $prod_id supplised just returns single row w/ product
		$params=array();
		$lists=qw("cat_ids prod_ids");
		$qry="SELECT * FROM $this->pTable";
		if($prod_id)
			$params[]="id='$prod_id'";
		if($cat_id)
			$params[]="find_in_set('$cat_id',cat_ids)";
		if($this->onlyEnabled)
			$params[]="enabled='1'";
		if(count($params))
			$qry.=" WHERE ".implode(" AND ",$params);
		$qry.=" ORDER BY $s ASC";
		//echo $qry;exit;
		if($this->paging && !$prod_id){
			$rows=num_rows($qry);//heavy for big catalogs. change it
			$this->totalrows=$rows;//total products in category
			$this->numpages=ceil($rows/$this->pageSize);//number of pages in this cateogry
			$offset=floor(intval($pageno)*$this->pageSize);//offset of first record in page
			$qry.=" LIMIT $offset, $this->pageSize";
		}
		$res=exec_query($qry);
		if($this->paging && !$prod_id){
			$this->numrows=count($res);//the actual size of products on page
		}
		foreach($res as $i=>$row){
			foreach($lists as $k){
				$row[$k]=explode(",",$row[$k]);
			}
			if($pids=implode(",",$row[prod_ids])){//related products
				$qry="SELECT id,title FROM $this->pTable
					  WHERE find_in_set(id,'$pids')
					  AND enabled='1'";
				$row[related]=exec_query($qry);
			}
			foreach($this->imgPrefixes as $pfx){
				$upath=$this->getImgUrl($row[id], $pfx, "products");
				$dpath=$this->getImgDir($row[id], $pfx, "products");
				$row[images][$pfx]=$upath;
			}
			$res[$i]=$row;
		}
		return ($prod_id?$res[0]:$res);
	}
	function getAdminProductList(){
		$qry="SELECT p.id,p.title,concat(c.title,'/',p.title) mtitle
			 FROM $this->pTable p, $this->cTable c
			 WHERE find_in_set(c.id,p.cat_ids)
			 ORDER BY mtitle
		";
		return exec_query($qry);
	}
	function getOtherProductList($prod_id){
		$qry="SELECT p.id, p.title
		     FROM $this->pTable
			 WHERE id!='$prod_id'
			 ORDER BY title, cat_ids";
		return exec_query($qry);
	}
	function getNumProducts($cat_id){
		debug("STORE:->getNumProducts($cat_id)");
		if(!$cat_id)return 0;
		$qry="SELECT count(*)c FROM $this->pTable
			 WHERE find_in_set('$cat_id', cat_ids)";
		if($this->onlyEnabled)
			$qry.=" AND enabled='1'";
		$this->totalrows=exec_query($qry,1,"c");
		return $this->totalrows;
	}
	function getCategories($cat_id=0,$order="ordr"){
		debug("STORE:getCategories($cat_id)");
		//if cat_id supplied returns single row w/cateogry
		$qry="SELECT * FROM $this->cTable";
		$params=array();
		if($cat_id)
			$qry.=" WHERE id='$cat_id'";
		$qry.=" ORDER BY $order";
		$res=exec_query($qry,($cat_id?1:0));
		if($cat_id)$res=array($res);
		foreach($res as $i=>$row){
			$imgs=$this->getImages($row[id],"categories");
			if(count($imgs))
				$res[$i][images]=$imgs;
		}
		if($cat_id)$res=$res[0];
		return $res;
	}
	function getInventoryIds($prod_id){
		debug("STORE:->getInventoryIds($prod_id)");
		if(!$prod_id)return array();
		$qry="SELECT id i FROM $this->iTable WHERE product_id='$prod_id' ORDER BY ordr";
		return exec_query($qry,0,"i");
	}
	function getInventory($prod_id=0, $inv_id=0, $s="ordr",$desc=0){
		debug("STORE:->getInventory($prod_id,$inv_id,$s,$desc)");
		if(!$prod_id && !$inv_id)return array();
		$params=array();
		//product options to get
		foreach($this->paramFields as $invcol=>$prodcol){
			$colselect[]="p.$prodcol";
		}
		$colselect=implode(",",$colselect);
		//
		$qry="SELECT p.title, p.min_purchase_count, p.min_purchase_price,
			  $colselect, i.*
			  FROM $this->iTable i, $this->pTable p";
		$params[]="p.id=i.product_id";
		if($prod_id) $params[]="i.product_id='$prod_id'";
		if($inv_id)  $params[]="i.id='$inv_id'";
		if($this->onlyEnabled)$params[]="p.enabled='1'";
		$qry.=" WHERE ". implode(" AND ", $params);
		if($s)$qry.=" ORDER BY $s ".($desc?"DESC":"ASC");
		if($inv_id)$qry.=" LIMIT 1";
		$res=exec_query($qry);
		foreach($res as $i=>$row){
			$row[params]=array();
			foreach($this->paramFields as $valkey=>$namekey){
				if($row[$namekey] && $row[$valkey]){
					$row[params][ $row[$namekey] ]=$row[$valkey];
				}else{
					unset($row[$valkey]);
				}
				unset($row[$namekey]);
			}
			foreach($this->imgPrefixes as $pfx){
				$upath=$this->getImgUrl($row[id], $pfx, "inventory");
				$dpath=$this->getImgDir($row[id], $pfx, "inventory");
				if(is_file($dpath))
					$row[images][$pfx]=$upath;
			}
			$res[$i]=$row;
		}
		return ($inv_id?$res[0]:$res);
	}
	function getProductParams($prod_id,$paramName,$params=array()){
		debug("STORE:->getProductParams($prod_id,$paramName,$params)");
		if(!in_array($paramName, $this->_getTableKeys($this->iTable) )){
			return array();
		}
		$params[product_id]=$prod_id;
		$qry="SELECT id,retail, $paramName
			   FROM $this->iTable";
		$qry.=stack_query($params);
		$qry.=" GROUP BY $paramName";
		return exec_query($qry);
	}
	function getInventoryItem($inv_id){
		debug("STORE:->getInventoryItem($inv_id)");
		return $this->getInventory(0,$inv_id);
	}
	function getNumSkus($prod_id){
		debug("STORE:->getNumSkus($prod_id)");
		return count_rows($this->iTable, array(product_id=>$prod_id));
	}

	function getImgDir($prod_id,$prefix, $type="products"){
		//debug("STORE:->getImgDir($prod_id,$prefix,$type)");
		//type will be either products, categories, inventory
		return sprintf($this->imgPathFormat, $type, $prod_id, $prefix);
	}
	function getImgUrl($prod_id, $prefix, $type="products"){
		//debug("STORE:->getImgUrl($prod_id,$prefix,$type)");
		//type will be either products, categories, inventory
		global $D_R;
		$path=$this->getImgDir($prod_id, $prefix, $type);
		return substr($path, strlen($D_R));
	}
	function getImages($id,$type="products"){
		$ret=array();
		foreach($this->imgPrefixes as $pfx){
				$upath=$this->getImgUrl($id, $pfx, $type);
				$dpath=$this->getImgDir($id, $pfx, $type);
				if(is_file($dpath))
					$ret[$pfx]=$upath;
		}
		return $ret;
	}
	function getStockNotifyFlag($inv_id){
		debug("STORE:->getStockNotifyFlag($inv_id)");
		if(!$this->notifyParams[donotify])
			return 1;//if it's turned off act like it was already sent
		$qry="SELECT notified_stock ns FROM $this->iTable WHERE id='$inv_id'";
		return (exec_query($qry,1,"ns")?1:0);
	}

	function hasLowStock($inv_id){
		debug("STORE:->hasLowStock($inv_id)");
		if(!$this->notifyParams[dodecrement]){
			debug("STORE:->hasLowStock($inv_id):stock decrementing turned off. quitting");
			return 0;
		}
		$qry="SELECT count(*) c
			  FROM $this->iTable
			  WHERE id='$inv_id'
			  AND stock<=".$this->notifyParams[stockno];
		return exec_query($qry,1,"c");
	}
/*================= setter methods =================*/
	function setParameter($row,$param_id=0){
		debug("STORE:->setParameter($row,$param_id)");
		$this->_adminExit();
		$row=trim_arr($this->_cleanInsert($row,$this->paramTable),1);
		if(!count($row))return 0;
		if(!$param_id){
			return insert_query($this->paramTable,$row,1);
		}else{
			$changed=update_query($this->paramTable, $row, array(id=>$param_id));
			if($changed){//update the product table with new param name
				$newparam=$row[name];
				$oldparam=$this->getParameters($param_id);
				foreach($this->paramFields as $k=>$fieldname){
					//go through param${x}_name in prod table and update them with new name
					//god. these should be links
					$upd=array($fieldname=>$newparam);
					$cond=array($fieldname=>$oldparam);
					update_query($this->pTable, $upd, $cond);
				}
			}
		}
		return $param_id;
	}
	function setProduct($row,$prod_id=0){
		debug("STORE:->setProduct($row,$prod_id)");
		$this->_adminExit();
		$row=$this->_cleanInsert($row, $this->pTable);
		if(!count($row))return 0;
		if(!$prod_id){
			return insert_query($this->pTable, $row);
		}else{
			update_query($this->pTable, $row, array(id=>$prod_id));
			return $prod_id;
		}
	}

	function setCategory($row, $cat_id=0){
		debug("STORE:->setCategory($row,$cat_id)");
		$this->_adminExit();
		//if cat_id is set it will update the blog info
		$row=$this->_cleanInsert($row, $this->cTable);
		if(!count($row))return 0;
		if(!$cat_id){
			return insert_query($this->cTable, $row);
		}else{
			update_query($this->cTable, $row, array(id=>$cat_id));
			return $cat_id;
		}
	}
	function setInventory($row, $inv_id=0){
		debug("STORE:->setInventory($row,$inv_id)");
		$this->_adminExit();
		$row=$this->_cleanInsert($row, $this->iTable);
		$params=$this->notifyParams;
		if(!count($row))return 0;
		if(!$inv_id){
			return insert_query($this->iTable, $row);
		}else{
			if($params[donotify] && $row[stock]>$params[stockno]){
				debug("STORE:->setInventory($row,$inv_id):toggle off notification flag");
				$row[notified_stock]=0;
			}
			update_query($this->iTable, $row, array(id=>$inv_id));
			return $inv_id;
		}
	}
	function setDecrementedStock($inv_id,$q=1){
		$q=intval($q);
		debug("STORE:->setDecrementedStock($inv_id,$q)");
		//returns whether stock is low
		//quits if notifyParams[dodecrement] is false (turned off)
		if(!$this->notifyParams[dodecrement]){
			debug("STORE:->setDecrementedStock($inv_id,$q):store decrementing turned off. quit");
			return 0;
		}
		if(!$q)return 0;
		if(!$inv_id)return 0;
		$qry="UPDATE $this->iTable SET stock=(stock-$q) WHERE id='$inv_id'";
		exec_query_nores($qry);
		return $this->hasLowStock($inv_id);
	}
	function setStockNotifyFlag($inv_id,$flag=0){
		debug("STORE:->setStockNotifyFlag($inv_id,$flag=0)");
		if($this->notifyParams[donotify]){
			update_query($this->iTable,array(notified_stock=>$flag?1:0),array(id=>$inv_id));
		}else{
			debug("STORE:->setStockNotifyFlag:notification turned off");
		}
	}
/*=============== image creation ================*/
	function createCategoryImages($cat_id, $files_obj=0){
		debug("STORE:createCategoyImages($cat_id,$files_obj");
		$this->_adminExit();
		if(!$files_obj[size] || !stristr($files_obj[type],"jpeg") || !$cat_id){
			return 0;
		}
		include_once(dirname(__FILE__)."/_gallery.php");
		foreach($this->images as $prefix=>$wh){
			list($w,$h)=$wh;
			$path=$this->getImgDir($cat_id, $prefix, "categories");
			resizeImg($w,$h, $files_obj[tmp_name], $path);
		}
		return 1;
	}
	function createProductImages($prod_id,$files_obj){
		debug("STORE:->createProcutImages($prod_id, $files_obj)");
		$this->_adminExit();
		if(!$files_obj[size] || !stristr($files_obj[type],"jpeg") || !$prod_id){
			return 0;
		}
		include_once(dirname(__FILE__)."/_gallery.php");
		foreach($this->images as $prefix=>$wh){
			list($w,$h)=$wh;
			$path=$this->getImgDir($prod_id, $prefix, "products");
			debug("createProductImages($prod_id):creating $path");
			resizeImg($w,$h, $files_obj[tmp_name], $path);
		}
		return 1;
	}
	function createInventoryImages($inv_id, $files_obj){
		debug("STORE:->createInventoryImages($inv_id, $files_obj)");
		//just creates images for a single inventory id
		$this->_adminExit();
		if(!$files_obj[size] || !stristr($files_obj[type],"jpeg") || !$inv_id){
			debug("STORE:->createInventoryImages:badimage: size:${files_obj[size]} id:$inv_id");
			return 0;
		}
		include_once(dirname(__FILE__)."/_gallery.php");
		foreach($this->images as $prefix=>$wh){
			list($w,$h)=$wh;
			$path=$this->getImgDir($inv_id, $prefix, "inventory");
			resizeImg($w,$h, $files_obj[tmp_name], $path);
		}
		return 1;
	}

	/*========= deletion methods ===============*/
	function deleteParameter($param_id){
		debug("STORE:->deleteParameter($param_id)");
		$this->_adminExit();
		if(!$param_id)return 0;
		/*
		//rename parameter to "-" in product table so no removals are done
		$param=exec_query("SELECT name n FROM $this->paramTable WHERE id='$param_id'"1,"n");
		foreach($this->paramFields as $i=>$field){
			update_query($this->pTable,array($field=>"-"),array($field=>$param));
		}
		*/
		del_query($this->paramTable,"id",$param_id,1);
	}
	function deleteProduct($prod_id){
		debug("STORE:->deleteProduct($prod_id)");
		$this->_adminExit();
		if(!$prod_id)return 0;
		$prod=$this->getProducts($prod_id);
		if(!count($prod))return 0;
		$this->deleteAllInventory($prod_id);
		$this->deleteProductImages($prod_id);
		del_query($this->pTable,"id",$prod_id, 1);
	}
	function deleteProductImages($prod_id, $prefix=0){
		debug("STORE:->deleteProductImages($prod_id,$prefix)");
		$this->_adminExit();
		if(!$prod_id)return;
		if($prefix && in_array($prefix,$this->imgPrefixes) ){
			munlink( $this->getImgDir($prod_id, $prefix, "products") );
			return;
		}
		foreach($this->imgPrefixes as $pfx){
			munlink( $this->getImgDir($prod_id, $pfx, "products") );
		}
	}
	function deleteInventoryImages($inv_id, $prefix=0){
		debug("STORE:->deleteInventoryImages($inv_id,$prefix)");
		$this->_adminExit();
		if(!$inv_id)return;
		if($prefix && in_array($prefix,$this->imgPrefixes) ){
			munlink( $this->getImgDir($inv_id, $prefix, "inventory") );
			return;
		}
		foreach($this->imgPrefixes as $pfx){
			munlink( $this->getImgDir($inv_id, $pfx, "inventory") );
		}
	}
	function deleteAllInventoryImages($prod_id){
		debug("STORE:->deleteAllInventoryImages($prod_id)");
		$this->_adminExit();
		if(!$prod_id)return;
		foreach($this->getInventoryIds($prod_id) as $inv_id){
			$this->deleteInventoryImages($inv_id);
		}
	}
	function deleteCategoryImages($cat_id, $prefix=0){
		debug("STORE:->deleteCategoryImages($cat_id,$prefix)");
		$this->_adminExit();
		if(!$cat_id)return;
		if($prefix && in_array($cat_id,$this->imgPrefixes) ){
			munlink( $this->getImgDir($cat_id, $prefix, "categories") );
			return;
		}
		foreach($this->imgPrefixes as $pfx){
			munlink( $this->getImgDir($cat_id, $pfx, "categories") );
		}
	}
	function deleteInventoryItem($inv_id){
		debug("STORE:->deleteInventoryItem($inv_id)");
		$this->_adminExit();
		if(!$inv_id)return;
		$this->deleteInventoryImages($inv_id);
		del_query($this->iTable,"id", $inv_id, 1);
	}
	function deleteAllInventory($prod_id){
		debug("STORE:->deleteAllInventory($prod_id)");
		$this->_adminExit();
		if(!$prod_id)return;
		$this->deleteAllInventoryImages($prod_id);
		del_query($this->iTable, "product_id", $prod_id, 1);
	}

	function deleteCategory($cat_id){
		debug("STORE:->deleteCategory($cat_id)");
		$this->_adminExit();
		if(!$cat_id)return;
		del_from_set($this->pTable, "id", "cat_ids", $cat_id);
		del_query($this->cTable, "id", $cat_id, 1);
	}

	/*===================== validation methods ===============*/
	function isValidProductId($id){
		debug("STORE:->isValidProductId($id)");
		if(!$id)return 0;
		return count_rows($this->pTable, array(id=>$id));
	}
	function isValidInventoryId($id){
		debug("STORE:->isValidInventoryId($id)");
		if(!$id)return 0;
		return count_rows($this->iTable, array(id=>$id));
	}
	function isValidCategoryId($id){
		debug("STORE:->isValidCategoryId($id)");
		if(!$id)return 0;
		return count_rows($this->cTable, array(id=>$id));
	}

	/*===================== helpers ===============*/
	function _cleanInsert($row, $tablename){
		debug("STORE:->_cleanInsert($row,$tablename)");
		//if $tablename isn't defined here it will completely empty the row
		//otherwise it will strip invalid keys from $row
		$keys=$this->_getTableKeys($tablename);
		if(!count($keys) || !is_array($row) ){
			//either tables doesn't exist or invalid input
			return array();
		}
		foreach($row as $field=>$val){
			if(!in_array($field, $keys)){
				unset($row[$field]);
			}
		}
		return $row;
	}
	function _getTableKeys($tablename){
		$tables=array(
			$this->pTable=>qw("ordr cat_ids prod_ids param1_name param2_name param3_name title blurb description  min_purchase_count min_purchase_price enabled featured"),
			$this->cTable=>qw("ordr title description"),
			$this->iTable=>qw("product_id ordr sku stock weight param1 param2 param3 retail wholesale description notified_stock"),
			$this->paramTable=>qw("name"),
			$this->oTable=>qw("ip trans_id trans_msg trans_code was_shipped
						b_fname b_lname b_email b_phone
						b_address b_address2 b_city b_state b_zip b_country
						s_fname s_lname s_email s_phone s_address
						s_address2 s_city s_state s_zip s_country cc_type
						cc_num cc_expiry cc_cvv2 shipping_type
						shipping_track shipping_charge shipping_weight
						date_created date_modified tax_rate tax_total
						discount subtotal total order_comments order_code
						order_summary admin_comments status")
		);
		if(!$tables[$tablename]){
			return array();
		}
		return $tables[$tablename];
	}
	function _adminExit($msg="Only administrators can do this."){
		if(!$this->admin){
			exit($msg);
		}
	}
	function _getSetProp($name,$value=""){
		if(!$this->$name && $value){
			$this->$name=$value;
		}
		return $this->$name;
	}
}//end Store{}
/*==================================================================
================ display layer =====================================
====================================================================*/

class StoreDisplay extends Store{
 	function StoreDisplay(){
		$this->Store();
		global $prod_id, $cat_id,$inv_id,$p;
		$this->cat_id=$cat_id;
		$this->prod_id=$prod_id;
		$this->inv_id=$inv_id;//pointer as we may want to modify it.
		$this->page=intval($p);
		$this->product=array();
		$this->category=array();
		$this->_setDefaults();
	}
	function _setDefaults(){
		$this->paging=1;
		$this->onlyEnabled=1;
		$this->category=$this->_getDefaultCategory();
		$this->cat_id=$this->category[id];
		$this->_setPagingVars();
		$this->product=$this->_getDefaultProduct();
		$this->prod_id=$this->product[id];
		$this->inventory=$this->_getDefaultInventory();
		if(count($this->inventory)==1 && !$this->inv_id){
			$this->inv_id=$this->inventory[0][id];
		}
		$this->hasProducts=count($this->product);

	}
	function _getCategories(){
		//top-level categories with nested "subcategory" keys
		$cats=$this->_getSubCats( $this->getCategories() );
		foreach($cats as $i=>$cat){
		   $cat_ids=extract_key($cats[$cat[id]], "id");
			if(in_array($this->cat_id, $cat_ids) || $this->cat_id==$cat[id]){
				$cats[$i][on]=1;
				break;
			}
		}
		return $cats;
	}
	function _getSubCats($cats, $subcat=""){//this never hits the database
		$ret=array();
		$subcat=$subcat[title];
		$sep=($subcat?"/":"");
		foreach($cats as $cat){
			$curdir=catbasename($cat[title]);
			$cat[name]=$curdir;
			if("$subcat$sep$curdir"==$cat[title]){//found a subcategory
				$ret[$cat[id]]=$cat;
				$ret[$cat[id]][subcategories]=$this->_getSubCats($cats, $cat);
			}
		}
		return $ret;
	}

	function _getProducts(){
		$prods=$this->getProducts(0,$this->cat_id, $this->page/*,"ordr"*/);
		foreach($prods as $i=>$prod){
			if($prod[id]==$this->prod_id){
				$prods[$i][on]=1;
				break;
			}
		}
		return $prods;
	}
	function catHasProducts($cat_id){
		debug("STOREDISPLAY:->catHasProducts($cat_id)");
		if(!$cat_id)
			return 0;
		$qry="SELECT count(*) c
			FROM $this->pTable
			WHERE enabled='1'
			AND find_in_set(cat_ids,'$cat_id')";
		return exec_query($qry,1,"c");
	}
	function displayCategories($tmplpath){
		debug("STOREDISPLAY:->displayCategories($tmplpath)");
		global $PHP_SELF;
		$cat_id=$this->cat_id;
		$data=$this->_getCategories();
		lose_keys($data,qw("subcategories"));
		foreach($data as $i=>$cat){
			unset($data[$i][description]);
			$data[$i]["link"]=$PHP_SELF.qsa(array(cat_id=>$cat[id]),"*");
		}
		unset($cat);
		include($tmplpath);
	}
	function displaySubcategories($tmplpath){
		debug("STOREDISPLAY->:displaySubcategories($tmplpath)");
		global $PHP_SELF;
		$cat_id=$this->cat_id;
		$data=$this->_getCategories();
		foreach($data as $i=>$cat){
			unset($data[$i][description]);
			if($cat[on]){
				if(count($cat[subcategories])){
					$subcategories=$cat[subcategories];
					//break up title into category names. get category ids from each name
					$selectedcats=array();
					$title=find_by_key($subcategories,"id",$cat_id);
					$title=$title[title];
					$cats=getcatdirs($title);
					array_pop($cats);//ignore last dir cuz it's the current one
					foreach($cats as $c){
						$tobj=find_by_key($subcategories,"title",$c);
						$selectedcats[]=$tobj[id];
					}
					unset($title);unset($cats);unset($tobj);
				}
			}
			$data[$i]["link"]=$PHP_SELF.qsa(array(cat_id=>$cat[id]),"*");
		}
		unset($cat);
		include($tmplpath);
	}
	function displayPaging($tmplpath){
		global $PHP_SELF;
		$this->_setPagingVars();
		$p        = intval($this->page);
		$cat_id   = $this->cat_id;
		$numpages = $this->numpages;
		$data     = array();
		if($numpages<=1){
			return;
		}
		$data=array();
		$data[links]=array();
		if($p)
			$data["prev"]=$PHP_SELF.qsa(array(p=>$p-1,cat_id=>$cat_id),"*");
		if($p < ($numpages-1) )
			$data["next"]=$PHP_SELF.qsa(array(p=>$p+1,cat_id=>$cat_id),"*");
		$data["links"]=array();
		foreach(range(0,$numpages-1) as $i){
			$link=$PHP_SELF.qsa(array(p=>$i,cat_id=>$cat_id),"*");
			$data["links"][$link]=($i+1);
		}
		unset($link);
		include($tmplpath);
	}
	function displayThumbs($tmplpath){
		global $PHP_SELF,$D_R;
		$data=$this->_getProducts();
		foreach($data as $i=>$row){
			$qs=array(prod_id=>$row[id],p=>$this->page,cat_id=>$this->cat_id);
			$data[$i]["link"]=$PHP_SELF.qsa($qs,"*");
			if($this->prod_id==$row[id])
				$data[$i][on]=1;
			if(!is_file($D_R.$row[images][tn]))
				$data[$i][images][tn]=$this->imgPath."/unavail-tn.jpg";
		}
		include($tmplpath);
	}
	function displayProduct($tmplpath){
		global $inv_id,$D_R;
	//this method sets global $inv_id and in turn $this->inv_id
		$data=$this->product;
		$data[inventory]=$this->inventory;
		$inv=array();
		foreach($data[inventory] as $row){
			$inv[$row[id]]=$row;
		}
		if(count($inv)==1){
			$inv_id=$inv[key($inv)][id];//pointed to by $this->inv_id
		}
		$data[options]=$this->getProductOptions($data[inventory]);
		$data[inventory]=$inv;
		$data[inv_id]=$this->inv_id;
		if($inv[$this->inv_id][images]){
			$data[images]=$inv[$this->inv_id][images];
		}
		if($inv[$this->inv_id][description]){
			$data[addl_descr]=$inv[$this->inv_id][description];
		}
		if(!is_file($D_R.$data[images][big]))
			$data[images][big]=$this->imgPath."/unavail-big.jpg";
		if($price=$inv[$this->inv_id][retail]){
			$data[price]='$'.dollars($price);
		}
		$data[hasProducts]=$this->hasProducts;
		unset($inv);
		$data[paramLength]=$this->paramLength;
		include($tmplpath);
	}
	function getProductOptions($inventory){
		//gets longest string PER COLUMN
		//in inventory and creates a list where values are all equal lengths
		$options=array();
		$lens=array();
		foreach($inventory as $prod){
			$params=$prod[params];
			$params["Price"]='$'.dollars($prod[retail]);
			$k=$prod[id];
			if(!$first){
				$keys=array_map("ucwords",array_keys($params));
				$options[0]=$keys;
				foreach($keys as $i=>$v){ $lens[$i][]=strlen($v); }
				$first=1;
			}
			$params=array_values($params);
			foreach($params as $i=>$v){ $lens[$i][]=strlen($v); }
		 	$options[$k]=$params;
		}
		if(count($options)<3)
			return array();
		foreach($options as $k=>$opts){
			foreach($opts as $i=>$opt){ $opts[$i]=str_pad($opt,max($lens[$i]),"`" );  }
			$opts=str_replace("`","&nbsp;", implode("|",$opts));
			$options[$k]=$opts;
		}
		return $options;
	}
	function displayAllProducts($tmplpath){
		global $PHP_SELF,$D_R,$STORE_TEMPLATE_DIR;
		$data=$this->_getProducts();
		foreach($data as $i=>$row){
			$qs=array(prod_id=>$row[id],p=>$this->page,cat_id=>$this->cat_id);
			$data[$i]["link"]=$PHP_SELF.qsa($qs,"*");
			if($this->prod_id==$row[id])
				$data[$i][on]=1;
			/*if(!is_file($D_R.$row[images][tn]))
				$data[$i][images][tn]=$this->imgPath."/unavail-tn.jpg";
			if(!is_file($D_R.$row[images][big]))
				$data[$i][images][big]=$this->imgPath."/unavail.jpg";
			*/
			$data[$i][inventory]=$this->getInventory($row[id]);
			if($this->notifyParams[stockno]){
				foreach($data[$i][inventory] as $j=>$row){
					$data[$i][inventory][$j][nostock]=($row[stock]<1?1:0);
				}
			}
		}
		unset($row);unset($srow);
		include($tmplpath);
	}
/*============= defaults =====================*/
	function _getDefaultCategory(){
		if(!$this->cat_id && $this->prod_id){
		//derive category from first category $this->prod_id--default product
			$qry="SELECT cat_ids FROM $this->pTable WHERE id='$this->prod_id'";
			$cat_ids=explode(",",exec_query($qry,1,"cat_ids"));
			if($cat_ids[0])
				$this->cat_id=$cat_ids[0];
		}
		$ret=$this->getCategories($this->cat_id);
		if(!$ret[id])
			$ret=$ret[0];
		return $ret;
	}
	function _getDefaultProduct(){
		//has to have this->cat_id
		if(!($prod_id=$this->prod_id)){
			$qry="SELECT id FROM $this->pTable
				  WHERE find_in_set('$this->cat_id',cat_ids)
				  AND enabled='1' ORDER BY ordr";
			if($this->offset){
				$qry.=" LIMIT $this->offset, 1";//fock
			}
			$prod_id=exec_query($qry,1,"id");
		}
		$product=$this->getProducts($prod_id,$this->cat_id, $this->page/*,"ordr"*/);
		foreach(range(1,$this->paramLength) as $i){
			if($pname=$product["param${i}_name"])
				$product[parameters]["param${i}_name"]=$pname;
			else
				unset($product["param$[i}_name"]);
		}
		return $product;
	}
	function _getDefaultInventory(){
		return $this->getInventory($this->prod_id,0);
	}
	function _setPagingVars(){
		if(!$this->totalrows)
			$this->getNumProducts($this->cat_id);//cats the total #products in category
		$this->numpages=ceil($this->totalrows/$this->pageSize);
		if($this->page)
			$this->offset=$this->page*$this->pageSize;
	}
 }//end StoreDisplay{}

/*==================================================================
================ data layer of cart ================================
====================================================================*/
 class Cart extends Store{
	function Cart(){
		global $_SESSION,$STORE_TAX_STATE,$STORE_TAX_RATE;
		global $STORE_FROM_ZIP, $STORE_SHIP_RATES,$STORE_SHIP_RANGES;
		global $STORE_TRANS_MOD, $STORE_GIFT_STR;
		session_start();
		$this->Store();
		$this->sessName="CART";
		$this->_data=munserial($_SESSION[$this->sessName]);
		$this->contents=array();
		$this->num_items=0;
		$this->total=0;
		$this->gTotal=0;
		$this->taxableState=$STORE_TAX_STATE;
		$this->taxableRate=$STORE_TAX_RATE;
		$this->fromZip=$STORE_FROM_ZIP;
		$this->shipRanges=$STORE_SHIP_RANGES;//base shipping rate gathered from this
		$this->shipRates=$STORE_SHIP_RATES;//added to base charges. uses shipping types
		$this->transMod=$STORE_TRANS_MOD;
		$this->shipping=0;
		$this->userdata=array();
		$this->_setDefaults();
		$this->giftStr=$STORE_GIFT_STR;
	}
	function reInit($enc_serialized_data=""){
		set_sess_vars($this->sessName,$enc_serialized_data);
/*		global $_SESSION;
		session_register($this->sessName);
		$_SESSION[$this->sessName]=$enc_serialized_data;
		//for some reason the secure isn't setting session shit
*/		$this->Cart();
	}
	function _setDefaults(){
		if(!is_array($this->_data))
			$this->_data=array();
		if(!is_array($this->_data["contents"])){
			$this->_data["contents"]=array();
		}
		$this->contents=$this->_data["contents"];
		$this->_data["total"]=array_sum(extract_key($this->contents,"sum"));
			$this->total=round($this->_data["total"],2);
		$this->_data["num_items"]=array_sum(extract_key($this->contents,"quantity"));
			$this->num_items=$this->_data["num_items"];
		$this->tax=round($this->_data["tax"],2);
		$this->taxAmount=round($this->_data["taxAmount"],2);
		$this->shipping=$this->_data["shipping"];
		$this->gTotal=$this->_data["gTotal"];
		$this->userdata=$this->_data["userdata"];
		$this->haveUserData=count($this->_data["userdata"]);
	}

	function setUserData($data){
		//should be a hash with fields mapping to db
		//after this data's collected the tax,shipping and final totals can be run
		$this->_data["userdata"]=$data;
		$this->_setTax();
		$this->_setShipping();
		$this->_setGrandTotal();
		$this->save();
	}
	function _setTax(){
		//assumes _data[userdata] is set
		if(!@count($this->_data["userdata"]))return;
		if(lc($this->_data["userdata"]["b_state"])==lc($this->taxableState)){
			$this->_data["tax"]=$this->taxableRate;
			$this->_data["taxRate"]=$this->_data["taxRate"]=$this->taxableRate;
		}else{
			$this->_data["tax"]=0;
			$this->_data["taxRate"]=0;
		}
		$this->_data["taxAmount"]= round($this->_data["tax"] * $this->_data["total"],2);
	}
	function _setShipping(){
		//grab shipping charge based on total items in cart

		//virtual product support
		$gitem=$this->getGiftSubsItem();
		$old_total=$this->_data["total"];

		if(count($gitem)){
			if($this->hasGiftOnly()){
				//if they only have the gift subscription in the cart don't calc shipping
				$this->_data["shipping"]=0;
				$this->_data["shippingRate"]=0;
				$this->_data["userdata"]["shipping_type"]="";
				return;
			}else{
				$old_total-=$gitem[price];
			}
		}
		foreach($this->shipRanges as $order_total=>$rate){
			if($old_total<=$order_total){
				$this->_data["shipping"]=round($rate,2);
				$this->_data["shippingRate"]=round($rate,2);
				break;
			}
		}
		//add shipping	charges based on delivery method
		$typecharge=$this->shipRates[$this->_data["userdata"]["shipping_type"]];
		$this->_data["shipping"]+=$typecharge;
		$this->_data["shipping"]=round($this->_data["shipping"],2);
	}
	function _setGrandTotal(){
		$tmt=$this->_data["taxAmount"];
		$smt=$this->_data["shipping"];
		$tot=$this->_data["total"];
		$this->_data["gTotal"]=($tmt + $smt + $tot );
	}

	function add($inv_id,$qty=1){
		if(!$this->isValidInventoryId($inv_id)){
			debug("CART:->add:invalid inv id $inv_id");
			$this->save();
			return;
		}
		if(!$qty){
			debug("CART:->add:remove $inv_id");
			unset($this->contents[$inv_id]);
			$this->save();
			return;
		}
		if(!($inv_item=$this->contents[$inv_id])){
			debug("CART:->add:add $inv_id");
			$inv_item=$this->getInventoryItem($inv_id);
			$inv_item["price"]=$inv_item["retail"];
			unset($inv_item["retail"]);
		}
		if($inv_item["min_purchase_count"]>$qty)
			$qty=$inv_item["min_purchase_count"];
		$inv_item["quantity"]=$qty;
		$inv_item["sum"]=floatval($qty*$inv_item["price"]);
		$this->contents[$inv_id]=$inv_item;
		$this->save();
	}
	function remove($inv_id){
		debug("CART:->remove:del $inv_id");
		$this->add($inv_id,0);
	}
	function removeAll(){
		debug("CART:->removeAll");
		$this->_data["contents"]=array();
		$this->_data["born_at"]=time();//reset the birthdate of cart
		$this->save();
		$this->CartDisplay();
	}
	function kill(){
		debug("CART:->kill");
		$this->_data["contents"]=array();
		$this->_data["userdata"]=array();
		$this->_data["gTotal"]=$this->_data["shipping"]=$this->_data["tax"]=$this->_data["taxAmount"]=0;
		$this->save();
		$this->CartDisplay();
	}
	function save(){
		global $_SESSION;//so fucking stupid. don't know what this shit is breaking
		debug("CART:->save");
		set_sess_vars($this->sessName,mserial($this->_data));
/*		session_register($this->sessName);
		$_SESSION[$this->sessName]=mserial($this->_data);*/
	}
	function prodInCart($prod_id){
		return in_array($prod_id, extract_key($this->contents,"product_id"));
	}
	function titleInCart($title){
		if(!$title)return 0;
		foreach($this->contents as $row){
			if(stristr($row[title],$title))
				return 1;
		}
		return 0;
	}
	function invInCart($inv_id){
		return in_array($inv_id, extract_key($this->contents,"id"));
	}
	function getQuantity($inv_id){
		return intval($this->contents[$inv_id][quantity]);
	}
	function getRequiredUserFields($filter=0,$supplied_data=array()){
		$list=array(
			"b"=>qw("b_fname b_lname b_address b_city b_email
					 b_country b_phone"),
			"s"=>qw("s_fname s_lname s_address s_city s_country"),
			"cc"=>qw("cc_type cc_expire"),
			"ship"=>qw("shipping_type")
		);
		if(@count($supplied_data)){//require state for US
			if(lc($supplied_data[b_country])=="united states")
				$list[b][]="b_state";
			if(lc($supplied_data[s_country])=="united states")
				$list[s][]="s_state";
		}
		if($filter)
			return $list[$filter];
		return array_merge($list[s],$list[b],$list[cc], $list[ship]);
	}
	function getOrderId($prefix="MV_"){//generates an MV-specific order id
		$charlen=3;
		$ret=$prefix.time()."-";
		$chrs="a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9";
		$chars=array_unique(array_merge(qw($chrs),qw(uc($chrs))));
		//seed the seed
		srand(getmicrotime());
		for($i=0;$i<$charlen;$i++){
			$idx=rand($i,count($chars)-1);
			$ret.=$chars[$idx];
			unset($chars[$idx]);
			$chars=array_values($chars);//reset character list
		}
		return $ret;
	}
}//end Cart{}

class CartDisplay extends Cart{
	function CartDisplay(){
		$this->Cart();
		global $inv_id, $prod_id, $cat_id, $p,$STORE_REDIR,$STORE_CART,$STORE_CHECKOUT_URL;
		global $STORE_CONFIRM_FROM, $STORE_CONFIRM_EMAIL,$STORE_CONFIRM_SUBJECT;
		global $STORE_THANKS_URL,$STORE_MAX_AGE,$STORE_FULFILLMENTCTR_PARAMS;
		$this->globals=array(
			"inv_id"  =>&$inv_id,
			"prod_id" => &$prod_id,
			"cat_id" => &$cat_id,
			"p" => &$p, "page" => &$p
		);
		$this->maxAge=$STORE_MAX_AGE;
		$this->fulfillParams=$STORE_FULFILLMENTCTR_PARAMS;
		$this->confirmFrom=$STORE_CONFIRM_FROM;
		$this->confirmSubject=$STORE_CONFIRM_SUBJECT;
		$this->confirmTemplate=$STORE_CONFIRM_EMAIL;
		$this->checkouturl=$STORE_CHECKOUT_URL;
		$this->thanksurl=$STORE_THANKS_URL;
		$this->redirect=$STORE_REDIR;
		$this->cartpage=$STORE_CART."?refer=".refer(1);
		$this->carturl=$STORE_CART;//this is if u don't need redirector
		$this->msgItemsInCart="$this->num_items item(s) in cart";
		$this->msgEmptyCart="0 items in cart";

		$this->killStaleCart();//kill the cart if it's too old

		$this->last_product=$this->_data["last_product"];
		if($this->num_items){
			//$this->msgCart="$this->msgItemsInCart ".hrefs($this->carturl,"cart");
			//$this->msgCart=hrefs($this->carturl,"<span style=\"color:#FFF; text-decoration:none\">$this->msgItemsInCart</span>");
			$this->msgCart="<a href=\"".$this->carturl."\" style=\"color:#FFF; text-decoration:none\">".$this->msgItemsInCart."</a>";
		}else{
			$this->msgCart=$this->msgEmptyCart;
		}
		if($this->invInCart($inv_id)){
			$this->msgIsInCart="This item is in your cart";
		}
	}

	function setLastProductUrl($url=0){
		if(!$url){
			global $UNSECURE_URL, $REQUEST_URI;
			$parts=parse_url($UNSECURE_URL);
			$userpass="";
			if($parts[user])$userpass=$parts[user];
			if($parts[pass])$userpass.=":".$parts[pass];
			if($userpass)$userpass.="@";
			$url="http://$userpass${parts[host]}:${parts[port]}$REQUEST_URI";
			//$url="$UNSECURE_URL$REQUEST_URI";
		}
		$this->_data["last_product"]=$url;
		$this->save();
	}
	//minyanville gift subscription-specific logic
	function hasGiftSubs(){
		return $this->titleInCart($this->giftStr);
	}
	function hasGiftOnly(){
		//there is a gift subs and there's only one product in the cart
		//one product designated by unique items. can have multiple quantity
		return ($this->hasGiftSubs()&&count($this->contents)===1)?1:0;
	}
	function isGiftSubsTitle($title){
		return stristr($title,$this->giftStr);
	}
	function getGiftSubsItem(){
		foreach($this->contents as $id=>$row){
			if( $this->isGiftSubsTitle($row[title]) ){
				return $row;
			}
		}
		return array();
	}
	function chunkGiftCodes(){
		//this makes sure the number of gift codes matches the quantity in the order
		$item=$this->getGiftSubsItem();
		$codes=$item[codes];
		if(!count($codes))return;
		$codes=array_chunk($codes, $item[quantity] );
		$item[codes]=$codes[0];
		$this->contents[$item[id]]=$item;
		$this->save();
	}
	function generateGiftCodes(){
		$this->chunkGiftCodes();
		$item=$this->getGiftSubsItem();
		if(!count($item))
			return 0;
		for($i=0;$i<$item[quantity];$i++){
			if($item[codes][$i][key1])//already generated code
				continue;
			list($key1, $key2)=createMVGiftCode();
			$item[codes][$i][key1]=$key1;
			$item[codes][$i][key2]=$key2;
		}
		$this->contents[$item[id]]=$item;
		$this->save();
		return 1;
	}
	function insertGiftCodes(){
		$this->chunkGiftCodes();
		$item=$this->getGiftSubsItem();
		$item=$item[codes];
		if(!is_array($item))
			return;
		foreach($item  as $row){
			$ins=array(reserved=>1, key1=>$row[key1],key2=>$row[key2]);
			insert_query("subscription_keys",$ins);
		}
	}

	function saveGiftCodeMails($maillist=array()){
		$this->chunkGiftCodes();
		$item=$this->getGiftSubsItem();
		$tmp=array();
		for($i=0;$i<$item[quantity];$i++){
			$item[codes][$i][email]=$maillist[$i];
		}
		$this->contents[$item[id]]=$item;
		$this->save();
		if(count($maillist)< count($item[codes])){
			return 0;
		}
		return 1;
	}
	function sendGiftCodeMails(){
		global $D_R;
		$this->chunkGiftCodes();
		$item=$this->getGiftSubsItem();
		$item=$item[codes];
		if(!is_array($item))
			return;
		$tmpl="$D_R/emails/_eml_receive_gift.htm";
		$udata=$this->_data[userdata];
		$fromname="${udata[b_fname]} ${udata[b_lname]}";
		$fromemail="$fromname <${udata[email]}>";
		$subject="$fromname has purchased you a gift subscription";
		foreach($item as $row){
			$params=array(email=>$row[email],key1=>$row[key1],key2=>$row[key2]);
			$body=include2str($tmpl,$params,1);
			mymail($row[email],$fromemail,$subject,$body);
		}
	}
	function doGiftSubsTransaction(){
		if(!$this->hasGiftSubs())
			return;
		$this->insertGiftCodes();
		$this->sendGiftCodeMails();
	}
	//end gift subscription stuff
	function killStaleCart(){
		return;
		if(!$this->_data[born_at])
			$this->_data[born_at]=time();
		$this->_data[last_viewed]=time();
		if(!num_items){
			//empty cart don't bother going thru kill process
			return;
		}
		if(($this->_data[last_viewed]-$this->_data[born_at])>$this->maxAge){
			echo "Your shopping session has expired... exiting";
			$exitparams=array(
				action=>"removeall",
				refer=>base64_encode($this->cartUrl)
			);
			location($this->redirect.qsa($exitparams));
			exit;
		}
	}

	function displayShopper($tmplpath,$inv_id=0){
		$data=$this->globals;
		if($inv_id)$data[inv_id]=$inv_id;
		$data[redirect]=$this->redirect;
		$data[msgItemsInCart]=$this->msgItemsInCart;
		$data[msgEmptyCart]=$this->msgEmtpyCart;
		$data[msgIsInCart]=$this->msgIsInCart;
		$data[isInCart]=$this->invInCart($data[inv_id]);//this is a hack b/c doesn't use ->inv_id
		$data[num_items]=$this->num_items;
		$data[addable]=($data[inv_id]?1:0);
		$data[cartpage]=$this->cartpage;
		$data[quantity]=$this->contents[$data[inv_id]][quantity];
		if(!$data[quantity])$data[quantity]=1;
		include($tmplpath);
	}
	function displayShopper2($tmplpath,$inventory){
	 //displays inventory ids as checkboxes ability to add multiple items
	 	global $REQUEST_URI;
		if(!count($inventory))return;
		$data=$this->globals;
		foreach($inventory as $i=>$row){
			$row[isInCart]=$this->invInCart($row[id]);
			$row[quantity]=$this->contents[$row[id]][quantity];
			$row[price]=dollars($row[retail]);
			$inventory[$i]=$row;
		}
		$data[inventory]=$inventory;
		$data[redirect]=$this->redirect;
		$data[cartpage]=$this->cartpage;
		$data[prod_id]=$inventory[0][product_id];
		$data[num_items]=$this->num_items;
		include($tmplpath);
	}
	function getCartData(){//grabs formattable data for cart page;
		$data=$this->_data;
		$conts=$data[contents];
		foreach($conts as $inv_id=>$row){
			$row=trim_arr($row,1);
			if(intval($row[min_purchase_count])>1){
				$row[title].="<br><i>(Min. purchase is ${row[min_purchase_count]} items)</i>";
			}
			if(count($row[params])){
				$row[title].="<br>";
				foreach($row[params] as $k=>$v){
					$row[title].=" $k: <b>$v</b>";
				}
			}
			unset($row[params]);
			$conts[$inv_id]=$row;
		}
		return $data;
	}
	function acceptCartData(){
		//pull cart out of post
		global $_POST;
		if(trim($_POST[cartdata])){
			debug("CARTDISPLAY:->arriveAtCheckOut()");
			//var sent on original post to checkout page.
			$mserial_data=$_POST[cartdata];
			$this->reInit($mserial_data);
			$this->CartDisplay();
		}
	}
	function sendCartData($url){
		//flattens out cart and posts it with a form
		$persist=array(cartdata=>mserial($this->_data) );
		persist($url,$persist);
		exit();
	}
	function goToCheckOut(){
		$this->sendCartData($this->checkouturl);
	}
	function sendCartDataRedirect($url){//don't encode the url!!
		$params=array(
			action=>"accept",
			refer=>base64_encode($url)
		);
		$this->sendCartData($this->redirect.qsa($params));
	}
	function createSummaryString(){
		$contents=$this->getCartData();
		$udata=$contents[userdata];
		$contents=$contents[contents];
		$str="";
		foreach($contents as $row){
			$title=str_replace("<br>"," ",$row[title]);
			$title=strip_tags($title);
			$title=unhtmlentities($title);
			$gc="";
			if($this->isGiftSubsTitle($row[title])){
				$gc="\n";
				foreach($row[codes] as $code){
					$gc.="EntryCode:${code[key1]} PassCode:${code[key2]} Recip: ${code[email]}\n";
				}
			}
			$srow=array(
				"Product: $title $gc",
				"SKU: ${row[sku]}",
				"Quantity: ${row[quantity]}",
				'Unit Price: $'.dollars($row[price]),
				'Price: $'.dollars($row[sum]),
			);

			$srow[]=str_repeat("=",10)."\n\n";
			$str.=implode("\n",$srow);
		}
		$summary=array(
			"Gift Card Message or General Instructions:\n".strip($udata[order_comments])."\n\n",
			'Subtotal: $'.dollars($this->total),
			'Tax: $'.dollars($this->taxAmount)." @ ".($this->tax*100) ."%",
			'Shipping: $'.dollars($this->shipping)." VIA {$this->userdata[shipping_type]}",
			'Total: $'.dollars($this->gTotal),
			'Billed To: '.$udata[cc_type]." number ".$this->hideCCNum($udata[cc_num])
		);
		if($this->order_id)
			$summary[]="Minyanville Order ID#: $this->order_id";
		/*if($udata[trans_id])
			$summary[]="Transaciton ID#: ${udata[trans_id]}";*/
		if($udata[trans_code])
			$summary[]="Tracking Code#: ${udata[trans_code]}";
		$str.=implode("\n",$summary);
		return $str;
	}
	function validateOrder(){
		//return some status codes
		//use data from userdata/contents etc
		//do credit trans
		return;
	}

	function completeOrder($force=1){
		global $REMOTE_ADDR;
		$this->_data[ip]=$REMOTE_ADDR;
		$dat=$this->_data;
		$ins=$dat[userdata];
		$ins[shipping_charge]=$dat[shipping];
		$ins[date_created]=$ins[date_modified]=mysqlNow();
		$ins[tax_rate]=floatval($dat[tax]);
		$ins[tax_total]=$dat[taxAmount];
		$ins[subtotal]=$dat[total];
		$ins[total]=$dat[gTotal];
		$ins[order_code]=mserial($dat[contents]);
		$ins[order_summary]=addslashes(strip($this->createSummaryString()));
		$this->_data[didorder]=1;
		$this->save();
		$this->order_id=insert_query($this->oTable,$ins);
		return $this->order_id;
	}
	function sendConfirmationEmail(){
		debug("CartDisplay->sendConfirmationEmail()");
		$udata=$this->userdata;
		$to=$udata[b_fname]." ".$udata[b_lname]." <${udata[b_email]}>";
		$from=$this->confirmFrom;
		$subject=$this->confirmSubject;
		$summary=$this->createSummaryString();
		/*$message=include2str($this->confirmTemplate,
							array(summary=>$summary),1);*/
		mymail($to, $from,
			$subject,
			include2str($this->confirmTemplate,array(summary=>$summary),1)
		);
	}
	function sendVerboseConfirmationEmail(){
		debug("CartDisplay->sendVerboseConfirmationEmail()");
		if(!$this->fulfillParams[dosend])return;
		$udata=$this->userdata;
		$to=$this->fulfillParams[toemail];
		$from=$this->fulfillParams[fromemail];
		$subject=$this->fulfillParams[subject];
		$summary=$this->createSummaryString();
/*		$message=include2str($this->fulfillParams[tmpl],
							array(summary=>$summary,udata=>$udata),1);*/
		mymail($to, $from,
			  $subject,
			  include2str($this->fulfillParams[tmpl],
							array(summary=>$summary,udata=>$udata),1)
				);
	}
	function handleNotifications(){
		global $HTTP_HOST,$STORE_ADMIN_URL, $STORE_URL;
		debug("CARTDISPLAY:->handleNotifications()");
		$this->sendVerboseConfirmationEmail();
		$params=$this->notifyParams;
		if(!$params[dodecrement]){
			debug("CARTDISPLAY:->handleNotifications():decrementing turned off. quit");
			return;
		}
		$notifylist=array();
		//go through cart and find low stock items. save to list
		foreach($this->contents as $row){
			$q=intval($row[quantity]);
			if(!$q)continue;
			$low_stock=$this->setDecrementedStock($row[id], $row[quantity]);//has to hit db
			if($low_stock && $params[donotify] && !$row[notified_stock]) {
				if(!$this->getStockNotifyFlag($row[id]))//double check it wasn't already sent
					$notifylist[]=$row;
			}
		}
		if(count($notifylist)){
			debug("CartDisplay->handleNotifications():emailing notify list");
			mymail(
				$params[toemail], $params[fromemail],
				$params[subject],
				include2str($params[tmpl],array(data=>$notifylist),1)
			);
			//set notification flags on inv items
			foreach(extract_key($notifylist,"id") as $inv_id){
				$this->setStockNotifyFlag($inv_id,1);
			}
		}
	}
	function handleSuccessfulOrder(){
		/*============= gift subscritions =========*/
		$this->doGiftSubsTransaction();

	}
	function completeCheckout(){
		if(!$this->num_items){
			$this->sendCartDataRedirect($this->carturl."?error=your cart is empty");
			exit();
		}

		//should do switch on status $ret codes
		//process payment
		//do credit card pre-authorization
		if($method=$this->transMod[type]){
			$result=$this->$method("CapturePayment");
			if($result[error]==1){//if there's an error this leaves FUNCTION!!!!
				//just return an error and let script handle workflow
				$redir=$result[fatal]?$this->thanksurl:$this->checkouturl;
				$redir=urlqsa($redir,array(error=>$result[msg]),"*");
				if($result[fatal]){
					$this->kill();
				}
				$this->sendCartDataRedirect($redir);
				exit();
			}
		}

		//data insertion
		if(! ($order_id=$this->completeOrder()) ){
			//order didn't go into the database for some reason
			$this->kill();
			$redir=urlqsa($this->thanksurl,array(error=>"Your order couldn't be placed in our system"));
			$this->sendCartDataRedirect($redir);
			exit;
		}
		//emails
		$this->handleNotifications();
		$this->sendConfirmationEmail();
		//final cleanup
		$persist=array(	);
		//do any post-checkout actions before kill and redirection
		$this->handleSuccessfulOrder();

		$this->kill();
		$this->sendCartDataRedirect( urlqsa($this->thanksurl,$persist) );
		exit;
	}
	function hideCCNum($cc){
		if(strlen($cc)<4)return $cc;
		$lastpart=substr($cc, strlen($cc)-4);
		$cc=@str_repeat("*",strlen($cc)-4).$lastpart;
		return $cc;
	}
	function isFatalTransMsg($msg){
		global $STORE_FATAL_ERROR_MSGS;
		$msg=strip($msg);
		if(!$msg)return 0;

		foreach($STORE_FATAL_ERROR_MSGS as $emsg){
			if( stristr($msg,$emsg) ){
				return 1;
			}
		}
		return 0;
	}
	/*==========credit card modules =============*/
	function yourpay($mode="CapturePayment"){
		include_once(dirname(__FILE__)."/cart-yourpay/lpphp.php");
		//include_once(dirname(__FILE__)."/cart-yourpay/lphp.php");
		global $is_dev;
		$this->_setTax();
		$ret=array();
		$lphp=new lpphp;
		$udata=$this->_data[userdata];
		list($ccmo,$ccyear)=explode("/",$udata[cc_expire]);
		$order=array(
			/*====configuration=========*/
			host=>($is_dev?"secure.linkpt.net":"secure.linkpt.net"),
			/*$host=>($is_dev?"staging.linkpt.net":"secure.linkpt.net"),*/
			port=>($is_dev?"1129":"1129"),
			#storename=>($is_dev?"1909103950":"1001103337"),
			#configfile=>($is_dev?"1909103950":"1001103337"),
			configfile=>"1001103337",
			#keyfile=>dirname(__FILE__)."/certs/".($is_dev?"test":"minyanville").".pem",
			keyfile=>dirname(__FILE__)."/certs/minyanville.pem",
			result => ($is_dev?"GOOD":"LIVE"),
			/*mototransaction=>"ECI_TRANSACTION",*/
			/*====== credit card*/
			#cvmindicator=>true,
			cvmindicator=>"provided",
			cardnumber=>$udata[cc_num],
			#expmonth=>$ccmo,
			cardexpmonth=>$ccmo,
			#expyear=>$ccyear,
			#cardexpyear=>$ccyear,
			cardexpyear=>substr($ccyear,2,2),
			cvmvalue=>$udata[cc_cvv2],
			/*=== billing address=====*/
			email=>$udata[b_email],
			#bname=>"${udata[b_fname]} ${udata[b_lname]}",
			name=>"${udata[b_fname]} ${udata[b_lname]}",
			phone=>$udata[b_phone],
			#baddr1=>$udata[b_address],
			address1=>$udata[b_address],
			#baddr2=>$udata[b_address2],
			address2=>$udata[b_address2],
			#bcity=>$udata[b_city],
			city=>$udata[b_city],
			#bstate=>$udata[b_state],
			state=>$udata[b_state],
			zip=>$udata[b_zip],
			#bcountry=>$udata[b_country],
			country=>$udata[b_country],
			ip=>$udata[ip],
			/*======= shipping address=======*/
			sname=>"${udata[s_fname]} ${udata[s_lname]}",
			sphone=>$udata[s_phone],
			#saddr1=>$udata[s_address],
			saddress1=>$udata[s_address],
			#saddr2=>$udata[s_address2],
			saddress2=>$udata[s_address2],
			scity=>$udata[s_city],
			sstate=>$udata[s_state],
			szip=>$udata[s_zip],
			scountry=>$udata[s_country],
			/*==========tax========*/
			tax=>$this->taxAmount,
			taxstate=>"CA",
			taxzip=>90028,
			/*======== shipping==========*/
			#Shipping=>$this->shipping,
			shipping=>$this->shipping,
			shipstate=>$udata[s_state],
			shipzip=>$udata[s_zip],
			shipcarrier=>$udata[shipping_type],
			/*======== order total ==========*/
			subtotal=>$this->total,
			chargetotal=>$this->gTotal,
			#amount=>$this->gTotal,
		);

		if($mode=="CapturePayment" && !$this->_data[didCapture]){
			//generate our format of orderID
			$order[orderID]=$this->getOrderId();
			$res=$lphp->CapturePayment($order);//this may make a charge. don't want that.
		}
		unset($order);
		if($res){
			if(!$res[statusCode]){
				$ret=array(
					error=>1,
					msg=>$res[statusMessage],
					fatal=>$this->isFatalTransMsg($res[statusMessage])
				);
			}else{
				$this->_data[userdata][trans_id]  = $res[orderID];
				$this->_data[userdata][trans_msg] = $res[statusMessage];
				$this->_data[userdata][trans_code]= $res[trackingID];
				$ret=array(
					error=>0,
					trans_id=>$res[orderID],
					trans_msg=>$res[statusMessage],
					trans_code=>$res[trackingID]
				);
			}
			$this->save();
		}
		return $ret;
	}



}//end CartDisplay{}

/*==================================================================
================ order managment =====================================
====================================================================*/
class OrderManager extends Cart{
	function OrderManager(){
		$this->Cart();
		$this->dateFormatUnix="m/d/Y h:i:sa T";
		$this->dateFormatDB="%m/%d/%Y %r";
	}
	function getOrder($id){
		if(!$id)return array();
		$qry="SELECT *, date_format(date_created,'$this->dateFormatDB')date_created,
			  date_format(date_modified,'$this->dateFormatDB')date_modified
			  FROM $this->oTable WHERE id='$id'";
		return exec_query($qry,1);
	}
}

function yourpayApproveSales($order_ids){
	global $ORDER_TABLE, $is_dev;
	$qry="SELECT * FROM $ORDER_TABLE WHERE find_in_set(id,'".implode(",",$order_ids)."')";
	include_once(dirname(__FILE__)."/cart-yourpay/lpphp.php");
	$ret=array();
	$lphp=new lpphp;
	foreach(exec_query($qry) as $i=>$row){
		//order didn't originally use automated cc processing
		if(/*!$row[trans_id]*/0){//ignore pre-auth transactions for now.
			$ret[]=array(
				error=>1,
				msg=>"Order ${row[id]} wasn't made with automated billing",
				id=>$row[trans_id]
			);
			continue;
		}
		//this is to disregard the pre-auth transaction: remove order id
		$trans_id=$row[trans_id];unset($row[trans_id]);//remove ref to old order
		$trans_code=$row[trans_code];unset($row[trans_code]);//remove ref to old order
		$trans_msg=$row[trans_msg];unset($row[trans_msg]);//remove ref to old order

		if($row[was_shipped]){
			//order has already been charged/shipped
			$ret[]=array(
				error=>1,
				msg=>"Order ${row[id]} was already shipped/charged",
				id=>$trans_id
			);
			continue;
		}
		//$res=$lphp->BillOrder( yourpayOrder($row,1) );//2nd arg generates OUR order id
		//just go straight to approveSale as if the pre-auth transaction didn't exist. fuck it
		$res=$lphp->ApproveSale(yourpayOrder($row));

		if($res){
			if(!$res[statusCode]){
				$ret[]=array(
					error=>1,
					msg=>$res[statusMessage],
					id=>$trans_id
				);
			}else{
				//mark the order as shipped
				$upd=array(
					was_shipped	=> 1,
					status		=> "fulfilled",
					trans_id	=> $res[orderID],
					trans_code	=> $res[trackingID],
					trans_msg	=> $res[statusMessage]
				);
				update_query($ORDER_TABLE, $upd, array(id=>$row[id]) );
				$ret[]=array(
					error=>0,
					id=>$row[orderID],
				);
			}
		}
	}
	return $ret;
}
function yourpayOrder($nord,$prefix="MV_"){
//returns yourPayAPI hash table
	global $is_dev;
	list($ccmo,$ccyear)=explode("/",$nord[cc_expire]);
	if(strlen($ccyear)>2){
		$ccyear=substr($ccyear,2);
	}
	//should always
	//if(!$nord[trans_id]) always generate a new trans id
	$nord[trans_id]=Cart::getOrderId($prefix);
	$order=array(
		/*====configuration=========*/
		#host=>($is_dev?"staging.linkpt.net":"63.146.126.8"),
		host=>"secure.linkpt.net",
		#port=>($is_dev?"1139":"1139"),
		port=>"1129",
		#storename=>($is_dev?"1909103950":"1001103337"),
		#configfile=>($is_dev?"1909103950":"1001103337"),
		configfile=>"1001103337",
		#keyfile=>dirname(__FILE__)."/certs/".($is_dev?"test":"minyanville").".pem",
		keyfile=>dirname(__FILE__)."/certs/minyanville.pem",
		result => ($is_dev?"GOOD":"LIVE"),
		/*====== credit card*/
		#cvmindicator=>true,
		cvmindicator=>"provided",
		cardnumber=>$nord[cc_num],
		#expmonth=>$ccmo,
		cardexpmonth=>$ccmo,
		#expyear=>$ccyear,
		#cardexpyear=>substr($ccyear,2,2),
		cardexpyear=>$ccyear,
		cvmvalue=>$nord[cc_cvv2],
		/*=== billing address=====*/
		email=>$row[b_email],
		#bname=>"${nord[b_fname]} ${nord[b_lname]}",
		name=>"${nord[b_fname]} ${nord[b_lname]}",
		#bphone=>$udata[b_phone],
		phone=>$udata[b_phone],
		#baddr1=>$nord[b_address],
		address1=>$nord[b_address],
		#baddr2=>$nord[b_address2],
		address2=>$nord[b_address2],
		#bcity=>$nord[b_city],
		city=>$nord[b_city],
		#bstate=>$nord[b_state],
		state=>$nord[b_state],
		#bzip=>$nord[b_zip],
		zip=>$nord[b_zip],
		#bcountry=>$nord[b_country],
		country=>$nord[b_country],
		ip=>$nord[ip],
		/*======= shipping address=======*/
		sname=>"${nord[s_fname]} ${nord[s_lname]}",
		sphone=>$udata[s_phone],
		#saddr1=>$nord[s_address],
		saddress1=>$nord[s_address],
		#saddr2=>$nord[s_address2],
		saddress2=>$nord[s_address2],
		scity=>$nord[s_city],
		sstate=>$nord[s_state],
		szip=>$nord[s_zip],
		scountry=>$nord[s_country],
		/*==========tax========*/
		tax=>$nord[tax_total],
		taxstate=>"CA",
		taxzip=>"90028",
		/*======== shipping==========*/
		#Shipping=>$nord[shipping_charge],
		shipping=>$nord[shipping_charge],
		shipstate=>$nord[s_state],
		shipzip=>$nord[s_zip],
		shipcarrier=>$nord[shipping_type],
		/*======== order total ==========*/
		subtotal=>dollars($nord[subtotal]),
		chargetotal=>dollars($nord[total]),
		#amount=>dollars($nord[total]),
		orderID=>$nord[trans_id],
		oid=>$nord[trans_id]
	);
	return $order;
}

?>
