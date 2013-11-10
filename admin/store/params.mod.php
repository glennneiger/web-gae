<?
include_once("$DOCUMENT_ROOT/lib/_includes.php");
	$store=new Store();
	$store->admin=1;
	if(!is_array($del))$del=array();

	foreach($param as $id=>$row){
		$store->setParameter($row, $id);
	}

	if(trim(&$new[name])){
		$store->setParameter($new);
	}

	foreach($del as $id=>$on){
		$store->deleteParameter($id);
	}

	location($refer.qsa(array(error=>"Your data was saved.")));
?>