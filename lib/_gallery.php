<?

function getGalleryName($id=0){
	debug("getGalleryName($id)");
	global $gallery_table;
	$qry="SELECT * FROM $gallery_table";
	if($id){
		$qry.=" WHERE id='$id'";
	}
	$qry.=" ORDER BY ordr";
	return exec_query($qry,($id?1:0));
}

function getGallery($gallery_id,$curpage=0,$limit=1){
	debug("getGallery($gallery_id,$curpage,$limit)");
	global $gallery_table, $images_table,$gallery_pagesize;
	$qry="SELECT im.*, g.name 
		FROM $images_table im, $gallery_table g
		WHERE g.id='$gallery_id' 
		AND g.id=im.gallery_id
		ORDER BY im.ordr ";
	if($limit)$qry.=paginate($curpage,$gallery_pagesize);
	return exec_query($qry);
}

function newGalleryImage($gallery_id,$raw_path,$img_id=0,$caption="",$description,$order=0){
	debug("getGallery($gallery_id,$raw_path,$img_id,$caption,$order)");
	//function will create requisite images for db record
	//will at least create a caption w/ no image
	global $gallery_table, $images_table,$gallery_path,$D_R;
	global $tsize,$bsize;
	$basepath="$D_R$gallery_path";
	if(!$img_id){
		$ins=array(gallery_id=>$gallery_id,ordr=>$order);
		if($caption)$ins[caption]=$caption;
		if($description)$ins[description]=$description;
		$img_id=insert_query($images_table,$ins);
	}
	if(!is_file($raw_path))exit("$raw_path isn't a file");
	if(!filesize($raw_path))exit("$raw_path is 0 length");
	$raw_img="$gallery_path/$img_id-raw.jpg";
	$big_img="$gallery_path/$img_id-big.jpg";
	$tn_img="$gallery_path/$img_id-tn.jpg";
	//raw
	copy($raw_path,"$D_R/$raw_img");
	//big
	resizeImg($bsize[0],$bsize[1],$raw_path,"$D_R/$big_img");
	//thumb
	resizeImg($tsize[0],$tsize[1],$raw_path,"$D_R/$tn_img");
	//update image record w/ new file info

	$upd=array(raw_path=>$raw_img,big_path=>$big_img,thumb_path=>$tn_img);
	update_query($images_table,$upd,array(id=>$img_id));
	return 1;
}

function removeGalleryImage($img_id){
	debug("removeGalleryImage($img_id)");
	global $images_table,$D_R;
	$res=exec_query("SELECT * FROM $images_table WHERE id='$img_id'",1);
	if(!count($res))return;
	munlink( "$D_R${res[raw_path]}" );
	munlink( "$D_R${res[big_path]}" );
	munlink( "$D_R${res[thumb_path]}" );
	del_query($images_table,"id", $img_id ,1);
}

function removeGallery($gallery_id){
	debug("removeGallery($gallery_id)");
	global $gallery_table,$images_table;
	$qry="SELECT id FROM $images_table WHERE gallery_id='$gallery_id'";
	foreach(exec_query($qry,0,"id") as $img_id){
		removeGalleryImage($img_id);
	}
	del_query($gallery_table,"id",$gallery_id);
}

function reOrderGalleryImage($img1, $img2){//swaps order of img1 with img2
	global $images_table;
}

function resetGalleryOrder($gallery_id){
//rearranges order to natural state
	global $images_table;
	$db=new dbObj("SELECT id FROM $images_table WHERE gallery_id='$gallery_id' ORDER BY ordr");
	$i=0;
	while($row=$db->getRow()){
		update_query($images_table, array(ordr=>$i),array(id=>$row[id]));
		$i++;
	}
}

class Gallery{
	function Gallery($nodefaults=0){
		global $PHP_SELF, $_GET, $id, $PHP_AUTH_USER, $PHP_AUTH_PW, $gallery_pagesize;
		global $gallery_table, $images_table, $gallery_path, $tsize, $bsize, $gallery_cols;
		global $gid, $imgid;
		$this->user=lc($PHP_AUTH_USER);
		$this->pass=lc($PHP_AUTH_PW);
		$this->pagesize=$gallery_pagesize;
		$this->cols=$gallery_cols;
		$this->gTable=$gallery_table;
		$this->iTable=$images_table;
		$this->path=$gallery_path;
		$this->tsize=$tsize;
		$this->bsize=$bsize;
		$this->img_id=$imgid;
		$this->gallery_id=$gid;
		$this->script=$PHP_SELF;//default same page object created on
		//defaults
		if(!$nodefaults){
			$this->gallery_id=$this->_getDefaultGalleryId();
			$this->img_id=$this->_getDefaultImgId();
			$this->numrows=$this->getNumRows();
			$this->numpages=$this->getNumPages();
			$this->authGallery();
		}
	}
	
