<?
$HEADER="/assets/thankyou.jpg";
include("$DOCUMENT_ROOT/lib/_includes.php")?>
<?readfile("$D_R/assets/data/order_confirmation.html")?>
<pre style="width:550;word-wrap:break-word">
<?echo $summary;unset($summary)?>
</pre>
