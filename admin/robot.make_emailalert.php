<?
include_once("$DOCUMENT_ROOT/lib/_db.php");
include_once("$D_R/lib/email_alert/_lib.php");
$qry="select id,email from subscription where is_exchange='1' and id not in (select subscriber_id from email_alert_authorsubscribe
union
select subscriber_id from email_alert_categorysubscribe)";

$reccount=count(exec_query($qry));
echo "<br>"."No. of records: ",$reccount;
if($reccount){
$i=0;
foreach(exec_query($qry) as $row){ 
    $emailalert[subscriber_id]=$row['id'];
	$emailalert[email_alert]=0;
	insert_query("email_alert_categorysubscribe",$emailalert);
	insert_query("email_alert_authorsubscribe",$emailalert);
	$prod=getproduct($row['email']);
	if($prod['buzz']!==1){
		$recv_digest[recv_daily_gazette]=1;
		$digestsubs['id']=$row['id'];
		update_query("subscription",$recv_digest,$digestsubs);

	}

?>
<table>
	<tr><td><?=$row['id'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row['email'];?> updated.</td></tr>
</table>

<?
	$i=$i+1;
}
	echo "<br>"."No. of records updated: ",$i;
} else {
?>	<table>
	<tr><td>No records updated.</td></tr>
	</table>
<? }

?>