<?php
//$truncateQuery="TRUNCATE buzzbanter_today";
$truncateQuery="DELETE FROM buzzbanter_today WHERE date_format(buzzbanter_today.date,'%m/%d/%y') = date_format(DATE_SUB('".mysqlNow()."',INTERVAL 1 DAY),'%m/%d/%y')";
$del=exec_query($truncateQuery);

echo "Record deleted successfully.";
?>