<?
	//receives $data. a list of cart items/joined product+inventory rows
?>
Some items in the <?=$HTTP_HOST?> inventory have hit a critically low stock
<br><br>
=================================================<br>
<?foreach($data as $row){
	$adminlink="$STORE_ADMIN_URL/inventory.php?id=${row[id]}";
	$storelink="$STORE_URL#prod${row[product_id]}";
?>
	<?=$row[title]?> Stock:<b><?=$row[stock]?></b> <?=href($adminlink,"admin")?><br><br>
<?}?>

