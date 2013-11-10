<?
$hide_ui=1;
include("./_header.php");
$s=new Store();//just use for vars

$fulfillment="SELECT if(`status`='','Undefined Status',`status`)status, count(*) `c`,
			 sum(total)total
			  FROM $s->oTable 
			  GROUP BY `status`
			  ORDER BY `c` DESC";


pdebug("/DB/");
?>

<fieldset><legend>Order Database Summary</legend>
	<div style="padding:20px">
	<b>Delivery Status</b>
	<div style="padding-left:10px">
	<?foreach(exec_query($fulfillment) as $row){?>
		<a href="orders.php?bystatus=1&status=<?=$row[status]?>" target="mainwindow"><?=$row[status]?></a>:$<?=number_format($row[total],2)?> (<?=number_format($row[c])?>) <br>
	<?}?>
	</div>
	<br>
	<b>Top 5 Products:</b>
	<div style="padding-left:10px">
		<i>coming soon...</i>
	</div>
	</div>
</fieldset>


<?include("./_footer.php")?>