	function _getDefaultGalleryId(){
		debug("Gallery->_getDefaultGalleryId()");
		if($this->gallery_id)
			return $this->gallery_id;
		if($this->img_id){
			$qry="SELECT i.gallery_id id,g.user,g.pass,g.authenticate a
				  FROM $this->iTable i, $this->gTable g
				  WHERE i.id='$this->img_id' AND i.gallery_id=g.id
				  ORDER by g.ordr
				  ";
		}else{
			$qry="SELECT id, user, pass, authenticate FROM $this->gTable ORDER BY ordr";
		}
		$res=exec_query($qry,1);
		$this->authUser=$res[user];
		$this->authPass=$res[authPass];
		$this->auth=$res[authenticate];
		return $res[id];
	}

	function _getDefaultImgId(){
		debug("Gallery->_getDefaultImgId()");
		if($this->img_id)
			return $this->img_id;
		if($this->gallery_id){
			$qry="SELECT i.id,g.user, g.pass,g.authenticate 
				  FROM $this->iTable i, $this->gTable g
				  WHERE i.gallery_id='$this->gallery_id'
				  AND i.gallery_id=g.id ORDER BY g.ordr,i.ordr";
		}else{
			$qry="SELECT im.id, g.user, g.pass,g.authenticate
				 FROM $this->iTable im, $this->gTable g 
				 WHERE im.gallery_id=g.id 
				 ORDER BY g.ordr,im.ordr";
		}
		$res=exec_query($qry,1);
		$this->authUser=$res[user];
		$this->authPass=$res[pass];
		$this->auth=$res[authenticate];
		return $res[id];
	}

	function _getOffsetFromId($id){
		debug("Gallery->_getOffsetFromId($id)");
		if($this->offset)return $this->offset;
		$imids="SELECT id FROM $this->iTable WHERE gallery_id='$this->gallery_id' ORDER BY ordr";
		$db=new dbObj($imids);
		while($row=$db->getRow()){
			if($id==$row[id])break;
		}
		$this->offset=intval($db->i);
		return $this->offset;
	}
	function _getPageOffset(){
		debug("Gallery->_getPageOffset()");
		if($this->pageoffset)return $this->pageoffset;
		$offset=$this->_getOffsetFromId($this->img_id);
		$this->pageoffset=floor($offset/$this->pagesize)*$this->pagesize;
		return $this->pageoffset;
	}
	function getNumRows(){
		if($this->numrows)return $this->numrows;
		$this->numrows=count_rows($this->iTable, array(gallery_id=>$this->gallery_id));
		return $this->numrows;
	}

	function getNumPages(){
		debug("Gallery->getNumPages()");
		if($this->numpages)return $this->numpages;
		$numrows=$this->getNumRows();
		$this->numpages=ceil($this->numrows/$this->pagesize);
		return $this->numpages;
	}
	
	function _getCurrentPage(){
		debug("Gallery->_getCurrentPage()");	
		if($this->currentpage)return $this->currentpage;
		$off=$this->_getOffsetFromId($this->img_id);
		$psize=$this->pagesize;
		$this->currentpage=floor($off/$psize);
		return $this->currentpage;
	}
	
	function _getDefaultImgIdsPerPage(){
		debug("Gallery->_getDefaultImgIdsPerPage()");	
		if($this->default_img_ids){
			return $this->default_img_ids;
		}
		$qry="SELECT id FROM $this->iTable WHERE gallery_id='$this->gallery_id' ORDER BY ordr";
		$db=new dbObj($qry);
		$ret=array();
		$this->pageimages=array();
		while($id=$db->getRow("id")){
			$page=floor($db->i/$this->pagesize);
			if(!$ret[$page])
				$ret[$page]=$id;
			if($this->img_id==$id)
				$this->offset=$db->i;
			$this->pageimages[$page][]=$id;
		}
		$this->numpages=ceil($db->i/$this->pagesize);
		$this->default_img_ids=$ret;
		return $ret;
	}
	function authGallery(){
		debug("Gallery->authGallery()");
		global $gallery_use_auth;
		if(!$gallery_use_auth)
			return;
		//this method should be called late in the object instantiation as other
		//default methods set up authentication (case-insensitive)
		if(!$this->auth && isset($this->auth))return;//doesn't require authentication
		if(!isset($this->auth)){
			$qry="SELECT user,pass,authenticate a FROM $this->gTable WHERE id='$this->gallery_id'";
			$res=exec_query($qry,1);
			$this->auth=$res[a];
			$this->authUser=$res[user];
			$this->authPass=$res[pass];
		}
		if(!$this->auth)return;
		auth(lc($this->authUser), lc($this->authPass));
	}

	function imgIsInGallery($id){
		debug("Gallery->imgIsInGallery($id)");
		return count_rows($this->iTable, array(id=>$id, gallery_id=>$this->gallery_id));
	}
	
	function getGalleryNames($id=0){
		debug("Gallery->getGalleryNames($id)");	
		$qry="SELECT * FROM $this->gTable ";
		if($id)
			$qry.="WHERE id='$id'";
		$qry.=" ORDER BY ordr";
		return exec_query($qry,($id?1:0));
	}
	
