<?
	//i am called from lib/_cart.php::StoreDisplay::displaySubcategories()
	
function nestedsubcats($subcat,$selectedcats=array(),$addl=""){
	global $cat_id;
	if(!is_array($subcat))
		return;
	if(!count($subcat))
		return;
	foreach($subcat as $row){
		$row[on]=in_array($row[id],$selectedcats);
		echo href($PHP_SELF.qsa(array(cat_id=>$row[id]),"*"),
			  uc($row[name]),0,
			  "$addl class=altlink".($row[on]?"on":"off")
		);
		echo "<br>".spacer(1,5)."<br>";
		if(in_array($row[id],$selectedcats)  ){
			if(count($row[subcategories])){
				echo '<div style="padding-left:10px;">';
				nestedsubcats($row[subcategories], $selectedcats,"style=font-weight:normal;");
				echo '</div>';
			}
		}
	}
}

nestedsubcats($subcategories,&$selectedcats);

?>