	function displayNav($tmplpath=0){
		global $gallery_use_auth;
		debug("Gallery->displayNav()");
		$data=array();
		foreach($this->getGalleryNames() as $row){
			if($row[authenticate] && $gallery_use_auth)continue;
			$sel=($this->gallery_id==$row[id]?" class=linkon":" class=linkoff");
			$row["on"]=($this->gallery_id==$row[id]?1:0);
			$row["link"]="$this->script?gid=${row[id]}";
			$data[]=$row;
		}
		if($tmplpath){
			include($tmplpath);
			return;
		}
		return $data;
	}
	function displayPages($tmplpath=0){
		global $p;
		debug("Gallery->displayPages($tmplpath)");
		$pages=$this->_getDefaultImgIdsPerPage();
		if($this->numpages<2)return;
		$last=count($this->pageimages)-1;
		$data=array();
		$curpage=$this->_getCurrentPage();
		if(!in_array($this->img_id,$this->pageimages[0])){//previous
			$previd=$this->pageimages[$curpage-1][0];
			$data["prev"]=$this->script.qsa(array(imgid=>$previd,gid=>$this->gallery_id));
		}
		if(!in_array($this->img_id,$this->pageimages[$last])){//next
			$nextid=$this->pageimages[$curpage+1][0];
			$data["next"]=$this->script.qsa(array(imgid=>$nextid,gid=>$this->gallery_id));
		}
		unset($previd);unset($last);unset($nextid);unset($previd);
		$data["links"]=array();
		foreach($pages as $i=>$id){
			$data["links"][$i]=array(
				"link"=>$this->script.qsa(array(imgid=>$id,gid=>$this->gallery_id),"*"),
				"on"=>(in_array($this->img_id,$this->pageimages[$i])?1:0)
			);
		}
		if($tmplpath){
			include($tmplpath);
			return;
		}
		return $data;
	}

	function displayThumbs($tmplpath=0){
		debug("Gallery->displayThumbs()");	
		global $D_R;
		$start=$this->_getPageOffset();
		$len=$this->pagesize;
		$qry="SELECT id, gallery_id, caption, thumb_path, ordr 
				FROM $this->iTable 
				WHERE gallery_id = '$this->gallery_id'
				ORDER BY ordr LIMIT $start , $len";
		$db=new dbObj($qry);
		$data=array();
		$numrows=$db->numRows();
		$data["numrows"]=$numrows;
		$data["cols"]=$this->cols;
		while($row=$db->getRow()){
			list($w,$h)=getimagesize($D_R.$row[thumb_path]);
			$row["on"]=($this->img_id==$row[id]?1:0);
			$row["link"]=$this->script.qsa(array(imgid=>$row[id],gid=>$this->gallery_id));
			$row["w"]=$w; $row["h"]=$h;
			$data["images"][]=$row;
		}
		if($tmplpath){
			include($tmplpath);
			return;
		}
		return;
	}
	function getImage($id=0){
		debug("Gallery->getImage($id)");
		if(!$id)$id=$this->img_id;
		if(!$id)return array();
		$qry="SELECT * FROM $this->iTable WHERE id='$id'";
		return exec_query($qry,1);
	}
	function displayImage($tmplpath=0,$id=0){
		global $D_R;		
		if(!$id)$id=$this->img_id;
		$data=$this->getImage($id);
		if(!count($data))return;
		list($data[w], $data[h])=getimagesize($D_R.$data[big_path]);
		$data[caption]=strip($data[caption]);
		if($tmplpath){
			include($tmplpath);
			return;
		}
		return $data;
	}
}





function thumbSize($w,$h,$src_img){ 
	list($sX,$sY)=getimagesize($src_img);
	if ($sX > $sY){
    	return array($w , intval(($w * $sY)/$sX));
	}else{
    	return array(intval(($h * $sX)/$sY) , $h);
	}
}
function resizeImg($width,$height,$src_file, $dest_file){
	global $USE_MAGICK;
	if($USE_MAGICK){
		return resizeImgMagick($width,$height,$src_file, $dest_file);
	}
	list($src_width,$src_height)  =getimagesize($src_file);
	list($dest_width,$dest_height)=thumbSize($width,$height,$src_file);
	$dest_img = imagecreatetruecolor($dest_width,$dest_height);
	$src_img  = imagecreatefromjpeg($src_file);
	imagecopyresampled($dest_img,$src_img,0,0,0,0,$dest_width,$dest_height,$src_width,$src_height);
	return imagejpeg($dest_img,$dest_file,75);
}

function resizeImgMagick($w,$h,$src,$dest){
	$ret = "";
	if(!is_file($src)){
		debug("resizeImgMagick()$src doesn't exist");
		return;
	}
	
	$conv="/usr/bin/convert";
	if(!is_file($conv)){
		debug("resizeImgMagick():couldn't find convert");
		return;
	}
	system("$conv +profile '*' -geometry ${w}x${h} \"$src\" \"$dest\"  ",$ret);
	debug("resizeImgMagick()system() returned $ret");
}


function createThumb($src,$dest){
	global $THUMB_SIZE;
	list($w,$h)=$THUMB_SIZE;
	resizeImg($w,$h,$src,$dest);
}

function createBig($src,$dest){
	global $BIG_SIZE;
	list($w,$h)=$BIG_SIZE;
	resizeImg($w,$h,$src,$dest);
}

?>
